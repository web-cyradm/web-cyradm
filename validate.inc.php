<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
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

	// Check if username to be changed belong to the domain


	$query3 = "SELECT * from domainadmin WHERE adminuser='$user' AND domain_name='$domain'";
	$result3 = $handle->query($query3);
	$cnt3 = $result3->numRows();

	if (!$cnt3 AND $domain != ""){
		print _("Security violation detected, attempt logged");
		include WC_BASE . "/logout.php";
		die ();
	}

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
		if ($quota>$quota2 && $admintype!=0 && $confirmed==TRUE){
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
			if (! empty($quota) && $quota > $quota2 && $admintype!=0){
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

######################################## Check on catch all setting ##################################
	case "catch":
	case "delete_catchall";
	case "aliases":
	case "newalias":
	case "editalias":
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

