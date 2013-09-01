<?php
/**
 * layout.php (modules/Admin)
 *
 * Admin layout
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

function sidebar() {
?>
 <div id="sidebar">
        <div class="mainNav">
            <!-- Block user -->
            <div class="user">
                <a title="" class="leftUserDrop"><img style="width:72px;height:70px;" src="http://images.samoth.fr/avatar/samoth_2013.jpg" alt="" /></a><span>Samoth</span>
                <ul class="leftUser">
                    <li><a href="index.php?file=User&nuked_nude=index" rel="modal" data-title="Mon compte" class="sProfile">My profile</a></li>
                    <li><a href="#" title="" class="sMessages">Messages</a></li>
                    <li><a href="#" title="" class="sSettings">Settings</a></li>
                    <li><a href="#" title="" class="sLogout">Logout</a></li>
                </ul>
            </div>
            <!-- Main nav -->
            <ul class="nav">
<?php
                foreach ($GLOBALS['arrayMainNav'] as $nav => $navContent):
?>
                    <li>
                        <a href="<?php echo $navContent[0]; ?>">
                            <span class="nkIcons <?php echo $navContent[1]; ?>"><?php echo $nav; ?></span>
                        </a>
<?php
                    if (!is_null($navContent[2])):
                        $styleUlMod = null;
                        $widthUl = ceil(count($GLOBALS['arrayModules']) / 8) * 200 + (ceil(count($GLOBALS['arrayModules']) / 8) * 2);

                        if ($widthUl > 606) {
                            $widthUl = 606;
                        }

                        if ($nav == MODULES) {
                            $styleUlMod = 'style="white-space:normal;width:'.$widthUl.'px;"';
                        }
?>
                        <ul <?php echo $styleUlMod; ?>>
<?php
                        foreach ($navContent[2] as $subNav => $subNavContent):
                            $styleLiMod = null;

                            if ($nav == MODULES) {
                                $styleLiMod = 'style="width:200px;display:inline-block;border-left:1px solid #545454;border-right:1px solid #343434;margin-right:-3px;"';
                            }
?>
                            <li <?php echo $styleLiMod; ?>>
                                <a href="<?php echo $subNavContent[0]; ?>">
                                    <span class="nkIcons <?php echo $subNavContent[1]; ?>"><?php echo $subNav; ?></span>
                                </a>
                            </li>
<?php
                        endforeach;
?>
                        </ul>
<?php
                    endif;
?>
                    </li>
<?php
                endforeach;
?>
            </ul>
        </div>
    </div>
<?php
}
?>
