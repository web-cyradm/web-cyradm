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
		if ($authorized){
			$cyr_conn = new cyradm;
			$cyr_conn->imap_login();

			$_sep = '.';
			if ($DOMAIN_AS_PREFIX) {
				$_sep = '/';
			}
			$q = $cyr_conn->getquota("user" . $_sep . $username);

			if (empty($confirmed)){
				?>
				<h3>
					<?php print _("Setting individual Quota for user");?>:
					<span style="color: red;">
						<?php echo $username;?>
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
					value="<?php print $domain; ?>"
					>
					
					<input type="hidden" 
					name="username"
					value="<?php print $username; ?>" >
					
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
				</form>
				<?php

			} else {
				$query1 = "SELECT `prefix`,`domainquota` from domain WHERE domain_name='$domain'";

				$handle = DB::connect($DB['DSN'], true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}
			
				$result1 = $handle->query($query1);
			
				$row = $result1->fetchRow(DB_FETCHMODE_ASSOC, 0);
			
				$prefix = $row['prefix'];
				$domain_quota = $row['domainquota'];

//				$cyr_conn = new cyradm;
//				$cyr_conn->imap_login();
				// for change bigger->smaler or none->set we don't want domain quota checks
				if ($domain_quota!=0 && $q['qmax']<$quota && $q['qmax']!="NOT-SET") {
					$used_domain_quota = 0;

					$query2 = "SELECT `username` FROM accountuser WHERE prefix='$prefix' ORDER BY `username`";
					$result2 = $handle->query($query2);
					$cnt2 = $result2->numRows($result2);

					for ($c2 = 0; $c2 < $cnt2; $c2++){
						$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, $c2);
						$user_quota = $cyr_conn->getquota("user" . $_sep . $row2['username']);
						if ($user_quota['qmax'] != "NOT-SET"){
							$used_domain_quota += $user_quota['qmax'];
						}
					}
					$quota_left = $domain_quota - $used_domain_quota;

					if ($quota_left>0) {
						if ($quota > $quota_left){
							$quota = $quota_left;
								?>
								<h3>
									<?php print _("Quota exeeded");?>
									<span style="color: red;">
										<?php print _("New quota is");?>:
									</span>
									<span style="font-weight: bolder;">
										<?php echo $quota;?>
									</span>
								</h3>
								<?php
						}
						print $cyr_conn->setmbquota("user" . $_sep . $username, $quota);
					}
					else {
						?>
						<h3>
							<span style="color: red;">
								<?php print _("Quota exeeded");?>
							<br>
								<?php print _("Quota NOT changed");?>
							</span>
						</h3>
						<?php
					}
				} // End of if ($domain_quota!=0 && $q['qmax']<$quota && $q['qmax']!="NOT-SET")
				else {
					print $cyr_conn->setmbquota("user" . $_sep . $username, $quota);
					?>
					<h3>
						<?php print _("Quote for user");?>
						<span style="color: red;">
							<?php echo $username;?>
						</span>
						<?php print _("changed to");?>
						<span style="color: red;">
							<?php echo $quota;?>
						</span>
					</h3>
				<?php
				}
				include WC_BASE . "/browseaccounts.php";
			}
		}

		print "<h3>".$err_msg."</h3>";

		?>
	</td>
</tr>

<!-- #################### setquota.php end #################### -->

