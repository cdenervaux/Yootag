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
  $token = trim($_GET["token"]);
  
  if (!$token == "") {
	  include ("../lib/db/connect_db.php");
	  $query_token = "SELECT * FROM users_site WHERE token='$token'";
			  
	  $result_token = mysqli_query($dbcon, $query_token) or trigger_error("A database request issue has occured", E_USER_ERROR);
	  $affected_rows = mysqli_num_rows($result_token);
	  
	  while($row = mysqli_fetch_row($result_token)){  // NEED TO BE UPDATED
	  $activated = $row[4];
	  $lastupdated = $row[6];
	  }
	  
	  // Only use with SELECT query
	  mysqli_free_result($result_token);
	  
	  // Check if token is valid
	  if($affected_rows == 1) {	
		  
		  $currentdate = new DateTime(date('Y-m-d H:i:s'));
		  $lastupdated = new DateTime($lastupdated);
		  $interval = $lastupdated->diff($currentdate);
		  
		  // Check if token has expired (1h validity)
		  if ($interval->h < 1) {
  
			  // Check if account is already activated
			  if (!$activated == 1) {
				  
				  $query_active = "UPDATE users_site SET activated='1' WHERE token='$token'";
				  $result_active = mysqli_query($dbcon, $query_active) or trigger_error("A database request issue has occured", E_USER_ERROR);
				  echo ("Thank you for confirming your email address. Your account is now active and you can <a href=\"logreg.php\">login</a>.");
			  
			  } else {
				 echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;This account has already been activated. <a href=\"logreg.php\">Login</a>.");
			  }
			  
		  } else {
		  echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Token has expired.");
		  }
	  
	  } else {
		  echo ("<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;Token is invalid.");
	  }
	  
	  mysqli_close($dbcon);
		  
  } else {
  echo ("Token is missing.");
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