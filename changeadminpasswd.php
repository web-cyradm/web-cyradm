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
	<?php
	if (!isset($_POST['confirmed'])){
		?>
		<h3>
			<?php print _("Change password for account");?>
			<span style="color: red;">
				<?php print $_SESSION['user'];?>
			</span>
		</h3>
		<form action="index.php" method="post">
			<input type="hidden" name="action" value="changeadminpasswd">
			<input type="hidden" name="confirmed" value="true">
			<table>
				<tr>
					<td>
						<?php print _("Old Password");?>
					</td>

					<td>
						<input
						class="inputfield"
						type="password"
						name="old_password"
						onfocus="this.style.backgroundColor='#aaaaaa'"
						>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php print _("New Password");?>
					</td>

					<td>
						<input
						class="inputfield"
						type="password"
						name="new_password"
						onfocus="this.style.backgroundColor='#aaaaaa'"
						>
					</td>
				</tr>

				<tr>
					<td>
						<?php
						print _("Confirm Password");
						?>
					</td>

					<td>
						<input
						class="inputfield"
						type="password"
						name="confirm_password"
						onfocus="this.style.backgroundColor='#aaaaaa'"
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
	</td>
</tr>
		<?php
	}
	else { // if (!isset($_POST['confirmed']))
		if ($authorized){
			$pwd = new password;
			$new_password = $pwd->encrypt($_POST['new_password'], $CRYPT);
			# If the new_password field is not empty and the password matches, update the password
			$query = "UPDATE adminuser SET password='".$new_password."' WHERE username='".$_SESSION['user']."'";
			$result =& $handle->query($query);
			if (DB::isError($result) || $handle->affectedRows()==0) {
				print _("Database error");
				echo ".&nbsp;";
				print _("Password not changed");
			} else {
				print _("Password successfully changed");
			}
		}
		else {
			print "<h3>".$err_msg."</h3>";
		}
		echo "</td></tr>\n";
		include WC_BASE . "/settings.php";
	}
	?>
<!-- #################### End main #################### -->

