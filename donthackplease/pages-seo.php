<?php

require_once ("system/_layout.php");

$id = $_GET['id'];

$self_params = $_SERVER['PHP_SELF']."?id=".$id;

// Списъкът с възможните категории е във файл modules/seo.php

$seo_category = "page";

include("seo.php");

$amenu = "pages";
$bmenu = "pages-edit";
$submenu = "pages-seo";
$layout = "pages-layout.html";

$file = "seo.html";
require ("templates/_layout.html");

?>
