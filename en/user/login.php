<?php
	include ("../lib/conf/config.php");
 	include ("../lib/db/connect_db.php");
	   
	$log_email = trim($_POST["log_email"]);
	$log_password = trim($_POST["log_password"]);
	$log_remember = $_POST["log_remember"];
	$log_type = $_POST["log_type"];
	
	// Password encryption with salt
	$salt = mb_strtolower($log_email);
	$crypted = sha1($salt.$log_password);
	
	$query_user = "SELECT * FROM users_site WHERE email='$log_email' AND password='$crypted'";   //ADD AND activated != '2' or password != Null to avoid accounts created from Oauth
            
    $result_user = mysqli_query($dbcon, $query_user) or trigger_error("A database request issue has occured", E_USER_ERROR);
	$resultset_user = mysqli_fetch_array($result_user);
	
	// Only use with SELECT query
	mysqli_free_result($result_user);
	
	  if(!is_null($resultset_user)) {
		   
		   // If account is not yet activated resend email w/ new token
		   if ($resultset_user['activated'] == 0) {
			   		   
			   $response = array(
			   'status' => false, 
			   'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;This account has not been activated yet. Please check your inbox and confirm your email to activate your account or  
			   <a href=\"resend.php?email=".$log_email."\">resend</a> a confirmation email.");
			   
		   // If login / pass is correct
		   } else {
			  // Session creation
			  session_start();
			  session_regenerate_id(true);
			  ini_set('session.cookie_httponly', true);
			  $_SESSION['user_uid'] = $resultset_user['uid']; 
			  $_SESSION['session'] = true;
	  
			  // Token creation
			  $token = sha1(uniqid(mt_rand(), true));
			  $_SESSION['token'] = $token;
			  $query_token = "UPDATE users_site SET token='$token' WHERE email='$log_email' AND password='$crypted'";
			  $result_token = mysqli_query($dbcon, $query_token) or trigger_error("A database request issue has occured", E_USER_ERROR);
			
			  // Save cookie for remember me	  
			  if ($log_remember == "true") {
			  	setcookie("yrme", $token, time()+3600*24*30, "/", "dev.yootag.com"); //30 days
			  }
			  			  
			  // Send response
			  $response = array(
			  'status' => true,
			  'msg' => $log_type);
			  
		   }
		   
	 // If login / pass are incorect
	 } else {
		$response = array(
		'status' => false, 
		'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Username / password is incorrect");
	 }
		
	mysqli_close($dbcon);
        
	echo json_encode($response);
?>