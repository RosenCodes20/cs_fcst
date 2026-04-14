<?php

require_once ("system/_layout.php");

$table->records = Event::request( $_GET, $table->navigation );

$table->fields = array(
	'id' => 'Id',
	'title' => 'Заглавие',
	'published_text' => 'Дата',
	'active' => 'Активна',
	// 'position' => 'Позиция',
);

$table->url = "events-edit.php?id=_KEY";

$amenu = "events";
$bmenu = "events";
$layout = "events-layout.html";

$file = "events.html";
require ("templates/_layout.html");

?>