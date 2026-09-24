<?php
include_once('includes/frontend.init.php');
include_once("includes/function.frontend.registration.php");
include_once("includes/function.edit.php");
include_once("includes/function.registration.php");
include_once('includes/function.delegate.php');
include_once('includes/function.invoice.php');
include_once('includes/function.accompany.php');
include_once('includes/function.workshop.php');
include_once('includes/function.registration.php');
include_once('includes/function.messaging.php');
include_once('includes/function.accommodation.php');
include_once("includes/function.dinner.php");
include_once('webmaster/engine/class.common.extended.php');
//include_once('includes/function.delegate.php');
//include_once('includes/function.invoice.php');
//include_once('includes/function.accompany.php');
//include_once('includes/function.workshop.php');
//include_once('includes/function.registration.php');
//include_once('includes/function.messaging.php');
//include_once('includes/function.accommodation.php');
//include_once("includes/function.dinner.php");
//include_once('includes/function.delegate.php');
//include_once('includes/function.invoice.php');
//include_once('includes/function.accompany.php');
//include_once('includes/function.workshop.php');
//include_once('includes/function.registration.php');
//include_once('includes/function.messaging.php');


if (isset($_FILES['file']) && $_POST['id'] != "") {
	$id = $_POST['id'];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_USER_REGISTRATION_ . "
													WHERE `id` = '" . $id . "'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	// echo "<pre>"; print_r($fetchData);
	$imgpath = _BASE_URL_ . $cfg['USER.PROFILE.IMAGE'] . $fetchData[0]['user_image'];
	unlink($imgpath);


	$tempFile = $_FILES['file']['tmp_name'];
	$fileName = $_POST['dynamic_name_prefix'] . "_" . $_FILES['file']['name'];
	$uploadDir = $cfg['USER.PROFILE.IMAGE'];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "UPDATE " . _DB_USER_REGISTRATION_ . "SET
													`user_image`='" . $fileName . "'
													WHERE `id` = '" . $id . "'";
	$mycms->sql_update($sqlUserImage, false);

	if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
		echo "1";
	} else {
		echo "Error: Unable to move file to destination directory.";
	}
}



$act = $_REQUEST['act'];
//echo "<pre>";print_r($_REQUEST);echo "</pre>";die;
switch ($act) {
	case 'combinedRegistrationProcess':

		combinedRegistrationProcess();
		break;
    case 'combinedEditRegistrationProcess':

		combinedEditRegistrationProcess();
		break;
	case 'step6':
		step6();
		break;

	case 'onlyWorkshopReg':
		onlyWorkshopReg();
		exit();
		break;

	case 'registerAbstractUser':
		registerAbstractUser();
		exit();
		break;


	case 'paymentSet':
		//print_r($_REQUEST['delegate_id']);die;
		paymentSet();
		break;

	case 'setPaymentTerms':
		setPaymentTerms();
		break;


	case 'addMoreDelegate':
		$mycms->setSession('CURRENT_REG_USER', 'ADDON');

		$USER_DETAILS_FRONT = $mycms->getSession('USER_DETAILS_FRONT');
		$mycms->setSession('CURRENCY', getCurrency($USER_DETAILS_FRONT['CATAGORY']));
		detailsInseringProcess($mycms->getSession('PROCESS_FLOW_ID_FRONT'));

		$ADD_MORE_COUNTER = $mycms->getSession('ADD_MORE_COUNTER');
		if ($ADD_MORE_COUNTER == '') {
			$ADD_MORE_COUNTER = 0;
		}
		$mycms->setSession('ADD_MORE_COUNTER', intval($ADD_MORE_COUNTER) + 1);

		$sqlProcessUpdateStep['QUERY']           = " UPDATE  " . _DB_PROCESS_STEP_ . "
												   SET `regitration_status` = 'COMPLETE'
												 WHERE `id` = '" . $mycms->getSession('PROCESS_FLOW_ID_FRONT') . "'";
		$mycms->sql_update($sqlProcessUpdateStep, false);

		// DETAILS INSERTING PROCESS
		$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
		$mycms->redirect("registration.tariff.php?" . $mycms->encoded('addMore'));

		exit();
		break;

	case 'gotoAddMoreDelegate':
		$mycms->setSession('CURRENT_REG_USER', 'ADDON');
		$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
		$mycms->redirect("registration.tariff.php?" . $mycms->encoded('addMore'));
		exit();
		break;

	case 'updateRegistration':
		include_once("includes/function.registration.php");
		include_once("includes/function.workshop.php");
		include_once("includes/function.delegate.php");
		include_once("includes/function.accommodation.php");
		include_once("includes/function.dinner.php");
		include_once("includes/function.invoice.php");
		include_once("includes/function.accompany.php");
		$delegateId												  = $_REQUEST['delegateId'];
		$invoiceId												  = $_REQUEST['invoiceId'];
		$slipId													  = $_REQUEST['slipId'];
		$userDetailsArray['id']			                          = addslashes(trim(strtoupper($_REQUEST['delegateId'])));
		$userDetailsArray['registration_classification_id']		  = $_REQUEST['registration_classification_id'][0];
		$invoiceId												  = addslashes(trim($_REQUEST['invoiceId']));
		$registration_classification_id							  = $_REQUEST['registration_classification_id'][0];
		$accompanyClasfId = 2;

		updatingUserDetails($userDetailsArray);
		//CONFERENCE UPDATE
		$invoiceIdConf = updateInvoiceDetails($invoiceId, $userDetailsArray['id'], 'CONFERENCE');
		$sqlUpdate  = array();
		$sqlUpdate['QUERY']      = " UPDATE " . _DB_INVOICE_ . "
										  SET payment_status = ?
										WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',  'DATA' => 'UNPAID',    'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',              'DATA' => $invoiceId,    'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		$sqlUpdateSlip = array();
		$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
											   SET `registration_payment_status` = ?
											 WHERE `id` = ?";
		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'registration_payment_status',  'DATA' => 'UNPAID',    'TYP' => 's');
		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'id',                           'DATA' => $delegateId,    'TYP' => 's');

		$mycms->sql_update($sqlUpdateSlip, false);

		$sqlUpdateSlip    = array();
		$sqlUpdateSlip['QUERY']	  = "UPDATE " . _DB_SLIP_ . "  
											SET `currency` = ? 
											WHERE `delegate_id` = ?
											AND `status` = ?";

		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'currency',    'DATA' => getRegistrationCurrency($userDetailsArray['registration_classification_id']),  'TYP' => 's');
		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId,                                                                   'TYP' => 's');
		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'A',                                                                           'TYP' => 's');

		$mycms->sql_update($sqlUpdateSlip, false);

		$details		  = getUserDetails($userDetailsArray['id']);

		$accDetails  	  = getUserTypeAndRoomType($userDetailsArray['registration_classification_id']);
		$delegateId	 	  = $userDetailsArray['id'];
		$cutoffId    	  = $details['registration_tariff_cutoff_id'];
		$workshopCutoffId = $details['workshop_tariff_cutoff_id'];

		// ALL INVOICE WILL BE D
		$sqlUpdateInv2    = array();
		$sqlUpdateInv2['QUERY']	  = "UPDATE " . _DB_INVOICE_ . "  
										   SET `status` = ?
										 WHERE `delegate_id` = ? 
										   AND `service_type`=?";

		$sqlUpdateInv2['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdateInv2['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId,                           'TYP' => 's');
		$sqlUpdateInv2['PARAM'][]   = array('FILD' => 'service_type', 'DATA' => 'ACCOMPANY_CONFERENCE_REGISTRATION',   'TYP' => 's');

		$mycms->sql_update($sqlUpdateInv2, false);

		$sqlUpdateInv3    = array();
		$sqlUpdateInv3['QUERY']	  = "UPDATE " . _DB_INVOICE_ . "  
										   SET `status` = ? 
										 WHERE `delegate_id` = ? 
										   AND `service_type`=?";

		$sqlUpdateInv3['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdateInv3['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId,                           'TYP' => 's');
		$sqlUpdateInv3['PARAM'][]   = array('FILD' => 'service_type', 'DATA' => 'DELEGATE_DINNER_REQUEST',             'TYP' => 's');

		$mycms->sql_update($sqlUpdateInv3, false);

		$sqlUpdateInv4    = array();
		$sqlUpdateInv4['QUERY']	  = "UPDATE " . _DB_INVOICE_ . "  
										   SET `status` = ? 
										 WHERE `delegate_id` = ? 
										   AND `service_type`=?";

		$sqlUpdateInv4['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdateInv4['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId,                           'TYP' => 's');
		$sqlUpdateInv4['PARAM'][]   = array('FILD' => 'service_type', 'DATA' => 'DELEGATE_WORKSHOP_REGISTRATION',      'TYP' => 's');

		$mycms->sql_update($sqlUpdateInv4, false);


		$sqlUpdate3      = array();
		$sqlUpdate3['QUERY']      = "UPDATE " . _DB_USER_REGISTRATION_ . "  
											SET `status` = ? 
											WHERE `refference_delegate_id` = ?";

		$sqlUpdate3['PARAM'][]   = array('FILD' => 'status',                 'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdate3['PARAM'][]   = array('FILD' => 'refference_delegate_id', 'DATA' => $delegateId,                           'TYP' => 's');

		$mycms->sql_update($sqlUpdate3, false);

		$sqlUpdateDinner  = array();
		$sqlUpdateDinner['QUERY']  = "UPDATE " . _DB_REQUEST_DINNER_ . "  
											SET `status` =? 
											WHERE `delegate_id` = ?";

		$sqlUpdateDinner['PARAM'][]   = array('FILD' => 'status',                 'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdateDinner['PARAM'][]   = array('FILD' => 'delegate_id',            'DATA' => $delegateId,                           'TYP' => 's');

		$sqlUpdateAccomodation  = array();
		$sqlUpdateAccomodation['QUERY']  = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "  
											SET `status` =? 
											WHERE `user_id` = ?";

		$sqlUpdateAccomodation['PARAM'][]   = array('FILD' => 'status',                 'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdateAccomodation['PARAM'][]   = array('FILD' => 'user_id',            	'DATA' => $delegateId,                           'TYP' => 's');

		$mycms->sql_update($sqlUpdateAccomodation, false);

		$DinnerDetails = $_REQUEST['dinner_value'];

		if ($DinnerDetails) {
			foreach ($DinnerDetails as $key => $val) {

				$dinnerDetailsArray2[$val]['refference_id']         = $delegateId;
				$dinnerDetailsArray2[$val]['delegate_id']           = $delegateId;
				$dinnerDetailsArray2[$val]['package_id']            = $val;
				$dinnerDetailsArray2[$val]['tariff_cutoff_id']      = $cutoffId;
				$dinnerDetailsArray2[$val]['booking_quantity']      = 1;
				$dinnerDetailsArray2[$val]['booking_mode']          = $details['registration_mode'];
				$dinnerDetailsArray2[$val]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray2[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray2[$val]['payment_status']        = 'UNPAID';
			}

			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray2);

			foreach ($dinerReqId as $key => $reqId) {
				insertingInvoiceDetails($reqId, 'DINNER', 'GENERAL', $date);
			}
		}

		$accompanyDetails = $_REQUEST['accompany_selected_add'];

		if ($accompanyDetails) {
			//echo'<pre>';print_r($_REQUEST);die();
			foreach ($accompanyDetails as $key => $val) {
				if (trim($_REQUEST['accompany_name_add'][$val]) != '') {
					$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
					$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($_REQUEST['accompany_name_add'][$val])));
					$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($_REQUEST['accompany_age_add'][$val])));
					$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($_REQUEST['accompany_person_edit_food_preference'][$val])));
					$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($_REQUEST['accompany_food_details_add'][$val])));
					$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));

					$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
					$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
					$accompanyDetailsArray[$val]['registration_classification_id']		 = $accompanyClasfId;
					$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
					$accompanyDetailsArray[$val]['registration_request']       		 	 = $_REQUEST['registration_request'];
					$accompanyDetailsArray[$val]['operational_area']   	    		 	 = $_REQUEST['registration_request'];
					$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
					$accompanyDetailsArray[$val]['registration_mode']					 = $details['registration_mode'];
					$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
					$accompanyDetailsArray[$val]['reg_type']              				 = 'FRONT';
				}
			}

			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray);

			$accDinnerDetails = $_REQUEST['accompany_dinner_add'];
			if ($accDinnerDetails) {

				foreach ($accDinnerDetails as $key => $val) {
					$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId[$key];
					$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					$dinnerDetailsArray1[$key]['package_id']            = $val;
					$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					$dinnerDetailsArray1[$key]['booking_mode']          = $details['registration_mode'];
					$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray1[$key]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
				}

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);

				foreach ($dinerReqId as $key => $reqId) {

					insertingInvoiceDetails($reqId, 'DINNER', 'GENERAL', $date);
				}
			}

			foreach ($accompanyReqId as $key => $reqId) {
				insertingInvoiceDetails($reqId, 'ACCOMPANY');
			}
		}

		$sqlUpdateWorkshop = array();
		$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "  SET 
											`status` = ? 
											WHERE `delegate_id` = ?";

		$sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'status',                 'DATA' => 'D',                                   'TYP' => 's');
		$sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'delegate_id',            'DATA' => $delegateId,                           'TYP' => 's');

		$mycms->sql_update($sqlUpdateWorkshop, false);

		$workshopDetails = $_REQUEST['workshop_id'];

		if ($workshopDetails) {
			foreach ($workshopDetails as $key => $workshopId) {
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= $workshopId;
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $workshopCutoffId;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getWorkshopTariffId($workshopId, $workshopCutoffId, $registration_classification_id);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $registration_classification_id;
				$workshopDetailArray[$workshopId]['booking_mode']        			= $details['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type']       		= $_REQUEST['registration_request'];
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= 'UNPAID';
			}
			$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);

			foreach ($workshopReqId as $key => $reqId) {
				$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP');

				if ($registration_classification_id == 11) // Free Workshop for PGT
				{
					$invoiceId = zeroValueInvoiceUpdate($invoiceIdWrkshp, 'WORKSHOP', $mycms->getSession('SLIP_ID')); //UPDATING COMPLIMENTARY INVOICE
				}
			}
		}

		if ($_REQUEST['accommodation_hotel_id'] != "" && $_REQUEST['check_in_date'] != "" && $_REQUEST['check_out_date'] != "") {
			$sqlUpdate1 = array();
			$sqlUpdate1['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "  
											SET `status` = 'D' WHERE `user_id` = '" . $delegateId . "'";
			$mycms->sql_update($sqlUpdate1, false);

			$sqlUpdateInv4 = array();
			$sqlUpdateInv4['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "  
										   SET `status` = 'D' 
										 WHERE `delegate_id` = '" . $delegateId . "' 
										   AND `service_type`='DELEGATE_ACCOMMODATION_REQUEST'";

			$mycms->sql_update($sqlUpdateInv4, false);

			$check_in_date_id                = $_REQUEST['check_in_date'];
			$check_out_date_id               = $_REQUEST['check_out_date'];
			$accommodation_hotel_id          = $_REQUEST['accommodation_hotel_id'];

			$sqlAccommodationDate	= array();
			$sqlAccommodationDate['QUERY']        = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
														   WHERE `id` = '" . $check_in_date_id . "'";

			$resultAccommodationDate         = $mycms->sql_select($sqlAccommodationDate);
			$rowAccommodationDate            = $resultAccommodationDate[0];

			$check_in_date                   = $rowAccommodationDate['check_in_date'];

			// GET ACCOMMODATION OUT DATE
			$sqlAccommodationOutDate  = array();
			$sqlAccommodationOutDate['QUERY']       = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
														   WHERE `id` = '" . $check_out_date_id . "'";

			$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate);
			$rowAccommodationOutDate           = $resultAccommodationOutDate[0];

			$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];

			$sqlFetchHotel = array();
			$sqlFetchHotel['QUERY']   = "SELECT id 
									   FROM " . _DB_ACCOMMODATION_PACKAGE_ . "  
									  WHERE  `hotel_id` = '" . $accommodation_hotel_id . "'
										  AND `status` = 'A'";

			$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
			$resultfetch 	  = $resultFetchHotel[0];
			$packageId 	      = $resultfetch['id'];


			$totalRoom = 0;
			$totalGuestCounter                 = 0;
			foreach ($_REQUEST['room_guest_counter'] as $key => $resDetails) {
				$totalRoom++;
				if ($resDetails != "") {

					$totalGuestCounter        += $resDetails;
				}
			}
			$accTariffId = getAccommodationTariffId($packageId, $check_in_date_id, $check_out_date_id, $cutoffId);
			$accomodationDetails['user_id']											 = $delegateId;
			$accomodationDetails['accompany_name']									 = $_REQUEST['accmName'];
			$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
			$accomodationDetails['guest_counter']									 = $_REQUEST['room_guest_counter'][0];
			$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
			$accomodationDetails['package_id']										 = $packageId;
			$accomodationDetails['tariff_ref_id']								     = $accTariffId;
			$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
			$accomodationDetails['checkin_date']									 = $check_in_date;
			$accomodationDetails['checkout_date']									 = $check_out_date;
			$accomodationDetails['booking_quantity']								 = 1;
			$accomodationDetails['refference_invoice_id']							 = 0;
			$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
			$accomodationDetails['booking_mode']									 = $details['registration_mode'];
			$accomodationDetails['payment_status']									 = 'UNPAID';

			$accompReqId	 = insertingAccomodationDetails($accomodationDetails);

			insertingInvoiceDetails($accompReqId, 'ACCOMMODATION');
		}

		if ($accDetails['TYPE'] == 'COMBO') {
			$dinnerComboDetails  = array(2);
			foreach ($dinnerComboDetails as $key => $val) {
				$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
				$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
				$dinnerDetailsArray[$val]['package_id']            = $val;
				$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
				$dinnerDetailsArray[$val]['booking_quantity']      = 1;
				$dinnerDetailsArray[$val]['booking_mode']          = $details['registration_mode'];
				$dinnerDetailsArray[$val]['refference_invoice_id'] = $invoiceIdConf; // Need To Edit
				$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray[$val]['payment_status']        = 'ZERO_VALUE';
			}

			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);

			if ($details['registration_classification_id'] != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
				$accmodationPackageId 	= $_REQUEST['accomPackId'];
				$accmodationDateSet 	= $_REQUEST['accDate'][$accmodationPackageId];
				$hotel_id				= $_REQUEST['hotel_id'];

				$accDates = explode('-', $accmodationDateSet);
				$accCheckinDateId = $accDates[0];
				$accCheckOutDateId = $accDates[1];

				$accomodationDetails['user_id']											 = $delegateId;
				$accomodationDetails['hotel_id']										 = $hotel_id;
				$accomodationDetails['package_id']										 = $accmodationPackageId;
				$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
				$accomodationDetails['checkin_date']									 = getCheckInDateById($accCheckinDateId, 1);
				$accomodationDetails['checkout_date']									 = getCheckOutDateById($accCheckOutDateId, 1);
				$accomodationDetails['booking_quantity']								 = 1;
				$accomodationDetails['type']								 			 = "COMBO";
				$accomodationDetails['refference_invoice_id']							 = $invoiceIdConf;
				$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
				$accomodationDetails['booking_mode']									 = $details['registration_mode'];

				$accomodationDetails['preffered_accommpany_name']						 = $_REQUEST['preffered_accommpany_name'];
				$accomodationDetails['preffered_accommpany_email']						 = $_REQUEST['preffered_accommpany_email'];
				$accomodationDetails['preffered_accommpany_mobile']						 = $_REQUEST['preffered_accommpany_mobile'];

				$accomodationDetails['payment_status']									 = 'ZERO_VALUE';
				$accompReqId	 														 = insertingAccomodationDetails($accomodationDetails);
			}
		}
		$totalSlipAmount = invoiceAmountOfSlip($slipId);

		$mycms->redirect("registration." . strtolower($details['registration_mode']) . ".checkout.php");

		exit();
		break;

	case 'updateWorkshop':
		$currentCutoffId = getWorkshopTariffCutoffId();
		$workshopDetailArray['id']      						= $_REQUEST['reqId'];
		$details 	   	 = getWorkshopDetails($workshopDetailArray['id']);
		$workshopDetailArray['workshop_id']      				= $_REQUEST['workshop_id'][0];

		$workshopDetailArray['registration_classification_id']  = getUserClassificationId($details['delegate_id']);
		$workshopDetailArray['workshop_tarrif_id']       		= getWorkshopTariffId($workshopDetailArray['workshop_id'], $currentCutoffId, $workshopDetailArray['registration_classification_id']);
		$invoiceId												= $_REQUEST['invoiceId'];

		updatingWorkShopDetails($workshopDetailArray);
		updateInvoiceDetails($invoiceId, $workshopDetailArray['id'], 'WORKSHOP');
		$details = getUserDetails($details['delegate_id']);
		$mycms->redirect("registration." . strtolower($details['registration_mode']) . ".checkout.php");
		exit();
		break;

	case 'updateAccompanys':
		foreach ($_REQUEST['accompany_selected_edit'] as $key => $val) {
			$sqlUpdate4['QUERY'] = "   UPDATE " . _DB_USER_REGISTRATION_ . " 
									 SET `user_full_name` = '" . $_REQUEST['accompany_name_edit'][$val] . "',
										 `user_age`	 = '" . $_REQUEST['user_age'][$val] . "',
										 `user_food_preference`			  = '" . $_REQUEST['accompany_food_choice'][$val] . "',
									 WHERE `id` = '" . $_REQUEST['reqIds'] . "' AND `status` = 'A'";
			$mycms->sql_update($sqlUpdate4, false);
		}
		$mycms->redirect("registration." . strtolower($details['registration_mode']) . ".checkout.php");
		exit();
		break;

	case 'updateAccompany':

		$sqlUpdate4['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  SET `status` = 'D' WHERE `refference_delegate_id` = '" . $delegateId . "' AND `accompany_relationship` != 'ADD_ON'";
		//$mycms->sql_update($sqlUpdate4,false);

		$accompanyClasfId = $_REQUEST['accompanyClasfId'];
		$delegateId												  = $_REQUEST['delegateId'];
		$invoiceId												  = $_REQUEST['invoiceId'];
		$userDetailsArray['id']			                          = addslashes(trim(strtoupper($_REQUEST['delegateId'])));
		$details	 = getUserDetails($userDetailsArray['id']);

		foreach ($_REQUEST['accompany_selected_add'] as $key => $val) {
			$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
			$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($_REQUEST['accompany_name_add'][$val])));
			$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($_REQUEST['accompany_age_add'][$val])));
			$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($_REQUEST['accompany_food_preference_add_' . $val])));
			$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($_REQUEST['accompany_food_details_add'][$val])));
			$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper($_REQUEST['accompany_relationship_add'][$val])));

			$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
			$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
			$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($accompanyClasfId)));
			$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
			$accompanyDetailsArray[$val]['registration_request']       		 	 = $details['registration_request'];
			$accompanyDetailsArray[$val]['operational_area']   	    		 	 = $details['registration_request'];
			$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
			$accompanyDetailsArray[$val]['registration_mode']					 = $details['registration_mode'];
			$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
			$accompanyDetailsArray[$val]['reg_type']              				 = addslashes(trim(strtoupper($details['reg_type'])));
		}

		if ($accompanyDetailsArray) {
			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray);
		}
		$sqlUpdateInv2['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "  
								   SET `status` = 'D' 
								 WHERE `delegate_id` = '" . $delegateId . "' 
								   AND `service_type`='ACCOMPANY_CONFERENCE_REGISTRATION'";

		//$mycms->sql_update($sqlUpdateInv2,false);

		foreach ($accompanyReqId as $key => $reqId) {
			if ($key == 0) {
				if (
					$userDetailsArray['registration_classification_id'] != 7
					&& $userDetailsArray['registration_classification_id'] != 12
				) {
					insertingInvoiceDetails($reqId, 'ACCOMPANY');
				}
			} else {
				insertingInvoiceDetails($reqId, 'ACCOMPANY');
			}
			//insertingInvoiceDetails($reqId,'ACCOMPANY');
		}

		//$mycms->redirect("registration.".strtolower($details['registration_mode']).".checkout.php");			 
		exit();
		break;

	case 'cancel_invoice_from_profile':
		include_once("includes/function.invoice.php");
		include_once("includes/function.dinner.php");
		include_once("includes/function.accompany.php");
		include_once("includes/function.workshop.php");

		$returntext = cancelInvoiceProcess();
		$mycms->redirect("profile.php");
		exit();
		break;

	case 'cancel_invoice':
		include_once("includes/function.invoice.php");
		include_once("includes/function.dinner.php");
		include_once("includes/function.accompany.php");
		include_once("includes/function.workshop.php");
		cancelInvoiceProcess();
		if (isset($_REQUEST['rtrnurlEnc'])) {
			$retUrl = $mycms->decoded($_REQUEST['rtrnurlEnc']);
		} else {
			$retUrl = $_REQUEST['rtrnurl'];
		}

		$mycms->redirect($retUrl);
		exit();
		break;

	case 'onlinePayment':
		$mycms->redirect("razorpay_payment_do.php?delegate_id=" . $_REQUEST['delegate_id'] . "&slip_id=" . $_REQUEST['slip_id']);
		exit();
		break;

	case 'add_workshop':
		$delegateId     = $mycms->getSession('LOGGED.USER.ID');

	   echo '<pre>'; print_r($_REQUEST); die;

		
		//$workshopIdArr = $_REQUEST['workshop_id'];
		$workshopDetails 	   = $_REQUEST['workshop_id'];
		$clsfId			 	   = $_REQUEST['delegateClasfId'];
		$workshopCutoffId	  = $_REQUEST['cutoff_id'];
		$registrationRequest   = $_REQUEST['registrationRequest'];
		$registrationMode	   = $_REQUEST['registrationMode'];

		if ($workshopDetails) {
			insertingSlipDetails($delegateId, $registrationMode, $registrationRequest);

			foreach ($workshopDetails as $key => $workshopId) {
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= $workshopId;
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $workshopCutoffId;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getWorkshopTariffId($workshopId, $workshopCutoffId, $clsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
				$workshopDetailArray[$workshopId]['booking_mode']        			= $registrationMode;
				$workshopDetailArray[$workshopId]['registration_type']       		= $registrationRequest;
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= (!$mycms->isSession('SLIP_ID')) ? 0 : $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= $_REQUEST['registration_type_add'] == "ZERO_VALUE" ? 'ZERO_VALUE' : 'UNPAID';
			}
			$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);

			foreach ($workshopReqId as $key => $reqId) {
				$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP');
				// complimentry invoice related work for workshop by weavers start
				$current_Invoice_details	= getInvoiceDetailsquery($invoiceIdWrkshp, $delegateId, $mycms->getSession('SLIP_ID'));
				$current_InvoiceAmount = $current_Invoice_details[0]['service_roundoff_price'];

				// if invoice amount = 0 and user registration classification type must be member type 
				if ($current_InvoiceAmount == 0 && $clsfId == 1) {
					// update invoice payment status
					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "
													SET `payment_status` = ?, 
													`invoice_mode` = ?
												  WHERE `id` = ?";

					$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'invoice_mode',   'DATA' => 'OFFLINE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $invoiceIdWrkshp,   'TYP' => 's');

					$mycms->sql_update($sqlUpdate, false);

					// update user workshop status

					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
													SET `payment_status` = ?,
													`booking_mode` = ?
												  WHERE `id` = ?";

					$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'booking_mode',   'DATA' => 'OFFLINE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $reqId,   'TYP' => 's');
					$mycms->sql_update($sqlUpdate, false);
				}

				// complimentry invoice related work for workshop by weavers end

				if ($clsfId == 11) // Free Workshop for PGT
				{
					$invoiceId = zeroValueInvoiceUpdate($invoiceIdWrkshp, 'WORKSHOP', $mycms->getSession('SLIP_ID')); //UPDATING COMPLIMENTARY INVOICE
				}
			}
		}

		$activeInvoiceAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
		if ( /*getUserClassificationId($delegateId) == 11 && */ $activeInvoiceAmount == 0.00) {
			complementary_workshop_confirmation_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");
			//$mycms->redirect("complementary.online.payment.success.php");
			$userDetails		 = getUserDetails($delegateId);
			$mycms->redirect("complimentary.success.php?email=" . $mycms->encoded($userDetails['user_email_id']) . "&did=" . $mycms->encoded($delegateId) . "&slip=" . $mycms->encoded($mycms->getSession('SLIP_ID')));
		} else {
			
			//$mycms->redirect("registration.".strtolower($registrationMode) .".checkout.php");
			// workshop related work for profile by weavers start     
			$slipId = $mycms->getSession('SLIP_ID');
			$userDetails		 = getUserDetails($delegateId);
			if ($registrationMode == "OFFLINE") {
				
				if (isset($_FILES['cash_document'])) {
					// Handle the file upload
					$tempFile = $_FILES['cash_document']['tmp_name'];
					// echo $tempFile;die;
					$cashFileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					
					move_uploaded_file($tempFile, $uploadDir . $cashFileName);
						
				}
				if (isset($_FILES['neft_document'])) {
					// Handle the file upload
					$tempFile = $_FILES['neft_document']['tmp_name'];
					// echo $tempFile;die;
					$neftFileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					
					move_uploaded_file($tempFile, $uploadDir . $neftFileName);
				}

				$offlinePaymentDetails  = array();
				$offlinePaymentDetails['payment_mode'] = trim($_REQUEST['payment_mode']);
				$offlinePaymentDetails['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date']);
				$offlinePaymentDetails['cash_document'] = trim($cashFileName);
				$offlinePaymentDetails['cheque_number'] = trim($_REQUEST['cheque_number']);
				$offlinePaymentDetails['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_bank']);
				$offlinePaymentDetails['cheque_date'] = trim($_REQUEST['cheque_date']);
				$offlinePaymentDetails['draft_number'] = trim($_REQUEST['draft_number']);
				$offlinePaymentDetails['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank']);
				$offlinePaymentDetails['draft_date'] = trim($_REQUEST['draft_date']);
				$offlinePaymentDetails['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no']);
				$offlinePaymentDetails['neft_bank_name'] = trim($_REQUEST['neft_bank_name']);
				$offlinePaymentDetails['neft_document'] = trim($neftFileName);
				$offlinePaymentDetails['neft_date'] = trim($_REQUEST['neft_date']);
				$offlinePaymentDetails['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no']);
				$offlinePaymentDetails['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name']);
				$offlinePaymentDetails['rtgs_date'] = trim($_REQUEST['rtgs_date']);
				//UPI Payment Option Added By Weavers start
				$offlinePaymentDetails['upi_date'] = trim($_REQUEST['upi_date']);
				$offlinePaymentDetails['txn_no'] = trim($_REQUEST['txn_no']);
				//UPI Payment Option Added By Weavers end
?>
<?php

        if($reg_type == 'BACK'){
			$RedirectURL = 'webmaster/registration.php' ;
		}else{
			$RedirectURL ='registration.success.php';
		}
		?>
				<center>
					<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
						<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
						<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
						<input type="hidden" id="delegate_id" name="user_email_id" value="<?= $userDetails['user_email_id'] ?>" />
						<input type="hidden" name="act" value="setPaymentTerms" />
						<input type="hidden" name="mode" value="<?= $registrationMode ?>" />
					    <input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />

						<input type="hidden" name="payment_mode" value="<?= $offlinePaymentDetails['payment_mode'] ?>" />

						<input type="hidden" name="cash_deposit_date" value="<?= $offlinePaymentDetails['cash_deposit_date'] ?>" />
						<input type="hidden" name="cash_document" value="<?= $offlinePaymentDetails['cash_document'] ?>" />

						<!-- UPI Payment Option Added By Weavers start -->
						<input type="hidden" name="upi_date" value="<?= $offlinePaymentDetails['upi_date'] ?>" />
						<input type="hidden" name="txn_no" value="<?= $offlinePaymentDetails['txn_no'] ?>" />
						<!-- UPI Payment Option Added By Weavers end -->

						<input type="hidden" name="cheque_number" value="<?= $offlinePaymentDetails['cheque_number'] ?>" />
						<input type="hidden" name="cheque_drawn_bank" value="<?= $offlinePaymentDetails['cheque_drawn_bank'] ?>" />
						<input type="hidden" name="cheque_date" value="<?= $offlinePaymentDetails['cheque_date'] ?>" />

						<input type="hidden" name="draft_number" value="<?= $offlinePaymentDetails['draft_number'] ?>" />
						<input type="hidden" name="draft_drawn_bank" value="<?= $offlinePaymentDetails['draft_drawn_bank'] ?>" />
						<input type="hidden" name="draft_date" value="<?= $offlinePaymentDetails['draft_date'] ?>" />

						<input type="hidden" name="neft_transaction_no" value="<?= $offlinePaymentDetails['neft_transaction_no'] ?>" />
						<input type="hidden" name="neft_bank_name" value="<?= $offlinePaymentDetails['neft_bank_name'] ?>" />
						<input type="hidden" name="neft_date" value="<?= $offlinePaymentDetails['neft_date'] ?>" />
						<input type="hidden" name="neft_document" value="<?= $offlinePaymentDetails['neft_document'] ?>" />

						<input type="hidden" name="rtgs_transaction_no" value="<?= $offlinePaymentDetails['rtgs_transaction_no'] ?>" />
						<input type="hidden" name="rtgs_bank_name" value="<?= $offlinePaymentDetails['rtgs_bank_name'] ?>" />
						<input type="hidden" name="rtgs_date" value="<?= $offlinePaymentDetails['rtgs_date'] ?>" />

						<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
					</form>
				</center>
				<script type="text/javascript">
					document.srchOnlineFrm.submit();
				</script>
			<?
				exit();
			} else if ($registrationMode == "ONLINE") {
				$onlinePaymentDetails   = array();
				$onlinePaymentDetails['card_mode'] = trim($_REQUEST['card_mode']);
			?>
				<center>
					<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
						<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
						<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
						<input type="hidden" name="act" value="paymentSet" />
						<input type="hidden" name="card_mode" value="<?= $onlinePaymentDetails['card_mode'] ?>" />
						<input type="hidden" name="mode" value="<?= $registrationMode ?>" />
						<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
						<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
					</form>
				</center>
				<script type="text/javascript">
					document.srchOnlineFrm.submit();
				</script>
			<?
				exit();
			}
			// workshop related work for profile by weavers end
		}
		exit();
		break;

	case 'add_accompany':
		include_once('includes/function.invoice.php');
		include_once('includes/function.delegate.php');
		include_once('includes/function.accompany.php');
		include_once('includes/function.dinner.php');

		$accompanyDetails = $_REQUEST;


		$delegateId                     = $mycms->getSession('LOGGED.USER.ID');


		if ($accompanyDetails) {

			foreach ($accompanyDetails['accompany_name_add'] as $kl => $val) {
				if (trim($val) == '') {
					unset($accompanyDetails['accompany_name_add'][$kl]);
					unset($accompanyDetails['accompany_selected_add'][$kl]);
				}
			}

			foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {

				$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
				$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
				$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
				$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
				$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
				$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));
				$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
				$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
				$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
				$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $_REQUEST['cutoff_id'];
				$accompanyDetailsArray[$val]['registration_request']       		 	 = $accompanyDetails['registration_request'];
				$accompanyDetailsArray[$val]['operational_area']   	    		 	 = $accompanyDetails['registration_request'];
				$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
				$accompanyDetailsArray[$val]['registration_mode']					 = $accompanyDetails['registrationMode'];
				$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
				$accompanyDetailsArray[$val]['reg_type']              				 = 'FRONT';
			}

			// echo '<pre>'; print_r($accompanyDetails);  die;

			$slip_id = insertingSlipDetails($delegateId, $_REQUEST['registrationMode'], 'GENERAL');
			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray, '', $accompanyDetails['cutoff_id']);

			foreach ($accompanyReqId as $key => $reqId) {
				insertingInvoiceDetails($reqId, 'ACCOMPANY');
			}
			$accDinnerDetails = $accompanyDetails['dinner_value'];
			if ($accDinnerDetails) {
				foreach ($accDinnerDetails as $key => $val) {
					$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId[$key];
					$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					$dinnerDetailsArray1[$key]['package_id']            = $val;
					$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $_REQUEST['cutoff_id'];
					$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					$dinnerDetailsArray1[$key]['booking_mode']          = $_REQUEST['registrationMode'];
					$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray1[$key]['refference_slip_id']	= $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
				}

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);

				foreach ($dinerReqId as $key => $reqId) {

					insertingInvoiceDetails($reqId, 'DINNER');
				}
			}
		}

		//$mycms->redirect("registration.".strtolower($_REQUEST['registrationMode']).".checkout.php");			 

		// redirect to payment
		$payment_mode = trim($accompanyDetails['registrationMode']);
		$payment_array = array();
		if ($payment_mode == "OFFLINE") {

			if (isset($_FILES['cash_document'])) {
				// Handle the file upload
				$tempFile = $_FILES['cash_document']['tmp_name'];
				// echo $tempFile;die;
				$cashFileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
				$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
				
				move_uploaded_file($tempFile, $uploadDir . $cashFileName);
					
			}
			if (isset($_FILES['neft_document'])) {
				// Handle the file upload
				$tempFile = $_FILES['neft_document']['tmp_name'];
				// echo $tempFile;die;
				$neftFileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
				$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
				
				move_uploaded_file($tempFile, $uploadDir . $neftFileName);
			}

			$payment_array['payment_mode'] = trim($_REQUEST['payment_mode']);
			$payment_array['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date']);
			$payment_array['cash_document'] = trim($cashFileName);
			$payment_array['cheque_number'] = trim($_REQUEST['cheque_number']);
			$payment_array['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_bank']);
			$payment_array['cheque_date'] = trim($_REQUEST['cheque_date']);
			$payment_array['draft_number'] = trim($_REQUEST['draft_number']);
			$payment_array['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank']);
			$payment_array['draft_date'] = trim($_REQUEST['draft_date']);
			$payment_array['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no']);
			$payment_array['neft_bank_name'] = trim($_REQUEST['neft_bank_name']);
			$payment_array['neft_date'] = trim($_REQUEST['neft_date']);
			$payment_array['neft_document'] = trim($neftFileName);
			$payment_array['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no']);
			$payment_array['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name']);
			$payment_array['rtgs_date'] = trim($_REQUEST['rtgs_date']);
			//UPI Payment Option Added By Weavers start
			$payment_array['upi_date'] = trim($_REQUEST['upi_date']);
			$payment_array['txn_no'] = trim($_REQUEST['txn_no']);
		} else if ($payment_mode == "ONLINE") {
			$payment_array['card_mode'] = trim($_REQUEST['card_mode']);
		}

		paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array);
		exit();
		break;

	case 'add_accompany_banquet':
		include_once('includes/function.invoice.php');
		include_once('includes/function.delegate.php');
		include_once('includes/function.accompany.php');
		include_once('includes/function.dinner.php');

		$accompanyDetails = $_REQUEST;


		$delegateId                     = $mycms->getSession('LOGGED.USER.ID');

		$accompanyID = $_REQUEST['accompanyID'];
		/*echo '<pre>'; print_r($accompanyDetails);
				die();*/
		if ($accompanyID) {

			if ($_REQUEST['payment_mode'] == 'Card') {
				$_REQUEST['registrationMode'] = 'ONLINE';
			} else {
				$_REQUEST['registrationMode'] = 'OFFLINE';
			}

			$slip_id = insertingSlipDetails($delegateId, $_REQUEST['registrationModePaymemt'], 'GENERAL');
			$accompanyReqId	 = $accompanyID;


			$accDinnerDetails = $accompanyDetails['dinner_value'];
			if ($accDinnerDetails) {
				//print_r($accDinnerDetails);
				foreach ($accDinnerDetails as $key => $val) {
					$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId;
					$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					$dinnerDetailsArray1[$key]['package_id']            = $val;
					$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $_REQUEST['cutoff_id'];
					$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					$dinnerDetailsArray1[$key]['booking_mode']          = $_REQUEST['registrationMode'];
					$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray1[$key]['refference_slip_id']	= $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
				}
				//echo '<pre>'; print_r($dinnerDetailsArray1);
				//die();

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);

				foreach ($dinerReqId as $key => $reqId) {

					insertingInvoiceDetails($reqId, 'DINNER');
				}
			}
		}

		//$mycms->redirect("registration.".strtolower($_REQUEST['registrationMode']).".checkout.php");			 

		// redirect to payment
		$payment_mode = trim($_REQUEST['registrationMode']);
		$payment_array = array();
		if ($payment_mode == "OFFLINE") {

			if (isset($_FILES['cash_document'])) {
				// Handle the file upload
				$tempFile = $_FILES['cash_document']['tmp_name'];
				// echo $tempFile;die;
				$cashFileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
				$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
				
				move_uploaded_file($tempFile, $uploadDir . $cashFileName);
					
			}
			if (isset($_FILES['neft_document'])) {
				// Handle the file upload
				$tempFile = $_FILES['neft_document']['tmp_name'];
				// echo $tempFile;die;
				$neftFileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
				$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
				
				move_uploaded_file($tempFile, $uploadDir . $neftFileName);
			}
			$payment_array['payment_mode'] = trim($_REQUEST['payment_mode']);
			$payment_array['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date']);
			$payment_array['cash_document'] = trim($cashFileName);
			$payment_array['cheque_number'] = trim($_REQUEST['cheque_number']);
			$payment_array['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_bank']);
			$payment_array['cheque_date'] = trim($_REQUEST['cheque_date']);
			$payment_array['draft_number'] = trim($_REQUEST['draft_number']);
			$payment_array['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank']);
			$payment_array['draft_date'] = trim($_REQUEST['draft_date']);
			$payment_array['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no']);
			$payment_array['neft_bank_name'] = trim($_REQUEST['neft_bank_name']);
			$payment_array['neft_date'] = trim($_REQUEST['neft_date']);
			$payment_array['neft_document'] = trim($neftFileName);
			$payment_array['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no']);
			$payment_array['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name']);
			$payment_array['rtgs_date'] = trim($_REQUEST['rtgs_date']);
			//UPI Payment Option Added By Weavers start
			$payment_array['upi_date'] = trim($_REQUEST['upi_date']);
			$payment_array['txn_no'] = trim($_REQUEST['txn_no']);
		} else if ($payment_mode == "ONLINE") {
			$payment_array['card_mode'] = trim($_REQUEST['card_mode']);
		}

		paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array);
		exit();
		break;

	case 'add_dinner':
		include_once('includes/function.invoice.php');
		include_once('includes/function.delegate.php');
		include_once('includes/function.accompany.php');
		include_once('includes/function.dinner.php');



		$delegateId                     = $mycms->getSession('LOGGED.USER.ID');
		$slipId = insertingSlipDetails($delegateId, $_REQUEST['registrationMode'], 'GENERAL');
		$dinnerDetails = $_REQUEST['dinner_value'];
		if ($dinnerDetails) {
			$dinnerTariffArray   = getAllDinnerTarrifDetails($_REQUEST['cutoff_id']);
			foreach ($dinnerTariffArray as $key => $dinnerArray) {
				$dinner_classification_id = $dinnerArray[$_REQUEST['cutoff_id']]['ID'];
			}
			foreach ($dinnerDetails as $key => $dinnerValue) {

				$dinnerDetailsArray1[$key]['refference_id']         = $dinnerValue;
				$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;

				$dinnerDetailsArray1[$key]['package_id']            = $dinner_classification_id;
				$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $_REQUEST['cutoff_id'];
				$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
				$dinnerDetailsArray1[$key]['booking_mode']          = $_REQUEST['registrationMode'];
				$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray1[$key]['refference_slip_id']    = 0;
				$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';

				$sqlUpdate = array();
				$sqlUpdate['QUERY']	  = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `isDinner` = ?
											  WHERE `id` = ?";
				$sqlUpdate['PARAM'][]   = array('FILD' => 'isDinner',      'DATA' => 'Y',        'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',            'DATA' => delegateId, 'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);
			}

			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);
			foreach ($dinerReqId as $key => $reqId) {
				insertingInvoiceDetails($reqId, 'DINNER');

				$sqlUpdate = array();
				$sqlUpdate['QUERY']	     = "UPDATE " . _DB_REQUEST_DINNER_ . "
												SET `refference_slip_id` = ?
											  WHERE `id` = ?";

				$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_slip_id',      'DATA' => $mycms->getSession('SLIP_ID'),   'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => reqId,                           'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);
			}
		}
		$mycms->redirect("registration." . strtolower($_REQUEST['registrationMode']) . ".checkout.php");
		exit();
		break;

	case 'add_dinner_profile':

		include_once('includes/function.invoice.php');
		include_once('includes/function.delegate.php');
		include_once('includes/function.accompany.php');
		include_once('includes/function.dinner.php');

		$delegateId  = $mycms->getSession('LOGGED.USER.ID');

		//echo '<pre>'; print_r($_REQUEST); die;

		$accDinnerDetails = $_REQUEST['accompany_dinner_value'];

		$dinnerDetails = $_REQUEST['dinner_value'];


		$dinnerTariffArrayVal   = getAllDinnerTarrifDetails($_REQUEST['cutoff_id']);

		//echo '<pre>'; print_r($dinnerDetails); die;

		$slipId = insertingSlipDetails($delegateId, $payment_mode, 'GENERAL');


		if ($accDinnerDetails) {

			if (!empty($_REQUEST['payment_mode']) && $_REQUEST['payment_mode'] == 'Card') {
				$_REQUEST['registrationMode'] = 'ONLINE';
			} else {
				$_REQUEST['registrationMode'] = 'OFFLINE';
			}

			foreach ($accDinnerDetails as $key => $val) {
				$dinnerDetailsArray2[$key]['refference_id']         = $_REQUEST['accompanyID'][$key];
				$dinnerDetailsArray2[$key]['delegate_id']           = $delegateId;
				$dinnerDetailsArray2[$key]['package_id']            = 1;
				$dinnerDetailsArray2[$key]['tariff_cutoff_id']      = $_REQUEST['cutoff_id'];
				$dinnerDetailsArray2[$key]['booking_quantity']      = 1;
				$dinnerDetailsArray2[$key]['booking_mode']          = $_REQUEST['registrationMode'];
				$dinnerDetailsArray2[$key]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray2[$key]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray2[$key]['payment_status']        = 'UNPAID';
			}


			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray2);

			foreach ($dinerReqId as $key => $reqId) {

				insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date);

				if ($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceIdDinner, 'DINNER', $mycms->getSession('SLIP_ID'));
					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE " . _DB_REQUEST_DINNER_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?";

					$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'ZERO_VALUE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $reqId,   'TYP' => 's');
					$mycms->sql_update($sqlUpdate, false);
				}
			}


			foreach ($_REQUEST['accompanyID'] as $key => $reqId) {
				if ($counter == 'Y') {
					//$invoiceIdAcompany = insertingInvoiceDetails($reqId,'ACCOMPANY',$userDetails['registration_request'], $date,$counter);
				} else {
					//$invoiceIdAcompany = insertingInvoiceDetails($reqId,'ACCOMPANY',$userDetails['registration_request'], $date);
				}

				if ($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceIdAcompany, 'ACCOMPANY', $mycms->getSession('SLIP_ID'));
					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `registration_payment_status` = ?
												  WHERE `id` = ?";

					$sqlUpdate['PARAM'][]   = array('FILD' => 'registration_payment_status',    'DATA' => 'ZERO_VALUE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                             'DATA' => $reqId,        'TYP' => 's');
					$mycms->sql_update($sqlUpdate, false);
				}
			}
		}

		if ($dinnerDetails) {

			if (!empty($_REQUEST['payment_mode']) && $_REQUEST['payment_mode'] == 'Card') {
				$payment_mode = 'ONLINE';
			} else {
				$payment_mode = 'OFFLINE';
			}

			foreach ($dinnerTariffArrayVal as $keyDinner => $dinnerValue) {
				//print_r($dinnerValue);
				$dinner_classification_id = $dinnerValue[$_REQUEST['cutoff_id']]['ID'];
				if (!empty($_REQUEST['payment_mode']) && $_REQUEST['payment_mode'] == 'Card') {
					$_REQUEST['registrationMode'] = 'ONLINE';
				} else {
					$_REQUEST['registrationMode'] = 'OFFLINE';
				}

				$dinnerDetailsArray1[$key]['refference_id']         = $delegateId;
				$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;

				$dinnerDetailsArray1[$key]['package_id']            = $dinner_classification_id;
				$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $_REQUEST['cutoff_id'];
				$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
				$dinnerDetailsArray1[$key]['booking_mode']          = $_REQUEST['registrationMode'];
				$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray1[$key]['refference_slip_id']    = 0;
				$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';

				$sqlUpdate = array();
				$sqlUpdate['QUERY']	  = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `isDinner` = ?
												  WHERE `id` = ?";
				$sqlUpdate['PARAM'][]   = array('FILD' => 'isDinner',      'DATA' => 'Y',        'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',            'DATA' => $delegateId, 'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);
			}

			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);

			foreach ($dinerReqId as $key => $reqId) {
				insertingInvoiceDetails($reqId, 'DINNER');

				$sqlUpdate = array();
				$sqlUpdate['QUERY']	     = "UPDATE " . _DB_REQUEST_DINNER_ . "
													SET `refference_slip_id` = ?
												  WHERE `id` = ?";

				$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_slip_id',      'DATA' => $mycms->getSession('SLIP_ID'),   'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => reqId,                           'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);
			}
		}


		$payment_array = array();
		if ($payment_mode == "OFFLINE") {

			if (isset($_FILES['cash_document'])) {
				// Handle the file upload
				$tempFile = $_FILES['cash_document']['tmp_name'];
				// echo $tempFile;die;
				$cashFileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
				$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
				
				move_uploaded_file($tempFile, $uploadDir . $cashFileName);
					
			}
			if (isset($_FILES['neft_document'])) {
				// Handle the file upload
				$tempFile = $_FILES['neft_document']['tmp_name'];
				// echo $tempFile;die;
				$neftFileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
				$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
				
				move_uploaded_file($tempFile, $uploadDir . $neftFileName);
			}

			$payment_array['payment_mode'] = trim($_REQUEST['payment_mode']);
			$payment_array['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date']);
			$payment_array['cash_document'] = trim($cashFileName);
			$payment_array['cheque_number'] = trim($_REQUEST['cheque_number']);
			$payment_array['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_bank']);
			$payment_array['cheque_date'] = trim($_REQUEST['cheque_date']);
			$payment_array['draft_number'] = trim($_REQUEST['draft_number']);
			$payment_array['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank']);
			$payment_array['draft_date'] = trim($_REQUEST['draft_date']);
			$payment_array['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no']);
			$payment_array['neft_bank_name'] = trim($_REQUEST['neft_bank_name']);
			$payment_array['neft_date'] = trim($_REQUEST['neft_date']);
			$payment_array['neft_document'] = trim($neftFileName);
			$payment_array['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no']);
			$payment_array['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name']);
			$payment_array['rtgs_date'] = trim($_REQUEST['rtgs_date']);
			//UPI Payment Option Added By Weavers start
			$payment_array['upi_date'] = trim($_REQUEST['upi_date']);
			$payment_array['txn_no'] = trim($_REQUEST['txn_no']);
		} else if ($payment_mode == "ONLINE") {
			//$_REQUEST['card_mode'] = 'Indian';
			$payment_array['card_mode'] = trim($_REQUEST['card_mode']);
		}


		paymentProcessSetup($delegateId, $slipId, $payment_mode, $payment_array);
		exit();
		break;

	case 'add_accommodationfrom_profile':

		setupAccommodationDetails();
		exit();
		break;
	case 'getUserValidated':
		global $mycms, $cfg;
		$email  	= trim($_REQUEST['email']);
		$mobile 	= trim($_REQUEST['mobile']);
		$status 	= 'NONE';
		$status_array = array();
		if ($email != '' && $mobile != '') {

			$sqlCheckUserEmail               = array();
			$sqlCheckUserEmail['QUERY']       = "SELECT count(*) as Totalcount
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? AND `status` = ? AND registration_request!=?";
			$sqlCheckUserEmail['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
			$sqlCheckUserEmail['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');
			$sqlCheckUserEmail['PARAM'][]   = array('FILD' => 'registration_request',        'DATA' => 'ABSTRACT',    'TYP' => 's');

			$resultUserEmail    		= $mycms->sql_select($sqlCheckUserEmail);
			//var_dump($resultUserEmail);
			if (intval($resultUserEmail[0]['Totalcount']) > 0) {
				$status = 'EMAIL_IN_USE';
			} else {
				$sqlCheckUserMobile               = array();
				$sqlCheckUserMobile['QUERY']       = "SELECT count(* ) as Totalcount
													 FROM " . _DB_USER_REGISTRATION_ . "
													WHERE `user_mobile_no` = ? AND `status` = ? AND registration_request!=?";
				$sqlCheckUserMobile['PARAM'][]   = array('FILD' => 'user_mobile_no', 'DATA' => $mobile, 'TYP' => 's');
				$sqlCheckUserMobile['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');
				$sqlCheckUserEmail['PARAM'][]   = array('FILD' => 'registration_request',        'DATA' => 'ABSTRACT',    'TYP' => 's');

				$resultUserMobile    		= $mycms->sql_select($sqlCheckUserMobile);
				//var_dump($resultUserMobile );
				if (intval($resultUserMobile[0]['Totalcount']) > 0) {
					$status = 'MOBILE_IN_USE';
				} else {
					$status = 'AVAILABLE';
				}
			}
		}
		echo $status;
		exit();
		break;

	case 'getComboDate':

		global $mycms, $cfg;

		$combo_val  = trim($_REQUEST['combo_val']);
		if (!empty($combo_val)) {

			$classificationCombo = getAllClassificationCombo($combo_val);
			$currentCutoffId = getTariffCutoffId();
			//print_r($classificationCombo);
			$hotelId = $classificationCombo['residential_hotel_id'];
			$classificationId = $classificationCombo['id'];

			$sqlHotel				= array();
			$sqlHotel['QUERY']	 	= "SELECT
													tracm.id as ACCOMMODATION_TARIFF_ID,
													tracm.hotel_id as HOTEL_ID,
													tracm.package_id as ACCOMMODATION_PACKAGE_ID, 
													chkindate.check_in_date as CHECKIN_DATE,
													tracm.checkin_date_id as CHECKIN_DATE_ID,
													tracm.checkout_date_id as CHECKOUT_DATE_ID,
													chkoutdate.check_out_date as CHECKOUT_DATE,
													DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
													FROM " . _DB_TARIFF_ACCOMMODATION_ . " as tracm
													INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " as chkindate
													on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
													INNER JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " as chkoutdate
													on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
													WHERE tracm.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ? 
													AND tracm.hotel_id ='" . $hotelId . "' ORDER BY CHECKIN_DATE ASC, CHECKOUT_DATE ASC"; // HAVING (DAYS) < 4 // remove on 21.09.2022 (user can select hotels more then 3 days)
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.status', 'DATA' => 'A',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
			$resultHotel		    	= $mycms->sql_select($sqlHotel);

			//echo '<pre>';print_r($resultHotel);


			/*$sqlComboDate  = array();
					 $sqlComboDate['QUERY'] = "SELECT CC.check_in_date, co.check_out_date,TA.inr_amount, TA.hotel_id,TA.checkout_date_id,TA.checkin_date_id,TA.tariff_cutoff_id, TA.classification_id, TA.id  FROM "._DB_TARIFF_COMBO_ACCOMODATION_." TA 
					INNER JOIN "._DB_ACCOMMODATION_CHECKIN_DATE_." CC ON
						 TA.checkin_date_id = CC.id 
					INNER JOIN "._DB_ACCOMMODATION_CHECKOUT_DATE_." co 
						ON TA.checkout_date_id = co.id 
						WHERE TA.classification_id=? AND CC.status=? AND co.status=? AND TA.status=? ";

					$sqlComboDate['PARAM'][]  = array('FILD' => 'TA.classification_id',  'DATA' =>$combo_val,  'TYP' => 's');
					$sqlComboDate['PARAM'][]  = array('FILD' => 'CC.status',  'DATA' =>'A',  'TYP' => 's');
					$sqlComboDate['PARAM'][]  = array('FILD' => 'co.status',  'DATA' =>'A',  'TYP' => 's');
					$sqlComboDate['PARAM'][]  = array('FILD' => 'TA.status',  'DATA' =>'A',  'TYP' => 's');				

					$resComboDate = $mycms->sql_select($sqlComboDate);*/

			/*----------------New code------------------------------*/
			/*$dates = array();	
					$dCount = 0;		
					$packageCheckDate = array();	
					$packageCheckDate['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
														   WHERE `hotel_id` = ?
															 AND `status` = ?
													    ORDER BY  check_in_date";
						$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelId , 	'TYP' => 's');
						$packageCheckDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 		'TYP' => 's');									    
						$resCheckIns = $mycms->sql_select($packageCheckDate);
						
						foreach ($resCheckIns as $key => $rowCheckIn) {
							$packageCheckoutDate = array();
							$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'".$rowCheckIn['check_in_date']."',`check_out_date`) AS dayDiff
															   FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
															  WHERE `hotel_id` = ?
															    AND `status` = ?
															    AND `check_out_date` > ?
														   ORDER BY check_out_date";
							$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelId , 	    'TYP' => 's');
							$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
							$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date' ,	'DATA' => $rowCheckIn['check_in_date'] , 			'TYP' => 's');
							
							
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

						}*/


			$accommodationDetails = array();
			foreach ($resultHotel as $key => $value) {

				$accommodationDetails[$value['HOTEL_ID']][] = $value;
			}

			if (count($resultHotel) > 0) {
				$uniqueArray = [];
				foreach ($resultHotel as $key => $accomodationTariff) {
					$hotelId = $accomodationTariff['CHECKIN_DATE_ID'];
					if (!isset($uniqueArray[$hotelId])) {
						$uniqueArray[$hotelId] = $accomodationTariff;
					}
				}
				$uniqueArray = array_values($uniqueArray);
				//echo '<pre>'; print_r($uniqueArray);
			?>

				<div class="col-xs-6 form-group" actAs='fieldContainer'>

					<div class="radio">
						<label class="select-lable">CHECK-IN DATE</label>

						<?php
						foreach ($uniqueArray as $key => $accomodationTariff) {
						?>
							<label class="container-box" style="display:block"><?= $accomodationTariff['CHECKIN_DATE'] ?>
								<input type="radio" operetionMode="checkInCheckOutCombo_<?= $accomodationTariff['HOTEL_ID'] . '_' . $accomodationTariff['CHECKINID'] ?>" use="accoStartDateCombo" id="accDateCombo_<?= $accomodationTariff['HOTEL_ID'] . '_' . $accomodationTariff['CHECKINID'] . '_' . $accomodationTariff['tariff_cutoff_id'] ?>" name="accDateCombo[]" value="<?= $accomodationTariff['CHECKIN_DATE_ID'] . '-' . $accomodationTariff['CHECKOUT_DATE_ID'] . '-' . $accomodationTariff['HOTEL_ID'] ?>" checkoutDateCombo="<?= $accomodationTariff['CHECKOUT_DATE_ID'] . '_' . $accomodationTariff['ACCOMMODATION_TARIFF_ID'] ?>" onClick="showChekinChekoutDateCombo(this);">
								<span class="checkmark"></span>
							</label>
						<?php
						}
						?>

					</div>

					<div class="alert alert-danger" callFor='alert'>Please select a proper
						option.</div>
				</div>
				<div class="col-xs-6 form-group" actAs='fieldContainer'>

					<div class="radio">
						<label class="select-lable">CHECK-OUT DATE</label>
						<?php
						foreach ($uniqueArray as $key => $accomodationTariff) {
						?>
							<label class="container-box" style="display:block;"><?= $accomodationTariff['CHECKOUT_DATE'] ?>
								<input type="radio" operetionMode="checkInCheckOutCombo_<?= $accomodationTariff['HOTEL_ID'] . '_' . $accomodationTariff['CHECKOUTID'] ?>" value="<?= $accomodationTariff['CHECKOUT_DATE_ID'] . '_' . $accomodationTariff['ACCOMMODATION_TARIFF_ID'] ?>" use="accoEndDateCombo" disabled="disabled">
								<span class="checkmark"></span>
							</label>
						<?php
						}
						?>

					</div>
					<div class="alert alert-danger" callFor='alert'>Please select a proper
						option.</div>
				</div>
	<?php

			}
		}


		exit();
		break;
}

function headerImageUpload($id, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		// $ids 							= str_pad($participantId,4,'0',STR_PAD_LEFT);
		$rand							= 'USER_IMAGE_' . $id . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['USER.PROFILE.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `user_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $id . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function combinedRegistrationProcess()
{
	global $mycms, $cfg;

	      
	
	///// Step 0 process /////
    // echo "<pre>";print_r($_REQUEST);echo "<pre>";die;
 
	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}

	if (isset($_FILES['user_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['user_document']['tmp_name'];
		// echo $tempFile;die;
		$fileName = "USERDOC_" .date('ymdHis')."_". $_FILES['user_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
		

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$dataArray['user_document_name'] 	= $fileName;
		} else {
			// echo "Error: Unable to move file to destination directory.";
		}
	}

	
	
	$abstractDelegateId 					= $_REQUEST['abstractDelegateId'];
     /////////////////////////////////
    if($abstractDelegateId!=''){
		$sqlDetails = array();
		$sqlDetails['QUERY'] = "SELECT `registration_classification_id` 
							  FROM " . _DB_USER_REGISTRATION_ . " 
							  WHERE `id` = '" . $abstractDelegateId . "'
							  AND `registration_request` != 'ONLYWORKSHOP'";
	    $resDetails = $mycms->sql_select($sqlDetails);
		if($resDetails[0]['registration_classification_id']>=1){
			if($_REQUEST['reg_area']=='FRONT'){
				header("Location: " . _BASE_URL_ );
			}else{
				 header("Location: " . _BASE_URL_ . "webmaster/registration.php");

			}
		}
	}

	////////////////////////////////////////
	$mycms->setSession('CUTOFF_ID_FRONT', 	$_REQUEST['cutoff_id']);
	$mycms->setSession('CLSF_ID_FRONT', 	$_REQUEST['registration_classification_id'][0]);

	$mycms->setSession('REGISTRATION_MODE', $_REQUEST['registrationMode']);

	$mycms->setSession('WORKSHOP_ID', 		$_REQUEST['workshop_id']);
	$mycms->setSession('DINNER_VALUE', 		$_REQUEST['dinner_value']);
	$mycms->setSession('HOTEL_ID', 			$_REQUEST['hotel_id']);
    $mycms->setSession('SLIP_ID', '');
	$regClsfId 								= $_REQUEST['registration_classification_id'][0];
	$dataArray['regClsfId'] 				= $regClsfId;

	$accmodationPackageId 					= $_REQUEST['package_id'];
	//$accmodationDateSet 					= $_REQUEST['accDate'][$accmodationPackageId];
	$accmodationDateSet 					= $_REQUEST['accDate'][0];

	$dataArray['accmodationPackageId'] 		= $accmodationPackageId;
	$dataArray['accmodationDateSet'] 		= $accmodationDateSet;

	$accDates 			= explode('-', $accmodationDateSet);
	$accCheckinDateId 	= $accDates[0];
	$accCheckOutDateId 	= $accDates[1];

	$dataArray['accCheckinDateId'] 			= $accCheckinDateId;
	$dataArray['accCheckOutDateId'] 		= $accCheckOutDateId;

	$mycms->setSession('STEP0_ACCM_PACKID', 		$accmodationPackageId);
	$mycms->setSession('STEP0_ACCM_CHECKINDATE',	$accCheckinDateId);
	$mycms->setSession('STEP0_ACCM_CHECKOUTDATE',	$accCheckOutDateId);

	$preffered_accommpany_name 				=  $_REQUEST['preffered_accommpany_name'];
	$preffered_accommpany_email 			=  $_REQUEST['preffered_accommpany_email'];
	$preffered_accommpany_mobile 			=  $_REQUEST['preffered_accommpany_mobile'];

	$dataArray['preffered_accommpany_name'] 	= $preffered_accommpany_name;
	$dataArray['preffered_accommpany_email'] 	= $preffered_accommpany_email;
	$dataArray['preffered_accommpany_mobile'] 	= $preffered_accommpany_mobile;

	$mycms->setSession('STEP0_PREF_ACMP_NAME', 	$preffered_accommpany_name);
	$mycms->setSession('STEP0_PREF_ACMP_EMAIL',	$preffered_accommpany_email);
	$mycms->setSession('STEP0_PREF_ACMP_MOB',	$preffered_accommpany_mobile);

	insertIntoProcessFlow("step0", $dataArray);

	
	///// Step 1 process /////		
	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}

	$dataArray['registration_cutoff'] 	        	 	= $mycms->getSession('CUTOFF_ID_FRONT');
	if($_REQUEST['workshop_cutoff']==""){
    	$dataArray['workshop_cutoff'] 	        	 	    = getWorkshopTariffCutoffId();

	}else{
	  $dataArray['workshop_cutoff'] 	        	 	    = $_REQUEST['workshop_cutoff'];

	}
	$dataArray['registrationMode']        			 	= $mycms->getSession('REGISTRATION_MODE');
	$dataArray['registration_mode']        				= $mycms->getSession('REGISTRATION_MODE');

	$dataArray['preffered_accommpany_name'] 	 		= $mycms->getSession('STEP0_PREF_ACMP_NAME');
	$dataArray['preffered_accommpany_email']  			= $mycms->getSession('STEP0_PREF_ACMP_EMAIL');
	$dataArray['preffered_accommpany_mobile'] 			= $mycms->getSession('STEP0_PREF_ACMP_MOB');
	$registration_Pay_mode	=  $_REQUEST['registration_Pay_mode'];
	$dataArray['regsitaion_mode'] 				= $registration_Pay_mode;

	if ($mycms->isSession('HOTEL_ID')) {
		$dataArray['hotel_id'] = $mycms->getSession('HOTEL_ID');
	}
	if ($mycms->isSession('STEP0_ACCM_PACKID')) {
		$dataArray['accommodation_package_id'] = $mycms->getSession('STEP0_ACCM_PACKID');
	}
	if ($mycms->isSession('STEP0_ACCM_CHECKINDATE')) {
		$dataArray['accommodation_checkIn'] = $mycms->getSession('STEP0_ACCM_CHECKINDATE');
	}
	if ($mycms->isSession('STEP0_ACCM_CHECKOUTDATE')) {
		$dataArray['accommodation_checkOut'] = $mycms->getSession('STEP0_ACCM_CHECKOUTDATE');
	}

	$USER_DETAILS_FRONT['NAME'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'] . ". " . $_REQUEST['user_first_name'] . " " . $_REQUEST['user_middle_name'] . " " . $_REQUEST['user_last_name'])));
	$USER_DETAILS_FRONT['EMAIL']	  = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
	$USER_DETAILS_FRONT['PH_NO'] 	  = addslashes(trim($_REQUEST['user_usd_code'] . " - " . $_REQUEST['user_mobile']));
	$USER_DETAILS_FRONT['CUTOFF']     = addslashes(trim($mycms->getSession('CUTOFF_ID_FRONT')));
	$USER_DETAILS_FRONT['CATAGORY']   = addslashes(trim($mycms->getSession('CLSF_ID_FRONT')));

	$accDetails	= getUserTypeAndRoomType($mycms->getSession('CLSF_ID_FRONT'));

	$mycms->setSession('USER_DETAILS_FRONT', $USER_DETAILS_FRONT);

	insertIntoProcessFlow("step1", $dataArray);


	$selectedWorkshop 				= $mycms->getSession('WORKSHOP_ID');
	$selectedRegClassf 				= $mycms->getSession('CLSF_ID_FRONT');

	$mycms->removeSession('WORKSHOP_ID');
	$mycms->removeSession('DINNER_VALUE');
	$mycms->removeSession('HOTEL_ID');
	$mycms->removeSession('STEP0_ACCM_PACKID');
	$mycms->removeSession('STEP0_ACCM_CHECKINDATE');
	$mycms->removeSession('STEP0_ACCM_CHECKOUTDATE');
	$mycms->removeSession('STEP0_PREF_ACMP_NAME');
	$mycms->removeSession('STEP0_PREF_ACMP_EMAIL');
	$mycms->removeSession('STEP0_PREF_ACMP_MOB');
     
	if (in_array($selectedWorkshop[0], $cfg['INDEPENDANT.WORKSHOPS']) && $selectedRegClassf == '') {
		$regNextAct	=	"onlyWorkshopReg";
	}else if($_REQUEST['registration_request']=='ONLYWORKSHOP'){

	$regNextAct	=	"onlyWorkshopReg";
	} else {
		///// Step 3 process /////			
		$dataArray = array();
		foreach ($_REQUEST as $key => $value) {
			$dataArray[$key] = $value;
		}

		$registrationClassificationId   			= $mycms->getSession('CLSF_ID_FRONT');
		$registrationCutoffId  	        			= $mycms->getSession('CUTOFF_ID_FRONT');
		$isAccompany	                			= $mycms->getSession('IS_ACCOMPANY');
		$no_accompany	                			= $mycms->getSession('NO_ACCOMPANY');
		//$accompanyCatagory              			= 2;
		$accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 

		$registrationDetails 						= getAllRegistrationTariffs();
		$registrationAmount 						= $registrationDetails[$accompanyCatagory][$registrationCutoffId]['AMOUNT'];
		$registrationCurrency 						= $registrationDetails[$accompanyCatagory][$registrationCutoffId]['CURRENCY'];

		$dataArray['accompanyTariffAmount']        	= $registrationAmount;
		$dataArray['registration_cutoff']        	= $registrationCutoffId;
		$dataArray['accompanyClasfId']        		= $accompanyCatagory;
		$dataArray['registration_mode']        		= $mycms->getSession('REGISTRATION_MODE');

		foreach ($dataArray['accompany_name_add'] as $kl => $val) {
			if (trim($val) == '') {
				unset($dataArray['accompany_name_add'][$kl]);
				unset($dataArray['accompany_selected_add'][$kl]);
			}
		}
		$dataArray['dinner_value'] = $_REQUEST['accompany_dinner_value'];

		insertIntoProcessFlow("step3", $dataArray);
		
    
		if ($abstractDelegateId != '' && $abstractDelegateId > 0) {
			$regNextAct	=	"registerAbstractUser";
		} else {
			$regNextAct	=	"step6";
		}
	}

	// accommodation related work by weavers start //
	///// Step 4 process /////		

	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}

	$dataArray['check_in_date'] 	        	 	= $accCheckinDateId;
	$dataArray['check_out_date']        			= $accCheckOutDateId;
	$dataArray['accmName']        					= $_REQUEST['accomDetails'];
	$dataArray['hotel_dates']        					= $_REQUEST['hotel_dates'];
	$dataArray['accmomdation-qty']        					= $_REQUEST['accmomdation-qty'];
	$dataArray['card_number']        					= $_REQUEST['card_number'];
	$dataArray['card_date']        					= $_REQUEST['card_date'];

	insertIntoProcessFlow("step4", $dataArray);


	// accommodation related work by weavers end //

	///// Step 6 process /////
	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}

	if (isset($_FILES['cash_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['cash_document']['tmp_name'];
		// echo $tempFile;die;
		$fileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
		

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$dataArray['cash_document'] 	= $fileName;
		} else {
			$dataArray['cash_document'] 	= $fileName;
		}
	}
	if (isset($_FILES['neft_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['neft_document']['tmp_name'];
		// echo $tempFile;die;
		$fileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
		

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$dataArray['neft_document'] 	= $fileName;
		} else {
			$dataArray['neft_document'] 	= $fileName;
		}
	}
	
	insertIntoProcessFlow("step6", $dataArray);


  
	?>
	<center>
		<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchProcessFrm">
			<input type="hidden" name="act" value="<?= $regNextAct ?>" />
			<input type="hidden" name="paymentstatus" value="<?= $_REQUEST['paymentstatus'] ?>" />
			<input type="hidden" name="discountAmount" value="<?= $_REQUEST['discountAmount'] ?>" />
			<input type="hidden" name="registration_Pay_mode" value="<?= $registration_Pay_mode ?>" />
			<style>
				.payment_loading {
					width: 100%;
					height: 100vh;
					position: fixed;
					background: #fff3e4;
					z-index: 2;
					/* display: none; */
				}
				
				.payment_loading_inner {
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 100%;
					height: 100%;
				}
				
				.payment_loading video {
				
					width: 150px;
				}
				
				.payment_loading h4 {
					text-transform: uppercase;
					letter-spacing: 8px;
					color: #560600;
					font-size: 23px;
					font-weight: 500;
				}
				
				.payment_loading p {
					margin: 0;
					width: 40%;
					text-align: center;
					font-size: 13px;
					color: #7e0900;
				}
			</style>
			<div class="payment_loading">
				<div class="payment_loading_inner">
					<video src="images/Cube.webm" autoplay inline muted loop></video>
					<h4>Running Registration Process</h4>
							<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
				</div>
			</div>
		</form>
	</center>
	<script type="text/javascript">
		document.srchProcessFrm.submit();
	</script>
	<?
	exit();
}

function step6()
{
	global $mycms, $cfg;

	$mycms->setSession('CURRENT_REG_USER', 'FINISH');
	$mycms->removeSession('SLIP_ID');

	$processFlowId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');

	$step6Data	= getProcessFlowData('step6');

    $registration_Pay_mode = $_REQUEST['registration_Pay_mode'] ?? '';

 	detailsInseringProcess($processFlowId,$registration_Pay_mode);


	$slipId	= $mycms->getSession('SLIP_ID');

	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT *, IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount, 
											user.registration_classification_id AS registration_classification_id
									   FROM " . _DB_SLIP_ . " slip 
								 INNER JOIN " . _DB_USER_REGISTRATION_ . " user
										 ON slip.delegate_id = user.id
							LEFT OUTER JOIN ( SELECT SUM(`service_roundoff_price`) AS totalInvoice, `slip_id`
												FROM " . _DB_INVOICE_ . " 
											   WHERE `status` = 'A'
											GROUP BY `slip_id` ) activeInvoiceAmount
										 ON slip.id = activeInvoiceAmount.slip_id
									  WHERE slip.status = ? 
										AND slip.id =?";

	$sqlWorkshopclsf['PARAM'][]  = array('FILD' => 'slip.status', 'DATA' => 'A',     'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]  = array('FILD' => 'slip.id',     'DATA' => $slipId, 'TYP' => 's');
	$resWorkshopclsf 			 = $mycms->sql_select($sqlWorkshopclsf);

	$resWorkshopclsfres 		 = $resWorkshopclsf[0];
	$clafId 					 = $resWorkshopclsfres['registration_classification_id'];
	$delegateId 				 = $resWorkshopclsfres['delegate_id'];
	$activeInvoiceAmount 		 = $resWorkshopclsfres['activeInvoiceAmount'];
	$reg_type 		             = $resWorkshopclsfres['reg_type'];

	$sqlProcessUpdateStep    			= array();
	$sqlProcessUpdateStep['QUERY']      = "  UPDATE " . _DB_PROCESS_STEP_ . "
													SET `regitration_status` = ?
												  WHERE `id` = ?";
	$sqlProcessUpdateStep['PARAM'][]    = array('FILD' => 'regitration_status',  'DATA' => 'COMPLETE',                              'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][]    = array('FILD' => 'id',                  'DATA' => $mycms->getSession('PROCESS_FLOW_ID'),   'TYP' => 's');
	$mycms->sql_update($sqlProcessUpdateStep, false);

        if($reg_type == 'FRONT'){
			$RedirectURL = 'registration.success.php';
		}else{
			$RedirectURL ='webmaster/registration.php';
		}
	if ($activeInvoiceAmount == 0.00 && $registration_Pay_mode != 'ZERO_VALUE') {
		$payment_mode = $_REQUEST['payment_mode'];
		complementaryPaymentConfirmationProcess($slipId, $delegateId);
		
	?>
	  
		<center>
			<form action="<?= _BASE_URL_ ?><?=$RedirectURL?>" method="post" name="srchComplementaryFrm">
				<input type="hidden" name="did" value="<?= $mycms->encoded($delegateId) ?>" />
				<style>
				.payment_loading {
					width: 100%;
					height: 100vh;
					position: fixed;
					background: #fff3e4;
					z-index: 2;
					/* display: none; */
				}
				
				.payment_loading_inner {
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 100%;
					height: 100%;
				}
				
				.payment_loading video {
				
					width: 150px;
				}
				
				.payment_loading h4 {
					text-transform: uppercase;
					letter-spacing: 8px;
					color: #560600;
					font-size: 23px;
					font-weight: 500;
				}
				
				.payment_loading p {
					margin: 0;
					width: 40%;
					text-align: center;
					font-size: 13px;
					color: #7e0900;
				}
			</style>
			<div class="payment_loading">
				<div class="payment_loading_inner">
					<video src="images/Cube.webm" autoplay inline muted loop></video>
					<h4 align="center">Finalizing<br />Please Wait</h4>
							<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
				</div>
			</div>
			</form>
		</center>
		<script type="text/javascript">
			document.srchComplementaryFrm.submit();
		</script>
		<?
		exit();
	} else {


		$sqlSlip             = array();
		$sqlSlip['QUERY']    = "SELECT * FROM " . _DB_SLIP_ . " 
									 WHERE `status` =? 
									   AND `id` =?";
		$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',      'TYP' => 's');
		$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' => $slipId,  'TYP' => 's');

		$resSlip			 = $mycms->sql_select($sqlSlip);
		$rowSlip			 = $resSlip[0];

		$userDetails		 = getUserDetails($rowSlip['delegate_id']);

		if ($mycms->getSession('REGISTRATION_MODE') == "OFFLINE") {
			$offlinePaymentDetails	= $step6Data;
			//echo 12122;


		?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" id="delegate_id" name="user_email_id" value="<?= $userDetails['user_email_id'] ?>" />
					<input type="hidden" name="act" value="setPaymentTerms" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />

					<input type="hidden" name="payment_mode" value="<?= $offlinePaymentDetails['payment_mode'] ?>" />
					<input type="hidden" name="paymentstatusPre" value="<?= $_REQUEST['paymentstatus'] ?>" />
					<input type="hidden" name="discountAmount" value="<?= $_REQUEST['discountAmount'] ?>" />
					<input type="hidden" name="card_number" value="<?= $offlinePaymentDetails['card_number'] ?>" />
					<input type="hidden" name="card_date" value="<?= $offlinePaymentDetails['card_date'] ?>" />

					<input type="hidden" name="cash_deposit_date" value="<?= $offlinePaymentDetails['cash_deposit_date'] ?>" />
					<input type="hidden" name="cash_document" value="<?= $offlinePaymentDetails['cash_document'] ?>" />

					<!-- UPI Payment Option Added By Weavers start -->
					<input type="hidden" name="upi_date" value="<?= $offlinePaymentDetails['upi_date'] ?>" />
					<input type="hidden" name="txn_no" value="<?= $offlinePaymentDetails['txn_no'] ?>" />
					<!-- UPI Payment Option Added By Weavers end -->

					<input type="hidden" name="cheque_number" value="<?= $offlinePaymentDetails['cheque_number'] ?>" />
					<input type="hidden" name="cheque_drawn_bank" value="<?= $offlinePaymentDetails['cheque_drawn_bank'] ?>" />
					<input type="hidden" name="cheque_date" value="<?= $offlinePaymentDetails['cheque_date'] ?>" />

					<input type="hidden" name="draft_number" value="<?= $offlinePaymentDetails['draft_number'] ?>" />
					<input type="hidden" name="draft_drawn_bank" value="<?= $offlinePaymentDetails['draft_drawn_bank'] ?>" />
					<input type="hidden" name="draft_date" value="<?= $offlinePaymentDetails['draft_date'] ?>" />

					<input type="hidden" name="neft_transaction_no" value="<?= $offlinePaymentDetails['neft_transaction_no'] ?>" />
					<input type="hidden" name="neft_bank_name" value="<?= $offlinePaymentDetails['neft_bank_name'] ?>" />
					<input type="hidden" name="neft_date" value="<?= $offlinePaymentDetails['neft_date'] ?>" />
					<input type="hidden" name="neft_document" value="<?= $offlinePaymentDetails['neft_document'] ?>" />

					<input type="hidden" name="rtgs_transaction_no" value="<?= $offlinePaymentDetails['rtgs_transaction_no'] ?>" />
					<input type="hidden" name="rtgs_bank_name" value="<?= $offlinePaymentDetails['rtgs_bank_name'] ?>" />
					<input type="hidden" name="rtgs_date" value="<?= $offlinePaymentDetails['rtgs_date'] ?>" />

					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
		<?
			exit();
		} else if ($mycms->getSession('REGISTRATION_MODE') == "ONLINE") {
			$onlinePaymentDetails	= $step6Data;

		?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" name="act" value="paymentSet" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
					<input type="hidden" name="card_mode" value="<?= $onlinePaymentDetails['card_mode'] ?>" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
		<?
			exit();
		}
	}
}

function onlyWorkshopReg()
{
	global $mycms, $cfg;
	// include_once('includes/function.delegate.php');
	// include_once('includes/function.invoice.php');
	// include_once('includes/function.accompany.php');
	// include_once('includes/function.workshop.php');
	// include_once('includes/function.registration.php');
	// include_once('includes/function.messaging.php');
	// include_once('includes/function.accommodation.php');
	// include_once("includes/function.dinner.php");
   
	$mycms->setSession('CURRENT_REG_USER', 'FINISH');
	$mycms->removeSession('SLIP_ID');
 
	$processFlowId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');
	$step6Data	= getProcessFlowData('step6');

	// DETAILS INSERTING PROCESS	
	$sqlProcessFlow          = array();
	$sqlProcessFlow['QUERY'] = "SELECT * FROM " . _DB_PROCESS_STEP_ . " 
									 WHERE `id` = ?";

	$sqlProcessFlow['PARAM'][]  = array('FILD' => 'id', 'DATA' => $processFlowId, 'TYP' => 's');

	$resProcessFlow			= $mycms->sql_select($sqlProcessFlow);
	if ($resProcessFlow) {
		$rowProcessFlow 	= $resProcessFlow[0];

		$userDetails		= unserialize($rowProcessFlow['step1']);
		$workshopDetails	= $userDetails['workshop_id'];
		$cutoffId			= $userDetails['registration_cutoff'];
		$workshopCutoffId	= $userDetails['workshop_cutoff'];
		$clsfId				= '8';
		$date				= $userDetails['date'];

		if ($userDetails) {
			$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($userDetails['user_email_id'])));
			$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($userDetails['comunication_email'])));
			$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
			$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
			// $userDetailsArray['membership_number']                    = addslashes(trim("L/A" . $userDetails['membership_number']));
			$userDetailsArray['user_initial_title']   				  = addslashes(trim(strtoupper($userDetails['user_initial_title'])));
			$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['user_first_name'])));
			$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['user_middle_name'])));
			$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['user_last_name'])));
			$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title'] . " " . $userDetailsArray['user_first_name'] . " " . $userDetailsArray['user_middle_name'] . " " . $userDetailsArray['user_last_name'];
			$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
			$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['user_usd_code'])));
			$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['user_mobile'])));
			$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
			$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['user_address'])));
			$userDetailsArray['user_country']                         = addslashes(trim(strtoupper($userDetails['user_country'])));
			$userDetailsArray['user_state']                           = addslashes(trim(strtoupper($userDetails['user_state'])));
			$userDetailsArray['user_city']                            = addslashes(trim(strtoupper($userDetails['user_city'])));
			$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['user_postal_code'])));
			$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
			$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
			$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));
			if ($userDetailsArray['user_dob_year'] != "" && $userDetailsArray['user_dob_month'] != "" && $userDetailsArray['user_dob_day'] != "") {
				$userDetailsArray['user_dob']                         = number_pad($userDetailsArray['user_dob_year'], 4) . "-" . number_pad($userDetailsArray['user_dob_month'], 2) . "-" . number_pad($userDetailsArray['user_dob_day'], 2);
			}

			$userDetailsArray['user_gender']                          = ($userDetails['user_gender'] == '') ? 'NA' : $userDetails['user_gender'];
			$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['user_designation'])));
			$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['user_depertment'])));
			$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['user_institution'])));
			$userDetailsArray['user_food_preference']                 = $userDetails['user_food_preference'];
			$userDetailsArray['user_other_food_details']              = addslashes(trim(strtoupper($userDetails['user_food_details'])));
			$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['user_pasport_no'])));
			$userDetailsArray['passport_expiry_date']                 = number_pad($userDetails['user_pasport_exp_year'], 4) . "-" . number_pad($userDetails['user_pasport_exp_month'], 2) . "-" . number_pad($userDetails['user_pasport_exp_day'], 2);

			$userDetailsArray['user_document']						  = $fileDetails;
			$userDetailsArray['isRegistration']						  = 'N';
			$userDetailsArray['isConference']						  = 'N';
			$userDetailsArray['isWorkshop']							  = 'N';
			$userDetailsArray['isAccommodation']                      = 'N';
			$userDetailsArray['isTour']								  = 'N';
			$userDetailsArray['IsAbstract']							  = 'N';
            $reg_type                                                   = $userDetails['reg_area'];
			$userDetailsArray['registration_classification_id']		  = '8';
			$userDetailsArray['registration_tariff_cutoff_id']        = '0';
			$userDetailsArray['registration_request']       		  = 'ONLYWORKSHOP';
			$userDetailsArray['operational_area']   	    		  = 'ONLYWORKSHOP';
			$paymentStatusPre =$userDetails['paymentstatus'];
			if($paymentStatusPre==''){
			$registration_payment_status='UNPAID';

			}else{
			$registration_payment_status=$paymentStatusPre;

			}
		    $userDetailsArray['registration_payment_status']		  = $registration_payment_status;
			$userDetailsArray['registration_mode']					  = ($mycms->getSession('REGISTRATION_MODE') == "ONLINE") ? "ONLINE" : "OFFLINE";
			$userDetailsArray['account_status']						  = 'REGISTERED';
			$userDetailsArray['reg_type']              				  = addslashes(trim(strtoupper($userDetails['reg_area'])));
			$paymentStatusPre = (!empty($userDetails['paymentstatus'])) 
				? $userDetails['paymentstatus'] 
				: 'UNPAID';
		    $discountAmount =$userDetails['discountAmount'];
            $userDetailsArray['regsitaion_mode']=$userDetails['registration_Pay_mode'];
            $userDetailsArray['workshop_classification_id']=$userDetails['workshop_classification_id'];

			if ($userDetails['abstractDelegateId'] != '' && $userDetails['abstractDelegateId'] > 0) {
				$delegateId	= insertingExistingUserDetails($userDetails['abstractDelegateId'], $userDetailsArray, $date);
			} else {
				$delegateId = insertingUserDetails($userDetailsArray, $date);
			}
	        //   echo '<pre>'; print_r($mycms->getSession('SLIP_ID'));die;

			if ($mycms->getSession('SLIP_ID') == "") {
				$mycms->setSession('LOGGED.USER.ID', $delegateId);
				insertingSlipDetails($delegateId, $userDetailsArray['registration_mode'], $userDetails['registration_request'], $date, $userDetailsArray['reg_type'],$paymentStatusPre);
			}
		}
		if ($workshopDetails) {
			foreach ($workshopDetails as $key => $workshopId) {
				$onlyWrkshopclsfId = $userDetailsArray['workshop_classification_id'][$workshopId];
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= $workshopId;
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $workshopCutoffId;
				if($onlyWrkshopclsfId == ''){
				 $workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getOnlyWorkshopTariffId($workshopId, $workshopCutoffId,0);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = 0;

				}else{
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getOnlyWorkshopTariffId($workshopId, $workshopCutoffId,0,$onlyWrkshopclsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $userDetailsArray['workshop_classification_id'][$workshopId];

				}
				$workshopDetailArray[$workshopId]['booking_mode']        			= $userDetailsArray['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type']       		= $userDetails['registration_request'];
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= $paymentStatusPre;
			}
			// 

			$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);
		
			foreach ($workshopReqId as $key => $reqId) {
				// $invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP', $userDetails['registration_request'], $date,'',$paymentStatusPre);
			    $invoiceIdWrkshp = insertingInvoiceDetails($reqId,'WORKSHOP',$userDetails['registration_request'], '',$date,$paymentStatusPre);

				if ($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceIdWrkshp, 'WORKSHOP', $mycms->getSession('SLIP_ID'));
					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
													SET `payment_status` = ?
												  WHERE `id` = ?";

					$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'ZERO_VALUE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $reqId,   'TYP' => 's');
					$mycms->sql_update($sqlUpdate, false);
				}
			}
		}
	}

	$slipId	 		 	= $mycms->getSession('SLIP_ID');

	$sqlInv 			= array();
	$sqlInv['QUERY'] 	= "  SELECT *, IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount, 
										user.registration_classification_id AS registration_classification_id
								   FROM " . _DB_SLIP_ . " slip 
							 INNER JOIN " . _DB_USER_REGISTRATION_ . " user
									 ON slip.delegate_id = user.id
						LEFT OUTER JOIN ( SELECT SUM(`service_roundoff_price`) AS totalInvoice, `slip_id`
											FROM " . _DB_INVOICE_ . " 
										   WHERE `status` = 'A'
										GROUP BY `slip_id` ) activeInvoiceAmount
									 ON slip.id = activeInvoiceAmount.slip_id
								  WHERE slip.status = ? 
									AND slip.id = ? ";
	$sqlInv['PARAM'][] = array('FILD' => 'slip.status', 'DATA' => 'A',     'TYP' => 's');
	$sqlInv['PARAM'][] = array('FILD' => 'slip.id',     'DATA' => $slipId, 'TYP' => 's');
    // echo "<pre>";
	// print_r($sqlInv);
	// die();
	$resInv = $mycms->sql_select($sqlInv);

	$rowInv 				= $resInv[0];
	$clafId 				= $rowInv['registration_classification_id'];
	$delegateId 			= $rowInv['delegate_id'];
	$activeInvoiceAmount 	= $rowInv['activeInvoiceAmount'];

	$sqlProcessUpdateStep    		   = array();
	$sqlProcessUpdateStep['QUERY']     = " UPDATE " . _DB_PROCESS_STEP_ . "
												  SET `regitration_status` = ?
											    WHERE `id` = ?";

	$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'regitration_status',  'DATA' => 'COMPLETE',                              'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',                  'DATA' => $mycms->getSession('PROCESS_FLOW_ID'),   'TYP' => 's');

	$mycms->sql_update($sqlProcessUpdateStep, false);
	if($reg_type == 'FRONT'){
		$RedirectURL = 'registration.success.php';
	}else if($reg_type == 'BACK'){
		$RedirectURL ='webmaster/only-workshop-registration.php';
	}

	if ($activeInvoiceAmount == 0.00) {
		$payment_mode = $_REQUEST['payment_mode'];
		complementaryPaymentConfirmationProcess($slipId, $delegateId);
		?>
		<center>
			<form action="<?= _BASE_URL_ ?><?= $RedirectURL?>" method="post" name="srchComplementaryFrm">
				<input type="hidden" name="did" value="<?= $mycms->encoded($delegateId) ?>" />
				<h5 align="center">Finalizing<br />Please Wait</h5>
				<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
				<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
				<br />
				<hr />
			</form>
		</center>
		<script type="text/javascript">
			document.srchComplementaryFrm.submit();
		</script>
		<?
		exit();
	} else {

		$sqlSlip             = array();
		$sqlSlip['QUERY']    = "SELECT * FROM " . _DB_SLIP_ . " 
									 WHERE `status` =? 
									   AND `id` =?";
		$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',      'TYP' => 's');
		$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' => $slipId,  'TYP' => 's');

		$resSlip			 = $mycms->sql_select($sqlSlip);
		$rowSlip			 = $resSlip[0];

		$userDetails		 = getUserDetails($rowSlip['delegate_id']);

		if ($mycms->getSession('REGISTRATION_MODE') == "OFFLINE") {
			$offlinePaymentDetails	= $step6Data;

		?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" id="delegate_id" name="user_email_id" value="<?= $userDetails['user_email_id'] ?>" />
					<input type="hidden" name="act" value="setPaymentTerms" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />

					<input type="hidden" name="payment_mode" value="<?= $offlinePaymentDetails['payment_mode'] ?>" />
					<input type="hidden" name="paymentstatusPre" value="<?= $paymentStatusPre ?>" />
					<input type="hidden" name="discountAmount" value="<?= $discountAmount ?>" />
                    <input type="hidden" name="card_number" value="<?= $offlinePaymentDetails['card_number'] ?>" />
					<input type="hidden" name="card_date" value="<?= $offlinePaymentDetails['card_date'] ?>" />

					<input type="hidden" name="cash_deposit_date" value="<?= $offlinePaymentDetails['cash_deposit_date'] ?>" />

					<!-- UPI Payment Option Added By Weavers start -->
					<input type="hidden" name="upi_date" value="<?= $offlinePaymentDetails['upi_date'] ?>" />
					<input type="hidden" name="txn_no" value="<?= $offlinePaymentDetails['txn_no'] ?>" />
					<!-- UPI Payment Option Added By Weavers end -->

					<input type="hidden" name="cheque_number" value="<?= $offlinePaymentDetails['cheque_number'] ?>" />
					<input type="hidden" name="cheque_drawn_bank" value="<?= $offlinePaymentDetails['cheque_drawn_bank'] ?>" />
					<input type="hidden" name="cheque_date" value="<?= $offlinePaymentDetails['cheque_date'] ?>" />

					<input type="hidden" name="draft_number" value="<?= $offlinePaymentDetails['draft_number'] ?>" />
					<input type="hidden" name="draft_drawn_bank" value="<?= $offlinePaymentDetails['draft_drawn_bank'] ?>" />
					<input type="hidden" name="draft_date" value="<?= $offlinePaymentDetails['draft_date'] ?>" />

					<input type="hidden" name="neft_transaction_no" value="<?= $offlinePaymentDetails['neft_transaction_no'] ?>" />
					<input type="hidden" name="neft_bank_name" value="<?= $offlinePaymentDetails['neft_bank_name'] ?>" />
					<input type="hidden" name="neft_date" value="<?= $offlinePaymentDetails['neft_date'] ?>" />
					<input type="hidden" name="neft_document" value="<?= $offlinePaymentDetails['neft_document'] ?>" />


					<input type="hidden" name="rtgs_transaction_no" value="<?= $offlinePaymentDetails['rtgs_transaction_no'] ?>" />
					<input type="hidden" name="rtgs_bank_name" value="<?= $offlinePaymentDetails['rtgs_bank_name'] ?>" />
					<input type="hidden" name="rtgs_date" value="<?= $offlinePaymentDetails['rtgs_date'] ?>" />

					<!-- <h5 align="center">Processing Payment<br />Please Wait</h5>
					<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
					<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
					<br />
					<hr /> -->
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
		<?
			exit();
		} else if ($mycms->getSession('REGISTRATION_MODE') == "ONLINE") {
			$onlinePaymentDetails	= $step6Data;

		?>
		<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" name="act" value="paymentSet" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
					<input type="hidden" name="card_mode" value="<?= $onlinePaymentDetails['card_mode'] ?>" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<!-- <h5 align="center">Processing Payment Mode<br />Please Wait</h5>
					<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
					<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
					<br />
					<hr /> -->
					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
			<!-- <center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" name="act" value="paymentSet" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
					<input type="hidden" name="card_mode" value="<?= $onlinePaymentDetails['card_mode'] ?>" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<h5 align="center">Processing Payment Mode<br />Please Wait</h5>
					<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
					<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
					<br />
					<hr />
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script> -->
		<?
			exit();
		}
	}
	exit();
}
function registerAbstractUser()
{
	global $mycms, $cfg;

	$mycms->setSession('CURRENT_REG_USER', 'FINISH');

	$processFlowId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');

	$step6Data	= getProcessFlowData('step6');
 
	 $registration_Pay_mode = $_REQUEST['registration_Pay_mode'] ?? '';

 	detailsInseringProcess($processFlowId,$registration_Pay_mode);


	$slipId = $mycms->getSession('SLIP_ID');

	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT *, IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount, 
											user.registration_classification_id AS registration_classification_id
									   FROM " . _DB_SLIP_ . " slip 
								 INNER JOIN " . _DB_USER_REGISTRATION_ . " user
										 ON slip.delegate_id = user.id
							LEFT OUTER JOIN ( SELECT SUM(`service_roundoff_price`) AS totalInvoice, `slip_id`
												FROM " . _DB_INVOICE_ . " 
											   WHERE `status` = 'A'
											GROUP BY `slip_id` ) activeInvoiceAmount
										 ON slip.id = activeInvoiceAmount.slip_id
									  WHERE slip.status = ? 
										AND slip.id =?";

	$sqlWorkshopclsf['PARAM'][]  = array('FILD' => 'slip.status', 'DATA' => 'A',     'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]  = array('FILD' => 'slip.id',     'DATA' => $slipId, 'TYP' => 's');
	$resWorkshopclsf 			 = $mycms->sql_select($sqlWorkshopclsf);

	$resWorkshopclsfres 		 = $resWorkshopclsf[0];
	$clafId 					 = $resWorkshopclsfres['registration_classification_id'];
	$delegateId 				 = $resWorkshopclsfres['delegate_id'];
	$activeInvoiceAmount 		 = $resWorkshopclsfres['activeInvoiceAmount'];
	$reg_type 		             = $resWorkshopclsfres['reg_type'];
	$payment_status 		     = $resWorkshopclsfres['payment_status'];

    if($reg_type == 'FRONT'){
		$RedirectURL = 'registration.success.php';
	}else{
		$RedirectURL ='webmaster/registration.php';
	}
	$sqlProcessUpdateStep    			= array();
	$sqlProcessUpdateStep['QUERY']      = "  UPDATE " . _DB_PROCESS_STEP_ . "
													SET `regitration_status` = ?
												  WHERE `id` = ?";
	$sqlProcessUpdateStep['PARAM'][]    = array('FILD' => 'regitration_status',  'DATA' => 'COMPLETE',                              'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][]    = array('FILD' => 'id',                  'DATA' => $mycms->getSession('PROCESS_FLOW_ID'),   'TYP' => 's');
	$mycms->sql_update($sqlProcessUpdateStep, false);
 
	if ($activeInvoiceAmount == 0.00 || $_REQUEST['registration_Pay_mode']=='COMPLIMENTARY' || $_REQUEST['registration_Pay_mode']=='ZERO_VALUE') {
		$payment_mode = $_REQUEST['payment_mode'];
		complementaryPaymentConfirmationProcess($slipId, $delegateId);
		?>
		<center>
			<form action="<?= _BASE_URL_ ?><?=$RedirectURL?>" method="post" name="srchComplementaryFrm">
				<input type="hidden" name="did" value="<?= $mycms->encoded($delegateId) ?>" />
				<style>
				.payment_loading {
					width: 100%;
					height: 100vh;
					position: fixed;
					background: #fff3e4;
					z-index: 2;
					/* display: none; */
				}
				
				.payment_loading_inner {
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 100%;
					height: 100%;
				}
				
				.payment_loading video {
				
					width: 150px;
				}
				
				.payment_loading h4 {
					text-transform: uppercase;
					letter-spacing: 8px;
					color: #560600;
					font-size: 23px;
					font-weight: 500;
				}
				
				.payment_loading p {
					margin: 0;
					width: 40%;
					text-align: center;
					font-size: 13px;
					color: #7e0900;
				}
			</style>
			<div class="payment_loading">
				<div class="payment_loading_inner">
					<video src="images/Cube.webm" autoplay inline muted loop></video>
					<h4 align="center">Finalizing<br />Please Wait</h4>
							<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
				</div>
			</div>
			</form>
		</center>
		<script type="text/javascript">
			document.srchComplementaryFrm.submit();
		</script>
		<?
		exit();
	} else {

		$sqlSlip             = array();
		$sqlSlip['QUERY']    = "SELECT * FROM " . _DB_SLIP_ . " 
									 WHERE `status` =? 
									   AND `id` =?";
		$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',      'TYP' => 's');
		$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' => $slipId,  'TYP' => 's');

		$resSlip			 = $mycms->sql_select($sqlSlip);
		$rowSlip			 = $resSlip[0];

		$userDetails		 = getUserDetails($rowSlip['delegate_id']);

		if ($mycms->getSession('REGISTRATION_MODE') == "OFFLINE") {
			$offlinePaymentDetails	= $step6Data;

		?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" id="delegate_id" name="user_email_id" value="<?= $userDetails['user_email_id'] ?>" />
					<input type="hidden" name="act" value="setPaymentTerms" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />

					<input type="hidden" name="payment_mode" value="<?= $offlinePaymentDetails['payment_mode'] ?>" />
                   <input type="hidden" name="card_number" value="<?= $offlinePaymentDetails['card_number'] ?>" />
					<input type="hidden" name="card_date" value="<?= $offlinePaymentDetails['card_date'] ?>" />
					<input type="hidden" name="paymentstatusPre" value="<?=$payment_status ?>" />
					<input type="hidden" name="discountAmount" value="<?= $offlinePaymentDetails['discountAmount'] ?>" />

					<input type="hidden" name="cash_deposit_date" value="<?= $offlinePaymentDetails['cash_deposit_date'] ?>" />
					<!-- UPI Payment Option Added By Weavers start -->
					<input type="hidden" name="upi_date" value="<?= $offlinePaymentDetails['upi_date'] ?>" />
					<input type="hidden" name="txn_no" value="<?= $offlinePaymentDetails['txn_no'] ?>" />
					<!-- UPI Payment Option Added By Weavers end -->
					<input type="hidden" name="cheque_number" value="<?= $offlinePaymentDetails['cheque_number'] ?>" />
					<input type="hidden" name="cheque_drawn_bank" value="<?= $offlinePaymentDetails['cheque_drawn_bank'] ?>" />
					<input type="hidden" name="cheque_date" value="<?= $offlinePaymentDetails['cheque_date'] ?>" />

					<input type="hidden" name="draft_number" value="<?= $offlinePaymentDetails['draft_number'] ?>" />
					<input type="hidden" name="draft_drawn_bank" value="<?= $offlinePaymentDetails['draft_drawn_bank'] ?>" />
					<input type="hidden" name="draft_date" value="<?= $offlinePaymentDetails['draft_date'] ?>" />

					<input type="hidden" name="neft_transaction_no" value="<?= $offlinePaymentDetails['neft_transaction_no'] ?>" />
					<input type="hidden" name="neft_bank_name" value="<?= $offlinePaymentDetails['neft_bank_name'] ?>" />
					<input type="hidden" name="neft_date" value="<?= $offlinePaymentDetails['neft_date'] ?>" />
					<input type="hidden" name="neft_document" value="<?= $offlinePaymentDetails['neft_document'] ?>" />

					<input type="hidden" name="rtgs_transaction_no" value="<?= $offlinePaymentDetails['rtgs_transaction_no'] ?>" />
					<input type="hidden" name="rtgs_bank_name" value="<?= $offlinePaymentDetails['rtgs_bank_name'] ?>" />
					<input type="hidden" name="rtgs_date" value="<?= $offlinePaymentDetails['rtgs_date'] ?>" />

					<!-- <h5 align="center">Processing Payment<br />Please Wait</h5>
					<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
					<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
					<br />
					<hr /> -->
					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
		<?
			exit();
		} else if ($mycms->getSession('REGISTRATION_MODE') == "ONLINE") {
			$onlinePaymentDetails	= $step6Data;

		?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowSlip['delegate_id'] ?>" />
					<input type="hidden" name="act" value="paymentSet" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
					<input type="hidden" name="card_mode" value="<?= $onlinePaymentDetails['card_mode'] ?>" />
					<input type="hidden" name="mode" value="<?= $rowSlip['invoice_mode'] ?>" />
					<!-- <h5 align="center">Processing Payment Mode<br />Please Wait</h5>
					<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
					<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
					<br />
					<hr /> -->
					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
	<?
			exit();
		}
	}
}

function paymentSet()
{

	global $mycms, $cfg;
	// echo 'card_mode='. $_REQUEST['card_mode'];
	// 	echo 'slip_id='. $_REQUEST['slip_id'];
	// 	echo 'delegate_id='. $_REQUEST['delegate_id'];
	// 	die();
	?>
	<center>
		<form action="<?= _BASE_URL_ ?>razorpay_payment_do.php" method="post" name="srchAtomFrm">
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $_REQUEST['delegate_id'] ?>" />
			<input type="hidden" id="slip_id" name="slip_id" value="<?= $_REQUEST['slip_id'] ?>" />
			<input type="hidden" id="card_mode" name="card_mode" value="<?= $_REQUEST['card_mode'] ?>" />
				<style>
					.payment_loading {
						width: 100%;
						height: 100vh;
						position: fixed;
						background: #fff3e4;
						z-index: 2;
						/* display: none; */
					}
					
					.payment_loading_inner {
						display: flex;
						flex-direction: column;
						align-items: center;
						justify-content: center;
						width: 100%;
						height: 100%;
					}
					
					.payment_loading video {
					
						width: 150px;
					}
					
					.payment_loading h4 {
						text-transform: uppercase;
						letter-spacing: 8px;
						color: #560600;
						font-size: 23px;
						font-weight: 500;
					}
					
					.payment_loading p {
						margin: 0;
						width: 40%;
						text-align: center;
						font-size: 13px;
						color: #7e0900;
					}
				</style>
				<div class="payment_loading">
					<div class="payment_loading_inner">
						<video src="images/Cube.webm" autoplay inline muted loop></video>
						<h4>Redirectiong to Payment Gateway.Please Wait..</h4>
								<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
					</div>
				</div>
		</form>
	</center>
	<script type="text/javascript">
		document.srchAtomFrm.submit();
	</script>
<?
	exit();
}
function combinedEditRegistrationProcess()
{
	global $mycms, $cfg;

	$delegateId = $_REQUEST['delegate_id'];

    // echo '<pre>'; print_r($_REQUEST);die;
	if($_REQUEST['paymentValue']=='makePay'){
		multiPaymentConfirmation();
	}
	// $user_titles = addslashes(trim(strtoupper($_REQUEST['user_title'])));
	// if ($user_titles != '') {
	// 	$user_title = $user_titles;
	// } else {
	// 	$user_title = $user_titles;
	// }
	// $user_first_name = addslashes(trim(strtoupper($_REQUEST['user_first_name'])));
	// $user_middle_name = addslashes(trim(strtoupper($_REQUEST['user_middle_name'])));
	// $user_last_name = addslashes(trim(strtoupper($_REQUEST['user_last_name'])));
	// $user_full_name = $user_title . " " . $user_first_name . " " . $user_middle_name . " " . $user_last_name;

	// $user_email_id = addslashes(trim(strtoupper($_REQUEST['user_email_id'])));
	// $user_usd_code = addslashes(trim(strtoupper($_REQUEST['user_usd_code'])));
	// $user_mobile = addslashes(trim(strtoupper($_REQUEST['user_mobile'])));
	// $user_institute_name = addslashes(trim(strtoupper($_REQUEST['user_institute_name'])));
	// $user_address = addslashes(trim(strtoupper($_REQUEST['user_address'])));
	// $user_country = addslashes(trim(($_REQUEST['user_country'] == "") ? 0 : $_REQUEST['user_country']));
	// $user_state = addslashes(trim(($_REQUEST['user_state'] == "") ? 0 : $_REQUEST['user_state']));
	// $user_city = addslashes(trim(strtoupper($_REQUEST['user_city'])));
	// $user_postal_code = addslashes(trim(strtoupper($_REQUEST['user_postal_code'])));
	// $user_food_preference = addslashes(trim(strtoupper($_REQUEST['user_food_preference'])));


	// $sqlUpdate = [];
	// $sqlUpdate['QUERY'] = "
    //     UPDATE " . _DB_USER_REGISTRATION_ . "
    //     SET user_title = ?,
    //         user_first_name = ?,
    //         user_middle_name = ?,
    //         user_last_name = ?,
    //         user_full_name = ?,
	// 		user_email_id = ?,
	// 		user_mobile_isd_code = ?,
	// 		user_mobile_no = ?,
	// 		user_institute_name = ?,
    //         user_address = ?,
    //         user_country_id = ?,
    //         user_state_id = ?,
    //         user_city = ?,
    //         user_pincode = ?,
    //         user_food_preference = ?,
    //         modified_ip = ?,
    //         modified_sessionId = ?,
    //         modified_browser = ?,
    //         modified_dateTime = ?
    //     WHERE id = ?
    // ";

	// $sqlUpdate['PARAM'] = [
	// 	['FILD' => 'user_title', 'DATA' => $user_title, 'TYP' => 's'],
	// 	['FILD' => 'user_first_name', 'DATA' => $user_first_name, 'TYP' => 's'],
	// 	['FILD' => 'user_middle_name', 'DATA' => $user_middle_name, 'TYP' => 's'],
	// 	['FILD' => 'user_last_name', 'DATA' => $user_last_name, 'TYP' => 's'],
	// 	['FILD' => 'user_full_name', 'DATA' => $user_full_name, 'TYP' => 's'],
	// 	['FILD' => 'user_email_id', 'DATA' => $user_email_id, 'TYP' => 's'],
	// 	['FILD' => 'user_mobile_isd_code', 'DATA' => $user_usd_code, 'TYP' => 's'],
	// 	['FILD' => 'user_mobile_no', 'DATA' => $user_mobile, 'TYP' => 's'],
	// 	['FILD' => 'user_institute_name', 'DATA' => $user_institute_name, 'TYP' => 's'],
	// 	['FILD' => 'user_address', 'DATA' => $user_address, 'TYP' => 's'],
	// 	['FILD' => 'user_country_id', 'DATA' => $user_country, 'TYP' => 'i'],
	// 	['FILD' => 'user_state_id', 'DATA' => $user_state, 'TYP' => 'i'],
	// 	['FILD' => 'user_city', 'DATA' => $user_city, 'TYP' => 's'],
	// 	['FILD' => 'user_pincode', 'DATA' => $user_postal_code, 'TYP' => 's'],
	// 	['FILD' => 'user_food_preference', 'DATA' => $user_food_preference, 'TYP' => 's'],
	// 	['FILD' => 'modified_ip', 'DATA' => ($_SERVER['REMOTE_ADDR'] ?? ''), 'TYP' => 's'],
	// 	['FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's'],
	// 	['FILD' => 'modified_browser', 'DATA' => ($_SERVER['HTTP_USER_AGENT'] ?? ''), 'TYP' => 's'],
	// 	['FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'],
	// 	['FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 'i'],
	// ];

	// $mycms->sql_update($sqlUpdate);


}
function multiPaymentConfirmation()
{
	global $cfg, $mycms;

	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];
	$userREGtype = $_REQUEST['userREGtype'];
   
	$slipDetails = slipDetails($slipId);
	$paymentDetails = getPaymentDetails($paymentId);

	$paymentModeDisplay = ($paymentDetails['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $paymentDetails['payment_mode']);
   // Prepare POST data
    $_POST = [
        'act' => 'make_partial_payment',
        'redirect' => 'N',
        'delegateId' => $delegateId,
        'slipId' => $slipId,
        'paymentId' => $paymentId,
        'userREGtype' => $userREGtype,
        'exhibitorCode' => $paymentDetails['exhibitor_code'],
        'amount' => $paymentDetails['amount'],
        'payment_date' => date('Y-m-d'),
        'remarks' => '', // optional
    ];
		make_partial_payment($mycms, $cfg);

    // Directly include the processing script (simulate form submission)
    exit(); // stop further execution
	?>
	
	<?
}
function make_partial_payment($mycms, $cfg, $redirect = true)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $_REQUEST['paymentId'];
	$spotUser = $_REQUEST['userREGtype'];
	$slipId = $_REQUEST['slipId'];
	$delegateId = $_REQUEST['delegateId'];

	//  echo '<pre>'; print_r($_REQUEST); die;

	$loggedUserId = $mycms->getLoggedUserId();
	partialPaymentConfirmationProcess($redirect);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
									SET `collected_by` = ?
								  WHERE `id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'collected_by', 'DATA' => $loggedUserId, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	if ($redirect) {
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=invoice&id=" . $delegateId);
		exit();
	}
}
function partialPaymentConfirmationProcess($sendmail = true)
{
	global $cfg, $mycms;

	//echo '<pre>'; print_r($_REQUEST); die;	

	$paymentId = $_REQUEST['paymentId'];
	$slipId = $_REQUEST['slipId'];
	$paymentRemark = $_REQUEST['remarks'];
	$paymentDate = $_REQUEST['payment_date'];
	$delegateId = $_REQUEST['delegateId'];
	$exhibitorCode = $_REQUEST['exhibitorCode'];
	$exhibitorName = $_REQUEST['exhibitorName'];
	if ($_REQUEST['amount'] != '') {
		$amount = $_REQUEST['amount'];
	} else {
		$amount = invoiceAmountOfSlip($slipId);
	}

	//echo "<pre> slipId "; print_r($slipId); echo "</pre>";

	$paidAmmount = invoiceAmountOfSlip($slipId);

	//echo "<pre> paidAmmount "; print_r($paidAmmount); echo "</pre>";

	$sqlDelegateInfo = array();
	$sqlDelegateInfo['QUERY'] = "SELECT `user_type`,`user_full_name`,isCombo 
										 FROM " . _DB_USER_REGISTRATION_ . " 
										WHERE `id` = ? ";
	$sqlDelegateInfo['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$delegateInfo = $mycms->sql_select($sqlDelegateInfo, false);

	//print_r($delegateInfo);	

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
											SET `payment_date` = ?,
												`payment_remark` = ?,
												`amount` = ?,
												`payment_status` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'payment_date', 'DATA' => $paymentDate, 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'payment_remark', 'DATA' => $paymentRemark, 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'amount', 'DATA' => $amount, 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdatePayment, false);

	//echo "<pre>"; print_r($sqlUpdatePayment); echo "</pre>";

	//////////////GET ALL PAID AMOUNT AGINST SLIP ///////////////////
	$totalAmount = getTotalPaidAmount($slipId);

	//echo "<pre> totalAmount "; print_r($totalAmount); echo "</pre>"; die;

	if ($totalAmount >= $paidAmmount) {
		$sqlUpdateSlip = array();
		$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
												SET `payment_status` = ?
											  WHERE `id` = ?";
		$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
		$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $slipId, 'TYP' => 's');
		$mycms->sql_update($sqlUpdateSlip, false);

		//echo "<pre>"; print_r($sqlUpdateSlip); echo "</pre>";

		// if($exhibitorCode)
		// {
		// 	$sqlInsertExhibitorUser['QUERY']		= "INSERT INTO "._DB_EXIBITOR_COMPANY_USERS_."
		// 										SET `exhibitor_company_code` = '".$exhibitorCode."',
		// 											`exhibitor_company_name` = '".$exhibitorName."',
		// 															`amount` = '".$paidAmmount."',
		// 													   `delegate_id` = '".$delegateId."',
		// 													    `payment_id` = '".$paymentId."',
		// 													`payment_status` = 'PAID',
		// 															`status` = 'A', 
		// 														 `user_type` = '".$delegateInfo[0]['user_type']."',
		// 													`user_full_name` = '".$delegateInfo[0]['user_full_name']."',
		// 													   `slip_number` = '".$slipId."',
		// 													`invoice_number` = '".$valActiveInvoice['invoice_number']."' ";

		// 	$exhibitorUserLastId= $mycms->sql_insert($sqlInsertExhibitorUser, false);

		// 	//echo $exhibitorUserLastId;die();

		// }
		$activeInvoice = invoiceDetailsActiveOfSlip($slipId);

		$isConference = "NO";
		$isResidential = "NO";

		$isOnlyWorkshop = "NO";
		$isOnlyAccompany = "NO";
		$isOnlyAccommodation = "NO";
		$isOnlyDinner = "NO";

		$i = 0;
		foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {


			if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
				$isConference = "YES";
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = ?
													  WHERE `id` = ?
													  AND	`registration_payment_status` = ?";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

				if ($delegateInfo[0]['isCombo'] == 'Y') {

					$sqlUpdateWorkshop = array();
					$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = ?
													  WHERE `delegate_id` = ?
														AND `status`=?";
					$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
					$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
					$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
					$mycms->sql_update($sqlUpdateWorkshop, false);

					$sqlUpdateAcco = array();
					$sqlUpdateAcco['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														SET `payment_status` = ?
													  WHERE `user_id` = ?
														AND `status`=?";
					$sqlUpdateAcco['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
					$sqlUpdateAcco['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');
					$sqlUpdateAcco['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
					$mycms->sql_update($sqlUpdateAcco, false);
				}
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
				$isResidential = "YES";
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = ?
													  WHERE `id` = ?";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = ?
													  WHERE `delegate_id` = ?
														AND `status`=?";
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $valActiveInvoice['delegate_id'], 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateWorkshop, false);

				$sqlUpdateAccom = array();
				$sqlUpdateAccom['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
															SET `payment_status` = ?
														  WHERE `user_id` = ?
															AND `status`=?";
				$sqlUpdateAccom['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateAccom['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $valActiveInvoice['delegate_id'], 'TYP' => 's');
				$sqlUpdateAccom['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateAccom, false);
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
				$isOnlyWorkshop = "YES";
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?
													  AND	`payment_status` = ?";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdate = array();
				$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `workshop_payment_status` = ?
													  WHERE `id` = ?
														AND	`workshop_payment_status` = ?";
				$sqlUpdate['PARAM'][] = array('FILD' => 'workshop_payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['delegate_id'], 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'workshop_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdate, false);
			}

			if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
				$isOnlyAccompany = 'YES';
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = ?
													  WHERE `id` = ?
														AND	`registration_payment_status` = ?";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
				$isOnlyAccommodation = 'YES';
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?
														AND	`payment_status` = ?";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdate = array();
				$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `accommodation_payment_status` = ?
											  WHERE `id` = ?
											   AND	`accommodation_payment_status` = ?";
				$sqlUpdate['PARAM'][] = array('FILD' => 'accommodation_payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['delegate_id'], 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'accommodation_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdate, false);
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_DINNER_REQUEST') {
				$isOnlyDinner = 'YES';
				$sqlUpdateRequest = array();
				$sqlUpdateRequest['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?
													   AND	`payment_status` = ?";

				$sqlUpdateRequest['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateRequest['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$sqlUpdateRequest['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$mycms->sql_update($sqlUpdateRequest, false);
			}

			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
											SET `payment_status` = ?
										  WHERE `id` = ?
											AND	`payment_status` = ?";
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['id'], 'TYP' => 's');
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
			$mycms->sql_update($sqlUpdateSlip, false);

			$invoiceNumber[$i] = $valActiveInvoice['id'];
			$i++;
		}
		$finalInvoiceNumber = implode(",", $invoiceNumber);
		// $sqlUpdateExhibitorUser 	  = array();
		// $sqlUpdateExhibitorUser['QUERY']		= "UPDATE "._DB_EXIBITOR_COMPANY_USERS_."
		// 										SET `invoice_number` = ?
		// 										WHERE `id` = ? ";

		// $sqlUpdateExhibitorUser['PARAM'][]     = array('FILD' => 'invoice_number',  	'DATA' =>$finalInvoiceNumber, 	'TYP' => 's');													
		// $sqlUpdateExhibitorUser['PARAM'][]     = array('FILD' => 'id',  				'DATA' =>$exhibitorUserLastId, 	'TYP' => 's');													
		// $mycms->sql_update($sqlUpdateExhibitorUser, false);

		if ($sendmail) {
			$userRegDetails = getUserDetails($delegateId);

			//offline_conference_payment_confirmation_message($delegateId,$slipId , $paymentId, 'SEND');

			if ($isConference == 'YES') {
				$isResidential = "NO";
				$isOnlyWorkshop = "NO";
				$isOnlyAccompany = "NO";
				$isOnlyAccommodation = "NO";
				$isOnlyDinner = "NO";
				offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
			}
			if ($isResidential == 'YES') {
				$isConference = "NO";
				$isOnlyWorkshop = "NO";
				$isOnlyAccompany = "NO";
				$isOnlyAccommodation = "NO";
				$isOnlyDinner = "NO";
				offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
			} elseif ($isOnlyAccommodation == 'YES') {
				$isConference = "NO";
				$isResidential = "NO";
				$isOnlyWorkshop = "NO";
				$isOnlyAccompany = "NO";
				$isOnlyDinner = "NO";
				offline_accommodation_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
			} elseif ($isOnlyAccompany == 'YES') {
				$isConference = "NO";
				$isResidential = "NO";
				$isOnlyWorkshop = "NO";
				$isOnlyAccommodation = "NO";
				$isOnlyDinner = "NO";

				offline_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, 'SEND');
			} elseif ($isOnlyDinner == 'YES') {
				$isConference = "NO";
				$isResidential = "NO";
				$isOnlyWorkshop = "NO";
				$isOnlyAccompany = "NO";
				$isOnlyAccommodation = "NO";

				offline_dinner_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
			} elseif ($isOnlyWorkshop == 'YES') {
				$isConference = "NO";
				$isResidential = "NO";
				$isOnlyAccompany = "NO";
				$isOnlyAccommodation = "NO";
				$isOnlyDinner = "NO";

				offline_conference_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, 'SEND');
			}
		}
	}
}
function setPaymentTerms()
{
	global $mycms, $cfg;
	// echo $mycms->getSession('SLIP_ID');
	// echo 1212;
	// die;
	$paymentId = insertingPaymentDetails();

	if (isset($_FILES['cash_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['cash_document']['tmp_name'];
		// echo $tempFile;die;
		$cashFileName = "CASH_DOC_" . date('ymdHis') . "_" . $_FILES['cash_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];

		move_uploaded_file($tempFile, $uploadDir . $cashFileName);


		$sqlProcessUpdateStep = array();
		$sqlProcessUpdateStep['QUERY']       = "UPDATE  " . _DB_PAYMENT_ . "
													   SET `cash_document` 		= ?
													 WHERE `id` = ?";
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'cash_document',          'DATA' => $cashFileName,     				 		'TYP' => 's');
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' => $paymentId,	'TYP' => 's');
		$mycms->sql_update($sqlProcessUpdateStep, false);
	}
	if (isset($_FILES['neft_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['neft_document']['tmp_name'];
		// echo $tempFile;die;
		$neftFileName = "NEFT_DOC_" . date('ymdHis') . "_" . $_FILES['neft_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];

		move_uploaded_file($tempFile, $uploadDir . $neftFileName);
		$sqlProcessUpdateStep = array();
		$sqlProcessUpdateStep['QUERY']       = "UPDATE  " . _DB_PAYMENT_ . "
													   SET `neft_document` 		= ?
													 WHERE `id` = ?";
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'neft_document',          'DATA' => $neftFileName,     				 		'TYP' => 's');
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' => $paymentId,	'TYP' => 's');
		$mycms->sql_update($sqlProcessUpdateStep, false);
	}

	$mycms->setSession('TEMP_SLIP_ID', $mycms->getSession('SLIP_ID'));
	$mycms->removeSession('REGISTRATION_MODE');
	$mycms->removeSession('REGISTRATION_TOKEN');
	$mycms->removeSession('CURRENCY');

	$userMailId = $_REQUEST['user_email_id'];
	$userId = base64_encode($userMailId);
	$mycms->removeSession('COMMUNICATION_EMAIL');

     $RedirectURL    = $_REQUEST['RedirectURL'];
	 if($RedirectURL==''){
		$RedirectURL ='registration.success.php';
	 }
?>
	<center>
		<form action="<?= _BASE_URL_ ?><?=$RedirectURL?>" method="post" name="srchOfflinFrm">
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $_REQUEST['delegate_id'] ?>" />
			<input type="hidden" id="slip_id" name="slip_id" value="<?= $_REQUEST['slip_id'] ?>" />
			<input type="hidden" id="email" name="email" value="<?= $userId ?>" />
			
			<style>
				.payment_loading {
					width: 100%;
					height: 100vh;
					position: fixed;
					background: #fff3e4;
					z-index: 2;
					/* display: none; */
				}
				
				.payment_loading_inner {
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 100%;
					height: 100%;
				}
				
				.payment_loading video {
				
					width: 150px;
				}
				
				.payment_loading h4 {
					text-transform: uppercase;
					letter-spacing: 8px;
					color: #560600;
					font-size: 23px;
					font-weight: 500;
				}
				
				.payment_loading p {
					margin: 0;
					width: 40%;
					text-align: center;
					font-size: 13px;
					color: #7e0900;
				}
			</style>
			<div class="payment_loading">
				<div class="payment_loading_inner">
					<video src="images/Cube.webm" autoplay inline muted loop></video>
					<h4>Payment Processing</h4>
							<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
				</div>
			</div>
		</form>
	</center>
	<script type="text/javascript">
		document.srchOfflinFrm.submit();
	</script>
	<?
	exit();
}

function insertIntoProcessFlow($stepName, $requestValues)
{
	global $mycms, $cfg;

	$sessionId	    = session_id();
	$userIp		    = $_SERVER['REMOTE_ADDR'];
	$userBrowser    = $_SERVER['HTTP_USER_AGENT'];

	if ($mycms->getSession('PROCESS_FLOW_ID_FRONT') == "") {
		$sqlProcessInsertStep = array();
		$sqlProcessInsertStep['QUERY']	  = "INSERT INTO " . _DB_PROCESS_STEP_ . "  
												  SET `" . $stepName . "` 	    = ?,
													  `created_ip`          = ?,
													  `reg_area`            = ?,
													  `created_sessionId`   = ?,
													  `created_browser`     = ?,
													  `created_dateTime`    = ?";

		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => $stepName,           'DATA' => serialize($requestValues),    'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'reg_area',          'DATA' => 'FRONT',                      'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',   'DATA' => $userBrowser,                 'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

		$id = $mycms->sql_insert($sqlProcessInsertStep, false);
		$mycms->setSession('PROCESS_FLOW_ID_FRONT', $id);
		$mycms->setSession('REGISTRATION_TOKEN', "#" . $mycms->getSession('PROCESS_FLOW_ID_FRONT'));
	} else {
		$sql  			= array();
		$sql['QUERY'] 	= "SELECT `" . $stepName . "` AS theData 
								 FROM " . _DB_PROCESS_STEP_ . "
								WHERE `id` = ?";
		$sql['PARAM'][] = array('FILD' => 'id', 	'DATA' => $mycms->getSession('PROCESS_FLOW_ID_FRONT'),	'TYP' => 's');
		$result       	= $mycms->sql_select($sql);
		$row			= $result[0];

		if (trim($row['theData']) != '') {
			$theData =  unserialize(trim($row['theData']));
		} else {
			$theData =  array();
		}

		foreach ($requestValues as $key => $value) {
			$theData[$key] = $value;
		}

		$sqlProcessUpdateStep = array();
		$sqlProcessUpdateStep['QUERY']       = "UPDATE  " . _DB_PROCESS_STEP_ . "
													   SET `" . $stepName . "` 		= ?,
														   `created_dateTime`   = ?
													 WHERE `id` = ?";
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => $stepName,          'DATA' => serialize($theData),     				 		'TYP' => 's');
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'created_dateTime', 'DATA' => date('Y-m-d H:i:s'), 							'TYP' => 's');
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' => $mycms->getSession('PROCESS_FLOW_ID_FRONT'),	'TYP' => 's');
		$mycms->sql_update($sqlProcessUpdateStep, false);
	}
}

function getProcessFlowData($stepName)
{
	global $mycms, $cfg;

	$processFlowId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');

	if ($processFlowId != '') {
		$sqlProcessFlow   = array();
		$sqlProcessFlow['QUERY']	= "SELECT * FROM " . _DB_PROCESS_STEP_ . " 
											WHERE `id` = ?";
		$sqlProcessFlow['PARAM'][]  = array('FILD' => 'id',   'DATA' => $processFlowId,  'TYP' => 's');
		$resProcessFlow				= $mycms->sql_select($sqlProcessFlow);
		$rowProcessFlow				= $resProcessFlow[0];

		return unserialize($rowProcessFlow[$stepName]);
	} else {
		return array("ERROR", "Process Flow Id not set");
	}
}


// Accommodation details setup by weavers start
function setupAccommodationDetails()
{
	global $mycms, $cfg;
        // echo "<pre>";
		//  print_r($_REQUEST);
		//  die();
	global $cfg, $mycms;
   //    echo '<pre>'; print_r($_REQUEST);die;
	// $loggedUserID = $mycms->getLoggedUserId();
	$delegateId = $_REQUEST['delegate_id'];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	$rowUserDetails = getUserDetails($delegateId);
	$registrationRequest   = $_REQUEST['registrationRequest'];
	$registrationMode	   = $_REQUEST['registrationMode'];

	// $currentCutoffId = getTariffCutoffId();
	if($_REQUEST['registration_accompany_cutoff']!=""){
		$currentCutoffId = $_REQUEST['registration_accompany_cutoff'];
	}else{
		$currentCutoffId =getUserCutoffId($delegateId);
	}

	if(!empty($_REQUEST['package_id']))
	{
        foreach($_REQUEST['package_id'] as $key =>$package_id)
		{
			$slip_id = insertingSlipDetails($delegateId, $_REQUEST['registrationMode'], 'GENERAL');

			$sqlPackageCheckoutDate1  =  array();
			// query in tariff table
			$sqlPackageCheckoutDate1['QUERY'] = "select * 
														FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation
														WHERE status = ?
														AND id = ?";
			$sqlPackageCheckoutDate1['PARAM'][]  =  array('FILD' => 'status',       'DATA' => 'A',           'TYP' => 's');
			$sqlPackageCheckoutDate1['PARAM'][]  =  array('FILD' => 'id',     'DATA' => $package_id,     'TYP' => 's');
		
			// die();
			$resPackageCheckoutDate1 = $mycms->sql_select($sqlPackageCheckoutDate1);
			
			$totalRoomVal = $userDetails['accommodation_room'];
			if($totalRoomVal==""){
				$totalRoomVal = 0;
			}
		
			$accommodation_hotel_id           = $resPackageCheckoutDate1[0]['hotel_id'];
			$explodeCheckin = explode('/',$_REQUEST['hotel_dates'][$accommodation_hotel_id]['checkin']);
			$explodeCheckout = explode('/',$_REQUEST['hotel_dates'][$accommodation_hotel_id]['checkout']);
 
			$check_in_date_id                 = $explodeCheckin[0];
			$check_out_date_id                = $explodeCheckout[0];
			$totalRoom = 0;
			$totalGuestCounter                 = 0;

			
			$sqlAccommodationDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
											WHERE `id` = '".$check_in_date_id."'";
															
			$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate); 
			$rowAccommodationDate           = $resultAccommodationDate[0];
			
			$check_in_date              = $rowAccommodationDate['check_in_date'];
			
			// GET ACCOMMODATION OUT DATE
			$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_."
													WHERE `id` = '".$check_out_date_id."'";
																	
			$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
			$rowAccommodationOutDate           = $resultAccommodationOutDate[0];
			
			$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];
			
				
			$sqlFetchHotel['QUERY']    = "SELECT id ,package_name
								FROM "._DB_PACKAGE_ACCOMMODATION_."  
								WHERE  `id` = '".$resPackageCheckoutDate1[0]['package_id']."'
									AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'
									
			$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
			$resultfetch 	  = $resultFetchHotel[0];
			$packageId 	      = $resultfetch['id'];
			if($resPackageCheckoutDate1[0]['package_id']==''){
				$respackage_id = '0';
			}else{
				$respackage_id =$resPackageCheckoutDate1[0]['package_id'];
			}
			$accTariffId = getAccommodationTariffId($respackage_id,$resPackageCheckoutDate1[0]['roomTypeId'],$check_in_date_id,$check_out_date_id,$currentCutoffId);
					

			$accomodationDetails['user_id']											 = $delegateId;
			$accomodationDetails['accommodation_details']							 = $_REQUEST['accmName'];
			$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
			//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
			$accomodationDetails['package_id']										 = $resPackageCheckoutDate1[0]['package_id'];
			$accomodationDetails['tariff_ref_id']								     = $accTariffId;
			$accomodationDetails['tariff_cutoff_id']								 = $currentCutoffId;
			$accomodationDetails['checkin_date']									 = $explodeCheckin[1];
			$accomodationDetails['checkout_date']									 = $explodeCheckout[1];
			if($resultfetch['package_name']=="Sharing"){
			$accomodationDetails['booking_quantity']                                 = 1;
			$accomodationDetails['booking_quantity_insert']								 = 0.5;
			}else{
			$accomodationDetails['booking_quantity']								 = $_REQUEST['accmomdation-qty'][$accommodation_hotel_id][$resPackageCheckoutDate1[0]['roomTypeId']];
			$accomodationDetails['booking_quantity_insert']						     = $_REQUEST['accmomdation-qty'][$accommodation_hotel_id][$resPackageCheckoutDate1[0]['roomTypeId']];
			}
			$accomodationDetails['refference_invoice_id']							 = 0;
			$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
			$accomodationDetails['booking_mode']									 = $registrationMode;
			$accomodationDetails['payment_status']									 = 'UNPAID';
			$accomodationDetails['roomTypeId']									 = $resPackageCheckoutDate1[0]['roomTypeId'];
          
			$accompReqId	 = insertingAccomodationDetails($accomodationDetails);

			$accommRoomDetails['user_id']										 = $delegateId;
			$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
			$accommRoomDetails['room_id']										 = 1;
			$accommRoomDetails['checkin_id']								     = $check_in_date_id;
			$accommRoomDetails['checkout_id']								     = $check_out_date_id;
			$accommRoomDetails['checkin_date']								     = $check_in_date;
			$accommRoomDetails['checkout_date']									 = $check_out_date;

					
			$accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);
			
			$invoiceIdConf = insertingInvoiceDetails($accompReqId,'ACCOMMODATION',$_REQUEST['registration_request'],'','',$_REQUEST['paymentstatus']);
            if($_REQUEST['invoice_generation_mode_edit'] == "COMPLIMENTARY")
			{
				zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMMODATION', $slip_id);
			}
			else if($_REQUEST['invoice_generation_mode_edit'] == "ZERO_VALUE")
			{
				zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMMODATION', $slip_id);
			}
			
		

			$sqlProcessUpdateRoom['QUERY']  = " UPDATE  "._DB_USER_REGISTRATION_."
													SET `accommodation_room` = '".$totalRoomVal."'
													WHERE `id` = '".$delegateId."' AND status='A'";													 
			$mycms->sql_update($sqlProcessUpdateRoom, false);
			
		
		}
			$activeInvoiceAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
		if ( /*getUserClassificationId($delegateId) == 11 && */ $activeInvoiceAmount == 0.00) {
			complementary_workshop_confirmation_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");
			//$mycms->redirect("complementary.online.payment.success.php");
			$userDetails		 = getUserDetails($delegateId);
			$mycms->redirect("complimentary.success.php?email=" . $mycms->encoded($userDetails['user_email_id']) . "&did=" . $mycms->encoded($delegateId) . "&slip=" . $mycms->encoded($mycms->getSession('SLIP_ID')));
		} else {
			
			//$mycms->redirect("registration.".strtolower($registrationMode) .".checkout.php");
			// workshop related work for profile by weavers start     
			$slipId = $mycms->getSession('SLIP_ID');
			$userDetails		 = getUserDetails($delegateId);
			if ($registrationMode == "OFFLINE") {
				
				if (isset($_FILES['cash_document'])) {
					// Handle the file upload
					$tempFile = $_FILES['cash_document']['tmp_name'];
					// echo $tempFile;die;
					$cashFileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					
					move_uploaded_file($tempFile, $uploadDir . $cashFileName);
						
				}
				if (isset($_FILES['neft_document'])) {
					// Handle the file upload
					$tempFile = $_FILES['neft_document']['tmp_name'];
					// echo $tempFile;die;
					$neftFileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					
					move_uploaded_file($tempFile, $uploadDir . $neftFileName);
				}

				$offlinePaymentDetails  = array();
				$offlinePaymentDetails['payment_mode'] = trim($_REQUEST['payment_mode']);
				$offlinePaymentDetails['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date']);
				$offlinePaymentDetails['cash_document'] = trim($cashFileName);
				$offlinePaymentDetails['cheque_number'] = trim($_REQUEST['cheque_number']);
				$offlinePaymentDetails['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_bank']);
				$offlinePaymentDetails['cheque_date'] = trim($_REQUEST['cheque_date']);
				$offlinePaymentDetails['draft_number'] = trim($_REQUEST['draft_number']);
				$offlinePaymentDetails['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank']);
				$offlinePaymentDetails['draft_date'] = trim($_REQUEST['draft_date']);
				$offlinePaymentDetails['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no']);
				$offlinePaymentDetails['neft_bank_name'] = trim($_REQUEST['neft_bank_name']);
				$offlinePaymentDetails['neft_document'] = trim($neftFileName);
				$offlinePaymentDetails['neft_date'] = trim($_REQUEST['neft_date']);
				$offlinePaymentDetails['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no']);
				$offlinePaymentDetails['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name']);
				$offlinePaymentDetails['rtgs_date'] = trim($_REQUEST['rtgs_date']);
				//UPI Payment Option Added By Weavers start
				$offlinePaymentDetails['upi_date'] = trim($_REQUEST['upi_date']);
				$offlinePaymentDetails['txn_no'] = trim($_REQUEST['txn_no']);
				//UPI Payment Option Added By Weavers end
  ?>
  <?php

        if($reg_type == 'BACK'){
			$RedirectURL = 'webmaster/registration.php' ;
		}else{
			$RedirectURL ='registration.success.php';
		}
		?>
				<center>
					<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
						<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
						<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
						<input type="hidden" id="delegate_id" name="user_email_id" value="<?= $userDetails['user_email_id'] ?>" />
						<input type="hidden" name="act" value="setPaymentTerms" />
						<input type="hidden" name="mode" value="<?= $registrationMode ?>" />
					    <input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />

						<input type="hidden" name="payment_mode" value="<?= $offlinePaymentDetails['payment_mode'] ?>" />

						<input type="hidden" name="cash_deposit_date" value="<?= $offlinePaymentDetails['cash_deposit_date'] ?>" />
						<input type="hidden" name="cash_document" value="<?= $offlinePaymentDetails['cash_document'] ?>" />

						<!-- UPI Payment Option Added By Weavers start -->
						<input type="hidden" name="upi_date" value="<?= $offlinePaymentDetails['upi_date'] ?>" />
						<input type="hidden" name="txn_no" value="<?= $offlinePaymentDetails['txn_no'] ?>" />
						<!-- UPI Payment Option Added By Weavers end -->

						<input type="hidden" name="cheque_number" value="<?= $offlinePaymentDetails['cheque_number'] ?>" />
						<input type="hidden" name="cheque_drawn_bank" value="<?= $offlinePaymentDetails['cheque_drawn_bank'] ?>" />
						<input type="hidden" name="cheque_date" value="<?= $offlinePaymentDetails['cheque_date'] ?>" />

						<input type="hidden" name="draft_number" value="<?= $offlinePaymentDetails['draft_number'] ?>" />
						<input type="hidden" name="draft_drawn_bank" value="<?= $offlinePaymentDetails['draft_drawn_bank'] ?>" />
						<input type="hidden" name="draft_date" value="<?= $offlinePaymentDetails['draft_date'] ?>" />

						<input type="hidden" name="neft_transaction_no" value="<?= $offlinePaymentDetails['neft_transaction_no'] ?>" />
						<input type="hidden" name="neft_bank_name" value="<?= $offlinePaymentDetails['neft_bank_name'] ?>" />
						<input type="hidden" name="neft_date" value="<?= $offlinePaymentDetails['neft_date'] ?>" />
						<input type="hidden" name="neft_document" value="<?= $offlinePaymentDetails['neft_document'] ?>" />

						<input type="hidden" name="rtgs_transaction_no" value="<?= $offlinePaymentDetails['rtgs_transaction_no'] ?>" />
						<input type="hidden" name="rtgs_bank_name" value="<?= $offlinePaymentDetails['rtgs_bank_name'] ?>" />
						<input type="hidden" name="rtgs_date" value="<?= $offlinePaymentDetails['rtgs_date'] ?>" />

						<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
					</form>
				</center>
				<script type="text/javascript">
					document.srchOnlineFrm.submit();
				</script>
			<?
				exit();
			} else if ($registrationMode == "ONLINE") {
				$onlinePaymentDetails   = array();
				$onlinePaymentDetails['card_mode'] = trim($_REQUEST['card_mode']);
			?>
				<center>
					<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
						<input type="hidden" id="slip_id" name="slip_id" value="<?= $slipId ?>" />
						<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
						<input type="hidden" name="act" value="paymentSet" />
						<input type="hidden" name="card_mode" value="<?= $onlinePaymentDetails['card_mode'] ?>" />
						<input type="hidden" name="mode" value="<?= $registrationMode ?>" />
						<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
						<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
					</form>
				</center>
				<script type="text/javascript">
					document.srchOnlineFrm.submit();
				</script>
			<?
				exit();
			}
			// workshop related work for profile by weavers end
		}
	}
}

function paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array)
{
	//echo 'slip_id='.$slip_id;
	//echo '<pre>'; print_r($payment_array); die;
	if($reg_type == 'BACK'){
		$RedirectURL = 'webmaster/registration.php' ;
	}else{
		$RedirectURL ='registration.success.php';
	}
	if (count($payment_array) > 0 && !empty($slip_id)) {
		$userDetails		 = getUserDetails($delegateId);
		if ($payment_mode == "OFFLINE") {
	?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slip_id ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
					<input type="hidden" id="user_email_id" name="user_email_id" value="<?= $userDetails['user_email_id'] ?>" />
					<input type="hidden" name="act" value="setPaymentTerms" />
					<input type="hidden" name="mode" value="<?= $payment_mode ?>" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
					<input type="hidden" name="payment_mode" value="<?= $payment_array['payment_mode'] ?>" />
					<input type="hidden" name="cash_deposit_date" value="<?= $payment_array['cash_deposit_date'] ?>" />
					<input type="hidden" name="cash_document" value="<?= $payment_array['cash_document'] ?>" />
					<!-- UPI Payment Option Added By Weavers start -->
					<input type="hidden" name="upi_date" value="<?= $payment_array['upi_date'] ?>" />
					<input type="hidden" name="txn_no" value="<?= $payment_array['txn_no'] ?>" />
					<!-- UPI Payment Option Added By Weavers end -->
					<input type="hidden" name="cheque_number" value="<?= $payment_array['cheque_number'] ?>" />
					<input type="hidden" name="cheque_drawn_bank" value="<?= $payment_array['cheque_drawn_bank'] ?>" />
					<input type="hidden" name="cheque_date" value="<?= $payment_array['cheque_date'] ?>" />
					<input type="hidden" name="draft_number" value="<?= $payment_array['draft_number'] ?>" />
					<input type="hidden" name="draft_drawn_bank" value="<?= $payment_array['draft_drawn_bank'] ?>" />
					<input type="hidden" name="draft_date" value="<?= $payment_array['draft_date'] ?>" />
					<input type="hidden" name="neft_transaction_no" value="<?= $payment_array['neft_transaction_no'] ?>" />
					<input type="hidden" name="neft_bank_name" value="<?= $payment_array['neft_bank_name'] ?>" />
					<input type="hidden" name="neft_date" value="<?= $payment_array['neft_date'] ?>" />
					<input type="hidden" name="neft_document" value="<?= $payment_array['neft_document'] ?>" />

					<input type="hidden" name="rtgs_transaction_no" value="<?= $payment_array['rtgs_transaction_no'] ?>" />
					<input type="hidden" name="rtgs_bank_name" value="<?= $payment_array['rtgs_bank_name'] ?>" />
					<input type="hidden" name="rtgs_date" value="<?= $payment_array['rtgs_date'] ?>" />
					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
		<?
			exit();
		} else if ($payment_mode == "ONLINE") {
		?>
			<center>
				<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchOnlineFrm">
					<input type="hidden" id="slip_id" name="slip_id" value="<?= $slip_id ?>" />
					<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
					<input type="hidden" name="act" value="paymentSet" />
					<input type="hidden" name="RedirectURL" value="<?= $RedirectURL?>" />
					<input type="hidden" name="card_mode" value="<?= $payment_array['card_mode'] ?>" />
					<input type="hidden" name="mode" value="<?= $payment_mode ?>" />
					<input type="hidden" name="payment_mode" value="<?= $payment_array['payment_mode'] ?>" />

					<style>
						.payment_loading {
							width: 100%;
							height: 100vh;
							position: fixed;
							background: #fff3e4;
							z-index: 2;
							/* display: none; */
						}
						
						.payment_loading_inner {
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 100%;
						}
						
						.payment_loading video {
						
							width: 150px;
						}
						
						.payment_loading h4 {
							text-transform: uppercase;
							letter-spacing: 8px;
							color: #560600;
							font-size: 23px;
							font-weight: 500;
						}
						
						.payment_loading p {
							margin: 0;
							width: 40%;
							text-align: center;
							font-size: 13px;
							color: #7e0900;
						}
					</style>
					<div class="payment_loading">
						<div class="payment_loading_inner">
							<video src="images/Cube.webm" autoplay inline muted loop></video>
							<h4>Payment Processing</h4>
									<p>Please do not click 'back' or 'refresh' button or close the browser window.</p>
						</div>
                   </div>
				</form>
			</center>
			<script type="text/javascript">
				document.srchOnlineFrm.submit();
			</script>
<?
			exit();
		}
	} else {
		$mycms->redirect("profile.php");
		$mycms->setSession('PAYMENT_PROCESS_FAILED', "Unable to procced.");
	}
	exit();
}

// Accommodation details setup by weavers end
?>