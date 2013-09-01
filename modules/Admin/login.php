<?php
/**
 * login.php (modules/Admin)
 *
 * Admin login
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

// Inclusion de la langue
nkTranslate('modules/Admin/lang/'.$GLOBALS['language'].'.lang.php');

// Inclusion du layout de l'administration
require_once 'modules/Admin/views/layout.php';

// Redirection des visiteurs sur un 404
if(nkHasVisitor()){
    header('location: index.php?file=404');
    exit();
}

// On vérifie que l'utilisateur à le droit de se connecter à l'administration
if (nkAccessModule('Admin') === true) {
    // Dans le cas d'une requete ajax
    if(isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin' && !empty($GLOBALS['user']['nickName'])) {
        // On vérifie que tous les champs on bien été rempli il qu'ils sont conformes aux attentes
        $arrayRequest = array('mail', 'password');
        $error = false;

        foreach ($arrayRequest as $request) {
            $error = true;

            if (isset($_REQUEST[$request]) && !empty($_REQUEST[$request])) {
                ${$request} = $_REQUEST[$request];
                $error = false;
            }
        }

        if ($error === true) {
            echo EMPTY_FIELDS;
            exit();
        }

        $nickName  = mysql_real_escape_string($GLOBALS['user']['nickName']);

        $dbsLogin  = 'SELECT mail, pass
                     FROM '.USER_TABLE.'
                     WHERE pseudo = "'.$nickName.'"
                     LIMIT 1';
        $dbeLogin  = mysql_query($dbsLogin) or die(mysql_error());

        $dataLogin = mysql_fetch_assoc($dbeLogin);

        $errorLogin = null;
        $arrayErrorMsg = array( 1 => BAD_MAIL, 2 => BAD_PASSWORD);

        if ($dataLogin['mail'] != $mail) {
            $errorLogin = 1;
        }

        if (!Check_Hash($password, $dataLogin['pass'])) {
            if (is_null($errorLogin)) {
                $errorLogin = 2;
            }
        }

        if (is_null($errorLogin)) {
            $texteaction = mysql_real_escape_string(ACTION_CONNECT);
            mysql_query('INSERT INTO '.$GLOBALS['nuked']['prefix'].'_action VALUES ("", '.time().'", "'.$GLOBALS['user']['id'].'", "'.$texteaction.'")');

            $_SESSION['admin'] = true;

            echo LOGIN_SUCCESS;

            redirect('index.php?file=Admin', 3);
        }
        else {
            echo $arrayErrorMsg[$errorLogin];
        }

    }
    else{
        // On affiche le header du layout
        adminHeader();
?>
    <div class="loginWrapper">
        <form id="login" class="validateForm" action="index.php?file=Admin&amp;nuked_nude=login" method="post">
            <div class="loginPic">
                <img alt="" src="<?php echo $GLOBALS['user']['avatar']; ?>">
                <span><?php echo $GLOBALS['user']['nickName']; ?></span>
            </div>
            <input type="text" id="loginMail" class="loginEmail validate[required,custom[email]]" placeholder="<?php echo MAIL; ?>" name="mail" />
            <input type="password" id="loginPassword" class="loginPassword validate[required]" placeholder="<?php echo PASSWORD; ?>" name="password" />
            <div class="logControl">
                <div class="memory">
                    <input type="checkbox" checked="checked" class="check" id="remember_me" />
                    <label for="remember_me"><?php echo REMEMBER_ME; ?></label>
                </div>
                <input type="submit" id="loginSubmit" class="buttonM bBlue" value="<?php echo LOGIN; ?>" name="submit" />
            </div>
        </form>
    </div>
    <div id="loginMessage" class="ajaxMessage ajaxLoading" title="<?php echo LOGIN; ?>">
        <p><?php echo CHECK_IN_PROGRESS; ?></p>
    </div>
<?php
        // On affiche le footer du layout
        adminFooter();
    }
}
else {
    // On affiche le header du layout
    adminHeader();
?>
    <div style="text-align: center;">
        <p><?php echo ZONEADMIN; ?></p>
        <a class="button" href="javascript:history.back()">
            <b><?php echo BACK; ?></b>
        </a>
    </div>
<?php
    // On affiche le footer du layout
    adminFooter();
}

?>
