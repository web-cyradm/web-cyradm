<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### Start main #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<h3>
			<?php print _("Display preferences for");?>
			<span style="color: red;">
				<?php
				print $_SESSION['user'];
				?>
			</span>
		</h3>

	<?php
	if (!isset($_GET['confirmed'])){
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
							foreach ($TEMPLATE as $style){
								print "<option";
								$_SESSION['style'] == $style?print " selected=\"selected\"":"";
								print " value=\"$style\">";
								print $style;
								print "</option>\n";
							}
						?>
						</select>
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
			$_SESSION['style'] = $_GET['style'];
		}
		else {
			print "<h3>".$err_msg."</h3>";
		}
		print "<center><a class=\"navi\" href=\"index.php?action=settings\">";
		print _("Reload page");
		print "</a></center>";
		echo "</td></tr>\n";
		include WC_BASE . "/settings.php";
	}
	?>
<!-- #################### End main #################### -->

