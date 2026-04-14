<?php

class Page
{
	function get($id)
	{
		$result = MySQL::get("SELECT * FROM pages p LEFT JOIN `pages_translations` t ON t.page_id = p.id AND t.language_id = '".($_SESSION['translation']->id)."' WHERE p.id = '$id'");
		
		if ($result) {

			$menu = Menu::search( array( 'navigation' => array ( 'type' => 'page', 'item_id' => $id ) ) );
		
			if ($menu[0]->id) {
			
				$result->menu_parent_id = $menu[0]->parent_id;
				$result->menu_title = $menu[0]->title;
				$result->menu_active = $menu[0]->active;
			}
		}
		
		return $result;
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
				MySQL::query("UPDATE pages_translations SET `position` = '".$value."' WHERE page_id = '".$key."' AND language_id = '".($_SESSION['translation']->id)."'");
				
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
		MySQL::query("INSERT INTO pages SET id = NULL");
		
		$id = MySql::insert_id();
		
		if ($id)		
			self::update($id, $data);
		
		return $id;
	}

	function update($id, $data)
	{
		$SQL = new MySQL();
		
		if (self::translated($id)) {
		
			$menu_check = false;
		
			$menu = Menu::search( array( 'navigation' => array ( 'type' => 'page', 'item_id' => $id ) ) );
			
			if ($data['menu_parent_id'] && $data['menu_title']) {
		
				if ($menu[0]->id) {
				
					$menu_check = Menu::update( $menu[0]->id, array(
						'parent_id' => $data['menu_parent_id'], 
						'title' => $data['menu_title'], 
						'active' => $data['active']
					));
				
				} else {
					
					$menu_check = Menu::create( array( 
						'parent_id' => $data['menu_parent_id'], 
						'title' => $data['menu_title'], 
						'active' => $data['active'], 
						'type' => 'page', 
						'item_id' => $id 
					));
				}
				
			} else {
			
				if ($menu[0]->id) {
				
					Menu::update( $menu[0]->id, array(
						'active' => 'No'
					));
				}
			
				$menu_check = true;
			}
			
			if ($menu_check) {
			
				$temp_content = str_replace( array( "'", "\\" ), array( "\'", "\\\\" ), $data['content']);
			
				$result = $SQL->query("UPDATE pages_translations SET title = '".$data['title']."', short_description = '".$data['short_description']."', content = '".$temp_content."', active = '".$data['active']."' WHERE page_id = '".$id."' AND language_id = '".($_SESSION['translation']->id)."'");
			}
			
		} else {
		
			if (!$data['position']) {
				$temp = MySQL::get("SELECT MAX(`position`) AS `max` FROM pages_translations WHERE language_id = '".($_SESSION['translation']->id)."'");
				$data['position'] = $temp->max + 10;
			}
			
			$temp_content = str_replace( array( "'", "\\" ), array( "\'", "\\\\" ), $data['content']);
			
			$result = $SQL->query("INSERT INTO pages_translations SET page_id = '".$id."', language_id = '".($_SESSION['translation']->id)."', short_description = '".$data['short_description']."', title = '".$data['title']."', content = '".$temp_content."', active = '".$data['active']."', position = '".$data['position']."'");
			
			if ($result) {
			
				if ($data['menu_parent_id'] && $data['menu_title']) {
				
					$menu_check = Menu::create( array( 
						'parent_id' => $data['menu_parent_id'], 
						'title' => $data['menu_title'], 
						'active' => $data['active'], 
						'type' => 'page', 
						'item_id' => $id 
					));
				}
			}
		}

		return $result ? $id : false;
	}

	function delete($id) 
	{
		$result = MySQL::query("DELETE FROM pages_translations WHERE page_id = '".$id."' AND language_id = '".($_SESSION['translation']->id)."'");
		
		$temp = MySQL::get("SELECT COUNT(*) AS `count` FROM `pages_translations` WHERE page_id = '$id'");

		if ($temp->count == 0)
			MySQL::query("DELETE FROM pages WHERE id = '".$id."'");
			
		$menu = Menu::search( array( 'navigation' => array ( 'type' => 'page', 'item_id' => $id ) ) );
		
		if ($menu[0]->id) {
		
			Menu::update( $menu[0]->id, array(
				'active' => 'No'
			));
		}
	}
	
	function search($criteria = false, &$navigation = false)
	{
		$result = array();
		
		$o = "ORDER BY ISNULL(`page_id`) ASC, t.position, p.id ASC";

		if ($criteria['navigation']['page'] < 1)
			$criteria['navigation']['page'] = 1;

		if (!$criteria['navigation']['limit'])
			$criteria['navigation']['limit'] = 30;

		$l = " LIMIT ".( ($criteria['navigation']['page'] - 1) * $criteria['navigation']['limit'] ).", ".$criteria['navigation']['limit'];
			
		$query = "
			SELECT *
			FROM pages p
			LEFT JOIN `pages_translations` t ON t.page_id = p.id AND t.language_id = '".($_SESSION['translation']->id)."'
			WHERE 1 $a $o $l
		";
		
		$temp = MySQL::get("
			SELECT COUNT(*) AS `records`
			FROM pages p
			WHERE 1 $a
		");
		
		$navigation->records = $temp->records;
		
		$navigation->pages = $navigation->records / $criteria['navigation']['limit'];

		if ($navigation->pages > (int)$navigation->pages)
			$navigation->pages = (int)$navigation->pages + 1;
		
		$navigation->page = $criteria['navigation']['page'];
		$navigation->limit = $criteria['navigation']['limit'];
		
		$result = MySQL::table($query);		

		foreach ($result as $key => $item) {
		
			if ($item->page_id == 1 || $item->page_id == 2 || $item->page_id == 3 || $item->page_id == 4 || $item->page_id == 5 || $item->page_id == 6 || $item->page_id == 7 || $item->page_id == 8) {
			
				$result[$key]->deleteDisabled = true;
			}
		
			if (!$item->page_id) {
			
				$temp = MySQL::get("SELECT title FROM pages_translations WHERE page_id = '".($item->id)."' LIMIT 1");
				
				$result[$key]->tableDisabled = array (
					'title' => $temp->title,
					'type' => 'translation'
				);
			}
		}

		return count($result) ? $result : array();
	}
	
	function translated($id)
	{
		return MySQL::get("SELECT page_id FROM `pages_translations` WHERE language_id = '".($_SESSION['translation']->id)."' AND page_id = '$id'");
	}
}