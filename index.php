<?php
/**
 * Index of CMS Nuked-Klan
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

// Permet de s'assurer que tous les scripts passe bien par l'index du CMS
define('INDEX_CHECK', 1);

require_once 'Includes/php51compatibility.php';
require_once 'globals.php';

if (file_exists('conf.inc.php')) {
    require_once 'conf.inc.php';
}

require_once 'Includes/fatal_errors.php';

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == TRUE){
    extract($_REQUEST);
}

// Redirection vers l'installation si NK n'est pas installé
if (!defined('NK_INSTALLED')) {
    if (file_exists('INSTALL/index.php')) {
        header('location: INSTALL/index.php');
        exit();
    }
}

// Si le site est fermé on affiche le message de fermeture
if (!defined('NK_OPEN')) {
    echo WEBSITE_CLOSED;
    exit();
}

require_once 'nuked.php';
require_once 'Includes/hash.php';

// Inclusion du fichier de langue général
include_once 'lang/'.$GLOBALS['language'].'.lang.php';
nkTranslate('lang/'.$GLOBALS['language'].'.lang.php');

// Ouverture du buffer PHP
$bufferMedias = ob_start();

if ($nuked['time_generate'] == 'on') {
    $microTime = microtime();
}

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
if(ini_get('set_error_handler')) set_error_handler('erreursql');

$session  = sessionCheck();

if($session === true){
    $user =  secure();
}
else{
    $dbsVisitor = "SELECT access AS accessMods FROM ".GROUP_TABLE." WHERE id = '3'";
    $dbeVisitor = mysql_query($dbsVisitor);
    $user = mysql_fetch_assoc($dbeVisitor);
    $user['nickName']  = VISITOR;
    $user['ids_group'] = 3;
}

$session_admin = adminCheck();

if (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'ajax') {
    if ($nuked['stats_share'] == 1) {
        $timediff = (time() - $nuked['stats_timestamp'])/60/60/24/60; // 60 Days
        if($timediff >= 60) {
            require_once 'Includes/nkStats.php';
            $data = getStats($nuked);
            $string = serialize($data);
            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'content' => 'data=' . $string
                )
            );

            $context = stream_context_create($opts);
            $daurl = 'http://stats.nuked-klan.org/';
            $retour = file_get_contents($daurl, false, $context);
            $value_sql = ($retour == 'YES') ? mysql_real_escape_string(time()) : 'value + 86400';

            $sql = mysql_query('UPDATE '.CONFIG_TABLE.' SET value = '.mysql_real_escape_string($value_sql).' WHERE name = "stats_timestamp"');
        }
    }
    exit();
}

// Définition du type de page à afficher
if (isset($_REQUEST['nuked_nude']) && !empty($_REQUEST['nuked_nude'])) {
    $_REQUEST['im_file'] = $_REQUEST['nuked_nude'];
}
else if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
    $_REQUEST['im_file'] = $_REQUEST['page'];
}
else {
    $_REQUEST['im_file'] = 'index';
}

// Securisation des variables utilisateurs
if (preg_match('`\.\.`', $theme) || preg_match('`\.\.`', $language) || preg_match('`\.\.`', $_REQUEST['file']) ||
    preg_match('`\.\.`', $_REQUEST['im_file']) || preg_match('`http\:\/\/`i', $_REQUEST['file']) ||
    preg_match('`http\:\/\/`i', $_REQUEST['im_file']) || is_int(strpos( $_SERVER['QUERY_STRING'], '..' )) ||
    is_int(strpos( $_SERVER['QUERY_STRING'], 'http://' )) || is_int(strpos( $_SERVER['QUERY_STRING'], '%3C%3F' ))){
    exit();
/**
 * @todo Ajouter un insert SQL avec les infos IP etc... pour prevenir l'administrateur
 */
}

$_REQUEST['file']    = basename(trim($_REQUEST['file']));
$_REQUEST['im_file'] = basename(trim($_REQUEST['im_file']));
$_REQUEST['page']    = basename(trim($_REQUEST['im_file']));
$theme               = trim($theme);
$language            = trim($language);

// Check Ban
$check_ip = banip();

if (nkHasVisitor()) {
    $_SESSION['admin'] = false;
}

// Inclusion du fichier des couleurs
require_once 'themes/'.$theme.'/colors.php';

// Si le site est fermé
if ($nuked['nk_status'] == 'closed'
    && (nkHasVisitor() || !nkAccessAdmin('Admin'))
    && $_REQUEST['op'] != 'login_screen'
    && $_REQUEST['op'] != 'login_message'
    && $_REQUEST['op'] != 'login') {
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
        <head>
            <title><?php echo $nuked['name']; ?> - <?php echo $nuked['slogan']; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <link type="text/css" rel="stylesheet" media="screen" href="assets/css/nkDefault.css" />
            <link type="text/css" rel="stylesheet" media="screen" href="themes/<?php echo $theme; ?>/style.css" />
        </head>
        <body style="background:<?php echo $bgcolor2; ?>;">
            <div id="nkSiteClosedWrapper" style=" border: 1px solid <?php echo $bgcolor3; ?>; background:<?php echo $bgcolor2; ?>;">
                <h1><?php echo $nuked['name']; ?> - <?php echo $nuked['slogan']; ?></h1>
                <p><?php echo WEBSITE_CLOSED; ?></p>
                <a href="index.php?file=User&amp;op=login_screen"><strong><?php echo LOGIN; ?></strong></a>
            </div>
        </body>
    </html>
<?php
}
else if ( ($_REQUEST['file'] == 'Admin'
            || $_REQUEST['page'] == 'admin'
            || ( isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin' )
          )
            && (isset($_SESSION['admin']) && $_SESSION['admin'] == 0)) {
    require_once 'modules/Admin/login.php';
}

else if ( ( $_REQUEST['file'] != 'Admin' AND $_REQUEST['page'] != 'admin' )
            || ( $_REQUEST['file'] == 'Admin' || nkIsModEnabled($_REQUEST['file']) && nkAccessModule($_REQUEST['file']) ) ) {


    require_once 'themes/'.$theme.'/theme.php';

    if ($nuked['level_analys'] != -1) {
        visits();
    }

    if (!isset($_REQUEST['nuked_nude'])){
        if (defined('NK_GZIP') && ini_get('zlib_output')) {
            ob_start('ob_gzhandler');
        }

        if ( !($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin'))
            || $_REQUEST['page'] == 'login') {

            top();

        require_once 'Includes/nkMediasIncludes.php';

        $bufferEdited = ob_get_contents();

        $findJquery = (boolean)preg_match('#<script[\s]*[type="text/javascript"]*[\s]*src="[A-z0-9:./_-]*(jquery)+[A-z0-9.:/_-]*"[\s]*[type="text/javascript"]*[\s]*>#', $bufferEdited);
        $mediasToInclude = printMedias($findJquery);

        if($findJquery === true){
            $bufferEdited = preg_replace('#<script[\s]*[type="text/javascript"]*[\s]*src="[A-z0-9:./_-]*(jquery)+[A-z0-9.:/_-]*"[\s]*[type="text/javascript"]*[\s]*>#',
                                      '<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js">',
                                      $bufferEdited);
        }

        $bufferEdited = preg_replace('#</head>#', $mediasToInclude.'</head>', $bufferEdited);

        ob_end_clean();

        echo $bufferEdited;
?>
            <script type="text/javascript" src="assets/scripts/infobulle.js"></script>
            <script type="text/javascript">
                InitBulle('<?php echo $bgcolor2; ?>','<?php echo $bgcolor3; ?>', 2);
            </script>
            <script type="text/javascript" src="assets/scripts/syntaxhighlighter/shCore.js"></script>
            <script type="text/javascript" src="assets/scripts/syntaxhighlighter/shAutoloader.js"></script>
            <script type="text/javascript" src="assets/scripts/syntaxhighlighter.autoloader.js"></script>
            <link type="text/css" rel="stylesheet" href="assets/css/syntaxhighlighter/shCoreMonokai.css"/>
            <link type="text/css" rel="stylesheet" href="assets/css/syntaxhighlighter/shThemeMonokai.css"/>
<?php
        }

        if(nkAccessAdmin('Admin') && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin') {
            if ($nuked['nk_status'] == 'closed') {
?>
                <div id="nkSiteClosedLogged" class="nkAlert">
                    <strong><?php echo WEBSITE_CLOSED_ADMIN; ?></strong>
                    <p><?php echo $nuked['url']; ?>/index.php?file=User&amp;op=login_screen</p>
                </div>
<?php
            }
            if (is_dir('INSTALL/')){
?>
                <div id="nkInstallDirTrue" class="nkAlert">
                    <strong><?php echo REMOVE_INSTALL_DIR; ?></strong>
                </div>
<?php
            }
            if (file_exists('install.php') || file_exists('update.php')){
?>
                <div id="nkInstallFileTrue" class="nkAlert">
                    <strong><?php echo REMOVE_INSTALL_FILES; ?></strong>
                </div>
<?php
            }
        }

        if ((!nkHasVisitor() && $GLOBALS['user']['nbMess'] > 0) && !isset($_COOKIE['popup']) && $_REQUEST['file'] != 'User' && $_REQUEST['file'] != 'Userbox' && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin'){
?>
                <div id="nkNewPrivateMsg" class="nkAlert">
                    <strong><?php echo NEW_PV_MSG_START; ?>&nbsp;<?php echo $GLOBALS['user']['nbMess']; ?>&nbsp;<?php echo NEW_PV_MSG_END; ?></strong>
                    <a href="index.php?file=Userbox"><?php echo GOTO_PRIVATE_MESSAGES; ?></a>
                    <a id="nkNewPrivateMsgClose" href="#" title="<?php echo CLICK_TO_CLOSE; ?>"><span><?php echo CLICK_TO_CLOSE; ?></span></a>
                </div>
<?php
        }
    }
    else {
        header('Content-Type: text/html;charset=ISO-8859-1');
    }

    $fileMod = 'modules/'.$_REQUEST['file'].'/'.$_REQUEST['im_file'].'.php';

    if ($_REQUEST['file'] == 'Admin' && $_REQUEST['im_file'] != 'index') {
        $fileMod = 'modules/'.$_REQUEST['file'].'/pages/'.$_REQUEST['im_file'].'.php';
    }

    if (is_file($fileMod)) {
        if($_REQUEST['im_file'] == 'admin'){
            $functionAccess = 'nkAccessAdmin';
        }
        else{
            $functionAccess = 'nkAccessModule';
        }

        $hasAccess = $functionAccess($_REQUEST['file']);
        if($_REQUEST['file'] != 'Admin'
           && $_REQUEST['file'] != '404'
           && $_REQUEST['page'] != 'Admin'
           && ((isset($_REQUEST['nuked_nude'])
                && $_REQUEST['nuked_nude'] != 'Admin')
              || !isset($_REQUEST['nuked_nude']))
          ){
            $modEnabled  = nkIsModEnabled($_REQUEST['file']);
        }
        else{
            $modEnabled = true;
        }

        if(($hasAccess === true && $modEnabled === true) || $_REQUEST['file'] == 'User'){

            require_once $fileMod;

        }
        else if ($modEnabled === false && $_REQUEST['file']) {
?>
            <div class="nkErrorMod">
                <p><?php echo MODULE_DISABLED; ?></p>
                <a href="javascript:history.back()"><strong><?php echo BACK; ?></strong></a>
            </div>
<?php
        }
        else if ($modEnabled === true && (!nkAccessModule($_REQUEST['file']) && nkHasVisitor())) {
?>
            <div class="nkErrorMod">
                <p><?php echo MODULE_VISITORS_DENIED; ?></p>
                <a href="index.php?file=User&amp;op=login_screen"><?php echo LOGIN; ?></a> |
                <a href="index.php?file=User&amp;op=reg_screen"><?php echo REGISTRATION; ?></a>
            </div>
<?php
        }
        else {
?>
            <div class="nkErrorMod">
                <p><?php echo MODULE_ACCESS_DENIED; ?></p>
                <a href="javascript:history.back()"><strong><?php echo BACK; ?></strong></a>
            </div>
<?php
        }
    }
    else {
        require_once 'modules/404/index.php';
    }

    if ($_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin' && defined('EDITOR_CHECK')) {

        ?>
            <script type="text/javascript" src="assets/ckeditor/ckeditor.js"></script>
            <script type="text/javascript" src="assets/ckeditor/config.js"></script>
            <script type="text/javascript">
                //<![CDATA[
                if(document.getElementById('e_basic')){
                    CKEDITOR.config.scayt_sLang = "<?php echo (($language == 'french') ? 'fr_FR' : 'en_US'); ?>";
                    CKEDITOR.config.scayt_autoStartup = "true";
                    CKEDITOR.replace('e_basic',{
                        toolbar : 'Basic',
                        language : '<?php echo substr($language, 0,2) ?>',
                        <?php echo !empty($bgcolor4) ? 'uiColor : \''.$bgcolor4.'\'' : ''; ?>
                    });
                    <?php echo ConfigSmileyCkeditor(); ?>
                }

                if(document.getElementById('e_advanced')){
                    <?php echo ($nuked['video_editeur'] == 'on') ? 'CKEDITOR.config.extraPlugins = \'Video\';' : ''; ?>
                    CKEDITOR.config.scayt_sLang = "<?php echo (($language == 'french') ? 'fr_FR' : 'en_US'); ?>";
                    <?php echo ($nuked['scayt_editeur'] == 'on') ? 'CKEDITOR.config.scayt_autoStartup = "true";' : ''; ?>
                    CKEDITOR.replace('e_advanced',{
                        toolbar : 'Full',
                        language : '<?php echo substr($language, 0,2) ?>',
                        <?php echo !empty($bgcolor4) ? 'uiColor : \''.$bgcolor4.'\',' : ''; ?>
                        allowedContent:
                            'p h1 h2 h3 h4 h5 h6 blockquote tr td div a span{text-align,font-size,font-family,font-style,color,background-color,display};' +
                            'img[!src,alt,width,height,class,id,style,title,border]{*}(*);' +
                            'strong s em u strike sub sup ol ul li br caption thead  hr big small tt code del ins cite q address section aside header;' +
                            'div[class,id,style,title,align]{page-break-after,width,height,background};' +
                            'a[!href,accesskey,class,id,name,rel,style,tabindex,target,title];' +
                            'table[align,border,cellpadding,cellspacing,class,id,style];' +
                            'td[colspan, rowspan];' +
                            'th[scope];' +
                            'pre(*);' +
                            'span[id, style];'
                            <?php if($nuked['video_editeur'] == 'on'){ ?>
                                + 'object[width,height,data,type];'
                                + 'param[name,value];'
                                + 'embed[width,height,src,type,allowfullscreen,allowscriptaccess];'
                            <?php } ?>
                    });
                    <?php echo ConfigSmileyCkeditor(); ?>
                }
                //]]>
            </script>
        <?php
    }

    if (!isset($_REQUEST['nuked_nude'])) {
        if (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') || $_REQUEST['page'] == 'login') {
            footer();
            require_once 'Includes/copyleft.php';
        }

        if ($nuked['time_generate'] == 'on' && (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin')))) {
            $microTime = microtime() - $microTime;
            echo '<p class="nkGenerated">Generated in '.$microTime.'s</p>';
        }

        send_stats_nk();

        echo '</body></html>';
    }
}
else {
    require_once 'themes/'.$theme.'/colors.php';
    require_once 'themes/'.$theme.'/theme.php';
    top();
    opentable();
    translate('lang/'.$language.'.lang.php');
?>
    <link type="text/css" rel="stylesheet" href="assets/css/nkDefault.css" />
    <div class="nkErrorMod">
        <p><?php echo MODULE_ACCESS_DENIED; ?></p>
        <a href="javascript:history.back()"><b><?php echo BACK; ?></b></a>
    </div>

<?php

    closetable();
    footer();
}

mysql_close($db);
?>
