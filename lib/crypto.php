<?php
#  lib for handling crypted passwords
#  Part of the web-cyradm project 
# 
#  See http://www.web-cyradm.org
#
#  Copyright (C) 2002 by Luc de Louw <luc@delouw.ch>
#
#  License: GNU GPL
#
#
class password{

	var $table;
	var $username;
	var $userinput;
	var $newpassword;
	var $encryption;

	// Check if supplied password is valid

	function check($table, $username, $userinput, $encryption){
		include WC_BASE . "/config/conf.php";
		if (!class_exists("DB")) include "DB.php";

		    $query = "SELECT password FROM $table WHERE username ='$username'";
			$handle=DB::connect ($DB['DSN'],true);

			if (DB::isError($handle)) {
				die (_("Database error"));
			}
		
	       	$result = $handle->query($query);
			$row=$result->fetchRow(DB_FETCHMODE_ASSOC, 0);

		$dbinput = $row['password'];
			
		switch (strval($encryption)){
		default:
			if ($dbinput == $userinput){
				return true;
			}
			else {
				return false;
			}
		break;
		case "crypt":
		case "1":
			// The salt used is the encrypted password
	
			if ($dbinput == crypt($userinput,$dbinput)){
				return true;
			}
			else {
				return false;
			}
		break;		
		case "md5":
		case "2":
			if ($dbinput == md5($userinput)){
				return true;
			}
			else {
				return false;
			}
		break;
		// For compatibility with older web-cyradm
		case "mysql":
		case "3":
			if ($dbinput == $this->mysql_password($userinput)){
							return true;
			}
			else {
				return false;
			}

		}
	}

	/* This function sets the new password without checking an old password.
	   If you use this function be sure to first check the old password supplied by the
	   user by doing: return =$pwd->check($table,$username,$userinput,$encryption);
	*/

	function update($table,$username,$newpassword,$encryption){
		include WC_BASE . "/config/conf.php";

		switch ($encryption){
		case "crypt":
			case "1":
			$newpassword=crypt($newpassword,substr($newpassword,0,2));
		break;
			case "md5":
                case "2":
				$newpassword=md5($newpassword);
			break;
			case "mysql":
		// for compatibility with older web-cyradm
			case "3":
				$newpassword==$this->mysql_password($newpassword);;
			break;

		}
		// If no encryption specified plain is used
			
		$query="UPDATE $table SET password='$newpassword' WHERE username='$username'";
			$handle=DB::connect ($DB['DSN'],true);

			if (DB::isError($handle)) {
                                die (_("Database error"));
                        }

			$result = $handle->query($query);
			
			if ($result){	
				return true;
			}
	}

	function encrypt($password,$encryption){

		switch ($encryption){
                case "crypt":
			case "crypt":
			case "1":
			$password=crypt($password,substr(md5(rand()),0,2));
			break;
			case "md5":
			case "2":
				$password=md5($password);
			break;
		// for compatibility with older web-cyradm
			case "mysql":
			case "3":
				$password=$this->mysql_password($password);
		}
		
		// Encrypt should always return something - on plaintext unchanged input
		return $password;
	}
	function mysql_password($input){
	        include_once WC_BASE . "/config/conf.php";
		include_once "DB.php";
		global $DB;
		
		$query = "SELECT PASSWORD('" . $input . "')";
		$handle=DB::connect ($DB['DSN'],true);

		if (DB::isError($handle)) {
                        die (_("Database error"));
            	}

		$result = $handle->query($query);
		$row=$result->fetchRow(DB_FETCHMODE_ORDERED, 0);

		$password = $row[0];
		return $password;
	}
}

?>
