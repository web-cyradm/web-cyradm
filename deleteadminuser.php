    <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($admintype==0){

	if (!$confirmed){

	?>
	<h3>Delete an Admin account from the System</h3>

	<h3>Do you really want to delete the Domain supervisor  "<?php print $username ?>" ?</h3>

	<form action="index.php">
	<input type="hidden" name="action" value="deleteadminuser">
	<input type="hidden" name="confirmed" value="true">
	<input type="hidden" name="username" value="<?php print $username?>">
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

	$handle=DB::connect($DSN,true);

	$query2="DELETE FROM adminuser WHERE username='$username'";
	$hnd2=$handle->query($query2);

	$query3="DELETE FROM domainadmin WHERE adminuser='$username'";
	$hnd3=$handle->query($query3);

	include ("adminuser.php");


	}

}
else{

	print "<h3>Security violation detected, nothing deleted, attempt has been loggd</h3>";
}

?>
</td></tr>

