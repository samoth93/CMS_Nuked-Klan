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

defined('INDEX_CHECK') or die ("You can't run this file alone.");

function affich_block_center($block){

    $arrayModules = explode('|', $block['content']);
    $module1 = $arrayModules[0];
    $module2 = $arrayModules[1];

    if (defined(strtoupper($module1).'_MODNAME')) {
        $modName1 = constant(strtoupper($module1).'_MODNAME');
    }
    else {
        $modName1 = $module1;
    }

    if (defined(strtoupper($module2).'_MODNAME')) {
        $modName2 = constant(strtoupper($module2).'_MODNAME');
    }
    else {
        $modName2 = $module2;
    }

    $block['content'] = '';

    if (!empty($module1)){
        $block['content'] .= '<div style="width: 48%; float: left" ><h3 style="text-align: center">' . $modName1 . '</h3>'."\n";
        $block['content'] .= inc_bl1($module1);
        $block['content'] .= '</div>'."\n";
    }

    if (!empty($module2)){
        $block['content'] .= '<div style="float: left; width: 4%">&nbsp;</div><div style="float: left; width: 48%"><h3 style="text-align: center">' . $modName2 . '</h3>'."\n";
        $block['content'] .= inc_bl2($module2);
        $block['content'] .= '</div>'."\n";
    }

    $block['content'] .= '<div style="clear: both"></div>'."\n";
    return $block;
}

function edit_block_center($block){

    $arrayModules = explode("|", $block['content']);

    $module1 = $module2 = null;

    if (array_key_exists(0, $arrayModules)) {
        $module1 = $arrayModules[0];
    }

    if (array_key_exists(1, $arrayModules)) {
        $module2 = $arrayModules[1];
    }

?>
                            <tr>
                            <td>
                                <strong><?php echo MODULE.' 1'; ?> :</strong>
                                <select name="content[1]">
<?php
                                    select_module($module1);
?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo MODULE.' 2'; ?> :</strong>
                                <select name="content[2]">
<?php
                                    select_module($module2);
?>
                                </select>
                            </td>
                        </tr>
<?php
}

function modif_advanced_center($data){
    if (!empty($data['content'][1]) && !empty($data['content'][2])) {
        $sep = '|';
    }
    else{
        $sep = '';
    }

    $content = $data['content'][1].$sep.$data['content'][2];
    $data['content'] = $content;
    return $data;
}

function select_module($mod){
        $handle = opendir('modules');
        while (false !== ($f = readdir($handle))){
            if ($f != '.' && $f != '..' && $f != 'CVS' && $f != 'index.html'  && !preg_match("/\./", $f)){
                if ($mod == $f) $checked = 'selected="selected"';
                else $checked = '';

                if (defined(strtoupper($f).'_MODNAME')) {
                    $modName = constant(strtoupper($f).'_MODNAME');
                }

                if (is_file('modules/' . $f . '/blok.php')) echo '<option value="' , $f , '" ' , $checked , '>' , $modName , '</option>',"\n";
            }
        }
        closedir($handle);
    }

function inc_bl1($mod1){
    ob_start();
    $bid = null;
    include_once 'modules/'.$mod1.'/blok.php';
    $blok_content = ob_get_contents();
    ob_end_clean();
    return $blok_content;

}

function inc_bl2($mod2){
    ob_start();
    $bid = null;
    include_once 'modules/'.$mod2.'/blok.php';
    $blok_content = ob_get_contents();
    ob_end_clean();
    return $blok_content;
}
?>
