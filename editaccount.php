          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

       $cyr_conn = new cyradm;

       $cyr_conn -> imap_login();

	print"<h3>Email adresses defined for user <font color=red>".$username."</font></h3>";

	$query="select * from virtual where username='$username'";
        mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
        $hnd=mysql_db_query($MYSQL_DB,$query);
        $cnt=mysql_num_rows($hnd);
	print "<table cellspacing=\"2\" cellpadding=\"0\"><tr>";
        print "<td class=\"navi\">";
	print "<a href=\"index.php?action=newemail&domain=$domain&username=$username\">new&nbsp;email&nbsp;address&nbsp;for&nbsp;this&nbsp;user</a>";	
	print "</td></tr></table><p>";

        $b=0;
        print "<table border=0>";
        print "<tr>";
        print "<th colspan=\"2\">Actions</th>";
        print "<th>Email address</th>";
        print "<th>Username</th>";
	print "<th>Password</th>";
	print "<th>Quota used</th>";
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
	  $alias=mysql_result($hnd,$c,'alias');	
          print "<tr class=\"$cssrow\"> \n";
          print "<td><a href=\"index.php?action=editemail&domain=$domain&alias=$alias&username=$username\">Edit Emailadress</a></td>";
          print "<td><a href=\"index.php?action=deleteemail&domain=$domain&alias=$alias&username=$username\">Delete Emailadress</a></td>";
          print "<td>";
	  print $alias;
          print "</td>\n";

	  if ($c==0) {
		print "<td rowspan=\"$cnt\">";
	        print mysql_result($hnd,$c,'dest');
	        print "</td>\n<td rowspan=\"$cnt\">";
		print "******";
	        print "</td>\n<td rowspan=\"$cnt\">";

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
                	print $quota[used]." Kilobytes out of ";
                	print $quota[qmax]." Kilobytes (".$q_percent." %)";
          	}
		else{
			print "Quota not set";
		}
		print "</td>\n</tr>";
	  }
	
	print "&nbsp;</td>\n</tr>\n";

        }
        print "</table>";
	print "<p>";
	print "<a href=\"index.php?action=newemail&domain=$domain&username=$username\">New Email Adress</a>";



?>
</td></tr>


