<!-- #################################### Start search ################################# -->
          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

$query="SELECT * FROM domain where domain_name LIKE '%$searchstring%' ORDER BY domain_name";
$query2="SELECT * FROM accountuser where username LIKE '%$searchstring%' ORDER BY username";
$query3="SELECT * FROM alias where username LIKE '%$searchstring%' ORDER BY username";

$handle=DB::connect($DSN, true);
	
$result=$handle->query($query);
$result2=$handle->query($query2);
$result3=$handle->query($query3);
	

$cnt=$result->numRows($result);
$cnt2=$result2->numRows($result2);
$cnt3=$result2->numRows($result3);

#####  Show matching Domains first #######

print "<tr>";
print "<td width=\"10\">&nbsp; </td>";
print "<td valign=\"top\"><h3>"._("Total domains matching:")." ".$cnt."</h3>";
print "<table border=0>";
print "<tbody>";
print "<tr>";
print "<th colspan=3>". _("action")."</th>";
print "<th>". _("domainname")."</th>";


if (!$DOMAIN_AS_PREFIX ) {
        print "<th>"._("prefix")."</th>";
}

print "<th>"._("max Accounts")."</th>";
print "<th>"._("default quota per user")."</th>";
print "</tr>";

$b=0;
for ($c=0;$c<$cnt;$c++){

	if ($b==0){
		$cssrow="row1";
	        $b=1;
	}
	else{
	        $cssrow="row2";
	        $b=0;
	}


	$row=$result->fetchRow($result,$c,'domain_name');

	$domain=$row[0];

	print "<tr class=\"$cssrow\"> \n";
	print "<td><a href=\"index.php?action=editdomain&domain=$domain\">". gettext("Edit Domain")."</a></td>\n";
	print "<td><a href=\"index.php?action=deletedomain&domain=$domain\">". _("Delete Domain")."</a></td>\n";
	print "<td><a href=\"index.php?action=accounts&domain=$domain\">". _("accounts")."</a></td>\n";
	print "<td>";
	print $domain;
	print "</td>\n<td>";
	if (!$DOMAIN_AS_PREFIX) {
	       	# Print the prefix
	       	print $row[1];
	       	print "</td>\n<td>";
	}

	# Print the maxaccount
	print $row[2];
	print "</td>\n<td>";
	# Print the quota
	print $row[3];

	print "&nbsp;</td>\n</tr>\n";
}




	

print "</tbody>";
print "</table>";

############ And now show the users matching the search query ###########


print "<h3>"._("Total usernames matching:")." ".$cnt3."</h3>";
if (!isset($row_pos)){
	$row_pos=0;
	}
	$query="SELECT * FROM accountuser where username LIKE '$searchstring%' ORDER BY username";
	$handle=DB::connect($DSN, true);
	$result=$handle->limitQuery($query,$row_pos,10);
	$cnt=$result->numRows($result);

	$query2="SELECT * FROM accountuser where domain_name='$domain' ORDER BY username";
//        $result2=mysql_db_query($MYSQL_DB,$query2);
	$result2=$handle->query($query2);


	$total=$result2->numRows($result2);

	$b=0;
	if ($cnt!=0){
		print "<table cellspacing=\"2\" cellpadding=\"0\"><tr>";
		print "<td class=\"navi\">";
		print "<a href=\"index.php?action=newaccount&domain=$domain&username=$username\">"._("Add new account")."</a>";
		print "</td>";


		$prev = $row_pos -10;
		$next = $row_pos +10;

		if ($row_pos<10){
			print "<td class=\"navi\"><a href=\"#\">"._("Previous 10 entries")."</a></td>";
		}
		else {
			print "<td class=\"navi\"><a href=\"index.php?action=accounts&domain=$domain&row_pos=$prev\">".
			_("Previous 10 entries") ."</a></td>";
		}
		if ($next>$total){
			print "<td class=\"navi\"><a href=\"#\">"._("Next 10 entries")."</a></td>";
		}
		else {
			print "<td class=\"navi\"><a href=\"index.php?action=accounts&domain=$domain&row_pos=$next\">".
			_("Next 10 entries")."</a></td>";
		}
		print "</tr></table><p>";

		print "<table border=\"0\">\n";
		print "<tbody>";
		print "<tr>";
		print "<th colspan=\"4\">"._("action")."</th>";
		print "<th>"._("Email address")."</th>";
		print "<th>"._("Username")."</th>";
		print "<th>"._("Password")."</th>";
		print "<th>"._("Quota used")."</th>";
		print "</tr>";


		for ($c=0;$c<$cnt;$c++){

			if ($b==0){
				$cssrow="row1";
				$b=1;
			}
			else{
			$cssrow="row2";
			$b=0;
		}

		$row=$result->fetchRow(DB_FETCHMODE_ASSOC,$c);
		$domain=$row['domain_name'];
		$username=$row['username'];
		print "\n<tr class=\"$cssrow\">";
		print "\n<td><a href=\"index.php?action=editaccount&domain=$domain&username=$username\">"._("Edit account")."</a></td>";
		print "\n<td><a href=\"index.php?action=deleteaccount&domain=$domain&username=$username\">"._("Delete account")."</a></td>";
		print "\n<td><a href=\"index.php?action=setquota&domain=$domain&username=$username\">"._("Set quota")."</a></td>";
		print "\n<td><a href=\"index.php?action=catch&domain=$domain&username=$username\">"._("Set catch all")."</a></td>";
		print "\n<td>";
		$query2="SELECT * FROM virtual WHERE username='$username'";
		$result2=$handle->query($query2);

		$cnt2=$result2->numRows($result2);

		for ($c2=0;$c2<$cnt2;$c2++){
			# Print All Emailadresses found for the account
			$row=$result2->fetchRow(DB_FETCHMODE_ASSOC, $c2);
			print $row['alias']."<br>";
		}

		print "</td>\n<td>";
		print $username;
		print "</td>\n<td>";
        //        print mysql_result($hnd,$c,'password');
		print "******";
		print "</td>\n<td>";
		if ($DOMAIN_AS_PREFIX) {
			$quota= $cyr_conn->getquota("user/$username");
		}
		else {
			$quota= $cyr_conn->getquota("user.$username");
		}
		if ($quota[used]!="NOT-SET"){
			$q_used=$quota[used];
			$q_total=$quota[qmax];
			$q_percent=100*$q_used/$q_total;
			print $quota[used]." Kbytes "._("out of")." ";
			print $quota[qmax]." Kbytes (".sprintf("%.2f",$q_percent)." %)";
		}
		else{
			print _("Quota not set");
		}


		print "&nbsp;</td>\n</tr>\n";


		}
		print "\n</tbody>\n";
		print "</table>\n";

	}
	else{
		print "\n"._("No accounts found")."\n<p>";

		print "<table><tr>";
		print "<td class=\"navi\">\n";
		print "<a href=\"index.php?action=newaccount&domain=$domain&username=$username\">"._("Add new account")."</a>";
		print "\n</td></tr></table>\n";

	}



?>

<!-- ##################################### End search.php #################################### -->


