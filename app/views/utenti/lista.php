<?php
$view->extend('base_layout');
$page_title = $content_title = "Utenti";
?>
<?php if ($tot_utenti):?>
	<ul id='lista_utenti' class='lista_contenuti'>
		
<?php // LOOP CORSI
	foreach ($utenti as $utente):
		$nome_utente = htmlspecialchars($utente->cognome).", ".htmlspecialchars($utente->nome);
?>
		<li class='lista_utenti' id='utente:<?php echo $utente->utente_id ?>'>
			<a href="<?php echo $utente->url_vedi() ?>" title="Visita il profilo di <?php echo $nome_utente ?>" ><?php echo $nome_utente ?></a>
		</li>
		
<?php // FINE LOOP CORSI
	endforeach;
?>
	</ul>
<?php else: ?>
	<p>Nessun utente iscritto.</p>
<?php endif; ?>
