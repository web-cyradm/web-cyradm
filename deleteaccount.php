    <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($authorized){

if (!$confirmed){

?>
<h3>Delete an Account from the System</h3>

<h3>Do you really want to delete the user <?php print $username ?> with all its defined Emailadresses?</h3>

<form action="index.php">
<input type="hidden" name="action" value="deleteaccount">
<input type="hidden" name="confirmed" value="true">
<input type="hidden" name="domain" value="<?php print $domain?>">
<input type="hidden" name="username" value="<?php print $username?>">
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

$query2="delete from virtual where username='$username'";
$hnd2=mysql_db_query($MYSQL_DB,$query2);

$query3="delete from accountuser where username='$username'";
$hnd3=mysql_db_query($MYSQL_DB,$query3);

$cyr_conn = new cyradm;
$cyr_conn -> imap_login();

if ($DOMAIN_AS_PREFIX) {
	print $cyr_conn -> deletemb("user/".$username);
}
else {
	print $cyr_conn -> deletemb("user.".$username);
}

include ("browseaccounts.php");


}

}
else{

	print "<h3>Security violation detected, nothing deleted, attempt has been logged.</h3>";
}

?>
</td></tr>


