<?php

// Specify location of translation tables
//bindtextdomain("web-cyradm", "./locale");

// Choose domain
//textdomain("web-cyradm");


################# Temporary fix for PHP 4.2.0 a better solution has to found #######################
$user= $_SESSION['user'];

$_get_vars = array(
	'adminuser', 'newadminuser', 'admintype', 'newadmintype', 'newusername',
	'type', 'newtype', 'domain', 'prefix', 'action', 'row_pos', 'username',
	'password', 'new_password', 'confirm_password', 'quota', 'maxaccounts',
	'newdomain', 'email', 'alias', 'dest', 'newalias', 'newdest', 'confirmed',
	'cancel', 'searchstring', 'transport', 'tparam', 'mode', 'forwards',
	'metoo', 'vacation_text', 'freenames', 'freeaddress', 'LANG'
);

foreach ($_get_vars as $_get_var){
	if (isset($_GET[$_get_var])){
		$$_get_var = $_GET[$_get_var];
	}
}

$setforward = $forwardto = (isset($_POST['setforward']))?($_POST['setforward']):('');

# Validate input and verify authorization of a users action

$query = "SELECT * FROM domainadmin WHERE adminuser='$user'";
$query2 = "SELECT * FROM adminuser WHERE username='$user'";
$handle = DB::connect($DB['DSN'],true);
if (DB::isError($handle)) {
	die (_("Database error"));
}

$result = $handle->query($query);
$result2 = $handle->query($query2);
$cnt = $result->numRows();
$row = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
$admintype = $row['type'];
if ($admintype != 0){
	if (!isset($domain)) $domain='';
//	$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
	for ($i=0; $i < $cnt; $i++){
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $i);
		$allowed_domains=$row['domain_name'];
	}
	if (!$allowed_domains){
		print _("Security violation detected, attempt logged");
		include WC_BASE . "/logout.php";
		die ();
	}
//	$domain=$row['domain_name'];

	$query3 = "SELECT * from domainadmin WHERE adminuser='$user' AND domain_name='$domain'";
	$result3 = $handle->query($query3);
	$cnt3 = $result3->numRows();
	
	if (!$cnt3 AND $domain != ""){
		print _("Security violation detected, attempt logged");
		include WC_BASE . "/logout.php";
		die ();
	}
	
	//print $domain;
}


// Functions

// function ValdateEmail V0.2 2002-04-10 22:14
function ValidateMail($email) {
//     if(!eregi("^[0-9a-z]([-_.]?[0-9a-z])*$",$email)) {
	if (!eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$email)){
		return 0;
	} else {
		return 1;
	}
}

if (! empty($action)){
############################## Check deleteaccount ##################################################
	switch ($action){
	case "deleteaccount":
		$query = "SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result3 = $handle->query($query);
		if (!$result3->numRows()){
			$authorized = FALSE;
		} else {
			$authorized = TRUE;
		}
		break;
################################ Check input if setquota ##################################################
	case "setquota":
		$query = "SELECT quota FROM domain WHERE domain_name='$domain'";
		$query2 = "SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result4 = $handle->query($query, $handle);
		$result5 = $handle->query($query2, $handle);
		$row = $result4->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$quota2 = $row['quota'];
		if ($result5->numRows()){
			$authorized=TRUE;
			if (! empty($quota) && $quota > $quota2){
				$err_msg=_("Quota exeedes the maximum allowed qutoa for this domain.");
				$authorized = FALSE;
			}
		} elseif (!$result5->numRows()){
			$err_msg=_("Security violation detected, attempt logged");
			$authorized = FALSE;
		}
		break;

################################## Check input if newemail ################################################
	case "newemail":
		$query = "SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result = $handle->query($query, $handle);
		$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$username2 = $row['username'];

                $query2 = "select * from domain where domain_name='$domain'";
		$result2 = $handle->query($query2);
		$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$freeaddress=$row2['freeaddress'];
		
		if (! empty($confirmed)){
			$valid_dest  = eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,}(g|l|m|pa|t|u|v)?$", $dest);
			if ($freeaddress != "YES") {
			    $valid_alias = eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,}(g|l|m|pa|t|u|v)?$", $alias."@".$domain);
			} else {
			    $valid_alias = eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,}(g|l|m|pa|t|u|v)?$", $alias);
			}
			if ($dest != $username2 and !$valid_dest){
//			if ($dest != $username2 and !ValidateMail($dest))
				$authorized = FALSE;
				$err_msg = "invalid destination";
			} elseif (!$valid_alias and isset($alias)){
//	        	elseif (!ValidateMail($alias."@".$domain) and isset($alias))
				$authorized = FALSE;
				$err_msg = "Invalid email adress";
			} else {
				$authorized=TRUE;
			}
		} else {
			$authorized = TRUE;
		}
		break;

#####################  Check if change email-adress ####################################

	case "editemail":

	## FIXME: make beter checks 
	case "change_password":
	case "vacation":
	case "forwardaccount":
	case "forwardalias":

		$query = "SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result = $handle->query($query);
		
                $query2 = "select * from domain where domain_name='$domain'";
		$result2 = $handle->query($query2);
		$row2 = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$freeaddress=$row2['freeaddress'];
		
		if (! empty($confirmed) && ! empty($newdest) && ! empty($newalias)){
			$valid_dest = eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$", $newdest);
			if ($freeaddress != "YES") {
			    $valid_alias = eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$", $newalias."@".$domain);
			} else {
			    $valid_alias = eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$", $newalias);
			}			
			if ($newdest != $username2 and !$valid_dest){
				$authorized=FALSE;
				$err_msg = "invalid destination";
			} elseif (!$valid_alias and isset($newalias)){
				$authorized = FALSE;
				$err_msg = "Invalid email adress";
			} else {
				$authorized = TRUE;
			}
		} else {
			$authorized=TRUE;
		}
		break;

######################################## Check new domain name ########################################

	case "newdomain":

		if (! empty($confirmed)){
			if (!$domain){
				$authorized = FALSE;
				$err_msg = "You must choose a valid domainname";
			} elseif (!$prefix){
				$authorized = FALSE;
				$err_msg = "You must choose a valid prefix for your domain";
			} else {
				$authorized=TRUE;
			}
		}
		break;

######################################### If nothing matches ##########################################

	default:
		break;
	} // End of switch ($action)
} // End of if (! empty($action))

