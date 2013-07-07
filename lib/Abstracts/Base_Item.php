<?php

namespace lib\Abstracts;

abstract class Base_Item{
	
	protected $item_type;
	
	protected $values = array();
	
	public function set_values( $values ){
		$this->values = $values;
	}
	
	public function add_values( $values ){
		$this->values = array_merge( $this->values, $values );
	}
	
	public function set_values_from( Base_Item $item ){
		$this->set_values( $this->item_values($item) );
	}
	
	public function add_values_from( Base_Item $item ){
		$this->add_values( $this->item_values($item) );
	}
	
	private function item_values( Base_Item $item ){
		if( $this->is_a($item) )
			return $item->values;
		else
			throw new \LogicException("cannot load values from an item of differnt type (this item type: {$this->type}, other item type: {$item->type})");
	}
	
	public function is_a(Base_Item $item){
		return $this->item_type === $item->item_type;
	}
	
	public function __get($name){
		return @$this->values[$name];
	}
	
	public function __isset($name){
		return isset( $this->values );
	}
	
	public function __unset($name){
		unset($this->values[$name]);
	}
}