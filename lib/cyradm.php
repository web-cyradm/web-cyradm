<?php
/*************************************************************

 File: cyradm-php.lib
 Author: gernot
 Revision: 2.0.0
 Date: 2000/08/11

 This is a completely new implementation of the IMAP Access for
 PHP. It is based on a socket connection to the server an is 
 independent from the imap-Functions of PHP

 Copyright 2000 Gernot Stocker <muecketb@sbox.tu-graz.ac.at>
 
 Changes by Luc de Louw <luc@delouw.ch> 
 - Added renamemailbox command as available with cyrus IMAP 2.2.0-Alpha
 - Added getversion to find out what version of cyrus IMAP is running

 Changes by Lukasz Marciniak <landm@ibi.pl>
 - Added geterror() to find out problems with conection to cyrus IMAP
 - Changed imap_login() and command() to work with geterror()
 - Changed imap_login() to work with passed login and password

 Last Change on $Date$

 $Id$
 

 You should have received a copy of the GNU Public
 License along with this package; if not, write to the
 Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 Boston, MA 02111-1307, USA.

 
 THIS PROGRAM IS AS IT IS! THE AUTHOR TAKES NO RESPONSABILTY ABOUT 
 EVENTUAL DEMAGES, SECURITS-HOLES OR ATTACKES, WHICH COULD BE ENABLED 
 BY THIS PROGRAM

 
 ***************************************************************/


class cyradm
{

	var $host;
	var $port;
	var $mbox;
	var $list;
	var $allacl;

	var $admin;
	var $pass;
	var $fp;
	var $line;
	var $error_msg;

	/*
	#
	#Konstruktor
	#
	*/
	function cyradm()
	{
		global $rtxt;
		$_keys = array('host', 'port', 'admin', 'pass');
		foreach ($_keys as $_key){
			$this->$_key = $GLOBALS['CYRUS'][strtoupper($_key)];
		}
		$this->mbox	= $this->line	= $this->error_msg	= '';
		$this->list	= array();
		$this->fp	= 0;
		$this->allacl	= 'lrswipcda';
	} 


	/*
	#
	# SOCKETLOGIN on Server via Telnet-Connection!
	#
	*/
	function imap_login($login='', $passwd='')
	{
		if (empty($login)) {
			$login = $this->admin;
			$passwd = $this->pass;
		}
		$this->fp = fsockopen($this->host, $this->port, $errno, $errstr);
		$this->error_msg = $errstr;
		if(!$this->fp) {
			echo "<br>ERRORNO: ($errno) <br>ERRSTR: ($errstr)<br><hr>\n";
		} else {
			$_cmd = '. capability';
			$result = $this->command($_cmd);
			foreach($result as $resline) {
				if (eregi("(RIGHTS=){1}[texk]{4}",$resline)) {
					$this->allacl = 'lrswipkxtecda';
				}
			}
			$_cmd = sprintf('. login "%s" "%s"',
				$login, $passwd);
			$this->command($_cmd);
			if ($this->error_msg!="No errors") {
				return 1;
			}
		}
		return $errno;
	}


	/*
	#
	# SOCKETLOGOUT from Server via Telnet-Connection!
	#
	*/
	function imap_logout()
	{
		$this->command(". logout");
		fclose($this->fp);
	} 

	/*
	#
	# SENDING COMMAND to Server via Telnet-Connection!
	#
	*/
	function command($line)
	{
		global $rtxt;
		// print ("line in command: <br><pre><tt>" . $line . "</tt></pre><br>");
		$result = array();
		$i = $f = 0;
		$returntext = "";
		$r = fputs($this->fp,$line . "\n");
		while (!((strstr($returntext,". OK")||(strstr($returntext,". NO"))||(strstr($returntext,". BAD"))))){
			$returntext = $this->getline();
			// print ("$returntext <br>"); 
			if (strstr($returntext,"IMAP4")){
				$rtxt = $returntext;
			}
			if ($returntext){
				if (!((strstr($returntext,". OK")||(strstr($returntext,". NO"))||(strstr($returntext,". BAD"))))){
					$result[$i]=$returntext;
				}
				$i++;
			}
		}

		if (strstr($returntext,". BAD")||(strstr($returntext,". NO"))){
			$result[0]="$returntext";

			if (( strstr($returntext,". NO Quota") )){
				$this->error_msg = "No errors";
			} else {
				/*
				print "<br><font color=red><hr><H1><center><blink>ERROR: </blink>UNEXPECTED IMAP-SERVER-ERROR</center></H1><hr><br>
				<table color=red border=0 align=center cellpadding=5 callspacing=3>
				<tr><td><font color=red>SENT COMMAND: </font></td><td><font color=red>$line</font></td></tr>
				<tr><td><font color=red>SERVER RETURNED:</font></td><td></td></tr>
				";
				for ($i=0; $i < count($result); $i++) {
				print "<tr><td></td><td><font color=red>$result[$i]</font></td></tr>";
				}
				print "</table><hr><br><br></font>";
				*/
				$this->error_msg  = $returntext;
				return false;
			}
		}
		else {
			$this->error_msg = "No errors";
		}
		return $result;  
	}


	/*
	#
	# READING from Server via Telnet-Connection!
	#
	*/
	function getline()
	{
		$this->line = fgets($this->fp, 512);
		return $this->line;
	}

	/*
	#
	# Getting Cyrus IMAP error
	#
	*/
	function geterror()
	{
		return $this->error_msg;
	}

	/*
	#
	# Getting Cyrus IMAP Version
	#
	*/
	function getversion()
	{
		global $rtxt;
		$pos=strpos($rtxt,"IMAP4 v");
		$pos+=7;
		$version=substr($rtxt,$pos,5);
		// print "<p>Version: ".$version;
		return $version;
	}

	/*
	#
	# QUOTA Functions
	#
	*/

	// GETTING QUOTA
	function getquota($mb_name)
	{
		$output = $this->command(". getquota \"" . $mb_name . "\"");
		if (strstr($output[0], ". NO")) {
			$ret["used"] = "NOT-SET";
			$ret["qmax"] = "NOT-SET";
		} else {
			$realoutput = str_replace(")", "", $output[0]);
			$tok_list = split(" ", $realoutput);
			$si_used = sizeof($tok_list) - 2;
			$si_max = sizeof($tok_list) - 1;
			$ret["used"] = str_replace(")", "", $tok_list[$si_used]);
			$ret["qmax"] = $tok_list[$si_max];
		}
		return $ret;
	}  


	// SETTING QUOTA
	function setmbquota($mb_name, $quota)
	{
		$this->command(". setquota \"$mb_name\" (STORAGE $quota)");
	}



	/*
	#
	# MAILBOX Functions
	#
	*/
	function createmb($mb_name)
	{
		$this->command(". create \"$mb_name\"");
	}


	function deletemb($mb_name)
	{
		$this->command(". setacl \"$mb_name\" $this->admin $this->allacl");
		$this->command(". delete \"$mb_name\"");
	}

	function renamemb($mb_name, $newmbname)
	{
		$this->setacl($mb_name, $this->admin,$this->allacl);
		$this->command(". rename \"$mb_name\" \"$newmbname\"");
		$this->deleteacl($newmbname, $this->admin);
	}

	function renamemailbox($oldname, $newname)
	{
		// This only works with cyrus imap version 2.2.x for older please use renameuser
		$ret=$this->command(". renamemailbox \"$oldname\" \"$newname\"");
		return $ret;
	}

	function renameuser($from_mb_name, $to_mb_name)
	{
		$find_out = $split_res = array();
		$owner = $oldowner = '';

		/* Anlegen und Kopieren der INBOX */
		$this->createmb($to_mb_name);
		$this->setacl($to_mb_name, $this->admin,$this->allacl);
		$this->copymailsfromfolder($from_mb_name, $to_mb_name);

		/* Quotas uebernehmen */  
		$quota = $this->getquota($from_mb_name);
		$oldquota = trim($quota["qmax"]);

		if (strcmp($oldquota,"NOT-SET")!=0) {
			$this->setmbquota($to_mb_name, $oldquota);
		}

		/* Den Rest Umbenennen */
		$username = str_replace(".","/",$from_mb_name);
		$split_res = explode(".", $to_mb_name);
		if (strcmp($split_res[0],"user") == 0) {
			$owner=$split_res[1];
		}
		$split_res=explode(".", $from_mb_name);
		if (strcmp($split_res[0],"user") == 0) {
			$oldowner=$split_res[1];
		}

		$find_out = $this->GetFolders($username);

		for ($i=0; $i < count($find_out); $i++) {

			if (strcmp($find_out[$i],$username)!=0) {
				$split_res = split("$username",$find_out[$i]);
				$split_res[1] = str_replace("/",".",$split_res[1]);
				$this->renamemb((str_replace("/",".",$find_out[$i])), ("$to_mb_name"."$split_res[1]"));
				if ($owner) {
					$this->setacl(("$to_mb_name"."$split_res[1]"),$owner,$this->allacl);
				}
				if ($oldowner) {
					$this->deleteacl(("$to_mb_name"."$split_res[1]"),$oldowner);
				}
			}
		}
		$this->deleteacl($to_mb_name, $this->admin);
		$this->imap_logout();
		$this->imap_login();
		$this->deletemb($from_mb_name);
	}

	function copymailsfromfolder($from_mb_name, $to_mb_name)
	{
		$com_ret = $find_out = array();
		$mails = 0;

		$this->setacl($from_mb_name, $this->admin,$this->allacl);
		$com_ret = $this->command(". select $from_mb_name");
		for ($i=0; $i < count($com_ret); $i++) {
			if (strstr($com_ret[$i], "EXISTS")){ 
			$findout=explode(" ", $com_ret[$i]);
			$mails=$findout[1];
			}
		}
		if ($mails != 0){
			$com_ret=$this->command(". copy 1:$mails $to_mb_name");
			for ($i=0; $i < count($com_ret); $i++) {
				print "<span style=\"color: red;\">" . $com_ret[$i] . "</span><br>";
			}
		}
		$this->deleteacl($from_mb_name, $this->admin);  
	}

	/*
	#
	# ACL Functions
	#
	*/
	function setacl($mb_name, $user, $acl)
	{
		$this->command(". setacl \"$mb_name\" \"$user\" $acl");
	}

	function deleteacl($mb_name, $user)
	{
		$result=$this->command(". deleteacl \"$mb_name\" \"$user\"");
	}


	function getacl($mb_name)
	{
		$aclflag = 1;
		$tmp_pos = 0;
		$output = $this->command(". getacl \"$mb_name\"");
		$output = explode(" ", $output[0]);
		$i = count($output)-1;
		while ($i>3) {
			if (strstr($output[$i],'"')) {
				$i++;
			}

			if (strstr($output[$i-1],'"')) {
				$aclflag = 1;
				$lauf = $i - 1;
				$spacestring = $output[$lauf];
				$tmp_pos = $i;
				$i = $i-2;
				while ($aclflag!=0){
					$spacestring=$output[$i]." ".$spacestring;
					if (strstr($output[$i],'"')){
						$aclflag=0;
					}
					$i--; 
				}
				$spacestring = str_replace("\"","",$spacestring);
				if ($i>2) {
					$ret[$spacestring] = $output[$tmp_pos];
				}
			} else { 
				$ret[$output[$i-1]] = $output[$i];
				$i = $i - 2;
			}
		}
		return $ret;
	}

	/*
	#
	# Folder Functions
	#
	*/

	function GetFolders($username)
	{
		$username = str_replace("/", ".", $username);
		$output = $this->command(". list \"$username\" *");

		for ($i=0; $i < count($output); $i++) {
			$splitfolder=split("\"",$output[$i]);
			$output[$i]=str_replace(".","/",$splitfolder[3]);
		}
		return $output;
	}

	function EGetFolders($username)
	{
		$lastfolder=split("/",$username);
		$position=count($lastfolder)-1;
		$last=$lastfolder[$position];
		$username=str_replace("/",".",$username);
		$output = $this->command(". list \"$username\" *");

		for ($i=0; $i < count($output); $i++) {
			$splitfolder=split("\"",$output[$i]);
			$currentfolder=split("\.",$splitfolder[3]);
			$current=$currentfolder[$position];
			// echo "<br>FOLDER:($) CURRENTFOLDER:($splitfolder[3]) CURRENT:($current) LAST:($last) POSITION:($position)<br>";
			if (strcmp($current,$last)==0){
				$newoutput[$i]=str_replace(".","/",$splitfolder[3]);
			}
		}
		return $newoutput;
	}


	/*
	#
	# Folder-Output Functions
	#
	*/
	function GenerateFolderList($folder_array, $username) 
	{
		?>
		<table border="0" align="center">
			<?php
			for ($l=0; $l < count($folder_array); $l++){
				?>
				<tr>
					<td>
						<a href="acl.php?username=<?php
						echo urlencode($username);
						?>&amp;folder=<?php
						echo urlencode($folder_array[$l]);
						?>">/<?php
						echo $folder_array[$l];
						?></a>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}


	function GetUsers($char="") {
		$users = array();
		$this->imap_login();
		$output = $this->GetFolders("user." . $char);
		$this->imap_logout();
		$j = $prev = 0;
		for ($i=0; $i < count($output); $i++) {
			$username = split("/", $output[$i], -1);
			$this->debug("(" . $username[1] . "\n" . $users[$prev]);
			if ((isset($username)) && (isset($users))) {
				if (strcmp($username[1], $users[$prev])) {
					$users[$j] = $username[1];
					$j++;
				}
			}
			if ($j != 0) {
				$prev = $j - 1;
			}
		}
		return $users;
	}

	function debug($message) {
		// echo "<hr>$message<br><hr>";
	}


} //KLASSEN ENDE

