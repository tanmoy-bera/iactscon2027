<?php
include_once("includes/frontend.init.php");
include_once('includes/function.delegate.php');
include_once('includes/function.invoice.php');
include_once('includes/function.accompany.php');
include_once('includes/function.workshop.php');
include_once('includes/function.registration.php');
include_once('includes/function.messaging.php');
include_once('includes/function.dinner.php');
require_once(__DIR__ . '/vendor/autoload.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$emailHeader                    = $cfg['BASE_URL'] . $cfg['EMAIL.TEMPLATE.1'] . "emailHeader.jpg";
$emailFooter                    = $cfg['BASE_URL'] . $cfg['EMAIL.TEMPLATE.1'] . "emailFooter.jpg";
$loginUrl                       = $cfg['BASE_URL'] . "login.php";

 $api = new Api($cfg['RAZORPAY_KEY_ID'], $cfg['RAZORPAY_SECRET']);
// $api = new Api('rzp_test_SFffDin9pNz04S', 's4H0hc0b8Plsnwm1RjN5aPZw'); //test

$delegateId = $mycms->getSession('LOGGED.USER.ID');
$slipId     = $mycms->getSession('SLIP_ID');
$handShake  = $_POST['HAND'] ?? $_GET['HAND'] ?? null;
$payCurrency 					= getRegistrationCurrency(getUserClassificationId($delegateId));

$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

if (floatval($pendingAmountOfSlip) > 0 && floatval($pendingAmountOfSlip) != floatval($invoiceAmountOfSlip)) {
	$serviceRoundOffPrice			= $pendingAmountOfSlip;
} else {
	$serviceRoundOffPrice			= invoiceAmountOfSlip($slipId);
}
 
$paymentId  = $_POST['razorpay_payment_id'] ?? '';
$orderId    = $_POST['razorpay_order_id'] ?? '';
$signature  = $_POST['razorpay_signature'] ?? '';

// Verify payment signature
try {
    $attributes  = [
        'razorpay_order_id'   => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature'  => $signature
    ];
    $api->utility->verifyPaymentSignature($attributes);
    $f_code = 'Ok';
} catch(SignatureVerificationError $e) {
    $f_code = 'F';
    $error  = $e->getMessage();
}

// Record raw data
$transactionRawData = addslashes(json_encode($_POST));
$paymentGateway = 'RAZORPAY';
$paymentAmount  = $serviceRoundOffPrice ?? 0;
 
$sqlInsertPaymentData = [
    'QUERY' => "INSERT INTO " . _DB_PAYMENT_RAW_DATA_ . " 
                SET `slip_id` = ?, 
                    `delegate_id` = ?,
                    `raw_data` = ?,
                    `payment_status` = ?,
                    `payment_mode` = ?,
                    `payment_date` = ?,
                    `online_transaction_gateway` = ?,
                    `razorpay_transaction_id` = ?,
                    `amount` = ?,
                    `status` = ?, 
                    `created_ip` = ?, 
                    `created_sessionId` = ?,
                    `created_browser` = ?,
                    `created_dateTime` = ?",
    'PARAM' => [
        ['FILD'=>'slip_id','DATA'=>$slipId,'TYP'=>'s'],
        ['FILD'=>'delegate_id','DATA'=>$delegateId,'TYP'=>'s'],
        ['FILD'=>'raw_data','DATA'=>$transactionRawData,'TYP'=>'s'],
        ['FILD'=>'payment_status','DATA'=>($f_code=='Ok'?'SUCCESS':'FAILURE'),'TYP'=>'s'],
        ['FILD'=>'payment_mode','DATA'=>'Online','TYP'=>'s'],
        ['FILD'=>'payment_date','DATA'=>date('Y-m-d'),'TYP'=>'s'],
        ['FILD'=>'online_transaction_gateway','DATA'=>$paymentGateway,'TYP'=>'s'],
        ['FILD'=>'razorpay_transaction_id','DATA'=>$paymentId,'TYP'=>'s'],
        ['FILD'=>'amount','DATA'=>$paymentAmount,'TYP'=>'s'],
        ['FILD'=>'status','DATA'=>'A','TYP'=>'s'],
        ['FILD'=>'created_ip','DATA'=>$_SERVER['REMOTE_ADDR'],'TYP'=>'s'],
        ['FILD'=>'created_sessionId','DATA'=>session_id(),'TYP'=>'s'],
        ['FILD'=>'created_browser','DATA'=>$_SERVER['HTTP_USER_AGENT'],'TYP'=>'s'],
        ['FILD'=>'created_dateTime','DATA'=>date('Y-m-d H:i:s'),'TYP'=>'s']
    ]
];

$insertedPayId = $mycms->sql_insert($sqlInsertPaymentData, false);

if ($f_code == "Ok") {
	// COUNTING PREVIOUS PAYMENT RECORD
	$sqlFetchPayment  = array();
	$sqlFetchPayment['QUERY']        = "SELECT * FROM " . _DB_PAYMENT_ . " 
											   WHERE `delegate_id` = ? 
											     AND `slip_id` = ? 
												 AND `payment_mode` = ? 
												 AND `razorpay_transaction_id` = ? 
											            ";

	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'delegate_id',                  'DATA' => $delegateId,            'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'slip_id',                      'DATA' => $slipId,                'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'payment_mode',                 'DATA' => 'Online',               'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'razorpay_transaction_id',   'DATA' => $paymentId,        'TYP' => 's');
	
	$resultPayment          	= $mycms->sql_select($sqlFetchPayment);
	$paymentNumRows         	= $mycms->sql_numrows($resultPayment);

	if ($paymentNumRows == 0) {
		// $paymentAmount          = $amt;

		// INSERTING PAYMENT DETAILS
		$sqlInsertPayment               = array();
		$sqlInsertPayment['QUERY']    	= "INSERT INTO " . _DB_PAYMENT_ . " 
											   SET `delegate_id` = ?,
											       `slip_id` = ?, 
												   `payment_mode` = ?, 
												   `payment_date` = ?,
												   `online_transaction_gateway` = ?,
												   `online_transaction_req_id`  = ?,
												   `atom_atom_transaction_id` = ?,
												   `atom_merchant_transaction_id` = ?,
												   `atom_transaction_amount` = ?,
												   `atom_surcharge` = ?,
												   `atom_product_id` = ?,
												   `atom_transaction_date` = ?,
												   `atom_bank_transaction_id` = ?,
												   `atom_f_code` = ?,
												   `atom_transaction_bank_name` = ?,
												   `atom_discriminator` = ?,
												   `atom_transaction_card_no` = ?,
												   `razorpay_transaction_id` = ?,
												   `currency` = ?,
												   `amount` = ?,
												   `payment_status` = ?,
												   `status` = ?, 
												   `created_ip` = ?, 
												   `created_sessionId` = ?,
												   `created_browser` = ?,
												   `created_dateTime` = ?";

		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'delegate_id',                    'DATA' => $delegateId,                  'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'slip_id',                        'DATA' => $slipId,                      'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'payment_mode',                   'DATA' => 'Online',                     'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'payment_date',                   'DATA' => date('Y-m-d'),                'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'online_transaction_gateway',     'DATA' => $paymentGateway,              'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'online_transaction_req_id',      'DATA' => $online_transaction_req_id,   'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_atom_transaction_id',        'DATA' => $mmp_txn,                    'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_merchant_transaction_id',    'DATA' => $mer_txn,                    'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_amount',         'DATA' => $paymentAmount,              'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_surcharge',                  'DATA' => $surcharge,                  'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_product_id',                 'DATA' => $prodid,                     'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_date',           'DATA' => $date,                       'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_bank_transaction_id',        'DATA' => $bank_txn,                   'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_f_code',                     'DATA' => $f_code,                     'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_bank_name',      'DATA' => $bank_name,                  'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_discriminator',              'DATA' => $discriminator,              'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_card_no',        'DATA' => $cardNumber,                 'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'razorpay_transaction_id',        'DATA' => $paymentId,                 'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'currency',                        'DATA' => $payCurrency,                'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'amount',                          'DATA' => $paymentAmount,              'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'payment_status',                  'DATA' => 'PAID',                      'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'status',                          'DATA' => 'A',                         'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_ip',                      'DATA' => $_SERVER['REMOTE_ADDR'],     'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_sessionId',               'DATA' => session_id(),                'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_browser',                 'DATA' => $_SERVER['HTTP_USER_AGENT'], 'TYP' => 's');
		$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_dateTime',                'DATA' => date('Y-m-d H:i:s'),         'TYP' => 's');
      
		$insertedPayId = $mycms->sql_insert($sqlInsertPayment, false);

		$mycms->setSession('LOGGED.USER.ID', $delegateId);
		$mycms->setSession('IS_LOGIN', "YES");

		$sqlUpdateSlip        = array();
		$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_SLIP_ . "
												SET `payment_status` = ?
											  WHERE `id` = ?";

		$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' => 'PAID',         'TYP' => 's');
		$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                'DATA' => $slipId,         'TYP' => 's');
		$mycms->sql_update($sqlUpdateSlip, false);

		$activeInvoice 		  = invoiceDetailsActiveOfSlip($slipId);

		$isConference		  = 'NO';
		$isResidential		  = 'NO';
		$isOnlyWorkshop		  = 'NO';
		$isOnlyAccompany	  = 'NO';
		$isOnlyAccommodation  = 'NO';
		$isOnlyDinner  		  = 'NO';

		$whoToMailnWhat 	  = array();
		foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
			if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
				$isConference		  = "YES";
				$sqlUpdateSlip       = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = ?
													  WHERE `id` = ?";

				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'registration_payment_status',    'DATA' => 'PAID',                             'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                             'DATA' => $valActiveInvoice['refference_id'],  'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

				$whoToMailnWhat[$valActiveInvoice['id']]['CONF_TYP'] = $valActiveInvoice['service_type'];
				$whoToMailnWhat[$valActiveInvoice['id']]['DELG_ID'] = $valActiveInvoice['delegate_id'];
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
				$isResidential		  = 'YES';

				$sqlUpdateSlip       = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = ?,
														    `workshop_payment_status`  = ?,
															`accommodation_payment_status` = ?
															WHERE `id` = ?";

				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'registration_payment_status',    'DATA' => 'PAID',                             'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'workshop_payment_status',        'DATA' => 'PAID',                               'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'accommodation_payment_status',   'DATA' => 'PAID',                             'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                             'DATA' => $valActiveInvoice['refference_id'],  'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdateWorkshop   = array();
				$sqlUpdateWorkshop['QUERY']	     = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = ?
													  WHERE `delegate_id` = ?";

				$sqlUpdateWorkshop['PARAM'][]  = array('FILD' => 'payment_status',       'DATA' => 'PAID',                               'TYP' => 's');
				$sqlUpdateWorkshop['PARAM'][]  = array('FILD' => 'delegate_id',       'DATA' => $valActiveInvoice['refference_id'],   'TYP' => 's');

				$mycms->sql_update($sqlUpdateWorkshop, false);



				$sqlUpdate1 = array();
				$sqlUpdate1['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "  
												SET `payment_status` = ? 
												WHERE `user_id` = ? 
												AND `status` = ?";

				$sqlUpdate1['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' => 'PAID',                               'TYP' => 's');
				$sqlUpdate1['PARAM'][]  = array('FILD' => 'user_id',           'DATA' => $valActiveInvoice['refference_id'],   'TYP' => 's');
				$sqlUpdate1['PARAM'][]  = array('FILD' => 'status',            'DATA' => 'A',                                  'TYP' => 's');
				$mycms->sql_update($sqlUpdate1, false);

				$whoToMailnWhat[$valActiveInvoice['id']]['CONF_TYP'] = $valActiveInvoice['service_type'];
				$whoToMailnWhat[$valActiveInvoice['id']]['DELG_ID'] = $valActiveInvoice['delegate_id'];
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
				$isOnlyWorkshop		  = "YES";
				$sqlUpdateSlip   = array();
				$sqlUpdateSlip['QUERY']	     = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?";

				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' => 'PAID',                               'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                   'DATA' => $valActiveInvoice['refference_id'],   'TYP' => 's');

				$mycms->sql_update($sqlUpdateSlip, false);
				$sqlUpdate = array();
				$sqlUpdate['QUERY']		 = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `workshop_payment_status` = ?
											  WHERE `id` = ?";

				$sqlUpdate['PARAM'][]  = array('FILD' => 'workshop_payment_status',    'DATA' => 'PAID',                               'TYP' => 's');
				$sqlUpdate['PARAM'][]  = array('FILD' => 'id',                         'DATA' => $valActiveInvoice['delegate_id'],   'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);
			}

			if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
				$isOnlyAccompany	  = 'YES';
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = ?
													  WHERE `id` = ?";

				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'registration_payment_status',    'DATA' => 'PAID',                            'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                             'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
				$isOnlyAccommodation  = 'YES';

				//$sqlUpdateSlip['QUERY']	      = "UPDATE ".$cfg['DB.REQUEST.ACCOMMODATION']."
				$sqlUpdateSlip	     = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
												SET `payment_status` = ?
											  WHERE `user_id` = ? AND `status` = ?";

				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' => 'PAID',                            'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'user_id',                'DATA' => $valActiveInvoice['delegate_id'],           'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'status',                'DATA' => 'A',           'TYP' => 's');
				$mycms->sql_update($sqlUpdateSlip, false);


				$sqlUpdate	     = array();
				$sqlUpdate['QUERY']		      = "UPDATE " . _DB_USER_REGISTRATION_ . "
												SET `accommodation_payment_status` = ?
											  WHERE `id` = ?";

				$sqlUpdate['PARAM'][]  = array('FILD' => 'accommodation_payment_status',    'DATA' => 'PAID',                            'TYP' => 's');
				$sqlUpdate['PARAM'][]  = array('FILD' => 'id',                'DATA' => $valActiveInvoice['delegate_id'],           'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);
			}

			if ($valActiveInvoice['service_type'] == 'DELEGATE_DINNER_REQUEST') {
				if ($isOnlyAccompany == 'NO') {
					$isOnlyDinner  		  = 'YES';
				}
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_DINNER_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?";

				$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'payment_status', 'DATA' => 'PAID',                             'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'id',             'DATA' => $valActiveInvoice['refference_id'], 'TYP' => 's');

				$mycms->sql_update($sqlUpdateSlip, false);
			}

			$sqlUpdateSlip	     = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_INVOICE_ . "
													SET `payment_status` = ?
												  WHERE `id` = ?
													AND `payment_status` = ?";

			$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' => 'PAID',                            'TYP' => 's');
			$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                'DATA' => $valActiveInvoice['id'],           'TYP' => 's');
			$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' => 'UNPAID',                          'TYP' => 's');

			$mycms->sql_update($sqlUpdateSlip, false);
		}

		$sqlUpdateSlip             = array();
		$sqlUpdateSlip['QUERY']	   = "UPDATE " . _DB_SLIP_ . "
											SET `finalization_status` = ?
										  WHERE `id` = ?";

		$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'finalization_status', 'DATA' => 'YES',   'TYP' => 's');
		$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                  'DATA' => $slipId, 'TYP' => 's');
		$mycms->sql_update($sqlUpdateSlip, false);

		foreach ($whoToMailnWhat as $invId => $rowdetails) {
			$delegate_id = $rowdetails['DELG_ID'];

			if ($rowdetails['CONF_TYP'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
				$isOnlyAccommodation  = 'NO';
				$isOnlyAccompany	  = 'NO';
				$isOnlyWorkshop		  = "NO";
				online_conference_registration_confirmation_message($delegate_id, $insertedPayId, $slipId, 'SEND');
			} else if ($rowdetails['CONF_TYP'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
				//online_package_registration_confirmation_message($delegate_id, $slipId, 'SEND'); NO FUNCTION AVAILABLE
				online_conference_registration_confirmation_message($delegate_id, $insertedPayId, $slipId, 'SEND');
			}
		}

		if ($isConference == "NO" && $isResidential == "NO" && $isOnlyAccommodation == "YES") {
			online_accommodation_confirmation_message($delegateId, $insertedPayId, $slipId, 'SEND');
		}
		if ($isConference == "NO" && $isResidential == "NO" && $isOnlyAccompany == "YES") {
			online_conference_registration_confirmation_accompany_message($delegateId, $insertedPayId, $slipId, 'SEND');
		}
		if ($isConference == "NO" && $isResidential == "NO" && $isOnlyDinner == "YES") {
			online_dinner_confirmation_message($delegateId, $insertedPayId, $slipId, 'SEND');
		}
		if ($isConference == "NO" && $isResidential == "NO" && $isOnlyWorkshop == "YES") {
			online_conference_registration_confirmation_workshop_message($delegateId, $insertedPayId, $slipId, 'SEND');
		}

		$mycms->removeSession('REGISTRATION_MODE');
		$mycms->removeSession('CURRENCY');
	}
	?>
	<script>
      window.location.href = "<?= _BASE_URL_ ?>online.payment.success.php?HAND=<?= $handShake ?>";
	  </script>
	  <?
	// $mycms->redirect("online.payment.success.php");
} else {
	online_conference_payment_failure_message($delegateId, 'SEND');
	$mycms->setSession('SLIP_ID', $slipId); // slip_id was not found from the session in payment failure page, so set it.
	$mycms->redirect("online.payment.failure.php");
}
