<!-- #################################### Start newalias.php ################################# -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

<?php

if( ! isset( $_GET['confirmed'] ) )
{
	if( isset( $_GET['dest'] ) )
	{
		// Removing a destination from an alias

?>


	<form action="index.php" method="GET">
	<input type="hidden" name="action" value="deletealias">
	<input type="hidden" name="dest" value="<?php echo $_GET['dest'] ?>">
	<input type="hidden" name="alias" value="<?php echo $_GET['alias'] ?>">
	<input type="hidden" name="domain" value="<?php echo $_GET['domain'] ?>">
	<?php print _("Please confirm you want to remove")?> <b><?php echo $_GET['dest'] ?></b> <?php print _("from the alias");?> <b><?php echo $_GET['alias'] ?></b>&nbsp;<p>
	<input name="confirmed" class="button" value="<?php print _("Yes");?>" type="submit" class="inputclass">&nbsp;
	<input name="no" class="button" value="<?php print _("Cancel");?>" type="button" class="inputclass" onClick="history.go(-1)">
	</form>
	</td>
</tr>	
<?php

	}
	else
	{
		// Removing the entire alias

?>

	<form action="index.php" method="GET">
	<input type="hidden" name="action" value="deletealias">
	<input type="hidden" name="alias" value="<?php echo $_GET['alias'] ?>">
	<input type="hidden" name="domain" value="<?php echo $_GET['domain'] ?>">
	<?php print _("Please confirm you want to remove the alias");?> <b><?php echo $_GET['alias'] ?></b>&nbsp;
	<input name="confirmed" class="button" value="<?php print _("Yes");?>" type="submit" class="inputclass">&nbsp;
	<input name="no" class="button" value="<?php print _("Cancel");?>" type="button" class="inputclass" onClick="history.go(-1)">
	</form>
	</td>
</tr>	
<?php

	}

}
else
{
	// $confirmed is set, so do the dirty work
	
	$handle=DB::connect($DSN, true);
	if (DB::isError($handle)) {
		die ($handle->getMessage());
	}
	$domain = $_GET['domain'];
	$alias = $_GET['alias'];

	if( isset( $dest ) )
	{
		// Remove a destination
		$dest = $_GET['dest'];
		$query1 = "DELETE FROM virtual WHERE alias = '$alias' AND dest = '$dest' AND username = '$domain'";
		$result1 = $handle->query( $query1 );
		print( _("Removed")." <b>$dest</b> "._("from")." <b>$alias</b>.\n" );
		include( "editalias.php" );
	}
	else
	{
		// Removing an entire alias
		$query2 = "DELETE FROM virtual WHERE alias = '$alias' AND username = '$domain'";
		$result2 = $handle->query( $query2 );
		print( _("Removed the alias")." <b>$alias</b>\n" );
		include( "aliases.php" );
	}
}

?>


	
<!-- ##################################### End newalias.php ################################## -->
