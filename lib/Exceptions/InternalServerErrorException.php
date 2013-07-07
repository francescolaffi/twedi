<?php

namespace lib\Exceptions;

class InternalServerErrorException extends HttpException
{
	public function __construct($message = '', $debug = '', \Exception $prev = null)
	{
		parent::__construct($message, 500, $debug, $prev);
	}
}