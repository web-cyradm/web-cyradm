          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($admintype==0){

	if ($confirmed){

	        $query="UPDATE domain SET domain_name='$newdomain', maxaccounts='$maxaccounts',quota='$quota' WHERE domain_name='$domain'";
	
		$query2="UPDATE accountuser SET domain_name='$newdomain' WHERE domain_name='$domain'";

	        $handle=mysql_connect ($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	        $result=mysql_db_query($MYSQL_DB,$query,$handle);
	        $result2=mysql_db_query($MYSQL_DB,$query2,$handle);


	        if ($result){
	                print "Sucessfully changed";
			include ("browse.php");
	        }
	        else{
	                print "<p>Database error, please try again<p>";
	        }

	}




	if (!$confirmed){

	        $query="select * from domain where domain_name='$domain'";
	        $handle=mysql_connect ($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	        $result=mysql_db_query($MYSQL_DB,$query,$handle);
	        $domain=mysql_result($result,0,"domain_name");
	        $prefix=mysql_result($result,0,"prefix");
	        $maxaccounts=mysql_result($result,0,"maxaccounts");
	        $quota=mysql_result($result,0,"quota");

	        ?>

	        <form action="index.php" method="get">

	        <input type="hidden" name="action" value="editdomain">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="id" value="<?php print $id ?>">

	        <table>

	        <tr>
	        <td>Domain</td>
		<td><input type="text" size="30" name=newdomain value="<?php print $domain?>"></td>
	        </tr>

	        <tr>
	        <td>Prefix (Not yet supported, change will be ignored)</td>
	        <td><input type="text" size="30"  value="<?php print $prefix ?>"></td>
	        </tr>

	        <tr>
	        <td width=150>Maximum Accounts</td>
	        <td><input type="text" size="4" name=maxaccounts value="<?php print $maxaccounts ?>"> </td>
	        </tr>

	        <tr>
	        <td>Default Quota in Kilobytes</td>
	        <td><input type="text" size="15" name=quota value="<?php print $quota ?>"></td>
	        </tr>

	        <tr><td>
	        <input type="submit" value="Accept Changes">
	        </td></tr>

	        </table>
		</form>

	        <?php

	}

}
else{

	print "<h3>Your are not allowed to change domains!</h3>";
}


?>
</td></tr>

