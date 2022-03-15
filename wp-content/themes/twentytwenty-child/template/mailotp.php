<?php
require_once("../../../../wp-config.php");
require_once("../../../../wp-load.php");

	$rndno=random_int(100000, 999999);//OTP generate
	echo $rndno;
	$subject = "OTP";
	$txt = "OTP : $rndno";
	$to = $_POST['email'];
	$headers = array('Content-Type: text/html; charset=UTF-8','From: kavita <kavita@plutustec.com>');
	wp_mail( $to, $subject, $txt, $headers );
		
?>