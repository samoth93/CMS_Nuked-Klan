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

function affich_block_rss($blok){

	list($rssHost, $titreactu, $puce, $nbr) = explode('|', $blok['content']);

	$blok['content'] = '';

	if ($handle = @fopen($rssHost, "r")) {
		fclose($handle);

		$xml = simplexml_load_file($rssHost);

		$title = (string) $xml->channel->title;
		$title = htmlentities($title, ENT_QUOTES, 'UTF-8');

		if($titreactu == 'Oui') $blok['content'] .= '<h3 style="text-align:center;padding:10px 0 0 0">'.$title.'</h3>';

		$blok['content'] .= '<ul style="margin:0 20px;padding:0;list-style:url(images/puces/'.$puce.')">';

		$i = 0;
		foreach ($xml->channel->item as $actu) {

			$href = htmlentities((string) $actu->link, ENT_QUOTES, 'UTF-8');
			$titleActu = htmlentities((string) $actu->title, ENT_QUOTES, 'UTF-8');
			$description = preg_replace("#<[^>]*>#", "", trim(strip_tags((string)$actu->description)));
			$description = html_entity_decode($description, ENT_QUOTES, 'ISO-8859-1');
			$pubDate = (string) $actu->pubDate;
			$description = (strlen($description) > 255) ? substr($description,0,255).'...' : $description;
			$texte = $pubDate . ' : ' . $description;

			if ($blok['active'] == 3 or $blok['active'] == 4) {

				$blok['content'] .= '<li><a href="'.$href.'" onclick="window.open(this.href);return false;" title="'.nkHtmlEntities(utf8_decode($description)).'">'.utf8_decode($titleActu).'</a> ( '.$pubDate.' )</li>';

			} else {

				$blok['content'] .= '<li><a href="'.$href.'" onclick="window.open(this.href);return false;" title="'.nkHtmlEntities(utf8_decode($texte)).'">'.utf8_decode($titleActu).'</a></li>';

			}

			$i++;
			if ($i == $nbr) break;
		}

		$blok['content'] .= '</ul>';

	} else {
		$blok['content'] = 'Echec lors de l\'ouverture du fichier '.$rssHost.'.';
	}

    return $blok;
}

function list_puce($spuce){

	echo '<option value="none.gif">-- ' . NONE . ' --</option>';

	$path = "images/puces/";
	$handle = opendir($path);
	while (false !== ($puce = readdir($handle))) {

		if ($puce != "." && $puce != ".." && $puce != "Thumbs.db" && $puce != "index.html" && $puce != "none.gif") {

			if (is_file($path . $puce)) {

				$selected = ($puce == $spuce) ? 'selected="selected"' : '';
				echo '<option value="' . $puce . '" ' . $selected . '>' . $puce . '</option>';
            }
        }
    }
}

function edit_block_rss($block){

    $dbsBlock = 'SELECT content
                 FROM '.BLOCK_TABLE.'
                 WHERE bid = "'.$block['id'].'" ';
    $dbeBlock = mysql_query($dbsBlock);
    $block = mysql_fetch_assoc($dbeBlock);

    $rssUrl = $rssTitle = $rssIcon = $rssCount = null;

    $arrayRss = explode('|', $block['content']);

    if (count($arrayRss) > 0) {
        list($rssUrl, $rssTitle, $rssIcon, $rssCount) = $arrayRss;
    }

	echo '<script type="text/javascript">
	      <!--
		  function update_img(newimage){
			  document.getElementById(\'img_puce\').src = \'images/puces/\' + newimage;
		  }
		  // -->
		  </script>';

    echo ' <tr>
					      <td colspan="4"><b>' . URL . ' : </b><input type="text" name="content" size="50" value="' . $rssUrl . '" /></td>
					  </tr>
					  <tr>
					      <td colspan="4"><b>' . RSS_TITLE . ' : </b>
						      <select name="rss_title">';

							  if (!empty($rssTitle)) {
							      echo '<option>' . $rssTitle . '</option>
								        <option disabled="disabled"> --- </option>';
							  }

							echo '<option>Oui</option>
								  <option>Non</option>
							  </select>
					      </td>
					  </tr>
					  <tr>
					      <td colspan="4"><b>' . RSS_COUNT . ' : </b>
						      <select name="rss_count">';

							  if (!empty($rssCount)) {
							      echo '<option>' . $rssCount . '</option>
								        <option disabled="disabled"> --- </option>';
							  }

							echo '<option>5</option>
								  <option>10</option>
								  <option>15</option>
								  <option>20</option>
							  </select>
					      </td>
					  </tr>
					  <tr>
					      <td colspan="3"><b>' . RSS_ICON . ' : </b>
						      <select name="rss_icon" onchange="update_img(this.options[selectedIndex].value);">';

							  list_puce($rssIcon);
							  if (empty($rssIcon)) $rssIcon = "none.gif";

					echo '    </select>
					          <img id="img_puce" src="images/puces/' . $rssIcon . '" alt="" />
					      </td>
					  </tr>';

}

function modif_advanced_rss($data){
	$data['content'] = $data['content'] . '|' . $data['rss_title'] . '|' . $data['rss_icon'] . '|' .$data['rss_count'];
	return $data;
}
?>
