<?php

################# Temporary fix for PHP 4.2.0 a better solution has to found #######################
$user= $HTTP_SESSION_VARS['user'];
$adminuser= $HTTP_SESSION_VARS['adminuser'];
$type= $HTTP_SESSION_VARS['type'];
$domain=$HTTP_GET_VARS['domain'];
$prefix=$HTTP_GET_VARS['prefix'];
$action=$HTTP_GET_VARS['action'];
$row_pos=$HTTP_GET_VARS['row_pos'];
$username=$HTTP_GET_VARS['username'];
$password=$HTTP_GET_VARS['password'];
$confirm_password=$HTTP_GET_VARS['confirm_password'];
$quota=$HTTP_GET_VARS['quota'];
$maxaccounts=$HTTP_GET_VARS['maxaccounts'];
$newdomain=$HTTP_GET_VARS['newdomain'];
$email=$HTTP_GET_VARS['email'];
$alias=$HTTP_GET_VARS['alias'];
$dest=$HTTP_GET_VARS['dest'];
$newalias=$HTTP_GET_VARS['newalias'];
$newdest=$HTTP_GET_VARS['newdest'];
$confirmed=$HTTP_GET_VARS['confirmed'];

# Validate input and verify authorization of a users action

$query="SELECT * FROM domainadmin WHERE adminuser='$user'";
$query2="SELECT type FROM adminuser WHERE username='$user'";
$handle=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
$dummy=mysql_select_db($MYSQL_DB,$handle);
$result=mysql_query($query,$handle);
$result2=mysql_query($query2, $handle);
$cnt=mysql_num_rows($result);
$admintype=mysql_result($result2,0,0);
if ($admintype!=0){
	$allowed_domains=mysql_result($result,0,'domain_name');
	$domain=mysql_result($result,0,'domain_name');
}


// Functions

// function ValdateEmail V0.2 2002-04-10 22:14
function ValidateMail($email) {
//     if(!eregi("^[0-9a-z]([-_.]?[0-9a-z])*$",$email)) {
	if (!eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$email)){;
		return 0;
     	}
     	else{
		return 1;
	}
}


############################## Check deleteaccount ##################################################

switch ($action){
	case "deleteaccount":
	$query="SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
	$result3=mysql_db_query($MYSQL_DB,$query, $handle);
	if (!mysql_num_rows($result3)){
		$authorized=FALSE;
	}
	else{
		$authorized=TRUE; 
	}
	break;
################################ Check input if setquota ##################################################
	case "setquota":
	$query="SELECT quota FROM domain WHERE domain_name='$domain'";
	$query2="SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
	$result4=mysql_db_query($MYSQL_DB,$query, $handle);
	$result5=mysql_db_query($MYSQL_DB,$query2, $handle);
	$quota2=mysql_result($result4,0,0);
	if (mysql_num_rows($result5)){
       	        $authorized=TRUE;
		if ($quota>$quota2){
			Print "<h3>Quota exeedes $quota2, the maximum allowed qutoa for domain.</h3>";
			$authorized=FALSE;
		}
	}
	else if (!mysql_num_rows($result5)){
			Print "<h3>Security violation detected, attempt logged</h3>";
			$authorized=FALSE;

	}	
	break;

################################## Check input if newemail ################################################
	case "newemail":
	$query="SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
	$result=mysql_db_query($MYSQL_DB,$query, $handle);
	
	$valid_dest=eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$dest);
	$valid_alias=eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$alias."@".$domain);
 	$username2=mysql_result($result,0,'username');	
	if ($confirmed){
		if ($dest != $username2 and !$valid_dest){
//		if ($dest != $username2 and !ValidateMail($dest)){
			$authorized=FALSE;
			$err_msg="invalid destination";
		}

		elseif (!$valid_alias and isset($alias)){
//		elseif (!ValidateMail($alias."@".$domain) and isset($alias)){
			$authorized=FALSE;
			$err_msg="Invalid email adress";
		}
		else{
	
			$authorized=TRUE;
		}
	}
	else{
		$authorized=TRUE;
	}
	break;

#####################  Check if change email-adress ####################################

	case "editemail":
		print $username;
	        $query="SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
	        $result=mysql_db_query($MYSQL_DB,$query, $handle);
		$valid_dest=eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$newdest);
		$valid_alias=eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$newalias."@".$domain); 
        if ($confirmed){
                if ($newdest != $username2 and !$valid_dest){
                        $authorized=FALSE;
                        $err_msg="invalid destination";
                }

                elseif (!$valid_alias and isset($newalias)){
                        $authorized=FALSE;
                        $err_msg="Invalid email adress"; 
                }
                else{

                        $authorized=TRUE;
                }
        }
        else{
                $authorized=TRUE;
        }
        break;

######################################## Check new domain name ########################################

	case "newdomain":

	if ($confirmed){
		if (!$domain){
			$authorized=FALSE;
			$err_msg="You must choose a valid domainname";
		}
	
		elseif (!$prefix){
			$authorized=FALSE;
			$err_msg="You must choose a valid prefix for your domain";
		}

		else {
			$authorized=TRUE;
		}

	break;
}


######################################### If nothing matches ##########################################

	default:
		break;
}
?>
