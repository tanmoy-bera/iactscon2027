<?php
include_once("includes/frontend.init.php");
include_once('includes/function.accommodation.php');
include_once("includes/function.registration.php");
include_once("includes/function.invoice.php");

$action                = $_REQUEST['act'];
switch ($action) {

	/*************************************************************/
	/*                    GENERATE STATE CONTROL                 */
	/*************************************************************/
	case 'generateStateList':
		$countryId = addslashes(trim($_REQUEST['countryId']));

		getSateList($countryId);

		exit();
		break;

	case 'generateCheckInDate':
		$hotelId = addslashes(trim($_REQUEST['hotelId']));

		getCheckInDate($hotelId);

		exit();
		break;

	case 'generateCheckOutDate':
		$dateId  = addslashes(trim($_REQUEST['dateId']));
		$hotelId = addslashes(trim($_REQUEST['hotelId']));

		getCheckOutDate($dateId, $hotelId);

		exit();
		break;
		case 'getAbstractTopicsByCategory':
			$categoryId = isset($_POST['categoryId']) ? (int) $_POST['categoryId'] : 0;

			$sqlAbstractTopic = array();
			$sqlAbstractTopic['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
											WHERE `status` = ? AND `category` = ?
											ORDER BY `abstract_topic` ASC";
			$sqlAbstractTopic['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
			$sqlAbstractTopic['PARAM'][] = array('FILD' => 'category', 'DATA' => $categoryId, 'TYP' => 'i');

			$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

			echo '<option value="">-- Select Topic --</option>';
			if ($resultAbstractTopic) {
				foreach ($resultAbstractTopic as $rowAbstractTopic) {
					echo '<option value="' . $rowAbstractTopic['id'] . '">' 
						. htmlspecialchars($rowAbstractTopic['abstract_topic']) 
						. '</option>';
				}
			}
		break;

	case 'generateRoomType':
		$hotelId   = addslashes(trim($_REQUEST['hotelId']));
		$inDateId  = addslashes(trim($_REQUEST['inDateId']));
		$outDateId = addslashes(trim($_REQUEST['outDateId']));

		getRoomType($hotelId, $inDateId, $outDateId);

		exit();
		break;

	case 'generateRoomNumber':

		$packageId = addslashes(trim($_REQUEST['packageId']));

		getRoomNumber($packageId);

		exit();
		break;

	case 'getEmailValidation':
		$email                  	= trim($_REQUEST['email']);
		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser['QUERY']       		= "SELECT `id` 
											 FROM " . _DB_USER_REGISTRATION_ . "
											WHERE `user_email_id` = '" . $email . "' 
											  AND `status` = 'A' ";
		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
		$row 						=	$resultFetchUser[0];

		if ($resultFetchUser) {
			$sqlFetchUser           = registrationDetailsQuerySet($row['id']);
			$resultFetchUser        = $mycms->sql_select($sqlFetchUser);
			$rowFetchUser           = $resultFetchUser[0];

			if (
				$rowFetchUser['registration_payment_status'] == "PAID"
				|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
				|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
			) {
				$availabilityStatus 	= "IN_USE";
			} else {
				$availabilityStatus 	= "NOT_PAID";
			}
		} else {
			$mycms->setSession('USER_EMAIL_FROM_INDEX', $email);
			$availabilityStatus 	= "AVAILABLE";
		}
		echo $availabilityStatus;
		exit();
		break;


	case 'getEmailValidationStatus':


		$email                  	= trim($_REQUEST['email']);
		$mycms->setSession('USER_EMAIL_FROM_INDEX', $email);
		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser               = array();
		$sqlFetchUser['QUERY']       = "SELECT * 
											 FROM " . _DB_USER_REGISTRATION_ . "
											WHERE `user_email_id` = ? 
											  AND `status` = ?";
		// AND `registration_request` = 'GENERAL'	

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
		$row 						= $resultFetchUser[0];

		$rowFetchUser	=	array();
		if ($resultFetchUser) {
			$rowFetchUser           = $resultFetchUser[0];
		}

		header('Content-type: application/json');

		if (!empty($rowFetchUser) && ($rowFetchUser['registration_request'] == 'GENERAL')) {

			$rowFetchUser           = $resultFetchUser[0];

			if (
				$rowFetchUser['registration_payment_status'] == "PAID"
				|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
				|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
			) {
				$availabilityStatus 	= '{"STATUS" : "IN_USE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			} else {
				$availabilityStatus 	= '{"STATUS" : "NOT_PAID", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			}
		} else {
			$availabilityStatus 	= '{"STATUS" : "AVAILABLE"';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';
				$dataString                .= '"ID": "' . $rowFetchUser['id'] . '",';
				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		}
		echo $availabilityStatus;
		exit();
		break;

	case 'getEmailValidationStatusIndex':
		$email                  	= trim($_REQUEST['email']);
		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser               = array();
		$sqlFetchUser['QUERY']       = "SELECT * 
											 FROM " . _DB_USER_REGISTRATION_ . "
											WHERE `user_email_id` = ? 
											  AND `status` = ?";
		// AND `registration_request` = 'GENERAL'	

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
		$row 						= $resultFetchUser[0];

		$rowFetchUser	=	array();
		if ($resultFetchUser) {
			$rowFetchUser           = $resultFetchUser[0];
		}

		header('Content-type: application/json');

		if (!empty($rowFetchUser) && ($rowFetchUser['registration_request'] == 'GENERAL' || $rowFetchUser['registration_request'] == 'SPOT')) {

			$rowFetchUser           = $resultFetchUser[0];
			//------------------------------------------------------ 1. to Check active abstract -------------------------------------------------------------------------------------
			$sqlFetchAbs               = array();
			$sqlFetchAbs['QUERY']       = "SELECT * 
											 FROM " . _DB_ABSTRACT_REQUEST_ . "
											WHERE `applicant_id` = ? 
											  AND `status` = ?";

			$sqlFetchAbs['PARAM'][]   = array('FILD' => 'applicant_id', 'DATA' => $rowFetchUser['id'], 'TYP' => 's');
			$sqlFetchAbs['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchAbs    		= $mycms->sql_select($sqlFetchAbs);
			if ($resultFetchAbs) {
				$isAbstract = 'Y';
			} else {
				$isAbstract = 'N';
			}
			//----------------------------------------------- 2.  to check payment terms set or not ---------------------------------------------------------------------------------
			$isSetPayment = getPaymentDetailsDelegate($rowFetchUser['id']);
			// ====================================================================== X =======================================================================

			// print_r($isSetPayment);die;
			if (
				$rowFetchUser['registration_payment_status'] == "PAID"
				|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
				|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
			) {
				send_uniqueSequence($rowFetchUser['id'], 'SEND');
				$availabilityStatus 	= '{"STATUS" : "IN_USE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			} else if (
				$rowFetchUser['registration_payment_status'] == "UNPAID"
				&& $rowFetchUser['registration_mode'] == 'OFFLINE'
				&& $isSetPayment == ''
			) {
				send_uniqueSequence($rowFetchUser['id'], 'SEND');
				$availabilityStatus 	= '{"STATUS" : "NOT_PAID_OFFLINE_NOT_SET", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			} else if (
				$rowFetchUser['registration_payment_status'] == "UNPAID"
				&& $isAbstract == 'Y' && $isSetPayment != ''
				&& $rowFetchUser['registration_mode'] == 'OFFLINE'
			) {
				send_uniqueSequence($rowFetchUser['id'], 'SEND');
				$availabilityStatus 	= '{"STATUS" : "ABSTRACT_UNPAID_OFFLINE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			} else if (
				$rowFetchUser['registration_payment_status'] == "UNPAID"
				&& $isAbstract == 'Y'
				&& $rowFetchUser['registration_mode'] == 'ONLINE'
			) {
				send_uniqueSequence($rowFetchUser['id'], 'SEND');
				$availabilityStatus 	= '{"STATUS" : "ABSTRACT_UNPAID_ONLINE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			} else if (
				$rowFetchUser['registration_payment_status'] == "UNPAID"
				&& $rowFetchUser['registration_mode'] == 'OFFLINE'
				&& $isAbstract == 'N'
			) {
				$availabilityStatus 	= '{"STATUS" : "NOT_PAID_OFFLINE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			}else if( $isAbstract == 'N' && $rowFetchUser['isArtWorkSubmitted']=='Y'){
					send_uniqueSequence($rowFetchUser['id'], 'SEND');
			     $availabilityStatus 	= '{"STATUS" : "NOT_AVAILABLE","ARTWRK" : "YES", "ID" : "' . $rowFetchUser['id'] . '"}';

			} else {
				// NOT PAID ONLINE
				$availabilityStatus 	= '{"STATUS" : "NOT_PAID", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			}
		} else if (!empty($rowFetchUser)  && ($rowFetchUser['registration_request'] == 'FACULTY')) {
			$availabilityStatus 	= '{"STATUS" : "FACULTY","ID": "' . $rowFetchUser['id'] . '","UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '" ';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';

				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';
				$dataString                .= '"UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		} else if (!empty($rowFetchUser) /* && ($rowFetchUser['registration_request'] == 'ABSTRACT')*/) {
			$availabilityStatus 	= '{"STATUS" : "AVAILABLE","ID": "' . $rowFetchUser['id'] . '","UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '" ';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';

				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';
				$dataString                .= '"UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		} else {
			$availabilityStatus 	= '{"STATUS" : "NOT_AVAILABLE"';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';

				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';
				$dataString                .= '"UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		}
		echo $availabilityStatus;
		exit();
		break;

	case 'getEmailValidationStatusAbstract':
		$email                  	= trim($_REQUEST['email']);
		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser               = array();
		$sqlFetchUser['QUERY']       = "SELECT * 
											 FROM " . _DB_USER_REGISTRATION_ . "
											WHERE `user_email_id` = ? 
											  AND `status` = ?";
		// AND `registration_request` = 'GENERAL'	

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
		$row 						= $resultFetchUser[0];

		$rowFetchUser	=	array();
		if ($resultFetchUser) {
			$rowFetchUser           = $resultFetchUser[0];
		}

		header('Content-type: application/json');

		if (!empty($rowFetchUser) && ($rowFetchUser['registration_request'] == 'GENERAL' || $rowFetchUser['registration_request'] == 'SPOT')) {

			send_uniqueSequence($rowFetchUser['id'], 'SEND');
			$availabilityStatus 	= '{"STATUS" : "IN_USE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';

			// $rowFetchUser           = $resultFetchUser[0];

			// $sqlFetchAbs               = array();
			// $sqlFetchAbs['QUERY']       = "SELECT * 
			// 								 FROM " . _DB_ABSTRACT_REQUEST_ . "
			// 								WHERE `applicant_id` = ? 
			// 								  AND `status` = ?";

			// $sqlFetchAbs['PARAM'][]   = array('FILD' => 'applicant_id', 'DATA' => $rowFetchUser['id'], 'TYP' => 's');
			// $sqlFetchAbs['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			// $resultFetchAbs    		= $mycms->sql_select($sqlFetchAbs);
			// if ($resultFetchAbs) {
			// 	// If the user has an abstract request, we consider it as in use
			// 	$isAbstract = 'Y';
			// } else {
			// 	$isAbstract = 'N';
			// }
			// if (
			// 	$rowFetchUser['registration_payment_status'] == "PAID"
			// 	|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
			// 	|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
			// 	/*||  $isAbstract == 'Y'*/
			// ) {
			// 	send_uniqueSequence($rowFetchUser['id'], 'SEND');
			// 	$availabilityStatus 	= '{"STATUS" : "IN_USE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			// } else if (
			// 	$rowFetchUser['registration_payment_status'] == "UNPAID"
			// 	&& $isAbstract == 'Y'
			// 	&& $rowFetchUser['registration_mode'] == 'OFFLINE'
			// ) {
			// 	send_uniqueSequence($rowFetchUser['id'], 'SEND');
			// 	$availabilityStatus 	= '{"STATUS" : "ABSTRACT_UNPAID_OFFLINE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			// } else if (
			// 	$rowFetchUser['registration_payment_status'] == "UNPAID"
			// 	&& $isAbstract == 'Y'
			// 	&& $rowFetchUser['registration_mode'] == 'ONLINE'
			// ) {
			// 	send_uniqueSequence($rowFetchUser['id'], 'SEND');
			// 	$availabilityStatus 	= '{"STATUS" : "ABSTRACT_UNPAID_ONLINE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			// } else if ($rowFetchUser['registration_payment_status'] == "UNPAID" && $rowFetchUser['registration_mode'] == 'OFFLINE') {

			// 	$isSetPayment = getPaymentDetailsDelegate($rowFetchUser['id']);
			// 	if ($isSetPayment == '') {
			// 		//PAYMENT NOT SET
			// 		$availabilityStatus 	= '{"STATUS" : "NOT_PAID_OFFLINE_NOT_SET", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			// 	} else {
			// 		$availabilityStatus 	= '{"STATUS" : "NOT_PAID_OFFLINE", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			// 	}
			// } else {
			// 	// NOT PAID ONLINE
			// 	$availabilityStatus 	= '{"STATUS" : "NOT_PAID", "UNIQUE_SEQUENCE" : "' . $rowFetchUser['user_unique_sequence'] . '", "ID" : "' . $rowFetchUser['id'] . '"}';
			// }
		} else if (!empty($rowFetchUser)  && ($rowFetchUser['registration_request'] == 'FACULTY')) {
			$availabilityStatus 	= '{"STATUS" : "FACULTY","ID": "' . $rowFetchUser['id'] . '","UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '" ';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';

				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';
				$dataString                .= '"UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		} 
		// else if ($_REQUEST['altEmail'] == 'Y') { // FOR NATCON2025

		// 	$sqlFetchUser               = array();
		// 	$sqlFetchUser['QUERY']       = "SELECT * 
		// 									 FROM " . _DB_SP_PARTICIPANT_DETAILS_ . "
		// 									WHERE `participant_alternative_email_id` = ? 
		// 									  AND `status` = ?";
		// 	// AND `registration_request` = 'GENERAL'	

		// 	$sqlFetchUser['PARAM'][]   = array('FILD' => 'participant_alternative_email_id', 'DATA' => $email, 'TYP' => 's');
		// 	$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		// 	$resultFetchSc    		= $mycms->sql_select($sqlFetchUser);
		// 	$rowFetchSc				= $resultFetchSc[0];
		// 	if (!empty($rowFetchSc)) {

		// 		$availabilityStatus 	= '{"STATUS" : "FACULTY","ID": "' . $rowFetchSc['id'] . '" }';
		// 	}
		// }
		 else if (!empty($rowFetchUser) /* && ($rowFetchUser['registration_request'] == 'ABSTRACT')*/) {
			$availabilityStatus 	= '{"STATUS" : "AVAILABLE","ID": "' . $rowFetchUser['id'] . '","UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '","MOBILE_NO": "' . $rowFetchUser['participant_mobile_no'] . '" ';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';

				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';
				$dataString                .= '"UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		} else {
			$availabilityStatus 	= '{"STATUS" : "NOT_AVAILABLE"';

			$sqlFetchUser  = array();
			$sqlFetchUser['QUERY']        = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";

			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$dataString                 = "";
			if ($resultFetchUser) {

				$rowFetchUser				= $resultFetchUser[0];
				$dataString                = '"DATA" : {';

				$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
				$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
				$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
				$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
				$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';
				$dataString                .= '"UNIQUE_SEQUENCE": "' . $rowFetchUser['user_unique_sequence'] . '",';

				$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
				$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

				$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

				$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
				$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
				$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
				$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

				$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
				$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
				$dataString                .= '}';
			}
			$availabilityStatus .= (trim($dataString) == '') ? '}' : ',' . $dataString . '}';
		}
		echo $availabilityStatus;
		exit();
		break;


	case 'getMobileValidation':

		$mobile                  	= trim($_REQUEST['mobile']);

		$availabilityStatus 		= "AVAILABLE";

		$sqlFetchUser              = array();
		$sqlFetchUser['QUERY']     = "SELECT `id` FROM " . _DB_USER_REGISTRATION_ . "
												  WHERE `user_mobile_no` = ? 
												  	AND `registration_request` = 'GENERAL'
													AND `status` =?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_mobile_no', 'DATA' => $mobile, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',     'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
		$maxRowsUser                = $mycms->sql_numrows($resultFetchUser);

		if ($maxRowsUser == 0) {

			$availabilityStatus 	= "AVAILABLE";
		} else {

			$availabilityStatus 	= "IN_USE";
		}

		echo $availabilityStatus;
		exit();
		break;

	case 'getAccompanyDetails':

		$accompanyId        = trim($_REQUEST['accompanyId']);

		$sqlAccompanyDetails['QUERY'] = "SELECT * FROM " . _DB_USER_REGISTRATION_ . " 							
									  WHERE `id` = '" . $accompanyId . "'";

		$resAccompanyDetails = $mycms->sql_select($sqlAccompanyDetails);

		$rowAccompanyUserDetails  =  $resAccompanyDetails[0];

		header('Content-type: application/json');

		$dataString                 = "";

		if ($rowAccompanyUserDetails) {
			$dataString                 = '{';
			$dataString                .= '"ACCOMAPNY_FULL_NAME": "' . $rowAccompanyUserDetails['user_full_name'] . '",';
			$dataString        		   .= '"ACCOMAPNY_AGE": "' . $rowAccompanyUserDetails['user_age'] . '",';
			$dataString        		   .= '"ACCOMAPNY_FOOD_PREFERENCE": "' . $rowAccompanyUserDetails['user_food_preference'] . '"';
			$dataString                .= '}';
		}
		echo $dataString;

		exit();
		break;

	case 'getDelegateDetails':


		$email                  	= trim($_REQUEST['email']);

		$sqlFetchUser   = array();
		$sqlFetchUser['QUERY']       	= "SELECT * 
											 FROM " . _DB_COMN_USER_DATA_ . "
											WHERE `user_email_id` = ? 
											  AND `status` = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);


		$dataString                 = "";

		header('Content-type: application/json');

		if ($resultFetchUser) {

			$rowFetchUser				= $resultFetchUser[0];
			$dataString                 = '{';

			$dataString                .= '"TITLE": "' . $rowFetchUser['user_title'] . '",';
			$dataString                .= '"FIRST_NAME": "' . $rowFetchUser['user_first_name'] . '",';
			$dataString                .= '"MIDDLE_NAME": "' . $rowFetchUser['user_middle_name'] . '",';
			$dataString        		   .= '"LAST_NAME": "' . $rowFetchUser['user_last_name'] . '",';
			$dataString                .= '"FULL_NAME": "' . $rowFetchUser['user_full_name'] . '",';

			$dataString                .= '"MOBILE_ISD_CODE": "' . $rowFetchUser['user_mobile_isd_code'] . '",';
			$dataString                .= '"MOBILE_NO": "' . $rowFetchUser['user_mobile_no'] . '",';

			$dataString                .= '"ADDRESS": "' . $rowFetchUser['user_address'] . '",';

			$dataString                .= '"COUNTRY_ID": "' . $rowFetchUser['user_country_id'] . '",';
			$dataString                .= '"STATE_ID": "' . $rowFetchUser['user_state_id'] . '",';
			$dataString                .= '"CITY": "' . $rowFetchUser['user_city_name'] . '",';
			$dataString                .= '"PIN_CODE": "' . $rowFetchUser['user_pincode'] . '",';

			$dataString                .= '"GENDER": "' . $rowFetchUser['user_gender'] . '",';
			$dataString                .= '"FOOD_PREFERENCE": "' . $rowFetchUser['user_food_preference'] . '"';
			$dataString                .= '}';
		}
		echo $dataString;
		exit();
		break;

	case 'getAllPackage':

		$currentCutoffId = getTariffCutoffId();
		$msg = '<div class="custom-radio-holder">';
		if (!empty($_REQUEST['hotel_id'])) {


			$sql_Count_hotel  =   array();
			$sql_Count_hotel['QUERY'] =   "SELECT COUNT(*) AS countdata 
                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . "
                                            WHERE `status`  = ?
                                            AND `hotel_id`  = ?
                                            ORDER BY `id` ASC";

			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_Count_hotel = $mycms->sql_select($sql_Count_hotel);
			$countSeatLimit = $row_Count_hotel[0]['countdata'];


			$sql_package  =   array();
			$sql_package['QUERY'] =   "SELECT * 
                                            FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                            WHERE `status`  = ?
                                            AND `hotel_id`  = ?
                                            ORDER BY `id` ASC";

			$sql_package['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_package['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_package = $mycms->sql_select($sql_package);

			$sql_hotel  =   array();
			$sql_hotel['QUERY'] =   "SELECT * 
                                            FROM " . _DB_MASTER_HOTEL_ . "
                                            WHERE `status`  = ?
                                            AND `id`  = ?
                                            ORDER BY `id` ASC";

			$sql_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_hotel['PARAM'][]   =   array('FILD' => 'id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_hotel = $mycms->sql_select($sql_hotel);

			//print_r($row_hotel[0]['hotel_name']);
			//die();
			$hotel_seat_limit = $row_hotel[0]['seat_limit'];


			$presentSeatLimit = $hotel_seat_limit - $countSeatLimit;
			if ($presentSeatLimit > 0) {
				$dates = array();
				$dCount = 0;
				$packageCheckDate = array();
				$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
															   WHERE `hotel_id` = ?
																 AND `status` = ?
														    ORDER BY  check_in_date";
				$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	'TYP' => 's');
				$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
				$resCheckIns = $mycms->sql_select($packageCheckDate);
				$check_in_array = array();
				foreach ($resCheckIns as $key => $rowCheckIn) {
					$packageCheckoutDate = array();
					$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
																   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
																  WHERE `hotel_id` = ?
																    AND `status` = ?
																    AND `check_out_date` > ?
															   ORDER BY check_out_date";
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	    'TYP' => 's');
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


					$resCheckOut = $mycms->sql_select($packageCheckoutDate);
					//echo '<pre>'; print_r($resCheckOut);
					foreach ($resCheckOut as $key => $rowCheckOut) {
						$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
						$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
						$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
						$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
						$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

						$dCount++;
					}
				}

				//echo '<pre>'; print_r($dates);
				//die();
				if ($row_package) {

					$i = 1;
					$checked = '';
					$flag = 0;
					foreach ($row_package as $key => $value) {


						$sql_package_price  =   array();
						$sql_package_price['QUERY'] =   "SELECT * 
					                                            FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
					                                            WHERE `status`  = ?
					                                            AND `hotel_id`  = ?
					                                            AND `package_id`  = ?
					                                             AND `tariff_cutoff_id`  = ?
					                                             AND `inr_amount`  >0
					                                            ORDER BY `id` ASC";

						$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');

						$row_package_price = $mycms->sql_select($sql_package_price);

						if ($i == 1) {
							$checked = 'checked';
						} else {
							$checked = '';
						}


						if ($row_package_price[0]['inr_amount'] > 0) {
							$flag = 1;
							$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

							$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];





							$msg .= '<li style="color:#767676;"><label class="container-box custom-radio menu-container-box">
				                        <i style="color: #fff; ">' . $packageLebe . '</i>
				                        <input type="radio" name="package_id" id="package_id" value="' . $value['id'] . '" ' . $checked . '  currency="' . $row_package_price[0]['currency'] . '"
				                         amount="' . $row_package_price[0]['inr_amount'] . '" onchange="getPackageVal(this.value)"  invoiceTitle="' . $invoiceTitle . '" package="accomodation">
				                        <span class="checkmark"></span>
				                        </label></li>
				                       ';
						}



						$i++;
					}




					if ($flag == 1) {

						$msg .= '</div><select operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id" id="accomodation_package_checkin_id" style="color:#767676 !important; height: 38px !important;" onchange="get_checkin_val(this.value)">
					                	 	<option value="">Select Check In Date</option>
					                	 	';
						//echo '<pre>'; print_r(array_unique($dates));
						$tempCheckIN = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));

						//echo '<pre>'; print_r($tempCheckIN);	


						foreach ($tempCheckIN as $key => $value) {


							$checkInVal = $key . "/" . $value;
							$msg .= '<option value="' . $checkInVal . '">' . $value . '</option>';
						}

						$msg .= '</select>';

						$msg .= '<select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id" id="accomodation_package_checkout_id" style="color:#767676 !important; height: 38px !important;margin-inline: 3px;" onchange="get_checkout_val(this.value)" accommodation="checkout">
					                	 	<option value="">Select Check Out Date</option>
					                	 	';

						$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));
						foreach ($tempCheckOUT as $key => $value) {
							$checkOutVal = $key . "/" . $value;
							$msg .= '<option value="' . $checkOutVal . '">' . $value . '</option>';
						}

						$msg .= '</select>';

						$msg .= '<div class="alert alert-danger" callFor="alert" id="hotelErr" style="display: none;">Please enter a proper value.</div>';
					} else {
						$msg .= "<span style='color:#eb4d4d;'>Please set package price from backend!</span>";
					}
				}

				echo $msg;
			} else {
				echo 'error';
			}
		} else {
			echo $msg;
		}


		exit();
		break;

	case 'getAllPackageProfile':
		include_once("includes/function.registration.php");
		$currentCutoffId = getTariffCutoffId();

		$msg = '<div class="custom-radio-holder">';
		if (!empty($_REQUEST['hotel_id'])) {


			$sql_Count_hotel  =   array();
			$sql_Count_hotel['QUERY'] =   "SELECT COUNT(*) AS countdata 
                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . "
                                            WHERE `status`  = ?
                                            AND `hotel_id`  = ?
                                            ORDER BY `id` ASC";

			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_Count_hotel = $mycms->sql_select($sql_Count_hotel);
			$countSeatLimit = $row_Count_hotel[0]['countdata'];


			$sql_package  =   array();
			$sql_package['QUERY'] =   "SELECT * 
                                            FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                            WHERE `status`  = ?
                                            AND `hotel_id`  = ?
                                            ORDER BY `id` ASC";

			$sql_package['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_package['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_package = $mycms->sql_select($sql_package);

			$sql_hotel  =   array();
			$sql_hotel['QUERY'] =   "SELECT * 
                                            FROM " . _DB_MASTER_HOTEL_ . "
                                            WHERE `status`  = ?
                                            AND `id`  = ?
                                            ORDER BY `id` ASC";

			$sql_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_hotel['PARAM'][]   =   array('FILD' => 'id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_hotel = $mycms->sql_select($sql_hotel);

			// print_r($row_hotel[0]['hotel_name']);
			//die();
			$hotel_seat_limit = $row_hotel[0]['seat_limit'];

			$dates = array();
			$dCount = 0;
			$packageCheckDate = array();
			$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
													   WHERE `hotel_id` = ?
														 AND `status` = ?
												    ORDER BY  check_in_date";
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	'TYP' => 's');
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
			$resCheckIns = $mycms->sql_select($packageCheckDate);
			$check_in_array = array();
			foreach ($resCheckIns as $key => $rowCheckIn) {
				$packageCheckoutDate = array();
				$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
														   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
														  WHERE `hotel_id` = ?
														    AND `status` = ?
														    AND `check_out_date` > ?
													   ORDER BY check_out_date";
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	    'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


				$resCheckOut = $mycms->sql_select($packageCheckoutDate);
				//echo '<pre>'; print_r($resCheckOut);
				foreach ($resCheckOut as $key => $rowCheckOut) {
					$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
					$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
					$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
					$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
					$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

					$dCount++;
				}
			}

			//echo '<pre>'; print_r($dates);
			//die();
			if ($row_package) {
				/*$msg.='<select operationmode="hotel_select_package_id" name="hotel_select_package_id" id="hotel_select_package_id" style="color:#767676 !important;width: 249px !important; height: 38px !important;">
                	 	<option value="">Select Package</option>
                	 	';*/
				$i = 1;
				$checked = '';
				$flag = 0;
				foreach ($row_package as $key => $value) {
					//echo '<pre>'; print_r($value);
					//$msg.='<option value="'.$value['id'].'">'.$value['package_name'].'</option>';

					$sql_package_price  =   array();
					$sql_package_price['QUERY'] =   "SELECT * 
		                                            FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
		                                            WHERE `status`  = ?
		                                            AND `hotel_id`  = ?
		                                            AND `package_id`  = ?
		                                            AND `tariff_cutoff_id`  = ?
		                                            ORDER BY `id` ASC";

					$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
					$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
					$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');
					$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');

					$row_package_price = $mycms->sql_select($sql_package_price);
					// echo '<pre>'; print_r($sql_package_price);

					if ($i == 1) {
						$checked = 'checked';
					} else {
						$checked = '';
					}

					if ($row_package_price[0]['inr_amount'] > 0) {
						$flag = 1;
						$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

						$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];

						//echo 'check='. $checked;
						$msg .= '<span style="color:#767676;"> <a><label class="container-box custom-radio menu-container-box">
		                        <span style="color: #6a5c5c; ">' . $packageLebe . '</span>
		                        <input type="radio" name="package_id" id="package_id" value="' . $value['id'] . '" ' . $checked . '  currency="' . $row_package_price[0]['currency'] . '"
		                         amount="' . $row_package_price[0]['inr_amount'] . '" onchange="getPackageVal(this.value)"  invoiceTitle="' . $invoiceTitle . '" package="accomodation">
		                        <span class="checkmark"></span>
		                        </a></label></span>
	                       ';
					}


					$i++;
				}
				//$msg.='</select>';


				if ($flag == 1) {

					$msg .= '</div><div class="accom-hotel-wrap">';

					$msg .= '<select operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id" id="accomodation_package_checkin_id" style="color:#767676 !important; height: 38px !important;" onchange="get_checkin_val(this.value)">
	                	 	<option value="">Select Check In Date</option>
	                	 	';
					//echo '<pre>'; print_r(array_unique($dates));
					$tempCheckIN = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));

					//echo '<pre>'; print_r($tempCheckIN);	


					foreach ($tempCheckIN as $key => $value) {


						$checkInVal = $key . "/" . $value;
						$msg .= '<option value="' . $checkInVal . '">' . $value . '</option>';
					}

					$msg .= '</select>';

					$msg .= '<select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id" id="accomodation_package_checkout_id" style="color:#767676 !important; height: 38px !important;margin-inline: 3px;" onchange="get_checkout_val(this.value)" accommodation="checkout">
	                	 	<option value="">Select Check Out Date</option>
	                	 	';

					$msg .= '</div>';

					$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));
					foreach ($tempCheckOUT as $key => $value) {
						$checkOutVal = $key . "/" . $value;
						$msg .= '<option value="' . $checkOutVal . '">' . $value . '</option>';
					}

					$msg .= '</select>';

					$msg .= '<div class="alert alert-danger" callFor="alert" id="hotelErr" style="display: none;">Please enter a proper value.</div>';
				} else {
					$msg .= '<span style="color:#eb4d4d;">Please set package price from backend!</span>';
				}
			}

			echo $msg;
		} else {
			echo $msg;
		}


		exit();
		break;

	case 'getAllPackageProfileRoom':
		include_once("includes/function.registration.php");
		$msg = '';
		$currentCutoffId = getTariffCutoffId();

		if (!empty($_REQUEST['hotel_id'])) {
			$delegateId = $_REQUEST['delegateId'];
			$getExistingAccommodationList = getTotalAccommodationWithoutCombo($delegateId);



			$checkinIdArray = array();
			$checkOutIdArray = array();
			foreach ($getExistingAccommodationList as $key => $room_val) {
				$checkin_date = $room_val['checkin_date'];
				$checkout_date = $room_val['checkout_date'];
				$hotel_id = $room_val['hotel_id'];

				$checkInIds = getVariableCheckInId($hotel_id, $checkin_date, $checkout_date);

				$checkOutIds = getVariableCheckOutId($hotel_id, $checkin_date, $checkout_date);


				foreach ($checkInIds as $key => $value) {
					$dateInId = $value['id'] . "/" . $value['check_in_date'];
					array_push($checkinIdArray, $dateInId);
				}


				foreach ($checkOutIds as $key => $value) {
					$dateOutId = $value['id'] . "/" . $value['check_out_date'];
					array_push($checkOutIdArray, $dateOutId);
				}
			}



			$sql_Count_hotel  =   array();
			$sql_Count_hotel['QUERY'] =   "SELECT COUNT(*) AS countdata 
                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . "
                                            WHERE `status`  = ?
                                            AND `hotel_id`  = ?
                                            ORDER BY `id` ASC";

			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_Count_hotel = $mycms->sql_select($sql_Count_hotel);
			$countSeatLimit = $row_Count_hotel[0]['countdata'];


			$sql_package  =   array();
			$sql_package['QUERY'] =   "SELECT * 
                                            FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                            WHERE `status`  = ?
                                            AND `hotel_id`  = ?
                                            ORDER BY `id` ASC";

			$sql_package['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_package['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_package = $mycms->sql_select($sql_package);

			$sql_hotel  =   array();
			$sql_hotel['QUERY'] =   "SELECT * 
                                            FROM " . _DB_MASTER_HOTEL_ . "
                                            WHERE `status`  = ?
                                            AND `id`  = ?
                                            ORDER BY `id` ASC";

			$sql_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_hotel['PARAM'][]   =   array('FILD' => 'id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_hotel = $mycms->sql_select($sql_hotel);

			// print_r($row_hotel[0]['hotel_name']);
			//die();
			$hotel_seat_limit = $row_hotel[0]['seat_limit'];

			$dates = array();
			$dCount = 0;
			$packageCheckDate = array();
			$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
													   WHERE `hotel_id` = ?
														 AND `status` = ?
												    ORDER BY  check_in_date";
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	'TYP' => 's');
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
			$resCheckIns = $mycms->sql_select($packageCheckDate);
			$check_in_array = array();
			foreach ($resCheckIns as $key => $rowCheckIn) {
				$packageCheckoutDate = array();
				$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
														   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
														  WHERE `hotel_id` = ?
														    AND `status` = ?
														    AND `check_out_date` > ?
													   ORDER BY check_out_date";
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	    'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


				$resCheckOut = $mycms->sql_select($packageCheckoutDate);
				//echo '<pre>'; print_r($resCheckOut);
				foreach ($resCheckOut as $key => $rowCheckOut) {
					$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
					$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
					$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
					$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
					$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

					$dCount++;
				}
			}

			//echo '<pre>'; print_r($dates);
			//die();
			if ($row_package) {
				/*$msg.='<select operationmode="hotel_select_package_id" name="hotel_select_package_id" id="hotel_select_package_id" style="color:#767676 !important;width: 249px !important; height: 38px !important;">
                	 	<option value="">Select Package</option>
                	 	';*/
				$i = 1;
				$checked = '';
				foreach ($row_package as $key => $value) {
					//echo '<pre>'; print_r($value);
					//$msg.='<option value="'.$value['id'].'">'.$value['package_name'].'</option>';

					$sql_package_price  =   array();
					$sql_package_price['QUERY'] =   "SELECT * 
			                                            FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
			                                            WHERE `status`  = ?
			                                            AND `hotel_id`  = ?
			                                            AND `package_id`  = ?

			                                            AND `tariff_cutoff_id`  = ?
			                                            ORDER BY `id` ASC";

					$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
					$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
					$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');

					$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');
					$row_package_price = $mycms->sql_select($sql_package_price);
					// echo '<pre>'; print_r($sql_package_price);

					if ($i == 1) {
						$checked = 'checked';
					} else {
						$checked = '';
					}

					$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

					$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];

					//echo 'check='. $checked;
					$msg .= '<span style="color:#767676;"> <a><label class="container-box menu-container-box">
	                        <span style="color: #6a5c5c; ">' . $packageLebe . '</span>
	                        <input type="radio" name="package_id" id="package_id" value="' . $value['id'] . '" ' . $checked . '  currency="' . $row_package_price[0]['currency'] . '"
	                         amount="' . $row_package_price[0]['inr_amount'] . '" onchange="getPackageValRoom(this.value)"  invoiceTitle="' . $invoiceTitle . '" package="accomodation">
	                        <span class="checkmark menu-checkmark"></span>
	                        </a></label></span>
	                       ';
					$i++;
				}
				//$msg.='</select>';


				$msg .= '<div class="accom-hotel-wrap">';

				$msg .= '<select operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id" id="accomodation_package_checkin_id" style="color:#767676 !important; height: 38px !important;" onchange="get_checkin_val(this.value)">
	                	 	<option value="">Select Check In Date</option>
	                	 	';
				//echo '<pre>'; print_r(array_unique($dates));
				$tempCheckIN = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));

				//echo '<pre>'; print_r($tempCheckIN);	


				foreach ($tempCheckIN as $key => $value) {


					$checkInVal = $key . "/" . $value;
					//if(!in_array($checkInVal, $checkinIdArray))
					//{


					$msg .= '<option value="' . $checkInVal . '" >' . $value . '</option>';
					//}
				}

				$msg .= '</select>';

				$msg .= '<select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id" id="accomodation_package_checkout_id" style="color:#767676 !important; height: 38px !important;margin-inline: 3px;" onchange="get_checkout_val_room(this.value)" accommodation="checkout">
                	 	<option value="">Select Check Out Date</option>
                	 	';

				$msg .= '</div>';


				$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));
				foreach ($tempCheckOUT as $key => $value) {
					$checkOutVal = $key . "/" . $value;
					//if(!in_array($checkOutVal, $checkOutIdArray))
					//{
					$msg .= '<option value="' . $checkOutVal . '">' . $value . '</option>';
					//}	
				}

				$msg .= '</select>';

				$msg .= '<div class="alert alert-danger" callFor="alert" id="hotelErr" style="display: none;">Please enter a proper value.</div>';
			}

			echo $msg;
		} else {
			echo $msg;
		}


		exit();
		break;

	case 'getAccommodationDetails':

		include_once("../../includes/function.registration.php");

		$hotel_id  = trim($_REQUEST['hotel_id']);
		$currentCutoffId = getTariffCutoffId();

		if (!empty($hotel_id)) {

			//========= Fetch Hotel Accessories Start ==========//
			$delegateId = $_REQUEST['abstractDelegateId'];
			$getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);
			$totalCount = 3 - $getAccommodationMaxRoom;
			$msg = '';
			$sqlAcc = array();
			$sqlAcc['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $_REQUEST['hotel_id'] . "' AND status='A' AND purpose='aminity' ";

			$queryAcc = $mycms->sql_select($sqlAcc, false);

			//========= Fetch Hotel Accessories End ==========//

			$sql_Count_hotel  =   array();
			$sql_Count_hotel['QUERY'] =   "SELECT COUNT(*) AS countdata 
															FROM " . _DB_REQUEST_ACCOMMODATION_ . "
															WHERE `status`  = ?
															AND `hotel_id`  = ?
															ORDER BY `id` ASC";

			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_Count_hotel = $mycms->sql_select($sql_Count_hotel);
			$countSeatLimit = $row_Count_hotel[0]['countdata'];


			$sql_package  =   array();
			$sql_package['QUERY'] =   "SELECT * 
														FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
														WHERE `status`  = ?
														AND `hotel_id`  = ?
														ORDER BY `id` ASC";

			$sql_package['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_package['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_package = $mycms->sql_select($sql_package);
			// print_r($sql_package); die;

			$sql_hotel  =   array();
			$sql_hotel['QUERY'] =   "SELECT * 
														FROM " . _DB_MASTER_HOTEL_ . "
														WHERE `status`  = ?
														AND `id`  = ?
														ORDER BY `id` ASC";

			$sql_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_hotel['PARAM'][]   =   array('FILD' => 'id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_hotel = $mycms->sql_select($sql_hotel);

			//print_r($row_hotel[0]['hotel_name']);
			//die();
			$hotel_seat_limit = $row_hotel[0]['seat_limit'];
			//hotel_image -> hotel_background_image
			$hotel_image = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row_hotel[0]['hotel_background_image'];


			$msg .=  '
							<div class="vanue-image aos-init aos-animate" ><img
							  src="' . $hotel_image . '" alt=""/></div>
							  <div class="vanue-form">
							  <div class="pxl-item--content">
						  <div class="pxl-item--popular pxl-flex " >
							<span class="accm-rating">
							  <i class="fas fa-star stared"></i>
							  <i class="fas fa-star stared"></i>
							  <i class="fas fa-star stared"></i>
							  <i class="fas fa-star stared"></i>
							  <i class="fas fa-star stared"></i>
							</span>
						  </div>
						  <h3 class="pxl-item--title">' . $row_hotel[0]['hotel_name'] . '</h3>
						  <div class="pxl-item--desc"><i class="far fa-map-marker-alt"></i> ' . $row_hotel[0]['hotel_address'] . '</div>
						  <div class="pxl-item--desc"><i class="fal fa-utensils"></i> Breakfast Included</div>
						</div>';


			if ($queryAcc) {

				$msg .=  '<div class="vanue-details">
							  <div class="row">
							  <input type="hidden" name="hotel_select_acco_id" value="' . $hotel_id . '">
							  ';
				$i = 1;
				foreach ($queryAcc as $k => $row) {

					$icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];

					$msg .= '<div class="col-lg-2" data-aos="zoom-in" data-aos-delay="100"
											data-aos-duration="500">
											<img src="' . $icon . '" />
											<p>' . $row['accessories_name'] . '</p>
										  </div>';
				}

				$msg .= '</div>
							   </div>';
			}




			$presentSeatLimit = $hotel_seat_limit - $countSeatLimit;
			$presentSeatLimit = 500; // test

			if ($presentSeatLimit > 0) {

				$dates = array();
				$dCount = 0;
				$packageCheckDate = array();
				$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
																			   WHERE `hotel_id` = ?
																				 AND `status` = ?
																			ORDER BY  check_in_date";
				$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	'TYP' => 's');
				$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
				$resCheckIns = $mycms->sql_select($packageCheckDate);
				$check_in_array = array();
				foreach ($resCheckIns as $key => $rowCheckIn) {
					$packageCheckoutDate = array();
					$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
																				   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
																				  WHERE `hotel_id` = ?
																					AND `status` = ?
																					AND `check_out_date` > ?
																			   ORDER BY check_out_date";
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	    'TYP' => 's');
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


					$resCheckOut = $mycms->sql_select($packageCheckoutDate);
					//echo '<pre>'; print_r($resCheckOut);
					foreach ($resCheckOut as $key => $rowCheckOut) {
						$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
						$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
						$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
						$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
						$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

						$dCount++;
					}
				}

				//echo '<pre>'; print_r($row_package);
				//die();
				if ($row_package) {

					$i = 1;
					$checked = '';
					$flag = 0;
					foreach ($row_package as $key => $value) {


						$sql_package_price  =   array();
						$sql_package_price['QUERY'] =   "SELECT * 
																				FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
																				WHERE `status`  = ?
																				AND `hotel_id`  = ?
																				AND `package_id`  = ?
																				 AND `tariff_cutoff_id`  = ?
																				 AND `inr_amount`  >0
																				ORDER BY `id` ASC";

						$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');

						$row_package_price = $mycms->sql_select($sql_package_price);

						if ($i == 1) {
							$checked = 'checked';
						} else {
							$checked = '';
						}


						if ($row_package_price[0]['inr_amount'] > 0) {
							$flag = 1;
							$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

							$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];
						}



						$i++;
					}

					if ($flag == 1) {


						$msg .= '<div class="vanue-date"><input type="hidden" name="hotel_id" value="' . $_REQUEST['hotel_id'] . '" >
															 ';
						/*$msg.='<h6>Check In</h6>';
														
														$tempCheckIN = array_unique(array_column($dates, 'CHECKIN','CHECKINID'));
	
														
														foreach ($tempCheckIN as $key => $value) {
	
																$day = date('D',strtotime($value));
																$date_in = date('d',strtotime($value));
	
																$checkInVal = $key."/".$value;
																 //$msg.='<option value="'.$checkInVal.'">'.$value.'</option>';
																 $msg.='<label class="dates" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500"><input type="radio" value='.$checkInVal.' checkIn='.$checkInVal.' operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id"  onClick="get_checkin_val(this.value)"> <span class="dates-select">'.$day.'<br>'.$date_in.'</span> </label>';
															 } 
	
														 
	
	
														$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT','CHECKOUTID'));
														$msg.='<h6>Check Out</h6>'; 	
														foreach ($tempCheckOUT as $key => $value) {
																$checkOutVal = $key."/".$value;
																$day = date('D',strtotime($value));
																$date_in = date('d',strtotime($value));
																 //$msg.='<option value="'.$checkOutVal.'">'.$value.'</option>';
																 $msg.='<label class="dates" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500"><input type="radio" value='.$checkOutVal.' checkOut='.$checkOutVal.' operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id"   accommodation="checkout" icon="images/cat-ic-6.png" > <span class="dates-select">'.$day.'<br>'.$date_in.'</span> </label>';
															 } */

						$msg .= '<select operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id" id="accomodation_package_checkin_id" style="color:#FFFFFF !important; height: 38px !important;" onchange="get_checkin_val(this.value)">
																 <option value="">Select Check In Date</option>
																 ';

						$tempCheckIN = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));




						foreach ($tempCheckIN as $key => $value) {


							$checkInVal = $key . "/" . $value;
							$msg .= '<option value="' . $checkInVal . '">' . $value . '</option>';
						}

						$msg .= '</select>';

						$msg .= '<select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id" id="accomodation_package_checkout_id" style="color:#ffffff !important; height: 38px !important;margin-inline: 3px;" onchange="get_checkout_val(this.value)" accommodation="checkout" reg="accommodation">
																 <option value="">Select Check Out Date</option>
																 ';

						$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));
						foreach ($tempCheckOUT as $key => $value) {
							$checkOutVal = $key . "/" . $value;
							$msg .= '<option value="' . $checkOutVal . '">' . $value . '</option>';
						}

						$msg .= '</select>';

						$msg .= '<select name="accommodation_room" id="accommodation_room">
																	<option value="" selected>Select Room</option>';

						for ($i = 1; $i <= $totalCount; $i++) {

							if ($i == 1) {
								$selected = 'selected';
							} else {
								$selected = '';
							}
							$msg .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
						}

						$msg .= '</select>';


						$msg .= '</div>';

						$msg .= '<div class="col-lg-8 custom-radio-holder vf-form aos-init aos-animate" data-aos="fade-right" data-aos-duration="500"><label class="custom-radio ps-0">';
						$i = 1;
						$checked = '';

						foreach ($row_package as $key => $value) {


							$sql_package_price  =   array();
							$sql_package_price['QUERY'] =   "SELECT * FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
																			WHERE `status`  = ?
																			AND `hotel_id`  = ?
																			AND `package_id`  = ?
																				AND `tariff_cutoff_id`  = ?
																				AND `inr_amount`  >0
																			ORDER BY `id` ASC";

							$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
							$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
							$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');
							$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');

							$row_package_price = $mycms->sql_select($sql_package_price);

							if ($i == 1) {
								$checked = 'checked';
							} else {
								$checked = '';
							}


							if ($row_package_price[0]['inr_amount'] > 0) {

								$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

								$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];

								$sql1['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
									WHERE `id`='5' AND `purpose`='Registration' AND status IN ('A', 'I')";
								$result 	 = $mycms->sql_select($sql1);
								$accomodationIcon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];

								$msg .= '<label class="custom-radio">
												<input type="radio" name="package_id" id="package_id" value="' . $value['id'] . '" ' . $checked . '  currency="' . $row_package_price[0]['currency'] . '"
													amount="' . $row_package_price[0]['inr_amount'] . '" onchange="getPackageVal(this.value)"  invoiceTitle="' . $invoiceTitle . '" package="accomodation" icon="' . $accomodationIcon . '">
												<i>' . $packageLebe . '</i><span class="checkmark"></span>
											</label>';
							}



							$i++;
						}



						$msg .= '</div></div>
															 <div class="bottom-fx">
							<div class="total-price">
																  <h6>Total</h6>
																 
																   <strong>&#8377; <span id="subTotalPrc">0.00</span></strong>
																	</div>
																   <div class="next-prev-btn-box">
							  <a  class="btn next-btn prev" sec="6">Prev</a>
							  <a href="javascript:void(0);" class="btn next-btn pay-now" id="paynowbtn" >Pay Now</a>
							</div>
																  
															  </div>
															</div>
														  </div>';

						// $msg .= ' <div class="category-table-row pr-5">

						// 				                <a href="javascript:void(0);" class="btn btn-w ml-10 pay-now" id="paynowbtn">Pay Now</a>
						// 								<a class="btn btn-w next-btn ml-10 prev" sec="6"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z"/></svg></a>
						// 				              </div>';
					} else {
						$msg .= "<span style='color:#eb4d4d;'>Please set package price from backend!</span>";
					}
				}

				echo $msg;
			} else {
				echo 'No seat limit';
			}
		} else {

			echo $msg;
		}

		exit();
		break;

	case 'getAccommodationDetailsProfile':

		include_once("../../includes/function.registration.php");
		include_once("../../includes/function.accommodation.php");

		$hotel_id  = trim($_REQUEST['hotel_id']);
		$currentCutoffId = getTariffCutoffId();


		if (!empty($hotel_id)) {

			//========= Fetch Hotel Accessories Start ==========//
			$delegateId = $_REQUEST['abstractDelegateId'];
			$getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);
			$getExistingAccommodationList = getTotalAccommodationWithoutCombo($delegateId);
			$totalCount = 3 - $getAccommodationMaxRoom;
			//=========Exist Accommodation Start==============//
			$checkinIdArray = array();
			$checkoutIdArray = array();
			$packageArray = array();
			if (count($getExistingAccommodationList) > 0) {

				foreach ($getExistingAccommodationList as $key => $value) {
					array_push($packageArray, $value['package_id']);
					//echo 'hoteelID='.$value['hotel_id'];
					//echo 'checkin_date='.$value['checkin_date'];
					//echo 'checkout_date='.$value['checkout_date'];

					$diff = strtotime($value['checkout_date']) - strtotime($value['checkin_date']);
					$days = abs(round($diff / 86400));

					if ($days > 1) {
						//echo 'hotelID='. $value['hotel_id'];
						//echo 'checkin_date='. $value['checkin_date'];
						//echo 'checkout_date='. $value['checkout_date'];
						$getAllCheckInId = getVariableCheckInId($value['hotel_id'], $value['checkin_date'], $value['checkout_date']);
						//print_r($getAllCheckInId);
						$getAllCheckOutId = getVariableCheckOutId($value['hotel_id'], $value['checkin_date'], $value['checkout_date']);
					} else {
						$getAllCheckInId = getAllCheckInId($value['hotel_id'], $value['checkin_date']);
						$getAllCheckOutId = getAllCheckOutId($value['hotel_id'], $value['checkout_date']);
					}

					foreach ($getAllCheckInId as $key => $value) {
						array_push($checkinIdArray, $value['id']);
					}

					foreach ($getAllCheckOutId as $key => $value) {

						array_push($checkoutIdArray, $value['id']);
					}
				}
			}
			//=========Exist Accommodation End==============//
			$msg = '';
			$sqlAcc = array();
			$sqlAcc['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $_REQUEST['hotel_id'] . "' AND status='A'  AND purpose='aminity'  ";

			$queryAcc = $mycms->sql_select($sqlAcc, false);

			//========= Fetch Hotel Accessories End ==========//

			$sql_Count_hotel  =   array();
			$sql_Count_hotel['QUERY'] =   "SELECT COUNT(*) AS countdata 
			                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . "
			                                            WHERE `status`  = ?
			                                            AND `hotel_id`  = ?
			                                            ORDER BY `id` ASC";

			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_Count_hotel['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_Count_hotel = $mycms->sql_select($sql_Count_hotel);
			$countSeatLimit = $row_Count_hotel[0]['countdata'];


			$sql_package  =   array();
			$sql_package['QUERY'] =   "SELECT * 
		                                            FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
		                                            WHERE `status`  = ?
		                                            AND `hotel_id`  = ?
		                                            ORDER BY `id` ASC";

			$sql_package['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_package['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_package = $mycms->sql_select($sql_package);
			// print_r($sql_package); die;

			$sql_hotel  =   array();
			$sql_hotel['QUERY'] =   "SELECT * 
		                                            FROM " . _DB_MASTER_HOTEL_ . "
		                                            WHERE `status`  = ?
		                                            AND `id`  = ?
		                                            ORDER BY `id` ASC";

			$sql_hotel['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
			$sql_hotel['PARAM'][]   =   array('FILD' => 'id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');

			$row_hotel = $mycms->sql_select($sql_hotel);

			//print_r($row_hotel[0]['hotel_name']);
			//die();
			$hotel_seat_limit = $row_hotel[0]['seat_limit'];
			//hotel_image -> hotel_background_image
			$hotel_image = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row_hotel[0]['hotel_background_image'];


			$msg .=  '
			<div class="vanue-image aos-init aos-animate" ><img
		                  src="' . $hotel_image . '" alt=""/></div>
						  <div class="vanue-form">
						  <div class="pxl-item--content">
                      <div class="pxl-item--popular pxl-flex " >
                        <span class="accm-rating">
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                        </span>
                      </div>
                      <h3 class="pxl-item--title">' . $row_hotel[0]['hotel_name'] . '</h3>
                      <div class="pxl-item--desc"><i class="far fa-map-marker-alt"></i> ' . $row_hotel[0]['hotel_address'] . '</div>
                      <div class="pxl-item--desc"><i class="fal fa-utensils"></i> Breakfast Included</div>
                    </div>
		              <div class="vanue-details">
		                  <div class="row">
		                  <input type="hidden" name="hotel_select_acco_id" value="' . $hotel_id . '">
		                  ';

			if ($queryAcc) {
				$i = 1;
				foreach ($queryAcc as $k => $row) {

					$icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];

					$msg .= '<div class="col-lg-2" data-aos="zoom-in" data-aos-delay="100"
		                                data-aos-duration="500">
		                                <img src="' . $icon . '" />
		                                <p>' . $row['accessories_name'] . '</p>
		                              </div>';
				}
			}


			$msg .= '</div>
		                   </div>';

			$presentSeatLimit = $hotel_seat_limit - $countSeatLimit;
			if ($presentSeatLimit > 0 || true) {

				$dates = array();
				$dCount = 0;
				$packageCheckDate = array();
				$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
																		   WHERE `hotel_id` = ?
																			 AND `status` = ?
																	    ORDER BY  check_in_date";
				$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	'TYP' => 's');
				$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
				$resCheckIns = $mycms->sql_select($packageCheckDate);
				$check_in_array = array();
				foreach ($resCheckIns as $key => $rowCheckIn) {
					$packageCheckoutDate = array();
					$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
																			   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
																			  WHERE `hotel_id` = ?
																			    AND `status` = ?
																			    AND `check_out_date` > ?
																		   ORDER BY check_out_date";
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $_REQUEST['hotel_id'], 	    'TYP' => 's');
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
					$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


					$resCheckOut = $mycms->sql_select($packageCheckoutDate);
					//echo '<pre>'; print_r($resCheckOut);
					foreach ($resCheckOut as $key => $rowCheckOut) {
						$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
						$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
						$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
						$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
						$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

						$dCount++;
					}
				}

				//echo '<pre>'; print_r($row_package);
				//die();
				if ($row_package) {

					$i = 1;
					$checked = '';
					$flag = 0;
					foreach ($row_package as $key => $value) {


						$sql_package_price  =   array();
						$sql_package_price['QUERY'] =   "SELECT * 
								                                            FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
								                                            WHERE `status`  = ?
								                                            AND `hotel_id`  = ?
								                                            AND `package_id`  = ?
								                                             AND `tariff_cutoff_id`  = ?
								                                             AND `inr_amount`  >0
								                                            ORDER BY `id` ASC";

						$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');
						$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');

						$row_package_price = $mycms->sql_select($sql_package_price);

						if ($i == 1) {
							$checked = 'checked';
						} else {
							$checked = '';
						}


						if ($row_package_price[0]['inr_amount'] > 0) {
							$flag = 1;
							$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

							$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];
						}



						$i++;
					}

					if ($flag == 1) {


						$msg .= '<div class="vanue-date">
								                	 	';


						$msg .= '<select operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id" id="accomodation_package_checkin_id" style="color:#FFFFFF !important; height: 38px !important;" onchange="get_checkin_val(this.value)">
									                	 	<option value="">Select Check In Date</option>
									                	 	';

						$tempCheckIN = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));




						foreach ($tempCheckIN as $key => $value) {


							$checkInVal = $key . "/" . $value;
							if (count($checkinIdArray) > 0) {
								if (in_array($key, $checkinIdArray)) {
									$checkinCheck = 'checked="checked"';
									$checkinDisabled = 'disabled="disabled"';
								} else {
									$checkinCheck = '';
									$checkinDisabled = '';
								}
							}
							$msg .= '<option value="' . $checkInVal . '" ' . $checkinCheck . ' ' . $checkinDisabled . '>' . $value . '</option>';
						}

						$msg .= '</select>';

						$msg .= '<select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id" id="accomodation_package_checkout_id" style="color:#ffffff !important; height: 38px !important;margin-inline: 3px;" onchange="get_checkout_val(this.value)" accommodation="checkout">
									                	 	<option value="">Select Check Out Date</option>
									                	 	';

						$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));
						foreach ($tempCheckOUT as $key => $value) {

							if (count($checkoutIdArray) > 0) {
								if (in_array($key, $checkoutIdArray)) {
									$checkinCheck = 'checked="checked"';
									$checkinDisabled = 'disabled="disabled"';
								} else {
									$checkinCheck = '';
									$checkinDisabled = '';
								}
							}
							$checkOutVal = $key . "/" . $value;
							$msg .= '<option value="' . $checkOutVal . '" ' . $checkinCheck . ' ' . $checkinDisabled . '>' . $value . '</option>';
						}

						$msg .= '</select>';

						$msg .= '<select name="accommodation_room" id="accommodation_room">
										                        <option value="" selected>Select Room</option>';

						for ($i = 1; $i <= $totalCount; $i++) {;

							if ($i == 1) {
								$selected = 'selected';
							} else {
								$selected = '';
							}
							$msg .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
						}

						$msg .= '</select>';


						$msg .= '</div>';



						$msg .= '<div class="vanue-form">
										                <div class="row align-items-center">
										                  <div class="col-lg-8 vf-form custom-radio-holder" data-aos="fade-right" data-aos-duration="500">';



						$i = 1;
						$checked = '';

						foreach ($row_package as $key => $value) {


							$sql_package_price  =   array();
							$sql_package_price['QUERY'] =   "SELECT * 
											                                            FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . "
											                                            WHERE `status`  = ?
											                                            AND `hotel_id`  = ?
											                                            AND `package_id`  = ?
											                                             AND `tariff_cutoff_id`  = ?
											                                             AND `inr_amount`  >0
											                                            ORDER BY `id` ASC";

							$sql_package_price['PARAM'][]   =   array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');
							$sql_package_price['PARAM'][]   =   array('FILD' => 'hotel_id',     'DATA' => $_REQUEST['hotel_id'],         'TYP' => 's');
							$sql_package_price['PARAM'][]   =   array('FILD' => 'package_id',     'DATA' => $value['id'],         'TYP' => 's');
							$sql_package_price['PARAM'][]   =   array('FILD' => 'tariff_cutoff_id',     'DATA' => $currentCutoffId,         'TYP' => 's');

							$row_package_price = $mycms->sql_select($sql_package_price);

							if ($i == 1) {
								$checked = 'checked';
							} else {
								$checked = '';
							}


							if ($row_package_price[0]['inr_amount'] > 0) {

								$packageLebe = "<b>" . $value['package_name'] . "</b>: " . $row_package_price[0]['currency'] . " " . $row_package_price[0]['inr_amount'];

								$invoiceTitle = "Residential package " . $value['package_name'] . "@" . $row_hotel[0]['hotel_name'];

								$sql_icon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
																WHERE `id`='5' AND `purpose`='Registration' AND status IN ('A', 'I')";
								$result 	 = $mycms->sql_select($sql_icon);
								$accomodationIcon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];

								$msg .= '<label class="custom-radio">
										                       <input type="radio" name="package_id" id="package_id" value="' . $value['id'] . '" ' . $checked . '  currency="' . $row_package_price[0]['currency'] . '"
										                         amount="' . $row_package_price[0]['inr_amount'] . '" onchange="getPackageVal(this.value)"  invoiceTitle="' . $invoiceTitle . '" package="accomodation" icon="' . $accomodationIcon . '">
										                        <i>' . $packageLebe . '</i><span class="checkmark"></span>
										                      </label>';
							}



							$i++;
						}

						$msg .= '</div>
										                  <div class="col-lg-4" data-aos="fade-left" data-aos-duration="500">
										                    <div class="price-box">
										                      <h6>Total</h6>
										                      <div class="price-inner">
										                        <span class="price" id="subTotalPrc">0.00</span>
										                        INR
										                      </div>
										                    </div>
										                  </div>
										                </div>
										              </div>';

						$msg .= ' <div class="category-table-row pr-5">
													   
										                 <a  class="btn btn-w next" formPay = "frmAddAccommodationfromProfile">Pay Now</a>
														<a href="' . _BASE_URL_ . 'profile-add.php?section=6" class="btn btn-w next-btn ml-10 prev" sec="6"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z"/></svg></a>
										              </div>';
					} else {
						$msg .= "<span style='color:#eb4d4d;'>Please set package price from backend!</span>";
					}
				}

				echo $msg;
			} else {
				echo 'error';
			}
		} else {

			echo $msg;
		}

		exit();
		break;
}
