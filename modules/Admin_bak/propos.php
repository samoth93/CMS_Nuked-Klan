<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die;

global $user;

include('modules/Admin/design.php');

$hasAdmin = nkHasAdmin();

admintop();

if ($hasAdmin === true) {
    ?>
    <div class="content-box"><!-- Start Content Box -->
        <div class="content-box-header"><h3><?php echo ABOUT; ?></h3></div>
        <div class="tab-content" id="tab2">
            <div style="margin:20px">
                <?php echo ABOUT_INFOS; ?>
            </div>
            <div style="text-align: center;margin:20px 0;"><a class="button" href="index.php?file=Admin"><b><?php echo BACK; ?></b></a></div>
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
adminfoot();
?>
