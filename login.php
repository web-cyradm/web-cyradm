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
<!-- </head> -->

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height: 15%;">
 <tr>
         <td></td>
  </tr>

</table>
<!-- -->
<form name="form1" method="post" action="auth.inc.php">
<!-- <input type="hidden" name="LANG" value="<?php print $LANG ?>"> -->
<table width="100%" border="0" style="height: 80%;">
  <tr>
    <td align="center" valign="middle"> 
        <table width="450" border="0" cellpadding="1" cellspacing="1" style="height: 150px;">
          <tr>


          <td bgcolor="#000000">		  
              <table border="0" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" width="450" style="height: 150px;">
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
                    <div align="left"><font color="#999999"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
                      <?php print _("Username") ?></font></b></font></div>
                  </td>
                  <td colspan="3"> 
                    <input type="text" name="login">
                  </td>
                </tr>
                <tr> 
                  <td width="10"> 
                    <div align="right"><font color="#999999"><b></b></font></div>
                  </td>
                  <td><font color="#999999"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><?php print _("Password") ?></font></b></font></td>
                  <td> 
                    <input type="password" name="password" onkeypress="return entsub()">
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
		<td><font color="#999999"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><?php print _("Select language")?></td>
		<td>
<select size="1" name="LANG" onchange= "selectLang()">

<script language="JavaScript" type="text/javascript">
function selectLang()
{
    if (document.form1.login.value == '' &&
        document.form1.password.value == '') {
        var lang_page = 'index.php?LANG=' + document.form1.LANG[document.form1.LANG.selectedIndex].value;
        self.location = lang_page;
    }
}

</script>

<?php

foreach ($nls['aliases'] as $l){

	if ($l == $LANG){
		print "<option selected";
	}
	else{
		print "<option";
	}

	print " value=\"$l\">";
	print $nls['languages'][$l];
	
//	print $l;
	print "</option>";
}

?>

 </select> 
</form>

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

<?php
include WC_BASE . "/footer.inc.php";

?>

