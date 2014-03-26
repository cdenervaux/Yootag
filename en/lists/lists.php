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
   
// Get product URL content function
function get_data($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag - My Lists</title>
<meta name="description" content="Web Development">
<meta name="viewport" content="width=device-width">
<link href="../lib/css/style.css" rel="stylesheet" type="text/css">
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
	<?php include '../lib/inc/topmenu_inc.php';?>
  </div>
</div>

</header>
<!--Closing div for header-->


<!--Opening div for content-->
<div id="gen_content" class="cf">

<section id="gen_section">
  <div class="gen_wrapper">
    <div class="gen_menu">
      <div class="gen_menu_left">LISTS</div>
      <div class="gen_menu_right"><a href="#" class="lists_modal_add">Create a new list</a></div>	
    </div>
   
    <div class="gen_list">
    <br/>
        <?php
          include ("../lib/db/connect_db.php");
          $usr_uid = $_SESSION['user_uid'];
          
          $query_list = "SELECT uid, name FROM lists_products WHERE user_uid='$usr_uid'";	 
          $result_list = mysqli_query($dbcon, $query_list) or trigger_error("A database request issue has occured", E_USER_ERROR);
          $resultset_list = mysqli_fetch_all($result_list, MYSQLI_ASSOC);
                  
          // Only use with SELECT query
          mysqli_free_result($result_list);
        
          // Display existing lists
          if(!is_null($resultset_list)) {				  
                  foreach($resultset_list as $edit_list_value) {		
                        echo ('<div class="lists_item"><div class="dashboard_modal_editlist_name">'.urldecode($edit_list_value['name']).'</div><div class="dashboard_modal_editlist_options">
                        <a href="#" class="dashboard_modal_editlist_edit_click">Edit</a> | <a href="#" class="dashboard_modal_editlist_delete_click">Delete</a></div>
						<div class="dashboard_modal_editlist_lastupdate">Last updated: 10/11/2013</div>
						
						<br/>5 products<br/>This list is the list description</div>');
                  }
          }
          
          mysqli_close($dbcon);
        ?>
        
        <div id="dashboard_modal_editlist_edit"><a href="#" title="Back" class="gen_modal_back"></a><h3>List</h3>
        <label for="dashboard_modal_editlist_name_label">Name</label><br/><input type="text" name="dashboard_modal_editlist_name" id="dashboard_modal_editlist_name" size="20" maxlength="45"><br/><br/>
        <label for="dashboard_modal_editlist_description_label">Description</label><br/><textarea name="dashboard_modal_editlist_description" id="dashboard_modal_editlist_description" cols="" rows=""></textarea><br/><br/>
        <label for="dashboard_modal_editlist_privacy_label">Privacy</label><br/>
        <label for=""><input type="radio" name="dashboard_modal_editlist_privacy" id="dashboard_modal_editlist_privacy_public" value = "0"/>Public</label>
        <label for=""><input type="radio" name="dashboard_modal_editlist_privacy" id="dashboard_modal_editlist_privacy_private" value = "1"/>Private</label>
        <br/><br/>You friends can access this list through the following URL:
        </div>
        
        <div id="dashboard_modal_editlist_delete"><a href="#" title="Back" class="gen_modal_back"></a><h3>Remove list</h3><br/>
        <label for="lists_modal_dellist_label">Are you sure ?</label><br/><br/>
					   <a href="#" class="dashboard_modal_delprod_yes">Yes</a> | <a href="#" class="dashboard_modal_delprod_no">No</a>
        </div>
        <div id="dashboard_modal_editlist_create"><a href="#" title="Back" class="gen_modal_back"></a><h3>Create list</h3><br/></div>
    </div>
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