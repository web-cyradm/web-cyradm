<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### editemail.php start #################### -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top" align="left" style="border: 0px dashed green;">

<?php
if ($authorized){
	$query = "SELECT * FROM domain WHERE domain_name='".$_GET['domain']."'";
	$result = $handle->query($query);
	$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
	$freeaddress=$row['freeaddress'];

	$query = "SELECT * FROM virtual WHERE alias='".$_GET['alias']."'";
	$result = $handle->query($query);
	$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
	$alias = $row['alias'];

	if (!empty($_GET['confirmed']) && empty($_GET['cancel'])) {
		if ($freeaddress!="YES") {
			$query = "UPDATE virtual SET alias='".$_GET['newalias']."@".$_GET['domain']."', dest='".$_GET['newdest']."' WHERE alias='".$alias."' AND username='".$_GET['username']."'";
		} else {
			$query = "UPDATE virtual SET alias='".$_GET['newalias']."@".$_GET['aliasdomain']."', dest='".$_GET['newdest']."' WHERE alias='".$alias."' AND username='".$_GET['username']."'";
		}
		$result = $handle->query($query);
		if (!DB::isError($result)){
?>
			<h3>
				<?php print _("Successfully changed");?>
			</h3>
			<?php
		} else {
			?>
			<p>
				<?php print _("Database error, please try again");?>
			</p>
			<?php
		}
		include WC_BASE . "/editaccount.php";
	} elseif (!empty($_GET['cancel'])) {
		include WC_BASE . "/editaccount.php";
	} else {
		$alias_orig = spliti('@',$alias,2);
		$aliasname = $alias_orig[0];
		$aliasdomain = $alias_orig[1];
		$dest = $row['dest'];
		$username = $row['username'];
		if ($freeaddress!="YES") {
			$alias_new = $aliasname . "@" . $_GET['domain'];
			if ($alias_new != $alias) {
				die ("<b>" . _("You can't edit this email address with 'Allow Free Mail Addressess' set to off!") . "</b>");
			}
		}
?>
				<h3>
                                        <?php print _("Edit emailadress for user");?>:
                                        <span style="color: red;">
                                                <?php echo $_GET['username'];?>
                                        </span>
                                </h3>
				<form action="index.php" method="get">

					<input type="hidden" name="action" value="editemail">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>"> 
					<input type="hidden" name="alias" value="<?php echo $alias; ?>">
					<input type="hidden" name="username" value="<?php echo $username;?>">

					<table>

						<tr>
							<td>
								<?php print _("Emailadress");?>
							</td>

							<td>
								<input class="inputfield" 
								type="text" size="30" 
								name="newalias" 
								value="<?php echo $aliasname;?>">
								<?php
								    if ($freeaddress!="YES") {
									echo "@" . $_GET['domain'];
								    } else {
									    echo "@";
								?>
									<input class="inputfield"
									type="text" size="20"
									name="aliasdomain"
									value="<?php echo $aliasdomain;?>">
								<?php
								    }
								?>
							</td>
						</tr>

						<tr>
							<td width=150>
								<?php print _("Destination");?>
							</td>

							<td>
								<input class="inputfield"
								type="text" size="30"
								name="newdest" value="<?php echo $dest;?>">
							</td>
						</tr>


						<tr>
							<td>
								<input class="button" type="submit"
                                                                value="<?php print _("Submit");?>">

                                                                <input class="button" type="submit"
                                                                name="cancel" value="<?php print _("Cancel"); ?>">
							</td>
						</tr>

					</table>
				</form>
				<?php
			} // End of if (empty($confirmed))
		} else {
			?>
			<h3>
				<?php echo $err_msg;?>
			</h3>
			<a href="index.php?action=editaccount&domain=<?php echo $_GET['domain'];?>&username=<?php echo $_GET['username'];?>"><?php print _("Back");?></a>
			<?php
		} // End of if ($authorized)
		?>
	</td>
</tr>

<!-- #################### editemail.php end #################### -->

