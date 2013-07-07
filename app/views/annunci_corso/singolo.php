<?php
$view->extend('base_layout');
$page_title = "Annuncio: {$annuncio->titolo_annuncio} in {$corso->nome_corso}";
$content_title = $annuncio->titolo_annuncio;

$nome_corso = htmlspecialchars($corso->nome_corso);
?>

<div class="contenuto_annuncio">
	<?php echo $annuncio->contenuto_formattato() ?>
</div>		

<div class="metainfo">
	Pubblicato il <?php echo $annuncio->data_pubblicazione()
	?> da <a href="<?php echo $autore->url_vedi()
	?>" title="Vai al profilo di <?php echo htmlspecialchars($autore->nome)
	?>"><?php echo htmlspecialchars($autore->nome.' '.$autore->cognome)
	?></a> nel corso <a href="<?php echo $corso->url_vedi()
	?>" title="Vai al corso <?php echo $nome_corso ?>"><?php echo $nome_corso ?></a>.
<?php if ($view->session()->is_admin()): ?>
	<a href="<?php echo $annuncio->url_modifica() ?>" title='Modifica questo annuncio' class='link_modifica icon' rel='nofollow'>modifica</a>
	<a href="<?php echo $annuncio->url_elimina() ?>" title='Elimina questo annuncio' class='link_elimina icon' rel='nofollow'>elimina</a>
<?php endif; ?>
</div>

<div class="context_info">
	<a href="<?php echo $corso->url_vedi() ?>#lista_annunci_corso" title='Vai alla lista di tutti gli annunci pubblicati nel corso <?php echo $nome_corso ?>' class='link_section'>Vedi tutti gli annunci del corso <?php echo $nome_corso ?></a>
</div>

