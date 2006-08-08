<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### newdomain.php start #################### -->
<script>
<!--
	function display(url, nam, width, height) {
                window.open(url,nam,'width=' + width + ',height=' + height + ',screenX=40,screenY=30,resizable=0,scrollbars=no,menubar=no,status=no,location=no,toolbar=no,top=220,left=180');
}
// -->
</script>
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
<?php
if ($authorized) {
?>
		<h3>
			<?php print _("Add new domain");?>
		</h3>
<?php
	if (empty($_GET['confirmed'])){
?>
			<form action="index.php" method="get" style="border: 0px double green;" name="mainform">
				<input type="hidden" name="action" value="newdomain">
				<input type="hidden" name="confirmed" value="true">
					<table>
						<tr>
							<td>
								<?php 
								print _("Domainname");
								?>
							</td>

							<td>
								<input 
								class="inputfield" 
								type="text" 
								size="20" 
								name="domain"> <a href="javascript:display('ace.php?domain='+document.mainform.domain.value+'&charset=<?php echo $charset; ?>','ACE',300,200);">Get ACE string</a>
							</td>
						</tr>

						<?php
						if ($DOMAIN_AS_PREFIX == 0) {
							?>
							<tr>
								<td>
									<?php print _("Prefix");?>
								</td>

								<td>
									<input
									class="inputfield"
									type="text"
									size="8"
									name="prefix"
									>
								</td>
							</tr>

						<!-- START Andreas Kreisl : freenames -->
						<tr>
							<td>
								<?php print _("Allow Free Names");?>
							</td>
							<td>
								<input
								class="inputfield"
								type="checkbox"
								name="freenames"
								value="Done by Andreas Kreisl"
								>
							</td>
						</tr>
							<!-- END Andreas Kreisl : freenames -->
							<?php
						} 
						?>
						<tr>
							<td>
								<?php print _("Allow Free Mail Addresses");?>
							</td>
							<td>
								<input
								class="inputfield"
								type="checkbox"
								name="freeaddress"
								>
							</td>
						</tr>
						<tr>
							<td>
								<?php print _("Maximum Accounts");?>
							</td>
							
							<td>
								<input
								class="inputfield"
								type="text"
								size="2"
								name="maxaccounts"
								>
							</td>
						</tr>
						
						<tr>
							<td>
								<?php print _("Default Quota in Kilobytes");?>
							</td>
							
							<td>
								<input
								class="inputfield"
								type="text"
								size="15"
								name="quota"
								value="<?php print $DEFAULT_QUOTA; ?>">
							</td>
						</tr>
						
						<tr>
							<td>
								<?php print _("Quota for Domain in Kilobytes");?>
							</td>

							<td>
								<input
                                                                class="inputfield"
                                                                type="text"
                                                                size="15"
                                                                name="domainquota"
								value="<?php print $DEFAULT_DOMAIN_QUOTA; ?>"> (0 = <?php print _("Quota not set"); ?>)
							</td>
						
						<tr>
							<td colspan="2">
								<p>
									&nbsp;
								</p>
							</td>
						</tr>
					</table>
<!-- 

					<h4>
						<?php print _("Standard Mailboxes");?>
					</h4>

					<table>
						<tr>
							<td>
								<?php print _("emailadress where the default aliases should be mapped (empty means no mapping)");?>
							</td>
						</tr> 
						
						<tr>
							<td>
								<input
								class="inputfield"
								type="text"
								name="defaultaliases"
								>
							</td>
						</tr>

					</table>
-->
					
					<h4>
						<?php print _("Mail transport");?>
					</h4>
					
					<?php print _("Leave this as is, unless you know what you are doing");?>

					<table>
						<tr>
							<td>
								<select 
								name="transport" 
								class="selectfield">
									<option
									selected 
									value="cyrus">cyrus</option>
									<option 
									value="lmtp">lmtp</option>
									<option 
									value="smtp">smtp</option>
									<option
									value="uucp">uucp</option>
								</select>
							</td>
							
							<td>
								<?php print _("Parameter");?>
								<input
								class="inputfield"
								type="text"
								name="tparam"
								>
							</td>
						</tr>
					</table>

					<table>
						<tr>
							<td>
								<input
								class="button"
								type="submit"
								value="<?php print _("Submit"); ?>">
							</td>
						</tr>
					</table>
				</form>
<?php
	} else { // If (empty($_GET['confirmed'])
				if ($DOMAIN_AS_PREFIX) {
					$_GET['prefix'] = $_GET['domain'];
				}

				if ($_GET['transport'] != "cyrus") {
					$trans = $_GET['transport'].":".$_GET['tparam'];
				} else {
					$trans = 'cyrus';
				}
				// START Andreas Kreisl : freenames
				if (!empty($_GET['freenames'])) {
					$freenames = "YES";
				} else {
					$freenames = "NO";
				}
				if (!empty($_GET['freeaddress'])) {
					$freeaddress = "YES";
				} else {
					$freeaddress = "NO";
				}
				$query = "INSERT INTO domain (domain_name, prefix, maxaccounts, quota, domainquota, transport,freenames,freeaddress) VALUES ('".$_GET['domain']."', '".$_GET['prefix']."', '".$_GET['maxaccounts']."', '".$_GET['quota']."', '".$_GET['domainquota']."', '".$trans."', '".$freenames."', '".$freeaddress."')";
				// END Andreas Kreisl : freenames

				$result = $handle->query($query);
				if (!DB::isError($result)){
?>
					<h3>
						<?php print _("Successfully added");?>:
						<span style="color: red;">
							<?php echo $_GET['domain'];?>
						</span>
					</h3>
<?php
				} else {
?>
					<h3>
						<?php print _("Database error, please try again");?>
						<?php # echo get_var_dump($result); ?>
					</h3>
<?php
				}
				include WC_BASE . "/browse.php";
	} // If (empty($_GET['confirmed'])
} else { // If ($authorized)
?>
			<h3>
				<?php echo $err_msg;?>
			</h3>
			<a href="index.php?action=browse"><?php print _("Back");?></a>
<?php
}
?>
	</td>
</tr>

<!-- #################### newdomain.php end #################### -->

