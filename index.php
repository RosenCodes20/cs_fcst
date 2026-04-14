<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

include("system/_layout.php");
switch ( $_SEO->key )
{
	case 'page':
	
		switch ( $_SEO->item_id )
		{
			case '1':
			
				$rewrite_file = "home.php";
			break;
			case '4':
			
				$rewrite_file = "contacts.php";
			break;
			case '5':
			
				$rewrite_file = "sitemap.php";
			break;
			case '6':
			
				if ($params[1]) {
				
					$_SEO = Seo::get_by_slug( $params[1] );
			
					$_GET['id'] = $_SEO->item_id;
					
					$rewrite_file = "events.php";
					
					$expected_params = 2;
				
				} else {
			
					$rewrite_file = "events-list.php";
				}
			break;
			case '1':
			
				$rewrite_file = "terms.php";
			break;
			default:
			
				$_GET['id'] = $_SEO->item_id;
			
				$rewrite_file = "page.php";
			break;
		}
	break;
	case 'events':
	
		if ($params[0]) {
		
			$_SEO = Seo::get_by_slug( $params[0] );
	
			$_GET['id'] = $_SEO->item_id;
			
			$rewrite_file = "events.php";
			
			$expected_params = 1;
		
		} else {
	
			$rewrite_file = "404.php";
		}
	break;
	default:
	{
		// $rewrite_file = "404.php";
		$rewrite_file = "home.php";
	}
}

if (count($params) > $expected_params)
	$rewrite_file = "404.php";

include ($rewrite_file);


?>
