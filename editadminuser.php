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
			$adminrow = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$type = $adminrow['type'];

			if (empty($confirmed)){
				?>
				<form action="index.php" method="get">
					<input type="hidden"
					name="action"
					value="editadminuser">
					
					<input type="hidden"
					name="confirmed"
					value="true">
					
					<input type="hidden"
					name="username"
					value="<?php print $username;?>"
					>
					
					<input type="hidden"
					name="domain"
					value="<?php print $domain;?>"
					>

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
				if (! empty($new_password) && $new_password == $confirm_password){
					switch($CRYPT){
					case "1":
					case "crypt":
						$pwd = new password;
						$new_password = $pwd->encrypt($new_password, $CRYPT);
						# If the new_password field is not 
						# empty and the password matches, 
						# update the password
						$query = "UPDATE adminuser SET password='$new_password', type='$newtype' WHERE username='$username'";		
						break;

					case "2":
					case "sql":
					case "mysql":
						$query = "UPDATE adminuser SET password=PASSWORD('$new_password'), type='$newtype' WHERE username='$username'";
						break;

					case "plain":
						$query = "UPDATE adminuser SET password='$new_password', type='$newtype' WHERE username='$username'";
						break;
					}	
				} elseif ($new_password != $confirm_password){
					die (_("New passwords are not equal. Password not changed"));
				} else {
					$query="UPDATE adminuser SET type='$newtype' WHERE username='$username'";
				}

				$result=$handle1->query($query);

				if ($newtype == 0){
					$query2="UPDATE domainadmin SET domain_name='*' WHERE adminuser='$username'";
				} else {
					$query2="UPDATE domainadmin SET domain_name='$domain' WHERE adminuser='$username'";
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

