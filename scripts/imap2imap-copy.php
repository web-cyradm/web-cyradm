#!/usr/local/bin/php -q
#
#  imap2imap-copy.php version 0.1
#
#  Purpose:
#
#  Migrating users from one to another IMAP Server.
#  i.e. from WU-IMAP to cyrus or vice versa
#
#  Just fill in the variables in capital letters and run the script from
#  the commandline. PHP must be compiled as cgi to be able to run this script.
#
#  Release date 2003-01-01
#
#  Copyright 2003 by Luc de Louw luc@delouw.ch
#  
<?php

$OLDMAILBOX="{whateverhost:143}INBOX";
$NEWMAILBOX="{localhost:143}INBOX";
$OLDUSER="foo";
$OLDPASSWORD="letmein";
$NEWUSER="test0001";
$NEWPASSWORD="changeme";

print "Logging in to old IMAP Server ...";
$oldimap = imap_open($OLDMAILBOX, $OLDUSER, $OLDPASSWORD); 

print "done\n\n";

print "Logging in to new IMAP Server ...";

$newimap = imap_open($NEWMAILBOX, $NEWUSER, $NEWPASSWORD);

print "done\n\n";

print "Fetching number of messages...";
$headers = imap_headers ($oldimap);
$cnt=count($headers);

print "done\n\n";

print "Numer of messages to transfer: ".$cnt."\n\n";

for ($i=1;$i<$cnt+1;$i++){

	print "Processing message number: ".$i." ...";

	$head= imap_header($oldimap,$i);

	$from = $head->fromaddress; 
	$to = $head->toaddress; 
	$subject = $head->subject;
	$body = imap_body($oldimap,$i);

	$newheader= imap_fetchheader($oldimap,$i,"FT_UID");
	
	$result=imap_append($newimap,$NEWMAILBOX,$newheader." From: ".$from."\r\n"."To: ".$to."\r\n"."Subject: ".$subject."\r\n\r\n".$body."\r\n");

	if ($result){
		print "done\n";
	}

}

imap_close($oldimap);
imap_close($newimap);

print "\n";
print "##########################################################################\n";
print "#                                                                        #\n";
print "# W A R N I N G !  D O   N O T   L E A V E   T H I S   S C R I P T   I N #\n";
print "#                                                                        #\n";
print "# A   W E B S E R V E R S   D O C U M E N T R O O T ! ! !                #\n";
print "#                                                                        #\n";
print "##########################################################################\n";


?>
