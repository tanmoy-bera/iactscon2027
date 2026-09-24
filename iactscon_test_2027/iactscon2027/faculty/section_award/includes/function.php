<?php
	function awardReviewUpdatePanel($awardId, $awardResultSet)
	{
		global $cfg, $mycms;
		
		$criteriaCounter            = 0;
		$tempCriteriaName           = "";
		
		$sqlReviewCriteria['QUERY']			= "SELECT criteriaOptions.*,
											  
											  criteria.id AS criteriaId,
											  criteria.review_criteria,
											  criteria.review_selection,
											  
											  reviewResultDetailsId,
											  resultCriteriaId,
											  resultCriteriaOptionId,
											  resultMarksObtained  
		
										 FROM "._DB_ABSTRACT_REVIEW_CRITERIA_." criteria 
								   
							  LEFT OUTER JOIN "._DB_ABSTRACT_REVIEW_CRITERIA_OPTIONS_." criteriaOptions
										   ON criteriaOptions.review_criteria_id = criteria.id 
										  AND criteriaOptions.status = 'A' 
										  
							  LEFT OUTER JOIN (
							  
													SELECT reviewResult.id AS reviewResultId, 
													
														  reviewResultDetails.id AS reviewResultDetailsId,
														  reviewResultDetails.review_criteria_id AS resultCriteriaId,
														  reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
														  reviewResultDetails.review_obtained_marks AS resultMarksObtained  
													  
													  FROM "._DB_AWARD_REVIEW_RESULT_." reviewResult 
													  
												INNER JOIN "._DB_AWARD_REVIEW_RESULT_DETAILS_." reviewResultDetails 
														ON reviewResult.id = reviewResultDetails.review_result_id 
														
													 WHERE reviewResult.faculty_id = '".$mycms->getLoggedUserId()."' 
													   AND reviewResult.award_id = '".$awardId."' 
													   AND reviewResult.status = 'A' 
													   AND reviewResultDetails.status = 'A' 
													  
											  ) reviewResultDetails 
										   ON reviewResultDetails.resultCriteriaId = criteria.id 
											   
										WHERE criteria.status = 'A' 
									 
									 ORDER BY criteria.sequence_by ASC, criteriaOptions.review_option_sequence ASC";
		
		
		
		$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
		if($resultReviewCriteria)
		{
			$reviewResultArray      = array();
			
			foreach($resultReviewCriteria as $keyReviewCriteria=>$rowReviewCriteria)
			{
				$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_ID']         = $rowReviewCriteria['resultCriteriaId'];
				$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['CRITERIA_OPTION_ID']  = $rowReviewCriteria['resultCriteriaOptionId'];
				$reviewResultArray[$rowReviewCriteria['resultCriteriaId']]['OBTAINED_MARKS']  	= $rowReviewCriteria['resultMarksObtained'];
			}
		?>
			<table width="100%">
		<?php
				foreach($resultReviewCriteria as $keyReviewCriteria=>$rowReviewCriteria)
				{
					$criteriaCounter++;
					
					if($tempCriteriaName != $rowReviewCriteria['review_criteria'])
					{
		?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top">&bull;</td>
							<td align="left" valign="top">
								<input type="hidden" name="award_review_criteria_id[]" id="award_review_criteria_id_<?=$criteriaCounter?>" 
								 value="<?=$rowReviewCriteria['criteriaId']?>" operationMode="award_review_criteria_id" 
								 criteriaTitle="<?=$rowReviewCriteria['review_criteria']?>" />
								
								<?=$rowReviewCriteria['review_criteria']?> 
							</td>
						</tr>
		<?php
					}
					if($rowReviewCriteria['id']!="" && $rowReviewCriteria['review_selection']=="OPTION")
					{
		?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top" style="margin: 0px; padding: 0px;"> 
								
								<table width="100%">
									<tr>
										<td width="7" style="border:none;">
											<input type="radio" name="award_review_option_id[<?=$rowReviewCriteria['criteriaId']?>]" id="award_review_option_id_<?=$criteriaCounter?>" 
											 operationMode="award_review_marks" criteriaId="<?=$rowReviewCriteria['criteriaId']?>" 
											 reviewMarksId="<?=$rowReviewCriteria['review_option_marks']?>" value="<?=$rowReviewCriteria['id']?>" 
											 <?=($rowReviewCriteria['id']==$reviewResultArray[$rowReviewCriteria['criteriaId']]['CRITERIA_OPTION_ID'])?'checked="checked"':''?> />
										</td>
										<td width="90" style="border:none;">
											<?=$rowReviewCriteria['review_option_title']?>
										</td>
										<td style="border:none;">
											<?=$rowReviewCriteria['review_option_marks']?> Point
										</td>
									</tr>
								</table>
							
							</td>
						</tr>
		<?php
					}
					else if($rowReviewCriteria['review_selection']=="TEXT")
					{
		?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top">
								 
								<input type="text" name="award_review_marks[<?=$rowReviewCriteria['criteriaId']?>]" id="award_review_marks_<?=$criteriaCounter?>" 
								 operationMode="award_review_marks" criteriaId="<?=$rowReviewCriteria['criteriaId']?>" style="width:148px;"
								 value="<?=$reviewResultArray[$rowReviewCriteria['criteriaId']]['OBTAINED_MARKS']?>" /> Point
							
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
							<span operationMode="award_review_marks_obtained" style="color:#D82133"><?=$awardResultSet['marks_obtained']?></span>
						</div> 
					</td>
				</tr>
			</table>
			
			<style>
				.hangingReviewMark { 	
					background: #D82133;
					
					font-size:25px; 
					width:150px; 
					text-align:center; 
					font-weight:bold; 
					position: fixed; 
					left:20px; 
					bottom:20px;  
					padding:30px 10px 30px 10px; 
					border:1px solid #C00;
				}
			</style>
			
			<div class="hangingReviewMark">
				<span style="font-size:20px; color:#FFFFFF;">Marks Obtained</span>
				<div style="width:100%; height:12px;"></div>
				<span operationMode="award_review_marks_obtained" style="font-size:40px; margin-top:15px; color:#FFFFFF"><?=$awardResultSet['marks_obtained']?></span>
			</div> 
		<?php
		}
	}
	
	function awardReviewDisplayPanel($awardId, $awardResultSet)
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
									
									     FROM "._DB_AWARD_REVIEW_RESULT_DETAILS_." reviewResultDetails 
													  
							       INNER JOIN "._DB_AWARD_REVIEW_RESULT_." reviewResult 
									       ON reviewResult.id = reviewResultDetails.review_result_id 
								   
								   INNER JOIN "._DB_ABSTRACT_REVIEW_CRITERIA_." criteria 
								           ON reviewResultDetails.review_criteria_id = criteria.id  
										   	
							  LEFT OUTER JOIN "._DB_ABSTRACT_REVIEW_CRITERIA_OPTIONS_." criteriaOptions
										   ON criteriaOptions.review_criteria_id = criteria.id 
										  AND criteriaOptions.id = reviewResultDetails.review_criteria_option_id 
										  AND criteriaOptions.status = 'A' 
										   	   
										WHERE criteria.status = 'A' 
										  
										  AND reviewResult.faculty_id = '".$mycms->getLoggedUserId()."' 
										  AND reviewResult.award_id = '".$awardId."' 
										  AND reviewResult.status = 'A' 
										  AND reviewResultDetails.status = 'A'
									 
									 ORDER BY criteria.sequence_by ASC, criteriaOptions.review_option_sequence ASC";
		
		
		
		$resultReviewCriteria		= $mycms->sql_select($sqlReviewCriteria);
		if($resultReviewCriteria)
		{
		?>
			<table width="100%">
		<?php
				foreach($resultReviewCriteria as $keyReviewCriteria=>$rowReviewCriteria)
				{
					$criteriaCounter++;
					
					if($tempCriteriaName != $rowReviewCriteria['review_criteria'])
					{
		?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top">&bull;</td>
							<td align="left" valign="top"><?=$rowReviewCriteria['review_criteria']?></td>
						</tr>
		<?php
					}
					if($rowReviewCriteria['id']!="" && $rowReviewCriteria['review_selection']=="OPTION")
					{
		?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top"> 
							
								<i>Option:</i> <?=$rowReviewCriteria['review_option_title']?>
								<br />
								<i>Marks:</i>  <?=$rowReviewCriteria['review_obtained_marks']?> Point
							
							</td>
						</tr>
		<?php
					}
					else if($rowReviewCriteria['review_selection']=="TEXT")
					{
		?>
						<tr class="tlisting">
							<td width="5" align="center" valign="top"></td>
							<td align="left" valign="top">
								<i>Marks:</i>  <?=$rowReviewCriteria['review_obtained_marks']?> Point
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
							<span style="color:#D82133"><?=$awardResultSet['marks_obtained']?></span>
						</div> 
					</td>
				</tr>
			</table>
		<?php
		}
	}
	
	function awardReviewProcess($awardId)
	{
		global $cfg, $mycms;
		
		$loggedUserID 						= $mycms->getLoggedUserId();
		$loggedUserType 					= $mycms->getLoggedUserType();
		
		$totalMarksObtained         		= 0;
		
		// INSERT AWARD REVIEW RESULT OPERATION
		$sqlInsertReviewResult['QUERY']      		= "INSERT INTO "._DB_AWARD_REVIEW_RESULT_." 
													   SET `award_id` = '".$awardId."', 
														   `faculty_id` = '".$loggedUserID."', 
														   `status` = 'A', 
														   `created_by` = '".$loggedUserID."',
														   `created_userType` = '".$loggedUserType."',
														   `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
														   `created_sessionId` = '".session_id()."', 
														   `created_dateTime` = '".date('Y-m-d H:i:s')."'";
												   
		$lastInsertedReviewResultId = $mycms->sql_insert($sqlInsertReviewResult);
		
		// AWARD REVIEW DETAILS RELATED OPERATION
		$reviewCriteriaArray 				= $_POST['award_review_criteria_id'];
		
		foreach($reviewCriteriaArray as $keyCriteria=>$valReviewCriteriaId)
		{
			// OPERATION RELATED OPTION VALUES
			$award_review_option_id_val 	= addslashes(trim($_POST['award_review_option_id'][$valReviewCriteriaId]));
			
			if($award_review_option_id_val != "")
			{
				$award_option_marks_val  = 0;
				$award_option_marks_val  = getAbstractReviewMarks($award_review_option_id_val);
				
				$totalMarksObtained        += $award_option_marks_val;
				
				// INSERT AWARD REVIEW RESULT DETAILS
				$sqlInsertReviewDetails['QUERY']     = "INSERT INTO "._DB_AWARD_REVIEW_RESULT_DETAILS_." 
													   SET `review_result_id` = '".$lastInsertedReviewResultId."', 
														   `review_criteria_id` = '".$valReviewCriteriaId."', 
														   `review_criteria_option_id` = '".$award_review_option_id_val."', 
														   `review_obtained_marks` = '".$award_option_marks_val."', 
														   `status` = 'A', 
														   `created_by` = '".$loggedUserID."',
														   `created_userType` = '".$loggedUserType."',
														   `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
														   `created_sessionId` = '".session_id()."', 
														   `created_dateTime` = '".date('Y-m-d H:i:s')."'";
														   
				$mycms->sql_insert($sqlInsertReviewDetails);      
			}
			
			// OPERATION RELATED TEXT VALUES
			$award_review_marks_val      = addslashes(trim($_POST['award_review_marks'][$valReviewCriteriaId]));
			
			if($award_review_marks_val != "")
			{
				$totalMarksObtained        += $award_review_marks_val;
				
				// INSERT AWARD REVIEW RESULT DETAILS
				$sqlInsertReviewDetails['QUERY']     = "INSERT INTO "._DB_AWARD_REVIEW_RESULT_DETAILS_." 
													   SET `review_result_id` = '".$lastInsertedReviewResultId."', 
														   `review_criteria_id` = '".$valReviewCriteriaId."', 
														   `review_criteria_option_id` = '0', 
														   `review_obtained_marks` = '".$award_review_marks_val."', 
														   `status` = 'A', 
														   `created_by` = '".$loggedUserID."',
														   `created_userType` = '".$loggedUserType."',
														   `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
														   `created_sessionId` = '".session_id()."', 
														   `created_dateTime` = '".date('Y-m-d H:i:s')."'";
														   
				$mycms->sql_insert($sqlInsertReviewDetails); 
			}
		}
		
		// UPDATE AWARD REVIEW RESULT OPERATION
		$sqlUpdateReviewResult['QUERY']      		= "UPDATE "._DB_AWARD_REVIEW_RESULT_." 
												  SET `marks_obtained` = '".$totalMarksObtained."',
													  `status` = 'A', 
													  `created_by` = '".$loggedUserID."',
													  `created_userType` = '".$loggedUserType."',
													  `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
													  `created_sessionId` = '".session_id()."', 
													  `created_dateTime` = '".date('Y-m-d H:i:s')."'
												WHERE `id` = '".$lastInsertedReviewResultId."'";
												   
		$mycms->sql_update($sqlUpdateReviewResult);
		
		// FETCHING EARLY REVIEW RESULTS
		$sqlFetchPrevReviewResult['QUERY']           = "SELECT * FROM "._DB_AWARD_REVIEW_RESULT_." 
													   WHERE `award_id` = '".$awardId."'
														 AND `faculty_id` = '".$loggedUserID."' 
														 AND `id` != '".$lastInsertedReviewResultId."'";
														 
		$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);
		
		if($resultFetchPrevReviewResult)
		{
			foreach($resultFetchPrevReviewResult as $keyPrevReviewResult=>$rowPrevReviewResult)
			{
				// MAKE EARLY REVIEW INACTIVE
				$sqlUpdatePrevReviewResult['QUERY']  = "UPDATE "._DB_AWARD_REVIEW_RESULT_." 
												  SET `status` = 'I' 
												WHERE `id` = '".$rowPrevReviewResult['id']."'";
				
				$mycms->sql_update($sqlUpdatePrevReviewResult);
				
				// MAKE EARLY REVIEW DETAILS INACTIVE
				$sqlUpdatePrevReviewDetails['QUERY'] = "UPDATE "._DB_AWARD_REVIEW_RESULT_DETAILS_." 
												  SET `status` = 'I'
												WHERE `review_result_id` = '".$rowPrevReviewResult['id']."'";
																   
				$mycms->sql_update($sqlUpdatePrevReviewDetails); 
			}
		}
	}
?>