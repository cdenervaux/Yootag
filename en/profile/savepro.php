<?php
include ("../lib/conf/config.php");
  
  // START SESSION CHECK //
  
  session_start();
  $inactive = 300;
	
  if (isset($_SESSION['session'])) {
	  
	  if (isset($_SESSION["timeout"])) {   
		  $sessionTTL = time() - $_SESSION["timeout"];
		  if ($sessionTTL > $inactive) {
			  session_destroy();
			  
			  $response = array(
			  'status' => false, 
			  'msg' => "redirect");
			  echo (json_encode($response));
			  exit;
		  }
	  }
	  $_SESSION["timeout"] = time();
  
  
  // Check Remember Me cookie	  
  } elseif (isset($_COOKIE['yrme'])) {
		
		// Check if new query is needed
		if (!isset($_SESSION['cookie'])) {
			
			$token = $_COOKIE['yrme'];
			
			// MySQL query
		  	include ("../lib/db/connect_db.php");
		  	$query_token = "SELECT uid, token FROM users_site WHERE token='$token'";
		  
		  	$result_token = mysqli_query($dbcon, $query_token) or trigger_error("A database request issue has occured", E_USER_ERROR);
		  	$resultset_token = mysqli_fetch_array($result_token);
		  
		  	// Only use with SELECT query
		  	mysqli_free_result($result_token);	
			mysqli_close($dbcon);
			
			if(!is_null($resultset)) {
				// Reactivate session
				session_regenerate_id(true);
				ini_set('session.cookie_httponly', true); 
				$_SESSION['cookie'] = true;
				$_SESSION['token'] = $token;
				$_SESSION['user_uid'] = $resultset['uid'];
			    
			} else {
				// Redirect to login page page
				$response = array(
			    'status' => false, 
			    'msg' => "redirect");
			    echo (json_encode($response));
			    exit;
			}
		} 
  
  } else {
	  // Redirect to login page
	  $response = array(
	  'status' => false, 
	  'msg' => "redirect");
	  echo (json_encode($response));
	  exit;
  }
  
  // END SESSION CHECK //
  
  // Save user profile information
  $pro_firstname = trim($_POST["pro_firstname"]);
  $pro_lastname = trim($_POST["pro_lastname"]);
  $usr_uid = $_SESSION['user_uid'];
  
  include ("../lib/db/connect_db.php");
  
  $query_save = "UPDATE users_profile 
  			SET first_name='$pro_firstname', last_name='$pro_lastname' 
			WHERE user_id='$usr_uid'";
			
  $result_save = mysqli_query($dbcon, $query_save) or trigger_error("A database request issue has occured", E_USER_ERROR);
 
  if (!$result_save) {
	  $response = array(
	  'status' => false, 
	  'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;<b>Error saving changes. Please try again later</b>");
  
  }  else {
	  $response = array(
	  'status' => true, 
	  'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;<b>Your changes have been saved</b>");
  }
	   
  mysqli_close($dbcon);

echo json_encode($response);
?>
