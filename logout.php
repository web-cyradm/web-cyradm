          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

session_start();
//print session_id();
if(session_destroy()){
	print "<html>
<!-- <head>
<title>Web-cyradm</title>
<meta http-equiv=Content-Type content=text/html; charset=iso-8859-1>
</head>
<body bgcolor=#FFFFFF text=#000000 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0> -->
<form name=form1 method=post action=>
<table width=100% border=0 height=100%>
  <tr>
      <td align=center valign=middle>
	  <table width=450 border=0 cellpadding=1 cellspacing=1 height=150>
          <tr>
          <td bgcolor=#000000>		  
              <table border=0 bgcolor=#FFFFFF cellpadding=0 cellspacing=0 width=450 height=150>
                <tr> 
                  <td bgcolor=#000666><font face=Verdana, Arial, Helvetica, sans-serif><b><font color=#FFFFFF size=2>Web-cyradm</font></b></font></td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
                <tr> 
                  <td> 
                    <p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif color=#000000 size=2>Thank 
                      you for using Web-cyradm</font></p>
                    <p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif size=2 color=#000000><b><font size=3>You 
                      are logged out</font></b></font></p>
                    <p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif size=2 color=#000000>If 
                      you like to login click <a class=\"navi\" href=index.php>here</a></font></p>
                  </td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
              </table>
			
          </td>
        </tr>
      </table>
	  </td>
  </tr>
</table>
</form>
</body>
</html>
";
}

?>
</td></tr>

