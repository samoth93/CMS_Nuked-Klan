<?php
/**
 * layout.php (modules/Admin)
 *
 * Admin layout
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

// Inclusion de la langue
nkTranslate('modules/Admin/lang/'.$GLOBALS['language'].'.lang.php');

// Inclusion de la configuration de l'administration
require_once 'modules/Admin/config.php';

// Inclusion de la sidebar de l'administration
require_once 'modules/Admin/views/sidebar.php';

function adminHeader(){
    // Définition du favicon
    $favicon = 'medias/modules/Admin/assets/images/favicon.ico';

    if (file_exists('themes/'.$GLOBALS['nuked']['theme'].'/favicon.ico')) {
        $favicon = 'themes/'.$GLOBALS['nuked']['theme'].'/favicon.ico';
    }
?>
 <!DOCTYPE html>
 <html>
     <head>
        <meta name="keywords" content="<?php echo $GLOBALS['nuked']['keyword'] ?>" />
        <meta name="Description" content="<?php echo $GLOBALS['nuked']['description'] ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title><?php echo $GLOBALS['nuked']['name'] ?> - <?php echo ADMIN_PANEL_TITLE; ?></title>
        <link rel="shortcut icon"  href="<?php echo $favicon; ?>" />
        <link href="modules/Admin/assets/css/main.css" rel="stylesheet" type="text/css" />
        <script src="modules/Admin/assets/scripts/jquery-1.8.3.min.js"></script>
        <script src="modules/Admin/assets/scripts/jqueryUI-1.10.3.min.js"></script>
        <script src="modules/Admin/assets/scripts/main.js"></script>
        <script src="modules/Admin/assets/scripts/jquery.validationEngine-<?php echo substr($GLOBALS['language'], 0, 2); ?>.js"></script>
        <script src="modules/Admin/assets/scripts/nk.js"></script>
    </head>
    <body>
        <div id="nkDialog">No content</div>
        <header id="top">
            <div class="wrapper">
                <a href="index.php?file=Admin" title="" class="logo">
                    <img src="modules/Admin/assets/images/logo.png" alt="" />
                </a>
<?php
                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
?>
                        <div class="topNav">
                            <ul class="userNav">
                                <li><a title="" class="search"></a></li>
                                <li><a href="#" title="" class="logout"></a></li>
                                <li class="showTabletP"><a href="#" title="" class="sidebar"></a></li>
                            </ul>
                            <a title="" class="iButton"></a>
                            <a title="" class="iTop"></a>
                            <div class="topSearch">
                                <div class="topDropArrow"></div>
                                <form action="">
                                    <input type="text" placeholder="search..." name="topSearch" />
                                    <input type="submit" value="" />
                                </form>
                            </div>
                        </div>
<?php
                    endif;
?>
                <ul class="altMenu">
<?php
                            foreach ($GLOBALS['arrayMainNav'] as $nav => $navContent):

                                $classAccordion = null;

                                if (!is_null($navContent[2])) {
                                    $classAccordion = ' class="exp" ';
                                }
?>
                                <li>
                                    <a <?php echo $classAccordion; ?> href="<?php echo $navContent[0]; ?>">
                                        <span><?php echo $nav; ?></span>
                                    </a>
<?php
                                    if (!is_null($navContent[2])):
?>
                                        <ul>
<?php
                                            foreach ($navContent[2] as $subNav => $subNavContent):
?>
                                                <li>
                                                    <a href="<?php echo $subNavContent[0]; ?>">
                                                        <span><?php echo $subNav; ?></span>
                                                    </a>
                                                </li>
<?php
                                            endforeach;
?>
                                        </ul>
<?php
                                    endif;
?>
                                </li>
<?php
                            endforeach;
?>
                        </ul>
                <div class="clear"></div>
            </div>
        </header>
        <section>
<?php
            if (isset($_SESSION['admin']) && $_SESSION['admin'] === true):
                // On affiche le header du layout
                sidebar();
?>
                <div id="content">
                    <div class="contentTop">
                        <span class="pageTitle"><?php echo $GLOBALS['mainTitle']; ?></span>
                        <ul class="quickStats">
                            <li>
                                <a href="" class="blueImg"></a>
                                <div class="floatR"><strong class="blue">5489</strong><span><?php echo VISITS; ?></span></div>
                            </li>
                            <li>
                                <a href="" class="redImg"></a>
                                <div class="floatR"><strong class="blue">4658</strong><span><?php echo USERS; ?></span></div>
                            </li>
                            <li>
                                <a href="" class="greenImg"></a>
                                <div class="floatR"><strong class="blue">1289</strong><span><?php echo PAGES_VIEWS; ?></span></div>
                            </li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <!-- Breadcrumbs line -->
                    <div class="breadLine">
                        <div class="bc">
                            <ul id="breadcrumbs" class="breadcrumbs">
<?php
                                $sizeBC = count($GLOBALS['breadcrumbs']);
                                $i = 0;

                                foreach ($GLOBALS['breadcrumbs'] as $title => $link):

                                    $classCurrent = null;

                                    if ($i == $sizeBC - 1) {
                                        $classCurrent = ' class="current" ';
                                    }

                                    $trueLink = $link;

                                    if (is_array($link)) {
                                        $trueLink = '#';
                                    }

?>
                                    <li <?php echo $classCurrent; ?>>
                                        <a href="<?php echo $trueLink; ?>"><?php echo $title; ?></a>
<?php
                                        if (is_array($link)) {
                                            echo '<ul>';

                                            foreach ($link as $subTitle => $data):
?>
                                                <li><a href="<?php echo $data[0]; ?>"><?php echo $subTitle; ?></a></li>
<?php
                                            endforeach;

                                            echo '</ul>';
                                        }
?>
                                    </li>
<?php
                                $i++;
                                endforeach;
?>
                            </ul>
                        </div>
                        <div class="breadLinks">
                            <ul>
                                <li>
                                    <a href="#">
                                        <span>Notifications</span> <strong>12</strong>
                                    </a>
                                </li>
                            </ul>
                             <div class="clear"></div>
                        </div>
                    </div>
                    <div class="wrapper">
<?php
            endif;
}

function adminFooter(){
?>
                        <script src="assets/scripts/syntaxhighlighter/shCore.js"></script>
                        <script src="assets/scripts/syntaxhighlighter/shAutoloader.js"></script>
                        <script src="assets/scripts/syntaxhighlighter.autoloader.js"></script>
                        <link type="text/css" rel="stylesheet" href="assets/css/syntaxhighlighter/shCore.css"/>
                        <link type="text/css" rel="stylesheet" href="assets/css/syntaxhighlighter/shThemeDefault.css"/>
                        <script src="assets/ckeditor/ckeditor.js"></script>
                        <script src="assets/ckeditor/config.js"></script>
                        <script>
                            //<![CDATA[
                            <?php echo ($GLOBALS['nuked']['video_editeur'] == 'on') ? 'CKEDITOR.config.extraPlugins = "Video";' : ''; ?>
                            CKEDITOR.config.scayt_sLang = "<?php echo (($GLOBALS['language'] == 'french') ? 'fr_FR' : 'en_US'); ?>";
                            <?php echo ($GLOBALS['nuked']['scayt_editeur'] == 'on') ? 'CKEDITOR.config.scayt_autoStartup = "true";' : ''; ?>
                            CKEDITOR.replaceAll(function(textarea,config){
                                if (textarea.className!='editor') return false;
                                CKEDITOR.config.toolbar = 'Full';
                                CKEDITOR.configlanguage = "<?php echo substr($GLOBALS['language'], 0,2) ?>";
                                CKEDITOR.config.uiColor = '#ffffff';
                                CKEDITOR.config.allowedContent=
                                    'p h1 h2 h3 h4 h5 h6 blockquote tr td div a span{text-align,font-size,font-family,font-style,color,background-color,display};' +
                                    'img[!src,alt,width,height,class,id,style,title,border,dir]{*}(*);' +
                                    'strong s em u strike sub sup ol ul li br caption thead  hr big small tt code del ins cite q address section aside header;' +
                                    'div[class,id,style,title,align]{page-break-after,width,height,background};' +
                                    'a[!href,accesskey,class,id,name,rel,style,tabindex,target,title];' +
                                    'table[align,border,cellpadding,cellspacing,class,id,style]{*}(*);' +
                                    'td[colspan, rowspan];' +
                                    'th[scope];' +
                                    'pre(*);' +
                                    'span[id, style];'
                                    <?php if($GLOBALS['nuked']['video_editeur'] == 'on'){ ?>
                                        + 'object[width,height,data,type];'
                                        + 'param[name,value];'
                                        + 'embed[width,height,src,type,allowfullscreen,allowscriptaccess];'
                                    <?php } ?>
                                    ;
                            });
                            <?php
                            if($_REQUEST['file'] == 'Forum' && ($_REQUEST['op'] == 'edit_forum' || $_REQUEST['op'] == 'add_forum')){
                                echo 'CKEDITOR.config.autoParagraph = false;
                                CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;';
                            }
                            echo ConfigSmileyCkeditor();
                            ?>
                            //]]>
                        </script>
                    </div>
                </div>
            </section>
        </body>
    </html>
<?php
}

function printMessage($text, $status = 'Information', $hideable = false) {

    $arrayStatus = array('Information', 'Warning', 'Success', 'Failure');

    if (!in_array($status, $arrayStatus)) {
        $status  = 'Information';
    }

    $classHideable = null;

    if ($hideable === true) {
        $classHideable = 'nNoteHideable';
    }

    if (!is_null($text) && !empty($text)):
?>
        <div class="nNote n<?php echo $status.' '.$classHideable; ?>">
            <p>
                <?php echo $text; ?>
            </p>
        </div>
<?php
    endif;
}

?>
