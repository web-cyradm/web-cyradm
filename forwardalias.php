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
//	$daemon = new sieve("localhost","2000",$dest,$password,"");
	$daemon = new sieve("localhost","2000", $username, $CYRUS_PASSWORD, $CYRUS_USERNAME);

	if ($confirmed){

//	        $query="UPDATE virtual SET alias='$newalias@$domain', dest='$dest' WHERE alias='$alias'";
	  switch ($mode) {
	  case 'set':
	  if ($daemon->sieve_login()) {
	    $sieve_str = new sieve_strs;
	    $forwards_script ='';
	    $forwardwhere = $forwards;
	    while ( preg_match ("/(.*),(.*$)/U",$forwardwhere, $matches)){
	      $forwards_script .= 'redirect "'.ltrim($matches[1]).'";'."\n";
	      $forwardwhere = $matches[2];
	    }
	    $forwards_script .= 'redirect "'.$forwardwhere.'";';
	    if ($metoo == 'on') {
	      $forwards_script .= "keep;";
	    }
	    $forwards_script .= "\n";
	    $old_script = $sieve_str->get_old_script($daemon);
	    // vacation is the first rule.
	    if (preg_match ("/(require.*)(redirect \"|$)/Uis", $old_script, $matches)){
	      $vacation_script = $matches[1];
	    } else $vacation_script = '';
	    $script = $vacation_script.$forwards_script;
	    if ($daemon->sieve_sendscript('sieve', $script) &&
	      $daemon->sieve_setactivescript('sieve')) {
	      print "<big><b>"._("Forward set")."</b></big>";
	    } else print "<big><b>"._("Failure in setting forward")."</b></big>";
	  }else print "<big><b>"._("Wrong password")."</b></big>";
	  break;

	  case 'unset':
	  if ($daemon->sieve_login() ){
	    $sieve_str = new sieve_strs;
	    $old_script = $sieve_str->get_old_script($daemon);
	    if (preg_match ("/(require \".*)(redirect \"|$)/Uis",$old_script,$matches)){
	      $vacation_script = $matches[1];
	      if ($daemon->sieve_sendscript('sieve', $vacation_script) ) {
	        print "<big><b>"._("Forwarding removed")."</b></big>";
	      }else print "<big><b>"._("Failure in removing forwarding")."</b></big>";
	    }else {
	      if ($daemon->sieve_deletescript('sieve')) {
	        print "<big><b>"._("Forwarding removed")."</b></big>";
	      }else print "<big><b>"._("Failure in removing forwarding")."</b></big>";
	    }
	  }else print "<big><b>"._("Failed to login")."</b></big>";
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

		<h3><?php print _("Forward for emailadress")." <font color=\"red\">".$alias."@".$domain;?></font></h3>

	        <form action="index.php" method="get">
	
	        <input type="hidden" name="action" value="forwardalias">
	        <input type="hidden" name="confirmed" value="true">
	        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
	        <input type="hidden" name="alias" value="<?php print $alias."@".$domain ?>"> 

		<?php
		  if ($daemon->sieve_login() ){
		    $sieve_str = new sieve_strs;
		    $old_script = $sieve_str->get_old_script($daemon);
		    if ( preg_match ("/redirect \".*$/siU", $old_script, $matches)){
		      $forwards_script = $matches[0];
		      $forwards_text = '';
		      while ( preg_match ("/(redirect \")(.*)(\";)(.*$)/siU", $forwards_script, $matches )){
		        $forwards_text .= $matches[2].','; 
		        $forwards_script = $matches[4];
		      }
		      $forwards_text = rtrim ($forwards_text, ',');
		      if (preg_match ("/keep;/i", $forwards_script, $matches)){
		        $keep = 'checked';
		      } else $keep = '';
		    } else {
		      $forwards_text ='';
		      $keep = '';
		    }
		  } else $forwards_text = ''; 

		  ?>

		<br>
		<INPUT TYPE="radio" NAME="mode" VALUE="set" checked><?php print _("Set forwarding to") ?>:
		<INPUT class="inputfield" type='text' name='forwards' value='<?php print $forwards_text ?>' size='50' ><br>
		<input type='checkbox' name='metoo' <?php print $keep."> "._("Keep a copy in the user's mailbox") ?><br><br>
		<INPUT TYPE="radio" NAME="mode" VALUE="unset"><?php print _("Remove forwarding") ?><br><br><br>
		      
	        <input class="button" type="submit" value="<?php print _("Submit")?>"> 

		</form>


	        <?php

	}

}
else{
	print "<h3>".$err_msg."</h3>";
}

?>
</td></tr>


