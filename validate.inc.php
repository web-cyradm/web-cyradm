<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}

################# Temporary fix for PHP 4.2.0 a better solution has to found #######################
$user= $_SESSION['user'];

$_get_vars = array(
	'adminuser', 'newadminuser', 'admintype', 'newadmintype', 'newusername',
	'type', 'newtype', 'domain', 'prefix', 'action', 'row_pos', 'username',
	'password', 'new_password', 'confirm_password', 'quota', 'maxaccounts',
	'newdomain', 'email', 'alias', 'dest', 'newalias', 'newdest', 'confirmed',
	'cancel', 'searchstring', 'transport', 'tparam', 'mode', 'forwards',
	'metoo', 'vacation_text', 'freenames', 'freeaddress', 'LANG', 'delete_catchall',
	'resp_domain', 'newdomain'
);

$_post_vars = array(
	'confirmed', 'action', 'domain', 'alias' , 'username', 'new_password',
	'confirm_password', 'email', 'quota', 'password','newadminuser', 'newadmintype',
	'newdomain', 'newtype', 'resp_domain');

if ($_GET){
	if ($_GET['action']!=""){

		foreach ($_get_vars as $_get_var){
			if (isset($_GET[$_get_var])){
				$$_get_var = $_GET[$_get_var];
			}
		}
	}
}

if ($_POST){
	if ($_POST['action']!=""){
		foreach ($_post_vars as $_post_var){
			if (isset($_POST[$_post_var])){
				$$_post_var = $_POST[$_post_var];
			}
		}
	}
}

# Load list of reserved Adresses into array
$reserved=explode(",",$RESERVED);
#
$setforward = $forwardto = (isset($_POST['setforward']))?($_POST['setforward']):('');

# Connecting to database
$handle =& DB::connect($DB['DSN'],true);
if (DB::isError($handle)) {
	die (_("Database error"));
}
# This part to "THE END" was moved to init.php
/*
# Check if admin has any domain to administrate.
# Superuser has always 1 entry.
$query = "SELECT * FROM domainadmin WHERE adminuser='".$_SESSION['user']."'";
$result = $handle->query($query);
$cnt = $result->numRows();

if (!$cnt){
        print _("Security violation detected, attempt logged");
	logger(sprintf("SECURITY VIOLATION %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"),"WARN");
        include WC_BASE . "/logout.php";
        die ();
}

# We check and remember admin type (superuser or domain admin).
$query2 = "SELECT * FROM adminuser WHERE username='".$_SESSION['user']."'";
$result2 = $handle->query($query2);
$row = $result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
$_SESSION['admintype'] = $row['type'];
$admintype = $row['type'];
*/
# We check and remember list of domains for domain admin
if ($_SESSION['admintype'] != 0){	
/*	$allowed_domains = array();
	
	for ($i=0; $i < $cnt; $i++){
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $i);
		$allowed_domains[] = $row['domain_name'];
	}
	$_SESSION['allowed_domains'] = $allowed_domains;
	#Fix me: It's unnecessary (duplicated with "if (!$cnt)").
	if (sizeof($allowed_domains)==0){
		print _("Security violation detected, attempt logged");
		include WC_BASE . "/logout.php";
		die ();
	}
*/
# THE END
	// Check if username to be changed belong to the domain
//	if (!isset($domain)) $domain='';

//	$query3 = "SELECT * from domainadmin WHERE adminuser='".$_SESSION['user']."' AND domain_name='$domain'";
//	$result3 = $handle->query($query3);
//	$cnt3 = $result3->numRows();

//	if (!$cnt3 AND $domain != ""){
	if (!in_array($domain,$allowed_domains) AND $domain != "") {
		print _("Security violation detected, attempt logged");
		include WC_BASE . "/logout.php";
		die ();
	}
}


// ############################### FUNCTIONS ##################################

// function ValdateEmail V0.2 2002-04-10 22:14
function ValidateMail($email) {
//     if(!eregi("^[0-9a-z]([-_.]?[0-9a-z])*$",$email)) {
	if (!eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$email)){
		return 0;
	} else {
		return 1;
	}
}

function ValidPassword($password) {
	if (empty($password)) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function ValidDomain($domain) {
	if (!eregi("^[[:alnum:]]([.-]?[[:alnum:]])*[.][a-wyz][[:alpha:]](g|l|m|pa|t|u|v)?$",$domain)){
		return FALSE;
	} else {
		return TRUE;
	}
}

function ValidPrefix($prefix) {
	global $DOMAIN_AS_PREFIX;
	
	if ($DOMAIN_AS_PREFIX) {
		return ValidDomain($prefix);
	} else {
		if (!eregi("^[[:alnum:]_-]+$",$prefix)){
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
}
//################## Validate input and verify users actions ##################
if (! empty($action)){
	switch ($action){
############################## Check deleteaccount ##################################################
	case "deleteaccount":
		$query = "SELECT username FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result3 = $handle->query($query);
		if (!$result3->numRows()){
			$authorized = FALSE;
		} else {
			$authorized = TRUE;
		}
		break;


################################ Check input if newaccount ################################################

	case "newaccount":
		# We need to check if the requested quota is NOT higher than the defined maximum Quota
		# Superusers can override
		$query = "SELECT quota FROM domain WHERE domain_name='$domain'";
		$result=$handle->query($query);
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		# $quota2 is the allowed quota, $quota the requested quota for the account
		$quota2=$row['quota'];

		#When the requuested quota is higher that the default quota, we need to check if
		#admin NOT superuser AND when submitting the request
		if ($quota>$quota2 && $_SESSION['admintype']!=0 && $confirmed==TRUE){
			$err_msg=_("Quota exeedes the maximum allowed qutoa for this domain.");
			$authorized = FALSE;
		}
		else {
			# If the requirements are not matches, deny submission
			$authorized=TRUE;
		}
		# Check for reserved addresses
		if (in_array($email, $reserved)) {
			$authorized = FALSE;	
			$err_msg="Reserved Emailadress, request cancelled";
		}

	break;




################################ Check input if setquota ##################################################
	case "setquota":
		$query = "SELECT quota FROM domain WHERE domain_name='$domain'";
		$query2 = "SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result4 = $handle->query($query);
		$result5 = $handle->query($query2);
		$row = $result4->fetchRow(DB_FETCHMODE_ASSOC, 0);
		$quota2 = $row['quota'];
		if ($result5->numRows()){
			$authorized=TRUE;

			# If the admin is a superuser, lets change the quota anyway, regardless what the default quota is
			if (! empty($quota) && $quota > $quota2 && $_SESSION['admintype']!=0){
				$err_msg=_("Quota exeedes the maximum allowed qutoa for this domain.");
				$authorized = FALSE;
			}
		# admins not responsible for the selected domain are rejected, lets assume a break-in try
		} elseif (!$result5->numRows()){
			$err_msg=_("Security violation detected, attempt logged");
			$authorized = FALSE;
			include WC_BASE . "/logout.php";
			die ();
		}
		break;

################################## Check input if newemail ################################################
	case "newemail":
		

		$query = "SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
		$result = $handle->query($query);
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

			# Check for reserved addresses
			if (in_array($alias, $reserved)) {
				$authorized = FALSE;	
				$err_msg="Reserved Emailadress, request cancelled";
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
			# Check for reserved addresses
		if (in_array($newalias, $reserved)) {
			$authorized = FALSE;	
			$err_msg="Reserved Emailadress, request cancelled";
		}

		break;

######################################## Check new domain name ########################################

	case "newdomain":

		if (! empty($confirmed)){
			if ($DOMAIN_AS_PREFIX) {
				$prefix = $domain;
			}
			if (!ValidDomain($domain)){
				$authorized = FALSE;
				$err_msg = "You must choose a valid domainname";
			} elseif (!ValidPrefix($prefix)){
				$authorized = FALSE;
				$err_msg = "You must choose a valid prefix for your domain";
			} else {
				$query = "SELECT domain_name FROM domain WHERE domain_name='$domain' OR prefix='$prefix'";
				$result = $handle->query($query);
				if ($result->numRows()){
					$authorized = FALSE;
					$err_msg = "Domain or prefix already exists";
				} else {
					$authorized = TRUE;
				}
			}
		}
		break;
####################################### Check input if editdomain ####################################
	case "editdomain":
		if (!empty($confirmed)){
			$query = "SELECT domain_name FROM domain WHERE domain_name='$newdomain' AND domain_name!='$domain' OR prefix='$_GET[newprefix]' AND prefix!='$prefix'";
			$result = $handle->query($query);
			if ($result->numRows()){
				$authorized = FALSE;
				$err_msg = "Domain or prefix already exists";
			} else {
				$authorized = TRUE;
			}
		}
		break;
##################################### Check input if changeadminpasswd ###############################
	case "changeadminpasswd":
		if (isset($_POST['confirmed'])){
			if (!ValidPassword($_POST['old_password']) || !ValidPassword($_POST['new_password']) || !ValidPassword($_POST['confirm_password'])) {
				$authorized = FALSE;
				$err_msg = "Password incorrect";
			} elseif ($_POST['new_password'] != $_POST['confirm_password']){
				$authorized = FALSE;
				$err_msg = _("New passwords are not equal. Password not changed");
			} else {
				$pwd=new password;
				$result=$pwd->check("adminuser",$_SESSION['user'],$_POST['old_password'],$CRYPT);
				if ($result) {
					$authorized = TRUE;
				} else {
					$authorized = FALSE;
					$err_msg = "Password incorrect";
				}
			}
		}
		break;
	case "display":
		$authorized = TRUE;
		break;
######################################## Check on catch all setting ##################################
	case "catch":
	case "delete_catchall";
	case "aliases":
	case "newalias":
########################################## Check input if editalias ##################################
	case "editalias":
		# Check for reserved addresses
		if (in_array($alias, $reserved)) {
			$authorized = FALSE;
			$err_msg="Reserved Emailadress, request cancelled";
		}
		break;		
		
	case "deletealias":
	case "forwardalias":
	case "deleteemail":
	case "vacation":

	$query3 = "SELECT domain_name FROM accountuser WHERE username='$username' AND domain_name='$domain'";
	$result3 = $handle->query($query3);
	$cnt3=$result3->numRows();

	if (!$cnt3 and $username !=""){
		print _("Security violation detected, attempt logged");
		include WC_BASE . "/logout.php";
		die ();
	}


######################################### If nothing matches ##########################################

	default:
		break;
	} // End of switch ($action)
} // End of if (! empty($action))

