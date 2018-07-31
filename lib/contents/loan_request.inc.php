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

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
    die("can not access this file directly");
}


// Request Loan form
$_forms  = '<div style="padding:30px 10px;">';
$_forms .= '<form method="post" action="index.php?p=member&id='.$_GET['id'].'" class="comment-form">';
$_forms .=  simbio_form_element::textField('textarea','address','','placeholder="Input address" class="comment-input"'). '<br />';
$_forms .= '<input type="submit" name="SaveLoanRequest" value="Submit Loan Request" class="button">';
$_forms .= '</form>';
$_forms .= '</div>';
echo $_list_comment.$_forms;
