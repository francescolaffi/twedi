<?php

namespace lib\Data;

use lib\Abstracts\Base_Item;
use lib\Data\DB_STMT;

abstract class Model extends Base_Item{
	
	protected $table;
	
	protected $pkey;
	
	protected $pkey_type = 'i';
	
	// no pkey unless not autoincrement
	protected $cols_and_types = array();
	
	private $injector;
	
	protected $stmt;
	
	public function __construct(DB_STMT $stmt) {
		$this->stmt = $stmt->table($this->table)->cols_assoc($this->cols_and_types);
	}
	
	public function __set($name,$value){
		$this->values[$name] = $value;
	}

	public function populate($id)
	{
		if(empty($id)) return false;

		$stmt = $this->stmt->where($this->pkey,'=',$id,$this->pkey_type);
		$stmt->select();
		$this->stmt->clean_where();

		$stmt->bind_assoc($this->values);
		$stmt->store_result();
		return $stmt->fetch();
	}
	
	public function save(){
		if (empty($this->values[$this->pkey])) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}
	
	public function insert()
	{
		if ($r = $this->stmt->insert($this->values)) {
			if ($this->stmt->insert_id) {
				$this->values[$this->pkey] = $this->stmt->insert_id;
			}
		}
		return $r;
	}
	
	public function update()
	{
		$r = $this->stmt->where($this->pkey,'=',$this->values[$this->pkey],$this->pkey_type)->update($this->values);
		$this->stmt->clean_where();
		return $r;
	}
	
	public function delete(){
		$r = false;
		if (!empty($this->values[$this->pkey])) {
			$r = $this->stmt->where($this->pkey, '=', $this->values[$this->pkey], $this->pkey_type)->delete();
			$this->stmt->clean_where();
		}
		return $r;
	}
	
	public function __destruct()
	{
		#echo '<pre>';
		#debug_print_backtrace();
		#TODO: questo dava dei problemi, controllare perchè (es: conferma email)
		#$this->stmt->close();
	}
}