          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php
// Specify location of translation tables
bindtextdomain("setquota", "./locale");

// Choose domain
textdomain("setquota");

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
	
		print"<h3>"._("Setting individual Quota for user")." <font color=red>".$username."</font></h3>";

		?>

		<form action="index.php">
		<input type="hidden" name="action" value="setquota">
		<input type="hidden" name="confirmed" value="true">
		<input type="hidden" name="domain" value="<?php print $domain?>">
		<input type="hidden" name="username" value="<?php print $username?>">
		<input type="text" size="10" name="quota" value="<?php print $q_total=$q[qmax]?>"> Kilobytes
		<input type="submit" value="<?php print _("Submit") ?>">
		</form>
 	
		<?php

	}
	else{
		$cyr_conn = new cyradm;
        	$cyr_conn -> imap_login();

		if ($DOMAIN_AS_PREFIX) {
			print $cyr_conn->setmbquota("user/$username","$quota");
		}
		else {
			print $cyr_conn->setmbquota("user.$username","$quota");
		}

		include ("browseaccounts.php");

	}
}
?>
</td></tr>


