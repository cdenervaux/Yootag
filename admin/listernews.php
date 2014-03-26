<?php
include ("sessionchk_a.php");
include ("lib/conf/config_a.php");
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
          <td bgcolor=#FFFFFF colspan="3" height="20" valign="top" align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Liste 
            des news post&eacute;es</b></font></td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF colspan="3" height="300" valign="top" align="left"> 
            <div align="center"><br>
              <table width="600" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="75"> 
                    <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Post&eacute;e 
                      le</b></font></div>
                  </td>
                  <td width="450"> 
                    <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Titre 
                      de la news</b></font></div>
                  </td>
                  <td width="75"> 
                    <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Actions</b></font></div>
                  </td>
                </tr>
              </table>
              
            </div>
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><br> 
<?php
include ("lib/db/connect_db.php");

$query = "SELECT uid_news, titre_news_fr , DATE_FORMAT(date,'%d/%m/%Y') AS datefr FROM news ORDER BY date DESC";
$result = mysqli_query($dbcon, $query) or trigger_error("A database request issue has occured", E_USER_ERROR);

while($row = mysqli_fetch_row($result)){

	$id = $row[0];
	$titre = $row[1];
	$date=$row[2];
	
	echo "<div align=\"center\"><table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"75\"><div align=\"center\">";
	echo "<font face=\"Arial, Helvetica, sans-serif\" size=\"-1\">$date</font></div></td><td width=\"450\"><div align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"-1\">$titre</font></div></td><td width=\"75\"><div align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"-1\"><a href=\"inter_mod_news.php?id=$id\"><img src=\"".ABSPATH_IMG."mod.gif\" border=\"0\" alt=\"Modifier une news\"></a>&nbsp;&nbsp;<a href=\"javascript:\" onClick=\"cf=confirm('Voulez-vous vraiment supprimer cette news ?');if(cf)window.location='supp_news.php?id=$id'; return false;\"><img src=\"".ABSPATH_IMG."suppr.gif\" border=\"0\" alt=\"Supprimer une news\"></a></font></div></td></tr></table></div>";
}

mysqli_free_result($result);
mysqli_close($dbcon);
?>
        </font></div>
        </td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF colspan="3" height="18" valign="top" align="left"> 
            <div align="center"><font size="-1" face="Arial, Helvetica, sans-serif"><a href="admin.php">Retour 
              &agrave; l'accueil</a></font></div>
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
