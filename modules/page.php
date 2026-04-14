<?php

class Page
{
	function get($id)
	{
		return MySQL::get("SELECT * FROM pages p, `pages_translations` t WHERE t.page_id = p.id AND t.language_id = '".($_SESSION['language']->id)."' AND p.id = '$id' AND t.active = 'Yes'");
	}
}