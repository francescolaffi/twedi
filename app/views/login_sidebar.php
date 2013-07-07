<?php
if( $view->session()->is_logged_in() ):
?>
<h4 class="sidebar_box_title">Benvenuto <?php echo htmlspecialchars($current_user->nome)?></h4>
<ul id="current_user_actions">
<?php if ($current_user->iscrizione_corso_id): ?>
	<li><a href="<?php echo $view->url_for('corso', array('id' => $current_user->iscrizione_corso_id)) ?>" title="Vai alla pagina del corso a cui sei iscritto">Il tuo corso</a></li>
<?php endif; ?>
	<li><a href="<?php echo $view->url_for('Account/modifica_profilo') ?>" title="Modifica il tuo profilo utente">Modifica Profilo</a></li>
	<li><a href="<?php echo $view->url_for('Account/impostazioni_account') ?>" title="Modifica le impostazioni del tuo account">Impostazioni Account</a></li>
	<li><a href="<?php echo $view->url_for('Auth/logout') ?>" title="Effettua il logout">Logout</a></li>
</ul>


<?php
else: 
$form = new \lib\Utils\FormBuilder('login-sidebar', $view->url_for('login'), 'POST');
$form->addTextbox('Email', 'email', false, null);
$form->addPassword('Password', 'password', false);
$form->addCheckbox('Resta loggato', 'resta_loggato', false, true);
$form->addSubmit('submit', 'Entra nel sito');

?>
<h4 class="sidebar_box_title">Login</h4>
<?php
echo $form->render();
?>
<div id="sidebar_login_actions">
	<a href="<?php echo $view->url_for('Account/password_dimenticata') ?>" title="Vai alla procedura per recuperare la password" >Password dimenticata</a>
	<a href="<?php echo $view->url_for('Account/registrazione') ?>" title="Vai alla procedura per creare un nuovo utente" >Registrazione</a>
</div>

<?php
endif;
?>