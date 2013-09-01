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

global $user;

include("modules/Admin/design.php");

$hasAdminAccess = nkAccessAdmin('PhpInfo');

if ($hasAdminAccess === true)
{
    if (isset($_REQUEST['what']) && $_REQUEST['what'] != "") $i = $_REQUEST['what'];
    else $i = -1;

    if ($i == -1) $selected1 = "selected=\"selected\""; else $selected1 = "";
    if ($i == 1) $selected2 = "selected=\"selected\""; else $selected2 = "";
    if ($i == 4) $selected3 = "selected=\"selected\""; else $selected3 = "";
    if ($i == 8) $selected4 = "selected=\"selected\""; else $selected4 = "";
    if ($i == 16) $selected5 = "selected=\"selected\""; else $selected5 = "";
    if ($i == 32) $selected6 = "selected=\"selected\""; else $selected6 = "";

    ob_start();
    phpinfo($i);
    $info .= ob_get_contents();
    ob_end_clean();
    preg_match_all("=<body[^>]*>(.*)</body>=siU", $info, $a);
    $php_info = $a[1][0];
    $php_info = preg_replace("/<img (.*?)>/i", "", $php_info);
    $php_info = str_replace("<h2>", "<h3>", $php_info);
    $php_info = str_replace("</h2>", "</h3>", $php_info);
    $php_info = str_replace("<h1 class=\"p\">", "<b>", $php_info);
    $php_info = str_replace("<h1>", "<b>", $php_info);
    $php_info = str_replace("</h1>", "</b>", $php_info);
    $php_info = str_replace("<tr>", "<tr>", $php_info);
    $php_info = str_replace("<table border=\"0\" cellpadding=\"3\" width=\"600\">", "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">", $php_info);
    $php_info = str_replace("<tr class=\"h\">", "<tr>", $php_info);
    $php_info = str_replace("<tr class=\"v\">", "<tr>", $php_info);
    $php_info = str_replace("<td class=\"e\">", "<td>", $php_info);
    $php_info    = str_replace(";", "; ", $php_info);
    $php_info    = str_replace(",", ", ", $php_info);
    $php_info    = str_replace("font", "span", $php_info);
    //$php_info    = str_replace("&", "&amp;", $php_info);


    admintop();
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . PHPINFO_MANAGEMENT . "</h3></div>\n"
    . "<form method=\"post\" action=\"index.php?file=Admin&amp;page=phpinfo\">\n"
    . "<div style=\"text-align: center;\"><b>" . SEE_INFOS . " :</b> <select name=\"what\" onchange=\"submit();\">\n"
    . "<option value=\"-1\" " . $selected1 . ">". ALL_F . "</option>\n"
    . "<option value=\"1\" " . $selected2 . ">". GLOBALES . "</option>\n"
    . "<option value=\"4\" " . $selected3 . ">". CONFIG . "</option>\n"
    . "<option value=\"8\" " . $selected4 . ">". MODULES . "</option>\n"
    . "<option value=\"16\" " . $selected5 . ">". ENVIRONMENT . "</option>\n"
    . "<option value=\"32\" " . $selected6 . ">". VARS . "</option></select><br /><br /></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"width:96%; margin-left:2%;\">" . $php_info . "</div>\n"
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
