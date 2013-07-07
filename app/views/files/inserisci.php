<?php
$view->extend('base_layout');
$page_title = 'Inserimento file'. (isset($corso) ? " in {$corso->nome_corso}" : '');
$content_title = $page_title;

$form_target = isset($corso) ? $corso->url_inserisci_file() : $view->url_for('files_globali',array('action'=>'inserisci'));

$form = new \lib\Utils\FormBuilder('inserimento_file', $form_target, 'POST');
$form->addTextbox('Titolo', 'titolo', true, filter_input(INPUT_POST, 'titolo'));
$form->addFile('File','file',true);
$form->addTextarea('Descrizione', 'descrizione', false, filter_input(INPUT_POST, 'descrizione'));
$form->addSelect('Inserisci nel corso', 'corso_id', false, $opzioni_select_corsi, filter_input(INPUT_POST, 'corso_id') ?: @$corso->corso_id);
$form->addHidden('nonce', $view->session()->generate_nonce('inserisci_file'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();
?>