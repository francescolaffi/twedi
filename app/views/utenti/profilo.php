<div class="profilo">
	<?php if ($utente->anno_nascita) {?><p>Anno di Nascita: <?php echo $utente->anno_nascita ?></p><?php }?>
	<?php if ($utente->sito_internet) {?><p>Sito Internet: <?php echo htmlspecialchars($utente->sito_internet) ?></p><?php }?>
	<?php if ($utente->cellulare){?><p>Cellulare: <?php echo htmlspecialchars($utente->cellulare) ?></p><?php }?>

	<p>email: <?php echo $utente->email ?></p>
	<?php if ($utente->occupazione){?><p>Situazione lavorativa: <?php echo htmlspecialchars($utente->occupazione) ?></p><?php }?>
	<?php if ($utente->attinenza_lavoro){?><p>Il lavoro è <?php echo htmlspecialchars($utente->attinenza_lavoro) ?></p><?php }?>
	<?php if ($utente->nome_azienda){?><p>L'azienda è <?php echo htmlspecialchars($utente->nome_azienda) ?></p><?php }?>
	<?php if ($utente->posizione){?><p>La posizione è <?php echo htmlspecialchars($utente->posizione) ?></p><?php }?>
	<?php if ($utente->carriera){?><p class='carriera'>Carriera: <?php echo htmlspecialchars($utente->carriera) ?></p><?php }?>
	<?php if ($utente->iscrizione_corso_id){?><p class='iscritto_al_corso'>Iscritto al corso: <a href="<?php echo $corso->url_vedi() ?>" title="Vai al corso <?php echo htmlspecialchars($corso->nome_corso) ?>">
	<?php echo htmlspecialchars($corso->nome_corso) ?></a></p><?php }?>
</div>
