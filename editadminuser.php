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
				<?php echo $domain;?>
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
				<form action="index.php" method="get">
					<input type="hidden" name="action" value="editadminuser">

					<input type="hidden" name="confirmed" value="true">

					<input type="hidden" name="username" value="<?php print $username;?>">

					<input type="hidden" name="domain" value="<?php print $domain;?>">

					<input type="hidden" name="nrdomains" value="<?php print $cnt;?>">

					<table>
						<tr>
							<td>
								<?php print _("Accountname");?>
							</td>

							<td>
								<?php echo $username;?>
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
										echo "selected";
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
							<?php
							$query = "SELECT * from domainadmin WHERE adminuser='$username'";
							$result = $handle1->query($query);
							$cnt = $result->numRows($result);

							if ($type==0){
								print "<p>";
								print "<h4>";
								print "Responsible for all domains";
								print "</h4>";
								print "</td></tr>";

							}
							else if ($type !=0 and $cnt>0){
								print "<p>";
								print "<h4>";
								print "Responsible for the following domains:";
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
							?>
							<tr>
								<td>Add new domain to this admin</td>
								<td><input type="text" name="newdomain"></td>
							</tr>


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

			# First delete and set new Domainresponsibilities

				$query = "DELETE from domainadmin WHERE adminuser='$username'";
// 				print $query."<p>";
				$result = $handle1->query($query);

				# Insert each key in the array "domain" into database again

				foreach ($resp_domain as $key => $r_domain){
					$query="INSERT INTO domainadmin (domain_name,adminuser) VALUES('$key', '$username')";
// 					print $query."<br>";
					$result=$handle1->query($query);
				}

				# If there is a new domain to add, lets insert it to the DB

				if ($newdomain){
// 					print "Newdomain=".$newdomain."<br>";

					# Check if the domain to be added really exists

					$query="SELECT domain_name FROM domain WHERE domain_name='$newdomain'";
// 					print $query."<p>";
					$result= $handle1->query($query);
					$cnt = $result->numRows($result);

						if ($cnt==0){

							# If the domain does not exist, print error

							die("No such domain");
						}
						else{

							# Insert if domain exists

							$query="INSERT INTO domainadmin (domain_name,adminuser) VALUES('$newdomain','$username')";
							$result=$handle1->query($query);
						}
				}

				if (! empty($new_password) && $new_password == $confirm_password){
						$pwd = new password;
						$new_password = $pwd->encrypt($new_password, $CRYPT);
						# If the new_password field is not
						# empty and the password matches,
						# update the password
						$query = "UPDATE adminuser SET password='$new_password', type='$newtype' WHERE username='$username'";
				} elseif ($new_password != $confirm_password){
					die (_("New passwords are not equal. Password not changed"));
				} else {
					$query="UPDATE adminuser SET type='$newtype' WHERE username='$username'";
				}

				$result=$handle1->query($query);

				if ($newtype == 0){
					$query2="UPDATE domainadmin SET domain_name='*' WHERE adminuser='$username'";
				}
				$result2=$handle1->query($query2);

				if ($result and $result2){
					print _("successfully changed Database....")."</br>";
				} else {
					print _("Database error")."<br>";
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

