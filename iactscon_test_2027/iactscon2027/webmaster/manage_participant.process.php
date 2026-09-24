<?php
include_once('includes/init.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php');
$loggedUserID     = $mycms->getLoggedUserId();
$loggedUserType   = $mycms->getLoggedUserType();

switch ($action) {
	case 'addParticipant':
		addParticipantForScientificProgram($mycms, $cfg);
		$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Addded successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_participant.php";</script>';
		break;

	case 'updateParticipant':
		updateParticipantForScientificProgram($mycms, $cfg);
		$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Updated successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_participant.php";</script>';
		break;

	case 'linkUpParticipant':
		linkUpParticipant($mycms, $cfg);
		exit();
		break;

	case 'linkUpParticipantMobile':
		linkUpParticipantMobile($mycms, $cfg);
		exit();
		break;

	case 'linkUpAllParticipant':
		linkUpAllParticipant($mycms, $cfg);
		exit();
		break;

	case 'linkUpAllParticipantMobile':
		linkUpAllParticipantMobile($mycms, $cfg);
		exit();
		break;

	case 'unlinkAllParticipant':
		unlinkAllParticipant($mycms, $cfg);
		exit();
		break;

	case 'getEmailValidationStatus':
		checkEmailAvailibilityStatus($mycms, $cfg);
		exit();
		break;

	case 'getMobileValidationStatus':
		checkMobileAvailibilityStatusFaculty($mycms, $cfg);
		exit();
		break;

	case 'removeParticipant':
		removeParticipant($mycms, $cfg);
		pageRedirection("manage_participant.php", 3);
		exit();
		break;

	case 'updateParticipation':
		updateParticipation($mycms, $cfg);
		pageRedirection("manage_participant.php", 2);
		exit();
		break;

	case 'getCommentComposerWindow':
		getCommentComposerWindow($mycms, $cfg);
		exit();
		break;

	case 'insertParticipantComment':
		insertParticipantComment($mycms, $cfg);
		exit();
		break;

	case 'getCommentCompletionWindow':
		getCommentCompletionWindow($mycms, $cfg);
		exit();
		break;

	case 'completeParticipantComment':
		completeParticipantComment($mycms, $cfg);
		exit();
		break;

	case 'getCommentEditWindow':
		getCommentEditWindow($mycms, $cfg);
		exit();
		break;

	case 'editParticipantComment':
		editParticipantComment($mycms, $cfg);
		exit();
		break;

	case 'getCommentDeleteWindow':
		getCommentDeleteWindow($mycms, $cfg);
		exit();
		break;

	case 'deleteParticipantComment':
		deleteParticipantComment($mycms, $cfg);
		exit();
		break;

	case 'sendParticipantMail':
		sendParticipantMail($mycms, $cfg);
		exit();
		break;
    case 'sendParticipantCVMail':
		sendParticipantCVMail($mycms, $cfg);
		exit();
		break;
	case 'sendParticipantInvitationMail':
		sendParticipantInvitationMail($mycms, $cfg);
		exit();
		break;

	case 'downloadScheduleExcel':
		downloadScheduleExcel($cfg, $mycms);
		break;
    case 'downloadParticipantScheduleExcel':
		downloadParticipantScheduleExcel($cfg, $mycms);
		break;
	case 'downloadCommentExcel':
		downloadCommentExcel($cfg, $mycms);
		break;

	case 'downloadHallCommentExcel':
		downloadHallCommentExcel($cfg, $mycms);
		break;

	case 'downloadParticipantExcel':
		downloadParticipantExcel($cfg, $mycms);
		break;

	case 'downloadParticipantCVImage':
		downloadParticipantCVImage($cfg, $mycms);
		exit();
		break;

	case 'downloadAllParticipantCVImage':
		downloadAllParticipantCVImage($cfg, $mycms);
		break;

	case 'printAllParticipantCVImage':
		printAllParticipantCVImage($cfg, $mycms);
		break;


	case 'updateParticipantFeature':

		if (!empty($_REQUEST['id'])) {
			$id = $_REQUEST['id'];
			$flag = $_REQUEST['flag'];

			if (!empty($id)) {
				$sqlUserImage = array();
				$sqlUserImage['QUERY']           = "   UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . "
														  SET `participant_feature` = '" . $flag . "' 
														WHERE `id` = '" . $id . "'";
				$mycms->sql_update($sqlUserImage, false);
			}
		}


		break;
	case 'getUserdetails':
		if (!empty($_REQUEST['email'])) {

			$sqlFetchUser['QUERY']       = "SELECT *
											 FROM " . _DB_USER_REGISTRATION_ . "
											WHERE `user_email_id` = ? 
											  AND `status` = ?";

			$sqlFetchUser['PARAM'][]    = array('FILD' => 'user_email_id', 'DATA' => $_REQUEST['email'], 'TYP' => 's');
			$sqlFetchUser['PARAM'][]    = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$row 						= $resultFetchUser[0];

			$name = $row['user_first_name'] . " ";
			$name .= ($row['user_middle_name'] == '') ? $row['user_last_name'] : ($row['user_middle_name'] . " " . $row['user_last_name']);

			$dataString                 = "";
			if ($row) {

				$data['ID'] = $row['id'];
				$data['TITLE'] = $row['user_title'];
				$data['FULL_NAME'] = $name;
				$data['EMAIL'] = $row['user_email_id'];
				$data['MOBILE'] = $row['user_mobile_no'];
				$data['REG_REQUEST'] = $row['registration_request'];
				$data['STATUS'] = 'IN USE';
			} else {
				$data['STATUS'] = 'NOT FOUND';
			}

			echo json_encode($data);


			/*$availabilityStatus .= (trim($dataString)=='')?'}':','.$dataString.'}';	
				echo $availabilityStatus;	*/
		}


		break;

	default:
		//$mycms->redirect("manage_participant.php");
		exit();
		break;
}

function addParticipantForScientificProgram($mycms, $cfg)
{
	global $mycms, $cfg;
	// echo '<pre>'; print_r($_REQUEST); die;

	$participant_email_id 					= addslashes(trim($_REQUEST['participantEmail']));
	$participant_alternative_email_id 		= addslashes(trim($_REQUEST['participantAlternativeEmail']));
	$participant_title 					    = addslashes(trim($_REQUEST['participantTitle']));
	$participant_first_name 				= addslashes(trim(ucwords(strtolower($_REQUEST['participantFirstName']))));
	$participant_middle_name 				= addslashes(trim(ucwords(strtolower($_REQUEST['participantMiddleName']))));
	$participant_last_name 					= addslashes(trim(ucwords(strtolower($_REQUEST['participantLastName']))));
	$participant_full_name 					= $participant_title . " " . $participant_first_name . " " . $participant_middle_name . " " . $participant_last_name;
	$participant_mobile_no 					= addslashes(trim($_REQUEST['participantMobile']));
	$participant_alternative_mobile_no 		= addslashes(trim($_REQUEST['participantAlternativeMobile']));
	$participant_dob 						= addslashes(trim($_REQUEST['participantDob']));
	$participant_nationality 				= addslashes(trim($_REQUEST['participantNationality']));
	$participant_fields 					= addslashes(trim($_REQUEST['participantFields']));
	$participant_institutions 				= addslashes(trim($_REQUEST['participantIntstitute']));
	$participant_alma_mater 				= addslashes(trim($_REQUEST['participantAlmaMater']));
	$participant_notable_awards 			= addslashes(trim($_REQUEST['participantNotableAwards']));
	$participant_description 				= addslashes(trim($_REQUEST['participantDescription']));
	$participant_career 					= addslashes(trim($_REQUEST['participantCareer']));
	$participant_others 					= addslashes(trim($_REQUEST['participantOthers']));

	// $explodeName = explode(" ", $participant_full_name, 3);

	if ($participant_dob == NULL || $participant_dob == '') {
		$participant_dob = "0000-00-00";
	}

	// print_r($explodeName);

	$sqlFetchUser                = array();
	$sqlFetchUser['QUERY']       = "SELECT COUNT(*) as COUNTDATA 
										 FROM " . _DB_USER_REGISTRATION_ . "
										WHERE `user_email_id` = ? 
										  AND `status` = ?";
	// AND `registration_request` = 'GENERAL'	

	$sqlFetchUser['PARAM'][]    = array('FILD' => 'user_email_id', 'DATA' => $participant_email_id, 'TYP' => 's');
	$sqlFetchUser['PARAM'][]    = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

	$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
	$row 						= $resultFetchUser[0];

	//print_r($row['COUNTDATA']); 
	$participant_delegate_id = 'NA';
	$participant_registration_id = 'NA';
	$participant_unique_sequence = 'NA';

	if ($row['COUNTDATA'] == 0) {
		$userDetailsArray['user_type']                        	  = 'DELEGATE';
		$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($participant_email_id)));
		$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($participant_email_id)));
		//$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
		//$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
		$userDetailsArray['membership_number']                    = '';
		$userDetailsArray['user_initial_title']   				  = '';
		$userDetailsArray['user_title']       				  = addslashes(trim(strtoupper($participant_title)));
		$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($participant_first_name)));
		$userDetailsArray['user_middle_name']                 = addslashes(trim(strtoupper($participant_middle_name)));
		$userDetailsArray['user_last_name']                   = addslashes(trim(strtoupper($participant_last_name)));
		$userDetailsArray['user_full_name']                       = addslashes(trim(strtoupper($participant_full_name)));

		$userDetailsArray['user_mobile_isd_code']                 = '';
		$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($participant_mobile_no)));
		$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($participant_mobile_no)));
		$userDetailsArray['user_address']                         = '';
		$userDetailsArray['user_country']                         = '0';
		$userDetailsArray['user_state']                           = '0';
		$userDetailsArray['user_city']                            = '';
		$userDetailsArray['user_postal_code']                     = '';
		$userDetailsArray['user_dob_year']                        = '';
		$userDetailsArray['user_dob_month']                       = '';
		$userDetailsArray['user_dob_day']                         = '';

		$userDetailsArray['user_dob']                             = $participant_dob;


		$userDetailsArray['user_gender']                          = 'NA';
		$userDetailsArray['user_designation']                     = '';
		$userDetailsArray['user_depertment']                      = '';
		$userDetailsArray['user_institution_name']                = '';
		$userDetailsArray['user_food_preference']                 = '';
		$userDetailsArray['user_other_food_details']              = '';
		$userDetailsArray['passport_no']                      	  = '';
		$userDetailsArray['passport_expiry_date']                 = '';

		$userDetailsArray['user_document']						  = '';
		$userDetailsArray['isRegistration']						  = 'N';
		$userDetailsArray['isConference']						  = 'N';
		$userDetailsArray['isWorkshop']							  = 'N';
		$userDetailsArray['isAccommodation']                      = 'N';
		$userDetailsArray['isTour']								  = 'N';
		$userDetailsArray['isCombo']							  = 'N';
		$userDetailsArray['IsAbstract']							  = 'N';

		$userDetailsArray['registration_classification_id']		  = '0';
		$userDetailsArray['registration_tariff_cutoff_id']        = '0';
		$userDetailsArray['registration_request']       		  = 'FACULTY';
		$userDetailsArray['operational_area']   	    		  = 'FACULTY';
		$userDetailsArray['registration_payment_status']		  = 'UNPAID';
		$userDetailsArray['registration_mode']					  = "OFFLINE";
		$userDetailsArray['account_status']						  = 'REGISTERED';
		$userDetailsArray['reg_type']              				  = 'BACK';


		$delegateId												  = insertingUserDetails($userDetailsArray, $date);
		$userRec 	= getUserDetails($delegateId);
	}



	$paricipantSql = array();
	$paricipantSql['QUERY']       = "SELECT COUNT(*) as COUNTP FROM " . _DB_SP_PARTICIPANT_DETAILS_ . "
										WHERE `participant_email_id` = '" . $participant_email_id . "' 
										AND `status` = 'A'";


	//$paricipantSql['PARAM'][]    = array('FILD' => 'participant_email_id', 'DATA' =>$participant_email_id, 'TYP' => 's');	
	//$paricipantSql['PARAM'][]    = array('FILD' => 'status',        'DATA' =>'A',    'TYP' => 's');

	$resultFetchParticipant    		= $mycms->sql_select($paricipantSql);
	$rowFetchParticipant 			= $resultFetchParticipant[0];
	$paricipantId = array();
	$paricipantId['QUERY']       = "SELECT `id` FROM " . _DB_USER_REGISTRATION_ . "
										WHERE `user_email_id` = '" .$participant_email_id. "' 
										AND `status` = 'A'";


	//$paricipantSql['PARAM'][]    = array('FILD' => 'participant_email_id', 'DATA' =>$participant_email_id, 'TYP' => 's');	
	//$paricipantSql['PARAM'][]    = array('FILD' => 'status',        'DATA' =>'A',    'TYP' => 's');

	$resultFetchParticipantId    		= $mycms->sql_select($paricipantId);
	$rowFetchParticipantId 			= $resultFetchParticipantId[0];


	if ($rowFetchParticipant['COUNTP'] == 0) {
		if ($row['COUNTDATA']>0) {
			$participant_delegate_id =$rowFetchParticipantId['id'];
			$userRec 	= getUserDetails($participant_delegate_id);

			$sqlInsertSession = array();
			$sqlInsertSession['QUERY'] 		= " UPDATE " . _DB_USER_REGISTRATION_ . " 
											SET `user_title`		= '" . $participant_title. "', 
											`user_first_name`= '" . $participant_first_name . "' ,
											`user_middle_name`= '" . $participant_middle_name . "' ,
											`user_last_name`= '" .$participant_last_name . "' , 
											`user_full_name`= '" . $participant_full_name . "' 
											WHERE `user_email_id` = '" . $participant_email_id . "' AND status = 'A' ";
			$mycms->sql_update($sqlInsertSession);
			
		} else {
			$participant_delegate_id = $userRec['id'];
			$userRec 	= getUserDetails($delegateId);
		}

		$participant_registration_id = $userRec['user_registration_id'];
		$participant_unique_sequence = $userRec['user_unique_sequence'];

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY'] 			= "INSERT INTO " . _DB_SP_PARTICIPANT_DETAILS_ . " 
													   SET `participant_email_id`				= '" . $participant_email_id . "', 
													   	   `participant_alternative_email_id`	= '" . $participant_alternative_email_id . "', 
													   	   `participant_delegate_id`			= '" . $participant_delegate_id . "', 
													   	   `participant_registration_id`		= '" . $participant_registration_id . "', 
													   	   `participant_unique_sequence`		= '" . $participant_unique_sequence . "', 	
													   	   `participant_title`					= '" . $participant_title . "', 	
														   `participant_first_name`				= '" . $participant_first_name . "', 
														   `participant_middle_name`				= '" . $participant_middle_name . "', 
														   `participant_last_name`				= '" . $participant_last_name . "', 
														   `participant_full_name`				= '" . $participant_full_name . "', 
														   `participant_mobile_no`				= '" . $participant_mobile_no . "', 
														   `participant_alternative_mobile_no`	= '" . $participant_alternative_mobile_no . "', 
														   `participant_dob` 					= '" . $participant_dob . "', 
														   `participant_nationality` 			= '" . $participant_nationality . "', 
														   `participant_fields` 				= '" . $participant_fields . "', 
														   `participant_institutions` 			= '" . $participant_institutions . "', 
														   `participant_alma_mater` 			= '" . $participant_alma_mater . "', 
														   `participant_notable_awards` 		= '" . $participant_notable_awards . "', 
														   `participant_description` 			= '" . $participant_description . "', 
														   `participant_career` 				= '" . $participant_career . "',
														   `participant_others` 				= '" . $participant_others . "',  
														   `status` 							= 'A',
														   `created_ip` 						= '" . $_SERVER['REMOTE_ADDR'] . "', 
														   `created_sessionId` 					= '" . session_id() . "',
														   `created_browser` 					= '" . $_SERVER['HTTP_USER_AGENT'] . "',
														   `created_dateTime` 					= '" . date('Y-m-d H:i:s') . "'";
	
		 $lastInsertId 	= $mycms->sql_insert($sqlInsertSession);
		participantDocumentUpload($lastInsertId, $_FILES['participantDocument']);



		foreach ($_REQUEST['participant_type'] as $key => $value) {
			$sqlInsertMaping = array();
			$sqlInsertMaping['QUERY']			 = "INSERT INTO " . _DB_SP_MAPING_PC_TO_PARTICIPANT_ . " 
															SET `participation_classification_id` = '" . $value . "', 
																`participant_id` = '" . $lastInsertId . "'";
			$mycms->sql_insert($sqlInsertMaping);
		}

		foreach ($_REQUEST['date_id'] as $key => $value) {
			$date_id					 = $_REQUEST['date_id'][$value];
			$avalable_start				 = $_REQUEST['avalable_start_hour'][$value] . ":" . $_REQUEST['avalable_start_min'][$value];
			$avalable_end				 = $_REQUEST['avalable_end_hour'][$value] . ":" . $_REQUEST['avalable_end_min'][$value];

			$sqlInsertMaping = array();
			$sqlInsertMaping['QUERY']			 = "INSERT INTO " . _DB_SP_PARTICIPANT_AVAILABILITY_ . " 
															SET `available_date_id` = '" . $date_id . "',
																`available_start_time` = '" . $avalable_start . "',
																`available_end_time` = '" . $avalable_end . "', 
																`participant_id` = '" . $lastInsertId . "'";
			$mycms->sql_insert($sqlInsertMaping);
		}

		participanImageUpload($lastInsertId, $_FILES['user_profile_image']);
	}
}

function participantDocumentUpload($participantId, $participant_Doc)
{
	global $mycms, $cfg;
	$participant_Doc_name = $participant_Doc['name'];
	$participant_Doc_tmp_name = $participant_Doc['tmp_name'];
	if ($participant_Doc_tmp_name != "") {
		$doc_unique_name = $participantId . '_' . date('YmdHi') . '_' . strtolower(str_replace(array('-', ' ', '\'', '"'), array('_', '_', '_', '_'), $participant_Doc_name));
		// $document_path = '../../' . $cfg['SP.PARTICIPANT.DOC'] . $doc_unique_name;
             $uploadDir = realpath(__DIR__ . "/../" . $cfg['SP.PARTICIPANT.DOC']);
            $document_path = $uploadDir . "/" . $doc_unique_name;

		if (move_uploaded_file($participant_Doc_tmp_name, $document_path)) {
			$paricipantId = array();
	        $paricipantId['QUERY']       = "SELECT `id` FROM " . _DB_SP_PARTICIPANT_DOCUMENT_ . "
										WHERE `participant_id` = '" .$participantId. "' 
									   ";
	         $resultFetchParticipantId    		= $mycms->sql_select($paricipantId);
            if($resultFetchParticipantId){
			$sqlInsertDocument = array();
			$sqlInsertDocument['QUERY'] = "UPDATE " . _DB_SP_PARTICIPANT_DOCUMENT_ . " 
										 SET `participant_document_name` = '" . $participant_Doc_name . "', 
											 `doc_unique_name` = '" . $doc_unique_name . "'
											 WHERE `participant_id` = '" .$participantId. "' ";
			$mycms->sql_update($sqlInsertDocument);
			}else{
            $sqlInsertDocument = array();
			$sqlInsertDocument['QUERY'] = "INSERT INTO " . _DB_SP_PARTICIPANT_DOCUMENT_ . " 
										 SET `participant_document_name` = '" . $participant_Doc_name . "', 
											 `doc_unique_name` = '" . $doc_unique_name . "',
											 `participant_id` = '" . $participantId . "'";
			$mycms->sql_insert($sqlInsertDocument);
			}
			
		}
	}
}

function participanImageUpload($participantId, $participant_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $participant_Image['name']);
	$userImageTempFile 	= $participant_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'PIC_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		// $userImagePath     				= '../../' . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $userImageFileName;
		$uploadDir = realpath(__DIR__ . "/../" . $cfg['SP.PARTICIPANT.PROFILE.IMAGE']);
	    $userImagePath = $uploadDir . "/" . $userImageFileName;
		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . "
														  SET `participant_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function updateParticipantForScientificProgram($mycms, $cfg)
{
	global $mycms, $cfg;

	//echo '<pre>'; print_r($_REQUEST); die;

	$participant_email_id 					= addslashes(trim($_REQUEST['participantEmail']));
	$participant_alternative_email_id 		= addslashes(trim($_REQUEST['participantAlternativeEmail']));
	$participant_registration_id			= addslashes(trim($_REQUEST['participantRegistrationId']));
	$participant_title						= addslashes(trim($_REQUEST['participantTitle']));
	$participant_first_name 				= addslashes(trim(ucwords(strtolower($_REQUEST['participantFirstName']))));
	$participant_middle_name 				= addslashes(trim(ucwords(strtolower($_REQUEST['participantMiddleName']))));
	$participant_last_name 					= addslashes(trim(ucwords(strtolower($_REQUEST['participantLastName']))));
	$participant_full_name 					= $participant_title . " " . $participant_first_name . " " . $participant_middle_name . " " . $participant_last_name;
	$participant_mobile_no 					= addslashes(trim($_REQUEST['participantMobile']));
	$participant_alternative_mobile_no 		= addslashes(trim($_REQUEST['participantAlternativeMobile']));
	$participant_dob 						= addslashes(trim($_REQUEST['participantDob']));
	$participant_nationality 				= addslashes(trim($_REQUEST['participantNationality']));
	$participant_fields 					= addslashes(trim($_REQUEST['participantFields']));
	$participant_institutions 				= addslashes(trim($_REQUEST['participantIntstitute']));
	$participant_alma_mater 				= addslashes(trim($_REQUEST['participantAlmaMater']));
	$participant_notable_awards 			= addslashes(trim($_REQUEST['participantNotableAwards']));
	$participant_description 				= addslashes(trim($_REQUEST['participantDescription']));
	$participant_career 					= addslashes(trim($_REQUEST['participantCareer']));
	$participant_others 					= addslashes(trim($_REQUEST['participantOthers']));
	$participant_id							= addslashes(trim($_REQUEST['id']));

	if ($participant_dob == NULL || $participant_dob == '') {
		$participant_dob = "0000-00-00";
	}

	$oldArr = array();
	$newArr = array();
	foreach ($_REQUEST['old_date_id'] as $key => $value) {
		$oldArr[$_REQUEST['old_date_id'][$value]] = $_REQUEST['old_avalable_start_hour'][$value] . ":" . $_REQUEST['old_avalable_start_min'][$value]
			. "--" . $_REQUEST['old_avalable_end_hour'][$value] . ":" . $_REQUEST['old_avalable_end_min'][$value];
	}
	foreach ($_REQUEST['date_id'] as $key => $value) {
		$newArr[$_REQUEST['date_id'][$value]] = $_REQUEST['avalable_start_hour'][$value] . ":" . $_REQUEST['avalable_start_min'][$value]
			. "--" . $_REQUEST['avalable_end_hour'][$value] . ":" . $_REQUEST['avalable_end_min'][$value];
	}
	$diffArr1 = array_diff_assoc($oldArr, $newArr);
	if ($diffArr1) {
		$diffArr = $diffArr1;
	} else {
		$diffArr2 = array_diff_assoc($newArr, $oldArr);
		if ($diffArr2) {
			$diffArr = $diffArr2;
		}
	}
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * 
							  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
							 WHERE `id` = '" . $participant_id . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$existingDetail	 = $resultsListing[0];

	// INSERTING SESSION DETAILS 
	$sqlInsertSession = array();
	$sqlInsertSession['QUERY'] 			= " UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
													   SET `participant_email_id`				= '" . $participant_email_id . "', 
													   	   `participant_alternative_email_id`	= '" . $participant_alternative_email_id . "', 
														   `participant_title`					= '" . $participant_title . "', 
														   `participant_first_name`				= '" . $participant_first_name . "', 
														   `participant_middle_name`			= '" . $participant_middle_name . "', 
														   `participant_last_name`				= '" . $participant_last_name . "', 
														   `participant_full_name`				= '" . $participant_full_name . "', 
														   `participant_mobile_no`				= '" . $participant_mobile_no . "', 
														   `participant_alternative_mobile_no`	= '" . $participant_alternative_mobile_no . "', 
														   `participant_dob` 					= '" . $participant_dob . "', 
														   `participant_nationality` 			= '" . $participant_nationality . "', 
														   `participant_fields` 				= '" . $participant_fields . "', 
														   `participant_institutions` 			= '" . $participant_institutions . "', 
														   `participant_alma_mater` 			= '" . $participant_alma_mater . "', 
														   `participant_notable_awards` 		= '" . $participant_notable_awards . "', 
														   `participant_description` 			= '" . $participant_description . "', 
														   `participant_career` 				= '" . $participant_career . "',
														   `participant_others` 				= '" . $participant_others . "'
													 WHERE `id` = '" . $participant_id . "'";
	$mycms->sql_update($sqlInsertSession);

	if ($participant_email_id != $existingDetail['participant_email_id']) {
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY'] 		= " UPDATE " . _DB_USER_REGISTRATION_ . " 
										   SET `user_email_id`		= '" . $participant_email_id . "' 
										 WHERE `user_email_id` = '" . $existingDetail['participant_email_id'] . "'  AND status = 'A' ";
		$mycms->sql_update($sqlInsertSession);
	}


	if ($participant_mobile_no != $existingDetail['participant_mobile_no']) {
		// echo   $existingDetail['participant_mobile_no']; echo "____".$participant_mobile_no;die;
		$sqlUpdateMobile = array();
		$sqlUpdateMobile['QUERY'] 		= " UPDATE " . _DB_USER_REGISTRATION_ . " 
										   SET `user_mobile_no`= '" . $participant_mobile_no . "' 
										 WHERE `user_mobile_no` = '" . $existingDetail['participant_mobile_no'] . "'  AND status = 'A' ";
		//  echo "<pre>"; print_r($sqlUpdateMobile);die;
		$mycms->sql_update($sqlUpdateMobile);
	}
	//=============================================== REGISTRATION TABLE NAME UPDATE ===================================================
	if ($participant_full_name != $existingDetail['participant_full_name']) {

		// $explodeName = explode(" ", $participant_full_name, 3);

		// $userDetailsArray = array();
		// $userDetailsArray['user_title']       				 	  = addslashes(trim(strtoupper($participant_title)));
		// $userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($explodeName[0])));
		// if (count($explodeName) == 3) {
		// 	$userDetailsArray['user_middle_name']                 = addslashes(trim(strtoupper($explodeName[1])));
		// 	$userDetailsArray['user_last_name']                   = addslashes(trim(strtoupper($explodeName[2])));
		// } else {
		// 	$userDetailsArray['user_middle_name']                 = '';
		// 	$userDetailsArray['user_last_name']                   = addslashes(trim(strtoupper($explodeName[1])));
		// }

		// $userDetailsArray['user_full_name']                       = addslashes(trim($participant_full_name));


		$sqlInsertSession = array();
		$sqlInsertSession['QUERY'] 		= " UPDATE " . _DB_USER_REGISTRATION_ . " 
										   SET `user_title`		= '" . $userDetailsArray['user_title'] . "', 
										   `user_first_name`= '" . strtoupper($participant_first_name) . "' ,
										   `user_middle_name`= '" . strtoupper($participant_middle_name) . "' ,
										   `user_last_name`= '" . strtoupper($participant_last_name) . "' , 
										   `user_full_name`= '" . strtoupper($participant_full_name) . "' 
										 WHERE `user_email_id` = '" . $participant_email_id . "' AND status = 'A' ";
		$mycms->sql_update($sqlInsertSession);
	}

	participantDocumentUpload($participant_id, $_FILES['participantDocument']);

	/*
		$participant_Doc_name = $_FILES['participantDocument']['name'];
		$participant_Doc_tmp_name = $_FILES['participantDocument']['tmp_name'];	
		
		if($participant_Doc_tmp_name !="")
		{
			$doc_unique_name = "_".rand(0,100).$participant_id.$participant_Doc_name;
			$document_path = ("documents/".$doc_unique_name);
			
			move_uploaded_file($participant_Doc_tmp_name,$document_path);
			
			$sqlInsertDocument['QUERY'] = "INSERT INTO "._DB_SP_PARTICIPANT_DOCUMENT_." 
									SET `participant_document_name` = '".$participant_Doc_name."', 
										`doc_unique_name` = '".$doc_unique_name."',
											`participant_id` = '".$participant_id ."'";
											
			$mycms->sql_insert($sqlInsertDocument);
		}
		*/

	participanImageUpload($participant_id, $_FILES['user_profile_image']);

	/*
		$userImage                      = str_replace(" ","",$_FILES['user_profile_image']['name']);
		$userImageTempFile              = $_FILES['user_profile_image']['tmp_name'];		
		
		$ids 							= str_pad($participant_id,4,'0',STR_PAD_LEFT);
		$rand							= 'PIC_'.$ids.'_'.date('ymdHis');
		$ext							= pathinfo($userImage,PATHINFO_EXTENSION);
		
		$userImageFileName				= $rand.'.'.$ext;
		
		if($userImageTempFile!="")
		{			
			$userImagePath              = '../../'.$cfg['SP.PARTICIPANT.PROFILE.IMAGE'].$userImageFileName;
			
			chmod($userImagePath, 0777);
			copy($userImageTempFile, $userImagePath);
			chmod($userImagePath, 0777);
	
			$sqlUserImage['QUERY'] = " UPDATE "._DB_SP_PARTICIPANT_DETAILS_."
										  SET `participant_image` = '".$userImageFileName."' 
										WHERE `id` = '".$participant_id."'";
			
			$mycms->sql_update($sqlUserImage, false);
			
			//$mycms->redirect($current_page);
		}
		*/

	$sqlTrancate = array();
	$sqlTrancate['QUERY']              = "DELETE FROM " . _DB_SP_PARTICIPANT_AVAILABILITY_ . " 
									       	   WHERE `participant_id` = '" . $participant_id . "'";
	//$mycms->sql_delete($sqlTrancate);


	foreach ($_REQUEST['date_id'] as $key => $value) {
		$date_id					 = $_REQUEST['date_id'][$value];
		$avalable_start				 = $_REQUEST['avalable_start_hour'][$value] . ":" . $_REQUEST['avalable_start_min'][$value];
		$avalable_end				 = $_REQUEST['avalable_end_hour'][$value] . ":" . $_REQUEST['avalable_end_min'][$value];

		$sqlInsertMaping = array();
		$sqlInsertMaping['QUERY']			 = "INSERT INTO " . _DB_SP_PARTICIPANT_AVAILABILITY_ . " 
															SET `available_date_id` = '" . $date_id . "',
																`available_start_time` = '" . $avalable_start . "',
																`available_end_time` = '" . $avalable_end . "', 
																`participant_id` = '" . $participant_id . "'";
		$mycms->sql_insert($sqlInsertMaping);
	}
	pageRedirection("manage_participant.php", "Data updated successfully");
}

function linkUpParticipant($mycms, $cfg)
{
	$id = addslashes(trim($_REQUEST['id']));

	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `id` = '" . $id . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	$email = trim($rowParticipant['participant_email_id']);

	$sqlAllGeneralUser = array();
	$sqlAllGeneralUser['QUERY']   = "SELECT * 
										  FROM " . _DB_USER_REGISTRATION_ . " 
										 WHERE `status` = 'A'
										   AND `isRegistration` = 'Y' 
										   AND `user_email_id` = '" . $email . "'
										   AND `user_type` = 'DELEGATE'
										   AND ( ( `registration_request` IN ('GENERAL','COUNTER')  AND `registration_payment_status` != 'UNPAID' ) OR `registration_request` IN ('ABSTRACT') )";
	$resAllGeneralUser   = $mycms->sql_select($sqlAllGeneralUser);

	if ($resAllGeneralUser && sizeof($resAllGeneralUser) == 1) {
		$rowAllGeneralUser = $resAllGeneralUser[0];

		$sqlListing = array();
		$sqlListing['QUERY']		 = "UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
											   SET `participant_delegate_id` = '" . $rowAllGeneralUser['id'] . "',
												   `participant_registration_id` = '" . $rowAllGeneralUser['user_registration_id'] . "',
												   `participant_unique_sequence` = '" . $rowAllGeneralUser['user_unique_sequence'] . "'
											 WHERE `id` = '" . $id . "'";


		$resultsListing	 = $mycms->sql_update($sqlListing);
		pageRedirection("manage_participant.php", '2');
	}
	pageRedirection("manage_participant.php", "Can not Linkup");
}

function linkUpAllParticipant($mycms, $cfg)
{
	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A'";
	$resultsListing	 			 = $mycms->sql_select($sqlListing);

	foreach ($resultsListing as $key => $rowParticipant) {
		if ($rowParticipant['participant_registration_id'] == "" || $rowParticipant['participant_unique_sequence'] == "") {
			$email = trim($rowParticipant['participant_email_id']);

			$sqlAllGeneralUser = array();
			$sqlAllGeneralUser['QUERY']   = "SELECT * 
												  FROM " . _DB_USER_REGISTRATION_ . " 
												 WHERE `status` = 'A'
												   AND `isRegistration` = 'Y' 
												   AND `user_email_id` = '" . $email . "'
												   AND `user_type` = 'DELEGATE'
												   AND ( ( `registration_request` IN ('GENERAL','COUNTER')  AND `registration_payment_status` != 'UNPAID' ) OR `registration_request` IN ('ABSTRACT') )";
			$resAllGeneralUser   		  = $mycms->sql_select($sqlAllGeneralUser);

			if ($resAllGeneralUser && sizeof($resAllGeneralUser) == 1) {
				$rowAllGeneralUser = $resAllGeneralUser[0];

				$sqlListing = array();
				$sqlListing['QUERY']		 = "UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
													   SET `participant_delegate_id` = '" . $rowAllGeneralUser['id'] . "',
														   `participant_registration_id` = '" . $rowAllGeneralUser['user_registration_id'] . "',
														   `participant_unique_sequence` = '" . $rowAllGeneralUser['user_unique_sequence'] . "'
													 WHERE `id` = '" . $rowParticipant['id'] . "'";
				echo '<pre>';
				print_r($sqlListing);
				echo '</pre>';
				$resultsListing	 = $mycms->sql_update($sqlListing);
			}
		}
	}
	pageRedirection("manage_participant.php", '2');
}

function linkUpParticipantMobile($mycms, $cfg)
{
	$id = addslashes(trim($_REQUEST['id']));
	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `id` = '" . $id . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	$mobile = trim($rowParticipant['participant_mobile_no']);

	$sqlAllGeneralUser = array();
	$sqlAllGeneralUser['QUERY']   = "SELECT * 
										  FROM " . _DB_USER_REGISTRATION_ . " 
										 WHERE `status` = 'A'
										   AND `isRegistration` = 'Y' 
										   AND `user_mobile_no` = '" . $mobile . "'
										   AND `user_type` = 'DELEGATE'
										   AND ( ( `registration_request` IN ('GENERAL','COUNTER')  AND `registration_payment_status` != 'UNPAID' ) OR `registration_request` IN ('ABSTRACT') )";
	$resAllGeneralUser   = $mycms->sql_select($sqlAllGeneralUser);

	if ($resAllGeneralUser && sizeof($resAllGeneralUser) == 1) {
		$rowAllGeneralUser = $resAllGeneralUser[0];

		$sqlListing	 = array();
		$sqlListing['QUERY']		 = "UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
											   SET `participant_delegate_id` = '" . $rowAllGeneralUser['id'] . "',
												   `participant_registration_id` = '" . $rowAllGeneralUser['user_registration_id'] . "',
												   `participant_unique_sequence` = '" . $rowAllGeneralUser['user_unique_sequence'] . "'
											 WHERE `id` = '" . $id . "'";


		$resultsListing	 = $mycms->sql_update($sqlListing);
		pageRedirection("manage_participant.php", '2');
	}
	pageRedirection("manage_participant.php", "Can not Linkup");
}

function linkUpAllParticipantMobile($mycms, $cfg)
{
	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										  WHERE `status` = 'A'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	foreach ($resultsListing as $key => $rowParticipant) {
		if ($rowParticipant['participant_registration_id'] == "" || $rowParticipant['participant_unique_sequence'] == "") {
			$mobile = trim($rowParticipant['participant_mobile_no']);

			$sqlAllGeneralUser = array();
			$sqlAllGeneralUser['QUERY']   = "SELECT * 
												  FROM " . _DB_USER_REGISTRATION_ . " 
												 WHERE `status` = 'A'
												   AND `isRegistration` = 'Y' 
												   AND `user_mobile_no` = '" . $mobile . "'
												   AND `user_type` = 'DELEGATE'
												   AND ( ( `registration_request` IN ('GENERAL','COUNTER')  AND `registration_payment_status` != 'UNPAID' ) OR `registration_request` IN ('ABSTRACT') )";

			$resAllGeneralUser   = $mycms->sql_select($sqlAllGeneralUser);

			if ($resAllGeneralUser && sizeof($resAllGeneralUser) == 1) {
				$rowAllGeneralUser = $resAllGeneralUser[0];

				$sqlListing	 = array();
				$sqlListing['QUERY']		 = "UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
													   SET `participant_delegate_id` = '" . $rowAllGeneralUser['id'] . "',
														   `participant_registration_id` = '" . $rowAllGeneralUser['user_registration_id'] . "',
														   `participant_unique_sequence` = '" . $rowAllGeneralUser['user_unique_sequence'] . "'
													 WHERE `id` = '" . $rowParticipant['id'] . "'";
				$mycms->sql_update($sqlListing);
			}
		}
	}
	pageRedirection("manage_participant.php", '2');
}

function unlinkAllParticipant($mycms, $cfg)
{
	$sqlListing = array();

	$sqlListing['QUERY']		 = "UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										   SET `participant_delegate_id` = NULL,
											   `participant_registration_id` = NULL,
											   `participant_unique_sequence` = NULL";
	$mycms->sql_update($sqlListing);

	pageRedirection("manage_participant.php", '2');
}

function removeParticipant($mycms, $cfg)
{
	$participant_id  = addslashes(trim($_REQUEST['id']));



	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT participant_delegate_id 
							  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
							 WHERE `status` = 'A' 
							   AND `id` = '" . $participant_id . "'";

	$resultsListing	 = $mycms->sql_select($sqlListing);

	$rowUserRegDet = getUserDetails($resultsListing[0]['participant_delegate_id']);
	//echo '<pre>'; print_r($rowUserRegDet); die; 

	$sqlDelete = array();
	$sqlDelete['QUERY']			 = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
									  WHERE `participant_id` = '" . $participant_id . "'";
	$mycms->sql_delete($sqlDelete);




	$sqlParticipant = array();
	$sqlParticipant['QUERY'] 			= " UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										   SET `status` = 'D'
										 WHERE `id` = '" . $participant_id . "'";
	$mycms->sql_update($sqlParticipant);

	if ($rowUserRegDet['registration_request'] == 'FACULTY') {
		$sqlUaer = array();
		$sqlUaer['QUERY'] 			= " UPDATE " . _DB_USER_REGISTRATION_ . " 
										   SET `status` = 'D'
										 WHERE `id` = '" . $resultsListing[0]['participant_delegate_id'] . "'";
		$mycms->sql_update($sqlUaer);
	}
}

function updateParticipation($mycms, $cfg)
{
	$participant_id  		= addslashes(trim($_REQUEST['paricipantId']));
	$participationStatus  	= addslashes(trim($_REQUEST['participationStatus']));

	$sqlUpdate = array();
	$sqlUpdate['QUERY']			 = "UPDATE  " . _DB_SP_PARTICIPANT_DETAILS_ . "
										   SET participation_status = '" . $participationStatus . "'
									     WHERE `id` = '" . $participant_id . "'";
	$mycms->sql_update($sqlUpdate);


	$participantId 	= $participant_id;
	$callDate		= date("Y-m-d");
	$callTimeHour	= date("H");
	$callTimeMin	= date("i");
	$call_subject	= 'PARTICIPANT STATUS';
	$call_contents	= 'Recorded : ' . $participationStatus;

	if (trim($callTimeHour) == '' || !is_numeric($callTimeHour) || $callTimeHour > 23 || $callTimeHour < 0) {
		$callTimeHour = 0;
	}
	if (trim($callTimeMin) == '' || !is_numeric($callTimeMin) || $callTimeMin > 59 || $callTimeMin < 0) {
		$callTimeMin = 0;
	}

	$call_time		= number_pad(intval(trim($callTimeHour)), 2) . ':' . number_pad(intval(trim($callTimeMin)), 2) . ':' . '00';

	$call_datetime 	= $callDate . ' ' . number_pad(intval(trim($callTimeHour)), 2) . ':' . number_pad(intval(trim($callTimeMin)), 2) . ':' . '00';


	$sqlupdatetravelpickup			 =	array();
	$sqlupdatetravelpickup['QUERY']  =   "  INSERT INTO " . _DB_USER_CALLDETAILS_	. " 
														SET `participant_id` = ?,
															`logged_user_id` = ?,
															`call_subject` = ?,
															`call_datetime` = ?,
															`call_date` = ?,
															`call_time` = ?,
															`call_contents` = ?";

	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'participant_id',   	'DATA' => $participantId,	'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'logged_user_id',   	'DATA' => $loggedUserID, 	'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_subject',   	'DATA' => $call_subject, 	'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_datetime',   	'DATA' => $call_datetime, 	'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'callDate',  	  		'DATA' => $callDate, 		'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_time',  	  	'DATA' => $call_time, 	  	'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_contents',   	'DATA' => $call_contents,	'TYP' => 's');
	$mycms->sql_insert($sqlupdatetravelpickup);
}

function checkEmailAvailibilityStatus($mycms, $cfg)
{
	$email                  	= trim($_REQUEST['email']);
	$id                  		= trim($_REQUEST['id']);
	$availabilityStatus 		= '{"STATUS" : "BLANK"}';
	if ($email != '') {
		$additionalString	= '';
		if ($id != '') {
			$additionalString	= "    AND `id` != '" . $id . "'";
		}

		$sqlListing	 = array();
		$sqlListing['QUERY']		 = "SELECT id 
								  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
								 WHERE `status` = 'A' 
								   AND `participant_email_id` = '" . $email . "'
								    " . $additionalString;

		$resultsListing	 = $mycms->sql_select($sqlListing);
		if ($resultsListing) {
			$availabilityStatus 	= '{"STATUS" : "IN_USE"}';
		} else {
			$sqlFetchUser = array();
			$sqlFetchUser['QUERY']       		= "SELECT id, registration_request,user_title,user_first_name,user_middle_name,user_last_name,user_mobile_isd_code,user_mobile_no,user_email_id
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = '" . $email . "'
												  AND `status` = 'A'";
			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			if ($resultFetchUser) {
				$availabilityStatus 	= '{"STATUS" : "AVAILABLE", 
											"REG_REQUEST" : "' . $resultFetchUser[0]['registration_request']. '",
											"TITLE" : "' . $resultFetchUser[0]['user_title']. '",
											"FIRST_NAME" : "' . $resultFetchUser[0]['user_first_name']. '",
											"MIDDLE_NAME" : "' . $resultFetchUser[0]['user_middle_name']. '",
											"LAST_NAME" : "' . $resultFetchUser[0]['user_last_name']. '",
											"MOBILE_NO" : "' . $resultFetchUser[0]['user_mobile_no']. '",
											"EMAIL_ID" : "' . $resultFetchUser[0]['user_email_id']. '"
											}';
			} else {
				$availabilityStatus 	= '{"STATUS" : "NOT_FOUND"}';
			}
		}
	}
	echo $availabilityStatus;
	exit();
}

function checkMobileAvailibilityStatusFaculty($mycms, $cfg)
{
	$mobile                  	= trim($_REQUEST['mobile']);
	$id                  		= trim($_REQUEST['id']);
	$availabilityStatus 		= '{"STATUS" : "BLANK"}';
	if ($mobile != '') {
		$additionalString	= '';
		if ($id != '') {
			$additionalString	= "    AND `id` != '" . $id . "'";
		}

		$sqlListing	 = array();
		$sqlListing['QUERY']		 = "SELECT id 
								  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
								 WHERE `status` = 'A' 
								   AND `participant_mobile_no` =  '" . $mobile . "'
								    " . $additionalString;

		$resultsListing	 = $mycms->sql_select($sqlListing);
		if ($resultsListing) {
			$availabilityStatus 	= '{"STATUS" : "IN_USE"}';
		} else {
			$sqlFetchUser = array();
			$sqlFetchUser['QUERY']       		= "SELECT id, registration_request,user_title,user_first_name,user_middle_name,user_last_name,user_mobile_isd_code,user_mobile_no,user_email_id 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_mobile_no` = '" . $mobile . "'
												  AND `status` = 'A'";
			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			if ($resultFetchUser) {
				$availabilityStatus 	= '{"STATUS" : "AVAILABLE", 
											"REG_REQUEST" : "' . $resultFetchUser[0]['registration_request']. '",
											"TITLE" : "' . $resultFetchUser[0]['user_title']. '",
											"FIRST_NAME" : "' . $resultFetchUser[0]['user_first_name']. '",
											"MIDDLE_NAME" : "' . $resultFetchUser[0]['user_middle_name']. '",
											"LAST_NAME" : "' . $resultFetchUser[0]['user_last_name']. '",
											"MOBILE_NO" : "' . $resultFetchUser[0]['user_mobile_no']. '",
											"EMAIL_ID" : "' . $resultFetchUser[0]['user_email_id']. '"
											}';
			} else {
				$availabilityStatus 	= '{"STATUS" : "NOT_FOUND"}';
			}
		}
	}
	echo $availabilityStatus;
	exit();
}

function sendParticipantMail($mycms, $cfg)
{
	global $loggedUserID;

	$participantId   		= trim($_REQUEST['participantId']);
	$user_email_id		    = $_REQUEST['user_email_id'];
	$cc_email_ids		    = $_REQUEST['cc_email_id'];
	$user_full_name 		= trim($_REQUEST['user_full_name']);
	$mail_subject  			= trim($_REQUEST['mail_subject']);

	$mailType   		    = trim($_REQUEST['mailType']);
	$user_mobile_no		    = trim($_REQUEST['user_mobile_no']);
	$sms_body		    	= trim($_REQUEST['sms_body']);
	$submission   		    = trim($_REQUEST['submission']);

	$mail_body				= trim($_REQUEST['mail_body']);

	// echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die();
    // $mail_body = preg_replace('/<td valign="top">/', '<td valign="top">', $mail_body_pre, 1);

	if ($submission === 'SEND MAIL' || $submission === 'RE-SEND MAIL') {

		$ccEmails = [];
		$cc_emails_display = '';
		foreach ($cc_email_ids as $cc_email) {
			if (!empty($cc_email)) {
				$ccEmails[] = [
					'email' => $cc_email,
					'name' => '' // Optionally, you can include names if available
				];
				$cc_emails_display .= " " . $cc_email;
			}
		}
		if ($cc_emails_display != '') {
			$email_display = $user_email_id . ", CC: " . $cc_emails_display;
		} else {
			$email_display = $user_email_id;
		}
		// foreach ($user_email_ids as $k => $user_email_id) {
		// 	if (trim($user_email_id) != '') {
		$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', $ccEmails, '', 'AICC RCOG 2019', 'secretariat@aiccrcog2019.com'); ////$cfg['ADMIN_EMAIL']

		$sqlInsert = array();
		$sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . " 
													   SET `participantId`				= '" . $participantId . "', 
														   `emailId`					= '" . $email_display . "', 
														   `emailType`					= '" . $mailType . "', 
														   `emailSubject`				= '" . addslashes($mail_subject) . "', 
														   `emailContent` 				= '" . addslashes($mail_body) . "', 
														   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
														   `created_by` 				= '" . $loggedUserID . "',
														   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
														   `created_sessionId` 			= '" . session_id() . "',
														   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
														   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
		$lastInsertId 	= $mycms->sql_insert($sqlInsert);
		// 	}
		// }

		if ($user_mobile_no != '') {
			// $mycms->send_sms($user_mobile_no, $sms_body);

			// $sqlInsert = array();
			// $sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . " 
			// 									   SET `participantId`				= '" . $participantId . "', 
			// 										   `emailId`					= '" . $user_mobile_no . "', 
			// 										   `emailType`					= '" . $mailType . "-SMS', 
			// 										   `emailSubject`				= '', 
			// 										   `emailContent` 				= '" . addslashes($sms_body) . "', 
			// 										   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
			// 										   `created_by` 				= '" . $loggedUserID . "',
			// 										   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
			// 										   `created_sessionId` 			= '" . session_id() . "',
			// 										   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
			// 										   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
			// $lastInsertId 	= $mycms->sql_insert($sqlInsert);
		}
		 $_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Mail Sent successfully!' // dynamic message
			];
	  pageRedirection(
		"participant_send_mail.php",
		"&id=" . $_REQUEST['participantId']
	   );
		}elseif ($submission === 'DOWNLOAD PDF') {
		include_once(__DIR__.'../../includes/pdfcrowd.php');
		try {
			$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);
			$client->enableImages(true);
			$client->setPageWidth("210mm");
			$client->setPageHeight("330mm");
			$pdf = $client->convertHtml($mail_body);
			header("Content-Type: application/pdf");
			header("Cache-Control: no-cache");
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=\"Schedule_for_" . str_replace(' ', '_', $user_full_name) . '_' . date('his') . ".pdf\"");
			echo $pdf;
		} catch (Exception $e) {
			echo $mail_body;
?>
			<script>
				window.print();
			</script>
		<?
		}
	}elseif ($submission === 'DOWNLOAD DOC') {
		try {
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="Schedule_for_' . str_replace(' ', '_', $user_full_name) . '_' . date('his') . '.doc"');
			header("Content-type: application/vnd.ms-word;"); // charset=Windows-1252
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0'); //
			echo $mail_body;
		} catch (Exception $e) {
			echo $mail_body;
		?>
			<script>
				window.print();
			</script>
		<?
		}
	}
}
function sendParticipantInvitationMail($mycms, $cfg)
{
	global $loggedUserID;

	$participantId   		= trim($_REQUEST['participantId']);
	$user_email_id		    = $_REQUEST['user_email_id'];
	$cc_email_ids		    = $_REQUEST['cc_email_id'];
	$user_full_name 		= trim($_REQUEST['user_full_name']);
	$mail_subject  			= trim($_REQUEST['mail_subject']);

	$mailType   		    = trim($_REQUEST['mailType']);
	// $user_mobile_no		    = trim($_REQUEST['user_mobile_no']);
	// $sms_body		    	= trim($_REQUEST['sms_body']);
	$submission   		    = trim($_REQUEST['submission']);

	$mail_body				= trim($_REQUEST['mail_body']);
    // $mail_body = preg_replace('/<td valign="top">/', '<td valign="top">', $mail_body_pre, 1);

	

	if ($submission === 'SEND MAIL' || $submission === 'RE-SEND MAIL') {

		$ccEmails = [];
		$cc_emails_display = '';
		foreach ($cc_email_ids as $cc_email) {
			if (!empty($cc_email)) {
				$ccEmails[] = [
					'email' => $cc_email,
					'name' => '' // Optionally, you can include names if available
				];
				$cc_emails_display .= " " . $cc_email;
			}
		}
		if ($cc_emails_display != '') {
			$email_display = $user_email_id . ", CC: " . $cc_emails_display;
		} else {
			$email_display = $user_email_id;
		}


		$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', $ccEmails, '', 'AICC RCOG 2019', 'secretariat@aiccrcog2019.com'); ////$cfg['ADMIN_EMAIL']

		$sqlInsert = array();
		$sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . " 
													   SET `participantId`				= '" . $participantId . "', 
														   `emailId`					= '" . $email_display . "', 
														   `emailType`					= 'Invitation Mail', 
														   `emailSubject`				= '" . addslashes($mail_subject) . "', 
														   `emailContent` 				= '" . addslashes($mail_body) . "', 
														   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
														   `created_by` 				= '" . $loggedUserID . "',
														   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
														   `created_sessionId` 			= '" . session_id() . "',
														   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
														   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
		$lastInsertId 	= $mycms->sql_insert($sqlInsert);
		
           $_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Mail Sent successfully!' // dynamic message
			];
	  pageRedirection(
		"participant_send_mail.php",
		"&id=" . $_REQUEST['participantId']
	   );
	} elseif ($submission === 'DOWNLOAD PDF') {
		// include_once('includes/pdfcrowd.php');
					include_once(__DIR__. "/../includes/pdfcrowd.php");

		try {
			$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);
			$client->enableImages(true);
			$client->setPageWidth("210mm");
			$client->setPageHeight("330mm");
			$pdf = $client->convertHtml($mail_body);
			header("Content-Type: application/pdf");
			header("Cache-Control: no-cache");
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=\"Schedule_for_" . str_replace(' ', '_', $user_full_name) . '_' . date('his') . ".pdf\"");
			echo $pdf;
		} catch (Exception $e) {
			echo $mail_body;
		?>
			<script>
				window.print();
			</script>
		<?
		}
	} elseif ($submission === 'DOWNLOAD DOC') {
		try {
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="Schedule_for_' . str_replace(' ', '_', $user_full_name) . '_' . date('his') . '.doc"');
			header("Content-type: application/vnd.ms-word;"); // charset=Windows-1252
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0'); //
			echo $mail_body;
		} catch (Exception $e) {
			echo $mail_body;
		?>
			<script>
				window.print();
			</script>
	<?
		}
	}
}

function getCommentComposerWindow($mycms, $cfg)
{
	$participant_id = $_REQUEST['participant_id'];
	$date_id 		= $_REQUEST['date_id'];
	$hall_id 		= $_REQUEST['hall_id'];
	$session_id 	= $_REQUEST['session_id'];
	$theme_id 		= $_REQUEST['theme_id'];
	$topic_id 		= $_REQUEST['topic_id'];
	$callFrom 		= $_REQUEST['callFrom'];

	$hallIdClause     = ($hall_id == '') ? (" AND (participant_schedule.hall_id IS NULL OR participant_schedule.hall_id = '')") : (" AND participant_schedule.hall_id = '" . $hall_id . "'");
	$sessionIdClause  = ($session_id == '') ? (" AND (participant_schedule.session_id IS NULL OR participant_schedule.session_id = '')") : (" AND participant_schedule.session_id = '" . $session_id . "'");
	$themeIdClause    = ($theme_id == '') ? (" AND (participant_schedule.theme_id IS NULL OR participant_schedule.theme_id = '')") : (" AND participant_schedule.theme_id = '" . $theme_id . "'");
	$topicIdClause    = ($topic_id == '') ? (" AND (participant_schedule.topic_id IS NULL OR participant_schedule.topic_id = '')") : (" AND participant_schedule.topic_id = '" . $topic_id . "'");



	$sqlParti = array();
	$sqlParti['QUERY'] 	   = "    SELECT participant_schedule.*, 
									 program_date.conf_date, 
									 program_topic.topic_title, program_theme.theme_title, program_session.session_title, program_hall.hall_title,
									 participant_details.participant_full_name 
								FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
						  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
								  ON participant_schedule.date_id = program_date.id
						  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
								  ON participant_schedule.participant_id = participant_details.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
								  ON participant_schedule.topic_id = program_topic.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
								  ON participant_schedule.session_id = program_session.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
								  ON participant_schedule.theme_id = program_theme.id
					 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
								  ON participant_schedule.hall_id = program_hall.id
							   WHERE program_date.status = 'A' 
								 AND participant_schedule.participant_id = '" . $participant_id . "' 
								     " . $hallIdClause . "
									 " . $sessionIdClause . "
									 " . $themeIdClause . "
									 " . $topicIdClause . "";
	$resParti		= $mycms->sql_select($sqlParti);
	$rowParti 	    = $resParti[0];

	if ($rowParti['hall_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['hall_title'];
	}
	if ($rowParti['session_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['session_title'];
	}
	if ($rowParti['theme_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['theme_title'];
	}
	if ($rowParti['topic_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['topic_title'];
	}
	$rowPartiDetail[$rowParti['conf_date']][$rowParti['session_id']] = $rowParti;

	?>
	<form name="frmComposeParticipantSchedule" id="frmComposeParticipantSchedule" action="manage_participant.process.php" method="post">
		<input type="hidden" name="act" value="insertParticipantComment" />
		<input type="hidden" name="participant_id" value="<?= $participant_id ?>" />
		<input type="hidden" name="date_id" value="<?= $date_id ?>" />
		<input type="hidden" name="hall_id" value="<?= $hall_id ?>" />
		<input type="hidden" name="session_id" value="<?= $session_id ?>" />
		<input type="hidden" name="theme_id" value="<?= $theme_id ?>" />
		<input type="hidden" name="topic_id" value="<?= $topic_id ?>" />
		<input type="hidden" name="callFrom" value="<?= $callFrom ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="2" class="tcat">
					<?= ($callFrom  == 'PARTICIPANT_LIST') ? "Call Records" : "Participant Comments" ?>
					<a onclick="$('#defaultOverLay').fadeOut();$('#ParticipantComments').fadeOut();"><span style="float:right"><span alt="Add Another Topic" title="Add Session" class="icon12b-x" /></span></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;" use="topicAddDetailContainer">
					<?
					if ($rowPartiDetail && $callFrom != 'PARTICIPANT_LIST') {
						foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
					?>
							<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px; border:thin solid #ccc" width="100%">
								<tr style="background-color:#93E7A3;">
									<td colspan=3 style="padding:2px;">
										<strong><?= $congDate ?></strong>
									</td>
								</tr>
								<?
								$ssId = '';
								foreach ($dateWiseDetail as $sessId => $schedule) {
									if ($ssId != $sessId) {
								?>
										<tr>
											<td colspan=2 style="background:#66FFFF;padding:2px;"><b><?= $schedule['session_title'] ?></b></td>
										</tr>
									<?
										$ssId = $sessId;
									}
									?>
									<tr>
										<td width="100px" align="center" valign="top" style="border-bottom:thin dotted #ccc; font-weight:bold; padding:2px;"><?= $schedule['start_time'] . ' - ' . $schedule['end_time'] ?></td>
										<td align="left" valign="top" style="border-bottom:thin dotted #ccc; padding:2px;">
											<?
											if ($schedule['participant_type'] != '') {
												echo "<b>" . $schedule['participant_type'] . "</b><br/>";
											}
											?>
											<?= $schedule['display_topic_title'] ?>
										</td>
									</tr>
								<?
								}
								?>
							</table>
					<?
						}
					}
					?>
					<table width="100%">
						<tr>
							<td class="thighlight" align="left"><?= ($callFrom  == 'PARTICIPANT_LIST') ? "Call Record" : "Comments" ?></td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<textarea name="comment" id="comment" style="width:98%;float:left;" autocomplete="off" required></textarea>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<input type="date" name="created_dateTime" id="created_dateTime" style="float:left;" autocomplete="off" required />
								<span style="float:left;">&nbsp;-&nbsp;</span>
								<input type="number" name="created_dateTime_HH" id="created_dateTime_HH" value="<?= date("H") ?>" min="0" max="23" style="float:left;" autocomplete="off" required style="width:30px;" placeholder="HH" />
								<span style="float:left;">&nbsp;:&nbsp;</span>
								<input type="number" name="created_dateTime_MM" id="created_dateTime_MM" value="<?= date("i") ?>" min="0" max="59" style="float:left;" autocomplete="off" required style="width:30px;" placeholder="mm" />
							</td>
						</tr>
					</table>
					<?
					$sqlComent = array();
					$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, 
																 recordingUser.name AS recoderName, completingUser.name As completerName
															FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
												 LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
															  ON participant_comment.recordedBy = recordingUser.a_id
												 LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
															  ON participant_comment.completedBy = completingUser.a_id
														   WHERE participant_comment.participant_id = '" . $participant_id . "'
															 AND participant_comment.hall_id 		= '" . $hall_id . "'
															 AND participant_comment.session_id 	= '" . $session_id . "'
															 AND participant_comment.theme_id 		= '" . $theme_id . "'
															 AND participant_comment.topic_id 		= '" . $topic_id . "' 
														ORDER BY participant_comment.id DESC";
					$resComent		= $mycms->sql_select($sqlComent);
					if ($resComent) {
					?>
						<table width="100%">
							<tr>
								<td colspan="4" class="thighlight" align="left">
									Previous <?= ($callFrom  == 'PARTICIPANT_LIST') ? "Calls" : "Comments" ?>
								</td>
							</tr>
						</table>
						<?
						foreach ($resComent as $k => $rowComments) {
						?>
							<div style="max-height:300px; overflow:auto;">
								<table width="97%" style="margin:1px; border:thin solid #ccc;">
									<tr>
										<td align="left" style="padding:3px;">
											<div style="max-height:100px; background:#F3F3F3;padding:2px; margin-bottom:3px; overflow:auto;"><?= nl2br($rowComments['comment']) ?></div>
											<span style="float:left; font-size:11px; font-weight:bold;">Recorded By <?= $rowComments['recoderName'] ?> on <?= $rowComments['created_dateTime'] ?></span>
											<?
											if ($rowComments['completionStatus'] == 'DONE') {
											?>
												<span style="float:right;text-align:right; font-size:11px; font-weight:bold;">Completed By <?= $rowComments['completerName'] ?> on <?= $rowComments['completionDate'] ?></span>
												<br />
												<div style="max-height:100px; background:#F3F3F3; text-align:right;"><?= nl2br($rowComments['completionRemarks']) ?></div>
											<?
											}
											?>
										</td>
									</tr>

									<tr>
										<td align="right" style="padding:3px;">

										</td>
									</tr>

								</table>
							</div>
					<?
						}
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="20%"></td>
				<td align="right">
					<input type="submit" name="bttnSubmit" id="bttnSubmit" value="Record" class="btn btn-small btn-blue" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="tfooter">&nbsp;</td>
			</tr>
		</table>
	</form>
	<div use="topicParticipantTemplate" style="display:none;">
		<table width="100%" use="topicParticipantAdd">
			<tr>
				<td align="left" width="20%" style="background:#CCCCCC;">Name</td>
				<td align="left">
					<input type="hidden" name="topic_participant_id[]" use="participant_id" style="width:80%;" refObject="" />
					<input type="text" name="topic_participant_name[]" use="participant_name" style="width:80%;" refObject="" refUpdateObject="" onclick="openParticipantSelector(this);" />
					<img src="images/ajax-loader-arrow.gif" style="float:right; display:none;" use='processingIcon' />
				</td>
				<td align="left" width="20%" style="background:#CCCCCC;">Participating As</td>
				<td align="left" width="30%">
					<input type="text" name="topic_participant_as[]" style="width:80%;" />
					<a use="removeParticipant" onclick="removeTopicParticipant(this)"><span style="float:right"><span alt="Remove Participant" title="Add Session" class="icon12b-x" /></span></a>
				</td>
			</tr>
		</table>
	</div>
	<script>
		function addTopicParticipant(obj) {
			var template = $("div[use=topicParticipantTemplate]").find('table').first().clone();
			var container = $(obj).parent().closest("td[use=topicAddDetailContainer]");
			$(container).append(template);
			$(container).find("a[use=removeParticipant]").first().hide();
			$.each($(container).find("input[use=participant_name]"), function(i, thisobj) {
				var par = $(thisobj).parent().closest("td");
				$(thisobj).attr("refObject", "topicParticipantName" + i);
				$(par).find('input[use=participant_id]').attr("refObject", "topicParticipantId" + i);
				$(thisobj).attr("refUpdateObject", $(par).find('input[use=participant_id]').attr("refObject"));
			});
		}

		function removeTopicParticipant(obj) {
			$(obj).parent().closest("table[use=topicParticipantAdd]").remove();
		}
	</script>
<?php
}

function insertParticipantComment($mycms, $cfg)
{
	global $loggedUserID;
    // echo "<pre>";
	// print_r($_REQUEST);
	// die();
	$participant_id 						= addslashes(trim($_REQUEST['participant_id']));
	$date_id 								= addslashes(trim($_REQUEST['date_id']));
	$hall_id 								= addslashes(trim($_REQUEST['hall_id']));
	$session_id 							= addslashes(trim($_REQUEST['session_id']));
	$theme_id 								= addslashes(trim($_REQUEST['theme_id']));
	$topic_id 								= addslashes(trim($_REQUEST['topic_id']));
	$comment 								= addslashes(trim($_REQUEST['comment']));
	$created_dateTime 						= addslashes(trim($_REQUEST['created_dateTime']));
	$created_dateTime_HH 					= addslashes(trim($_REQUEST['created_Time']));
	$callFrom 								= $_REQUEST['callFrom'];


	$created_dateTime = $created_dateTime . ' ' . $created_dateTime_HH;

	$sqlInsert = array();
	$sqlInsert['QUERY']					 = "INSERT INTO " . _DB_SP_PARTICIPANT_COMMENTS_ . " 
												SET `participant_id` 	= '" . $participant_id . "',
													`dateId` 			= '" . $date_id . "',
													`hall_id` 			= '" . $hall_id . "',
													`session_id` 		= '" . $session_id . "', 
													`theme_id` 			= '" . $theme_id . "',
													`topic_id` 			= '" . $topic_id . "',
													`comment` 			= '" . $comment . "',
													`recordedBy` 		= '" . $loggedUserID . "',
													`created_by` 		= '" . $loggedUserID . "',
													`created_ip` 		= '" . $_SERVER['REMOTE_ADDR'] . "', 
												    `created_sessionId` = '" . session_id() . "',
												    `created_browser` 	= '" . $_SERVER['HTTP_USER_AGENT'] . "',
												    `created_dateTime` 	= '" . $created_dateTime . "'";
	$mycms->sql_insert($sqlInsert);

	$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Addded successfully!' // dynamic message
			];


			echo '<script>
					window.location.href = "manage_participant_comment.php?id=' . $participant_id . '";
				</script>';
}

function getCommentCompletionWindow($mycms, $cfg)
{
	$participant_id = $_REQUEST['participant_id'];
	$date_id 		= $_REQUEST['date_id'];
	$hall_id 		= $_REQUEST['hall_id'];
	$session_id 	= $_REQUEST['session_id'];
	$theme_id 		= $_REQUEST['theme_id'];
	$topic_id 		= $_REQUEST['topic_id'];
	$comment_id		= $_REQUEST['comment_id'];

	$hallIdClause     = ($hall_id == '') ? (" AND (participant_schedule.hall_id IS NULL OR participant_schedule.hall_id = '')") : (" AND participant_schedule.hall_id = '" . $hall_id . "'");
	$sessionIdClause  = ($session_id == '') ? (" AND (participant_schedule.session_id IS NULL OR participant_schedule.session_id = '')") : (" AND participant_schedule.session_id = '" . $session_id . "'");
	$themeIdClause    = ($theme_id == '') ? (" AND (participant_schedule.theme_id IS NULL OR participant_schedule.theme_id = '')") : (" AND participant_schedule.theme_id = '" . $theme_id . "'");
	$topicIdClause    = ($topic_id == '') ? (" AND (participant_schedule.topic_id IS NULL OR participant_schedule.topic_id = '')") : (" AND participant_schedule.topic_id = '" . $topic_id . "'");

	$sqlParti = array();
	$sqlParti['QUERY'] 	   = "    SELECT participant_schedule.*, 
									 program_date.conf_date, 
									 program_topic.topic_title, program_theme.theme_title, program_session.session_title, program_hall.hall_title,
									 participant_details.participant_full_name 
								FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
						  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
								  ON participant_schedule.date_id = program_date.id
						  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
								  ON participant_schedule.participant_id = participant_details.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
								  ON participant_schedule.topic_id = program_topic.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
								  ON participant_schedule.session_id = program_session.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
								  ON participant_schedule.theme_id = program_theme.id
					 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
								  ON participant_schedule.hall_id = program_hall.id
							   WHERE program_date.status = 'A' 
								 AND participant_schedule.participant_id = '" . $participant_id . "' 
								     " . $hallIdClause . "
									 " . $sessionIdClause . "
									 " . $themeIdClause . "
									 " . $topicIdClause . "";
	$resParti		= $mycms->sql_select($sqlParti);
	$rowParti 	    = $resParti[0];

	if ($rowParti['hall_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['hall_title'];
	}
	if ($rowParti['session_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['session_title'];
	}
	if ($rowParti['theme_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['theme_title'];
	}
	if ($rowParti['topic_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['topic_title'];
	}
	$rowPartiDetail[$rowParti['conf_date']][$rowParti['session_id']] = $rowParti;

?>
	<form name="frmComposeParticipantSchedule" id="frmComposeParticipantSchedule" action="manage_participant.process.php" method="post">
		<input type="hidden" name="act" value="completeParticipantComment" />
		<input type="hidden" name="participant_id" value="<?= $participant_id ?>" />
		<input type="hidden" name="date_id" value="<?= $date_id ?>" />
		<input type="hidden" name="hall_id" value="<?= $hall_id ?>" />
		<input type="hidden" name="session_id" value="<?= $session_id ?>" />
		<input type="hidden" name="theme_id" value="<?= $theme_id ?>" />
		<input type="hidden" name="topic_id" value="<?= $topic_id ?>" />
		<input type="hidden" name="comment_id" value="<?= $comment_id ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="2" class="tcat">
					Participant Comments Completion
					<a onclick="$('#defaultOverLay').fadeOut();$('#CommentsComplete').fadeOut();"><span style="float:right"><span alt="Add Another Topic" title="Add Session" class="icon12b-x" /></span></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;" use="topicAddDetailContainer">
					<?
					foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
					?>
						<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px; border:thin solid #ccc" width="100%">
							<tr style="background-color:#93E7A3;">
								<td colspan=3 style="padding:2px;">
									<strong><?= $congDate ?></strong>
								</td>
							</tr>
							<?
							$ssId = '';
							foreach ($dateWiseDetail as $sessId => $schedule) {
								if ($ssId != $sessId) {
							?>
									<tr>
										<td colspan=2 style="background:#66FFFF;padding:2px;"><b><?= $schedule['session_title'] ?></b></td>
									</tr>
								<?
									$ssId = $sessId;
								}
								?>
								<tr>
									<td width="100px" align="center" valign="top" style="border-bottom:thin dotted #ccc; font-weight:bold; padding:2px;"><?= $schedule['start_time'] . ' - ' . $schedule['end_time'] ?></td>
									<td align="left" valign="top" style="border-bottom:thin dotted #ccc; padding:2px;">
										<?
										if ($schedule['participant_type'] != '') {
											echo "<b>" . $schedule['participant_type'] . "</b><br/>";
										}
										?>
										<?= $schedule['display_topic_title'] ?>
									</td>
								</tr>
							<?
							}
							?>
						</table>
					<?
					}

					$sqlComent = array();
					$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, 
													 recordingUser.name AS recoderName, completingUser.name As completerName
												FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
									 LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
												  ON participant_comment.recordedBy = recordingUser.a_id
									 LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
												  ON participant_comment.completedBy = completingUser.a_id
											   WHERE participant_comment.participant_id = '" . $participant_id . "'
												 AND participant_comment.hall_id 		= '" . $hall_id . "'
												 AND participant_comment.session_id 	= '" . $session_id . "'
												 AND participant_comment.theme_id 		= '" . $theme_id . "'
												 AND participant_comment.topic_id 		= '" . $topic_id . "'
												 AND participant_comment.id 			= '" . $comment_id . "' ";
					$resComent		= $mycms->sql_select($sqlComent);
					if ($resComent) {
					?>
						<table width="100%">
							<tr>
								<td colspan="4" class="thighlight" align="left">
									Comments
								</td>
							</tr>
						</table>
						<?
						foreach ($resComent as $k => $rowComments) {
						?>
							<div style="max-height:300px; overflow:auto;">
								<table width="97%" style="margin:1px; border:thin solid #ccc;">
									<tr>
										<td align="left" style="padding:3px;">
											<div style="max-height:100px; background:#F3F3F3;padding:2px; margin-bottom:3px; overflow:auto;"><?= nl2br($rowComments['comment']) ?></div>
											<span style="font-size:11px; font-weight:bold;">Recorded By <?= $rowComments['recoderName'] ?> on <?= $rowComments['created_dateTime'] ?></span>
										</td>
									</tr>
									<?
									if ($rowComments['completionStatus'] == 'DONE') {
									?>
										<tr>
											<td align="right" style="padding:3px;">
												<span style="text-align:right;">Completed By <?= $rowComments['completerName'] ?> on <?= $rowComments['completionDate'] ?></span>
												<div style="max-height:100px; background:#F3F3F3; text-align:right;"><?= nl2br($rowComments['completionRemarks']) ?></div>
											</td>
										</tr>
									<?
									}
									?>
								</table>
							</div>
					<?
						}
					}
					?>
					<table width="100%">
						<tr>
							<td class="thighlight" align="left">Completion Remarks</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<textarea name="completionRemarks" id="completionRemarks" style="width:98%;float:left;" autocomplete="off" required></textarea>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<input type="date" name="completionDate" id="completionDate" style="float:left;" autocomplete="off" required />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="20%"></td>
				<td align="right">
					<input type="submit" name="bttnSubmit" id="bttnSubmit" value="Record" class="btn btn-small btn-blue" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="tfooter">&nbsp;</td>
			</tr>
		</table>
	</form>
	<script>
		function addTopicParticipant(obj) {
			var template = $("div[use=topicParticipantTemplate]").find('table').first().clone();
			var container = $(obj).parent().closest("td[use=topicAddDetailContainer]");
			$(container).append(template);
			$(container).find("a[use=removeParticipant]").first().hide();
			$.each($(container).find("input[use=participant_name]"), function(i, thisobj) {
				var par = $(thisobj).parent().closest("td");
				$(thisobj).attr("refObject", "topicParticipantName" + i);
				$(par).find('input[use=participant_id]').attr("refObject", "topicParticipantId" + i);
				$(thisobj).attr("refUpdateObject", $(par).find('input[use=participant_id]').attr("refObject"));
			});
		}

		function removeTopicParticipant(obj) {
			$(obj).parent().closest("table[use=topicParticipantAdd]").remove();
		}
	</script>
<?php
}

function completeParticipantComment($mycms, $cfg)
{
	global $loggedUserID;
	$participant_id 						= addslashes(trim($_REQUEST['participant_id']));
	$date_id 								= addslashes(trim($_REQUEST['date_id']));
	$hall_id 								= addslashes(trim($_REQUEST['hall_id']));
	$session_id 							= addslashes(trim($_REQUEST['session_id']));
	$theme_id 								= addslashes(trim($_REQUEST['theme_id']));
	$topic_id 								= addslashes(trim($_REQUEST['topic_id']));
	$comment_id 							= addslashes(trim($_REQUEST['comment_id']));
	$completionRemarks 						= addslashes(trim($_REQUEST['completionRemarks']));
	$completionDate 						= addslashes(trim($_REQUEST['completionDate']));

	$sqlInsert = array();
	$sqlInsert['QUERY']					 = " 	 UPDATE " . _DB_SP_PARTICIPANT_COMMENTS_ . " 
												SET `completionRemarks` = '" . $completionRemarks . "',
													`completionStatus` 	= 'DONE',
													`completedBy` 		= '" . $loggedUserID . "',
													`completionDate` 	= '" . $completionDate . "',
													`modified_by` 		= '" . $loggedUserID . "',
													`modified_ip` 		= '" . $_SERVER['REMOTE_ADDR'] . "', 
												    `modified_sessionId`= '" . session_id() . "',
												    `modified_browser` 	= '" . $_SERVER['HTTP_USER_AGENT'] . "',
												    `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
											  WHERE `id`				= '" . $comment_id . "'";
	$mycms->sql_update($sqlInsert);
	$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Addded successfully!' // dynamic message
			];


			echo '<script>
					window.location.href = "manage_participant_comment.php?id=' . $participant_id . '";
				</script>';
}

function getCommentEditWindow($mycms, $cfg)
{
	$participant_id = $_REQUEST['participant_id'];
	$date_id 		= $_REQUEST['date_id'];
	$hall_id 		= $_REQUEST['hall_id'];
	$session_id 	= $_REQUEST['session_id'];
	$theme_id 		= $_REQUEST['theme_id'];
	$topic_id 		= $_REQUEST['topic_id'];
	$comment_id		= $_REQUEST['comment_id'];

	$hallIdClause     = ($hall_id == '') ? (" AND (participant_schedule.hall_id IS NULL OR participant_schedule.hall_id = '')") : (" AND participant_schedule.hall_id = '" . $hall_id . "'");
	$sessionIdClause  = ($session_id == '') ? (" AND (participant_schedule.session_id IS NULL OR participant_schedule.session_id = '')") : (" AND participant_schedule.session_id = '" . $session_id . "'");
	$themeIdClause    = ($theme_id == '') ? (" AND (participant_schedule.theme_id IS NULL OR participant_schedule.theme_id = '')") : (" AND participant_schedule.theme_id = '" . $theme_id . "'");
	$topicIdClause    = ($topic_id == '') ? (" AND (participant_schedule.topic_id IS NULL OR participant_schedule.topic_id = '')") : (" AND participant_schedule.topic_id = '" . $topic_id . "'");

	$sqlParti = array();
	$sqlParti['QUERY'] 	   = "    SELECT participant_schedule.*, 
									 program_date.conf_date, 
									 program_topic.topic_title, program_theme.theme_title, program_session.session_title, program_hall.hall_title,
									 participant_details.participant_full_name, participant_details.participant_title 
								FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
						  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
								  ON participant_schedule.date_id = program_date.id
						  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
								  ON participant_schedule.participant_id = participant_details.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
								  ON participant_schedule.topic_id = program_topic.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
								  ON participant_schedule.session_id = program_session.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
								  ON participant_schedule.theme_id = program_theme.id
					 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
								  ON participant_schedule.hall_id = program_hall.id
							   WHERE program_date.status = 'A' 
								 AND participant_schedule.participant_id = '" . $participant_id . "' 
								     " . $hallIdClause . "
									 " . $sessionIdClause . "
									 " . $themeIdClause . "
									 " . $topicIdClause . "";
	$resParti		= $mycms->sql_select($sqlParti);
	$rowParti 	    = $resParti[0];

	if ($rowParti['hall_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['hall_title'];
	}
	if ($rowParti['session_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['session_title'];
	}
	if ($rowParti['theme_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['theme_title'];
	}
	if ($rowParti['topic_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['topic_title'];
	}
	$rowPartiDetail[$rowParti['conf_date']][$rowParti['session_id']] = $rowParti;

?>
	<form name="frmComposeParticipantSchedule" id="frmComposeParticipantSchedule" action="manage_participant.process.php" method="post">
		<input type="hidden" name="act" value="editParticipantComment" />
		<input type="hidden" name="participant_id" value="<?= $participant_id ?>" />
		<input type="hidden" name="date_id" value="<?= $date_id ?>" />
		<input type="hidden" name="hall_id" value="<?= $hall_id ?>" />
		<input type="hidden" name="session_id" value="<?= $session_id ?>" />
		<input type="hidden" name="theme_id" value="<?= $theme_id ?>" />
		<input type="hidden" name="topic_id" value="<?= $topic_id ?>" />
		<input type="hidden" name="comment_id" value="<?= $comment_id ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="2" class="tcat">
					Participant Comments Edit
					<a onclick="$('#defaultOverLay').fadeOut();$('#CommentsEdit').fadeOut();"><span style="float:right"><span alt="Close" title="Close" class="icon12b-x" /></span></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;" use="topicAddDetailContainer">
					<?
					foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
					?>
						<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px; border:thin solid #ccc" width="100%">
							<tr style="background-color:#93E7A3;">
								<td colspan=3 style="padding:2px;">
									<strong><?= $congDate ?></strong>
								</td>
							</tr>
							<?
							$ssId = '';
							foreach ($dateWiseDetail as $sessId => $schedule) {
								if ($ssId != $sessId) {
							?>
									<tr>
										<td colspan=2 style="background:#66FFFF;padding:2px;"><b><?= $schedule['session_title'] ?></b></td>
									</tr>
								<?
									$ssId = $sessId;
								}
								?>
								<tr>
									<td width="100px" align="center" valign="top" style="border-bottom:thin dotted #ccc; font-weight:bold; padding:2px;"><?= $schedule['start_time'] . ' - ' . $schedule['end_time'] ?></td>
									<td align="left" valign="top" style="border-bottom:thin dotted #ccc; padding:2px;">
										<?
										if ($schedule['participant_type'] != '') {
											echo "<b>" . $schedule['participant_type'] . "</b><br/>";
										}
										?>
										<?= $schedule['display_topic_title'] ?>
									</td>
								</tr>
							<?
							}
							?>
						</table>
						<?
					}

					$sqlComent = array();
					$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
													 recordingUser.name AS recoderName, completingUser.name As completerName
												FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
									 LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
												  ON participant_comment.recordedBy = recordingUser.a_id
									 LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
												  ON participant_comment.completedBy = completingUser.a_id
											   WHERE participant_comment.participant_id = '" . $participant_id . "'
												 AND participant_comment.hall_id 		= '" . $hall_id . "'
												 AND participant_comment.session_id 	= '" . $session_id . "'
												 AND participant_comment.theme_id 		= '" . $theme_id . "'
												 AND participant_comment.topic_id 		= '" . $topic_id . "'
												 AND participant_comment.id 			= '" . $comment_id . "' ";
					$resComent		= $mycms->sql_select($sqlComent);
					if ($resComent) {
						foreach ($resComent as $k => $rowComments) {
						?>
							<table width="100%">
								<tr>
									<td colspan="4" class="thighlight" align="left">
										Comments
									</td>
								</tr>
							</table>
							<div style="max-height:300px; overflow:auto;">
								<table width="97%" style="margin:1px; border:thin solid #ccc;">
									<tr>
										<td align="left" style="padding:3px;">
											<textarea name="comment" id="comment" style="width:98%;float:left;" autocomplete="off" required><?= $rowComments['comment'] ?></textarea>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top">
											<input type="date" name="created_dateTime" id="created_dateTime" style="float:left;" autocomplete="off" required value="<?= $rowComments['created_dateTime'] ?>" />
										</td>
									</tr>
								</table>
							</div>
							<?
							if ($rowComments['completionStatus'] == 'DONE') {
							?>
								<table width="100%">
									<tr>
										<td colspan="4" class="thighlight" align="left">
											Completion Remarks
										</td>
									</tr>
								</table>
								<div style="max-height:300px; overflow:auto;">
									<table width="100%">
										<tr>
											<td align="left" valign="top">
												<textarea name="completionRemarks" id="completionRemarks" style="width:98%;float:left;" autocomplete="off" required><?= $rowComments['completionRemarks'] ?></textarea>
											</td>
										</tr>
										<tr>
											<td align="left" valign="top">
												<input type="date" name="completionDate" id="completionDate" style="float:left;" autocomplete="off" required value="<?= $rowComments['completionDate'] ?>" />
											</td>
										</tr>
									</table>
								</div>
					<?
							}
						}
					}
					?>

				</td>
			</tr>
			<tr>
				<td width="20%"></td>
				<td align="right">
					<input type="submit" name="bttnSubmit" id="bttnSubmit" value="Update" class="btn btn-small btn-blue" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="tfooter">&nbsp;</td>
			</tr>
		</table>
	</form>
	<script>
		function addTopicParticipant(obj) {
			var template = $("div[use=topicParticipantTemplate]").find('table').first().clone();
			var container = $(obj).parent().closest("td[use=topicAddDetailContainer]");
			$(container).append(template);
			$(container).find("a[use=removeParticipant]").first().hide();
			$.each($(container).find("input[use=participant_name]"), function(i, thisobj) {
				var par = $(thisobj).parent().closest("td");
				$(thisobj).attr("refObject", "topicParticipantName" + i);
				$(par).find('input[use=participant_id]').attr("refObject", "topicParticipantId" + i);
				$(thisobj).attr("refUpdateObject", $(par).find('input[use=participant_id]').attr("refObject"));
			});
		}

		function removeTopicParticipant(obj) {
			$(obj).parent().closest("table[use=topicParticipantAdd]").remove();
		}
	</script>
<?php
}

function editParticipantComment($mycms, $cfg)
{
	global $loggedUserID;
	$participant_id 						= addslashes(trim($_REQUEST['participant_id']));
	$date_id 								= addslashes(trim($_REQUEST['date_id']));
	$hall_id 								= addslashes(trim($_REQUEST['hall_id']));
	$session_id 							= addslashes(trim($_REQUEST['session_id']));
	$theme_id 								= addslashes(trim($_REQUEST['theme_id']));
	$topic_id 								= addslashes(trim($_REQUEST['topic_id']));
	$comment_id 							= addslashes(trim($_REQUEST['comment_id']));
	$comment 								= addslashes(trim($_REQUEST['comment']));
	$created_dateTime 						= addslashes(trim($_REQUEST['created_dateTime']));
	$completionRemarks 						= addslashes(trim($_REQUEST['completionRemarks']));
	$completionDate 						= addslashes(trim($_REQUEST['completionDate']));

	$sqlInsert = array();
	$sqlInsert['QUERY']					 = " 	 UPDATE " . _DB_SP_PARTICIPANT_COMMENTS_ . " 
												SET `comment` 			= '" . $comment . "',
													`created_dateTime` 	= '" . $created_dateTime . "',
													`modified_by` 		= '" . $loggedUserID . "',
													`modified_ip` 		= '" . $_SERVER['REMOTE_ADDR'] . "', 
												    `modified_sessionId`= '" . session_id() . "',
												    `modified_browser` 	= '" . $_SERVER['HTTP_USER_AGENT'] . "',
												    `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
											  WHERE `id`				= '" . $comment_id . "'";
	$mycms->sql_update($sqlInsert);

	if (trim($completionRemarks) != '' && $completionDate != '') {
		$sqlInsert = array();
		$sqlInsert['QUERY']						 = "UPDATE " . _DB_SP_PARTICIPANT_COMMENTS_ . " 
													SET `completionRemarks` = '" . $completionRemarks . "',
														`completionDate` 	= '" . $completionDate . "',
														`modified_by` 		= '" . $loggedUserID . "',
														`modified_ip` 		= '" . $_SERVER['REMOTE_ADDR'] . "', 
														`modified_sessionId`= '" . session_id() . "',
														`modified_browser` 	= '" . $_SERVER['HTTP_USER_AGENT'] . "',
														`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
												  WHERE `id`				= '" . $comment_id . "'";
		$mycms->sql_update($sqlInsert);
	}

	$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Comment edited successfully!' // dynamic message
			];


			echo '<script>
					window.location.href = "manage_participant_comment.php?id=' . $participant_id . '";
				</script>';
}


function sendParticipantCVMail($mycms, $cfg)
{
	global $loggedUserID;

	$participantId   		= trim($_REQUEST['participantId']);
	$user_email_id		    = $_REQUEST['user_email_id'];
	$cc_email_ids		    = $_REQUEST['cc_email_id'];
	$user_full_name 		= trim($_REQUEST['user_full_name']);
	$mail_subject  			= trim($_REQUEST['mail_subject']);

	$mailType   		    = trim($_REQUEST['mailType']);
	$user_mobile_no		    = trim($_REQUEST['user_mobile_no']);
	$sms_body		    	= trim($_REQUEST['sms_body']);
	$submission   		    = trim($_REQUEST['submission']);
     $attachment  = trim($_REQUEST['attachment']);
	$mail_body				= trim($_REQUEST['mail_body']);

	//  echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die();
    // $mail_body = preg_replace('/<td valign="top">/', '<td valign="top">', $mail_body_pre, 1);

	if ($submission === 'SEND MAIL' || $submission === 'RE-SEND MAIL') {

		$ccEmails = [];
		$cc_emails_display = '';
		foreach ($cc_email_ids as $cc_email) {
			if (!empty($cc_email)) {
				$ccEmails[] = [
					'email' => $cc_email,
					'name' => '' // Optionally, you can include names if available
				];
				$cc_emails_display .= " " . $cc_email;
			}
		}
		if ($cc_emails_display != '') {
			$email_display = $user_email_id . ", CC: " . $cc_emails_display;
		} else {
			$email_display = $user_email_id;
		}
		// foreach ($user_email_ids as $k => $user_email_id) {
		// 	if (trim($user_email_id) != '') {
		$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', $ccEmails, '', 'AICC RCOG 2019', 'secretariat@aiccrcog2019.com','','',$attachment); ////$cfg['ADMIN_EMAIL']

		$sqlInsert = array();
		$sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . " 
													   SET `participantId`				= '" . $participantId . "', 
														   `emailId`					= '" . $email_display . "', 
														   `emailType`					= '" . $mailType . "', 
														   `emailSubject`				= '" . addslashes($mail_subject) . "', 
														   `emailContent` 				= '" . addslashes($mail_body) . "', 
														   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
														   `created_by` 				= '" . $loggedUserID . "',
														   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
														   `created_sessionId` 			= '" . session_id() . "',
														   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
														   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
		$lastInsertId 	= $mycms->sql_insert($sqlInsert);
		// 	}
		// }

		if ($user_mobile_no != '') {
			// $mycms->send_sms($user_mobile_no, $sms_body);

			// $sqlInsert = array();
			// $sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . " 
			// 									   SET `participantId`				= '" . $participantId . "', 
			// 										   `emailId`					= '" . $user_mobile_no . "', 
			// 										   `emailType`					= '" . $mailType . "-SMS', 
			// 										   `emailSubject`				= '', 
			// 										   `emailContent` 				= '" . addslashes($sms_body) . "', 
			// 										   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
			// 										   `created_by` 				= '" . $loggedUserID . "',
			// 										   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
			// 										   `created_sessionId` 			= '" . session_id() . "',
			// 										   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
			// 										   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
			// $lastInsertId 	= $mycms->sql_insert($sqlInsert);
		}
		 $_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Mail Sent successfully!' // dynamic message
			];
	  pageRedirection(
		"participant_send_mail.php",
		"&id=" . $_REQUEST['participantId']
	   );
	}elseif ($submission === 'DOWNLOAD PDF') {
		include_once(__DIR__.'../../includes/pdfcrowd.php');
		try {
			$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);
			$client->enableImages(true);
			$client->setPageWidth("210mm");
			$client->setPageHeight("330mm");
			$pdf = $client->convertHtml($mail_body);
			header("Content-Type: application/pdf");
			header("Cache-Control: no-cache");
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=\"Schedule_for_" . str_replace(' ', '_', $user_full_name) . '_' . date('his') . ".pdf\"");
			echo $pdf;
		} catch (Exception $e) {
			echo $mail_body;
?>
			<script>
				window.print();
			</script>
		<?
		}
	}  elseif ($submission === 'DOWNLOAD DOC') {
		try {
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="Schedule_for_' . str_replace(' ', '_', $user_full_name) . '_' . date('his') . '.doc"');
			header("Content-type: application/vnd.ms-word;"); // charset=Windows-1252
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0'); //
			echo $mail_body;
		} catch (Exception $e) {
			echo $mail_body;
		?>
			<script>
				window.print();
			</script>
		<?
		}
	}
}
function getCommentDeleteWindow($mycms, $cfg)
{
	$participant_id = $_REQUEST['participant_id'];
	$date_id 		= $_REQUEST['date_id'];
	$hall_id 		= $_REQUEST['hall_id'];
	$session_id 	= $_REQUEST['session_id'];
	$theme_id 		= $_REQUEST['theme_id'];
	$topic_id 		= $_REQUEST['topic_id'];
	$comment_id		= $_REQUEST['comment_id'];

	$hallIdClause     = ($hall_id == '') ? (" AND (participant_schedule.hall_id IS NULL OR participant_schedule.hall_id = '')") : (" AND participant_schedule.hall_id = '" . $hall_id . "'");
	$sessionIdClause  = ($session_id == '') ? (" AND (participant_schedule.session_id IS NULL OR participant_schedule.session_id = '')") : (" AND participant_schedule.session_id = '" . $session_id . "'");
	$themeIdClause    = ($theme_id == '') ? (" AND (participant_schedule.theme_id IS NULL OR participant_schedule.theme_id = '')") : (" AND participant_schedule.theme_id = '" . $theme_id . "'");
	$topicIdClause    = ($topic_id == '') ? (" AND (participant_schedule.topic_id IS NULL OR participant_schedule.topic_id = '')") : (" AND participant_schedule.topic_id = '" . $topic_id . "'");

	$sqlParti = array();
	$sqlParti['QUERY'] 	   = "    SELECT participant_schedule.*, 
									 program_date.conf_date, 
									 program_topic.topic_title, program_theme.theme_title, program_session.session_title, program_hall.hall_title,
									 participant_details.participant_full_name, participant_details.participant_title 
								FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
						  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
								  ON participant_schedule.date_id = program_date.id
						  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
								  ON participant_schedule.participant_id = participant_details.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
								  ON participant_schedule.topic_id = program_topic.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
								  ON participant_schedule.session_id = program_session.id
					 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
								  ON participant_schedule.theme_id = program_theme.id
					 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
								  ON participant_schedule.hall_id = program_hall.id
							   WHERE program_date.status = 'A' 
								 AND participant_schedule.participant_id = '" . $participant_id . "' 
								     " . $hallIdClause . "
									 " . $sessionIdClause . "
									 " . $themeIdClause . "
									 " . $topicIdClause . "";
	$resParti		= $mycms->sql_select($sqlParti);
	$rowParti 	    = $resParti[0];

	if ($rowParti['hall_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['hall_title'];
	}
	if ($rowParti['session_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['session_title'];
	}
	if ($rowParti['theme_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['theme_title'];
	}
	if ($rowParti['topic_id'] != '') {
		$rowParti['display_topic_title'] = $rowParti['topic_title'];
	}
	$rowPartiDetail[$rowParti['conf_date']][$rowParti['session_id']] = $rowParti;

?>
	<form name="frmComposeParticipantSchedule" id="frmComposeParticipantSchedule" action="manage_participant.process.php" method="post">
		<input type="hidden" name="act" value="deleteParticipantComment" />
		<input type="hidden" name="participant_id" value="<?= $participant_id ?>" />
		<input type="hidden" name="date_id" value="<?= $date_id ?>" />
		<input type="hidden" name="hall_id" value="<?= $hall_id ?>" />
		<input type="hidden" name="session_id" value="<?= $session_id ?>" />
		<input type="hidden" name="theme_id" value="<?= $theme_id ?>" />
		<input type="hidden" name="topic_id" value="<?= $topic_id ?>" />
		<input type="hidden" name="comment_id" value="<?= $comment_id ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="2" class="tcat">
					Participant Comments Delete
					<a onclick="$('#defaultOverLay').fadeOut();$('#CommentsDelete').fadeOut();"><span style="float:right"><span alt="Close" title="Close" class="icon12b-x" /></span></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;" use="topicAddDetailContainer">
					<?
					foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
					?>
						<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px; border:thin solid #ccc" width="100%">
							<tr style="background-color:#93E7A3;">
								<td colspan=3 style="padding:2px;">
									<strong><?= $congDate ?></strong>
								</td>
							</tr>
							<?
							$ssId = '';
							foreach ($dateWiseDetail as $sessId => $schedule) {
								if ($ssId != $sessId) {
							?>
									<tr>
										<td colspan=2 style="background:#66FFFF;padding:2px;"><b><?= $schedule['session_title'] ?></b></td>
									</tr>
								<?
									$ssId = $sessId;
								}
								?>
								<tr>
									<td width="100px" align="center" valign="top" style="border-bottom:thin dotted #ccc; font-weight:bold; padding:2px;"><?= $schedule['start_time'] . ' - ' . $schedule['end_time'] ?></td>
									<td align="left" valign="top" style="border-bottom:thin dotted #ccc; padding:2px;">
										<?
										if ($schedule['participant_type'] != '') {
											echo "<b>" . $schedule['participant_type'] . "</b><br/>";
										}
										?>
										<?= $schedule['display_topic_title'] ?>
									</td>
								</tr>
							<?
							}
							?>
						</table>
						<?
					}
					$sqlComent = array();
					$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
													 recordingUser.name AS recoderName, completingUser.name As completerName
												FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
									 LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
												  ON participant_comment.recordedBy = recordingUser.a_id
									 LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
												  ON participant_comment.completedBy = completingUser.a_id
											   WHERE participant_comment.participant_id = '" . $participant_id . "'
												 AND participant_comment.hall_id 		= '" . $hall_id . "'
												 AND participant_comment.session_id 	= '" . $session_id . "'
												 AND participant_comment.theme_id 		= '" . $theme_id . "'
												 AND participant_comment.topic_id 		= '" . $topic_id . "'
												 AND participant_comment.id 			= '" . $comment_id . "' ";
					$resComent		= $mycms->sql_select($sqlComent);
					if ($resComent) {
						foreach ($resComent as $k => $rowComments) {
						?>
							<table width="100%">
								<tr>
									<td colspan="4" class="thighlight" align="left">
										Comments
									</td>
								</tr>
							</table>
							<table width="97%" style="margin:1px; border:thin solid #ccc;">
								<tr>
									<td align="left" style="padding:3px;">
										<div style="max-height:100px; background:#F3F3F3;padding:2px; margin-bottom:3px; overflow:auto;"><?= nl2br($rowComments['comment']) ?></div>
										<span style="font-size:11px; font-weight:bold;">Recorded By <?= $rowComments['recoderName'] ?> on <?= $rowComments['created_dateTime'] ?></span>
									</td>
								</tr>
								<?
								if ($rowComments['completionStatus'] == 'DONE') {
								?>
									<tr>
										<td align="right" style="padding:3px;">
											<span style="text-align:right; font-size:11px; font-weight:bold;">Completed By <?= $rowComments['completerName'] ?> on <?= $rowComments['completionDate'] ?></span>
											<div style="max-height:100px; background:#F3F3F3; text-align:right; overflow:auto;"><?= nl2br($rowComments['completionRemarks']) ?></div>
										</td>
									</tr>
								<?
								}
								?>
							</table>
					<?
						}
					}
					?>

				</td>
			</tr>
			<tr>
				<td width="20%"></td>
				<td align="right">
					<input type="submit" name="bttnSubmit" id="bttnSubmit" value="Remove" class="btn btn-small btn-blue" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="tfooter">&nbsp;</td>
			</tr>
		</table>
	</form>
	<script>
		function addTopicParticipant(obj) {
			var template = $("div[use=topicParticipantTemplate]").find('table').first().clone();
			var container = $(obj).parent().closest("td[use=topicAddDetailContainer]");
			$(container).append(template);
			$(container).find("a[use=removeParticipant]").first().hide();
			$.each($(container).find("input[use=participant_name]"), function(i, thisobj) {
				var par = $(thisobj).parent().closest("td");
				$(thisobj).attr("refObject", "topicParticipantName" + i);
				$(par).find('input[use=participant_id]').attr("refObject", "topicParticipantId" + i);
				$(thisobj).attr("refUpdateObject", $(par).find('input[use=participant_id]').attr("refObject"));
			});
		}

		function removeTopicParticipant(obj) {
			$(obj).parent().closest("table[use=topicParticipantAdd]").remove();
		}
	</script>
<?php
}

function deleteParticipantComment($mycms, $cfg)
{
	global $loggedUserID;
	$participant_id 						= addslashes(trim($_REQUEST['participant_id']));
	$date_id 								= addslashes(trim($_REQUEST['date_id']));
	$hall_id 								= addslashes(trim($_REQUEST['hall_id']));
	$session_id 							= addslashes(trim($_REQUEST['session_id']));
	$theme_id 								= addslashes(trim($_REQUEST['theme_id']));
	$topic_id 								= addslashes(trim($_REQUEST['topic_id']));
	$comment_id 							= addslashes(trim($_REQUEST['comment_id']));
	$comment 								= addslashes(trim($_REQUEST['comment']));
	$created_dateTime 						= addslashes(trim($_REQUEST['created_dateTime']));
	$completionRemarks 						= addslashes(trim($_REQUEST['completionRemarks']));
	$completionDate 						= addslashes(trim($_REQUEST['completionDate']));

	$sqlDelete = array();
	$sqlDelete['QUERY']					 = "DELETE FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " 
											 	   WHERE `id` = '" . $comment_id . "'";
	$mycms->sql_delete($sqlDelete);

		$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Deleted successfully!' // dynamic message
			];


			echo '<script>
					window.location.href = "manage_participant_comment.php?id=' . $participant_id . '";
				</script>';
}

function downloadScheduleExcel($cfg, $mycms)
{
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=participantSchedule" . time() . ".xls");

	$sqlPartiTyp = array();
	$sqlPartiTyp['QUERY'] 	 = "SELECT DISTINCT LOWER(TRIM(participant_type)) AS participant_type
							  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
						  ORDER BY (CASE WHEN LOWER(TRIM(participant_type)) = 'speaker' THEN 1
						  				 WHEN LOWER(TRIM(participant_type)) = 'moderator' THEN 2
										 WHEN LOWER(TRIM(participant_type)) = 'chairperson' THEN 3
										 WHEN LOWER(TRIM(participant_type)) = 'panelist' THEN 4	
										 WHEN LOWER(TRIM(participant_type)) = 'orator' THEN 5
										 ELSE 999	
						  			END), participant_type";
	$resPartiTyp  	 = $mycms->sql_select($sqlPartiTyp);
?>
	<table border="1">
		<tr>
			<td colspan="<?= sizeof($resPartiTyp) + 3 ?>" align="left">
				<h4 style="color:#000000;">PARTICIPANT SCHEDULE REPORT</h4>
			</td>
		</tr>
		<tr>
			<td align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Name</td>
			<td align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Email</td>
			<td align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Mobile</td>
			<?
			foreach ($resPartiTyp as $k => $rowPartiTyp) {
			?>
				<td width="300px" align="left" style="font-size:12px; padding:2px 5px 2px 5px;"><?= ucwords($rowPartiTyp['participant_type']) ?></td>
			<?
			}
			?>
		</tr>
		<?
		$sqlListing	 = array();
		$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A' ORDER BY participant_full_name";
		$resultsListing	 = $mycms->sql_select($sqlListing);
		foreach ($resultsListing as $k => $rowDetails) {
			$sqlParti = array();
			$sqlParti['QUERY'] 	   = "    SELECT participant_schedule.*, 
										 program_date.conf_date, 
										 program_topic.topic_title, program_theme.theme_title, program_session.session_title, IFNULL(program_hall.hall_title, session_hall.hall_title) AS hall_title,
										 participant_details.participant_full_name,participant_details.participant_title , participant_details.id AS participantId
									FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
							  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
									  ON participant_schedule.date_id = program_date.id
							  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
									  ON participant_schedule.participant_id = participant_details.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
									  ON participant_schedule.topic_id = program_topic.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
									  ON participant_schedule.session_id = program_session.id
						 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " session_hall
									  ON program_session.session_hall_id = session_hall.id	
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
									  ON participant_schedule.theme_id = program_theme.id
						 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
									  ON participant_schedule.hall_id = program_hall.id
								   WHERE program_date.status = 'A' 
									 AND participant_schedule.participant_id = '" . $rowDetails['id'] . "' 
								ORDER BY program_date.conf_date";

			$resParti		= $mycms->sql_select($sqlParti);
			$rowPartiDetail = array();
			foreach ($resParti as $key => $rowaccomm) {
				if ($rowaccomm['hall_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['hall_title'];
				}
				if ($rowaccomm['session_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['session_title'];
				}
				if ($rowaccomm['theme_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'];
				}
				if ($rowaccomm['topic_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['topic_title'];
				}

				$startTmExpld				= explode(":", $rowaccomm['start_time']);
				$endTmExpld					= explode(":", $rowaccomm['end_time']);

				$start_time					= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
					. ":"
					. (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

				$end_time					= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
					. ":"
					. (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

				$start_time_mins			= ($startTmExpld[0] * 60) + $startTmExpld[1];
				$end_time_mins				= ($endTmExpld[0] * 60) + $endTmExpld[1];
				$duration_mins		  		= $end_time_mins - $start_time_mins;

				$rowaccomm['start_time']	= $start_time;
				$rowaccomm['end_time']		= $end_time;
				$rowaccomm['duration']		= $duration_mins;

				$rowPartiDetail[$rowaccomm['participantId']]['DETAILS'] = $rowDetails;
				$rowPartiDetail[$rowaccomm['participantId']]['PARTICIPATION'][strtolower(trim($rowaccomm['participant_type']))][] = $rowaccomm;
			}

			/*
			echo '<pre>';
			print_r($rowPartiDetail);
			echo '</pre>';
			*/

			foreach ($rowPartiDetail as $participantId => $partiCpant) {
				$ii = 0;
				$sizeOfParticipantRow = 1;
				foreach ($partiCpant['PARTICIPATION'] as $key => $schedule) {
					if (sizeof($schedule) > $sizeOfParticipantRow) {
						$sizeOfParticipantRow = sizeof($schedule);
					}
				}

				$participantDetails = $partiCpant['DETAILS'];
		?>
				<tr>
					<td valign="top" style="font-size:12px; padding:2px 5px 2px 5px;" rowspan="<?= $sizeOfParticipantRow ?>"><?= $participantDetails['participant_full_name'] ?></td>
					<td valign="top" style="font-size:12px; padding:2px 5px 2px 5px;" rowspan="<?= $sizeOfParticipantRow ?>"><?= $participantDetails['participant_email_id'] ?></td>
					<td valign="top" style="font-size:12px; padding:2px 5px 2px 5px;" rowspan="<?= $sizeOfParticipantRow ?>"><?= $participantDetails['participant_mobile_no'] ?></td>
					<?

					for ($i = 0; $i < $sizeOfParticipantRow; $i++) {
						foreach ($resPartiTyp as $k => $rowPartiTyp) {
							$scheduleData = $partiCpant['PARTICIPATION'][$rowPartiTyp['participant_type']];
							if (!empty($scheduleData[$i])) {
								$schedule = $scheduleData[$i];
					?>
								<td valign="top" style="font-size:12px; padding:2px 5px 2px 5px; width:300px;">
									Date: <?= $schedule['conf_date'] ?><br />
									Hall: <?= $schedule['hall_title'] ?><br />
									Topic: <?= $schedule['display_topic_title'] ?><br />
									Duration : <?= $schedule['duration'] ?>
								</td>
							<?
							} else {
							?>
								<td valign="top">&nbsp;</td>
					<?
							}
						}

						if ($i < $sizeOfParticipantRow - 1) {
							echo "</tr><tr>";
						}
					}
					?>
				</tr>
		<?
			}
		}
		?>
		<tr>
			<td align="left" colspan="<?= sizeof($resPartiTyp) + 3 ?>">
				<h3>Excel Download Date and Time : <?= date('d/m/Y h:i A') ?> </h3>
			</td>
		</tr>
	</table>
<?php
}
function downloadParticipantScheduleExcel($cfg, $mycms)
{
	
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=downloadParticipantScheduleExcel" . time() . ".xls");
    $sqlSelectDate = array();
	$sqlSelectDate['QUERY']		= "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
									WHERE `status` = 'A'";

	$resultDate         = $mycms->sql_select($sqlSelectDate);
	$sqlPartiTyp = array();
	$sqlPartiTyp['QUERY'] 	 = "SELECT DISTINCT LOWER(TRIM(participant_type)) AS participant_type
							  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
						  ORDER BY (CASE WHEN LOWER(TRIM(participant_type)) = 'speaker' THEN 1
						  				 WHEN LOWER(TRIM(participant_type)) = 'moderator' THEN 2
										 WHEN LOWER(TRIM(participant_type)) = 'chairperson' THEN 3
										 WHEN LOWER(TRIM(participant_type)) = 'panelist' THEN 4	
										 WHEN LOWER(TRIM(participant_type)) = 'orator' THEN 5
										 ELSE 999	
						  			END), participant_type";
	$resPartiTyp  	 = $mycms->sql_select($sqlPartiTyp);
?>
	<table border="1">
		<tr>
			<td colspan="<?= sizeof($resultDate) + 3 ?>" align="left">
				<h4 style="color:#000000;">PARTICIPANT SCHEDULE REPORT</h4>
			</td>
		</tr>
		<tr>
			<td align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Name</td>
			<td align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Email</td>
			<td align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Mobile</td>
			<?
			 foreach ($resultDate as $keyDate => $rowDate) {
			?>
				<td width="300px" align="left" style="font-size:12px; padding:2px 5px 2px 5px;"><?=$rowDate['conf_date'] ?></td>
			<?
			}
			?>
		</tr>
		<?
		$sqlListing	 = array();
		$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A' ORDER BY participant_full_name";
		$resultsListing	 = $mycms->sql_select($sqlListing);
		foreach ($resultsListing as $k => $rowDetails) {
			$sqlParti = array();
			$sqlParti['QUERY'] 	   = "    SELECT participant_schedule.*, 
										 program_date.conf_date, 
										 program_topic.topic_title, program_theme.theme_title, program_session.session_title, IFNULL(program_hall.hall_title, session_hall.hall_title) AS hall_title,
										 participant_details.participant_full_name,participant_details.participant_title , participant_details.id AS participantId
									FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
							  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
									  ON participant_schedule.date_id = program_date.id
							  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
									  ON participant_schedule.participant_id = participant_details.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
									  ON participant_schedule.topic_id = program_topic.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
									  ON participant_schedule.session_id = program_session.id
						 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " session_hall
									  ON program_session.session_hall_id = session_hall.id	
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
									  ON participant_schedule.theme_id = program_theme.id
						 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
									  ON participant_schedule.hall_id = program_hall.id
								   WHERE program_date.status = 'A' 
									 AND participant_schedule.participant_id = '" . $rowDetails['id'] . "' 
								ORDER BY program_date.conf_date";

			$resParti		= $mycms->sql_select($sqlParti);
			$rowPartiDetail = array();
			foreach ($resParti as $key => $rowaccomm) {
				if ($rowaccomm['hall_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['hall_title'];
				}
				if ($rowaccomm['session_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['session_title'];
				}
				if ($rowaccomm['theme_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'];
				}
				if ($rowaccomm['topic_id'] != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['topic_title'];
				}

				$startTmExpld				= explode(":", $rowaccomm['start_time']);
				$endTmExpld					= explode(":", $rowaccomm['end_time']);

				$start_time					= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
					. ":"
					. (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

				$end_time					= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
					. ":"
					. (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

				$start_time_mins			= ($startTmExpld[0] * 60) + $startTmExpld[1];
				$end_time_mins				= ($endTmExpld[0] * 60) + $endTmExpld[1];
				$duration_mins		  		= $end_time_mins - $start_time_mins;

				$rowaccomm['start_time']	= $start_time;
				$rowaccomm['end_time']		= $end_time;
				$rowaccomm['duration']		= $duration_mins;

				$rowPartiDetail[$rowaccomm['participantId']]['DETAILS'] = $rowDetails;
				$rowPartiDetail[$rowaccomm['participantId']]['PARTICIPATION'][$rowaccomm['conf_date']][] = $rowaccomm;
			}

			/*
			echo '<pre>';
			print_r($rowPartiDetail);
			echo '</pre>';
			*/

		foreach ($rowPartiDetail as $participantId => $partiCpant) {

			$participantDetails = $partiCpant['DETAILS'];
		?>
		<tr>
			<td valign="top" style="font-size:12px; padding:2px 5px;">
				<?= $participantDetails['participant_full_name'] ?>
			</td>

			<td valign="top" style="font-size:12px; padding:2px 5px;">
				<?= $participantDetails['participant_email_id'] ?>
			</td>

			<td valign="top" style="font-size:12px; padding:2px 5px;">
				<?= $participantDetails['participant_mobile_no'] ?>
			</td>

			<?php foreach ($resultDate as $rowDate) { ?>

				<td valign="top"
					style="font-size:12px; padding:2px 5px; width:350px; white-space:normal;">

					<?php
					$date = $rowDate['conf_date'];

					if (!empty($partiCpant['PARTICIPATION'][$date])) {

						foreach ($partiCpant['PARTICIPATION'][$date] as $schedule) {
							?>
							<strong><?= ucfirst($schedule['participant_type']) ?></strong><br />

							Hall: <?= $schedule['hall_title'] ?><br />

							Topic: <?= $schedule['display_topic_title'] ?><br />

							Time: <?= $schedule['start_time'] ?> - <?= $schedule['end_time'] ?><br />

							Duration: <?= $schedule['duration'] ?> mins<br /><br />
							<?php
						}

					} else {
						echo '&nbsp;';
					}
					?>

				</td>

			<?php } ?>

		</tr>
		<?php
		} 
		}?>
		<tr>
			<td align="left" colspan="<?= sizeof($resultDate) + 3 ?>">
				<h3>Excel Download Date and Time : <?= date('d/m/Y h:i A') ?> </h3>
			</td>
		</tr>
	</table>
<?php
}
function downloadCommentExcel($cfg, $mycms)
{
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=comments" . time() . ".xls");

	$participantid	= $_REQUEST['id'];
	if ($participantid != '') {
		$searchCond = " AND `id` = '" . $participantid . "'";
	}
	if ($_REQUEST['participants'] != '') {
		$searchCond = " AND `id` IN (" . $_REQUEST['participants'] . ")";
	}
	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A'" . $searchCond . " ORDER BY participant_full_name";
	$resultsListing	 = $mycms->sql_select($sqlListing);
?>
	<table width="100%" border="1">
		<tr>
			<td colspan="14" class="tcat">
				<h3>Comments Report</h3>
			</td>
		</tr>
		<tr>
			<td>Name</td>
			<td>Mobile</td>
			<td>Email</td>
			<td>Date</td>
			<td>Hall</td>
			<td>Session</td>
			<td>Group</td>
			<td>Topic</td>
			<td>Time</td>
			<td>Participating As</td>
			<td>Recorded By</td>
			<td>Record Time</td>
			<td>Comment</td>
			<td>Done</td>
			<td>Done By</td>
			<td>Done Date</td>
		</tr>
		<?
		foreach ($resultsListing as $k => $rowDetails) {
			$participant_id = $rowDetails['id'];

			$sqlParti = array();
			$sqlParti['QUERY'] 	   	= "   SELECT participant_schedule.*, 
										 program_date.conf_date, participant_schedule.date_id AS date_id,
										 program_topic.topic_title, participant_schedule.topic_id AS topic_id,
										 program_theme.theme_title, participant_schedule.theme_id AS theme_id,
										 program_session.session_title, participant_schedule.session_id AS session_id,
										 program_hall.hall_title, participant_schedule.hall_id AS hall_id,
										 participant_details.participant_full_name,participant_details.participant_title 
									FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
							  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
									  ON participant_schedule.date_id = program_date.id
							  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
									  ON participant_schedule.participant_id = participant_details.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
									  ON participant_schedule.topic_id = program_topic.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
									  ON participant_schedule.session_id = program_session.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
									  ON participant_schedule.theme_id = program_theme.id
						 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
									  ON program_session.session_hall_id = program_hall.id
								   WHERE program_date.status = 'A' 
									 AND participant_schedule.participant_id = '" . $participant_id . "'
								ORDER BY program_date.conf_date";
			$resParti		= $mycms->sql_select($sqlParti);
			if ($resParti) {
				$cnt = 0;
				$tRowCount = 0;
				foreach ($resParti as $k => $rowParti) {
					$sqlComent = array();
					$sqlComent['QUERY'] 	   = "    SELECT COUNT(*) tCount
											FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . "
										   WHERE participant_id = '" . $participant_id . "'
											 AND hall_id 		= '" . $rowParti['hall_id'] . "'
											 AND session_id 	= '" . $rowParti['session_id'] . "'
											 AND theme_id 		= '" . $rowParti['theme_id'] . "'
											 AND topic_id 		= '" . $rowParti['topic_id'] . "' ";
					$resComent		= $mycms->sql_select($sqlComent);
					$tCount			= $resComent[0]['tCount'];
					$tRowCount	   += $tCount;
					//$tRowCount	   += ($tCount>0)?$tCount:1;
				}

				foreach ($resParti as $k => $rowParti) {
					$sqlComent = array();
					$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*,  DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
												 recordingUser.name AS recoderName, completingUser.name As completerName
											FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
								 LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
											  ON participant_comment.recordedBy = recordingUser.a_id
								 LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
											  ON participant_comment.completedBy = completingUser.a_id
										   WHERE participant_comment.participant_id = '" . $participant_id . "'
											 AND participant_comment.hall_id 		= '" . $rowParti['hall_id'] . "'
											 AND participant_comment.session_id 	= '" . $rowParti['session_id'] . "'
											 AND participant_comment.theme_id 		= '" . $rowParti['theme_id'] . "'
											 AND participant_comment.topic_id 		= '" . $rowParti['topic_id'] . "' ";
					$resComent		= $mycms->sql_select($sqlComent);
					if ($resComent) {

						foreach ($resComent as $k => $rowComments) {
		?>
							<tr>
								<?
								if ($cnt == 0) {
								?>
									<td valign="top" rowspan="<?= $tRowCount ?>"><?= $rowDetails['participant_full_name'] ?></td>
									<td valign="top" rowspan="<?= $tRowCount ?>"><?= $rowDetails['participant_mobile_no'] ?></td>
									<td valign="top" rowspan="<?= $tRowCount ?>"><?= $rowDetails['participant_email_id'] ?></td>
								<?
								}
								?>
								<td valign="top"><?= $rowParti['conf_date'] ?></td>
								<td valign="top"><?= $rowParti['hall_title'] ?></td>
								<td valign="top"><?= $rowParti['session_title'] ?></td>
								<td valign="top"><?= $rowParti['theme_title'] ?></td>
								<td valign="top"><?= $rowParti['topic_title'] ?></td>
								<td valign="top"><?= $rowParti['start_time'] . ' - ' . $rowParti['end_time'] ?></td>
								<td valign="top"><?= $rowParti['participant_type'] ?></td>
								<td valign="top"><?= $rowComments['recoderName'] ?></td>
								<td valign="top"><?= $rowComments['created_dateTime'] ?></td>
								<td valign="top"><?= nl2br($rowComments['comment']) ?></td>
								<td valign="top"><?= nl2br($rowComments['completionRemarks']) ?></td>
								<td valign="top"><?= $rowComments['completerName'] ?></td>
								<td valign="top"><?= $rowComments['completionDate'] ?></td>
							</tr>
		<?
							$cnt++;
						}
					}
					/*else
					{
?>
				<tr>
<?
							if($tRowCount>$cnt)
							{
?>
					<td valign="top" rowspan="<?=$tRowCount?>"><?=$rowDetails['participant_full_name']?></td>
<?
							}
?>
					<td valign="top"><?=$rowParti['conf_date']?></td>
					<td valign="top"><?=$rowParti['hall_title']?></td>
					<td valign="top"><?=$rowParti['session_title']?></td>
					<td valign="top"><?=$rowParti['theme_title']?></td>
					<td valign="top"><?=$rowParti['topic_title']?></td>
					<td valign="top"><?=$rowParti['start_time'].' - '.$rowParti['end_time']?></td>					
					<td valign="top"><?=$rowParti['participant_type']?></td>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
				</tr>
<?
						$cnt++;
					}*/
				}
			}
		}
		?>
		<tr>
			<td align="left" colspan="13">
				<b>Excel Download Date and Time : <?= date('d/m/Y h:i A') ?> </b>
			</td>
		</tr>
	</table>
<?php
}

function downloadParticipantExcel($cfg, $mycms)
{
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=participants" . time() . ".xls");

	$participantid	= $_REQUEST['id'];
	if ($participantid != '') {
		$searchCond = " AND `id` = '" . $participantid . "'";
	}
	if ($_REQUEST['participants'] != '') {
		$searchCond = " AND `id` IN (" . $_REQUEST['participants'] . ")";
	}
	$sqlListing	= array();
	$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A'" . $searchCond . " ORDER BY participant_full_name";
	$resultsListing	 = $mycms->sql_select($sqlListing);
?>
	<table width="100%" border="1">
		<tr>
			<td colspan="14" class="tcat">
				<h3>Participant Report</h3>
			</td>
		</tr>
		<tr>
			<td>Name</td>
			<td>Mobile</td>
			<td>Email</td>
		</tr>
		<?
		foreach ($resultsListing as $k => $rowDetails) {
			$participant_id = $rowDetails['id'];

		?>
			<tr>
				<td valign="top" rowspan="<?= $tRowCount ?>"><?= $rowDetails['participant_full_name'] ?></td>
				<td valign="top" rowspan="<?= $tRowCount ?>"><?= $rowDetails['participant_mobile_no'] ?></td>
				<td valign="top" rowspan="<?= $tRowCount ?>"><?= $rowDetails['participant_email_id'] ?></td>
			</tr>
		<?

		}
		?>
		<tr>
			<td align="left" colspan="13">
				<b>Excel Download Date and Time : <?= date('d/m/Y h:i A') ?> </b>
			</td>
		</tr>
	</table>
<?php
}

function downloadHallCommentExcel($cfg, $mycms)
{
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=HallComments" . time() . ".xls");
?>
	<table width="100%" border="1">
		<tr>
			<td colspan="14" class="tcat">
				<h3>Comments Report</h3>
			</td>
		</tr>
		<tr>
			<td>Date</td>
			<td>Hall</td>
			<td>Session</td>
			<td>Group</td>
			<td>Topic</td>
			<td>Time</td>
			<td>Name</td>
			<td>Participating As</td>
			<td>Recorded By</td>
			<td>Record Time</td>
			<td>Comment</td>
			<td>Done</td>
			<td>Done By</td>
			<td>Done Date</td>
		</tr>
		<?


		$participant_id = $rowDetails['id'];

		$sqlParti = array();
		$sqlParti['QUERY'] 	   	= "   SELECT participant_schedule.*, 
										 program_date.conf_date, participant_schedule.date_id AS date_id,
										 program_topic.topic_title, participant_schedule.topic_id AS topic_id,
										 program_theme.theme_title, participant_schedule.theme_id AS theme_id,
										 program_session.session_title, participant_schedule.session_id AS session_id,
										 program_hall.hall_title, participant_schedule.hall_id AS hall_id,
										 participant_details.participant_full_name, participant_details.participant_title  
									FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
							  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
									  ON participant_schedule.date_id = program_date.id
							  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
									  ON participant_schedule.participant_id = participant_details.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
									  ON participant_schedule.topic_id = program_topic.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
									  ON participant_schedule.session_id = program_session.id
						 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
									  ON participant_schedule.theme_id = program_theme.id
						 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
									  ON program_session.session_hall_id = program_hall.id
								   WHERE program_date.status = 'A' 
									 AND participant_schedule.participant_id IS NOT NULL AND  participant_schedule.participant_id != ''
								ORDER BY program_date.conf_date, program_hall.hall_title, (TIME_TO_SEC(CONCAT(program_session.session_start_time,':00'))/60), (TIME_TO_SEC(CONCAT(IFNULL(program_theme.theme_time_start,'00:00'),':00'))/60), program_topic.sequence";
		$resParti		= $mycms->sql_select($sqlParti);
		if ($resParti) {


			foreach ($resParti as $k => $rowParti) {

				$sqlComent = array();
				$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*,  DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
												 recordingUser.name AS recoderName, completingUser.name As completerName
											FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
								 LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
											  ON participant_comment.recordedBy = recordingUser.a_id
								 LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
											  ON participant_comment.completedBy = completingUser.a_id
										   WHERE participant_comment.participant_id = '" . $rowParti['participant_id'] . "'
											 AND participant_comment.hall_id 		= '" . $rowParti['hall_id'] . "'
											 AND participant_comment.session_id 	= '" . $rowParti['session_id'] . "'
											 AND participant_comment.theme_id 		= '" . $rowParti['theme_id'] . "'
											 AND participant_comment.topic_id 		= '" . $rowParti['topic_id'] . "' ";
				$resComent		= $mycms->sql_select($sqlComent);
				if ($resComent) {

					foreach ($resComent as $k => $rowComments) {
		?>
						<tr>
							<td valign="top"><?= $rowParti['conf_date'] ?></td>
							<td valign="top"><?= $rowParti['hall_title'] ?></td>
							<td valign="top"><?= $rowParti['session_title'] ?></td>
							<td valign="top"><?= $rowParti['theme_title'] ?></td>
							<td valign="top"><?= $rowParti['topic_title'] ?></td>
							<td valign="top"><?= $rowParti['start_time'] . ' - ' . $rowParti['end_time'] ?></td>
							<td valign="top"><?= $rowParti['participant_title'] . " " . $rowParti['participant_full_name'] ?></td>
							<td valign="top"><?= $rowParti['participant_type'] ?></td>
							<td valign="top"><?= $rowComments['recoderName'] ?></td>
							<td valign="top"><?= $rowComments['created_dateTime'] ?></td>
							<td valign="top"><?= nl2br($rowComments['comment']) ?></td>
							<td valign="top"><?= nl2br($rowComments['completionRemarks']) ?></td>
							<td valign="top"><?= $rowComments['completerName'] ?></td>
							<td valign="top"><?= $rowComments['completionDate'] ?></td>
						</tr>
		<?
						$cnt++;
					}
				}
			}
		}

		?>
		<tr>
			<td align="left" colspan="13">
				<b>Excel Download Date and Time : <?= date('d/m/Y h:i A') ?> </b>
			</td>
		</tr>
	</table>
	<?php
	$mycms->clearDBConnections();
}

function downloadAllParticipantCVImage($cfg, $mycms)
{
	$id = $_REQUEST['pid'];
	if ($id == '') {
		$id = 0;
	}

	$sqlListing['QUERY']		 = "SELECT id
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = ? 
										   AND `id` > '" . $id . "'
									  ORDER BY id ASC
									  	 LIMIT 1";

	$sqlListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

	$resultsListing	 = $mycms->sql_select($sqlListing);

	if ($resultsListing) {
		$participantId 	 	= $resultsListing[0]['id'];

		$_REQUEST['id'] 	= $participantId;

		downloadParticipantCVImage($cfg, $mycms);
	?>
		<center>
			<form action="manage_participant.process.php" method="post" name="srchProcessFrm">
				<input type="hidden" name="act" value="downloadAllParticipantCVImage" />
				<input type="hidden" name="pid" value="<?= $participantId ?>" />
				<h5 align="center">Downloading CVs<br />Please Wait</h5>
				<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
				<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
				<br />
				<hr />
			</form>
		</center>
		<script type="text/javascript">
			document.srchProcessFrm.submit();
		</script>
<?
	} else {
		$mycms->redirect("manage_participant.php");
	}
}

function downloadParticipantCVImage($cfg, $mycms)
{
	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A' AND `id` = '" . $_REQUEST['id'] . "'";
	$resultsListing	 			 = $mycms->sql_select($sqlListing);
	$rowDetails		 			 = $resultsListing[0];

	if ($rowDetails['participant_image'] != "" && file_exists('../../' . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $rowDetails['participant_image'])) {
		$hasPicture				= true;
		$setUserProfileImage    = _BASE_URL_ . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $rowDetails['participant_image'];
	} else {
		$hasPicture				= false;
		$setUserProfileImage    = _BASE_URL_ . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . "nouserimage.png";
	}

	$cv = $rowDetails['participant_description'];
	if (trim($cv) != '') {
		$cvList = explode(PHP_EOL, $cv);

		$cvText = '<ul>';
		foreach ($cvList as $cc => $cvTxt) {
			$cvText .= '<li style="font-size:28px; margin:20px;">' . $cvTxt . '</li>';
		}
		$cvText .= '</ul>';

		$userCV = $cvText;
	}


	$html  = '<style type="text/css">
					/* latin */
					@font-face {
					  font-family: "Amaranth";
					  font-style: normal;
					  font-weight: 400;
					  src: local("Amaranth Regular"), local("Amaranth-Regular"), url(' . _BASE_URL_ . 'css/website/fonts/KtkuALODe433f0j1zMnFHdA.woff2) format("woff2");
					  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
					}
	
					body, html
					{
						padding: 0;
						margin: 0;
						font-family: "Amaranth";
						height: 100%;
						letter-spacing: 1px;
					}
					.clr
					{
						clear: both;
					}
					.container
					{
						min-width: 1280px;
						min-height: 720px;
					}
					.box-left
					{
						width: 30%;
						float: left;                    
						position: relative;
						text-align: center;
						min-height: 720px;
						background-image: linear-gradient(-76deg, white, rgba(0,0,0,.2));
	
					}
					.box-image
					{
						width: 50%;  
						min-height: 200px;                  
						margin-left: 25%;
						position: absolute;
						top: 50px;
						-moz-box-shadow: 0px 0px 4px rgba(90,44,100,.8);
						-webkit-box-shadow: 0px 0px 4px rgba(90,44,100,.8);
						box-shadow: 0px 0px 4px rgba(90,44,100,.8);
						border: 2px solid #5a2c64;
						background-color: #874396;
						color: white;
						font-size: 24px;
						font-weight: bold;
						vertical-align:middle;
					}
					.box-name
					{
						width: 100%;
						position: absolute;
						bottom: 0px;
						padding: 17px 0;
						background-color: #134c9d;/*#874396;*/
						color: white;
						font-size: 24px;
						text-align: center;                    
					}
					.box-right
					{
						width: 70%;
						min-height: 720px;
						position: relative;
						float: left;
						background: #132d60;/*#5a2c64; */           
						height: 720px;
					}
					.box-cv
					{
						width: 72%;
						float: left;
						margin-left: 8%;
						margin-top: 55px;
						color: white;
						font-size: 18px;
					}
					.box-logo
					{
						width: 15%;
						float: left;
						margin-left: 2.5%;
						margin-top: 25px;                   
					}
				</style>';
	$html .= '<div class="container">
						<div class="box-left">';
	if ($hasPicture) {
		$html .= '		<div class="box-image"><img src="' . $setUserProfileImage . '" style="width: 100%; object-fit: contain;"></div>
							<div class="box-name"><p>' . $rowDetails['participant_title'] . " " . $rowDetails['participant_full_name'] . '</p></div>';
	} else {
		$html .= '		<div class="box-image" style="display: flex; align-items: center; justify-content: center; font-size: 28px; padding:10px;"><span>' . $rowDetails['participant_full_name'] . '</span></div>
							<div class="box-name"><p>&nbsp;</p></div>';
	}
	$html .= '		</div>
						<div class="box-right">
							<div class="box-cv"><p>' . $userCV . '</p></div>
							<div class="box-logo"><img src="' . _BASE_URL_ . 'images/logo_white.png" style="width: 100%; object-fit: contain; padding-top:580px;"></div>
						</div>
						<div class="clr"></div>            
					</div>';

	//echo $html;

	// include_once('lib/pdfcrowd.php');
	// include_once(__DIR__. "/../lib/pdfcrowd.php");
    require_once __DIR__ . '/../vendor/autoload.php'; // path to mPDF autoload
	$autoloadPath = __DIR__ . '/../lib/vendor/autoload.php';


	require_once $autoloadPath;
		

	$mpdf = new \Mpdf\Mpdf([
		'mode' => 'utf-8',
		'format' => 'A4',
		'margin_top' => 10,
		'margin_right' => 10,
		'margin_bottom' => 10,
		'margin_left' => 10,
		'ignore_invalid_errors' => true,  // Move this INSIDE the config array
		'showImageErrors' => false  // Also inside config array
	]);

	$mpdf->WriteHTML($html);
	$mpdf->Output(str_replace(' ', '_', $rowDetails['participant_full_name']) . '_CV.pdf', 'D');
		//exit();
}

function printAllParticipantCVImage($cfg, $mycms)
{
	$sqlListing = array();
	$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A' AND (participant_description IS NOT NULL AND participant_description != '')";
	$resultsListing	 			 = $mycms->sql_select($sqlListing);

	foreach ($resultsListing as $kk => $rowDetails) {
		if ($rowDetails['participant_image'] != "" && file_exists('../../' . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $rowDetails['participant_image'])) {
			$hasPicture				= true;
			$setUserProfileImage    = _BASE_URL_ . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $rowDetails['participant_image'];
		} else {
			$hasPicture				= false;
			$setUserProfileImage    = _BASE_URL_ . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . "nouserimage.png";
		}

		$cv = $rowDetails['participant_description'];
		if (trim($cv) != '') {
			$cvList = explode(PHP_EOL, $cv);

			$cvText = '<ul>';
			foreach ($cvList as $cc => $cvTxt) {
				$cvText .= '<li style="font-size:28px; margin:20px;">' . $cvTxt . '</li>';
			}
			$cvText .= '</ul>';

			$userCV = $cvText;
		}


		$html  = '<style type="text/css">
						/* latin */
						@font-face {
						  font-family: "Amaranth";
						  font-style: normal;
						  font-weight: 400;
						  src: local("Amaranth Regular"), local("Amaranth-Regular"), url(' . _BASE_URL_ . 'css/website/fonts/KtkuALODe433f0j1zMnFHdA.woff2) format("woff2");
						  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
						}
		
						body, html
						{
							padding: 0;
							margin: 0;
							font-family: "Amaranth";
							height: 100%;
							letter-spacing: 1px;
						}
						.clr
						{
							clear: both;
						}
						.container
						{
							min-width: 1280px;
							min-height: 720px;
						}
						.box-left
						{
							width: 30%;
							float: left;                    
							position: relative;
							text-align: center;
							min-height: 720px;
							background-image: linear-gradient(-76deg, white, rgba(0,0,0,.2));
		
						}
						.box-image
						{
							width: 50%;  
							min-height: 200px;                  
							margin-left: 25%;
							position: absolute;
							top: 50px;
							-moz-box-shadow: 0px 0px 4px rgba(90,44,100,.8);
							-webkit-box-shadow: 0px 0px 4px rgba(90,44,100,.8);
							box-shadow: 0px 0px 4px rgba(90,44,100,.8);
							border: 2px solid #5a2c64;
							background-color: #874396;
							color: white;
							font-size: 24px;
							font-weight: bold;
							vertical-align:middle;
						}
						.box-name
						{
							width: 100%;
							position: absolute;
							bottom: 0px;
							padding: 17px 0;
							background-color: #134c9d;/*#874396;*/
							color: white;
							font-size: 24px;
							text-align: center;                    
						}
						.box-right
						{
							width: 70%;
							min-height: 720px;
							position: relative;
							float: left;
							background: #132d60;/*#5a2c64; */           
							height: 720px;
						}
						.box-cv
						{
							width: 72%;
							float: left;
							margin-left: 8%;
							margin-top: 55px;
							color: white;
							font-size: 18px;
						}
						.box-logo
						{
							width: 15%;
							float: left;
							margin-left: 2.5%;
							margin-top: 25px;                   
						}
					</style>';
		$html .= '<div class="container">
							<div class="box-left">';
		if ($hasPicture) {
			$html .= '		<div class="box-image"><img src="' . $setUserProfileImage . '" style="width: 100%; object-fit: contain;"></div>
								<div class="box-name"><p>' . $rowDetails['participant_title'] . " " . $rowDetails['participant_full_name'] . '</p></div>';
		} else {
			$html .= '		<div class="box-image" style="display: flex; align-items: center; justify-content: center; font-size: 28px; padding:10px;"><span>' . $rowDetails['participant_title'] . " " . $rowDetails['participant_full_name'] . '</span></div>
								<div class="box-name"><p>&nbsp;</p></div>';
		}
		$html .= '		</div>
							<div class="box-right">
								<div class="box-cv"><p>' . $userCV . '</p></div>
								<div class="box-logo"><img src="' . _BASE_URL_ . 'images/logo_white.png" style="width: 100%; object-fit: contain; padding-top:580px;"></div>
							</div>
							<div class="clr"></div>            
						</div>
						<h3 style="page-break-after: always;"></h3>';

		echo $html;
	}

	$mycms->clearDBConnections();
	//exit();
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey                       		       		 = "_pgn_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();

	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['src_nationality']        		 = addslashes(trim($_REQUEST['src_nationality']));
	$searchArray['src_mobile_no']        		 = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_email_id']        		 = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_field']        			 = addslashes(trim($_REQUEST['src_field']));
	$searchArray['src_institution']        		 = addslashes(trim($_REQUEST['src_institution']));
	$searchArray['src_alma_mater']        		 = addslashes(trim($_REQUEST['src_alma_mater']));
	$searchArray['src_awards']        			 = addslashes(trim($_REQUEST['src_awards']));
	$searchArray['src_biodata']        			 = addslashes(trim($_REQUEST['src_biodata']));
	$searchArray['src_availibility_date']        = addslashes(trim($_REQUEST['src_availibility_date']));
	$searchArray['src_availibility_hour']        = addslashes(trim($_REQUEST['src_availibility_hour']));
	$searchArray['src_availibility_min']         = addslashes(trim($_REQUEST['src_availibility_min']));
	$searchArray['src_linked']         			 = addslashes(trim($_REQUEST['src_linked']));
	$searchArray['src_allocated']         		 = addslashes(trim($_REQUEST['src_allocated']));
	$searchArray['src_has_pending_comment']      = addslashes(trim($_REQUEST['src_has_pending_comment']));
	$searchArray['src_has_done_comment']         = addslashes(trim($_REQUEST['src_has_done_comment']));
	$searchArray['src_participantion']         	 = addslashes(trim($_REQUEST['src_participantion']));
	$searchArray['src_participant']         	 = addslashes(trim($_REQUEST['src_participant']));
	$searchArray['src_user_tags']         	 	 = addslashes(trim($_REQUEST['src_user_tags']));

	if (isset($_REQUEST['goto']) &&  trim($_REQUEST['goto']) != '') {
		$goto = '&show=' . trim($_REQUEST['goto']);
	}

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
	exit();
}



/// deprecated
function participantDetailsUpdation($mycms, $cfg)
{														//	if(isset($_REQUEST["goSearch"]))
	$delegateId = $_REQUEST["delegateId"];
	$participaneId = $_REQUEST["participantId"];

	//echo $delegateId;
	//echo "<br/>".$participaneId;
	//die();
	global $mycms, $cfg;

	$sqlUserDetails['QUERY'] = "SELECT `user_registration_id`,`user_unique_sequence` 
										FROM `kass_user_registration` 
										WHERE `id` = '" . $delegateId . "' ";

	$UserDetails	= $mycms->sql_select($sqlUserDetails);
	$details = $UserDetails[0];

	$sqlUpdate['QUERY'] = "UPDATE `kass_sp_participant_details` 
							SET `participant_registration_id` ='" . $details['user_registration_id'] . "',
								`participant_unique_sequence` ='" . $details['user_unique_sequence'] . "',
								`participant_delegate_id` ='" . $delegateId . "'
								 WHERE `id` = '" . $participaneId . "' ";

	$mycms->sql_update($sqlUpdate);
	//$mycms->redirect("manage_participant.php",8);
	pageRedirection("manage_participant.php", 2);
}
?>