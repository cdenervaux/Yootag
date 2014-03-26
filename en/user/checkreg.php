<?php
include ("../lib/conf/config.php");

  $reg_email = trim($_POST["reg_email"]); // strip any white space
  $response = array();
  
  // if the email is blank
  if (!$reg_email) {
    $response = array(
      'status' => false, 
      'msg' => "<img src=\"".ABSPATH_IMG."cross_circle_frame.png\">&nbsp;The email value is missing");
	   
  // if the email is not in the form of me@site.com it's not valid
  } elseif (!preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $reg_email)) {
		$response = array(
		  'status' => false, 
		  'msg' => "<img src=\"".ABSPATH_IMG."cross_circle_frame.png\">&nbsp;The email format is not valid");  
  
  } else {
	  // check if the email already exists in the database
	  include ("../lib/db/connect_db.php");
	  $query_email = "SELECT * FROM users_site WHERE email='$reg_email'";
			  	  
	  $result_email = mysqli_query($dbcon, $query_email) or trigger_error("A database request issue has occured", E_USER_ERROR);
      $affected_rows = mysqli_num_rows($result_email);
	
	  if($affected_rows == 1) {
		$response = array(
			  'status' => false, 
			  'msg' => "<img src=\"".ABSPATH_IMG."cross_circle_frame.png\">&nbsp;This email is already registered. <a href=\"#\" onclick=\"$('input#log_email').focus();\">Log in?</a>");
	  } else {  
		$response = array(
		  'status' => true);
	  }
	  mysqli_free_result($result_email);
	  mysqli_close($dbcon);
  }

echo json_encode($response);
?>