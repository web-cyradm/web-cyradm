<?php

// Specify location of translation tables
//bindtextdomain("web-cyradm", "./locale");

// Choose domain
//textdomain("web-cyradm");


################# Temporary fix for PHP 4.2.0 a better solution has to found #######################
$user= $_SESSION['user'];
$adminuser= $_GET['adminuser'];
$newadminuser= $_GET['newadminuser'];
$admintype= $_GET['admintype'];
$newadmintype= $_GET['newadmintype'];
$type= $_GET['type'];
$domain=$_GET['domain'];
$prefix=$_GET['prefix'];
$action=$_GET['action'];
$row_pos=$_GET['row_pos'];
$username=$_GET['username'];
$password=$_GET['password'];
$confirm_password=$_GET['confirm_password'];
$quota=$_GET['quota'];
$maxaccounts=$_GET['maxaccounts'];
$newdomain=$_GET['newdomain'];
$email=$_GET['email'];
$alias=$_GET['alias'];
$dest=$_GET['dest'];
$newalias=$_GET['newalias'];
$newdest=$_GET['newdest'];
$confirmed=$_GET['confirmed'];
$cancel=$_GET['cancel'];
$searchstring=$_GET['searchstring'];
$transport=$_GET['transport'];
$tparam=$_GET['tparam'];

# Validate input and verify authorization of a users action

$query="SELECT * FROM domainadmin WHERE adminuser='$user'";
$query2="SELECT type FROM adminuser WHERE username='$user'";
$handle=DB::connect($DSN,true);
$result=$handle->query($query);
$result2=$handle->query($query2);
$cnt=$result->numRows();
$row=$result2->fetchRow(DB_FETCHMODE_ASSOC, 0);
$admintype=$row['type'];
if ($admintype!=0){
 $row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
 $allowed_domains=$row['domain_name'];
 $domain=$row['domain_name'];
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
 $result3=$handle->query($query);
 if (!$result3->numRows()){
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
 $result4=$handle->query($query, $handle);
 $result5=$handle->query($query2, $handle);
 $row=$result4->fetchRow(DB_FETCHMODE_ASSOC, 0);
 $quota2=$row['quota'];
 if ($result5->numRows()){
                $authorized=TRUE;
  if ($quota>$quota2){
   Print "<h3>Quota exeedes $quota2, the maximum allowed qutoa for domain.</h3>";
   $authorized=FALSE;
  }
 }
 else if (!$result5->numRows()){
   Print "<h3>Security violation detected, attempt logged</h3>";
   $authorized=FALSE;

 }
 break;

################################## Check input if newemail ################################################
 case "newemail":
 $query="SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
 $result=$handle->query($query, $handle);

 $valid_dest=eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$dest);
 $valid_alias=eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](g|l|m|pa|t|u|v)?$",$alias."@".$domain);
 $row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
  $username2=$row['username'];
 if ($confirmed){
  if ($dest != $username2 and !$valid_dest){
//  if ($dest != $username2 and !ValidateMail($dest)){
   $authorized=FALSE;
   $err_msg="invalid destination";
  }

  elseif (!$valid_alias and isset($alias)){
//  elseif (!ValidateMail($alias."@".$domain) and isset($alias)){
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


## FIXEM: make beter checks 
 case "change_password":
 case "vacation":
 case "forwardaccount":
 case "forwardalias":

         $query="SELECT * FROM accountuser WHERE username='$username' AND domain_name='$domain'";
         $result=$handle->query($query);
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
