<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

/* This plugin is deprecated, and exists for backwards compatibility purposes only. Please install the jojo_community plugin which is replacing this plugin. */

class Jojo_Plugin_Register extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;
        $content = array();

        $approvecode = Jojo::getFormData('approvecode', false);
        $deletecode  = Jojo::getFormData('deletecode',  false);

        /* handle usergroup approvals */
        if ($approvecode) {
            $groupid = Jojo::getFormData('groupid', false);
            $users = Jojo::selectQuery("SELECT userid, us_firstname, us_lastname FROM {user} WHERE us_approvecode=?", $approvecode);
            if (!count($users)) {
                $content['content'] = 'This approval link is invalid.';
                return $content;
            }
            $u = $users[0];

            /* add user to group */
            Jojo::insertQuery("REPLACE INTO {usergroup_membership} SET userid=?, groupid=?", array($u['userid'], $groupid));

            $content['content'] = $u['us_firstname'].' '.$u['us_lastname'].' added to '.$groupid.' group.';
            return $content;
        }

        /* handle user deletions */
        if ($deletecode) {
            $groupid = Jojo::getFormData('groupid', false);
            $users = Jojo::selectQuery("SELECT userid, us_firstname, us_lastname FROM {user} WHERE us_deletecode=?", $deletecode);
            if (!count($users)) {
                $content['content'] = 'This delete link is invalid, or the user has already been deleted.';
                return $content;
            }
            $u = $users[0];
            Jojo::deleteQuery("DELETE FROM {user} WHERE userid=?", $u['userid']);
            Jojo::deleteQuery("DELETE FROM {usergroup_membership} WHERE userid=?", $u['userid']);

            $content['content'] = $u['us_firstname'].' '.$u['us_lastname'].' deleted.';
            return $content;
        }

        $redirect = Jojo::getFormData('redirect', false);

        $errors  = array();

        $message = '';
        if (isset($_POST['submit'])) {
            $firstname    = Jojo::getPost('firstname', '');
            $lastname     = Jojo::getPost('lastname', '');
            $login        = Jojo::getPost('reglogin', '');
            $password     = Jojo::getPost('regpassword', '');
            $password2    = Jojo::getPost('regpassword2', '');
            $email        = Jojo::getPost('email', '');
            $emailaddress = Jojo::getPost('email', ''); //$email is wiped later on in the script
            $defaultgroup = Jojo::getOption('defaultgroup'); //specified in options
            $reminder     = Jojo::getPost('reminder', '');
            $website      = Jojo::getPost('website', '');
            $location     = Jojo::getPost('location', '');
            $signature    = Jojo::getPost('signature', '');
            $tagline      = Jojo::getPost('tagline', '');

            /* Error checking */
            if ($firstname == '')                                   $errors[] = 'Please enter your first name';
            if ($lastname == '')                                    $errors[] = 'Please enter your last name';
            if ($login == '')                                       $errors[] = 'Please enter a preferred login';
            if ($password == '')                                    $errors[] = 'Please enter your password';
            elseif ($password != $password2)                        $errors[] = 'Passwords do not match, please check';
            elseif (strlen($password) < 8)                          $errors[] = 'Password should be at least 8 characters';
            elseif (strtolower($password) == 'password')            $errors[] = 'Password cannot be "password"';
            elseif (strtolower($password) == strtolower($login))    $errors[] = 'Password cannot the same as your user name';
            if ($email == '')                                       $errors[] = 'Please enter your email address';
            if (($email != '') &&  !Jojo::checkEmailFormat($email)) $errors[] = 'Please enter a valid email address';
            if ($reminder == '')                                    $errors[] = 'Please enter a password reminder';
            if ($reminder == $password)                             $errors[] = 'Password reminder cannot be the same as your password';

            /* Check user does not already exist */
            $user = Jojo::selectQuery("SELECT userid FROM {user} WHERE us_login = ? AND us_login != ''", array($login));
            if (count($user)) {
                $errors[] = 'The username "' . $login . '" is already taken';
            }
            /* Check email address is not already in the system */
            $user = Jojo::selectQuery("SELECT userid FROM {user} WHERE us_email = ? AND us_email != ''", array($emailaddress));
            if (count($user)) {
                $errors[] = 'The email "' . $emailaddress . '" is already in use by another user';
            }

            if (!count($errors)) {
                $approvecode = Jojo::randomString(16);
                $deletecode  = Jojo::randomString(16);

                Jojo::insertQuery("INSERT INTO {user} SET us_login = ?, us_password = ?,
                                    us_reminder = ?, us_firstname = ?,
                                    us_lastname = ?, us_email = ?, us_signature = ?,
                                    us_status = 'active', us_tagline = ?,
                                    us_website = ?, us_location = ?, us_approvecode = ?,
                                    us_deletecode = ?",
                            array(
                                $login, sha1($password), $reminder, $firstname, $lastname,
                                $email, $signature, $tagline, $website, $location,
                                $approvecode, $deletecode
                                )
                );

                $user = Jojo::selectQuery("SELECT userid FROM {user} WHERE us_login = ? AND us_password = ? LIMIT 1", array($login, sha1($password)));
                if (!count($user)) {
                    $errors[] = 'An error occured. Please contact the webmaster if this error continues.';
                } else {
                    if ($defaultgroup != '') {
                        Jojo::insertQuery("INSERT INTO {usergroup_membership} (userid, groupid) VALUES (?, ?)", array($user[0]['userid'], $defaultgroup));
                    }
                    $message = 'Your registration was successful.';
                    $smarty->assign('success', true);

                    /* log them in */
                    $_USERID = $user[0]['userid'];
                    $_SESSION['userid'] = $_USERID;

                    $_USERGROUPS = array('everyone');
                    $groups = Jojo::selectQuery("SELECT * FROM {usergroup_membership} WHERE userid = ?", $_USERID);
                    foreach ($groups as $group) {
                        $_USERGROUPS[] = $group['groupid'];
                    }

                    $username = $login;
                    $_SESSION['username'] = $username;
                    Jojo::runHook('register_complete');
                }

                $email  = "A new User has registered on " . _SITEURL . "\n\n";
                $email .= "Submitted by: " . $firstname . " " . $lastname . "\n";
                $email .= $emailaddress != '' ? "Email: " . $emailaddress . "\n" : '';

                /* provide links for adding the user into each group */
                $allgroups = Jojo::selectQuery("SELECT * FROM {usergroups} WHERE groupid!='notloggedin' ORDER BY groupid");
                foreach ($allgroups as $g) {
                    if ($g['groupid'] != $defaultgroup) { //no need to include a link for adding them to the default group
                        $email .= "\nTo add the user to the '".$g['gr_name']."' group\n";
                        $email .= _SITEURL.'/register/approve/'.$g['groupid'].'/'.$approvecode."/\n";
                    }
                }
                /* and a link for deleting the user */
                $email .= "\nTo DELETE this User, click the following link\n";
                $email .= _SITEURL.'/register/delete/'.$deletecode."/\n";

                $email .= Jojo::emailFooter();

                /* Email notification to webmaster */
                Jojo::simplemail(_WEBMASTERNAME, _WEBMASTERADDRESS, 'User Registration - '._SITETITLE, $email);

                if ($redirect) {
                    Jojo::redirect(_SITEURL . '/' . $redirect, 302);
                }
            }

            /* Errors - return to form */
            $smarty->assign('firstname',    $firstname);
            $smarty->assign('lastname',     $lastname);
            $smarty->assign('reglogin',     $login);
            $smarty->assign('regpassword',  $password);
            $smarty->assign('regpassword2', $password2);
            $smarty->assign('email',        $emailaddress);
            $smarty->assign('reminder',     $reminder);
            $smarty->assign('website',      $website);
            $smarty->assign('location',     $location);
            $smarty->assign('signature',    $signature);
            $smarty->assign('tagline',      $tagline);
        }

        $smarty->assign('redirect', $redirect);
        $smarty->assign('message',  $message);
        $smarty->assign('error',    implode("<br />\n", $errors));
        $content['content'] = $smarty->fetch('register.tpl');
        $content['head']    = $smarty->fetch('register_head.tpl');
        return $content;
    }

    function getCorrectUrl()
    {
        $approvecode = Jojo::getFormData('approvecode', false);
        $deletecode  = Jojo::getFormData('deletecode',  false);
        $redirect    = Jojo::getGet('redirect', false);

        if ($approvecode || $deletecode) {
            return _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        if ($redirect) {
            return parent::getCorrectUrl() . $redirect . '/';
        }
        return parent::getCorrectUrl();
    }

}