<?php
include_once ("model/meta_model.php");
include_once ("hash_tool.php");
/**
 * htpasswd tools for Apache Basic Auth.
 *
 * Uses crypt only!
 */
class htpasswd {
	var $fp;
	var $metafp;
	var $filename;
	var $metafilename;
	var $use_metadata;
	
	/* All ht-files. These files are stored within the secured folder. */
	const HTPASSWD_NAME = ".htpasswd";
	const HTACCESS_NAME = ".htaccess";
	const HTMETA_NAME = ".htmeta";
	function __construct($configpath, $use_metadata = false) {
		$path = realpath ( $configpath );
		$htaccessfile = $path . "/" . self::HTACCESS_NAME;
		$htpasswdfile = $path . "/" . self::HTPASSWD_NAME;
		@$this->use_metadata = $use_metadata;
		
		if (! file_exists ( $htaccessfile )) {
			$bdfp = fopen ( $htaccessfile, 'w' );
			$htaccess_content = "AuthType Basic\nAuthName \"Password Protected Area\"\nAuthUserFile \"" . $htpasswdfile . "\"\nRequire valid-user" . "\n<Files .ht*>\nOrder deny,allow\nDeny from all\n</Files>";
			if (!fwrite ( $bdfp, $htaccess_content )) {
				echo ("can not write to file " . $htaccessfile);
			}
		}


		@$this->fp = @$this::open_or_create ( $htpasswdfile );

		if ($this->fp == null) {
			echo ("can not read/write file " .$htpasswdfile);
		}
		
		if ($use_metadata) {
			$htmetafile = $path . "/" . self::HTMETA_NAME;
			@$this->metafp = @$this::open_or_create ( $htmetafile );
			$this->metafilename = $htmetafile;
		}
		
		$this->filename = $htpasswdfile;
	}
	function user_exists($username) {
		return self::exists ( @$this->fp, $username );
	}
	function meta_exists($username) {
		return self::exists ( @$this->metafp, $username );
	}
	function meta_find_user_for_mail($email) {
		while ( ! feof ( $this->metafp ) && $meta = explode ( ":", $line = rtrim ( fgets ( $this->metafp ) ) ) ) {			
			if (count ( $meta ) > 1) {
				$username = trim ( $meta [0] );
				$lemail = $meta [1];
				
				if ($lemail == $email) {
					return $username;
				}
			}
		}
		return null;
	}
	function get_metadata() {
		$meta_model_map = array ();
		$metaarr = array ();

		if ( $this->metafp == null) {
			return $meta_model_map;
		}

		rewind ( $this->metafp );

		while ( ! feof ( $this->metafp ) && $line = rtrim ( fgets ( $this->metafp ) ) ) {
			$metaarr = explode ( ":", $line );
			$model = new meta_model ();
			$model->user = $metaarr [0];
			if (count ( $metaarr ) > 1) {
				$model->email = $metaarr [1];
			}
			if (count ( $metaarr ) > 2) {
				$model->name = $metaarr [2];
			}
			if (count ( $metaarr ) > 3) {
				$model->mailkey = $metaarr [3];
			}
			
			$meta_model_map [$model->user] = $model;
		}
		return $meta_model_map;
	}
	function get_users() {
		$users = array ();
		if ( $this->fp == null) {
			return $users;
		}

		rewind ( $this->fp );

		$i = 0;
		while ( ! feof ( $this->fp )) {
			$usernames = explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) );
			trim ( $lusername = array_shift ( $usernames ) );
			$users [$i] = $lusername;
			$i ++;
		}
		return $users;
	}
	function user_add($username, $password) {
		if ($this->user_exists ( $username ))
			return false;
		if ($this->fp==null) {
			return false;
		}
		fseek ( $this->fp, 0, SEEK_END );
		fwrite ( $this->fp, $username . ':' . self::htcrypt ( $password ) . "\n" );
		return true;
	}
	function meta_add(meta_model $meta_model) {
		if ($this->metafp==null) {
			return false;
		}
		if (self::exists ( @$this->metafp, $meta_model->user )) {
			return false;
		}
		fseek ( $this->metafp, 0, SEEK_END );
		if (!fwrite ( $this->metafp, $meta_model->user . ':' . $meta_model->email . ':' . $meta_model->name . ':' . $meta_model->mailkey . "\n" )) {
			echo ("can not write to file for meta_add!");
		}
		return true;
	}
	
	/**
	 * Login check
	 * first 2 characters of hash is the salt.
	 *
	 * @param user $username        	
	 * @param pass $password        	
	 * @return boolean true if password is correct!
	 */
	function user_check($username, $password) {
		rewind ( $this->fp );
		while ( ! feof ( $this->fp ) && $userpass = explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) {
			$lusername = trim ( $userpass [0] );
			$hash = trim ($userpass [1] );
			
			if ($lusername == $username) {
				$validator = self::create_hash_tool($hash);
				return $validator->check_password_hash($password, $hash);
			}
		}
		return false;
	}
	
	function user_delete($username) {
		return self::delete ( @$this->fp, $username, @$this->filename );
	}
	
	function meta_delete($username) {
		return self::delete ( @$this->metafp, $username, @$this->metafilename );
	}
	function user_update($username, $password) {
		rewind ( $this->fp );
		while ( ! feof ( $this->fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $this->fp ) ) ) ) ) ) {
			if ($lusername == $username) {
				fseek ( $this->fp, (- 15 - strlen ( $username )), SEEK_CUR );
				fwrite ( $this->fp, $username . ':' . self::htcrypt ( $password ) . "\n" );
				return true;
			}
		}
		return false;
	}
	function meta_update(meta_model $meta_model) {
		$this->meta_delete ( $meta_model->user );
		$this->meta_add ( $meta_model );
		return false;
	}
	static function write_meta_line($fp, meta_model $meta_model) {
		fwrite ( $fp, $meta_model->user . ':' . $meta_model->email . ':' . $meta_model->name . "\n" );
	}
	static function exists($fp, $username) {
		if ($fp == null) {
			return false;
		}
		rewind ( $fp );
		while ( ! feof ( $fp )) {
			$usernames = explode ( ":", $line = rtrim ( fgets ( $fp ) ) );
			trim ( $lusername = array_shift ( $usernames ) );
			if ($lusername == $username)
				return true;
		}
		return false;
	}
	static function open_or_create($filename) {
		if (! file_exists ( $filename )) {
			return fopen ( $filename, 'w+' );
		} else {
			return fopen ( $filename, 'r+' );
		}
	}
	static function delete($fp, $username, $filename) {
		$data = '';
		rewind ( $fp );
		while ( ! feof ( $fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $fp ) ) ) ) ) ) {
			if (! trim ( $line ))
				break;
			if ($lusername != $username)
				$data .= $line . "\n";
		}
		$fp = fopen ( $filename, 'w' );
		fwrite ( $fp, rtrim ( $data ) . (trim ( $data ) ? "\n" : '') );
		fclose ( $fp );
		$fp = fopen ( $filename, 'r+' );
		return true;
	}
	static function htcrypt($password) {
		return crypt ( $password, substr ( str_replace ( '+', '.', base64_encode ( pack ( 'N4', mt_rand (), mt_rand (), mt_rand (), mt_rand () ) ) ), 0, 22 ) );
	}

	
	static function create_hash_tool($hash) {
		if (strpos($hash, '$apr1') === 0) {
			return new md5_hash_tool();
		} else {
			return new crypt_hash_tool();
		}
	}
		
}	
	

?>