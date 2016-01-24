<?php
function check_login() {
	if (isset($_SESSION['login']) && $_SESSION ['login'] === true) {
		return true;
	}
	return false;
}

function read_config() {
	return parse_ini_file('config/config.ini');
}

function check_password_quality($pwd) {
	if (!isset($pwd)||strlen($pwd)<4) {
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

?>