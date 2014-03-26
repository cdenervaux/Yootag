<?php
include ("../lib/conf/config.php");
session_start();
?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Yootag - Contact</title>
<meta name="description" content="Web Development">
<meta name="viewport" content="width=device-width">
<link href="../lib/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<!--Opening div for header-->
<header id="gen_header" class="cf">

<div id="gen_header_wrapper">
  <div id="gen_logo">Yootag</div>  
  <div id="gen_navbar">
	<?php include '../lib/inc/topmenu_inc.php';?>
  </div>
</div>

</header>
<!--Closing div for header-->


<!--Opening div for content-->
<div id="gen_content" class="cf">
 
<section id="gen_section">

<form>
<br/>
<center>
  <table width="100%" border="0" bordercolor="#000000">
    <tr>
      <td width="77"><label for="name">Name</label>&nbsp;*</td>
      <td width="379"><input type="text" name="con_name" id="con_name" size="30" maxlength="45"><span id="msg_con_name"></span></td>
    </tr>
    <tr>
      <td><label for="email">Email</label>&nbsp;*</td>
      <td><input type="email" name="con_email" id="con_email" size="30" maxlength="45"><span id="msg_con_email"></span></td>
    </tr>
    <tr>
      <td><label for="enquiry">Enquiry</label>&nbsp;*</td>
      <td><select name="con_enquiry" id="con_enquiry" maxlength="45">  
            <option value="General">General</option>  
            <option value="Feature request">Feature request</option>  
            <option value="Bug report">Bug report</option>
            <option value="Other">Other</option> 
        </select>
        <span id="msg_con_enquiry"></span></td>
    </tr>
    <tr>
      <td><label for="message">Message</label>&nbsp;*</td>
      <td><textarea name="con_message" cols="30" id="con_message"></textarea><span id="msg_con_message"></span></td>
    </tr>
  </table>
</center>
<br/>
<center>
  <input type="submit" class="con_submit_button" name="con_submit_button" value="Send message"><br/><br/><span id="msg_con_sendmsg"></span>
</center>
</form>
 <br/>         
</section>

</div>
<!--Closing div for content-->

<!--Opening div for footer-->
<footer id="gen_footer">
<?php include '../lib/inc/bottommenu_inc.php';?>
</footer>
<!--Closing div for footer-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='../lib/js/script.js'></script>
</body>
</html>