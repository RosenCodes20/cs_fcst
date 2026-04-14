<?php

$fields = array (
	"title" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
	"slug" => array (
		'not-empty' => 'Мета линк не може да бъде празен',
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
	"keywords" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
	"description" => array (
		255 => "Заглавието не трябва да е повече от 255 символа",
	),
);

if ($_POST) {

	$data = Form::data( $fields, $errors );
	
	$data['slug'] = get_raw( $_POST['slug'] );
	$data['title'] = get_raw( $_POST['title'] );
	$data['keywords'] = get_raw( $_POST['keywords'] );
	$data['description'] = get_raw( $_POST['description'] );
	
	if (!$errors) {

		$result = Seo::update( $seo_category, $id, $data, $errors );
		
		if ( $result && !$errors) {
		
			$_SESSION['message'] = "Промените са записани успешно";
			
			header("location: ".$self_params);
			exit;
			
		} else if (!$errors)
			$errors[] = "Грешка при записване на данните. Проверете всички данни и опитайте отново";
	}

} else if ($id) {
	
	$data = Seo::get( $seo_category, $id );
	
	if ($data)
		$data = (array) $data;
	
/*
	else {
	
		$errors[] = "Този ресурс не може да бъде зареден. Опитайте отново";
		$file = "messages.html";
	}
*/
}

?>
