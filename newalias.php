<!-- #################################### Start newalias.php ################################# -->

<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<h3>
			<?php print _("Add new alias to domain");?>
			<span style="color: red;">
				<?php echo $_GET['domain'];?>
			</span>
		</h3>

		<form method="get" action="index.php">
			<input type="hidden"
			name="domain"
			value="<?php echo $_GET['domain']; ?>"
			>

			<input type="hidden"
			name="action"
			value="editalias"
			>

			<table>
				<tr>
					<td>
						<?php print _("Email address");?>
					</td>

					<td>
						<input
						type="text"
						name="alias"
						size="30"
						maxlength="50"
						value="<?php if (! empty($_GET['alias'])){ echo $_GET['alias']; } ?>"
						class="inputfield"
						onfocus="this.style.backgroundColor='#aaaaaa'"
						>@<?php
						echo $_GET['domain'];
						?>
					</td>
				</tr>

				<tr>
					<td colspan="2" align="center">
						<input 
						name="create"
						class="button"
						type="submit"
						value="<?php print _("Submit"); ?>"
						>&nbsp;
						
						<input
						name="reset"
						class="buttoN"
						type="reset"
						value="<?php print _("Cancel"); ?>"
						>
					</td>
				</tr>
			</table>
		</form>
	</td>
</tr>

<!-- ##################################### End newalias.php ################################## -->
