<?php

namespace lib\Exceptions;

class NotFoundHttpException extends HttpException
{
	public function __construct($message = '', $debug = '', \Exception $prev = null)
	{
		parent::__construct($message, 404, $debug, $prev);
	}
}