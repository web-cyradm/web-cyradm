<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### newaccount.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

	<?php
	if ($authorized){
		if (empty($_POST['confirmed'])){
			$query = "SELECT * FROM domain WHERE domain_name='".$_GET['domain']."'";
		} else {
			$query = "SELECT * FROM domain WHERE domain_name='".$_POST['domain']."'";
		}
		$result = $handle->query($query);
		if (DB::isError($result)) {
			die (_("Database error"));
		}

		$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);

		$prefix		= $row['prefix'];
		$maxaccounts	= $row['maxaccounts'];
		$def_quota	= $row['quota'];
		$domain_quota	= $row['domainquota'];
		$transport	= $row['transport'];
		// START Andreas Kreisl : freenames
		$freenames	= $row['freenames'];
		// END Andreas Kreisl : freenames
		$freeaddress    = $row['freeaddress'];
		$folders	= $row['folders'];

		if ($transport != "cyrus"){
			print _("transport is not cyrus, unable to create account");
			echo "<br>";
			echo "<a href=\"index.php\">";
			print _("Back");
			echo "</a>";
			echo "</td></tr>";
			exit();
		}
		
		if (empty($_POST['confirmed'])){
			# Why prefix, not domain_name?
			$query = "SELECT * FROM accountuser WHERE prefix='$prefix' order by username";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			$cnt = $result->numRows();
			
			if ($domain_quota != 0) {
				$used_domain_quota = 0;
				$cyr_conn = new cyradm;
				$error = $cyr_conn->imap_login();

				if ($error != 0){
					die ("IMAP Error: ".$cyr_conn->geterror());
				}

				for ($c = 0; $c < $cnt; $c++) {
					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
					
					$_sep = '.';
					if ($DOMAIN_AS_PREFIX) {
						$_sep = '/';
					}
					
					$user_quota = $cyr_conn->getquota("user" . $_sep . $row['username']);
					
					if ($user_quota['qmax'] != "NOT-SET"){
						$used_domain_quota += $user_quota['qmax'];
					}
				}

				$quota_left = $domain_quota - $used_domain_quota;
				
				if ($def_quota > $quota_left) {
 					$def_quota = $quota_left;
				}
				$_SESSION['quota_left'] = $quota_left;
			} // End of if ($domain_quota!=0)
				
			if ($cnt+1 > $maxaccounts){
				?>
				<h3>
					<?php print _("Sorry, no more account allowed for domain");?>
					<span style="color: red;">
						<?php echo $_GET['domain'];?>
					</span>
					<br>
					<?php print _("Maximum allowed accounts is");?>
					<span style="font-weight: bolder;">
						<?php echo $maxaccounts;?>
					</span>
					<br>
					<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
				<?php
			} elseif ($domain_quota != 0 && $quota_left <= 0) {
				?>
				<h3>
					<?php print _("Sorry, no more disk space left for domain");?>
					<span style="color: red;">
						<?php echo $_GET['domain'];?>
					</span>
					<br>
					<?php print _("Quota is");?>
					<span style="font-weight: bolder;">
						<?php echo $domain_quota;?>
					</span>
					<br>
					<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
				<?php
			} else {
				?>
				<h3>
					<?php print _("Add new Account to domain");?>:
					<span style="color: red;">
						<?php echo $_GET['domain'];?>
					</span>
				</h3>
				<p>
					<?php print _("Total accounts") . ": " . $cnt;?>
				</p>
				<?php

				if (!$DOMAIN_AS_PREFIX){
					// START Andreas Kreisl : freenames
					if ($freenames=="YES"){
						$lastaccount = sprintf("%04d",$cnt);
						$lastaccount = $prefix . $lastaccount;
					} else {
						$lastaccount = $prefix."0000";
						if ($cnt > 0){
							$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $cnt - 1);
							$lastaccount = $row['username'];
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
					<input type="hidden" name="domain" value="<?php print $_GET['domain'];?>">

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
							'email'	=> array(_("Email address"), 'a', false, '@' . $_GET['domain']),
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
		} elseif (!empty($_POST['confirmed'])){

			if ($DOMAIN_AS_PREFIX){
				if ($FQUN) {
					$username = $_POST['email']."@".$_POST['domain'];
				} else {
					$username = $_POST['email'].".".$_POST['domain'];
				}
				$separator = '/';
			} else {
				$username = $_POST['username'];
				$separator = '.';
			}

			if ($domain_quota!=0){
				// WARNING: if domain_quota is set, all accounts MUST have quotas!
				if (empty($_POST['quota'])){
					$_POST['quota'] = $def_quota;
				}
				if ($_POST['quota'] > $_SESSION['quota_left']){
					$_POST['quota'] = $_SESSION['quota_left'];
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
				unset($_SESSION['quota_left']);
			}

			$cyr_conn = new cyradm;
			$error = $cyr_conn->imap_login();
			if ($error != 0) {
				die ("Error $error");
			}
			
			$pwd = new password;
			$password = $pwd->encrypt($_POST['password'], $CRYPT);
			
			$query = "INSERT INTO accountuser (username, password, prefix, domain_name) VALUES ('".$username."','".$password."','".$prefix."','".$_POST['domain']."')";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}

			$query = "INSERT INTO virtual (alias, dest, username, status) values ( '".$_POST['email']."@".$_POST['domain']."','".$username."','".$username."','1')";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			?>
				<h3>
					<?php print _("Account successfully added to the Database");?>:
					<span style="color: red;">
					<?php echo $username;?>
					</span>
				</h3>
			<?php

			$result = $cyr_conn->createmb("user" . $separator . $username);

			if ($result) {
				?>
				<h3>
					<?php print _("Account succesfully added to the IMAP Subsystem");?>
				</h3>
				<?php
			}
			$result = $cyr_conn->setacl("user" . $separator . $username, $CYRUS['ADMIN'], $cyr_conn->allacl);
			$result = $cyr_conn->setmbquota("user" . $separator . $username, $_POST['quota']);
			$AUTOCREATE_MAILBOXES = array();
			if (!empty($folders)) $AUTOCREATE_MAILBOXES = explode(',',$folders);
			if(sizeof($AUTOCREATE_MAILBOXES) > 0) {
				//log out of admin, log in as user
				$cyr_conn->imap_logout();
				$cyr_conn->imap_login($username, $_POST['password']);

				//create and subscribe to each mailbox listed in AUTOCREATE_MAILBOXES
				for($i=0; $i < sizeof($AUTOCREATE_MAILBOXES); $i++) {
					$new_folder = trim($AUTOCREATE_MAILBOXES[$i]);
					//if (function_exists('mb_convert_encoding')) {
					$new_folder = mb_convert_encoding($new_folder,"UTF7-IMAP",$charset);
					//}
					$cyr_conn->createmb("INBOX".$separator.$new_folder);
					$cyr_conn->command('. subscribe "INBOX'.$separator.$new_folder.'"');
				}
				$cyr_conn->imap_logout();
			}
			$_GET['domain'] = $_POST['domain'];
			include WC_BASE . "/browseaccounts.php";
		} // End of if (empty($_POST['confirmed']))
	} else { // if ($authorized)
		?>
		<h3>
			<?php print $err_msg;?>
		</h3>
			<a href="index.php?action=newaccount&domain=<?php echo $_POST['domain'];?>"><?php print _("Back");?></a>
		<?php
	} // End of if ($authorized)
	?>
	</td>
</tr>
<!-- #################### newaccount.php end #################### -->
