<?php
  include ("lib/conf/config.php");
  include ("lib/api/fb/config.php");
  
  // START SESSION CHECK //
  
  session_start();
  $inactive = 300;
  //$inactive_oauth = 3600;

  // Check PHP session
  if (isset($_SESSION['session'])) {
	  
	  // If valid but timed out
	  if (isset($_SESSION['timeout'])) {   
		  $sessionTTL = time() - $_SESSION['timeout'];
		  if ($sessionTTL > $inactive) {
			  session_destroy();
			  header ("Location: ".ABSPATH."/en/user/logreg.php");
			  exit;
		  }
	  }
	  $_SESSION['timeout'] = time();
	  
	  // If session is still valid redirect to dashboard page
  	  header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
	  exit;
  
  
  // Check Remember Me cookie	  
  } elseif (isset($_COOKIE['yrme'])) {
		
		// Check if new query is needed
		if (!isset($_SESSION['cookie'])) {
			
			$token = $_COOKIE['yrme'];
			
			// MySQL query
		  	include ("lib/db/connect_db.php");
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
			    
				// Redirect to dashboard page
				header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
				exit;
			}
			  
		} else {
			// Redirect to dashboard page
			header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
			exit;
		}
  
  }
/* 
  // Check Facebook session
  } elseif (isset($_SESSION['oauth'])) {
	  
	  // If valid but timed out
	  if (isset($_SESSION["timeout_oauth"])) {   
		  $sessionTTL_oauth = time() - $_SESSION["timeout_oauth"];
		  
		  if ($sessionTTL_oauth > $inactive_oauth) {
		
			  include ("lib/api/fb/facebook.php");
			  include ("lib/api/fb/config.php");
				
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
			  
			  if ($fb_uid) {
				// Redirect to dahboard page
				header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
				exit;
			  }
		  }
	  }
	  $_SESSION["timeout_oauth"] = time();
	  
	  // Redirect to dahboard page
	  header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
	  exit;
	  
  
  } else {
	  // Check Facebook session
	  include ("lib/api/fb/facebook.php");
	  include ("lib/api/fb/config.php");
		
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
	  
	  if ($fb_uid) {
		// Set session variable
		$_SESSION['oauth'] = true;

		// Redirect to login page
		header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
		exit;
	  }
  }
*/

  // END SESSION CHECK //

?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag - Home</title>
<meta name="description" content="Yootag">
<meta name="viewport" content="width=device-width">
<link href="lib/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<!--Facebook SDK start-->
<div id="fb-root"></div>
<script> /*
  window.fbAsyncInit = function() {
    // Initialize
    FB.init({
      appId: '296010013757663',                        
      status: true,
      cookie: true,
      oauth: true                               
    });
  };

  // Load asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk')); */
</script>
<!--Facebook SDK end-->


<!--Opening div for header-->
<header id="gen_header" class="cf">

<div id="gen_header_wrapper">
  <div id="gen_logo">Yootag</div>  
  <div id="gen_navbar">
	<?php include 'lib/inc/topmenu_inc.php';?>
  </div>
</div>

</header>
<!--Closing div for header-->


<!--Opening div for content-->
<div id="gen_content" class="cf">

<section id="gen_section">
   
    <div id="welcome_img_main">
        <img src="img/man_shop.jpg">
    </div>

    <div id="welcome_text_main">
      Track your favorite products.<br>
      Share them with your friends.<br>
      Get alerted when price drops.<br>
    </div>
    
</section> 

<section id="gen_section">
    <div class="welcome_steps">Step 1:<br>Create an account</div>
    <div class="welcome_steps">Step 2:<br>Get the <a href="javascript:void((function(){var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('src','http://dev.yootag.com/en/lib/js/tagmarklet.js');document.body.appendChild(e);document.close();})())">Yootag button</a></div>
    <div class="welcome_steps">Step 3:<br>Start tagging!</div>
</section>

</div>
<!--Closing div for content-->


<!--Opening div for footer-->
<footer id="gen_footer">
<?php include 'lib/inc/bottommenu_inc.php';?>
</footer>
<!--Closing div for footer-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='lib/js/script.js'></script>
</body>
</html>