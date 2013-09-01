<?php
/**
 * french.lang.php
 *
 * Admin french language constants
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

$arrayAdminLang = array(
    #####################################
    # Admin - Panel
    #####################################
    'ADMIN_PANEL_TITLE'    => 'Panel d\'administration',
    'PANEL'                => 'Panneau',
    'MANAGEMENTS'          => 'Gestions',
    'GENERAL_SETTINGS'     => 'Pr&eacute;f&eacute;rences g&eacute;n&eacute;rales',
    'MYSQL'                => 'MySql',
    'PHPINFO'              => 'PHP Info',
    'ACTIONS'              => 'Actions',
    'SQL_ERRORS'           => 'Erreurs SQL',
    'OFFICIAL_FORUM'       => 'Forum officiel Nuked-KLan',
    'ABOUT'                => 'A propos',
    'LICENSE'              => 'Licence',
    'VISITS'               => 'Visites',
    'PAGES_VIEWS'          => 'Pages vues',
    'STATS'                => 'Statistiques',
    'CLOSE'                => 'Fermer',
    #####################################
    # Admin - Login page
    #####################################
    'CHECK_IN_PROGRESS'    => 'V&eacute;rification en cours...',
    'EMPTY_FIELDS'         => 'Vous devez remplir tous les champs !',
    'BAD_MAIL'             => 'L\'adresse e-mail ne correspond pas !',
    'BAD_PASSWORD'         => 'Le mot de passe ne correspond pas !',
    'LOGIN_SUCCESS'        => 'Connexion r&eacute;ussie, vous allez &ecirc;tre redirig&eacute; !',
    #####################################
    # Admin - Home page
    #####################################
    'NEWS_NKORG'           => 'Actualit&eacute;s de Nuked-Klan.org',
    'NK_UPDATE'            => 'Mis &agrave; jour du CMS',
    'VERSION_UP_TO_DATE'   => 'Votre version de Nuked-Klan est &agrave; jour !',
    #####################################
    # Admin - General Settings
    #####################################
    'STATS_ALERT'               => 'Activer les statistiques consomment beaucoup de ressources sur votre base SQL ! <br />Pensez à vider les statistiques de temps en temps depuis l\'administration.',
    'GENERAL'                   => 'G&eacute;n&eacute;ral',
    'WEBSITE_NAME'              => 'Nom du site',
    'SLOGAN'                    => 'Slogan',
    'TAG_PREFIX'                => 'Tag pr&eacute;fix',
    'TAG_SUFFIX'                => 'Tag suffix',
    'WEBSITE_URL'               => 'Url du site',
    'DATE_FORMAT'               => 'Format de la date',
    'DATE_ZONE'                 => 'Fuseau horaire',
    'DATE_ADJUSTEMENT'          => 'En prenant en compte de votre ajustement, nous sommes le :',
    'ADMIN_MAIL'                => 'E-mail de l\'administrateur',
    'FOOTER_MESSAGE'            => 'Message en bas de page',
    'WEBSITE_STATUS'            => 'Statut du site',
    'WEBSITE_INDEX'             => 'Index du site',
    'DEFAULT_THEME'             => 'Th&egrave;me par d&eacute;faut',
    'DEFAULT_LANGUAGE'          => 'Langue par d&eacute;faut',
    'PREVIEW'                   => 'Pr&eacute;visualisation',
    'REGISTRATIONS'             => 'Inscriptions',
    'BY_MAIL'                   => 'Par mail',
    'VALIDATION'                => 'Validation',
    'AUTO'                      => 'Automatique',
    'DELETE_THEMSELVES'         => 'Autoriser les membres &agrave; supprimer leur compte',
    'EDITOR'                    => 'Editeur',
    'VIDEOS_EDITOR'             => 'Autoriser l\'ajout de vid&eacute;os dans l\'&eacute;diteur (Youtube, Dailymotion, etc...)',
    'SCAYT_EDITOR'              => 'Activer le correcteur orthographique SCAYT dans l\'&eacute;diteur',
    'WEBSITE_MEMBERS'           => 'Membres du site',
    'MEMBERS_PER_PAGE'          => 'Membres par page',
    'AVATARS'                   => 'Avatars',
    'ALLOW_AVATAR_UPLOAD'       => 'Autoriser l\'upload d\'avatars',
    'ALLOW_EXTERNAL_AVATAR'     => 'Autoriser les avatars externes (liens)',
    'REGISTRATION_MAIL'         => 'Etre averti par email des nouvelles inscriptions',
    'REGISTRATION_DISCLAIMER'   => 'Charte - r&egrave;glement de l\'inscription',
    'REGISTRATION_MAIL_CONTENT' => 'Contenu de l\'email d\'inscription',
    'VISIT_TIME'                => 'Dur&eacute;e en minutes d\'une visite',
    'STATS_ACCESS'              => 'Groupes autoris&eacute;s &agrave; voir l\'analyse des visites',
    'DISPLAY_GENERATE_TIME'     => 'Afficher le temps de g&eacute;n&eacute;ration en bas de page',
    'SHARE_STATS'               => 'Partagez vos statistiques',
    'SEE_SHARED_STATS'          => 'Voir les statistiques partag&eacute;es',
    'SHARE_STATS_INFO'          => 'Ce service &agrave; pour but de nous aider &agrave; am&eacute;liorer les services Nuked-Klan tout en gardant votre anonymat.',
    'CONNECTION_OPTIONS'        => 'Options de connexion',
    'COOKIE_NAME'               => 'Nom du cookie',
    'SESSION_TIME'              => 'Dur&eacute;e en minutes d\'une session',
    'COOKIE_TIME'               => 'Dur&eacute;e en jours d\'une session cookie',
    'CONNECTED_TIME'            => 'Dur&eacute;e en seconde du Time-Out du Compteur live',
    'META_TAGS'                 => 'Meta tags',
    'KEYWORDS'                  => 'Mots-cl&eacute;s',
    'WEBSITE_DESCRIPTION'       => 'Description du site',
    'GENERAL_SETTINGS_SAVED'    => 'Pr&eacute;f&eacute;rences g&eacute;n&eacute;rales sauvegard&eacute;es',
    'CHAR_NOT_ALLOW'            => 'Caract&egrave;re non autoris&eacute; !',
    #####################################
    # Admin - Actions
    #####################################
    'ACTIONS_MANAGEMENT'        => 'Gestion des actions',
    'ACTIONS_AUTO_DELETE'       => 'Apr&egrave;s votre lecture, les actions d&eacute;passant 2 semaines seront supprim&eacute;es d&eacute;finitivement.',
    'ACTION_CONNECT'            => 's\'est connect&eacute; &agrave; l\'administration',
    'ACTION_DISCONNECT'         => 's\'est d&eacute;connect&eacute; de l\'administration',
    'ACTION_SAVE_DB'            => 'a sauvegard&eacute; la base de donn&eacute;e',
    'ACTION_OPTIMIZE_DB'        => 'a optimis&eacute; la base de donn&eacute;e',
    'ACTION_PURGE_SQL_ERRORS'   => 'a supprim&eacute; les erreurs SQL',
    'ACTION_GENERAL_SETTINGS'   => 'a modifi&eacute; les pr&eacute;f&eacute;rences g&eacute;n&eacute;rales',
    'ACTION_DEL_NOTIFICATION'   => 'a supprim&eacute; une notification',
    'ACTION_MODIFY_BLOCK'       => 'a modifi&eacute; le bloc',
    'ACTION_ADD_BLOCK'          => 'a ajout&eacute; le bloc',
    'ACTION_DEL_BLOCK'          => 'a supprim&eacute; le bloc',
    'ACTION_EDIT_MODULES'       => 'a modifi&eacute; les modules',
    'ACTION_DELETE_SMILEY'      => 'a supprim&eacute; l\'&eacute;moticone',
    'ACTION_EDIT_SMILEY'        => 'a modifi&eacute; l\'&eacute;moticone',
    'ACTION_ADD_SMILEY'         => 'a ajout&eacute; l\'&eacute;moticone',
    #####################################
    # Admin - Smilies Management
    #####################################
    'SMILIES_MANAGEMENT'   => 'Gestion des &eacute;moticones',
    'SMILEY'               => 'Emoticone',
    'CODE'                 => 'Code',
    'SMILEY_ADD'           => 'Ajouter un &eacute;moticone',
    'UPLOAD_SMILEY'        => 'Envoyer un &eacute;moticone',
    'SMILEY_DELETED'       => 'Smiley supprim&eacute; avec succ&egrave;s',
    'SMILEY_UPLOAD_FAILED' => 'Une erreur est survenue lors de l\'upload du smiley',
    'DELETE_SMILEY'        => 'Supprimer le smiley ',
    'SMILEY_ADDED'         => 'Smiley ajout&eacute; avec succ&egrave;s',
    'SMILEY_EDITED'        => 'Smiley modifi&eacute; avec succ&egrave;s',
    'SMILEY_BAD_CODE'      => 'Le code de l\'&eacute;moticone ne peut contenir de quotes (\' ou ") et ne peut &ecirc;tre &eacute;gale au nom',
    'BAD_IMAGE_FILE'       => 'L\'image que vous avez envoyé n\'est pas valide, seules les extensions JPG, PNG et GIF sont autorisées',
);

?>
