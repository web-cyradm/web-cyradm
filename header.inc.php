<?php
session_start();

$sess_timeout = $SESS_TIMEOUT; // seconds
if (!isset($first)) $first = 1;
$newid = time();
if (($newid > $oldid+$sess_timeout) & !$first) {

  $session_ok = FALSE;
  session_register("session_ok");
  $first = 0;
  session_register("first");
  $oldid = $newid;
  session_register("oldid");

  Header("Location: $base_url/");
}
else {
  $first = 0;
  session_register("first");
  $oldid = $newid;
  session_register("oldid");

  if (!$session_ok) {
	header ("Location: timeout.php");
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- #####################################  Begin header ############################################ -->

<html>
<head>
<title>web-cyradm</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META content="MSHTML 5.00.2920.0" name=GENERATOR>
<link rel="stylesheet" href="css/web-cyradm.css" type="text/css">
</head>
<body bgColor=#ffffff leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
 <tr>
	<td colspan="2" height="80" class="banner" bgcolor="#CCCCCC"><img src="images/banner.gif" width="780" height="80" usemap="#Map" border="0">
	  <map name="Map">
	    <area shape="rect" coords="689,2,767,15" href="mailto:luc at delouw.ch">
	</map>
	  </td>
  </tr>
  <tr>
	<td width="10">&nbsp;</td>
	<td valign="middle" height="45">
<!--  <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> -->
<!-- ########################################## End header ############################################## -->
