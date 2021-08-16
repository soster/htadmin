<?php
session_start();
include_once("tools/util.php");
if (!check_login()) {
	echo "unauthorized";
	die();
}
include_once ('tools/htpasswd.php');
$ini = read_config();
$use_metadata = $ini ['use_metadata'];

$htpasswd = new htpasswd ( $ini ['secure_path'], $use_metadata );

if (isset ( $_POST['user'] )) {
	$user = $_POST['user'];
	if ($htpasswd->user_delete($user)) {
		if ($use_metadata) {
			$htpasswd->meta_delete($user);
		}
		echo "success";
	} else {
		echo "error";
	}
	
} else {
	echo "error";
}
?>