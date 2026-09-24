<?php
include_once('includes/init.php');
include_once('../../includes/function.abstract.php');
include_once('../../includes/function.delegate.php');

$absCat = '';
$absSubCat = '';
if (!empty($_REQUEST['src_abstract_sub_cat_id'])) {
	$explodeSub = explode('-', $_REQUEST['src_abstract_sub_cat_id']);
	//print_r($explodeSub);
	$absCat = $explodeSub[0];
	$absSubCat = $explodeSub[1];
}

if (!empty($absCat) && $absCat > 0) {
	$category = $absCat;
} else {
	$category = $_REQUEST['cat'];
}

$sqlCategory	 =	array();
$sqlCategory['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
						  WHERE `status`='A' AND id='" . trim($category) . "'
					   ORDER BY `id` ASC";

$resultCategory = $mycms->sql_select($sqlCategory);
//print_r($resultCategory[0]['category']);

page_header($resultCategory[0]['category']);

$pageKey                       		       		 = "_pgn1_";
$pageKeyVal                    		       		 = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString                 		       		 = "";
$searchArray                   		       		 = array();

$searchArray[$pageKey]         		       		 = $pageKeyVal;
$searchArray['goto']  						 	 = trim($_REQUEST['goto']);
$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
$searchArray['src_abstract_topic_id']  		 	 = trim($_REQUEST['src_abstract_topic_id']);
$searchArray['src_paper_presentation_category']  = trim($_REQUEST['src_paper_presentation_category']);





foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}


$searchStatus = false;
foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchStatus = true;
	}
}

//echo 'show='.$show;
?>
<script language="javascript" src="<?= _BASE_URL_ ?>js/website/jquery-1.11.3.min.js"></script>
<script language="javascript" src="scripts/abstract.free_paper.js?x=<?= date("YmdHis") ?>"></script>
<div class="container" style="display: block;">
	<?php

	//echo 'show='.$show;
	switch ($show) {
		// ABSTRACT VIEW DETAILS WINDOW
		case 'view':
			abstractReviewDetailsWindow($cfg, $mycms);
			break;

		default:
			abstractListingDisplayWindow($cfg, $mycms);
			break;
	}
	?>
</div>
<?php

page_footer();

/****************************************************************************/
/*                      ABSTRACT LISTING DETAILS WINDOW                     */
/****************************************************************************/
/*function abstractListingDisplayWindow($cfg, $mycms)
	{
		global $searchString, $searchArray, $searchStatus;
		 $loggedUserID = $mycms->getLoggedUserId();

		 $absCat = '';
		 $absSubCat = '';
		 if(!empty($_REQUEST['src_abstract_sub_cat_id']))
		 {
		 	$explodeSub = explode('-',$_REQUEST['src_abstract_sub_cat_id']);
		 	//print_r($explodeSub);
		 	$absCat = $explodeSub[0];
		 	$absSubCat = $explodeSub[1];
		 }

		 if(!empty($absCat) && $absCat>0)
		 {
		 	$category = $absCat;
		 }
		 else
		 {
		 	$category = $_REQUEST['cat'];
		 }

		 //echo '<pre>'; print_r($_REQUEST);

		 //echo 'cat='.$category;

		 $sqlSubcat	 =	array();
					$sqlSubcat['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
										  WHERE `status`='A' AND category='".$category."'
									   ORDER BY `id` ASC";

	    $resultSubcat = $mycms->sql_select($sqlSubcat);
	    $maxRowsSubCat                = $mycms->sql_numrows($sqlSubcat);

	    //echo '<pre>'; print_r($resultSubcat);

	    
	?>
		
			<table width="100%" class="tborder">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Submission List</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<form name="frmSearch" id="frmSearch" action="all_abstract.free_paper.php" method="post">
						<input type="hidden" name="goto" value="<?=$searchArray['goto']?>" />
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									<td align="left" width="150">Submission Code:</td>
									<td align="left" width="250">
										<input type="text" name="src_abstract_submission_code" id="src_abstract_submission_code" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_abstract_submission_code']?>" />
									</td>
									<td align="left">Topic:</td>
									<td align="left">
										<select name="src_abstract_topic_id" id="src_abstract_topic_id" style="text-transform:uppercase; width:70%;">
											<option value="">-- Select Topic --</option>
											<?php
											$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_." 
																			WHERE `status` = 'A' 
																		 ORDER BY `abstract_topic` ASC";
											
											$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
											if($resultAbstractTopic)
											{
												foreach($resultAbstractTopic as $keyAbstractTopic=>$rowAbstractTopic)
												{
											?>
													<option value="<?=$rowAbstractTopic['id']?>" <?=($rowAbstractTopic['id']==$_REQUEST['src_abstract_topic_id'])?'selected="selected"':''?>><?=$rowAbstractTopic['abstract_topic']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
									<?php
									
									if($maxRowsSubCat>0 || $_REQUEST['category']>0)
									{
									?>
										<td align="left">Sub Category:</td>
										<td align="left">
											<select name="src_abstract_sub_cat_id" id="src_abstract_sub_cat_id" style="text-transform:uppercase; width:70%;">
												<option value="" selected="">-- Select Sub Category --</option>
												<?php
												
												if($resultSubcat)
												{
													foreach($resultSubcat as $keyAbstractTopic=>$value)
													{
												?>
														<option value="<?=$category?>-<?=$value['id']?>" <?=($category.'-'.$value['id']==$_REQUEST['src_abstract_sub_cat_id'])?'selected="selected"':''?>><?=$value['abstract_submission']?></option>
												<?php
													}
												}
												?>
											</select>
										</td>
									<?php
									}
									?>
									<input type="hidden" name="category" id="category" value="<?=$_REQUEST['cat']?>">
									<td align="right" rowspan="2">
										<?php 
										if($searchStatus)
										{
										?>
											<input type="button" name="clearBttn" id="clearBttn" class="btn btn-small btn-red" 
											 value="Clear" onclick="window.location.href='<?='all_abstract.free_paper.php?show='.$searchArray['goto']?>'" />
										<?php
										}
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
						</div>
						</form>
						<table width="100%">
							<tr class="theader">
								<td width="50" align="center">Sl No</td>
								<td width="100" align="center">Subm. Code</td>
								<td align="left">Abstract Title</td>
								<td width="100" align="center">Status</td>
								<td width="85" align="center">Action</td>
							</tr>
							<?php
							$counter                       = 0;
							$searchCondition               = "";
							if($_REQUEST['src_registration_id']!="")
							{
								$searchCondition          .= " AND registeredDelegates.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%'";
							}
							if($_REQUEST['src_full_name']!="")
							{
								$searchCondition          .= " AND registeredDelegates.user_full_name LIKE '%".$_REQUEST['src_full_name']."%'";
							}
							if($_REQUEST['src_applicant_unique_sequence']!="")
							{
								$searchCondition          .= " AND registeredDelegates.user_unique_sequence LIKE '%".$_REQUEST['src_applicant_unique_sequence']."%'";
							}
							if($_REQUEST['src_abstract_submission_code']!="")
							{
								$searchCondition          .= " AND abstractRequest.abstract_submition_code LIKE '%".$_REQUEST['src_abstract_submission_code']."%'";
							}
							if($_REQUEST['src_applicant_email_id']!="")
							{
								$searchCondition          .= " AND registeredDelegates.user_email_id LIKE '%".$_REQUEST['src_applicant_email_id']."%'";
							}
							if($_REQUEST['src_apply_from_date']!="" && $_REQUEST['src_apply_to_date']=="")
							{
								$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '".$_REQUEST['src_apply_from_date']."'  AND '3000-01-01'";
							}
							if($_REQUEST['src_apply_from_date']=="" && $_REQUEST['src_apply_to_date']!="")
							{
								$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '2000-01-01'  AND '".$_REQUEST['src_apply_to_date']."'";
							}
							if($_REQUEST['src_apply_from_date']!="" && $_REQUEST['src_apply_to_date']!="")
							{
								$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '".$_REQUEST['src_apply_from_date']."'  AND '".$_REQUEST['src_apply_to_date']."'";
							}
							if($_REQUEST['src_paper_presentation_category']!="")
							{
								$searchCondition          .= " AND abstractRequest.abstract_child_type = '".$_REQUEST['src_paper_presentation_category']."'";
							}
							if($_REQUEST['src_abstract_topic_id']!="")
							{
								$searchCondition          .= " AND abstractRequest.abstract_topic_id = '".$_REQUEST['src_abstract_topic_id']."'";
							}
							if($_REQUEST['src_presentation_type']!="")
							{
								$searchCondition          .= " AND abstractRequest.abstract_child_type = '".$_REQUEST['src_presentation_type']."'";
							}
							if($_REQUEST['src_abstract_award_id']!="")
							{
								$searchCondition          .= " AND abstractRequest.id IN ( SELECT submission_id FROM "._DB_AWARD_REQUEST_." WHERE award_id = '".$_REQUEST['src_abstract_award_id']."' AND status = 'A' )";
							}
							
							if($_REQUEST['goto']=='abstarct')
							{					
								$searchCondition .= " AND abstractRequest.abstract_parent_type = 'ABSTRACT'";
							}
							elseif($_REQUEST['goto']=='caseReport')
							{				
								$searchCondition .= " AND abstractRequest.abstract_parent_type = 'CASEREPORT'";
							}
							elseif($_REQUEST['goto']=='oral')
							{				
								$searchCondition .= " AND abstractRequest.abstract_child_type = 'ORAL'";
							}
							elseif($_REQUEST['goto']=='poster')
							{				
								$searchCondition .= " AND abstractRequest.abstract_child_type = 'POSTER'";
							}
							elseif($_REQUEST['goto']=='video')
							{				
								$searchCondition .= " AND abstractRequest.abstract_child_type = 'VIDEO'";
							}
							elseif(!empty($category) && $category>0)
							{
								$searchCondition .= " AND abstractRequest.abstract_cat = '".$category."'";
							}

							elseif(!empty($absSubCat) && $absSubCat>0)
							{
								$searchCondition .= " AND abstractRequest.abstract_cat = '".$category."' AND abstractRequest.abstract_parent_type = '".$absSubCat."'";
							}
							
							 $searchCondition  .= " AND abstractRequest.id IN (SELECT abstract_id FROM "._DB_ABSTRACT_ALLOTMENT_." WHERE review_user_id = '".$loggedUserID."')";


							
							$sqlAbstractDetails			   = abstractDetailsQuerySet("",$searchCondition);
							
							$resultAbstractDetails	       = $mycms->sql_select($sqlAbstractDetails);
							if($resultAbstractDetails)
							{
								foreach($resultAbstractDetails as $i=>$rowAbstractDetails) 
								{
									$counter++;
									
								

									$ReviewMarks['QUERY'] 		= "SELECT marks_obtained AS totalMarksObtained 
																	 FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
																	WHERE abstract_id =".$rowAbstractDetails['id']." 
																	  AND status = 'A'
																	  AND `faculty_id` = '".$loggedUserID."'"; 
													 
									$resultReviewMarks  = $mycms->sql_select($ReviewMarks);
									$rowReviewMarks     = $resultReviewMarks[0];
									
									$abstractDetails = getAbstractDetailsArray($rowAbstractDetails['id']);
									
									
									
									if($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE']=='UNABLE')
									{
										$rowStyleDecission = " style='background-color: #FF28FF;'";
									}
									elseif($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE']=='ABLE')
									{
										$rowStyleDecission = " style='background-color: #DDF1D6;'";
									}
									else
									{
										$rowStyleDecission = " style='background-color: #FFFFFF;'";
									}
									
							?>
									<tr class="tlisting" <?=$rowStyleDecission?>>
										<td align="center" valign="top"><?=$counter?></td>
										<td align="center" valign="top"><?=$abstractDetails['SUBMISSION_CODE']?></td>
										
										<td align="left" valign="top"><?=$rowAbstractDetails['abstract_title']?></td>
										<td align="center" valign="top">
										<?php
										if(trim($rowReviewMarks['totalMarksObtained']) != '' )
										{
											echo "<b>".$rowReviewMarks['totalMarksObtained'].'</b>';
										}
										else
										{
										?>
											<span class="ticket ticket-important">Not Reviewed</span>
										<?php
										}
										?>
										</td>
										<td align="center" valign="top">
											
										<?php
										if(true)
										{
											if(trim($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE']) == 'NOTREVIEW')
											{
										?>
											<a href="all_abstract.free_paper.php?show=view&mode=review&id=<?=$rowAbstractDetails['id']?>&tags=<?=$rowAbstractDetails['tags'].$searchString?>">Review</a>
										<?php
											}
											elseif(trim($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['TOTAL']) != '' )
											{
										?>
											<a href="all_abstract.free_paper.php?show=view&mode=review&id=<?=$rowAbstractDetails['id']?>&tags=<?=$rowAbstractDetails['tags'].$searchString?>">Update Review</a>
										<?php
											}
										}
										else
										{
										?>
											<a href="all_abstract.free_paper.php?show=view&mode=viewOnly&id=<?=$rowAbstractDetails['id']?>&tags=<?=$rowAbstractDetails['tags'].$searchString?>">View</a>
										<?php
										}
										?>
										</td>
									</tr>
							<?php
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="6" align="center">
										<span class="mandatory">No Record(s) Found</span>
									</td>
								</tr>
							<?php
							}
							?>
						</table>
							
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>	
			</table>
			</script>
		
	<?php
	}*/

function abstractListingDisplayWindow($cfg, $mycms)
{
	global $searchString, $searchArray, $searchStatus;
	$loggedUserID = $mycms->getLoggedUserId();

	$absCat = '';
	$absSubCat = '';
	if (!empty($_REQUEST['src_abstract_sub_cat_id'])) {
		$explodeSub = explode('-', $_REQUEST['src_abstract_sub_cat_id']);
		//print_r($explodeSub);
		$absCat = $explodeSub[0];
		$absSubCat = $explodeSub[1];
	}

	if (!empty($absCat) && $absCat > 0) {
		$category = $absCat;
	} else {
		$category = $_REQUEST['cat'];
	}

	//echo '<pre>'; print_r($_REQUEST);

	//echo 'cat='.$category;

	$sqlSubcat	 =	array();
	$sqlSubcat['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
										  WHERE `status`='A' AND category='" . $category . "'
									   ORDER BY `id` ASC";

	$resultSubcat = $mycms->sql_select($sqlSubcat);
	$maxRowsSubCat                = $mycms->sql_numrows($sqlSubcat);

	//echo '<pre>'; print_r($resultSubcat);


	$counter                       = 0;
	$searchCondition               = "";
	if ($_REQUEST['src_registration_id'] != "") {
		$searchCondition          .= " AND registeredDelegates.user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "%'";
	}
	if ($_REQUEST['src_full_name'] != "") {
		$searchCondition          .= " AND registeredDelegates.user_full_name LIKE '%" . $_REQUEST['src_full_name'] . "%'";
	}
	if ($_REQUEST['src_applicant_unique_sequence'] != "") {
		$searchCondition          .= " AND registeredDelegates.user_unique_sequence LIKE '%" . $_REQUEST['src_applicant_unique_sequence'] . "%'";
	}
	if ($_REQUEST['src_abstract_submission_code'] != "") {
		$searchCondition          .= " AND abstractRequest.abstract_submition_code LIKE '%" . $_REQUEST['src_abstract_submission_code'] . "%'";
	}
	if ($_REQUEST['src_applicant_email_id'] != "") {
		$searchCondition          .= " AND registeredDelegates.user_email_id LIKE '%" . $_REQUEST['src_applicant_email_id'] . "%'";
	}
	if ($_REQUEST['src_apply_from_date'] != "" && $_REQUEST['src_apply_to_date'] == "") {
		$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '" . $_REQUEST['src_apply_from_date'] . "'  AND '3000-01-01'";
	}
	if ($_REQUEST['src_apply_from_date'] == "" && $_REQUEST['src_apply_to_date'] != "") {
		$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '2000-01-01'  AND '" . $_REQUEST['src_apply_to_date'] . "'";
	}
	if ($_REQUEST['src_apply_from_date'] != "" && $_REQUEST['src_apply_to_date'] != "") {
		$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '" . $_REQUEST['src_apply_from_date'] . "'  AND '" . $_REQUEST['src_apply_to_date'] . "'";
	}
	if ($_REQUEST['src_paper_presentation_category'] != "") {
		$searchCondition          .= " AND abstractRequest.abstract_child_type = '" . $_REQUEST['src_paper_presentation_category'] . "'";
	}
	if ($_REQUEST['src_abstract_topic_id'] != "") {
		$searchCondition          .= " AND abstractRequest.abstract_topic_id = '" . $_REQUEST['src_abstract_topic_id'] . "'";
	}
	if ($_REQUEST['src_presentation_type'] != "") {
		$searchCondition          .= " AND abstractRequest.abstract_child_type = '" . $_REQUEST['src_presentation_type'] . "'";
	}
	if ($_REQUEST['src_abstract_award_id'] != "") {
		$searchCondition          .= " AND abstractRequest.id IN ( SELECT submission_id FROM " . _DB_AWARD_REQUEST_ . " WHERE award_id = '" . $_REQUEST['src_abstract_award_id'] . "' AND status = 'A' )";
	}

	if ($_REQUEST['goto'] == 'abstarct') {
		$searchCondition .= " AND abstractRequest.abstract_parent_type = 'ABSTRACT'";
	}
	if ($_REQUEST['goto'] == 'caseReport') {
		$searchCondition .= " AND abstractRequest.abstract_parent_type = 'CASEREPORT'";
	}
	if ($_REQUEST['goto'] == 'oral') {
		$searchCondition .= " AND abstractRequest.abstract_child_type = 'ORAL'";
	}
	if ($_REQUEST['goto'] == 'poster') {
		$searchCondition .= " AND abstractRequest.abstract_child_type = 'POSTER'";
	}
	if ($_REQUEST['goto'] == 'video') {
		$searchCondition .= " AND abstractRequest.abstract_child_type = 'VIDEO'";
	}
	if (!empty($category) && $category > 0) {
		$searchCondition .= " AND abstractRequest.abstract_cat = '" . $category . "'";
	}

	if (!empty($absSubCat) && $absSubCat > 0) {
		//echo 2222;
		$searchCondition .= " AND abstractRequest.abstract_cat = '" . $category . "' AND abstractRequest.abstract_parent_type = '" . $absSubCat . "'";
	}

	$searchCondition  .= " AND abstractRequest.id IN (SELECT abstract_id FROM " . _DB_ABSTRACT_ALLOTMENT_ . " WHERE review_user_id = '" . $loggedUserID . "')";


?>

	<table width="100%" class="tborder">
		<tr>
			<td class="tcat" colspan="2" align="left">
				<span style="float:left">Submission List</span>
				<span class="tsearchTool" forType="tsearchTool"></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="margin:0px; padding:0px;">
				<form name="frmSearch" id="frmSearch" action="all_abstract.free_paper.php" method="post">
					<input type="hidden" name="goto" value="<?= $searchArray['goto'] ?>" />
					<div class="tsearch" style="display:block;">
						<table width="100%">
							<tr>
								<td align="left" width="150">Submission Code:</td>
								<td align="left" width="250">
									<input type="text" name="src_abstract_submission_code" id="src_abstract_submission_code"
										style="width:90%; text-transform:uppercase;" value="<?= $_REQUEST['src_abstract_submission_code'] ?>" />
								</td>
								<td align="left">Topic:</td>
								<td align="left">
									<select name="src_abstract_topic_id" id="src_abstract_topic_id" style="text-transform:uppercase; width:70%;">
										<option value="">-- Select Topic --</option>
										<?php
										$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
																			WHERE `status` = 'A' 
																		 ORDER BY `abstract_topic` ASC";

										$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
										if ($resultAbstractTopic) {
											foreach ($resultAbstractTopic as $keyAbstractTopic => $rowAbstractTopic) {
										?>
												<option value="<?= $rowAbstractTopic['id'] ?>" <?= ($rowAbstractTopic['id'] == $_REQUEST['src_abstract_topic_id']) ? 'selected="selected"' : '' ?>><?= $rowAbstractTopic['abstract_topic'] ?></option>
										<?php
											}
										}
										?>
									</select>
								</td>
								<?php

								if ($maxRowsSubCat > 0 || $_REQUEST['category'] > 0) {
								?>
									<td align="left">Sub Category:</td>
									<td align="left">
										<select name="src_abstract_sub_cat_id" id="src_abstract_sub_cat_id" style="text-transform:uppercase; width:70%;">
											<option value="" selected="">-- Select Sub Category --</option>
											<?php

											if ($resultSubcat) {
												foreach ($resultSubcat as $keyAbstractTopic => $value) {
											?>
													<option value="<?= $category ?>-<?= $value['id'] ?>" <?= ($category . '-' . $value['id'] == $_REQUEST['src_abstract_sub_cat_id']) ? 'selected="selected"' : '' ?>><?= $value['abstract_submission'] ?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								<?php
								}
								?>
								<input type="hidden" name="category" id="category" value="<?= $_REQUEST['cat'] ?>">
								<td align="right" rowspan="2">
									<?php
									if ($searchStatus) {
									?>
										<input type="button" name="clearBttn" id="clearBttn" class="btn btn-small btn-red"
											value="Clear" onclick="window.location.href='<?= 'all_abstract.free_paper.php?show=' . $searchArray['goto'] ?>'" />
									<?php
									}
									?>
									<input type="submit" name="goSearch" value="Search"
										class="btn btn-small btn-blue" />
								</td>
							</tr>
						</table>
					</div>
				</form>
				<table width="100%">
					<tr class="theader">
						<td width="50" align="center">Sl No</td>
						<td width="100" align="center">Subm. Code</td>
						<!-- <td width="150" align="center">Abstract Topic</td> -->
						<td align="left">Abstract Title</td>
						<td width="100" align="center">Status</td>
						<td width="85" align="center">Action</td>
					</tr>
					<?php




					//echo 'cat='.$category;

					//echo 'sub='.$absSubCat;
					//echo $searchCondition;

					$sqlAbstractDetails			   = abstractDetailsQuerySet("", $searchCondition);
					//echo '<pre>';print_r($sqlAbstractDetails);echo '</pre>';
					$resultAbstractDetails	       = $mycms->sql_select($sqlAbstractDetails);
					if ($resultAbstractDetails) {
						foreach ($resultAbstractDetails as $i => $rowAbstractDetails) {
							$counter++;

							/*
									$ReviewCount['QUERY'] 		= " SELECT COUNT(*) AS totalReviewAttemptCount 
																	  FROM "._DB_ABSTRACT_REVIEW_RESULT_."
																	 WHERE abstract_id =".$rowAbstractDetails['id']."
																	   AND `faculty_id` = '".$loggedUserID."' 
																	   AND status = 'A'"; 
									$resultReviewCount  = $mycms->sql_select($ReviewCount);
									$rowReviewCount		= $resultReviewCount[0];
											
									$ReviewMarks['QUERY'] 		= "SELECT marks_obtained AS totalMarksObtained 
																	 FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
																	WHERE abstract_id =".$rowAbstractDetails['id']." 
																	  AND status = 'A'
																	  AND `faculty_id` = '".$loggedUserID."'"; 
													 
									$resultReviewMarks  = $mycms->sql_select($ReviewMarks);
									$rowReviewMarks     = $resultReviewMarks[0];
															
									$rowStyleDecission     = "";
									if($rowReviewCount['totalReviewAttemptCount']==0)
									{
										$rowStyleDecission = " style='background-color: #FFFFFF;'";
									}
									else
									{
										$rowStyleDecission = " style='background-color: #DDF1D6;'";
									}
									*/

							$ReviewMarks['QUERY'] 		= "SELECT marks_obtained AS totalMarksObtained 
																	 FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
																	WHERE abstract_id =" . $rowAbstractDetails['id'] . " 
																	  AND status = 'A'
																	  AND `faculty_id` = '" . $loggedUserID . "'";

							$resultReviewMarks  = $mycms->sql_select($ReviewMarks);
							$rowReviewMarks     = $resultReviewMarks[0];

							$abstractDetails = getAbstractDetailsArray($rowAbstractDetails['id']);

							//echo '<pre>'; print_r($abstractDetails);

							if ($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE'] == 'UNABLE') {
								$rowStyleDecission = " style='background-color: #FF28FF;'";
							} elseif ($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE'] == 'ABLE') {
								$rowStyleDecission = " style='background-color: #DDF1D6;'";
							} else {
								$rowStyleDecission = " style='background-color: #FFFFFF;'";
							}

					?>
							<tr class="tlisting" <?= $rowStyleDecission ?>>
								<td align="center" valign="top"><?= $counter ?></td>
								<td align="center" valign="top"><?= $abstractDetails['SUBMISSION_CODE'] ?></td>
								<!-- <td align="center" valign="top"><?= $abstractDetails['TOPIC'] ?></td> -->
								<td align="left" valign="top"><?= $rowAbstractDetails['abstract_title'] ?></td>
								<td align="center" valign="top">
									<?php
									if (trim($rowReviewMarks['totalMarksObtained']) != '') {
										echo "<b>" . $rowReviewMarks['totalMarksObtained'] . '</b>';
									} else {
									?>
										<span class="ticket ticket-important">Not Reviewed</span>
									<?php
									}
									?>
								</td>
								<td align="center" valign="top">

									<?php
									if (true) {
										if (trim($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE']) == 'NOTREVIEW') {
									?>
											<a href="all_abstract.free_paper.php?show=view&mode=review&id=<?= $rowAbstractDetails['id'] ?>&tags=<?= $rowAbstractDetails['tags'] . $searchString ?>">Review</a>
										<?php
										} elseif (trim($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['TOTAL']) != '') {
										?>
											<a href="all_abstract.free_paper.php?show=view&mode=review&id=<?= $rowAbstractDetails['id'] ?>&tags=<?= $rowAbstractDetails['tags'] . $searchString ?>">Update Review</a>
										<?php
										}
									} else {
										?>
										<a href="all_abstract.free_paper.php?show=view&mode=viewOnly&id=<?= $rowAbstractDetails['id'] ?>&tags=<?= $rowAbstractDetails['tags'] . $searchString ?>">View</a>
									<?php
									}
									?>
								</td>
							</tr>
						<?php
						}
					} else {
						?>
						<tr>
							<td colspan="6" align="center">
								<span class="mandatory">No Record(s) Found</span>
							</td>
						</tr>
					<?php
					}
					?>
				</table>

			</td>
		</tr>
		<tr class="tfooter">

			<td colspan="2">
				<span style="color: red;"> For any query contact : +91 81005 69558 (11:00 - 20:00)</span>
				<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
				<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
			</td>
		</tr>
	</table>
	</script>

<?php
}

/****************************************************************************/
/*                       ABSTRACT VIEW DETAILS WINDOW                       */
/****************************************************************************/
/*function abstractReviewDetailsWindow($cfg, $mycms)
	{
		global $searchString, $searchArray;
		
		 $abstractId 				   = addslashes(trim($_REQUEST['id']));
		
		$sqlAbstractDetails['QUERY']            = "  SELECT abstractRequest.*,
												   abstractTopic.abstract_topic,
												   
												   registeredDelegates.user_email_id,
												   registeredDelegates.user_mobile_no,
												   registeredDelegates.user_unique_sequence,
												   registeredDelegates.user_registration_id,
												   
												   IFNULL(registeredDelegates.user_title, '') AS user_title,
												   IFNULL(registeredDelegates.user_first_name, '') AS user_first_name,
												   IFNULL(registeredDelegates.user_middle_name, '') AS user_middle_name,
												   IFNULL(registeredDelegates.user_last_name, '') AS user_last_name,
												
												   registeredDelegates.isRegistration,
												   registeredDelegates.isWorkshop,
												  
												   
												   registeredDelegates.registration_payment_status,
												   registeredDelegates.workshop_payment_status,
												   
												   country.country_name AS author_country_name,
												   state.state_name AS author_state_name,
												   abstractCat.category AS category_name,
												   abstractSub.abstract_submission AS submission_name,

												   abstractPresent.abstract_presentation AS presentaion_name,
												   
												   IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
												   IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
												   IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName,
												   
												   IFNULL(abstractReview.totalMarksObtained, 0) AS totalMarksObtained,
												   IFNULL(abstractReview.totalReviewCount, 0) AS totalReviewCount
								
											  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
											  
								   		LEFT JOIN "._DB_ABSTRACT_TOPIC_." abstractTopic 
												ON abstractRequest.abstract_topic_id = abstractTopic.id 

										LEFT JOIN "._DB_ABSTRACT_TOPIC_CATEGORY_." abstractCat 
												ON abstractRequest.abstract_cat = abstractCat.id 

										LEFT JOIN "._DB_ABSTRACT_SUBMISSION_." abstractSub 
												ON abstractRequest.abstract_parent_type = abstractSub.id 

										LEFT JOIN "._DB_ABSTRACT_PRESENTATION_." abstractPresent 
												ON abstractRequest.	abstract_child_type = abstractPresent.id					
								   
								   		INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
												ON abstractRequest.applicant_id = registeredDelegates.id 
											   AND registeredDelegates.status = 'A'
								   
								   LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
												ON abstractRequest.abstract_author_country_id = country.country_id
											 
								   LEFT OUTER JOIN "._DB_COMN_STATE_." state
												ON abstractRequest.abstract_author_state_id = state.st_id
																
								   LEFT OUTER JOIN (
														SELECT SUM(abstractReview.marks_obtained) AS totalMarksObtained,
															   COUNT(abstractReview.id) AS totalReviewCount,
															   abstractReview.abstract_id 
														   
														  FROM "._DB_ABSTRACT_REVIEW_RESULT_." abstractReview 
														  
														 WHERE abstractReview.status = 'A' 
														 
													  GROUP BY abstractReview.abstract_id 
												   ) abstractReview 
												ON abstractReview.abstract_id = abstractRequest.id 
												
											 WHERE abstractRequest.id=".$abstractId ."";
												
		$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
		$rowAbstractDetails            = $resultAbstractDetails[0];
		
		$abstractDetails = getAbstractDetailsArray($abstractId);

		//echo '<pre>'; print_r($rowAbstractDetails);
	?>
		<style>
			.tborder td, .tborder th {
				font-size: 14px;
			}
		</style>
		<form name="frmAbstractReview" id="frmAbstractReview" action="all_abstract.free_paper.process.php" method="post">
			<input type="hidden" name="act" value="review" />
			<input type="hidden" name="goto" value="<?=$_REQUEST['src_show']?>" />
			<input type="hidden" name="abstract_id" value="<?=$rowAbstractDetails['id']?>" />
			<?php
			foreach($searchArray as $keyString=>$valString)
			{
				if($valString!="")
				{
			?>
					<input type="hidden" name="<?=$keyString?>" id="<?=$keyString?>" value="<?=$valString?>" />
			<?php
				}
			}
			 
			

			$ReviewCount['QUERY'] = " SELECT COUNT(*) AS totalReviewAttemptCount 
							   FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
							  WHERE abstract_id =".$rowAbstractDetails['id'].""; 
			$resultReviewCount         = $mycms->sql_select($ReviewCount);
			$rowReviewCount            = $resultReviewCount[0];
			//print_r($rowReviewCount['totalReviewAttemptCount']);
			if(!empty($rowAbstractDetails['abstract_cat']) && $rowAbstractDetails['abstract_cat']>0)
			{
				$abstractProofContent = getAbstractProofContent($abstractId, '../../');
				$abstractOriginalContent = getAbstractContent($abstractId, '../../');				
			?>
			<table width="100%" class="tborder">
				<tr>
					<td colspan="2" align="left" class="tcat">Review Panel</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<a href="all_abstract.free_paper.php?show=abstarct<?=$searchString?>">Back To Listing</a>
					</td>
				</tr>
				<tr>
					<td width="65%" style="margin:0px; padding:0px;" class="tborder" valign="top">						
						<table width="100%">
							<tr class="thighlight">
								<td align="left">Basic Details</td>
								<td align="right">
									<!-- <a href="msdoc.abstract.download.php?id=<?=$rowAbstractDetails['id']?>&operation=pdfdownload" target="_blank">
									<img src="images/pdf.png" height="32" style="cursor:pointer;" title="Download In .PDF Format" />
									</a> -->
									<!--<a href="msdoc.abstract.download.php?id=<?=$rowAbstractDetails['id']?>&operation=print" target="_blank">
									<img src="images/print.png" height="32" style="cursor:pointer;" title="Print Abstract" />-->
									</a>
								</td>
							</tr>
							<tr>
								<td width="30%" align="left">Submission Code</td>
								<td align="left"><?=strtoupper($rowAbstractDetails['abstract_submition_code'])?></td>
							</tr>
							<tr>
								<td align="left">Submission Date</td>
								<td align="left"><?=setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D")?></td>
							</tr>
							<?php
							if(!empty($rowAbstractDetails['category_name']))
							{
							?>
								<tr>
									<td align="left">Category</td>
									<td align="left"><?=$rowAbstractDetails['category_name']?></td>
								</tr>
							<?php
							}
							if(!empty($rowAbstractDetails['submission_name']))
							{
							?>
							<tr>
								<td align="left">Sub Category</td>
								<td align="left"><?=$rowAbstractDetails['submission_name']?></td>
							</tr>
							<?php
							}
							?>
						</table>
						<?
							$totalWordCount   = 0;

							//echo '<pre>'; print_r($rowAbstractDetails);
						?>
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Details</td>
							</tr>
							<?php
							if(!empty($rowAbstractDetails['abstract_topic']))
							{
							?>
							<tr>
								<td width="30%" align="left">Topic</td>
								<td align="left"><?=$rowAbstractDetails['abstract_topic']?></td>
							</tr>
							<?php
							}
							?>
							<tr>
								<td align="left" valign="top">Title</td>
								<td align="left" valign="top"><?php echo (!empty($rowAbstractDetails['proof_abstract_title']))? $rowAbstractDetails['proof_abstract_title']:$rowAbstractDetails['abstract_title'] ?></td>
							</tr>
							<tr class="tlisting">
								<td align="left" valign="top" style="color:#0066FF;">Title Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?=str_word_count($rowAbstractDetails['abstract_title'])?></b>
								</td>
							</tr>
							<?
							if($rowAbstractDetails['abstract_child_type'] == 'VIDEO')
							{
							?>	
							<tr>
								<td align="left" valign="top">Description</td>
								<td align="left" valign="top">
									<?php
									
									$totalWordCount  += str_word_count($abstractOriginalContent['DESCRIPTION']);
									echo nl2br($abstractProofContent['DESCRIPTION']);
									?>
								</td>
							</tr>
							<tr use='contentTr'>
								<td align="left" valign="top" width="20%"><strong>Video File</strong></td>
							</tr>
							<tr use='contentTr'>
								<td colspan="3" align="left" valign="top">
									<a href="<?=_BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].$rowAbstractDetails['abstract_video_file']?>" target="_blank" title="<?="Download ".$rowAbstractDetails['abstract_original_file_name']?>">
									<img src="images/invDnld.png"  style="width:20px;" /></a>
									&nbsp;<?=$rowAbstractDetails['abstract_original_file_name']?>
								</td>
							</tr>
							<?
							}
							else
							{
								//echo '<pre>'; print_r($rowAbstractDetails);



								$abs_background = (!empty($rowAbstractDetails['proof_abstract_background'] && $rowAbstractDetails['proof_abstract_background']!='NULL')?$rowAbstractDetails['proof_abstract_background']:$rowAbstractDetails['abstract_background']);

								if(!empty($abs_background))
								{
							?>
							    <tr>
								<td align="left" valign="top"><?php if(trim($rowAbstractDetails['category_name'])=='Case Report'){ echo 'Introduction'; }else{ echo 'Introduction'; } ?></td>
								<td align="left" valign="top">
									<?php
									
									$totalWordCount  += str_word_count($abs_background);
									echo nl2br($abs_background);
									?>
								</td>
							</tr>
							<?php
							}

							$abs_background_aims = (!empty($rowAbstractDetails['proof_abstract_background_aims'] && $rowAbstractDetails['proof_abstract_background_aims']!='NULL')?$rowAbstractDetails['proof_abstract_background_aims']:$rowAbstractDetails['abstract_background_aims']);

								if(!empty($abs_background_aims))
								{
							?>
							    <tr>
								<td align="left" valign="top"><?php if(trim($rowAbstractDetails['category_name'])=='Case Report'){ echo 'Introduction'; }else{ echo 'Aims & Objective'; } ?></td>
								<td align="left" valign="top">
									<?php
									
									$totalWordCount  += str_word_count($abs_background_aims);
									echo nl2br($abs_background_aims);
									?>
								</td>
							</tr>
							<?php
							}


							$abs_desc = (!empty($rowAbstractDetails['proof_abstract_description'] && $rowAbstractDetails['proof_abstract_description']!='NULL')?$rowAbstractDetails['proof_abstract_description']:$rowAbstractDetails['abstract_description']);

							  if(!empty($abs_desc))
							  {

							?>
								<tr>
									<td align="left" valign="top"><?php if(trim($rowAbstractDetails['category_name'])=='Case Report'){ echo 'Case Description'; }else{ echo 'Methods and Material'; } ?>  </td>
									<td align="left" valign="top">
										<?php
										
										$totalWordCount  += str_word_count($abs_desc);									
										echo nl2br($abs_desc);
										?>
									</td>
								</tr>
							<?php
							}

							  $abs_material = (!empty($rowAbstractDetails['proof_abstract_material_methods'] && $rowAbstractDetails['proof_abstract_material_methods']!='NULL')?$rowAbstractDetails['proof_abstract_material_methods']:$rowAbstractDetails['abstract_material_methods']);

							  if(!empty($abs_material))
							  {

							?>
								<tr>
									<td align="left" valign="top"> Methods and Material </td>
									<td align="left" valign="top">
										<?php
										
										$totalWordCount  += str_word_count($abs_material);									
										echo nl2br($abs_material);
										?>
									</td>
								</tr>
							<?php
							}

							$abs_result = (!empty($rowAbstractDetails['proof_abstract_results'] && $rowAbstractDetails['proof_abstract_results']!='NULL')?$rowAbstractDetails['proof_abstract_results']:$rowAbstractDetails['abstract_results']);

							if(!empty($abs_result))
							{
							?>
							<tr>
								<td align="left" valign="top">Results</td>
								<td align="left" valign="top">
									<?php

									
									$totalWordCount  += str_word_count($abs_result);									
									echo nl2br($abs_result);
									?>
								</td>
							</tr>
							<?php
							}

							$abs_conclusion = (!empty($rowAbstractDetails['proof_abstract_conclution'] && $rowAbstractDetails['proof_abstract_conclution']!='NULL')?$rowAbstractDetails['proof_abstract_conclution']:$rowAbstractDetails['abstract_conclution']);
							if(!empty($abs_conclusion))
							{
							?>
							<tr>
								<td align="left" valign="top">Conclusion</td>
								<td align="left" valign="top">
									<?php
									$abs_conclusion = (!empty($rowAbstractDetails['proof_abstract_conclution'] && $rowAbstractDetails['proof_abstract_conclution']!='NULL')?$rowAbstractDetails['proof_abstract_conclution']:$rowAbstractDetails['abstract_conclution']);
									$totalWordCount  += str_word_count($abs_conclusion);
									echo nl2br($abs_conclusion);
									?>
								</td>
							</tr>
							<?php
							}

							$abs_references = (!empty($rowAbstractDetails['proof_abstract_references'] && $rowAbstractDetails['proof_abstract_references']!='NULL')?$rowAbstractDetails['proof_abstract_references']:$rowAbstractDetails['abstract_references']);
							if(!empty($abs_references))
							{
							?>
							<tr>
								<td align="left" valign="top"><?php if(trim($rowAbstractDetails['category_name'])=='Case Report'){ echo 'Discussion'; }else{ echo 'References'; } ?></td>
								<td align="left" valign="top">
									<?php
									
									$totalWordCount  += str_word_count($abs_references);
									echo nl2br($abs_references);
									?>
								</td>
							</tr>
							<?php
							}
							?>
							<?php
								if(!empty($rowAbstractDetails['abstract_file']))
		 						{	
								?>
								<tr >
									<td align="left"><strong>Signed PDF</strong></td>
								</tr>
								
								<tr>
									<td align="left" >
										<a href="<?php echo _BASE_URL_.'uploads/FILES.ABSTRACT.REQUEST/'.$rowAbstractDetails['abstract_file'];?>" target="_blank">Download</a>
									</td>
								</tr>
								<?php
								}
								?>

								<?php
		 						if(!empty($rowAbstractDetails['abstract_image_file']))
		 						{	
								?>

								<tr>
									<td align="left"><strong>Single Image</strong></td>
								</tr>
								
								<tr >
									<td  align="left" >
										<a href="<?php echo _BASE_URL_.'uploads/FILES.ABSTRACT.REQUEST/'.$rowAbstractDetails['abstract_image_file'];?>" target="_blank">Download</a>
									</td>
								</tr>
								<?php
								}
						?>	
						<?php	
							}
							?>
							<tr>
								<td align="left" valign="top" style="color:#0066FF;">Total Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?=$totalWordCount?></b>
								</td>
							</tr>
						</table>
					</td>
					<td width="35%" style="margin:0px; padding:0px;" class="tborder" valign="top">	
						<table width="100%" use='marksContainer'>
							<tr>
								<td style="margin:0px; padding:0px;" >
									<input type="radio" name="faculty_decision" value="NOTSUBJECT"  style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required/> <b style="font-size:16px; color:#0000FF;">I am unable Review</b>
								</td>
							</tr>
							<tr>
								<td style="margin:0px; padding:0px;" >
									<input type="radio" checked="checked" name="faculty_decision" value="WILLREVIEW"  style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required/> <b style="font-size:16px; color:#CC0000;">Review</b>
								</td>
							</tr>
							<tr>
								<td>
								</td>
							</tr>
							<tr>
								<td use="updateMarks" style="margin:0px; padding:0px;" >
					<?


					if($_REQUEST['mode']=='review')
					{
					?>					
						<table width="100%">
							<tr>
								<td align="left" class="thighlight">Score Board</td>
							</tr>
						</table>						
						<?php
						//echo 'count='. $rowReviewCount['totalReviewAttemptCount'];
						if(!empty($rowReviewCount['totalReviewAttemptCount']) && $rowReviewCount['totalReviewAttemptCount']<200)
						{
							
							//abstractReviewUpdatePanel($rowAbstractDetails['id'], $rowAbstractDetails);
							abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
							
						}
						else
						{
							
							abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
						}
					}
					?>
								</td>
							</tr>
						</table>						
						<table width="100%">
							<tr>
								<td align="left">
									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?=($rowAbstractDetails ['totalReviewAttemptCount']>2)?'Cancel':'Back'?>" 
									 	   class="btn btn-medium btn-red" 
										   onclick="window.location.href='all_abstract.free_paper.php?show=abstarct<?=$searchString?>'" style=" margin:10px 10px 10px 0;" />
									
									<?php
									if($_REQUEST['mode']=='review' && $rowReviewCount['totalReviewAttemptCount']<200)
									{
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"  
										 	   value="<?=($rowReviewCount['totalReviewAttemptCount']==0)?'Save':'Update...'?>" 
											   style=" margin:10px 10px 10px 0;" />
									<?php
									}
									?>
									
								</td>
							</tr>
						</table>		
									
					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter">&nbsp; For any query contact - 7596071512 @Secretariat</td>
				</tr>
			</table>
			<?
			}
			elseif($rowAbstractDetails['abstract_parent_type']=='CASEREPORT')
			{
				
				$abstractProofContent = getAbstractProofContent($abstractId, '../../');				
				$abstractOriginalContent = getAbstractContent($abstractId, '../../');				
			?>
			<table width="100%" class="tborder">
				<tr>
					<td colspan="2" align="left" class="tcat">Case study Review Panel</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<a href="all_abstract.free_paper.php?<?=$searchString?>">Back To Listing</a>
					</td>
				</tr>
				<tr>
					<td width="65%" style="margin:0px; padding:0px;" class="tborder" valign="top">						
						<table width="100%">
							<tr class="thighlight">
								<td align="left" >Basic Details</td>
								<td align="right">
									<a href="msdoc.abstract.download.php?id=<?=$rowAbstractDetails['id']?>&operation=pdfdownload">
									<img src="images/pdf.png" height="32" style="cursor:pointer;" title="Download In .PDF Format" />
									</a>
									<!--<a href="msdoc.abstract.download.php?id=<?=$rowAbstractDetails['id']?>&operation=print" target="_blank">
									<img src="images/print.png" height="32" style="cursor:pointer;" title="Print Abstract" />
									</a>-->
								</td>
							</tr>
							<tr>
								<td width="30%" align="left">Case report Submission Code</td>
								<td align="left"><?=strtoupper($rowAbstractDetails['abstract_submition_code'])?></td>
							</tr>
							<tr>
								<td align="left">Submission Date</td>
								<td align="left"><?=setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D")?></td>
							</tr>
							<tr>
								<td align="left">Submission Category</td>
								<td align="left"><?=$rowAbstractDetails['abstract_child_type']?></td>
							</tr>
							<tr>
								<td align="left">Submission Type</td>
								<td align="left"><?=$rowAbstractDetails['abstract_parent_type']?></td>
							</tr>
						</table>
						<?
							$totalWordCount   = 0;
						?>
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Case Report Details</td>
							</tr>
							<tr>
								<td width="30%" align="left">Topic</td>
								<td align="left"><?=$rowAbstractDetails['abstract_topic']?></td>
							</tr>
							<tr>
								<td align="left" valign="top">Title</td>
								<td align="left" valign="top"><?=$abstractProofContent['TITLE']?></td>
							</tr>
							<tr class="tlisting">
								<td align="left" valign="top" style="color:#0066FF;">Title Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?=str_word_count($abstractOriginalContent['TITLE'])?></b>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Description</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($abstractOriginalContent['DESCRIPTION']);									
									echo nl2br($abstractProofContent['DESCRIPTION']);
									?>
								</td>
							</tr>							
							<tr>
								<td align="left" valign="top">Conclusion</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($abstractOriginalContent['CONCLUSION']);									
									echo nl2br($abstractProofContent['CONCLUSION']);
									?>
								</td>
							</tr>
							
							<tr>
								<td align="left" valign="top" style="color:#0066FF;">Total Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?=$totalWordCount?></b>
								</td>
							</tr>
						</table>
							
					</td>
					<td width="35%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						<table width="100%" use='marksContainer'>
							<tr>
								<td style="margin:0px; padding:0px;" >
									<input type="radio" name="faculty_decision" value="NOTSUBJECT"  style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required/> <b style="font-size:16px; color:#0000FF;">I am unable Review</b>
								</td>
							</tr>
							<tr>
								<td style="margin:0px; padding:0px;" >
									<input type="radio" checked="checked" name="faculty_decision" value="WILLREVIEW"  style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required/> <b style="font-size:16px; color:#CC0000;">Review</b>
								</td>
							</tr>
							<tr>
								<td>
								</td>
							</tr>
							<tr>
								<td use="updateMarks" style="margin:0px; padding:0px;" >
					<?
					if($_REQUEST['mode']=='review')
					{
					?>	
						<table width="100%">
							<tr>
								<td align="left" class="thighlight">Score Board</td>
							</tr>
						</table>
						
						<?php
						if($rowReviewCount['totalReviewAttemptCount']<2000)
						{
							casereportReviewUpdatePanel($rowAbstractDetails['id'], $rowAbstractDetails);
						}
						else
						{
							casereportReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
						}
					}
					?>
								</td>
							</tr>
						</table>
							
						<table width="100%">
							<tr>
								<td align="left">
									
									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?=($rowAbstractDetails ['totalReviewAttemptCount']>2)?'Cancel':'Back'?>" 
									 class="btn btn-medium btn-red" onclick="window.location.href='all_abstract.free_paper.php?<?=$searchString?>'" style=" margin:10px 10px 10px 0;" />
									
									<?php
									if($_REQUEST['mode']=='review' && $rowReviewCount['totalReviewAttemptCount']<2000)
									{
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"  
										 value="<?=($rowReviewCount['totalReviewAttemptCount']==0)?'Save':'Update'?>" style=" margin:10px 10px 10px 0;" />
									<?php
									}
									?>
									
								</td>
							</tr>
						</table>
							
					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter">&nbsp; For any query contact - 7596071512 @Secretariat</td>
				</tr>
			</table>
			<?
			}
			?>
		</form>
		<script>
			function setNotMySubject(obj)
			{
				console.log(obj);
			
				var parent = $(obj).parent().closest("table[use=marksContainer]");
				
				console.log(parent);
				
				console.log($(obj).is(":checked"));
				
				if($(obj).val()=='WILLREVIEW')
				{
					$(parent).find("td[use=updateMarks]").find("input[type=radio]").attr("required","true");
					//$(parent).find("td[use=updateMarks]").find("textarea").attr("required","true");
					$(parent).find("td[use=updateMarks]").show();					
				}
				else
				{
					$(parent).find("td[use=updateMarks]").hide();
					$(parent).find("td[use=updateMarks]").find("input[type=radio]").removeAttr("required");
					$(parent).find("td[use=updateMarks]").find("textarea").removeAttr("required");
				}
			}
			<?
			if($abstractDetails['MARKS']['REVIEW_STATE']=='UNABLE')
			{
			?>
				$(document).ready(function(){
					$("table[use=marksContainer]").find("input[type=radio][name=faculty_decision][value=NOTSUBJECT]").trigger("click");
				});
			<?
			}
			else
			{
			?>
				$(document).ready(function(){
					$("table[use=marksContainer]").find("input[type=radio][name=faculty_decision][value=WILLREVIEW]").trigger("click");
				});
			<?
			}
			?>
		</script>
	<?php
	}*/

function abstractReviewDetailsWindow($cfg, $mycms)
{
	global $searchString, $searchArray;

	$abstractId 				   = addslashes(trim($_REQUEST['id']));

	$sqlAbstractDetails['QUERY']            = "  SELECT abstractRequest.*,
												   abstractTopic.abstract_topic,
												   
												   registeredDelegates.user_email_id,
												   registeredDelegates.user_mobile_no,
												   registeredDelegates.user_unique_sequence,
												   registeredDelegates.user_registration_id,
												   
												   IFNULL(registeredDelegates.user_title, '') AS user_title,
												   IFNULL(registeredDelegates.user_first_name, '') AS user_first_name,
												   IFNULL(registeredDelegates.user_middle_name, '') AS user_middle_name,
												   IFNULL(registeredDelegates.user_last_name, '') AS user_last_name,
												
												   registeredDelegates.isRegistration,
												   registeredDelegates.isWorkshop,
												  
												   
												   registeredDelegates.registration_payment_status,
												   registeredDelegates.workshop_payment_status,
												   
												   country.country_name AS author_country_name,
												   state.state_name AS author_state_name,
												   abstractCat.category AS category_name,
												   abstractSub.abstract_submission AS submission_name,

												   abstractPresent.abstract_presentation AS presentaion_name,
												   
												   IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
												   IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
												   IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName,
												   
												   IFNULL(abstractReview.totalMarksObtained, 0) AS totalMarksObtained,
												   IFNULL(abstractReview.totalReviewCount, 0) AS totalReviewCount
								
											  FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest 
											  
								   		LEFT JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
												ON abstractRequest.abstract_topic_id = abstractTopic.id 

										LEFT JOIN " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " abstractCat 
												ON abstractRequest.abstract_cat = abstractCat.id 

										LEFT JOIN " . _DB_ABSTRACT_SUBMISSION_ . " abstractSub 
												ON abstractRequest.abstract_parent_type = abstractSub.id 

										LEFT JOIN " . _DB_ABSTRACT_PRESENTATION_ . " abstractPresent 
												ON abstractRequest.	abstract_child_type = abstractPresent.id					
								   
								   		INNER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
												ON abstractRequest.applicant_id = registeredDelegates.id 
											   AND registeredDelegates.status = 'A'
								   
								   LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
												ON abstractRequest.abstract_author_country_id = country.country_id
											 
								   LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
												ON abstractRequest.abstract_author_state_id = state.st_id
																
								   LEFT OUTER JOIN (
														SELECT SUM(abstractReview.marks_obtained) AS totalMarksObtained,
															   COUNT(abstractReview.id) AS totalReviewCount,
															   abstractReview.abstract_id 
														   
														  FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " abstractReview 
														  
														 WHERE abstractReview.status = 'A' 
														 
													  GROUP BY abstractReview.abstract_id 
												   ) abstractReview 
												ON abstractReview.abstract_id = abstractRequest.id 
												
											 WHERE abstractRequest.id=" . $abstractId . "";

	$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
	$rowAbstractDetails            = $resultAbstractDetails[0];

	$abstractDetails = getAbstractDetailsArray($abstractId);

	//echo '<pre>'; print_r($rowAbstractDetails['category_name']);
?>
	<style>
		.tborder td,
		.tborder th {
			font-size: 14px;
		}
	</style>
	<form name="frmAbstractReview" id="frmAbstractReview" action="all_abstract.free_paper.process.php" method="post">
		<input type="hidden" name="act" value="review" />
		<input type="hidden" name="goto" value="<?= $_REQUEST['src_show'] ?>" />
		<input type="hidden" name="abstract_id" value="<?= $rowAbstractDetails['id'] ?>" />
		<?php
		foreach ($searchArray as $keyString => $valString) {
			if ($valString != "") {
		?>
				<input type="hidden" name="<?= $keyString ?>" id="<?= $keyString ?>" value="<?= $valString ?>" />
			<?php
			}
		}



		$ReviewCount['QUERY'] = " SELECT COUNT(*) AS totalReviewAttemptCount 
							   FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
							  WHERE abstract_id =" . $rowAbstractDetails['id'] . "";
		$resultReviewCount         = $mycms->sql_select($ReviewCount);
		$rowReviewCount            = $resultReviewCount[0];
		//print_r($rowReviewCount['totalReviewAttemptCount']);
		if (!empty($rowAbstractDetails['abstract_cat']) && $rowAbstractDetails['abstract_cat'] > 0) {
			$abstractProofContent = getAbstractProofContent($abstractId, '../../');
			$abstractOriginalContent = getAbstractContent($abstractId, '../../');
			?>
			<table width="100%" class="tborder">
				<tr>
					<td colspan="2" align="left" class="tcat">Review Panel</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<a href="all_abstract.free_paper.php?show=abstarct<?= $searchString ?>">Back To Listing</a>
					</td>
				</tr>
				<tr>
					<td width="65%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						<table width="100%">
							<tr class="thighlight">
								<td align="left">Basic Details</td>
								<td align="right">
									<!-- <a href="msdoc.abstract.download.php?id=<?= $rowAbstractDetails['id'] ?>&operation=pdfdownload" target="_blank">
									<img src="images/pdf.png" height="32" style="cursor:pointer;" title="Download In .PDF Format" />
									</a> -->
									<!--<a href="msdoc.abstract.download.php?id=<?= $rowAbstractDetails['id'] ?>&operation=print" target="_blank">
									<img src="images/print.png" height="32" style="cursor:pointer;" title="Print Abstract" />-->
									</a>
								</td>
							</tr>
							<tr>
								<td width="30%" align="left">Submission Code</td>
								<td align="left"><?= strtoupper($rowAbstractDetails['abstract_submition_code']) ?></td>
							</tr>
							<tr>
								<td align="left">Submission Date</td>
								<td align="left"><?= setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D") ?></td>
							</tr>
							<?php
							if (!empty($rowAbstractDetails['category_name'])) {
							?>
								<tr>
									<td align="left">Category</td>
									<td align="left"><?= $rowAbstractDetails['category_name'] ?></td>
								</tr>
							<?php
							}
							if (!empty($rowAbstractDetails['submission_name'])) {
							?>
								<tr>
									<td align="left">Sub Category</td>
									<td align="left"><?= $rowAbstractDetails['submission_name'] ?></td>
								</tr>
							<?php
							}
							?>
						</table>
						<?
						$totalWordCount   = 0;

						//echo '<pre>'; print_r($rowAbstractDetails);
						?>
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Details</td>
							</tr>
							<?php
							if (!empty($rowAbstractDetails['abstract_topic'])) {
							?>
								<tr>
									<td width="30%" align="left">Topic</td>
									<td align="left"><?= $rowAbstractDetails['abstract_topic'] ?></td>
								</tr>
							<?php
							}
							?>
							<tr>
								<td align="left" valign="top">Title</td>
								<td align="left" valign="top"><?php echo (!empty($rowAbstractDetails['proof_abstract_title'])) ? $rowAbstractDetails['proof_abstract_title'] : $rowAbstractDetails['abstract_title'] ?></td>
							</tr>
							<tr class="tlisting">
								<td align="left" valign="top" style="color:#0066FF;">Title Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?= str_word_count($rowAbstractDetails['abstract_title']) ?></b>
								</td>
							</tr>
							<?
							if ($rowAbstractDetails['abstract_child_type'] == 'VIDEO') {
							?>
								<tr>
									<td align="left" valign="top">Description</td>
									<td align="left" valign="top">
										<?php

										$totalWordCount  += str_word_count($abstractOriginalContent['DESCRIPTION']);
										echo nl2br($abstractProofContent['DESCRIPTION']);
										?>
									</td>
								</tr>
								<tr use='contentTr'>
									<td align="left" valign="top" width="20%"><strong>Video File</strong></td>
								</tr>
								<tr use='contentTr'>
									<td colspan="3" align="left" valign="top">
										<a href="<?= _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . $rowAbstractDetails['abstract_video_file'] ?>" target="_blank" title="<?= "Download " . $rowAbstractDetails['abstract_original_file_name'] ?>">
											<img src="images/invDnld.png" style="width:20px;" /></a>
										&nbsp;<?= $rowAbstractDetails['abstract_original_file_name'] ?>
									</td>
								</tr>
								<?
							} else {


								$sqlAbstractFields			  =	array();
								$sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
									WHERE `status` = ?";

								$sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

								$resultAbstractFields = $mycms->sql_select($sqlAbstractFields);

								foreach ($resultAbstractFields as $key => $value) {

									// echo "<pre>"; print_r($rowAbstractDetails);
									$sqlAbstractFieldsVal			  =	array();
									$sqlAbstractFieldsVal['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . " 
										WHERE " . $value['field_key'] . "!='NULL' AND id='" . $rowAbstractDetails['id'] . "'";

									$resultAbstractFieldsVal = $mycms->sql_select($sqlAbstractFieldsVal);


									if ($resultAbstractFieldsVal[0]['COUNTDATA'] > 0) {

										$totalWordCount  += str_word_count($rowAbstractDetails[$value['field_key']]);
								?>


										<tr>
											<td width="30%" valign="top" align="left" style="padding: 10px 8px;"><strong><?= $value['display_name'] ?></strong></td>
											<!-- </tr>
											<tr use='contentTr' style="display:block;"> -->
											<td valign="top" align="left" style="padding: 10px 8px;"><?= $rowAbstractDetails['proof_' . $value['field_key']] == '' ? $rowAbstractDetails[$value['field_key']] : $rowAbstractDetails['proof_' . $value['field_key']] ?></td>
											<!-- <td valign="top"  align="left" style="padding: 10px 8px;"><?= $rowAbstractDetails['proof_' . $value['field_key']] ?></td> -->

										</tr>
								<?php
									}
								}
								?>
								<?php
								if (!empty($rowAbstractDetails['abstract_file'])) {
								?>
									<tr>
										<td align="left"><strong>Signed PDF</strong></td>
									</tr>

									<tr>
										<td align="left">
											<a href="<?php echo _BASE_URL_ . 'uploads/FILES.ABSTRACT.REQUEST/' . $rowAbstractDetails['abstract_file']; ?>" target="_blank">Download</a>
										</td>
									</tr>
								<?php
								}
								?>

								<?php
								if (!empty($rowAbstractDetails['abstract_image_file'])) {
								?>

									<tr>
										<td align="left"><strong>Single Image</strong></td>
									</tr>

									<tr>
										<td align="left">
											<a href="<?php echo _BASE_URL_ . 'uploads/FILES.ABSTRACT.REQUEST/' . $rowAbstractDetails['abstract_image_file']; ?>" target="_blank">Download</a>
										</td>
									</tr>
								<?php
								}
								?>
							<?php
							}
							?>
							<tr>
								<td align="left" valign="top" style="color:#0066FF;">Total Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?= $totalWordCount ?></b>
								</td>
							</tr>
						</table>
					</td>
					<td width="35%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						<table width="100%" use='marksContainer'>
							<tr>
								<td style="margin:0px; padding:0px;">
									<input type="radio" name="faculty_decision" value="NOTSUBJECT" style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required /> <b style="font-size:16px; color:#0000FF;">I am unable Review</b>
								</td>
							</tr>
							<tr>
								<td style="margin:0px; padding:0px;">
									<input type="radio" checked="checked" name="faculty_decision" value="WILLREVIEW" style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required /> <b style="font-size:16px; color:#CC0000;">Review</b>
								</td>
							</tr>
							<tr>
								<td>
								</td>
							</tr>
							<tr>
								<td use="updateMarks" style="margin:0px; padding:0px;">
									<?


									if ($_REQUEST['mode'] == 'review') {
									?>
										<table width="100%">
											<tr>
												<td align="left" class="thighlight">Score Board</td>
											</tr>
										</table>
									<?php
										//echo 'count='. $rowReviewCount['totalReviewAttemptCount'];
										if (!empty($rowReviewCount['totalReviewAttemptCount']) && $rowReviewCount['totalReviewAttemptCount'] < 200) {

											//abstractReviewUpdatePanel($rowAbstractDetails['id'], $rowAbstractDetails);
											abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
										} else {

											abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
										}
									}
									?>
								</td>
							</tr>
						</table>
						<table width="100%">
							<tr>
								<td align="left">
									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?= ($rowAbstractDetails['totalReviewAttemptCount'] > 2) ? 'Cancel' : 'Back' ?>"
										class="btn btn-medium btn-red"
										onclick="window.location.href='all_abstract.free_paper.php?show=abstarct<?= $searchString ?>'" style=" margin:10px 10px 10px 0;" />

									<?php
									if ($_REQUEST['mode'] == 'review' && $rowReviewCount['totalReviewAttemptCount'] < 200) {
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"
											value="<?= ($rowReviewCount['totalReviewAttemptCount'] == 0) ? 'Save' : 'Update...' ?>"
											style=" margin:10px 10px 10px 0;" />
									<?php
									}
									?>

								</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter" style="color: red;">&nbsp; For any query contact : +91 81005 69558 (11:00 - 20:00)</td>
				</tr>
			</table>
		<?
		} elseif ($rowAbstractDetails['abstract_parent_type'] == 'CASEREPORT') {

			$abstractProofContent = getAbstractProofContent($abstractId, '../../');
			$abstractOriginalContent = getAbstractContent($abstractId, '../../');
		?>
			<table width="100%" class="tborder">
				<tr>
					<td colspan="2" align="left" class="tcat">Case study Review Panel</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<a href="all_abstract.free_paper.php?<?= $searchString ?>">Back To Listing</a>
					</td>
				</tr>
				<tr>
					<td width="65%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						<table width="100%">
							<tr class="thighlight">
								<td align="left">Basic Details</td>
								<td align="right">
									<a href="msdoc.abstract.download.php?id=<?= $rowAbstractDetails['id'] ?>&operation=pdfdownload">
										<img src="images/pdf.png" height="32" style="cursor:pointer;" title="Download In .PDF Format" />
									</a>
									<!--<a href="msdoc.abstract.download.php?id=<?= $rowAbstractDetails['id'] ?>&operation=print" target="_blank">
									<img src="images/print.png" height="32" style="cursor:pointer;" title="Print Abstract" />
									</a>-->
								</td>
							</tr>
							<tr>
								<td width="30%" align="left">Case report Submission Code</td>
								<td align="left"><?= strtoupper($rowAbstractDetails['abstract_submition_code']) ?></td>
							</tr>
							<tr>
								<td align="left">Submission Date</td>
								<td align="left"><?= setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D") ?></td>
							</tr>
							<tr>
								<td align="left">Submission Category</td>
								<td align="left"><?= $rowAbstractDetails['abstract_child_type'] ?></td>
							</tr>
							<tr>
								<td align="left">Submission Type</td>
								<td align="left"><?= $rowAbstractDetails['abstract_parent_type'] ?></td>
							</tr>
						</table>
						<?
						$totalWordCount   = 0;
						?>
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Case Report Details</td>
							</tr>
							<tr>
								<td width="30%" align="left">Topic</td>
								<td align="left"><?= $rowAbstractDetails['abstract_topic'] ?></td>
							</tr>
							<tr>
								<td align="left" valign="top">Title</td>
								<td align="left" valign="top"><?= $abstractProofContent['TITLE'] ?></td>
							</tr>
							<tr class="tlisting">
								<td align="left" valign="top" style="color:#0066FF;">Title Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?= str_word_count($abstractOriginalContent['TITLE']) ?></b>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Description</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($abstractOriginalContent['DESCRIPTION']);
									echo nl2br($abstractProofContent['DESCRIPTION']);
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Conclusion</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($abstractOriginalContent['CONCLUSION']);
									echo nl2br($abstractProofContent['CONCLUSION']);
									?>
								</td>
							</tr>

							<tr>
								<td align="left" valign="top" style="color:#0066FF;">Total Word Count</td>
								<td align="left" valign="top" style="color:#0066FF;">
									<b><?= $totalWordCount ?></b>
								</td>
							</tr>
						</table>

					</td>
					<td width="35%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						<table width="100%" use='marksContainer'>
							<tr>
								<td style="margin:0px; padding:0px;">
									<input type="radio" name="faculty_decision" value="NOTSUBJECT" style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required /> <b style="font-size:16px; color:#0000FF;">I am unable Review</b>
								</td>
							</tr>
							<tr>
								<td style="margin:0px; padding:0px;">
									<input type="radio" checked="checked" name="faculty_decision" value="WILLREVIEW" style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required /> <b style="font-size:16px; color:#CC0000;">Review</b>
								</td>
							</tr>
							<tr>
								<td>
								</td>
							</tr>
							<tr>
								<td use="updateMarks" style="margin:0px; padding:0px;">
									<?
									if ($_REQUEST['mode'] == 'review') {
									?>
										<table width="100%">
											<tr>
												<td align="left" class="thighlight">Score Board</td>
											</tr>
										</table>

									<?php
										if ($rowReviewCount['totalReviewAttemptCount'] < 2000) {
											casereportReviewUpdatePanel($rowAbstractDetails['id'], $rowAbstractDetails);
										} else {
											casereportReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
										}
									}
									?>
								</td>
							</tr>
						</table>

						<table width="100%">
							<tr>
								<td align="left">

									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?= ($rowAbstractDetails['totalReviewAttemptCount'] > 2) ? 'Cancel' : 'Back' ?>"
										class="btn btn-medium btn-red" onclick="window.location.href='all_abstract.free_paper.php?<?= $searchString ?>'" style=" margin:10px 10px 10px 0;" />

									<?php
									if ($_REQUEST['mode'] == 'review' && $rowReviewCount['totalReviewAttemptCount'] < 2000) {
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"
											value="<?= ($rowReviewCount['totalReviewAttemptCount'] == 0) ? 'Save' : 'Update' ?>" style=" margin:10px 10px 10px 0;" />
									<?php
									}
									?>

								</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>

					<td colspan="2" class="tfooter" style="color: red;">&nbsp; For any query contact : +91 81005 69558 (11:00 - 20:00)</td>
				</tr>
			</table>
		<?
		}
		?>
	</form>
	<script>
		function setNotMySubject(obj) {
			console.log(obj);

			var parent = $(obj).parent().closest("table[use=marksContainer]");

			console.log(parent);

			console.log($(obj).is(":checked"));

			if ($(obj).val() == 'WILLREVIEW') {
				$(parent).find("td[use=updateMarks]").find("input[type=radio]").attr("required", "true");
				//$(parent).find("td[use=updateMarks]").find("textarea").attr("required","true");
				$(parent).find("td[use=updateMarks]").show();
			} else {
				$(parent).find("td[use=updateMarks]").hide();
				$(parent).find("td[use=updateMarks]").find("input[type=radio]").removeAttr("required");
				$(parent).find("td[use=updateMarks]").find("textarea").removeAttr("required");
			}
		}
		<?
		if ($abstractDetails['MARKS']['REVIEW_STATE'] == 'UNABLE') {
		?>
			$(document).ready(function() {
				$("table[use=marksContainer]").find("input[type=radio][name=faculty_decision][value=NOTSUBJECT]").trigger("click");
			});
		<?
		} else {
		?>
			$(document).ready(function() {
				$("table[use=marksContainer]").find("input[type=radio][name=faculty_decision][value=WILLREVIEW]").trigger("click");
			});
		<?
		}
		?>
	</script>
<?php
}
?>

<script type="text/javascript">
	function getSub(id) {
		//$('.subcat').hide();
		//$('#subcatPanel'+id).show();
	}
</script>