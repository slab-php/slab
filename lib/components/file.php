<?php
/* /lib/components/file.php
** FileComponent, encapsulates some methods for working with disk files
** Contains some inspiration from CodeIgniter
** BJS20090406
** (CC A-SA) 2009 Belfry Images [http://www.belfryimages.com.au | ben@belfryimages.com.au]
** Changes:
*/

class FileComponent extends Component {
	// Fields
	
	
	function init() {
	}		
	function beforeAction() {
	}	
	function afterAction() {
	}
	function shutdown() {
	}
	
	
	// wrapper for file_exists()
	function exists($filename) {
		return file_exists($filename);
	}

	// Reads the file as text to a string. Returns false if the file doesn't exist or there is an error reading the file
	function readText($filename) {
		if (!$this->exists($filename)) {
			return false;
		}
		
		return file_get_contents($filename);
	}
	
	// Reads the file as binary. Returns false if the file doesn't exist or there is an error reading the file
	function readBinary($filename) {
		if (!$this->exists($filename)) {
			return false;
		}
		
		$f = fopen($filename, 'rb');
		if (!$f) {
			return false;
		}
		
		$data = fread($f, filesize($filename));
		fclose($f);
		
		return $data;
	}
	
	// write a string to a file as text. Returns false or true to indicate success
	function writeText($filename, $data, $mode = 'wb') {
		$f = fopen($filename, $mode);
		if (!$f) {
			return false;
		}
		
		flock($f, LOCK_EX);
		fwrite($f, $data);
		flock($f, LOCK_UN);
		fclose($f);

		return true;
	}
	
	// Writes a serialized object to a file. If the Security component is available, encrypts the serialized object first. Read the object
	// with FileComponent::readObject()
	function writeObject($filename, $data, $mode = 'wb', $useEncryption = true) {
		$data = serialize($data);
		if ($useEncryption && !empty($this->controller->Security)) {
			$data = $this->controller->Security->encrypt($data);
		}
		return $this->writeText($filename, $data, $mode);
	}
	
	// Reads a serialized object from a file. If the Security component is available, decrypts the serialized data before unserializing. Write the object
	// with FileComponent::writeObject()
	function readObject($filename, $useEncryption = true) {
		$data = $this->readText($filename);
		if ($useEncryption && !empty($this->controller->Security)) {
			$data = $this->controller->Security->decrypt($data);
		}
		$data = unserialize($data);
		return $data;
	}
	
	// Wrapper for unlink()
	function remove($filename) {
		return $this->delete($filename);
	}
	function delete($filename) {
		return unlink($filename);
	}
	
	// get a directory listing of the path. This includes any subdirectories and returns the full path to each entry
	function dir($path, $filesOnly = false) {
		// resolves the path to a canonicalized absolute path and make sure it ends with a directory separator
		$path = rtrim(realpath($path), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		
		$filenames = array_diff(scandir($path), array('.', '..'));
		
		foreach ($filenames as $k=>$v) {
			$filenames[$k] = $path.$v;
		}
		
		if ($filesOnly) {
			$strippedFilenames = array();
			foreach ($filenames as $fn) {
				if (!is_dir($fn)) {
					$strippedFilenames[] = $fn;
				}
			}
			$filenames = $strippedFilenames;
		}
		
		return $filenames;
	}
};

?>