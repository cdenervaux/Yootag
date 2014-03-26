<?php
include ("../lib/conf/config.php");
?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag</title>
<meta name="description" content="Yootag">
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
   
<span class="gen_msgbox">

<?php
$email = trim($_GET["email"]);

if ($email) {
	
	// if the email is not in the form of me@site.com it's not valid
	if (preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email)) {
	
		include ("../lib/db/connect_db.php");
		$query_email = "SELECT * FROM users_site WHERE email='$email'";
				
		$result_email = mysqli_query($dbcon, $query_email) or trigger_error("A database request issue has occured", E_USER_ERROR);
		$affected_rows = mysqli_num_rows($result_email);
	
		// Only use with SELECT query
		mysqli_free_result($result_email);
		
		// Check if email is valid
		if($affected_rows == 1) {	
			
			$token = sha1(uniqid(mt_rand(), true));
			$currentdate = date('Y-m-d H:i:s');
	
			$query_token = "UPDATE users_site SET token='$token', last_updated='$currentdate' WHERE email='$email'";
			$result_token = mysqli_query($dbcon, $query_token) or trigger_error("A database request issue has occured", E_USER_ERROR);
			
			// Confirmation email
      		$headers = "From:noreply@yootag.com"."\r\n";  
      		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";  
      		$emailbody = "<p>Hello,</p><p>Welcome to Yootag! To start using Yootag please click on the link below to confirm your email and activate your account.</p> 
            			  <p>Activation URL: <a href=\"".ABSPATH."/en/user/activate.php?token={$token}\">".ABSPATH."/en/user/activate.php?token={$token}</a></p> 
            			  <p>Thank you,</p>
						  <p>The Yootag Team</p>";
    
	  		// Send email message
	  		mail($email,"Yootag: Your Account Info",$emailbody,$headers);
			
			echo("<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;Confirmation email sent. Please check your inbox to confirm your email and activate your account.");
				
		} else {
			echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Sorry, we don't know that email address.");
		}

		mysqli_close($dbcon);
	} else {
		echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;The email format is not valid.");
	}
	
} else {
	echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;The email value is missing.");
}

?>

</span>

</section> 

</div>
<!--Closing div for content-->


<!--Opening div for footer-->
<footer id="gen_footer">
<?php include '../lib/inc/bottommenu_inc.php';?>
</footer>
<!--Closing div for footer-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='lib/js/script.js'></script>
</body>
</html>