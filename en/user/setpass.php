<?php
include ("../lib/conf/config.php");   
    
$token = trim($_POST["token"]);
$new_password = trim($_POST["new_pass"]);

  if (!$token == "") {
	  include ("../lib/db/connect_db.php");
	  $query_user = "SELECT * FROM users_site WHERE token='$token'";
			  
	  $result_user = mysqli_query($dbcon, $query_user) or trigger_error("A database request issue has occured", E_USER_ERROR);
	  $affected_rows = mysqli_num_rows($result_user);
	  
	  while($row = mysqli_fetch_row($result_user)){
	  $usr_email = $row[1];
	  $lastupdated = $row[6];
	  }
	  
	  // Only use with SELECT query
	  mysqli_free_result($result_user);
	  
	  // Check if token is valid
	  if($affected_rows == 1) {
		  
		  $currentdate = new DateTime(date('Y-m-d H:i:s'));
		  $lastupdated = new DateTime($lastupdated);
		  $interval = $lastupdated->diff($currentdate);
		  
		  // Check if token has expired (1h validity)
		  if ($interval->h < 1) {
		  
			  // Update user password
			  $salt = mb_strtolower($usr_email);
			  $crypted = sha1($salt.$new_password);
			  $new_token = sha1(uniqid(mt_rand(), true));
  
			  $query_password = "UPDATE users_site SET password='$crypted', token='$new_token' WHERE email ='$usr_email'";
			  $result_password = mysqli_query($dbcon, $query_password) or trigger_error("A database request issue has occured", E_USER_ERROR);
						  
			  $response = array(
			  'status' => true, 
			  'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;Your new password has been set. You can now <a href=\"logreg.php\">login</a>.");
			  
		  } else {
		  echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Token has expired.");
		  }
	  
	  } else {
		  $response = array(
		  'status' => false, 
		  'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Token is invalid.");
	  }
	  
	  mysqli_close($dbcon);
		  
  } else {
	  $response = array(
	  'status' => false, 
	  'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Token is missing.");
  
  }
  echo json_encode($response);
  ?>