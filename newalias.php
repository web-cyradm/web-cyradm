<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>

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
    <?php
                require_once WC_BASE . '/config/conf.php';
                $query1 = "SELECT * from domain WHERE domain_name='$domain'";
		
                $handle = DB::connect($DB['DSN'], true);
                if (DB::isError($handle)) {
    	            die (_("Database error"));
                }

                $result1 = $handle->query($query1);

                $row = $result1->fetchRow(DB_FETCHMODE_ORDERED, 0);

	        $prefix         = $row[1];
	        $maxaccounts    = $row[2];
                $def_quota      = $row[3];
                $transport      = $row[4];
                // START Andreas Kreisl : freenames
                $freenames      = $row[5];
                // END Andreas Kreisl : freenames
                $freeaddress    = $row[6];

	        if ($transport != "cyrus"){
                        die (_("transport is not cyrus, unable to create account"));
            	}
		
		if (empty($confirmed)){
		
	        $query2         = "SELECT * FROM virtual WHERE username='$prefix' order by alias";
		$result2        = $handle->query($query2);
		$cnt2           = $result2->numRows($result2);
		
		if ($cnt2+1 > $maxaccounts){
			?>
			<h3>
			    <?php print _("Sorry, no more alias allowed for domain");?>
			    <span style="color: red;">
				<?php echo $domain;?>
			    </span>
			    <br>
			    <?php print _("Maximum allowed aliases is");?>
			    <span style="font-weight: bolder;">
				<?php echo $maxaccounts;?>
			    </span>
			<?php
		} else {
			?>

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
		<?php
		    }
		}
		?>
	</td>
</tr>

<!-- ##################################### End newalias.php ################################## -->
