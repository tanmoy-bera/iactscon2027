<?php
// all_leaderboard.php
include_once('includes/init.php');

page_header("Leaderboard");
$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

$searchArray[$pageKey]              = $pageKeyVal;
// today
?>

<div class="container">
    <h2>All Completed Quizzes Leaderboard</h2>

<?php
$sqlQuiz = [];
$sqlQuiz['QUERY'] = "SELECT q.id, q.title, q.start_time, q.marks_correct, q.marks_incorrect, q.participation_type, h.hall_title FROM rcg_quizzes q LEFT JOIN rcg_program_schedule_hall h ON h.id = q.hall_id WHERE q.status = ? ORDER BY q.start_time DESC";

$sqlQuiz['PARAM'][] = [
    'FILD' => 'status',
    'DATA' => 'completed',
    'TYP'  => 's'
];

$completedQuizzes = $mycms->sql_select($sqlQuiz);


if ($completedQuizzes) {
    foreach ($completedQuizzes as $quiz) {

        $quizId = $quiz['id'];

        // Count participants
        $partSql = [];
        $partSql['QUERY'] = "
            SELECT COUNT(*) AS total_participants
            FROM (
                SELECT DISTINCT user_id
                FROM rcg_quiz_answers
                WHERE quiz_id = ? AND user_id IS NOT NULL

                UNION

                SELECT DISTINCT device_id
                FROM quiz_guest_users
                WHERE latest_visit_quiz_id = ?
            ) AS participants
        ";
        $partSql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
        $partSql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
        $partRes = $mycms->sql_select($partSql);
        $participants = $partRes[0]['total_participants'] ?? 0;

        // Total questions
        $qSql = [];
        $qSql['QUERY'] = "SELECT COUNT(*) AS total_q FROM rcg_quiz_questions WHERE quiz_id=?";
        $qSql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
        $qRes = $mycms->sql_select($qSql);
        $total_q = $qRes[0]['total_q'] ?? 0;

        // Leaderboard Query
        $sql = [];
        $sql['QUERY'] = "
            SELECT 
                display_name,
                user_id,
                device_id,
                SUM(correct) AS correct_count,
                SUM(NOT correct) AS incorrect_count,
                SUM(total_time) AS total_time,
                SUM(marks_obtained) AS total_score
            FROM (
                SELECT 
                    COALESCE(u.user_full_name, g.guest_name, 'Guest') AS display_name,
                    a.user_id,
                    a.device_id,
                    (a.is_correct = 1) AS correct,
                    CASE WHEN a.is_correct = 1 THEN a.time_taken ELSE 0 END AS total_time,
                    a.marks_obtained
                FROM rcg_quiz_answers a
                LEFT JOIN quiz_guest_users g ON g.device_id = a.device_id
                LEFT JOIN rcg_user_registration u ON u.id = a.user_id
                WHERE a.quiz_id = ?
                AND g.latest_visit_quiz_id = ?

                UNION ALL

                SELECT 
                    COALESCE(g.guest_name, 'Guest') AS display_name,
                    NULL AS user_id,
                    g.device_id,
                    0 AS correct,
                    0 AS total_time,
                    0 AS marks_obtained
                FROM quiz_guest_users g
                WHERE g.latest_visit_quiz_id = ?
                AND g.device_id NOT IN (
                    SELECT DISTINCT device_id FROM rcg_quiz_answers WHERE quiz_id = ?
                )
            ) AS leaderboard
            GROUP BY user_id, device_id
            ORDER BY total_score DESC, correct_count DESC, total_time ASC;
        ";

        $sql['PARAM'][]=['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
        $sql['PARAM'][]=['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
        $sql['PARAM'][]=['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
        $sql['PARAM'][]=['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];

        $board=$mycms->sql_select($sql);
?>

    <!-- Collapsible Header for Quiz -->
    <div class="quiz-header" 
         style="cursor:pointer;background:#f1f1f1;padding:10px;border:1px solid #ccc;font-weight:bold;margin-top:20px;"
         onclick="toggleDetails('details_<?= $quizId ?>','icon_<?= $quizId ?>')">

        <?= htmlspecialchars($quiz['title']) ?>
        <span style="font-weight:normal;font-size:12px;">(<?= $participants ?> Participants)</span>
        <span id="icon_<?= $quizId ?>" style="float:right;">+</span>
    </div>

    <!-- Collapsible Content Section (Both Details & Leaderboard) -->
    <div id="details_<?= $quizId ?>" style="display:none; margin-bottom:10px;">
        
        <!-- Quiz Details Table -->
        <table width="100%" class="tborder" style="margin-top:10px;">
            <tr class="theader">
                <td colspan="4" align="left">Quiz Details</td>
            </tr>
            <tr class="tlisting">
                <td width="200">Hall</td>
                <td><?= $quiz['hall_title'] ?? '—' ?></td>
                <td width="200">Start Time</td>
                <td><?= date('d-m-Y H:i:s', strtotime($quiz['start_time'])) ?></td>
            </tr>
            <tr class="tlisting">
                <td>Correct Marks</td>
                <td><?= $quiz['marks_correct'] ?></td>
                <td>Incorrect Marks</td>
                <td><?= $quiz['marks_incorrect'] ?></td>
            </tr>
            <tr class="tlisting">
                <td>Total Questions</td>
                <td><?= $total_q ?></td>
                <td>Participants</td>
                <td><?= $participants ?></td>
            </tr>
        </table>

        <!-- Leaderboard Table -->
        <?php if ($board) { ?>
        <table width="100%" class="tborder" style="margin-top:10px;">
            <tr class="theader">
                <td width="50" align="center">Rank</td>
                <td width="200">Player</td>
                <td width="60" align="center">Correct</td>
                <td width="60" align="center">Incorrect</td>
                <td width="60" align="center">Score</td>
                <td width="80" align="center">Time (Sec)</td>
            </tr>

            <?php
            $rank=1;
            foreach($board as $u){
            ?>
            <tr class="tlisting">
                <td align="center"><?= $rank++ ?></td>
                <td><?= htmlspecialchars($u['display_name']) ?></td>
                <td align="center"><?= $u['correct_count'] ?></td>
                <td align="center"><?= $u['incorrect_count'] ?></td>
                <td align="center"><?= $u['total_score'] ?></td>
                <td align="center"><?= $u['total_time'] ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <p style='color:red;margin-top:10px;'>No participants found for this quiz.</p>
        <?php } ?>

    </div>

<?php
    }
?>



<?php
} else {
    echo "<div class='mandatory'>No Completed Quizzes Found!</div>";
}
?>

</div>

<script>
function toggleDetails(id, iconId) {
    var section = document.getElementById(id);
    var icon = document.getElementById(iconId);

    if (section.style.display === "none") {
        section.style.display = "block";
        icon.innerHTML = "−";
    } else {
        section.style.display = "none";
        icon.innerHTML = "+";
    }
}
</script>

<?php page_footer(); ?>