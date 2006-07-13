<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################################### Start aliases.php ################################# -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top">
	<h3><?php print _("Browse aliases for domain")?> <font color=red><?php print( $_GET['domain'] ); ?></font></h3>

	<?php
	if ($authorized) {
		$query = "SELECT COUNT(DISTINCT(alias)) FROM virtual WHERE username = '".$_GET['domain']."'";
		$alias_count = $handle->getOne($query);
		$row_pos = (empty($_GET['row_pos']))?(0):($_GET['row_pos']);
		
		$prev = $row_pos -10;
		$next = $row_pos +10;
		
		if ($row_pos<10) {
			$prev_url = "#";
		} else {
			$prev_url = "index.php?action=aliases&domain=".$_GET['domain']."&row_pos=".$prev;
		}

		if ($next >= $alias_count) {
			$next_url = "#";
		} else {
			$next_url = "index.php?action=aliases&domain=".$_GET['domain']."&row_pos=".$next;
		}

		print _("Total aliases").":".$alias_count;
	?><P>
	<table cellspacing="2" cellpadding="0">
	<tr>
		<td class="navi"><a class="navilink" href="index.php?action=newalias&domain=<?php print $_GET['domain']; ?>">
		<?php print _("Add new alias");?></a></td>
		<td class="navi"><a class="navilink" href="<?php print($prev_url); ?>"><?php print _("Previous 10 entries");?></a></td>
		<td class="navi"><a class="navilink" href="<?php print($next_url); ?>"><?php print _("Next 10 entries");?></a></td>
	</tr>
	</table>
	<p>
	<table border="0">
		<tbody>
		<tr>
			<th colspan="2"><?php print _("action");?></th>
			<th><?php print _("Email address"); ?></th>
			<th><?php print _("Destination"); ?></th>
		</tr>
	<?php

		$query = "SELECT DISTINCT(alias) FROM virtual WHERE username = '".$_GET['domain']."'";
		$result = $handle->limitQuery($query,$row_pos,10);
		$num_alias = $result->numRows();
		$b = 0;
		for($c = 0;$c < $num_alias;$c++) {
			if ($c%2==0){
				$cssrow = "row1";
			} else {
				$cssrow = "row2";
			}
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC,$c);
			$alias = $row['alias'];	
		?>
		<tr class="<?php print($cssrow); ?>">
			<td><a href="index.php?action=editalias&alias=<?php print($alias); ?>&domain=<?php print($_GET['domain']); ?>"><?php print _("Edit Alias"); ?></a></td>
			<td><a href="index.php?action=deletealias&alias=<?php print($alias); ?>&domain=<?php print($_GET['domain']); ?>"><?php print _("Delete Alias"); ?></a></td>
			<td><?php print($alias); ?></td>
			<td>	
		<?php
	
			$query1 = "SELECT dest FROM virtual WHERE alias = '".$alias."'";
			#$result1 = $handle->query($query1);
			$result1 = $handle->Query($query1);
			$cnt = $result1->numRows();
			$num_dest = ($cnt<3)?($cnt):(3);
			for($d = 0;$d < $num_dest;$d++) {
				$row2 = $result1->fetchRow(DB_FETCHMODE_ASSOC,$d);
				if($d != 0) {
					print ", ";
				}
				print($row2['dest']);
			}
			if ($cnt > 3) {
				print ", ... ";
			}
		?>
			</td>
		</tr>
	<?php
		}
	?>
		</table>
<?php
	}
?>
	</td>
</tr>
<!-- ##################################### End aliases.php #################################### -->
