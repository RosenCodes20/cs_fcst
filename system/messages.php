<?php

if ($_SESSION['message']) {

	$message = $_SESSION['message'];
	unset ($_SESSION['message']);
	
}

include ("templates/messages.html");

?>