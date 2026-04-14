<?php

require_once ("system/_layout.php");

$fields = array (
	"title" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
	"short_description" => array (
		255 => "Краткото описание не трябва да е повече от 255 символа",
	),
);

$file = "pages-edit.html";

$id = $_GET['id'];

$menus = Menu::search();

if ($_POST) {

	$data = Form::data( $fields, $errors );
	
	$data['content'] = $_POST['content'];
	
	if ($data['menu_parent_id'] && !$data['menu_title'])
		$errors['menu_title'] = "Моля, попълнете полето Име за меню";

	if (!$errors) {

		$result = $id ? Page::update( $id, $data ) : Page::create( $data );
		
		if ( $result ) {
		
			$_SESSION['message'] = "Промените са записани успешно";
			
			header("location: ".$_SERVER['PHP_SELF']."?id=".$result);
			exit;
			
		} else
			$errors[] = "Грешка при записване на данните. Проверете всички данни и опитайте отново";
	}

} else if ($id) {
	
	$data = Page::get( $id );
	
	if ($data)
		$data = (array) $data;
	else {
	
		$errors[] = "Този ресурс не може да бъде зареден. Опитайте отново";
		$file = "messages.html";
	}

}

// Режим редактиране

if ($id) {

	$_CURRENT_URI = add_to_link("id=".$id, $_SERVER['REQUEST_URI']);
		
	// Добавяне на превод към същесвуваща страница

	if (!Page::translated($id))
		$new_translation = true;
}

$amenu = "pages";
$bmenu = "pages-edit";
$submenu = "pages-edit";
$layout = "pages-layout.html";

require ("templates/_layout.html");

?>