<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### newadminuser.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

	<h3>
		<?php print _("Add new administrator");?>:</h3>
	<?php
	if ($admintype == 0){
		if (empty($confirmed)){
			?>
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
				value="<?php print $domain; ?>">

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
							value="<?print $domain?>"
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
			} elseif (! empty($confirmed)){

				################### Begin admin USER checks and INSERT ####################

				# Username most not be empty
				if (empty($newadminuser)){
					die (_("You must provide a username"));
				}

				$handle1 = DB::connect($DB['DSN'],true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				# Check if the username already exists
				$query = "SELECT * FROM adminuser WHERE username='$newadminuser'";
				$result = $handle1->query($query);

				if (empty($new_password) || $new_password != $confirm_password){
					die (_("Passwords are empty or not equal."));
				}

				if (!$result->numRows()){
					# If the username does not exist, then insert the adminuser table
					$pwd = new password;
					$password = $pwd->encrypt($new_password,$CRYPT);
					$query = "INSERT INTO adminuser (username , password , type ) VALUES ('$newadminuser','$password','$newadmintype')";
					$result=$handle1->query($query);
				}
				else {
					# It is not a new admin, so lets die
					die(_("Username already exist"));
				}

				#################### End admin USER checks and INSERT ###########################

				#################### Begin domain name checks and INSERT ########################

				if ($newdomain){

					# Check if the domain to be added really exists

					$query="SELECT domain_name FROM domain WHERE domain_name='$newdomain'";
					$result= $handle1->query($query);
					$cnt = $result->numRows($result);

					if ($cnt==0){

						# If the domain does not exist, print error

						die("No such domain");
					}
				}

				if ($newadmintype==0 AND !empty($newdomain)){
					print $newadminuser;
					$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('*' , '$newadminuser')";
					$result2=$handle1->query($query2);
				}
				else if ($newadmintype==1 AND !empty($newdomain)){
					$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('$newdomain' ,'$newadminuser')";
					$result2=$handle1->query($query2);
				}

				################### End domain name checks and INSERTS ##############################

				?>

				<h3>
					<?php
					if (!DB::isError($result)){
						print _("successfully added to Database");
						?>
						:
						<span style="color: red;">
							<?php echo $newadminuser;?>
						</span>
						as
						<span style="color: red;">
							<?php
							if ($newadmintype==0){
								print _("Superuser");
							} else {
								print _("Domain Master");
							}
							?>
						</span>
						<?php
					} else {
						print _("Database error");
					}
					?>
				</h3>

				<?php
				include WC_BASE . "/adminuser.php";
		} // End of if (empty($confirmed)) .. elseif (! empty($confirmed))
	} elseif ($admintype != 0){
		?>
		<h3>
		<?php print _("Security violation detected, nothing deleted, attempt has been logged");?>
		</h3>
		<?php
	}

	?>
	</td>
</tr>
<!-- #################### newadminuser.php end #################### -->

