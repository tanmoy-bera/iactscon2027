<?php
include_once('includes/init.php');

page_header("Participants");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]     = $pageKeyVal;
$searchArray['quiz_id']    = $_REQUEST['quiz_id'];
$searchArray['group_name'] = $_REQUEST['group_name'];
$searchArray['group_code'] = $_REQUEST['group_code'];
$searchArray['quiz_date']  = $_REQUEST['quiz_date'];
$searchArray['start_date'] = $_REQUEST['start_date'];
$searchArray['end_date']   = $_REQUEST['end_date'];

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ . "webmaster/util/" ?>font-awesome-4.7.0/css/font-awesome.min.css" />
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
    <?php
    switch ($show) {
        // show add window
        case 'add':
            break;
        // show all record	
        default:
            showAllParticipants($cfg, $mycms);
            break;
    }
    ?>
</div>
<?php
page_footer();

/*============== SHOW ALL COUNTRY WINDOW =============*/
function showAllParticipants($cfg, $mycms)
{
    global $searchArray, $searchString;
?>
    <form name="frmParticipantListing" id="frmParticipantListing" action="<?= $cfg['SECTION_BASE_URL'] ?>participation.process.php" method="post">
        <input type="hidden" name="act" id="act" value="search_participants" />
        <table width="100%" align="center" class="tborder">
            <tr>
                <td colspan="2" align="left" class="tcat">
                    <span style="float:left">Participants</span>
                    <span class="tsearchTool" forType="tsearchTool"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="margin:0px; padding:0px;">
                    <div class="tsearch">
                        <table width="100%">
                            <tr>
                                <td align="left">
                                    Group Name:
                                    <input type="text" name="group_name" id="group_name"
                                        value="<?= $_REQUEST['group_name'] ?>" />
                                    &nbsp;

                                    Group Code:
                                    <input type="text" name="group_code" id="group_code"
                                        value="<?= $_REQUEST['group_code'] ?>" />
                                    &nbsp;

                                    Quiz Date:
                                    <input type="date" name="quiz_date" id="quiz_date"
                                        value="<?= $_REQUEST['quiz_date'] ?>" />
                                    &nbsp;

                                    Applied From:
                                    <input type="date" name="start_date" id="start_date"
                                        value="<?= $_REQUEST['start_date'] ?>" />
                                    &nbsp;

                                    Applied To:
                                    <input type="date" name="end_date" id="end_date"
                                        value="<?= $_REQUEST['end_date'] ?>" />
                                    &nbsp;

                                    <?php
                                    searchStatus();
                                    ?>
                                    <input type="submit" name="searchBtn" id="searchBtn" value="Search" class="btn btn-small btn-blue" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table width="100%">
                        <tr class="theader">
                            <td width="20" align="center">
                                <input name="check_all" id="check_all" targetElement="checkBoxListing"
                                    type="checkbox" onclick="checkall(this);" />
                            </td>
                            <td width="20" align="center">Sl No </td>
                            <td width="100" align="left">Quiz Title</td>
                            <td width="40" align="center">Quiz Date</td>
                            <td width="40" align="center">Applied On</td>
                            <td width="50" align="center">Participation Type</td>
                            <td width="70" align="center">Group Name</td>
                            <td width="50" align="center">Group Code</td>
                            <td width="50" align="center">Members</td>
                            <td width="50" align="center">Status</td>
                            <td width="70" align="center">Action</td>
                        </tr>
                        <?php
                        // --- Initialize ---
                        $con = "";
                        $params = [];

                        // --- Optional Filters ---
                        if (!empty($_REQUEST['quiz_id'])) {
                            $con .= " AND qp.quiz_id = ?";
                            $params[] = [
                                'FILD' => 'quiz_id',
                                'DATA' => $_REQUEST['quiz_id'],
                                'TYP'  => 'i'
                            ];
                        }

                        if (!empty($_REQUEST['group_name'])) {
                            $con .= " AND qp.group_name LIKE ?";
                            $params[] = [
                                'FILD' => 'group_name',
                                'DATA' => '%' . trim($_REQUEST['group_name']) . '%',
                                'TYP'  => 's'
                            ];
                        }

                        if (!empty($_REQUEST['group_code'])) {
                            $con .= " AND qp.group_code LIKE ?";
                            $params[] = [
                                'FILD' => 'group_code',
                                'DATA' => '%' . trim($_REQUEST['group_code']) . '%',
                                'TYP'  => 's'
                            ];
                        }

                        if (!empty($_REQUEST['quiz_date'])) {
                            $con .= " AND DATE(q.start_time) = ?";
                            $params[] = [
                                'FILD' => 'start_time',
                                'DATA' => $_REQUEST['quiz_date'],
                                'TYP'  => 's'
                            ];
                        }

                        if (!empty($_REQUEST['start_date'])) {
                            $con .= " AND DATE(qp.created_at) >= ?";
                            $params[] = [
                                'FILD' => 'created_at',
                                'DATA' => $_REQUEST['start_date'],
                                'TYP'  => 's'
                            ];
                        }

                        if (!empty($_REQUEST['end_date'])) {
                            $con .= " AND DATE(qp.created_at) <= ?";
                            $params[] = [
                                'FILD' => 'created_at',
                                'DATA' => $_REQUEST['end_date'],
                                'TYP'  => 's'
                            ];
                        }

                        if (!empty($_REQUEST['status'])) {
                            $con .= " AND qp.status = ?";
                            $params[] = [
                                'FILD' => 'row_status',
                                'DATA' => $_REQUEST['status'],
                                'TYP'  => 's'
                            ];
                        }

                        // --- Query ---
                        // $sql = [];
                        // $sql['QUERY'] = "SELECT 
                        //     qp.id AS participation_id,
                        //     q.id AS quiz_id,
                        //     q.title AS quiz_title,
                        //     DATE(q.start_time) AS quiz_date,
                        //     DATE(qp.created_at) AS applied_on,
                        //     qp.participation_type,
                        //     qp.group_name,
                        //     qp.group_code,
                        //     qp.status,
                        //     GROUP_CONCAT(ur.user_full_name ORDER BY ur.user_full_name SEPARATOR ', ') AS member_names
                        // FROM quiz_participation AS qp
                        // LEFT JOIN rcg_quizzes AS q ON qp.quiz_id = q.id
                        // LEFT JOIN rcg_quiz_participation_members AS pm ON qp.id = pm.participation_id
                        // LEFT JOIN rcg_user_registration AS ur ON pm.user_id = ur.id
                        // WHERE q.row_status != ?
                        // $con
                        // GROUP BY qp.id
                        // ORDER BY qp.created_at DESC";

                        $sql = [];
                        $sql['QUERY'] = "
                            SELECT 
                                qp.id AS participation_id,
                                q.id AS quiz_id,
                                q.title AS quiz_title,
                                DATE(q.start_time) AS quiz_date,
                                DATE(qp.created_at) AS applied_on,
                                qp.participation_type,
                                qp.group_name,
                                qp.group_code,
                                qp.status,
                                GROUP_CONCAT(ur.user_full_name ORDER BY ur.user_full_name SEPARATOR ', ') AS member_names
                            FROM quiz_participation AS qp
                            LEFT JOIN rcg_quizzes AS q ON qp.quiz_id = q.id
                            LEFT JOIN rcg_quiz_participation_members AS pm ON qp.id = pm.participation_id
                            LEFT JOIN rcg_user_registration AS ur ON pm.user_id = ur.id
                            WHERE q.row_status != ?
                            $con
                            GROUP BY qp.id
                            ORDER BY qp.created_at DESC
                        ";

                        $sql['PARAM'][] = [
                            'FILD' => 'row_status',
                            'DATA' => 'D',
                            'TYP'  => 's'
                        ];

                        foreach ($params as $p) {
                            $sql['PARAM'][] = $p;
                        }

                        // --- Pagination ---
                        $result = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);

                        // --- Render Table Rows ---
                        if ($result) {
                            foreach ($result as $i => $row) {
                                @$i++;
                        ?>
                                <tr class="tlisting">
                                    <td align="center">
                                        <input type="checkbox" name="selected_ids[]" id="checkvalue" class="checkBoxListing"
                                            value="<?= $row['participation_id'] ?>" />
                                    </td>
                                    <td align="center"><?= $i + ($_REQUEST['_pgn_'] * 15) ?></td>
                                    <td align="left"><?= htmlspecialchars($row['quiz_title']) ?></td>
                                    <td align="center"><?= htmlspecialchars($row['quiz_date']) ?></td>
                                    <td align="center"><?= htmlspecialchars($row['applied_on']) ?></td>
                                    <td align="left"><?= ucfirst($row['participation_type']) ?></td>
                                    <td align="left"><?= htmlspecialchars($row['group_name']) ?></td>
                                    <td align="center"><?= htmlspecialchars($row['group_code']) ?></td>
                                    <?php /*
                                    <td align="center"><?= htmlspecialchars($row['member_names'] ?: '-') ?></td>
                                    <td align="left"><?= !empty($row['member_names']) ? implode('<br>', array_map('trim', explode(',', $row['member_names']))) : '-' ?></td>
                                    */
                                    $member_name = !empty($row['member_names']) ? strtolower($row['member_names']) : '';
                                    $member_name = ucwords($member_name);
                                    ?>
                                    <td align="left"><?= !empty($member_name) ? implode('<br>', array_map('trim', explode(',', $member_name))) : '-' ?>


                                    <td align="center"><?= htmlspecialchars($row['status']) ?></td>
                                    <td align="center">
                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>participation.process.php?id=<?= $row['participation_id'] ?>&act=Approve<?= $searchString ?>"
                                            onclick="return confirm('Are you sure you want to approve this participation?');">
                                            <i class="fa fa-check" aria-hidden="true" style="color:#28a745;" title="Approve"></i>
                                        </a>
                                        &nbsp;
                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>participation.process.php?id=<?= $row['participation_id'] ?>&act=Reject<?= $searchString ?>"
                                            onclick="return confirm('Are you sure you want to reject this participation?');">
                                            <i class="fa fa-times" aria-hidden="true" style="color:#dc3545;" title="Reject"></i>
                                        </a>
                                    </td>

                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="11" align="center">No records found</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>

            <tr>
                <td width="50%" align="left">
                    <select name="multiOperationSelector" id="multiOperationSelector">
                        <option value="">Choose an action...</option>
                        <option value="Approve">Approve</option>
                        <option value="Reject">Reject</option>
                    </select>
                    <input value="Apply to selected" name="applyMultiAction" type="button"
                        class="btn btn-small btn-blue" id="applyMultiActionBtn" />
                </td>
                <td width="50%" align="right">
                </td>
            </tr>
            <tr class="tfooter">
                <td colspan="2">
                    <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                    <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                </td>
            </tr>
        </table>
    </form>

    <script>
        // Select/Deselect All
        $('#check_all').on('click', function() {
            $('.checkBoxListing').prop('checked', this.checked);
        });
        // Apply Button Click
        $('#applyMultiActionBtn').on('click', function() {
            let action = $('#multiOperationSelector').val();
            let selected = $('.checkBoxListing:checked').map(function() {
                return $(this).val();
            }).get();

            if (action === '') {
                alert('Please choose an action.');
                return;
            }
            if (selected.length === 0) {
                alert('Please select at least one quiz.');
                return;
            }

            console.log(selected);
            // return;
            if (confirm(`Are you sure you want to ${action.toLowerCase()} the selected quizzes?`)) {
                $.ajax({
                    url: 'participation.process.php',
                    type: 'POST',
                    data: {
                        act: 'MultiAction',
                        action: action,
                        ids: selected
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload(); // refresh after success
                    }
                });
            }
        });
    </script>
<?php
}
?>