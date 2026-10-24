<?php
	include_once('includes/init.php');
include_once('includes/fcm-helper.php');
    require_once __DIR__ . '/phpqrcode/qrlib.php';

$searchArray['quiz']       		= $_REQUEST['quiz'];
// echo $searchArray['quiz'];
// die();

$quizId = $searchArray['quiz'];

// Check if there are active participants
$checkParticipants = [];
$checkParticipants['QUERY'] = "SELECT COUNT(DISTINCT m.user_id) as participant_count
                               FROM quiz_participation p
                               JOIN rcg_quiz_participation_members m 
                                    ON m.participation_id = p.id
                               WHERE p.quiz_id = ?
                               AND p.status = 'Approved'
                               AND m.status = 'Active'";

$checkParticipants['PARAM'][] = [
    'FILD' => 'quiz_id',
    'DATA' => $quizId,
    'TYP'  => 'i'
];

$result = $mycms->sql_select($checkParticipants);
$participantCount = !empty($result) ? (int)$result[0]['participant_count'] : 0;

if ($participantCount == 0) {
    $mycms->redirect("quiz.php?m=Cannot start quiz. No active participants found.");
    exit();
}

function notifyQuizParticipants($quizId, $quizName)
{
    global $mycms;

    // --- Get all participants (user_ids) ---
    $users = [];
    $users['QUERY'] = "SELECT DISTINCT m.user_id
                       FROM quiz_participation p
                       JOIN rcg_quiz_participation_members m 
                            ON m.participation_id = p.id
                       WHERE p.quiz_id = ?
                       AND p.status = 'Approved'
                       AND m.status = 'Active'";

    $users['PARAM'][] = [
        'FILD' => 'quiz_id',
        'DATA' => $quizId,
        'TYP'  => 'i'
    ];

    $userList = $mycms->sql_select($users);

    if (empty($userList)) return;

    foreach ($userList as $u) {

        $userId = $u['user_id'];

        // --- Get tokens ---
        $tokens = [];
        $tokens['QUERY'] = "SELECT token FROM rcg_user_fcm_tokens WHERE user_id = ?";
        $tokens['PARAM'][] = [
            'FILD' => 'user_id',
            'DATA' => $userId,
            'TYP'  => 'i'
        ];

        $tokenList = $mycms->sql_select($tokens);

        foreach ($tokenList as $t) {

            $message = [
                "token" => $t['token'],
                "notification" => [
                    "title" => "Quiz Starting",
                    "body"  => $quizName . " is starting in a few seconds!"
                ],
                "data" => [
                    "type" => "quiz_start",
                    "quiz_id" => (string)$quizId
                ]
            ];

            $response = sendFCM($message);

            // --- Remove invalid tokens ---
            if (
                strpos($response, 'UNREGISTERED') !== false ||
                strpos($response, 'INVALID_ARGUMENT') !== false
            ) {

                $del = [];
                $del['QUERY'] = "DELETE FROM rcg_user_fcm_tokens WHERE token = ?";
                $del['PARAM'][] = [
                    'FILD' => 'token',
                    'DATA' => $t['token'],
                    'TYP'  => 's'
                ];
                $mycms->sql_delete($del);
            }
        }
    }
}

$sql 	=	array();
$sql['QUERY']    = "SELECT * FROM `rcg_quizzes`  
                    WHERE `id` = ?";
$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $_REQUEST['quiz'], 'TYP' => 's');
$result			 = $mycms->sql_select($sql);
$row    		 = $result[0];

$quiz_details = $row;
notifyQuizParticipants($row['id'], $row['title']);

// for redirect if completed
if(!empty($result)) {
    $quiz_details = $result[0];

    // If status is completed → redirect
    if (isset($quiz_details['status']) && $quiz_details['status'] == 'completed') {
        header("Location: ".$cfg['SECTION_BASE_URL']."quiz.php");
        exit;
    }
}

// echo'<pre>';
// print_r($quiz_details);
// echo'</pre>';
// die();

// AUTO GENERATE QR IF ENABLED
if ($quiz_details['enable_qr'] == 'yes') {
    

    // URL to open on QR scan
    $qrData = $cfg['SECTION_BASE_URL'] . "quiz-process.php?quiz_id=" . $quiz_details['id'] . "&modal=1";

    // Directory for QR
    $qrDir = __DIR__ . '/upload/qr_code/';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0777, true);
    }

    $qrFileName = 'qr_quiz_' . $quiz_details['id'] . '.png';
    $qrFullPath = $qrDir . $qrFileName;

    // Generate QR only if missing

    // Generate QR only if missing
    // if (!file_exists($qrFullPath)) {
        QRcode::png($qrData, $qrFullPath, QR_ECLEVEL_L, 6);

        // Store path in DB for display
        $qrImageLink = 'upload/qr_code/' . $qrFileName;

        $updateSql = array();
        $updateSql['QUERY'] = "UPDATE `rcg_quizzes` SET `qr_image_link` = ? WHERE `id` = ?";
        $updateSql['PARAM'][] = array('FILD' => 'qr_image_link', 'DATA' => $qrImageLink, 'TYP' => 's');
        $updateSql['PARAM'][] = array('FILD' => 'id', 'DATA' => $quiz_details['id'], 'TYP' => 's');
        $mycms->sql_update($updateSql);

        // Show immediately on first load
        $quiz_details['qr_image_link'] = $qrImageLink;
    // }

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
</head>

<body>
    <div class="container">
        <div class="section-pad">
            <div class="header">
                <h1 class="logoname">CCICON <span>2025</span></h1>
                <img src="section_quiz/admin_quiz/img/l1.png" alt="" class="divdr">
                <p class="texthead-sm">AUGUST 22-24. 2025 ITC ROYAL BENGAL</p>
                <img src="section_quiz/admin_quiz/img/l2.png" alt="" class="divdr">
                <p class="texthead-lg">6TH NATIONAL PULMONOLOGY CONFERANCE OF THE CHEST COUNCIL OF INDIA</p>
            </div>
            <div class="use_code">
                <!-- <h6>Join the quiz | use code 100000</h6> -->
            </div>
            <div class="startquiz_sec">
                <div class="content_qz">
                    <h1>WELCOME TO THE<br>CCICON 2025 QUIZ</h1>
                    <p>Embrace yourself for the<br> academic quest!</p>
                </div>
                <div class="qr_qz">
                    <div class="qr_scan">
                        <!-- <img src="admin_quiz/img/qr2.png" class="img-fluid" alt=""> -->
                         <?php if (!empty($quiz_details['qr_image_link'])): ?>
                            <img src="<?= $cfg['SECTION_BASE_URL'] ?>/<?= $quiz_details['qr_image_link']; ?>" class="img-fluid" alt="Quiz QR Code">
                        <?php else: ?>
                            <img src="admin_quiz/img/qr2.png" class="img-fluid" alt="QR Coming Soon">
                        <?php endif; ?>
                        <p>SCAN QR TO JOIN <br>NOW!</p>
                    </div>
                </div>
            </div>
            <div class="button_qz">
                <a href="<?= $cfg['SECTION_BASE_URL'] ?>live_quiz.php?show=live&quiz=<?=$searchArray['quiz']?>" class="btn_quiz">START QUIZ</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="section-pad position-relative footer_flex">
                <div class="left_footer">
                    <div class="left_f_logo">
                        <p>MANAGED BY</p>
                        <img src="section_quiz/admin_quiz/img/Rueda New logo.png" alt="">
                    </div>
                </div>
                <div style="display: none;" class="right_footer">
                    <div class="right_f_content">
                        <div class="participent_no">
                            <p>TOTAL PARTICIPATION<br><span>625</span></p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="admin_quiz/js/jquery.min.js"></script>
</body>

</html>