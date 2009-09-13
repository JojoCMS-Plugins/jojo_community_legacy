<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2009 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_community_legacy
 */

// Register - do not add the new page for new sites but leave it intact for existing sites

/*$data = Jojo::selectQuery("SELECT pageid FROM {page} WHERE pg_url = 'register'");
if (!count($data)) {
    echo "Adding <b>register</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Register', pg_link = 'Jojo_Plugin_Register', pg_url = 'register', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}
*/

// User-Profile  - do not add the new page for new sites but leave it intact for existing sites
/*
$data = Jojo::selectQuery("SELECT pageid FROM {page} WHERE pg_link = 'Jojo_Plugin_User_Profile'");
if (!count($data)) {
    echo "Adding <b>user-profile</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'User Profile', pg_link = 'Jojo_Plugin_User_Profile', pg_url = 'user-profile', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}
*/