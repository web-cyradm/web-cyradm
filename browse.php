<!-- ############################## Start browse.php ###################################### -->
	   <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"><h3>Browse domains</h3>  

<table border=0>
<tbody>
<tr>
<th colspan=3>actions</th>
<th>domainname</th>
<?php
if (!$DOMAIN_AS_PREFIX ) {
	print "<th>prefix</th>";
}
?>
<th>max Accounts</th>
<th>default quota per user</th>
</tr>
<?php

if (!isset($allowed_domains)){
	$query="SELECT * FROM domain ORDER BY domain_name";
}
else{
	$query="SELECT * FROM domain WHERE domain_name='$allowed_domains' ORDER BY domain_name";
}

$handle=DB::connect($DSN, true);

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
  print "<td><a href=\"index.php?action=editdomain&domain=$domain\">Edit domain</a></td>\n";
  print "<td><a href=\"index.php?action=deletedomain&domain=$domain\">Delete Domain</a></td>\n";
  print "<td><a href=\"index.php?action=accounts&domain=$domain\">accounts</a></td>\n";
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
