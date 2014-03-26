<?php
include ("../lib/db/connect_db.php");
include ("../lib/conf/config.php");

$con_name = $_POST["con_name"];
$con_email = trim($_POST["con_email"]);
$con_enquiry = $_POST["con_enquiry"];
$con_message = $_POST["con_message"];
$con_ipaddress = $_SERVER['REMOTE_ADDR'];  
$response = array();

  // Anti-SQL injection - Check input fields are not empty
  if (!$con_name) {
      $response = array(
      'status' => false, 
      'msg' => "Name value is missing");
	  
  } elseif (!$con_email){
	  $response = array(
      'status' => false, 
      'msg' => "Email value is missing");
	  
  } elseif (!$con_enquiry){
	  $response = array(
      'status' => false, 
      'msg' => "Enquiry value is missing");

  } elseif (!$con_message){
	  $response = array(
      'status' => false, 
      'msg' => "Message value is missing"); 
  
  } else {
	  // Build email message
      $headers = "From:".$con_email."\r\n";  
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";  
      $emailbody = "<p>You have received a new message from the Yootag website.</p> 
                  <p><strong>Name: </strong> {$con_name} </p> 
                  <p><strong>Email Address: </strong> {$con_email} </p> 
                  <p><strong>Enquiry: </strong> {$con_enquiry} </p> 
                  <p><strong>Message: </strong> {$con_message} </p> 
                  <p>This message was sent from the IP Address: {$con_ipaddress} </p>";  
    
	  // Send email message
	  mail("cyrille.denervaux@gmail.com","New message on Yootag",$emailbody,$headers);  
 
	  $response = array(
	  'status' => true, 
	  'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;<b>Thank you. Your message has been sent</b>");
  }
   
echo json_encode($response);
?>