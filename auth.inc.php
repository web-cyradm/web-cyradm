<?php
define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}

include WC_BASE . "/config/conf.php";
include WC_BASE . "/lib/crypto.php";

session_name('web-cyradm-session');
session_start();
$method=getenv('REQUEST_METHOD');

$session_ok = $_SESSION['session_ok'];

$login = $_POST['login'];
$password = $_POST['login_password'];
$LANG = $_POST['LANG'];

if ($login && $password){
     // Log access
     $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
     $date = date("d/M/Y H:i:s");
     fwrite($fp, sprintf("LOGIN : %s %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $login, $date, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"));
     fclose($fp);

     $pwd=new password;
     $result=$pwd->check("adminuser",$login,$password,$CRYPT);

     if ($result){
         
          // Log successfull login
          $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
          $date = date("d/M/Y H:i:s");
	  fwrite($fp, sprintf("PASS : %s %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $login, $date, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"));
          fclose($fp);

          $_SESSION['session_ok'] = TRUE;
          $user = $login;

	  $_SESSION['user'] = $user;
	  $_SESSION['LANG'] = $LANG;

	  /*
          session_register("session_ok");
          session_register("user");
          session_register("LANG");
	  */

          header ("Location: index.php");

          //print "Authentication sucessful";
          break;
     } else {
          // Log login failure
          $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
          $date = date("d/M/Y H:i:s");
	  fwrite($fp, sprintf("FAIL : %s %s %s %s %s %s%s", $_SERVER['REMOTE_ADDR'], $login, $date, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], "\n"));
          fclose($fp);
          unset($_SESSION['session_ok']);
          //session_unregister("session_ok");
	   #include ("failed.php");
	   header ("Location: failed.php");
	   die();
	
     }
} else {
     print "<center><h4><font face=Verdana,Geneva,Arial,Helvetica,sans-serif>"
	   ._("Web-cyradm is for authorized users only."). 
           "<br>"._("Make sure you entered the right password.").
           "<br>"._("Push the back button in your browser to try again.").
           "<br>"._(" Your attempt to login has been stored.")."</font></h4></center>";
}

?>

<!-- ###################################### End auth.inc.php ################################################ --!>
