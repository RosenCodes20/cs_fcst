<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
require_once ("system/_layout.php");

$_GET['id'] = 1;
$page = Page::get(1);

$file = "home.html";
include ("templates/_layout.html");

?>