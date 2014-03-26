<?php
include ("../lib/conf/config.php");

$reg_email = trim($_POST["reg_email"]);
$reg_password = trim($_POST["reg_password"]);
$response = array();

  // Anti-SQL injection - Check input fields are not empty  
  if (!$reg_email){
	  $response = array(
      'status' => false, 
      'msg' => "Email is missing");
	  
  } elseif (!$reg_password){
	  $response = array(
      'status' => false, 
      'msg' => "Password is missing");
  
  } else {
	  // Anti-SQL injection - Check email is not already registered
	  include ("../lib/db/connect_db.php");
	  $query_email = "SELECT * FROM users_site WHERE email='$reg_email'";
	  $result_email = mysqli_query($dbcon, $query_email) or trigger_error("A database request issue has occured", E_USER_ERROR);
	  $affected_rows = mysqli_num_rows($result_email);
	  
	  // Only use with SELECT query
	  mysqli_free_result($result_email);
	  
	  if($affected_rows == 1) {
		$response = array(
			  'status' => false, 
			  'msg' => "<img src=\"".ABSPATH_IMG."cross_circle_frame.png\">&nbsp;This email is already registered. <a href=\"#\" onclick=\"$('input#log_email').focus();\">Log in</a>.");
	  
	  } else {    
		// Account creation 
		$uid = substr(sha1(uniqid(mt_rand(), true)),0,8);
		$salt = mb_strtolower($reg_email);
		$crypted = sha1($salt.$reg_password);
		$token = sha1(uniqid(mt_rand(), true));
	  	$currentdate = date('Y-m-d H:i:s');
		
		$query_site = "INSERT INTO users_site VALUES('$uid','$reg_email','$crypted','$token','0','$currentdate','$currentdate')";
		$result_site = mysqli_query($dbcon, $query_site) or trigger_error("A database request issue has occured", E_USER_ERROR);
		
		$query_profile = "INSERT INTO users_profile VALUES('$uid','','','$currentdate','$currentdate')";
		$result_profile = mysqli_query($dbcon, $query_profile) or trigger_error("A database request issue has occured", E_USER_ERROR);
		
			if ((!$result_site) || (!$result_profile)) {
				$response = array(
				'status' => false, 
				'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;<b>Error creating account. Please try again later</b>");
			
			}  else {
				// Confirmation email
      			$headers = "From:noreply@yootag.com"."\r\n";  
      			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";  
      			$emailbody = "<p>Hello,</p><p>Welcome to Yootag! To start using Yootag please click on the link below to confirm your email and activate your account.</p> 
                  			  <p>Activation URL: <a href=\"".ABSPATH."/en/user/activate.php?token={$token}\">".ABSPATH."/en/user/activate.php?token={$token}</a></p> 
                  			  <p>Thank you,</p>
				  			  <p>The Yootag Team</p>";
    
	  			// Send email message
	  			mail($reg_email,"Yootag: Your Account Info",$emailbody,$headers);
			
				$response = array(
				'status' => true, 
				'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;Your account has been created. Please check your inbox to confirm your email and activate your account.");
			}
	   }
	   mysqli_close($dbcon);
  }
   
echo json_encode($response);
?>