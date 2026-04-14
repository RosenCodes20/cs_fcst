<?php

require_once ("system/_layout.php");

$fields = array (
	"title" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
);

$file = "external-links-edit.html";

$id = $_GET['id'];

if ($_GET['delete-picture']) {

	$id = $_GET['delete-picture'];
	
	unlink("../images/external_links/$id/main.jpg");
	
	foreach (glob("../images/external_links/$id/_resized/main_*.jpg") as $filename)
		unlink($filename);

	$_SESSION['message'] = "Промените са записани успешно";
	
	header("location: ".$_SERVER['PHP_SELF']."?id=".$id);
	exit;
}

if ($_POST) {

	$data = Form::data( $fields, $errors );
	
	$data['url'] = $_POST['url'];
	$picture = $_FILES['picture'];
	
	if (!$errors) {

		$result = $id ? External_link::update( $id, $data ) : External_link::create( $data );

		if ($picture && $result) {
		
			$image_path = "../images/external_links/$result";
		
			if (!file_exists($image_path))
				mkdir($image_path);
		
			if ( upload_picture($picture, $image_path."/main.jpg") ) {
			
				foreach (glob($image_path."/_resized/main_*.jpg") as $filename)
					unlink($filename);
					
			} 
		}

		if ( $result ) {
		
			$_SESSION['message'] = "Промените са записани успешно";
			
			header("location: ".$_SERVER['PHP_SELF']."?id=".$result);
			exit;
			
		} else
			$errors[] = "Грешка при записване на данните. Проверете всички данни и опитайте отново";
	}

} else if ($id) {
	
	$data = External_link::get( $id );
	
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

	//if (!Banner::translated($id))
		//$new_translation = true;
}

$amenu = "external-links";
$bmenu = "external-links-edit";
$submenu = "external-links-edit";
$layout = "external-links-layout.html";

require ("templates/_layout.html");

?>