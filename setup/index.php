<?php

include ("../header.inc.php");
require_once("DB.php");

$dbroot=$_POST['dbroot'];
$dbrootpassword=$_POST['dbrootpassword'];
$dbname=$_POST['dbname'];
$createdb=$_POST['createdb'];
$dbuser=$_POST['dbuser'];
$dbuserpassword=$_POST['dbuserpassword'];
$admin=$_POST['admin'];
$adminpassword=$_POST['adminpassword'];
$cyruspassword=$_POST['cyruspassword'];
$proceed=$_POST['Proceed'];

?>


<h3>Web-cyradm setup</h3>
This is the web-cyradm setup, which will guide you troght the installation and setup of the system. <br>
After finishing the setup, <b>you MUST make this file unreadable for the web-server</b>, or web-cyradm denies working for security reasons.

<h4>Database setup (MySQL only at the moment)</h4>
Please choose a user that is allowed to create databases and set privileges. Usually this is the user "root".<br>
This user is only needed for the creation of the database. In a further step you have to provide a application user such as "mail".

<form method="post" name="dbuser">
<table>
<tr>
	<td>Username</td>
	<td><input type="text" size="10" name="dbroot" value="<?php print $dbroot;?>"</input>
</tr>
<tr>
	<td>Password</td>
	<td><input type="password" size="10" name="dbrootpassword" value="<?php print $dbrootpassword;?>"</input>
<input type="submit" name="submit" value="Reload page">
</tr>

</table>

<?php

if (!empty($dbroot) && !empty($dbrootpassword)){

	?>
	<hr>
	<h4>Choose or create a database</h4>
	Here you can choose between a existing database or the creation of a new database.



	<?php

	$DB_TYPE="mysql";

	$DB_HOST="localhost";
	$DB_NAME="mysql";
	$DB_PROTOCOL="unix"; // set to "tcp" for TCP/IP
	$DSN="$DB_TYPE://$dbroot:$dbrootpassword@$DB_PROTOCOL+$DB_HOST/$DB_NAME";
	$handle = DB::connect($DSN, true);


	if (DB::isError($handle)) {
		#die (_("Database error"));
		die ("<h3><font color='red'>Database error</font></h3>");
	}

	#$query="SELECT Db from db";
	$query="SHOW DATABASES";
	$result=$handle->query($query);
	$count=$result->numRows($result);

	if (DB::isError($handle)) {
		print $handle->getMessage();
	}

	?>
	<table>
	<tr>
		<td>Choose a database</td>
		<td>
	<?php

	print "<select name=\"dbname\">
<option value=\"new\" selected>New Database</option>\n<option ";

	for ($i=0;$i<$count;$i++){
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, $i);
		if ($row['Database']==$dbname){
			print "selected ";
		}
		print "value=\"".$row['Database']."\">".$row['Database']."</option>\n";
		if ($i<$count-1){
			print "<option ";
		}
	}

	print "</select>\n";

	?>
		</td>
		<td><input type="submit" name="submit" value="Reload page">
		</td>
	</tr>

	<?php


	if (!isset($dbname)){
		$dbname="new";
	}
	if ($dbname=="new"){
		?>
		<tr>
			<td>Database name</td>
			<td><input type="text" name="createdb" value="<?php print $createdb;?>" size="20"></td>
			<td><input type="submit" name="submit" value="Reload page"></td>
	
		<?php
	}	
	print "</tr></table>";
	if (!empty($createdb) && !empty($dbname) OR $dbname!="new"){
		?>
		<hr>
		<h4>Define a database user</h4>
		Here you need to define the user which and its password that is accessing the database for web-cyradm. The default user is "mail"

		<table>
                <tr>
                        <td>Database user</td>
                        <td><input type="text" name="dbuser" value="<?php print $dbuser;?>" size="20"></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="dbuserpassword" value="<?php print $dbuserpassword;?>" size="20"></td>
                        <td><input type="submit" name="submit" value="Reload page"></td>
		</tr>
		</table>


		<?php

	if (!empty($dbuser) && !empty($dbuserpassword)){
	?>
                <hr>
                <h4>Define the initial Superuser</h4>
		Here you define the inital name and password for the superuser to be able to login into web-cyradm.

                <table>
                <tr>
                        <td>Superusername</td>
                        <td><input type="text" name="admin" value="<?php print $admin;?>" size="20"></td>
                </tr>
                <tr>
                        <td>Password</td>
                        <td><input type="password" name="adminpassword" value="<?php print $adminpassword;?>" size="20"></td>
                        <td><input type="submit" name="submit" value="Reload page"></td>
                </tr>
                </table>
	<?php
	}

        if (!empty($admin) && !empty($adminpassword)){
        ?>
                <hr>
                <h4>Define the password for the user "cyrus"</h4>
                Here you define the inital password for the cyrus user which is needed to create new mailboxes and access to quota functions.

                <table>
                <tr>
                        <td>cyrus password</td>
                        <td><input type="password" name="cyruspassword" value="<?php print $cyruspassword;?>" size="20"></td>
                        <td><input type="submit" name="submit" value="Reload page"></td>
                </tr>
                </table>
		</form>


                <?php

	if (!empty($cyruspassword)){
		?>
		<hr>
		<font color="red">
		<h4>Proceed with this settings?</h4>
		After confirmation, the database will be populated with the initial data.
		</font>
		<form action="populate.php" method="post">
		<input type="hidden" name="dbroot" value="<?php print $dbroot;?>">
		<input type="hidden" name="dbrootpassword" value="<?php print $dbrootpassword;?>">
		<input type="hidden" name="dbname" value="<?php print $dbname;?>">
		<input type="hidden" name="createdb" value="<?php print $createdb;?>">
		<input type="hidden" name="dbuser" value="<?php print $dbuser;?>">
		<input type="hidden" name="dbuserpassword" value="<?php print $dbuserpassword;?>">
		<input type="hidden" name="admin" value="<?php print $admin;?>">
		<input type="hidden" name="adminpassword" value="<?php print $adminpassword;?>">
		<input type="hidden" name="cyruspassword" value="<?php print $cyruspassword;?>">
		<input type="submit" name="submit" value="Proceed">
		</form>

	<?php
	}



	}
	}
}
include ("../footer.inc.php");

