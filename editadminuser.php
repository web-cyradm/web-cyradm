          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>change admin user for domain $domain</h3>";
if (!$domain or $domain=="new"){
	print "Please select a domain first";
}
else{

	if ($admintype==0){
		$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
		$query="SELECT * from adminuser WHERE username='$username'";
		$result=mysql_db_query($MYSQL_DB,$query);
                $adminrow=mysql_fetch_array($result);
                $password=$adminrow["password"];
	//	$password=mysql_result($result,0,'password');
		

		if (!$confirmed){

		?>

		<form action="index.php" action="get">
		<input type="hidden" name="action" value="editadminuser">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="username" value="<?php print $username ?>">
		<input type="hidden" name="domain" value="<?php print $domain ?>">

		<table>
			<tr>
				<td>login</td>
				<td><input class="inputfield" type="text" name="newusername" value="<?php print $username ?>" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>
		
			<tr>
				<td>Type</td>
				<td><select name="type">
					<option value=0>Superuser</option>
					<option selected value=1>Domain supervisor</option>
				</select>

		
			<tr>
				<td>Password</td>
				<td><input class="inputfield" type="password" value="<?php print $password ?>" name="newpassword" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>

			<tr>
				<td>Confirm Password</td>
				<td><input class="inputfield" type="password" name="confirm_password" value="<?php print $password ?>" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
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

			$query="UPDATE adminuser SET password='$newpassword' , type='$type', username='$newusername' WHERE username='$username'";

			$result=mysql_db_query($MYSQL_DB,$query,$handle1);
	
			if ($type==0){
				$query2="UPDATE domainadmin SET domain_name='*', adminuser='$newusername' WHERE adminuser='$username'";
			}
			else{
				$query2="UPDATE domainadmin SET domain_name='$domain', adminuser='$newusername' WHERE adminuser='$username'";
			}
			$result2=mysql_db_query($MYSQL_DB,$query2,$handle1);

			if ($result and $result2){
				print "successfully changed Database....</br>";
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

