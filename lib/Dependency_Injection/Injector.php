<?php

namespace lib\Dependency_Injection;

use lib\Data\DB_STMT_Iterator;

/**
 * Injector is a dependecy injection container 
 */
class Injector implements \ArrayAccess {
	
	private $services = array();
	
	private $parameters = array();
	
	private $definitions = array();
	
	public function __construct($parameters, $definitions)
	{
		$this->parameters = $parameters;
		
		foreach ($definitions as $n => &$d) {
			if (empty($d['class']) || !isset($d['is_service'])) {
				die("error in injection definition $n"); #TODO: appropriate exception
			}
			if (!isset($d['param_names'])) {
				$d['param_names'] = array();
			}
			if (!isset($d['calls_list'])) {
				$d['calls_list'] = array();
			}
		}
		
		$this->definitions = $definitions;
		
		$this->services['injector'] = $this;
	}
	
	/*----- Definitions Handling -----*/
	
	public function add_definition($name, $class, $is_service, array $param_names = array(), array $calls_list = array())
	{
		$this->definitions[$name] = compact('class', 'is_service', 'param_names', 'calls_list');
	}
	
	/*----- Parameters Handling -----*/
	
	public function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	public function offsetGet($name)
	{
		return $this->get_parameter($name);
	}
	
	public function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	public function offsetSet($name, $value)
	{
		if( is_null($name) ) throw new \InvalidArgumentException(); //TODO: find appropriate exc and describe
		$this->set_parameter($name, $value);
	}
	
	public function add_parameters($params)
	{
		$this->parameters = array_merge($this->parameters, $params);
	}
	
	public function has_parameter($name)
	{
		return isset($this->parameters[$name]);
	}
	public function offsetExists($name)
	{
		$this->has_parameter($name);
	}
	
	public function unset_parameter($name)
	{
		unset($this->parameters[$name]);
	}
	public function offsetUnset($name)
	{
		$this->unset_parameter($name);
	}
	
	/*----- Services Handling -----*/
	
	public function get_service($name)
	{
		if( isset($this->services[$name]) ){
			return $this->services[$name];
		}
		
		if (method_exists(__CLASS__, $method = "load_$name")) {
			return $this->services[$name] = $this->$method();
		}
		
		if (!isset($this->definitions[$name]) || true != $this->definitions[$name]['is_service']) {
			throw new \InvalidArgumentException(sprintf('Service %s does not exist',$name));
		}
		$definition = $this->definitions[$name];
		$class = @$this->parameters[$definition['class']] ?: $definition['class'];
		$refl = new \ReflectionClass($class);
		$service = $refl->newInstanceArgs($this->resolve_params($definition['param_names']));
		foreach ($definition['calls_list'] as $method => $params_names) {
			call_user_func_array(array($service, $method), $this->resolve_params($params_names));
		}
		return $this->services[$name] = $service;
	}
	public function __get($name)
	{
		return $this->get_service($name);
	}
	
	public function set_service($name, $value) {
		$this->services[$name] = $value;
	}
	public function __set($name, $value) {
		$this->set_service($name, $value);
	}
	
	public function has_service($name)
	{
		return isset($this->services[$name]) || (isset($this->definitions[$name]) && true == $this->definitions[$name]['is_service']) || method_exists(__CLASS__, "load_$name");
	}
	public function __isset($name)
	{
		return $this->has_service($name);
	}
	
	public function drop_service($name)
	{
		unset($this->services[$name]);
	}
	public function __unset($name)
	{
		$this->drop_service($name);
	}
	
	/*----- Factory Handling -----*/
	
	public function build($name, array $extra_params = array(), $class_override = null)
	{
		if( $class_override && !class_exists($class_override) )
			throw new \InvalidArgumentException("class not found $class_override");
			
		$refl = new \ReflectionClass($this);
		if ($refl->hasMethod("new_$name")) {
			return call_user_func(array($this,"new_$name"),$extra_params,$class_override);
		}
		
		if (!isset($this->definitions[$name]) || false != $this->definitions[$name]['is_service']) {
			throw new \InvalidArgumentException(sprintf('Dont know how to build %s',$name));
		}
		$definition = $this->definitions[$name];
		$class = $class_override ?: (@$this->parameters[$definition['class']] ?: $definition['class']);
		$params = array_merge($this->resolve_params($definition['param_names']), $extra_params);
		$refl = new \ReflectionClass($class);
		$obj = $refl->newInstanceArgs($params);
		foreach ($definition['calls_list'] as $method => $params_names) {
			call_user_func_array(array($service, $method), $this->resolve_params($params_names));
		}
		return $obj;
	}
	
	public function __call($name, $args)
	{
		array_unshift($args, $name);
		return call_user_func_array(array($this,'build'), $args);
	}
	
	public function can_build($name)
	{
		$refl = new \ReflectionClass($this);
		return (isset($this->definitions[$name]) && false == $this->definitions[$name]['is_service']) || $refl->hasMethod("new_$name");
	}
	
	/* commons */
	
	private function resolve_params($params)
	{
		if (empty($params)) {
			return array();
		}
		foreach($params as &$p){
			if ($this->has_service($p)) {
				$p = $this->get_service($p);
			} elseif ($this->can_build($p)) {
				$p = $this->build($p);
			} else {
				$p = @$this->parameters[$p];
			}
		}
		return $params;
	}
	
	/*-----*/
	
	private function load_router(){
		$routes_ini = parse_ini_file(BASEPATH.'/app/routes.ini.php', true);
		
		$default_action = $this->get_parameter('routing.default_action');
		$default_conditions = @$routes_ini['default_conditions'] ?: array();
		unset($routes_ini['default_conditions']);
		
		$router = new \lib\Routing\Router($default_action, $default_conditions);
		
		foreach($routes_ini as $name => $args )
			$router->map($name, $args['uri'], @$args['defaults']?:array(), @$args['conditions']?:array() );
			
		return $router;
	}
	
	private function load_mailer(){
		return new Mail( MAIL_USER, MAIL_PASS );
	}
	
	/*---------*/
	
	public function new_db_stmt_iterator(){
		return new DB_STMT_Iterator();
	}
}