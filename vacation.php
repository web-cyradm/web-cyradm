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
		$handle = DB::connect($DB['DSN'], true);
		if (DB::isError($handle)) {
			die (_("Database error"));
		}

		if ($authorized){
			$query = "select * from virtual where alias='$alias'";
			$result = $handle->query($query);
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$dest = $row['dest'];
			$username = $row['username'];

			include WC_BASE . '/lib/sieve-php.lib';
			include WC_BASE . '/lib/sieve_strs.php';
			$query = "select * from accountuser where username='$dest'";
			$handle = DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result = $handle->query($query);
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$password = $row['password'];
			$daemon = new sieve("localhost", "2000", $username, $CYRUS['PASS'], $CYRUS['ADMIN']);

			if (! empty($confirmed)){
				switch ($mode) {
				case 'set':
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$mess = $vacation_text;
						$mess2 = preg_replace ("/\s*$/s",'',$mess);
						$mess3 = preg_replace ("/\r/",'',$mess2);
						if (preg_match ("/subject.*\n(.*)$/iUs", $mess3, $matches)){
							// remove 'subject:' and trailing space.
							preg_match ("/subject.*(\w.*)\s*\r?\n/iU",$matches[0],$matches2);
							$subject = $matches2[1];
							$text = $matches[1];
						} else {
							$subject = 'On vacation messages';
							$text = $mess2;
						}
						// remove leading lines.
						if (preg_match ("/^\s*\n*(.*)$/s", $text, $matches)){
							$text = $matches[1];
						} else {
							$text = '';
						}
						$vacation_script = 'require "vacation"; vacation :days 1 :addresses ["'.$alias.'"] :subject "'.$subject.'" "'.$text."\";\n";
						$old_script = $sieve_str->get_old_script($daemon);
						if (preg_match("/redirect \".*$/siU", $old_script, $matches)){
							$forwards_script = $matches[0];
						} else {
							$forwards_script ='';
						}
						$script = $vacation_script.$forwards_script;
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

				include WC_BASE . "/browseaccounts.php";
			} // End of if (! empty($confirmed))


			if (empty($confirmed)){

    				$query = "select * from domain where domain_name='$domain'";

				$result = $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$freeaddress = $row['freeaddress'];
				if ($freeaddress!="YES") { 
                            	    $alias_orig = $alias;
				    $alias = spliti("@",$alias);
				    $alias = $alias[0];
				    $alias_new = $alias . "@" . $domain;
				    if ($alias_new!=$alias_orig) {
					die ("<b>" . _("You can't set Vacation Message for this email address with 'Allow Free Mail Addressess' set to off!") . "</b>");
				    }
				}
				
				if (isset($result_array)){
					print $result_array[0];
				}

				?>
				<h3>
					<?php print _("Vacation message for emailadress");?>
					<span style="color: red;">
						<?php
						printf ("%s@%s", $alias, $domain);
						?>
					</span>
				</h3>

				<form action="index.php" method="get">

					<input type="hidden" name="action"
					value="vacation">
					<input type="hidden" name="confirmed"
					value="true">
					<input type="hidden" name="domain"
					value="<?php print $domain ?>"> 
					<input type="hidden" name="alias" value="<?php print $alias; if ($freeaddress!="YES") { print "@" . $domain; } ?>"> 
					<input type="hidden" name="username"
					value="<?php echo $username;?>">

					<input class="menu" type="radio"
					name="mode" value="set" checked><?php
					print _("Set vacation notice");?><br>

					<input class="menu" type="radio"
					name="mode" value="unset"><?php
					print _("Remove vacation notice");?><br>

					<?php
					if ($daemon->sieve_login()){
						$sieve_str = new sieve_strs;
						$old_script = utf8_decode($sieve_str->get_old_script($daemon));
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
						$vacation_script = $matches[1]."\n\n".$matches[2];
						$vacation_script2 = preg_replace("/subject \"/", "Subject: ", $vacation_script);
						$vacation_script = preg_replace("/\";$/", "", $vacation_script2);
					} else {
						$vacation_script = '';
					}
					?>
					<textarea name="vacation_text"
					rows="6" cols="55"><?php
					print $vacation_script;
					?></textarea><br>

					<input class="button"
					type="submit"
					value="<?php print _("Submit");?>"> 

				</form>
				<?php
			} // End of if (empty($confirmed))
		} else {
			?>
			<h3>
				<?php echo $err_msg;?>
			</h3>
			<?php
		} // End of if ($authorized)
		?>
	</td>
</tr>

<!-- #################### vacation.php end #################### -->
