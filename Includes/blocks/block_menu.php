<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

function affich_block_menu($blok){
    $blok['content'] = block_link($blok['content']);
    return $blok;
}

function block_link($content){
    global $user;

    $content = nkHtmlEntityDecode($content);
    $link = explode('NEWLINE', $content);
    $screen = '<ul style="list-style: none; padding: 0">';
    $size = count($link);

    for($i=0; $i<$size; $i++){
        list($url, $title, $comment, $groupsId, $blank) = explode('|', $link[$i]);
        $url = preg_replace("/\[(.*?)\]/si", "index.php?file=\\1", $url);

        $title = preg_replace("`&amp;lt;`i", "<", $title);
        $title = preg_replace("`&amp;gt;`i", ">", $title);
        $comment = nkHtmlEntities($comment);
        $url = nkHtmlEntities($url);
        $arrayGroupsId = explode(',', $groupsId);

        $hasAccess = false;

        if (!nkHasVisitor()) {
            foreach ($arrayGroupsId as $groupId) {
                if (in_array($groupId, $arrayGroupsId)) {
                    $hasAccess = true;
                }
            }
        }
        else {
            if (in_array(3, $arrayGroupsId)) {
                $hasAccess = true;
            }
        }

        if ($hasAccess === true){
            if ($url <> '' && $title <> '' && $blank == 0)
                $screen .= '<li><a href="' . $url . '" title="' . $comment . '" style="padding-left: 10px" class="menu">' . $title . '</a></li>';

            if ($url <> '' && $title <> '' && $blank == 1)
                $screen .= '<li><a href="' . $url . '" title="' . $comment . '" class="menu" style="padding-left: 10px" onclick="window.open(this.href); return false;">' . $title . '</a></li>';

            if ($url == '' && $title <> '' && $comment == '')
                $screen .= '<li style="padding-left: 20px" class="titlemenu">' . $title . '</li>';
        }
    }
    $screen .= '</ul>';
    return $screen;
}

function edit_block_menu($block){

    $block['content'] = nkHtmlEntities($block['content']);
?>
    <tr>
        <td >
            <input type="button" value="<?php echo EDIT_MENU; ?>" onclick="javascript:window.location='index.php?file=Admin&amp;page=menu&amp;op=edit_menu&amp;bid=<?php echo $block['id']; ?>'" />
        </td>
    </tr>
<?php
}
?>
