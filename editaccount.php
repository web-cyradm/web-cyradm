<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### editaccount.php start #################### -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top">

<?php
if ($authorized) {
	include WC_BASE . '/lib/sieve-php.lib.php';
	include WC_BASE . '/lib/sieve_strs.php'; 
	$daemon = new sieve($CYRUS['HOST'],"2000", $CYRUS['ADMIN'], $CYRUS['PASS'], $_GET['username']);
	$cyr_conn = new cyradm;
	$cyr_conn->imap_login();
?>

		<h3>
			<?php print _("Email addresses defined for user");?>
			<span style="color: red;">
				<?php echo $_GET['username'];?>
			</span>
		</h3>

		<?php
		$query = "SELECT * FROM virtual WHERE username='".$_GET['username']."'"; # AND alias != '$username'";
		$result = $handle->query($query);
		$cnt = $result->numRows();
		?>

		<table cellspacing="2" cellpadding="0">
			<tr>
				<td class="navi">
					<a class="navilink" href="index.php?action=newemail&amp;domain=<?php echo $_GET['domain'];?>&amp;username=<?php echo $_GET['username'];?>"><?php
					print _("New email address");?></a>
				</td>
			</tr>
		</table>

		<table border="0">
			<tr>
				<th colspan="4">
					<?php print _("action");?>
				</th>
				
				<th>
					<?php print _("Email address");?>
				</th>

				<th>
					<?php print _("Forward");?>
				</th>

				<!--
				<th>
					<?php print _("Username");?>
				</th>
				-->

				<th>
					<?php print _("Quota used");?>
				</th>
			</tr>

<?php
					for ($c=0; $c < $cnt; $c++) {
						if ($c%2==0){
							$cssrow="row1";
						} else {
							$cssrow="row2";
						}
						$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
						$alias = $row['alias'];
?>

					<tr class="<?php echo $cssrow;?>">
					<?php
					$_cols = array(
						'editemail'	=> _("Edit Emailadress"),
						'forwardalias'	=> _("Forward"),
						'vacation'	=> _("Vacation"),
						'deleteemail'	=> _("Delete Emailadress")
					);
					foreach ($_cols as $_action => $_txt){
						?>
						<td align="center" valign="middle">
							<a href="<?php printf("index.php?action=%s&amp;domain=%s&amp;alias=%s&amp;username=%s", $_action, $_GET['domain'], $alias, $_GET['username']); ?>"><?php echo $_txt;?></a>
						</td>
						<?php
					}
					?>

					<td valign="middle">
						<?php echo $alias;?>
					</td>

					<td valign="middle">
					    <?php
                                    		if ($daemon->sieve_login()){
                                            	    $sieve_str = new sieve_strs;
                                            	    $old_script = $sieve_str->get_old_script($daemon);
                                            	    if (preg_match("/redirect \".*$/siU", $old_script, $matches)){
                                                        $forwards_script = $matches[0];
                                                        $forwards_text = '';
                                                        while (preg_match ("/(redirect \")(.*)(\";)(.*$)/siU", $forwards_script, $matches)){
                                                                $forwards_text .= $matches[2].'<br>';
                                                                $forwards_script = $matches[4];
                                                        }
                                                        $forwards_text = rtrim ($forwards_text, ', ');
                                                        if (preg_match ("/keep;/i", $forwards_script, $matches)){
                                                                $forwards_text .= "<b>" . $username . "</b>";
                                                        }
                                            	    } else {
                                                        $forwards_text ='';
							$keep = '';
                                            	    }
                                    		} else {
                                            	    $forwards_text = '';
                                    		}
                        			print $forwards_text;
					    ?>
					</td>

					<?php
					if ($c == 0) {
						?>
						<td valign="middle" rowspan="<?php echo $cnt;?>">
							<?php
							if ($DOMAIN_AS_PREFIX){
								$quota = $cyr_conn->getquota("user/" . $_GET['username']);
							} else {
								$quota = $cyr_conn->getquota("user." . $_GET['username']);
							}

							if ($quota['used'] != 'NOT-SET'){
								$q_used		= $quota['used'];
								$q_total	= $quota['qmax'];
								$q_percent	= 100 * $q_used / $q_total;
								
								printf ("%d MB %s %d MB (%.2f%%)",
									$quota['used']/1024, _("out of"),
									$quota['qmax']/1024, $q_percent);
							} else {
								print _("Quota not set");
							}
							?>
						</td>
						<?php
					}
					?>
				</tr>
<?php

			}
			?>
		</table>
<?php
} else {
?>
		<h3>
			<?php print $err_msg;?>
		</h3>
		<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
<?php
}
?>
	</td>
</tr>

<!-- #################### editaccount.php end #################### -->
