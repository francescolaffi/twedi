<?php

namespace lib\Exceptions;

class HttpException extends \Exception
{
	private $debug_msg;
	
	public function __construct($message = '', $code = 0, $debug_msg = '', \Exception $prev = null)
	{
		parent::__construct($message, $code, $prev);
		$this->debug_msg = $debug_msg;
	}
	
	public function getDebugMessage()
	{
		return $this->debug_msg;
	}
}