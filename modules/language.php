<?php

class Language
{
	// Set active translation by language abbreviation. If $language is missling, load default translation
	
	function set($language = false, $language_id = false)
	{
		if ($language_id) {
		
			global $languages;
			
			foreach ($languages as $item)
				if ($item->id == $language_id)
					$language = $item->abbr;
		}
	
		// Check for no language changes
		
		if ($_SESSION['language'] && !$language)
			return $_SESSION['language'];
			
		if ($_SESSION['language'] && $language && $_SESSION['language']->abbr == $language)
			return $_SESSION['language'];
			
		global $settings;
		
		if ($language)
			$_SESSION['language'] = MySQL::get("SELECT * FROM `languages` WHERE `abbr` = '".$language."'");

		if (!$_SESSION['language'])
			$_SESSION['language'] = MySQL::get("SELECT * FROM `languages` WHERE `id` = '".($settings->default_language)."'");
		
		return $_SESSION['language'];
	}
	
	// Get language by id. If $id is missing get all languages
	
	function get($id = false)
	{
		if ($id)
			return MySQL::get("SELECT * FROM `languages` WHERE `id` = '".$id."'");
		else
			return MySQL::table("SELECT * FROM `languages`");
	}

}

?>