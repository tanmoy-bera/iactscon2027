<?php

include_once('includes/init.php');

// include_once("../includes/function.registration.php");
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");

// Get the full path to the current file's directory
$action = $_REQUEST['act'];

?>

<?php
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {

	case 'combinedRegistrationProcess':
		combinedRegistrationProcess($mycms, $cfg);
		exit();
		break;
	case 'combinedAddServiceProcess':
		//    echo "<pre>";
		//   print_r($_REQUEST);
		//   die();
		   $delegateId = $_REQUEST['delegate_id'];
    
	// $workshopIds = isset($_REQUEST['workshop_id'])
	// 	? array_map('intval', $_REQUEST['workshop_id'])
	// 	: [];

	$clsfId = getUserClassificationId($delegateId);
    if($_REQUEST['registration_accompany_cutoff']!=""){
		$accCutoffId = $_REQUEST['registration_accompany_cutoff'];
	}else{
		$accCutoffId =getUserCutoffId($delegateId);
	}
	$cutoffId = getUserCutoffId($delegateId);
	$spotUser = $_REQUEST['userREGtype'] ?? '';
	// relocateMasterWorkshopNew(
	// 	$delegateId,
	// 	$workshopIds,
	// 	$clsfId,
	// 	$cutoffId,
	// 	$spotUser
	// );

	if (!empty($_REQUEST['accompany_name_add'])) {

		foreach ($_REQUEST['accompany_name_add'] as $accompanyId => $data) {

			$user_full_name = $data['user_full_name'] ?? '';
			$food_preference = $_REQUEST['accompany_food_choice'][$accompanyId] ?? '';

			nameAccompanyUpdateProcessNew($accompanyId, $user_full_name, $food_preference);
		}
	}


	$accompanyDetails = $_REQUEST;
	//add new accompany
    if ((!empty($_REQUEST['workshop_id'])) || (!empty($_REQUEST['accompany_selected_add']))) {
	     $slip_id = insertingSlipDetails($delegateId, $_REQUEST['registrationMode'], $_REQUEST['registration_request'],'','BACK',$_REQUEST['paymentstatus']);
	}
	if ($accompanyDetails) {
		foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {


			if (
					!is_numeric($val) ||
					empty($accompanyDetails['accompany_name_add'][$val]) ||
					checkAccompanyExists($val)
				) {
					continue;
				}

				$accompanyDetailsArray[$val]['refference_delegate_id'] = $delegateId;
				$accompanyDetailsArray[$val]['user_full_name'] = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
				$accompanyDetailsArray[$val]['user_age'] = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
				$accompanyDetailsArray[$val]['user_food_preference'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
				$accompanyDetailsArray[$val]['user_food_details'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
				$accompanyDetailsArray[$val]['accompany_relationship'] = addslashes(trim(strtoupper('ACCOMPANY')));

				$accompanyDetailsArray[$val]['isRegistration'] = 'Y';
				$accompanyDetailsArray[$val]['isConference'] = 'Y';
				$accompanyDetailsArray[$val]['registration_classification_id'] = $clsfId;
				$accompanyDetailsArray[$val]['registration_tariff_cutoff_id'] = $accCutoffId;
				$accompanyDetailsArray[$val]['registration_request'] = $_REQUEST['registration_request'];
				$accompanyDetailsArray[$val]['operational_area'] = $_REQUEST['registration_request'];
				$accompanyDetailsArray[$val]['registration_payment_status'] =$_REQUEST['paymentstatus']?? 'UNPAID';
				$accompanyDetailsArray[$val]['registration_mode'] = 'OFFLINE';
				$accompanyDetailsArray[$val]['account_status'] = 'REGISTERED';
				$accompanyDetailsArray[$val]['reg_type'] = 'BACK';
        }
		// echo '<pre>'; print_r($_REQUEST);die;
		$accompanyCutoff =$accCutoffId;
		// echo "<pre>"; print_r($accompanyDetailsArray); echo "</pre>"; die();

         if ($accompanyDetailsArray) {
			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray,'',$accompanyCutoff);
		 }
        foreach ($accompanyReqId as $key => $reqId) {
			if ($key == 0) {
				if (
					$userDetailsArray['registration_classification_id'] != 7
					&& $userDetailsArray['registration_classification_id'] != 12
				) {
					$invoiceIdConf = insertingInvoiceDetails($reqId, 'ACCOMPANY',$_REQUEST['registration_request'],'','',$_REQUEST['paymentstatus']);
					                        
				}
			} else {
				$invoiceIdConf = insertingInvoiceDetails($reqId, 'ACCOMPANY',$_REQUEST['registration_request'],'','',$_REQUEST['paymentstatus']);
			}

			if($_REQUEST['invoice_generation_mode_edit'] == "COMPLIMENTARY")
			{
				zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMPANY', $slip_id);
			}
			else if($_REQUEST['invoice_generation_mode_edit'] == "ZERO_VALUE")
			{
				zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMPANY', $slip_id);
			}
			//insertingInvoiceDetails($reqId,'ACCOMPANY');
		}
		
	
			// exit;
		// addAccompnyNew($accompanyDetailsArray, $delegateId, $mycms, $cfg);
	}
	//////////////workshop add/////
    if ((!empty($_REQUEST['workshop_id']))) {
			applyWorkshop($mycms, $cfg);
	}
	/////////////////////////////////
	//////////////Accomodation add/////
    if ((!empty($_REQUEST['edit_package_id']))) {
			additionalAccomodationRequestProcess($mycms, $cfg);
	}
	/////////////////////////////////
	if ($accompanyDetailsArray || (!empty($_REQUEST['workshop_id'])) || (!empty($_REQUEST['edit_package_id']))) {
		$payment_mode = trim($accompanyDetails['registrationMode']);
		$payment_array = array();
		
		if ($payment_mode == "OFFLINE") {
             if($payment_mode=='OFFLINE' && $_REQUEST['payment_mode_edit']=='Card' && $_REQUEST['cheque_number_edit']=='' && $_REQUEST['paymentstatus']!='PAID'){
				echo '<form id="paymentMode" 
					action="registration.process.php?act=changePaymentMode
					&delegateId='.$delegateId.'
					&slipId='.$slip_id.'
					&paymentId=
					&registrationMode=ONLINE"
					method="POST">';
					echo '</form>';

					echo '<script>
							document.getElementById("paymentMode").submit();
						</script>';
				}else{
				$payment_array['payment_mode'] = trim($_REQUEST['payment_mode_edit']);
				$payment_array['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date_edit']);
				$payment_array['cash_document'] = '';
				$payment_array['cheque_number'] = trim($_REQUEST['cheque_number_edit']);
				$payment_array['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_ban_edit']);
				$payment_array['cheque_date'] = trim($_REQUEST['cheque_date_edit']);
				$payment_array['draft_number'] = trim($_REQUEST['draft_number_edit']);
				$payment_array['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank_edit']);
				$payment_array['draft_date'] = trim($_REQUEST['draft_date_edit']);
				$payment_array['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no_edit']);
				$payment_array['neft_bank_name'] = trim($_REQUEST['neft_bank_name_edit']);
				$payment_array['neft_date'] = trim($_REQUEST['neft_date_edit']);
				$payment_array['neft_document'] ='';
				$payment_array['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no_edit']);
				$payment_array['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name_edit']);
				$payment_array['rtgs_date'] = trim($_REQUEST['rtgs_date_edit']);
				//UPI Payment Option Added By Weavers start
				$payment_array['paymentstatus'] = trim($_REQUEST['paymentstatus']);
				$payment_array['upi_date'] = trim($_REQUEST['upi_date_edit']);
				$payment_array['txn_no'] = trim($_REQUEST['txn_no_edit']);
				$payment_array['card_number'] = trim($_REQUEST['card_number_edit']);
				$payment_array['card_date'] = trim($_REQUEST['card_date_edit']);
			}
		} else if ($payment_mode == "ONLINE") {
			$payment_array['card_mode'] = trim($_REQUEST['card_mode_edit']);
		}
        if($payment_array['cash_deposit_date']!='' || $payment_array['card_date']!='' || $payment_array['rtgs_date']!='' || $payment_array['upi_date']!='' || $payment_array['cheque_date']|='' || $payment_array['draft_date']!='' || $payment_array['neft_date']!=''){
	    	paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array,$_REQUEST['invoice_generation_mode_edit']);

		}
	}
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];
			
               echo '<script>window.location.href="spot_distribution.php";</script>';
		exit();
		break;
	case 'combinedEditRegistrationProcess':

		combinedEditRegistrationProcess($mycms, $cfg);

		exit();
		break;
	case 'setPaymentAreaEdit':
		header('Content-Type: application/json');
       
		$slipId = $_REQUEST['slipId'] ?? '';
        //   echo "<pre>";
		//   print_r($_REQUEST);
		//   die();
		if (empty($slipId)) {
			echo json_encode(array('status' => 0, 'message' => 'Missing slip ID.'));
			exit;
		}

		$paymentId = setPaymentAreaEdit($mycms, $cfg, $slipId, false);

		if (!empty($paymentId)) {
			echo json_encode(array('status' => 1, 'message' => 'Payment recorded successfully.'));
		} else {
			echo json_encode(array('status' => 0, 'message' => 'Payment could not be recorded.'));
		}
    exit;
	break;
	case 'makeOnlinePay':

    foreach ($_REQUEST as $key => $value) {

        if (preg_match('/^paymentId_(\d+)$/', $key, $matches)) {

            $slipId = $matches[1];

            if (empty($slipId)) {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Missing slip ID.'
                ]);
                exit;
            }

            $paymentId = $_REQUEST['paymentId_' . $slipId] ?? '';
            $delegateId = $_REQUEST['delegateId'] ?? '';
            $userREGtype = $_REQUEST['userREGtype'] ?? '';
            $paymentName = $_REQUEST['paymentName' . $slipId] ?? '';

            if ($paymentName != 'atomPay') {

                $razorpay_transaction_id =
                    $_REQUEST['razorpay_transaction_id_' . $slipId] ?? '';

                $payment_date =
                    $_REQUEST['payment_date_' . $slipId] ?? '';

                $bank_name =
                    $_REQUEST['atom_transaction_bank_name_' . $slipId] ?? '';

                $cardNumber =
                    $_REQUEST['atom_transaction_card_no_' . $slipId] ?? '';

                echo '<form id="razorpayForm"
                        action="set_online_payment_razorpay.php"
                        method="POST">';

                echo '<input type="hidden" name="slipId" value="' . htmlspecialchars($slipId) . '">';
                echo '<input type="hidden" name="paymentId" value="' . htmlspecialchars($paymentId) . '">';
                echo '<input type="hidden" name="delegateId" value="' . htmlspecialchars($delegateId) . '">';
                echo '<input type="hidden" name="userREGtype" value="' . htmlspecialchars($userREGtype) . '">';
                echo '<input type="hidden" name="razorpay_transaction_id" value="' . htmlspecialchars($razorpay_transaction_id) . '">';
                echo '<input type="hidden" name="payment_date" value="' . htmlspecialchars($payment_date) . '">';
                echo '<input type="hidden" name="bank_name" value="' . htmlspecialchars($bank_name) . '">';
                echo '<input type="hidden" name="CardNumber" value="' . htmlspecialchars($cardNumber) . '">';

                echo '</form>';

                echo '<script>
                    document.getElementById("razorpayForm").submit();
                </script>';

            } else {

                echo '<form id="atomForm"
                        action="set_online_payment_dr.php"
                        method="POST">';

                echo '<input type="hidden" name="slipId" value="' . htmlspecialchars($slipId) . '">';
                echo '<input type="hidden" name="paymentId" value="' . htmlspecialchars($paymentId) . '">';
                echo '<input type="hidden" name="delegateId" value="' . htmlspecialchars($delegateId) . '">';
                echo '<input type="hidden" name="payment_date" value="' . htmlspecialchars($_REQUEST['payment_date' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="clientcode" value="' . htmlspecialchars($_REQUEST['clientcode' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="mmp_txn" value="' . htmlspecialchars($_REQUEST['mmp_txn' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="mer_txn" value="' . htmlspecialchars($_REQUEST['mer_txn' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="date" value="' . htmlspecialchars($_REQUEST['date' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="bank_name" value="' . htmlspecialchars($_REQUEST['bank_name' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="bank_txn" value="' . htmlspecialchars($_REQUEST['bank_txn' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="CardNumber" value="' . htmlspecialchars($_REQUEST['CardNumber' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="discriminator" value="' . htmlspecialchars($_REQUEST['discriminator' . $slipId] ?? '') . '">';
                echo '<input type="hidden" name="remarks" value="' . htmlspecialchars($_REQUEST['remarks' . $slipId] ?? '') . '">';

                echo '</form>';

                echo '<script>
                    document.getElementById("atomForm").submit();
                </script>';
            }

            exit;
        }
    }

    exit;
		break;
//   case 'makeOnlinePay':

//     header('Content-Type: application/json');

//     foreach ($_REQUEST as $key => $value) {

//         if (!preg_match('/^paymentId_(\d+)$/', $key, $matches)) {
//             continue;
//         }

//         $slipId = $matches[1];

//         if (empty($slipId)) {
//             echo json_encode([
//                 'status' => 0,
//                 'message' => 'Missing slip ID.'
//             ]);
//             exit;
//         }

//         $paymentId = $_REQUEST['paymentId_' . $slipId] ?? '';
//         $delegateId = $_REQUEST['delegateId'] ?? '';
//         $userREGtype = $_REQUEST['userREGtype'] ?? '';
//         $paymentName = $_REQUEST['paymentName' . $slipId] ?? '';

//         /*
//          * ============================
//          * ATOM PAYMENT
//          * ============================
//          */
//         if ($paymentName == 'atomPay') {

//             $postData = [
//                 'slipId'        => $slipId,
//                 'paymentId'     => $paymentId,
//                 'delegateId'    => $delegateId,
//                 'payment_date'  => $_REQUEST['payment_date' . $slipId] ?? '',
//                 'clientcode'    => $_REQUEST['clientcode' . $slipId] ?? '',
//                 'mmp_txn'       => $_REQUEST['mmp_txn' . $slipId] ?? '',
//                 'mer_txn'       => $_REQUEST['mer_txn' . $slipId] ?? '',
//                 'date'          => $_REQUEST['date' . $slipId] ?? '',
//                 'bank_name'     => $_REQUEST['bank_name' . $slipId] ?? '',
//                 'bank_txn'      => $_REQUEST['bank_txn' . $slipId] ?? '',
//                 'CardNumber'    => $_REQUEST['CardNumber' . $slipId] ?? '',
//                 'discriminator' => $_REQUEST['discriminator' . $slipId] ?? '',
//                 'remarks'       => $_REQUEST['remarks' . $slipId] ?? ''
//             ];

//             /*
//              * Server-to-server POST
//              */
//             $url = _BASE_URL_ . 'webmaster/set_online_payment_dr.php';
//             $ch = curl_init($url);

//             curl_setopt_array($ch, [
//                 CURLOPT_POST           => true,
//                 CURLOPT_POSTFIELDS     => http_build_query($postData),
//                 CURLOPT_RETURNTRANSFER => true,
//                 CURLOPT_FOLLOWLOCATION => true,
//                 CURLOPT_TIMEOUT        => 60,
//                 CURLOPT_CONNECTTIMEOUT => 15,
//                 CURLOPT_SSL_VERIFYPEER => true,
//                 CURLOPT_SSL_VERIFYHOST => 2
//             ]);

//             $response = curl_exec($ch);

//             $curlError = curl_error($ch);
//             $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//             curl_close($ch);

//             /*
//              * cURL failed
//              */
//             if ($response === false || !empty($curlError)) {

//                 error_log(
//                     'set_online_payment_dr.php cURL error: ' .
//                     $curlError
//                 );

//                 echo json_encode([
//                     'status' => 0,
//                     'message' => 'Payment processing failed.'
//                 ]);

//                 exit;
//             }

//             /*
//              * HTTP error
//              */
//             if ($httpCode < 200 || $httpCode >= 300) {

//                 error_log(
//                     'set_online_payment_dr.php HTTP error: ' .
//                     $httpCode .
//                     ' Response: ' .
//                     $response
//                 );

//                 echo json_encode([
//                     'status' => 0,
//                     'message' => 'Payment processing failed.'
//                 ]);

//                 exit;
//             }

//             /*
//              * Log response while testing
//              */
//             error_log(
//                 'set_online_payment_dr.php response: ' .
//                 $response
//             );

//             /*
//              * Return normal JSON to your existing AJAX
//              */
//             echo json_encode([
//                 'status' => 1,
//                 'message' => 'Payment processed successfully.',
//                 'slipId' => $slipId
//             ]);

//             exit;
//         }

//         /*
//          * ============================
//          * RAZORPAY / OTHER PAYMENT
//          * ============================
//          */

//         if ($paymentName != 'atomPay') {

//             $postData = [
//                 'slipId'                   => $slipId,
//                 'paymentId'                => $paymentId,
//                 'delegateId'               => $delegateId,
//                 'userREGtype'              => $userREGtype,
//                 'razorpay_transaction_id'  => $_REQUEST['razorpay_transaction_id_' . $slipId] ?? '',
//                 'payment_date'             => $_REQUEST['payment_date_' . $slipId] ?? '',
//                 'bank_name'                => $_REQUEST['atom_transaction_bank_name_' . $slipId] ?? '',
//                 'CardNumber'              => $_REQUEST['atom_transaction_card_no_' . $slipId] ?? ''
//             ];

//             $url = _BASE_URL_ . 'webmaster/set_online_payment_razorpay.php';

//             $ch = curl_init($url);

//             curl_setopt_array($ch, [
//                 CURLOPT_POST           => true,
//                 CURLOPT_POSTFIELDS     => http_build_query($postData),
//                 CURLOPT_RETURNTRANSFER => true,
//                 CURLOPT_FOLLOWLOCATION => true,
//                 CURLOPT_TIMEOUT        => 60,
//                 CURLOPT_CONNECTTIMEOUT => 15,
//                 CURLOPT_SSL_VERIFYPEER => true,
//                 CURLOPT_SSL_VERIFYHOST => 2
//             ]);

//             $response = curl_exec($ch);

//             $curlError = curl_error($ch);
//             $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//             curl_close($ch);

//             if ($response === false || !empty($curlError)) {

//                 error_log(
//                     'set_online_payment_razorpay.php cURL error: ' .
//                     $curlError
//                 );

//                 echo json_encode([
//                     'status' => 0,
//                     'message' => 'Payment processing failed.'
//                 ]);

//                 exit;
//             }

//             echo json_encode([
//                 'status' => 1,
//                 'message' => 'Payment processed successfully.',
//                 'slipId' => $slipId
//             ]);

//             exit;
//         }
//     }

//     echo json_encode([
//         'status' => 0,
//         'message' => 'No payment data found.'
//     ]);

//     exit;
	case 'step6':
		step6($mycms, $cfg);
		exit();
		break;

	case 'onlyWorkshopReg':
		onlyWorkshopReg($mycms, $cfg);
		exit();
		break;

	case 'setPaymentTerms':
		setPaymentTerms($mycms, $cfg);
		exit();
		break;

	case 'setPaymentArea':
		setPaymentArea($mycms, $cfg);
		exit();
		break;

	case 'getEmailValidation':
		/////****** BACKEND EMAIL VALIDATION ****** /////////
		return emailIdValidationProcess();
		break;

	case 'getMobileValidation':
		////****** BACKEND MOBILE NO VALIDATION ****** /////////
		return mobileValidationProcess();
		break;

	case 'onlinePaymentDetails':
		onlinePaymentConfirmation();
		exit();
		break;

	case 'Trash':
		Trash($mycms, $cfg);
		exit();
		break;

	case 'payment_discard':
		payment_discard_spot($mycms, $cfg,$_REQUEST['slipId'],$_REQUEST['paymentId']);
		exit();
		break;

	case 'makeComplemantary':
		makeComplemantary($mycms, $cfg);
		exit();
		break;
   case 'makeComplemantaryAjax':
		makeComplemantaryAjax($mycms, $cfg);
		exit();
		break;

	case 'counterSetPaymentTerms':
		counterSetPaymentTerms($mycms, $cfg);
		exit();
		break;

	case 'additionalSetPaymentTerms':
		additionalSetPaymentTerms($mycms, $cfg);
		exit();
		break;

	case 'search_registration':
		pageRedirection("registration_new.php", 5);
		exit();
		break;

	case 'quickSearch_registration':
		pageRedirection("registration_new.php", 5);
		exit();
		break;

	case 'getRegistrationDetails':

		getRegistrationDetails($mycms, $cfg);
		break;
	//edit new
	case 'registrationEditDetails':

		registrationEditDetails($mycms, $cfg);
		break;

	//view new
	case 'ViewProfileDetails':

		ViewProfileDetails($mycms, $cfg);
		break;
		//delete new
	case 'DeleteUser':

		DeleteUser($mycms, $cfg);
		break;
	case 'getRegistrationDetailsForSpot':
		getRegistrationDetailsForSpot($mycms, $cfg);
		break;

	case 'getUserRegTableDetails':
		getUserRegTableDetails($mycms, $cfg);
		break;

	case 'exhibitorBal':
		exhibitorRemainBal();
		exit();
		break;

	case 'addCallDetails':
		addCallDetails($mycms, $cfg);
		exit();
		break;

	case 'applyDinner':
		applyDinner($mycms, $cfg);
		exit();
		break;

	case 'addAccompny':
		addAccompny($mycms, $cfg);
		exit();
		break;

	case 'addGuestAccompany':
		addGuestAccompany($mycms, $cfg);
		exit();
		break;

	case 'applyWorkshop':
		applyWorkshop($mycms, $cfg);
		exit();
		break;

	case 'editReallocationOfMasterWorkshop':
		relocateMasterWorkshop();
		break;

	case 'editReallocationOfWorkshop':
		relocateWorkshop();
		break;

	case 'changeRegClassification':
		changeRegClassification();
		break;

	case 'sendRegFinalMail':
		sendRegFinalMail($mycms, $cfg);
		exit();
		break;

	case 'sendWorkshopCertificateMail':
		sendWorkshopCertificateMail($mycms, $cfg);
		exit();
		break;

	case 'downloadWorkshopCertificate':
		get_workshop_certificate_pdf($_REQUEST['username'], $_REQUEST['workshopId'], true);
		break;


	case 'sendRegFinalSMS':
		sendRegFinalSMS($mycms, $cfg);
		exit();
		break;

	case 'search_slip_wise':
		pageRedirection("unpaid.invoice.make.payment.php", 5);
		exit();
		break;

	case 'deleteTrash':
		deleteTrash($mycms, $cfg);
		exit();
		break;

	case 'Active':
		Active($mycms, $cfg);
		exit();
		break;

	case 'paymentDetails':
		paymentConfirmation();
		exit();
		break;

	case 'multiPaymentDetails':
		multiPaymentConfirmation();
		exit();
		break;
	case 'multiPaymentConfirmation':
		ob_start();

		$slipId      = $_REQUEST['slipId'] ?? '';
		$paymentId   = $_REQUEST['paymentId'] ?? '';
		$delegateId  = $_REQUEST['delegateId'] ?? '';
		$userREGtype = $_REQUEST['userREGtype'] ?? '';
         $paymentDate = $_REQUEST['payment_date'] ?? '';
		$res = multiPaymentConfirmationSpot($mycms, $cfg, $slipId, $paymentId, $delegateId, $userREGtype,$paymentDate);
		 ob_end_clean();
		header('Content-Type: application/json');
		echo json_encode($res);
		exit;
	case 'make_payment':
		make_payment($mycms, $cfg);
		exit();
		break;

	case 'make_partial_payment':
		make_partial_payment($mycms, $cfg);
		exit();
		break;

	case 'changePaymentMode':
		changePaymentMode($mycms, $cfg);
		exit();
		break;
   case 'changePaymentModeAjax':
		changePaymentModeAjax($mycms, $cfg);
		exit();
		break;
	case 'cancel_invoice':
	
		cancelInvoiceProcess();
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Invoice cancelled successfully!' // dynamic message
	    	];
	    echo '<script>window.location.href="registration.php";</script>';
		exit();
		break;
		case 'cancel_invoice_only':
	
		cancelInvoiceProcess();
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Invoice cancelled successfully!' // dynamic message
	    	];
	    echo '<script>window.location.href="request_cancelation.php";</script>';
		exit();
		break;
      case 'cancelInvoiceProcessAjax':
	
		cancelInvoiceProcess();
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Invoice cancelled successfully!' // dynamic message
	    	];
	    echo json_encode(array('status' => 1, 'message' => 'Payment changed to complementry successfully'));

		exit();
	    break;
	case 'viewDetails':
		$delegateId = $_REQUEST['delegateId'];
		viewDelegateRegistrationDetailsView($delegateId, 500);
		exit();
		break;

	case 'downloadUserListExcel':
		downloadUserListExcel($mycms, $cfg);
		break;

	case 'downloadUserListAccommodationExcel':
		downloadUserListAccommodationExcel($mycms, $cfg);
		break;

	case 'downloadUserListSummaryExcel':
		downloadUserListSummaryExcel($mycms, $cfg);
		break;

	case 'downloadAccompanyListExcel':
		downloadAccompanyListExcel($mycms, $cfg);
		break;

	case 'downloadCountryWiseUserListExcel':
		downloadCountryWiseUserListExcel($mycms, $cfg);
		break;

	case 'downloadFacultyListExcel':
		downloadFacultyListExcel($mycms, $cfg);
		break;


	case 'updateNote':
		updateNote();
		exit();
		break;

	case 'updateSharePref':
		updateSharePref();
		exit();
		break;

	case 'updateAccomDate':
		updateAccomDate();
		exit();
		break;

	case 'apply_additional_accommodation':
		$delegateId = $_REQUEST['delegate_id'];
		$registrationType = $_REQUEST['registration_type_add'];
		$returnArray = additionalAccomodationRequestProcess($registrationType);
		$spotUser = $_REQUEST['userREGtype'];

		if ($spotUser != '') {
			pageRedirection("registration_new.php?show=viewAll&id=" . $returnArray['delegateId'] . "&slipId=" . $returnArray['slipId'] . "&paymentId=" . $returnArray['paymentId'] . "&mailFor=Accomod&reg_type=" . $returnArray['reg_type'] . "&userREGtype=SPOT&mode=spotSearch", "");
		} else {
			pageRedirection("registration_new.php?show=invoice&id=" . $delegateId, "");
		}
		exit();
		break;

	case 'registerAbstractUser':
		registerAbstractUser();
		exit();
		break;

	case 'getAccompanyCutoffAmnt':

		$cutoffId = $_REQUEST['cutoffId'];
		echo $accompanyAmnt = getCutoffTariffAmnt($cutoffId);

		exit();
		break;
}

function combinedRegistrationProcess($mycms, $cfg)
{
	// DETAILS INSERTING PROCESS
	// include_once('../../includes/function.delegate.php');
	// include_once('../../includes/function.invoice.php');
	// include_once('../../includes/function.accompany.php');
	// include_once('../../includes/function.workshop.php');
	// include_once('../../includes/function.dinner.php');
	// include_once('../../includes/function.accommodation.php');
    echo '<pre>'; print_r($_REQUEST);die;
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	$sessionId = session_id();
	$userIp = $_SERVER['REMOTE_ADDR'] ?? '';
	$userBrowser = $_SERVER['HTTP_USER_AGENT'] ?? '';



	$step1Data = serialize($_REQUEST);
	$step2Data = serialize($_REQUEST);
	$step3Data = serialize($_REQUEST);
	$step4Data = serialize($_REQUEST);
	$step5Data = serialize($_REQUEST);



	$USER_DETAILS = [];

	$USER_DETAILS['NAME'] = addslashes(trim(strtoupper(
		($_REQUEST['user_initial_title'] ?? '') . ' ' .
		($_REQUEST['user_first_name'] ?? '') . ' ' .
		($_REQUEST['user_middle_name'] ?? '') . ' ' .
		($_REQUEST['user_last_name'] ?? '')
	)));

	$USER_DETAILS['EMAIL'] = addslashes(trim(strtolower($_REQUEST['user_email_id'] ?? '')));
	$USER_DETAILS['PH_NO'] = addslashes(trim(($_REQUEST['user_usd_code'] ?? '') . ' - ' . ($_REQUEST['user_mobile'] ?? '')));
	$USER_DETAILS['CUTOFF'] = $_REQUEST['registration_cutoff'] ?? '';
	$USER_DETAILS['CATAGORY'] = (int)$_REQUEST['registration_classification_id'][0] ?? '';

	//workshop
	$sqlWorkshop = array();
	$sqlWorkshop['QUERY'] = "SELECT * 
												 FROM " . _DB_REGISTRATION_CLASSIFICATION_ . "
												WHERE id = ?
												  AND status = ? ";
	$sqlWorkshop['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['registration_classification_id'][0], 'TYP' => 's');
	$sqlWorkshop['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resultWorkshop = $mycms->sql_select($sqlWorkshop);
	$rowRegClas = $resultWorkshop[0];
	if ($rowRegClas) {

		if (($rowRegClas['classification_title'] == 'FACULTY') || ($rowRegClas['classification_title'] == 'EC Member')) {
			$byPassWOrkshopId = array();
			$sqlWorkshop = array();
			$sqlWorkshop['QUERY'] = "SELECT * 
															 FROM " . _DB_WORKSHOP_CLASSIFICATION_ . "
															WHERE status = ? ";
			$sqlWorkshop['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
			$resultWorkshop = $mycms->sql_select($sqlWorkshop);
			foreach ($resultWorkshop as $key => $rowWorkshop) {
				$byPassWOrkshopId[] = $rowWorkshop['id'];
			}

			$_REQUEST['workshop_id'] = $byPassWOrkshopId;
		}
	}
	$step1Data = serialize($_REQUEST);

	$mycms->setSession('USER_DETAILS', $USER_DETAILS);


	$userImageFileName = '';

	if (!empty($_FILES['student_idcard']['tmp_name'])) {
		$userImageFileName = $mycms->getRandom(6, 'snum') . '_' . time() . strstr($_FILES['student_idcard']['name'], '.');
		$userImagePath = '../../' . $cfg['USER.ID.CARD'] . $userImageFileName;
		copy($_FILES['student_idcard']['tmp_name'], $userImagePath);
		chmod($userImagePath, 0777);
	}



	if ($mycms->getSession('PROCESS_FLOW_ID') == '') {

		$sql['QUERY'] = "
            INSERT INTO " . _DB_PROCESS_STEP_ . " SET
                step1              = ?,
                step10             = ?,
                created_ip         = ?,
                reg_area           = 'BACK',
                created_sessionId  = ?,
                created_browser    = ?,
                created_dateTime   = ?
        ";

		$sql['PARAM'][] = ['FILD' => 'step1', 'DATA' => $step1Data, 'TYP' => 's'];
		$sql['PARAM'][] = ['FILD' => 'step10', 'DATA' => $userImageFileName, 'TYP' => 's'];
		$sql['PARAM'][] = ['FILD' => 'created_ip', 'DATA' => $userIp, 'TYP' => 's'];
		$sql['PARAM'][] = ['FILD' => 'created_sessionId', 'DATA' => $sessionId, 'TYP' => 's'];
		$sql['PARAM'][] = ['FILD' => 'created_browser', 'DATA' => $userBrowser, 'TYP' => 's'];
		$sql['PARAM'][] = ['FILD' => 'created_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];

		$processId = $mycms->sql_insert($sql, false);
		$mycms->setSession('PROCESS_FLOW_ID', $processId);
	}

	$processId = $mycms->getSession('PROCESS_FLOW_ID');



	$sqlUpdate['QUERY'] = "
        UPDATE " . _DB_PROCESS_STEP_ . " SET
            step2 = ?,
            step3 = ?,
            step4 = ?,
            step5 = ?,
            regitration_status = 'COMPLETE',
            created_dateTime = ?
        WHERE id = ?
    ";

	$sqlUpdate['PARAM'][] = ['FILD' => 'step2', 'DATA' => $step2Data, 'TYP' => 's'];
	$sqlUpdate['PARAM'][] = ['FILD' => 'step3', 'DATA' => $step3Data, 'TYP' => 's'];
	$sqlUpdate['PARAM'][] = ['FILD' => 'step4', 'DATA' => $step4Data, 'TYP' => 's'];
	$sqlUpdate['PARAM'][] = ['FILD' => 'step5', 'DATA' => $step5Data, 'TYP' => 's'];
	$sqlUpdate['PARAM'][] = ['FILD' => 'created_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];
	$sqlUpdate['PARAM'][] = ['FILD' => 'id', 'DATA' => $processId, 'TYP' => 's'];

	$mycms->sql_update($sqlUpdate, false);


	detailsInseringProcess($processId);
	$mycms->removeSession('PROCESS_FLOW_ID');

	pageRedirection("registration_new.php", "Registration Completed Successfully");
}
//edit registration
function combinedEditRegistrationProcess($mycms, $cfg)
{
	// echo '<pre>'; print_r($_REQUEST); die;
	$delegateId = $_REQUEST['delegate_id'];
    $user_titles = addslashes(trim(strtoupper($_REQUEST['user_initial_title'])));
	if ($user_titles != '') {
		$user_title = $user_titles;
	} else {
		$user_title = $user_titles;
	}
	$user_first_name = addslashes(trim(strtoupper($_REQUEST['user_first_name'])));
	$user_middle_name = addslashes(trim(strtoupper($_REQUEST['user_middle_name'])));
	$user_last_name = addslashes(trim(strtoupper($_REQUEST['user_last_name'])));
	$user_full_name = $user_title . " " . $user_first_name . " " . $user_middle_name . " " . $user_last_name;

	$user_email_id = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
	$user_usd_code = addslashes(trim(strtoupper($_REQUEST['user_usd_code'])));
	$user_mobile = addslashes(trim(strtoupper($_REQUEST['user_mobile'])));
	$user_institute_name = addslashes(trim(strtoupper($_REQUEST['user_institute_name'])));
	$user_address = addslashes(trim(strtoupper($_REQUEST['user_address'])));
	$user_country = addslashes(trim(($_REQUEST['user_country'] == "") ? 0 : $_REQUEST['user_country']));
	$user_state = addslashes(trim(($_REQUEST['user_state'] == "") ? 0 : $_REQUEST['user_state']));
	$user_city = addslashes(trim(strtoupper($_REQUEST['user_city'])));
	$user_postal_code = addslashes(trim(strtoupper($_REQUEST['user_postal_code'])));
	$user_food_preference = addslashes(trim(strtoupper($_REQUEST['user_food_preference'])));
			// " . ($user_state != '' ? "user_state_id = ?," : "") . "


	$sqlUpdate = [];
	$sqlUpdate['QUERY'] = "
        UPDATE " . _DB_USER_REGISTRATION_ . "
        SET user_title = ?,
            user_first_name = ?,
            user_middle_name = ?,
            user_last_name = ?,
            user_full_name = ?,
			user_email_id = ?,
			user_mobile_isd_code = ?,
			user_mobile_no = ?,
			user_institute_name = ?,
            user_address = ?,
            user_country_id = ?,
            user_city = ?,
            user_pincode = ?,
            user_food_preference = ?,
            modified_ip = ?,
            modified_sessionId = ?,
            modified_browser = ?,
            modified_dateTime = ?
        WHERE id = ?
    ";

	$sqlUpdate['PARAM'] = [];
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_title', 'DATA' => $user_title, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_first_name', 'DATA' => $user_first_name, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_middle_name', 'DATA' => $user_middle_name, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_last_name', 'DATA' => $user_last_name, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_full_name', 'DATA' => $user_full_name, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_email_id', 'DATA' => $user_email_id, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_mobile_isd_code', 'DATA' => $user_usd_code, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_mobile_no', 'DATA' => $user_mobile, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_institute_name', 'DATA' => $user_institute_name, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_address', 'DATA' => $user_address, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_country_id', 'DATA' => $user_country, 'TYP' => 's');

	// if ($user_state != '') {
	// 	$sqlUpdate['PARAM'][] = array('FILD' => 'user_state_id', 'DATA' => $user_state, 'TYP' => 's');
	// }

	$sqlUpdate['PARAM'][] = array('FILD' => 'user_city', 'DATA' => $user_city, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_pincode', 'DATA' => $user_postal_code, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'user_food_preference', 'DATA' => $user_food_preference, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $modified_ip, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'modified_sessionId', 'DATA' => $modified_sessionId, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'modified_browser', 'DATA' => $modified_browser, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => $modified_dateTime, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 'i');
	if($user_full_name!='' && $user_email_id!='' && $user_mobile!='' && $user_address!='' && $user_title!=''){
        $mycms->sql_update($sqlUpdate);
		
		$paricipantSql = array();
		$paricipantSql['QUERY']       = "SELECT COUNT(*) as COUNTP FROM " . _DB_SP_PARTICIPANT_DETAILS_ . "
											WHERE `participant_email_id` = '" . $user_email_id . "' 
											AND `status` = 'A'";


		$resultFetchParticipant    		= $mycms->sql_select($paricipantSql);
		$rowFetchParticipant 			= $resultFetchParticipant[0];
		if ($rowFetchParticipant['COUNTP'] > 0) {
			$sqlInsertSession = array();
			$sqlInsertSession['QUERY'] 			= " UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
															SET `participant_title`				= '" . $user_title . "', 
																`participant_first_name`				= '" . $user_first_name. "', 
																`participant_middle_name`			= '" . $user_middle_name. "', 
																`participant_last_name`				= '" . $user_last_name. "', 
																`participant_full_name`				= '" . $user_full_name . "'
															WHERE `participant_email_id` = '" . $user_email_id . "'
															AND `status` = 'A'";
			$mycms->sql_update($sqlInsertSession);
		}
	}

	// $workshopIds = isset($_REQUEST['workshop_id'])
	// 	? array_map('intval', $_REQUEST['workshop_id'])
	// 	: [];

	$clsfId = getUserClassificationId($delegateId);
    if($_REQUEST['registration_accompany_cutoff']!=""){
		$accCutoffId = $_REQUEST['registration_accompany_cutoff'];
	}else{
		$accCutoffId =getUserCutoffId($delegateId);
	}
	$cutoffId = getUserCutoffId($delegateId);
	$spotUser = $_REQUEST['userREGtype'] ?? '';
	// relocateMasterWorkshopNew(
	// 	$delegateId,
	// 	$workshopIds,
	// 	$clsfId,
	// 	$cutoffId,
	// 	$spotUser
	// );

	if (!empty($_REQUEST['accompany_name_add'])) {

		foreach ($_REQUEST['accompany_name_add'] as $accompanyId => $data) {

			$user_full_name = $data['user_full_name'] ?? '';
			$food_preference = $_REQUEST['accompany_food_choice'][$accompanyId] ?? '';

			nameAccompanyUpdateProcessNew($accompanyId, $user_full_name, $food_preference);
		}
	}


	$accompanyDetails = $_REQUEST;
	//add new accompany
    if ((!empty($_REQUEST['workshop_id'])) || (!empty($_REQUEST['accompany_selected_add']))) {
	     $slip_id = insertingSlipDetails($delegateId, $_REQUEST['registrationMode'], $_REQUEST['registration_request'],'','BACK',$_REQUEST['paymentstatus']);
	}
	if ($accompanyDetails) {
		foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {


			if (
					!is_numeric($val) ||
					empty($accompanyDetails['accompany_name_add'][$val]) ||
					checkAccompanyExists($val)
				) {
					continue;
				}

				$accompanyDetailsArray[$val]['refference_delegate_id'] = $delegateId;
				$accompanyDetailsArray[$val]['user_full_name'] = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
				$accompanyDetailsArray[$val]['user_age'] = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
				$accompanyDetailsArray[$val]['user_food_preference'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
				$accompanyDetailsArray[$val]['user_food_details'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
				$accompanyDetailsArray[$val]['accompany_relationship'] = addslashes(trim(strtoupper('ACCOMPANY')));

				$accompanyDetailsArray[$val]['isRegistration'] = 'Y';
				$accompanyDetailsArray[$val]['isConference'] = 'Y';
				$accompanyDetailsArray[$val]['registration_classification_id'] = $clsfId;
				$accompanyDetailsArray[$val]['registration_tariff_cutoff_id'] = $accCutoffId;
				$accompanyDetailsArray[$val]['registration_request'] = $_REQUEST['registration_request'];
				$accompanyDetailsArray[$val]['operational_area'] = $_REQUEST['registration_request'];
				$accompanyDetailsArray[$val]['registration_payment_status'] =$_REQUEST['paymentstatus']?? 'UNPAID';
				$accompanyDetailsArray[$val]['registration_mode'] = 'OFFLINE';
				$accompanyDetailsArray[$val]['account_status'] = 'REGISTERED';
				$accompanyDetailsArray[$val]['reg_type'] = 'BACK';
        }
		// echo '<pre>'; print_r($_REQUEST);die;
		$accompanyCutoff =$accCutoffId;
		// echo "<pre>"; print_r($accompanyDetailsArray); echo "</pre>"; die();

         if ($accompanyDetailsArray) {
			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray,'',$accompanyCutoff);
		 }
        foreach ($accompanyReqId as $key => $reqId) {
			if ($key == 0) {
				if (
					$userDetailsArray['registration_classification_id'] != 7
					&& $userDetailsArray['registration_classification_id'] != 12
				) {
					$invoiceIdConf = insertingInvoiceDetails($reqId, 'ACCOMPANY',$_REQUEST['registration_request'],'','',$_REQUEST['paymentstatus']);
					                        
				}
			} else {
				$invoiceIdConf = insertingInvoiceDetails($reqId, 'ACCOMPANY',$_REQUEST['registration_request'],'','',$_REQUEST['paymentstatus']);
			}

			if($_REQUEST['invoice_generation_mode_edit'] == "COMPLIMENTARY")
			{
				complementaryPaymentConfirmationProcess($slip_id, $delegateId);
			}
			else if($_REQUEST['invoice_generation_mode_edit'] == "ZERO_VALUE")
			{
				zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMPANY', $slip_id);
			}
			//insertingInvoiceDetails($reqId,'ACCOMPANY');
		}
		
	
			// exit;
		// addAccompnyNew($accompanyDetailsArray, $delegateId, $mycms, $cfg);
	}
	//////////////workshop add/////
    if ((!empty($_REQUEST['workshop_id'])) && $_REQUEST['workshopEdit']) {
			applyWorkshop($mycms, $cfg);
	}
	/////////////////////////////////
	//////////////Accomodation add/////
    if ((!empty($_REQUEST['edit_package_id']))) {
			additionalAccomodationRequestProcess($mycms, $cfg);
	}
	/////////////////////////////////
	if ($accompanyDetailsArray || (!empty($_REQUEST['workshop_id'])) || (!empty($_REQUEST['edit_package_id']))) {
		$payment_mode = trim($accompanyDetails['registrationMode']);
		$payment_array = array();
		if ($_REQUEST['invoice_generation_mode_edit']!='COMPLIMENTARY' && $_REQUEST['invoice_generation_mode_edit']!='ZERO_VALUE') {

			if ($payment_mode == "OFFLINE") {
				//  if($payment_mode=='OFFLINE' && $_REQUEST['payment_mode_edit']=='Card' && $_REQUEST['cheque_number_edit']=='' && $_REQUEST['paymentstatus']!='PAID'){
					// echo '<form id="paymentMode" 
					// 	action="registration.process.php?act=changePaymentMode
					// 	&delegateId='.$delegateId.'
					// 	&slipId='.$slip_id.'
					// 	&paymentId=
					// 	&registrationMode=ONLINE"
					// 	method="POST">';
					// 	echo '</form>';

					// 	echo '<script>
					// 			document.getElementById("paymentMode").submit();
					// 		</script>';
					// }else{
					$payment_array['payment_mode'] = trim($_REQUEST['payment_mode_edit']);
					$payment_array['cash_deposit_date'] = trim($_REQUEST['cash_deposit_date_edit']);
					$payment_array['cash_document'] = '';
					$payment_array['cheque_number'] = trim($_REQUEST['cheque_number_edit']);
					$payment_array['cheque_drawn_bank'] = trim($_REQUEST['cheque_drawn_ban_edit']);
					$payment_array['cheque_date'] = trim($_REQUEST['cheque_date_edit']);
					$payment_array['draft_number'] = trim($_REQUEST['draft_number_edit']);
					$payment_array['draft_drawn_bank'] = trim($_REQUEST['draft_drawn_bank_edit']);
					$payment_array['draft_date'] = trim($_REQUEST['draft_date_edit']);
					$payment_array['neft_transaction_no'] = trim($_REQUEST['neft_transaction_no_edit']);
					$payment_array['neft_bank_name'] = trim($_REQUEST['neft_bank_name_edit']);
					$payment_array['neft_date'] = trim($_REQUEST['neft_date_edit']);
					$payment_array['neft_document'] ='';
					$payment_array['rtgs_transaction_no'] = trim($_REQUEST['rtgs_transaction_no_edit']);
					$payment_array['rtgs_bank_name'] = trim($_REQUEST['rtgs_bank_name_edit']);
					$payment_array['rtgs_date'] = trim($_REQUEST['rtgs_date_edit']);
					//UPI Payment Option Added By Weavers start
					$payment_array['discountAmountEdit'] = trim($_REQUEST['discountAmountEdit']);
					$payment_array['paymentstatus'] = trim($_REQUEST['paymentstatus']);
					$payment_array['upi_date'] = trim($_REQUEST['upi_date_edit']);
					$payment_array['txn_no'] = trim($_REQUEST['txn_no_edit']);
					$payment_array['card_number'] = trim($_REQUEST['card_number_edit']);
					$payment_array['card_date'] = trim($_REQUEST['card_date_edit']);
				// }
			} else if ($payment_mode == "ONLINE") {
				$payment_array['card_mode'] = trim($_REQUEST['card_mode_edit']);
			}
			if($payment_array['cash_deposit_date']!='' || $payment_array['card_date']!='' || $payment_array['rtgs_date']!='' || $payment_array['upi_date']!='' || $payment_array['cheque_date']|='' || $payment_array['draft_date']!='' || $payment_array['neft_date']!=''){
				paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array,$_REQUEST['invoice_generation_mode_edit']);

			}
		}else{
			paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array='',$_REQUEST['invoice_generation_mode_edit']);

		}
	}

	// $mycms->sql_update($sqlUpdate);
     $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];
			if($_REQUEST['registration_request']=='SPOT'){
				echo '<script>window.location.href="spot_registration.php";</script>';
			}else{
               echo '<script>window.location.href="registration.php";</script>';
			}
	
}



function step6($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	//echo '<pre>'; print_r($_REQUEST); die;

	// DETAILS INSERTING PROCESS
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	$mycms->removeSession('ADD_MORE_COUNTER');
	$regsitaion_mode = $_REQUEST['regsitaion_mode'];

	$userREGtype = $_REQUEST['userREGtype'];
	$abstractDelegateId = $_REQUEST['abstractDelegateId'];

	$mycms->removeSession('SLIP_ID');

	if ($_REQUEST['counter'] == 'Y') {
		$counter = $_REQUEST['counter'];
		detailsInseringProcess($mycms->getSession('PROCESS_FLOW_ID'), $regsitaion_mode, $counter);
	} else {
		detailsInseringProcess($mycms->getSession('PROCESS_FLOW_ID'), $regsitaion_mode);
	}


	$sqlProcessUpdateStep = array();
	$sqlProcessUpdateStep['QUERY'] = "    UPDATE  " . _DB_PROCESS_STEP_ . "
													   SET `regitration_status` = ?
													 WHERE `id` = ?";

	$sqlProcessUpdateStep['PARAM'][] = array('FILD' => 'regitration_status', 'DATA' => 'COMPLETE', 'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('PROCESS_FLOW_ID'), 'TYP' => 's');

	$mycms->sql_update($sqlProcessUpdateStep, false);
	$mycms->removeSession('PROCESS_FLOW_ID');

	if ($userREGtype == 'SPOT') {
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=registrationSummery&userREGtype=" . $userREGtype);
	} elseif ($_REQUEST['counter'] == 'Y') {
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=registrationSummery&COUNTER=Y");
	} else {
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=registrationSummery");
	}
}

function registerAbstractUser()
{
	global $mycms, $cfg;


	// DETAILS INSERTING PROCESS
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	$regsitaion_mode = $_REQUEST['regsitaion_mode'];
	$abstractDelegateId = $_REQUEST['abstractDelegateId'];

	detailsUpdatingProcessForAbstract($mycms->getSession('PROCESS_FLOW_ID'), $abstractDelegateId, $regsitaion_mode);

	$sqlProcessUpdateStep = array();
	$sqlProcessUpdateStep['QUERY'] = "    UPDATE  " . _DB_PROCESS_STEP_ . "
													   SET `regitration_status` = ?
													 WHERE `id` = ?";

	$sqlProcessUpdateStep['PARAM'][] = array('FILD' => 'regitration_status', 'DATA' => 'COMPLETE', 'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('PROCESS_FLOW_ID'), 'TYP' => 's');

	$mycms->sql_update($sqlProcessUpdateStep, false);
	$mycms->removeSession('PROCESS_FLOW_ID');

	pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=registrationSummery");
}

function onlyWorkshopReg()
{
	global $mycms, $cfg;

	// include_once('../../includes/function.delegate.php');
	// include_once('../../includes/function.invoice.php');
	// include_once('../../includes/function.accompany.php');
	// include_once('../../includes/function.workshop.php');
	// include_once('../../includes/function.registration.php');
	// include_once('../../includes/function.messaging.php');
	// include_once('../../includes/function.accommodation.php');
	// include_once("../../includes/function.dinner.php");

	//echo '<pre>'; print_r($_REQUEST);


	$mycms->setSession('CURRENT_REG_USER', 'FINISH');

	$regsitaion_mode = $_REQUEST['regsitaion_mode'];

	// DETAILS INSERTING PROCESS	
	$sqlProcessFlow = array();
	$sqlProcessFlow['QUERY'] = "SELECT * FROM " . _DB_PROCESS_STEP_ . " 
									 WHERE `id` = ?";

	$sqlProcessFlow['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('PROCESS_FLOW_ID'), 'TYP' => 's');

	$resProcessFlow = $mycms->sql_select($sqlProcessFlow);
	if ($resProcessFlow) {
		$rowProcessFlow = $resProcessFlow[0];

		$userDetails = unserialize($rowProcessFlow['step1']);
		$workshopDetails = $userDetails['workshop_id'];
		$cutoffId = $userDetails['registration_cutoff'];
		$workshopCutoffId = $userDetails['workshop_cutoff'];
		$clsfId = '1';
		$date = $userDetails['date'];

		if ($userDetails) {
			$userDetailsArray['user_email_id'] = addslashes(trim(strtolower($userDetails['user_email_id'])));
			$userDetailsArray['comunication_email'] = addslashes(trim(strtolower($userDetails['comunication_email'])));
			$userDetailsArray['user_password_raw'] = $userDetails['user_password'];
			$userDetailsArray['user_password'] = $mycms->encoded($userDetails['user_password']);
			$userDetailsArray['membership_number'] = addslashes(trim($userDetails['membership_number']));
			$userDetailsArray['user_initial_title'] = addslashes(trim(strtoupper($userDetails['user_initial_title'])));
			$userDetailsArray['user_first_name'] = addslashes(trim(strtoupper($userDetails['user_first_name'])));
			$userDetailsArray['user_middle_name'] = addslashes(trim(strtoupper($userDetails['user_middle_name'])));
			$userDetailsArray['user_last_name'] = addslashes(trim(strtoupper($userDetails['user_last_name'])));
			$userDetailsArray['user_full_name'] = $userDetailsArray['user_initial_title'] . " " . $userDetailsArray['user_first_name'] . " " . $userDetailsArray['user_middle_name'] . " " . $userDetailsArray['user_last_name'];
			$userDetailsArray['user_full_name'] = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
			$userDetailsArray['user_mobile_isd_code'] = addslashes(trim(strtoupper($userDetails['user_usd_code'])));
			$userDetailsArray['user_mobile_no'] = addslashes(trim(strtoupper($userDetails['user_mobile'])));
			$userDetailsArray['user_phone_no'] = addslashes(trim(strtoupper($userDetails['user_phone'])));
			$userDetailsArray['user_address'] = addslashes(trim(strtoupper($userDetails['user_address'])));
			$userDetailsArray['user_country'] = addslashes(trim(strtoupper($userDetails['user_country'])));
			$userDetailsArray['user_state'] = addslashes(trim(strtoupper($userDetails['user_state'])));
			$userDetailsArray['user_city'] = addslashes(trim(strtoupper($userDetails['user_city'])));
			$userDetailsArray['user_postal_code'] = addslashes(trim(strtoupper($userDetails['user_postal_code'])));
			$userDetailsArray['user_dob_year'] = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
			$userDetailsArray['user_dob_month'] = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
			$userDetailsArray['user_dob_day'] = addslashes(trim(strtoupper($userDetails['user_dob_day'])));
			if ($userDetailsArray['user_dob_year'] != "" && $userDetailsArray['user_dob_month'] != "" && $userDetailsArray['user_dob_day'] != "") {
				$userDetailsArray['user_dob'] = number_pad($userDetailsArray['user_dob_year'], 4) . "-" . number_pad($userDetailsArray['user_dob_month'], 2) . "-" . number_pad($userDetailsArray['user_dob_day'], 2);
			}

			$userDetailsArray['user_gender'] = ($userDetails['user_gender'] == '') ? 'NA' : $userDetails['user_gender'];
			$userDetailsArray['user_designation'] = addslashes(trim(strtoupper($userDetails['user_designation'])));
			$userDetailsArray['user_depertment'] = addslashes(trim(strtoupper($userDetails['user_depertment'])));
			$userDetailsArray['user_institution_name'] = addslashes(trim(strtoupper($userDetails['user_institution'])));
			$userDetailsArray['user_food_preference'] = $userDetails['user_food_preference'];
			$userDetailsArray['user_other_food_details'] = addslashes(trim(strtoupper($userDetails['user_food_details'])));
			$userDetailsArray['passport_no'] = addslashes(trim(strtoupper($userDetails['user_pasport_no'])));
			$userDetailsArray['passport_expiry_date'] = number_pad($userDetails['user_pasport_exp_year'], 4) . "-" . number_pad($userDetails['user_pasport_exp_month'], 2) . "-" . number_pad($userDetails['user_pasport_exp_day'], 2);

			$userDetailsArray['user_document'] = $fileDetails;
			$userDetailsArray['isRegistration'] = 'N';
			$userDetailsArray['isConference'] = 'N';
			$userDetailsArray['isWorkshop'] = 'N';
			$userDetailsArray['isAccommodation'] = 'N';
			$userDetailsArray['isTour'] = 'N';
			$userDetailsArray['IsAbstract'] = 'N';
			$userDetailsArray['isCombo'] = 'N';
			$userDetailsArray['registration_classification_id'] = '1';
			$userDetailsArray['registration_tariff_cutoff_id'] = '0';
			$userDetailsArray['workshop_tariff_cutoff_id'] = $userDetails['workshop_cutoff'];
			$userDetailsArray['registration_request'] = 'ONLYWORKSHOP';
			$userDetailsArray['operational_area'] = 'ONLYWORKSHOP';
			$userDetailsArray['registration_payment_status'] = 'ZERO_VALUE';
			$userDetailsArray['registration_mode'] = 'OFFLINE';
			$userDetailsArray['account_status'] = 'REGISTERED';
			$userDetailsArray['reg_type'] = addslashes(trim(strtoupper($userDetails['reg_area'])));

			// echo '<pre>'; print_r($userDetailsArray);die;

			//echo '<pre>'; print_r($userDetailsArray);

			$delegateId = insertingUserDetails($userDetailsArray, $date);

			if ($mycms->getSession('SLIP_ID') == "") {
				$mycms->setSession('LOGGED.USER.ID', $delegateId);
				insertingSlipDetails($delegateId, $userDetailsArray['registration_mode'], $userDetails['registration_request'], $date, $userDetailsArray['reg_type']);
			}
		}

		if ($workshopDetails) {
			foreach ($workshopDetails as $key => $workshopId) {
				$workshopDetailArray[$workshopId]['delegate_id'] = $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id'] = $workshopId;
				$workshopDetailArray[$workshopId]['tariff_cutoff_id'] = $workshopCutoffId;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id'] = getWorkshopTariffId($workshopId, $workshopCutoffId, $clsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
				$workshopDetailArray[$workshopId]['booking_mode'] = $userDetailsArray['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type'] = 'GENERAL';
				$workshopDetailArray[$workshopId]['refference_invoice_id'] = 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id'] = $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status'] = 'UNPAID';
			}
			$workshopReqId = insertingWorkshopDetails($workshopDetailArray);
			foreach ($workshopReqId as $key => $reqId) {
				$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP', $userDetails['registration_request'], $date);
				if ($_REQUEST['regsitaion_mode'] == "COMPLIMENTARY" || $_REQUEST['regsitaion_mode'] == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceIdWrkshp, 'WORKSHOP', $mycms->getSession('SLIP_ID'));
					$sqlUpdate = array();
					$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
													SET `payment_status` = ?
												  WHERE `id` = ?";

					$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
					$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $reqId, 'TYP' => 's');
					$mycms->sql_update($sqlUpdate, false);
				}
			}
		}
	}

	$slipId = $mycms->getSession('SLIP_ID');

	$sqlInv = array();
	$sqlInv['QUERY'] = "  SELECT *, IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount, 
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
	$sqlInv['PARAM'][] = array('FILD' => 'slip.status', 'DATA' => 'A', 'TYP' => 's');
	$sqlInv['PARAM'][] = array('FILD' => 'slip.id', 'DATA' => $slipId, 'TYP' => 's');

	$resInv = $mycms->sql_select($sqlInv);

	$rowInv = $resInv[0];
	$clafId = $rowInv['registration_classification_id'];
	$delegateId = $rowInv['delegate_id'];
	$activeInvoiceAmount = $rowInv['activeInvoiceAmount'];

	$sqlProcessUpdateStep = array();
	$sqlProcessUpdateStep['QUERY'] = " UPDATE " . _DB_PROCESS_STEP_ . "
												  SET `regitration_status` = ?
											    WHERE `id` = ?";

	$sqlProcessUpdateStep['PARAM'][] = array('FILD' => 'regitration_status', 'DATA' => 'COMPLETE', 'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('PROCESS_FLOW_ID'), 'TYP' => 's');

	$mycms->sql_update($sqlProcessUpdateStep, false);

	if ($activeInvoiceAmount == 0.00) {
		//complementaryPaymentConfirmationProcess($slipId,$delegateId);
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=registrationSummery");
		exit();
	} else {
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=registrationSummery");
	}
	exit();
}

function setPaymentTerms($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.workshop.php');
	$userREGtype = $_REQUEST['userREGtype'];

	$delegateId = $_REQUEST['delegate_id'];
	$slipId = $_REQUEST['slip_id'];
	if ($_REQUEST['discountAmount'] > 0) {
		$discountAmount = $_REQUEST['discountAmount'];
		$totalAmount = $_REQUEST['totalAmount'];
		//updateonDiscount($slipId, $discountAmount);
		//die();
		updateOnDiscountWithTotal($slipId, $discountAmount, $totalAmount);
	}
	$thisUserDetails = getUserDetails($delegateId);
	if (number_format(invoiceAmountOfSlip($slipId), 2) != 0) {
		//offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId,'SEND','BACK');
		pageRedirection("registration_new.php", 'Process Goto Next Step', "&show=paymentArea&userREGtype=" . $userREGtype);
	} else {
		if ($thisUserDetails['registration_request'] == 'ONLYWORKSHOP') {
			offline_conference_registration_confirmation_workshop_message($delegateId, "", $slipId, 'SEND');
			pageRedirection("registration_new.php", 'Registration Complete', '&show=viewOnlyWorkshopRegistration');
		} else if ($userREGtype == 'SPOT') {
			pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
		} else {
			//offline_conference_complimentry_registration_confirmation_message($delegateId,$slipId,'SEND') ;
			offline_conference_registration_confirmation_message($delegateId, "", $slipId, 'SEND');
			pageRedirection("registration_new.php", 'Registration Complete');
		}
	}
}
function setPaymentAreaEdit($mycms, $cfg, $slipId="", $redirect = true)
{
	$loggedUserID = $mycms->getLoggedUserId();
	// echo '<pre>'; print_r($_REQUEST);die;
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.workshop.php');
   
	$paymentDetails = $_REQUEST;
	$paymentSlipId = $slipId;
    $userREGtype = $_REQUEST['registration_request'];
    $payment_mode = $_REQUEST['payment_mode_' . $slipId] ?? '';
    $amount = $_REQUEST['amount_' . $slipId] ?? 0;
    $discountAmount = $_REQUEST['discountAmount_' . $slipId] ?? 0;
    $payment_remark = $_REQUEST['payment_remark_' . $slipId] ?? '';
    $delegateId = $_REQUEST['delegateId_' . $slipId] ?? $_REQUEST['delegateId'] ?? null;
	 
     
	// /*echo 'dis=='. $_RE
	// QUEST['discountAmount'];
	// 	echo 'amnt='. $totalPaySlipAmt = invoiceAmountOfSlip($paymentSlipId);

	// 	die;
    $totalPaySlipAmt = invoiceAmountOfSlip($paymentSlipId);
	if ($discountAmount > 0) {
        $totalAmount = $amount;

        if ($discountAmount == $totalPaySlipAmt) {

            updateOnDiscountWithStatus(
                $paymentSlipId,
                $discountAmount,
                $totalAmount,
                $delegateId,
                $payment_remark
            );
        }

        updateOnDiscountWithTotal($paymentSlipId, $discountAmount, $totalAmount);
    }
	if (floatval($totalPaySlipAmt) > 0) {

     $val=0;

		// if ($paymentDetails) {

		// 	foreach ($paymentDetails as $key => $val) {
	$paymentDetailsArray[$val]['slip_id'] = $paymentSlipId;
	$paymentDetailsArray[$val]['delegate_id'] = $_REQUEST['delegateId'] ?? '';
	$paymentDetailsArray[$val]['payment_mode'] = $_REQUEST['payment_mode_' . $slipId] ?? '';

	$payment_date = "0000-00-00";
	$card_payment_date = "0000-00-00";

	// CASH RELATED VARIABLES
	$cash_deposit_date = "0000-00-00";

	// CHEQUE RELATED VARIABLES
	$cheque_number = "";
	$cheque_bank_name = "";
	$cheque_date = "0000-00-00";

	// DRAFT RELATED VARIABLES
	$draft_number = "";
	$draft_bank_name = "";
	$draft_date = "0000-00-00";

	// NEFT RELATED VARIABLES
	$neft_bank_name = "";
	$neft_date = "0000-00-00";
	$neft_transaction_no = "";

	// RTGS RELATED VARIABLES 
	$rtgs_bank_name = "";
	$rtgs_date = "0000-00-00";
	$rtgs_transaction_no = "";

	// UPI RELATED VARIABLES
	$upi_bank_name = "";
	$upi_date = "0000-00-00";
	$txn_no = "";

	$payment_status = 'UNPAID';

	// CASH RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "Cash") {
		$cash_deposit_date = $_REQUEST['cash_deposit_date_' . $slipId] ?? "0000-00-00";
	}

	// CHEQUE RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "Cheque") {
		$cheque_number = $_REQUEST['cheque_number_' . $slipId] ?? "";
		$cheque_bank_name = $_REQUEST['cheque_drawn_bank_' . $slipId] ?? "";
		$cheque_date = $_REQUEST['cheque_date_' . $slipId] ?? "0000-00-00";
	}

	// DRAFT RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "Draft") {
		$draft_number = $_REQUEST['draft_number_' . $slipId] ?? "";
		$draft_bank_name = $_REQUEST['draft_drawn_bank_' . $slipId] ?? "";
		$draft_date = $_REQUEST['draft_date_' . $slipId] ?? "0000-00-00";
	}

	// NEFT RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "Neft") {
		$neft_bank_name = $_REQUEST['neft_bank_name_' . $slipId] ?? "";
		$neft_date = $_REQUEST['neft_date_' . $slipId] ?? "0000-00-00";
		$neft_transaction_no = $_REQUEST['neft_transaction_no_' . $slipId] ?? "";
	}

	// CARD RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "Card") {
		$card_transaction_no = $_REQUEST['card_number_' . $slipId] ?? "";
		$card_payment_date = $_REQUEST['card_date_' . $slipId] ?? "0000-00-00";
	}

	// RTGS RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "RTGS") {
		$rtgs_bank_name = $_REQUEST['rtgs_bank_name_' . $slipId] ?? "";
		$rtgs_date = $_REQUEST['rtgs_date_' . $slipId] ?? "0000-00-00";
		$rtgs_transaction_no = $_REQUEST['rtgs_transaction_no_' . $slipId] ?? "";
	}

	// UPI RELATED ASSIGNMENTS
	if ($paymentDetailsArray[$val]['payment_mode'] == "Upi") {
		$upi_bank_name = $_REQUEST['upi_bank_name_' . $slipId] ?? "";
		$upi_date = $_REQUEST['upi_date_' . $slipId] ?? "0000-00-00";
		$txn_no = $_REQUEST['txn_no_' . $slipId] ?? "";
	}

	// Assign to array
	$paymentDetailsArray[$val] = [
		'slip_id' => $paymentSlipId,
        'delegate_id' => $_REQUEST['delegateId'] ?? '',
        'payment_mode' => $_REQUEST['payment_mode_' . $slipId] ?? '',
		'payment_date' => $payment_date,
		'cash_deposit_date' => $cash_deposit_date,
		'card_payment_date' => $card_payment_date,
		'card_transaction_no' => $card_transaction_no ?? '',
		'cheque_number' => $cheque_number,
		'cheque_date' => $cheque_date,
		'cheque_bank_name' => $cheque_bank_name,
		'draft_number' => $draft_number,
		'draft_date' => $draft_date,
		'draft_bank_name' => $draft_bank_name,
		'neft_bank_name' => $neft_bank_name,
		'neft_transaction_no' => $neft_transaction_no,
		'neft_date' => $neft_date,
		'rtgs_bank_name' => $rtgs_bank_name,
		'rtgs_transaction_no' => $rtgs_transaction_no,
		'rtgs_date' => $rtgs_date,
		'upi_bank_name' => $upi_bank_name,
		'upi_date' => $upi_date,
		'txn_no' => $txn_no,
		'payment_remark' => $_REQUEST['payment_remark_' . $slipId] ?? '',
		'payment_status' => $payment_status,
		'currency' => getRegistrationCurrency(getUserClassificationId($_REQUEST['delegateId'] ?? 0)),
		'amount' => $_REQUEST['amount_' . $slipId] ?? 0
	];

	if (($_REQUEST['discountAmount_' . $slipId] ?? 0) != $totalPaySlipAmt) {
		$paymentId = insertingMultiPaymentDetails($paymentDetailsArray);
	}
   
    }
    // }
	if ($userREGtype == 'SPOT') {
		if ($paymentId != '') {
			$slipId = $slipId;

			$paymentDetailsAmount = invoiceAmountOfSlip($slipId);
			$payment_date = date('Y-m-d');

			$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
														  SET `payment_status` = 'PAID',
														  `collected_by` = '" . $mycms->getLoggedUserId() . "',
														  `payment_type` = 'SPOT',
														  `payment_date` = '" . $payment_date . "'
																  WHERE `id` = '" . $paymentId[0] . "' 
																  AND `status` = 'A' "; // $paymentId ->  $paymentId[0] 16 July 2025
			$mycms->sql_update($sqlUpdatePaymentRequest, false);

			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $slipId . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$activeInvoice = invoiceDetailsActiveOfSlip($slipId);
			foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
				if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
					$workshopId = $valActiveInvoice['refference_id'];

					$sqlUpdateSlip = array();
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate = array();
					$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															SET `workshop_payment_status` = 'PAID'
														  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

					$mycms->sql_update($sqlUpdate, false);
				}

				if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
					$sqlUpdateSlip = array();
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															SET `registration_payment_status` = 'PAID'
														  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);
				}

				if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
					if ($valActiveInvoice['payment_status'] == 'UNPAID') {
						$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

						$mycms->sql_update($sqlUpdateSlip, false);

						$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = 'PAID'
													  WHERE `delegate_id` = '" . $valActiveInvoice['delegate_id'] . "'";

						$mycms->sql_update($sqlUpdateWorkshop, false);


						$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `workshop_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

						$mycms->sql_update($sqlUpdate, false);
					}
				}

				if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `registration_payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);
				}
				if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `accommodation_payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

					$mycms->sql_update($sqlUpdate, false);
				}
				if ($valActiveInvoice['service_type'] == 'DELEGATE_TOUR_REQUEST') {
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_TOUR_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `tour_payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

					$mycms->sql_update($sqlUpdate, false);
				}
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
														SET `payment_status` = 'PAID',
															`invoice_request` = 'SPOT'
														WHERE `id` = '" . $valActiveInvoice['id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);
			}
		}
	}

	$mycms->removeSession('SLIP_ID');

	// //offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId,'SEND','BACK');
	// if ($redirect) {
	// 	if ($userREGtype == 'SPOT') {
	// 		pageRedirection(_BASE_URL_ . "/webmaster/spot_registration.php");
	// 	} else {
	// 			pageRedirection("registration.php", "Payment Set Successfully");

	// 	}
	// } else {
		return $paymentId;
	// }
}
function setPaymentArea($mycms, $cfg, $redirect = true)
{
	$loggedUserID = $mycms->getLoggedUserId();

	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.workshop.php');

	$paymentDetails = $_REQUEST;
	$paymentSlipId = $_REQUEST['slipId'];
	$userREGtype = $_REQUEST['userREGtype'];
	 

	/*echo 'dis=='. $_RE
	QUEST['discountAmount'];
		echo 'amnt='. $totalPaySlipAmt = invoiceAmountOfSlip($paymentSlipId);

		die;*/

	$totalPaySlipAmt = invoiceAmountOfSlip($paymentSlipId);
	if ($_REQUEST['discountAmount'] > 0) {
		$discountAmount = $_REQUEST['discountAmount'];
		$totalAmount = $_REQUEST['amount'];
		//updateonDiscount($slipId, $discountAmount);
		//die();
		if ($_REQUEST['discountAmount'] == $totalPaySlipAmt) {

			updateOnDiscountWithStatus($paymentSlipId, $discountAmount, $totalAmount, $_REQUEST['delegate_id'], $_REQUEST['payment_remark']);
		}


		updateOnDiscountWithTotal($paymentSlipId, $discountAmount, $totalAmount);
	}

	if (floatval($totalPaySlipAmt) > 0) {

     $val=0;

		// if ($paymentDetails) {

		// 	foreach ($paymentDetails as $key => $val) {
				$paymentDetailsArray[$val]['slip_id'] = $paymentSlipId;
				$paymentDetailsArray[$val]['delegate_id'] = $_REQUEST['delegateId'];

				$paymentDetailsArray[$val]['payment_mode'] = $_REQUEST['payment_mode'];


				$payment_date = "0000-00-00";
				$card_payment_date = "0000-00-00";

				// CASH RELATED VARRIABLES
				$cash_deposit_date = "0000-00-00";

				// CHEQUE RELATED VARRIABLES
				$cheque_number = "";
				$cheque_bank_name = "";
				$cheque_date = "0000-00-00";

				// DRAFT RELATED VARRIABLES
				$draft_number = "";
				$draft_bank_name = "";
				$draft_date = "0000-00-00";

				// NEFT RELATED VARRIABLES
				$neft_bank_name = "";
				$neft_date = "0000-00-00";
				$neft_transaction_no = "";

				// RTGS RELATED VARRIABLES 
				$rtgs_bank_name = "";
				$rtgs_date = "0000-00-00";
				$rtgs_transaction_no = "";


				// CASH RELATED ASSIGNMENTS
				if ($paymentDetails['payment_mode'] == "Cash") {
					$cash_deposit_date = $paymentDetails['cash_deposit_date'];
					$payment_status = 'UNPAID';
				}

				// CHEQUE RELATED ASSIGNMENTS
				if ($paymentDetails['payment_mode'] == "Cheque") {
					$cheque_number = $paymentDetails['cheque_number'];
					$cheque_bank_name = $paymentDetails['cheque_drawn_bank'];
					$cheque_date = $paymentDetails['cheque_date'];
					$payment_status = 'UNPAID';
				}

				// DRAFT RELATED ASSIGNMENTS
				if ($paymentDetails['payment_mode'] == "Draft") {
					$draft_number = $paymentDetails['draft_number'];
					$draft_bank_name = $paymentDetails['draft_drawn_bank'];
					$draft_date = $paymentDetails['draft_date'];
					$payment_status = 'UNPAID';
				}

				// NEFT RELATED ASSIGNMENTS
				if ($paymentDetails['payment_mode']== "Neft") {
					$neft_bank_name = $paymentDetails['neft_bank_name'];
					$neft_date = $paymentDetails['neft_date'];
					$neft_transaction_no = $paymentDetails['neft_transaction_no'];
					$payment_status = 'UNPAID';
				}

				// CARD RELATED ASSIGNMENTS
				if ($paymentDetails['payment_mode'] == "Card") {
					$card_transaction_no = $paymentDetails['card_number'];
					$card_payment_date = $paymentDetails['card_date'];
					$payment_status = 'UNPAID';
				}

				// RTGS RELATED ASSIGNMENTS
				if ($paymentDetails['payment_mode'][$key] == "RTGS") {
					$rtgs_bank_name = $paymentDetails['rtgs_bank_name'];
					$rtgs_date = $paymentDetails['rtgs_date'];
					$rtgs_transaction_no = $paymentDetails['rtgs_transaction_no'];
					$payment_status = 'UNPAID';
				}

				if ($paymentDetails['payment_mode'] == "Upi") {
					$upi_bank_name = $paymentDetails['upi_bank_name'];
					$upi_date = $paymentDetails['upi_date'];
					$txn_no = $paymentDetails['txn_no'];
					$payment_status = 'UNPAID';
				}

				$paymentDetailsArray[$val]['payment_date'] = $payment_date;
				$paymentDetailsArray[$val]['cash_deposit_date'] = $cash_deposit_date;
				$paymentDetailsArray[$val]['card_payment_date'] = $card_payment_date;
				$paymentDetailsArray[$val]['card_transaction_no'] = $card_transaction_no;
				$paymentDetailsArray[$val]['cheque_number'] = $cheque_number;
				$paymentDetailsArray[$val]['cheque_date'] = $cheque_date;
				$paymentDetailsArray[$val]['cheque_bank_name'] = $cheque_bank_name;
				$paymentDetailsArray[$val]['draft_number'] = $draft_number;
				$paymentDetailsArray[$val]['draft_date'] = $draft_date;
				$paymentDetailsArray[$val]['draft_bank_name'] = $draft_bank_name;
				$paymentDetailsArray[$val]['neft_bank_name'] = $neft_bank_name;
				$paymentDetailsArray[$val]['neft_transaction_no'] = $neft_transaction_no;
				$paymentDetailsArray[$val]['neft_date'] = $neft_date;
				$paymentDetailsArray[$val]['rtgs_bank_name'] = $rtgs_bank_name;
				$paymentDetailsArray[$val]['rtgs_transaction_no'] = $rtgs_transaction_no;
				$paymentDetailsArray[$val]['rtgs_date'] = $rtgs_date;
				$paymentDetailsArray[$val]['upi_bank_name'] = $upi_bank_name;
				$paymentDetailsArray[$val]['upi_date'] = $upi_date;
				$paymentDetailsArray[$val]['txn_no'] = $txn_no;
				$paymentDetailsArray[$val]['payment_remark'] = $_REQUEST['payment_remark'];
				$paymentDetailsArray[$val]['payment_status'] = $payment_status;
				$paymentDetailsArray[$val]['currency'] = getRegistrationCurrency(getUserClassificationId($_REQUEST['delegate_id']));
				$paymentDetailsArray[$val]['amount'] = $paymentDetails['amount'];
			}
         
			if ($_REQUEST['discountAmount'] == $totalPaySlipAmt) {
			} else {
				$paymentId = insertingMultiPaymentDetails($paymentDetailsArray);
			}
    //     }
    // }
	if ($userREGtype == 'SPOT') {
		if ($paymentId != '') {
			$slipId = $mycms->getSession('SLIP_ID');

			$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
			$payment_date = date('Y-m-d');

			$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
														  SET `payment_status` = 'PAID',
														  `collected_by` = '" . $mycms->getLoggedUserId() . "',
														  `payment_type` = 'SPOT',
														  `payment_date` = '" . $payment_date . "',
														  `amount` = '" . $paymentDetailsAmount . "'
																  WHERE `id` = '" . $paymentId[0] . "' 
																  AND `status` = 'A' "; // $paymentId ->  $paymentId[0] 16 July 2025
			$mycms->sql_update($sqlUpdatePaymentRequest, false);

			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $slipId . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$activeInvoice = invoiceDetailsActiveOfSlip($slipId);
			foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
				if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
					$workshopId = $valActiveInvoice['refference_id'];

					$sqlUpdateSlip = array();
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate = array();
					$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															SET `workshop_payment_status` = 'PAID'
														  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

					$mycms->sql_update($sqlUpdate, false);
				}

				if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
					$sqlUpdateSlip = array();
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															SET `registration_payment_status` = 'PAID'
														  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);
				}

				if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
					if ($valActiveInvoice['payment_status'] == 'UNPAID') {
						$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

						$mycms->sql_update($sqlUpdateSlip, false);

						$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = 'PAID'
													  WHERE `delegate_id` = '" . $valActiveInvoice['delegate_id'] . "'";

						$mycms->sql_update($sqlUpdateWorkshop, false);


						$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `workshop_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

						$mycms->sql_update($sqlUpdate, false);
					}
				}

				if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `registration_payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);
				}
				if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `accommodation_payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

					$mycms->sql_update($sqlUpdate, false);
				}
				if ($valActiveInvoice['service_type'] == 'DELEGATE_TOUR_REQUEST') {
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_TOUR_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
													SET `tour_payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

					$mycms->sql_update($sqlUpdate, false);
				}
				if ($valActiveInvoice['payment_status'] == 'UNPAID') {
					$sqlUpdateSlip = array();
					$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
															SET `payment_status` = 'PAID'
														  WHERE `id` = '" . $valActiveInvoice['id'] . "'";

					$mycms->sql_update($sqlUpdateSlip, false);
				}
			}
		}
	}

	$mycms->removeSession('SLIP_ID');

	//offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId,'SEND','BACK');
	// if ($redirect) {
	// 	if ($userREGtype == 'SPOT') {
	// 		pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $_REQUEST['delegate_id'] . "&paymentId=" . $paymentId, "1");
	// 	} else {
	// 			pageRedirection("registration.php", "Payment Set Successfully");

	// 	}
	// } else {
		return $paymentId;
	// }
}
function getRegistrationDetails($mycms, $cfg)
{
	$loggedUserId = $mycms->getLoggedUserId();
	$delegate_id = $_REQUEST['id'];
	$rowFetchUser = getUserDetails($delegate_id);

	$serviceSummary = array();

	$hasUnpaidBill = false;
	$isResReg = false;

	$sqlFetchInvoice = getRegistrationInvoiceCancelInvoiceDetails("", $delegate_id, "");

	//echo '<pre>'; print_r($sqlFetchInvoice);																
	$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);
	//echo '<pre>'; print_r($resultFetchInvoice ); echo '</pre>'; 

	if ($resultFetchInvoice) {
		$accommodationRecords = array();

		foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
			//echo '<pre>'; print_r($rowFetchInvoice['Refund_status']);
			$showTheRecord = true;

			$invoiceCounter++;
			$slip = getInvoice($rowFetchInvoice['slip_id']);
			$returnArray = discountAmount($rowFetchInvoice['id']);
			$percentage = $returnArray['PERCENTAGE'];
			$totalAmount = $returnArray['TOTAL_AMOUNT'];
			$discountAmount = $returnArray['DISCOUNT'];

			//echo '<pre>'; print_r($rowFetchInvoice ); echo '</pre>'; 

			$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
			$thisUserClasfId = getUserClassificationId($rowFetchInvoice['delegate_id']);
			$thisUserClasfName = getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']));

			$type = "";
			if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "CONFERENCE");
				$serviceSummary[$rowFetchInvoice['id']] = '<li class="badge_padding badge_primary"><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;CONFERENCE REGISTRATION<br>';
			}
			if ($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				$isResReg = true;
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "RESIDENTIAL");
				$serviceSummary[$rowFetchInvoice['id']] = '<li class="badge_padding badge_info"><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;' . $type;

				$sqlAccomm = array();
				$sqlAccomm['QUERY'] = " SELECT accomm.*, hotel.hotel_name
																 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
														   INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
																   ON accomm.hotel_id = hotel.id
																WHERE accomm.`user_id` = ?
																  AND accomm.`refference_invoice_id` = ?";
				$sqlAccomm['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegate_id, 'TYP' => 's');
				$sqlAccomm['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $rowFetchInvoice['id'], 'TYP' => 's');
				$resAccomm = $mycms->sql_select($sqlAccomm);

				foreach ($resAccomm as $kk => $row) {
					$serviceSummary[$rowFetchInvoice['id'] . 'accm'] = '';

					$accommodationRecords[$rowFetchInvoice['id']]['KEY'] = $rowFetchInvoice['id'] . 'accm';
					$accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] = 'RES-PACK';
					$accommodationRecords[$rowFetchInvoice['id']]['accomId'] = $row['id'];
					$accommodationRecords[$rowFetchInvoice['id']]['packageId'] = $cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId];
					$accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] = $row['hotel_name'];
					$accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] = $row['checkin_date'];
					$accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] = $row['checkout_date'];

					$accommodationRecords[$rowFetchInvoice['id']]['WILL-SHARE'] = false;
					$accommodationRecords[$rowFetchInvoice['id']]['SHARE'] = array();

					//$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';

					if (in_array($thisUserClasfId, $cfg['RESIDENTIAL_SHARING_CLASF_ID'])) {
						$accommodationRecords[$rowFetchInvoice['id']]['WILL-SHARE'] = true;
						$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['KEY'] = $rowFetchInvoice['id'] . 'accmshr';
						$serviceSummary[$rowFetchInvoice['id'] . 'accmshr'] = '';

						if (trim($row['preffered_accommpany_name']) != '') {
							$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['accomId'] = $row['id'];
							$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefName'] = $row['preffered_accommpany_name'];
							$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefMobile'] = $row['preffered_accommpany_mobile'];
							$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefEmail'] = $row['preffered_accommpany_email'];
							/*$serviceSummary[$rowFetchInvoice['id'].'accmshr'] = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" onClick="openSharePrefEditPopup(this)" accomId="'.$row['id'].'" prefName="'.$row['preffered_accommpany_name'].'"  prefMobile="'.$row['preffered_accommpany_mobile'].'"  prefEmail="'.$row['preffered_accommpany_email'].'"></i>&nbsp;'.$row['preffered_accommpany_name'].'<br>
																					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-phone" aria-hidden="true"></i>&nbsp;'.$row['preffered_accommpany_mobile'].'<br>
																					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;'.$row['preffered_accommpany_email'].'';*/
						} else {
							$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['accomId'] = $row['id'];
							//$serviceSummary[$rowFetchInvoice['id'].'accmshr'] = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" onClick="openSharePrefEditPopup(this)" accomId="'.$row['id'].'"></i>&nbsp;-';
						}
					}
				}
			}
			if ($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'], true);
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "WORKSHOP");
				if ($workShopDetails['showInInvoices'] != 'Y') {
					$showTheRecord = false;
				}
				$serviceSummary[$rowFetchInvoice['id']] = '<li class="badge_padding badge_info"><i class="fa fa-stethoscope" aria-hidden="true"></i><span>&nbsp;' . $workShopDetails['classification_title'] . "<br>";
			}
			if ($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "ACCOMPANY");
				$accompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
				if ($accompanyDetails['registration_request'] == 'GUEST') {
					$serviceSummary[$rowFetchInvoice['id']] = '<li class="badge_padding badge_secondary"><i class="fa fa-smile-o" aria-hidden="true"></i><span>&nbsp;Accompaning Guest - ' . $accompanyDetails['user_full_name'] . "<br>";
				} else {
					$serviceSummary[$rowFetchInvoice['id']] = '<li class="badge_padding badge_secondary"><i class="fa fa-users" aria-hidden="true"></i><span>&nbsp;Accompany - ' . $accompanyDetails['user_full_name'] . "<br>";
				}
			}
			if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "ACCOMMODATION");
				//$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-building" aria-hidden="true"></i>&nbsp;Accomodation';

				//$showTheRecord = false;
				$sqlAccomm = array();
				$sqlAccomm['QUERY'] = " SELECT accomm.*, hotel.hotel_name
																 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
														   INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
																   ON accomm.hotel_id = hotel.id
																WHERE accomm.`user_id` = ?
																  AND accomm.`refference_invoice_id` = ?";
				$sqlAccomm['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegate_id, 'TYP' => 's');
				$sqlAccomm['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $rowFetchInvoice['id'], 'TYP' => 's');
				$resAccomm = $mycms->sql_select($sqlAccomm);
				//echo "<pre>";print_r($resAccomm);echo "</pre>";
				foreach ($resAccomm as $kk => $row) {
					$accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] = 'ACCOMMODATION';
					$accommodationRecords[$rowFetchInvoice['id']]['accomId'] = $row['id'];
					$accommodationRecords[$rowFetchInvoice['id']]['packageId'] = $row['package_id'];
					$accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] = $row['hotel_name'];
					$accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] = $row['checkin_date'];
					$accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] = $row['checkout_date'];
					$accommodationRecords[$rowFetchInvoice['id']]['SHARE'] = array();
					//$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';
				}

				$serviceSummary[$rowFetchInvoice['id']] = '<li ><i class="fa fa-building" aria-hidden="true"></i><span>&nbsp;Accommodation @ ' . $accommodationRecords[$rowFetchInvoice['id']]['hotel_name'];
			}
			if ($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST") {
				$tourDetails = getTourDetails($invoiceDetails['refference_id']);
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "TOUR");
				$serviceSummary[$rowFetchInvoice['id']] = '<li><i class="fa fa-bus" aria-hidden="true"></i><span>&nbsp;Tour';
			}
			if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
				$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "DINNER");
				$serviceSummary[$rowFetchInvoice['id']] = '<li class="badge_padding badge_danger"><i class="fa fa-cutlery" aria-hidden="true"></i><span>&nbsp;Dinner<br>';
			}
			if (isset($rowFetchInvoice['Refund_status']) && $rowFetchInvoice['Refund_status'] == 'Not_refunded') {
				$rowBackGround = "#50392d";
			} elseif (isset($rowFetchInvoice['Refund_status']) && $rowFetchInvoice['Refund_status'] == 'Refunded') {
				$rowBackGround = "#502d2d";
			} else {
				$rowBackGround = "#2a2626;";
			}

		}
	}


	$sqlPickupDropOff = array();
	$sqlPickupDropOff['QUERY'] = " SELECT *
										    FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " 
										   WHERE `user_id` = ?";
	$sqlPickupDropOff['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegate_id, 'TYP' => 's');
	$resPickupDropOff = $mycms->sql_select($sqlPickupDropOff);

	foreach ($resPickupDropOff as $kk => $row) {
		if ($row['pikup_time'] != '') {
			$serviceSummary[$rowFetchInvoice['id'] . '.' . $kk . 'p'] = 'Pickup on ' . $row['pikup_time'];
		}
		if ($row['dropoff_time'] != '') {
			$serviceSummary[$rowFetchInvoice['id'] . '.' . $kk . 'd'] = 'Dropoff on ' . $row['dropoff_time'];
		}
	}
	?>



	<ul class="service_ul">

		<?= implode('</span></li>', $serviceSummary) ?>
	</ul>
	<?php



	exit();
}
//view details in pop up

function ViewProfileDetails($mycms, $cfg)
{ ?>

	<?php
	$loggedUserId = $mycms->getLoggedUserId();
	$delegate_id = $_REQUEST['id'];

	$userData = getUserDetails($delegate_id);

	if (empty($userData)) {
		echo '<p>User not found</p>';
		exit;
	}
	$cutoffArray = array();
	$sqlCutoff['QUERY'] = " SELECT * 
									 FROM " . _DB_TARIFF_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
	$sqlCutoff['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resCutoff = $mycms->sql_select($sqlCutoff);
	if ($resCutoff) {
		foreach ($resCutoff as $i => $rowCutoff) {
			$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
		}
	}

	$workshopCutoffArray = array();
	$sqlWorkshopCutoff['QUERY'] = " SELECT * 
									 FROM " . _DB_WORKSHOP_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
	$sqlWorkshopCutoff['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resWorkshopCutoff = $mycms->sql_select($sqlWorkshopCutoff);
	if ($resWorkshopCutoff) {
		foreach ($resWorkshopCutoff as $i => $rowWorkshopCutoff) {
			$workshopCutoffArray[$rowWorkshopCutoff['id']] = $rowWorkshopCutoff['cutoff_title'];
		}
	}

	$currentCutoffId = getTariffCutoffId();

	$conferenceTariffArray = getAllRegistrationTariffs("", false);

	$workshopDetailsArray = getAllWorkshopTariffs();
	$workshopCountArr = totalWorkshopCountReport();

	$userREGtype = $_REQUEST['userREGtype'];
	$abstractDelegateId = $_REQUEST['delegateId'];
	$userRec = $userData;


	global $cfg, $mycms;

	// include_once('../../includes/function.registration.php');
	// include_once('../../includes/function.workshop.php');

	$sqlConfDate = array();
	$sqlConfDate['QUERY'] = " SELECT MIN(conf_date) AS startDate, MAX(conf_date) AS endDate
								FROM " . _DB_CONFERENCE_DATE_ . " 
								WHERE `status` = ?";
	$sqlConfDate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resConfDate = $mycms->sql_select($sqlConfDate);
	$rowConfDate = $resConfDate[0];




	//accompany

	$accompanyTariffMap = [];
	$sqlaccompanyCutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_ACCOMPANY_ . " WHERE `status` = ?";

	$sqlaccompanyCutoff['PARAM'][] = [
		'FILD' => 'status',
		'DATA' => 'A',
		'TYP' => 's'
	];

	$resaccompanyCutoff = $mycms->sql_select($sqlaccompanyCutoff);

	if ($resaccompanyCutoff) {
		foreach ($resaccompanyCutoff as $row) {

			$accompanyTariffMap[$row['tariff_cutoff_id']] = [
				'amount' => $row['amount'],
				'currency' => $row['currency']
			];
		}
	}
	//print_r($accompanyTariffMap);
	?>
	<div class="profile_pop_up">
		<div class="profile_pop_left">
			<div class="profile_left_box text-center ">
				<div class="regi_img_circle m-auto">
					<?php
					$fname = $userData['user_first_name'];
					$lname = $userData['user_last_name'];
					$initials = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1)); ?>
					<span><?php echo $initials; ?></span>
				</div>
				<h5><?= strtoupper($userData['user_full_name']) ?></h5>
				<h6><?php
				if ($userData['registration_payment_status'] != "UNPAID") {
					echo $userData['user_registration_id'];
				} else {
					echo "-";
				}
				?></h6>
				<div class="regi_type justify-content-center">
					<span class="badge_padding badge_primary"><?php
					if ($userData['isRegistration'] == "Y") {
						if (empty(getRegClsfName($userData['registration_classification_id']))) {
							$regClsName = getRegClsfComboName($userData['registration_classification_id']);
						} else {
							$regClsName = getRegClsfName($userData['registration_classification_id']);
						}
						echo $regClsName;

					}
					?></span>
					<?php if (getCutoffName($userData['registration_tariff_cutoff_id'])) { ?>
						<span class="badge_padding badge_secondary">
							<?php
							echo getCutoffName($userData['registration_tariff_cutoff_id']);

							?></span>
					<?php } ?>

				</div>
			</div>
			<div class="profile_left_box">
				<h4>Contact Info</h4>
				<ul>
					<li>
						<?php email(); ?>
						<p>
							<b>Email</b>
							<span><?= $userData['user_email_id'] ?></span>
						</p>
					</li>
					<li>
						<?php call(); ?>
						<p>
							<b>Phone</b>
							<span> <?= $userData['user_mobile_no'] ?></span>
						</p>
					</li>
					<li>
						<?php calendar(); ?>
						<p>
							<b>Registered On</b>
							<span><?= date('d/m/Y h:i A', strtotime($userData['created_dateTime'])) ?></span>
						</p>
					</li>
				</ul>
			</div>
			<div class="profile_left_box">
				<h4>Address</h4>
				<ul>
					<li>
						<?php address(); ?>
						<p>
							<b>Location</b>
							<span><?= $userData['user_city'] ?> 	<?= $userData['state_name'] ?>,
								<?= $userData['user_pincode'] ?>, <?= $userData['country_name'] ?></span>
						</p>
					</li>
					<li>
						<i class="fal fa-tags"></i>
						<p>
							<b>Tags</b>
							<span><?= $userData['tags'] ?></span>
						</p>
					</li>
				</ul>
			</div>
			<div class="profile_left_box">
				<div class="profile_uniq_sque">
					Unique Sequence<span><?php
					echo $userData['user_unique_sequence'];
					?></span>
				</div>
			</div>
		</div>
		<div class="profile_pop_right">
			<div class="profile_pop_right_heading">
				<span>Registration Details</span>
				<p>
					<a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php export(); ?></a>
					<a href="javascript:void(null)" class="popup_close popup_close_profile  icon_hover badge_danger action-transparent"><?php close(); ?></a>
				</p>
			</div>
			<div class="profile_pop_right_body">
				<ul class="profile_payments_grid_ul">

					<li>
						<h6>Total Amount</h6>
						<h5> <?php
						$paymentCounter = 0;
						$delegateId = $userData['id'];
						$resFetchSlip = slipDetailsOfUser($delegateId, true);
						// print_r($resFetchSlip);
						$styleCss = 'style="display:none;"';

						foreach ($resFetchSlip as $key => $rowFetchSlip) {

							$counter++;
							$resPaymentDetails = paymentDetails($rowFetchSlip['id']);

							$paymentDescription = "-";
							if ($key == 0) {
								$paymentId = $resPaymentDetails['id'];
								$slipId = $rowFetchSlip['id'];
							}
							$isChange = "YES";
							$excludedAmount = invoiceAmountOfSlip($rowFetchSlip['id'], false, false);

							$amount = invoiceAmountOfSlip($rowFetchSlip['id']);
							$discountDeductionAmount = ($excludedAmount - $amount);

							foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
								if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
									$discountAmountofSlip = $discountDeductionAmount;
								} else {
									$discountAmountofSlip = ($discountDeductionAmount / 1.18);
								}
							}

							$DiscountAmount = invoiceDiscountAmountOfSlip($rowFetchSlip['id'], true);

						}

						if ($resPaymentDetails['totalAmount'] > 0) {
							echo $rowFetchSlip['currency'] . '&nbsp;' . number_format($resPaymentDetails['totalAmount'], 2);
							// echo $rowFetchSlip['currency'] . '&nbsp;' . number_format($amount, 2);
						} else {
							echo $rowFetchSlip['currency'] . '&nbsp;' . "0.00";
						}
						?></h5>
					</li>
					<li>
						<h6>Payment Status</h6>
						<h5>
							<?php if ($rowFetchSlip['payment_status'] === 'PAID') { ?>
								<span class="mi-1 badge_padding badge_success w-max-con text-uppercase"><?php paid() ?><?= $rowFetchSlip['payment_status']; ?>
								</span>

							<?php } ?>
							<?php if ($rowFetchSlip['payment_status'] === 'UNPAID') { ?>
								<span class="mi-1 badge_padding badge_danger w-max-con text-uppercase"><?php unpaid(); ?><?= $rowFetchSlip['payment_status']; ?>
								</span>

							<?php } ?>
						</h5>
					</li>
					<li>
						<h6>Invoice No.</h6>
						<h5><?php echo $rowFetchSlip['invoice_number']; ?></h5>
						<small class="text-muted">UPI TXN ID:00000001</small>
					</li>
				</ul>
				<div class="service_breakdown_wrap">
					<h4><?php windowi(); ?>Service Breakdown</h4>
					<div class="table_wrap">
						<table>
							<thead>
								<tr>
									<th>Service / Item</th>
									<th>Category</th>
									<th class="text-right">Amount</th>
									<th class="text-right">Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$sqlFetchInvoice = getRegistrationInvoiceCancelInvoiceDetails("", $delegate_id, "");
								$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);

								if ($resultFetchInvoice) {

									foreach ($resultFetchInvoice as $row) {

										$accompanyDetails = getUserDetails($row['refference_id']);

										/* Workshop visibility check */
										if ($row['service_type'] === 'DELEGATE_WORKSHOP_REGISTRATION') {
											$ws = getWorkshopDetails($row['refference_id'], true);
											if ($ws['showInInvoices'] !== 'Y') {
												continue;
											}
										}

										/* -------------------------
										 * Service Name
										 * -------------------------*/
										switch ($row['service_type']) {

											case 'DELEGATE_CONFERENCE_REGISTRATION':
												$serviceName = 'Conference Registration';
												$category = 'Delegate';
												break;

											case 'DELEGATE_WORKSHOP_REGISTRATION':
												$serviceName = $ws['classification_title'];
												$category = 'Workshop';
												break;

											case 'DELEGATE_DINNER_REQUEST':
												$serviceName = 'Gala Dinner';
												$category = 'Delegate';
												break;

											case 'ACCOMPANY_CONFERENCE_REGISTRATION':
												if ($accompanyDetails['registration_request'] == 'GUEST') {
													$serviceName = 'Accompanying Guest - ' . $accompanyDetails['user_full_name'];
													$row['currency'] = 'INR';
												} else {
													$serviceName = 'Accompany - ' . $accompanyDetails['user_full_name'];
													$row['currency'] = 'INR';
												}
												$category = 'Accompany';
												break;

											case 'DELEGATE_ACCOMMODATION_REQUEST':
												$serviceName = 'Accommodation';
												$category = 'Delegate';
												break;

											case 'DELEGATE_TOUR_REQUEST':
												$serviceName = 'Tour';
												$category = 'Delegate';
												break;

											default:
												$serviceName = 'Other';
												$category = '-';
										}

										/* -------------------------
										 * Amount
										 * -------------------------*/
										$amountArr = discountAmount($row['id']);
										$amount = number_format($amountArr['TOTAL_AMOUNT'], 2);

										/* -------------------------
										 * Mode
										 * -------------------------*/
										$mode = $row['invoice_mode'];
										if ($row['invoice_mode'] === 'OFFLINE') {
											$mode .= ' (' . $row['reg_type'] . ')';
										}


										?>
										<tr>
											<td>
												<?= $serviceName ?><br>
												<small class="text-muted">
													<?= $row['invoice_number']; ?>
												</small>
											</td>

											<td>
												<?= $category ?>
												<span class="badge_padding badge_dark">
													<?= $mode; ?>
												</span>
											</td>

											<td class="text-right">
												<?= $row['currency']; ?>
												<?= $amount; ?>
											</td>

											<td class="text-right">
												<?php if ($row['payment_status'] === 'PAID') { ?>
													<span class="mi-1 badge_padding badge_success w-max-con text-uppercase"><?php paid() ?><?= $row['payment_status']; ?>
													</span>

												<?php } ?>
												<?php if ($row['payment_status'] === 'UNPAID') { ?>
													<span class="mi-1 badge_padding badge_danger w-max-con text-uppercase"><?php unpaid(); ?><?= $row['payment_status']; ?>
													</span>

												<?php } ?>

											</td>
										</tr>
										<?php
									}
								} else {
									?>
									<tr>
										<td colspan="4" class="text-center">No services found</td>
									</tr>
								<?php } ?>

							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>

	<?php



	exit();
}
//edit details new in pop up
function registrationEditDetails($mycms, $cfg)
{ ?>
	<script language="javascript" src="<?= _BASE_URL_ ?>js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/dinner_registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/accompany_registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/workshop_registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
	<script type="text/javascript" language="javascript"
		src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.tariff.js"></script>

	<script type="text/javascript" language="javascript"
		src="<?= _BASE_URL_ ?>webmaster/section_login/scripts/CountryStateRetriver.js"></script>
	<?php
	$loggedUserId = $mycms->getLoggedUserId();
	$delegate_id = $_REQUEST['id'];

	$userData = getUserDetails($delegate_id);

	if (empty($userData)) {
		echo '<p>User not found</p>';
		exit;
	}
	$cutoffArray = array();
	$sqlCutoff['QUERY'] = " SELECT * 
									 FROM " . _DB_TARIFF_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
	$sqlCutoff['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resCutoff = $mycms->sql_select($sqlCutoff);
	if ($resCutoff) {
		foreach ($resCutoff as $i => $rowCutoff) {
			$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
		}
	}

	$workshopCutoffArray = array();
	$sqlWorkshopCutoff['QUERY'] = " SELECT * 
									 FROM " . _DB_WORKSHOP_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
	$sqlWorkshopCutoff['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resWorkshopCutoff = $mycms->sql_select($sqlWorkshopCutoff);
	if ($resWorkshopCutoff) {
		foreach ($resWorkshopCutoff as $i => $rowWorkshopCutoff) {
			$workshopCutoffArray[$rowWorkshopCutoff['id']] = $rowWorkshopCutoff['cutoff_title'];
		}
	}

	$currentCutoffId = getTariffCutoffId();

	$conferenceTariffArray = getAllRegistrationTariffs("", false);

	$workshopDetailsArray = getAllWorkshopTariffs();
	$workshopCountArr = totalWorkshopCountReport();

	$userREGtype = $_REQUEST['userREGtype'];
	$abstractDelegateId = $_REQUEST['delegateId'];
	$userRec = $userData;
	//print_r($userRec);


	global $cfg, $mycms;

	// include_once('../../includes/function.registration.php');
	// include_once('../../includes/function.workshop.php');

	$sqlConfDate = array();
	$sqlConfDate['QUERY'] = " SELECT MIN(conf_date) AS startDate, MAX(conf_date) AS endDate
								FROM " . _DB_CONFERENCE_DATE_ . " 
								WHERE `status` = ?";
	$sqlConfDate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$resConfDate = $mycms->sql_select($sqlConfDate);
	$rowConfDate = $resConfDate[0];




	//accompany

	$accompanyTariffMap = [];
	$sqlaccompanyCutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_ACCOMPANY_ . " WHERE `status` = ?";

	$sqlaccompanyCutoff['PARAM'][] = [
		'FILD' => 'status',
		'DATA' => 'A',
		'TYP' => 's'
	];

	$resaccompanyCutoff = $mycms->sql_select($sqlaccompanyCutoff);

	if ($resaccompanyCutoff) {
		foreach ($resaccompanyCutoff as $row) {

			$accompanyTariffMap[$row['tariff_cutoff_id']] = [
				'amount' => $row['amount'],
				'currency' => $row['currency']
			];
		}
	}
	//print_r($accompanyTariffMap);


	?>

	<script>

		jQuery(document).ready(function ($) {
			$(document).on('click', '.CheckAllWorkshop', function (e) {

				e.preventDefault();

				let $workshops = $(".workshop_check input[type='checkbox']");
				let allChecked = $workshops.length === $workshops.filter(":checked").length;

				// Toggle
				$workshops.prop("checked", !allChecked).trigger("change");

				// Optional: change text
				$(this).text(allChecked ? "Select All" : "Unselect All");
			});

		});
	</script>

	<div class="registration-pop_body">
		<input type="hidden" name="act" value="combinedEditRegistrationProcess" />

		<input type="hidden" name="process_id" value="<?= $_REQUEST['processId'] ?>" />

		<input type="hidden" name="delegate_id" value="<?= $userRec['id'] ?>">

		<div class="registration-pop_body_box">
			<div class="registration-pop_body_box_inner">
				<h4 class="registration-pop_body_box_heading">
					<span><?php user(); ?>Delegate Profile</span>
				</h4>
				<div class="form_grid">
					<div class="frm_grp span_1">
						<p class="frm-head">Title</p>
						<select name="user_initial_title" id="user_initial_title" tabindex="4" required>
							<option value="Dr" <?= ($userRec['user_initial_title'] == 'Dr') ? 'selected' : '' ?>>Dr
							</option>
							<option value="Prof" <?= ($userRec['user_initial_title'] == 'Prof') ? 'selected' : '' ?>>Prof
							</option>
							<option value="Mr" <?= ($userRec['user_initial_title'] == 'Mr') ? 'selected' : '' ?>>Mr
							</option>
							<option value="Ms" <?= ($userRec['user_initial_title'] == 'Ms') ? 'selected' : '' ?>>Ms
							</option>
						</select>
					</div>
					<div class="frm_grp span_3">
						<p class="frm-head">First Name <i class="mandatory">*</i></p>
						<input type="text" name="user_first_name" id="user_first_name" style="text-transform:uppercase;"
							tabindex="5" value="<?= $userRec['user_first_name'] ?>" required />
					</div>
					<div class="frm_grp span_2">
						<p class="frm-head">Middle Name</p>
						<input type="text" name="user_middle_name" id="user_middle_name" style="text-transform:uppercase;"
							value="<?= $userRec['user_middle_name'] ?>" tabindex="6" />
					</div>
					<div class="frm_grp span_2">
						<p class="frm-head">Last Name <i class="mandatory">*</i></p>
						<input type="text" name="user_last_name" id="user_last_name" tabindex="7"
							style="text-transform:uppercase;" value="<?= $userRec['user_last_name'] ?>" required />
					</div>
					<div class="frm_grp span_4">
						<p class="frm-head">Email Address <i class="mandatory">*</i></p>
						<input type="text" name="user_email_id" id="user_email_id" value="<?= $userRec['user_email_id'] ?>"
							required />
						<input type="hidden" name="abstractDelegateId" value="<?= $userRec['delegate_id'] ?>" />
					</div>
					<div class="frm_grp span_4">
						<p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
						<input type="tel" name="user_usd_code" id="user_mobile_isd_code"
							style="width:12% !important;  text-align:right;" tabindex="2"
							value="<?= ($userRec['user_mobile_isd_code'] != '') ? ($userRec['user_mobile_isd_code']) : '+91' ?>"
							required />
						<input type="tel" name="user_mobile" id="user_mobile_no" style="width: 86% !important;"
							forType="mobileValidate" pattern="^\d{10}$" tabindex="3"
							value="<?= ($userRec['user_mobile_no'] != '') ? ($userRec['user_mobile_no']) : '' ?>"
							required />
					</div>
					<div class="frm_grp span_4">
						<p class="frm-head">Institute / Org</p>
						<input name="user_institute_name" value="<?= $userRec['user_institute_name'] ?>">
					</div>
					<!-- <div class="frm_grp span_4">
							<p class="frm-head">GST Number</p>
							<input>
						</div> -->
					<div class="frm_grp span_2">
						<p class="frm-head">Gender</p>
						<div class="cus_check_wrap">
							<label class="cus_check gender_check">
								<input type="radio" name="user_gender" value="Male" <?= ($userRec['user_gender'] == 'Male') ? 'checked' : '' ?>>
								<span class="checkmark">Male</span>
							</label>
							<label class="cus_check gender_check">
								<input type="radio" name="user_gender" value="Female" <?= ($userRec['user_gender'] == 'Female') ? 'checked' : '' ?>>
								<span class="checkmark">Female</span>
							</label>
						</div>
					</div>
					<div class="frm_grp span_2">
						<p class="frm-head">Food</p>
						<div class="cus_check_wrap">
							<label class="cus_check gender_check">
								<input type="radio" name="user_food_preference" value="VEG"
									<?= ($userRec['user_food_preference'] == 'VEG') ? 'checked' : '' ?>>
								<span class="checkmark">Veg</span>
							</label>
							<label class="cus_check gender_check">
								<input type="radio" name="user_food_preference" value="NON VEG"
									<?= ($userRec['user_food_preference'] == 'NON VEG') ? 'checked' : '' ?>>
								<span class="checkmark">Non Veg</span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="registration-pop_body_box_inner">
				<h4 class="registration-pop_body_box_heading">
					<span><?php address(); ?>Address Details</span>
				</h4>
				<div class="form_grid">
					<div class="frm_grp span_4">
						<p class="frm-head">Full Address</p>
						<textarea name="user_address"><?= $userRec['user_address'] ?></textarea>
					</div>
					<div class="frm_grp span_2">
						<p class="frm-head">Country</p>
						<select required implementvalidate="y" name="user_country" id="user_country" forType="country"
							stateId="user_state" onchange="stateRetriver(this);" tabindex="11" sequence="1">
							<option value="">-- Select Country --</option>
							<?php
							$sqlCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                            WHERE `status` = 'A' 
                                                        ORDER BY `country_name`";
							$resultCountry = $mycms->sql_select($sqlCountry);
							if ($resultCountry) {
								foreach ($resultCountry as $i => $rowFetchUserCountry) {
									?>
									<option value="<?= $rowFetchUserCountry['country_id'] ?>"
										<?= ($rowFetchUserCountry['country_id'] == $userRec['user_country_id']) ? 'selected' : '' ?>>
										<?= $rowFetchUserCountry['country_name'] ?>
									</option>
									<!-- -- FOR DEFAULT INDIA ---------- -->
									<!-- <option value="<?= $rowFetchUserCountry['country_id'] ?>" <?= ($rowFetchUserCountry['country_id'] == '1') ? 'selected' : '' ?>><?= $rowFetchUserCountry['country_name'] ?></option> -->
									<?php
								}
							}

							?>
						</select>
						<?
					if ($userRec['user_country_id'] != '') {

						?>
						<script>
							jQuery(document).ready(function ($) {
								var jsBASE_URL = "https://ruedakolkata.com/natcon_2025/";
								jBaseUrl = jsBASE_URL;
								generateSateList(<?= $userRec['user_country_id'] ?>, jBaseUrl);
								$('#user_state option[value="<?= $userRec['user_state_id'] ?>"]').prop('selected', true);

							});
						</script>
						<?php
					} ?>
					</div>
					<div class="frm_grp span_2" >
						<p class="frm-head">State</p>
						<div use='stateContainer' >
                                            <select name="user_state" id="user_state" forType="state" tabindex="12"
                                                sequence="1" disabled="disabled" required implementvalidate="y">
                                                <option value="">-- Select Country First --</option>

                                            </select>
                                        </div>



					</div>
					
					<div class="frm_grp span_2">
						<p class="frm-head">City</p>
						<input type="text" name="user_city" id="user_city" tabindex="13" style="text-transform:uppercase;"
							value="<?= ($userRec['user_city'] != '') ? ($userRec['user_city']) : '' ?>" required
							implementvalidate="y" />
					</div>
					<div class="frm_grp span_2">
						<p class="frm-head">Pincode</p>
						<input type="text" name="user_postal_code" id="user_postal_code" tabindex="14"
							value="<?= ($userRec['user_pincode'] != '') ? ($userRec['user_pincode']) : '' ?>"
							style="text-transform:uppercase;" required implementvalidate="y" />
					</div>
				</div>
			</div>
		</div>
		<div class="registration-pop_body_box">
			<div class="registration-pop_body_box_inner">
				<h4 class="registration-pop_body_box_heading">
					<span><?php calendar(); ?>Conference Details</span>
				</h4>
				<div class="form_grid">


					<div class="frm_grp span_4">
						<p class="frm-head">Registration Mode</p>
						<div class="cus_check_wrap">

							<?php if (!empty($cutoffArray)): ?>
								<?php foreach ($cutoffArray as $cutoffId => $cutoffName): ?>
									<?php $tabId = strtolower(str_replace(' ', '', $cutoffName)); ?>

									<label class="cus_check mode_check registration_cutoff" data-tab="<?= $tabId; ?>">
										<input type="radio" name="registration_cutoff" value="<?= $cutoffId; ?>"
											<?= ($userRec['registration_tariff_cutoff_id'] == $cutoffId) ? 'checked' : '' ?>>
										<span class="checkmark"><?= $cutoffName ?></span>
									</label>


								<?php endforeach; ?>
							<?php endif; ?>

						</div>
					</div>


					<div class="frm_grp span_4">
						<p class="frm-head">Select Category</p>

						<?php

						if (!empty($cutoffArray)):

							foreach ($cutoffArray as $cutoffId => $cutoffName):
								$tabId = strtolower(str_replace(' ', '', $cutoffName)); ?>
								<div class="regi_category class_cat" id="<?= $tabId ?>">
									<div class="cus_check_wrap flex-column">

										<?php

										foreach ($conferenceTariffArray as $categoryKey => $tariffRow):

											$clsfType = getRegClsfType($categoryKey);
											$categoryName = getRegClsfName($categoryKey);

											if ($clsfType == 'ACCOMPANY')
												continue;
											if ($clsfType == 'COMBO' && $categoryKey != 3)
												continue;


											$amount = $tariffRow[$cutoffId]['AMOUNT'];
											$currency = $tariffRow[$cutoffId]['CURRENCY'];

											$displayAmount = ($amount <= 0)
												? (($clsfType == 'FULL_ACCESS') ? "Complimentary" : "Zero Value")
												: $currency . " " . $amount;
											?>

											<label class="cus_check category_check">
												<input type="radio" name="registration_classification_id" value="<?= $categoryKey ?>"
													currency="<?= $currency ?>" tariffAmount="<?= $amount ?>"
													cutoffId="<?= $cutoffId ?>" registrationType="<?= $clsfType ?>"
													<?= ($userRec['registration_classification_id'] == $categoryKey) ? 'checked' : '' ?>>

												<span class="checkmark">
													<?= $categoryName ?>
													<i use="registrationTariff" tariffAmount="<?= $amount ?>"
														tariffCurrency="<?= $currency ?>"><?= $displayAmount ?></i>
												</span>
											</label>

										<?php endforeach; ?>

									</div>

								</div>

							<?php endforeach;
						endif; ?>

					</div>

				</div>

			</div>


			<div class="registration-pop_body_box_inner">
				<div class="form_grid">
					<div class="frm_grp span_4">
						<p class="frm-head">Workshop Cutoff</p>
						<div class="cus_check_wrap">

							<?php if (!empty($workshopCutoffArray)): ?>
								<?php foreach ($workshopCutoffArray as $cutoffId => $cutoffName): ?>
									<?php $tabId = strtolower(str_replace(' ', '', $cutoffName)); ?>

									<label class="cus_check mode_check workshop_cutoff">
										<input type="radio" name="workshop_cutoff[]" value="<?= $cutoffId; ?>"
											<?= ($userRec['workshop_cutoff'] == $cutoffId) ? 'checked' : '' ?>>
										<span class="checkmark"><?= $cutoffName ?></span>
									</label>


								<?php endforeach; ?>
							<?php endif; ?>

						</div>
					</div>

					<div class="frm_grp span_4">
						<h4 class="registration-pop_body_box_heading">
							<span><?php workshop(); ?>Workshops</span>
							<a class="add mi-1 CheckAllWorkshop"><?php ckeck(); ?>Select All</a>
						</h4>



						<div class="cus_check_wrap flex-column">

							<?php

							//workshop request table data
							$requestWorkshopSql = array();
							$requestWorkshopSql['QUERY'] = "  SELECT *
											   FROM " . _DB_REQUEST_WORKSHOP_ . "
											  WHERE status = 'A'AND delegate_id = '" . $delegate_id . "'";
							$requestWorkshopData = $mycms->sql_select($requestWorkshopSql);

							$selectedWorkshops = [];

							if (!empty($requestWorkshopData)) {
								foreach ($requestWorkshopData as $rowWorkshop) {
									$selectedWorkshops[] = $rowWorkshop['workshop_id'];
								}
							}

							//workshop request table data
						
							foreach ($workshopDetailsArray as $workshopId => $rowWorkshop): ?>

								<?php
								$wsName = getWorkshopName($workshopId);


								$regGroup = reset($rowWorkshop);


								$firstCutoff = reset($regGroup);
								$defaultAmount = (float) $firstCutoff['INR'];
								$defaultDisplay = ($defaultAmount > 0) ? "INR " . number_format($defaultAmount, 2) : "Included";
								?>


								<label class="cus_check category_check workshop_check">
									<input type="checkbox" name="workshop_id[]" value="<?= $workshopId ?>"
										workshop-id="<?= $workshopId ?>" <?= in_array($workshopId, $selectedWorkshops) ? 'checked="checked"' : ''; ?>>
									<span class="checkmark">
										<?= $wsName ?>
										<i use="workshopPriceDisplay"><?= $defaultDisplay ?></i>
									</span>
								</label>


								<?php foreach ($regGroup as $cutoffId => $cutoffData): ?>
									<span class="workshopTariffData" workshop-id="<?= $workshopId ?>" cutoff-id="<?= $cutoffId ?>"
										tariffAmount="<?= (float) $cutoffData['INR'] ?>" tariffCurrency="INR" style="display:none">
									</span>
								<?php endforeach; ?>

							<?php endforeach; ?>
						</div>


					</div>
				</div>
			</div>
			<?php
			$accSql = [];
			$accSql['QUERY'] = "SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE `status` = 'A' AND `refference_delegate_id` = '" . $delegate_id . "' ";
			$resultAcc = $mycms->sql_select($accSql);

			?>
			<div class="registration-pop_body_box_inner">
				<h4 class="registration-pop_body_box_heading">
					<span>Accompanying</span>
					<a class="add mi-1 add_accompany"><i class="fal fa-plus"></i>Add</a>
				</h4>
				<div class="accm_add_wrap">
					<?php if (!empty($resultAcc)) { ?>

						<?php foreach ($resultAcc as $key => $acc) { ?>

							<div class="accompany_box accm_add_box" data-id="<?= $acc['id'] ?>">
								<div class="form_grid">

									<div class="frm_grp span_4">
										<label class="frm-head">Full Name</label>
										<input type="text" name="accompany_name_add[<?= $acc['id'] ?>][user_full_name]"
											value="<?= htmlspecialchars($acc['user_full_name']) ?>">
									</div>
									<div class="frm_grp span_2">
										<div class="cus_check_wrap">
											<label class="cus_check gender_check">
												<input type="radio" name="accompany_food_choice[<?= $acc['id'] ?>]" value="VEG"
													<?= ($acc['user_food_preference'] == 'VEG') ? 'checked' : '' ?>>
												<span class="checkmark">Veg</span>
											</label>
											<label class="cus_check gender_check">
												<input type="radio" name="accompany_food_choice[<?= $acc['id'] ?>]" value="NON VEG"
													<?= ($acc['user_food_preference'] == 'NON VEG') ? 'checked' : '' ?>>
												<span class="checkmark">Non Veg</span>
											</label>
										</div>
									</div>


									<div class="frm_grp span_4">
										<a href="javascript:void(0)"
											class="accm_delet icon_hover badge_danger action-transparent rmv_accompany"><?php delete(); ?></a>

									</div>

								</div>
							</div>

						<?php } ?>

					<?php } else { ?>

						<h6 class="accm_add_empty">No accompanying persons</h6>

					<?php } ?>

					<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="0" />

					<div class="accm_add_box">
						<div class="form_grid">
							<div class="frm_grp span_4">
								<p class="frm-head">Accompanying Mode</p>
								<div class="cus_check_wrap">

									<input type="hidden" name="accompanyClasfId" value="<?= $accompanyCatagory ?>" />
									<input type="hidden" name="userREGtype" value="<?= $_REQUEST['userREGtype'] ?>" />
									<input type="hidden" name="abstractDelegateId"
										value="<?= $_REQUEST['abstractDelegateId'] ?>" />
									<?php if ($_REQUEST['COUNTER'] == 'Y') { ?>
										<input type="hidden" name="counter" id="counter" value="Y" />
									<?php } ?>
									<?
									$registrationDetails = getAllRegistrationTariffs();
									//$registrationAmount 	= $registrationDetails[$accompanyCatagory][$delagateCutoff]['AMOUNT'];
									$dinnerTariffArray = getAllDinnerTarrifDetails();
									$dinnerAmmount = $dinnerTariffArray[2][$delagateCutoff]['AMOUNT'];
									$registrationAmount = getCutoffTariffAmnt($USER_DETAILS['CUTOFF']);
									?>


									<input type="hidden" name="dinnerTariffAmount" id="dinnerTariffAmount"
										value="<?= $dinnerAmmount ?>" />
									<?php if (!empty($cutoffArray)): ?>
										<?php foreach ($cutoffArray as $cutoffId => $cutoffName): ?>

											<?php
											$amount = $accompanyTariffMap[$cutoffId]['amount'] ?? 0;
											$currency = $accompanyTariffMap[$cutoffId]['currency'] ?? 'INR';
											$tabId = strtolower(str_replace(' ', '', $cutoffName));
											?>

											<label class="cus_check mode_check registration_acc_cutoff" data-tab="<?= $tabId; ?>">
												<input type="radio" name="registration_acc_cutoff" value="<?= $cutoffId; ?>"
													data-amount="<?= $amount; ?>" data-currency="<?= $currency; ?>">

												<span class="checkmark"><?= $cutoffName ?></span>
											</label>

										<?php endforeach; ?>
									<?php endif; ?>


								</div>
							</div>
						</div>
					</div>
					<div class="accm_add_box accompany_box">
						<div class="form_grid">
							<div class="frm_grp span_4">
								<label class="frm-head">Full Name</label>
								<input type="hidden" name="accompany_selected_add[1]" value="1" autocomplete="off">

								<input type="text" name="accompany_name_add[1]" id="accompany_name_add_1"
									style="text-transform:uppercase;" autocomplete="off">

							</div>

							<div class="frm_grp span_2">
								<div class="cus_check_wrap">
									<label class="cus_check gender_check">
										<input type="radio" name="accompany_food_choice[1]" value="VEG">
										<span class="checkmark">Veg</span>
									</label>
									<label class="cus_check gender_check">
										<input type="radio" name="accompany_food_choice[1]" value="NON VEG">
										<span class="checkmark">Non Veg</span>
									</label>
								</div>
							</div>
							<div class="frm_grp span_2">
								<a href="#"
									class="accm_delet icon_hover badge_danger action-transparent rmv_accompany"><?php delete(); ?></a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="registration-pop_body_box">
			<div class="registration-pop_body_box_inner">
				<h4 class="registration-pop_body_box_heading">
					<span><?php bill(); ?>Bill Summary</span>
				</h4>
				<div class="billSummaryItems"></div>

				<hr class="my-3" style="border-top:1px dashed currentColor;">

				<p class="frm-head text_dark d-flex justify-content-between">
					Subtotal
					<span class="text-white billSubtotal" id="billSubtotal">₹ 0</span>
				</p>

				<p class="frm-head text_dark d-flex justify-content-between">
					GST (18%)
					<span class="text-white billGST" id="billGST">₹ 0</span>
				</p>

				<h5 class="regi_total">
					Total <span class="billTotal">₹ 0</span>
				</h5>
			</div>
			<div class="registration-pop_body_box_inner">
				<h4 class="registration-pop_body_box_heading">
					<span><?php credit(); ?>Payment Details</span>
				</h4>
				<div class="form_grid">
					<div class="frm_grp span_4">
						<p class="frm-head">Payment Mode</p>
						<div class="cus_check_wrap">
							<label class="cus_check pay_check" data-tab="cash">
								<input type="radio" name="paymentmood" checked>
								<span class="checkmark">Cash</span>
							</label>

							<label class="cus_check pay_check" data-tab="upi">
								<input type="radio" name="paymentmood">
								<span class="checkmark">UPI</span>
							</label>

							<label class="cus_check pay_check" data-tab="card">
								<input type="radio" name="paymentmood">
								<span class="checkmark">Card</span>
							</label>

						</div>
					</div>
					<div class="payment_details span_4" id="cash" style="display:block;">
						<div class="form_grid">
							<div class="frm_grp span_2">
								<p class="frm-head">Date</p>
								<input type="date">
							</div>
							<div class="frm_grp span_2">
								<p class="frm-head">Amount Paid</p>
								<input type="number" class="amountPaidInput">
							</div>
							<div class="frm_grp span_4">
								<p class="frm-head">Transaction ID / Ref No</p>
								<input>
							</div>
						</div>
					</div>

					<div class="payment_details span_4" id="upi" style="display:none;">
						<div class="form_grid">
							<div class="frm_grp span_2">
								<p class="frm-head">Date</p>
								<input type="date" class="amountPaidInput">
							</div>
							<div class="frm_grp span_2">
								<p class="frm-head">Amount Paid</p>
								<input type="number" class="amountPaidInput">
							</div>
							<div class="frm_grp span_4">
								<p class="frm-head">Transaction ID / Ref No</p>
								<input>
							</div>
						</div>
					</div>
					<div class="payment_details span_4" id="card" style="display:none;">
						<div class="form_grid">
							<div class="frm_grp span_2">
								<p class="frm-head">Date</p>
								<input type="date">
							</div>
							<div class="frm_grp span_2">
								<p class="frm-head">Amount Paid</p>
								<input type="number" class="amountPaidInput">
							</div>
							<div class="frm_grp span_4">
								<p class="frm-head">Transaction ID / Ref No</p>
								<input>
							</div>
						</div>
					</div>
				</div>
				<h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">
					Balance Due<span class="text-white mt-0 billBalance" id="billBalance">₹ 0.00</span></h6>
				<a href="#" class="due_balance badge_info p-2 mt-1 d-flex justify-content-center align-items-center"
					id="autoFillAmount">Auto-Fill
					Full Amount</a>

			</div>
			<div class="registration-pop_body_box_inner bg-transparent p-0 border-0">
				<div class="registration_btn_wrap">
					<button type="submit" class="mi-1"><?php save(); ?>Update Registration</button>
					<button>Cancel</button>
				</div>
			</div>
		</div>

	</div>
	<?php

}
exit;

//delete usernew 
function DeleteUser($mycms, $cfg)
{
	

	$loggedUserID = $mycms->getLoggedUserId();

	$loggedUserID = $mycms->getLoggedUserId();
	$delegateId = $_REQUEST['id'];
	$userType = $_REQUEST['userType'];

	// REMOVING USER
	$sqlRemove = array();
	$sqlRemove['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
										 SET `status` = ? 
									   WHERE `id`     = ?";
	$sqlRemove['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemove['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemove);

	$sqlRemoveInvoice = array();
	$sqlRemoveInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
											  SET `status_before_trash`  = `status` 
											WHERE `delegate_id`= ?";
	$sqlRemoveInvoice['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveInvoice);

	$sqlRemoveInvoice = array();
	$sqlRemoveInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
											  SET `status` = ? 
											WHERE `delegate_id` = ?";
	$sqlRemoveInvoice['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemoveInvoice['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveInvoice);

	$sqlRemoveSlip = array();
	$sqlRemoveSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
										   SET `status_before_trash`  = `status` 
										 WHERE `delegate_id` = ?";
	$sqlRemoveSlip['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveSlip);

	$sqlRemoveSlip = array();
	$sqlRemoveSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
										   SET `status` = ? 
										 WHERE `delegate_id` = ?";
	$sqlRemoveSlip['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemoveSlip['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveSlip);

	if ($userType == 'EXHIBITOR') {
		// pageRedirection("exhibitor_listing.php", "1");
		$mycms->redirect(_BASE_URL_ . 'webmaster/section_exhibitor/exhibitor_listing.php');
	} else if ($userType == 'GUEST') {
		$mycms->redirect(_BASE_URL_ . 'webmaster/section_spot/spot_create_delegate.php?show=guestUsers');
	} else if ($userType == 'VOLUNTEER') {
		$mycms->redirect(_BASE_URL_ . 'webmaster/section_spot/spot_create_delegate.php?show=volunteerUsers');
	} else {
		pageRedirection("registration_new.php", "1");
	}

	
exit;
}

function getRegistrationDetails_old($mycms, $cfg)
{
	$delegate_id = $_REQUEST['id'];

	// Flags
	$hasConference = false;
	$hasDinner = false;
	$hasWorkshop = false;
	$hasAccompany = false;

	$sqlFetchInvoice = getRegistrationInvoiceCancelInvoiceDetails("", $delegate_id, "");
	$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);

	if ($resultFetchInvoice) {
		foreach ($resultFetchInvoice as $rowFetchInvoice) {

			if ($rowFetchInvoice['status'] !== 'A') {
				continue;
			}

			switch ($rowFetchInvoice['service_type']) {

				case 'DELEGATE_CONFERENCE_REGISTRATION':
					$hasConference = true;
					break;

				case 'DELEGATE_DINNER_REQUEST':
					$hasDinner = true;
					break;

				case 'DELEGATE_WORKSHOP_REGISTRATION':
					$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'], true);
					if (isset($workShopDetails['showInInvoices']) && $workShopDetails['showInInvoices'] === 'Y') {
						$hasWorkshop = true;
					}
					break;

				case 'ACCOMPANY_CONFERENCE_REGISTRATION':
					$hasAccompany = true;
					break;
			}
		}
	}
	?>

	<ul class="service_ul">

		<?php if ($hasConference): ?>
			<li class="badge_padding badge_primary">
				<?php conregi(); ?><span>Conference Registration</span>
			</li>
		<?php endif; ?>

		<?php if ($hasDinner): ?>
			<li class="badge_padding badge_danger">
				<?php dinner(); ?><span>Gala Dinner</span>
			</li>
		<?php endif; ?>

		<?php if ($hasWorkshop): ?>
			<li class="badge_padding badge_info">
				<?php workshop(); ?><span>Imaging Workshop</span>
			</li>
		<?php endif; ?>

		<?php if ($hasAccompany): ?>
			<li class="badge_padding badge_secondary">
				<?php duser(); ?><span>Accompanying Person</span>
			</li>
		<?php endif; ?>

	</ul>

	<?php
	exit();
}



function getRegistrationDetailsForSpot($mycms, $cfg)
{
	$loggedUserId = $mycms->getLoggedUserId();
	$delegate_id = $_REQUEST['id'];
	$rowFetchUser = getUserDetails($delegate_id);

	$serviceSummary = array();

	$hasUnpaidBill = false;
	$isResReg = false;

	?>
	<table width="100%" style="border:0px;">
		<tr style="border:0px;">
			<td style="border:0px;" align="center" valign="top" use='registrationDetailsList'>
				<?php
				$sqlFetchInvoice = getRegistrationInvoiceCancelInvoiceDetails("", $delegate_id, "");
				$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);
				//echo '<!--'; print_r($resultFetchInvoice ); echo '-->'; 
				if ($resultFetchInvoice) {
					$accommodationRecords = array();

					foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
						$showTheRecord = true;

						$invoiceCounter++;
						$slip = getInvoice($rowFetchInvoice['slip_id']);
						$returnArray = discountAmount($rowFetchInvoice['id']);
						$percentage = $returnArray['PERCENTAGE'];
						$totalAmount = $returnArray['TOTAL_AMOUNT'];
						$discountAmount = $returnArray['DISCOUNT'];


						$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
						$thisUserClasfId = getUserClassificationId($rowFetchInvoice['delegate_id']);
						$thisUserClasfName = getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']));

						if ($rowFetchInvoice['status'] = 'A') {
							$type = "";
							if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "CONFERENCE");
								$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-gift" aria-hidden="true"></i>&nbsp;Conference Registration';
							}
							if ($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
								$isResReg = true;
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "RESIDENTIAL");
								$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-gift" aria-hidden="true"></i>&nbsp;' . $type;

								$sqlAccomm = array();
								$sqlAccomm['QUERY'] = " SELECT accomm.*, hotel.hotel_name
															 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
													   INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
															   ON accomm.hotel_id = hotel.id
															WHERE accomm.`user_id` = ?
															  AND accomm.`refference_invoice_id` = ?";
								$sqlAccomm['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegate_id, 'TYP' => 's');
								$sqlAccomm['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $rowFetchInvoice['id'], 'TYP' => 's');
								$resAccomm = $mycms->sql_select($sqlAccomm);

								foreach ($resAccomm as $kk => $row) {
									$serviceSummary[$rowFetchInvoice['id'] . 'accm'] = '';

									$accommodationRecords[$rowFetchInvoice['id']]['KEY'] = $rowFetchInvoice['id'] . 'accm';
									$accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] = 'RES-PACK';
									$accommodationRecords[$rowFetchInvoice['id']]['accomId'] = $row['id'];
									$accommodationRecords[$rowFetchInvoice['id']]['packageId'] = $cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId];
									$accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] = $row['hotel_name'];
									$accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] = $row['checkin_date'];
									$accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] = $row['checkout_date'];

									$accommodationRecords[$rowFetchInvoice['id']]['WILL-SHARE'] = false;
									$accommodationRecords[$rowFetchInvoice['id']]['SHARE'] = array();

									//$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';
			
									if (in_array($thisUserClasfId, $cfg['RESIDENTIAL_SHARING_CLASF_ID'])) {
										$accommodationRecords[$rowFetchInvoice['id']]['WILL-SHARE'] = true;
										$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['KEY'] = $rowFetchInvoice['id'] . 'accmshr';
										$serviceSummary[$rowFetchInvoice['id'] . 'accmshr'] = '';

										if (trim($row['preffered_accommpany_name']) != '') {
											$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['accomId'] = $row['id'];
											$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefName'] = $row['preffered_accommpany_name'];
											$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefMobile'] = $row['preffered_accommpany_mobile'];
											$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefEmail'] = $row['preffered_accommpany_email'];
											/*$serviceSummary[$rowFetchInvoice['id'].'accmshr'] = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" onClick="openSharePrefEditPopup(this)" accomId="'.$row['id'].'" prefName="'.$row['preffered_accommpany_name'].'"  prefMobile="'.$row['preffered_accommpany_mobile'].'"  prefEmail="'.$row['preffered_accommpany_email'].'"></i>&nbsp;'.$row['preffered_accommpany_name'].'<br>
																								 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-phone" aria-hidden="true"></i>&nbsp;'.$row['preffered_accommpany_mobile'].'<br>
																								 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;'.$row['preffered_accommpany_email'].'';*/
										} else {
											$accommodationRecords[$rowFetchInvoice['id']]['SHARE']['accomId'] = $row['id'];
											//$serviceSummary[$rowFetchInvoice['id'].'accmshr'] = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" onClick="openSharePrefEditPopup(this)" accomId="'.$row['id'].'"></i>&nbsp;-';
										}
									}
								}
							}
							if ($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
								$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'], true);
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "WORKSHOP");
								if ($workShopDetails['showInInvoices'] != 'Y') {
									$showTheRecord = false;
								}
								$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-stethoscope" aria-hidden="true"></i>&nbsp;' . $workShopDetails['classification_title'];
							}
							if ($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "ACCOMPANY");
								$accompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
								if ($accompanyDetails['registration_request'] == 'GUEST') {
									$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-smile-o" aria-hidden="true"></i>&nbsp;Accompaning Guest - ' . $accompanyDetails['user_full_name'];
								} else {
									$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-users" aria-hidden="true"></i>&nbsp;Accompany - ' . $accompanyDetails['user_full_name'];
								}
							}
							if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "ACCOMMODATION");
								//$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-building" aria-hidden="true"></i>&nbsp;Accomodation';
								$showTheRecord = false;
								$sqlAccomm = array();
								$sqlAccomm['QUERY'] = " SELECT accomm.*, hotel.hotel_name
															 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
													   INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
															   ON accomm.hotel_id = hotel.id
															WHERE accomm.`user_id` = ?
															  AND accomm.`refference_invoice_id` = ?";
								$sqlAccomm['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegate_id, 'TYP' => 's');
								$sqlAccomm['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $rowFetchInvoice['id'], 'TYP' => 's');
								$resAccomm = $mycms->sql_select($sqlAccomm);

								foreach ($resAccomm as $kk => $row) {
									$accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] = 'ACCOMMODATION';
									$accommodationRecords[$rowFetchInvoice['id']]['accomId'] = $row['id'];
									$accommodationRecords[$rowFetchInvoice['id']]['packageId'] = $row['package_id'];
									$accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] = $row['hotel_name'];
									$accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] = $row['checkin_date'];
									$accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] = $row['checkout_date'];
									$accommodationRecords[$rowFetchInvoice['id']]['SHARE'] = array();
									//$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';
								}
							}
							if ($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST") {
								$tourDetails = getTourDetails($invoiceDetails['refference_id']);
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "TOUR");
								$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-bus" aria-hidden="true"></i>&nbsp;Tour';
							}
							if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
								$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "DINNER");
								$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-cutlery" aria-hidden="true"></i>&nbsp;Dinner';
							}
						}





						//echo '<!--'; print_r($serviceSummary); echo '-->';
			

					}

					if (!empty($accommodationRecords)) {
						$checkinDate = 99991231;
						$checkoutDate = 00000000;
						$the_has_additional = false;
						$the_has_share = false;
						$the_checkoutDate = '';
						$the_checkinDate = '';

						foreach ($accommodationRecords as $invoiceId => $data) {
							$cinDt = intval($mycms->cDate('Ymd', $data['checkin_date']));
							$coutDt = intval($mycms->cDate('Ymd', $data['checkout_date']));
							if ($coutDt > $checkoutDate) {
								$the_checkoutDate = $data['checkout_date'];
								$checkoutDate = $coutDt;
							}
							if ($cinDt < $checkinDate) {
								$the_checkinDate = $data['checkin_date'];
								$checkinDate = $cinDt;
							}

							if ($data['BOOK-TYP'] == 'RES-PACK') {
								$the_key = $data['KEY'];
								$the_accomId = $data['accomId'];
								$the_packageId = $data['packageId'];
								$the_hotel_name = $data['hotel_name'];

								if ($data['WILL-SHARE']) {
									$the_has_share = true;
									$the_share_key = $data['SHARE']['KEY'];
									$the_share_accomId = $data['SHARE']['accomId'];
									$the_share_prefName = $data['SHARE']['prefName'];
									$the_share_prefMobile = $data['SHARE']['prefMobile'];
									$the_share_prefEmail = $data['SHARE']['prefEmail'];
								}
							} else {
								$the_has_additional = true;
							}
						}

						$serviceSummary[$the_key] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" ' . ((!$the_has_additional) ? 'onClick="openAccmDateEditPopup(this)"' : '') . ' accomId="' . $the_accomId . '" packageId="' . $the_packageId . '"></i>&nbsp;Accommodation @ ' . $the_hotel_name . ' <span style="font-size:12px;">[' . $the_checkinDate . ' to ' . $the_checkoutDate . ']</span>';
						if ($the_has_share) {
							if ($the_share_prefName != '') {
								$serviceSummary[$the_share_key] = '  &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" ' . ((!$the_has_additional) ? 'onClick="openSharePrefEditPopup(this)"' : '') . ' accomId="' . $the_share_accomId . '" prefName="' . $the_share_prefName . '"  prefMobile="' . $the_share_prefMobile . '"  prefEmail="' . $the_share_prefEmail . '"></i>&nbsp;' . $the_share_prefName . '<br>
																	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-phone" aria-hidden="true"></i>&nbsp;' . $the_share_prefMobile . '<br>
																	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;' . $the_share_prefEmail . '';
							} else {
								$serviceSummary[$the_share_key] = '	&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" ' . ((!$the_has_additional) ? 'onClick="openSharePrefEditPopup(this)"' : '') . ' accomId="' . $the_share_accomId . '"></i>&nbsp;-';
							}
						}
					}
				}

				$sqlPickupDropOff = array();
				$sqlPickupDropOff['QUERY'] = " SELECT *
													FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " 
												   WHERE `user_id` = ?";
				$sqlPickupDropOff['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegate_id, 'TYP' => 's');
				$resPickupDropOff = $mycms->sql_select($sqlPickupDropOff);

				foreach ($resPickupDropOff as $kk => $row) {
					if ($row['pikup_time'] != '') {
						$serviceSummary[$rowFetchInvoice['id'] . '.' . $kk . 'p'] = 'Pickup on ' . $row['pikup_time'];
					}
					if ($row['dropoff_time'] != '') {
						$serviceSummary[$rowFetchInvoice['id'] . '.' . $kk . 'd'] = 'Dropoff on ' . $row['dropoff_time'];
					}
				}

				?>
				<table width="100%" style="border: 1px solid black; margin-top:3px; font-size:13px;"
					use="registrationSummaryDetails">
					<tr>
						<td><?= implode(',<br>', $serviceSummary) ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?
	exit();
}

function getUserRegTableDetails($mycms, $cfg)
{
	$loggedUserId = $mycms->getLoggedUserId();
	$delegate_id = $_REQUEST['id'];
	$rowFetchUser = getUserDetails($delegate_id);

	/*echo '<pre>';
		print_r($rowFetchUser);
		echo '</pre>';*/

	$resultAbstract = false;
	$resUserInvoice = false;
	$resFetchHotelBooking = false;

	$otherIconColor = '';
	if ($rowFetchUser['user_type'] == 'ACCOMPANY') {
		$otherIconColor = '#0066FF';
		$delegateUser = getUserDetails($rowFetchUser['refference_delegate_id']);

		$sqlUserInvoice = array();
		$sqlUserInvoice['QUERY'] = "SELECT invDetails.*, invDetails.delegate_id AS delegateId, slipDetails.slip_number AS slipNO 
										  FROM  " . _DB_INVOICE_ . " invDetails
							   LEFT OUTER JOIN " . _DB_SLIP_ . " slipDetails
										    ON slipDetails.id = invDetails.slip_id
										 WHERE invDetails.service_type = 'ACCOMPANY_CONFERENCE_REGISTRATION' 
										   AND invDetails.refference_id = '" . $delegate_id . "'
										   AND invDetails.status IN ('A')";
		$resUserInvoice = $mycms->sql_select($sqlUserInvoice);
	} elseif ($rowFetchUser['user_type'] == 'DELEGATE') {
		if ($rowFetchUser['registration_request'] == 'GENERAL') {
			$otherIconColor = '#009900';

			$sqlUserInvoice = array();
			$sqlUserInvoice['QUERY'] = "SELECT invDetails.*, invDetails.delegate_id AS delegateId, slipDetails.slip_number AS slipNO 
											  FROM  " . _DB_INVOICE_ . " invDetails
								   LEFT OUTER JOIN " . _DB_SLIP_ . " slipDetails
												ON slipDetails.id = invDetails.slip_id
											 WHERE invDetails.service_type IN ('DELEGATE_CONFERENCE_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION') 
											   AND invDetails.refference_id = '" . $delegate_id . "'
											   AND invDetails.status IN ('A')";
			$resUserInvoice = $mycms->sql_select($sqlUserInvoice);
		} elseif ($rowFetchUser['registration_request'] == 'ABSTRACT') {
			$otherIconColor = '#FF9900';
		} elseif ($rowFetchUser['registration_request'] == 'ONLYWORKSHOP') {
			$otherIconColor = '#FF00FF';
			$sqlUserInvoice = array();
			$sqlUserInvoice['QUERY'] = "SELECT invDetails.*, invDetails.delegate_id AS delegateId, slipDetails.slip_number AS slipNO 
											  FROM  " . _DB_INVOICE_ . " invDetails
								   LEFT OUTER JOIN " . _DB_SLIP_ . " slipDetails
												ON slipDetails.id = invDetails.slip_id
											 WHERE invDetails.service_type IN ('DELEGATE_WORKSHOP_REGISTRATION') 
											   AND invDetails.delegate_id = '" . $delegate_id . "'
											   AND invDetails.status IN ('A')";
			$resUserInvoice = $mycms->sql_select($sqlUserInvoice);
		} else {
			$otherIconColor = '#FF6600';
		}
	} else {
		$otherIconColor = '#9900CC';
	}

	$sqlTotPlusFetch = array();
	$sqlTotPlusFetch['QUERY'] = "  SELECT *
											   FROM " . _DB_REQUEST_WORKSHOP_ . "
											  WHERE status = 'A'
												AND workshop_id = '9'
												AND delegate_id = '" . $delegate_id . "'";
	$resTotPlusFetch = $mycms->sql_select($sqlTotPlusFetch);

	$sqlCervicalBreastCancerFetch = array();
	$sqlCervicalBreastCancerFetch['QUERY'] = "  SELECT *
														   FROM " . _DB_REQUEST_WORKSHOP_ . "
														  WHERE status = 'A'
															AND workshop_id = '8'
															AND delegate_id = '" . $delegate_id . "'";
	$resCervicalBreastCancerFetch = $mycms->sql_select($sqlCervicalBreastCancerFetch);

	$sqlPerinealTearsFetch = array();
	$sqlPerinealTearsFetch['QUERY'] = "  SELECT *
														   FROM " . _DB_REQUEST_WORKSHOP_ . "
														  WHERE status = 'A'
															AND workshop_id = '7'
															AND delegate_id = '" . $delegate_id . "'";
	$resPerinealTearsFetch = $mycms->sql_select($sqlPerinealTearsFetch);

	$sqlLapSutureFetch = array();
	$sqlLapSutureFetch['QUERY'] = "  SELECT *
														   FROM " . _DB_REQUEST_WORKSHOP_ . "
														  WHERE status = 'A'
															AND workshop_id = '6'
															AND delegate_id = '" . $delegate_id . "'";
	$resLapSutureFetch = $mycms->sql_select($sqlLapSutureFetch);

	$sqlFetchHotelBooking = array();
	$sqlFetchHotelBooking['QUERY'] = "  SELECT accomod.*, packageAcc.package_name, 
														  hotel.hotel_name as hotel_name		  
													 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomod
										  LEFT OUTER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " packageAcc
													   ON packageAcc.id =  accomod.package_id
										  LEFT OUTER JOIN " . _DB_MASTER_HOTEL_ . " hotel
													   ON hotel.id= accomod.hotel_id	
												    WHERE accomod.user_id	= " . $delegate_id . "
												      AND accomod.status = 'A'
												 ORDER BY accomod.checkin_date";
	$resFetchHotelBooking = $mycms->sql_select($sqlFetchHotelBooking);

	$sqlDinnerDetails = array();
	$sqlDinnerDetails['QUERY'] = "   SELECT dinnerReq.*,  
												 dinner.dinner_classification_name, dinner.date AS dinnerDate
											FROM " . _DB_REQUEST_DINNER_ . " dinnerReq
									  INNER JOIN " . _DB_DINNER_CLASSIFICATION_ . " dinner
											  ON dinner.id = dinnerReq.package_id
										   WHERE dinnerReq.status = 'A'
											 AND dinnerReq.`refference_id` = '" . $delegate_id . "'";
	$resDinnerDetails = $mycms->sql_select($sqlDinnerDetails);

	$sqlAbstract = array();
	$sqlAbstract['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
									WHERE `applicant_id` = '" . $delegate_id . "'
									  AND `status` = 'A'";
	$resultAbstract = $mycms->sql_select($sqlAbstract);

	?>
	<table width="100%" style="border:0px; font-size:9px !important;">
		<?
		if ($rowFetchUser['user_type'] == 'ACCOMPANY') {
			?>
			<tr style="border:0px;">
				<td style="border:0px;" align="left" valign="top">
					<i class="fa fa-user-md" aria-hidden="true" style="color:#009900;"
						title="Delegate"></i>&nbsp;<?= strtoupper($delegateUser['user_full_name']) ?>
					<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#FFCCFF;"
						indx="basic" onclick="toggleDetails(this)">Basic Details</span>
					<br />
					<span style="font-size:9px; line-height:10px;">
						** Conference Kit, Lunch on September 5, 6 & 7, 2019, Inaugural Dinner on September 5, 2019<br />
						*** Gala Dinner
					</span>
				</td>
			</tr>
			<?
		} elseif ($rowFetchUser['user_type'] == 'DELEGATE') {
			if ($rowFetchUser['registration_request'] == 'GENERAL') {
				?>
				<tr style="border:0px;">
					<td style="border:0px;" align="left" valign="top">
						<i class="fa fa-certificate" aria-hidden="true" style="color:<?= $otherIconColor ?>;"
							title="Classification"></i>&nbsp;<?= getRegClsfName($rowFetchUser['registration_classification_id']) ?>
						<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; background:#FFCCFF;"
							indx="basic" onclick="toggleDetails(this)">Basic Details</span>
						<br />
						<span style="font-size:9px; line-height:10px;">
							<?
							switch ($rowFetchUser['registration_classification_id']) {
								case '1':
								case '4':
								case '5':
								case '6':
									echo '** Master Class on September 5, 2019, Lunch on September 5, 6 & 7, 2019, Inaugural Dinner on September 5, 2019, Conference Kit';
									echo '<br/>*** Post-Congress Workshop, Gala Dinner, Abstract';
									break;
								case '3':
									echo '** Master Class on September 5, 2019, Lunch on September 5, 6 & 7, 2019, Inaugural Dinner on September 5, 2019, Conference Kit, Gala Dinner';
									echo '<br/>*** Post-Congress Workshop, Abstract';
									break;
								case '7':
								case '8':
								case '9':
								case '10':
									echo '** Master Class on September 5, 2019, Lunch on September 5, 6 & 7, 2019, Inaugural Dinner on September 5, 2019, Gala Dinner, Conference Kit, Accommodation @ ITC';
									if ($rowFetchUser['conf_reg_date'] < '2019-01-16') {
										echo ', Pickup-Drop';
									}
									echo '<br/>*** Post-Congress Workshop, Abstract';
									break;
								case '11':
								case '12':
								case '13':
								case '14':
									echo '** Master Class on September 5, 2019, Lunch on September 5, 6 & 7, 2019, Inaugural Dinner on September 5, 2019, Gala Dinner, Conference Kit, Accommodation @ Visitel';
									if ($rowFetchUser['conf_reg_date'] < '2019-01-16') {
										echo ', Pickup-Drop';
									}
									echo '<br/>*** Post-Congress Workshop, Abstract';
									break;
								case '15':
								case '16':
								case '17':
								case '18':
									echo '** Master Class on September 5, 2019, Lunch on September 5, 6 & 7, 2019, Inaugural Dinner on September 5, 2019, Gala Dinner, Conference Kit, Accommodation @ JW Mariot';
									if ($rowFetchUser['conf_reg_date'] < '2019-01-16') {
										echo ', Pickup-Drop';
									}
									echo '<br/>*** Post-Congress Workshop, Abstract';
									break;
							}
							?>
						</span>
					</td>
				</tr>
				<?
			}
		}
		?>
		<tr style="border:0px;">
			<td style="border:0px;" align="left" valign="top">
				<!--<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left;" indx="basic" onclick="toggleDetails(this)">Basic Details</span>-->
				<?
				if ($resTotPlusFetch) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#E0E0C2;"
						indx="TotPlus" onclick="toggleDetails(this)">RCOG Tot Plus</span>
					<?
				}
				if ($resCervicalBreastCancerFetch) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#FFFFB0;"
						indx="CervicalBreastCancer" onclick="toggleDetails(this)">Cervical & Breast Cancer</span>
					<?
				}
				if ($resPerinealTearsFetch) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#DEDEEF;"
						indx="PerinealTears" onclick="toggleDetails(this)">Diagnosis of Perineal Tears</span>
					<?
				}
				if ($resLapSutureFetch) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#FFDFDF;"
						indx="LapSuture" onclick="toggleDetails(this)">Laparoscopic Suturing</span>
					<?
				}
				if ($resFetchHotelBooking) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#CCCCFF;"
						indx="Accommodation" onclick="toggleDetails(this)">Accommodation</span>
					<?
				}
				if ($resDinnerDetails) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#B6DADA;"
						indx="GalaDinner" onclick="toggleDetails(this)">Gala Dinner</span>
					<?
				}
				if ($resultAbstract) {
					?>
					<span
						style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px; float:left; background:#FFD9B3;"
						indx="abstract" onclick="toggleDetails(this)">Abstracts</span>
					<?
				}
				?>
			</td>
		</tr>
	</table>

	<table width="100%"
		style="border:0px; border-top: thin dashed #333333; background:#FFCCFF; font-size:9px !important; display:none;"
		use="userDetails" indx="basic">
		<?
		if ($resUserInvoice) {
			foreach ($resUserInvoice as $key => $invoiceDetails) {
				$type = '';
				if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
				} elseif ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
				} elseif ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
				} elseif ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $rowDinner['refference_invoice_id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
		}
		?>
	</table>
	<?
	if ($resultAbstract) {
		?>
		<table width="100%"
			style="border:0px;  border-top: thin dashed #333333; background:#FFD9B3; font-size:9px !important; display:none;"
			use="userDetails" indx="abstract">
			<?
			foreach ($resultAbstract as $key => $rowAbstract) {
				$sqlAbstractDetails = abstractDetailsQuerySet($rowAbstract['id']);
				$resultAbstractDetails = $mycms->sql_select($sqlAbstractDetails);
				$rowAbstractDetails = $resultAbstractDetails[0];
				?>
				<tr style="border:0px;">
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $rowAbstractDetails['abstract_submition_code'] ?></b>&nbsp;&nbsp;&nbsp;
						<i class="fa fa-flag" aria-hidden="true" title="Type"
							style="color:<?= $abstractColor[$rowAbstractDetails['abstract_parent_type']] ?>"></i>&nbsp;<?= $rowAbstractDetails['abstract_parent_type'] ?>&nbsp;&nbsp;&nbsp;
						<i class="fa fa-flag-checkered" aria-hidden="true" title="Presentation"
							style="color:<?= $presentationColor[$rowAbstractDetails['abstract_child_type']] ?>"></i>&nbsp;<?= $rowAbstractDetails['abstract_child_type'] ?><br />
						<i class="fa fa-book" aria-hidden="true"
							title="Topic"></i>&nbsp;<b><?= $rowAbstractDetails['abstract_topic'] ?></b><br />
						<?= $rowAbstractDetails['abstract_title'] ?>
						<?
						if ($rowAbstractDetails['award_names'] != '') {
							?>
							<br /><i class="fa fa-trophy" aria-hidden="true"
								title="Nomination"></i>&nbsp;<?= $rowAbstractDetails['award_names'] ?>
							<?
						}
						?>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	if ($resDinnerDetails) {
		?>
		<table width="100%"
			style="border:0px; border-top: thin dashed #333333; background:#B6DADA; font-size:9px !important; display:none;"
			use="userDetails" indx="GalaDinner">
			<?
			foreach ($resDinnerDetails as $key => $rowDinner) {
				$invoiceDetails = getInvoiceDetails($rowDinner['refference_invoice_id']);
				if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
				} elseif ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
				} elseif ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top" width="50px">
						#<?= $rowDinner['id'] ?><br />
						<span style="font-size:smaller;"><?= $rowDinner['payment_status'] ?></span>
					</td>
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $invoiceDetails['id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	if ($resFetchHotelBooking) {
		?>
		<table width="100%"
			style="border:0px; border-top: thin dashed #333333; background:#CCCCFF; font-size:9px !important; display:none;"
			use="userDetails" indx="Accommodation">
			<?
			foreach ($resFetchHotelBooking as $key => $rowAccommodation) {
				$invoiceDetails = getInvoiceDetails($rowAccommodation['refference_invoice_id']);
				if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
				} elseif ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
				} elseif ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top" width="50px">
						#<?= $rowAccommodation['id'] ?><br />
						<span style="font-size:smaller;"><?= $rowAccommodation['payment_status'] ?></span>
					</td>
					<td align="left" valign="top">
						<i class="fa fa-building" aria-hidden="true"></i>&nbsp;<?= $rowAccommodation['hotel_name'] ?>
						<br />
						<i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;<?= $rowAccommodation['checkin_date'] ?> to
						<?= $rowAccommodation['checkout_date'] ?>
						<br />
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $invoiceDetails['id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	if ($resTotPlusFetch) {
		?>
		<table width="100%"
			style="border:0px; border-top: thin dashed #333333; background:#E0E0C2; font-size:9px !important; display:none;"
			use="userDetails" indx="TotPlus">
			<?
			foreach ($resTotPlusFetch as $key => $rowTotPlus) {
				$invoiceDetails = getInvoiceDetails($rowTotPlus['refference_invoice_id']);
				if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top" width="50px">
						#<?= $rowTotPlus['id'] ?><br />
						<span style="font-size:smaller;"><?= $rowTotPlus['payment_status'] ?></span>
					</td>
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $invoiceDetails['id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	if ($resCervicalBreastCancerFetch) {
		?>
		<table width="100%"
			style="border:0px; border-top: thin dashed #333333; background:#FFFFB0; font-size:9px !important; display:none;"
			use="userDetails" indx="CervicalBreastCancer">
			<?
			foreach ($resCervicalBreastCancerFetch as $key => $rowWorkshop) {
				$invoiceDetails = getInvoiceDetails($rowWorkshop['refference_invoice_id']);
				if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top" width="50px">
						#<?= $rowWorkshop['id'] ?><br />
						<span style="font-size:smaller;"><?= $rowWorkshop['payment_status'] ?></span>
					</td>
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $invoiceDetails['id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	if ($resPerinealTearsFetch) {
		?>
		<table width="100%"
			style="border:0px; border-top: thin dashed #333333; background:#DEDEEF; font-size:9px !important; display:none;"
			use="userDetails" indx="PerinealTears">
			<?
			foreach ($resPerinealTearsFetch as $key => $rowWorkshop) {
				$invoiceDetails = getInvoiceDetails($rowWorkshop['refference_invoice_id']);
				if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top" width="50px">
						#<?= $rowWorkshop['id'] ?><br />
						<span style="font-size:smaller;"><?= $rowWorkshop['payment_status'] ?></span>
					</td>
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $invoiceDetails['id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	if ($resLapSutureFetch) {
		?>
		<table width="100%"
			style="border:0px; border-top: thin dashed #333333; background:#FFDFDF; font-size:9px !important; display:none;"
			use="userDetails" indx="LapSuture">
			<?
			foreach ($resLapSutureFetch as $key => $rowWorkshop) {
				$invoiceDetails = getInvoiceDetails($rowWorkshop['refference_invoice_id']);
				if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
				}
				?>
				<tr style="border:0px;">
					<td align="left" valign="top" width="50px">
						#<?= $rowWorkshop['id'] ?><br />
						<span style="font-size:smaller;"><?= $rowWorkshop['payment_status'] ?></span>
					</td>
					<td align="left" valign="top">
						<i class="fa fa-list-alt" aria-hidden="true"
							title="Code"></i>&nbsp;<b><?= $invoiceDetails['invoice_number'] ?></b>&nbsp;&nbsp;<span
							style="font-size:smaller;">[<?= $invoiceDetails['payment_status'] ?>]</span><br />
						<?= $type ?>
					</td>
					<td width="80px" align="right" valign="top">
						<a href="print.invoice.php?user_id=<?= $invoiceDetails['delegate_id'] ?>&invoice_id=<?= $invoiceDetails['id'] ?>"
							target="_blank">
							<i class="fa fa-file-text-o" aria-hidden="true"></i>
						</a>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
	exit();
}

function applyWorkshopOld($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();
	// DETAILS INSERTING PROCESS
	$workshopIdArr = $_REQUEST['workshop_id'];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	$delegateId = $_REQUEST['delegate_id'];
	$clsfId = getUserClassificationId($delegateId);
	$cutoffId = $_REQUEST['tariff_cutoff_id'];

	if ($_REQUEST['workshop_id']) {
		if ($_REQUEST['registration_type_add'] == "GENERAL" || $_REQUEST['registration_type_add'] == "ZERO_VALUE") {
			insertingSlipDetails($delegateId, 'OFFLINE', 'GENERAL');
		}

		if ($spotUser != '') {
			if ($_REQUEST['registration_type_add'] == "GENERAL") {
				if ($clsfId == 11) {
					$payment_status = "ZERO_VALUE";
				} else {
					$payment_status = "PAID";
				}
			} else if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
				$payment_status = "ZERO_VALUE";
			}

			$sqlUpdateSlipRequest['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
											  SET  `invoice_request` = '" . $spotUser . "',													  
													  `invoice_type` = '" . $spotUser . "',
													  `payment_status` = '" . $payment_status . "'
													  WHERE `status` = 'A'
															AND `id` = '" . $mycms->getSession('SLIP_ID') . "' ";
			$mycms->sql_update($sqlUpdateSlipRequest, false);
		}

		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
								SET `payment_status` = 'ZERO_VALUE'
								WHERE `id` = '" . $mycms->getSession('SLIP_ID') . "' ";
			$mycms->sql_update($sqlUpdateSlip, false);

			$SqlUpdateUserDetails['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
								SET `workshop_payment_status` = 'ZERO_VALUE'
								WHERE `id` = '" . $delegateId . "' ";
			$mycms->sql_update($SqlUpdateUserDetails, false);
		}

		foreach ($workshopIdArr as $key => $workshopId) {
			$workshopDetailArray[$workshopId]['delegate_id'] = $delegateId;
			$workshopDetailArray[$workshopId]['workshop_id'] = $workshopId;
			$workshopDetailArray[$workshopId]['tariff_cutoff_id'] = $cutoffId;
			$workshopDetailArray[$workshopId]['workshop_tarrif_id'] = getWorkshopTariffId($workshopId, $cutoffId, $clsfId);
			$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
			$workshopDetailArray[$workshopId]['booking_mode'] = 'OFFLINE';
			$workshopDetailArray[$workshopId]['registration_type'] = 'GENERAL';
			$workshopDetailArray[$workshopId]['refference_invoice_id'] = 0; // Need To Edit
			$workshopDetailArray[$workshopId]['refference_slip_id'] = (!$mycms->isSession('SLIP_ID')) ? 0 : $mycms->getSession('SLIP_ID');
			$workshopDetailArray[$workshopId]['payment_status'] = $_REQUEST['registration_type_add'] == "ZERO_VALUE" ? 'ZERO_VALUE' : 'UNPAID';
		}
		$workshopReqId = insertingWorkshopDetails($workshopDetailArray);

		foreach ($workshopReqId as $key => $reqId) {
			$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP');
			if ($spotUser != '') {
				$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
											  SET `invoice_request`  = '" . $spotUser . "',
													`payment_status` = '" . $payment_status . "'													  
											WHERE `status` = 'A'
												  AND `id` = '" . $invoiceIdWrkshp . "' ";
				$mycms->sql_update($sqlUpdateInvoiceRequest, false);

				$sqlUpdateWorkshopRequest['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . " 
														  SET `payment_status` = '" . $payment_status . "'
														  WHERE `id` = '" . $reqId . "' 
														  AND `status` = 'A' ";
				$mycms->sql_update($sqlUpdateWorkshopRequest, false);
			}

			if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
				zeroValueInvoiceUpdate($invoiceIdWrkshp, 'WORKSHOP');
				gstInsertionInInvoice($invoiceIdWrkshp);
			}
		}
		
		if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
			updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
		}
		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") // Free Workshop for PGT
		{
			offline_conference_registration_confirmation_workshop_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");
			// complementary_workshop_confirmation_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");
		} elseif ($_REQUEST['registration_type_add'] == "GENERAL") {
			$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
		}

		if ($spotUser != '') {
			$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
			$payment_date = date('Y-m-d');

			$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
													   SET `payment_status` = 'PAID',
														   `collected_by` = '" . $loggedUserID . "',
														   `payment_type` = 'SPOT',
														   `payment_date` = '" . $payment_date . "',
														   `amount` = '" . $paymentDetailsAmount . "'
													 WHERE `id` = '" . $paymentId . "' 
													   AND `status` = 'A'";

			$mycms->sql_update($sqlUpdatePaymentRequest, false);

			if ($_REQUEST['registration_type_add'] == "GENERAL") {
				//offline_conference_registration_confirmation_workshop_message($delegateId,$paymentId, $mycms->getSession('SLIP_ID'),'SEND');
			}
		}
	}
     
}

function applyWorkshop($mycms, $cfg)
{
	
	$loggedUserID = $mycms->getLoggedUserId();
	// DETAILS INSERTING PROCESS
	$workshopIdArr = $_REQUEST['workshop_id'];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	$delegateId = $_REQUEST['delegate_id'];
	$clsfId = getUserClassificationId($delegateId);
	$cutoffId = $_REQUEST['tariff_cutoff_id'];
     
	if ($_REQUEST['workshop_id']) {
		
	
		foreach ($workshopIdArr as $key => $workshopId) {
			$onlyWrkshopclsfId = $_REQUEST['workshop_classification_id'][$workshopId];

			$workshopDetailArray[$workshopId]['delegate_id'] = $delegateId;
			$workshopDetailArray[$workshopId]['workshop_id'] = $workshopId;
			$workshopDetailArray[$workshopId]['tariff_cutoff_id'] = $_REQUEST['workshop_cutoff'];	
		
			$workshopDetailArray[$workshopId]['booking_mode'] = 'OFFLINE';
			
			if($_REQUEST['tarif_regis_id']==0){
			    $workshopDetailArray[$workshopId]['registration_type'] = 'ONLYWORKSHOP';
				if($onlyWrkshopclsfId == ''){
					$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getOnlyWorkshopTariffId($workshopId, $_REQUEST['workshop_cutoff'],0);
					$workshopDetailArray[$workshopId]['registration_classification_id'] = 0;

				}else{
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getOnlyWorkshopTariffId($workshopId, $_REQUEST['workshop_cutoff'],0,$onlyWrkshopclsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $_REQUEST['tarif_regis_id'];

				}

			}else{
				$workshopDetailArray[$workshopId]['registration_type'] = 'GENERAL';
				$workshopDetailArray[$workshopId]['workshop_tarrif_id'] = getWorkshopTariffId($workshopId,  $_REQUEST['workshop_cutoff'],  $_REQUEST['tarif_regis_id']);
			   $workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
			}
			$workshopDetailArray[$workshopId]['refference_invoice_id'] = 0; // Need To Edit
			$workshopDetailArray[$workshopId]['refference_slip_id'] = (!$mycms->isSession('SLIP_ID')) ? 0 : $mycms->getSession('SLIP_ID');
			$workshopDetailArray[$workshopId]['payment_status'] = $_REQUEST['paymentstatus'];
		}
		      
		$workshopReqId = insertingWorkshopDetails($workshopDetailArray);
		foreach ($workshopReqId as $key => $reqId) {
		    $invoiceIdWrkshp = insertingInvoiceDetails($reqId,'WORKSHOP',$_REQUEST['registration_request'], '','',$_REQUEST['paymentstatus']);
			// if($_REQUEST['invoice_generation_mode_edit'] == "COMPLIMENTARY")
			// {
			// 	zeroValueInvoiceUpdate($invoiceIdWrkshp,'WORKSHOP', $slip_id);
			// }
			// else if($_REQUEST['invoice_generation_mode_edit'] == "ZERO_VALUE")
			// {
			// 	zeroValueInvoiceUpdate($invoiceIdWrkshp,'WORKSHOP', $slip_id);
			// }
		}
		
		$payment_mode = trim($_REQUEST['registrationMode']);
		$payment_array = array();
		
	
	}
     
}
function relocateMasterWorkshop()
{
	global $mycms, $cfg;

	include_once('../section_cancelation/includes/function.php');

	$delegateId = $_REQUEST['delegateId'];
	$workshopIds = $_REQUEST['workshop_id'];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT

	$workShopOfDelegate = getWorkshopDetailsOfDelegate($delegateId);
	$regClassfId = getUserClassificationId($delegateId);

	foreach ($workShopOfDelegate as $lk => $selWrkShp) {
		$invoiceId = $selWrkShp['refference_invoice_id'];
		$invoiceDetails = getInvoiceDetails($invoiceId);

		if ($invoiceDetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION' && $selWrkShp['type'] != 'POST-CONFERENCE') {
			$workshopDetails = getWorkshopDetails($invoiceDetails['refference_id']);

			$sqlUpdateInvoice = array();
			$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
												  SET status = 'D'
												WHERE `id` = '" . $selWrkShp['refference_invoice_id'] . "'";
			$mycms->sql_update($sqlUpdateInvoice, false);


			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = " UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `status` = ?
										  WHERE `id` = ?";

			$sqlUpdate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
			$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $selWrkShp['id'], 'TYP' => 's');
			$mycms->sql_update($sqlUpdate, false);
		}
	}

	$confRegInv = invoiceDetailsOfDelegate($delegateId, " AND ( service_type = 'DELEGATE_CONFERENCE_REGISTRATION' OR service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION') ");

	$lastSlipId = $confRegInv[0]['slip_id'];
	$lastCutoffId = $confRegInv[0]['service_tariff_cutoff_id'];

	if ($lastCutoffId == '') {
		$lastCutoffId = getTariffCutoffId();
	}

	if ($lastSlipId == '') {
		$lastSlipId = insertingSlipDetails($delegateId, 'OFFLINE', 'GENERAL', date("Y-m-d"), 'BACK');
	}

	$mycms->setSession('SLIP_ID', $lastSlipId);

	$workshopDetailArray = array();

	foreach ($workshopIds as $kk => $workshopId) {
		$workshopDetailArray[$workshopId]['delegate_id'] = $delegateId;
		$workshopDetailArray[$workshopId]['workshop_id'] = $workshopId;
		$workshopDetailArray[$workshopId]['tariff_cutoff_id'] = $lastCutoffId;
		$workshopDetailArray[$workshopId]['workshop_tarrif_id'] = getWorkshopTariffId($workshopId, $lastCutoffId, $regClassfId);
		$workshopDetailArray[$workshopId]['registration_classification_id'] = $regClassfId;
		$workshopDetailArray[$workshopId]['booking_mode'] = 'OFFLINE';
		$workshopDetailArray[$workshopId]['registration_type'] = 'GENERAL';
		$workshopDetailArray[$workshopId]['refference_invoice_id'] = 0; // Need To Edit
		$workshopDetailArray[$workshopId]['refference_slip_id'] = $lastSlipId;
		$workshopDetailArray[$workshopId]['payment_status'] = 'UNPAID';
	}

	//echo '<pre>>>'; print_r($workshopDetailArray); echo '</pre>'; die();

	$workshopReqId = insertingWorkshopDetails($workshopDetailArray);
	foreach ($workshopReqId as $key => $reqId) {
		$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP', 'GENERAL', date("Y-m-d"));

		zeroValueInvoiceUpdate($invoiceIdWrkshp, 'WORKSHOP', $lastSlipId);
		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = " UPDATE " . _DB_REQUEST_WORKSHOP_ . "
										SET `payment_status` = ?
									  WHERE `id` = ?";

		$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
		$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $reqId, 'TYP' => 's');
		$mycms->sql_update($sqlUpdate, false);
	}

	//$output = workshop_adjustment_confirmation_message($delegateId, $paymentDetails['id'], $newInvoiceDetails['slip_id'], $newInvoiceId, 'SEND');

	$mycms->removeSession('SLIP_ID');

	if ($spotUser != '') {
		pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
	} else {
		pageRedirection("registration_new.php", "1"); //,"&show=reallocationOfWorkshop"
	}
}

function relocateWorkshop()
{
	global $mycms, $cfg;

	$invoiceId = $_REQUEST['invoiceId'][0];
	$delegateId = $_REQUEST['delegateId'];
	$workshopId = $_REQUEST['workshop_id'][0];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT

	//echo "<pre>";echo $workshopId;echo "</pre>"; die('ppppp');
	$invoiceDetails = getInvoiceDetails($invoiceId);
	$workshopDetails = getWorkshopDetails($invoiceDetails['refference_id']);

	$workshopTariffId = getWorkshopTariffId($workshopId, $workshopDetails['tariff_cutoff_id'], $workshopDetails['registration_classification_id']);

	$returnArray = discountAmount($invoiceId);
	$originalAmount = $returnArray['PRE_DISCOUNT_AMOUNT'];
	$percentage = $returnArray['PERCENTAGE'];
	$totalAmount = $returnArray['TOTAL_AMOUNT'];
	$discountAmount = $returnArray['DISCOUNT'];
	$internetHandAmount = $returnArray['INT_HND_DISCOUNT'];

	include_once('../section_cancelation/includes/function.php');

	$sqlInsertWorkshopRequest = array();
	$sqlInsertWorkshopRequest['QUERY'] = "  INSERT INTO " . _DB_REQUEST_WORKSHOP_ . " 
																(`delegate_id`, `workshop_id`, `tariff_cutoff_id`, `registration_type`, `workshop_tarrif_id`, 
																 `booking_mode`, `payment_status`, `registration_classification_id`, `refference_invoice_id`, 
																 `refference_slip_id`, `status`, `created_ip`, `created_sessionId`, `created_browser`, `created_dateTime`)
														(SELECT  `delegate_id`, '" . $workshopId . "', `tariff_cutoff_id`, `registration_type`, '" . $workshopTariffId . "', 
																 `booking_mode`, `payment_status`, `registration_classification_id`, '0', 
																 `refference_slip_id`, 'A', '" . $_SERVER['REMOTE_ADDR'] . "', '" . session_id() . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . date('Y-m-d H:i:s') . "'
														   FROM  " . _DB_REQUEST_WORKSHOP_ . "
														  WHERE  `id` = '" . $invoiceDetails['refference_id'] . "')";
	$newWorkshopId = $mycms->sql_insert($sqlInsertWorkshopRequest);
	$sqlInsertInvoiceRequest = array();
	$sqlInsertInvoiceRequest['QUERY'] = " INSERT INTO " . _DB_INVOICE_ . " 
															 (`delegate_id`, `slip_id`, `invoice_number`, `invoice_date`, `invoice_request`,
															  `invoice_mode`, `currency`, `registration_type`, `refference_id`, `service_type`,
															  `tariff_ref_id`, `service_tariff_cutoff_id`, `service_unit_price`, `service_consumed_quantity`,
															  `service_product_price`, `internet_handling_percentage`,
															  `internet_handling_amount`, `service_total_price`, `service_grand_price`, `service_roundoff_price`, `payment_status`,
															  `status`, `remarks`, `created_ip`, `created_sessionId`, `created_browser`, `created_dateTime`)
													  (SELECT `delegate_id`, `slip_id`, '-1', `invoice_date`, `invoice_request`,
															  `invoice_mode`, `currency`, `registration_type`, '" . $newWorkshopId . "', `service_type`,
															  '" . $workshopTariffId . "', `service_tariff_cutoff_id`, `service_unit_price`, `service_consumed_quantity`,
															  `service_product_price`, `internet_handling_percentage`,
															  `internet_handling_amount`, `service_total_price`, `service_grand_price`, `service_roundoff_price`, `payment_status`,
															  'A', 'Adjusted Workshop', '" . $_SERVER['REMOTE_ADDR'] . "', '" . session_id() . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . date('Y-m-d H:i:s') . "'
														 FROM " . _DB_INVOICE_ . " 
														WHERE `id` = '" . $invoiceId . "')";
	$newInvoiceId = $mycms->sql_insert($sqlInsertInvoiceRequest);


	$seqNumber = number_pad($newInvoiceId, 6);
	//$newInvoiceNumber = "RCOG19/19-20/".$seqNumber;
	$newInvoiceNumber = $cfg['invoive_number_format'] . "-" . $seqNumber;

	$sqlUpdateInvoice = array();
	$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
											  SET invoice_number = '" . $newInvoiceNumber . "'
											  WHERE `id` = '" . $newInvoiceId . "'";

	$mycms->sql_update($sqlUpdateInvoice, false);



	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
											SET `remarks` = 'Adjusted Workshop'
										    WHERE `id` = '" . $newInvoiceId . "'";
	$mycms->sql_update($sqlUpdate, false);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = " UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `refference_invoice_id` = '" . $newInvoiceId . "'
										  WHERE `id` = '" . $newWorkshopId . "'";
	$mycms->sql_update($sqlUpdate, false);

	$newInvoiceDetails = getInvoiceDetails($newInvoiceId);

	$cancelId = cancelInvoice($delegateId, $invoiceId);
	approveProveProcess($cancelId, $delegateId, $invoiceId);
	refundCancelInvoiceProcess($delegateId, $invoiceId);

	refundProcess($cancelId, $totalAmount);
	walletInProcess($delegateId, $totalAmount, 'Refund for ' . $invoiceDetails['invoice_number'], $invoiceId);

	walletOutProcess($delegateId, $totalAmount, 'Payment for ' . $newInvoiceDetails['invoice_number'], $newInvoiceId);

	$sqlUpdateUser = array();
	$sqlUpdateUser['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
												SET `isWorkshop` = 'Y'											
											    WHERE `id` = '" . $delegateId . "'";
	$mycms->sql_update($sqlUpdateUser, false);

	$paymentDetails = paymentDetails($newInvoiceDetails['slip_id']);

	if ($spotUser != '') {
		pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
	} else {
		//$output = workshop_adjustment_confirmation_message($delegateId, $paymentDetails['id'], $newInvoiceDetails['slip_id'], $newInvoiceId, 'SEND');		
		pageRedirection("registration_new.php", "1"); //,"&show=reallocationOfWorkshop"
	}
}



function changeRegClassification()
{
	global $mycms, $cfg, $loggedUserID;

	$mycms->removeSession('SLIP_ID');

	include_once('../section_cancelation/includes/function.php');

	$delegateId = $_REQUEST['delegateId'];
	$regCutoffId = $_REQUEST['regCutoffId'];
	$regInvoiceId = $_REQUEST['regInvoiceId'];
	$regClassfId = $_REQUEST['regClassfId'];

	$selHotelId = $_REQUEST['hotel_id'];
	$selAccommodationPackageId = $_REQUEST['accommodation_package_id'];
	$selAccommodationCheckin = $_REQUEST['accommodation_checkIn'];
	$selAccommodationCheckout = $_REQUEST['accommodation_checkOut'];

	$payment_mode = $_REQUEST['paymentMode'];
	$selectedpayment_mode = $_REQUEST['paymentMode'];

	$applied_discount = $_REQUEST['give_discount'];

	$regUserDetails = getUserDetails($delegateId);
	$regInvoiceDetails = getInvoiceDetails($regInvoiceId);
	$regClasfDetails = getRegClsfDetails($regClassfId);
	$regClasfType = $regClasfDetails['type'];

	$registration_classification_id = $_REQUEST['registration_classification_id'][0];
	$registration_classification_details = getRegClsfDetails($registration_classification_id);
	$registration_classification_type = $registration_classification_details['type'];
	$registration_classification_PayMode = $registration_classification_details['payment_type'];
	$registration_invoiceMode = $regInvoiceDetails['invoice_mode'];

	$opertion = '';
	if ($regClasfType == $registration_classification_type) {
		$opertion = "SAMETOSAME";
	} elseif ($regClasfType == 'COMBO' && $registration_classification_type == 'DELEGATE') {
		$opertion = "DOWNGRADE";
	} elseif ($regClasfType == 'DELEGATE' && $registration_classification_type == 'COMBO') {
		$opertion = "UPGRADE";
	}

	//echo "<pre>"; print_r($_REQUEST); echo "</pre>";
	//echo "<pre>"; print_r($regInvoiceDetails); echo "</pre>";
	//echo "<pre>"; print_r($regClasfDetails); echo "</pre>";
	//echo "<pre>"; print_r($regClasfType); echo "</pre>";
	//echo "<pre>"; print_r($registration_classification_details); echo "</pre>";
	//echo "<pre>"; print_r($registration_classification_type); echo "</pre>";
	//echo "<pre>"; print_r($opertion); echo "</pre>";

	$reg_totalInternetHandlingAmt = 0;

	$previousServiceAmount = 0;

	switch ($opertion) {
		case 'UPGRADE':
			$userFetchInvoice = getInvoiceDetailsquery("", $delegateId, "");
			foreach ($userFetchInvoice as $key => $rowFetchInvoice) {
				$invoiceId = $rowFetchInvoice['id'];

				$returnArray = discountAmount($invoiceId);
				//$totalAmount 			= $returnArray['TOTAL_AMOUNT']-$returnArray['INT_HND_AMOUNT'];
				$totalAmount = $returnArray['BASIC_AMOUNT'] + $returnArray['CGST_AMOUNT'] + $returnArray['SGST_AMOUNT'];

				$previousServiceAmount += $totalAmount;

				if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {

					$invoiceMode = $rowFetchInvoice['invoice_mode'];
					if ($invoiceMode == 'ONLINE') {
						$invoice_internet_handling_amount = $rowFetchInvoice['internet_handling_amount'];
						$reg_totalInternetHandlingAmt += $invoice_internet_handling_amount;
					} else {
						$invoice_internet_handling_amount = 0;
					}

					$cancelId = cancelInvoice($delegateId, $invoiceId);
					approveProveProcess($cancelId, $delegateId, $invoiceId);
					cancelOnlyInvoice($invoiceId);

					refundProcess($cancelId, $totalAmount);
					walletInProcess($delegateId, $totalAmount, 'Refund for ' . $rowFetchInvoice['invoice_number'], $invoiceId);
					/*if($invoiceMode=='ONLINE' && $payment_mode=='ONLINE')
						{
							walletInProcess($delegateId,$invoice_internet_handling_amount,'Refund Internet Handling Charge for '.$rowFetchInvoice['invoice_number'],$invoiceId);
						}*/
				}

				if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
					$sqlSelectRequest = array();
					$sqlSelectRequest['QUERY'] = "	   SELECT * 
																 FROM " . _DB_REQUEST_DINNER_ . " 
																WHERE `refference_invoice_id` = ?";

					$sqlSelectRequest['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');

					$resSelectRequest = $mycms->sql_select($sqlSelectRequest);
					$rowSelectRequest = $resSelectRequest[0];

					if ($rowSelectRequest['refference_id'] == $delegateId) {

						$invoiceMode = $rowFetchInvoice['invoice_mode'];
						if ($invoiceMode == 'ONLINE') {
							$invoice_internet_handling_amount = $rowFetchInvoice['internet_handling_amount'];
							$reg_totalInternetHandlingAmt += $invoice_internet_handling_amount;
						} else {
							$invoice_internet_handling_amount = 0;
						}

						$cancelId = cancelInvoice($delegateId, $invoiceId);
						approveProveProcess($cancelId, $delegateId, $invoiceId);
						refundCancelInvoiceProcess($delegateId, $invoiceId);

						refundProcess($cancelId, $totalAmount);
						walletInProcess($delegateId, $totalAmount, 'Refund for ' . $rowFetchInvoice['invoice_number'], $invoiceId);
						/*if($invoiceMode=='ONLINE' && $payment_mode=='ONLINE')
							{
								walletInProcess($delegateId,$invoice_internet_handling_amount,'Refund Internet Handling Charge for '.$rowFetchInvoice['invoice_number'],$invoiceId);
							}*/
					}
				}
			}
			break;

		case 'DOWNGRADE':
			$sqlupdateRequest = array();
			$sqlupdateRequest['QUERY'] = "	   UPDATE " . _DB_REQUEST_DINNER_ . " 
														  SET `status` = ?
														WHERE `refference_id` = ?
														  AND `refference_invoice_id` = ?";

			$sqlupdateRequest['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => 'D', 'TYP' => 's');
			$sqlupdateRequest['PARAM'][] = array('FILD' => 'refference_id', 'DATA' => $delegateId, 'TYP' => 's');
			$sqlupdateRequest['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $regInvoiceId, 'TYP' => 's');
			$mycms->sql_update($sqlupdateRequest, false);

			$sqlupdateRequest = array();
			$sqlupdateRequest['QUERY'] = "	   UPDATE " . _DB_REQUEST_ACCOMMODATION_ . " 
														  SET `status` = ?
														WHERE `user_id` = ?
														  AND `refference_invoice_id` = ?";

			$sqlupdateRequest['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => 'D', 'TYP' => 's');
			$sqlupdateRequest['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');
			$sqlupdateRequest['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $regInvoiceId, 'TYP' => 's');
			$mycms->sql_update($sqlupdateRequest, false);

			$returnArray = discountAmount($regInvoiceId);
			//$totalAmount 					= $returnArray['TOTAL_AMOUNT']-$returnArray['INT_HND_AMOUNT'];
			$totalAmount = $returnArray['BASIC_AMOUNT'] + $returnArray['CGST_AMOUNT'] + $returnArray['SGST_AMOUNT'];

			$previousServiceAmount += $totalAmount;

			$invoiceMode = $regInvoiceDetails['invoice_mode'];
			if ($invoiceMode == 'ONLINE') {
				$invoice_internet_handling_amount = $rowFetchInvoice['internet_handling_amount'];
				$reg_totalInternetHandlingAmt += $invoice_internet_handling_amount;
			} else {
				$invoice_internet_handling_amount = 0;
			}

			$cancelId = cancelInvoice($delegateId, $regInvoiceId);
			approveProveProcess($cancelId, $delegateId, $regInvoiceId);
			cancelOnlyInvoice($regInvoiceId);

			refundProcess($cancelId, $totalAmount);
			walletInProcess($delegateId, $totalAmount, 'Refund for ' . $regInvoiceDetails['invoice_number'], $regInvoiceId);
			/*if($invoiceMode=='ONLINE' && $payment_mode=='ONLINE')
				{
					walletInProcess($delegateId,$invoice_internet_handling_amount,'Refund Internet Handling Charge for '.$rowFetchInvoice['invoice_number'],$invoiceId);
				}*/
			break;

		case 'SAMETOSAME':
			$returnArray = discountAmount($regInvoiceId);
			//$totalAmount 			= $returnArray['TOTAL_AMOUNT']-$returnArray['INT_HND_AMOUNT'];
			$totalAmount = $returnArray['BASIC_AMOUNT'] + $returnArray['CGST_AMOUNT'] + $returnArray['SGST_AMOUNT'];

			$previousServiceAmount += $totalAmount;

			$invoiceMode = $regInvoiceDetails['invoice_mode'];
			if ($invoiceMode == 'ONLINE') {
				$invoice_internet_handling_amount = $rowFetchInvoice['internet_handling_amount'];
				$reg_totalInternetHandlingAmt += $invoice_internet_handling_amount;
			} else {
				$invoice_internet_handling_amount = 0;
			}

			$cancelId = cancelInvoice($delegateId, $regInvoiceId);
			approveProveProcess($cancelId, $delegateId, $regInvoiceId);
			cancelOnlyInvoice($regInvoiceId);

			refundProcess($cancelId, $totalAmount);
			walletInProcess($delegateId, $totalAmount, 'Refund for ' . $regInvoiceDetails['invoice_number'], $regInvoiceId);
			/*if($invoiceMode=='ONLINE' && $payment_mode=='ONLINE')
				{
					walletInProcess($delegateId,$invoice_internet_handling_amount,'Refund Internet Handling Charge for '.$rowFetchInvoice['invoice_number'],$invoiceId);
				}*/
			break;
	}

	$sqlUpdateUserPhaseTwo = array();
	$sqlUpdateUserPhaseTwo['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
													  SET `status` = ?,
													  	  `account_status` = ?,
													  	  `isAccommodation` = ?,
														  `registration_classification_id` = ?,
														  `registration_payment_status` = ?,
														  `registration_mode` = ?,
														  `modified_ip` = ?,
														  `modified_sessionId` = ?,
														  `modified_browser` = ?,
														  `modified_dateTime` = ?
													WHERE `id` = ?";

	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'account_status', 'DATA' => 'REGISTERED', 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'isAccommodation', 'DATA' => 'N', 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'registration_classification_id', 'DATA' => $registration_classification_id, 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $payment_mode, 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'modified_browser', 'DATA' => $_SERVER['HTTP_USER_AGENT'], 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sqlUpdateUserPhaseTwo['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');

	$mycms->sql_update($sqlUpdateUserPhaseTwo, false);

	//echo "<pre>"; print_r($sqlUpdateUserPhaseTwo); echo "</pre>";

	if ($mycms->getSession('SLIP_ID') == "") {
		$mycms->setSession('LOGGED.USER.ID', $delegateId);
		insertingSlipDetails($delegateId, $payment_mode, $regUserDetails['registration_request'], $date, 'BACK');
	}

	$registration_slip_id = $mycms->getSession('SLIP_ID');

	$invoiceIdConf = insertingInvoiceDetails($delegateId, 'CONFERENCE', $regUserDetails['registration_request'], date('Y-m-d'));

	if ($registration_classification_PayMode == "COMPLIMENTARY" || $registration_classification_PayMode == "ZERO_VALUE") {
		if ($registration_classification_PayMode == "COMPLIMENTARY") {
			complimentarySlipUpdate($registration_slip_id);
			zeroValueInvoiceUpdate($invoiceIdConf, 'CONFERENCE', $registration_slip_id);
		} else if ($registration_classification_PayMode == "ZERO_VALUE") {
			zeroValueSlipUpdate($registration_slip_id);
			zeroValueInvoiceUpdate($invoiceIdConf, 'CONFERENCE', $registration_slip_id);
		}

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
										SET `payment_status` = ?
									  WHERE `id` = ?";

		$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
		$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceIdConf, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		$sqlUpdateUserReg = array();
		$sqlUpdateUserReg['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												 SET `registration_payment_status` = ?
										  	   WHERE `id` = ?";

		$sqlUpdateUserReg['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
		$sqlUpdateUserReg['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdateUserReg, false);
	}

	//echo "<pre>"; print_r($invoiceIdConf); echo "</pre>";

	if ($registration_classification_type == 'COMBO') {
		$dinnerComboDetails = array(2);
		foreach ($dinnerComboDetails as $key => $val) {
			$dinnerDetailsArray[$val]['delegate_id'] = $delegateId;
			$dinnerDetailsArray[$val]['refference_id'] = $delegateId;
			$dinnerDetailsArray[$val]['package_id'] = $val;
			$dinnerDetailsArray[$val]['tariff_cutoff_id'] = $regCutoffId;
			$dinnerDetailsArray[$val]['booking_quantity'] = 1;
			$dinnerDetailsArray[$val]['booking_mode'] = $payment_mode;
			$dinnerDetailsArray[$val]['refference_invoice_id'] = $invoiceIdConf; // Need To Edit
			$dinnerDetailsArray[$val]['refference_slip_id'] = $registration_slip_id;
			$dinnerDetailsArray[$val]['payment_status'] = 'ZERO_VALUE';
		}

		$dinerReqId = insertingDinnerDetails($dinnerDetailsArray);

		if ($clsfId != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
			$accomodationDetails['user_id'] = $delegateId;
			$accomodationDetails['hotel_id'] = $selHotelId;
			$accomodationDetails['package_id'] = $selAccommodationPackageId;
			$accomodationDetails['tariff_cutoff_id'] = $regCutoffId;
			$accomodationDetails['checkin_date'] = getCheckInDateById($selAccommodationCheckin, 1);
			$accomodationDetails['checkout_date'] = getCheckOutDateById($selAccommodationCheckout, 1);
			$accomodationDetails['booking_quantity'] = 1;
			$accomodationDetails['type'] = "COMBO";
			$accomodationDetails['refference_invoice_id'] = $invoiceIdConf;
			$accomodationDetails['refference_slip_id'] = $registration_slip_id;
			$accomodationDetails['booking_mode'] = $payment_mode;

			$accomodationDetails['preffered_accommpany_name'] = '';
			$accomodationDetails['preffered_accommpany_email'] = '';
			$accomodationDetails['preffered_accommpany_mobile'] = '';

			$accomodationDetails['payment_status'] = 'ZERO_VALUE';

			$accompReqId = insertingAccomodationDetails($accomodationDetails);
		}
	}

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
										 SET `remarks` = 'ALTERED REGISTRATION'
									   WHERE `id` = '" . $invoiceIdConf . "'";
	$mycms->sql_update($sqlUpdate, false);

	if (floatval($applied_discount) > 0) {
		updateonDiscount($registration_slip_id, $applied_discount);
	}

	$newInvoiceDetails = getInvoiceDetails($invoiceIdConf);

	$returnArray = discountAmount($invoiceIdConf);
	$invTotalAmount = $returnArray['TOTAL_AMOUNT'];
	$invInternetHandAmount = $returnArray['INT_HND_AMOUNT'];

	if ($selectedpayment_mode == 'ONLINE') {

		$differenceAmount = floatval($invTotalAmount) - floatval($previousServiceAmount);

		if ($differenceAmount > 0) {
			$internetOnDiff = calculateTaxAmmount($differenceAmount, $cfg['INTERNET.HANDLING.PERCENTAGE']);

			$deductibleInternetHandling = floatval($invInternetHandAmount) - floatval($internetOnDiff);

			if ($deductibleInternetHandling > 0) {
				walletInProcess($delegateId, $deductibleInternetHandling, 'Additional Amount for Deduction in Registration Upgrade', 0);
			}
		}
	}

	$walletBalance = userWalletBalance($delegateId);
	if ($walletBalance > $invTotalAmount) {
		$walletPaidAmount = $invTotalAmount;
	} else {
		$walletPaidAmount = $walletBalance;
	}

	/*if($registration_invoiceMode=='OFFLINE' && $payment_mode=='ONLINE' && $walletPaidAmount > 0)
		{
			$amount4Dioscount = $invTotalAmount - $walletPaidAmount;
			$discountAmount	 = ($cfg['INTERNET.HANDLING.PERCENTAGE']/100)*$amount4Dioscount;
			if($discountAmount)
			{
				updateonDiscount($registration_slip_id, $discountAmount);
			}
		}*/

	//echo "<pre> walletPaidAmount"; print_r($walletPaidAmount); echo "</pre>";

	if ($walletPaidAmount > 0) {
		$_REQUEST['slip_id'] = $registration_slip_id;
		$_REQUEST['delegate_id'] = $delegateId;
		$_REQUEST['payment_selected'][0] = 0;
		$_REQUEST['payment_mode'][0] = 'Cash';
		$_REQUEST['cash_deposit_date'][0] = date('Y-m-d');
		$_REQUEST['amount'][0] = $walletPaidAmount;

		$paymentId = setPaymentArea($mycms, $cfg, false);

		//echo "<pre>"; print_r($paymentId); echo "</pre>";

		unset($_REQUEST['paymentId']);
		unset($_REQUEST['slipId']);
		unset($_REQUEST['delegateId']);
		unset($_REQUEST['remarks']);
		unset($_REQUEST['payment_date']);
		unset($_REQUEST['amount']);

		$_REQUEST['paymentId'] = $paymentId[0];
		$_REQUEST['slipId'] = $registration_slip_id;
		$_REQUEST['delegateId'] = $delegateId;

		$_REQUEST['remarks'] = 'WALLET PAY';
		$_REQUEST['payment_date'] = date('Y-m-d');
		$_REQUEST['amount'] = $walletPaidAmount;

		make_partial_payment($mycms, $cfg, false);

		walletOutProcess($delegateId, $walletPaidAmount, 'Payment for ' . $newInvoiceDetails['invoice_number'], $invoiceIdConf);
	}
	//die('--EOC--');

	pageRedirection("registration_new.php", "1", "&show=invoice&id=" . $delegateId); //,"&show=reallocationOfWorkshop"
}

function setPaymentTermsTemplete()
{
	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];

	$slipDetails = slipDetails($slipId);
	$paymentDetails = paymentDetails($slipId)
		?>
	<form action="registration.process.php" name="frmPaymentPopup" id="frmPaymentPopup" method="post"
		onsubmit="return validatePaymentTermsSubmission(this)">
		<input type="hidden" name="act" id="act" value="setNewPaymentTermsProcess" />
		<input type="hidden" name="delegateId" id="delegateId" value="<?= $delegateId ?>" />
		<input type="hidden" name="slipId" id="slipId" value="<?= $slipId ?>" />
		<input type="hidden" name="paymentId" id="paymentId" value="<?= $paymentId ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="4" class="tcat">
					<span style="float:left">Set Payment Terms</span>
					<span class="close" onclick="closeSetPaymentTermsPopup()">X</span>
				</td>
			</tr>
			<tr>
				<td width="20%" align="left">Slip No</td>
				<td width="30%" align="left"><?= $slipDetails['slip_number'] ?></td>
				<td width="20%" align="left">Slip Date</td>
				<td width="30%" align="left"><?= $slipDetails['slip_date'] ?></td>
			</tr>
			<tr>
				<td align="left">Slip Amount</td>
				<td align="left"><?= $paymentDetails['amount'] ?></td>
				<td align="left">No Of Invoice</td>
				<td align="left"><?= invoiceCountOfSlip($slipId) ?></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">
					<?
					paymentArea();
					?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" align="left">

					<input type="submit" name="btnPayment" id="btnPayment" value="Set Payment Terms"
						class="btn btn-small btn-blue" />

				</td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
		</table>
	</form>
	<?
}

function counterPaymentConfirmationProcess($paymentId)
{
	global $cfg, $mycms;

	$paymentId = $paymentId;
	$slipId = $_REQUEST['slip_id'];
	$paymentRemark = $_REQUEST['remarks'];
	$paymentDate = date('Y-m-d');
	$delegateId = $_REQUEST['delegate_id'];
	$exhibitorCode = $_REQUEST['exhibitor_name'];
	$paidAmmount = invoiceAmountOfSlip($slipId);

	$sqlDelegateInfo['QUERY'] = "SELECT `user_type`,`user_full_name` FROM " . _DB_USER_REGISTRATION_ . " 
								WHERE `id` = '" . $delegateId . "' ";

	$delegateInfo = $mycms->sql_select($sqlDelegateInfo, false);


	$sqlexhibitor['QUERY'] = "SELECT `exhibitor_company_code`,`exhibitor_company_name`
									FROM " . _DB_EXIBITOR_COMPANY_ . " 
									WHERE `exhibitor_company_code` = '" . $exhibitorCode . "' ";

	$exhibitorInfo = $mycms->sql_select($sqlexhibitor, false);

	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
									SET `payment_date` = '" . $paymentDate . "',
										`payment_remark` = '" . $paymentRemark . "',
										`amount` = '" . $paidAmmount . "',
										`payment_status` = 'PAID'
								  WHERE `id` = '" . $paymentId . "'";

	$mycms->sql_update($sqlUpdatePayment, false);

	$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
									SET `payment_status` = 'PAID'
								  WHERE `id` = '" . $slipId . "'";

	$mycms->sql_update($sqlUpdateSlip, false);

	if ($exhibitorCode != "") {
		$sqlInsertExhibitorUser['QUERY'] = "INSERT INTO " . _DB_EXIBITOR_COMPANY_USERS_ . "
												SET `exhibitor_company_code` = '" . $exhibitorCode . "',
													`exhibitor_company_name` = '" . $exhibitorInfo[0]['exhibitor_company_name'] . "',
																	`amount` = '" . $paidAmmount . "',
															   `delegate_id` = '" . $delegateId . "',
															`payment_status` = 'PAID',
																	`status` = 'A', 
																 `user_type` = '" . $delegateInfo[0]['user_type'] . "',
															`user_full_name` = '" . $delegateInfo[0]['user_full_name'] . "',
															   `slip_number` = '" . $slipId . "' ";

		$mycms->sql_insert($sqlInsertExhibitorUser, false);
	}

	$activeInvoice = invoiceDetailsActiveOfSlip($slipId);
	foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
		if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `registration_payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `registration_payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `payment_status` = 'PAID'
										  WHERE `delegate_id` = '" . $valActiveInvoice['delegate_id'] . "'
										    AND `status`='A'";

			$mycms->sql_update($sqlUpdateWorkshop, false);

			$sqlUpdateAccom['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
											SET `payment_status` = 'PAID'
										  WHERE `user_id` = '" . $valActiveInvoice['delegate_id'] . "'
										    AND `status`='A'";

			$mycms->sql_update($sqlUpdateAccom, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `workshop_payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
		if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `registration_payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
											SET `payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `accommodation_payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_TOUR_REQUEST') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_TOUR_ . "
											SET `payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `tour_payment_status` = 'PAID'
										  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}

		$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
										SET `payment_status` = 'PAID'
									  WHERE `id` = '" . $valActiveInvoice['id'] . "'";

		$mycms->sql_update($sqlUpdateSlip, false);
	}
	//offline_conference_payment_confirmation_message($delegateId,$slipId , $paymentId, 'SEND');
	offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'SEND', 'BACK');
}

function complementaryPaymentConfirmationProcessOLd($slipId, $delegateId)
{
	global $cfg, $mycms;
	$paymentId = $paymentId;
	//$slipId 			  = $_REQUEST['slip_id'];
	$paymentRemark = $_REQUEST['remarks'];
	$paymentDate = date('Y-m-d');
	//$delegateId 		  = $_REQUEST['delegate_id'];
	$paidAmmount = invoiceAmountOfSlip($slipId);

	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
									SET `payment_date` = '" . $paymentDate . "',
										`payment_remark` = '" . $paymentRemark . "',
										`amount` = '" . $paidAmmount . "',
										`payment_status` = 'PAID'
								  WHERE `id` = '" . $paymentId . "'";

	//$mycms->sql_update($sqlUpdatePayment, false);

	$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
									SET `payment_status` = 'COMPLIMENTARY'
								  WHERE `id` = '" . $slipId . "'";

	$mycms->sql_update($sqlUpdateSlip, false);

	$activeInvoice = invoiceDetailsActiveOfSlip($slipId);
	foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
		if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `registration_payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `registration_payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdateWorkshop = array();
			$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `payment_status` = 'COMPLIMENTARY'
										  WHERE `delegate_id` = '" . $valActiveInvoice['delegate_id'] . "'
										    AND `status`='A'";

			$mycms->sql_update($sqlUpdateWorkshop, false);

			$sqlUpdateAccom = array();
			$sqlUpdateAccom['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
											SET `payment_status` = 'COMPLIMENTARY'
										  WHERE `user_id` = '" . $valActiveInvoice['delegate_id'] . "'
										    AND `status`='A'";

			$mycms->sql_update($sqlUpdateAccom, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `workshop_payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
		if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `registration_payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
											SET `payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
											SET `accommodation_payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_TOUR_REQUEST') {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_REQUEST . TOUR_ . "
											SET `payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `tour_payment_status` = 'COMPLIMENTARY'
											WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}

		$sqlUpdateSlip = array();
		$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
										SET `payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['id'] . "'";

		$mycms->sql_update($sqlUpdateSlip, false);
	}
	offline_conference_registration_confirmation_message($delegateId, $paymentId = "", $slipId, 'SEND');
}

function paymentConfirmation()
{
	global $cfg, $mycms;

	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];
	$userREGtype = $_REQUEST['userREGtype'];

	$slipDetails = slipDetails($slipId);
	$paymentDetails = paymentDetails($slipId);

	//echo "<pre>";
	//print_r($paymentDetails);
	//echo "</pre>";
	?>
	<form action="registration.process.php" name="frmPaymentPopup" id="frmPaymentPopup" method="post"
		onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" id="act" value="make_payment" />
		<input type="hidden" name="redirect" id="redirect" value="N" />
		<input type="hidden" name="delegateId" id="delegateId" value="<?= $delegateId ?>" />
		<input type="hidden" name="slipId" id="slipId" value="<?= $slipId ?>" />
		<input type="hidden" name="paymentId" id="paymentId" value="<?= $paymentId ?>" />
		<input type="hidden" name="userREGtype" id="userREGtype" value="<?= $userREGtype ?>" />
		<input type="hidden" name="exhibitorCode" id="exhibitorCode" value="<?= $paymentDetails['exhibitor_code'] ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="4" class="tcat">
					<span style="float:left">Payments</span>
					<span class="close" onclick="closePaymentPaymentPopUp()">X</span>
				</td>
			</tr>
			<tr>
				<td width="20%" align="left">Slip No</td>
				<td width="30%" align="left"><?= $slipDetails['slip_number'] ?></td>
				<td width="20%" align="left">Slip Date</td>
				<td width="30%" align="left"><?= $slipDetails['slip_date'] ?></td>
			</tr>
			<tr>
				<td align="left">Slip Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
				<td align="left">Pending Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
			</tr>
			<tr>
				<td align="left">Payment Mode</td>
				<td align="left"><?= $paymentDetails['payment_mode'] ?></td>
				<td align="left"></td>
				<td align="left"></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">

					<?
					if ($paymentDetails['payment_mode'] == "Cash") {
						?>
						<div id="cashPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Date of Deposit</td>
									<td width="30%" align="left"><?= $paymentDetails['cash_deposit_date'] ?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
							</table>
						</div>

						<?
					}
					if ($paymentDetails['payment_mode'] == "Cheque") {
						?>
						<div id="chequePaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Cheque No</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Cheque Date</td>
									<td align="left"><?= $paymentDetails['cheque_date'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Draft") {
						?>
						<div id="draftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Draft No</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Draft Date</td>
									<td align="left"><?= $paymentDetails['draft_date'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "NEFT") {
						?>
						<div id="neftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_bank_name'] ?></td>

									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['neft_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "RTGS") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_bank_name'] ?></td>
									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['rtgs_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Card") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Card Payment Date</td>
									<td width="30%" align="left"><?= $paymentDetails['card_payment_date'] ?></td>
									<td width="20%" align="left">Card Number</td>
									<td width="30%" align="left"><?= $paymentDetails['card_transaction_no'] ?></td>
								</tr>
								<tr>
									<td align="left"></td>
									<td align="left"><? //=$paymentDetails['rtgs_transaction_no']
											?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Credit") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Credit Payment Date</td>
									<td width="30%" align="left"><?= $paymentDetails['credit_date'] ?></td>
									<td width="20%" align="left">Exhibitor Name</td>
									<?php
									$sqlExhibitorName = array();
									$sqlExhibitorName['QUERY'] = "SELECT `exhibitor_company_name`
																	FROM " . _DB_EXIBITOR_COMPANY_ . "
																	WHERE `status` = 'A' AND `exhibitor_company_code` = '" . $paymentDetails['exhibitor_code'] . "' ";

									$Exhibitor = $mycms->sql_select($sqlExhibitorName, false);
									?>
									<td width="30%" align="left"><?= $Exhibitor[0]['exhibitor_company_name'] ?>
										<input type="hidden" name="exhibitorName" id="exhibitorName"
											value="<?= $Exhibitor[0]['exhibitor_company_name'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="left"></td>
									<td align="left"><? //=$paymentDetails['rtgs_transaction_no']
											?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					?>

				</td>
			</tr>
			<tr>
				<td align="left">Amount</td>
				<td align="left"><?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?></td>
				<td align="left">Payment Date</td>
				<td align="left">
					<input type="date" name="payment_date" id="payment_date" style="width:90%;"
						value="<?= date('Y-m-d') ?>" />
				</td>
			</tr>
			<tr>
				<td align="left"></td>
				<td align="left"></td>
				<td align="left">Remarks</td>
				<td align="left">
					<textarea name="remarks" id="remarks" style="width: 90%; height: 70%; resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" align="left">

					<input type="button" name="btnPayment" id="btnPayment" value="Payment Discard"
						onclick="window.location.href='registration.process.php?act=payment_discard&paymentId=<?= $paymentId ?>&delegateId=<?= $delegateId ?>&slip_id=<?= $slipDetails['id'] ?>'"
						class="btn btn-small btn-red" />
					&nbsp;
					<input type="submit" name="btnPayment" id="btnPayment" value="Make Payment"
						class="btn btn-small btn-blue" />

				</td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
		</table>
	</form>
	<?
}
function multiPaymentConfirmationSpot($mycms, $cfg, $slipId = "", $paymentId = "", $delegateId = "", $userREGtype = "SPOT" ,$paymentDate = "")
{
    if (empty($slipId) || empty($paymentId)) {
        return array('status' => 0, 'message' => 'Missing slip or payment ID.');
    }

    $paymentDetails = getPaymentDetails($paymentId);

    if (empty($paymentDetails)) {
        return array('status' => 0, 'message' => 'Payment record not found.');
    }

    $paymentModeDisplay = ($paymentDetails['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $paymentDetails['payment_mode']);
    $finalPaymentDate = !empty($paymentDate) ? $paymentDate : date('Y-m-d');

    // Prepare POST data for make_partial_payment()
    $_POST = [
        'act'           => 'make_partial_payment',
        'redirect'      => 'N',
        'delegateId'    => $delegateId,
        'slipId'        => $slipId,
        'paymentId'     => $paymentId,
        'userREGtype'   => $userREGtype,
        'exhibitorCode' => $paymentDetails['exhibitor_code'],
        'amount'        => $paymentDetails['amount'],
        'payment_date'  => $finalPaymentDate,
        'remarks'       => '',
    ];

    $result = make_partial_payment_spot($mycms, $cfg);

    return array('status' => 1, 'message' => 'Payment confirmed successfully.');
}
function multiPaymentConfirmation($slipId="", $paymentId="", $delegateId="", $userREGtype="")
{
	global $cfg, $mycms;

	// $delegateId = $_REQUEST['delegateId'];
	// $slipId = $_REQUEST['slipId'];
	// $paymentId = $paymentId;
	// $userREGtype = $_REQUEST['userREGtype'];
 
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
	<!-- <form action="<?= _BASE_URL_ ?>webmaster/section_registration/registration.process.php" name="frmPaymentPopup"
		id="frmPaymentPopup" method="post" onsubmit="return onSubmitAction();" style="display:none;">
		<input type="hidden" name="act" id="act" value="make_partial_payment" />
		<input type="hidden" name="redirect" id="redirect" value="N" />
		<input type="hidden" name="delegateId" id="delegateId" value="<?= $delegateId ?>" />
		<input type="hidden" name="slipId" id="slipId" value="<?= $slipId ?>" />
		<input type="hidden" name="paymentId" id="paymentId" value="<?= $paymentId ?>" />
		<input type="hidden" name="userREGtype" id="userREGtype" value="<?= $userREGtype ?>" />
		<input type="hidden" name="exhibitorCode" id="exhibitorCode" value="<?= $paymentDetails['exhibitor_code'] ?>" />
		<input type="hidden" name="amount" id="amount" value="<?= $paymentDetails['amount'] ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="4" class="tcat">
					<span style="float:left">Partial Payment</span>
					<span class="close" onclick="closePaymentPaymentPopUp()">X</span>
				</td>
			</tr>
			<tr>
				<td width="20%" align="left">Slip No</td>
				<td width="30%" align="left"><?= $slipDetails['slip_number'] ?></td>
				<td width="20%" align="left">Slip Date</td>
				<td width="30%" align="left"><?= $slipDetails['slip_date'] ?></td>
			</tr>
			<tr>
				<td align="left">Slip Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
				<td align="left">Pending Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
			</tr>
			<tr>
				<td align="left">Payment Mode</td>
				<td align="left"><?= $paymentModeDisplay ?></td>
				<td align="left"></td>
				<td align="left"></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">

					<?
					if ($paymentDetails['payment_mode'] == "Cash") {
						?>
						<div id="cashPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Date of Deposit</td>
									<td width="30%" align="left"><?= $paymentDetails['cash_deposit_date'] ?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
							</table>
						</div>

						<?
					}
					if ($paymentDetails['payment_mode'] == "Cheque") {
						?>
						<div id="chequePaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Cheque/DD No</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left"> Date</td>
									<td align="left"><?= $paymentDetails['cheque_date'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "UPI") {
						?>
						<div id="chequePaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Transaction NUmber</td>
									<td width="30%" align="left"><?= $paymentDetails['txn_no'] ?></td>
									<td width="20%" align="left">UPI Date</td>
									<td width="30%" align="left"><?= $paymentDetails['upi_date'] ?></td>
								</tr>
								<tr>
									<td align="left"></td>
									<td align="left"></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Draft") {
						?>
						<div id="draftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Draft No</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Draft Date</td>
									<td align="left"><?= $paymentDetails['draft_date'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "NEFT") {
						?>
						<div id="neftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_bank_name'] ?></td>

									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['neft_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "RTGS") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_bank_name'] ?></td>
									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['rtgs_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Card") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Card Payment Date</td>
									<td width="30%" align="left"><?= $paymentDetails['card_payment_date'] ?></td>
									<td width="20%" align="left">Card Number</td>
									<td width="30%" align="left"><?= $paymentDetails['card_transaction_no'] ?></td>
								</tr>
								<tr>
									<td align="left"></td>
									<td align="left"><? //=$paymentDetails['rtgs_transaction_no']
											?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Credit") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Credit Payment Date</td>
									<td width="30%" align="left"><?= $paymentDetails['credit_date'] ?></td>
									<td width="20%" align="left">Exhibitor Name</td>
									<?php
									$sqlExhibitorName = array();
									$sqlExhibitorName['QUERY'] = "SELECT `exhibitor_company_name`
																	FROM " . _DB_EXIBITOR_COMPANY_ . "
																	WHERE `status` = 'A' AND `exhibitor_company_code` = '" . $paymentDetails['exhibitor_code'] . "' ";

									$Exhibitor = $mycms->sql_select($sqlExhibitorName, false);
									?>
									<td width="30%" align="left"><?= $Exhibitor[0]['exhibitor_company_name'] ?>
										<input type="hidden" name="exhibitorName" id="exhibitorName"
											value="<?= $Exhibitor[0]['exhibitor_company_name'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="left"></td>
									<td align="left"><? //=$paymentDetails['rtgs_transaction_no']
											?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					?>

				</td>
			</tr>
			<tr>
				<td align="left">Payable Amount</td>
				<td align="left"><?= $paymentDetails['amount'] ?></td>
				<td align="left">Payment Date</td>
				<td align="left">
					<input type="date" name="payment_date" id="payment_date" style="width:90%;"
						value="<?= date('Y-m-d') ?>" />
				</td>
			</tr>
			<tr>
				<td align="left"></td>
				<td align="left"></td>
				<td align="left">Remarks</td>
				<td align="left">
					<textarea name="remarks" id="remarks" style="width: 90%; height: 70%; resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" align="left">

					<input type="button" name="btnPayment" id="btnPayment" value="Payment Discard"
						onclick="window.location.href='<?= _BASE_URL_ ?>webmaster/section_registration/registration.process.php?act=payment_discard&paymentId=<?= $paymentId ?>&delegateId=<?= $delegateId ?>&slip_id=<?= $slipDetails['id'] ?>'"
						class="btn btn-small btn-red" />
					&nbsp;
					<input type="submit" name="btnPayment" id="btnPayment" value="Make Payment"
						class="btn btn-small btn-blue" />
				</td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
		</table>
	</form> -->
	<?
}

function openSettlementPopupFrom()
{
	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];

	$slipDetails = slipDetails($slipId);
	$paymentDetails = paymentDetails($slipId)
		?>
	<form action="registration.process.php" name="frmPaymentPopup" id="frmPaymentPopup" method="post">
		<input type="hidden" name="act" id="act" value="make_payment" />
		<input type="hidden" name="delegateId" id="delegateId" value="<?= $delegateId ?>" />
		<input type="hidden" name="slipId" id="slipId" value="<?= $slipId ?>" />
		<input type="hidden" name="paymentId" id="paymentId" value="<?= $paymentId ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="4" class="tcat">
					<span style="float:left">Payments</span>
					<span class="close" onclick="closePaymentPaymentPopUp()">X</span>
				</td>
			</tr>
			<tr>
				<td width="20%" align="left">Slip No</td>
				<td width="30%" align="left"><?= $slipDetails['slip_number'] ?></td>
				<td width="20%" align="left">Slip Date</td>
				<td width="30%" align="left"><?= $slipDetails['slip_date'] ?></td>
			</tr>
			<tr>
				<td align="left">Slip Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
				<td align="left">Pending Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
			</tr>
			<tr>
				<td align="left">Payment Mode</td>
				<td align="left"><?= $paymentDetails['payment_mode'] ?></td>
				<td align="left"></td>
				<td align="left"></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">

					<?
					if ($paymentDetails['payment_mode'] == "Cash") {
						?>
						<div id="cashPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Date of Deposit</td>
									<td width="30%" align="left"><?= $paymentDetails['cash_deposit_date'] ?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
							</table>
						</div>

						<?
					}
					if ($paymentDetails['payment_mode'] == "Cheque") {
						?>
						<div id="chequePaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Cheque No</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Cheque Date</td>
									<td align="left"><?= $paymentDetails['cheque_date'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Draft") {
						?>
						<div id="draftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Draft No</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Draft Date</td>
									<td align="left"><?= $paymentDetails['draft_date	'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "NEFT") {
						?>
						<div id="neftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_bank_name'] ?></td>
									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['neft_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "RTGS") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_bank_name'] ?></td>
									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['rtgs_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					?>
				</td>
			</tr>
			<tr>
				<td align="left">Amount</td>
				<td align="left"><?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?></td>
				<td align="left">Payment Date</td>
				<td align="left">
					<input type="text" name="payment_date" id="payment_date" style="width:90%;" readonly="readonly"
						rel="tcal" value="<?= date('Y-m-d') ?>" />
				</td>
			</tr>
			<tr>
				<td align="left"></td>
				<td align="left"></td>
				<td align="left">Remarks</td>
				<td align="left">
					<textarea name="remarks" id="remarks" style="width: 90%; height: 70%; resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" align="left">

					<input type="button" name="btnPayment" id="btnPayment" value="Payment Discard"
						onclick="window.location.href='registration.process.php?act=payment_discard&paymentId=<?= $paymentId ?>&delegateId=<?= $delegateId ?>&slip_id=<?= $slipDetails['id'] ?>'"
						class="btn btn-small btn-red" />
					&nbsp;
					<input type="submit" name="btnPayment" id="btnPayment" value="Make Payment"
						class="btn btn-small btn-blue" />

				</td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
		</table>
	</form>
	<?
}

function paymentConfirmationProcess()
{
	global $cfg, $mycms;

	$paymentId = $_REQUEST['paymentId'];
	$slipId = $_REQUEST['slipId'];
	$paymentRemark = $_REQUEST['remarks'];
	$paymentDate = $_REQUEST['payment_date'];
	$delegateId = $_REQUEST['delegateId'];
	$exhibitorCode = $_REQUEST['exhibitorCode'];
	$exhibitorName = $_REQUEST['exhibitorName'];

	$paidAmmount = invoiceAmountOfSlip($slipId);

	$sqlDelegateInfo = array();
	$sqlDelegateInfo['QUERY'] = "SELECT `user_type`,`user_full_name` 
										 FROM " . _DB_USER_REGISTRATION_ . " 
										WHERE `id` = ? ";
	$sqlDelegateInfo['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$delegateInfo = $mycms->sql_select($sqlDelegateInfo, false);

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
											SET `payment_date` = ?,
												`payment_remark` = ?,
												`amount` = ?,
												`payment_status` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'payment_date', 'DATA' => $paymentDate, 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'payment_remark', 'DATA' => $paymentRemark, 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'amount', 'DATA' => $paidAmmount, 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdatePayment, false);

	$sqlUpdateSlip = array();
	$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
											SET `payment_status` = ?
										  WHERE `id` = ?";
	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $slipId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdateSlip, false);

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
	$isOnlyWorkshop = "NO";
	$isOnlyAccompany = 'NO';
	$isOnlyAccommodation = 'NO';
	$isConference = "NO";
	$isOnlyDinner = 'NO';
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
		}

		if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
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
			if ($isOnlyAccompany == 'NO') {
				$isOnlyDinner = 'YES';
			}
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


	$userRegDetails = getUserDetails($delegateId);

	//offline_conference_payment_confirmation_message($delegateId,$slipId , $paymentId, 'SEND');

	if ($isConference == 'YES') {
		offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
	} elseif ($isOnlyAccommodation == 'YES') {
		offline_accommodation_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
	} elseif ($isOnlyAccompany == 'YES') {
		offline_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, 'SEND');
	} elseif ($isOnlyDinner == 'YES') {
		offline_dinner_confirmation_message($delegateId, $paymentId, $slipId, 'SEND');
	} elseif ($isOnlyWorkshop == 'YES') {
		offline_conference_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, 'SEND');
	}
}

function partialPaymentConfirmationProcess($sendmail = true)
{
	global $cfg, $mycms;

	//  echo '<pre>'; print_r($_POST); die;	

	$paymentId = $_POST['paymentId'];
	$slipId = $_POST['slipId'];
	$paymentRemark = $_POST['remarks'];
	$paymentDate = $_POST['payment_date'];
	$delegateId = $_POST['delegateId'];
	$exhibitorCode = $_POST['exhibitorCode'];
	$exhibitorName = $_POST['exhibitorName'];
	if ($_POST['amount'] != '') {
		$amount = $_POST['amount'];
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

	$confStartDate = date("Y-m-d", strtotime($cfg['CONF_START_DATE']));
	$confEndDate = date("Y-m-d", strtotime($cfg['CONF_END_DATE']));

	if ($confStartDate <= $paymentDate && $confEndDate >= $paymentDate) {
		$sqlUpdateSlip = array();
		$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
												SET `invoice_request` = ?
											  WHERE `id` = ?";
		$sqlUpdateSlip['PARAM'][] = array('FILD' => 'invoice_request', 'DATA' => 'SPOT', 'TYP' => 's');
		$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $slipId, 'TYP' => 's');
		$mycms->sql_update($sqlUpdateSlip, false);
		
		$sqlUpdateSlip = array();
		$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
												SET `payment_type` = ?
											  WHERE `id` = ?";
		$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_type', 'DATA' => 'SPOT', 'TYP' => 's');
		$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
		$mycms->sql_update($sqlUpdateSlip, false);
	}

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
			if ($confStartDate <= $paymentDate && $confEndDate >= $paymentDate) {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
												SET `invoice_request` = ?
											WHERE `id` = ?
												";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'invoice_request', 'DATA' => 'SPOT', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $valActiveInvoice['id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

			}

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

				online_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, 'SEND');
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

function additionalAccomodationRequestProcess($registrationType)
{
	global $cfg, $mycms;
   //    echo '<pre>'; print_r($_REQUEST);die;
	$loggedUserID = $mycms->getLoggedUserId();
	$delegateId = $_REQUEST['delegate_id'];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	$rowUserDetails = getUserDetails($delegateId);
	// $currentCutoffId = getTariffCutoffId();
	if($_REQUEST['registration_accompany_cutoff']!=""){
		$currentCutoffId = $_REQUEST['registration_accompany_cutoff'];
	}else{
		$currentCutoffId =getUserCutoffId($delegateId);
	}

	if(!empty($_REQUEST['edit_package_id']))
	{
        foreach($_REQUEST['edit_package_id'] as $key =>$package_id)
		{
						
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
			$explodeCheckin = explode('/',$_REQUEST['edit_hotel_dates'][$accommodation_hotel_id]['checkin']);
			$explodeCheckout = explode('/',$_REQUEST['edit_hotel_dates'][$accommodation_hotel_id]['checkout']);
 
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
			$accomodationDetails['booking_quantity']								 = $_REQUEST['edit_accmomdation_qty'][$accommodation_hotel_id][$resPackageCheckoutDate1[0]['roomTypeId']];
			$accomodationDetails['booking_quantity_insert']						     = $_REQUEST['edit_accmomdation_qty'][$accommodation_hotel_id][$resPackageCheckoutDate1[0]['roomTypeId']];
			}
			$accomodationDetails['refference_invoice_id']							 = 0;
			$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
			$accomodationDetails['booking_mode']									 = 'OFFLINE';
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
            // if($_REQUEST['invoice_generation_mode_edit'] == "COMPLIMENTARY")
			// {
			// 	zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMMODATION', $slip_id);
			// }
			// else if($_REQUEST['invoice_generation_mode_edit'] == "ZERO_VALUE")
			// {
			// 	zeroValueInvoiceUpdate($invoiceIdConf,'ACCOMMODATION', $slip_id);
			// }
			
		

			$sqlProcessUpdateRoom['QUERY']  = " UPDATE  "._DB_USER_REGISTRATION_."
													SET `accommodation_room` = '".$totalRoomVal."'
													WHERE `id` = '".$delegateId."' AND status='A'";													 
			$mycms->sql_update($sqlProcessUpdateRoom, false);
			
		
		}
			
	}
	// $sqlFetchHotel = array();
	// $sqlFetchHotel['QUERY'] = "SELECT id 
	// 									 FROM " . _DB_PACKAGE_ACCOMMODATION_ . "  
	// 									WHERE `id` = '" . $accommodation_pack . "'
	// 									  AND `status` = 'A'";

	// $resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
	// $resultfetch = $resultFetchHotel[0];
	// $packageId = $resultfetch['id'];


	// $sqlAccommodationDate = array();
	// $sqlAccommodationDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
	// 												   WHERE `id` = '" . $check_in_date_id . "'";

	// $resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate);
	// $rowAccommodationDate = $resultAccommodationDate[0];

	// $check_in_date = $rowAccommodationDate['check_in_date'];

	// // GET ACCOMMODATION OUT DATE
	// $sqlAccommodationOutDate = array();
	// $sqlAccommodationOutDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
	// 											   WHERE `id` = '" . $check_out_date_id . "'";

	// $resultAccommodationOutDate = $mycms->sql_select($sqlAccommodationOutDate);
	// $rowAccommodationOutDate = $resultAccommodationOutDate[0];

	// $check_out_date = $rowAccommodationOutDate['check_out_date'];


	// $slipId = insertingSlipDetails($delegateId, 'OFFLINE');
	// $slipIdd = $mycms->getSession('SLIP_ID');

	// if ($spotUser != '') {
	// 	if ($_REQUEST['registration_type_add'] == "GENERAL") {
	// 		$payment_status = "PAID";
	// 	} else if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
	// 		$payment_status = "ZERO_VALUE";
	// 	}

	// 	$sqlUpdateSlipRequest['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
	// 									  SET  `invoice_request` = '" . $spotUser . "',													  
	// 											  `invoice_type` = '" . $spotUser . "',
	// 											  `payment_status` = '" . $payment_status . "'
	// 											  WHERE `status` = 'A'
	// 													AND `id` = '" . $mycms->getSession('SLIP_ID') . "' ";

	// 	$mycms->sql_update($sqlUpdateSlipRequest, false);
	// }

	// if ($_REQUEST['registration_type_add'] == "ZERO_VALUE" && $spotUser == '') {
	// 	$sqlUpdateSlipRequest['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
	// 									  SET `payment_status` = 'ZERO_VALUE'
	// 											WHERE `status` = 'A'
	// 												  AND `id` = '" . $mycms->getSession('SLIP_ID') . "' ";

	// 	$mycms->sql_update($sqlUpdateSlipRequest, false);
	// }

	// $accTariffDet = getAccommodationTariff($accommodation_hotel_id, $accommodation_pack, $check_in_date_id, $check_out_date_id, $chosenCutOffId);
	// $accTariffId = $accTariffDet['id'];

	// $accomodationDetails['user_id'] = $delegateId;
	// $accomodationDetails['accompany_name'] = strtoupper(addslashes(trim($_REQUEST['accmName'])));
	// $accomodationDetails['hotel_id'] = $accommodation_hotel_id;
	// $accomodationDetails['roomType_id'] = $accommodation_hotel_type_id;
	// $accomodationDetails['package_id'] = $accommodation_pack;
	// $accomodationDetails['tariff_ref_id'] = $accTariffId;
	// $accomodationDetails['guest_counter'] = '1';
	// $accomodationDetails['tariff_cutoff_id'] = $chosenCutOffId;
	// $accomodationDetails['checkin_date'] = $check_in_date;
	// $accomodationDetails['checkout_date'] = $check_out_date;
	// $accomodationDetails['booking_quantity'] = $_REQUEST['booking_quantity'];
	// $accomodationDetails['refference_invoice_id'] = 0;
	// $accomodationDetails['refference_slip_id'] = $mycms->getSession('SLIP_ID');
	// $accomodationDetails['booking_mode'] = 'OFFLINE';
	// $accomodationDetails['payment_status'] = 'UNPAID';

	// $accompReqId = insertingAccomodationDetails($accomodationDetails);
	// if ($spotUser != '') {
	// 	$sqlAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  
	// 											SET `registration_payment_status` = '" . $payment_status . "'
	// 											WHERE `id` = '" . $reqId . "' 
	// 												AND `status` = 'A' ";

	// 	$mycms->sql_update($sqlAccompany, false);
	// }
	// if ($_REQUEST['registration_type_add'] == "ZERO_VALUE" && $spotUser == '') {
	// 	$sqlUpdateAccommodationRequest['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . " 
	// 										    SET `payment_status` = 'ZERO_VALUE'
	// 											   WHERE `id` = '" . $accompReqId . "' 
	// 												AND `status` = 'A' ";

	// 	$mycms->sql_update($sqlUpdateAccommodationRequest, false);
	// }
	// $invoiceId = insertingInvoiceDetails($accompReqId, 'ACCOMMODATION');
	// if ($spotUser != '') {
	// 	$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
	// 								  SET `invoice_request`  = '" . $spotUser . "',
	// 										`payment_status` = '" . $payment_status . "'													  
	// 								WHERE `status` = 'A'
	// 									  AND `id` = '" . $invoiceId . "' ";

	// 	$mycms->sql_update($sqlUpdateInvoiceRequest, false);

	// 	$sqlUpdateWorkshopRequest['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . " 
	// 														  SET `payment_status` = 'PAID'
	// 														  WHERE `id` = '" . $accompReqId . "' 
	// 														  AND `status` = 'A' ";
	// 	$mycms->sql_update($sqlUpdateWorkshopRequest, false);
	// }
	// if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
	// 	zeroValueInvoiceUpdate($invoiceId, 'ACCOMMODATION');
	// 	gstInsertionInInvoice($invoiceId);
	// }
	// if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") // mail for complimentry
	// {
	// 	complementary_accommodation_confirmation_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");
	// 	//$mycms->redirect("complementary.online.payment.success.php");
	// }
	// $paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));

	// if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") // mail for complimentry
	// {

	// 	$sqlUpdate = array();
	// 	$sqlUpdate['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
	// 									SET `collected_by` = '" . $loggedUserID . "',
	// 									    `payment_mode` = ' '
	// 								  WHERE `id` = '" . $paymentId . "'";

	// 	$mycms->sql_update($sqlUpdate, false);
	// }
	// if ($spotUser != '') {
	// 	$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));

	// 	$payment_date = date('Y-m-d');

	// 	$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
	// 										  SET `payment_status` = 'PAID',
	// 										  `collected_by` = '" . $loggedUserID . "',
	// 										  `payment_type` = 'SPOT',
	// 										  `payment_date` = '" . $payment_date . "',
	// 										  `amount` = '" . $paymentDetailsAmount . "'
	// 											  WHERE `id` = '" . $paymentId . "' 
	// 											  AND `status` = 'A' ";

	// 	$mycms->sql_update($sqlUpdatePaymentRequest, false);
	// 	offline_accommodation_confirmation_message($delegateId, $paymentId, $mycms->getSession('SLIP_ID'), 'SEND');
	// }
	// $array = array();
	// $array['delegateId'] = $delegateId;
	// $array['paymentId'] = $paymentId;
	// $array['slipId'] = $mycms->getSession('SLIP_ID');
	// $array['reg_type'] = $_REQUEST['registration_type_add'];

	// return $array;
}

function Trash($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$loggedUserID = $mycms->getLoggedUserId();
	$delegateId = $_REQUEST['id'];
	$userType = $_REQUEST['userType'];

	// REMOVING USER
	$sqlRemove = array();
	$sqlRemove['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
										 SET `status` = ? 
									   WHERE `id`     = ?";
	$sqlRemove['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemove['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemove);

	$sqlRemoveInvoice = array();
	$sqlRemoveInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
											  SET `status_before_trash`  = `status` 
											WHERE `delegate_id`= ?";
	$sqlRemoveInvoice['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveInvoice);

	$sqlRemoveInvoice = array();
	$sqlRemoveInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
											  SET `status` = ? 
											WHERE `delegate_id` = ?";
	$sqlRemoveInvoice['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemoveInvoice['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveInvoice);

	$sqlRemoveSlip = array();
	$sqlRemoveSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
										   SET `status_before_trash`  = `status` 
										 WHERE `delegate_id` = ?";
	$sqlRemoveSlip['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveSlip);

	$sqlRemoveSlip = array();
	$sqlRemoveSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
										   SET `status` = ? 
										 WHERE `delegate_id` = ?";
	$sqlRemoveSlip['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemoveSlip['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveSlip);
    //    Remove Accomodation
	$sqlRemoveAcco = array();
	$sqlRemoveAcco['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . " 
										   SET `status_before_trash`  = `status` 
										 WHERE `user_id` = ?";
	$sqlRemoveAcco['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveAcco);

	$sqlRemoveAcco = array();
	$sqlRemoveAcco['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . " 
										   SET `status` = ? 
										 WHERE `user_id` = ?";
	$sqlRemoveAcco['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemoveAcco['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveAcco);
    // 
	  //    Remove Workshop
	$sqlRemoveWorkshop = array();
	$sqlRemoveWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . " 
										   SET `status_before_trash`  = `status` 
										 WHERE `delegate_id` = ?";
	$sqlRemoveWorkshop['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveWorkshop);

	$sqlRemoveWorkshop = array();
	$sqlRemoveWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . " 
										   SET `status` = ? 
										 WHERE `delegate_id` = ?";
	$sqlRemoveWorkshop['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlRemoveWorkshop['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemoveWorkshop);
    // 
	if ($userType == 'EXHIBITOR') {
		// pageRedirection("exhibitor_listing.php", "1");
		$mycms->redirect(_BASE_URL_ . 'webmaster/section_exhibitor/exhibitor_listing.php');
	} else if ($userType == 'GUEST') {
		$mycms->redirect(_BASE_URL_ . 'webmaster/section_spot/spot_create_delegate.php?show=guestUsers');
	} else if ($userType == 'VOLUNTEER') {
		$mycms->redirect(_BASE_URL_ . 'webmaster/section_spot/spot_create_delegate.php?show=volunteerUsers');
	} else if ($userType == 'WORKSHOP') {
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="only-workshop-registration.php";</script>';	exit();
	} else {
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="registration.php";</script>';	exit();
		
	}
}

function payment_discard($mycms, $cfg, $slipId="", $paymentId="")
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $paymentId;
	$slip_id = $slipId;

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
											SET `status` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdatePayment, false);
	
	discardDiscount($slip_id);
	pageRedirection("registration.php", "Payment discarded Successfully");
}
function payment_discard_spot($mycms, $cfg, $slipId="", $paymentId="")
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $paymentId;
	$slip_id = $slipId;

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
											SET `status` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdatePayment, false);

	discardDiscount($slip_id);
    echo json_encode(array('status' => 1, 'message' => 'Payment discarded successfully'));
}

function makeComplemantary($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $_REQUEST['paymentId'];
	$slip_id = $_REQUEST['slipId'];
	$delegate_id = $_REQUEST['delegateId'];

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
											SET `status` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdatePayment, false);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_SLIP_ . "
												SET `payment_status` = ?
											  WHERE `delegate_id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegate_id, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	$invoices = invoiceDetailsOfSlip($slip_id);
	// print_r($invoices);
	// die;
	foreach ($invoices as $k => $invoice) {
		$sqlUpdateInvoice = array();
		if ($invoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			$payment_status = 'COMPLIMENTARY';
		} else {
			$payment_status = 'ZERO_VALUE';
		}


		$sqlUpdateInvoice['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
										 SET  `service_total_price` 	     = ?,
										 	  `service_product_price`        = ?,
										 	  `service_unit_price`           = ?,
										 	  `discount_amount`              = ?,
										 	  `total_amount_after_discount`  = ?,
											  `cgst_int_percentage` 	     = ?, 
											  `sgst_int_percentage`	         = ?,
											  `cgst_int_price` 	  	         = ?, 
											  `sgst_int_price`	 	         = ?,										 							  
											  `service_gst_int_price`	     = ?,
											  `service_basic_price`          = ?,
											  `cgst_price` 	  	 		 	 = ?, 
											  `sgst_price`	 	 	     	 = ?,										 
											  `service_gst_total_price`	 	 = ?,
											  `service_grand_price`  	     = ?,
											  `service_roundoff_price`  	 = ?,
											  `has_gst`  				 	 = ?,
											  `payment_status`  			 = ?
											WHERE `id` 						 = ?";

		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_total_price', 'DATA' => (float) 0, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_product_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_unit_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'discount_amount', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'total_amount_after_discount', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'cgst_int_percentage', 'DATA' => (float) $cfg['INT.CGST'], 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'sgst_int_percentage', 'DATA' => (float) $cfg['INT.SGST'], 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'cgst_int_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'sgst_int_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_gst_int_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_basic_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'cgst_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'sgst_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_gst_total_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_grand_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_roundoff_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'has_gst', 'DATA' => 'Y', 'TYP' => 's');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => $payment_status, 'TYP' => 's');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'id', 'DATA' => (int) $invoice['id'], 'TYP' => 'i');


		//print_r($sqlUpdateInvoice);


		// echo '<pre>';
		// print_r($sqlUpdateInvoice['PARAM']);
		// exit;

		$mycms->sql_update($sqlUpdateInvoice, false);
	}

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `registration_payment_status` = ?,
												`workshop_payment_status` = ?,
												 `accommodation_payment_status` = ?
											  WHERE `id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'workshop_payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'accommodation_payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegate_id, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `registration_payment_status` = ?
											  WHERE `refference_delegate_id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'refference_delegate_id', 'DATA' => $delegate_id, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	echo '<script>window.location.href="registration.php";</script>';
		exit();
}


function makeComplemantaryAjax($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $_REQUEST['paymentId'];
	$slip_id = $_REQUEST['slipId'];
	$delegate_id = $_REQUEST['delegateId'];

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
											SET `status` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
	$sqlUpdatePayment['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdatePayment, false);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_SLIP_ . "
												SET `payment_status` = ?
											  WHERE `delegate_id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegate_id, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	$invoices = invoiceDetailsOfSlip($slip_id);
	// print_r($invoices);
	// die;
	foreach ($invoices as $k => $invoice) {
		$sqlUpdateInvoice = array();
		if ($invoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			$payment_status = 'COMPLIMENTARY';
		} else {
			$payment_status = 'COMPLIMENTARY';
		}


		$sqlUpdateInvoice['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
										 SET  `service_total_price` 	     = ?,
										 	  `service_product_price`        = ?,
										 	  `service_unit_price`           = ?,
										 	  `discount_amount`              = ?,
										 	  `total_amount_after_discount`  = ?,
											  `cgst_int_percentage` 	     = ?, 
											  `sgst_int_percentage`	         = ?,
											  `cgst_int_price` 	  	         = ?, 
											  `sgst_int_price`	 	         = ?,										 							  
											  `service_gst_int_price`	     = ?,
											  `service_basic_price`          = ?,
											  `cgst_price` 	  	 		 	 = ?, 
											  `sgst_price`	 	 	     	 = ?,										 
											  `service_gst_total_price`	 	 = ?,
											  `service_grand_price`  	     = ?,
											  `service_roundoff_price`  	 = ?,
											  `has_gst`  				 	 = ?,
											  `payment_status`  			 = ?
											WHERE `id` 						 = ?";

		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_total_price', 'DATA' => (float) 0, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_product_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_unit_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'discount_amount', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'total_amount_after_discount', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'cgst_int_percentage', 'DATA' => (float) $cfg['INT.CGST'], 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'sgst_int_percentage', 'DATA' => (float) $cfg['INT.SGST'], 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'cgst_int_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'sgst_int_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_gst_int_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_basic_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'cgst_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'sgst_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_gst_total_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_grand_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_roundoff_price', 'DATA' => 0.00, 'TYP' => 'd');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'has_gst', 'DATA' => 'Y', 'TYP' => 's');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => $payment_status, 'TYP' => 's');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'id', 'DATA' => (int) $invoice['id'], 'TYP' => 'i');


		//print_r($sqlUpdateInvoice);


		// echo '<pre>';
		// print_r($sqlUpdateInvoice['PARAM']);
		// exit;

		$mycms->sql_update($sqlUpdateInvoice, false);
	}

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `registration_payment_status` = ?,
												`workshop_payment_status` = ?,
												 `accommodation_payment_status` = ?
											  WHERE `id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'workshop_payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'accommodation_payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegate_id, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `registration_payment_status` = ?
											  WHERE `refference_delegate_id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'refference_delegate_id', 'DATA' => $delegate_id, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	    echo json_encode(array('status' => 1, 'message' => 'Payment changed to complementry successfully'));

}


function counterSetPaymentTerms($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$payment_mode = $_REQUEST['payment_mode'];
	//$exhibitorCode 	 	  = $_REQUEST['exhibitor_name'];
	$paymentId = counterInsertingPaymentDetails();
	if ($payment_mode == 'Cheque' || $payment_mode == 'Draft') {
		offline_registration_acknowledgement_message($_REQUEST['delegate_id'], $_REQUEST['slip_id'], $paymentId, $operation = 'SEND', 'BACK');
	} else {
		counterPaymentConfirmationProcess($paymentId);
	}
	$mycms->removeSession('SLIP_ID');
	pageRedirection("registration_new.php", '', "");
}

function additionalSetPaymentTerms($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = insertingPaymentDetails(false);
	$loggedUserId = $mycms->getLoggedUserId();
	$spotUser = $_REQUEST['userREGtype'];
	$mycms->removeSession('SLIP_ID');
	if ($_REQUEST['fullPayment'] == 'Y') {
		additionalPaymentConfirmationProcess($paymentId);

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
										SET `collected_by` = ?
									  WHERE `id` = ?";

		$sqlUpdate['PARAM'][] = array('FILD' => 'collected_by', 'DATA' => $loggedUserId, 'TYP' => 's');
		$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		//pageRedirection("registration_new.php",'',"");
	} else {
		//pageRedirection("unpaid.invoice.make.payment.php",'',"");
	}
	if ($spotUser != '') {
		pageRedirection("registration_new.php?show=spotInvoice&mailFor=SPOT&paymentId=" . $paymentId . "&id=" . $_REQUEST['delegate_id'] . "", '', "");
	} else {
		pageRedirection("registration_new.php?show=invoice&id=" . $_REQUEST['delegate_id'] . "", '', "");
	}
}

function addCallDetails($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$delegateId = addslashes(trim($_REQUEST['delegateId']));
	$participantId = addslashes(trim($_REQUEST['participantId']));
	$callDate = addslashes(trim($_REQUEST['callDate']));
	$callTimeHour = addslashes(trim($_REQUEST['callTimeHour']));
	$callTimeMin = addslashes(trim($_REQUEST['callTimeMin']));
	$call_subject = addslashes(trim($_REQUEST['call_subject']));
	$call_contents = addslashes(trim($_REQUEST['call_contents']));

	if (trim($callTimeHour) == '' || !is_numeric($callTimeHour) || $callTimeHour > 23 || $callTimeHour < 0) {
		$callTimeHour = 0;
	}
	if (trim($callTimeMin) == '' || !is_numeric($callTimeMin) || $callTimeMin > 59 || $callTimeMin < 0) {
		$callTimeMin = 0;
	}

	$call_time = number_pad(intval(trim($callTimeHour)), 2) . ':' . number_pad(intval(trim($callTimeMin)), 2) . ':' . '00';

	$call_datetime = $callDate . ' ' . number_pad(intval(trim($callTimeHour)), 2) . ':' . number_pad(intval(trim($callTimeMin)), 2) . ':' . '00';

	$sqlupdatetravelpickup = array();
	$sqlupdatetravelpickup['QUERY'] = "  INSERT INTO " . _DB_USER_CALLDETAILS_ . " 
														SET `delegate_id` = ?,
															`participant_id` = ?,
															`logged_user_id` = ?,
															`call_subject` = ?,
															`call_datetime` = ?,
															`call_date` = ?,
															`call_time` = ?,
															`call_contents` = ?";

	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'participant_id', 'DATA' => $participantId, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'logged_user_id', 'DATA' => $loggedUserID, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'call_subject', 'DATA' => $call_subject, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'call_datetime', 'DATA' => $call_datetime, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'callDate', 'DATA' => $callDate, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'call_time', 'DATA' => $call_time, 'TYP' => 's');
	$sqlupdatetravelpickup['PARAM'][] = array('FILD' => 'call_contents', 'DATA' => $call_contents, 'TYP' => 's');
	$mycms->sql_insert($sqlupdatetravelpickup);

	if ($delegateId != "") {
		$addlPram = "delegateId=" . $delegateId;
	} elseif ($participantId != "") {
		$addlPram = "participantId=" . $participantId;
	}

	pageRedirection(_BASE_URL_ . "webmaster/section_registration/registration_new.php", 1, "&" . $addlPram . "&" . $searchString);
}

function applyDinner($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	// DETAILS INSERTING PROCESS
	// include_once('../../includes/function.delegate.php');
	// include_once('../../includes/function.registration.php');
	// include_once('../../includes/function.dinner.php');
	$dinnerDetails = $_REQUEST['dinner_value'];
	$delegateId = $_REQUEST['delegate_id'];
	$clsfId = getUserClassificationId($delegateId);
	$cutoffId = $_REQUEST['cutoff_id_add'];
	$spotUser = $_REQUEST['userREGtype'];		// SPOT

	//echo '<pre>'; print_r($dinnerDetails);
	//die();

	if ($dinnerDetails) {
		if ($_REQUEST['registration_type_add'] == "GENERAL" || $_REQUEST['registration_type_add'] == "ZERO_VALUE") {
			$slip_id = insertingSlipDetails($delegateId, 'OFFLINE', 'GENERAL');
		}
		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
											  SET `payment_status` = ?
											WHERE `id` = ? ";
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('SLIP_ID'), 'TYP' => 's');
			$mycms->sql_update($sqlUpdateSlip, false);
		}

		//// qury for dinner callid
		$dinnerTariffArray = getAllDinnerTarrifDetails($cutoffId);

		foreach ($dinnerTariffArray as $key => $dinnerTarrif) {
			$dinner_classification_id = $dinnerTarrif[$cutoffId]['ID'];
		}

		foreach ($dinnerDetails as $key => $dinnerValue) {
			$dinnerDetailsArray1[$key]['refference_id'] = $dinnerValue;
			$dinnerDetailsArray1[$key]['delegate_id'] = $delegateId;
			$dinnerDetailsArray1[$key]['package_id'] = $dinner_classification_id;
			$dinnerDetailsArray1[$key]['tariff_cutoff_id'] = $cutoffId;
			$dinnerDetailsArray1[$key]['booking_quantity'] = 1;
			$dinnerDetailsArray1[$key]['booking_mode'] = 'OFFLINE';
			$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
			$dinnerDetailsArray1[$key]['refference_slip_id'] = (!$mycms->isSession('SLIP_ID')) ? 0 : $mycms->getSession('SLIP_ID');
			$dinnerDetailsArray1[$key]['payment_status'] = $_REQUEST['registration_type_add'] == "ZERO_VALUE" ? 'ZERO_VALUE' : 'UNPAID';

			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `isDinner` = ?
											  WHERE `id` = ?";
			$sqlUpdate['PARAM'][] = array('FILD' => 'isDinner', 'DATA' => 'Y', 'TYP' => 's');
			$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $dinnerValue, 'TYP' => 's');
			$mycms->sql_update($sqlUpdate, false);
		}
		//echo "<pre>"; print_r($_REQUEST); echo "</pre>";die();

		$dinerReqId = insertingDinnerDetails($dinnerDetailsArray1);

		foreach ($dinerReqId as $key => $reqId) {
			$invoiceId = insertingInvoiceDetails($reqId, 'DINNER');

			if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
				zeroValueInvoiceUpdate($invoiceId, 'DINNER');
				gstInsertionInInvoice($invoiceId);
			} elseif ($_REQUEST['registration_type_add'] == "GENERAL") {
				if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
					updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
				}
				//$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
			}
		}

		if ($spotUser != '') {
			//pageRedirection(_BASE_URL_."/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=".$delegateId."&paymentId=".$paymentId);
		} elseif ($_REQUEST['registration_type_add'] == "ZERO_VALUE") // mail for complimentry
		{
			complementary_dinner_confirmation_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");
			//$mycms->redirect("complementary.online.payment.success.php");
		} elseif ($_REQUEST['registration_type_add'] == "GENERAL") {
			$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
			offline_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $mycms->getSession('SLIP_ID'), 'SEND');
		}
	}

	pageRedirection("registration_new.php", '1', "&show=invoice&id=" . $delegateId);
}

function addAccompny($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	// include_once('../../includes/function.delegate.php');
	// include_once('../../includes/function.registration.php');
	// include_once('../../includes/function.accompany.php');
	$loggedUserID = $mycms->getLoggedUserId();
	$accompanyDetails = $_REQUEST;
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	$delegateId = $_REQUEST['delegate_id'];
	$discountAmount = $_REQUEST['discountAmount'];

	//echo 1212; die;


	if ($accompanyDetails) {
		$registrationMode = ($accompanyDetails['registrationMode'] == "OFFLINE") ? "OFFLINE" : "ONLINE";
		$accompanyCutoff = $accompanyDetails['registration_acc_cutoff'];
		foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {
			$accompanyDetailsArray[$val]['refference_delegate_id'] = $delegateId;
			$accompanyDetailsArray[$val]['user_full_name'] = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
			$accompanyDetailsArray[$val]['user_age'] = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
			$accompanyDetailsArray[$val]['user_food_preference'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
			$accompanyDetailsArray[$val]['user_food_details'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
			$accompanyDetailsArray[$val]['accompany_relationship'] = addslashes(trim(strtoupper('ACCOMPANY')));

			$accompanyDetailsArray[$val]['isRegistration'] = 'Y';
			$accompanyDetailsArray[$val]['isConference'] = 'Y';
			$accompanyDetailsArray[$val]['registration_classification_id'] = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
			$accompanyDetailsArray[$val]['registration_tariff_cutoff_id'] = $accompanyCutoff;
			$accompanyDetailsArray[$val]['accompany_tariff_cutoff_id'] = $accompanyCutoff;
			$accompanyDetailsArray[$val]['registration_request'] = ($_REQUEST['userREGtype'] == "SPOT") ? 'SPOT' : 'GENERAL';
			$accompanyDetailsArray[$val]['operational_area'] = ($_REQUEST['userREGtype'] == "SPOT") ? 'SPOT' : 'GENERAL';
			$accompanyDetailsArray[$val]['registration_payment_status'] = ($_REQUEST['registration_type_add'] == "ZERO_VALUE") ? 'ZERO_VALUE' : 'UNPAID';
			$accompanyDetailsArray[$val]['registration_mode'] = "OFFLINE";
			$accompanyDetailsArray[$val]['account_status'] = 'REGISTERED';
			$accompanyDetailsArray[$val]['reg_type'] = addslashes(trim(strtoupper('BACK')));
		}

		$slipId = insertingSlipDetails($delegateId, 'OFFLINE', "");

		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
											  SET `payment_status` = ?
											WHERE `id` = ? ";
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('SLIP_ID'), 'TYP' => 's');
			$mycms->sql_update($sqlUpdateSlip, false);

			// $paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
			// $sqlUpdatePaymentRequest['QUERY']  = "UPDATE " . _DB_PAYMENT_ . " 
			// 									  SET `status` = 'D'
			// 											  WHERE `id` = '" . $paymentId . "' 
			// 											  AND `status` = 'A' ";

			// $mycms->sql_update($sqlUpdatePaymentRequest, false);

			$payment_status = "ZERO_VALUE";
		} else {
			$payment_status = "UNPAID";
		}



		/*if($spotUser != '')
			{
				 $accompanyReqId = updatingAccompanyDetails($accompanyDetailsArray);
			}
			else
			{*/

		$accompanyReqId = insertingAccompanyDetails($accompanyDetailsArray, '', $accompanyCutoff);
		//}
		foreach ($accompanyReqId as $key => $reqId) {

			$sqlAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  
											SET `registration_payment_status` = '" . $payment_status . "'
											WHERE `id` = '" . $reqId . "' 
												AND `status` = 'A' ";

			$mycms->sql_update($sqlAccompany, false);

			$invoiceId = insertingInvoiceDetails($reqId, 'ACCOMPANY');

			if ($spotUser != '') {
				$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
														  SET `invoice_request`  = '" . $spotUser . "',
																`payment_status` = 'PAID'													  
														WHERE `status` = 'A'
															  AND `id` = '" . $invoiceId . "' ";

				$mycms->sql_update($sqlUpdateInvoiceRequest, false);

				$sqlAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  
											SET `registration_payment_status` = 'PAID'
											WHERE `id` = '" . $reqId . "' 
												AND `status` = 'A' ";

				$mycms->sql_update($sqlAccompany, false);

				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
											  SET `payment_status` = ?
											WHERE `id` = ? ";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('SLIP_ID'), 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);
			} else {
				$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
														  SET `invoice_request`  = '" . $spotUser . "',
																`payment_status` = 'UNPAID'													  
														WHERE `status` = 'A'
															  AND `id` = '" . $invoiceId . "' ";

				$mycms->sql_update($sqlUpdateInvoiceRequest, false);
			}

			$payment_date = date('Y-m-d');

			// if ($_REQUEST['registration_type_add'] != "ZERO_VALUE") {
			// 	$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
			// 	$sqlUpdatePaymentRequest['QUERY']  = "UPDATE " . _DB_PAYMENT_ . " 
			// 									  SET `payment_status` = 'PAID',
			// 									  `collected_by` = '" . $loggedUserID . "',
			// 									  `payment_type` = 'SPOT',
			// 									  `payment_date` = '" . $payment_date . "',
			// 									  `amount` = '" . $paymentDetailsAmount . "'
			// 											  WHERE `id` = '" . $paymentId . "' 
			// 											  AND `status` = 'A' ";

			// 	$mycms->sql_update($sqlUpdatePaymentRequest, false);
			// }

			//30-07-2024
			if ($_REQUEST['registration_type_add'] != "ZERO_VALUE") {

				// if ($_REQUEST['registration_type_add'] == "GENERAL") {
				if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
					updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
					$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
				} else {
					$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
				}
				// }
				$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
				if ($spotUser != '') {
					$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '" . $loggedUserID . "',
												  `payment_type` = 'SPOT',
												  `payment_date` = '" . $payment_date . "',
												  `amount` = '" . $paymentDetailsAmount . "'
														  WHERE `id` = '" . $paymentId . "' 
														  AND `status` = 'A' ";

					$mycms->sql_update($sqlUpdatePaymentRequest, false);
				} else {
					$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
												  SET `payment_status` = 'UNPAID',
												  `collected_by` = '" . $loggedUserID . "',
												  
												  `payment_date` = '" . $payment_date . "',
												  `amount` = '" . $paymentDetailsAmount . "'
														  WHERE `id` = '" . $paymentId . "' 
														  AND `status` = 'A' ";

					$mycms->sql_update($sqlUpdatePaymentRequest, false);
				}
			}
			if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
				zeroValueInvoiceUpdate($invoiceId, 'ACCOMPANY', $slipId);
				gstInsertionInInvoice($invoiceId);
			}
		}

		$accDinnerDetails = $accompanyDetails['banquet_dinner_add'];

		if ($accDinnerDetails) {
			foreach ($accDinnerDetails as $key => $val) {
				$dinnerDetailsArray1[$key]['refference_id'] = $accompanyReqId[$key - 1];
				$dinnerDetailsArray1[$key]['delegate_id'] = $delegateId;
				$dinnerDetailsArray1[$key]['package_id'] = 2;
				$dinnerDetailsArray1[$key]['tariff_cutoff_id'] = $accompanyDetails['cutoff_id_add'];
				$dinnerDetailsArray1[$key]['booking_quantity'] = 1;
				$dinnerDetailsArray1[$key]['booking_mode'] = 'OFFLINE';
				$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray1[$key]['refference_slip_id'] = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray1[$key]['payment_status'] = $_REQUEST['registration_type_add'] == "ZERO_VALUE" ? 'ZERO_VALUE' : 'UNPAID';
			}

			$dinerReqId = insertingDinnerDetails($dinnerDetailsArray1);

			//echo "<pre>"; print_r($dinerReqId); echo "</pre>"; die();
			foreach ($dinerReqId as $key => $reqId) {
				if ($spotUser != '') {
					$sqlAccompany['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "  
												SET `payment_status` = '" . $payment_status . "'
												WHERE `id` = '" . $reqId . "' 
													AND `status` = 'A' ";

					$mycms->sql_update($sqlAccompany, false);
				}

				//insertingInvoiceDetails($reqId,'DINNER','GENERAL', $date);
				$invoiceId = insertingInvoiceDetails($reqId, 'DINNER');
				if ($spotUser != '') {
					$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
												  SET `invoice_request`  = '" . $spotUser . "',
														`payment_status` = '" . $payment_status . "'													  
												WHERE `status` = 'A'
													  AND `id` = '" . $invoiceId . "' ";

					$mycms->sql_update($sqlUpdateInvoiceRequest, false);
				}
				if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceId, 'DINNER');
					gstInsertionInInvoice($invoiceId);
				}
			}
		}

		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") // mail for complimentry
		{
			offline_conference_registration_confirmation_accompany_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");

			//$mycms->redirect("complementary.online.payment.success.php");
		}

		// if ($_REQUEST['registration_type_add'] == "GENERAL") {
		// 	if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
		// 		updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
		// 		$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
		// 	}
		// }
		if ($spotUser != '') {
			$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
			$payment_date = date('Y-m-d');

			/*$sqlUpdatePaymentRequest['QUERY']  = "UPDATE "._DB_PAYMENT_." 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '".$loggedUserID."',
												  `payment_type` = 'SPOT',
												  `payment_date` = '".$payment_date."',
												  `amount` = '".$paymentDetailsAmount."'
														  WHERE `id` = '".$paymentId."' 
														  AND `status` = 'A' ";

				$mycms->sql_update($sqlUpdatePaymentRequest, false);*/
		}
	}

	if ($spotUser != '') {
		//pageRedirection("registration_new.php?show=viewAll&id=".$delegateId."&slipId=".$mycms->getSession('SLIP_ID')."&paymentId=".$paymentId."&reg_type=".$_REQUEST['registration_type_add']."&mailFor=Accom&userREGtype=SPOT&mode=spotSearch");
		pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
	} else {
		if ($_REQUEST['registration_type_add'] == "GENERAL") // mail for General
		{

			/*$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
					$sqlUpdatePaymentRequest['QUERY']  = "UPDATE "._DB_PAYMENT_." 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '".$loggedUserID."',
												  `payment_type` = 'SPOT',
												  `payment_date` = '".$payment_date."',
												  `amount` = '".$paymentDetailsAmount."'
														  WHERE `id` = '".$paymentId."' 
														  AND `status` = 'A' ";*/

			//$mycms->sql_update($sqlUpdatePaymentRequest, false);

			offline_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $mycms->getSession('SLIP_ID'), 'SEND');
		}




		pageRedirection("registration_new.php?show=viewAll", "");
	}
}

function addGuestAccompany($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	// include_once('../../includes/function.delegate.php');
	// include_once('../../includes/function.registration.php');
	// include_once('../../includes/function.accompany.php');
	// echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die();
	$loggedUserID = $mycms->getLoggedUserId();
	$accompanyDetails = $_REQUEST;
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	$delegateId = $_REQUEST['delegate_id'];
	$discountAmount = $_REQUEST['discountAmount'];

	if ($accompanyDetails) {
		$registrationMode = ($accompanyDetails['registrationMode'] == "OFFLINE") ? "OFFLINE" : "ONLINE";
		foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {
			$accompanyDetailsArray[$val]['refference_delegate_id'] = $delegateId;
			$accompanyDetailsArray[$val]['user_full_name'] = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
			$accompanyDetailsArray[$val]['user_age'] = 0;
			$accompanyDetailsArray[$val]['user_food_preference'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
			$accompanyDetailsArray[$val]['user_food_details'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
			$accompanyDetailsArray[$val]['accompany_relationship'] = '';

			$accompanyDetailsArray[$val]['isRegistration'] = 'Y';
			$accompanyDetailsArray[$val]['isConference'] = 'Y';
			$accompanyDetailsArray[$val]['registration_classification_id'] = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
			$accompanyDetailsArray[$val]['registration_tariff_cutoff_id'] = $accompanyDetails['cutoff_id_add'];
			$accompanyDetailsArray[$val]['registration_request'] = 'GUEST';
			$accompanyDetailsArray[$val]['operational_area'] = 'GUEST';
			$accompanyDetailsArray[$val]['registration_payment_status'] = 'ZERO_VALUE';
			$accompanyDetailsArray[$val]['registration_mode'] = "OFFLINE";
			$accompanyDetailsArray[$val]['account_status'] = 'REGISTERED';
			$accompanyDetailsArray[$val]['reg_type'] = addslashes(trim(strtoupper('BACK')));
		}

		$slipId = insertingSlipDetails($delegateId, 'OFFLINE', "");

		$accompanyReqId = insertingAccompanyDetails($accompanyDetailsArray);

		foreach ($accompanyReqId as $key => $reqId) {
			$invoiceId = insertingInvoiceDetails($reqId, 'ACCOMPANY');
			zeroValueInvoiceUpdate($invoiceId, 'ACCOMPANY', $slipId);
			gstInsertionInInvoice($invoiceId);
		}

		$accDinnerDetails = $accompanyDetails['dinner_value'];

		if ($accDinnerDetails) {
			foreach ($accDinnerDetails as $key => $val) {
				$dinnerDetailsArray1[$key]['refference_id'] = $accompanyReqId[$key - 1];
				$dinnerDetailsArray1[$key]['delegate_id'] = $delegateId;
				$dinnerDetailsArray1[$key]['package_id'] = 2;
				$dinnerDetailsArray1[$key]['tariff_cutoff_id'] = $accompanyDetails['cutoff_id_add'];
				$dinnerDetailsArray1[$key]['booking_quantity'] = 1;
				$dinnerDetailsArray1[$key]['booking_mode'] = 'OFFLINE';
				$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray1[$key]['refference_slip_id'] = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray1[$key]['payment_status'] = $_REQUEST['registration_type_add'] == "ZERO_VALUE" ? 'ZERO_VALUE' : 'UNPAID';
			}

			$dinerReqId = insertingDinnerDetails($dinnerDetailsArray1);

			foreach ($dinerReqId as $key => $reqId) {
				$invoiceId = insertingInvoiceDetails($reqId, 'DINNER');
				if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceId, 'DINNER');
					gstInsertionInInvoice($invoiceId);
				}
			}
		}

		if ($_REQUEST['registration_type_add'] == "GENERAL") {
			if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
				updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
			}
			$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
		}
	}

	if ($spotUser != '') {
		if ($paymentId != '') {
			$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
			$payment_date = date('Y-m-d');

			$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '" . $mycms->getLoggedUserId() . "',
												  `payment_type` = 'SPOT',
												  `payment_date` = '" . $payment_date . "',
												  `amount` = '" . $paymentDetailsAmount . "'
														  WHERE `id` = '" . $paymentId . "' 
														  AND `status` = 'A' ";
			$mycms->sql_update($sqlUpdatePaymentRequest, false);
		}

		pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
	} elseif ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
		pageRedirection("registration_new.php?show=viewAll", "");
	} else {
		pageRedirection("registration_new.php", 'Make Payment', "&show=makePaymentArea&id=" . $_REQUEST['delegate_id']);
	}
}

function sendRegFinalMail($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];
	$invoice_mode = $_REQUEST['invoice_mode'];
	$user_email_id = $_REQUEST['user_email_id'];
	$user_full_name = $_REQUEST['user_full_name'];
	$mail_subject = $_REQUEST['mail_subject'];
	$mail_body = $_REQUEST['mail_body'];
	$buttonForSpot = $_REQUEST['buttonForSpot'];
	$rowFetchUserDetails = getUserDetails($delegateId);
  
	$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', '', '', '', '', '', $cfg['ADMIN_EMAIL']);

	pageRedirection(
		"invoice_send_mail.php",
		'Mail sent successfully',
		"&show=sendMail&paymentId=" . $_REQUEST['paymentId'] . "&slipId=" . $_REQUEST['slipId'] . "&delegateId=" . $_REQUEST['delegateId']
	);
}

function sendRegFinalSMS($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];
	$invoice_mode = $_REQUEST['invoice_mode'];
	$user_mobile_no = $_REQUEST['user_number'];
	$user_full_name = $_REQUEST['user_full_name'];
	$regsms = $_REQUEST['registration_sms_body'];
	$paysms = $_REQUEST['payment_sms_body'];
	$rowFetchUserDetails = getUserDetails($delegateId);

	if (trim($paysms) != '') {
		$paystatus = $mycms->send_sms($user_mobile_no, $paysms);
		//insertSMSRecord($delegateId, $user_full_name, $user_mobile_no, $paysms, "SEND",$paystatus);
	}

	if (trim($regsms) != '') {
		$regstatus = $mycms->send_sms($user_mobile_no, $regsms);
		//insertSMSRecord($delegateId, $user_full_name, $user_mobile_no, $regsms, "SEND",$regstatus);
	}

	pageRedirection("registration_new.php", 'SMS sent successfully', "&show=invoice&id=" . $_REQUEST['delegateId']);
}

function deleteTrash($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$loggedUserID = $mycms->getLoggedUserId();
	$delegateId = $_REQUEST['id'];

	//REMOVING USER
	$sqlRemove = array();
	$sqlRemove['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
										SET `status` = ?
									  WHERE `id`     = ?";

	$sqlRemove['PARAM'][] = array('FILD' => 'status', 'DATA' => 'F', 'TYP' => 's');
	$sqlRemove['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlRemove);

	//pageRedirection("registration_new.php?show=trash");
	
	 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="trash_registrations.php";</script>';	exit();
}

function Active($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$loggedUserID = $mycms->getLoggedUserId();
	$delegateId = $_REQUEST['id'];
	$sqlActive = array();
	$sqlActive['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
									SET `status` = ?
								  WHERE `id`	 = ?";
	$sqlActive['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$sqlActive['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlActive);
	$sqlRemoveSlip = array();
	$sqlRemoveSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . " 
										   SET `status`  = `status_before_trash`
										 WHERE `delegate_id` = '" . $delegateId . "'";
	$mycms->sql_update($sqlRemoveSlip);

	$sqlRemoveInvoice = array();
	$sqlRemoveInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
											  SET `status` = `status_before_trash`
										   WHERE `delegate_id`     = '" . $delegateId . "'";
	$mycms->sql_update($sqlRemoveInvoice);

	 //    Remove Accomodation
	$sqlRemoveAcco = array();
	$sqlRemoveAcco['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . " 
										   SET `status` = `status_before_trash`
										 WHERE `user_id` =  '" . $delegateId . "'";
	$mycms->sql_update($sqlRemoveAcco);

    // 
	  //    Remove Workshop
	$sqlRemoveWorkshop = array();
	$sqlRemoveWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . " 
										   SET `status` = `status_before_trash`
										WHERE `delegate_id`     = '" . $delegateId . "'";
	$mycms->sql_update($sqlRemoveWorkshop);

    // 

	 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data re-Activated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="registration.php";</script>';	exit();
}

function make_payment($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $_REQUEST['paymentId'];
	$spotUser = $_REQUEST['userREGtype'];
	$slipId = $_REQUEST['slipId'];

	$loggedUserId = $mycms->getLoggedUserId();
	paymentConfirmationProcess();

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
									SET `collected_by` = ?
								  WHERE `id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'collected_by', 'DATA' => $loggedUserId, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	if ($_REQUEST['redirect'] == 'Y') {
		pageRedirection("unpaid.invoice.make.payment.php", 6, "");
	} elseif ($spotUser != '') {
		pageRedirection("registration_new.php", 6, "&show=spotInvoice&mailFor=SPOT&paymentId=" . $paymentId . "&id=" . $_REQUEST['delegateId']);
	} else {
		pageRedirection("registration_new.php", 6, "&show=invoice&id=" . $_REQUEST['delegateId']);
	}
}

function make_partial_payment($mycms, $cfg, $redirect = true)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $_POST['paymentId'];
	$spotUser = $_POST['userREGtype'];
	$slipId = $_POST['slipId'];
	$delegateId = $_POST['delegateId'];


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
		echo '<script>window.location.href="registration.php";</script>';
		exit();
	}
}
function make_partial_payment_spot($mycms, $cfg, $redirect = true)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$paymentId = $_POST['paymentId'];
	$spotUser = $_POST['userREGtype'];
	$slipId = $_POST['slipId'];
	$delegateId = $_POST['delegateId'];


	$loggedUserId = $mycms->getLoggedUserId();
	partialPaymentConfirmationProcess($redirect);

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
									SET `collected_by` = ?
								  WHERE `id` = ?";
	$sqlUpdate['PARAM'][] = array('FILD' => 'collected_by', 'DATA' => $loggedUserId, 'TYP' => 's');
	$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $paymentId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);

	
}



/*function changePaymentMode($mycms,$cfg)
	{
		$loggedUserID = $mycms->getLoggedUserId();

		$registrationMode = $_REQUEST['registrationMode'];
		$slipId			  = $_REQUEST['slip_id'];

		$reason = "Change To ".strtolower(ucwords($registrationMode));
		insertingSlipCopy($slipId,$reason);

		$sqlUpdateSlip = array();
		$sqlUpdateSlip['QUERY']	  = "UPDATE "._DB_SLIP_."
										SET `invoice_mode`	= ?,
											`payment_status` = ?
									  WHERE `id` = ?";

		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'invoice_mode',               'DATA' =>$registrationMode,   'TYP' => 's');	
		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'payment_status',             'DATA' =>'UNPAID', 		'TYP' => 's');	
		$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'id',                         'DATA' =>$slipId,    'TYP' => 's');			 
		$mycms->sql_update($sqlUpdateSlip, false);



		$slipInvoiceDetails = invoiceDetailsOfSlip($slipId);	
		//echo '<pre>'; print_r($slipInvoiceDetails);
		foreach($slipInvoiceDetails as $key => $invoiceDetails) 
		{

			//$invoiceDetails = getInvoiceDetails($invoiceId);
			$invoiceId = $invoiceDetails['id'];

			$clsfId	   = getUserClassificationId($invoiceDetails['delegate_id']);
			$cutoffId  = getUserCutoffId($invoiceDetails['delegate_id']);
			$tariffAmt = getRegistrationTariffAmount($clsfId,$cutoffId);

			$invoiceDetailsArr['id']							= $invoiceId;
			$invoiceDetailsArr['invoice_mode']					= $registrationMode;

			if($registrationMode=="ONLINE")
			{
				$invoiceDetailsArr['internet_handling_percentage']	= $cfg['INTERNET.HANDLING.PERCENTAGE'];
				$invoiceDetailsArr['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'],$cfg['INTERNET.HANDLING.PERCENTAGE']);
				if($invoiceDetails['has_gst'] == 'Y')
				{
					$gstIntArray = gstCalculation($cfg['INT.CGST'],$cfg['INT.SGST'] ,$invoiceDetails['service_product_price']);

					$invoiceDetailsArr['cgst_int_percentage']		= $cfg['INT.CGST'];
					$invoiceDetailsArr['sgst_int_percentage']		= $cfg['INT.SGST'];
					$invoiceDetailsArr['cgst_int_price']			= $gstIntArray['CGST.PRICE'];
					$invoiceDetailsArr['sgst_int_price']			= $gstIntArray['SGST.PRICE'];
					$invoiceDetailsArr['service_basic_int_price']	= $gstIntArray['BASIC.PRICE'];
					$invoiceDetailsArr['service_gst_int_price']		= $gstIntArray['GST.PRICE'];

					if($cfg['GST.FLAG']==1)
					{
						//echo $cfg['INTERNET.HANDLING.PERCENTAGE'];
						$invoiceDetailsArr['internet_handling_amount']		= calculateTaxAmmount($invoiceDetailsArr['service_gst_int_price'],$cfg['INTERNET.HANDLING.PERCENTAGE']);
					}
					else
					{
						$invoiceDetailsArr['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'],$cfg['INTERNET.HANDLING.PERCENTAGE']);
					}




				}
			}
			elseif($registrationMode=="OFFLINE")
			{
				$invoiceDetailsArr['internet_handling_percentage']	= "0.00";
				$invoiceDetailsArr['internet_handling_amount']		= "0.00";
				//if($invoiceDetails['has_gst'] == 'Y')
				//{

					$gstIntArray = gstCalculation($cfg['INT.CGST'],$cfg['INT.SGST'] ,$invoiceDetails['service_product_price']);
					$invoiceDetailsArr['cgst_internet_handling_percentage']		= '0';
					$invoiceDetailsArr['sgst_internet_handling_percentage']		= '0';
					$invoiceDetailsArr['cgst_internet_handling_price']			= '0';
					$invoiceDetailsArr['sgst_internet_handling_price']			= '0';
					$invoiceDetailsArr['internet_handling_amount']				= '0';
					$invoiceDetailsArr['internet_handling_gst_price']			= '0';


					$invoiceDetailsArr['cgst_int_price']			= $gstIntArray['CGST.PRICE'];
					$invoiceDetailsArr['sgst_int_price']			= $gstIntArray['SGST.PRICE'];


					$invoiceDetailsArr['service_basic_int_price']	= $gstIntArray['BASIC.PRICE'];
					$invoiceDetailsArr['service_gst_int_price']		= $gstIntArray['GST.PRICE'];
				//}

			}


			//echo $registrationMode;

			//echo 'registrationMode='.$registrationMode;


			$invoiceDetails['applicable_tax_price'] = $invoiceDetailsArr['cgst_int_price'] + $invoiceDetailsArr['sgst_int_price'];

			//echo $registrationMode;
			//echo '<pre>'; print_r($invoiceDetailsArr);	
			//die();			
			$invoiceDetailsArr['service_unit_price']				= $invoiceDetails['service_unit_price'];
			$invoiceDetailsArr['service_product_price']				= $invoiceDetails['service_product_price'];
			$invoiceDetailsArr['service_total_price']				= $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'];
			$invoiceDetailsArr['service_grand_price']				= $invoiceDetailsArr['service_total_price'];
			 $invoiceDetailsArr['service_roundoff_price']			= intToFloat(round($invoiceDetailsArr['service_total_price']));

			// echo '<pre>'; print_r($invoiceDetailsArr);
			//die();


			insertingInvoiceCopy($invoiceId,$reason);
			$sqlUpdateInvoice = array();
			$sqlUpdateInvoice['QUERY'] = "UPDATE "._DB_INVOICE_."
											SET `invoice_mode`	   	 		 	 = ?,
												`internet_handling_percentage`	 = ?, 
												`internet_handling_amount` 	  	 = ?, 
												`service_unit_price` 	 	 	 = ?, 
												`service_product_price` 		 = ?, 
												`service_total_price` 	 	 	 = ?, 
												`service_grand_price` 			 = ?, 
												`service_roundoff_price`	 	 = ?,";

			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'invoice_mode',               		'DATA' =>$invoiceDetailsArr['invoice_mode'],   					'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'internet_handling_percentage',    'DATA' =>$invoiceDetailsArr['internet_handling_percentage'],    'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'internet_handling_amount',        'DATA' =>$invoiceDetailsArr['internet_handling_amount'],   		'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_unit_price',              'DATA' =>$invoiceDetailsArr['service_unit_price'],   			'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_product_price',           'DATA' =>$invoiceDetailsArr['service_product_price'],   		'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_total_price',             'DATA' =>$invoiceDetailsArr['service_total_price'],   			'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_grand_price',             'DATA' =>$invoiceDetailsArr['service_grand_price'], 			'TYP' => 's');	
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_roundoff_price',          'DATA' =>$invoiceDetailsArr['service_roundoff_price'],  		'TYP' => 's');								
			if($invoiceDetails['has_gst'] == 'Y')
			{							
				$sqlUpdateInvoice['QUERY'] .= "`service_roundoff_price`  						 = ?,";

				$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_roundoff_price',             'DATA' =>round($invoiceDetailsArr['service_roundoff_price']),   		'TYP' => 's');											

			}							
			$sqlUpdateInvoice['QUERY'] .= 	"`payment_status` 				 = ?
												WHERE `id` = ?";
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'payment_status',          'DATA' =>'UNPAID',  					'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'id',          			'DATA' =>$invoiceDetails['id'],  		'TYP' => 's');					  
			$mycms->sql_update($sqlUpdateInvoice, false);



			if($invoiceDetails['service_type']=="DELEGATE_RESIDENTIAL_REGISTRATION")
			{
				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE "._DB_REQUEST_WORKSHOP_."
												 SET `booking_mode`	   	 	 = ?,
													 `payment_status` 		 = ?
											   WHERE `refference_invoice_id` = ?
												 AND `refference_slip_id` 	 = ?";
				$sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'booking_mode',             'DATA' =>$registrationMode,  			'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'payment_status',           'DATA' =>'UNPAID',  					'TYP' => 's');	
				$sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'refference_invoice_id',    'DATA' => $invoiceId,  				'TYP' => 's');	
				$sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'refference_slip_id',       'DATA' =>$slipId,  					'TYP' => 's');					   
				 $mycms->sql_update($sqlUpdateWorkshop, false);

				 $sqlUpdateAcmdaton = array();
				 $sqlUpdateAcmdaton['QUERY'] = "UPDATE "._DB_REQUEST_ACCOMMODATION_."
												  SET `booking_mode`	   	 	 = ?,
													  `payment_status` 		 = ?
												WHERE `refference_invoice_id` = ?
												  AND `refference_slip_id` 	 = ?";
				$sqlUpdateAcmdaton['PARAM'][]   = array('FILD' => 'booking_mode',             'DATA' =>$registrationMode,  			'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][]   = array('FILD' => 'payment_status',           'DATA' =>'UNPAID',  					'TYP' => 's');	
				$sqlUpdateAcmdaton['PARAM'][]   = array('FILD' => 'refference_invoice_id',    'DATA' => $invoiceId,  				'TYP' => 's');	
				$sqlUpdateAcmdaton['PARAM'][]   = array('FILD' => 'refference_slip_id',       'DATA' =>$slipId,  					'TYP' => 's');						   
				$mycms->sql_update($sqlUpdateAcmdaton, false);

				  $sqlUpdateDelegate = array();
				  $sqlUpdateDelegate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
												   SET `registration_mode`	  = ?,
													   `registration_payment_status` = ?
												 WHERE `id` = ?";
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'registration_mode',             'DATA' =>$registrationMode,  			'TYP' => 's');
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' =>'UNPAID',  					'TYP' => 's');	
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'id',             				'DATA' =>$invoiceDetails['delegate_id'],'TYP' => 's');				   
				 $mycms->sql_update($sqlUpdateDelegate, false);
			}
			if($invoiceDetails['service_type']=="DELEGATE_CONFERENCE_REGISTRATION")
			{
					$sqlUpdateDelegate = array();
				  $sqlUpdateDelegate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
													 SET `registration_mode`	  = ?,
														 `registration_payment_status` = ?
												   WHERE `id` = ?";
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'registration_mode',             'DATA' =>$registrationMode,  			'TYP' => 's');
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' =>'UNPAID',  					'TYP' => 's');	
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'id',             				'DATA' =>$invoiceDetails['delegate_id'],'TYP' => 's');				   
			   $mycms->sql_update($sqlUpdateDelegate, false);
			}
			if($invoiceDetails['service_type']=="ACCOMPANY_CONFERENCE_REGISTRATION")
			{
				$sqlUpdateDelegate = array();
				  $sqlUpdateDelegate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
													 SET `registration_mode`	  = ?,
														 `registration_payment_status` = ?
												   WHERE `id` = ?";
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'registration_mode',             'DATA' =>$registrationMode,  			'TYP' => 's');
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' =>'UNPAID',  					'TYP' => 's');	
				 $sqlUpdateDelegate['PARAM'][]   = array('FILD' => 'id',             				'DATA' =>$invoiceDetails['delegate_id'],'TYP' => 's');

			  // $mycms->sql_update($sqlUpdateDelegate, false);
			}
			if($invoiceDetails['service_type']=="DELEGATE_WORKSHOP_REGISTRATION")
			{
				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE "._DB_REQUEST_WORKSHOP_."
												 SET `booking_mode`	   	 	 = ?,
													 `payment_status` 		 = ?
											   WHERE `id` = ?";
				 $sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'booking_mode',             	 'DATA' =>$registrationMode,  							'TYP' => 's');	
				 $sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'payment_status',             'DATA' =>'UNPAID',  									'TYP' => 's');	
				 $sqlUpdateWorkshop['PARAM'][]   = array('FILD' => 'id',            			 'DATA' =>$invoiceDetails['refference_id'],  			'TYP' => 's');					   
				 $mycms->sql_update($sqlUpdateWorkshop, false);
			}
			if($invoiceDetails['service_type']=="DELEGATE_ACCOMMODATION_REQUEST")
			{
				 $sqlUpdate = array();
				 $sqlUpdate['QUERY']	  = "UPDATE "._DB_REQUEST_ACCOMMODATION_."
												SET `booking_mode` = ?,
													`payment_status` = ?
											  WHERE `id` = ?";
				 $sqlUpdate['PARAM'][]   = array('FILD' => 'booking_mode',             	 'DATA' =>$registrationMode,  							'TYP' => 's');	
				 $sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',             'DATA' =>'UNPAID',  									'TYP' => 's');	
				 $sqlUpdate['PARAM'][]   = array('FILD' => 'id',            			 'DATA' =>$invoiceDetails['refference_id'],  			'TYP' => 's');						 
				$mycms->sql_update($sqlUpdate, false);
			}
			if($invoiceDetails['service_type']=="DELEGATE_DINNER_REQUEST")
			{
				$sqlUpdate = array();
				$sqlUpdate['QUERY']	  = "UPDATE "._DB_REQUEST_DINNER_."
											SET `booking_mode` = ?,
												`payment_status` = ?
										  WHERE `id` = ?";
				 $sqlUpdate['PARAM'][]   = array('FILD' => 'booking_mode',             	 'DATA' =>$registrationMode,  							'TYP' => 's');	
				 $sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',             'DATA' =>'UNPAID',  									'TYP' => 's');	
				 $sqlUpdate['PARAM'][]   = array('FILD' => 'id',            			 'DATA' =>$invoiceDetails['refference_id'],  			'TYP' => 's');						 
				$mycms->sql_update($sqlUpdate, false);
			}

		}
		pageRedirection("registration_new.php",6,"&show=invoice&id=".$_REQUEST['delegate_id']);
		exit();
	}*/

function changePaymentMode($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$registrationMode = $_REQUEST['registrationMode'];
	$slipId = $_REQUEST['slipId'];
   
	$reason = "Change To " . strtolower(ucwords($registrationMode));
	insertingSlipCopy($slipId, $reason);

	$sqlUpdateSlip = array();
	$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
										SET `invoice_mode`	= ?,
											`payment_status` = ?
									  WHERE `id` = ?";

	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'invoice_mode', 'DATA' => $registrationMode, 'TYP' => 's');
	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $slipId, 'TYP' => 's');
	// echo '<pre>'; print_r($sqlUpdateSlip);die();

	$mycms->sql_update($sqlUpdateSlip, false);


	$slipInvoiceDetails = invoiceDetailsOfSlip($slipId);
	// echo '<pre>'; print_r($slipInvoiceDetails);die();
	foreach ($slipInvoiceDetails as $key => $invoiceDetails) {

		//$invoiceDetails = getInvoiceDetails($invoiceId);
		$invoiceId = $invoiceDetails['id'];

		$clsfId = getUserClassificationId($invoiceDetails['delegate_id']);
		$cutoffId = getUserCutoffId($invoiceDetails['delegate_id']);
		$tariffAmt = getRegistrationTariffAmount($clsfId, $cutoffId);

		$invoiceDetailsArr['id'] = $invoiceId;
		$invoiceDetailsArr['invoice_mode'] = $registrationMode;

		if ($registrationMode == "ONLINE") {
			$invoiceDetailsArr['internet_handling_percentage'] = $cfg['INTERNET.HANDLING.PERCENTAGE'];
			$invoiceDetailsArr['internet_handling_amount'] = calculateTaxAmmount($invoiceDetails['service_product_price'], $cfg['INTERNET.HANDLING.PERCENTAGE']);
			if ($invoiceDetails['has_gst'] == 'Y') {

				$gstIntArray = gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $invoiceDetails['service_product_price']);
				//$gstIntArray = gstCalculation($cfg['INT.CGST'],$cfg['INT.SGST'] ,$invoiceDetailsArr['internet_handling_amount']);

				$invoiceDetailsArr['cgst_int_percentage'] = $cfg['INT.CGST'];
				$invoiceDetailsArr['sgst_int_percentage'] = $cfg['INT.SGST'];
				$invoiceDetailsArr['cgst_int_price'] = $gstIntArray['CGST.PRICE'];
				$invoiceDetailsArr['sgst_int_price'] = $gstIntArray['SGST.PRICE'];
				$invoiceDetailsArr['service_basic_int_price'] = $gstIntArray['BASIC.PRICE'];
				$invoiceDetailsArr['service_gst_int_price'] = $gstIntArray['GST.PRICE'];

				if ($cfg['GST.FLAG'] == 1) {
					//echo $cfg['INTERNET.HANDLING.PERCENTAGE'];
					$invoiceDetailsArr['internet_handling_amount'] = calculateTaxAmmount($invoiceDetailsArr['service_gst_int_price'], $cfg['INTERNET.HANDLING.PERCENTAGE']);
				} else {
					$invoiceDetailsArr['internet_handling_amount'] = calculateTaxAmmount($invoiceDetails['service_product_price'], $cfg['INTERNET.HANDLING.PERCENTAGE']);
				}
			}
		} elseif ($registrationMode == "OFFLINE") {

			$invoiceDetailsArr['internet_handling_percentage'] = "0.00";
			$invoiceDetailsArr['internet_handling_amount'] = "0.00";
			//if($invoiceDetails['has_gst'] == 'Y')
			//{

			$gstIntArray = gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $invoiceDetails['service_product_price']);
			$invoiceDetailsArr['cgst_internet_handling_percentage'] = '0';
			$invoiceDetailsArr['sgst_internet_handling_percentage'] = '0';
			$invoiceDetailsArr['cgst_internet_handling_price'] = '0';
			$invoiceDetailsArr['sgst_internet_handling_price'] = '0';
			$invoiceDetailsArr['internet_handling_amount'] = '0';
			$invoiceDetailsArr['internet_handling_gst_price'] = '0';


			$invoiceDetailsArr['cgst_int_price'] = $gstIntArray['CGST.PRICE'];
			$invoiceDetailsArr['sgst_int_price'] = $gstIntArray['SGST.PRICE'];


			$invoiceDetailsArr['service_basic_int_price'] = $gstIntArray['BASIC.PRICE'];
			$invoiceDetailsArr['service_gst_int_price'] = $gstIntArray['GST.PRICE'];
			//}

		}


		//echo $registrationMode;

		//echo 'registrationMode='.$registrationMode;


		$invoiceDetails['applicable_tax_price'] = $invoiceDetailsArr['cgst_int_price'] + $invoiceDetailsArr['sgst_int_price'];

		// echo $registrationMode;
		// echo '<pre>'; print_r($invoiceDetails);	
		//die();			
		$invoiceDetailsArr['service_unit_price'] = $invoiceDetails['service_unit_price'];
		$invoiceDetailsArr['service_product_price'] = $invoiceDetails['service_product_price'];

		if (!empty($invoiceDetails['discount_amount']) && $invoiceDetails['discount_amount'] > 0 && $registrationMode == "ONLINE") {
			$invoiceDetailsArr['service_total_price'] = $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'] - $invoiceDetails['discount_amount'];
		} else if (!empty($invoiceDetails['discount_amount']) && $invoiceDetails['discount_amount'] > 0 && $registrationMode == "OFFLINE") {
			$invoiceDetailsArr['service_total_price'] = $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'] - $invoiceDetails['discount_amount'];
		} else {

			$invoiceDetailsArr['service_total_price'] = $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'];
		}



		$invoiceDetailsArr['service_grand_price'] = $invoiceDetailsArr['service_total_price'];
		$invoiceDetailsArr['service_roundoff_price'] = intToFloat(round($invoiceDetailsArr['service_total_price']));

		/*echo '<pre>'; print_r($invoiceDetailsArr);
		   die();*/

		if (!empty($invoiceDetails['service_roundoff_price']) && $invoiceDetails['service_roundoff_price'] > 0) {

			insertingInvoiceCopy($invoiceId, $reason);
			$sqlUpdateInvoice = array();
			$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
													SET `invoice_mode`	   	 		 	 = ?,
														`internet_handling_percentage`	 = ?, 
														`internet_handling_amount` 	  	 = ?, 
														`service_unit_price` 	 	 	 = ?, 
														`service_product_price` 		 = ?, 
														`service_total_price` 	 	 	 = ?, 
														`service_grand_price` 			 = ?, 
														`service_roundoff_price`	 	 = ?,";

			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'invoice_mode', 'DATA' => $invoiceDetailsArr['invoice_mode'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'internet_handling_percentage', 'DATA' => $invoiceDetailsArr['internet_handling_percentage'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'internet_handling_amount', 'DATA' => $invoiceDetailsArr['internet_handling_amount'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_unit_price', 'DATA' => $invoiceDetailsArr['service_unit_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_product_price', 'DATA' => $invoiceDetailsArr['service_product_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_total_price', 'DATA' => $invoiceDetailsArr['service_total_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_grand_price', 'DATA' => $invoiceDetailsArr['service_grand_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_roundoff_price', 'DATA' => $invoiceDetailsArr['service_roundoff_price'], 'TYP' => 's');
			if ($invoiceDetails['has_gst'] == 'Y') {
				$sqlUpdateInvoice['QUERY'] .= "`service_roundoff_price`  						 = ?,";

				$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_roundoff_price', 'DATA' => round($invoiceDetailsArr['service_roundoff_price']), 'TYP' => 's');
			}
			$sqlUpdateInvoice['QUERY'] .= "`payment_status` 				 = ?
														WHERE `id` = ?";
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['id'], 'TYP' => 's');
			$mycms->sql_update($sqlUpdateInvoice, false);



			if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														 SET `booking_mode`	   	 	 = ?,
															 `payment_status` 		 = ?
													   WHERE `refference_invoice_id` = ?
														 AND `refference_slip_id` 	 = ?";
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'refference_slip_id', 'DATA' => $slipId, 'TYP' => 's');
				$mycms->sql_update($sqlUpdateWorkshop, false);

				$sqlUpdateAcmdaton = array();
				$sqlUpdateAcmdaton['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														  SET `booking_mode`	   	 	 = ?,
															  `payment_status` 		 = ?
														WHERE `refference_invoice_id` = ?
														  AND `refference_slip_id` 	 = ?";
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'refference_slip_id', 'DATA' => $slipId, 'TYP' => 's');
				$mycms->sql_update($sqlUpdateAcmdaton, false);

				$sqlUpdateDelegate = array();
				$sqlUpdateDelegate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														   SET `registration_mode`	  = ?,
															   `registration_payment_status` = ?
														 WHERE `id` = ?";
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['delegate_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateDelegate, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
				$sqlUpdateDelegate = array();
				$sqlUpdateDelegate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															 SET `registration_mode`	  = ?,
																 `registration_payment_status` = ?
														   WHERE `id` = ?";
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['delegate_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateDelegate, false);
			}
			if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$sqlUpdateDelegate = array();
				$sqlUpdateDelegate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															 SET `registration_mode`	  = ?,
																 `registration_payment_status` = ?
														   WHERE `id` = ?";
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['delegate_id'], 'TYP' => 's');

				// $mycms->sql_update($sqlUpdateDelegate, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														 SET `booking_mode`	   	 	 = ?,
															 `payment_status` 		 = ?
													   WHERE `id` = ?";
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateWorkshop, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$sqlUpdate = array();
				$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														SET `booking_mode` = ?,
															`payment_status` = ?
													  WHERE `id` = ?";
				$sqlUpdate['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdate, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
				$sqlUpdate = array();
				$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "
													SET `booking_mode` = ?,
														`payment_status` = ?
												  WHERE `id` = ?";
				$sqlUpdate['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdate, false);
			}
		}
	}
				pageRedirection("registration.php", "Payment Mode Changed Successfully");
	exit();
}

function changePaymentModeAjax($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$registrationMode = $_REQUEST['registrationMode'];
	$slipId = $_REQUEST['slipId'];
   
	$reason = "Change To " . strtolower(ucwords($registrationMode));
	insertingSlipCopy($slipId, $reason);

	$sqlUpdateSlip = array();
	$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
										SET `invoice_mode`	= ?,
											`payment_status` = ?
									  WHERE `id` = ?";

	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'invoice_mode', 'DATA' => $registrationMode, 'TYP' => 's');
	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
	$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $slipId, 'TYP' => 's');
	// echo '<pre>'; print_r($sqlUpdateSlip);die();

	$mycms->sql_update($sqlUpdateSlip, false);


	$slipInvoiceDetails = invoiceDetailsOfSlip($slipId);
	// echo '<pre>'; print_r($slipInvoiceDetails);die();
	foreach ($slipInvoiceDetails as $key => $invoiceDetails) {

		//$invoiceDetails = getInvoiceDetails($invoiceId);
		$invoiceId = $invoiceDetails['id'];

		$clsfId = getUserClassificationId($invoiceDetails['delegate_id']);
		$cutoffId = getUserCutoffId($invoiceDetails['delegate_id']);
		$tariffAmt = getRegistrationTariffAmount($clsfId, $cutoffId);

		$invoiceDetailsArr['id'] = $invoiceId;
		$invoiceDetailsArr['invoice_mode'] = $registrationMode;

		if ($registrationMode == "ONLINE") {
			$invoiceDetailsArr['internet_handling_percentage'] = $cfg['INTERNET.HANDLING.PERCENTAGE'];
			$invoiceDetailsArr['internet_handling_amount'] = calculateTaxAmmount($invoiceDetails['service_product_price'], $cfg['INTERNET.HANDLING.PERCENTAGE']);
			if ($invoiceDetails['has_gst'] == 'Y') {

				$gstIntArray = gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $invoiceDetails['service_product_price']);
				//$gstIntArray = gstCalculation($cfg['INT.CGST'],$cfg['INT.SGST'] ,$invoiceDetailsArr['internet_handling_amount']);

				$invoiceDetailsArr['cgst_int_percentage'] = $cfg['INT.CGST'];
				$invoiceDetailsArr['sgst_int_percentage'] = $cfg['INT.SGST'];
				$invoiceDetailsArr['cgst_int_price'] = $gstIntArray['CGST.PRICE'];
				$invoiceDetailsArr['sgst_int_price'] = $gstIntArray['SGST.PRICE'];
				$invoiceDetailsArr['service_basic_int_price'] = $gstIntArray['BASIC.PRICE'];
				$invoiceDetailsArr['service_gst_int_price'] = $gstIntArray['GST.PRICE'];

				if ($cfg['GST.FLAG'] == 1) {
					//echo $cfg['INTERNET.HANDLING.PERCENTAGE'];
					$invoiceDetailsArr['internet_handling_amount'] = calculateTaxAmmount($invoiceDetailsArr['service_gst_int_price'], $cfg['INTERNET.HANDLING.PERCENTAGE']);
				} else {
					$invoiceDetailsArr['internet_handling_amount'] = calculateTaxAmmount($invoiceDetails['service_product_price'], $cfg['INTERNET.HANDLING.PERCENTAGE']);
				}
			}
		} elseif ($registrationMode == "OFFLINE") {

			$invoiceDetailsArr['internet_handling_percentage'] = "0.00";
			$invoiceDetailsArr['internet_handling_amount'] = "0.00";
			//if($invoiceDetails['has_gst'] == 'Y')
			//{

			$gstIntArray = gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $invoiceDetails['service_product_price']);
			$invoiceDetailsArr['cgst_internet_handling_percentage'] = '0';
			$invoiceDetailsArr['sgst_internet_handling_percentage'] = '0';
			$invoiceDetailsArr['cgst_internet_handling_price'] = '0';
			$invoiceDetailsArr['sgst_internet_handling_price'] = '0';
			$invoiceDetailsArr['internet_handling_amount'] = '0';
			$invoiceDetailsArr['internet_handling_gst_price'] = '0';


			$invoiceDetailsArr['cgst_int_price'] = $gstIntArray['CGST.PRICE'];
			$invoiceDetailsArr['sgst_int_price'] = $gstIntArray['SGST.PRICE'];


			$invoiceDetailsArr['service_basic_int_price'] = $gstIntArray['BASIC.PRICE'];
			$invoiceDetailsArr['service_gst_int_price'] = $gstIntArray['GST.PRICE'];
			//}

		}


		//echo $registrationMode;

		//echo 'registrationMode='.$registrationMode;


		$invoiceDetails['applicable_tax_price'] = $invoiceDetailsArr['cgst_int_price'] + $invoiceDetailsArr['sgst_int_price'];

		// echo $registrationMode;
		// echo '<pre>'; print_r($invoiceDetails);	
		//die();			
		$invoiceDetailsArr['service_unit_price'] = $invoiceDetails['service_unit_price'];
		$invoiceDetailsArr['service_product_price'] = $invoiceDetails['service_product_price'];

		if (!empty($invoiceDetails['discount_amount']) && $invoiceDetails['discount_amount'] > 0 && $registrationMode == "ONLINE") {
			$invoiceDetailsArr['service_total_price'] = $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'] - $invoiceDetails['discount_amount'];
		} else if (!empty($invoiceDetails['discount_amount']) && $invoiceDetails['discount_amount'] > 0 && $registrationMode == "OFFLINE") {
			$invoiceDetailsArr['service_total_price'] = $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'] - $invoiceDetails['discount_amount'];
		} else {

			$invoiceDetailsArr['service_total_price'] = $invoiceDetailsArr['service_basic_int_price'] + $invoiceDetails['applicable_tax_price'] + $invoiceDetailsArr['internet_handling_amount'];
		}



		$invoiceDetailsArr['service_grand_price'] = $invoiceDetailsArr['service_total_price'];
		$invoiceDetailsArr['service_roundoff_price'] = intToFloat(round($invoiceDetailsArr['service_total_price']));

		/*echo '<pre>'; print_r($invoiceDetailsArr);
		   die();*/

		if (!empty($invoiceDetails['service_roundoff_price']) && $invoiceDetails['service_roundoff_price'] > 0) {

			insertingInvoiceCopy($invoiceId, $reason);
			$sqlUpdateInvoice = array();
			$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
													SET `invoice_mode`	   	 		 	 = ?,
														`internet_handling_percentage`	 = ?, 
														`internet_handling_amount` 	  	 = ?, 
														`service_unit_price` 	 	 	 = ?, 
														`service_product_price` 		 = ?, 
														`service_total_price` 	 	 	 = ?, 
														`service_grand_price` 			 = ?, 
														`service_roundoff_price`	 	 = ?,";

			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'invoice_mode', 'DATA' => $invoiceDetailsArr['invoice_mode'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'internet_handling_percentage', 'DATA' => $invoiceDetailsArr['internet_handling_percentage'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'internet_handling_amount', 'DATA' => $invoiceDetailsArr['internet_handling_amount'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_unit_price', 'DATA' => $invoiceDetailsArr['service_unit_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_product_price', 'DATA' => $invoiceDetailsArr['service_product_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_total_price', 'DATA' => $invoiceDetailsArr['service_total_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_grand_price', 'DATA' => $invoiceDetailsArr['service_grand_price'], 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_roundoff_price', 'DATA' => $invoiceDetailsArr['service_roundoff_price'], 'TYP' => 's');
			if ($invoiceDetails['has_gst'] == 'Y') {
				$sqlUpdateInvoice['QUERY'] .= "`service_roundoff_price`  						 = ?,";

				$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'service_roundoff_price', 'DATA' => round($invoiceDetailsArr['service_roundoff_price']), 'TYP' => 's');
			}
			$sqlUpdateInvoice['QUERY'] .= "`payment_status` 				 = ?
														WHERE `id` = ?";
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
			$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['id'], 'TYP' => 's');
			$mycms->sql_update($sqlUpdateInvoice, false);



			if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														 SET `booking_mode`	   	 	 = ?,
															 `payment_status` 		 = ?
													   WHERE `refference_invoice_id` = ?
														 AND `refference_slip_id` 	 = ?";
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'refference_slip_id', 'DATA' => $slipId, 'TYP' => 's');
				$mycms->sql_update($sqlUpdateWorkshop, false);

				$sqlUpdateAcmdaton = array();
				$sqlUpdateAcmdaton['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														  SET `booking_mode`	   	 	 = ?,
															  `payment_status` 		 = ?
														WHERE `refference_invoice_id` = ?
														  AND `refference_slip_id` 	 = ?";
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');
				$sqlUpdateAcmdaton['PARAM'][] = array('FILD' => 'refference_slip_id', 'DATA' => $slipId, 'TYP' => 's');
				$mycms->sql_update($sqlUpdateAcmdaton, false);

				$sqlUpdateDelegate = array();
				$sqlUpdateDelegate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
														   SET `registration_mode`	  = ?,
															   `registration_payment_status` = ?
														 WHERE `id` = ?";
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['delegate_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateDelegate, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
				$sqlUpdateDelegate = array();
				$sqlUpdateDelegate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															 SET `registration_mode`	  = ?,
																 `registration_payment_status` = ?
														   WHERE `id` = ?";
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['delegate_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateDelegate, false);
			}
			if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$sqlUpdateDelegate = array();
				$sqlUpdateDelegate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
															 SET `registration_mode`	  = ?,
																 `registration_payment_status` = ?
														   WHERE `id` = ?";
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'registration_payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['delegate_id'], 'TYP' => 's');

				// $mycms->sql_update($sqlUpdateDelegate, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														 SET `booking_mode`	   	 	 = ?,
															 `payment_status` 		 = ?
													   WHERE `id` = ?";
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateWorkshop, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$sqlUpdate = array();
				$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														SET `booking_mode` = ?,
															`payment_status` = ?
													  WHERE `id` = ?";
				$sqlUpdate['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdate, false);
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
				$sqlUpdate = array();
				$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "
													SET `booking_mode` = ?,
														`payment_status` = ?
												  WHERE `id` = ?";
				$sqlUpdate['PARAM'][] = array('FILD' => 'booking_mode', 'DATA' => $registrationMode, 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
				$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $invoiceDetails['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdate, false);
			}
		}
	}
	echo json_encode(array('status' => 1, 'message' => 'Payment Mode Changed successfully.'));
	exit();
}

/////////////////////////////////	

function exhibitorRemainBal()
{
	global $cfg, $mycms;
	$exhibitorCode = $_REQUEST['exhibitorCode'];
	//	echo $barcode;
	header('Content-type: application/json');

	$sqlExhibitorCommit = array();
	$sqlExhibitorCommit['QUERY'] = "SELECT SUM(`committed_money`) AS total_commitment
											   FROM " . _DB_EXIBITOR_COMPANY_COMMITMENT_ . " 
											  WHERE `exhibitor_company_code` = '" . $exhibitorCode . "' ";

	$exhibitorCommit = $mycms->sql_select($sqlExhibitorCommit, false);

	$sqlExhibitorCommit = array();
	$sqlExhibitorPaidAmount['QUERY'] = "SELECT SUM(`amount`) AS paid_amount
												  FROM " . _DB_EXIBITOR_COMPANY_USERS_ . " 
												 WHERE `status` = 'A'
												   AND `exhibitor_company_code` = '" . $exhibitorCode . "' ";

	$exhibitorPaidAmount = $mycms->sql_select($sqlExhibitorPaidAmount, false);
	//_DB_EXIBITOR_COMPANY_COMMITMENT_


	$dataString = '{';

	$rowDetailsCommitment = $exhibitorCommit[0]['total_commitment'];
	$rowDtlsPaidAmount = $exhibitorPaidAmount[0]['paid_amount'];

	$remainAmount = $rowDetailsCommitment - $rowDtlsPaidAmount;
	if ($rowDetailsCommitment != "") {
		$dataString .= '"total_commitment": "' . $rowDetailsCommitment . '",';
		$dataString .= '"paid_amount": "' . $rowDtlsPaidAmount . '",';
		$dataString .= '"remain_amount": "' . $remainAmount . '"';
	} else if ($rowDetailsCommitment == "") {
		$dataString .= '"total_commitment": "0",';
		$dataString .= '"paid_amount": "0",';
		$dataString .= '"remain_amount": "0"';
	} else {
		$dataString .= '"current_amount": "No Commitment"';
	}
	$dataString .= '}';
	echo $dataString;
}

function onlinePaymentConfirmation()
{
	$delegateId = $_REQUEST['delegateId'];
	$slipId = $_REQUEST['slipId'];
	$paymentId = $_REQUEST['paymentId'];

	$slipDetails = slipDetails($slipId);
	$paymentDetails = paymentDetails($slipId)
		?>
	<form action="set_online_payment_dr.php" name="frmPaymentPopup" id="frmPaymentPopup" method="post"
		onsubmit="return onSubmitAction();">
		<input type="hidden" name="delegateId" id="delegateId" value="<?= $delegateId ?>" />
		<input type="hidden" name="slipId" id="slipId" value="<?= $slipId ?>" />
		<input type="hidden" name="paymentId" id="paymentId" value="<?= $paymentId ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td colspan="4" class="tcat">
					<span style="float:left">Payments</span>
					<span class="close" onclick="closeSetPaymentTermsPopup()">X</span>
				</td>
			</tr>
			<tr>
				<td width="20%" align="left">Slip No</td>
				<td width="30%" align="left"><?= $slipDetails['slip_number'] ?></td>
				<td width="20%" align="left">Slip Date</td>
				<td width="30%" align="left"><?= $slipDetails['slip_date'] ?></td>
			</tr>
			<tr>
				<td align="left">Slip Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
				<td align="left">Pending Amount</td>
				<td align="left"><?= $slipDetails['currency'] ?>
					<?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?>
				</td>
			</tr>
			<tr>
				<td align="left">Payment Mode</td>
				<td align="left"><?= $paymentDetails['payment_mode'] ?></td>
				<td align="left"></td>
				<td align="left"></td>
			</tr>

			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">

					<?
					if ($paymentDetails['payment_mode'] == "Cash") {
						?>
						<div id="cashPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Date of Deposit</td>
									<td width="30%" align="left"><?= $paymentDetails['cash_deposit_date'] ?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
							</table>
						</div>

						<?
					}

					if ($paymentDetails['payment_mode'] == "Cheque") {
						?>
						<div id="chequePaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Cheque No</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['cheque_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Cheque Date</td>
									<td align="left"><?= $paymentDetails['cheque_date'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "Draft") {
						?>
						<div id="draftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Draft No</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_number'] ?></td>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['draft_bank_name'] ?></td>
								</tr>
								<tr>
									<td align="left">Draft Date</td>
									<td align="left"><?= $paymentDetails['draft_date	'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "NEFT") {
						?>
						<div id="neftPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_bank_name'] ?></td>
									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['neft_date'] ?></td>

								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['neft_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					if ($paymentDetails['payment_mode'] == "RTGS") {
						?>
						<div id="rtgsPaymentDiv">
							<table width="100%" class="noborder">
								<tr>
									<td width="20%" align="left">Drawee Bank</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_bank_name'] ?></td>
									<td width="20%" align="left">Date</td>
									<td width="30%" align="left"><?= $paymentDetails['rtgs_date'] ?></td>
								</tr>
								<tr>
									<td align="left">Transaction Id</td>
									<td align="left"><?= $paymentDetails['rtgs_transaction_no'] ?></td>
									<td align="left"></td>
									<td align="left"></td>
								</tr>
							</table>
						</div>
						<?
					}
					?>
				</td>
			</tr>
			<tr>
				<td align="left">Amount</td>
				<td align="left"><?= number_format(invoiceAmountOfSlip($slipDetails['id']), 2) ?></td>
				<td align="left">Payment Date</td>
				<td align="left">
					<input type="date" name="payment_date" id="payment_date" style="width:90%;"
						value="<?= date('Y-m-d') ?>" />
				</td>
			</tr>
			<tr>
				<td align="left">Client Code</td>
				<td align="left"><input type="text" name="payment_date" id="payment_date" style="width:90%;" value=""
						required /></td>
				<td align="left">Atom Txn ID</td>
				<td align="left">
					<input type="text" name="mmp_txn" id="mmp_txn" style="width:90%;" value="" required />
				</td>
			</tr>
			<tr>
				<td align="left">Merchant Txn ID</td>
				<td align="left">
					<input type="text" name="mer_txn" id="mer_txn" style="width:90%;" value="" required />
				</td>
				<td align="left">Txn Date</td>
				<td align="left">
					<input type="date" name="txn_date" id="txn_date" style="width:90%;" rel="" value="" required />
				</td>
			</tr>
			<tr>
				<td align="left">Bank Name</td>
				<td align="left">
					<input type="text" name="bank_name" id="bank_name" style="width:90%;" value="" />
				</td>
				<td align="left">Bank Ref No</td>
				<td align="left">
					<input type="text" name="bank_txn" id="bank_txn" style="width:90%;" value="" />
				</td>
			</tr>
			<tr>
				<td align="left">Discriminator</td>
				<td align="left">
					<input type="text" name="discriminator" id="discriminator" style="width:90%;" value="" />
				</td>
				<td align="left">Card Number</td>
				<td align="left">
					<input type="text" name="CardNumber" id="CardNumber" style="width:90%;" value="" />
				</td>
			</tr>
			<tr>

				<td align="left">Remarks</td>
				<td align="left">
					<textarea name="remarks" id="remarks" style="width: 90%; height: 70%; resize:none;"></textarea>
				</td>
				<td align="left"></td>
				<td align="left"></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" align="right">


					<input type="submit" name="btnPayment" id="btnPayment" value="Make Payment"
						class="btn btn-small btn-red" />


				</td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
		</table>
	</form>
	<?
}

function updatingAccompanyDetails($accompanyDetailsArray, $date = '')
{
	global $cfg, $mycms;
	if ($date == '') {
		$date = date('Y-m-d');
	}
	$acmpnyReqId = array();

	foreach ($accompanyDetailsArray as $key => $accompanyDetails) {
		$sqlFetchId = array();
		$sqlFetchId['QUERY'] = "SELECT id 
										FROM " . _DB_USER_REGISTRATION_ . "  
										WHERE  `registration_request` = 'BLANK'
										AND `status` = 'I'
										ORDER BY id ASC
										LIMIT 1";

		$resultFetchId = $mycms->sql_select($sqlFetchId);
		$rowFetchId = $resultFetchId[0];

		$sqlUpdateAccompany = array();
		$sqlUpdateAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
											 SET `refference_delegate_id`		  = '" . $accompanyDetails['refference_delegate_id'] . "',
												 `user_type`					  = 'ACCOMPANY',
												 `user_full_name` 				  = '" . $accompanyDetails['user_full_name'] . "',
												 `user_age`						  = '" . $accompanyDetails['user_age'] . "',
												 `user_food_preference`			  = '" . $accompanyDetails['user_food_preference'] . "',
												 `isRegistration` 				  = '" . $accompanyDetails['isRegistration'] . "',
												 `isConference` 				  = '" . $accompanyDetails['isConference'] . "',
												 `registration_classification_id` = '" . $accompanyDetails['registration_classification_id'] . "',
												 `registration_tariff_cutoff_id`  = '" . $accompanyDetails['registration_tariff_cutoff_id'] . "',
												 `registration_request`  		  = '" . $accompanyDetails['registration_request'] . "',
												 `operational_area`     		  = '" . $accompanyDetails['operational_area'] . "',
												 `registration_payment_status` 	  = '" . $accompanyDetails['registration_payment_status'] . "',
												 `registration_mode`     		  = '" . $accompanyDetails['registration_mode'] . "',
												 `account_status`		 		  = '" . $accompanyDetails['account_status'] . "',
												 `reg_type`						  = '" . $accompanyDetails['reg_type'] . "',
												 `accompany_relationship`		  = '" . $accompanyDetails['accompany_relationship'] . "',
												 `conf_reg_date`		 		  = '" . $date . "',
												 `created_dateTime`               = '" . date('Y-m-d H:i:s') . "',
												 `status` 						  = 'A'
												WHERE `id`='" . $rowFetchId['id'] . "'";


		$lastInsertedAccompanyId = $mycms->sql_update($sqlUpdateAccompany, false);
		//echo nl2br($sqlInsertAccompany);
		//die();
		$sql = array();
		$sql['QUERY'] = "SELECT *
										  FROM " . _DB_USER_REGISTRATION_ . "
										WHERE `id` = '" . $accompanyDetails['refference_delegate_id'] . "'";

		$result = $mycms->sql_select($sql);
		$rowUserID = $result[0];
		$user_registration_id = $rowUserID['user_registration_id'];

		$accompany_registration_id = $user_registration_id . "-" . number_pad($lastInsertedAccompanyId, 4);

		$sqlUpdateAccompany = array();
		$sqlUpdateAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
												SET `user_registration_id` = '" . $accompany_registration_id . "'
											WHERE `id` = '" . $lastInsertedAccompanyId . "'";

		$mycms->sql_update($sqlUpdateAccompany, false);
		$sqlUpdateUser = array();
		$sqlUpdateUser['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . " 
													SET `isAccompany` = 'Y'
												WHERE `id` = '" . $accompanyDetails['refference_delegate_id'] . "'";

		$mycms->sql_update($sqlUpdateUser, false);

		$acmpnyReqId[] = $rowFetchId['id'];
	}
	return $acmpnyReqId;
}

function downloadUserListExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	if ($_REQUEST['SHOW'] != 'HTML') {
		ini_set('max_execution_time', 9000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=UserList" . time() . ".xls");
	}

	$condition = "";
	if (isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'PAID') {
		$condition = "AND ( delegate.registration_payment_status != 'UNPAID')";
		//  OR ( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online'))";
	}

	if (isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'UNPAID') {
		$condition = "AND ( ( delegate.registration_payment_status = 'UNPAID' AND !( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online')) 
									 OR ( delegate.registration_payment_status = 'UNPAID' AND payment.payment_mode IS NULL ) )";
	}

	$sqlDelegateQueryset['QUERY'] = "SELECT DISTINCT delegate.id
												   FROM ( SELECT inv.* 
												   		    FROM " . _DB_INVOICE_ . " inv
														   WHERE inv.status IN ('A','C')
														   	 AND (inv.service_type = 'DELEGATE_CONFERENCE_REGISTRATION' 
														          OR inv.service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION') 
														) invoice 
									
										  	 INNER JOIN ( SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE status IN ( 'A', 'C') ) AS delegate
												     ON delegate.id = invoice.delegate_id	
									
										     INNER JOIN ( SELECT MIN(id) AS id 
											 				FROM " . _DB_SLIP_ . "
														GROUP BY delegate_id ) AS slip
												     ON slip.id = invoice.slip_id										  
												  
									    LEFT OUTER JOIN ( SELECT MAX(payment_date) as payment_date, slip_id, created_dateTime, payment_mode
															FROM " . _DB_PAYMENT_ . " 
														   WHERE status = 'A'
														GROUP BY slip_id) AS payment
												     ON payment.slip_id = slip.id
													 										 
											      WHERE delegate.user_type = 'DELEGATE'
												    AND delegate.status IN ( 'A', 'C') 
												    AND delegate.operational_area !='EXHIBITOR'
												    AND delegate.isRegistration = 'Y'
												    AND delegate.registration_request != 'GUEST'
													" . $condition . "
											   ORDER BY  (CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
														      WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
													     END) DESC,
														payment.created_dateTime DESC, 
													    DATE(delegate.id) DESC limit 0,700";

	$resultFetchUser = $mycms->sql_select($sqlDelegateQueryset);

	/*delegate.conf_reg_date ASC, delegate.id ASC,
													   (CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
														  WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
													 END) DESC,
													IFNULL(payment.created_dateTime, IFNULL(invoice.created_dateTime,delegate.created_dateTime)) ASC*/

	$idArr = array();
	if ($resultFetchUser) {
		foreach ($resultFetchUser as $i => $rowFetchUser) {
			$idArr[] = $rowFetchUser['id'];
		}
	}

	if ($idArr) {
		/*foreach($idArr as $i=>$id) 
				{

					$sqlFetchInvoice                = getInvoiceWithCancelInvoiceDetails("",$id,""," AND inv.status  = 'A'");																	
					$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
					foreach($resultFetchInvoice as $key=>$rowInvoice)
					{

						if($rowInvoice['service_type']=="ACCOMPANY_CONFERENCE_REGISTRATION")
							{

								$RegData['ACCOMPANY_DATA']['SERVICE'][$id]	= getServiceTypeString($rowInvoice['delegate_id'],$rowInvoice['refference_id'],"ACCOMPANY");
							}

					}
				}*/

		//echo '<pre>'; print_r($RegData['ACCOMPANY_DATA']['SERVICE']);
		?>
		<table border="1" width="100%">
			<thead>
				<tr style="font-weight:bold;">
					<td width="40" align="center">Sl No</td>
					<td align="left">Name</td>

					<td align="left">Gender</td>
					<td align="left">Mobile</td>
					<td align="left">Email</td>
					<? if (false) { ?>
						<td align="left" width='500'>Address</td>
						<td align="left">City</td>
					<? } ?>
					<td align="left">State</td>
					<? if (false) { ?>
						<td align="left">Postal Code</td>
						<td align="left">Country</td>
					<? } ?>
					<td align="left">Applied On</td>
					<td align="left">Completed On</td>
					<td align="left">Registered as</td>
					<td align="left">Registration ID</td>
					<td align="left">Unique Sequence</td>
					<td align="left" width='500'>Cutoff</td>
					<td align="left">Registration Type</td>
					<td align="left">Tags</td>
					<td align="left">Roles</td>
					<td align="left">Operated From</td>
					<td align="left" width='500'>Workshop</td>
					<td align="left" width='500'>Post Congress Workshop</td>

					<td align="left" width='300'>Sharing Preference</td>

					<td align="left">Accompany</td>
					<td align="left" width='150'>Inaugural Dinner</td>
					<td align="left" width='150'>Gala Dinner</td>
					<td align="left">Registration Price</td>
					<td align="left">Workshop Price</td>
					<td align="left">Accompany Price</td>
					<td align="left">Dinner Price</td>
					<td align="left">Registration Payment Status</td>
					<td align="left">Total Accommodation Price</td>
					<td align="left">Total Base Price</td>
					<!-- <td align="left">Total GST Price</td> -->
					<td align="left">Total Internet Handling Price</td>
					<td align="left">Discount Amount</td>
					<td align="left">Overall Bill Amount</td>

					<td align="left">Overall Paid Amount</td>
					<td align="left">Overall Due Amount</td>



					<td align="left">Notes</td>
					<td align="left">Payment Remarks</td>

					<td align="left">Payment Details</td>
				</tr>
			</thead>
			<?
			foreach ($idArr as $i => $id) {
				$status = true;
				$rowFetchUser = getUserDetails($id);

				$classificationDetails = getRegClsfDetails($rowFetchUser['registration_classification_id']);

				//$accommodationsDetails = accommodationsDetailsofDelegate($id);
				$counter = $counter + 1;
				$color = "#FFFFFF";
				if ($rowFetchUser['account_status'] == "UNREGISTERED") {
					$color = "#FFCCCC";
					$status = false;
				}

				$array = $rowFetchUser['tags'];


				$var = (explode(",", $array));

				$totalAccompanyCount = 0;

				$financialSummary = getFinancialSummaryOfDelegate($id);

				$communicationSummary = getDelegateCallRecords($id, 'REGISTRATION');

				$communicationSummary['NOTES'] = $rowFetchUser['user_food_preference_in_details'];

				if ($_REQUEST['SHOW'] == 'HTML') {
					echo '<pre>>>';
					print_r($financialSummary);
					echo '</pre>';

					//echo '<pre>'; print_r($classificationDetails); echo '</pre>';
				}

				$services = array();

				$sqlFetchInvoice = getInvoiceWithCancelInvoiceDetails("", $id, "", " AND inv.status  = 'A'");
				$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);
				// echo '<pre>'; print_r($resultFetchInvoice);
	
				//echo '<pre>'; print_r($resultFetchInvoice); echo '</pre>';
	
				$RegData = array();

				// COMMON DINNER
				$RegData['DINNER_DATA']['SERVICE'][] = strtoupper('Inaugural Dinner') . ' on September 5, 2019';
				$RegData['DINNER_DATA']['INAUGRAL_DINNER'] = 'YES';
				$RegData['DINNER_DATA']['GALA_DINNER'] = 'NO';

				if ($resultFetchInvoice) {
					$totalBaseAmnt = 0;
					$registrationPrice = 0;
					$workshopPrice = 0;
					$accompanyPrice = 0;
					$dinnerPrice = 0;

					$totalGstPrc = 0;
					$totalInternetPrc = 0;
					$totalAccommodationPrc = 0;
					$accompanyPriceArr = array();
					foreach ($resultFetchInvoice as $key => $rowInvoice) {

						//echo '<pre>'; print_r($rowInvoice);
						if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
							$totalBaseAmnt += $rowInvoice['service_product_price'];

							$registrationPrice = $rowInvoice['service_product_price'];

							$subGstPrc = $rowInvoice['cgst_price'] + $rowInvoice['sgst_price'];

							$totalGstPrc = $totalGstPrc + $subGstPrc;

							$totalInternetPrc += $rowInvoice['internet_handling_amount'];

							$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
							if ($userDetails['registration_request'] == 'EXHIBITOR') {
								$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
								$RegData['REGISTRATION_DATA']['SERVICE'] = "Exhibitor Representative";
							} else {
								$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
								$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "CONFERENCE");
							}
						}

						if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
							$totalBaseAmnt += $rowInvoice['service_product_price'];
							$subGstPrc = $rowInvoice['cgst_price'] + $rowInvoice['sgst_price'];

							$totalGstPrc = $totalGstPrc + $subGstPrc;

							$totalInternetPrc += $rowInvoice['internet_handling_amount'];


							$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
							$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
							$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "RESIDENTIAL");
							$RegData['ACCOMODATION_DETAILS'] = accmodation_details($rowInvoice['delegate_id']);
							$RegData['DINNER_DATA'][$rowInvoice['id']]['DETAILS'] = getDinnerDetailsOfDelegate($rowInvoice['delegate_id']);
							$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
						}

						if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
							$totalBaseAmnt += $rowInvoice['service_product_price'];
							$workshopPrice = $rowInvoice['service_product_price'];
							$subGstPrc = $rowInvoice['cgst_price'] + $rowInvoice['sgst_price'];

							$totalGstPrc = $totalGstPrc + $subGstPrc;

							$totalInternetPrc += $rowInvoice['internet_handling_amount'];

							$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
							$Wstatus = true;
							$Wcounter++;
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails'] = getWorkshopDetails($rowInvoice['refference_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name'] = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date'] = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA'] = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] = $RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']['type'];

							$RegData['WORKSHOP_DATA']['SERVICE'][$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE']][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "WORKSHOP");
						}

						if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
							$totalBaseAmnt += $rowInvoice['service_product_price'];
							$accompanyPrice = $rowInvoice['service_product_price'];

							$subGstPrc = $rowInvoice['cgst_price'] + $rowInvoice['sgst_price'];

							$totalGstPrc = $totalGstPrc + $subGstPrc;

							$totalInternetPrc += $rowInvoice['internet_handling_amount'];


							array_push($accompanyPriceArr, $rowInvoice['service_product_price']);
							$acmponyStatus = true;
							$totalAccompanyCount++;

							$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
							$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails'] = getUserDetails($rowInvoice['refference_id']);
							$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name'] = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
							$RegData['ACCOMPANY_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "ACCOMPANY");
						}

						if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
							$dinnerPrice += $rowInvoice['service_product_price'];
							$totalBaseAmnt += $rowInvoice['service_product_price'];

							$subGstPrc = $rowInvoice['cgst_price'] + $rowInvoice['sgst_price'];

							$totalGstPrc = $totalGstPrc + $subGstPrc;

							$totalInternetPrc += $rowInvoice['internet_handling_amount'];

							$Dstatus = true;
							$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
							$Dcounter++;
							$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
							$RegData['DINNER_DATA']['GALA_DINNER'] = 'YES';
						}

						if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
							$totalBaseAmnt += $rowInvoice['service_product_price'];
							$totalAccommodationPrc = +$rowInvoice['service_product_price'];




							$subGstPrc = $rowInvoice['cgst_price'] + $rowInvoice['sgst_price'];

							$totalGstPrc = $totalGstPrc + $subGstPrc;
						}
						$discountAmount = $rowInvoice['discount_amount'];
					}
				}

				if (trim($financialSummary['REG_LAST_PAYMENT_ON']) == '') {
					if (strtoupper($rowFetchUser['registration_payment_status']) == 'COMPLIMENTARY') {
						$financialSummary['REG_LAST_PAYMENT_ON'] = $rowFetchUser['conf_reg_date'];
					}
				}

				//echo '<pre>'; print_r($RegData['WORKSHOP_DATA']['SERVICE']);
				//echo $totalBaseAmnt;
				//echo '<pre>'; print_r($accompanyPriceArr); echo '<pre>'; 
	
				//echo $rowFetchUser['user_first_name'].'=<br>'.$rowInvoice['service_type'];
				?>
				<tr class="tlisting" bgcolor="<?= $color ?>">
					<td align="center" valign="top"><?= $counter ?></td>
					<td align="left" valign="top">
						<?= strtoupper($rowFetchUser['user_first_name'] . ' ' . $rowFetchUser['user_middle_name'] . ' ' . $rowFetchUser['user_last_name']) ?>
					</td>

					<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_gender']) ?></td>

					<td align="left" valign="top">
						<?= $rowFetchUser['user_mobile_isd_code'] . ' ' . formatMobileNo($rowFetchUser['user_mobile_no']) ?>
					</td>
					<td align="left" valign="top"><?= $rowFetchUser['user_email_id'] ?></td>
					<? if (false) { ?>
						<td align="left" valign="top"><?= $rowFetchUser['user_address'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['user_city'] ?></td>
					<? } ?>
					<td align="left" valign="top"><?= $rowFetchUser['state_name'] ?></td>
					<? if (false) { ?>
						<td align="left" valign="top"><?= $rowFetchUser['user_pincode'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['country_name'] ?></td>
					<? } ?>
					<td align="left" valign="top"><?= $mycms->cDate('Y-m-d', $rowFetchUser['conf_reg_date']) ?></td>
					<td align="left" valign="top">
						<?= ((isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'PAID')) ? $mycms->cDate('Y-m-d', $financialSummary['REG_LAST_PAYMENT_ON']) : '' ?>
					</td>
					<td align="left" valign="top">
						<?php
						/*if($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID'])
									{
										echo "DELEGATE";
									}
									else
									{
										echo getRegClsfName($rowFetchUser['registration_classification_id']);
									}*/

						echo getRegClsfName($rowFetchUser['registration_classification_id']);
						?>

					</td>
					<td align="left" valign="top">
						<?php
						if (
							$rowFetchUser['registration_payment_status'] == "PAID"
							|| $rowFetchUser['registration_payment_status'] == "COMPLEMENTARY"
							|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
							|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
						) {
							echo $rowFetchUser['user_registration_id'];
						}
						?>
					</td>
					<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
					<td align="left" valign="top">
						<?php
						/*if($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID'])
									{
										echo getRegClsfName($rowFetchUser['registration_classification_id']);
									}
									else
									{
										echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']); 
									}*/

						echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
						?>
					</td>
					<td align="left" valign="top"><?= $rowFetchUser['registration_mode'] ?></td>
					<td align="left" valign="top"><?= $array ?></td>
					<td align="left" valign="top"><?= $rowFetchUser['roles'] ?></td>
					<td align="left" valign="top"><?= $rowFetchUser['reg_type'] ?></td>


					<td align="left" valign="top"><?= implode('; ', $RegData['WORKSHOP_DATA']['SERVICE']['WORKSHOP']) ?></td>
					<td align="left" valign="top"><?= implode('; ', $RegData['WORKSHOP_DATA']['SERVICE']['POST-CONFERENCE']) ?></td>

					<td align="left" valign="top">
						<?
						if ($isShared && $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] != '') {
							echo $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] . '
									[email : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['EMAIL'] . ']
									[mobile : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['MOBILE'] . ']';
						}
						?>
					</td>
					<td align="left" valign="top">
						<?php
						if (count($RegData['ACCOMPANY_DATA']['SERVICE']) > 0) {

							?>
							<table width="100%">
								<tbody>
									<?php
									$i = 1;
									foreach ($RegData['ACCOMPANY_DATA']['SERVICE'] as $k => $val) {
										?>
										<tr>
											<td>Accomapny <?= $i ?></td>
											<td><?= $val ?></td>
										</tr>
										<?php
										$i++;
									}
									?>

								</tbody>
							</table>
							<?php
						}
						?>
					</td>
					<td align="left" valign="top"><?= $RegData['DINNER_DATA']['INAUGRAL_DINNER'] ?></td>
					<td align="left" valign="top">
						<?
						if ($classificationDetails['type'] == 'COMBO') {
							echo 'YES';
						} else {
							echo $RegData['DINNER_DATA']['GALA_DINNER'];
						}
						?>
					</td>



					<td align="left" valign="top"><?= floatval($registrationPrice); ?></td>
					<td align="left" valign="top"><?= floatval($workshopPrice); ?></td>
					<td align="left" valign="top">
						<?php
						if (count($accompanyPriceArr) > 0) {
							?>
							<table width="100%">
								<tbody>
									<?php
									$i = 1;
									foreach ($accompanyPriceArr as $k => $val) {
										?>
										<tr>

											<td><?= floatval($val) ?></td>
										</tr>
										<?php
										$i++;
									}
									?>

								</tbody>
							</table>
							<?php
						} else {
							echo '0';
						}

						$cgst = $cfg['INT.CGST'];
						$sgst = $cfg['INT.SGST'];
						$gstArray = gstCalculation($cgst, $sgst, $totalInternetPrc);
						?>
					</td>
					<td align="left" valign="top"><?= floatval($dinnerPrice) ?></td>

					<td align="left" valign="top"><?= $rowFetchUser['registration_payment_status'] ?></td>
					<td align="left" valign="top"><?= floatval($totalAccommodationPrc); ?></td>
					<td align="left" valign="top"><?= floatval($totalBaseAmnt); ?></td>

					<!-- <td align="left" valign="top"><?= floatval($totalGstPrc); ?></td> -->
					<td align="left" valign="top"><?= number_format($gstArray['GST.PRICE'], 2); ?></td>
					<td align="right" valign="top"><?= $discountAmount ?></td>
					<td align="right" valign="top"><?= number_format($financialSummary['TOTAL'], 2) ?></td>

					<td align="right" valign="top"><?= number_format($financialSummary['PAID'], 2) ?></td>

					<td align="right" valign="top">
						<?= number_format(floatval($financialSummary['TOTAL']) - floatval($financialSummary['PAID']), 2) ?>
					</td>

					<td align="left" valign="top"
						style=" <?= (trim($communicationSummary['NOTES']) != '' ? 'background:#0099FF;' : '') ?>">
						<?= ($communicationSummary['LAST_MESSAGE'] != '') ? ($communicationSummary['LAST_MESSAGE'] . ',') : '' ?>
						<?= $communicationSummary['NOTES'] ?>
					</td>

					<?php
					$remark = "";
					$sql_remark = array();
					$sql_remark['QUERY'] = "SELECT * FROM " . _DB_PAYMENT_ . " 
									WHERE `delegate_id`='" . $id . "'";
					$results = $mycms->sql_select($sql_remark);
					foreach ($results as $key => $rows) {
						if ($rows['remarks'] != "") {
							$remark .= $rows['remarks'] . ". ";
						}
						if ($rows['payment_remark'] != "") {
							$remark .= $rows['payment_remark'] . ". ";
						}
					}

					?>
					<td align="left" valign="top" style=""><?= trim($remark) ?></td>

					<?
					foreach ($financialSummary['SLIP'] as $slipId => $slipData) {
						foreach ($slipData['PAYMENTS']['RAW']['paymentDetails'] as $k => $rowPayment) {
							$paymentDescription = "";
							if ($rowPayment['payment_mode'] == "Cash") {
								$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>" . setDateTimeFormat2($rowPayment['cash_deposit_date'], "D") . "</b>.";
							}
							if ($rowPayment['payment_mode'] == "Online") {
								if (!empty($rowPayment['razorpay_transaction_id'])) {
									$transaction_id = $rowPayment['razorpay_transaction_id'];
								}
								else{
                                     $transaction_id = $rowPayment['atom_atom_transaction_id'];
								}
								$paymentDescription = "Paid by <b>Online</b>. Date of Payment: <b>" . setDateTimeFormat2($rowPayment['payment_date'], "D") . "</b>.
															Transaction Number: <b>" . $transaction_id . "</b>.
															Bank Transaction Number: <b>" . $rowPayment['atom_bank_transaction_id'] . "</b>.";
							}
							if ($rowPayment['payment_mode'] == "Card") {
								$paymentDescription = "Paid by <b>Card</b>. Reference Number: <b>" . $rowPayment['card_transaction_no'] . "</b>.
															Date of Payment: <b>" . setDateTimeFormat2($rowPayment['card_payment_date'], "D") . "</b>.
															Remarks: <b>" . $rowPayment['payment_remark'] . "</b> ";
							}
							if ($rowPayment['payment_mode'] == "Draft") {
								$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>" . $rowPayment['draft_number'] . "</b>.
														   Draft Date: <b>" . setDateTimeFormat2($rowPayment['draft_date'], "D") . "</b>.
														   Draft Drawee Bank: <b>" . $rowPayment['draft_bank_name'] . "</b>.";
							}
							if ($rowPayment['payment_mode'] == "NEFT") {
								$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>" . $rowPayment['neft_transaction_no'] . "</b>.
														   Transaction Date: <b>" . setDateTimeFormat2($rowPayment['neft_date'], "D") . "</b>.
														   Transaction Bank: <b>" . $rowPayment['neft_bank_name'] . "</b>.";
							}
							if ($rowPayment['payment_mode'] == "RTGS") {
								$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>" . $rowPayment['rtgs_transaction_no'] . "</b>.
														   Transaction Date: <b>" . setDateTimeFormat2($rowPayment['rtgs_date'], "D") . "</b>.
														   Transaction Bank: <b>" . $rowPayment['rtgs_bank_name'] . "</b>.";
							}
							if ($rowPayment['payment_mode'] == "Cheque") {
								$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>" . $rowPayment['cheque_number'] . "</b>.
														   Cheque Date: <b>" . setDateTimeFormat2($rowPayment['cheque_date'], "D") . "</b>.
														   Cheque Drawee Bank: <b>" . $rowPayment['cheque_bank_name'] . "</b>.";
							}
							if ($rowPayment['payment_mode'] == "UPI") {
								$paymentDescription = "Paid by <b>UPI</b>. Transaction Number: <b>" . $rowPayment['txn_no'] .
									"</b> UPI Date: <b>" . $rowPayment['upi_date'] . "</b>";
							}
							?>
							<td align="left"><?= $paymentDescription ?></td>
							<?
						}
					}
					?>
				</tr>
				<?php
			}
			?>
		</table>
		<?
	}
}

function downloadUserTotPlusListExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');


	?>
	<table border="1">
		<thead>
			<tr style="font-weight:bold;">
				<td width="40" align="center">Sl No</td>
				<td align="left">Name</td>
				<td align="left">Ref.</td>
				<td align="left">Gender</td>
				<td align="left">Mobile</td>
				<td align="left">Email</td>
				<td align="left">Registered as</td>
				<td align="left">Registration ID</td>
				<td align="left">Unique Sequence</td>
				<td align="left" width='500'>Cutoff</td>
				<td align="left">Registration Type</td>
			</tr>
		</thead>
		<?
		foreach ($idArr as $i => $id) {
			$status = true;
			$rowFetchUser = getUserDetails($id);

			$classificationDetails = getRegClsfDetails($rowFetchUser['registration_classification_id']);

			//$accommodationsDetails = accommodationsDetailsofDelegate($id);
			$counter = $counter + 1;
			$color = "#FFFFFF";
			if ($rowFetchUser['account_status'] == "UNREGISTERED") {
				$color = "#FFCCCC";
				$status = false;
			}

			$totalAccompanyCount = 0;

			$financialSummary = getFinancialSummaryOfDelegate($id);

			$communicationSummary = getDelegateCallRecords($id, 'REGISTRATION');

			$communicationSummary['NOTES'] = $rowFetchUser['user_food_preference_in_details'];

			if ($_REQUEST['SHOW'] == 'HTML') {
				echo '<pre>>>';
				print_r($financialSummary);
				echo '</pre>';

				//echo '<pre>'; print_r($classificationDetails); echo '</pre>';
			}

			$services = array();

			$sqlFetchInvoice = getInvoiceWithCancelInvoiceDetails("", $id, "", " AND inv.status  = 'A'");
			$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);

			//echo '<pre>'; print_r($rowFetchUser); echo '</pre>';
	
			$RegData = array();

			// COMMON DINNER
			$RegData['DINNER_DATA']['SERVICE'][] = strtoupper('Inaugural Dinner') . ' on September 5, 2019';
			$RegData['DINNER_DATA']['INAUGRAL_DINNER'] = 'YES';
			$RegData['DINNER_DATA']['GALA_DINNER'] = 'NO';

			if ($resultFetchInvoice) {
				foreach ($resultFetchInvoice as $key => $rowInvoice) {
					if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
						$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
						if ($userDetails['registration_request'] == 'EXHIBITOR') {
							$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
							$RegData['REGISTRATION_DATA']['SERVICE'] = "Exhibitor Representative";
						} else {
							$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
							$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "CONFERENCE");
						}
					}

					if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
						$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
						$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
						$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "RESIDENTIAL");
						$RegData['ACCOMODATION_DETAILS'] = accmodation_details($rowInvoice['delegate_id']);
						$RegData['DINNER_DATA'][$rowInvoice['id']]['DETAILS'] = getDinnerDetailsOfDelegate($rowInvoice['delegate_id']);
						$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
					}

					if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
						$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
						$Wstatus = true;
						$Wcounter++;
						$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails'] = getWorkshopDetails($rowInvoice['refference_id']);
						$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name'] = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
						$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date'] = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
						$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA'] = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
						$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] = $RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']['type'];

						$RegData['WORKSHOP_DATA']['SERVICE'][$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE']][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "WORKSHOP");
					}

					if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {

						$acmponyStatus = true;
						$totalAccompanyCount++;

						$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
						$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails'] = getUserDetails($rowInvoice['refference_id']);
						$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name'] = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
						$RegData['ACCOMPANY_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "ACCOMPANY");
					}

					if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
						$Dstatus = true;
						$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
						$Dcounter++;
						$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
						$RegData['DINNER_DATA']['GALA_DINNER'] = 'YES';
					}

					if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
						// todo
					}
				}
			}

			if (trim($financialSummary['REG_LAST_PAYMENT_ON']) == '') {
				if (strtoupper($rowFetchUser['registration_payment_status']) == 'COMPLIMENTARY') {
					$financialSummary['REG_LAST_PAYMENT_ON'] = $rowFetchUser['conf_reg_date'];
				}
			}

			//echo '<pre>'; print_r($RegData); echo '<pre>';
			?>
			<tr class="tlisting" bgcolor="<?= $color ?>">
				<td align="center" valign="top"><?= $counter ?></td>
				<td align="left" valign="top">
					<?= strtoupper($rowFetchUser['user_first_name'] . ' ' . $rowFetchUser['user_middle_name'] . ' ' . $rowFetchUser['user_last_name']) ?>
				</td>
				<td align="left" valign="top">
					<?= ($rowFetchUser['participation_type'] != 'OTHERS') ? $rowFetchUser['participation_type'] : "" ?>
				</td>
				<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_gender']) ?></td>

				<td align="left" valign="top">
					<?= $rowFetchUser['user_mobile_isd_code'] . ' ' . formatMobileNo($rowFetchUser['user_mobile_no']) ?>
				</td>
				<td align="left" valign="top"><?= $rowFetchUser['user_email_id'] ?></td>
				<? if (false) { ?>
					<td align="left" valign="top"><?= $rowFetchUser['user_address'] ?></td>
					<td align="left" valign="top"><?= $rowFetchUser['user_city'] ?></td>
				<? } ?>
				<td align="left" valign="top"><?= $rowFetchUser['state_name'] ?></td>
				<? if (false) { ?>
					<td align="left" valign="top"><?= $rowFetchUser['user_pincode'] ?></td>
					<td align="left" valign="top"><?= $rowFetchUser['country_name'] ?></td>
				<? } ?>
				<td align="left" valign="top"><?= $mycms->cDate('Y-m-d', $rowFetchUser['conf_reg_date']) ?></td>
				<td align="left" valign="top">
					<?= ((isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'PAID')) ? $mycms->cDate('Y-m-d', $financialSummary['REG_LAST_PAYMENT_ON']) : '' ?>
				</td>
				<td align="left" valign="top">
					<?php
					if ($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID']) {
						echo "DELEGATE";
					} else {
						echo getRegClsfName($rowFetchUser['registration_classification_id']);
					}
					?>

				</td>
				<td align="left" valign="top">
					<?php
					if (
						$rowFetchUser['registration_payment_status'] == "PAID"
						|| $rowFetchUser['registration_payment_status'] == "COMPLEMENTARY"
						|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
						|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
					) {
						echo $rowFetchUser['user_registration_id'];
					}
					?>
				</td>
				<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
				<td align="left" valign="top">
					<?php
					if ($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID']) {
						echo getRegClsfName($rowFetchUser['registration_classification_id']);
					} else {
						echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
					}
					?>
				</td>
				<td align="left" valign="top"><?= $rowFetchUser['registration_mode'] ?></td>
				<td align="left" valign="top"><?= $rowFetchUser['reg_type'] ?></td>
				<td align="left" valign="top"><?= implode('; ', $RegData['WORKSHOP_DATA']['SERVICE']['MASTER CLASS']) ?></td>
				<td align="left" valign="top"><?= implode('; ', $RegData['WORKSHOP_DATA']['SERVICE']['POST-CONFERENCE']) ?></td>
				<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] ?></td>
				<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['CHECKIN_DATE'] ?></td>
				<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['CHECKOUT_DATE'] ?></td>

				<td align="left" valign="top">
					<?php
					$isShared = false;

					if ($classificationDetails['type'] == 'COMBO') {
						if (in_array($rowFetchUser['registration_classification_id'], $cfg['RESIDENTIAL_SHARING_CLASF_ID'])) {
							echo "SHARED";
							$isShared = true;
						} elseif ($rowFetchUser['registration_classification_id'] != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
							echo "INDIVIDUAL";
						}
					}
					?>
				</td>
				<td align="left" valign="top">
					<?
					if ($isShared && $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] != '') {
						echo $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] . '
							[email : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['EMAIL'] . ']
							[mobile : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['MOBILE'] . ']';
					}
					?>
				</td>

				<td align="left" valign="top"><?= implode('; ', $RegData['ACCOMPANY_DATA']['SERVICE']) ?></td>

				<td align="left" valign="top"><?= $RegData['DINNER_DATA']['INAUGRAL_DINNER'] ?></td>
				<td align="left" valign="top">
					<?
					if ($classificationDetails['type'] == 'COMBO') {
						echo 'YES';
					} else {
						echo $RegData['DINNER_DATA']['GALA_DINNER'];
					}
					?>
				</td>

				<td align="left" valign="top"><?= $rowFetchUser['registration_payment_status'] ?></td>
				<td align="right" valign="top"><?= number_format($financialSummary['TOTAL'], 2) ?></td>
				<td align="right" valign="top"><?= number_format($financialSummary['PAID'], 2) ?></td>

				<td align="right" valign="top">
					<?= number_format(floatval($financialSummary['TOTAL']) - floatval($financialSummary['PAID']), 2) ?>
				</td>
				<td align="left" valign="top">
					<?= ($financialSummary['SETTLED_AMOUNT'] > 0) ? number_format($financialSummary['SETTLED_AMOUNT'], 2) : '' ?>
				</td>
				<td align="left" valign="top">
					<?= ($financialSummary['SETTLED_AMOUNT'] > 0) ? $financialSummary['SETTLEMENT_DATE'] : '' ?>
				</td>
				<td align="left" valign="top"
					style=" <?= (trim($communicationSummary['NOTES']) != '' ? 'background:#0099FF;' : '') ?>">
					<?= ($communicationSummary['LAST_MESSAGE'] != '') ? ($communicationSummary['LAST_MESSAGE'] . ',') : '' ?>
					<?= $communicationSummary['NOTES'] ?>
				</td>
				<?
				foreach ($financialSummary['SLIP'] as $slipId => $slipData) {
					foreach ($slipData['PAYMENTS']['RAW']['paymentDetails'] as $k => $rowPayment) {
						$paymentDescription = "";
						if ($rowPayment['payment_mode'] == "Cash") {
							$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>" . setDateTimeFormat2($rowPayment['cash_deposit_date'], "D") . "</b>.";
						}
						if ($rowPayment['payment_mode'] == "Online") {
							$paymentDescription = "Paid by <b>Online</b>. Date of Payment: <b>" . setDateTimeFormat2($rowPayment['payment_date'], "D") . "</b>.
															Transaction Number: <b>" . $rowPayment['atom_atom_transaction_id'] . "</b>.
															Bank Transaction Number: <b>" . $rowPayment['atom_bank_transaction_id'] . "</b>.";
						}
						if ($rowPayment['payment_mode'] == "Card") {
							$paymentDescription = "Paid by <b>Card</b>. Reference Number: <b>" . $rowPayment['card_transaction_no'] . "</b>.
															Date of Payment: <b>" . setDateTimeFormat2($rowPayment['card_payment_date'], "D") . "</b>.
															Remarks: <b>" . $rowPayment['payment_remark'] . "</b> ";
						}
						if ($rowPayment['payment_mode'] == "Draft") {
							$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>" . $rowPayment['draft_number'] . "</b>.
														   Draft Date: <b>" . setDateTimeFormat2($rowPayment['draft_date'], "D") . "</b>.
														   Draft Drawee Bank: <b>" . $rowPayment['draft_bank_name'] . "</b>.";
						}
						if ($rowPayment['payment_mode'] == "NEFT") {
							$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>" . $rowPayment['neft_transaction_no'] . "</b>.
														   Transaction Date: <b>" . setDateTimeFormat2($rowPayment['neft_date'], "D") . "</b>.
														   Transaction Bank: <b>" . $rowPayment['neft_bank_name'] . "</b>.";
						}
						if ($rowPayment['payment_mode'] == "RTGS") {
							$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>" . $rowPayment['rtgs_transaction_no'] . "</b>.
														   Transaction Date: <b>" . setDateTimeFormat2($rowPayment['rtgs_date'], "D") . "</b>.
														   Transaction Bank: <b>" . $rowPayment['rtgs_bank_name'] . "</b>.";
						}
						if ($rowPayment['payment_mode'] == "Cheque") {
							$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>" . $rowPayment['cheque_number'] . "</b>.
														   Cheque Date: <b>" . setDateTimeFormat2($rowPayment['cheque_date'], "D") . "</b>.
														   Cheque Drawee Bank: <b>" . $rowPayment['cheque_bank_name'] . "</b>.";
						}
						?>
						<td align="left"><?= $paymentDescription ?></td>
						<?
					}
				}
				?>
			</tr>
			<?php
		}
		?>
	</table>
	<?

}

function downloadUserListAccommodationExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	if ($_REQUEST['SHOW'] != 'HTML') {
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=UserList" . time() . ".xls");
	}
	//,'C'
	$sqlDelegateQueryset['QUERY'] = "SELECT DISTINCT delegate.id, delegate.registration_classification_id
										   FROM ( SELECT * 
										   		    FROM " . _DB_INVOICE_ . " 
												   WHERE status IN ('A')
												   	 AND service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION'
												) invoice 
							
								  	 INNER JOIN " . _DB_USER_REGISTRATION_ . " AS delegate
										     ON delegate.id = invoice.delegate_id	
											AND delegate.registration_classification_id != '" . $cfg['INAUGURAL_OFFER_CLASF_ID'] . "'
									
									 INNER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS clsf
										     ON delegate.registration_classification_id = clsf.id
							
								     INNER JOIN " . _DB_SLIP_ . " AS slip
										     ON slip.id = invoice.slip_id										  
										  
							    LEFT OUTER JOIN ( SELECT MAX(payment_date) as payment_date, slip_id, created_dateTime, payment_mode
													FROM " . _DB_PAYMENT_ . " 
												   WHERE status = 'A'
												 GROUP BY slip_id) AS payment
										     ON payment.slip_id = slip.id
											 										 
									      WHERE delegate.user_type = 'DELEGATE'
										    AND delegate.status IN ( 'A', 'C') 
										    AND delegate.operational_area !='EXHIBITOR'
										    AND delegate.isRegistration = 'Y'
										    AND delegate.registration_request != 'GUEST'
											" . $condition . "
									   ORDER BY clsf.residential_hotel_id ASC, clsf.classification_title ASC,
									   			(CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
												      WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
											     END) DESC,
												payment.created_dateTime DESC, 
											    DATE(delegate.id) DESC";
	$resultFetchUser = $mycms->sql_select($sqlDelegateQueryset);

	$idArrs = array();
	if ($resultFetchUser) {
		foreach ($resultFetchUser as $i => $rowFetchUser) {
			$classificationDetails = getRegClsfDetails($rowFetchUser['registration_classification_id']);
			$idArrs[$classificationDetails['residential_hotel_id']][$rowFetchUser['registration_classification_id']][] = $rowFetchUser['id'];
		}
	}

	$excelData = array();

	if ($idArrs) {
		?>
		<table border="1">
			<thead>
				<tr style="font-weight:bold;">
					<td width="40" align="center">Sl No</td>
					<td align="left">Name</td>
					<td align="left">Gender</td>
					<td align="left">Mobile</td>
					<td align="left">Email</td>
					<td align="left">Applied On</td>
					<td align="left">Completed On</td>
					<td align="left">Registered as</td>
					<td align="left">Registration ID</td>
					<td align="left">Unique Sequence</td>
					<td align="left">Registration Type</td>
					<td align="left" width='400'>Hotel Name</td>
					<td align="left" width='100'>Checkin Date</td>
					<td align="left" width='100'>Checkout Date</td>
					<td align="left" width='100'>Accommodation Mode</td>
					<td align="left" width='300'>Sharing Preference</td>
					<td align="left" width='500'>Accompany</td>
				</tr>
			</thead>
			<?
			foreach ($idArrs as $i1 => $id1s) {
				$theHotelName = '';
				foreach ($id1s as $i2 => $idArr) {
					foreach ($idArr as $i => $id) {
						$status = true;
						$rowFetchUser = getUserDetails($id);

						$classificationDetails = getRegClsfDetails($rowFetchUser['registration_classification_id']);

						//$accommodationsDetails = accommodationsDetailsofDelegate($id);
						$counter = $counter + 1;
						$color = "#FFFFFF";
						if ($rowFetchUser['account_status'] == "UNREGISTERED") {
							$color = "#FFCCCC";
							$status = false;
						}

						$totalAccompanyCount = 0;

						$financialSummary = getFinancialSummaryOfDelegate($id);

						$communicationSummary = getDelegateCallRecords($id, 'REGISTRATION');

						if ($_REQUEST['SHOW'] == 'HTML') {
							//echo '<pre>>>'; print_r($communicationSummary); echo '</pre>';
	
							//echo '<pre>'; print_r($classificationDetails); echo '</pre>';
						}

						$services = array();

						$sqlFetchInvoice = getInvoiceWithCancelInvoiceDetails("", $id, "", " AND inv.status  = 'A'");
						$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);

						//echo '<pre>'; print_r($rowFetchUser); echo '</pre>';
	
						$RegData = array();

						// COMMON DINNER
						$RegData['DINNER_DATA']['SERVICE'][] = strtoupper('Inaugural Dinner') . ' on September 5, 2019';
						$RegData['DINNER_DATA']['INAUGRAL_DINNER'] = 'YES';
						$RegData['DINNER_DATA']['GALA_DINNER'] = 'NO';

						if ($resultFetchInvoice) {
							foreach ($resultFetchInvoice as $key => $rowInvoice) {
								if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
									$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
									if ($userDetails['registration_request'] == 'EXHIBITOR') {
										$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
										$RegData['REGISTRATION_DATA']['SERVICE'] = "Exhibitor Representative";
									} else {
										$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
										$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "CONFERENCE");
									}
								}


								if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
									$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
									$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
									$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "RESIDENTIAL");
									$RegData['ACCOMODATION_DETAILS'] = accmodation_details($rowInvoice['delegate_id']);
									$RegData['DINNER_DATA'][$rowInvoice['id']]['DETAILS'] = getDinnerDetailsOfDelegate($rowInvoice['delegate_id']);
									$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
								}

								if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
									$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
									$Wstatus = true;
									$Wcounter++;
									$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails'] = getWorkshopDetails($rowInvoice['refference_id']);
									$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name'] = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
									$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date'] = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
									$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA'] = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
									$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] = $RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']['type'];

									$RegData['WORKSHOP_DATA']['SERVICE'][$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE']][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "WORKSHOP");
								}

								if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {

									$acmponyStatus = true;
									$totalAccompanyCount++;

									$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
									$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails'] = getUserDetails($rowInvoice['refference_id']);
									$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name'] = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
									$RegData['ACCOMPANY_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "ACCOMPANY");
								}

								if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
									$Dstatus = true;
									$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
									$Dcounter++;
									$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
									$RegData['DINNER_DATA']['GALA_DINNER'] = 'YES';
								}

								if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
									// todo
								}
							}
						}

						$isShared = false;
						if ($classificationDetails['type'] == 'COMBO') {
							if (in_array($rowFetchUser['registration_classification_id'], $cfg['RESIDENTIAL_SHARING_CLASF_ID'])) {
								$sharedStatus = "SHARED";
								$isShared = true;
							} elseif ($rowFetchUser['registration_classification_id'] != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
								$sharedStatus = "INDIVIDUAL";
							}
						}

						if ($theHotelName != $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] . ' - ' . $sharedStatus) {
							$theHotelName = $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] . ' - ' . $sharedStatus;
							?>
							<tr style="font-weight:bold;">
								<td colspan="17">&nbsp;</td>
							</tr>
							<tr style="font-weight:bold;">
								<td colspan="17"><b><?= $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] . ' - ' . $sharedStatus ?></b></td>
							</tr>
							<?
						}

						//echo '<pre>'; print_r($RegData); echo '<pre>';
						?>
						<tr class="tlisting" bgcolor="<?= $color ?>">
							<td align="center" valign="top"><?= $counter ?></td>
							<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_full_name']) ?></td>
							<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_gender']) ?></td>

							<td align="left" valign="top">
								<?= $rowFetchUser['user_mobile_isd_code'] . ' ' . formatMobileNo($rowFetchUser['user_mobile_no']) ?>
							</td>
							<td align="left" valign="top"><?= $rowFetchUser['user_email_id'] ?></td>

							<td align="left" valign="top"><?= $mycms->cDate('Y-m-d', $rowFetchUser['conf_reg_date']) ?></td>
							<td align="left" valign="top"><?= $financialSummary['REG_LAST_PAYMENT_ON'] ?></td>
							<td align="left" valign="top">
								<?php
								if ($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID']) {
									echo "DELEGATE";
								} else {
									echo getRegClsfName($rowFetchUser['registration_classification_id']);
								}
								?>

							</td>
							<td align="left" valign="top">
								<?php
								if (
									$rowFetchUser['registration_payment_status'] == "PAID"
									|| $rowFetchUser['registration_payment_status'] == "COMPLEMENTARY"
									|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
									|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
								) {
									echo $rowFetchUser['user_registration_id'];
								}
								?>
							</td>
							<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
							<td align="left" valign="top"><?= $rowFetchUser['reg_type'] ?></td>
							<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] ?></td>
							<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['CHECKIN_DATE'] ?></td>
							<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['CHECKOUT_DATE'] ?></td>

							<td align="left" valign="top"><?= $sharedStatus ?></td>
							<td align="left" valign="top">
								<?
								if ($isShared && $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] != '') {
									echo $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] . '
									[email : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['EMAIL'] . ']
									[mobile : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['MOBILE'] . ']';
								}
								?>
							</td>
						</tr>
						<?php
					}
				}
			}
			?>
		</table>
		<?
	}
}

function downloadUserListSummaryExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	if ($_REQUEST['SHOW'] != 'HTML') {
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=UserList" . time() . ".xls");
	}

	$condition = "";

	$sqlDelegateQueryset['QUERY'] = "SELECT DISTINCT delegate.id
										   FROM ( SELECT inv.* 
										   		    FROM " . _DB_INVOICE_ . " inv
												   WHERE inv.status IN ('A')
												   	 AND (inv.service_type = 'DELEGATE_CONFERENCE_REGISTRATION' 
												          OR inv.service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION') 
												) invoice 
							
								  	 INNER JOIN ( SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE status IN ( 'A') ) AS delegate
										     ON delegate.id = invoice.delegate_id	
										  
							    LEFT OUTER JOIN ( SELECT MAX(payment_date) as payment_date, slip_id, created_dateTime, payment_mode
													FROM " . _DB_PAYMENT_ . " 
												   WHERE status = 'A'
												GROUP BY slip_id) AS payment
										     ON payment.slip_id = invoice.slip_id
											 										 
									      WHERE delegate.user_type = 'DELEGATE'
										    AND delegate.status IN ( 'A') 
										    AND delegate.operational_area !='EXHIBITOR'
										    AND delegate.isRegistration = 'Y'
										    AND delegate.registration_request != 'GUEST'
									   ORDER BY IFNULL(payment.created_dateTime, IFNULL(invoice.created_dateTime,delegate.created_dateTime)) ASC,
									   			delegate.conf_reg_date ASC, delegate.id ASC,
									   			(CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
												      WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
											     END) DESC";
	if ($_REQUEST['SHOW'] == 'HTML') {
		//echo '<pre>'; print_r($sqlDelegateQueryset); echo '</pre>';
	}
	$resultFetchUser = $mycms->sql_select($sqlDelegateQueryset);

	$idArr = array();
	if ($resultFetchUser) {
		foreach ($resultFetchUser as $i => $rowFetchUser) {
			$idArr[] = $rowFetchUser['id'];
		}
	}

	if ($_REQUEST['SHOW'] == 'HTML') {
		//echo '<pre>'; print_r($idArr); echo '</pre>';
	}


	$collection = array();

	$totalUsers = 0;

	$delegates = 0;

	$itc2nShared = 0;
	$marriot2nShared = 0;
	$vistel2nShared = 0;

	$itc2nIndividual = 0;
	$marriot2nIndividual = 0;
	$vistel2nIndividual = 0;

	$itc3nShared = 0;
	$marriot3nShared = 0;
	$vistel3nShared = 0;

	$itc3nIndividual = 0;
	$marriot3nIndividual = 0;
	$vistel3nIndividual = 0;

	$totalShared = 0;
	$totalIndividual = 0;

	$masterClasses = array();
	$workshops = array();

	$galaDinner = 0;
	$accompany = 0;

	if ($idArr) {
		$workshopDetailsArray = getAllWorkshopTariffs();

		foreach ($workshopDetailsArray as $keyWorkshopclsf => $rowWorkshopclsf) {
			foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {
				if ($rowRegClasf[1]['WORKSHOP_TYPE'] != 'POST-CONFERENCE' && $keyWorkshopclsf != 5) {
					$masterClasses[$keyWorkshopclsf]['NAME'] = getWorkshopName($keyWorkshopclsf);
					$masterClasses[$keyWorkshopclsf]['COUNT'] = 0;
				}
				if ($rowRegClasf[1]['WORKSHOP_TYPE'] == 'POST-CONFERENCE') {
					$workshops[$keyWorkshopclsf]['NAME'] = getWorkshopName($keyWorkshopclsf);
					$workshops[$keyWorkshopclsf]['COUNT'] = 0;
				}
			}
		}

		foreach ($idArr as $i => $id) {
			ini_set('max_execution_time', 1000);
			$status = true;
			$rowFetchUser = getUserDetails($id);

			$classificationDetails = getRegClsfDetails($rowFetchUser['registration_classification_id']);

			//$accommodationsDetails = accommodationsDetailsofDelegate($id);
			$counter = $counter + 1;
			$color = "#FFFFFF";

			$totalAccompanyCount = 0;

			//$financialSummary 		= getFinancialSummaryOfDelegate($id);

			//$communicationSummary 	= getDelegateCallRecords($id,'REGISTRATION');

			if ($_REQUEST['SHOW'] == 'HTML') {
				//echo '<pre>>>'; print_r($communicationSummary); echo '</pre>';

				//echo '<pre>'; print_r($classificationDetails); echo '</pre>';
			}

			$services = array();

			$sqlFetchInvoice = getInvoiceWithCancelInvoiceDetails("", $id, "", " AND inv.status  = 'A'");
			$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);

			$RegData = array();

			// COMMON DINNER
			$RegData['DINNER_DATA']['SERVICE'][] = strtoupper('Inaugural Dinner') . ' on September 5, 2019';
			$RegData['DINNER_DATA']['INAUGRAL_DINNER'] = 'YES';
			$RegData['DINNER_DATA']['GALA_DINNER'] = 'NO';


			if ($resultFetchInvoice) {
				foreach ($resultFetchInvoice as $key => $rowInvoice) {
					if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
						$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
						if ($userDetails['registration_request'] == 'EXHIBITOR') {
							$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
							$RegData['REGISTRATION_DATA']['SERVICE'] = "Exhibitor Representative";
						} else {
							$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
							$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "CONFERENCE");
						}

						$collection['DELEGATE'][] = $id;

						$delegates++;
						$totalUsers++;
					}

					if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
						$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
						$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
						$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "RESIDENTIAL");
						$RegData['ACCOMODATION_DETAILS'] = accmodation_details($rowInvoice['delegate_id']);
						$RegData['DINNER_DATA'][$rowInvoice['id']]['DETAILS'] = getDinnerDetailsOfDelegate($rowInvoice['delegate_id']);
						$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");

						switch ($rowFetchUser['registration_classification_id']) {
							case 1:
							case 3:
							case 4:
							case 5:
							case 6:
								$collection['DELEGATE'][] = $id;
								$delegates++;
								break;
							case 7:
								$collection['HOTEL']['ITC-2-IND'][] = $id;
								$itc2nIndividual++;
								$totalIndividual++;
								break;
							case 8:
								$collection['HOTEL']['ITC-3-IND'][] = $id;
								$itc3nIndividual++;
								$totalIndividual++;
								break;
							case 9:
								$collection['HOTEL']['ITC-2-SHR'][] = $id;
								$itc2nShared++;
								$totalShared++;
								break;
							case 10:
								$collection['HOTEL']['ITC-3-SHR'][] = $id;
								$itc3nShared++;
								$totalShared++;
								break;
							case 11:
								$collection['HOTEL']['VISI-2-IND'][] = $id;
								$vistel2nIndividual++;
								$totalIndividual++;
								break;
							case 12:
								$collection['HOTEL']['VISI-3-IND'][] = $id;
								$vistel3nIndividual++;
								$totalIndividual++;
								break;
							case 13:
								$collection['HOTEL']['VISI-2-SHR'][] = $id;
								$vistel2nShared++;
								$totalShared++;
								break;
							case 14:
								$collection['HOTEL']['VISI-3-SHR'][] = $id;
								$vistel3nShared++;
								$totalShared++;
								break;

							case 15:
								$collection['HOTEL']['MARI-2-IND'][] = $id;
								$marriot2nIndividual++;
								$totalIndividual++;
								break;
							case 16:
								$collection['HOTEL']['MARI-3-IND'][] = $id;
								$marriot3nIndividual++;
								$totalIndividual++;
								break;
							case 17:
								$collection['HOTEL']['MARI-2-SHR'][] = $id;
								$marriot2nShared++;
								$totalShared++;
								break;
							case 14:
								$collection['HOTEL']['MARI-3-SHR'][] = $id;
								$marriot3nShared++;
								$totalShared++;
								break;
						}

						$totalUsers++;
					}

					if (false && $_REQUEST['SHOW'] != 'HTML') {
						if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION" && $rowInvoice['payment_status'] != 'UNPAID') {
							$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
							$Wstatus = true;
							$Wcounter++;
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails'] = getWorkshopDetails($rowInvoice['refference_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name'] = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date'] = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA'] = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
							$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] = $RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']['type'];

							$RegData['WORKSHOP_DATA']['SERVICE'][$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE']][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "WORKSHOP");

							if ($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] != 'POST-CONFERENCE' && $RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id'] != 5) {
								$masterClasses[$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']]['COUNT'] = $masterClasses[$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']]['COUNT'] + 1;
							} elseif ($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] == 'POST-CONFERENCE') {
								$workshops[$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']]['COUNT'] = $workshops[$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']]['COUNT'] + 1;
							}

							$collection['WORKSHOP'][$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name']][] = $id;
						}
					}

					if (false && $_REQUEST['SHOW'] != 'HTML') {
						if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
							$accompanyDetails = getUserDetails($rowInvoice['refference_id']);

							if ($accompanyDetails['registration_request'] == 'GENERAL') {
								$acmponyStatus = true;
								$totalAccompanyCount++;

								$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
								$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails'] = getUserDetails($rowInvoice['refference_id']);
								$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name'] = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
								$RegData['ACCOMPANY_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "ACCOMPANY");

								$collection['ACCOMPANY'][$id][] = $rowInvoice['refference_id'];
							}
						}
					}

					if (false && $_REQUEST['SHOW'] != 'HTML') {
						if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
							$Dstatus = true;
							$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
							$Dcounter++;

							$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
							$RegData['DINNER_DATA']['GALA_DINNER'] = 'YES';

							$collection['GALA-DINNER'][] = $id;

							$galaDinner++;
						}
					}

					if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
						// todo
					}

					if (true || $_REQUEST['SHOW'] == 'HTML') {
						//$masterClasses = array();
						//$workshops = array();						
						$workshopDetailArr = getWorkshopDetailsOfDelegate($id, true);
						foreach ($workshopDetailArr as $key => $workshopDetail) {
							if (
								!in_array($id, $workshopCheck[$workshopDetail['workshop_id']])
								&& (($workshopDetail['INVOICE_DETAILS']['invoice_mode'] == 'ONLINE' && $workshopDetail['INVOICE_DETAILS']['payment_status'] == 'PAID')
									|| $workshopDetail['INVOICE_DETAILS']['invoice_mode'] == 'OFFLINE')
							) {
								$datta['WORKSHOP'][$masterClasses[$workshopDetail['workshop_id']]['NAME']][] = $id;
								$workshopCheck[$workshopDetail['workshop_id']][] = $id;
								if ($workshopDetail['type'] != 'POST-CONFERENCE' && $workshopDetail['workshop_id'] != 5) {
									$masterClasses[$workshopDetail['workshop_id']]['COUNT'] = $masterClasses[$workshopDetail['workshop_id']]['COUNT'] + 1;
								} elseif ($workshopDetail['type'] == 'POST-CONFERENCE') {
									$workshops[$workshopDetail['workshop_id']]['COUNT'] = $workshops[$workshopDetail['workshop_id']]['COUNT'] + 1;
								}
							}
						}

						$dinnerDetailArr = getDinnerOrderedDetailsOfDelegate($id, true);
						foreach ($dinnerDetailArr as $key => $dinnerDetail) {
							if (
								!in_array($dinnerDetail['refference_id'], $dinnerCheck[$dinnerDetail['package_id']])
								&& (($dinnerDetail['INVOICE_DETAILS']['invoice_mode'] == 'ONLINE' && $dinnerDetail['INVOICE_DETAILS']['payment_status'] == 'PAID')
									|| $dinnerDetail['INVOICE_DETAILS']['invoice_mode'] == 'OFFLINE')
							) {
								if ($dinnerDetail['package_id'] == '2') {
									$datta['DINNER'][$dinnerDetail['package_id']][] = $dinnerDetail['refference_id'];

									$dinnerCheck[$dinnerDetail['package_id']][] = $dinnerDetail['refference_id'];

									$collection['GALA-DINNER'][] = $dinnerDetail['refference_id'];
									$galaDinner++;
								}
							}
						}

						$accompanyDetailArr = getAcompanyDetailsOfDelegate($id, true);
						foreach ($accompanyDetailArr as $key => $accompanyDetail) {
							if (
								!in_array($accompanyDetail['id'], $accompanyCheck[$accompanyDetail['refference_delegate_id']])
								&& (($accompanyDetail['INVOICE_DETAILS']['invoice_mode'] == 'ONLINE' && $accompanyDetail['INVOICE_DETAILS']['payment_status'] == 'PAID')
									|| $accompanyDetail['INVOICE_DETAILS']['invoice_mode'] == 'OFFLINE')
							) {
								if ($dinnerDetail['package_id'] == '2' && $accompanyDetail['registration_request'] == 'GENERAL') {
									$datta['ACCOMPANY'][$accompanyDetail['refference_delegate_id']][] = $accompanyDetail['id'];

									$accompanyCheck[$accompanyDetail['refference_delegate_id']][] = $accompanyDetail['id'];

									$collection['ACCOMPANY'][] = $accompanyDetail['id'];
									$accompany++;
								}
							}
						}
					}
				}
			}
		}
	}

	$sqlGalaDinnerQueryset = array();
	$sqlGalaDinnerQueryset['QUERY'] = "SELECT COUNT(*) countDinner
												FROM " . _DB_USER_REGISTRATION_ . " 
											   WHERE status IN ('A')
												 AND id IN (SELECT refference_id 
									 						  FROM " . _DB_REQUEST_DINNER_ . " 
															 WHERE package_id='2' 
															   AND status = 'A' 
															   AND refference_invoice_id IN (SELECT id FROM " . _DB_INVOICE_ . " WHERE service_type IN ('DELEGATE_DINNER_REQUEST','DELEGATE_RESIDENTIAL_REGISTRATION','DELEGATE_CONFERENCE_REGISTRATION') AND status ='A' AND payment_status != 'UNPAID' ))
												
											ORDER BY id";
	$resultGalaDinner = $mycms->sql_select($sqlGalaDinnerQueryset);
	$galaDinnerCount = $resultGalaDinner[0]['countDinner'];

	$wkSpArray = array();
	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT * 
									   FROM " . _DB_WORKSHOP_CLASSIFICATION_ . "
									  WHERE status = 'A'
									    AND id != '5'";
	$resultWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	foreach ($resultWorkshopclsf as $i => $rowWorkshopclsf) {
		$sqlwrkueryset['QUERY'] = "SELECT COUNT(*) countWrk
											FROM " . _DB_USER_REGISTRATION_ . " 
										   WHERE status IN ('A')
											 AND id IN (SELECT delegate_id 
														  FROM " . _DB_REQUEST_WORKSHOP_ . " 
														 WHERE workshop_id='" . $rowWorkshopclsf['id'] . "' 
														   AND status = 'A' 
														   AND id IN (SELECT refference_id FROM " . _DB_INVOICE_ . " WHERE service_type IN('DELEGATE_WORKSHOP_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION','DELEGATE_CONFERENCE_REGISTRATION') AND status ='A' AND payment_status != 'UNPAID' ))
												ORDER BY id";
		$resultWrk = $mycms->sql_select($sqlwrkueryset);
		$WorkShopCountCount = $resultWrk[0]['countWrk'];

		$wkSpArray[$rowWorkshopclsf['type']][$rowWorkshopclsf['id']]['NAME'] = $rowWorkshopclsf['classification_title'];
		$wkSpArray[$rowWorkshopclsf['type']][$rowWorkshopclsf['id']]['COUNT'] = $WorkShopCountCount;
	}
	$masterClasses = $wkSpArray['MASTER CLASS'];
	$workshops = $wkSpArray['POST-CONFERENCE'];

	if ($_REQUEST['SHOW'] == 'HTML') {

		echo '<h1>WORKSHOP</h1>';
		echo '<pre>';
		print_r($datta);
		echo '</pre>';

		//echo '<pre>'; print_r($collection); echo '</pre>';

		/*
		echo '<h1>SORTED DELEGATE</h1>';
		sort($collection['DELEGATE']);
		echo '<pre>'; print_r($collection['DELEGATE']); echo '</pre>';
		echo '<h1>SEARCH - DUPLICATE DELEGATE</h1>';
		foreach($collection['DELEGATE'] as $kk=>$DId)
		{
			foreach($collection['HOTEL'] as $HotelPack=>$Hdata)
			{
				if(in_array($DId,$Hdata))
				{
					echo '<pre>'.$HotelPack.'>>'.$DId.'</pre>';
				}
			}
		}
		*/

		//echo '<h1>ALL-TOG DELEGATE</h1>';
		$allTog = $collection['DELEGATE'];


		foreach ($collection['HOTEL'] as $HotelPack => $Hdata) {
			foreach ($Hdata as $cc => $idd) {
				$allTog[] = $idd;
			}
		}


		//sort($allTog);
		//echo '<pre>'; print_r($allTog); echo '</pre>';

		//echo '<h1>ALL-TOG NAMES</h1>';
		//			foreach($allTog as $fft=>$uId)
		//			{
		//				$userDetail = getUserDetails($uId,true);
		//				$invTypes	= getInvoicedServiceTypesOfDelegate($uId);
		//				//echo '<br/>'.$userDetail['user_full_name'].'['.$uId.']['.implode(', ',$invTypes).']';
		//				//echo '<br/>'.$uId;
		//			}

	}


	?>
	<table border="1">
		<tr>
			<td align="center" valign="top" colspan="10">CUMULATIVE REPORT</td>
		</tr>
		<tr>
			<td align="center" valign="top" colspan="10" style="color:#FF0000; font-weight:bold;">
				Total:<?= $delegates + $totalShared + $totalIndividual ?></td><? //$totalUsers
					  	?>
		</tr>
		<tr>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">Normal Registration</td>
			<td align="center" valign="top" colspan="4" style="background:#999999; font-weight:bold;">Residential Package
				Shared</td>
			<td align="center" valign="top" colspan="4" style="background:#999999; font-weight:bold;">Residential Package
				Individual</td>
		</tr>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top"></td>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" style="font-weight:bold;">ITC</td>
			<td align="center" valign="top" style="font-weight:bold;">Marriott</td>
			<td align="center" valign="top" style="font-weight:bold;">VISITEL</td>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" style="font-weight:bold;">ITC</td>
			<td align="center" valign="top" style="font-weight:bold;">Marriott</td>
			<td align="center" valign="top" style="font-weight:bold;">VISITEL</td>
		</tr>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">2N</td>
			<td align="center" valign="top"><?= $itc2nShared ?></td>
			<td align="center" valign="top"><?= $marriot2nShared ?></td>
			<td align="center" valign="top"><?= $vistel2nShared ?></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">2N</td>
			<td align="center" valign="top"><?= $itc2nIndividual ?></td>
			<td align="center" valign="top"><?= $marriot2nIndividual ?></td>
			<td align="center" valign="top"><?= $vistel2nIndividual ?></td>
		</tr>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top"><?= $delegates ?></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">3N</td>
			<td align="center" valign="top"><?= $itc3nShared ?></td>
			<td align="center" valign="top"><?= $marriot3nShared ?></td>
			<td align="center" valign="top"><?= $vistel3nShared ?></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">3N</td>
			<td align="center" valign="top"><?= $itc3nIndividual ?></td>
			<td align="center" valign="top"><?= $marriot3nIndividual ?></td>
			<td align="center" valign="top"><?= $vistel3nIndividual ?></td>
		</tr>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" style="color:#FF0000; font-weight:bold;"><?= $delegates ?></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" colspan="3" style="color:#FF0000; font-weight:bold;"><?= $totalShared ?></td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" colspan="3" style="color:#FF0000; font-weight:bold;"><?= $totalIndividual ?>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" colspan="10"></td>
		</tr>
		<tr>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" colspan="8" style="background:#999999; font-weight:bold;">Masterclass</td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">Paid</td>
		</tr>
		<?
		$cc = 1;
		$masterTotal = 0;
		foreach ($masterClasses as $keyy => $masterClass) {
			?>
			<tr>
				<td align="center" valign="top"><?= $cc++ ?></td>
				<td align="left" valign="top" colspan="8"><?= $masterClass['NAME'] ?></td>
				<td align="center" valign="top"><?= $masterClass['COUNT'] ?></td>
			</tr>
			<?
			$masterTotal += $masterClass['COUNT'];
		}
		?>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" colspan="8"></td>
			<td align="center" valign="top" style="font-weight:bold;"><?= $masterTotal ?></td>
		</tr>

		<tr>
			<td align="center" valign="top" colspan="10"></td>
		</tr>
		<tr>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" colspan="8" style="background:#999999; font-weight:bold;">Workshop</td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">Paid</td>
		</tr>
		<?
		$cc = 1;
		$workshopTotal = 0;
		foreach ($workshops as $keyy => $workshop) {
			?>
			<tr>
				<td align="center" valign="top"><?= $cc++ ?></td>
				<td align="left" valign="top" colspan="8"><?= $workshop['NAME'] ?></td>
				<td align="center" valign="top"><?= $workshop['COUNT'] ?></td>
			</tr>
			<?
			$workshopTotal += $workshop['COUNT'];
		}
		?>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" colspan="8"></td>
			<td align="center" valign="top" style="font-weight:bold;"><?= $workshopTotal ?></td>
		</tr>

		<tr>
			<td align="center" valign="top" colspan="10"></td>
		</tr>
		<tr>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" colspan="8" style="background:#999999; font-weight:bold;">Gala Dinner</td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">Paid</td>
		</tr>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" colspan="8">6th September </td>
			<td align="center" valign="top"><?= $galaDinnerCount ?></td><? //$galaDinner
			  	?>
		</tr>

		<tr>
			<td align="center" valign="top" colspan="10"></td>
		</tr>
		<tr>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;"></td>
			<td align="center" valign="top" colspan="8" style="background:#999999; font-weight:bold;">Accompany</td>
			<td align="center" valign="top" style="background:#999999; font-weight:bold;">Paid</td>
		</tr>
		<tr>
			<td align="center" valign="top"></td>
			<td align="center" valign="top" colspan="8"></td>
			<td align="center" valign="top"><?= $accompany ?></td>
		</tr>
	</table>
	<?
}

function downloadAccompanyListExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	if ($_REQUEST['SHOW'] != 'HTML') {
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=UserList" . time() . ".xls");
	}

	/*$condition	= "";
	if(isset($_REQUEST['FILTER']) && $_REQUEST['FILTER']=='PAID')
	{
		$condition = "AND ( delegate.registration_payment_status != 'UNPAID' OR ( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online'))";
	}

	if(isset($_REQUEST['FILTER']) && $_REQUEST['FILTER']=='UNPAID')
	{
		$condition = "AND ( ( delegate.registration_payment_status = 'UNPAID' AND !( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online')) 
						 OR ( delegate.registration_payment_status = 'UNPAID' AND payment.payment_mode IS NULL ) )";
	}*/

	$sqlDelegateQueryset = array();
	$sqlDelegateQueryset['QUERY'] = "SELECT id, refference_delegate_id, user_full_name, user_mobile_no, user_email_id,
													 user_registration_id, user_unique_sequence, registration_tariff_cutoff_id,
													 user_type, registration_request, operational_area, created_dateTime, status
												FROM " . _DB_USER_REGISTRATION_ . " 
											   WHERE status IN ('A','C')
											     AND user_type = 'ACCOMPANY' 
												 AND registration_request = 'GENERAL' 
												 AND id IN (SELECT refference_id FROM " . _DB_INVOICE_ . " WHERE service_type = 'ACCOMPANY_CONFERENCE_REGISTRATION' AND status ='A')
											ORDER BY id";

	$resultFetchUser = $mycms->sql_select($sqlDelegateQueryset);

	if ($resultFetchUser) {
		?>
		<table border="1">
			<thead>
				<tr style="font-weight:bold;">
					<td width="40" align="center" rowspan="2">Sl No</td>
					<td align="left" rowspan="2">Name</td>
					<td align="left" rowspan="2">Registration ID</td>
					<td align="left" rowspan="2">Has Dinner</td>
					<td align="left" colspan="7">Delegate</td>
				</tr>
				<tr style="font-weight:bold;">
					<td align="left">Name</td>
					<td align="left">Ref.</td>
					<td align="left">Country</td>
					<td align="left">State</td>
					<td align="left">Registration ID</td>
					<td align="left">Unique Sequence</td>
					<td align="left">Registration Type</td>
				</tr>
			</thead>
			<tbody>
				<?
				$sl = 1;
				foreach ($resultFetchUser as $i => $row) {
					$rowFetchUser = getUserDetails($row['refference_delegate_id']);
					?>
					<tr>
						<td align="center" valign="top"><?= $sl++ ?></td>
						<td align="left" valign="top"><?= strtoupper($row['user_full_name']) ?></td>
						<td align="left" valign="top"><?= $row['user_registration_id'] ?></td>
						<td align="left" valign="top">
							<?
							$sqlDinnerDetails = array();
							$sqlDinnerDetails['QUERY'] = "   SELECT dinnerReq.*,  
																	 dinner.dinner_classification_name, dinner.date AS dinnerDate
																FROM " . _DB_REQUEST_DINNER_ . " dinnerReq
														  INNER JOIN " . _DB_DINNER_CLASSIFICATION_ . " dinner
																  ON dinner.id = dinnerReq.package_id
															   WHERE dinnerReq.status = 'A'
																 AND dinnerReq.`refference_id` = '" . $row['id'] . "'
																 AND dinnerReq.refference_invoice_id IN (SELECT id FROM " . _DB_INVOICE_ . " WHERE service_type IN ('DELEGATE_CONFERENCE_REGISTRATION', 'DELEGATE_RESIDENTIAL_REGISTRATION', 'DELEGATE_DINNER_REQUEST') AND status ='A')";
							$resDinnerDetails = $mycms->sql_select($sqlDinnerDetails);

							if ($resDinnerDetails) {
								echo "YES";
							} else {
								echo "NO";
							}

							?>
						</td>
						<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_full_name']) ?></td>
						<td align="left" valign="top">
							<?= ($rowFetchUser['participation_type'] != 'OTHERS') ? $rowFetchUser['participation_type'] : "" ?>
						</td>
						<td align="left" valign="top"><?= $rowFetchUser['country_name'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['state_name'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['user_registration_id'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['user_unique_sequence'] ?></td>
						<td align="left" valign="top">
							<?php
							if ($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID']) {
								echo "DELEGATE";
							} else {
								echo getRegClsfName($rowFetchUser['registration_classification_id']);
							}
							?>
						</td>
					</tr>
					<?
				}
				?>
				<tr>
					<td align="left" valign="top" colspan="11">EXCEL DOWNLOADED ON <?= date("d/m/Y") ?></td>
				</tr>
			</tbody>
		</table>
		<?
	}
}

function downloadCountryWiseUserListExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	if ($_REQUEST['SHOW'] != 'HTML') {
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=UserList" . time() . ".xls");
	}

	$condition = "";
	/*if(isset($_REQUEST['FILTER']) && $_REQUEST['FILTER']=='PAID')
	{
		$condition = "AND ( delegate.registration_payment_status != 'UNPAID' OR ( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online'))";
	}

	if(isset($_REQUEST['FILTER']) && $_REQUEST['FILTER']=='UNPAID')
	{
		$condition = "AND ( ( delegate.registration_payment_status = 'UNPAID' AND !( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online')) 
						 OR ( delegate.registration_payment_status = 'UNPAID' AND payment.payment_mode IS NULL ) )";
	}*/

	$sqlDelegateQueryset['QUERY'] = "SELECT DISTINCT delegate.id, delegate.user_country_id, delegate.user_state_id
										   FROM ( SELECT inv.* 
										   		    FROM " . _DB_INVOICE_ . " inv
												   WHERE inv.status IN ('A','C')
												   	 AND (inv.service_type = 'DELEGATE_CONFERENCE_REGISTRATION' 
												          OR inv.service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION') 
												) invoice 
							
								  	 INNER JOIN ( SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE status IN ( 'A', 'C') ) AS delegate
										     ON delegate.id = invoice.delegate_id	
							
								     INNER JOIN ( SELECT MIN(id) AS id 
									 				FROM " . _DB_SLIP_ . "
												GROUP BY delegate_id ) AS slip
										     ON slip.id = invoice.slip_id										  
										  
							    LEFT OUTER JOIN ( SELECT MAX(payment_date) as payment_date, slip_id, created_dateTime, payment_mode
													FROM " . _DB_PAYMENT_ . " 
												   WHERE status = 'A'
												GROUP BY slip_id) AS payment
										     ON payment.slip_id = slip.id
											 										 
									      WHERE delegate.user_type = 'DELEGATE'
										    AND delegate.status IN ( 'A', 'C') 
										    AND delegate.operational_area !='EXHIBITOR'
										    AND delegate.isRegistration = 'Y'
										    AND delegate.registration_request != 'GUEST'
											" . $condition . "
									   ORDER BY  (CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
												      WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
											     END) DESC,
												payment.created_dateTime DESC, 
											    DATE(delegate.id) DESC";
	$resultFetchUser = $mycms->sql_select($sqlDelegateQueryset);

	/*delegate.conf_reg_date ASC, delegate.id ASC,
											   (CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
												  WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
											 END) DESC,
											IFNULL(payment.created_dateTime, IFNULL(invoice.created_dateTime,delegate.created_dateTime)) ASC*/

	$idArr = array();
	if ($resultFetchUser) {
		foreach ($resultFetchUser as $i => $rowFetchUser) {
			$idArr[$rowFetchUser['user_country_id']][$rowFetchUser['user_state_id']][] = $rowFetchUser['id'];
		}
	}

	$countryStateList = array();

	$countryStateList[0]['NAME'] = "Not Set";
	$countryStateList[0]['STATE'][0]['NAME'] = "Not Set";

	$sqlCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . "
											ORDER BY `country_name`";
	$resultCountry = $mycms->sql_select($sqlCountry);
	foreach ($resultCountry as $i => $rowFetchUserCountry) {
		$countryStateList[$rowFetchUserCountry['country_id']]['NAME'] = $rowFetchUserCountry['country_name'];
		$countryStateList[$rowFetchUserCountry['country_id']]['STATE'][0]['NAME'] = "Not Set";

		$sqlFetchState['QUERY'] = "SELECT * FROM " . _DB_COMN_STATE_ . "
										WHERE `country_id` = '" . $rowFetchUserCountry['country_id'] . "'
									 ORDER BY TRIM(state_name) ASC ";
		$resultState = $mycms->sql_select($sqlFetchState);

		foreach ($resultState as $keyState => $rowState) {
			$countryStateList[$rowFetchUserCountry['country_id']]['STATE'][$rowState['st_id']]['NAME'] = $rowState['state_name'];
		}
	}

	//print_r($countryStateList);

	if ($idArr) {

		?>
		<table border="1">
			<tr style="font-weight:bold;">
				<td colspan="2">Summary</td>
			</tr>
			<?
			foreach ($countryStateList as $ci => $rowFetchUserCountry) {
				$idAs = $idArr[$ci];
				if (sizeof($idAs) > 0) {
					?>
					<tr style="font-weight:bold;">
						<td colspan="2"><?= $rowFetchUserCountry['NAME'] ?></td>
					</tr>
					<?
					foreach ($rowFetchUserCountry['STATE'] as $si => $rowState) {
						$idA = $idAs[$si];
						if (sizeof($idA) > 0) {
							?>
							<tr>
								<td><?= $rowState['NAME'] ?></td>
								<td><?= sizeof($idA) ?></td>
							</tr>
							<?
						}
					}
				}
			}
			?>
			<tr>
				<td></td>
			</tr>
		</table>

		<table border="1">
			<thead>
				<tr style="font-weight:bold;">
					<td width="40" align="center">Sl No</td>
					<td align="left">Name</td>
					<td align="left">Ref.</td>
					<td align="left">Gender</td>
					<td align="left">Mobile</td>
					<td align="left">Email</td>
					<? if (false) { ?>
						<td align="left" width='500'>Address</td>
						<td align="left">City</td>
					<? } ?>
					<td align="left">State</td>
					<? if (false) { ?>
						<td align="left">Postal Code</td>
					<? } ?>
					<td align="left">Country</td>
					<td align="left">Applied On</td>
					<td align="left">Completed On</td>
					<td align="left">Registered as</td>
					<td align="left">Registration ID</td>
					<td align="left">Unique Sequence</td>
					<td align="left" width='500'>Cutoff</td>
					<td align="left">Registration Type</td>
					<td align="left">Operated From</td>
					<td align="left" width='500'>Masterclass</td>
					<td align="left" width='500'>Post Congress Workshop</td>
					<td align="left" width='400'>Hotel Name</td>
					<td align="left" width='100'>Checkin Date</td>
					<td align="left" width='100'>Checkout Date</td>

					<td align="left" width='100'>Accommodation Mode</td>
					<td align="left" width='300'>Sharing Preference</td>

					<td align="left" width='500'>Accompany</td>
					<td align="left" width='150'>Inaugural Dinner</td>
					<td align="left" width='150'>Gala Dinner</td>
					<td align="left">Registration Payment Status</td>
					<td align="left">Overall Bill Amount</td>
					<td align="left">Overall Paid Amount</td>
					<td align="left">Overall Due Amount</td>

					<td align="left">Total PG Settlement Amount</td>
					<td align="left">PG Settlement Date(s)</td>

					<td align="left">Notes</td>

					<td align="left">Payment Details</td>
				</tr>
			</thead>
			<?

			foreach ($countryStateList as $ci => $rowFetchUserCountry) {
				$idAs = $idArr[$ci];
				if (sizeof($idAs) > 0) {
					?>
					<tr style="font-weight:bold;">
						<td colspan="10"><?= $rowFetchUserCountry['NAME'] ?></td>
					</tr>
					<?
					foreach ($rowFetchUserCountry['STATE'] as $si => $rowState) {
						$idA = $idAs[$si];
						if (sizeof($idA) > 0) {
							?>
							<tr style="font-weight:bold;">
								<td colspan="10"><?= $rowState['NAME'] ?></td>
							</tr>
							<?
							foreach ($idA as $i => $id) {
								$status = true;
								$rowFetchUser = getUserDetails($id);

								$classificationDetails = getRegClsfDetails($rowFetchUser['registration_classification_id']);

								//$accommodationsDetails = accommodationsDetailsofDelegate($id);
								$counter = $counter + 1;
								$color = "#FFFFFF";
								if ($rowFetchUser['account_status'] == "UNREGISTERED") {
									$color = "#FFCCCC";
									$status = false;
								}

								$totalAccompanyCount = 0;

								$financialSummary = getFinancialSummaryOfDelegate($id);

								$communicationSummary = getDelegateCallRecords($id, 'REGISTRATION');

								$communicationSummary['NOTES'] = $rowFetchUser['user_food_preference_in_details'];

								if ($_REQUEST['SHOW'] == 'HTML') {
									echo '<pre>>>';
									print_r($financialSummary);
									echo '</pre>';

									//echo '<pre>'; print_r($classificationDetails); echo '</pre>';
								}

								$services = array();

								$sqlFetchInvoice = getInvoiceWithCancelInvoiceDetails("", $id, "", " AND inv.status  = 'A'");
								$resultFetchInvoice = $mycms->sql_select($sqlFetchInvoice);

								//echo '<pre>'; print_r($rowFetchUser); echo '</pre>';
	
								$RegData = array();

								// COMMON DINNER
								$RegData['DINNER_DATA']['SERVICE'][] = strtoupper('Inaugural Dinner') . ' on September 5, 2019';
								$RegData['DINNER_DATA']['INAUGRAL_DINNER'] = 'YES';
								$RegData['DINNER_DATA']['GALA_DINNER'] = 'NO';

								if ($resultFetchInvoice) {
									foreach ($resultFetchInvoice as $key => $rowInvoice) {
										if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
											$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
											if ($userDetails['registration_request'] == 'EXHIBITOR') {
												$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
												$RegData['REGISTRATION_DATA']['SERVICE'] = "Exhibitor Representative";
											} else {
												$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
												$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "CONFERENCE");
											}
										}

										if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
											$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
											$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($rowInvoice['delegate_id']));
											$RegData['REGISTRATION_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "RESIDENTIAL");
											$RegData['ACCOMODATION_DETAILS'] = accmodation_details($rowInvoice['delegate_id']);
											$RegData['DINNER_DATA'][$rowInvoice['id']]['DETAILS'] = getDinnerDetailsOfDelegate($rowInvoice['delegate_id']);
											$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
										}

										if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
											$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
											$Wstatus = true;
											$Wcounter++;
											$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails'] = getWorkshopDetails($rowInvoice['refference_id']);
											$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name'] = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
											$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date'] = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
											$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA'] = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
											$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE'] = $RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']['type'];

											$RegData['WORKSHOP_DATA']['SERVICE'][$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['TYPE']][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "WORKSHOP");
										}

										if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {

											$acmponyStatus = true;
											$totalAccompanyCount++;

											$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
											$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails'] = getUserDetails($rowInvoice['refference_id']);
											$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name'] = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
											$RegData['ACCOMPANY_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "ACCOMPANY");
										}

										if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
											$Dstatus = true;
											$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
											$Dcounter++;
											$RegData['DINNER_DATA']['SERVICE'][] = getServiceTypeString($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
											$RegData['DINNER_DATA']['GALA_DINNER'] = 'YES';
										}

										if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
											// todo
										}
									}
								}

								if (trim($financialSummary['REG_LAST_PAYMENT_ON']) == '') {
									if (strtoupper($rowFetchUser['registration_payment_status']) == 'COMPLIMENTARY') {
										$financialSummary['REG_LAST_PAYMENT_ON'] = $rowFetchUser['conf_reg_date'];
									}
								}
								//echo '<pre>'; print_r($RegData); echo '<pre>';
								?>
								<tr class="tlisting" bgcolor="<?= $color ?>">
									<td align="center" valign="top"><?= $counter ?></td>
									<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_full_name']) ?></td>
									<td align="left" valign="top">
										<?= ($rowFetchUser['participation_type'] != 'OTHERS') ? $rowFetchUser['participation_type'] : "" ?>
									</td>
									<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_gender']) ?></td>

									<td align="left" valign="top">
										<?= $rowFetchUser['user_mobile_isd_code'] . ' ' . formatMobileNo($rowFetchUser['user_mobile_no']) ?>
									</td>
									<td align="left" valign="top"><?= $rowFetchUser['user_email_id'] ?></td>
									<? if (false) { ?>
										<td align="left" valign="top"><?= $rowFetchUser['user_address'] ?></td>
										<td align="left" valign="top"><?= $rowFetchUser['user_city'] ?></td>
									<? } ?>
									<td align="left" valign="top"><?= $rowFetchUser['state_name'] ?></td>
									<? if (false) { ?>
										<td align="left" valign="top"><?= $rowFetchUser['user_pincode'] ?></td>
									<? } ?>
									<td align="left" valign="top"><?= $rowFetchUser['country_name'] ?></td>

									<td align="left" valign="top"><?= $mycms->cDate('Y-m-d', $rowFetchUser['conf_reg_date']) ?></td>
									<td align="left" valign="top">
										<?= ((isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'PAID')) ? $mycms->cDate('Y-m-d', $financialSummary['REG_LAST_PAYMENT_ON']) : '' ?>
									</td>
									<td align="left" valign="top">
										<?php
										if ($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID']) {
											echo "DELEGATE";
										} else {
											echo getRegClsfName($rowFetchUser['registration_classification_id']);
										}
										?>

									</td>
									<td align="left" valign="top">
										<?php
										if (
											$rowFetchUser['registration_payment_status'] == "PAID"
											|| $rowFetchUser['registration_payment_status'] == "COMPLEMENTARY"
											|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
											|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
										) {
											echo $rowFetchUser['user_registration_id'];
										}
										?>
									</td>
									<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
									<td align="left" valign="top">
										<?php
										if ($rowFetchUser['registration_classification_id'] == $cfg['INAUGURAL_OFFER_CLASF_ID']) {
											echo getRegClsfName($rowFetchUser['registration_classification_id']);
										} else {
											echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
										}
										?>
									</td>
									<td align="left" valign="top"><?= $rowFetchUser['registration_mode'] ?></td>
									<td align="left" valign="top"><?= $rowFetchUser['reg_type'] ?></td>
									<td align="left" valign="top"><?= implode('; ', $RegData['WORKSHOP_DATA']['SERVICE']['MASTER CLASS']) ?></td>
									<td align="left" valign="top"><?= implode('; ', $RegData['WORKSHOP_DATA']['SERVICE']['POST-CONFERENCE']) ?></td>
									<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] ?></td>
									<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['CHECKIN_DATE'] ?></td>
									<td align="left" valign="top"><?= $RegData['ACCOMODATION_DETAILS']['CHECKOUT_DATE'] ?></td>

									<td align="left" valign="top">
										<?php
										$isShared = false;

										if ($classificationDetails['type'] == 'COMBO') {
											if (in_array($rowFetchUser['registration_classification_id'], $cfg['RESIDENTIAL_SHARING_CLASF_ID'])) {
												echo "SHARED";
												$isShared = true;
											} elseif ($rowFetchUser['registration_classification_id'] != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
												echo "INDIVIDUAL";
											}
										}
										?>
									</td>
									<td align="left" valign="top">
										<?
										if ($isShared && $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] != '') {
											echo $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['NAME'] . '
							[email : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['EMAIL'] . ']
							[mobile : ' . $RegData['ACCOMODATION_DETAILS']['PREFERRED_ACCOMPANY']['MOBILE'] . ']';
										}
										?>
									</td>

									<td align="left" valign="top"><?= implode('; ', $RegData['ACCOMPANY_DATA']['SERVICE']) ?></td>

									<td align="left" valign="top"><?= $RegData['DINNER_DATA']['INAUGRAL_DINNER'] ?></td>
									<td align="left" valign="top">
										<?
										if ($classificationDetails['type'] == 'COMBO') {
											echo 'YES';
										} else {
											echo $RegData['DINNER_DATA']['GALA_DINNER'];
										}
										?>
									</td>

									<td align="left" valign="top"><?= $rowFetchUser['registration_payment_status'] ?></td>
									<td align="right" valign="top"><?= number_format($financialSummary['TOTAL'], 2) ?></td>
									<td align="right" valign="top"><?= number_format($financialSummary['PAID'], 2) ?></td>

									<td align="right" valign="top">
										<?= number_format(floatval($financialSummary['TOTAL']) - floatval($financialSummary['PAID']), 2) ?>
									</td>
									<td align="left" valign="top">
										<?= ($financialSummary['SETTLED_AMOUNT'] > 0) ? number_format($financialSummary['SETTLED_AMOUNT'], 2) : '' ?>
									</td>
									<td align="left" valign="top">
										<?= ($financialSummary['SETTLED_AMOUNT'] > 0) ? $financialSummary['SETTLEMENT_DATE'] : '' ?>
									</td>
									<td align="left" valign="top"
										style=" <?= (trim($communicationSummary['NOTES']) != '' ? 'background:#0099FF;' : '') ?>">
										<?= ($communicationSummary['LAST_MESSAGE'] != '') ? ($communicationSummary['LAST_MESSAGE'] . ',') : '' ?>
										<?= $communicationSummary['NOTES'] ?>
									</td>
									<?
									foreach ($financialSummary['SLIP'] as $slipId => $slipData) {
										foreach ($slipData['PAYMENTS']['RAW']['paymentDetails'] as $k => $rowPayment) {
											$paymentDescription = "";
											if ($rowPayment['payment_mode'] == "Cash") {
												$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>" . setDateTimeFormat2($rowPayment['cash_deposit_date'], "D") . "</b>.";
											}
											if ($rowPayment['payment_mode'] == "Online") {
												$paymentDescription = "Paid by <b>Online</b>. Date of Payment: <b>" . setDateTimeFormat2($rowPayment['payment_date'], "D") . "</b>.
															Transaction Number: <b>" . $rowPayment['atom_atom_transaction_id'] . "</b>.
															Bank Transaction Number: <b>" . $rowPayment['atom_bank_transaction_id'] . "</b>.";
											}
											if ($rowPayment['payment_mode'] == "Card") {
												$paymentDescription = "Paid by <b>Card</b>. Reference Number: <b>" . $rowPayment['card_transaction_no'] . "</b>.
															Date of Payment: <b>" . setDateTimeFormat2($rowPayment['card_payment_date'], "D") . "</b>.
															Remarks: <b>" . $rowPayment['payment_remark'] . "</b> ";
											}
											if ($rowPayment['payment_mode'] == "Draft") {
												$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>" . $rowPayment['draft_number'] . "</b>.
														   Draft Date: <b>" . setDateTimeFormat2($rowPayment['draft_date'], "D") . "</b>.
														   Draft Drawee Bank: <b>" . $rowPayment['draft_bank_name'] . "</b>.";
											}
											if ($rowPayment['payment_mode'] == "NEFT") {
												$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>" . $rowPayment['neft_transaction_no'] . "</b>.
														   Transaction Date: <b>" . setDateTimeFormat2($rowPayment['neft_date'], "D") . "</b>.
														   Transaction Bank: <b>" . $rowPayment['neft_bank_name'] . "</b>.";
											}
											if ($rowPayment['payment_mode'] == "RTGS") {
												$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>" . $rowPayment['rtgs_transaction_no'] . "</b>.
														   Transaction Date: <b>" . setDateTimeFormat2($rowPayment['rtgs_date'], "D") . "</b>.
														   Transaction Bank: <b>" . $rowPayment['rtgs_bank_name'] . "</b>.";
											}
											if ($rowPayment['payment_mode'] == "Cheque") {
												$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>" . $rowPayment['cheque_number'] . "</b>.
														   Cheque Date: <b>" . setDateTimeFormat2($rowPayment['cheque_date'], "D") . "</b>.
														   Cheque Drawee Bank: <b>" . $rowPayment['cheque_bank_name'] . "</b>.";
											}
											?>
											<td align="left"><?= $paymentDescription ?></td>
											<?
										}
									}
									?>
								</tr>
								<?php
							}
						}
					}
				}
			}
			?>
		</table>
		<?
	}
}

function downloadFacultyListExcel($mycms, $cfg)
{
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');

	if ($_REQUEST['SHOW'] != 'HTML') {
		ini_set('max_execution_time', 9000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=FacultyList" . time() . ".xls");
	}

	$condition = "";
	if (isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'FACULTY') {
		$condition = "AND registration_payment_status = 'COMPLEMENTARY' AND tags= 'National Faculty' ";
	}

	if (isset($_REQUEST['FILTER']) && $_REQUEST['FILTER'] == 'UNPAID') {
		$condition = "AND ( ( delegate.registration_payment_status = 'UNPAID' AND !( delegate.registration_mode = 'OFFLINE' AND payment.payment_mode != 'Online')) 
									 OR ( delegate.registration_payment_status = 'UNPAID' AND payment.payment_mode IS NULL ) )";
	}
	$sqlDelegateQueryset = array();
	$sqlDelegateQueryset['QUERY'] = " SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE status='A' 
														AND tags= 'National Faculty' 
											 			  ORDER BY `id` DESC";

	$resultFetchUser = $mycms->sql_select($sqlDelegateQueryset);
	// echo '<pre>'; print_r($resultFetchUser);die;
	/*delegate.conf_reg_date ASC, delegate.id ASC,
													   (CASE WHEN delegate.registration_payment_status = 'PAID' THEN  payment.payment_date
														  WHEN delegate.registration_payment_status = 'COMPLIMENTARY' OR delegate.registration_payment_status = 'ZERO_VALUE' THEN delegate.created_dateTime
													 END) DESC,
													IFNULL(payment.created_dateTime, IFNULL(invoice.created_dateTime,delegate.created_dateTime)) ASC*/

	if ($resultFetchUser) {
		?>

		<table border="1" width="100%">
			<thead>
				<tr style="font-weight:bold;">
					<td width="40" align="center">Sl No</td>
					<td align="left">Name</td>
					<!-- <td align="left">Gender</td> -->
					<td align="left">Mobile</td>
					<td align="left">Email</td>
					<td align="left">Reg Id</td>
					<td align="left">Hopecon Id</td>
					<td align="left">Payment Status</td>
					<td align="left">Tags</td>
					<td align="left">Date</td>

				</tr>
			</thead>
			<tbody>
				<?php

				foreach ($resultFetchUser as $i => $rowFetchUser) { ?>
					<tr class="tlisting" bgcolor="<?= $color ?>">
						<td align="center" valign="top"><?= $i + 1 ?></td>
						<td align="left" valign="top"><?= strtoupper($rowFetchUser['user_full_name']) ?></td>

						<!-- <td align="left" valign="top"><?= strtoupper($rowFetchUser['user_gender']) ?></td> -->

						<td align="left" valign="top">
							<?= $rowFetchUser['user_mobile_isd_code'] . ' ' . formatMobileNo($rowFetchUser['user_mobile_no']) ?>
						</td>
						<td align="left" valign="top"><?= $rowFetchUser['user_email_id'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['user_registration_id'] ?></td>
						<!-- <td align="left" valign="top"><?= $rowFetchUser['hopecon_id'] ?></td> -->
						<td align="left" valign="top"><?= $rowFetchUser['registration_payment_status'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['tags'] ?></td>
						<td align="left" valign="top"><?= $rowFetchUser['conf_reg_date'] ?></td>

					</tr>
				<?php }
				?>

			</tbody>
		</table>
		<?
	}
}

function getServiceTypeString($delegateId = "", $reqId = "", $type = "", $reqStatus = "A")
{
	global $cfg, $mycms;
	$thisUserDetails = getUserDetails($delegateId);
	$dinnerDetails = getUserDetailsByDinnerRefferenceId($reqId, $reqStatus);
	$dinnerUserDetails = getUserDetails($dinnerDetails['refference_id']);

	$morningSession = array();
	if ($type == "CONFERENCE") {
		$string = getRegClsfName(getUserClassificationId($delegateId, true), true);
	}
	if ($type == "EXHIBITOR") {
		$string = getRegClsfName(getUserClassificationId($delegateId, true), true);
	}
	if ($type == "RESIDENTIAL") {
		$string = getRegClsfName(getUserClassificationId($delegateId, true), true);
	}
	if ($type == "WORKSHOP") {
		$workShopDetails = getWorkshopDetails($reqId, true);
		$string = strtoupper($workShopDetails['classification_title']);
		if ($workShopDetails['showInInvoices'] == 'Y') {
			$string .= ' on ' . $mycms->cDate("F j, Y", $workShopDetails['workshop_date']);
		}
		//$string =  strtoupper(getWorkshopName($workShopDetails['workshop_id'],true));
	}
	if ($type == "ACCOMPANY") {
		$accompanyDetails = getUserDetails($reqId);
		$string = $accompanyDetails['user_full_name'];
	}
	if ($type == "ACCOMMODATION") {
		$accmDetails = getAccomodationDetails($reqId, true);
		//echo "<pre>"; print_r($accmDetails); echo "</pre>";
		$string = getAccomPackageName($accmDetails['package_id']) . " - " . $accmDetails['checkin_date'] . " to " . $accmDetails['checkout_date'];
		if ($accmDetails['accompany_name'] != '') {
			$string .= "<b>Sharing Room With - </b>" . $accmDetails['accompany_name'] . "";
		}
	}
	if ($type == "TOUR") {
		$tourDetails = getTourDetails($reqId, true);
		$string = getTourName($tourDetails['package_id']) . " Booking";
	}
	if ($type == "DINNER") {
		$dinnerDetails = getDinnerDetailsOfDelegate($reqId);
		$string = strtoupper($dinnerDetails['dinner_classification_name']) . ' on ' . $mycms->cDate("F j, Y", $dinnerDetails['dinnerDate']);
	}
	return $string;
}

function availableAccommodationRoomTypeOptionList($hotelId, $delegateId)
{
	global $cfg, $mycms;

	// FETCH ACCOMMODATION DETAILS
	$sqlAccommodationDetails['QUERY'] = "SELECT accommodationRequest.checkin_date,
												  accommodationRequest.checkout_date,
												  SUM(accommodationRequest.guest_counter) AS totalGuestCount,
												  accommodationPackage.package_name AS package_name,
												  
												  hotelMaster.hotel_name
											 
											 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodationRequest 
											 
									   INNER JOIN " . _DB_MASTER_HOTEL_ . " hotelMaster 
											   ON accommodationRequest.hotel_id = hotelMaster.id
									   
									   INNER JOIN " . _DB_ACCOMMODATION_PACKAGE_ . " accommodationPackage 
											   ON accommodationRequest.package_id = accommodationPackage.id  
										   
											WHERE accommodationRequest.user_id = '" . $delegateId . "' 
											  AND accommodationRequest.status = 'A'
											  AND accommodationPackage.status = 'A'
											  
										 GROUP BY accommodationRequest.user_id";

	$resultAccommodationDetails = $mycms->sql_select($sqlAccommodationDetails);
	$rowAccommodationDetails = $resultAccommodationDetails[0];


	$sqlFetchRoomType['QUERY'] = "SELECT *	
										  FROM " . _DB_ACCOMMODATION_PACKAGE_ . " 
										  WHERE status = 'A' 
										  AND hotel_id = '" . $hotelId . "'";

	$resultRoomType = $mycms->sql_select($sqlFetchRoomType);

	?>
	<option value="">-- Room Type --</option>
	<?php

	if ($resultRoomType) {

		foreach ($resultRoomType as $keyRoomType => $rowRoomType) {

			?>
			<option value="<?= $rowRoomType['id'] ?>" <?= ($rowRoomType['package_name'] == $rowAccommodationDetails['package_name']) ? 'selected="selected"' : '' ?>><?= $rowRoomType['package_name'] ?></option>
			<?php

		}
	}
}

/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey = "_pgn_";
	$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString = "";
	$searchArray = array();

	$searchArray[$pageKey] = $pageKeyVal;
	$searchArray['src_email_id'] = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_access_key'] = addslashes(trim($_REQUEST['src_access_key'], '#'));
	$searchArray['src_mobile_no'] = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_user_first_name'] = addslashes(trim($_REQUEST['src_user_first_name']));
	$searchArray['src_user_middle_name'] = addslashes(trim($_REQUEST['src_user_middle_name']));
	$searchArray['src_invoice_no'] = addslashes(trim($_REQUEST['src_invoice_no']));
	$searchArray['src_slip_no'] = addslashes(trim($_REQUEST['src_slip_no'], '##'));
	$searchArray['src_registration_mode'] = addslashes(trim($_REQUEST['src_registration_mode']));
	$searchArray['src_user_last_name'] = addslashes(trim($_REQUEST['src_user_last_name']));
	$searchArray['src_atom_transaction_ids'] = addslashes(trim($_REQUEST['src_atom_transaction_ids']));
	$searchArray['src_transaction_ids'] = addslashes(trim($_REQUEST['src_transaction_ids']));
	$searchArray['src_conf_reg_category'] = addslashes(trim($_REQUEST['src_conf_reg_category']));
	$searchArray['src_reg_category'] = addslashes(trim($_REQUEST['src_reg_category']));
	$searchArray['src_registration_id'] = addslashes(trim($_REQUEST['src_registration_id']));
	$searchArray['src_slip_no'] = addslashes(trim($_REQUEST['src_slip_no'], '##'));
	$searchArray['src_invoice_no'] = addslashes(trim($_REQUEST['src_invoice_no']));
	$searchArray['src_payment_status'] = addslashes(trim($_REQUEST['src_payment_status']));
	$searchArray['src_registration_status'] = addslashes(trim($_REQUEST['src_registration_status']));
	$searchArray['src_payment_mode_off'] = addslashes(trim($_REQUEST['src_payment_mode_off']));
	$searchArray['src_invoice_user'] = addslashes(trim($_REQUEST['src_invoice_user']));
	$searchArray['src_current_status'] = addslashes(trim($_REQUEST['src_current_status']));
	$searchArray['src_payment_status'] = addslashes(trim($_REQUEST['src_payment_status']));
	$searchArray['month'] = addslashes(trim($_REQUEST['month']));
	$searchArray['src_payment_status'] = addslashes(trim($_REQUEST['src_payment_status']));
	$searchArray['src_registration_type'] = addslashes(trim($_REQUEST['src_registration_type']));
	$searchArray['src_workshop_classf'] = addslashes(trim($_REQUEST['src_workshop_classf']));
	$searchArray['src_payment_date'] = addslashes(trim($_REQUEST['src_payment_date']));
	$searchArray['src_workshop_type'] = addslashes(trim($_REQUEST['src_workshop_type']));
	$searchArray['src_payment_mode'] = addslashes(trim($_REQUEST['src_payment_mode']));
	$searchArray['src_payment_no'] = addslashes(trim($_REQUEST['src_payment_no']));
	$searchArray['src_cancel_invoice_id'] = addslashes(trim($_REQUEST['src_cancel_invoice_id']));

	$searchArray['src_hasPickup'] = addslashes(trim($_REQUEST['src_hasPickup']));
	$searchArray['src_hasDropoff'] = addslashes(trim($_REQUEST['src_hasDropoff']));


	if (isset($_REQUEST['goto']) && trim($_REQUEST['goto']) != '') {
		$goto = '&show=' . trim($_REQUEST['goto']);
	}

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString . $goto);
}

//////////////////

function getDelegateCallRecords($id, $subject = "")
{
	global $mycms, $cfg;

	$return = array();

	$condition = '';
	if ($subject != '') {
		$condition = " AND call_subject = '" . $subject . "'";
	}

	$sqlfetchPickdropdata = array();
	$sqlfetchPickdropdata['QUERY'] = "SELECT *,	user.username AS LoggedUserName 
										   FROM " . _DB_USER_CALLDETAILS_ . " calldetails
									 INNER JOIN " . _DB_USER_REGISTRATION_ . " delegate
											 ON calldetails.delegate_id=delegate.id
									 INNER JOIN " . _DB_CONF_USER_ . " user
											 ON calldetails.logged_user_id=user.a_id
										  WHERE `delegate_id`='" . $id . "' " . $condition . "
									   ORDER BY calldetails.id ASC";

	$resfetchPickdropdata = $mycms->sql_select($sqlfetchPickdropdata);

	$counter = 0;
	foreach ($resfetchPickdropdata as $key => $val) {
		$counter++;
		$return['CALL'][$counter]['RAW'] = $val;

		$return['LAST_MESSAGE'] = $val['call_contents'];
		$return['LAST_MESSAGE_DATE'] = $val['call_date'] . ' ' . $val['call_time'];
		$return['LAST_MESSAGE_USER'] = $val['LoggedUserName'];
	}

	return $return;
}


function updateNote()
{
	global $cfg, $mycms;

	$loggedUserID = $mycms->getLoggedUserId();

	$note = addslashes(trim($_REQUEST['note']));
	$delegateId = $_REQUEST['delegateId'];

	$sqlUpdateRecord = array();
	$sqlUpdateRecord['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET `user_food_preference_in_details` = ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";

	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'user_food_preference_in_details', 'DATA' => $note, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_by', 'DATA' => $loggedUserID, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdateRecord);
}

function updateSharePref()
{
	global $cfg, $mycms;

	$loggedUserID = $mycms->getLoggedUserId();

	$preffered_accommpany_name = addslashes(trim($_REQUEST['preffered_accommpany_name']));
	$preffered_accommpany_mobile = addslashes(trim($_REQUEST['preffered_accommpany_mobile']));
	$preffered_accommpany_email = addslashes(trim($_REQUEST['preffered_accommpany_email']));
	$accomId = $_REQUEST['accomId'];

	$sqlUpdateRecord = array();
	$sqlUpdateRecord['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
										   SET `preffered_accommpany_name` = ?, 
										   	   `preffered_accommpany_mobile` = ?, 
											   `preffered_accommpany_email` = ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";

	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'preffered_accommpany_name', 'DATA' => $preffered_accommpany_name, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'preffered_accommpany_mobile', 'DATA' => $preffered_accommpany_mobile, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'preffered_accommpany_email', 'DATA' => $preffered_accommpany_email, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_by', 'DATA' => $loggedUserID, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'id', 'DATA' => $accomId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdateRecord);
}

function updateAccomDate()
{
	global $cfg, $mycms;

	$loggedUserID = $mycms->getLoggedUserId();

	$accDate = addslashes(trim($_REQUEST['accDate']));
	$accomId = $_REQUEST['accomId'];
	$dates = explode("|", $accDate);

	$sqlUpdateRecord = array();
	$sqlUpdateRecord['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
										   SET `checkin_date` = ?, 
										   	   `checkout_date` = ?,
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";

	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'checkin_date', 'DATA' => $dates[0], 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'checkout_date', 'DATA' => $dates[1], 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_by', 'DATA' => $loggedUserID, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('FILD' => 'id', 'DATA' => $accomId, 'TYP' => 's');
	$mycms->sql_update($sqlUpdateRecord);
}


function get_workshop_certificate_pdf($user_name, $workshop_id, $download = false)
{
	// if (file_exists(__DIR__ . '/../../lib/vendor/autoload.php')) {
	include_once __DIR__ . '/../../lib/vendor/autoload.php';
	// 	echo "autoload loaded successfully";
	// } else {
	// 	echo "autoload.php not found!";
	// }

	// Validate inputs
	if (empty($user_name) || empty($workshop_id)) {
		return false; // Return early if inputs are missing
	}
	if ($workshop_id == 2) {
		$img_url = _BASE_URL_ . 'images/BasicLungFunctionCertificate.jpg';
	} else if ($workshop_id == 3) {
		$img_url = _BASE_URL_ . 'images/SleepApneaCertificate.jpg';
	} else if ($workshop_id == 4) {
		$img_url = _BASE_URL_ . 'images/BronchoscopyCertificate.jpg';
	} else if ($workshop_id == 5) {
		$img_url = _BASE_URL_ . 'images/ThoracoscopyCertificate.jpg';
	}


	// Generate HTML content
	$html = '<body style="padding: 0; margin:0;">
        <div class="img-holder" style="text-align: center;height: 100vh;width: 100%;">
            <img class="body-img" src="' . $img_url . '" style="width: 100%;height: 100%;object-fit: contain;">

            <div style="position: absolute; width: 100%; height: 100%; top: 0;">
                <div style="width: 100%; margin: 0; text-align:center; position: absolute; top: 48%; z-index: 99; left: 13px; right: 0;">
                    <p style="margin: -474px 0px 38px 329px; font-size: 38px; text-align: center; font-weight: bold; color: black; font-family: \'Times New Roman\',Times, serif;">
                        Dr ' . htmlspecialchars(ucwords(strtolower(trim($user_name)))) . '
                    </p>
                </div>
            </div>
        </div>
    </body>';
	// echo $html;
	// die;
	try {
		// Configure mPDF
		$mpdf = new \Mpdf\Mpdf([
			'format' => 'A4-L', // Landscape A4
			'margin_left' => 0,
			'margin_right' => 0,
			'margin_top' => 0,
			'margin_bottom' => 0,
			'margin_header' => 0,
			'margin_footer' => 0,
		]);

		// Disable automatic page break
		$mpdf->SetAutoPageBreak(false);

		$mpdf->WriteHTML($html);

		if ($download == true) {
			return $mpdf->Output('Workshop_Certificate-' . $user_name . "-" . $workshop_id . '.pdf', 'D');
		} else {
			// Output PDF as a string
			$pdfOutput = $mpdf->Output('conference_certificate.pdf', 'S');
			// echo $pdfOutput; die;
			return base64_encode($pdfOutput);
		}
	} catch (\Mpdf\MpdfException $e) {
		return ('PDF Generation Error: ' . $e->getMessage());
		// return false;
	}
}

function sendWorkshopCertificateMail($mycms, $cfg)
{
	include_once('../../includes/function.workshop.php');
	$loggedUserID = $mycms->getLoggedUserId();

	// Extract inputs with validation
	$delegateId = $_REQUEST['delegateId'] ?? null;
	$abstractId = $_REQUEST['abstractId'] ?? null;
	$slipId = $_REQUEST['slipId'] ?? null;
	$paymentId = $_REQUEST['paymentId'] ?? null;
	$invoice_mode = $_REQUEST['invoice_mode'] ?? null;
	$user_email_id = $_REQUEST['user_email_id'] ?? null;
	$user_full_name = $_REQUEST['user_full_name'] ?? null;
	$mail_subject = $_REQUEST['mail_subject'] ?? 'No Subject';
	$mail_body = $_REQUEST['mail_body'] ?? '';
	$buttonForSpot = $_REQUEST['buttonForSpot'] ?? null;
	$sendCertificate = $_REQUEST['sendCertificate'] ?? false;

	// Validate essential fields
	if (!$delegateId || !$user_email_id || !$user_full_name) {
		error_log('Missing required input parameters for sendRegFinalMail');
		return false;
	}



	if ($sendCertificate) {
		// Generate PDF for attachment
		// $pdfAttachment = get_workshop_certificate_pdf($user_full_name);

		// for multiple attachemnts
		$workshop_details = getWorkshopDetailsOfDelegate($delegateId);
		$pdfAttachment = array();
		foreach ($workshop_details as $key => $rowWorkshop) {
			$pdfAttachment[$key] = get_workshop_certificate_pdf($user_full_name, $rowWorkshop['workshop_id']);
		}

		// if (!$pdfAttachment) {
		// 	echo ("Failed to generate PDF for delegateId: $delegateId");
		// 	return false;
		// }
	} else {
		$pdfAttachment = '';
	}

	// print_r($pdfAttachment);
	// die;

	// Send the email
	$emailSent = $mycms->send_mail(
		$user_full_name,
		$user_email_id,
		$mail_subject,
		$mail_body,
		'',
		'',
		'',
		'',
		'',
		'',
		$cfg['ADMIN_EMAIL'],
		$pdfAttachment
	);


	// if ($emailSent && $sendCertificate) {
	// 	pageRedirection(
	// 		_BASE_URL_ . "webmaster/section_abstract/abstract.free_papers.php",
	// 		'Mail sent successfully',
	// 		''
	// 	);
	// } else
	if ($emailSent) {
		pageRedirection(
			"registration_new.php",
			'Mail sent successfully',
			"&show=invoice&id=" . $delegateId
		);
	} else {
		error_log("Failed to send mail to: $user_email_id");
		return false;
	}
}

//new for pop up
function relocateMasterWorkshopNew($delegateId, $workshopIds, $clsfId, $cutoffId, $spotUser = '')
{
	global $mycms, $cfg;

	include_once('../section_cancelation/includes/function.php');


	// $delegateId = $_REQUEST['delegateId'];
	//$workshopIds = $_REQUEST['workshop_id'];
	// $spotUser = $_REQUEST['userREGtype'];		// SPOT

	$workShopOfDelegate = getWorkshopDetailsOfDelegate($delegateId);
	$regClassfId = getUserClassificationId($delegateId);

	foreach ($workShopOfDelegate as $lk => $selWrkShp) {
		$invoiceId = $selWrkShp['refference_invoice_id'];
		$invoiceDetails = getInvoiceDetails($invoiceId);

		if ($invoiceDetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION' && $selWrkShp['type'] != 'POST-CONFERENCE') {
			$workshopDetails = getWorkshopDetails($invoiceDetails['refference_id']);

			$sqlUpdateInvoice = array();
			$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
												  SET status = 'D'
												WHERE `id` = '" . $selWrkShp['refference_invoice_id'] . "'";
			$mycms->sql_update($sqlUpdateInvoice, false);


			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = " UPDATE " . _DB_REQUEST_WORKSHOP_ . "
											SET `status` = ?
										  WHERE `id` = ?";

			$sqlUpdate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
			$sqlUpdate['PARAM'][] = array('FILD' => 'id', 'DATA' => $selWrkShp['id'], 'TYP' => 's');
			$mycms->sql_update($sqlUpdate, false);
		}
	}

	$confRegInv = invoiceDetailsOfDelegate($delegateId, " AND ( service_type = 'DELEGATE_CONFERENCE_REGISTRATION' OR service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION') ");

	$lastSlipId = $confRegInv[0]['slip_id'];
	$lastCutoffId = $confRegInv[0]['service_tariff_cutoff_id'];

	if ($lastCutoffId == '') {
		$lastCutoffId = getTariffCutoffId();
	}

	if ($lastSlipId == '') {
		$lastSlipId = insertingSlipDetails($delegateId, 'OFFLINE', 'GENERAL', date("Y-m-d"), 'BACK');
	}

	$mycms->setSession('SLIP_ID', $lastSlipId);

	$workshopDetailArray = array();

	foreach ($workshopIds as $kk => $workshopId) {
		$workshopDetailArray[$workshopId]['delegate_id'] = $delegateId;
		$workshopDetailArray[$workshopId]['workshop_id'] = $workshopId;
		$workshopDetailArray[$workshopId]['tariff_cutoff_id'] = $cutoffId;
		$workshopDetailArray[$workshopId]['workshop_tarrif_id'] = getWorkshopTariffId($workshopId, $cutoffId, $clsfId);
		$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
		$workshopDetailArray[$workshopId]['booking_mode'] = 'OFFLINE';
		$workshopDetailArray[$workshopId]['registration_type'] = 'GENERAL';
		$workshopDetailArray[$workshopId]['refference_invoice_id'] = 0; // Need To Edit
		$workshopDetailArray[$workshopId]['refference_slip_id'] = $lastSlipId;
		$workshopDetailArray[$workshopId]['payment_status'] = 'UNPAID';
	}

	$workshopReqId = insertingWorkshopDetails($workshopDetailArray);
	foreach ($workshopReqId as $key => $reqId) {
		$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP', 'GENERAL', date("Y-m-d"));


	}

	$mycms->removeSession('SLIP_ID');

	// if ($spotUser != '') {
	// 	pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
	// } else {
	// 	pageRedirection("registration_new.php", "1"); //,"&show=reallocationOfWorkshop"
	// }
}
//new addAccompany in edit
function addAccompnyNew($accompanyDetailsArray, $delegateId, $mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.registration.php');
	include_once('../../includes/function.accompany.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	$accompanyDetails = $_REQUEST;
	$spotUser = $_REQUEST['userREGtype'];		// SPOT
	//$delegateId = $_REQUEST['delegate_id'];
	$discountAmount = $_REQUEST['discountAmount'];

	//echo 1212; die;

	if ($accompanyDetails) {
		// $registrationMode = ($accompanyDetails['registrationMode'] == "OFFLINE") ? "OFFLINE" : "ONLINE";
		// $accompanyCutoff = $accompanyDetails['registration_acc_cutoff'];
		// foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {
		// 	$accompanyDetailsArray[$val]['refference_delegate_id'] = $delegateId;
		// 	$accompanyDetailsArray[$val]['user_full_name'] = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
		// 	$accompanyDetailsArray[$val]['user_age'] = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
		// 	$accompanyDetailsArray[$val]['user_food_preference'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
		// 	$accompanyDetailsArray[$val]['user_food_details'] = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
		// 	$accompanyDetailsArray[$val]['accompany_relationship'] = addslashes(trim(strtoupper('ACCOMPANY')));

		// 	$accompanyDetailsArray[$val]['isRegistration'] = 'Y';
		// 	$accompanyDetailsArray[$val]['isConference'] = 'Y';
		// 	$accompanyDetailsArray[$val]['registration_classification_id'] = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
		// 	$accompanyDetailsArray[$val]['registration_tariff_cutoff_id'] = $accompanyCutoff;
		// 	$accompanyDetailsArray[$val]['accompany_tariff_cutoff_id'] = $accompanyCutoff;
		// 	$accompanyDetailsArray[$val]['registration_request'] = ($_REQUEST['userREGtype'] == "SPOT") ? 'SPOT' : 'GENERAL';
		// 	$accompanyDetailsArray[$val]['operational_area'] = ($_REQUEST['userREGtype'] == "SPOT") ? 'SPOT' : 'GENERAL';
		// 	$accompanyDetailsArray[$val]['registration_payment_status'] = ($_REQUEST['registration_type_add'] == "ZERO_VALUE") ? 'ZERO_VALUE' : 'UNPAID';
		// 	$accompanyDetailsArray[$val]['registration_mode'] = "OFFLINE";
		// 	$accompanyDetailsArray[$val]['account_status'] = 'REGISTERED';
		// 	$accompanyDetailsArray[$val]['reg_type'] = addslashes(trim(strtoupper('BACK')));
		// }

		$slipId = insertingSlipDetails($delegateId, 'OFFLINE', "");

		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
											  SET `payment_status` = ?
											WHERE `id` = ? ";
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'ZERO_VALUE', 'TYP' => 's');
			$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('SLIP_ID'), 'TYP' => 's');
			$mycms->sql_update($sqlUpdateSlip, false);

			// $paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
			// $sqlUpdatePaymentRequest['QUERY']  = "UPDATE " . _DB_PAYMENT_ . " 
			// 									  SET `status` = 'D'
			// 											  WHERE `id` = '" . $paymentId . "' 
			// 											  AND `status` = 'A' ";

			// $mycms->sql_update($sqlUpdatePaymentRequest, false);

			$payment_status = "ZERO_VALUE";
		} else {
			$payment_status = "UNPAID";
		}



		/*if($spotUser != '')
			{
				 $accompanyReqId = updatingAccompanyDetails($accompanyDetailsArray);
			}
			else
			{*/
		$accompanyReqId = insertingAccompanyDetails($accompanyDetailsArray, '', $accompanyCutoff);
		//}
		foreach ($accompanyReqId as $key => $reqId) {

			$sqlAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  
											SET `registration_payment_status` = '" . $payment_status . "'
											WHERE `id` = '" . $reqId . "' 
												AND `status` = 'A' ";

			$mycms->sql_update($sqlAccompany, false);

			$invoiceId = insertingInvoiceDetails($reqId, 'ACCOMPANY');

			if ($spotUser != '') {
				$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
														  SET `invoice_request`  = '" . $spotUser . "',
																`payment_status` = 'PAID'													  
														WHERE `status` = 'A'
															  AND `id` = '" . $invoiceId . "' ";

				$mycms->sql_update($sqlUpdateInvoiceRequest, false);

				$sqlAccompany['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "  
											SET `registration_payment_status` = 'PAID'
											WHERE `id` = '" . $reqId . "' 
												AND `status` = 'A' ";

				$mycms->sql_update($sqlAccompany, false);

				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY'] = "UPDATE " . _DB_SLIP_ . "
											  SET `payment_status` = ?
											WHERE `id` = ? ";
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'payment_status', 'DATA' => 'PAID', 'TYP' => 's');
				$sqlUpdateSlip['PARAM'][] = array('FILD' => 'id', 'DATA' => $mycms->getSession('SLIP_ID'), 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);
			} else {
				$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
														  SET `invoice_request`  = '" . $spotUser . "',
																`payment_status` = 'UNPAID'													  
														WHERE `status` = 'A'
															  AND `id` = '" . $invoiceId . "' ";

				$mycms->sql_update($sqlUpdateInvoiceRequest, false);
			}

			$payment_date = date('Y-m-d');

			// if ($_REQUEST['registration_type_add'] != "ZERO_VALUE") {
			// 	$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
			// 	$sqlUpdatePaymentRequest['QUERY']  = "UPDATE " . _DB_PAYMENT_ . " 
			// 									  SET `payment_status` = 'PAID',
			// 									  `collected_by` = '" . $loggedUserID . "',
			// 									  `payment_type` = 'SPOT',
			// 									  `payment_date` = '" . $payment_date . "',
			// 									  `amount` = '" . $paymentDetailsAmount . "'
			// 											  WHERE `id` = '" . $paymentId . "' 
			// 											  AND `status` = 'A' ";

			// 	$mycms->sql_update($sqlUpdatePaymentRequest, false);
			// }

			//30-07-2024
			if ($_REQUEST['registration_type_add'] != "ZERO_VALUE") {

				// if ($_REQUEST['registration_type_add'] == "GENERAL") {
				if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
					updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
					$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
				} else {
					$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
				}
				// }
				$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
				if ($spotUser != '') {
					$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '" . $loggedUserID . "',
												  `payment_type` = 'SPOT',
												  `payment_date` = '" . $payment_date . "',
												  `amount` = '" . $paymentDetailsAmount . "'
														  WHERE `id` = '" . $paymentId . "' 
														  AND `status` = 'A' ";

					$mycms->sql_update($sqlUpdatePaymentRequest, false);
				} else {
					$sqlUpdatePaymentRequest['QUERY'] = "UPDATE " . _DB_PAYMENT_ . " 
												  SET `payment_status` = 'UNPAID',
												  `collected_by` = '" . $loggedUserID . "',
												  
												  `payment_date` = '" . $payment_date . "',
												  `amount` = '" . $paymentDetailsAmount . "'
														  WHERE `id` = '" . $paymentId . "' 
														  AND `status` = 'A' ";

					$mycms->sql_update($sqlUpdatePaymentRequest, false);
				}
			}
			if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
				zeroValueInvoiceUpdate($invoiceId, 'ACCOMPANY', $slipId);
				gstInsertionInInvoice($invoiceId);
			}
		}

		$accDinnerDetails = $accompanyDetails['banquet_dinner_add'];

		if ($accDinnerDetails) {
			foreach ($accDinnerDetails as $key => $val) {
				$dinnerDetailsArray1[$key]['refference_id'] = $accompanyReqId[$key - 1];
				$dinnerDetailsArray1[$key]['delegate_id'] = $delegateId;
				$dinnerDetailsArray1[$key]['package_id'] = 2;
				$dinnerDetailsArray1[$key]['tariff_cutoff_id'] = $accompanyDetails['cutoff_id_add'];
				$dinnerDetailsArray1[$key]['booking_quantity'] = 1;
				$dinnerDetailsArray1[$key]['booking_mode'] = 'OFFLINE';
				$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray1[$key]['refference_slip_id'] = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray1[$key]['payment_status'] = $_REQUEST['registration_type_add'] == "ZERO_VALUE" ? 'ZERO_VALUE' : 'UNPAID';
			}

			$dinerReqId = insertingDinnerDetails($dinnerDetailsArray1);

			//echo "<pre>"; print_r($dinerReqId); echo "</pre>"; die();
			foreach ($dinerReqId as $key => $reqId) {
				if ($spotUser != '') {
					$sqlAccompany['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "  
												SET `payment_status` = '" . $payment_status . "'
												WHERE `id` = '" . $reqId . "' 
													AND `status` = 'A' ";

					$mycms->sql_update($sqlAccompany, false);
				}

				//insertingInvoiceDetails($reqId,'DINNER','GENERAL', $date);
				$invoiceId = insertingInvoiceDetails($reqId, 'DINNER');
				if ($spotUser != '') {
					$sqlUpdateInvoiceRequest['QUERY'] = "UPDATE " . _DB_INVOICE_ . " 
												  SET `invoice_request`  = '" . $spotUser . "',
														`payment_status` = '" . $payment_status . "'													  
												WHERE `status` = 'A'
													  AND `id` = '" . $invoiceId . "' ";

					$mycms->sql_update($sqlUpdateInvoiceRequest, false);
				}
				if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") {
					zeroValueInvoiceUpdate($invoiceId, 'DINNER');
					gstInsertionInInvoice($invoiceId);
				}
			}
		}

		if ($_REQUEST['registration_type_add'] == "ZERO_VALUE") // mail for complimentry
		{
			offline_conference_registration_confirmation_accompany_message($delegateId, '', $mycms->getSession('SLIP_ID'), "SEND");

			//$mycms->redirect("complementary.online.payment.success.php");
		}

		// if ($_REQUEST['registration_type_add'] == "GENERAL") {
		// 	if (isset($_REQUEST['discountAmount']) && trim($_REQUEST['discountAmount']) != '') {
		// 		updateonDiscount($mycms->getSession('SLIP_ID'), $_REQUEST['discountAmount']);
		// 		$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
		// 	}
		// }
		if ($spotUser != '') {
			$paymentDetailsAmount = invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
			$payment_date = date('Y-m-d');

			/*$sqlUpdatePaymentRequest['QUERY']  = "UPDATE "._DB_PAYMENT_." 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '".$loggedUserID."',
												  `payment_type` = 'SPOT',
												  `payment_date` = '".$payment_date."',
												  `amount` = '".$paymentDetailsAmount."'
														  WHERE `id` = '".$paymentId."' 
														  AND `status` = 'A' ";

				$mycms->sql_update($sqlUpdatePaymentRequest, false);*/
		}
	}

	if ($spotUser != '') {
		//pageRedirection("registration_new.php?show=viewAll&id=".$delegateId."&slipId=".$mycms->getSession('SLIP_ID')."&paymentId=".$paymentId."&reg_type=".$_REQUEST['registration_type_add']."&mailFor=Accom&userREGtype=SPOT&mode=spotSearch");
		//pageRedirection(_BASE_URL_ . "/webmaster/section_spot/spot_create_delegate.php?show=submitted&userId=" . $delegateId . "&paymentId=" . $paymentId, "");
	} else {
		if ($_REQUEST['registration_type_add'] == "GENERAL") // mail for General
		{

			/*$paymentId = insertingPartialPaymentDetails($mycms->getSession('SLIP_ID'));
					$sqlUpdatePaymentRequest['QUERY']  = "UPDATE "._DB_PAYMENT_." 
												  SET `payment_status` = 'PAID',
												  `collected_by` = '".$loggedUserID."',
												  `payment_type` = 'SPOT',
												  `payment_date` = '".$payment_date."',
												  `amount` = '".$paymentDetailsAmount."'
														  WHERE `id` = '".$paymentId."' 
														  AND `status` = 'A' ";*/

			//$mycms->sql_update($sqlUpdatePaymentRequest, false);

			offline_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $mycms->getSession('SLIP_ID'), 'SEND');
		}




		//pageRedirection("registration_new.php?show=viewAll", "");
	}
}

function nameAccompanyUpdateProcessNew($accompanyId, $user_full_name, $food_preference)
{
	global $cfg, $mycms;

	$loggedUserID = $mycms->getLoggedUserId();
   if($user_full_name!=''){
	$sqlUpdateRecord = array();
	$sqlUpdateRecord['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
        SET 
            `user_full_name` = ?, 
            `user_food_preference` = ?,
            `modified_by` = ?,
            `modified_ip` = ?,
            `modified_sessionId` = ?,
            `modified_dateTime` = ?
        WHERE `id` = ?";

	$sqlUpdateRecord['PARAM'][] = array('DATA' => strtoupper(trim($user_full_name)), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('DATA' => strtoupper(trim($food_preference)), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('DATA' => $loggedUserID, 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('DATA' => session_id(), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][] = array('DATA' => $accompanyId, 'TYP' => 'i');
	$mycms->sql_update($sqlUpdateRecord);
   }
}

function checkAccompanyExists($id)
{
	global $mycms;

	$sql['QUERY'] = "SELECT id FROM " . _DB_USER_REGISTRATION_ . "
                     WHERE id = ?
                     LIMIT 1";

	$sql['PARAM'][] = [
		'DATA' => $id,
		'TYP' => 'i'
	];

	$result = $mycms->sql_select($sql);

	return !empty($result);
}

function paymentProcessSetup($delegateId, $slip_id, $payment_mode, $payment_array,$invoice_generation_mode_edit='')
{
	//echo 'slip_id='.$slip_id;
	//  echo '<pre>'; print_r($slip_id); die;
	$reg_type = 'BACK';
	if($reg_type == 'BACK'){
		$RedirectURL = 'webmaster/registration.php' ;
	}else{
		$RedirectURL ='registration.success.php';
	}
	
	if ($invoice_generation_mode_edit=='COMPLIMENTARY' || $invoice_generation_mode_edit=='ZERO_VALUE') {
		$payment_mode = $payment_mode;
		complementaryPaymentConfirmationProcess($slip_id, $delegateId);
		
		?>
		<center>
			<form action="<?= _BASE_URL_ ?><?=$RedirectURL?>" method="post" name="srchComplementaryFrm" >
				<input type="hidden" name="did"  />
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
					<input type="hidden" name="paymentstatusPre" value="<?= $_REQUEST['paymentstatus'] ?>" />
					<input type="hidden" name="discountAmount" value="<?= $payment_array['discountAmountEdit'] ?>" />
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
                    <input type="hidden" name="card_number" value="<?= $payment_array['card_number'] ?>" />
					<input type="hidden" name="card_date" value="<?= $payment_array['card_date'] ?>" />

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
?>