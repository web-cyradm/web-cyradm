<?php
function logger($entry, $level = 'INFO') {
	global $LOG_DIR;
	global $LOG_LEVEL;

	$levels = array (
		'DEBUG'=> 0,
		'INFO' => 1,
		'WARN' => 2,
		'ERR'=> 3
	);

	if ($levels[$level] >= $levels[$LOG_LEVEL]) {
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
}
?>
