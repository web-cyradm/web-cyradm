<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- ############################## Start browse.php ###################################### -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">
		<h3>
			<?php print _("Browse domains");?>
		</h3>

		<table border="0">
			<tbody>
				<tr>
					<th colspan="4">
						<?php print _("action");?>
					</th>

					<th>
						<?php print _("domainname");?>
					</th>

					<?php
					if (! $DOMAIN_AS_PREFIX){
						?>
						<th>
							<?php print _("prefix");?>
						</th>
						<?php
					}
					?>

					<th>
						<?php print _("max Accounts");?>
					</th>

					<th>
						<?php print _("default quota per user");?>
					</th>
				</tr>
				
				<?php

				if (! isset($allowed_domains)) {
					$query = "SELECT * FROM domain ORDER BY domain_name";
				} else {
//					$query = "SELECT * FROM domain WHERE domain_name='$allowed_domains' ORDER BY domain_name";
					$query = "SELECT * FROM domain WHERE domain_name='";
					for ($i = 0; $i < $cnt; $i++){
						$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $i);
						$allowed_domains=$row['domain_name'];
//						print "DEBUG: Allowed Domains".$allowed_domains;
						$query.="$allowed_domains' OR domain_name='";
					}
					$query .= "' ORDER BY domain_name";
//					print $query;
				}

				$handle = DB::connect($DB['DSN'], true);

				if (DB::isError($handle)) {
					die (_("Database error"));
				}


				$result = $handle->query($query);
				$cnt    = $result->numRows($result);

				$b = 0;
				for ($c=0; $c < $cnt; $c++){
					if ($b==0){
						$cssrow="row1";
						$b=1;
					} else {
						$cssrow="row2";
						$b=0;
					}

					$row = $result->fetchRow(DB_FETCHMODE_ASSOC,$c);
					$domain = $row['domain_name'];

					?>
					<tr class="<?php echo $cssrow;?>">
						<?php
						$_cols = array(
							'editdomain'	=> _("Edit Domain"),
							'deletedomain'	=> _("Delete Domain"),
							'accounts'	=> _("accounts"),
							'aliases'	=> _("Aliases")
						);
						foreach ($_cols as $_action => $_txt){
							?>
							<td>
								<?php
								printf ('<a href="index.php?action=%s&amp;domain=%s">%s</a>',
									$_action, $domain, $_txt);
								?>
							</td>
							<?php
						}
						?>

						<td>
							<?php echo $domain;?>
						</td>

						<td>
							<?php
							if (! $DOMAIN_AS_PREFIX){
								# Print the prefix
								echo $row['prefix'];
								echo "</td><td>";
							}
							?>
							<!-- Max Account -->
							<?php
							echo $row['maxaccounts'];
							?>
						</td>
						
						<td>
							<!-- Quota -->
							<?php
							echo $row['quota'];
							?>
						</td>
					</tr>
					<?php
				} // End of for
				?>
			</tbody>
		</table>
<!-- ############################### End browse.php ############################################# -->

