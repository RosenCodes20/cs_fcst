<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

require_once ("system/_layout.php");

$event = Event::get( (int)$_GET['id'] );

if ($event->id) {

	$temp = Seo::get_all( 'events', $event->id );
	
	if ($temp) {
	
		$temp_main_page = Seo::get_all( 'page', 6 );
		
		if ($temp_main_page) {
		
			foreach ($temp_main_page as $item)
				$main_page_slugs[$item->language_id] = $item;
		}
	
		foreach ($temp as $item) {
		
			if ($main_page_slugs[$item->language_id]->slug && $item->slug)
				$_META['events'][$item->language_id][$event->id] = $item->slug;
		}
	}
}

$file = $event ? "events.html" : "404.html";
include ("templates/_layout.html");

?>