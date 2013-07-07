<?php

namespace lib\Business_Logic;

use lib\Abstracts\Base_Helper;
use lib\Abstracts\Base_Item;
use lib\Data\DB_STMT;
use lib\Data\Items_Loop;


abstract class Controller extends Base_Helper {
	
	protected function build_model($model_name)
	{
		return $this->injector->get_service('dispatcher')->build_model($model_name);
	}
	
	/**
	 * Build and populate a model
	 *
	 * if id not found optionally throw not found exception
	 * 
	 * @param string $model_name Model class name
	 * @param mixed $id id of the db row to load
	 * @param string|false $not_found_msg Message for the not found exception,
	 * if false no exception will be thrown
	 *
	 * @throws \lib\Exceptions\NotFoundHttpException if id not found
	 * @return \lib\Data\Model populate model
	 */
	protected function populate_model($model_name, $id, $not_found_msg = '')
	{
		$model = $this->build_model($model_name);
		if (!$model->populate($id) && false !== $not_found_msg) {
			throw new \lib\Exceptions\NotFoundHttpException($not_found_msg);
		}
		return $model;
	}
	
	protected function build_template_item($item_name)
	{
		return $this->injector->get_service('dispatcher')->build_template_item($item_name);
	}
	
	protected function template_item_from(Base_Item $item)
	{
		$reflector = new \ReflectionClass($item);
		$tpl_item = $this->build_template_item($reflector->getShortName());
		$tpl_item->set_values_from($item);
		return $tpl_item;
	}
	
	protected function build_stmt()
	{
		return $this->injector->build('DB_STMT');
	}
	
	protected function build_loop(DB_STMT $stmt, $tpl_item_name)
	{
		$tpl_item = $this->build_template_item($tpl_item_name);
		$loop = new Items_Loop($stmt, $tpl_item);
		//$loop->buffer();
		return $loop;
	}
	
	protected function render($view, $slots = array())
	{
		//convert models to template items
		foreach ($slots as &$slot) {
			if (is_object($slot) && ($slot instanceof \lib\Data\Model)) {
				$slot = $this->template_item_from($slot);
			}
		}
		
		$this->injector->get_service('view')->render($view, $slots);
	}
	
	protected function redirect($location, $code = 302)
	{
		header("Location: $location", true, $code);
		die();
	}
	
	/**
	 * check that user is logged in or throw the auth required exception
	 *
	 * by default user will be redirected to route named 'login'
	 * 
	 * @param string $auth_msg message displayed on login page or a default msg will be used
	 * @param string $return_to url to redirect after succesful login (default is current url)
	 */
	protected function check_logged_in($auth_msg = '', $return_to = null)
	{
		if (!$this->session()->is_logged_in()) {
			throw new \lib\Exceptions\AuthRequiredException($auth_msg, $return_to);
		}
	}
	
	/**
	 * check that logged in user is admin or throw the insufficient permissions exception
	 *
	 * by default an error message will be displayed in same page using 'base_layout' view
	 * 
	 * @param string $error_msg message displayed or a default one will be used
	 */
	protected function check_is_admin($error_msg = '')
	{
		if (!$this->session()->is_logged_in()) {
			throw new \lib\Exceptions\InsufficientPermissionsException($error_msg);
		}
	}
	
	/**
	 * check that logged nonce in request is correct
	 *
	 * by default an error message will be displayed in same page using 'base_layout' view
	 * 
	 * @param string $nonce_action      nonce action used to generate nonce
	 * @param string $nonce_token       nonce token, if empty nonce will be read from $_REQUEST
	 * @param string $nonce_request_key nonce key in $_REQUEST array, used only if $nonce_token is empty, default is 'nonce'
	 */
	protected function check_nonce($nonce_action, $nonce_token = '', $nonce_request_key = 'nonce')
	{
		if (!$nonce_token) {
			$nonce_token = filter_input(INPUT_POST, $nonce_request_key) ?: filter_input(INPUT_GET, $nonce_request_key) ;
		}
		if(!$this->session()->check_nonce($nonce_action, $nonce_token)){
			throw new \lib\Exceptions\InvalidNonceException();
		}
	}
}