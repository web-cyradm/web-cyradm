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
                $type=$adminrow['type'];

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
				<td><?php print $username ?></td>
			</tr>
		
			<tr>
				<td><?php print _("Admin Type") ?></td>
				<td><select class="selectfield" name="newtype">
					<option <?php 
					if ($type==0){
						print "selected";
					}
					print " value=0>";
					 print _("Superuser") ?></option>
					<option <?php
					if ($type==1){
						print "selected";
					}
					 print " value=1>";
					 print _("Domain Master") ?></option>
				</select>

		
			<tr>
				<td><?php print _("Password") ?></td>
				<td><input class="inputfield" type="password" name="new_password" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>

			<tr>
				<td><?php print _("Confirm Password") ?></td>
				<td><input class="inputfield" type="password" name="confirm_password" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
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

			if ($new_password && $new_password==$confirm_password){
				$pwd=new password;
				$new_password=$pwd->encrypt($new_password,$CRYPT);
				# If the new_password field is not empty and the password matches, update the password
				$query="UPDATE adminuser SET password='$new_password' , type='$newtype' WHERE username='$username'";
			}

			else if ($new_password!=$confirm_password){
				die (_("New passwords are not equal. Password not changed"));
			}

			else{

				$query="UPDATE adminuser SET type='$newtype' WHERE username='$username'";
			}

			$result=$handle1->query($query);
	
			if ($newtype==0){
				$query2="UPDATE domainadmin SET domain_name='*' WHERE adminuser='$username'";
			}
			else{
				$query2="UPDATE domainadmin SET domain_name='$domain' WHERE adminuser='$username'";
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

