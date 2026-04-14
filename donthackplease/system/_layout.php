<?php

session_start();

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


include ("../system/_config.php");
include ("../system/functions.php");

if (!$_SESSION['admin_logged_user'][HOST] && !$_LOGIN_PAGE) {
	header("location: login.php");
	exit;
}

include ("modules/mysql.php");
include ("modules/translate.php");
include ("modules/form.php");
include ("../modules/image.php");
include ("modules/page.php");
include ("modules/seo.php");
include ("modules/event.php");
include ("modules/feedback.php");
include ("modules/menu.php");
include ("modules/external_link.php");


// Admin settings

$settings->default_language = 1;

// Initialize current language for website content translation. Requires $settings variable

$translation = Translate::set( $_GET['translation'] );

// Get all translation languages

$translations = Translate::get();

?>