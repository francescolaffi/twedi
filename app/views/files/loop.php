<?php
if ($tot_files):
?>
<div id='lista_files' class='lista_contenuti'>

<?php // LOOP FILES
foreach ($files as $file):
	$autore = $file->autore;
?>
	<div class='file' id='file-<?php echo $file->file_id ?>'>
		<h4><a href="<?php echo $file->url_vedi() ?>" title="Leggi l'annuncio <?php echo htmlspecialchars($file->titolo_file) ?>" class='link_vedi_file' ><?php echo htmlspecialchars($file->titolo_file) ?></a></h4>
		<div class="descrizione_file">
			<?php echo htmlspecialchars(\lib\Utils\StringUtils::truncate($file->descrizione_file, 120)); ?>
		</div>

		<div class="metainfo">
			Caricato il <?php echo $file->data_caricamento() ?>
			da <a href="<?php echo $autore->url_vedi() ?>" title="Vai al profilo di <?php echo htmlspecialchars($autore->nome) ?>"><?php echo htmlspecialchars($autore->nome.' '.$autore->cognome) ?></a>
<?php	if (isset($corso)): ?>
			nel corso <a href="<?php echo $corso->url_vedi() ?>" title="Vai al corso <?php echo htmlspecialchars($corso->nome_corso) ?>"><?php echo htmlspecialchars($corso->nome_corso) ?></a>
<?php	endif; ?>
			<a href="<?php echo $file->url_download() ?>" title='Scarica questo file (devi essere loggato)' class='link_download' rel='nofollow'>download</a>
<?php	if ($view->session()->is_admin()): ?>
			<a href="<?php echo $file->url_modifica() ?>" title='Modifica questo file' class='link_modifica' rel='nofollow'>modifica</a>
			<a href="<?php echo $file->url_elimina() ?>" title='Elimina questo file' class='link_elimina' rel='nofollow'>elimina</a>
<?php	endif; ?>
		</div>
	</div>

<?php // FINE LOOP FILES
endforeach;
?>
</div>
<?php
else:
?>
<p>Nessun file.</p>
<?php
endif;
?>
