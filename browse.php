<!-- ############################## Start browse.php ###################################### -->
<?php

print "<tr>";
print "<td width=\"10\">&nbsp; </td>";
print "<td valign=\"top\"><h3>"._("Browse domains")."</h3>";  
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

if (!isset($allowed_domains)){
	$query="SELECT * FROM domain ORDER BY domain_name";
}
else{

	//$query="SELECT * FROM domain WHERE domain_name='$allowed_domains' ORDER BY domain_name";
	$query="SELECT * FROM domain WHERE domain_name='";
	for ($i=0;$i<$cnt;$i++){
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $i);
		$allowed_domains=$row['domain_name'];
//		print "DEBUG: Allowed Domains".$allowed_domains;
		$query.="$allowed_domains' OR domain_name='";
		
	}
	$query.="' ORDER BY domain_name";
//	print $query;
}

$handle=DB::connect($DSN, true);

if (DB::isError($handle)) {
	die (_("Database error"));
}


$result=$handle->query($query);
$cnt=$result->numRows($result);

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
  print "<td><a href=\"index.php?action=aliases&domain=$domain\">". _("Aliases")."</a></td>\n";
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
?>
</tbody>
</table>
<!-- ############################### End browse.php ############################################# -->
