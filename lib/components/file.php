<?php

class FileComponent extends Component {
	var $config = null;

	function __construct($config) {
		$this->config = $config;
	}

	function exists($filename) {
		return file_exists($filename);
	}

	function is_posted_file($postedFile) {
		if (empty($postedFile)) return false;
		if (!is_array($postedFile)) return false;
		if (empty($postedFile['tmp_name'])) return false;
		return is_uploaded_file($postedFile['tmp_name']);
	}

	function load_posted_file($postedFile) { return $this->read_posted_file($postedFile); }
	function read_posted_file($postedFile) {
		if (empty($postedFile)) {
			throw new Exception('No data provided');
		}
		
		if (is_array($postedFile)) {
			$postedFile = $postedFile['tmp_name'];
		}
		
		if (!is_uploaded_file($postedFile)) {
			throw new Exception('Invalid uploaded file location');
		}
		
		return $this->read($postedFile);
	}

	function store_file($sourcePath, $destinationKey) {
		return $this->copy($sourcePath, $this->__get_stored_file_path($destinationKey));
	}

	function read_stored_file($destinationKey) {
		return $this->read($this->__get_stored_file_path($destinationKey));
	}

	function __get_stored_file_path($destinationKey) {
		return SLAB_APP.'/stored_files/'.sha1($destinationKey);
	}

	// Reads the file
	function read($filename, $mode = 'rb') {
		if (!$this->exists($filename)) {
			throw new Exception('File does not exist');
		}
		
		$handle = @fopen($filename, $mode);
		if ($handle === FALSE) throw new Exception('File could not be opened. Please try again.');
		
		$size = filesize($filename);
		if ($size == 0) throw new Exception('File has zero length. Please try again.');
		
		$data = @fread($handle, $size);
		if ($data === FALSE) throw new Exception('File could not be read. Please try again.');
		
		fclose($handle);
		
		return $data;
	}

	function read_text($filename, $mode = 'rb') {
		return $this->read($filename, $mode);
	}
	
	// write a string to a file as text
	function write($filename, $data, $mode = 'wb') {
		$f = fopen($filename, $mode);
		if (!$f) {
			throw new Exception('Could not open file for writing');;
		}
		
		flock($f, LOCK_EX);
		fwrite($f, $data);
		flock($f, LOCK_UN);
		fclose($f);
	}

	function write_text($filename, $data, $mode = 'wb') {
		$this->write($filename, $data, $mode);
	}
	
	// Writes a serialized object to a file. If the Security component is available, encrypts the serialized object first. Read the object
	// with FileComponent::readObject()
	function write_object($filename, $data, $mode = 'wb', $useEncryption = true) {
		$data = serialize($data);
		if ($useEncryption && !empty($this->controller->Security)) {
			$data = $this->controller->Security->encrypt($data);
		}
		$this->write($filename, $data, $mode);
	}
	
	// Reads a serialized object from a file. If the Security component is available, decrypts the serialized data before unserializing. Write the object
	// with FileComponent::writeObject()
	function read_object($filename, $useEncryption = true) {
		$data = $this->read($filename);
		if ($useEncryption && !empty($this->controller->Security)) {
			$data = $this->controller->Security->decrypt($data);
		}
		$data = unserialize($data);
		return $data;
	}
	
	function remove($filename) {
		return unlink($filename);
	}

	function delete($filename) {
		return unlink($filename);
	}

	function rename($source, $dest) {
		return rename($source, $dest);
	}

	function copy($source, $dest) {
		return copy($source, $dest);
	}
	
	function dir($path, $filesOnly = false) {
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