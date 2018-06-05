<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once "Mail.php";
  
	$pass = $_POST['pass'];
	$hour = $_POST['hour'];
	$service = $_POST['service'];
	$date = $_POST['date'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$detail = $_POST['detail'];
	
	$host = "host.pcsaved.com";
	$username = "email@pcsaved.com";
	$password = "Bluffton1!";
	$headers = array ('From' => $email,
	'To' => "majditahaps@gmail.com",
	'Subject' => $name);
	$message="Number of Passengers:".$pass."\n"."Hours of service:".$hour."\n"."Type of service:".$service."\n"."Date:".$date."\n"."Phone:".$phone."\n"."Email:".$email."\n"."Detail:".$detail."\n";
	echo ($message);
	$smtp = Mail::factory('smtp',
	array ('host' => $host,
		'auth' => true,
		'username' => $username,
		'password' => $password));
//var_dump($smtp);
	$mail = $smtp->send("majditahaps@gmail.com", $headers, $message);

	if (PEAR::isError($mail)) {
	echo("<p>" . $mail->getMessage() . "</p>");
	} else {
	echo("Message successfully sent!");
	}
?> 