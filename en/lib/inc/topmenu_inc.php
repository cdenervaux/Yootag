<?php
// Check for valid regular or Facebook session
if (isset($_SESSION['session']) || isset($_SESSION['cookie']) || isset($_SESSION['oauth'])) {

// ABSPATH constant is loaded ahead, on the page where login_inc.php is called
	echo ("<ul><li><a href=\"".ABSPATH."\\en\\dashboard\\dashboard.php\">Dashboard</a></li>");
	echo ("<li><a href=\"".ABSPATH."\\en\\lists\\lists.php\">Lists</a></li>");
	echo ("<li><a href=\"".ABSPATH."\\en\\profile\\profile.php\">Profile</a></li>");
	echo ("<li><a href=\"".ABSPATH."\\en\\user\\logout.php\">Logout</a></li></ul>");
}	

else {
echo ("<ul><li><a href=\"".ABSPATH."/en/welcome.php\">Home</a></li>
       <li><a href=\"".ABSPATH."/en/user/logreg.php\">Login</a> |
  	   <a href=\"".ABSPATH."/en/user/logreg.php\">Sign up</a></li></ul>");
}
?>
