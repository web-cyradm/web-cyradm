<!-- #################### catchall.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top"> 

		<h3>
			<?php print _("Define a Account for receiving undefined adresses for domain");?>
			<span style="color: red;">
				<?php echo $domain;?>
			</span>
		</h3>
		<?php
		if (empty($confirmed)){
			?>
			<h3>
				<?php print _("Do you really want to define the user");?>
				<span style="color: red;">
					<?php echo $username;?>
				</span>
				<?php print _("to receive all undefined emailadresses");?>
				?
			</h3>

			<form action="index.php" method="get">
				<input type="hidden" name="action"
				value="catch">
				
				<input type="hidden" name="confirmed"
				value="true">
				
				<input type="hidden" name="domain"
				value="<?php print $domain;?>">
				
				<input type="hidden" name="username"
				value="<?php print $username;?>">
				
				<input class="button" type="submit"
				name="confirmed"
				value="<?php print _("Yes");?>">
				
				<input class="button" type="submit"
				name="cancel"
				value="<?php print _("Cancel");?>">
			</form>
			<?php
		} elseif (! empty($confirmed) AND empty($cancel)){

			# First Delete the entry from the database

			$deletequery = "DELETE from virtual WHERE alias='@$domain'";

			# And then add the new one	

			$insertquery = "INSERT INTO virtual (alias, dest, username, status) values ('@$domain' , '$username' , '$username' , '1')";
			
			$handle=DB::connect($DB['DSN'], true);
			if (DB::isError($handle)) {
				die (_("Database error"));
			}

			$result = $handle->query($deletequery);
			$result = $handle->query($insertquery);

			if ($result){
				?>
				<h3>
					<?php print _("successfully added to Database");?>
				</h3>
				<?php
			} else {
				?>
				<h3>
					<?php print _("Database error, please try again");?>
				</h3>
				<?php
			}
		} elseif (! empty($cancel)){
			?>
			<h3>
				<?php print _("Cancelled");?>
			</h3>
			<?php
		}
		?>
	</td>
</tr>
<!-- #################### catchall.php end #################### -->

