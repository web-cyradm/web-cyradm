          <tr>
        <td width="10">
<?php
if($err_msg) {
   echo "<font color=\"red\">$err_msg</font>";
} else {
   echo "&nbsp;";
}
?></td>
        <td valign="top">

<?php

if ($authorized){
    $handle=DB::connect($DSN, true);
    if($setforward==1 && !empty($forwardto)) {
        // delete all first
        $query="delete from virtual WHERE alias='$username'";
        $result=$handle->query($query);
        // insert new forward
        $query="insert into virtual (alias,dest) VALUES('$username','$forwardto')";
        $result=$handle->query($query);
        $msg=_("Forward set");
    } elseif($setforward==2) {
        $query="delete from virtual WHERE alias='$username'";
        $result=$handle->query($query);
        $msg=_("Forwarding removed");
    }

    $query="select * from virtual where alias='$username'";
    $result=$handle->query($query);
    $row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
    if(is_array($row))
        $forw_is_set=1;
    else
        $forw_is_set=0;
    if($msg) {
       echo "<font color=\"red\">$msg</font>";
    }
?>
<h3><?php print _("Forward for account")." <font color=\"red\">".$username;?></font></h3> 
<form action="<?php echo $PHP_SELF."?domain=$domain&username=$username&alias=$alias&action=$action";?>" method="POST">
<table>
<tr>
<td><input type="radio" name="setforward" value="1"<?php echo ($forw_is_set)?' checked':'';?>><?php echo _("Set forwarding to");?>:</td>
<td><input class="inputfield" type="text" name="forwardto" value="<?php echo ($forw_is_set)?$row['dest']:'';?>" size="30"></td>
</tr>
<td colspan="2"><input type="radio" name="setforward" value="2"<?php echo (!$forw_is_set)?' checked':'';?>><?php echo _("Remove forwarding");?></td>
</tr>
</table>
<input class="button" type="submit" value="<?php echo _("Submit");?>">
<input class="button" type="reset" value="<?php echo _("Cancel");?>">
</form>
<?php

} else {
	print "<h3>".$err_msg."</h3>";
}
?>
</td></tr>


