<?php
session_start();
include_once("tools/util.php");
if (!check_login()) {
	echo "error";
	die();
}
include_once ('tools/htpasswd.php');
$ini = read_config();

$htpasswd = new htpasswd ( $ini ['secure_path'] . ".htpasswd" );

if (isset ( $_POST['user'] )) {
	$user = $_POST['user'];
	if ($htpasswd->user_delete($user)) {
		echo "success";
	} else {
		echo "error";
	}
	
} else {
	echo "error";
}
?>