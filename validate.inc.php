<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}

################# Temporary fix for PHP 4.2.0 a better solution has to found #######################
$_get_vars = array(
	'adminuser', 'newadminuser', 'newadmintype', 'newusername',
	'type', 'newtype', 'domain', 'prefix', 'action', 'row_pos', 'username',
	'password', 'new_password', 'confirm_password', 'quota', 'maxaccounts',
	'newdomain', 'email', 'alias', 'dest', 'newalias', 'newdest', 'confirmed',
	'cancel', 'searchstring', 'transport', 'tparam', 'mode', 'forwards',
	'metoo', 'vacation_text', 'freenames', 'freeaddress', 'LANG', 'delete_catchall',
	'resp_domain', 'newdomain','orderby'
);

$_post_vars = array('action','domain');

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

function ValidMail($email) {
	if (!eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$email)){
		return FALSE;
	} else {
		return TRUE;
	}
}

function ValidName($username) {
	if (empty($username)) {
		return FALSE;
	} else {
		return TRUE;
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
//		if (!eregi("^[[:alnum:]_-]+$",$prefix)){
		if (empty($prefix)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

function ValidAdminType($type) {
	if ($type == 0 || $type == 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}

# Security precaution if register_globals = on
$authorized = FALSE;
# Load list of reserved Adresses into array
$reserved=explode(",",$RESERVED);
#
$setforward = $forwardto = (isset($_POST['setforward']))?($_POST['setforward']):('');

# Connecting to database
$handle =& DB::connect($DB['DSN'],true);
if (DB::isError($handle)) {
	die (_("Database error"));
}

# We check and remember list of domains for domain admin
$query = "SELECT * FROM domainadmin WHERE adminuser='".$_SESSION['user']."'";
$result = $handle->query($query);
if (DB::isError($result)) {
	die (_("Database error"));
}
$cnt = $result->numRows();

if (!$cnt){
	print _("Security violation detected, attempt logged");
	logger(sprintf("SECURITY VIOLATION %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"),"WARN");
	include WC_BASE . "/logout.php";
	die ();
}

if ($_SESSION['admintype'] != 0){
	$allowed_domains = array();
	
	for ($i=0; $i < $cnt; $i++){
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $i);
		$allowed_domains[] = $row['domain_name'];
	}
	$_SESSION['allowed_domains'] = $allowed_domains;
	if (!isset($domain)) $domain='';

	if (!empty($domain) AND !in_array($domain,$_SESSION['allowed_domains'])) {
		print _("Security violation detected, attempt logged");
		logger(sprintf("SECURITY VIOLATION %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"),"WARN");
		include WC_BASE . "/logout.php";
		die ();
	}
}
//################## Validate input and verify users actions ##################
if (! empty($action)){
	switch ($action){
#OK############################# Check input if browse ################################################
	case "browse":
		if (isset($_GET['orderby']) AND
			!in_array($_GET['orderby'], array('domain_name', 'prefix', 'maxaccounts',
							  'domainquota', 'quota'))){
			unset($_GET['orderby']);
		}
		if (isset($_GET['row_pos']) AND !is_numeric($_GET['row_pos'])) {
			unset($_GET['row_pos']);
		}
		break;
#OK############################# Check input if accounts ##############################################
	case "accounts":
		if (isset($_GET['domain']) AND !ValidDomain($_GET['domain'])) {
			unset($_GET['domain']);
		}
		if (isset($_GET['row_pos']) AND !is_numeric($_GET['row_pos'])) {
			unset($_GET['row_pos']);
		}
		break;
#OK########################### Check input if newadminuser ###############################################
	case "newadminuser":
		if ($_SESSION['admintype'] != 0) {
			$authorized = FALSE;
			$err_msg = _("Security violation detected, nothing deleted, attempt has been logged");
		} elseif (!isset($_POST['confirmed'])) {
			if (!empty($_GET['domain']) && !ValidDomain($_GET['domain'])) {
				$authorized = FALSE;
				$err_msg = "";
			} else {
				$authorized = TRUE;
			}
		} else {
			if (!empty($_POST['domain']) && !ValidDomain($_POST['domain']) || !ValidAdminType($_POST['newadmintype'])) {
				$authorized = FALSE;
				$err_msg = "";
			} elseif (!ValidName($_POST['newadminuser'])) {
				$authorized = FALSE;
				$err_msg = _("You must provide a username");
			} elseif (!ValidPassword($_POST['new_password']) || !ValidPassword($_POST['confirm_password'])){
				$authorized = FALSE;
				$err_msg = _("Password incorrect");
			} elseif ($_POST['new_password'] != $_POST['confirm_password']) {
				$authorized = FALSE;
				$err_msg = _("New passwords are not equal. Password not changed");
			} else {
				# Check if admin already exists
				$query = "SELECT * FROM adminuser WHERE username='".$_POST['newadminuser']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				if ($result->numRows()){
					$authorized = FALSE;
					$err_msg = _("Username already exist");
				# If domain is not set or admin will be superuser: that's all
				} elseif (empty($_POST['newdomain']) || $_POST['newadmintype'] == 0) {
					$authorized = TRUE;
				} elseif (!ValidDomain($_POST['newdomain'])) {
					$authorized = FALSE;
					$err_msg = _("Invalid domain name");
				} else {
					# Check if domain already exists
					$query="SELECT domain_name FROM domain WHERE domain_name='".$_POST['newdomain']."'";
					$result= $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
					if (!$result->numRows()){
						$authorized = FALSE;
						$err_msg = _("No such domain");
					} else {
						$authorized = TRUE;
					}
				}
			}
		}
		break;
#Almost OK################### Check input if editadminuser ###############################################
	case "editadminuser":
		if ($_SESSION['admintype'] != 0) {
			$authorized = FALSE;
			$err_msg = _("Security violation detected, nothing deleted, attempt has been logged");
		} elseif (!isset($_POST['confirmed'])) {
			if (!empty($_GET['domain']) && !ValidDomain($_GET['domain'])) {
				$authorized = FALSE;
				$err_msg = "";
			} elseif (!ValidName($_GET['username'])) {
				$authorized = FALSE;
				$err_msg = _("You must provide a username");
			} else {
				$authorized = TRUE;
			}
		} else {
			if (!empty($_POST['domain']) && !ValidDomain($_POST['domain']) || !ValidAdminType($_POST['newtype'])) {
				$authorized = FALSE;
				$err_msg = "";
			} elseif (!ValidName($_POST['username'])) {
				$authorized = FALSE;
				$err_msg = _("You must provide a username");
			} elseif (!empty($_POST['new_password']) && (!ValidPassword($_POST['new_password']) || !ValidPassword($_POST['confirm_password']))){
				$authorized = FALSE;
				$err_msg = _("Password incorrect");
			} elseif ($_POST['new_password'] != $_POST['confirm_password']) {
				$authorized = FALSE;
				$err_msg = _("New passwords are not equal. Password not changed");
			} else {
				$query = "SELECT `type` from adminuser WHERE username='".$_POST['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$type = $row['type'];
				
				if ($type != $_POST['newtype']) {
					if ($_POST['newtype'] == 1) {
						# We have to take care to have at least one superuser left, or we cannot use
						# Web-cyradm again

						# Query to get the count of superusers
						$query = "SELECT `type` FROM adminuser WHERE type='0'";
						$result = $handle->query($query);
						if (DB::isError($result)) {
							die (_("Database error"));
						}
						$cnt = $result->numRows($result);

						# Check if only 1 superuser is defined, in case of requested change of a superuser
						if ($cnt==1 && $type==0){
							# No Way! We cannot change the last Superuser to domainadmin!
							$authorized = FALSE;
							$err_msg = _("At least one Superuser is needed for Web-cyradm");
						}
					}
				}
# TO DO: Checks for array of domains $_POST['resp_domain']
				//if (!empty($_POST['resp_domain'])) {
				//}
				# If domain is not set: that's all
				if (empty($_POST['newdomain']) || $_POST['newtype'] == 0) {
					$authorized = TRUE;
				} elseif (!ValidDomain($_POST['newdomain'])) {
					$authorized = FALSE;
					$err_msg = _("Invalid domain name");
				} else {
					# Check if domain already exists
					$query = "SELECT domain_name FROM domain WHERE domain_name='".$_POST['newdomain']."'";
					$result = $handle->query($query);
					if (DB::isError($result)) {
						die (_("Database error"));
					}
					if (!$result->numRows()){
						$authorized = FALSE;
						$err_msg = _("No such domain");
					} else {
						$query = "SELECT * FROM domainadmin WHERE adminuser='".$_POST['username']."' AND domain_name='".$_POST['newdomain']."'";
						$result = $handle->query($query);
						if (DB::isError($result)) {
							die (_("Database error"));
						}
						if ($result->numRows()) {
							$err_msg = _("Admin already repsonsible for the domain")." ".$_POST['newdomain'];
						} else {
							$authorized = TRUE;
						}
					}
				}
			}
		}
		break;
#OK############################# Check input if newaccount ################################################
	case "newaccount":
		if (!isset($_POST['confirmed'])) {
			if (!empty($_GET['domain']) && !ValidDomain($_GET['domain'])) {
				$authorized = FALSE;
				$err_msg = "";
			} else {
				$authorized = TRUE;
			}
		} else {
			if (!ValidDomain($_POST['domain']) || !is_numeric($_POST['quota'])) {
				$authorized = FALSE;
				$err_msg = "";
			} elseif (!ValidPassword($_POST['password']) || !ValidPassword($_POST['confirm_password'])){
				$authorized = FALSE;
				$err_msg = _("Password incorrect");
			} elseif ($_POST['password'] != $_POST['confirm_password']) {
				$authorized = FALSE;
				$err_msg = _("Passwords do not match");
			} elseif (!ValidMail($_POST['email']."@".$_POST['domain'])) {
				$authorized = FALSE;
				$err_msg = _("Invalid email adress");
			} elseif (!empty($_POST['username']) && !ValidName($_POST['username'])) {
				$authorized = FALSE;
				$err_msg = _("Invalid username");
			# Check for reserved addresses
			} elseif (in_array($_POST['email'], $reserved)) {
				$authorized = FALSE;
				$err_msg="Reserved Emailadress, request cancelled";
			} else {
				$authorized = TRUE;
				
				if ($DOMAIN_AS_PREFIX){
					$_POST['username'] = $_POST['email'];
				}
				// check to see if there's an account with the same username
				$query = "SELECT * FROM accountuser WHERE username='".$_POST['username']."'";
				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error"));
				}
				$cnt = $result->numRows();
				if ($cnt != 0) {
					$authorized = FALSE;
					$err_msg = _("Sorry, the username already exists");
				}
				
				# We need to check if the requested quota is NOT higher than the defined maximum Quota
				# Superusers can override
				$query = "SELECT quota FROM domain WHERE domain_name='".$_POST['domain']."'";
				$result = $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				# $quota2 is the allowed quota, $quota the requested quota for the account
				$quota2=$row['quota'];

				#When the requuested quota is higher that the default quota, we need to check if
				#admin NOT superuser AND when submitting the request
				if ($_POST['quota'] > $row['quota'] && $_SESSION['admintype'] != 0) {
					$authorized = FALSE;
					$err_msg = _("Quota exeedes the maximum allowed quota for this domain.");
				}
			}
		}
		break;
#OK########################### Check input if deleteaccount ###############################################
	case "deleteaccount":
		if (!ValidDomain($_GET['domain'])) {
			$authorized = FALSE;
			$err_msg = "";
		} elseif (!ValidName($_GET['username'])) {
			$authorized = FALSE;
			logger(sprintf("SECURITY VIOLATION %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"),"WARN");
			$err_msg = _("Security violation detected, action cancelled. Your attempt has been logged.");
		} else {
			# it's needed to defend 'cyrus' user
			$query = "SELECT username FROM accountuser WHERE username='".$_GET['username']."' AND domain_name='".$_GET['domain']."'";
			$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			if (!$result->numRows()){
				$authorized = FALSE;
				logger(sprintf("SECURITY VIOLATION %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"),"WARN");
				$err_msg = _("Security violation detected, action cancelled. Your attempt has been logged.");
			} else {
				$authorized = TRUE;
			}
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
				$err_msg = _("Invalid email adress");
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
#OK############################ Check input if change_password ###############################
	case "change_password":
		if (!isset($_POST['confirmed'])) {
			if (!ValidDomain($_GET['domain']) || !ValidName($_GET['username'])) {
				$authorized = FALSE;
				$err_msg = "";
			} else {
				$authorized = TRUE;
			}
		} else {
			if (!ValidDomain($_POST['domain']) || !ValidName($_POST['username'])) {
				$authorized = FALSE;
				$err_msg = "";
			} elseif (!ValidPassword($_POST['new_password']) || !ValidPassword($_POST['confirm_password'])){
				$authorized = FALSE;
				$err_msg = _("Password incorrect");
			} elseif ($_POST['new_password'] != $_POST['confirm_password']) {
				$authorized = FALSE;
				$err_msg = _("New passwords are not equal. Password not changed");
			} else {
				$authorized = TRUE;
			}
		}
		break;
###########################  Check if change email-adress ####################################
	## FIXME: make beter checks
	case "editemail":

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
				if (DB::isError($result)) {
					die (_("Database error"));
				}
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
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			if ($result->numRows()){
				$authorized = FALSE;
				$err_msg = "Domain or prefix already exists";
			} else {
				$authorized = TRUE;
			}
		}
		break;
#OK################################## Check input if changeadminpasswd ###############################
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
#OK####################################### Check input if display ##################################
	case "display":
		if (isset($_GET['confirmed'])) {
			if (!is_numeric($_GET['maxdisplay']) OR $_GET['maxdisplay'] <= 0) {
				$authorized = FALSE;
				$err_msg = "Value incorrect";
			}
			elseif (!is_numeric($_GET['account_maxdisplay']) OR $_GET['account_maxdisplay'] <= 0) {
				$authorized = FALSE;
				$err_msg = "Value incorrect";
			}
			elseif (!is_numeric($_GET['warnlevel']) OR $_GET['warnlevel'] < 0 OR $_GET['warnlevel'] > 100) {
				$authorized = FALSE;
				$err_msg = "Warn level should be beetwen 0 and 100";
			}
			elseif (!isset($_GET['style']) OR empty($_GET['style']) OR !in_array($_GET['style'], $TEMPLATE)){
				$authorized = FALSE;
				$err_msg = "Value incorrect";
			}
			else {
				$authorized = TRUE;
			}
		}
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
##########################################
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
		break;
######################################### If nothing matches ##########################################
	default:
		break;
	} // End of switch ($action)
} // End of if (! empty($action))
?>

