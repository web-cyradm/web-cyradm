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
		include WC_BASE . '/lib/sieve-php.lib';                                                                     
                include WC_BASE . '/lib/sieve_strs.php'; 
		$daemon = new sieve("localhost","2000", $username, $CYRUS['PASS'], $CYRUS['ADMIN']);
		$cyr_conn = new cyradm;
		$cyr_conn->imap_login();
		?>

		<h3>
			<?php print _("Email adresses defined for user");?>
			<span style="color: red;">
				<?php echo $username;?>
			</span>
		</h3>

		<?php
		$query = "SELECT * FROM virtual WHERE username='$username'"; # AND alias != '$username'";
		$handle = DB::connect($DB['DSN'], true);
		if (DB::isError($handle)) {
			die (_("Database error"));
		}

		$hnd = $handle->query($query);
		$cnt = $hnd->numRows();
		?>

		<table cellspacing="2" cellpadding="0">
			<tr>
				<td class="navi">
					<a href="index.php?action=newemail&amp;domain=<?php echo $domain;?>&amp;username=<?php echo $username;?>"><?php
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
			$b = 0;
			for ($c = 0; $c < $cnt; $c++){
				if ($b == 0){
					$cssrow = 'row1';
					$b = 1;
				} else {
					$cssrow = 'row2';
					$b = 0;
				}

				$row = $hnd->fetchRow(DB_FETCHMODE_ASSOC, $c);
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
							<a href="<?php printf("index.php?action=%s&amp;domain=%s&amp;alias=%s&amp;username=%s", $_action, $domain, $alias, $username); ?>"><?php echo $_txt;?></a>
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
					if ($c == 0){
						?>
						<td valign="middle" rowspan="<?php echo $cnt;?>">
							<?php
							if ($DOMAIN_AS_PREFIX){
								$quota = $cyr_conn->getquota("user/" . $username);
							} else {
								$quota = $cyr_conn->getquota("user." . $username);
							}

							if ($quota['used'] != 'NOT-SET'){
								$q_used		= $quota['used'];
								$q_total	= $quota['qmax'];
								$q_percent	= 100 * $q_used / $q_total;
								
								printf ("%d KBytes %s %d KBytes (%.2f%%)",
									$quota['used'], _("out of"),
									$quota['qmax'], $q_percent);
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
	</td>
</tr>

</td></tr>

<!-- #################### editaccount.php end #################### -->

