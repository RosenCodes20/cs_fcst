<?php

require_once ("system/_layout.php");

$table->records = Menu::request( $_GET, $table->navigation );

$table->fields = array(
	'id' => 'Id',
	'title' => 'Заглавие',
	'active' => 'Активен',
	'position' => 'Позиция',
);

$table->url = "menus-edit.php?id=_KEY";

$amenu = "menus";
$bmenu = "menus";
$layout = "menus-layout.html";

$file = "menus.html";
require ("templates/_layout.html");

?>