<?php
// === fetch_participants.php ===
include_once('includes/init.php');

$quizId = $_GET['quiz'] ?? 0;

// $sql = [];
// $sql['QUERY'] = "
//     SELECT COUNT(*) AS total_participants
//     FROM (
//         SELECT DISTINCT CONCAT('user_', user_id)
//         FROM rcg_quiz_answers
//         WHERE quiz_id = ? AND user_id IS NOT NULL AND user_id != ''

//         UNION

//         SELECT DISTINCT CONCAT('dev_', device_id)
//         FROM rcg_quiz_answers
//         WHERE quiz_id = ? AND device_id IS NOT NULL AND device_id != ''
//     ) AS participants
// ";
// $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
// $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];

// $participants = $mycms->sql_select($sql);

// echo json_encode([
//     'count' => $participants[0]['total_participants'] ?? 0
// ]);
// exit;


$sql = [];
$sql['QUERY'] = "
    SELECT COUNT(*) AS total_participants
    FROM (
        -- Registered users who answered in this quiz
        SELECT DISTINCT CONCAT('user_', user_id)
        FROM rcg_quiz_answers
        WHERE quiz_id = ? AND user_id IS NOT NULL AND user_id != ''

        UNION

        -- Guest devices where latest quiz is this quiz
        SELECT DISTINCT CONCAT('dev_', device_id)
        FROM quiz_guest_users
        WHERE latest_visit_quiz_id = ? 
          AND device_id IS NOT NULL 
          AND device_id != ''
    ) AS participants
";
$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];

$participants = $mycms->sql_select($sql);

echo json_encode([
    'count' => $participants[0]['total_participants'] ?? 0
]);
exit;
