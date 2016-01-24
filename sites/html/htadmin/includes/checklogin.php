<?php
session_start();
include_once("tools/util.php");
if (!check_login()) {
	header('LOCATION:login.php');
	die();
}
?>