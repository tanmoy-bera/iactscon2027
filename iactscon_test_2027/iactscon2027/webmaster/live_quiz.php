<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once('includes/init.php');

$pageKey        = "_pgn_";
$pageKeyVal     = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];
@$searchString  = "";
$searchArray    = array();

$searchArray[$pageKey]          = $pageKeyVal;
$searchArray['quiz']            = $_REQUEST['quiz'];
$searchArray['next_question']   = $_REQUEST['next_question'];

$show                           = trim($_REQUEST['show']);
$correct_ans                    = '';
foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_question') {
    require_once __DIR__ . '/includes/FirebaseService.php';

    $quiz_Id = $_POST['quizId'] ?? '';
    $question_Data = $_POST['questionData'] ?? '';

    if ($quiz_Id === '' || $question_Data === '') {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit;
    }

    $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);
    $question_Data = json_decode($question_Data, true);

    try {
        $firebase->set("live_quiz/$quiz_Id/current_question", $question_Data);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCICON 2025</title>
    <link rel="stylesheet" href="admin_quiz/css/style.css">
    <link rel="stylesheet" href="admin_quiz/css/responsive.css">

    <style>
        .correct-answer {
            border: 2px solid #28a745 !important;
            background: #143d14 !important;
            color: #fff !important;
            position: relative;
        }

        .correct-answer::after {
            content: "✔";
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        .question-media img,
        .question-media video {
            max-width: 90%;
            height: auto;
            margin: 10px auto;
            display: block;
        }

        .question-media audio {
            margin-top: 10px;
        }

        .dash-icon svg {
            width: 14px;
            height: 14px;
            stroke: #999;
            vertical-align: middle;
        }

        /* Dynamic grid for options */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        /* For odd number of options, last one spans full width */
        .grid-2 .col:last-child:nth-child(odd) {
            grid-column: span 2;
        }

        /* For more than 4 options, use 3 columns on desktop */
        @media (min-width: 768px) {
            .grid-2.many-options {
                grid-template-columns: repeat(3, 1fr);
            }

            .grid-2.many-options .col:last-child:nth-child(3n-1) {
                grid-column: span 2;
            }
        }
    </style>
</head>

<body>
    <?php
    switch ($show) {
        case 'live':
            liveQuiz($cfg, $mycms);
            break;
    }
    ?>
    <div class="container">
        <?php
        function liveQuiz($cfg, $mycms)
        {
            // global $searchArray, $q_answer_time, $q_buffer_time, $correct_ans, $timer_override, $quizId, $questionData, $display, $current_question;
            global $searchArray, $q_answer_time, $q_buffer_time, $correct_ans, $timer_override, $quizId, $questionData, $mediaData, $display, $current_question;
        ?>
            <div class="section-pad">
                <div class="header">
                    <h1 class="logoname">CCICON <span>2025</span></h1>
                    <img src="admin_quiz/img/l1.png" alt="" class="divdr">
                    <p class="texthead-sm">AUGUST 22-24. 2025 ITC ROYAL BENGAL</p>
                    <img src="admin_quiz/img/l2.png" alt="" class="divdr">
                    <p class="texthead-lg">6TH NATIONAL PULMONOLOGY CONFERANCE OF THE CHEST COUNCIL OF INDIA</p>
                </div>
                <?php
                $sql = [];
                $sql['QUERY'] = "SELECT * FROM rcg_quiz_questions 
                     WHERE quiz_id = ? 
                       AND status = 'active' 
                       AND row_status = 'A'
                     ORDER BY round ASC, level ASC, id ASC";
                $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $searchArray['quiz'], 'TYP' => 'i'];

                $questions = $mycms->sql_select($sql);

                $current_question   = null;
                $next_question      = null;
                $previous_question  = null;

                if (!empty($searchArray['next_question'])) {
                    foreach ($questions as $i => $q) {
                        if ($q['id'] == $searchArray['next_question']) {
                            $current_question = $q;
                            if (isset($questions[$i + 1])) {
                                $next_question = $questions[$i + 1];
                            }
                            if (isset($questions[$i - 1])) {
                                $previous_question = $questions[$i - 1];
                            }
                            break;
                        }
                    }
                } else {
                    $current_question   = $questions[0] ?? null;
                    $next_question      = $questions[1] ?? null;
                }

                // Archive previous question
                if (!empty($previous_question)) {
                    $updateSql = [];
                    $updateSql['QUERY'] = "UPDATE rcg_quiz_questions 
                           SET status = 'archived', updated_at = NOW()
                           WHERE id = ? AND quiz_id = ?";
                    $updateSql['PARAM'] = [
                        ['FILD' => 'id',        'DATA' => $previous_question['id'], 'TYP' => 'i'],
                        ['FILD' => 'quiz_id',   'DATA' => $searchArray['quiz'],     'TYP' => 'i']
                    ];
                    $mycms->sql_update($updateSql);
                }

                // Reset answers for this question on every page load
                if ($current_question) {
                    $delSql = [];
                    $delSql['QUERY'] = "DELETE FROM rcg_quiz_answers WHERE question_id = ? AND quiz_id = ?";
                    $delSql['PARAM'] = [
                        ['FILD' => 'question_id', 'DATA' => (int)$current_question['id'],   'TYP' => 'i'],
                        ['FILD' => 'quiz_id',     'DATA' => (int)$searchArray['quiz'],      'TYP' => 'i'],
                    ];
                    $mycms->sql_delete($delSql);
                }

                // Firebase preparation
                if ($current_question) {
                    date_default_timezone_set('Asia/Kolkata');

                    $sql_hall['QUERY'] = "SELECT h.id as hall_id, h.hall_title, 
                    q.id as quiz_id, q.answer_time, q.buffer_time, q.hall_id as quiz_hall_id,
                    q.view_answer as leaderboard_mode
                    FROM rcg_quizzes as q
                        JOIN rcg_program_schedule_hall as h 
                            ON h.id=q.hall_id
                    WHERE q.id = ? 
                        AND q.status = 'active' 
                        AND q.row_status = 'A'
                    LIMIT 1";
                    $sql_hall['PARAM'][] = ['FILD' => 'q.id', 'DATA' => $searchArray['quiz'], 'TYP' => 'i'];

                    $hall_details = $mycms->sql_select($sql_hall);
                    $hall_name = '';

                    if (isset($hall_details) && isset($hall_details[0])) {
                        $hall_name          = $hall_details[0]['hall_title'] ?? '';
                        $q_answer_time      = $hall_details[0]['answer_time'] ?? 20;
                        $q_buffer_time      = $hall_details[0]['buffer_time'] ?? 0;
                        $leaderboardMode    = $hall_details[0]['leaderboard_mode'] ?? 'end';
                    }
                    $timer_override = $current_question['timer_override'] ?? 0;

                    // Fetch dynamic options
                    $opt_sql = [];
                    $opt_sql['QUERY'] = "SELECT id, option_text, is_correct 
                                        FROM rcg_quiz_question_options
                                        WHERE question_id = ?
                                        ORDER BY id ASC";
                    $opt_sql['PARAM'][] = ['FILD' => 'question_id', 'DATA' => $current_question['id'], 'TYP' => 'i'];

                    $options = $mycms->sql_select($opt_sql);

                    // Assign option labels A, B, C, ...
                    $labels = range('A', 'Z');
                    $dynamicOptions = [];
                    $options = array_values($options);

                    foreach ($options as $index => $opt) {
                        $dynamicOptions[] = [
                            'id'   => $opt['id'],
                            'key'  => $labels[$index] ?? '',
                            'text' => $opt['option_text']
                        ];

                        // Track correct answer
                        if ($opt['is_correct'] == 1) {
                            $correct_ans = $labels[$index] ?? '';
                        }
                    }

                    // Determine quiz status based on next question availability
                    $quiz_status = 'active';

                    // ── resolve media for firebase ────────────────────────────
                    $fb_media_type = $current_question['media_type'] ?? null;
                    $fb_media_raw  = trim($current_question['media_url'] ?? '');
                    $fb_media_url  = null;

                    if (!empty($fb_media_type) && $fb_media_raw !== '') {
                        $isRemote = (bool)preg_match('#^https?://#i', $fb_media_raw);
                        $fb_media_url = $isRemote
                            ? $fb_media_raw
                            : 'https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/' . ltrim($fb_media_raw, '/');
                    } else {
                        $fb_media_type = null;
                    }

                    require_once __DIR__ . '/includes/FirebaseService.php';
                    $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);

                    $quizId = $searchArray['quiz'];
                    $questionData = [
                        'id'            => $current_question['id'],
                        'hall_name'     => $hall_name ?? '',
                        'question'      => $current_question['question_text'],
                        'options'       => $dynamicOptions,
                        'level'         => $current_question['level'],
                        'round'         => $current_question['round'],
                        'question_time' => $q_answer_time + $timer_override,
                        'buffer_time'   => $q_buffer_time,
                        'start_time'    => (int) round(microtime(true) * 1000),
                        'status'        => 'active',
                        'quiz_status'   => $quiz_status,
                        // 'image_url'    => ($current_question['media_type'] === 'image' && !empty($current_question['media_url'])) ? 'https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/'.trim($current_question['media_url']) : null
                        'media_type'    => $fb_media_type,                                  // image | video | audio | null
                        'media_url'     => $fb_media_url,
                        'media_description'      => $current_question['media_description'],
                        'image_url'     => ($fb_media_type === 'image') ? $fb_media_url : null,  // legacy, keep for old clients
                        'video_url'     => ($fb_media_type === 'video') ? $fb_media_url : null,
                        'audio_url'     => ($fb_media_type === 'audio') ? $fb_media_url : null
                    ];

                    // Media-only payload sent on initial page load (before "Show Question")
                    $mediaData = [
                        'id'                => $current_question['id'],
                        'hall_name'         => $hall_name ?? '',
                        'level'             => $current_question['level'],
                        'round'             => $current_question['round'],
                        'question_time'     => $q_answer_time + $timer_override,
                        'buffer_time'       => $q_buffer_time,
                        'start_time'        => (int) round(microtime(true) * 1000),
                        'status'            => 'media',
                        'quiz_status'       => $quiz_status,
                        'media_type'        => $fb_media_type,
                        'media_url'         => $fb_media_url,
                        'media_description' => $current_question['media_description'],
                        'image_url'         => ($fb_media_type === 'image') ? $fb_media_url : null,
                        'video_url'         => ($fb_media_type === 'video') ? $fb_media_url : null,
                        'audio_url'         => ($fb_media_type === 'audio') ? $fb_media_url : null,
                    ];
                }

                // If no current question found
                if (!$current_question) {
                    echo '<div class="section-pad">
                        <div class="header">
                            <h1 class="logoname">CCICON <span>2025</span></h1>
                            <img src="admin_quiz/img/l1.png" alt="" class="divdr">
                            <p class="texthead-sm">AUGUST 22-24. 2025 ITC ROYAL BENGAL</p>
                            <img src="admin_quiz/img/l2.png" alt="" class="divdr">
                            <p class="texthead-lg">6TH NATIONAL PULMONOLOGY CONFERANCE OF THE CHEST COUNCIL OF INDIA</p>
                        </div>
                        <div class="question_panel quiz_part">
                            <div class="questions_exams_row">
                                <div class="questions_list active" id="qstn1">
                                    <div class="question_box">
                                        <p class="qstn">🎉 All questions have been completed!</p>
                                        <p style="margin-top: 10px;">You can close this session or restart the quiz.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    return;
                } ?>

                <!-- <
                if (!empty($current_question['media_type']) && !empty($current_question['media_url'])) {
                    $mediaType = $current_question['media_type'];
                    $mediaUrl  = trim($current_question['media_url']);
                    $display = 'none';

                    $isDrive = strpos($mediaUrl, 'drive.google.com') !== false;
                    $driveId = null;
                    if ($isDrive && preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $mediaUrl, $matches)) {
                        $driveId = $matches[1];
                    }
                ?> -->

                <?php
                if (!empty($current_question['media_type']) && !empty($current_question['media_url'])) {
                    $mediaType = $current_question['media_type'];
                    $mediaUrl  = trim($current_question['media_url']);
                    $display = 'none';

                    $isDrive = strpos($mediaUrl, 'drive.google.com') !== false;
                    $driveId = null;
                    if ($isDrive && preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $mediaUrl, $matches)) {
                        $driveId = $matches[1];
                    }

                    // NEW: detect YouTube links and extract the video ID
                    $isYoutube = (bool)preg_match('#(youtube\.com|youtu\.be)#i', $mediaUrl);
                    $youtubeId = null;
                    if ($isYoutube) {
                        if (preg_match('#youtu\.be/([a-zA-Z0-9_-]+)#i', $mediaUrl, $m)) {
                            $youtubeId = $m[1];
                        } elseif (preg_match('#[?&]v=([a-zA-Z0-9_-]+)#i', $mediaUrl, $m)) {
                            $youtubeId = $m[1];
                        } elseif (preg_match('#youtube\.com/embed/([a-zA-Z0-9_-]+)#i', $mediaUrl, $m)) {
                            $youtubeId = $m[1];
                        } elseif (preg_match('#youtube\.com/shorts/([a-zA-Z0-9_-]+)#i', $mediaUrl, $m)) {
                            $youtubeId = $m[1];
                        } elseif (preg_match('#youtube\.com/live/([a-zA-Z0-9_-]+)#i', $mediaUrl, $m)) {
                            $youtubeId = $m[1];
                        }
                    }
                ?>
                    <div class="question-media" style="text-align:center; margin-bottom:15px;">
                        <?php if ($mediaType == 'image'): ?>
                            <?php if ($isDrive && $driveId): ?>
                                <iframe src="https://drive.google.com/file/d/<?= $driveId ?>/preview"
                                    width="640" height="360" allowfullscreen style="border:none;"></iframe>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($mediaUrl) ?>"
                                    alt="Question Image"
                                    style="max-width:80%; border-radius:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);">
                            <?php endif; ?>

                        <?php elseif ($mediaType == 'audio'): ?>
                            <?php if ($isDrive && $driveId): ?>
                                <iframe src="https://drive.google.com/file/d/<?= $driveId ?>/preview"
                                    width="400" height="80" allow="autoplay"></iframe>
                            <?php else: ?>
                                <audio controls style="width:70%;">
                                    <source src="<?= htmlspecialchars($mediaUrl) ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            <?php endif; ?>

                            <!-- < elseif ($mediaType == 'video'): ?>
                            < if ($isDrive && $driveId): ?>
                                <iframe src="https://drive.google.com/file/d/< $driveId ?>/preview"
                                    width="640" height="360" allow="autoplay"></iframe>
                            < else: ?>
                                <video controls style="max-width:80%; border-radius:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);" preload="metadata">
                                    <source src="< htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                                    Your browser does not support the video element.
                                </video>
                            < endif; ?>
                        < endif; ?> -->

                        <?php elseif ($mediaType == 'video'): ?>
                            <?php if ($isYoutube && $youtubeId): ?>
                                <iframe src="https://www.youtube.com/embed/<?= $youtubeId ?>?autoplay=0&rel=0"
                                    width="640" height="360" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    style="border-radius:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);"></iframe>
                            <?php elseif ($isDrive && $driveId): ?>
                                <iframe src="https://drive.google.com/file/d/<?= $driveId ?>/preview"
                                    width="640" height="360" allow="autoplay"></iframe>
                            <?php else: ?>
                                <video controls style="max-width:80%; border-radius:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);" preload="metadata">
                                    <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                                    Your browser does not support the video element.
                                </video>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($current_question['media_description'])): ?>
                            <div style="margin: 0 auto 15px auto; max-width: 90%; text-align: center; background: #1a1a1a; border-radius: 10px; padding: 15px; box-shadow: 0 0 6px rgba(0,0,0,0.2);">
                                <p style="margin: 0; color: #fff; font-size: 16px; font-weight: normal; line-height: 1.5;">
                                    <?= htmlspecialchars($current_question['media_description']) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div style="margin-top:10px;">
                            <button id="show-question-btn" class="btn_quiz">Show Question</button>
                        </div>
                    </div>
                <?php } else {
                    $display = 'block';
                } ?>

                <div class="question_panel quiz_part" id="quiz-content" style="display:<?= $display ?>;">
                    <div class="couinter_box">
                        <span class="countdown" id="timer">00</span>
                    </div>
                    <div class="questions_exams_row">
                        <div class="questions_list active" id="qstn1">
                            <div class="question_box">
                                <div class="d-qz-res-flex">
                                    <p>Level <span class="attemt_count"><?= $current_question['level'] ?? '' ?></span> | Round <span class="rountattempt_count"><?= $current_question['round'] ?? '' ?></span></p>
                                    <p><span class="plyer_count">Hall:</span> <?= $hall_name ?? '' ?></p>
                                </div>
                                <p class="qstn">Q. <?= $current_question['question_text'] ?></p>
                            </div>

                            <!-- Dynamic Options Grid -->
                            <div class="grid-2 answer_panel <?= count($dynamicOptions) > 4 ? 'many-options' : '' ?>" id="dynamic-options-container">
                                <?php
                                if (!empty($dynamicOptions)) {
                                    foreach ($dynamicOptions as $index => $opt) {
                                        $optionId = "q1a" . ($index + 1);
                                        $optionKey = $opt['key'];
                                ?>
                                        <div class="col">
                                            <input type="radio" id="<?= $optionId ?>" name="answr" value="<?= $optionKey ?>" data-option-id="<?= $opt['id'] ?>">
                                            <label class="answer_box" for="<?= $optionId ?>">
                                                <p class="answrno"><?= $optionKey ?>.</p>
                                                <p class="answr" style="padding-top: 20px;"><?= htmlspecialchars($opt['text']) ?></p>
                                            </label>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<p>No options available for this question.</p>';
                                }
                                ?>
                            </div>

                        </div>
                        <?php
                        // Leaderboard decision
                        $showLeaderboard = false;
                        $leaderboardMode = $leaderboardMode ?? 'end';

                        $nextRound    = $next_question['round'] ?? null;
                        $nextLevel    = $next_question['level'] ?? null;
                        $currentRound = $current_question['round'] ?? null;
                        $currentLevel = $current_question['level'] ?? null;

                        if ($leaderboardMode === 'question') {
                            $showLeaderboard = true;
                        } elseif ($leaderboardMode === 'round') {
                            if (empty($next_question) || $nextRound !== $currentRound) {
                                $showLeaderboard = true;
                            }
                        } elseif ($leaderboardMode === 'level') {
                            if (empty($next_question)) {
                                $showLeaderboard = true;
                            } elseif (
                                ($nextRound === $currentRound && $nextLevel !== $currentLevel) ||
                                ($nextRound !== $currentRound)
                            ) {
                                $showLeaderboard = true;
                            }
                        } elseif ($leaderboardMode === 'end') {
                            if (empty($next_question)) {
                                $showLeaderboard = true;
                            }
                        }

                        if (!empty($next_question)) {
                            if ($showLeaderboard) {
                                $nextLink  = "{$cfg['SECTION_BASE_URL']}quiz_leaderboard.php?quiz={$searchArray['quiz']}&next_question={$next_question['id']}";
                                $nextLabel = "Show Leaderboard";
                            } else {
                                $nextLink  = "{$cfg['SECTION_BASE_URL']}live_quiz.php?show=live&quiz={$searchArray['quiz']}&next_question={$next_question['id']}";
                                $nextLabel = "Next Question";
                            }
                        } else {
                            if ($showLeaderboard) {
                                $nextLink  = "{$cfg['SECTION_BASE_URL']}quiz_leaderboard.php?quiz={$searchArray['quiz']}";
                                $nextLabel = "Show Final Leaderboard";
                            } else {
                                $nextLink = '';
                                $nextLabel = '';
                            }
                        }
                        ?>

                        <div id="btnNextQuestion" style="display: none;">
                            <div class="button_qz">
                                <a href="javascript:void(0);" id="showAnswer" onclick="showAnswer();" class="btn_quiz">Show Answer</a>
                                <?php if ($nextLink): ?>
                                    &nbsp;&nbsp;
                                    <a href="<?= $nextLink ?>" class="btn_quiz"><?= $nextLabel ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer exam_footer">
                <div class="container">
                    <div class="section-pad position-relative footer_flex">
                        <div class="left_footer">
                            <div class="left_f_logo">
                                <p>MANAGED BY</p>
                                <img src="admin_quiz/img/Rueda New logo.png" alt="">
                            </div>
                        </div>
                        <div class="center_footer">
                            <div class="button_qz" style="margin-top: 30px;margin-bottom: 30px;">
                                <button type="button" class="btn_quiz" data-click="modal" data-modal-id="#modal_answer">Show Leaderboard</button>
                            </div>
                        </div>
                        <div class="right_footer">
                            <div class="right_f_content">
                                <div class="participent_no">
                                    <p>TOTAL PARTICIPATION<br><span> <?= $totalParticipants ?> </span></p>
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
        <?php } ?>
    </div>

    <!-- Leaderboard Modal (keeping existing code) -->
    <div class="modal" id="modal_answer">
        <!-- Keep your existing modal code here -->
        <div class="modal-container">
            <div class="modal_box">
                <div class="modal-header">
                    <h3>Modal Title</h3>
                    <button type="button" class="modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 64 64"
                            style="fill:#FFFFFF;">
                            <path d="M51.092 15.737L48.263 12.908 32 29.172 15.737 12.908 12.908 15.737 29.172 32 12.908 48.263 15.737 51.092 32 34.828 48.263 51.092 51.092 48.263 34.828 32z"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    global $mycms;

                    $quizId = $_REQUEST['quiz']; // dynamically set from request

                    $sql = [];
                    $sql['QUERY'] = "SELECT * FROM rcg_quiz_questions 
                        WHERE quiz_id = ? 
                        AND row_status = 'A'
                        ORDER BY round ASC, level ASC, id ASC";
                    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];

                    $questions = $mycms->sql_select($sql);
                    $sql = [];
                    $sql['QUERY'] = "
                        SELECT
                            s.user_id,
                            s.device_id,
                            s.correct_count,
                            s.total_time,
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
                            SELECT
                                user_id,
                                device_id,
                                SUM(is_correct = 1)                                       AS correct_count,
                                SUM(CASE WHEN is_correct = 1 THEN time_taken ELSE 0 END)  AS total_time
                            FROM rcg_quiz_answers
                            WHERE quiz_id = ?
                            GROUP BY user_id, device_id
                        ) AS s
                        ORDER BY s.correct_count DESC, s.total_time ASC
                    ";
                    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];   // group lookup (NEW — must be first)
                    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];   // Part A

                    $leaderboard = $mycms->sql_select($sql);

                    ?>

                    <div class="question_answer_list">
                        <?php
                        if (!empty($leaderboard)) {
                            $questionIds = array_column($questions, 'id');
                            $position = 1;
                            foreach ($leaderboard as $user) {

                                $isGuest = empty($user['user_id']);
                                $ansSql = [];
                                $ansSql['QUERY'] = "
                                    SELECT question_id, selected_option, is_correct
                                    FROM rcg_quiz_answers
                                    WHERE quiz_id = ? AND " . ($isGuest ? "device_id" : "user_id") . " = ?
                                    ORDER BY question_id ASC
                                ";
                                $ansSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
                                $ansSql['PARAM'][] = [
                                    'FILD' => $isGuest ? 'device_id' : 'user_id',
                                    'DATA' => $isGuest ? $user['device_id'] : $user['user_id'],
                                    'TYP' => 's'
                                ];

                                $answers = $mycms->sql_select($ansSql);


                                $answerMap = [];
                                foreach ($answers as $ans) {
                                    $answerMap[$ans['question_id']] = $ans;
                                }

                                $isTop = ($position <= 3) ? 'checkcolor' : '';
                        ?>
                                <div class="leader_board_box <?= $isTop ?>">
                                    <span class="pos">
                                        <?= $position ?><?= $position == 1 ? 'ST' : ($position == 2 ? 'ND' : ($position == 3 ? 'RD' : 'TH')) ?>
                                    </span>
                                    <!-- <span class="nm">< htmlspecialchars($user['user_full_name']) ?></span> -->
                                    <span class="nm"><?= htmlspecialchars($user['display_name']) ?></span>

                                    <div class="ans_row">
                                        <?php
                                        // Fallback current question ID (leaderboard page)
                                        $currentQ = isset($_REQUEST['next_question']) ? (int)$_REQUEST['next_question'] : 0;

                                        foreach ($questions as $q) {
                                            $qid = $q['id'];

                                            $hasAnswer = isset($answerMap[$qid]);
                                            $isCorrect = $hasAnswer ? $answerMap[$qid]['is_correct'] : null;

                                            // Determine if user reached this question
                                            $didReach = ($qid < $currentQ);

                                            if ($hasAnswer) {
                                                $iconClass = $isCorrect ? 'correct' : 'incorrect';
                                            } elseif ($didReach) {
                                                $iconClass = 'skipped';
                                            } else {
                                                echo '<span class="answer-icon empty"></span>';
                                                continue;
                                            }
                                        ?>
                                            <span class="answer-icon <?= $iconClass ?>">
                                                <?php if ($iconClass === 'correct') { ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>

                                                <?php } elseif ($iconClass === 'skipped') { ?>
                                                    <span class="dash-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                                        </svg>
                                                    </span>
                                                <?php } else { ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                <?php } ?>
                                            </span>
                                        <?php } ?>



                                    </div>

                                    <span class="tm">[<?= number_format($user['total_score'], 2) ?>] <?= number_format($user['total_time'], 2) ?> SEC</span>
                                </div>
                        <?php
                                $position++;
                            }
                        } else {
                            echo "<p>No answers yet.</p>";
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="admin_quiz/js/jquery.min.js"></script>

    <script>
        function showAnswer() {
            <?php
            // Get correct option ID
            $correctOptionSql = [];
            $correctOptionSql['QUERY'] = "SELECT id FROM rcg_quiz_question_options 
                                           WHERE question_id = ? AND is_correct = 1 LIMIT 1";
            $correctOptionSql['PARAM'][] = ['FILD' => 'question_id', 'DATA' => $current_question['id'], 'TYP' => 'i'];
            $correctOptionData = $mycms->sql_select($correctOptionSql);
            $correctOptionId = $correctOptionData[0]['id'] ?? null;

            // Also get the correct key (A, B, C, etc.) as backup
            $correctKey = '';
            foreach ($dynamicOptions as $opt) {
                if ($opt['id'] == $correctOptionId) {
                    $correctKey = $opt['key'];
                    break;
                }
            }
            ?>

            console.log('Looking for correct option ID:', '<?= $correctOptionId ?>');
            console.log('Correct key:', '<?= $correctKey ?>');

            const correctOptionId = "<?= $correctOptionId ?>";
            const correctKey = "<?= $correctKey ?>";

            // Try to find by option ID
            let correctInput = document.querySelector('input[data-option-id="' + correctOptionId + '"]');

            // Fallback: find by value (key)
            if (!correctInput) {
                console.log('Trying fallback with key:', correctKey);
                correctInput = document.querySelector('input[value="' + correctKey + '"]');
            }

            // Clear old highlights
            document.querySelectorAll(".answer_box").forEach(el => {
                el.classList.remove("correct-answer");
            });

            // Highlight correct
            if (correctInput) {
                console.log('Found correct input:', correctInput);
                correctInput.checked = true;
                correctInput.nextElementSibling.classList.add("correct-answer");
            } else {
                console.error('Could not find correct answer input');
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Keep your existing scripts for participants, leaderboard, timer, etc. -->
    <script>
        let quizId = "<?= $quizId ?>";

        function refreshParticipants() {
            $.ajax({
                url: "fetch_participants.php",
                method: "GET",
                data: {
                    quiz: quizId
                },
                dataType: "json",
                success: function(res) {
                    $(".participent_no span").text(res.count);
                }
            });
        }

        setInterval(refreshParticipants, 3000);
    </script>


    <script>
        function refreshLeaderboard() {
            let currentQ = <?= (int)($_REQUEST['next_question'] ?? 0) ?>;


            $.ajax({
                url: "fetch_leaderboard.php",
                method: "GET",
                data: {
                    quiz: quizId,
                    currentQ: currentQ
                },
                dataType: "json",
                success: function(res) {
                    let html = '';
                    let position = 1;

                    res.leaderboard.forEach(row => {

                        let posSuffix =
                            (position === 1 ? "ST" :
                                (position === 2 ? "ND" :
                                    (position === 3 ? "RD" : "TH")));

                        let icons = "";
                        let qIds = row.question_ids;

                        qIds.forEach((qid, index) => {
                            let isCorrect = row.answers[qid];
                            let didReach = (qid < res.currentQ);

                            if (isCorrect == 1) {
                                // Correct ✔
                                icons += `
                        <span class="answer-icon correct">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </span>`;

                            } else if (isCorrect == 0) {
                                // Incorrect ✖
                                icons += `
                        <span class="answer-icon incorrect">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>`;

                            } else if (didReach) {
                                // Skipped ➖ (reached but no answer)
                                icons += `
                        <span class="answer-icon skipped">
                            <span class="dash-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14" />
                                </svg>
                            </span>
                        </span>`;

                            } else {
                                // NOT reached yet ▢ empty
                                icons += `<span class="answer-icon empty"></span>`;
                            }
                        });

                        let tmHtml = '';
                        if (res.scoringEnabled) {
                            tmHtml = `[${parseFloat(row.total_score).toFixed(2)}] ` +
                                `${parseFloat(row.total_time).toFixed(2)} SEC`;
                        } else {
                            tmHtml = `${parseFloat(row.total_time).toFixed(2)} SEC`;
                        }


                        html += `
                <div class="leader_board_box ${position <= 3 ? 'checkcolor' : ''}">
                    <span class="pos">${position}${posSuffix}</span>
                    <span class="nm">${row.display_name}</span>
                    <div class="ans_row">
                        ${icons}
                    </div>
                    <span class="tm">${tmHtml}</span>
                </div>`;


                        position++;
                    });

                    $(".question_answer_list").html(html);
                }
            });
        }

        let leaderboardTimer;

        // Modal open → start auto refresh
        $(document).on("click", '[data-click="modal"]', function() {
            refreshLeaderboard();
            leaderboardTimer = setInterval(refreshLeaderboard, 3000);
        });

        // Modal close → stop refresh
        $(document).on("click", ".modal-close", function() {
            clearInterval(leaderboardTimer);
        });
    </script>

    <!-- <script>
        document.getElementById('show-question-btn')?.addEventListener('click', function() {
            const btn = this;
            btn.style.display = 'none';
            $('#quiz-content').show();
            startTimerAndFirebase();
        });

        const display = "<?= $display ?>";
        if (display === 'block') {
            startTimerAndFirebase();
        }

        function startTimerAndFirebase() {
            const quizId = "<?= $quizId ?>";
            const questionData = <?= json_encode($questionData) ?>;

            $.ajax({
                url: "",
                type: "POST",
                data: {
                    action: "set_question",
                    quizId: quizId,
                    questionData: JSON.stringify(questionData)
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === "success") {
                        console.log("Firebase updated!");

                        let totalSeconds = Number(<?= (int)$q_answer_time ?>);
                        let bufferSeconds = Number(<?= (int)$q_buffer_time ?>);
                        let timer_override = Number(<?= (int)$timer_override ?>);
                        let remaining = totalSeconds + bufferSeconds + timer_override;
                        let nextBtn = document.getElementById('btnNextQuestion');

                        function updateTimer() {
                            if (remaining < 0) {
                                clearInterval(timerID);
                                document.getElementById('timer').textContent = "00";
                                setTimeout(() => {
                                    if (nextBtn) nextBtn.style.display = "block";
                                }, 1000);
                                return;
                            }
                            document.getElementById('timer').textContent = remaining.toString().padStart(2, '0');
                            remaining--;
                        }

                        updateTimer();
                        const timerID = setInterval(updateTimer, 1000);

                    } else {
                        console.error("Firebase error:", res.message);
                    }
                },
                error: function(err) {
                    console.error("AJAX error:", err);
                }
            });
        }

        $(document).ready(function() {
            $(document).on("click", '[data-click="modal"]', function() {
                var modalId = $(this).data("modal-id");
                $(modalId).addClass("open");
            });

            $(document).on("click", ".modal-close", function() {
                $(this).closest(".modal").removeClass("open");
            });
        });
    </script> -->

    <script>
        const mediaData = <?= json_encode($mediaData ?? null) ?>;

        // 1st Firebase call — sends only media info, fired as soon as the media screen loads
        function sendMediaToFirebase() {
            console.log("📤 [1st CALL] Sending MEDIA data to Firebase:", mediaData);

            $.ajax({
                url: "",
                type: "POST",
                data: {
                    action: "set_question",
                    quizId: "<?= $quizId ?>",
                    questionData: JSON.stringify(mediaData)
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === "success") {
                        console.log("✅ [1st CALL] Firebase MEDIA update successful!", res);
                    } else {
                        console.error("❌ [1st CALL] Firebase MEDIA error:", res.message);
                    }
                },
                error: function(err) {
                    console.error("❌ [1st CALL] AJAX MEDIA error:", err);
                }
            });
        }

        document.getElementById('show-question-btn')?.addEventListener('click', function() {
            const btn = this;
            btn.style.display = 'none';
            $('#quiz-content').show();
            startTimerAndFirebase();
        });

        const display = "<?= $display ?>";

        if (display === 'none') {
            // Media is shown first — send media data immediately
            sendMediaToFirebase();
        } else if (display === 'block') {
            // No media at all — go straight to question
            startTimerAndFirebase();
        }

        // 2nd Firebase call — sends full question + options, fired on "Show Question" click
        function startTimerAndFirebase() {
            const quizId = "<?= $quizId ?>";
            const questionData = <?= json_encode($questionData) ?>;

            console.log("📤 [2nd CALL] Sending QUESTION data to Firebase:", questionData);

            $.ajax({
                url: "",
                type: "POST",
                data: {
                    action: "set_question",
                    quizId: quizId,
                    questionData: JSON.stringify(questionData)
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === "success") {
                        console.log("✅ [2nd CALL] Firebase QUESTION update successful!", res);

                        let totalSeconds = Number(<?= (int)$q_answer_time ?>);
                        let bufferSeconds = Number(<?= (int)$q_buffer_time ?>);
                        let timer_override = Number(<?= (int)$timer_override ?>);
                        let remaining = totalSeconds + bufferSeconds + timer_override;
                        let nextBtn = document.getElementById('btnNextQuestion');

                        function updateTimer() {
                            if (remaining < 0) {
                                clearInterval(timerID);
                                document.getElementById('timer').textContent = "00";
                                setTimeout(() => {
                                    if (nextBtn) nextBtn.style.display = "block";
                                }, 1000);
                                return;
                            }
                            document.getElementById('timer').textContent = remaining.toString().padStart(2, '0');
                            remaining--;
                        }

                        updateTimer();
                        const timerID = setInterval(updateTimer, 1000);

                    } else {
                        console.error("❌ [2nd CALL] Firebase error:", res.message);
                    }
                },
                error: function(err) {
                    console.error("❌ [2nd CALL] AJAX error:", err);
                }
            });
        }
    </script>

</body>

</html>