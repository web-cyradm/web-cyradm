<?php

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

function get_var_dump(&$var, $name = NULL){
	ob_start();
	echo '<pre class="varDump" style="color: black; background-color: silver; font-family: monospace; border: 2px dotted red; display: block;">';
	if ($name !== NULL){
		echo $name . ":\n";
	}
	var_dump($var);
	echo '</pre>';
	$ret = ob_get_contents();
	ob_end_clean();

	return $ret;
}

if (file_exists("./migrate.php")){
	die(_("migrate.php exists! please delete or rename it"));
}

define('WC_BASE', dirname(__FILE__));

$wc_configured = @file_exists(WC_BASE . '/config/conf.php');

if ($wc_configured){
	#include "DB.php";
	require "DB.php";
	include WC_BASE . "/config/conf.php";
	include WC_BASE . "/lib/nls.php";
	include WC_BASE . "/lib/crypto.php";
	include WC_BASE . "/lib/log.php";

	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		$browserlang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	} else {
		$browserlang = 'en_EN';
	}
	require_once WC_BASE . "/session.php";

	// 1nd) If there is a language setting in the session, use this instead
	// 2st) Try to get the language from the browser
	// 3rd) If none of the above is true, use the default language
	if (isset($_SESSION['LANG'])){
		// Use Language setting from session
		$LANG = $_SESSION['LANG'];
	}
	elseif (isset($nls['aliases'][substr($browserlang[0], 0, 2)])){
                // Get language from the browser
              $LANG = $nls['aliases'][substr($browserlang[0], 0, 2)];

	} else {
		// Fall back to default language
		//$LANG = $DEFAULTLANG;
		$LANG = $nls['aliases'][substr($DEFAULTLANG,0,2)];
	}

	// For testing porpose, GET variable LANG overrides all
//	$LANG = (! empty($_GET['LANG']))?($_GET['LANG']):($LANG);

	if (isset($_GET['LANG'])){
		$LANG=$_GET['LANG'];
	}
	
	if (isset($_SESSION['init'])) {
		include WC_BASE . "/init.php";
	}
	include WC_BASE . "/header.inc.php";

	setlocale(LC_MESSAGES, $LANG);
	setlocale(LC_ALL, $LANG);
	putenv("LANG=" . $LANG);
	putenv("LANGUAGE=" . $LANG);

	// Specify location of translation tables
	bindtextdomain("web-cyradm", "./locale");

	// Choose domain
	textdomain("web-cyradm");

	if ($_SESSION['session_ok'] === TRUE) {
		require WC_BASE . "/validate.inc.php";
		require WC_BASE . "/menu.inc.php";
		require WC_BASE . "/lib/cyradm.php";

//		if (empty($_GET['domain']) && (empty($_GET['action']) || (! in_array($_GET['action'], array('logout', 'adminuser', 'newdomain', 'editadminuser', 'newadminuser', 'search'))))){

		if (!isset($action) OR
		    empty($domain) AND !in_array($action, array('logout', 'adminuser', 'editadminuser',
		   						'deleteadminuser', 'newadminuser',
								'search', 'settings', 'changeadminpasswd',
								'display','browse'))){
			include WC_BASE . "/welcome.php";
		} else {

			# Only allow defined actions and include them

			if (isset($_GET['action']) AND
			    in_array($_GET['action'], array('logout', 'browse', 'editdomain',
							    'newdomain', "deletedomain",
							    "adminuser", "newadminuser",
							    "editadminuser", "deleteadminuser",
							    "editaccount", "newaccount",
							    "deleteaccount", "setquota",
							    "change_password", "vacation",
							    "forwardalias", "forwardaccount",
							    "newemail", "deleteemail",
							    "editemail", "aliases", "newalias",
							    "editalias", "deletealias", "search",
							    "delete_catchall", "settings",
							    "changeadminpasswd", "display", "editservices"))){
				include sprintf('%s/%s.php', WC_BASE, $_GET['action']);
			}

			# For password related stuff we also need to allow POST vars for some actions

			else if (isset($_POST['action']) AND
				 in_array($_POST['action'], array('change_password', 'newaccount',
				 				  'newadminuser', 'editadminuser',
								  'changeadminpasswd'))){
				include sprintf('%s/%s.php', WC_BASE, $_POST['action']);
			}
			else {
				switch ($_GET['action']){
					case "accounts":
						include WC_BASE . "/browseaccounts.php";
						break;

					case "catch":
						include WC_BASE . "/catchall.php";
						break;

					default:
						include WC_BASE . "/browse.php";
						break;
				}
			}


		}
		include WC_BASE . "/footer.inc.php";
	} else {
		include WC_BASE . "/login.php";
	}
} else {

	print "<h1>";
	print _("web-cyradm has not yet been configured");
	print "</h1>";
	print "<p>";
	print _("Configuration steps:");
	print "<ul><li>";
	print _("copy the config file that comes with the distribution: ");
	print "<b>cp config/conf.php.dist config/conf.php</b>";
	print "</li><li>";
	print _("Edit the file config/conf.php to match your systems configuration");
	print "</ul>";
	print _("Further information about how to configure Web-cyradm can be found at the following website:");
	print "<br><a href=\"http://www.delouw.ch/linux/Postfix-Cyrus-Web-cyradm-HOWTO/html/index.html\" target=_new>Postfix-Cyrus-Web-cyradm-HOWTO</a>";
	die();
}

