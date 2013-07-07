<?php

namespace lib\Services;

use lib\Data\DB_STMT;
/*
 * class Session
 */

class Session {
	private $user = null;
	
	private $auth_stmt;
	
	/**
	 * @var string hash salt for auth cookie token
	 */
	private $auth_salt;
	
	/**
	 * @var int auth cookie duration
	 */
	private $auth_duration;
	
	/**
	 * @var string auth cookie name
	 */
	private $auth_cookie;
	
	private $flash_cookie;
	
	private $new_flashes = array();
	private $old_flashes = array();
	
	/**
	 * Load user session from cookie
	 */
	public function __construct(DB_STMT $auth_stmt, $auth_table, $auth_salt, $auth_cookie, $auth_duration, $flash_cookie, $cookies_base_url)
	{
		$this->auth_stmt = $auth_stmt->table($auth_table)->limit(0,1);
		$this->auth_salt = $auth_salt;
		$this->auth_cookie = $auth_cookie;
		$this->auth_duration = $auth_duration;
		
		$this->flash_cookie = $flash_cookie;
		
		$url_parts = parse_url($cookies_base_url);
		$this->cookies_domain = ('localhost' == $url_parts['host'] || '127.0.0.1' == $url_parts['host']) ? null : $url_parts['host'];
		$this->cookies_path = $url_parts['path'].'/';
		
		if (!empty($_COOKIE[$this->auth_cookie])) {
			$id = intval( strstr( $_COOKIE[$this->auth_cookie], ':', true) );
			
			if ($id && ($user = $this->fetch_by_id($id)) && $user->is_valid && $this->check_auth_cookie($user)) {
				$this->user = $user;
				#$this->set_auth_cookie(); TODO:renew auth cookie
			} else {
				$this->delete_cookie($this->auth_cookie);
			}
		}
		
		$this->load_flashes();
	}
	
	/**
	 * check if user is logged in
	 * 
	 * @return bool if logged in or not
	 */
	public function is_logged_in() {
		return !is_null($this->user);
	}
	
	public function is_admin() {
		return (bool)@$this->user->is_admin;
	}
	
	public function user_id()
	{
		return is_null($this->user) ? false : $this->user->id;
	}
	
	/**
	 * Authorize an user
	 * 
	 * @param string $name user name
	 * @param string $password user password
	 * 
	 * @return bool if logged int
	 */
	public function login($name, $password, $keep_logged){
		if (!($user = $this->fetch_by_name($name)) || !$user->is_valid || md5($password.$user->salt) !== $user->pass) {
			return false;
		}
		
		$this->user = $user;
		$this->set_auth_cookie($keep_logged);
		return true;
	}
	
	/**
	 * Logout an user
	 */
	public function logout(){
		$this->delete_cookie($this->auth_cookie);
		$this->user = null;
	}
	
	/**
	 * set auth cookie
	 * 
	 * @return bool result
	 */
	private function set_auth_cookie($keep_logged)
	{
		return $this->set_cookie (
			$this->auth_cookie,
			$this->generate_auth_cookie($this->user),
			$keep_logged ? time()+$this->auth_duration : 0,
			/*ssl only*/ false,
			/*http only*/ true
		);
	}
	
	/**
	 * generate auth cookie
	 * 
	 * @return string auth cookie value
	 */
	private function generate_auth_cookie( $user ){
		return $user->id .':'. md5( $user->id . $user->name . $user->pass . $this->auth_salt );
	}
	
	private function check_auth_cookie($user)
	{
		return $this->generate_auth_cookie($user) == $_COOKIE[$this->auth_cookie];
	}
	
	/** begin COOKIES **/
	
	public function set_cookie($name, $value, $expire = 0, $ssl = false, $httponly = false) {
		return setcookie($name, $value, $expire, $this->cookies_path, $this->cookies_domain, $ssl, $httponly);
	}
	
	public function delete_cookie($name, $ssl = false, $httponly = false) {
		return setcookie($name, false, 1, $this->cookies_path, $this->cookies_domain, $ssl, $httponly);
	}
	
	/** end COOKIES **/
	
	/** begin FLASHES **/
	
	/**
	 * Add a notice message that can be fetched with get notices and displayed in views
	 * 
	 * @param string $type info|success|warning|error
	 * @param string $message   the notice message
	 * @param bool $next_load display in current page load or in next one
	 */
	public function add_notice($type, $message, $next_load)
	{
		if ($next_load) {
			$notices = @is_array($this->new_flashes['notices']) ? $this->new_flashes['notices'] : array();
			$notices[] = compact('type','message');
			$this->set_flash_cookie('notices', $notices);	
		} else {
			$this->old_flashes['notices'][] = compact('type','message');
		}
	}
	
	public function get_notices()
	{
		$notice_priority = array(
			'success' => 0,
			'info'    => 1,
			'warning' => 2,
			'error'   => 3,
		);
		
		usort($this->old_flashes['notices'],
			function ($a, $b) use ($notice_priority)
			{
				$a = $notice_priority[$a['type']];
				$b = $notice_priority[$b['type']];

				return ($a == $b) ? 0 : (($a > $b) ? -1 : 1);
			});
		
		return $this->old_flashes['notices'];
	}
	
	public function set_flash_cookie($name, $value)
	{
		$this->new_flashes[$name] = $value;
		return $this->set_cookie("{$this->flash_cookie}[$name]", json_encode($value));
	}
	
	public function get_flash_cookie($name)
	{
		return @$this->old_flashes[$name];
	}
	
	private function load_flashes(){
		if (@is_array($_COOKIE[$this->flash_cookie])) {
			foreach ($_COOKIE[$this->flash_cookie] as $name => $json) {
				$this->old_flashes[$name] = json_decode(stripslashes($json), true);
				$this->delete_cookie("{$this->flash_cookie}[$name]");
			}
		}
		#error_log(print_r($_COOKIE,true));
		if (@is_array($this->old_flashes['notices'])) {
			foreach ($this->old_flashes['notices'] as &$n) {
				$n['message'] = htmlspecialchars($n['message']);
			}
		} else {
			$this->old_flashes['notices'] = array();
		}
	}
	
	/** end FLASHES **/
	
	/** begin NONCES **/
	
	public function generate_nonce($action)
	{
		return $this->shifted_nonce($action);
	}
	
	public function check_nonce($action, $nonce)
	{
		if (!$action || !$nonce) {
			return false;
		}
		
		if ($this->shifted_nonce($action, 0) == $nonce) {
			return 1; // Nonce generated 0-12 hours ago
		}
		if ($this->shifted_nonce($action, 1) == $nonce) {
			return 2; // Nonce generated 12-24 hours ago
		}
		return false; // Invalid nonce
	}
	
	private function shifted_nonce($action, $shift = 0)
	{
		$tick = ceil(time() / (86400 / 2)); //TODO: setting for nonce life
		$uid = (int) $this->user_id();
		
		return substr(md5(($tick - $shift) . $action . $uid), 12, 10);
	}
	
	private function nonce_tick()
	{
		return ceil(time() / (86400 / 2)); //TODO: setting for nonce life
	}
	
	/** end NONCES **/
	
	private function fetch_by_id($id)
	{
		$this->auth_stmt->where('id', '=', $id, 'i');
		return $this->fetch_user();
	}
	
	private function fetch_by_name($name)
	{
		$this->auth_stmt->where('name', '=', $name, 's');
		return $this->fetch_user();
	}
	
	private function fetch_user()
	{
		$this->auth_stmt->select(array('id','name','pass','salt','is_admin','is_valid'));
		$this->auth_stmt->bind_new_obj($user);
		if (!$this->auth_stmt->fetch()) $user = null;
		else $this->auth_stmt->fetch();
		$this->auth_stmt->clean_where();
		return $user;
	}
}