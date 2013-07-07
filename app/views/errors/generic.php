<?php
$view->extend('base_layout');
$page_title = strpos($error_msg, '.') ? strstr($error_msg, '.', true) : $error_msg;
$content_title = $page_title;

echo "<p>Si è riscontrato un errore: $error_msg</p>"
?>