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
$handle=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
$result=mysql_db_query($MYSQL_DB,$query, $handle);
$cnt=mysql_num_rows($result);
$b=0;
for ($c=0;$c<$cnt;$c++){

if ($b==0){
//    $color="dcdcdc";
	$cssrow="row1";
	$b=1;
  }
else{
//    $color="ffffcc";
	$cssrow="row2";
	$b=0;
}

  $domain=mysql_result($result,$c,'domain_name');
  print "<tr class=\"$cssrow\"> \n";
  print "<td><a href=\"index.php?action=editdomain&domain=$domain\">Edit domain</a></td>\n";
  print "<td><a href=\"index.php?action=deletedomain&domain=$domain\">Delete Domain</a></td>\n";
  print "<td><a href=\"index.php?action=accounts&domain=$domain\">accounts</a></td>\n";
  print "<td>";
  print mysql_result($result,$c,'domain_name');
  print "</td>\n<td>";
if (!$DOMAIN_AS_PREFIX) {
	print mysql_result($result,$c,'prefix');
	print "</td>\n<td>";
}

  print mysql_result($result,$c,'maxaccounts');
  print "</td>\n<td>";
  print mysql_result($result,$c,'quota');

  print "&nbsp;</td>\n</tr>\n";

}
?>
</tbody>
</table>
<!-- ############################### End browse.php ############################################# -->
