          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

       $cyr_conn = new cyradm;

       $cyr_conn -> imap_login();

	print"<h3>"._("Email adresses defined for user")." <font color=red>".$username."</font></h3>";

	$query="select * from virtual where username='$username'";
        $handle=DB::connect($DSN, true);
        $hnd=$handle->query($query);
        $cnt=$hnd->numRows();
	print "<table cellspacing=\"2\" cellpadding=\"0\"><tr>";
        print "<td class=\"navi\">";
	print "<a href=\"index.php?action=newemail&domain=$domain&username=$username\">"._("New email address")."</a>";	
	print "</td></tr></table><p>";

        $b=0;
        print "<table border=0>";
        print "<tr>";
        print "<th colspan=\"4\">"._("action")."</th>";
        print "<th>"._("Email address")."</th>";
	print "<th>"._("Forward")."</th>";
//        print "<th>"._("Username")."</th>";
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
	  $row=$hnd->fetchRow(DB_FETCHMODE_ASSOC, $c);
	  $alias=$row['alias'];	
          print "<tr class=\"$cssrow\"> \n";
          print "<td><a href=\"index.php?action=editemail&domain=$domain&alias=$alias&username=$username\">"._("Edit Emailadress")."</a></td>";
	  print "\n<td><a href=\"index.php?action=forwardalias&domain=$domain&alias=$alias&username=$username\">"._("Forward")."
</a></td>";

	  print "\n<td><a href=\"index.php?action=vacation&domain=$domain&alias=$alias&username=$username\">"._("Vacation")."
</a></td>";

          print "<td><a href=\"index.php?action=deleteemail&domain=$domain&alias=$alias&username=$username\">"._("Delete Emailadress")."</a></td>";
          print "<td>";
	  print $alias;
          print "</td>\n";

	  print "<td>";
	  print "Forwared or not?\n";
	  print "</td>";

	  if ($c==0) {
//		print "<td rowspan=\"$cnt\">";
//	        print $row['dest'];
//	        print $row['1'];
//	        print "</td>\n<td rowspan=\"$cnt\">";
//		print "******";
//	        print "</td>\n<td rowspan=\"$cnt\">";
		print "<td rowspan=\"$cnt\">";

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
                	print $quota[used]." Kilobytes "._("out of")." ";
			print $quota[qmax]." Kbytes (".sprintf("%.2f",$q_percent)." %)";
          	}
		else{
			print _("Quota not set");
		}
		print "</td>\n</tr>";
	  }
	
	print "&nbsp;</td>\n</tr>\n";

        }
        print "</table>";



?>
</td></tr>


