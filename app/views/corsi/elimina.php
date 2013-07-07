<?php
$view->extend('base_layout');
$page_title = "Elimina il corso {$corso->nome_corso}";
$content_title = $page_title;

$form = new \lib\Utils\FormBuilder('modifica_annuncio_corso', $corso->url_elimina(), 'POST');
$form->addHidden('nonce', $view->session()->generate_nonce('elimina_corso'));
$form->addSubmit('submit', 'Confermo');
?>

<p>Vuoi davvero eliminare questo corso?</p>
<?php echo $form->render(); ?>