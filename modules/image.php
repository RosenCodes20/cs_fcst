<?php

class Image {

	function resize($file_name, $width, $height)
	{
		$result = false;
		
		$info = self::create_resized($file_name, $width, $height);
		
		if ($info['resized']) {
		
			$result = $info['resized'];
			
		} else if ($info) {

			$src_width = $info['imagesize'][0];
			$src_height = $info['imagesize'][1];
		
			$temp_image = imagecreatetruecolor($width, $height);
			
			if ($info['imagesize']['mime'] == 'image/jpeg') {
			
				$image = @ImageCreateFromJPEG($info['imagepath']);
				
			} else if ($info['imagesize']['mime'] == 'image/png') {
			
				$image = @ImageCreateFromPNG($info['imagepath']) ;
				
			} else if ($info['imagesize']['mime'] == 'image/gif') {
			
				$image = @ImageCreateFromGIF($info['imagepath']);
				
			} else {
			
				$image = false;
			}
			
			if ($image) {
			
				imagefill($temp_image, 0, 0, imagecolorallocate($temp_image, 255, 255, 255));

				if ($src_width - $src_height / $height * $width < 0) {		
					
					imagecopyresampled($temp_image, $image, 0, 0, 0, ( $src_height - $src_width / $width * $height ) / 2, $width, $width / $src_width * $src_height, $src_width, $src_height);
					
				} else {
				
					imagecopyresampled($temp_image, $image, 0, 0, ( $src_width - $src_height / $height * $width ) / 2, 0, $height / $src_height * $src_width, $height, $src_width, $src_height);
				}
				
				$created = @imagejpeg($temp_image, $info['resized_base_name'], 85) or $created = @imagepng($temp_image, $info['resized_base_name']) or $created = @imagegif($temp_image, $info['resized_base_name']) or $created = false;

				imagedestroy($image);

				if ($created)
					$result = $info['resized_file_name'];
			}
			
			imagedestroy($temp_image);
		}
		
		return $result ? $result : 'image_resize_error.jpg';
	}
	
	function envelope($file_name, $width, $height)
	{
		$result = false;
		
		$info = self::create_resized($file_name, $width, $height);
		
		if ($info['resized']) {
		
			$result = $info['resized'];
			
		} else if ($info) {

			$src_width = $info['imagesize'][0];
			$src_height = $info['imagesize'][1];
		
			$temp_image = imagecreatetruecolor($width, $height);
			
			$image = @ImageCreateFromJPEG($info['imagepath']) or $image = @ImageCreateFromPNG($info['imagepath']) or $image = @ImageCreateFromGIF($info['imagepath']) or $image = false;
			
			if ($image) {
				
				imagefill($temp_image, 0, 0, imagecolorallocate($temp_image, 255, 255, 255));

				$r = $src_width / $src_height;

				if ($width / $height > $r) {
					
					imagecopyresampled($temp_image, $image, ( $width - $height * $r ) / 2, 0, 0, 0, $height * $r, $height, $src_width, $src_height);
					
				} else {
				
					imagecopyresampled($temp_image, $image, 0, ( $height - $width / $r ) / 2, 0, 0, $width, $width / $r, $src_width, $src_height);
				}
				
				$created = @imagejpeg($temp_image, $info['resized_base_name'], 85) or $created = @imagepng($temp_image, $info['resized_base_name']) or $created = @imagegif($temp_image, $info['resized_base_name']) or $created = false;

				imagedestroy($image);

				if ($created)
					$result = $info['resized_file_name'];
			}
			
			imagedestroy($temp_image);
		}
		
		return $result ? $result : 'image_resize_error.jpg';
	}	

	function scale($file_name, $width, $height)
	{
		return $file_name;
		
		$result = false;
		
		$info = self::create_resized($file_name, $width, $height);
		
		if ($info['resized']) {
		
			$result = $info['resized'];
			
		} else if ($info) {
		
			$src_width = $info['imagesize'][0];
			$src_height = $info['imagesize'][1];
		
			$temp_image = imagecreatetruecolor($width, $height);

			if ($info['imagesize']['mime'] == 'image/jpeg') {
			
				$image = @ImageCreateFromJPEG($info['imagepath']);
				
			} else if ($info['imagesize']['mime'] == 'image/png') {
			
				$image = @ImageCreateFromPNG($info['imagepath']) ;
				
			} else if ($info['imagesize']['mime'] == 'image/gif') {
			
				$image = @ImageCreateFromGIF($info['imagepath']);
				
			} else {
			
				$image = false;
			}
			
			if ($image) {
			
				imagecopyresampled($temp_image, $image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

				$created = @imagejpeg($temp_image, $info['resized_base_name'], 85) or $created = @imagepng($temp_image, $info['resized_base_name']) or $created = @imagegif($temp_image, $info['resized_base_name']) or $created = false;

				imagedestroy($image);

				if ($created)
					$result = $info['resized_file_name'];
			}

			imagedestroy($temp_image);
		}
		
		return $result ? $result : 'image_resize_error.jpg';
	}

	function create_resized($file_name, $width, $height) 
	{
		$result = false;
		
		$info['path'] = substr(ltrim($file_name), 0, 1) == '/' ? $_SERVER['DOCUMENT_ROOT'] : false;		
		$info['pathinfo'] = pathinfo($file_name);
		$info['pathinfo']['dirname'] = str_replace('\\', '/', $info['pathinfo']['dirname']);
		
		$info['resized_file_name'] = str_replace("//", "/", ($info['pathinfo']['dirname'] != '.' ? $info['pathinfo']['dirname'].'/' : '').'_resized/'.$info['pathinfo']['filename'].'_'.$width.'x'.$height.".".$info['pathinfo']['extension']);
		
		if (is_file($info['path'].$info['resized_file_name'])) {
		
			$result['resized'] = $info['resized_file_name'];
	
		} else if (is_file($info['path'].$file_name)) {
		
			$result = $info;
			$result['imagesize'] = getimagesize($info['path'].$file_name);
			$result['imagepath'] = $info['path'].$info['pathinfo']['dirname']."/".$info['pathinfo']['basename'];
			$result['resized_base_name'] = ($info['path'] ? $info['path']."/" : "").$info['resized_file_name'];
			
			if ($result['imagesize'][0] == $width && $result['imagesize'][1] == $height) {
			
				$result['resized'] = $file_name;
				
			} else {
				
				@mkdir($info['path'].$result['pathinfo']['dirname'].'/_resized/', 0777);
			}
		}
		
		return $result;
	}

}

?>