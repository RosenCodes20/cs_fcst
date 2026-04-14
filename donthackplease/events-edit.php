<?php

require_once ("system/_layout.php");

$fields = array (
	"title" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
	"event_date" => array ( 
		"not-empty" => "Датата на събитието не може да бъде празна",
	)
);

$file = "events-edit.html";

if ($_GET['delete-picture']) {

	$id = $_GET['delete-picture'];
	
	unlink("../images/events/$id/main.jpg");
	
	foreach (glob("../images/events/$id/_resized/main_*.jpg") as $filename)
		unlink($filename);

	$_SESSION['message'] = "Промените са записани успешно";
	
	header("location: ".$_SERVER['PHP_SELF']."?id=".$id);
	exit;
}

$id = $_GET['id'];

if ($_POST) {

	$data = Form::data( $fields, $errors );
	
	$data['event_date'] = cDate($_POST['event_date']);
	$data['content'] = $_POST['content'];
	$picture = $_FILES['picture'];
	
	if (!$errors) {

		$result = $id ? Event::update( $id, $data ) : Event::create( $data );
		
		if ($picture && $result) {
		
			$image_path = "../images/events/$result";
		
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
	
	$data = Event::get( $id );
	
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

	if (!Event::translated($id))
		$new_translation = true;
}

$amenu = "events";
$bmenu = "events-edit";
$submenu = "events-edit";
$layout = "events-layout.html";

require ("templates/_layout.html");

?>