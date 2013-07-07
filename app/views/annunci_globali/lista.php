<?php
use \lib\Utils\StringUtils;

$view->extend('base_layout');
$page_title = $content_title = "Annunci Globali";
?>

<?php if ($view->session()->is_admin()): ?>
<div id="context_actions" >
	<p>
		<a href="<?php echo $view->url_for('annunci_globali', array('action' => 'inserisci')); ?>" title='Inserisci un annuncio' class='link_inserisci' rel='nofollow'>Inserisci un annuncio</a>
	</p>
</div>
<?php endif; ?>

<?php if ($tot_annunci_globali): ?>
	<div id='lista_annunci' class='lista_contenuti'>
		
<?php // LOOP CORSI
	foreach ($annunci as $annuncio):
?>
		<div class='annuncio' id='annuncio-<?php echo $annuncio->annuncio_id ?>'>
			<h4><a href="<?php echo $annuncio->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($annuncio->titolo_annuncio) ?>" class='link_vedi_annuncio' ><?php echo htmlspecialchars($annuncio->titolo_annuncio) ?></a></h4>
			<div class='contenuto_annuncio'>
				<?php echo htmlspecialchars(StringUtils::truncate($annuncio->contenuto_annuncio, 90)) ?>
			</div>
			<div class='metainfo'>
				Pubblicato il <?php echo $annuncio->data_pubblicazione()
				?> da <a href="<?php echo $annuncio->autore->url_vedi()
				?>" title="Vai al profilo di <?php echo htmlspecialchars($annuncio->autore->nome)
				?>"><?php echo htmlspecialchars($annuncio->autore->nome.' '.$annuncio->autore->cognome) ?></a>.
<?php		if ($view->session()->is_admin()): ?>
				<a href="<?php echo $annuncio->url_modifica() ?>" title='Modifica questo annuncio' class='link_modifica icon' rel='nofollow'>modifica</a>
				<a href="<?php echo $annuncio->url_elimina() ?>" title='Elimina questo annuncio' class='link_elimina icon' rel='nofollow'>elimina</a>
<?php		endif; ?>
			</div>
		</div>
		
<?php // FINE LOOP CORSI
	endforeach;
?>
	</div>
<?php else: ?>
	<p>Nessun annuncio.</p>
<?php endif; ?>