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

//	        $query="UPDATE virtual SET alias='$newalias@$domain', dest='$dest' WHERE alias='$alias'";
	  if ($new_password == $new_password2) {
		$query="select * from accountuser where username='$dest'";
	        $handle=DB::connect($DSN, true);
	        $result=$handle->query($query);
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$password=$row['password'];
		include ('lib/poppassd.php');
		$daemon = new poppassd;
		if ($daemon->change_password($dest, $password, $new_password)) {
		  print  "<em><big>"._("Password changed")."</big></em><p><p>";
		} else {
		  print $daemon->$err_str;
		  print "<big>"._("Failure in changing password.")."</big><p><p>";
		}
	  } else {
	    print "<b>"._("New passwords are not equal. Password not changed")."</b><p><p>";
	  }
	  include ("browseaccounts.php");
//	        if (!DB::isError($result)){
//	                print "<h3>"._("Sucessfully changed")."</h3>";
//			include ("browseaccounts.php");
//	        }
//	        else{
//	                print "<p>"._("Database error, please try again")."<p>";
//	        }

	}



	if (!$confirmed){
//		$test = ereg ("",$alias,$result_array);

		$alias = spliti("@",$alias);
		$alias = $alias[0];

		print $result_array[0];

	        ?>

	        <form action="index.php" method="get">
	
	        <input type="hidden" name="action" value="change_password">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="alias" value="<?php print $alias."@".$domain ?>"> 

	        <table>

	        <tr>
	        <td><?php print _("Emailadress:") ?></td>
<!--		<td><input type="text" size="30" name=newalias value="<?php print $alias?>">@<?php print $domain?></td>	-->
		<td><?php print $alias.'@'.$domain?></td>
	        </tr>

	        <tr>
	        <td width=150><?php print _("Destination:") ?></td>
<!--	        <td><input type="text" size="30" name=dest value="<?php print $dest ?>"> </td> -->
		<td><?php print $dest?> </td>
	        </tr>

		<tr>
		<td width=150>New password:</td>
		<td><input type="text" size='30' name=new_password></td>
		</tr>
		<tr>
		<td width=150>Confirmed new password:</td>
		<td><input type='text' size='30' name=new_password2></td>
		</tr>

	        <tr><td>
	        <input type="submit" value="Change Password"> 
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


