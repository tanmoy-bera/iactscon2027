<?php
// ── LEADERBOARD AJAX (must be first, before any output) ──────────────────────
if (isset($_GET['act']) && $_GET['act'] === 'leaderboard') {
    ini_set('display_errors', 0);
    include_once("..includes/init.php");
    header('Content-Type: application/json');

    $quizId = isset($_GET['quiz_id']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['quiz_id']) : null;
    if (!$quizId) {
        echo json_encode(['success' => false, 'message' => 'Missing quiz_id']);
        exit;
    }

    global $mycms;

    // Questions list (for per-question result icons)
    $sqlQ = [];
    $sqlQ['QUERY']   = "SELECT id FROM rcg_quiz_questions
                        WHERE quiz_id = ? AND row_status = 'A'
                        ORDER BY round ASC, level ASC, id ASC";
    $sqlQ['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
    $questions   = $mycms->sql_select($sqlQ);
    $questionIds = array_column($questions, 'id');

    // Scoring rule
    $sqlMark = [];
    $sqlMark['QUERY']   = "SELECT marks_correct, marks_incorrect FROM rcg_quizzes WHERE id=?";
    $sqlMark['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
    $markRule        = $mycms->sql_select($sqlMark);
    $marks_correct   = $markRule[0]['marks_correct']   ?? 0;
    $marks_incorrect = $markRule[0]['marks_incorrect'] ?? 0;
    $scoringEnabled  = !($marks_correct == 0 && $marks_incorrect == 0);

    // ── LEADERBOARD QUERY ────────────────────────────────────────────────────
    // FIX: split into 3 clean parts — eliminates ghost/duplicate rows
    //  Part 1: registered users (INNER JOIN to user table, device_id forced NULL)
    //  Part 2: guests who answered (user_id IS NULL, INNER JOIN to guest_users)
    //  Part 3: guests who joined but never answered
    $sqlLB = [];
    $sqlLB['QUERY'] = "
        SELECT
            s.user_id,
            s.device_id,
            s.correct_count,
            s.total_time,
            s.total_score,
            CASE
                WHEN s.user_id IS NOT NULL THEN COALESCE(
                    (
                        SELECT p.group_name
                        FROM rcg_quiz_participation_members m
                        INNER JOIN quiz_participation p ON p.id = m.participation_id
                        WHERE m.user_id = s.user_id
                          AND p.quiz_id = ?
                          AND p.participation_type = 'group'
                          AND p.group_name IS NOT NULL
                          AND p.group_name != ''
                        LIMIT 1
                    ),
                    (
                        SELECT u.user_full_name
                        FROM rcg_user_registration u
                        WHERE u.id = s.user_id
                        LIMIT 1
                    )
                )
                ELSE COALESCE((
                    SELECT g.guest_name
                    FROM quiz_guest_users g
                    WHERE g.device_id = s.device_id
                    LIMIT 1
                ), 'Guest')
            END AS display_name
        FROM (
            -- Part A: everyone who answered — counts from answers table only, no fan-out
            SELECT
                user_id,
                device_id,
                SUM(is_correct = 1)                                        AS correct_count,
                SUM(CASE WHEN is_correct = 1 THEN time_taken ELSE 0 END)   AS total_time,
                SUM(marks_obtained)                                         AS total_score
            FROM rcg_quiz_answers
            WHERE quiz_id = ?
            GROUP BY user_id, device_id
            UNION ALL
            -- Part B: guests who joined but never answered (zero-score rows)
            SELECT
                NULL        AS user_id,
                g.device_id,
                0           AS correct_count,
                0           AS total_time,
                0           AS total_score
            FROM quiz_guest_users g
            WHERE g.latest_visit_quiz_id = ?
              AND g.device_id NOT IN (
                  SELECT DISTINCT device_id
                  FROM rcg_quiz_answers
                  WHERE quiz_id = ? AND device_id IS NOT NULL
              )
            GROUP BY g.device_id
        ) AS s
        ORDER BY s.total_score DESC, s.correct_count DESC, s.total_time ASC
    ";
    $sqlLB['PARAM'] = [
        ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'],   // group lookup (NEW — must be first)
        ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'],   // Part A
        ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'],   // Part B latest_visit
        ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'],   // Part B NOT IN
    ];
    $leaderboard = $mycms->sql_select($sqlLB);

    $rows     = [];
    $position = 1;

    foreach ($leaderboard as $user) {
        // FIX: consistent guest detection — user_id presence wins
        $isGuest = empty($user['user_id']);
        $idField = $isGuest ? 'device_id' : 'user_id';
        $idValue = $isGuest ? $user['device_id'] : $user['user_id'];
        $idType  = $isGuest ? 's' : 'i';
        if (!$idValue) continue;

        $ansSql = [];
        $ansSql['QUERY']  = "SELECT question_id, is_correct
                              FROM rcg_quiz_answers
                              WHERE quiz_id = ? AND $idField = ?
                              ORDER BY question_id ASC";
        $ansSql['PARAM']  = [
            ['FILD' => 'quiz_id', 'DATA' => $quizId,  'TYP' => 'i'],
            ['FILD' => $idField,  'DATA' => $idValue,  'TYP' => $idType],
        ];
        $answers = $mycms->sql_select($ansSql);

        $answerMap = [];
        foreach ($answers as $ans) {
            $answerMap[$ans['question_id']] = (int)$ans['is_correct'];
        }

        // Build per-question result: 1=correct, 0=incorrect, null=skipped
        $questionResults = [];
        foreach ($questionIds as $qid) {
            $questionResults[] = isset($answerMap[$qid]) ? $answerMap[$qid] : null;
        }

        $rows[] = [
            'position'         => $position,
            'display_name'     => $user['display_name'] ?: 'Guest',
            'correct_count'    => (int)$user['correct_count'],
            'total_time'       => round((float)$user['total_time'],  2),
            'total_score'      => round((float)$user['total_score'], 2),
            'question_results' => $questionResults,
        ];
        $position++;
    }

    echo json_encode([
        'success'         => true,
        'scoring_enabled' => $scoringEnabled,
        'question_count'  => count($questionIds),
        'leaderboard'     => $rows,
    ]);
    exit;
}
// ── END LEADERBOARD AJAX ─────────────────────────────────────────────────────

ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once("includes/init.php");
include_once("live_quiz/includes/source.php");
include_once("live_quiz/includes/firebase_bootstrap.php");

$quizId = isset($_GET['quiz_id']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['quiz_id']) : null;
if (!$quizId) {
    die("Missing quiz_id");
}
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date('Y-m-d H:i:s');

function getRealIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'];
}
function generateGuestCode() {
    return str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
}

// Generate or reuse stable device ID
if (!isset($_COOKIE['session_based_device_id'])) {
    $baseId = bin2hex(random_bytes(32));
    setcookie('session_based_device_id', $baseId, time() + (86400 * 365), "/");
} else {
    $baseId = $_COOKIE['session_based_device_id'];
}

$sessionDeviceId         = $baseId;
$device_ip_address       = getRealIpAddress();
$first_login_for_quiz_id = $quizId;

$openModal           = false;
$submittedGuestName  = isset($_POST['guest_name'])  ? trim($_POST['guest_name'])  : '';
$submittedGuestPhone = isset($_POST['guest_phone']) ? trim($_POST['guest_phone']) : '';
$phoneError          = "";
$hasPhoneError       = false;

// Phone validation + registration check (only when form submitted)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($submittedGuestPhone === '') {
        $phoneError    = "Please enter your phone number.";
        $hasPhoneError = true;
    } elseif (!preg_match('/^[0-9]{10}$/', $submittedGuestPhone)) {
        $phoneError    = "Please enter a valid 10 digit phone number.";
        $hasPhoneError = true;
    } else {
        $checkPhone = [];
        $checkPhone['QUERY']   = "SELECT id FROM rcg_user_registration
                                  WHERE user_mobile_no = ? AND status = 'A'";
        $checkPhone['PARAM'][] = ['FILD' => 'user_mobile_no', 'DATA' => $submittedGuestPhone, 'TYP' => 's'];
        $phoneExists = $mycms->sql_select($checkPhone);
        if (empty($phoneExists)) {
            $phoneError    = "This phone number is not registered.";
            $hasPhoneError = true;
        }
    }
}

$sql = [];
$sql['QUERY']   = "SELECT guest_code, latest_visit_quiz_id, guest_name
                   FROM quiz_guest_users
                   WHERE device_id=? AND latest_visit_quiz_id=?";
$sql['PARAM'][] = ['FILD' => 'device_id',             'DATA' => $sessionDeviceId,         'TYP' => 's'];
$sql['PARAM'][] = ['FILD' => 'latest_visit_quiz_id',  'DATA' => $first_login_for_quiz_id, 'TYP' => 's'];
$check_device = $mycms->sql_select($sql);

$guest_name = $check_device[0]['guest_name'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($hasPhoneError) {
        $openModal = true;
    } elseif ($submittedGuestName == '') {
        $openModal = true;
    } else {
        if (!empty($check_device)) {
            $previousQuizId = $check_device[0]['latest_visit_quiz_id'];
            $guest_code     = $check_device[0]['guest_code'];
            $guest_name     = $submittedGuestName;
            if ($previousQuizId != $first_login_for_quiz_id) {
                $guest_code = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            }
            $update = [];
            $update['QUERY']  = "UPDATE quiz_guest_users SET
                                  latest_visit_time=?, ip_address=?,
                                  latest_visit_quiz_id=?, guest_code=?, guest_name=?, phn_no=?
                                  WHERE device_id=?";
            $update['PARAM']  = [
                ['FILD' => 'latest_visit_time',    'DATA' => $currentDateTime,         'TYP' => 's'],
                ['FILD' => 'ip_address',           'DATA' => $device_ip_address,       'TYP' => 's'],
                ['FILD' => 'latest_visit_quiz_id', 'DATA' => $first_login_for_quiz_id, 'TYP' => 's'],
                ['FILD' => 'guest_code',           'DATA' => $guest_code,              'TYP' => 's'],
                ['FILD' => 'guest_name',           'DATA' => $guest_name,              'TYP' => 's'],
                ['FILD' => 'phn_no',               'DATA' => $submittedGuestPhone,     'TYP' => 's'],
                ['FILD' => 'device_id',            'DATA' => $sessionDeviceId,         'TYP' => 's'],
            ];
            $mycms->sql_update($update);
            $openModal = false;
        } else {
            $guest_code = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            $insert = [];
            $insert['QUERY']  = "INSERT INTO quiz_guest_users SET
                                  device_id=?, ip_address=?, first_visit_quiz_id=?,
                                  first_visit_time=?, latest_visit_time=?,
                                  latest_visit_quiz_id=?, guest_code=?, guest_name=?, phn_no=?";
            $insert['PARAM']  = [
                ['FILD' => 'device_id',            'DATA' => $sessionDeviceId,         'TYP' => 's'],
                ['FILD' => 'ip_address',           'DATA' => $device_ip_address,       'TYP' => 's'],
                ['FILD' => 'first_visit_quiz_id',  'DATA' => $first_login_for_quiz_id, 'TYP' => 's'],
                ['FILD' => 'first_visit_time',     'DATA' => $currentDateTime,         'TYP' => 's'],
                ['FILD' => 'latest_visit_time',    'DATA' => $currentDateTime,         'TYP' => 's'],
                ['FILD' => 'latest_visit_quiz_id', 'DATA' => $first_login_for_quiz_id, 'TYP' => 's'],
                ['FILD' => 'guest_code',           'DATA' => $guest_code,              'TYP' => 's'],
                ['FILD' => 'guest_name',           'DATA' => $submittedGuestName,      'TYP' => 's'],
                ['FILD' => 'phn_no',               'DATA' => $submittedGuestPhone,     'TYP' => 's'],
            ];
            $mycms->sql_insert($insert);
            $openModal = false;
        }
    }
} else {
    $openModal = true;
}

$openModalFlag = $openModal ? "1" : "0";

$current     = $firebase->get(fb_path_quiz($quizId, 'current_question'));
$serverNowMs = fb_get_server_ms();
$quizStatus  = $current['quiz_status'] ?? null;
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
    .modal-backdrop { z-index: 200000 !important; }
    .modal          { z-index: 200001 !important; }
    .modal-dialog   { pointer-events: auto !important; }

    /* ── question image ── */
    .q-image-wrap {
        width: 100%; margin: 10px 0 14px;
        border-radius: 8px; overflow: hidden;
        background: #111; text-align: center;
    }
    .q-image-wrap img {
        width: 100%; max-height: 220px;
        object-fit: contain; display: block;
    }

    /* ── completed box — full-height flex column ─────────────── */
    #box-completed {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px); /* fill viewport minus header */
        min-height: 0;
    }
    #box-completed .lb-header-area {
        flex-shrink: 0;
        text-align: center;
        padding: 12px 0 4px;
    }
    .lb-title {
        background: linear-gradient(90deg,#FFD700,#FFB300);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        font-size: 20px;
        margin: 0 0 2px;
    }
    .lb-sub {
        color: #4BC9FF;
        font-size: 13px;
        margin: 0 0 10px;
    }
    #lb-loading {
        color: #aaa;
        font-size: 14px;
        text-align: center;
        padding: 20px 0;
        flex-shrink: 0;
    }

    /* ── scrollable leaderboard area ────────────────────────── */
    #lb-container {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0 2px 4px;
        scrollbar-width: thin;
        min-height: 0;
    }
    #lb-container::-webkit-scrollbar       { width: 4px; }
    #lb-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

    /* ── leaderboard rows ────────────────────────────────────── */
    .lb-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        margin-bottom: 5px;
        border-radius: 8px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .lb-row.top1 { border-color: #FFD700;  background: rgba(255,215,0,0.08); }
    .lb-row.top2 { border-color: #C0C0C0;  background: rgba(192,192,192,0.07); }
    .lb-row.top3 { border-color: #cd7f32;  background: rgba(205,127,50,0.07); }

    .lb-pos {
        font-size: 11px; font-weight: 700; color: #aaa;
        min-width: 26px; text-align: center; flex-shrink: 0;
    }
    .lb-row.top1 .lb-pos { color: #FFD700; }
    .lb-row.top2 .lb-pos { color: #C0C0C0; }
    .lb-row.top3 .lb-pos { color: #cd7f32; }

    .lb-name {
        flex: 1; font-size: 13px; font-weight: 500; color: #eee;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .lb-icons {
        direction: rtl;
        display: flex !important;
        flex-direction: row-reverse;
        justify-content: flex-start;
        gap: 3px;
        max-width: 110px;
        overflow-x: auto;
        overflow-y: hidden;
        flex-shrink: 0;
        padding-bottom: 2px;
        scrollbar-width: none;
    }
    .lb-icons::-webkit-scrollbar { display: none; }
    .lb-icons > * { direction: ltr; flex-shrink: 0; }

    .lb-icon {
        width: 18px; height: 18px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .lb-icon.correct   { background: rgba(34,197,94,0.2);  color: #22c55e; }
    .lb-icon.incorrect { background: rgba(239,68,68,0.2);  color: #ef4444; }
    .lb-icon.skipped   { background: rgba(156,163,175,0.15); color: #9ca3af; }
    .lb-icon svg       { width: 10px; height: 10px; stroke: currentColor; }

    .lb-score {
        font-size: 11px; color: #aaa;
        white-space: nowrap; flex-shrink: 0;
    }
</style>

<body class="ccicon">
    <div class="main_bar">
        <?php include_once("live_quiz/includes/header.php"); ?>
        <div class="main_body">

            <!-- Active question -->
            <div class="quiz_box d-none" id="box-active">
                <div class="sc_prgm_head">
                    <h3>Quiz</h3>
                    <p><?php date_default_timezone_set('Asia/Kolkata'); echo date('F d, Y'); ?> | Hall- <i id="hall_name_active"></i></p>
                </div>
                <div class="quiz_head">
                    <span class="timer-container" id="timer-active"></span>
                    <h6><i>Level <i id="level-active"></i></i><i>ROUND <i id="round-active"></i></i></h6>
                    <h4 id="qtextActive">Q. —</h4>
                </div>
                <div><ul id="optionsActive"></ul></div>
            </div>

            <!-- Answered (locked) -->
            <div class="quiz_box answered d-none" id="box-answered">
                <div class="sc_prgm_head">
                    <h3>Quiz</h3>
                    <p><?php date_default_timezone_set('Asia/Kolkata'); echo date('F d, Y'); ?> | Hall- <i id="hall_name_answered"></i></p>
                </div>
                <div class="quiz_head">
                    <span id="timer-answered">00</span>
                    <h6><i>Level <i id="level-answered"></i></i><i>ROUND <i id="round-answered"></i></i></h6>
                    <h4 id="qtextAnswered">Q. —</h4>
                </div>
                <div><ul id="optionsAnswered"></ul></div>
            </div>

            <!-- Waiting -->
            <div class="quiz_thank d-none" id="box-wait">
                <h2>Waiting for the question</h2>
            </div>

            <!-- Quiz Completed — flex column: title + scrollable leaderboard -->
            <div class="quiz_thank d-none" id="box-completed">
                <div class="lb-header-area">
                    <h2 class="lb-title">Quiz Completed</h2>
                    <p class="lb-sub">Thank you for participating!</p>
                </div>
                <div id="lb-loading">Loading results...</div>
                <div id="lb-container"></div>
            </div>

        </div>
        <?php include_once("live_quiz/includes/footer.php"); ?>
    </div>

    <input type="hidden" id="openModalFlag" value="<?= $openModalFlag ?>">

    <div class="modal fade" id="guestNameModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                    <input type="hidden" name="modal"   value="1">
                    <div class="modal-header">
                        <h5 class="modal-title">Enter Your Details</h5>
                    </div>
                    <div class="modal-body">
                        <label>Your Name</label>
                        <input type="text" name="guest_name" class="form-control"
                               placeholder="Enter name"
                               value="<?= htmlspecialchars($guest_name ?? '') ?>" required>
                        <label class="mt-3">Phone Number</label>
                        <input type="text" name="guest_phone" class="form-control"
                               placeholder="Enter 10 digit phone number"
                               value="<?= htmlspecialchars($submittedGuestPhone ?? '') ?>"
                               maxlength="10" required>
                        <?php if (!empty($phoneError)) : ?>
                            <p class="text-danger mt-2"><?= $phoneError ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once("live_quiz/includes/js-source.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var flag = document.getElementById('openModalFlag');
            if (flag && flag.value === "1") {
                var modal = new bootstrap.Modal(document.getElementById('guestNameModal'), {
                    backdrop: 'static', keyboard: false
                });
                modal.show();
            }
        });
    </script>

    <script>
        window.__QUIZ_ID__      = <?php echo json_encode($quizId); ?>;
        window.__CURRENT__      = <?php echo json_encode($current ?: null); ?>;
        window.__SERVER_NOW_MS__ = <?php echo (int)$serverNowMs; ?>;
        window.__QUIZ_STATUS__  = <?php echo json_encode($quizStatus); ?>;
    </script>

    <script>
    (function () {
        var boxActive    = document.getElementById('box-active');
        var boxAnswered  = document.getElementById('box-answered');
        var boxWait      = document.getElementById('box-wait');
        var boxCompleted = document.getElementById('box-completed');

        var timerActive   = document.getElementById('timer-active');
        var timerAnswered = document.getElementById('timer-answered');

        var qtextActive   = document.getElementById('qtextActive');
        var qtextAnswered = document.getElementById('qtextAnswered');

        var lvltextActive    = document.getElementById('level-active');
        var lvltextAnswered  = document.getElementById('level-answered');
        var roundtextActive  = document.getElementById('round-active');
        var roundtextAnswered = document.getElementById('round-answered');
        var halltextActive   = document.getElementById('hall_name_active');
        var halltextAnswered = document.getElementById('hall_name_answered');

        var optionsActiveContainer   = document.getElementById('optionsActive');
        var optionsAnsweredContainer = document.getElementById('optionsAnswered');

        // Session ID
        var SESSION_KEY = "quizSessionId";
        var sessionId = localStorage.getItem(SESSION_KEY);
        if (!sessionId) {
            sessionId = "sess_" + Math.random().toString(36).slice(2);
            localStorage.setItem(SESSION_KEY, sessionId);
        }

        // Server-client clock sync
        var serverOffset = 0;
        function setServerOffset(serverNowMs) { serverOffset = Number(serverNowMs) - Date.now(); }
        function nowMs() { return Date.now() + serverOffset; }

        // State
        var current        = window.__CURRENT__ || null;
        var selectedOptionId = null;
        var answered       = false;
        var ticker         = null;
        var endAt          = null;
        var driftComp      = 0;
        var quizCompleted  = false;

        var LATENCY_FUDGE  = 200;
        var pollDelay      = 800;
        var QUIZ_ID        = window.__QUIZ_ID__;

        var show  = function (el) { el.classList.remove('d-none'); };
        var hide  = function (el) { el.classList.add('d-none'); };
        var pad2  = function (n)  { return n < 10 ? "0" + n : "" + n; };
        var toMMSS = function (s) {
            s = Math.max(0, Math.ceil(s));
            return pad2(Math.floor(s / 60)) + ':' + pad2(s % 60);
        };

        function setAllRadiosDisabled(container, dis) {
            container.querySelectorAll('input[type="radio"]').forEach(function (r) { r.disabled = dis; });
        }

        // XSS helper
        function escapeHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }

        // ── Show quiz completed + fetch leaderboard ────────────────────────
        function showQuizCompleted() {
            stopTicker();
            hide(boxActive);
            hide(boxAnswered);
            hide(boxWait);
            show(boxCompleted);
            quizCompleted = true;

            var lbLoading   = document.getElementById('lb-loading');
            var lbContainer = document.getElementById('lb-container');

            lbLoading.style.display = 'block';
            lbContainer.innerHTML   = '';

            fetch('quiz-process.php?act=leaderboard&quiz_id=' + encodeURIComponent(QUIZ_ID), { cache: 'no-store' })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    lbLoading.style.display = 'none';
                    if (!data.success || !data.leaderboard.length) {
                        lbContainer.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;">No results yet.</p>';
                        return;
                    }
                    var scoringEnabled = data.scoring_enabled;

                    data.leaderboard.forEach(function (user) {
                        var pos      = user.position;
                        var topClass = pos === 1 ? 'top1' : pos === 2 ? 'top2' : pos === 3 ? 'top3' : '';
                        var ordinal  = pos === 1 ? 'ST'  : pos === 2 ? 'ND'  : pos === 3 ? 'RD'  : 'TH';

                        var iconsHtml = '';
                        (user.question_results || []).forEach(function (r) {
                            var cls, svg;
                            if (r === 1) {
                                cls = 'correct';
                                svg = '<svg viewBox="0 0 24 24" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                            } else if (r === 0) {
                                cls = 'incorrect';
                                svg = '<svg viewBox="0 0 24 24" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
                            } else {
                                cls = 'skipped';
                                svg = '<svg viewBox="0 0 24 24" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"/></svg>';
                            }
                            iconsHtml += '<span class="lb-icon ' + cls + '">' + svg + '</span>';
                        });

                        var scoreHtml = scoringEnabled
                            ? '[' + user.total_score.toFixed(2) + '] ' + user.total_time.toFixed(2) + 's'
                            : user.total_time.toFixed(2) + 's';

                        var row = document.createElement('div');
                        row.className = 'lb-row ' + topClass;
                        row.innerHTML =
                            '<span class="lb-pos">'  + pos + ordinal + '</span>' +
                            '<span class="lb-name">' + escapeHtml(user.display_name) + '</span>' +
                            '<div class="lb-icons">' + iconsHtml + '</div>' +
                            '<span class="lb-score">' + scoreHtml + '</span>';
                        lbContainer.appendChild(row);

                        // Scroll icons so most-recent is visible
                        var iconsEl = row.querySelector('.lb-icons');
                        if (iconsEl) iconsEl.scrollLeft = 999999;
                    });
                })
                .catch(function (err) {
                    lbLoading.style.display = 'none';
                    lbContainer.innerHTML   = '<p style="color:#aaa;font-size:13px;text-align:center;">Could not load results.</p>';
                    console.warn('Leaderboard fetch failed:', err);
                });
        }

        function generateOptions(container, options, nameAttr) {
            container.innerHTML = '';
            if (!options || !Array.isArray(options)) return;
            options.forEach(function (opt) {
                var label    = document.createElement('label');
                label.className = 'con';
                var keyBold  = document.createElement('b');
                keyBold.textContent = (opt.key || '') + '.';
                var textItalic = document.createElement('i');
                textItalic.textContent = opt.text || '';
                var input    = document.createElement('input');
                input.type   = 'radio';
                input.name   = nameAttr;
                input.value  = opt.id;
                input.setAttribute('data-key', opt.key || '');
                input.disabled = true;
                var checkmark = document.createElement('span');
                checkmark.className = 'checkmark';
                label.appendChild(keyBold);
                label.appendChild(textItalic);
                label.appendChild(input);
                label.appendChild(checkmark);
                container.appendChild(label);
            });
        }

        function renderQuestion(q) {
            qtextActive.textContent   = "Q. " + (q.question || "");
            qtextAnswered.textContent = "Q. " + (q.question || "");

            ['box-active', 'box-answered'].forEach(function (boxId) {
                var old = document.getElementById(boxId).querySelector('.q-image-wrap');
                if (old) old.remove();
            });
            if (q.image_url) {
                ['optionsActive', 'optionsAnswered'].forEach(function (containerId) {
                    var wrap = document.createElement('div');
                    wrap.className = 'q-image-wrap';
                    var img = document.createElement('img');
                    img.src = q.image_url; img.alt = 'Question image';
                    wrap.appendChild(img);
                    var optEl = document.getElementById(containerId);
                    optEl.parentNode.insertBefore(wrap, optEl);
                });
            }

            lvltextActive.textContent    = q.level  || '';
            lvltextAnswered.textContent  = q.level  || '';
            roundtextActive.textContent  = q.round  || '';
            roundtextAnswered.textContent = q.round || '';
            halltextActive.textContent   = q.hall_name || '';
            halltextAnswered.textContent = q.hall_name || '';

            if (q.options && Array.isArray(q.options) && q.options.length > 0) {
                generateOptions(optionsActiveContainer,   q.options, 'radio');
                generateOptions(optionsAnsweredContainer, q.options, 'radio2');
            }

            selectedOptionId = null;
            answered = false;
            show(boxActive);
            hide(boxAnswered);
            hide(boxWait);
            hide(boxCompleted);
            attachRadioListeners();
        }

        function attachRadioListeners() {
            optionsActiveContainer.querySelectorAll('input[type="radio"]').forEach(function (r) {
                r.addEventListener('change', function (e) { handleAnswerSelect(e.target); });
            });
        }

        function lockUI() {
            setAllRadiosDisabled(optionsActiveContainer, true);
            optionsAnsweredContainer.querySelectorAll('input[type="radio"]').forEach(function (r) {
                r.checked  = (r.value === selectedOptionId);
                r.disabled = true;
            });
            hide(boxActive);
            show(boxAnswered);
            hide(boxWait);
            hide(boxCompleted);
            stopTicker();
            timerActive.textContent   = "00:00";
            timerActive.style.color   = '';
            timerAnswered.textContent = "00:00";
        }

        function updateTimerDisplays(now) {
            if (endAt == null || !current) return;
            var totalRemainingMs  = endAt - (now - driftComp);
            var totalRemainingSec = totalRemainingMs / 1000;
            if (totalRemainingSec > 0) {
                var bufferTime  = Number(current.buffer_time   || 0);
                var startAt     = Number(current.start_time);
                var elapsedSec  = ((now - driftComp) - startAt) / 1000;
                var displayTime = toMMSS(totalRemainingSec);
                timerActive.textContent   = displayTime;
                timerActive.style.color   = elapsedSec < bufferTime ? '#ff9800' : '';
                timerAnswered.textContent = displayTime;
            } else {
                timerActive.textContent   = "00:00";
                timerActive.style.color   = '';
                timerAnswered.textContent = "00:00";
            }
        }

        function stopTicker() {
            if (ticker) { clearInterval(ticker); ticker = null; }
        }

        function startTicker() {
            stopTicker();
            ticker = setInterval(function () {
                if (!current || endAt == null) return;
                var n           = nowMs();
                var remainingMs = endAt - (n - driftComp);
                if (remainingMs <= 0) {
                    if (!answered) answered = true;
                    lockUI();
                    return;
                }
                var startAt    = Number(current.start_time);
                var elapsedSec = ((n - driftComp) - startAt) / 1000;
                var bufferTime = Number(current.buffer_time || 0);
                if (!answered) {
                    setAllRadiosDisabled(optionsActiveContainer, elapsedSec < bufferTime);
                } else {
                    setAllRadiosDisabled(optionsActiveContainer, true);
                }
                updateTimerDisplays(n);
            }, 150);
        }

        function startTimer(q) {
            var startAt      = Number(q.start_time);
            var bufferTime   = Number(q.buffer_time   || 0);
            var questionTime = Number(q.question_time || 0);
            endAt     = startAt + (bufferTime + questionTime) * 1000;
            driftComp = (nowMs() - startAt) - LATENCY_FUDGE;
            updateTimerDisplays(nowMs());
            startTicker();
        }

        function handleAnswerSelect(radioEl) {
            var n           = nowMs();
            var remainAtClick = Math.max(0, (endAt - (n - driftComp)) / 1000);
            if ((endAt - (n - driftComp)) > 0 && !answered) {
                selectedOptionId = radioEl.value;
                answered = true;
                lockUI();
                document.getElementById("timer-answered").textContent = toMMSS(remainAtClick);
                var bufferTime   = Number(current.buffer_time   || 0);
                var questionTime = Number(current.question_time || 0);
                var timeTaken    = Math.max(0, (bufferTime + questionTime) - remainAtClick);
                fetch("answer.process.php?act=submit_answer", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        quiz_id: QUIZ_ID, question_id: current.id,
                        selected_option: selectedOptionId,
                        time_taken: timeTaken, session: sessionId
                    })
                }).then(function (res) { return res.json(); })
                  .then(function (data) { console.log("Answer response:", data); })
                  .catch(function (err) { console.warn("submit failed:", err); });
            } else {
                radioEl.checked = false;
            }
        }

        function applyQuestion(q, quizStatus) {
            if (quizStatus === 'completed') { showQuizCompleted(); return; }
            if (!q || q.status !== 'active') {
                stopTicker();
                hide(boxActive); hide(boxAnswered); hide(boxCompleted); show(boxWait);
                current = null;
                return;
            }
            var needReset = !current || q.id !== current.id || q.start_time !== current.start_time;
            current = q;
            if (needReset) { renderQuestion(q); startTimer(q); }
            else if (!answered) { updateTimerDisplays(nowMs()); }
        }

        async function pollLoop() {
            try {
                var t0  = Date.now();
                var res = await fetch('live_quiz/ajax/get_current_question.php?quiz_id=' + encodeURIComponent(QUIZ_ID), { cache: 'no-store' });
                var t1  = Date.now();
                if (!res.ok) throw new Error(res.status);
                var js = await res.json();
                if (js && js.serverNowMs) setServerOffset(Number(js.serverNowMs));
                var quizStatus = (js.data && js.data.quiz_status) ? js.data.quiz_status : null;
                applyQuestion(js.data || null, quizStatus);
                if (quizStatus === 'completed') return;
                pollDelay = (js.data && js.data.status === 'active') ? 800 : 1500;
            } catch (e) {
                console.warn("poll error", e);
                pollDelay = 2000;
            } finally {
                if (!quizCompleted) setTimeout(pollLoop, pollDelay);
            }
        }

        // Bootstrap
        var initialQuizStatus = window.__QUIZ_STATUS__ || null;
        if (initialQuizStatus === 'completed') {
            showQuizCompleted();
        } else if (window.__CURRENT__) {
            if (window.__SERVER_NOW_MS__) setServerOffset(window.__SERVER_NOW_MS__);
            applyQuestion(window.__CURRENT__, initialQuizStatus);
        } else {
            hide(boxActive); hide(boxAnswered); hide(boxCompleted); show(boxWait);
        }
        setTimeout(pollLoop, 200);
    })();
    </script>
</body>
</html>