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
        <h2>Discussions, Quizzes & Polls</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <?php
    // One-time flash alert set by cardiac_talks_quiz.process.php / cardiac_talks_poll.process.php
    $flash = $_SESSION['cardiac_alert'] ?? null;
    unset($_SESSION['cardiac_alert']);
    ?>
    <style>
        .int_alert {
            position: fixed; top: 20px; right: 20px; z-index: 99999;
            min-width: 280px; max-width: 420px; padding: 14px 18px;
            border-radius: 10px; border: 1px solid;
            display: flex; gap: 14px; align-items: center; justify-content: space-between;
            font-size: 14px; box-shadow: 0 10px 30px rgba(0, 0, 0, .6);
        }
        .int_alert.success { color: #34d399; background: #0f1f1a; border-color: rgba(16, 185, 129, .45); }
        .int_alert.error   { color: #f87171; background: #221212; border-color: rgba(239, 68, 68, .45); }
        .int_alert button  { background: none; border: 0; color: inherit; font-size: 20px; line-height: 1; cursor: pointer; }
        /* all column headings centered */
        .table_wrap table thead th { text-align: center !important; }
        /* Action column: center the "..." button (theme aligns it right / floats it by default) */
        .table_wrap table td.action,
        .table_wrap table th.action { text-align: center !important; }
        .table_wrap table td.action .action_div {
            display: flex !important;
            justify-content: center !important;
            align-items: center;
            float: none !important;
            margin-left: auto !important;
            margin-right: auto !important;
            width: 100%;
        }

        /* ===== Tabs: Discussion | Quiz | Poll ===== */
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

        /* ===== Poll result row (expands under a poll) ===== */
        .poll_result_row { display: none; }
        .poll_result_row.open { display: table-row; }
        .poll_result_row td { background: #12141a; }
    </style>
    <?php if (!empty($flash['message'])) { ?>
    <div class="int_alert <?= htmlspecialchars($flash['type']) ?>" id="intAlert">
        <span>
            <i class="fal <?= ($flash['type'] === 'error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
            <?= htmlspecialchars($flash['message']) ?>
        </span>
        <button type="button" id="intAlertClose" aria-label="Close">&times;</button>
    </div>
    <?php } ?>

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

        /* ---------- Page / filter state carried through links and forms ---------- */
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : (int)$_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]    = $pageKeyVal;
        $searchArray['src_title'] = $_REQUEST['src_title'] ?? '';
        $searchArray['start_date'] = $_REQUEST['start_date'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . urlencode($searchVal);
        }

        /* ===== Public Group Management Tabs ===== */
        $groupTabs = array(
            'discussion' => array('label' => 'Discussion', 'icon' => 'fa-comments',        'url' => $_SERVER['PHP_SELF'] . '?tab=discussion'),
            'quiz'       => array('label' => 'Quiz',       'icon' => 'fa-question-circle', 'url' => $_SERVER['PHP_SELF'] . '?tab=quiz'),
            'poll'       => array('label' => 'Poll',       'icon' => 'fa-poll-h',          'url' => $_SERVER['PHP_SELF'] . '?tab=poll'),
        );

        if ($activeTab == 'quiz') {
            $pageTitle = 'Manage Quiz';
            $pageHead  = $isFormView ? ((($_REQUEST['edit_quiz'] ?? '') !== '') ? 'Edit Quiz' : 'Create New Quiz') : 'Quiz Listing';
            $pageSub   = 'Manage gamified quizzes, halls, rounds and participation settings.';
        } elseif ($activeTab == 'poll') {
            $pageTitle = 'Manage Polls';
            $pageHead  = $isFormView ? ((($_REQUEST['edit_poll'] ?? '') !== '') ? 'Edit Poll' : 'Create New Poll') : 'Poll Listing';
            $pageSub   = 'Manage live audience polls, options and voting results.';
        } else {
            $pageTitle = 'Manage Discussion';
            $pageHead  = 'Discussion Listing';
            $pageSub   = 'Moderate group posts and comments.';
        }
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Cardiac Talks</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#"><?= $pageTitle ?></a></li>
                    </ol>
                </nav>
                <h2><?= $pageHead ?></h2>
                <h6><?= $pageSub ?></h6>
            </div>
            <?php if ($activeTab != 'discussion' && !$isFormView) { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?tab=<?= $activeTab ?>&mode=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New <?= ($activeTab == 'quiz') ? 'Quiz' : 'Poll' ?></a>
            </div>
            <?php } ?>
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

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
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
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    // ---------- Flash alert: auto hide + close ----------
    $(function () {
        if ($('#intAlert').length) {
            setTimeout(function () { $('#intAlert').fadeOut(300); }, 4000);
        }
    });
    $(document).on('click', '#intAlertClose', function () {
        $('#intAlert').fadeOut(200);
    });
</script>

<?php if ($activeTab == 'quiz') { ?>
<script>
    // ---------- Select / Deselect all (quiz) ----------
    $(document).on('click', '#check_all', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxListing', function () {
        $('#check_all').prop('checked', $('.checkBoxListing').length === $('.checkBoxListing:checked').length);
    });

    // ---------- Remove confirm (quiz) ----------
    $(document).on('click', '.deleteQuizBtn', function () {
        if (confirm('Do you really want to remove this record?')) {
            window.location.href = $(this).data('href');
        }
    });

    // ---------- Multi action apply (quiz) ----------
    $(document).on('click', '#applyMultiActionBtn', function () {
        var action = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one quiz.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected quizzes?')) {
            $.ajax({
                url: 'cardiac_talks_quiz.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?tab=quiz'; }
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

    $(document).on('click', '#check_all_poll', function () {
        $('.checkBoxPoll').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxPoll', function () {
        $('#check_all_poll').prop('checked', $('.checkBoxPoll').length === $('.checkBoxPoll:checked').length);
    });

    $(document).on('click', '.deletePollBtn', function () {
        if (confirm('Do you really want to remove this poll?')) {
            window.location.href = $(this).data('href');
        }
    });

    $(document).on('click', '#applyPollMultiActionBtn', function () {
        var action = $('#pollMultiOperationSelector').val();
        var selected = $('.checkBoxPoll:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one poll.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected polls?')) {
            $.ajax({
                url: 'cardiac_talks_poll.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?tab=poll'; }
            });
        }
    });
</script>
<?php } else { ?>
<script>
    $(document).on('click', '#check_all_disc', function () {
        $('.checkBoxDisc').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxDisc', function () {
        $('#check_all_disc').prop('checked', $('.checkBoxDisc').length === $('.checkBoxDisc:checked').length);
    });

    $(document).on('click', '.deleteDiscBtn', function () {
        if (confirm('Do you really want to remove this?')) {
            window.location.href = $(this).data('href');
        }
    });

    $(document).on('click', '#applyDiscMultiActionBtn', function () {
        var action = $('#discMultiOperationSelector').val();
        var selected = $('.checkBoxDisc:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one post.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected posts?')) {
            $.ajax({
                url: 'manage_discussion.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?tab=discussion'; }
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
    global $searchArray, $searchString;

    $qmSelf  = $_SERVER['PHP_SELF'];
    $qmStart = $_REQUEST['start_date'] ?? '';
    $qmTitle = trim($_REQUEST['src_title'] ?? '');

    $con    = "";
    $params = [];
    if (!empty($qmStart)) {
        $con .= " AND DATE(q.`start_time`) >= ?";
        $params[] = ['FILD' => 'start_date', 'DATA' => $qmStart, 'TYP' => 's'];
    }
    if ($qmTitle !== '') {
        $con .= " AND q.`title` LIKE ?";
        $params[] = ['FILD' => 'title', 'DATA' => '%' . $qmTitle . '%', 'TYP' => 's'];
    }

    $sql = [];
    $sql['QUERY'] = "SELECT q.*, h.hall_title, g.group_name
        FROM `rcg_cardiac_talk_quizzes` AS q
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
    <div class="com_info_box active" id="quizListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-question-circle"></i></span> Quiz Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <!-- Search / filter -->
                    <form method="get" action="<?= $qmSelf ?>" class="mb-3">
                        <input type="hidden" name="tab" value="quiz" />
                        <div class="form_grid g_4">
                            <div class="frm_grp">
                                <p class="frm-head">Quiz Title</p>
                                <input type="text" name="src_title" value="<?= htmlspecialchars($qmTitle) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Start Date From</p>
                                <input type="date" name="start_date" value="<?= htmlspecialchars($qmStart) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">&nbsp;</p>
                                <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;text-align:center;"><input name="check_all" id="check_all" type="checkbox" /></th>
                                    <th class="sl">Sl. No.</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Group</th>
                                    <th>Hall</th>
                                    <th>Start Time</th>
                                    <th>Joining</th>
                                    <th>Quiz Status</th>
                                    <th>Status</th>
                                    <th class="action text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($qmList) {
                                    foreach ($qmList as $row) {
                                        $sl++;
                                        $isActive = ($row['row_status'] == 'A');
                                        $qid      = (int)$row['id'];
                                ?>
                                <tr>
                                    <td style="text-align:center;"><input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= $qid ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td style="text-align:center;"><?= htmlspecialchars(ucwords($row['quiz_type'] ?? 'default')) ?></td>
                                    <td><?= htmlspecialchars($row['group_name'] ?? '--') ?></td>
                                    <td><?= htmlspecialchars($row['hall_title'] ?? '--') ?></td>
                                    <td style="text-align:center;"><?= !empty($row['start_time']) ? date('d-m-Y H:i', strtotime($row['start_time'])) : '--' ?></td>
                                    <td style="text-align:center;">
                                        <?php if (($row['join_type'] ?? 'open') == 'approval') { ?>
                                            <n class="badge_padding badge_dark w-max-con text-uppercase">Approval</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Anyone</n>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;"><?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?></td>
                                    <td style="text-align:center;">
                                        <?php if ($isActive) { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                        <?php } ?>
                                    </td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="cardiac_talks_quiz_questions.php?quiz_id=<?= $qid ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-eye"></i> View</a>
                                                <a href="<?= $qmSelf ?>?tab=quiz&edit_quiz=<?= $qid ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                <?php if ($isActive) { ?>
                                                <a href="cardiac_talks_quiz.process.php?act=Inactive&id=<?= $qid ?><?= $searchString ?>" class="icon_hover badge_dark action-transparent"><i class="fal fa-ban"></i> Make Inactive</a>
                                                <?php } else { ?>
                                                <a href="cardiac_talks_quiz.process.php?act=Active&id=<?= $qid ?><?= $searchString ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-check"></i> Make Active</a>
                                                <?php } ?>
                                                <a href="cardiac_talks_quiz.process.php?act=reset_quiz&quiz_id=<?= $qid ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent" onclick="return confirm('Reset this quiz?');"><i class="fal fa-redo"></i> Reset Quiz</a>
                                                <a href="participants.php?quiz_id=<?= $qid ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-users"></i> Participants</a>
                                                <a href="javascript:void(0);" class="deleteQuizBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="cardiac_talks_quiz.process.php?act=Remove&id=<?= $qid ?><?= $searchString ?>">
                                                    <i class="fal fa-trash"></i> Delete Quiz
                                                </a>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="11" align="center">
                                        <span class="mandatory">No Record.</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk actions -->
                    <div class="mt-3 mb-3" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <div class="frm_grp" style="width:240px;margin:0;">
                            <select name="multiOperationSelector" id="multiOperationSelector">
                                <option value="">Choose an action...</option>
                                <option value="Active">Make Active</option>
                                <option value="Inactive">Make Inactive</option>
                                <option value="Remove">Remove</option>
                            </select>
                        </div>
                        <button type="button" class="badge_primary" id="applyMultiActionBtn"><i class="fal fa-check"></i> Apply to selected</button>
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

/*==================================================================
   QUIZ  -  FORM : add / edit
==================================================================*/
function manageQuizForm($cfg, $mycms)
{
    global $searchArray, $searchString;

    $qmSelf   = $_SERVER['PHP_SELF'];
    $qmEditId = $_REQUEST['edit_quiz'] ?? '';

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
        $sqlE['QUERY']   = "SELECT * FROM `rcg_cardiac_talk_quizzes` WHERE `id` = ?";
        $sqlE['PARAM'][] = ['FILD' => 'id', 'DATA' => $qmEditId, 'TYP' => 's'];
        $resE  = $mycms->sql_select($sqlE);
        $qmRow = $resE ? $resE[0] : [];
    }
    $isEdit = !empty($qmRow);

    $qmQuizType     = strtolower($qmRow['quiz_type'] ?? 'default');
    $qmHideOptional = ($qmQuizType == 'panel discussion') ? 'none' : '';
    ?>
    <div class="com_info_box active" id="quizForm">
        <form name="frmquiz" method="post" action="cardiac_talks_quiz.process.php" id="frmquiz" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="<?= $isEdit ? 'edit_quiz' : 'add_quiz' ?>" />
            <?php if ($isEdit) { ?>
                <input type="hidden" name="quiz_id" id="quiz_id" value="<?= htmlspecialchars($qmEditId) ?>" />
            <?php } ?>
            <input type="hidden" name="tab" value="quiz" />
            <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php } ?>

            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-question-circle"></i></span> Quiz Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $qmSelf ?>?tab=quiz<?= $searchString ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_6">
                            <div class="frm_grp span_6">
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

                            <div class="frm_grp span_3">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="start_time" id="start_time" value="<?= qmdt($qmRow['start_time'] ?? '') ?>" required />
                            </div>
                            <div class="frm_grp span_3">
                                <p class="frm-head">Answer Time (in sec.) <span class="mandatory">*</span></p>
                                <input type="number" name="answer_time" id="answer_time" value="<?= qmv($qmRow, 'answer_time') ?>" required />
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
                                <p class="frm-head">Joining Access <span class="mandatory">*</span></p>
                                <select name="join_type" id="join_type" required>
                                    <option value="open" <?= qmsel($qmRow, 'join_type', 'open', true) ?>>Anyone can join</option>
                                    <option value="approval" <?= qmsel($qmRow, 'join_type', 'approval') ?>>Approval needed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*==================================================================
   POLL  -  LIST (table) : read / status toggle / publish / reset / remove / multi action
==================================================================*/
function managePollList($cfg, $mycms)
{
    global $searchArray, $searchString;

    $qmSelf = $_SERVER['PHP_SELF'];

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
    <div class="com_info_box active" id="pollListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-poll-h"></i></span> Poll Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;text-align:center;"><input name="check_all_poll" id="check_all_poll" type="checkbox" /></th>
                                    <th class="sl">Sl. No.</th>
                                    <th>Question</th>
                                    <th>Group</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Phase</th>
                                    <th>Choice</th>
                                    <th>Votes</th>
                                    <th>Published</th>
                                    <th>Status</th>
                                    <th class="action text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($plList) {
                                    foreach ($plList as $prow) {
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
                                    <td style="text-align:center;"><input type="checkbox" name="poll_selected_ids[]" class="checkBoxPoll" value="<?= $pid ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($prow['title']) ?></td>
                                    <td><?= htmlspecialchars($prow['group_name'] ?? '--') ?></td>
                                    <td style="text-align:center;"><?= $stTs ? date('d-m-Y H:i', $stTs) : '--' ?></td>
                                    <td style="text-align:center;"><?= $enTs ? date('d-m-Y H:i', $enTs) : '--' ?></td>
                                    <td style="text-align:center;"><?= $phase ?></td>
                                    <td style="text-align:center;"><?= $prow['allow_multiple'] ? 'Multiple (max ' . (int)$prow['max_choices'] . ')' : 'Single' ?></td>
                                    <td style="text-align:center;"><?= $total ?></td>
                                    <td style="text-align:center;">
                                        <?php if ($prow['is_published']) { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Published</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_dark w-max-con text-uppercase">Unpublished</n>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if ($prow['is_active']) { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                        <?php } ?>
                                    </td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="javascript:void(0);" class="togglePollResult icon_hover badge_secondary action-transparent" data-id="<?= $pid ?>"><i class="fal fa-chart-bar"></i> View Results</a>
                                                <a href="<?= $qmSelf ?>?tab=poll&edit_poll=<?= $pid ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                <?php if ($prow['is_published']) { ?>
                                                <a href="cardiac_talks_poll.process.php?act=Unpublish&id=<?= $pid ?><?= $searchString ?>" class="icon_hover badge_dark action-transparent"><i class="fal fa-eye-slash"></i> Unpublish</a>
                                                <?php } else { ?>
                                                <a href="cardiac_talks_poll.process.php?act=Publish&id=<?= $pid ?><?= $searchString ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-eye"></i> Publish</a>
                                                <?php } ?>
                                                <?php if ($prow['is_active']) { ?>
                                                <a href="cardiac_talks_poll.process.php?act=Inactive&id=<?= $pid ?><?= $searchString ?>" class="icon_hover badge_dark action-transparent"><i class="fal fa-ban"></i> Make Inactive</a>
                                                <?php } else { ?>
                                                <a href="cardiac_talks_poll.process.php?act=Active&id=<?= $pid ?><?= $searchString ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-check"></i> Make Active</a>
                                                <?php } ?>
                                                <a href="cardiac_talks_poll.process.php?act=reset_poll&poll_id=<?= $pid ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent" onclick="return confirm('Reset all votes of this poll?');"><i class="fal fa-redo"></i> Reset Votes</a>
                                                <a href="javascript:void(0);" class="deletePollBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="cardiac_talks_poll.process.php?act=Remove&id=<?= $pid ?><?= $searchString ?>">
                                                    <i class="fal fa-trash"></i> Delete Poll
                                                </a>
                                            </ul>
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
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="12" align="center">
                                        <span class="mandatory">No Record.</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk actions -->
                    <div class="mt-3 mb-3" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <div class="frm_grp" style="width:240px;margin:0;">
                            <select name="pollMultiOperationSelector" id="pollMultiOperationSelector">
                                <option value="">Choose an action...</option>
                                <option value="Active">Make Active</option>
                                <option value="Inactive">Make Inactive</option>
                                <option value="Publish">Publish</option>
                                <option value="Unpublish">Unpublish</option>
                                <option value="Remove">Remove</option>
                            </select>
                        </div>
                        <button type="button" class="badge_primary" id="applyPollMultiActionBtn"><i class="fal fa-check"></i> Apply to selected</button>
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

/*==================================================================
   POLL  -  FORM : add / edit
==================================================================*/
function managePollForm($cfg, $mycms)
{
    global $searchArray, $searchString;

    $qmSelf   = $_SERVER['PHP_SELF'];
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
    <div class="com_info_box active" id="pollForm">
        <form name="frmpoll" method="post" action="cardiac_talks_poll.process.php" id="frmpoll" onsubmit="return onSubmitPoll();">
            <input type="hidden" name="act" value="<?= $plIsEdit ? 'edit_poll' : 'add_poll' ?>" />
            <?php if ($plIsEdit) { ?>
                <input type="hidden" name="poll_id" id="poll_id" value="<?= (int)$plRow['id'] ?>" />
            <?php } ?>
            <input type="hidden" name="tab" value="poll" />
            <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php } ?>

            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-poll-h"></i></span> Poll Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $qmSelf ?>?tab=poll<?= $searchString ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="SavePoll" name="SavePoll" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
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
                </div>
            </div>
        </form>
    </div>
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
    $sl     = is_numeric($restrt ?? null) ? (int)$restrt : 0;

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

    // Link to open a post on this page (keeps filters + page)
    $viewBase = $qmSelf . '?tab=discussion&f_group=' . $fGroup . '&f_status=' . urlencode($fStatus)
              . '&f_search=' . urlencode($fSearch) . '&_pgn_=' . $qmPg;
    ?>

    <!-- ============ FILTER ============ -->
    <div class="com_info_box active" id="discussionFilter">
        <form method="get" action="<?= $qmSelf ?>">
            <input type="hidden" name="tab" value="discussion" />
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-filter"></i></span> Filter Posts</n>
                        <div class="page_top_wrap_right">
                            <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                            <a href="<?= $qmSelf ?>?tab=discussion" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
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
                </div>
            </div>
        </form>
    </div>

    <!-- ============ POST LISTING ============ -->
    <div class="com_info_box active mt-3" id="discussionListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-comments"></i></span> Post Listing</n>
                </h5>
                <div class="com_info_box_inner">
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;text-align:center;"><input name="check_all_disc" id="check_all_disc" type="checkbox" /></th>
                                    <th class="sl">Sl. No.</th>
                                    <th>Posted By</th>
                                    <th>Group</th>
                                    <th>Post</th>
                                    <th>Media</th>
                                    <th>Status</th>
                                    <th>Posted On</th>
                                    <th class="action text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($dList) {
                                    foreach ($dList as $drow) {
                                        $sl++;
                                        $did = (int)$drow['id'];
                                ?>
                                <tr>
                                    <td style="text-align:center;"><input type="checkbox" name="disc_selected_ids[]" class="checkBoxDisc" value="<?= $did ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($drow['user_full_name'] ?? ('User #' . (int)$drow['user_id'])) ?></td>
                                    <td><?= htmlspecialchars($drow['group_name'] ?? '--') ?></td>
                                    <td><?= htmlspecialchars(mb_strimwidth((string)($drow['content'] ?? ''), 0, 90, '...')) ?></td>
                                    <td style="text-align:center;"><?= (int)$drow['media_count'] ?></td>
                                    <td style="text-align:center;">
                                        <?php if ($drow['status'] == 'A') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;"><?= !empty($drow['created_at']) ? date('d-m-Y H:i', strtotime($drow['created_at'])) : '--' ?></td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="<?= $viewBase ?>&view_post=<?= $did ?>#discussionPost" class="icon_hover badge_secondary action-transparent"><i class="fal fa-comments"></i> View &amp; Moderate</a>
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

                    <!-- Bulk actions -->
                    <div class="mt-3 mb-3" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <div class="frm_grp" style="width:240px;margin:0;">
                            <select name="discMultiOperationSelector" id="discMultiOperationSelector">
                                <option value="">Choose an action...</option>
                                <option value="Active">Make Active</option>
                                <option value="Inactive">Make Inactive</option>
                                <option value="Remove">Remove</option>
                            </select>
                        </div>
                        <button type="button" class="badge_primary" id="applyDiscMultiActionBtn"><i class="fal fa-check"></i> Apply to selected</button>
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

    <?php if ($vPost) { ?>
    <!-- ============ POST DETAIL / COMMENTS ============ -->
    <div class="com_info_box active mt-3" id="discussionPost">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-comments"></i></span> Post #<?= (int)$vPost['id'] ?>
                        &middot; <?= htmlspecialchars($vPost['group_name'] ?? '--') ?> &middot; by <?= htmlspecialchars($vPost['user_full_name'] ?? ('User #' . (int)$vPost['user_id'])) ?></n>
                    <div class="page_top_wrap_right">
                        <a href="<?= $viewBase ?>" class="badge_dark"><i class="fal fa-times"></i> Close</a>
                    </div>
                </h5>
                <div class="com_info_box_inner">

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
            </div>
        </div>
    </div>
    <?php } ?>
    <?php
}
?>