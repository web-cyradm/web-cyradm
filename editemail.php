          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($authorized){

	$query="select * from virtual where alias='$alias'";
	$handle=DB::connect($DSN, true);
	$result=$handle->query($query);
	$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
	$alias=$row['alias'];
	$dest=$row['dest'];
	$username=$row['username'];

	if ($confirmed){

	        $query="UPDATE virtual SET alias='$newalias@$domain', dest='$dest' WHERE alias='$alias'";
	
	        $handle=DB::connect($DSN, true);
	        $result=$handle->query($query);


	        if (!DB::isError($result)){
	                print "<h3>"._("Successfully changed")."</h3>";
			include ("editaccount.php");
	        }
	        else{
	                print "<p>"._("Database error, please try again")."<p>";
	        }

	}



	if (!$confirmed){
//		$test = ereg ("",$alias,$result_array);

		$alias = spliti("@",$alias);
		$alias = $alias[0];

		print $result_array[0];

	        ?>

	        <form action="index.php" method="get">
	
	        <input type="hidden" name="action" value="editemail">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="alias" value="<?php print $alias."@".$domain ?>"> 

	        <table>

	        <tr>
	        <td><?php print _("Emailadress") ?></td>
		<td><input class="inputfield" type="text" size="30" name=newalias value="<?php print $alias?>">@<?php print $domain?></td>
	        </tr>

	        <tr>
	        <td width=150><?php print _("Destination") ?></td>
	        <td><input class="inputfield" type="text" size="30" name=dest value="<?php print $dest ?>"> </td>
	        </tr>


	        <tr><td>
	        <input class="button" type="submit" value="<?php print _("Submit") ?>">
	        </td></tr>

	        </table>
		</form>


	        <?php

	}

}
else{
	print "<h3>".$err_msg."</h3>";
}

?>
</td></tr>


