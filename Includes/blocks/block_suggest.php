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

function affich_block_suggest($blok){
    global $user, $nuked;

    $modules = array();
    $path = 'modules/Suggest/modules/';
    $handle = opendir($path);
    while (false !== ($mod = readdir($handle))){
        if ($mod != '.' && $mod != '..'){
            $mod = str_replace('.php', '', $mod);

            if (defined(strtoupper($mod).'MODNAME')) {
                $modname = constant(strtoupper($mod).'MODNAME');
            }
            else {
                $modname = $mod;
            }

            array_push($modules, $modname . '|' . $mod);
        }
    }

    closedir($handle);
    natcasesort($modules);
    $size = count($modules);
    for($i=0; $i<$size; $i++){
        $temp = explode('|', $modules[$i]);

        $sql = mysql_query('SELECT id FROM ' . SUGGEST_TABLE . ' WHERE module = \'' . $temp[1] . '\' ');
        $nb_sug = mysql_num_rows($sql);

        $hasAccessMod = nkAccessModule($temp[1]);
        $hasAccessAdmin = nkAccessAdmin($temp[1]);

        if ($hasAccessMod === true && nkIsModEnabled($temp[1])){
            $blok['content'] .= '&nbsp;<b><big>&middot;</big></b>&nbsp;<a href="index.php?file=Suggest&amp;module=' . $temp[1] . '">' . $temp[0] . '</a>';
        }

        if ($hasAccessAdmin === true && nkIsModEnabled($temp[1])){
            if ($nb_sug > 0){
                $blok['content'] .= '&nbsp;(<a href="index.php?file=Suggest&amp;page=admin">' . $nb_sug . '</a>)<br />'."\n";
            }
            else{
                $blok['content'] .= '&nbsp;(' . $nb_sug . ')<br />'."\n";
            }
        }
        else if ($hasAccessMod === true && nkIsModEnabled($temp[1])){
            $blok['content'] .= '<br />'."\n";
        }
    }

    return $blok;
}


?>
