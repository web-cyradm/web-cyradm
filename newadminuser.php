<!-- #################### newadminuser.php start #################### -->
<tr>        
	<td width="10">&nbsp;</td>
	<td valign="top"> 

		<h3>
			<?php print _("Add new admin user for domain");?>:
			<?php 
			if (! empty($domain) && $domain != 'new'){
				?>
				<span style="color: red;">
					<?php echo $domain;?>
				</span>
				<?php
			}
			?>
			<?php
			if (empty($domain) || $domain === "new"){
				?>
				<br>
				<span style="color: red;">
					<?php print _("Please select a domain first");?>!
				</span>
				<?php
			}
			?>
		</h3>

		<?php
		if (!$domain or $domain=="new"){
			// Output moved to the h3 above
		} else {
			if ($admintype == 0){
				if (empty($confirmed)){
					?>
					<form action="index.php" method="get">
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
									name="password"
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
					$query = "SELECT * FROM adminuser WHERE username='$newadminuser'";
					$handle1 = DB::connect($DB['DSN'],true);
					if (DB::isError($handle)) {
						die (_("Database error"));
					}
					$result = $handle1->query($query);

					if (!$result->numRows()){
							$pwd = new password;
							$password = $pwd->encrypt($password,$CRYPT);
							$query = "INSERT INTO adminuser (username , password , type ) VALUES ('$newadminuser','$password','$newadmintype')";
					}

					$handle1 = DB::connect($DB['DSN'],true);
					if (DB::isError($handle)) {
						die (_("Database error"));
					}

					$result = $handle1->query($query);

					if ($newadmintype==0){
						print $newadminuser;
						$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('*' , '$newadminuser')";
					} else {
						$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('$domain' , '$newadminuser')";
					}
					$result2=$handle1->query($query2);
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
		}
		?>
	</td>
</tr>
<!-- #################### newadminuser.php end #################### -->

