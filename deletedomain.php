          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($admintype==0){

	$handle=DB::connect($DSN, true);
	$query1="SELECT * FROM accountuser WHERE domain_name='$domain' order by username";
	$result1=$handle->query($query1);
	$cnt1=$result1->numRows();
        

	if (!$confirmed){

		?>
		<h3><?php print _("Delete a Domain from the System") ?></h3>

		<h3><?php print _("Do you really want to delete the Domain")?> <font color=red><?php print $domain ?></font> <?php print _("with all its defined accounts, admins, and emailadresses")?>?</h3>
		<?php print _("This can take a while depending on how many account have to be deleted")?><p>

		<font color="red"><?php print _("Your action will delete")." " ?> <?php print $cnt1 ."&nbsp;"._("accounts")?> </font><p>

		<form action="index.php">
		<input type="hidden" name="action" value="deletedomain">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="domain" value="<?php print $domain?>">
		<input class="button" type="submit" name="confirmed" value="<?php print _("Yes, delete")?>">
		<input class="button" type="submit" name="cancel" value="<?php print _("Cancel")?>">
		</form>




		<?php

	}

	else if ($cancel){
		print "<h3>"._("Action cancelled, nothing deleted")."</h3>";
	}

	else{
		$cyr_conn = new cyradm;
	        $cyr_conn -> imap_login();

		# First Delete all stuff related to the domain from the database
	
		$query2="DELETE FROM virtual WHERE domain_name='$domain'";
		$hnd2=$handle->query($query2);

		$query3="DELETE FROM accountuser WHERE domain_name='$domain'";
		$hnd3=$handle->query($query3);

		$query4="DELETE FROM domain WHERE domain_name='$domain'";
		$hnd3=$handle->query($query4);

		for ($i=0;$i<$cnt1;$i++){
	
			$row = $result1->fetchRow(DB_FETCHMODE_ASSOC, $i);
			$username=$row['username'];
			$query5="DELETE FROM virtual WHERE username='$username'";
			$result5=$handle->query($query5);

			# And delete also the Usermailboxes from the cyrus system

                        if ($DOMAIN_AS_PREFIX){
				print $cyr_conn -> deletemb("user/".$username);
			}
			else {
				print $cyr_conn -> deletemb("user.".$username);
			}

		}

		# Finally the domain must be removed from the domainadmin table
	
		$query6="SELECT * FROM domainadmin WHERE domain_name='$domain'";
		$result6=$handle->query($query6);
		$cnt6=$result6->numRows();
                for ($i=0;$i<$cnt6;$i++){
			$row=$result6->fetchRow($i);
			$username=$row['adminuser'];
			print $username;
			$query7="SELECT * FROM domainadmin where adminuser='$username'";
			$result7=$handle->query($query7);
			$cnt7=$result7->numRows();
			print $cnt7;
			if ($cnt7==1){
                        	$query7="DELETE FROM adminuser where username='$username'";
			}
                        $query8="DELETE FROM domainadmin where domain_name='$domain'";
                        $result8=$handle->query($query8);
                } 
	print "<h3>". _("Domain")." ".$domain." ". _("successfully deleted")."</h3>";

	include ("browse.php");


	}
}
else{
	print "<h3>"._("Security violation detected, action cancelled. Your attempt has been logged.")."</h3>";
}

?>
</td></tr>

