<?php

namespace lib\Routing;

/**
 * URI Route
 */
class Route {
	
	/**
	 * @var string pattern to match URIs
	 */
	private $uri_pattern;
	
	/**
	 * @var array params defaults
	 */
	private $defaults;
	
	/**
	 * @var string regex to match URIs
	 */
	private $uri_regex;
	
	/**
	 * @var array<string,bool> map pattern placeholders to their mandatoriness
	 */
	private $params_mandatoriness;
	
	/**
	 * Route constructor
	 * 
	 * @param string $uri_pattern
	 * @param array $defaults
	 * @param array $conditions
	 * @see Router::map()
	 */
	public function __construct($uri_pattern, $defaults = array() , $conditions = array()) {
    	
		$this->uri_pattern = trim($uri_pattern,'/ ');
		$this->defaults = $defaults;
		
		$params_mandatoriness = array();
		
		//generate a regex pattern to match URIs
		$uri_regex = preg_replace_callback(
			'@([/-_]?):(\w+)(\??)@',
			function ($matches) use ($conditions, &$params_mandatoriness)
			{
				if (isset($conditions[$matches[2]])) {
					preg_match( '@^(?:([^()]*)\()?(.*?)(?:\)([^()]*))?$@', $conditions[$matches[2]], $parts);
					$param_regex = "{$matches[1]}{$parts[1]}(?P<{$matches[2]}>{$parts[2]})".@$parts[3];
				} else {
					$param_regex = "{$matches[1]}(?P<{$matches[2]}>[\w- ]+)";
				}
				
				if ( '?' === $matches[3] ){
					$params_mandatoriness[$matches[2]] = false;
					$param_regex = "(?:$param_regex)?";
				} else {
					$params_mandatoriness[$matches[2]] = true;
				}
				return $param_regex;
			},
			$this->uri_pattern
		);
		$this->params_mandatoriness = $params_mandatoriness;

		$this->uri_regex = "@^$uri_regex$@";
		
		//check if controller is defined, otherwise route is invalid
		if ( empty($defaults['controller']) && empty($this->params_mandatoriness['controller']) ){
			return false; //TODO: throw exception
		}
	}
	
	/**
	 * Match the URI and extract parameters
	 * 
	 * @param string $uri URI to match
	 * 
	 * @return array|false extracted params or false if no match 
	 */
	public function match($uri){
		if( preg_match($this->uri_regex, $uri, $params) ){
			// remove numerical keys
			$params = array_intersect_key($params, $this->params_mandatoriness);
			
			$controller = empty($params['controller']) ? '' : join('_', array_map('ucfirst', explode('-', $params['controller'])));
			unset($params['controller']);
			
			$action = empty($params['action']) ? '' : str_replace('-', '_', $params['action']);
			unset($params['action']);
			
			$defaults = $this->defaults;
			
			$def_controller = (string) @$defaults['controller'];
			unset($defaults['controller']);
			
			$def_action = (string) @$defaults['action'];
			unset($defaults['action']);
			
			$params = array_merge($defaults, $params);
			
			return compact('controller','action','def_controller','def_action','params');
		} else {
			return false;
		}
	}
	
	/**
	 * check if this route can point to the target specified by params
	 * 
	 * @param array $params
	 * 
	 * @return bool
	 *
	 * TODO: check logic efficency 
	 */
	public function can_point_to( $params ){
		$params = array_filter($params);
		
		//infos needed are the mandatory placeholders, controller and action
		$needed_infos = array_filter( array_merge( $this->params_mandatoriness, array('controller'=>1,'action'=>1) ));
		
		//and they have to be in params or in defaults
		$needed_infos_ok = !count( array_diff_key( $needed_infos, $params, $this->defaults ) );
		
		//all params have a placeholder in the pattern or are equal to defaults
		$represent_all_params = !count( array_diff_assoc( array_diff_key( $params, $this->params_mandatoriness ), $this->defaults ) );
		
		return $needed_infos_ok && $represent_all_params;
	}
	
	/**
	 * fill the URI pattern replacing placeholder with corresponding params
	 * 
	 * @param array $params
	 * 
	 * @return string filled URI
	 */
	public function fill($params) {
		$params = array_diff_assoc(array_filter($params), $this->defaults);
		$mandatory_params = array_filter($this->params_mandatoriness);
		$defaults = $this->defaults;
		
		return preg_replace_callback(
			'@([/-_]?):(\w+)(\??)@',
			function ($m) use (&$params, &$mandatory_params, $defaults)
			{
				$par = $m[2];
				
				if( isset($params[$par]) ){
					$r = $m[1].$params[$par];
					unset($params[$par]);
					unset($mandatory_params[$par]);
				} elseif ( count($params) || count($mandatory_params) ) {
					$r = $m[1].$defaults[$par];
					unset($mandatory_params[$par]);
				} else {
					$r = '';
				}
				
				switch($par) {
					case 'controller':
						$r = strtolower($r);
					case 'action':
						$r = str_replace('_', '-', $r);
				}
				
				return $r;
			},
			$this->uri_pattern
		);
	}
	
}