<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- ############################## Start main ###################################### -->
<tr>
        <td width="10">&nbsp;</td>
	<td align="center" valign="top">
		<table border="0" cellpadding="2" cellspacing="0" width="99%">
			<tr>
				<td bgcolor="#B0C4DE" align="left" valign="top" width="49%">
					<a href="">
					<?php print _("Administration Settings"); ?>
					</a>
				</td>
				<td align="left" valign="top" width="2%">&nbsp;</td>
				<td bgcolor="#B0C4DE" align="left" valign="top" width="49%">
					<a href="index.php?action=display">
					<?php print _("Display Preferences"); ?>
					</a>
				</td>
			</tr>

			<tr>
				<td bgcolor="#CBCBCB" align="left" valign="top" width="49%">
					<?php print _("You can set all administrative settings."); ?>
				</td>
				<td align="left" valign="top" width="2%">&nbsp;</td>
				<td bgcolor="#CBCBCB" align="left" valign="top" width="49%">
					<?php print _("You can change the way that Web-cyradm displays information to you."); ?>
				</td>
			</tr>

			<tr><td align="left" valign="top" height="2%">&nbsp;</td></tr>
			
			<tr>
				<td bgcolor="#B0C4DE" align="left" valign="top" width="49%">
					<a href="index.php?action=changeadminpasswd">
					<?php print _("Change password"); ?>
					</a>
				</td>
				<td align="left" valign="top" width="2%">&nbsp;</td>
				<td bgcolor="#B0C4DE" align="left" valign="top" width="49%">
				</td>
			</tr>

			<tr>
				<td bgcolor="#CBCBCB" align="left" valign="top" width="49%">
					<?php print _("Use this to change your admin password."); ?>
				</td>
				<td align="left" valign="top" width="2%">&nbsp;</td>
				<td bgcolor="#CBCBCB" align="left" valign="top" width="49%">
				</td>
			</tr>
		</table>
	</td>
</tr>
<!-- ############################## End main ###################################### -->
