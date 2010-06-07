<?php
class AppController extends Controller {
	var $models = array(
		'User' => 'users'
	);
	
	function beforeFilter() {
		$this->set('loggedInUser', $this->Session->check('user'));
	}
	
	
	
	// checks if a user is logged in. If not, redirects to the login page
	function __checkUser() { 
		if (!$this->Session->check('user')) {
			$this->redirect('/admin/login');
		}
	}
	
	
	
	
	/************* Helper functions for working with files and images ********/
	/* 
	Loads an uploaded image and resizes it to fit in the supplied
	dimensions. If the dimensions aren't supplied, 60x60 is used.
	*/
	function __loadAndResizeImage($img, $newWidth = null, $newHeight = null) {
		if (empty($img['tmp_name'])) {
			return array('img' => null, 'error' => null);
		}
		
		// check tmp_name is valid
		if (!is_uploaded_file($img['tmp_name'])) {
			return array('img' => null, 'error' => 'The uploaded filename is invalid');
		}
		
		// check uploaded file is a JPEG
		if ($img['type'] != 'image/jpeg' && $img['type'] != 'image/jpg' && $img['type'] != 'image/pjpeg') {
			return array('img'=>null, 'error' => 'The uploaded file is not a JPEG');
		}
		
		$filename = $img['tmp_name'];
		
		// jack up the memory available to the script
		ini_set('memory_limit', '64M');
		
		list($width, $height) = getimagesize($filename);
		
		if ($width >= $height || !empty($newWidth)) {
			if (empty($newWidth)) {
				$newWidth = 60;
			}
			$newHeight = ($newWidth / $width) * $height;
		} else {
			if (empty($newHeight)) {
				$newHeight = 60;
			}
			$newWidth = ($newHeight / $height) * $width;
		}
		
		// create a dest image
		$dest = imagecreatetruecolor($newWidth, $newHeight);
		
		// load the source image
		$source = imagecreatefromjpeg($filename);
		
		// resize if required
		if ($width != $newWidth)
			imagecopyresized($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		else
			$dest = $source;
		
		// use output buffering to capture the JPEG output
		ob_start();
		imagejpeg($dest);
		$data = ob_get_contents();
		ob_clean();
		
		return array('img'=>$data, 'error'=>null);
	}	

	/* Load an uploaded file into memory (for example, load an uploaded PDF into memory
		for saving to the database) */
	function __loadFile($f) {
		if (empty($f['tmp_name'])) {
			return array('file' => null, 'error' => null);
		}
		
		// check tmp_name is valid
		if (!is_uploaded_file($f['tmp_name'])) {
			return array('file' => null, 'error' => 'The uploaded filename is invalid');
		}
		
		// read the file into memory
		$data = fread(fopen($f['tmp_name'], 'r'), $f['size']);
		
		return array('file' => $data, 'error' => null);
	}
	
	/* Load an uploaded image into memory (compressed, ready for insertion into the
	database (doesn't resize the image) */
	function __loadImage($img) {
		if (empty($img['tmp_name'])) {
			return array('img' => null, 'error' => null);
		}

		// check tmp_name is valid
		if (!is_uploaded_file($img['tmp_name'])) {
			return array('img' => null, 'error' => 'The uploaded filename is invalid');
		}
		// check uploaded file is a JPEG
		if ($img['type'] != 'image/jpeg' && $img['type'] != 'image/jpg' && $img['type'] != 'image/pjpeg') {
			return array('img'=>null, 'error'=>'The uploaded file is not a JPEG');
		}
		
		// read the file into memory
		$data = file_get_contents($img['tmp_name']);
		
		return array('img'=>$data, 'error'=>null);
	}	
}

?>