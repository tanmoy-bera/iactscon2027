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

        /* ===== Date-time inputs: make the native calendar icon visible on the dark theme ===== */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator,
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: .85;
            cursor: pointer;
        }
        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover,
        input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }

        /* ===== Poll result row (expands under a poll) ===== */
        .poll_result_row { display: none; }
        .poll_result_row.open { display: table-row; }
        .poll_result_row td { background: #12141a; }

        /* ===== Discussion: media modal ===== */
        #discMediaModal .modal-content {
            background: #14161c;
            color: #e2e8f0;
            border: 1px solid #262a35;
            border-radius: 12px;
        }
        #discMediaModal .modal-header,
        #discMediaModal .modal-footer { border-color: #262a35; }
        #discMediaModal .modal-title { font-size: 16px; font-weight: 600; }
        #discMediaModal .disc_close_btn {
            background: none; border: 0; color: #94a3b8;
            font-size: 24px; line-height: 1; cursor: pointer;
        }
        #discMediaModal .disc_close_btn:hover { color: #fff; }
        .disc_modal_content {
            white-space: pre-wrap; word-break: break-word;
            background: #12141a; border: 1px solid #262a35;
            border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; font-size: 14px;
        }
        .disc_media_grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }
        .disc_media_item {
            background: #0f1116; border: 1px solid #262a35;
            border-radius: 10px; overflow: hidden;
        }
        .disc_media_item a { display: block; }
        .disc_media_item img,
        .disc_media_item video {
            display: block; width: 100%; height: 200px; object-fit: contain; background: #000;
        }
        .disc_media_item .disc_media_cap {
            height: 34px; line-height: 22px; padding: 6px 10px; font-size: 12px; color: #94a3b8;
            text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .disc_counts span { white-space: nowrap; }
        #discMediaModal { z-index: 100001; }
        .modal-backdrop { z-index: 100000; background: #000; }
        .modal-backdrop.show { opacity: .75; }
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
        $searchArray['src_group'] = $_REQUEST['src_group'] ?? '';

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
            $pageSub   = 'Manage quizzes, halls, topics and participation settings.';
        } elseif ($activeTab == 'poll') {
            $pageTitle = 'Manage Polls';
            $pageHead  = $isFormView ? ((($_REQUEST['edit_poll'] ?? '') !== '') ? 'Edit Poll' : 'Create New Poll') : 'Poll Listing';
            $pageSub   = 'Manage live audience polls, topics, options and voting results.';
        } else {
            $pageTitle = 'Manage Discussion';
            $pageHead  = 'Discussion Listing';
            $pageSub   = 'View group posts and change their status.';
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

    function onSubmitPoll() {
        if ($('#poll_group_id').val() === '') {
            alert('Please select a topic.');
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
        if (!st) { alert('Please select the start time.'); return false; }
        if (!en) { alert('Please select the end time.'); return false; }
        if (new Date(en) <= new Date(st)) {
            alert('End time must be after start time.');
            return false;
        }
        return true;
    }

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

    $(document).on('click', '#applyDiscMultiActionBtn', function () {
        var action = $('#discMultiOperationSelector').val();
        var selected = $('.checkBoxDisc:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one post.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected posts?')) {
            $.ajax({
                url: 'cardiac_talks_discussion.process.php',
                type: 'POST',
                dataType: 'text',
                data: { act: 'MultiAction', action: action, ids: selected },
                success: function () {
                    window.location.href = window.location.href.split('#')[0];
                },
                error: function (xhr) {
                    alert('Bulk update failed (HTTP ' + xhr.status + '). Check cardiac_talks_discussion.process.php.');
                }
            });
        }
    });

    // ---------- View media / post content in modal ----------
    function discOpenModal() {
        var el = document.getElementById('discMediaModal');
        if (el.parentNode !== document.body) { document.body.appendChild(el); }
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
        } else if ($.fn.modal) {
            $(el).modal('show');
        }
    }
    function discCloseModal() {
        var el = document.getElementById('discMediaModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            var inst = window.bootstrap.Modal.getInstance(el);
            if (inst) inst.hide();
        } else if ($.fn.modal) {
            $(el).modal('hide');
        }
    }
    $(document).on('click', '.viewDiscMediaBtn', function () {
        var id  = $(this).data('post-id');
        var src = document.getElementById('discMediaSrc_' + id);
        if (!src) return;
        $('#discMediaModalTitle').text('Post #' + id + ' - Content & Media');
        $('#discMediaModalBody').html(src.innerHTML);
        discOpenModal();
    });
    $(document).on('click', '.discCloseBtn', function () {
        discCloseModal();
    });
    // stop any playing video and free the body when the modal is closed
    $(document).on('hidden.bs.modal', '#discMediaModal', function () {
        $('#discMediaModalBody').empty();
    });
</script>
<?php } ?>
</html>
<?php

/*==================================================================
   Public groups for the Group / Topic dropdown
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
    $sql['QUERY'] = "SELECT q.*, h.hall_title, g.group_name,
            (SELECT COUNT(*) FROM `rcg_cardiac_talk_quiz_participation` AS pp
              WHERE pp.quiz_id = q.id AND pp.status = 'Applied' AND pp.row_status = 'A') AS pending_applicants
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
                                    <th>Topic</th>
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
                                                <a href="cardiac_talks_quiz_participants.php?quiz_id=<?= $qid ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-users"></i> Participants</a>
                                                <?php if (($row['join_type'] ?? 'open') == 'approval') { ?>
                                                <a href="cardiac_talks_quiz_applicants.php?quiz_id=<?= $qid ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-user-clock"></i> Applicants<?= ((int)($row['pending_applicants'] ?? 0) > 0) ? ' (' . (int)$row['pending_applicants'] . ' pending)' : '' ?></a>
                                                <?php } ?>
                                                <?php if (($row['status'] ?? '') == 'completed') { ?>
                                                <a href="cardiac_talks_quiz_leaderboard.php?quiz_id=<?= $qid ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-trophy"></i> Leader Board</a>
                                                <?php } ?>
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
                                    <td colspan="10" align="center">
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
                                <p class="frm-head">Topic <span class="mandatory">*</span></p>
                                <select name="group_id" id="quiz_group_id" required>
                                    <option value=""> - Select Topic - </option>
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

                            <div class="frm_grp span_3">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="start_time" id="start_time" value="<?= qmdt($qmRow['start_time'] ?? '') ?>" required />
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
                                <p class="frm-head">Marks for Correct Answer</p>
                                <input type="number" name="marks_correct" id="marks_correct" value="<?= (($qmRow['marks_correct'] ?? 0) > 0) ? $qmRow['marks_correct'] : 0 ?>" step="0.1" />
                            </div>
                            <div class="frm_grp span_3">
                                <p class="frm-head">Marks for Incorrect Answer</p>
                                <input type="number" name="marks_incorrect" id="marks_incorrect" value="<?= (($qmRow['marks_incorrect'] ?? 0) > 0) ? $qmRow['marks_incorrect'] : 0 ?>" step="0.1" />
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
                            <div class="frm_grp span_3">
                                <p class="frm-head">Joining Access <span class="mandatory">*</span></p>
                                <select name="join_type" id="join_type" required>
                                    <option value="open" <?= qmsel($qmRow, 'join_type', 'open', true) ?>>Anyone can join</option>
                                    <option value="approval" <?= qmsel($qmRow, 'join_type', 'approval') ?>>Approval needed</option>
                                </select>
                            </div>

                            <div class="frm_grp span_2">
                                <p class="frm-head">Seat Limit <span class="mandatory">*</span></p>
                                <input type="number" name="seat_limit" id="seat_limit" value="<?= qmv($qmRow, 'seat_limit', '0') ?>" required />
                            </div>
                            <div class="frm_grp span_2 approval-only">
                                <p class="frm-head">Application Closing Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="application_close_time" id="application_close_time" value="<?= qmdt($qmRow['application_close_time'] ?? '') ?>" required />
                            </div>
                            <div class="frm_grp span_2 approval-only">
                                <p class="frm-head">Application Status <span class="mandatory">*</span></p>
                                <select name="application_status" id="application_status" required>
                                    <option value="active" <?= qmsel($qmRow, 'application_status', 'active', true) ?>>Active</option>
                                    <option value="closed" <?= qmsel($qmRow, 'application_status', 'closed') ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        // Joining Access = open  ->  hide Application Closing Time / Application Status and make them non-mandatory
        (function () {
            function toggleApplicationFields() {
                var sel = document.getElementById('join_type');
                if (!sel) return;
                var isApproval = sel.value === 'approval';
                var groups = document.querySelectorAll('#frmquiz .approval-only');
                for (var i = 0; i < groups.length; i++) {
                    if (isApproval) groups[i].style.removeProperty('display');
                    else groups[i].style.setProperty('display', 'none', 'important');
                }
                ['application_close_time', 'application_status'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    el.disabled = !isApproval;   // disabled = not validated, not posted
                    el.required = isApproval;
                });
            }
            function init() {
                var sel = document.getElementById('join_type');
                if (sel) {
                    sel.addEventListener('change', toggleApplicationFields);
                    if (window.jQuery) window.jQuery(sel).on('change', toggleApplicationFields);
                }
                toggleApplicationFields();
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
            else init();
        })();
    </script>
    <?php
}

/*==================================================================
   POLL  -  LIST (table) : read / status toggle / publish / reset / remove / multi action
   Tables: rcg_cardiac_talks_polls, rcg_cardiac_talks_poll_options, rcg_cardiac_talks_poll_votes
==================================================================*/
function managePollList($cfg, $mycms)
{
    global $searchArray, $searchString;

    $qmSelf = $_SERVER['PHP_SELF'];

    /* ---------- Search filters ---------- */
    $plSrcTitle = trim($_REQUEST['src_title'] ?? '');
    $plSrcGroup = (int)($_REQUEST['src_group'] ?? 0);
    $plSrcStart = $_REQUEST['start_date'] ?? '';
    $plGroups   = getPublicGroups($mycms);

    $plCon    = "";
    $plParams = [];
    if ($plSrcTitle !== '') {
        $plCon .= " AND p.`title` LIKE ?";
        $plParams[] = ['FILD' => 'title', 'DATA' => '%' . $plSrcTitle . '%', 'TYP' => 's'];
    }
    if ($plSrcGroup > 0) {
        $plCon .= " AND p.`group_id` = ?";
        $plParams[] = ['FILD' => 'group_id', 'DATA' => $plSrcGroup, 'TYP' => 'i'];
    }
    if (!empty($plSrcStart)) {
        $plCon .= " AND DATE(p.`start_time`) >= ?";
        $plParams[] = ['FILD' => 'start_date', 'DATA' => $plSrcStart, 'TYP' => 's'];
    }

    /* ---------- Poll listing (with total votes via subquery) ---------- */
    $sqlPL = [];
    $sqlPL['QUERY'] = "SELECT p.*, g.group_name,
            (SELECT COALESCE(SUM(o.vote_count), 0) FROM `rcg_cardiac_talks_poll_options` o WHERE o.poll_id = p.id) AS total_votes
        FROM `rcg_cardiac_talks_polls` AS p
        LEFT JOIN `rcg_groups` AS g ON p.group_id = g.id
        WHERE p.`is_deleted` = 0
        $plCon
        ORDER BY p.id DESC";
    foreach ($plParams as $pp) { $sqlPL['PARAM'][] = $pp; }
    $plList = $mycms->sql_select_paginated($indexVal, $sqlPL, 15, $restrt);
    $sl     = is_numeric($restrt ?? null) ? (int)$restrt : 0;

    /* ---------- Options (for result rows) ---------- */
    $plOptMap = [];
    if ($plList) {
        $plIds = implode(',', array_map('intval', array_column($plList, 'id')));
        $sqlOL = [];
        $sqlOL['QUERY'] = "SELECT id, poll_id, option_text, vote_count FROM `rcg_cardiac_talks_poll_options` WHERE poll_id IN ($plIds) ORDER BY id ASC";
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

                    <!-- Search / filter -->
                    <form method="get" action="<?= $qmSelf ?>" class="mb-3">
                        <input type="hidden" name="tab" value="poll" />
                        <div class="form_grid g_4">
                            <div class="frm_grp">
                                <p class="frm-head">Poll Title</p>
                                <input type="text" name="src_title" value="<?= htmlspecialchars($plSrcTitle) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Topic</p>
                                <select name="src_group">
                                    <option value="0"> - All Topics - </option>
                                    <?php foreach ($plGroups as $g) { ?>
                                        <option value="<?= (int)$g['id'] ?>" <?= ($plSrcGroup == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['group_name']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Start Date From</p>
                                <input type="date" name="start_date" value="<?= htmlspecialchars($plSrcStart) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">&nbsp;</p>
                                <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                                <a href="<?= $qmSelf ?>?tab=poll" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;text-align:center;"><input name="check_all_poll" id="check_all_poll" type="checkbox" /></th>
                                    <th class="sl">Sl No</th>
                                    <th>Poll Title</th>
                                    <th>Topic</th>
                                    <th>Is Active</th>
                                    <th>Is Published</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Total Votes</th>
                                    <th>Result</th>
                                    <th>Reset</th>
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
                                        $stTs  = !empty($prow['start_time']) ? strtotime($prow['start_time']) : 0;
                                        $enTs  = !empty($prow['end_time']) ? strtotime($prow['end_time']) : 0;
                                ?>
                                <tr>
                                    <td style="text-align:center;"><input type="checkbox" name="poll_selected_ids[]" class="checkBoxPoll" value="<?= $pid ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($prow['title']) ?></td>
                                    <td><?= htmlspecialchars($prow['group_name'] ?? '--') ?></td>
                                    <td style="text-align:center;">
                                        <?php if ($prow['is_active']) { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if ($prow['is_published']) { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Published</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_dark w-max-con text-uppercase">Unpublished</n>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;"><?= $stTs ? date('d-m-Y', $stTs) . '<br>' . date('H:i:s', $stTs) : '--' ?></td>
                                    <td style="text-align:center;"><?= $enTs ? date('d-m-Y', $enTs) . '<br>' . date('H:i:s', $enTs) : '--' ?></td>
                                    <td style="text-align:center;"><?= $total ?></td>
                                    <td>
                                        <?php foreach (($plOptMap[$pid] ?? []) as $o) {
                                            $pct = $total > 0 ? round(($o['vote_count'] / $total) * 100) : 0; ?>
                                            <div><?= htmlspecialchars($o['option_text']) ?> (<?= $pct ?>%)</div>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="cardiac_talks_poll.process.php?act=reset_poll&poll_id=<?= $pid ?><?= $searchString ?>" class="badge_dark" onclick="return confirm('Reset all votes of this poll?');">Reset Poll</a>
                                    </td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
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
                                                <a href="javascript:void(0);" class="deletePollBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="cardiac_talks_poll.process.php?act=Remove&id=<?= $pid ?><?= $searchString ?>">
                                                    <i class="fal fa-trash"></i> Delete Poll
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

    /* ---------- Public groups (shown as Topic) ---------- */
    $plGroups = getPublicGroups($mycms);

    /* ---------- Poll edit mode: row + options ---------- */
    $plRow     = [];
    $plOptions = [];
    if ($plEditId !== '') {
        $sqlP = [];
        $sqlP['QUERY']   = "SELECT * FROM `rcg_cardiac_talks_polls` WHERE `id` = ? AND `is_deleted` = 0";
        $sqlP['PARAM'][] = ['FILD' => 'id', 'DATA' => (int)$plEditId, 'TYP' => 'i'];
        $resP  = $mycms->sql_select($sqlP);
        $plRow = $resP ? $resP[0] : [];

        if (!empty($plRow)) {
            $sqlO = [];
            $sqlO['QUERY']   = "SELECT id, option_text FROM `rcg_cardiac_talks_poll_options` WHERE `poll_id` = ? ORDER BY id ASC";
            $sqlO['PARAM'][] = ['FILD' => 'poll_id', 'DATA' => (int)$plEditId, 'TYP' => 'i'];
            $plOptions = $mycms->sql_select($sqlO) ?: [];
        }
    }
    $plIsEdit = !empty($plRow);
    if (!$plIsEdit || empty($plOptions)) {
        $plOptions = [['id' => '', 'option_text' => ''], ['id' => '', 'option_text' => '']];
    }
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
                                <p class="frm-head">Topic <span class="mandatory">*</span></p>
                                <select name="group_id" id="poll_group_id" required>
                                    <option value=""> - Select Topic - </option>
                                    <?php foreach ($plGroups as $g) { ?>
                                        <option value="<?= (int)$g['id'] ?>" <?= (($plRow['group_id'] ?? '') == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['group_name']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="frm_grp span_2">
                                <p class="frm-head">Publish <span class="mandatory">*</span></p>
                                <select name="is_published" id="is_published" required>
                                    <option value="0" <?= ($plIsEdit && (int)$plRow['is_published'] === 0) ? 'selected' : '' ?>>Unpublished</option>
                                    <option value="1" <?= (!$plIsEdit || (int)$plRow['is_published'] === 1) ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="start_time" id="poll_start_time" value="<?= $plStartVal ?>" required />
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">End Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="end_time" id="poll_end_time" value="<?= $plEndVal ?>" required />
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
   DISCUSSION helpers
==================================================================*/
/* Full URL of an uploaded discussion file.
   Stored value may be just a file name, a relative path or a full URL. */
function discMediaUrl($url)
{
    global $discMediaBase;
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $url)) {
        return $url;                                   // already absolute
    }
    $url = ltrim($url, '/');
    if (strpos($url, 'App/api/uploads/') === 0) {
        return $discMediaBase . $url;                  // already has the folder
    }
    return $discMediaBase . 'App/api/uploads/discussion/posts/' . rawurlencode(basename($url));
}

/* Is this media row a video? (by media_type, falling back to file extension) */
function discIsVideo($m)
{
    $type = strtolower((string)($m['media_type'] ?? ''));
    if (strpos($type, 'video') !== false) {
        return true;
    }
    if (strpos($type, 'image') !== false || strpos($type, 'photo') !== false || strpos($type, 'sticker') !== false || strpos($type, 'gif') !== false) {
        return false;
    }
    $path = strtolower((string)parse_url((string)($m['media_url'] ?? ''), PHP_URL_PATH));
    return (bool)preg_match('/\.(mp4|mov|webm|m4v|3gp|mkv|avi)$/', $path);
}

/* HTML placed inside the modal for one post: full content + all media */
function discRenderModalHtml($content, $mediaList)
{
    $html  = '<div class="disc_modal_content">' . ($content !== '' ? nl2br(htmlspecialchars($content)) : '<span class="mandatory">No text content.</span>') . '</div>';

    if (!$mediaList) {
        return $html . '<span class="mandatory">No media attached to this post.</span>';
    }

    $html .= '<div class="disc_media_grid">';
    foreach ($mediaList as $m) {
        $url = discMediaUrl($m['media_url'] ?? '');
        $html .= '<div class="disc_media_item">';
        if ($url === '') {
            $html .= '<div class="disc_media_cap">' . htmlspecialchars($m['media_type'] ?? 'media')
                   . (!empty($m['sticker_id']) ? ' #' . (int)$m['sticker_id'] : '') . ' - no file</div>';
        } elseif (discIsVideo($m)) {
            $html .= '<video controls preload="none" src="' . htmlspecialchars($url) . '"></video>'
                   . '<div class="disc_media_cap">Video &middot; <a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener">Open</a></div>';
        } else {
            $html .= '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener"><img src="' . htmlspecialchars($url) . '" alt="media" loading="lazy" /></a>'
                   . '<div class="disc_media_cap">' . htmlspecialchars($m['media_type'] ?? 'Image') . '</div>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

/*==================================================================
   DISCUSSION  (post list - status change + media modal)
==================================================================*/
function manageDiscussion($cfg, $mycms)
{
    /* Base URL that contains the "App" folder (site root of this project).
       Change this one line if your base URL is stored elsewhere, e.g. $cfg['BASE_URL']. */
    global $discMediaBase;
    $discScheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $discMediaBase = rtrim($cfg['BASE_URL'] ?? ($discScheme . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF']))), '/') . '/';

    $qmSelf   = $_SERVER['PHP_SELF'];
    $qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
    $fGroup   = (int)($_REQUEST['f_group'] ?? 0);
    $fStatus  = $_REQUEST['f_status'] ?? '';
    $fSearch  = trim($_REQUEST['f_search'] ?? '');

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

    /* ---------- Post listing (live like / comment counts) ---------- */
    $sql = [];
    $sql['QUERY'] = "SELECT p.*, g.group_name, u.user_full_name,
            (SELECT COUNT(*) FROM `rcg_discussion_post_likes` pl WHERE pl.post_id = p.id AND pl.status = 'A') AS live_like_count,
            (SELECT COUNT(*) FROM `rcg_discussion_comments` cm WHERE cm.post_id = p.id AND cm.status = 'A') AS live_comment_count
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

    /* ---------- Media of the posts on this page (images / videos) ---------- */
    $dMediaMap = [];
    if ($dList) {
        $dIds = implode(',', array_map('intval', array_column($dList, 'id')));
        $sqlMM = [];
        $sqlMM['QUERY'] = "SELECT id, post_id, media_type, media_url, sticker_id
            FROM `rcg_discussion_post_media`
            WHERE post_id IN ($dIds) AND status = 'A'
            ORDER BY sort_order ASC, id ASC";
        $resMM = $mycms->sql_select($sqlMM);
        if ($resMM) {
            foreach ($resMM as $mm) { $dMediaMap[(int)$mm['post_id']][] = $mm; }
        }
    }

    // Return URL (keeps filters + page). Always starts with "<this page>.php?"
    $back = basename($qmSelf) . '?tab=discussion&f_group=' . $fGroup . '&f_status=' . urlencode($fStatus)
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
                                <p class="frm-head">Topic</p>
                                <select name="f_group">
                                    <option value="0"> - All Topics - </option>
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
                                    <th>Topic</th>
                                    <th>Post</th>
                                    <th>Media</th>
                                    <th>Likes</th>
                                    <th>Comments</th>
                                    <th>Status</th>
                                    <th>Posted On</th>
                                    <th class="action text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dHidden = ''; // hidden modal sources, printed after the table
                                if ($dList) {
                                    foreach ($dList as $drow) {
                                        $sl++;
                                        $did     = (int)$drow['id'];
                                        $dActive = ($drow['status'] == 'A');
                                        $dMedia  = $dMediaMap[$did] ?? [];

                                        $imgCnt = 0; $vidCnt = 0;
                                        foreach ($dMedia as $mm) {
                                            if (empty($mm['media_url'])) { continue; }
                                            if (discIsVideo($mm)) { $vidCnt++; } else { $imgCnt++; }
                                        }

                                        $dHidden .= '<div id="discMediaSrc_' . $did . '" style="display:none;">'
                                                  . discRenderModalHtml((string)($drow['content'] ?? ''), $dMedia)
                                                  . '</div>';
                                ?>
                                <tr>
                                    <td style="text-align:center;"><input type="checkbox" name="disc_selected_ids[]" class="checkBoxDisc" value="<?= $did ?>" /></td>
                                    <td class="sl"><?= $sl ?></td>
                                    <td><?= htmlspecialchars($drow['user_full_name'] ?? ('User #' . (int)$drow['user_id'])) ?></td>
                                    <td><?= htmlspecialchars($drow['group_name'] ?? '--') ?></td>
                                    <td><?= htmlspecialchars(mb_strimwidth((string)($drow['content'] ?? ''), 0, 90, '...')) ?></td>
                                    <td style="text-align:center;" class="disc_counts">
                                        <?php if ($imgCnt + $vidCnt > 0) { ?>
                                            <?php if ($imgCnt > 0) { ?><span><i class="fal fa-image"></i> <?= $imgCnt ?></span><?php } ?>
                                            <?php if ($vidCnt > 0) { ?><span><i class="fal fa-video"></i> <?= $vidCnt ?></span><?php } ?>
                                        <?php } else { ?>--<?php } ?>
                                    </td>
                                    <td style="text-align:center;"><?= (int)$drow['live_like_count'] ?></td>
                                    <td style="text-align:center;"><?= (int)$drow['live_comment_count'] ?></td>
                                    <td style="text-align:center;">
                                        <?php if ($dActive) { ?>
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
                                                <a href="javascript:void(0);" class="viewDiscMediaBtn icon_hover badge_secondary action-transparent" data-post-id="<?= $did ?>"><i class="fal fa-photo-video"></i> View Media</a>
                                                <?php if ($dActive) { ?>
                                                <a href="cardiac_talks_discussion.process.php?act=PostInactive&id=<?= $did ?>&back=<?= urlencode($back) ?>" class="icon_hover badge_dark action-transparent"><i class="fal fa-ban"></i> Make Inactive</a>
                                                <?php } else { ?>
                                                <a href="cardiac_talks_discussion.process.php?act=PostActive&id=<?= $did ?>&back=<?= urlencode($back) ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-check"></i> Make Active</a>
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
                            <select name="discMultiOperationSelector" id="discMultiOperationSelector">
                                <option value="">Choose an action...</option>
                                <option value="Active">Make Active</option>
                                <option value="Inactive">Make Inactive</option>
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

    <!-- Hidden per-post content + media (copied into the modal on click) -->
    <?= $dHidden ?>

    <!-- ============ MEDIA MODAL ============ -->
    <div class="modal fade" id="discMediaModal" tabindex="-1" aria-labelledby="discMediaModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discMediaModalTitle">Post Media</h5>
                    <button type="button" class="disc_close_btn discCloseBtn" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body" id="discMediaModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="badge_dark discCloseBtn"><i class="fal fa-times"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>