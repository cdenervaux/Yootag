<?php
include ("../lib/conf/config.php");

  // START SESSION CHECK //
  
  session_start();
  $inactive = 300;

  // Check PHP session	
  if (isset($_SESSION['session'])) {
	  
	  if (isset($_SESSION["timeout"])) {   
		  $sessionTTL = time() - $_SESSION["timeout"];
		  if ($sessionTTL > $inactive) {
			  session_destroy();
			  header ("Location: ".ABSPATH."/en/user/logreg.php");
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
			
			if(!is_null($resultset_token)) {
				// Reactivate session
				session_regenerate_id(true);
				ini_set('session.cookie_httponly', true); 
				$_SESSION['cookie'] = true;
				$_SESSION['token'] = $token;
				$_SESSION['user_uid'] = $resultset_token['uid'];
			    
			} else {
				// Redirect to login page page
				header ("Location: ".ABSPATH."/en/user/logreg.php");
			    exit;
			}
		} 
  
  } else {
	  // Redirect to login page
	  header ("Location: ".ABSPATH."/en/user/logreg.php");
	  exit;
  }
  
  // END SESSION CHECK //
 
  
  $prod_uid = ($_POST['prod_uid']);
  $mod_alert = ($_POST['mod_alert']);

  // Anti-SQL injection - Check input fields are not empty  
  if ((!$prod_uid) || (!$mod_alert)) {
	  $response = array(
      'status' => false, 
      'msg' => "Product id or Alert setting is missing");
	  
  } else {
	  // Update product alert
	  include ("../lib/db/connect_db.php");
	  $query_modalert = "UPDATE products_info SET alert_range='$mod_alert' WHERE uid ='$prod_uid'";
	  $result_modalert = mysqli_query($dbcon, $query_modalert) or trigger_error("A database request issue has occured", E_USER_ERROR);
	
	  		
		if (!$result_modalert) {
			$response = array(
			'status' => false, 
			'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;<b>Error updating alert. Please try again later</b>");
		
		}  else {
  
			$response = array(
			'status' => true, 
			'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;Alert updated.");
		}
		
	  mysqli_close($dbcon);  
  }
  echo json_encode($response);    
?>