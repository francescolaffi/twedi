<?php
$view->extend('base_layout');
$page_title = "Impostazioni Account";
$content_title = $page_title;

$is_me = $view->session()->user_id() == $utente->utente_id;

$form_target = $view->url_for('account', array('action' => 'impostazioni_account'));
$form = new \lib\Utils\FormBuilder('impostazioni_account', $form_target, 'POST');
$form->addSelect('Iscrizione al corso', 'corso_id', false, $opzioni_select_corsi, filter_input(INPUT_POST, 'corso_id') ?: $utente->iscrizione_corso_id);
$form->addCheckbox('Ricevi via email gli aggiornamenti sul tuo corso', 'avvisi_corso', false, filter_input(INPUT_POST, 'avvisi_corso') ?: $utente->avviso_annunci_corso);
$form->addCheckbox('Ricevi via email gli annunci di lavoro', 'avvisi_lavoro', false, filter_input(INPUT_POST, 'avvisi_lavoro') ?: $utente->avviso_annunci_lavoro);

if ($is_me) {
	$form->addHtml('<p><strong>NB:</strong> se modifichi la tua email ti verrà mandato un messaggio con un link di conferma, la modifica non sarà effettiva finchè non confermerai la nuova email.</p>');
	$form->addTextbox('Nuova email', 'nuova_email', false, filter_input(INPUT_POST, 'email'));
	$form->addTextbox('Conferma email', 'conferma_email', false, filter_input(INPUT_POST, 'conferma_email'));

	$form->addHtml('<p><strong>NB:</strong> se scegli una nuova password devi confermare la tua identità inserendo anche la password attuale.</p>');
	$form->addPassword('Nuova password', 'nuova_password', false);
	$form->addPassword('Conferma password', 'conferma_password', false);
	$form->addPassword('Password attuale', 'vecchia_password', false);
} else {
	$form->addTextbox('Nuova email', 'nuova_email', false, filter_input(INPUT_POST, 'email'));
	$form->addTextbox('Conferma email', 'conferma_email', false, filter_input(INPUT_POST, 'conferma_email'));

	$form->addPassword('Nuova password', 'nuova_password', false);
	$form->addPassword('Conferma password', 'conferma_password', false);
}

$form->addSubmit('submit', 'Invia');
$form->setErrors($errori_form);
echo $form->render();
?>