<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- #################### newdomain.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
		<?php
		if ($admintype==0){
			?>
			<h3>
				<?php print _("Add new domain");?>
			</h3>
			<?php

			if (empty($confirmed)){
				?>
				<form action="index.php" style="border: 0px double green;">
					<input type="hidden" name="action" value="newdomain">
					<input type="hidden" name="confirmed" value="true">
					<?php
					if ($DOMAIN_AS_PREFIX==1){
						?>
						<input type="hidden"
						name="prefix"
						value="to-be-overwritten-by-domain"
						>
						<?php
					}
					?>

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
								name="domain">
							</td>
						</tr>

						<?php
						if ($DOMAIN_AS_PREFIX==0) {
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
			} else {
				if ($authorized == TRUE){
					if ($DOMAIN_AS_PREFIX) {
						$prefix = $domain;
					}

					$trans = 'cyrus';
					if ($transport != "cyrus"){
						$trans = $transport . ":" . $tparam;
					}
					// START Andreas Kreisl : freenames
					if (! empty($freenames)){
						$freenames = "YES";
					} else {
						$freenames = "NO";
					}
					if (! empty($freeaddress)){
						$freeaddress = "YES";
					} else {
						$freeaddress = "NO";
					}
					$query="INSERT INTO domain (domain_name, prefix, maxaccounts, quota, domainquota, transport,freenames,freeaddress) VALUES ('$domain', '$prefix', '$maxaccounts', '$quota', '$_GET[domainquota]', '$trans', '$freenames', '$freeaddress')";

					// END Andreas Kreisl : freenames

					$handle = DB::connect ($DB['DSN'],true);
					if (DB::isError($handle)){
						die (_("Database error"));
					}

					$result = $handle->query($query);

					if (!DB::isError($result)){
						?>
						<h3>
							<?php print _("Successfully added");?>:
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
							<?php
							echo get_var_dump($result);
							?>
						</h3>
						<?php
					}
				} else {
					?>
					<h3>
						<?php echo $err_msg;?>
					</h3>
					<?php
				}
			}
		} else {
			?>
			<h3>
				<?php print _("You are not allowed to add new domains");?>
			</h3>
			<?php
		}
		?>
	</td>
</tr>

<!-- #################### newdomain.php end #################### -->

