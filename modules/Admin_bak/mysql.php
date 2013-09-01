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

$hasAdminAccess = nkAccessAdmin('Mysql');

if ($hasAdminAccess === true)
{
    function main()
    {
        admintop();
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . MYSQL_MANAGEMENT . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=mysql&amp;op=upgrade_db\" enctype=\"multipart/form-data\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td align=\"center\"><b>" . DATABASE . " :</b> <select onchange=\"if (this.selectedIndex != 0) document.location = this.options[this.selectedIndex].value;\">\n"
    . "<option value=\"0\">" . ACTION . "</option>\n"
    . "<option value=\"index.php?file=Admin&amp;nuked_nude=mysql&amp;op=save_db\">" . SAVE_DATABASE . "</option>\n"
    . "<option value=\"index.php?file=Admin&amp;page=mysql&amp;op=optimise\">" . OPTIMIZE_DATABASE . "</option>\n"
    . "</select></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><a class=\"button\" href=\"index.php?file=Admin\"><b>" . BACK . "</b></a></div></form><br /></div>";

        adminfoot();
    }

    function save_db()
    {
        global $global, $nuked, $user;

        require("modules/Admin/class/iam_backup.php");

        $host = $global['db_host'];
        $base = $global['db_name'];
        $login = $global['db_user'];
        $password = $global['db_pass'];

        $backup = new iam_backup($host, $base, $login, $password, false, true, false);
        $backup->perform_backup();
        // Action
        $texteaction = "". ACTION_SAVE_DB ."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action
    }

    function optimise()
    {
        global $nuked, $global, $user;

        /**
        */
        /* Optimize your database                                                      */
        /*                                                                             */
        /* Copyright (c) 2001 by Xavier JULIE (webmaster@securite-internet.org         */
        /* http://www.securite-internet.org                                            */
        /* Adapted and modified for nuked-klan By Specops (specops57@hotmail.com)      */
        /* And MaStErPsX (masterpsx@numericable.fr)                        */
        /*                                         */
        /**
        */

        admintop();
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . MYSQL_MANAGEMENT . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><br />" . OPTIMIZE_DATABASE . " : <b>" . $global['db_name'] . "</b></div>\n"
    . "<br /><div style=\"width:96%\"><table style=\"margin-left: 2%;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 35%;\" align=\"center\"><b>" . TABLE . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . SIZE . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . STATUS . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . SPACE_SAVED . "</b></td></tr>\n";

        $db_clean = $global['db_name'];
        $tot_data = 0;
        $tot_idx = 0;
        $tot_all = 0;
        $total_gain = 0;
        $local_query = 'SHOW TABLE STATUS FROM `' . $global['db_name'] . '`';
        $result = mysql_query($local_query);
        if (is_resource($result) && mysql_num_rows($result))
        {
            while ($row = mysql_fetch_array($result))
            {
                $tot_data = $row['Data_length'];
                $tot_idx = $row['Index_length'];
                $total = $tot_data + $tot_idx;
                $total = $total / 1024 ;
                $total = round ($total, 3);
                $gain = $row['Data_free'];
                $gain = $gain / 1024 ;
                $total_gain += $gain;
                $gain = round ($gain, 3);
                $local_query = "OPTIMIZE TABLE " . $row[0];
                $resultat = mysql_query($local_query);


                if ($gain == 0) $statut = NO_OPTIMIZED;
        else $statut = "<b>" . OPTIMIZED . "</b>";

        echo "<tr>\n"
        . "<td style=\"width: 35%;\">" . $row[0] . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $total . " Kb</td>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $statut . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $gain . " Kb</td></tr>\n";
            }
        }
        echo "</table><br />";

        $total_gain = round ($total_gain, 3);

        echo "<div style=\"text-align: center;\"><br /><b>" . TOTAL . "</b> : " . $total_gain . " Kb</div>\n"
        . "<div style=\"text-align: center;\"><br /><a class=\"button\" href=\"index.php?file=Admin&amp;page=mysql\"><b>" . BACK . "</b></a></div><br /></div></div>\n";

        // Action
        $texteaction = "". ACTION_OPTIMIZE_DB ."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action

        adminfoot();
    }



    switch ($_REQUEST['op'])
    {
        case "save_db":
            save_db();
            break;

        case "optimise":
            optimise();
            break;

        default:
            main();
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
