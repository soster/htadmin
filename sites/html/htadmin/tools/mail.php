<?php
require 'phpmailer/PHPMailerAutoload.php';
include_once ('tools/util.php');


function send_forgotten_mail($email, $username, $link) {
	if (!isset($ini)) {
		$ini = read_config ();
	}
	$mail = new PHPMailer ();
	
	$mail->isSMTP (); // Set mailer to use SMTP
	
	$mail->Host = $ini ["mail_server"]; // Specify main and backup SMTP servers
	$mail->SMTPAuth = true; // Enable SMTP authentication
	$mail->Username = $ini ["mail_user"]; // SMTP username
	$mail->Password = $ini ["mail_pwd"]; // SMTP password
	//$mail->SMTPSecure = 'ssl'; // Enable encryption, 'ssl' also accepted
	//$mail->Port = 465;
	
	$mail->From = $ini ["mail_from"];
	$mail->FromName = 'Mailer';
	$mail->addAddress ( $email ); // Add a recipient
	
	$mail->Subject = 'Reset password';
	$mail->Body = 'You can reset your password with this link: ' . $link;
	//$mail->SMTPDebug = 2;
	
	if (! $mail->send ()) {
		return false;
	} else {
		return true;
	}
}

?>