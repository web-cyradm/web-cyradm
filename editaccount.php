<!-- #################### editaccount.php start #################### -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top">

		<?php
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
		$query = "select * from virtual where username='$username'";
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
					<a href="index.php?action=newemail&amp;domain=<?php
					echo $domain;?>&amp;username=<?php
					echo $username;?>"><?php
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
							<a href="<?php
							printf("index.php?action=%s&amp;domain=%s&amp;alias=%s&amp;username=%s",
								$_action, $domain, $alias, $username);
							?>"><?php echo $_txt;?></a>
						</td>
						<?php
					}
					?>

					<td valign="middle">
						<?php echo $alias;?>
					</td>

					<td valign="middle">
						<?php print _("Forward or not?");?>
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
								
								printf ("%d KiBytes %s %d KiBytes (%.2f%%)",
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

