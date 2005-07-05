<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
        header("Location: index.php");
        exit();
}
?>
<!-- #################################### Start editservices ################################# -->
<tr>
        <td width="10">&nbsp;</td>

        <td valign="top">

        <h3>
                <?php print _("Edit services for user");?>
                <span style="color: red;">
                        <?php echo $username;?>
                </span>
        </h3>

<?php
$query = "SELECT * FROM accountuser where username ='".$_GET['username']."'";
$result = $handle->query($query);
if (DB::isError($result)) {
	die (_("Database error"));
}
$cnt = $result->numRows($result);

$query2 = "SELECT status FROM virtual where username ='".$_GET['username']."' LIMIT 1";
$result2 = $handle->query($query2);

if (DB::isError($result2)) {
        die (_("Database error"));
}

if ($cnt){
	$row=$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0 );
	$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0 );
	if ($row['imap']){
		$imap_checked="checked";
	}
	if ($row['pop']){
                $pop_checked="checked";
        }
	if ($row['sieve']){
                $sieve_checked="checked";
        }
	if ($row['smtpauth']){
                $smtpauth_checked="checked";
        }
        if ($row2['status']){
                $smtp_checked="checked";
        }
}


if (!$confirmed){

	?>
	<form action="index.php" method="get">
	<input type="hidden" name="action" value="editservices">
	<input type="hidden" name="domain" value="<?php print $_GET['domain']?>">
	<input type="hidden" name="username" value="<?php print $_GET['username']?>">
	<input type="hidden" name="confirmed" value="true">
	<table>
		<tr>
			<th><?php print _("Service");?></th>
			<th><?php print _("Status");?></th>
		</tr>
		<tr>
			<td><?php print _("Fetch mail via IMAP client");?></td>
			<td><input name="imap" value="1" type="checkbox" <?php print $imap_checked?>></td>
		</tr>
                <tr>
                        <td><?php print _("Fetch mail via POP client");?></td>
                        <td><input name="pop" value="1" type="checkbox" <?php print $pop_checked?>></td>
                </tr>
                <tr>
                        <td><?php print _("Set vacation message and filter rules with sieve");?></td>
                        <td><input name="sieve" value="1" type="checkbox" <?print $sieve_checked?>></td>
                </tr>

                <tr>
                        <td><?php print _("Send E-Mails via smtp authentication");?></td>
                        <td><input name="smtpauth" value="1" type="checkbox" <?print $smtpauth_checked?>></td>
                </tr>

                <tr>
                        <td><?php print _("Receive E-Mails via smtp");?></td>
                        <td><input name="smtp" value="1" type="checkbox" <?print $smtp_checked?>></td>
                </tr>




	</table>
	<input type="submit" value="<?php print _("Change");?>">

<?php
}
if ($confirmed){

	$query_update="UPDATE accountuser SET imap='".$_GET['imap']."', pop='".$_GET['pop']."', sieve='".$_GET['sieve']."', smtpauth='".$_GET['smtpauth']."' WHERE username='".$_GET['username']."'";
	$result2 = $handle->query($query_update);
	$query_update_alias="UPDATE virtual SET status='".$_GET['smtp']."' WHERE username='".$_GET['username']."'";
	$result3 = $handle->query($query_update_alias);
	if ($result2 && $result3){
		print _("Services successfully changed");
	}

}

?>




