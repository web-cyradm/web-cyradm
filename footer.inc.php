<?php
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
}
?>
<!-- ######################## beginning of footer ################ -->
				</td>
			</tr>
			<tr>
				<td height="5%" valign="bottom" bgcolor="#CCCCCC">&nbsp;</td>
				
				<td height="5%" valign="bottom" class="footer" bgcolor="#CCCCCC">
					&copy; 2002, 2003 by Luc de Louw | 
					see <a href="http://www.web-cyradm.org"
					target="_new">http://www.web-cyradm.org</a> | <?php print _("translated by: ").$nls['translator']["$LANG"]; ?>
				</td>
			</tr>
		</table>
	</body>
</html>
<!-- ######################## end of footer #################### -->
