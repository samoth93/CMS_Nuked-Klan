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

// ################################
// @todo : Mettre a jour avec les groupes
// ################################
// On définit le niveau du visiteur
$visiteur = $GLOBALS['user'] ? $GLOBALS['user'][1] : 0;
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1){

    // On définit la date de dernière visite du visiteur
    if (empty($GLOBALS['user'][4])) {
        $userLastVisit = nkDate(time());
    }
    else {
        $userLastVisit = nkDate($GLOBALS['user'][4]);
    }

    // On définit la date d'aujourd'hui
    $today = nkDate(time());

    // On prépare le texte d'affichage de la dernière visite
    if (isset($GLOBALS['user']) && !empty($GLOBALS['user'][4])) {
        $textLastVisit = _LASTVISIT.' : '.$userLastVisit;
    }
    else{
        $textLastVisit = null;
    }

    // On affiche le nom du Forum
    if ($GLOBALS['nuked']['forum_title'] != "") {

        $titleForum = $GLOBALS['nuked']['forum_title'];
        $descForum  = $GLOBALS['nuked']['forum_desc'];
    }
    else {
        $titleForum = $GLOBALS['nuked']['name'];
        $descForum  = $GLOBALS['nuked']['slogan'];
    }

    // On récupère toutes les catégorie en base de donnée
    $dbsForumCat = "SELECT id AS catId, nom AS catName, ordre AS catOrdre, niveau AS catLevel
                    FROM ".FORUM_CAT_TABLE."
                    ORDER BY ordre, nom, id";
    $dbeForumCat = mysql_query($dbsForumCat) or die(mysql_error());

    // On initialise le tableau des catégories
    $arrayForumCat  = array();

    // On parcours le résultat de la requete $dbeForumCat pour remplir le tableau des catégories
    while ($data = mysql_fetch_assoc($dbeForumCat)) {
        // On stocke l'id de la catégorie pour créer les index du tableau arrayForumCat
        $id = $data['catId'];

        foreach ($data as $field => $value) {
            if ($field != 'catId') {
                $arrayForumCat[$id][$field] = $value;
            }
        }
    }
    // (SELECT id FROM ".FORUM_MESSAGES_TABLE." WHERE forum_id = forumId ORDER BY id DESC LIMIT 0,1) as forumLastId,
    // On récupère tous les forums en base de donnée
    $dbsForums = "SELECT id AS forumId, cat AS forumCat, parentid, nom AS forumName, comment AS forumDesc, moderateurs AS forumModos,
                           niveau AS forumLevel, ordre AS forumOrder, image,
                    (SELECT count(*)
                     FROM ".FORUM_THREADS_TABLE."
                     WHERE forum_id = forumId
                    ) as forumNbTopic,
                    (SELECT count(*)
                     FROM ".FORUM_MESSAGES_TABLE."
                     WHERE forum_id = forumId
                    ) as forumNbMessages,
                    (SELECT Max(id)
                     FROM ".FORUM_MESSAGES_TABLE."
                     WHERE forum_id = forumId
                    ) AS lastMsgId
                  FROM ".FORUM_TABLE."
                  ORDER BY ordre, nom, id";
    $dbeForums = mysql_query($dbsForums) or die(mysql_error());

    // On initialise le tableau des forums
    $arrayForums    = array();

    // On parcours le résultat de la requete $dbeForums pour remplir le tableau des forums
    while ($data = mysql_fetch_assoc($dbeForums)) {
        // On stocke l'id du forum pour créer les index du tableau arrayForums
        $id = $data['forumId'];

        foreach ($data as $field => $value) {
            if ($field != 'forumId') {
                $arrayForums[$id][$field] = $value;
            }

            // Initialisation de l'index lastMsg
            $arrayForums[$id]['lastMsg'] = _NOPOST;
        }
    }

    // #################################
    // # @todo : REMPLACER LE $GLOBALS['user'][0] par $GLOBALS['user']['id']
    // #################################
    // On inclus le statut de visite (forum vu ou non) dans les forums
    if (isset($GLOBALS['user']) && !empty($GLOBALS['user'])){
        $dbsForumsVisit = "SELECT user_id AS userId, forum_id AS forumId
                           FROM ".FORUM_READ_TABLE."
                           WHERE user_id = '".$GLOBALS['user'][0]."'
                          ";
        $dbeForumsVisit = mysql_query($dbsForumsVisit);
        $dataForumsRead = mysql_fetch_assoc($dbeForumsVisit);

        foreach($arrayForums as $forumId => $forum){
            $arrayForumsRead = explode(',', $dataForumsRead['forumId']);
            if($forum['forumNbMessages'] > 0 && in_array($forumId, $arrayForumsRead)){
                $arrayForums[$forumId]['imageForumRead'] = 'modules/Forum/Images/forum.png';
            }
            else{
                $arrayForums[$forumId]['imageForumRead'] = 'modules/Forum/Images/forumNew.png';
            }
        }
    }
    else{
        foreach($arrayForums as $forumId => $forum){
            $arrayForums[$forumId]['imageForumRead'] = 'modules/Forum/Images/forum.png';
        }
    }


    // On créer une variable qui va contenir tous les id des Forums primaires
    $whereForumsId = '';
    // On parcours le tableau des Forums et on ajoute l'id dans la clause where si le forum est primaire
    $i = 0;

    foreach ($arrayForums as $forumId => $forum){
        if(!empty($forum['lastMsgId'])){
            if($i == 0){
                $whereForumsId .= ' WHERE ';
            }
            if($i > 0){
                $whereForumsId .= ' OR ';
            }
            $whereForumsId .= 'A.id = '.$forum['lastMsgId'];
        }
        $i++;
    }

    /*$dbsLastsMsg = "SELECT  A.id, A.thread_id AS threadId, A.forum_id as forumId, A.date, A.auteur AS author, A.auteur_id AS authorId,
                            B.parentid,
                            C.titre AS topicTitle,
                            D.avatar
                    FROM ".FORUM_MESSAGES_TABLE." AS A
                    LEFT JOIN ".FORUM_TABLE." AS B
                    ON B.id = A.forum_id
                    LEFT JOIN ".FORUM_THREADS_TABLE." AS C
                    ON C.id = A.thread_id
                    LEFT JOIN ".USER_TABLE." AS D
                    ON D.pseudo = A.auteur
                    ".$whereForumsId." ";*/

    $dbsLastsMsg = "SELECT  A.id, A.thread_id AS threadId, A.forum_id as forumId, A.date, A.auteur AS author, A.auteur_id AS authorId,
                            B.parentid,
                            C.titre AS topicTitle
                    FROM ".FORUM_MESSAGES_TABLE." AS A
                    LEFT JOIN ".FORUM_TABLE." AS B
                    ON B.id = A.forum_id
                    LEFT JOIN ".FORUM_THREADS_TABLE." AS C
                    ON C.id = A.thread_id
                    ".$whereForumsId." ";
    $dbeLastsMsg = mysql_query($dbsLastsMsg);

    // On initialise le tableau des derniers messages
    $arrayLastsMsg = array();

    // On parcours le résultat de la requete $dbeLastsMsg pour remplir le tableau des derniers messages
    while ($data = mysql_fetch_assoc($dbeLastsMsg)) {
        foreach ($data as $field => $value) {
            // Si le titre du topic dépace les 25 caractères on le tronque
            if($field == 'topicTitle' && strlen($value) > 25){
                $value = substr($value, 0, 25).' ...';
            }
            $arrayLastsMsg[$data['id']][$field] = $value;
        }
    }

    // On créer un tableau qui va contenir les différents pseudo à affiché
    $arrayWhereAuthor = array();

    foreach ($arrayLastsMsg as $lastMsg) {
        $arrayWhereAuthor[] = $lastMsg['author'];
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

    foreach ($arrayLastsMsg as $lastMsgId => $lastMsg) {
        if(array_key_exists($lastMsg['author'], $arrayAuthors)){
            $arrayLastsMsg[$lastMsgId]['avatar'] = $arrayAuthors[$lastMsg['author']];
        }
        else{
            $arrayLastsMsg[$lastMsgId]['avatar'] = 'modules/Forum/images/noAvatar.png';
        }
    }

    // On initialise le tableau des sous-forums
    $arraySubForums = array();

    // On sépare les sous-forums des forums principaux
    foreach($arrayForums as $forumId => $forum){
        if($forum['parentid'] != 0){
            $arraySubForums[$forumId] = $forum;
            unset($arrayForums[$forumId]);
        }
    }

    // On fusionne le tableau des derniers messages dans le tableau des forums
    foreach($arrayLastsMsg as $lastMsg){
        if($lastMsg['parentid'] != 0){
            $id = $lastMsg['parentid'];
            $arraySubForums[$lastMsg['forumId']]['lastMsg'] = $lastMsg;
        }
        else{
            $id = $lastMsg['forumId'];
        }
        $arrayForums[$id]['lastMsg'] = $lastMsg;
    }

    // On inclus les stats des sous-forums dans les forums principaux
    foreach($arraySubForums as $subForum){
        // Mis à jour du nombre de messages dans le forum principal
        $arrayForums[$subForum['parentid']]['forumNbMessages'] += $subForum['forumNbMessages'];
        // Mis à jour du nombre de sujets dans le forum principal
        $arrayForums[$subForum['parentid']]['forumNbTopic'] += $subForum['forumNbTopic'];
        // Mis à jour du dernier post dans le forum principal
        if($subForum['lastMsgId'] > $arrayForums[$subForum['parentid']]['lastMsgId']){
            $arrayForums[$subForum['parentid']]['lastMsgId'] = $subForum['lastMsgId'];
        }
    }

    // On fusionne le tableau subForum dans le tableau Forum
    foreach($arraySubForums as $subForumId => $subForum){
        if(array_key_exists($subForum['parentid'], $arrayForums)){
            $arrayForums[$subForum['parentid']]['forumContent'][$subForumId] = $subForum;
        }
    }

    // on Fusionne le tableau Forum dans le tableau Catégorie
    foreach($arrayForums as $forumId => $Forum){
        if(array_key_exists($Forum['forumCat'], $arrayForumCat)){
            $arrayForumCat[$Forum['forumCat']]['catContent'][$forumId] = $Forum;
        }
    }

    // Initialisation de la variable nav
    $nav = null;

    // Affichage de la catégorie dans le breadcrumb si elle donnée via l'url et suppression des autres catégorie dans le tableau
    if (array_key_exists('cat', $_REQUEST) && !empty($_REQUEST['cat'])) {
        $urlCatId = $_REQUEST['cat'];
        if (array_key_exists($urlCatId, $arrayForumCat)) {
            $nav = '-> <strong>'.$arrayForumCat[$urlCatId]['catName'].'</strong>';
        }

        foreach ($arrayForumCat as $catId => $cat) {
            if ($catId != $urlCatId) {
                unset($arrayForumCat[$catId]);
            }
        }
    }

    // Affichage du forum primaire dans le breadcrumb s'il est donné via l'url et suppression des autres forums
    else if (array_key_exists('forumCat', $_REQUEST) && !empty($_REQUEST['forumCat'])) {
        $urlForumCat = $_REQUEST['forumCat'];
        if (array_key_exists($urlForumCat, $arrayForums) && !empty($arrayForums[$urlForumCat]['forumContent'])) {
            $forumCat = '-> <a href="index.php?file=Forum&amp;cat='.$arrayForums[$urlForumCat]['forumCat'].'">
                                <strong>'.$arrayForumCat[$arrayForums[$urlForumCat]['forumCat']]['catName'].'</strong>
                            </a>&nbsp;';
            $nav = $forumCat.'-> <strong>'.$arrayForums[$urlForumCat]['forumName'].'</strong>';
        }

        // On supprime le tableau des categories
        $arrayForumCat = array();

        // On transfere le forum principal dans le tableau des catégories
        $arrayForumCat[$urlForumCat]['catName'] = $arrayForums[$urlForumCat]['forumName'];
        $arrayForumCat[$urlForumCat]['catLevel'] = $arrayForums[$urlForumCat]['forumLevel'];

        // On stocke temporairement les sous forums dans une variable
        $forumContentTmp = $arrayForums[$urlForumCat]['forumContent'];

        // On supprime le tableau des forums
        $arrayForums = array();

        // On transfere les sous forums dans le tableau des forums
        $arrayForums = $forumContentTmp;

        // On link le tableau des forums dans la nouvelle catégorie
        $arrayForumCat[$urlForumCat]['catContent'] = $arrayForums;
    }

    // Récupération des informations divers (qui est en ligne + stats)
    $dbsForumInfos = "  SELECT count(id) as nbMessages,
                            (SELECT count(id) FROM ".USER_TABLE.") as nbMembers,
                            (SELECT pseudo FROM ".USER_TABLE." ORDER BY date DESC LIMIT 0,1) as lastMember
                        FROM ".FORUM_MESSAGES_TABLE."
                     ";
    $dbeForumInfos = mysql_query($dbsForumInfos);

    $arrayForumInfos = mysql_fetch_assoc($dbeForumInfos);

    // ##############################
    // @todo : A METTRE A JOUR (system de nbvisiteur() à mergé avec les groupes)
    // ##############################
    // Affichage données users en ligne
    $dbsForumWhoIsOnline = "SELECT A.username, A.type, B.country
                            FROM ".NBCONNECTE_TABLE." AS A
                            LEFT JOIN ".USER_TABLE." AS B
                            ON B.pseudo = A.username";
    $dbsForumWhoIsOnline = mysql_query($dbsForumWhoIsOnline);

    $arrayNbOnline = array(
                        'visitors' => 0,
                        'members'  => 0,
                        'admins'   => 0
                    );
    $arrayWhoIsOnline = array();

    while($dataOnline = mysql_fetch_assoc($dbsForumWhoIsOnline)){
        if(!empty($dataOnline['country'])){
            $flag = '<img class="nkForumOnlineFlag" src="images/flags/'.$dataOnline['country'].'" alt="" />';
        }
        else{
            $flag = '';
        }

        if($dataOnline['type'] == 0){
            $arrayNbOnline['visitors']++;
        }
        elseif($dataOnline['type'] == 1){
            $arrayNbOnline['members']++;
            $arrayWhoIsOnline[] = $flag.'<span class="nkForumOnlineMember">'.$dataOnline['username'].'</span>';
        }
        elseif($dataOnline['type'] >= 2){
            $arrayNbOnline['admins']++;
            $arrayWhoIsOnline[] = $flag.'<span class="nkForumOnlineAdmin">'.$dataOnline['username'].'</span>';
        }
    }

    $arrayForumInfos['nbOnline'] = _THEREARE."&nbsp;".$arrayNbOnline['visitors']."&nbsp;"._FVISITORS.", ".$arrayNbOnline['members']."&nbsp;"._FMEMBERS."&nbsp;"._AND."&nbsp;".$arrayNbOnline['admins']."&nbsp;"._FADMINISTRATORS."&nbsp;"._ONLINE."<br />"._MEMBERSONLINE." : ";

    if(count($arrayWhoIsOnline) > 0){
        $whoIsOnline = implode(', ', $arrayWhoIsOnline);
    }
    else{
        $whoIsOnline = _NONE;
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
            <div id="nkForumHeader">
                <h1>Forums <?php echo $titleForum; ?></h1>
                <p><?php echo $descForum; ?></p>
            </div><!-- Hack inline-block
            --><div id="nkForumMainSearch">
                <form method="get" action="index.php" >
                    <label for="forumSearch"><?php echo _SEARCH; ?> :</label>
                    <input id="forumSearch" type="text" name="forumSearch" size="25" />
                    <p>
                        [ <a href="index.php?file=Forum&amp;page=search"><?php echo _ADVANCEDSEARCH; ?></a> ]
                    </p>
                    <input type="hidden" name="file" value="Forum" />
                    <input type="hidden" name="page" value="search" />
                    <input type="hidden" name="do" value="search" />
                    <input type="hidden" name="into" value="all" />
                </form>
            </div>
            <div class="nkForumMainBreadcrumb">
                <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;<?php echo $nav; ?>
            </div><!-- Hack inline-block
            --><div id="nkForumMainDates">
                <span><?php echo _DAYIS; ?> : <?php echo $today; ?></span>&nbsp;<span><?php echo $textLastVisit; ?></span>
            </div>
    <?php
            foreach ($arrayForumCat as $catId => $cat) {
                $catLink = 'index.php?file=Forum&amp;cat='.$catId;
    ?>
                <div class="nkForumCat">
                    <div class="nkForumCatNameCell">
                            <h2><a href="<?php echo $catLink; ?>"><?php echo $cat['catName']; ?></a></h2>
                    </div>
                    <div class="nkForumCatWrapper">
                        <div class="nkForumCatHead">
                            <div>
                                <div class="nkForumBlankCell"></div>
                                <div class="nkForumForumCell"><?php echo _FORUM; ?></div>
                                <div class="nkForumStatsCell"><?php echo _STATS; ?></div>
                                <div class="nkForumDateCell"><?php echo _LASTPOST; ?></div>
                            </div>
                        </div>
                        <div class="nkForumCatContent">
    <?php
                if (!empty($cat['catContent'])) {
                    $arrayStyleImageForum = array();
                    foreach ($cat['catContent'] as $forumId => $forum) {
                        if (empty($forum['forumContent'])) {
                            $forumLink = 'index.php?file=Forum&amp;page=viewforum&amp;forum_id='.$forumId;
                        }
                        else {
                            $forumLink = 'index.php?file=Forum&amp;forumCat='.$forumId;
                        }

                        $forumInfos = ' <h3><a href="'.$forumLink.'">'.$forum['forumName'].'</a></h3>';

                        if (!empty($forum['forumContent'])) {
                            foreach ($forum['forumContent'] as $subForumId => $subForum) {
                                $forumInfos .= '<span><a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id='.$subForumId.'">'.$subForum['forumName'].'</a></span>&nbsp;';
                            }
                        }

                        $forumInfos .= '<p>'.$forum['forumDesc'].'</p>';


                        if(array_key_exists('image', $forum) && !empty($forum['image'])){
                            $arrayStyleImageForum[$forumId] = $forum['image'];
                            $classImage = 'nkForumNameCellImage';
                        }
                        else{
                            $classImage = null;
                        }
    ?>
                            <div>
                                <div class="nkForumIconCell">
                                    <img src="<?php echo $forum['imageForumRead']; ?>" alt="" />
                                </div>
                                <div id="nkForumNameCell_<?php echo $forumId; ?>" class="nkForumNameCell <?php echo $classImage; ?>"><?php echo $forumInfos; ?></div>
                                <div class="nkForumStatsCell">
                                    <strong><?php echo $forum['forumNbTopic']; ?></strong>&nbsp;<?php echo strtolower(_TOPICS); ?>
                                    <br/>
                                    <strong><?php echo $forum['forumNbMessages']; ?></strong>&nbsp;<?php echo strtolower(_MESSAGES); ?>
                                </div>
                                <div class="nkForumDateCell">
                                    <div class="nkForumAuthorAvatar">
                                        <img src="<?php echo $forum['lastMsg']['avatar']; ?>" alt="" />
                                    </div>
                                    <div>
                                        <p>
                                            <a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $forum['lastMsg']['forumId']; ?>&amp;thread_id=<?php echo $forum['lastMsg']['threadId']; ?>#<?php echo $forum['lastMsg']['id']; ?>">
                                                <?php echo $forum['lastMsg']['topicTitle']; ?>
                                            </a>
                                        </p>
                                        <p>
                                            <span><?php echo _BY; ?></span>
                                            <a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo $forum['lastMsg']['author']; ?>">
                                                <strong><?php echo $forum['lastMsg']['author']; ?></strong>
                                            </a>
                                        </p>
                                        <p><?php echo nkDate($forum['lastMsg']['date']); ?></p>
                                    </div>
                                </div>
                            </div>
    <?php
                    }
                }
    ?>
                        </div>
                    </div>
                </div>
    <?php
                if(count($arrayStyleImageForum) > 0){
                    echo '<style type="text/css">
                            .nkForumNameCellImage:before{
                                content:\'\';
                                display: block;
                                width:50px;
                                height:50px;
                                float:left;
                                margin-right: 5px;
                                -webkit-background-size: cover !important;
                                -moz-background-size: cover !important;
                                -o-background-size: cover !important;
                                -ms-background-size: cover !important;
                                background-size: cover !important;
                            }';

                    foreach($arrayStyleImageForum as $id => $image){
                        if(!empty($image)){
                            echo '#nkForumNameCell_'.$id.':before{
                                    background:url('.$image.') no-repeat center;
                                    }';
                        }
                    }

                    echo '</style>';
                }
            }
    ?>
                <div id="nkForumWhoIsOnline">
                    <h3><?php echo _FWHOISONLINE; ?></h3>
                    <div id="nkForumWhoIsOnlineIcon"></div>
                    <div id="nkForumWhoIsOnlineContent">
                        <p><?php echo _TOTAL_MEMBERS_POSTS.'<strong>'.$arrayForumInfos['nbMessages'].'</strong>&nbsp;'.strtolower(_MESSAGES).'.'; ?></p>
                        <p><?php echo _WE_HAVE.'<strong>'.$arrayForumInfos['nbMembers'].'</strong>'._REGISTERED_MEMBERS; ?></p>
                        <p><?php echo _LAST_USER_IS.'<a href="index.php?file=Members&op=detail&autor='.$arrayForumInfos['lastMember'].'">'.$arrayForumInfos['lastMember'].'</a>'; ?></p>
                        <p><?php echo $arrayForumInfos['nbOnline'].$whoIsOnline; ?></p>
                    </div>
                </div>
                <div id="nkForumUserActionLink">
    <?php
                    if($GLOBALS['user']){
    ?>
                        <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark"><?php echo _MARKREAD; ?></a>
                        <!--
                        #################################
                        # @todo : REMPLACER LE $GLOBALS['user'][4] par $GLOBALS['user']['lastVisit']
                        #################################
                         -->
    <?php
                        $lastVisit = $GLOBALS['user'][4];
                        if(isset($lastVisit) && !empty($lastVisit)){
    ?>
                            <a id="nkForumViewUnread" href="index.php?file=Forum&amp;page=search&amp;do=search&amp;date_max=<?php echo $lastVisit; ?>"><?php echo _VIEWLASTVISITMESS; ?></a>
    <?php
                        }
                    }
    ?>
                </div>
                <div id="nkForumReadLegend">
                    <div>
                        <img src="modules/Forum/images/forumNew.png" alt="NEW" />
                        <span><?php echo _NEWSPOSTLASTVISIT; ?></span>
                    </div>
                    <div>
                        <img src="modules/Forum/images/forum.png" alt="" />
                        <span><?php echo _NOPOSTLASTVISIT; ?></span>
                    </div>
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

closetable();
?>
