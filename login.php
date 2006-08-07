<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<script type="text/javascript">
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

<script language="JavaScript" type="text/javascript">
function selectLang()
{
        var lang_page = 'index.php?LANG=' + document.form1.LANG[document.form1.LANG.selectedIndex].value;
        self.location = lang_page;
}

</script>

<!-- </head> -->

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height: 15%;">
 <tr>
         <td></td>
  </tr>

</table>
<!-- -->
<form name="form1" method="post" action="auth.inc.php">
<table width="100%" border="0" style="height: 80%;">
  <tr>
    <td align="center" valign="middle"> 
        <table width="450" border="0" cellpadding="1" cellspacing="1" style="height: 150px;">
          <tr>


          <td bgcolor="#000000">		  
              <table border="0" bgcolor="#FFFFFF" cellpadding="2" cellspacing="0" width="450" style="height: 150px;">
                <tr> 
                  <td colspan="5" bgcolor="#000666">
			<font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b>
                    	Web-cyradm</b></font></td>
                </tr>
                <tr> 
                  <td colspan="5">&nbsp;</td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                  <td colspan="4">
                    <p><font face="Verdana,Geneva,Arial,Helvetica,sans-serif" color="#000000" size="2">
		     <?php print _("Welcome to Web-cyradm") ?><br>  
                     <?php print _("This is only for authorized users") ?><br><br>
		     <?php print _("Please authenticate yourself") ?>
		
                      </font></p><br>                    
                  </td>
                </tr>
                <tr> 
                  <td></td>
                  <td nowrap> 
                    <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif" color="#999999" size="2"><b>
                      <?php print _("Username") ?></b></font></div>
                  </td>
                  <td colspan="3"> 
                    <input type="text" name="login">
                  </td>
                </tr>
                <tr> 
                  <td width="10"> 
                    <div align="right"><font color="#999999"><b></b></font></div>
                  </td>
                  <td><font face="Verdana, Arial, Helvetica, sans-serif" color="#999999" size="2"><b><?php print _("Password") ?></b></font></td>
                  <td> 
                    <input type="password" name="login_password" onkeypress="return entsub()">
                  </td>
                  <td > 
                    <div align="right">
<input type="submit" value="<?php print _("Submit")?>">
</div>
                  </td>
                  <td width="10"></td>
                </tr>
		<tr>
		<td></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#999999" size="2"><b><?php print _("Select language")?></b></font></td>
		<td>
<select size="1" name="LANG" onchange= "selectLang()">

<?php
	if (!$LANG) {
		print "<option selected=\"selected\">Select language</option>";
	}

foreach ($nls['aliases'] as $l){

	if ($l == $LANG){
		print "<option selected=\"selected\"";
		print " value=\"$l\">";
		print $nls['languages'][$l];
		print "</option>\n";
	}
	else {
		print "<option";
		print " value=\"$l\">";
		print $nls['languages'][$l];
		print "</option>\n";
	}
}


?>

 </select> 

		</td>
		</tr>
                <tr> 
               <td colspan="5">&nbsp;</td> 
                </tr>
              </table>			
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</form> 
<script type="text/javascript">
<!--
document.form1.login.focus();
//-->
</script>
<?php
include WC_BASE . "/footer.inc.php";
?>
