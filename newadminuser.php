          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>Add new admin user for domain $domain</h3>";
if (!$domain or $domain=="new"){
	print "Please select a domain first";
}
else{

	if ($admintype==0){

		if (!$confirmed){

		?>

		<form action="index.php" action="get">
		<input type="hidden" name="action" value="newadminuser">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="domain" value="<?php print $domain ?>">

		<table>
			<tr>
				<td>login</td>
				<td><input class="inputfield" type="text" name="adminuser" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>
		
			<tr>
				<td>Type</td>
				<td><select name="type">
					<option value=0>Superuser</option>
					<option selected value=1>Domain supervisor</option>
				</select> Select "Superuser" for all domains

		
			<tr>
				<td>Password</td>
				<td><input class="inputfield" type="password" name="password" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>

			<tr>
				<td>Confirm Password</td>
				<td><input class="inputfield" type="password" name="confirm_password" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>
	
			<tr>
				<td></td>
				<td><input class="inputfield" type="submit"></td>
			</tr>
	

		</table>
		</form>
		<?php

		}

		else if ($confirmed){
	
			$query="INSERT INTO adminuser (username , password , type ) VALUES ('$adminuser','$password','$type')";

			$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
			$result=mysql_db_query($MYSQL_DB,$query,$handle1);
	
			if ($type==0){
				$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('*' , '$adminuser')";
			}
			else{
				$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('$domain' , '$adminuser')";
			}
			$result2=mysql_db_query($MYSQL_DB,$query2,$handle1);

			if ($result){
				print "successfully added to Database....</br>";
			}
			else{
				print "Database error<br>";
			}
	
			include ("adminuser.php");

		}

	}

	else if ($admintype!=0){
        	print "<h3>Security violation detected, nothing deleted, attempt has been loggd</h3>";
	}
}

?>

<!-- </td></tr>
</table> -->

