<?php
$view->extend('base_layout');
$page_title = "Modifica del file {$file->titolo_file}";
$content_title = $page_title;

$form = new \lib\Utils\FormBuilder('modifica_file', $file->url_modifica(), 'POST');
$form->addTextbox('Titolo', 'titolo', true, filter_input(INPUT_POST, 'titolo') ?: $file->titolo_file);
$form->addTextarea('Descrizione', 'descrizione', false, filter_input(INPUT_POST, 'descrizione') ?: $file->descrizione_file);
$form->addTextbox('Nome del file', 'nome_file', true, filter_input(INPUT_POST, 'nome_file') ?: $file->nome_originale_file);
$form->addSelect('Inserisci nel corso', 'corso_id', false, $opzioni_select_corsi, filter_input(INPUT_POST, 'corso_id') ?: $file->caricato_in_corso_id);
$form->addHidden('nonce', $view->session()->generate_nonce('modifica_file'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();
?>