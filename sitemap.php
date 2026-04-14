<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

require_once ("system/_layout.php");

$page = Page::get( 5 );


$file = $page ? "sitemap.html" : "404.html";
include ("templates/_layout.html");

?>