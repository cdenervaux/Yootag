<?php
include ("../lib/conf/config.php");
include ("../lib/api/fb/config.php");

  // START SESSION CHECK //

  session_start();
  $inactive = 300;
  //$inactive_oauth = 3600;

  // Check PHP session
  if (isset($_SESSION['session'])) {
	
  	// If valid but timed out
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
				// Redirect to dashboard page
				header ("Location: ".ABSPATH."/en/user/logreg.php");
				exit;
			}
		} 
  
  } else {
	  // Redirect to login page
	  header ("Location: ".ABSPATH."/en/user/logreg.php");
	  exit;
  }
/* 
  // Check Facebook session
  } elseif (isset($_SESSION['oauth'])) {
	  
	  // If valid but timed out
	  if (isset($_SESSION["timeout_oauth"])) {   
		  $sessionTTL_oauth = time() - $_SESSION["timeout_oauth"];
		  
		  if ($sessionTTL_oauth > $inactive_oauth) {
		
			  include ("../lib/api/fb/facebook.php");
			  include ("../lib/api/fb/config.php");
				
			  // Initiate Facebook instance 
			  $facebook = new Facebook(array(
				  'appId'  => $fbconfig['appid'],
				  'secret' => $fbconfig['secret'], 
				  'cookie' => true,
			  ));
					 
			  // Check for existing FB session
			  $fb_uid = $facebook->getUser();
			  
			  // Check if existing FB session token is still valid
			  try { 
				$fb_user = $facebook->api('/me');
			  
			  } catch (FacebookApiException $e) {
				error_log($e);
				$fb_uid = null;
			  }
			  
			  if (!$fb_uid) {
				// Redirect to login page
				header ("Location: ".ABSPATH."/en/user/logreg.php");
				exit;
			  }
		  }
	  }
	  $_SESSION["timeout_oauth"] = time();


  } else {
	  // Check Facebook session
	  include ("../lib/api/fb/facebook.php");
	  include ("../lib/api/fb/config.php");
		
	  // Initiate Facebook instance 
	  $facebook = new Facebook(array(
		  'appId'  => $fbconfig['appid'],
		  'secret' => $fbconfig['secret'], 
		  'cookie' => true,
	  ));
			 
	  // Check for existing FB session
	  $fb_uid = $facebook->getUser();
	  
	  // Check if existing FB session token is still valid
	  try { 
		$fb_user = $facebook->api('/me');
	  
	  } catch (FacebookApiException $e) {
		error_log($e);
		$fb_uid = null;
	  }
	  
	  if (!$fb_uid) {
		// Redirect to login page
		header ("Location: ".ABSPATH."/en/user/logreg.php");
		exit;
	  }
	  else {
		// Set session variable
		$_SESSION['oauth'] = true; 
	  }
  }

*/

  // END SESSION CHECK //

  // Query user profile information
  include ("../lib/db/connect_db.php");
  $usr_uid = $_SESSION['user_uid'];
  
  $query_profile = "SELECT users_site.uid, users_site.email, users_site.password, users_site.token, users_profile.first_name, users_profile.last_name FROM users_site, users_profile 
  			WHERE users_site.uid='$usr_uid'
			AND users_profile.user_id ='$usr_uid'";
			 
  $result_profile = mysqli_query($dbcon, $query_profile) or trigger_error("A database request issue has occured", E_USER_ERROR);
  
  while($row = mysqli_fetch_array($result_profile)){  // LOOP TO BE REMOVED AND REPLACED BY RESULTSET VAR
	  $pro_email = $row['email'];
	  $pro_firstname = $row['first_name'];
	  $pro_lastname = $row['last_name'];
  }
  
  mysqli_free_result($result_profile);
  mysqli_close($dbcon);
?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag - My Profile</title>
<meta name="description" content="Web Development">
<meta name="viewport" content="width=device-width">
<link href="../lib/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<!--Opening div for header-->
<header id="gen_header" class="cf">

<div id="gen_header_wrapper">
  <div id="gen_logo">Yootag</div>  
  <div id="gen_navbar">
	<?php include '../lib/inc/topmenu_inc.php';?>
  </div>
</div>

</header>
<!--Closing div for header-->


<!--Opening div for content-->
<div id="gen_content" class="cf">

<section id="gen_section">

<span class="gen_msgbox"></span>

<div id="profile_form">
  <H3>Account Information</H3>                            
  <form>       
    <label for="email">Email</label><br/>
    <input type="text" name="pro_email" id="pro_email" size="30" maxlength="45" value="<?php echo($pro_email);?>" readonly disabled><br/><br/>
	<label for="firstname">First name</label><br/>
    <input type="text" name="pro_firstname" id="pro_firstname"  size="30" maxlength="45" value="<?php echo($pro_firstname);?>" tabindex="1"><br/><br/>
	<label for="lastname">Last name</label><br/>
 	<input type="text" name="pro_lastname" id="pro_lastname"  size="30" maxlength="45" value="<?php echo($pro_lastname);?>" tabindex="2"><br/><br/>
    <input type="submit" name="pro_submit_button" class="pro_submit_button" value="Save changes" tabindex="3">
  </form>
</div>
 
</section>

</div>
<!--Closing div for content-->

<!--Opening div for footer-->
<footer id="gen_footer">
<?php include '../lib/inc/bottommenu_inc.php';?>
</footer>
<!--Closing div for footer-->


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='../lib/js/script.js'></script>
</body>
</html>