<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### deleteemail.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if (empty($confirmed)){
			?>
			<h3>
				<?php print _("Delete emailadress from the System");?>:
				<span style="color: red;">
					<?php echo $alias;?>
				</span>
			</h3>

			<h3>
				<?php print _("Do you really want to delete the emailadress for user");?>
				<span style="color: red;">
					<?php echo $username;?>
				</span>
				?
			</h3>

			<form action="index.php">
				<input type="hidden" name="action"
				value="deleteemail">
				<input type="hidden" name="confirmed"
				value="true">
				<input type="hidden" name="domain"
				value="<?php print $domain?>">
				<input type="hidden" name="username"
				value="<?php print $username?>">
				<input type="hidden" name="alias"
				value="<?php print $alias?>">
				
				<input class="button" type="submit" name="confirmed" value="<?php print _("Yes, delete");?>">
				
				<input class="button" type="submit" name="cancel" value=" <?php print _("Cancel");?>"> </form>
			<?php
		} elseif (! empty($cancel)){
			?>
			<h3>
				<?php print _("Action cancelled, nothing deleted");?>
			</h3>
			<?php
			include WC_BASE . "/editaccount.php";
		} else {
			$handle = DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$query = "delete from virtual where alias='$alias'";
			$result = $handle->query($query);

			?>
			<h3>
				<?php print _("Emailadress deleted.");?>:
				<span style="color: red;">
					<?php echo $alias;?>
				</span>
			</h3>
			<?php
			include WC_BASE . "/editaccount.php";
		}
		?>
	</td>
</tr>
<!-- #################### deleteemail.php end #################### -->

