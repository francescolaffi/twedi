<?php

namespace lib\Exceptions;

class AuthRequiredException extends \Exception {
	
	private $return_to_url;
	
	public function __construct($msg = '', $return_to_url = null, \Exception $prev = null)
	{
		$this->return_to_url = filter_var($return_to_url, FILTER_VALIDATE_URL) ?: 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		parent::__construct($msg, 0, $prev);
	}
	
	public function getReturnToUrl()
	{
		return $this->return_to_url;
	}
}