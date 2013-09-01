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

function affich_block_survey($blok){
    global $file, $nuked;

    if ($file != 'Survey'){
        $survey_id = $blok['content'];
        $blok['content'] = '';

        if ($survey_id != '') $where = 'WHERE sid = \'' . $survey_id . '\' ';
        else $where = 'ORDER BY sid DESC LIMIT 0, 1';

        $sql = mysql_query('SELECT sid, titre FROM ' . SURVEY_TABLE . ' ' . $where);
        list($poll_id, $titre) = mysql_fetch_array($sql);
        $titre = printSecuTags($titre);

        $blok['content'] = '<form action="index.php?file=Survey&amp;nuked_nude=index&amp;op=update_sondage" method="post">'."\n"
        . '<div style="text-align: center">'."\n"
        . '<b>' . $titre . '</b><br /><p style="text-align: left" >'."\n";

        $sql2 = mysql_query('SELECT voteID, optionText FROM ' . SURVEY_DATA_TABLE . ' WHERE sid = \'' . $poll_id . '\' ORDER BY voteID ASC');
        while (list($voteid, $optiontext) = mysql_fetch_array($sql2)){
            $optiontext = printSecuTags($optiontext);

            $blok['content'] .= '<input type="radio" class="checkbox" name="voteID" value="' . $voteid . '" />&nbsp;' . $optiontext . '<br />'."\n";
        }

        $blok['content'] .= '</p><p style="text-align: center"><input type="hidden" name="poll_id" value="' . $poll_id . '" />'."\n"
                                    . '<br /><input type="submit" value="' . VOTE . '" />&nbsp;'
                                    . '<input type="button" value="' . RESULTS . '" onclick="document.location=\'index.php?file=Survey&amp;op=affich_res&amp;poll_id=' . $poll_id . '\'" />'."\n"
                                    . '<br /><a href="index.php?file=Survey"><b>' . OTHER_POLLS . '</b></a></p></div></form>'."\n";
        }
    return $blok;
}

function edit_block_survey($block){

    $dbsBlock ='SELECT content
                FROM '.BLOCK_TABLE.'
                WHERE bid = "'.$block['id'].'" ';
    $dbeBlock = mysql_query($dbsBlock);
    $block = mysql_fetch_assoc($dbeBlock);

    $dbsSurvey = 'SELECT sid AS id, titre AS title
                 FROM '.SURVEY_TABLE.'
                 ORDER BY title';
    $dbeSurvey = mysql_query($dbsSurvey) or die(mysql_error());

?>
    <tr>
        <td>
            <strong><?php echo SURVEY; ?> : </strong>
            <select name="content">
<?php
    while ($survey = mysql_fetch_assoc($dbeSurvey)) {
        $survey['title'] = printSecuTags($survey['title']);

        $checked = null;
        if ($survey['id'] == $block['content']) {
            $checked = 'selected="selected"';
        }

        echo '<option value="'.$survey['id'].'" '.$checked.'>' . $survey['title'] . '</option>';
    }
?>
            </select>
        </td>
    </tr>
<?php

}
?>
