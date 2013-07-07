<?php

namespace app\Controllers;

class Errors extends Generic {

	public function display_error($error_msg, $error_type = null)
	{
		if ($error_type) {
			try {
				@$this->render("errors/$error_type", compact('error_msg','error_type'));
				die();
			} catch (\Exception $e) {}
		}
		
		@$this->render('errors/generic', compact('error_msg','error_type'));
		die();
	}
}
?>
