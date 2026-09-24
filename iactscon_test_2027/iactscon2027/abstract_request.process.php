<?php
include_once('includes/frontend.init.php');
include_once('includes/function.abstract.php');
$act          = $_REQUEST['act'];

// category wise topic listing by weavers start
if (isset($_POST['action']) && !empty($_POST['topic']) && $_POST['action'] === 'generateTopic') {
	$abstract_topic = '';
	$sql  			  = array();
	$sql['QUERY']     = " SELECT * 
								FROM " . _DB_ABSTRACT_TOPIC_ . " 
							   	WHERE `status` = ?
								AND `category` = ?
								ORDER BY `abstract_topic` ASC";
	$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'category',   'DATA' => trim($_POST['topic']), 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql);
	if ($resultAbstractType) {
		$abstract_topic .= "<option value=''>-- Select Topic --</option>";
		foreach ($resultAbstractType as $key => $value) {
			// $topicData = $value['id'] . "-" . $value['abstract_topic'];
			$topicData = $value['id'];
			$abstract_topic .= "<option value='" . $topicData . "'>" . $value['abstract_topic'] . "</option>";
		}

		echo $abstract_topic;
	} else {
		echo 'empty';
	}

	exit;
}

if (isset($_POST['action']) && !empty($_POST['cat_id']) && $_POST['action'] === 'generateCatSubTopic') {
	$abstract_topic = '';
	$sql  			  = array();
	$sql['QUERY']     = " SELECT * 
								FROM " . _DB_ABSTRACT_TOPIC_ . " 
							   	WHERE `status` = ?
								AND `category` = ?
								AND `sub_category` = ?
								ORDER BY `abstract_topic` ASC";
	$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'category',   'DATA' => trim($_POST['cat_id']), 'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'sub_category',   'DATA' => trim($_POST['submission_id']), 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql);
	if ($resultAbstractType) {
		$abstract_topic .= "<option value=''>-- Select Topic --</option>";
		foreach ($resultAbstractType as $key => $value) {
			$topicData = $value['id'] . "-" . $value['abstract_topic'];
			// $topicData = $value['id'];
			$abstract_topic .= "<option value='" . $topicData . "'>" . $value['abstract_topic'] . "</option>";
		}
		echo $abstract_topic;
	} else {
		echo 'empty';
	}
	exit;
}

if (isset($_POST['action']) && !empty($_POST['cat_id']) && $_POST['action'] === 'generateSubSubTopic') {
	$abstract_topic = '';
	$sql  			  = array();
	$sql['QUERY']     = " SELECT * 
								FROM " . _DB_ABSTRACT_TOPIC_ . " 
							   	WHERE `status` = ?
								AND `category` = ?
								AND `sub_category` = ?
								AND `sub_sub_category` = ?
								ORDER BY `abstract_topic` ASC";
	$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'category',   'DATA' => trim($_POST['cat_id']), 'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'sub_category',   'DATA' => trim($_POST['subcat_id']), 'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'sub_sub_category',   'DATA' => trim($_POST['subsubcat_id']), 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql);
	if ($resultAbstractType) {
		$abstract_topic .= "<option value=''>-- Select Topic --</option>";
		foreach ($resultAbstractType as $key => $value) {
			$abstract_topic .= "<option value='" . $value['id'] . "'>" . $value['abstract_topic'] . "</option>";
		}
	}
	echo $abstract_topic;
	exit;
}

if (isset($_POST['action']) && !empty($_POST['cat_id']) && $_POST['action'] === 'checkSubCat') {

	echo 'user=' . $_POST['delegateId'];
	/*$abstract_topic = '';
		$sql  			  = array();
		$sql['QUERY']     = " SELECT * 
								FROM "._DB_ABSTRACT_TOPIC_." 
							   	WHERE `status` = ?
								AND `category` = ?
								AND `sub_category` = ?
								AND `sub_sub_category` = ?
								ORDER BY `abstract_topic` ASC";
		$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'category',   'DATA' => trim($_POST['cat_id']) , 'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'sub_category',   'DATA' => trim($_POST['subcat_id']) , 'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'sub_sub_category',   'DATA' => trim($_POST['subsubcat_id']) , 'TYP' => 's');
		$resultAbstractType = $mycms->sql_select($sql);
		if($resultAbstractType){
			$abstract_topic .="<option value=''>-- Select Topic --</option>";
			foreach ($resultAbstractType as $key => $value) {
				$abstract_topic .= "<option value='".$value['id']."'>".$value['abstract_topic']."</option>";
			}
		}
		echo $abstract_topic;*/
	exit;
}


// category wise topic listing by weavers end

switch ($act) {
	case 'abstractSubmission':
		$abstratcSubmission = abstratcSubmission($mycms, $cfg);
		$mycms->redirect("abstract.submission.notification.php?Submissionid=" . $abstratcSubmission);
		exit();
		break;
    case 'abstractSubmissionBack':
		$abstratcSubmission = abstratcSubmission($mycms, $cfg);
		$_SESSION['toaster'] = [
		'type' => 'success', // 'success' or 'error'
		'message' => 'Data Added successfully!' // dynamic message
		];
		$mycms->redirect("webmaster/abstract_submited.php");
		exit();
		break;

	case 'editAbstractFile':
		$details = editAbstratcSubmission($mycms, $cfg);
		$mycms->redirect("profile.php?menuId=" . ($details['abstract_parent_type'] == 'CASEREPORT' ? 'casereport_details' : 'abstract_details'));
		exit();
		break;
    case 'editAbstractFileBack':
		$details = editAbstractFileProcess();
		$_SESSION['toaster'] = [
		'type' => 'success', // 'success' or 'error'
		'message' => 'Data Updated successfully!' // dynamic message
		];
		$mycms->redirect("webmaster/abstract_submited.php");
		exit();
		break;
	case 'editAbstractFileFront':
    try {
        $details = editAbstractFileProcessFront();

        if (!empty($_POST['is_ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'status'   => 'success',
                'redirect' => 'abstract.updation.notification.php?Submissionid=' . $details
            ]);
            exit();
        }

        $mycms->redirect("abstract.updation.notification.php?Submissionid=" . $details);
        exit();

    } catch (Exception $e) {
        if (!empty($_POST['is_ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again.'
            ]);
            exit();
        }
        throw $e; // fall back to normal error handling for non-ajax requests
    }
    break;
	case 'caseReportSubmission':
		$casereportSubmission = caseReportSubmission($mycms, $cfg);
		$mycms->redirect("abstract.submission.notification.php?Submissionid=" . $casereportSubmission);
		exit();
		break;

	case 'editCaseReportFile':
		editCaseReportSubmission();
		$mycms->redirect("profile.php?menuId=casereport_details");
		exit();
		break;

	case 'getStateOptionList':
		generateStateOptionListProcess();
		exit();
		break;
		// abstract submission update from porfile by weavers start
	case 'abstractUpdate':
		updateAbstratcSubmission($mycms, $cfg);
		exit();
		break;
		// abstract submission update from porfile by weavers end		
		///////////////////////////////////////////////

	case 'getAbstractWordLimit':
		echo getAbstractWordLimitProcess();
		exit();
		break;

	case 'updateAbstract':
		editAbstratcSubmission();
		$mycms->redirect("abstract_list.php?m=Data updated successfully&abs=" . $_REQUEST['absStudyId']);
		exit();
		break;

	case 'editAbstractCase':
		abstractCaseEdit();
		$mycms->redirect("case_report_list.php?m= Case Study Data updated successfully");
		exit();
		break;

	case 'removeCase':
		$sqlUpdateAbstractDetails['QUERY'] = "UPDATE " . $cfg['DB.CASE.STUDY.FILE'] . "  
											 SET `status` = 'D'
										   WHERE `id` = '" . $_REQUEST['id'] . "'";
		$mycms->sql_update($sqlUpdateAbstractDetails, false);
		$mycms->redirect("profile.php");
		exit();
		break;

	case 'removeCaseFile':
		$sqlUpdateAbstractDetails['QUERY'] = "UPDATE " . $cfg['DB.CASE.STUDY.FILE'] . "  
											SET `status` = 'D'
										  WHERE `id` = '" . $_REQUEST['caseFileId'] . "'";
		$mycms->sql_update($sqlUpdateAbstractDetails, false);
		$mycms->redirect("casereport_edit_details.php?caseId=" . $mycms->encoded($_REQUEST['caseId']));
		exit();
		break;

	case 'uploadCaseReportFile':

		$abstratcSubmission = abstratcSubmission($mycms, $cfg);
		$mycms->redirect("abstract_notification.php?submission_code=" . $abstratcSubmission . "");
		exit();
		break;

	case 'getNominationByTopic':
		$topicId = $_REQUEST['topicId'];
		$catId = $_REQUEST['catId'];
		$sqlAbstractSubcat['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
										WHERE `related_category_id`='" . $catId . "' AND `related_topic_id`='".$topicId."' AND
										`status` = 'A' ORDER BY `id` ASC";

		$resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);
		$nomination_ids = array();
		if ($resultAbstractSubcat) {
			foreach ($resultAbstractSubcat as $key => $nomination) {
				array_push($nomination_ids, $nomination['id']);
			}
		}
		$nomination_ids = json_encode($nomination_ids);
		echo $nomination_ids;
}

function abstratcSubmission($mycms, $cfg)
{
	// FETCHING DELEGATE DETAILS
	include_once('includes/function.delegate.php');


	//echo '<pre>'; print_r($_REQUEST['abstract_author_email'][0]); 

	//echo 'COUNTDATA='. count($_REQUEST['abstract_author_email'][0]); die;
	$sqlProcessFlow          = array();	
	$sqlProcessFlow['QUERY'] = "SELECT * FROM "._DB_PROCESS_STEP_." 
									WHERE `id` = ?";
								
	$sqlProcessFlow['PARAM'][]  = array('FILD' => 'id', 'DATA' =>trim($_REQUEST['processId']), 'TYP' => 's');
	
	$resProcessFlow			= $mycms->sql_select($sqlProcessFlow);
	if($resProcessFlow)
	{
		$rowProcessFlow 	= $resProcessFlow[0];

		//echo '<pre>'; print_r($rowProcessFlow);
		
		$step1Data 		= unserialize($rowProcessFlow['step1']);
	}


	$applicantId							  = $_REQUEST['applicantId'];

	$rowUserDetails            		  		  = registrationDetailsQuery($_REQUEST['applicantId'], "");
	//  echo '<pre>'; print_r($rowUserDetails); die();

	$applicant_id                  			  = $rowUserDetails['id'];
	$applicant_registration_id 				  = $rowUserDetails['user_registration_id'];
	$applicant_initial_title                  = $rowUserDetails['user_title'];
	$applicant_first_name                     = addslashes(trim($rowUserDetails['user_first_name']));
	$applicant_middle_name                    = addslashes(trim($rowUserDetails['user_middle_name']));
	$applicant_last_name                      = addslashes(trim($rowUserDetails['user_last_name']));
	$applicant_full_name            		  = $applicant_initial_title . ". " . $applicant_first_name . " " . $applicant_middle_name . " " . $applicant_last_name;
	$applicant_email_id                       = $rowUserDetails['user_email_id'];
	$applicant_mobile_isd_code                = $rowUserDetails['user_mobile_isd_code'];
	$applicant_mobile_no                      = $rowUserDetails['user_mobile_no'];
	$applicant_phone_no              		  = $rowUserDetails['user_phone_no'];

	$applicantUniqueSequence				  = $rowUserDetails['user_unique_sequence'];

	$topicArr = explode('-', $step1Data['abstract_topic_id']);

	//echo '<pre>'; print_r($topicArr); die;

	// DECLARATION OF VARRIABLES

	//author or presenter details from abstract.user.entrypoint
	$abstract_presenter_email = trim($step1Data['abstract_author_email']);
	$abstract_presenter_first_name = addslashes(trim(strtoupper($step1Data['abstract_author_first_name'])));
	$abstract_author_middle_name = addslashes(trim(strtoupper($step1Data['abstract_author_middle_name'])));
	$abstract_presenter_last_name = addslashes(trim(strtoupper($step1Data['abstract_author_last_name'])));
	$abstract_presenter_address = addslashes(trim($step1Data['abstract_author_address']));
	$abstract_presenter_country = addslashes(trim($step1Data['abstract_author_country']));
	$abstract_presenter_state = trim($step1Data['abstract_author_state']);
	$abstract_presenter_city = addslashes(trim($step1Data['abstract_author_city']));
	$abstract_presenter_title = trim(strtoupper($step1Data['abstract_author_title']));
	$abstract_presenter_mobile = trim($step1Data['abstract_author_mobile']);
	$abstract_presenter_pincode = addslashes(trim($step1Data['abstract_author_pincode']));

	$author_name = addslashes(trim($step1Data['abstract_author_title'] . " " . $step1Data['abstract_author_first_name'] . " " . $step1Data['abstract_author_middle_name'] . " " . $step1Data['abstract_author_last_name']));

	$abstract_presenter_institute_name        = addslashes(trim(strtoupper($step1Data['abstract_presenter_institute_name'])));
	$abstract_presenter_department            = addslashes(trim((strtoupper(($step1Data['abstract_presenter_department'])))));
	$abstract_author_name                     = addslashes(trim(strtoupper($author_name)));
	$abstract_author_department               = addslashes(trim(strtoupper($step1Data['abstract_author_department'])));
	$abstract_author_institute_name           = addslashes(trim(strtoupper($step1Data['abstract_author_institute'])));
	$abstract_author_country             	  = addslashes(trim(strtoupper($step1Data['abstract_presenter_country'])));
	// echo "country= ".$abstract_author_country ;die;
	$abstract_author_state             		  = addslashes(trim(strtoupper($step1Data['abstract_presenter_state'])));
	$abstract_author_city             		  = addslashes(trim(strtoupper($step1Data['abstract_presenter_city'])));
	$abstract_author_phone_code				  = addslashes(trim(strtoupper($step1Data['abstract_author_phone_isd_code'])));
	$abstract_author_phone_no             	  = addslashes(trim(strtoupper($step1Data['abstract_author_phone_no'])));

	$abstract_topic_id                   	  = (!empty($step1Data['abstract_topic_id'])) ? ($topicArr[0]) : "0";
	$abstract_title                           = addslashes(trim($step1Data['abstract_title']));
	$abstract_study                           = addslashes(trim($step1Data['abstract_study']));

	$abstract_parent_type                     = addslashes(trim(strtoupper($step1Data['abstract_parent_type'])));
	$abstract_child_type                      = addslashes(trim(strtoupper($step1Data['abstract_child_type'])));

	$abstract_background                 	  = addslashes(trim($step1Data['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($step1Data['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($step1Data['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($step1Data['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($step1Data['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($step1Data['abstract_references']));
	$abstract_description   				  = addslashes(trim($step1Data['abstract_description']));
	$tags 									  = addslashes(trim($step1Data['report_data']));
	$abstract_category 						  = addslashes(trim($step1Data['abstract_category']));
	$isPresenter 						 = (!empty($step1Data['isPresenter'])) ? addslashes(trim($step1Data['isPresenter'])) : 'N';
	$abstract_references 						 = (!empty($step1Data['abstract_references'])) ? addslashes(trim($step1Data['abstract_references'])) : 'NULL';

	$fields1 						 = (!empty($step1Data['fields1'][0])) ? addslashes(trim($step1Data['fields1'][0])) : 'NULL';
	$fields2 						 = (!empty($step1Data['fields2'][0])) ? addslashes(trim($step1Data['fields2'][0])) : 'NULL';
	$fields3 						 = (!empty($step1Data['fields3'][0])) ? addslashes(trim($step1Data['fields3'][0])) : 'NULL';
	$fields4 						 = (!empty($step1Data['fields4'][0])) ? addslashes(trim($step1Data['fields4'][0])) : 'NULL';
	$fields5 						 = (!empty($step1Data['fields5'][0])) ? addslashes(trim($step1Data['fields5'][0])) : 'NULL';
	$fields6 						 = (!empty($step1Data['fields6'][0])) ? addslashes(trim($step1Data['fields6'][0])) : 'NULL';
	$fields7 						 = (!empty($step1Data['fields7'][0])) ? addslashes(trim($step1Data['fields7'][0])) : 'NULL';
	$fields8 						 = (!empty($step1Data['fields8'][0])) ? addslashes(trim($step1Data['fields8'][0])) : 'NULL';
	$fields9 						 = (!empty($step1Data['fields9'][0])) ? addslashes(trim($step1Data['fields9'][0])) : 'NULL';
	$fields10 						 = (!empty($step1Data['fields10'][0])) ? addslashes(trim($step1Data['fields10'][0])) : 'NULL';
	$fields11 						 = (!empty($step1Data['fields11'][0])) ? addslashes(trim($step1Data['fields11'][0])) : 'NULL';


	// $sqlUpdateDelegateDetails				  = array();
	// $sqlUpdateDelegateDetails['QUERY']        = "UPDATE " . _DB_USER_REGISTRATION_ . "  
	// 													SET `user_department` = ?, 
	// 														`user_institute_name` = ?															
	// 												  WHERE `id` = ?";

	// $sqlUpdateDelegateDetails['PARAM'][]   	  = array('FILD' => 'user_department',    'DATA' => $abstract_presenter_department,     'TYP' => 's');
	// $sqlUpdateDelegateDetails['PARAM'][]      = array('FILD' => 'user_institute_name', 'DATA' => $abstract_presenter_institute_name, 'TYP' => 's');
	// $sqlUpdateDelegateDetails['PARAM'][]      = array('FILD' => 'id',                 'DATA' => $applicant_id,                      'TYP' => 's');

	// $mycms->sql_update($sqlUpdateDelegateDetails, false);


	$sqlInsertAbstractProcess				  = array();
	$sqlInsertAbstractProcess['QUERY']        = "INSERT INTO " . _DB_ABSTRACT_REQUEST_ . " 
															SET `applicant_id` = '" . $applicant_id . "', 
																`applicant_registration_id` = '" . $applicant_registration_id . "', 
																`applicant_title` = '" . $applicant_initial_title . "',
																`abstract_topic_id` = '" . $abstract_topic_id . "', 
																`abstract_cat` = '" . $abstract_category . "', 
																`applicant_first_name` = '" . $applicant_first_name . "', 
																`applicant_middle_name` = '" . $applicant_middle_name . "', 
																`applicant_last_name` = '" . $applicant_last_name . "',
																`applicant_email_id` = '" . $applicant_email_id . "', 
																`applicant_mobile_isd_code` = '" . $applicant_mobile_isd_code . "',
																`applicant_mobile_no` = '" . $applicant_mobile_no . "',
																`applicant_phone_no` = '" . $applicant_phone_no . "', 

																`abstract_author_name` = '" . $abstract_author_name . "', 
																`abstract_author_email_id` = '" . $abstract_presenter_email . "', 
																`abstract_author_first_name` = '" . $abstract_presenter_first_name . "', 
															    `abstract_author_middle_name` = '" . $abstract_author_middle_name . "', 
																`abstract_author_last_name` = '" . $abstract_presenter_last_name . "', 
																`abstract_author_phone_no`='" . $abstract_author_phone_no . "',
																`abstract_author_address` = '" . $abstract_presenter_address . "', 
																`abstract_author_country_id` = '" . $abstract_presenter_country . "', 
																`abstract_author_state_id` = '" . $abstract_presenter_state . "', 
																`abstract_author_city` = '" . $abstract_presenter_city . "', 
																`abstract_author_title` = '" . $abstract_presenter_title . "',
																`abstract_author_pin`='" . $abstract_presenter_pincode . "',

																`abstract_author_department` = '" . $abstract_author_department . "', 
																`abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
																`abstract_author_phone_code` = '" . $abstract_author_phone_code . "',

																`isPresenter` = '" . $isPresenter . "', 
																`abstract_parent_type` = '" . $abstract_parent_type . "',
																`abstract_child_type` = '" . $abstract_child_type . "', 
																`abstract_title` = '" . $abstract_title . "',
																`abstract_study` = '" . $abstract_study . "',
																`abstract_background` = '" . $abstract_background . "', 
																`abstract_background_aims` = '" . $abstract_background_aims . "', 
																`abstract_material_methods` = '" . $abstract_material_methods . "', 
																`abstract_results` = '" . $abstract_results . "', 
																`abstract_conclution` = '" . $abstract_conclusion . "', 
																`abstract_references` = '" . $abstract_references . "', 
																`abstract_description` = '" . $abstract_description . "',
																`fields1` = '" . $fields1 . "',
																`fields2` = '" . $fields2 . "',
																`fields3` = '" . $fields3 . "',
																`fields4` = '" . $fields4 . "',
																`fields5` = '" . $fields5 . "',
																`fields6` = '" . $fields6 . "',
																`fields7` = '" . $fields7 . "', 
																`fields8` = '" . $fields8 . "', 
																`fields9` = '" . $fields9 . "', 
																`fields10` = '" . $fields10 . "', 
																`fields11` = '" . $fields11 . "', 
																`tags` = '" . $tags . "',
																`status` = 'A',
																`created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																`created_sessionId` = '" . session_id() . "', 
																`created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
	//  echo '<pre>'; print_r($sqlInsertAbstractProcess); die();													   
	$lastInsertedAbstractId                   = $mycms->sql_insert($sqlInsertAbstractProcess, false);

	$_SESSION['ABSTRACT.SUBMISSION.ID']  	  = $lastInsertedAbstractId;

	$sqlUpdateUserDetails					  = array();
	$sqlUpdateUserDetails['QUERY']     	      = "UPDATE " . _DB_USER_REGISTRATION_ . "   
													SET `isAbstract` = ?
												  WHERE `id` = ?";

	$sqlUpdateUserDetails['PARAM'][]   = array('FILD' => 'isAbstract',    'DATA' => 'Y',     'TYP' => 's');
	$sqlUpdateUserDetails['PARAM'][]   = array('FILD' => 'id',            'DATA' => $applicant_id,                'TYP' => 's');

	$mycms->sql_update($sqlUpdateUserDetails, false);



	$abstract_submition_code                  = $mycms->getRandom(4, 'num') . number_pad($lastInsertedAbstractId, 4);

	$sqlUpdateAbstractProcess				  = array();
	$sqlUpdateAbstractProcess['QUERY']        = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "    
														SET `abstract_submition_code` = ? 
													  WHERE `id` = ?";

	$sqlUpdateAbstractProcess['PARAM'][]   = array('FILD' => 'abstract_submition_code',    'DATA' => $abstract_submition_code,     'TYP' => 's');
	$sqlUpdateAbstractProcess['PARAM'][]   = array('FILD' => 'id',                         'DATA' => $lastInsertedAbstractId,       'TYP' => 's');

	$mycms->sql_update($sqlUpdateAbstractProcess, false);

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `isConference` 
													   FROM " . _DB_USER_REGISTRATION_ . "
													 WHERE `id` = ?";

	$sqlSelectUser['PARAM'][]   = array('FILD' => 'id',                         'DATA' => $applicant_id,       'TYP' => 's');

	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);
	$isConference         = $resultSelectUser[0]['isConference'];




	$abstractCoAuthorArray = $step1Data['abstract_coauthor_name'];


	 if (!empty($step1Data['abstract_coauthor_first_name'][0])) {

		foreach ($step1Data['abstract_coauthor_first_name'] as $k => $val) {
			$abstract_coauthor_email = $step1Data['abstract_coauthor_email'][$k];
			$abstract_coauthor_first_name = $step1Data['abstract_coauthor_first_name'][$k];
			$abstract_coauthor_middle_name = $step1Data['abstract_coauthor_middle_name'][$k];
			$abstract_coauthor_last_name = $step1Data['abstract_coauthor_last_name'][$k];
			$abstract_coauthor_address = $step1Data['abstract_coauthor_address'][$k];

			$abstract_coauthor_country_id = $step1Data['abstract_coauthor_country'][$k];
			$abstract_coauthor_state_id = $step1Data['abstract_coauthor_state'][$k];
			$abstract_coauthor_city_name = $step1Data['abstract_coauthor_city'][$k];
			$abstract_coauthor_phone_code = $step1Data['abstract_coauthor_usd_code'][$k];

			$abstract_coauthor_phone_no = $step1Data['abstract_coauthor_mobile'][$k];
			$abstract_coauthor_title = trim(strtoupper($step1Data['abstract_coauthor_title'][$k]));
			$abstract_coauthor_pincode = $step1Data['abstract_coauthor_pincode'][$k];
			$abstract_coauthor_institute = $step1Data['abstract_coauthor_institute'][$k];
			$abstract_coauthor_department = $step1Data['abstract_coauthor_department'][$k];

			$abstract_coauthor_name = $abstract_coauthor_first_name . " ". $abstract_coauthor_middle_name . " " . $abstract_coauthor_last_name;
			if (!empty($abstract_coauthor_email) || !empty($abstract_coauthor_first_name) || !empty($abstract_coauthor_last_name) || !empty($abstract_coauthor_country_id) || !empty($abstract_coauthor_state_id) || !empty($abstract_coauthor_city_name) || !empty($abstract_coauthor_phone_no) || !empty($abstract_coauthor_title) || !empty($abstract_coauthor_pincode)) {
				$sqlInsertCoAuthor				  = array();
				$sqlInsertCoAuthor['QUERY']       = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . "  
															 SET `abstract_id` = ?, 
																 `abstract_coauthor_first_name` = ?,
																`abstract_coauthor_middle_name` = ?,
																 `abstract_coauthor_last_name` = ?,
																 `abstract_coauthor_title` = ?,
																 `abstract_coauthor_email` = ?,
																 `abstract_coauthor_name` = ?,
																 `abstract_coauthor_address` = ?,
																 `abstract_coauthor_country_id` = ?,
																 `abstract_coauthor_state_id` = ?,
																 `abstract_coauthor_city_name` = ?,
																 `abstract_coauthor_phone_code` = ?,
																 `abstract_coauthor_phone_no` = ?,
																 `abstract_coauthor_pincode` = ?,  
																 `abstract_coauthor_institute_name` = ?,  
																 `abstract_coauthor_department` = ?,  
																 `status` = ?,
																 `created_ip` =?, 
																 `created_sessionId` = ?, 
																 `created_dateTime` = ?";

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_id',                      'DATA' => $lastInsertedAbstractId,               'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_first_name',           'DATA' => $abstract_coauthor_first_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_middle_name',           'DATA' => $abstract_coauthor_middle_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_last_name',           'DATA' => $abstract_coauthor_last_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_title',           'DATA' => $abstract_coauthor_title,           'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_email',           'DATA' => $abstract_coauthor_email,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_name',           'DATA' => $abstract_coauthor_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_address',     'DATA' => $abstract_coauthor_address,        'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_country_id',     'DATA' => $abstract_coauthor_country_id,        'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_state_id',       'DATA' => $abstract_coauthor_state_id,          'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_city_name',      'DATA' => $abstract_coauthor_city_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_phone_code',      'DATA' => $abstract_coauthor_phone_code,           'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_phone_no',       'DATA' => $abstract_coauthor_phone_no,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_pincode',       'DATA' => $abstract_coauthor_pincode,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_institute_name',       'DATA' => $abstract_coauthor_institute,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_department',       'DATA' => $abstract_coauthor_department,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'status',                           'DATA' => 'A',                                   'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_ip',                       'DATA' => $_SERVER['REMOTE_ADDR'],               'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_sessionId',                'DATA' => session_id(),                          'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_dateTime',                 'DATA' => date('Y-m-d H:i:s'),                   'TYP' => 's');

				//echo '<pre>'; print_r($sqlInsertCoAuthor); die;

				$mycms->sql_insert($sqlInsertCoAuthor, false);
			}
		}
	 }

	if (isset($step1Data['upload_temp_doc_fileName'])) {


		$abstractFile                             = addslashes($_FILES['upload_abstract_file']['name']);
		$abstractFileTempFile                     = $_FILES['upload_abstract_file']['tmp_name'];
		$ext							 		  = pathinfo($abstractFile, PATHINFO_EXTENSION);
		$abstractFileFileName             		  = 'DOC_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															SET `abstract_file` = '" .$step1Data['upload_temp_doc_fileName'] . "',
																`abstract_original_file_name` = '" .$step1Data['upload_temp_doc_fileName']. "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";


			$mycms->sql_update($sqlAbstractFile, false);
		}
	} else {
			// INSERTING CATEGORY FILE
		$abstractFile                             =$step1Data['category_required_file'];
		$abstractFileTempFile                     =$step1Data['category_required_file'];
		$ext							 		  = pathinfo($abstractFile, PATHINFO_EXTENSION);
		// $abstractFileFileName             		  = 'DOC_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;
		$abstractFileFileName             		  = $step1Data['category_required_file'];

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															SET `category_required_file` = '" . $abstractFileFileName . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}



		// INSERTING ABSTRACT FILE
		$abstractFile                             =$step1Data['upload_abstract_file'];
		$abstractFileTempFile                     =$step1Data['upload_abstract_file'];
		$ext							 		  = pathinfo($abstractFile, PATHINFO_EXTENSION);
		// $abstractFileFileName             		  = 'DOC_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;
		$abstractFileFileName             		  = $step1Data['upload_abstract_file'];

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															SET `abstract_file` = '" . $abstractFileFileName . "',
																`abstract_original_file_name` = '" . $abstractFile . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}


		//Consent File Upload Start------------//

		$abstractFileConsent                             = $step1Data['upload_consent_abstract_file'];
		$abstractFileTempFile                     =$step1Data['upload_consent_abstract_file'];
		$ext							 		  = pathinfo($abstractFileConsent, PATHINFO_EXTENSION);
		$abstractFileFileName             		  = $step1Data['upload_consent_abstract_file'];

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															SET `abstract_consent_file` = '" . $abstractFileFileName . "',
																`abstract_consent_original_file_name` = '" . $abstractFileConsent . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}

		//Consent File Upload End------------//

	}

	//	INSERTING ABSTRACT VIDEO
	if (isset($step1Data['upload_temp_vdo_fileName'])) {
		$abstractVideoFile               = $step1Data['upload_original_vdo_fileName'];
		$abstractVideoFileTempFile       = $step1Data['upload_temp_vdo_fileName'];
		$ext							 = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);

		if (trim($ext) != '') {
			$newFileName    = 'VIDEO_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

			$src 			= $cfg['FILES.TEMP'] . $abstractVideoFileTempFile;

			$target			= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

			if (rename($src, $target)) {
				$sqlAbstractFile				  = array();
				$sqlAbstractFile['QUERY']         = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET `abstract_video_file` = '" . $newFileName . "',
																`abstract_original_file_name` = '" . $abstractVideoFile . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

				$mycms->sql_update($sqlAbstractFile, false);
			}
		}
	} else {
		$abstractVideoFile                             = $_FILES['upload_abstract_video']['name'];
		$abstractVideoFileTempFile                     = $_FILES['upload_abstract_video']['tmp_name'];
		$ext							 			   = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);
		$abstractVideoFileFileName                     = 'VIDEO_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractVideoFileTempFile != "") {
			$abstractVideoFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractVideoFileFileName;

			chmod($abstractVideoFilePath, 0777);
			copy($abstractVideoFileTempFile, $abstractVideoFilePath);
			chmod($abstractVideoFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']         = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET `abstract_video_file` = '" . $abstractVideoFileFileName . "' 
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	if (isset($step1Data['award_request'])) {
		$awardId=$step1Data['award_request'];
		// foreach ($_REQUEST['award_request'] as $awardId => $nomination) {
		// 	if ($nomination == 'Y') {
				$awardFileTempFile                     =$step1Data['upload_nomination_file'];

				$sqlAbstractAward			  =	array();
				$sqlAbstractAward['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
													  WHERE `id` = ?";
				$sqlAbstractAward['PARAM'][]  = array('FILD' => 'is', 'DATA' => $awardId,  'TYP' => 's');
				$resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
				$rowAbstractAward	 = $resultAbstractAward[0];

				$sql				  = array();
				$sql['QUERY']         = "INSERT INTO " . _DB_AWARD_REQUEST_ . " 
													 SET `submission_id` = '" . $lastInsertedAbstractId . "', 
														 `submission_code` = '" . $abstract_submition_code . "', 
														 `abstract_parent_type` = '" . $abstract_parent_type . "',
														 `abstract_child_type` = '" . $abstract_child_type . "', 
														 `abstract_topic_id` = '" . $abstract_topic_id . "', 
														 `applicant_id` = '" . $applicant_id . "', 
														 `applicant_name` = '" . $applicant_full_name . "',
														 `award_id` = '" . $awardId . "', 
														 `award_title` = '" . $rowAbstractAward['award_name'] . "',	
														 `upload_nomination_file` = '" . $awardFileTempFile. "',														 
														 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `created_sessionId` = '" . session_id() . "', 
														 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
				$awardRequestId       = $mycms->sql_insert($sql, false);
		// 	}
		// }
	}

	if ($isConference == "Y") {
		//abstract_free_paper_request($applicant_id, $lastInsertedAbstractId, $operation='SEND');

	} else {
		// abstract_free_paper_request_nonregistered($applicant_id, $lastInsertedAbstractId, $operation='SEND');

	}

	abstract_submission_message($applicant_id, $lastInsertedAbstractId, 'SEND');

	return $abstract_submition_code;
}

function editAbstratcSubmission($mycms, $cfg)
{
	global $cfg, $mycms;

	$sqlFetchUserDetails            		  = registrationDetailsQuerySet($_REQUEST['delegateId'], "");
	$resultUserDetails        	    		  = $mycms->sql_select($sqlFetchUserDetails);
	$rowUserDetails       	    			  = $resultUserDetails[0];
	$absreqId 								  = $_REQUEST['abstract_id'];
	$applicant_id                  			  = $rowUserDetails['id'];
	$applicant_registration_id 				  = $rowUserDetails['user_registration_id'];
	$applicant_initial_title                  = $rowUserDetails['user_title'];
	$applicant_first_name                     = addslashes(trim($rowUserDetails['user_first_name']));
	$applicant_middle_name                    = addslashes(trim($rowUserDetails['user_middle_name']));
	$applicant_last_name                      = addslashes(trim($rowUserDetails['user_last_name']));
	$applicant_full_name            		  = $applicant_initial_title . ". " . $applicant_first_name . " " . $applicant_middle_name . " " . $applicant_last_name;
	$applicant_email_id                       = $rowUserDetails['user_email_id'];
	$applicant_mobile_isd_code                = $rowUserDetails['user_mobile_isd_code'];
	$applicant_mobile_no                      = $rowUserDetails['user_mobile_no'];
	$applicant_phone_no              		  = $rowUserDetails['user_phone_no'];
	$applicantUniqueSequence                  = $rowUserDetails['user_unique_sequence'];

	// DECLARATION OF VARRIABLES
	$abstract_author_name                     = addslashes(trim(strtoupper($_REQUEST['abstract_author_name'])));
	$abstract_author_department               = addslashes(trim(strtoupper($_REQUEST['abstract_author_department'])));
	$abstract_author_institute_name           = addslashes(trim(strtoupper($_REQUEST['abstract_author_institiute'])));
	$abstract_author_country             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_country'])));
	$abstract_author_state             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_state'])));
	$abstract_author_city             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_city'])));
	$abstract_author_phone_code               = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_code'])));
	$abstract_author_phone_no             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_no'])));

	$abstract_topic_id                   	  = $_REQUEST['abstract_topic_id'];
	$abstract_title                           = addslashes(trim(strtoupper($_REQUEST['abstract_title'])));
	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));

	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));

	$sqlUpdateAbstractDetails['QUERY']        = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
														SET `abstract_topic_id` = '" . $abstract_topic_id . "', 
															`abstract_title` = '" . $abstract_title . "', 
															`abstract_background` = '" . $abstract_background . "', 
															`abstract_background_aims` = '" . $abstract_background_aims . "', 
															`abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
															`abstract_author_department` = '" . $abstract_author_country . "', 
															`abstract_material_methods` = '" . $abstract_material_methods . "', 
															`abstract_results` = '" . $abstract_results . "', 
															`abstract_conclution` = '" . $abstract_conclusion . "', 
															`abstract_author_name` = '" . $abstract_author_name . "', 
															`abstract_author_department` = '" . $abstract_author_department . "', 
															`abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
															`abstract_author_state_id` = '" . $abstract_author_state . "', 
															`abstract_author_city` = '" . $abstract_author_city . "', 
															`abstract_author_country_id` = '" . $abstract_author_country . "', 
															`abstract_author_phone_code` = '" . $abstract_author_phone_code . "',															
															`abstract_author_phone_no` = '" . $abstract_author_phone_no . "',
															`abstract_references` = '" . $abstract_references . "',
															`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
															`modified_sessionId` = '" . session_id() . "', 
															`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
													  WHERE `id` = '" . $absreqId . "'";
	$mycms->sql_update($sqlUpdateAbstractDetails, false);

	$sqlUpdateUser['QUERY']             	  = "UPDATE " . _DB_USER_REGISTRATION_ . "  
														SET `user_institute_name` = '" . $_REQUEST['presenter_institute_name'] . "',
															`user_department` = '" . $_REQUEST['presenter_institute_department'] . "'
													  WHERE `id` = '" . $applicant_id . "'";
	$mycms->sql_update($sqlUpdateUser, false);


	$idCollection 			  = array();

	$abstractCoAuthorIdArray  = $_REQUEST['abstract_coauthor_id'];

	foreach ($abstractCoAuthorIdArray as $keyCoAuthor => $abstractCoAuthorId) {
		$abstract_coauthor_name_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_name'][$keyCoAuthor])));
		$abstract_coauthor_department_val     = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_department'][$keyCoAuthor])));
		$abstract_coauthor_institute_name_val = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute'][$keyCoAuthor])));
		$abstract_coauthor_country_val        = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_country'][$keyCoAuthor])));
		$abstract_coauthor_state_val          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_state'][$keyCoAuthor])));
		$abstract_coauthor_city_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_city'][$keyCoAuthor])));
		$abstract_coauthor_phone_no_val       = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_phone_no'][$keyCoAuthor])));

		if ($abstract_coauthor_name_val != "") {
			$sqlInsertCoAuthor	= array();
			if ($abstractCoAuthorId == '') {
				// INSERT PROCESS
				$sqlInsertCoAuthor['QUERY']                = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . " 
																		 SET `abstract_id` = '" . $absreqId . "', 															 	 
																			 `abstract_coauthor_name` = '" . $abstract_coauthor_name_val . "',
																			 `abstract_coauthor_department` = '" . $abstract_coauthor_department_val . "',
																			 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_name_val . "',
																			 `abstract_coauthor_country_id` = '" . $abstract_coauthor_country_val . "',
																			 `abstract_coauthor_state_id` = '" . $abstract_coauthor_state_val . "',
																			 `abstract_coauthor_city_name` = '" . $abstract_coauthor_city_val . "',
																			 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_phone_no_val . "', 
																			 `status` = 'A',
																			 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																			 `created_sessionId` = '" . session_id() . "', 
																			 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";

				$idCollection[] = $mycms->sql_insert($sqlInsertCoAuthor, false);
			} else {
				$sqlInsertCoAuthor['QUERY']                = "UPDATE " . _DB_ABSTRACT_COAUTHOR_ . " 
																	 SET `abstract_coauthor_name` = '" . $abstract_coauthor_name_val . "',															 	 
																		 `abstract_coauthor_department` = '" . $abstract_coauthor_department_val . "',
																		 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_name_val . "',
																		 `abstract_coauthor_country_id` = '" . $abstract_coauthor_country_val . "',
																		 `abstract_coauthor_state_id` = '" . $abstract_coauthor_state_val . "',
																		 `abstract_coauthor_city_name` = '" . $abstract_coauthor_city_val . "',
																		 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_phone_no_val . "', 
																		 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																		 `modified_sessionId` = '" . session_id() . "', 
																		 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
																   WHERE `id` = '" . $abstractCoAuthorId . "'
																	 AND `abstract_id` = '" . $absreqId . "' ";
				$mycms->sql_update($sqlInsertCoAuthor, false);
				$idCollection[] = $abstractCoAuthorId;
			}
		}
	}

	$sqlCoAuthorDtls  = array();
	$sqlCoAuthorDtls['QUERY']  = " SELECT id 
										 FROM " . _DB_ABSTRACT_COAUTHOR_ . "
									    WHERE `abstract_id` = '" . $absreqId . "' 
										  AND `status` = 'A'";
	$coAuthorDetails  		   = $mycms->sql_select($sqlCoAuthorDtls);
	foreach ($coAuthorDetails as $keyRow => $rowFetchcoAuthorDetails) {
		if (!in_array($rowFetchcoAuthorDetails['id'], $idCollection)) {
			$sqlRemoveCoAuthor			= array();
			$sqlRemoveCoAuthor['QUERY'] = "UPDATE " . _DB_ABSTRACT_COAUTHOR_ . "  
												  SET `status` = 'D' 														
											    WHERE `id` = '" . $rowFetchcoAuthorDetails['id'] . "'
											      AND `abstract_id` = '" . $absreqId . "' ";
			$mycms->sql_update($sqlRemoveCoAuthor, false);
		}
	}

	//	INSERTING ABSTRACT FILE
	if (isset($_REQUEST['upload_temp_doc_fileName'])) {
		$abstractFile               	 = $_REQUEST['upload_original_doc_fileName'];
		$abstractFileTempFile       	 = $_REQUEST['upload_temp_doc_fileName'];
		$ext							 = pathinfo($abstractFile, PATHINFO_EXTENSION);

		if (trim($ext) != '') {
			$newFileName                     = 'DOC_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

			$src 	= $cfg['FILES.TEMP'] . $abstractFileTempFile;

			$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

			if (rename($src, $target)) {
				$sqlAbstractFile				  	  = array();
				$sqlAbstractFile['QUERY'] 			  = " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																 SET `abstract_file` = '" . $newFileName . "' ,
																	 `abstract_original_file_name` = '" . $abstractFile . "'
															   WHERE `id` = '" . $absreqId . "'";

				$mycms->sql_update($sqlAbstractFile, false);
			}
		}
	} else {
		// INSERTING ABSTRACT FILE
		$abstractFile                             = $_FILES['upload_abstract_file']['name'];
		$abstractFileTempFile                     = $_FILES['upload_abstract_file']['tmp_name'];
		$ext							 		   = pathinfo($abstractFile, PATHINFO_EXTENSION);
		$abstractFileFileName                     = 'DOC_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile['QUERY'] 			  = " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															 SET `abstract_file` = '" . $abstractFileFileName . "' ,
																 `abstract_original_file_name` = '" . $abstractFile . "'
														   WHERE `id` = '" . $absreqId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	//	INSERTING ABSTRACT VIDEO
	if (isset($_REQUEST['upload_temp_vdo_fileName'])) {
		if (trim($_REQUEST['upload_temp_vdo_fileName']) != '') {
			$abstractVideoFile               = $_REQUEST['upload_original_vdo_fileName'];
			$abstractVideoFileTempFile       = $_REQUEST['upload_temp_vdo_fileName'];
			$ext							 = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);

			if (trim($ext) != '') {
				$newFileName                     = 'VIDEO_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

				$src 	= $cfg['FILES.TEMP'] . $abstractVideoFileTempFile;

				$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

				if (rename($src, $target)) {
					$sqlAbstractFile				  = array();
					$sqlAbstractFile['QUERY']         = "  UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																  SET `abstract_video_file` = '" . $newFileName . "' ,
																	  `abstract_original_file_name` = '" . $abstractVideoFile . "'
																WHERE `id` = '" . $absreqId . "'";

					$mycms->sql_update($sqlAbstractFile, false);
				}
			}
		}
	} else {
		$abstractVideoFile                             = $_FILES['upload_abstract_video']['name'];
		$abstractVideoFileTempFile                     = $_FILES['upload_abstract_video']['tmp_name'];
		$ext							 			   = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);
		$abstractVideoFileFileName                     = 'VIDEO_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractVideoFileTempFile != "") {
			$abstractVideoFilePath                     = $cfg['VIDEO.FILES.ABSTRACT.REQUEST'] . $abstractVideoFileFileName;

			chmod($abstractVideoFilePath, 0777);
			copy($abstractVideoFileTempFile, $abstractVideoFilePath);
			chmod($abstractVideoFilePath, 0777);

			$sqlAbstractFile['QUERY']                  = " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																  SET `abstract_video_file` = '" . $abstractVideoFileFileName . "' ,
														    		  `abstract_original_file_name` = '" . $abstractVideoFile . "'
																WHERE `id` = '" . $absreqId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	if (isset($_REQUEST['award_request'])) {
		$sql				  = array();
		$sql['QUERY']         = " UPDATE " . _DB_AWARD_REQUEST_ . " 
										 SET `status` = 'D',								 
											 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
											 `modified_sessionId` = '" . session_id() . "', 
											 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
									   WHERE `applicant_id` = '" . $applicant_id . "'";
		$mycms->sql_update($sql, false);

		$sql             = array();
		$sql['QUERY']    = "SELECT abstractRequest.*,
									   abstractTopic.abstract_topic AS abstract_topic,
									   
									   registeredDelegates.user_email_id,
									   registeredDelegates.user_unique_sequence,
									   registeredDelegates.user_registration_id,
									   registeredDelegates.user_full_name
								   
								  FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest 
								  
					   LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
									ON abstractRequest.abstract_topic_id = abstractTopic.id 
					   
					   LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
									ON abstractRequest.applicant_id = registeredDelegates.id 									
									
								 WHERE abstractRequest.status = 'A' 
								   AND abstractRequest.abstract_parent_type = 'ABSTRACT'
								   AND abstractRequest.tags = 'Abstract'
								   AND abstractRequest.id = '" . $absreqId . "'  ";

		$resultAbstractDetailsForAward 	= $mycms->sql_select($sql);

		$rowAbstractDetailsForAward		= $resultAbstractDetailsForAward[0];

		foreach ($_REQUEST['award_request'] as $awardId => $nomination) {
			if ($nomination == 'Y') {
				$sqlAbstractAward			  =	array();
				$sqlAbstractAward['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
													  WHERE `id` = ?";
				$sqlAbstractAward['PARAM'][]  = array('FILD' => 'is', 'DATA' => $awardId,  'TYP' => 's');
				$resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
				$rowAbstractAward	 = $resultAbstractAward[0];

				$sql				  = array();
				$sql['QUERY']         = "INSERT INTO " . _DB_AWARD_REQUEST_ . " 
													 SET `submission_id` = '" . $absreqId . "', 
														 `submission_code` = '" . $rowAbstractDetailsForAward['abstract_submition_code'] . "', 
														 `abstract_parent_type` = '" . $rowAbstractDetailsForAward['abstract_parent_type'] . "',
														 `abstract_child_type` = '" . $rowAbstractDetailsForAward['abstract_child_type'] . "', 
														 `abstract_topic_id` = '" . $rowAbstractDetailsForAward['abstract_topic_id'] . "', 
														 `applicant_id` = '" . $applicant_id . "', 
														 `applicant_name` = '" . $applicant_full_name . "',
														 `award_id` = '" . $awardId . "', 
														 `award_title` = '" . $rowAbstractAward['award_name'] . "',														 
														 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `created_sessionId` = '" . session_id() . "', 
														 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
				$awardRequestId       = $mycms->sql_insert($sql, false);
			}
		}
	}

	$sqlAbstractDetails['QUERY']    =  "SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
											 WHERE `id` = ?";
	$sqlAbstractDetails['PARAM'][]   = array('FILD' => 'id',         'DATA' => $absreqId,          'TYP' => 's');
	$resultViewAbstractDetails       = $mycms->sql_select($sqlAbstractDetails);
	$rowAbstract					 = $resultViewAbstractDetails[0];

	return $rowAbstract;
}

function caseReportSubmission($mycms, $cfg)
{
	// FETCHING DELEGATE DETAILS
	include_once('includes/function.delegate.php');

	$applicantId							  = $_REQUEST['applicantId'];

	$rowUserDetails            		  		  = registrationDetailsQuery($_REQUEST['applicantId'], "");

	$applicant_id                  			  = $rowUserDetails['id'];
	$applicant_registration_id 				  = $rowUserDetails['user_registration_id'];
	$applicant_initial_title                  = $rowUserDetails['user_title'];
	$applicant_first_name                     = $rowUserDetails['user_first_name'];
	$applicant_middle_name                    = $rowUserDetails['user_middle_name'];
	$applicant_last_name                      = $rowUserDetails['user_last_name'];
	$applicant_full_name            		  = $applicant_initial_title . ". " . $applicant_first_name . " " . $applicant_middle_name . " " . $applicant_last_name;
	$applicant_email_id                       = $rowUserDetails['user_email_id'];
	$applicant_mobile_isd_code                = $rowUserDetails['user_mobile_isd_code'];
	$applicant_mobile_no                      = $rowUserDetails['user_mobile_no'];
	$applicant_phone_no              		  = $rowUserDetails['user_phone_no'];

	$applicantUniqueSequence				  = $rowUserDetails['user_unique_sequence'];

	// DECLARATION OF VARRIABLES

	$abstract_presenter_institute_name        = addslashes(trim(strtoupper($_REQUEST['abstract_presenter_institute_name'])));
	$abstract_presenter_department            = addslashes(trim(strtoupper($_REQUEST['abstract_presenter_department'])));

	$abstract_author_name                     = addslashes(trim(strtoupper($_REQUEST['abstract_author_name'])));
	$abstract_author_department               = addslashes(trim(strtoupper($_REQUEST['abstract_author_department'])));
	$abstract_author_institute_name           = addslashes(trim(strtoupper($_REQUEST['abstract_author_institute_name'])));
	$abstract_author_country             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_country'])));
	$abstract_author_state             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_state_id'])));
	$abstract_author_city             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_city'])));
	$abstract_author_phone_code				  = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_isd_code'])));
	$abstract_author_phone_no             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_no'])));

	$abstract_topic_id                   	  = $_REQUEST['abstract_topic_id'];
	$abstract_title                           = addslashes(trim($_REQUEST['abstract_title']));
	$abstract_study                           = addslashes(trim($_REQUEST['abstract_study']));

	$abstract_parent_type                     = addslashes(trim(strtoupper($_REQUEST['abstract_parent_type'])));
	$abstract_child_type                      = addslashes(trim(strtoupper($_REQUEST['abstract_child_type'])));

	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));
	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));
	$tags 									  = addslashes(trim($_REQUEST['report_data']));

	// INSERTING DELEGATE REQUEST PROCESS		
	$sqlUpdateDelegateDetails				  = array();
	$sqlUpdateDelegateDetails['QUERY']        = "UPDATE " . _DB_USER_REGISTRATION_ . "  
														SET `user_department` = ?, 
															`user_institute_name` = ?															
													  WHERE `id` = ?";

	$sqlUpdateDelegateDetails['PARAM'][]   	  = array('FILD' => 'user_department',    'DATA' => $abstract_presenter_department,     'TYP' => 's');
	$sqlUpdateDelegateDetails['PARAM'][]      = array('FILD' => 'user_institute_name', 'DATA' => $abstract_presenter_institute_name, 'TYP' => 's');
	$sqlUpdateDelegateDetails['PARAM'][]      = array('FILD' => 'id',                 'DATA' => $applicant_id,                      'TYP' => 's');

	$mycms->sql_update($sqlUpdateDelegateDetails, false);

	// INSERTING ABSTRACT REQUEST PROCESS
	$sqlInsertAbstractProcess				  = array();
	$sqlInsertAbstractProcess['QUERY']        = "INSERT INTO " . _DB_ABSTRACT_REQUEST_ . " 
															 SET `applicant_id` = '" . $applicant_id . "', 
																 `applicant_registration_id` = '" . $applicant_registration_id . "', 
																 `applicant_title` = '" . $applicant_initial_title . "',
																 `abstract_topic_id` = '" . $abstract_topic_id . "', 
																 `applicant_first_name` = '" . $applicant_first_name . "', 
																 `applicant_middle_name` = '" . $applicant_middle_name . "', 
																 `applicant_last_name` = '" . $applicant_last_name . "',
																 `applicant_email_id` = '" . $applicant_email_id . "', 
																 `applicant_mobile_isd_code` = '" . $applicant_mobile_isd_code . "',
																 `applicant_mobile_no` = '" . $applicant_mobile_no . "',
																 `applicant_phone_no` = '" . $applicant_phone_no . "', 
																 `abstract_author_name` = '" . $abstract_author_name . "', 
																 `abstract_author_department` = '" . $abstract_author_department . "', 
																 `abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
																 `abstract_author_country_id` = '" . $abstract_author_country . "', 
																 `abstract_author_state_id` = '" . $abstract_author_state . "', 
																 `abstract_author_city` = '" . $abstract_author_city . "', 																 
																 `abstract_author_phone_code` = '" . $abstract_author_phone_code . "',																 
																 `abstract_author_phone_no` = '" . $abstract_author_phone_no . "', 
																 `abstract_parent_type` = '" . $abstract_parent_type . "',
																 `abstract_child_type` = '" . $abstract_child_type . "', 
																 `abstract_title` = '" . $abstract_title . "',
																 `abstract_study` = '" . $abstract_study . "',
																 `abstract_background` = '" . $abstract_background . "', 
																 `abstract_background_aims` = '" . $abstract_background_aims . "', 
																 `abstract_material_methods` = '" . $abstract_material_methods . "', 
																 `abstract_results` = '" . $abstract_results . "', 
																 `abstract_conclution` = '" . $abstract_conclusion . "', 
																 `abstract_description` = '" . $abstract_description . "', 
																 `tags` = '" . $tags . "',
																 `status` = 'A',
																 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																 `created_sessionId` = '" . session_id() . "', 
																 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
	$lastInsertedAbstractId                   = $mycms->sql_insert($sqlInsertAbstractProcess, false);

	$_SESSION['ABSTRACT.SUBMISSION.ID']  	  = $lastInsertedAbstractId;

	$sqlUpdateUserDetails					  = array();
	$sqlUpdateUserDetails['QUERY']     	      = "UPDATE " . _DB_USER_REGISTRATION_ . "   
													    SET `isAbstract` = ?
												      WHERE `id` = ?";

	$sqlUpdateUserDetails['PARAM'][]   = array('FILD' => 'isAbstract',    'DATA' => 'Y',     'TYP' => 's');
	$sqlUpdateUserDetails['PARAM'][]   = array('FILD' => 'id',            'DATA' => $applicant_id,                'TYP' => 's');

	$mycms->sql_update($sqlUpdateUserDetails, false);


	// UPDATING ABSTRACT SUBMITION CODE
	$abstract_submition_code                  = $mycms->getRandom(4, 'num') . number_pad($lastInsertedAbstractId, 4);

	$sqlUpdateAbstractProcess				  = array();
	$sqlUpdateAbstractProcess['QUERY']        = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "    
														SET `abstract_submition_code` = ? 
													  WHERE `id` = ?";

	$sqlUpdateAbstractProcess['PARAM'][]   = array('FILD' => 'abstract_submition_code',    'DATA' => $abstract_submition_code,     'TYP' => 's');
	$sqlUpdateAbstractProcess['PARAM'][]   = array('FILD' => 'id',                         'DATA' => $lastInsertedAbstractId,       'TYP' => 's');

	$mycms->sql_update($sqlUpdateAbstractProcess, false);

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `isConference` 
													   FROM " . _DB_USER_REGISTRATION_ . "
													 WHERE `id` = ?";

	$sqlSelectUser['PARAM'][]   = array('FILD' => 'id',                         'DATA' => $applicant_id,       'TYP' => 's');

	$resultSelectUser     = $mycms->sql_select($sqlSelectUser);
	$isConference         = $resultSelectUser[0]['isConference'];



	// INSERTING ABSTRACT COAUTHOR DETAILS
	$abstractCoAuthorArray                    = $_REQUEST['abstract_coauthor_name'];

	foreach ($abstractCoAuthorArray as $keyCoAuthor => $valCoAuthor) {
		$abstract_coauthor_name_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_name'][$keyCoAuthor])));
		$abstract_coauthor_department_val     = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_department'][$keyCoAuthor])));
		$abstract_coauthor_institute_name_val = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_name'][$keyCoAuthor])));
		$abstract_coauthor_country_val        = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_country'][$keyCoAuthor])));
		$abstract_coauthor_state_val          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_state'][$keyCoAuthor])));

		if ($abstract_coauthor_state_val == "") {
			$abstract_coauthor_state_val = 0;
		}

		$abstract_coauthor_city_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_city'][$keyCoAuthor])));
		$abstract_coauthor_phone_no_val       = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_phone_code'][$keyCoAuthor] . " " . $_REQUEST['abstract_coauthor_phone_no'][$keyCoAuthor])));

		if ($abstract_coauthor_name_val != "") {
			// INSERT PROCESS
			$sqlInsertCoAuthor				  = array();
			$sqlInsertCoAuthor['QUERY']       = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . "  
															 SET `abstract_id` = ?, 
																 `abstract_coauthor_name` = ?,
																 `abstract_coauthor_department` = ?,
																 `abstract_coauthor_institute_name` = ?,
																 `abstract_coauthor_country_id` = ?,
																 `abstract_coauthor_state_id` = ?,
																 `abstract_coauthor_city_name` = ?,
																 `abstract_coauthor_phone_no` = ?, 
																 `status` = ?,
																 `created_ip` =?, 
																 `created_sessionId` = ?, 
																 `created_dateTime` = ?";

			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_id',                      'DATA' => $lastInsertedAbstractId,               'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_name',           'DATA' => $abstract_coauthor_name_val,           'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_department',     'DATA' => $abstract_coauthor_department_val,     'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_institute_name', 'DATA' => $abstract_coauthor_institute_name_val, 'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_country_id',     'DATA' => $abstract_coauthor_country_val,        'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_state_id',       'DATA' => $abstract_coauthor_state_val,          'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_city_name',      'DATA' => $abstract_coauthor_city_val,           'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_phone_no',       'DATA' => $abstract_coauthor_phone_no_val,       'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'status',                           'DATA' => 'A',                                   'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_ip',                       'DATA' => $_SERVER['REMOTE_ADDR'],               'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_sessionId',                'DATA' => session_id(),                          'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_dateTime',                 'DATA' => date('Y-m-d H:i:s'),                   'TYP' => 's');

			$mycms->sql_insert($sqlInsertCoAuthor, false);
		}
	}

	//	INSERTING ABSTRACT FILE
	if (isset($_REQUEST['upload_temp_doc_fileName'])) {
		$abstractFile               	 = $_REQUEST['upload_original_doc_fileName'];
		$abstractFileTempFile       	 = $_REQUEST['upload_temp_doc_fileName'];
		$ext							 = pathinfo($abstractFile, PATHINFO_EXTENSION);

		if (trim($ext) != '') {
			$newFileName                     = 'DOC_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

			$src = $cfg['FILES.TEMP'] . $abstractFileTempFile;

			$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

			if (rename($src, $target)) {
				$sqlAbstractFile				  = array();
				$sqlAbstractFile['QUERY']         = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET `abstract_file` = '" . $newFileName . "',
																`abstract_original_file_name` = '" . $abstractFile . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

				$mycms->sql_update($sqlAbstractFile, false);
			}
		}
	} else {
		// INSERTING ABSTRACT FILE
		$abstractFile                             = addslashes($_FILES['upload_abstract_file']['name']);
		$abstractFileTempFile                     = $_FILES['upload_abstract_file']['tmp_name'];
		$ext							 		  = pathinfo($abstractFile, PATHINFO_EXTENSION);
		$abstractFileFileName             		  = 'DOC_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															SET `abstract_file` = '" . $abstractFileFileName . "',
																`abstract_original_file_name` = '" . $abstractFile . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	//	INSERTING ABSTRACT VIDEO
	if (isset($_REQUEST['upload_temp_vdo_fileName'])) {
		$abstractVideoFile               = $_REQUEST['upload_original_vdo_fileName'];
		$abstractVideoFileTempFile       = $_REQUEST['upload_temp_vdo_fileName'];
		$ext							 = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);

		if (trim($ext) != '') {
			$newFileName                     = 'VIDEO_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

			$src = $cfg['FILES.TEMP'] . $abstractVideoFileTempFile;

			$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

			if (rename($src, $target)) {
				$sqlAbstractFile				  = array();
				$sqlAbstractFile['QUERY']         = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET `abstract_video_file` = '" . $newFileName . "',
																`abstract_original_file_name` = '" . $abstractVideoFile . "'
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

				$mycms->sql_update($sqlAbstractFile, false);
			}
		}
	} else {
		$abstractVideoFile                             = $_FILES['upload_abstract_video']['name'];
		$abstractVideoFileTempFile                     = $_FILES['upload_abstract_video']['tmp_name'];
		$ext							 			   = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);
		$abstractVideoFileFileName                     = 'VIDEO_' . $lastInsertedAbstractId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractVideoFileTempFile != "") {
			$abstractVideoFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractVideoFileFileName;

			chmod($abstractVideoFilePath, 0777);
			copy($abstractVideoFileTempFile, $abstractVideoFilePath);
			chmod($abstractVideoFilePath, 0777);

			$sqlAbstractFile				  = array();
			$sqlAbstractFile['QUERY']         = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET `abstract_video_file` = '" . $abstractVideoFileFileName . "' 
														  WHERE `id` = '" . $lastInsertedAbstractId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	if ($isConference == "Y") {
		//abstract_free_paper_request($applicant_id, $lastInsertedAbstractId, $operation='SEND');

	} else {
		// abstract_free_paper_request_nonregistered($applicant_id, $lastInsertedAbstractId, $operation='SEND');

	}

	abstract_submission_message($applicant_id, $lastInsertedAbstractId, 'SEND');

	return $abstract_submition_code;
}

function editCaseReportSubmission($mycms, $cfg)
{
	global $cfg, $mycms;

	$sqlFetchUserDetails            		  = registrationDetailsQuerySet($_REQUEST['delegateId'], "");
	$resultUserDetails        	    		  = $mycms->sql_select($sqlFetchUserDetails);
	$rowUserDetails       	    			  = $resultUserDetails[0];
	$absreqId 								  = $_REQUEST['abstract_id'];
	$applicant_id                  			  = $rowUserDetails['id'];
	$applicant_registration_id 				  = $rowUserDetails['user_registration_id'];
	$applicant_initial_title                  = $rowUserDetails['user_title'];
	$applicant_first_name                     = $rowUserDetails['user_first_name'];
	$applicant_middle_name                    = $rowUserDetails['user_middle_name'];
	$applicant_last_name                      = $rowUserDetails['user_last_name'];
	$applicant_full_name            		  = $applicant_initial_title . ". " . $applicant_first_name . " " . $applicant_middle_name . " " . $applicant_last_name;
	$applicant_email_id                       = $rowUserDetails['user_email_id'];
	$applicant_mobile_isd_code                = $rowUserDetails['user_mobile_isd_code'];
	$applicant_mobile_no                      = $rowUserDetails['user_mobile_no'];
	$applicant_phone_no              		  = $rowUserDetails['user_phone_no'];
	$applicantUniqueSequence                  = $rowUserDetails['user_unique_sequence'];

	// DECLARATION OF VARRIABLES
	$abstract_author_name                     = addslashes(trim(strtoupper($_REQUEST['abstract_author_name'])));
	$abstract_author_department               = addslashes(trim(strtoupper($_REQUEST['abstract_author_department'])));
	$abstract_author_institute_name           = addslashes(trim(strtoupper($_REQUEST['abstract_author_institiute'])));
	$abstract_author_country             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_country'])));
	$abstract_author_state             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_state'])));
	$abstract_author_city             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_city'])));
	$abstract_author_phone_code               = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_code'])));
	$abstract_author_phone_no             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_no'])));

	$abstract_title                           = addslashes(trim(strtoupper($_REQUEST['abstract_title'])));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));

	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));

	$sqlUpdateAbstractDetails['QUERY']        = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
														SET `abstract_title` = '" . $abstract_title . "', 
															`abstract_background_aims` = '" . $abstract_background_aims . "', 
															`abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
															`abstract_author_department` = '" . $abstract_author_country . "', 
															`abstract_material_methods` = '" . $abstract_material_methods . "', 
															`abstract_results` = '" . $abstract_results . "', 
															`abstract_conclution` = '" . $abstract_conclusion . "', 
															`abstract_author_name` = '" . $abstract_author_name . "', 
															`abstract_author_department` = '" . $abstract_author_department . "', 
															`abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
															`abstract_author_state_id` = '" . $abstract_author_state . "', 
															`abstract_author_city` = '" . $abstract_author_city . "', 
															`abstract_author_country_id` = '" . $abstract_author_country . "', 
															`abstract_author_phone_code` = '" . $abstract_author_phone_code . "',															
															`abstract_author_phone_no` = '" . $abstract_author_phone_no . "',
															`abstract_references` = '" . $abstract_references . "',
															`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
															`modified_sessionId` = '" . session_id() . "', 
															`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
													  WHERE `id` = '" . $absreqId . "'";
	$mycms->sql_update($sqlUpdateAbstractDetails, false);

	$sqlUpdateUser['QUERY']             	  = "UPDATE " . _DB_USER_REGISTRATION_ . "  
														SET `user_institute_name` = '" . $_REQUEST['presenter_institute_name'] . "',
															`user_department` = '" . $_REQUEST['presenter_institute_department'] . "'
													  WHERE `id` = '" . $applicant_id . "'";
	$mycms->sql_update($sqlUpdateUser, false);


	$idCollection 			  = array();

	$abstractCoAuthorIdArray  = $_REQUEST['abstract_coauthor_id'];

	foreach ($abstractCoAuthorIdArray as $keyCoAuthor => $abstractCoAuthorId) {
		$abstract_coauthor_name_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_name'][$keyCoAuthor])));
		$abstract_coauthor_department_val     = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_department'][$keyCoAuthor])));
		$abstract_coauthor_institute_name_val = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_name'][$keyCoAuthor])));
		$abstract_coauthor_country_val        = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_country'][$keyCoAuthor])));
		$abstract_coauthor_state_val          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_state'][$keyCoAuthor])));
		$abstract_coauthor_city_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_city'][$keyCoAuthor])));
		$abstract_coauthor_phone_no_val       = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_phone_no'][$keyCoAuthor])));

		if ($abstract_coauthor_name_val != "") {
			$sqlInsertCoAuthor	= array();
			if ($abstractCoAuthorId == '') {
				// INSERT PROCESS
				$sqlInsertCoAuthor['QUERY']                = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . " 
																		 SET `abstract_id` = '" . $absreqId . "', 															 	 
																			 `abstract_coauthor_name` = '" . $abstract_coauthor_name_val . "',
																			 `abstract_coauthor_department` = '" . $abstract_coauthor_department_val . "',
																			 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_name_val . "',
																			 `abstract_coauthor_country_id` = '" . $abstract_coauthor_country_val . "',
																			 `abstract_coauthor_state_id` = '" . $abstract_coauthor_state_val . "',
																			 `abstract_coauthor_city_name` = '" . $abstract_coauthor_city_val . "',
																			 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_phone_no_val . "', 
																			 `status` = 'A',
																			 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																			 `created_sessionId` = '" . session_id() . "', 
																			 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";

				$idCollection[] = $mycms->sql_insert($sqlInsertCoAuthor, false);
			} else {
				$sqlInsertCoAuthor['QUERY']                = "UPDATE " . _DB_ABSTRACT_COAUTHOR_ . " 
																	 SET `abstract_coauthor_name` = '" . $abstract_coauthor_name_val . "',															 	 
																		 `abstract_coauthor_department` = '" . $abstract_coauthor_department_val . "',
																		 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_name_val . "',
																		 `abstract_coauthor_country_id` = '" . $abstract_coauthor_country_val . "',
																		 `abstract_coauthor_state_id` = '" . $abstract_coauthor_state_val . "',
																		 `abstract_coauthor_city_name` = '" . $abstract_coauthor_city_val . "',
																		 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_phone_no_val . "', 
																		 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																		 `modified_sessionId` = '" . session_id() . "', 
																		 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
																   WHERE `id` = '" . $abstractCoAuthorId . "'
																	 AND `abstract_id` = '" . $absreqId . "' ";
				$mycms->sql_update($sqlInsertCoAuthor, false);
				$idCollection[] = $abstractCoAuthorId;
			}
		}
	}

	$sqlCoAuthorDtls  = array();
	$sqlCoAuthorDtls['QUERY']  = " SELECT id 
										 FROM " . _DB_ABSTRACT_COAUTHOR_ . "
									    WHERE `abstract_id` = '" . $absreqId . "' 
										  AND `status` = 'A'";
	$coAuthorDetails  		   = $mycms->sql_select($sqlCoAuthorDtls);
	foreach ($coAuthorDetails as $keyRow => $rowFetchcoAuthorDetails) {
		if (!in_array($rowFetchcoAuthorDetails['id'], $idCollection)) {
			$sqlRemoveCoAuthor			= array();
			$sqlRemoveCoAuthor['QUERY'] = "UPDATE " . _DB_ABSTRACT_COAUTHOR_ . "  
												  SET `status` = 'D' 														
											    WHERE `id` = '" . $rowFetchcoAuthorDetails['id'] . "'
											      AND `abstract_id` = '" . $absreqId . "' ";
			$mycms->sql_update($sqlRemoveCoAuthor, false);
		}
	}

	//	INSERTING ABSTRACT FILE
	if (isset($_REQUEST['upload_temp_doc_fileName'])) {
		$abstractFile               	 = $_REQUEST['upload_original_doc_fileName'];
		$abstractFileTempFile       	 = $_REQUEST['upload_temp_doc_fileName'];
		$ext							 = pathinfo($abstractFile, PATHINFO_EXTENSION);

		if (trim($ext) != '') {
			$newFileName                     = 'DOC_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

			$src 	= $cfg['FILES.TEMP'] . $abstractFileTempFile;

			$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

			if (rename($src, $target)) {
				$sqlAbstractFile				  	  = array();
				$sqlAbstractFile['QUERY'] 			  = " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																 SET `abstract_file` = '" . $newFileName . "' ,
																	 `abstract_original_file_name` = '" . $abstractFile . "'
															   WHERE `id` = '" . $absreqId . "'";

				$mycms->sql_update($sqlAbstractFile, false);
			}
		}
	} else {
		// INSERTING ABSTRACT FILE
		$abstractFile                             = $_FILES['upload_abstract_file']['name'];
		$abstractFileTempFile                     = $_FILES['upload_abstract_file']['tmp_name'];
		$ext							 		   = pathinfo($abstractFile, PATHINFO_EXTENSION);
		$abstractFileFileName                     = 'DOC_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractFileTempFile != "") {
			$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlAbstractFile['QUERY'] 			  = " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															 SET `abstract_file` = '" . $abstractFileFileName . "' ,
																 `abstract_original_file_name` = '" . $abstractFile . "'
														   WHERE `id` = '" . $absreqId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	//	INSERTING ABSTRACT VIDEO
	if (isset($_REQUEST['upload_temp_vdo_fileName'])) {
		if (trim($_REQUEST['upload_temp_vdo_fileName']) != '') {
			$abstractVideoFile               = $_REQUEST['upload_original_vdo_fileName'];
			$abstractVideoFileTempFile       = $_REQUEST['upload_temp_vdo_fileName'];
			$ext							 = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);

			if (trim($ext) != '') {
				$newFileName                     = 'VIDEO_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

				$src 	= $cfg['FILES.TEMP'] . $abstractVideoFileTempFile;

				$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

				if (rename($src, $target)) {
					$sqlAbstractFile				  = array();
					$sqlAbstractFile['QUERY']         = "  UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																  SET `abstract_video_file` = '" . $newFileName . "' ,
																	  `abstract_original_file_name` = '" . $abstractVideoFile . "'
																WHERE `id` = '" . $absreqId . "'";

					$mycms->sql_update($sqlAbstractFile, false);
				}
			}
		}
	} else {
		$abstractVideoFile                             = $_FILES['upload_abstract_video']['name'];
		$abstractVideoFileTempFile                     = $_FILES['upload_abstract_video']['tmp_name'];
		$ext							 			   = pathinfo($abstractVideoFile, PATHINFO_EXTENSION);
		$abstractVideoFileFileName                     = 'VIDEO_' . $absreqId . '_' . date('YmdHis') . '.' . $ext;

		if ($abstractVideoFileTempFile != "") {
			$abstractVideoFilePath                     = $cfg['VIDEO.FILES.ABSTRACT.REQUEST'] . $abstractVideoFileFileName;

			chmod($abstractVideoFilePath, 0777);
			copy($abstractVideoFileTempFile, $abstractVideoFilePath);
			chmod($abstractVideoFilePath, 0777);

			$sqlAbstractFile['QUERY']                  = " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																  SET `abstract_video_file` = '" . $abstractVideoFileFileName . "' ,
														    		  `abstract_original_file_name` = '" . $abstractVideoFile . "'
																WHERE `id` = '" . $absreqId . "'";

			$mycms->sql_update($sqlAbstractFile, false);
		}
	}

	return $abstract_submition_code;
}

function generateStateOptionListProcess()
{
	global $cfg, $mycms;
	$countryId = addslashes(trim($_REQUEST['countryId']));
?>
	<option value="">-- Choose State --</option>
	<?php
	$sqlFetchState			=	array();
	$sqlFetchState['QUERY'] = "SELECT * FROM " . _DB_COMN_STATE_ . "
								    WHERE `country_id` = ?
									  AND `status` = ?
									  ORDER BY TRIM(state_name) ASC ";
	$sqlFetchState['PARAM'][]   = array('FILD' => 'country_id',    'DATA' => $countryId,  'TYP' => 's');
	$sqlFetchState['PARAM'][]   = array('FILD' => 'status',  	   'DATA' => 'A',  		 'TYP' => 's');
	$resultState	= $mycms->sql_select($sqlFetchState);
	if ($resultState) {
		foreach ($resultState as $keyState => $rowState) {
	?>
			<option value="<?= $rowState['st_id'] ?>"><?= $rowState['state_name'] ?></option>
<?php
		}
	}
}


// abstract submission update from porfile by weavers start
function updateAbstratcSubmission($mycms, $cfg)
{
	// echo '<pre>'; print_r($_REQUEST); die;
	// FETCHING DELEGATE DETAILS
	include_once('includes/function.delegate.php');

	$applicantId							  = $_REQUEST['applicantId'];
	$abstract_id                   	  		  = $_REQUEST['abstract_id'];

	$sql  			  = array();
	$sql['QUERY']     = " SELECT id,abstract_category,abstract_parent_type
								FROM " . _DB_ABSTRACT_REQUEST_ . " 
							   WHERE `status` = ?
								 AND `applicant_id` = ? AND id = ?";
	$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' => $applicantId, 'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'id',   'DATA' => $abstract_id, 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql);

	//echo '<pre>'; print_r($_REQUEST);

	$sqlDelete  			  = array();
	$sqlDelete['QUERY']     = " DELETE 
								FROM " . _DB_ABSTRACT_COAUTHOR_ . " 
							   WHERE `abstract_id` = ?
								 ";

	$sqlDelete['PARAM'][]   = array('FILD' => 'abstract_id',   'DATA' => $abstract_id, 'TYP' => 's');
	$mycms->sql_delete($sqlDelete);
	//die();

	// DECLARATION OF VARRIABLES

	//Presenter details UPDATE
	$abstract_presenter_email = $_REQUEST['edit_author_email'];
	$abstract_presenter_first_name = $_REQUEST['edit_author_first_name'];
	$abstract_presenter_last_name = $_REQUEST['edit_author_last_name'];
	$abstract_presenter_country = $_REQUEST['edit_author_country'];
	$abstract_presenter_state = $_REQUEST['edit_author_state'];
	$abstract_presenter_city = $_REQUEST['edit_author_city'];
	$abstract_presenter_title = $_REQUEST['edit_author_title'];
	$abstract_presenter_mobile = $_REQUEST['edit_author_mobile'];
	$abstract_presenter_pincode = $_REQUEST['edit_author_pincode'];

	$abstract_presenter_name = $abstract_presenter_title . " " . $abstract_presenter_first_name . " " . $abstract_presenter_last_name;

	$abstract_category = $_REQUEST['abstract_category'];
	$abstract_parent_type = $_REQUEST['abstract_parent_type'];
	$topicArr = explode('-', $_REQUEST['abstract_topic_id']);
	// ABSTRACT DETAILS
	$abstract_topic_id                   	  = (!empty($_REQUEST['abstract_topic_id'])) ? ($topicArr[0]) : "0";
	$abstract_title                           = addslashes(trim($_REQUEST['abstract_title']));

	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background'])); // Introduction
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));

	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));

	$existing_abstract_file					  = trim($_REQUEST['existing_abstract_file']);
	$existing_abstract_file_name			  = trim($_REQUEST['existing_abstract_file_name']);

	$fields1 						 = (!empty($_REQUEST['fields1'][0])) ? addslashes(trim($_REQUEST['fields1'][0])) : 'NULL';
	$fields2 						 = (!empty($_REQUEST['fields2'][0])) ? addslashes(trim($_REQUEST['fields2'][0])) : 'NULL';
	$fields3 						 = (!empty($_REQUEST['fields3'][0])) ? addslashes(trim($_REQUEST['fields3'][0])) : 'NULL';
	$fields4 						 = (!empty($_REQUEST['fields4'][0])) ? addslashes(trim($_REQUEST['fields4'][0])) : 'NULL';
	$fields5 						 = (!empty($_REQUEST['fields5'][0])) ? addslashes(trim($_REQUEST['fields5'][0])) : 'NULL';
	$fields6 						 = (!empty($_REQUEST['fields6'][0])) ? addslashes(trim($_REQUEST['fields6'][0])) : 'NULL';
	$fields7 						 = (!empty($_REQUEST['fields7'][0])) ? addslashes(trim($_REQUEST['fields7'][0])) : 'NULL';
	$fields8 						 = (!empty($_REQUEST['fields8'][0])) ? addslashes(trim($_REQUEST['fields8'][0])) : 'NULL';
	$fields9 						 = (!empty($_REQUEST['fields9'][0])) ? addslashes(trim($_REQUEST['fields9'][0])) : 'NULL';
    $fields10 						 = (!empty($step1Data['fields10'][0])) ? addslashes(trim($step1Data['fields10'][0])) : 'NULL';
	$fields11 						 = (!empty($step1Data['fields11'][0])) ? addslashes(trim($step1Data['fields11'][0])) : 'NULL';

	/*----Abstrat Coauther Start----------------*/

	$abstractCoAuthorArray  = $_REQUEST['abstract_coauthor_name'];
	// echo '<pre>'; print_r($_REQUEST); 
	if (!empty($_REQUEST['abstract_coauthor_email']) || !empty($_REQUEST['abstract_coauthor_mobile'])) {
		//print_r("mail= ".$_REQUEST['abstract_author_email']);die;

		foreach ($_REQUEST['abstract_coauthor_email'] as $k => $val) {

			$abstract_coauthor_email = $val;
			$abstract_coauthor_first_name = $_REQUEST['abstract_coauthor_first_name'][$k];
			$abstract_coauthor_last_name = $_REQUEST['abstract_coauthor_last_name'][$k];
			$abstract_coauthor_country_id = $_REQUEST['abstract_coauthor_country'][$k];
			$abstract_coauthor_state_id = $_REQUEST['abstract_coauthor_state'][$k];
			$abstract_coauthor_city_name = $_REQUEST['abstract_coauthor_city'][$k];
			$abstract_coauthor_phone_no = $_REQUEST['abstract_coauthor_mobile'][$k];
			$abstract_coauthor_title = $_REQUEST['abstract_coauthor_title'][$k];
			$abstract_coauthor_pincode = $_REQUEST['abstract_coauthor_pincode'][$k];
			$abstract_coauthor_institute_name = $_REQUEST['abstract_coauthor_institute'][$k];
			$abstract_coauthor_department = $_REQUEST['abstract_coauthor_department'][$k];

			$abstract_coauthor_name = $abstract_coauthor_first_name . " " . $abstract_coauthor_last_name;


			if (!empty($abstract_coauthor_email) || !empty($abstract_coauthor_first_name) || !empty($abstract_coauthor_last_name) || !empty($abstract_coauthor_country_id) || !empty($abstract_coauthor_state_id) || !empty($abstract_coauthor_city_name) || !empty($abstract_coauthor_phone_no) || !empty($abstract_coauthor_title) || !empty($abstract_coauthor_pincode)) {
				$sqlInsertCoAuthor                = array();
				$sqlInsertCoAuthor['QUERY']       = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . "  
	                                                             SET `abstract_id` = ?, 
	                                                                 `abstract_coauthor_first_name` = ?,
	                                                                 `abstract_coauthor_last_name` = ?,
	                                                                 `abstract_coauthor_title` = ?,
	                                                                 `abstract_coauthor_email` = ?,
	                                                                 `abstract_coauthor_name` = ?,
	                                                                
	                                                                 `abstract_coauthor_country_id` = ?,
	                                                                 `abstract_coauthor_state_id` = ?,
	                                                                 `abstract_coauthor_city_name` = ?,
	                                                                 `abstract_coauthor_phone_no` = ?,
	                                                                 `abstract_coauthor_pincode` = ?,  
	                                                                 `abstract_coauthor_institute_name` = ?,  
	                                                                 `abstract_coauthor_department` = ?,  
	                                                                 `status` = ?,
	                                                                 `created_ip` =?, 
	                                                                 `created_sessionId` = ?, 
	                                                                 `created_dateTime` = ?";

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_id',                      'DATA' => $abstract_id,               'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_first_name',           'DATA' => $abstract_coauthor_first_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_last_name',           'DATA' => $abstract_coauthor_last_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_title',           'DATA' => $abstract_coauthor_title,           'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_email',           'DATA' => $abstract_coauthor_email,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_name',           'DATA' => $abstract_coauthor_name,           'TYP' => 's');

				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_country_id',     'DATA' => $abstract_coauthor_country_id,        'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_state_id',       'DATA' => $abstract_coauthor_state_id,          'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_city_name',      'DATA' => $abstract_coauthor_city_name,           'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_phone_no',       'DATA' => $abstract_coauthor_phone_no,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_pincode',       'DATA' => $abstract_coauthor_pincode,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_institute_name',       'DATA' => $abstract_coauthor_institute_name,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_department',       'DATA' => $abstract_coauthor_department,       'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'status',                           'DATA' => 'A',                                   'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_ip',                       'DATA' => $_SERVER['REMOTE_ADDR'],               'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_sessionId',                'DATA' => session_id(),                          'TYP' => 's');
				$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_dateTime',                 'DATA' => date('Y-m-d H:i:s'),                   'TYP' => 's');

				//echo '<pre>'; print_r($sqlInsertCoAuthor); 

				$mycms->sql_insert($sqlInsertCoAuthor, false);
			}
		}
	}
	// die;

	/*----Abstrat Coauther End----------------*/
	$total_count_word = 0;
	if (trim($resultAbstractType[0]['abstract_category']) === "Free Paper" && strtoupper(trim($resultAbstractType[0]['abstract_parent_type']) === "CASE REPORT")) {
		$intro_words = str_word_count(trim($abstract_background));
		$desc_words = str_word_count(trim($abstract_description));
		$conclution_words = str_word_count(trim($abstract_conclusion));
		$total_count_word = $intro_words + $desc_words + $conclution_words;
	} else {
		$intro_words = str_word_count(trim($abstract_background));
		$aims_obj_words = str_word_count(trim($abstract_background_aims));
		$material_wrods = str_word_count(trim($abstract_material_methods));
		$results_wrods = str_word_count(trim($abstract_results));
		$conclution_words = str_word_count(trim($abstract_conclusion));
		$total_count_word = $intro_words + $aims_obj_words + $material_wrods + $results_wrods + $conclution_words;
	}

	if ($total_count_word > 30 || $total_count_word > 250) {
		$msg = "Unable to update abstract due to title or content size excceed the limit. Please check and update again.";
		//exit();
	}

	if (count($resultAbstractType) > 0) {

		// UPDATING ABSTRACT REQUEST PROCESS
		$sqlInsertAbstractProcess				  = array();
		$sqlInsertAbstractProcess['QUERY']        = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
																 SET 
																 	`abstract_author_email_id` = '" . $abstract_presenter_email . "', 
																 	`abstract_author_first_name` = '" . $abstract_presenter_first_name . "', 
																 	`abstract_author_last_name` = '" . $abstract_presenter_last_name . "', 
																	`abstract_author_phone_no`='" . $abstract_presenter_mobile . "',
																 	`abstract_author_country_id` = '" . $abstract_presenter_country . "', 
																 	`abstract_author_state_id` = '" . $abstract_presenter_state . "', 
																 	`abstract_author_city` = '" . $abstract_presenter_city . "', 
																 	`abstract_author_name` = '" . $abstract_presenter_name . "', 
																 	`abstract_author_title` = '" . $abstract_presenter_title . "', 
																	`abstract_author_pin`='" . $abstract_presenter_pincode . "',

																	`abstract_cat`='" . $abstract_category . "',
																	`abstract_parent_type`='" . $abstract_parent_type . "',
																 	`abstract_topic_id` = '" . $abstract_topic_id . "', 
																	`abstract_title` = '" . $abstract_title . "',
																	`abstract_background` = '" . $abstract_background . "', 
																	`abstract_background_aims` = '" . $abstract_background_aims . "', 
																	`abstract_material_methods` = '" . $abstract_material_methods . "', 
																	`abstract_results` = '" . $abstract_results . "', 
																	`abstract_conclution` = '" . $abstract_conclusion . "', 
																	`abstract_description` = '" . $abstract_description . "',

																	`abstract_author_institute_name` = '" . $_REQUEST['edit_author_institute_name']. "', 
																	`abstract_author_department` = '" . $_REQUEST['edit_author_department']. "',
																	`fields1` = '" . $fields1 . "',
																	`fields2` = '" . $fields2 . "',
																	`fields3` = '" . $fields3 . "',
																	`fields4` = '" . $fields4 . "',
																	`fields5` = '" . $fields5 . "',
																	`fields6` = '" . $fields6 . "',
																	`fields7` = '" . $fields7 . "', 
																	`fields8` = '" . $fields8 . "', 
																	`fields9` = '" . $fields9 . "', 
																	`fields10` = '" . $fields10 . "', 
																	`fields11` = '" . $fields11. "', 
																	`status` = 'A',
																	`modified_by` = '" . $applicantId . "', 
																	`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																	`modified_sessionId` = '" . session_id() . "', 
																	`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
																	 WHERE `id` = '" . $abstract_id . "'";
		$mycms->sql_update($sqlInsertAbstractProcess, false);


		//delete the existing file 
		if (file_exists($cfg['FILES.ABSTRACT.REQUEST'] . $existing_abstract_file)) {
			unlink($cfg['FILES.ABSTRACT.REQUEST'] . $existing_abstract_file);
		}

		//	INSERTING ABSTRACT FILE
		if (isset($_REQUEST['upload_temp_doc_fileName'])) {
			$abstractFile               	 = $_REQUEST['upload_original_doc_fileName'];
			$abstractFileTempFile       	 = $_REQUEST['upload_temp_doc_fileName'];
			$ext							 = pathinfo($abstractFile, PATHINFO_EXTENSION);

			if (trim($ext) != '') {
				$newFileName                     = 'DOC_' . $abstract_id . '_' . date('YmdHis') . '.' . $ext;

				$src = $cfg['FILES.TEMP'] . $abstractFileTempFile;

				$target	= $cfg['FILES.ABSTRACT.REQUEST'] . $newFileName;

				if (rename($src, $target)) {
					$sqlAbstractFile				  = array();
					$sqlAbstractFile['QUERY']         = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
																SET `abstract_file` = '" . $newFileName . "',
																	`abstract_original_file_name` = '" . $abstractFile . "'
															  WHERE `id` = '" . $abstract_id . "'";

					$mycms->sql_update($sqlAbstractFile, false);
				}
			}
		} else {

			if (!empty($_FILES['upload_abstract_file']['name']) || !empty($_FILES['upload_consent_abstract_file']['name'])) {

				// INSERTING ABSTRACT FILE
				$abstractFile                             = addslashes($_FILES['upload_abstract_file']['name']);
				$abstractFileTempFile                     = $_FILES['upload_abstract_file']['tmp_name'];
				$ext                                      = pathinfo($abstractFile, PATHINFO_EXTENSION);
				$abstractFileFileName                     = 'ABSTRACT_' . $abstract_id . '_' . date('YmdHis') . '.' . $ext;

				if ($abstractFileTempFile != "") {
					$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

					chmod($abstractFilePath, 0777);
					copy($abstractFileTempFile, $abstractFilePath);
					chmod($abstractFilePath, 0777);

					$sqlAbstractFile                  = array();
					$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
		                                                            SET `abstract_file` = '" . $abstractFileFileName . "',
		                                                                `abstract_original_file_name` = '" . $abstractFile . "'
		                                                          WHERE `id` = '" . $abstract_id . "'";

					$mycms->sql_update($sqlAbstractFile, false);
				}


				//Consent File Upload Start------------//

				$abstractFileConsent                      = addslashes($_FILES['upload_consent_abstract_file']['name']);
				$abstractFileTempFile                     = $_FILES['upload_consent_abstract_file']['tmp_name'];
				$ext                                      = pathinfo($abstractFileConsent, PATHINFO_EXTENSION);
				$abstractFileFileName                     = 'CONSENT_' . $abstract_id . '_' . date('YmdHis') . '.' . $ext;

				if ($abstractFileTempFile != "") {
					$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

					chmod($abstractFilePath, 0777);
					copy($abstractFileTempFile, $abstractFilePath);
					chmod($abstractFilePath, 0777);

					$sqlAbstractFile                  = array();
					$sqlAbstractFile['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
		                                                            SET `abstract_consent_file` = '" . $abstractFileFileName . "',
		                                                                `abstract_consent_original_file_name` = '" . $abstractFileConsent . "'
		                                                          WHERE `id` = '" . $abstract_id . "'";
					// echo "<pre>"; print_r($sqlAbstractFile);die;
					$mycms->sql_update($sqlAbstractFile, false);
				}

				//Consent File Upload End------------//

			}
		}

		//abstract_submission_message($applicant_id, $lastInsertedAbstractId, 'SEND');

		$msg = "Abstract Updated Successfully.";
	} else {
		$msg = "Unable To Update Abstract.";
	}
	$_SESSION["absedmsg"] = $msg;
	$mycms->redirect("profile.php");
	exit();
}
// abstract submission update from porfile by weavers end



/////////////////////////////////////////////////////
function abstractFileEdit_x()
{
	global $cfg, $mycms;
	$delegate_id 				 			  = $_REQUEST['delegate_id'];
	$abstract_id                  			  = $_REQUEST['abstract_id'];
	$edit_abstract_title					  = addslashes(trim(strtoupper($_REQUEST['edit_abstract_title'])));
	$abstractFile                             = $_FILES['edit_abstract_file']['name'];
	$abstractFileTempFile                     = $_FILES['edit_abstract_file']['tmp_name'];
	$abstractFileFileName                     = $delegate_id . "_" . time() . strstr($abstractFile, '.');


	if ($abstractFileTempFile != "") {
		$abstractFilePath                     = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;

		chmod($abstractFilePath, 0777);
		copy($abstractFileTempFile, $abstractFilePath);
		chmod($abstractFilePath, 0777);

		$sqlUpdateAbstractDetails['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
														SET `abstract_file` = '" . $abstractFileFileName . "'
													  WHERE `id` = '" . $abstract_id . "'";

		$mycms->sql_update($sqlUpdateAbstractDetails, false);
	}

	if ($edit_abstract_title != "") {

		$sqlUpdateAbstracttitle['QUERY']             = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET`abstract_title`= '" . $edit_abstract_title . "'
														  WHERE `id` = '" . $abstract_id . "'";

		$mycms->sql_update($sqlUpdateAbstracttitle, false);
	}
}

function abstractCaseEdit_x()
{
	global $cfg, $mycms;

	$delegate_id 				 			  = $_REQUEST['applicantId'];
	$case_title 				  			  = addslashes(trim(strtoupper($_REQUEST['casestudy_title'])));
	$caseStudyId                  			  = $_REQUEST['caseStudyId'];
	$abstractFile                             = $_FILES['upload_case_file']['name'];

	$caseFileRemainArrayid          	      = $_REQUEST['caseFileExistingId'];

	$sqlExistingCaseFileCount['QUERY']	= "	 SELECT `id` 
										   FROM " . $cfg['DB.CASE.STUDY.FILE'] . "
										  WHERE `case_stady_id` = '" . $caseStudyId . "' 
											AND `delegate_id` = '" . $delegate_id . "'
											AND `status` = 'A' ";
	$resultCaseFileCount 		= $mycms->sql_select($sqlExistingCaseFileCount);

	if ($resultCaseFileCount) {
		foreach ($resultCaseFileCount as $key1 => $rowCaseFileCount) {
			$caseFileExistingIds[$key1] = $rowCaseFileCount['id'];
		}
	}

	if ($caseFileRemainArrayid) {
		if ($caseFileExistingIds) {
			foreach ($caseFileExistingIds as $key2 => $rowEachCaseFile) {
				if (!in_array($rowEachCaseFile, $caseFileRemainArrayid)) {
					$sqlRemoveCaseFile['QUERY'] = "UPDATE " . $cfg['DB.CASE.STUDY.FILE'] . "  
												 SET `status` = 'D' 														
										  	   WHERE `id` = '" . $rowEachCaseFile . "'
												 AND `case_stady_id` = '" . $caseStudyId . "' 
												 AND `delegate_id` = '" . $delegate_id . "' ";
					$mycms->sql_update($sqlRemoveCaseFile, false);
				}
			}
		}
	} else {
		foreach ($caseFileExistingIds as $key2 => $rowEachCaseFile2) {
			$sqlRemoveCaseFile['QUERY'] = " UPDATE " . $cfg['DB.CASE.STUDY.FILE'] . "  
										  SET `status` = 'D' 														
									    WHERE `id` = '" . $rowEachCaseFile2 . "'
										  AND `case_stady_id` = '" . $caseStudyId . "' 
										  AND `delegate_id` = '" . $delegate_id . "' ";
			$mycms->sql_update($sqlRemoveCaseFile, false);
		}
	}

	if ($_REQUEST['casestudy_title'] != "") {
		$sqlUpdateAbstractTitle['QUERY'] = " UPDATE " . $cfg['DB.CASE.STUDY'] . "  
										   SET `casestudy_title` = '" . $_REQUEST['casestudy_title'] . "',
											   `abstract_author_institute_name` = '" . $_REQUEST['presenter_institute_name'] . "',
											   `abstract_author_department` = '" . $_REQUEST['presenter_institute_department'] . "'
									  	 WHERE `id` = '" . $caseStudyId . "'";
		$mycms->sql_update($sqlUpdateAbstractTitle, false);
	}

	$sqlUpdateUser['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  
							 SET `user_institute_name` = '" . $_REQUEST['presenter_institute_name'] . "',
								 `user_depertment` = '" . $_REQUEST['presenter_institute_department'] . "'
						   WHERE `id` = '" . $delegate_id . "'";
	$mycms->sql_update($sqlUpdateUser, false);

	foreach ($abstractFile as $key => $rowfetch) {
		$abstractFileTempFile 	= $_FILES['upload_case_file']['tmp_name'][$key];
		$abstractFileFileName 	= $delegate_id . "_" . time() . $rowfetch;
		$abstractFilePath 		= $cfg['FILES.CASE.STUDY'] . $abstractFileFileName;

		if ($abstractFileTempFile != "") {
			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlInsertAbstractDetails['QUERY'] = "INSERT INTO " . $cfg['DB.CASE.STUDY.FILE'] . "  
													 SET `case_study_file` = '" . $abstractFileFileName . "',
														 `case_study_original_file` = '" . $rowfetch . "',
														 `delegate_id` = '" . $delegate_id . "', 
														 `case_stady_id` = '" . $caseStudyId . "',
														 `created_dateTime` = '" . date('Y-m-d H:i:s') . "' ";
			$mycms->sql_insert($sqlInsertAbstractDetails, false);
		}
	}
}

function submitcasestudySubmissionProcess_x()
{
	global $mycms, $cfg;

	$applicant_id 				= addslashes(trim(strtoupper($_REQUEST['applicantId'])));
	$case_title 				= addslashes(trim(strtoupper($_REQUEST['casestudy_title'])));
	$case_intro                 = addslashes(trim(strtoupper($_REQUEST['casestudy_intro'])));
	$sqlFetchUserDetails 		= registrationDetailsQuerySet($_REQUEST['applicantId'], "");
	$resultUserDetails 			= $mycms->sql_select($sqlFetchUserDetails);
	$rowUserDetails 			= $resultUserDetails[0];
	$applicantUniqueSequence 	= $rowUserDetails['unique_sequence'];

	$sqlAbstractFile['QUERY'] 			= "  INSERT INTO " . $cfg['DB.CASE.STUDY'] . " 
												 SET `delegate_id` 						  = '" . $_REQUEST['applicantId'] . "',
													 `casestudy_title` 					  = '" . $case_title . "',
													 `casestudy_intro` 					  = '" . $case_intro . "',
													 `abstract_author_institute_name` 	  = '" . addslashes(trim(strtoupper($_REQUEST['presenter_institute_name']))) . "',
													 `abstract_author_department`		  = '" . addslashes(trim(strtoupper($_REQUEST['presenter_institute_department']))) . "',
													 `created_ip` 						  = '" . $_SERVER['REMOTE_ADDR'] . "', 
													 `created_sessionId` 				  = '" . session_id() . "', 
													 `created_dateTime` 				  = '" . date('Y-m-d H:i:s') . "'";
	$lastInsertedAbstractId = $mycms->sql_insert($sqlAbstractFile, false);

	$CASE_submition_code                  = $mycms->getRandom(4, 'num') . number_pad($lastInsertedAbstractId, 4);

	$sqlAbstractFile1['QUERY']                     = "UPDATE " . $cfg['DB.CASE.STUDY'] . " 
													SET `submission_code` ='" . $CASE_submition_code . "'
												  WHERE `id` = '" . $lastInsertedAbstractId . "'";
	$mycms->sql_update($sqlAbstractFile1, false);

	$sqlUpdateUser['QUERY']             = "UPDATE " . _DB_USER_REGISTRATION_ . "  
										 SET `user_institute_name` = '" . $_REQUEST['presenter_institute_name'] . "',
											 `user_depertment` = '" . $_REQUEST['presenter_institute_department'] . "'
									   WHERE `id` = '" . $applicant_id . "'";
	$mycms->sql_update($sqlUpdateUser, false);

	$abstractFiles                                = $_FILES['upload_case_file']['name'];
	foreach ($abstractFiles as $key => $rowfetch) {
		$abstractFile                             = $_FILES['upload_case_file']['name'][$key];
		$abstractFileTempFile                     = $_FILES['upload_case_file']['tmp_name'][$key];
		$abstractFileFileName                     = $applicant_id . $key . "_" . time() . $abstractFile;

		$abstractFilePath                         = $cfg['FILES.CASE.STUDY'] . $abstractFileFileName;

		if ($abstractFileTempFile != "") {
			chmod($abstractFilePath, 0777);
			copy($abstractFileTempFile, $abstractFilePath);
			chmod($abstractFilePath, 0777);

			$sqlCaseFile['QUERY']                      = "INSERT INTO " . $cfg['DB.CASE.STUDY.FILE'] . " 
															SET `delegate_id` = '" . $applicant_id . "',
																 `case_stady_id` = '" . $lastInsertedAbstractId . "',
																 `case_study_original_file` = '" . $rowfetch . "',
																 `case_study_file` = '" . $abstractFileFileName . "',
																 `created_dateTime` = '" . date('Y-m-d H:i:s') . "' ";

			$lastInsertedCaseStady = $mycms->sql_insert($sqlCaseFile, false);
		}
	}
	abstract_case_report_message($applicant_id, $lastInsertedAbstractId, 'SEND');

	return $CASE_submition_code;
}

function cseReportSubmission_x($mycms, $cfg)
{
	// FETCHING DELEGATE DETAILS


	$applicantId = $_REQUEST['applicantId'];


	$sqlFetchUserDetails            		  = registrationDetailsQuerySet($applicantId, "");
	$resultUserDetails        	    		  = $mycms->sql_select($sqlFetchUserDetails);
	$rowUserDetails       	    			  = $resultUserDetails[0];

	$applicant_id                  			  = $rowUserDetails['id'];
	$applicant_registration_id 				  = $rowUserDetails['user_registration_id'];
	$applicant_initial_title                  = $rowUserDetails['user_title'];
	$applicant_first_name                     = $rowUserDetails['user_first_name'];
	$applicant_middle_name                    = $rowUserDetails['user_middle_name'];
	$applicant_last_name                      = $rowUserDetails['user_last_name'];
	$applicant_full_name            		  = $applicant_initial_title . ". " . $applicant_first_name . " " . $applicant_middle_name . " " . $applicant_last_name;
	$applicant_email_id                       = $rowUserDetails['user_email_id'];
	$applicant_mobile_isd_code                = $rowUserDetails['user_mobile_isd_code'];
	$applicant_mobile_no                      = $rowUserDetails['user_mobile_no'];
	$applicant_phone_no              		  = $rowUserDetails['user_phone_no'];

	$applicantUniqueSequence				  = $rowUserDetails['user_unique_sequence'];

	// DECLARATION OF VARRIABLES

	$abstract_presenter_institute_name        = addslashes(trim(strtoupper($_REQUEST['abstract_presenter_institute_name'])));
	$abstract_presenter_department            = addslashes(trim(strtoupper($_REQUEST['abstract_presenter_department'])));

	$abstract_author_name                     = addslashes(trim(strtoupper($_REQUEST['abstract_author_name'])));
	$abstract_author_department               = addslashes(trim(strtoupper($_REQUEST['abstract_author_department'])));
	$abstract_author_institute_name           = addslashes(trim(strtoupper($_REQUEST['abstract_author_institute_name'])));
	$abstract_author_country             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_country'])));
	$abstract_author_state             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_state_id'])));
	$abstract_author_city             		  = addslashes(trim(strtoupper($_REQUEST['abstract_author_city'])));
	$abstract_author_phone_code				  = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_isd_code'])));
	$abstract_author_phone_no             	  = addslashes(trim(strtoupper($_REQUEST['abstract_author_phone_no'])));

	$abstract_topic_id                   	  = $_REQUEST['abstract_topic_id']; //0;
	$abstract_title                           = addslashes(trim($_REQUEST['abstract_title']));
	$abstract_study                           = addslashes(trim($_REQUEST['abstract_study']));

	$abstract_parent_type                     = addslashes(trim(strtoupper($_REQUEST['abstract_parent_type'])));
	$abstract_child_type                      = addslashes(trim(strtoupper($_REQUEST['abstract_child_type'])));
	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));
	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));
	$tags 									  = addslashes(trim($_REQUEST['report_data']));

	// INSERTING DELEGATE REQUEST PROCESS		
	$sqlUpdateDelegateDetails					  = array();
	$sqlUpdateDelegateDetails['QUERY']      	  = "UPDATE " . _DB_USER_REGISTRATION_ . "  
																	SET `user_department` = ?, 
																		`user_institute_name` = ?															
																  WHERE `id` = ?";

	$sqlUpdateDelegateDetails['PARAM'][]   = array('FILD' => 'user_department',    'DATA' => $abstract_presenter_department,     'TYP' => 's');
	$sqlUpdateDelegateDetails['PARAM'][]   = array('FILD' => 'user_institute_name', 'DATA' => $abstract_presenter_institute_name, 'TYP' => 's');
	$sqlUpdateDelegateDetails['PARAM'][]   = array('FILD' => 'id',                 'DATA' => $applicant_id,                      'TYP' => 's');

	$mycms->sql_update($sqlUpdateDelegateDetails, false);

	// INSERTING ABSTRACT REQUEST PROCESS
	$sqlInsertAbstractProcess                 = array();
	$sqlInsertAbstractProcess['QUERY']        = "INSERT INTO " . _DB_ABSTRACT_REQUEST_ . " 
																 SET `applicant_id` = '" . $applicant_id . "', 
																	 `applicant_registration_id` = '" . $applicant_registration_id . "', 
																	 `applicant_title` = '" . $applicant_initial_title . "',
																	 `abstract_topic_id` = '" . $abstract_topic_id . "', 
																	 `applicant_first_name` = '" . $applicant_first_name . "', 
																	 `applicant_middle_name` = '" . $applicant_middle_name . "', 
																	 `applicant_last_name` = '" . $applicant_last_name . "',
																	 `applicant_email_id` = '" . $applicant_email_id . "', 
																	 `applicant_mobile_isd_code` = '" . $applicant_mobile_isd_code . "',
																	 `applicant_mobile_no` = '" . $applicant_mobile_no . "',
																	 `applicant_phone_no` = '" . $applicant_phone_no . "', 
																	 `abstract_author_name` = '" . $abstract_author_name . "', 
																	 `abstract_author_department` = '" . $abstract_author_department . "', 
																	 `abstract_author_institute_name` = '" . $abstract_author_institute_name . "', 
																	 `abstract_author_country_id` = '" . $abstract_author_country . "', 
																	 `abstract_author_state_id` = '" . $abstract_author_state . "', 
																	 `abstract_author_city` = '" . $abstract_author_city . "', 																 
																	 `abstract_author_phone_code` = '" . $abstract_author_phone_code . "',																 
																	 `abstract_author_phone_no` = '" . $abstract_author_phone_no . "', 
																	 `abstract_parent_type` = '" . $abstract_parent_type . "',
																	 `abstract_child_type` = '" . $abstract_child_type . "', 
																	 `abstract_title` = '" . $abstract_title . "',
																	 `abstract_study` = '" . $abstract_study . "',
																	 `abstract_background` = '" . $abstract_background . "', 
																	 `abstract_background_aims` = '" . $abstract_background_aims . "', 
																	 `abstract_material_methods` = '" . $abstract_material_methods . "', 
																	 `abstract_results` = '" . $abstract_results . "', 
																	 `abstract_conclution` = '" . $abstract_conclusion . "', 
																	 `abstract_description` = '" . $abstract_description . "', 
																	 `tags` = '" . $tags . "',
																	 `status` = 'A',
																	 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																	 `created_sessionId` = '" . session_id() . "', 
																	 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";

	$lastInsertedAbstractId                   = $mycms->sql_insert($sqlInsertAbstractProcess, false);

	$_SESSION['ABSTRACT.SUBMISSION.ID']  	  = $lastInsertedAbstractId;


	$sqlUpdateUserDetails					  = array();
	$sqlUpdateUserDetails['QUERY']     	      = "UPDATE " . _DB_USER_REGISTRATION_ . "   
															SET `isAbstract` = ?
														  WHERE `id` = ?";

	$sqlUpdateUserDetails['PARAM'][]   = array('FILD' => 'isAbstract',    'DATA' => 'Y',              'TYP' => 's');
	$sqlUpdateUserDetails['PARAM'][]   = array('FILD' => 'id',            'DATA' => $applicant_id,    'TYP' => 's');

	$mycms->sql_update($sqlUpdateUserDetails, false);


	//ABSTRACT FREE PAPER MESSAGE SENDING


	// UPDATING ABSTRACT SUBMITION CODE
	$abstract_submition_code                  = $mycms->getRandom(4, 'num') . number_pad($lastInsertedAbstractId, 4);

	$sqlUpdateAbstractProcess                 = array();
	$sqlUpdateAbstractProcess['QUERY']        = "UPDATE " . _DB_ABSTRACT_REQUEST_ . "  
															SET `abstract_submition_code` = ?
														  WHERE `id` = ?";

	$sqlUpdateAbstractProcess['PARAM'][]   = array('FILD' => 'abstract_submition_code',    'DATA' => $abstract_submition_code,     'TYP' => 's');
	$sqlUpdateAbstractProcess['PARAM'][]   = array('FILD' => 'id',                         'DATA' => $lastInsertedAbstractId,      'TYP' => 's');

	$mycms->sql_update($sqlUpdateAbstractProcess, false);


	// INSERTING ABSTRACT COAUTHOR DETAILS
	$abstractCoAuthorArray                    = $_REQUEST['abstract_coauthor_name'];

	foreach ($abstractCoAuthorArray as $keyCoAuthor => $valCoAuthor) {
		$abstract_coauthor_name_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_name'][$keyCoAuthor])));
		$abstract_coauthor_department_val     = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_department'][$keyCoAuthor])));
		$abstract_coauthor_institute_name_val = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_name'][$keyCoAuthor])));
		$abstract_coauthor_country_val        = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_country'][$keyCoAuthor])));
		$abstract_coauthor_state_val          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_state'][$keyCoAuthor])));

		if ($abstract_coauthor_state_val == "") {
			$abstract_coauthor_state_val = 0;
		}

		$abstract_coauthor_city_val           = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_city'][$keyCoAuthor])));
		$abstract_coauthor_phone_no_val       = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_phone_code'][$keyCoAuthor] . " " . $_REQUEST['abstract_coauthor_phone_no'][$keyCoAuthor])));

		if ($abstract_coauthor_name_val != "") {
			// INSERT PROCESS
			$sqlInsertCoAuthor				  = array();
			$sqlInsertCoAuthor['QUERY']       = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . "  
																 SET `abstract_id` = ?, 
																	 `abstract_coauthor_name` = ?,
																	 `abstract_coauthor_department` = ?,
																	 `abstract_coauthor_institute_name` = ?,
																	 `abstract_coauthor_country_id` = ?,
																	 `abstract_coauthor_state_id` = ?,
																	 `abstract_coauthor_city_name` = ?,
																	 `abstract_coauthor_phone_no` = ?, 
																	 `status` = ?,
																	 `created_ip` =?, 
																	 `created_sessionId` = ?, 
																	 `created_dateTime` = ?";

			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_id',                      'DATA' => $lastInsertedAbstractId,               'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_name',           'DATA' => $abstract_coauthor_name_val,           'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_department',     'DATA' => $abstract_coauthor_department_val,     'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_institute_name', 'DATA' => $abstract_coauthor_institute_name_val, 'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_country_id',     'DATA' => $abstract_coauthor_country_val,        'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_state_id',       'DATA' => $abstract_coauthor_state_val,          'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_city_name',      'DATA' => $abstract_coauthor_city_val,           'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'abstract_coauthor_phone_no',       'DATA' => $abstract_coauthor_phone_no_val,       'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'status',                           'DATA' => 'A',                                   'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_ip',                       'DATA' => $_SERVER['REMOTE_ADDR'],               'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_sessionId',                'DATA' => session_id(),                          'TYP' => 's');
			$sqlInsertCoAuthor['PARAM'][]   = array('FILD' => 'created_dateTime',                 'DATA' => date('Y-m-d H:i:s'),                   'TYP' => 's');

			$mycms->sql_insert($sqlInsertCoAuthor, false);
		}
	}


	// INSERTING ABSTRACT FILE
	$abstractFiles                            = $_REQUEST['upload_case_file_name'];
	foreach ($abstractFiles as $key => $rowfetch) {
		$abstractFileFileName					  = addslashes(trim($applicantUniqueSequence . "_" . $abstract_submition_code . '_' . time() . '_' . $rowfetch, "#"));
		if ($rowfetch != '' && $abstractFileFileName != '') {

			$abstractFilePath                         = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;
			$abstractFilePathTmp                      = $cfg['FILES.ABSTRACT.REQUEST.TEMP'] . $rowfetch;
			rename($abstractFilePathTmp, $abstractFilePath);
			$sqlAbstractFile       = array();
			$sqlAbstractFile['QUERY']                      = "INSERT INTO " . _DB_CASE_STUDY_FILE_ . " 
																	SET `case_study_file`          = ?,
																		`case_study_original_file` = ?,
																		`delegate_id`              =?,
																		`case_stady_id`            =?";

			$sqlAbstractFile['PARAM'][]   = array('FILD' => 'case_study_file',           'DATA' => $abstractFileFileName,      'TYP' => 's');
			$sqlAbstractFile['PARAM'][]   = array('FILD' => 'case_study_original_file',  'DATA' => $rowfetch,                  'TYP' => 's');
			$sqlAbstractFile['PARAM'][]   = array('FILD' => 'delegate_id',               'DATA' => $applicantId,               'TYP' => 's');
			$sqlAbstractFile['PARAM'][]   = array('FILD' => 'case_stady_id',             'DATA' => $lastInsertedAbstractId,    'TYP' => 's');

			$mycms->sql_insert($sqlAbstractFile, false);
		}
	}


	return $abstract_submition_code;
}

function editCaseFileProcess_x()
{
	global $mycms, $cfg;

	$abstract_id 							 = 	addslashes(trim(strtoupper($_REQUEST['abstract_id'])));
	$delegate_id 							 = 	addslashes(trim(strtoupper($_REQUEST['delegateId'])));

	$abstract_edit_author_name 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_edit_author_name'])));
	$abstract_author_edit_country_id 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_country'])));
	$abstract_author_edit_state_id 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_state_id'])));
	$abstract_author_edit_city 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_city'])));
	$abstract_author_edit_phone_isd_code 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_isd_code'])));
	$abstract_author_edit_phone_no 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_no'])));
	$abstract_author_edit_institute_name 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_institute_name'])));
	$abstract_author_edit_department 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_department'])));

	$abstract_edit_topic_id 				 = 	0; //addslashes(trim(strtoupper($_REQUEST['abstract_edit_topic_id'])));
	$abstract_edit_study 					 = 	addslashes(trim($_REQUEST['abstract_edit_study']));
	$abstract_edit_title					 =	addslashes(trim(strtoupper($_REQUEST['abstract_edit_title'])));

	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));
	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));
	$caseFileRemainArrayid          	      = $_REQUEST['caseFileExistingId'];


	$sqlUpdateAbstractDtls['QUERY']             =  " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
												   SET 	`abstract_author_name`				= '" . $abstract_edit_author_name . "' ,
														`abstract_author_institute_name`	= '" . $abstract_author_edit_institute_name . "' ,
														`abstract_author_department`		= '" . $abstract_author_edit_department . "' ,
														`abstract_author_country_id`		= '" . $abstract_author_edit_country_id . "' ,
														`abstract_author_state_id`			= '" . $abstract_author_edit_state_id . "' ,
														`abstract_author_city`				= '" . $abstract_author_edit_city . "' ,
														`abstract_author_phone_code`		= '" . $abstract_author_edit_phone_isd_code . "' ,
														`abstract_author_phone_no`			= '" . $abstract_author_edit_phone_no . "' ,
														`abstract_topic_id`					= '" . $abstract_edit_topic_id . "' ,
														`abstract_study`					= '" . $abstract_edit_study . "' ,
														`abstract_title`					= '" . $abstract_edit_title . "' ,
														`abstract_background` 				= '" . $abstract_background . "', 
														 `abstract_background_aims` 		= '" . $abstract_background_aims . "', 
														 `abstract_material_methods` 		= '" . $abstract_material_methods . "', 
														 `abstract_results` 				= '" . $abstract_results . "', 
														 `abstract_conclution` 				= '" . $abstract_conclusion . "', 
														 `abstract_description` 			= '" . $abstract_description . "'															
												  WHERE `id` = '" . $abstract_id . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);

	$sqlUserDetails['QUERY']			=	"SELECT *
									  FROM " . _DB_USER_REGISTRATION_ . " 
										WHERE `id` = '" . $delegate_id . "' ";

	$rowUserDetails	=	$mycms->sql_select($sqlUserDetails, false);

	$applicantUniqueSequence				  = $rowUserDetails[0]['user_unique_sequence'];

	$sqlAbstractSubmission['QUERY']			= 	"SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
												WHERE `id` = '" . $abstract_id . "'  ";

	$sqlAbstractSubmission	=	$mycms->sql_select($sqlAbstractSubmission, false);

	$abstract_submition_code	=	$sqlAbstractSubmission[0]['abstract_submition_code'];


	// INSERTING ABSTRACT FILE
	$abstractFiles                            = $_REQUEST['upload_case_file_name'];
	foreach ($abstractFiles as $key => $rowfetch) {
		$abstractFileFileName					  = addslashes(trim($applicantUniqueSequence . "_" . $abstract_submition_code . '_' . time() . '_' . $rowfetch, "#"));
		if ($rowfetch != '' && $abstractFileFileName != '') {

			$abstractFilePath                         = $cfg['FILES.ABSTRACT.REQUEST'] . $abstractFileFileName;
			$abstractFilePathTmp                      = $cfg['FILES.ABSTRACT.REQUEST.TEMP'] . $rowfetch;
			rename($abstractFilePathTmp, $abstractFilePath);
			$sqlAbstractFile['QUERY']                      = "INSERT INTO " . $cfg['DB.CASE.STUDY.FILE'] . " 
															SET `case_study_file`          = '" . $abstractFileFileName . "',
																`case_study_original_file` = '" . $rowfetch . "',
																`delegate_id`              = '" . $delegate_id . "',
																`case_stady_id`            ='" . $abstract_id . "'";

			$mycms->sql_insert($sqlAbstractFile, false);
		}
	}

	$abstract_coauthor_edit_id	=	($_REQUEST['abstract_coauthor_edit_id']);

	foreach ($abstract_coauthor_edit_id as $keyCoAuthor => $idCoAuthor) {
		$abstract_coauthor_edit_name          	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_institute_edit_name 	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_edit_department 		= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_department'][$keyCoAuthor])));
		$abstract_coauthor_edit_country        	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_country'][$keyCoAuthor])));
		$abstract_coauthor__edit_state          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_state'][$keyCoAuthor])));
		$abstract_coauthor_edit_city           	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_city'][$keyCoAuthor])));
		$abstract_coauthor_edit_phone_no       	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_phone_no'][$keyCoAuthor])));


		// INSERT PROCESS
		if ($abstract_coauthor_edit_id != "") {
			$sqlUpdateCoAuthor['QUERY']          = "    UPDATE " . _DB_ABSTRACT_COAUTHOR_ . " 
															 SET `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
																 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
																 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
																 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
																 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
																 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
																 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "',
																 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																 `modified_sessionId` = '" . session_id() . "', 
																 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
														   WHERE `id` = '" . $idCoAuthor . "' ";

			$mycms->sql_update($sqlUpdateCoAuthor, false);
		}
	}
}

function editAbstractFileProcess_x()
{
	global $mycms, $cfg;

	$abstract_id 							 = 	addslashes(trim(strtoupper($_REQUEST['abstract_id'])));
	$delegate_id 							 = 	addslashes(trim(strtoupper($_REQUEST['delegateId'])));

	$abstract_edit_author_name 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_edit_author_name'])));
	$abstract_author_edit_country_id 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_country'])));
	$abstract_author_edit_state_id 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_state_id'])));
	$abstract_author_edit_city 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_city'])));
	$abstract_author_edit_phone_isd_code 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_isd_code'])));
	$abstract_author_edit_phone_no 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_no'])));
	$abstract_author_edit_institute_name 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_institute_name'])));
	$abstract_author_edit_department 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_department'])));

	$abstract_edit_topic_id 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_edit_topic_id'])));
	$abstract_edit_study 					 = 	addslashes(trim($_REQUEST['abstract_edit_study']));
	$abstract_edit_title					 =	addslashes(trim(strtoupper($_REQUEST['abstract_edit_title'])));

	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));
	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));


	$sqlUpdateAbstractDtls            = array();
	$sqlUpdateAbstractDtls['QUERY']   =  " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
												   SET 	`abstract_author_name`				= '" . $abstract_edit_author_name . "' ,
														`abstract_author_institute_name`	= '" . $abstract_author_edit_institute_name . "' ,
														`abstract_author_department`		= '" . $abstract_author_edit_department . "' ,
														`abstract_author_country_id`		= '" . $abstract_author_edit_country_id . "' ,
														`abstract_author_state_id`			= '" . $abstract_author_edit_state_id . "' ,
														`abstract_author_city`				= '" . $abstract_author_edit_city . "' ,
														`abstract_author_phone_code`		= '" . $abstract_author_edit_phone_isd_code . "' ,
														`abstract_author_phone_no`			= '" . $abstract_author_edit_phone_no . "' ,
														`abstract_topic_id`					= '" . $abstract_edit_topic_id . "' ,
														`abstract_study`					= '" . $abstract_edit_study . "' ,
														`abstract_title`					= '" . $abstract_edit_title . "' ,
														`abstract_background` 				= '" . $abstract_background . "', 
														 `abstract_background_aims` 		= '" . $abstract_background_aims . "', 
														 `abstract_material_methods` 		= '" . $abstract_material_methods . "', 
														 `abstract_results` 				= '" . $abstract_results . "', 
														 `abstract_conclution` 				= '" . $abstract_conclusion . "', 
														 `abstract_description` 			= '" . $abstract_description . "'															
												  WHERE `id` = '" . $abstract_id . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);

	$sqlUserDetails           = array();
	$sqlUserDetails['QUERY']  =	"SELECT *
									  FROM " . _DB_USER_REGISTRATION_ . " 
										WHERE `id` = '" . $delegate_id . "' ";

	$rowUserDetails	=	$mycms->sql_select($sqlUserDetails, false);

	$applicantUniqueSequence				  = $rowUserDetails[0]['user_unique_sequence'];

	$abstractFile                            = addslashes($_FILES['upload_abstract_edit_file']['name']);
	$abstractFileTempFile                    = $_FILES['upload_abstract_edit_file']['tmp_name'];

	$sqlAbstractSubmission   = array();
	$sqlAbstractSubmission['QUERY']		= 	"SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
													WHERE `id` = '" . $abstract_id . "'  ";

	$sqlAbstractSubmission	=	$mycms->sql_select($sqlAbstractSubmission, false);

	$abstract_submition_code	=	$sqlAbstractSubmission[0]['abstract_submition_code'];

	$abstractFileFileName                    = addslashes(trim($applicantUniqueSequence . "_" . $abstract_submition_code . '_' . time() . '_' . $abstractFile, "#"));

	$abstractFilePath                     	 = "uploads/FILES.ABSTRACT.REQUEST/" . $abstractFileFileName;

	if ($abstractFileTempFile != "") {
		chmod($abstractFilePath, 0777);
		copy($abstractFileTempFile, $abstractFilePath);
		chmod($abstractFilePath, 0777);

		$sqlUpdateAbstractFile  = array();
		$sqlUpdateAbstractFile['QUERY']     =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															 SET  `abstract_file`	 = '" . $abstractFileFileName . "' ,
																  `abstract_original_file_name` 	= '" . $abstractFile . "'
														    WHERE `id` = '" . $abstract_id . "' ";
		$mycms->sql_update($sqlUpdateAbstractFile, false);
	}

	$abstract_coauthor_edit_id	=	($_REQUEST['abstract_coauthor_edit_id']);

	foreach ($abstract_coauthor_edit_id as $keyCoAuthor => $idCoAuthor) {
		$abstract_coauthor_edit_name          	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_institute_edit_name 	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_edit_department 		= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_department'][$keyCoAuthor])));
		$abstract_coauthor_edit_country        	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_country'][$keyCoAuthor])));
		$abstract_coauthor__edit_state          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_state'][$keyCoAuthor])));
		$abstract_coauthor_edit_city           	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_city'][$keyCoAuthor])));
		$abstract_coauthor_edit_phone_no       	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_phone_no'][$keyCoAuthor])));

		// INSERT PROCESS
		if ($idCoAuthor != "") {
			$sqlUpdateCoAuthor  = array();
			$sqlUpdateCoAuthor['QUERY']        = "    UPDATE " . _DB_ABSTRACT_COAUTHOR_ . " 
													 SET `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
														 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
														 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
														 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
														 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
														 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
														 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "',
														 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `modified_sessionId` = '" . session_id() . "', 
														 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
												   WHERE `id` = '" . $idCoAuthor . "' ";

			$mycms->sql_update($sqlUpdateCoAuthor, false);
		} elseif ($abstract_coauthor_edit_name != '') {
			// INSERT PROCESS
			$sqlInsertCoAuthor  = array();
			$sqlInsertCoAuthor['QUERY']         = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . " 
															 SET `abstract_id` = '" . $abstract_id . "', 
																 `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
																 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
																 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
																 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
																 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
																 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
																 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "', 
																 `status` = 'A',
																 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																 `created_sessionId` = '" . session_id() . "', 
																 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
			$mycms->sql_insert($sqlInsertCoAuthor, false);
		}
	}
}
function editAbstractFileProcess()
{
	global $mycms, $cfg;
    // echo "<pre>";
	// print_r($_REQUEST);
	// die();
	$abstract_id 							 = 	addslashes(trim(strtoupper($_REQUEST['abstract_id'])));
	$delegate_id 							 = 	addslashes(trim(strtoupper($_REQUEST['delegateId'])));

	$applicant_full_name            		  = addslashes(trim(strtoupper($_REQUEST['abstract_edit_user_name'])));
	$applicant_email_id                       = addslashes(trim(strtoupper($_REQUEST['abstract_user_edit_email_id'])));
	$applicant_mobile_no                      = addslashes(trim(strtoupper($_REQUEST['abstract_user_edit_phone_no'])));

	$abstract_edit_author_email				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_email_id'])));
	$abstract_edit_author_name 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_edit_author_name'])));
	$abstract_author_edit_country_id 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_country'])));
	$abstract_author_edit_state_id 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_state_id'])));
	$abstract_author_edit_city 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_city'])));
	$abstract_author_edit_phone_isd_code 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_isd_code'])));
	$abstract_author_edit_phone_no 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_no'])));
	$abstract_author_edit_institute_name 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_institute_name'])));
	$abstract_author_edit_department 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_department'])));

	$abstract_edit_topic_id                   	  = (!empty($_REQUEST['abstract_edit_topic_id'])) ? ($_REQUEST['abstract_edit_topic_id']) : "0";
	$abstract_edit_study 					 = 	addslashes(trim($_REQUEST['abstract_edit_study']));
	$abstract_edit_title					 =	addslashes(trim(strtoupper($_REQUEST['abstract_edit_title'])));

	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));
	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));

	$fields1 						 = (!empty($_REQUEST['abstract_edit_fields1'])) ? addslashes(trim($_REQUEST['abstract_edit_fields1'])) : 'NULL';
	$fields2 						 = (!empty($_REQUEST['abstract_edit_fields2'])) ? addslashes(trim($_REQUEST['abstract_edit_fields2'])) : 'NULL';
	$fields3 						 = (!empty($_REQUEST['abstract_edit_fields3'])) ? addslashes(trim($_REQUEST['abstract_edit_fields3'])) : 'NULL';
	$fields4 						 = (!empty($_REQUEST['abstract_edit_fields4'])) ? addslashes(trim($_REQUEST['abstract_edit_fields4'])) : 'NULL';
	$fields5 						 = (!empty($_REQUEST['abstract_edit_fields5'])) ? addslashes(trim($_REQUEST['abstract_edit_fields5'])) : 'NULL';
	$fields6 						 = (!empty($_REQUEST['abstract_edit_fields6'])) ? addslashes(trim($_REQUEST['abstract_edit_fields6'])) : 'NULL';
	$fields7 						 = (!empty($_REQUEST['abstract_edit_fields7'])) ? addslashes(trim($_REQUEST['abstract_edit_fields7'])) : 'NULL';
	$fields8 						 = (!empty($_REQUEST['abstract_edit_fields8'])) ? addslashes(trim($_REQUEST['abstract_edit_fields8'])) : 'NULL';
	$fields9 						 = (!empty($_REQUEST['abstract_edit_fields9'])) ? addslashes(trim($_REQUEST['abstract_edit_fields9'])) : 'NULL';
   	$fields10 						 = (!empty($_REQUEST['abstract_edit_fields10'])) ? addslashes(trim($_REQUEST['abstract_edit_fields10'])) : 'NULL';
	$fields11						 = (!empty($_REQUEST['abstract_edit_fields11'])) ? addslashes(trim($_REQUEST['abstract_edit_fields11'])) : 'NULL';

	$sqlUpdateAbstractDtls            = array();
	$sqlUpdateAbstractDtls['QUERY']   =  " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
												   SET 	`abstract_author_name`				= '" . $abstract_edit_author_name . "' ,
														`abstract_author_institute_name`	= '" . $abstract_author_edit_institute_name . "' ,
														`abstract_author_department`		= '" . $abstract_author_edit_department . "' ,
														`abstract_author_country_id`		= '" . $abstract_author_edit_country_id . "' ,
														`abstract_author_state_id`			= '" . $abstract_author_edit_state_id . "' ,
														`abstract_author_city`				= '" . $abstract_author_edit_city . "' ,
														`abstract_author_phone_code`		= '" . $abstract_author_edit_phone_isd_code . "' ,
														`abstract_author_phone_no`			= '" . $abstract_author_edit_phone_no . "' ,
														`abstract_topic_id`					= '" . $abstract_edit_topic_id . "' ,
														`abstract_study`					= '" . $abstract_edit_study . "' ,
														`abstract_title`					= '" . $abstract_edit_title . "' ,
														`abstract_background` 				= '" . $abstract_background . "', 
														 `abstract_background_aims` 		= '" . $abstract_background_aims . "', 
														 `abstract_material_methods` 		= '" . $abstract_material_methods . "', 
														 `abstract_results` 				= '" . $abstract_results . "', 
														 `abstract_conclution` 				= '" . $abstract_conclusion . "', 
														 `abstract_description` 			= '" . $abstract_description . "',
														 `fields1` = '" . $fields1 . "',
														`fields2` = '" . $fields2 . "',
														`fields3` = '" . $fields3 . "',
														`fields4` = '" . $fields4 . "',
														`fields5` = '" . $fields5 . "',
														`fields6` = '" . $fields6 . "',
														`fields7` = '" . $fields7 . "', 
														`fields8` = '" . $fields8 . "', 
														`fields9` = '" . $fields9 . "',
														`fields10` = '" . $fields10 . "',
														`fields11` = '" . $fields11 . "',
														`applicant_email_id` = '" . $applicant_email_id . "',
														`applicant_mobile_no` = '" . $applicant_mobile_no . "', 
														`abstract_author_email_id` = '" . $abstract_edit_author_email . "'															
												  WHERE `id` = '" . $abstract_id . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);
	////////////////////////user update/////////////////
	$sqlUpdate = [];
	$sqlUpdate['QUERY'] = "
        UPDATE " . _DB_USER_REGISTRATION_ . "
        SET user_full_name = ?,
			user_email_id = ?,
			user_mobile_no = ?,
            modified_ip = ?,
            modified_sessionId = ?,
            modified_browser = ?,
            modified_dateTime = ?
        WHERE id = ?
    ";

	$sqlUpdate['PARAM'] = [
	
		['FILD' => 'user_full_name', 'DATA' => $applicant_full_name, 'TYP' => 's'],
		['FILD' => 'user_email_id', 'DATA' => $applicant_email_id, 'TYP' => 's'],
		['FILD' => 'user_mobile_no', 'DATA' => $applicant_mobile_no, 'TYP' => 's'],
		['FILD' => 'modified_ip', 'DATA' => ($_SERVER['REMOTE_ADDR'] ?? ''), 'TYP' => 's'],
		['FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's'],
		['FILD' => 'modified_browser', 'DATA' => ($_SERVER['HTTP_USER_AGENT'] ?? ''), 'TYP' => 's'],
		['FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'],
		['FILD' => 'id', 'DATA' => $delegate_id, 'TYP' => 'i'],
	];
	if($applicant_full_name!='' && $applicant_email_id!='' && $applicant_mobile_no!=''){
        $mycms->sql_update($sqlUpdate);
	}
	/////////////////////////////////////////////////
    if($_REQUEST['proofingEdit']=='yes'){
		$proof_abstract_title							= addslashes(trim($_REQUEST['proof_abstract_title']));
		$proof_fields1 = (!empty($_REQUEST['proof_fields1'])) ? addslashes(trim($_REQUEST['proof_fields1'])) : 'NULL';
		$proof_fields2 = (!empty($_REQUEST['proof_fields2'])) ? addslashes(trim($_REQUEST['proof_fields2'])) : 'NULL';
		$proof_fields3 = (!empty($_REQUEST['proof_fields3'])) ? addslashes(trim($_REQUEST['proof_fields3'])) : 'NULL';
		$proof_fields4 = (!empty($_REQUEST['proof_fields4'])) ? addslashes(trim($_REQUEST['proof_fields4'])) : 'NULL';
		$proof_fields5 = (!empty($_REQUEST['proof_fields5'])) ? addslashes(trim($_REQUEST['proof_fields5'])) : 'NULL';
		$proof_fields6 = (!empty($_REQUEST['proof_fields6'])) ? addslashes(trim($_REQUEST['proof_fields6'])) : 'NULL';
		$proof_fields7 = (!empty($_REQUEST['proof_fields7'])) ? addslashes(trim($_REQUEST['proof_fields7'])) : 'NULL';
		$proof_fields8 = (!empty($_REQUEST['proof_fields8'])) ? addslashes(trim($_REQUEST['proof_fields8'])) : 'NULL';
		$proof_fields9 = (!empty($_REQUEST['proof_fields9'])) ? addslashes(trim($_REQUEST['proof_fields9'])) : 'NULL';
		$proof_fields10 = (!empty($_REQUEST['proof_fields10'])) ? addslashes(trim($_REQUEST['proof_fields10'])) : 'NULL';
		$proof_fields11 = (!empty($_REQUEST['proof_fields11'])) ? addslashes(trim($_REQUEST['proof_fields11'])) : 'NULL';

		$sqlUpdateAbstractProofing = array();
		$sqlUpdateAbstractProofing['QUERY']         =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
														SET `proof_abstract_title`		= '" . $proof_abstract_title . "' , 
															`proof_fields1`		= '" . $proof_fields1 . "' ,
															`proof_fields2` 		= '" . $proof_fields2 . "', 
															`proof_fields3` 		= '" . $proof_fields3 . "', 
															`proof_fields4` 		= '" . $proof_fields4 . "', 
															`proof_fields5` 		= '" . $proof_fields5 . "', 
															`proof_fields6` 		= '" . $proof_fields6 . "', 
															`proof_fields7` 		= '" . $proof_fields7 . "',	
															`proof_fields8` 		= '" . $proof_fields8 . "',				
															`proof_fields9` 		= '" . $proof_fields9 . "',				
															`proof_fields10` 		= '" . $proof_fields10 . "',		
														    `proof_fields11` 		= '" . $proof_fields11 . "'																	
																
														WHERE `id` = '" . $abstract_id . "'  ";
		$mycms->sql_update($sqlUpdateAbstractProofing, false);
	}
	$sqlUserDetails           = array();
	$sqlUserDetails['QUERY']  =	"SELECT *
									  FROM " . _DB_USER_REGISTRATION_ . " 
										WHERE `id` = '" . $delegate_id . "' ";

	$rowUserDetails	=	$mycms->sql_select($sqlUserDetails, false);

	$applicantUniqueSequence				  = $rowUserDetails[0]['user_unique_sequence'];

	$abstractFile                            = addslashes($_FILES['upload_abstract_edit_file']['name']);
	$abstractFileTempFile                    = $_FILES['upload_abstract_edit_file']['tmp_name'];

	$sqlAbstractSubmission   = array();
	$sqlAbstractSubmission['QUERY']		= 	"SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
													WHERE `id` = '" . $abstract_id . "'  ";

	$sqlAbstractSubmission	=	$mycms->sql_select($sqlAbstractSubmission, false);

	$abstract_submition_code	=	$sqlAbstractSubmission[0]['abstract_submition_code'];

	$abstractFileFileName                    = addslashes(trim($applicantUniqueSequence . "_" . $abstract_submition_code . '_' . time() . '_' . $abstractFile, "#"));

	$abstractFilePath                     	 = "uploads/FILES.ABSTRACT.REQUEST/" . $abstractFileFileName;

	if ($abstractFileTempFile != "") {
		chmod($abstractFilePath, 0777);
		copy($abstractFileTempFile, $abstractFilePath);
		chmod($abstractFilePath, 0777);

		$sqlUpdateAbstractFile  = array();
		$sqlUpdateAbstractFile['QUERY']     =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
															 SET  `abstract_file`	 = '" . $abstractFileFileName . "' ,
																  `abstract_original_file_name` 	= '" . $abstractFile . "'
														    WHERE `id` = '" . $abstract_id . "' ";
		$mycms->sql_update($sqlUpdateAbstractFile, false);
	}

	$abstract_coauthor_edit_id	=	($_REQUEST['abstract_coauthor_edit_id']);

	foreach ($abstract_coauthor_edit_id as $keyCoAuthor => $idCoAuthor) {
		$abstract_coauthor_edit_name          	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_institute_edit_name 	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_edit_department 		= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_department'][$keyCoAuthor])));
		$abstract_coauthor_edit_country        	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_country'][$keyCoAuthor])));
		$abstract_coauthor__edit_state          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_state'][$keyCoAuthor])));
		$abstract_coauthor_edit_city           	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_city'][$keyCoAuthor])));
		$abstract_coauthor_edit_phone_no       	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_phone_no'][$keyCoAuthor])));
		$abstract_coauthor_edit_email       	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_email'][$keyCoAuthor])));

		// INSERT PROCESS
		if ($idCoAuthor != "") {
			$sqlUpdateCoAuthor  = array();
			$sqlUpdateCoAuthor['QUERY']        = "    UPDATE " . _DB_ABSTRACT_COAUTHOR_ . " 
													 SET `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
														 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
														 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
														 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
														 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
														 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
														 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "',
														 `abstract_coauthor_email` = '" . $abstract_coauthor_edit_email . "',
														 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `modified_sessionId` = '" . session_id() . "', 
														 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
												   WHERE `id` = '" . $idCoAuthor . "' ";

			$mycms->sql_update($sqlUpdateCoAuthor, false);
		} elseif ($abstract_coauthor_edit_name != '') {
			// INSERT PROCESS
			$sqlInsertCoAuthor  = array();
			$sqlInsertCoAuthor['QUERY']         = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . " 
															 SET `abstract_id` = '" . $abstract_id . "', 
																 `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
																 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
																 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
																 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
																 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
																 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
																 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "', 
																  `abstract_coauthor_email` = '" . $abstract_coauthor_edit_email . "',
																 `status` = 'A',
																 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																 `created_sessionId` = '" . session_id() . "', 
																 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
			$mycms->sql_insert($sqlInsertCoAuthor, false);
		}
	}
	$sqlUpdateAbstractFile  = array();
	$sqlUpdateAbstractFile['QUERY']     =  "UPDATE " . _DB_AWARD_REQUEST_ . " 
															SET  `status`	 = 'D'
														WHERE `submission_id` = '" . $abstract_id . "' ";
	$mycms->sql_update($sqlUpdateAbstractFile, false);

	if (isset($_REQUEST['award_request'])) {
		$awardId=$_REQUEST['award_request'];
		// foreach ($_REQUEST['award_request'] as $awardId => $nomination) {
		// 	if ($nomination == 'Y') {
				$awardFileTempFile                     =$_REQUEST['upload_nomination_file'];

				$sqlAbstractAward			  =	array();
				$sqlAbstractAward['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
													  WHERE `id` = ?";
				$sqlAbstractAward['PARAM'][]  = array('FILD' => 'is', 'DATA' => $awardId,  'TYP' => 's');
				$resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
				$rowAbstractAward	 = $resultAbstractAward[0];
	            $applicant_full_name = $sqlAbstractSubmission[0]['applicant_title'] . " " .  $sqlAbstractSubmission[0]['applicant_first_name'] ." " . $sqlAbstractSubmission[0]['applicant_middle_name'] . " " . $sqlAbstractSubmission[0]['applicant_last_name'];

				$sql				  = array();
				$sql['QUERY']         = "INSERT INTO " . _DB_AWARD_REQUEST_ . " 
													 SET `submission_id` = '" . $abstract_id . "', 
														 `submission_code` = '" . $abstract_submition_code . "', 
														 `abstract_parent_type` = '" . $sqlAbstractSubmission[0]['abstract_cat'] . "',
														 `abstract_child_type` = '" . $sqlAbstractSubmission[0]['abstract_category']. "', 
														 `abstract_topic_id` = '" . $sqlAbstractSubmission[0]['abstract_topic_id'] . "', 
														 `applicant_id` = '" . $sqlAbstractSubmission[0]['applicant_id']  . "', 
														 `applicant_name` = '" . $applicant_full_name . "',
														 `award_id` = '" . $awardId . "', 
														 `award_title` = '" . $rowAbstractAward['award_name'] . "',	
														 `upload_nomination_file` = '" . $awardFileTempFile. "',														 
														 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `created_sessionId` = '" . session_id() . "', 
														 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
				$awardRequestId       = $mycms->sql_insert($sql, false);
		// 	}
		// }
	}
}
function editAbstractFileProcessFront()
{
	global $mycms, $cfg;
   
	$abstract_id 							 = 	addslashes(trim(strtoupper($_REQUEST['abstract_id'])));
	$delegate_id 							 = 	addslashes(trim(strtoupper($_REQUEST['delegateId'])));
    $abstract_author_edit_email              = $_REQUEST['abstract_author_edit_email'];
	$abstract_author_edit_title              = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_title'])));
	$abstract_author_edit_first_name         =   addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_first_name'])));
	$abstract_author_edit_middle_name         =   addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_middle_name'])));
    $abstract_author_edit_last_name          =   addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_last_name'])));
	$abstract_author_edit_address 			= 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_address'])));
	$abstract_author_edit_country_id 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_country'])));
	$abstract_author_edit_state_id 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_state_id'])));
	$abstract_author_edit_city 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_city'])));
	$abstract_author_edit_phone_isd_code 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_code'])));
	$abstract_author_edit_phone_no 			 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_phone_no'])));
	$abstract_author_edit_institute_name 	 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_institute_name'])));
	$abstract_author_edit_department 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_department'])));
	$abstract_author_edit_pincode 		 = 	addslashes(trim(strtoupper($_REQUEST['abstract_author_edit_pincode'])));

	// $abstract_edit_topic_id 				 = 	addslashes(trim(strtoupper($_REQUEST['abstract_edit_topic_id'])));

	$abstract_edit_topic_id                   	  = (!empty($_REQUEST['abstract_edit_topic_id'])) ? ($_REQUEST['abstract_edit_topic_id']) : "0";

	$abstract_edit_study 					 = 	addslashes(trim($_REQUEST['abstract_edit_study']));
	$abstract_edit_title					 =	addslashes(trim(strtoupper($_REQUEST['abstract_edit_title'])));

	$abstract_background                 	  = addslashes(trim($_REQUEST['abstract_background']));
	$abstract_background_aims                 = addslashes(trim($_REQUEST['abstract_background_aims']));
	$abstract_material_methods                = addslashes(trim($_REQUEST['abstract_material_methods']));
	$abstract_results             			  = addslashes(trim($_REQUEST['abstract_results']));
	$abstract_conclusion              		  = addslashes(trim($_REQUEST['abstract_conclusion']));
	$abstract_references   					  = addslashes(trim($_REQUEST['abstract_references']));
	$abstract_description   				  = addslashes(trim($_REQUEST['abstract_description']));

	$abstract_edit_author_name = $abstract_author_edit_title . " " . $abstract_author_edit_first_name ." " . $abstract_author_edit_middle_name . " " . $abstract_author_edit_last_name;

	$fields1 						 = (!empty($_REQUEST['abstract_edit_fields1'])) ? addslashes(trim($_REQUEST['abstract_edit_fields1'])) : 'NULL';
	$fields2 						 = (!empty($_REQUEST['abstract_edit_fields2'])) ? addslashes(trim($_REQUEST['abstract_edit_fields2'])) : 'NULL';
	$fields3 						 = (!empty($_REQUEST['abstract_edit_fields3'])) ? addslashes(trim($_REQUEST['abstract_edit_fields3'])) : 'NULL';
	$fields4 						 = (!empty($_REQUEST['abstract_edit_fields4'])) ? addslashes(trim($_REQUEST['abstract_edit_fields4'])) : 'NULL';
	$fields5 						 = (!empty($_REQUEST['abstract_edit_fields5'])) ? addslashes(trim($_REQUEST['abstract_edit_fields5'])) : 'NULL';
	$fields6 						 = (!empty($_REQUEST['abstract_edit_fields6'])) ? addslashes(trim($_REQUEST['abstract_edit_fields6'])) : 'NULL';
	$fields7 						 = (!empty($_REQUEST['abstract_edit_fields7'])) ? addslashes(trim($_REQUEST['abstract_edit_fields7'])) : 'NULL';
	$fields8 						 = (!empty($_REQUEST['abstract_edit_fields8'])) ? addslashes(trim($_REQUEST['abstract_edit_fields8'])) : 'NULL';
	$fields9 						 = (!empty($_REQUEST['abstract_edit_fields9'])) ? addslashes(trim($_REQUEST['abstract_edit_fields9'])) : 'NULL';
	$fields10 						 = (!empty($_REQUEST['abstract_edit_fields10'])) ? addslashes(trim($_REQUEST['abstract_edit_fields10'])) : 'NULL';
	$fields11						 = (!empty($_REQUEST['abstract_edit_fields11'])) ? addslashes(trim($_REQUEST['abstract_edit_fields11'])) : 'NULL';

	/////////////////////////Documents//////////////////
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];

	if ($_POST['delete_abstract_file'] == 1) {

		// DELETE
		if (!empty($_POST['original_abstract_file_name'])) {
			@unlink($uploadDir . $_POST['original_abstract_file_name']);
		}
		$_REQUEST['upload_abstract_file'] = '';

	}
	if (!empty($_FILES['upload_abstract_file']['name'])) {

		// NEW UPLOAD
		$tempFile = $_FILES['upload_abstract_file']['tmp_name'];
		$fileName = "DOC_" . date('ymdHis') . "_" . rand(1000,9999) . "_" . $_FILES['upload_abstract_file']['name'];

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$_REQUEST['upload_abstract_file'] = $fileName;
		}

	} else {

		// KEEP OLD
		$_REQUEST['upload_abstract_file'] = $_POST['original_abstract_file_name'] ?? '';
	}
	
	//////////////////HOD/////////////
	if ($_POST['delete_consent_file'] == 1) {

    if (!empty($_POST['original_consent_file_name'])) {
        @unlink($uploadDir . $_POST['original_consent_file_name']);
    }
    $_REQUEST['upload_consent_abstract_file'] = '';

	} 
	if (!empty($_FILES['upload_consent_abstract_file']['name'])) {

		$tempFile = $_FILES['upload_consent_abstract_file']['tmp_name'];
		$fileName = "DOC_" . date('ymdHis') . "_" . rand(1000,9999) . "_" . $_FILES['upload_consent_abstract_file']['name'];

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$_REQUEST['upload_consent_abstract_file'] = $fileName;
		}

	} else {

		$_REQUEST['upload_consent_abstract_file'] = $_POST['original_consent_file_name'] ?? '';
	}
	///////////////////Category file///////////
	if ($_POST['delete_category_file'] == 1) {

		if (!empty($_POST['original_category_file_name'])) {
			@unlink($uploadDir . $_POST['original_category_file_name']);
		}
		$_REQUEST['category_required_file'] = '';

	} 
	if (!empty($_FILES['category_required_file']['name'])) {

		$tempFile = $_FILES['category_required_file']['tmp_name'];
		$fileName = "DOC_" . date('ymdHis') . "_" . rand(1000,9999) . "_" . $_FILES['category_required_file']['name'];

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$_REQUEST['category_required_file'] = $fileName;
		}

	} else {

		$_REQUEST['category_required_file'] = $_POST['original_category_file_name'] ?? '';
	}
	/////////////////////////////////////////////////////////////
 
	$sqlUpdateAbstractDtls            = array();
	$sqlUpdateAbstractDtls['QUERY']   =  " UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
												   SET 	`abstract_author_name`				= '" . $abstract_edit_author_name . "' ,
														`abstract_author_institute_name`	= '" . $abstract_author_edit_institute_name . "' ,
														`abstract_author_department`		= '" . $abstract_author_edit_department . "' ,
														`abstract_author_country_id`		= '" . $abstract_author_edit_country_id . "' ,
														`abstract_author_state_id`			= '" . $abstract_author_edit_state_id . "' ,
														`abstract_author_city`				= '" . $abstract_author_edit_city . "' ,
														`abstract_author_phone_code`		= '" . $abstract_author_edit_phone_isd_code . "' ,
														`abstract_author_phone_no`			= '" . $abstract_author_edit_phone_no . "' ,
														`abstract_topic_id`					= '" . $abstract_edit_topic_id . "' ,
														`abstract_study`					= '" . $abstract_edit_study . "' ,
														`abstract_title`					= '" . $abstract_edit_title . "' ,
														`abstract_background` 				= '" . $abstract_background . "', 
														 `abstract_background_aims` 		= '" . $abstract_background_aims . "', 
														 `abstract_material_methods` 		= '" . $abstract_material_methods . "', 
														 `abstract_results` 				= '" . $abstract_results . "', 
														 `abstract_conclution` 				= '" . $abstract_conclusion . "', 
														 `abstract_description` 			= '" . $abstract_description . "',
														  `abstract_author_title` 			= '" . $abstract_author_edit_title . "',
														   `abstract_author_first_name` 			= '" . $abstract_author_edit_first_name . "',
														    `abstract_author_middle_name` 			= '" . $abstract_author_edit_middle_name . "',
															 `abstract_author_last_name` 			= '" . $abstract_author_edit_last_name . "',
															 `abstract_author_address` 			= '" . $abstract_author_edit_address . "',
													      	 `abstract_author_pin` 			= '" . $abstract_author_edit_pincode . "',
														 `fields1` = '" . $fields1 . "',
														`fields2` = '" . $fields2 . "',
														`fields3` = '" . $fields3 . "',
														`fields4` = '" . $fields4 . "',
														`fields5` = '" . $fields5 . "',
														`fields6` = '" . $fields6 . "',
														`fields7` = '" . $fields7 . "', 
														`fields8` = '" . $fields8 . "', 
														`fields9` = '" . $fields9 . "',
														`fields10` = '" . $fields10 . "',
														`fields11` = '" . $fields11 . "',
														`abstract_file` = '" . $_REQUEST['upload_abstract_file'] . "',
														`abstract_original_file_name` = '" . $_REQUEST['upload_abstract_file'] . "',
														`abstract_consent_file` = '" . $_REQUEST['upload_consent_abstract_file'] . "',
														`abstract_consent_original_file_name` = '" . $_REQUEST['upload_consent_abstract_file'] . "',
														`category_required_file` = '" . $_REQUEST['category_required_file'] . "'

												  WHERE `id` = '" . $abstract_id . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);

	$sqlUserDetails           = array();
	$sqlUserDetails['QUERY']  =	"SELECT *
									  FROM " . _DB_USER_REGISTRATION_ . " 
										WHERE `id` = '" . $delegate_id . "' ";

	$rowUserDetails	=	$mycms->sql_select($sqlUserDetails, false);

	$applicantUniqueSequence				  = $rowUserDetails[0]['user_unique_sequence'];

	$abstractFile                            = addslashes($_FILES['upload_abstract_edit_file']['name']);
	$abstractFileTempFile                    = $_FILES['upload_abstract_edit_file']['tmp_name'];

	$sqlAbstractSubmission   = array();
	$sqlAbstractSubmission['QUERY']		= 	"SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
													WHERE `id` = '" . $abstract_id . "'  ";

	$sqlAbstractSubmission	=	$mycms->sql_select($sqlAbstractSubmission, false);

	$abstract_submition_code	=	$sqlAbstractSubmission[0]['abstract_submition_code'];

	// $abstractFileFileName                    = addslashes(trim($applicantUniqueSequence . "_" . $abstract_submition_code . '_' . time() . '_' . $abstractFile, "#"));

	// $abstractFilePath                     	 = "uploads/FILES.ABSTRACT.REQUEST/" . $abstractFileFileName;

	// if ($abstractFileTempFile != "") {
	// 	chmod($abstractFilePath, 0777);
	// 	copy($abstractFileTempFile, $abstractFilePath);
	// 	chmod($abstractFilePath, 0777);

	// 	$sqlUpdateAbstractFile  = array();
	// 	$sqlUpdateAbstractFile['QUERY']     =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
	// 														 SET  `abstract_file`	 = '" . $abstractFileFileName . "' ,
	// 															  `abstract_original_file_name` 	= '" . $abstractFile . "'
	// 													    WHERE `id` = '" . $abstract_id . "' ";
	// 	$mycms->sql_update($sqlUpdateAbstractFile, false);
	// }

	$abstract_coauthor_edit_id	=	($_REQUEST['abstract_coauthor_edit_id']);

	foreach ($abstract_coauthor_edit_id as $keyCoAuthor => $idCoAuthor) {
		$abstract_coauthor_institute_edit_name 	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_institute_edit_name'][$keyCoAuthor])));
		$abstract_coauthor_edit_department 		= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_department'][$keyCoAuthor])));
		$abstract_coauthor_edit_country        	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_country'][$keyCoAuthor])));
		$abstract_coauthor__edit_state          = addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_state'][$keyCoAuthor])));
		$abstract_coauthor_edit_city           	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_city'][$keyCoAuthor])));
		$abstract_coauthor_edit_phone_no       	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_phone_no'][$keyCoAuthor])));
		$abstract_coauthor_edit_email       	= $_REQUEST['abstract_coauthor_edit_email'][$keyCoAuthor];
		$abstract_coauthor_edit_phone_code     = $_REQUEST['abstract_coauthor_edit_phone_code'][$keyCoAuthor];
		$abstract_coauthor_edit_title       	= addslashes(trim(strtoupper($_REQUEST['abstract_coauthor_edit_title'][$keyCoAuthor])));
		$abstract_coauthor_edit_email       	= $_REQUEST['abstract_coauthor_edit_email'][$keyCoAuthor];
		$abstract_coauthor_edit_first_name    = $_REQUEST['abstract_coauthor_edit_first_name'][$keyCoAuthor];
		$abstract_coauthor_edit_middle_name   = $_REQUEST['abstract_coauthor_edit_middle_name'][$keyCoAuthor];
		$abstract_coauthor_edit_last_name    = $_REQUEST['abstract_coauthor_edit_last_name'][$keyCoAuthor];
		$abstract_coauthor_edit_address    =  addslashes(trim($_REQUEST['abstract_coauthor_edit_address'][$keyCoAuthor]));
		$abstract_coauthor_pincode    = $_REQUEST['abstract_coauthor_pincode'][$keyCoAuthor];
	    $abstract_coauthor_edit_name = $abstract_coauthor_edit_title . " " . $abstract_coauthor_edit_first_name ." " . $abstract_coauthor_edit_middle_name . " " . $abstract_coauthor_edit_last_name;

		// INSERT PROCESS
		if ($idCoAuthor != "") {
			$sqlUpdateCoAuthor  = array();
			$sqlUpdateCoAuthor['QUERY']        = "    UPDATE " . _DB_ABSTRACT_COAUTHOR_ . " 
													 SET `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
														 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
														 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
														 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
														 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
														 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
														 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "',
														  `abstract_coauthor_email` = '" . $abstract_coauthor_edit_email . "',
														  `abstract_coauthor_phone_code` = '" . $abstract_coauthor_edit_phone_code . "',
														`abstract_coauthor_title` = '" . $abstract_coauthor_edit_title . "',
														`abstract_coauthor_first_name` = '" . $abstract_coauthor_edit_first_name . "',
														`abstract_coauthor_middle_name` = '" . $abstract_coauthor_edit_middle_name . "',
														 `abstract_coauthor_last_name` = '" . $abstract_coauthor_edit_last_name . "',
														 `abstract_coauthor_address` = '" . $abstract_coauthor_edit_address . "',
														`abstract_coauthor_pincode` = '" . $abstract_coauthor_pincode . "',

														 `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `modified_sessionId` = '" . session_id() . "', 
														 `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
												   WHERE `id` = '" . $idCoAuthor . "' ";

			$mycms->sql_update($sqlUpdateCoAuthor, false);
		} elseif ($abstract_coauthor_edit_name != '') {
			// INSERT PROCESS
			$sqlInsertCoAuthor  = array();
			$sqlInsertCoAuthor['QUERY']         = "INSERT INTO " . _DB_ABSTRACT_COAUTHOR_ . " 
															 SET `abstract_id` = '" . $abstract_id . "', 
																 `abstract_coauthor_name` = '" . $abstract_coauthor_edit_name . "',
																 `abstract_coauthor_department` = '" . $abstract_coauthor_edit_department . "',
																 `abstract_coauthor_institute_name` = '" . $abstract_coauthor_institute_edit_name . "',
																 `abstract_coauthor_country_id` = '" . $abstract_coauthor_edit_country . "',
																 `abstract_coauthor_state_id` = '" . $abstract_coauthor__edit_state . "',
																 `abstract_coauthor_city_name` = '" . $abstract_coauthor_edit_city . "',
																 `abstract_coauthor_phone_no` = '" . $abstract_coauthor_edit_phone_no . "', 
																  `abstract_coauthor_email` = '" . $abstract_coauthor_edit_email . "',
																`abstract_coauthor_phone_code` = '" . $abstract_coauthor_edit_phone_code . "',
																`abstract_coauthor_title` = '" . $abstract_coauthor_edit_title . "',
																`abstract_coauthor_first_name` = '" . $abstract_coauthor_edit_first_name . "',
																`abstract_coauthor_middle_name` = '" . $abstract_coauthor_edit_middle_name . "',
																`abstract_coauthor_last_name` = '" . $abstract_coauthor_edit_last_name . "',
																`abstract_coauthor_address` = '" . $abstract_coauthor_edit_address . "',
																`abstract_coauthor_pincode` = '" . $abstract_coauthor_pincode . "',

																 `status` = 'A',
																 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
																 `created_sessionId` = '" . session_id() . "', 
																 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
			$mycms->sql_insert($sqlInsertCoAuthor, false);
		}
	}

	//////////////////nomination////////////
	if ($_POST['delete_nomination_file'] == 1) {

    if (!empty($_POST['original_nomination_file_name'])) {
        @unlink($uploadDir . $_POST['original_nomination_file_name']);
    }
    $_REQUEST['upload_nomination_file'] = '';

	}
	if (!empty($_FILES['upload_nomination_file']['name'])) {

		$tempFile = $_FILES['upload_nomination_file']['tmp_name'];
		$fileName = "DOC_" . date('ymdHis') . "_" . rand(1000,9999) . "_" . $_FILES['upload_nomination_file']['name'];

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$_REQUEST['upload_nomination_file'] = $fileName;
		}

	} else {

		$_REQUEST['upload_nomination_file'] = $_POST['original_nomination_file_name'] ?? '';
	}
	$sqlUpdateAbstractFile  = array();
	$sqlUpdateAbstractFile['QUERY']     =  "UPDATE " . _DB_AWARD_REQUEST_ . " 
															SET  `status`	 = 'D'
														WHERE `submission_id` = '" . $abstract_id . "' ";
	$mycms->sql_update($sqlUpdateAbstractFile, false);

	if (isset($_REQUEST['award_request'])) {
		$awardId=$_REQUEST['award_request'];
		// foreach ($_REQUEST['award_request'] as $awardId => $nomination) {
		// 	if ($nomination == 'Y') {
				$awardFileTempFile                     =$_REQUEST['upload_nomination_file'];

				$sqlAbstractAward			  =	array();
				$sqlAbstractAward['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
													  WHERE `id` = ?";
				$sqlAbstractAward['PARAM'][]  = array('FILD' => 'is', 'DATA' => $awardId,  'TYP' => 's');
				$resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
				$rowAbstractAward	 = $resultAbstractAward[0];
	            $applicant_full_name = $sqlAbstractSubmission[0]['applicant_title'] . " " .  $sqlAbstractSubmission[0]['applicant_first_name'] ." " . $sqlAbstractSubmission[0]['applicant_middle_name'] . " " . $sqlAbstractSubmission[0]['applicant_last_name'];

				$sql				  = array();
				$sql['QUERY']         = "INSERT INTO " . _DB_AWARD_REQUEST_ . " 
													 SET `submission_id` = '" . $abstract_id . "', 
														 `submission_code` = '" . $abstract_submition_code . "', 
														 `abstract_parent_type` = '" . $sqlAbstractSubmission[0]['abstract_cat'] . "',
														 `abstract_child_type` = '" . $sqlAbstractSubmission[0]['abstract_category']. "', 
														 `abstract_topic_id` = '" . $sqlAbstractSubmission[0]['abstract_topic_id'] . "', 
														 `applicant_id` = '" . $sqlAbstractSubmission[0]['applicant_id']  . "', 
														 `applicant_name` = '" . $applicant_full_name . "',
														 `award_id` = '" . $awardId . "', 
														 `award_title` = '" . $rowAbstractAward['award_name'] . "',	
														 `upload_nomination_file` = '" . $awardFileTempFile. "',														 
														 `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														 `created_sessionId` = '" . session_id() . "', 
														 `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
				$awardRequestId       = $mycms->sql_insert($sql, false);
		// 	}
		// }
	}
		return $abstract_submition_code;
}
?>