<?php
$view->extend('base_layout');
$page_title = "Entra nel sito";
$content_title = $page_title;

$form = new \lib\Utils\FormBuilder('login', $view->url_for('login'), 'POST');
$form->addTextbox('Email', 'email', true, filter_input(INPUT_POST, 'email'));
$form->addPassword('Password', 'password', true);
$form->addCheckbox('Resta loggato', 'resta_loggato', false, filter_input(INPUT_POST, 'resta_loggato'));
if ($return_to_url) {
	$form->addHidden('return_to_url', $return_to_url);
	if ($return_to_msg) {
		$form->addHidden('return_to_msg', $return_to_msg);
	}
}
$form->addSubmit('submit', 'Entra nel sito');
echo $form->render();
?>