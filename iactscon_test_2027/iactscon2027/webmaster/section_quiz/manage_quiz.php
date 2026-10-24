<?php
include_once('includes/init.php');

page_header("Quiz");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]                 	= $pageKeyVal;
$searchArray['start_date']       		= $_REQUEST['start_date'];

foreach ($searchArray as $searchKey => $searchVal) {
	$searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ccicon_2025/webmaster/util/font-awesome-4.7.0/css/font-awesome.min.css" />
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
	<?php
	switch ($show) {
		// show add window
		case 'add':
			addQuiz($cfg, $mycms);
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

/*============== SHOW ALL COUNTRY WINDOW =============*/
function showAllQuiz($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmCountryListing" id="frmCountryListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php" method="post">
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
							<td width="30" align="center">
								<input name="check_all" id="check_all" targetElement="checkBoxListing"
									type="checkbox" onclick="checkall(this);" />
							</td>
							<td width="50" align="center">Sl No </td>
							<td width="100" align="left">Quiz Title</td>
							<td width="50" align="center">Hall</td>
							<td width="70" align="center">Start Time</td>
							<td width="50" align="center">Quiz Status</td>
							<!-- <td width="50" align="center">Questions</td> -->
							<td width="50" align="center">Applicants</td>
							<td width="50" align="center">Status</td>
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

						$i = 0;

						$sql = [];
						// $sql['QUERY'] = "SELECT * FROM `rcg_quizzes` 
						// 	WHERE `row_status` != ? 
						// 	$con 
						// 	AND (`status` = 'active' OR `status` = 'draft') 
						// 	ORDER BY start_time DESC";

						// $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];
						$sql = [];
						$sql['QUERY'] = "SELECT q.*, h.hall_title 
							FROM `rcg_quizzes` AS q
							LEFT JOIN `rcg_program_schedule_hall` AS h 
								ON q.hall_id = h.id
							WHERE q.`row_status` != ? 
							$con 
							-- AND (q.`status` = 'active' OR q.`status` = 'draft') 
							ORDER BY q.start_time DESC";

						$sql['PARAM'][] = [
							'FILD' => 'row_status',
							'DATA' => 'D',
							'TYP'  => 's'
						];

						foreach ($params as $p) {
							$sql['PARAM'][] = $p;
						}
						$result = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);

						if ($result) {
							foreach ($result as $i => $row) {
								@$i++;
						?>
								<tr class="tlisting">
									<td align="center">
										<!-- <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing"
											value="" /> -->
										<input type="checkbox" name="selected_ids[]" id="checkvalue" class="checkBoxListing" forType="checkBoxListing"
											value="<?= $row['id'] ?>" />
									</td>
									<td align="center"><?= $i + ($_REQUEST['_pgn_'] * 15) ?></td>
									<td><?= $row['title'] ?></td>
									<td align="left"><?= $row['hall_title'] ?? '' ?></td>
									<td align="center"><?= !empty($row['start_time']) ? date('d-m-Y H:i:s', strtotime($row['start_time'])) : '--' ?></td>
									<td align="center"><?= ucfirst($row['status']) ?></td>
									<!-- <td align="center"><a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_questions.php?quiz_id=<?= $row['id'] ?>" target="_blank">View</a></td> -->
									<td align="center"><a href="<?= $cfg['SECTION_BASE_URL'] ?>participations.php?quiz_id=<?= $row['id'] ?>" target="_blank">View</a></td>
									<td align="center">
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?act=<?= ($row['row_status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $row['id'] ?><?= $searchString ?>"
											class="<?= ($row['row_status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($row['row_status'] == 'A') ? 'Active' : 'Inactive' ?></a>
									</td>

									<td align="center">

										<?php /* <a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $cfg['SECTION_BASE_URL'] ?>quiz_details.php?quiz_id=<?= $row['id'] ?>">
											<span title="View" class="icon-eye">
										</a> */ ?>
										<?php
										if($row['status'] !== 'completed'){
											?>
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $cfg['SECTION_BASE_URL'] ?>new_quiz_updated.php?quiz_id=<?= $row['id'] ?>">
											<span title="View" class="icon-eye"></span>
										</a>
										&nbsp;

										<?php
										if($row['status'] === 'active'){
											?>
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=notify_all">
											<span title="Notify All" class="icon-notification"></span>
										</a>
										&nbsp;
										<?php } } ?>

										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=edit&quiz_id=<?= $row['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'edit.png' ?>" title="Edit" class="icon-pen"></span>

										</a>
										&nbsp;
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?quiz_id=<?= $row['id'] ?>&act=reset_quiz">
											<span title="Reset Quiz" class="fa fa-refresh"></span>

										</a>
										&nbsp;
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>participants.php?quiz_id=<?= $row['id'] ?>">
											<span title="Participants" class="fa fa-users"></span>
										</a>
										&nbsp;
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'drop.png' ?>" title="Remove" class="icon-trash-stroke"
												onClick="return confirm('Do you really want to remove this record');"></span>
										</a>
										<br>
										<!-- <button type="button">
											<a href="<?= $cfg['SECTION_BASE_URL'] ?>quiz.php" target="_blank"> Start Quiz </a>
										</button> -->
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
					<a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>">Create New Quiz</a>
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
			if (confirm(`Are you sure you want to ${action} the selected quizzes?`)) {
				$.ajax({
					url: 'manage_quiz.process.php',
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

/*================= ADD COUNTRY WINDOW ===============*/
function addQuiz($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="add_quiz" />
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
					<td colspan="2" align="left" class="tcat">Create New Quiz</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Quiz Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Quiz Type <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="quiz_type" id="quiz_type" style="width:83.6%;" required="">
										<option value="default">Default</option>
										<option value="panel discussion">Panel Discussion</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Quiz Title <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="title" id="title"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Hall <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="hall" id="hall" class="validate[required]" style="width:83.6%;" required="">
										<option value=""> - Select - </option>
										<?php
										// rcg_program_schedule_hall
										// id, hall_title, status(A, I, D)
										$sql_hall = [];
										$sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall 
															WHERE status = 'A'
															ORDER BY hall_title ASC";

										$quiz_halls = $mycms->sql_select($sql_hall);
										foreach ($quiz_halls as $i => $hall) {
										?>
											<option value="<?= $hall['id'] ?>"><?= $hall['hall_title'] ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr class="optionalData">
								<td width="40%" align="left">
									Rounds <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="rounds" id="rounds"
										class="validate[required]" style="width:80%;" value="1" required="" />
								</td>
							</tr>
							<tr class="optionalData">
								<td width="40%" align="left">
									Levels <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="levels" id="levels"
										class="validate[required]" style="width:80%;" value="1" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Start Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" name="start_time" id="start_time" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Answer Time(in sec.) <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="answer_time" id="answer_time" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Buffer Time(in sec.) <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="buffer_time" id="buffer_time" style="width:80%;" required="" value="0" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Marks for Correct Answer
								</td>
								<td width="60%" align="left">
									<input type="number" name="marks_correct" id="marks_correct" style="width:80%;" value="0" step="0.1"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Marks for Incorrect Answer
								</td>
								<td width="60%" align="left">
									<input type="number" name="marks_incorrect" id="marks_incorrect" style="width:80%;" value="0" step="0.1"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Quiz Status <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="quiz_status" id="quiz_status" style="width:83.6%;" required="">
										<option value="draft">Draft</option>
										<option value="active">Active</option>
										<option value="completed">Completed</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									View Leader Board <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="view_answer" id="view_answer" style="width:83.6%;" required="">
										<option value="end">At The End of The Quiz</option>
										<option value="question">After Each Question</option>
										<option value="level">After Each Level</option>
										<option value="round">After Each Round</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Seat Limit <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="seat_limit" id="seat_limit" style="width:80%;" required="" value="0" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Application Closing Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" name="application_close_time" id="application_close_time" style="width:80%;" required=""/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Application Status <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="application_status" id="application_status" style="width:83.6%;" required="">
										<option value="active">Active</option>
										<option value="closed">Closed</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Participation Type <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="participation_type" id="participation_type" style="width:83.6%;" required="">
										<option value="solo" selected>Solo</option>
										<option value="group">Group</option>
										<option value="both">Both</option>
									</select>
								</td>
							</tr>
							<tr class="thighlight">
								<td width="100%" align="left" colspan="2">
									Joining instructions
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Show QR Code <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="radio" value="yes" name="enable_qr">
									<label for="">Yes</label>
									&nbsp;
									<input type="radio" value="no" name="enable_qr" checked>
									<label for="">No</label>
								</td>
							</tr>
							<?php
							/*
							<tr>
								<td width="40%" align="left">
									Show instructions bar <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="radio" value="yes" name="enable_instruction_bar">
									<label for="">Yes</label>
									&nbsp;
									<input type="radio" value="no" name="enable_instruction_bar" checked>
									<label for="">No</label>
								</td>
							</tr>
							*/ ?>
							
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
		$(document).ready(function(){
			$('#quiz_type').on('change', function(){
				let quizType = $('#quiz_type').val();
				if(quizType === 'panel discussion') {
					$('.optionalData').hide();
				} else {
					$('.optionalData').show();
				}
			});
		});
	</script>
<?php
}

/*================= EDIT COUNTRY WINDOW ==============*/
function editQuiz($cfg, $mycms)
{
	global $searchArray, $searchString;

	$sql 	=	array();
	$sql['QUERY']    = "SELECT * FROM `rcg_quizzes`  
						   WHERE `id` = ?";
	$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $_REQUEST['quiz_id'], 'TYP' => 's');
	$result			 = $mycms->sql_select($sql);
	$row    		 = $result[0];
?>
	<form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="edit_quiz" />
		<input type="hidden" name="quiz_id" id="quiz_id" value="<?= $_REQUEST['quiz_id'] ?>" />
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
					<td colspan="2" align="left" class="tcat">Edit Quiz</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Quiz Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Quiz Type <span class="mandatory">*</span>
								</td>
								<?php
									$quiz_type = $row['quiz_type'];
									$displayOptionTr = (strtolower($quiz_type) == 'panel discussion') ? 'none' : '';
								?>
								<td width="60%" align="left">
									<select name="quiz_type" id="quiz_type" style="width:83.6%;" required="">
										<option <?= (strtolower($quiz_type) == 'default') ? 'selected' : ''; ?> value="default">Default</option>
										<option <?= (strtolower($quiz_type) == 'panel discussion') ? 'selected' : ''; ?> value="panel discussion">Panel Discussion</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Quiz Title <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="title" id="title"
										class="validate[required]" style="width:80%;" value="<?= $row['title']; ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Hall <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="hall" id="hall" class="validate[required]" style="width:83.6%;" required="">
										<option value=""> - Select - </option>
										<?php
										// rcg_program_schedule_hall
										// id, hall_title, status(A, I, D)
										$sql_hall = [];
										$sql_hall['QUERY'] = "SELECT id, hall_title FROM rcg_program_schedule_hall 
														WHERE status = 'A'
														ORDER BY hall_title ASC";

										$quiz_halls = $mycms->sql_select($sql_hall);
										foreach ($quiz_halls as $i => $hall) {
										?>
											<option value="<?= $hall['id'] ?>" <?= ($row['hall_id'] == $hall['id']) ? 'selected' : ''; ?>><?= $hall['hall_title']; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr class="optionalData" style="width:100%; display: <?= $displayOptionTr ?>;">
								<td width="40%" align="left">
									Rounds <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="rounds" id="rounds"
										class="validate[required]" style="width:80%;" value="<?= ($row['rounds'] > 0) ? $row['rounds'] : 1; ?>" required="" />
								</td>
							</tr>
							<tr class="optionalData" style="width:100%; display: <?= $displayOptionTr ?>;">
								<td width="40%" align="left">
									Levels <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="levels" id="levels"
										class="validate[required]" style="width:80%;" value="<?= ($row['levels'] > 0) ? $row['levels'] : 1; ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Start Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" name="start_time" id="start_time" value="<?= $row['start_time']; ?>" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Answer Time(in sec.) <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="answer_time" value="<?= $row['answer_time']; ?>" id="answer_time" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Buffer Time(in sec.) <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="buffer_time" id="buffer_time" style="width:80%;" required="" value="<?= ($row['buffer_time'] > 0) ? $row['buffer_time'] : 1; ?>" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Marks for Correct Answer
								</td>
								<td width="60%" align="left">
									<input type="number" name="marks_correct" id="marks_correct" style="width:80%;" value="<?= ($row['marks_correct'] > 0) ? $row['marks_correct'] : 0; ?>" step="0.1"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Marks for Incorrect Answer
								</td>
								<td width="60%" align="left">
									<input type="number" name="marks_incorrect" id="marks_incorrect" style="width:80%;" value="<?= ($row['marks_incorrect'] > 0) ? $row['marks_incorrect'] : 0; ?>" step="0.1"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Quiz Status <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="quiz_status" id="quiz_status" style="width:83.6%;" required="">
										<option <?= ($row['status'] == 'draft') ? 'selected' : ''; ?> value="draft">Draft</option>
										<option <?= ($row['status'] == 'active') ? 'selected' : ''; ?> value="active">Active</option>
										<option <?= ($row['status'] == 'completed') ? 'selected' : ''; ?> value="completed">Completed</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									View Leader Board <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="view_answer" id="view_answer" style="width:83.6%;" required="">
										<option <?= ($row['view_answer'] == 'end') ? 'selected' : ''; ?> value="end">At The End of The Quiz</option>
										<option <?= ($row['view_answer'] == 'question') ? 'selected' : ''; ?> value="question">After Each Question</option>
										<option <?= ($row['view_answer'] == 'level') ? 'selected' : ''; ?> value="level">After Each Level</option>
										<option <?= ($row['view_answer'] == 'round') ? 'selected' : ''; ?> value="round">After Each Round</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Seat Limit <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="number" name="seat_limit" id="seat_limit" style="width:80%;" required="" value="<?= $row['seat_limit']; ?>" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Application Closing Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" name="application_close_time" id="application_close_time" style="width:80%;" required="" value="<?= $row['application_close_time']; ?>"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Application Status <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="application_status" id="application_status" style="width:83.6%;" required="">
										<option <?= ($row['application_status'] == 'active') ? 'selected' : ''; ?> value="active">Active</option>
										<option <?= ($row['application_status'] == 'closed') ? 'selected' : ''; ?> value="closed">Closed</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Participation Type <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="participation_type" id="participation_type" style="width:83.6%;" required="">
										<option <?= ($row['participation_type'] == 'solo') ? 'selected' : ''; ?> value="solo">Solo</option>
										<option <?= ($row['participation_type'] == 'group') ? 'selected' : ''; ?> value="group">Group</option>
										<option <?= ($row['participation_type'] == 'both') ? 'selected' : ''; ?> value="both">Both</option>
									</select>
								</td>
							</tr>
							<tr class="thighlight">
								<td width="100%" align="left" colspan="2">
									Joining instructions
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Show QR Code <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="radio" value="yes" name="enable_qr" <?= ($row['enable_qr'] == 'yes') ? 'checked' : ''; ?> >
									<label for="">Yes</label>
									&nbsp;
									<input type="radio" value="no" name="enable_qr" <?= ($row['enable_qr'] == 'no') ? 'checked' : ''; ?>>
									<label for="">No</label>
								</td>
							</tr>
							<?php
							/*
							<tr>
								<td width="40%" align="left">
									Show instructions bar <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="radio" value="yes" name="enable_instruction_bar"  <?= ($row['enable_instruction_bar'] == 'yes') ? 'checked' : ''; ?> >
									<label for="">Yes</label>
									&nbsp;
									<input type="radio" value="no" name="enable_instruction_bar"  <?= ($row['enable_instruction_bar'] == 'no') ? 'checked' : ''; ?> >
									<label for="">No</label>
								</td>
							</tr>
							*/ ?>

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
		$(document).ready(function(){
			$('#quiz_type').on('change', function(){
				let quizType = $('#quiz_type').val();
				if(quizType === 'panel discussion') {
					$('.optionalData').hide();
				} else {
					$('.optionalData').show();
				}
			});
		});
	</script>
<?php
}
?>