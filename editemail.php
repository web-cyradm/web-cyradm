          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($authorized){

	$query="select * from virtual where alias='$alias'";
	$handle=mysql_connect ($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	$result=mysql_db_query($MYSQL_DB,$query,$handle);
	$alias=mysql_result($result,0,"alias");
	$dest=mysql_result($result,0,"dest");
	$username=mysql_result($result,0,"username");

	if ($confirmed){

	        $query="UPDATE virtual SET alias='$newalias@$domain', dest='$dest' WHERE alias='$alias'";
	
	        $handle=mysql_connect ($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	        $result=mysql_db_query($MYSQL_DB,$query,$handle);


	        if ($result){
	                print "<h3>Sucessfully changed</h3>";
			include ("editaccount.php");
	        }
	        else{
	                print "<p>Database error, please try again<p>";
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
	        <td>Emailadress</td>
		<td><input type="text" size="30" name=newalias value="<?php print $alias?>">@<?php print $domain?></td>
	        </tr>

	        <tr>
	        <td width=150>Destination</td>
	        <td><input type="text" size="30" name=dest value="<?php print $dest ?>"> </td>
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
	print "<h3>".$err_msg."</h3>";
}

?>
</td></tr>


