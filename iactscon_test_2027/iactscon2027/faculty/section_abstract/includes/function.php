<?php
function abstractReviewUpdatePanel($abstractId, $abstractResultSet, $status)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";



	$sqlReviewCriteria['QUERY']			= "SELECT criteria.*,
											  
													  criteria.id AS criteriaId,
													  criteria.review_criteria,
													  criteria.review_selection,
													  
													  reviewResultDetailsId,
													  resultCriteriaId,
													  resultCriteriaOptionId,
													  resultMarksObtained,
													  resultMarksObtainedInvalue,
													  review_individual_assessment_marks,
													  faculty_review 
				
												 FROM " . _DB_ABSTRACT_REVIEW_CRITERIA_ . " criteria 
									
												  
									  LEFT OUTER JOIN (
															SELECT reviewResult.id AS reviewResultId, 
																   reviewResult.faculty_review AS faculty_review, 
																   reviewResultDetails.id AS reviewResultDetailsId,
																   reviewResultDetails.review_criteria_id AS resultCriteriaId,
																   reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
																   reviewResultDetails.review_obtained_marks AS resultMarksObtained,
																   reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
																   reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks
																	
															  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
															  
														INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
																ON reviewResult.id = reviewResultDetails.review_result_id 
																
															 WHERE reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
															   AND reviewResult.abstract_id = '" . $abstractId . "' 
															   AND reviewResult.status = 'A' 
															   AND reviewResultDetails.status = 'A' 
															  
													  ) reviewResultDetails 
												   ON reviewResultDetails.resultCriteriaId = criteria.id 
													   
												WHERE criteria.status = 'A'
												  AND criteria.criteria_for = 'abstract'
											 
											 ORDER BY criteria.sequence_by ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
	if ($resultReviewCriteria) {
		$reviewResultArray      = array();

		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']          = $rowReviewCriteria['resultCriteriaId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']   = $rowReviewCriteria['resultCriteriaOptionId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	   = $rowReviewCriteria['resultMarksObtained'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS_VALUE'] = $rowReviewCriteria['resultMarksObtainedInvalue'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['INTERNAL_ASSESSMENT']  = $rowReviewCriteria['review_individual_assessment_marks'];
		}
?>
		<table width="100%">
			<?php
			foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
				$criteriaCounter++;

				$totalFullMarks 	+= $rowReviewCriteria['full_marks'];
				$obtainedFullMarks  += round($rowReviewCriteria['resultMarksObtainedInvalue'], 0);
				$obtainedMarks		= (trim($rowReviewCriteria['resultMarksObtainedInvalue']) != '') ? round($rowReviewCriteria['resultMarksObtainedInvalue'], 0) : "";
			?>
				<tr class="tlisting">
					<td align="left" valign="top" width="15%">
						<input type="hidden" name="abstract_review_criteria_id[]" id="abstract_review_criteria_id_<?= $criteriaCounter ?>"
							value="<?= $rowReviewCriteria['criteriaId'] ?>" operationMode="abstract_review_criteria_id"
							criteriaTitle="<?= $rowReviewCriteria['review_criteria'] ?>"
							fullMarks="<?= $rowReviewCriteria['full_marks'] ?>" />
						<b><?= $rowReviewCriteria['review_criteria'] ?></b>
					</td>
					<td>
						<table width="100%">
							<tr>
								<td valign="top" style="background:#FFC7C6;"> Bad & Poor :
									<span style="float:right;"> 0 &nbsp;&nbsp;
										<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
											id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
											operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											value="0" <?= ($obtainedMarks == '0') ? 'checked' : '' ?> reviewMarksId="0"
											style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
									</span>
								</td>
							<tr>
							<tr>
								<td valign="top" style="background:#FFFF91;"> Fair & Good :
									<span style="float:right;"> 1 &nbsp;&nbsp;
										<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
											id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
											operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											value="1" <?= ($obtainedMarks == '1') ? 'checked' : '' ?> reviewMarksId="1"
											style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
									</span>
								</td>
							<tr>
							<tr>
								<td valign="top" style="background:#9DFF9D;"> Very Good & Excellent :
									<span style="float:right;"> 2 &nbsp;&nbsp;
										<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
											id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
											operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											value="2" <?= ($obtainedMarks == '2') ? 'checked' : '' ?> reviewMarksId="2"
											style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
									</span>
								</td>
							<tr>
						</table>
					</td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td align="left" colspan="2">
					<i>Remarks</i><br>
					<textarea name="faculty_review" <?= $status == true ? 'readonly=readonly' : '' ?>
						cols="35px" rows="5"><?= $rowReviewCriteria['faculty_review'] ?></textarea>
				</td>
			</tr>

			<tr>
				<td align="left" colspan="2">
					<div style="font-size:30px; font-weight:bold; margin:17px 0 10px 0;">
						Marks Obtained:
						<span operationMode="abstract_review_marks_obtained" style="color:#D82133"><?= $obtainedFullMarks ?></span>
						/ <span><?= $totalFullMarks ?></span>
					</div>
				</td>
			</tr>
		</table>

		<style>
			.hangingReviewMark {
				background: #D82133;

				font-size: 25px;
				width: 150px;
				text-align: center;
				font-weight: bold;
				position: fixed;
				left: 20px;
				bottom: 20px;
				padding: 30px 10px 30px 10px;
				border: 1px solid #C00;
			}
		</style>

		<div class="hangingReviewMark">
			<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
			<div style="width:100%; height:12px;"></div>
			<span operationMode="abstract_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF;"><?= $obtainedFullMarks ?></span>
			<span style="font-size:40px; margin-top:15px; color:#FFFFFF;">/ <?= $totalFullMarks ?></span>
		</div>
	<?php
	}
}



function casereportReviewUpdatePanel($abstractId, $abstractResultSet, $status)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";



	$sqlReviewCriteria['QUERY']			= "SELECT criteria.*,
											  
													  criteria.id AS criteriaId,
													  criteria.review_criteria,
													  criteria.review_selection,
													  
													  reviewResultDetailsId,
													  resultCriteriaId,
													  resultCriteriaOptionId,
													  resultMarksObtained,
													  resultMarksObtainedInvalue,
													  review_individual_assessment_marks,
													  faculty_review 
		
										 FROM " . _DB_ABSTRACT_REVIEW_CRITERIA_ . " criteria 
							
										  
							  LEFT OUTER JOIN (
							  
													SELECT reviewResult.id AS reviewResultId, 
														   reviewResult.faculty_review AS faculty_review, 
														   reviewResultDetails.id AS reviewResultDetailsId,
														   reviewResultDetails.review_criteria_id AS resultCriteriaId,
														   reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
														   reviewResultDetails.review_obtained_marks AS resultMarksObtained,
														   reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
														   reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks 
													  
													  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
													  
												INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
														ON reviewResult.id = reviewResultDetails.review_result_id 
														
													 WHERE reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
													   AND reviewResult.abstract_id = '" . $abstractId . "' 
													   AND reviewResult.status = 'A' 
													   AND reviewResultDetails.status = 'A' 
													  
											  ) reviewResultDetails 
										   ON reviewResultDetails.resultCriteriaId = criteria.id 
											   
										WHERE criteria.status = 'A'
										  AND criteria.criteria_for = 'caseReport'
									 
									 ORDER BY criteria.sequence_by ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
	if ($resultReviewCriteria) {
		$reviewResultArray      = array();

		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']         	= $rowReviewCriteria['resultCriteriaId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']  	= $rowReviewCriteria['resultCriteriaOptionId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	  	= $rowReviewCriteria['resultMarksObtained'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS_VALUE'] 	= $rowReviewCriteria['resultMarksObtainedInvalue'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['INTERNAL_ASSESSMENT'] 	= $rowReviewCriteria['review_individual_assessment_marks'];
		}
	?>
		<table width="100%">
			<?php
			foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
				$criteriaCounter++;

				$totalFullMarks 	+= $rowReviewCriteria['full_marks'];
				$obtainedFullMarks  += round($rowReviewCriteria['resultMarksObtainedInvalue'], 0);
				$obtainedMarks		= (trim($rowReviewCriteria['resultMarksObtainedInvalue']) != '') ? round($rowReviewCriteria['resultMarksObtainedInvalue'], 0) : "";


			?>
				<tr class="tlisting">
					<td align="left" valign="top" width="15%">
						<input type="hidden" name="abstract_review_criteria_id[]" id="abstract_review_criteria_id_<?= $criteriaCounter ?>"
							value="<?= $rowReviewCriteria['criteriaId'] ?>" operationMode="abstract_review_criteria_id"
							criteriaTitle="<?= $rowReviewCriteria['review_criteria'] ?>"
							fullMarks="<?= $rowReviewCriteria['full_marks'] ?>" />
						<b><?= $rowReviewCriteria['review_criteria'] ?></b>
					</td>
					<td>
						<table width="100%">
							<tr>
								<td valign="top" style="background:#FFC7C6;"> Bad & Poor :
									<span style="float:right;"> 0 &nbsp;&nbsp;
										<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
											id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
											operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											value="0" <?= ($obtainedMarks == '0') ? 'checked' : '' ?> reviewMarksId="0"
											style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
									</span>
								</td>
							<tr>
							<tr>
								<td valign="top" style="background:#FFFF91;"> Fair & Good :
									<span style="float:right;"> 1 &nbsp;&nbsp;
										<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
											id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
											operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											value="1" <?= ($obtainedMarks == '1') ? 'checked' : '' ?> reviewMarksId="1"
											style="float:right; -webkit-appearance: checkbox; -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
									</span>
								</td>
							<tr>
							<tr>
								<td valign="top" style="background:#9DFF9D;"> Very Good & Excellent :
									<span style="float:right;"> 2 &nbsp;&nbsp;
										<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
											id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
											operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											value="2" <?= ($obtainedMarks == '2') ? 'checked' : '' ?> reviewMarksId="2"
											style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
									</span>
								</td>
							<tr>
						</table>
					</td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td align="left" colspan="2">
					<i>Remarks</i><br>
					<textarea name="faculty_review" <?= $status == true ? 'readonly=readonly' : '' ?>
						cols="35px" rows="5"><?= $rowReviewCriteria['faculty_review'] ?></textarea>
				</td>
			</tr>

			<tr>
				<td align="left" colspan="2">
					<div style="font-size:30px; font-weight:bold; margin:17px 0 10px 0;">
						Marks Obtained:
						<span operationMode="abstract_review_marks_obtained" style="color:#D82133"><?= round($obtainedFullMarks) ?></span>
						/ <span><?= $totalFullMarks ?></span>
					</div>
				</td>
			</tr>
		</table>

		<style>
			.hangingReviewMark {
				background: #D82133;

				font-size: 25px;
				width: 150px;
				text-align: center;
				font-weight: bold;
				position: fixed;
				left: 20px;
				bottom: 20px;
				padding: 30px 10px 30px 10px;
				border: 1px solid #C00;
			}
		</style>

		<div class="hangingReviewMark">
			<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
			<div style="width:100%; height:12px;"></div>
			<span operationMode="abstract_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF;"><?= $obtainedFullMarks ?></span>
			<span style="font-size:40px; margin-top:15px; color:#FFFFFF;">/ <?= $totalFullMarks ?></span>
		</div>
	<?php
	}
}

function abstractReviewDisplayPanel($abstractId, $abstractResultSet)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";

	//echo $abstractResultSet['abstract_cat'];

	$searchData = '';
	if (!empty($abstractResultSet['abstract_cat'])) {
		$searchData .= " AND JSON_SEARCH(criteria.`category_id`, 'one', '" . $abstractResultSet['abstract_cat'] . "') IS NOT NULL";
	}

	if (!empty($abstractResultSet['abstract_parent_type'])) {
		//$searchData.=" AND JSON_SEARCH(criteria.`sub_category_id`, 'one', '".$abstractResultSet['abstract_parent_type']."') IS NOT NULL";
	}

	$sqlReviewCriteria['QUERY']			= "SELECT criteria.*,
											  
													  criteria.id AS criteriaId,
													  criteria.	abstract_name AS review_criteria,
													
													  
													  reviewResultDetailsId,
													  resultCriteriaId,
													  resultCriteriaOptionId,
													  resultMarksObtained,
													  resultMarksObtainedInvalue,
													  review_individual_assessment_marks,
													  faculty_review 
		
										 FROM " . _DB_ABSTRACT_REVIEW_LIST_ . " criteria 
							
										  
							  LEFT OUTER JOIN (
							  
													SELECT reviewResult.id AS reviewResultId, 
														   reviewResult.faculty_review AS faculty_review, 
														   reviewResultDetails.id AS reviewResultDetailsId,
														   reviewResultDetails.review_criteria_id AS resultCriteriaId,
														   reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
														   reviewResultDetails.review_obtained_marks AS resultMarksObtained,
														   reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
														   reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks 
													  
													  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
													  
												INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
														ON reviewResult.id = reviewResultDetails.review_result_id 
														
													 WHERE reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
													   AND reviewResult.abstract_id = '" . $abstractId . "' 
													   AND reviewResult.status = 'A' 
													   AND reviewResultDetails.status = 'A' 
													  
											  ) reviewResultDetails 
										   ON reviewResultDetails.resultCriteriaId = criteria.id 
											   
										WHERE criteria.status = 'A' " . $searchData . "
										  
									 
									 ORDER BY criteria.id ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
	$total_criteria		= count($resultReviewCriteria);

	//echo '<pre>'; print_r($sqlReviewCriteria);

	if ($resultReviewCriteria) {
		$reviewResultArray      = array();

		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']         	= $rowReviewCriteria['resultCriteriaId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']  	= $rowReviewCriteria['resultCriteriaOptionId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	  	= $rowReviewCriteria['resultMarksObtained'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS_VALUE'] 	= $rowReviewCriteria['resultMarksObtainedInvalue'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['INTERNAL_ASSESSMENT'] 	= $rowReviewCriteria['review_individual_assessment_marks'];
		}
	?>
		<ul>
			<?php
			foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
				$criteriaCounter++;

				//print_r(json_decode($rowReviewCriteria['review_category_id']));

				$countData = count(json_decode($rowReviewCriteria['review_category_id']));

				$totalFullMarks 	+= $rowReviewCriteria['full_marks'];
				$obtainedFullMarks  += $rowReviewCriteria['resultMarksObtainedInvalue'];
				$obtainedMarks		= (trim($rowReviewCriteria['resultMarksObtainedInvalue']) != '') ? $rowReviewCriteria['resultMarksObtainedInvalue'] : "";
				$textboxvalue = $rowReviewCriteria['resultMarksObtained'];

				//echo '<pre>'; print_r($rowReviewCriteria['score_option']);


			?>
				<li>
					<input type="hidden" id="total_criteria" value="<?=  $total_criteria ?>">
					<h6><b><?= $rowReviewCriteria['review_criteria'] ?></b> </h6>

					<input type="hidden" name="abstract_review_criteria_id[]" id="abstract_review_criteria_id_<?= $criteriaCounter ?>"
						value="<?= $rowReviewCriteria['criteriaId'] ?>" operationMode="abstract_review_criteria_id"
						criteriaTitle="<?= $rowReviewCriteria['review_criteria'] ?>"
						fullMarks="<?= $rowReviewCriteria['full_marks'] ?>" />


					<?php
					if ($rowReviewCriteria['score_option'] == 'dropdown') {
					?>
						<h5>
							<?php
							$i = 1;
							$k = 1;
							foreach (json_decode($rowReviewCriteria['review_category_id']) as $key => $value) {
								$sqlReviewCategory			  =	array();
								$sqlReviewCategory['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_REVIEW_SCORE_ . " 
																			  WHERE `status` ='A' AND id='" . $value . "'
																		   ORDER BY `id` ASC";
								$resultReviewCategory = $mycms->sql_select($sqlReviewCategory);
								// echo '<pre>'; print_r($reviewResultArray);
								if ($i % 2 == 0) {
									$cls = '#FFFF91';
								} else {
									$cls = '#FFC7C6';
								}
							?>
								<?php
								$optRow  = $resultReviewCategory[0];
								$isGrade = (($optRow['category'] ?? '') === 'Grade');

								if ($isGrade) {                       // Grade -> submit the grade, marks = 0
									$optLabel = $optRow['score'];
									$optMarks = 0;
									$style= "width:50%";
								} elseif ($optRow['score'] == -1) {   // NA
									$optLabel = $optRow['category'];
									$optMarks = 0;
								} else {                              // Score
									$optLabel = $optRow['score'];
									$optMarks = $optRow['score'];
								}
							?>
								<label class="score_radio" style="<?=$style?>">
									<input type="radio"
										name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
										id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
										operationMode="abstract_review_marks"
										criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
										criteriaOptionId="<?= $optRow['id'] ?>"
										isGrade="<?= $isGrade ? 'Y' : 'N' ?>"
										value="<?= htmlspecialchars($optLabel) ?>@@<?= $optRow['id'] ?>"
										<?= ($reviewResultArray[$rowReviewCriteria['criteriaId']]['CRITERIA_OPTION_ID'] == $optRow['id']) ? 'checked' : '' ?>
										reviewMarksId="<?= $optMarks ?>"
										required />
									<span class="checkmark"><i><?= htmlspecialchars($optLabel) ?></i></span>
								</label>
							<?php
								$i++;
							}
								?>

						</h5>
					<?php
					} else {
					?>
						<td><input type="text" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]" operationMode="abstract_review_marks" marks="<?= $rowReviewCriteria['full_marks'] ?>" id="abstract_review_option_id<?= $k ?>" countdata="<?= $k ?>" value="<?= $textboxvalue ?>" required></td>
					<?php
					}
					?>
				</li>
			<?php
				$k++;
			}
			?>


			<textarea placeholder="Remarks..." name="faculty_review" <?= $status == true ? 'readonly=readonly' : '' ?>
				cols="35px" rows="5"><?= $rowReviewCriteria['faculty_review'] ?></textarea>
            <? if (empty($isGrade)) {   ?>

			<p>Mark Obtained: <span operationMode="abstract_review_marks_obtained" style="color: #ff384c;font-weight:bolder"><?= $obtainedFullMarks ?></span> / <?= $totalFullMarks ?></p>
           <? } ?>


			<style>
				.hangingReviewMark {
					background: #D82133;

					font-size: 25px;
					width: 150px;
					text-align: center;
					font-weight: bold;
					position: fixed;
					left: 20px;
					bottom: 20px;
					padding: 30px 10px 30px 10px;
					border: 1px solid #C00;
				}
			</style>

			<!-- <div class="hangingReviewMark">
					<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
					<div style="width:100%; height:12px;"></div>
					<span operationMode="abstract_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF;"><?= $obtainedFullMarks ?></span>
					<span style="font-size:40px; margin-top:15px; color:#FFFFFF;">/ <?= $totalFullMarks ?></span>
				</div> -->
			<script type="text/javascript">
				function isNumber(evt) {
					evt = (evt) ? evt : window.event;
					var charCode = (evt.which) ? evt.which : evt.keyCode;
					if (charCode > 31 && (charCode < 48 || charCode > 57)) {
						return false;
					}
					return true;
				}
			</script>
		<?php
	}
}

function abstractReviewDisplayPanel_old($abstractId, $abstractResultSet)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";

	//echo $abstractResultSet['abstract_cat'];

	$searchData = '';
	if (!empty($abstractResultSet['abstract_cat'])) {
		$searchData .= " AND JSON_SEARCH(criteria.`category_id`, 'one', '" . $abstractResultSet['abstract_cat'] . "') IS NOT NULL";
	}

	if (!empty($abstractResultSet['abstract_parent_type'])) {
		//$searchData.=" AND JSON_SEARCH(criteria.`sub_category_id`, 'one', '".$abstractResultSet['abstract_parent_type']."') IS NOT NULL";
	}

	$sqlReviewCriteria['QUERY']			= "SELECT criteria.*,
											  
													  criteria.id AS criteriaId,
													  criteria.	abstract_name AS review_criteria,
													
													  
													  reviewResultDetailsId,
													  resultCriteriaId,
													  resultCriteriaOptionId,
													  resultMarksObtained,
													  resultMarksObtainedInvalue,
													  review_individual_assessment_marks,
													  faculty_review 
		
										 FROM " . _DB_ABSTRACT_REVIEW_LIST_ . " criteria 
							
										  
							  LEFT OUTER JOIN (
							  
													SELECT reviewResult.id AS reviewResultId, 
														   reviewResult.faculty_review AS faculty_review, 
														   reviewResultDetails.id AS reviewResultDetailsId,
														   reviewResultDetails.review_criteria_id AS resultCriteriaId,
														   reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
														   reviewResultDetails.review_obtained_marks AS resultMarksObtained,
														   reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
														   reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks 
													  
													  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
													  
												INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
														ON reviewResult.id = reviewResultDetails.review_result_id 
														
													 WHERE reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
													   AND reviewResult.abstract_id = '" . $abstractId . "' 
													   AND reviewResult.status = 'A' 
													   AND reviewResultDetails.status = 'A' 
													  
											  ) reviewResultDetails 
										   ON reviewResultDetails.resultCriteriaId = criteria.id 
											   
										WHERE criteria.status = 'A' " . $searchData . "
										  
									 
									 ORDER BY criteria.id ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);

	//echo '<pre>'; print_r($sqlReviewCriteria);

	if ($resultReviewCriteria) {
		$reviewResultArray      = array();

		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']         	= $rowReviewCriteria['resultCriteriaId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']  	= $rowReviewCriteria['resultCriteriaOptionId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	  	= $rowReviewCriteria['resultMarksObtained'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS_VALUE'] 	= $rowReviewCriteria['resultMarksObtainedInvalue'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['INTERNAL_ASSESSMENT'] 	= $rowReviewCriteria['review_individual_assessment_marks'];
		}
		?>
			<table width="100%">
				<?php
				foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
					$criteriaCounter++;

					//print_r(json_decode($rowReviewCriteria['review_category_id']));

					$countData = count(json_decode($rowReviewCriteria['review_category_id']));

					$totalFullMarks 	+= $rowReviewCriteria['full_marks'];
					$obtainedFullMarks  += $rowReviewCriteria['resultMarksObtainedInvalue'];
					$obtainedMarks		= (trim($rowReviewCriteria['resultMarksObtainedInvalue']) != '') ? $rowReviewCriteria['resultMarksObtainedInvalue'] : "";
					$textboxvalue = $rowReviewCriteria['resultMarksObtained'];

					//echo '<pre>'; print_r($rowReviewCriteria['score_option']);


				?>
					<tr>
						<td></td>
						<td><b><?= $rowReviewCriteria['review_criteria'] ?></b> </td>
					</tr>
					<tr class="tlisting">
						<td align="left" valign="top" width="15%">
							<input type="hidden" name="abstract_review_criteria_id[]" id="abstract_review_criteria_id_<?= $criteriaCounter ?>"
								value="<?= $rowReviewCriteria['criteriaId'] ?>" operationMode="abstract_review_criteria_id"
								criteriaTitle="<?= $rowReviewCriteria['review_criteria'] ?>"
								fullMarks="<?= $rowReviewCriteria['full_marks'] ?>" />

						</td>
						<?php
						if ($rowReviewCriteria['score_option'] == 'dropdown') {
						?>
							<td>
								<table width="100%">
									<?php
									$i = 1;
									$k = 1;
									foreach (json_decode($rowReviewCriteria['review_category_id']) as $key => $value) {



										$sqlReviewCategory			  =	array();
										$sqlReviewCategory['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_REVIEW_SCORE_ . " 
																			  WHERE `status` ='A' AND id='" . $value . "'
																		   ORDER BY `id` ASC";


										$resultReviewCategory = $mycms->sql_select($sqlReviewCategory);

										// echo '<pre>'; print_r($reviewResultArray);
										if ($i % 2 == 0) {
											$cls = '#FFFF91';
										} else {
											$cls = '#FFC7C6';
										}
									?>
										<tr>
											<td valign="top" style="background:<?= $cls ?>;"> <!-- <?= $resultReviewCategory[0]['category'] ?> : -->
												<span style="float:right;"> <?= $resultReviewCategory[0]['score'] == -1 ? $resultReviewCategory[0]['category'] : $resultReviewCategory[0]['score'] ?> &nbsp;&nbsp;
													<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
														id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
														operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>" criteriaOptionId="<?= $resultReviewCategory[0]['id'] ?>"
														value="<?= $resultReviewCategory[0]['score'] == -1 ? $resultReviewCategory[0]['category'] : $resultReviewCategory[0]['score'] ?>@@<?= $resultReviewCategory[0]['id'] ?>" <?= ($reviewResultArray[$rowReviewCriteria['criteriaId']]['CRITERIA_OPTION_ID'] == $resultReviewCategory[0]['id']) ? 'checked' : '' ?> reviewMarksId="<?= $resultReviewCategory[0]['score'] == -1 ? 0 : $resultReviewCategory[0]['score'] ?>"
														style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required />
												</span>
											</td>
										<tr>
										<?php
										$i++;
									}
										?>
										<!-- <tr>
											<td valign="top" style="background:#FFFF91;"> Fair & Good :
												<span style="float:right;"> 1 &nbsp;&nbsp;
												<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]" 
													   id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
												 	   operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"   
												 	   value="1" <?= ($obtainedMarks == '1') ? 'checked' : '' ?> reviewMarksId="1"
													   style="float:right; -webkit-appearance: checkbox; -moz-appearance: checkbox; -ms-appearance: checkbox;" required/>
												</span>
											</td>
										<tr>
										<tr>
											<td valign="top" style="background:#9DFF9D;"> Very Good & Excellent :
												<span style="float:right;"> 2 &nbsp;&nbsp;
												<input type="radio" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]" 
													   id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
												 	   operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"   
												 	   value="2" <?= ($obtainedMarks == '2') ? 'checked' : '' ?> reviewMarksId="2"
													   style="float:right; -webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" required/>
												</span>
											</td>
										<tr> -->
								</table>
							</td>
						<?php
						} else {
						?>
							<td><input type="text" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]" operationMode="abstract_review_marks" marks="<?= $rowReviewCriteria['full_marks'] ?>" id="abstract_review_option_id<?= $k ?>" countdata="<?= $k ?>" value="<?= $textboxvalue ?>" required></td>
						<?php
						}
						?>
					</tr>
				<?php
					$k++;
				}
				?>
				<tr>
					<td align="left" colspan="2">
						<i>Remarks</i><br>
						<textarea name="faculty_review" <?= $status == true ? 'readonly=readonly' : '' ?>
							cols="35px" rows="5"><?= $rowReviewCriteria['faculty_review'] ?></textarea>
					</td>
				</tr>

				<tr>
					<td align="left" colspan="2">
						<div style="font-size:20px; font-weight:bold; margin:17px 0 10px 0;">
							Marks Obtained:
							<span operationMode="abstract_review_marks_obtained" style="color:#D82133"><?= $obtainedFullMarks ?></span>
							/ <span><?= $totalFullMarks ?></span>
						</div>
					</td>
				</tr>
			</table>

			<style>
				.hangingReviewMark {
					background: #D82133;

					font-size: 25px;
					width: 150px;
					text-align: center;
					font-weight: bold;
					position: fixed;
					left: 20px;
					bottom: 20px;
					padding: 30px 10px 30px 10px;
					border: 1px solid #C00;
				}
			</style>

			<div class="hangingReviewMark">
				<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
				<div style="width:100%; height:12px;"></div>
				<span operationMode="abstract_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF;"><?= $obtainedFullMarks ?></span>
				<span style="font-size:40px; margin-top:15px; color:#FFFFFF;">/ <?= $totalFullMarks ?></span>
			</div>
			<script type="text/javascript">
				function isNumber(evt) {
					evt = (evt) ? evt : window.event;
					var charCode = (evt.which) ? evt.which : evt.keyCode;
					if (charCode > 31 && (charCode < 48 || charCode > 57)) {
						return false;
					}
					return true;
				}
			</script>
		<?php
	}
}

function casereportReviewDisplayPanel($abstractId, $abstractResultSet)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";

	$sqlReviewCriteria['QUERY']			= "SELECT reviewResultDetails.*,
											  
											  criteria.id AS criteriaId,
											  criteria.review_criteria,
											  criteria.review_selection,
											  
											  criteriaOptions.id AS criteriaOptionsId,
											  criteriaOptions.review_option_title   
									
									     FROM " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
													  
							       INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
									       ON reviewResult.id = reviewResultDetails.review_result_id 
								   
								   INNER JOIN " . _DB_ABSTRACT_REVIEW_CRITERIA_ . " criteria 
								           ON reviewResultDetails.review_criteria_id = criteria.id  
										   	
							  LEFT OUTER JOIN " . _DB_ABSTRACT_REVIEW_CRITERIA_OPTIONS_ . " criteriaOptions
										   ON criteriaOptions.review_criteria_id = criteria.id 
										  AND criteriaOptions.id = reviewResultDetails.review_criteria_option_id 
										  AND criteriaOptions.status = 'A' 
										   	   
										WHERE criteria.status = 'A' 
										AND criteria.id IN(1,2,4,6,11) 
										  
										  AND reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
										  AND reviewResult.abstract_id = '" . $abstractId . "' 
										  AND reviewResult.status = 'A' 
										  AND reviewResultDetails.status = 'A'
									 
									 ORDER BY criteria.sequence_by ASC, criteriaOptions.review_option_sequence ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
	if ($resultReviewCriteria) {
		?>
			<table width="100%">
				<?php
				foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
					$criteriaCounter++;

					if ($tempCriteriaName != $rowReviewCriteria['review_criteria']) {
				?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top">&bull;</td>
							<td align="left" valign="top"><?= $rowReviewCriteria['review_criteria'] ?></td>
						</tr>
					<?php
					}
					if ($rowReviewCriteria['id'] != "" && $rowReviewCriteria['review_selection'] == "OPTION") {
					?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top">


								<i>Marks:</i> <?= $rowReviewCriteria['review_obtained_marks'] ?>

							</td>
						</tr>
					<?php
					} else if ($rowReviewCriteria['review_selection'] == "TEXT") {
					?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top">
								<i>Marks:</i> <?= $rowReviewCriteria['review_obtained_marks'] ?>
							</td>
						</tr>
				<?php
					}

					$tempCriteriaName   = $rowReviewCriteria['review_criteria'];
				}
				?>
				<tr>
					<td align="left" colspan="2">
						<div style="font-size:30px; font-weight:bold; margin:17px 0 10px 0;">
							Marks Obtained:
							<span style="color:#D82133"><?= $abstractResultSet['totalMarksObtained'] ?></span>
						</div>
					</td>
				</tr>
			</table>
		<?php
	}
}

function abstractReviewProcess($abstractId)
{
	global $cfg, $mycms;

	// echo '<pre>'; print_r($_REQUEST); 

	$loggedUserID 						= $mycms->getLoggedUserId();
	$loggedUserType 					= $mycms->getLoggedUserType();
	$totalMarksObtained         		= 0;

	// FETCHING EARLY REVIEW RESULTS
	$sqlFetchPrevReviewResult['QUERY']           = "  SELECT * 
															FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
														   WHERE `abstract_id` = '" . $abstractId . "'
															 AND `faculty_id` = '" . $loggedUserID . "'";
	$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);
	if ($resultFetchPrevReviewResult) {
		foreach ($resultFetchPrevReviewResult as $keyPrevReviewResult => $rowPrevReviewResult) {
			// MAKE EARLY REVIEW INACTIVE
			$sqlUpdatePrevReviewResult['QUERY']  = "   UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
															  SET `status` = 'I' 
															WHERE `id` = '" . $rowPrevReviewResult['id'] . "'";

			$mycms->sql_update($sqlUpdatePrevReviewResult);

			// MAKE EARLY REVIEW DETAILS INACTIVE
			$sqlUpdatePrevReviewDetails['QUERY'] = "   UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " 
															  SET `status` = 'I'
															WHERE `review_result_id` = '" . $rowPrevReviewResult['id'] . "'";

			$mycms->sql_update($sqlUpdatePrevReviewDetails);
		}
	}

	if ($_REQUEST['faculty_decision'] == 'NOTSUBJECT') {
		$sqlInsert = array();
		$sqlInsert['QUERY']         =  "UPDATE " . _DB_ABSTRACT_ALLOTMENT_ . " 
											   SET `notmystuff` = 'NOTMYSTUFF'
											 WHERE `abstract_id` = '" . $abstractId . "'
											   AND `review_user_id` = '" . $loggedUserID . "'";
		$mycms->sql_update($sqlInsert, false);
	} else {
		$sqlInsert = array();
		$sqlInsert['QUERY']         =  "UPDATE " . _DB_ABSTRACT_ALLOTMENT_ . " 
											   SET `notmystuff` = 'MYSTUFF'
											 WHERE `abstract_id` = '" . $abstractId . "'
											   AND `review_user_id` = '" . $loggedUserID . "'";
		$mycms->sql_update($sqlInsert, false);

		// INSERT ABSTRACT REVIEW RESULT OPERATION
		$sqlInsertReviewResult['QUERY']      		= "INSERT INTO " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
																   SET `abstract_id` = '" . $abstractId . "', 
																	   `faculty_id` = '" . $loggedUserID . "', 
																	   `status` = 'A', 
																	   `created_by` = '" . $loggedUserID . "',
																	   `created_userType` = '" . $loggedUserType . "',
																	   `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																	   `created_sessionId` = '" . session_id() . "', 
																	   `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
		$lastInsertedReviewResultId = $mycms->sql_insert($sqlInsertReviewResult);

		// ABSTRACT REVIEW DETAILS RELATED OPERATION
		$reviewCriteriaArray 				= $_REQUEST['abstract_review_criteria_id'];
		$netMarks = 0;
		foreach ($reviewCriteriaArray as $keyCriteria => $valReviewCriteriaId) {
			// OPERATION RELATED OPTION VALUES
			$scoreDetailsArray                     = explode('@@', trim($_REQUEST['abstract_review_option_id_inval'][$valReviewCriteriaId]));
			$abstract_review_option_id             = addslashes($scoreDetailsArray[1] == '' ? '-1' : $scoreDetailsArray[1]);
			$abstract_review_option_id_val         = addslashes($scoreDetailsArray[0]);
			$abstract_review_option_id_val_numeric = trim(is_numeric($scoreDetailsArray[0]) ? $scoreDetailsArray[0] : 0);

			if ($abstract_review_option_id != '-1') {
				$sqlOpt = array();
				$sqlOpt['QUERY'] = "SELECT category, score FROM " . _DB_ABSTRACT_REVIEW_SCORE_ . "
									WHERE id = '" . (int)$scoreDetailsArray[1] . "' LIMIT 1";
				$resOpt = $mycms->sql_select($sqlOpt);

				if ($resOpt && $resOpt[0]['category'] === 'Grade') {
					$abstract_review_option_id_val         = addslashes($resOpt[0]['score']);
					$abstract_review_option_id_val_numeric = '0';
				}
			}
            $abstract_review_option_id_val_numeric = (string)$abstract_review_option_id_val_numeric;

			if ($abstract_review_option_id_val_numeric != "") {
               $netMarks += (float)$abstract_review_option_id_val_numeric;

				// INSERT ABSTRACT REVIEW RESULT DETAILS review_criteria_option_id
				$sqlInsertReviewDetails['QUERY']     = "INSERT INTO " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " 
																   SET `review_result_id` = '" . $lastInsertedReviewResultId . "', 
																	   `review_criteria_id` = '" . $valReviewCriteriaId . "', 
																	   `review_criteria_option_id` = '" . $abstract_review_option_id . "', 
																	   `review_obtained_marks` = '" . $abstract_review_option_id_val . "', 
																	   `review_obtained_marks_invalue` = '" . $abstract_review_option_id_val_numeric . "',
																	   `status` = 'A', 
																	   `created_by` = '" . $loggedUserID . "',
																	   `created_userType` = '" . $loggedUserType . "',
																	   `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																	   `created_sessionId` = '" . session_id() . "', 
																	   `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
				// echo "-----<br>";
				$mycms->sql_insert($sqlInsertReviewDetails);
			}

			// OPERATION RELATED TEXT VALUES
			$abstract_review_marks_val      = addslashes(trim($_POST['abstract_review_marks'][$valReviewCriteriaId]));

			if ($abstract_review_marks_val != "") {
				$totalMarksObtained        = ($abstract_review_marks_val + $netMarks);

				// INSERT ABSTRACT REVIEW RESULT DETAILS
				$sqlInsertReviewDetails['QUERY']     = "INSERT INTO " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " 
														   SET `review_result_id` = '" . $lastInsertedReviewResultId . "', 
															   `review_criteria_id` = '" . $valReviewCriteriaId . "', 
															   `review_criteria_option_id` = '0', 
															   `review_individual_assessment_marks` = '" . $abstract_review_marks_val . "', 
															   `status` = 'A', 
															   `created_by` = '" . $loggedUserID . "',
															   `created_userType` = '" . $loggedUserType . "',
															   `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
															   `created_sessionId` = '" . session_id() . "', 
															   `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";

				// $mycms->sql_insert($sqlInsertReviewDetails); 
			}
		}
		// die;
		// UPDATE ABSTRACT REVIEW RESULT OPERATION
		$sqlUpdateReviewResult['QUERY']      		= "UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
															  SET `marks_obtained` = '" . $netMarks . "',
																  `status` = 'A', 
																  `faculty_review` = '" . addslashes(trim($_REQUEST['faculty_review'])) . "',
																  `created_by` = '" . $loggedUserID . "',
																  `created_userType` = '" . $loggedUserType . "',
																  `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																  `created_sessionId` = '" . session_id() . "', 
																  `created_dateTime` = '" . date('Y-m-d H:i:s') . "'
															WHERE `id` = '" . $lastInsertedReviewResultId . "'";
		$mycms->sql_update($sqlUpdateReviewResult);
	}
}

/////////// DEPRECATED  //////////////
function getReviewerGrade($abstractId, $facultyId)
{
    global $mycms;

    $sql = array();
    $sql['QUERY'] = "SELECT d.review_obtained_marks AS grade
                       FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " r
                 INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " d
                         ON d.review_result_id = r.id AND d.status = 'A'
                 INNER JOIN " . _DB_ABSTRACT_REVIEW_SCORE_ . " s
                         ON s.id = d.review_criteria_option_id AND s.category = 'Grade'
                      WHERE r.abstract_id = '" . (int)$abstractId . "'
                        AND r.faculty_id  = '" . addslashes($facultyId) . "'
                        AND r.status = 'A'
                   ORDER BY d.id ASC";

    $res = $mycms->sql_select($sql);
    return $res ? implode(', ', array_column($res, 'grade')) : '';
}

function abstractReviewUpdatePanel_20190708($abstractId, $abstractResultSet, $status)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";



	$sqlReviewCriteria['QUERY']			= "SELECT criteria.*,
											  
											  criteria.id AS criteriaId,
											  criteria.review_criteria,
											  criteria.review_selection,
											  
											  reviewResultDetailsId,
											  resultCriteriaId,
											  resultCriteriaOptionId,
											  resultMarksObtained,
											  resultMarksObtainedInvalue,
											  review_individual_assessment_marks
		
										 FROM " . _DB_ABSTRACT_REVIEW_CRITERIA_ . " criteria 
							
										  
							  LEFT OUTER JOIN (
												    SELECT reviewResult.id AS reviewResultId, 
													
														   reviewResultDetails.id AS reviewResultDetailsId,
														   reviewResultDetails.review_criteria_id AS resultCriteriaId,
														   reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
														   reviewResultDetails.review_obtained_marks AS resultMarksObtained,
														   reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
														   reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks
														    
													  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
													  
												INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
														ON reviewResult.id = reviewResultDetails.review_result_id 
														
													 WHERE reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
													   AND reviewResult.abstract_id = '" . $abstractId . "' 
													   AND reviewResult.status = 'A' 
													   AND reviewResultDetails.status = 'A' 
													  
											  ) reviewResultDetails 
										   ON reviewResultDetails.resultCriteriaId = criteria.id 
											   
										WHERE criteria.status = 'A'
										  AND criteria.criteria_for = 'abstract'
									 
									 ORDER BY criteria.sequence_by ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
	if ($resultReviewCriteria) {
		$reviewResultArray      = array();

		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']          = $rowReviewCriteria['resultCriteriaId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']   = $rowReviewCriteria['resultCriteriaOptionId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	   = $rowReviewCriteria['resultMarksObtained'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS_VALUE'] = $rowReviewCriteria['resultMarksObtainedInvalue'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['INTERNAL_ASSESSMENT']  = $rowReviewCriteria['review_individual_assessment_marks'];
		}
		?>
			<table width="100%">
				<?php
				foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
					$criteriaCounter++;

					if ($tempCriteriaName != $rowReviewCriteria['review_criteria']) {
				?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top">&bull;</td>
							<td align="left" valign="top">
								<input type="hidden" name="abstract_review_criteria_id[]" id="abstract_review_criteria_id_<?= $criteriaCounter ?>"
									value="<?= $rowReviewCriteria['criteriaId'] ?>" operationMode="abstract_review_criteria_id"
									criteriaTitle="<?= $rowReviewCriteria['review_criteria'] ?>"
									fullMarks="<?= $rowReviewCriteria['full_marks'] ?>" />
								<b><?= $rowReviewCriteria['review_criteria'] ?></b>
							</td>
						</tr>
					<?php
					}
					if ($rowReviewCriteria['id'] != "" && $rowReviewCriteria['review_selection'] == "OPTION") {
						$totalFullMarks 	+= $rowReviewCriteria['full_marks'];
						$obtainedFullMarks  += round($rowReviewCriteria['resultMarksObtainedInvalue'], 0);
						$obtainedMarks		= (trim($rowReviewCriteria['resultMarksObtainedInvalue']) != '') ? round($rowReviewCriteria['resultMarksObtainedInvalue'], 0) : "";
					?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top" style="margin: 0px; padding: 0px;">

								<table width="100%">
									<tr>
										<td valign="top">Marks :
											<input type="text" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]"
												id="abstract_review_option_id_inval<?= $criteriaCounter ?>"
												operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
												value="<?= $obtainedMarks ?>"
												size="2" style="width:50px;" /> / <?= $rowReviewCriteria['full_marks'] ?>
										</td>
									<tr>
									</tr>
									<td style="border:none;"><i>Remarks</i><br>
										<textarea name="abstract_review_option_id[<?= $rowReviewCriteria['criteriaId'] ?>]" <?= $status == true ? 'readonly=readonly' : '' ?>
											cols="35px" rows="5" id="abstract_review_option_id_<?= $criteriaCounter ?>"
											criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
											reviewMarksId="<?= $rowReviewCriteria['resultMarksObtainedInvalue'] ?>"
											value="<?= $rowReviewCriteria['id'] ?>"><?= $rowReviewCriteria['resultMarksObtained'] ?></textarea>
									</td>
						</tr>
			</table>

			</td>
			</tr>
		<?php
					} else if ($rowReviewCriteria['review_selection'] == "TEXT") {
		?>
			<tr class="tlisting">
				<td width="5" align="center" valign="top"></td>
				<td align="left" valign="top">

					<input type="text" name="abstract_review_marks[<?= $rowReviewCriteria['criteriaId'] ?>]" id="abstract_review_marks_<?= $criteriaCounter ?>" <?= $status == true ? 'readonly=readonly' : '' ?>
						operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>" style="width:263px;"
						value="<?= $reviewResultArray[$rowReviewCriteria['criteriaId']]['INTERNAL_ASSESSMENT'] ?>" />

				</td>
			</tr>
	<?php
					}

					$tempCriteriaName   = $rowReviewCriteria['review_criteria'];
				}
	?>
	<tr>
		<td align="left" colspan="2">
			<div style="font-size:30px; font-weight:bold; margin:17px 0 10px 0;">
				Marks Obtained:
				<span operationMode="abstract_review_marks_obtained" style="color:#D82133"><?= $obtainedFullMarks ?></span>
				/ <span><?= $totalFullMarks ?></span>
			</div>
		</td>
	</tr>
	</table>

	<style>
		.hangingReviewMark {
			background: #D82133;

			font-size: 25px;
			width: 150px;
			text-align: center;
			font-weight: bold;
			position: fixed;
			left: 20px;
			bottom: 20px;
			padding: 30px 10px 30px 10px;
			border: 1px solid #C00;
		}
	</style>

	<div class="hangingReviewMark">
		<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
		<div style="width:100%; height:12px;"></div>
		<span operationMode="abstract_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF;"><?= $obtainedFullMarks ?></span>
		<span style="font-size:40px; margin-top:15px; color:#FFFFFF;">/ <?= $totalFullMarks ?></span>
	</div>
<?php
	}
}

function casereportReviewUpdatePanel_20190708($abstractId, $abstractResultSet, $status)
{
	global $cfg, $mycms;

	$criteriaCounter            = 0;
	$tempCriteriaName           = "";



	$sqlReviewCriteria['QUERY']			= "SELECT criteria.*,
											  
											  criteria.id AS criteriaId,
											  criteria.review_criteria,
											  criteria.review_selection,
											  
											  reviewResultDetailsId,
											  resultCriteriaId,
											  resultCriteriaOptionId,
											  resultMarksObtained,
											  resultMarksObtainedInvalue,
											  review_individual_assessment_marks  
		
										 FROM " . _DB_ABSTRACT_REVIEW_CRITERIA_ . " criteria 
							
										  
							  LEFT OUTER JOIN (
							  
													SELECT reviewResult.id AS reviewResultId, 
													
														  reviewResultDetails.id AS reviewResultDetailsId,
														  reviewResultDetails.review_criteria_id AS resultCriteriaId,
														  reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
														  reviewResultDetails.review_obtained_marks AS resultMarksObtained,
														  reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
														  reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks 
													  
													  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
													  
												INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
														ON reviewResult.id = reviewResultDetails.review_result_id 
														
													 WHERE reviewResult.faculty_id = '" . $mycms->getLoggedUserId() . "' 
													   AND reviewResult.abstract_id = '" . $abstractId . "' 
													   AND reviewResult.status = 'A' 
													   AND reviewResultDetails.status = 'A' 
													  
											  ) reviewResultDetails 
										   ON reviewResultDetails.resultCriteriaId = criteria.id 
											   
										WHERE criteria.status = 'A'
										  AND criteria.criteria_for = 'caseReport'
									 
									 ORDER BY criteria.sequence_by ASC";



	$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
	if ($resultReviewCriteria) {
		$reviewResultArray      = array();

		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']         = $rowReviewCriteria['resultCriteriaId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']  = $rowReviewCriteria['resultCriteriaOptionId'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	= $rowReviewCriteria['resultMarksObtained'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS_VALUE'] = $rowReviewCriteria['resultMarksObtainedInvalue'];
			$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['INTERNAL_ASSESSMENT'] = $rowReviewCriteria['review_individual_assessment_marks'];
		}
?>
	<table width="100%">
		<?php
		foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
			$criteriaCounter++;

			if ($tempCriteriaName != $rowReviewCriteria['review_criteria']) {
		?>
				<tr class="tlisting">
					<td width="5" align="center" valign="top">&bull;</td>
					<td align="left" valign="top">
						<input type="hidden" name="abstract_review_criteria_id[]" id="abstract_review_criteria_id_<?= $criteriaCounter ?>"
							value="<?= $rowReviewCriteria['criteriaId'] ?>" operationMode="abstract_review_criteria_id"
							criteriaTitle="<?= $rowReviewCriteria['review_criteria'] ?>"
							fullMarks="<?= $rowReviewCriteria['full_marks'] ?>" />
						<b><?= $rowReviewCriteria['review_criteria'] ?></b>
					</td>
				</tr>
			<?php
			}
			if ($rowReviewCriteria['id'] != "" && $rowReviewCriteria['review_selection'] == "OPTION") {
				$totalFullMarks 	+= $rowReviewCriteria['full_marks'];
				$obtainedFullMarks  += round($rowReviewCriteria['resultMarksObtainedInvalue'], 0);
				$obtainedMarks		= (trim($rowReviewCriteria['resultMarksObtainedInvalue']) != '') ? round($rowReviewCriteria['resultMarksObtainedInvalue'], 0) : "";
			?>
				<tr class="tlisting">
					<td width="5" align="center" valign="top"></td>
					<td align="left" valign="top" style="margin: 0px; padding: 0px;">

						<table width="100%">
							<tr>
								<td valign="top">Marks :
									<input type="text" name="abstract_review_option_id_inval[<?= $rowReviewCriteria['criteriaId'] ?>]" id="abstract_review_option_id_inval<?= $criteriaCounter ?>" cols="35px" rows="5"
										operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
										value="<?= $obtainedMarks ?>"
										size="2" style="width:50px;" /> / <?= $rowReviewCriteria['full_marks'] ?>
								</td>
							</tr>
							<tr>
								<td style="border:none;"><i>Remarks</i><br>
									<textarea name="abstract_review_option_id[<?= $rowReviewCriteria['criteriaId'] ?>]" <?= $status == true ? 'readonly=readonly' : '' ?> cols="35px" rows="5" id="abstract_review_option_id_<?= $criteriaCounter ?>"
										criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>"
										reviewMarksId="<?= $rowReviewCriteria['review_option_marks'] ?>"
										value="<?= $rowReviewCriteria['id'] ?>"><?= $rowReviewCriteria['resultMarksObtained'] ?></textarea>
								</td>

							</tr>
						</table>

					</td>
				</tr>
			<?php
			} else if ($rowReviewCriteria['review_selection'] == "TEXT") {
			?>
				<tr class="tlisting">
					<td width="5" align="center" valign="top"></td>
					<td align="left" valign="top">

						<input type="text" name="abstract_review_marks[<?= $rowReviewCriteria['criteriaId'] ?>]" id="abstract_review_marks_<?= $criteriaCounter ?>" <?= $status == true ? 'readonly=readonly' : '' ?>
							operationMode="abstract_review_marks" criteriaId="<?= $rowReviewCriteria['criteriaId'] ?>" style="width:263px;"
							value="<?= $reviewResultArray[$rowReviewCriteria['criteriaId']]['INTERNAL_ASSESSMENT'] ?>" />

					</td>
				</tr>
		<?php
			}

			$tempCriteriaName   = $rowReviewCriteria['review_criteria'];
		}
		?>
		<tr>
			<td align="left" colspan="2">
				<div style="font-size:30px; font-weight:bold; margin:17px 0 10px 0;">
					Marks Obtained:
					<span operationMode="abstract_review_marks_obtained" style="color:#D82133"><?= round($obtainedFullMarks) ?></span>
					/ <span><?= $totalFullMarks ?></span>
				</div>
			</td>
		</tr>
	</table>

	<style>
		.hangingReviewMark {
			background: #D82133;

			font-size: 25px;
			width: 150px;
			text-align: center;
			font-weight: bold;
			position: fixed;
			left: 20px;
			bottom: 20px;
			padding: 30px 10px 30px 10px;
			border: 1px solid #C00;
		}
	</style>

	<div class="hangingReviewMark">
		<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
		<div style="width:100%; height:12px;"></div>
		<span operationMode="abstract_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF;"><?= $obtainedFullMarks ?></span>
		<span style="font-size:40px; margin-top:15px; color:#FFFFFF;">/ <?= $totalFullMarks ?></span>
	</div>
<?php
	}
}
?>