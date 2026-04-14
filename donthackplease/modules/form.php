<?php

class Form
{
	static $regexp = Array(
		"not-empty"	=> "/^.{1,}$/",
		"email" 		=> "/^[a-zA-Z0-9]+([_\.\-]+|[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+|\-[a-zA-Z0-9]+)*\.[a-zA-Z]{2,}$/",
		//"username" 	=> "/^\w{3,10}$/",
		//"password"	=> "/^.{4,20}$/",
		//"firstname"	=> "/^[a-zA-Z]{0,30}$/",
		//"lastname"	=> "/^[a-zA-Z]{0,30}$/",
		//"other"		=> "/^[\w- !@.:;\?,]{0,100}$/",
	);

	function data( $fields = array(), &$errors = false )
	{
		$result = get_post_data();
		
		foreach ($fields as $field_key => $field_value)
			foreach ($field_value as $key => $value)
				if ( (self::$regexp[$key] && !preg_match(self::$regexp[$key], $result[$field_key]))
				|| (is_int($key) && mb_strlen($result[$field_key], 'utf8') > $key) )
					$errors[$field_key][] = $value;
		
		return $result;
	}
	
	function errors( $errors )
	{
		$result = array();
		
		foreach ($errors as $error) {
		
			if (!is_array($error) && !is_object($error))
				$error = array( $error );
			
			foreach ($error as $key => $value)
				$result[] = $value;
		}
		
		return $result;
	}
}

?>