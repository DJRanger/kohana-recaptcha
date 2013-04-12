<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Simple wrapper for Googles reCAPTCHA library
 *
 * @author     Alex Cartwright <alexc223@gmail.com>
 * @copyright  Copyright (c) 2012 Alex Cartwright
 * @license    BSD 3-Clause License, see LICENSE file
 */
class Kohana_Recaptcha {

	/**
	 * Public key
	 * @var string
	 */
	protected $_public_key;

	/**
	 * Private key
	 * @var string
	 */
	protected $_private_key;

	/**
	 * Error code returned when checking the answer
	 * @var string
	 */
	protected $_error;

	/**
	 * Load the reCAPTCHA PHP library and configure the keys from the config
	 * file or the provided array argument.
	 *
	 * @param   array  $config
	 * @return  object
	 */
	public function __construct(array $config = NULL)
	{
		require_once Kohana::find_file('vendor', 'recaptcha/recaptchalib');

		if (empty($config))
		{
			$config = Kohana::$config->load('recaptcha');
		}
		$this->_public_key = $config['public_key'];
		$this->_private_key = $config['private_key'];
	}

	/**
	 * Generate the HTML to display to the client
	 *
	 * @return  string
	 */
	public function get_html()
	{
		return recaptcha_get_html(
			$this->_public_key,
			$this->_error,
			Request::$initial->secure()
		);
	}

	/**
	 * Returns bool true if successful, bool false if not.
	 *
	 * @param   string  $challenge
	 * @param   string  $response
	 * @return  bool
	 */
	public function check($challenge = NULL, $response = NULL)
	{
		if (NULL === $challenge)
		{
			$challenge = Request::$current->post('recaptcha_challenge_field') OR FALSE;
		}
		if (NULL === $response)
		{
			$response = Request::$current->post('recaptcha_response_field') OR FALSE;
		}
		$result = recaptcha_check_answer($this->_private_key, Request::$client_ip, $challenge, $response);
		$this->_error = $result->error;
		return (bool) $result->is_valid;
	}

}
