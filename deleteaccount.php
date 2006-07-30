<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### deleteaccount.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php

		if ($authorized){
			if (empty($_GET['confirmed'])){
				?>
				<h3>
					<?php print _("Delete an Account from the System");?>
				</h3>

				<h3>
					<?php print _("Do you really want to delete the user ");?>
					<span style="color: red;">
						<?php echo $_GET['username'];?>
					</span>
					<?php print _("with all its defined Emailadresses");?>
				</h3>

				<form action="index.php">
					<input type="hidden"
					name="action"
					value="deleteaccount">
					
					<input type="hidden"
					name="confirmed"
					value="true">
					
					<input type="hidden"
					name="domain"
					value="<?php print $_GET['domain'];?>">
					
					<input
					type="hidden"
					name="username"
					value="<?php print $_GET['username'];?>">
					
					<input class="button"
					type="submit"
					name="confirmed"
					value="<?php print _("Yes, delete"); ?>">
					
					<input class="button"
					type="submit"
					name="cancel"
					value="<?php print _("Cancel"); ?>">
				</form>

				<?php
			} elseif (! empty($_GET['cancel'])){
				?>
				<h3>
					<?php
						print _("Action cancelled, nothing deleted");
						include WC_BASE . "/browseaccounts.php";
					?>
				</h3>
				<?php
			} else {

				$cyr_conn = new cyradm;
				$error = $cyr_conn->imap_login();

				if ($error != 0){
					die ("Error: " . $error);
				}

				$query ="DELETE FROM virtual WHERE username='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				// Removing forwards
				$query = "DELETE FROM virtual WHERE alias='".$_GET['username']."' AND username=''";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				$query = "DELETE FROM accountuser WHERE username='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				$query = "DELETE FROM log WHERE user='".$_GET['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}

				if ($DOMAIN_AS_PREFIX) {
					print $cyr_conn->deletemb("user/".$_GET['username']);
				} else {
					print $cyr_conn->deletemb("user.".$_GET['username']);
				}
				?>
				<h3>
					<?php print _("User deleted");?>:
					<span style="color: red;">
						<?php echo $_GET['username'];?>
					</span>
				</h3>
				<?php
				include WC_BASE . "/browseaccounts.php";
			}
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
<!-- #################### deleteaccount.php end #################### -->

