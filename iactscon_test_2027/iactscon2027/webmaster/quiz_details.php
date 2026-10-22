<?php
include_once('includes/init.php');

// =======================
// AJAX: Load Options
// =======================
if (isset($_GET['ajax']) && $_GET['ajax'] === 'load_options') {

	header('Content-Type: application/json');

	$question_id = intval($_GET['question_id'] ?? 0);

	if ($question_id <= 0) {
		echo json_encode([], JSON_UNESCAPED_UNICODE);
		exit;
	}

	$sql = [];
	$sql['QUERY'] = "SELECT option_text, is_correct 
                     FROM rcg_quiz_question_options
                     WHERE question_id = ?
                     ORDER BY id ASC";
	$sql['PARAM'][] = [
		'FILD' => 'question_id',
		'DATA' => $question_id,
		'TYP'  => 'i'
	];

	$data = $mycms->sql_select($sql);

	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}


page_header("Quiz");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

date_default_timezone_set('Asia/Kolkata');
$searchArray[$pageKey]                 	= $pageKeyVal;
$searchArray['start_date']       		= $_REQUEST['start_date'];

foreach ($searchArray as $searchKey => $searchVal) {
	$searchString .= "&" . $searchKey . "=" . $searchVal;
}




?>
<link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ . "webmaster/util/" ?>font-awesome-4.7.0/css/font-awesome.min.css" />
<style>
	.modal-backdrop {
		z-index: 1040 !important;
	}

	.modal {
		z-index: 1050 !important;
	}

	.modal-dialog {
		z-index: 1060 !important;
		pointer-events: auto !important;
	}
	.correct-option {
    background: #d4f8d4;
}

</style>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
	<?php
	switch ($show) {
		// show add window
		case 'add':
			// addQuiz($cfg, $mycms);
			break;
		default:
			quiz_details($cfg, $mycms);
			break;
	}
	?>
</div>
<?php
page_footer();

/*================= EDIT COUNTRY WINDOW ==============*/
function quiz_details($cfg, $mycms)
{
	global $searchArray, $searchString;

	$sql 	=	array();
	$sql['QUERY']    = "SELECT * FROM `rcg_quizzes`  
						   WHERE `id` = ?";
	$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $_REQUEST['quiz_id'], 'TYP' => 's');
	$result			 = $mycms->sql_select($sql);
	$row    		 = $result[0];
?>
	<!-- <form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();"> -->
	<input type="hidden" name="act" value="edit_quiz" />
	<input type="hidden" name="quiz_id" id="quiz_id" value="<?= $_REQUEST['quiz_id'] ?>" />
	<?php
	foreach ($searchArray as $key => $val) {
	?>
		<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
	<?php
	}
	?>
	<table width="100%" class="tborder">
		<thead>
			<tr>
				<td colspan="2" align="left" class="tcat">Quiz Details

					<a style="float: right;" href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>">
						<input type="button" name="BackEdit" id="BackEdit" value="Back" class="btn btn-small btn-red" /></a>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2" align="center" style="margin:0px; padding:0px;">

					<table width="100%">
						<!-- <tr class="thighlight"> -->
						<!-- <td colspan="2" align="left">Quiz Details</td> -->
						<!-- </tr> -->
						<tr>
							<td width="15%" align="left">
								Quiz Title:
							</td>
							<td width="35%" align="left">
								<?= $row['title']; ?>
							</td>
							<td width="15%" align="left">
								Hall:
							</td>
							<td width="35%" align="left">
								<?php
								$sql_hall = [];
								$sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall 
														WHERE status = 'A' AND `id`=?
														ORDER BY hall_title ASC";
								$sql_hall['PARAM'][] = [
									'FILD' => 'id',
									'DATA' => $row['hall_id'],
									'TYP'  => 'i'
								];

								$quiz_halls = $mycms->sql_select($sql_hall);
								$hrow    		 = $quiz_halls[0];
								?>
								<?= $hrow['hall_title'] ?? '' ?>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Quiz Status:
							</td>
							<td width="35%" align="left">
								<?= ucfirst($row['status']) ?>
							</td>
							<td width="15%" align="left">
								Start Time:
							</td>
							<td width="35%" align="left">
								<?php echo date('d-m-Y H:i:s', strtotime($row['start_time'])); ?>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Rounds:
							</td>
							<td width="35%" align="left">
								<?= $row['rounds'] ?>
							</td>
							<td width="15%" align="left">
								Levels:
							</td>
							<td width="35%" align="left">
								<?= $row['levels'] ?>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Answer Time:
							</td>
							<td width="35%" align="left">
								<?= $row['answer_time'] ?> sec.
							</td>
							<td width="15%" align="left">
								Buffer Time:
							</td>
							<td width="35%" align="left">
								<?= $row['buffer_time'] ?> sec.
							</td>
						</tr>

						<tr>
							<td width="15%" align="left">
								View Answer:
							</td>
							<td width="35%" align="left">
								<?php
								if ($row['view_answer'] == 'end') {
									echo 'At The End of The Quiz';
								} elseif ($row['view_answer'] == 'question') {
									echo 'After Each Question';
								} elseif ($row['view_answer'] == 'round') {
									echo 'After Each Round';
								} elseif ($row['view_answer'] == 'level') {
									echo 'After Each Level';
								}
								?>
							</td>
							<td width="15%" align="left">
								QR Code:
							</td>
							<td width="35%" align="left">
								<?= ($row['enable_qr'] == 'yes') ? 'Enabled' : 'Disabled'; ?>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Seat Limit:
							</td>
							<td width="35%" align="left">
								<?= $row['seat_limit']; ?>
							</td>
							<td width="15%" align="left">
								Application Closing Time:
							</td>
							<td width="35%" align="left">
								<?= $row['application_close_time']; ?>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Application Status:
							</td>
							<td width="35%" align="left">
								<?php
								if ($row['application_status'] == 'active' && strtotime(date('Y-m-d H:i:s')) > strtotime($row['application_close_time'])) {
									$application_status = 'Closed';
								} else {
									$application_status = ucfirst($row['application_status']);
								}
								?>
								<?= htmlspecialchars($application_status); ?>

							</td>
							<td width="15%" align="left">
								Instruction Bar
							</td>
							<td width="35%" align="left">
								<?= ($row['enable_instruction_bar'] == 'yes') ? 'Enabled' : 'Disabled'; ?>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Correct Answer(Marks):
							</td>
							<td width="35%" align="left">
								<?= $row['marks_correct']; ?>
							</td>
							<td width="15%" align="left">

							</td>
							<td width="35%" align="left">

							</td>
						</tr>
						<tr>
							<td width="15%" align="left">
								Incorrect Answer(Marks):
							</td>
							<td width="35%" align="left">
								<?= $row['marks_incorrect']; ?>
							</td>
							<td width="15%" align="left">


							</td>
							<td width="15%" align="right">
								<form id="importQuestionForm" enctype="multipart/form-data" method="post">
									<input type="hidden" name="import_quiz_id" value="<?= $_REQUEST['quiz_id'] ?>">

									<div class="form-group" style="display: flex; align-items: center; gap: 10px;">
										<input type="file" name="question_file" id="question_file" accept=".csv" required>
										<button type="submit" class="btn btn-small btn-blue">Upload Questions</button>

										&nbsp;
										<a href="templates/import-question-template.csv" class="btn btn-small btn-blue" download="import-question-template.csv"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#0066FF;" title="Download Sample Excel"></i></a>
									</div>
								</form>

							</td>
						</tr>

					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<form name="frmQuestionListing" id="frmQuestionListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php" method="post">
		<input type="hidden" name="act" id="act" value="search_Question" />
		<input type="hidden" name="fr" id="fr" value="1" />
		<input type="hidden" id="excelPrintVal" name="excelPrintVal" />
		<table width="100%" align="center" class="tborder">
			<?php
			$sql_quiz = [];
			$sql_quiz['QUERY'] = "SELECT * FROM rcg_quizzes 
								WHERE id = ? 
								AND status = 'active' 
								AND row_status = 'A'
								ORDER BY id ASC";
			$sql_quiz['PARAM'][] = ['FILD' => 'id', 'DATA' => $_REQUEST['quiz_id'], 'TYP' => 'i'];

			$quiz_details = $mycms->sql_select($sql_quiz);
			$quiz_details = $quiz_details[0];
			?>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%">
						<tr class="theader">
							<td width="30" align="center">
								<input name="check_all" id="check_all" targetElement="checkBoxListing"
									type="checkbox" onclick="checkall(this);" />
							</td>
							<td width="50" align="center">Sl No </td>
							<td width="100" align="center">Question</td>
							<!-- <td width="50" align="center">Option A</td>
							<td width="50" align="center">Option B</td>
							<td width="50" align="center">Option C</td>
							<td width="50" align="center">Option D</td>
							<td width="100" align="center">Correct Option</td> -->
							<td width="250" align="center">Options</td>
							<td width="100" align="center">Extra Time</td>
							<td width="100" align="center">Is Last</td>
							<td width="50" align="center">Status</td>
							<td width="100" align="center">Action</td>
						</tr>

						<?php
						$con = "";
						$params = [];

						if (!empty($_REQUEST['quiz_id'])) {
							$con .= " AND quiz_id = ?";
							$params[] = [
								'FILD' => 'quiz_id',
								'DATA' => $_REQUEST['quiz_id'],
								'TYP'  => 'i'
							];
						}

						$sql = [];
						$sql['QUERY'] = "SELECT id, quiz_id, level, round, question_text, media_url, option_a, option_b, option_c, option_d, correct_option, timer_override , row_status, status, is_last_question
						FROM rcg_quiz_questions 
						WHERE row_status != ? 
						$con 
						ORDER BY round ASC, level ASC, id ASC";

						$sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];

						foreach ($params as $p) {
							$sql['PARAM'][] = $p;
						}

						// $questionList = $mycms->sql_select($sql);

						$questionList = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);

						$sl = 1;
						$i = 0;
						if (!empty($questionList)) {
							foreach ($questionList as $i => $q) {
								$i++;
						?>
								<tr>
									<td align="center">
										<!-- <input type="checkbox" name="checkvalue" forType="checkBoxListing" value="<?= $q['id'] ?>" /> -->
										<input type="checkbox" name="selected_ids[]" id="checkvalue" class="checkBoxListing" forType="checkBoxListing" value="<?= $q['id'] ?>" />
									</td>
									<!-- <td><?= $sl++ ?></td> -->
									<td><?= $i + ($_REQUEST['_pgn_'] * 15) ?></td>
									<td><?= htmlspecialchars($q['question_text']) ?></td>
									<!-- <td align="center">< htmlspecialchars($q['option_a']) ?></td>
									<td align="center">< htmlspecialchars($q['option_b']) ?></td>
									<td align="center">< htmlspecialchars($q['option_c']) ?></td>
									<td align="center">< htmlspecialchars($q['option_d']) ?></td>
									<td align="center">< strtoupper($q['correct_option']) ?></td> -->
									<td align="center">
										<button type="button"
											class="btn btn-small btn-blue btn-view-options"
											data-id="<?= $q['id'] ?>"
											onclick="event.stopPropagation();">
											<i class="fa fa-eye"></i>
										</button>
									</td>

									<td align="center"><?= ($q['timer_override']) ? $q['timer_override'] . ' sec.' : '' ?></td>
									<!-- <td align="center"><?= ucfirst($q['status']) ?></td> -->
									<td align="center"><?= ($q['is_last_question'] == '1') ? 'Yes' : 'No'; ?></td>
									<td align="center">
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php?fr=1&quiz_id=<?= $_REQUEST['quiz_id'] ?>&act=<?= ($q['row_status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $q['id'] ?><?= $searchString ?>"
											class="<?= ($q['row_status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($q['row_status'] == 'A') ? 'Active' : 'Inactive' ?></a>
									</td>
									<td align="center">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $cfg['SECTION_BASE_URL'] ?>manage_questions.php?fr=1&quiz_id=<?= $_REQUEST['quiz_id'] ?>&show=edit&question_id=<?= $q['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'edit.png' ?>" title="Edit" class="icon-pen">
										</a>
										&nbsp;
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php?fr=1&quiz_id=<?= $_REQUEST['quiz_id'] ?>&act=Remove&id=<?= $q['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'drop.png' ?>" title="Remove" class="icon-trash-stroke"
												onClick="return confirm('Do you really want to remove this record');">
										</a>
									</td>
								</tr>
							<?php
							}
						} else {
							?>
							<tr>
								<td colspan="11" align="center">No questions found.</td>
							</tr>
						<?php
						}
						?>
					</table>

				</td>
			</tr>
			<tr>
				<td width="50%" align="left">
					<select name="multiOperationSelector" id="multiOperationSelector">
						<option value="">Choose an action...</option>
						<option value="Active">Active</option>
						<option value="Inactive">Inactive</option>
						<option value="Remove">Remove</option>
					</select>
					<input value="Apply to selected" name="applyMultiAction" type="button"
						class="btn btn-small btn-blue" id="applyMultiActionBtn" />
				</td>
				<td style="display: none;" width="50%" align="right">
					<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_questions.php?show=add&fr=1&quiz_id=<?= $_REQUEST['quiz_id'] ?><?= $searchString ?>">Add New Question</a>
				</td>
				<td width="50%" align="right">
					<a href="<?= $cfg['SECTION_BASE_URL'] ?>new_quiz_updated.php?quiz_id=<?= $_REQUEST['quiz_id'] ?>">Add New Question</a>
				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	</form>

	<div id="optionsModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" style="width: 450px;">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>

					<h4 class="modal-title">Options</h4>
				</div>

				<div class="modal-body" id="optionsList">
					Loading...
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
				</div>

			</div>
		</div>
	</div>



	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

	<script>
		// Select/Deselect All
		$('#check_all').on('click', function() {
			$('.checkBoxListing').prop('checked', this.checked);
		});
		// Apply Button Click
		$('#applyMultiActionBtn').on('click', function() {
			let action = $('#multiOperationSelector').val();
			let selected = $('.checkBoxListing:checked').map(function() {
				return $(this).val();
			}).get();

			if (action === '') {
				alert('Please choose an action.');
				return;
			}
			if (selected.length === 0) {
				alert('Please select at least one quiz.');
				return;
			}

			console.log(selected);
			// return;
			if (confirm(`Are you sure you want to ${action} the selected quizzes?`)) {
				$.ajax({
					url: 'manage_question.process.php',
					type: 'POST',
					data: {
						act: 'MultiAction',
						action: action,
						ids: selected
					},
					success: function(response) {
						console.log(response);
						location.reload(); // refresh after success
					}
				});
			}
		});


		document.querySelectorAll('.btn-view-options').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				e.preventDefault();

				const questionId = this.getAttribute('data-id');
				loadOptions(questionId);
			});
		});

		// Close button functionality fix
		document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
			btn.addEventListener('click', function() {
				let modalEl = document.getElementById('optionsModal');
				let modalObj = bootstrap.Modal.getInstance(modalEl);

				if (!modalObj) {
					modalObj = new bootstrap.Modal(modalEl);
				}

				modalObj.hide();
			});
		});


		let modalInstance = null;

		// function loadOptions(questionId) {
		// 	fetch('?ajax=load_options&question_id=' + questionId)
		// 		.then(res => res.json())
		// 		.then(data => {

		// 			let html = '';

		// 			if (data.length > 0) {
		// 				data.forEach(opt => {
		// 					html += `
        //                 <div class="option-box ${opt.is_correct == 1 ? 'correct' : ''}">
        //                     ${opt.option_text}
        //                 </div>`;
		// 				});
		// 			} else {
		// 				html = "<i>No options found</i>";
		// 			}

		// 			document.getElementById('optionsList').innerHTML = html;

		// 			modalInstance = new bootstrap.Modal(document.getElementById('optionsModal'), {
		// 				backdrop: false, // 👈 BACKDROP COMPLETELY DISABLED
		// 				keyboard: true
		// 			});

		// 			modalInstance.show();
		// 		});
		// }

		function loadOptions(questionId) {
    fetch('?ajax=load_options&question_id=' + questionId)
        .then(res => res.json())
        .then(data => {

            let html = '';

            if (data.length > 0) {
                data.forEach(opt => {
                    html += `
                        <div class="option-box"
                             style="padding:6px; margin-bottom:6px; border-radius:4px; ${
                                (opt.is_correct == 1 || opt.is_correct === '1')
                                ? 'background:#d4f8d4; font-weight:bold;'
                                : ''
                             }">
                            ${opt.option_text}
                        </div>`;
                });
            } else {
                html = "<i>No options found</i>";
            }

            document.getElementById('optionsList').innerHTML = html;

            modalInstance = new bootstrap.Modal(document.getElementById('optionsModal'), {
                backdrop: false,
                keyboard: true
            });

            modalInstance.show();
        });
}

	</script>

	<script type="text/javascript">
		$(document).ready(function() {
			console.log('data');
			$('#importQuestionForm').on('submit', function(e) {
				e.preventDefault();

				const fileInput = $('#question_file')[0].files[0];
				if (!fileInput) {
					alert('Please select a CSV file to upload.');
					return;
				}

				let formData = new FormData(this);

				$.ajax({
					url: 'manage_question.process.php?act=import_questions',
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					beforeSend: function() {
						// Optionally show loading indicator
						$('button[type="submit"]').prop('disabled', true).text('Uploading...');
					},
					success: function(response) {
						$('button[type="submit"]').prop('disabled', false).text('Upload CSV');

						try {
							let res = JSON.parse(response);
							if (res.status === 1) {
								alert(res.message || 'Questions imported successfully!');
								let url = new URL(window.location.href);
								url.searchParams.delete('m'); // remove 'm' param if exists
								// importQuestionForm

								$('#importQuestionForm')[0].reset();
								// Reload page without 'm'
								window.location.href = url.toString();
							} else {
								alert(res.message || 'Import failed. Please check your file.');
							}
						} catch (e) {
							console.log(response);
							alert('Unexpected response from server. Check console.');
						}
					},
					error: function(xhr, status, error) {
						$('button[type="submit"]').prop('disabled', false).text('Upload CSV');
						alert('Error uploading file: ' + error);
					}
				});
			});
		});
	</script>
	<!-- </form> -->
<?php
}
?>