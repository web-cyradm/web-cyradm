<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################################### Start aliases.php ################################# -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top">
	<h3><?php print _("Browse aliases for domain")?> <font color=red><?php print( $_GET['domain'] ); ?></font></h3>

<?php

$domain = $_GET['domain'];

$handle=DB::connect($DB['DSN'], true);
if (DB::isError($handle)) {
	die ($handle->getMessage());
}
$query1 = "SELECT COUNT( DISTINCT( alias ) ) FROM virtual WHERE username = '$domain'";
$alias_count = $handle->getOne( $query1 );

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
	$prev_url = "index.php?action=aliases&domain=".$domain."&row_pos=".$prev;
}

if( $next > $alias_count )
{
	$next_url = "#";
}
else {
	$next_url = "index.php?action=aliases&domain=".$domain."&row_pos=".$next;
}



	print _("Total aliases").":".$alias_count;
	?><P>
	<table cellspacing="2" cellpadding="0">
	<tr>
		<td class="navi"><a href="index.php?action=newalias&domain=<?php print $domain; ?>">
		<?php print _("Add new alias");?></a></td>
		<td class="navi"><a href="<?php print( $prev_url ); ?>"><?php print _("Previous 10 entries");?></a></td>
		<td class="navi"><a href="<?php print( $next_url ); ?>"><?php print _("Next 10 entries");?></a></td>
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

$query2 = "SELECT DISTINCT( alias ) FROM virtual WHERE username = '$domain'";
$result2 = $handle->limitQuery( $query2, $row_pos, 10 );
$num_alias = $result2->numRows( $result2 );
$b = 0;
for( $c = 0; $c < $num_alias; $c++ )
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
	$row = $result2->fetchRow( DB_FETCHMODE_ASSOC, $c );
	$alias = $row['alias'];
	
	?>
		<tr class="<?php print( $cssrow ); ?>">
			<td><a href="index.php?action=editalias&alias=<?php print( $alias ); ?>&domain=<?php print( $domain ); ?>"><?php print _("Edit Alias"); ?></a></td>
			<td><a href="index.php?action=deletealias&alias=<?php print( $alias ); ?>&domain=<?php print( $domain ); ?>"><?php print _("Delete Alias"); ?></a></td>
			<td><?php print( $alias ); ?></td>
			<td>
	
	<?php
	
	$query3 = "SELECT dest FROM virtual WHERE alias = '$alias'";
	#$result3 = $handle->query( $query3 );
	$result3 = $handle->limitQuery( $query3, 0, 3 );
	$num_dest = $result3->numRows( $result3 );
	for( $d = 0; $d < $num_dest; $d++ )
	{
		$row2 = $result3->fetchRow( DB_FETCHMODE_ASSOC, $d );
		if( $d != 0 )
		{
			print ", ";
		}
		print( $row2['dest'] );
	}
	$query4 = "SELECT COUNT( dest ) FROM virtual WHERE alias = '$alias'";
	$num_dests = $handle->getOne( $query4 );
	if( $num_dests > 3 )
	{
		print ", ... ";
	}
	?>
			</td>
		</tr>
	
	<?php
}

?>
		</table>
	</td>
</tr>

<!-- ##################################### End aliases.php #################################### -->
