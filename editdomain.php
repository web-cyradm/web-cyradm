<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### editdomain.php start #################### -->
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
		if (!empty($_GET['confirmed'])){
				if ($DOMAIN_AS_PREFIX) {
					$_GET['newprefix'] = $_GET['domain'];
				}
				// START Andreas Kreisl : freenames
				if (!empty($_GET['freenames'])){
					$freenames = "YES";
				} else {
					$freenames = "NO";
				}
				if (!empty($_GET['freeaddress'])){
					$freeaddress = "YES";
				} else {
					$freeaddress = "NO";
				}
				// END Andreas Kreisl : freenames
				
				$query_result = true;
				$query = "UPDATE domain SET domain_name='".$_GET['newdomain']."', maxaccounts='".$_GET['maxaccounts']."', quota='".$_GET['quota']."', domainquota='".$_GET['domainquota']."', freenames='".$freenames."',freeaddress='".$freeaddress."', folders='".$_GET['defaultfolders']."', prefix='".$_GET['newprefix']."' WHERE domain_name='".$_GET['domain']."'";
				$result = $handle->query($query);
				if (DB::isError($result)){
					$query_result = false;
				}
				if ($_GET['newdomain'] != $_GET['domain']) {
					// update accounts to be associated to the new domain
					$query = "UPDATE accountuser SET domain_name='".$_GET['newdomain']."' WHERE domain_name='".$_GET['domain']."'";
					$result = $handle->query($query);
					if (DB::isError($result)){
						$query_result = false;
					}

					// update domainadmins to have rights transferred to the new domainname
					$query = "UPDATE domainadmin SET domain_name='".$_GET['newdomain']."' WHERE domain_name='".$_GET['domain']."'";
					$result = $handle->query($query);
					if (DB::isError($result)){
						$query_result = false;
					}

					// update aliases to be associated to the new domain
					$query = "UPDATE virtual SET username='".$_GET['newdomain']."' WHERE username='".$_GET['domain']."'";
					$result = $handle->query($query);
					if (DB::isError($result)){
						$query_result = false;
					}
					// ok, everything ok so far. now let's update all aliases and destinations to the new domainname.					
					$query = "SELECT alias FROM virtual WHERE alias LIKE '%".$_GET['domain']."'";
					$result = $handle->query($query);
					$cnt = $result->numRows();
					
					for($i=0; $i < $cnt; $i++){
						$row = $result->fetchRow();
						$oldalias = $row['alias'];
						$newalias = preg_replace("/".preg_quote($_GET['domain'])."/", $_GET['newdomain'], $oldalias);
						$query = "UPDATE virtual SET alias = '".$newalias."' WHERE alias = '".$oldalias."'";
						$result1 = $handle->query($query);
						$query = "UPDATE virtual SET dest = '".$newalias."' WHERE dest = '".$oldalias."'";
						$result2 = $handle->query($query);
					}
				}
				if ($query_result) {
					?>
					<h3>
						<?php print _("Successfully changed domain");?>:
						<span style="color: red;">
							<?php echo $_GET['domain']; ?>
						</span>
					</h3>
					<?php
					include WC_BASE . "/browse.php";
				} else {
					?>
					<h3>
						<?php print _("Database error, please try again");?>
					</h3>
					<?php
				}
		} elseif (empty($_GET['confirmed'])) {
				$query = "SELECT * FROM domain WHERE domain_name='".$_GET['domain']."'";
				$result = $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$domain = $row['domain_name'];
				$prefix = $row['prefix'];
				$maxaccounts = $row['maxaccounts'];
				$quota = $row['quota']; 
				$domainquota = $row['domainquota'];
				// START Andreas Kreisl : freenames
				$freenames = $row['freenames']; 
				// END Andreas Kreisl : freenames
				$freeaddress = $row['freeaddress'];
				$folders = $row['folders'];
				?>
				<form action="index.php" method="get" name="mainform">

					<input type="hidden" name="action" value="editdomain">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php print $domain ?>">
					<input type="hidden" name="prefix" value="<?php print $prefix ?>">
					<table>
						<tr>
							<td>
								<?php print _("Domainname");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="text" size="30"
								name="newdomain" 
								value="<?php print $domain;?>"
								> <a href="javascript:display('ace.php?newdomain='+document.mainform.newdomain.value+'&charset=<?php echo $charset; ?>','ACE',300,200);">Get ACE string</a>
							</td>
						</tr>
						<?php if ($DOMAIN_AS_PREFIX == 0) { ?>
						<tr>
							<td>
								<?php print _("Prefix");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="text"
								size="30"
								name="newprefix" 
								value="<?php print $prefix; ?>"
								>
							</td>
						</tr>


						<!-- // START Andreas Kreisl : freenames -->
						<tr>
							<td>
								<?php print _("Allow Free Names");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="checkbox"
								name="freenames" 
								<?php 
								if ($freenames == "YES") {
									echo "checked";
								}
								?>
								>
							</td>
						</tr>
						<!-- // END Andreas Kreisl : freenames -->
						<?php
						}
						?>
						<tr>
							<td>
								<?php print _("Allow Free Mail Addresses");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="checkbox"
								name="freeaddress" 
								<?php 
								if ($freeaddress == "YES") {
									echo "checked";
								}
								?>
								>
							</td>
						</tr>

						<tr>
							<td width="150">
								<?php print _("Maximum Accounts");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="text" size="4"
								name="maxaccounts" 
								value="<?php print $maxaccounts; ?>"
								>
							</td>
						</tr>

						<tr>
							<td>
								<?php print _("Default Quota in Kilobytes");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="text" size="15"
								name="quota" 
								value="<?php print $quota; ?>"
								>
							</td>
						</tr>

						<tr>
							<td>
								<?php print _("Quota for Domain in Kilobytes");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="text" size="15"
								name="domainquota"
								value="<?php print $domainquota; ?>"
								> (0 = <?php print _("Quota not set"); ?>)
							</td>
							
						</tr>

						<tr>
							<td colspan="2">
								<b><?php print _("Standard Folders");?></b>
								<?php echo "<br>";
								print _("Forders which are automaticaly created for every new account (comma separated list)");?>
							</td>
						</tr> 
						
						<tr>
							<td colspan="2">
								<input class="inputfield"
								type="text" size="80"
								name="defaultfolders"
								value="<?php print $folders; ?>"
								>
							</td>
						</tr>
					</table>

						<tr>
							<td colspan="2" align="center">
								<input 
								class="button" 
								type="submit" 
								value="<?php print _("Submit"); ?>"
								>
							</td>
						</tr>

					</table>
				</form>
				<?php
		} // End of if (empty($confirmed))
	} else {
		?>
		<h3>
			<?php print $err_msg;?>
		</h3>
		<a href="index.php?action=browse"><?php print _("Back");?></a>
		<?php
	} // End of if ($authorized)
	?>
	</td>
</tr>
<!-- #################### editdomain.php end #################### -->

