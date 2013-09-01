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

global $language, $user, $nuked;

include('modules/Admin/design.php');

$hasAdmin = nkHasAdmin();

if(nkHasVisitor()){
    header('location: index.php?file=404');
    exit();
}

if ($hasAdmin === true) {
    admintop();
    ?>
    <!-- Page Head -->

    <h2><?php echo HELLO . '&nbsp;' . $GLOBALS['user']['nickName']; ?></h2>
    <p id="page-intro"><?php echo ADMIN_WELCOME; ?></p>
        <div style="text-align: right">
        <form method="post" onsubmit="maFonctionAjax3(this.module.value);return false" action="">
            <fieldset>
            <select id="module" name="module">
                <option value="Admin"><?php echo PANEL; ?></option>
                <?php
                $modules = array();
                $sql = mysql_query('SELECT name FROM ' . MODULES_TABLE . ' WHERE status = \'on\' ORDER BY name');
                while (list($mod) = mysql_fetch_array($sql)){
                    if (defined(strtoupper($mod).'_MODNAME')) {
                        $modname = constant(strtoupper($mod).'_MODNAME');
                    }
                    else{
                        $modname = $mod;
                    }

                    if(nkAccessAdmin($mod)){
                        array_push($modules, $modname . '|' . $mod);
                    }
                }

                natcasesort($modules);
                foreach($modules as $value)
                {
                    $temp = explode('|', $value);

                    if (is_file('modules/' . $temp[1] . '/admin.php') AND is_file('modules/' . $temp[1] . '/menu/'.$language.'/menu.php'))
                    {
                        echo '<option value="' . $temp[1] . '">' . $temp[0] . '</option>';
                    }
                }
                ?>
            </select>
            <input class="button" type="submit" value="Send" />
            </fieldset>
        </form>
        </div>
        <ul class="shortcut-buttons-set" id="1">
            <li>
                <a class="shortcut-button" href="modules/Admin/menu/<?php echo $language; ?>/aide.php" rel="modal">
                    <span><img src="modules/Admin/images/icons/aide.png" alt="icon" /><br /><br /><?php echo HELP; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" rel="modal" href="index.php?file=Stats&amp;nuked_nude=admin">
                    <span><img src="modules/Admin/images/icons/statistiques.png" alt="icon" /><br /><br /><?php echo STATS; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=erreursql">
                    <span><img src="modules/Admin/images/icons/erreur.png" alt="icon" /><br /><?php if($language=='english'){echo '<br/>';} echo SQL_ERRORS; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" href="#notification" rel="modal">
                    <span><img src="modules/Admin/images/icons/clock_48.png" alt="icon" /><br /><?php echo NOTIFICATIONS; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" href="#messages" rel="modal">
                    <span><img src="modules/Admin/images/icons/comment_48.png" alt="icon" /><br /><br /><?php echo DISCUSSIONS; ?></span>
                </a>
            </li>
            <?php
                if(file_exists('themes/' . $nuked['theme'] . '/admin.php'))
                {
                    if (file_exists('themes/' . $nuked['theme'] . '/images/adminpanel.png'))
                        $imagepanel = 'themes/' . $nuked['theme'] . '/images/adminpanel.png';
                    else $imagepanel = 'modules/Admin/images/icons/logo.png';
                ?>
                <li>
                    <a class="shortcut-button" href="index.php?file=Admin&amp;page=theme">
                        <span><img src="<?php echo $imagepanel; ?>" alt="icon" /><br /><?php echo THEME_MANAGEMENT; ?></span>
                    </a>
                </li>
            <?php
                }
            ?>
        </ul><!-- End .shortcut-buttons-set -->

        <div class="clear"></div><!-- End .clear -->
            <div id="notification" style="display: none">
                <h3><?php echo NOTIFICATIONS; ?>:</h3>
                <form method="post" onsubmit="maFonctionAjax2(this.texte.value,this.type.value);return false" action="">
                    <h4><?php echo MESSAGE; ?>:</h4>
                    <fieldset>
                        <textarea name="texte" cols="79" rows="5"></textarea>
                    </fieldset>

                    <fieldset>
                        <select id="type" name="type" class="small-input">
                            <option value="0"><?php echo TYPE; ?>...</option>
                            <option value="1"><?php echo INFO; ?></option>
                            <option value="2"><?php echo FAIL; ?></option>
                            <option value="3"><?php echo SUCCESS; ?></option>
                            <option value="4"><?php echo ALERT; ?></option>
                        </select>

                        <input class="button" type="submit" value="Send" />
                    </fieldset>
                </form>
            </div>

            <div style="width: 100%">
                <div class="content-box column-left">
                    <div class="content-box-header" style="margin-bottom: 0">
                        <h3><?php echo ANNOUNCES; ?></h3>
                    </div><!-- End .content-box-header -->

                    <div class="content-box-content">
                        <div class="tab-content default-tab" id="NKmess">
                            <p>
                                <?php echo CONNECT_IN_PROGRESS; ?>
                            </p>
                        </div><!-- End #tab3 -->
                        <div class="tab-content default-tab" id="NKUpdate">
                            <!-- NK.UPDATE CONTENT -->
                        </div><!-- End #tab3 -->
                        <?php echo '<script type="text/javascript" src="http://www.nuked-klan.org/extra/message.php?version=' . $nuked['version'] . '&lang=' . $language . '"></script>'; ?>
                    </div><!-- End .content-box-content -->
                </div><!-- End .content-box -->

            <div class="content-box column-right">
                <div class="content-box-header" style="margin-bottom: 0"><!-- Add the class "closed" to the Content box header to have it closed by default -->
                    <h3><?php echo ACTIONS; ?></h3>
                </div><!-- End .content-box-header -->

                <div class="content-box-content">
                    <div class="tab-content default-tab">
                        <h4><a href="index.php?file=Admin&amp;page=action"><?php echo SEE_ACTIONS; ?></a></h4>
                        <p>
                        <?php
                        $sql_act = mysql_query("SELECT date, pseudo, action  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC LIMIT 0, 10");
                        while ($action = mysql_fetch_array($sql_act))
                        {
                            $sql = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $action['pseudo'] . "'");
                            list($pseudo) = mysql_fetch_array($sql);

                            $action['action'] = $pseudo . ' ' . $action['action'];
                            $action['date'] = nkDate($action['date']);

                            echo '<div style="font-size: 12px"><em>' . $action['date'] . '</em></div>
                            <div style="font-size: 12px; margin-bottom: 4px">' . $action['action'] . '</div>';
                        }
                        ?>
                        </p>
                    </div><!-- End #tab3 -->
                </div><!-- End .content-box-content -->
            </div><!-- End .content-box -->
            <div class="clear"></div>
        </div>

        <!-- Start Notifications -->
        <?php
            $sql2 = mysql_query('SELECT id, type, texte  FROM ' . $nuked['prefix'] . '_notification ORDER BY date DESC LIMIT 0, 4');
            while (list($id, $type, $texte) = mysql_fetch_array($sql2))
            {
                if($type == 4)
                {
                ?>

                <div class="notification attention png_bg">
                    <?php if (nkHasGod()){ ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#"  class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <?php } ?>
                    <div>
                        <?php echo NOTIFICATION_ALERT; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
                else if($type == 1)
                {
                ?>
                <div class="notification information png_bg">
                    <?php if (nkHasGod()){ ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#" class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <?php } ?>
                    <div>
                        <?php echo NOTIFICATION_INFO; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
                else if($type == 3)
                {
                ?>
                <div class="notification success png_bg">
                    <?php if (nkHasGod()){ ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#" class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <?php } ?>
                    <div>
                        <?php echo NOTIFICATION_SUCCESS; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
                else if($type == 2)
                {
                ?>
                <div class="notification error png_bg">
                    <?php if (nkHasGod()){ ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#" class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <?php } ?>
                    <div>
                        <?php echo NOTIFICATION_FAIL; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
            }
    adminfoot();
}
else
{
    admintop();
    echo '<div class="notification error png_bg">
        <div>
        <br /><br /><div style="text-align: center">' . ZONEADMIN . '<br /><br /><a href="javascript:history.back()"><b>' . BACK . '</b></a></div><br /><br />
        </div>
    </div>';
    adminfoot();
}
?>
