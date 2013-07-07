<?php
$view->extend('base_layout');
$page_title = "Registrazione nuovo utente";
$content_title = $page_title;

$form_target = $view->url_for('account', array('action' => 'registrazione'));
$form = new \lib\Utils\FormBuilder('registrazione', $form_target, 'POST');
$form->addTextbox('Nome', 'nome', true, filter_input(INPUT_POST, 'nome'));
$form->addTextbox('Cognome', 'cognome', true, filter_input(INPUT_POST, 'cognome'));
$form->addTextbox('Email', 'email', true, filter_input(INPUT_POST, 'email'));
$form->addTextbox('Conferma email', 'conferma_email', true, filter_input(INPUT_POST, 'conferma_email'));
$form->addPassword('Password', 'password', true);
$form->addPassword('Conferma password', 'conferma_password', true);
$form->addCheckbox('Accetto le condizioni', 'accetto_condizioni', true, filter_input(INPUT_POST, 'accetto_condizioni')); //TODO: le condizioni di registrazione!
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();
?>