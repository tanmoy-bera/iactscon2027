<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$action		  = $_REQUEST['act'];

switch ($action) {

		/*********************************************************************/
		/*                          SEARCH DELEGATE                          */
		/*********************************************************************/
	case 'add':

		$time				= addslashes(trim(strtoupper($_REQUEST['callTimeHour']))) . ":" . addslashes(trim(strtoupper($_REQUEST['callTimeMin'])));

		$sqlupdatetravelpickup = array();
		$sqlupdatetravelpickup['QUERY']  =   "INSERT INTO " . _DB_USER_CALLDETAILS_ . " 
												SET `delegate_id` = '" . addslashes(trim($_REQUEST['delId'])) . "',
													`logged_user_id` = '" . $loggedUserID . "',
													`call_date` = '" . addslashes(trim($_REQUEST['callDate'])) . "',
													`call_time` = '" . $time . "',
													`call_contents` = '" . addslashes(trim($_REQUEST['user_call_details'])) . "'";
		$mycms->sql_insert($sqlupdatetravelpickup);
		$mycms->redirect("add_callHistory.php");

		exit();
		break;

	case 'getEmailValidation':
		global $cfg, $mycms;

		$email                  	= trim($_REQUEST['email']);

		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser            = array();
		$sqlFetchUser['QUERY']	  = "SELECT `id` 
											   FROM " . _DB_USER_REGISTRATION_ . "  
											  WHERE `user_email_id` = ? 
											  AND `status`          = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email,  'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

		$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
		$maxRowsUser                = $mycms->sql_numrows($resultFetchUser);

		if ($maxRowsUser == 0) {
			$availabilityStatus 	= "AVAILABLE";
		} else {

			$availabilityStatus 	= "IN_USE";
		}

		echo $availabilityStatus;
		exit();
		break;
	case 'getMobileValidation':
		global $cfg, $mycms;

		$mobile                  	= trim($_REQUEST['mobile']);

		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser            = array();
		$sqlFetchUser['QUERY']	  = "SELECT `id` 
											   FROM " . _DB_USER_REGISTRATION_ . "  
											  WHERE `user_mobile_no` = ? 
											  AND `status`          = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_mobile_no', 'DATA' => $mobile,  'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

		$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
		$maxRowsUser                = $mycms->sql_numrows($resultFetchUser);

		if ($maxRowsUser == 0) {
			$availabilityStatus 	= "AVAILABLE";
		} else {

			$availabilityStatus 	= "IN_USE";
		}

		echo $availabilityStatus;
		exit();
		break;

	case 'mobile_update':
		mobileNoUpdateProcess();
		pageRedirection("edit_userDetails.php", 2, $_REQUEST['search_string']);
		exit();
		break;

	case 'name_update':
		nameUpdateProcess();
		pageRedirection("edit_userDetails.php", 2, $_REQUEST['search_string']);
		exit();
		break;

	case 'accomapny_name_update':
		nameAccompanyUpdateProcess();
		pageRedirection("edit_userDetails.php", 2, $_REQUEST['search_string']);
		exit();
		break;

	case 'email_update':

		emailUpdateProcess();
		pageRedirection("edit_userDetails.php", 2, $_REQUEST['search_string']);
		exit();
		break;

		//FOR NAPCON 2024 
	case 'membership_id_update':

		membership_id_update();
		pageRedirection("registration.php", 1);
		exit();
		break;

	case 'add_user_doc':

		add_user_doc();
		pageRedirection("registration.php", 1);
		exit();
		break;

	case 'update_profile':

		// FETCHING LOGGED USER DETAILS			
		$delegateId 					= $_REQUEST['delegate_id'];

		// VARRIABLE DECLARATION
		$user_initial                   = addslashes(trim(strtoupper($_REQUEST['user_initial'])));
		$user_first_name                = addslashes(trim(strtoupper($_REQUEST['user_first_name'])));
		$user_middle_name               = addslashes(trim(strtoupper($_REQUEST['user_middle_name'])));
		$user_last_name                 = addslashes(trim(strtoupper($_REQUEST['user_last_name'])));

		$user_address                   = addslashes(trim(strtoupper($_REQUEST['user_address'])));
		$user_country    		        = addslashes(trim(($_REQUEST['user_country'] == "") ? 0 : $_REQUEST['user_country']));
		$user_state      		        = addslashes(trim(($_REQUEST['user_state'] == "") ? 0 : $_REQUEST['user_state']));
		$user_city                      = addslashes(trim(strtoupper($_REQUEST['user_city'])));
		$user_postal_code		        = addslashes(trim(strtoupper($_REQUEST['user_postal_code'])));
		$user_food_preference     		= addslashes(trim(strtoupper($_REQUEST['user_food_preference'])));

		// UPDATING USER DETAILS PHASE TWO
		$sqlUpdateUserPhaseTwo 					=	array();
		$sqlUpdateUserPhaseTwo['QUERY']      	= "UPDATE " . _DB_USER_REGISTRATION_ . " 
														  SET `user_address` = ?,
															  `user_country_id` = ?,
															  `user_state_id` = ?,
															  `user_city` = ?,
															  `user_pincode` = ?,
															  `user_food_preference` = ?,
															  `modified_ip` = ?,
															  `modified_sessionId` = ?,
															  `modified_browser` = ?,
															  `modified_dateTime` = ?
														WHERE `id` = ?";

		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_address',            		 'DATA' => addslashes($user_address),   	 		 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_country_id',            	 'DATA' => addslashes($user_country),   			 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_state_id',            		 'DATA' => addslashes($user_state),   	 		 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_city',            		 'DATA' => addslashes($user_city),   	 		 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_pincode',            		 'DATA' => addslashes($user_postal_code),   	 	 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_food_preference',          	 'DATA' => addslashes($user_food_preference),   	 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_ip',            		 'DATA' => $_SERVER['REMOTE_ADDR'],   	 		 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_sessionId',            	 'DATA' => session_id(),   	 					 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_browser',            	 'DATA' => $_SERVER['HTTP_USER_AGENT'],   		 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_dateTime',            	 'DATA' => date('Y-m-d H:i:s'),   	 			 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'id',            	 				 'DATA' => addslashes($delegateId),   			 'TYP' => 's');

		$mycms->sql_update($sqlUpdateUserPhaseTwo, false);

		$mycms->redirect('edit_userDetails.php?m=2');
		exit();
		break;
}


function mobileNoUpdateProcess()
{
	global $cfg, $mycms;

	$loggedUserID 		 = $mycms->getLoggedUserId();

	$isd_code			 = $_REQUEST['new_isd_code'];
	$old_isd_code		 = $_REQUEST['old_isd_code'];
	$mobile_no			 = $_REQUEST['new_mobile_no'];
	$old_mobile_no		 = $_REQUEST['old_mobile_no'];
	$delegateId			 = $_REQUEST['delegate_id'];

	$sqlUpdateRecord 			=	array();
	$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET `user_mobile_no` = ?,
											   `user_mobile_isd_code`= ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";

	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_mobile_no',            	 'DATA' => addslashes($mobile_no),  	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_mobile_isd_code',          'DATA' => $isd_code,  			 	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 'DATA' => $loggedUserID,  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            'DATA' => session_id(),  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 'DATA' => $delegateId,   			 'TYP' => 's');
	$mycms->sql_update($sqlUpdateRecord);

	$sqlInsertRecord	= array();
	$sqlInsertRecord['QUERY']	 = "INSERT INTO " . _DB_OLD_CONTACT_HISTORY_ . "
											SET `delegate_id` = ?,
												`old_isd_code` = ?,
												`old_mobile_no` = ?,
												`status` = ?, 
												`created_by` = ?,
												`created_ip` = ?,
												`created_sessionId` = ?,
												`created_dateTime` = ?";

	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'delegate_id',            	 'DATA' => addslashes($delegateId),   'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'old_isd_code',            	 'DATA' => addslashes($old_isd_code), 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'old_mobile_no',             'DATA' => addslashes($old_mobile_no), 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'status',            	 	 'DATA' => 'A',   	 				 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_by',            	 'DATA' => $loggedUserID,   			 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_ip',            	 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_sessionId',       	 'DATA' => session_id(),   	 		 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_dateTime',          'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');

	$lastInsertedId = $mycms->sql_insert($sqlInsertRecord);

	//spotPhNoUpdateMessage($delegateId,'SEND');
}

function nameUpdateProcess()
{
	global $cfg, $mycms;

	$loggedUserID 		        = $mycms->getLoggedUserId();

	//print_r($_REQUEST);
	$delegateId					= $_REQUEST['delegateId'];
	$user_titles					= addslashes(trim(strtoupper($_REQUEST['user_title'])));
	if ($user_titles != '') {
		$user_title = $user_titles;
	} else {
		$user_title = $user_titles;
	}
	$user_first_name			= addslashes(trim(strtoupper($_REQUEST['user_first_name'])));
	$user_middle_name		    = addslashes(trim(strtoupper($_REQUEST['user_middle_name'])));
	$user_last_name			    = addslashes(trim(strtoupper($_REQUEST['user_last_name'])));
	$user_full_name             = $user_title . " " . $user_first_name . " " . $user_middle_name . " " . $user_last_name;


	$sqlUpdateRecord 			=	array();
	$sqlUpdateRecord['QUERY']	= "UPDATE " . _DB_USER_REGISTRATION_ . "
									   SET  `user_title` = ?, 
											`user_first_name` = ?, 
											`user_middle_name` = ?, 
											`user_last_name` = ?,
											`user_full_name` = ?, 
											`modified_by` = ?,
											`modified_ip` = ?,
											`modified_sessionId` = ?,
											`modified_dateTime` = ?
									 WHERE  `id` = ?";
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_title',            	   'DATA' => addslashes($user_title),   		'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_first_name',             'DATA' => addslashes($user_first_name),   'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_middle_name',            'DATA' => addslashes($user_middle_name),  'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_last_name',              'DATA' => addslashes($user_last_name),    'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_full_name',              'DATA' => addslashes($user_full_name),    'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',                 'DATA' => $loggedUserID,   				'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             	   'DATA' => $_SERVER['REMOTE_ADDR'],   		'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',          'DATA' => session_id(),   				'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',           'DATA' => date('Y-m-d H:i:s'),   			'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',           			   'DATA' => $delegateId,   					'TYP' => 's');
	$mycms->sql_update($sqlUpdateRecord);

	//spotNameUpdateMessage($delegateId,'SEND');

	//Update on scientific section participant list
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * 
							  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
							 WHERE `participant_delegate_id` = '" . $delegateId . "' AND status ='A'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$existingDetail	 = $resultsListing[0];
	if ($resultsListing) {


		$sqlInsertSession = array();
		$sqlInsertSession['QUERY'] 		= " UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
									   SET `participant_full_name`		= '" . addslashes($user_full_name) . "' 
									  
									 WHERE `participant_delegate_id` = '" . $delegateId . "' AND `id`= '" . $existingDetail['id'] . "'";
		$mycms->sql_update($sqlInsertSession);
	}

}

function nameAccompanyUpdateProcess()
{
	global $cfg, $mycms;

	$loggedUserID 		        = $mycms->getLoggedUserId();

	//print_r($_REQUEST);
	$accompanyId					= $_REQUEST['accompanyId'];
	$user_titles					= addslashes(trim(strtoupper($_REQUEST['user_full_name'])));
	if ($user_titles != '') {
		$user_title = $user_titles;
	} else {
		$user_title = $user_titles;
	}
	$user_full_name			= addslashes(trim(strtoupper($_REQUEST['user_full_name'])));
	$accompany_edit_age		    = addslashes(trim($_REQUEST['accompany_edit_age']));
	$accompany_edit_food_preference			    = addslashes(trim(strtoupper($_REQUEST['accompany_edit_food_preference'])));

	$sqlUpdateRecord 	=	array();
	$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET 	`user_full_name` = ?, 
												`user_age` =?,
												`user_food_preference` = ?,
												`modified_by` = ?,
												`modified_ip` = ?,
												`modified_sessionId` = ?,
												`modified_dateTime` = ?
										 WHERE  `id` = ?";
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_full_name',            	   'DATA' => addslashes($user_title),   					'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_age',            	     	   'DATA' => addslashes($accompany_edit_age),   			'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_food_preference',            'DATA' => addslashes($accompany_edit_food_preference), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            	   	   'DATA' => addslashes($loggedUserID),   				'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',            	   	   'DATA' => $_SERVER['REMOTE_ADDR'],   					'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',              'DATA' => session_id(),   							'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',               'DATA' => date('Y-m-d H:i:s'),   						'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',            	   			   'DATA' => addslashes($accompanyId),   				'TYP' => 's');
	$mycms->sql_update($sqlUpdateRecord);
	//spotNameUpdateMessage($delegateId,'SEND');

}

function emailUpdateProcess()
{
	global $cfg, $mycms;

	$loggedUserID 		 = $mycms->getLoggedUserId();

	$email				 = $_REQUEST['new_email_id'];
	$oldEmail			 = $_REQUEST['old_email_id'];
	$delegateId			 = $_REQUEST['user_id'];

	$sqlUpdateRecord 			 =	array();
	$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET `user_email_id` = ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_email_id',            	 'DATA' => addslashes($email),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 'DATA' => $loggedUserID,  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            'DATA' => session_id(),  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 'DATA' => $delegateId,   			 'TYP' => 's');

	$mycms->sql_update($sqlUpdateRecord);

	//Update on scientific section participant list
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * 
							  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
							 WHERE `participant_delegate_id` = '" . $delegateId . "' AND status ='A'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$existingDetail	 = $resultsListing[0];
	if ($resultsListing) {

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY'] 		= " UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
									   SET `participant_email_id`		= '" . addslashes($email) . "' 
									 WHERE `participant_delegate_id` = '" . $delegateId . "' AND `id`= '" . $existingDetail['id'] . "'";
		$mycms->sql_update($sqlInsertSession);
	}

	$sqlInsertRecord 			 =	array();
	$sqlInsertRecord['QUERY']	 = "INSERT INTO " . _DB_OLD_CONTACT_HISTORY_ . "
										SET `delegate_id` = ?,
											`old_email_id` = ?,
											`status` = ?, 
											`created_by` = ?,
											`created_ip` = ?,
											`created_sessionId` = ?,
											`created_dateTime` = ?";

	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'delegate_id',            	 'DATA' => addslashes($delegateId),   'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'old_email_id',            	 'DATA' => addslashes($oldEmail),   	 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'status',            	 	 'DATA' => 'A',   	 				 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_by',            	 'DATA' => $loggedUserID,   			 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_ip',            	 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_sessionId',       	 'DATA' => session_id(),   	 		 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_dateTime',          'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$lastInsertedId = $mycms->sql_insert($sqlInsertRecord);



	//spotEmailUpdateMessage($delegateId,'SEND');
}

function membership_id_update()
{
	global $cfg, $mycms;

	$loggedUserID 		 = $mycms->getLoggedUserId();

	$membership_id				 = $_REQUEST['membership_id'];
	// $oldEmail			 = $_REQUEST['old_email_id'];
	$delegateId			 = $_REQUEST['user_id'];

	$sqlUpdateRecord 			 =	array();
	$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET `membership_number` = ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'membership_number',            	 'DATA' => addslashes($membership_id),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 'DATA' => $loggedUserID,  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            'DATA' => session_id(),  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 'DATA' => $delegateId,   			 'TYP' => 's');

	$mycms->sql_update($sqlUpdateRecord);
}

function add_user_doc()
{
	global $cfg, $mycms;

	$loggedUserID 		 = $mycms->getLoggedUserId();
	$delegateId			 = $_REQUEST['user_id'];

	if (isset($_FILES['user_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['user_document']['tmp_name'];
		// echo $tempFile;die;
		$fileName = "USERDOC_" . date('ymdHis') . "_" . $_FILES['user_document']['name'];
		$uploadDir = '../../'. $cfg['FILES.ABSTRACT.REQUEST'];
		// echo $uploadDir;die;

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {

			$sqlUpdateRecord 			 =	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET `user_document` = ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_document',            	 'DATA' => addslashes($fileName),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 'DATA' => $loggedUserID,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            'DATA' => session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 'DATA' => $delegateId,   			 'TYP' => 's');

			$mycms->sql_update($sqlUpdateRecord);
		}
	}
}
/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	@$searchString                 		       = "";
	$searchArray                   		       = array();

	$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key']));
	$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_user_first_name']        = addslashes(trim($_REQUEST['src_user_first_name']));
	$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
	$searchArray['src_user_last_name']         = addslashes(trim($_REQUEST['src_user_last_name']));
	$searchArray['src_registration_id']        = addslashes(trim($_REQUEST['src_registration_id']));

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
