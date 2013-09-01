<?php
/**
 * block_center.php
 *
 * Display and Admin of block center
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

function affich_block_counter($block){
    global $nuked;

    $sql = mysql_query('SELECT count FROM '.STATS_TABLE.' WHERE type = "pages" ');
    $visites = 0;

    while (list($count) = mysql_fetch_array($sql)){
        $visites = $visites + $count;
    }

    $nb_digits = max(strlen($visites), 8);
    $visites = substr('0000000000' . $visites, - $nb_digits);

    for ($i = 0; $i <= 9; $i++) {
        $visites = str_replace($i, '<img src="modules/Stats/images/compteur/'.$i.'.jpg" alt="" />', $visites);
    }

    $block['content'] = '<div style="text-align: center; padding-top: 10px">' . $visites . '</div><br />'."\n";

    return $block;
}

?>
