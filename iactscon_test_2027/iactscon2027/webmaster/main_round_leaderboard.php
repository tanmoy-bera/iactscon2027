<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Live Quiz</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]        = $pageKeyVal;
        $searchArray['quiz']          = $_REQUEST['quiz'] ?? '';
        $searchArray['next_question'] = $_REQUEST['next_question'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . $searchVal;
        }

        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Live Quiz</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'live') ? 'Live Quiz' : 'Quiz Listing' ?></h2>
                <h6>View completed quizzes and their leaderboards.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                switch ($show) {
                    case 'live':
                        liveQuiz($cfg, $mycms);
                        break;
                    case 'edit':
                        if (function_exists('editQuiz')) {
                            editQuiz($cfg, $mycms);
                        }
                        break;
                    case 'view':
                        if (function_exists('viewQuiz')) {
                            viewQuiz($cfg, $mycms);
                        }
                        break;
                    default:
                        showAllQuiz($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    window.onload = function() {
        let timeLeft = 20; // in seconds
        const timerElement = document.getElementById('timer');
        const nextBtn = document.getElementById('btnNextQuestion');

        if (timerElement) {
            timerElement.textContent = timeLeft;

            const countdown = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    timerElement.textContent = "Time's up!";
                    if (nextBtn) nextBtn.style.display = "inline-block"; // Show the button
                }
            }, 1000);
        }
    };
</script>
</html>
<?php

/*============== SHOW ALL QUIZ ================*/
function showAllQuiz($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];
    if (!empty($_REQUEST['start_date'])) {
        $con .= " AND DATE(`start_time`) >= ?";
        $params[] = [
            'FILD' => 'start_date',
            'DATA' => $_REQUEST['start_date'],
            'TYP'  => 's'
        ];
    }
    if (!empty($searchArray['next_question'])) {
        $con .= " AND `id`` = ?";
        $params[] = [
            'FILD' => 'id',
            'DATA' => $searchArray['next_question'],
            'TYP'  => 's'
        ];
    }

    $sql = [];
    $sql['QUERY'] = "SELECT * FROM `rcg_quizzes` 
        WHERE `row_status` != ? 
        $con 
        AND (`status` = 'completed' OR `status` = 'completed') 
        ORDER BY id DESC";

    $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);
    ?>
    <div class="com_info_box active" id="liveQuizListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-trophy"></i></span> Quiz Listing</n>
                </h5>
                <div class="com_info_box_inner">
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th class="sl">#</th>
                                    <th>Quiz Title</th>
                                    <th>Rounds</th>
                                    <th>Levels (In each Round)</th>
                                    <th class="action text-right">Action</th>
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
                                    <td class="sl"><?= $i + ($_REQUEST['_pgn_'] * 50) ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= $row['rounds'] ?></td>
                                    <td><?= $row['levels'] ?></td>
                                    <td class="action">
                                        <a href="main_round_leaderboard_quiz.php?quiz_id=<?= $row['id'] ?>" class="badge_primary">Show Leaderboard</a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="5" align="center">
                                        <span class="mandatory">No Record.</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/*============== LIVE QUIZ ================*/
function liveQuiz($cfg, $mycms)
{
    global $searchArray;

    $sql = [];
    $sql['QUERY'] = "SELECT * FROM rcg_quiz_questions 
                     WHERE quiz_id = ? 
                       AND status = 'active' 
                       AND row_status = 'A'
                     ORDER BY round ASC, level ASC, id ASC";
    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $searchArray['quiz'], 'TYP' => 'i'];

    $questions = $mycms->sql_select($sql);

    $current_question = null;
    $next_question = null;

    if (!empty($searchArray['next_question'])) {
        // Loop to find the current and next question by ID
        foreach ($questions as $i => $q) {
            if ($q['id'] == $searchArray['next_question']) {
                $current_question = $q;
                if (isset($questions[$i + 1])) {
                    $next_question = $questions[$i + 1];
                }
                break;
            }
        }
    } else {
        // First time: no next_question param, show the first
        $current_question = $questions[0] ?? null;
        $next_question = $questions[1] ?? null;
    }

    // firebase
    if ($current_question) {
        require_once 'includes/FirebaseService.php'; // include your FirebaseService class

        $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);

        $quizId = $searchArray['quiz'];
        date_default_timezone_set('Asia/Kolkata');
        $questionData = [
            'id' => $current_question['id'],
            'question' => $current_question['question_text'],
            'option_a' => $current_question['option_a'],
            'option_b' => $current_question['option_b'],
            'option_c' => $current_question['option_c'],
            'option_d' => $current_question['option_d'],
            'question_time' => 20,
            'buffer_time' => 5,
            'start_time' => (int) round(microtime(true) * 1000), // time(),
            'status' => 'active'
        ];

        $firebase->set("live_quiz/$quizId/current_question", $questionData);
    }
    ?>
    <div class="com_info_box active" id="liveQuizQuestion">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-broadcast-tower"></i></span> Quiz</n>
                </h5>
                <div class="com_info_box_inner">
                    <?php if ($current_question) { ?>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <p class="frm-head">Q: <?= htmlspecialchars($current_question['question_text']) ?></p>
                        </div>
                        <div class="frm_grp span_2">
                            <p>A. <?= htmlspecialchars($current_question['option_a']) ?></p>
                        </div>
                        <div class="frm_grp span_2">
                            <p>B. <?= htmlspecialchars($current_question['option_b']) ?></p>
                        </div>
                        <div class="frm_grp span_2">
                            <p>C. <?= htmlspecialchars($current_question['option_c']) ?></p>
                        </div>
                        <div class="frm_grp span_2">
                            <p>D. <?= htmlspecialchars($current_question['option_d']) ?></p>
                        </div>
                    </div>

                    <div class="page_top_wrap mt-3">
                        <div class="page_top_wrap_left">
                            Time Remaining: <span id="timer"></span>
                        </div>
                        <div class="page_top_wrap_right">
                            <?php if ($next_question) { ?>
                                <a href="quizstart.php?quiz=<?= $searchArray['quiz'] ?>&next_question=<?= $next_question['id'] ?>" id="btnNextQuestion" class="badge_primary" style="display: none;">Next Question</a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <span class="mandatory">No Record.</span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>