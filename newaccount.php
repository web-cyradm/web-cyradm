          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
require_once('config.inc.php');

print "<h3>"._("Add new Account to domain")." <font color=red>$domain</font></h3>";

$query1="SELECT * from domain WHERE domain_name='$domain'";
//$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
//$result1=mysql_db_query($MYSQL_DB,$query1,$handle1);

$handle=DB::connect($DSN, true);
$result1=$handle->query($query1);

$row=$result1->fetchRow($result1,$c,'prefix');

//$prefix=mysql_result($result1,0,"prefix");
//$maxaccounts=mysql_result($result1,0,"maxaccounts");

$prefix=$row[1];
$maxaccounts=$row[2];
$transport=$row[4];

if ($transport != "cyrus"){
	die (_("transport is not cyrus, unable to create account"));
}

if (!$confirmed){

	$query2="SELECT * FROM accountuser WHERE prefix='$prefix' order by username";


//	$result2=mysql_db_query($MYSQL_DB,$query2,$handle1);
//	$cnt2=mysql_num_rows($result2);

	$result2=$handle->query($query2);
	$cnt2=$result2->numRows($result2);	

	if ($cnt2+1>$maxaccounts){

		print _("Sorry, no more account allowed for domain"). ".$domain."._("Maximum allowed accounts is"). ".$maxaccounts";
	}
	else{

	print "<p>"._("Total accounts").": ".$cnt2."<p>";

        if (!$DOMAIN_AS_PREFIX) {
		if ($cnt2>0){
		$row2=$result2->fetchRow($result2,$cnt2-1,'username');
//		$lastaccount=mysql_result($result2,$cnt2-1,"username");
		$lastaccount=$row2[0];
		}

		if ($cnt2=0){
			$lastaccount=$prefix."0000";
		}	

		//$test = ereg ("[0-9]*$",$lastaccount,$result_array);
		$test = ereg ("[0-9][0-9][0-9][0-9]$",$lastaccount,$result_array);
		$next= $result_array[0]+1;

		$nextaccount= sprintf("%04d",$next);
		$nextaccount=$prefix.$nextaccount;
	}

	?>

	<form action="index.php" action="get">
	<input type="hidden" name="action" value="newaccount">
	<input type="hidden" name="confirmed" value="true">
	<input type="hidden" name="domain" value="<?php print $domain ?>">
	<table>	
	<?php
		if (!$DOMAIN_AS_PREFIX) {
			print "<input type=\"hidden\" name=\"username\" value=\"$nextaccount\">";
			print "<tr>\n";
			print "<td>"._("Accountname")."</td>\n";
			print "<td>$nextaccount</td>\n";
			print "</tr>\n";
		}
	?>

		<tr>
			<td><?php print _("Email address") ?></td>
			<td><input class="inputfield" type="text" name="email" onFocus="this.style.backgroundColor='#aaaaaa'">@<?php print $domain?>
		</tr>

		<tr>
			<td><?php print _("Quota") ?></td>
			<td><input class="inputfield" type="text" name="quota" value="<?php print $row[3]; ?>" onFocus="this.style.backgroundColor='#888888'"></td>
		</tr>

		<tr>
			<td><?php print _("Password") ?></td>
			<td><input class="inputfield" type="password" name="password" onFocus="this.style.backgroundColor='#cccccc'"></td>
		</tr>

		<tr>
			<td><?php print _("Confirm Password") ?></td>
			<td><input class="inputfield" type="password" name="confirm_password" onFocus="this.style.backgroundColor='#cccccc'"></td>
		</tr>
	
		<tr>
			<td></td>
			<td><input class="button" type="submit" value="<?php print _("Submit") ?>"></td>
		</tr>
	

	</table>
	</form>
	<?php

	}
}

else{
	if ($DOMAIN_AS_PREFIX) {
		$prefix=$domain;
		$username="$email.$domain";
	}

   $query3="INSERT INTO accountuser (username , password , prefix , " .
       "domain_name) VALUES ('$username',";
   switch($CRYPT){
   case 1:
       $query3 .= "ENCRYPT('$password')";
       break;
   case 2:
       $query3 .= "PASSWORD('$password')";
       break;
   default:
       $query3 .= "'$password'";
        }
   $query3.=",'$prefix','$domain')";

//	print $query3;

//	$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
//	$result=mysql_db_query($MYSQL_DB,$query3,$handle1);

	$cyr_conn = new cyradm;
	$error=$cyr_conn -> imap_login();

	if ($error!=0){
		die ("Error $error");
	}


	$result=$handle->query($query3);

	$query4="INSERT INTO virtual (alias , dest , username , status) values ('$email@$domain' , '$username' , '$username' , '1')";
//	$result2=mysql_db_query($MYSQL_DB,$query4,$handle1);

	$result2=$handle->query($query4);

	if ($result and $result2){
		print _("Account successfully added to the Database")."...</br>";
	}


	if ($DOMAIN_AS_PREFIX) {
		$result=$cyr_conn->createmb("user/".$username);
	}
	else {
		$result=$cyr_conn->createmb("user.".$username);
	}

	if ($result){
		print _("Account succesfully added to the IMAP Subsystem");
	}

	if ($DOMAIN_AS_PREFIX) {
		print $cyr_conn->setacl("user/$username","$CYRUS_USERNAME","lrswipcda");	
		$result=$cyr_conn->setmbquota("user/".$username,"$quota");
	}
	else {
		print $cyr_conn->setacl("user.$username","$CYRUS_USERNAME","lrswipcda");
		$result=$cyr_conn->setmbquota("user.".$username,"$quota");
	}


}


?>

</td></tr>

