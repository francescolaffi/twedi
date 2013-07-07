<?php

namespace lib;

use lib\Dependency_Injection\Injector;

class Main {
	
	private $injector;
	
	public function __construct($config)
	{	
		try {
			$injection_definitions = parse_ini_file(BASEPATH.'/lib/Dependency_Injection/default_definitions.ini', true);
			if (is_file($definitions_file = BASEPATH.'/app/injection_definitions.ini.php')) {
				$injection_definitions = array_merge($injection_definitions, parse_ini_file($definitions_file, true));
			}
			
			$this->injector = new Injector($config, $injection_definitions);
			
			$this->injector->get_service('session');
			
			$this->init();
		} catch (\Exception $e) {
			die('Internal server error');
		}
	}
	
	private function init()
	{	
		$uri = @$_SERVER['PATH_INFO'] ?: '/';
		
		try {
			$this->injector->get_service('dispatcher')->dispatch($uri);
		}
		//handle AuthRequired, redirecting to login page
		catch (\lib\Exceptions\AuthRequiredException $e) {
			if ($msg = $e->getMessage()) {
				$this->injector->get_service('session')->set_flash_cookie('auth_required_msg', $msg);
			}
			$redirect = $this->injector->get_service('url_helper')->url_for('login').'?return_to='.urlencode($e->getReturnToUrl());
			header("Location: $redirect", true, 302);
		}
		
		catch (\lib\Exceptions\HttpException $e) {
			$def_msgs = array(
				404 => 'Page Not Found',
				500 => 'Internal Server Error',
			);
			
			if (!$err_code = $e->getCode()) {
				$err_code = 500;
				$message = '';
			} else {
				$message = $e->getMessage();
			}
			if (!$message && @$this->injector->has_parameter("strings.{$err_code}msg")) {
				$message = @$this->injector->get_parameter("strings.{$err_code}msg");
			}
			if (!$message && isset($def_msgs[$err_code])) {
				$message = $def_msgs[$err_code];
			}
			if (!$message){
				$err_code = 500;
				$message = $def_msgs[500];
			}
			
			if (404 != $err_code) {
				$refl = new \ReflectionClass($e);
				error_log($refl->getShortName().' at '.$e->getFile().':'.$e->getLine().' : '.$e->getDebugMessage());
			}
			
			header('-', true, $err_code);
			
			$this->handle_error($message, $err_code);
		}
		
		catch (\lib\Exceptions\InsufficientPermissionsException $e) {
			$def_msgs = 'Insufficient Permissions. You need admin permissions to perform this action.';
			$message = $e->getMessage() ?: @$this->injector->get_parameter("strings.permissions_err");
			$this->handle_error($message ?: $def_msgs, 'permissions');
		}
		
		catch (\lib\Exceptions\InvalidNonceException $e) {
			$def_msgs = 'Action Verification Token Error. Please go back, refresh and retry.';
			$message = @$this->injector->get_parameter("strings.nonce_err");
			$this->handle_error($message ?: $def_msgs, 'nonce');
		}
		
		catch (\Exception $e) {
			$refl = new \ReflectionClass($e);
			error_log($refl->getShortName().' at '.$e->getFile().':'.$e->getLine().' : '.$e->getMessage());
			$this->handle_error();
		}
	}

	private function handle_error($error_msg = 'An error occurred', $error_type = null)
	{
		try {
			$this->injector->get_service('dispatcher')->forward('Errors', 'display_error', compact('error_msg','error_type'));
		} catch (\Exception $e) {
			error_log(get_class($e).' at '.$e->getFile().':'.$e->getLine().' : '.($e->getDebugMessage() ?: $e->getMessage()));
			die ($error_msg);
		}
	}
}