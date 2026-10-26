<?php
include_once("../includes/source.php");
include_once('../includes/init.php');
include_once('../includes/function.workshop.php');
?>

<body>
    <?php include_once("../includes/left-menu.php"); ?>
    <header>
        <h2>Participants</h2>
        <?php include_once("../includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]  = $pageKeyVal;
        $searchArray['quiz_id'] = $_REQUEST['quiz_id'] ?? '';
        $searchArray['keyword'] = $_REQUEST['keyword'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . $searchVal;
        }
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Participants</a></li>
                    </ol>
                </nav>
                <h2>Participants</h2>
                <h6>View registered and guest participants for a quiz.</h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="manage_quiz.php" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php showAllParticipants($cfg, $mycms); ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("../includes/js-source.php"); ?>
</html>
<?php

/*============== SHOW ALL PARTICIPANTS ================*/
function showAllParticipants($cfg, $mycms)
{
    global $searchArray, $searchString;

    if (empty($_REQUEST['quiz_id'])) {
        ?>
        <div class="com_info_box active">
            <div class="com_info_box_inner" style="padding:15px;">
                <span class="mandatory">Quiz ID is required.</span>
                <p><a href="manage_quiz.php" class="badge_dark">&laquo; Back</a></p>
            </div>
        </div>
        <?php
        return;
    }

    $quiz_id = (int)$_REQUEST['quiz_id'];

    // Get quiz title for the heading
    $quizSql = [];
    $quizSql['QUERY'] = "SELECT title FROM rcg_quizzes WHERE id = ?";
    $quizSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i'];
    $quizRow   = $mycms->sql_select($quizSql);
    $quizTitle = $quizRow[0]['title'] ?? '';

    $con    = "";
    $params = [];

    if (!empty($_REQUEST['keyword'])) {
        $con .= " AND (u.user_full_name LIKE ? OR m.email LIKE ? OR u.user_email_id LIKE ? OR m.mobile LIKE ? OR u.user_mobile_no LIKE ? OR p.group_name LIKE ?)";
        $kw = '%' . $_REQUEST['keyword'] . '%';
        $params[] = ['FILD' => 'user_full_name', 'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'm_email',        'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'user_email_id',  'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'm_mobile',       'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'user_mobile_no', 'DATA' => $kw, 'TYP' => 's'];
        $params[] = ['FILD' => 'group_name',     'DATA' => $kw, 'TYP' => 's'];
    }

    // Registered participants (solo + group members)
    // Prefer email/mobile captured at join-time on the members table;
    // fall back to the live rcg_user_registration record if those are empty.
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
    ?>
    <form name="frmParticipantListing" id="frmParticipantListing" action="participants.php" method="get">
        <input type="hidden" name="quiz_id" id="quiz_id" value="<?= $quiz_id ?>" />
        <input type="hidden" id="excelPrintVal" name="excelPrintVal" />

        <div class="com_info_box active" id="participantListing">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-users"></i></span> Participants — <?= htmlspecialchars($quizTitle) ?></n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3 mb-3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Name / Email / Mobile</p>
                                <input type="text" name="keyword" id="keyword" value="<?= htmlspecialchars($_REQUEST['keyword'] ?? '') ?>" />
                            </div>
                            <div class="frm_grp" style="align-self:flex-end;">
                                <button type="submit" name="searchBtn" id="searchBtn" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                            </div>
                        </div>

                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sl">#</th>
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
                                    if ($result) {
                                        $i = 0;
                                        foreach ($result as $row) {
                                            $i++;
                                    ?>
                                    <tr>
                                        <td class="sl"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>
                                        <td><?= htmlspecialchars($row['user_full_name'] ?? '') ?></td>
                                        <td><?= ucfirst($row['participation_type']) ?></td>
                                        <td><?= htmlspecialchars($row['group_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['display_email'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['display_mobile'] ?? '') ?></td>
                                        <td><?php echo !empty($row['joined_at']) ? date('d-m-Y H:i:s', strtotime($row['joined_at'])) : ''; ?></td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" align="center">
                                            <span class="mandatory">No Record.</span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="paginationDisplay">
                            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                        </div>
                    </div>
                </div>

                <?php
                // Guest participants (shown separately, not paginated with the block above)
                $guestSql = [];
                $guestSql['QUERY'] = "SELECT device_id, guest_name, latest_visit_time AS joined_at
                    FROM quiz_guest_users
                    WHERE latest_visit_quiz_id = ?
                    ORDER BY latest_visit_time DESC";
                $guestSql['PARAM'][] = ['FILD' => 'latest_visit_quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i'];
                $guests = $mycms->sql_select($guestSql);

                if (!empty($guests)) {
                ?>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_secondary"><i class="fal fa-user-friends"></i></span> Guest Participants</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sl">#</th>
                                        <th>Guest Name</th>
                                        <th>Joined At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $gi = 0;
                                    foreach ($guests as $g) {
                                        $gi++; ?>
                                    <tr>
                                        <td class="sl"><?= $gi ?></td>
                                        <td><?= htmlspecialchars($g['guest_name'] ?: 'Guest') ?></td>
                                        <td><?php echo !empty($g['joined_at']) ? date('d-m-Y H:i:s', strtotime($g['joined_at'])) : ''; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </form>
    <?php
}
?>