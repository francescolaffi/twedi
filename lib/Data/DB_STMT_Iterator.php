<?php

namespace lib\Data;
use \lib\Data\DB_STMT;

class DB_STMT_Iterator implements \Iterator {
	
	protected $stmt;
	
	protected $buffered = false;
	
	protected $item = null;
	
	protected $key = -1;
	
	protected $valid = false;
	
	public function __construct(DB_STMT $stmt, $buffered = true, $item_type = '\stdClass', $item_args = null ) {
		$this->stmt = $stmt;
		
		if ($buffered) {
			$this->stmt->store_result();
			$this->buffered = true;
		}
		
		switch ($item_type) {
			case 'array':
				$stmt->bind_array($this->item);
				break;
			case 'assoc':
				$stmt->bind_assoc($this->item);
				break;
			case 'object' === $item_type && is_object($item_args):
				$this->item = $item_args;
				$stmt->bind_obj($this->item);
				break;
			case class_exists( $item_type ):
				$stmt->bind_new_obj( $this->item, $item_type, $item_args );
				break;
			default:
				throw new \InvalidArgumentException(); //TODO: explain
		}
	}
	
	public function current(){
		return $this->item;
	}
	
	public function key(){
		return $this->key;
	}
	
	public function next(){
		if( $this->valid = (!is_null($this->item) && $this->stmt->fetch()) )
			$this->key++;
	}
	
	public function rewind(){
		if( -1 !== $this->key ){
			if( $this->buffered )
				$this->stmt->data_seek(0);
			else
				$this->stmt->execute();
			$this->key = -1;
		}
		$this->next();
	}
	
	public function valid(){
		return $this->valid && !is_null($this->item);
	}
}