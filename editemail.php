<!-- #################### editemail.php start #################### -->
<tr>
	<td width="10">&nbsp; </td>
	<td valign="top" align="center" style="border: 0px dashed green;">

		<?php
		if ($authorized){

			$query = "select * from virtual where alias='$alias'";
			$handle = DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result = $handle->query($query);
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$alias = $row['alias'];
			$dest = $row['dest'];
			$username = $row['username'];

			if (! empty($confirmed)){
				$query = "UPDATE virtual SET alias='$newalias@$domain', dest='$dest' WHERE alias='$alias'";
				$handle = DB::connect($DB['DSN'], true);
				if (DB::isError($handle)) {
					die (_("Database error"));
				}

				$result = $handle->query($query);

				if (!DB::isError($result)){
					?>
					<h3>
						<?php print _("Successfully changed");?>
					</h3>
					<?php
					include WC_BASE . "/editaccount.php";
				} else {
					?>
					<p>
						<?php print _("Database error, please try again");?>
					</p>
					<?php
				}

			}

			if (empty($confirmed)){

				$alias = spliti("@",$alias);
				$alias = $alias[0];

				if (isset($result_array)){
					print $result_array[0];
				}
				?>

				<form action="index.php" method="get">

					<input type="hidden" name="action" value="editemail">
					<input type="hidden" name="confirmed" value="true">
					<input type="hidden" name="domain" value="<?php echo $domain ?>"> 
					<input type="hidden" name="alias" value="<?php echo $alias . "@" . $domain ?>"> 
					<input type="hidden" name="username" value="<?php echo $username;?>">

					<table>

						<tr>
							<td>
								<?php print _("Emailadress");?>
							</td>

							<td>
								<input class="inputfield" 
								type="text" size="30" 
								name="newalias" value="<?php
								echo $alias;?>">@<?php
								echo $domain;?>
							</td>
						</tr>

						<tr>
							<td width=150>
								<?php print _("Destination");?>
							</td>

							<td>
								<input class="inputfield"
								type="text" size="30"
								name="dest" value="<?php
								echo $dest;?>">
							</td>
						</tr>


						<tr>
							<td colspan="2" align="center">
								<input class="button"
								type="submit"
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
		} // End of if ($authorized)
		?>
	</td>
</tr>

<!-- #################### editemail.php end #################### -->

