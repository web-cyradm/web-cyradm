          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php
if ($authorized){
        $cyr_conn = new cyradm;
       	$cyr_conn -> imap_login();

	if ($DOMAIN_AS_PREFIX) {
		$q= $cyr_conn->getquota("user/$username");
	}
	else {
		$q= $cyr_conn->getquota("user.$username");
	}

	if (!$confirmed){
	
		print"<h3>Setting individual Quota for user <font color=red>".$username."</font></h3>";

		?>

		<form action="index.php">
		<input type="hidden" name="action" value="setquota">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="domain" value="<?php print $domain?>">
		<input type="hidden" name="username" value="<?php print $username?>">
		<input type="text" size="10" name="quotaset" value="<?php print $q_total=$q[qmax]?>"> Kilobytes
		<input type="submit">
		</form>
 	
		<?php

	}
	else{
		$cyr_conn = new cyradm;
        	$cyr_conn -> imap_login();

		if ($DOMAIN_AS_PREFIX) {
			print $cyr_conn->setmbquota("user/$username","$quotaset");
		}
		else {
			print $cyr_conn->setmbquota("user.$username","$quotaset");
		}

		include ("browseaccounts.php");

	}
}
?>
</td></tr>


