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

		if ($new_password == $confirm_password && $new_password!="") {
			$query="select * from accountuser where username='$dest'";
		        $handle=DB::connect($DSN, true);
		        $result=$handle->query($query);
			$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$password=$row['password'];


			if ($PASSWORD_CHANGE_METHOD=="sql"){
			

			$handle=DB::connect($DSN, true);
			switch($CRYPT){
			case 1:
			  $query="update accountuser set password=ENCRYPT('$new_password') where username='$username'";
			  break;

			case 2:
			  $query="update accountuser set password=PASSWORD('$new_password') where username='$username'";
			break;

			default:
			  $query="update accountuser set password='$new_password' where username='$username'";
			}


	
			$result=$handle->query($query);

			include ("browseaccounts.php");




			}

			else if ($PASSWORD_CHANGE_METHOD=="poppassd"){

				include ('lib/poppassd.php');
				$daemon = new poppassd;
				if ($daemon->change_password($dest, $password, $new_password)) {
					print  "<em><big>"._("Password changed")."</big></em><p><p>";
				} else {
					print $daemon->$err_str;
					print "<big>"._("Failure in changing password.")."</big><p><p>";
				}
			} 

		else if ($new_password != $confirm_password){
			print "Nix da";
			print "<b>"._("New passwords are not equal. Password not changed")."</b><p><p>";
		}
	}

}



	if (!$confirmed){
//		$test = ereg ("",$alias,$result_array);

		$alias = spliti("@",$alias);
		$alias = $alias[0];

		print $result_array[0];

	        ?>

		<h3><?php print _("Change password for account")." <font color=\"red\">".$dest; ?></font></h3>

	        <form action="index.php" method="get">
	
	        <input type="hidden" name="action" value="change_password">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="alias" value="<?php print $alias."@".$domain ?>"> 

		<table>		

		<tr>
		<td width=150><?php print _("New password")?>:</td>
		<td><input type="password" size='30' name=new_password></td>
		</tr>
		<tr>
		<td width=150><?php print _("Confirm new password")?>:</td>
		<td><input type='password' size='30' name=confirm_password></td>
		</tr>

	        <tr><td>
	        <input type="submit" value="<?php print _("Submit")?>"> 
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


