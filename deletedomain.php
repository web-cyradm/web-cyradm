          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($admintype==0){

	$handle=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	$query1="SELECT * FROM accountuser WHERE domain_name='$domain' order by username";
	$result1=mysql_db_query($MYSQL_DB,$query1,$handle);
	$cnt1=mysql_num_rows($result1);
        

	if (!$confirmed){

		?>
		<h3>Delete a Domain from the System</h3>

		<h3>Do you really want to delete the Domain <font color=red><?php print $domain ?></font> with all its defined accounts, admins, and emailadresses?</h3>
		This can take a while depending on how many account have to be deleted<p>

		<font color="red">Your action will delete <?php print $cnt1 ?> accounts </font><p>

		<form action="index.php">
		<input type="hidden" name="action" value="deletedomain">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="domain" value="<?php print $domain?>">
		<input type="submit" name="confirmed" value="Yes, delete">
		<input type="submit" name="cancel" value="Cancel">
		</form>




		<?php

	}

	else if ($cancel){
		print "<h3>Action cancelled, nothing deleted</h3>";
	}

	else{
		$cyr_conn = new cyradm;
	        $cyr_conn -> imap_login();

		# First Delete all stuff related to the domain from the mysql database
	
		$query2="DELETE FROM virtual WHERE domain_name='$domain'";
		$hnd2=mysql_db_query($MYSQL_DB,$query2);

		$query3="DELETE FROM accountuser WHERE domain_name='$domain'";
		$hnd3=mysql_db_query($MYSQL_DB,$query3);

		$query4="DELETE FROM domain WHERE domain_name='$domain'";
		$hnd3=mysql_db_query($MYSQL_DB,$query4);

		for ($i=0;$i<$cnt1;$i++){
	
			$username=mysql_result($result1,$i,"username");
			$query5="DELETE FROM virtual WHERE username='$username'";
			$result5=mysql_db_query($MYSQL_DB,$query5);

			# And delete also the Usermailboxes from the cyrus system

                        if ($DOMAIN_AS_PREFIX){
				print $cyr_conn -> deletemb("user/".$username);
			}
			else {
				print $cyr_conn -> deletemb("user.".$username);
			}

		}
	
		$query6="SELECT * FROM domainadmin WHERE domain_name='$domain'";
		$result6=mysql_db_query($MYSQL_DB,$query6);
		$cnt6=mysql_num_rows($result6);
                for ($i=0;$i<$cnt6;$i++){
			$username=mysql_result($result6,$i,"adminuser");
                        $query7="DELETE FROM adminuser where username='$username'";
                        $result7=mysql_db_query($MYSQL_DB,$query7);
                } 
		$query8="DELETE FROM domainadmin WHERE domain_name='$domain'";
                $hnd8=mysql_db_query($MYSQL_DB,$query8); 
	print "<h3>Domain ".$domain." sucessfully deleted</h3>";

	include ("browse.php");


	}
}
else{
	print "<h3>Yor are not allowed to delete domains!</h3>";
}

?>
</td></tr>

