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
<title>Yootag - Dashboard</title>
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

  <div class="gen_menu">
	<div class="gen_menu_left">DASHBOARD</div>
    
	<div class="gen_menu_right">Filter by list <select name="prod_list" id="prod_list" tabindex="1">
    <option value="0">View all</option>
    <?php 
		if (isset($_GET['filter']) || (!empty($_GET['filter'])))
			$list_filter = $_GET['filter'];
		else 
			$list_filter = '';
		
		$list_crumb = '';
		include ("../lib/db/connect_db.php");
		$usr_uid = $_SESSION['user_uid'];
		
		$query_list = "SELECT uid, name FROM lists_products WHERE user_uid='$usr_uid'";	 
		$result_list = mysqli_query($dbcon, $query_list) or trigger_error("A database request issue has occured", E_USER_ERROR);
		$resultset_list = mysqli_fetch_all($result_list, MYSQLI_ASSOC);
				
		// Only use with SELECT query
		mysqli_free_result($result_list);
	
		if(!is_null($resultset_list)) {				  
			foreach($resultset_list as $filter_list_value) {		
				if ($list_filter == $filter_list_value['uid']) {
					$list_crumb = urldecode($filter_list_value['name']);
					echo ("<option value=\"".$filter_list_value['uid']."\" selected>".urldecode($filter_list_value['name'])."</option>");	
			} else
					echo ("<option value=\"".$filter_list_value['uid']."\">".urldecode($filter_list_value['name'])."</option>");
			}
		}
	?>
	</select>
    <!-- <a href="<?php ABSPATH?>/en/lists/lists.php"> Edit lists</a> -->
	</div>
   </div>
   <!-- <div id="dashboard_crumblist"><br/><?php //if (empty($list_crumb)) echo ("List > View All"); else echo ("List > ".$list_crumb);?></div> -->  
   <div id="dashboard_modal_editlist"><a href="#" title="Close" class="gen_modal_close"></a><h3>My lists</h3>
	  <?php
		// Display existing lists
		if(!is_null($resultset_list)) {				  
				foreach($resultset_list as $edit_list_value) {		
					  echo ('<div class="dashboard_modal_editlist_item"><div class="dashboard_modal_editlist_name">'.urldecode($edit_list_value['name']).'</div><div class="dashboard_modal_editlist_options">
					  <a href="#" class="dashboard_modal_editlist_edit_click">Edit</a> | <a href="#" class="dashboard_modal_editlist_delete_click">Delete</a></div></div>');
				}
		}
	  ?>
      <div id="dashboard_modal_editlist_edit"><a href="#" title="Back" class="gen_modal_back"></a><h3>Properties</h3>
      <label for="dashboard_modal_editlist_name_label">Name</label><br/><input type="text" name="dashboard_modal_editlist_name" id="dashboard_modal_editlist_name" size="20" maxlength="45"><br/><br/>
      <label for="dashboard_modal_editlist_description_label">Description</label><br/><textarea name="dashboard_modal_editlist_description" id="dashboard_modal_editlist_description" cols="" rows=""></textarea><br/><br/>
      <label for="dashboard_modal_editlist_privacy_label">Privacy</label><br/>
      <label for=""><input type="radio" name="dashboard_modal_editlist_privacy" id="dashboard_modal_editlist_privacy_public" value = "0"/>Public</label>
      <label for=""><input type="radio" name="dashboard_modal_editlist_privacy" id="dashboard_modal_editlist_privacy_private" value = "1"/>Private</label>
      </div>
      
	  <div id="dashboard_modal_editlist_delete"><a href="#" title="Back" class="gen_modal_back"></a><h3>Remove list</h3><br/></div>
      <div id="dashboard_modal_editlist_create"><a href="#" title="Back" class="gen_modal_back"></a><h3>Create list</h3><br/></div>
      <br/><br/><a href="#" class="dashboard_modal_editlist_create_click">Create a new list</a><br/><br/>
  </div>
 
  <div class="gen_list">
    <br/>
    <?php
      // Lookup product info
      include ("../lib/db/connect_db.php");
      $usr_uid = $_SESSION['user_uid'];
      
      // Check for list filter
      if (!$list_filter) {
          $query_filter = "SELECT uid, site_uid, list_uid, title, description, image_link, direct_link, alert_range, affiliate, DATE_FORMAT(created ,'%M %d, %Y') AS created_date 
          FROM products_info 
          WHERE user_uid='$usr_uid'";
          
      } else {	
          $query_filter = "SELECT uid, site_uid, list_uid, title, description, image_link, direct_link, alert_range, affiliate, DATE_FORMAT(created ,'%M %d, %Y') AS created_date 
          FROM products_info	
          WHERE user_uid='$usr_uid' 
          AND list_uid ='$list_filter'";
      }
      
      $result_filter = mysqli_query($dbcon, $query_filter) or trigger_error("A database request issue has occured", E_USER_ERROR);
      
      while($resultset_filter = mysqli_fetch_array($result_filter)){
          
          $prod_uid = $resultset_filter['uid'];
          $site_uid = $resultset_filter['site_uid'];
          $list_uid = $resultset_filter['list_uid'];
          
          $prod_title = urldecode($resultset_filter['title']);
		  if (strlen($prod_title) >= 55) {
			  $prod_title = substr($prod_title, 0, 55). "...";
		  	  
		  }
          $prod_description = urldecode($resultset_filter['description']);
		  
		  $prod_boxhsot = urldecode($resultset_filter['image_link']);
		  $image_file = get_data($prod_boxhsot);
		  $im = imagecreatefromstring($image_file);
		  
		  // Check product boxshot size
		  $ratio = imagesx($im)/imagesy($im);
		  if( $ratio > 1) {
    		$image_width = 150;
    		$image_height = 150/$ratio;
		  } else {
    		$image_width = 150*$ratio;
    		$image_height = 150;
		  }
		  
		  $prod_link = urldecode($resultset_filter['direct_link']);
          $prod_alert = $resultset_filter['alert_range'];
          $prod_affiliate = $resultset_filter['affiliate'];
          $prod_created = $resultset_filter['created_date'];
          
          
          // Lookup latest product price
          $query_price = "SELECT currency, amount FROM products_price WHERE product_uid='$prod_uid' ORDER BY created DESC LIMIT 1";
  
          $result_price = mysqli_query($dbcon, $query_price) or trigger_error("A database request issue has occured", E_USER_ERROR);
          $resultset_price = mysqli_fetch_array($result_price);
          
          $prod_currency = urldecode($resultset_price['currency']);
          $prod_amount = number_format($resultset_price['amount'],2);
  
          mysqli_free_result($result_price);
      
          
          // Lookup product site info
          $query_site = "SELECT name FROM sites_info WHERE uid='$site_uid'";
             
          $result_site = mysqli_query($dbcon, $query_site) or trigger_error("A database request issue has occured", E_USER_ERROR);
          $resultset_site = mysqli_fetch_array($result_site);
          
          $site_name = urldecode($resultset_site['name']);
  
          mysqli_free_result($result_site);
          
      
          // Display product content
          echo ('<div class="dashboard_item">
                 <a href="#" title="Edit" class="gen_modal_edit"></a>
                 <a href="#" title="Remove" class="gen_modal_remove"></a>
                 <span title="'.$site_name.'" class="dashboard_item_site">'.$site_name.'</span><br/>
                 <img class="dashboard_item_boxshot" src="'.$prod_boxhsot.'" height="'.$image_height.'" width="'.$image_width.'"><br/><span class="dashboard_item_added">Added '.$prod_created.'</span><br/>
                 <span class="dashboard_item_title">'.$prod_title.'</span><br/><span class="dashboard_item_price">'.$prod_currency.$prod_amount.'</span><br/>
                 <a href="'.$prod_link.'" target="_blank" class="dashboard_button_buy">Buy Now</a>
                 <input type="hidden" name="prod_uid" class="prod_uid" value="'.$prod_uid.'">');
				 
                // Display product properties modal
                echo ('<div class="dashboard_modal_editprod"><a href="#" title="Back" class="gen_modal_back"></a><h3>Properties</h3><br/>
                
                       <label for="mod_alert_label">Notify me when price</label><br/>
                       <select name="mod_alert" class="mod_alert">');
                       if ($prod_alert == '1')
                       echo ('<option value="1" selected>drops by any amount</option>
                              <option value="2">drops by at least 25%</option>
                              <option value="3">drops by at least 50%</option>');
                       
                       elseif ($prod_alert == '2')
                       echo ('<option value="1">drops by any amount</option>
                              <option value="2" selected>drops by at least 25%</option>
                              <option value="3">drops by at least 50%</option>');
                       
                       elseif ($prod_alert == '3')
                       echo ('<option value="1">drops by any amount</option>
                              <option value="2">drops by at least 25%</option>
                              <option value="3" selected>drops by at least 50%</option>');
                       echo ('</select><br/><br/>');
                       
                       echo('<label for="add_listname_label">Assign to list</label><br/>
                       <select name="mod_list" class="mod_list"><option value=" ">None</option>'); // Space needed for value None
                       if(!is_null($resultset_list))
                        foreach($resultset_list as $mod_list_value) 
                          if ($list_uid == $mod_list_value['uid'])
                            echo ('<option value='.$mod_list_value['uid'].' selected>'.urldecode($mod_list_value["name"]).'</option>');
                          else		
                            echo ('<option value='.$mod_list_value['uid'].'>'.urldecode($mod_list_value["name"]).'</option>');
                       
                       echo('</select><br/><br/></div>');
				 
				 // Display	product delete modal
				 echo ('<div class="dashboard_modal_delprod"><h3>Remove item</h3><br/>
                       <label for="dashboard_modal_delprod_label">Are you sure ?</label><br/><br/>
					   <a href="#" class="dashboard_modal_delprod_yes">Yes</a> | <a href="#" class="dashboard_modal_delprod_no">No</a>');	   	   
           		 echo ('</div>'); 
				 
           echo ('</div>'); 	
                          
      }
      mysqli_free_result($result_filter);
      mysqli_close($dbcon);
  ?>
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