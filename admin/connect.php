<?php
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
          <td bgcolor=#FFFFFF colspan="3" height="20" valign="top" align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b>Authentification</b></font></td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF colspan="3" height="300" valign="top" align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b><br>
            <br>
            </b></font>
            <form name="form1" method="post" action="validate_a.php">
              <font face="Arial, Helvetica, sans-serif" size="-1"><br>
              Nom d'utilisateur</font><br>
              <input name="username" type="text" maxlength="10">
              <br>
              <br>
              <font face="Arial, Helvetica, sans-serif" size="-1">Mot de passe</font><br>
              <input name="password" type="password" maxlength="12">
              <font face="Arial, Helvetica, sans-serif" size="-1"><b> <br>
              <br>
              <input type="submit" name="Submit" value="Connexion">
              </b></font> 
            </form>
            <font face="Arial, Helvetica, sans-serif" size="-1"><b><br>
            </b></font></td>
        </tr>
        <tr> 
          <td bgcolor=#FFFFFF colspan="3" height="20" valign="top" align="left">
            <div align="center"><font face="Arial, Helvetica, sans-serif" size="-1"><b><a href="<?php echo(ABSPATH."/en/welcome.php")?>">Quitter</a></b></font></div>
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
