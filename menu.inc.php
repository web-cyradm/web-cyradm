

<?php

print "<!-- ############################## Begin Menu ############################################ -->";

############### Root menu first ##########

	


print "<table border=\"0\" cellspacing=\"2\" cellpadding=\"0\">\n";
print "<tr>\n";

if ($admintype==0){

	print "<td colspan=\"7\">Superusers Menu</td><td colspan=\"3\">"._("Domainmasters menu")."</td></tr><tr>";


	print "<td class=\"rootnavi\" width\"20\">";
	print "<a href=\"index.php?action=newdomain&domain=new\">"._("add new domain")."</a></td>\n";

	print "<td>&nbsp;</td><td class=\"rootnavi\">";
	print "<a href=\"index.php?action=browse&domain=$domain\">"._("browse domains")."</a></td>\n";

	print "<td>&nbsp;</td><td class=\"rootnavi\">";
	print "<a href=\"index.php?action=adminuser&domain=$domain\">"._("adminusers")."</a></td>\n";

	print "<td>&nbsp;</td><td>&nbsp;</td>";

}

################ And the supervisors menu #####

print "<td class=\"navi\">";
print "<a href=\"index.php?action=accounts&domain=$domain\">"._("accounts")."</a></td>\n";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"index.php\">"._("home")."</a></td>\n";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"index.php?action=logout&domain=$domain\">"._("logout")."</a></td>\n";

print "<form action=\"index.php\" method=\"get\">";

print "<td>&nbsp;</td><td class=\"navi\">";
print "<a href=\"#\">"._("Search").":</a>";
print "<input type=\"hidden\" name=\"action\" value=\"search\">";
print "<input type=\"hidden\" name=\"domain\" value=\"$domain\">";
print "<input class=\"inputfield\" type=\"text\" name=\"searchstring\">";
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
print "<td>"._("Logged in as user").": </td>";
print "<td><b>$user</b></td>";
print "</tr>";
print "<tr>";
print "<td>-&gt;</td>";
print "<td>"._("Your role is").": </td>";
if ($admintype==0){
        print "<td><b><font color=\"red\">"._("Superuser")."</font></b></td>";
}
else if ($admintype==1){
        print "<td><b><font color=\"red\">"._("Domain Master")."</font></b></td>";
}
print "<tr>";
print "<td>-&gt;</td>";
print "<td>"._("Current domain is").": </td>";
if ($domain==""){
	print "<td><b>"._("No domain selected")."</b></td>";
}
else {
	print "<td><b>$domain</b></td>";
}
print "</tr>";
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

