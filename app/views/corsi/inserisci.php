<?php
$view->extend('base_layout');
$page_title = "Inserimento nuovo corso";
$content_title = $page_title;

$form_target = $view->url_for('corsi', array('action' => 'inserisci'));
 
$form = new \lib\Utils\FormBuilder('inserimento_corso', $form_target, 'POST');
$form->addTextbox('Nome Corso', 'nome_corso', true, filter_input(INPUT_POST, 'nome_corso'));
$form->addHidden('nonce', $view->session()->generate_nonce('inserisci_corso'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();
?>