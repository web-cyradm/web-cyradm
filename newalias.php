<!-- #################################### Start newalias.php ################################# -->

<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">
	<h3><?php print _("Add new alias to domain"); ?> <font color=red><?php print( $_GET['domain'] ); ?></font></h3>

	<form action="index.php" method="GET">
	<input type="hidden" name="domain" value="<?php echo $_GET['domain'] ?>">
	<input type="hidden" name="action" value="editalias">
	<table>
		<tr>
			<td><?php print _("Email address")?>:</td>
			<td><input type="text" name="alias" size="30" maxlength="50" value="<?php echo $_GET['alias'] ?>" class="inputfield" onFocus="this.style.backgroundColor='#aaaaaa'">@<?php echo $_GET['domain'] ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
			<input name="create" class="button" value="<?php print _("Submit")?>" type="submit">&nbsp;
			<input name="reset" class="button" value="<?php print _("Cancel")?>" type="reset"></td>
		</tr>
	</table>
	</form>

	</td>
</tr>

<!-- ##################################### End newalias.php ################################## -->
