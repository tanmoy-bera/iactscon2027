<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

/* ---------- Request params ---------- */
$indexVal = null; // same pagination key behaviour as before (_pgn_)
$qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
$quiz_id  = (int)($_REQUEST['quiz_id'] ?? 0);
$keyword  = trim($_REQUEST['keyword'] ?? '');

$backUrl  = $cfg['SECTION_BASE_URL'] . 'manage_quiz_polls.php';

$quizTitle = '';
$result    = [];
$guests    = [];

if ($quiz_id > 0) {

    /* ---------- Quiz title for the heading ---------- */
    $quizSql = [];
    $quizSql['QUERY']   = "SELECT title FROM rcg_quizzes WHERE id = ?";
    $quizSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i'];
    $quizRow   = $mycms->sql_select($quizSql);
    $quizTitle = $quizRow[0]['title'] ?? '';

    /* ---------- Search condition ---------- */
    $con    = "";
    $params = [];

    if ($keyword !== '') {
        $con .= " AND (u.user_full_name LIKE ? OR m.email LIKE ? OR u.user_email_id LIKE ? OR m.mobile LIKE ? OR u.user_mobile_no LIKE ? OR p.group_name LIKE ?)";
        $kw = '%' . $keyword . '%';
        $params[] = ['FILD' => 'user_full_name', 'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'm_email',        'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'user_email_id',  'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'm_mobile',       'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'user_mobile_no', 'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'group_name',     'DATA' => $kw, 'TYP' => 's'];
    }

    /* ---------- Registered participants (solo + group members) ----------
       Prefer email/mobile captured at join-time on the members table;
       fall back to the live rcg_user_registration record if those are empty. */
    $sql = [];
    $sql['QUERY'] = "SELECT 
            p.participation_type,
            p.group_name,
            p.created_at AS joined_at,
            u.user_full_name,
            COALESCE(NULLIF(m.email, ''), u.user_email_id)   AS display_email,
            COALESCE(NULLIF(m.mobile, ''), u.user_mobile_no) AS display_mobile
        FROM quiz_participation p
        INNER JOIN rcg_quiz_participation_members m ON m.participation_id = p.id
        LEFT JOIN rcg_user_registration u ON u.id = m.user_id
        WHERE p.quiz_id = ?
        AND m.status = 'Active'
        $con
        ORDER BY p.created_at DESC";

    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i'];
    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);

    /* ---------- Guest participants (separate, not paginated) ---------- */
    $guestSql = [];
    $guestSql['QUERY'] = "SELECT device_id, guest_name, latest_visit_time AS joined_at
        FROM quiz_guest_users
        WHERE latest_visit_quiz_id = ?
        ORDER BY latest_visit_time DESC";
    $guestSql['PARAM'][] = ['FILD' => 'latest_visit_quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i'];
    $guests = $mycms->sql_select($guestSql);
}
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Participants</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <style>
        /* all column headings centered */
        .table_wrap table thead th { text-align: center !important; }
    </style>

    <div class="body_wrap">

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $backUrl ?>">Manage Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Participants</a></li>
                    </ol>
                </nav>
                <h2>Participants<?= $quizTitle !== '' ? ' — ' . htmlspecialchars($quizTitle) : '' ?></h2>
            </div>
            <div class="page_top_wrap_right">
                <a href="<?= $backUrl ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">

                <?php if ($quiz_id <= 0) { ?>

                    <span class="mandatory">Quiz ID is required.</span>

                <?php } else { ?>

                <!-- ==================== REGISTERED PARTICIPANTS ==================== -->
                <div class="com_info_box active" id="participantListing">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><i class="fal fa-users"></i></span> Participant Listing</n>
                            </h5>
                            <div class="com_info_box_inner">

                                <!-- Search -->
                                <form name="frmParticipantListing" id="frmParticipantListing" action="<?= $cfg['SECTION_BASE_URL'] ?>participants.php" method="get" class="mb-3">
                                    <input type="hidden" name="quiz_id" id="quiz_id" value="<?= $quiz_id ?>" />
                                    <div class="form_grid g_3">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Search by Name, Email, Mobile or Group</p>
                                            <input type="text" name="keyword" id="keyword" value="<?= htmlspecialchars($keyword) ?>" />
                                        </div>
                                        <div class="frm_grp">
                                            <p class="frm-head">&nbsp;</p>
                                            <button type="submit" name="searchBtn" id="searchBtn" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                                            <?php if ($keyword !== '') { ?>
                                                <a href="<?= $cfg['SECTION_BASE_URL'] ?>participants.php?quiz_id=<?= $quiz_id ?>" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </form>

                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">Sl. No.</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Group</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Joined At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $counter = 0;
                                            if ($result) {
                                                foreach ($result as $row) {
                                                    $counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $counter + ($qmPg * 20) ?></td>
                                                <td><?= htmlspecialchars($row['user_full_name'] ?? '') ?></td>
                                                <td style="text-align:center;"><n class="badge_padding badge_secondary w-max-con text-uppercase"><?= htmlspecialchars($row['participation_type'] ?? '') ?></n></td>
                                                <td><?= htmlspecialchars($row['group_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['display_email'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['display_mobile'] ?? '') ?></td>
                                                <td style="text-align:center;"><?= !empty($row['joined_at']) ? date('d M Y, H:i', strtotime($row['joined_at'])) : '--' ?></td>
                                            </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="7" align="center"><span class="mandatory">No Record(s) Found !!</span></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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

                <!-- ==================== GUEST PARTICIPANTS ==================== -->
                <?php if (!empty($guests)) { ?>
                <div class="com_info_box active" id="guestListing">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><i class="fal fa-user"></i></span> Guest Participants</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <p class="mb-3" style="opacity:.7;">Guests who joined this quiz without registering.</p>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">Sl. No.</th>
                                                <th>Guest Name</th>
                                                <th>Joined At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $gi = 0;
                                            foreach ($guests as $g) {
                                                $gi++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $gi ?></td>
                                                <td><?= htmlspecialchars($g['guest_name'] ?: 'Guest') ?></td>
                                                <td style="text-align:center;"><?= !empty($g['joined_at']) ? date('d M Y, H:i', strtotime($g['joined_at'])) : '--' ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php } ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
</html>