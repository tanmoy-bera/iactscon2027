<?php
include_once('includes/init.php');
include_once('../../includes/function.abstract.php');
include_once('../../includes/function.delegate.php');
page_header("Home");




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
<div class="review_section">
	<?php faculty_topNav_content() ?>
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
<footer>
	<img src="<?= _BASE_URL_ ?>images/logo_reviewPanel.png" alt="">
	<!-- <ul>
		<li>
			<h6>Total Submission</h6>
			<h5>60</h5>
		</li>
		<li>
			<h6>Reviewed</h6>
			<h5>60</h5>
		</li>
		<li>
			<h6>Pending</h6>
			<h5>60</h5>
		</li>
	</ul> -->
</footer>
<?php

page_footer();
/****************************************************************************/
/*                      ABSTRACT LISTING DETAILS WINDOW                     */
/****************************************************************************/

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

	$sqlCategory	 =	array();
	$sqlCategory['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
						  WHERE `status`='A' AND id='" . trim($category) . "'
					   ORDER BY `id` ASC";

	$resultCategory = $mycms->sql_select($sqlCategory);
?>

	<div class="review_box" id="research" style="display: block;">
		<div class="review_box_head">
			<div class="review_box_head_left">
				<h3><?= $resultCategory[0]['category'] ?> </h3>
				<h4>Submission List</h4>
			</div>
			<?php
			$sqlCountAbstract			  =	array();
			$sqlCountAbstract['QUERY']    = "SELECT id FROM " . _DB_ABSTRACT_REQUEST_ . "  
													  WHERE `status` = 'A' AND abstract_cat='" . $category . "'
												  AND id IN (SELECT abstract_id FROM " . _DB_ABSTRACT_ALLOTMENT_ . " WHERE review_user_id = '" . $loggedUserID . "')
												 ";


			$resultCountAbstract = $mycms->sql_select($sqlCountAbstract);
			$totalCountAbstract = $mycms->sql_numrows($resultCountAbstract);
			if ($totalCountAbstract > 0) {
				$alloted_abs_id_arr = array();
				foreach ($resultCountAbstract as $key => $rowAbs) {
					$alloted_abs_id_arr[$key] = $rowAbs['id'];
				}
				$alloted_abs_ids = implode(',', $alloted_abs_id_arr);

				$sqlCountAbstract			  =	array();
				$sqlCountAbstract['QUERY']    = "SELECT COUNT(*) REVIEWED FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . "  
													  WHERE `status` = 'A' AND `faculty_id`= '" . $loggedUserID . "' 
												  AND abstract_id IN (" . $alloted_abs_ids . ")";
				$resultReviewCountAbstract = $mycms->sql_select($sqlCountAbstract);
				$review_count = $resultReviewCountAbstract[0]['REVIEWED'];
				$pending =  $totalCountAbstract - $resultReviewCountAbstract[0]['REVIEWED'];
				// echo "<pre>";print_r($resultReviewCountAbstract);
			} else {
				$review_count = 0;
				$pending = 0;
			}
			?>
			<ul>
				<li>
					<h6>Total Submission</h6>
					<h5><?= $totalCountAbstract ?></h5>
				</li>
				<li>
					<h6>Reviewed</h6>
					<h5><?= $review_count ?></h5>
				</li>
				<li>
					<h6>Pending</h6>
					<h5><?= $pending ?></h5>
				</li>
			</ul>
		</div>

		<form name="frmSearch" id="frmSearch" action="admin.php?cat=<?= $category  ?>" method="post">
			<div class="filter">
				<input type="hidden" name="goto" value="<?= $searchArray['goto'] ?>" />

				<div>
					<label for="">Submission Code:</label>
					<input type="text" name="src_abstract_submission_code" id="src_abstract_submission_code"
						style="width:90%; text-transform:uppercase;" value="<?= $_REQUEST['src_abstract_submission_code'] ?>" />
				</div>
				<div>
					<label for="">Topic:</label>
					<select name="src_abstract_topic_id" id="src_abstract_topic_id" style="text-transform:uppercase; width:70%;">
						<option value="">-- Select Topic --</option>
						<?php
						$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
																			WHERE `status` = 'A' AND `category`= '" . $category . "' 
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
				</div>
				<?php

				if ($maxRowsSubCat > 0 && $_REQUEST['category'] > 0) {
				?>
					<div>
						<label for="">Sub Category:</label>
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
					</div>

				<?
				}
				?>
				<input type="hidden" name="category" id="category" value="<?= $_REQUEST['cat'] ?>">
				<input type="hidden" name="cat" value="<?= $_REQUEST['cat'] ?>">
				<?php
				if ($searchStatus) {
				?>
					<input type="button" class="btn btn-small btn-danger"
						value="Clear" onclick="window.location.href='<?= 'admin.php?cat=' . $_REQUEST['category'] ?>'" />
				<?php
				}
				?>

				<button><i class="fas fa-search"></i></button>

			</div>
		</form>
		<ul class="rsah_listing">
			<ul class="rsah_listing">
				<?php
				$sqlAbstractDetails			   = abstractDetailsQuerySet("", $searchCondition);
				//echo '<pre>';print_r($sqlAbstractDetails);echo '</pre>';
				$resultAbstractDetails	       = $mycms->sql_select($sqlAbstractDetails);
				if ($resultAbstractDetails) {
					foreach ($resultAbstractDetails as $i => $rowAbstractDetails) {
						$counter++;

						$ReviewMarks['QUERY'] 		= "SELECT marks_obtained AS totalMarksObtained 
																	 FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
																	WHERE abstract_id =" . $rowAbstractDetails['id'] . " 
																	  AND status = 'A'
																	  AND `faculty_id` = '" . $loggedUserID . "'";

						$resultReviewMarks  = $mycms->sql_select($ReviewMarks);
						$rowReviewMarks     = $resultReviewMarks[0];

						$abstractDetails = getAbstractDetailsArray($rowAbstractDetails['id']);

						// echo '<pre>'; print_r($abstractDetails);

						if ($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE'] == 'UNABLE') {
							$rowStyleDecission = " style='background-color: #FF28FF;'";
						} elseif ($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE'] == 'ABLE') {
							$rowStyleDecission = " style='background-color: #DDF1D6;'";
						} else {
							$rowStyleDecission = " style='background-color: #FFFFFF;'";
						}
				?>

						<li>
							<h2><?= $counter ?></h2>
							<h3><span><b>Subm. Code</b></span><br><?= $abstractDetails['SUBMISSION_CODE'] ?></h3>
							<h4><span><b>Abstract Title</b></span><br><?= $rowAbstractDetails['abstract_title'] ?></h4>
							<h5><span><b>Status</b></span><br>
								<?php
								$reviewGrade = getReviewerGrade($rowAbstractDetails['id'], $loggedUserID);

								if ($reviewGrade != '') {
									echo "<b>" . htmlspecialchars($reviewGrade) . "</b>";
								} elseif (trim($rowReviewMarks['totalMarksObtained']) != '') {
									echo "<b>" . $rowReviewMarks['totalMarksObtained'] . "</b>";
								} else {
									echo "Not Reviewed";
								}
								?>
							</h5>
							<h6>
								<?php
								if (true) {
									if (trim($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['REVIEW_STATE']) == 'NOTREVIEW') {
								?>
										<a href="admin.php?show=view&cat=<?= $rowAbstractDetails['abstract_cat'] ?>&mode=review&id=<?= $rowAbstractDetails['id'] ?>&tags=<?= $rowAbstractDetails['tags'] . $searchString ?>"><i class="fas fa-eye" title="Review"></i></a>
									<?php
									} elseif (trim($abstractDetails['MARKS']['REVIEWER'][$loggedUserID]['TOTAL']) != '') {
									?>
										<a href="admin.php?show=view&cat=<?= $rowAbstractDetails['abstract_cat'] ?>&mode=review&id=<?= $rowAbstractDetails['id'] ?>&tags=<?= $rowAbstractDetails['tags'] . $searchString ?>"><i class="fas fa-eye" title="Update Review"></i></a>
									<?php
									}
								} else {
									?>
									<a href="admin.php?show=view&cat=<?= $rowAbstractDetails['abstract_cat'] ?>&mode=viewOnly&id=<?= $rowAbstractDetails['id'] ?>&tags=<?= $rowAbstractDetails['tags'] . $searchString ?>"><i class="fas fa-eye" title="View"></i></a>
								<?php
								}
								?>


							</h6>
						</li>
					<?php
					}
				} else {
					?>
					<li>No Record(s) Found</li>
				<?php
				}
				?>
			</ul>

	</div>
	<!-- <div class="review_box" id="case">
		<div class="review_box_head">
			<div class="review_box_head_left">
				<h3>Research Paper</h3>
				<h4>Submission List</h4>
			</div>
			<ul>
				<li>
					<h6>Total Submission</h6>
					<h5>60</h5>
				</li>
				<li>
					<h6>Reviewed</h6>
					<h5>60</h5>
				</li>
				<li>
					<h6>Pending</h6>
					<h5>60</h5>
				</li>
			</ul>
		</div>
		<div class="filter">
			<div>
				<label for="">Submission Code:</label>
				<input type="text">
			</div>
			<div>
				<label for="">Topic:</label>
				<select>
					<option>--Select--</option>
				</select>
			</div>
			<button><i class="fas fa-search"></i></button>
		</div>
		<ul class="rsah_listing">
			<ul class="rsah_listing">
				<li>
					<h2>1</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>2</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>1</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>2</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>1</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>2</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>1</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>2</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>1</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
				<li>
					<h2>2</h2>
					<h3><span>Subm. Code</span><br>4000215</h3>
					<h4><span>Abstract Title</span><br>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</h4>
					<h5><span>Status</span><br>Not Reviewed</h5>
					<h6>
						<a href="#"><i class="fas fa-eye"></i></a>
					</h6>
				</li>
			</ul>
	</div> -->

<?php
}

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
?><div class="review_box" id="research" style="display: block;">
		<div class="review_box_head">
			<div class="review_box_head_left">
				<h3><?= $rowAbstractDetails['category_name'] ?></h3>
				<h4>Review Panel</h4>
			</div>
			<a href="admin.php?cat=<?= $_REQUEST['cat'] ?>"><i class="fas fa-arrow-left"></i> Back</a>
		</div>

		<form name="frmAbstractReview" id="frmAbstractReview" action="all_abstract.free_paper.process.php" method="post">
			<input type="hidden" name="act" value="review" />
			<input type="hidden" name="goto" value="<?= $_REQUEST['src_show'] ?>" />
			<input type="hidden" name="abstract_id" value="<?= $rowAbstractDetails['id'] ?>" />
			<input type="hidden" name="cat_id" value="<?= $rowAbstractDetails['abstract_cat'] ?>" />
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
				<div class="review_basic_deyails">
					<div class="basic_details_left">
						<h4 class="sub_head">Basic Details</h4>
						<ul>
							<li>
								<h6>Submission Code</h6>
								<h5><?= strtoupper($rowAbstractDetails['abstract_submition_code']) ?></h5>
							</li>
							<li>
								<h6>Submission Date</h6>
								<h5><?= setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D") ?></h5>
							</li>
							<?php
							if (!empty($rowAbstractDetails['category_name'])) {
							?>
								<li>
									<h6>Category</h6>
									<h5><?= $rowAbstractDetails['category_name'] ?></h5>
								</li>
							<?php
							}
							if (!empty($rowAbstractDetails['submission_name'])) {
							?>
								<li>
									<h6>Sub Category</h6>
									<h5><?= $rowAbstractDetails['submission_name'] ?></h5>
								</li>
							<?php
							}
							?>
						</ul>
					</div>
					<div class="basic_details_right">
						<label class="rvw_chk">Skip
							<!-- <input type="checkbox"> -->
							<input type="radio" name="faculty_decision" value="NOTSUBJECT" style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required />

							<span class="checkmark"></span>
						</label>
						<label class="rvw_chk">Review
							<!-- <input type="checkbox"> -->
							<input type="radio" checked="checked" name="faculty_decision" value="WILLREVIEW" style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" onclick="setNotMySubject(this);" required />

							<span class="checkmark"></span>
						</label>
					</div>
				</div>
				<?
				$totalWordCount   = 0;

				//echo '<pre>'; print_r($rowAbstractDetails);
				?>
				<div class="review_submission_details">
					<div class="submission_details_left">
						<h4 class="sub_head">Details</h4>
						<ul>
							<?php
							if (!empty($rowAbstractDetails['abstract_topic'])) {
							?>
								<li>
									<h6>Topic</h6>
									<h5><b><?= $rowAbstractDetails['abstract_topic'] ?></b></h5>
								</li>
							<?php
							}
							?>
							<li>
								<h6>Title</h6>
								<h5><b><?php echo (!empty($rowAbstractDetails['proof_abstract_title'])) ? $rowAbstractDetails['proof_abstract_title'] : $rowAbstractDetails['abstract_title'] ?></b></h5>
							</li>
							<li>
								<h6>Title Word Count</h6>
								<h5><b><?= str_word_count($rowAbstractDetails['abstract_title']) ?></b></h5>
							</li>
							<hr>
							<?
							if ($rowAbstractDetails['abstract_child_type'] == 'VIDEO') {
							?>
								<li>
									<h6>Description</h6>
									<td align="left" valign="top">
										<?php

										$totalWordCount  += str_word_count($abstractOriginalContent['DESCRIPTION']);
										echo nl2br($abstractProofContent['DESCRIPTION']);
										?>
									</td>
								</li>
								<li use='contentTr'>
									<h6><strong>Video File</strong></h6>
								</li>
								<li use='contentTr'>
									<h5>
										<a href="<?= _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . $rowAbstractDetails['abstract_video_file'] ?>" target="_blank" title="<?= "Download " . $rowAbstractDetails['abstract_original_file_name'] ?>">
											<img src="images/invDnld.png" style="width:20px;" /></a>
										&nbsp;<?= $rowAbstractDetails['abstract_original_file_name'] ?>
									</h5>
								</li>
								<hr>
								<?
							} else {
							function cleanFields($fields){
								$fields = json_decode($fields, true);

								if(!is_array($fields)){
									return [];
								}

								// remove empty values like "", null
								$fields = array_filter($fields, function($val){
									return $val !== null && $val !== '';
								});

								// ensure integers only (security)
								return array_map('intval', $fields);
							}
								$category_id = $rowAbstractDetails['abstract_cat']?? null;
								$sub_category_id =$rowAbstractDetails['abstract_parent_type'] ?? null;
								$sub_subcategory_Id = $rowAbstractDetails['abstract_child_type'] ?? null;

								$category_fields = [];

								/* ================= CATEGORY ================= */
								if($category_id){

									$sqlCategory1 = [
										'QUERY' => "SELECT category_fields 
													FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
													WHERE id = ?",
										'PARAM' => [
											['FILD'=>'id','DATA'=>$category_id,'TYP'=>'i']
										]
									];

									$resultCategory1 = $mycms->sql_select($sqlCategory1);
									
									if($resultCategory1){
										$category_fields = cleanFields($resultCategory1[0]['category_fields']);
									}

									/* ================= SUB CATEGORY ================= */
									if(empty($category_fields) && $sub_category_id){

										$sqlSubmission = [
											'QUERY' => "SELECT category_fields 
														FROM "._DB_ABSTRACT_SUBMISSION_."
														WHERE category = ? AND id = ?
														LIMIT 1",
											'PARAM' => [
												['FILD'=>'category','DATA'=>$category_id,'TYP'=>'i'],
												['FILD'=>'id','DATA'=>$sub_category_id,'TYP'=>'i']
											]
										];

										$resultSubmission = $mycms->sql_select($sqlSubmission);

										if($resultSubmission){
											$category_fields = cleanFields($resultSubmission[0]['category_fields']);
										}
									}

									/* ================= SUB SUB CATEGORY ================= */
									if(empty($category_fields) && $sub_subcategory_Id){

										$sqlPresentation = [
											'QUERY' => "SELECT category_fields 
														FROM "._DB_ABSTRACT_PRESENTATION_."
														WHERE category_id = ? 
														AND submission_id = ? 
														AND id = ?
														LIMIT 1",
											'PARAM' => [
												['FILD'=>'category_id','DATA'=>$category_id,'TYP'=>'i'],
												['FILD'=>'submission_id','DATA'=>$sub_category_id,'TYP'=>'i'],
												['FILD'=>'id','DATA'=>$sub_subcategory_Id,'TYP'=>'i']
											]
										];

										$resultPresentation = $mycms->sql_select($sqlPresentation);

										if($resultPresentation){
											$category_fields = cleanFields($resultPresentation[0]['category_fields']);
										}
									}
								}

								/* ================= FINAL FALLBACK ================= */
								if(empty($category_fields)){
									$category_fields = [];
								}
								$field_ids = implode(",", $category_fields);
								if (!empty($field_ids)) {
									$order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
								} else {
									$order_by = "ORDER BY id ASC";
								}

								$sqlAbstractFields			  =	array();
								$sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
									WHERE `status` = ? ".$order_by."";

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


										<li>
											<h6><?= $value['display_name'] ?></h6>
											<h5><b><?= $rowAbstractDetails['proof_' . $value['field_key']] == '' ? $rowAbstractDetails[$value['field_key']] : $rowAbstractDetails['proof_' . $value['field_key']] ?></b></h5>
										</li>
								<?php
									}
								}
								?>
								<?php
								if (!empty($rowAbstractDetails['abstract_file'])) {
								?>
									<li>
										<h6><strong>Signed PDF</strong></h6>
									</li>

									<li>
										<h5>
											<a href="<?php echo _BASE_URL_ . 'uploads/FILES.ABSTRACT.REQUEST/' . $rowAbstractDetails['abstract_file']; ?>" target="_blank">Download</a>
										</h5>
									</li>
								<?php
								}
								?>
								<?php
								if (!empty($rowAbstractDetails['abstract_image_file'])) {
								?>

									<li>
										<h6><strong>Single Image</strong></h6>
									</li>

									<li>
										<h5>
											<a href="<?php echo _BASE_URL_ . 'uploads/FILES.ABSTRACT.REQUEST/' . $rowAbstractDetails['abstract_image_file']; ?>" target="_blank">Download</a>
										</h5>
									</li>
								<?php
								}
								?>
							<?php
							}
							?>
							<li>
								<h6>Total Word Count</h6>
								<h5><b><?= $totalWordCount ?></b></h5>
							</li>
						</ul>
					</div>

					<div class="submission_details_right">
						<?


						if ($_REQUEST['mode'] == 'review') {
						?>
							<div id="score_board_wrap">
								<h4 class="sub_head">SCORE BOARD</h4>
								<?php
								//echo 'count='. $rowReviewCount['totalReviewAttemptCount'];
								if (!empty($rowReviewCount['totalReviewAttemptCount']) && $rowReviewCount['totalReviewAttemptCount'] < 200) {

									//abstractReviewUpdatePanel($rowAbstractDetails['id'], $rowAbstractDetails);
									abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
								} else {

									abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
								}
								?>
							</div>
						<?php
						}
						?>


						<div class="remarks_btn_wrap">
							<!-- <input type="button" name="bttnWindowView" id="bttnWindowView" value="<?= ($rowAbstractDetails['totalReviewAttemptCount'] > 2) ? 'Cancel' : 'Back' ?>"
								class="btn btn-secondary"
								onclick="window.location.href='admin.php?show=abstarct<?= $searchString ?>'" style=" margin:10px 10px 10px 0;" /> -->
							<button type="button" onclick="window.location.href='admin.php?cat=<?= $_REQUEST['cat'] ?>'"><?= ($rowAbstractDetails['totalReviewAttemptCount'] > 2) ? 'Cancel' : 'Back' ?></button>

							<?php
							if ($_REQUEST['mode'] == 'review' && $rowReviewCount['totalReviewAttemptCount'] < 200) {
							?>
								<!-- <input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-danger"
									value="<?= ($rowReviewCount['totalReviewAttemptCount'] == 0) ? 'Save' : 'Update...' ?>"
									style=" margin:10px 10px 10px 0;" /> -->
								<button type="button" id="submit_marks" class="save"><?= ($rowReviewCount['totalReviewAttemptCount'] == 0) ? 'Save' : 'Update...' ?></button>

							<?php
							}
							?>
						</div>
					</div>
				</div>
				<script>
					$(document).on("click", "#submit_marks", function(e) {
						e.preventDefault();
						// alert(1)
						let isValid = true;
						let message = "";

						let total_criteria = Number($('#total_criteria').val());
						let faculty_decision = $('input[type=radio][name="faculty_decision"]:checked').val();
						if ($("input[operationMode='abstract_review_marks']:checked").length < total_criteria && faculty_decision == 'WILLREVIEW') {
							isValid = false;
							message = "Please select marks for all review criteria.";
							alert(message);

							return false; 
						}

						if (!isValid) {
							e.preventDefault();
							// alert(message);
							return false;
						}

						if (isValid) {
							$("#frmAbstractReview").submit();
						}
					});
				</script>
		</form>
	</div>
<?
			} elseif ($rowAbstractDetails['abstract_parent_type'] == 'CASEREPORT') {

				$abstractProofContent = getAbstractProofContent($abstractId, '../../');
				$abstractOriginalContent = getAbstractContent($abstractId, '../../');
			}
?>

<?php
}
?>

<script>


	function setNotMySubject(obj) {
		// console.log(obj);
		console.log($(obj).is(":checked"));

		if ($(obj).val() == 'WILLREVIEW') {
			$('#score_board_wrap').show();
			$("input[type=radio][operationMode='abstract_review_marks']").attr("required", "true");
			//$(parent).find("li[use=updateMarks]").find("textarea").attr("required","true");

		} else {
			$("input[type=radio][operationMode='abstract_review_marks']").removeAttr("required");
			// $(parent).find("li[use=updateMarks]").find("textarea").removeAttr("required");
			$('#score_board_wrap').hide();

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
page_footer();
?>