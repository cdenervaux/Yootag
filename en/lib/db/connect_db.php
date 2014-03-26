<?php
// MySQL database connection variables
$host = "dev.yootag.com";
$user = "frenchyus";
$pass = "Cdn160176";
$bdd = "frenchyus";

// MySQLi connection to the database 
$dbcon = mysqli_connect($host, $user, $pass, $bdd);

if (!$dbcon) {
    printf("Can't connect to database ".$bdd." on host ".$host." Error: %s\n", mysqli_connect_error());
    exit();
}
?>
