<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

/* ---------- Helpers ---------- */
if (!function_exists('qmv')) {
    function qmv($row, $key, $default = '')
    {
        return (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) ? htmlspecialchars($row[$key]) : $default;
    }
}
if (!function_exists('qmdt')) {
    function qmdt($val)
    {
        return !empty($val) ? date('Y-m-d\TH:i', strtotime($val)) : '';
    }
}
if (!function_exists('qmsel')) {
    function qmsel($row, $key, $val, $default = false)
    {
        if (!isset($row[$key]) || $row[$key] === '') {
            return $default ? 'selected' : '';
        }
        return ($row[$key] == $val) ? 'selected' : '';
    }
}

/* ---------- Request params ---------- */
$qmSelf   = $_SERVER['PHP_SELF'];
$qmEditId = $_REQUEST['edit_quiz'] ?? '';
$qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
$qmStart  = $_REQUEST['start_date'] ?? '';

/* ---------- Halls ---------- */
$sql_hall = [];
$sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall WHERE status = 'A' ORDER BY hall_title ASC";
$qmHalls = $mycms->sql_select($sql_hall);

/* ---------- Edit mode: row load ---------- */
$qmRow = [];
if ($qmEditId !== '') {
    $sqlE = [];
    $sqlE['QUERY']   = "SELECT * FROM `rcg_quizzes` WHERE `id` = ?";
    $sqlE['PARAM'][] = ['FILD' => 'id', 'DATA' => $qmEditId, 'TYP' => 's'];
    $resE  = $mycms->sql_select($sqlE);
    $qmRow = $resE ? $resE[0] : [];
}
$isEdit = !empty($qmRow);

$qmQuizType     = strtolower($qmRow['quiz_type'] ?? 'default');
$qmHideOptional = ($qmQuizType == 'panel discussion') ? 'none' : '';
$qmEnableQr     = strtolower($qmRow['enable_qr'] ?? 'no');   // yes / no (default: no)

/* ---------- Group (process file requires group_id) ----------
 * Edit mode  : quiz er nijer group_id
 * Add mode   : URL / form theke ?group_id=
 */
$qmGroupId = $isEdit
    ? (int)($qmRow['group_id'] ?? 0)
    : (int)($_REQUEST['group_id'] ?? 0);
if ($qmGroupId <= 0) {
    $qmGroupId = (int)($_REQUEST['group_id'] ?? 0);
}
$qmGrpQs = ($qmGroupId > 0) ? '&group_id=' . $qmGroupId : '';

/* ---------- Quiz listing ---------- */
$con    = "";
$params = [];
if (!empty($qmStart)) {
    $con .= " AND DATE(q.`start_time`) >= ?";
    $params[] = ['FILD' => 'start_date', 'DATA' => $qmStart, 'TYP' => 's'];
}

$sql = [];
$sql['QUERY'] = "SELECT q.*, h.hall_title
    FROM `rcg_quizzes` AS q
    LEFT JOIN `rcg_program_schedule_hall` AS h ON q.hall_id = h.id
    WHERE q.`row_status` != ?
    $con
    ORDER BY q.start_time DESC";
$sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];
foreach ($params as $p) { $sql['PARAM'][] = $p; }

$qmList  = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);
$qmCount = $qmList ? count($qmList) : 0;

/* ==================================================================
   POLL DATA
   ================================================================== */
$plEditId = $_REQUEST['edit_poll'] ?? '';

/* ---------- Poll edit mode: row + options ---------- */
$plRow     = [];
$plOptions = [];
if ($plEditId !== '') {
    $sqlP = [];
    $sqlP['QUERY']   = "SELECT * FROM `rcg_polls` WHERE `id` = ? AND `is_deleted` = 0";
    $sqlP['PARAM'][] = ['FILD' => 'id', 'DATA' => (int)$plEditId, 'TYP' => 'i'];
    $resP  = $mycms->sql_select($sqlP);
    $plRow = $resP ? $resP[0] : [];

    if (!empty($plRow)) {
        $sqlO = [];
        $sqlO['QUERY']   = "SELECT id, option_text FROM `rcg_poll_options` WHERE `poll_id` = ? ORDER BY id ASC";
        $sqlO['PARAM'][] = ['FILD' => 'poll_id', 'DATA' => (int)$plEditId, 'TYP' => 'i'];
        $plOptions = $mycms->sql_select($sqlO) ?: [];
    }
}
$plIsEdit = !empty($plRow);
if (!$plIsEdit || empty($plOptions)) {
    $plOptions = [['id' => '', 'option_text' => ''], ['id' => '', 'option_text' => '']];
}
$plAllowMultiple = (int)($plRow['allow_multiple'] ?? 0);
$plMaxChoices    = max(1, (int)($plRow['max_choices'] ?? 1));
$plStartVal = $plIsEdit ? qmdt($plRow['start_time'] ?? '') : date('Y-m-d\TH:i');
$plEndVal   = $plIsEdit ? qmdt($plRow['end_time'] ?? '') : '';

/* ---------- Poll listing (with total votes) ---------- */
$sqlPL = [];
$sqlPL['QUERY'] = "SELECT p.*, COALESCE(SUM(o.vote_count), 0) AS total_votes
    FROM `rcg_polls` AS p
    LEFT JOIN `rcg_poll_options` AS o ON o.poll_id = p.id
    WHERE p.`is_deleted` = 0
    GROUP BY p.id
    ORDER BY p.id DESC";
$plList  = $mycms->sql_select($sqlPL);
$plCount = $plList ? count($plList) : 0;

$plOptMap = [];
if ($plList) {
    $plIds = implode(',', array_map('intval', array_column($plList, 'id')));
    $sqlOL = [];
    $sqlOL['QUERY'] = "SELECT id, poll_id, option_text, vote_count FROM `rcg_poll_options` WHERE poll_id IN ($plIds) ORDER BY id ASC";
    $resOL = $mycms->sql_select($sqlOL);
    if ($resOL) {
        foreach ($resOL as $o) { $plOptMap[$o['poll_id']][] = $o; }
    }
}
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Quiz &amp; Polls</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Conference App</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Quiz &amp; Polls</a></li>
                    </ol>
                </nav>
                <h2>Quiz &amp; Polls</h2>
                <h6>Manage gamified quizzes, halls, rounds, participation settings and live audience polls.</h6>
            </div>
        </div>

        <div class="app_tab_content" id="quizpolls" style="display:block;">

            <div class="committee_filter">
                <ul class="quiz_tab">
                    <button data-tab="quizmaster" class="active">Gamified Quiz Master</button>
                    <button data-tab="quizlivepoll">Live Audience Poll</button>
                </ul>
            </div>

            <!-- ==================================================================
                 TAB 1 : GAMIFIED QUIZ MASTER
                 ================================================================== -->
            <div id="quizmaster" class="quizpollcontent" style="display:block;">
                <div class="basic_grid">

                    <!-- ============ LEFT : ADD / EDIT QUIZ ============ -->
                    <div class="basic_grid_left">
                        <form name="frmquiz" method="post" action="manage_quiz.process.php" id="frmquiz" onsubmit="return onSubmitAction();">
                            <input type="hidden" name="act" value="<?= $isEdit ? 'edit_quiz' : 'add_quiz' ?>" />
                            <?php if ($isEdit) { ?>
                                <input type="hidden" name="quiz_id" id="quiz_id" value="<?= htmlspecialchars($qmEditId) ?>" />
                            <?php } ?>
                            <input type="hidden" name="group_id" id="group_id" value="<?= (int)$qmGroupId ?>" />
                            <input type="hidden" name="return_to" value="manage_quiz_polls.php" />
                            <input type="hidden" name="_pgn_" value="<?= $qmPg ?>" />
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($qmStart) ?>" />

                            <div class="basic_grid_box">
                                <div class="venue_hall_top push_noti_top">
                                    <h5>
                                        <n><i class="fal fa-question-circle"></i><?= $isEdit ? 'Edit Quiz' : 'Create New Quiz' ?></n>
                                        <l>Manage quizzes, halls, rounds and participation settings.</l>
                                    </h5>
                                    <div class="page_top_wrap_right">
                                        <?php if ($isEdit) { ?>
                                            <a href="<?= $qmSelf ?>?_pgn_=<?= $qmPg ?><?= $qmGrpQs ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                                        <?php } ?>
                                        <button type="submit" id="Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                                    </div>
                                </div>

                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Quiz Type <span class="mandatory">*</span></p>
                                        <select name="quiz_type" id="quiz_type" required>
                                            <option value="default" <?= ($qmQuizType == 'default') ? 'selected' : '' ?>>Default</option>
                                            <option value="panel discussion" <?= ($qmQuizType == 'panel discussion') ? 'selected' : '' ?>>Panel Discussion</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Quiz Title <span class="mandatory">*</span></p>
                                        <input type="text" name="title" id="title" class="validate[required]" value="<?= qmv($qmRow, 'title') ?>" required />
                                    </div>

                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Hall <span class="mandatory">*</span></p>
                                        <select name="hall" id="hall" class="validate[required]" required>
                                            <option value=""> - Select - </option>
                                            <?php if ($qmHalls) { foreach ($qmHalls as $hall) { ?>
                                                <option value="<?= $hall['id'] ?>" <?= (($qmRow['hall_id'] ?? '') == $hall['id']) ? 'selected' : '' ?>><?= htmlspecialchars($hall['hall_title']) ?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2 optionalData" style="display: <?= $qmHideOptional ?>;">
                                        <p class="frm-head">Rounds <span class="mandatory">*</span></p>
                                        <input type="number" name="rounds" id="rounds" class="validate[required]" value="<?= (($qmRow['rounds'] ?? 0) > 0) ? (int)$qmRow['rounds'] : 1 ?>" required />
                                    </div>
                                    <div class="frm_grp span_2 optionalData" style="display: <?= $qmHideOptional ?>;">
                                        <p class="frm-head">Levels <span class="mandatory">*</span></p>
                                        <input type="number" name="levels" id="levels" class="validate[required]" value="<?= (($qmRow['levels'] ?? 0) > 0) ? (int)$qmRow['levels'] : 1 ?>" required />
                                    </div>

                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                        <input type="datetime-local" name="start_time" id="start_time" value="<?= qmdt($qmRow['start_time'] ?? '') ?>" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Answer Time (in sec.) <span class="mandatory">*</span></p>
                                        <input type="number" name="answer_time" id="answer_time" value="<?= qmv($qmRow, 'answer_time') ?>" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Buffer Time (in sec.) <span class="mandatory">*</span></p>
                                        <input type="number" name="buffer_time" id="buffer_time" value="<?= $isEdit ? ((($qmRow['buffer_time'] ?? 0) > 0) ? (int)$qmRow['buffer_time'] : 1) : 0 ?>" required />
                                    </div>

                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Marks for Correct Answer</p>
                                        <input type="number" name="marks_correct" id="marks_correct" value="<?= (($qmRow['marks_correct'] ?? 0) > 0) ? $qmRow['marks_correct'] : 0 ?>" step="0.1" />
                                    </div>
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Marks for Incorrect Answer</p>
                                        <input type="number" name="marks_incorrect" id="marks_incorrect" value="<?= (($qmRow['marks_incorrect'] ?? 0) > 0) ? $qmRow['marks_incorrect'] : 0 ?>" step="0.1" />
                                    </div>

                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Quiz Status <span class="mandatory">*</span></p>
                                        <select name="quiz_status" id="quiz_status" required>
                                            <option value="draft" <?= qmsel($qmRow, 'status', 'draft', true) ?>>Draft</option>
                                            <option value="active" <?= qmsel($qmRow, 'status', 'active') ?>>Active</option>
                                            <option value="completed" <?= qmsel($qmRow, 'status', 'completed') ?>>Completed</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">View Leader Board <span class="mandatory">*</span></p>
                                        <select name="view_answer" id="view_answer" required>
                                            <option value="end" <?= qmsel($qmRow, 'view_answer', 'end', true) ?>>At The End of The Quiz</option>
                                            <option value="question" <?= qmsel($qmRow, 'view_answer', 'question') ?>>After Each Question</option>
                                            <option value="level" <?= qmsel($qmRow, 'view_answer', 'level') ?>>After Each Level</option>
                                            <option value="round" <?= qmsel($qmRow, 'view_answer', 'round') ?>>After Each Round</option>
                                        </select>
                                    </div>

                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Seat Limit <span class="mandatory">*</span></p>
                                        <input type="number" name="seat_limit" id="seat_limit" value="<?= qmv($qmRow, 'seat_limit', '0') ?>" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Application Closing Time <span class="mandatory">*</span></p>
                                        <input type="datetime-local" name="application_close_time" id="application_close_time" value="<?= qmdt($qmRow['application_close_time'] ?? '') ?>" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Application Status <span class="mandatory">*</span></p>
                                        <select name="application_status" id="application_status" required>
                                            <option value="active" <?= qmsel($qmRow, 'application_status', 'active', true) ?>>Active</option>
                                            <option value="closed" <?= qmsel($qmRow, 'application_status', 'closed') ?>>Closed</option>
                                        </select>
                                    </div>

                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Participation Type <span class="mandatory">*</span></p>
                                        <select name="participation_type" id="participation_type" required>
                                            <option value="solo" <?= qmsel($qmRow, 'participation_type', 'solo', true) ?>>Solo</option>
                                            <option value="group" <?= qmsel($qmRow, 'participation_type', 'group') ?>>Group</option>
                                            <option value="both" <?= qmsel($qmRow, 'participation_type', 'both') ?>>Both</option>
                                        </select>
                                    </div>

                                    <!-- ===== Joining Instructions ===== -->
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Show QR Code (Joining Instructions) <span class="mandatory">*</span></p>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="enable_qr" value="yes" <?= ($qmEnableQr == 'yes') ? 'checked' : '' ?> />
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="enable_qr" value="no" <?= ($qmEnableQr != 'yes') ? 'checked' : '' ?> />
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- ============ RIGHT : QUIZ LISTING ============ -->
                    <div class="basic_grid_right">
                        <div class="push_noti_right_botttom">
                            <h5 class="real_time_portal_heading">
                                <n>Quiz Listing (<?= $qmCount ?>)</n>
                                <a href="<?= $qmSelf ?><?= $qmGroupId > 0 ? '?group_id=' . $qmGroupId : '' ?>" class="badge_success"><i class="fal fa-plus"></i> Create New Quiz</a>
                            </h5>

                            <div class="push_noti_right_botttom_body quizques_wrap">
                                <?php if ($qmList) { ?>
                                    <div class="push_noti_right_botttom_body_box">
                                        <label class="cus_check gender_check">
                                            <input name="check_all" id="check_all" type="checkbox" />
                                            <span class="checkmark">Select All</span>
                                        </label>
                                    </div>
                                    <?php foreach ($qmList as $row) { ?>
                                    <div class="push_noti_right_botttom_body_box">
                                        <p>
                                            <n>
                                                <input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= $row['id'] ?>" />
                                                <?= htmlspecialchars($row['title']) ?>
                                            </n>
                                            <a href="manage_quiz.process.php?act=<?= ($row['row_status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $row['id'] ?>&_pgn_=<?= $qmPg ?>&return_to=manage_quiz_polls.php"
                                               class="badge_padding <?= ($row['row_status'] == 'A') ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                                <?= ($row['row_status'] == 'A') ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </p>
                                        <ul class="quizques">
                                            <li>Hall: <?= htmlspecialchars($row['hall_title'] ?? '') ?></li>
                                            <li>Start Time: <?= !empty($row['start_time']) ? date('d-m-Y H:i:s', strtotime($row['start_time'])) : '--' ?></li>
                                            <li>Quiz Status: <?= ucfirst($row['status']) ?></li>
                                            <li>Participents: <a href="participations.php?quiz_id=<?= $row['id'] ?>" target="_blank">View</a></li>
                                        </ul>
                                        <h6>
                                            <?php if ($row['status'] !== 'completed') { ?>
                                                <a href="new_quiz_updated.php?quiz_id=<?= $row['id'] ?>" class="badge_secondary"><i class="fal fa-eye"></i> View</a>
                                                <?php if ($row['status'] === 'active') { ?>
                                                    <a href="manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=notify_all&return_to=manage_quiz_polls.php" class="badge_secondary" onclick="return confirm('Send notification to all users?');"><i class="fal fa-bell"></i> Notify All</a>
                                                <?php } ?>
                                            <?php } ?>
                                            <a href="<?= $qmSelf ?>?edit_quiz=<?= $row['id'] ?>&_pgn_=<?= $qmPg ?>" class="badge_secondary"><i class="fal fa-edit"></i> Edit Details</a>
                                            <a href="manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=reset_quiz&return_to=manage_quiz_polls.php" class="badge_secondary" onclick="return confirm('Reset this quiz?');"><i class="fal fa-redo"></i> Reset Quiz</a>
                                            <a href="participants.php?quiz_id=<?= $row['id'] ?>" class="badge_secondary"><i class="fal fa-users"></i> Participants</a>
                                            <a href="javascript:void(0);" class="deleteQuizBtn badge_danger" data-href="manage_quiz.process.php?act=Remove&id=<?= $row['id'] ?>&_pgn_=<?= $qmPg ?>&return_to=manage_quiz_polls.php"><i class="fal fa-trash-alt"></i> Remove</a>
                                        </h6>
                                    </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="push_noti_right_botttom_body_box">
                                        <span class="mandatory">No Record.</span>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Multi action -->
                            <div class="page_top_wrap mt-3">
                                <div class="page_top_wrap_left">
                                    <select name="multiOperationSelector" id="multiOperationSelector">
                                        <option value="">Choose an action..</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Remove">Remove</option>
                                    </select>
                                    <button type="button" id="applyMultiActionBtn" class="badge_dark">Apply to selected</button>
                                </div>
                            </div>

                            <div class="paginationDisplay">
                                <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                                <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================================================================
                 TAB 2 : LIVE AUDIENCE POLL  (full add / edit / delete here)
                 ================================================================== -->
            <div id="quizlivepoll" class="quizpollcontent">
                <div class="basic_grid">

                    <!-- ============ LEFT : ADD / EDIT POLL ============ -->
                    <div class="basic_grid_left">
                        <form name="frmpoll" method="post" action="manage_poll.process.php" id="frmpoll" onsubmit="return onSubmitPoll();">
                            <input type="hidden" name="act" value="<?= $plIsEdit ? 'edit_poll' : 'add_poll' ?>" />
                            <?php if ($plIsEdit) { ?>
                                <input type="hidden" name="poll_id" id="poll_id" value="<?= (int)$plRow['id'] ?>" />
                            <?php } ?>
                            <input type="hidden" name="_pgn_" value="<?= $qmPg ?>" />

                            <div class="basic_grid_box">
                                <div class="venue_hall_top push_noti_top">
                                    <h5>
                                        <n><i class="fal fa-poll"></i><?= $plIsEdit ? 'Edit Poll' : 'Create New Poll' ?></n>
                                        <l>Launch real-time polls during keynotes or panel discussions.</l>
                                    </h5>
                                    <div class="page_top_wrap_right">
                                        <?php if ($plIsEdit) { ?>
                                            <a href="<?= $qmSelf ?>?tab=poll&_pgn_=<?= $qmPg ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                                        <?php } ?>
                                        <button type="submit" id="SavePoll" name="SavePoll" class="badge_success"><i class="fal fa-save"></i> Save</button>
                                    </div>
                                </div>

                                <div class="form_grid g_6">
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Poll Question <span class="mandatory">*</span></p>
                                        <input type="text" name="title" id="poll_title" value="<?= qmv($plRow, 'title') ?>" required />
                                    </div>

                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Description</p>
                                        <textarea name="description" id="poll_description" rows="2"><?= qmv($plRow, 'description') ?></textarea>
                                    </div>

                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                        <input type="datetime-local" name="start_time" id="poll_start_time" value="<?= $plStartVal ?>" required />
                                    </div>
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">End Time</p>
                                        <input type="datetime-local" name="end_time" id="poll_end_time" value="<?= $plEndVal ?>" />
                                    </div>

                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Allow Multiple Choices <span class="mandatory">*</span></p>
                                        <select name="allow_multiple" id="allow_multiple" required>
                                            <option value="0" <?= $plAllowMultiple == 0 ? 'selected' : '' ?>>No (Single choice)</option>
                                            <option value="1" <?= $plAllowMultiple == 1 ? 'selected' : '' ?>>Yes (Multiple choices)</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2" id="maxChoicesWrap" style="display: <?= $plAllowMultiple ? '' : 'none' ?>;">
                                        <p class="frm-head">Max Choices <span class="mandatory">*</span></p>
                                        <input type="number" name="max_choices" id="max_choices" min="1" value="<?= $plMaxChoices ?>" />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Publish Status <span class="mandatory">*</span></p>
                                        <select name="is_published" id="is_published" required>
                                            <option value="0" <?= ($plIsEdit && (int)$plRow['is_published'] === 0) ? 'selected' : '' ?>>Unpublished</option>
                                            <option value="1" <?= (!$plIsEdit || (int)$plRow['is_published'] === 1) ? 'selected' : '' ?>>Published</option>
                                        </select>
                                    </div>

                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Options <span class="mandatory">*</span> <small>(minimum 2)</small></p>
                                        <ul class="quizliveques" id="pollOptionList">
                                            <?php foreach ($plOptions as $opt) { ?>
                                                <li class="p-0 border-0 bg-transparent pollOptRow">
                                                    <span class="pollOptNo"></span>
                                                    <div class="frm_grp">
                                                        <input type="text" name="options[]" value="<?= htmlspecialchars($opt['option_text'] ?? '') ?>" />
                                                        <input type="hidden" name="option_ids[]" value="<?= htmlspecialchars((string)($opt['id'] ?? '')) ?>" />
                                                    </div>
                                                    <a href="javascript:void(0);" class="badge_danger removeOptBtn"><i class="fal fa-trash-alt"></i></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <a href="javascript:void(0);" id="addOptBtn" class="badge_secondary mt-2"><i class="fal fa-plus"></i> Add Option</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ============ RIGHT : POLL LISTING ============ -->
                    <div class="basic_grid_right">
                        <div class="push_noti_right_botttom">
                            <h5 class="real_time_portal_heading">
                                <n>Poll Listing (<?= $plCount ?>)</n>
                                <a href="<?= $qmSelf ?>?tab=poll" class="badge_success"><i class="fal fa-plus"></i> Create New Poll</a>
                            </h5>

                            <div class="push_noti_right_botttom_body quizques_wrap">
                                <?php if ($plList) { ?>
                                    <div class="push_noti_right_botttom_body_box">
                                        <label class="cus_check gender_check">
                                            <input name="check_all_poll" id="check_all_poll" type="checkbox" />
                                            <span class="checkmark">Select All</span>
                                        </label>
                                    </div>
                                    <?php foreach ($plList as $prow) {
                                        $pid   = (int)$prow['id'];
                                        $total = (int)$prow['total_votes'];
                                        $now   = time();
                                        $stTs  = !empty($prow['start_time']) ? strtotime($prow['start_time']) : 0;
                                        $enTs  = !empty($prow['end_time']) ? strtotime($prow['end_time']) : 0;
                                        if ($stTs && $now < $stTs)     { $phase = 'Upcoming'; }
                                        elseif ($enTs && $now > $enTs) { $phase = 'Ended'; }
                                        else                           { $phase = 'Live'; }
                                    ?>
                                    <div class="push_noti_right_botttom_body_box">
                                        <p>
                                            <n>
                                                <input type="checkbox" name="poll_selected_ids[]" class="checkBoxPoll" value="<?= $pid ?>" />
                                                <?= htmlspecialchars($prow['title']) ?>
                                            </n>
                                            <a href="manage_poll.process.php?act=<?= $prow['is_active'] ? 'Inactive' : 'Active' ?>&id=<?= $pid ?>&_pgn_=<?= $qmPg ?>"
                                               class="badge_padding <?= $prow['is_active'] ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                                <?= $prow['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </p>
                                        <ul class="quizques">
                                            <li>Start: <?= $stTs ? date('d-m-Y H:i', $stTs) : '--' ?></li>
                                            <li>End: <?= $enTs ? date('d-m-Y H:i', $enTs) : '--' ?></li>
                                            <li>Status: <?= $phase ?> &middot; <?= $prow['allow_multiple'] ? 'Multiple (max ' . (int)$prow['max_choices'] . ')' : 'Single choice' ?></li>
                                            <li>
                                                Visibility:
                                                <a href="manage_poll.process.php?act=<?= $prow['is_published'] ? 'Unpublish' : 'Publish' ?>&id=<?= $pid ?>&_pgn_=<?= $qmPg ?>"
                                                   class="badge_padding <?= $prow['is_published'] ? 'badge_success' : 'badge_dark' ?> w-max-con">
                                                    <?= $prow['is_published'] ? 'Published' : 'Unpublished' ?>
                                                </a>
                                            </li>
                                        </ul>

                                        <ul class="quizques quizliveprew">
                                            <?php foreach (($plOptMap[$pid] ?? []) as $o) {
                                                $pct = $total > 0 ? round(($o['vote_count'] / $total) * 100) : 0; ?>
                                                <li class="p-0 border-0 bg-transparent">
                                                    <div class="progress-bar-wrap">
                                                        <div class="progress-value"><?= htmlspecialchars($o['option_text']) ?><l>
                                                                <n><?= $pct ?>%</n> (<?= (int)$o['vote_count'] ?>)
                                                            </l>
                                                        </div>
                                                        <div class="progress">
                                                            <div class="progress-done bg_secondary" data-progress="<?= $pct ?>" style="width: <?= $pct ?>%; opacity: 1;"></div>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <li class="p-0 border-0 bg-transparent"><small>Total votes: <?= $total ?></small></li>
                                        </ul>

                                        <h6>
                                            <a href="<?= $qmSelf ?>?edit_poll=<?= $pid ?>&tab=poll&_pgn_=<?= $qmPg ?>" class="badge_secondary"><i class="fal fa-edit"></i> Edit Details</a>
                                            <a href="manage_poll.process.php?poll_id=<?= $pid ?>&act=reset_poll&_pgn_=<?= $qmPg ?>" class="badge_secondary" onclick="return confirm('Reset all votes of this poll?');"><i class="fal fa-redo"></i> Reset Votes</a>
                                            <a href="javascript:void(0);" class="deletePollBtn badge_danger" data-href="manage_poll.process.php?act=Remove&id=<?= $pid ?>&_pgn_=<?= $qmPg ?>"><i class="fal fa-trash-alt"></i> Remove</a>
                                        </h6>
                                    </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="push_noti_right_botttom_body_box">
                                        <span class="mandatory">No Record.</span>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Multi action -->
                            <div class="page_top_wrap mt-3">
                                <div class="page_top_wrap_left">
                                    <select name="pollMultiOperationSelector" id="pollMultiOperationSelector">
                                        <option value="">Choose an action..</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Publish">Publish</option>
                                        <option value="Unpublish">Unpublish</option>
                                        <option value="Remove">Remove</option>
                                    </select>
                                    <button type="button" id="applyPollMultiActionBtn" class="badge_dark">Apply to selected</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    // ---------- Quiz / Poll inner tab switch ----------
    $('.quiz_tab button').click(function () {
        var tabId = $(this).attr('data-tab');
        $('.quizpollcontent').hide();
        $('.quiz_tab button').removeClass('active');
        $('#' + tabId).show();
        $(this).addClass('active');
    });

    // ---------- Panel Discussion hide Rounds / Levels ----------
    $('#quiz_type').on('change', function () {
        if ($(this).val() === 'panel discussion') {
            $('#quizmaster .optionalData').hide();
        } else {
            $('#quizmaster .optionalData').show();
        }
    });

    // ---------- Select / Deselect all (quiz) ----------
    $('#check_all').on('click', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });

    // ---------- Remove confirm (quiz) ----------
    $(document).on('click', '.deleteQuizBtn', function () {
        if (confirm('Do you really want to remove this record?')) {
            window.location.href = $(this).data('href');
        }
    });

    // ---------- Multi action apply (quiz) ----------
    $('#applyMultiActionBtn').on('click', function () {
        var action = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one quiz.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected quizzes?')) {
            $.ajax({
                url: 'manage_quiz.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                success: function () { location.reload(); }
            });
        }
    });

    // ---------- Open Poll tab after save / edit / delete (?tab=poll) ----------
    if (window.location.search.indexOf('tab=poll') > -1) {
        $('.quiz_tab button[data-tab="quizlivepoll"]').click();
    }

    /* ====================================================================
       LIVE AUDIENCE POLL
       ==================================================================== */

    // ---------- Option rows: numbering / add / remove ----------
    function renumberPollOptions() {
        $('#pollOptionList .pollOptRow').each(function (i) {
            var n = i + 1;
            $(this).find('.pollOptNo').text((n < 10 ? '0' : '') + n + '.');
        });
    }
    renumberPollOptions();

    $('#addOptBtn').on('click', function () {
        var row =
            '<li class="p-0 border-0 bg-transparent pollOptRow">' +
                '<span class="pollOptNo"></span>' +
                '<div class="frm_grp">' +
                    '<input type="text" name="options[]" value="" />' +
                    '<input type="hidden" name="option_ids[]" value="" />' +
                '</div>' +
                '<a href="javascript:void(0);" class="badge_danger removeOptBtn"><i class="fal fa-trash-alt"></i></a>' +
            '</li>';
        $('#pollOptionList').append(row);
        renumberPollOptions();
    });

    $(document).on('click', '.removeOptBtn', function () {
        if ($('#pollOptionList .pollOptRow').length <= 2) {
            alert('A poll needs at least 2 options.');
            return;
        }
        $(this).closest('.pollOptRow').remove();
        renumberPollOptions();
    });

    // ---------- Max Choices only for multiple-choice polls ----------
    $('#allow_multiple').on('change', function () {
        if ($(this).val() === '1') {
            $('#maxChoicesWrap').show();
        } else {
            $('#maxChoicesWrap').hide();
            $('#max_choices').val(1);
        }
    });

    // ---------- Poll form validation ----------
    function onSubmitPoll() {
        if ($.trim($('#poll_title').val()) === '') {
            alert('Please enter the poll question.');
            return false;
        }

        var filled = 0;
        $('#pollOptionList input[name="options[]"]').each(function () {
            if ($.trim($(this).val()) !== '') filled++;
        });
        if (filled < 2) {
            alert('Please enter at least 2 options.');
            return false;
        }

        var st = $('#poll_start_time').val();
        var en = $('#poll_end_time').val();
        if (st && en && new Date(en) <= new Date(st)) {
            alert('End time must be after start time.');
            return false;
        }

        if ($('#allow_multiple').val() === '1') {
            var mc = parseInt($('#max_choices').val(), 10);
            if (isNaN(mc) || mc < 1) {
                alert('Max choices must be at least 1.');
                return false;
            }
            if (mc > filled) {
                alert('Max choices cannot be more than the number of options.');
                return false;
            }
        }
        return true;
    }

    // ---------- Select / Deselect all (poll) ----------
    $('#check_all_poll').on('click', function () {
        $('.checkBoxPoll').prop('checked', this.checked);
    });

    // ---------- Remove confirm (poll) ----------
    $(document).on('click', '.deletePollBtn', function () {
        if (confirm('Do you really want to remove this poll?')) {
            window.location.href = $(this).data('href');
        }
    });

    // ---------- Multi action apply (poll) ----------
    $('#applyPollMultiActionBtn').on('click', function () {
        var action = $('#pollMultiOperationSelector').val();
        var selected = $('.checkBoxPoll:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one poll.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected polls?')) {
            $.ajax({
                url: 'manage_poll.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = '<?= $qmSelf ?>?tab=poll'; }
            });
        }
    });
</script>

</html>