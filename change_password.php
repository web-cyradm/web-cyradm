<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### change_password.php start #################### -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top">

	<?php

	if ($authorized){
		if (!empty($_POST['confirmed']) && ("true" == $_POST['confirmed'])) {
			$query = "select password from accountuser where username='".$_POST['username']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$password = $row['password'];

			if ($PASSWORD_CHANGE_METHOD=="sql"){
				$pwd = new password;
			        $new_password = $pwd->encrypt($_POST['new_password'], $CRYPT);
				$query = "UPDATE accountuser SET password='$new_password' where username='".$_POST['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
		
				# Give some feedback
				if ($result){
					print "<h3>"._("Password changed")."</h3>";
				}
				else {
					print "<h3>"._("Unknown error")."</h3>";
				}	
			} elseif ($PASSWORD_CHANGE_METHOD=="poppassd") {
				require WC_BASE . '/lib/poppassd.php';
				$daemon = new poppassd;
				if ($daemon->change_password($_POST['username'], $password, $_POST['new_password'])) {
					print  "<h3>"._("Password changed")."</h3>";
				} else {
					print $daemon->$err_str;
					print "<h3>"._("Failure in changing password.")."</h3>";
				}
			}
			$_GET['domain'] = $_POST['domain'];
			include WC_BASE . "/browseaccounts.php";	
		}
		if (empty($_POST['confirmed']) || ($_POST['confirmed'] != "true")){
			?>

			<h3>
				<?php print _("Change password for account");?>
				<span style="color: red;">
					<?php echo $_GET['username'];?>
				</span>
			</h3>

			<form action="index.php" method="POST">

				<input type="hidden" name="action" value="change_password">
				<input type="hidden" name="confirmed" value="true">
				<input type="hidden" name="domain" value="<?php echo $_GET['domain'];?>"> 
				<input type="hidden" name="username" value="<?php echo $_GET['username'];?>">

				<table>		

					<tr>
						<td width="150">
							<?php print _("New password");?>:
						</td>
						
						<td>
							<input class="inputfield" type="password" size="30" name="new_password">
						</td>
					</tr>
					
					<tr>
						<td width="150">
							<?php print _("Confirm new password")?>:
						</td>
						
						<td>
							<input class="inputfield" type="password" size="30" name="confirm_password">
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<input class="button" type="submit" value="<?php print _("Submit");?>">
						</td>
					</tr>
				</table>
			</form>
			<?php
		} // End of if (! $confirmed)
	} else { // Not authorized
		?>
		<h3>
			<?php echo $err_msg;?>
		</h3>
		<?php
	} // End of if ($authorized)
?>
</td></tr>

<!-- #################### change_password.php end #################### -->

