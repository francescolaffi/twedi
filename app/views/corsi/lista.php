<?php
$view->extend('base_layout');
$page_title = $content_title = "Corsi";
?>

<?php	if ($view->session()->is_admin()): ?>
	<p>Corsi:
		<a href="<?php echo $view->url_for('corsi', array('action' => 'inserisci')); ?>" title='Inserisci un nuovo corso' class='link_modifica' rel='nofollow'>Inserisci un nuovo corso</a>
	</p>
<?php	endif; ?>


<?php if ($tot_corsi): ?>
	<ul id='lista_corsi' class='lista_contenuti'>
		
<?php // LOOP CORSI
	foreach ($corsi as $corso):
?>
		<li id='corso:'>
			<h4><a href="<?php echo $corso->url_vedi() ?>" title="Visita la pagina del corso <?php echo htmlspecialchars($corso->nome_corso) ?>" class='link_vedi_corso' ><?php echo htmlspecialchars($corso->nome_corso) ?></a></h4>
		</li>
		
<?php // FINE LOOP CORSI
	endforeach;
?>
	</ul>
<?php else: ?>
	<p>Nessun corso.</p>
<?php endif; ?>