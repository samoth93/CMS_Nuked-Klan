<?php
/**
 * block_login.php
 *
 * Display and Admin of login block
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

function affich_block_login($block){
    global $user, $nuked, $bgcolor3, $bgcolor1 ;

    if(!empty($block['content'])){
        list($login, $messpv, $members, $whoisonline, $showavatar) = explode('|', $block['content']);
    }
    else{
        $login = $messpv = $members = $whoisonline = $showavatar = 'on';
    }

	$c = 0;

	if($login != 'off'){
		if (nkHasVisitor()){
			$block['content'] = '<form action="index.php?file=User&amp;nuked_nude=index&amp;op=login" method="post">'."\n"
			. '<table style="margin-left: auto;margin-right: auto;text-align: left;">'."\n"
			. '<tr><td>' . NICKNAME . ' :</td><td><input type="text" name="pseudo" size="10" maxlength="250" /></td></tr>'."\n"
			. '<tr><td>' . PASSWORD . ' :</td><td><input type="password" name="pass" size="10" maxlength="15" /></td></tr>'."\n"
			. '<tr><td colspan="2"><input type="checkbox" class="checkbox" name="remember_me" value="ok" checked="checked" />&nbsp;' . REMEMBER_ME . '</td></tr>'."\n"
			. '<tr><td colspan="2" align="center"><input type="submit" value="' . LOGIN . '" /></td></tr>'."\n"
			. '<tr><td colspan="2"><a href="index.php?file=User&amp;op=reg_screen">' . REGISTER . '</a><br />'."\n"
			. '<a href="index.php?file=User&amp;op=oubli_pass">' . FORGOTTEN_PASSWORD . '</a> ?</td></tr></table></form>'."\n";
		}
		else{
			$block['content'] = '<div style="text-align: center;">' . WELCOME . ', <b>' . $GLOBALS['user']['nickName'] . '</b><br /><br />'."\n";
			if ($showavatar != 'off'){
				$sql_avatar=mysql_query('SELECT avatar FROM ' . USERS_TABLE . ' WHERE id = \'' . $GLOBALS['user']['id'] . '\' ');
				list($avatar_url) = mysql_fetch_array($sql_avatar);
				if($avatar_url) $block['content'] .= '<img src="' . $avatar_url . '" style="border:1px ' . $bgcolor3 . ' dashed; width:100px; background:' . $bgcolor1 . '; padding:2px;" alt="' . $GLOBALS['user']['nickName'] . ' avatar" /><br /><br />';
			}
			$block['content'] .= '<a href="index.php?file=User">' . ACCOUNT . '</a> / <a href="index.php?file=User&amp;nuked_nude=index&amp;op=logout">' . LOGOUT . '</a></div>'."\n";
		}
		$c++;
	}

    if($messpv != 'off' && !nkHasVisitor()){
		if ($c > 0) $block['content'] .= '<hr style="height: 1px;" />'."\n";

		$sql2 = mysql_query('SELECT mid FROM ' . USERBOX_TABLE . ' WHERE user_for = \'' . $GLOBALS['user']['id'] . '\' AND status = 1');
		$nb_mess_lu = mysql_num_rows($sql2);

		$block['content'] .= '&nbsp;<img width="14" height="12" src="images/message.gif" alt="" />&nbsp;<span style="text-decoration: underline"><b>' . PRIVATE_MESSAGE . '</b></span><br />'."\n";

		if ($GLOBALS['user']['nbMess'] > 0){
			$block['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . UNREADS . ' : <a href="index.php?file=Userbox"><b>' . $GLOBALS['user']['nbMess'] . '</b></a>'."\n";
		}
		else{
			$block['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . UNREADS . ' : <b>' . $GLOBALS['user']['nbMess'] . '</b>'."\n";
		}

		if ($nb_mess_lu > 0){
			$block['content'] .= '<br />&nbsp;<b><big>·</big></b>&nbsp;' . READS . ' : <a href="index.php?file=Userbox"><b>' . $nb_mess_lu . '</b></a>'."\n";
		}
		else{
			$block['content'] .= '<br />&nbsp;<b><big>·</big></b>&nbsp;' . READS . ' : <b>' . $nb_mess_lu . '</b>'."\n";
		}

		$c++;
    }

	if ($members != 'off'){
		if ($c > 0) $block['content'] .= '<hr style="height: 1px;" />'."\n";

    	$block['content'] .= '&nbsp;<img width="16" height="13" src="images/memberslist.gif" alt="" />&nbsp;<span style="text-decoration: underline"><b>' . MEMBERS . '</b></span><br />'."\n";

    	$sql_users = mysql_query('SELECT id FROM ' . USERS_TABLE . ' WHERE niveau < 3');
    	$nb_users = mysql_num_rows($sql_users);

    	$sql_admin = mysql_query('SELECT id FROM ' . USERS_TABLE . ' WHERE niveau > 2');
    	$nb_admin = mysql_num_rows($sql_admin);

    	$sql_lastmember = mysql_query('SELECT pseudo FROM ' . USERS_TABLE . ' ORDER BY date DESC LIMIT 0, 1');
    	list($lastmember) = mysql_fetch_array($sql_lastmember);

    	$block['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . ADMINISTRATOR . ' : <b>' . $nb_admin . '</b><br />&nbsp;<b><big>·</big></b>&nbsp;' . MEMBERS . ' :'
        . '&nbsp;<b>' . $nb_users . '</b> [<a href="index.php?file=Members">' . NK_LIST . '</a>]<br />'."\n"
        . '&nbsp;<b><big>·</big></b>&nbsp;' . LAST_MEMBER . ' : <a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($lastmember) . '"><b>' . $lastmember . '</b></a>'."\n";

		 $c++;
	}

	if ($whoisonline != 'off'){
		if ($c > 0) $block['content'] .= '<hr style="height: 1px;" />'."\n";

    	$block['content'] .= '&nbsp;<img width="16" height="13" src="images/online.gif" alt="" />&nbsp;<span style="text-decoration: underline"><b>' . WHO_IS_ONLINE . '</b></span><br />'."\n";

    	$nb = nbvisiteur();

    	if ($nb[1] > 0){
            $user_online = '';
			$sql4 = mysql_query('SELECT username FROM ' . NBCONNECTE_TABLE . ' WHERE type BETWEEN 1 AND 2 ORDER BY date');
			while (list($nom) = mysql_fetch_array($sql4)){
				   $user_online .= '&nbsp;<b><big>·</big></b>&nbsp;<b>' . $nom . '</b><br />';
			}

			$user_list = '&nbsp;[<a href="#" onmouseover="AffBulle(\'&nbsp;&nbsp;' . WHO_IS_ONLINE . '\', \'' . htmlentities(mysql_real_escape_string($user_online), ENT_NOQUOTES, 'ISO-8859-1') . '\', 150)" onmouseout="HideBulle()">' . NK_LIST . '</a>]';
			}
    	else{
			$user_list = '';
    	}

		if ($nb[2] > 0){
            $admin_online = '';
			$sql5 = mysql_query('SELECT username FROM ' . NBCONNECTE_TABLE . ' WHERE type > 2 ORDER BY date');
			while (list($name) = mysql_fetch_array($sql5)){
				   $admin_online .= '&nbsp;<b><big>·</big></b>&nbsp;<b>' . $name . '</b><br />';
			}

			$admin_list = '&nbsp;[<a href="#" onmouseover="AffBulle(\'&nbsp;&nbsp;' . WHO_IS_ONLINE . '\', \'' . htmlentities(mysql_real_escape_string($admin_online), ENT_NOQUOTES, 'ISO-8859-1') . '\', 150)" onmouseout="HideBulle()">' . NK_LIST . '</a>]';
		}
		else{
			$admin_list = '';
		}

		$block['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . VISITOR;
		if ($nb[0] > 1) $block['content'] .= 's';
		$block['content'] .= ' : <b>' . $nb[0] . '</b><br />&nbsp;<b><big>·</big></b>&nbsp;' . MEMBER;
		if ($nb[1] > 1) $block['content'] .= 's';
		$block['content'] .= ' : <b>' . $nb[1] . '</b>' . $user_list . '<br />&nbsp;<b><big>·</big></b>&nbsp;' . ADMINISTRATOR;
		if ($nb[2] > 1) $block['content'] .= 's';
		$block['content'] .= ' : <b>' . $nb[2] . '</b>' . $admin_list . '<br />'."\n";

		$c++;
   }

   return $block;
}

function edit_block_login($block){

    $arrayOptions = array(
                        'login'       => 'on',
                        'messpv'      => 'on',
                        'members'     => 'on',
                        'whoisonline' => 'on',
                        'showavatar'  => 'on'
                    );

    $arrayConstants = array(
                        'login' => 'LOGIN',
                        'messpv' => 'PRIVATE_MESSAGE',
                        'members' => 'MEMBERS',
                        'whoisonline' => 'WHO_IS_ONLINE',
                        'showavatar' => 'DISPLAY_AVATAR'
                      );

    if(!empty($block['content'])){
        list($login, $messpv, $members, $whoisonline, $showavatar) = explode('|', $block['content']);

        foreach ($arrayOptions as $option => $value) {
            $arrayOptions[$option] = ${$option};
        }
    }

    foreach ($arrayOptions as $option => $value):
        if ($value == 'on') {
            $checked = ' checked="checked" ';
        }
        else{
            $checked = '';
        }
?>
        <tr>
            <td>
                <strong style="display:inline-block;width:120px;"><?php echo constant($arrayConstants[$option]); ?></strong>
                <div class="checkboxSliderWrapper">
                     <div class="onoffswitch">
                        <input type="checkbox" name="<?php echo $option; ?>" class="onoffswitch-checkbox" id="block-<?php echo $option; ?>" <?php echo $checked; ?>>
                        <label class="onoffswitch-label" for="block-<?php echo $option; ?>">
                            <div class="onoffswitch-inner"></div>
                            <div class="onoffswitch-switch"></div>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
<?php

    endforeach;
}

function modif_advanced_login($data){

    $arrayOptions = array('login','messpv','members','whoisonline','showavatar');

    foreach ($arrayOptions as $option) {
        if (array_key_exists($option, $data)) {
            ${$option} = mysql_real_escape_string($data[$option]);
        }
        else{
            ${$option} = 'off';
        }
    }

	$data['content'] = $login.'|'.$messpv.'|'.$members.'|'.$whoisonline.'|'.$showavatar;
	return $data;
}
?>
