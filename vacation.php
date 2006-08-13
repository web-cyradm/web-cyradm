<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### vacation.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

<?php
if ($authorized){
			if (!empty($_GET['confirmed']) && empty($_GET['cancel'])){
				include_once WC_BASE . '/lib/sieve-php.lib.php';
				include_once WC_BASE . '/lib/sieve_strs.php';
				$daemon = new sieve($CYRUS['HOST'],"2000", $CYRUS['ADMIN'], $CYRUS['PASS'], $_GET['username']);
				switch ($_GET['mode']) {
				case 'set':
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$mess = $_GET['vacation_text'];
						$mess2 = preg_replace ("/\s*$/s",'',$mess);
						$mess3 = preg_replace ("/\r/",'',$mess2);
						if (preg_match ("/subject.*\n(.*)$/iUs", $mess3, $matches)){
							// remove 'subject:' and trailing space.
							preg_match ("/subject.*(\w.*)\s*\r?\n/iU",$matches[0],$matches2);
							// adding extra slashes because
							// sieve_sendscript() calls stripslaches()
							$subject = addslashes($matches2[1]);
							$text = addslashes($matches[1]);
						} else {
							$subject = addslashes($_GET['subject']);
							$text = addslashes($mess2);
						}
						// remove leading lines.
						if (preg_match ("/^\s*\n*(.*)$/s", $text, $matches)){
							$text = $matches[1];
						} else {
							$text = '';
						}
						$vacation_script = 'require "vacation"; vacation :days 1 :addresses ["'.$_GET['alias'].'"] :subject "'.$subject.'" "'.$text."\";\n";
						$old_script = $sieve_str->get_old_script($daemon);
						if (preg_match("/redirect \".*$/siU", $old_script, $matches)){
							$forwards_script = $matches[0];
						} else {
							$forwards_script ='';
						}
						$script = $vacation_script.$forwards_script;
						$script = iconv($charset,"UTF-8",$script);
						if ($daemon->sieve_sendscript('sieve', $script)){
							if ($daemon->sieve_setactivescript('sieve')){
								$_msg = _("Vacation notice set");
							} else {
								$_msg = _("Failed to activate vacation");
							}
						} else {
							$_msg = _("Failure in modifying vacation notice");
						}
					} else {
						$_msg = _("Failed to login");
					}
					?>
					<span style="font-weight: bold; font-size: large;">
						<?php echo $_msg;?>
					</span>
					<?php
					break;

				case 'unset':
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$old_script = $sieve_str->get_old_script($daemon);
						if (preg_match ("/redirect \".*$/is",$old_script,$matches)){
							$forwards_script = $matches[0];
							if ($daemon->sieve_sendscript('sieve', $forwards_script)){
								$_msg = _("Vacation notice unset");
							} else {
								$_msg = _("Failure in unseting vacation notice");
							}
						} else {
							if ($daemon->sieve_deletescript('sieve')) {
								$_msg = _("Vacation notice removed");
							} else {
								$_msg = _("Failure in removing vacation notice");
							}
						}
					} else {
						$_msg = _("Failed to login");
					}
					?>
					<span style="font-weight: bold; font-size: large;">
						<?php echo $_msg;?>
					</span>
					<?php
					break;

				default:
					?>
					<span style="font-weight: bold; font-size: large;">
						<?php print _("Not possible");?>
					</span>
					<?php
					break;
				}
				include WC_BASE . "/editaccount.php";
			} elseif (!empty($_GET['confirmed']) && !empty($_GET['cancel'])){
				include WC_BASE . "/editaccount.php";
			} elseif (empty($_GET['confirmed'])) {

				include_once WC_BASE . '/lib/sieve-php.lib.php';
				include_once WC_BASE . '/lib/sieve_strs.php';
				$daemon = new sieve($CYRUS['HOST'],"2000", $CYRUS['ADMIN'], $CYRUS['PASS'], $_GET['username']);

    				$query = "SELECT * FROM domain WHERE domain_name='".$_GET['domain']."'";
				$result = $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$freeaddress = $row['freeaddress'];
				if ($freeaddress!="YES") {
                            	    $aliasname = spliti("@",$_GET['alias'],2);
				    $aliasname = $aliasname[0];
				    $alias_new = $aliasname."@".$_GET['domain'];
				    if ($alias_new != $_GET['alias']) {
					die ("<b>" . _("You can't set Vacation Message for this email address with 'Allow Free Mail Addressess' set to off!") . "</b>");
				    }
				}
				?>
				<h3>
					<?php print _("Vacation message for emailadress");?>
					<span style="color: red;">
						<?php echo $_GET['alias']; ?>
					</span>
				</h3>

				<form action="index.php" method="get">

					<input type="hidden" name="action" value="vacation">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php print $_GET['domain']; ?>"> 
					<input type="hidden" name="alias" value="<?php print $_GET['alias']; ?>"> 
					<input type="hidden" name="username" value="<?php echo $_GET['username'];?>">
				
					<table>
						<tr>
							<td>
								<input class="menu" type="radio"
								name="mode" value="set" checked><?php
								print _("Set vacation notice");?>
							</td>
						</tr>
						<tr>
							<td>
								<input class="menu" type="radio"
								name="mode" value="unset"><?php
								print _("Remove vacation notice");?>
							</td>
						</tr>
<?php
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$old_script = iconv('UTF-8',$charset,$sieve_str->get_old_script($daemon));
						if (preg_match("/(require \".*)(redirect \"|$)/Uis", $old_script, $matches)){
							if (preg_match("/subject \".*/is",$matches[1],$matches2)){
								$vacation_script = $matches2[0];
							} else {
								$vacation_script = "";
							}
						} else {
							$vacation_script = "";
						}
					} else {
						$vacation_script = "";
					}
					preg_match ("/(subject \".*)\" \"(.*$)/is", $vacation_script, $matches);
					if (count($matches) >= 2){
						$subject = stripslashes(preg_replace("/subject \"/", "", $matches[1]));
						$vacation_script = $matches[2];
						$vacation_script = stripslashes(preg_replace("/\";$/", "", $vacation_script));
					} else {
						$subject = '';
						$vacation_script = '';
					}
?>
						<tr>
							<td>
								<?php print _("Subject"); ?>:<br>
								<input type="text" name="subject"
								size="60" value="<?php echo htmlspecialchars($subject); ?>">
							</td>
						</tr>
						<tr>
							<td>
								<?php print _("Body"); ?>:<br>
								<textarea name="vacation_text"
								rows="6" cols="55"><?php
								print $vacation_script;?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<input class="button" type="submit"
								value="<?php print _("Submit");?>"> 

								<input class="button" name="cancel"
								type="submit" value="<?php print _("Cancel");?>">
							</td>
						</tr>
					</table>
				</form>
<?php
			}
} else {
?>
	<h3>
		<?php echo $err_msg;?>
		<a href="index.php?action=editaccount&domain=<?php echo $_GET['domain'];?>&username=<?php echo $_GET['username'];?>"><?php print _("Back");?></a>
	</h3>
<?php
} // End of if ($authorized)
?>
	</td>
</tr>

<!-- #################### vacation.php end #################### -->
