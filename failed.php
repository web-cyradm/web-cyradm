<?php

include WC_BASE . "/config/conf.php";
include WC_BASE . "/lib/nls.php";

$browserlang=explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
$browserlang1=substr($browserlang[0], 0, 2);
if ($nls['aliases'][$browserlang1]){
        $LANG=$nls['aliases'][$browserlang1];
}


include WC_BASE . "/header.inc.php";

setlocale(LC_MESSAGES, "$LANG");
putenv("LANG=$LANG");
putenv("LANGUAGE=$LANG");

setlocale(LC_ALL, $LANG);

// Specify location of translation tables
bindtextdomain("web-cyradm", "./locale");

// Choose domain
textdomain("web-cyradm");

header('Content-Type: text/html; charset=iso-8859-1');

?>
<html>
	<head>
		<title>Web-cyradm</title>
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
									style="font-family: Verdana, Arial, sans-serif; color: #ffffff; font-size: large; font-weight: bold;">
										Web-cyradm
									</td>
								</tr>
								
								<tr>
									<td>&nbsp;</td>
								</tr>
								
								<tr>
									<td>
										<p align="center" 
										style="font-family: Verdana, Arial, sans-serif; color: #000; font-size: x-large;">
											<?php print _("Login failed");?>
										</p>

										<p align="center"
										style="font-family: Verdana, Arial, sans-serif; color: #000; font-size: xx-large; font-weight: bold;">
											<?php print _("This is only for authorized users");?>
										</p>

										<p align="center"
										style="font-family: Verdana, Arial, sans-serif; color: #000; font-size: x-large;">
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

<?php
include WC_BASE . "/footer.inc.php";
?>
</td></tr>

