<?php

namespace lib\Routing;

/**
 * Router points URIs to controllers and can generate URIs
 */
class Router {

	/**
	 * @var array<Route> application Routes
	 */
	private $routes = array();

	/**
	 * @var string default controller method
	 */
	private $default_action;

	/**
	 * @var array<string> default conditions
	 */
	private $default_conditions;
	
	/**
	 * Router constructor
	 * 
	 * @param string $default_action default controller method
	 */
	public function __construct( $default_action, $default_conditions )
	{
		$this->default_action = $default_action;
		
		$this->default_conditions = array_merge(
			array(
				'id' => '\d+', 'id2' => '\d+', 'id3' => '\d+',
			),
			$default_conditions
		);
	}
	
	/**
	 * Add an URI route
	 * 
	 * if the pattern does not contain :controller placeholder or if it is
	 * optional, it must be defined in defaults
	 * 
	 * if the pattern does not contain :action placeholder or if it is optional,
	 * it is used the one defined in route defaults or in the application config
	 *
	 * by default placeholder :id has condition \d+
	 * 
	 * @param string $name route name
	 * @param string $uri_pattern pattern to match an URI, can contains
	 * 		placeholder like :placeholder or :placeholder? if optional
	 * @param array $defaults associative array of default params
	 * @param array $conditions associative array of pattern placeholder names to regex conditions
	 */
	public function map($name, $uri_pattern, $defaults = array() , $conditions = array())
	{
		if (empty($defaults['action'])) {
			$defaults['action'] = $this->default_action;
		}
		$this->routes[$name] = new Route($uri_pattern, $defaults, array_merge($this->default_conditions, $conditions));
	}
	
	/**
	 * Search a route that match the uri and extract parameters
	 * 
	 * @param string $uri already decoded uri
	 * 
	 * @return array|false controller, action and other params
	 */
	public function match( $uri )
	{
		$uri = explode('?', $uri);
		$uri = trim($uri[0],'/ ');
		
		foreach( $this->routes as $name => $route )
			if( $result = $route->match($uri) ){
				$result['route'] = $name;
				return $result;
			}
		
		return false;
	}
	
	/**
	 * generate an URI for a given target with given parameters
	 * 
	 * @param string $target
	 * @param array $params
	 * @see Router::url_for()
	 * 
	 * @return string generated URI
	 */
	public function uri_for ( $target, $params = array() ){
		
		//merge params passed as target query string
		if( false !== strpos($target, '?') ){
			$target_parts = explode('?',$target);
			parse_str($target_parts[1],$target_params);
			$params = array_merge($target_params,$params);
			$target = $target_parts[0];
		}
		
		//find route pointed by route name
		if ( isset($this->routes[$target]) && $this->routes[$target]->can_point_to( $params ) ) {
			$route = $this->routes[$target];
		}
		// find route pointed by "controller/action"
		elseif( preg_match('@(\w+)/(\w+)@', $target, $matches) ) {
			$params['controller'] = $matches[1];
			$params['action'] = $matches[2];
			
			foreach( $this->routes as $r ) {
				if( $r->can_point_to( $params ) ) {
					$route = $r;
					break;
				}
			}
		}
		
		//check if a route has been found
		if(!isset($route)){
			trigger_error("could not find any route for target $target with params ".print_r($params, true));
			return '';
		}
		
		return $route->fill($params);
	}
	
}