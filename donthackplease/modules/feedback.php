<?php

class Feedback
{
	function get($id)
	{
		return MySQL::get("SELECT * FROM feedback p WHERE p.id = '$id'");
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
			
			/*
			foreach ($data['positions'] as $key => $value)
				MySQL::query("UPDATE pages_translations SET `position` = '".$value."' WHERE page_id = '".$key."' AND language_id = '".($_SESSION['translation']->id)."'");
			*/
			
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
		MySQL::query("INSERT INTO feedback SET id = NULL");
		
		$id = MySql::insert_id();
		
		if ($id)		
			self::update($id, $data);
		
		return $id;
	}

	function update($id, $data)
	{
		// if (self::translated($id)) {
		
			$result = MySQL::query("UPDATE feedback SET `type` = '".$data['type']."', `date` = '".date("Y-m-d H:i:s")."', `from` = '".to_mysql($data['from'])."', content = '".to_mysql($data['content'])."' WHERE id = '".$id."'");
			
		// } else {
		
		/*
			if (!$data['position']) {
				$temp = MySQL::get("SELECT MAX(`position`) AS `max` FROM pages_translations WHERE language_id = '".($_SESSION['translation']->id)."'");
				$data['position'] = $temp->max + 10;
			}
		*/
			
			// $result = MySQL::query("INSERT INTO pages_translations SET page_id = '".$id."', language_id = '".($_SESSION['translation']->id)."', title = '".$data['title']."', content = '".$data['content']."', active = '".$data['active']."', position = '".$data['position']."'");
		//}

		return $result ? $id : false;
	}

	function delete($id) 
	{
		$result = MySQL::query("DELETE FROM feedback WHERE id = '".$id."'");
		
		$temp = MySQL::get("SELECT COUNT(*) AS `count` FROM `feedback` WHERE id = '$id'");

		if ($temp->count == 0)
			MySQL::query("DELETE FROM feedback WHERE id = '".$id."'");
			
		foreach (glob("../images/userfiles/orders/$id.*") as $filename)
			@unlink($filename);
	}
	
	function search($criteria = false, &$navigation = false)
	{
		$result = array();
		
		$o = "ORDER BY p.`date` DESC";

		if ($criteria['navigation']['page'] < 1)
			$criteria['navigation']['page'] = 1;

		if (!$criteria['navigation']['limit'])
			$criteria['navigation']['limit'] = 30;

		if ($criteria['navigation']['type'])
			$a .= " AND `type` = '".$criteria['navigation']['type']."'";


		$l = " LIMIT ".( ($criteria['navigation']['page'] - 1) * $criteria['navigation']['limit'] ).", ".$criteria['navigation']['limit'];
			
		$query = "
			SELECT *
			FROM feedback p
			WHERE 1 $a $o $l
		";
		
		$temp = MySQL::get("
			SELECT COUNT(*) AS `records`
			FROM feedback p
			WHERE 1 $a
		");
		
		$navigation->records = $temp->records;
		
		$navigation->pages = $navigation->records / $criteria['navigation']['limit'];

		if ($navigation->pages > (int)$navigation->pages)
			$navigation->pages = (int)$navigation->pages + 1;
		
		$navigation->page = $criteria['navigation']['page'];
		$navigation->limit = $criteria['navigation']['limit'];
		
		$result = MySQL::table($query);		

/*
		foreach ($result as $key => $item)
			if (!$item->page_id) {
			
				$temp = MySQL::get("SELECT title FROM pages_translations WHERE page_id = '".($item->id)."' LIMIT 1");
				
				$result[$key]->tableDisabled = array (
					'title' => $temp->title,
					'type' => 'translation'
				);
			}
*/

		return count($result) ? $result : array();
	}
	
	function count_not_read( $type )
	{
		$temp = MySQL::get("SELECT COUNT(*) AS result FROM feedback WHERE `type` = '$type' AND `read` = 'No'");
		
		return $temp->result;
	}
	
	function translated($id)
	{
		// return MySQL::get("SELECT page_id FROM `pages_translations` WHERE language_id = '".($_SESSION['translation']->id)."' AND page_id = '$id'");
	}
}
?>