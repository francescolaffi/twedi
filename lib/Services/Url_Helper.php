<?php

namespace lib\Services;

use lib\Routing\Router;
use lib\Services\Session;

/*
 * class Url_Helper
 */

class Url_Helper {
	private $router;
	private $session;
	private $home_url;
	private $public_folder;
	
	public function __construct(Router $router, Session $session, $home_url, $public_folder)
	{
		$this->router = $router;
		$this->session = $session;
		$this->home_url = $home_url;
		$this->public_folder = $public_folder;
	}
	
	public function url_for($target, array $params = array(), array $query = array())
	{
		$uri = $this->router->uri_for($target, $params);
		return $this->home_url.'/'.($uri ? $uri.'/' : '').(empty($query) ? '' : '?'.http_build_query($query));
	}
	
	public function nonced_url($target, array $params = array(), $nonce_action, $nonce_key = 'nonce', array $query = array())
	{
		$query[$nonce_key] = $this->session->generate_nonce($nonce_action);
		return $this->url_for($target, $params, $query);
	}
	
	public function static_url($asset)
	{
		return $this->home_url.$this->public_folder.$asset;
	}
}