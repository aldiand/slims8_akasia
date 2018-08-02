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
/* Loan Request By Members */
// TODO : UPDATE FOR CSRF_TOKEN SECURITY

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-circulation');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
// privileges checking
$can_read = utility::havePrivilege('circulation', 'r') || utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('circulation', 'w') || utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}


require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_element.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require MDLBS.'reporting/report_dbgrid.inc.php';

$membershipTypes = membershipApi::getMembershipType($dbs);
$page_title = 'Loan Request';
$reportView = false;
$num_recs_show = 20;


if (isset($_GET['action']) AND isset($_GET['id'])) {
    if(!empty($_GET['action']) AND !empty($_GET['id'])) {
        switch ($_GET['action']) {
            case 'confirm':
                if (checkStatus($_GET['id'], 0,0,0)) {
                    setStatus($_GET['id'], 1, 0 ,0);
                    setDate($_GET['id'], 'confirm_date');
                }
                break;
            case 'reject':
                if (checkStatus($_GET['id'], 0,0,0)) {
                    setStatus($_GET['id'], 0, 0 ,1, $_GET['reason']);
                }
                break;
            case 'send':
                if (checkStatus($_GET['id'], 1,0,0)) {
                    setStatus($_GET['id'], 1, 1 ,0);
                    setDate($_GET['id'], 'send_date');
                }
                break;
            default:
                break;
        }
    }
}

function checkStatus($dataid, $confirmed, $send, $rejected) {
    global $dbs;
    $_str_select_req = 'SELECT loan_request_id FROM loan_request WHERE loan_request_id='.$dataid.'  AND is_confirmed='.$confirmed.' AND is_rejected='.$rejected.' AND  is_send='.$send;
    $_status_q = $dbs->query($_str_select_req);
    if ($_status_q->num_rows > 0) return true;
    return false;
}

function setStatus($dataid, $confirmed, $send, $rejected, $reason = '-') {
    global $dbs;
    $_str_save_status_sql = '
        UPDATE `loan_request` 
        SET is_confirmed='.$confirmed.', is_send='.$send.', is_rejected='.$rejected.', librarian_note=\''.$reason.'\'
        WHERE loan_request_id = '.$dataid;
    @$dbs->query($_str_save_status_sql);
    if (!$dbs->error) {
        return true;
    } else {
        return false;
    }
}

function setDate($dataid, $columnName) {
    global $dbs;
    $_str_save_status_sql = '
        UPDATE `loan_request` 
        SET '.$columnName.'= NOW()
        WHERE loan_request_id = '.$dataid;
    @$dbs->query($_str_save_status_sql);
    if (!$dbs->error) {
        return true;
    } else {
        return false;
    }
}

if (isset($_GET['reportView'])) {
    $reportView = true;
}

if (!$reportView) {
?>
    <!-- filter -->
    <fieldset>
    <div class="per_title">
    	<h2><?php echo __('Loan Request'); ?></h2>
	  </div>
    <div class="infoBox">
    <?php echo __('Report Filter'); ?>
    </div>
    <div class="sub_section">
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
    <div id="filterForm">
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Member ID').'/'.__('Member Name'); ?></div>
            <div class="divRowContent">
            <?php
            echo simbio_form_element::textField('text', 'id_name', '', 'style="width: 50%"');
            ?>
            </div>
        </div>
        <div class="divRow">
          <div class="divRowLabel"><?php echo __('Membership Type'); ?></div>
          <div class="divRowContent">
            <select name="membershipType">
              <?php 
              foreach ($membershipTypes as $key => $membershipType) {
                echo '<option value="'.$key.'">'.$membershipType['member_type_name'].'</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Title'); ?></div>
            <div class="divRowContent">
            <?php
            echo simbio_form_element::textField('text', 'title', '', 'style="width: 50%"');
            ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Item Code'); ?></div>
            <div class="divRowContent">
            <?php
            echo simbio_form_element::textField('text', 'itemCode', '', 'style="width: 50%"');
            ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Address'); ?></div>
            <div class="divRowContent">
            <?php
            echo simbio_form_element::textField('text', 'address', '', 'style="width: 50%"');
            ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Request Date From'); ?></div>
            <div class="divRowContent">
            <?php
            echo simbio_form_element::dateField('input_date_from', date('2000-01-01'));
            ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Request Date Until'); ?></div>
            <div class="divRowContent">
            <?php
            echo simbio_form_element::dateField('input_date_until', date('Y-m-d'));
            ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Record each page'); ?></div>
            <div class="divRowContent"><input type="text" name="recsEachPage" size="3" maxlength="3" value="<?php echo $num_recs_show; ?>" /> <?php echo __('Set between 20 and 200'); ?></div>
        </div>
    </div>
    <div style="padding-top: 10px; clear: both;">
    <input type="button" class="button" name="moreFilter" value="<?php echo __('Show More Filter Options'); ?>" />
    <input type="submit" name="applyFilter" value="<?php echo __('Apply Filter'); ?>" />
    <input type="hidden" name="reportView" value="true" />
    </div>
    </form>
	</div>
    </fieldset>
    <!-- filter end -->
    <div class="dataListHeader" style="padding: 3px;"><span id="pagingBox"></span></div>
    <iframe name="reportView" id="reportView" src="<?php echo $_SERVER['PHP_SELF'].'?reportView=true'; ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
<?php
} else {
    ob_start();
    // table spec
    $table_spec = 'loan_request AS l
    LEFT JOIN member AS m ON l.member_id=m.member_id
    LEFT JOIN item AS i ON l.item_code=i.item_code
    LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id';

    // create datagrid
    $reportgrid = new report_datagrid();
    $reportgrid->invisible_fields = array(8,9,12);
    $reportgrid->setSQLColumn('m.member_id AS \''.__('Member ID').'\'',
        'm.member_name AS \''.__('Member Name').'\'',
        'm.member_type_id AS \''.__('Membership Type').'\'',
        'l.item_code AS \''.__('Item Code').'\'',
        'l.address AS \''.__('Address').'\'',
        'l.input_date AS \''.__('Input Date').'\'',
        'l.confirm_date AS \''.__('Confirm Date').'\'',
        'l.send_date AS \''.__('Send Date').'\'',
        'l.is_confirmed AS \''.__('Status').'\'',
        'l.is_rejected AS \''.__('Status').'\'',
        'l.is_send AS \''.__('Status Pengiriman').'\'',
        'l.librarian_note AS \''.__('Note').'\'',
        'l.loan_request_id AS \''.__('Id').'\'');
    $reportgrid->setSQLorder('l.input_date DESC');

    $criteria = '1=1';
    if (isset($_GET['id_name']) AND !empty($_GET['id_name'])) {
        $id_name = $dbs->escape_string($_GET['id_name']);
        $criteria .= ' AND (m.member_id LIKE \'%'.$id_name.'%\' OR m.member_name LIKE \'%'.$id_name.'%\')';
    }
    if (isset($_GET['title']) AND !empty($_GET['title'])) {
        $keyword = $dbs->escape_string(trim($_GET['title']));
        $words = explode(' ', $keyword);
        if (count($words) > 1) {
            $concat_sql = ' AND (';
            foreach ($words as $word) {
                $concat_sql .= " (b.title LIKE '%$word%') AND";
            }
            // remove the last AND
            $concat_sql = substr_replace($concat_sql, '', -3);
            $concat_sql .= ') ';
            $criteria .= $concat_sql;
        } else {
            $criteria .= ' AND b.title LIKE \'%'.$keyword.'%\'';
        }
    }
    if (isset($_GET['itemCode']) AND !empty($_GET['itemCode'])) {
        $item_code = $dbs->escape_string(trim($_GET['itemCode']));
        $criteria .= ' AND i.item_code=\''.$item_code.'\'';
    }
    // loan address
    if (isset($_GET['address']) AND !empty($_GET['address'])) {
        $address = $dbs->escape_string(trim($_GET['address']));
        $criteria .= ' AND l.address\''.$address.'\'';
    }
    // Request Date
    if (isset($_GET['input_date_from']) AND isset($_GET['input_date_until'])) {
        $criteria .= ' AND (TO_DAYS(l.input_date) BETWEEN TO_DAYS(\''.$_GET['input_date_from'].'\') AND
            TO_DAYS(\''.$_GET['input_date_until'].'\'))';
    }
	
    if (isset($_GET['recsEachPage'])) {
        $recsEachPage = (integer)$_GET['recsEachPage'];
        $num_recs_show = ($recsEachPage >= 20 && $recsEachPage <= 200)?$recsEachPage:$num_recs_show;
    }
    $reportgrid->setSQLCriteria($criteria);

   // callback function to show loan status
    function loanStatus($obj_db, $array_data)
    {
        if($array_data[8] == 0 AND $array_data[9] == 0 AND $array_data[10] == 0) {
            return '<strong style="color: #ffc81e;">UNCONFIRMED</strong>';
        }
        else if($array_data[8] == 0 AND $array_data[9] == 1 AND $array_data[10] == 0) {
            return '<strong style="color: #f00;">REJECTED</strong>';
        }
        else if($array_data[8] == 1 AND $array_data[9] ==0 AND $array_data[10] == 0) {
            return '<strong style="color: #0032ff;">CONFIRMED</strong>';
        }
        else if($array_data[8] == 1 AND $array_data[9]==0 AND $array_data[10] == 1) {
            return '<strong style="color: #00ff12;">BOOK SENT</strong>';
        }
        return 'hehe';
    }

    // callback function for note button
    function loanNote($obj_db, $array_data) {
        $dataid = $array_data[12];
        $confirm = '<button onclick="formSubmit(\'Are you sure to confirm this loan request?\', \'confirm\', '.$dataid.' )">' .__('Confirm').'</button>';
        $reject = '<button onclick="formSubmit(\'Please enter the reason: \', \'reject\', '.$dataid.' )">' .__('Reject').'</button>';
        $send = '<button onclick="formSubmit(\'Are you sure to send this loan request?\', \'send\', '.$dataid.' )">' .__('Send').'</button>';
        if($array_data[8] == 0 AND $array_data[9] == 0 AND $array_data[10] == 0) {
            return $confirm.$reject;
        }
        else if($array_data[8] == 0 AND $array_data[9] == 1 AND $array_data[10] == 0) {
            return $array_data[11];
        }
        else if($array_data[8] == 1 AND $array_data[9] ==0 AND $array_data[10] == 0) {
            return $send;
        }
        else if($array_data[8] == 1 AND $array_data[9]==0 AND $array_data[10] == 1) {
            return $array_data[11];
        }
        return 'Something went wrong';

    }

    function showMembershipType($obj_db, $array_data)
    {
      global  $membershipTypes;
      $_member_type_id = $array_data[2];
      return $membershipTypes[$_member_type_id]['member_type_name'];
    }

    // modify column value
    $reportgrid->modifyColumnContent(11, 'callback{loanNote}');
    $reportgrid->modifyColumnContent(10, 'callback{loanStatus}');
    $reportgrid->modifyColumnContent(2, 'callback{showMembershipType}');

    // put the result into variables
    echo $reportgrid->createDataGrid($dbs, $table_spec, $num_recs_show);

    echo '<script type="text/javascript">'."\n";
    echo 'parent.$(\'#pagingBox\').html(\''.str_replace(array("\n", "\r", "\t"), '', $reportgrid->paging_set).'\');'."\n";
    echo '</script>';
	$xlsquery = 'SELECT m.member_id AS \''.__('Member ID').'\''.
        ', m.member_name AS \''.__('Member Name').'\''.
        ', l.item_code AS \''.__('Item Code').'\''.
        ', b.title AS \''.__('Title').'\''.
        ', l.address AS \''.__('Address').'\''.
        ', l.input_date AS \''.__('Request Date').'\''.
        ', l.confirm_date AS \''.__('Confirm Date').'\''.
        ', l.send_date AS \''.__('Send Date').'\''.
        ', l.librarian_note AS \''.__('Note').'\''.
		' FROM '.$table_spec.' WHERE '.$criteria;

		unset($_SESSION['xlsdata']);
		$_SESSION['xlsquery'] = $xlsquery;
		$_SESSION['tblout'] = "loan_request";

	echo '<div class="s-export"><a href="xlsoutput.php" class="button">'.__('Export to spreadsheet format').'</a></div>';

    echo '
    <script>
        /* function to collect checkbox data and submit form */
        function formSubmit (strMessage, strAction, dataId) {
            var confirmMsg = arguments[0];
            if (strAction != \'reject\') {
                var isConfirm = confirm(confirmMsg);
                if (isConfirm) {
                    document.location.href = document.location.href + \'&action=\' + strAction + \'&id=\' + dataId;
                }
            } else {
                var userPromp = prompt(strMessage, \'-\');
                if (userPromp == null || userPromp == "") {
                    alert("Action cancelled!");
                } else {
                    document.location.href = document.location.href + \'&action=\' + strAction + \'&id=\' + dataId + \'&reason=\' + userPromp;
                }
            }
        }
    </script>
    ';
    $content = ob_get_clean();
    // include the page template
    require SB.'/admin/'.$sysconf['admin_template']['dir'].'/printed_page_tpl.php';
}
?>
