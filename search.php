<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################################### Start search ################################# -->
          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php
$cyr_conn = new cyradm;
$error = $cyr_conn->imap_login();

if ($error!=0){
    die ("Error $error");
}

if ($_SESSION['admintype']==0) {
    $allowed_domains1="('1'='1";
    $allowed_domains3="('1'='1";
} else {
    $allowed_domains1="(a.domain_name='";
    $allowed_domains3="(virtual.username='";
    foreach($_SESSION['allowed_domains'] as $allowed_domain) {
	$allowed_domains1 .= $allowed_domain."' OR a.domain_name='";                                       
	$allowed_domains3 .= $allowed_domain."' OR virtual.username='";                                       
    }
}

$query="SELECT * FROM domain a WHERE domain_name LIKE '%$searchstring%' AND $allowed_domains1') ORDER BY domain_name";
$query2="SELECT DISTINCT a.username, a.domain_name FROM virtual as v, accountuser as a WHERE ((v.username LIKE '%$searchstring%') OR (v.alias LIKE '%$searchstring%')) AND (v.username=a.username) AND $allowed_domains1') ORDER BY username";
$query3="SELECT DISTINCT alias, username FROM virtual WHERE (((dest LIKE '%$searchstring%') OR (alias LIKE '%$searchstring%')) AND (dest <> username) AND (username<>'') ) AND $allowed_domains3') ORDER BY username";	
$result=$handle->query($query);
$result2=$handle->query($query2);
$result3=$handle->query($query3);	

$cnt=$result->numRows($result);
$total=$result2->numRows($result2);
$total3=$result3->numRows($result3);

#####  Show matching Domains first #######

print "<tr>";
print "<td width=\"10\">&nbsp; </td>";
print "<td valign=\"top\"><h3>"._("Total domains matching").": ".$cnt."</h3>";
print "<table border=0>";
print "<tbody>";
print "<tr>";
print "<th colspan=4>". _("action")."</th>";
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


	$row=$result->fetchRow(DB_FETCHMODE_ASSOC,$c);

	$domain=$row['domain_name'];

	print "<tr class=\"$cssrow\"> \n";
	print "<td><a href=\"index.php?action=editdomain&domain=$domain\">". _("Edit Domain")."</a></td>\n";
	print "<td><a href=\"index.php?action=deletedomain&domain=$domain\">". _("Delete Domain")."</a></td>\n";
	print "<td><a href=\"index.php?action=accounts&domain=$domain\">". _("accounts")."</a></td>\n";
	print "<td><a href=\"index.php?action=aliases&domain=$domain\">"._("Aliases")."</a></td>\n";
	print "<td>";
	print $domain;
	print "</td>\n<td>";
	if (!$DOMAIN_AS_PREFIX) {
	       	# Print the prefix
	       	print $row['prefix'];
	       	print "</td>\n<td>";
	}

	# Print the maxaccount
	print $row['maxaccounts'];
	print "</td>\n<td>";
	# Print the quota
	print $row['quota'];

	print "&nbsp;</td>\n</tr>\n";
    }

print "</tbody>";
print "</table>";

############ And now show the users matching the search query ###########


print "<h3>"._("Total users matching").": ".$total."</h3>";
if (!isset($row_pos)){
	$row_pos=0;
	}
        $query="SELECT DISTINCT a.username, a.domain_name FROM virtual as v, accountuser as a WHERE ((v.username LIKE '%$searchstring%') OR (v.alias LIKE '%$searchstring%')) AND (v.username=a.username) AND $allowed_domains1') ORDER BY username";
	$result=$handle->limitQuery($query,$row_pos,10);
	$cnt=$result->numRows($result);

	print "<h4>"._("Displaying from position").": ". "$row_pos</h4>";
	$b=0;
	if ($cnt!=0){
		print "<table cellspacing=\"2\" cellpadding=\"0\"><tr>";
		print "<td class=\"navi\">";
		print "<a class=\"navilink\" href=\"index.php?action=newaccount&domain=$domain\">"._("Add new account")."</a>";
		print "</td>";


		$prev = $row_pos -10;
		$next = $row_pos +10;

		if ($row_pos<10){
			print "<td class=\"navi\"><a class=\"navilink\" href=\"#\">"._("Previous 10 entries")."</a></td>";
		}
		else {
			print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=search&domain=$domain&row_pos=$prev&searchstring=$searchstring\">".
			_("Previous 10 entries") ."</a></td>";
		}
		if ($next>$total){
			print "<td class=\"navi\"><a class=\"navilink\" href=\"#\">"._("Next 10 entries")."</a></td>";
		}
		else {
			print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=search&domain=$domain&row_pos=$next&searchstring=$searchstring\">".
			_("Next 10 entries")."</a></td>";
		}
		print "</tr></table><p>";

		print "<table border=\"0\">\n";
		print "<tbody>";
		print "<tr>";
		print "<th colspan=\"6\">"._("action")."</th>";
		print "<th>"._("Email address")."</th>";
		print "<th>"._("Username")."</th>";
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
		$username=$row['username'];
		$domain=$row['domain_name'];
		$query2="SELECT * FROM virtual WHERE username='$username'";
		$result2=$handle->query($query2);
		$row2=$result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$alias=$row2['alias'];
		print "\n<tr class=\"$cssrow\">";
		print "\n<td><a href=\"index.php?action=editaccount&domain=$domain&username=$username\">"._("Edit account")."</a></td>";
		print "\n<td><a href=\"index.php?action=change_password&domain=$domain&username=$username&alias=$alias\">"._("Change Password")."</a></td>";
		
		print "\n<td><a href=\"index.php?action=forwardaccount&domain=$domain&username=$username&alias=$alias\">". _("Forward")."</a></td>";
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
                $query4 = "SELECT * FROM virtual WHERE alias='" . $username . "'";
                $result4 = $handle->query($query4);
                $row4 = $result4->fetchRow(DB_FETCHMODE_ASSOC, 0);
                if (is_array($row4)){
                    print "<br><b>" . _("Forwards") . ":</b><br><br>";
                    $forwards_tmp = preg_split('|,\s*|', stripslashes($row4['dest']));
                    $forwards = array();
	            while (list(, $forward) = each($forwards_tmp)){
                        if (strtolower($forward) != strtolower($username)){
                            $forwards[] = htmlspecialchars(trim($forward));
                        } else {
    	                    $forwards[] = "<b>" . htmlspecialchars(trim($forward)) . "</b>";
                        }
                    }
                    echo implode("<br>", $forwards);
                }
		print "</td>\n<td>";
		print $username;
		print "</td>\n<td>";
                if ($DOMAIN_AS_PREFIX){
                    $quota = $cyr_conn->getquota("user/" . $username);
                } else {
                    $quota = $cyr_conn->getquota("user." . $username);
                }
                                                    
                if ($quota['used'] != "NOT-SET"){
                    $q_used  = $quota['used'];
                    $q_total = $quota['qmax'];
                    if (! $q_total == 0){
                        $q_percent = 100*$q_used/$q_total;
                        printf ("%d KBytes %s %d KBytes (%.2f%%)",
                        $quota['used'], _("out of"),
	                $quota['qmax'], $q_percent);
                    } else {
                        print _("Unable to retrieve quota");
                    }
                } else { 
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
		print "<a class=\"navilink\" href=\"index.php?action=newaccount&domain=$domain\">"._("Add new account")."</a>";
		print "\n</td></tr></table>\n";

	}

################ And now show the matching aliases #######################
	print "<h3>"._("Total aliases matching").": ".$total3."</h3>";
	if ($total3 == 0) 
		print _("No aliases found");
	else {
?>
        <table border="0">
                <tbody>
                <tr>
                        <th colspan="2"><?php print _("action");?></th>
                        <th><?php print _("Email address"); ?></th>
                        <th><?php print _("Destination"); ?></th>
                </tr>
<?php 
$b = 0;

for ( $c = 0; $c < $total3; $c++){
	if ($b == 0){
		$cssrow = "row1";
		$b = 1;
	}
	else {
		$cssrow = "row2";
		$b = 0;
	}
	$row = $result3->fetchRow( DB_FETCHMODE_ASSOC, $c);
	$alias = $row['alias'];
	$domain = $row['username'];
	?><tr class="<?php print( $cssrow ); ?>">
                        <td><a href="index.php?action=editalias&alias=<?php print( $alias ); ?>&domain=<?php print( $domain ); ?>"><?php print _("Edit Alias"); ?></a></td>
                        <td><a href="index.php?action=deletealias&alias=<?php print( $alias ); ?>&domain=<?php print( $domain ); ?>"><?php print _("Delete Alias"); ?></a></td>
                        <td><?php print( $alias ); ?></td>
                        <td>	
	<?php
	$query4 = "SELECT dest FROM virtual WHERE alias = '$alias'";
	$result4 = $handle->limitQuery($query4, 0, 3);
	$num_dest = $result4->numRows ($result4);
	for ($d =0; $d < $num_dest; $d++){
		$row2 = $result4->fetchRow (DB_FETCHMODE_ASSOC, $d);
		if ($d != 0) {
			print ", ";
		}
		print ($row2['dest']);
	}
	$query5 = "SELECT COUNT( dest ) FROM virtual WHERE alias = '$alias'";
	$num_dests = $handle->getOne($query5);
		if ($num_dests > 3){
			print ", ... ";
		}
	?></td></tr>
<?php
	}

?>
</table>
<?php
}
?>

<!-- ##################################### End search.php #################################### -->


