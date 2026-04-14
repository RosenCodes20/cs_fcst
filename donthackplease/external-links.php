<?php

require_once ("system/_layout.php");

$table->records = External_link::request( $_GET, $table->navigation );

$table->fields = array(
	'id' => 'Id',
	'title' => 'Заглавие',
	'active' => 'Активен',
	'position' => 'Позиция',
);

$table->url = "external-links-edit.php?id=_KEY";

$amenu = "external-links";
$bmenu = "external-links";
$layout = "external-links-layout.html";

$file = "external-links.html";
require ("templates/_layout.html");

?>