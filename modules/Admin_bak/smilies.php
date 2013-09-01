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


include("modules/Admin/design.php");

$hasAdminAccess = nkAccessAdmin('Smilies');

admintop();

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
        <div class="content-box" style="max-width:900px;">
            <div class="content-box-header">
                <h3><?php echo SMILIES_MANAGEMENT; ?></h3>
                <div style="text-align:right">
                    <a class="adminIcon icon-help" href="help/<?php echo $GLOBALS['language'];?>/block.php" rel="modal" title="<?php echo HELP; ?>">
                        <span><?php echo HELP; ?></span>
                    </a>
                </div>
            </div>
            <div id="tab2" class="tab-content">
                <div class="iconLinkWrapper">
                    <a class="iconLinkAdd" href="index.php?file=Admin&amp;page=smilies&amp;op=formSmiley">
                        <span><?php echo SMILEY_ADD; ?></span>
                    </a>
                </div>
                <table style="width:100%;border:none;" cellspacing="1" cellpadding="2">
                    <thead>
                        <tr>
                            <td style="width: 20%;text-align:center;">
                                <strong><?php echo SMILEY; ?></strong>
                            </td>
                            <td style="width: 30%;text-align:center;">
                                <strong><?php echo NAME; ?></strong>
                            </td>
                            <td style="width: 20%;text-align:center;">
                                <strong><?php echo CODE; ?></strong>
                            </td>
                            <td style="width: 15%;text-align:center;">
                                <strong><?php echo EDIT; ?></strong>
                            </td>
                            <td style="width: 15%;text-align:center;">
                                <strong><?php echo DELETE; ?></strong>
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
                            <td style="width: 20%;text-align:center;vertical-align:middle;">
                                <img style="max-width:100px;max-height:100px;" src="images/icones/<?php echo $smiley['url']; ?>" alt="" title="<?php echo $smiley['url']; ?>" />
                            </td>
                            <td style="width: 30%;text-align:center;vertical-align:middle;">
                                <?php echo htmlentities($smiley['name']); ?>
                            </td>
                            <td style="width: 20%;text-align:center;vertical-align:middle;">
                                <?php echo $smiley['code']; ?>
                            </td>
                            <td style="width: 15%;text-align:center;vertical-align:middle;">
                                <a class="adminIcon icon-edit" href="index.php?file=Admin&amp;page=smilies&amp;op=formSmiley&amp;id=<?php echo $smiley['id']; ?>" title="<?php echo EDIT; ?>">
                                    <span><?php echo EDIT; ?></span>
                                </a>
                            </td>
                            <td style="width: 15%;text-align:center;vertical-align:middle;">
                                <a class="adminIcon icon-delete" href="javascript:delSmiley('<?php echo mysql_real_escape_string($smiley['name']); ?>', '<?php echo $smiley['id']; ?>');" title="<?php echo DELETE; ?>">
                                    <span><?php echo DELETE; ?></span>
                                </a>
                            </td>
                        </tr>
<?php
        endwhile;
?>
                    </tbody>
                </table>
                <p style="text-align:center;margin-top:20px;">
                    <a href="index.php?file=Admin" class="button">
                       <?php echo BACK; ?>
                    </a>
                </p>
            </div>
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
        <div class="content-box" style="max-width:900px;">
            <div class="content-box-header">
                <h3><?php echo SMILIES_MANAGEMENT; ?></h3>
                <div style="text-align:right">
                    <a class="adminIcon icon-help" href="help/<?php echo $GLOBALS['language'];?>/block.php" rel="modal" title="<?php echo HELP; ?>">
                        <span><?php echo HELP; ?></span>
                    </a>
                </div>
            </div>
            <div id="tab2" class="tab-content">
                <form method="post" action="index.php?file=Admin&amp;page=smilies&amp;op=sendSmiley" enctype="multipart/form-data">
                    <table style="margin: auto;text-align: left" cellspacing="0" cellpadding="2" border="0">
                        <tr>
                            <td>
                                <strong><?php echo NAME; ?> :</strong>
                                <input type="text" name="name" size="30" required="required" value="<?php echo $dataSmiley['name']; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo CODE; ?> :</strong>
                                <input type="text" name="code" size="10" required="required" value="<?php echo $dataSmiley['code']; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo SMILEY; ?> :</strong>
                                <select name="url" onchange="updateSmiley(this.options[selectedIndex].value);">
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
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo UPLOAD_SMILEY; ?> :</strong>
                                <input type="file" name="smileyFile" />
                            </td>
                        </tr>
                        <tr>
                            <td>
<?php
                                if (!empty($id)) {
                                    echo '<input type="hidden" name="id" value="'.$id.'" />';
                                }
?>
                                <input type="submit" value="<?php echo $submitValue; ?>" />
                            </td>
                        </tr>
                    </table>
                </form>
                <div style="text-align:center;margin:10px 0;">
                    <a class="button" href="index.php?file=Admin&amp;page=smilies"><?php echo BACK; ?></a>
                </div>
            </div>
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
?>
            <div class="notification error png_bg">
                <div>
                    <?php echo SMILEY_BAD_CODE; ?>
                </div>
            </div>
<?php
            redirect('index.php?file=Admin&page=smilies&op=formSmiley', 3);
            adminfoot();
            footer();
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
?>
                <div class="notification error png_bg">
                    <div>
                        <?php echo BAD_IMAGE_FILE; ?>
                    </div>
                </div>
<?php
                redirect('index.php?file=Admin&page=smilies&op=formSmiley', 2);
                adminfoot();
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
        mysql_query('INSERT INTO '.ACTION_TABLE.' VALUES ("", "'.time().'", "'.$GLOBALS['user']['id'].'", "'.$texteaction.'") ')or die (mysql_error());


?>
        <div class="notification success png_bg">
            <div>
                <?php echo $successMsg; ?>
            </div>
        </div>
<?php
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
        mysql_query('INSERT INTO '.ACTION_TABLE.' VALUES ("", "'.time().'", "'.$GLOBALS['user']['id'].'", "'.$texteaction.'") ')or die (mysql_error());
        //Fin action
?>
        <div class="notification success png_bg">
            <div>
                <?php echo SMILEY_DELETED; ?>
            </div>
        </div>
<?php

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
adminfoot();

?>
