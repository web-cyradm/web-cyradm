          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>Define a Account for receiving undefined adresses for domain <font color=red>$domain</font></h3>";

#$result=mysql_db_query($MYSQL_DB,$query1,$handle1);


if (!$confirmed){



	?>

<h3>Do you really want to define the user <?php print $username ?> to receive all undefined emailadresses?</h3>

	
<form action="index.php">
<input type="hidden" name="action" value="catch">
<input type="hidden" name="confirmed" value="true">
<input type="hidden" name="domain" value="<?php print $domain?>">
<input type="hidden" name="username" value="<?php print $username?>">
<input type="submit" name="confirmed" value="Yes">
<input type="submit" name="cancel" value="Cancel">
</form>

	<?php

	}

else{

	$deletequery="DELETE from virtual WHERE alias='@$domain'";
	$insertquery="INSERT INTO virtual  (alias , dest , username , status) values ('@$domain' , '$username' , '$username' , '1')";
	$handle=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	$result=mysql_query($deletequery,$handle);
	$result=mysql_query($insertquery,$handle);

	if ($result){
		print "successfully added to Database....</br>";
	}
	else{
		print "<p>Database error, please try again<p>";
	}

}


?>

</td></tr>

