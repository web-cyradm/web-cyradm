<?php
session_start();
$session_ok= $HTTP_SESSION_VARS['session_ok'];
if ($session_ok) {
include ("config.inc.php");
include ("DB.php");
include ("header.inc.php");
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
