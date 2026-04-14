<?php

class Event
{
	function get($id)
	{
		return MySQL::get("SELECT * FROM events p LEFT JOIN `events_translations` t ON t.event_id = p.id AND t.language_id = '".($_SESSION['translation']->id)."' WHERE p.id = '$id'");
	}
	
	function request($params, &$navigation)
	{
		$data = get_post_data( $_POST );
	
		if ($params['delete']) {
		
			self::delete($params['delete']);
			
			header("location: ".remove_from_link("delete", $_SERVER['REQUEST_URI']));
			exit;
		}
		
		if ($data['positions']) {
			
			foreach ($data['positions'] as $key => $value)
				MySQL::query("UPDATE events_translations SET `position` = '".$value."' WHERE event_id = '".$key."' AND language_id = '".($_SESSION['translation']->id)."'");
				
			header("location: ".$_SERVER['REQUEST_URI']);
			exit;
		}
		
		$result = self::search( array('navigation' => $params), $navigation );
		
		if (!$result && $navigation->pages > 0 && $navigation->page != $navigation->pages) {
		
			$params['page'] = $navigation->pages;
		
			$result = self::search( array('navigation' => $params), $navigation );
		}
		
		return $result;
	}
	
	function create($data)
	{
		MySQL::query("INSERT INTO events SET id = NULL");
		
		$id = MySql::insert_id();
		
		if ($id)		
			self::update($id, $data);
		
		return $id;
	}

	function update($id, $data)
	{
		if (self::translated($id)) {
		
			$temp_content = str_replace( array( "'", "\\" ), array( "\'", "\\\\" ), $data['content']);
		
			$result = MySQL::query("UPDATE events_translations SET title = '".$data['title']."', event_date = '".$data['event_date']."', content = '".$temp_content."', menu_desc = '".$data['menu_desc']."', short_desc = '".$data['short_desc']."', active = '".$data['active']."', show_image = '".$data['show_image']."' WHERE event_id = '".$id."' AND language_id = '".($_SESSION['translation']->id)."'");
			
		} else {
		
			if (!$data['position']) {
				$temp = MySQL::get("SELECT MAX(`position`) AS `max` FROM events_translations WHERE language_id = '".($_SESSION['translation']->id)."'");
				$data['position'] = $temp->max + 10;
			}
			
			$temp_content = str_replace( array( "'", "\\" ), array( "\'", "\\\\" ), $data['content']);
			
			$result = MySQL::query("INSERT INTO events_translations SET event_id = '".$id."', language_id = '".($_SESSION['translation']->id)."', title = '".$data['title']."', event_date = '".$data['event_date']."', content = '".$temp_content."', menu_desc = '".$data['menu_desc']."', short_desc = '".$data['short_desc']."', active = '".$data['active']."', position = '".$data['position']."', show_image = '".$data['show_image']."'");
		}

		return $result ? $id : false;
	}

	function delete($id) 
	{
		$result = MySQL::query("DELETE FROM events_translations WHERE event_id = '".$id."' AND language_id = '".($_SESSION['translation']->id)."'");
		
		$temp = MySQL::get("SELECT COUNT(*) AS `count` FROM `events_translations` WHERE event_id = '$id'");

		if ($temp->count == 0)
			MySQL::query("DELETE FROM events WHERE id = '".$id."'");
	}
	
	function search($criteria = false, &$navigation = false)
	{
		$result = array();
		
		$o = "ORDER BY t.event_date DESC";

		if ($criteria['navigation']['page'] < 1)
			$criteria['navigation']['page'] = 1;

		if (!$criteria['navigation']['limit'])
			$criteria['navigation']['limit'] = 30;

		$l = " LIMIT ".( ($criteria['navigation']['page'] - 1) * $criteria['navigation']['limit'] ).", ".$criteria['navigation']['limit'];
			
		$query = "
			SELECT *
			FROM events p
			LEFT JOIN `events_translations` t ON t.event_id = p.id AND t.language_id = '".($_SESSION['translation']->id)."'
			WHERE 1 $a $o $l
		";
		
		$temp = MySQL::get("
			SELECT COUNT(*) AS `records`
			FROM events p
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
			foreach ($result as $key => $item) {
			
				$result[$key]->published_text = get_date_format($item->event_date);
			
				if (!$item->event_id) {
				
					$temp = MySQL::get("SELECT title FROM events_translations WHERE event_id = '".($item->id)."' LIMIT 1");
					
					$result[$key]->tableDisabled = array (
						'title' => $temp->title,
						'type' => 'translation'
					);
				}
			}

		return $result ? $result : array();
	}
	
	function translated($id)
	{
		return MySQL::get("SELECT event_id FROM `events_translations` WHERE language_id = '".($_SESSION['translation']->id)."' AND event_id = '$id'");
	}
}
?>