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
            cq_header($quiz, 'participants');
            manageParticipants($cfg, $mycms, $quiz);
        }
        ?>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<?php cq_scripts(); ?>
<script>
    $(document).on('click', '#check_all_part', function () {
        $('.checkBoxPart').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxPart', function () {
        $('#check_all_part').prop('checked', $('.checkBoxPart').length === $('.checkBoxPart:checked').length);
    });

    function removeParticipants(ids) {
        if (!ids.length) { alert('Please select at least one participant.'); return; }
        if (confirm('Remove ' + ids.length + ' participant(s) from this quiz?')) {
            cqPost('cardiac_talks_quiz.process.php', {
                act: 'remove_participants',
                quiz_id: '<?= $quizId ?>',
                ids: ids,
                return_to: 'participants',
                f_search: $('#f_search_keep').val()
            });
        }
    }
    $(document).on('click', '.removeOneBtn', function () { removeParticipants([$(this).data('id')]); });
    $(document).on('click', '#removeSelectedBtn', function () {
        removeParticipants($('.checkBoxPart:checked').map(function () { return $(this).val(); }).get());
    });
</script>
</html>
<?php

/*==================================================================
   PARTICIPANTS
   - Approval quiz : only applications with status = Approved
   - Open quiz     : every application that is not Rejected
==================================================================*/
function manageParticipants($cfg, $mycms, $quiz)
{
    $qid        = (int)$quiz['id'];
    $self       = $_SERVER['PHP_SELF'];
    $isApproval = (isset($quiz['join_type']) && $quiz['join_type'] === 'approval');
    $seatLimit  = (int)(isset($quiz['seat_limit']) ? $quiz['seat_limit'] : 0);

    $fSearch = trim(isset($_REQUEST['f_search']) ? $_REQUEST['f_search'] : '');

    $con    = $isApproval ? " AND p.`status` = 'Approved'" : " AND p.`status` != 'Rejected'";
    $params = [];
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

    $counts       = cq_counts($mycms, $qid);
    $totalPart    = $isApproval ? $counts['Approved'] : ($counts['Applied'] + $counts['Approved']);
    $seatsLeftTxt = ($seatLimit > 0) ? max(0, $seatLimit - $totalPart) : 'Unlimited';
    $colCount     = $isApproval ? 9 : 8;
    ?>
    <div class="cq_stats">
        <div class="cq_stat"><span>Participants</span><b><?= $totalPart ?></b></div>
        <div class="cq_stat"><span>Seat Limit</span><b><?= ($seatLimit > 0) ? $seatLimit : 'No limit' ?></b></div>
        <div class="cq_stat"><span>Seats Left</span><b><?= $seatsLeftTxt ?></b></div>
        <?php if ($isApproval) { ?>
        <div class="cq_stat"><span>Awaiting Approval</span><b><?= $counts['Applied'] ?></b></div>
        <?php } ?>
    </div>

    <div class="com_info_box active" id="participantListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-users"></i></span> Participant Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <form method="get" action="<?= $self ?>" class="mb-3">
                        <input type="hidden" name="quiz_id" value="<?= $qid ?>" />
                        <div class="form_grid g_4">
                            <div class="frm_grp">
                                <p class="frm-head">Name</p>
                                <input type="text" name="f_search" id="f_search_keep" value="<?= htmlspecialchars($fSearch) ?>" />
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
                                    <th style="width:36px;text-align:center;"><input id="check_all_part" type="checkbox" /></th>
                                    <th class="sl">Sl. No.</th>
                                    <th>Participant</th>
                                    <th>Participation</th>
                                    <th>Team Name</th>
                                    <th>Team Code</th>
                                    <th><?= $isApproval ? 'Approved On' : 'Joined On' ?></th>
                                    <?php if ($isApproval) { ?><th>Applied On</th><?php } ?>
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
                                        $mainDt = $isApproval ? $row['approved_at'] : $row['created_at'];
                                ?>
                                <tr>
                                    <td style="text-align:center;"><input type="checkbox" class="checkBoxPart" value="<?= $pid ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td style="text-align:center;">
                                        <n class="badge_padding <?= $isTeam ? 'badge_dark' : 'badge_success' ?> w-max-con text-uppercase"><?= $isTeam ? 'Group' : 'Solo' ?></n>
                                    </td>
                                    <td><?= !empty($row['group_name']) ? htmlspecialchars($row['group_name']) : '--' ?></td>
                                    <td style="text-align:center;"><?= !empty($row['group_code']) ? htmlspecialchars($row['group_code']) : '--' ?></td>
                                    <td style="text-align:center;"><?= !empty($mainDt) ? date('d-m-Y H:i', strtotime($mainDt)) : '--' ?></td>
                                    <?php if ($isApproval) { ?>
                                    <td style="text-align:center;"><?= !empty($row['created_at']) ? date('d-m-Y H:i', strtotime($row['created_at'])) : '--' ?></td>
                                    <?php } ?>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="javascript:void(0);" class="removeOneBtn icon_hover badge_danger action-transparent br-5 w-auto" data-id="<?= $pid ?>"><i class="fal fa-user-times"></i> Remove Participant</a>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="<?= $colCount ?>" align="center"><span class="mandatory">No Record.</span></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 mb-3" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <button type="button" class="badge_danger" id="removeSelectedBtn"><i class="fal fa-user-times"></i> Remove selected</button>
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