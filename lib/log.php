<?
function logger($entry, $level = 'INFO') {
	global $LOG_DIR;
	$fp = @fopen($LOG_DIR . "web-cyradm-login.log", "a");
	
	if ($fp) {
		$date = date("M d H:i:s");
		fwrite($fp, sprintf("%s [%s] %s%s", $date, $level, $entry, "\n"));
		fclose($fp);
		return TRUE;
	}
	else {
		return FALSE;
	}
}
?>
