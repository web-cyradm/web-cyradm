<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### delete_catchall.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if (! empty($confirmed) AND empty($cancel)){

			# First Delete the entry from the database

			$deletequery = "DELETE from virtual WHERE alias='@$domain'";

			# And then add the new one

			$handle=DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result = $handle->query($deletequery);

			if ($result){
				?>
				<h3>
					<?php print _("successfully deleted from the Database");?>
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
<!-- #################### delete_catchall.php end #################### -->

