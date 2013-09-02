<?php
/**
 * phpinfo.php
 *
 * PhpInfo display
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');


// Inclusion du layout de l'administration
require_once 'modules/Admin/views/layout.php';

$hasAdminAccess = nkAccessAdmin('Smilies');

adminHeader();

if ($hasAdminAccess === true){

    $i = -1;

    if (isset($_REQUEST['what']) && $_REQUEST['what'] != "") {
        $i = $_REQUEST['what'];
    }

    $selected1 = $selected2 = $selected3 = $selected4 = $selected5 = $selected6 = null;

    if ($i == -1) $selected1 = 'selected="selected"';
    if ($i == 1)  $selected2 = 'selected="selected"';
    if ($i == 4)  $selected3 = 'selected="selected"';
    if ($i == 8)  $selected4 = 'selected="selected"';
    if ($i == 16) $selected5 = 'selected="selected"';
    if ($i == 32) $selected6 = 'selected="selected"';

    ob_start();

    phpinfo($i);

    $info .= ob_get_contents();

    ob_end_clean();

    preg_match_all("=<body[^>]*>(.*)</body>=siU", $info, $a);

    $php_info = $a[1][0];
    $php_info = preg_replace("/<img (.*?)>/i", "", $php_info);
    $php_info = str_replace("<h2>", "<h3>", $php_info);
    $php_info = str_replace('<div class="center">', '', $php_info);
    $php_info = str_replace('<td class="noBorderB">', '<td class="center">', $php_info);
    $php_info = str_replace('</div>', '', $php_info);
    $php_info = str_replace("<h2>", '<h3 class="center">', $php_info);
    $php_info = str_replace("<h3>", '<h3 style="padding-left:10px;">', $php_info);
    $php_info = str_replace("<a", '<span', $php_info);
    $php_info = str_replace("/a>", '/span>', $php_info);
    $php_info = str_replace("</h2>", "</h3>", $php_info);
    $php_info = str_replace("<h1 class=\"p\">", '<h1 class="center">', $php_info);
    $php_info = str_replace("<h1>", '<h1 class="center">', $php_info);
    $php_info = str_replace("<hr />", '<div class="divider"><span></span></div>', $php_info);
    $php_info = str_replace("<tr>", "<tr>", $php_info);
    $php_info = str_replace("<table border=\"0\" cellpadding=\"3\" width=\"600\">", '<table class="tDefault" >', $php_info);
    $php_info = str_replace("<tr class=\"h\">", "<tr>", $php_info);
    $php_info = str_replace("<tr class=\"v\">", "<tr>", $php_info);
    $php_info = str_replace("<td class=\"e\">", "<td>", $php_info);
    $php_info = str_replace(";", "; ", $php_info);
    $php_info = str_replace(",", ", ", $php_info);
    $php_info = str_replace("font", "span", $php_info);

?>
    <div class="fluid">
        <div class="widget">
            <div class="whead">
                <h6><?php echo PHPINFO; ?></h6>
                <div class="clear"></div>
            </div>
            <form method="post" action="index.php?file=Admin&amp;page=phpinfo">
                <div class="formRow">
                    <div class="grid2"><label><?php echo SEE_INFOS; ?> : </label></div>
                    <div class="grid9 noSearch">
                        <select name="what" class="select" onchange="submit();">
                            <option value="-1" <?php echo $selected1; ?>><?php echo ALL_F; ?></option>\n"
                            <option value="1" <?php echo $selected2; ?>><?php echo GLOBALES; ?></option>\n"
                            <option value="4" <?php echo $selected3; ?>><?php echo CONFIG; ?></option>\n"
                            <option value="8" <?php echo $selected4; ?>><?php echo MODULES; ?></option>\n"
                            <option value="16" <?php echo $selected5; ?>><?php echo ENVIRONMENT; ?></option>\n"
                            <option value="32" <?php echo $selected6; ?>><?php echo VARS; ?></option>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <div>
                <?php echo $php_info; ?>
            </div>
            <div class="body center">
                <a class="buttonM bDefault" href="index.php?file=Admin"><?php echo BACK; ?></a>
            </div>
        </div>
    </div>
<?php
}
else{
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
}

adminFooter();
?>
