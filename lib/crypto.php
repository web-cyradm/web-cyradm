<?php
#
# lib for crypting passwords
#
#
#  Copyright (C) 2002 by Luc de Louw
# 
#  License: GNU GLP
#


// Used for login

function checkpassword ($table, $username, $userinput, $encryption){

	include ("config.inc.php");
	include ("DB.php");

	switch ($encryption){
	case "crypt":

		/* First get the encrypted password out of the database to have the salt */
	        $query = "SELECT password from $table where username ='$username'";
		$handle=DB::connect ($DSN,true);
       		$result = $handle->query($query);
		$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);
		
		$dbinput = $row['password'];

		// The salt used is the encrypted password
		
		if ($dbinput == crypt($userinput,$dbinput)){
			return true;
		}
		else {
			return false;
		}

		break;		
	}
}

// Updates only checked passowrd

function updatepassword($table,$username,$userinput,$newpassword, $encryption){

	if (checkpassword($table, $username, $userinput, $encryption)){
		print "Okay Password will be changed";
	}

	else {
		return false;
	}
	

}

// Ignores old password and set an new one

function setpassword ($table, $username, $newpassword,$encryption){
}


?>
