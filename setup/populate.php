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

if ($createdb){
	$query_priv1="INSERT INTO user (Host, User, Password, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', '$createdb', PASSWORD('$dbuserpassword'), 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');";

	$query_priv2="INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', '$createdb', '$createdb', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y ');";

	$query_flush="flush privileges;";
	$create_db="CREATE DATABASE $createdb";
}

$query="CREATE TABLE accountuser (
  username varchar(255) binary NOT NULL default '',
  password varchar(30) binary NOT NULL default '',
  prefix varchar(50) NOT NULL default '',
  domain_name varchar(255) NOT NULL default '',
  UNIQUE KEY username (username)
) TYPE=MyISAM;";

$query2="CREATE TABLE adminuser (
  username varchar(50) binary NOT NULL default '',
  password varchar(50) binary NOT NULL default '',
  type int(11) NOT NULL default '0',
  SID varchar(255) NOT NULL default '',
  home varchar(255) NOT NULL default '',
  PRIMARY KEY  (username)
) TYPE=MyISAM;";

$query3="CREATE TABLE settings (
  `username` varchar(50) binary NOT NULL default '',
  `style` varchar(50) NOT NULL default 'default',
  `maxdisplay` int(4) NOT NULL default '15',
  `warnlevel` int(3) NOT NULL default '90',
  PRIMARY KEY  (username)
) TYPE=MyISAM;";

$query4="CREATE TABLE alias (
  alias varchar(255) NOT NULL default '',
  dest longtext,
  username varchar(50) NOT NULL default '',
  status int(11) NOT NULL default '1',
  PRIMARY KEY  (alias)
) TYPE=MyISAM;";

$query5="CREATE TABLE domain (
  domain_name varchar(255) NOT NULL default '',
  prefix varchar(50) NOT NULL default '',
  maxaccounts int(11) NOT NULL default '20',
  quota int(10) NOT NULL default '20000',
  domainquota int(10) NOT NULL default '0',
  transport varchar(255) NOT NULL default 'cyrus',
  freenames enum('YES','NO') NOT NULL default 'NO',
  freeaddress enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (domain_name),
  UNIQUE KEY prefix (prefix)
) TYPE=MyISAM;";

$query6="CREATE TABLE domainadmin (
  domain_name varchar(255) NOT NULL default '',
  adminuser varchar(255) NOT NULL default ''
) TYPE=MyISAM;";

$query7="CREATE TABLE virtual (
  alias varchar(255) NOT NULL default '',
  dest longtext,
  username varchar(50) NOT NULL default '',
  status int(11) NOT NULL default '1',
  KEY alias (alias)
) TYPE=MyISAM;";

$query8="CREATE TABLE log (
  id int(11) NOT NULL auto_increment,
  msg text NOT NULL,
  user varchar(255) NOT NULL default '',
  host varchar(255) NOT NULL default '',
  time datetime NOT NULL default '2000-00-00 00:00:00',
  pid varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;";

$query9="INSERT INTO adminuser (username, password) VALUES ('$admin', ENCRYPT('$adminpassword'));";
$query10="INSERT INTO domainadmin (domain_name,adminuser) VALUES ('*','$admin');";
$query11="INSERT INTO accountuser (username, password) VALUES ('cyrus', ENCRYPT('$cyruspassword'));";

?>
<h4>Population seems to be successful</h4>
Now create the files /etc/pam.d/imap, pop and sieve with the following content:

<table border=2 bordercolor="ffffff" width=80% >
<tr><td bgcolor="#a7a6a6">
auth sufficient pam_mysql.so user=<?php print $dbuser;?> passwd=<?php print $dbuserpassword;?> host=localhost db=<?php print $createdb;?> table=accountuser usercolumn=username passwdcolumn=password crypt=1 logtable=log logmsgcolumn=msg logusercolumn=user loghostcolumn=host logpidcolumn=pid logtimecolumn=time
<p>
auth required pam_mysql.so user=<?php print $dbuser;?> passwd=<?php print $dbuserpassword;?> 
host=localhost db=<?php print $createdb?> table=accountuser usercolumn=username   
passwdcolumn=password crypt=1 logtable=log logmsgcolumn=msg logusercolumn=user loghostcolumn=host logpidcolumn=pid logtimecolumn=time
</td>
</tr>
</table>

<p>
<b>Warning!</b> Be sure that the parameters are on ONE line when copy-pasting.


<?php

include ("../footer.inc.php");
?>
