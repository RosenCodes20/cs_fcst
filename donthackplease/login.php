<?php

$_LOGIN_PAGE = true;

require_once ("system/_layout.php");

if ($_POST) {

	// $data = Form::data( $fields, $errors );

	$data['username'] = get_raw( $_POST['username'] );
	$data['password'] = get_raw( $_POST['password'] );

	if ($data['username'] == 'Потребителско име') {
		unset($data['username']);
	}

	if ($data['password'] == 'Парола') {
		unset($data['password']);
	}

	$user = MySQL::get("SELECT * FROM users WHERE username = '".to_mysql($data['username'])."' AND password = '".md5($data['password'])."'");
	
	if ($user->id) {
		
		$_SESSION['admin_logged_user'][HOST] = true;
		
		// За логване във filemanager
		
		$_SESSION['website_host'] = HOST;
		
		header("location: index.php");
		echo "<script>window.location.href='index.php'</script>";
		exit;
	} else {
		$error = "Грешни потребителски данни";
	}
}

include ("templates/login.html");

?>
