<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### editdomain.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php

		if ($admintype == 0){

			if (! empty($confirmed)){

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

				$query = "UPDATE domain SET domain_name='$newdomain', maxaccounts='$maxaccounts',quota='$quota',freenames='$freenames',freeaddress='$freeaddress',prefix='$prefix' WHERE domain_name='$domain'";
				// END Andreas Kreisl : freenames

				$query2 = "UPDATE accountuser SET domain_name='$newdomain' WHERE domain_name='$domain'";

				$handle = DB::connect ($DB['DSN'],true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				$result = $handle->query($query);
				$result2 = $handle->query($query2);

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
				// START Andreas Kreisl : freenames
				$freenames=$row['freenames']; 
				// END Andreas Kreisl : freenames
				$freeaddress=$row['freeaddress'];
				?>
				<form action="index.php" method="get">

					<input type="hidden" name="action" value="editdomain">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php print $domain ?>"> 
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
								name="prefix" 
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

