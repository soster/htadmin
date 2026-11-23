<?php
function check_login() {
	if (isset($_SESSION['login']) && $_SESSION ['login'] === true) {
		return true;
	}
	return false;
}

function require_config() {
	if (file_exists ( 'config/config.ini' )) {
		return;
	}
	// Refuse to render with a broken config - this is a security-sensitive tool.
	echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>HTAdmin - Not configured</title></head><body>';
	echo '<h1>Not configured</h1><div class="alert"><p><strong>HTAdmin is not configured yet.</strong></p>';
	echo '<p>Create <code>config/config.ini</code> from <code>config/config.ini.example</code> (see the README) and reload this page.</p>';
	echo '</div></body></html>';
	exit;
}

function read_config() {
	if (! file_exists ( 'config/config.ini' )) {
		return array ();
	}
	$ini = @parse_ini_file ( 'config/config.ini' );
	return is_array ( $ini ) ? $ini : array ();
}

function check_password_quality($pwd) {
	if (!isset($pwd)||strlen($pwd)<8) {
		return false;
	}
	return true;
}

function check_username($username) {
	if (!isset($username)||strlen($username)>20 || strlen($username)<3) {
		return false;
	}
	return preg_match('/^[a-zA-Z0-9]+$/', $username);

}

// default password / mailkey length:
const PASSWORD_LENGTH = 18;

function random_password($length) {
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890';
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < $length; $i++) {
		$n = random_int(0, $alphaLength); // cryptographically secure random index
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}

function username_from_line($fp) {
	$usernames = explode ( ":", $line = rtrim ( fgets ( $fp ) ) );
	trim ( $lusername = array_shift ( $usernames ) );
	return $lusername;
}

?>