          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 
<?php
print "<h3>"._("Change admin user for domain")." <font color=red>". $domain. "</font></h3>";
if (!$domain or $domain=="new"){
	print _("Please select a domain first");
}
else{

	if ($admintype==0){
		$handle1=DB::connect($DSN, true);
		$query="SELECT * from adminuser WHERE username='$username'";
		$result=$handle1->query($query);
                $adminrow=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
                $password=$adminrow['password'];
	//	$password=mysql_result($result,0,'password');
		

		if (!$confirmed){

		?>

		<form action="index.php" method="get">
		<input type="hidden" name="action" value="editadminuser">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="username" value="<?php print $username ?>">
		<input type="hidden" name="domain" value="<?php print $domain ?>">

		<table>
			<tr>
				<td><?php print _("Accountname") ?></td>
				<td><input class="inputfield" type="text" name="newusername" value="<?php print $username ?>" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>
		
			<tr>
				<td><?php print _("Admin Type") ?></td>
				<td><select class="selectfield" name="type">
					<option value=0><?php print _("Superuser") ?></option>
					<option selected value=1><?php print _("Domain Master") ?></option>
				</select>

		
			<tr>
				<td><?php print _("Password") ?></td>
				<td><input class="inputfield" type="password" value="<?php print $password ?>" name="newpassword" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>

			<tr>
				<td><?php print _("Confirm Password") ?></td>
				<td><input class="inputfield" type="password" name="confirm_password" value="<?php print $password ?>" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>
	
			<tr>
				<td></td>
				<td><input class="inputfield" type="submit" value="<?php print _("Submit") ?>"></td>
			</tr>
	

		</table>
		</form>
		<?php

		}

		else if ($confirmed){

			$query="UPDATE adminuser SET password='$newpassword' , type='$type', username='$newusername' WHERE username='$username'";

			$result=$handle1->query($query);
	
			if ($type==0){
				$query2="UPDATE domainadmin SET domain_name='*', adminuser='$newusername' WHERE adminuser='$username'";
			}
			else{
				$query2="UPDATE domainadmin SET domain_name='$domain', adminuser='$newusername' WHERE adminuser='$username'";
			}
			$result2=$handle1->query($query2);

			if ($result and $result2){
				print _("successfully changed Database....")."</br>";
			}
			else{
				print _("Database error")."<br>";
			}
	
			include ("adminuser.php");

		}

	}

	else if ($admintype!=0){
        	print "<h3>"._("Security violation detected, nothing deleted, attempt has been logged")."</h3>";
	}
}

?>

<!-- </td></tr>
</table> -->

