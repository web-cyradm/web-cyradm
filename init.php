<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}

#### Getting admin settings
$query = "SELECT * from settings WHERE username='".$_SESSION['user']."'";
$result = $handle->query($query);
$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
$_SESSION['style'] = $row['style'];
$_SESSION['maxdisplay'] = $row['maxdisplay'];

if (!$_SESSION['maxdisplay']){
	$_SESSION['maxdisplay'] = 15;
}

##### Getting admin privilages
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

# We check and remember list of domains for domain admin
if ($admintype != 0){
	$allowed_domains = array();
	
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
}
unset($_SESSION['init']);
?>
