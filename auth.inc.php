<?php
include("config.inc.php");

session_start();
$method=getenv('REQUEST_METHOD');

$session_ok = $_SESSION['session_ok'];

$login = $_POST['login'];
$password = $_POST['password'];
$LANG = $_POST['LANG'];

function authenticate($user, $pw)
    {
     include("config.inc.php");
     include_once("DB.php");
     global $handle;

     $query="SELECT * FROM adminuser WHERE username='$user' AND password='$pw'";
     $handle=DB::connect($DSN, true);
     $result=$handle->query($query);
     $cnt=$result->numRows();

     if ($cnt)
         {
          $row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
          $username=$row['username'];
          $password=$row['password'];
         }

     if ($username==$user and $password==$pw)
         {
          $auth=TRUE;
         }

     if ($auth)
         {
          return TRUE;
         }
     else
         {
          return FALSE;
         }
    }

$_SESSION['session_ok'] = FALSE;

if ($login && $password)
    {
     // Log access
     $fp = fopen($LOG_DIR . "web-cyradm-login.log", "a");
     $date = date("d/M/Y H:i:s");
     fwrite($fp, "LOGIN : $REMOTE_ADDR $login $date $HTTP_USER_AGENT $HTTP_REFERER $REQUEST_METHOD \n");
     fclose($fp);

     if (authenticate($login,$password))
         {
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

          $SID=session_id();

          $query="UPDATE adminuser
                  SET SID='$SID'
                  WHERE username='$user'";

          $result=$handle->query($query);

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
