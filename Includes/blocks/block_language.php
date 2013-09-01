<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")){
	exit('You can\'t run this file alone.');
}

function affich_block_language($blok){

    $blok['content'] .= "<form method=\"post\" action=\"index.php?file=User&amp;nuked_nude=index&amp;op=modif_langue\">\n"
    . "<div style=\"text-align: center;\"><select name=\"user_langue\" onchange=\"submit();\">\n"
    . "<option value=\"\">--------^-------</option>\n";

    if ($rep = @opendir('lang/')){
        while (false !== ($f = readdir($rep))){
            if ($f != '..' && $f != '.' && $f != 'index.html'){
				list ($langfile, ,) = explode ('.', $f);

                if ($GLOBALS['cookieLang'] == $langfile){
                    $checked = 'selected="selected"';
                }
                else{
                    $checked = '';
                }

                $blok['content'] .= '<option value="' . $langfile . '" ' . $checked . '>' . $langfile . '</option>'."\n";
            }
        }
        closedir($rep);
        clearstatcache();
    }
    $blok['content'] .= '</select></div></form>'."\n";
    return $blok;
}
?>
