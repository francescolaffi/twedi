<?php
$view->extend('base_layout');
$page_title = 'inserimento annuncio globale';
$content_title = $page_title;

$form_target = $view->url_for('annunci_globali', array('action' => 'inserisci'));

$form = new \lib\Utils\FormBuilder('inserimento_annuncio_globale', $form_target, 'POST');
$form->addTextbox('Titolo', 'titolo', true, filter_input(INPUT_POST, 'titolo'));
$form->addTextarea('Contenuto', 'contenuto', true, filter_input(INPUT_POST, 'contenuto'));
$form->addSelect('Inserisci nel corso', 'corso_id', false, $opzioni_select_corsi, filter_input(INPUT_POST, 'corso_id') ?: 0);
$form->addHidden('nonce', $view->session()->generate_nonce('inserisci_annuncio'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();

?>
