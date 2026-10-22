<?php
include_once('includes/init.php');

page_header("Answer Breakdown");
?>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>

<div class="container">
	<?php
	switch ($show) {
		default:
			showAllQuiz($cfg, $mycms);
			break;
	}
	?>
</div>

<?php page_footer();


/*============== DYNAMIC ANSWER BREAKDOWN =============*/
function showAllQuiz($cfg, $mycms)
{
	$quizId = intval($_GET['quiz_id'] ?? 0);
	$userId = intval($_GET['user_id'] ?? 0);
	$deviceId = $_GET['device_id'] ?? '';

	if (!$quizId) {
		echo "<div class='mandatory'>Quiz ID Missing</div>";
		return;
	}

	// Player Name
	$playerName = "Player";
	if ($userId) {
		$ns = [];
		$ns['QUERY'] = "SELECT user_full_name FROM rcg_user_registration WHERE id=?";
		$ns['PARAM'][] = ['FILD' => 'id', 'DATA' => $userId, 'TYP' => 'i'];
		$nr = $mycms->sql_select($ns);
		$playerName = $nr[0]['user_full_name'] ?? "Player";
	} else if ($deviceId) {
		$ns = [];
		$ns['QUERY'] = "SELECT guest_name FROM quiz_guest_users WHERE device_id=? AND latest_visit_quiz_id=?";
		$ns['PARAM'][] = ['FILD' => 'device_id', 'DATA' => $deviceId, 'TYP' => 's'];
		$ns['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
		$nr = $mycms->sql_select($ns);
		$playerName = $nr[0]['guest_name'] ?? "Guest";
	}

	// Fetch Quiz Title
	$titleSql = [];
	$titleSql['QUERY'] = "SELECT title FROM rcg_quizzes WHERE id=?";
	$titleSql['PARAM'][] = ['FILD'=>'quiz_id','DATA'=>$quizId,'TYP'=>'i'];
	$titleRes = $mycms->sql_select($titleSql);
	$quizTitle = $titleRes[0]['title'] ?? "Quiz #$quizId";

	// Fetch ALL questions with user's answers (LEFT JOIN so skipped bhi ayega)
	$sql = [];
	$sql['QUERY'] = "
        SELECT 
            q.id AS question_id,
            q.question_text,
            qa.selected_option,
            qa.time_taken,
            qa.is_correct
        FROM rcg_quiz_questions q
        LEFT JOIN rcg_quiz_answers qa 
            ON q.id = qa.question_id
            AND qa.quiz_id = ?
            AND " . ($userId ? "qa.user_id=?" : "qa.device_id=?") . "
        WHERE q.quiz_id = ?
        ORDER BY q.id ASC
    ";

	$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
	if ($userId) {
		$sql['PARAM'][] = ['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i'];
	} else {
		$sql['PARAM'][] = ['FILD' => 'device_id', 'DATA' => $deviceId, 'TYP' => 's'];
	}
	$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];

	$questions = $mycms->sql_select($sql);
?>

	<table width="100%" align="center" class="tborder">
		<tr>
			<td colspan="6" class="tcat">
				<b><?=  $quizTitle ?? "Quiz" ?> - <?= $playerName ?></b> - Answer Breakdown
			</td>
		</tr>

		<tr class="theader">
			<td width="50" align="center">Sl No</td>
			<td align="left">Question</td>
			<td align="center">Your Answer</td>
			<td align="center">Correct Answer</td>
			<td align="center">Result</td>
			<td align="center">Time (sec)</td>
		</tr>

		<?php
		if (!$questions) {
			echo "<tr><td colspan='6' align='center' class='mandatory'>No Record Found</td></tr>";
		} else {
			$i = 1;
			foreach ($questions as $q) {

				// Correct Option Fetch
				$cSql = [];
				$cSql['QUERY'] = "SELECT option_text FROM rcg_quiz_question_options WHERE question_id=? AND is_correct=1 LIMIT 1";
				$cSql['PARAM'][] = ['FILD' => 'question_id', 'DATA' => $q['question_id'], 'TYP' => 'i'];
				$correctArr = $mycms->sql_select($cSql);
				$correctOption = $correctArr[0]['option_text'] ?? "-";

				// Fetch selected option TEXT if answered
				// Fetch selected option TEXT properly
				$userAns = "-";
				if ($q['selected_option'] !== null && $q['selected_option'] !== '') {
					$sSql = [];
					$sSql['QUERY'] = "SELECT option_text 
                      FROM rcg_quiz_question_options 
                      WHERE id=? LIMIT 1";
					$sSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $q['selected_option'], 'TYP' => 'i'];
					$sRes = $mycms->sql_select($sSql);
					$userAns = $sRes[0]['option_text'] ?? "-";
				}


				// Result text
				if ($q['selected_option'] === null || $q['selected_option'] === '') {
					$result = "<span>Skipped</span>";
				} else if ($q['is_correct']) {
					$result = "<span>Correct</span>";
				} else {
					$result = "<span>Wrong</span>";
				}
		?>
				<tr class="tlisting">
					<td align="center"><?= $i++ ?></td>
					<td><?= htmlspecialchars($q['question_text']) ?></td>

					<td align="center" style="<?= ($q['is_correct'] ? '' : ($q['selected_option'] ? '' : '')) ?>">
						<?= htmlspecialchars($userAns) ?>
					</td>

					<td align="center">
						<b style=""><?= htmlspecialchars($correctOption) ?></b>
					</td>

					<td align="center"><?= $result ?></td>
					<td align="center"><?= $q['time_taken'] ? round($q['time_taken'], 2) : "-" ?></td>
				</tr>

		<?php
			}
		} ?>
	</table>

<?php
}
?>