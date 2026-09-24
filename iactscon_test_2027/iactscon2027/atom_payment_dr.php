<?php
include_once("includes/frontend.init.php");
include_once('includes/function.delegate.php');
include_once('includes/function.invoice.php');
include_once('includes/function.accompany.php');
include_once('includes/function.workshop.php');
include_once('includes/function.registration.php');
include_once('includes/function.messaging.php');
include_once('includes/function.dinner.php');


$emailHeader                    = $cfg['BASE_URL'] . $cfg['EMAIL.TEMPLATE.1'] . "emailHeader.jpg";
$emailFooter                    = $cfg['BASE_URL'] . $cfg['EMAIL.TEMPLATE.1'] . "emailFooter.jpg";
$loginUrl                       = $cfg['BASE_URL'] . "login.php";

unset($_SESSION['PG_CARDMODE']);

$paymentGateway					= 'ATOM';

$delegateId                     = $mycms->getSession('LOGGED.USER.ID');
$slipId		                    = $mycms->getSession('SLIP_ID');

$handShake 						= $_REQUEST['HAND'];

$paymentRequestDetails			= getPaymentRequestDetails($handShake);

if (trim($delegateId) == '') {
	$delegateId = $paymentRequestDetails['delegate_id'];
}
if (trim($slipId) == '') {
	$slipId = $paymentRequestDetails['slip_id'];
}


// FETCHING USER DETAILS
$rowFetchUserDetails        	= getUserDetails($delegateId);

$user_id                    	= $rowFetchUserDetails['id'];
$user_first_name            	= $rowFetchUserDetails['user_first_name'];
$user_middle_name           	= $rowFetchUserDetails['user_middle_name'];
$user_last_name             	= $rowFetchUserDetails['user_last_name'];
$user_full_name             	= $rowFetchUserDetails['user_full_name'];
$user_password              	= $mycms->decoded($rowFetchUserDetails['user_password']);
$user_email_id              	= $rowFetchUserDetails['user_email_id'];
$user_mobile_no             	= $rowFetchUserDetails['user_mobile_no'];

$user_unique_sequence           = $rowFetchUserDetails['user_unique_sequence'];
$user_registration_id           = $rowFetchUserDetails['user_registration_id'];

$registerForArray               = array();
$payCurrency 					= getRegistrationCurrency(getUserClassificationId($delegateId));

$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

if (floatval($pendingAmountOfSlip) > 0 && floatval($pendingAmountOfSlip) != floatval($invoiceAmountOfSlip)) {
	$serviceRoundOffPrice			= $pendingAmountOfSlip;
} else {
	$serviceRoundOffPrice			= invoiceAmountOfSlip($slipId);
}



$user_conference_title          = $rowFetchUserDetails['classification_title'];
$user_workshop_title            = "-";
$user_accompany_count           = "0";
$user_accommodation_details     = "-";
$user_tour_details              = "-";

if (isset($_REQUEST['FROM']) && $_REQUEST['FROM'] == 'WL') {

	require_once 'PG/PHP_WORLDLINE/TransactionResponseBean.php';


	$paymentGateway = 'WORLDLINE';

	// echo '<pre>'; print_r($_REQUEST);die;

	// include_once("PG/WorldLine/AWLMEAPI.php");

	// $obj = new AWLMEAPI();
	// $resMsgDTO = new ResMsgDTO();
	// $reqMsgDTO = new ReqMsgDTO();
	// $responseMerchant = $_REQUEST['merchantResponse'];

	// if(isset($_REQUEST['MODE']) && $_REQUEST['MODE']=='DEMO')
	// {
	// 	$response = $obj->parseTrnResMsg( $responseMerchant , $cfg['TEST.WL_ENC_KEY'] );
	// 	$Merchantclientcode  = $cfg['TEST.WL_MERCHANT_ID'];
	// }
	// else
	// {
	// 	$response = $obj->parseTrnResMsg( $responseMerchant , $cfg['WL_ENC_KEY'] );
	// 	$Merchantclientcode  = $cfg['WL_MERCHANT_ID'];
	// }


	// $online_transaction_req_id      = addslashes($_REQUEST['HAND']);
	// $mmp_txn                    	= $response->getpgMeTrnRefNo();
	// $mer_txn                    	= $response->getorderId();
	// $amt                        	= $response->gettrnAmt()/100;
	// $surcharge                  	= 0;
	// $prodid                     	= '';
	// $date                       	= $response->gettrnReqDate();
	// $bank_txn                   	= $response->getrrn();
	// $f_code                     	= ($response->getStatusCode()=='S')?'Ok':'';
	// $clientcode                 	= $Merchantclientcode;
	// $bank_name                  	= '';	
	// $discriminator              	= '';
	// $add10							= json_decode(trim($response->getaddField10()), true);
	// $cardNumber                 	= $add10['card'];
	// $paymentAmount              	= 0; 


	if (isset($_POST['msg'])) {
		$response = $_POST;
		if (is_array($response)) {
			$str = $response['msg'];
		} else if (is_string($response) && strstr($response, 'msg=')) {
			$outputstr = str_replace('msg=', '', $response);
			$outputArr = explode('&', $outputstr);
			$str = $outputArr[0];
		} else {
			$str = $response;
		}


		$parameters = file_get_contents("PG/PHP_WORLDLINE/parameters.json");
		$data = json_decode($parameters, true);

		$transactionResponseBean = new TransactionResponseBean();
		$transactionResponseBean->setResponsePayload($str);
		$transactionResponseBean->key = $data['key'];
		$transactionResponseBean->iv = $data['iv'];
		$response = $transactionResponseBean->getResponsePayload();

		//Writing in Response Log
		$log  = "Date : " . date("F j, Y, g:i a") . "; Response Data : " . $response . PHP_EOL;

		//Saving string to log by using "FILE_APPEND" to append.
		file_put_contents('PG/PHP_WORLDLINE/logs/response/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);

		$response_n = explode("|", $response);
		// echo '<pre>';print_r($response_n);echo '</pre>'; // die;

		$result = [];
		foreach ($response_n as $item) {
			if (strpos($item, '=') !== false) {
				list($key, $value) = explode('=', $item, 2);
				$result[$key] = $value;
			}
		}

		$online_transaction_req_id      = addslashes($_REQUEST['HAND']);
		$mmp_txn                    	= $result['tpsl_txn_id'];
		$mer_txn                    	= $result['clnt_txn_ref'];
		$amt                        	= $result['txn_amt'];
		$surcharge                  	= 0;
		$prodid                     	= '';
		$date                       	= $result['tpsl_txn_time'];
		$bank_txn                   	= $result['tpsl_bank_cd'];
		$f_code                     	= ($result['txn_msg'] == 'success' || $result['txn_status'] == '0300') ? 'Ok' : ($result['txn_msg'] == 'Aborted' ? 'C' : 'F');
		$clientcode                 	= '';//$result['clnt_rqst_meta'];//$Merchantclientcode;
		$bank_name                  	= '';
		$discriminator              	= '';
		// $add10							= json_decode(trim($response->getaddField10()), true);
		 $cardNumber                 	= '';//$result['rqst_token'] . "|HASH:" . $result['hash'];
		$paymentAmount              	= 0;
	}
} else {
	// PAYMENT GATEWAY [ATOM] PARAMETERS
	$online_transaction_req_id      = addslashes($_REQUEST['HAND']);
	$mmp_txn                    	= addslashes($_REQUEST['mmp_txn']);
	$mer_txn                    	= addslashes($_REQUEST['mer_txn']);
	$amt                        	= addslashes($_REQUEST['amt']);
	$surcharge                  	= addslashes($_REQUEST['surcharge']);
	$prodid                     	= addslashes($_REQUEST['prodid']);
	$date                       	= addslashes($_REQUEST['date']);
	$bank_txn                   	= addslashes($_REQUEST['bank_txn']);
	$f_code                     	= addslashes($_REQUEST['f_code']);
	$clientcode                 	= addslashes($_REQUEST['clientcode']);
	$bank_name                  	= addslashes($_REQUEST['bank_name']);
	$discriminator              	= addslashes($_REQUEST['discriminator']);
	$cardNumber                 	= addslashes($_REQUEST['CardNumber']);
	$paymentAmount              	= 0;
}

$transactionRawData         	= $mycms->_getValueOf($_REQUEST);
$transactionRawData         	= addslashes($transactionRawData);
$resultInvoice 					= invoiceDetailsActiveOfSlip($slipId);

if (strtolower($_SERVER['HTTP_HOST']) == 'localhost') {
	$mmp_txn                    	= '12345';
	$mer_txn                    	= 'demo';
	$amt                        	= $serviceRoundOffPrice;
	$surcharge                  	= 'demo';
	$prodid                     	= 'demo';
	$date                       	= date('d/m/Y');
	$bank_txn                   	= 'demo';
	$f_code                     	= 'Ok';
	$clientcode                 	= base64_encode($user_unique_sequence);
	$bank_name                  	= 'demo';
	$discriminator              	= 'demo';
	$cardNumber                 	= '4111111111111';
	$paymentAmount              	= 0;
	$transactionRawData         	= $mycms->_getValueOf($_REQUEST);
	$transactionRawData         	= addslashes($transactionRawData);
	$resultInvoice 					= invoiceDetailsActiveOfSlip($slipId);
}

$logfileName = 'logs/log.atom_payment_dr.' . $delegateId . '.txt';
file_put_contents($logfileName, date("Y-m-d H:i:s.u") . ":: DATA :: response => " . $mycms->_getValueOf($_REQUEST) . PHP_EOL, FILE_APPEND | LOCK_EX);
file_put_contents($logfileName, "" . PHP_EOL, FILE_APPEND | LOCK_EX);


// INSERTING PAYMENT RAW DATA
$paymentStatus                  = "";

if ($f_code == "Ok") {
	$paymentStatus              = "SUCCESS";
} else {

	$paymentStatus              = "FAILURE";
}

$sqlInsertPaymentData           = array();
$sqlInsertPaymentData['QUERY']   	= "INSERT INTO " . _DB_PAYMENT_RAW_DATA_ . " 
											   SET `slip_id` = ?, 
												   `delegate_id` = ?,
												   `raw_data` = ?,
												   `payment_status` = ?,
												   `payment_mode` = ?, 
												   `payment_date` = ?,
												   `online_transaction_gateway` = ?,
												   `atom_atom_transaction_id` = ?,
												   `atom_merchant_transaction_id` = ?,
												   `atom_transaction_amount` = ?,
												   `atom_surcharge` = ?,
												   `atom_product_id` = ?,
												   `atom_bank_transaction_id` = ?,
												   `atom_f_code` = ?,
												   `atom_transaction_bank_name` = ?,
												   `atom_discriminator` = ?,
												   `atom_transaction_card_no` = ?,
												   `amount` = ?,
												   `status` = ?, 
												   `created_ip` = ?, 
												   `created_sessionId` = ?,
												   `created_browser` = ?,
												   `created_dateTime` = ?";

$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'slip_id',                         'DATA' => $slipId,              'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'delegate_id',                     'DATA' => $delegateId,          'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'raw_data',                        'DATA' => $transactionRawData,  'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'payment_status',                  'DATA' => $paymentStatus,       'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'payment_mode',                    'DATA' => 'Online',             'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'payment_date',                    'DATA' => date('Y-m-d'),        'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'online_transaction_gateway',      'DATA' => $paymentGateway,      'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_atom_transaction_id',        'DATA' => $mmp_txn,             'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_merchant_transaction_id',    'DATA' => $mer_txn,             'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_transaction_amount',         'DATA' => $paymentAmount,       'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_surcharge',                  'DATA' => $surcharge,           'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_product_id',                 'DATA' => $prodid,              'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_bank_transaction_id',        'DATA' => $bank_txn,            'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_f_code',                     'DATA' => $f_code,              'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_transaction_bank_name',      'DATA' => $bank_name,           'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_discriminator',              'DATA' => $discriminator,       'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'atom_transaction_card_no',        'DATA' => $cardNumber,                   'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'amount',                          'DATA' => $paymentAmount,                'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'status',                          'DATA' => 'A',                           'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'created_ip',                      'DATA' => $_SERVER['REMOTE_ADDR'],       'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'created_sessionId',               'DATA' => session_id(),                  'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'created_browser',                 'DATA' => $_SERVER['HTTP_USER_AGENT'],   'TYP' => 's');
$sqlInsertPaymentData['PARAM'][]  = array('FILD' => 'created_dateTime',                'DATA' => date('Y-m-d H:i:s'),           'TYP' => 's');

$mycms->sql_insert($sqlInsertPaymentData, false);

// INSERTING PAYMENT DETAILS TO PROJECT DB
if ($f_code == "Ok") {
	// COUNTING PREVIOUS PAYMENT RECORD
	$sqlFetchPayment  = array();
	$sqlFetchPayment['QUERY']        = "SELECT * FROM " . _DB_PAYMENT_ . " 
											   WHERE `delegate_id` = ? 
											     AND `slip_id` = ? 
												 AND `payment_mode` = ? 
												 AND `online_transaction_gateway` = ?
												 AND `atom_atom_transaction_id` =?
												 AND `atom_merchant_transaction_id` = ?
												 AND `atom_transaction_amount` = ?
												 AND `atom_bank_transaction_id` = ?
												 AND `atom_f_code` = ?";

	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'delegate_id',                  'DATA' => $delegateId,            'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'slip_id',                      'DATA' => $slipId,                'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'payment_mode',                 'DATA' => 'Online',               'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'online_transaction_gateway',   'DATA' => $paymentGateway,        'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'atom_atom_transaction_id',     'DATA' => $mmp_txn,               'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'atom_merchant_transaction_id', 'DATA' => $mer_txn,               'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'atom_transaction_amount',      'DATA' => $paymentAmount,         'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'atom_bank_transaction_id',     'DATA' => $bank_txn,              'TYP' => 's');
	$sqlFetchPayment['PARAM'][]  = array('FILD' => 'atom_f_code',                  'DATA' => $f_code,                'TYP' => 's');

	$resultPayment          	= $mycms->sql_select($sqlFetchPayment);
	$paymentNumRows         	= $mycms->sql_numrows($resultPayment);

	if ($paymentNumRows == 0) {
		$paymentAmount          = $amt;

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

	$mycms->redirect("online.payment.success.php");
} else {
	online_conference_payment_failure_message($delegateId, 'SEND');
	$mycms->setSession('SLIP_ID', $slipId); // slip_id was not found from the session in payment failure page, so set it.
	$mycms->redirect("online.payment.failure.php");
}
