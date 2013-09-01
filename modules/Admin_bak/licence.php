<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $user, $language;

include("modules/Admin/design.php");

$hasAdminAccess = nkAccessAdmin('License');

if ($hasAdminAccess === true)
{
    admintop();
	echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . LICENSE_TYPE . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"width:90%; margin-left:5%;\">\n";
	echo LICENSE_CONTENT;
	echo "</div>\n"
    . "<div style=\"text-align: center;\"><br /><a class=\"button\" href=\"index.php?file=Admin\"><b>" . BACK . "</b></a></div></form><br /></div></div>\n";

    adminfoot();

}
else{
    admintop();
?>
    <div class="notification error png_bg">
        <div>
            <div style="text-align: center;">
                <?php echo ZONEADMIN; ?>
            </div>
        </div>
    </div>
    <div style="text-align:center;">
        <a class="button" href="javascript:history.back()">
            <b><?php echo BACK; ?></b>
        </a>
    </div>
<?php
    adminfoot();
}
?>
