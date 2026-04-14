<?php

class Event
{
	function get($id)
	{
		return MySQL::get("SELECT * FROM events p, `events_translations` t WHERE t.event_id = p.id AND t.language_id = '".($_SESSION['language']->id)."' AND p.id = '$id' AND t.active = 'Yes'");
	}

	function get_next()
	{
	
		return MySQL::get("
			SELECT p.*, t.*, seo.slug
			FROM events p
			LEFT JOIN `events_translations` t ON t.event_id = p.id
			LEFT JOIN seo ON seo.category = ".(Seo::$categories['events'])." AND seo.item_id = p.id AND seo.language_id = '".($_SESSION['language']->id)."'
			WHERE t.event_date >= '".date("Y-m-d")."' AND t.language_id = '".($_SESSION['language']->id)."' AND t.active = 'Yes' 
			LIMIT 1
		");
	}

	function search($criteria = false, &$navigation = false)
	{
		$result = array();
		
		$o = "ORDER BY t.event_date DESC, ISNULL(`event_id`) ASC, t.position DESC, p.id ASC";

		if ($criteria['navigation']['page'] < 1)
			$criteria['navigation']['page'] = 1;

		if (!$criteria['navigation']['limit'])
			$criteria['navigation']['limit'] = 30;

		if ($criteria['navigation']['from_date'])
			$a .= " AND t.event_date >= '".$criteria['navigation']['from_date']."'";

		if ($criteria['date'])
			$a .= " AND t.event_date = '".$criteria['date']."'";

		$a .= " AND t.active = 'Yes' AND t.language_id = '".($_SESSION['language']->id)."'";
		
		$l = " LIMIT ".( ($criteria['navigation']['page'] - 1) * $criteria['navigation']['limit'] ).", ".$criteria['navigation']['limit'];
			
		$query = "
			SELECT p.*, t.*, seo.slug
			FROM events p
			LEFT JOIN `events_translations` t ON t.event_id = p.id
			LEFT JOIN seo ON seo.category = ".(Seo::$categories['events'])." AND seo.item_id = p.id AND seo.language_id = '".($_SESSION['language']->id)."'
			WHERE 1 $a $o $l
		";
		
		$temp = MySQL::get("
			SELECT COUNT(*) AS `records`
			FROM events p
			LEFT JOIN `events_translations` t ON t.event_id = p.id AND t.language_id = '".($_SESSION['language']->id)."'
			WHERE 1 $a
		");
		
		$navigation->records = $temp->records;
		
		$navigation->pages = $navigation->records / $criteria['navigation']['limit'];

		if ($navigation->pages > (int)$navigation->pages)
			$navigation->pages = (int)$navigation->pages + 1;
		
		$navigation->page = $criteria['navigation']['page'];
		$navigation->limit = $criteria['navigation']['limit'];
		
		$result = MySQL::table($query);		

		if ($result)
			foreach ($result as $key => $item)
				if (!$item->event_id) {
				
					$temp = MySQL::get("SELECT title FROM events_translations WHERE event_id = '".($item->id)."' LIMIT 1");
					
					$result[$key]->tableDisabled = array (
						'title' => $temp->title,
						'type' => 'translation'
					);
				}

		return $result ? $result : array();
	}
	
	function get_upcoming_events( $date = false )
	{
		if (!$date)
			$date = date("Y-m-d");
	
		$result = array();
		
		$o = "ORDER BY t.event_date DESC";
		$a = "AND t.event_date >= '".$date."' AND t.active = 'Yes' AND t.language_id = '".($_SESSION['language']->id)."'";
		
		$query = "
			SELECT p.id, t.event_date
			FROM events p
			LEFT JOIN `events_translations` t ON t.event_id = p.id
			WHERE 1 $a $o $l
		";
		
		return MySQL::table($query);		
	}
	
	function translated($id)
	{
		return MySQL::get("SELECT event_id FROM `events_translations` WHERE language_id = '".($_SESSION['language']->id)."' AND event_id = '$id'");
	}	
}