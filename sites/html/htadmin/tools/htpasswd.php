<?php

/**
 * htpasswd tools for Apache Basic Auth. 
 * Uses crypt only!
  *
 */
class htpasswd {
	var $fp;
	var $filename;
	
	const HTPASSWD_NAME = ".htpasswd";
	const HTACCESS_NAME = ".htaccess";

	
	function htpasswd($configpath) {
		$path = realpath($configpath);
		$htaccessfile = $path . "/" . self::HTACCESS_NAME;
		$htpasswdfile = $path . "/" . self::HTPASSWD_NAME;
		
		if (!file_exists($htaccessfile)) {
			$bdfp = fopen($htaccessfile, 'w');
			$htaccess_content = "AuthType Basic\nAuthName \"Password Protected Area\"\nAuthUserFile \"" . $htpasswdfile . "\"\nRequire valid-user";
			fwrite($bdfp,$htaccess_content);
		}

		if (!file_exists($htpasswdfile)) {
			@$this->fp = fopen ( $htpasswdfile, 'w+' );
		} else {
			@$this->fp = fopen ( $htpasswdfile, 'r+' ) or die ( 'Invalid file name' );
		}
		

		
		
		$this->filename = $htpasswdfile;
	}
	function user_exists($username) {
		rewind ( $this->fp );
		while ( ! feof ( $this->fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) ) ) {
			if ($lusername == $username)
				return 1;
		}
		return 0;
	}
	
	function get_users() {
		rewind ( $this->fp );
		$users = array();
		$i = 0;
		while ( ! feof ( $this->fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) ) ) {
			$users[$i] = $lusername;
			$i++;
		}
		return $users;
	}
	
	function user_add($username, $password) {		
		if ($this->user_exists ( $username ))
			return false;
		fseek ( $this->fp, 0, SEEK_END );
		fwrite ( $this->fp, $username . ':' . self::htcrypt($password) . "\n" );
		return true;
	}
	
	/**
	 * Login check
	 * first 2 characters of hash is the salt.
	 * @param user $username
	 * @param pass $password
	 * @return boolean true if password is correct!
	 */
	function user_check($username, $password) {
		rewind ( $this->fp );
		while ( ! feof ( $this->fp ) && $userpass = explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) {
			$lusername = trim($userpass[0]);
			$hash = $userpass[1];

			if ($lusername == $username) {
				return (self::check_password_hash($password, $hash));
			}
		}
		return false;
	}
	
	static function check_password_hash($password, $hash) {
		$salt = substr($hash,0,2);
		if (crypt($password,$salt)==$hash) {
			return true;
		} else {
			return false;
		}
	}
	
	static function htcrypt($password) {
		return crypt ( $password, substr ( str_replace ( '+', '.', base64_encode ( pack ( 'N4', mt_rand (), mt_rand (), mt_rand (), mt_rand () ) ) ), 0, 22 ) );
	}
	
	
	function user_delete($username) {
		$data = '';
		rewind ( $this->fp );
		while ( ! feof ( $this->fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) ) ) {
			if (! trim ( $line ))
				break;
			if ($lusername != $username)
				$data .= $line . "\n";
		}
		$this->fp = fopen ( $this->filename, 'w' );
		fwrite ( $this->fp, rtrim ( $data ) . (trim ( $data ) ? "\n" : '') );
		fclose ( $this->fp );
		$this->fp = fopen ( $this->filename, 'r+' );
		return true;
	}
	
	function user_update($username, $password) {
		rewind ( $this->fp );
		while ( ! feof ( $this->fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) ) ) {
			if ($lusername == $username) {
				fseek ( $this->fp, (- 15 - strlen ( $username )), SEEK_CUR );
				fwrite ( $this->fp, $username . ':' . self::htcrypt($password) . "\n" );
				return true;
			}
		}
		return false;
	}
}
?>