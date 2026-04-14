<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

session_start();

$RandomStr = md5(microtime());
$ResultStr = substr($RandomStr,0,5);
$NewImage =imagecreatefromjpeg("images/captcha.jpg");

$TextColor = imagecolorallocate($NewImage, rand(1,100), rand(1,100), rand(1,100));
for ($i=0; $i<3; $i++) {
	$LineColor = imagecolorallocate($NewImage,rand(1,250),rand(1,250),rand(1,250));
	imageline($NewImage,rand(1,200),rand(1,40),rand(1,200),rand(1,40),$LineColor);
}
// imagestring($NewImage, 5, rand(1,150), rand(1,30), $ResultStr, $TextColor);
imagettftext($NewImage, 20, rand(-10, 10), rand(0,120), rand(30,40), $TextColor, "system/comicbd.ttf", $ResultStr);

$_SESSION['captcha_key'] = $ResultStr;

header("Content-type: image/jpeg");
imagejpeg($NewImage);



?>