<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### newaccount.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<h3>
			<?php print _("Add new Account to domain");?>:
			<span style="color: red;">
				<?php echo $domain;?>
			</span>
		</h3>

		<?php
		require_once WC_BASE . '/config/conf.php';

		$query1 = "SELECT * from domain WHERE domain_name='$domain'";

		$handle = DB::connect($DB['DSN'], true);
		if (DB::isError($handle)) {
			die (_("Database error"));
		}

		$result1 = $handle->query($query1);

		$row = $result1->fetchRow(DB_FETCHMODE_ORDERED, 0);

		$prefix		= $row[1];
		$maxaccounts	= $row[2];
		$def_quota	= $row[3];
		$transport	= $row[4];
		// START Andreas Kreisl : freenames
		$freenames	= $row[5];
		// END Andreas Kreisl : freenames
		$freeaddress    = $row[6];

		if ($transport != "cyrus"){
			die (_("transport is not cyrus, unable to create account"));
		}

		if (empty($confirmed)){

			$query2 	= "SELECT * FROM accountuser WHERE prefix='$prefix' order by username";
			$result2	= $handle->query($query2);
			$cnt2		= $result2->numRows($result2);

			if ($cnt2+1 > $maxaccounts){
				?>
				<h3>
					<?php print _("Sorry, no more account allowed for domain");?>
					<span style="color: red;">
						<?php echo $domain;?>
					</span>
					<br>
					<?php print _("Maximum allowed accounts is");?>
					<span style="font-weight: bolder;">
						<?php echo $maxaccounts;?>
					</span>
				<?php
			} else {
				?>
				<p>
					<?php print _("Total accounts") . ": " . $cnt2;?>
				</p>
				<?php

				if (!$DOMAIN_AS_PREFIX){
					// START Andreas Kreisl : freenames
					if ($freenames=="YES"){
						$lastaccount = sprintf("%04d",$cnt2);
						$lastaccount = $prefix . $lastaccount;
					} else {
						$lastaccount = $prefix."0000";
						if ($cnt2 > 0){
							$row2 = $result2->fetchRow(DB_FETCHMODE_ORDERED, $cnt2 - 1);
							// $row2 = $result2->fetchRow($result2,$cnt2-1,'username');
							// $lastaccount=mysql_result($result2,$cnt2-1,"username");
							$lastaccount = $row2[0];
						}
					}
					// END Andreas Kreisl : freenames

					$test = ereg ("[0-9][0-9][0-9][0-9]$", $lastaccount, $result_array);
					$next = $result_array[0] + 1;

					$nextaccount = sprintf("%04d",$next);
					$nextaccount = $prefix.$nextaccount;
				}
				?>
				<form action="index.php" method="POST" style="border: ridge 0px maroon;">
					<input type="hidden" name="action" value="newaccount">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php print $domain ?>">

					<table>
						<?php
						if (!$DOMAIN_AS_PREFIX){
							?>
							<tr>
								<td>
									<?php print _("Accountname");?>
								</td>

								<!-- START Andreas Kreisl : freenames -->
								<td>
									<?php
									if ($freenames == "YES"){
										$_type = 'text';
										$_disp = '';
									} else {
										$_type = 'hidden';
										$_disp = $nextaccount;
									}
									?>
									<input
									<?php
									echo ($_type === 'hidden')?(''):('class="inputfield"');
									?>
									type="<?php echo $_type;?>"
									name="username"
									value="<?php echo $nextaccount;?>"
									onfocus="this.style.backgroundColor='#aaaaaa';"
									><?php echo $_disp;?>
								</td>
								<!-- END Andreas Kreisl : freenames -->
							</tr>
							<?php
						} // End of if (!$DOMAIN_AS_PREFIX)

						$_fields = array(
							'email'	=> array(_("Email address"), 'a', false, '@' . $domain),
							'quota' => array(_("Quota"), '8', false, '', $def_quota),
							'password' => array(_("Password"), 'c', true, ''),
							'confirm_password' => array(_("Confirm Password"), 'c', true, '')
						);

						foreach ($_fields as $_name => $_def){
							?>
								<tr>
									<td>
										<?php echo $_def[0];?>
									</td>

									<td>
										<input
										class="inputfield"
										type="<?php echo ($_def[2])?('password'):('text'); ?>"
										name="<?php echo $_name; ?>" onfocus="this.style.backgroundColor='#<?php echo str_repeat($_def[1], 6); ?>'"
										<?php
										echo (isset($_def[4]))?('value="' . $_def[4] . '"'):('');
										?>
										><?php
										echo $_def[3];
										?>
									</td>
								</tr>
							<?php
						}
						?>

						<tr>
							<td colspan="2" align="center" style="border: 0px inset maroon;">
								<input
								class="button"
								type="submit"
								value="<?php print _("Submit"); ?>"
								>
							</td>
						</tr>
					</table>
				</form>
				<?php
			} // End of if ($cnt2+1 > $maxaccounts) .. else
		} else {

			if ($authorized!=TRUE){
				print $err_msg;
				die($err_msg);
			}

			if ($DOMAIN_AS_PREFIX){
				$prefix		= $domain;
				$username	= $email;
				if ($freenames!="YES") {
				    $username = $username . "." . $domain;
				}
				$seperator	= '/';
			} else {
				$seperator	= '.';
			}
		    // check to see if there's an account with the same username
		    $query3="select * from accountuser where username='$username'";
		    $result3=$handle->query($query3);
		    $cnt3=$result3->numRows();
		    if ($cnt3!=0) {
			print "<h3>" .
			       _("Sorry, the username already exists") .
			       "</h3><br>";
			include WC_BASE . "/browseaccounts.php";
		} else {
			if ($password == $confirm_password){
				$pwd = new password;
			        $password = $pwd->encrypt($password, $CRYPT);
			    	$query3="INSERT INTO accountuser (username, password, prefix, domain_name) VALUES ('" . $username . "','" . $password . "','" . $prefix . "','" . $domain . "')";

				$cyr_conn = new cyradm;
				$error=$cyr_conn -> imap_login();

				if ($error!=0){
					die ("Error $error");
				}

				$result=$handle->query($query3);

				$query4 = "INSERT INTO virtual (alias, dest, username, status) values ( '" . $email . "@" . $domain . "' , '$username' , '$username' , '1')";

				$result2 = $handle->query($query4);

				if ($result and $result2){
					?>
					<h3>
						<?php print _("Account successfully added to the Database");?>:
						<span style="color: red;">
						<?php echo $username;?>
						</span>
					</h3>
					<?php
				}

				$result=$cyr_conn->createmb("user" . $seperator . $username);

				if ($result){
					?>
					<h3>
						<?php print _("Account succesfully added to the IMAP Subsystem");?>
					</h3>
					<?php
				}
				print $cyr_conn->setacl("user" . $seperator . $username, $CYRUS['ADMIN'], "lrswipcda");
				$result = $cyr_conn->setmbquota("user" . $seperator . $username, $quota);
				include WC_BASE . "/browseaccounts.php";
			}
			else{ # if password and confirm_password are not the same
				print _("Passwords do not match");
			}
		}
	}
		?>
	</td>
</tr>
<!-- #################### newaccount.php end #################### -->

