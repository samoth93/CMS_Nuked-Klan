<?php
/**
 * block.php
 *
 * Blocks administration
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

require_once 'modules/Admin/design.php';

$hasAdminAccess = nkAccessAdmin('block');

if ($hasAdminAccess === true) {

    function pageCheckboxMods($mod = null) {
        if($mod === null){
            $mod = array();
        }
        else{
            $mod = explode('|', $mod);
        }

        $dbsMods = "SELECT name FROM ".MODULES_TABLE." ORDER BY name";
        $dbeMods = mysql_query($dbsMods);

        $arrayMods = array();
        while($dataMods = mysql_fetch_assoc($dbeMods)){
            $arrayMods[] = $dataMods['name'];
        }

        $arrayMods[] = USER;

        foreach($arrayMods as $module){
            $checked = null;

            if($module == 'ALL' || in_array($module, $mod) || in_array('ALL', $mod)){
                $checked = ' checked="checked" ';
            }

            if (defined(strtoupper($module.'_MODNAME'))) {
                $moduleTranslated = constant(strtoupper($module.'_MODNAME'));
            }
            else {
                $moduleTranslated = $module;
            }
?>
                <div class="checkboxSliderWrapper">
                    <span><?php echo $moduleTranslated; ?></span>
                    <div class="onoffswitch">
                        <input <?php echo $checked; ?> type="checkbox" name="pages[<?php echo $module; ?>]" class="onoffswitch-checkbox pageCheckbox" id="<?php echo $module; ?>">
                        <label class="onoffswitch-label" for="<?php echo $module; ?>">
                            <div class="onoffswitch-inner"></div>
                            <div class="onoffswitch-switch"></div>
                        </label>
                    </div>
                </div>
<?php
        }
    }

    function displayAccess($arrayPages = 'ALL', $arrayGroups = null){

        if (empty($arrayGroups)) {
            $arrayGroups = null;
        }

        if (empty($arrayPages)) {
            $arrayPages = 'ALL';
        }
?>
        <tr>
            <td align="center">
                <strong><?php echo SELECT_PAGE; ?> :</strong>
                <div class="checkboxSliderWrapper">
                    <span><?php echo ALL_F; ?></span>
                    <div class="onoffswitch">
                        <input type="checkbox" name="pages[<?php echo ALL_F; ?>]" class="onoffswitch-checkbox" id="pageAll">
                        <label class="onoffswitch-label" for="pageAll">
                            <div class="onoffswitch-inner"></div>
                            <div class="onoffswitch-switch"></div>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center">
 <?php
                pageCheckboxMods($arrayPages);
?>
            </td>
        </tr>
<?php
                groupsCheckbox($arrayGroups);
    }

    function add_block(){
        admintop();
?>
        <div class="content-box">
            <div class="content-box-header">
                <h3><?php echo BLOCKS_MANAGEMENT; ?></h3>
                <div style="text-align:right">
                    <a class="adminIcon icon-help" href="help/<?php echo $GLOBALS['language'];?>/block.php" rel="modal" title="<?php echo HELP; ?>">
                        <span><?php echo HELP; ?></span>
                    </a>
                </div>
            </div>
            <div class="tab-content" id="tab2">
                <form method="post" action="index.php?file=Admin&amp;page=block&amp;op=send_block">
                    <table style="margin: auto;text-align: left" cellspacing="0" cellpadding="2" border="0">
                        <tr>
                            <td>
                                <strong><?php echo TITLE; ?> :</strong>
                                <input type="text" name="title" size="40" required="required" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo TYPE; ?> : </strong>
                                <select name="type"><?php
                                    select_block();

                                    echo '<option value="b|module">* '.BLOCK_OF_MODULE.' :</option>';

                                    select_mod();
?>                              </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <input type="submit" value="<?php echo ADD; ?>" />
                            </td>
                        </tr>
                    </table>
                    <p style="text-align:center;margin-top:20px;">
                        <a href="index.php?file=Admin" class="button">
                           <?php echo BACK; ?>
                        </a>
                    </p>
                </form>
            </div>
        </div>
<?php
        adminfoot();
    }

    function send_block(){

        $arrayRequest = array('title', 'type');

        foreach ($arrayRequest as $request) {
            if (isset($_REQUEST[$request]) && !empty($_REQUEST[$request])) {
                ${$request} = $_REQUEST[$request];
            }
            else {
                ${$request} = '';
            }
        }

        $tmp = explode('|', $type);

        $prefix     = $tmp[0];
        $name       = $tmp[1];

        if ($prefix == 'm'){
            $type = 'module';
            $module = $name;
        }
        else{
            $type = $name;
            $module = '';
        }

        $arraySanitize = array('title', 'type', 'module');

        foreach ($arraySanitize as $var) {
            $_CLEAN[$var] = mysql_real_escape_string(${$var});
        }

        $dbiBlock = 'INSERT INTO '.BLOCK_TABLE.' VALUES ("", "0", "", "'.$_CLEAN['module'].'", "'.$_CLEAN['title'].'", "",  "'.$_CLEAN['type'].'", "", "") ';

        $dbeBlock = mysql_query($dbiBlock) or die(mysql_error());

        $lastId = mysql_insert_id();

        // Action
        $texteaction = ACTION_ADD_BLOCK . ': ' . $_CLEAN['title'];
        $sqlaction = mysql_query("INSERT INTO ". $GLOBALS['nuked']['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".time()."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action

        admintop();

?>
        <div class="notification success png_bg">
            <div>
                <?php echo BLOCK_ADDED; ?>
            </div>
        </div>
<?php
        redirect('index.php?file=Admin&page=block&op=edit_block&bid='.$lastId, 2);
        adminfoot();
    }

    function del_block(){

        if (isset($_REQUEST['bid']) && !empty($_REQUEST['bid'])) {
            $_CLEAN['id'] = intval($_REQUEST['bid']);
        }

        $sql2 = mysql_query("SELECT titre FROM " . BLOCK_TABLE . " WHERE bid = '" . $_CLEAN['id'] . "'");
        list($titre) = mysql_fetch_array($sql2);

        $sql = mysql_query("DELETE FROM " . BLOCK_TABLE . " WHERE bid = '" . $_CLEAN['id'] . "'");

        // Action
        $texteaction = ACTION_DEL_BLOCK . ': ' . $titre;
        $sqlaction = mysql_query("INSERT INTO ". $GLOBALS['nuked']['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".time()."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action

        admintop();
?>
        <div class="notification success png_bg">
            <div>
                <?php echo BLOCK_DELETED; ?>
            </div>
        </div>
<?php
        redirect('index.php?file=Admin&page=block', 2);

        adminfoot();
    }

    function select_block(){
        $blocks = array();
        $path = 'Includes/blocks/';
        $handle = @opendir($path);
        $arrayBlocksName = array(
                            'survey'   => SURVEY_MODNAME,
                            'menu'     => MENU,
                            'suggest'  => SUGGEST_MODNAME,
                            'event'    => CALENDAR_MODNAME,
                            'login'    => LOGIN_NAME,
                            'center'   => CENTER,
                            'html'     => HTML,
                            'language' => LANGUAGE,
                            'theme'    => THEME,
                            'counter'  => COUNTER
                            );

        while (false !== ($block = readdir($handle))){
            if ($block != '.' && $block != '..' && $block != 'index.html' && $block != 'block_module.php'){
                if (substr($block, -3, 3) == 'php') {
                    $block = substr($block, 6, -4);

                    if(array_key_exists($block, $arrayBlocksName)){
                        $blockName = $arrayBlocksName[$block];
                    }
                    else $blockName = $block;

                    array_push($blocks, $blockName . '|' . $block);
                }
            }
        }
        closedir($handle);
        natcasesort($blocks);

        foreach($blocks as $block){
            $temp = explode('|', $block);
            echo '<option value="b|' . $temp[1] . '">' . $temp[0] . '</option>',"\n";
        }
    }

    function select_mod($mod = null){
        $modules = array();
        $handle = opendir('modules');
        while (false !== ($f = readdir($handle))){
            if ($f != '.' && $f != '..' && $f != 'CVS' && $f != 'index.html'  && !preg_match('`[.]`', $f)){
                if (defined($f.'_MODNAME')) {
                    $modname = constant($f.'_MODNAME');
                }
                else {
                    $modname = $f;
                }

                array_push($modules, $modname . '|' . $f);
            }
        }

        closedir($handle);
        natcasesort($modules);

        foreach($modules as $value){
                $temp = explode('|', $value);

                if ($mod == $temp[1]){
                    $checked = 'selected="selected"';
                }
                else{
                    $checked = '';
                }

                if (is_file('modules/' . $temp[1] . '/blok.php')){
                    echo '<option value="m|' . $temp[1] . '" ' . $checked . '>' . $temp[0] . '</option>',"\n";
                }
        }
    }

    function edit_block(){

        if (isset($_REQUEST['bid']) && !empty($_REQUEST['bid'])) {
            $id = intval($_REQUEST['bid']);
        }

        admintop();

        $dbsBlock = 'SELECT bid AS id, active, position, titre AS title, module, content, type, groups, pages
                     FROM '.BLOCK_TABLE.'
                     WHERE bid = "'.$id.'" ';
        $dbeBlock = mysql_query($dbsBlock);

        $block = mysql_fetch_assoc($dbeBlock);

        $block['title'] = printSecuTags($block['title']);

        $arrayBlocksCenter = array('center');
        $arrayBlocksSide   = array('counter', 'event', 'language', 'login', 'menu', 'roster', 'suggest', 'survey', 'theme');

        if (in_array($block['type'], $arrayBlocksCenter)) {
            $arrayPosition = array(3 => CENTER, 4 => BOTTOM, 0 => DISABLE);
        }
        else if (in_array($block['type'], $arrayBlocksSide)) {
            $arrayPosition = array(1 => LEFT, 2 => RIGHT, 0 => DISABLE);
        }
        else if (function_exists('block_header') && function_exists('block_footer')) {
            $arrayPosition = array(1 => LEFT, 2 => RIGHT, 3 => CENTER, 4 => BOTTOM, 5 => HEADER, 6 => FOOTER, 0 => DISABLE);
        }
        else {
            $arrayPosition = array(1 => LEFT, 2 => RIGHT, 3 => CENTER, 4 => BOTTOM, 0 => DISABLE);
        }


        $checked1 = $checked2 = $checked3 = $checked4 = $checked5 = $checked6 = $checked0 = null;

        if ($block['active'] == 1) {
            $checked1 = ' checked="checked" ';
        }
        else if ($block['active'] == 2) {
            $checked2 = ' checked="checked" ';
        }
        else if ($block['active'] == 3) {
            $checked3 = ' checked="checked" ';
        }
        else if ($block['active'] == 4) {
            $checked4 = ' checked="checked" ';
        }
        else if ($block['active'] == 5) {
            $checked5 = ' checked="checked" ';
        }
        else if ($block['active'] == 6) {
            $checked6 = ' checked="checked" ';
        }
        else {
            $checked0 = ' checked="checked" ';
        }

?>
        <div class="content-box">
            <div class="content-box-header">
                <h3><?php echo BLOCKS_MANAGEMENT; ?></h3>
                <div style="text-align:right">
                    <a class="adminIcon icon-help" href="help/<?php echo $GLOBALS['language'];?>/block.php" rel="modal" title="<?php echo HELP; ?>">
                        <span><?php echo HELP; ?></span>
                    </a>
                </div>
            </div>
            <div class="tab-content" id="tab2">
                <form method="post" action="index.php?file=Admin&amp;page=block&amp;op=modif_block">
                    <table style="margin: auto;text-align: left" cellspacing="0" cellpadding="2" border="0">
                        <tr>
                            <td>
                                <strong><?php echo TITLE; ?> :</strong>
                                <input type="text" name="title" size="40" required="required" value="<?php echo $block['title']; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo POSITION; ?> :</strong>
<?php
                                    foreach ($arrayPosition as $key => $value) {
                                        echo '<input class="screenPositionInput" type="radio" id="active'.$key.'" value="'.$key.'" '.${'checked'.$key}.' name="active" />
                                              <label class="screenPosition" id="screenPosition'.$key.'" for="active'.$key.'" ><span>'.$value.'</span></label>';
                                    }
?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo ORDER; ?> :</strong>
                                <input type="text" name="position" size="2" value="<?php echo $block['position']; ?>" />
                            </td>
                        </tr>

<?php
                        include_once('Includes/blocks/block_' . $block['type'] . '.php');

                        $function = 'edit_block_' . $block['type'];

                        if (function_exists($function)) {
                            $function($block);
                        }

                        echo displayAccess($block['pages'], $block['groups']);
?>
                        <tr>
                            <td style="text-align:center;">
                                <input type="hidden" name="type" value="<?php echo $block['type']; ?>" />
                                <input type="hidden" name="bid" value="<?php echo $id; ?>" />
                                <input type="submit" value="<?php echo MODIFY; ?>" />
                            </td>
                        </tr>
                    </table>
                </form>
                <p style="text-align:center;margin-top:20px;">
                    <a href="index.php?file=Admin&amp;page=block" class="button">
                       <?php echo BACK; ?>
                    </a>
                </p>
            </div>
        </div>
<?php

        adminfoot();
    }

    function modif_block(){

        admintop();

        $data = $_REQUEST;

        $arrayBlocks = array('center', 'counter', 'event', 'html', 'language', 'login', 'menu', 'module', 'roster', 'rss', 'suggest', 'survey', 'theme');

        if (!in_array($data['type'], $arrayBlocks)) {
            $data['type'] = 'module';
        }

        $function = 'modif_advanced_' . $data['type'];

        require_once 'Includes/blocks/block_'.$data['type'].'.php';

        if (function_exists($function)){
            $data = $function($data);
        }

        $arrayRequest = array('type', 'pages', 'content', 'title', 'module', 'active', 'position', 'groups', 'bid');

        foreach ($arrayRequest as $request) {
            if (isset($data[$request]) && !empty($data[$request])) {
                ${$request} = $data[$request];
            }
            else{
                ${$request} = '';
            }
        }

        if (isset($pages[ALL_F])) {
            unset($pages[ALL_F]);
        }

        if (isset($groups[ALL])) {
            unset($groups[ALL]);
        }

        if (is_array($pages)) {
            $pages = array_map('mysql_real_escape_string', $pages);
        }

        if (is_array($groups)) {
            $groups = array_map('mysql_real_escape_string', $groups);
        }

        $pagesAuthorized = $groupsAuthorized = null;

        if (!empty($pages)) {
            $pagesAuthorized = implode('|', array_keys($pages));
        }

        if (!empty($groups)) {
            $groupsAuthorized = implode('|', array_keys($groups));
        }

        if(!isset($content)){
            $content = '';
        }
        else if(!empty($content) && is_array($content)) {
            $content = implode('|', $content);
        }

        $arraySanitize = array('bid', 'active', 'position', 'module', 'title', 'content', 'type');

        foreach ($arraySanitize as $var) {
            $_CLEAN[$var] = mysql_real_escape_string(${$var});
        }

        if (!empty($module)){
            list ($t, $module) = explode ('|', $module);
        }
        else{
            $module = '';
        }

        $dbsBlock = 'UPDATE '.BLOCK_TABLE.'
                     SET active = "'.$_CLEAN['active'].'", position = "'.$_CLEAN['position'].'", module = "'.$_CLEAN['module'].'",
                         titre = "'.$_CLEAN['title'].'", content = "'.$_CLEAN['content'].'", type = "'.$_CLEAN['type'].'",
                         groups = "'.$groupsAuthorized.'", pages = "'.$pagesAuthorized.'"
                     WHERE bid = "'.$_CLEAN['bid'].'" ';
        $dbeBlock = mysql_query($dbsBlock);

        // Action
        $texteaction = ACTION_MODIFY_BLOCK . ': ' . $_CLEAN['title'];
        $sqlaction = mysql_query("INSERT INTO ". $GLOBALS['nuked']['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".time()."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action

?>
        <div class="notification success png_bg">
            <div>
                <?php echo BLOCK_EDITED; ?>
            </div>
        </div>
<?php
        redirect('index.php?file=Admin&page=block', 2);

        adminfoot();
    }

    function modif_position_block($bid, $method)
    {
        global $nuked;

        admintop();

        $sql2 = mysql_query("SELECT titre, position FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
        list($titre, $position) = mysql_fetch_array($sql2);

        if ($method == 'up')
        {
            $position--;
        }
        else if ($method == 'down')
        {
            $position++;
        }

        if ($position < 0)
        {
            $position = 0;
        }

        $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET position = '" . $position . "' WHERE bid = '" . $bid . "'");
         // Action
        $texteaction = ACTIONPOSBLOCK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$GLOBALS['user']['id']."', '".$texteaction."')");
        //Fin action

        echo '<div class="notification success png_bg">',"\n"
        . '<div>',"\n"
        . '' . BLOCK_EDITED . '',"\n"
        . '</div>',"\n"
        . '</div>',"\n"
        . '<script>',"\n"
        . "setTimeout('screen()','3000');\n"
        . "function screen() { \n"
        . "screenon('index.php', 'index.php?file=Admin&page=block');\n"
        . "}\n"
        . "</script>\n";

        adminfoot();
    }

    function main(){

        admintop();

?>
        <script type="text/javascript">
            function delBlock(titre, id) {
                if (confirm('<?php echo DELETE_BLOCK; ?> '+titre+' ! <?php echo CONFIRM; ?>')) {
                    document.location.href = 'index.php?file=Admin&page=block&op=del_block&bid='+id;
                }
            }
        </script>
<?php
        $dbsGroups = 'SELECT id, nameGroup
                      FROM '.GROUP_TABLE;
        $dbeGroups = mysql_query($dbsGroups);

        $arrayGroups = array();

        while ($data = mysql_fetch_assoc($dbeGroups)) {
            if (defined($data['nameGroup'])) {
                $arrayGroups[$data['id']] = constant($data['nameGroup']);
            }
            else {
                $arrayGroups[$data['id']] = $data['nameGroup'];
            }
        }

        $dbsBlocks = 'SELECT bid AS id, active, position, titre AS title, module, content, type, groups
                      FROM '.BLOCK_TABLE.'
                      ORDER BY active, position';
        $dbeBlocks = mysql_query($dbsBlocks);

        $arrayBlocks = array();

        $arrayPosition = array(0 => DISABLE, 1 => LEFT, 2 => RIGHT, 3 => CENTER, 4 => BOTTOM);

        while ($data = mysql_fetch_assoc($dbeBlocks)) {
            foreach ($data as $field => $value) {
                if ($field != 'id') {
                    if ($field == 'title') {
                    $arrayBlocks[$data['id']][$field] = printSecuTags($value);
                    }
                    elseif ($field == 'active'){
                        $arrayBlocks[$data['id']][$field] = $arrayPosition[$value];
                    }
                    elseif ($field == 'groups'){
                        $arrayBlocks[$data['id']][$field] = explode('|', $value);
                    }
                    else{
                      $arrayBlocks[$data['id']][$field] = $value;
                    }
                }
            }
        }
?>
        <div class="content-box" style="max-width:900px;">
            <div class="content-box-header">
                <h3><?php echo BLOCKS_MANAGEMENT; ?></h3>
                <div style="text-align:right">
                    <a class="adminIcon icon-help" href="help/<?php echo $GLOBALS['language'];?>/block.php" rel="modal" title="<?php echo HELP; ?>">
                        <span><?php echo HELP; ?></span>
                    </a>
                </div>
            </div>
            <div id="tab2" class="tab-content">
                <div class="iconLinkWrapper">
                    <a class="iconLinkAdd" href="index.php?file=Admin&amp;page=block&amp;op=add_block">
                        <span><?php echo BLOCK_ADD; ?></span>
                    </a>
                </div>
                <table style="width:100%;border:none;" cellspacing="1" cellpadding="2">
                    <tr>
                        <td style="width: 20%;text-align:center;">
                            <strong><?php echo TITLE; ?></strong>
                        </td>
                        <td style="width: 15%;text-align:center;">
                            <strong><?php echo POSITION; ?></strong>
                        </td>
                        <td style="width: 15%;text-align:center;">
                            <strong><?php echo ORDER; ?></strong>
                        </td>
                        <td style="width: 15%;text-align:center;">
                            <strong><?php echo TYPE; ?></strong>
                        </td>
                        <td style="width: 10%;text-align:center;">
                            <strong><?php echo GROUPS; ?></strong>
                        </td>
                        <td style="width: 10%;text-align:center;">
                            <strong><?php echo EDIT; ?></strong>
                        </td>
                        <td style="width: 15%;text-align:center;">
                            <strong><?php echo DELETE; ?></strong>
                        </td>
                    </tr>
<?php
            foreach ($arrayBlocks as $blockId => $block):
?>
                <tr>
                    <td style="width: 20%;">
                        <?php echo $block['title']; ?>
                    </td>
                    <td style="width: 15%;text-align:center;">
                        <?php echo $block['active']; ?>
                    </td>
                    <td style="width: 15%;text-align:center;">
                        <?php echo $block['position']; ?>
                    </td>
                    <td style="width: 15%;text-align:center;">
                        <?php echo $block['type']; ?>
                    </td>
                    <td style="width: 10%;text-align:center;">
                        <div class="iconGroupList adminIcon icon-groups">
                            <ul>
<?php
                                if (is_array($block['groups'])) {
                                    foreach ($block['groups'] as $groupId) {
                                        if (array_key_exists($groupId, $arrayGroups)) {
                                            echo '<li>'.$arrayGroups[$groupId].'</li>';
                                        }
                                    }
                                }
?>
                            </ul>
                        </div>
                    </td>
                    <td style="width: 10%;text-align:center;">
                        <a class="adminIcon icon-edit" href="index.php?file=Admin&amp;page=block&amp;op=edit_block&amp;bid=<?php echo $blockId; ?>" title="<?php echo EDIT; ?>">
                            <span><?php echo EDIT; ?></span>
                        </a>
                    </td>
                    <td style="width: 15%;text-align:center;">
                        <a class="adminIcon icon-delete" href="javascript:delBlock('<?php echo $block['title']; ?>','<?php echo $blockId; ?>');" title="<?php echo DELETE; ?>">
                            <span><?php echo DELETE; ?></span>
                        </a>
                    </td>
                </tr>
<?php
            endforeach;

            if (count($arrayBlocks) == 0) {
?>
                <tr>
                    <td colspan="7" style="text-align:center;"><?php echo NO_BLOCKS;?></td>
                </tr>
<?php
            }
?>
                </table>
                <p style="text-align:center;margin-top:20px;">
                    <a href="index.php?file=Admin" class="button">
                       <?php echo BACK; ?>
                    </a>
                </p>
            </div>
        </div>
<?php
        adminfoot();
    }

    switch ($_REQUEST['op']){
        case "edit_block":
            edit_block();
            break;
        case "add_block":
            add_block();
            break;
        case "del_block":
            del_block();
            break;
        case "send_block":
            send_block();
            break;
        case "modif_position_block":
            modif_position_block();
            break;
        case "modif_block":
            modif_block();
            break;
        case "main":
            main();
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
