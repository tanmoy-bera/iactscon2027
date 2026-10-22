<?php
include_once('includes/init.php');

page_header("Live Quiz");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]                 	= $pageKeyVal;
$searchArray['quiz']       		= $_REQUEST['quiz'];
$searchArray['next_question']       		= $_REQUEST['next_question'];

foreach ($searchArray as $searchKey => $searchVal) {
	$searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
	<?php
	switch ($show) {
		// show add window
		case 'live':
			liveQuiz($cfg, $mycms);
			break;
		// show edit window	
		case 'edit':
			editQuiz($cfg, $mycms);
			break;
		// view details record
		case 'view':
			viewQuiz($cfg, $mycms);
			break;
		// show all record	
		default:
			showAllQuiz($cfg, $mycms);
			break;
	}
	?>
</div>
<?php
page_footer();

function showAllQuiz($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmCountryListing" id="frmCountryListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_country.process.php" method="post">
		<input type="hidden" name="act" id="act" value="search_country" />
		<input type="hidden" id="excelPrintVal" name="excelPrintVal" />
		<table width="100%" align="center" class="tborder">
			<tr>
				<td colspan="2" align="left" class="tcat">
					<span style="float:left">Quiz Listing</span>
					<span class="tsearchTool" forType="tsearchTool"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<!-- <div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Country Name:
										<input type="text" name="start_date" id="start_date" 
										 value="<?= $_REQUEST['start_date'] ?>" />                        
										&nbsp;
										<?php
										searchStatus();
										?>
										<input type="submit" name="searchBtn" id="searchBtn" value="Search" class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
						</div> -->

					<table width="100%">
						<tr class="theader">
							<td width="50" align="center">Sl No </td>
							<td width="100" align="left">Quiz Title</td>
							<td width="50" align="center">Rounds</td>
							<td width="100" align="center">Levels(In each Round)</td>
							<td width="70" align="center">Start Time</td>
							<td width="70" align="center">Action</td>
						</tr>
						<?php
						$con = "";
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

						$i = 0;

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

						if ($result) {
							foreach ($result as $i => $row) {
								@$i++;
						?>
								<tr class="tlisting">
									<td align="center"><?= $i + ($_REQUEST['_pgn_'] * 50) ?></td>
									<td><?= $row['title'] ?></td>
									<td align="center"><?= $row['rounds'] ?></td>
									<td align="center"><?= $row['levels'] ?></td>
									<td align="center"><?php echo date('d-m-Y H:i:s', strtotime($row['start_time'])); ?></td>

									<td align="center">
										<button type="button">
											<a href="<?= $cfg['SECTION_BASE_URL'] ?>main_round_leaderboard.php?quiz_id=<?= $row['id'] ?>"> Show Leaderboard </a>
										</button>
									</td>
								</tr>
							<?php }
						} else { ?>
							<tr>
								<td colspan="10" align="center">
									<span class="mandatory">No Record.</span>
								</td>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
		</table>
	</form>
<?php
}
/*============== SHOW ALL COUNTRY WINDOW =============*/
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
	<form name="frmCountryListing" id="frmCountryListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_country.process.php" method="post">
		<input type="hidden" name="act" id="act" value="search_country" />
		<input type="hidden" id="excelPrintVal" name="excelPrintVal" />
		<table width="100%" align="center" class="tborder">
			<tr>
				<td colspan="2" align="left" class="tcat">
					<span style="float:left">Quiz</span>
					<span class="tsearchTool" forType="tsearchTool"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<!-- <div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Country Name:
										<input type="text" name="src_country_name" id="src_country_name" 
										 value="<?= $_REQUEST['src_country_name'] ?>" />                        
										&nbsp;
										<?php
										searchStatus();
										?>
										<input type="submit" name="searchBtn" id="searchBtn" value="Search" class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
						</div> -->

					<table width="100%">
						<tr class="theader">
							<td colspan="2">
								Q: <?= $current_question['question_text'] ?>
							</td>
						</tr>
						<tr class="tlisting">
							<td width="50%">A. <?= $current_question['option_a'] ?></td>
							<td width="50%">B. <?= $current_question['option_b'] ?></td>
						</tr>
						<tr class="tlisting">
							<td width="50%">C. <?= $current_question['option_c'] ?></td>
							<td width="50%">D. <?= $current_question['option_d'] ?></td>
						</tr>

						<!-- <tr>
                                <td colspan="12" align="center">
                                    <span class="mandatory">No Record.</span>
                                </td>
                            </tr>   -->
					</table>

				</td>
			</tr>
			<tr>
				<td width="50%" align="left">Time Remaining: <span id="timer"></span></td>
				<td width="50%" align="right">
					<?php if ($next_question) { ?>
						<button type="button" id="btnNextQuestion" style="display: none;">
							<a href="<?= $cfg['SECTION_BASE_URL'] ?>quizstart.php?quiz=<?= $searchArray['quiz'] ?>&next_question=<?= $next_question['id'] ?>"> Next Question </a>
						</button>
					<?php } ?>
				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">

				</td>
			</tr>
		</table>
	</form>
<?php
}
?>
<script>
	window.onload = function() {
		let timeLeft = 20; // in seconds
		const timerElement = document.getElementById('timer');
		const nextBtn = document.getElementById('btnNextQuestion');

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
	};
</script>