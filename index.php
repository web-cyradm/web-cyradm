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
	include WC_BASE . "/config/conf.php";
	include WC_BASE . "/lib/nls.php";
	include WC_BASE . "/lib/crypto.php";

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
		$LANG = $DEFAULTLANG;
	}

	// For testing porpose, GET variable LANG overrides all
//	$LANG = (! empty($_GET['LANG']))?($_GET['LANG']):($LANG);

	if (isset($_GET['LANG'])){
		$LANG=$_GET['LANG'];
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
//	require_once WC_BASE . "/session.php";

	if ($_SESSION['session_ok'] === TRUE) {
		include "DB.php";
		include WC_BASE . "/validate.inc.php";
		include WC_BASE . "/menu.inc.php";
		include WC_BASE . "/lib/cyradm.php";

		if (empty($_GET['domain']) &&
		    (empty($_GET['action']) || 
		     (! in_array($_GET['action'], array('logout', 'adminuser', 'newdomain', 'editadminuser', 'newadminuser', 'search'))
		     )
		    )
		   ){

//		if (!$_GET['domain'] and ! in_array($_GET['action'], array('logout', 'adminuser', 'newdomain', 'editadminuser'))){
			include WC_BASE . "/welcome.php";
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
							    "editalias", "deletealias", "search"))){
				include sprintf('%s/%s.php', WC_BASE, $_GET['action']);
			} else {
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
	die("web-cyradm has not yet been configured!");
}

