<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### deletedomain.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
<?php
if ($authorized) {
	$query = "SELECT * FROM accountuser WHERE domain_name='".$_GET['domain']."' ORDER BY username";
	$result1 = $handle->query($query);
	$cnt1 = $result1->numRows();

			if (empty($_GET['confirmed'])) {
				?>
					<h3>
						<?php print _("Delete a Domain from the System");?>
					</h3>

					<h3>
						<?php print _("Do you really want to delete the Domain");?> 
						<span style="color: red;">
							<?php echo $_GET['domain'];?>
						</span>
						<?php
						print _("with all its defined accounts, admins, and emailadresses");
						?>
						?
					</h3>

					<p>
						<?php print _("This can take a while depending on how many account have to be deleted");?>
					</p>

					<p style="color: red;">
						<?php print _("Your action will delete");?>
						<span style="font-weight: bolder;">
							<?php echo $cnt1;?>
						</span>
						<?php print _("accounts");?>
					</p>


					<form action="index.php" method="get">
						<input type="hidden" name="action" value="deletedomain">
						<input type="hidden" name="confirmed" value="true">
						<input type="hidden" name="domain" value="<?php print $_GET['domain']; ?>">

						<input class="button"
						type="submit" name="confirmed" 
						value="<?php print _("Yes, delete");?>">
						
						<input class="button"
						type="submit" name="cancel"
						value="<?php print _("Cancel");?>">
					</form>
					<?php
			} elseif (!empty($_GET['confirmed']) && !empty($_GET['cancel'])) {
					?>
					<h3>
						<?php print _("Action cancelled, nothing deleted");?>
					</h3>
					<?php
					include WC_BASE . "/browse.php";
			} elseif (!empty($_GET['confirmed']) && empty($_GET['cancel'])) {
					$cyr_conn = new cyradm;
					$cyr_conn->imap_login();

					# First Delete all stuff related to the domain from the database
					$query = "DELETE FROM virtual WHERE alias LIKE '%@".$_GET['domain']."'";
					$result = $handle->query($query);

					$query = "DELETE FROM accountuser WHERE domain_name='".$_GET['domain']."'";
					$result = $handle->query($query);

					$query = "DELETE FROM domain WHERE domain_name='".$_GET['domain']."'";
					$result = $handle->query($query);

					for ($i=0; $i<$cnt1; $i++) {
						$row = $result1->fetchRow(DB_FETCHMODE_ASSOC, $i);
						$username = $row['username'];
						$query = "DELETE FROM virtual WHERE username='".$username."'";
						$result = $handle->query($query);
						# Removing forwards
						$query = "DELETE FROM virtual WHERE alias='".$username."'";
						$result = $handle->query($query);

						# And also delete the Usermailboxes from the cyrus system
						if ($DOMAIN_AS_PREFIX){
							print $cyr_conn->deletemb("user/".$username);
						} else {
							print $cyr_conn->deletemb("user.".$username);
						}

					}

					# Finally the domain must be removed from the domainadmin table
					$query = "SELECT * FROM domainadmin WHERE domain_name='".$_GET['domain']."'";
					$result = $handle->query($query);
					$cnt = $result->numRows();
					for ($i=0; $i < $cnt; $i++) {

						# After getting the resulttable we search for the adminuser 
						# in each row
						$row = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
						$username = $row['adminuser'];

						$query = "SELECT * FROM domainadmin where adminuser='".$username."'";
						$result2 = $handle->query($query);
						$cnt2 = $result1->numRows();

						# If the adminuser is only the admin for the domain to be
						# deleted, then this adminuser also needs to be deleted
						if ($cnt2 == 1){
							$query = "DELETE FROM adminuser where username='".$username."'";
							$result = $handle->query($query);
						}
					}

					# Finally delete every entry with the domain to be deleted
					$query = "DELETE FROM domainadmin where domain_name='".$_GET['domain']."'";
					$result = $handle->query($query);
					?>
					<h3>
						<?php print _("Domain");?>
						<span style="color: red;">
							<?php echo $_GET['domain']; ?>
						</span>
						<?php print _("successfully deleted");?>
					</h3>
					<?php
					unset($_GET['domain']);
					include WC_BASE . "/browse.php";
			} // End If (empty($_GET['confirmed']))
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
<!-- #################### deletedomain.php start #################### -->

