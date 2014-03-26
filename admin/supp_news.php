<?php
include ("lib/db/connect_db.php");
include ("lib/conf/config_a.php");

$id = $_GET["id"];
$response = array();

$query = "DELETE FROM news WHERE uid_news=$id";
$result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);

if (!$result) {
	$response = array(
	'status' => false, 
	'msg' => "<img src=\"".ABSPATH_IMG."exclamation_red_frame.png\">&nbsp;<b>Error deleting news. Please try again later</b>");
		
}  else {
	$response = array(
	'status' => true, 
	'msg' => "<img src=\"".ABSPATH_IMG."tick_circle_frame.png\">&nbsp;<b>News deleted successfully</b>");
}

mysqli_close($dbcon);
?>

<html>
<head>
<title>Administration Web Dev</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV=Refresh CONTENT="2; URL=listernews.php"> 
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#000000">
<br>
<table cellspacing=0 cellpadding=0 width=750 border=0 align="center">
  <tbody> 
  <tr> 
    <td valign=top align=left height="2"> 
      <table cellspacing=0 cellpadding=0 width=750 
            border=0>
        <tbody> 
        <tr> 
          <td width="20" height="2"><img height=18 src="img/angle_haut_gauche.gif" width=20 border=0></td>
          <td height=18 width="725" bgcolor="#D9DEE1" valign="middle" align="left" background="img/fond_barreboite_haut.gif"><font face="Arial, Helvetica" size=1><b><font color="#000000" face="Arial, Helvetica, sans-serif" size="-1">Administration du site Web Dev</font></b></font></td>
          <td valign=top align=left width="10" height="2"><img src="img/angle_haut_droit.gif" width="20" height="18"></td>
        </tr>
        </tbody> 
      </table>
    </td>
  </tr>
  <tr> 
    <td valign=top align=left height=137 bgcolor="#000000"> 
      <table cellspacing=0 cellpadding=3 width=748 align=center 
              border=0>
        <tbody> 
        <tr> 
          <td bgcolor=#FFFFFF><img src="img/pixeltrans.gif" width="1" height="2"></td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF height="340" valign="middle" align="center"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Votre 
              news a &eacute;t&eacute; supprim&eacute;e avec succ&egrave;s !</b><br>
              <br>
              <br>
              <br>
              Redirection automatique. Veuillez patienter...</font></div>
          </td>
        </tr>
        </tbody> 
      </table>
      <table cellspacing=0 cellpadding=0 width=750 border=0 height="8">
        <tbody> 
        <tr> 
          <td width="20" height="2" bgcolor="#FFFFFF" valign="top" align="left"><img src="img/angle_gauche_bas.gif" width="20"></td>
          <td valign="bottom" align="left" width="430" bgcolor="#FFFFFF" height="1"><img src="img/pixel_noir.gif" width="710" height="1"></td>
          <td valign=top align=right width="10" height="2" bgcolor="#FFFFFF"><img src="img/angle_droit_bas.gif" width="20"></td>
        </tr>
        </tbody> 
      </table>
    </td>
  </tr>
  </tbody> 
</table>
</body>
</html>