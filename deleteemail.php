          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if (!$confirmed){

?>
<h3><?php print _("Delete an emailadress from the System") ?></h3>

<h3><?php print _("Do you really want to delete the emailadress for user") ?> <?php print $username ?>?</h3>

<form action="index.php">
<input type="hidden" name="action" value="deleteemail">
<input type="hidden" name="confirmed" value="true">
<input type="hidden" name="domain" value="<?php print $domain?>">
<input type="hidden" name="username" value="<?php print $username?>">
<input type="hidden" name="alias" value="<?php print $alias?>">
<input type="submit" name="confirmed" value="<?php print _("Yes, delete") ?>">
<input type="submit" name="cancel" value="<?php print _("Cancel") ?>">
</form>




<?php

}

else if ($cancel){
	print "<h3>"._("Action cancelled, nothing deleted")."</h3>";
	include ("editaccount.php");
}

else{

$handle=DB::connect($DSN, true);
$query="delete from virtual where alias='$alias'";
$result=$handle->query($query);

include ("editaccount.php");


}


?>
</td></tr>

