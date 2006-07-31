<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### deleteemail.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
<?php
if ($authorized) {
		if (empty($_GET['confirmed'])){
			?>
			<h3>
				<?php print _("Delete emailadress from the System");?>:
				<span style="color: red;">
					<?php echo $_GET['alias'];?>
				</span>
			</h3>

			<h3>
				<?php print _("Do you really want to delete the emailadress for user");?>
				<span style="color: red;">
					<?php echo $_GET['username'];?>
				</span>
				?
			</h3>

			<form action="index.php" method="get">
				<input type="hidden" name="action" value="deleteemail">
				<input type="hidden" name="confirmed" value="true">
				<input type="hidden" name="domain" value="<?php print $_GET['domain'];?>">
				<input type="hidden" name="username" value="<?php print $_GET['username'];?>">
				<input type="hidden" name="alias" value="<?php print $_GET['alias'];?>">
				<input class="button" type="submit" name="confirmed" value="<?php print _("Yes, delete");?>">
				
				<input class="button" type="submit" name="cancel" value=" <?php print _("Cancel");?>">
			</form>
		<?php
		} elseif (!empty($_GET['confirmed']) && !empty($_GET['cancel'])) {
		?>
			<h3>
				<?php print _("Action cancelled, nothing deleted");?>
			</h3>
		<?php
			include WC_BASE . "/editaccount.php";
		} else {
			$query = "DELETE FROM virtual WHERE alias='".$_GET['alias']."' AND username='".$_GET['username']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}

			#TODO: Removing forwards from sieve script
			?>
			<h3>
				<?php print _("Emailadress deleted.");?>:
				<span style="color: red;">
					<?php echo $_GET['alias'];?>
				</span>
			</h3>
		<?php
			include WC_BASE . "/editaccount.php";
		}
} else {
?>
		<h3>
			<?php echo $err_msg;?>
		</h3>
		<a href="index.php?action=editaccount&domain=<?php echo $_GET['domain'];?>&username=<?php echo $_GET['username'];?>"><?php print _("Back");?></a>
<?php
}
?>
	</td>
</tr>
<!-- #################### deleteemail.php end #################### -->

