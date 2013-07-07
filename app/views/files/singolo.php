<?php
$view->extend('base_layout');
$page_title = "File: {$file->titolo_file}".(isset($corso) ? " in {$corso->nome_corso}" : '');
$content_title = $file->titolo_file;
?>

<div class="descrizione_file">
	<?php echo $file->descrizione_formattata() ?>
</div>

<div class="download_file">
	<a href="<?php echo $file->url_download() ?>" title="Scarica il file" rel="nofollow">Scarica il file <?php echo htmlspecialchars($file->nome_originale_file) ?></a>
</div>

<div class="metainfo">
	Caricato il <?php echo $file->data_caricamento() ?>
	da <a href="<?php echo $autore->url_vedi() ?>" title="Vai al profilo di <?php echo htmlspecialchars($autore->nome) ?>"><?php echo htmlspecialchars($autore->nome.' '.$autore->cognome) ?></a>
<?php if (isset($corso)): ?>
	nel corso <a href="<?php echo $corso->url_vedi() ?>" title="Vai al corso <?php echo htmlspecialchars($corso->nome_corso) ?>"><?php echo htmlspecialchars($corso->nome_corso) ?></a>
<?php endif; ?>.
<?php if ($view->session()->is_admin()): ?>
	<a href="<?php echo $file->url_modifica() ?>" title='Modifica questo file' class='link_modifica' rel='nofollow'>modifica</a>
	<a href="<?php echo $file->url_elimina() ?>" title='Elimina questo file' class='link_elimina' rel='nofollow'>elimina</a>
<?php endif; ?>
</div>

<div class="context_info">
<?php if (isset($corso)): ?>
	<a href="<?php echo $corso->url_vedi() ?>#lista_files_corso" title='Vai alla lista di tutti i files caricati nel corso <?php echo htmlspecialchars($corso->nome_corso) ?>' class='link_section'>Vedi tutti i files del corso <?php echo htmlspecialchars($corso->nome_corso) ?></a>
<?php else: ?>
	<a href="<?php echo $view->url_for('files_globali') ?>" title='Vai alla lista di tutti i files globali caricati' class='link_section'>Vedi tutti i files globali</a>
<?php endif; ?>
</div>

