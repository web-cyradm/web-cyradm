<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### editadminuser.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

	<?php
	if ($authorized){
		if (empty($_POST['confirmed'])) {
			$query = "SELECT `type` from adminuser WHERE username='".$_GET['username']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$type = $row['type'];
			?>
			<h3>
				<?php print _("Change admin user for domain");?>
				<span style="color: red;">
					<?php print $_GET['domain']; ?>
				</span>
			</h3>
			<form action="index.php" method="post">
				<input type="hidden" name="action" value="editadminuser">
				<input type="hidden" name="confirmed" value="true">
				<input type="hidden" name="username" value="<?php print $_GET['username'];?>">
				<input type="hidden" name="domain" value="<?php print $_GET['domain'];?>">
				<table>
					<tr>
						<td>
							<?php print _("Accountname");?>
						</td>

						<td>
							<?php print $_GET['username'];?>
						</td>
						</tr>

						<tr>
							<td>
								<?php print _("Admin Type");?>
							</td>

							<td>
								<select class="selectfield" name="newtype">
									<option value="0"
									<?php
									if ($type == 0) {
										# This is NOT a i18n string!
										echo "selected";
									}
									?>
									><?php print _("Superuser");?>
									</option>

									<option value="1"
									<?php
									if ($type == 1) {
										# This is NOT a i18n string!
										echo "selected";
									}
									?>
									><?php print _("Domain Master"); ?>
									</option>
								</select>
							</td>
						</tr>

						<tr>
							<td>
								<?php print _("Password");?>
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
							<td>
							<?php
							# For Superusers there is nothing to display
							if ($type == 0) {
								print "<p>";
								print "<h4>";
								print _("Responsible for all domains");
								print "</h4>";
							} else {
								# Query for what domains this admin is already reponsible for
								$query = "SELECT * FROM domainadmin WHERE adminuser='".$_GET['username']."'";
								$result = $handle->query($query);
								if (DB::isError($result)) {
									die (_("Database error"));
								}
								$cnt = $result->numRows();

								if ($cnt > 0) {
									print "<p>";
									print "<h4>";
									print _("Responsible for the following domains:");
									print "</h4>";
									print "</td></tr><tr>";
									for ($i=0; $i<$cnt; $i++) {
										$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $i);
										$resp_domain = $row['domain_name'];
										print "<td>";
										print $resp_domain;
										print "</td>";
										print "<td>";
										print "<input type=\"checkbox\" name=\"resp_domain[$resp_domain]\" checked>";
										print "</td>";
										print "</tr>";
									}
								} else {
									print "<p><h4>";
									print _("No Domain assigned yet");
									print "</h4>";
									print "</td></tr>";
								}
							?>
								<tr>
									<td>
									<?php print _("Add new domain to this admin");?>
									</td>
									<td><input
									class="inputfield"
									type="text"
									name="newdomain"
									value="<?print $_GET['domain'];?>"
									onfocus="this.style.backgroundColor='#aaaaaa'">
							<?php
							} # End of if ($type == 0)
							?>
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
		elseif (!empty($_POST['confirmed'])){
				$query = "SELECT `type` from adminuser WHERE username='".$_POST['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$type = $row['type'];

				if ($type != $_POST['newtype'] || $type = '1') {
					# First delete and set new Domainresponsibilities
					$query = "DELETE from domainadmin WHERE adminuser='".$_POST['username']."'";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
				}
				
				# First admin user related thing we can do is updating the type of the admin
				if ($type != $_POST['newtype']) {
					# Update the type of this admin
					$query = "UPDATE adminuser SET type='".$_POST['newtype']."' WHERE username='".$_POST['username']."'";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
					
					if ($_POST['newtype']==0){
						# Set responsibility for all domains to superuser
						$query = "INSERT INTO domainadmin (domain_name,adminuser) VALUES ('*','".$_POST['username']."')";
  						$result = $handle->query($query);
						if (DB::isError($result)) {
							die (_("Database error"));
						}
					} else {
						# no responsibility is set for domainadmins by default
					}
				} else { # if admin type remain the same
					# Insert each key in the array "domain" into database again
					if (!empty($_POST['resp_domain'])) {
						foreach ($_POST['resp_domain'] as $key => $r_domain){
							$query = "INSERT INTO domainadmin (domain_name,adminuser) VALUES('$key', '".$_POST['username']."')";
							$result = $handle->query($query);
							if (DB::isError($result)) {
								die (_("Database error"));
							}
						}
					}

					# If there is a new domain to add, lets insert it to the DB
					if (!empty($_POST['newdomain'])){
						$query = "INSERT INTO domainadmin (domain_name,adminuser) VALUES('".$_POST['newdomain']."','".$_POST['username']."')";
						$result = $handle->query($query);
						if (DB::isError($result)) {
							die (_("Database error"));
						}
					}
				}

				# If the password is empty, the password will not be changed at all, but it is not a error
				if (!empty($_POST['new_password'])) {
					$pwd = new password;
					$new_password = $pwd->encrypt($_POST['new_password'], $CRYPT);
					# If the new_password field is not empty and the password matches, update the password
					$query = "UPDATE adminuser SET password='".$new_password."' WHERE username='".$_POST['username']."'";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
				}
				$_GET['domain'] = $_POST['domain'];
				include WC_BASE . "/adminuser.php";
		} // End of if (empty($_POST['confirmed']))
	} else { // if ($authorized)
		?>
		<h3>
			<?php print $err_msg;?>
		</h3>
		<?php
	} // End of if ($authorized)
	?>
	</td>
</tr>
<!-- #################### editadminuser.php end #################### -->

