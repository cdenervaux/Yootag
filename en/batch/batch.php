<?php
 //****************************************************
 // Yootag daily batch process for user products
 //****************************************************
  
 include ("../lib/conf/config.php");
  
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
	
	$result = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	$response = array(
	 'content' => $result, 
	 'code' => $code);
	 
	return $response;
 }
	 
 // Error log
 $file = 'batch_error.txt';
 $batch_error = false;
 
 // Batch timer
 $time_start = microtime(true);
 
 // Iterate through users
 include ("../lib/db/connect_db.php");
 
 $query_user = "SELECT uid, email FROM users_site WHERE activated='1'";
 $result_user = mysqli_query($dbcon, $query_user) or trigger_error("A database request issue has occured", E_USER_ERROR);
 
	while($resultset_user = mysqli_fetch_array($result_user)){
 
		$user_uid = $resultset_user['uid'];
		$user_email = $resultset_user['email'];
 
		// Get user products
		$query_prod = "SELECT uid, title, image_link, site_uid, direct_link, alert_range FROM products_info WHERE user_uid='$user_uid'";
		$result_prod = mysqli_query($dbcon, $query_prod) or trigger_error("A database request issue has occured", E_USER_ERROR);
		
		// Process each user product
		while($resultset_prod = mysqli_fetch_array($result_prod)){
		
			$prod_uid = $resultset_prod['uid'];
			$site_uid = $resultset_prod['site_uid'];
			$prod_title = urldecode($resultset_prod['title']);
			$prod_link = urldecode($resultset_prod['direct_link']);
			$prod_alert = $resultset_prod['alert_range'];
			
			// Get latest product price
			$query_price = "SELECT currency, amount FROM products_price WHERE product_uid='$prod_uid' ORDER BY created DESC LIMIT 1";
			$result_price = mysqli_query($dbcon, $query_price) or trigger_error("A database request issue has occured", E_USER_ERROR);
			$resultset_price = mysqli_fetch_array($result_price);
			mysqli_free_result($result_price);
			
			$prod_currency = $resultset_price['currency'];
			$prod_amount = $resultset_price['amount'];
			
			// Get product site
			$query_site = "SELECT name, xpath_title, xpath_currency, xpath_amount, xpath_decimal FROM sites_info WHERE uid='$site_uid'";
			$result_site = mysqli_query($dbcon, $query_site) or trigger_error("A database request issue has occured", E_USER_ERROR);
			$resultset_site = mysqli_fetch_array($result_site);
			mysqli_free_result($result_site);
			
			$site_name = $resultset_site['name'];
			$xpath_title = $resultset_site['xpath_title'];
			$xpath_currency = $resultset_site['xpath_currency'];
			$xpath_amount = $resultset_site['xpath_amount'];
			$xpath_decimal = $resultset_site['xpath_decimal'];	
			
			// Get and check the product boxshot
			$prod_boxhsot = urldecode($resultset_prod['image_link']);
			$image_file = get_data($prod_boxhsot);
			
			if ($image_file['code'] == '200') {
				$im = imagecreatefromstring($image_file['content']);
				// Handle Width
				if (imagesx($im) > 150) 
				  $image_width = 150;
				else
				  $image_width = imagesx($im);
				// Handle Height
				if (imagesy($im) > 150) 
				  $image_height = 150;
				else
				  $image_height = imagesy($im);	
			} else {
				$currentdate = date('Y-m-d H:i:s');
				file_put_contents($file, $currentdate.' - '.$site_name.' - '.'Error: Image link http error '.$image_file['code'].' - Link: '.$prod_boxhsot.PHP_EOL, FILE_APPEND | LOCK_EX);
				$batch_error = true;
				break;
			}   
		   
			// Get and check the product URL content
			$prod_file = get_data($prod_link);
		    
			if ($prod_file['code'] == '200') {
				
				// Force UTF-8 encoding
				$prod_file['content'] = mb_convert_encoding($prod_file['content'], 'HTML-ENTITIES', 'UTF-8');
				   
				// Parsing with XPath
				$dom = new DOMDocument;
				libxml_use_internal_errors(true);
				$dom->loadHTML($prod_file['content']);		
				$xpath = new DOMXPath($dom);
				
				// Get the product currency
				$nodes = $xpath->query($xpath_currency); 
				if ($nodes) {		 
				   foreach($nodes as $new_currency) {
					  $new_currency = $new_currency->nodeValue;
					  
					  // Keep only currency symbol
					  $new_currency = preg_replace('/[^$]/', '', $new_currency);
					  break;
				   }
				} else {
				   $currentdate = date('Y-m-d H:i:s');
				   file_put_contents($file, $currentdate.' - '.$site_name.' - '.'Error: Currency not found - XPath: '.$xpath_currency.PHP_EOL, FILE_APPEND | LOCK_EX);
				   $batch_error = true;
				   break;
				}
				
				// Get the product amount
				$nodes = $xpath->query($xpath_amount); 		 
				if ($nodes) {
				   foreach($nodes as $new_amount) {
					  $new_amount = $new_amount->nodeValue;
					  
					  // Keep only amount
					  $new_amount = preg_replace('/[$]/', '', $new_amount);
					  break;
				   }
				} else {
				   $currentdate = date('Y-m-d H:i:s');
				   file_put_contents($file, $currentdate.' - '.$site_name.' - '.'Error: Amount not found - XPath: '.$xpath_amount.PHP_EOL, FILE_APPEND | LOCK_EX);
				   $batch_error = true;
				   break;
				}
				  
				// Get the product amount decimal if needed
				if (!empty($new_amount)) { 
				   if (!empty($xpath_decimal)) {
					  $nodes = $xpath->query($xpath_decimal); 		 
					  if ($nodes) {
						  foreach($nodes as $new_decimal) {
							$new_decimal = $new_decimal->nodeValue;
							
							// Keep only decimal value
							$new_decimal = preg_replace('/[.]/', '', $new_decimal);
							
							// Add amount and decimal
							$new_amount = $new_amount.'.'.$new_decimal;
							break;
						 }
					 } else {
						$currentdate = date('Y-m-d H:i:s');
						file_put_contents($file, $currentdate.' - '.$site_name.' - '.'Error: Decimal not found - XPath: '.$xpath_decimal.PHP_EOL, FILE_APPEND | LOCK_EX);
						$batch_error = true;
						break;
					 }
					  
				   }
				}
			   
				// Compare latest product and new price
				$sendalert = false;
				
				// Price drop by any amount
				if ($prod_alert == '1') {
					if ($prod_amount < $new_amount)
					$sendalert = true;
					$prod_save = $prod_amount - $new_amount;
				}
	
				// Price drop by 25%
				elseif ($prod_alert == '2') {
					$prod_calc = $prod_amount - $prod_amount*0.25;
					
					if ($new_amount <= $prod_calc) {
					$sendalert = true;
					$prod_save = $prod_amount - $new_amount;
					}
				}
				
				// Price drop by 50%
				elseif ($prod_alert == '3') {
					$prod_calc = $prod_amount - $prod_amount*0.5;
					
					if ($new_amount <= $prod_calc) {
					$sendalert = true;
					$prod_save = $prod_amount - $new_amount;
					}
				}
				  
				// Send email alert
				if ($sendalert) {	
					$headers = "From:noreply@yootag.com"."\r\n";  
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";  
					$emailbody = "<p>Hello,</p><p>One of your product price has just dropped:</p> 
								  <p><b>{$prod_title} @ {$site_name}</b></p>
								  <p><img src=\"{$prod_boxhsot}\" height=\"{$image_height}\" width =\"{$image_width}\"></p>
								  <p><b>Was:</b> {$new_currency}{$prod_amount} <b>Now:</b> {$new_currency}{$new_amount} (<b>You save:</b> {$new_currency}{$prod_save})</p>
								  <p><a href=\"{$prod_link}\" target=\"_blank\">Buy Now</a></p>
								  <p>The Yootag Team</p>";
		
					// Send email message
					mail($user_email,"Yootag: One of your product price has just dropped!",$emailbody,$headers);
				}
			
			} else {
				$currentdate = date('Y-m-d H:i:s');
				file_put_contents($file, $currentdate.' - '.$site_name.' - '.'Error: Product link http error '.$prod_file['code'].' - Link: '.$prod_link.PHP_EOL, FILE_APPEND | LOCK_EX);
				$batch_error = true;
				break;
			}
			
		} // end while
	
		mysqli_free_result($result_prod);
		
	} // end while
	
	mysqli_free_result($result_user);
	
	// End timer
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	// Print out batch report
	if (!$batch_error) {
		echo('Batch execution successful. Completed in '.round($time,4).' seconds');
	} else {
		echo('Batch has encountered some errors. Completed in '.round($time,4).' seconds');
	}
?>