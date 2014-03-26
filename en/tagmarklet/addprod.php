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
			
			if(!is_null($resultset_token)) {
				// Reactivate session
				session_regenerate_id(true);
				ini_set('session.cookie_httponly', true); 
				$_SESSION['cookie'] = true;
				$_SESSION['token'] = $token;
				$_SESSION['user_uid'] = $resultset_token['uid'];
			    
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
 
   $response = array(
      'status' => true, 
      'msg' => "");
	
  // Get Site Id
  if (isset($_POST['site_uid']) && !empty($_POST['site_uid']))
  	$site_uid = $_POST['site_uid'];
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "Site ID is missing");
  }

  // Get product URL
  if (isset($_POST['add_url']) && !empty($_POST['add_url']))
  	$add_url = urlencode($_POST['add_url']);
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "URL is missing");
  }

  // Get product title	
  if (isset($_POST['add_title']) && !empty($_POST['add_title']))
  	$add_title = urlencode($_POST['add_title']);
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "Title is missing");
  }

  // Get product boxshot
  if (isset($_POST['add_boxshot']) && !empty($_POST['add_boxshot']))
  	$add_boxshot = urlencode($_POST['add_boxshot']);
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "Boxshot is missing");
  }

  // Get product currency
  if (isset($_POST['add_currency']) && !empty($_POST['add_currency']))
  	$add_currency = urlencode($_POST['add_currency']);
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "Currency is missing");
  }

  // Get product amount
  if (isset($_POST['add_amount']) && !empty($_POST['add_amount']))
  	$add_amount = str_replace(',', '' , $_POST['add_amount']);
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "Amount is missing");
  }

  // Get product alert
  if (isset($_POST['add_alert']) && !empty($_POST['add_alert']))
  	$add_alert = urlencode($_POST['add_alert']);
  else {
  	  $response = array(
      'status' => false, 
      'msg' => "Alert is missing");
  }
		 

  // Proceeed if all elements above exist
  if ($response['status']) {
	  
      $usr_uid = $_SESSION['user_uid'];
	  
	  // Check if product already exists
	  include ("../lib/db/connect_db.php");
	  
	  $query_check = "SELECT title FROM products_info WHERE user_uid='$usr_uid' AND site_uid='$site_uid' AND title='$add_title'";
	  $result_check = mysqli_query($dbcon, $query_check) or trigger_error("A database request issue has occured", E_USER_ERROR);
	  $affected_rows = mysqli_num_rows($result_check);
	  
	  // Only use with SELECT query
	  mysqli_free_result($result_check);
	  
	  if($affected_rows == 1) {
		$response = array(
			  'status' => false, 
			  'msg' => "Oops! You have already added this product to your dashboard.");
	  
	  // Add new product
	  } else {    
	  	
		$currentdate = date('Y-m-d H:i:s');
		$uid = substr(sha1(uniqid(mt_rand(), true)),0,8);
		$add_listuid = '';
		
  		// Check if product list already exists
  		if (isset($_POST['add_listname']) && !empty($_POST['add_listname'])) {
			
			$add_listname = urlencode($_POST['add_listname']);
				  
			$query_checklist = "SELECT uid, name FROM lists_products WHERE name='$add_listname'";	 
			$result_checklist = mysqli_query($dbcon, $query_checklist) or trigger_error("A database request issue has occured", E_USER_ERROR);
			$resultset_checklist = mysqli_fetch_array($result_checklist);
			
			// Only use with SELECT query
			mysqli_free_result($result_checklist);
		
			if(!is_null($resultset_checklist)) {
				$add_listuid = $resultset_checklist['uid'];
			
			} else {
				$add_listuid = substr(sha1(uniqid(mt_rand(), true)),0,8);
				
				// Add new category
				$query_addlist = "INSERT INTO lists_products VALUES('$add_listuid','$usr_uid','$add_listname','$currentdate','$currentdate')";
				$result_addlist = mysqli_query($dbcon, $query_addlist) or trigger_error("A database request issue has occured", E_USER_ERROR);
			}
		
		}
	  
		// Add product info
		$query_addprod = "INSERT INTO products_info VALUES('$uid','$usr_uid','$site_uid','$add_listuid','$add_title','','$add_boxshot','$add_url','$add_alert','','$currentdate','$currentdate')";
		$result_addprod = mysqli_query($dbcon, $query_addprod) or trigger_error("A database request issue has occured", E_USER_ERROR);
		
		// Add product price
		$query_addprice = "INSERT INTO products_price VALUES('$uid','$add_currency','$add_amount','$currentdate','$currentdate')";
		$result_addprice = mysqli_query($dbcon, $query_addprice) or trigger_error("A database request issue has occured", E_USER_ERROR);
		
			if ((!$result_addprod) || (!$result_addprice)){
				$response = array(
				'status' => false, 
				'msg' => "Oops! There was an error adding this product to your dashboard. Sorry for the inconvenience.");
			
			}  else {

				$response = array(
				'status' => true, 
				'msg' => "Great! This product has been added to your dashboard.");
			}
	   mysqli_close($dbcon);
  	 }
  }
	   
echo json_encode($response);
?>