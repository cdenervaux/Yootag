<?php
include ("../lib/conf/config.php");

  $lost_email = trim($_POST["lost_email"]); // strip any white space
  $response = array();
  
  // if the email is blank
  if (!$lost_email) {
    $response = array(
    'status' => false, 
    'msg' => "<img src=\"".ABSPATH_IMG."cross_circle_frame.png\">&nbsp;The email value is missing");
	   
	  // if the email is not in the form of me@site.com it's not valid
	  } elseif (!preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $lost_email)) {
			$response = array(
			  'status' => false, 
			  'msg' => "<img src=\"".ABSPATH_IMG."cross_circle_frame.png\">&nbsp;The email format is not valid");  
	  
	  } else {
		  // check if the email already exists in the database
		  include ("../lib/db/connect_db.php");
		  $query_email = "SELECT * FROM users_site WHERE email='$lost_email'";
					  
		  $result_email = mysqli_query($dbcon, $query_email) or trigger_error("A database request issue has occured", E_USER_ERROR);
		  $affected_rows = mysqli_num_rows($result_email);
		  
		  // Only use with SELECT query
	 	  mysqli_free_result($result_email);
		
		  if($affected_rows == 1) {
			  
			 //Token creation
			 $token = sha1(uniqid(mt_rand(), true));
			 $currentdate = date('Y-m-d H:i:s');
			 
			 $query_token = "UPDATE users_site SET token='$token', last_updated='$currentdate' WHERE email='$lost_email'";
			 $result_token = mysqli_query($dbcon, $query_token) or trigger_error("A database request issue has occured", E_USER_ERROR);
			 
			 // Confirmation email
			 $headers = "From:noreply@yootag.com"."\r\n";
			 $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";  
			 $emailbody = "<p>Hello,</p><p>You are receiving this e-mail because you requested a password reset for your user account at Yootag. Please go to the following page and choose a new password:</p> 
				<p><a href=\"".ABSPATH."/en/user/reset.php?token=".$token."\">".ABSPATH."/en/user/reset.php?token=".$token."</a></p> 
				<p>Thank you,</p>
				<p>The Yootag Team</p>";
	
			 // Send email message
			 mail($lost_email,"Yootag: Password reset",$emailbody,$headers);
			  
			 $response = array(
				  'status' => true, 
				  'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;An email has been sent with further instructions.");
		   
		   } else {  
		   	 $response = array(
 				  'status' => false, 
				  'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Sorry, we don't know that email address. Try again?");
		   }
		   mysqli_close($dbcon);
  }

echo json_encode($response);
?>