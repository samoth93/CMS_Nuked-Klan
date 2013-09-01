<?php
/**
 * index.php (modules/Admin)
 *
 * Admin root
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

// Redirection des visiteurs sur un 404
if(nkHasVisitor()){
    header('location: index.php?file=404');
    exit();
}

// Inclusion du layout de l'administration
require_once 'modules/Admin/views/layout.php';

// On affiche le header du layout
adminHeader();

// Check si l'user est un admin
if (nkHasAdmin()) {
    require_once 'modules/Admin/pages/home.php';
}
else {
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

// On affiche le footer du layout
adminFooter();

}
?>
