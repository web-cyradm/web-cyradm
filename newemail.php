<!-- #################### newemail.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if ($authorized){
			if (! empty($confirmed)){
				$query = "INSERT INTO virtual (alias,dest,username)
				VALUES ('$alias@$domain','$dest','$username')";

				$handle = DB::connect($DB['DSN'], true);
				if (DB::isError($handle)){
					die (_("Database error"));
				}

				$result = $handle->query($query);

				if (! DB::isError($result)){
					?>
					<h3>
						<?php print _("Successfully added");?>:
						<span style="color: red;">
							<?php echo $alias;?>
						</span>
					</h3>
					<?php
					include WC_BASE . "/editaccount.php";
				} else {
					?>
					<h3>
						<?php print _("Database error, please try again");?>
					</h3>
					<?php
					include WC_BASE . "/editaccount.php";
				}

			}

			if (empty($confirmed)){
				?>

				<h3>
					<?php print _("New emailadress for user");?>:
					<span style="color: red;">
						<?php echo $username;?>
					</span>
				</h3>

				<form action="index.php" method="get">

					<input type="hidden" name="action"
					value="newemail">
					<input type="hidden" name="confirmed"
					value="true">
					<input type="hidden" name="domain"
					value="<?php print $domain ?>"> 
					<input type="hidden" name="username"
					value="<?php print $username ?>"> 

					<table>

						<tr>
							<td>
								<?php print _("Emailadress");?>
							</td>

							<td>
								<input type="text" 
								size="30" name="alias"
								value="<?php
								if (isset($alias)){
									print $alias;
								}
								?>">@<?php print $domain;?>
							</td>
						</tr>

						<tr>
							<td width="150">
								<?php print _("Destination");?>
							</td>
							
							<td>
								<input type="text"
								size="30" name="dest" 
								value="<?php 
								print $username;?>">
							</td>
						</tr>


						<tr>
							<td>
								<input type="submit"
								value="<?php
								print _("Submit");?>">
							</td>
						</tr>
					</table>
				</form>
				<?php
			} // End of if (empty($confirmed))
		} else {
			?>
				<h3>
					<?php echo $err_msg;?>
				</h3>
			<?php
		}
		?>
	</td>
</tr>
<!-- #################### newemail.php end #################### -->

