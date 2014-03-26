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
   
<span class="gen_msgbox"></span>

<div id="new_pass">
<?php  
  $token = trim($_GET["token"]);
  
  if (!$token == "") {
	  include ("../lib/db/connect_db.php"); 
	  $query_token = "SELECT * FROM users_site WHERE token='$token'";
			  
	  $result_token = mysqli_query($dbcon, $query_token) or trigger_error("A database request issue has occured", E_USER_ERROR);
	  $affected_rows = mysqli_num_rows($result_token);
	  
	  while($row = mysqli_fetch_row($result_token)){
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
  
			  echo("<form>
			  <label for=\"npassword\">New password</label><br/>
			  <input type=\"password\" name=\"new_password\" id=\"new_password\" size=\"25\" maxlength=\"45\" placeholder=\"New password\"><br/><span id=\"msg_new_password\"></span><br/><br/>
			  <label for=\"nmpassword\">Re-type new password</label><br/>
			  <input type=\"password\" name=\"new_matchpassword\" id=\"new_matchpassword\" size=\"25\" maxlength=\"45\" placeholder=\"Re-enter new password\"><br/><span id=\"msg_new_matchpassword\"></span><br/><br/>
			  <input type=\"submit\" class=\"new_submit_button\" name=\"new_submit_button\" value=\"Confirm\">
			  </form>");
		  
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