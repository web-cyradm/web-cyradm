<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### newadminuser.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

	<?php
	if ($authorized){
		if (empty($_POST['confirmed'])){
			?>
			<h3>
				<?php print _("Add new administrator");?>:
			</h3>
			<form action="index.php" method="post">
				<input type="hidden"
				name="action"
				value="newadminuser">

				<input
				type="hidden"
				name="confirmed"
				value="true">

				<input
				type="hidden"
				name="domain"
				value="<?php print $_GET['domain']; ?>">

				<table>
					<tr>
						<td>
							<?php print _("Accountname");?>
						</td>

						<td>
							<input
							class="inputfield"
							type="text"
							name="newadminuser"
							onfocus="this.style.backgroundColor='#aaaaaa'"
							>
						</td>
					</tr>

					<tr>
						<td>
							<?php print _("Admin Type");?>
						</td>

						<td>
							<select name="newadmintype">
								<option value="0"><?php
								print _("Superuser");
								?></option>

								<option selected value="1"><?php
								print _("Domain Master");
								?></option>
							</select> <?php
							print _("Select \"Superuser\" for all domains");
							?>
						</td>
					</tr>

					<tr>
						<td>
							<?php print _("Password") ?>
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
							<?php print _("Confirm Password");?>
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
						<td>
							<?php print _("Domain");?>
						</td>
						<td>
							<input
							class="inputfield"
							type="text"
							name="newdomain"
							value="<?print $_GET['domain']?>"
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
				<?php
			} elseif (! empty($_POST['confirmed'])){
				# Generate password for new admin
				$pwd = new password;
				$password = $pwd->encrypt($_POST['new_password'],$CRYPT);
				# Save new admin into table
				$query = "INSERT INTO adminuser (username , password , type ) VALUES ('".$_POST['newadminuser']."','".$password."','".$_POST['newadmintype']."')";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				# Save initial setup for new admin
				$query = "INSERT INTO settings (username) VALUES ('".$_POST['newadminuser']."')";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				# Save domain which new admin will be responsible for
				# If admin is superuser, he will be responsible for all domains
				if ($_POST['newadmintype'] == 0){
					$query = "INSERT INTO domainadmin (domain_name , adminuser) values ('*' , '".$_POST['newadminuser']."')";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
				}
				else if (!empty($_POST['newdomain'])){
					$query = "INSERT INTO domainadmin (domain_name , adminuser) values ('".$_POST['newdomain']."' ,'".$_POST['newadminuser']."')";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
				}
				?>
				<h3>
					<?php
						print _("successfully added to Database");
					?>
					:
					<span style="color: red;">
						<?php echo $_POST['newadminuser'];?>
					</span>
					as
					<span style="color: red;">
						<?php
						if ($_POST['newadmintype'] == 0) {
							print _("Superuser");
						} else {
							print _("Domain Master");
						}
						?>
					</span>
				</h3>
				<?php
				include WC_BASE . "/adminuser.php";
		} // End of if (empty($_POST['confirmed']))
	} else { // if ($authorized)
		?>
		<h3>
			<?php print $err_msg; ?>
		</h3>
		<?php
	} // End of if ($authorized)
	?>
	</td>
</tr>
<!-- #################### newadminuser.php end #################### -->

