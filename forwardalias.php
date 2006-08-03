<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### forwardalias.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

<?php
if ($authorized){
		if (!empty($_GET['confirmed']) && empty($_GET['cancel'])){
			include_once WC_BASE . '/lib/sieve-php.lib.php';
			include_once WC_BASE . '/lib/sieve_strs.php';
			$daemon = new sieve($CYRUS['HOST'],"2000", $CYRUS['ADMIN'], $CYRUS['PASS'], $_GET['username']);
				switch ($_GET['mode']){
				case 'set':
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$forwards_script ='';
						$forwardwhere = $_GET['forwards'];
						while (preg_match ("/(.*),(.*$)/U",$forwardwhere, $matches)){
							$forwards_script .= 'redirect "' . trim($matches[1]) . '";'."\n";
							$forwardwhere = $matches[2];
						}
						$forwards_script .= 'redirect "' . trim($forwardwhere) . '";';
						if (!empty($_GET['metoo']) && $_GET['metoo'] == 'on'){
							$forwards_script .= "\nkeep;";
						}
						$forwards_script .= "\n";
						$old_script = $sieve_str->get_old_script($daemon);

						// vacation is the first rule.
						if (preg_match ("/(require.*)(redirect \"|$)/Uis", $old_script, $matches)){
							$vacation_script = $matches[1];
						} else {
							$vacation_script = '';
						}
						$script = $vacation_script.$forwards_script;
						if ($daemon->sieve_sendscript('sieve', $script) &&
						  $daemon->sieve_setactivescript('sieve')) {
							$_msg = _("Forward set");
						} else {
							$_msg = _("Failure in setting forward");
						}
					} else {
						$_msg = _("Wrong password");
					}
					?>
					<span style="font-size: large; font-weight: medium;">
						<?php echo $_msg;?>
					</span>
					<?php
					break;

				case 'unset':
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$old_script = $sieve_str->get_old_script($daemon);
						if (preg_match("/(require \".*)(redirect \"|$)/Uis",$old_script,$matches)){
							$vacation_script = $matches[1];
							if ($daemon->sieve_sendscript('sieve', $vacation_script)){
								$_msg = _("Forwarding removed");
							} else {
								$_msg = _("Failure in removing forwarding");
							}
						} else {
							if ($daemon->sieve_listscripts() !== FALSE){
								if (in_array('sieve', $daemon->response)){
									if ($daemon->sieve_deletescript('sieve')) {
										$_msg = _("Forwarding removed");
									} else {
										$_msg = _("Failure in removing forwarding");
									}
								} else {
									// TODO: Comeup with a better message here...
									$_msg = _("Forwarding removed");
								}
							} else {
								// TODO: ... and comeup with a better message here.
								$_msg = _("Forwarding removed");
							}
						}
					} else {
						$_msg = _("Failed to login");
					}
					?>
					<span style="font-size: large; font-weight: medium;">
						<?php echo $_msg;?>
					</span>
					<?php
					break;

				default:
					?>
					<span style="font-size: large; font-weight: medium;">
						<?php print _("Not possible");?>
					</span>
					<?php
					break;
				}
				include WC_BASE . "/editaccount.php";
			} elseif (!empty($_GET['confirmed']) && !empty($_GET['cancel'])) {
				include WC_BASE . "/editaccount.php";
			} elseif (empty($_GET['confirmed'])){
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
						die ("<b>" . _("You can't forward this email address with 'Allow Free Mail Addressess' set to off!") . "</b>");
    					}
				}
				?>

				<h3>
					<?php print _("Forward for emailadress");?>
					<span style="color: red;">
						<?php echo $_GET['alias']; ?>
					</span>
				</h3>

				<form action="index.php" method="get" style="border: 0px dashed blue;">

					<input type="hidden" name="action" value="forwardalias">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php print $_GET['domain']; ?>"> 
					<input type="hidden" name="alias" value="<?php print $_GET['alias']; ?>"> 
					<input type="hidden" name="username" value="<?php echo $_GET['username'];?>">

					<?php
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$old_script = $sieve_str->get_old_script($daemon);
						if (preg_match("/redirect \".*$/siU", $old_script, $matches)){
							$forwards_script = $matches[0];
							$forwards_text = '';
							while (preg_match ("/(redirect \")(.*)(\";)(.*$)/siU", $forwards_script, $matches)){
								$forwards_text .= $matches[2].', '; 
								$forwards_script = $matches[4];
							}
							$forwards_text = rtrim ($forwards_text, ', ');
							if (preg_match ("/keep;/i", $forwards_script, $matches)){
								$keep = 'checked';
							} else {
								$keep = '';
							}
						} else {
							$forwards_text ='';
							$keep = '';
						}
					} else {
						$forwards_text = '';
					}
					?>

					<br>

					<input type="radio" name="mode" value="set"
					checked><?php print _("Set forwarding to") . " " . _("(Seperate multiple values with ,)") ?>:

					<input class="inputfield" type="text"
					name="forwards" value="<?php print $forwards_text;?>" size="50"><br>
					
					<input type="checkbox" name="metoo" 
					<?php print $keep."> "._("Keep a copy in the user's mailbox");?><br>
					<br>
					
					<input type="radio" name="mode"
					value="unset"><?php
					print _("Remove forwarding");?><br>
					<br>
					<br>

					<input class="button" type="submit"
					value="<?php print _("Submit")?>">

					<input class="button" name="cancel"
					type="submit"
					value="<?php print _("Cancel");?>"> 
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
}
?>
	</td>
</tr>

<!-- #################### forwardalias.php end #################### -->

