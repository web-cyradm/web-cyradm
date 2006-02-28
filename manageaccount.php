<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### manageaccount.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if ($authorized) {
			$cyr_conn = new cyradm;
			$error = $cyr_conn->imap_login();

			if ($error != 0) {
				die ("Error: " . $error);
			}

			if (empty($_POST['confirmed'])) {
				$imap_checked="";
				$pop_checked="";
				$sieve_checked="";
				$smtpauth_checked="";
				$smtp_checked="";
				
				$_sep = '.';
				if ($DOMAIN_AS_PREFIX) {
					$_sep = '/';
				}
				$q = $cyr_conn->getquota("user" . $_sep . $_GET['username']);

				$query = "SELECT * FROM accountuser WHERE username ='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				$cnt = $result->numRows($result);

				if ($cnt) {
					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
					if ($row['imap']) {
						$imap_checked="checked";
					}					
					if ($row['pop']) {
						$pop_checked="checked";
					}
					if ($row['sieve']) {
						$sieve_checked="checked";
					}
					if ($row['smtpauth']) {
						$smtpauth_checked="checked";
					}
				}
				
				$query = "SELECT status FROM virtual WHERE username ='".$_GET['username']."' LIMIT 1";
				$result = $handle->query($query);

				if (DB::isError($result)) {
				        die (_("Database error"));
				}
				$cnt = $result->numRows($result);

				if ($cnt) {
					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
					if ($row['status']) {
						$smtp_checked="checked";
					}
				}				
				?>
				<form action="index.php" method="POST">
					<input type="hidden" name="action" value="manageaccount">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php echo $_GET['domain'];?>"> 
					<input type="hidden" name="username" value="<?php echo $_GET['username'];?>">
<!--
					<h3>
						<?php print _("Additional information for account");?>
						<span style="color: red;">
							<?php echo $_GET['username'];?>
						</span>
					</h3>

					<table>
					<tr>	
						<td width="150">
							<?php print _("First name");?>:
						</td>
						<td>
							<input class="inputfield" type="password" size="30" name="firstname" readonly>
						</td>
					</tr>

					<tr>	
						<td width="150">
							<?php print _("Last name");?>:
						</td>
						<td>
							<input class="inputfield" type="password" size="30" name="lastname" readonly>
						</td>
					</tr>
					</table>
-->
					<h3>
						<?php print _("Change password for account");?>
						<span style="color: red;">
							<?php echo $_GET['username'];?>
						</span>
					</h3>

					<table>		
					<tr>
						<td width="150">
							<?php print _("New password");?>:
						</td>
						
						<td>
							<input class="inputfield" type="password" size="30" name="new_password">
						</td>
					</tr>
					
					<tr>
						<td width="150">
							<?php print _("Confirm new password")?>:
						</td>
						
						<td>
							<input class="inputfield" type="password" size="30" name="confirm_password">
						</td>
					</tr>
					</table>

					<h3>
						<?php print _("Setting individual Quota for user");?>:
						<span style="color: red;">
							<?php echo $_GET['username'];?>
						</span>
					</h3>
					
					<table>
					<tr>
						<td width="150">
							<?php print _("Quota")?>:
						</td>

						<td>
							<input class="inputfield" type="text" size="10" name="quota" value="<?php print $q_total = $q['qmax']; ?>" > Kbytes
						</td>
					</tr>
					</table>

					<h3>
				                <?php print _("Edit services for user");?>
							<span style="color: red;">
								<?php echo $username;?>
							</span>
					</h3>

					<table>
					<tr>
						<th><?php print _("Service");?></th>
						<th><?php print _("Status");?></th>
					</tr>
					<tr>
						<td><?php print _("Fetch mail via IMAP client");?></td>
						<td><input name="imap" value="1" type="checkbox" <?php print $imap_checked?>></td>
					</tr>
					<tr>
						<td><?php print _("Fetch mail via POP client");?></td>
						<td><input name="pop" value="1" type="checkbox" <?php print $pop_checked?>></td>
					</tr>
					<tr>
						<td><?php print _("Set vacation message and filter rules with sieve");?></td>
						<td><input name="sieve" value="1" type="checkbox" <?php print $sieve_checked; ?>></td>
					</tr>

					<tr>
						<td><?php print _("Send E-Mails via smtp authentication");?></td>
						<td><input name="smtpauth" value="1" type="checkbox" <?php print $smtpauth_checked; ?>></td>
					</tr>

					<tr>
						<td><?php print _("Receive E-Mails via smtp");?></td>
						<td><input name="smtp" value="1" type="checkbox" <?php print $smtp_checked; ?>></td>
					</tr>

					<tr>
						<td height="10">&nbsp;</td>
					</tr>

					<tr>
						<td colspan="2" align="center">
							<input class="button" type="submit" value="<?php print _("Submit");?>">&nbsp;
							<input class="button" type="submit" name="cancel" value="<?php print _("Cancel"); ?>">
						</td>
					</tr>
					</table>
				</form>
				<?php
			} elseif (!empty($_POST['cancel'])) {
				$_GET['domain'] = $_POST['domain'];
				include WC_BASE . "/browseaccounts.php";
			} else {
				// Change password
				if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
					$query = "SELECT password FROM accountuser WHERE username='".$_POST['username']."'";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
					$password = $row['password'];

					if ($PASSWORD_CHANGE_METHOD=="sql"){
						$pwd = new password;
						$new_password = $pwd->encrypt($_POST['new_password'], $CRYPT);
						$query = "UPDATE accountuser SET password='$new_password' WHERE username='".$_POST['username']."'";
						$result = $handle->query($query);
						if (DB::isError($result)) {
							die (_("Database error"));
						} else {
							print "<h3>"._("Password changed")."</h3>";
						}
					} elseif ($PASSWORD_CHANGE_METHOD=="poppassd") {
						require WC_BASE . '/lib/poppassd.php';
						$daemon = new poppassd;
						if ($daemon->change_password($_POST['username'], $password, $_POST['new_password'])) {
							print  "<h3>"._("Password changed")."</h3>";
						} else {
							print $daemon->$err_str;
							print "<h3>"._("Failure in changing password.")."</h3>";
						}
					}
				}
			
				// Change services
				if (empty($_POST['imap'])) {
					$_POST['imap'] = 0;
				}
				if (empty($_POST['pop'])) {
					$_POST['pop'] = 0;
				}
				if (empty($_POST['sieve'])) {
					$_POST['sieve'] = 0;
				}
				if (empty($_POST['smtpauth'])) {
					$_POST['smtpauth'] = 0;
				}
				if (empty($_POST['smtp'])) {
					$_POST['smtp'] = 0;
				}
				$query = "UPDATE accountuser SET imap='".$_POST['imap']."', pop='".$_POST['pop']."', sieve='".$_POST['sieve']."', smtpauth='".$_POST['smtpauth']."' WHERE username='".$_POST['username']."'";
				$result1 = $handle->query($query);
				$query = "UPDATE virtual SET status='".$_POST['smtp']."' WHERE username='".$_POST['username']."'";
				$result2 = $handle->query($query);
				if ($result1 && $result2) {
					print "<h3>"._("Services successfully changed")."</h3>";
				} else {
					print "<h3>"._("Services NOT changed")."</h3>";
				}

				// Change quota
				$_sep = '.';
				if ($DOMAIN_AS_PREFIX) {
					$_sep = '/';
				}
				$q = $cyr_conn->getquota("user" . $_sep . $_POST['username']);
				if ($q['qmax']!=$_POST['quota']) {
				$query = "SELECT `prefix`,`domainquota` FROM `domain` WHERE `domain_name`='".$_POST['domain']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			
				$prefix = $row['prefix'];
				$domain_quota = $row['domainquota'];

				// for change bigger->smaler or none->set we don't want domain quota checks
				if ($domain_quota!=0 && $q['qmax']<(int)$_POST['quota'] && $q['qmax']!="NOT-SET") {
					$used_domain_quota = 0;

					$query = "SELECT `username` FROM `accountuser` WHERE `prefix`='$prefix' ORDER BY `username`";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}

					$cnt = $result->numRows($result);

					for ($c = 0; $c < $cnt; $c++) {
						$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
						$user_quota = $cyr_conn->getquota("user" . $_sep . $row['username']);
						if ($user_quota['qmax'] != "NOT-SET"){
							$used_domain_quota += $user_quota['qmax'];
						}
					}
					# All space - space used by all accounts + space used changed account
					$quota_left = $domain_quota - $used_domain_quota + $q['qmax'];

					if ($quota_left>0) {
						if ($_POST['quota'] > $quota_left){
							$_POST['quota'] = $quota_left;
								?>
								<h3>
									<?php print _("Quota exeeded");?>
									<span style="color: red;">
										<?php print _("New quota is");?>:
									</span>
									<span style="font-weight: bolder;">
										<?php echo $_POST['quota'];?>
									</span>
								</h3>
								<?php
						}
						$cyr_conn->setmbquota("user" . $_sep . $_POST['username'], $_POST['quota']);
						?>
						<h3>
							<?php print _("Quote for user");?>
							<span style="color: red;">
								<?php echo $_POST['username'];?>
							</span>
							<?php print _("changed to");?>
							<span style="color: red;">
								<?php echo $_POST['quota'];?>
							</span>
						</h3>
						<?php
					} else {
						?>
						<h3>
							<span style="color: red;">
								<?php print _("Quota exeeded");?>
							<br>
								<?php print _("Quota NOT changed");?>
							</span>
						</h3>
						<?php
					} // End of if ($quota_left>0)
				} else { // if ($domain_quota!=0 && $q['qmax']<$_POST['quota'] && $q['qmax']!="NOT-SET")
					$cyr_conn->setmbquota("user" . $_sep . $_POST['username'], $_POST['quota']);
					?>
					<h3>
						<?php print _("Quote for user");?>
						<span style="color: red;">
							<?php echo $_POST['username'];?>
						</span>
						<?php print _("changed to");?>
						<span style="color: red;">
							<?php echo $_POST['quota'];?>
						</span>
					</h3>
				<?php
				} // End of if ($domain_quota!=0 && $q['qmax']<$_POST['quota'] && $q['qmax']!="NOT-SET")
				}
				$_GET['domain'] = $_POST['domain'];
				include WC_BASE . "/browseaccounts.php";
			} // End of if (empty($_POST['confirmed']))
		} else {
			?>
			<h3>
				<?php print $err_msg; ?>
			</h3>
			<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
			<?php
		} // End of if ($authorized)
		?>
	</td>
</tr>

<!-- #################### editeccountnew.php end #################### -->
