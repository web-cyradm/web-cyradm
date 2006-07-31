<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### forwardaccount.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
<?php
if ($authorized){
	if (!empty($_GET['confirmed'])) {
		if (!empty($_GET['setforward']) && $_GET['setforward'] == "1") {
			// delete all first
			$query = "DELETE FROM virtual WHERE alias='".$_GET['username']."'";
			$result = $handle->query($query);
			$forwards = explode("\n", $_GET['forwardto']);
			reset($forwards);
			$query = "INSERT INTO virtual (alias,dest) VALUES ('".$_GET['username']."', '";
			$q = array();
			while (list(, $forward) = each($forwards)) {
				// insert new forwards
				$q[] = substr(trim($forward), 0, 255);
			}
			if (!empty($_GET['metoo'])){
				$keep = ' checked ';
				$q[] = $username;
			} else {
				$keep = '';
			}
			$query .= implode(',', $q)."')";
			$result = $handle->query($query);
			$msg = _("Forward set");
		} elseif (!empty($_GET['setforward']) && $_GET['setforward'] == "2") {
			$query = "DELETE FROM virtual WHERE alias='".$_GET['username']."'";
			$result = $handle->query($query);
			$msg = _("Forwarding removed");
		}
	}

	$query = "SELECT * FROM virtual WHERE alias='".$_GET['username']."'";
	$result = $handle->query($query);
	$cnt = $result->numRows();
	if ($cnt) {
		$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$forw_is_set = 1;
	} else {
		$forw_is_set = 0;
	}
	if (!empty($msg)){
?>
				<span style="color: red;">
					<?php echo $msg;?>
				</span>
<?php
	}
?>
			<h3>
				<?php print _("Forward for account");?>
				<span style="color: red;">
					<?php echo $_GET['username'];?>
				</span>
			</h3>
			<form action="index.php" method="get">
				<input type="hidden" name="action" value="forwardaccount">
				<input type="hidden" name="confirmed" value="true">
				<input type="hidden" name="domain" value="<?php echo $_GET['domain'];?>">
				<input type="hidden" name="username" value="<?php echo $_GET['username'];?>">
				<table>
					<tr>
						<td>
							<input type="radio"
							name="setforward"
							value="1"<?php 
							echo ($forw_is_set)?' checked':'';?>
							>
							<?php 
							echo _("Set forwarding to (one adress per line)");
							?>
							:
						</td>
					</tr>

					<tr>
						<td>
							<textarea name="forwardto" class="inputfield" cols="60" rows="5"><?php
							if ($forw_is_set){
								$forwards_tmp = preg_split('|,\s*|', stripslashes($row['dest']));
								$forwards = array();
							        $keep = '';
								while (list(, $forward) = each($forwards_tmp)){
									// If a mail is to be kept on the server,
									// the $row[dest] also contains the $username
									// -> filter it out
									if (strtolower($forward) != strtolower($_GET['username'])){
										$forwards[] = htmlspecialchars(trim($forward));
									} else {
									       $keep = ' checked ';
								        }
								}
								echo implode("\n", $forwards);
							}
							?></textarea>
							
							<br>
							<input type="checkbox"
							name="metoo" 
							<?php echo (!empty($keep))?($keep):('');?>
							>
							<?php print _("Keep a copy in the user's mailbox");?>
							<br>
							<br>
						</td>
					</tr>

					<tr>
						<td>
							<input type="radio" 
							name="setforward" 
							value="2"<?php 
							echo (!$forw_is_set)?' checked':'';
							?>
							><?php 
							echo _("Remove forwarding");
							?>
						</td>
					</tr>
				</table>
				
				<input class="button"
				type="submit"
				value="<?php echo _("Submit");?>"
				>
				
				<input class="button"
				type="reset"
				value="<?php echo _("Cancel");?>"
				>
			</form>
<?php
} else {
?>
			<h3>
				<?php print $err_msg;?>
			</h3>
			<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
<?php
}
?>
	</td>
</tr>

<!-- #################### forwardaccount.php end #################### -->

