<?php
    // User authentication
	include ("lib/db/connect_db.php");
	
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	
	$salt = mb_strtolower($username);
	$crypted = sha1($salt.$password);
	
	$query = "SELECT * FROM users_admin WHERE user_name='$username' AND password='$crypted'";   // REPLACE USERNAME WITH EMAIL FOLLOWING DB CHANGES       
    $result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);
    $affected_rows = mysqli_num_rows($result);
	// Only use with SELECT query
	mysqli_free_result($result);

    if($affected_rows == 1) {
        // User successfully authenticated
		session_start();
		session_regenerate_id(true);
		ini_set('session.cookie_httponly', true);
		$_SESSION['a_valid'] = true;
		$_SESSION['a_username'] = $username;
		  
		// Token creation
		$token = sha1(uniqid(mt_rand(), true));
		$_SESSION['token'] = $token;
		$query = "UPDATE users_admin SET token='$token' WHERE user_name='$username' AND password='$crypted'";
		$result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);
		
		// Redirection to admin page
		header ("Location: admin.php");
    }
    else {
        // Redirection in case of failure
		header ("Location: connect.php");
	}
	mysqli_close($dbcon);
?> 