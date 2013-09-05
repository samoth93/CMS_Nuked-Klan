<?php
/**
 * Smilies.php
 *
 * Smilies Management
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');


// Inclusion du layout de l'administration
require_once 'modules/Admin/views/layout.php';

$hasAdminAccess = nkAccessAdmin('Smilies');

adminHeader();

if ($hasAdminAccess === true){

    function main(){
?>
        <script type="text/javascript">
            function delSmiley(name, id) {
                if (confirm('<?php echo DELETE_SMILEY; ?> '+name+' ! <?php echo CONFIRM; ?>')) {
                    document.location.href = 'index.php?file=Admin&page=smilies&op=delSmiley&id='+id;
                }
            }
        </script>
        <ul class="middleNavR">
            <li>
                <a class="tipN" href="index.php?file=Admin&amp;page=smilies&amp;op=formSmiley" original-title="<?php echo SMILEY_ADD; ?>">
                    <span class="nkIcons icon-add"></span>
                </a>
            </li>
        </ul>
        <div class="widget">
            <div class="whead">
                <h6><?php echo SMILIES_MANAGEMENT; ?></h6>
                <div class="clear"></div>
            </div>
            <table class="tDefault">
                <thead>
                    <tr>
                        <td class="center">
                            <strong><?php echo SMILEY; ?></strong>
                        </td>
                        <td class="center">
                            <strong><?php echo NAME; ?></strong>
                        </td>
                        <td class="center">
                            <strong><?php echo CODE; ?></strong>
                        </td>
                        <td class="center">
                            <strong><?php echo ACTIONS; ?></strong>
                        </td>
                    </tr>
                </thead>
                <tbody>
<?php
        $dbsSmilies = 'SELECT id, code, url, name
                       FROM '.SMILIES_TABLE.'
                       ORDER BY id';
        $dbeSmilies = mysql_query($dbsSmilies);

        while ($smiley = mysql_fetch_assoc($dbeSmilies)):
?>
                    <tr>
                        <td class="center">
                            <img style="max-width:100px;max-height:100px;" src="images/icones/<?php echo $smiley['url']; ?>" alt="" title="<?php echo $smiley['url']; ?>" />
                        </td>
                        <td class="center">
                            <?php echo htmlentities($smiley['name']); ?>
                        </td>
                        <td class="center">
                            <?php echo $smiley['code']; ?>
                        </td>
                        <td class="center">
                            <a class="tablectrl_medium bDefault tipS nkIcons icon-edit" href="index.php?file=Admin&amp;page=smilies&amp;op=formSmiley&amp;id=<?php echo $smiley['id']; ?>" original-title="<?php echo EDIT; ?>"></a>
                            <a class="tablectrl_medium bDefault tipS nkIcons icon-delete" href="javascript:delSmiley('<?php echo mysql_real_escape_string($smiley['name']); ?>', '<?php echo $smiley['id']; ?>');" original-title="<?php echo DELETE; ?>"></a>
                        </td>
                    </tr>
<?php
        endwhile;
?>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="body center">
                            <a class="buttonM bDefault" href="index.php?file=Admin"><?php echo BACK; ?></a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
<?php
    }

    function formSmiley(){

        $submitValue = ADD;

        $dataSmiley  = array('name' => null, 'code' => null, 'url' => null);

        if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $id = intval($_REQUEST['id']);

            $dbsSmiley  = 'SELECT name, code, url, count(id) AS count
                          FROM '.SMILIES_TABLE.'
                          WHERE id = "'.$id.'" ';
            $dbeSmiley  = mysql_query($dbsSmiley);

            $dataSmiley = mysql_fetch_assoc($dbeSmiley);

            if ($dataSmiley['count'] == 1) {
                $submitValue = MODIFY;
            }
            else {

            }
        }
?>
        <script type="text/javascript">
            function updateSmiley(image) {
                document.getElementById('smiley').src = 'images/icones/'+image;
            }
        </script>
        <ul class="middleNavR">
            <li>
                <a class="tipN" rel="modal" data-title="<?php echo HELP; ?>" data-close="<?php echo CLOSE; ?>" href="help/<?php echo $GLOBALS['language'];?>/smilies.php" original-title="<?php echo HELP; ?>">
                    <span class="nkIcons icon-help"></span>
                </a>
            </li>
        </ul>
        <div class="widget fluid">
            <div class="whead">
                <h6><?php echo SMILEY_ADD; ?></h6>
                <div class="clear"></div>
            </div>
            <form method="post" class="validateForm" action="index.php?file=Admin&amp;page=smilies&amp;op=sendSmiley" enctype="multipart/form-data">
                <div class="formRow">
                    <div class="grid2"><label><?php echo NAME; ?> :</label></div>
                    <div class="grid8"><input type="text" name="name" id="smiliesName" class="validate[required, custom[codeSmiley]]" value="<?php echo $dataSmiley['name']; ?>" /></div>
                    <div class="clear"></div>
                </div>
                <div class="formRow">
                    <div class="grid2"><label><?php echo CODE; ?> :</label></div>
                    <div class="grid8"><input type="text" id="smiliesCode" class="validate[required, custom[codeSmiley]]" name="code" value="<?php echo $dataSmiley['code']; ?>" /></div>
                    <div class="clear"></div>
                </div>
                <div class="formRow">
                    <div class="grid2"><label><?php echo SMILEY; ?> :</label></div>
                    <div class="grid8 noSearch">
                        <select name="url" class="select" onchange="updateSmiley(this.options[selectedIndex].value);">
<?php
                            $arraySmilies = array();
                            $arrayBadFiles = array('.', '..', 'index.html', '.htaccess', '.htpasswd', 'Thumbs.db');
                            $handle = opendir('images/icones');

                            while (false != ($smiley = readdir($handle))) {
                                if (!in_array($smiley, $arrayBadFiles)) {
                                    $arraySmilies[] = $smiley;
                                }
                            }
                            closedir($handle);

                            sort($arraySmilies);

                            foreach ($arraySmilies as $smiley) {
                                $selected = null;

                                if ($dataSmiley['url'] == $smiley) {
                                    $selected = ' selected="selected" ';
                                }

                                echo '<option value="'.$smiley.'" '.$selected.'>'.$smiley.'</option>';
                            }

                            if (!empty($dataSmiley['url'])) {
                                $preview = $dataSmiley['url'];
                            }
                            else {
                                $preview = $arraySmilies[0];
                            }
?>
                        </select>
                        <img id="smiley" style="max-width:100px;max-height:100px;" src="images/icones/<?php echo $preview ?>" alt="" />
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="formRow">
                    <div class="grid2"><label><?php echo UPLOAD_SMILEY; ?> :</label></div>
                    <div class="grid8"><input type="file" name="smileyFile" /></div>
                    <div class="clear"></div>
                </div>
                <div class="body center">
<?php
                    if (!empty($id)) {
                        echo '<input type="hidden" name="id" value="'.$id.'" />';
                    }
?>
                    <input class="buttonM bBlue" type="submit" value="<?php echo $submitValue; ?>" />
                    <a class="buttonM bDefault" href="index.php?file=Admin&amp;page=smilies"><?php echo BACK; ?></a>
                </div>
            </form>
        </div>
<?php
    }

    function sendSmiley() {

        $arrayRequest = array('name', 'code', 'url', 'id');

        foreach ($arrayRequest as $request) {
            if (array_key_exists($request, $_REQUEST) && !empty($_REQUEST[$request])) {
                ${$request} = mysql_real_escape_string($_REQUEST[$request]);
            }
            else {
                ${$request} = null;
            }
        }

        if ($name == $code || strpos($code, '"') !== false || strpos($code, "'") !== false) {
            printMessage(SMILEY_BAD_CODE, 'Failure');
            redirect('index.php?file=Admin&page=smilies&op=formSmiley', 3);
            adminFooter();
            exit();
        }

        $smileyFile = $_FILES['smileyFile'];

        if (!empty($smileyFile['name'])) {
            $tmp = explode('.', $smileyFile['name']);
            $extension = $tmp[count($tmp) -1];

            $arrayExtensions = array(
                                'jpg'  => 'jpeg',
                                'jpeg' => 'jpeg',
                                'JPG'  => 'jpeg',
                                'JPEG' => 'jpeg',
                                'png'  => 'png',
                                'PNG'  => 'png',
                                'gif'  => 'gif',
                                'GIF'  => 'gif'
                               );
            $arrayTypeMime  = array('image/jpeg', 'image/gif', 'image/png');

            $errors = 0;



            if (extension_loaded('fileinfo')) {
                $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                $fileType = finfo_file($finfo, $smileyFile['tmp_name']);

                if (!in_array($fileType, $arrayTypeMime)) {
                    $errors++;
                }
            }
            else if (extension_loaded('gd')) {
                $function = 'imagecreatefrom'.$arrayExtensions[$extension];
                $checkImage = @$function($smileyFile['tmp_name']);

                if ($checkImage === false) {
                    $errors++;
                }
            }
            else if (!array_key_exists($extension, $arrayExtensions)) {
                $errors++;
            }

            if ($errors > 0) {
                printMessage(BAD_IMAGE_FILE, 'Failure');
                redirect('index.php?file=Admin&page=smilies&op=formSmiley', 4);
                adminFooter();
                footer();
                exit();
            }

            $errorUploadMsg = '<div style="text-align: center;margin:20px 0;">
                                    <p><strong>'.SMILEY_UPLOAD_FAILED.'</strong></p>
                                    <a class="button" href="index.php?file=Admin&amp;page=smilies&amp;op=formSmiley">'.BACK.'</a>
                                </div>';

            $path = 'images/icones/';

            $url = $smileyFile['name'];
            move_uploaded_file($smileyFile['tmp_name'], $path.$url)
                or die ($errorUploadMsg);
            @chmod ($path.$url, 0644);
        }

        mysql_query('INSERT INTO '.SMILIES_TABLE.'
                     VALUES ("'.$id.'", "'.$code.'", "'.$url.'",  "'.$name.'")
                     ON DUPLICATE KEY UPDATE
                        id = VALUES(id),
                        code = VALUES(code),
                        url = VALUES(url),
                        name = VALUES(name);');

        $successMsg = SMILEY_ADDED;
        $actionMsg  = ACTION_ADD_SMILEY;

        if (!empty($id)) {
            $successMsg = SMILEY_EDITED;
            $actionMsg  = ACTION_EDIT_SMILEY;
        }

        $texteaction = $actionMsg.': '.$name;
        mysql_query('INSERT INTO '.ACTIONS_TABLE.' VALUES ("", "'.time().'", "'.$GLOBALS['user']['id'].'", "'.$texteaction.'") ')or die (mysql_error());

        printMessage($successMsg, 'Success');
        redirect("index.php?file=Admin&page=smilies", 2);
    }

    function delSmiley(){

        $id = intval($_REQUEST['id']);

        $dbsSmiley = 'SELECT name, url
                      FROM '.SMILIES_TABLE.'
                      WHERE id = "'.$id.'" ';
        $dbeSmiley = mysql_query($dbsSmiley);
        $smiley    = mysql_fetch_assoc($dbeSmiley);

        $path = 'images/icones/';
        @unlink($path.$smiley['url']);

        mysql_query ('DELETE FROM '.SMILIES_TABLE.' WHERE id = "'.$id.'" ');

        // Action
        $texteaction = ACTION_DELETE_SMILEY .': '.$smiley['name'];
        mysql_query('INSERT INTO '.ACTIONS_TABLE.' VALUES ("", "'.time().'", "'.$GLOBALS['user']['id'].'", "'.$texteaction.'") ')or die (mysql_error());
        //Fin action

        printMessage(SMILEY_DELETED, 'Success');
        redirect('index.php?file=Admin&page=smilies', 2);
    }

    switch ($_REQUEST['op']){
        case "formSmiley":
            formSmiley();
            break;
        case "sendSmiley":
            sendSmiley();
            break;
        case "delSmiley":
            delSmiley();
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
adminFooter();

?>
