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

require_once 'modules/Admin/design.php';

$hasAdminAccess = nkAccessAdmin('Mods');

admintop();

if ($hasAdminAccess === true) {
    function main() {
?>
        <div class="content-box" style="max-width:650px;">
            <div class="content-box-header">
                <h3><?php echo MODULES_MANAGEMENT; ?></h3>
                <div style="text-align:right">
                    <a class="adminIcon icon-help" href="help/<?php echo $GLOBALS['language'];?>/modules.php" rel="modal" title="<?php echo HELP; ?>">
                        <span><?php echo HELP; ?></span>
                    </a>
                </div>
            </div>
            <div id="tab2" class="tab-content">
                <form method="POST" action="index.php?file=Admin&amp;page=modules&amp;op=sendForm">
                <table style="width:100%;border:none;" cellspacing="1" cellpadding="2">
                    <tr>
                        <td style="width: 60%;text-align:center;">
                            <strong><?php echo NAME; ?></strong>
                        </td>
                        <td style="width: 40%;text-align:center;">
                            <strong><?php echo STATUS; ?></strong>
                        </td>
                    </tr>

<?php
        $arrayModules = array();

        $dbsModules = 'SELECT id, name, status
                       FROM '.MODULES_TABLE.' ';
        $dbeModules = mysql_query($dbsModules);
        while ($mods = mysql_fetch_assoc($dbeModules)) {    
            if (defined(strtoupper($mods['name']).'_MODNAME')) {
                $arrayModules[constant(strtoupper($mods['name']).'_MODNAME')] = $mods;
            }
            else {
                $arrayModules[$mods['name']] = $mods;
            }
        }
        ksort($arrayModules);
        
        foreach ($arrayModules as $moduleName => $moduleVars):
            
            $checked = null;
            
            if ($moduleVars['status'] == 'on') {
                $checked = ' checked="checked" ';
            }
?>
            <tr>
                <td style="width: 60%;text-align:center;">
                    <?php echo $moduleName; ?>
                </td>
                <td style="width: 40%;text-align:center;">
                    <div class="checkboxSliderWrapperAlone">
                        <div class="onoffswitch">
                            <input <?php echo $checked; ?> type="checkbox" name="modules[<?php echo $moduleVars['id']; ?>]" class="onoffswitch-checkbox" id="<?php echo $moduleVars['name']; ?>">
                            <label class="onoffswitch-label" for="<?php echo $moduleVars['name']; ?>">
                                <div class="onoffswitch-inner"></div>
                                <div class="onoffswitch-switch"></div>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
<?php
        endforeach;
?>
        <tr>
            <td colspan="2" style="text-align:center;">
                <input type="hidden" name="modules[none]" value="none" />
                <input type="submit" value="<?php echo MODIFY; ?>" />
            </td>
        </tr>
        </table>
        </form>
        <p style="text-align:center;margin-top:20px;">
            <a href="index.php?file=Admin" class="button">
               <?php echo BACK; ?>
            </a>
        </p>
<?php
    }
    
    function sendForm() {
        if (array_key_exists('modules', $_REQUEST) && !empty($_REQUEST['modules'])) {
            $modules = $_REQUEST['modules'];
        }
        else {
            $modules = null;
        }
        
        $arrayUpdate = array();        
        $mods        = array();

        $dbsModules  = 'SELECT id, status
                        FROM '.MODULES_TABLE.' ';
        $dbeModules  = mysql_query($dbsModules) or die(mysql_error());
        while ($data = mysql_fetch_assoc($dbeModules)) {   
            $mods[]  = array('id' => $data['id'], 'status' => $data['status']);
        }
        
        foreach ($mods as $mod) {
            if (array_key_exists($mod['id'], $modules) && $mod['status'] != 'on') {
                $arrayUpdate[] = '("'.$mod['id'].'", "on")';
            }
            else if (!array_key_exists($mod['id'], $modules) && $mod['status'] != 'off') {
                $arrayUpdate[] = '("'.$mod['id'].'", "off")';
            }
        }
                
        if (count($arrayUpdate) > 0){
            $insertValues = implode(', ',$arrayUpdate);
            
            mysql_query('INSERT INTO '.MODULES_TABLE.' (id, status) VALUES '.$insertValues.' 
                         ON DUPLICATE KEY UPDATE id = VALUES(id), status = VALUES(status);');
                         
            // Action
            $texteaction = ACTION_EDIT_MODULES;
            $sqlaction = mysql_query("INSERT INTO ". $GLOBALS['nuked']['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".time()."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
            //Fin action    
            
        }       
?>
        <div class="notification success png_bg">
            <div>
                <?php echo MODULE_EDITED; ?>
            </div>
        </div>
<?php
        redirect('index.php?file=Admin&page=modules', 2);
        
    }

    switch ($_REQUEST['op']) {
        case 'sendForm':
            sendForm();
            break;
        default:
            main();
            break;
    }
}
else{
?>
    <div class="notification error png_bg">
        <div>
            <div style="text-align: center;">
                <?php echo _ZONEADMIN; ?>
            </div>
        </div>
    </div>
    <div style="text-align:center;">
        <a class="button" href="javascript:history.back()">
            <b><?php echo _BACK; ?></b>
        </a>
    </div>
<?php
}
adminfoot();

?>
