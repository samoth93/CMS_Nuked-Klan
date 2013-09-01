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

global $user, $nuked, $language;

include('modules/Admin/design.php');

$hasAdmin = nkHasAdmin();

if ($hasAdmin === true)
{
    function main()
    {
        global $user, $nuked;

        $_SESSION['admin'] = false;

        ?>

            <!-- Page Head -->
            <h2><?php echo GOODBYE; ?> <?php echo $GLOBALS['user']['nickName']; ?></h2>

            <?php
            if ($_SESSION['admin'] == false)
            {
            // Action
            $texteaction = mysql_real_escape_string(ACTION_DISCONNECT);
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
            //Fin action
            ?>
            <div class="notification success png_bg">
                <div>
                    <?php echo DISCONNECT_SUCCESS; ?>
                </div>
            </div>
            <?php
            redirect("index.php", 2);
            }
            else
            {
            ?>
            <div class="notification error png_bg">
                <div>
                    <?php echo DISCONNECT_FAIL; ?>
                </div>
            </div>
                <?php
        redirect("index.php?file=Admin", 2);
            }
    }
    switch ($_REQUEST['op'])
    {
        case "main":
    admintop();
        main();
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
