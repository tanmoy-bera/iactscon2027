<?php
include_once('includes/init.php');

page_header("Quiz Questions");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]              = $pageKeyVal;
$searchArray['question_title']       		= $_REQUEST['question_title'];

foreach ($searchArray as $searchKey => $searchVal) {
	$searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
	<?php
	switch ($show) {
		// show add window
		case 'add':
			addQuestion($cfg, $mycms);
			break;
		// show edit window	
		case 'edit':
			editQuestion($cfg, $mycms);
			break;
		// view details record
		case 'view':
			viewQuiz($cfg, $mycms);
			break;
		// show all record	
		default:
			showAllQuestion($cfg, $mycms);
			break;
	}
	?>
</div>
<?php
page_footer();

/*============== SHOW ALL Question WINDOW =============*/
function showAllQuestion($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmQuestionListing" id="frmQuestionListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php" method="post">
		<input type="hidden" name="act" id="act" value="search_Question" />
		<input type="hidden" id="excelPrintVal" name="excelPrintVal" />
		<table width="100%" align="center" class="tborder">
			<?php
			$sql_quiz = [];
			$sql_quiz['QUERY'] = "SELECT * FROM rcg_quizzes 
								WHERE id = ? 
								AND status = 'active' 
								AND row_status = 'A'
								ORDER BY id ASC";
			$sql_quiz['PARAM'][] = ['FILD' => 'id', 'DATA' => $searchArray['quiz_id'], 'TYP' => 'i'];

			$quiz_details = $mycms->sql_select($sql_quiz);
			$quiz_details = $quiz_details[0];
			?>
			<tr>
				<td colspan="2" align="left" class="tcat">
					<span style="float:left">Quiz: <?= ($quiz_details) ? $quiz_details['title'] : ''; ?></span>
					<span class="tsearchTool" forType="tsearchTool"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<!-- <div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Question Name:
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
							<td width="30" align="center">
								<input name="check_all" id="check_all" targetElement="checkBoxListing"
									type="checkbox" onclick="checkall(this);" />
							</td>
							<td width="50" align="center">Sl No </td>
							<td width="100" align="center">Question</td>
							<td width="50" align="center">Option A</td>
							<td width="50" align="center">Option B</td>
							<td width="50" align="center">Option C</td>
							<td width="50" align="center">Option D</td>
							<td width="100" align="center">Correct Option</td>
							<td width="50" align="center">Round</td>
							<td width="50" align="center">Level</td>
							<td width="100" align="center">Extra Time</td>
							<td width="100" align="center">Is Last Question</td>
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
						// $sql['QUERY'] = "SELECT id, quiz_id, level, round, question_text, media_url, option_a, option_b, option_c, option_d, correct_option, timer_override , row_status, status, is_last_question
						// FROM rcg_quiz_questions 
						// WHERE row_status != ? 
						// $con 
						// ORDER BY round ASC, level ASC, id ASC";

						// $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];

						// foreach ($params as $p) {
						// 	$sql['PARAM'][] = $p;
						// }
						$sql['QUERY'] = "
							SELECT q.id, q.quiz_id, q.level, q.round, q.question_text, q.media_url,
								q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option,
								q.timer_override, q.row_status, q.status, q.is_last_question
							FROM rcg_quiz_questions AS q
							JOIN rcg_quizzes AS qm ON qm.id = q.quiz_id
							WHERE q.row_status != ?
							AND qm.row_status = 'A'
							$con
							ORDER BY q.id DESC, q.round ASC 
						";

						$sql['PARAM'][] = ['FILD' => 'q.row_status', 'DATA' => 'D', 'TYP' => 's'];

						// $questionList = $mycms->sql_select($sql);
						$questionList = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);

						$sl = 1;
						$i = 0;
						if (!empty($questionList)) {
							foreach ($questionList as $i => $q) {
								@$i++;
						?>
								<tr>
									<td align="center">
										<!-- <input type="checkbox" name="checkvalue" forType="checkBoxListing" value="<?= $q['id'] ?>" /> -->
										<input type="checkbox" name="selected_ids[]" id="checkvalue" class="checkBoxListing" forType="checkBoxListing" value="<?= $q['id'] ?>" />
									</td>
									<td><?= $i + ($_REQUEST['_pgn_'] * 15) ?></td>
									<td><?= htmlspecialchars($q['question_text']) ?></td>
									<td align="center"><?= htmlspecialchars($q['option_a']) ?></td>
									<td align="center"><?= htmlspecialchars($q['option_b']) ?></td>
									<td align="center"><?= htmlspecialchars($q['option_c']) ?></td>
									<td align="center"><?= htmlspecialchars($q['option_d']) ?></td>
									<td align="center"><?= strtoupper($q['correct_option']) ?></td>
									<td align="center"><?= $q['round'] ?></td>
									<td align="center"><?= $q['level'] ?></td>
									<td align="center"><?= ($q['timer_override']) ? $q['timer_override'] . ' sec.' : '' ?></td>
									<!-- <td align="center"><?= ucfirst($q['status']) ?></td> -->
									<td align="center"><?= ($q['is_last_question'] == 1) ? 'Yes' : 'No'; ?></td>
									<td align="center">
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php?act=<?= ($q['row_status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $q['id'] ?><?= $searchString ?>"
											class="<?= ($q['row_status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($q['row_status'] == 'A') ? 'Active' : 'Inactive' ?></a>
									</td>
									<td align="center">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=edit&question_id=<?= $q['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'edit.png' ?>" title="Edit" class="icon-pen">
										</a>
										&nbsp;
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php?act=Remove&id=<?= $q['id'] ?><?= $searchString ?>">
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
								<td colspan="13" align="center">No questions found.</td>
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
				<td width="50%" align="right">
					<a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>">Add New Question</a>
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
			if (confirm(`Are you sure you want to ${action} the selected questions?`)) {
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
	</script>
<?php
}

/*================= ADD Question WINDOW ===============*/
function addQuestion($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="add_question" />
		<?php
		foreach ($searchArray as $key => $val) {
		?>
			<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
		<?php
		}
		?>
		<table width="50%" class="tborder">
			<thead>
				<tr>
					<td colspan="2" align="left" class="tcat">Set Question</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Question Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Select Quiz <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">

									<input type="hidden" name="fr" value="<?= $_REQUEST['fr'] ?>" />
									<select name="quiz_id" style="width: 83.6%;" id="quiz" class="validate[required]">
										<option value="">- Select -</option>
										<?php
										$sql_quiz 			= [];
										$sql_quiz['PARAM'] 	= [];
										$sql_quiz['QUERY'] 	= "SELECT * FROM rcg_quizzes 
                      						WHERE row_status = 'A' AND (status = 'active' OR status = 'draft')";

										if (isset($_REQUEST['quiz_id']) && (int)$_REQUEST['quiz_id'] > 0) {
											$quiz_id 				= (int)$_REQUEST['quiz_id'];
											$sql_quiz['QUERY'] 		.= " AND id = ?";
											$sql_quiz['PARAM'][] 	= ['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i'];
										}

										$sql_quiz['QUERY'] .= " ORDER BY id ASC";

										$quiz_list = $mycms->sql_select($sql_quiz);
										$selected_quiz_id = isset($_REQUEST['quiz_id']) ? (int)$_REQUEST['quiz_id'] : 0;

										foreach ($quiz_list as $i => $row) {
											$isSelected = ($row['id'] == $selected_quiz_id) ? 'selected' : '';
										?>
											<option value="<?= $row['id']; ?>"
												data-rounds="<?= $row['rounds']; ?>"
												data-levels="<?= $row['levels']; ?>"
												<?= $isSelected; ?>>
												<?= htmlspecialchars($row['title']); ?>
											</option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Round <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="round" style="width: 83.6%;" id="quiz_round" class="validate[required]">
										<option value="">- Select -</option>

									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Level <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="level" style="width: 83.6%;" id="quiz_level" class="validate[required]">
										<option value="">- Select -</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Question <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="question_text" id="question_text"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Media Type
								</td>
								<td width="60%" align="left">
									<select name="media_type" id="media_type" class="" style="width:83.6%">
										<option value="">Select Media Type</option>
										<option value="Image">Image</option>
										<option value="Audio">Audio</option>
										<option value="Video">Video</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Media URL
								</td>
								<td width="60%" align="left">
									<input type="text" id="media_url" name="media_url" style="width:80%">
								</td>
							</tr>
							<?php /*
							<tr>
								<td width="40%" align="left">
									Upload Image
								</td>
								<td width="60%" align="left">
									<input type="file" id="image" name="image" style="width:80%">
								</td>
							</tr>
							*/ ?>
							<tr>
								<td width="40%" align="left">
									Option A <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_a" id="option_a"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option B <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_b" id="option_b"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option C <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_c" id="option_c"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option D <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_d" id="option_d"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Correct Option <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="correct_option" style="width: 83.6%;" id="" class="validate[required]">
										<option value="">- Select -</option>
										<option value="A">A</option>
										<option value="B">B</option>
										<option value="C">C</option>
										<option value="D">D</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Extra Time(in sec.)
								</td>
								<td width="60%" align="left">
									<input type="number" name="timer_override" id="timer_override" value="0" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Is Last Question <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="is_last_question" style="width: 83.6%;" id="" class="validate[required]">
										<option value="No" selected>No</option>
										<option value="Yes">Yes</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="40%"></td>
					<td align="left">
						<a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
							<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
						&nbsp;
						<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</form>

	<script>
		$(document).ready(function() {
			// Initially hide both rows
			$("#media_url").closest("tr").hide();
			// $("#image").closest("tr").hide();

			$("#media_type").on("change", function() {
				const type = $(this).val();

				// Hide both by default
				$("#media_url").closest("tr").hide();
				// $("#image").closest("tr").hide();

				// Show relevant input
				// if (type === "Image") {
				// 	$("#image").closest("tr").show();
				// } else if (type === "Video" || type === "Audio") {
				// 	$("#media_url").closest("tr").show();
				// }
				if (type !== "") {
					$("#media_url").closest("tr").show();
				}
			});
		});
	</script>
<?php
}

/*================= EDIT Question WINDOW ==============*/
function editQuestion($cfg, $mycms)
{
	global $searchArray, $searchString;

	$sql 	=	array();
	$sql['QUERY']    = "SELECT * FROM `rcg_quiz_questions` 
						   WHERE `id` = ?";
	$sql['PARAM'][]	=	array('FILD' => 'id',     		 'DATA' => $_REQUEST['question_id'],       	           'TYP' => 's');
	$result			 = $mycms->sql_select($sql);
	$qrow    		 = $result[0];
?>
	<form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_question.process.php" id="frmtypeedit">
		<!-- onsubmit="return onSubmitAction();" -->
		<input type="hidden" name="act" value="edit_question" />
		<input type="hidden" name="question_id" id="question_id" value="<?= $_REQUEST['question_id'] ?>" />
		<input type="hidden" name="fr" value="<?= $_REQUEST['fr'] ?>" />
		<?php
		foreach ($searchArray as $key => $val) {
		?>
			<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
		<?php
		}
		?>
		<table width="50%" class="tborder">
			<thead>
				<tr>
					<td colspan="2" align="left" class="tcat">Edit Question</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Question Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Select Quiz <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="quiz_id" style="width: 83.6%;" id="quiz" class="validate[required]">
										<option value="">- Select -</option>
										<?php
										$sql_quiz = [];
										$sql_quiz['QUERY'] = "SELECT * FROM rcg_quizzes 
														WHERE row_status = 'A' AND (status = 'active' OR status = 'draft')
														ORDER BY id ASC";

										$quiz_list = $mycms->sql_select($sql_quiz);
										foreach ($quiz_list as $i => $row) {
										?>
											<option value="<?= $row['id']; ?>"
												data-rounds="<?= $row['rounds']; ?>"
												data-levels="<?= $row['levels']; ?>"
												<?= ($qrow['quiz_id'] == $row['id']) ? 'selected' : ''; ?>>
												<?= $row['title']; ?>
											</option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Round <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="hidden" value="<?= $qrow['round'] ?>" id="round_value">
									<input type="hidden" value="<?= $qrow['level'] ?>" id="level_value">
									<select name="round" style="width: 83.6%;" id="quiz_round" class="validate[required]">
										<option value="">- Select -</option>

									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Level <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="level" style="width: 83.6%;" id="quiz_level" class="validate[required]">
										<option value="">- Select -</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Question <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="question_text" id="question_text"
										class="validate[required]" style="width:80%;" required="" value="<?= $qrow['question_text'] ?>" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Media Type
								</td>
								<td width="60%" align="left">
									<select name="media_type" id="media_type" class="" style="width:83.6%">
										<option value="">Select Media Type</option>
										<option <?= ($qrow['media_type'] == 'image') ? 'selected' : ''; ?> value="Image">Image</option>
										<option <?= ($qrow['media_type'] == 'audio') ? 'selected' : ''; ?> value="Audio">Audio</option>
										<option <?= ($qrow['media_type'] == 'video') ? 'selected' : ''; ?> value="Video">Video</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Media URL
								</td>
								<td width="60%" align="left">
									<input type="text" id="media_url" name="media_url" style="width:80%" value="<?= htmlspecialchars($qrow['media_url']) ?>">
								</td>
							</tr>
							<?php /*
							<tr>
								<td width="40%" align="left">
									Upload Image
								</td>
								<td width="60%" align="left">
									<?php
										$existingImage = '';
										if (!empty($qrow['media_type']) && strtolower($qrow['media_type']) === 'image' && !empty($qrow['media_url'])) {
											$existingImage = htmlspecialchars($qrow['media_url'], ENT_QUOTES, 'UTF-8');
										}
									?>
									<input type="file" id="image" name="image" style="width:80%">
									<input type="hidden" id="existing_image_row" name="existing_image_row" style="width:80%"
										 value="<?= $existingImage ?>">
								</td>
							</tr>
							*/ ?>
							<tr>
								<td width="40%" align="left">
									Option A <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_a" id="option_a"
										class="validate[required]" style="width:80%;" value="<?= $qrow['option_a'] ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option B <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_b" id="option_b"
										class="validate[required]" style="width:80%;" value="<?= $qrow['option_b'] ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option C <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_c" id="option_c"
										class="validate[required]" style="width:80%;" value="<?= $qrow['option_c'] ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option D <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="option_d" id="option_d"
										class="validate[required]" style="width:80%;" value="<?= $qrow['option_d'] ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Correct Option <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="correct_option" style="width: 83.6%;" id="" class="validate[required]">
										<option value="">- Select -</option>
										<option value="A" <?= ($qrow['correct_option'] == 'A') ? 'selected' : ''; ?>>A</option>
										<option value="B" <?= ($qrow['correct_option'] == 'B') ? 'selected' : ''; ?>>B</option>
										<option value="C" <?= ($qrow['correct_option'] == 'C') ? 'selected' : ''; ?>>C</option>
										<option value="D" <?= ($qrow['correct_option'] == 'D') ? 'selected' : ''; ?>>D</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Extra Time(in sec.)
								</td>
								<td width="60%" align="left">
									<input type="number" name="timer_override" id="timer_override" value="<?= ($qrow['timer_override'] > 0) ? $qrow['timer_override'] : 0; ?>" style="width:80%;" required="" />
								</td>
							</tr>
							<!-- <tr>
								<td width="40%" align="left">
									Question Status <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="question_status" style="width: 83.6%;" id="" class="validate[required]">
										<option <?= ($qrow['status'] == 'active') ? 'selected' : ''; ?> value="Active">Active</option>
										<option <?= ($qrow['status'] == 'draft') ? 'selected' : ''; ?> value="Draft">Draft</option>
										<option <?= ($qrow['status'] == 'archived') ? 'selected' : ''; ?> value="Archived">Archived</option>
									</select>
								</td>
							</tr> -->
							<tr>
								<td width="40%" align="left">
									Is Last Question <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="is_last_question" style="width: 83.6%;" id="" class="validate[required]">
										<option <?= ($qrow['is_last_question'] == '1') ? 'selected' : ''; ?> value="Yes">Yes</option>
										<option <?= ($qrow['is_last_question'] == '0') ? 'selected' : ''; ?> value="No">No</option>
									</select>
								</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td width="40%"></td>
					<td align="left">
						<a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>">
							<input type="button" name="BackEdit" id="BackEdit" value="Back" class="btn btn-small btn-red" /></a>
						&nbsp;
						<input type="submit" name="Save" id="Save" value="Save" class="btn btn-small btn-blue" />
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</form>
	<script>
		$(document).ready(function() {
			function toggleMediaFields() {
				const type = $("#media_type").val()?.toLowerCase() || "";

				// Hide both by default
				$("#media_url").closest("tr").hide();
				$("#image").closest("tr").hide();

				// Show relevant field
				// if (type === "image") {
				// 	$("#image").closest("tr").show();
				// } else if (type === "video" || type === "audio") {
				// 	$("#media_url").closest("tr").show();
				// }
				if (type !== "") {
					$("#media_url").closest("tr").show();
				}
			}

			// Initial call (important for edit mode)
			toggleMediaFields();

			// Change event
			$("#media_type").on("change", function() {
				toggleMediaFields();
			});

			// Form validation before submit
			$("#frmtypeedit").on("submit", function(e) {
				const mediaType = $("#media_type").val()?.toLowerCase() || "";
				const mediaUrl = $("#media_url").val().trim();
				const imageFile = $("#image").val().trim();

				// If no media type selected → block
				// if (!mediaType) {
				// 	alert("Please select a media type.");
				// 	$("#media_type").focus();
				// 	e.preventDefault();
				// 	return false;
				// }

				// For image type → ensure file selected (if new file not already uploaded)
				// if (mediaType === "image") {
				// 	// If editing, you might already have an image in DB
				// 	const existingImage = $("#existing_image_row img").length > 0;

				// 	if (!imageFile && !existingImage) {
				// 		alert("Please upload an image file.");
				// 		$("#image").focus();
				// 		e.preventDefault();
				// 		return false;
				// 	}
				// }

				// For audio/video type → ensure valid URL entered
				if (mediaType === "audio" || mediaType === "video" ||  mediaType === "image") {
					if (!mediaUrl) {
						alert("Please enter a valid media URL.");
						$("#media_url").focus();
						e.preventDefault();
						return false;
					}

					// Optional: basic URL pattern check
					const urlPattern = /^(https?:\/\/)?([\w-]+(\.[\w-]+)+)([\/?#].*)?$/i;
					if (!urlPattern.test(mediaUrl)) {
						alert("Please enter a valid media URL format.");
						$("#media_url").focus();
						e.preventDefault();
						return false;
					}
				}

				// All good → allow form submit
				// return true;
				onSubmitAction();
			});
		});
	</script>


<?php
}
?>

<script>
	// document.addEventListener('DOMContentLoaded', function() {
	// 	const quizSelect = document.getElementById('quiz');
	// 	const roundSelect = document.getElementById('quiz_round');
	// 	const levelSelect = document.getElementById('quiz_level');

	// 	quizSelect.addEventListener('change', function() {
	// 		populateRoundLevel();
	// 	});

	// 	populateRoundLevel(true);

	// 	function populateRoundLevel(isEdit = false) {
	// 		const selectedOption = quizSelect.options[quizSelect.selectedIndex];
	// 		if (!selectedOption || !selectedOption.value) return;

	// 		const totalRounds = parseInt(selectedOption.getAttribute('data-rounds')) || 0;
	// 		const totalLevels = parseInt(selectedOption.getAttribute('data-levels')) || 0;

	// 		let selectedRound = 0,
	// 			selectedLevel = 0;
	// 		if (isEdit) {
	// 			selectedRound = parseInt(document.getElementById('round_value').value || 0);
	// 			selectedLevel = parseInt(document.getElementById('level_value').value || 0);
	// 		}

	// 		// Populate rounds
	// 		roundSelect.innerHTML = '<option value="">- Select -</option>';
	// 		for (let i = 1; i <= totalRounds; i++) {
	// 			const opt = document.createElement('option');
	// 			opt.value = i;
	// 			opt.text = i;
	// 			if (isEdit && i === selectedRound) opt.selected = true;
	// 			roundSelect.appendChild(opt);
	// 		}

	// 		// Populate levels
	// 		levelSelect.innerHTML = '<option value="">- Select -</option>';
	// 		for (let i = 1; i <= totalLevels; i++) {
	// 			const opt = document.createElement('option');
	// 			opt.value = i;
	// 			opt.text = i;
	// 			if (isEdit && i === selectedLevel) opt.selected = true;
	// 			levelSelect.appendChild(opt);
	// 		}
	// 	}
	// });
	document.addEventListener('DOMContentLoaded', function() {
		const quizSelect = document.getElementById('quiz');
		const roundSelect = document.getElementById('quiz_round');
		const levelSelect = document.getElementById('quiz_level');

		quizSelect.addEventListener('change', function() {
			populateRoundLevel(false);
		});

		// Determine if a quiz is pre-selected
		const isPreSelected = quizSelect.value && quizSelect.value !== '';

		// Populate round/level on page load
		populateRoundLevel(isPreSelected);

		function populateRoundLevel(isEdit = false) {
			const selectedOption = quizSelect.options[quizSelect.selectedIndex];
			if (!selectedOption || !selectedOption.value) return;

			const totalRounds = parseInt(selectedOption.getAttribute('data-rounds')) || 0;
			const totalLevels = parseInt(selectedOption.getAttribute('data-levels')) || 0;

			let selectedRound = 0,
				selectedLevel = 0;

			if (isEdit) {
				// Check for hidden inputs or defaults
				selectedRound = parseInt(document.getElementById('round_value')?.value || 0);
				selectedLevel = parseInt(document.getElementById('level_value')?.value || 0);

				// If no hidden input, default to 1
				if (!selectedRound && totalRounds) selectedRound = '';
				if (!selectedLevel && totalLevels) selectedLevel = '';
			}

			// Populate rounds
			roundSelect.innerHTML = '<option value="">- Select -</option>';
			for (let i = 1; i <= totalRounds; i++) {
				const opt = document.createElement('option');
				opt.value = i;
				opt.text = i;
				if (isEdit && i === selectedRound) opt.selected = true;
				roundSelect.appendChild(opt);
			}

			// Populate levels
			levelSelect.innerHTML = '<option value="">- Select -</option>';
			for (let i = 1; i <= totalLevels; i++) {
				const opt = document.createElement('option');
				opt.value = i;
				opt.text = i;
				if (isEdit && i === selectedLevel) opt.selected = true;
				levelSelect.appendChild(opt);
			}
		}
	});
</script>