<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### setquota.php start #################### -->
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

			$_sep = '.';
			if ($DOMAIN_AS_PREFIX) {
				$_sep = '/';
			}
			$q = $cyr_conn->getquota("user" . $_sep . $_GET['username']);

			if (empty($_GET['confirmed'])) {
				?>
				<h3>
					<?php print _("Setting individual Quota for user");?>:
					<span style="color: red;">
						<?php echo $_GET['username'];?>
					</span>
				</h3>
				<form action="index.php">
					<input type="hidden"
					name="action"
					value="setquota">
					
					<input type="hidden"
					name="confirmed"
					value="true">
					
					<input type="hidden"
					name="domain"
					value="<?php print $_GET['domain']; ?>"
					>
					
					<input type="hidden" 
					name="username"
					value="<?php print $_GET['username']; ?>" >
					
					<input class="inputfield"
					type="text"
					size="10"
					name="quota"
					value="<?php print $q_total = $q['qmax']; ?>" > Kbytes
					
					<input class="button" 
					type="submit" 
					value="<?php 
					print _("Submit"); ?>"
					>

					<input class="button"
					type="submit"
					name="cancel"
					value="<?php print _("Cancel"); ?>"
					>
				</form>
				<?php
			} elseif (!empty($_GET['cancel'])) {
				include WC_BASE . "/browseaccounts.php";
			} else {
				$query = "SELECT `prefix`,`domainquota` FROM `domain` WHERE `domain_name`='".$_GET['domain']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			
				$prefix = $row['prefix'];
				$domain_quota = $row['domainquota'];

				// for change bigger->smaler or none->set we don't want domain quota checks
				if ($domain_quota!=0 && $q['qmax']<(int)$_GET['quota'] && $q['qmax']!="NOT-SET") {
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
						if ($_GET['quota'] > $quota_left){
							$_GET['quota'] = $quota_left;
								?>
								<h3>
									<?php print _("Quota exeeded");?>
									<span style="color: red;">
										<?php print _("New quota is");?>:
									</span>
									<span style="font-weight: bolder;">
										<?php echo $_GET['quota'];?>
									</span>
								</h3>
								<?php
						}
						$cyr_conn->setmbquota("user" . $_sep . $_GET['username'], $_GET['quota']);
						?>
						<h3>
							<?php print _("Quote for user");?>
							<span style="color: red;">
								<?php echo $_GET['username'];?>
							</span>
							<?php print _("changed to");?>
							<span style="color: red;">
								<?php echo $_GET['quota'];?>
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
				} else { // if ($domain_quota!=0 && $q['qmax']<$_GET['quota'] && $q['qmax']!="NOT-SET")
					$cyr_conn->setmbquota("user" . $_sep . $_GET['username'], $_GET['quota']);
					?>
					<h3>
						<?php print _("Quote for user");?>
						<span style="color: red;">
							<?php echo $_GET['username'];?>
						</span>
						<?php print _("changed to");?>
						<span style="color: red;">
							<?php echo $_GET['quota'];?>
						</span>
					</h3>
				<?php
				} // End of if ($domain_quota!=0 && $q['qmax']<$_GET['quota'] && $q['qmax']!="NOT-SET")
				include WC_BASE . "/browseaccounts.php";
			} // End of if (empty($_GET['confirmed']))
		} else {
			?>
			<h3>
				<? print $err_msg;?>
			</h3>
			<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
			<?php
		} // End of if ($authorized)
		?>
	</td>
</tr>

<!-- #################### setquota.php end #################### -->

