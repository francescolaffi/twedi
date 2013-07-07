<?php

namespace lib\Abstracts;

use lib\Dependency_Injection\Injector;

abstract class Base_Helper {
	
	protected $injector;
	
	public function __construct(Injector $injector) {
		$this->injector = $injector;
	}
	
	public function get_service($name){
		return $this->injector->get_service($name);
	}
	
	public function factory_build($name, array $extra_params = array(), $class_override = null){
		return $this->injector->build($name, $extra_params, $class_override);
	}
	
	public function url_for($target, array $params = array(), array $query = array())
	{
		return $this->injector->get_service('url_helper')->url_for($target, $params, $query);
	}
	
	public function nonced_url($target, array $params = array(), $nonce_action, $nonce_key = 'nonce', array $query = array())
	{
		return $this->injector->get_service('url_helper')->nonced_url($target, $params, $nonce_action, $nonce_key, $query);
	}
	
	public function static_url($asset)
	{
		return $this->injector->get_service('url_helper')->static_url($asset);
	}
	
	public function session() {
		return $this->injector->get_service('session');
	}
}