<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- ############################## Start browse.php ###################################### -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">
		<h3>
			<?php print _("Browse domains");?>
		</h3>
		<?php
		$row_pos = (empty($row_pos))?(0):($row_pos);
		if (! isset($_SESSION['allowed_domains'])) {
			$query2 = "SELECT * FROM domain ORDER BY domain_name";
		} 
		else {
			$allowed_domains = '';
			foreach ($_SESSION['allowed_domains'] as $allowed_domain) {
			$allowed_domains .= $allowed_domain."' OR domain_name='";
		 }
		$query2 = "SELECT * FROM domain WHERE domain_name='$allowed_domains' ORDER BY domain_name";
		}

	        $result2 = $handle->query($query2);
        	$total=$result2->numRows($result2);

?>


	



		<table border="1" width="98%">
				<?php

				if (! isset($_SESSION['allowed_domains'])) {
					$query = "SELECT * FROM domain ORDER BY domain_name";
				} else {
					$allowed_domains = '';
					foreach ($_SESSION['allowed_domains'] as $allowed_domain) {
						$allowed_domains .= $allowed_domain."' OR domain_name='";
					}
					$query = "SELECT * FROM domain WHERE domain_name='$allowed_domains' ORDER BY domain_name";
//					print $query;
				}

				$result = $handle->limitQuery($query,$row_pos,$_SESSION['maxdisplay']);
				$cnt    = $result->numRows($result);

				print _("Total Domains")." ".$total;
				print "<br>"._("Displaying from position:")." $row_pos";
				
				?>
<!-- 		</table> -->
                <table cellspacing="2" cellpadding="0">
                        <tr>
                                <td class="navi">
                                        <a href="index.php?action=newdomain&domain=new"><?php print _("Add new domain");?></a>
                                </td>

                                <?php
                                $prev = $row_pos - $_SESSION['maxdisplay'];
                                $next = $row_pos + $_SESSION['maxdisplay'];

                                if ($row_pos < $_SESSION['maxdisplay']){
					print "<td class=\"navi\"><a href=\"#\">"._("Previous entries")."</a></td>";
                                } else {
					print "<td class=\"navi\"><a href=\"index.php?action=accounts&domain=$domain&row_pos=$prev\">"._("Previous entries") ."</a></td>";		
                                }

				if ($next>$total){
					print "<td class=\"navi\"><a href=\"#\">"._("Next 10 entries")."</a></td>";
				}
				else {
					print "<td class=\"navi\"><a href=\"index.php?action=accounts&domain=$domain&row_pos=$next\">". _("Next entries")."</a></td>";
				}
                                ?>


                        </tr>
                 </table> 
		<table>


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
                                                <?php print _("max Domain quota");?>
                                        </th>

                                        <th>
                                                <?php print _("default quota per user");?>
                                        </th>
                                </tr>


<?php
				

				for ($c=0; $c < $cnt; $c++){
					if ($c%2==0){
						$cssrow="row1";
					} else {
						$cssrow="row2";
					}

					$row = $result->fetchRow(DB_FETCHMODE_ASSOC,$c);

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
									$_action, $row['domain_name'], $_txt);
								?>
							</td>
							<?php
						}
						?>

						<td>
							<?php echo $row['domain_name'];?>
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
							<!--  Max Domain Quota -->
							<?php
							if (! $row['domainquota'] == 0) {
								echo $row['domainquota'];
							} else {
								print _("Quota not set");
							}
							?>
						</td>
						
						<td>
							<!-- Default Account Quota -->
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
			<p>&nbsp;

<!-- ############################### End browse.php ############################################# -->

