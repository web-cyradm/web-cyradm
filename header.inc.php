<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
        header("Location: index.php");
	exit();
}

include WC_BASE . "/lib/nls.php";
$charset = isset($nls["charsets"][$LANG])?$nls["charsets"][$LANG]:'iso-8859-1';

if (isset($LANG)) {
    header('Vary: Accept-Language');
    header('Content-type: text/html; charset=' . $charset);
}
$handle=DB::connect($DB['DSN'], true);
$query = "SELECT `style` from adminuser WHERE username='$user'";
$result = $handle->query($query);
$settingsrow = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
$style = $settingsrow['style'];
if (!$style){
	$style="web-cyradm";
}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<!-- #####################################  Begin header ############################################ -->
<html>
	<head>
		<title>
			web-cyradm
		</title>

<?php
//print $LANG;

/*
//print "Charset is". $nls["charsets"][$LANG];

switch ($LANG){
	case "zh_CN":
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
	break;

	case "ru_RU":
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
	break;

	case "hu_HU":
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=\"$charset\">";
	break;

	case "pl_PL":
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
	break;

	case "ro_RO":
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
	break;

	case "tr_TR":
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
	break;

	default:
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">";
}

//print "Charset is: ".$charset;
*/
?>

		<link rel="stylesheet" href="css/<?php print $style;?>.css" type="text/css">
	</head>

<?php
/*
<body bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
*/
?>
	<body style="margin: 0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height: 100%;">
		<tr>
			<td colspan="2" height="80" class="banner" bgcolor="#CCCCCC"><img 
			src="images/banner.gif" width="780" height="80" usemap="#Map" border="0"
			alt="web-cyradm" title="">
				<map name="Map">
					<area shape="rect" coords="689,2,767,15" 
					href="mailto:luc at delouw.ch"
					alt="send mail to luc at delouw.ch">
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

