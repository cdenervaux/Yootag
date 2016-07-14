<?php
// MySQL database connection variables
$host = "";
$user = "";
$pass = "";
$bdd = "";

// MySQLi connection to the database 
$dbcon = mysqli_connect($host, $user, $pass, $bdd);

if (!$dbcon) {
    printf("Can't connect to database ".$bdd." on host ".$host." Error: %s\n", mysqli_connect_error());
    exit();
}
?>
