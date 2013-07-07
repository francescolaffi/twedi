<?php

namespace lib\Data;

class DB extends \MySQLi {

	public function __construct($host, $user, $pass, $db = '') {
		parent::__construct($host, $user, $pass, $db);
		
		if ($this->connect_errno)
			throw new \lib\Exceptions\DbConnectionException( "DB connection error ({$this->connect_errno}): {$this->connect_error}", $this->connect_errno );
	}
	
	public function __destruct(){
		@$this->close();
	}
}