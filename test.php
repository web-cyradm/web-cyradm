<?php
/*
 * Web-cyradm testpage based in the Horde Test-page
 *
 * Changes: Stuff not needed by web-cyradm was deleted
 *
 * Copyright 2002 Brent J. Nordquist <bjn@horde.org>
 * Copyright 1999-2002 Charles J. Hagenbuch <chuck@horde.org>
 * Copyright 1999-2002 Jon Parise <jon@horde.org>
 *
 * See the enclosed file COPYING for license information (LGPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 */

@session_name('web-cyradm-testing-session');
@session_start();
/* Register a session. */
if (!isset($_SESSION['webcyradm_test_count'])) {
    $horde_test_count = 0;
    session_register('webcyradm_test_count');
}

$webcyradm_test_count = &$_SESSION['webcyradm_test_count'];

/* We want to be as verbose as possible here. */
error_reporting(E_ALL);

/* Set character encoding. */
header('Content-type: text/html; charset=utf-8');
header('Vary: Accept-Language');

function testErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
    global $pear, $newpear, $pearmail, $pearlog, $peardb, $unkerr;
    if (preg_match("/PEAR\.php/", $errmsg)) {
        $pear = false;
    } elseif (preg_match("/IT_Error\.php/", $errmsg)) {
        $newpear = false;
    } elseif (preg_match("/RFC822\.php/", $errmsg)) {
        $pearmail = false;
    } elseif (preg_match("/DB\.php/", $errmsg)) {
        $peardb = false;
    } else {
        $unkerr = $errmsg;
    }
}

function status($foo) {
    if ($foo) {
        echo '<font color="green"><b>Yes</b></font>';
    } else {
        echo '<font color="red"><b>No</b></font>';
    }
}

/* Parse PHP version */
function split_php_version($version)
{
    // First pick off major version, and lower-case the rest.
    if (strlen($version) >= 3 && $version[1] == '.') {
        $phpver['major'] = substr($version, 0, 3);
        $version = substr(strtolower($version), 3);
    } else {
        $phpver['major'] = $version;
        $phpver['class'] = 'unknown';
        return $phpver;
    }
    if ($version[0] == '.') {
        $version = substr($version, 1);
    }
    // Next, determine if this is 4.0b or 4.0rc; if so, there is no minor,
    // the rest is the subminor, and class is set to beta.
    $s = strspn($version, '0123456789');
    if ($s == 0) {
        $phpver['subminor'] = $version;
        $phpver['class'] = 'beta';
        return $phpver;
    }
    // Otherwise, this is non-beta;  the numeric part is the minor,
    // the rest is either a classification (dev, cvs) or a subminor
    // version (rc<x>, pl<x>).
    $phpver['minor'] = substr($version, 0, $s);
    if ((strlen($version) > $s) && ($version[$s] == '.' || $version[$s] == '-')) {
        $s++;
    }
    $phpver['subminor'] = substr($version, $s);
    if ($phpver['subminor'] == 'cvs' || $phpver['subminor'] == 'dev' || substr($phpver['subminor'], 0, 2) == 'rc') {
        unset($phpver['subminor']);
        $phpver['class'] = 'dev';
    } else {
        if (!$phpver['subminor']) {
            unset($phpver['subminor']);
        }
        $phpver['class'] = 'release';
    }
    return $phpver;
}

/* Display PHP version bullets */
function show_php_version($phpver)
{
    echo '    <li>PHP Major Version: ' . $phpver['major'] . "</li>\n";
    if (isset($phpver['minor'])) {
        echo '    <li>PHP Minor Version: ' . $phpver['minor'] . "</li>\n";
    }
    if (isset($phpver['subminor'])) {
        echo '    <li>PHP Subminor Version: ' . $phpver['subminor'] . "</li>\n";
    }
    echo '    <li>PHP Version Classification: ' . $phpver['class'] . "</li>\n";
}

/* PHP version-parsing regression test; early PHP version formats were only */
/* roughly consistent, thus the need to test a wide range. Lately they've */
/* been better. */
if (false) {
    $phpversions = array('4.0B1', '4.0B2-1', '4.0B2', '4.0B3-RC2', '4.0b3-RC3', '4.0b3-RC4', '4.0b3-RC5', '4.0b3', '4.0b4-rc1', '4.0b4', '4.0b4pl1', '4.0RC1', '4.0RC2', '4.0.0', '4.0.1', '4.0.2-dev', '4.0.2', '4.0.3RC1', '4.0.3RC2', '4.0.3', '4.0.3pl1', '4.0.4RC3', '4.0.4RC5', '4.0.4RC6', '4.0.4', '4.0.4pl1-RC1', '4.0.4pl1', '4.0.5RC1', '4.0.5-dev', '4.0.6RC1', '4.0.6', '4.0.7RC1', '4.0.7', '4.1.0RC1', '4.1.0');
    foreach ($phpversions as $version) {
        echo "    <li>PHP Version: $version</li>\n";
        $phpver = split_php_version($version);
        show_php_version($phpver);
        echo '<br/>';
    }
}

/* PHP Version */
$phpver = split_php_version(phpversion());

/* PHP module capabilities */
$gettext = extension_loaded('gettext');
$imap = extension_loaded('imap');
$ldap = extension_loaded('ldap');
$mcrypt = extension_loaded('mcrypt');
$mysql = extension_loaded('mysql');
$pgsql = extension_loaded('pgsql');

/* PHP Settings */
$magic_quotes_runtime = !get_magic_quotes_runtime();
$file_uploads = ini_get('file_uploads');
$short_open_tag = ini_get('short_open_tag');

/* PEAR */
$pear = true;
$newpear = true;
$pearmail = true;
$peardb = true;
$unkerr = "";
set_error_handler('testErrorHandler');
include 'PEAR.php';
include 'HTML/IT_Error.php';  # new in 4.0.7RC
include 'Mail/RFC822.php';
include 'DB.php';
restore_error_handler();

/* Check the version of the pear database API. */
if ($peardb) {
    $peardbversion = '0';
    $peardbversion = @DB::apiVersion();
    if ($peardbversion < 2) {
        $peardb = false;
    }
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">';

/* Handle special modes */
if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
    case 'phpinfo':
        phpinfo();
        exit;
        break;

    case 'unregister':
        $_SESSION['webcyradm_test_count'] = null;
        session_unregister('webcyradm_test_count');
        ?>
        <html>
        <body bgcolor="white" text="black">
        <font face="Helvetica, Arial, sans-serif" size="2">
        The test session has been unregistered.<br>
        <a href="test.php">Go back</a> to the test.php page.<br>
        <?php
        exit;
        break;

    default:
        break;
    }
} else {
?>

<html>
<head>
<title>Web-cyradm: System Capabilities Test</title>
<!--<link rel="stylesheet" href="css/web-cyradm.css" type="text/css"> -->
</head>

<body bgcolor="#ffffff" text="#000000">

<table border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td>

<h3>PHP Version</h3>
<ul>
    <li><a href="test.php?mode=phpinfo">View phpinfo() screen</a></li>
    <li>PHP Version: <?php echo phpversion(); ?></li>
<?php
    show_php_version($phpver);
    if ($phpver['major'] < '4.0') {
        echo '        <li><font color="red">You need to upgrade to PHP4. PHP3 will not work.</font></li>';
        $requires = 1;
    } elseif ($phpver['class'] == 'beta' || $phpver['class'] == 'unknown') {
        echo '        <li><font color="red">This is a beta/prerelease version of PHP4. You need to upgrade to a release version.</font></li>';
        $requires = 1;
    } elseif ($phpver['major'] == '4.0') {
        echo '        <li><font color="red">This version of PHP is not supported. You need to upgrade to a more recent version.</font></li>';
        $requires = 1;
    } elseif ($phpver['major'] == '4.1' || $phpver['major'] == '4.2' || $phpver['major'] == '4.3') {
        if ($phpver['major'] == '4.1' && $phpver['minor'] < '2') {
            $insecure = 1;
        }
        echo '        <li><font color="green">You are running a supported version of PHP.</font></li>';
    } else {
        echo '        <li><font color="orange">Wow, a mystical version of PHP from the future.</font></li>';
    }
    if (!empty($requires)) {
        echo '        <li>Web-cyradm requires PHP 4.1.0.</li>';
    }
    if (!empty($insecure)) {
        echo '        <li><font color="orange">This version of PHP contains a serious security vulnerability in its upload code.</font> You should apply the patch or upgrade to 4.1.2 or later as soon as possible.</li>';
    }
    echo '</ul>';
?>

<h3>PHP Module Capabilities</h3>
<ul>
    <li>Gettext Support: <?php status($gettext); ?></li>
    <?php if (!$gettext) { ?>
    <li><font color="red"><b>Web-cyradm will not run without gettext support. Compile php <code>--with-gettext</code> before continuing.</b></font></li>
    <?php exit; } ?>
    <li>IMAP Support: <?php status($imap) ?></li>
    <li>LDAP Support: <?php status($ldap); ?></li>
    <li>Mcrypt Support: <?php status($mcrypt); ?></li>
    <li>MySQL Support: <?php status($mysql); ?></li>
    <li>PostgreSQL Support: <?php status($pgsql); ?></li>
</ul>

<h3>Miscellaneous PHP Settings</h3>
<ul>
    <li>magic_quotes_runtime set to Off: <?php echo status($magic_quotes_runtime); ?></li>
    <?php if (!$magic_quotes_runtime) { ?>
    <li><font color="red"><b>magic_quotes_runtime may cause problems with database inserts, etc. Turn it off.</b></font></li>
    <?php } ?>
</ul>

<h3>PHP Sessions</h3>
<?php $webcyradm_test_count++; ?>
<ul>
    <li>Session counter: <?php echo $webcyradm_test_count; ?></li>
    <li>To unregister the session: <a href="test.php?mode=unregister">click here</a></li>
</ul>

<h3>PEAR</h3>
<ul>
    <li>PEAR - <?php status($pear); ?></li>
    <?php if (!$pear) { ?>
        <li><font color="red">Check your PHP include_path setting to make sure it has the PEAR library directory.</font></li>
    <?php } ?>
    <li>Recent PEAR - <?php status($newpear); ?></li>
    <?php if ($pear && !$newpear) { ?>
        <li><font color="red">This version of PEAR is not recent enough. See the <a href="http://www.horde.org/pear/">Horde PEAR page</a> for details.</font></li>
    <?php } ?>
    <li>Mail::RFC822 - <?php status($pearmail); ?></li>
    <?php if ($pear && !$pearmail) { ?>
       <li><font color="red">Make sure you are using a recent version of PEAR which includes the Mail class. This is not a problem</font></li>
    <?php } ?>
    <li>DB - <?php status($peardb); ?></li>
    <?php if ($pear && !$peardb) {
              if ($peardbversion) { ?>
                  <li><font color="red">Your version of DB is not recent enough.</font></li>
              <?php } else { ?>
                  <li><font color="red">Web-cyradm does not run without DB </font></li>
              <?php }
          } ?>
    <?php if ($unkerr) { ?>
        <li><font color="red">Unknown error:</font> <?php echo $unkerr; ?></li>
    <?php } ?>
</ul>

</td></tr>
</table>

<?php } ?>

</body>
</html>
