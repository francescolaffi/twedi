<?php

namespace lib\Data;

abstract class Model_Hash extends Model{
	
	protected $pkey_type = 's';
	
	public function save(){
		if (empty($this->values[$this->pkey])) {
			$this->generate_unique_hash();
			return $this->insert();
		} else {
			return $this->save();
		}
	}
	
	protected function generate_unique_hash() {
		$values = $this->values;
		
		do {
			$token = substr(md5(mt_rand()), 10, 10);
		} while ($this->populate($token));
		
		$values[$this->pkey] = $token;
		$this->values = $values;
	}
}