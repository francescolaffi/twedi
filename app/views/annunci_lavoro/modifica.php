<?php
$view->extend('base_layout');
$page_title = "Modifica dell'annuncio {$annuncio->titolo_annuncio}";
$content_title = $page_title;

$form = new \lib\Utils\FormBuilder('modifica_annuncio_lavoro', $annuncio->url_modifica(), 'POST');
$form->addTextbox('Titolo', 'titolo', true, filter_input(INPUT_POST, 'titolo') ?: $annuncio->titolo_annuncio);
$form->addTextarea('Contenuto', 'contenuto', true, filter_input(INPUT_POST, 'contenuto') ?: $annuncio->contenuto_annuncio);

$form->addHidden('nonce', $view->session()->generate_nonce('modifica_annuncio'));
$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();

?>