<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### delete_catchall.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

<?php
	if ($authorized) {
		if (!empty($_GET['confirmed']) AND empty($_GET['cancel'])){
			$query = "DELETE from virtual WHERE alias='@".$_GET['domain']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			} else {
				?>
				<h3>
					<?php print _("successfully deleted from the Database");?>
				</h3>
				<?php
				include WC_BASE . "/browseaccounts.php";
			}
		} elseif (!empty($_GET['cancel'])){
			?>
			<h3>
				<?php print _("Cancelled");?>
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
<!-- #################### delete_catchall.php end #################### -->

