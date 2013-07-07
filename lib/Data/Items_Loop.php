<?php

namespace lib\Data;

use \lib\Data\DB_STMT_Iterator;
use \lib\Data\DB_STMT;
use \lib\Abstracts\Base_Item;

class Items_Loop extends DB_STMT_Iterator {
	
	private $item_obj = null;
	
	public function __construct(DB_STMT $stmt, Base_Item $item_obj){
		$this->item_obj = $item_obj;
		parent::__construct($stmt, true, 'assoc');
	}
	
	public function next(){
		if( $this->valid = (!is_null($this->item) && $this->stmt->fetch()) ) {
			$this->key++;
			$this->item_obj->set_values( $this->item );
		}
	}
	
	public function current(){
		return $this->item_obj;
	}
}