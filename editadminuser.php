<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### editadminuser.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<h3>
			<?php print _("Change admin user for domain");?>
			<span style="color: red;">
				<?php
				print $domain;
				?>
			</span>
		</h3>

	<?php
	if ($admintype == 0){
		$handle1=DB::connect($DB['DSN'], true);
		$query = "SELECT * from adminuser WHERE username='$username'";
		$result = $handle1->query($query);
		$cnt = $result->numRows($result);
		$adminrow = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$type = $adminrow['type'];

		if (empty($confirmed)){
			?>
			<form action="index.php" method="post">
				<input type="hidden" name="action" value="editadminuser">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="username" value="<?php print $username;?>">
					<input type="hidden" name="domain" value="<?php print $domain;?>">
					<input type="hidden" name="nrdomains" value="<?php print $cnt;?>">
					<input type="hidden" name="row_pos" value="<?php print $row_pos;?>">
					<table>
						<tr>
							<td>
								<?php print _("Accountname");?>
							</td>

							<td>
								<?php print $username;?>
							</td>
						</tr>

						<tr>
							<td>
								<?php print _("Admin Type");?>
							</td>

							<td>
								<select class="selectfield"
								name="newtype">
									<option value="0"
									<?php
									if ($type == 0){
										echo "selected";
									}
									?>
									><?php
									print _("Superuser");
									?></option>

									<option value="1"
									<?php
									if ($type == 1){
										# This is NOT a i18n string!
										print "selected";
									}
									?>
									><?php
									print _("Domain Master");
									?></option>
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
							# Query for what domains this admin is already reponsible for
							$query = "SELECT * from domainadmin WHERE adminuser='$username'";
							$result = $handle1->query($query);
							$cnt = $result->numRows($result);

							# For Superusers there is nothing to display
							if ($type==0){
								print "<p>";
								print "<h4>";
								print _("Responsible for all domains");
								print "</h4>";
								print "</td></tr>";

							}
							else if ($type !=0 && $cnt>0){
								print "<p>";
								print "<h4>";
								print _("Responsible for the following domains:");
								print "</h4>";
								print "</td></tr><tr>";
								for ($i=0;$i<$cnt;$i++){
									$query = "SELECT * from domainadmin WHERE adminuser='$username'";
									$result = $handle1->query($query);
									$cnt = $result->numRows($result);
// 									$adminrow = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
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
							}
							else{
								print "<p><h4>";
								print _("No Domain assigned yet");
								print "</h4>";
							}

							if ($type !=0 ){


							?>
								<tr>
									<td>
									<?php print _("Add new domain to this admin");?>
									</td>
									<td><input
									class="inputfield"
									type="text"
									name="newdomain"
									value="<?print $domain?>"
									onfocus="this.style.backgroundColor='#aaaaaa'"
									></td>
								</tr>

							<?php
							} # End if type!=0
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
			elseif (! empty($confirmed)){

				# First delete and set new Domainresponsibilities

				$query = "DELETE from domainadmin WHERE adminuser='$username'";
				$result = $handle1->query($query);

				# Insert each key in the array "domain" into database again

				foreach ($resp_domain as $key => $r_domain){
					$query="INSERT INTO domainadmin (domain_name,adminuser) VALUES('$key', '$username')";
					$result=$handle1->query($query);
				}

				# If there is a new domain to add, lets insert it to the DB

				if ($newdomain){

					# Check if the domain to be added really exists

					$query="SELECT domain_name FROM domain WHERE domain_name='$newdomain'";
					$result= $handle1->query($query);
					$cnt = $result->numRows($result);

					if ($cnt==0){

						# If the domain does not exist, print error
						die(_("No such domain, create domain first"));
					}

					$query="SELECT * FROM domainadmin WHERE adminuser='$username' AND domain_name='$newdomain'";
					$result=$handle1->query($query);
					$cnt=$result->numRows($result);

					if ($cnt==1){
						die (_("Admin already repsonsible for the domain")." ".$newdomain);
					}

					# Insert if domain exists
					$query="INSERT INTO domainadmin (domain_name,adminuser) VALUES('$newdomain','$username')";
					$result=$handle1->query($query);



				}

				# If the password is empty, the password will no be changed at all, but it is not a error

				if (! empty($new_password) && $new_password == $confirm_password){
					$pwd = new password;
					$new_password = $pwd->encrypt($new_password, $CRYPT);
					# If the new_password field is not empty and the password matches, update the password
					$query = "UPDATE adminuser SET password='$new_password', type='$newtype' WHERE username='$username'";
					$result=$handle1->query($query);
				}
				elseif ($new_password != $confirm_password){
					die (_("New passwords are not equal. Password not changed"));
				}

				# The only admin user related thing we can do now is updating the type of the admin

				if ($newtype == 1){

					# We have to take care to have at least one superuser left, or we cannot use
					# Web-cyradm again

					# Query to get the count of superusers
					$query="SELECT type FROM adminuser WHERE type='0'";
					$result = $handle1->query($query);
					$cnt=$result->numRows($result);

					# Determine what kind of user is beeing edited
					$query="SELECT type FROM adminuser WHERE username='$username'";
					$result=$handle1->query($query);
					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
					$type= $row['type'];

					# Check if only 1 superuser is defined, in case of requested change of a superuser
					if ($cnt==1 && $type==0){
						# No Way! We cannot change the last Superuser to domainadmin!
						die (_("At least one Superuser is needed for Web-cyradm"));
					}
					# If not died, lets update the type
					$query="UPDATE adminuser SET type='$newtype' WHERE username='$username'";
					$result=$handle1->query($query);
				}
				if ($newtype==0){

					# If the new type of admin will be superuser, we also need to delete the domain
					# Responsibilities, because a superuser is always responsible for all domains
					$query = "DELETE FROM domainadmin WHERE adminuser='$username'";
					$result = $handle1->query($query);

					# And update the type of this admin
					$query="UPDATE adminuser SET type='$newtype' WHERE username='$username'";
					$result=$handle1->query($query);
				}

				include WC_BASE . "/adminuser.php";
			}
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
<!-- #################### editadminuser.php end #################### -->

