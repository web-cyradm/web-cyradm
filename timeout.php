<html>
<head>
<title>Web-cyradm</title>
<link rel="stylesheet" href="css/web-cyradm.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<?php

// Specify location of translation tables
bindtextdomain("timeout", "./locale");

// Choose domain
textdomain("timeout");

?>

<script>
<!--
function setfocus() {
document.form1.login.focus();
}
function entsub() {
  if (window.event && window.event.keyCode == 13)
    document.form1.submit();
  else
    return true;}
// --></script>
</head>

<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload=setfocus()>

<table width="100%" border="0" cellspacing="0" cellpadding="0" height="15%">
 <tr>
        <td colspan="2" height="80" class="banner" bgcolor="#CCCCCC"><img src="images/banner.gif" width="780" height="80" usemap=
"#Map" border="0">
          <map name="Map">
            <area shape="rect" coords="689,2,767,15" href="mailto:luc at delouw.ch">
        </map>
          </td>
  </tr>

</table>

<table width="100%" border="0" height="80%">
  <tr>
    <td align="center" valign="middle"> 
        <table width="450" border="0" cellpadding="1" cellspacing="1" height="150">
          <tr>


          <td bgcolor="#000000">		  
              <table border="0" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" width="450" height="150">
                <tr> 
                  <td colspan="5" bgcolor="#000666">
			<font face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF" size="2">
                    	Web-cyradm</font></b></font></td>
                </tr>
                <tr> 
                  <td colspan="5">&nbsp;</td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                  <td colspan="4">
                    <p align="center"><font face="Verdana,Geneva,Arial,Helvetica,sans-serif" color="#000000" size="2">
                     <?php print _("Your session timed out") ?> </p>
		     <p align=center><font face=Verdana,Geneva,Arial,Helvetica,sans-serif size=2 color=#000000>	
		     <?php print _("If you like to login click") ?> <a class="navi" href=index.php><?php print _("here") ?></a></font></p>
                      </font></p><br>                    
                  </td>
                </tr>
              </table>			
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>


<table width="100%" border="0" cellspacing="0" cellpadding="0"  height="5%">
<tr>
        <td height="20" valign="bottom" bgcolor="#CCCCCC">&nbsp;</td>
        <td height="20" valign="bottom" class="footer" bgcolor="#CCCCCC">
          &copy;  2002 by Luc de Louw | contact<a href="mailto:luc at delouw.ch"> luc at delouw.ch</a>| see
<a href="http://www.delouw.ch/linux/web-cyradm/" target=_new>Web-cyradm</a>     </td>
  </tr>
</table>

</body>
</html>

