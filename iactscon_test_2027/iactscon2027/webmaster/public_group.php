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
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Public Group Management</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        /* ---------- Active tab: discussion | quiz | poll ---------- */
        $activeTab = $_REQUEST['tab'] ?? 'quiz';
        if (!in_array($activeTab, array('discussion', 'quiz', 'poll'))) {
            $activeTab = 'quiz';
        }

        /* ---------- View mode (quiz / poll): list | form ---------- */
        $isFormView = false;
        if ($activeTab == 'quiz') {
            $isFormView = (($_REQUEST['mode'] ?? '') === 'add') || (($_REQUEST['edit_quiz'] ?? '') !== '');
        } elseif ($activeTab == 'poll') {
            $isFormView = (($_REQUEST['mode'] ?? '') === 'add') || (($_REQUEST['edit_poll'] ?? '') !== '');
        }

        /* ===== Public Group Management Tabs ===== */
        $groupTabs = array(
            'discussion' => array('label' => 'Discussion', 'icon' => 'fa-comments',        'url' => 'public_group.php?tab=discussion'),
            'quiz'       => array('label' => 'Quiz',       'icon' => 'fa-question-circle', 'url' => 'public_group.php?tab=quiz'),
            'poll'       => array('label' => 'Poll',       'icon' => 'fa-poll-h',          'url' => 'public_group.php?tab=poll'),
        );

        if ($activeTab == 'quiz') {
            $pageTitle = 'Manage Quiz';
            $pageHead  = 'Quiz Management';
            $pageSub   = 'Manage gamified quizzes, halls, rounds and participation settings.';
        } elseif ($activeTab == 'poll') {
            $pageTitle = 'Manage Polls';
            $pageHead  = 'Poll Management';
            $pageSub   = 'Manage live audience polls, options and voting results.';
        } else {
            $pageTitle = 'Manage Discussion';
            $pageHead  = 'Discussion Management';
            $pageSub   = 'Moderate group posts and comments.';
        }
        ?>

        <style>
            .group_tabs_wrap {
                background: transparent;
                padding: 0;
                margin-bottom: 18px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .group_tabs {
                display: flex;
                flex-wrap: nowrap;
                justify-content: flex-start;
                align-items: center;
                gap: 10px;
                list-style: none;
                margin: 0;
                padding: 4px 2px 10px;
            }
            .group_tabs li { flex: 0 0 auto; }
            .group_tabs a {
                display: inline-flex;
                align-items: center;
                gap: 9px;
                padding: 10px 22px;
                background: #14161c;
                border: 1px solid #262a35;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 600;
                color: #94a3b8;
                text-decoration: none;
                white-space: nowrap;
                transition: all .25s ease;
            }
            .group_tabs a i { font-size: 15px; transition: transform .25s ease; }
            .group_tabs a:hover {
                background: #1e222b;
                border-color: #3b4252;
                color: #fff;
            }
            .group_tabs a:hover i { transform: translateY(-2px) scale(1.1); }
            .group_tabs a.active {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
                border-color: transparent;
                color: #fff;
                box-shadow: 0 6px 16px rgba(79, 70, 229, .45);
            }
            @media (max-width: 576px) {
                .group_tabs a { padding: 9px 16px; font-size: 13px; }
            }

            /* ===== CRUD table ===== */
            .crud_head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 14px;
            }
            .crud_head h5 { margin: 0; }
            .crud_filter {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }
            .crud_table_wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .crud_table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 13px;
            }
            .crud_table th {
                background: #1a1d26;
                color: #94a3b8;
                font-weight: 600;
                text-align: left;
                padding: 11px 12px;
                border-bottom: 1px solid #262a35;
                white-space: nowrap;
            }
            .crud_table td {
                padding: 11px 12px;
                border-bottom: 1px solid #1e222b;
                vertical-align: middle;
            }
            .crud_table tbody tr:hover { background: rgba(255, 255, 255, .03); }
            .crud_table .col_title { min-width: 200px; font-weight: 600; }
            .crud_actions { display: flex; gap: 6px; flex-wrap: nowrap; white-space: nowrap; }
            .crud_actions a { padding: 6px 10px; }
            .crud_empty { text-align: center; padding: 30px 10px; }
            .crud_bulk {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: 14px;
            }
            .poll_result_row { display: none; }
            .poll_result_row.open { display: table-row; }
            .poll_result_row td { background: #12141a; }
        </style>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Public Group Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#"><?= $pageTitle ?></a></li>
                    </ol>
                </nav>
                <h2><?= $pageHead ?></h2>
                <h6><?= $pageSub ?></h6>
            </div>
        </div>

        <!-- ===== Tabs: Discussion | Quiz | Poll ===== -->
        <div class="group_tabs_wrap">
            <ul class="group_tabs">
                <?php foreach ($groupTabs as $tabKey => $tab) { ?>
                <li>
                    <a href="<?= $tab['url'] ?>" class="<?= ($tabKey == $activeTab) ? 'active' : '' ?>" <?= ($tabKey == $activeTab) ? 'aria-current="page"' : '' ?>>
                        <i class="fal <?= $tab['icon'] ?>"></i> <?= $tab['label'] ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>

        <?php
        if ($activeTab == 'quiz') {
            /* ===== QUIZ TAB ===== */
            if ($isFormView) {
                manageQuizForm($cfg, $mycms);
            } else {
                manageQuizList($cfg, $mycms);
            }
        } elseif ($activeTab == 'poll') {
            /* ===== POLL TAB ===== */
            if ($isFormView) {
                managePollForm($cfg, $mycms);
            } else {
                managePollList($cfg, $mycms);
            }
        } else {
            /* ===== DISCUSSION TAB ===== */
            manageDiscussion($cfg, $mycms);
        }
        ?>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>

<?php if ($activeTab == 'quiz') { ?>
<script>
    // ---------- Panel Discussion hide Rounds / Levels ----------
    $('#quiz_type').on('change', function () {
        if ($(this).val() === 'panel discussion') {
            $('.optionalData').hide();
        } else {
            $('.optionalData').show();
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
                url: 'public_manage_quiz.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = 'public_group.php?tab=quiz'; }
            });
        }
    });
</script>
<?php } elseif ($activeTab == 'poll') { ?>
<script>
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

    $('#allow_multiple').on('change', function () {
        if ($(this).val() === '1') {
            $('#maxChoicesWrap').show();
        } else {
            $('#maxChoicesWrap').hide();
            $('#max_choices').val(1);
        }
    });

    function onSubmitPoll() {
        if ($('#poll_group_id').val() === '') {
            alert('Please select a group.');
            return false;
        }
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

    // ---------- Result row toggle ----------
    $(document).on('click', '.togglePollResult', function () {
        $('#pollResult_' + $(this).data('id')).toggleClass('open');
    });

    $('#check_all_poll').on('click', function () {
        $('.checkBoxPoll').prop('checked', this.checked);
    });

    $(document).on('click', '.deletePollBtn', function () {
        if (confirm('Do you really want to remove this poll?')) {
            window.location.href = $(this).data('href');
        }
    });

    $('#applyPollMultiActionBtn').on('click', function () {
        var action = $('#pollMultiOperationSelector').val();
        var selected = $('.checkBoxPoll:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one poll.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected polls?')) {
            $.ajax({
                url: 'public_manage_poll.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = 'public_group.php?tab=poll'; }
            });
        }
    });
</script>
<?php } else { ?>
<script>
    $('#check_all_disc').on('click', function () {
        $('.checkBoxDisc').prop('checked', this.checked);
    });

    $(document).on('click', '.deleteDiscBtn', function () {
        if (confirm('Do you really want to remove this?')) {
            window.location.href = $(this).data('href');
        }
    });

    $('#applyDiscMultiActionBtn').on('click', function () {
        var action = $('#discMultiOperationSelector').val();
        var selected = $('.checkBoxDisc:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one post.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected posts?')) {
            $.ajax({
                url: 'manage_discussion.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = 'public_group.php?tab=discussion'; }
            });
        }
    });
</script>
<?php } ?>
</html>
<?php

/*==================================================================
   Public groups for the Group dropdown
==================================================================*/
function getPublicGroups($mycms)
{
    $sqlG = [];
    $sqlG['QUERY'] = "SELECT id, group_name FROM `rcg_groups` WHERE `group_type` = 'public' AND `status` = 'A' ORDER BY group_name ASC";
    $res = $mycms->sql_select($sqlG);
    return $res ? $res : [];
}

/*==================================================================
   QUIZ  -  LIST (table)  : read / status toggle / reset / remove / multi action
==================================================================*/
function manageQuizList($cfg, $mycms)
{
    $qmSelf  = $_SERVER['PHP_SELF'];
    $qmPg    = (int)($_REQUEST['_pgn_'] ?? 0);
    $qmStart = $_REQUEST['start_date'] ?? '';

    $con    = "";
    $params = [];
    if (!empty($qmStart)) {
        $con .= " AND DATE(q.`start_time`) >= ?";
        $params[] = ['FILD' => 'start_date', 'DATA' => $qmStart, 'TYP' => 's'];
    }

    $sql = [];
    $sql['QUERY'] = "SELECT q.*, h.hall_title, g.group_name
        FROM `public_rcg_quiz` AS q
        LEFT JOIN `rcg_program_schedule_hall` AS h ON q.hall_id = h.id
        LEFT JOIN `rcg_groups` AS g ON q.group_id = g.id
        WHERE q.`row_status` != ?
        $con
        ORDER BY q.start_time DESC";
    $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];
    foreach ($params as $p) { $sql['PARAM'][] = $p; }

    $qmList = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);
    $sl     = is_numeric($restrt ?? null) ? (int)$restrt : 0;
    ?>
    <div class="basic_grid_box">
        <div class="crud_head">
            <h5><n><i class="fal fa-question-circle"></i> Quiz Listing</n></h5>
            <div class="crud_filter">
                <form method="get" action="<?= $qmSelf ?>" class="crud_filter">
                    <input type="hidden" name="tab" value="quiz" />
                    <input type="date" name="start_date" value="<?= htmlspecialchars($qmStart) ?>" title="Start date from" />
                    <button type="submit" class="badge_dark"><i class="fal fa-search"></i> Filter</button>
                    <?php if ($qmStart !== '') { ?>
                        <a href="<?= $qmSelf ?>?tab=quiz" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                    <?php } ?>
                </form>
                <a href="<?= $qmSelf ?>?tab=quiz&mode=add&_pgn_=<?= $qmPg ?>" class="badge_success"><i class="fal fa-plus"></i> Create New Quiz</a>
            </div>
        </div>

        <div class="crud_table_wrap">
            <table class="crud_table">
                <thead>
                    <tr>
                        <th style="width:36px;"><input name="check_all" id="check_all" type="checkbox" /></th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Group</th>
                        <th>Hall</th>
                        <th>Start Time</th>
                        <th>Quiz Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($qmList) { foreach ($qmList as $row) { $sl++; $isActive = ($row['row_status'] == 'A'); ?>
                    <tr>
                        <td><input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= (int)$row['id'] ?>" /></td>
                        <td><?= $sl ?></td>
                        <td class="col_title"><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars(ucwords($row['quiz_type'] ?? 'default')) ?></td>
                        <td><?= htmlspecialchars($row['group_name'] ?? '--') ?></td>
                        <td><?= htmlspecialchars($row['hall_title'] ?? '--') ?></td>
                        <td><?= !empty($row['start_time']) ? date('d-m-Y H:i', strtotime($row['start_time'])) : '--' ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?></td>
                        <td>
                            <a href="public_manage_quiz.process.php?act=<?= $isActive ? 'Inactive' : 'Active' ?>&id=<?= (int)$row['id'] ?>&_pgn_=<?= $qmPg ?>"
                               class="badge_padding <?= $isActive ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                <?= $isActive ? 'Active' : 'Inactive' ?>
                            </a>
                        </td>
                        <td>
                            <div class="crud_actions">
                                <a href="<?= $qmSelf ?>?tab=quiz&edit_quiz=<?= (int)$row['id'] ?>&_pgn_=<?= $qmPg ?>" class="badge_secondary" title="Edit"><i class="fal fa-edit"></i></a>
                                <a href="public_manage_quiz.process.php?quiz_id=<?= (int)$row['id'] ?>&act=reset_quiz" class="badge_secondary" title="Reset Quiz" onclick="return confirm('Reset this quiz?');"><i class="fal fa-redo"></i></a>
                                <a href="participants.php?quiz_id=<?= (int)$row['id'] ?>" class="badge_secondary" title="Participants"><i class="fal fa-users"></i></a>
                                <a href="javascript:void(0);" class="deleteQuizBtn badge_danger" title="Remove" data-href="public_manage_quiz.process.php?act=Remove&id=<?= (int)$row['id'] ?>&_pgn_=<?= $qmPg ?>"><i class="fal fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="10" class="crud_empty"><span class="mandatory">No Record.</span></td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Multi action -->
        <div class="crud_bulk">
            <select name="multiOperationSelector" id="multiOperationSelector">
                <option value="">Choose an action..</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Remove">Remove</option>
            </select>
            <button type="button" id="applyMultiActionBtn" class="badge_dark">Apply to selected</button>
        </div>

        <div class="paginationDisplay">
            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
        </div>
    </div>
    <?php
}

/*==================================================================
   QUIZ  -  FORM : add / edit
==================================================================*/
function manageQuizForm($cfg, $mycms)
{
    $qmSelf   = $_SERVER['PHP_SELF'];
    $qmEditId = $_REQUEST['edit_quiz'] ?? '';
    $qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
    $qmStart  = $_REQUEST['start_date'] ?? '';

    /* ---------- Halls ---------- */
    $sql_hall = [];
    $sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall WHERE status = 'A' ORDER BY hall_title ASC";
    $qmHalls = $mycms->sql_select($sql_hall);

    /* ---------- Public groups ---------- */
    $qmGroups = getPublicGroups($mycms);

    /* ---------- Edit mode: row load ---------- */
    $qmRow = [];
    if ($qmEditId !== '') {
        $sqlE = [];
        $sqlE['QUERY']   = "SELECT * FROM `public_rcg_quiz` WHERE `id` = ?";
        $sqlE['PARAM'][] = ['FILD' => 'id', 'DATA' => $qmEditId, 'TYP' => 's'];
        $resE  = $mycms->sql_select($sqlE);
        $qmRow = $resE ? $resE[0] : [];
    }
    $isEdit = !empty($qmRow);

    $qmQuizType     = strtolower($qmRow['quiz_type'] ?? 'default');
    $qmHideOptional = ($qmQuizType == 'panel discussion') ? 'none' : '';
    ?>
    <form name="frmquiz" method="post" action="public_manage_quiz.process.php" id="frmquiz" onsubmit="return onSubmitAction();">
        <input type="hidden" name="act" value="<?= $isEdit ? 'edit_quiz' : 'add_quiz' ?>" />
        <?php if ($isEdit) { ?>
            <input type="hidden" name="quiz_id" id="quiz_id" value="<?= htmlspecialchars($qmEditId) ?>" />
        <?php } ?>
        <input type="hidden" name="tab" value="quiz" />
        <input type="hidden" name="_pgn_" value="<?= $qmPg ?>" />
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($qmStart) ?>" />

        <div class="basic_grid_box">
            <div class="venue_hall_top push_noti_top">
                <h5>
                    <n><i class="fal fa-question-circle"></i><?= $isEdit ? 'Edit Quiz' : 'Create New Quiz' ?></n>
                    <l>Manage quizzes, halls, rounds and participation settings.</l>
                </h5>
                <div class="page_top_wrap_right">
                    <a href="<?= $qmSelf ?>?tab=quiz&_pgn_=<?= $qmPg ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back to List</a>
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

                <div class="frm_grp span_3">
                    <p class="frm-head">Group <span class="mandatory">*</span></p>
                    <select name="group_id" id="quiz_group_id" required>
                        <option value=""> - Select Group - </option>
                        <?php foreach ($qmGroups as $g) { ?>
                            <option value="<?= (int)$g['id'] ?>" <?= (($qmRow['group_id'] ?? '') == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['group_name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="frm_grp span_3">
                    <p class="frm-head">Hall <span class="mandatory"></span></p>
                    <select name="hall" id="hall" class="validate[required]">
                        <option value=""> - Select - </option>
                        <?php if ($qmHalls) { foreach ($qmHalls as $hall) { ?>
                            <option value="<?= $hall['id'] ?>" <?= (($qmRow['hall_id'] ?? '') == $hall['id']) ? 'selected' : '' ?>><?= htmlspecialchars($hall['hall_title']) ?></option>
                        <?php } } ?>
                    </select>
                </div>
                <div class="frm_grp span_3 optionalData" style="display: <?= $qmHideOptional ?>;">
                    <p class="frm-head">Rounds <span class="mandatory">*</span></p>
                    <input type="number" name="rounds" id="rounds" class="validate[required]" value="<?= (($qmRow['rounds'] ?? 0) > 0) ? (int)$qmRow['rounds'] : 1 ?>" required />
                </div>
                <div class="frm_grp span_3 optionalData" style="display: <?= $qmHideOptional ?>;">
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
            </div>
        </div>
    </form>
    <?php
}

/*==================================================================
   POLL  -  LIST (table) : read / status toggle / publish / reset / remove / multi action
==================================================================*/
function managePollList($cfg, $mycms)
{
    $qmSelf = $_SERVER['PHP_SELF'];
    $qmPg   = (int)($_REQUEST['_pgn_'] ?? 0);

    /* ---------- Poll listing (with total votes via subquery) ---------- */
    $sqlPL = [];
    $sqlPL['QUERY'] = "SELECT p.*, g.group_name,
            (SELECT COALESCE(SUM(o.vote_count), 0) FROM `public_rcg_poll_options` o WHERE o.poll_id = p.id) AS total_votes
        FROM `public_rcg_polls` AS p
        LEFT JOIN `rcg_groups` AS g ON p.group_id = g.id
        WHERE p.`is_deleted` = 0
        ORDER BY p.id DESC";
    $plList = $mycms->sql_select_paginated($indexVal, $sqlPL, 15, $restrt);
    $sl     = is_numeric($restrt ?? null) ? (int)$restrt : 0;

    /* ---------- Options (for result rows) ---------- */
    $plOptMap = [];
    if ($plList) {
        $plIds = implode(',', array_map('intval', array_column($plList, 'id')));
        $sqlOL = [];
        $sqlOL['QUERY'] = "SELECT id, poll_id, option_text, vote_count FROM `public_rcg_poll_options` WHERE poll_id IN ($plIds) ORDER BY id ASC";
        $resOL = $mycms->sql_select($sqlOL);
        if ($resOL) {
            foreach ($resOL as $o) { $plOptMap[$o['poll_id']][] = $o; }
        }
    }
    ?>
    <div class="basic_grid_box">
        <div class="crud_head">
            <h5><n><i class="fal fa-poll-h"></i> Poll Listing</n></h5>
            <div class="crud_filter">
                <a href="<?= $qmSelf ?>?tab=poll&mode=add&_pgn_=<?= $qmPg ?>" class="badge_success"><i class="fal fa-plus"></i> Create New Poll</a>
            </div>
        </div>

        <div class="crud_table_wrap">
            <table class="crud_table">
                <thead>
                    <tr>
                        <th style="width:36px;"><input name="check_all_poll" id="check_all_poll" type="checkbox" /></th>
                        <th>#</th>
                        <th>Question</th>
                        <th>Group</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Phase</th>
                        <th>Choice</th>
                        <th>Votes</th>
                        <th>Published</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($plList) { foreach ($plList as $prow) {
                    $sl++;
                    $pid   = (int)$prow['id'];
                    $total = (int)$prow['total_votes'];
                    $now   = time();
                    $stTs  = !empty($prow['start_time']) ? strtotime($prow['start_time']) : 0;
                    $enTs  = !empty($prow['end_time']) ? strtotime($prow['end_time']) : 0;
                    if ($stTs && $now < $stTs)     { $phase = 'Upcoming'; }
                    elseif ($enTs && $now > $enTs) { $phase = 'Ended'; }
                    else                           { $phase = 'Live'; }
                ?>
                    <tr>
                        <td><input type="checkbox" name="poll_selected_ids[]" class="checkBoxPoll" value="<?= $pid ?>" /></td>
                        <td><?= $sl ?></td>
                        <td class="col_title"><?= htmlspecialchars($prow['title']) ?></td>
                        <td><?= htmlspecialchars($prow['group_name'] ?? '--') ?></td>
                        <td><?= $stTs ? date('d-m-Y H:i', $stTs) : '--' ?></td>
                        <td><?= $enTs ? date('d-m-Y H:i', $enTs) : '--' ?></td>
                        <td><?= $phase ?></td>
                        <td><?= $prow['allow_multiple'] ? 'Multiple (max ' . (int)$prow['max_choices'] . ')' : 'Single' ?></td>
                        <td><?= $total ?></td>
                        <td>
                            <a href="public_manage_poll.process.php?act=<?= $prow['is_published'] ? 'Unpublish' : 'Publish' ?>&id=<?= $pid ?>&_pgn_=<?= $qmPg ?>"
                               class="badge_padding <?= $prow['is_published'] ? 'badge_success' : 'badge_dark' ?> w-max-con">
                                <?= $prow['is_published'] ? 'Published' : 'Unpublished' ?>
                            </a>
                        </td>
                        <td>
                            <a href="public_manage_poll.process.php?act=<?= $prow['is_active'] ? 'Inactive' : 'Active' ?>&id=<?= $pid ?>&_pgn_=<?= $qmPg ?>"
                               class="badge_padding <?= $prow['is_active'] ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                <?= $prow['is_active'] ? 'Active' : 'Inactive' ?>
                            </a>
                        </td>
                        <td>
                            <div class="crud_actions">
                                <a href="javascript:void(0);" class="badge_secondary togglePollResult" data-id="<?= $pid ?>" title="View Results"><i class="fal fa-chart-bar"></i></a>
                                <a href="<?= $qmSelf ?>?tab=poll&edit_poll=<?= $pid ?>&_pgn_=<?= $qmPg ?>" class="badge_secondary" title="Edit"><i class="fal fa-edit"></i></a>
                                <a href="public_manage_poll.process.php?poll_id=<?= $pid ?>&act=reset_poll&_pgn_=<?= $qmPg ?>" class="badge_secondary" title="Reset Votes" onclick="return confirm('Reset all votes of this poll?');"><i class="fal fa-redo"></i></a>
                                <a href="javascript:void(0);" class="deletePollBtn badge_danger" title="Remove" data-href="public_manage_poll.process.php?act=Remove&id=<?= $pid ?>&_pgn_=<?= $qmPg ?>"><i class="fal fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr class="poll_result_row" id="pollResult_<?= $pid ?>">
                        <td colspan="12">
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
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="12" class="crud_empty"><span class="mandatory">No Record.</span></td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Multi action -->
        <div class="crud_bulk">
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

        <div class="paginationDisplay">
            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
        </div>
    </div>
    <?php
}

/*==================================================================
   POLL  -  FORM : add / edit
==================================================================*/
function managePollForm($cfg, $mycms)
{
    $qmSelf   = $_SERVER['PHP_SELF'];
    $qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
    $plEditId = $_REQUEST['edit_poll'] ?? '';

    /* ---------- Public groups ---------- */
    $plGroups = getPublicGroups($mycms);

    /* ---------- Poll edit mode: row + options ---------- */
    $plRow     = [];
    $plOptions = [];
    if ($plEditId !== '') {
        $sqlP = [];
        $sqlP['QUERY']   = "SELECT * FROM `public_rcg_polls` WHERE `id` = ? AND `is_deleted` = 0";
        $sqlP['PARAM'][] = ['FILD' => 'id', 'DATA' => (int)$plEditId, 'TYP' => 'i'];
        $resP  = $mycms->sql_select($sqlP);
        $plRow = $resP ? $resP[0] : [];

        if (!empty($plRow)) {
            $sqlO = [];
            $sqlO['QUERY']   = "SELECT id, option_text FROM `public_rcg_poll_options` WHERE `poll_id` = ? ORDER BY id ASC";
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
    ?>
    <form name="frmpoll" method="post" action="public_manage_poll.process.php" id="frmpoll" onsubmit="return onSubmitPoll();">
        <input type="hidden" name="act" value="<?= $plIsEdit ? 'edit_poll' : 'add_poll' ?>" />
        <?php if ($plIsEdit) { ?>
            <input type="hidden" name="poll_id" id="poll_id" value="<?= (int)$plRow['id'] ?>" />
        <?php } ?>
        <input type="hidden" name="tab" value="poll" />
        <input type="hidden" name="_pgn_" value="<?= $qmPg ?>" />

        <div class="basic_grid_box">
            <div class="venue_hall_top push_noti_top">
                <h5>
                    <n><i class="fal fa-poll-h"></i><?= $plIsEdit ? 'Edit Poll' : 'Create New Poll' ?></n>
                    <l>Launch real-time polls during keynotes or panel discussions.</l>
                </h5>
                <div class="page_top_wrap_right">
                    <a href="<?= $qmSelf ?>?tab=poll&_pgn_=<?= $qmPg ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back to List</a>
                    <button type="submit" id="SavePoll" name="SavePoll" class="badge_success"><i class="fal fa-save"></i> Save</button>
                </div>
            </div>

            <div class="form_grid g_6">
                <div class="frm_grp span_6">
                    <p class="frm-head">Poll Question <span class="mandatory">*</span></p>
                    <input type="text" name="title" id="poll_title" value="<?= qmv($plRow, 'title') ?>" required />
                </div>

                <div class="frm_grp span_6">
                    <p class="frm-head">Group <span class="mandatory">*</span></p>
                    <select name="group_id" id="poll_group_id" required>
                        <option value=""> - Select Group - </option>
                        <?php foreach ($plGroups as $g) { ?>
                            <option value="<?= (int)$g['id'] ?>" <?= (($plRow['group_id'] ?? '') == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['group_name']) ?></option>
                        <?php } ?>
                    </select>
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
    <?php
}

/*==================================================================
   DISCUSSION  (post list + comments moderation)
==================================================================*/
function manageDiscussion($cfg, $mycms)
{
    $qmSelf   = $_SERVER['PHP_SELF'];
    $qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
    $fGroup   = (int)($_REQUEST['f_group'] ?? 0);
    $fStatus  = $_REQUEST['f_status'] ?? '';
    $fSearch  = trim($_REQUEST['f_search'] ?? '');
    $viewPost = (int)($_REQUEST['view_post'] ?? 0);

    /* ---------- Public groups ---------- */
    $qmGroups = getPublicGroups($mycms);

    /* ---------- Filters ---------- */
    $con    = "";
    $params = [];
    if ($fGroup > 0) {
        $con .= " AND p.`group_id` = ?";
        $params[] = ['FILD' => 'group_id', 'DATA' => $fGroup, 'TYP' => 'i'];
    }
    if (in_array($fStatus, ['A', 'I'])) {
        $con .= " AND p.`status` = ?";
        $params[] = ['FILD' => 'status', 'DATA' => $fStatus, 'TYP' => 's'];
    }
    if ($fSearch !== '') {
        $con .= " AND p.`content` LIKE ?";
        $params[] = ['FILD' => 'content', 'DATA' => '%' . $fSearch . '%', 'TYP' => 's'];
    }

    /* ---------- Post listing ---------- */
    $sql = [];
    $sql['QUERY'] = "SELECT p.*, g.group_name, u.user_full_name,
            (SELECT COUNT(*) FROM `rcg_discussion_post_media` m WHERE m.post_id = p.id AND m.status = 'A') AS media_count
        FROM `rcg_discussion_posts` AS p
        LEFT JOIN `rcg_groups` AS g ON g.id = p.group_id
        LEFT JOIN `rcg_user_registration` AS u ON u.id = p.user_id
        WHERE p.`status` != ?
        $con
        ORDER BY p.id DESC";
    $sql['PARAM'][] = ['FILD' => 'status_not', 'DATA' => 'D', 'TYP' => 's'];
    foreach ($params as $p) { $sql['PARAM'][] = $p; }

    $dList  = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);
    $dCount = $dList ? count($dList) : 0;

    /* ---------- Selected post: detail + media + comments ---------- */
    $vPost = []; $vMedia = []; $vComments = [];
    if ($viewPost > 0) {
        $sqlV = [];
        $sqlV['QUERY'] = "SELECT p.*, g.group_name, u.user_full_name
            FROM `rcg_discussion_posts` AS p
            LEFT JOIN `rcg_groups` AS g ON g.id = p.group_id
            LEFT JOIN `rcg_user_registration` AS u ON u.id = p.user_id
            WHERE p.id = ? AND p.status != 'D'";
        $sqlV['PARAM'][] = ['FILD' => 'id', 'DATA' => $viewPost, 'TYP' => 'i'];
        $resV  = $mycms->sql_select($sqlV);
        $vPost = $resV ? $resV[0] : [];

        if ($vPost) {
            $sqlM = [];
            $sqlM['QUERY'] = "SELECT media_type, media_url FROM `rcg_discussion_post_media`
                WHERE post_id = ? AND status = 'A' ORDER BY sort_order ASC, id ASC";
            $sqlM['PARAM'][] = ['FILD' => 'post_id', 'DATA' => $viewPost, 'TYP' => 'i'];
            $vMedia = $mycms->sql_select($sqlM) ?: [];

            $sqlC = [];
            $sqlC['QUERY'] = "SELECT c.*, u.user_full_name
                FROM `rcg_discussion_comments` AS c
                LEFT JOIN `rcg_user_registration` AS u ON u.id = c.user_id
                WHERE c.post_id = ? AND c.status != 'D'
                ORDER BY c.id ASC";
            $sqlC['PARAM'][] = ['FILD' => 'post_id', 'DATA' => $viewPost, 'TYP' => 'i'];
            $vComments = $mycms->sql_select($sqlC) ?: [];
        }
    }

    // Return URL (keeps filters + page). Always starts with "public_group.php?"
    $back = 'public_group.php?tab=discussion&f_group=' . $fGroup . '&f_status=' . urlencode($fStatus)
          . '&f_search=' . urlencode($fSearch) . '&_pgn_=' . $qmPg;
    ?>
    <div class="basic_grid">

        <!-- ============ LEFT : FILTER + POST DETAIL / COMMENTS ============ -->
        <div class="basic_grid_left">
            <form method="get" action="<?= $qmSelf ?>">
                <input type="hidden" name="tab" value="discussion" />
                <div class="basic_grid_box">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-filter"></i>Filter Posts</n>
                            <l>Filter discussion posts by group, status or text.</l>
                        </h5>
                        <div class="page_top_wrap_right">
                            <button type="submit" class="badge_success"><i class="fal fa-search"></i> Search</button>
                            <a href="<?= $qmSelf ?>?tab=discussion" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                        </div>
                    </div>
                    <div class="form_grid g_6">
                        <div class="frm_grp span_3">
                            <p class="frm-head">Group</p>
                            <select name="f_group">
                                <option value="0"> - All Groups - </option>
                                <?php foreach ($qmGroups as $g) { ?>
                                    <option value="<?= (int)$g['id'] ?>" <?= ($fGroup == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['group_name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="frm_grp span_3">
                            <p class="frm-head">Status</p>
                            <select name="f_status">
                                <option value="">All</option>
                                <option value="A" <?= ($fStatus == 'A') ? 'selected' : '' ?>>Active</option>
                                <option value="I" <?= ($fStatus == 'I') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="frm_grp span_6">
                            <p class="frm-head">Search in post text</p>
                            <input type="text" name="f_search" value="<?= htmlspecialchars($fSearch) ?>" />
                        </div>
                    </div>
                </div>
            </form>

            <?php if ($vPost) { ?>
            <div class="basic_grid_box mt-3">
                <div class="venue_hall_top push_noti_top">
                    <h5>
                        <n><i class="fal fa-comments"></i>Post #<?= (int)$vPost['id'] ?></n>
                        <l><?= htmlspecialchars($vPost['group_name'] ?? '--') ?> &middot; by <?= htmlspecialchars($vPost['user_full_name'] ?? ('User #' . (int)$vPost['user_id'])) ?></l>
                    </h5>
                    <div class="page_top_wrap_right">
                        <a href="<?= $back ?>" class="badge_dark"><i class="fal fa-times"></i> Close</a>
                    </div>
                </div>

                <p><?= nl2br(htmlspecialchars($vPost['content'] ?? '')) ?></p>

                <?php if ($vMedia) { ?>
                    <ul class="quizques">
                        <?php foreach ($vMedia as $m) { ?>
                            <li>
                                <?= htmlspecialchars($m['media_type'] ?? 'media') ?>:
                                <?php if (!empty($m['media_url'])) { ?>
                                    <a href="<?= htmlspecialchars($m['media_url']) ?>" target="_blank">View</a>
                                <?php } else { ?>--<?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <h5 class="real_time_portal_heading mt-3"><n>Comments (<?= count($vComments) ?>)</n></h5>

                <?php if ($vComments) { foreach ($vComments as $c) {
                    $isReply = !empty($c['parent_comment_id']);
                    $cActive = ($c['status'] == 'A');
                ?>
                    <div class="push_noti_right_botttom_body_box" style="<?= $isReply ? 'margin-left:24px;' : '' ?>">
                        <p>
                            <n>
                                <?= $isReply ? '&#8627; ' : '' ?>
                                <?= htmlspecialchars($c['user_full_name'] ?? ('User #' . (int)$c['user_id'])) ?>
                            </n>
                            <a href="manage_discussion.process.php?act=<?= $cActive ? 'CommentInactive' : 'CommentActive' ?>&id=<?= (int)$c['id'] ?>&post_id=<?= (int)$vPost['id'] ?>&back=<?= urlencode($back) ?>"
                               class="badge_padding <?= $cActive ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase">
                                <?= $cActive ? 'Visible' : 'Hidden' ?>
                            </a>
                        </p>
                        <ul class="quizques">
                            <li><?= nl2br(htmlspecialchars($c['comment'] ?? '')) ?></li>
                            <?php if (!empty($c['media_url'])) { ?>
                                <li>Media: <a href="<?= htmlspecialchars($c['media_url']) ?>" target="_blank">View</a></li>
                            <?php } ?>
                            <li>Likes: <?= (int)$c['like_count'] ?> &middot; Replies: <?= (int)$c['reply_count'] ?> &middot; <?= !empty($c['created_at']) ? date('d-m-Y H:i', strtotime($c['created_at'])) : '--' ?></li>
                        </ul>
                        <h6>
                            <a href="javascript:void(0);" class="deleteDiscBtn badge_danger"
                               data-href="manage_discussion.process.php?act=CommentRemove&id=<?= (int)$c['id'] ?>&post_id=<?= (int)$vPost['id'] ?>&back=<?= urlencode($back) ?>">
                                <i class="fal fa-trash-alt"></i> Remove
                            </a>
                        </h6>
                    </div>
                <?php } } else { ?>
                    <div class="push_noti_right_botttom_body_box"><span class="mandatory">No comments.</span></div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>

    </div>
    <?php
}
?>