<?php

require_once ("system/_layout.php");

$id = $_GET['id'];
$current_language_only = true;

if ($id) {

	$check = Menu::get( $id );
	
	if ($check)
		$translation = Translate::set_by_id( $check->language_id );
}

$menus = Menu::search();

$fields = array (
	"title" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
	"parent_id" => array (
		'not-empty' => "Моля, изберете Основно меню",
	),
);

$file = "menus-edit.html";

if ($_POST) {

	$data = Form::data( $fields, $errors );
	
	$data['url'] = trim( $_POST['url'] );
	
	if (!$errors) {

		$result = $id ? Menu::update( $id, $data ) : Menu::create( $data );
		
		if ( $result ) {
		
			$_SESSION['message'] = "Промените са записани успешно";
			
			header("location: ".$_SERVER['PHP_SELF']."?id=".$result);
			exit;
			
		} else
			$errors[] = "Грешка при записване на данните. Проверете всички данни и опитайте отново";
	}

} else if ($id) {
	
	$data = Menu::get( $id );
	
	if ($data) {
		$data = (array) $data;
	} else {
	
		$errors[] = "Този ресурс не може да бъде зареден. Опитайте отново";
		$file = "messages.html";
	}

}

// Режим редактиране

if ($id) {

	$_CURRENT_URI = add_to_link("id=".$id, $_SERVER['REQUEST_URI']);
}

$amenu = "menus";
$bmenu = "menus-edit";
$submenu = "menus-edit";
$layout = "menus-layout.html";

require ("templates/_layout.html");

?>
