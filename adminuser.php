<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- ############################## Start adminuser.php ###################################### -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">
		<h3>
			<?php print _("Browse admins");?>
		</h3>

		<?php
		if ($_SESSION['admintype'] == 0){
			$query = "SELECT * FROM adminuser";
			$handle = DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}
			$row_pos = (empty($row_pos))?(0):($row_pos);

			# Fist query is for displaying 
			$result = $handle->limitQuery($query,$row_pos,10);

			# second query is needed to get the total of administrators
			$result2 = $handle->Query($query);
			$cnt = $result->numRows();

			$total = $result2->numRows();
			?>
			<p>
				<?php print _("Total administrators") . ": " . $total;
				print "<br>"._("Displaying from position:")." $row_pos";
				?>
			</p>

			<table cellspacing="2" cellpadding="0">
				<tr>
					<td class="navi">
						<a href="index.php?action=newadminuser&amp;domain=<?php echo $domain; ?>"><?php print _("Add administator"); ?></a>
					</td>

					<?php
					$prev = $row_pos - 10;
					$next = $row_pos + 10;

					if ($row_pos < 10){
						$_linkP = '#';
					} else {
						$_linkP = 'index.php?action=adminuser&amp;domain=' . $domain . '&amp;row_pos=' . $prev;
					}
					if ($next > $total){
						$_linkN = '#';
					} else {
						$_linkN = 'index.php?action=adminuser&amp;domain=' . $domain . '&amp;row_pos=' . $next;
					}
					?>
					<td class="navi">
						<a href="<?php echo $_linkP; ?>"><?php print _("Previous 10 entries");?></a>
					</td>

					<td class="navi">
						<a href="<?php echo $_linkN; ?>"><?php print _("Next 10 entries");?></a>
					</td>
				</tr>
			</table>

			<table border="0">
				<tbody>
					<tr>
						<th colspan="2">
							<?php print _("action");?>
						</th>

						<th>
							<?php print _("Adminname");?>
						</th>

						<th>
							<?php print _("domain");?>
						</th>

						<th>
							<?php print _("admin type");?>
						</th>
					</tr>

					<?php
					for ($c = 0; $c < $cnt; $c++){
						if ($c%2==0){
							$cssrow = "row1";
						} else {
							$cssrow = "row2";
						}

						$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
						$username = $row['username'];
						$query2 = "SELECT * from domainadmin WHERE adminuser='$username'";
						$result2 = $handle->query($query2);
						$cnt2 = $result2->numRows();

						$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
						$domainname = $row2['domain_name'];
						$type = $row['type'];

						?>

						<tr class="<?php echo $cssrow;?>">
							<td valign="middle">
								<a href="index.php?action=editadminuser&username=<?php echo $username; ?>&domain=<?php echo $domain; ?>&row_pos=<?php print $row_pos;?>"><?php
								print _("Edit adminuser");
								?></a>
							</td>

							<td valign="middle">
								<a href="index.php?action=deleteadminuser&amp;username=<?php echo $username; ?>&amp;domain=<?php echo $domain; ?>"><?php
								print _("Delete adminuser");
								?></a>
							</td>

							<td valign="middle">
								<?php echo $username;?>
							</td>

							<td valign="middle">
								<?php
								for ($i = 0; $i < $cnt2; $i++){
									$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, $i);
									$domainname = $row2['domain_name'];
									if ($type){
										print "$domainname<br>";
									}
								}

								if ($type==0){
									print _("All domains");
								}
								if ($domainname=="" AND $type!=0){
									print "<font color=\"red\">";
									print _("None");
									print "</font>";
								}

								?>
							</td>

							<td valign="middle">
								<?php
								if ($type == 0){
									print _("Superuser");
								} elseif ($type == 1){
									print _("Domain Master");
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
		}
		?>
	</td>
</tr>
<!-- ############################### End adminuser.php ############################################# -->

