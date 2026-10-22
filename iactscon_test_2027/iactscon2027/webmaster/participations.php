<?php
include_once('includes/init.php');

/* ---------- Request params ---------- */
$indexVal = null; // same pagination key behaviour as before (_pgn_)
$qmPg     = (int)($_REQUEST['_pgn_'] ?? 0);
$quiz_id  = (int)($_REQUEST['quiz_id'] ?? 0);
$keyword  = trim($_REQUEST['keyword'] ?? '');

$backUrl  = $cfg['SECTION_BASE_URL'] . 'manage_quiz.php';

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Participants</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* =====================================================
           SELF-CONTAINED BLACK THEME - no external CSS/JS needed
           Change colors from the variables below
           ===================================================== */
        :root {
            --bg:        #000000;
            --panel:     #0d0d0d;
            --card:      #161616;
            --card-hover:#1f1f1f;
            --border:    #2a2a2a;
            --text:      #f3f4f6;
            --muted:     #9ca3af;
            --accent:    #6366f1;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            background: var(--bg); color: var(--text); color-scheme: dark;
            font-family: 'Inter', -apple-system, 'Segoe UI', Arial, sans-serif; font-size: 14px;
        }
        a { color: inherit; text-decoration: none; }
        .page { max-width: 1280px; margin: 0 auto; padding: 24px 28px 40px; }

        /* ---------- Top bar ---------- */
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 22px; }
        .topbar-left { display: flex; align-items: center; gap: 14px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 9px 14px; border-radius: 8px;
            background: var(--card); color: var(--text); border: 1px solid var(--border);
            font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer;
        }
        .btn:hover { background: var(--card-hover); }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { filter: brightness(1.1); background: var(--accent); }
        .title h1 { margin: 0; font-size: 22px; font-weight: 700; color: #fff; }
        .title p  { margin: 4px 0 0; font-size: 13px; color: var(--muted); }

        /* ---------- Search ---------- */
        .search-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
        .search-input {
            flex: 1; min-width: 240px; display: flex; align-items: center; gap: 10px; padding: 0 14px;
            background: var(--card); border: 1px solid var(--border); border-radius: 8px;
        }
        .search-input i { color: var(--muted); }
        .search-input input {
            flex: 1; padding: 11px 0; background: transparent; border: none; outline: none;
            color: var(--text); font-family: inherit; font-size: 14px;
        }
        .search-input input::placeholder { color: #6b7280; }
        .search-input:focus-within { border-color: var(--accent); }

        /* ---------- Tables ---------- */
        .section-title { margin: 30px 0 4px; font-size: 17px; font-weight: 600; color: #fff; }
        .section-sub   { margin: 0 0 12px; font-size: 13px; color: var(--muted); }
        .table-wrap { overflow-x: auto; background: var(--panel); border: 1px solid var(--border); border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 12px 14px; text-align: left; white-space: nowrap;
            background: var(--card); color: var(--muted); border-bottom: 1px solid var(--border);
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px;
        }
        tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; color: var(--text); vertical-align: top; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr.tlisting:hover { background: var(--card-hover); }
        .c { text-align: center !important; }
        .sl { width: 60px; }
        .empty { padding: 28px 14px !important; text-align: center; color: #f87171; }
        .tag {
            display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 500;
            background: var(--card); border: 1px solid var(--border); color: #c4b5fd;
        }

        /* ---------- Pagination ---------- */
        .pagination-wrap {
            display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;
            margin-top: 14px; color: var(--muted); font-size: 13px;
        }
        .pagination-wrap a, .pagination-wrap b, .pagination-wrap strong, .pagination-wrap .current, .pagination-wrap .active {
            display: inline-block; min-width: 30px; padding: 5px 9px; margin: 0 2px; text-align: center;
            border-radius: 6px; background: var(--card); border: 1px solid var(--border); color: var(--text);
        }
        .pagination-wrap a:hover { background: var(--card-hover); }
        .pagination-wrap b, .pagination-wrap strong, .pagination-wrap .current, .pagination-wrap .active {
            background: var(--accent); border-color: var(--accent); color: #fff;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }

        @media (max-width: 700px) { .page { padding: 16px; } }
    </style>
</head>
<body>
<div class="page">

    <?php if ($quiz_id <= 0) { ?>

        <div class="topbar">
            <div class="topbar-left">
                <a href="<?= $backUrl ?>" class="btn"><i class="fas fa-arrow-left"></i> Back</a>
                <div class="title"><h1>Participants</h1></div>
            </div>
        </div>
        <div class="table-wrap">
            <table><tbody><tr><td class="empty">Quiz ID is required.</td></tr></tbody></table>
        </div>

    <?php } else { ?>

        <div class="topbar">
            <div class="topbar-left">
                <a href="<?= $backUrl ?>" class="btn"><i class="fas fa-arrow-left"></i> Back</a>
                <div class="title">
                    <h1>Participants</h1>
                    <p><?= $quizTitle !== '' ? htmlspecialchars($quizTitle) . ' &mdash; ' : '' ?>Everyone who has joined this quiz, including group members and guests.</p>
                </div>
            </div>
        </div>

        <!-- ==================== SEARCH ==================== -->
        <form name="frmParticipantListing" id="frmParticipantListing" action="<?= $cfg['SECTION_BASE_URL'] ?>participants.php" method="get">
            <input type="hidden" name="quiz_id" id="quiz_id" value="<?= $quiz_id ?>" />
            <div class="search-bar">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" id="keyword" placeholder="Search by Name, Email, Mobile or Group..."
                           value="<?= htmlspecialchars($keyword) ?>" />
                </div>
                <button type="submit" name="searchBtn" id="searchBtn" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                <?php if ($keyword !== '') { ?>
                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>participants.php?quiz_id=<?= $quiz_id ?>" class="btn"><i class="fas fa-redo"></i> Reset</a>
                <?php } ?>
            </div>
        </form>

        <!-- ==================== REGISTERED PARTICIPANTS ==================== -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl c">Sl No</th>
                        <th>Name</th>
                        <th class="c">Type</th>
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
                            <tr class="tlisting">
                                <td class="c"><?= $counter + ($qmPg * 20) ?></td>
                                <td><?= htmlspecialchars($row['user_full_name'] ?? '') ?></td>
                                <td class="c"><span class="tag"><?= htmlspecialchars(ucfirst($row['participation_type'] ?? '')) ?></span></td>
                                <td><?= htmlspecialchars($row['group_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['display_email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['display_mobile'] ?? '') ?></td>
                                <td><?= !empty($row['joined_at']) ? date('d M Y, H:i', strtotime($row['joined_at'])) : '--' ?></td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="7" class="empty">No Record(s) Found !!</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
        </div>

        <!-- ==================== GUEST PARTICIPANTS ==================== -->
        <?php if (!empty($guests)) { ?>
            <h2 class="section-title">Guest Participants</h2>
            <p class="section-sub">Guests who joined this quiz without registering.</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th class="sl c">Sl No</th>
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
                            <tr class="tlisting">
                                <td class="c"><?= $gi ?></td>
                                <td><?= htmlspecialchars($g['guest_name'] ?: 'Guest') ?></td>
                                <td><?= !empty($g['joined_at']) ? date('d M Y, H:i', strtotime($g['joined_at'])) : '--' ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

    <?php } ?>

</div>
</body>
</html>