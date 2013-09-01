<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

translate('modules/Search/lang/' . $GLOBALS['language'] . '.lang.php');
translate('modules/404/lang/' . $GLOBALS['language'] . '.lang.php');

if($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin'){
	require_once 'modules/Admin/views/layout.php';
	adminHeader();
?>
    <div class="errorWrapper">
        <span class="errorNum">404</span>
        <div class="errorContent">
            <span class="errorDesc"><span class="nkIcons icon-warning"></span><span><?php echo PAGE_NOT_FOUND; ?></span>
            <div class="searchLine first">
                <form action="">
                    <input type="text" name="search" class="ac" placeholder="<?php echo SEARCH.' ...'; ?>" />
                    <button type="submit" name="find" value=""><span class="icos-search"></span></button>
                </form>
            </div>
            <div class="fluid">
                <a href="index.php?file=Admin" title="" class="buttonM bLightBlue grid6"><?php echo BACK_DASHBOARD; ?></a>
                <a href="index.php" title="" class="buttonM bRed grid6"><?php echo BACK_WEBSITE; ?></a>
            </div>
        </div>
    </div>
<?php

    adminFooter();
}
else {
	opentable();

    $error_title = ($_REQUEST['op'] != 'sql') ? '<big><b>' . $nuked['name'] . '</b></big><br /><br />' . _NOEXIST . '<br /><br />' : _ERROR404SQL . '<br /><br />';

    echo '<div style="text-align: center; padding: 0 10px">' . $error_title . '
              <form method="post" action="index.php?file=Search&amp;op=mod_search">
                  <p><input type="hidden" name="module" value="" /><input type="text" name="main" size="25" /></p>
                  <p><input type="submit" class="button" name="submit" value="' . _SEARCHFOR . '" /></p>
                  <p><a href="index.php?file=Search"><b>' . _ADVANCEDSEARCH . '</b></a> - <a href="javascript:history.back()"><b>' . _BACK . '</b></a></p>
    		  </form>
    	  </div>';
    closetable();
}

?>
