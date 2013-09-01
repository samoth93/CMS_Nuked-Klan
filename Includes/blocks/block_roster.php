<?php
/**
 * block_roster.php
 *
 * Display and Admin of block roster
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

function affich_block_roster($block){

    $teamId = $block['content'];
    $block['content'] = null;

    if (!empty($teamId)) {
        $where = 'WHERE team = "'.$teamId.'" OR team2 = "'.$teamId.'" OR team3 = "'.$teamId.'" ';
    }
    else{
		$dbsNbTeams = 'SELECT count(cid) AS count FROM '.TEAM_TABLE;
        $dbeNbTeams = mysql_query($dbsNbTeams);
		$teams = mysql_fetch_assoc($dbeNbTeams);

		if ($teams['count'] > 0) {
            $where = 'WHERE team > 0 OR team2 > 0 OR team3 > 0';
        }
		else {
            $where = 'WHERE niveau > 1';
        }
    }

    $block['content'] .= '<table style="width:100%;" cellspacing="0" cellpadding="1">';

    $dbsUser = 'SELECT pseudo AS nickName, mail, country
                FROM '.USER_TABLE.' '.$where.'
                ORDER BY ordre, pseudo';
    $dbeUser = mysql_query($dbsUser);
    while ($user = mysql_fetch_assoc($dbeUser)) {
        list($pays, $ext) = explode ('.', $user['country']);

        $nickTeam = $GLOBALS['nuked']['tag_pre'] . $user['nickName'] . $GLOBALS['nuked']['tag_suf'];

        if (is_file('themes/'.$GLOBALS['theme'].'/images/mail.gif')){
            $img = 'themes/'.$GLOBALS['theme'].'/images/mail.gif';
        }
        else{
            $img = 'modules/Team/images/mail.gif';
        }

        $block['content'] .= '<tr><td style="width: 20%;text-align:center;" ><img src="images/flags/' . $user['country'] . '" alt="" title="' . $pays . '" /></td>'."\n"
								. '<td style="width: 60%;"><a href="index.php?file=Team&amp;op=detail&amp;autor=' . urlencode($user['nickName']) . '"><b>' . $nickTeam . '</b></a></td>'."\n"
								. '<td style="width: 20%;text-align:center;" ><a href="mailto:' . $user['mail'] . '"><img style="border: 0;" src="' . $img . '" alt="" title="' . $user['mail'] . '" /></a></td></tr>'."\n";
	}

    $block['content'] .= '</table>'."\n";
    return $block;
}

function edit_block_roster($block){

    $dbsBlock ='SELECT content
                FROM '.BLOCK_TABLE.'
                WHERE bid = "'.$block['id'].'" ';
    $dbeBlock = mysql_query($dbsBlock);
    $block = mysql_fetch_assoc($dbeBlock);

    $dbsTeams = 'SELECT cid AS id, titre AS title
                 FROM '.TEAM_TABLE.'
                 ORDER BY ordre, title';
    $dbeTeams = mysql_query($dbsTeams);

?>
    <tr>
        <td>
            <strong><?php echo TEAMS; ?> : </strong>
            <select name="content">
<?php
    while ($team = mysql_fetch_assoc($dbeTeams)) {
        $team['title'] = printSecuTags($team['title']);

        $checked = null;
        if ($team['id'] == $block['content']) {
            $checked = 'selected="selected"';
        }

        echo '<option value="'.$team['id'].'" '.$checked.'>' . $team['title'] . '</option>';
    }
?>
            </select>
        </td>
    </tr>
<?php
}
?>
