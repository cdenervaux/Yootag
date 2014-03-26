<?php
include ("../lib/conf/config.php");
include ("../lib/api/fb/config.php");

  // START SESSION CHECK //

  session_start();
  $inactive = 300; // 5 minutes
  //$inactive_oauth = 3600; // 1 hour
  $loginUrl = '';
  
  // Check PHP session 
  if (isset($_SESSION['session'])) {
	  
	  // If valid but timed out
	  if (isset($_SESSION["timeout"])) {   
		  $sessionTTL = time() - $_SESSION["timeout"];
		  if ($sessionTTL > $inactive) {
			  session_destroy();
			  exit;
		  }
	  }
	  $_SESSION["timeout"] = time();
	  
	  // If session is still valid redirect to dashboard page
  	  header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
	  exit;	  
  
  
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

	  // Include Facebook API
	  include ("../lib/api/fb/facebook.php");
	  
	  // Initiate Facebook instance
	  $facebook = new Facebook(array(
		'appId'  => $fbconfig['appid'],
		'secret' => $fbconfig['secret'], 
		'cookie' => true,
	  ));
	  	   
	  // Check for existing session
	  $fb_uid = $facebook->getUser();
	
	  if ($fb_uid) {
		try {		  
			// Check existing session token
			$fb_user = $facebook->api('/me');
			
			// Retrieve profile info
			$fb_email = $fb_user['email'];
			$fb_first_name = $fb_user['first_name'];
			$fb_last_name = $fb_user['last_name'];
			
			// Set session variable
			$_SESSION['oauth'] = true;
		
			// Check if user already exists in DB
			include ("../lib/db/connect_db.php");
			$query = "SELECT * FROM users_oauth WHERE email='$fb_email'";
			$result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);
			
			$affected_rows = mysqli_num_rows($result);
			// Only use with SELECT query
			mysqli_free_result($result);
			
			// Register new user
			if($affected_rows == 0) {
				
			  $currentdate = date('Y-m-d H:i:s');
			  $query = "INSERT INTO users_oauth VALUES('','$fb_uid','fb','$fb_email','$fb_first_name','$fb_last_name','$currentdate','$currentdate')";
			  $result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);
			  
				  if ($result) {  
				    // Confirmation email
					$headers = "From:noreply@yootag.com"."\r\n";  
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";  
					$emailbody = "<p>Hello,</p><p>Welcome to Yootag!</p> 
									<p>You can start using Yootag right away through your Facebook account.</p> 
									<p>Thank you,</p>
									<p>The Yootag Team</p>";
			
					// Send email message
					mail($fb_email,"Yootag: Your Account Info",$emailbody,$headers);
				  }
			}
			mysqli_close($dbcon);
		   
			// Then redirect to dashboard page
			header ("Location: ".ABSPATH."/en/dashboard/dashboard.php");
			exit;
		  
	    } catch (FacebookApiException $e) {
		    error_log($e);
		    $fb_uid = null;
				
			// If session token is not valid
			$loginUrl = $facebook->getLoginUrl(array(
				'scope'		=> 'email',
				'redirect_uri' => ABSPATH.'/en/user/logreg.php'
			));
	    }
	
	 // If session is not valid
	 } else {
	    $loginUrl = $facebook->getLoginUrl(array(
		  'scope'		=> 'email',
		  'redirect_uri' => ABSPATH.'/en/user/logreg.php'
	    ));
	 }
}
*/

  // END SESSION CHECK //
?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag - Sign-in</title>
<meta name="description" content="Yootag">
<meta name="viewport" content="width=device-width">
<link href="../lib/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="fb-root"></div>
<script> /*
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId: '296010013757663',                        
      status: true,
      cookie: true,
	  oauth: true                               
    });
  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk')); */
</script>

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

<div class="gen_msgbox"></div>

  <div id="gen_form_login">
  <H3>Log in</H3>Please log in to continue.<br/><br/>
    <form>  
     <label for="email" id="log_email_label">Email</label><br/>
     <input type="text" name="log_email" id="log_email" size="25" maxlength="45" placeholder="Email" tabindex="1"/><br/><span id="msg_log_email"></span><br/><br/>
     <label for="password" id="log_password_label">Password</label><a href="#gen_modal_forgot" id="gen_modal_forgot_link">Forgot password?</a><br/>
     <input type="password" name="log_password" id="log_password" size="25" maxlength="45" placeholder="Password" tabindex="2"/><br/><span id="msg_log_password"></span><br/>
     <input type="checkbox" name="log_remember" id="log_remember" tabindex="3">Remember me<br/><br/>
     <input type="submit" name="log_submit_button" class="log_submit_button" id="log_submit_button" value="Log me in!" tabindex="4"/> 
     <input type="hidden" name="log_type" id="log_type" value="std">
    </form>
    <!-- <br/><br/><a href="<?php echo $loginUrl; ?>"><img src="../img/signin_facebook.jpg"></a><br/> -->
  </div>
  
  <div id="logreg_form_register">
  <H3>New to Yootag?</H3>A Yootag account is required to continue.<br/><br/>
    <form>
	  <label for="email">Email</label><br/>
      <input type="email" name="reg_email" id="reg_email" size="25" maxlength="45" placeholder="Email" tabindex="5"><br/><span id="msg_reg_email"></span><br/><br/>
	  <label for="password">Password</label><br/>
      <input type="password" name="reg_password" id="reg_password" size="25" maxlength="45" placeholder="Password" tabindex="6"><br/><span id="msg_reg_password"></span><br/><br/>
	  <label for="mpassword">Re-type Password</label><br/>
      <input type="password" name="reg_matchpassword" id="reg_matchpassword" size="25" maxlength="45" placeholder="Re-enter password" tabindex="7"><br/><span id="msg_reg_matchpassword"></span><br/><br/>
      <input type="submit" class="reg_submit_button" name="reg_submit_button" value="Sign me up!" tabindex="8">
    </form>
  </div>
    
</section>

<!--Closing div for content-->
</div>

<div id="gen_modal_forgot">
	<div>
		<a href="#close" title="Close" class="gen_modal_close"></a>
		<h3>Forgot your password?</h3>
		<p>Enter your email and we will reset your password.</p>
        <form>
         <label for="email" id="lost_email_label">Email</label>
         <input type="text" name="lost_email" id="lost_email" size="25" maxlength="45" placeholder="Email"/><br/><span id="msg_lost_email"></span><br/><br/>
         <input type="submit" name="lost_submit_button" class="lost_submit_button" id="lost_submit_button" value="Reset my password" />
        </form>
	</div>
</div>

<!--Opening div for footer-->
<footer id="gen_footer">
<?php include '../lib/inc/bottommenu_inc.php';?>
</footer>
<!--Closing div for footer-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='../lib/js/script.js'></script>
</body>
</html>