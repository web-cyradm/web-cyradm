<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### deleteadminuser.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if ($admintype==0){
			if (empty($confirmed)){
				?>
				<h3>
					<?php print _("Delete an Admin account from the System");?>
				</h3>

				<h3>
					<?php print _("Do you really want to delete the Domain supervisor");?>
					<span style="color: red;">
						<?php echo $username;?>
					</span>
				</h3>

				<form action="index.php" method="get">
					<input
					type="hidden"
					name="action"
					value="deleteadminuser"
					>

					<input
					type="hidden"
					name="confirmed"
					value="true"
					>

					<input
					type="hidden"
					name="username"
					value="<?php echo $username; ?>"
					>

					<input
					type="hidden"
					name="domain"
					value="<?php echo $domain; ?>"
					>

					<input class="button" type="submit" name="confirmed" value="<?php print _("Yes, delete"); ?>">

					<input class="button" type="submit" name="cancel" value="<?php print _("Cancel"); ?>" >
				</form>
				<?php
			} elseif (! empty($cancel)){
				?>
				<h3>
					<?php print _("Action cancelled, nothing deleted");?>
				</h3>
				<?php
			} else {
				$handle=DB::connect($DB['DSN'],true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				#Determine what type of admin should be deleted
				$query="SELECT type FROM adminuser WHERE username='$username'";
				$result= $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$type= $row['type'];

				# Get the count of actual supersusers
				$query="SELECT type FROM adminuser WHERE type='0'";
				$result = $handle->query($query);
				$cnt=$result->numRows();

				if ($cnt==1 && $type==0){
					# No Way! We cannot change the last Superuser to domainadmin!
					die (_("At least one Superuser is needed for Web-cyradm"));
				}

				# If not died, delete that brave admin
				$query2 = "DELETE FROM adminuser WHERE username='$username'";
				$hnd2 = $handle->query($query2);

				# The admin also needs to be deleted from the assigment table
				$query3 = "DELETE FROM domainadmin WHERE adminuser='$username'";
				$hnd3 = $handle->query($query3);

				?>
				<h3>
					<?php print _("Admin user deleted");?>
					:
					<span style="color: red;">
						<?php echo $username;?>
					</span>
				</h3>
				<?php

				include WC_BASE . "/adminuser.php";
			}
		} else {
			?>
			<h3>
				<?php print _("Security violation detected, nothing deleted, attempt has been logged");?>
			</h3>
			<?php
		}
		?>
	</td>
</tr>
<!-- #################### deleteadminuser.php end #################### -->

