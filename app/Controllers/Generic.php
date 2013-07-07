<?php

namespace app\Controllers;

use lib\Business_Logic\Controller;

class Generic extends Controller {


	public function  render($view, $slots = array()) {
		$corsi_stmt = $this->build_stmt()->table('corsi')->order_by('corso_id DESC')->limit(0,5);
		$corsi_stmt->select();
		$slots['topnav_corsi'] = $this->build_loop($corsi_stmt, 'Corso');


		$current_user = $this->build_template_item('Utente');
		if ($this->session()->is_logged_in()) {
			$user_model = $this->build_model('Utente');
			$user_model->populate($this->session()->user_id());
			$current_user->set_values_from($user_model);
		}
		$slots['current_user'] = $current_user;

		parent::render($view, $slots);
	}
}
?>
