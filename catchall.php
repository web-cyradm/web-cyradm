          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>Define a Account for receiving undefined adresses for domain <font color=red>$domain</font></h3>";

$query1="SELECT * from domain WHERE domain_name='$domain'";
$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
$result1=mysql_db_query($MYSQL_DB,$query1,$handle1);


if (!$confirmed){

	$query2="SELECT * FROM accountuser WHERE prefix='$prefix' order by username";


	$result2=mysql_db_query($MYSQL_DB,$query2,$handle1);
	$cnt2=mysql_num_rows($result2);


	?>

	<form action="index.php" action="get">
	<input type="hidden" name="action" value="catch">
	<input type="hidden" name="confirmed" value="true">
	<input type="hidden" name="domain" value="<?php print $domain ?>">
	<table>	
	<?php
			print "<input type=\"hidden\" name=\"username\" value=\"$username\">";
			print "<tr>\n";
			print "<td>Accountname</td>\n";
			print "<td>";
			print "<input type=\"text\" name=\"catch\" value=\"$catch\">";
			print "</td></tr>\n";
	?>

		<tr>
			<td></td>
			<td><input class="inputfield" type="submit"></td>
		</tr>
	

	</table>
	</form>
	<?php

	}

else{
	print "lala";

}


?>

</td></tr>

