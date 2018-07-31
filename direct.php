<?php
/**
 * Copyright (C) 2018  Andre Bahtiar Fauzi (andrebahtiarfauzi@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

// key to authenticate
define('INDEX_AUTH', '1');


// required file
require 'sysconfig.inc.php';
// member session params
require LIB.'member_session.inc.php';
// start session
session_start();
require LIB.'contents/common.inc.php';
if (isset($_GET['p'])) {
    $path = utility::filterData('p', 'get', false, true, true);
    // some extra checking
    $path = preg_replace('@^(http|https|ftp|sftp|file|smb):@i', '', $path);
    $path = preg_replace('@\/@i','',$path);
    // check if the file exists
    if (file_exists(LIB.'contents/'.$path.'.inc.php')) {
        include LIB.'contents/'.$path.'.inc.php';
        $metadata = '<meta name="robots" content="noindex, follow">';
    } else {
        // get content data from database
        $metadata = '<meta name="robots" content="index, follow">';
        include LIB.'content.inc.php';
        $content = new Content();
        $content_data = $content->get($dbs, $path);
        if ($content_data) {
            $page_title = $content_data['Title'];
            echo $content_data['Content'];
            unset($content_data);
        } else {
            // header ("location:index.php");
            // check in api router
            require 'api/v'.$sysconf['api']['version'].'/routes.php';
        }
    }
}