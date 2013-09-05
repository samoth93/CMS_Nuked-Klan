<?php
/**
 * constants.php
 *
 * Create constants for tables names
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

//Update param
define('UPDATE_URL', 'http://nuked-klan.org/');

$arrayConstants =   array(
    'ACTIONS_TABLE'        => '_actions',
    'BANNED_TABLE'         => '_banned',
    'BLOCK_TABLE'          => '_block',
    'CALENDAR_TABLE'       => '_calendar',
    'COMMENT_TABLE'        => '_comment',
    'CONFIG_TABLE'         => '_config',
    'CONTACT_TABLE'        => '_contact',
    'DEFY_TABLE'           => '_defie',
    'DEFY_PREF_TABLE'      => '_defie_pref',
    'DOWNLOAD_TABLE'       => '_downloads',
    'DOWNLOAD_CAT_TABLE'   => '_downloads_cat',
    'FORUM_TABLE'          => '_forums',
    'FORUM_CAT_TABLE'      => '_forums_cat',
    'FORUM_MESSAGES_TABLE' => '_forums_messages',
    'FORUM_OPTIONS_TABLE'  => '_forums_options',
    'FORUM_POLL_TABLE'     => '_forums_poll',
    'FORUM_RANK_TABLE'     => '_forums_rank',
    'FORUM_READ_TABLE'     => '_forums_read',
    'FORUM_THREADS_TABLE'  => '_forums_threads',
    'FORUM_VOTE_TABLE'     => '_forums_vote',
    'GALLERY_TABLE'        => '_gallery',
    'GALLERY_CAT_TABLE'    => '_gallery_cat',
    'GAMES_TABLE'          => '_games',
    'GAMES_PREFS_TABLE'    => '_games_prefs',
    'GROUPS_TABLE'         => '_groups',
    'GUESTBOOK_TABLE'      => '_guestbook',
    'IRC_AWARDS_TABLE'     => '_irc_awards',
    'LINKS_TABLE'          => '_liens',
    'LINKS_CAT_TABLE'      => '_liens_cat',
    'MODULES_TABLE'        => '_modules',
    'NBCONNECTE_TABLE'     => '_nbconnecte',
    'NEWS_TABLE'           => '_news',
    'NEWS_CAT_TABLE'       => '_news_cat',
    'NOTIFICATIONS_TABLE'  => '_notification',
    'PAGE_TABLE'           => '_page',
    'RECRUIT_TABLE'        => '_recrute',
    'RECRUIT_PREF_TABLE'   => '_recrute_pref',
    'SECTIONS_TABLE'       => '_sections',
    'SECTIONS_CAT_TABLE'   => '_sections_cat',
    'SERVER_TABLE'         => '_serveur',
    'SERVER_CAT_TABLE'     => '_serveur_cat',
    'SESSIONS_TABLE'       => '_sessions',
    'SMILIES_TABLE'        => '_smilies',
    'STATS_TABLE'          => '_stats',
    'STATS_VISITOR_TABLE'  => '_stats_visitor',
    'SUGGEST_TABLE'        => '_suggest',
    'SURVEY_TABLE'         => '_sondage',
    'SURVEY_CHECK_TABLE'   => '_sondage_check',
    'SURVEY_DATA_TABLE'    => '_sondage_data',
    'TEAM_TABLE'           => '_team',
    'TMPSES_TABLE'         => '_tmpses',
    'TEAM_RANK_TABLE'      => '_team_rank',
    'TEXTBOX_TABLE'        => '_shoutbox',
    'USERBOX_TABLE'        => '_userbox',
    'USERS_TABLE'          => '_users',
    'USERS_DETAIL_TABLE'   => '_users_detail',
    'VOTE_TABLE'           => '_vote',
    'WARS_TABLE'           => '_match',
    'WARS_FILES_TABLE'     => '_match_files'
);

foreach ($arrayConstants as $constant => $tableName) {
    define($constant, $GLOBALS['nuked']['prefix'].$tableName);
}

unset($arrayConstants);

?>
