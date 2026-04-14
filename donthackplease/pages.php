<?php

require_once ("system/_layout.php");

$table->records = Page::request( $_GET, $table->navigation );

$table->fields = array(
	'id' => 'Id',
	'title' => 'Заглавие',
	'active' => 'Активна',
	'position' => 'Позиция',
);

$table->url = "pages-edit.php?id=_KEY";

$amenu = "pages";
$bmenu = "pages";
$layout = "pages-layout.html";

$file = "pages.html";
require ("templates/_layout.html");

?>