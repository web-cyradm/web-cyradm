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
		if ($authorized){
			if (empty($_GET['confirmed'])){
				?>
				<h3>
					<?php print _("Delete an Admin account from the System");?>
				</h3>

				<h3>
					<?php print _("Do you really want to delete the Domain supervisor");?>
					<span style="color: red;">
						<?php echo $_GET['username'];?>
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
					value="<?php echo $_GET['username']; ?>"
					>

					<input
					type="hidden"
					name="domain"
					value="<?php echo $_GET['domain']; ?>"
					>

					<input class="button" type="submit" name="confirmed" value="<?php print _("Yes, delete"); ?>">

					<input class="button" type="submit" name="cancel" value="<?php print _("Cancel"); ?>" >
				</form>
				<?php
			} elseif (!empty($_GET['cancel'])){
				?>
				<h3>
					<?php print _("Action cancelled, nothing deleted");?>
				</h3>
				<?php
				include WC_BASE . "/adminuser.php";
			} else {
				$query = "DELETE FROM adminuser WHERE username='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				# The admin also needs to be deleted from the assigment table
				$query = "DELETE FROM domainadmin WHERE adminuser='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				# The admin also needs to be deleted from the settings table
				$query = "DELETE FROM settings WHERE username='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				?>
				<h3>
					<?php print _("Admin user deleted");?>
					:
					<span style="color: red;">
						<?php echo $_GET['username'];?>
					</span>
				</h3>
				<?php
				include WC_BASE . "/adminuser.php";
			}
		} else {
			?>
			<h3>
				<?php print $err_msg;?>
			</h3>
			<a href="index.php?action=adminuser&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
			<?php
		}
		?>
	</td>
</tr>
<!-- #################### deleteadminuser.php end #################### -->

