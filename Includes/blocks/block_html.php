<?php
/**
 * block_html.php
 *
 * Display and Admin of block HTML
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

function affich_block_html($blok){
    if(!function_exists('htmlspecialchars_decode'))
		$blok['content'] = strtr($blok['content'], array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
	else
		$blok['content'] = htmlspecialchars_decode(nkHtmlEntityDecode($blok['content']));
    return $blok;
}

function edit_block_html($block){

    $block['content'] = nkHtmlEntityDecode($block['content']);

?>
    <tr>
        <td>
            <strong><?php echo HTML_CONTENT; ?></strong>
        </td>
    </tr>
    <tr>
        <td>
            <textarea name="content" rows="10" cols="70"  class="noediteur"><?php echo $block['content']; ?></textarea>
        </td>
    </tr>
<?php
}
?>
