<?php

/**
 *
 * Tableaux des menus et sous-menu
 *
 */

    // Sous-Menu des préférences
    $arraySettings    = array(
                            GENERAL_SETTINGS => array('index.php?file=Admin&amp;page=settings', 'icon-general-settings'),
                            PHPINFO          => array('index.php?file=Admin&amp;page=phpinfo', 'icon-phpinfo'),
                            ACTIONS          => array('index.php?file=Admin&amp;page=actions', 'icon-actions'),
                            SQL_ERRORS       => array('index.php?file=Admin&amp;page=sql_errors', 'icon-warning')
                        );

    // Sous-Menu des gestions
    $arrayManagements = array(
                            USERS            => array('index.php?file=Admin&amp;page=users', 'icon-users'),
                            GROUPS           => array('index.php?file=Admin&amp;page=groups', 'icon-groups'),
                            MODULES          => array('index.php?file=Admin&amp;page=modules', 'icon-modules'),
                            BLOCKS           => array('index.php?file=Admin&amp;page=blocks', 'icon-blocks'),
                            SMILIES          => array('index.php?file=Admin&amp;page=smilies', 'icon-smilies'),
                            GAMES            => array('index.php?file=Admin&amp;page=games', 'icon-games')
                        );

    // Initialisation du sous-menu des modules
    $arrayModules     = array();

    // Récupération de la liste des modules
    $dbsModules = 'SELECT name
                   FROM '.MODULES_TABLE.'
                   WHERE status = "on"
                   ORDER BY name ASC';
    $dbeModules = mysql_query($dbsModules);

    while (list($name) = mysql_fetch_array($dbeModules)) {
        $translateName = $name;

        // Si le nom du module a une traduction on traduit
        if (defined(strtoupper($name).'_MODNAME')) {
            $translateName = constant(strtoupper($name).'_MODNAME');
        }

        $arrayModules[$translateName] = array($name, 'icon'.strtoupper(substr($name, 0, 1)).substr($name, 1));
    }

    // On tri le sous-menu par rapport aux traductions
    ksort($arrayModules);

    // Sous-Menu divers
    $arrayMisc        = array(
                            OFFICIAL_FORUM   => array('http://www.nuked-klan.org/index.php?file=Forum', 'icon-forums'),
                            LICENSE          => array('index.php?file=Admin&amp;page=license', 'icon-license'),
                            ABOUT            => array('index.php?file=Admin&amp;page=about', 'icon-about')
                        );

    // Menu principal
    $arrayMainNav     = array(
                            PANEL            => array('index.php?file=Admin', 'icon-dashboard', null),
                            SETTINGS         => array('#', 'icon-settings', $arraySettings),
                            MANAGEMENTS      => array('#', 'icon-managements', $arrayManagements),
                            MODULES          => array('#', 'icon-modules', $arrayModules),
                            MISC             => array('#', 'icon-misc', $arrayMisc)
                        );

    // Si une administration de theme est fournie avec le theme on l'inclus dans le menu
    if (file_exists('themes/'.$GLOBALS['nuked']['theme'].'/admin.php')) {
        $arrayMainNav[THEME] = array('index.php?file=Admin&amp;page=template', 'icon-template', null);
    }

    // Définition du titre principal
    $arrayPages =   array(
                        SETTINGS    =>  array(
                                            'settings' => array('title' => GENERAL_SETTINGS, 'link' => 'index.php?file=Admin&page=settings'),
                                            'phpinfo'  => array('title' => PHPINFO, 'link' => 'index.php?file=Admin&page=phpinfo'),
                                            'subNav'   => $arraySettings
                                        ),
                        MANAGEMENTS =>  array(
                                            'smilies' => array('title' => SMILIES, 'link' => 'index.php?file=Admin&page=smilies'),
                                            'subNav'   => $arrayManagements
                                        )
                    );

    $breadcrumbs =  array(
                        ADMIN_PANEL_TITLE => 'index.php?file=Admin'
                    );

    $mainTitle = ADMIN_PANEL_TITLE;

    if ((isset($_REQUEST['file']) && $_REQUEST['file'] == 'Admin') && isset($_REQUEST['page'])) {
        foreach ($arrayPages as $navTitle => $navContent) {
            if (array_key_exists($_REQUEST['page'], $navContent)) {
                $mainTitle = $navContent[$_REQUEST['page']]['title'];
                $breadcrumbs[$navTitle]  = $navContent['subNav'];
                $breadcrumbs[$mainTitle] = $navContent[$_REQUEST['page']]['link'];
            }
        }
    }


?>
