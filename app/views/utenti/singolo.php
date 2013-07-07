<?php
$view->extend('base_layout');
$page_title = $content_title = $utente->nome. ' '. $utente->cognome;
?>

<?php if ($view->session()->is_admin()): ?>
<div id="context_actions" >
	<p>Amministrazione Utente:
		<a href="<?php echo $utente->url_modifica() ?>" title='Modifica il profilo di questo utente' class='link_modifica' rel='nofollow'>modifica profilo</a>,
		<a href="<?php echo $utente->url_impostazioni() ?>" title='Modifica le impostazioni account di questo utente' class='link_modifica' rel='nofollow'>modifica impostazioni account</a>,
		<a href="<?php echo $utente->url_elimina() ?>" title='Elimina questo utente' class='link_elimina' rel='nofollow'>cancella account</a>
	</p>
</div>
<?php endif; ?>

<?php $view->render('utenti/profilo'); ?>