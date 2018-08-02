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


// key to authenticate
define('INDEX_AUTH', '1');

require '../../../sysconfig.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_UTILS/simbio_date.inc.php';

// Request Loan form
$_forms  = '<div style="padding:30px 10px;">';
$_forms .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$_GET['id'].'" class="comment-form">';
$_forms .=  simbio_form_element::textField('textarea','address','','placeholder="Input Note" class="comment-input"'). '<br />';
$_forms .= '<input type="submit" name="RejectLoanRequest" value="Reject Loan Request" class="button">';
$_forms .= '</form>';
$_forms .= '</div>';
echo $_forms;
