          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

session_start();
//print session_id();
if(session_destroy()){
	print "<html>";
	print "<title>Web-cyradm</title>";
	print "<meta http-equiv=Content-Type content=text/html; charset=iso-8859-1>";
	print "</head>";
	print "<body bgcolor=#FFFFFF text=#000000 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>";
	print "<form name=form1 method=post action=>";
	print "<table width=100% border=0 height=100%>";
	print "<tr>";	
	print "<td align=center valign=middle>";
	print "<table width=450 border=0 cellpadding=1 cellspacing=1 height=150>";
	print "<tr>";
	print "<td bgcolor=#000000>";
	print "<table border=0 bgcolor=#FFFFFF cellpadding=0 cellspacing=0 width=450 height=150>";
	print "<tr>"; 
	print "<td bgcolor=#000666><font face=Verdana, Arial, Helvetica, sans-serif><b><font color=#FFFFFF size=2>Web-cyradm</font></b></font></td>";
	print "</tr>";
	print "<tr> ";
	print "<td>&nbsp;</td>";
	print "</tr>";
	print "<tr> ";
	print "<td> ";
	print "<p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif color=#000000 size=2>";
	print _("Thank you for using Web-cyradm")."</font></p>";
	print "<p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif size=2 color=#000000><b><font size=3>";
	print _("You are logged out")."</font></b></font></p>";
        print "<p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif size=2 color=#000000>"; 
        print _("If you like to login click")." <a class=\"navi\" href=index.php>"._("here")."</a></font></p>";
        print "</td>";
        print "</tr>";
        print "<tr>";
        print "<td>&nbsp;</td>";
	print "</tr>";
	print "</table>";
			
	print "</td>";
	print "</tr>";
	print "</table>";
	print "</td>";
	print "</tr>";
	print "</table>";
	print "</form>";
	print "</body>";
	print "</html>";

}

?>
</td></tr>

