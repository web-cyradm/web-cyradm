<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################################### Start browseaccounts ################################# -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">

	<?php
	$cyr_conn = new cyradm;
	$error = $cyr_conn -> imap_login();

	if ($error!=0){
		die ("Error $error");
	}
	?>
	<h3>
		<?php print _("Browse accounts for domain");?>
		<span style="color: red;">
			<?php echo $domain;?>
		</span>
	</h3>
	<?php

	$row_pos = (empty($row_pos))?(0):($row_pos);

	$query = "SELECT * FROM accountuser where domain_name='$domain' ORDER BY username";
	$handle = DB::connect($DB['DSN'], true);
	if (DB::isError($handle)) {
		die (_("Database error"));
	}

	$result = $handle->limitQuery($query,$row_pos,10);
	$cnt = $result->numRows($result);

	$query2 = "SELECT * FROM accountuser where domain_name='$domain' ORDER BY username";
	$result2 = $handle->query($query2);


	$total=$result2->numRows($result2);

	$b = 0;
	if ($cnt != 0){
		printf ("%s: %d", _("Total accounts"), $total);
		print "<br>"._("Displaying from position:")." $row_pos";
		?>
		<table cellspacing="2" cellpadding="0">
			<tr>
				<td class="navi">
					<a href="index.php?action=newaccount&amp;domain=<?php echo $domain; ?>"
					><?php print _("Add new account");?></a>
				</td>

				<?php
				$prev = $row_pos - 10;
				$next = $row_pos + 10;
				
				if ($row_pos < 10){
					$_linkP = '#';
				} else {
					$_linkP = sprintf('index.php?action=accounts&amp;domain=%s&amp;row_pos=%d',
						$domain, $prev);
				}

				if ($next > $total){
					$_linkN = '#';
				} else {
					$_linkN = sprintf('index.php?action=accounts&amp;domain=%s&amp;row_pos=%d',
						$domain, $next);
				}
				?>
				
				<td class="navi">
					<a href="<?php echo $_linkP;?>"><?php print _("Previous 10 entries");?></a>
				</td>

				<td class="navi">
					<a href="<?php echo $_linkN;?>"><?php print _("Next 10 entries");?></a>
				</td>
			</tr>
		</table>

		<table border="0">
			<tbody>
				<tr>
					<th colspan="6">
						<?php print _("action");?>
					</th>

					<?php
					$_heads = array(
						_("Email address"), _("Username"), 
						_("Last login"), _("Quota used")
					);
					foreach ($_heads as $_head){
						printf ('<th>%s</th>', $_head);
					}
					?>
				</tr>

				<?php
				for ($c=0;$c<$cnt;$c++){

					if ($b==0){
						$cssrow = "row1";
						$b = 1;
					} else {
						$cssrow = "row2";
						$b = 0;
					}

					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
					$domain = $row['domain_name'];
					$username = $row['username'];

					$query2 = "SELECT * FROM virtual WHERE username='$username'"; # AND alias  !='$username'"; 
					$result2 = $handle->query($query2);
					$cnt2 = $result2->numRows($result2);
					$row = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
					$alias = $row['alias'];

					$query3 = "SELECT * FROM log WHERE user='" . $username . "' ORDER BY time DESC";
					$result3 = $handle->query($query3); 
					if (! DB::isError($result3)){
						$row3 = $result3->fetchRow(DB_FETCHMODE_ASSOC, 0);
						$lastlogin = $row3['time'];
					} else {
						$lastlogin = '';
					}
					if ($lastlogin==""){
						//$lastlogin=_("Never logged in");
						$lastlogin=_("n/a");
					}
					$_dom_user = sprintf('&amp;domain=%s&amp;username=%s',
						$domain, $username);
					$_dom_user_alias = $_dom_user . '&amp;alias=' . $alias;
					?>

					<tr class="<?php echo $cssrow;?>">
						<td align="center" valign="middle">
							<a href="index.php?action=editaccount<?php echo $_dom_user; ?>"
							><?php print _("Edit account");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=change_password<?php echo $_dom_user_alias; ?>"
							><?php print _("Change Password");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=forwardaccount<?php echo $_dom_user_alias; ?>"> <?php print _("Forward");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=deleteaccount<?php echo $_dom_user; ?>"> <?php print _("Delete account");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=setquota<?php echo $_dom_user; ?>"><?php print _("Set quota");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=catch<?php echo $_dom_user; ?>"><?php print _("Set catch all");?></a>
						</td>

						<td valign="middle">
							<?php
							// Print All Emailadresses found for the account
							for ($c2 = 0; $c2 < $cnt2; $c2++){
								$row = $result2->fetchRow(DB_FETCHMODE_ASSOC, $c2);
								print $row['alias'] . "<br>";
							}
				                        $query4 = "select * from virtual where alias='" . $username . "'";
							$result4 = $handle->query($query4);
							$row4 = $result4->fetchRow(DB_FETCHMODE_ASSOC, 0);
							if (is_array($row4)){
							    print "<hr color=\"ffffff\"><b>" . _("Forwards") . ":</b><br>";
							    $forwards_tmp = preg_split('|,\s*|', stripslashes($row4['dest']));   
							    $forwards = array();                                                
							    while (list(, $forward) = each($forwards_tmp)){
								if (strtolower($forward) != strtolower($username)){
								    $forwards[] = htmlspecialchars(trim($forward));
								} else {
								    $forwards[] = "<b>" . htmlspecialchars(trim($forward)) . "</b>";
								}
							    }
							    echo implode("<br>", $forwards);
							}
							?>
						</td>

						<td valign="middle">
							<?php echo $username;?>
						</td>

						<td align="center" valign="middle">
							<?php echo $lastlogin;?>
						</td>

						<td valign="middle">
							<?php
							if ($DOMAIN_AS_PREFIX){
								$quota = $cyr_conn->getquota("user/" . $username);
							} else {
								$quota = $cyr_conn->getquota("user." . $username);
							}

							if ($quota['used'] != "NOT-SET"){
								$q_used  = $quota['used'];
								$q_total = $quota['qmax'];
								if (! $q_total == 0){
									$q_percent = 100*$q_used/$q_total;
									printf ("%d KBytes %s %d KBytes (%.2f%%)",
										$quota['used'], _("out of"),
										$quota['qmax'], $q_percent);
								} else {
									print _("Unable to retrieve quota");
								}
							} else {
								print _("Quota not set");
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	} else {
		print _("No accounts found");
		?>
		<table>
			<tr>
				<td class="navi">
					<a href="index.php?action=newaccount&amp;domain=<?php echo $domain;?>&amp;username=<?php echo $username;?>">
					<?php print _("Add new account");
					?></a>
				</td>
			</tr>
		</table>
		<?php
	}
	?>
</tr>
<!-- ##################################### End browseaccounts.php #################################### -->

