

<?php
print "<!-- ############################## Begin Menu ############################################ -->";

############### Root menu first ##########

	


print "<table border=\"0\" cellspacing=\"2\" cellpadding=\"0\">\n";
print "<tr>\n";

if ($admintype==0){

	print "<td colspan=\"7\">Superusers Menu</td><td colspan=\"3\">Supervisors menu</td></tr><tr>";


	print "<td class=\"rootnavi\" width\"20\">";
	print "<a href=\"index.php?action=newdomain&domain=new\">add&nbsp;new&nbsp;domain</a></td>\n";

	print "<td>&nbsp;</td><td class=\"rootnavi\">";
	print "<a href=\"index.php?action=browse&domain=$domain\">domains</a></td>\n";

	print "<td>&nbsp;</td><td class=\"rootnavi\">";
	print "<a href=\"index.php?action=adminuser&domain=$domain\">adminusers</a></td>\n";

	print "<td>&nbsp;</td><td>&nbsp;</td>";

}

################ And the supervisors menu #####

print "<td class=\"navi\">";
print "<a href=\"index.php?action=accounts&domain=$domain\">accounts</a></td>\n";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"index.php?action=catch&domain=$domain\">Catch all Account</a></td>\n";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"index.php\">home</a></td>\n";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"index.php?action=logout&domain=$domain\">logout</a></td>\n";

print "<form action=\"search.php\" method=\"get\">";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"#\">Search:&nbsp;</a>";
print "<input class=\"inputfield\" type=\"text\" name=\"$searchstring\">";
print "</form>";


print "</td>";
print "</tr>";
print "</table>";

print "</td></tr>";
print "<tr>";
print "<td width=\"10\">&nbsp;</td>";
print "<td valign=\"top\" height=\"60\">";
print "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" class=\"header\">";
print "<tr>";
print "<td>-&gt;</td>";
print "<td>Logged in as user: </td>";
print "<td><b>$user</b></td>";
print "</tr>";
print "<tr>";
print "<td>-&gt;</td>";
print "<td>Your role is: </td>";
if ($admintype==0){
        print "<td><b><font color=\"red\">superuser</font></b></td>";
}
else if ($admintype==1){
        print "<td><b><font color=\"red\">Domain Supervisor</font></b></td>";
}
?>
</tr>
</table>
</td>
</tr>
  <tr>
	<td width="10">&nbsp;</td>
	<td height="5">
	  <hr noshade size="1">
	</td>
  </tr>

<!-- ############################## End Menu ############################################ -->

