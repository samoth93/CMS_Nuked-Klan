<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $nuked;
include("modules/Admin/design.php");

$hasAdmin = nkHasAdmin();

if ($hasAdmin === true)
{
    function main()
    {
        global $user, $nuked, $language;

        echo"<script type=\"text/javascript\">\n"
        ."<!--\n"
        ."\n"
        . "function delfile()\n"
        . "{\n"
        . "if (confirm('".PURGE_SQL_ERRORS . " ? " . CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Admin&page=erreursql&op=delete';}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . SQL_ERRORS_MANAGEMENT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Erreursql.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"javascript:delfile();\">" . PURGE_SQL_ERRORS . "</a></b></div><br />\n";

        echo "<table><tr><td><b>" . DATE . "</b>\n"
        ."</td><td><b>" . URL . "</b>\n"
        ."</td><td><b>" . INFO . "</b>\n"
        ."</td></tr>\n";

        $sql = mysql_query("SELECT date, lien, texte  FROM " . $nuked['prefix'] . "_erreursql ORDER BY date DESC");
        while (list($date, $lien, $texte) = mysql_fetch_array($sql))
        {
            $date = nkDate($date);

            echo "<tr><td>" . $date . "</td>\n"
            . "<td><a href=\"" . $nuked['url'] . $lien . "\">" . $lien . "</a></td>\n"
            . "<td>" . $texte . "</td></tr>\n";
        }
        echo "</table><div style=\"text-align: center;\"><br /><a class=\"button\" href=\"index.php?file=Admin\"><b>" . BACK . "</b></a></div></form><br /></div></div>\n";
    }

    function delete()
    {
        global $user, $nuked;

        if (nkHasGod())
        $sql3 = mysql_query("DELETE FROM ". $nuked['prefix'] ."_erreursql");

        // Action
        $texteaction = mysql_real_escape_string(ACTION_PURGE_SQL_ERRORS);
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action
        ?>
        <div class="notification success png_bg">
            <div>
                <?php echo SQL_ERRORS_DELETED; ?>
            </div>
        </div>
        <?php
        redirect('index.php?file=Admin&page=erreursql', 1);
    }
    switch ($_REQUEST['op'])
    {
        case 'main':
        admintop();
        main();
        adminfoot();
        break;
        case 'delete':
        admintop();
        delete();
        adminfoot();
        break;
        default:
        admintop();
        main();
        adminfoot();
        break;
    }

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
