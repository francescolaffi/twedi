<?php

namespace lib\Data;

/**
 * TODO: structure as a decorator checking error and throwing exc for each overridden call
 */
class DB_STMT extends \MySQLi_STMT{
	
	private $table;
	
	private $cols_names = array();
	
	private $cols_binds = array();
	
	private $cols_types = '';
	
	private $where_exprs = array();
	
	private $where_values = array();
	
	private $where_types = '';
	
	private $group_by = '';
	
	private $order_by = '';
	
	private $limit = '';
	
	private $prepared = null;
	
	/*** CHAINABLE QUERY METHODS ***/
	
	public function table( $table ){
		$this->prepared and $this->prepared = null;
		
		$this->table = $table;
		return $this;
	}
	
	public function cols( $names, $types = '' ){
		$this->prepared and $this->prepared = null;
		
		$this->cols_names = $names;
		$this->cols_types = $types;
		
		return $this;
	}
	
	public function cols_assoc( $names_and_types ){
		$this->prepared and $this->prepared = null;
		
		$this->cols_names = array_keys($names_and_types);
		$this->cols_types = join($names_and_types);
		
		return $this;
	}
	
	public function where( $field, $operator, $values, $type ){
		$this->prepared and $this->prepared = null;
		
		$values = (array) $values;
		
		switch( $operator ){
			case '=': case '!=':
			case '<': case '<=':
			case '>': case '>=':
			case 'LIKE': case 'NOT LIKE':
				$n_values = 1;
				$expr = "$field $operator ?";
				break;
			case 'IN': case 'NOT IN':
				$n_values = count($values);
				$expr = "$field $operator (?" . str_repeat( ',?', $n_values-1 ) . ')' ;
				break;
			case 'BETWEEN': case 'NOT BETWEEN':
				$n_values = 2;
				$expr = "$field $operator ? AND ?" ;
				break;
			default:
				return $this; //TODO: exception
		}
		
		$this->where_exprs  []= $expr;
		
		for( $i=0; $i<$n_values; $i++ ){
			$this->where_values []= $values[$i];
			$this->where_types .= $type;
		}
		return $this;
	}
	
	public function where_expr( $expr, $values, $types ){
		$this->prepared and $this->prepared = null;
		
		//TODO: check that placeholder in expr, values and types are the same quantity or throw
		
		$this->where_exprs  []= $expr;
		
		foreach((array)$values as $v)
			$this->where_values []= $v;
		
		$this->where_types .= $types;
		
		return $this;
	}
	
	public function group_by( $group_by ){
		$this->prepared and $this->prepared = null;
		$this->group_by = "GROUP BY $group_by";
		return $this;
	}
	
	public function order_by( $order_by ){
		$this->prepared and $this->prepared = null;
		$this->order_by = "ORDER BY $order_by";
		return $this;
	}
	
	public function limit( $offset, $length = null ){
		$this->prepared and $this->prepared = null;
		$this->limit = "LIMIT $offset" . (null === $length ? '' : ", $length");
		return $this;
	}
	
	/*** EXECUTE QUERY METHOD ***/
	
	public function __call( $method, $args ){
		if( ! is_callable($sql_cb = array(__CLASS__,"{$method}_sql") ) )
		   throw new \BadMethodCallException( 'Bad Method Call: class '.__CLASS__.' don\'t have method '.$method );
		
		if ($method !== $this->prepared ) {
			$sql = call_user_func_array($sql_cb,$args);
			$this->prepared = $method;
			$this->prepare($sql);
			$this->bind_params();
		}
		
		if( 'insert' === $method || 'update' === $method )
			$this->bind_values( $args[0] );
		
		#var_dump('<pre>',$sql,$this->where_values,$this->binded_args,'</pre>');
		$result = $this->execute();
		if('count' === $method && $result ){
			$this->bind_array($a);
			$this->store_result();
			$this->fetch();
			return $a[0];
		} else {
			return $result;
		}
	}
	
	private function insert_sql( ){
		$cols = join( ',', $this->cols_names );
		$marks = '?' . str_repeat( ',?', count($this->cols_names)-1 );
		return "INSERT INTO {$this->table} ($cols) VALUES ($marks)";
	}
	
	private function update_sql( ){
		$cols = join( ' = ?, ', $this->cols_names ) . ' = ?';
		$where = empty($this->where_exprs) ? '' : 'WHERE '.join( ' AND ', $this->where_exprs );
		return "UPDATE {$this->table} SET $cols $where {$this->order_by} {$this->limit}";
	}
	
	private function select_sql( $cols_names = null ){
		$cols = empty($cols_names) ? '*' : join( ', ', (array) $cols_names );
		$where = empty($this->where_exprs) ? '' : 'WHERE '.join( ' AND ', $this->where_exprs );
		return "SELECT $cols FROM {$this->table} $where {$this->group_by} {$this->order_by} {$this->limit}";
	}
	
	private function count_sql( $col_name = null ){
		$col = $col_name ?: '*';
		$where = empty($this->where_exprs) ? '' : 'WHERE '.join( ' AND ', $this->where_exprs );
		return "SELECT COUNT($col) as count FROM {$this->table} $where {$this->group_by} {$this->order_by} {$this->limit}";
	}
	
	private function delete_sql( ){
		$where = empty($this->where_exprs) ? '' : 'WHERE '.join( ' AND ', $this->where_exprs );
		return "DELETE FROM {$this->table} $where {$this->order_by} {$this->limit}";
	}
	
	/*** BINDING RESULTS METHODS ***/
	
	public function bind_array( &$array = null ){
		$meta = $this->result_metadata();
		$args = array();
		$array = array();
		
		for ( $i=0; $i<$meta->field_count; $i++ )
			$args[] = &$array[$i];
		
		call_user_func_array(array($this, 'bind_result'), $args);
	}
	
	public function bind_assoc( &$assoc = null ){
		$meta = $this->result_metadata();
		$args = array();
		if (!is_array($assoc)) {
			$assoc = array();
		}
		
		while ($field = $meta->fetch_field())
			$args[] = &$assoc[$field->name];
		
		call_user_func_array(array($this, 'bind_result'), $args);
	}
	
	public function bind_obj(&$obj){
		if (!is_object($obj)) {
			throw new \InvalidArgumentException('first parameter is expected to be an object');
		}
		$meta = $this->result_metadata();
		$args = array();
		
		while ($field = $meta->fetch_field())
			$args[] = &$obj->{$field->name};
		
		call_user_func_array(array($this, 'bind_result'), $args);
	}
	
	public function bind_new_obj( &$obj = null, $class = '\stdClass', $args = array() ){
		if (empty($args)) {
			$obj = new $class();
		} else {
			$refl = new \ReflectionClass($class);
			$obj = $refl->newInstanceArgs($args);
		}
		$this->bind_obj($obj);
	}
	
	/*** EXCEPTIONS ON ERROR ***/
	
	public function prepare($query){
		if( !$this->errno ) {
			$r = parent::prepare($query);
		}
		$this->throw_on_error();
		return @$r;
	}
	
	public function execute(){
		if( !$this->errno ) {
			$r = parent::execute();
		}
		$this->throw_on_error();
		return @$r;
	}
	
	private function throw_on_error(){
		if( $this->errno ) 
			throw new \lib\Exceptions\DbStmtException( "DB statement error ({$this->errno}): {$this->error}", $this->errno );
	}
	
	/*** MISC METHODS ***/
	
	public function clean(){
		$this->table       = null;
		$this->cols_names  = array();
		$this->cols_binds  = array();
		$this->cols_types  = '';
		$this->where_exprs = array();
		$this->where_values = array();
		$this->where_types = '';
		$this->prepared    = null;
	}
	
	public function clean_where(){
		$this->where_exprs = array();
		$this->where_values = array();
		$this->where_types = '';
		$this->prepared    = null;
	}
	
	/*** PRIVATE METHODS ***/
	
	private function bind_params( ){
		
		$this->cols_binds = array();
		$bind_args = array( 0=>'' );
		
		if( ( 'insert' === $this->prepared || 'update' === $this->prepared )
			&& $this->cols_types )
		{
			$bind_args[0] .= $this->cols_types;
			
			foreach( $this->cols_names as $name ){
				$this->cols_binds[$name] = null;
				$bind_args []=& $this->cols_binds[$name];
			}
		}
		
		if( ( 'update' === $this->prepared || 'select' === $this->prepared  || 'count' === $this->prepared || 'delete' === $this->prepared )
			&& $this->where_types )
		{
			$bind_args[0] .= $this->where_types;
			for ($i = 0; $i < count($this->where_values); $i++) {
				$bind_args []=& $this->where_values[$i];
			}
		}
		
		if( !empty($bind_args[0]) )
			call_user_func_array( array($this,'bind_param'), $bind_args );
			
		$this->binded_args = $bind_args;
	}
	
	private function bind_values( $values ){
		foreach( $this->cols_binds as $name => &$bind ){
			$bind = isset($values[$name]) ? $values[$name] : '';
		}
	}
}