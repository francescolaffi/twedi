<?php
$view->extend('base_layout');
$page_title = "Password dimenticata";
$content_title = $page_title;
?>
<p>Compila ed invia questo form, riceverai un'email con le istruzioni per settare una nuova password</p>

<?php
$form_target = $view->url_for('account', array('action' => 'password_dimenticata'));
$form = new \lib\Utils\FormBuilder('rimanda_conferma_email', $form_target, 'POST');
$form->addTextbox('Email', 'email', true, filter_input(INPUT_POST, 'email'));
$form->addSubmit('submit', 'Recupera password');
#$form->setErrors($errori_form);
echo $form->render();
?>

