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

class Jojo_Plugin_User_profile extends Jojo_Plugin
{

    function uploadAvatar() {
        if (isset($_FILES['avatar'])) {

            $filename = $_FILES['avatar']['name']; //for convenience
            //We must not allow PHP files to be uploaded to the server as the visitor could guess the location and execute them.
            $ext = strtolower(Jojo::getfileextension($filename));
            if ( ($ext == 'php') || ($ext == 'php3') || ($ext == 'php4') || ($ext == 'inc') || ($ext == 'phtml')) {
                echo "You cannot upload PHP files into this system for security reasons. If you really need to, please Zip them first and upload the Zip file.";
                exit();
            }

            //TODO: Check destination directory exists etc

            //Check error codes
            switch ($_FILES['avatar']['error']) {
                case UPLOAD_ERR_INI_SIZE: //1
                    //error
                    break;
                case UPLOAD_ERR_FORM_SIZE: //2
                    //error
                    break;
                case UPLOAD_ERR_PARTIAL: //3
                    //error
                    break;
                case UPLOAD_ERR_NO_FILE: //4 - this is only a problem if it's a required field
                    //remember, a required field only needs to be set the first time, perhaps its better to check this somewhere else
                    //if ($this->fd_required == "yes") {
                    //    $this->error = "Required field";
                    //}
                    break;
                case 6: // UPLOAD_ERR_NO_TMP_DIR - for some odd reason the constant wont work
                    //error
                    //log for administrator
                    break;
                case UPLOAD_ERR_OK: //0
                    //check for empty file
                    if($_FILES['avatar']["size"] == 0) {
                        //error
                    }
                    if (!is_uploaded_file($_FILES['avatar']['tmp_name'])) { //improve this code when you have time - will work, but needs fleshing out
                        //log
                        die("Upload error. Script will now halt.");
                    }
                    //All appears good, so attempt to move to final resting place
                    $destination = _DOWNLOADDIR . '/users/' . basename($filename);
                    Jojo::recursiveMkdir(dirname($destination));

                    //Ensure file does not already exist on server, rename if it does
                    $i=1;
                    while (file_exists($destination)){
                        $i++;
                        $newname = $i . "_" . $filename;
                        $destination = _DOWNLOADDIR . '/users/' . $newname;
                    }

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                        $message = "Upload successful";
                        //echo "Upload successful";
                        $this->value = isset($newname) ? $nename : $filename ;
                    } else {
                        //log
                        die("File upload error. Script will now halt.");
                    }
                    break;
                default:
                    //this code shouldn't execute - 0 should be the default
            }
            return basename($destination);
        }
        return '';
    }

    function _getContent()
    {
        global $smarty, $_USERID;
        $errors = array();
        $content = array();

        if (empty($_USERID)) {
            echo "There was an error accessing your profile. Please check you are logged in and try again.";
            exit();
        }

        if (isset($_POST['submit'])) {
            /* get details of existing user */
            $data = Jojo::selectRow("SELECT us_login FROM {user} WHERE userid = ?", array($_USERID));
            if (isset($data)) {
                $oldlogin = $data['us_login'];
            }



            $newfirstname = Jojo::getPost('firstname','');
            $newlastname  = Jojo::getPost('lastname','');
            $newlogin     = Jojo::getPost('reglogin','');
            $newpassword  = Jojo::getPost('regpassword','');
            $newpassword2 = Jojo::getPost('regpassword2','');
            $newemail     = Jojo::getPost('email','');
            $newreminder  = Jojo::getPost('reminder','');
            $newwebsite   = Jojo::getPost('website','');
            $newlocation  = Jojo::getPost('location','');
            $newsignature = Jojo::getPost('signature','');
            $newtagline   = Jojo::getPost('tagline','');
            $newavatar    = Jojo_Plugin_User_profile::uploadAvatar();
            $message      = '';

            /* error checking */
            if (empty($newfirstname))                                      $errors[] = 'Please enter your first name';
            if (empty($newlastname))                                       $errors[] = 'Please enter your last name';
            if (empty($newlogin))                                          $errors[] = 'Please enter a preferred login';
            if (empty($newemail))                                          $errors[] = 'Please enter your email address';
            if (!empty($newemail) && !Jojo::checkEmailFormat($newemail))   $errors[] = 'Please enter a valid email address';
            if (!empty($newwebsite) && !Jojo::checkUrlFormat($newwebsite)) $errors[] = 'Please enter a valid website';
            if ($newpassword != '') {
                if ($newpassword != $newpassword2)                     $errors[] = 'Passwords do not match, please check';
                if (strlen($newpassword) < 8)                          $errors[] = 'Password should be at least 8 characters';
                if (strtolower($newpassword) == 'password')            $errors[] = 'Password cannot be "password"';
                if (strtolower($newpassword) == strtolower($newlogin)) $errors[] = 'Password cannot the same as your user name';
                if ($newreminder == $newpassword)                      $errors[] = 'Password reminder cannot be the same as your password';
            }

            /* Check user does not already exist */
            if ($oldlogin != $newlogin) {
                $user = Jojo::selectQuery("SELECT `userid` FROM {user} WHERE `us_login` = ? AND us_login != ''", array($login));
                if (count($user)) {
                    $errors[] = 'The username "' . $login . '" is already taken';
                }
            }

            /* no errors */
            if (!count($errors)) {
                Jojo::selectQuery("UPDATE {user} SET
                    us_login = ?, us_firstname = ?, us_lastname = ?,
                    us_email = ?, us_signature = ?, us_avatar = ?,
                    us_tagline = ?, us_website = ?, us_location = ?
                    WHERE userid = ? LIMIT 1", array(
                    $newlogin, $newfirstname, $newlastname, $newemail, $newsignature,
                    $newavatar, $newtagline, $newwebsite, $newlocation, $_USERID
                    ));

                $user = Jojo::selectRow("SELECT userid FROM {user} WHERE us_login = ?", array($newlogin));
                if (!count($user)) {
                    $errors[] = 'An error occured. Please contact the webmaster if this error continues.';
                } else {
                    $message = "Your user profile has been updated.";
                }
                $data = Jojo::selectRow("SELECT us_firstname, us_lastname, us_login, us_email, us_reminder, us_website, us_location, us_signature, us_avatar, us_tagline FROM {user} WHERE userid = ?", array($_USERID));
                if (isset($data)) {
                    $firstname  = $data['us_firstname'];
                    $lastname   = $data['us_lastname'];
                    $login      = $data['us_login'];
                    $email      = $data['us_email'];
                    $reminder   = $data['us_reminder'];
                    $website    = $data['us_website'];
                    $location   = $data['us_location'];
                    $signature  = $data['us_signature'];
                    $avatar     = $data['us_avatar'];
                    $tagline    = $data['us_tagline'];
                }
            } else {
                /* Errors - return to form */
                $firstname  = $newfirstname;
                $lastname   = $newlastname;
                $login      = $newlogin;
                $email      = $newemail;
                $reminder   = $newreminder;
                $website    = $newwebsite;
                $location   = $newlocation;
                $signature  = $newsignature;
                $tagline    = $newtagline;
                $avatar     = $newavatar;
                $password   = $newpassword;
                $password2  = $newpassword2;
            }

        } else {
            $data = Jojo::selectRow("SELECT us_firstname, us_lastname, us_login, us_email, us_reminder, us_website, us_location, us_signature, us_avatar, us_tagline FROM {user} WHERE userid = ?", array($_USERID));
            if (isset($data)) {
                $firstname  = $data['us_firstname'];
                $lastname   = $data['us_lastname'];
                $login      = $data['us_login'];
                $email      = $data['us_email'];
                $reminder   = $data['us_reminder'];
                $website    = $data['us_website'];
                $location   = $data['us_location'];
                $signature  = $data['us_signature'];
                $avatar     = $data['us_avatar'];
                $tagline    = $data['us_tagline'];
            }
        }

        $smarty->assign('firstname', $firstname);
        $smarty->assign('lastname', $lastname);
        $smarty->assign('reglogin', $login);
        $smarty->assign('regpassword', (isset($password) ? $password : '') );
        $smarty->assign('regpassword2', (isset($password2) ? $password2 : '') );
        $smarty->assign('email', $email);
        $smarty->assign('reminder', $reminder);
        $smarty->assign('website', $website);
        $smarty->assign('location', $location);
        $smarty->assign('signature', $signature);
        $smarty->assign('avatar', $avatar);
        $smarty->assign('tagline', $tagline);
        $smarty->assign('message', (isset($message) ? $message : ''));
        $smarty->assign('error', implode("<br />\n", $errors));
        $content['content'] = $smarty->fetch('user-profile.tpl');

        return $content;
    }

}