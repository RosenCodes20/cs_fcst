<?php

require_once ("system/_layout.php");

$_GET['type'] = 'contact';

$table->records = Feedback::request( $_GET, $table->navigation );

$table->fields = array(
	'id' => 'Id',
	'date' => 'Дата',
	'from' => 'От',
	'read' => 'Прочетено'
);

$table->url = "feedback-view.php?id=_KEY";

$amenu = "feedback";
$bmenu = "feedback";
$layout = "feedback-layout.html";

$file = "feedback.html";
require ("templates/_layout.html");

?>