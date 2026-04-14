<?php

require_once ("system/_layout.php");

$_page = $_GET['help-page'] ? $_GET['help-page'] : 'tables';

$amenu = "help";
$submenu = $_page;
$layout = "help-layout.html";

include ("help/".$_page.".php");

$file = $file ? $file : "help/".$_page.".html";
require ("templates/_layout.html");

?>