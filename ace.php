<?php
session_name('web-cyradm-session');
session_start();
if ($_SESSION['session_ok'] === TRUE){
?>
<html>
	<body>
<?php
	@include 'Net/IDNA.php';

	$field = 'domain';
	if (!empty($_GET['newdomain'])) {
		$_GET['domain'] = $_GET['newdomain'];
		$field = 'newdomain';
	}

	if (!empty($_GET['domain']) && substr($_GET['domain'],0,4) != 'xn--') {
		if (class_exists("Net_IDNA")) {
			$idn = Net_IDNA::getInstance();
			$_GET['domain'] = iconv($_GET['charset'],'UTF-8',$_GET['domain']);
			$ACE = $idn->encode($_GET['domain']);
?>
			<script>
			<!--
			self.opener.document.mainform.<?php echo $field; ?>.value = '<?php echo $ACE; ?>';
			self.close();
			//-->
			</script>
<?php
		} else {
?>
		<center>
			<?php print _("To use Internationalized Domain Names (IDNs) you should install Net_IDNA class from");?> <a href="http://pear.php.net/package/Net_IDNA" target="_blank">pear.php.net</a><br>
			<a href="javascript:self.close();"><?php print _("Close"); ?></a>
		</center>
<?php
		}
	} else {
?>
		<center>
			<?php print _("Invalid domain name"); ?><br>
			<a href="javascript:self.close();"><?php print _("Close"); ?></a>
		</center>
<?php
	}
?>
	</body>
</html>
<?php
}
?>
