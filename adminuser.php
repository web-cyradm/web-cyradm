<!-- ############################## Start adminuser.php ###################################### -->
           <tr>
        <td width="10">&nbsp; </td>
<?php

print "<td valign=\"top\"><h3>"._("Browse admins")."</h3>";


if ($admintype==0){
        $query="SELECT * FROM adminuser"; 
        $handle=DB::connect($DSN, true);
        $result=$handle->query($query);
        $cnt=$result->numRows();

        $total=$result->numRows();
        $b=0;
        if ($cnt!=0){
                print _("Total administrators").": ".$total."<p>";
                print "<table cellspacing=\"2\" cellpadding=\"0\"><tr>";
                print "<td class=\"navi\">";
                print "<a href=\"index.php?action=newadminuser&domain=$domain&username=$username\">"._("Add administator")."</a>";
                print "</td>";


                $prev = $row_pos -10;
                $next = $row_pos +10;

                if ($row_pos<10){
                        print "<td class=\"navi\"><a href=\"#\">"._("Previous 10 entries")."</a></td>";
                }
                else {
                        print "<td class=\"navi\"><a href=\"index.php?action=accounts&domain=$domain&row_pos=$prev\">".
                        _("Previous 10 entries")."</a></td>";
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
                print "<th colspan=\"2\">"._("actions")."</th>";
                print "<th>"._("Adminname")."</th>";
                print "<th>"._("Password")."</th>";
                print "<th>"._("domain")."</th>";
                print "<th>"._("admin type")."</th>";
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
		
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $c);
                $username=$row['username'];
		$query2="SELECT * from domainadmin WHERE adminuser='$username'";
		$result2=$handle->query($query2);

		$row2=$result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$domainname=$row['domain_name'];
                $type=$row['type'];
                print "\n<tr class=\"$cssrow\">";
                print "\n<td><a href=\"index.php?action=editadminuser&username=$username&domain=$domain\">"._("Edit adminuser")."</a></td>";
                print "\n<td><a href=\"index.php?action=deleteadminuser&username=$username&domain=$domain\">"._("Delete adminuser")."</a></td>";
                print "</td>\n<td>";
                print $username;
                print "</td>\n<td>";
//                print $row['password'];
                print "******";
                print "</td>\n<td>";
		print $domain;

                print "</td>\n<td>";
		if ($type==0){
			print _("Superuser");
		}
		else if ($type==1){
			print _("Domain Master");
		}

		print "&nbsp;</td>\n</tr>\n";


                }
                print "\n</tbody>\n";
                print "</table>\n";

                }
                else{
                        print "\n"._("No accounts fount")."\n<p>";

                print "<table><tr>";
                print "<td class=\"navi\">\n";
                print "<a href=\"index.php?action=newaccount&domain=$domain&username=$username\">"._("Add administrator")."</a>";
                print "\n</td></tr></table>\n";

        }


}

?>
<!-- </tbody>
</table> -->
<!-- ############################### End adminuser.php ############################################# -->

