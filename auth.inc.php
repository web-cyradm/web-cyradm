<?php
include("config.inc.php");
include ("lib/crypto.php");

session_start();
$method=getenv('REQUEST_METHOD');

$session_ok = $_SESSION['session_ok'];

$login = $_POST['login'];
$password = $_POST['password'];
$LANG = $_POST['LANG'];

if ($login && $password){
     // Log access
     $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
     $date = date("d/M/Y H:i:s");
     fwrite($fp, "LOGIN : $REMOTE_ADDR $login $date $HTTP_USER_AGENT $HTTP_REFERER $REQUEST_METHOD \n");
     fclose($fp);

     $pwd=new password;
     $result=$pwd->check("adminuser",$login,$password,$CRYPT);

     if ($result){
         
          // Log successfull login
          $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
          $date = date("d/M/Y H:i:s");
          fwrite($fp, "PASS: $REMOTE_ADDR $login $date $HTTP_USER_AGENT $HTTP_REFERER $REQUEST_METHOD \n");
          fclose($fp);

          $_SESSION['session_ok'] = TRUE;
          $user = $login;

          session_register("session_ok");
          session_register("user");
          session_register("LANG");

          header ("Location: index.php");

          //print "Authentication sucessful";
          break;
         }
     else
         {
          // Log login failure
          $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
          $date = date("d/M/Y H:i:s");
          fwrite($fp, "FAIL: $REMOTE_ADDR $login $date $HTTP_USER_AGENT $HTTP_REFERER $REQUEST_METHOD \n");
          fclose($fp);
          unset($_SESSION['session_ok']);
          //session_unregister("session_ok");
         header ("Location: failed.php");
         }
    }
else
    {
     print "<center><h4><font face=Verdana,Geneva,Arial,Helvetica,sans-serif>"
	   ._("Web-cyradm is for authorized users only."). 
           "<br>"._("Make sure you entered the right password.").
           "<br>"._("Push the back button in your browser to try again.").
           "<br>"._(" Your attempt to login has been stored.")."</font></h4></center>";
    }


?>

<!-- ###################################### End auth.inc.php ################################################ --!>
