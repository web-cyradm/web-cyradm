          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if (!$confirmed){

?>
<h3>Delete an emailadress from the System</h3>

<h3>Do you really want to delete the emailadress for user <?php print $username ?>?</h3>

<form action="index.php">
<input type="hidden" name="action" value="deleteemail">
<input type="hidden" name="confirmed" value="true">
<input type="hidden" name="domain" value="<?php print $domain?>">
<input type="hidden" name="username" value="<?php print $username?>">
<input type="hidden" name="alias" value="<?php print $alias?>">
<input type="submit" name="confirmed" value="Yes, delete">
<input type="submit" name="cancel" value="Cancel">
</form>




<?php

}

else if ($cancel){
	print "<h3>Action cancelled, nothing deleted</h3>";
	include ("editaccount.php");
}

else{

$handle=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
$query="delete from virtual where alias='$alias'";
$result=mysql_db_query($MYSQL_DB,$query,$handle);

include ("editaccount.php");


}


?>
</td></tr>

