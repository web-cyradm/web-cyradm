<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################################### Start browseaccounts ################################# -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">

	<?php
	if ($authorized) {
	$cyr_conn = new cyradm;
	$error = $cyr_conn->imap_login();

	if ($error!=0){
		die ("IMAP Error: ".$cyr_conn->geterror());
	}
	?>
	<h3>
		<?php print _("Browse accounts for domain");?>
		<span style="color: red;">
			<?php echo $_GET['domain'];?>
		</span>
	</h3>
	<?php
	if (isset($_GET['row_pos'])) $_SESSION['account_row_pos'] = $_GET['row_pos'];

	$query = "SELECT * FROM accountuser where domain_name='".$_GET['domain']."' ORDER BY username";
	$result = $handle->query($query);
	if (DB::isError($result)) {
		die (_("Database error"));
	}
	$cnt = $result->numRows($result);

	if ($cnt != 0){
		printf ("%s: %d", _("Total accounts"), $cnt);
		print "<br>"._("Displaying from position:")." ".($_SESSION['account_row_pos']+1);
		?>
		<table cellspacing="2" cellpadding="0">
			<tr>
				<td class="navi">
					<a class="navilink" href="index.php?action=newaccount&amp;domain=<?php echo $_GET['domain']; ?>"
					><?php print _("Add new account");?></a>
				</td>

				<td width="20">&nbsp;</td>

				<?php
				$prev = $_SESSION['account_row_pos'] - $_SESSION['account_maxdisplay'];
				if ($prev < 0) $prev = 0;
				$next = $_SESSION['account_row_pos'] + $_SESSION['account_maxdisplay'];
				$last = $cnt - $_SESSION['account_maxdisplay'];
				
				if ($_SESSION['account_row_pos'] <= 0){
					print "<td class=\"navi\"><a class=\"navilink\">"._("First entry") ."</a></td>";
					print "<td class=\"navi\"><a class=\"navilink\">"._("Previous entries")."</a></td>";
				} else {
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=accounts&domain=$domain&row_pos=0\">"._("First entry") ."</a></td>";
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=accounts&domain=".$_GET['domain']."&row_pos=".$prev."\">"._("Previous entries") ."</a></td>";
				}

				if ($next >= $cnt){
					print "<td class=\"navi\"><a class=\"navilink\">"._("Next entries")."</a></td>";
					print "<td class=\"navi\"><a class=\"navilink\">"._("Last entry") ."</a></td>";
				} else {
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=accounts&domain=".$_GET['domain']."&row_pos=".$next."\">"._("Next entries") ."</a></td>";
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=accounts&domain=$domain&row_pos=$last\">"._("Last entry") ."</a></td>";
				}
				?>
			</tr>
		</table>

		<table border="0" width="98%">
			<tbody>
				<tr>
					<th colspan="5">
						<?php print _("action");?>
					</th>

					<th>
						<?php print _("Email address");?>
					</th>

					<th>
						<?php print _("Username");?>
					</th>
					<?php
					$_heads = array( 
						_("Last login"), _("Quota used")
					);
					foreach ($_heads as $_head){
						printf ('<th>%s</th>', $_head);
					}
					?>
					<th>
						<?php print _("services");?>
					</th>
				</tr>

				<?php
				for ($c=$_SESSION['account_row_pos']; $c < (($next>$cnt)?($cnt):($next)); $c++){

					if ($c%2==0){
						$cssrow = "row1";
					} else {
						$cssrow = "row2";
					}

					$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
					$domain = $row['domain_name'];
					$username = $row['username'];

					$query2 = "SELECT * FROM virtual WHERE dest='".$username."' AND username='".$username."'"; # AND alias  !='$username'"; 
					$result2 = $handle->query($query2);
					if (DB::isError($result2)) {
						die (_("Database error"));
					}
					$cnt2 = $result2->numRows($result2);

					$query3 = "SELECT * FROM log WHERE user='".$username."' ORDER BY time DESC";
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
						$_GET['domain'], $username);
					?>

					<tr class="<?php echo $cssrow;?>">
						<td align="center" valign="middle">
							<a href="index.php?action=editaccount<?php echo $_dom_user; ?>"
							><?php print _("Edit email addresses");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=manageaccount<?php echo $_dom_user; ?>"
							><?php print _("Edit account");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=forwardaccount<?php echo $_dom_user; ?>"> <?php print _("Forward");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=deleteaccount<?php echo $_dom_user; ?>"> <?php print _("Delete account");?></a>
						</td>

						<td align="center" valign="middle">
							<a href="index.php?action=catch<?php echo $_dom_user; ?>"><?php print _("Set catch all");?></a>
						</td>

						<td valign="middle">
							<?php
							// Print All Emailadresses found for the account
							for ($c2 = 0; $c2 < $cnt2; $c2++){
								$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, $c2);
								print $row2['alias'] . "<br>";
							}
				                        $query4 = "SELECT * FROM virtual WHERE alias='".$username."' AND username=''";
							$result4 = $handle->query($query4);
							if (DB::isError($result4)) {
								die (_("Database error"));
							}
							$row4 = $result4->fetchRow(DB_FETCHMODE_ASSOC, 0);
							if (is_array($row4)){
							    #print "<hr color=\"ffffff\"><b>" . _("Forwards") . ":</b><br>";
							    print "<hr class=table><b>" . _("Forwards") . ":</b><br>";
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

						<td valign="middle" align="center">
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
									$b_img = 'green.gif';
									if ($q_percent >= $_SESSION['warnlevel']){
										$b_img = 'red.gif';
									}
									echo '<table class="quota_table">';
									echo '<tr>';
									echo '<td class="quota_td" style="background: url(\'images/'.$b_img.'\') repeat-y; background-position: '.((-100)+min(100,round($q_percent))).';">';
									echo round($q_percent,2).'%</td>';
									echo '</tr>';
									echo '</table>';

									printf ("%d MB %s %d MB",
										$quota['used']/1024, _("out of"),
										$quota['qmax']/1024);
								} else {
									print _("Unable to retrieve quota");
								}
							} else {
								print _("Quota not set");
							}
							?>
						</td>
						<td valign="middle">
						<table border=0 align="center">
						<?php
						#print_r ($row);
						if($row['imap']==1){
							print "<tr><td>imap</td><td><img src=\"images/checked.png\" alt=\"yes\" border=0></td></tr>";
						} 
						else{
							print "<tr><td>imap</td><td><img src=\"images/false.png\" alt=\"no\" border=0></td></tr>";
						}
						if($row['pop']==1){
                                                        print "<tr><td>pop</td><td><img src=\"images/checked.png\" alt=\"yes\" border=0></td></tr>";
                                                }
						else{
							print "<tr><td>pop</td><td><img src=\"images/false.png\" alt=\"no\" border=0></td></tr>";
						}
						if($row['sieve']==1){
                                                        print "<tr><td>sieve</td><td><img src=\"images/checked.png\" alt=\"yes\" border=0></td></tr>";
                                                }
						else{
							print "<tr><td>sieve</td><td><img src=\"images/false.png\" alt=\"no\" border=0></td></tr>";
						}

						if($row['smtpauth']==1){
                                                        print "<tr><td>smtpauth</td><td><img src=\"images/checked.png\" alt=\"yes\" border=0></td></tr>";
                                                }
                                                else{
                                                        print "<tr><td>smtpauth</td><td><img src=\"images/false.png\" alt=\"no\" border=0></td></tr>";
                                                }

                                                if($row2['status']==1){
                                                        print "<tr><td>smtp</td><td><img src=\"images/checked.png\" alt=\"yes\" border=0></td></tr>";
                                                }
                                                else{
                                                        print "<tr><td>smtp</td><td><img src=\"images/false.png\" alt=\"no\" border=0></td></tr>";
                                                }



						?>
						</table>
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
					<a class="navilink" href="index.php?action=newaccount&amp;domain=<?php echo $_GET['domain'];?>">
					<?php print _("Add new account");
					?></a>
				</td>
			</tr>
		</table>
		<?php
	}
	} else {
	?>
                        <h3>
                                <?php print $err_msg;?>
                        </h3>
                        <a href="index.php?action=browse"><?php print _("Back");?></a>
                        <?php
	}
	?>
	</td>
</tr>
<!-- ##################################### End browseaccounts.php #################################### -->

