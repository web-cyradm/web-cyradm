          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>"._("Add new admin user for domain")." <font color=red>". $domain."</font></h3>";
if (!$domain or $domain=="new"){
	print _("Please select a domain first");
}
else{

	if ($admintype==0){

		if (!$confirmed){

		?>

		<form action="index.php" method="get">
		<input type="hidden" name="action" value="newadminuser">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="domain" value="<?php print $domain ?>">

		<table>
			<tr>
				<td><?php print _("Accountname") ?></td>
				<td><input class="inputfield" type="text" name="newadminuser" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
			</tr>
		
			<tr>
				<td><?php print _("Admin Type") ?></td>
				<td><select name="newadmintype">
					<option value=0><?php print _("Superuser") ?></option>
					<option selected value=1><?php print _("Domain Master") ?></option>
				</select> <?print _("Select \"Superuser\" for all domains") ?>

		
			<tr>
				<td><?php print _("Password") ?></td>
				<td><input class="inputfield" type="password" name="password" onFocus="this.style.backgroundColor='#aaaaaa'"></td>
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

			$pwd=new password;

			/* This is temporary needed, because mysql uses proprietary crypto, thus 
			Admin password must be stored plaintext */

			if ($CRYPT=="mysql") {
				$CRYPT="plain";
			}

			$password=$pwd->encrypt($password,$CRYPT);
	
			$query="INSERT INTO adminuser (username , password , type ) VALUES ('$newadminuser','$password','$newadmintype')";

			$handle1=DB::connect($DSN,true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result=$handle1->query($query);
	
			if ($newadmintype==0){
				print $newadminuser;
				$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('*' , '$newadminuser')";
			}
			else{
				$query2="INSERT INTO domainadmin (domain_name , adminuser) values ('$domain' , '$newadminuser')";
			}
			$result2=$handle1->query($query2);

			if (!DB::isError($result)){
				print _(" successfully added to Database")."</br>";
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

