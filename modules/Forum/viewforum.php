<?php
/**
 * Main page of Forum Mod
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

// Inclusion des variables de langue
translate('modules/Forum/lang/'.$GLOBALS['language'].'.lang.php');

// Ouverture du conteneur de module
opentable();

// ################################
// @todo : Mettre a jour avec les groupes
// ################################
// On définit le niveau du visiteur
$visiteur = $GLOBALS['user'] ? $GLOBALS['user'][1] : 0;
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1){

    // On récupère et on sécurise l'ID du forum
    if(array_key_exists('forum_id', $_REQUEST) && !empty($_REQUEST['forum_id'])){
        $_CLEAN['forumId'] = intval($_REQUEST['forum_id']);
    }
    else{
        echo '<div class="nkNotificationMod nkSuccessMod">'._BAD_FORUM_ID.'</div>';
        redirect('index.php?file=Forum', 3);
        footer();
        exit();
    }

    // On récupère les informations du forum en base de donnée
    $dbsForum = "SELECT A.id AS forumId, A.cat, A.parentid, A.nom AS name, A.comment, A.image, A.niveau AS level, C.id AS catId, C.nom AS catName,
                    (SELECT count(id) FROM ".FORUM_THREADS_TABLE." WHERE forum_id = forumId) AS nbThreads,
                    (SELECT nom FROM ".FORUM_TABLE." WHERE id = A.parentid) AS parentName
                 FROM ".FORUM_TABLE." AS A
                 LEFT JOIN ".FORUM_TABLE." AS B
                 ON B.id = A.parentid
                 LEFT JOIN ".FORUM_CAT_TABLE." AS C
                 ON C.id = A.cat
                 WHERE A.id = ".$_CLEAN['forumId']."
                 ";

    $dbeForum = mysql_query($dbsForum) or die(mysql_error());

    // On stocke les données du forum dans la variable dataForum
    $arrayForum = mysql_fetch_assoc($dbeForum);

    // On récupère le nombre de sujets maximum a affiché (définit dans l'administration)
    $maxThreadsPerPage = $GLOBALS['nuked']['thread_forum_page'];

    // On définit la page courante sur 1 par défaut
    $page = 1;

    // On vérifie la présence et on sécurise la variable p qui gère la page courante via l'url
    if(array_key_exists('p', $_REQUEST) && !empty($_REQUEST['p'])){
        $page = $_REQUEST['p'];
    }

    $currentItem = $page * $maxThreadsPerPage - $maxThreadsPerPage;

    // On récupère les informations des sujets en base de donnée
    $dbsThreads = "SELECT  id AS threadId, titre AS title, date, closed AS status, auteur AS author,
                           auteur_id AS authorId, last_post AS lastPost, view AS nbViews, annonce, sondage AS survey, forum_id AS forumId,
                            (SELECT auteur
                             FROM ".FORUM_MESSAGES_TABLE."
                             WHERE thread_id = threadId
                             ORDER BY date DESC
                             LIMIT 0,1
                            ) AS lastMsgAuthor,
                            (SELECT id
                             FROM ".FORUM_MESSAGES_TABLE."
                             WHERE thread_id = threadId
                             ORDER BY date DESC
                             LIMIT 0,1
                            ) AS lastMsgId,
                            (SELECT count(id) - 1
                             FROM ".FORUM_MESSAGES_TABLE."
                             WHERE thread_id = threadId
                            ) AS nbMessages
                   FROM ".FORUM_THREADS_TABLE."
                   WHERE forum_id = ".$_CLEAN['forumId']."
                   ORDER BY lastPost DESC
                   LIMIT ".$currentItem.", ".$maxThreadsPerPage." ";
    $dbeThreads = mysql_query($dbsThreads) or die(mysql_error());

    // On initialise le tableau des sujets
    $arrayThreads = array();

    // On stocke les données dans le tableau des sujets
    while ($dataThread = mysql_fetch_assoc($dbeThreads)) {
        foreach ($dataThread as $field => $value) {
            if($field != 'threadId'){
                // Si le titre est trop long on le tronque
                if($field == 'title' && strlen($value) > 60){
                    $value = substr($value, 0, 60).'&nbsp;...';
                }

                $arrayThreads[$dataThread['threadId']][$field] = $value;
            }
        }
    }

    // On créer un tableau qui va contenir les différents pseudo à affiché
    $arrayWhereAuthor = array();

    foreach ($arrayThreads as $thread) {
        $arrayWhereAuthor[] = $thread['author'];
        $arrayWhereAuthor[] = $thread['lastMsgAuthor'];
    }

    // On supprime les doublons
    $arrayWhereAuthor = array_unique($arrayWhereAuthor);

    $where = null;
    $i = 0;
    // On créer le where pour la requete sur la table user
    if(count($arrayWhereAuthor) > 0){
        $where = implode('\' OR pseudo = \'', $arrayWhereAuthor);
    }

    $where = 'WHERE pseudo = \''.$where.'\'';

    // On récupère les informations des auteurs en base de donnée
    $dbsAuthors = " SELECT pseudo, avatar
                    FROM ".USER_TABLE."
                    ".$where." ";
    $dbeAuthors = mysql_query($dbsAuthors);

    // On stocke les données dans le tableau des auteurs
    $arrayAuthors = array();

    while($dataAuthors = mysql_fetch_assoc($dbeAuthors)){
        $arrayAuthors[$dataAuthors['pseudo']] = $dataAuthors['avatar'];
    }

    // On intègre les avatars dans le tableau des threads
    foreach($arrayThreads as $threadId => $thread){
        if(array_key_exists($thread['lastMsgAuthor'], $arrayAuthors)){
            $arrayThreads[$threadId]['avatar'] = $arrayAuthors[$thread['lastMsgAuthor']];
        }
        else{
            $arrayThreads[$threadId]['avatar'] = 'modules/Forum/images/noAvatar.png';
        }
    }

    $navPage = null;
    // On affiche la navigation des pages s'il y a plus de topic que le maximum autorisé
    if($arrayForum['nbThreads'] > $maxThreadsPerPage){
        $navPage = number($arrayForum['nbThreads'], $maxThreadsPerPage, 'index.php?file=Forum&page=viewforum&forum_id='.$_CLEAN['forumId']);
    }

    $cat = '-> <a href="index.php?file=Forum&amp;cat='.$arrayForum['catId'].'"><strong>'.$arrayForum['catName'].'</strong></a>&nbsp;';

    $parentForum = null;

    if($arrayForum['parentid'] != 0){
        $parentForum = '-> <a href="index.php?file=Forum&forumCat='.$arrayForum['parentid'].'">
                                <strong>'.$arrayForum['parentName'].'</strong>
                            </a>&nbsp;';
    }

    $forum = '-> <strong>'.$arrayForum['name'].'</strong>&nbsp;';

    $nav = $cat.$parentForum.$forum;

    // #################################
    // # @todo : REMPLACER LE $GLOBALS['user'][0] par $GLOBALS['user']['id']
    // #################################
    // On inclus le statut de visite (forum vu ou non) dans les forums
    foreach($arrayThreads as $threadId => $thread){
        $arrayThreads[$threadId]['classThreadRead'] = null;
    }

    if (isset($GLOBALS['user']) && !empty($GLOBALS['user'])){
        $dbsThreadsVisit = "SELECT thread_id AS threadId
                           FROM ".FORUM_READ_TABLE."
                           WHERE user_id = '".$GLOBALS['user'][0]."'
                          ";
        $dbeThreadsVisit = mysql_query($dbsThreadsVisit);
        $dataThreadsRead = mysql_fetch_assoc($dbeThreadsVisit);

        foreach($arrayThreads as $threadId => $thread){
            $arrayThreadsRead = explode(',', $dataThreadsRead['threadId']);
            $newTopic = false;
            if($arrayThreads[$threadId]['nbMessages'] > 0 && !in_array($threadId, $arrayThreadsRead)){
                $newTopic = true;
                $arrayThreads[$threadId]['classThreadRead'] = 'nkForumNewTopic';
            }

            if($thread['nbMessages'] > $GLOBALS['nuked']['hot_topic']){
                if($newTopic === true){
                    $arrayThreads[$threadId]['classThreadRead'] = 'nkForumNewTopicPopular';
                }
                else{
                    $arrayThreads[$threadId]['classThreadRead'] = 'nkForumTopicPopular';
                }
            }

            if($thread['status'] == 1){
                if($newTopic === true){
                    $arrayThreads[$threadId]['classThreadRead'] = 'nkForumNewTopicLock';
                }
                else{
                    $arrayThreads[$threadId]['classThreadRead'] = 'nkForumTopicLock';
                }
            }
        }
    }

    // ------------------------------
    // AFFICHAGE
    // ------------------------------
    if(isset($GLOBALS['bgcolor1']) && isset($GLOBALS['bgcolor2']) && isset($GLOBALS['bgcolor3']) && isset($GLOBALS['bgcolor4'])){
    ?>
        <style type="text/css">
            .nkForumCatHead, #nkForumWhoIsOnline{
                background: <?php echo $GLOBALS['bgcolor3']; ?>
            }
        </style>
    <?php
    }
?>
    <div id="nkForumWrapper">
        <div id="nkForumInfos">
<?php
            if(!empty($arrayForum['image'])){
                echo '<img src="'.$arrayForum['image'].'" alt="" />';
            }
?>          <div>
                <h2><?php echo $arrayForum['name']; ?></h2>
                <p><?php echo $arrayForum['comment'] ?></p>
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;<?php echo $nav; ?>
        </div>
        <div class="nkForumNavPage">
            <?php echo $navPage; ?>
        </div>
        <div class="nkForumCat">
            <div class="nkForumCatWrapper">
                <div class="nkForumCatHead">
                    <div>
                        <div class="nkForumBlankCell"></div>
                        <div class="nkForumForumCell"><?php echo _TOPIC; ?></div>
                        <div class="nkForumStatsCell"><?php echo _STATS; ?></div>
                        <div class="nkForumDateCell"><?php echo _LASTPOST; ?></div>
                    </div>
                </div>
                <div class="nkForumCatContent">
<?php
                    foreach($arrayThreads as $threadId => $thread){
?>                      <div>
                            <div class="nkForumIconCell">
                                <span class="nkForumTopicIcon <?php echo $thread['classThreadRead']; ?>"></span>
                            </div>
                            <div class="nkForumForumCell">
                                <a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $thread['forumId']; ?>&amp;thread_id=<?php echo $threadId; ?>">
                                    <h3><?php echo $thread['title']; ?></h3>
                                </a>
                                <div>
                                    <span>
                                        <?php echo _CREATED_BY; ?>
<?php
                                        if(array_key_exists($thread['author'], $arrayAuthors)){
?>
                                            <a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo $thread['author']; ?>">
                                                <?php echo $thread['author']; ?>
                                            </a>
<?php
                                        }
                                        else{
                                            echo $thread['author'];
                                        }
?>
                                        <?php echo _THE.'&nbsp;'.nkdate($thread['date']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="nkForumStatsCell">
                                <strong><?php echo $thread['nbMessages']; ?></strong>&nbsp;<?php echo strtolower(_ANSWERS); ?>
                                <br/>
                                <strong><?php echo $thread['nbViews']; ?></strong>&nbsp;<?php echo strtolower(_VIEWS); ?>
                            </div>
                            <div class="nkForumDateCell">
                                <div class="nkForumAuthorAvatar">
                                    <img src="<?php echo $thread['avatar']; ?>" alt="" />
                                </div>
                                <div>
                                    <p>
                                        <span><?php echo _BY; ?></span>
<?php
                                        if(array_key_exists($thread['lastMsgAuthor'], $arrayAuthors)){
?>
                                            <a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo $thread['lastMsgAuthor']; ?>">
                                                <strong><?php echo $thread['lastMsgAuthor']; ?></strong>
                                            </a>
<?php
                                        }
                                        else{
                                            echo '<strong>'.$thread['lastMsgAuthor'].'</strong>';
                                        }
?>
                                    </p>
                                    <p><?php echo nkDate($thread['lastPost']); ?></p>
                                </div>
                            </div>
                        </div>
<?php
                    }
?>
                </div>
            </div>

        </div>
        <div class="nkForumNavPage">
            <?php echo $navPage; ?>
        </div>
    </div>
<?php
}
// ################################
// @todo : Mettre a jour avec les groupes
// ################################
else if ($level_access == -1) {
    // On affiche le message qui previent l'utilisateur que le module est désactivé
    echo '<div class="nkAccessModError">
            <p>'._MODULEOFF.'</p>
            <a href="javascript:history.back()"><strong>'._BACK.'</strong></a>
        </div>';
}
else if ($level_access == 1 && $visiteur == 0) {
    // On affiche le message qui previent l'utilisateur qu'il n'as pas accès à ce module
    echo '<div class="nkAccessModError">
            <p>'._USERENTRANCE.'</p>
            <a href="index.php?file=User&amp;op=login_screen"><strong>'._LOGINUSER.'</strong></a>
            &nbsp;|&nbsp;
            <a href="index.php?file=User&amp;op=reg_screen"><strong>'._REGISTERUSER.'</strong></a>
        </div>';
}
else {
    // On affiche le message qui previent l'utilisateur que le module est désactivé
    echo '<div class="nkAccessModError">
            <p>'._NOENTRANCE.'</p>
            <a href="javascript:history.back()"><strong>'._BACK.'</strong></a>
        </div>';
}

// Fermeture du conteneur de module
closetable();

// ######################################
// MARQUER DE MODIFICATION
// ######################################

global $nuked, $user, $language, $cookie_forum;

translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Forum/template.php");

opentable();

if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    $nb_mess_for = $nuked['thread_forum_page'];

    if ($_REQUEST['date_max'] != "")
    {
        $date_jour = time();
        $date_select = $date_jour - $_REQUEST['date_max'];
    }

    if ($_REQUEST['date_max'] != "")
    {
        $sql2 = mysql_query("SELECT forum_id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND date > '" . $date_select . "' ORDER BY last_post DESC");
    }
    else
    {
        $sql2 = mysql_query("SELECT forum_id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY last_post DESC");
    }

    $count = mysql_num_rows($sql2);

    $p = !$_GET['p']?1:$_GET['p'];
    $start = $p * $nb_mess_for - $nb_mess_for;

    $sql = mysql_query("SELECT nom, moderateurs, cat, level FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
    $level_ok = mysql_num_rows($sql);

    if ($level_ok == 0)
    {
        echo "<br /><br /><div style=\"text-align: center;\">" . _NOACCESSFORUM . "</div><br /><br />";
    }
    else
    {
        list($nom, $modos, $cat, $level) = mysql_fetch_array($sql);
        $nom = printSecuTags($nom);

        $sql_cat = mysql_query("SELECT nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cat . "'");
        list($cat_name) = mysql_fetch_array($sql_cat);
        $cat_name = printSecuTags($cat_name);

        if ($modos != "")
        {
            $moderateurs = explode('|', $modos);
            for ($i = 0;$i < count($moderateurs);$i++)
            {
                if ($i > 0) $sep = ",&nbsp;";
                $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $moderateurs[$i] . "'");
                list($modo_pseudo) = mysql_fetch_row($sql2);
                $modo .= $sep . $modo_pseudo;
            }
        }
        else
        {
            $modo = _NONE;
        }

        if ($user && $modos != "" && strpos($user[0], $modos))
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        echo "<br /><table width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\">\n"
     ."<tr><td><big><b>" . $nom . "</b></big><small> -  " . _MODO . " : " . $modo . "</small></td></tr>\n"
    . "<tr><td valign=\"bottom\"><a href=\"index.php?file=Forum\"><b>" . _INDEXFORUM . "</b></a> -&gt; <a href=\"index.php?file=Forum&amp;cat=" . $cat . "\"><b>" . $cat_name . "</b></a> -&gt; <b>" . $nom . "</b>\n";

        if ($count > $nb_mess_for)
        {
            echo "<br /><br />\n";
            $url_page = "index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;date_max=" . $_REQUEST['date_max'];
            number($count, $nb_mess_for, $url_page);
        }

        echo "</td><td align=\"right\" valign=\"bottom\">";

        if ($level == 0 || $user[1] >= $level || $administrator == 1)
        {
            echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/newthread.gif\" alt=\"\" title=\"" . _NEWSTOPIC . "\" /></a>";
        }

        if ($user)
        {
            echo "<br /><a href=\"index.php?file=Forum&amp;op=mark&amp;forum_id=" . $_REQUEST['forum_id'] . "\">" . _MARKSUBJECTREAD . "</a>";
        }

        echo "</td></tr></table>\n"
    . "<table style=\"background: " . $color3 . ";\" width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\">\n"
    . "<tr " . $background . "><td style=\"width: 4%;\">&nbsp;</td>\n"
        . "<td style=\"width: 40%;\" align=\"center\"><b>" . _SUBJECTS . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
    . "<td style=\"width: 8%;\" align=\"center\"><b>" . _ANSWERS . "</b></td>\n"
    . "<td style=\"width: 8%;\" align=\"center\"><b>" . _VIEWS . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _LASTPOST . "</b></td></tr>\n";

        if ($count == 0)
        {
            echo "<tr style=\"background: " . $color2 . ";\"><td colspan=\"6\" align=\"center\">" . _NOPOSTFORUM . "</td></tr>\n";
        }

        if ($_REQUEST['date_max'] != "")
        {
            $sql3 = mysql_query("SELECT id, titre, auteur, view, closed, annonce, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND date > '" . $date_select . "' ORDER BY annonce DESC, last_post DESC LIMIT " . $start . ", " . $nb_mess_for."");
        }
        else
        {
            $sql3 = mysql_query("SELECT id, titre, auteur, auteur_id, view, closed, annonce, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY annonce DESC, last_post DESC LIMIT " . $start . ", " . $nb_mess_for."");
        }

        while (list($thread_id, $titre, $auteur, $auteur_id, $nb_read, $closed, $annonce, $sondage) = mysql_fetch_row($sql3))
        {
            $sql8 = mysql_query("SELECT txt FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "' ORDER BY id LIMIT 0, 1");
            list($txt) = mysql_fetch_array($sql8);

            $auteur = nk_CSS($auteur);

            $txt = str_replace("\r", "", $txt);
            $txt = str_replace("\n", " ", $txt);

            $texte = strip_tags($txt);

            if (!preg_match("`[a-zA-Z0-9\?\.]`i", $texte))
            {
                $texte = _NOTEXTRESUME;
            }

            if (strlen($texte) > 150)
            {
                $texte = substr($texte, 0, 150) . "...";
            }

            $texte = nkHtmlEntities($texte);
            $texte = nk_CSS($texte);

            $title = nkHtmlEntities(printSecuTags($titre));

            if (strlen($titre) > 30)
            {
                $titre_topic = "<a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "\" onmouseover=\"AffBulle('" . mysql_real_escape_string(stripslashes($title)) . "', '" . mysql_real_escape_string(stripslashes($texte)) . "', 400)\" onmouseout=\"HideBulle()\"><b>" . printSecuTags(substr($titre, 0, 30)) . "...</b></a>";
            }
            else
            {
                $titre_topic = "<a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "\" onmouseover=\"AffBulle('" . mysql_real_escape_string(stripslashes($title)) . "', '" . mysql_real_escape_string(stripslashes($texte)) . "', 400)\" onmouseout=\"HideBulle()\"><b>" . printSecuTags($titre) . "</b></a>";
            }

            $sql4 = mysql_query("SELECT file FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            $nb_rep = mysql_num_rows($sql4) - 1;

            $fichier_joint = 0;
            while (list($url_file) = mysql_fetch_row($sql4))
            {
                if ($url_file != "") $fichier_joint++;
            }

            $sql6 = mysql_query("SELECT MAX(id) FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            $idmax = mysql_result($sql6, 0, "MAX(id)");

            $sql7 = mysql_query("SELECT id, date, auteur, auteur_id FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $idmax . "'");
            list($mess_id, $last_date, $last_auteur, $last_auteur_id) = mysql_fetch_array($sql7);
            $last_auteur = nk_CSS($last_auteur);


               if ($user) {
                    $visitx = mysql_query("SELECT user_id FROM " . FORUM_READ_TABLE . " WHERE user_id = '" . $user[0] . "' AND `thread_id` LIKE '%" . ',' . $thread_id . ',' . "%' ");
                    $results = mysql_num_rows($visitx);
                         $user_visitx = $results;
               } else {
                $user_visitx = 0;
            }
            if ($user && $closed == 1 && ($user_visitx == 0))
            {
                $img = "<img src=\"modules/Forum/images/folder_new_lock.gif\" alt=\"\" />";
            }
            else if ($closed == 1)
            {
                $img = "<img src=\"modules/Forum/images/folder_lock.gif\" alt=\"\" />";
            }
            else if ($user && $nb_rep >= $nuked['hot_topic'] && ($user_visitx == 0))
            {
                $img = "<img src=\"modules/Forum/images/folder_new_hot.gif\" alt=\"\" />";
            }
            else if ($user && ($user_visitx >= 0) && $nb_rep >= $nuked['hot_topic'])
            {
                $img = "<img src=\"modules/Forum/images/folder_hot.gif\" alt=\"\" />";
            }
            else if ($user && ($user_visitx == 0) && $nb_rep < $nuked['hot_topic'])
            {
                $img = "<img src=\"modules/Forum/images/folder_new.gif\" alt=\"\" />";
            }
            else
            {
                $img = "<img src=\"modules/Forum/images/folder.gif\" alt=\"\" />";
            }


            if ($annonce == 1)
            {
                $a_img = "<img src=\"modules/Forum/images/announce.gif\" alt=\"\" title=\"" . _ANNOUNCE . "\" />&nbsp;";
            }
            else
            {
                $a_img = "";
            }

            if ($sondage == 1)
            {
                $s_img = "<img src=\"modules/Forum/images/poll.gif\" alt=\"\" title=\"" . _SURVEY . "\" />&nbsp;";
            }
            else
            {
                $s_img = "";
            }

            if ($fichier_joint > 0)
            {
                $f_img = "<img src=\"modules/Forum/images/clip.gif\" alt=\"\" title=\"" . _ATTACHFILE . " (" . $fichier_joint . ")\" />&nbsp;";
            }
            else
            {
                $f_img = "";
            }

            $title = $a_img . $s_img . $f_img . $titre_topic;

            $posts = $nb_rep + 1;
            if ($posts > $nuked['mess_forum_page'])
            {
                $topicpages = $posts / $nuked['mess_forum_page'];
                $topicpages = ceil($topicpages);

                $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "&amp;p=" . $topicpages . "#" . $mess_id;

                for ($l = 1; $l <= $topicpages; $l++)
                {
                    $pagelinks .= " <a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "&amp;p=" . $l . "\">" . $l . "</a>";
                }

                $multipage2 = "<small>( " . _PAGES . ": " . $pagelinks . " )</small>";
                $pagelinks = "";
            }
            else
            {
                $multipage2 = "";
                $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "#" . $mess_id;
            }

            if ($auteur_id != "")
            {
                $sql5 = mysql_query("SELECT pseudo, country FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                $test = mysql_num_rows($sql5);
                list($autor, $country) = mysql_fetch_array($sql5);

                if ($test > 0 && $autor != "")
                {
                    $initiat = "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\"><b>" . $autor . "</b></a>";
                }
                else
                {
                    $initiat = "<b>" . $auteur . "</b>";
                }
            }
            else
            {
                $initiat = "<b>" . $auteur . "</b>";
            }

            echo "<tr style=\"background: " . $color2 . ";\"><td style=\"width: 4%;\" align=\"center\">" . $img . "</td>\n"
            . "<td style=\"width: 40%;\" onmouseover=\"this.style.backgroundColor='" . $color1 . "'\" onmouseout=\"this.style.backgroundColor='" . $color2 . "'\">&nbsp;" . $title . $multipage2 . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $initiat . "</td>\n"
            . "<td style=\"width: 8%;\" align=\"center\">" . $nb_rep . "</td>\n"
            . "<td style=\"width: 8%;\" align=\"center\">" . $nb_read . "</td>\n";

            if (strftime("%d %m %Y", time()) ==  strftime("%d %m %Y", $last_date)) $last_date = _FTODAY . "&nbsp;" . strftime("%H:%M", $last_date);
            else if (strftime("%d", $last_date) == (strftime("%d", time()) - 1) && strftime("%m %Y", time()) == strftime("%m %Y", $last_date)) $last_date = _FYESTERDAY . "&nbsp;" . strftime("%H:%M", $last_date);
            else $last_date = nkDate($last_date);



            echo "<td style=\"width: 25%;\" align=\"center\">" . $last_date . "<br />\n";

            if ($last_auteur_id != "")
            {
                $sql8 = mysql_query("SELECT pseudo, country FROM " . USER_TABLE . " WHERE id = '" . $last_auteur_id . "'");
                $test1 = mysql_num_rows($sql8);
                list($last_autor, $last_country) = mysql_fetch_array($sql8);

                if ($test1 > 0 && $last_autor != "")
                {
                    echo "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . $last_autor . "\"><b>" . $last_autor . "</b></a>";
                }
                else
                {
                    echo "<b>" . $last_auteur . "</b>";
                }
            }
            else
            {
                echo "<b>" . $last_auteur . "</b>";
            }

            echo "&nbsp;<a href=\"" . $link_post . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/icon_latest_reply.gif\" alt=\"\" title=\"" . _SEELASTPOST . "\" /></a></td></tr>\n";
        }

        echo "</table><table width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\"><tr><td valign=\"top\">";

        if ($count > $nb_mess_for)
        {
            $url_page = "index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;date_max=" . $_REQUEST['date_max'];
            number($count, $nb_mess_for, $url_page);
        }

        echo "</td><td align=\"right\" valign=\"top\">";

        if ($user)
        {
            echo "<a href=\"index.php?file=Forum&amp;op=mark&amp;forum_id=" . $_REQUEST['forum_id'] . "\">" . _MARKSUBJECTREAD . "</a><br />\n";
        }

        if ($level == 0 || $user[1] >= $level || $administrator == 1)
        {
            echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "\">"
            . "<img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/newthread.gif\" alt=\"\" title=\"" . _NEWSTOPIC . "\" /></a>";
        }

        echo "</td></tr></table>\n"
        . "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">\n"
    . "<tr><td><img src=\"modules/Forum/images/folder_new.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _POSTNEW . "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"modules/Forum/images/folder_lock.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _SUBJECTCLOSE . "</td></tr>\n"
    . "<tr><td><img src=\"modules/Forum/images/folder.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _NOPOSTNEW . "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"modules/Forum/images/folder_new_hot.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _POSTNEWHOT . "</td></tr>\n"
    . "<tr><td><img src=\"modules/Forum/images/folder_new_lock.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _POSTNEWCLOSE . "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"modules/Forum/images/folder_hot.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _NOPOSTNEWHOT . "</td></tr></table><br />\n"
        . "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">\n"
        . "<tr><td><form method=\"post\" action=\"index.php?file=Forum&amp;page=viewforum\">\n"
        . "<div>" . _JUMPTO . " : <select name=\"forum_id\" onchange=\"submit();\">\n"
        . "<option value=\"\">" . _SELECTFORUM . "</option>";

        $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' ORDER BY ordre, nom");
        while (list($forum_name, $fid) = mysql_fetch_row($sql_forum))
        {
            $forum_name = printSecuTags($forum_name);

            echo "<option value=\"" . $fid . "\">" . $forum_name . "</option>\n";
        }

        echo "</select></div></form></td><td>"
        . "<form method=\"post\" action=\"index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "\">\n"
       . "<div>&nbsp;&nbsp;" . _SEETHETOPIC . " : <select name=\"date_max\" onchange=\"submit();\">\n"
    . "<option>" . _THEFIRST . "</option>\n"
    . "<option value=\"86400\">" . _ONEDAY . "</option>\n"
    . "<option value=\"604800\">" . _ONEWEEK . "</option>\n"
    . "<option value=\"2592000\">" . _ONEMONTH . "</option>\n"
    . "<option value=\"15552000\">" . _SIXMONTH . "</option>\n"
    . "<option value=\"31104000\">" . _ONEYEAR . "</option></select></div></form></td></tr></table><br />\n";
    }

}
else if ($level_access == -1)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
}
else if ($level_access == 1 && $visiteur == 0)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
}
else
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
}

closetable();

?>
