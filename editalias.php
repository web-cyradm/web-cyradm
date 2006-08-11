<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################################### Start editalias.php ################################# -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">
<?php
if ($authorized) {
	if (!empty($_GET['create'])) {
		$_GET['alias'] = $_GET['alias']."@".$_GET['domain'];
	}
	if (!empty($_GET['adddest'])) {
		$query = "INSERT INTO virtual (alias,dest,username) values ('".$_GET['alias']."', '".$_GET['dest']."', '".$_GET['domain']."')";
		$result = $handle->query($query);
		if (DB::isError($handle)) {
			print _("There was an error adding ".$_GET['dest']." to ".$_GET['alias'].".");
		}
	}

	$query = "SELECT dest, username FROM virtual WHERE alias = '".$_GET['alias']."' AND username<>''";
	$result = $handle->query($query);
	$alias_count = $result->numRows($result);

	if (empty($row_pos)) {
		$row_pos = 0;
	}
	$prev = $row_pos-10;
	$next = $row_pos+10;

	if ($row_pos < 10) {
		$prev_url = "#";
	} else {
		$prev_url = "index.php?action=editalias&domain=".$_GET['domain']."&alias=".$_GET['alias']."&row_pos=".$prev;
	}

	if( $next > $alias_count ) {
		$next_url = "#";
	} else {
		$next_url = "index.php?action=editalias&domain=".$_GET['domain']."&alias=".$_GET['alias']."&row_pos=".$next;
	}
?>
	<h3><?php print _("Editing alias"); ?> <font color=red><?php echo $_GET['alias'] ?></font></h3>

	<table cellspacing="2" cellpadding="0">
	<tr>
		<td class="navi"><a href="index.php?action=deletealias&domain=<?php echo $_GET['domain'] ?>&alias=<?php echo $_GET['alias'] ?>"><?php print _("Delete this alias");?></a></td>
		<td class="navi"><a href="index.php?action=aliases&domain=<?php echo $_GET['domain'] ?>"><?php print _("Back to aliases");?></a></td>
		<td class="navi"><a href="<?php echo $prev_url; ?>"><?php print _("Previous 10 entries");?></a></td>
		<td class="navi"><a href="<?php echo $next_url; ?>"><?php print _("Next 10 entries");?></a></td>
	</tr>
	</table>
	<table border=0>
	<tr>
		<th colspan="1"><?php print _("action"); ?></th>
		<th><?php print _("Destination");?></th>
	</tr>

<?php	
	if ($alias_count != 0) {
		for ($c = 0; $c < $alias_count; $c++) {
			if ($c%2==0){
				$cssrow="row1";
			} else {
				$cssrow="row2";
			}
		
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
			if ($row['username'] != $_GET['domain']) {
				$action = _("Cannot remove from account");
			} else {
				$action = '<a href="index.php?action=deletealias&domain='.$_GET['domain'].'&alias='.$_GET['alias'].'&dest='.$row['dest'].'">'. _("Remove destination").'</a>';
			}
?>
			<tr class="<?php echo $cssrow; ?>">
				<td><?php echo $action; ?></td>
				<td><?php echo $row['dest']; ?></td>
			</tr>
<?php
		}
	} else {
?>
	<tr>
		<td colspan="4" align="center" bgcolor="#b4c6de"><?php print _("This alias has no destinations");?></td>
	</tr>
<?php
	}
?>
	</table>
	<br>

	<form action="index.php" method="GET">
	<input type="hidden" name="action" value="editalias">
	<input type="hidden" name="domain" value="<?php echo $_GET['domain'];?>">
	<input type="hidden" name="alias" value="<?php echo $_GET['alias'];?>">
	<?php print _("New destination") ?>:
	<input type="text" name="dest" size="30" maxlength="50" class="inputfield" onFocus="this.style.backgroundColor='#aaaaaa'">&nbsp;
	<input name="adddest" value="<?php print _("Submit");?>" class="button" type="submit">&nbsp;
	<input name="reset" value="<?php echo _("Cancel");?>" class="button" type="reset">
	</form>
<?php
} else { // if ($authorized)
?>
	<h3>
		<?php echo $err_msg;?>
	</h3>
	<a href="index.php?action=editalias&domain=<?php echo $_GET['domain'];?>&alias=<?php echo $_GET['alias'];?>"><?php print _("Back");?></a>
<?php
}
?>
	</td>
</tr>
<!-- ##################################### End editalias.php ################################## -->
