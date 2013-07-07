<?php
use \lib\Utils\StringUtils;

$view->extend('base_layout');
$page_title = "Homepage";
?>

<div class="colonna">
	<h2>Info sui Corsi</h2>
	<p>Attraverso questa sezione potrai visualizzare le informazioni sui corsi inseriti
	nel sito dall'amministratore. Una volta effettuato l'accesso in ogni corso potrai
	vedere gli annunci pubblicati sulla bacheca, i file inseriti dai professori e tutti
	gli utenti iscritti a quel corso.</p>
	<p>Potrai infine iscriverti ai corsi che più ti interessano: in questo modo ti sarà
	più facile comunicare con gli altri utenti del corso stesso.</p>
	<p>Per un accesso rapido seleziona il corso nel menu a tendina sottostante</p>
</div>
<div class="colonna">
	<h2>Annunci di Lavoro</h2>
	<p>Attraverso questa sezione potrai visualizzare gli annunci di lavoro inseriti
	nel sito da ciascun utente registrato. Una volta effettuato l'accesso potrai
	anche inserire il tuo annuncio personale che comparirà nella tua pagina.</p>
	<p>Se sei interessato ad un particolare annuncio puoi entrare nella pagina personale
	dell'utente che ha effettuato l'inserimento e visualizzare tutti i recapiti
	necessari per contattarlo.</p>
	<p>Registrati subito utilizzando il link qui in basso.</p>
</div>
<div class="clearfix"></div>
<div class="colonna">
	<div id="home_links_corsi">
		<ul class="sf-menu">
			<li>
				<a title="Seleziona un corso dal menu a tendina">Seleziona un corso</a>
				<ul>
<?php			foreach ($topnav_corsi as $corso): ?>
					<li>
						<a href="<?php echo $corso->url_vedi(); ?>" title="Vai al corso  <?php echo htmlspecialchars($corso->nome_corso) ?>"> <?php echo htmlspecialchars($corso->nome_corso) ?></a>
					</li>
<?php			endforeach ?>
					<li>
						<a href="<?php echo $view->url_for('corsi'); ?>" title="Vai alla lista di tutti corsi">Tutti i Corsi</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>
<div class="colonna">
	<div id="home_link_registrazione">
		<a href="<?php echo $view->url_for('account?action=registrazione') ?>" title="Registrati come nuovo utente" >Registrati!</a>
	</div>
</div>
<div class="clearfix"></div>
<div class="colonna">
	<div id="home_lista_annunci_corso" class="home_lista_annunci">
		<?php if ($tot_annunci_corsi): 
		 // LOOP ANNUNCI
			foreach ($annunci_c as $annuncio):
		?>
			<div class='home_annuncio' id='annuncio-<?php echo $annuncio->annuncio_id ?>'>
				<h5><a href="<?php echo $annuncio->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($annuncio->titolo_annuncio) ?>" class='link_vedi_annuncio'><?php echo htmlspecialchars($annuncio->titolo_annuncio) ?></a></h5>
				<div class='home_contenuto_annuncio'>
					<?php echo htmlspecialchars(StringUtils::truncate($annuncio->contenuto_annuncio, 40)); ?>
				</div>
			</div>

		<?php // FINE LOOP ANNUNCI
			endforeach;
		?>
		<?php else: ?>
			<p>Ancora nessun annuncio di corso inserito.</p>
		<?php endif; ?>
	</div>
</div>
<div class="colonna">
	<div id="home_lista_annunci_lavoro" class="home_lista_annunci">
		<?php if ($tot_annunci_lavoro): 
		// LOOP ANNUNCI
			foreach ($annunci as $annuncio):
		?>
			<div class='home_annuncio' id='annuncio-<?php echo $annuncio->annuncio_id ?>'>
				<h5><a href="<?php echo $annuncio->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($annuncio->titolo_annuncio) ?>" class='link_vedi_annuncio' ><?php echo htmlspecialchars($annuncio->titolo_annuncio) ?></a></h5>
				<div class='home_contenuto_annuncio'>
					<?php echo htmlspecialchars(StringUtils::truncate($annuncio->contenuto_annuncio, 60)); ?>
				</div>
			</div>
		<?php // FINE LOOP CORSI
			endforeach;
		?>
		<?php else: ?>
			<p>Ancora nessun annuncio di lavoro inserito.</p>
		<?php endif; ?>
	</div>
</div>	

