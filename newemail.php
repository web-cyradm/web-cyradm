<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- #################### newemail.php start #################### -->
<tr>
	<td width="10">&nbsp;</td>
	<td valign="top">

		<?php
		if ($authorized) {
	                $query = "SELECT freeaddress FROM domain WHERE domain_name='".$_GET['domain']."'";
	 		$result = $handle->query($query);
			if (DB::isError($result)) {
				die (_("Database error"));
			}
			$row = $result->fetchRow(DB_FETCHMODE_ASSOC, 0);
			$freeaddress=$row['freeaddress'];
		
			if (!empty($_GET['confirmed']) && empty($_GET['cancel'])) {
				if ($freeaddress != "YES") {
					$query = "INSERT INTO virtual (alias,dest,username) VALUES ('".$_GET['alias']."@".$_GET['domain']."','".$_GET['dest']."','".$_GET['username']."')";
				} else {
					$query = "INSERT INTO virtual (alias,dest,username) VALUES ('".$_GET['alias']."','".$_GET['dest']."','".$_GET['username']."')";
				}
				$result = $handle->query($query);

				if (!DB::isError($result)) {
					?>
					<h3>
						<?php print _("Successfully added");?>:
						<span style="color: red;">
							<?php echo $_GET['alias'];?>
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
			} elseif (!empty($_GET['cancel'])) {
				include WC_BASE . "/editaccount.php";
			} else {
				?>

				<h3>
					<?php print _("New emailadress for user");?>:
					<span style="color: red;">
						<?php echo $_GET['username'];?>
					</span>
				</h3>

				<form action="index.php" method="get">

					<input type="hidden" name="action"
					value="newemail">
					<input type="hidden" name="confirmed"
					value="true">
					<input type="hidden" name="domain"
					value="<?php print $_GET['domain'] ?>"> 
					<input type="hidden" name="username"
					value="<?php print $_GET['username'] ?>"> 

					<table>

						<tr>
							<td>
								<?php print _("Emailadress");?>
							</td>

							<td>
								<input  class="inputfield" type="text" 
								size="30" name="alias">
								<?php
									if ($freeaddress != "YES") {
										print "@".$_GET['domain'];
									}
								?>
							</td>
						</tr>

						<tr>
							<td width="150">
								<?php print _("Destination");?>
							</td>
							
							<td>
								<input  class="inputfield" type="text"
								size="30" name="dest" 
								value="<?php print $_GET['username'];?>">
							</td>
						</tr>


						<tr>
							<td>
								<input class="button" type="submit"
								value="<?php print _("Submit");?>">

								<input class="button" type="submit"
								name="cancel" value="<?php print _("Cancel"); ?>">
							</td>
						</tr>
					</table>
				</form>
				<?php
			} // End of if (!empty($_GET['confirmed']))
		} else { // if ($authorized)
			?>
				<h3>
					<?php echo $err_msg;?>
				</h3>
				<a href="index.php?action=accounts&domain=<?php echo $_GET['domain'];?>"><?php print _("Back");?></a>
			<?php
		}
		?>
	</td>
</tr>
<!-- #################### newemail.php end #################### -->

