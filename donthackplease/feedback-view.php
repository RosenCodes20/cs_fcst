<?php

require_once ("system/_layout.php");

$file = "feedback-view.html";

$id = $_GET['id'];

if ($id) {
	
	MySQL::query("UPDATE feedback SET `read` = 'Yes' WHERE id = ".$id);
	
	$data = Feedback::get( $id );
	
	if ($data)
		$data = (array) $data;
	else {
	
		$errors[] = "Този ресурс не може да бъде зареден. Опитайте отново";
		$file = "messages.html";
	}

}

$amenu = "feedback";
$bmenu = "feedback";
$layout = "feedback-layout.html";

require ("templates/_layout.html");

?>