<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('includes/init.php');
echo $loggedUserID = $mycms->getLoggedUserId();
die();
// page_header("Live Quiz");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]                     = $pageKeyVal;
$searchArray['quiz']                        = $_REQUEST['quiz'];
$searchArray['next_question']               = $_REQUEST['next_question'];

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
$show = trim($_REQUEST['show']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCICON 2025</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>

<body>
    <?php
	switch ($show) {
		// show add window
		case 'live':
			liveQuiz($cfg, $mycms);
			break;
    }
    ?>
    <div class="container">
    <?php
    function liveQuiz($cfg, $mycms)
    {
	global $searchArray;
     ?>
        <div class="section-pad">
            <div class="header">
                <h1 class="logoname">CCICON <span>2025</span></h1>
                <img src="img/l1.png" alt="" class="divdr">
                <p class="texthead-sm">AUGUST 22-24. 2025 ITC ROYAL BENGAL</p>
                <img src="img/l2.png" alt="" class="divdr">
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
                require_once __DIR__ . '/includes/FirebaseService.php'; // include your FirebaseService class

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
            <div class="question_panel quiz_part">
                <div class="couinter_box">
                    <span class="countdown" id="timer">00</span>
                </div>
                <div class="questions_exams_row">
                    <div class="questions_list active" id="qstn1">
                        <div class="question_box">
                            <div class="d-qz-res-flex">
                                <p>Question <span class="attemt_count">1</span> of <span class="totalqt_count">7</span> | Round <span class="rountattempt_count"></span>1</span> of <span class="rounttotal_count">5</span></p>
                                <p><span class="plyer_count">255</span> Players Ready!</p>
                            </div>
                            <p class="qstn">Q. <?= $current_question['question_text'] ?></p>
                        </div>
                        <div class="grid-2 answer_panel">
                            <div class="col">
                                <input type="radio" id="q1a1" name="answr[]">
                                <label class="answer_box" for="q1a1">
                                    <p class="answrno">A</p>
                                    <p class="answr"><?= $current_question['option_a'] ?></p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a2" name="answr[]">
                                <label class="answer_box" for="q1a2">
                                    <p class="answrno">B</p>
                                    <p class="answr"><?= $current_question['option_b'] ?></p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a3" name="answr[]">
                                <label class="answer_box" for="q1a3">
                                    <p class="answrno">C</p>
                                    <p class="answr"><?= $current_question['option_c'] ?></p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a4" name="answr[]">
                                <label class="answer_box" for="q1a4">
                                    <p class="answrno">D</p>
                                    <p class="answr"><?= $current_question['option_d'] ?></p>
                                </label>
                            </div>

                        </div>
                    </div>
                    <!-- <div class="questions_list" id="qstn2">
                        <div class="question_box">
                            <div class="d-qz-res-flex">
                                <p>Question <span class="attemt_count">2</span> of <span class="totalqt_count">7</span> | Round <span class="rountattempt_count"></span>1</span> of <span class="rounttotal_count">5</span></p>
                                <p><span class="plyer_count">255</span> Players Ready!</p>
                            </div>
                            <p class="qstn">Q. WHICH OF THE FOLLOWING IS NOT A FUNCTION OF THE LIVER?</p>
                        </div>
                        <div class="grid-2 answer_panel">
                            <div class="col">
                                <input type="radio" id="q1a1" name="answr[]">
                                <label class="answer_box" for="q1a1">
                                    <p class="answrno">A</p>
                                    <p class="answr">DETOXIFICATION OF HARMFUL SUBSTANCES</p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a2" name="answr[]">
                                <label class="answer_box" for="q1a2">
                                    <p class="answrno">B</p>
                                    <p class="answr">PRODUCTION OF BILE</p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a3" name="answr[]">
                                <label class="answer_box" for="q1a3">
                                    <p class="answrno">C</p>
                                    <p class="answr">PRODUCTION OF RED BLOOD CELLS </p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a4" name="answr[]">
                                <label class="answer_box" for="q1a4">
                                    <p class="answrno">D</p>
                                    <p class="answr">STORAGE OF VITAMINS AND MINERALS </p>
                                </label>
                            </div>

                        </div>
                    </div>
                    <div class="questions_list" id="qstn3">
                        <div class="question_box">
                            <div class="d-qz-res-flex">
                                <p>Question <span class="attemt_count">3</span> of <span class="totalqt_count">7</span> | Round <span class="rountattempt_count"></span>1</span> of <span class="rounttotal_count">5</span></p>
                                <p><span class="plyer_count">255</span> Players Ready!</p>
                            </div>
                            <p class="qstn">Q. WHICH OF THE FOLLOWING IS NOT A FUNCTION OF THE LIVER?</p>
                        </div>
                        <div class="grid-2 answer_panel">
                            <div class="col">
                                <input type="radio" id="q1a1" name="answr[]">
                                <label class="answer_box" for="q1a1">
                                    <p class="answrno">A</p>
                                    <p class="answr">DETOXIFICATION OF HARMFUL SUBSTANCES</p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a2" name="answr[]">
                                <label class="answer_box" for="q1a2">
                                    <p class="answrno">B</p>
                                    <p class="answr">PRODUCTION OF BILE</p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a3" name="answr[]">
                                <label class="answer_box" for="q1a3">
                                    <p class="answrno">C</p>
                                    <p class="answr">PRODUCTION OF RED BLOOD CELLS </p>
                                </label>
                            </div>
                            <div class="col">
                                <input type="radio" id="q1a4" name="answr[]">
                                <label class="answer_box" for="q1a4">
                                    <p class="answrno">D</p>
                                    <p class="answr">STORAGE OF VITAMINS AND MINERALS </p>
                                </label>
                            </div>

                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="footer exam_footer">
            <div class="container">
                <div class="section-pad position-relative footer_flex">
                    <div class="left_footer">
                        <div class="left_f_logo">
                            <p>MANAGED BY</p>
                            <img src="img/Rueda New logo.png" alt="">
                        </div>
                    </div>
                    <div class="center_footer">
                        <div class="button_qstn">
                            <!-- <button type="button" class="btnqtsn btn_prev" data-id="1"><span>PREVIOUS QUESTION</span></button> -->
                             <!-- btn_next -->
                            <button type="button" class="btnqtsn " data-id="1">
                                <a href="<?= $cfg['SECTION_BASE_URL'] ?>/admin_quiz/quiz.php?show=live&quiz=<?= $searchArray['quiz'] ?>&next_question=<?= $next_question['id'] ?>">
                                    <span>NEXT QUESTION</span>
                                </button>
                            <!-- <button type="button" class="submit_exam"><span>SUBMIT EXAM</span></button> -->
                        </div>
                    </div>
                    <div class="right_footer">
                        <div class="right_f_content">
                            <div class="participent_no">
                                <p>TOTAL PARTICIPATION<br><span>625</span></p>
                            </div>
                            <div class="participent_qr">
                                <img src="img/qr.png" alt="">
                                <p>SCAN QR TO JOIN NOW</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
        <script src="js/jquery.min.js"></script>
        <script>
            // $(document).ready(function() {
            //     var qstnRow = $('.questions_exams_row');
            //     var qstnRowList = $('.questions_exams_row').find('.questions_list');
            //     var QuestionDivCount = $('.questions_list').length;
            //     $('.totalqt_count').html(QuestionDivCount);
            //     // alert(QuestionDivCount);

            //     $(document).on("click", ".btn_next", function() {
            //         var dataIdnxt = $(this).attr('data-id');
            //         var dataPrev = parseInt(dataIdnxt);
            //         var dataNext = parseInt(dataIdnxt) + parseInt(1);

            //         if (QuestionDivCount > dataNext) {
            //             $('.btn_prev').attr('data-id', dataPrev);
            //             $(this).attr('data-id', dataNext);
            //             qstnRowList.removeClass('active inactive');
            //             $(this).addClass('inactive');
            //             qstnRow.find('#qstn' + dataNext + "").addClass('active');
            //         }
            //         if (QuestionDivCount == dataNext) {
            //             $('.btn_prev').attr('data-id', dataPrev);
            //             $(this).attr('data-id', dataNext);
            //             qstnRowList.removeClass('active inactive');
            //             $(this).addClass('inactive');
            //             qstnRow.find('#qstn' + dataNext + "").addClass('active');
            //             $('.btn_next').hide();
            //             $('.submit_exam').show();

            //         }
            //     });

            //     $(document).on("click", ".btn_prev", function() {
            //         var dataIdprev = $(this).attr('data-id');
            //         var dataNext = parseInt(dataIdprev);
            //         var dataPrev = parseInt(dataIdprev) - parseInt(1);

            //         if (dataIdprev > 0) {
            //             qstnRowList.removeClass('active inactive');
            //             $(this).addClass('inactive');
            //             qstnRow.find('#qstn' + dataIdprev + "").addClass('active');
            //             $(this).attr('data-id', dataPrev);
            //             $('.btn_next').attr('data-id', dataNext);
            //             $('.btn_next').show();
            //             $('.submit_exam').hide();
            //         } else {
            //             $(this).attr('data-id', dataIdprev);
            //         }

            //     });

            // });
        </script>

        <script>
            //---------------Coundown Timer------------
            let totalSeconds = 20;
            let remaining = totalSeconds;

            function updateTimer() {
                if (remaining < 0) {
                    clearInterval(timerID);
                    document.getElementById('timer').textContent = "00";
                    alert("Time's up!");
                    return;
                }
                const formatted = remaining.toString().padStart(2, '0');
                document.getElementById('timer').textContent = formatted;
                remaining--;
            }

            updateTimer();
            const timerID = setInterval(updateTimer, 1000);
        </script>
</body>

</html>