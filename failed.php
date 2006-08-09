<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));

include WC_BASE . "/config/conf.php";
include WC_BASE . "/lib/nls.php";

$browserlang=explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
if (isset($_GET['LANG'])) {
	// Use Language setting from session
	$LANG = $_GET['LANG'];
} elseif (isset($nls['aliases'][substr($browserlang[0], 0, 2)])) {
	// Get language from the browser
	$LANG = $nls['aliases'][substr($browserlang[0], 0, 2)];
} else {
	// Fall back to default language
	$LANG = $DEFAULTLANG;
	$LANG = $nls['aliases'][substr($DEFAULTLANG,0,2)];
}
$charset = isset($nls["charsets"][$LANG])?$nls["charsets"][$LANG]:'iso-8859-1';

header('Vary: Accept-Language');
header('Content-type: text/html; charset=' . $charset);
#include WC_BASE . "/header.inc.php";
setlocale(LC_MESSAGES, "$LANG");
putenv("LANG=$LANG");
putenv("LANGUAGE=$LANG");

setlocale(LC_ALL, $LANG);

// Specify location of translation tables
bindtextdomain("web-cyradm", "./locale");

// Choose domain
textdomain("web-cyradm");


?>
<html>
	<head>
		<title>Web-cyradm</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php print $charset;?>">
	</head>
	<body bgcolor="#FFFFFF" text="#000000" style="margin: 0;">
	<table width="100%" border="0" style="height: 100%;">
		<tr>
			<td align="center" valign="middle">
				<table width="450" border="0" 
				cellpadding="1" cellspacing="1"
				style="height: 150px;">
					<tr>
						<td bgcolor="#000000">
							<table border="0" bgcolor="#FFFFFF" 
							cellpadding=0 cellspacing=0 width=450 
							style="height: 150px;">
								<tr>
									<td bgcolor="#000666" 
									style="font-family: Verdana, Arial, sans-serif; color: #ffffff; font-weight: bold;">
										Web-cyradm
									</td>
								</tr>
								
								<tr>
									<td>&nbsp;</td>
								</tr>
								
								<tr>
									<td>
										<p align="center" 
										style="font-family: Verdana, Arial, sans-serif; color: #000; ">
											<?php print _("Login failed");?>
										</p>

										<p align="center"
										style="font-family: Verdana, Arial, sans-serif; color: #000; font-weight: bold;">
											<?php print _("This is only for authorized users");?>
										</p>

										<p align="center"
										style="font-family: Verdana, Arial, sans-serif; color: #000; ">
											<?php print _("If you like to login click"); ?>
											<a class="navi" href="index.php"><?php
											print _("here");
											?></a>
										</p>
									</td>
								</tr>

								<tr>
									<td>&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</body>
</html>
