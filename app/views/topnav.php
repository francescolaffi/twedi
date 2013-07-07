<ul class="sf-menu">
	<li>
		<a href="<?php echo $view->url_for('home'); ?>" title="Homepage" <?php
		if ('home' == $view->current_route()): ?> class="current" <?php endif;
		?>>Home</a>
	</li>
	<li>
<?php
$is_corso = in_array($view->current_controller(), array('Corsi','Annunci_Corso','Files_Corso'));
?>
		<a href="<?php echo $view->url_for('corsi'); ?>" title="Vedi i corsi presenti sul sito" <?php
		if ($is_corso): ?> class="current" <?php endif; ?>>Corsi</a>
		<ul>
<?php foreach ($topnav_corsi as $corso): ?>
			<li>
				<a href="<?php echo $corso->url_vedi(); ?>" title="Vai al corso  <?php echo htmlspecialchars($corso->nome_corso) ?>" <?php
				if ($is_corso && $corso->corso_id == $view->current_route_param('id')): ?> class="current" <?php endif;
				?>> <?php echo htmlspecialchars($corso->nome_corso) ?></a>
			</li>
<?php endforeach ?>
			<li>
				<a href="<?php echo $view->url_for('corsi'); ?>" title="Vai alla lista di tutti corsi" <?php
				if ('Corsi' == $view->current_controller() && 'index' == $view->current_action()): ?> class="current" <?php endif;
				?>>Tutti i Corsi</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="<?php echo $view->url_for('annunci_lavoro'); ?>" title="Vedi gli annunci di lavoro pubblicati sul sito" <?php
		if ('Annunci_Lavoro' == $view->current_controller()): ?> class="current" <?php endif;
		?>>Annunci di Lavoro</a>
	</li>
<?php if ($view->session()->is_logged_in()): ?>
	<li>
		<a href="<?php echo $view->url_for('utenti'); ?>" title="Vedi gli utenti iscritti al sito" <?php
		if ('Utenti' == $view->current_controller()): ?> class="current" <?php endif;
		?>>Utenti</a>
	</li>
<?php endif ?>
</ul>
