<?php 
session_start();

$sess_timeout = $SESS_TIMEOUT; // seconds
if (!isset($first)) $first = 1;
$newid = time();
if (($newid > $oldid+$sess_timeout) & !$first) {

  $session_ok = FALSE;
  session_register("session_ok");
  $first = 0;
  session_register("first");
  $oldid = $newid;
  session_register("oldid");

  Header("Location: $base_url/");
}
else {
  $first = 0;
  session_register("first");
  $oldid = $newid;
  session_register("oldid");

  if (!$session_ok) {
        header ("Location: timeout.php");
  }
}

?>
