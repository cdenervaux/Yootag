<?php
include ("sessionchk_a.php");
include ("lib/db/connect_db.php");

$query = "SELECT url_fr, url_en, contenu_fr, contenu_en FROM site_moment WHERE uid_site=1";
$result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);

while($row = mysqli_fetch_row($result)){
	$urlfr=$row[0];
	$urlen=$row[1];
	$contenufr=$row[2];
	$contenuen=$row[3];
	}
	
mysqli_free_result($result);
mysqli_close($dbcon);
?>

<html>
<head>
<title>Administration Web Dev</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="lib/css/style.css" type="text/css">
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
          <td bgcolor=#FFFFFF colspan="3"><img src="img/pixeltrans.gif" width="1" height="2"></td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF colspan="3" height="20" valign="top" align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Modifier 
            le site du moment </b></font></td>
        </tr>
        <tr>
          <td bgcolor=#FFFFFF colspan="3" height="300" valign="top" align="left">
			  <form method="post" action="mod_site.php?id=0">
              <p align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><br>
                Url vers l'image en fran&ccedil;ais </font><br>
                <input type="text" name="url_fr" value="<?php echo $urlfr ?>" size="60">
                <br>
                <br>
                <font face="Arial, Helvetica, sans-serif" size="-1">Url vers l'image en anglais</font><br>
                <input type="text" name="url_en" value="<?php echo $urlen ?>" size="60">
</p>
              <p align="center"><font face="Arial, Helvetica, sans-serif" size="-1">Description du site en fran&ccedil;ais</font><br>
                  <textarea name="contenu_fr" value="cont_fr" cols="45" rows="5"><?php echo $contenufr ?></textarea>
                  <br>
                  <br>
                  <font face="Arial, Helvetica, sans-serif" size="-1">Description du site en anglais</font><br>
                <textarea name="contenu_en" value="cont_en" cols="45" rows="5"><?php echo $contenuen ?></textarea>
</p>
                
              <p align="center"> 
                <input type="submit" name="modifier" value="Modifier">
              </p>
            </form>
          </td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF colspan="3" height="20" valign="top" align="left"> 
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><a href="admin.php">Retour &agrave; l'accueil</a></font></div>
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
