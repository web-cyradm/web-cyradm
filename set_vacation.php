          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">

<?php

if ($authorized){

	$query="select * from virtual where alias='$alias'";
	$handle=DB::connect($DSN, true);
	$result=$handle->query($query);
	$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
//	$alias=$row['alias'];
	$dest=$row['dest'];
	$username=$row['username'];

	include ('lib/sieve-php.lib');
	include ('lib/sieve_strs.php');
	$query="select * from accountuser where username='$dest'";
	$handle=DB::connect($DSN, true);
	$result=$handle->query($query);
	$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
	$password=$row['password'];
	//$daemon = new sieve("localhost","2000",$dest,$password,"");
	$daemon = new sieve("localhost","2000", $user, $CYRUS_PASSWORD, $CYRUS_USERNAME);

	if ($confirmed){

//	        $query="UPDATE virtual SET alias='$newalias@$domain', dest='$dest' WHERE alias='$alias'";
	switch ($mode) {
		case 'set':
			if ($daemon->sieve_login()) {
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
				} else $text = '';
					$vacation_script = 'require "vacation"; vacation :days 1 :addresses ["'.$alias.'"] :subject "'.$subject.'" "'.$text."\";\n";
					$old_script = $sieve_str->get_old_script($daemon);
					if (preg_match ("/redirect \".*$/siU", $old_script, $matches)){
						$forwards_script = $matches[0];
					} else $forwards_script ='';
						$script = $vacation_script.$forwards_script;
						if ($daemon->sieve_sendscript('sieve', $script) ) {
							if ($daemon->sieve_setactivescript('sieve')) {
								print "<big><b>"._("Vacation notice set")."</b></big>";
							} else {
								print "<big><b>"._("Failed to activate vacation")."</b></big>";
							}
						} else {
							print "<big><b>"._("Failure in modifying vacation notice")."</b></big>";
						}
					} else {
						print "<big><b>"._("Failed to login")."</b></big><p><p>";
				}
			break;

		case 'unset':
			if ($daemon->sieve_login() ){
				$sieve_str = new sieve_strs;
				$old_script = $sieve_str->get_old_script($daemon);
					if (preg_match ("/redirect \".*$/is",$old_script,$matches) ){
						$forwards_script = $matches[0];
						if ($daemon->sieve_sendscript('sieve', $forwards_script) ) {
							print "<big><b>"._("Vacation notice unset")."</b></big>";
						} else {
							print "<big><b>"._("Failure in unseting vacation notice")."</b></big>";
						}
					} else {
						if ($daemon->sieve_deletescript('sieve')) {
							print "<big><b>"._("Vacation notice removed")."</b></big>";
						} else {
							print "<big><b>"._("Failure in removing vacation notice")."</b></big>";
						}
					}
				} else {
					print "<big><b>"._("Failed to login")."</b></big>";
				}
			break;

		default:
			print "<big><b>"._("Not possible")."</b></big>";
		break;
	}

	include ("browseaccounts.php");
//	        if (!DB::isError($result)){
//	                print "<h3>"._("Sucessfully changed")."</h3>";
//			include ("browseaccounts.php");
//	        }
//	        else{
//	                print "<p>"._("Database error, please try again")."<p>";
//	        }

	}



	if (!$confirmed){
//		$test = ereg ("",$alias,$result_array);

		$alias = spliti("@",$alias);
		$alias = $alias[0];

		print $result_array[0];

	        ?>

	        <form action="index.php" method="get">
	
	        <input type="hidden" name="action" value="set_vacation">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="alias" value="<?php print $alias."@".$domain ?>"> 

	        <table>

	        <tr>
	        <td><?php print _("Emailadress:") ?></td>
<!--		<td><input type="text" size="30" name=newalias value="<?php print $alias?>">@<?php print $domain?></td>	-->
		<td><?php print $alias.'@'.$domain?></td>
	        </tr>

	        <tr>
	        <td width=150><?php print _("Destination:") ?></td>
<!--	        <td><input type="text" size="30" name=dest value="<?php print $dest ?>"> </td> -->
		<td><?php print $dest?> </td>
	        </tr>

		</table>
		
		<br>
		<INPUT class="menu" TYPE="radio" NAME="mode" VALUE="set" checked>Set/install vacation notice<br>
		<INPUT class="menu" TYPE="radio" NAME="mode" VALUE="unset">Unset/remove vacation notice<br>
		<?php
		  if ($daemon->sieve_login() ){
		    $sieve_str = new sieve_strs;
		    $old_script = $sieve_str->get_old_script($daemon);
		    if (preg_match ("/(require \".*)(redirect \"|$)/Uis",$old_script,$matches)){
		      if (preg_match("/subject \".*/is",$matches[1],$matches2)){
		        $vacation_script = $matches2[0];
		      } else $vacation_script = "";
		    } else $vacation_script = "";
		  } else $vacation_script = "";
		  preg_match ("/(subject \".*)\" \"(.*$)/is", $vacation_script, $matches );
		  $vacation_script = $matches[1]."\n\n".$matches[2];
		  $vacation_script2 = preg_replace ( "/subject \"/", "Subject: ",$vacation_script );
		  $vacation_script = preg_replace ( "/\";$/", "", $vacation_script2 );
		  ?>
		  <textarea name='vacation_text' rows='6' cols='55'><?php print $vacation_script; ?></textarea><br>
		      
		      
	        <input type="submit" value="Submit"> 

		</form>


	        <?php

	}

}
else{
	print "<h3>".$err_msg."</h3>";
}

?>
</td></tr>


