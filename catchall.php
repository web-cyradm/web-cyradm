<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### catchall.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<h3>
			<?php print _("Define a Account for receiving undefined adresses for domain");?>
			<span style="color: red;">
				<?php echo $domain;?>
			</span>
		</h3>
		<?php

		if (empty($confirmed)){
			$query = "SELECT * from virtual WHERE alias='@$domain'";
			$handle=DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result = $handle->query($query);
			$cnt = $result->numRows();
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$current_username=$row['username'];

			if (empty($cnt) OR $current_username != $username){



				?>
				<h3>
					<?php print _("Do you really want to define the user");?>
					<span style="color: red;">
					<?php echo $username;?>
					</span>
					<?php print _("to receive all undefined emailadresses");
					print "?";
					print "</h3>";
					if ($cnt==1){
						print _("The currently defined user")."&nbsp;".$current_username."&nbsp;"._("will not receiving any undefined emailadresses anymore");
					}
				?>

				<form action="index.php" method="get">
					<input type="hidden" name="action"
					value="catch">

					<input type="hidden" name="confirmed"
					value="true">

					<input type="hidden" name="domain"
					value="<?php print $domain;?>">

					<input type="hidden" name="username"
					value="<?php print $username;?>">

					<input class="button" type="submit"
					name="confirmed"
					value="<?php print _("Yes");?>">

					<input class="button" type="submit"
					name="cancel"
					value="<?php print _("Cancel");?>">
				</form>
				<?php
			}
			else if ($cnt==1 AND $current_username==$username){
				print _("The user")." ".$current_username." "._("is allready defined to receive all undefined Emails.");
				print "<h3>";
				print _("Do you want to remove the function \"catch all\" for the account");
				print " &nbsp;".$current_username;
				print "?";

				?>
				<form action="index.php" method="get">
					<input type="hidden" name="action"
					value="delete_catchall">

					<input type="hidden" name="confirmed"
					value="true">

					<input type="hidden" name="domain"
					value="<?php print $domain;?>">

					<input type="hidden" name="username"
					value="<?php print $username;?>">

					<input class="button" type="submit"
					name="confirmed"
					value="<?php print _("Yes");?>">

					<input class="button" type="submit"
					name="cancel"
					value="<?php print _("Cancel");?>">
				</form>
			<?php

			}

		} elseif (! empty($confirmed) AND empty($cancel)){

			# First Delete the entry from the database

			$deletequery = "DELETE from virtual WHERE alias='@$domain'";

			# And then add the new one

			$insertquery = "INSERT INTO virtual (alias, dest, username, status) values ('@$domain' , '$username' , '$username' , '1')";

			$handle=DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result = $handle->query($deletequery);
			$result = $handle->query($insertquery);

			if ($result){
				?>
				<h3>
					<?php print _("successfully added to Database");?>
				</h3>
				<?php
			} else {
				?>
				<h3>
					<?php print _("Database error, please try again");?>
				</h3>
				<?php
			}
		} elseif (! empty($cancel)){
			?>
			<h3>
				<?php print _("Cancelled");?>
			</h3>
			<?php
		}
		?>
	</td>
</tr>
<!-- #################### catchall.php end #################### -->

