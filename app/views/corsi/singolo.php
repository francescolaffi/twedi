<?php
use \lib\Utils\StringUtils;
$view->extend('base_layout');
$page_title = $content_title = $corso->nome_corso;
?>
	
<?php if ($view->session()->is_admin()): ?>
<div id="context_actions" >
	<p>Amministrazione Corso:	
		<a href="<?php echo $corso->url_modifica() ?>" title='Modifica questo corso' class='link_modifica' rel='nofollow'>modifica questo corso</a>,
		<a href="<?php echo $corso->url_elimina() ?>" title='Elimina questo corso' class='link_elimina' rel='nofollow'>elimina questo corso</a>
	</p>
	<p>Contenuti Corso:
		<a href="<?php echo $corso->url_inserisci_annuncio() ?>" title='Inserisci un annuncio' class='link_inserisci' rel='nofollow'>inserisci un annuncio</a>,
		<a href="<?php echo $corso->url_inserisci_file() ?>" title='Inserisci un file' class='link_inserisci' rel='nofollow'>inserisci un file</a>
	</p>
</div>
<?php endif; ?>


	<h3>Annunci</h3>		
<?php if ($tot_annunci): ?>
	<div id='lista_annunci_corso'>
		
<?php // LOOP ANNUNCI
	foreach ($annunci as $annuncio):
?>
		<div class='annuncio' id='annuncio-<?php echo $annuncio->annuncio_id ?>'>

			<h4><a href="<?php echo $annuncio->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($annuncio->titolo_annuncio) ?>" class='link_vedi_annuncio'><?php echo htmlspecialchars($annuncio->titolo_annuncio) ?></a></h4>
			<div class='contenuto_annuncio'>
				<?php echo htmlspecialchars(StringUtils::truncate($annuncio->contenuto_annuncio, 90)); ?>
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
	

<?php // FINE LOOP ANNUNCI
	endforeach;
?>
	</div>
<?php else: ?>
	<p>Nessun annuncio in questo corso.</p>
<?php endif; ?>


<h3>Files Inseriti</h3>
<?php $view->render('files/loop'); ?>


<h3>Studenti Iscritti</h3>	
<?php
if ($view->session()->is_logged_in()):
?>	
<?php if ($tot_studenti): ?>

	<ol id='lista_studenti_iscritti' class='lista_studenti'>
<?php 	// LOOP STUDENTI
	foreach ($studenti as $studente):
		$nome_studente = htmlspecialchars($studente->nome.' '.$studente->cognome);
?>
		<li>
			<a href="<?php echo $studente->url_vedi()
			?>" title="Vai al profilo di <?php echo htmlspecialchars($studente->nome.' '.$studente->cognome)
			?>"> <?php echo htmlspecialchars($studente->nome.' '.$studente->cognome) ?></a> 
		</li>

<?php // FINE LOOP STUDENTI
	endforeach;
?>
	</ol>
<?php else:?>
	<p>Nessuno studente iscritto a questo corso.</p>
<?php endif; ?>
<?php
else:
?>
<p>Effettua il login per vedere la lista degli studenti iscritti.</p>
<?php
endif;
?>

<div class="context_info">
	<a href="<?php echo $view->url_for('corsi') ?>" title='Vai alla lista di tutti i corsi' class='link_section'>Vedi tutti i corsi</a>
</div>
