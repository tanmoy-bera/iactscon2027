<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/fcm-helper.php');


$sessionId = (int)($_REQUEST['session_id'] ?? 0);
$view      = $_REQUEST['view'] ?? 'all';   // all | live | hidden | spam
if (!in_array($view, array('all', 'live', 'hidden', 'spam'), true)) $view = 'all';

$qaMediaBase = QA_API_BASE;

function sdUrl($over = array())
{
    $base = array('session_id' => $_REQUEST['session_id'] ?? '', 'view' => $_REQUEST['view'] ?? 'all');
    return $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($base, $over));
}

// Moderation holei CI-ke signal pathay, jate Firebase-e event jay
function qaPing($type, $id)
{
    if (!function_exists('curl_init')) return;
    $ch = curl_init(QA_API_BASE . 'v1/qa/ping');
    curl_setopt_array($ch, array(
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(array('type' => $type, 'id' => (int)$id)),
        CURLOPT_HTTPHEADER     => array('Content-Type: application/json', 'X-Internal-Key: ' . QA_INTERNAL_KEY),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_TIMEOUT        => 4,
    ));
    curl_exec($ch);
    curl_close($ch);
}

// ---------- Moderation handler (Hide / Spam / Restore) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mod_action'])) {
    $type   = (($_POST['mod_type'] ?? '') === 'A') ? 'A' : 'Q';
    $id     = (int)($_POST['mod_id'] ?? 0);
    $act    = $_POST['mod_action'];
    $reason = trim($_POST['mod_reason'] ?? '');
    $map    = array('hide' => 'I', 'spam' => 'S', 'restore' => 'A');

    if ($id && isset($map[$act])) {
        $table     = ($type === 'A') ? 'rcg_session_question_answers' : 'rcg_session_questions';
        $newStatus = $map[$act];

        if ($act === 'restore') {
            $reason = '';
        } elseif ($act === 'spam' && $reason === '') {
            $reason = 'Spam / inappropriate content';
        }
        $reason = mb_substr($reason, 0, 255);

        $u = array();
        $u['QUERY'] = "UPDATE $table
                          SET status = ?, spam_reason = ?, moderated_at = NOW()
                        WHERE id = ? AND status != 'D'";
        $u['PARAM'][] = array('FILD' => 'status',      'DATA' => $newStatus, 'TYP' => 's');
        $u['PARAM'][] = array('FILD' => 'spam_reason', 'DATA' => $reason,    'TYP' => 's');
        $u['PARAM'][] = array('FILD' => 'id',          'DATA' => $id,        'TYP' => 'i');
        $mycms->sql_update($u);

        if ($type === 'A') {
            $r = $mycms->sql_select(array('QUERY' =>
                "SELECT question_id FROM rcg_session_question_answers WHERE id = " . $id));
            if ($r) {
                $qid = (int)$r[0]['question_id'];
                $c = array();
                $c['QUERY'] = "UPDATE rcg_session_questions qq
                    SET answer_count = (SELECT COUNT(*) FROM rcg_session_question_answers
                                         WHERE question_id = qq.id AND status = 'A'),
                        is_answered  = IF(EXISTS(SELECT 1 FROM rcg_session_question_answers
                                         WHERE question_id = qq.id AND status = 'A' AND is_faculty = 'Y'),'Y','N')
                    WHERE qq.id = " . $qid;
                $mycms->sql_update($c);
            }
        }

        // Realtime signal (fail korle-o page atkabe na)
        qaPing($type, $id);
    }

    echo '<script>window.location.href=' . json_encode(sdUrl()) . ';</script>';
    exit;
}
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Live Q&amp;A</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Live Q&amp;A</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Session Discussion</a></li>
                    </ol>
                </nav>
                <h2>Session Discussion</h2>
                <h6>Questions and answers posted by delegates, session-wise.</h6>
            </div>
        </div>
        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php showSessionDiscussion($mycms, $sessionId, $view, $qaMediaBase); ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
</html>
<?php

function showSessionDiscussion($mycms, $sessionId, $view, $qaMediaBase = '')
{
    $indexVal = null;
    $restrt   = 0;

    // Status filter (constant value, safe to embed)
    $qStatus = "q.status != 'D'";
    $aStatus = "a.status != 'D'";
    if ($view == 'live') {
        $qStatus = "q.status = 'A'"; $aStatus = "a.status = 'A'";
    } elseif ($view == 'hidden') {
        $qStatus = "q.status = 'I'"; $aStatus = "a.status = 'I'";
    } elseif ($view == 'spam') {
        $qStatus = "q.status = 'S'"; $aStatus = "a.status = 'S'";
    }

    // Session filter ($sessionId already (int) cast, so safe)
    $qSession = $sessionId ? " AND q.session_id = " . (int)$sessionId : "";

    $sql = array();
    $sql['QUERY'] = "
        SELECT * FROM (
            SELECT 'Q' AS row_type, q.id AS id, q.id AS thread_id, q.session_id,
                   q.user_id, q.question_text AS message, q.image_path AS media_path,
                   'image' AS media_type, q.is_anonymous, 'N' AS is_faculty,
                   q.upvote_count, q.answer_count, q.is_answered,
                   q.status, q.spam_reason,
                   ((SELECT COUNT(*) FROM rcg_session_questions z1 WHERE z1.user_id = q.user_id AND z1.status = 'S')
                    + (SELECT COUNT(*) FROM rcg_session_question_answers z2 WHERE z2.user_id = q.user_id AND z2.status = 'S')) AS user_spam_count,
                   q.created_dateTime AS created_at,
                   s.session_title, u.user_full_name, u.user_registration_id,
                   (1 + (SELECT COUNT(DISTINCT a2.user_id)
                           FROM rcg_session_question_answers a2
                          WHERE a2.question_id = q.id AND a2.status != 'D'
                            AND a2.user_id != q.user_id)) AS thread_users,
                   0 AS type_order
            FROM rcg_session_questions q
            LEFT JOIN rcg_program_schedule_session s ON s.id = q.session_id
            LEFT JOIN " . _DB_USER_REGISTRATION_ . " u ON u.id = q.user_id
            WHERE $qStatus $qSession

            UNION ALL

            SELECT 'A' AS row_type, a.id AS id, a.question_id AS thread_id, q.session_id,
                   a.user_id, a.answer_text AS message, a.media_path AS media_path,
                   a.media_type, 'N' AS is_anonymous, a.is_faculty,
                   0 AS upvote_count, 0 AS answer_count, 'N' AS is_answered,
                   a.status, a.spam_reason,
                   ((SELECT COUNT(*) FROM rcg_session_questions z1 WHERE z1.user_id = a.user_id AND z1.status = 'S')
                    + (SELECT COUNT(*) FROM rcg_session_question_answers z2 WHERE z2.user_id = a.user_id AND z2.status = 'S')) AS user_spam_count,
                   a.created_dateTime AS created_at,
                   s.session_title, u.user_full_name, u.user_registration_id,
                   0 AS thread_users,
                   1 AS type_order
            FROM rcg_session_question_answers a
            INNER JOIN rcg_session_questions q ON q.id = a.question_id AND q.status != 'D'
            LEFT JOIN rcg_program_schedule_session s ON s.id = q.session_id
            LEFT JOIN " . _DB_USER_REGISTRATION_ . " u ON u.id = a.user_id
            WHERE $aStatus $qSession
        ) t
        ORDER BY t.session_id ASC, t.thread_id DESC, t.type_order ASC, t.id ASC
    ";
    $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);

    $ssql = array();
    $ssql['QUERY'] = "SELECT id, session_title FROM rcg_program_schedule_session WHERE status='A' ORDER BY session_title ASC";
    $sessions = $mycms->sql_select($ssql);
    ?>
    <div class="com_info_box active">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-comments"></i></span> Discussion</n>
                    <div class="page_top_wrap_right">
                        <?php
                        foreach (array('all' => 'All', 'live' => 'Live', 'hidden' => 'Hidden', 'spam' => 'Spam') as $k => $label) {
                            $cls = ($view == $k) ? 'badge_primary' : 'badge_dark';
                            echo '<a href="' . sdUrl(array('view' => $k)) . '" class="' . $cls . '">' . $label . '</a> ';
                        }
                        ?>
                    </div>
                </h5>
                <div class="com_info_box_inner">
                    <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="mb-3">
                        <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>" />
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Session</p>
                                <select name="session_id" onchange="this.form.submit()">
                                    <option value="">-- All Sessions --</option>
                                    <?php
                                    if ($sessions) {
                                        foreach ($sessions as $s) {
                                            $sel = ($sessionId == $s['id']) ? ' selected' : '';
                                            echo '<option value="' . $s['id'] . '"' . $sel . '>' . htmlspecialchars($s['session_title']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th class="sl">#</th>
                                    <th>Session</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>By</th>
                                    <th>Posted</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result) {
                                    $i = 0;
                                    foreach ($result as $row) {
                                        $i++;
                                        $isReply = ($row['row_type'] == 'A');
                                        $rowType = $row['row_type'];
                                        $rowId   = (int)$row['id'];
                                ?>
                                <tr>
                                    <td class="sl"><?= $i + ((int)($_REQUEST['_pgn_'] ?? 0) * 50) ?></td>
                                    <td><?= $row['session_title'] ? htmlspecialchars($row['session_title']) : '-' ?></td>
                                    <td>
                                        <?php if ($isReply) { ?>
                                            <n class="badge_padding badge_secondary w-max-con">Answer</n>
                                            <?php if ($row['is_faculty'] == 'Y') { ?>
                                                <br><n class="badge_padding badge_success w-max-con">Faculty</n>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_primary w-max-con">Question</n>
                                            <?php if ($row['is_answered'] == 'Y') { ?>
                                                <br><n class="badge_padding badge_success w-max-con">Answered</n>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td style="<?= $isReply ? 'padding-left:30px;' : 'font-weight:600;' ?>">
                                        <?= $isReply ? '&#8627; ' : '' ?><?= nl2br(htmlspecialchars((string)$row['message'])) ?>
                                        <?php if (!empty($row['media_path'])) {
                                            // New files: qa_image/xx or qa_gif/xx -> inside webmaster (relative to this page)
                                            // Old files: uploads/xx -> inside CI project (App/api)
                                            $mp = ltrim($row['media_path'], '/');
                                            $mediaUrl = (strpos($mp, 'uploads/') === 0) ? ($qaMediaBase . $mp) : $mp;
                                            $mediaLbl = ($row['media_type'] == 'gif') ? 'GIF' : 'Image';
                                        ?>
                                            <br>
                                            <a href="<?= htmlspecialchars($mediaUrl) ?>" target="_blank">
                                                <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="<?= $mediaLbl ?>"
                                                     style="max-width:120px; max-height:120px; border-radius:6px; margin-top:6px;">
                                            </a>
                                        <?php } ?>
                                        <?php if (!$isReply) { ?>
                                            <br><small>
                                                <i class="fal fa-thumbs-up"></i> <?= (int)$row['upvote_count'] ?> upvotes
                                                &nbsp;|&nbsp;
                                                <i class="fal fa-comment"></i> <?= (int)$row['answer_count'] ?> answers
                                            </small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?= $row['user_full_name'] ? htmlspecialchars($row['user_full_name']) : 'User #' . (int)$row['user_id'] ?>
                                        <?php if ($row['user_registration_id']) { ?><br><small><?= htmlspecialchars($row['user_registration_id']) ?></small><?php } ?>
                                        <?php if (!$isReply && $row['is_anonymous'] == 'Y') { ?><br><small class="mandatory">Posted as Anonymous</small><?php } ?>
                                        <?php if (!$isReply && (int)$row['thread_users'] > 1) { ?><br><small class="text_primary"><?= (int)$row['thread_users'] ?> users discussing</small><?php } ?>
                                        <?php if ((int)$row['user_spam_count'] > 0) { ?>
                                            <br><small class="mandatory"><?= (int)$row['user_spam_count'] ?> spam post(s) by this user</small>
                                        <?php } ?>
                                    </td>
                                    <td><?= htmlspecialchars((string)$row['created_at']) ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'A') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Live</n>
                                        <?php } elseif ($row['status'] == 'S') { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Spam</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_dark w-max-con text-uppercase">Hidden</n>
                                        <?php } ?>
                                        <?php if ($row['status'] != 'A' && !empty($row['spam_reason'])) { ?>
                                            <br><small class="mandatory"><?= htmlspecialchars($row['spam_reason']) ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'A') { ?>
                                            <button type="button" class="badge_padding badge_dark"
                                                    onclick="modAct('<?= $rowType ?>',<?= $rowId ?>,'hide')">Hide</button>
                                            <button type="button" class="badge_padding badge_danger"
                                                    onclick="modAct('<?= $rowType ?>',<?= $rowId ?>,'spam')">Spam</button>
                                        <?php } else { ?>
                                            <button type="button" class="badge_padding badge_success"
                                                    onclick="modAct('<?= $rowType ?>',<?= $rowId ?>,'restore')">Restore</button>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr><td colspan="8" align="center"><span class="mandatory">No Record.</span></td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <form id="modForm" method="post" action="<?= htmlspecialchars(sdUrl()) ?>" style="display:none;">
                        <input type="hidden" name="mod_type"   id="mod_type">
                        <input type="hidden" name="mod_id"     id="mod_id">
                        <input type="hidden" name="mod_action" id="mod_action">
                        <input type="hidden" name="mod_reason" id="mod_reason">
                    </form>
                    <script>
                    function modAct(type, id, act) {
                        var reason = '';
                        if (act === 'spam') {
                            reason = prompt('Reason (e.g. vulgar / abusive / irrelevant):', '');
                            if (reason === null) return;
                            if (reason.trim() === '') { alert('Please enter a reason for marking this as spam.'); return; }
                        } else if (act === 'hide') {
                            reason = prompt('Reason for hiding (optional):', '');
                            if (reason === null) return;
                        } else if (!confirm('Do you want to restore this item to Live?')) {
                            return;
                        }
                        document.getElementById('mod_type').value   = type;
                        document.getElementById('mod_id').value     = id;
                        document.getElementById('mod_action').value = act;
                        document.getElementById('mod_reason').value = reason;
                        document.getElementById('modForm').submit();
                    }
                    </script>

                    <!-- ===== LIVE UPDATE: Firebase Realtime Database listener ===== -->
                    <script type="module">
                    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
                    import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-database.js";

                    const app = initializeApp({ databaseURL: <?= json_encode(QA_FB_DB_URL) ?> });
                    const db  = getDatabase(app);

                    const sid  = <?= (int)$sessionId ?>;
                    const node = sid ? 'qa_events/session_' + sid : 'qa_events/all';
                    let first = true, busy = false, again = false;

                    console.log('QA listener started on', node);

                    function refreshFeed() {
                        if (busy) { again = true; return; }
                        busy = true;
                        fetch(location.href, { credentials: 'same-origin' })
                            .then(function (r) { return r.text(); })
                            .then(function (html) {
                                var doc = new DOMParser().parseFromString(html, 'text/html');
                                var nw = doc.querySelector('.table_wrap');
                                var cw = document.querySelector('.table_wrap');
                                if (nw && cw) cw.innerHTML = nw.innerHTML;
                                else console.warn('QA: .table_wrap not found');
                            })
                            .finally(function () {
                                busy = false;
                                if (again) { again = false; refreshFeed(); }
                            });
                    }

                    onValue(ref(db, node), function (snap) {
                        console.log('QA signal:', snap.val());
                        if (first) { first = false; return; }   // page load-er prothom event skip
                        refreshFeed();
                    }, function (err) { console.error('QA listener error:', err); });
                    </script>

                    <div class="paginationDisplay">
                        <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                        <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>