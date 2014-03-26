<?php
include ("../lib/conf/config.php");
include ("../lib/api/fb/config.php");

// Resume session
session_start(); 

// Delete Remember me cookie
if (isset($_COOKIE['yrme'])) {
	setcookie("yrme", "", time()-3600, "/", "dev.yootag.com");
}

// Delete Facebook cookie
if (isset($_COOKIE['fbsr_'.$fbconfig['appid']])) {
    
	include ("../lib/api/fb/facebook.php");
	
	// Facebook application instance 
	$facebook = new Facebook(array(
	'appId'  => $fbconfig['appid'],
	'secret' => $fbconfig['secret'], 
	'cookie' => true,
	));
	
	$token = $facebook->getAccessToken();    
	$url = 'https://www.facebook.com/logout.php?next=' . ABSPATH.'/en/user/logreg.php&access_token='.$token;
	
	$_SESSION = array();
	session_destroy();
	
	setcookie("PHPSESSID", "",time()-3600, "/", "dev.yootag.com"); 
	setcookie("fbsr_".$fbconfig['appid'], "", time()-3600, "/", "dev.yootag.com");
	   
	header('Location: '.$url);
	exit;
}

// Destroy session
$_SESSION = array();
session_destroy();

// Redirect to login page
header ("Location: ".ABSPATH."/en/user/logreg.php");
?> 
