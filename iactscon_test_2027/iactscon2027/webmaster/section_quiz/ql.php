<?php
include_once('includes/init.php');

$pageKey        = "_pgn_";
$pageKeyVal     = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];
@$searchString  = "";
$searchArray    = array();

$searchArray[$pageKey]        = $pageKeyVal;
$searchArray['quiz']          = $_REQUEST['quiz'];
$searchArray['next_question'] = $_REQUEST['next_question'];
$next_question                = $_REQUEST['next_question'] ?? '';

foreach ($searchArray as $searchKey => $searchVal) {
	$searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CCICON 2025</title>
	<link rel="stylesheet" href="admin_quiz/css/style.css?v=1">
	<link rel="stylesheet" href="admin_quiz/css/responsive.css?v=1">
</head>

<style>
	.answers {
		display: flex;
		gap: 10px;
		/* spacing between answers */
		overflow-x: auto;
		/* enable horizontal scroll */
		white-space: nowrap;
		/* prevent wrapping */
		scrollbar-width: thin;
		/* slim scrollbar for Firefox */
		scrollbar-color: #888 #f1f1f1;
		padding-bottom: 5px;
	}

	.answers::-webkit-scrollbar {
		height: 6px;
		/* height of horizontal scrollbar */
	}

	.answers::-webkit-scrollbar-track {
		background: #f1f1f1;
		border-radius: 10px;
	}

	.answers::-webkit-scrollbar-thumb {
		background: #888;
		border-radius: 10px;
	}

	.answers::-webkit-scrollbar-thumb:hover {
		background: #555;
	}

	.answer-icon {
		flex: 0 0 auto;
		/* prevent shrinking */
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 40px;
		height: 40px;
		border-radius: 50%;
		background-color: #f5f5f5;
	}

	.correct {
		color: green;
	}

	.incorrect {
		color: red;
	}
</style>

<body>
	<div class="container">
		<div class="section-pad">
			<div class="header">
				<h1 class="logoname">CCICON <span>2025</span></h1>
				<img src="admin_quiz/img/l1.png" alt="" class="divdr">
				<p class="texthead-sm">AUGUST 22-24. 2025 ITC ROYAL BENGAL</p>
				<img src="admin_quiz/img/l2.png" alt="" class="divdr">
				<p class="texthead-lg">6TH NATIONAL PULMONOLOGY CONFERANCE OF THE CHEST COUNCIL OF INDIA</p>
			</div>
			<h1 class="thank_msg">THANK YOU FOR YOUR PARTICIPATION !</h1>

			<div class="leaderboard">
				<div class="leaderboard_scroll">
					<?php
					global $mycms;

					$quizId = $searchArray['quiz']; // <-- set dynamically from request

					// Step 1: Get leaderboard aggregated per user
					$sql = [];
					$sql['QUERY'] = "
                    SELECT a.user_id, u.user_full_name,
                        SUM(CASE WHEN a.is_correct = 1 THEN 1 ELSE 0 END) AS correct_count,
                        SUM(CASE WHEN a.is_correct = 1 THEN a.time_taken ELSE 0 END) AS total_time
                    FROM rcg_quiz_answers a
                    INNER JOIN rcg_user_registration u ON u.id = a.user_id
                    WHERE a.quiz_id = ?
                    GROUP BY a.user_id, u.user_full_name
                    ORDER BY correct_count DESC, total_time ASC
                ";
					$sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
					// $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];

					$leaderboard = $mycms->sql_select($sql);

					if (!empty($leaderboard)) {
						$position = 1;
						foreach ($leaderboard as $user) {

							// Step 2: Fetch this user’s answers for this quiz
							$ansSql = [];
							$ansSql['QUERY'] = "
                            SELECT question_id, selected_option, is_correct
                            FROM rcg_quiz_answers
                            WHERE quiz_id = ? AND user_id = ?
                            ORDER BY question_id ASC
                        ";
							$ansSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
							$ansSql['PARAM'][] = ['FILD' => 'user_id', 'DATA' => $user['user_id'], 'TYP' => 'i'];
							// $ansSql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];
							$answers = $mycms->sql_select($ansSql);

							$isTop = ($position <= 3) ? 'checkcolor' : '';
					?>
							<div class="leaderboard-row <?= $isTop ?>">
								<span class="position">
									<?= $position ?><?= $position == 1 ? 'ST' : ($position == 2 ? 'ND' : ($position == 3 ? 'RD' : 'TH')) ?>
								</span>
								<span class="name"><?= htmlspecialchars($user['user_full_name']) ?></span>

								<div class="answers">
									<?php foreach ($answers as $ans) { ?>
										<span class="answer-icon <?= $ans['is_correct'] ? 'correct' : 'incorrect' ?>">
											<?php if ($ans['is_correct']) { ?>
												<!-- Correct -->
												<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
												</svg>
											<?php } else { ?>
												<!-- Incorrect -->
												<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
												</svg>
											<?php } ?>
										</span>
									<?php } ?>
									<?php for ($i = 0; $i < 10; $i++) { ?>
										<span class="answer-icon incorrect">
											<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
											</svg>
										</span>
									<?php } ?>
								</div>

								<span class="time"><?= number_format($user['total_time'], 2) ?> SEC</span>
							</div>
					<?php
							$position++;
						}
					} else {
						echo "<p>No answers yet.</p>";
					}
					?>
				</div>

				<?php if (!empty($next_question)) { ?>
					<div class="button_qz" style="margin-top: 30px;">
						<a href="<?= $cfg['SECTION_BASE_URL'] ?>live_quiz.php?show=live&quiz=<?= $quizId ?>&next_question=<?= $next_question ?>"
							class="btn_quiz">
							Proceed to Next Question
						</a>
					</div>
				<?php } else { ?>
					<div class="button_qz" style="margin-top: 30px;">
						<p class="text-center">
							🎉 Quiz Completed! No more questions left.
						</p>
						<button type="button" class="btn_quiz" data-click="modal" data-modal-id="#modal_answer">Modal Show</button>
					</div>
				<?php } ?>


			</div>
		</div>
	</div>

	<div class="footer">
		<div class="container">
			<div class="section-pad position-relative footer_flex">
				<div class="left_footer">
					<div class="left_f_logo">
						<p>MANAGED BY</p>
						<img src="admin_quiz/img/Rueda New logo.png" alt="">
					</div>
				</div>
				<div class="right_footer">
					<?php
					$partSql = [];
					$partSql['QUERY'] = "
                        SELECT COUNT(*) AS total_participants
                        FROM quiz_participation
                        WHERE quiz_id = ? AND status = 'Approved' AND row_status='A'
                    ";
					$partSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];

					$participants = $mycms->sql_select($partSql);
					$totalParticipants = $participants[0]['total_participants'] ?? 0;

					?>
					<div class="right_f_content">
						<div class="participent_no">
							<p>TOTAL PARTICIPATION<br><span> <?= $totalParticipants ?></span></p>
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


	<div class="modal" id="modal_answer">
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
					<div class="question_answer_list">

						<div class="leader_board_box checkcolor">
							<span class="pos">1</span>
							<span class="nm">Dipan Karmakar</span>
							<div class="ans_row">
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>

							</div>
							<span class="tm">10:05 SEC</span>
						</div>

						<div class="leader_board_box checkcolor">
							<span class="pos">2</span>
							<span class="nm">Dipan Karmakar</span>
							<div class="ans_row">
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>

							</div>
							<span class="tm">10:05 SEC</span>
						</div>

						<div class="leader_board_box">
							<span class="pos">3</span>
							<span class="nm">Dipan Karmakar</span>
							<div class="ans_row">
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>

							</div>
							<span class="tm">10:05 SEC</span>
						</div>

						<div class="leader_board_box">
							<span class="pos">4</span>
							<span class="nm">Dipan Karmakar</span>
							<div class="ans_row">
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>

							</div>
							<span class="tm">10:05 SEC</span>
						</div>

						<div class="leader_board_box">
							<span class="pos">5</span>
							<span class="nm">Dipan Karmakar</span>
							<div class="ans_row">
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>
								<span class="ansicon correct">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
									</svg>
								</span>
								<span class="ansicon incorrect">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</span>

							</div>
							<span class="tm">10:05 SEC</span>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="admin_quiz/js/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			$(document).on("click", '[data-click="modal"]', function() {
				var modalId = $(this).data("modal-id");
				/* console.log("Modal ID:", modalId); */
				$(modalId).addClass("open");
			});

			$(document).on("click", ".modal-close", function() {
				$(this).closest(".modal").removeClass("open"); // close modal
			});


		});
	</script>
</body>

</html>