<?php

class External_link
{
	function get($id)
	{
		return MySQL::get("SELECT * FROM external_links p WHERE p.id = '$id'");
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
				MySQL::query("UPDATE external_links SET `position` = '".$value."' WHERE id = '".$key."'");
				
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
			$temp = MySQL::get("SELECT MAX(`position`) AS `max` FROM external_links");
			$data['position'] = $temp->max + 10;
		}
		
		MySQL::query("INSERT INTO external_links SET id = NULL, language_id = '".($_SESSION['translation']->id)."', position = '".$data['position']."'");
		
		$id = MySql::insert_id();
		
		if ($id)		
			self::update($id, $data);
		
		return $id;
	}

	function update($id, $data)
	{
		$result = MySQL::query("UPDATE external_links SET title = '".$data['title']."', url = '".$data['url']."', active = '".$data['active']."' WHERE id = '".$id."'");
		
		return $result ? $id : false;
	}

	function delete($id) 
	{
		$result = MySQL::query("DELETE FROM external_links WHERE id = '".$id."'");
		
		return $result;
	}
	
	function search($criteria = false, &$navigation = false)
	{
		$result = array();
		
		$o = "ORDER BY ISNULL(`id`) ASC, p.position, p.id ASC";
		$a = "AND p.language_id = '".($_SESSION['translation']->id)."'";

		if ($criteria['navigation']['page'] < 1)
			$criteria['navigation']['page'] = 1;

		if (!$criteria['navigation']['limit'])
			$criteria['navigation']['limit'] = 30;

		$l = " LIMIT ".( ($criteria['navigation']['page'] - 1) * $criteria['navigation']['limit'] ).", ".$criteria['navigation']['limit'];
			
		$query = "
			SELECT *
			FROM external_links p
			WHERE 1 $a $o $l
		";
		
		$temp = MySQL::get("
			SELECT COUNT(*) AS `records`
			FROM external_links p
			WHERE 1 $a
		");
		
		$navigation->records = $temp->records;
		
		$navigation->pages = $navigation->records / $criteria['navigation']['limit'];

		if ($navigation->pages > (int)$navigation->pages)
			$navigation->pages = (int)$navigation->pages + 1;
		
		$navigation->page = $criteria['navigation']['page'];
		$navigation->limit = $criteria['navigation']['limit'];
		
		$result = MySQL::table($query);		

		return count($result) ? $result : array();
	}
	
	function translated($id)
	{
		// return MySQL::get("SELECT page_id FROM `pages_translations` WHERE language_id = '".($_SESSION['translation']->id)."' AND page_id = '$id'");
	}
}
?>