          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>"._("Define a Account for receiving undefined adresses for domain")." <font color=red>$domain</font></h3>";

// $result=mysql_db_query($MYSQL_DB,$query1,$handle1);



if (!$confirmed){

	?>

<h3><?php print _("Do you really want to define the user")." ".$username." "._("to receive all undefined emailadresses")?></h3>

	
<form action="index.php">
<input type="hidden" name="action" value="catch">
<input type="hidden" name="confirmed" value="true">
<input type="hidden" name="domain" value="<?php print $domain?>">
<input type="hidden" name="username" value="<?php print $username?>">
<input class="button" type="submit" name="confirmed" value="<?php print _("Yes")?>">
<input class="button" type="submit" name="cancel" value="<?php print _("Cancel")?>">
</form>

	<?php

}elseif(($confirmed)and(!$cancel)){

	# First Delete the entry from the database

	$deletequery="DELETE from virtual WHERE alias='@$domain'";

	# And then add the new one	

	$insertquery="INSERT INTO virtual  (alias , dest , username , status) values ('@$domain' , '$username' , '$username' , '1')";
	$handle=DB::connect($DSN, true);
	if (DB::isError($handle)) {
		die (_("Database error"));
	}

	$result=$handle->query($deletequery);
	$result=$handle->query($insertquery);

	if ($result){
		print _("successfully added to Database")."...</br>";
	}
	else{
		print "<p>"._("Database error, please try again")."<p>";
	}

}elseif($cancel){
		print "<p>"._("Cancelled")."<p>";

}
?>

</td></tr>

