<?php
$view->extend('base_layout');
$page_title = 'Files Globali';
$content_title = $page_title;
?>

<?php if ($view->session()->is_admin()): ?>
<div id="context_actions" >
	<p>
		<a href="<?php echo $view->url_for('files_globali', array('action' => 'inserisci')); ?>" title='Inserisci un file' class='link_inserisci' rel='nofollow'>Inserisci un file</a>
	</p>
</div>
<?php endif; ?>

<?php $view->render('files/loop'); ?>