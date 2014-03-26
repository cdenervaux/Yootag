<?php
  // Session checking process for NON-AJAX request
  session_start();
  $inactive = 300;
  
  if (isset($_SESSION['a_valid'])) {
	  
	  if (isset($_SESSION["a_timeout"])) {   
		  $sessionTTL = time() - $_SESSION["a_timeout"];
		  if ($sessionTTL > $inactive) {
			  session_destroy();
			  header ("Location: connect.php");
			  exit;
		  }
	  }
	  $_SESSION["a_timeout"] = time();
  
  } else {
	  header ("Location: connect.php");
	  exit;
  }
?> 