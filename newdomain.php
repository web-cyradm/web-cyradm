          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">


<?php

// Specify location of translation tables
bindtextdomain("newdomain", "./locale");

// Choose domain
textdomain("newdomain");


if ($admintype==0){

	print "<h3>"._("Add new domain")."</h3>";

	if (!$confirmed){
	

	?>

	<form action="index.php">
	<input type="hidden" name="action" value="newdomain">
	<input type="hidden" name="confirmed" value="true">
		<table>
			<tr>
			<td><?php print _("Domainname") ?></td>
			<td><input type="text" size="20" name="domain"></td>
			</tr>
			<?php
				if (!$DOMAIN_AS_PREFIX) {
					print "<tr>\n";
				        print "<td>"._("Prefix")."</td>\n";
					print "<td><input type=\"text\" size=\"8\" name=\"prefix\"></td>\n";
					print "</tr>\n";
				}
				else {
					print "<input type=\"hidden\" name=\"prefix\" value=\"to-be-overwritten-by-domain\">\n";
				} 
			?>
			<tr>
			<td><?php print _("Maximum Accounts") ?></td>
			<td><input type="text" size="2" name="maxaccounts"></td>
			</tr>
			<tr>
			<td><?php print _("Default Quota in Kilobytes") ?></td>
			<td><input type="text" size="5" name="quota" value="<?php print $DEFAULT_QUOTA ?>"></td>
			</tr>
			<tr>
			<td><input type="submit" value="<?php print _("Submit") ?>" ></td>
			</tr>
		</table>
	</form>

	<?php

	}

	else{

		if ($authorized==TRUE){
			if ($DOMAIN_AS_PREFIX) {
				$prefix=$domain;
			}

			$query="INSERT INTO domain (domain_name, prefix, maxaccounts, quota) VALUES ('$domain', '$prefix', '$maxaccounts', '$quota')";

		        $handle=DB::connect ($DSN,true);
		        $result=$handle->query($query);

		        if (!DB::isError($result)){
		                print "Successfully added";
				include ("browse.php");
		        }
		        else{
		                print "<p>Database error, please try again<p>";
		        }
		}
		else{
			print "<h3>".$err_msg."</h3>";
		}
	
	}

}
else{

	print "<h3>You are not allowed to add new domains</h3>";
}


?>
</td></tr>


