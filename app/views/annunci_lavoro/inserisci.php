<?php
$view->extend('base_layout');
$page_title = "Inserimento annuncio di lavoro";
$content_title = $page_title;

$form_target = $view->url_for('annunci_lavoro', array('action' => 'inserisci'));
 
$form = new \lib\Utils\FormBuilder('inserimento_annuncio_lavoro', $form_target, 'POST');
$form->addTextbox('Titolo', 'titolo', true, filter_input(INPUT_POST, 'titolo'));
$form->addTextarea('Contenuto', 'contenuto', true, filter_input(INPUT_POST, 'contenuto'));
$form->addHidden('nonce', $view->session()->generate_nonce('inserisci_annuncio'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();

?>