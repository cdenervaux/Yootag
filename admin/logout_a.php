<?php
include ("lib/conf/config_a.php");

// D�log et fin de session
session_start(); 
//session_unset(); 
session_destroy();

// Redirection vers le site
header ("Location: ".ABSPATH."/admin/connect.php");
?> 