<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}


if (isset($_SESSION['LANG'])){
	// Use Language setting from session
	$LANG = $_SESSION['LANG'];
}

elseif (isset($nls['aliases'][substr($browserlang[0], 0, 2)])){
	// Get language from the browser
	$LANG = $nls['aliases'][substr($browserlang[0], 0, 2)];

} 
else {
	// Fall back to default language
	//$LANG = $DEFAULTLANG;
	$LANG = $nls['aliases'][substr($DEFAULTLANG,0,2)];
}

setlocale(LC_MESSAGES, $LANG);
setlocale(LC_ALL, $LANG);
putenv("LANG=" . $LANG);
putenv("LANGUAGE=" . $LANG);

// Specify location of translation tables
bindtextdomain("web-cyradm", "./locale");

// Choose domain
textdomain("web-cyradm");

?>
	<table width="100%" border="0" style="height: 80%;">
		<tr>
			<td align="center" valign="middle"> 
				<table width="450" border="0" cellpadding="1" cellspacing="1" style="height: 150px;">
					<tr>
						<td bgcolor="#000000">		  
							<table border="0" bgcolor="#FFFFFF" cellpadding="0" 
							cellspacing="0" width="450" 
							style="height: 150px;">
								<tr> 
									<td colspan="5" bgcolor="#000666"
									style="font-family: Verdana, Arial, Helvetica, sans-serif; color: #fff; font-size: large; font-weight: bold;">
										Web-cyradm
									</td>
								</tr>
								
								<tr> 
									<td colspan="5">&nbsp;</td>
								</tr>
								
								<tr> 
									<td>&nbsp;</td>

									<td colspan="4"
									style="font-family: Verdana, Arial, Helvetica, sans-serif; color: #000; font-size: large;">
										<p align="center">
											<?php print _("Your session timed out");?>
										</p>

										<p align="center">
											<?php
											print _("If you like to login click");?>
											<a class="navi" href="index.php"
											><?php print _("here");?></a>
										</p>

										<br>
											
									</td>
								</tr>
							</table>			
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
