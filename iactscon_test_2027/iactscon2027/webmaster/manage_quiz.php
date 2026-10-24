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
$qmSelf    = $_SERVER['PHP_SELF'];
$qmPg      = (int)($_REQUEST['_pgn_'] ?? 0);
$qmStart   = $_REQUEST['start_date'] ?? '';

$searchString = "&_pgn_=" . $qmPg . "&start_date=" . urlencode($qmStart);
// return_to follows the actual file name of this page, so it works whatever you save it as
$qmPageFile   = basename($qmSelf);
$qmRet        = "&return_to=" . $qmPageFile;

// After Create / Edit quiz save, always go back to this page
$qmFormReturnPage = 'manage_quiz.php';

/* ---------- Mode: list (default) / add / edit  (form opens on this same page) ---------- */
$qmShow     = $_REQUEST['show'] ?? '';
$qmFormMode = in_array($qmShow, ['add', 'edit'], true);
$qmEditId   = ($qmShow === 'edit') ? ($_REQUEST['quiz_id'] ?? '') : '';

/* ---------- Form data (only when add / edit is open) ---------- */
$qmRow  = [];
$qmHalls = [];
if ($qmFormMode) {
    $sql_hall = [];
    $sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall WHERE status = 'A' ORDER BY hall_title ASC";
    $qmHalls = $mycms->sql_select($sql_hall);

    if ($qmEditId !== '') {
        $sqlE = [];
        $sqlE['QUERY']   = "SELECT * FROM `rcg_quizzes` WHERE `id` = ?";
        $sqlE['PARAM'][] = ['FILD' => 'id', 'DATA' => $qmEditId, 'TYP' => 's'];
        $resE  = $mycms->sql_select($sqlE);
        $qmRow = $resE ? $resE[0] : [];
    }
}
$isEdit = !empty($qmRow);

$qmQuizType     = strtolower($qmRow['quiz_type'] ?? 'default');
$qmHideOptional = ($qmQuizType == 'panel discussion') ? 'none' : '';
$qmEnableQr     = strtolower($qmRow['enable_qr'] ?? 'no');   // yes / no (default: no)

/* ---------- Quiz listing (only when list is shown) ---------- */
$qmList = [];
if (!$qmFormMode) {
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

    $qmList = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);
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
                <h6>Manage gamified quizzes, halls, rounds and participation settings.</h6>
            </div>
        </div>

        <div class="app_tab_content" id="quizpolls" style="display:block;">

            <div class="committee_filter">
                <ul class="quiz_tab">
                    <button data-tab="quizmaster" class="active">Gamified Quiz Master</button>
                </ul>
            </div>

            <!-- ==================================================================
                 GAMIFIED QUIZ MASTER  (list table  OR  add / edit form)
                 ================================================================== -->
            <div id="quizmaster" class="quizpollcontent" style="display:block;">

<?php if ($qmFormMode) { ?>

                <!-- ============ ADD / EDIT QUIZ FORM (centered) ============ -->
                <div class="basic_grid" style="display:flex; justify-content:center;">
                    <div class="basic_grid_left" style="flex:0 1 900px; width:100%; max-width:900px; margin:0 auto;">
                        <form name="frmquiz" method="post" action="manage_quiz.process.php" id="frmquiz" onsubmit="return onSubmitAction();">
                            <input type="hidden" name="act" value="<?= $isEdit ? 'edit_quiz' : 'add_quiz' ?>" />
                            <?php if ($isEdit) { ?>
                                <input type="hidden" name="quiz_id" id="quiz_id" value="<?= htmlspecialchars($qmEditId) ?>" />
                            <?php } ?>
                            <input type="hidden" name="_pgn_" value="<?= $qmPg ?>" />
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($qmStart) ?>" />
                            <!-- after save, redirect to manage_quiz.php -->
                            <input type="hidden" name="return_to" value="<?= htmlspecialchars($qmFormReturnPage) ?>" />

                            <div class="basic_grid_box">
                                <div class="venue_hall_top push_noti_top">
                                    <h5>
                                        <n><i class="fal fa-question-circle"></i><?= $isEdit ? 'Edit Quiz' : 'Create New Quiz' ?></n>
                                        <l>Manage quizzes, halls, rounds and participation settings.</l>
                                    </h5>
                                    <div class="page_top_wrap_right">
                                        <a href="<?= $qmSelf ?>?_pgn_=<?= $qmPg ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
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
                </div>

<?php } else { ?>

                <!-- Table alignment: header and body cells use the same alignment per column -->
                <style>
                    #quizmaster .table_wrap table th,
                    #quizmaster .table_wrap table td {
                        vertical-align: middle;
                    }
                    /* 1 = checkbox, 2 = Sl No, 6 = Quiz Status, 7 = Applicants, 8 = Status -> center */
                    #quizmaster .table_wrap table th:nth-child(1),
                    #quizmaster .table_wrap table td:nth-child(1),
                    #quizmaster .table_wrap table th:nth-child(2),
                    #quizmaster .table_wrap table td:nth-child(2),
                    #quizmaster .table_wrap table th:nth-child(6),
                    #quizmaster .table_wrap table td:nth-child(6),
                    #quizmaster .table_wrap table th:nth-child(7),
                    #quizmaster .table_wrap table td:nth-child(7),
                    #quizmaster .table_wrap table th:nth-child(8),
                    #quizmaster .table_wrap table td:nth-child(8) {
                        text-align: center;
                    }
                    /* 3 = Quiz Title, 4 = Hall, 5 = Start Time -> left */
                    #quizmaster .table_wrap table th:nth-child(3),
                    #quizmaster .table_wrap table td:nth-child(3),
                    #quizmaster .table_wrap table th:nth-child(4),
                    #quizmaster .table_wrap table td:nth-child(4),
                    #quizmaster .table_wrap table th:nth-child(5),
                    #quizmaster .table_wrap table td:nth-child(5) {
                        text-align: left;
                    }
                </style>

                <div class="regi_search_wrap mb-3">
                    <div class="regi_search_wrap_btn_box">
                        <a href="<?= $qmSelf ?>?show=add<?= $searchString ?>"><i class="fal fa-plus"></i> Create New Quiz</a>
                    </div>
                </div>

                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="sl" align="center"><input name="check_all" id="check_all" type="checkbox" /></th>
                                <th class="sl" align="center">Sl No</th>
                                <th align="left">Quiz Title</th>
                                <th align="left">Hall</th>
                                <th align="left">Start Time</th>
                                <th align="center">Quiz Status</th>
                                <th align="center">Applicants</th>
                                <th align="center">Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $quizCounter = 0;
                            if ($qmList) {
                                foreach ($qmList as $row) {
                                    $quizCounter++;
                            ?>
                                    <tr class="tlisting">
                                        <td align="center">
                                            <input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= $row['id'] ?>" />
                                        </td>
                                        <td align="center"><?= $quizCounter + ($qmPg * 15) ?></td>
                                        <td align="left"><?= htmlspecialchars($row['title']) ?></td>
                                        <td align="left"><?= htmlspecialchars($row['hall_title'] ?? '') ?></td>
                                        <td align="left"><?= !empty($row['start_time']) ? date('d M Y, H:i', strtotime($row['start_time'])) : '--' ?></td>
                                        <td align="center"><?= ucfirst($row['status']) ?></td>
                                        <!-- Applicants: approve / reject applications (participations.php) -->
                                        <td align="center">
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>participations.php?quiz_id=<?= $row['id'] ?>" target="_blank">View</a>
                                        </td>
                                        <td align="center">
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?act=<?= ($row['row_status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $row['id'] ?><?= $searchString ?><?= $qmRet ?>"
                                               class="badge_padding <?= ($row['row_status'] == 'A') ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                                <?= ($row['row_status'] == 'A') ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td class="action">
                                            <div class="action_div dropdown" role="menu" aria-label="Actions for <?= htmlspecialchars($row['title']) ?>">
                                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for <?= htmlspecialchars($row['title']) ?>"><?php ellips(); ?></button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <?php if ($row['status'] !== 'completed') { ?>
                                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>new_quiz_updated.php?quiz_id=<?= $row['id'] ?>" class="icon_hover badge_primary action-transparent"><?php view(); ?>View</a>
                                                        <?php if ($row['status'] === 'active') { ?>
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=notify_all<?= $qmRet ?>" class="icon_hover badge_primary action-transparent"><i class="fal fa-bell"></i>Notify All</a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <a href="<?= $qmSelf ?>?show=edit&quiz_id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_primary action-transparent"><i class="fal fa-edit"></i>Edit</a>
                                                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=reset_quiz<?= $qmRet ?>" class="icon_hover badge_primary action-transparent" onclick="return confirm('Reset this quiz?');"><i class="fal fa-redo"></i>Reset Quiz</a>
                                                    <!-- Participants: users who have joined (participants.php) -->
                                                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>participations.php?quiz_id=<?= $row['id'] ?>" class="icon_hover badge_primary action-transparent"><i class="fal fa-users"></i>Participants</a>
                                                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?><?= $qmRet ?>" class="icon_hover badge_primary action-transparent" onclick="return confirm('Do you really want to remove this record');"><i class="fal fa-trash-alt"></i>Remove</a>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="9" align="center" style="text-align:center;">
                                        <span class="mandatory">No Record(s) Found !!</span>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
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

<?php } ?>

            </div>

        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    // ---------- Fallback: quiz form submit hook (used only if not defined elsewhere) ----------
    if (typeof onSubmitAction !== 'function') {
        window.onSubmitAction = function () { return true; };
    }

    // ---------- Panel Discussion hide Rounds / Levels (quiz form) ----------
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

    // ---------- Multi action apply (quiz) ----------
    $('#applyMultiActionBtn').on('click', function () {
        var action = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one quiz.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected quizzes?')) {
            $.ajax({
                url: '<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                success: function () { location.reload(); }
            });
        }
    });
</script>

</html>