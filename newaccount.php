          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top"> 

<?php
print "<h3>Add new Account to domain <font color=red>$domain</font></h3>";

$query1="SELECT * from domain WHERE domain_name='$domain'";
$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
$result1=mysql_db_query($MYSQL_DB,$query1,$handle1);
$prefix=mysql_result($result1,0,"prefix");
$maxaccounts=mysql_result($result1,0,"maxaccounts");


if (!$confirmed){

	$query2="SELECT * FROM accountuser WHERE prefix='$prefix' order by username";


	$result2=mysql_db_query($MYSQL_DB,$query2,$handle1);
	$cnt2=mysql_num_rows($result2);


	if ($cnt2+1>$maxaccounts){

		print "Sorry, no more account allowed for domain ".$domain.". Maximum allowed accounts is ".$maxaccounts;
	}
	else{

	print "<p>Total accounts: ".$cnt2."<p>";

        if (!$DOMAIN_AS_PREFIX) {
		if ($cnt2>0){

		$lastaccount=mysql_result($result2,$cnt2-1,"username");
		}

		if ($cnt2=0){
			$lastaccount=$prefix."0000";
		}	

		$test = ereg ("[0-9]*$",$lastaccount,$result_array);
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
			print "<td>Accountname</td>\n";
			print "<td>$nextaccount</td>\n";
			print "</tr>\n";
		}
	?>

		<tr>
			<td>Email address</td>
			<td><input class="inputfield" type="text" name="email" onFocus="this.style.backgroundColor='#aaaaaa'">@<?php print $domain?>
		</tr>

		<tr>
			<td>Quota</td>
			<td><input class="inputfield" type="text" name="quota" value="<?php print mysql_result($result1,0,"quota"); ?>" onFocus="this.style.backgroundColor='#888888'"></td>
		</tr>

		<tr>
			<td>Password</td>
			<td><input class="inputfield" type="password" name="password" onFocus="this.style.backgroundColor='#cccccc'"></td>
		</tr>

		<tr>
			<td>Confirm Password</td>
			<td><input class="inputfield" type="password" name="confirm_password" onFocus="this.style.backgroundColor='#cccccc'"></td>
		</tr>
	
		<tr>
			<td></td>
			<td><input class="inputfield" type="submit"></td>
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

	$query3="INSERT INTO accountuser (username , password , prefix , domain_name) VALUES ('$username','$password','$prefix','$domain')";

	//print $query3;

	$handle1=mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWD);
	$result=mysql_db_query($MYSQL_DB,$query3,$handle1);

	$query4="INSERT INTO virtual (alias , dest , username , status) values ('$email@$domain' , '$username' , '$username' , '1')";
	$result2=mysql_db_query($MYSQL_DB,$query4,$handle1);

	if ($result){
		print "Account successfully added to Database....</br>";
	}

	$cyr_conn = new cyradm;
        $cyr_conn -> imap_login();

	if ($DOMAIN_AS_PREFIX) {
		$result=$cyr_conn->createmb("user/".$username);
	}
	else {
		$result=$cyr_conn->createmb("user.".$username);
	}

	if ($result){
		print "Account succesfully added to IMAP Subsystem";
	}

	if ($DOMAIN_AS_PREFIX) {
		print $cyr_conn->setacl("user/$username","$CYRUS_USERNAME","lrswipcda");	
		$result=$cyr_conn->setmbquota("user/".$username,"$quota");
	}
	else {
		print $cyr_conn->setacl("user.$username","$CYRUS_USERNAME","lrswipcda");
		$result=$cyr_conn->setmbquota("user.".$username,"$quota");
	}

	print $result;





}


?>

</td></tr>

