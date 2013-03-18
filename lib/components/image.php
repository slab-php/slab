<?php

/*
Example:
$this->Image = Dispatcher::load_component('image');
$img = $this->Image->get_posted_image($this->data['Foo']['photo']);
$thumbnail = $this->Image->resize_image($img, THUMBNAIL_X, THUMBNAIL_Y);
$popup = $this->Image->resize_image($img, POPUP_X, POPUP_Y);
$this->data['Foo']['thumbnail'] = $this->Image->save_image_to_in_memory_jpeg($thumbnail);
$this->data['Foo']['popup'] = $this->Image->save_image_to_in_memory_jpeg($popup);
unset($this->data['Foo']['photo']);
*/

class ImageComponent extends Component {
	var $config = null;

	function __construct($config) {
		$this->config = $config;
		ini_set('memory_limit', '64M');
	}
	
	function init() {
	}

	// assuming JPEGs
	function load_image_from_file($filename) {
		return imagecreatefromjpeg($filename);
	}

	// Loads the data for a POSTed image into memory. $img is the uploaded file data, eg $_POST['image']
	function get_posted_image($postImg) {
		if (empty($postImg)) {
			throw new Exception('No data provided');
		}
		
		$fileSize = $postImg['size'];
		
		if (!is_uploaded_file($postImg['tmp_name'])) {
			throw new Exception('Invalid uploaded file location');
		}
		$mimeType = $postImg['type'];
		if ($mimeType != 'image/jpg' && $mimeType != 'image/jpeg' && $mimeType != 'image/pjpeg') {
			throw new Exception('Invalid uploaded file type, only JPEG images are accepted');
		}

		return imagecreatefromjpeg($postImg['tmp_name']);
	}
	
	// returns a resized image, using the source image (a resource, returned by get_posted_image() for example)
	function resize_image($source, $maxWidth, $maxHeight) {
		$width = imagesx($source);
		$height = imagesy($source);
		
		$newWidth = $width;
		$newHeight = $height;
		
		if ($width > $height) {
			// scale back the width
			$newWidth = $maxWidth;
			$newHeight = ($maxWidth / $width) * $height;
		} else {
			// scale back the height
			$newHeight = $maxHeight;
			$newWidth = ($maxHeight / $height) * $width;
		}
		
		$dest = $source;
		if ($newWidth != $width || $newHeight != $height) {
			$dest = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresized($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		}
		
		return $dest;
	}
	
	function resize_image_horizontal($source, $newWidth) {
		$width = imagesx($source);
		$height = imagesy($source);
		
		$newHeight = ($newWidth / $width) * $height;		
		
		$dest = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresized($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		
		return $dest;
	}
	
	function resize_with_background($source, $width, $height, $hexRGB) {
		list($r, $g, $b) = hex2rgb($hexRGB);
	
		$srcWidth = imagesx($source);
		$srcHeight = imagesy($source);
		$newWidth = $width;
		$newHeight = $height;
		
		if ($srcWidth > $srcHeight) {
			$newHeight = ($newWidth / $srcWidth) * $srcHeight;
		} else {
			$newWidth = ($newHeight / $srcHeight) * $srcWidth;
		}
		
		/*
		pr(array(
			'width' => $width,
			'height' => $height,
			'srcWidth' => $srcWidth,
			'srcHeight' => $srcHeight,
			'newWidth' => $newWidth,
			'newHeight' => $newHeight
		));die();
		*/
		
		$dest = imagecreatetruecolor($width, $height);
		$color = imagecolorallocate($dest, $r, $g, $b);
		
		imagefill($dest, 0, 0, $color);
		imagecopyresized(
			$dest, 
			$source,
			($width - $newWidth) / 2,	// dst_x
			($height - $newHeight) / 2,	// dst_y
			0,	// src_x
			0,	// src_y
			$newWidth,	// dst_w
			$newHeight,	//dst_h
			$srcWidth,	// src_w
			$srcHeight	// src_h
			);
		
		return $dest;
	}
	
	function save_image_to_in_memory_jpeg($img) {
		ob_start();
		imagejpeg($img);
		$jpeg = ob_get_contents();
		ob_clean();
		
		return $jpeg;
	}
}

?>