<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}

if (! isset($domain)){
	$domain = '';
}
?>
<!-- ############################## Begin Menu ############################################ -->
<table border="0" cellspacing="2" cellpadding="0">
	<tr>
		<?php
		if ($admintype==0){
			?>
			<!-- ############### Root menu first ########## -->
			<td colspan="7">
				<?php print _("Superusers Menu");?>
			</td>

			<td colspan="6">
				<?php print _("Domainmasters menu");?>
			</td>

		</tr>

		<tr>
			<td class="rootnavi">
				<a href="index.php?action=newdomain&amp;domain=new"
				><?php print _("add new domain");?></a>
			</td>

			<td>&nbsp;</td>

			<td class="rootnavi">
				<a href="index.php?action=browse"
				><?php print _("browse domains");?></a>
			</td>

			<td>&nbsp;</td>

			<td class="rootnavi">
				<a href="index.php?action=adminuser&amp;domain=<?php echo $domain;?>"
				><?php print _("adminusers");?></a>
			</td>

			<td>&nbsp;</td>

			<td>&nbsp;</td>
			
			<?php
		}
		?>
		<!-- ################ And the supervisors menu ##### -->

		<td class="navi">
			<a href="index.php?action=accounts&amp;domain=<?php echo $domain;?>"
			><?php print _("accounts");?></a>
		</td>

		<td>&nbsp;</td>

		<!--
		# Temporary removed, subject to discuss
		<td class="navi">
			<a href="index.php?action=aliases&amp;domain=<?php echo $domain;?>"
			><?php print _("Aliases");?></a>
		</td>
		-->

		<td>&nbsp;</td>

		<td class="navi">
			<a href="index.php"><?php print _("home");?></a>
		</td>

		<td>&nbsp;</td>

		<td class="navi">
			<a href="index.php?action=logout&amp;domain=<?php echo $domain;?>"
			><?php print _("logout");?></a>
		</td>

		<td>&nbsp;</td>

		<td class="navi">
			<form action="index.php" method="get" >
				<input type="submit" value="<?php 
				print _("Search");
				?>">
				<input type="hidden" name="action" value="search">
				<input type="hidden" name="domain" value="<?php echo $domain;?>">
				<input type="text" size="10" class="inputfield" name="searchstring" value="<?php if (isset($searchstring))  echo $searchstring; ?>">
			</form>
		</td>
	</tr>
</table>

</td></tr>

<tr>
	<td width="10">&nbsp;</td>

	<td valign="top" height="30">
		<table border="0" cellspacing="2" cellpadding="2"
		class="header">
			<tr>
				<td>
					-&gt;
				</td>

				<td>
					<?php print _("Logged in as user");?>:
				</td>

				<td style="font-weight: bold;">
					<?php echo $user;?>
				</td>

				<td>
					-&gt;
				</td>

				<td>
					<?php print _("Your role is");?>:
				</td>

				<td style="font-weight: bold; color: red;">
					<?php
					if ($admintype == 0){
						print _("Superuser");
					} elseif ($admintype == 1){
						print _("Domain Master");
					}
					?>
				</td>

				<td>
					-&gt;
				</td>

				<td>
					<?php print _("Current domain is");?>:
				</td>

				<td style="font-weight: bold;">
					<?php
					if (empty($domain)){
						print _("No domain selected");
					} else {
						echo $domain;
					}
					?>
				</td>
			</tr>
		</table>
	</td>
</tr>

<tr>
	<td width="10">&nbsp;</td>
	
	<td height="5">
		<hr noshade="noshade" size="1">
	</td>
</tr>

<!-- ############################## End Menu ############################################ -->

