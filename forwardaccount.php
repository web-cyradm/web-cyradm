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
	if (DB::isError($handle)) {
		die (_("Database error"));
	}

    if($_POST['setforward'] == "1" && !empty($_POST['forwardto'])) {
        // delete all first
        $query = "delete from virtual WHERE alias=" . $handle->quote($_GET['username']);
        $result=$handle->query($query);
	$forwards = explode("\n", $_POST['forwardto']);
	reset ($forwards);
	$query = "insert into virtual (alias,dest) VALUES (" . $handle->quote($_GET['username'])  . ", ";
	$q = array();
	while (list(, $forward) = each($forwards)) {
		// insert new forwards
		$q[] = substr(trim($forward), 0, 255);
	}
	if (isset($_POST['metoo'])){
		$keep = ' checked ';
		$q[] = $username;
	} else {
		$keep = '';
	}
	$query .= $handle->quote(implode(',', $q)) . ')';
	$result=$handle->query($query);
        $msg=_("Forward set");
    } elseif($_POST['setforward'] == "2") {
        $query="delete from virtual WHERE alias=" . $handle->quote($_GET['username']);
        $result=$handle->query($query);
        $msg=_("Forwarding removed");
    }

    $query="select * from virtual where alias=" . $handle->quote($_GET['username']);
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
<form action="<?php 
printf ('%s?domain=%s&amp;username=%s&amp;alias=%s&amp;action=%s', 
	$_SERVER['PHP_SELF'],
        htmlspecialchars($_GET['domain']),
	htmlspecialchars($_GET['username']),
	htmlspecialchars($_GET['alias']),
	htmlspecialchars($_GET['action'])
);
?>" method="POST">
<table>
<tr>
<td><input type="radio" name="setforward" value="1"<?php echo ($forw_is_set)?' checked':'';?>><?php echo _("Set forwarding to (one adress per line)");?>:</td>
</tr>
<tr>
<td><textarea name="forwardto" class="inputfield" cols="30" rows="5"><?php
	if ($forw_is_set){
		$forwards_tmp = preg_split('|,\s*|', stripslashes($row['dest']));
		$forwards = array();
		while (list(, $forward) = each($forwards_tmp)){
			// If a mail is to be kept on the server,
			// the $row[dest] also contains the $username
			// filter it out
			if (strtolower($forward) != strtolower($username)){
				$forwards[] = htmlspecialchars(trim($forward));
			}
		}
		echo implode("\n", $forwards);
	}
?></textarea><br>
<input type="checkbox" name="metoo" <?php print $keep ."> " . _("Keep a copy in the user's mailbox");?><br><br>
</td>
</tr>
<tr>
<td><input type="radio" name="setforward" value="2"<?php echo (!$forw_is_set)?' checked':'';?>><?php echo _("Remove forwarding");?></td>
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


