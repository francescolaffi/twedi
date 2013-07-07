<?php
$view->extend('base_layout');
$page_title = "Modifica del corso {$corso->nome_corso}";
$content_title = $page_title;

$form = new \lib\Utils\FormBuilder('modifica_annuncio_corso', $corso->url_modifica(), 'POST');
$form->addTextbox('Nome Corso', 'nome_corso', true, filter_input(INPUT_POST, 'nome_corso')?: $corso->nome_corso);

$form->addHidden('nonce', $view->session()->generate_nonce('modifica_corso'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();

?>