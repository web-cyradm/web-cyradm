<?php

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
        return ((float)$usec + (float)$sec); 
} 

if (file_exists("./migrate.php")){
	die(_("migrate.php exists! please delete or rename it"));
}

include ("config.inc.php");
include ("lib/nls.php");
include ("lib/crypto.php");

$browserlang=explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']); // $HTTP_ACCEPT_LANGUAGE;

$browserlang1=substr($browserlang[0], 0, 2);

//if ($nls['aliases'][$browserlang[0]]){
if ($nls['aliases'][$browserlang1]){
	$LANG=$nls['aliases'][$browserlang1];
}

require_once ("session.php");
$session_ok = $_SESSION['session_ok'];

// Lowest prio langauge is the session setting
if ($_SESSION['LANG']){
	$LANG=$_SESSION['LANG'];
}

// if no langauge is already set, use defaultlanguage
if ($LANG==""){
	$LANG=$DEFAULTLANG;
}

// For testing porpose, http variable LANG overrides all
if ($_GET['LANG']){
	$LANG=$_GET['LANG'];
}

include ("header.inc.php");

setlocale(LC_MESSAGES, $LANG);
putenv("LANG=" . $LANG);
putenv("LANGUAGE=" . $LANG);

setlocale(LC_ALL, $LANG);

// Specify location of translation tables
bindtextdomain("web-cyradm", "./locale");

// Choose domain
textdomain("web-cyradm");

if ($_SESSION['session_ok'] === TRUE) {
	include ("DB.php");
	// include ("session.php");
	include ("validate.inc.php");
	include ("menu.inc.php");
	include ("lib/cyradm.php");

	if (!$_GET['domain'] and ! in_array($_GET['action'], array('logout', 'adminuser', 'newdomain', 'editadminuser'))){
		include ("welcome.php");
	} else {
		if (in_array($_GET['action'], array('logout', 'browse', 'editdomain', 
		                                    'newdomain', "deletedomain",
						    "adminuser", "newadminuser",
						    "editadminuser", "deleteadminuser",
						    "editaccount", "newaccount",
						    "deleteaccount", "setquota",
						    "change_password", "vacation",
						    "forwardalias", "forwardaccount",
						    "newemail", "deleteemail",
						    "editemail", "aliases", "newalias",
						    "editalias", "deletealias"))){
			include(sprintf('%s.php', $_GET['action']));
		} else {
			switch ($_GET['action']){
				case "accounts":
					include ("browseaccounts.php");
					break;

				case "catch":
					include ("catchall.php");
					break;

				default:
					include ("browse.php");
					break;
			}
		}
	}
	include ("footer.inc.php");
} else {
	include ("login.php");
}
