<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');
include_once('./cardiac_talks_quiz_common.php');

$quizId = (int)(isset($_REQUEST['quiz_id']) ? $_REQUEST['quiz_id'] : 0);
$quiz   = cq_get_quiz($mycms, $quizId);
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Discussions, Quizzes & Polls</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <?php cq_styles(); ?>
    <?php cq_flash(); ?>

    <div class="body_wrap">
        <?php
        if (empty($quiz)) {
            echo '<div class="cq_notice">Quiz not found. <a href="cardiac_talks_discussion_quiz_poll.php?tab=quiz">Back to quiz list</a></div>';
        } else {
            cq_header($quiz, 'applicants');
            if (!isset($quiz['join_type']) || $quiz['join_type'] !== 'approval') {
                echo '<div class="cq_notice">This quiz is set to <b>Anyone can join</b>, so there are no applications to review. '
                   . '<a href="cardiac_talks_quiz_participants.php?quiz_id=' . (int)$quiz['id'] . '">View participants</a>'
                   . ' &middot; to require approval, change <b>Joining Access</b> in the quiz details.</div>';
            } else {
                manageApplicants($cfg, $mycms, $quiz);
            }
        }
        ?>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<?php cq_scripts(); ?>
<script>
    $(document).on('click', '#check_all_app', function () {
        $('.checkBoxApp').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxApp', function () {
        $('#check_all_app').prop('checked', $('.checkBoxApp').length === $('.checkBoxApp:checked').length);
    });

    function decideApplicants(decision, ids) {
        if (!ids.length) { alert('Please select at least one applicant.'); return; }
        var verb = (decision === 'approve') ? 'approve' : 'reject';
        if (confirm('Are you sure you want to ' + verb + ' ' + ids.length + ' applicant(s)?')) {
            cqPost('cardiac_talks_quiz.process.php', {
                act: decision + '_applicants',
                quiz_id: '<?= $quizId ?>',
                ids: ids,
                return_to: 'applicants',
                f_status: $('#f_status_keep').val(),
                f_search: $('#f_search_keep').val()
            });
        }
    }
    function selectedApplicants() {
        return $('.checkBoxApp:checked').map(function () { return $(this).val(); }).get();
    }
    $(document).on('click', '.approveOneBtn', function () { decideApplicants('approve', [$(this).data('id')]); });
    $(document).on('click', '.rejectOneBtn', function () { decideApplicants('reject', [$(this).data('id')]); });
    $(document).on('click', '#approveSelectedBtn', function () { decideApplicants('approve', selectedApplicants()); });
    $(document).on('click', '#rejectSelectedBtn', function () { decideApplicants('reject', selectedApplicants()); });
</script>
</html>
<?php

/*==================================================================
   APPLICANTS (only for quizzes with join_type = approval)
==================================================================*/
function manageApplicants($cfg, $mycms, $quiz)
{
    $qid       = (int)$quiz['id'];
    $self      = $_SERVER['PHP_SELF'];
    $seatLimit = (int)(isset($quiz['seat_limit']) ? $quiz['seat_limit'] : 0);

    // Default view = pending applications
    $fStatus = isset($_REQUEST['f_status']) ? $_REQUEST['f_status'] : 'Applied';
    if (!in_array($fStatus, ['Applied', 'Approved', 'Rejected', 'All'], true)) {
        $fStatus = 'Applied';
    }
    $fSearch = trim(isset($_REQUEST['f_search']) ? $_REQUEST['f_search'] : '');

    $con    = '';
    $params = [];
    if ($fStatus !== 'All') {
        $con .= " AND p.`status` = ?";
        $params[] = ['FILD' => 'status', 'DATA' => $fStatus, 'TYP' => 's'];
    }
    if ($fSearch !== '') {
        $con .= " AND (u.`user_full_name` LIKE ? OR p.`group_name` LIKE ? OR p.`group_code` LIKE ?)";
        $like = '%' . $fSearch . '%';
        $params[] = ['FILD' => 's1', 'DATA' => $like, 'TYP' => 's'];
        $params[] = ['FILD' => 's2', 'DATA' => $like, 'TYP' => 's'];
        $params[] = ['FILD' => 's3', 'DATA' => $like, 'TYP' => 's'];
    }

    $sql = [];
    $sql['QUERY'] = "SELECT p.*, u.user_full_name
        FROM `rcg_cardiac_talk_quiz_participation` AS p
        LEFT JOIN `rcg_user_registration` AS u ON u.id = p.applied_by_user_id
        WHERE p.`quiz_id` = ? AND p.`row_status` = 'A'
        $con
        ORDER BY p.`created_at` ASC, p.`id` ASC";
    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $qid, 'TYP' => 'i'];
    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $list = $mycms->sql_select_paginated($indexVal, $sql, 25, $restrt);
    $sl   = is_numeric(isset($restrt) ? $restrt : null) ? (int)$restrt : 0;

    $counts   = cq_counts($mycms, $qid);
    $seatsTxt = ($seatLimit > 0) ? max(0, $seatLimit - $counts['Approved']) : 'Unlimited';

    $appClosed = (isset($quiz['application_status']) && $quiz['application_status'] === 'closed')
        || (!empty($quiz['application_close_time']) && strtotime($quiz['application_close_time']) < time());
    ?>
    <div class="cq_stats">
        <div class="cq_stat"><span>Awaiting Approval</span><b><?= $counts['Applied'] ?></b></div>
        <div class="cq_stat"><span>Approved</span><b><?= $counts['Approved'] ?></b></div>
        <div class="cq_stat"><span>Rejected</span><b><?= $counts['Rejected'] ?></b></div>
        <div class="cq_stat"><span>Seats Left</span><b><?= $seatsTxt ?></b></div>
        <div class="cq_stat"><span>Applications</span><b><?= $appClosed ? 'Closed' : 'Open' ?></b></div>
    </div>

    <div class="com_info_box active" id="applicantListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-user-clock"></i></span> Applicant Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <form method="get" action="<?= $self ?>" class="mb-3">
                        <input type="hidden" name="quiz_id" value="<?= $qid ?>" />
                        <div class="form_grid g_4">
                            <div class="frm_grp">
                                <p class="frm-head">Name / Team</p>
                                <input type="text" name="f_search" id="f_search_keep" value="<?= htmlspecialchars($fSearch) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Application Status</p>
                                <select name="f_status" id="f_status_keep">
                                    <option value="Applied" <?= ($fStatus === 'Applied') ? 'selected' : '' ?>>Awaiting approval</option>
                                    <option value="Approved" <?= ($fStatus === 'Approved') ? 'selected' : '' ?>>Approved</option>
                                    <option value="Rejected" <?= ($fStatus === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                                    <option value="All" <?= ($fStatus === 'All') ? 'selected' : '' ?>>All</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">&nbsp;</p>
                                <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                                <a href="<?= $self ?>?quiz_id=<?= $qid ?>" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;text-align:center;"><input id="check_all_app" type="checkbox" /></th>
                                    <th class="sl">Sl. No.</th>
                                    <th>Applicant</th>
                                    <th>Participation</th>
                                    <th>Team Name</th>
                                    <th>Team Code</th>
                                    <th>Applied On</th>
                                    <th>Status</th>
                                    <th class="action text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($list) {
                                    foreach ($list as $row) {
                                        $sl++;
                                        $pid    = (int)$row['id'];
                                        $isTeam = ($row['participation_type'] === 'group');
                                        $name   = !empty($row['user_full_name']) ? $row['user_full_name'] : ('User #' . (int)$row['applied_by_user_id']);
                                        $st     = $row['status'];
                                        $stCls  = ($st === 'Approved') ? 'badge_success' : (($st === 'Rejected') ? 'badge_danger' : 'badge_dark');
                                        $stTxt  = ($st === 'Applied') ? 'Pending' : $st;
                                ?>
                                <tr>
                                    <td style="text-align:center;"><input type="checkbox" class="checkBoxApp" value="<?= $pid ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td style="text-align:center;">
                                        <n class="badge_padding <?= $isTeam ? 'badge_dark' : 'badge_success' ?> w-max-con text-uppercase"><?= $isTeam ? 'Group' : 'Solo' ?></n>
                                    </td>
                                    <td><?= !empty($row['group_name']) ? htmlspecialchars($row['group_name']) : '--' ?></td>
                                    <td style="text-align:center;"><?= !empty($row['group_code']) ? htmlspecialchars($row['group_code']) : '--' ?></td>
                                    <td style="text-align:center;"><?= !empty($row['created_at']) ? date('d-m-Y H:i', strtotime($row['created_at'])) : '--' ?></td>
                                    <td style="text-align:center;"><n class="badge_padding <?= $stCls ?> w-max-con text-uppercase"><?= htmlspecialchars($stTxt) ?></n></td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <?php if ($st !== 'Approved') { ?>
                                                <a href="javascript:void(0);" class="approveOneBtn icon_hover badge_success action-transparent" data-id="<?= $pid ?>"><i class="fal fa-check"></i> Approve</a>
                                                <?php } ?>
                                                <?php if ($st !== 'Rejected') { ?>
                                                <a href="javascript:void(0);" class="rejectOneBtn icon_hover badge_danger action-transparent br-5 w-auto" data-id="<?= $pid ?>"><i class="fal fa-times"></i> Reject</a>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="9" align="center"><span class="mandatory">No Record.</span></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 mb-3" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <button type="button" class="badge_success" id="approveSelectedBtn"><i class="fal fa-check"></i> Approve selected</button>
                        <button type="button" class="badge_danger" id="rejectSelectedBtn"><i class="fal fa-times"></i> Reject selected</button>
                    </div>

                    <div class="bbp-pagination">
                        <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo($indexVal) ?></div>
                        <span class="paginationDisplay">
                            <div class="pagination"><a><?= $mycms->paginate($indexVal, 'pagination') ?></a></div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>