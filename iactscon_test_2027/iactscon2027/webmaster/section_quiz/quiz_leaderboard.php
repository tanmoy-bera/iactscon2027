<?php
include_once('includes/init.php');
require_once __DIR__ . '/includes/FirebaseService.php';
$firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);

$pageKey    = "_pgn_";
$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];
$searchString = "";
$searchArray  = [];

$searchArray[$pageKey]        = $pageKeyVal;
$searchArray['quiz']          = $_REQUEST['quiz'];
$searchArray['next_question'] = $_REQUEST['next_question'];

// FIX: don't shadow $next_question here — only use $searchArray['next_question']
foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}

// ─────────────────────────────────────────────────────────────────────────────
//  SHARED LEADERBOARD QUERY — run ONCE, reused in both main view and modal
//  FIX: split UNION into 3 clean parts to eliminate ghost/duplicate rows
// ─────────────────────────────────────────────────────────────────────────────
global $mycms;

$quizId = $searchArray['quiz'];

// Questions (for answer-icon mapping)
$sqlQ = [];
$sqlQ['QUERY'] = "SELECT id FROM rcg_quiz_questions
                  WHERE quiz_id = ? AND row_status = 'A'
                  ORDER BY round ASC, level ASC, id ASC";
$sqlQ['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
$questions   = $mycms->sql_select($sqlQ);
$questionIds = array_column($questions, 'id');

// Full question rows needed for previous/next/current navigation
$sqlAllQ = [];
$sqlAllQ['QUERY'] = "SELECT * FROM rcg_quiz_questions
                     WHERE quiz_id = ? AND row_status = 'A'
                     ORDER BY round ASC, level ASC, id ASC";
$sqlAllQ['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
$allQuestions = $mycms->sql_select($sqlAllQ);

// Navigation
$current_question  = null;
$next_question_row = null;
$previous_question = null;

if (!empty($searchArray['next_question'])) {
    foreach ($allQuestions as $i => $q) {
        if ($q['id'] == $searchArray['next_question']) {
            $current_question  = $q;
            $next_question_row = $allQuestions[$i + 1] ?? null;
            $previous_question = $allQuestions[$i - 1] ?? null;
            break;
        }
    }
} else {
    $current_question  = $allQuestions[0] ?? null;
    $next_question_row = $allQuestions[1] ?? null;
}

// Archive previous question
if (!empty($previous_question)) {
    $updateSql = [];
    $updateSql['QUERY'] = "UPDATE rcg_quiz_questions
                           SET status = 'archived', updated_at = NOW()
                           WHERE id = ? AND quiz_id = ?";
    $updateSql['PARAM'] = [
        ['FILD' => 'id',      'DATA' => $previous_question['id'], 'TYP' => 'i'],
        ['FILD' => 'quiz_id', 'DATA' => $quizId,                  'TYP' => 'i'],
    ];
    $mycms->sql_update($updateSql);
}

// Archive ALL when quiz is over
if (empty($searchArray['next_question'])) {
    $updateAllSql = [];
    $updateAllSql['QUERY'] = "UPDATE rcg_quiz_questions
                              SET status = 'archived', updated_at = NOW()
                              WHERE quiz_id = ? AND row_status = 'A'";
    $updateAllSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
    $mycms->sql_update($updateAllSql);
}

// Scoring rule
$sqlMark = [];
$sqlMark['QUERY']   = "SELECT marks_correct, marks_incorrect FROM rcg_quizzes WHERE id=?";
$sqlMark['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
$markRule        = $mycms->sql_select($sqlMark);
$marks_correct   = $markRule[0]['marks_correct']   ?? 0;
$marks_incorrect = $markRule[0]['marks_incorrect'] ?? 0;
$scoringEnabled  = !($marks_correct == 0 && $marks_incorrect == 0);

// ── LEADERBOARD QUERY ────────────────────────────────────────────────────────
// FIX: Three clean UNION parts:
//   Part 1 — registered users who answered (INNER JOIN to user_registration,
//             force device_id = NULL so they never collide with guest rows in GROUP BY)
//   Part 2 — guests who answered (user_id IS NULL, INNER JOIN to quiz_guest_users)
//   Part 3 — guests who joined but never answered
// This eliminates the "ghost row" that appeared when a registered user also had
// a device record in quiz_guest_users, causing the count to show N+1.
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
        -- Part A: everyone who answered — counts come from the answers table only,
        --         so no JOIN can fan out and inflate the totals
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

// ── PARTICIPANT COUNT ────────────────────────────────────────────────────────
// FIX: separate registered users + guests to avoid double-counting
$partSql = [];
$partSql['QUERY'] = "
    SELECT COUNT(*) AS total_participants
    FROM (
        SELECT DISTINCT user_id AS uid
        FROM rcg_quiz_answers
        WHERE quiz_id = ? AND user_id IS NOT NULL

        UNION

        SELECT DISTINCT device_id AS uid
        FROM quiz_guest_users
        WHERE latest_visit_quiz_id = ?
    ) AS participants
";
$partSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
$partSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
$participants      = $mycms->sql_select($partSql);
$totalParticipants = $participants[0]['total_participants'] ?? 0;

// ── HELPER: fetch answers for one user ───────────────────────────────────────
// FIX: consistent guest detection — user_id presence wins over device_id
function fetchAnswerMap($quizId, $user, $mycms) {
    $isGuest = empty($user['user_id']); // user_id presence = registered user
    $idField = $isGuest ? 'device_id' : 'user_id';
    $idValue = $isGuest ? $user['device_id'] : $user['user_id'];
    $idType  = $isGuest ? 's' : 'i';
    if (!$idValue) return [];

    $ansSql = [];
    $ansSql['QUERY'] = "
        SELECT question_id, selected_option, is_correct
        FROM rcg_quiz_answers
        WHERE quiz_id = ? AND $idField = ?
        ORDER BY question_id ASC
    ";
    $ansSql['PARAM'] = [
        ['FILD' => 'quiz_id',  'DATA' => $quizId,  'TYP' => 'i'],
        ['FILD' => $idField,   'DATA' => $idValue,  'TYP' => $idType],
    ];
    $answers = $mycms->sql_select($ansSql);

    $map = [];
    foreach ($answers as $ans) {
        $map[$ans['question_id']] = $ans;
    }
    return $map;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCICON 2025</title>
    <link rel="stylesheet" href="admin_quiz/css/style.css?v=1">
    <link rel="stylesheet" href="admin_quiz/css/responsive.css?v=1">
    <style>
        /* ── answer-icon strip ──────────────────────────────────────── */
        .answers_height {
            direction: rtl;
            display: flex !important;
            flex-direction: row-reverse;
            justify-content: flex-start;
            gap: 0.6rem;
            max-width: 320px;
            overflow-x: auto;
            overflow-y: hidden;
            max-height: 75px;
            padding: 2px;
        }
        .answers_height > * { direction: ltr; }

        .dash-icon svg {
            width: 14px;
            height: 14px;
            stroke: #999;
            vertical-align: middle;
        }

        /* ── leaderboard layout ─────────────────────────────────────── */
        .leaderboard {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 220px); /* leaves room for header */
            min-height: 0;
        }
        .leaderboard_scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
            scrollbar-width: thin;
        }
        .leaderboard_scroll::-webkit-scrollbar { width: 5px; }
        .leaderboard_scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        /* ── bottom action area ─────────────────────────────────────── */
        .leaderboard_footer {
            flex-shrink: 0;
            padding: 14px 0 8px;
            text-align: center;
        }
        .leaderboard_footer p {
            margin: 0 0 10px;
            font-size: 15px;
            color: #eee;
        }

        /* ── leaderboard row improvements ──────────────────────────── */
        .leaderboard-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            margin-bottom: 6px;
            border-radius: 10px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            transition: background 0.15s;
        }
        .leaderboard-row.checkcolor {
            border-color: rgba(255,215,0,0.35);
            background: rgba(255,215,0,0.06);
        }
        .leaderboard-row .position {
            font-size: 13px;
            font-weight: 700;
            min-width: 34px;
            text-align: center;
            color: #aaa;
            flex-shrink: 0;
        }
        .leaderboard-row.checkcolor .position { color: #FFD700; }
        .leaderboard-row .name {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .leaderboard-row .time {
            font-size: 12px;
            color: #9ca3af;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── answer icons ───────────────────────────────────────────── */
        .answer-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        /*.answer-icon.correct   { background: rgba(34,197,94,0.18);  color: #22c55e; }
        .answer-icon.incorrect { background: rgba(239,68,68,0.18);  color: #ef4444; }
        .answer-icon.skipped   { background: rgba(156,163,175,0.12); color: #9ca3af; }
        .answer-icon svg       { width: 12px; height: 12px; stroke: currentColor; stroke-width: 2.5; }*/
    </style>
</head>
<body>
<div class="container">
    <div class="section-pad">
        <div class="header">
            <h1 class="logoname">CCICON <span>2025</span></h1>
            <img src="admin_quiz/img/l1.png" alt="" class="divdr">
            <p class="texthead-sm">AUGUST 22-24. 2025 ITC ROYAL BENGAL</p>
            <img src="admin_quiz/img/l2.png" alt="" class="divdr">
            <p class="texthead-lg">6TH NATIONAL PULMONOLOGY CONFERANCE OF THE CHEST COUNCIL OF INDIA</p>
        </div>
        <h1 class="thank_msg">THANK YOU FOR YOUR PARTICIPATION !</h1>

        <div class="leaderboard">
            <!-- Scrollable list -->
            <div class="leaderboard_scroll" id="leaderboardScroll">
                <?php if (!empty($leaderboard)) :
                    $position = 1;
                    foreach ($leaderboard as $user) :
                        $answerMap = fetchAnswerMap($quizId, $user, $mycms);
                        $isTop = ($position <= 3) ? 'checkcolor' : '';
                ?>
                    <div class="leaderboard-row <?= $isTop ?>">
                        <span class="position">
                            <?= $position ?><?= $position === 1 ? 'ST' : ($position === 2 ? 'ND' : ($position === 3 ? 'RD' : 'TH')) ?>
                        </span>
                        <span class="name"><?= htmlspecialchars($user['display_name'] ?: 'Guest') ?></span>

                        <div class="answers answers_height">
                            <?php foreach ($questionIds as $qid) :
                                if (isset($answerMap[$qid])) {
                                    $isCorrect = $answerMap[$qid]['is_correct'];
                                    $isSkipped = false;
                                } else {
                                    $isCorrect = null;
                                    $isSkipped = true;
                                }
                            ?>
                                <span class="answer-icon <?= $isCorrect == 1 ? 'correct' : ($isSkipped ? 'skipped' : 'incorrect') ?>">
                                    <?php if ($isCorrect == 1) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    <?php elseif ($isSkipped) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                        </svg>
                                    <?php else : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($scoringEnabled) : ?>
                            <span class="time">
                                [<?= number_format($user['total_score'], 2) ?>]
                                <?= number_format($user['total_time'], 2) ?> SEC
                            </span>
                        <?php else : ?>
                            <span class="time"><?= number_format($user['total_time'], 2) ?> SEC</span>
                        <?php endif; ?>
                    </div>
                <?php
                    $position++;
                    endforeach;
                else : ?>
                    <p>No answers yet.</p>
                <?php endif; ?>
            </div><!-- /.leaderboard_scroll -->

            <!-- Fixed bottom action -->
            <div class="leaderboard_footer">
                <?php if (!empty($searchArray['next_question'])) :
                    $nq = $searchArray['next_question'];
                ?>
                    <div class="button_qz">
                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>live_quiz.php?show=live&quiz=<?= $quizId ?>&next_question=<?= $nq ?>"
                           class="btn_quiz">
                            Proceed to Next Question
                        </a>
                    </div>
                <?php else :
                    // Mark quiz complete + push to Firebase
                    $updateQuiz = [];
                    $updateQuiz['QUERY'] = "UPDATE rcg_quizzes SET status=? WHERE id=?";
                    $updateQuiz['PARAM'] = [
                        ['FILD' => 'status', 'DATA' => 'completed', 'TYP' => 's'],
                        ['FILD' => 'id',     'DATA' => $quizId,     'TYP' => 's'],
                    ];
                    $mycms->sql_update($updateQuiz);

                    // FIX: set 'completed' signal in Firebase and KEEP IT THERE.
                    // Previously we set() then immediately delete() on the same page load,
                    // which wiped the signal before any participant poll could read it —
                    // causing participants to see "Waiting" forever instead of the leaderboard.
                    // The record stays as {quiz_status:'completed'} permanently; it will be
                    // cleaned up at the start of the next quiz session.
                    $completedQuestionData = ['id' => (int)$quizId, 'quiz_status' => 'completed'];
                    $firebase->set("live_quiz/$quizId/current_question", $completedQuestionData);
                ?>
                    <p>🎉 Quiz Completed! No more questions left.</p>
                <?php endif; ?>
            </div>
        </div><!-- /.leaderboard -->
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <div class="container">
        <div class="section-pad position-relative footer_flex">
            <div class="left_footer">
                <div class="left_f_logo">
                    <p>MANAGED BY</p>
                    <img src="admin_quiz/img/Rueda New logo.png" alt="">
                </div>
            </div>
            <div class="right_footer">
                <div class="right_f_content">
                    <div class="participent_no">
                        <p>TOTAL PARTICIPATION<br><span><?= $totalParticipants ?></span></p>
                    </div>
                    <div class="participent_qr">
                        <img src="admin_quiz/img/qr.png" alt="">
                        <p>SCAN QR TO JOIN NOW</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal (detail view) -->
<div class="modal" id="modal_answer">
    <div class="modal-container">
        <div class="modal_box">
            <div class="modal-header">
                <h3>Answer Details</h3>
                <button type="button" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" style="fill:#FFFFFF;">
                        <path d="M51.092 15.737L48.263 12.908 32 29.172 15.737 12.908 12.908 15.737 29.172 32 12.908 48.263 15.737 51.092 32 34.828 48.263 51.092 51.092 48.263 34.828 32z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="question_answer_list">
                    <?php if (!empty($leaderboard)) :
                        $position = 1;
                        foreach ($leaderboard as $user) :
                            // FIX: reuse $leaderboard already fetched — no second query
                            $answers   = fetchAnswerMap($quizId, $user, $mycms);
                            $isTop     = ($position <= 3) ? 'checkcolor' : '';
                    ?>
                        <div class="leader_board_box <?= $isTop ?>">
                            <span class="pos">
                                <?= $position ?><?= $position === 1 ? 'ST' : ($position === 2 ? 'ND' : ($position === 3 ? 'RD' : 'TH')) ?>
                            </span>
                            <span class="name"><?= htmlspecialchars($user['display_name'] ?: 'Guest') ?></span>

                            <div class="ans_row">
                                <?php foreach ($answers as $ans) :
                                    $iconClass = $ans['is_correct'] ? 'correct' : 'incorrect';
                                ?>
                                    <span class="ansicon <?= $iconClass ?>">
                                        <?php if ($ans['is_correct']) : ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        <?php else : ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <span class="tm">
                                [<?= number_format($user['total_score'], 2) ?>]
                                <?= number_format($user['total_time'], 2) ?> SEC
                            </span>
                        </div>
                    <?php
                        $position++;
                        endforeach;
                    else : ?>
                        <p>No answers yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="admin_quiz/js/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $(document).on('click', '[data-click="modal"]', function () {
            var modalId = $(this).data('modal-id');
            $(modalId).addClass('open');
        });
        $(document).on('click', '.modal-close', function () {
            $(this).closest('.modal').removeClass('open');
        });
    });

    // Scroll all answer strips to the right so the most-recent answer is visible
    document.querySelectorAll('.answers_height').forEach(function (el) {
        el.scrollLeft = 999999;
    });
</script>
</body>
</html>