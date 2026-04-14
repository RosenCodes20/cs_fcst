<?php

class Menu
{
	function get($id)
	{
		return MySQL::get("SELECT * FROM menus p WHERE p.id = '$id'");
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
				MySQL::query("UPDATE menus SET `position` = '".$value."' WHERE id = '".$key."'");
				
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
		if (!$data['position']) {
		
			$temp = MySQL::get("SELECT MAX(`position`) AS `max` FROM menus WHERE language_id = '".($_SESSION['translation']->id)."'");
			$data['position'] = $temp->max + 10;
		}
		
		if (!$data['type'])
			$data['type'] = 'general';
	
		MySQL::query("INSERT INTO menus SET id = NULL, position = '".$data['position']."', language_id = '".($_SESSION['translation']->id)."'");
		
		$id = MySql::insert_id();
		
		if ($id)		
			self::update($id, $data);
		
		return $id;
	}

	function update($id, $data)
	{
		$u = "";

		if (isset($data['parent_id']))
			$u .= ( $u ? ", " : "" )."parent_id = '".$data['parent_id']."'";

		if (isset($data['title']))
			$u .= ( $u ? ", " : "" )."title = '".$data['title']."'";

		if (isset($data['active']))
			$u .= ( $u ? ", " : "" )."active = '".$data['active']."'";

		if (isset($data['type']))
			$u .= ( $u ? ", " : "" )."`type` = '".$data['type']."'";

		if (isset($data['item_id']))
			$u .= ( $u ? ", " : "" )."`item_id` = '".$data['item_id']."'";

		if (isset($data['target']))
			$u .= ( $u ? ", " : "" )."`target` = '".$data['target']."'";

		if (isset($data['url'])) {
		
			$url = str_replace( array("\\", "'", "\""), array("\\\\", "", "" ), $data['url']);		
			
			$u .= ( $u ? ", " : "" )."`url` = '".$url."'";
		}

		$result = MySQL::query("UPDATE menus SET $u WHERE id = '".$id."'");
		
		return $result ? $id : false;
	}

	function delete($id) 
	{
		$items = MySQL::table("SELECT * FROM menus WHERE parent_id = $id");
		
		MySQL::query("DELETE FROM menus WHERE id = '".$id."'");
		
		if ($items)
			foreach ($items as $item)
				self::delete($item->id);
	}

	function delete_by_page_id($id) 
	{
		$items = MySQL::table("SELECT * FROM menus WHERE `type` = 'page' AND item_id = '".$id."' AND language_id = '".($_SESSION['translation']->id)."'");
		
		if ($items)
			foreach ($items as $item)
				self::delete($item->id);
	}

	function search($criteria = false, &$navigation = false)
	{
		$result = array();
		
		$o = "ORDER BY ISNULL(`id`) ASC, p.position, p.id ASC";
		$a = "AND ( p.language_id = '".($_SESSION['translation']->id)."' OR p.type = 'system' )";
		
		if ($criteria['navigation']['page'] < 1)
			$criteria['navigation']['page'] = 1;

		if (!$criteria['navigation']['limit'])
			$criteria['navigation']['limit'] = 100000;

		if ($criteria['navigation']['type'])
			$a .= " AND `type` = '".$criteria['navigation']['type']."'";

		if ($criteria['navigation']['item_id'])
			$a .= " AND `item_id` = '".$criteria['navigation']['item_id']."'";

		$l = " LIMIT ".( ($criteria['navigation']['page'] - 1) * $criteria['navigation']['limit'] ).", ".$criteria['navigation']['limit'];
			
		$query = "
			SELECT *
			FROM menus p
			WHERE 1 $a $o $l
		";
		
		$temp = MySQL::get("
			SELECT COUNT(*) AS `records`
			FROM menus p
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
			
				if ($item->type == 'system') {
			
					$result[$key]->tableDisabled = array (
						'title' => $item->title,
						'type' => 'system'
					);
					
				} 
			}

		return count($result) ? $result : array();
	}
	
}
?>