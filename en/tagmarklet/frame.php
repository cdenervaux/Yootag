<?php
  include ("../lib/conf/config.php");
  
  // Check for URL parameter
  if (isset($_GET["add_url"])) {
     $add_url = $_GET["add_url"];
	 
	 // START SESSION CHECK //
	
	 session_start();
	 $inactive = 300;
	  
	 if (isset($_SESSION['session'])) {
		
		if (isset($_SESSION["timeout"])) {   
			$sessionTTL = time() - $_SESSION["timeout"];
			if ($sessionTTL > $inactive) {
				session_destroy();
				header ("Location: ".ABSPATH."/en/tagmarklet/logframe.php?add_url=".$add_url);
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
				  header ("Location: ".ABSPATH."/en/tagmarklet/logframe.php?add_url=".$add_url);
				  exit;
			  }
		  } 
	
	 } else {
		// Redirect to login page
		header ("Location: ".ABSPATH."/en/tagmarklet/logframe.php?add_url=".$add_url);
		exit;
	 }
	
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
 
	 // Get the product URL
 	 $parsed_url = parse_url($add_url);
	 $parsed_scheme = $parsed_url['scheme'];
	 $parsed_host = $parsed_url['host'];
	 $parsed_domain = $parsed_scheme."://".$parsed_host;
	 
	 // Check if site is supported
	 include ("../lib/db/connect_db.php");
	 $query_site = "SELECT uid, site_url, xpath_title, xpath_description, xpath_image, xpath_currency, xpath_amount, xpath_decimal FROM sites_info WHERE site_url='$parsed_domain'";
	 $result_site = mysqli_query($dbcon, $query_site) or trigger_error("A database request issue has occured", E_USER_ERROR);
	 $resultset_site = mysqli_fetch_array($result_site);
	 
	 // Only use with SELECT query
	 mysqli_free_result($result_site);
	  
	 if(!is_null($resultset_site)) {
		 
		 $site_uid = $resultset_site['uid'];
		 $xpath_title = $resultset_site['xpath_title'];
		 $xpath_description = $resultset_site['xpath_description'];
		 $xpath_image = $resultset_site['xpath_image'];
		 $xpath_currency = $resultset_site['xpath_currency'];
		 $xpath_amount = $resultset_site['xpath_amount'];
		 $xpath_decimal = $resultset_site['xpath_decimal'];
		 	  	 
		 
		 // Get the product URL content
		 $content = get_data($add_url);
		 
		 // Force UTF-8 encoding
		 $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
		 	 
		 // Parsing with XPath
		 $dom = new DOMDocument;
		 libxml_use_internal_errors(true);
		 $dom->loadHTML($content);		
		 $xpath = new DOMXPath($dom);
		 
		 
		 // Get the product title
    	 $nodes = $xpath->query($xpath_title); 		 
		 if ($nodes) {
			 foreach($nodes as $add_title) {
    			$add_title = $add_title->nodeValue;
				if (strlen($add_title) >= 110)
			 		$add_title = substr($add_title, 0, 107)."...";	
			 break;
		 	 }
		 }
		
		 // Get the product currency		 
    	 $nodes = $xpath->query($xpath_currency); 
		 if ($nodes) {		 
			 foreach($nodes as $add_currency) {
				$add_currency = $add_currency->nodeValue;
				
				// Keep only currency symbol
				$add_currency = trim(preg_replace('/[^$]/', '', $add_currency));
			 break;
			 }
		 }

		 
		 // Get the product amount
    	 $nodes = $xpath->query($xpath_amount); 		 
		 if ($nodes) {
			 foreach($nodes as $add_amount) {
				$add_amount = $add_amount->nodeValue;
				
				// Keep only amount
				$add_amount = trim(preg_replace('/[$]/', '', $add_amount));
		     break;
			 }
		 }
		 
		
		 // Get the product amount decimal if needed
    	 if (!empty($add_amount)) { 
			 if (!empty($xpath_decimal)) {
				$nodes = $xpath->query($xpath_decimal); 		 
				if ($nodes) {
					foreach($nodes as $add_decimal) {
					  $add_decimal = $add_decimal->nodeValue;
					  
					  // Keep only decimal value
					  $add_decimal = preg_replace('/[.]/', '', $add_decimal);
					  
					  // Add amount and decimal
					  $add_amount = $add_amount.'.'.$add_decimal;
					break;
				   }
			   }
		    }
		 }
		 
		 
		 // Get the product boxshot
    	 $nodes = $xpath->query($xpath_image); 	 
		 if ($nodes) {	 
			 foreach($nodes as $add_boxshot) {
				$add_boxshot = $add_boxshot->nodeValue;			 	
				
				// Check boxshot URL for missing scheme
				$parsed_boxshot_url = parse_url($add_boxshot);
				if (!isset($parsed_boxshot_url['scheme'])) {
					$add_boxshot = $parsed_domain.$add_boxshot;
				}
				
				// Check boxshot image validity				
				$image_file = get_data($add_boxshot);
		  		$im = imagecreatefromstring($image_file);
		  
		  		if($im) {
					// Check product boxshot size
					$ratio = imagesx($im)/imagesy($im);
					if( $ratio > 1) {
					  $image_width = 150;
					  $image_height = 150/$ratio;
					} else {
					  $image_width = 150*$ratio;
					  $image_height = 150;
					}	  
				}
				else {
					$add_boxshot = ABSPATH_IMG.'noimg.jpg';
					$image_width = 150;
					$image_height = 150;
				}
				
			 break;
			 }
		 }
		 
		 
		 // Get the users lists
		 include ("../lib/db/connect_db.php");
		 $usr_uid = $_SESSION['user_uid'];
		  
		 $query_list = "SELECT name FROM lists_products WHERE user_uid='$usr_uid'";
					 
		 $result_list = mysqli_query($dbcon, $query_list) or trigger_error("A database request issue has occured", E_USER_ERROR);
	     $resultset_list = mysqli_fetch_all($result_list, MYSQLI_ASSOC);
		  
		 // Only use with SELECT query
		 mysqli_free_result($result_list); 	
	     mysqli_close($dbcon);
			
		 // Check if all tags are available on site page
		 if (empty($add_title) || empty($add_currency) || empty($add_amount) || empty($add_boxshot)) {
		 	echo ('To tag a product please go to a specific product page');
			exit;
		 }
	 
	 } else {
		echo ('This site is not yet supported.');
		exit;
	 }
	 
  } else {
  	 echo ('URL is missing.');
	 exit;
  }

?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag - Tagmarklet</title>
<meta name="description" content="Yootag">
<meta name="viewport" content="width=device-width">
<link href="../lib/css/style.css" rel="stylesheet" type="text/css">
</head>

<body onload="getMainframeURL();">

<!--Opening div for content-->
<div id="frame_content" class="cf">

<a href="javascript:closeMarklet('close');" title="Close" id="frame_close"></a>

<section id="frame_section">
	<div id="frame_product">
        <label for="frame_title_label"><b><?php if ($add_title) echo ($add_title)?></b></label><br/><br/>
        <div id="frame_boxshot">	
            <img class="gen_boxshot" src="<?php if ($add_boxshot) echo ($add_boxshot)?>" height="<?php if ($image_height) echo ($image_height)?>" width="<?php if ($image_width) echo ($image_width)?>">
        </div>
        <div id="frame_detail">
            <label for="frame_price_label"><b>Price: </b></label><span id="frame_price"><?php if ($add_currency) echo ($add_currency) ?><?php if ($add_amount) echo ($add_amount)?></span><br/><br/>
            <label for="frame_alert_label"><b>Notify me when price: </b></label><br/>
            <select name="frame_alert" id="add_alert" tabindex="1" placeholder="Select a value">
              <option value="1">drops by any amount</option>
              <option value="2">drops by at least 25%</option>
              <option value="3">drops by at least 50%</option>
            </select><br/><br/>
            <label for="frame_listname_label"><b>Add to list: </b></label><br/>
            <input list="frame_listname_list" id="add_listname" placeholder="Select or create a list" tabindex="2">
            <datalist id="frame_listname_list">
            <?php if(!is_null($resultset_list)) foreach($resultset_list as $add_list_value) echo ("<option value=\"".urldecode($add_list_value['name'])."\">")?>
            </datalist><br/><br/>
            <input type="hidden" name="site_uid" id="site_uid" value="<?php if ($site_uid) echo ($site_uid)?>">
            <input type="hidden" name="add_url" id="add_url" value="<?php if ($add_url) echo ($add_url)?>">
            <input type="hidden" name="add_title" id="add_title" value="<?php if ($add_title) echo ($add_title)?>">
            <input type="hidden" name="add_boxshot" id="add_boxshot" value="<?php if ($add_boxshot) echo ($add_boxshot)?>">
            <input type="hidden" name="add_currency" id="add_currency" value="<?php if ($add_currency) echo ($add_currency)?>">
            <input type="hidden" name="add_amount" id="add_amount" value="<?php if ($add_amount) echo ($add_amount)?>">
            <input type="submit" name="add_submit_button" class="add_submit_button" value="Add to my products" tabindex="3">
        </div>
    </div>
    <div id="frame_product_result">
      <span id="frame_product_result_msg"></span><br/>
    </div>
    <span id="frame_product_result_gosite"><a href="javascript:closeMarklet('close');">Back to website</a></span>
    <span id="frame_product_result_goprod"><a href="<?php ABSPATH ?>/en/dashboard/dashboard.php" target="_blank">View my products</a></span>
</section>

</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='../lib/js/script.js'></script>
</body>
</html>