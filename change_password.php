<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### change_password.php start #################### -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top">

	<?php

	if ($authorized AND $new_password == $confirm_password){

		$query = "select * from virtual where alias='$alias'";
		$handle = DB::connect($DB['DSN'], true);
		if (DB::isError($handle)){
			die (_("Database error"));
		}

		$result = $handle->query($query);
		$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$alias = $row['alias'];
		$dest = $row['dest'];
		$username = $row['username'];
		if (! empty($confirmed) && ("true" == $confirmed)){
			if ($new_password == $confirm_password && $new_password != ""){
				$query = "select * from accountuser where username='$dest'";
				$handle = DB::connect($DB['DSN'], true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				$result = $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$password = $row['password'];

				if ($PASSWORD_CHANGE_METHOD=="sql"){
					$handle = DB::connect($DB['DSN'], true);
					if (DB::isError($handle)) {
						die (_("Database error"));
					}
						$pwd = new password;
					        $new_password = $pwd->encrypt($new_password, $CRYPT);
						$query = "update accountuser set password='$new_password' where username='$username'";
					$result = $handle->query($query);
					include WC_BASE . "/browseaccounts.php";
				} elseif ($PASSWORD_CHANGE_METHOD=="poppassd"){
					include WC_BASE . '/lib/poppassd.php';
					$daemon = new poppassd;
					if ($daemon->change_password($dest, $password, $new_password)) {
						print  "<em><big>"._("Password changed")."</big></em><p><p>";
					} else {
						print $daemon->$err_str;
						print "<big>"._("Failure in changing password.")."</big><p><p>";
					}
				} elseif ($new_password != $confirm_password){
					print "<b>"._("New passwords are not equal. Password not changed")."</b>";
				}
			}
		}
		if (empty($confirmed) || ($confirmed != "true")){
//			$test = ereg ("",$alias,$result_array);

			if (isset($result_array)){
				print $result_array[0];
			}
			?>

			<h3>
				<?php print _("Change password for account");?>
				<span style="color: red;">
					<?php echo $dest;?>
				</span>
			</h3>

			<!-- <form action="index.php" method="get"> -->
			<form action="index.php" method="POST">

				<input type="hidden" name="action" value="change_password">
				<input type="hidden" name="confirmed" value="true">
				<input type="hidden" name="domain" value="<?php echo $domain ?>"> 
				<input type="hidden" name="alias" value="<?php echo $alias ?>"> 
				<input type="hidden" name="username" value="<?php echo $username;?>">

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
	} else {
		// Not authorized
		if ($new_password != $confirm_password){
			print _("Passwords do not match");
		}
		?>
		<h3>
			<?php echo $err_msg;?>
		</h3>
		<?php
	} // End of if ($authorized)
?>
</td></tr>

<!-- #################### change_password.php end #################### -->

