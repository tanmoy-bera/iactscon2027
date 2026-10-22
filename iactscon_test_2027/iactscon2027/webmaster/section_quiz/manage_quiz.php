<?php
include_once("../includes/source.php");
include_once('../includes/init.php');
include_once('../includes/function.workshop.php');
?>

<body>
    <?php include_once("../includes/left-menu.php"); ?>
    <header>
        <h2>Manage Quiz</h2>
        <?php include_once("../includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]     = $pageKeyVal;
        $searchArray['start_date'] = $_REQUEST['start_date'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . $searchVal;
        }

        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Quiz</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Create New Quiz' : (($show == 'edit') ? 'Edit Quiz' : 'Quiz Listing') ?></h2>
                <h6>Manage quizzes, halls, rounds and participation settings.</h6>
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Quiz</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                switch ($show) {
                    case 'add':
                        addQuiz($cfg, $mycms);
                        break;
                    case 'edit':
                        editQuiz($cfg, $mycms);
                        break;
                    case 'view':
                        if (function_exists('viewQuiz')) {
                            viewQuiz($cfg, $mycms);
                        }
                        break;
                    default:
                        showAllQuiz($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("../includes/js-source.php"); ?>
<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ccicon_2025/webmaster/util/font-awesome-4.7.0/css/font-awesome.min.css" />
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

        if (confirm(`Are you sure you want to ${action} the selected quizzes?`)) {
            $.ajax({
                url: 'manage_quiz.process.php',
                type: 'POST',
                data: {
                    act: 'MultiAction',
                    action: action,
                    ids: selected
                },
                success: function(response) {
                    location.reload(); // refresh after success
                }
            });
        }
    });

    $(document).ready(function () {
        $('#quiz_type').on('change', function () {
            let quizType = $('#quiz_type').val();
            if (quizType === 'panel discussion') {
                $('.optionalData').hide();
            } else {
                $('.optionalData').show();
            }
        });
    });
</script>
</html>
<?php

/*============== SHOW ALL QUIZ ================*/
function showAllQuiz($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];
    if (!empty($_REQUEST['start_date'])) {
        $con .= " AND DATE(`start_time`) >= ?";
        $params[] = [
            'FILD' => 'start_date',
            'DATA' => $_REQUEST['start_date'],
            'TYP'  => 's'
        ];
    }

    $sql = [];
    $sql['QUERY'] = "SELECT q.*, h.hall_title 
        FROM `rcg_quizzes` AS q
        LEFT JOIN `rcg_program_schedule_hall` AS h 
            ON q.hall_id = h.id
        WHERE q.`row_status` != ? 
        $con 
        ORDER BY q.start_time DESC";

    $sql['PARAM'][] = [
        'FILD' => 'row_status',
        'DATA' => 'D',
        'TYP'  => 's'
    ];

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);
    ?>
    <form name="frmCountryListing" id="frmCountryListing" action="manage_quiz.process.php" method="post">
        <input type="hidden" name="act" id="act" value="search_country" />
        <input type="hidden" id="excelPrintVal" name="excelPrintVal" />

        <div class="com_info_box active" id="quizListing">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-question-circle"></i></span> Quiz Listing</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sl">
                                            <input name="check_all" id="check_all" type="checkbox" />
                                        </th>
                                        <th class="sl">#</th>
                                        <th>Quiz Title</th>
                                        <th>Hall</th>
                                        <th>Start Time</th>
                                        <th>Quiz Status</th>
                                        <th>Applicants</th>
                                        <th>Status</th>
                                        <th class="action text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result) {
                                        $i = 0;
                                        foreach ($result as $row) {
                                            $i++;
                                    ?>
                                    <tr>
                                        <td class="sl">
                                            <input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= $row['id'] ?>" />
                                        </td>
                                        <td class="sl"><?= $i + ($_REQUEST['_pgn_'] * 15) ?></td>
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= htmlspecialchars($row['hall_title'] ?? '') ?></td>
                                        <td><?= !empty($row['start_time']) ? date('d-m-Y H:i:s', strtotime($row['start_time'])) : '--' ?></td>
                                        <td><?= ucfirst($row['status']) ?></td>
                                        <td>
                                            <a href="participations.php?quiz_id=<?= $row['id'] ?>" target="_blank" class="icon_hover badge_secondary action-transparent">View</a>
                                        </td>
                                        <td>
                                            <a href="manage_quiz.process.php?act=<?= ($row['row_status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $row['id'] ?><?= $searchString ?>"
                                                class="badge_padding <?= ($row['row_status'] == 'A') ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                                <?= ($row['row_status'] == 'A') ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td class="action">
                                            <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <?php if ($row['status'] !== 'completed') { ?>
                                                        <a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="new_quiz_updated.php?quiz_id=<?= $row['id'] ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-eye"></i> View</a>
                                                        <?php if ($row['status'] === 'active') { ?>
                                                        <a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=notify_all" class="icon_hover badge_secondary action-transparent"><i class="fal fa-bell"></i> Notify All</a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=edit&quiz_id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                    <a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=reset_quiz" class="icon_hover badge_secondary action-transparent"><i class="fal fa-redo"></i> Reset Quiz</a>
                                                    <a href="participants.php?quiz_id=<?= $row['id'] ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-users"></i> Participants</a>
                                                    <a href="javascript:void(0);" class="deleteQuizBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="manage_quiz.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>"><?php delete(); ?>Remove</a>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="9" align="center">
                                            <span class="mandatory">No Record.</span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="page_top_wrap mt-3">
                            <div class="page_top_wrap_left">
                                <select name="multiOperationSelector" id="multiOperationSelector">
                                    <option value="">Choose an action...</option>
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
    </form>
    <script>
        $(document).on('click', '.deleteQuizBtn', function () {
            if (confirm('Do you really want to remove this record?')) {
                window.location.href = $(this).data('href');
            }
        });
    </script>
    <?php
}

/*================= ADD QUIZ ===============*/
function addQuiz($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql_hall = [];
    $sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall 
                        WHERE status = 'A'
                        ORDER BY hall_title ASC";
    $quiz_halls = $mycms->sql_select($sql_hall);
    ?>
    <div class="com_info_box active" id="addQuiz">
        <form name="frmtypeadd" method="post" action="manage_quiz.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="add_quiz" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-question-circle"></i></span> Quiz Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Quiz Type <span class="mandatory">*</span></p>
                                <select name="quiz_type" id="quiz_type" required>
                                    <option value="default">Default</option>
                                    <option value="panel discussion">Panel Discussion</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Quiz Title <span class="mandatory">*</span></p>
                                <input type="text" name="title" id="title" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Hall <span class="mandatory">*</span></p>
                                <select name="hall" id="hall" class="validate[required]" required>
                                    <option value=""> - Select - </option>
                                    <?php foreach ($quiz_halls as $hall) { ?>
                                        <option value="<?= $hall['id'] ?>"><?= htmlspecialchars($hall['hall_title']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="frm_grp optionalData">
                                <p class="frm-head">Rounds <span class="mandatory">*</span></p>
                                <input type="number" name="rounds" id="rounds" class="validate[required]" value="1" required />
                            </div>
                            <div class="frm_grp optionalData">
                                <p class="frm-head">Levels <span class="mandatory">*</span></p>
                                <input type="number" name="levels" id="levels" class="validate[required]" value="1" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="start_time" id="start_time" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Answer Time (in sec.) <span class="mandatory">*</span></p>
                                <input type="number" name="answer_time" id="answer_time" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Buffer Time (in sec.) <span class="mandatory">*</span></p>
                                <input type="number" name="buffer_time" id="buffer_time" required value="0" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Marks for Correct Answer</p>
                                <input type="number" name="marks_correct" id="marks_correct" value="0" step="0.1" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Marks for Incorrect Answer</p>
                                <input type="number" name="marks_incorrect" id="marks_incorrect" value="0" step="0.1" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Quiz Status <span class="mandatory">*</span></p>
                                <select name="quiz_status" id="quiz_status" required>
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">View Leader Board <span class="mandatory">*</span></p>
                                <select name="view_answer" id="view_answer" required>
                                    <option value="end">At The End of The Quiz</option>
                                    <option value="question">After Each Question</option>
                                    <option value="level">After Each Level</option>
                                    <option value="round">After Each Round</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Seat Limit <span class="mandatory">*</span></p>
                                <input type="number" name="seat_limit" id="seat_limit" required value="0" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Application Closing Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="application_close_time" id="application_close_time" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Application Status <span class="mandatory">*</span></p>
                                <select name="application_status" id="application_status" required>
                                    <option value="active">Active</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Participation Type <span class="mandatory">*</span></p>
                                <select name="participation_type" id="participation_type" required>
                                    <option value="solo" selected>Solo</option>
                                    <option value="group">Group</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-info-circle"></i></span> Joining Instructions</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Show QR Code <span class="mandatory">*</span></p>
                                <div class="cus_check_wrap">
                                    <label class="cus_check outfrm_radio">
                                        <input type="radio" value="yes" name="enable_qr">
                                        <span class="checkmark">Yes</span>
                                    </label>
                                    <label class="cus_check outfrm_radio">
                                        <input type="radio" value="no" name="enable_qr" checked>
                                        <span class="checkmark">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*================= EDIT QUIZ ==============*/
function editQuiz($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql            = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_quizzes` WHERE `id` = ?";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['quiz_id'], 'TYP' => 's');
    $result         = $mycms->sql_select($sql);
    $row            = $result[0];

    $sql_hall = [];
    $sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall 
                        WHERE status = 'A'
                        ORDER BY hall_title ASC";
    $quiz_halls = $mycms->sql_select($sql_hall);

    $quiz_type       = $row['quiz_type'];
    $displayOptionTr = (strtolower($quiz_type) == 'panel discussion') ? 'none' : '';
    ?>
    <div class="com_info_box active" id="editQuiz">
        <form name="frmtypeedit" method="post" action="manage_quiz.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="edit_quiz" />
            <input type="hidden" name="quiz_id" id="quiz_id" value="<?= $_REQUEST['quiz_id'] ?>" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-question-circle"></i></span> Quiz Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Quiz Type <span class="mandatory">*</span></p>
                                <select name="quiz_type" id="quiz_type" required>
                                    <option <?= (strtolower($quiz_type) == 'default') ? 'selected' : ''; ?> value="default">Default</option>
                                    <option <?= (strtolower($quiz_type) == 'panel discussion') ? 'selected' : ''; ?> value="panel discussion">Panel Discussion</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Quiz Title <span class="mandatory">*</span></p>
                                <input type="text" name="title" id="title" class="validate[required]" value="<?= htmlspecialchars($row['title']); ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Hall <span class="mandatory">*</span></p>
                                <select name="hall" id="hall" class="validate[required]" required>
                                    <option value=""> - Select - </option>
                                    <?php foreach ($quiz_halls as $hall) { ?>
                                        <option value="<?= $hall['id'] ?>" <?= ($row['hall_id'] == $hall['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($hall['hall_title']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="frm_grp optionalData" style="display: <?= $displayOptionTr ?>;">
                                <p class="frm-head">Rounds <span class="mandatory">*</span></p>
                                <input type="number" name="rounds" id="rounds" class="validate[required]" value="<?= ($row['rounds'] > 0) ? $row['rounds'] : 1; ?>" required />
                            </div>
                            <div class="frm_grp optionalData" style="display: <?= $displayOptionTr ?>;">
                                <p class="frm-head">Levels <span class="mandatory">*</span></p>
                                <input type="number" name="levels" id="levels" class="validate[required]" value="<?= ($row['levels'] > 0) ? $row['levels'] : 1; ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="start_time" id="start_time" value="<?= $row['start_time']; ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Answer Time (in sec.) <span class="mandatory">*</span></p>
                                <input type="number" name="answer_time" value="<?= $row['answer_time']; ?>" id="answer_time" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Buffer Time (in sec.) <span class="mandatory">*</span></p>
                                <input type="number" name="buffer_time" id="buffer_time" required value="<?= ($row['buffer_time'] > 0) ? $row['buffer_time'] : 1; ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Marks for Correct Answer</p>
                                <input type="number" name="marks_correct" id="marks_correct" value="<?= ($row['marks_correct'] > 0) ? $row['marks_correct'] : 0; ?>" step="0.1" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Marks for Incorrect Answer</p>
                                <input type="number" name="marks_incorrect" id="marks_incorrect" value="<?= ($row['marks_incorrect'] > 0) ? $row['marks_incorrect'] : 0; ?>" step="0.1" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Quiz Status <span class="mandatory">*</span></p>
                                <select name="quiz_status" id="quiz_status" required>
                                    <option <?= ($row['status'] == 'draft') ? 'selected' : ''; ?> value="draft">Draft</option>
                                    <option <?= ($row['status'] == 'active') ? 'selected' : ''; ?> value="active">Active</option>
                                    <option <?= ($row['status'] == 'completed') ? 'selected' : ''; ?> value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">View Leader Board <span class="mandatory">*</span></p>
                                <select name="view_answer" id="view_answer" required>
                                    <option <?= ($row['view_answer'] == 'end') ? 'selected' : ''; ?> value="end">At The End of The Quiz</option>
                                    <option <?= ($row['view_answer'] == 'question') ? 'selected' : ''; ?> value="question">After Each Question</option>
                                    <option <?= ($row['view_answer'] == 'level') ? 'selected' : ''; ?> value="level">After Each Level</option>
                                    <option <?= ($row['view_answer'] == 'round') ? 'selected' : ''; ?> value="round">After Each Round</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Seat Limit <span class="mandatory">*</span></p>
                                <input type="number" name="seat_limit" id="seat_limit" required value="<?= $row['seat_limit']; ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Application Closing Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="application_close_time" id="application_close_time" required value="<?= $row['application_close_time']; ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Application Status <span class="mandatory">*</span></p>
                                <select name="application_status" id="application_status" required>
                                    <option <?= ($row['application_status'] == 'active') ? 'selected' : ''; ?> value="active">Active</option>
                                    <option <?= ($row['application_status'] == 'closed') ? 'selected' : ''; ?> value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Participation Type <span class="mandatory">*</span></p>
                                <select name="participation_type" id="participation_type" required>
                                    <option <?= ($row['participation_type'] == 'solo') ? 'selected' : ''; ?> value="solo">Solo</option>
                                    <option <?= ($row['participation_type'] == 'group') ? 'selected' : ''; ?> value="group">Group</option>
                                    <option <?= ($row['participation_type'] == 'both') ? 'selected' : ''; ?> value="both">Both</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-info-circle"></i></span> Joining Instructions</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Show QR Code <span class="mandatory">*</span></p>
                                <div class="cus_check_wrap">
                                    <label class="cus_check outfrm_radio">
                                        <input type="radio" value="yes" name="enable_qr" <?= ($row['enable_qr'] == 'yes') ? 'checked' : ''; ?>>
                                        <span class="checkmark">Yes</span>
                                    </label>
                                    <label class="cus_check outfrm_radio">
                                        <input type="radio" value="no" name="enable_qr" <?= ($row['enable_qr'] == 'no') ? 'checked' : ''; ?>>
                                        <span class="checkmark">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>