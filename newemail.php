          <tr>
        <td width="10">&nbsp; </td>
        <td valign="top">
<?php


if ($authorized){

if ($confirmed){

        $query="INSERT INTO virtual (alias,dest,username) VALUES('$alias@$domain','$dest','$username')";

        $handle=DB::connect($DSN, true);
	if (DB::isError($handle)) {
		die (_("Database error"));
	}

        $result=$handle->query($query);


        if (!DB::isError($result)){
                print _("Successfully added");
		include ("editaccount.php");
        }
        else{
                print "<p>"._("Database error, please try again")."<p>";
		include ("editaccount.php");
        }

}



if (!$confirmed){

        ?>

	<h3><?php print _("New emailadress for user")?> <?php print $username?></h3>

        <form action="index.php" method="get">

        <input type="hidden" name="action" value="newemail">
        <input type="hidden" name="confirmed" value="true">
        <input type="hidden" name="domain" value="<?php print $domain ?>"> 
        <input type="hidden" name="username" value="<?php print $username ?>"> 

        <table>

        <tr>
        <td><?php print _("Emailadress") ?></td>
	<td><input type="text" size="30" name=alias value="<?php print $alias?>">@<?php print $domain?></td>
        </tr>

        <tr>
        <td width=150><?php print _("Destination") ?></td>
        <td><input type="text" size="30" name=dest value="<?php print $username ?>"> </td>
        </tr>


        <tr><td>
        <input type="submit" value="<?php print _("Submit")?>">
        </td></tr>

        </table>
	</form>

        <?php

}
}
else{

	print "<h3>".$err_msg."</h3>";
}

?>
</td></tr>

