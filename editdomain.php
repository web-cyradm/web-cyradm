          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($admintype==0){

	if ($confirmed){

	        $query="UPDATE domain SET domain_name='$newdomain', maxaccounts='$maxaccounts',quota='$quota' WHERE domain_name='$domain'";
	
		$query2="UPDATE accountuser SET domain_name='$newdomain' WHERE domain_name='$domain'";

	        $handle=DB::connect ($DSN,true);
	        $result=$handle->query($query);
	        $result2=$handle->query($query2);


	        if (!DB::isError($result)){
	                print _("Successfully changed");
			include ("browse.php");
	        }
	        else{
	                print "<p>"._("Database error, please try again")."<p>";
	        }

	}




	if (!$confirmed){

	        $query="select * from domain where domain_name='$domain'";
	        $handle=DB::connect($DSN,true);
	        $result=$handle->query($query);
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
	        $domain=$row['domain_name'];
	        $prefix=$row['prefix'];
	        $maxaccounts=$row['maxaccounts'];
	        $quota=$row['quota'];

	        ?>

	        <form action="index.php" method="get">

	        <input type="hidden" name="action" value="editdomain">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="id" value="<?php print $id ?>">

	        <table>

	        <tr>
	        <td><?php print _("Domainname") ?></td>
		<td><input class="inputfield" type="text" size="30" name=newdomain value="<?php print $domain?>"></td>
	        </tr>

	        <tr>
	        <td><?php print _("Prefix"). " ". _("(Not yet supported, change will be ignored)") ?></td>
	        <td><input class="inputfield" type="text" size="30"  value="<?php print $prefix ?>"></td>
	        </tr>

	        <tr>
	        <td width=150><?php print _("Maximum Accounts") ?></td>
	        <td><input class="inputfield" type="text" size="4" name=maxaccounts value="<?php print $maxaccounts ?>"> </td>
	        </tr>

	        <tr>
	        <td><?php print _("Default Quota in Kilobytes") ?></td>
	        <td><input class="inputfield" type="text" size="15" name=quota value="<?php print $quota ?>"></td>
	        </tr>

	        <tr><td>
	        <input class="button" type="submit" value=<?php print _("Submit") ?>>
	        </td></tr>

	        </table>
		</form>

	        <?php

	}

}
else{

	print "<h3>"._("Your are not allowed to change domains!")."</h3>";
}


?>
</td></tr>

