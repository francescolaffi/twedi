<?php
$view->extend('base_layout');
$page_title = "Sitemap";
?>

<?php if ($tot_corsi): ?>
	<h3>Elenco Corsi</h3>
	<ul>
		<?php // LOOP CORSI
			foreach ($corsi as $corso):
		?>
			<li>
				<a href="<?php echo $corso->url_vedi() ?>" title="Visita la pagina del corso <?php echo htmlspecialchars($corso->nome_corso) ?>" ><?php echo htmlspecialchars($corso->nome_corso) ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>


 <?php // LOOP ANNUNCI CORSO
if ($tot_annunci_corsi):  ?>
	<h3>Annunci Corso</h3>		
	<ul>
		<?php
			foreach ($annunci_c as $annuncio):
		?>
			<li>
				<a href="<?php echo $annuncio->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($annuncio->titolo_annuncio) ?>" ><?php echo htmlspecialchars($annuncio->titolo_annuncio) ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php // LOOP ANNUNCI LAVORO
if ($tot_annunci_lavoro):  ?>
	<h3>Annunci Lavoro</h3>
	<ul>
		<?php
			foreach ($annunci as $annuncio):
		?>
			<li>
				<a href="<?php echo $annuncio->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($annuncio->titolo_annuncio) ?>"><?php echo htmlspecialchars($annuncio->titolo_annuncio) ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>


<?php // LOOP FILES
if ($tot_files):
?>
	<h3>Files</h3>
	<ul>
		<?php 
			foreach ($files as $file):
		?>
			<li>
				<a href="<?php echo $file->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($file->titolo_file) ?>" ><?php echo htmlspecialchars($file->titolo_file) ?></a>
			</li>
		
		<?php endforeach; ?>
	</ul>
<?php
endif;
?>
