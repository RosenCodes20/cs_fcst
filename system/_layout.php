<?php

session_start();

// unset($_SESSION['_META']);

include ("system/_config.php");
include ("system/functions.php");

include ("modules/mysql.php");
include ("modules/language.php");
include ("modules/image.php");
include ("modules/page.php");
include ("modules/seo.php");
include ("modules/content.php");
include ("modules/events.php");
include ("modules/feedback.php");
include ("modules/form.php");
include ("modules/menu.php");
include ("modules/external_link.php");


// Meta links

$_META = Seo::get_base_slugs();

// Website settings

$settings->default_language = 1;

// Get list of all languages

$languages = Language::get();

$language = Language::set( get_var( $_GET['language'] ) );
include ("system/languages/".($language->abbr).".php");

// SEO инициализиране

$params = get_params_uri();

$expected_params = 1;

if ($params[0] == '') {
	
	$temp = MySQL::get("SELECT slug FROM seo WHERE category = 1 AND item_id = 1 AND language_id = '".($_SESSION['language']->id)."'");
	$params[0] = $temp->slug;
}

$_SEO = Seo::get_by_slug( $params[0] );


// Check for language change

if ($_SEO->language_id != $_SESSION['language']->id) {

	$language = Language::set( false, $_SEO->language_id );
	include ("system/languages/".($language->abbr).".php");
	
}

// Събития за календара

$_LAYOUT_EVENTS = Event::get_upcoming_events();
$_LAYOUT_NEXT_EVENT = Event::get_next();

// Менюта 

$menus = Menu::search();

$_LAYOUT_ANNOUNCEMENTS = Page::get( 8 );

// Други сайтове

$menu_external_links = External_link::get();

// xCheck

// xCheck();
			

?>