<?php

include ("config.inc.php");
include ("lib/nls.php");

$browserlang=explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']); // $HTTP_ACCEPT_LANGUAGE;

$browserlang1=substr($browserlang[0], 0, 2);


//if ($nls['aliases'][$browserlang[0]]){
if ($nls['aliases'][$browserlang1]){
	$LANG=$nls['aliases'][$browserlang1];
}

session_start();
$session_ok= $HTTP_SESSION_VARS['session_ok'];

// Lowest prio langauge is the session setting

if ($HTTP_SESSION_VARS['LANG']){
	$LANG=$HTTP_SESSION_VARS['LANG'];
}

// if no langauge is already set, use defaultlanguage

if ($LANG==""){
	$LANG=$DEFAULTLANG;
}

// For testing porpose, http variable LANG overrides all

if ($HTTP_GET_VARS['LANG']){
	$LANG=$HTTP_GET_VARS['LANG'];
}

include ("header.inc.php");

setlocale(LC_MESSAGES, "$LANG");
putenv("LANG=$LANG");
putenv("LANGUAGE=$LANG");

setlocale(LC_ALL, $LANG);

// Specify location of translation tables
bindtextdomain("web-cyradm", "./locale");

// Choose domain
textdomain("web-cyradm");


if ($session_ok) {
include ("DB.php");
include ("session.php");
include ("validate.inc.php");
include ("menu.inc.php");
include ("lib/cyradm.php");


	if (!$domain and $action !="logout" and $action !="adminuser" and $action !="newdomain"){
	
		include ("welcome.php");
	
	}

	else {


		switch ($action){
			case "logout":
			include ("logout.php");
			break;

			case "browse":
			include ("browse.php");
		        break;

			case "editdomain":
			include ("editdomain.php");
		        break;

			case "newdomain":
			include ("newdomain.php");
		        break;

			case "deletedomain":
			include ("deletedomain.php");
		        break;

			case "adminuser":
			include ("adminuser.php");
		        break;

			case "newadminuser":
			include ("newadminuser.php");
		        break;

			case "editadminuser":
			include ("editadminuser.php");
		        break;

			case "deleteadminuser":
			include ("deleteadminuser.php");
		        break;

			case "accounts":
			include ("browseaccounts.php");
		        break;

			case "newaccount":
			include ("newaccount.php");
		        break;

			case "catch":
			include ("catchall.php");
		        break;

			case "deleteaccount":
			include ("deleteaccount.php");
		        break;

		        default:
	        	include ("browse.php");
		        break;
	
		        case "setquota":
	        	include ("setquota.php");
		        break;
	
		        case"editaccount":
	        	include ("editaccount.php");
		        break;

		        case"newemail":
	        	include ("newemail.php");
		        break;

		        case"deleteemail":
	        	include ("deleteemail.php");
		        break;

		        case"editemail":
	        	include ("editemail.php");
		        break;

		}


	}

include ("footer.inc.php");
} 
else {

include ("login.php");

}


?>
