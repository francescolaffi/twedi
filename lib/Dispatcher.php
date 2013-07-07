<?php

namespace lib;

use lib\Dependency_Injection\Injector;

use lib\Exceptions\NotFoundHttpException;
use lib\Exceptions\InternalServerErrorException;

class Dispatcher {
	
	private $injector;
	
	private $current_route;
	private $current_controller_cb;
	
	public function __construct(Injector $injector) {
		$this->injector = $injector;
	}
	
	public function dispatch( $uri ){
		$result = $this->injector->get_service('router')->match($uri);
		
		if (!$result) {
			throw new NotFoundHttpException('', 'uri not matched');
		}
		
		if (@$result['controller']) {
			$cur_controller = $result['controller'];
			$controller_class = "app\Controllers\\$cur_controller";
			if (!class_exists($controller_class)) {
				throw new NotFoundHttpException('', "404 matched controller $controller_class not found, uri: $uri");
			}
		} else {
			$cur_controller = @$result['def_controller'];
			$controller_class = "app\Controllers\\$cur_controller";
			if (!class_exists($controller_class)) {
				throw new InternalServerErrorException('', "500 invalid default controller $controller_class, uri: $uri");
			}
		}
		
		$controller = new $controller_class($this->injector);
		
		if (@$result['action']) {
			$cur_action = $result['action'];
			$callback = array($controller, $cur_action);
			if (!is_callable($callback)) {
				throw new NotFoundHttpException('', "404 controller $controller_class can't perform matched action $cur_action, uri: $uri");
			}
		} else {
			$cur_action = @$result['def_action'];
			$callback = array($controller, $cur_action);
			if (!is_callable($callback)) {
			throw new InternalServerErrorException('', "500 no action matched for controller $controller_class and invalid default $cur_action, uri: $uri");
			}
		}
		
		$this->injector->add_parameters(array(
			'runtime.route' => $result['route'],
			'runtime.controller' => $cur_controller,
			'runtime.action' => $cur_action,
			'runtime.route_params' => $result['params'],
		));
		
		call_user_func_array( $callback, $result['params'] );
	}
	
	public function forward($controller, $action, $args = array())
	{
		if (!$controller || !$action) {
			throw new \InvalidArgumentException('Either controller or action are empty');
		}
		
		if (!class_exists($controller_class = "app\Controllers\\$controller")) {
			throw new InternalServerErrorException('', "500 invalid controller $controller_class");
		}
		
		$controller = new $controller_class($this->injector);
		
		if (!is_callable($callback = array($controller,$action))) {
			throw new InternalServerErrorException('', "500 invalid action action $action for controller $controller_class");
		}
		
		call_user_func_array($callback, $args);
	}
	
	public function build_model( $model_name ){
		if ( !class_exists($model_class = "app\Models\\$model_name") )
			throw new InternalServerErrorException('', "500 model $model_class not found");
		
		return $this->injector->build('model', array(), $model_class);
	}
	
	public function build_template_item($tpl_item_name)
	{
		if ( !class_exists($tpl_item_class = "app\Template_Items\\$tpl_item_name") )
			throw new InternalServerErrorException('', "500 template item $tpl_item_class not found");
		
		$tpl_item = $this->injector->build('template_item', array(), $tpl_item_class);
		
		return $tpl_item;
	}
}