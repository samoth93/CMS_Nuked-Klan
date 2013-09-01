<?php
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');
// PHP ERROR NK
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2) == 'fr'){
    define('ERROR_SESSION', 'Erreur dans la création de la session anonyme');
    define('THEME_NOTFOUND','Erreur fatale : Impossible de trouver le thème');
    define('ERROR_QUERY','Veuillez nous excuser, le site web est actuellement indisponible !<br />Information :<br />Connexion SQL impossible.');
    define('ERROR_QUERYDB','Veuillez nous excuser, le site web est actuellement indisponible !<br />Information :<br />Nom de base de données sql incorrect.');
    define('DBPREFIX_ERROR', 'Impossible de se connecter à la base de données ! Vérifier que la variable $db_prefix du fichier conf.inc.php correspond au préfixe de vos tables.');
}
else{
    define('ERROR_SESSION', 'Error in creating the anonymous session');
    define('THEME_NOTFOUND','Fatal error : No theme found');
    define('ERROR_QUERY','Sorry but the website is not available !<br />Information :<br />SQL connection impossible.');
    define('ERROR_QUERYDB','Sorry but the website is not available !<br />Information :<br />Database SQL name incorrect.');
    define('DBPREFIX_ERROR', 'Can\'t connect to the database ! Check that $db_prefix variable on conf.inc.php file match with your prefix tables.');
}
?>
