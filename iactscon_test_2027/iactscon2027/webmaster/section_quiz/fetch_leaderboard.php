<?php
include_once('includes/init.php');
$quizId = $_GET['quiz'] ?? 0;

// fetch questions list (for order)
$qs = [];
$qs['QUERY'] = "SELECT id FROM rcg_quiz_questions WHERE quiz_id=? AND row_status='A' ORDER BY round ASC, level ASC, id ASC";
$qs['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
$questions = $mycms->sql_select($qs);
$qIds = array_column($questions, 'id');

// Fetch quiz marking rule
$sqlMark = [];
$sqlMark['QUERY'] = "SELECT marks_correct, marks_incorrect FROM rcg_quizzes WHERE id=?";
$sqlMark['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
$markRule = $mycms->sql_select($sqlMark);
$marks_correct = $markRule[0]['marks_correct'] ?? 0;
$marks_incorrect = $markRule[0]['marks_incorrect'] ?? 0;

// Check if scoring enabled
$scoringEnabled = !($marks_correct == 0 && $marks_incorrect == 0);

// Leaderboard query — count on the answers table alone (no name-table JOIN = no fan-out),
// then attach display_name via scalar subqueries that can't multiply rows.
// $sql = [];
// $sql['QUERY'] = "
//     SELECT
//         s.user_id,
//         s.device_id,
//         s.correct_count,
//         s.total_time,
//         s.total_score,
//         CASE
//             WHEN s.user_id IS NOT NULL THEN (
//                 SELECT u.user_full_name
//                 FROM rcg_user_registration u
//                 WHERE u.id = s.user_id
//                 LIMIT 1
//             )
//             ELSE COALESCE((
//                 SELECT g.guest_name
//                 FROM quiz_guest_users g
//                 WHERE g.device_id = s.device_id
//                 LIMIT 1
//             ), 'Guest')
//         END AS display_name
//     FROM (
//         -- Part A: everyone who answered
//         SELECT
//             user_id,
//             device_id,
//             SUM(is_correct = 1)                                       AS correct_count,
//             SUM(CASE WHEN is_correct = 1 THEN time_taken ELSE 0 END)  AS total_time,
//             SUM(marks_obtained)                                       AS total_score
//         FROM rcg_quiz_answers
//         WHERE quiz_id = ?
//         GROUP BY user_id, device_id

//         UNION ALL

//         -- Part B: guests who joined but never answered
//         SELECT
//             NULL        AS user_id,
//             g.device_id,
//             0           AS correct_count,
//             0           AS total_time,
//             0           AS total_score
//         FROM quiz_guest_users g
//         WHERE g.latest_visit_quiz_id = ?
//           AND g.device_id NOT IN (
//               SELECT DISTINCT device_id
//               FROM rcg_quiz_answers
//               WHERE quiz_id = ? AND device_id IS NOT NULL
//           )
//         GROUP BY g.device_id
//     ) AS s
//     ORDER BY s.total_score DESC, s.correct_count DESC, s.total_time ASC
// ";
// $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // Part A
// $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // Part B latest_visit
// $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // Part B NOT IN
// $users = $mycms->sql_select($sql);

$sql = [];
$sql['QUERY'] = "
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
        -- Part A: everyone who answered
        SELECT
            user_id,
            device_id,
            SUM(is_correct = 1)                                       AS correct_count,
            SUM(CASE WHEN is_correct = 1 THEN time_taken ELSE 0 END)  AS total_time,
            SUM(marks_obtained)                                       AS total_score
        FROM rcg_quiz_answers
        WHERE quiz_id = ?
        GROUP BY user_id, device_id

        UNION ALL

        -- Part B: guests who joined but never answered
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
$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // display_name participation lookup
$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // Part A
$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // Part B latest_visit
$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP'=>'i'];   // Part B NOT IN
$users = $mycms->sql_select($sql);

// fetch each user answers
foreach ($users as $k => $u) {
    $idField = empty($u['user_id']) ? 'device_id' : 'user_id';
    $idValue = empty($u['user_id']) ? $u['device_id'] : $u['user_id'];
    $idType  = empty($u['user_id']) ? 's' : 'i';

    $as = [];
    $as['QUERY'] = "
        SELECT question_id, is_correct FROM rcg_quiz_answers
        WHERE quiz_id=? AND $idField=? ORDER BY question_id ASC";
    $as['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
    $as['PARAM'][] = ['FILD'=>$idField,'DATA'=>$idValue,'TYP'=>$idType];
    $ans = $mycms->sql_select($as);

    $ansMap = [];
    foreach ($ans as $row) {
        $ansMap[$row['question_id']] = $row['is_correct'];
    }
    $users[$k]['answers'] = $ansMap;
    $users[$k]['question_ids'] = $qIds;
}

$currentQ = isset($_GET['currentQ']) ? (int)$_GET['currentQ'] : 0;
echo json_encode([
    'leaderboard' => $users,
    'currentQ' => $currentQ,
    'scoringEnabled' => $scoringEnabled
]);
exit;