<?php
include_once('..includes/init.php');

page_header("Leaderboard");

$quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

if (!$quizId) {
    echo "<div class='mandatory'>Invalid Quiz</div>";
    page_footer();
    exit;
}

// Fetch total questions
$qSql = [];
$qSql['QUERY'] = "SELECT COUNT(*) AS total_q FROM rcg_quiz_questions WHERE quiz_id=?";
$qSql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
$qRes = $mycms->sql_select($qSql);
$total_q = $qRes[0]['total_q'] ?? 0;

// Fetch Quiz Title
$titleSql = [];
$titleSql['QUERY'] = "SELECT title FROM rcg_quizzes WHERE id=?";
$titleSql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
$titleRes = $mycms->sql_select($titleSql);
$quizTitle = $titleRes[0]['title'] ?? "Quiz #$quizId";

// Leaderboard Query
$sql = [];
$sql['QUERY'] = "
    SELECT
        s.user_id,
        s.device_id,
        s.correct_count,
        s.incorrect_count,
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
            SUM(is_correct = 0)                                        AS incorrect_count,
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
            0           AS incorrect_count,
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
$sql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];   // group lookup (NEW — must be first)
$sql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];   // Part A
$sql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];   // Part B latest_visit
$sql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];   // Part B NOT IN
$board = $mycms->sql_select($sql);
?>

<div class="container">

    <?php if ($board) { ?>
    <table width="100%" class="tborder">
		<tr>
			<td colspan="6" align="left" class="tcat">
				<span style="float:left"><?=  $quizTitle ?? "Quiz" ?> - Leaderboard</span>
				<span class="tsearchTool" forType="tsearchTool"></span>
			</td>
		</tr>
        <tr class="theader">
            <td width="30" align="center">Rank</td>
            <td>Name</td>
            <td width="120" align="center">Total Question</td>
            <td width="100" align="center">Correct</td>
            <td width="100" align="center">Incorrect</td>
            <td width="150" align="center">Total Time Taken</td>
        </tr>

        <?php  
        $rank = 1;
        foreach ($board as $u) { ?>
        <tr class="tlisting">
            <td align="center"><?= $rank++ ?></td>
            <td align="left">
			<a href="player_details.php?quiz_id=<?= $quizId ?>
				<?php if (!empty($u['user_id'])) { ?>
					&user_id=<?= $u['user_id'] ?>
				<?php } else { ?>
					&device_id=<?= $u['device_id'] ?>
				<?php } ?>
			">
				<?= htmlspecialchars($u['display_name']) ?>
			</a>
		</td>
            <td align="center"><?= $total_q ?></td>
            <td align="center"><?= $u['correct_count'] ?></td>
            <td align="center"><?= $u['incorrect_count'] ?></td>
            <td align="center"><?= $u['total_time'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <?php } else { ?>
        <p style='color:red;'>No participants found.</p>
    <?php } ?>

</div>

<?php page_footer(); ?>
