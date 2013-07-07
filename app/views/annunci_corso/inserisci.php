<?php
$view->extend('base_layout');
$page_title = "Inserimento annuncio in {$corso->nome_corso}";
$content_title = $page_title;

$form_target = $view->url_for('annunci_corso', array('id' => $corso->corso_id, 'action' => 'inserisci'));

$form = new \lib\Utils\FormBuilder('inserimento_annuncio_corso', $form_target, 'POST');
$form->addTextbox('Titolo', 'titolo', true, filter_input(INPUT_POST, 'titolo'));
$form->addTextarea('Contenuto', 'contenuto', true, filter_input(INPUT_POST, 'contenuto'));
$form->addSelect('Inserisci nel corso', 'corso_id', false, $opzioni_select_corsi, filter_input(INPUT_POST, 'corso_id') ?: $corso->corso_id);
$form->addHidden('nonce', $view->session()->generate_nonce('inserisci_annuncio'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();

?>