<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

require_once ("system/_layout.php");

$id = (int)$_GET['id'];

$page = Page::get($id);

if ($page->id) {

	$file = "page.html";
	require ("templates/_layout.html");
	
} else {
	
	require ("404.php");
}

?>