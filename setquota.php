<!-- #################### setquota.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if ($authorized){
			$cyr_conn = new cyradm;
			$cyr_conn->imap_login();

			$_sep = '.';
			if ($DOMAIN_AS_PREFIX) {
				$_sep = '/';
			}
			$q = $cyr_conn->getquota("user" . $_sep . $username);

			if (empty($confirmed)){
				?>
				<h3>
					<?php print _("Setting individual Quota for user");?>:
					<span style="color: red;">
						<?php echo $username;?>
					</span>
				</h3>
				<form action="index.php">
					<input type="hidden"
					name="action"
					value="setquota">
					
					<input type="hidden"
					name="confirmed"
					value="true">
					
					<input type="hidden"
					name="domain"
					value="<?php print $domain; ?>"
					>
					
					<input type="hidden" 
					name="username"
					value="<?php print $username; ?>" >
					
					<input class="inputfield"
					type="text"
					size="10"
					name="quota"
					value="<?php print $q_total = $q['qmax']; ?>" > Kbytes
					
					<input class="button" 
					type="submit" 
					value="<?php print _("Submit"); ?>"
					>
				</form>
				<?php

			} else {
				$cyr_conn = new cyradm;
				$cyr_conn->imap_login();

				print $cyr_conn->setmbquota("user" . $_sep . $username, $quota);
				?>
				<h3>
					<?php print _("Quote for user");?>
					<span style="color: red;">
						<?php echo $username;?>
					</span>
					<?php print _("changed to");?>
					<span style="color: red;">
						<?php echo $quota;?>
					</span>
				</h3>

				<?php
				include WC_BASE . "/browseaccounts.php";
			}
		}
		?>
	</td>
</tr>

<!-- #################### setquota.php end #################### -->

