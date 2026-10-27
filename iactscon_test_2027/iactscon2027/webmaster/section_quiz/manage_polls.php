<?php
include_once('includes/init.php');

page_header("Polls");

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
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
	<?php
	switch ($show) {
		// show add window
		case 'add':
			addPoll($cfg, $mycms);
			break;
		// show edit window	
		case 'edit':
			editPoll($cfg, $mycms);
			break;
		// view details record
		case 'view':
			viewPoll($cfg, $mycms);
			break;
		// show all record	
		default:
			showAllPolls($cfg, $mycms);
			break;
	}
	?>
</div>
<?php
page_footer();

/*============== SHOW ALL COUNTRY WINDOW =============*/
function showAllPolls($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmCountryListing" id="frmCountryListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php" method="post">
		<input type="hidden" name="act" id="act" value="search_poll" />
		<input type="hidden" id="excelPrintVal" name="excelPrintVal" />
		<table width="100%" align="center" class="tborder">
			<tr>
				<td colspan="2" align="left" class="tcat">
					<span style="float:left">Poll Listing</span>
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
							<td width="40" align="center">Sl No </td>
							<td width="100" align="left">Poll Title</td>
							<td width="50" align="center">Is Active</td>
							<td width="60" align="center">Is Published</td>
							<td width="70" align="center">Start Time</td>
							<td width="70" align="center">End Time</td>
							<td width="70" align="center">Total Votes</td>
							<td width="70" align="center">Result</td>
							<td width="40" align="center">Reset</td>
							<td width="40" align="center">Action</td>
						</tr>
						<?php
						$con = "";
						$params = [];
						if (!empty($_REQUEST['start_date'])) {
							$con .= " AND DATE(`start_time`) >= ?";
							$params[] = [
								'FILD' => 'start_time',
								'DATA' => $_REQUEST['start_date'],
								'TYP'  => 's'
							];
						}

						$i = 0;

						$sql = [];
						$sql['QUERY'] = "
							SELECT p.id, p.title, p.is_active, p.is_published, p.start_time, p.end_time,
								COALESCE(SUM(po.vote_count), 0) AS total_votes,
								GROUP_CONCAT(
									CONCAT(po.option_text, '::', po.vote_count)
									ORDER BY po.vote_count DESC
									SEPARATOR '||'
								) AS options_result
							FROM rcg_polls p
							LEFT JOIN rcg_poll_options po 
								ON p.id = po.poll_id
							WHERE p.is_deleted != '1'
							$con
							GROUP BY p.id
							ORDER BY p.id DESC
						";

						foreach ($params as $p) {
							$sql['PARAM'][] = $p;
						}

						$result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);


						$result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);

						if ($result) {
							foreach ($result as $i => $row) {
								@$i++;
						?>
								<tr class="tlisting">
									<td align="center">
										<input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing"
											value="" />
									</td>
									<td align="center"><?= $i + ($_REQUEST['_pgn_'] * 50) ?></td>
									<td><?= $row['title'] ?></td>
									<td align="center">
										<!-- <?= ($row['is_active'] == '1') ? 'Yes' : 'No'; ?> -->
										<?php
										date_default_timezone_set('Asia/Kolkata');
										$current_time = time();
										
										if ($row['start_time'] !== null && $row['end_time'] !== null) {
											if ($current_time >= strtotime($row['start_time']) && $current_time <= strtotime($row['end_time'])) {
												// $is_active = 1;
												echo 'Yes';
											} else {
												echo 'No';
											}
										} else {
											echo 'No';
										}
										?>
									</td>
									<td align="center"><?= ($row['is_published'] == '1') ? 'Yes' : 'No'; ?></td>
									<td align="center"><?php echo date('d-m-Y H:i:s', strtotime($row['start_time'])); ?></td>
									<td align="center"><?php echo date('d-m-Y H:i:s', strtotime($row['end_time'])); ?></td>
									<td align="center"><?= $row['total_votes'] ?></td>
									<td align="left">
										<?php 
											if (!empty($row['options_result'])) {
												$options = explode('||', $row['options_result']);
												foreach ($options as $opt) {
													list($text, $count) = explode('::', $opt);
													$percentage = ($row['total_votes'] > 0) 
														? round(($count / $row['total_votes']) * 100, 2) 
														: 0;
													echo htmlspecialchars($text) . " ({$percentage}%)<br>";
												}
											} else {
												echo '—';
											}
										?>
									</td>

									<td align="center">
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php?act=Reset&id=<?= $row['id'] ?>" class="btn btn-small btn-red" onClick="return confirm('Do you really want to reset this poll result?');">
											Reset Poll
										</a>
									</td>
									<td align="center">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=edit&poll_id=<?= $row['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'edit.png' ?>" title="Edit" class="icon-pen">
										</a>
										&nbsp;
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'drop.png' ?>" title="Remove" class="icon-trash-stroke"
												onClick="return confirm('Do you really want to remove this record');">
										</a>
										<br>
									</td>
								</tr>
							<?php }
						} else { ?>
							<tr>
								<td colspan="11" align="center">
									<span class="mandatory">No Record.</span>
								</td>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>

			<tr>
				<td width="50%" align="left">
					<!-- <select name="multiOperationSelector" id="multiOperationSelector">
						<option value="">Choose an action...</option>
						<option value="Active">Active</option>
						<option value="Inactive">Inactive</option>
						<option value="Remove">Remove</option>
					</select>
					<input value="Apply to selected"  name="submit" type="button" 
						 onclick="return multiTaskValidation('<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php','<?= $searchString ?>');" class="btn btn-small btn-blue"/>  -->
				</td>
				<td width="50%" align="right">
					<a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>">Create New Poll</a>
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
<?php
}

/*================= ADD COUNTRY WINDOW ===============*/
function addPoll($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="add_poll" />
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
					<td colspan="2" align="left" class="tcat">Create New Poll</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Poll Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Poll Question <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="title" id="title"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Publish <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="is_published" id="is_published" style="width: 83.6%;" required="">
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
									</select>
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
									End Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" name="end_time" id="end_time" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 1 <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="options[]" id="option_1" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 2 <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="options[]" id="option_2" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 3
								</td>
								<td width="60%" align="left">
									<input type="text" name="options[]" id="option_3" style="width:80%;"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 4
								</td>
								<td width="60%" align="left">
									<input type="text" name="options[]" id="option_4" style="width:80%;"/>
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
<?php
}

/*================= EDIT COUNTRY WINDOW ==============*/
function editPoll($cfg, $mycms)
{
	global $searchArray, $searchString;

	$sql 	=	array();
	$sql['QUERY']   = "SELECT * FROM `rcg_polls`  
						   WHERE `id` = ?";
	$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $_REQUEST['poll_id'], 'TYP' => 's');
	$result			= $mycms->sql_select($sql);
	$row    		= $result[0];


	$sql_option['QUERY']    = "SELECT * FROM `rcg_poll_options`  
						   WHERE `poll_id` = ? ORDER BY `id` ASC";
	$sql_option['PARAM'][]	=	array('FILD' => 'poll_id', 'DATA' => $row['id'], 'TYP' => 's');
	$result_option			= $mycms->sql_select($sql_option);
	// $row_option    		 	= $result_option[0];


?>
	<form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="edit_poll" />
		<input type="hidden" name="poll_id" id="poll_id" value="<?= $_REQUEST['poll_id'] ?>" />
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
					<td colspan="2" align="left" class="tcat">Edit Poll</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Poll Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Poll Question <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="title" id="title"
										class="validate[required]" style="width:80%;" value="<?= $row['title']; ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Publish <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="is_published" id="is_published" style="width:83.6%;" required="">
										<option value="1" <?= ($row['is_published'] == '1') ? 'selected' : ''; ?>>Yes</option>
										<option value="0" <?= ($row['is_published'] == '0') ? 'selected' : ''; ?>>No</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Start Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" value="<?= $row['start_time']; ?>" name="start_time" id="start_time" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									End Time <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="datetime-local" name="end_time" id="end_time" value="<?= $row['end_time']; ?>" style="width:80%;" required="" />
								</td>
							</tr>
							<?php
							$option_count = 0;
							foreach ($result_option as $row_option) {
								$option_count++;
							?>
								<tr>
									<td width="40%" align="left">
										Option <?= $option_count ?> 
										<?php
										if($option_count < 3 ) { ?>
										<span class="mandatory">*</span>
										<?php } ?>
									</td>
									<td width="60%" align="left">
										<input type="hidden" name="options[<?= $option_count ?>][id]" value="<?= $row_option['id']; ?>">
										<input type="text" name="options[<?= $option_count ?>][text]" value="<?= $row_option['option_text']; ?>" style="width:80%;" <?= ($option_count < 3) ? 'required' : '' ?>>
										<!-- 
										<input type="hidden" value="<?= $row_option['id']; ?>" name="options['id'][]">
										<input type="text" name="options['text'][]" value="<?= $row_option['option_text']; ?>" id="option_<?= $option_count ?>" style="width:80%;" required="" /> -->
									</td>
								</tr>
							<?php }
								if($option_count == 0) {
							?>
							<tr>
								<td width="40%" align="left">
									Option 1 <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="options['text'][]" id="option_1" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 2 <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="options['text'][]" id="option_2" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 3
								</td>
								<td width="60%" align="left">
									<input type="text" name="options[]" id="option_3" style="width:80%;"/>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Option 4
								</td>
								<td width="60%" align="left">
									<input type="text" name="options[]" id="option_4" style="width:80%;"/>
								</td>
							</tr>
							<?php } ?>
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
<?php
}
?>