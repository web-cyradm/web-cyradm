<?php

include ("header.inc.php");
require_once("DB.php");

$dbroot=$_POST['dbroot'];
$dbrootpassword=$_POST['dbrootpassword'];
$dbname=$_POST['dbname'];
$createdb=$_POST['createdb'];

?>
<h3>Web-cyradm setup</h3>
This is the web-cyradm setup, which will guide you troght the installation and setup of the system. <br>
After finishing the setup, <b>you MUST make this file unreadable for the web-server</b>, or web-cyradm denies working for security reasons.

<h4>Database setup (MySQL only at the moment)</h4>
Please choose a user that is allowed to create databases and set privileges. Usually this is the user "root"

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

if (isset($dbroot) && isset($dbrootpassword)){

?>
<hr>
<h4>Choose or create a database</h4>
Here you can choose between a existing database or the creation of a new database.



<?php
}

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

if ($dbname=="new"){
?>
<tr>
	<td>Database name</td>
	<td><input type="text" name="createdb" value="<?php print $createdb;?>" size="20"></td>
	<td><input type="submit" name="submit" value="Reload page"></td>

<?php
}	
print "</tr></table>";

if (isset($createdb) OR isset($dbname)){
?>
<hr>
<h4>Define a database user</h4>
	

<?php
}
include ("footer.inc.php");
?>
