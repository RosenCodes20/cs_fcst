<?php

class External_link
{
	function get()
	{
		return MySQL::table("SELECT * FROM external_links p WHERE p.active = 'Yes' AND language_id = '".($_SESSION['language']->id)."' ORDER BY p.position");
	}
}