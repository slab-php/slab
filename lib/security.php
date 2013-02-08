<?php
/*
Public functions:
init()			used in bootstrap.php to configure
md5($data)		wrapper for md5 for consistency
hash($data)		(preferred) one-way hash using sha1, mhash or md5 depending on availability
encrypt($data)	wrapper for encode()
encode($data)	encrypts using the security.encryption_key
decrypt($data)	wrapper for decode()
decode($data)	decrypts using the security.encryption_key
*/

class Security extends Object {
	var $__encryptionKey = null;	// the encryption key (md5 of the security.encryption_key configuration setting)
	var $__mcryptExists = false;
	var $__mcryptCipher = 'MCRYPT_RIJNDAEL_256';
	var $__mcryptMode = 'MCRYPT_MODE_ECB';
	var $__sha1Exists = false;
	var $__mhashExists = false;
	var $config = null;
	
	function __construct($config) {
		$this->config = config;
		$this->__encryptionKey = md5($this->config->get('security.encryption_key'));
		$this->__mcryptExists = function_exists('mcrypt_encrypt');
		$this->__sha1Exists = function_exists('sha1');
		$this->__mhashExists = function_exists('mhash');
	}	
	
		
	// Wrapper for md5 for consistency and backward compat
	function md5($data) {
		return md5($data);
	}

	function hash($data) {
		if ($this->__sha1Exists) return sha1($data);
		if ($$this->__mhashExists) return bin2hex(mhash(MHASH_SHA1, $data));
		return md5($data);
	}
	
	// Encodes some data using the encryption key
	// This is based on CodeIgniter's CI_Encrypt::encode()
	function encrypt($data) { return $this->encode($data); }
	function encode($data) {
		// xor and encode the data and the encryption key
		$result = $this->__xorEncode($data, $_Security__encryptionKey);

		// if configured and supported, mcrypt encode the data as well (using Rijndael256)
		if ($this->config->get('security.use_mcrypt') && $this->__mcryptExists) {
			$result = $this->__mcryptEncode($result, $this->__encryptionKey);
		}
		
		return base64_encode($result);
	}
	
	// Decodes the result of encode() (given the same encryption key) into the original string
	function decrypt($data) { return $this->decode($data); }
	function decode($data) {
		// check for invalid chars in the data
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $data)) {
			return false;
		}

		$result = base64_decode($data);

		if ($this->config->get('security.use_mcrypt') && $this->__mcryptExists) {
			$result = $this->__mcryptDecode($result, $this->__encryptionKey);
			if ($result === false) return false;
		}

		$result = $this->__xorDecode($result, $this->__encryptionKey);
	
		return $result;
	}

	// Encrypt some data using mcrypt
	// This is based on CodeIgniter's CI_Encrypt::mcrypt_encode()
	function __mcryptEncode($data, $key) {
		//mcrypt_module_open($_Security__mcryptCipher, '', $_Security__mcryptMode, '');
		// TODO: mcrypt is giving errors re not warning, I've disabled it for now
		$initSize = mcrypt_get_iv_size($this->__mcryptCipher, $this->__mcryptMode);
		$initVect = mcrypt_create_iv($initSize, MCRYPT_RAND);
		return $this->__addCipherNoise(
			$initVect . mcrypt_encrypt($this->__mcryptCipher, $key, $data, $this->__mcryptMode, $initVect), 
			$key);
	}
	
	// Decrypt some data using mcrypt
	// This is based on CodeIgniter's CI_Encrypt::mcrypt_decode()
	function __mcryptDecode($data, $key) {
		$data = $this->__removeCipherNoise($data, $key);
		$initSize = mcrypt_get_iv_size($this->__mcryptCipher, $this->__mcryptMode);

		if ($initSize > strlen($data)) {
			return false;
		}

		$initVect = substr($data, 0, $initSize);
		$data = substr($data, $initSize);
		return rtrim(mcrypt_decrypt($this->__mcryptCipher, $key, $data, $this->__mcryptMode, $initVect), "\0");
	}
	
	
	// Based on CodeIgniter's CI_Encrypt::_xor_encode()
	function __xorEncode($data, $key) {
		// Generate a random hash
		$rand = '';
		while (strlen($rand) < 32) {
			$rand .= mt_rand(0, mt_getrandmax());
		}
		$rand = $this->hash($rand);

		// encode the data
		$result = '';
		for ($i = 0; $i < strlen($data); $i++) {			
			$result .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($data, $i, 1));
		}

		return $this->__xorMerge($result, $key);
	}

	// Based on CodeIgniter's CI_Encrypt::_xor_decode()
	function __xorDecode($data, $key) {
		$data = $this->__xorMerge($data, $key);

		$result = '';
		for ($i = 0; $i < strlen($data); $i++) {
			$result .= (substr($data, $i++, 1) ^ substr($data, $i, 1));
		}

		return $result;
	}

	// Merge a string with a key using XOR
	// Based on CodeIgniter's CI_Encrypt::_xor_merge()
	function __xorMerge($data, $key) {
		$hash = $this->hash($key);
		$result = '';
		for ($i = 0; $i < strlen($data); $i++) {
			$result .= substr($data, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
		}

		return $result;
	}

	// Based on CodeIgniter's CI_Encrypt::_add_cypher_noise():
	// Adds permuted noise to the IV + encrypted data to protect against Man-in-the-middle attacks on CBC mode ciphers
	// http://www.ciphersbyritter.com/GLOSSARY.HTM#IV
	function __addCipherNoise($data, $key) {
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';

		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
			if ($j >= $keylen) {
				$j = 0;
			}

			$str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
		}

		return $str;
	}

	// Based on CodeIgniter's CI_Encrypt::_add_cipher_noise():
	// Removes permuted noise from the IV + encrypted data, reversing _add_cipher_noise()
	function __removeCipherNoise($data, $key) {
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';

		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
			if ($j >= $keylen) {
				$j = 0;
			}

			$temp = ord($data[$i]) - ord($keyhash[$j]);

			if ($temp < 0) {
				$temp = $temp + 256;
			}
			
			$str .= chr($temp);
		}

		return $str;
	}
}

?>