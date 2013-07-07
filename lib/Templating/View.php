<?php

namespace lib\Templating;

use \lib\Abstracts\Base_helper;

class View extends Base_Helper {
	
	private $slots = array();
	
	private $content = '';
	
	private $views_stack = array();
	
	private $rendering = false;
	
	private $called_view = '';
	
	public function render($view_name, $slots = array(), $content = '')
	{
		if (!$this->called_view) {
			$this->called_view = $view_name;
		}
		
		if (!empty($slots)) {
			$this->slots = array_merge($this->slots, $slots);
		}
		
		if ($content) {
			$this->content = $content;
		}
		
		extract ($this->slots);
		$view = $this;
		
		if ($this->rendering) {
			include( $this->check_file( $view_name ) );
			return;
		} 
		
		$this->rendering = true;
		
		while ($view_name) {
			ob_start();
			
			include($this->check_file($view_name));
			
			$this->content = ob_get_clean();
			
			$view_name = array_pop($this->views_stack) ;
		}
		
		echo $this->content;
	}
	
	public function extend($view)
	{
		$this->views_stack[] = $view;
	}
	
	public function content()
	{
		echo $this->content;
	}
	
	public function called_view()
	{
		return $this->content;
	}
	
	public function current_controller()
	{
		return $this->injector->get_parameter('runtime.controller');
	}
	
	public function current_action()
	{
		return $this->injector->get_parameter('runtime.action');
	}
	
	public function current_route()
	{
		return $this->injector->get_parameter('runtime.route');
	}

	public function current_route_params()
	{
		return $this->injector->get_parameter('runtime.route_params');
	}

	public function current_route_param($key)
	{
		$route_params = $this->injector->get_parameter('runtime.route_params');
		return @$route_params[$key];
	}
	
	private function check_file($view_name)
	{
		if (! file_exists( $file = BASEPATH."/app/views/$view_name.php")) {
			$this->rendering = false;
			throw new \lib\Exceptions\InternalServerErrorException('', "500 view $file not fount");
		}
		
		return $file;
	}
}