<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### editdomain.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

	<?php

	if ($admintype == 0){

		if (! empty($confirmed)){
			if ($authorized) {

				// START Andreas Kreisl : freenames
				if (isset($freenames)){
					$freenames="YES";
				} else {
					$freenames="NO";
				}
				if (isset($freeaddress)){
					$freeaddress="YES";
				} else {
					$freeaddress="NO";
				}

				$query = "UPDATE domain SET domain_name='$newdomain', maxaccounts='$maxaccounts', quota='$quota', domainquota='$_GET[domainquota]', freenames='$freenames',freeaddress='$freeaddress',prefix='$_GET[newprefix]' WHERE domain_name='$domain'";
				// END Andreas Kreisl : freenames

				$query2 = "UPDATE accountuser SET domain_name='$newdomain' WHERE domain_name='$domain'";

				$query3 = "UPDATE domainadmin SET domain_name='$newdomain' WHERE domain_name='$domain'";

				$query4 = "UPDATE virtual SET username='$newdomain' WHERE username='$domain'";

				$handle = DB::connect ($DB['DSN'],true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				$result = $handle->query($query);
				$result2 = $handle->query($query2);
				$result3 = $handle->query($query3);
				$result4 = $handle->query($query4);

				if (!DB::isError($result)){
					?>
					<h3>
						<?php print _("Successfully changed domain");?>:
						<span style="color: red;">
							<?php echo $domain;?>
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
			}
			else {
				?>
				<h3>
					<?php echo $err_msg;?>
				</h3>
				<?
			} // End of if (authorized)

		}

		if (empty($confirmed)){
				$query = "select * from domain where domain_name='$domain'";
				$handle = DB::connect($DB['DSN'],true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				$result = $handle->query($query);
				$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
				$domain = $row['domain_name'];
				$prefix = $row['prefix'];
				$maxaccounts = $row['maxaccounts'];
				$quota = $row['quota']; 
				$domainquota = $row['domainquota'];
				// START Andreas Kreisl : freenames
				$freenames=$row['freenames']; 
				// END Andreas Kreisl : freenames
				$freeaddress=$row['freeaddress'];
				?>
				<form action="index.php" method="get">

					<input type="hidden" name="action" value="editdomain">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php print $domain ?>">
					<input type="hidden" name="prefix" value="<?php print $prefix ?>">
					<input type="hidden" name="id" value="<?php print $id ?>">

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
								>
							</td>
						</tr>

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
								if ($freenames=="YES"){
									echo "checked";
								}
								?>
								>
							</td>
						</tr>
						<!-- // END Andreas Kreisl : freenames -->
						<tr>
							<td>
								<?php print _("Allow Free Mail Addresses");?>
							</td>
							
							<td>
								<input class="inputfield"
								type="checkbox"
								name="freeaddress" 
								<?php 
								if ($freeaddress=="YES"){
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
							
						<tr>

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
			<?php print _("Your are not allowed to change domains!");?>
		</h3>
		<?php
	} // End of if ($admintype == 0)
	?>
	</td>
</tr>
<!-- #################### editdomain.php end #################### -->

