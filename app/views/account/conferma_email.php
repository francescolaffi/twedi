<?php
$view->extend('base_layout');
$page_title = "Conferma Indirizzo Email";
$content_title = $page_title;
?>
<p>Quanto ti registri o quando cambi il tuo indirizzo email, ti viene inviata una
email contenente un link da cliccare per confermare la validità del tuo indirizzo
email.</p>
<p>Può capitare che questa email non arrivi o che venga scartata dal tuo client di
posta come spam. Se non hai ricevuto l'email di conferma ti invitiamo a controllare
nella cartella "posta indesiderata"/"spam" e nel cestino della tua email, altrimenti
puoi usare il modulo sottostante per reinviare l'email di conferma.</p>

<?php
$form_target = $view->url_for('account', array('action' => 'conferma_email'));
$form = new \lib\Utils\FormBuilder('rimanda_conferma_email', $form_target, 'POST');
$form->addTextbox('Email', 'email', true, filter_input(INPUT_POST, 'email'));
$form->addSubmit('submit', 'Rimanda email');
$form->setErrors($errori_form);
echo $form->render();
?>

