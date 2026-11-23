<?php
include_once ('tools/util.php');
require_config ();
if (!isset($ini)) {
	$ini = read_config ();
}
$title = htmlspecialchars($ini['app_title'] ?? 'HTAdmin', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Compiled and minified CSS (self-hosted) -->
<link rel="stylesheet" href="styles/bootstrap.min.css">
<link rel="stylesheet" href="styles/bootstrap-theme.min.css">
<script src="script/jquery-3.8.0.min.js"></script>
<script src="script/bootstrap.min.js"></script>
<script src="script/script.js"></script>
<link rel="stylesheet" href="styles/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title; ?></title>
</head>
<body>
