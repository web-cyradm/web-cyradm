<?php
class sieve_strs {

function user_domain_decomp ( $user_domain, $host_domain, &$user, &$domain )
{
	$user_domain_x = $user_domain;
	$user = '';
	do {
	  $domain = substr (strstr($user_domain_x,'.'),1);
	  if ( $domain == '' ) {
	    return false;
	  }
	  preg_match ( '/.*\./U', $user_domain_x, $matches );
	  $user_domain_x = $domain;
	  $user_x = rtrim ( $matches[0], '.' );
	  $user .= '.'.$user_x;
	} while (!preg_match("/$domain$/", $host_domain));
	$user = substr ($user,1);
	return true;
}

function get_old_script ( $sieve )
{
  if($sieve->sieve_getscript('sieve')){
    $old_script = '';
    foreach($sieve->response as $line){
      $old_script .= $line;
    }
    $old_script2 = rtrim ($old_script, "\n");
    $old_script = rtrim ($old_script2, "\r");
  } else $old_script ='';
  return $old_script;
}

}
?>
