<!-- #################################### Start editalias.php ################################# -->
<?php

if( isset( $_GET['create'] ) )
{
	$alias = $_GET['alias']."@".$_GET['domain'];
}

$handle=DB::connect($DB['DSN'], true);
if (DB::isError($handle)) {
	die ($handle->getMessage());
}
$domain = $_GET['domain'];
if( isset( $_GET['adddest'] ) )
{
	$dest = $_GET['dest'];
	$query2 = "INSERT INTO virtual (alias,dest,username) values ('$alias', '$dest', '$domain')";
	$result2 = $handle->query( $query2 );
	if( DB::isError( $handle ) )
	{
		print( "There was an error adding $dest to $alias:  ".$handle->getMessage() );
	}
}

$query1 = "SELECT dest, username FROM virtual WHERE alias = '$alias'";
$result1 = $handle->query( $query1 );
$num_aliases = $result1->numRows( $result1 );

if( !isset( $row_pos ) )
{
	$row_pos=0;
}
$prev = $row_pos -10;
$next = $row_pos +10;

if( $row_pos<10 )
{
	$prev_url = "#";
}
else
{
	$prev_url = "index.php?action=editalias&domain=".$domain."&alias=".$alias."&row_pos=".$prev;
}

if( $next > $alias_count )
{
	$next_url = "#";
}
else {
	$next_url = "index.php?action=editalias&domain=".$domain."&alias=".$alias."&row_pos=".$next;
}

?>

<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
	<h3><?php print _("Editing alias"); ?> <font color=red><?php echo $alias ?></font></h3>

	<table cellspacing="2" cellpadding="0">
	<tr>
		<td class="navi"><a href="index.php?action=deletealias&domain=<?php echo $domain ?>&alias=<?php echo $alias ?>"><?php print _("Delete this alias");?></a></td>
		<td class="navi"><a href="<?php print( $prev_url ); ?>"><?php print _("Previous 10 entries");?></a></td>
		<td class="navi"><a href="<?php print( $next_url ); ?>"><?php print _("Next 10 entries");?></a></td>
	</tr>
	</table>
	<p>
	<table border=0>
	<tr>
		<th colspan="1"><?php print _("action"); ?></th>
		<th><?php print _("Destination");?></th>
	</tr>

<?php	
if( $num_aliases != 0 )
{
	$b = 0;
	for( $a = 0; $a < $num_aliases; $a++ )
	{
		if( $b == 0 )
		{
			$cssrow = "row1";
			$b = 1;
		}
		else
		{
			$cssrow = "row2";
			$b = 0;
		}
		
		$row = $result1->fetchRow( DB_FETCHMODE_ASSOC, $a );
		$dest = $row['dest'];
		if( $row['username'] != $domain )
		{
			$action = _("Cannot remove from account")." <b>FIXME:Why not? We should think about reorganize that";
		}
		else
		{
			$action = "<a href=\"index.php?action=deletealias&domain=$domain&alias=$alias&dest=$dest\">". _("Remove destination")."</a>";
		}
?>
	<tr class="<?php echo $cssrow ?>">
		<!-- <td><a href="index.php?action=deletealias&domain=<?php echo $domain ?>&alias=<?php echo $alias ?>&dest=<?php echo $dest ?>">Remove destination</a></td> -->
		<td><?php echo $action ?></td>
		<td><?php echo $dest ?></td>
	</tr>

<?php
	
	}

}
else
{

?>

	<tr>
		<td colspan="4" align="center" bgcolor="red"><?php print _("This alias has no destinations");?></td>
	</tr>

<?php

}

?>

	</table>

	<P>	

	<form action="index.php" method="GET">
	<input type="hidden" name="domain" value="<?php echo $_GET['domain'] ?>">
	<input type="hidden" name="action" value="editalias">
	<input type="hidden" name="alias" value="<?php echo $alias ?>">
	<?php print _("New destination&nbsp"); ?>;<input type="text" name="dest" size="30" maxlength="50" class="inputfield" onFocus="this.style.backgroundColor='#aaaaaa'">&nbsp;
	<input name="adddest" value="<?php print _("Submit");?>" class="button" type="submit">&nbsp;
	<input name="reset" value="<?php echo _("Cancel");?>" class="button" type="reset">
	</form>

	</td>
</tr>
	
<!-- ##################################### End editalias.php ################################## -->
