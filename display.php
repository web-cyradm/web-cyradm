<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### Start display #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
	<?php
	if (!isset($_GET['confirmed'])){
		?>
		<h3>
			<?php print _("Display preferences for");?>
			<span style="color: red;">
				<?php
				print $_SESSION['user'];
				?>
			</span>
		</h3>
		<?php
			if (!$TEMPLATE){
				die(_("No template definitions found, please check your config file"));
			}
		?>

		<form action="index.php" method="get">
			<input type="hidden" name="action" value="display">
			<input type="hidden" name="confirmed" value="true">
			<table>
				<tr>
					<td>
						<?php print _("Color scheme");?>
					</td>

					<td>
						<select size="1" name="style">
						<?php
							foreach ($TEMPLATE as $temp){
								print "<option";
								if ($_SESSION['style'] == $temp) {
									echo " selected";
								}
								print " value=\"$temp\">";
								print $temp;
								print "</option>\n";
							}
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php print _("Number of domains displayed on page");?>
					</td>

					<td>
						<input
						class="inputfield"
						type="text"
						name="maxdisplay"
						size="4"
						value="<?php print $_SESSION['maxdisplay'];?>"
						>
					</td>
				</tr>

				<tr>
					<td>
						<?php print _("Number of accounts displayed on page");?>
					</td>
					
					<td>
						<input
						class="inputfield"
						type="text"
						name="account_maxdisplay"
						size="4"
						value="<?php print $_SESSION['account_maxdisplay'];?>"
						>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php print _("Quota usage warn level");?>
					</td>

					<td>
						<input
						class="inputfield"
						type="text"
						name="warnlevel";
						size="4"
						value="<?php print $_SESSION['warnlevel'];?>"
						>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" align="center">
						<input
						class="inputfield"
						type="submit"
						value="<?php print _("Submit"); ?>"
						>
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
	else { // if (!isset($_GET['confirmed']))
		if ($authorized){
			if ($_SESSION['style'] != $_GET['style']) {
				print "<center><a class=\"navi\" href=\"index.php?action=settings\">";
				print _("Reload page");
				print "</a></center>";
			}
			$_SESSION['style'] = $_GET['style'];
			$_SESSION['maxdisplay'] = $_GET['maxdisplay'];
			$_SESSION['domain_row_pos'] = 0;
			$_SESSION['account_maxdisplay'] = $_GET['account_maxdisplay'];
			$_SESSION['account_row_pos'] = 0;
			$_SESSION['warnlevel'] = $_GET['warnlevel'];
			$query = "UPDATE `settings` SET `style`='".$_SESSION['style']."', maxdisplay='".$_SESSION['maxdisplay']."', warnlevel='".$_SESSION['warnlevel']."' WHERE username='".$_SESSION['user']."'";
			$handle->query($query);
		}
		else {
			print "<h3>".$err_msg."</h3>";
		}
		echo "</td></tr>\n";
		include WC_BASE . "/settings.php";
	}
	?>
<!-- #################### End display #################### -->

