<?php
$view->extend('base_layout');
$page_title = "Modifica Profilo";
$content_title = $page_title;

$form = new \lib\Utils\FormBuilder('modifica_profilo', $utente->url_modifica(), 'POST');
$form->addTextbox('Nome', 'nome', true, filter_input(INPUT_POST, 'nome') ?: $utente->nome);
$form->addTextbox('Cognome', 'cognome', true, filter_input(INPUT_POST, 'cognome') ?: $utente->cognome);
$form->addTextbox('Anno di nascita', 'anno_nascita', false, filter_input(INPUT_POST, 'anno_nascita') ?: ($utente->anno_nascita ?: ''));
$form->addTextbox('Sito internet', 'sito', false, filter_input(INPUT_POST, 'sito') ?: $utente->sito_internet);
$form->addTextbox('Cellulare', 'cellulare', false, filter_input(INPUT_POST, 'cellulare') ?: $utente->cellulare);

$opzioni_occupazione = array(
	'' => '------',
	'studente non lavora' => 'studio e non lavoro',
	'studente cerca lavoro' => 'studio e cerco lavoro',
	'studente lavora' => 'studio e lavoro',
	'cerca lavoro' => 'ho terminato gli studi e cerco lavoro',
	'lavora' => 'ho terminato gli studi e lavoro',
	'disoccupato' => 'ho terminato gli studi e non lavoro nè cerco lavoro',
);
$form->addSelect('Situazione lavorativa', 'occupazione', false, $opzioni_occupazione, filter_input(INPUT_POST, 'occupazione') ?: $utente->occupazione);
$opzioni_attinenza = array(
	'' => '------',
	'attinente twedi' => 'attinenente al corso di Tecnologie Web e di Internet',
	'attinente corso laurea' => 'attinente al corso di laurea, ma non a Tecnologie Web e di internet',
	'non attinente' => 'non attinente al corso di laurea nè a Tecnologie Web e di Internet',
);
$form->addSelect('Se lavori, il lavoro è ', 'attinenza_lavoro', false, $opzioni_attinenza, filter_input(INPUT_POST, 'attinenza_lavoro') ?: $utente->attinenza_lavoro);
$form->addTextbox('Se lavori, la tua azienda è ', 'azienda', false, filter_input(INPUT_POST, 'azienda') ?: $utente->nome_azienda);
$form->addTextbox('Se lavori, la tua posizione è ', 'posizione', false, filter_input(INPUT_POST, 'posizione') ?: $utente->posizione);

$form->addTextarea('Carriera o altre info', 'carriera', false, filter_input(INPUT_POST, 'carriera') ?: $utente->carriera);

$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();
?>