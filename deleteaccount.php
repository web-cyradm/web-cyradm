    <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($authorized){

if (!$confirmed){

?>
<h3><?php print _("Delete an Account from the System") ?></h3>

<h3><?php print _("Do you really want to delete the user ")." <font color=\"red\">".$username."</font> "._("with all its defined Emailadresses")?>?</h3>

<form action="index.php">
<input type="hidden" name="action" value="deleteaccount">
<input type="hidden" name="confirmed" value="true">
<input type="hidden" name="domain" value="<?php print $domain?>">
<input type="hidden" name="username" value="<?php print $username?>">
<input class="button" type="submit" name="confirmed" value="<?php print _("Yes, delete") ?>">
<input class="button" type="submit" name="cancel" value="<?php print _("Cancel") ?>">
</form>




<?php

}

else if ($cancel){
	print "<h3>"._("Action cancelled, nothing deleted")."</h3>";
}

else{

$cyr_conn = new cyradm;
$error=$cyr_conn -> imap_login();

if ($error!=0){
	die ("Error $error");
}

$handle=DB::connect($DSN, true);
if (DB::isError($handle)) {
	die (_("Database error"));
}


$query2="delete from virtual where username='$username'";
$hnd2=$handle->query($query2);

$query3="delete from accountuser where username='$username'";
$hnd3=$handle->query($query3);


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

	print "<h3>"._("Security violation detected, action cancelled. Your attempt has been logged.")."</h3>";
}

?>
</td></tr>


