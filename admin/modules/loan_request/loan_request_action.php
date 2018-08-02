<?php
/**
 *
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


// main system configuration
require '../../../sysconfig.inc.php';
if (isset($_POST['action']) AND isset($_POST['id'])) {
    if(!empty($_POST['action']) AND !empty($_POST['id'])) {
        switch ($_POST['action']) {
            case 'confirm':
                if (checkStatus($_POST['id'], 0,0,0)) {
                    setStatus($_POST['id'], 1, 0 ,0);
                    return true;
                }
                break;
            case 'reject':
                if (checkStatus($_POST['id'], 0,0,0)) {
                    setStatus($_POST['id'], 0, 0 ,1);
                    return true;
                }
                break;
            case 'send':
                if (checkStatus($_POST['id'], 1,0,0)) {
                    setStatus($_POST['id'], 1, 1 ,0);
                    return true;
                }
                break;
            default:
                break;
        }
    }
}

function checkStatus($dataid, $confirmed, $send, $rejected) {
    global $dbs;
    $_str_select_req = 'SELECT loan_request_id FROM loan_request WHERE loan_request_id='.$dataid.'  AND is_confirmed='.$confirmed.'AND is_rejected='.$rejected.' AND  is_send='.$send;
    $_status_q = $dbs->query($_str_select_req);
    if ($_status_q->num_rows > 0) return true;
    return false;
}

function setStatus($dataid, $confirmed, $send, $rejected) {
    global $dbs;
    $_str_save_status_sql = '
        UPDATE `loan_request` 
        SET is_confirmed='.$confirmed.', is_send='.$send.', is_rejected='.$rejected.'
        WHERE loan_request_id = '.$dataid;
    @$dbs->query($_str_save_status_sql);
    if (!$dbs->error) {
        return true;
    } else {
        return false;
    }
}