<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

require_once ("system/_layout.php");

$page = Page::get(4);

$fields = array (
	"company" => array (
		200 => _t("грешка-поле-компания"),
	),
	"name" => array (
		'not-empty' => _t('грешка-празно-поле-име'),
		200 => _t('грешка-поле-име'),
	),
	"email" => array (
		'not-empty' => _t('грешка-празно-поле-имейл'),
		200 => _t('грешка-поле-имейл'),
	),
	"address" => array (
		300 => _t('грешка-поле-адрес'),
	),
	"phone" => array (
		200 => _t('грешка-поле-телефон'),
	),
	"comments" => array (
		5000 => _t('грешка-поле-запитване'),
	),
);

if ($_POST) {

	$data = Form::data( $fields, $errors );
	
	if ( strlen($data['comments']) < 1 )
		$errors['comments'] = _t('грешка-празно-поле-запитване');

	if ( !recaptcha() )
		$errors['captcha'] = _t('грешка-поле-captcha');



		
	if (!$errors) {

		$to = EMAIL;
		$subject = "Contact from cs.tu-sofia";
		$random_hash = md5(date('r', time()));
		$headers = "From: ".to_email($data['name'])." <".to_email($data['email']).">\r\nReply-To: ".to_email($data['email'])."\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=\"utf-8\"\n";
		$headers .= "Content-Transfer-Encoding: 7bit";
		
		$body = "
			<b>Компания:</b> ".to_email($data['company'])."<br>
			<b>Име:</b> ".to_email($data['name'])."<br>
			<b>Адрес:</b> ".to_email($data['address'])."<br>
			<b>Телефон:</b> ".to_email($data['phone'])."<br>
			<b>Електронна поща:</b> ".to_email($data['email'])."<br>
			
			<br>
			
			<b>Съобщение:</b><br><br>
			
			".to_email(str_replace("\n", "<br>", trim($data['comments'])));
	
		$result_send = @mail( $to , $subject, $body, $headers );
		
		if (defined('EMAIL2')) {
		
			@mail( EMAIL2, $subject, $body, $headers );
		}
		
		$result_record = Feedback::create( array( 
			'type' => 'contact',
			'from' => $data['name'],
			'content' => $body
		));
		
		if (!$result_send || !$result_record) {

			$errors[] = _t('send-error');
			
			MySQL::query("DELETE FROM feedback WHERE id = ".$result_record);
			
		} else {
		
			header("location: ".HOST."/contacts.php?success=".$result_record);
			exit;
		}
	}
}

$file = $page ? "contacts.html" : "404.html";
include ("templates/_layout.html");



	function recaptcha() 
	{
		try {

			$url = 'https://www.google.com/recaptcha/api/siteverify';
			
			$data = array(
				'secret'   => RECAPTCHA_SECRET,
				'response' => $_POST['g-recaptcha-response'],
				'remoteip' => $_SERVER['REMOTE_ADDR'],
			);

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query( $data ) 
				)
			);

			$context  = stream_context_create( $options );
			$response = @file_get_contents( $url, false, $context );
			
			$result = json_decode( $response, true );
			
			return $result['success']; 	// true | false
		}
		catch ( Exception $e ) {
			
		}
		
		return false;
	}


?>