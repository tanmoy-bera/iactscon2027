<?php
function abstractDetailsQuerySet($abstractId = "", $searchCondition = "", $limitCondition = "")
{
	global $cfg, $mycms;


	$filterCondition         = "";

	if ($abstractId != "") {
		$filterCondition    .= " AND abstractRequest.id =" . $abstractId . "";
	}

	$sqlAbstractDetails   = array();
	$sqlAbstractDetails['QUERY']  = "					SELECT abstractRequest.*,
															   abstractTopic.abstract_topic,
															   
															   registeredDelegates.id AS delegate_id,
															   registeredDelegates.user_email_id,
															   registeredDelegates.user_mobile_no,
															   registeredDelegates.user_unique_sequence,
															   registeredDelegates.user_registration_id,
															   registeredDelegates.account_status,
															   registeredDelegates.registration_classification_id,
															    registeredDelegates.registration_payment_status,
																 registeredDelegates.registration_tariff_cutoff_id,
																  registeredDelegates.user_unique_sequence,
															   registeredDelegates.user_institute_name AS delegateInstitute,
															   registeredDelegates.user_department AS delegateDepertment,
															   registeredDelegates.user_designation AS delegateDesignation,
															   registeredDelegates.user_city AS delegatecity,
															    IFNULL(registeredDelegates.user_full_name, '') AS user_full_name,
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
															   
															   IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
															   IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
															   IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName,
															   IFNULL(abstractRequest.abstract_submition_code, '') AS abstract_submition_code,
															   
															   abstractAward.award_ids AS award_ids, 
															   abstractAward.award_names AS award_names
							
														  FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest 
														  
											   LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
															ON abstractRequest.abstract_topic_id = abstractTopic.id 
															
											   LEFT OUTER JOIN ( SELECT GROUP_CONCAT(award_id) AS award_ids, GROUP_CONCAT(award_name) AS award_names, req.submission_id AS submission_id
											   					   FROM " . _DB_AWARD_REQUEST_ . " req
															 INNER JOIN " . _DB_AWARD_MASTER_ . " awrd
															 		 ON awrd.id = req.award_id
																  WHERE req.status = 'A'
															   GROUP BY req.submission_id) abstractAward 
															ON abstractRequest.id = abstractAward.submission_id 
															
											   LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
															ON abstractRequest.abstract_author_country_id = country.country_id
														 
											   LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
															ON abstractRequest.abstract_author_state_id = state.st_id
											   
											   LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
															ON abstractRequest.applicant_id = registeredDelegates.id 
											   
											   
														  WHERE abstractRequest.status = 'A' AND registeredDelegates.status = 'A'  " . $filterCondition . " " . $searchCondition . " 
													
													  ORDER BY abstractRequest.id DESC " . $limitCondition . "";


	return $sqlAbstractDetails;
}

function getAbstractDetailsArray($abstractId)
{
	global $cfg, $mycms;

	$return = array();

	$sql = abstractDetailsQuerySet($abstractId);

	$resultAbstractDetails         = $mycms->sql_select($sql);

	foreach ($resultAbstractDetails as $i => $rowAbstractDetails) {
		$return['RAW'] 				= $rowAbstractDetails;
		$return['SUBMISSION_CODE'] 	= $rowAbstractDetails['abstract_submition_code'];
		$return['SUBMISSION_DATE'] 	= $rowAbstractDetails['created_dateTime'];
		$return['TOPIC'] 			= $rowAbstractDetails['abstract_topic'];
		$return['PARENT_TYPE'] 		= $rowAbstractDetails['abstract_parent_type'];
		$return['CHILD_TYPE'] 		= $rowAbstractDetails['abstract_child_type'];
		$return['CONTENT']			= getAbstractContent($abstractId);
		$return['PROOF_CONTENT']	= getAbstractProofContent($abstractId);
		$return['MARKS']			= getAbstractRsultArray($abstractId);
	}

	return $return;
}

function getTotalAbstractCount($delegatesId)
{
	global $cfg, $mycms;
	$sqlFetch			  =	array();
	$sqlFetch['QUERY']	  = "SELECT count(id) AS totalAbstractCount 
							   FROM " . _DB_ABSTRACT_REQUEST_ . " 
							  WHERE status = ?
								AND tags = ?
								AND applicant_id = ? ";

	$sqlFetch['PARAM'][]   = array('FILD' => 'status',  	   'DATA' => 'A',  'TYP' => 's');
	$sqlFetch['PARAM'][]   = array('FILD' => 'tags',  	  	   'DATA' => 'Abstract',  'TYP' => 's');
	$sqlFetch['PARAM'][]   = array('FILD' => 'applicant_id',  'DATA' => $delegatesId,  'TYP' => 's');


	$result			     	    = $mycms->sql_select($sqlFetch);

	return $result[0]['totalAbstractCount'];
}

function getAbstractContent($abstractId, $rootPath = '')
{
	global $cfg, $mycms;

	$return = array();

	$sqlFetch			  =	array();
	$sqlFetch['QUERY']	  = "SELECT *
							   FROM " . _DB_ABSTRACT_REQUEST_ . " 
							  WHERE id = ? ";

	$sqlFetch['PARAM'][]   = array('FILD' => 'id',  			'DATA' => $abstractId,  'TYP' => 's');
	$result			       = $mycms->sql_select($sqlFetch);
	$row 				   = $result[0];

	$return['TITLE'] = $row['abstract_title'];

	//if($row['abstract_parent_type']=='CASEREPORT' && $row['abstract_child_type']=='POSTER')
	if ($row['abstract_parent_type'] == 'CASE REPORT' && $row['abstract_category'] == 'Free Paper') {
		$return['BACKGROUND'] 			= trim($row['abstract_background']);
		$return['DESCRIPTION'] 			= trim($row['abstract_description']);
		$return['CONCLUSION'] 			= trim($row['abstract_results']) . trim($row['abstract_conclution']);
	} elseif ($row['abstract_parent_type'] == 'ABSTRACT' && $row['abstract_category'] == 'Free Paper') {
		$return['BACKGROUND_N_AIM'] 	= trim($row['abstract_background']) . "\n\n" . trim($row['abstract_background_aims']);
		$return['MATERIAL_N_METHOD']	= trim($row['abstract_material_methods']);
		$return['RESULT']				= trim($row['abstract_results']);
		$return['CONCLUSION']			= trim($row['abstract_conclution']);
	} elseif ($row['abstract_parent_type'] == '' && $row['abstract_category'] == 'Award Paper') {
		$return['BACKGROUND_N_AIM'] 	= trim($row['abstract_background']) . "\n\n" . trim($row['abstract_background_aims']);
		$return['MATERIAL_N_METHOD']	= trim($row['abstract_material_methods']);
		$return['RESULT']				= trim($row['abstract_results']);
		$return['CONCLUSION']			= trim($row['abstract_conclution']);
		$return['FILE_PATH']			= _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . trim($row['abstract_file']);
		$return['FILE_NAME']			= trim($row['abstract_original_file_name']);
	} else {
		$return['BACKGROUND_N_AIM'] 	= trim($row['abstract_background']) . "\n\n" . trim($row['abstract_background_aims']);
		$return['MATERIAL_N_METHOD']	= trim($row['abstract_material_methods']);
		$return['RESULT']				= trim($row['abstract_results']);
		$return['CONCLUSION']			= trim($row['abstract_conclution']);
		$return['FILE_PATH']			= _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . trim($row['abstract_file']);
		$return['FILE_NAME']			= trim($row['abstract_original_file_name']);
	}
	/*elseif($row['abstract_parent_type']=='ABSTRACT' && $row['abstract_child_type']=='ORAL')
	{
		$return['BACKGROUND_N_AIM'] 	= trim($row['abstract_background'])."\n\n".trim($row['abstract_background_aims']); 
		$return['MATERIAL_N_METHOD']	= trim($row['abstract_material_methods']); 
		$return['RESULT']				= trim($row['abstract_results']); 
		$return['CONCLUSION']			= trim($row['abstract_conclution']); 
	}
	elseif($row['abstract_parent_type']=='ABSTRACT' && $row['abstract_child_type']=='VIDEO')
	{
		$return['DESCRIPTION'] 			= trim($row['abstract_background'])."\n\n".trim($row['abstract_background_aims'])."\n\n".trim($row['abstract_material_methods']); 
		$return['CONCLUSION']			= trim($row['abstract_conclution']); 
		$return['VIDEO_PATH']			= _BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].trim($row['abstract_video_file']);
		$return['VIDEO_NAME']			= trim($row['abstract_original_file_name']);
	}*/

	return $return;
}

function getAbstractProofContent($abstractId, $rootPath = '')
{
	global $cfg, $mycms;

	$return = array();

	$sqlFetch			  =	array();
	$sqlFetch['QUERY']	  = "SELECT *
							   FROM " . _DB_ABSTRACT_REQUEST_ . " 
							  WHERE id = ? ";

	$sqlFetch['PARAM'][]   = array('FILD' => 'id',  			'DATA' => $abstractId,  'TYP' => 's');
	$result			       = $mycms->sql_select($sqlFetch);
	$row 				   = $result[0];

	$return['TITLE'] = $row['proof_abstract_title'];

	if ($row['abstract_parent_type'] == 'CASEREPORT' && $row['abstract_child_type'] == 'POSTER') {
		$return['DESCRIPTION'] 			= trim($row['proof_abstract_description']);
		$return['CONCLUSION'] 			= trim($row['proof_abstract_conclution']);
	} elseif ($row['abstract_parent_type'] == 'ABSTRACT' && $row['abstract_child_type'] == 'POSTER') {
		$return['BACKGROUND_N_AIM'] 	= trim($row['proof_abstract_background_aims']);
		$return['MATERIAL_N_METHOD']	= trim($row['proof_abstract_material_methods']);
		$return['RESULT']				= trim($row['proof_abstract_results']);
		$return['CONCLUSION']			= trim($row['proof_abstract_conclution']);
	} elseif ($row['abstract_parent_type'] == 'ABSTRACT' && $row['abstract_child_type'] == 'ORAL') {
		$return['BACKGROUND_N_AIM'] 	= trim($row['proof_abstract_background_aims']);
		$return['MATERIAL_N_METHOD']	= trim($row['proof_abstract_material_methods']);
		$return['RESULT']				= trim($row['proof_abstract_results']);
		$return['CONCLUSION']			= trim($row['proof_abstract_conclution']);
	} elseif ($row['abstract_parent_type'] == 'ABSTRACT' && $row['abstract_child_type'] == 'VIDEO') {
		$return['DESCRIPTION'] 			= trim($row['proof_abstract_description']);
		$return['CONCLUSION']			= trim($row['proof_abstract_conclution']);
		$return['VIDEO_PATH']			= _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . trim($row['abstract_video_file']);
		$return['VIDEO_NAME']			= trim($row['abstract_original_file_name']);
	}

	return $return;
}

function getAbstractAllotedUserIds($abstractId)
{
	global $cfg, $mycms;

	$return = array();

	$sqlAllotmentDetails					= array();
	$sqlAllotmentDetails['QUERY'] 			= " SELECT *
												  FROM " . _DB_ABSTRACT_ALLOTMENT_ . "  
												 WHERE abstract_id = '" . $abstractId . "'";

	$resultAllotmentDetails         		= $mycms->sql_select($sqlAllotmentDetails);
	if ($resultAllotmentDetails) {
		foreach ($resultAllotmentDetails as $i => $rowAllotmentDetails) {
			$return[] = $rowAllotmentDetails['review_user_id'];
		}
	}

	return $return;
}

function getReviewUserAllotedAbstractIds($userId)
{
	global $cfg, $mycms;

	$return = array();

	$sqlAllotmentDetails					= array();
	$sqlAllotmentDetails['QUERY'] 			= " SELECT *
												  FROM " . _DB_ABSTRACT_ALLOTMENT_ . "  
												 WHERE review_user_id = '" . $userId . "'";

	$resultAllotmentDetails         		= $mycms->sql_select($sqlAllotmentDetails);
	if ($resultAllotmentDetails) {
		foreach ($resultAllotmentDetails as $i => $rowAllotmentDetails) {
			$return[] = $rowAllotmentDetails['abstract_id'];
		}
	}

	return $return;
}

function getAbstractRsultArray($abstractId)
{
	global $cfg, $mycms;

	$return = array();

	$allocations				= getAbstractAllotedUserIds($abstractId);

	$return['REVIEW_COUNT'] 	= 0;
	$return['TOTAL'] 			= 0;
	$return['AVERAGE'] 			= 0;

	foreach ($allocations as $lki => $faculty_id) {
		$ReviewMarks       			= array();
		$ReviewMarks['QUERY'] 		= "SELECT details.*, 										  
											  criteria.review_criteria,
											  result.abstract_id, result.faculty_id, result.marks_obtained, result.faculty_review,
											  IFNULL(faculty.faculty_title, '') AS facultyTitle,
											  IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
											  IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
											  IFNULL(faculty.faculty_last_name, '') AS facultyLastName
										 FROM " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " details							  
								   INNER JOIN " . _DB_ABSTRACT_REVIEW_CRITERIA_ . " criteria
										   ON criteria.id = details.review_criteria_id
								   INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_ . " result
										   ON result.id = details.review_result_id
								   INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " faculty
										   ON faculty.id = result.faculty_id
										  AND faculty.status = 'A'
										WHERE result.`abstract_id` = ? 
										  AND result.`faculty_id` =  ?
										  AND result.`status` =  ?";

		$ReviewMarks['PARAM'][]    = array('FILD' => 'abstract_id',  'DATA' => $abstractId,  			  'TYP' => 's');
		$ReviewMarks['PARAM'][]    = array('FILD' => 'faculty_id',   'DATA' => $faculty_id,  			  'TYP' => 's');
		$ReviewMarks['PARAM'][]    = array('FILD' => 'status',       'DATA' => 'A',                        'TYP' => 's');

		$resultReviewMarks  	   = $mycms->sql_select($ReviewMarks);

		if ($resultReviewMarks) {
			$totalMarksInDB				= 0;

			foreach ($resultReviewMarks as $kk => $rowReviewMarks) {
				$return['REVIEWER'][$faculty_id]['FACULTY_ID'] 									= $rowReviewMarks['faculty_id'];
				$return['REVIEWER'][$faculty_id]['NAME'] 										= $rowReviewMarks['facultyTitle'] . " " . $rowReviewMarks['facultyFirstName'] . " " . $rowReviewMarks['facultyMiddleName'] . " " . $rowReviewMarks['facultyLastName'] . " ";
				$return['REVIEWER'][$faculty_id]['MARKS'][$rowReviewMarks['review_criteria']]	= $rowReviewMarks['review_obtained_marks_invalue'];
				$return['REVIEWER'][$faculty_id]['TOTAL']										= $return['REVIEWER'][$faculty_id]['TOTAL'] + $rowReviewMarks['review_obtained_marks_invalue'];
				$return['REVIEWER'][$faculty_id]['REMARKS']										= $rowReviewMarks['faculty_review'];

				$return['BREAKUP'][$rowReviewMarks['review_criteria']]['TOTAL'] 				= $return['BREAKUP'][$rowReviewMarks['review_criteria']]['TOTAL'] + $rowReviewMarks['review_obtained_marks_invalue'];
				$return['BREAKUP'][$rowReviewMarks['review_criteria']]['COUNT'] 				= $return['BREAKUP'][$rowReviewMarks['review_criteria']]['COUNT'] + 1;
				$return['BREAKUP'][$rowReviewMarks['review_criteria']]['AVERAGE']				= $return['BREAKUP'][$rowReviewMarks['review_criteria']]['TOTAL'] / $return['BREAKUP'][$rowReviewMarks['review_criteria']]['COUNT'];



				$totalMarksInDB				= $rowReviewMarks['marks_obtained'];
			}

			$return['TOTAL'] 			= $return['TOTAL'] + $return['REVIEWER'][$faculty_id]['TOTAL'];
			$return['REVIEW_COUNT'] 	= $return['REVIEW_COUNT'] + 1;
			$return['AVERAGE'] 			= round(floatval($return['TOTAL']) / floatval($return['REVIEW_COUNT']), 2);

			if ($totalMarksInDB != $return['REVIEWER'][$faculty_id]['TOTAL']) {
				$return['REVIEWER'][$faculty_id]['CALCULATION_STATUS']  = 'NOT OK';
			} else {
				$return['REVIEWER'][$faculty_id]['CALCULATION_STATUS']  = 'OK';
			}
			$return['REVIEWER'][$faculty_id]['REVIEW_STATE'] 					= 'ABLE';
		} else {
			$sqlAllotmentDetails					= array();
			$sqlAllotmentDetails['QUERY'] 			= " SELECT allotment.*,
															   IFNULL(faculty.faculty_title, '') AS facultyTitle,
															   IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
															   IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
															   IFNULL(faculty.faculty_last_name, '') AS facultyLastName
														  FROM " . _DB_ABSTRACT_ALLOTMENT_ . "  allotment
													INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " faculty
										   					ON faculty.id = allotment.review_user_id
														   AND faculty.status = 'A'
														 WHERE review_user_id = '" . $faculty_id . "'
														   AND abstract_id = '" . $abstractId . "'";

			$resultAllotmentDetails         		= $mycms->sql_select($sqlAllotmentDetails);

			if ($resultAllotmentDetails) {
				$rowAllotmentDetails					= $resultAllotmentDetails[0];

				if ($rowAllotmentDetails['notmystuff'] == 'NOTMYSTUFF') {
					//$return['REVIEW_COUNT'] 											= $return['REVIEW_COUNT']+1;
					$return['REVIEWER'][$faculty_id]['REVIEW_STATE'] 					= 'UNABLE';
					$return['HAS_A_UNABLE']												= "YES";
					$return['REVIEWER'][$faculty_id]['FACULTY_ID'] 						= $faculty_id;
					$return['REVIEWER'][$faculty_id]['NAME'] 							= $rowAllotmentDetails['facultyTitle'] . " " . $rowAllotmentDetails['facultyFirstName'] . " " . $rowAllotmentDetails['facultyMiddleName'] . " " . $rowAllotmentDetails['facultyLastName'] . " ";
					$return['REVIEWER'][$faculty_id]['TOTAL']							= 'Unable to Review';
					$return['REVIEWER'][$faculty_id]['AVERAGE']							= 'Unable to Review';
				} else {
					$return['REVIEWER'][$faculty_id]['REVIEW_STATE'] 					= 'NOTREVIEW';
					$return['REVIEWER'][$faculty_id]['FACULTY_ID'] 						= $faculty_id;
					$return['REVIEWER'][$faculty_id]['NAME'] 							= $rowAllotmentDetails['facultyTitle'] . " " . $rowAllotmentDetails['facultyFirstName'] . " " . $rowAllotmentDetails['facultyMiddleName'] . " " . $rowAllotmentDetails['facultyLastName'] . " ";
					$return['REVIEWER'][$faculty_id]['TOTAL']							= 'Not Reviewed yet';
					$return['REVIEWER'][$faculty_id]['AVERAGE']							= 'Not Reviewed yet';
				}
			}
		}
	}

	return $return;
}

function getReviewUserAllotmentDetailsArray($userId)
{
	global $cfg, $mycms;

	$return = array();

	$sqlAllotmentDetails					= array();
	$sqlAllotmentDetails['QUERY'] 			= " SELECT *
												  FROM " . _DB_ABSTRACT_ALLOTMENT_ . "  
												 WHERE review_user_id = '" . $userId . "'";

	$resultAllotmentDetails         		= $mycms->sql_select($sqlAllotmentDetails);
	if ($resultAllotmentDetails) {
		$cntr = 0;
		foreach ($resultAllotmentDetails as $i => $rowAllotmentDetails) {
			$abstractDetailsArray 			= getAbstractDetailsArray($rowAllotmentDetails['abstract_id']);

			$return['ABSTRACTS'][$cntr] 	= $abstractDetailsArray;

			if ($abstractDetailsArray['MARKS']['REVIEWER'][$userId]['REVIEW_STATE'] == 'UNABLE') {
				$return['HAS_A_UNABLE'] = 'YES';
			}
			$cntr++;
		}
	}

	return $return;
}


function delegateAbstractDetails($userId)
{
	global $cfg, $mycms;
	$resultAbstractDetails = array();
	$searchCondition = "";
	if ($userId != '') {

		$searchCondition    .= " AND abstractRequest.applicant_id =" . $userId . "";

		$sql = abstractDetailsQuerySet("", $searchCondition, "");
		$result         = $mycms->sql_select($sql, false);
		foreach ($result as $i => $rowAbstractDetails) {
			$resultAbstractDetails['RAW'] 				= $rowAbstractDetails;
			$resultAbstractDetails['SUBMISSION_CODE'] 	= $rowAbstractDetails['abstract_submition_code'];
			$resultAbstractDetails['SUBMISSION_DATE'] 	= $rowAbstractDetails['created_dateTime'];
			$resultAbstractDetails['TOPIC'] 			= $rowAbstractDetails['abstract_topic'];
			$resultAbstractDetails['PARENT_TYPE'] 		= $rowAbstractDetails['abstract_parent_type'];
			$resultAbstractDetails['CHILD_TYPE'] 		= $rowAbstractDetails['abstract_child_type'];
			$resultAbstractDetails['CONTENT']			= getAbstractContent($rowAbstractDetails['id']);
		}
	}
	return $resultAbstractDetails;
}


function delegateAbstractDetailsSummery($userId)
{
	global $cfg, $mycms;
	$resultAbstractDetails = array();
	if ($userId != '') {
		$sqlFetch			  =	array();
		$sqlFetch['QUERY']	  = "SELECT abstractRequest.id,abstractTopic.abstract_topic,abstractRequest.abstract_submition_code,abstractRequest.abstract_parent_type,						 abstractRequest.abstract_category
								   	FROM " . _DB_ABSTRACT_REQUEST_ . " as abstractRequest
								   	LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic ON abstractRequest.abstract_topic_id = abstractTopic.id 
								   	AND abstractRequest.status = 'A'
								  	WHERE abstractRequest.applicant_id = ? AND abstractRequest.status = 'A'";
		$sqlFetch['PARAM'][]   = array('FILD' => 'applicant_id',  			'DATA' => $userId,  'TYP' => 's');
		$result = $mycms->sql_select($sqlFetch, false);
		$resultAbstractDetails 				   = $result[0];
	}
	return $resultAbstractDetails;
}

function delegateAbstractDetailsSummeryWithoutTopic($userId)
{
	global $cfg, $mycms;
	$resultAbstractDetails = array();
	if ($userId != '') {
		$sqlFetch			  =	array();
		$sqlFetch['QUERY']	  = "SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
								  	WHERE applicant_id = ? AND status = 'A'";
		$sqlFetch['PARAM'][]   = array('FILD' => 'applicant_id',  			'DATA' => $userId,  'TYP' => 's');
		$result = $mycms->sql_select($sqlFetch, false);
		//$resultAbstractDetails 				   = $result[0];
	}
	return $result;
}

function getAbstractCategoryName($cat_id)
{
	global $cfg,$mycms;
	$sqlAbstractTopic              =    array();
	$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                              WHERE `status` ='A' AND `id` ='" . $cat_id . "'";
	$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
	return $resultAbstractTopic[0]['category'];
}
