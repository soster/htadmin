<?php

interface i_password_hash_tool
{
	public function check_password_hash($password, $hash);
	public function crypt($password);
}

class md5_hash_tool implements i_password_hash_tool
{

	public function check_password_hash($password, $hash)
	{
		$passParts = explode('$', $hash);
		$salt = $passParts[2];
		$hashed = $this->crypt_apr_md5($password, $salt);
		return $hashed == $hash;
	}

	public function crypt($password)
	{

	}

	protected function crypt_apr_md5($password, $salt)
	{
		$len = strlen($password);
		$text = $password . '$apr1$' . $salt;
		$bin = pack("H32", md5($password . $salt . $password));
		for ($i = $len; $i > 0; $i -= 16) {
			$text .= substr($bin, 0, min(16, $i));
		}
		for ($i = $len; $i > 0; $i >>= 1) {
			$text .= ($i & 1) ? chr(0) : $password [0];
		}
		$bin = pack("H32", md5($text));
		for ($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $password : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $password;
			$new .= ($i & 1) ? $bin : $password;
			$bin = pack("H32", md5($new));
		}
		$tmp = '';
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
		}
		$tmp = chr(0) . chr(0) . $bin[11] . $tmp;
		$tmp = strtr(
			strrev(substr(base64_encode($tmp), 2)),
			"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
			"./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
		);
		return "$" . "apr1" . "$" . $salt . "$" . $tmp;
	}



}

class password_hash_tool implements i_password_hash_tool
{
	/**
	 * Modern password_hash() formats — e.g. bcrypt ($2y$), which works with
	 * both PHP and nginx/Apache basic auth.
	 */
	public function check_password_hash($password, $hash)
	{
		return password_verify($password, $hash);
	}

	public function crypt($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}
}

class crypt_hash_tool implements i_password_hash_tool
{
	/**
	 * Legacy DES-style (13 character) hashes produced by earlier versions of
	 * the app. Kept so existing files can still be checked. Never generate
	 * new hashes with this tool.
	 */
	public function check_password_hash($password, $hash)
	{
		$salt = substr($hash, 0, 2);
		if (crypt($password, $salt) == $hash) {
			return true;
		} else {
			return false;
		}
	}

	public function crypt($password)
	{
		// never use the legacy DES scheme for new hashes
		return password_hash($password, PASSWORD_BCRYPT);
	}
}


?>