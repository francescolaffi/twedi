<?php

namespace app\Controllers;

class Auth extends Generic {
	
	public function login()
	{
		$return_to_url = filter_input(INPUT_GET, 'return_to', FILTER_VALIDATE_URL) ?: @$_SERVER['HTTP_REFERER'];
		$return_to_msg = '';
		
		if ($this->session()->is_logged_in()) {
			$this->redirect($return_to_url ?: $this->url_for('home'));
		}
		
		if (filter_input(INPUT_POST, 'submit')) {
			$return_to_url = filter_input(INPUT_POST, 'return_to_url', FILTER_VALIDATE_URL) ?: @$_SERVER['HTTP_REFERER'];
			
			
			if ((!$email = filter_input(INPUT_POST, 'email')) || (!$pwd = filter_input(INPUT_POST, 'password'))) {
				$this->session()->add_notice('warning', 'Inserisci la tua email e la tua password per loggarti.', false);
			} elseif ($this->session()->login($email, $pwd, filter_input(INPUT_POST, 'resta_loggato'))) {
				$this->redirect($return_to_url ?: $this->url_for('home'));
			} else {
				$this->session()->add_notice('error', 'Email o password non corretti.', false);
			}
			
			$return_to_msg = filter_input(INPUT_POST, 'return_to_msg');	
		}
		
		if ($return_to_url) {
			if (!$return_to_msg) {
				$return_to_msg = $this->session()->get_flash_cookie('auth_required_msg');
			}
			if ($return_to_msg) {
				$notice_msg = htmlspecialchars($return_to_msg);
			} else {
				$notice_msg = "Devi accedere per visitare la pagina $return_to_url.";
			}
			$this->session()->add_notice('info', $notice_msg, false);
		}
		
		$this->render('account/login', compact('return_to_url','return_to_msg'));
	}
	
	public function logout()
	{
		$this->session()->logout();
		$this->redirect($this->url_for('home'));
	}
}