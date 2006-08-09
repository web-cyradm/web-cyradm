<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<?php 
session_name('web-cyradm-session');
session_start();

$sess_timeout = $SESS_TIMEOUT; // seconds
// if (!isset($first)) $first = 1;
$current_time = time();
// $newid = time();

// Check session of current user.
// If the user doesn't have a session, create a new
// session and set session_ok to FALSE, so that he can
// login.
// If the user already had a session, check if the
// session expired.
// If it has expired, set session_ok to FALSE and
// redirect to timeout.php.
// If the session has NOT expired, update timestamp in session.

// Read old timestamp
$old_time = isset($_SESSION['timestamp'])?($_SESSION['timestamp']):(-1);
// Update timestamp
$_SESSION['timestamp'] = $current_time;
if (! isset($_SESSION['session_ok'])){
	// User doesn't have a session.
	$_SESSION['session_ok'] = FALSE;
} else {
	// User seems to have a session.
	// If it pretends to be a valid session, check if
	// has expired.  If it has, invalidate the session.
	// If the session is already invalid, pass through,
	// so that the login screen is shown.

	if ($_SESSION['session_ok'] === TRUE){
		if ($current_time > ($old_time + $SESS_TIMEOUT)){
			// Session has expired
		#	$_SESSION['session_ok']	= FALSE;
			$_SESSION['timestamp']	= -1;
			$LANG = $_SESSION['LANG'];
			include ("header.inc.php");
			include ("timeout.php");
			include ("footer.inc.php");
			$_SESSION['session_ok'] = FALSE;
			session_unset();
			die();
		} else {
			// Session has NOT expired
			$_SESSION['session_ok']	= TRUE;
		}
	}
}

