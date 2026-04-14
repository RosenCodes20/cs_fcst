<?php

class Seo
{
	static $categories = array(
		'page' => 1,
		'events' => 2,
	);
	
	// Функцията да се използва само в админската част
	
	function get( $category_key, $item_id )
	{
		$category = self::$categories[$category_key];
		
		if (!$category)
			return false;
	
		return MySQL::get("SELECT * FROM seo WHERE category = $category AND item_id = $item_id AND language_id = '".($_SESSION['translation']->id)."'");
	}

	// Oбща функцията за админската част и за сайт частта
	
	function get_all( $category_key, $item_id = false )
	{
		$category = self::$categories[$category_key];
		
		if (!$category || !$item_id)
			return false;
			
		$a = "";
		
		if ($item_id)
			$a .= " AND item_id = $item_id";
			
		$query = "SELECT * FROM seo WHERE category = $category $a";
		
		return MySQL::table($query);
	}

	// Oбща функцията за админската част и за сайт частта

	function get_base_slugs()
	{
		if ($_SESSION['_META']) {
		
			$_META = $_SESSION['_META'];
		
		} else {
		
			// Страници
			
			$pages = MySQL::table("SELECT pages.id, seo.language_id, seo.slug FROM pages LEFT JOIN seo ON pages.id = seo.item_id WHERE seo.category = ".(Seo::$categories['page']));
			
			if ($pages) {
			
				foreach( $pages as $item ) {
				
					if ($item->slug)
						$_META['page'][$item->language_id][$item->id] = $item->slug;
				}
			}

			$_SESSION['_META'] = $_META;
		}
		
		return $_META;
	}
	
	// Oбща функцията за админската част и за сайт частта
	
	function get_by_slug( $slug )
	{
		$slug = get_raw( $slug );
	
		if (!$slug)
			return false;
	
		$record = MySQL::get("SELECT * FROM seo WHERE slug = '$slug'");
		
		if (!$record)
			return false;
			
		$record->key = false;
			
		foreach ( self::$categories as $key => $item ) {
			if ( $record->category == $item ) {
			
				$record->key = $key;
				break;
			}
		}
		
		return $record;
	}
	
	// Функцията да се използва само в админската част
	
	function update($category_key, $item_id, $data, &$errors)
	{
		$category = self::$categories[$category_key];
		
		if (!$category)
			return false;
			
		$exists = MySQL::get("
			SELECT * FROM seo WHERE 
			slug = '".$data['slug']."' 
			AND ( 
				( category <> $category OR item_id <> $item_id ) 
				OR ( category = $category AND item_id = $item_id AND language_id <> '".($_SESSION['translation']->id)."')
			)
		");
		
		if ($exists) {
		
			$errors['slug'] = "Този мета линк е използван на друго място";
			
			return false;
		}
			
		$record_exists = MySQL::get("SELECT * FROM seo WHERE category = $category AND item_id = $item_id AND language_id = '".($_SESSION['translation']->id)."'");
		
		if ($record_exists) {
		
			$result = MySQL::query("UPDATE seo SET slug = '".$data['slug']."', title = '".$data['title']."', keywords = '".$data['keywords']."', description = '".$data['description']."' WHERE category = $category AND item_id = $item_id AND language_id = '".($_SESSION['translation']->id)."'");
			
			$id = $item_id;
			
		} else {
		
			$result = MySQL::query("INSERT INTO seo SET category = $category, item_id = $item_id, slug = '".$data['slug']."', title = '".$data['title']."', keywords = '".$data['keywords']."', description = '".$data['description']."', language_id = '".($_SESSION['translation']->id)."'");

			$id = MySql::insert_id();
		}
		
		return $result ? $id : false;
	}

}