    <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($admintype==0){

	if (!$confirmed){

	?>
	<h3>Delete an Admin account from the System</h3>

	<h3>Do you really want to delete the Domain supervisor  "<?php print $adminuser ?>" ?</h3>

	<form action="index.php">
	<input type="hidden" name="action" value="deleteadminuser">
	<input type="hidden" name="confirmed" value="true">
	<input type="hidden" name="adminuser" value="<?php print $adminuser?>">
	<input type="hidden" name="domain" value="<?php print $domain?>">
	<input type="submit" name="confirmed" value="Yes, delete">
	<input type="submit" name="cancel" value="Cancel">
	</form>




	<?php

	}

	else if ($cancel){
		print "<h3>Action cancelled, nothing deleted</h3>";
	}

	else{

	$handle=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);

	$query2="DELETE FROM adminuser WHERE username='$adminuser'";
	$hnd2=mysql_db_query($MYSQL_DB,$query2);

	$query3="DELETE FROM domainadmin WHERE username='$adminuser'";
	$hnd3=mysql_db_query($MYSQL_DB,$query3);

	include ("adminuser.php");


	}

}
else{

	print "<h3>Security violation detected, nothing deleted, attempt has been loggd</h3>";
}

?>
</td></tr>

