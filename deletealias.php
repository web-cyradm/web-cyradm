<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################################### Start deletealias.php ################################# -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

<?php
if ($authorized) {
	if(empty($_GET['confirmed'])) {
		if (!empty( $_GET['dest'])) {
			// Removing a destination from an alias
?>
			<form action="index.php" method="GET">
			<input type="hidden" name="action" value="deletealias">
			<input type="hidden" name="confirmed" value="true">
			<input type="hidden" name="dest" value="<?php echo $_GET['dest'] ?>">
			<input type="hidden" name="alias" value="<?php echo $_GET['alias'] ?>">
			<input type="hidden" name="domain" value="<?php echo $_GET['domain'] ?>">
			<?php print _("Please confirm you want to remove")?> <b><?php echo $_GET['dest'] ?></b> <?php print _("from the alias");?> <b><?php echo $_GET['alias'] ?></b><br>
			<input name="submit" class="button" value="<?php print _("Yes");?>" type="submit">&nbsp;
			<input name="cancel" class="button" value="<?php print _("Cancel");?>" type="submit">
			</form>
<?php

		} else {
			// Removing the entire alias
?>
			<form action="index.php" method="GET">
			<input type="hidden" name="action" value="deletealias">
			<input type="hidden" name="confirmed" value="true">
			<input type="hidden" name="alias" value="<?php echo $_GET['alias'] ?>">
			<input type="hidden" name="domain" value="<?php echo $_GET['domain'] ?>">
			<?php print _("Please confirm you want to remove the alias");?> <b><?php echo $_GET['alias'] ?></b><br>
			<input name="submit" class="button" value="<?php print _("Yes");?>" type="submit">&nbsp;
			<input name="cancel" class="button" value="<?php print _("Cancel");?>" type="submit">
			</form>
<?php
		}
	} elseif (!empty($_GET['confirmed']) && !empty($_GET['cancel'])) {
		if (!empty($_GET['dest'])) {
			include WC_BASE . "/editalias.php";
		} else {
			include WC_BASE . "/aliases.php";
		}
	} else {
		if (!empty($_GET['dest'])) {
			// Remove a destination
			$query = "DELETE FROM virtual WHERE alias='".$_GET['alias']."' AND dest='".$_GET['dest']."' AND username = '".$_GET['domain']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			} else {
				print _("Removed")." <b>".$_GET['dest']."</b> "._("from")." <b>".$_GET['alias']."</b>.\n";
				include WC_BASE . "/editalias.php";
			}
		} else {
			// Removing an entire alias
			$query = "DELETE FROM virtual WHERE alias = '".$_GET['alias']."' AND username = '".$_GET['domain']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			} else {
				print _("Removed the alias")." <b>".$_GET['alias']."</b>\n";
				include WC_BASE . "/aliases.php";
			}
		}
	}
} else {
?>
			<h3>
				<?php echo $err_msg;?>
			</h3>
<?php
	if (!empty($_GET['dest'])) {
		print '<a href="index.php?action=editalias&domain='.$_GET['domain'].'&alias='.$_GET['alias'].'&dest='.$_GET['dest'].'">'. _("Back").'</a>';
	} else {
		print '<a href="index.php?action=aliases&domain='.$_GET['domain'].'&alias='.$_GET['alias'].'">'. _("Back").'</a>';
	}
}
?>
	</td>
</tr>	


	
<!-- ##################################### End newalias.php ################################## -->
