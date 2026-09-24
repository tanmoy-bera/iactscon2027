<?php
include_once('function.registration.php');
include_once("function.accommodation.php");
function getInvoiceTypeString($delegateId = "", $reqId = "", $type = "", $reqStatus = "A")
{
	global $cfg, $mycms;
	include_once('function.dinner.php');
	include_once('function.workshop.php');
	$thisUserDetails = getUserDetails($delegateId);

	$morningSession = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
	if ($type == "CONFERENCE") {
		$string = "<b>Conference Registration -</b> " . getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
	}
	if ($type == "EXHIBITOR") {
		$string = "<b>Representative<br>Registration -</b> " . getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
	}
	if ($type == "RESIDENTIAL") {
		$string = getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
	}
	if ($type == "WORKSHOP") {
		$workShopDetails = getWorkshopDetails($reqId, true);
		//$string =  ($workShopDetails['workshop_id']==3?'WORKSHOP':strtoupper(getWorkshopName($workShopDetails['workshop_id'])));
		$string =  '<b>Workshop Registration ' . '</b><br>' . strtoupper(getWorkshopName($workShopDetails['workshop_id'], true) . ' <br> ');
	}
	if ($type == "ACCOMPANY") {
		$accompanyDetails = getUserDetails($reqId);
		if ($accompanyDetails['registration_request'] == 'GUEST') {
			$string  = "<b>Accompanying Guest</b><br>  <u>" . $accompanyDetails['user_full_name'] . "</u>";
		} else {
			$string  = "<b>Accompanying Person Registration</b><br>  <u>" . $accompanyDetails['user_full_name'] . "</u>";
		}
	}
	if ($type == "ACCOMMODATION") {
		$accmDetails = getAccomodationDetails($reqId, true);
		//echo "<pre>"; print_r($accmDetails); echo "</pre>";
		$string  = "<b>Accommodation Booking -</b> " . getAccomPackageName($accmDetails['package_id']) . "<br /><span style='color:#F94E29;'>" . $accmDetails['checkin_date'] . " to " . $accmDetails['checkout_date'] . "</span>";
		if ($accmDetails['accompany_name'] != '') {
			$string  .= "<br> <b>Sharing Room With - </b>" . $accmDetails['accompany_name'] . "";
		}
	}
	if ($type == "TOUR") {
		$tourDetails = getTourDetails($reqId, true);
		$string  = getTourName($tourDetails['package_id']) . " Booking";
	}
	if ($type == "DINNER") {
		$dinnerDetails = getDinnerDetails($reqId);
		$getDinnerDetailsOfDelegate = getDinnerDetailsOfDelegate($dinnerDetails['refference_id']);
				
		$dinner_user_type = dinnerForWhome($dinnerDetails['refference_id']);
		$dinnerUserDetails     = getUserDetails($dinnerDetails['refference_id']);
		if ($dinner_user_type == 'ACCOMPANY') {
			$string  = $getDinnerDetailsOfDelegate['dinner_classification_name'] . "-".$getDinnerDetailsOfDelegate['dinner_hotel_name']."<br>" . $getDinnerDetailsOfDelegate['dinnerDate'] . "</br> <u>" . $dinnerUserDetails['user_full_name'] . "</u>";
		} else {
			$string  = $getDinnerDetailsOfDelegate['dinner_classification_name'] . "-".$getDinnerDetailsOfDelegate['dinner_hotel_name']."</br>". $getDinnerDetailsOfDelegate['dinnerDate'] ."</br> <u>" . $dinnerUserDetails['user_full_name'] . "</u>";
		}
	}
	return $string;
}
function getInvoiceTypeSummary($delegateId = "", $reqId = "", $type = "", $reqStatus = "A")
{
	global $cfg, $mycms;
	include_once('function.dinner.php');
	include_once('function.workshop.php');
	$thisUserDetails = getUserDetails($delegateId);

	$morningSession = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
	if ($type == "CONFERENCE") {
		$string =  getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
	}
	if ($type == "EXHIBITOR") {
		$string = "<b>Representative<br>Registration -</b> " . getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
	}
	if ($type == "RESIDENTIAL") {
		$string = getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
	}
	if ($type == "WORKSHOP") {
		$workShopDetails = getWorkshopDetails($reqId, true);
		//$string =  ($workShopDetails['workshop_id']==3?'WORKSHOP':strtoupper(getWorkshopName($workShopDetails['workshop_id'])));
		$string =   strtoupper(getWorkshopName($workShopDetails['workshop_id'], true) . ' <br> ');
	}
	if ($type == "ACCOMPANY") {
		$accompanyDetails = getUserDetails($reqId);
		if ($accompanyDetails['registration_request'] == 'GUEST') {
			$string  = "<b>Accompanying Guest</b><br>  <u>" . $accompanyDetails['user_full_name'] . "</u>";
		} else {
			// $string  = "<b>Accompanying Person -</br><u>".  $accompanyDetails['user_full_name']."</u>";
			$string  =  "Accompanying Person-".$accompanyDetails['user_full_name'];
		}
	}
	if ($type == "ACCOMMODATION") {
		$accmDetails = getAccomodationDetails($reqId, true);
		//echo "<pre>"; print_r($accmDetails); echo "</pre>";
		$string  = "<b>Accommodation-</b> " . getAccomPackageName($accmDetails['package_id']) . "<br /><span style='color:#F94E29;'>" . $accmDetails['checkin_date'] . " to " . $accmDetails['checkout_date'] . "</span>";
		if ($accmDetails['accompany_name'] != '') {
			$string  .= "<br> <b>Sharing Room With - </b>" . $accmDetails['accompany_name'] . "";
		}
	}
	if ($type == "TOUR") {
		$tourDetails = getTourDetails($reqId, true);
		$string  = getTourName($tourDetails['package_id']) . " Booking";
	}
	if ($type == "DINNER") {
		$dinnerDetails = getDinnerDetails($reqId);
		$getDinnerDetailsOfDelegate = getDinnerDetailsOfDelegate($dinnerDetails['refference_id']);
				
		$dinner_user_type = dinnerForWhome($dinnerDetails['refference_id']);
		$dinnerUserDetails     = getUserDetails($dinnerDetails['refference_id']);
		if ($dinner_user_type == 'ACCOMPANY') {
			$string  = $getDinnerDetailsOfDelegate['dinner_classification_name'] . "-".$getDinnerDetailsOfDelegate['dinner_hotel_name']."<br>" . $getDinnerDetailsOfDelegate['dinnerDate'] . "</br> <u>" . $dinnerUserDetails['user_full_name'] . "</u>";
		} else {
			$string  = $getDinnerDetailsOfDelegate['dinner_classification_name'] . "-".$getDinnerDetailsOfDelegate['dinner_hotel_name']."</br>". $getDinnerDetailsOfDelegate['dinnerDate'] ."</br> <u>" . $dinnerUserDetails['user_full_name'] . "</u>";
		}
	}
	return $string;
}

function getInvoiceDetails($invoiceId, $status = false)
{
	global $cfg, $mycms;

	if ($status) {
		$condition = " AND invDetails.status IN (?,?)";
		$conditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' => 'A',  'TYP' => 's');
		$conditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' => 'C',  'TYP' => 's');
	} else {
		$condition = " AND invDetails.status IN (?)";
		$conditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' => 'A',  'TYP' => 's');
	}

	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = "SELECT invDetails.*, invDetails.delegate_id AS delegateId, slipDetails.slip_number AS slipNO 
							 FROM  " . _DB_INVOICE_ . " invDetails
				  LEFT OUTER JOIN " . _DB_SLIP_ . " slipDetails
							   ON slipDetails.id = invDetails.slip_id
							WHERE invDetails.id =? 
								  " . $condition;
	$sqlInvoice['PARAM'][]   = array('FILD' => 'invDetails.id',  'DATA' => $invoiceId, 'TYP' => 's');
	//echo '<pre>'; print_r($sqlInvoice);						  
	foreach ($conditionPARAM as $k => $val) {
		$sqlInvoice['PARAM'][] = $val;
	}

	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice[0];
}

function getInvoiceRecords($invoiceId = "", $delegateId = "", $slipId = "")
{
	global $cfg, $mycms;

	$filterCondition           = "";
	$filterParam           	   = array();

	if ($invoiceId != "") {
		$filterCondition      .= " AND id = ?";
		$filterParam['id']	   = $invoiceId;
	}

	if ($delegateId != "") {
		$filterCondition      		  .= " AND delegate_id = ?";
		$filterParam['delegate_id']	   = $delegateId;
	}

	if ($slipId != "") {
		$filterCondition      	  .= " AND slip_id = ?";
		$filterParam['slip_id']	   = $slipId;
	}
	$sqlFetchInvoiceDtls['QUERY']   = "SELECT * FROM " . _DB_INVOICE_ . " WHERE `status` IN ('A','C') " . $filterCondition . " ORDER BY id ASC";
	foreach ($filterParam as $key => $val) {
		$sqlFetchInvoiceDtls['PARAM'][]   = array('FILD' => $key,  			'DATA' => $val, 			'TYP' => 's');
	}

	return $mycms->sql_select($sqlFetchInvoiceDtls);;
}

function getAllSlipsOfDelegate($delegateId)
{
	global $cfg, $mycms;

	$sqlSlip['QUERY']  = "  SELECT slip.id
							  FROM " . _DB_SLIP_ . " slip			  
				   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
										   `slip_id`
									 FROM  " . _DB_INVOICE_ . " 
									WHERE `status` = ?
								 GROUP BY `slip_id` ) activeInvoice
									   ON slip.id = activeInvoice.slip_id 
							 WHERE slip.delegate_id = ? 
							   AND slip.status = ?
							   AND activeInvoice.invoiceCount>0";
	$sqlSlip['PARAM'][]   = array('FILD' => 'status',  			'DATA' => 'A', 			'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'delegate_id',  	'DATA' => $delegateId, 	'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'status',  			'DATA' => 'A', 			'TYP' => 's');

	$resSlip   = $mycms->sql_select($sqlSlip);

	$slipIdArr = array();
	if ($resSlip) {
		foreach ($resSlip as $key => $slipId) {
			$slipIdArr[] = $slipId['id'];
		}
	}
	return $slipIdArr;
}

function getFinancialSummaryOfDelegate($delegateId)
{
	global $cfg, $mycms;

	$summary	= array();

	$slips 					= getAllSlipsOfDelegate($delegateId);
	$totalSlipAmount		= 0;
	$totalDiscountAmount	= 0;
	$totalPaidAmount		= 0;

	$totalSettlementAmount	= 0;
	$settlementDates		= array();

	foreach ($slips as $k => $slipId) {
		$summary['SLIP'][$slipId]['DETAILS'] = slipDetails($slipId);
		$summary['SLIP'][$slipId]['DETAILS']['INVOICES'] = invoiceDetailsOfSlip($slipId);
		$summary['SLIP'][$slipId]['SERVICES'] = servicesOfSlip($slipId);


		$amoutOfThisSlip = floatval(invoiceAmountOfSlip($slipId, true));
		$summary['SLIP'][$slipId]['AMOUNT'] = $amoutOfThisSlip;
		$totalSlipAmount += $amoutOfThisSlip;

		$discountOfThisSlip = floatval(discountAmountOfSlip($slipId));
		$summary['SLIP'][$slipId]['DISCOUNT'] = $discountOfThisSlip;
		$totalDiscountAmount += $discountOfThisSlip;

		$paymentsDetails = paymentDetails($slipId);

		$slipSettlementAmount	= 0;
		$slipSettlementDates	= array();

		foreach ($paymentsDetails['paymentDetails'] as $kl => $payments) {
			if ($payments['payment_status'] == 'PAID') {
				$paidAmt = floatval($payments['amount']);
				$summary['SLIP'][$slipId]['PAID_AMOUNT'] += $paidAmt;
				$totalPaidAmount += $paidAmt;

				if (in_array('DELEGATE_CONFERENCE_REGISTRATION', $summary['SLIP'][$slipId]['SERVICES']) || in_array('DELEGATE_RESIDENTIAL_REGISTRATION', $summary['SLIP'][$slipId]['SERVICES'])) {
					$summary['REG_LAST_PAYMENT_ON'] = $payments['payment_date'];
				}

				$summary['SLIP'][$slipId]['LAST_PAYMENT_ON'] = $payments['payment_date'];
			}

			if ($payments['sattlement_ammount'] > 0) {
				$totalSettlementAmount 	   += $payments['sattlement_ammount'];
				$settlementDates[] 			= $payments['settlement_date'];
				$slipSettlementAmount 	   += $payments['sattlement_ammount'];
				$slipSettlementDates[] 		= $payments['settlement_date'];
			}
		}

		$summary['SLIP'][$slipId]['PAYMENTS']['RAW'] 				= $paymentsDetails;
		$summary['SLIP'][$slipId]['PAYMENTS']['COUNT'] 				= sizeof($paymentsDetails['paymentDetails']);
		$summary['SLIP'][$slipId]['PAYMENTS']['SETTLED_AMOUNT'] 	= $slipSettlementAmount;
		$summary['SLIP'][$slipId]['PAYMENTS']['SETTLEMENT_DATE'] 	= implode(",", $slipSettlementDates);
	}

	$summary['PAID-TOTAL'] 			= $totalSlipAmount;
	$summary['REFUND-TOTAL']		= 0;

	$sqlCanceledInvoice['QUERY']	= " SELECT *,
											   invoice.invoice_number AS invoiceNumber,
											   invoice.id AS invoiceId,
											   invoice.service_roundoff_price AS invoiceRoundPrice,
											   slip.slip_number AS slipNumber,
											   invoiceUser.registration_mode AS registrationMode,
											   invoiceUser.id AS userId,
											   invoiceUser.user_full_name AS invoiceUserName,
											   invoice.service_type AS invoiceFor,
											   cancelInv.refunded_datetime AS invoiceDate,
											   cancelInv.refunded_amount AS refunded_amount,
											   invoice.refference_id AS reqId,
											   invoiceUser.registration_classification_id AS registrationClassificationId,
											   invoiceUser.user_registration_id AS user_registration_id,
											   invoiceUser.user_unique_sequence AS user_unique_sequence,
											   invoiceUser.user_email_id AS user_email_id,
											   invoiceUser.user_mobile_no AS user_mobile_no,
											   slipUser.user_full_name AS slipUserName,
											   slipUser.user_registration_id AS slipUser_registration_id,
											   slipUser.user_unique_sequence AS slipUser_unique_sequence,
											   slipUser.user_email_id AS slipUser_email_id,
											   slipUser.user_mobile_no AS slipUser_mobile_no
										   
										 FROM " . _DB_CANCEL_INVOICE_ . " cancelInv
									
									INNER JOIN " . _DB_INVOICE_ . " invoice
											ON cancelInv.invoice_id = invoice.id
													
									INNER JOIN " . _DB_SLIP_ . " slip
											ON invoice.slip_id = slip.id
											
									INNER JOIN " . _DB_USER_REGISTRATION_ . " invoiceUser
											ON invoice.delegate_id = invoiceUser.id
											AND invoiceUser.status IN ('A','C')
													
									INNER JOIN " . _DB_USER_REGISTRATION_ . " slipUser
											ON slip.delegate_id = slipUser.id
										 WHERE cancelInv.status 	= 'A' 
										   AND invoice.payment_status = 'PAID' 
										   AND cancelInv.Refund_status 	= 'Refunded' 
										   AND invoiceUser.id = '" . $delegateId . "'
									  ORDER BY cancelInv.id DESC";
	$resultCanceledInvoice          = $mycms->sql_select($sqlCanceledInvoice);

	if ($resultCanceledInvoice) {
		foreach ($resultCanceledInvoice as $key => $rowFetchInvoice) {
			$summary['REFUND-TOTAL'] += floatval($rowFetchInvoice['refunded_amount']);
		}
	}

	$summary['TOTAL'] = $summary['PAID-TOTAL'] - $summary['REFUND-TOTAL'];

	if ($totalDiscountAmount == '') {
		$totalDiscountAmount = 0;
	}
	$summary['TOTAL_DISCOUNT'] 	= $totalDiscountAmount;
	if ($totalPaidAmount == '') {
		$totalPaidAmount = 0;
	}
	$summary['PAID'] 			= $totalPaidAmount - $summary['REFUND-TOTAL'];
	$summary['PENDING'] 		= $totalSlipAmount - $totalPaidAmount;

	$summary['SETTLED_AMOUNT'] 	= $totalSettlementAmount;
	$summary['SETTLEMENT_DATE'] = implode(",", $settlementDates);

	return $summary;
}

function getFinancialSummaryOfSlip($slipId)
{
	global $cfg, $mycms;

	$summary				= array();

	$totalPaidAmount		= 0;

	$summary['DETAILS'] = slipDetails($slipId);
	$summary['DETAILS']['INVOICES'] = invoiceDetailsOfSlip($slipId);
	$summary['SERVICES'] = servicesOfSlip($slipId);


	$amoutOfThisSlip = floatval(invoiceAmountOfSlip($slipId, true));
	$summary['AMOUNT'] = $amoutOfThisSlip;


	$discountOfThisSlip = floatval(discountAmountOfSlip($slipId));
	$summary['DISCOUNT'] = $discountOfThisSlip;

	$paymentsDetails = paymentDetails($slipId);

	foreach ($paymentsDetails['paymentDetails'] as $kl => $payments) {
		if ($payments['payment_status'] == 'PAID') {
			$paidAmt = floatval($payments['amount']);
			$summary['PAID_AMOUNT'] += $paidAmt;
			$totalPaidAmount += $paidAmt;

			if (in_array('DELEGATE_CONFERENCE_REGISTRATION', $summary['SERVICES']) || in_array('DELEGATE_RESIDENTIAL_REGISTRATION', $summary['SERVICES'])) {
				$summary['REG_LAST_PAYMENT_ON'] = $payments['payment_date'];
			}

			$summary['LAST_PAYMENT_ON'] = $payments['payment_date'];
		}
	}

	$summary['PAYMENTS']['RAW'] = $paymentsDetails;
	$summary['PAYMENTS']['COUNT'] = sizeof($paymentsDetails['paymentDetails']);

	if ($totalPaidAmount == '') {
		$totalPaidAmount = 0;
	}
	$summary['PAID'] 			= $totalPaidAmount;
	$summary['PENDING'] 		= $amoutOfThisSlip - $totalPaidAmount;

	return $summary;
}

function getInvoiceDetailsquery($invoiceId = "", $delegateId = "", $slipId = "")
{
	global $cfg, $mycms;

	$filterCondition           = "";

	if ($invoiceId != "") {
		$filterCondition      .= " AND id = ? ";
		$filterConditionPARAM[]   = array('FILD' => 'id',  'DATA' => $invoiceId,  'TYP' => 's');
	}

	if ($delegateId != "") {
		$filterCondition      .= " AND delegate_id = ?";
		$filterConditionPARAM[]   = array('FILD' => 'delegate_id',  'DATA' => $delegateId,  'TYP' => 's');
	}

	if ($slipId != "") {
		$filterCondition      .= " AND slip_id = ? ";
		$filterConditionPARAM[]   = array('FILD' => 'slip_id',  'DATA' => $slipId,  'TYP' => 's');
	}
	$sqlFetchInvoiceDtls['QUERY']   = "SELECT * 
									    FROM " . _DB_INVOICE_ . " 
									   WHERE `status` IN ('A','C') " . $filterCondition . " 
									ORDER BY id ASC";
	foreach ($filterConditionPARAM as $k => $val) {
		$sqlFetchInvoiceDtls['PARAM'][] = $val;
	}
	$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoiceDtls);
	return $resultFetchInvoice;
}

function calculateTaxAmmount($ammount, $percentage)
{
	return ($ammount * $percentage) / 100;
}

function invoiceCountOfSlip($slipId)
{
	global $cfg, $mycms;

	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = "SELECT COUNT(*) AS totalInvoice 
							  FROM  " . _DB_INVOICE_ . "
							  WHERE  `slip_id` =?
								AND `status` = ?";

	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id', 'DATA' => $slipId, 'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',     'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice[0]['totalInvoice'];
}

function invoiceAmountOfSlip($slipId, $status = false, $includeDiscount = true)
{
	global $cfg, $mycms;

	if ($status) {
		$searchCondition = "AND status IN ('A','C')";
	} else {
		$searchCondition = "AND status = 'A'";
	}
	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = "SELECT SUM((CASE WHEN status = 'C' AND remarks = 'Adjusted Workshop' 
									   THEN 0 
									   ELSE `service_roundoff_price` 
									   END)) AS totalAmount 
							 FROM " . _DB_INVOICE_ . " 
							WHERE `slip_id` = ? " . $searchCondition . "";

	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id', 'DATA' => $slipId, 'TYP' => 's');
// echo '<pre>'; print_r($sqlInvoice); die();

	$resInvoice = $mycms->sql_select($sqlInvoice);
	$rowInvoice = $resInvoice[0];
	$invoiceAmount = ($rowInvoice['totalAmount'] == '') ? 0.00 : floatval($rowInvoice['totalAmount']);

	$totalSlipAmount = $invoiceAmount;
	return $totalSlipAmount;
}

function pendingAmountOfSlip($slipId)
{
	global $cfg, $mycms;

	$amoutOfThisSlip = floatval(invoiceAmountOfSlip($slipId, true));

	$paymentsDetails = paymentDetails($slipId);

	$totalPaidAmount = 0;

	foreach ($paymentsDetails['paymentDetails'] as $kl => $payments) {
		if ($payments['payment_status'] == 'PAID') {
			$paidAmt = floatval($payments['amount']);
			$totalPaidAmount += $paidAmt;
		}
	}

	$pendingAmount = $amoutOfThisSlip - $totalPaidAmount;

	return $pendingAmount;
}

function getPaymentRequestDetails($paymentId)
{
	global $cfg, $mycms;

	$sqlPayment 		 = array();
	$sqlPayment['QUERY'] = "SELECT *
							 FROM " . _DB_PAYMENT_REQUEST_ . " 
							WHERE `id` = '" . $paymentId . "'";
	$resPayment = $mycms->sql_select($sqlPayment);
	return $resPayment[0];
}

function discountAmountOfSlip($slipId, $status = false)
{
	global $cfg, $mycms;
	$returnDiscount = 0;
	if ($status) {
		$searchCondition = "AND inv.status IN ('A','C')";
	} else {
		$searchCondition = "AND inv.status = 'A'";
	}

	$sqlInvoice['QUERY'] = "SELECT id 
							  FROM " . _DB_INVOICE_ . " inv
							 WHERE inv.`slip_id` ='" . $slipId . "' 
						  " . $searchCondition . "";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	foreach ($resInvoice as $key => $rowInvoice) {
		$amount = discountAmount($rowInvoice[''], $searchCondition);
		$returnDiscount  += $amount['DISCOUNT'] - ($amount['SGST_DISCOUNT'] + $amount['CGST_DISCOUNT']);
	}
	return $returnDiscount;
}

function invoiceIntHandAmountOfSlip($slipId)
{
	global $cfg, $mycms;
	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = "SELECT SUM(`internet_handling_amount`) AS totalAmount 
							FROM  " . _DB_INVOICE_ . " 
							WHERE  `slip_id` ='" . $slipId . "'
						   AND `status` IN ('A','C')";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice[0]['totalAmount'];
}

function invoiceServiceAmountOfSlip($slipId)
{
	global $cfg, $mycms;
	$sqlInvoice['QUERY'] = "SELECT SUM(`service_product_price`) AS totalAmount FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` ='" . $slipId . "' AND `status` = 'A'";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice[0]['totalAmount'];
}

function servicesOfSlip($slipId)
{
	global $cfg, $mycms;
	$sqlInvoice 	=	array();
	$sqlInvoice['QUERY'] = "SELECT DISTINCT service_type 
							  FROM " . _DB_INVOICE_ . " 
							  WHERE  `slip_id` =? 
							  AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id', 'DATA' => $slipId,  'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$serviceList = array();
	foreach ($resInvoice as $k => $rowInvoice) {
		$serviceList[] = $rowInvoice['service_type'];
	}
	return $serviceList;
}

function invoiceDetailsOfSlip($slipId)
{
	global $cfg, $mycms;
	$sqlInvoice  = array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " 
							WHERE  `slip_id` =?
							AND `status` = ?
							ORDER BY delegate_id";

	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',  'DATA' => $slipId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',       'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice;
}

function invoiceDetailsOfDelegate($delegateId, $searchCondition = "")
{
	global $cfg, $mycms;

	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = " SELECT * FROM  " . _DB_INVOICE_ . "
							 WHERE  `delegate_id` =?
							   AND `status` = ?
							  " . $searchCondition . "";

	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId, 'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'A',         'TYP' => 's');

	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice;
}

function invoiceDetailsActiveOfSlip($slipId)
{
	global $cfg, $mycms;
	$sqlInvoice =	array();
	$sqlInvoice['QUERY'] = "SELECT * 
							 FROM  " . _DB_INVOICE_ . " 
							WHERE  `slip_id` =? 
							  AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',  'DATA' => $slipId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   	   'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice;
}

function AddOnInvoiceDetailsActiveOfSlip($slipId)
{
	global $cfg, $mycms;
	$sqlInvoice 	=	array();
	$sqlInvoice['QUERY'] = " SELECT * 
							   FROM  " . _DB_INVOICE_ . " invDetails
					LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " userDetails
								 ON userDetails.id = invDetails.delegate_id
							  WHERE invDetails.slip_id =? 
								AND invDetails.status = ?";

	$sqlInvoice['PARAM'][]	=	array('FILD' => 'invDetails.slip_id', 	  'DATA' => $slipId,             'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'invDetails.status', 	  'DATA' => 'A',                 'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice;
}

function slipDetailsOfUser($delegateId, $status = false)
{
	global $cfg, $mycms;

	if ($status) {
		$condition = " AND status IN ('A','C')";
	} else {
		$condition = " AND status IN ('A')";
	}

	$sqlSlip 	= array();
	$sqlSlip['QUERY']  = "SELECT IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
								slip.*
						   FROM " . _DB_SLIP_ . " slip			  
			   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
									   `slip_id`
						  FROM  " . _DB_INVOICE_ . " 
						 WHERE 1 " . $condition . "
						GROUP BY `slip_id` ) activeInvoice
							  ON slip.id = activeInvoice.slip_id 
						 WHERE slip.delegate_id = ? 
						   AND slip.status = ?
						   AND activeInvoice.invoiceCount >?";

	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.delegate_id', 	       'DATA' => $delegateId,             'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.status', 	  	       'DATA' => 'A',             		   'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'activeInvoice.invoiceCount', 'DATA' => 0,             		   'TYP' => 's');
	$resSlip   = $mycms->sql_select($sqlSlip);
	return $resSlip;
}

function slipDetailsOfUserSpecific($delegateId, $status = false, $slip_id)
{
	global $cfg, $mycms;

	if ($status) {
		$condition = " AND status IN ('A','C')";
	} else {
		$condition = " AND status IN ('A')";
	}

	$sqlSlip 	= array();
	$sqlSlip['QUERY']  = "SELECT IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
								slip.*
						   FROM " . _DB_SLIP_ . " slip			  
			   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
									   `slip_id`
						  FROM  " . _DB_INVOICE_ . " 
						 WHERE 1 " . $condition . "
						GROUP BY `slip_id` ) activeInvoice
							  ON slip.id = activeInvoice.slip_id 
						 WHERE slip.delegate_id = ? 
						   AND slip.status = ?
						   AND slip.id = ?
						   AND activeInvoice.invoiceCount >?";

	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.delegate_id', 	       'DATA' => $delegateId,             'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.status', 	  	       'DATA' => 'A',             		   'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.id', 	  	           'DATA' => $slip_id,             		   'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'activeInvoice.invoiceCount', 'DATA' => 0,             		   'TYP' => 's');
	$resSlip   = $mycms->sql_select($sqlSlip);

	return $resSlip;
}


function getSlipOwner($slipId)
{
	global $cfg, $mycms;
	$sDetails = slipDetails($slipId);
	$delegateId = $sDetails['delegate_id'];
	$rowDelegate = getUserDetails($delegateId);
	if ($rowDelegate) {

		$user_full_name = str_replace('..', '.', $rowDelegate['user_title']) . " " . $rowDelegate['user_first_name'] . " " . $rowDelegate['user_middle_name'] . " " . $rowDelegate['user_last_name'];
		$length			= strlen($user_full_name);
		if ($length > 12) {
			$shortFirstNames = explode(' ', $rowDelegate['user_first_name']);
			foreach ($shortFirstNames as $fNames) {
				$shortFirstName  .= ' ' . substr($fNames, 0, 1);
			}

			$shortMiddleNames = explode(' ', $rowDelegate['user_middle_name']);
			foreach ($shortMiddleNames as $mNames) {
				$shortMiddleName  .= ' ' . substr($mNames, 0, 1);
			}

			$printName = str_replace('..', '.', $rowDelegate['user_title']) . $shortFirstName . $shortMiddleName . " " . $rowDelegate['user_last_name'];
		} else {
			$printName = $user_full_name;
		}
	}
	return $printName;
}

function slipDetails($slipId)
{
	global $cfg, $mycms;
	$sqlSlip['QUERY'] = "SELECT slip.*
						  FROM " . _DB_SLIP_ . " slip
						 WHERE slip.id = ? 
						   AND slip.status = ?";
	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.id', 	  'DATA' => $slipId,       'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.status',   'DATA' => 'A',      		'TYP' => 's');

	$resSlip = $mycms->sql_select($sqlSlip);

	return $resSlip[0];
}

function getSlipNumber($slipId)
{
	global $cfg, $mycms;
	$sqlSlip 	=	array();
	$sqlSlip['QUERY']    = "SELECT slip.*
							  FROM " . _DB_SLIP_ . " slip
							 WHERE slip.id =? 
							   AND slip.status = ?";

	$sqlSlip['PARAM'][]   = array('FILD' => 'slip.id',       'DATA' => $slipId,     'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'slip.status',   'DATA' => 'A',   	   'TYP' => 's');

	$resSlip = $mycms->sql_select($sqlSlip);

	return $resSlip[0]['slip_number'];
}

function paymentDetails($slipId)
{
	global $cfg, $mycms;

	$paymentDetails = array();

	$sqlPayment 	=	array();
	$sqlPayment['QUERY'] = "SELECT payment.*
							 FROM " . _DB_PAYMENT_ . " payment
							WHERE payment.slip_id = ?
							  AND payment.status = ?
							  ORDER BY `id` ASC";

	$sqlPayment['PARAM'][]   = array('FILD' => 'payment.slip_id',  'DATA' => $slipId,   'TYP' => 's');
	$sqlPayment['PARAM'][]   = array('FILD' => 'payment.status',   'DATA' => 'A',       'TYP' => 's');
	$resPayment = $mycms->sql_select($sqlPayment);
	$paymentDetails = $resPayment[0];
	$slipDetails = slipDetails($slipId);

	$paymentDetails['delegate_id'] = $slipDetails['delegate_id'];
	$paymentDetails['slip_id'] =     $slipId;
	$paymentDetails['exhibitor_code'] = $resPayment['exhibitor_code'];
	$paymentDetails['currency'] =   $slipDetails['currency'];
	$paymentDetails['totalAmount'] = invoiceAmountOfSlip($slipId);
	$paymentDetails['totalAmountPaid'] = getTotalPaidAmount($slipId);
	$paymentDetails['totalPendingAmount'] = ($paymentDetails['totalAmount'] - $paymentDetails['totalAmountPaid']);
	$paymentDetails['totalSetPaymentAmount'] = getTotalSetPaymentAmount($slipId);
	$paymentDetails['totalUnsetPaymentAmount'] = ($paymentDetails['totalAmount'] - $paymentDetails['totalSetPaymentAmount']);
	$paymentDetails['payment_type'] = $resPayment['payment_type'];

	if ($paymentDetails['totalAmount'] > $paymentDetails['totalSetPaymentAmount']) {
		$paymentDetails['has_to_set_payment'] = true;
	}
	if ($paymentDetails['totalAmount'] == $paymentDetails['totalAmountPaid']) {
		$paymentDetails['payment_status'] = 'PAID';
	} else {
		$paymentDetails['payment_status'] = 'UNPAID';
	}
	$paymentDetails['slip_invoice_mode'] = $slipDetails['invoice_mode'];
	if ($totalNoOfUnpaidCount) {
	}

	/////////////////////////////////////////////////////////////

	$sqlPayments 	=	array();
	$sqlPayments['QUERY'] = "SELECT *
							 FROM " . _DB_PAYMENT_ . " 
							WHERE `slip_id` = ?
							  AND `status` = ?
						 ORDER BY id ASC";

	$sqlPayments['PARAM'][]   = array('FILD' => 'slip_id',  'DATA' => $slipId,   'TYP' => 's');
	$sqlPayments['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',       'TYP' => 's');
	$resInnerPayment = $mycms->sql_select($sqlPayments);

	foreach ($resInnerPayment as $i => $rowInnerPayment) {

		$paymentDetails['paymentDetails'][$i]['payment_id'] = $rowInnerPayment['id'];
		$paymentDetails['paymentDetails'][$i]['payment_mode'] = $rowInnerPayment['payment_mode'];
		$paymentDetails['paymentDetails'][$i]['payment_date'] = $rowInnerPayment['payment_date'];
		$paymentDetails['paymentDetails'][$i]['cash_deposit_date'] = $rowInnerPayment['cash_deposit_date'];
		$paymentDetails['paymentDetails'][$i]['cash_document'] = $rowInnerPayment['cash_document'];
		$paymentDetails['paymentDetails'][$i]['card_payment_date'] = $rowInnerPayment['card_payment_date'];
		$paymentDetails['paymentDetails'][$i]['card_transaction_no'] = $rowInnerPayment['card_transaction_no'];
		$paymentDetails['paymentDetails'][$i]['cheque_number'] = $rowInnerPayment['cheque_number'];
		$paymentDetails['paymentDetails'][$i]['cheque_date'] = $rowInnerPayment['cheque_date'];
		$paymentDetails['paymentDetails'][$i]['cheque_bank_name'] = $rowInnerPayment['cheque_bank_name'];
		$paymentDetails['paymentDetails'][$i]['draft_number'] = $rowInnerPayment['draft_number'];
		$paymentDetails['paymentDetails'][$i]['draft_date'] = $rowInnerPayment['draft_date'];
		$paymentDetails['paymentDetails'][$i]['draft_bank_name'] = $rowInnerPayment['draft_bank_name'];
		$paymentDetails['paymentDetails'][$i]['neft_bank_name'] = $rowInnerPayment['neft_bank_name'];
		$paymentDetails['paymentDetails'][$i]['neft_transaction_no'] = $rowInnerPayment['neft_transaction_no'];
		$paymentDetails['paymentDetails'][$i]['neft_date'] = $rowInnerPayment['neft_date'];
		$paymentDetails['paymentDetails'][$i]['neft_document'] = $rowInnerPayment['neft_document'];
		$paymentDetails['paymentDetails'][$i]['rtgs_bank_name'] = $rowInnerPayment['rtgs_bank_name'];
		$paymentDetails['paymentDetails'][$i]['rtgs_transaction_no'] = $rowInnerPayment['rtgs_transaction_no'];
		$paymentDetails['paymentDetails'][$i]['rtgs_date'] = $rowInnerPayment['rtgs_date'];
		$paymentDetails['paymentDetails'][$i]['credit_date'] = $rowInnerPayment['credit_date'];
		$paymentDetails['paymentDetails'][$i]['upi_date'] = $rowInnerPayment['upi_date'];
		$paymentDetails['paymentDetails'][$i]['upi_bank_name'] = $rowInnerPayment['upi_bank_name'];
		$paymentDetails['paymentDetails'][$i]['txn_no'] = $rowInnerPayment['txn_no'];
		$paymentDetails['paymentDetails'][$i]['upi_transaction_number'] = $rowInnerPayment['upi_transaction_number'];
		$paymentDetails['paymentDetails'][$i]['payment_remark'] = $rowInnerPayment['payment_remark'];
		$paymentDetails['paymentDetails'][$i]['online_transaction_gateway'] = $rowInnerPayment['online_transaction_gateway'];
		$paymentDetails['paymentDetails'][$i]['online_transaction_req_id'] = $rowInnerPayment['online_transaction_req_id'];
		$paymentDetails['paymentDetails'][$i]['online_payment_req_id'] = $rowInnerPayment['online_payment_req_id'];
		$paymentDetails['paymentDetails'][$i]['atom_atom_transaction_id'] = $rowInnerPayment['atom_atom_transaction_id'];
		$paymentDetails['paymentDetails'][$i]['atom_merchant_transaction_id'] = $rowInnerPayment['atom_merchant_transaction_id'];
		$paymentDetails['paymentDetails'][$i]['atom_transaction_amount'] = $rowInnerPayment['atom_transaction_amount'];
		$paymentDetails['paymentDetails'][$i]['atom_surcharge'] = $rowInnerPayment['atom_surcharge'];
		$paymentDetails['paymentDetails'][$i]['atom_product_id'] = $rowInnerPayment['atom_product_id'];
		$paymentDetails['paymentDetails'][$i]['atom_transaction_date'] = $rowInnerPayment['atom_transaction_date'];
		$paymentDetails['paymentDetails'][$i]['atom_bank_transaction_id'] = $rowInnerPayment['atom_bank_transaction_id'];
		$paymentDetails['paymentDetails'][$i]['atom_f_code'] = $rowInnerPayment['atom_f_code'];
		$paymentDetails['paymentDetails'][$i]['atom_client_code'] = $rowInnerPayment['atom_client_code'];
		$paymentDetails['paymentDetails'][$i]['atom_transaction_bank_name'] = $rowInnerPayment['atom_transaction_bank_name'];
		$paymentDetails['paymentDetails'][$i]['atom_discriminator'] = $rowInnerPayment['atom_discriminator'];
		$paymentDetails['paymentDetails'][$i]['atom_transaction_card_no'] = $rowInnerPayment['atom_transaction_card_no'];
		$paymentDetails['paymentDetails'][$i]['amount'] = $rowInnerPayment['amount'];
		$paymentDetails['paymentDetails'][$i]['settlement_date'] = $rowInnerPayment['settlement_date'];
		$paymentDetails['paymentDetails'][$i]['sattlement_ammount'] = $rowInnerPayment['settlement_ammount'];
		$paymentDetails['paymentDetails'][$i]['remarks'] = $rowInnerPayment['remarks'];
		$paymentDetails['paymentDetails'][$i]['rrn_number'] = 	$rowInnerPayment['rrn_number'];
		$paymentDetails['paymentDetails'][$i]['payment_status'] = 	$rowInnerPayment['payment_status'];
		$paymentDetails['paymentDetails'][$i]['status'] = $rowInnerPayment['status'];
		$paymentDetails['paymentDetails'][$i]['collected_by'] = $rowInnerPayment['collected_by'];
		$paymentDetails['paymentDetails'][$i]['acc_ref_id'] = $rowInnerPayment['acc_ref_id'];
		$paymentDetails['paymentDetails'][$i]['created_by'] = $rowInnerPayment['created_by'];
		$paymentDetails['paymentDetails'][$i]['created_ip'] = $rowInnerPayment['created_ip'];
		$paymentDetails['paymentDetails'][$i]['created_sessionId'] = $rowInnerPayment['created_sessionId'];
		$paymentDetails['paymentDetails'][$i]['created_browser'] = $rowInnerPayment['created_browser'];
		$paymentDetails['paymentDetails'][$i]['created_dateTime'] = $rowInnerPayment['created_dateTime'];
		$paymentDetails['paymentDetails'][$i]['modified_by'] = $rowInnerPayment['modified_by'];
		$paymentDetails['paymentDetails'][$i]['modified_ip'] = $rowInnerPayment['modified_ip'];
		$paymentDetails['paymentDetails'][$i]['modified_sessionId'] = $rowInnerPayment['modified_sessionId'];
		$paymentDetails['paymentDetails'][$i]['modified_browser'] = $rowInnerPayment['modified_browser'];
		$paymentDetails['paymentDetails'][$i]['modified_dateTime'] = 	$rowInnerPayment['modified_dateTime'];
		$paymentDetails['paymentDetails'][$i]['razorpay_transaction_id'] = 	$rowInnerPayment['razorpay_transaction_id'];

		$i++;
	}

	return $paymentDetails;
}

function getTransactionDetails($delegateId)
{
	global $cfg, $mycms;
	$sqlPayment 	=	array();
	$sqlPayment['QUERY'] = "SELECT payment.*
					 		  FROM " . _DB_PAYMENT_ . " payment
							 WHERE payment.delegate_id =?";
	$sqlPayment['PARAM'][]   = array('FILD' => 'payment.delegate_id ',  'DATA' => $delegateId,   'TYP' => 's');
	$resPayment = $mycms->sql_select($sqlPayment);
	return $resPayment[0];
}

function getPaymentDetails($paymentId)
{
	global $cfg, $mycms;
	$sqlPayment 	=	array();
	$sqlPayment['QUERY'] = "SELECT payment.*
							  FROM " . _DB_PAYMENT_ . " payment
							 WHERE payment.id =?";
	$sqlPayment['PARAM'][]   = array('FILD' => 'payment.id',  'DATA' => $paymentId,   'TYP' => 's');
	$resPayment = $mycms->sql_select($sqlPayment);
	return $resPayment[0];
}

function isUnpaidSlipOfDelegate($delegateId)
{
	global $cfg, $mycms;
	$sqlSlip['QUERY']  = "SELECT IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
								slip.*
						  FROM " . _DB_SLIP_ . " slip			  
			   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
									   `slip_id`
								 FROM  " . _DB_INVOICE_ . " 
								WHERE `status` = 'A'
							 GROUP BY `slip_id` ) activeInvoice
								   ON slip.id = activeInvoice.slip_id 
						 WHERE slip.delegate_id ='" . $delegateId . "' 
						   AND slip.status = 'A'
						   AND activeInvoice.invoiceCount>0";
	$resSlip = $mycms->sql_select($sqlSlip);
	if ($resSlip) {
		foreach ($resSlip as $key => $valSlip) {
			$payment_status[] = $valSlip['payment_status'];
		}
	}
	if (in_array("UNPAID", $payment_status)) {
		return true;
	} else {
		return false;
	}
}

function isSlipOfDelegate($delegateId)
{
	global $cfg, $mycms;

	$sqlSlip['QUERY']  = "SELECT IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
								slip.*
						  FROM " . _DB_SLIP_ . " slip			  
			   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
									   `slip_id`
								 FROM  " . _DB_INVOICE_ . " 
								WHERE `status` = 'A'
							 GROUP BY `slip_id` ) activeInvoice
								   ON slip.id = activeInvoice.slip_id 
						 WHERE slip.delegate_id ='" . $delegateId . "' 
						   AND slip.status = 'A'
						   AND activeInvoice.invoiceCount>0";
	$resSlip = $mycms->sql_select($sqlSlip);
	if ($resSlip) {
		return true;
	} else {
		return false;
	}
}

function discountAmount($invoiceId, $searchCondition = "")
{
	global $cfg, $mycms;

	$returnArray     = array();
	$selectQuery     = array();
	$selectQuery['QUERY']    = "SELECT    dis.discountMode,
											dis.hasGST,
											IFNULL(dis.discount_amount,0) AS discount_amount,
											IFNULL(dis.percentage,0) AS percentage,
											(CASE WHEN inv.has_gst='Y' THEN inv.service_roundoff_price ELSE inv.service_roundoff_price END) AS service_roundoff_price,
											inv.service_grand_price AS service_grand_price,
											inv.service_product_price AS service_product_price,
											inv.service_basic_price AS service_basic_price,
											inv.cgst_price AS cgst_price,
											inv.sgst_price AS sgst_price,
											inv.has_gst AS has_gst,
											inv.service_gst_int_price,
											inv.internet_handling_amount
									
									FROM " . _DB_INVOICE_ . " inv
										
						 LEFT OUTER JOIN " . _DB_DISCOUNT_ . " dis
									  ON inv.slip_id = dis.slip_id
										
											WHERE inv.id = ?
											  AND inv.status IN ('A','C') " . $searchCondition . " ";
	$selectQuery['PARAM'][]	=	array('FILD' => 'inv.id', 	  'DATA' => $invoiceId,     'TYP' => 's');
	$resultState	= $mycms->sql_select($selectQuery);

	if ($resultState) {
		$rowfetch       = $resultState[0];
		if ($rowfetch['has_gst'] == 'Y') {
			$returnArray['PERCENTAGE'] 			= $rowfetch['percentage'];

			$returnArray['PRE_DISCOUNT_AMOUNT'] = $rowfetch['service_roundoff_price'];

			$returnArray['DISCOUNT']    	    = (($rowfetch['percentage'] * $rowfetch['service_roundoff_price']) / 100);
			$returnArray['TOTAL_AMOUNT'] 		= floatval(round($rowfetch['service_roundoff_price'] - $returnArray['DISCOUNT']));

			$returnArray['RAW_DISCOUNT']    	= (($rowfetch['percentage'] * $rowfetch['service_grand_price']) / 100);
			$returnArray['RAW_AMOUNT'] 		    = floatval(round($rowfetch['service_grand_price'] - $returnArray['RAW_DISCOUNT']));

			$returnArray['BASIC_DISCOUNT']      = (($rowfetch['percentage'] * $rowfetch['service_basic_price']) / 100);
			$returnArray['BASIC_AMOUNT'] 		= floatval(round($rowfetch['service_basic_price'] - $returnArray['BASIC_DISCOUNT']));

			$returnArray['CGST_DISCOUNT']       = (($rowfetch['percentage'] * $rowfetch['cgst_price']) / 100);
			$returnArray['CGST_AMOUNT'] 		= floatval(round($rowfetch['cgst_price'] - $returnArray['CGST_DISCOUNT']));

			$returnArray['SGST_DISCOUNT']       = (($rowfetch['percentage'] * $rowfetch['sgst_price']) / 100);
			$returnArray['SGST_AMOUNT'] 		= floatval(round($rowfetch['sgst_price'] - $returnArray['SGST_DISCOUNT'], 0));

			$returnArray['INT_HND_DISCOUNT']    = (($rowfetch['percentage'] * $rowfetch['service_gst_int_price']) / 100);
			$returnArray['INT_HND_AMOUNT'] 		= floatval(round($rowfetch['service_gst_int_price'] - $returnArray['INT_HND_DISCOUNT'], 0));
		} else {
			$returnArray['PERCENTAGE'] 			= $rowfetch['percentage'];

			$returnArray['PRE_DISCOUNT_AMOUNT'] = $rowfetch['service_roundoff_price'];

			$returnArray['DISCOUNT']    	    = (($rowfetch['percentage'] * $rowfetch['service_roundoff_price']) / 100);
			$returnArray['TOTAL_AMOUNT'] 		= floatval(round($rowfetch['service_roundoff_price'] - $returnArray['DISCOUNT'], 0));

			$returnArray['BASIC_DISCOUNT']      = (($rowfetch['percentage'] * $rowfetch['service_product_price']) / 100);
			$returnArray['BASIC_AMOUNT'] 		= floatval(round($rowfetch['service_product_price'] - $returnArray['BASIC_DISCOUNT'], 0));

			$returnArray['CGST_DISCOUNT']  		= 0;
			$returnArray['CGST_AMOUNT'] 		= 0;

			$returnArray['SGST_DISCOUNT']		= 0;
			$returnArray['SGST_AMOUNT'] 		= 0;

			$returnArray['INT_HND_DISCOUNT']    = (($rowfetch['percentage'] * $rowfetch['internet_handling_amount']) / 100);
			$returnArray['INT_HND_AMOUNT'] 		= floatval(round($rowfetch['internet_handling_amount'] - $returnArray['INT_HND_DISCOUNT'], 0));
		}
	} else {
		$returnArray['TOTAL_AMOUNT'] = 0;
	}
	return $returnArray;
}

// function mailInvoiceContent($delegateId, $invoiceId)
// {

// 	global $cfg, $mycms;

// 	include_once('function.workshop.php');
// 	include_once('function.delegate.php');
// 	include_once('function.dinner.php');

// 	$contentBody = "";
// 	$invoiceDetails  = getInvoiceDetails($invoiceId, true);
// 	//echo $invoiceId;
// 	//echo '<pre>'; print_r($invoiceDetails); die;
// 	$delegateDetails = getUserDetails($delegateId);

// 	if (!empty($delegateDetails['isCombo']) && $delegateDetails['isCombo'] == 'Y') {
// 		$particulaTitle =	getClassificationComboTitle($delegateDetails['registration_classification_id']);
// 	} else {
// 		$particulaTitle = 'Conference Registration ' . getRegClsfName(getUserClassificationId($delegateId, true));
// 	}


// 	$user_registration_id            = "-";

// 	$user_registration_id = "-";
// 	if ($delegateDetails['registration_payment_status'] == "PAID"  || $delegateDetails['registration_payment_status'] == "COMPLIMENTARY" || $delegateDetails['registration_payment_status'] == "ZERO_VALUE") {
// 		$user_registration_id        = $delegateDetails['user_registration_id'];
// 	}

// 	$totalConferenceRegistrationAmount    = 0.00;
// 	$totalWorkshopRegistrationAmount      = 0.00;
// 	$totalAccompanyRegistrationAmount     = 0.00;
// 	$totalAccommodationRegistrationAmount = 0.00;
// 	$totalTourRegistrationAmount          = 0.00;
// 	$totalInternetHandlingAmount          = 0.00;
// 	$totalTaxAmount     			      = 0.00;

// 	if (intval($mycms->cDate('Ymd', $invoiceDetails['invoice_date'])) < 20190401) {
// 		//return mailInvoiceContent_pre_april2019($delegateId, $invoiceId);
// 	}


// 	$sql    =   array();
// 	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
// 	                        WHERE `status`='A' order by id desc limit 1";
// 	//$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
// 	$result = $mycms->sql_select($sql);
// 	$row             = $result[0];

// 	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
// 	if ($row['footer_image'] != '') {
// 		$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
// 	}
// 	if ($row['header_image'] != '') {
// 		$emailHeader  = $header_image;
// 	}




// 	if ($invoiceDetails['has_gst'] == 'Y') {
// 		$GST_DATA_aary = array();

// 		$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
// 												<table width="100%" border="0" cellpadding="0" cellspacing="0">												
// 													<tbody>';
// 		if ($emailHeader) {
// 			$contentBody                    .= 				'<tr>
// 															<td align="center" style="border-collapse:collapse;">
// 															<img src="' . $emailHeader . '" width="100%"/>
// 															</td>
// 														</tr>';
// 		}
// 		$contentBody                    .= 				'<tr>
// 															<td align="center" height="730px" style="border-collapse:collapse; fontsize:11px;" valign="top">
// 																<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
// 																	<tr>
// 																		<td style="border:0; width:70%;">
// 																		<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">' .
// 			(($invoiceDetails['currency'] == 'USD') ? 'PROFORMA EXPORT INVOICE SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX' : 'TAX INVOICE AND RECEIPT')
// 			. '</div>
// 																		</td>
// 																	</tr>
// 																</table>
// 																<table width="100%" cellpadding="0" cellspacing="0" style="font-size:11px;" border="1" cellpadding="5">
// 																	<tr>
// 																		<td width="50%"  valign="top">
// 																			<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
// 																				<tr>
// 																					<td><b>' . $cfg['INVOICE_COMPANY_NAME_PREFIX'] . '</b></td>
// 																				</tr>
// 																				<tr>
// 																					<td>' . $cfg['INVOICE_COMPANY_NAME'] . '</td>
// 																				</tr>																	
// 																				<tr>
// 																					<td>' . $cfg['INVOICE_ADDRESS'] . '</td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> GSTN/UIN11 : ' . $cfg['GSTIN'] . '</td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> STATE NAME : ' . $cfg['INVOICE_STATE_NAME'] . ' &nbsp;&nbsp;&nbsp; CODE : ' . $cfg['INVOICE_STATE_CODE'] . '</td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> CONTACT : ' . $cfg['INVOICE_CONTACT'] . '</td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> EMAIL : ' . $cfg['INVOICE_EMAIL'] . '</td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> WEBSITE : ' . $cfg['INVOICE_WEBSITE'] . '</td>
// 																				</tr>
// 																			</table>
// 																		</td>
// 																		<td width="50%" rowspan="2"  valign="top">
// 																			<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
// 																				<tr>
// 																					<td width="50%" valign="top">
// 																						Invoice No:<br>
// 																						<b>' . $invoiceDetails['invoice_number'] . '</b><br><br>
// 																					</td>
// 																					<td valign="top">
// 																						Dated :<br>
// 																						<b>' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</b><br><br>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td valign="top">
// 																						Delivery Note : 
// 																						<br><br><br>
// 																					</td>
// 																					<td valign="top">
// 																						Mode / Terms of Payment : 
// 																						<br><br><br>
// 																					</td>
// 																				</tr>																				
// 																				<tr>
// 																					<td valign="top">
// 																						Suplier\'s Ref : 
// 																						<br>
// 																						<b>' . $invoiceDetails['invoice_number'] . '</b><br><br>
// 																					</td>
// 																					<td valign="top">
// 																						Other Reference(s) : 
// 																						<br>
// 																						<i>Reg. Id: ' . $delegateDetails['user_registration_id'] . '</i><br>
// 																						<i>PV No.: ' . $invoiceDetails['slipNO'] . '</i><br>
// 																						<i>Payment Status: <b>' . $invoiceDetails['payment_status'] . '</b></i><br><br>
// 																					</td>
// 																				</tr>																				
// 																				<tr>
// 																					<td valign="top">
// 																						Buyer\'s Order No. : 
// 																						<br><br><br>
// 																					</td>
// 																					<td valign="top">
// 																						Dated :
// 																						<br><br><br>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td valign="top">
// 																						Dispatch Document No. : 
// 																						<br><br><br>
// 																					</td>
// 																					<td valign="top">
// 																						Delivery Note Date :
// 																						<br><br><br>
// 																					</td>
// 																				</tr>	
// 																				<tr>
// 																					<td valign="top">
// 																						Despatched Through : 
// 																						<br><br><br>
// 																					</td>
// 																					<td valign="top">
// 																						Destination :
// 																						<br><br><br>
// 																					</td>
// 																				</tr>																	
// 																				<tr>
// 																					<td colspan="2"  valign="top">
// 																						Terms of Delivery : 
// 																						<br><br><br>
// 																					</td>
// 																				</tr>
// 																			</table>
// 																		</td>
// 																	</tr>
// 																	<tr>
// 																		<td valign="top">
// 																			<table width="100%" cellpadding="1" style="font-size:11px;" border="0">
// 																				<tr>
// 																					<td><i>Buyer</i></td>
// 																				</tr>
// 																				<tr>
// 																					<td><b>' . $delegateDetails['user_full_name'] . '</b></td>
// 																				</tr>
// 																				<tr>
// 																					<td><b>' . $delegateDetails['user_address'] . '</b></td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> GSTN/UIN : </td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> STATE NAME :  &nbsp;&nbsp;&nbsp; CODE : </td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> PAN/IT No. : </td>
// 																				</tr>
// 																				<tr>
// 																					<td colspan="6"> PLACE OF SUPPLY : WEST BENGAL</td>
// 																				</tr>
// 																			</table>
// 																		</td>
// 																	</tr>
// 																</table><br>
// 																';


// 		$contentBody                    .= '					<table width="100%" style="font-size:11px;" border="1" cellpadding="1" cellspacing="0">
// 																	<tbody>
// 																		<tr>
// 																			<th align="center">Sl. No.</th>
// 																			<th align="left">Particulars</th>
// 																			<th align="center">HSN/SAC</th>
// 																			<th align="center">Quantity</th>
// 																			<th align="center">Rate</th>
// 																			<th align="center">Per</th>
// 																			<th colspan="2" align="right">Amount (' . getInvoiceCurrencyById($invoiceId) . ')</th>
// 																		</tr>';

// 		$slCounter = 1;
// 		$totalCGST = 0;
// 		$totalSGST = 0;
// 		//echo $invoiceDetails['service_type'];
// 		if ($invoiceDetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
// 			if ($cfg['IGST.FLAG'] == 0) {
// 				$cgst = $cfg['CONFERENCE.CGST'];
// 				$sgst = $cfg['CONFERENCE.SGST'];
// 			} else {
// 				$cgst = $cfg['CONFERENCE.IGST'] / 2;
// 				$sgst = $cfg['CONFERENCE.IGST'] / 2;
// 			}

// 			$gstArray 		= gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price'], $igst);
// 			//print_r($gstArray);
// 			//echo $cfg['GST.FLAG'];
// 			if ($cfg['GST.FLAG'] == 3) {
// 				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
// 			} else {
// 				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
// 			}

// 			$totalCGST 	   += $gstArray['CGST.PRICE'];
// 			$totalSGST 	   += $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
// 			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE']	= $cgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';


// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>
// 																			<td align="left">' . $particulaTitle . '</td>
// 																			<td align="center">998596</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . $invoice_price . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . $invoice_price . '</td>
// 																		</tr>';
// 		}
// 		if ($invoiceDetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
// 			$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id'], true);
// 			$workshop_id                     = $workShopDetails['workshop_id'];
// 			$cgst 							 = $cfg['WORKSHOP.CGST'];
// 			$sgst 							 = $cfg['WORKSHOP.SGST'];
// 			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
// 			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
// 			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
// 			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE'] = $cgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

// 			if ($cfg['GST.FLAG'] == 3) {
// 				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
// 			} else {
// 				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
// 			}

// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>
// 																			<td align="left">' . getWorkshopName($workShopDetails['workshop_id']) . '(' . date("M d, Y", strtotime(getWorkshopDate($workShopDetails['workshop_id']))) . ')<br>' . $session . '</td>
// 																			<td align="center">998596</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . $invoice_price . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . $invoice_price . '</td>
// 																		</tr>';
// 		}
// 		if ($invoiceDetails['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
// 			$cgst 							 = $cfg['ACCOMPANY.CGST'];
// 			$sgst 							 = $cfg['ACCOMPANY.SGST'];
// 			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id'], true);
// 			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
// 			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
// 			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
// 			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE']  = $cgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE']  = $sgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

// 			if ($cfg['GST.FLAG'] == 3) {
// 				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
// 			} else {
// 				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
// 			}


// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>
// 																			<td align="left">Accompanying Person Registration <br />' . $accompanyDetails['user_full_name'] . '</td>
// 																			<td align="center">998596</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . $invoice_price . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . $invoice_price . '</td>
// 																		</tr>';
// 		}
// 		if ($invoiceDetails['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
// 			$sqlaccommodationDetails 				 = 	array();
// 			$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,masterHotel.hotel_address AS hotel_address
// 														  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
// 													INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
// 															ON masterHotel.id = accommodation.hotel_id
// 														 WHERE accommodation.status = ? 
// 														   AND accommodation.user_id = ?";
// 			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',                'DATA' => 'A',   'TYP' => 's');
// 			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',               'DATA' => $delegateId,   'TYP' => 's');

// 			$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);

// 			$rowaccommodation = $resaccommodation[0];
// 			$cgst 							 = $cfg['ACCOMMODATION.CGST'];
// 			$sgst 							 = $cfg['ACCOMMODATION.SGST'];
// 			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
// 			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
// 			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
// 			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE']  = $cgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE']  = $sgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';


// 			if ($cfg['GST.FLAG'] == 3) {
// 				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
// 			} else {
// 				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
// 			}


// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>		
// 																			<td>' . getRegClsfName(getUserClassificationId($delegateId, true)) . '<br/>' . $rowaccommodation['hotel_name'] . '</td>
// 																			<td align="center">998596</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . $invoice_price . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . $invoice_price . '</td>
// 																		</tr>';
// 		}
// 		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
// 			$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id'], true);
// 			$dinnerDetails   		=  getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id'], true);
// 			$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);

// 			$cgst 							 = $cfg['DINNER.CGST'];
// 			$sgst 							 = $cfg['DINNER.SGST'];
// 			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
// 			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
// 			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
// 			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE'] = $cgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

// 			if ($cfg['GST.FLAG'] == 3) {
// 				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
// 			} else {
// 				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
// 			}

// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>		
// 																			<td align="left">' . $cfg['BANQUET_DINNER_NAME'] . ' Registration(' . $cfg['BANQUET_DINNER_DATE'] . ')<br>' . $dinnerUserDetails['user_full_name'] . '</td>
// 																			<td align="center">998596</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . $invoice_price . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . $invoice_price . '</td>
// 																		</tr>';
// 		}
// 		if ($invoiceDetails['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {

// 			$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*,package.package_name, package.id AS packageId, room.room_id
// 														  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
// 													INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
// 															ON accommodation.package_id = package.id
// 													INNER JOIN " . _DB_MASTER_ROOM_ . " room	
// 															ON accommodation.id = room.request_accommodation_id	
// 														 WHERE accommodation.status = ? 
// 														   AND accommodation.user_id = ?
// 														   AND accommodation.id = ? ";

// 			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',        'DATA' => 'A',   							 'TYP' => 's');
// 			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',       'DATA' => $invoiceDetails['delegateId'],   'TYP' => 's');
// 			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.id',            'DATA' => $invoiceDetails['refference_id'],   'TYP' => 's');

// 			$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
// 			$rowaccommodation = $resaccommodation[0];

// 			//echo '<pre>'; print_r($sqlaccommodationDetails);

// 			$cgst 							 = $cfg['ACCOMMODATION.CGST'];
// 			$sgst 							 = $cfg['ACCOMMODATION.SGST'];
// 			$accommodationDtls 				 = getAccomodationDetails($invoiceDetails['refference_id'], true);
// 			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
// 			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
// 			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
// 			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE'] = $cgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
// 			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

// 			if ($cfg['GST.FLAG'] == 3) {
// 				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
// 			} else {
// 				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
// 			}

// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>		
// 																			<td colspan="" align="left">
// 																				Accommodation Booking - (' . getAccomPackageName($rowaccommodation['packageId']) . ')<br />
// 																				' . $accommodationDtls['checkin_date'] . ' to ' . $accommodationDtls['checkout_date'] . '
// 																				&nbsp;&nbsp;Room: ' . $rowaccommodation['room_id'] . '														
// 																			</td>
// 																			<td align="center">998596</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . $invoice_price . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . $invoice_price . '</td>
// 																		</tr>';
// 		}

// 		if (trim($invoiceDetails['discount_amount']) != '' && is_numeric($invoiceDetails['discount_amount']) &&  floatval($invoiceDetails['discount_amount']) > 0) {
// 			$contentBody                    .= '							<tr> 
// 																				<td align="center">&nbsp;</td>
// 																				<td colspan="5" align="right">Discount Amount (-)</td>
// 																				<td align="right">' . $invoiceDetails['discount_amount'] . '</td>
// 																			</tr>';
// 		}


// 		if ($cfg['GST.FLAG'] == 1 || $cfg['GST.FLAG'] == 2) {
// 			// if($cfg['IGST.FLAG']==1 && $invoiceDetails['service_type']=='DELEGATE_CONFERENCE_REGISTRATION'){   
// 			if ($cfg['IGST.FLAG'] == 1) {
// 				$contentBody .= '<tr>
// 									<td align="center">' . $slCounter++ . '</td>
// 									<td align="left">IGST</td>
// 									<td align="center"></td>	
// 									<td align="center"></td>
// 									<td align="right"></td>
// 									<td align="center"></td>
// 									<td align="right"> ' . number_format($totalCGST + $totalSGST, 2) . '</td>
// 								</tr>';
// 			} else {
// 				$contentBody .= '<tr>
// 									<td align="center">' . $slCounter++ . '</td>
// 									<td align="left">CGST</td>
// 									<td align="center"></td>	
// 									<td align="center"></td>
// 									<td align="right"></td>
// 									<td align="center"></td>
// 									<td align="right"> ' . number_format($totalCGST, 2) . '</td>
// 								</tr>
// 								<tr>
// 									<td align="center">' . $slCounter++ . '</td>
// 									<td align="left">SGST</td>
// 									<td align="center"></td>	
// 									<td align="center"></td>
// 									<td align="right"></td>
// 									<td align="center"></td>
// 									<td align="right"> ' . number_format($totalSGST, 2) . '</td>
// 								</tr>';
// 			}
// 		}

// 		if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
// 			$cgst 							 = $cfg['INT.CGST'];
// 			$sgst 							 = $cfg['INT.SGST'];
// 			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['internet_handling_amount']);
// 			//print_r($gstArray);
// 			//$totalCGST 	   					+= $gstArray['CGST.PRICE'];
// 			//$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

// 			$GST_DATA_aary['INTERNET'] 					= $gstArray;
// 			$GST_DATA_aary['INTERNET']['CGST.RATE']	= $cgst;
// 			$GST_DATA_aary['INTERNET']['SGST.RATE']	= $sgst;
// 			$GST_DATA_aary['INTERNET']['HSN'] 			= '998429';

// 			$contentBody                    .= '						<tr>
// 																			<td align="center">' . $slCounter++ . '</td>
// 																			<td>Internet Handling Charges</td>
// 																			<td align="center">998429</td>	
// 																			<td align="center">1</td>
// 																			<td align="right">' . number_format($gstArray['BASIC.PRICE'], 2) . '</td>
// 																			<td align="center"></td>
// 																			<td align="right"> ' . number_format($gstArray['GST.PRICE'], 2) . '</td>
// 																		</tr>';
// 			/*
// 			$contentBody                    .= '	<tr>
// 														<td>&nbsp;</td>
// 														<td colspan="4">CGST @ '.number_format($invoiceDetails['cgst_internet_handling_percentage'],0).'%</td>
// 														<td align="right" colspan="2">'.$invoiceDetails['cgst_internet_handling_price'].'</td>
// 													</tr>';
// 			$contentBody                    .= '	<tr>
// 														<td>&nbsp;</td>
// 														<td colspan="4">SGST @ '.number_format($invoiceDetails['sgst_internet_handling_percentage'],0).'%</td>
// 														<td align="right" colspan="2">'.$invoiceDetails['sgst_internet_handling_price'].'</td>
// 													</tr>';
// 			*/
// 		}
// 		//echo $totalCGST;							



// 		$contentBody 					.= '						<tr>
// 																		<td colspan="5" align="right">
// 																			<b>Total (Rounded)</b>
// 																		</td>
// 																		<td align="right" colspan="2"><b>' . number_format($invoiceDetails['service_roundoff_price'], 2) . '</b></td>
// 																	</tr>
// 																	<tr>
// 																		<td colspan="6">Amoun Chargeable (in Words): <br> <b>' . convert_number($invoiceDetails['service_roundoff_price']) . ' Only.</b></td>
// 																		<td align="right" valign="bottom"><b>E. & O.E.</b></td>
// 																	</tr>
// 																</table> <br>';

// 		if ($cfg['GST.FLAG'] == 1 || $cfg['GST.FLAG'] == 2) {
// 			$contentBody    .= '<table width="100%" style="font-size:11px;" border="1" cellpadding="1" cellspacing="0">
// 								<tbody>
// 									<tr>
// 										<th align="center" rowspan="2" colspan="2">HSN/SAC1</th>
// 										<th align="center" rowspan="2">Taxable Value</th>';
// 			// if($cfg['IGST.FLAG']==1 && $invoiceDetails['service_type']=='DELEGATE_CONFERENCE_REGISTRATION'){   											
// 			if ($cfg['IGST.FLAG'] == 1) {
// 				$contentBody	.= '			<th align="center" colspan="2">IGST</th>
// 										<th align="center"  rowspan="2">TOTAL (' . getInvoiceCurrencyById($invoiceId) . ')</th>
// 									</tr>
// 									<tr>
// 										<th align="center">Rate</th>
// 										<th align="right">Amount</th>
// 									</tr>';
// 			} else {
// 				$contentBody	.= '			<th align="center" colspan="2">CGST</th>
// 										<th align="center" colspan="2">SGST</th>
// 										<th align="center"  rowspan="2">TOTAL (' . getInvoiceCurrencyById($invoiceId) . ')</th>
// 									</tr>
// 									<tr>
// 										<th align="center">Rate</th>
// 										<th align="right">Amount</th>
// 										<th align="center">Rate</th>
// 										<th align="right">Amount</th>
// 									</tr>';
// 			}


// 			$overallTotalGST = 0;
// 			//echo '<pre>'; print_r($GST_DATA_aary);
// 			foreach ($GST_DATA_aary as $kk => $data) {
// 				$totalGST 			= floatval($data['CGST.PRICE']) + floatval($data['SGST.PRICE']);
// 				$overallTotalGST   += $totalGST;
// 				if ($cfg['IGST.FLAG'] == 1 && $invoiceDetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
// 					$contentBody .= '<tr>
// 											<td align="center">' . $data['HSN'] . '</td>
// 											<td></td>
// 											<td align="center">' . number_format($data['BASIC.PRICE'], 2) . '</td>	
// 											<td align="center">' . number_format($data['CGST.RATE'] + $data['SGST.RATE']) . '%</td>
// 											<td align="right">' . number_format($data['CGST.PRICE'] + $data['SGST.PRICE'], 2) . '</td>
// 											<td align="right"> ' . number_format($totalGST, 2) . '</td>
// 										</tr>';
// 				} else {
// 					$contentBody .= '<tr>
// 											<td align="left">' . $data['HSN'] . '</td>
// 											<td></td>
// 											<td align="center">' . number_format($data['BASIC.PRICE'], 2) . '</td>	
// 											<td align="center">' . $data['CGST.RATE'] . '%</td>
// 											<td align="right">' . number_format($data['CGST.PRICE'], 2) . '</td>
// 											<td align="center">' . $data['SGST.RATE'] . '%</td>
// 											<td align="right"> ' . number_format($data['SGST.PRICE'], 2) . '</td>
// 											<td align="right"> ' . number_format($totalGST, 2) . '</td>
// 										</tr>';
// 				}
// 			}


// 			$contentBody                    .= '							<tr>
// 																					<td align="left" colspan="8">
// 																					Tax Amount (in words) : 
// 																					<b>' . convert_number($overallTotalGST) . ' Only.</b>
// 																					</td>
// 																				</tr>
// 																			</table> <br>';
// 		}



// 		$contentBody 					.= '					<table width="100%" cellpadding="0" cellspacing="0" style="font-size:11px;" border="1" cellpadding="5">
// 																	<tr>
// 																		<td width="50%"  valign="top">
// 																			Company\'s Pan No: ' . $cfg['PAN'] . '
// 																		</td>
// 																		<td width="50%" valign="top">
// 																			<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
// 																				<tr>
// 																					<td colspan=2>
// 																						<b>Company\'s Bank Details</b>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td width="30%">
// 																						Bank Name:
// 																					</td>
// 																					<td>
// 																						<b>' . $cfg['INVOICE_BANKNAME'] . '</b>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td width="20%">
// 																						Beneficiary Name:
// 																					</td>
// 																					<td>
// 																						<b>' . $cfg['INVOICE_BENEFECIARY'] . '</b>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td width="20%">
// 																						A/c No.:
// 																					</td>
// 																					<td>
// 																						<b>' . $cfg['INVOICE_BANKACNO'] . '</b>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td width="20%">
// 																						Branch Address:
// 																					</td>
// 																					<td>
// 																						<b>' . $cfg['INVOICE_BANKBRANCH'] . '</b>
// 																					</td>
// 																				</tr>
// 																				<tr>
// 																					<td width="20%">
// 																						IFSC:
// 																					</td>
// 																					<td>
// 																						<b>' . $cfg['INVOICE_BANKIFSC'] . '</b>
// 																					</td>
// 																				</tr>
// 																			</table>
// 																		</td>
// 																	</tr>
// 																	<tr>
// 																		<td valign="top" align="right" colspan="2">
// 																			<table width="40%" cellpadding="1" style="font-size:11px;" border="0">
// 																				<tr>
// 																					<td align="center"><b>' . $cfg['INVOICE_AUTORIZED_SIGNATURE_PREFIX'] . '</b></td>
// 																				</tr>
// 																				<tr>
// 																					<td><br></td>
// 																				</tr>
// 																				<tr>
// 																					<td align="center">Authorized Signatory</td>
// 																				</tr>
// 																			</table>
// 																		</td>
// 																	</tr>
// 																	<tr>
// 																		<td valign="bottom" align="center" colspan="2">
// 																			This is a Computer Generated Invoice<br>
// 																			Subject to Kolkata Jurisdiction
// 																		</td>
// 																	</tr>
// 																</table> <br><br>'; //Payment Status: '.$invoiceDetails['payment_status'].'

// 		$contentBody    .= '								</td>
// 														</tr>';
// 		if ($footer_image != '') {
// 			$contentBody    .= '							<tr>
// 															<td align="center" valign="bottom" style="border-collapse:collapse;">
// 															<img src="' . $footer_image . '" width="100%"/>
// 															</td>
// 														</tr>';
// 		}
// 		$contentBody    .= '						</tbody>
// 												</table>
// 											</div> ';
// 		/*

// 											*/
// 	} else {

// 
?>

<?php
// 		//echo 22;
// 		/*$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
// 											<table width="100%" border="0" cellpadding="0" cellspacing="0">												
// 												<tr>
// 													<td align="center" style="border-collapse:collapse;">
// 													<img src="'.$cfg['BASE_URL'].'images/header20191011.jpg" width="100%"/>
// 													</td>
// 												</tr>
// 												<tr>
// 													<td align="center" height="360px" style="border-collapse:collapse;" valign="top">
// 														<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
// 														'.(($invoiceDetails['currency']=='USD')?'PROFORMA EXPORT INVOICE SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX':'TAX INVOICE AND RECEIPT').'
// 														</div>
// 														<table width="100%" cellpadding="1" style="font-size:13px;">
// 															<tr>
// 																<td width="18%"></td>
// 																<td width="32%"></td>
// 																<td width="18%"></td>
// 																<td width="32%"></td>
// 															</tr>
// 															<tr>
// 																<td>Name:</td>
// 																<td>'.$delegateDetails['user_full_name'].'</td>
// 																<td width="18%">Date:</td>
// 																<td width="32%">'.date("d/m/Y", strtotime($invoiceDetails['invoice_date'])).'</td>
// 															</tr>
// 															<tr>
// 																<td>E-mail id:</td>
// 																<td>'.$delegateDetails['user_email_id'].'</td>
// 																<td>Invoice No:</td>
// 																<td>'.$invoiceDetails['invoice_number'].'</td>
// 															</tr>
// 															<tr>
// 																<td>Mobile:</td>
// 																<td>'.$delegateDetails['user_mobile_isd_code'].' '.$delegateDetails['user_mobile_no'].'</td>
// 																<td>Registration Id:</td>
// 																<td>'.$user_registration_id.'</td>
// 																</td>
// 															</tr>
// 															<tr>
// 																<td>PV No:</td>
// 																<td>'.$invoiceDetails['slipNO'].'</td>
// 																<td></td>
// 																<td></td>
// 															</tr> 
// 														</table>';


// 			if($invoiceDetails['service_type'] =="DELEGATE_CONFERENCE_REGISTRATION")
// 			{
// 				$totalAmount	= 0;
// 				$contentBody                				.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
// 																 Registration Details
// 																</div>
// 																<table width="100%" style="font-size:13px;">
// 																	<tr>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
// 																		<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
// 																		<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
// 																		<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
// 																	</tr>
// 																	<tr>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">Conference - '.getRegClsfName(getUserClassificationId($delegateId)).'</td>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
// 																		<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.($invoiceDetails['service_unit_price']==0.00?'Complimentary':$invoiceDetails['service_unit_price']).'</td>
// 																	</tr>';
// 				$totalAmount += $invoiceDetails['service_unit_price'];
// 				if($invoiceDetails['invoice_mode']=='ONLINE' && $invoiceDetails['service_roundoff_price']!=0.00)
// 				{										
// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
// 																	</tr>';
// 					$totalAmount += $invoiceDetails['internet_handling_amount'];
// 				}

// 				if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
// 				{
// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
// 																	 </tr>';
// 					$totalAmount -= $discountAmount;
// 				}

// 				if($invoiceDetails['service_roundoff_price']!=0.00)
// 				{
// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
// 																	</tr>';											

// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
// 																	</tr>';
// 				}
// 				$contentBody                    			.= '</table>';
// 			}

// 			if($invoiceDetails['service_type'] =="DELEGATE_RESIDENTIAL_REGISTRATION")
// 			{
// 				$totalAmount	 = 0;
// 				$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
// 									 Registration Details
// 									</div>
// 									<table width="100%" style="font-size:13px;">
// 										<tr>
// 											<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
// 											<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
// 											<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
// 											<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
// 										</tr>


// 										<tr>
// 											<td height="24" align="center" valign="middle" style="border:thin solid #000;">AI PACKAGE - '.getRegClsfName(getUserClassificationId($delegateId)).'</td>
// 											<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
// 											<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
// 											<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['service_unit_price'].'</td>
// 										</tr>';
// 				$totalAmount 	 += $invoiceDetails['service_unit_price'];
// 				if($invoiceDetails['invoice_mode']=='ONLINE')
// 				{										

// 				   $contentBody       .='<tr>
// 											<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
// 											<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
// 										</tr>';
// 					$totalAmount += $invoiceDetails['internet_handling_amount'];
// 				}			
// 				$contentBody         .='<tr>
// 											<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
// 											<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['service_roundoff_price'].'</td>
// 										</tr>

// 										<tr>
// 											<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
// 										</tr>	
// 									</table>';
// 			}				

// 			if($invoiceDetails['service_type'] =="DELEGATE_WORKSHOP_REGISTRATION")
// 			{
// 				$totalAmount					 = 0;
// 				$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id']);
// 				$workshop_id                     = $workShopDetails['workshop_id'];

// 				$contentBody    				.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
// 												 Registration Details
// 												</div>
// 												<table width="100%" style="font-size:13px;">
// 													<tr>
// 														<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
// 														<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
// 														<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
// 														<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
// 													</tr>
// 													<tr>
// 														<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>WORKSHOP</b><br>'.getWorkshopName($workShopDetails['workshop_id']).'</td>
// 														<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($workShopDetails['tariff_cutoff_id']).'</td>
// 														<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
// 														<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['service_unit_price'].'</td>
// 													</tr>';
// 				$totalAmount += $invoiceDetails['service_unit_price'];
// 				if($invoiceDetails['invoice_mode']=='ONLINE')
// 				{											
// 				   $contentBody       						.=	   '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
// 																	</tr>';
// 				   $totalAmount += $invoiceDetails['internet_handling_amount'];
// 				}		

// 				if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
// 				{
// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
// 																	</tr>';
// 					$totalAmount -= $discountAmount;

// 				}
// 				$contentBody                    			.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
// 																	</tr>';											

// 				$contentBody                    			.=     '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
// 																	</tr>
// 																</table>';
// 			}

// 			if($invoiceDetails['service_type'] =="ACCOMPANY_CONFERENCE_REGISTRATION")
// 			{
// 				$totalAmount					 = 0;
// 				$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);

// 				$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
// 																	Registration Details
// 																</div>
// 																<table width="100%" style="font-size:13px;">
// 																	<tr>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
// 																		<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
// 																		<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
// 																		<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
// 																	</tr>
// 																	<tr>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Accompanying Person</b><br>'.$accompanyDetails['user_full_name'].'</td>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
// 																		<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.($invoiceDetails['service_unit_price']==0.00?'Complimentary':$invoiceDetails['service_unit_price']).'</td>
// 																	</tr>';
// 				$totalAmount += $invoiceDetails['service_unit_price'];
// 				if($invoiceDetails['invoice_mode']=='ONLINE' && $invoiceDetails['service_roundoff_price']!=0.00)
// 				{											
// 					$contentBody                    			.=  '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
// 																	</tr>';
// 					$totalAmount += $invoiceDetails['internet_handling_amount'];
// 				}

// 				if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
// 				{
// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
// 																	</tr>';
// 					$totalAmount -= $discountAmount;
// 				}
// 				$contentBody                    			.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
// 																	</tr>';											

// 				$contentBody                    			.=     '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
// 																	</tr>
// 																</table>';
// 			}

// 			if($invoiceDetails['service_type'] =="DELEGATE_TOUR_REQUEST")
// 			{
// 				$totalAmount	 = 0;
// 				$tourDetails  	 = getTourDetails($invoiceDetails['refference_id']);
// 				$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
// 											Tour Booking Details
// 										</div>
// 										<table width="100%" style="font-size:13px;">
// 											<tr>
// 												<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
// 												<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
// 												<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
// 												<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Details</td>
// 												<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
// 											</tr>
// 											<tr>
// 												<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="2">'.getTourName($tourDetails['package_id']).'</td>
// 												<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">'.getCutoffName($tourDetails['tariff_cutoff_id']).'</td>
// 												<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">'.$invoiceDetails['service_consumed_quantity'].'</td>
// 												<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
// 												<td align="right" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_unit_price'].'</td>
// 											</tr>
// 											<tr>
// 												<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
// 												<td align="right" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['internet_handling_amount'].'</td>
// 											</tr>
// 											<tr>
// 												<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
// 												<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
// 												<td align="right" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_roundoff_price'].'</td>
// 											</tr>
// 											<tr>
// 												<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($invoiceDetails['service_roundoff_price']).' Only.</u></td>
// 											</tr>
// 										</table>';

// 			}

// 			if($invoiceDetails['service_type'] =="DELEGATE_DINNER_REQUEST")
// 			{
// 				$totalAmount			= 0;
// 				$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id']);
// 				$dinnerDetails   		= getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id']);
// 				$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);

// 				$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
// 																	Registration Details
// 																</div>
// 																<table width="100%" style="font-size:13px;">
// 																	<tr>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
// 																		<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
// 																		<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
// 																		<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
// 																	</tr>
// 																	<tr>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Gala Dinner</b><br>'.$dinnerUserDetails['user_full_name'].'</td>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
// 																		<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
// 																		<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.($invoiceDetails['service_unit_price']==0.00?'Complimentary':$invoiceDetails['service_unit_price']).'</td>
// 																	</tr>';
// 				$totalAmount += $invoiceDetails['service_unit_price'];
// 				if($invoiceDetails['invoice_mode']=='ONLINE' && $invoiceDetails['service_roundoff_price']!=0.00)
// 				{											
// 				   $contentBody                    			.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
// 																	</tr>';
// 				   $totalAmount += $invoiceDetails['internet_handling_amount'];
// 				}

// 				if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
// 				{
// 					$contentBody                    		.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
// 																	</tr>';
// 					$totalAmount -= $discountAmount;
// 				}

// 				$contentBody                    			.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
// 																		<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
// 																	</tr>';											

// 				$contentBody                   				.=      '<tr>
// 																		<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
// 																	</tr>
// 																</table>';
// 			}

// 		$contentBody .= '<table width="100%" style="font-size:13px;">
// 															<tr>
// 																<td><br /><br /></td>
// 															</tr>
// 														</table>
// 													</td>
// 												</tr>
// 												<tr><td style="font-size: x-small;background-color: #EEEAE9;">'.getNoticeDetails("Cancellation & Refund Policy").'</td></tr>
// 												<tr>
// 													<td align="center" valign="bottom" style="border-collapse:collapse;">
// 													<img src="'.$cfg['BASE_URL'].'images/footer20191011.jpg" width="100%"/>
// 													</td>
// 												</tr>	
// 											</table>
// 										</div>';*/
// 	}
// 	return $contentBody;
// }

function mailInvoiceContent($delegateId, $invoiceId)
{

	global $cfg, $mycms;

	include_once('function.workshop.php');
	include_once('function.delegate.php');
	include_once('function.dinner.php');

	$contentBody = "";
	$invoiceDetails  = getInvoiceDetails($invoiceId, true);
	//echo $invoiceId;
	// echo '<pre>'; print_r($invoiceDetails); die;
	$delegateDetails = getUserDetails($delegateId);

	if (!empty($delegateDetails['isCombo']) && $delegateDetails['isCombo'] == 'Y') {
		$particulaTitle =	getClassificationComboTitle($delegateDetails['registration_classification_id']);
	} else {
		$particulaTitle = 'Conference Registration ' . getRegClsfName(getUserClassificationId($delegateId, true));
	}


	$user_registration_id            = "-";

	$user_registration_id = "-";
	if ($delegateDetails['registration_payment_status'] == "PAID"  || $delegateDetails['registration_payment_status'] == "COMPLIMENTARY" || $delegateDetails['registration_payment_status'] == "ZERO_VALUE") {
		$user_registration_id        = $delegateDetails['user_registration_id'];
	}

	$totalConferenceRegistrationAmount    = 0.00;
	$totalWorkshopRegistrationAmount      = 0.00;
	$totalAccompanyRegistrationAmount     = 0.00;
	$totalAccommodationRegistrationAmount = 0.00;
	$totalTourRegistrationAmount          = 0.00;
	$totalInternetHandlingAmount          = 0.00;
	$totalTaxAmount     			      = 0.00;

	if (intval($mycms->cDate('Ymd', $invoiceDetails['invoice_date'])) < 20190401) {
		//return mailInvoiceContent_pre_april2019($delegateId, $invoiceId);
	}


	$sql    =   array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
	                        WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
	$result = $mycms->sql_select($sql);
	$row             = $result[0];

	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
	if ($row['footer_image'] != '') {
		$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	}
	if ($row['header_image'] != '') {
		$emailHeader  = $header_image;
	}

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
					WHERE `id`!='' AND `title`='Invoice Signature' AND status IN ('A', 'I')";
	$result 	 = $mycms->sql_select($sql);
	if ($result) {

		$invoice_sign_img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
	}
	$authorized_person_arr = explode(',', $cfg['INVOICE_SIGN_NAME']);

	if ($invoiceDetails['has_gst'] == 'Y') {
		$GST_DATA_aary = array();

		$contentBody                    .= '<div style="width: 100%;max-width: 800px;margin: auto; height: 27cm; font-family: sans-serif;">
        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
            <tbody>
                <tr>
                    <td colspan="2" style="padding:20px 0 20px 30px; color: #114C5C;">
                        <p style="display: flex; color: #114C5C; font-weight: bold;
                        align-items: center;
                        justify-content: space-between; margin: 0;">
 <span>Invoice No.: ' . $invoiceDetails['invoice_number'] . '</span>
 <span >Date: ' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</span>
                        </p>
                       
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-left: 30px;">
                        <p
                            style="background: #BADEC4;padding: 25px;margin: 0;font-size: 35px;font-weight: 600;letter-spacing: .8px;color: #114c5c;display: flex;justify-content: space-between;">
                            INVOICE
							<span style="font-size:20px;padding: 10px 0px;">' . $delegateDetails['user_full_name'] . '</span></p>		
                    </td>
                </tr>
                <tr>
                    <td
                        style="background: #114C5C;width: 30%;padding: 45px 15px;text-align: right;color: white;height: 19cm;vertical-align: top;position: relative;">
                        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p
                                            style="margin: 0;font-size: 24px;letter-spacing: .8px;font-weight: 600; margin-bottom: 13px;">
                                            <img style="width: 100%;     filter: brightness(36.5);"
                                                src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '"
                                                alt="logo">
                                        </p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;color: white;">
                                            ' . $cfg['INVOICE_ADDRESS'] . '
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            GSTN/UIN11</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                           ' . $cfg['GSTIN'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            STATE NAME</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                           ' . $cfg['INVOICE_STATE_NAME'] . ' 
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            Code</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                           ' . $cfg['INVOICE_STATE_CODE'] . '
                                        </p>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            CONTACT</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_CONTACT'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            EMAIL</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_EMAIL'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            WEBSITE</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                             ' . $cfg['INVOICE_WEBSITE'] . '
                                        </p>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            Payment Status</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;color: white;">
                                            ' . $invoiceDetails['payment_status'] . '
                                        </p>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                        <table style="position: absolute;bottom: 17px;left: 0;margin: 22px;">
                            <tbody>
                                <tr></tr>
                                <tr>
                                    <td>
                                        <p
                                            style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600; color: white;border-bottom: 1px solid; padding-bottom: 6px;">
                                            Bank Details</p>
                                        <p
                                            style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;margin-top: 0px; line-height: 21px; padding-top: 7px; color: white;">
                                            <b>' . $cfg['INVOICE_BANKNAME'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BENEFECIARY'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKACNO'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKBRANCH'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKIFSC'] . '</b>
                                        </p>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 70%;padding: 0 30px;vertical-align: text-bottom;background: #BADEC4;">
                        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="text-align: right;font-size: 18px;padding-bottom: 25px;font-weight: 600;color: #114C5C;">
                                        
                                        <p style="margin-top: 2px;font-size: 14px;line-height: 24px;">Reg. Id:
                                            ' . $delegateDetails['user_registration_id'] . '<br>
                                            PV No.: ' . $invoiceDetails['slipNO'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table cellspacing="0" cellpadding="0" border="0"
                                            style="width: 100%; color: #114C5C;">
                                            <tbody>
                                                <tr>
                                                    <td
                                                        style="width: 5%; text-transform: uppercase; font-weight: bold ; text-align: center; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Sl. No.</td>
                                                    <td
                                                        style="width: 30%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Particulars</td>
                                                    <td
                                                        style="width: 15%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        HSN/SAC
                                                    </td>
                                                    <td
                                                        style="width: 5%; text-transform: uppercase; font-weight: bold ; text-align: center; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Qty</td>
                                                    <td
                                                        style="width: 20%; text-transform: uppercase; font-weight: bold ; text-align: center; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Rate</td>

                                                    <td
                                                        style="width: 25%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: right;">
                                                        Amount (' . getInvoiceCurrencyById($invoiceId) . ')</td>
                                                </tr>';

		$slCounter = 1;
		$totalCGST = 0;
		$totalSGST = 0;
		//echo $invoiceDetails['service_type'];
		if ($invoiceDetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			if ($cfg['IGST.FLAG'] == 0) {
				$cgst = $cfg['CONFERENCE.CGST'];
				$sgst = $cfg['CONFERENCE.SGST'];
			} else {
				$cgst = $cfg['CONFERENCE.IGST'] / 2;
				$sgst = $cfg['CONFERENCE.IGST'] / 2;
			}

			$gstArray 		= gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price'], $igst);
			//print_r($gstArray);
			//echo $cfg['GST.FLAG'];
			if ($cfg['GST.FLAG'] == 3) {
				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
			} else {
				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
			}

			$totalCGST 	   += $gstArray['CGST.PRICE'];
			$totalSGST 	   += $gstArray['SGST.PRICE'];

			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE']	= $cgst;
			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';


			$contentBody                    .= '<tr>
													<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
														</td>
													<td style="font-size: 13px; padding:10px 5px;">' . $particulaTitle . '
														</td>
													<td style="text-align: center; font-size: 13px; padding:10px 5px;">
														998596</td>
													<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
													</td>
													<td style="text-align: center; font-size: 13px; padding:10px 5px;">
														' . $invoice_price . '
													</td>

													<td style="text-align: right; font-size: 13px; padding:10px 5px;">
														' . $invoice_price . '</td>
												</tr>';
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
			$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id'], true);
			$workshop_id                     = $workShopDetails['workshop_id'];
			$cgst 							 = $cfg['WORKSHOP.CGST'];
			$sgst 							 = $cfg['WORKSHOP.SGST'];
			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE'] = $cgst;
			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

			if ($cfg['GST.FLAG'] == 3) {
				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
			} else {
				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
			}

			$contentBody                    .= '         <tr>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
															</td>
															<td style="font-size: 13px; padding:10px 5px;">' . getWorkshopName($workShopDetails['workshop_id']) . '(' . date("M d, Y", strtotime(getWorkshopDate($workShopDetails['workshop_id']))) . ')<br>' . $session . '</td>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																998596</td>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
															</td>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																' . $invoice_price . '
															</td>

															<td style="text-align: right; font-size: 13px; padding:10px 5px;">
																' . $invoice_price . '</td>
														</tr>';
		}
		if ($invoiceDetails['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$cgst 							 = $cfg['ACCOMPANY.CGST'];
			$sgst 							 = $cfg['ACCOMPANY.SGST'];
			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id'], true);
			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE']  = $cgst;
			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE']  = $sgst;
			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

			if ($cfg['GST.FLAG'] == 3) {
				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
			} else {
				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
			}


			$contentBody                    .= '           <tr>
																		<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
																		</td>
																		<td style="font-size: 13px; padding:10px 5px;">Accompanying Person- ' . $accompanyDetails['user_full_name'] . '</td>
																		<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																			998596</td>
																		<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
																		</td>
																		<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																			' . $invoice_price . '
																		</td>
					
																		<td style="text-align: right; font-size: 13px; padding:10px 5px;">
																			' . $invoice_price . '</td>
																	</tr>';
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			$sqlaccommodationDetails 				 = 	array();
			$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,masterHotel.hotel_address AS hotel_address
														  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
													INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
															ON masterHotel.id = accommodation.hotel_id
														 WHERE accommodation.status = ? 
														   AND accommodation.user_id = ?";
			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',                'DATA' => 'A',   'TYP' => 's');
			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',               'DATA' => $delegateId,   'TYP' => 's');

			$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);

			$rowaccommodation = $resaccommodation[0];
			$cgst 							 = $cfg['ACCOMMODATION.CGST'];
			$sgst 							 = $cfg['ACCOMMODATION.SGST'];
			$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
			$totalCGST 	   					+= $gstArray['CGST.PRICE'];
			$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

			$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
			$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE']  = $cgst;
			$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE']  = $sgst;
			$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';


			if ($cfg['GST.FLAG'] == 3) {
				$invoice_price = number_format($gstArray['GST.PRICE'], 2);
			} else {
				$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
			}


			$contentBody                    .= '           <tr>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
																</td>
																<td style="font-size: 13px; padding:10px 5px;">' . getRegClsfName(getUserClassificationId($delegateId, true)) . '<br/>' . $rowaccommodation['hotel_name'] . '</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																	998596</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
																</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																	' . $invoice_price . '
																</td>
			
																<td style="text-align: right; font-size: 13px; padding:10px 5px;">
																	' . $invoice_price . '</td>
															</tr>';
		}
	}
	if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
		$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id'], true);
		$dinnerDetails   		=  getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id'], true);
		$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);

		$cgst 							 = $cfg['DINNER.CGST'];
		$sgst 							 = $cfg['DINNER.SGST'];
		$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
		$totalCGST 	   					+= $gstArray['CGST.PRICE'];
		$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

		$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
		$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE'] = $cgst;
		$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
		$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

		if ($cfg['GST.FLAG'] == 3) {
			$invoice_price = number_format($gstArray['GST.PRICE'], 2);
		} else {
			$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
		}

		$contentBody                    .= '           <tr>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
																</td>
																<td style="font-size: 13px; padding:10px 5px;">' . $cfg['BANQUET_DINNER_NAME'] . ' Registration(' . $cfg['BANQUET_DINNER_DATE'] . ')<br>' . $dinnerUserDetails['user_full_name'] . '</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																	998596</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
																</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																	' . $invoice_price . '
																</td>
			
																<td style="text-align: right; font-size: 13px; padding:10px 5px;">
																	' . $invoice_price . '</td>
															</tr>';
	}
	if ($invoiceDetails['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {

		$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*,package.package_name, package.id AS packageId, room.room_id
														  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
													INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
															ON accommodation.package_id = package.id
													INNER JOIN " . _DB_MASTER_ROOM_ . " room	
															ON accommodation.id = room.request_accommodation_id	
														 WHERE accommodation.status = ? 
														   AND accommodation.user_id = ?
														   AND accommodation.id = ? ";

		$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',        'DATA' => 'A',   							 'TYP' => 's');
		$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',       'DATA' => $invoiceDetails['delegateId'],   'TYP' => 's');
		$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.id',            'DATA' => $invoiceDetails['refference_id'],   'TYP' => 's');

		$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
		$rowaccommodation = $resaccommodation[0];

		//echo '<pre>'; print_r($sqlaccommodationDetails);

		$cgst 							 = $cfg['ACCOMMODATION.CGST'];
		$sgst 							 = $cfg['ACCOMMODATION.SGST'];
		$accommodationDtls 				 = getAccomodationDetails($invoiceDetails['refference_id'], true);
		$gstArray 						 = gstCalculation($cgst, $sgst, $invoiceDetails['service_unit_price']);
		$totalCGST 	   					+= $gstArray['CGST.PRICE'];
		$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

		$GST_DATA_aary[$invoiceDetails['id']] 				= $gstArray;
		$GST_DATA_aary[$invoiceDetails['id']]['CGST.RATE'] = $cgst;
		$GST_DATA_aary[$invoiceDetails['id']]['SGST.RATE'] = $sgst;
		$GST_DATA_aary[$invoiceDetails['id']]['HSN'] 		= '998596';

		if ($cfg['GST.FLAG'] == 3) {
			$invoice_price = number_format($gstArray['GST.PRICE'], 2);
		} else {
			$invoice_price = number_format($gstArray['BASIC.PRICE'], 2);
		}

		$contentBody                    .= '           <tr>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter1++ . '
																</td>
																<td style="font-size: 13px; padding:10px 5px;">Accommodation Booking - (' . getAccomPackageName($rowaccommodation['packageId']) . ')<br />
																				' . $accommodationDtls['checkin_date'] . ' to ' . $accommodationDtls['checkout_date'] . '
																				&nbsp;&nbsp;Room: ' . $rowaccommodation['room_id'] . '	</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																	998596</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
																</td>
																<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																	' . $invoice_price . '
																</td>
			
																<td style="text-align: right; font-size: 13px; padding:10px 5px;">
																	' . $invoice_price . '</td>
															</tr>';
	}

	if (trim($invoiceDetails['discount_amount']) != '' && is_numeric($invoiceDetails['discount_amount']) &&  floatval($invoiceDetails['discount_amount']) > 0) {
		$contentBody                    .= '							<tr> 
																				<td align="center">&nbsp;</td>
																				<td colspan="5" align="right">Discount Amount (-)</td>
																				<td align="right">' . $invoiceDetails['discount_amount'] . '</td>
																			</tr>';
	}


	if ($cfg['GST.FLAG'] == 1 || $cfg['GST.FLAG'] == 2) {
		// if($cfg['IGST.FLAG']==1 && $invoiceDetails['service_type']=='DELEGATE_CONFERENCE_REGISTRATION'){   
		if ($cfg['IGST.FLAG'] == 1) {
			$contentBody .= '<tr>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
								</td>
								<td style="font-size: 13px; padding:10px 5px;">IGST (' . $cfg['CONFERENCE.IGST'] . '%)</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">
									998596</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
								</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">
									
								</td>

								<td style="text-align: right; font-size: 13px; padding:10px 5px;">
									' . number_format($totalCGST + $totalSGST, 2) . '</td>
							</tr>';
		} else {
			$contentBody .= '<tr>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
								</td>
								<td style="font-size: 13px; padding:10px 5px;">CGST (' . $cfg['CONFERENCE.CGST'] . '%)</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">
									998596</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
								</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">
									
								</td>

								<td style="text-align: right; font-size: 13px; padding:10px 5px;">
									 ' . number_format($totalCGST, 2) . '</td>
							</tr>
							<tr>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
								</td>
								<td style="font-size: 13px; padding:10px 5px;">SGST (' . $cfg['CONFERENCE.SGST'] . '%)</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">
									998596</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
								</td>
								<td style="text-align: center; font-size: 13px; padding:10px 5px;">
									
								</td>

								<td style="text-align: right; font-size: 13px; padding:10px 5px;">
									' . number_format($totalSGST, 2) . '</td>
							</tr>';
		}
	}

	if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
		$cgst 							 = $cfg['INT.CGST'];
		$sgst 							 = $cfg['INT.SGST'];
		$gstArray 						 = gstCalculation($cgst, $sgst, 0);
		// print_r($gstArray);
		//$totalCGST 	   					+= $gstArray['CGST.PRICE'];
		//$totalSGST 	   				  	+= $gstArray['SGST.PRICE'];

		$GST_DATA_aary['INTERNET'] 					= $gstArray;
		$GST_DATA_aary['INTERNET']['CGST.RATE']	= $cgst;
		$GST_DATA_aary['INTERNET']['SGST.RATE']	= $sgst;
		$GST_DATA_aary['INTERNET']['HSN'] 			= '998429';


		$contentBody                    .= '           <tr>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $slCounter++ . '
															</td>
															<td style="font-size: 13px; padding:10px 5px;">Internet Handling Charges</td>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																998596</td>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">1
															</td>
															<td style="text-align: center; font-size: 13px; padding:10px 5px;">
																' . number_format($gstArray['BASIC.PRICE'], 2) . '
															</td>
		
															<td style="text-align: right; font-size: 13px; padding:10px 5px;">
																' . number_format($invoiceDetails['internet_handling_amount'], 2) . '</td>
														</tr>';
	}

	if ($cfg['GST.FLAG'] == 1 || $cfg['GST.FLAG'] == 2) {
		$contentBody1    .= '<table width="100%" style="font-size:11px;" border="1" cellpadding="1" cellspacing="0">
								<tbody>
									<tr>
										<th align="center" rowspan="2" colspan="2">HSN/SAC1</th>
										<th align="center" rowspan="2">Taxable Value</th>';
		// if($cfg['IGST.FLAG']==1 && $invoiceDetails['service_type']=='DELEGATE_CONFERENCE_REGISTRATION'){   											
		if ($cfg['IGST.FLAG'] == 1) {
			$contentBody1	.= '			<th align="center" colspan="2">IGST</th>
										<th align="center"  rowspan="2">TOTAL (' . getInvoiceCurrencyById($invoiceId) . ')</th>
									</tr>
									<tr>
										<th align="center">Rate</th>
										<th align="right">Amount</th>
									</tr>';
		} else {
			$contentBody1	.= '			<th align="center" colspan="2">CGST</th>
										<th align="center" colspan="2">SGST</th>
										<th align="center"  rowspan="2">TOTAL (' . getInvoiceCurrencyById($invoiceId) . ')</th>
									</tr>
									<tr>
										<th align="center">Rate</th>
										<th align="right">Amount</th>
										<th align="center">Rate</th>
										<th align="right">Amount</th>
									</tr>';
		}


		$overallTotalGST = 0;
		//echo '<pre>'; print_r($GST_DATA_aary);
		foreach ($GST_DATA_aary as $kk => $data) {
			$totalGST 			= floatval($data['CGST.PRICE']) + floatval($data['SGST.PRICE']);
			$overallTotalGST   += $totalGST;
			if ($cfg['IGST.FLAG'] == 1 && $invoiceDetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
				$contentBody1 .= '<tr>
											<td align="center">' . $data['HSN'] . '</td>
											<td></td>
											<td align="center">' . number_format($data['BASIC.PRICE'], 2) . '</td>	
											<td align="center">' . number_format($data['CGST.RATE'] + $data['SGST.RATE']) . '%</td>
											<td align="right">' . number_format($data['CGST.PRICE'] + $data['SGST.PRICE'], 2) . '</td>
											<td align="right"> ' . number_format($totalGST, 2) . '</td>
										</tr>';
			} else {
				$contentBody1 .= '<tr>
											<td align="left">' . $data['HSN'] . '</td>
											<td></td>
											<td align="center">' . number_format($data['BASIC.PRICE'], 2) . '</td>	
											<td align="center">' . $data['CGST.RATE'] . '%</td>
											<td align="right">' . number_format($data['CGST.PRICE'], 2) . '</td>
											<td align="center">' . $data['SGST.RATE'] . '%</td>
											<td align="right"> ' . number_format($data['SGST.PRICE'], 2) . '</td>
											<td align="right"> ' . number_format($totalGST, 2) . '</td>
										</tr>';
			}
		}


		$contentBody1                    .= '							<tr>
																					<td align="left" colspan="8">
																					Tax Amount (in words) : 
																					<b>' . convert_number($overallTotalGST) . ' Only.</b>
																					</td>
																				</tr>
																			</table> <br>';
	}

	$contentBody                    .= '      <tr>
												<td colspan="2"
													style="font-size: 14px; padding:10px 5px; border-bottom: 2px solid #114C5C; font-weight: bold;">
													GST TOTAL</td>
												<td colspan="4"
													style="font-size: 14px; padding:10px 5px; border-bottom: 2px solid #114C5C; font-weight: bold; text-align: right;">
													TOTAL PAYABLE</td>
											</tr>
											<tr>
												<td colspan="2"
													style="padding:10px 5px; border-bottom: 2px solid #114C5C; font-size: 20px; font-weight: bold;">
													' . number_format($overallTotalGST, 2) . '</td>
												<td colspan="4"
													style="padding:10px 5px; border-bottom: 2px solid #114C5C; font-size: 20px; text-align: right; font-weight: bold;">
													' . number_format($invoiceDetails['service_roundoff_price'], 2) . '</td>
											</tr>
											<tr>
												<td colspan="6"
													style="border-bottom: 2px solid #114C5C; padding:10px 5px;">
													<p
														style="margin: 0;font-size: 12px;letter-spacing: .8px;margin-top: 0px;">
														Amount Chargeable (in Words):
													</p>
													<p
														style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;">
													<b> ' . convert_number($invoiceDetails['service_roundoff_price']) . ' Only</b>
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>

						</tbody>
					</table>
					</td>
					</tr>
					
					<tr>
						<td colspan="2" style="text-align: right;padding-left: 30px;" >
							<p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;padding-right: 30px;padding-bottom: 10px;background: #BADEC4;">
								<img src="' . $invoice_sign_img . '" style="width:15%">
							</p>
						</td>
					</tr>
					<!-- <tr>
                    	<td colspan="2" style="text-align: right;padding-left: 30px;color: #114C5C;">
                       	 <p
                            style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;padding-right: 30px;height: 20px;padding-bottom: 5px;background: #BADEC4;">
                            </p>
                   		 </td>
                	</tr> -->
                <tr>
                    <td colspan="2"
                        style="text-align: right;padding-left: 30px;color: #114C5C;/* padding-bottom: 30px; */">
                        <p
                            style="margin: 0;font-size: 16px;letter-spacing: .8px;font-weight: 600;padding-right: 30px;background: #BADEC4;padding-bottom: 30px;">
                            Name: ' . $authorized_person_arr[0]    . '<br>' . ($authorized_person_arr[1] == '' ? '' : 'Designation: ' . $authorized_person_arr[1] . '<br>') . '
                          
                        </p>
                    </td>
                </tr>
					<tr>
						<td colspan="2">
							<p style="display: flex;color: #114C5C;font-weight: bold;align-items: center;padding: 11px 30px;justify-content: space-evenly;border-bottom: 1px solid;margin: 0;margin-left: 30px;"><span>' . $cfg['INVOICE_EMAIL'] . '</span><span>|</span><span>' . $cfg['INVOICE_CONTACT'] . '</span><span>|</span><span>' . $cfg['INVOICE_WEBSITE'] . '</span></p>
								</td>
					</tr>
					<tr>
                    <td colspan="2">
                        <p style="display: flex;color: #000000;font-weight: bold;align-items: center;padding: 11px 30px;justify-content: space-evenly;margin: 0;margin-left: 30px;font-size: 14px;
                        ">' . $cfg['INVOICE_FOOTER_TEXT'] . '</p>
                    </td>
                </tr>
				</tbody>
			</table>
		</div>';



	$contentBody1 					.= '					<table width="100%" cellpadding="0" cellspacing="0" style="font-size:11px;" border="1" cellpadding="5">
																	<tr>
																		<td width="50%"  valign="top">
																			Company\'s Pan No: ' . $cfg['PAN'] . '
																		</td>
																		<td width="50%" valign="top">
																			<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
																				<tr>
																					<td colspan=2>
																						<b>Company\'s Bank Details</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="30%">
																						Bank Name:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKNAME'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						Beneficiary Name:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BENEFECIARY'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						A/c No.:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKACNO'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						Branch Address:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKBRANCH'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						IFSC:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKIFSC'] . '</b>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td valign="top" align="right" colspan="2">
																			<table width="40%" cellpadding="1" style="font-size:11px;" border="0">
																				<tr>
																					<td align="center"><b>' . $cfg['INVOICE_AUTORIZED_SIGNATURE_PREFIX'] . '</b></td>
																				</tr>
																				<tr>
																					<td><br></td>
																				</tr>
																				<tr>
																					<td align="center">Authorized Signatory</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td valign="bottom" align="center" colspan="2">
																			This is a Computer Generated Invoice<br>
																			Subject to Kolkata Jurisdiction
																		</td>
																	</tr>
																</table> <br><br>'; //Payment Status: '.$invoiceDetails['payment_status'].'

	$contentBody1    .= '								</td>
														</tr>';
	if ($footer_image != '') {
		$contentBody1    .= '							<tr>
															<td align="center" valign="bottom" style="border-collapse:collapse;">
															<img src="' . $footer_image . '" width="100%"/>
															</td>
														</tr>';
	}
	$contentBody1    .= '						</tbody>
												</table>
											</div> ';
	/*
											
											*/
	// } else {

?>

<?php

	// }
	return $contentBody;
}
function mailInvoiceContent_pre_april2019($delegateId, $invoiceId)
{

	global $cfg, $mycms;

	include_once('function.workshop.php');
	include_once('function.delegate.php');
	include_once('function.dinner.php');
	$contentBody = "";
	$invoiceDetails  = getInvoiceDetails($invoiceId, true);


	$delegateDetails = getUserDetails($delegateId);

	$user_registration_id            = "-";

	$user_registration_id = "-";
	if ($delegateDetails['registration_payment_status'] == "PAID"  || $delegateDetails['registration_payment_status'] == "COMPLIMENTARY" || $delegateDetails['registration_payment_status'] == "ZERO_VALUE") {
		$user_registration_id        = $delegateDetails['user_registration_id'];
	}

	$totalConferenceRegistrationAmount    = 0.00;
	$totalWorkshopRegistrationAmount      = 0.00;
	$totalAccompanyRegistrationAmount     = 0.00;
	$totalAccommodationRegistrationAmount = 0.00;
	$totalTourRegistrationAmount          = 0.00;
	$totalInternetHandlingAmount          = 0.00;
	$totalTaxAmount     			      = 0.00;

	if ($invoiceDetails['has_gst'] == 'Y') {
		$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">												
													<tbody>
														<tr>
															<td align="center" style="border-collapse:collapse;">
														<img src="' . _BASE_URL_ . 'images/invoiceHeader.jpg" width="100%"/>
														</td>
														</tr>
														<tr>
															<td align="center" height="730px" style="border-collapse:collapse;" valign="top">
																<table width="100%" cellpadding="1" style="font-size:13px;" border="0">
																<tr>
																	<td colspan="7" style="border:0; width:70%;">
																	<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:left;">' .
			(($invoiceDetails['currency'] == 'USD') ? 'PROFORMA EXPORT INVOICE SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX' : 'TAX INVOICE AND RECEIPT')
			. '</div>
																	</td>
																	<td colspan="2" aligh ="right" valign="bottom" style="border:0;"><b>Billed to</b> </td>
																</tr>
																</table>
																<table width="100%" cellpadding="1" style="font-size:13px;" border="1" cellpadding="5">
																<tr>
																	<td>Invoice No:</td>
																	<td colspan="4" style="color:red;">' . $invoiceDetails['invoice_number'] . '</td>
																	<td>Date</td>	
																	<td>' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</td>
																	<td >Reg. Id: </td>
																	<td>' . $delegateDetails['user_registration_id'] . '</td>
																</tr>																	
																<tr>
																	<td>PV NO:</td>
																	<td colspan="6">' . $invoiceDetails['slipNO'] . '</td>
																	<td >Name: </td>
																	<td>' . $delegateDetails['user_full_name'] . '</td>
																</tr>
																<tr>
																	<td colspan="6">Reverse Charge (Y/N):</td>
																	<td>N</td>																		
																	<td >E-mail id: </td>
																	<td>' . $delegateDetails['user_email_id'] . '</td>
																</tr>
																<tr>
																	<td colspan="5">State: West Bengal </td>
																	<td>Code</td>
																	<td>19</td>																		
																	<td >Mobile No: </td>
																	<td>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</td>																	
																</tr>
																<tr>
																	<td colspan="7">Place of Supply: Kolkata, West Bengal</td>																		
																	<td >State: </td>
																	<td>' . $delegateDetails['state_name'] . '</td>
																</tr>
															</table>';
		$contentBody                    .= ' <div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">Details</div>';

		$contentBody                    .= '<table width="100%" style="font-size:13px;" border="1" cellpadding="5">
												<tbody>
												<tr>
													<th align="center">Sl. No.</th>
													<th align="left">Services  Description</th>
													<th align="center">SAC Code</th>
													<th align="center">UOM</th>
													<th align="center">Qty</th>
													<th colspan="2" align="right">Amount (' . getInvoiceCurrencyById($invoiceId) . ')</th>
												</tr>';
		if ($invoiceDetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			if ($cfg['IGST.FLAG'] == 0) {
				$cgst = $cfg['CONFERENCE.CGST'];
				$sgst = $cfg['CONFERENCE.SGST'];
			} else {
				$cgst = $cfg['CONFERENCE.IGST'] / 2;
				$sgst = $cfg['CONFERENCE.IGST'] / 2;
			}
			$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">Conference Registration - ' . getRegClsfName(getUserClassificationId($delegateId, true)) . '</td>
													<td align="center">998596</td>	
													<td align="center">No. </td>
													<td align="center">1 </td>
													<td colspan="2" align="right"> ' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
			$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id'], true);
			$workshop_id                     = $workShopDetails['workshop_id'];
			$cgst 							 = $cfg['WORKSHOP.CGST'];
			$sgst 							 = $cfg['WORKSHOP.SGST'];
			$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">' . getWorkshopName($workShopDetails['workshop_id']) . '(' . date("M d, Y", strtotime(getWorkshopDate($workShopDetails['workshop_id']))) . ')<br>' . $session . '</td>
													<td align="center">998596</td>	
													<td align="center">No. </td>
													<td align="center">' . $invoiceDetails['service_consumed_quantity'] . '</td>
													<td colspan="2" align="right">' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}
		if ($invoiceDetails['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$cgst 							 = $cfg['ACCOMPANY.CGST'];
			$sgst 							 = $cfg['ACCOMPANY.SGST'];
			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id'], true);
			$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">Accompanying Person Registration <br />' . $accompanyDetails['user_full_name'] . '</td>
													<td align="center">998596</td>	
													<td align="center">No. </td>
													<td align="center">1 </td>
													<td colspan="2" align="right">' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			$sqlaccommodationDetails 				 = 	array();
			$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,masterHotel.hotel_address AS hotel_address
													 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
											   INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
													   ON masterHotel.id = accommodation.hotel_id
													 WHERE accommodation.status = ? 
													   AND accommodation.user_id = ?";
			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',                'DATA' => 'A',   'TYP' => 's');
			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',               'DATA' => $delegateId,   'TYP' => 's');

			$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);

			$rowaccommodation = $resaccommodation[0];
			$cgst 							 = $cfg['ACCOMMODATION.CGST'];
			$sgst 							 = $cfg['ACCOMMODATION.SGST'];
			$contentBody                    .= '
												<tr>
													<td align="center">1</td>';


			$contentBody                    .= '<td>' . getRegClsfName(getUserClassificationId($delegateId, true)) . '<br/>' . $rowaccommodation['hotel_name'] . '</td>';
			$contentBody                    .= '<td align="center">998596</td>	
													<td align="center">No. </td>
													<td align="center">1 </td>
													<td colspan="2" align="right">' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id'], true);
			$dinnerDetails   		=  getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id'], true);
			$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);

			$cgst 							 = $cfg['DINNER.CGST'];
			$sgst 							 = $cfg['DINNER.SGST'];
			$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">' . $cfg['BANQUET_DINNER_NAME'] . ' Registration(' . $cfg['BANQUET_DINNER_DATE'] . ')<br>' . $dinnerUserDetails['user_full_name'] . '</td>
													<td align="center">998596</td>	
													<td align="center">No. </td>
													<td align="center">1 </td>
													<td colspan="2" align="right">' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*,package.package_name, package.id AS packageId
													  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
												INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
														ON accommodation.package_id = package.id
													 WHERE accommodation.status = ? 
													   AND accommodation.user_id = ?
													   AND accommodation.id = ? ";

			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',        'DATA' => 'A',   							 'TYP' => 's');
			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',       'DATA' => $invoiceDetails['delegateId'],   'TYP' => 's');
			$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.id',            'DATA' => $invoiceDetails['refference_id'],   'TYP' => 's');

			$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
			$rowaccommodation = $resaccommodation[0];

			$cgst 							 = $cfg['ACCOMMODATION.CGST'];
			$sgst 							 = $cfg['ACCOMMODATION.SGST'];
			$accommodationDtls = getAccomodationDetails($invoiceDetails['refference_id'], true);

			$contentBody                    .= '
												<tr>
													<td colspan="" align="center">1</td>
													<td colspan="" align="left">Accommodation Booking - (' . getAccomPackageName($rowaccommodation['packageId']) . ')<br />
													' . $accommodationDtls['checkin_date'] . ' to ' . $accommodationDtls['checkout_date'] . '														
													</td>
													<td colspan="" align="center">998596</td>	
													<td colspan="" align="center">No. </td>
													<td align="center">1 </td>
													<td colspan="2" align="right">' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}
		if (floatval($invoiceDetails['discount_amount']) > 0) {
			$contentBody                    .= '	<tr> 
													<td align="center">&nbsp;</td>
													<td colspan="4">Discount Amount (-)</td>
													<td align="right" colspan="2">' . $invoiceDetails['discount_amount'] . '</td>
												</tr>';
		}

		if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
			$cgst 							 = $cfg['INT.CGST'];
			$sgst 							 = $cfg['INT.SGST'];

			$contentBody                    .= '<tr>
												<td align="center">2</td>
												<td>Internet Handling Charges</td>
												<td align="center">998429</td>	
												<td align="center">No. </td>
												<td align="center">1 </td>
												<td  align="right" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
											</tr>';
			/*$contentBody                    .= '	<tr>
													<td>&nbsp;</td>
													<td colspan="4">CGST @ '.number_format($invoiceDetails['cgst_internet_handling_percentage'],0).'%</td>
													<td align="right" colspan="2">'.$invoiceDetails['cgst_internet_handling_price'].'</td>
												</tr>';
		$contentBody                    .= '	<tr>
													<td>&nbsp;</td>
													<td colspan="4">SGST @ '.number_format($invoiceDetails['sgst_internet_handling_percentage'],0).'%</td>
													<td align="right" colspan="2">'.$invoiceDetails['sgst_internet_handling_price'].'</td>
												</tr>';*/
		}


		$contentBody 					.= '<tr>
										<td colspan="5" align="right">';

		$contentBody                    .=		'<span style="float:left; padding-left:1%; color:red;">Inclusive of All Taxes</span><b>Total (Rounded)</b></td>
										<td align="right" colspan="2"><b>' . $invoiceDetails['service_roundoff_price'] . '</b></td>
									</tr>
									<tr>
										<td colspan="6">In word:  <i>' . convert_number($invoiceDetails['service_roundoff_price']) . ' Only.</i></td>
										<td align="right">Payment Status: <b>' . $invoiceDetails['payment_status'] . '</b></td>
									</tr>
									<tr>
										<td colspan="2"></td>
										<td  colspan="5" align="right">Pan No: ' . $cfg['PAN'] . '</td>
									</tr>
								</tbody>
							</table>
							<br><br>
							<div style="text-align:left;">
							<b>NOTE : </b><br>
							<span style="font-size:12px;">By registering for AICC RCOG 2019, you hereby agree to receive all promotional SMS and e-mails. To unsubscribe, send us a mail at secretariat@aiccrcog2019.com</span>
							</div><br><br>
							<table>
								<tr>
									<td style="font-size: x-small;background-color: #EEEAE9;" colspan="2"><ul>
										' . getNoticeDetails("Cancellation & Refund Policy") . '
									</td>
								</tr>
							</table>';

		$contentBody    .= '</td>
					</tr>
					
					<tr>
						<td align="center" valign="bottom" style="border-collapse:collapse;">
						<img src="' . _BASE_URL_ . 'images/invoiceFooter.jpg" width="100%"/>
						</td>
					</tr>	
				</tbody>
			</table>
			</div> ';
	} else {
		/*$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">												
												<tr>
													<td align="center" style="border-collapse:collapse;">
													<img src="'.$cfg['BASE_URL'].'images/header20191011.jpg" width="100%"/>
													</td>
												</tr>
												<tr>
													<td align="center" height="360px" style="border-collapse:collapse;" valign="top">
														<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
														'.(($invoiceDetails['currency']=='USD')?'PROFORMA EXPORT INVOICE SUPPLY MEANT FOR EXPORT ON PAYMENT OF INTEGRATED TAX':'TAX INVOICE AND RECEIPT').'
														</div>
														<table width="100%" cellpadding="1" style="font-size:13px;">
															<tr>
																<td width="18%"></td>
																<td width="32%"></td>
																<td width="18%"></td>
																<td width="32%"></td>
															</tr>
															<tr>
																<td>Name:</td>
																<td>'.$delegateDetails['user_full_name'].'</td>
																<td width="18%">Date:</td>
																<td width="32%">'.date("d/m/Y", strtotime($invoiceDetails['invoice_date'])).'</td>
															</tr>
															<tr>
																<td>E-mail id:</td>
																<td>'.$delegateDetails['user_email_id'].'</td>
																<td>Invoice No:</td>
																<td>'.$invoiceDetails['invoice_number'].'</td>
															</tr>
															<tr>
																<td>Mobile:</td>
																<td>'.$delegateDetails['user_mobile_isd_code'].' '.$delegateDetails['user_mobile_no'].'</td>
																<td>Registration Id:</td>
																<td>'.$user_registration_id.'</td>
																</td>
															</tr>
															<tr>
																<td>PV No:</td>
																<td>'.$invoiceDetails['slipNO'].'</td>
																<td></td>
																<td></td>
															</tr> 
														</table>';
	
	
	if($invoiceDetails['service_type'] =="DELEGATE_CONFERENCE_REGISTRATION")
	{
		$totalAmount	= 0;
		$contentBody                				.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
														 Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Conference - '.getRegClsfName(getUserClassificationId($delegateId)).'</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.($invoiceDetails['service_unit_price']==0.00?'Complimentary':$invoiceDetails['service_unit_price']).'</td>
															</tr>';
		$totalAmount += $invoiceDetails['service_unit_price'];
		if($invoiceDetails['invoice_mode']=='ONLINE' && $invoiceDetails['service_roundoff_price']!=0.00)
		{										
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
															</tr>';
			$totalAmount += $invoiceDetails['internet_handling_amount'];
		}
		
		if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
		{
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
															 </tr>';
			$totalAmount -= $discountAmount;
		}
		
		if($invoiceDetails['service_roundoff_price']!=0.00)
		{
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
															</tr>';											
													
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
															</tr>';
		}
		$contentBody                    			.= '</table>';
	}
	
	if($invoiceDetails['service_type'] =="DELEGATE_RESIDENTIAL_REGISTRATION")
	{
		$totalAmount	 = 0;
		$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							 Registration Details
							</div>
							<table width="100%" style="font-size:13px;">
								<tr>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
									<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
									<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
								</tr>
								
								
								<tr>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">AI PACKAGE - '.getRegClsfName(getUserClassificationId($delegateId)).'</td>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['service_unit_price'].'</td>
								</tr>';
		$totalAmount 	 += $invoiceDetails['service_unit_price'];
		if($invoiceDetails['invoice_mode']=='ONLINE')
		{										
													
		   $contentBody       .='<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
								</tr>';
			$totalAmount += $invoiceDetails['internet_handling_amount'];
		}			
		$contentBody         .='<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['service_roundoff_price'].'</td>
								</tr>
								
								<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
								</tr>	
							</table>';
	}				
		
	if($invoiceDetails['service_type'] =="DELEGATE_WORKSHOP_REGISTRATION")
	{
		$totalAmount					 = 0;
		$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id']);
		$workshop_id                     = $workShopDetails['workshop_id'];
			
		$contentBody    				.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
										 Registration Details
										</div>
										<table width="100%" style="font-size:13px;">
											<tr>
												<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
												<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
												<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
												<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
											</tr>
											<tr>
												<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>WORKSHOP</b><br>'.getWorkshopName($workShopDetails['workshop_id']).'</td>
												<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($workShopDetails['tariff_cutoff_id']).'</td>
												<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
												<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['service_unit_price'].'</td>
											</tr>';
		$totalAmount += $invoiceDetails['service_unit_price'];
		if($invoiceDetails['invoice_mode']=='ONLINE')
		{											
		   $contentBody       						.=	   '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
															</tr>';
		   $totalAmount += $invoiceDetails['internet_handling_amount'];
		}		
		
		if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
		{
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
															</tr>';
			$totalAmount -= $discountAmount;
				  
		}
		$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
															</tr>';											
													
		$contentBody                    			.=     '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
															</tr>
														</table>';
	}
					
	if($invoiceDetails['service_type'] =="ACCOMPANY_CONFERENCE_REGISTRATION")
	{
		$totalAmount					 = 0;
		$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);
	
		$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
															Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Accompanying Person</b><br>'.$accompanyDetails['user_full_name'].'</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.($invoiceDetails['service_unit_price']==0.00?'Complimentary':$invoiceDetails['service_unit_price']).'</td>
															</tr>';
		$totalAmount += $invoiceDetails['service_unit_price'];
		if($invoiceDetails['invoice_mode']=='ONLINE' && $invoiceDetails['service_roundoff_price']!=0.00)
		{											
			$contentBody                    			.=  '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
															</tr>';
			$totalAmount += $invoiceDetails['internet_handling_amount'];
		}
		
		if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
		{
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
															</tr>';
			$totalAmount -= $discountAmount;
		}
		$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
															</tr>';											
													
		$contentBody                    			.=     '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
															</tr>
														</table>';
	}
	
	if($invoiceDetails['service_type'] =="DELEGATE_TOUR_REQUEST")
	{
		$totalAmount	 = 0;
		$tourDetails  	 = getTourDetails($invoiceDetails['refference_id']);
		$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
									Tour Booking Details
								</div>
								<table width="100%" style="font-size:13px;">
									<tr>
										<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
										<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
										<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
										<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Details</td>
										<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
									</tr>
									<tr>
										<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="2">'.getTourName($tourDetails['package_id']).'</td>
										<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">'.getCutoffName($tourDetails['tariff_cutoff_id']).'</td>
										<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">'.$invoiceDetails['service_consumed_quantity'].'</td>
										<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
										<td align="right" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_unit_price'].'</td>
									</tr>
									<tr>
										<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
										<td align="right" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['internet_handling_amount'].'</td>
									</tr>
									<tr>
										<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
										<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
										<td align="right" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_roundoff_price'].'</td>
									</tr>
									<tr>
										<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($invoiceDetails['service_roundoff_price']).' Only.</u></td>
									</tr>
								</table>';
	
	}
	
	if($invoiceDetails['service_type'] =="DELEGATE_DINNER_REQUEST")
	{
		$totalAmount			= 0;
		$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id']);
		$dinnerDetails   		= getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id']);
		$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);
	
		$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
															Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount ('.getRegistrationCurrency(getUserClassificationId($delegateId)).')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Gala Dinner</b><br>'.$dinnerUserDetails['user_full_name'].'</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.getCutoffName($delegateDetails['registration_tariff_cutoff_id']).'</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">'.$invoiceDetails['service_consumed_quantity'].'</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.($invoiceDetails['service_unit_price']==0.00?'Complimentary':$invoiceDetails['service_unit_price']).'</td>
															</tr>';
		$totalAmount += $invoiceDetails['service_unit_price'];
		if($invoiceDetails['invoice_mode']=='ONLINE' && $invoiceDetails['service_roundoff_price']!=0.00)
		{											
		   $contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.$invoiceDetails['internet_handling_amount'].'</td>
															</tr>';
		   $totalAmount += $invoiceDetails['internet_handling_amount'];
		}
		
		if($invoiceDetails['invoice_mode']=='OFFLINE' && $discountAmount !="")
		{
			$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($discountAmount,2).'</td>
															</tr>';
			$totalAmount -= $discountAmount;
		}
		
		$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">'.number_format($totalAmount,2).'</td>
															</tr>';											
													
		$contentBody                   				.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>'.convert_number($totalAmount).' Only.</u></td>
															</tr>
														</table>';
	}
					
	$contentBody    								.= '<table width="100%" style="font-size:13px;">
															<tr>
																<td><br /><br /></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr><td style="font-size: x-small;background-color: #EEEAE9;">'.getNoticeDetails("Cancellation & Refund Policy").'</td></tr>
												<tr>
													<td align="center" valign="bottom" style="border-collapse:collapse;">
													<img src="'.$cfg['BASE_URL'].'images/footer20191011.jpg" width="100%"/>
													</td>
												</tr>	
											</table>
										</div>';*/
	}
	return $contentBody;
}

function mailInvoiceContentEXhibitor($delegateId, $invoiceId)
{
	global $cfg, $mycms;
	$contentBody     = "";
	$sqlInvoice['QUERY']  = "SELECT *, slipDetails.slip_number AS slipNO,invDetails.service_gst_price 
					 FROM  " . _DB_EXIBITOR_INVOICE_ . " invDetails
		  LEFT OUTER JOIN " . _DB_EXIBITOR_SLIP_ . " slipDetails
					   ON slipDetails.id = invDetails.slip_id
					WHERE invDetails.id ='" . $invoiceId . "' 
					  AND invDetails.status = 'A' ";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$invoiceDetails = $resInvoice[0];
	$slip_id         = $invoiceDetails['slip_id'];


	$originalAmount 		= number_format($returnArray['PRE_DISCOUNT_AMOUNT'], 2);
	$percentage  			= $returnArray['PERCENTAGE'];
	$totalAmount 			= $returnArray['TOTAL_AMOUNT'];
	$discountAmount 		= $returnArray['DISCOUNT'];
	$cgstAmount				= $returnArray['CGST_AMOUNT'];
	$sgstAmount				= $returnArray['SGST_AMOUNT'];

	$cgstDiscountAmount		= number_format($returnArray['CGST_DISCOUNT'], 2);
	$sgstDiscountAmount		= number_format($returnArray['SGST_DISCOUNT'], 2);

	$internetHandAmount		= number_format($returnArray['INT_HND_AMOUNT'], 2);

	if ($cgstDiscountAmount > 0 && $sgstDiscountAmount) {
		$discountAmount = $returnArray['DISCOUNT'] - $returnArray['CGST_DISCOUNT'] - $returnArray['SGST_DISCOUNT'];
	}

	if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
		$totalAmount = $returnArray['TOTAL_AMOUNT'] - $returnArray['INT_HND_AMOUNT'];
	}

	$delegateDetails['QUERY'] = "SELECT * ,country_list.country_name,state_list.state_name 
									 FROM " . _DB_USER_REGISTRATION_ . " registration_list
						  LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country_list
									   ON registration_list.user_country_id = country_list . country_id
						  LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state_list
									   ON registration_list.user_state_id = state_list.st_id 
									WHERE registration_list.id = '" . $delegateId . "'";

	$resDetails = $mycms->sql_select($delegateDetails);
	$delegateDetails						= $resDetails[0];

	$user_registration_id            = "-";

	$user_registration_id = "-";

	if ($delegateDetails['registration_payment_status'] == "PAID"  || $delegateDetails['registration_payment_status'] == "COMPLIMENTARY" || $delegateDetails['registration_payment_status'] == "ZERO_VALUE") {
		$user_registration_id        = $delegateDetails['user_registration_id'];
	}

	$totalConferenceRegistrationAmount    = 0.00;
	$totalWorkshopRegistrationAmount      = 0.00;
	$totalAccompanyRegistrationAmount     = 0.00;
	$totalAccommodationRegistrationAmount = 0.00;
	$totalTourRegistrationAmount          = 0.00;
	$totalInternetHandlingAmount          = 0.00;
	$totalTaxAmount     			      = 0.00;

	if ($invoiceDetails['has_gst'] == 'Y') {
		$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">												
													<tbody>';

		$contentBody                    .= '			<tr>
															<td align="center" style="border-collapse:collapse;">
																<img src="' . _BASE_URL_ . 'images/header20191011.jpg" width="790px" />
															</td>
														</tr>';
		$contentBody                    .= '			<tr>
															<td align="center" height="500px" style="border-collapse:collapse;" valign="top">
																<table width="100%" cellpadding="1" style="font-size:13px;" border="0">
																<tr>
																	<td colspan="7" style="border:0; width:70%;">
																	<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:left;">
																	TAX INVOICE AND RECEIPT
																	</div>
																	</td>
																	<td colspan="2" aligh ="right" valign="bottom" style="border:0;"><b>Billed to</b> </td>
																</tr>
																</table>
																<table width="100%" cellpadding="1" style="font-size:13px;" border="1" cellpadding="5">
																<tr>
																	<td>Invoice No :</td>
																	<td colspan="4" style="color:red;">' . $invoiceDetails['invoice_number'] . '</td>
																	<td>Date</td>	
																	<td>' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</td>
																	<td >Reg. Id: </td>
																	<td>' . $delegateDetails['user_registration_id'] . '</td>
																</tr>																	
																<tr>
																	<td>PV NO :</td>
																	<td colspan="6">' . $invoiceDetails['slipNO'] . '</td>
																	<td >Name: </td>
																	<td>' . $delegateDetails['user_full_name'] . '</td>
																</tr>
																<tr>
																	<td colspan="6">Reverse Charge (Y/N) :</td>
																	<td>N</td>																		
																	<td >E-mail id: </td>
																	<td>' . $delegateDetails['user_email_id'] . '</td>
																</tr>
																<tr>
																	<td colspan="5">State: West Bengal </td>
																	<td>Code</td>
																	<td>19</td>																		
																	<td >Mobile No: </td>
																	<td>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</td>																	
																</tr>
																<tr>
																	<td colspan="7">Place of Supply: Kolkata, West Bengal</td>																		
																	<td >State: </td>
																	<td>' . $delegateDetails['state_name'] . '</td>
																</tr>
															</table>';
		$contentBody                    .= ' <div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">Details</div>';

		$contentBody                    .= '<table width="100%" style="font-size:13px;" border="1" cellpadding="5">
												<tbody>
												<tr>
													<th align="center">Sl. No.</th>
													<th align="left">Services  Description</th>
													<th align="center">SAC Code</th>
													<th align="center">UOM</th>
													<th align="center">Qty</th>
													<th colspan="2" align="right">Amount (' . $invoiceDetails['currency'] . ')</th>
												</tr>';
		if ($invoiceDetails['service_type'] == 'EXHIBITOR_REPRESENTATIVE_REGISTRATION') {
			if ($cfg['IGST.FLAG'] == 0) {
				$cgst = $cfg['CONFERENCE.CGST'];
				$sgst = $cfg['CONFERENCE.SGST'];
			} else {
				$cgst = $cfg['CONFERENCE.IGST'] / 2;
				$sgst = $cfg['CONFERENCE.IGST'] / 2;
			}
			$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">Representative Registration  </td>
													<td align="center">XXXX</td>	
													<td align="center">No. </td>
													<td align="center">1 </td>
													<td colspan="2" align="right"> ' . $invoiceDetails['service_unit_price'] . '</td>
												</tr>';
		}


		if (floatval($percentage) > 0) {
			$contentBody                    .= '	<tr> 
													<td align="center">&nbsp;</td>
													<td colspan="4">DISCOUNT</td>
													<td align="right" colspan="2">(-)' . number_format($discountAmount) . '</td>
												</tr>';
		}

		if (floatval($invoiceDetails['cgst_percentage']) > 0) {
			$contentBody                    .= '	<tr> 
													<td align="center">&nbsp;</td>
													<td colspan="4">CGST @ ' . (($invoiceDetails['cgst_percentage'] == "9.00") ? 9 : 14) . '%</td>
													<td align="right" colspan="2">' . number_format($invoiceDetails['cgst_price'], 2) . '</td>
												</tr>';
		}

		if (floatval($invoiceDetails['sgst_percentage']) > 0) {
			$contentBody                    .= '	<tr>
													<td align="center">&nbsp;</td>
													<td colspan="4">SGST @ ' . (($invoiceDetails['sgst_percentage'] == "9.00") ? 9 : 14) . '%</td>
													<td align="right" colspan="2">' . number_format($invoiceDetails['sgst_price'], 2) . '</td>
												</tr>';
		}

		$contentBody 					.= '<tr>
										<td colspan="5" align="right">';
		if ($invoiceDetails['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$contentBody 				.=		'<span style="float:left; padding-left:1%; color:red;">Inclusive of Govt. tax</span>';
		}
		$contentBody                    .=		'<b>Total (Rounded)</b></td>
										<td align="right" colspan="2"><b>' . number_format($invoiceDetails['service_gst_price'], 2) . '</b></td>
									</tr>
									<tr>
										<td colspan="6">In word :  <i>' . convert_number($invoiceDetails['service_gst_price']) . ' Only.</i></td>
										<td align="right">Payment Status : <b>' . $invoiceDetails['payment_status'] . '</b></td>
									</tr>
								</tbody>
							</table><br>';

		$contentBody                    .= ' <table width="100%" style="font-size:13px;argin-top: 2%;" border="1" >
										<tbody>';
		if ($invoiceDetails['cgst_price'] > 0 && $invoiceDetails['sgst_price'] > 0) {
			$contentBody                 .= 		'<tr>
												<td align="right">Collected CGST</td>
												<td align="right">' . number_format($invoiceDetails['cgst_price'], 2) . '</td>
											</tr>
											<tr>
												<td align="right">Collected SGST</td>
												<td align="right">' . number_format($invoiceDetails['sgst_price'], 2) . '</td>
											</tr>';
		}
		$contentBody                     .= 		'<tr>
												<td colspan="2">GSTIN: ' . $cfg['GSTIN'] . '<br>PAN &nbsp;&nbsp; : ' . $cfg['PAN'] . '<br></td>
											</tr>
										</tbody>
									</table>';

		$contentBody    .= '</td>
					</tr>
					<tr>
						<td style="font-size: x-small;background-color: #EEEAE9;" colspan="2"><ul>
							' . getNoticeDetails("Cancellation & Refund Policy") . '
						</td>
					</tr>
					<tr>
						<td align="center" valign="bottom" style="border-collapse:collapse;">
						<img src="' . _BASE_URL_ . 'images/footer20191011.jpg" width="790px" />
						</td>
					</tr>	
				</tbody>
			</table>
			</div> ';
	} else {
		$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">												
												<tr>
													<td align="center" style="border-collapse:collapse;">
													<img src="' . _BASE_URL_ . 'images/header20191011.jpg" width="790px"/>
													</td>
												</tr>
												<tr>
													<td align="center" height="360px" style="border-collapse:collapse;" valign="top">
														<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
														INVOICE/RECEIPT
														</div>
														<table width="100%" cellpadding="1" style="font-size:13px;">
															<tr>
																<td width="18%"></td>
																<td width="32%"></td>
																<td width="18%"></td>
																<td width="32%"></td>
															</tr>
															<tr>
																<td>Name:</td>
																<td>' . $delegateDetails['user_full_name'] . '</td>
																<td width="18%">Date:</td>
																<td width="32%">' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</td>
															</tr>
															<tr>
																<td>E-mail id:</td>
																<td>' . $delegateDetails['user_email_id'] . '</td>
																<td>Invoice No:</td>
																<td>' . $invoiceDetails['invoice_number'] . '</td>
															</tr>
															<tr>
																<td>Mobile:</td>
																<td>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</td>
																<td>Registration Id:</td>
																<td>' . $user_registration_id . '</td>
																</td>
															</tr>
															<tr>
																<td>PV No:</td>
																<td>' . $invoiceDetails['slipNO'] . '</td>
																<td></td>
																<td></td>
															</tr> 
														</table>';


		if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {

			$contentBody                				.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
														 Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Conference - ' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . ($invoiceDetails['service_unit_price'] == 0.00 ? 'Complimentary' : $invoiceDetails['service_unit_price']) . '</td>
															</tr>';

			if ($invoiceDetails['invoice_mode'] == 'ONLINE' && $invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
				$totalAmount = $totalAmount + $invoiceDetails['internet_handling_amount'];
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															 </tr>';
			}

			if ($invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>';
			}
			$contentBody                    			.= '</table>';
		}

		if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
			$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							 Registration Details
							</div>
							<table width="100%" style="font-size:13px;">
								<tr>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
									<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
									<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
								</tr>
								
								
								<tr>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">Residential Registration - ' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['service_unit_price'] . '</td>
								</tr>';
			if ($invoiceDetails['invoice_mode'] == 'ONLINE') {

				$contentBody       .= '<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
								</tr>';
			}
			$contentBody                 .= '<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['service_roundoff_price'] . '</td>
								</tr>
								
								<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
								</tr>							
								
								
							</table>';
		}

		if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id']);
			$workshop_id                     = $workShopDetails['workshop_id'];


			$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
														 Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>WORKSHOP</b><br>' . getWorkshopName($workShopDetails['workshop_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($workShopDetails['tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['service_unit_price'] . '</td>
															</tr>';
			if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
				$contentBody       						.=	   '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
				$totalAmount = $totalAmount + $invoiceDetails['internet_handling_amount'];
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															</tr>';
			}
			$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

			$contentBody                    			.=     '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>
														</table>';
		}

		if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);

			$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
															Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Accompanying Person</b><br>' . $accompanyDetails['user_full_name'] . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . ($invoiceDetails['service_unit_price'] == 0.00 ? 'Complimentary' : $invoiceDetails['service_unit_price']) . '</td>
															</tr>';

			if ($invoiceDetails['invoice_mode'] == 'ONLINE' && $invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
				$totalAmount = $totalAmount + $invoiceDetails['internet_handling_amount'];
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															</tr>';
			}
			$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

			$contentBody                    			.=     '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>
														</table>';
		}

		if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
			$tourDetails  = getTourDetails($invoiceDetails['refference_id']);

			$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
									Tour Booking Details
								</div>
								<table width="100%" style="font-size:13px;">
									<tr>
										<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
										<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
										<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
										<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Details</td>
										<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
									</tr>
									<tr>
										<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="2">' . getTourName($tourDetails['package_id']) . '</td>
										<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">' . getCutoffName($tourDetails['tariff_cutoff_id']) . '</td>
										<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">' . $invoiceDetails['service_consumed_quantity'] . '</td>
										<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
										<td align="right" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_unit_price'] . '</td>
									</tr>
									<tr>
										<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
										<td align="right" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['internet_handling_amount'] . '</td>
									</tr>
									<tr>
										<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
										<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
										<td align="right" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_roundoff_price'] . '</td>
									</tr>
									<tr>
										<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($invoiceDetails['service_roundoff_price']) . ' Only.</u></td>
									</tr>
								</table>';
		}

		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id']);
			$dinnerDetails   		=  getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id']);
			$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);

			$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
															Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Gala Dinner</b><br>' . $dinnerUserDetails['user_full_name'] . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . ($invoiceDetails['service_unit_price'] == 0.00 ? 'Complimentary' : $invoiceDetails['service_unit_price']) . '</td>
															</tr>';
			if ($invoiceDetails['invoice_mode'] == 'ONLINE' && $invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
				$totalAmount = $totalAmount + $invoiceDetails['internet_handling_amount'];
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															</tr>';
			}

			$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

			$contentBody                   				.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>
														</table>';
		}

		$contentBody    								.= '<table width="100%" style="font-size:13px;">
															<tr>
																<td><br /><br /></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr><td style="font-size: x-small;background-color: #EEEAE9;">' . getNoticeDetails("Cancellation & Refund Policy") . '</td></tr>
												<tr>
													<td align="center" valign="bottom" style="border-collapse:collapse;">
													<img src="' . _BASE_URL_ . 'images/footer20191011.jpg" width="790px"/>
													</td>
												</tr>	
											</table>
										</div>';
	}

	return $contentBody;
}

function getRuedaInvoiceNo($invoiceId)
{
	return "17-18/IMS/" . number_pad($invoiceId, 6);
}

function getRuedaInvoiceId($invoiceNo)
{
	$e = explode("/", $invoiceNo);
	return intval($e[2]);
}

function ruedaMailInvoiceContent($delegateId, $invoiceId)
{
	global $cfg, $mycms;
	$contentBody     = "";
	$invoiceDetails  = getInvoiceDetails($invoiceId, true);
	$slip_id         = $invoiceDetails['slip_id'];

	$returnArray 	 = discountAmount($invoiceId);
	$percentage  	 = $returnArray['PERCENTAGE'];
	$totalAmount 	 = $returnArray['TOTAL_AMOUNT'];
	$discountAmount  = $returnArray['DISCOUNT'];
	$paymentDtls 	 = paymentDetails($invoiceDetails['slip_id']);



	$delegateDetails = getUserDetails($delegateId);

	$user_registration_id            = "-";

	$user_registration_id = "-";
	if ($delegateDetails['registration_payment_status'] == "PAID"  || $delegateDetails['registration_payment_status'] == "COMPLIMENTARY" || $delegateDetails['registration_payment_status'] == "ZERO_VALUE") {
		$user_registration_id        = $delegateDetails['user_registration_id'];
	}

	$totalConferenceRegistrationAmount    = 0.00;
	$totalWorkshopRegistrationAmount      = 0.00;
	$totalAccompanyRegistrationAmount     = 0.00;
	$totalAccommodationRegistrationAmount = 0.00;
	$totalTourRegistrationAmount          = 0.00;
	$totalInternetHandlingAmount          = 0.00;
	$totalTaxAmount     			      = 0.00;

	if ($invoiceDetails['has_gst'] == 'Y') {
		$paymentModeDisplay = $paymentDtls['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($paymentDtls['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $paymentDtls['payment_mode']);
		$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">												
													<tbody>															
														<tr>
															<td align="center" height="400px" style="border-collapse:collapse;" valign="top">
																<table width="100%" cellpadding="1" style="font-size:13px;" border="0">
																<tr>
																	<td colspan="7" style="border:0; width:70%;">
																	<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:left;">
																	TAX INVOICE AND RECEIPT
																	</div>
																	</td>																	
																</tr>
																</table>
																<table width="100%" cellpadding="1" style="font-size:13px;" border="1" cellpadding="5">
																	<tr>
																		<td rowspan="2" style="width:33%;"><b>NNF Room, IMA House,</b><br /><br />
																			53 Creek Row,<br />
																			Kolkata - 7000 014<br />
																			GST IN/UIN: ' . $cfg['GSTIN'] . '
																		</td>
																		<td valign="top" style="width:33%;">Invoice No.<br />
																			<b>' . getRuedaInvoiceNo($invoiceId) . '</b>
																		</td>
																		<td valign="top">Dated<br />
																			<b>' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</b>
																		</td>
																	</tr>
																	<tr>
																		<td valign="top">Delivery Note<br />
																		</td>
																		<td valign="top">Mode/Terms of Payment<br />
																			' . $paymentModeDisplay . '
																		</td>																		
																	</tr>																	
																	<tr>
																		<td valign="top" rowspan="5">
																		Billed To <br />
																		<b>' . $delegateDetails['user_full_name'] . '</b>
																		<br />
																		Address<br />
																		' . $delegateDetails['user_address'] . '<br />
																		' . $delegateDetails['user_city_id'] . ',' . $delegateDetails['state_name'] . ', ' . $delegateDetails['country_name'] . ',<br/>
																		PIN - ' . $delegateDetails['user_pincode'] . '
																		</td>
																		<td valign="top">' . "Supplier's" . ' Ref.<br />
																		</td>
																		<td valign="top">Other Reference(s)<br />
																		</td>																		
																	</tr>
																	<tr>
																		<td valign="top">' . "Buyer's" . ' Order No.<br />
																		</td>
																		<td valign="top">Dated<br />																			
																		</td>																		
																	</tr>
																	<tr>
																		<td valign="top">Despatch Document No.<br />
																		</td>
																		<td valign="top">Delivery Note Date<br />																			
																		</td>																		
																	</tr>
																	<tr>
																		<td valign="top">Despatched through<br />
																		</td>
																		<td valign="top">Destination<br />																			
																		</td>																		
																	</tr>
																	<tr>
																		<td valign="top" colspan="2">Terms of Delivery<br />
																		</td>																																				
																	</tr>																																	
																</table><br>';
		$contentBody                    .= '					<table width="100%" style="font-size:13px;" border="1" cellpadding="5">
																	<tbody>
																	<tr>
																		<th align="center">Sl. No.</th>
																		<th align="left">Particulars</th>
																		<th align="center">HSN/SAC</th>													
																		<th align="center">Quantity</th>
																		<th align="center">Rate</th>
																		<th align="center">per</th>
																		<th align="right">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</th>
																	</tr>';


		if ($invoiceDetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			if (false) {
				if ($cfg['IGST.FLAG'] == 0) {
					$cgst = $cfg['CONFERENCE.CGST'];
					$sgst = $cfg['CONFERENCE.SGST'];
				} else {
					$cgst = $cfg['CONFERENCE.IGST'] / 2;
					$sgst = $cfg['CONFERENCE.IGST'] / 2;
				}
				$contentBody                    .= '
													<tr>
														<td align="center">1</td>
														<td align="left">Conference Registration - ' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>
														<td align="center">998596</td>	
														<td align="center">1</td>
														<td align="center"></td>
														<td align="center"></td>
														<td align="right"> ' . $invoiceDetails['service_basic_price'] . '</td>
													</tr>';
			} else {
				$ref = 'Conference Registration - ' . getRegClsfName(getUserClassificationId($delegateId));
			}
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
			$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id']);
			$workshop_id                     = $workShopDetails['workshop_id'];
			if (false) {
				// $cgst 							 = $cfg['WORKSHOP.CGST'];
				// $sgst 							 = $cfg['WORKSHOP.SGST'];
				if ($cfg['IGST.FLAG'] == 0) {
					$cgst = $cfg['CONFERENCE.CGST'];
					$sgst = $cfg['CONFERENCE.SGST'];
				} else {
					$cgst = $cfg['CONFERENCE.IGST'] / 2;
					$sgst = $cfg['CONFERENCE.IGST'] / 2;
				}
				$contentBody                    .= '
													<tr>
														<td align="center">1</td>
														<td align="left">Workshop Registration<br>' . $session . '<br>' . getWorkshopName($workShopDetails['workshop_id']) . '</td>
														<td align="center">998596</td>	
														<td align="center">1</td>
														<td align="center"></td>
														<td align="center">' . $invoiceDetails['service_consumed_quantity'] . '</td>
														<td align="right">' . $invoiceDetails['service_basic_price'] . '</td>
													</tr>';
			} else {
				$ref = 'Workshop Registration<br>' . $session . '<br>' . getWorkshopName($workShopDetails['workshop_id']);
			}
		}
		if ($invoiceDetails['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);
			if (false) {
				$cgst 							 = $cfg['ACCOMPANY.CGST'];
				$sgst 							 = $cfg['ACCOMPANY.SGST'];
				$contentBody                    .= '
													<tr>
														<td align="center">1</td>
														<td align="left">Accompanying Person Registration <br />' . $accompanyDetails['user_full_name'] . '</td>
														<td align="center">998596</td>	
														<td align="center">1</td>
														<td align="center"></td>
														<td align="center"></td>
														<td align="right">' . $invoiceDetails['service_basic_price'] . '</td>
													</tr>';
			} else {
				$ref = 'Accompanying Person Registration <br />' . $accompanyDetails['user_full_name'];
			}
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			if (false) {
				$cgst 							 = $cfg['ACCOMMODATION.CGST'];
				$sgst 							 = $cfg['ACCOMMODATION.SGST'];
				$contentBody                    .= '
													<tr>
														<td align="center">1</td>
														<td align="left">Residential Package - ' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>
														<td align="center">998596</td>	
														<td align="center">No. </td>
														<td align="center">1 </td>
														<td colspan="2" align="right">' . $invoiceDetails['service_basic_price'] . '</td>
													</tr>';
			} else {
				$ref = 'Residential Package - ' . getRegClsfName(getUserClassificationId($delegateId));
			}
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id']);
			$dinnerDetails   		= getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id']);
			$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);
			if (false) {
				$cgst 							 = $cfg['DINNER.CGST'];
				$sgst 							 = $cfg['DINNER.SGST'];
				$contentBody                    .= '
													<tr>
														<td align="center">1</td>
														<td align="left">' . $cfg['BANQUET_DINNER_NAME'] . ' Registration<br>' . $dinnerUserDetails['user_full_name'] . '</td>
														<td align="center">998596</td>	
														<td align="center">1</td>
														<td align="center"></td>
														<td align="center"></td>
														<td align="right">' . $invoiceDetails['service_basic_price'] . '</td>
													</tr>';
			} else {
				$ref = 'Banquet Dinner Registration<br>' . $dinnerUserDetails['user_full_name'];
			}
		}
		if ($invoiceDetails['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$sqlaccommodationDetails['QUERY'] 				 = "SELECT accommodation.*,package.package_name 
													  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
												INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
														ON accommodation.package_id = package.id
													 WHERE accommodation.status = 'A' 
													   AND accommodation.user_id = '" . $invoiceDetails['delegateId'] . "'
													   AND accommodation.id = '" . $invoiceDetails['refference_id'] . "'";

			$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
			$rowaccommodation = $resaccommodation[0];
			$accommodationDtls = getAccomodationDetails($invoiceDetails['refference_id'], true);
			if (false) {
				$cgst 							 = $cfg['ACCOMMODATION.CGST'];
				$sgst 							 = $cfg['ACCOMMODATION.SGST'];

				$contentBody                    .= '
													<tr>
														<td colspan="" align="center">1</td>
														<td colspan="" align="left">Accommodation Booking - (' . $rowaccommodation['package_name'] . ')<br />
														' . $accommodationDtls['checkin_date'] . ' to ' . $accommodationDtls['checkout_date'] . '														
														</td>
														<td colspan="" align="center">998596</td>	
														<td colspan="" align="center">1</td>
														<td align="center"></td>
														<td align="center"></td>
														<td align="right">' . $invoiceDetails['service_basic_price'] . '</td>
													</tr>';
			} else {
				$ref = 'Accommodation Booking - (' . $rowaccommodation['package_name'] . ')<br />
											 ' . $accommodationDtls['checkin_date'] . ' to ' . $accommodationDtls['checkout_date'];
			}
		}

		if (false) {
			if (floatval($invoiceDetails['cgst_percentage']) > 0) {
				$contentBody                    .= '	<tr> 
														<td align="center">&nbsp;</td>
														<td colspan="3">CGST </td>
														<td align="right">' . (($invoiceDetails['cgst_percentage'] == "9.00") ? 9 : 14) . '</td>
														<td align="left">%</td>
														<td align="right" colspan="2">' . $invoiceDetails['cgst_price'] . '</td>
													</tr>';
			}
			if (floatval($invoiceDetails['sgst_percentage']) > 0) {
				$contentBody                    .= '	<tr>
														<td align="center">&nbsp;</td>
														<td colspan="3">SGST </td>
														<td align="right">' . (($invoiceDetails['sgst_percentage'] == "9.00") ? 9 : 14) . '</td>
														<td align="left">%</td>
														<td align="right" colspan="2">' . $invoiceDetails['sgst_price'] . '</td>
													</tr>';
			}
		}


		if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
			$cgst 							 = $cfg['INT.CGST'];
			$sgst 							 = $cfg['INT.SGST'];

			$contentBody                    .= '						<tr>
																		<td align="center">2</td>
																		<td>Internet Handling Charges (REF: ' . $invoiceDetails['invoice_number'] . ')</td>
																		<td align="center">998429</td>	
																		<td align="center"></td>
																		<td align="center"></td>
																		<td align="center"></td>
																		<td  align="right" >' . $invoiceDetails['service_basic_int_price'] . '</td>
																	</tr>';
			$contentBody                    .= '						<tr>
																		<td>&nbsp;</td>
																		<td colspan="3">CGST </td>
																		<td align="right">' . $cgst . '</td>
																		<td align="left">%</td>
																		<td align="right" >' . $invoiceDetails['cgst_int_price'] . '</td>
																	</tr>';
			$contentBody                    .= '						<tr>
																		<td>&nbsp;</td>
																		<td colspan="3">SGST</td>
																		<td align="right">' . $sgst . '</td>
																		<td align="left">%</td>
																		<td align="right" >' . $invoiceDetails['sgst_int_price'] . '</td>
																	</tr>';
		}

		$contentBody                    .= '<tr>
											<td colspan="6" align="right">';
		$contentBody                    .=		'<b>Total (Rounded)</b></td>
											<td align="right" ><b>' . number_format(round($invoiceDetails['service_basic_int_price'] + $invoiceDetails['cgst_int_price'] + $invoiceDetails['sgst_int_price'], 0), 2) . '</b></td>
										</tr>
										<tr>
											<td colspan="7">Amount Chargeable (in words)<br /> 
											<b><i>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '
											  ' . convert_number(round($invoiceDetails['service_basic_int_price'] + $invoiceDetails['cgst_int_price'] + $invoiceDetails['sgst_int_price'], 0)) . ' Only.</i></b>
											<!--td align="right">Payment Status : <b>' . $invoiceDetails['payment_status'] . '</b></td-->
											<span style="float:right;">E. & O.E</span>
											</td>
										</tr>
									</tbody>
								</table><br>';

		$contentBody           .= ' <table width="100%" style="font-size:13px;argin-top: 2%;" border="1" >
										<tbody>
											<tr>
												<td width="40%">HSN/SAC</td>
												<td align="center">Taxable Value</td>
												<td align="right">CGST Rate</td>
												<td align="right">Amount</td>
												<td align="right">SGST Rate</td>
												<td align="right">Amount</td>
												<td align="center">Total Tax Amount</td>
											</tr>
											
											<tr>
												<td>998429</td>
												<td align="right">' . $invoiceDetails['service_basic_int_price'] . '</td>
												<td align="right">' . (($invoiceDetails['cgst_percentage'] == "9.00") ? 9 : 14) . '%</td>
												<td align="right">' . number_format(round($invoiceDetails['cgst_int_price'], 0), 2) . '</td>
												<td align="right">' . (($invoiceDetails['sgst_percentage'] == "9.00") ? 9 : 14) . '%</td>
												<td align="right">' . number_format(round($invoiceDetails['sgst_int_price'], 0), 2) . '</td>
												<td align="right">' . number_format(round($invoiceDetails['cgst_int_price'] + $invoiceDetails['sgst_int_price'], 0), 2) . '</td>
											</tr>
											<tr>
												<td>Total</td>
												<td align="right">' . $invoiceDetails['service_basic_int_price'] . '</td>
												<td align="right"></td>
												<td align="right">' . number_format(round($invoiceDetails['cgst_int_price'], 0), 2) . '</td>
												<td align="right"></td>
												<td align="right">' . number_format(round($invoiceDetails['sgst_int_price'], 0), 2) . '</td>
												<td align="right">' . number_format(round($invoiceDetails['cgst_int_price'] + $invoiceDetails['sgst_int_price'], 0), 2) . '</td>
											</tr>
											<tr>
												<td>Tax Amount (in words)  :</td>
												<td colspan="6" align="right"><strong>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . convert_number(round($invoiceDetails['cgst_int_price'] + $invoiceDetails['sgst_int_price'], 0), 2) . ' Only.</strong></td>														
											</tr>
										</tbody>
									</table>';

		$contentBody              .= ' <br />
								<table width="100%" style="font-size:13px;argin-top: 2%;" >	
									<tr>
										<td valign="top" width="20%" align="right">' . addslashes("Company's") . ' PAN :</td>
										<td valign="top" width="20%" align="left"><b>' . $cfg['PAN_RUEDA'] . '</b></td>
										<td align="center" rowspan="3" style="border:1px solid black;"><br /><br /><br />Authorised Signatory</td>												
									</tr>
									<tr>
										<td colspan="2"></td>												
									</tr>
									<tr>
										<td colspan="2"></td>												
									</tr>
									<tr>
										<td align="right" colspan="4">This is a Computer Generated Invoice</td>												
									</tr>
								</table>';
	} else {
		$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">												
												<tr>
													<td align="center" style="border-collapse:collapse;">
													<img src="' . _BASE_URL_ . 'images/header20191011.jpg" width="790px" />
													</td>
												</tr>
												<tr>
													<td align="center" height="360px" style="border-collapse:collapse;" valign="top">
														<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
														INVOICE/RECEIPT
														</div>
														<table width="100%" cellpadding="1" style="font-size:13px;">
															<tr>
																<td width="18%"></td>
																<td width="32%"></td>
																<td width="18%"></td>
																<td width="32%"></td>
															</tr>
															<tr>
																<td>Name:</td>
																<td>' . $delegateDetails['user_full_name'] . '</td>
																<td width="18%">Date:</td>
																<td width="32%">' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</td>
															</tr>
															<tr>
																<td>E-mail id:</td>
																<td>' . $delegateDetails['user_email_id'] . '</td>
																<td>Invoice No:</td>
																<td>' . $invoiceDetails['invoice_number'] . '</td>
															</tr>
															<tr>
																<td>Mobile:</td>
																<td>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</td>
																<td>Registration Id:</td>
																<td>' . $user_registration_id . '</td>
																</td>
															</tr>
															<tr>
																<td>PV No:</td>
																<td>' . $invoiceDetails['slipNO'] . '</td>
																<td></td>
																<td></td>
															</tr> 
														</table>';


		if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {

			$contentBody                				.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
														 Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Conference - ' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . ($invoiceDetails['service_unit_price'] == 0.00 ? 'Complimentary' : $invoiceDetails['service_unit_price']) . '</td>
															</tr>';

			if ($invoiceDetails['invoice_mode'] == 'ONLINE' && $invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															 </tr>';
			}

			if ($invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>';
			}
			$contentBody                    			.= '</table>';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
			$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							 Registration Details
							</div>
							<table width="100%" style="font-size:13px;">
								<tr>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
									<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
									<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
								</tr>
								
								
								<tr>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">Residential Registration - ' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
									<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['service_unit_price'] . '</td>
								</tr>';
			if ($invoiceDetails['invoice_mode'] == 'ONLINE') {

				$contentBody       .= '<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
								</tr>';
			}
			$contentBody                 .= '<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
									<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['service_roundoff_price'] . '</td>
								</tr>
								
								<tr>
									<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
								</tr>							
								
								
							</table>';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id']);
			$workshop_id                     = $workShopDetails['workshop_id'];


			$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
														 Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>WORKSHOP</b><br>' . getWorkshopName($workShopDetails['workshop_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($workShopDetails['tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['service_unit_price'] . '</td>
															</tr>';
			if ($invoiceDetails['invoice_mode'] == 'ONLINE') {
				$contentBody       						.=	   '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    		.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															</tr>';
			}
			$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

			$contentBody                    			.=     '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>
														</table>';
		}
		if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);

			$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
															Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Accompanying Person</b><br>' . $accompanyDetails['user_full_name'] . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . ($invoiceDetails['service_unit_price'] == 0.00 ? 'Complimentary' : $invoiceDetails['service_unit_price']) . '</td>
															</tr>';

			if ($invoiceDetails['invoice_mode'] == 'ONLINE' && $invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															</tr>';
			}
			$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

			$contentBody                    			.=     '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>
														</table>';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
			$tourDetails  = getTourDetails($invoiceDetails['refference_id']);

			$contentBody    .= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
									Tour Booking Details
								</div>
								<table width="100%" style="font-size:13px;">
									<tr>
										<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
										<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
										<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
										<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Details</td>
										<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
									</tr>
									<tr>
										<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="2">' . getTourName($tourDetails['package_id']) . '</td>
										<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">' . getCutoffName($tourDetails['tariff_cutoff_id']) . '</td>
										<td align="center" valign="middle" style="border:thin solid #000;" rowspan="2">' . $invoiceDetails['service_consumed_quantity'] . '</td>
										<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
										<td align="right" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_unit_price'] . '</td>
									</tr>
									<tr>
										<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
										<td align="right" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['internet_handling_amount'] . '</td>
									</tr>
									<tr>
										<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
										<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
										<td align="right" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_roundoff_price'] . '</td>
									</tr>
									<tr>
										<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($invoiceDetails['service_roundoff_price']) . ' Only.</u></td>
									</tr>
								</table>';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$dinnerDetails  		= getDinnerDetails($invoiceDetails['refference_id']);
			$dinnerDetails   		=  getUserDetailsByDinnerRefferenceId($invoiceDetails['refference_id']);
			$dinnerUserDetails     	= getUserDetails($dinnerDetails['refference_id']);

			$contentBody    							.= '<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
															Registration Details
														</div>
														<table width="100%" style="font-size:13px;">
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particulars</td>
																<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
																<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
															</tr>
															<tr>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;"><b>Gala Dinner</b><br>' . $dinnerUserDetails['user_full_name'] . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . getCutoffName($delegateDetails['registration_tariff_cutoff_id']) . '</td>
																<td height="24" align="center" valign="middle" style="border:thin solid #000;">' . $invoiceDetails['service_consumed_quantity'] . '</td>
																<td width="30%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . ($invoiceDetails['service_unit_price'] == 0.00 ? 'Complimentary' : $invoiceDetails['service_unit_price']) . '</td>
															</tr>';
			if ($invoiceDetails['invoice_mode'] == 'ONLINE' && $invoiceDetails['service_roundoff_price'] != 0.00) {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . $invoiceDetails['internet_handling_amount'] . '</td>
															</tr>';
			}

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE' && $discountAmount != "") {
				$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">(Less) Discount Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($discountAmount, 2) . '</td>
															</tr>';
			}

			$contentBody                    			.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total Amount</td>
																<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">' . number_format($totalAmount, 2) . '</td>
															</tr>';

			$contentBody                   				.=      '<tr>
																<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="5">In Words: <u>' . convert_number($totalAmount) . ' Only.</u></td>
															</tr>
														</table>';
		}

		$contentBody    								.= '<table width="100%" style="font-size:13px;">
															<tr>
																<td><br /><br /></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr><td style="font-size: x-small;background-color: #EEEAE9;">' . getNoticeDetails("Cancellation & Refund Policy") . '</td></tr>';
		// $contentBody .=								'<tr>
		// 											<td align="center" valign="bottom" style="border-collapse:collapse;">
		// 											<img src="' . _BASE_URL_ . 'images/footer20191011.jpg" width="790px"/>
		// 											</td>
		// 										</tr>';
		$contentBody    	= '</table>
							</div>';
	}

	return $contentBody;
}

function downloadSlipDetailsContent($delegateId, $slipId)
{
	global $cfg, $mycms;
	$delegateId;
	$slipId;
	$slipDetails  	 = slipDetails($slipId);
	$delegateDetails = getUserDetails($delegateId);
	$contentBody = "";

	$contentBody    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;" operationmode="SlipPrint">
		
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								
								<tr>
									<td align="center" style="border-collapse:collapse;">
									<img src="' . _BASE_URL_ . 'images/header20191011.jpg" width="100%"/>
									</td>
								</tr>
								<tr>
									<td align="center" height="450px" style="border-collapse:collapse; padding:2px;" valign="top">
										
										<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
										Payment Voucher Details											
										</div>';

	$contentBody    .= '<table width="100%" cellpadding="1" style="font-size:13px;">
											<tr>
												<td width="18%" align="left"><strong><u>Billed to</u></strong></td>
												<td width="32%"></td>
												<td width="18%"></td>
												<td width="32%"></td>
											</tr>
											<tr>
												<td>Name:</td>
												<td>' . $delegateDetails['user_full_name'] . '</td>
												<td width="18%">Date:</td>
												<td width="32%">' . date("d/m/Y", strtotime($slipDetails['slip_date'])) . '</td>
											</tr>
											<tr>
												<td>E-mail id:</td>
												<td>' . $delegateDetails['user_email_id'] . '</td>
												<td>PV No:</td>
												<td>' . $slipDetails['slip_number'] . '</td>
											</tr>
											<tr>
												<td>Mobile:</td>
												<td>' . $delegateDetails['user_mobile_isd_code'] . ' - ' . $delegateDetails['user_mobile_no'] . '</td>
												<td></td>
												<td></td>
											</tr>
										</table>';

	$contentBody     .= 	'Payment Summary
								</div>
								<table width="100%" style="font-size:13px;">
									<tr>
										<td align="center" valign="middle" height="25" style="border:thin solid #00adde; font-weight:bold;" width="20%">Invoice Number</td>
										<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;">Invoice For</td>
										<td width="16%" align="right" valign="middle" style="border:thin solid #00adde; font-weight:bold;  padding-right: 15px;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
									</tr>';
	$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
	$counter = 0;
	foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
		$show = true;
		if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id'], true);
			if ($workShopDetails['display'] == 'N') {
				if ($invoiceDetails['remarks'] == 'Adjusted Workshop') {
					$show = false;
				}
			}
		}

		if ($show) {
			$counter 		 = $counter + 1;
			$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
			$type			 = "";
			if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
			}
			if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
				$tourDetails = getTourDetails($invoiceDetails['refference_id']);

				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
			}
			if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
			}

			$contentBody    .= 	'<tr>
									<td style="border:thin solid #00adde;" align="center"  valign="top" >' . $invoiceDetails['invoice_number'] . '</td>
									<td style="border:thin solid #00adde; padding-left: 15px;" align="left" valign="top">' . $type . '</td>
									<td style="border:thin solid #00adde; padding-right: 15px;" align="right" valign="top">
										' . $invoiceDetails['service_roundoff_price'] . '
									</td>
								</tr>';
		}
	}
	$contentBody    .= '<tr>
									<td align="left" style="border:thin solid #00adde;" valign="middle" colspan="2" height="30"><strong style="float:right; padding-right:15px;">Total Amount</strong> <span style="color:#FF0000; float:left; padding-left:15px;">** Including Internet Handling Charges.</span></td>
									<td style="text-align:right; padding-right:15px; border:thin solid #00adde;" valign="top">
										' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' 
										' . invoiceAmountOfSlip($slipId) . '
									</td>
								</tr>
									</table>';

	$resPaymentDetails      = paymentDetails($slipId);

	$paymentDescription     = "-";
	if ($resPaymentDetails['payment_mode'] == "Online") {
		$payDate = setDateTimeFormat2($resPaymentDetails['payment_date'], "D");
		$paymentDescription = " Transaction Number: <b>" . $resPaymentDetails['atom_atom_transaction_id'] . "</b>.<br>
											Bank Transaction Number: <b>" . $resPaymentDetails['atom_bank_transaction_id'] . "</b>.";
	}
	if ($resPaymentDetails['payment_mode'] == "Cash") {
		$payDate = setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D");
		$paymentDescription = "--";
	}
	if ($resPaymentDetails['payment_mode'] == "Card") {
		$payDate = setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D");
		$paymentDescription = "Reference Number: <b>" . $resPaymentDetails['card_refference_no'] . "</b>.";
	}
	if ($resPaymentDetails['payment_mode'] == "Draft") {
		$payDate = setDateTimeFormat2($rowFetchSlip['draft_date'], "D");
		$paymentDescription = "Draft Number: <b>" . $rowFetchSlip['draft_number'] . "</b>.<br>
										   Draft Date: <b>" . setDateTimeFormat2($rowFetchSlip['draft_date'], "D") . "</b>.
										   Draft Drawee Bank: <b>" . $rowFetchSlip['draft_bank_name'] . "</b>.";
	}
	if ($resPaymentDetails['payment_mode'] == "NEFT") {
		$payDate = setDateTimeFormat2($resPaymentDetails['neft_date'], "D");
		$paymentDescription = "Transaction Number: <b>" . $resPaymentDetails['neft_transaction_no'] . "</b>.<br>
										   Transaction Date: <b>" . setDateTimeFormat2($resPaymentDetails['neft_date'], "D") . "</b>.
										   Transaction Bank: <b>" . $resPaymentDetails['neft_bank_name'] . "</b>.";
	}
	if ($resPaymentDetails['payment_mode'] == "RTGS") {
		$payDate = setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D");
		$paymentDescription = "Transaction Number: <b>" . $resPaymentDetails['rtgs_transaction_no'] . "</b>.<br>
										   Transaction Date: <b>" . setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D") . "</b>.
										   Transaction Bank: <b>" . $resPaymentDetails['rtgs_bank_name'] . "</b>.";
	}
	if ($resPaymentDetails['payment_mode'] == "Cheque") {
		$payDate = setDateTimeFormat2($resPaymentDetails['cheque_date'], "D");
		$paymentDescription = "Cheque/DD Number: <b>" . $resPaymentDetails['cheque_number'] . "</b>.<br>
										   Date: <b>" . setDateTimeFormat2($resPaymentDetails['cheque_date'], "D") . "</b>.
										   Drawee Bank: <b>" . $resPaymentDetails['cheque_bank_name'] . "</b>.";
	}
	$paymentModeDisplay = $resPaymentDetails['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($resPaymentDetails['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $resPaymentDetails['payment_mode']);





	$contentBody    .= 	'<div style="color:#00adde; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
						Transaction Summary
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td align="center" valign="middle" height="25" style="border:thin solid #0000adde0; font-weight:bold;" width="20%"> Payment Voucher No.</td>
								<td width="16%" align="right" valign="middle" style="border:thin solid #00adde; font-weight:bold;  padding-right: 15px;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
								<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;" width="16%">Payment Mode</td>
								<td align="center" valign="middle" style="border:thin solid #000; font-weight:bold;">Payment Date</td>
								<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;">Transaction Details</td>
							</tr>
							
							<tr>
								<td style="border:thin solid #00adde;" align="center" valign="top" >' . $slipDetails['slip_number'] . '</td>									
								<td style="border:thin solid #00adde; padding-right: 15px;" align="right" valign="top">
									' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '' . invoiceAmountOfSlip($slipId) . '										
								</td>
								<td style="border:thin solid #00adde;" align="center"  valign="top" >' . $paymentModeDisplay . '</td>
								<td style="border:thin solid #00adde;" align="center" valign="top">' . $payDate . '</td>
								<td style="border:thin solid #00adde;" align="center"  valign="top" >' . $paymentDescription . '</td>
							</tr>
						</table>';


	$contentBody    .= '</td>
				</tr>
				<tr><td style="font-size: x-small;background-color: #EEEAE9;">' . getNoticeDetails("Cancellation & Refund Policy") . '</td></tr>
				<tr>
					<td align="center" valign="bottom" style="border-collapse:collapse;">
					<img src="' . _BASE_URL_ . 'images/footer20191011.jpg" width="100%" />
					</td>
				</tr>
					
			</table>
		</div>';
	return $contentBody;
}

function complementarygetPrintSlipDetailsContent($delegateId, $slipId, $showTransaction = false, $showHeaderFooter = true)
{
	global $cfg, $mycms;
	$delegateId;
	$slipId;
	$slipDetails  	 = slipDetails($slipId);
	$delegateDetails = getUserDetails($delegateId);

	$message .= '<div style="width: 100%;max-width: 800px;margin: auto; height: 27cm; font-family: sans-serif;">';
	$message .= '	<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">';
	// if ($showHeaderFooter) {
	// 	$message .= '		<tr>';
	// 	$message .= '			<td align="center" style="border-collapse:collapse;">';
	// 	$message .= '			<img src="' . _BASE_URL_ . 'images/header20191011.jpg" width="100%"/>';
	// 	$message .= '			</td>';
	// 	$message .= '		</tr>';
	// }
	$message .= '<tbody>
                	<tr>
                   		<td colspan="2" style="padding:20px 0 20px 30px; color: #114C5C;">
                        	<p style="display: flex; color: #114C5C; font-weight: bold;
                        	align-items: center;
                        	justify-content: space-between; margin: 0;">
 							<span> &nbsp;</span>
 							<span >Date: ' . date("d/m/Y", strtotime($invoiceDetails['invoice_date'])) . '</span>
                        	</p>
                    	</td>
               	    </tr><tr>';
	$message .= '			<td align="center" height="' . (($showHeaderFooter) ? '780px' : 'auto') . '" style="border-collapse:collapse; padding:2px;" valign="top">';
	if ($showHeaderFooter) {
		$message .= '				<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">';
		$message .= '				Payment Voucher';
		$message .= '				</div>';
	}
	$message .= '				<table width="100%" cellpadding="1" style="font-size:15px;">';
	$message .= '					<tr>';
	$message .= '						<td width="18%" align="left"><u><b>Billed to</b></u></td>';
	$message .= '						<td width="32%"></td>';
	$message .= '						<td width="18%"></td>';
	$message .= '						<td width="32%"></td>';
	$message .= '					</tr>';
	$message .= '					<tr>';
	$message .= '						<td align="left">Name:</td>';
	$message .= '						<td align="left">' . $delegateDetails['user_full_name'] . '</td>';
	$message .= '						<td align="left" width="18%">Date:</td>';
	$message .= '						<td align="left" width="32%">' . date("d/m/Y", strtotime($slipDetails['slip_date'])) . '</td>';
	$message .= '					</tr>';
	$message .= '					<tr>';
	$message .= '						<td align="left">E-mail id:</td>';
	$message .= '						<td align="left">' . $delegateDetails['user_email_id'] . '</td>';
	$message .= '						<td align="left">PV No.</td>';
	$message .= '						<td align="left">' . $slipDetails['slip_number'] . '</td>';
	$message .= '					</tr>';
	$message .= '					<tr>';
	$message .= '						<td align="left">Mobile:</td>';
	$message .= '						<td align="left">' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</td>';
	$message .= '						<td align="left"></td>';
	$message .= '						<td align="left"></td>';
	$message .= '					</tr>';
	$message .= '				</table>';
	$message .= '				<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">';
	$message .= '				Order Summary';
	$message .= '				</div>';
	$message .= '				<table width="100%" style="font-size:13px;">';
	$message .= '					<tr>';
	$message .= '						<td align="center" valign="middle" height="25" style="border:thin solid #000; font-weight:bold;" width="20%">Invoice No.</td>';
	$message .= '						<td align="center" valign="middle" style="border:thin solid #000; font-weight:bold; text-align:left;">&nbsp;&nbsp;Invoice for</td>';
	$message .= '						<td width="16%" align="center" valign="middle" style="border:thin solid #000; font-weight:bold;  padding-right: 15px;">Amount </td>';
	$message .= '					</tr>';


	$contentBody                    .= '<div style="width: 100%;max-width: 800px;margin: auto; height: 27cm; font-family: sans-serif;">
        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
            <tbody>
                <tr>
                    <td colspan="2" style="padding:20px 0 20px 30px; color: #114C5C;">
                        <p style="display: flex; color: #114C5C; font-weight: bold;
                        align-items: center;
                        justify-content: space-between; margin: 0;">
 <span> &nbsp;</span>
 <span >Date: ' . date("d/m/Y", strtotime($slipDetails['slip_date'])) . '</span>
                        </p>
                       
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-left: 30px;">
                        <p
                            style="background: #BADEC4;padding: 25px;margin: 0;font-size: 35px;font-weight: 600;letter-spacing: .8px;color: #114c5c;display: flex;justify-content: space-between;">
                            Payment Voucher
							<span style="font-size:20px;padding: 10px 0px;">' . $delegateDetails['user_full_name'] . '</span></p>		
                    </td>
                </tr>
                <tr>
                    <td
                        style="background: #114C5C;width: 30%;padding: 45px 15px;text-align: right;color: white;height: 19cm;vertical-align: top;position: relative;">
                        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p
                                            style="margin: 0;font-size: 24px;letter-spacing: .8px;font-weight: 600; margin-bottom: 13px;">
                                            <img style="width: 100%;     filter: brightness(36.5);"
                                                src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '"
                                                alt="logo">
                                        </p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;color: white;">
                                            ' . $cfg['INVOICE_ADDRESS'] . '
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            EMAIL</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_EMAIL'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            CONTACT</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_CONTACT'] . '
                                        </p>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            Code</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                           ' . $cfg['INVOICE_STATE_CODE'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            CONTACT</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_CONTACT'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            EMAIL</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_EMAIL'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            WEBSITE</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                             ' . $cfg['INVOICE_WEBSITE'] . '
                                        </p>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            Payment Status</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;color: white;">
                                            ' . $invoiceDetails['payment_status'] . '
                                        </p>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                        <!--<table style="position: absolute;bottom: 17px;left: 0;margin: 22px;">
                            <tbody>
                                <tr></tr>
                                <tr>
                                    <td>
                                        <p
                                            style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600; color: white;border-bottom: 1px solid; padding-bottom: 6px;">
                                            Bank Details</p>
                                        <p
                                            style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;margin-top: 0px; line-height: 21px; padding-top: 7px; color: white;">
                                            <b>' . $cfg['INVOICE_BANKNAME'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BENEFECIARY'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKACNO'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKBRANCH'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKIFSC'] . '</b>
                                        </p>

                                    </td>
                                </tr>
                            </tbody>
                        </table>-->
                    </td>
                    <td style="width: 70%;padding: 0 30px;vertical-align: text-bottom;background: #BADEC4;">
                        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="text-align: right;font-size: 18px;padding-bottom: 25px;font-weight: 600;color: #114C5C;">
                                        
                                        <p style="margin-top: 2px;font-size: 14px;line-height: 24px;">Reg. Id:
                                            ' . $delegateDetails['user_registration_id'] . '<br>
                                            Email Id: ' . $delegateDetails['user_email_id'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table cellspacing="0" cellpadding="0" border="0"
                                            style="width: 100%; color: #114C5C;">
                                            <tbody>
                                                <tr>
                                                    <td
                                                        style="width: 5%; text-transform: uppercase; font-weight: bold ; text-align: center; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                       Invoice No.</td>
                                                    <td
                                                        style="width: 30%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Invoice for</td>
                                                    <td
                                                        style="width: 15%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Amount
                                                    </td>
                                                    
                                                   <!-- <td
                                                        style="width: 25%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: right;">
                                                        Amount (' . getInvoiceCurrencyById($invoiceId) . ')</td>-->
                                                </tr>';


	$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
	$counter = 0;
	foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
		$counter 		 = $counter + 1;
		$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
		$type			 = "";
		if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
		}
		if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
			$tourDetails = getTourDetails($invoiceDetails['refference_id']);

			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
		}

		$message .= '						<tr>';
		$message .= '							<td style="border:thin solid #000;" align="left"  valign="top" >' . $invoiceDetails['invoice_number'] . '</td>';
		$message .= '							<td style="border:thin solid #000; padding-left: 15px; text-align:left;" align="left" valign="top">' . $type . '</td>';
		$message .= '							<td style="border:thin solid #000; padding-right: 15px; text-align:right;" align="right" valign="top">';
		$message .= '								Complimentary';
		$message .= '							</td>';
		$message .= '						</tr>';

		$contentBody                    .= '<tr>
													<td style="text-align: center; font-size: 13px; padding:10px 5px;">' . $invoiceDetails['invoice_number'] . '
														</td>
													<td style="font-size: 13px; padding:10px 5px;">' . $type . '
														</td>
													
													<td style="text-align: center; font-size: 13px; padding:10px 5px;">
														' . $invoice_price . '
													</td>

													<td style="text-align: right; font-size: 13px; padding:10px 5px;">
														' . $invoice_price . '</td>
												</tr>';
	}

	$message .= '				</table>';

	if ($showTransaction) {
		$resPaymentDetails      = paymentDetails($slipId);

		$paymentDescription     = "-";
		if ($resPaymentDetails['payment_mode'] == "Online") {
			$payDate = setDateTimeFormat2($resPaymentDetails['payment_date'], "D");
			$paymentDescription = " Transaction No. <b>" . $resPaymentDetails['atom_atom_transaction_id'] . "</b><br>
															Bank Transaction No. <b>" . $resPaymentDetails['atom_bank_transaction_id'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "Cash") {
			$payDate = setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D");
			$paymentDescription = "--";
		}
		if ($resPaymentDetails['payment_mode'] == "Card") {
			$payDate = setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D");
			$paymentDescription = "Reference No. <b>" . $resPaymentDetails['card_refference_no'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "Draft") {
			$payDate = setDateTimeFormat2($rowFetchSlip['draft_date'], "D");
			$paymentDescription = "Draft No. <b>" . $rowFetchSlip['draft_number'] . "</b><br>
														   Draft Date: <b>" . setDateTimeFormat2($rowFetchSlip['draft_date'], "D") . "</b>
														   Draft Drawn Bank: <b>" . $rowFetchSlip['draft_bank_name'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "NEFT") {
			$payDate = setDateTimeFormat2($resPaymentDetails['neft_date'], "D");
			$paymentDescription = "Transaction No. <b>" . $resPaymentDetails['neft_transaction_no'] . "</b><br>
														   Transaction Date: <b>" . setDateTimeFormat2($resPaymentDetails['neft_date'], "D") . "</b>
														   Transaction Bank: <b>" . $resPaymentDetails['neft_bank_name'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "RTGS") {
			$payDate = setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D");
			$paymentDescription = "Transaction No. <b>" . $resPaymentDetails['rtgs_transaction_no'] . "</b><br>
														   Transaction Date: <b>" . setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D") . "</b>
														   Transaction Bank: <b>" . $resPaymentDetails['rtgs_bank_name'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "Cheque") {
			$payDate = setDateTimeFormat2($resPaymentDetails['cheque_date'], "D");
			$paymentDescription = "Cheque/DD No. <b>" . $resPaymentDetails['cheque_number'] . "</b><br>
														    Date: <b>" . setDateTimeFormat2($resPaymentDetails['cheque_date'], "D") . "</b>
														    Drawee Bank: <b>" . $resPaymentDetails['cheque_bank_name'] . "</b>";
		}
	}
	$message .= '			</td>';
	$message .= '		</tr>';
	if ($showHeaderFooter) {

		$message .= '		<tr>';
		$message .= '			<td align="center" valign="bottom" style="border-collapse:collapse;">';
		$message .= '			<img src="' . _BASE_URL_ . 'images/footer20191011.jpg" width="100%" />';
		$message .= '			</td>';
		$message .= '		</tr>';
	}
	$message .= '	</table>';
	$message .= '</div>';

	$contentBody                    .= '      <tr>
												<td colspan="2"
													style="font-size: 14px; padding:10px 5px; border-bottom: 2px solid #114C5C; font-weight: bold;">
													GST TOTAL</td>
												<td colspan="4"
													style="font-size: 14px; padding:10px 5px; border-bottom: 2px solid #114C5C; font-weight: bold; text-align: right;">
													TOTAL PAYABLE</td>
											</tr>
											<tr>
												<td colspan="2"
													style="padding:10px 5px; border-bottom: 2px solid #114C5C; font-size: 20px; font-weight: bold;">
													' . number_format($overallTotalGST, 2) . '</td>
												<td colspan="4"
													style="padding:10px 5px; border-bottom: 2px solid #114C5C; font-size: 20px; text-align: right; font-weight: bold;">
													' . number_format($invoiceDetails['service_roundoff_price'], 2) . '</td>
											</tr>
											<tr>
												<td colspan="6"
													style="border-bottom: 2px solid #114C5C; padding:10px 5px;">
													<p
														style="margin: 0;font-size: 12px;letter-spacing: .8px;margin-top: 0px;">
														Amount Chargeable (in Words):
													</p>
													<p
														style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;">
													<b> ' . convert_number($invoiceDetails['service_roundoff_price']) . ' Only</b>
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>

						</tbody>
					</table>
					</td>
					</tr>
					
					<tr>
						<td colspan="2" style="text-align: right;padding-left: 30px;" >
							<p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;padding-right: 30px;padding-bottom: 10px;background: #BADEC4;">
								<img src="' . $invoice_sign_img . '" style="width:15%">
							</p>
						</td>
					</tr>
					<!-- <tr>
                    	<td colspan="2" style="text-align: right;padding-left: 30px;color: #114C5C;">
                       	 <p
                            style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;padding-right: 30px;height: 20px;padding-bottom: 5px;background: #BADEC4;">
                            </p>
                   		 </td>
                	</tr> -->
                <tr>
                    <td colspan="2"
                        style="text-align: right;padding-left: 30px;color: #114C5C;/* padding-bottom: 30px; */">
                        <p
                            style="margin: 0;font-size: 16px;letter-spacing: .8px;font-weight: 600;padding-right: 30px;background: #BADEC4;padding-bottom: 30px;">
                            Name: ' . $authorized_person_arr[0]    . '<br>' . ($authorized_person_arr[1] == '' ? '' : 'Designation: ' . $authorized_person_arr[1] . '<br>') . '
                          
                        </p>
                    </td>
                </tr>
					<tr>
						<td colspan="2">
							<p style="display: flex;color: #114C5C;font-weight: bold;align-items: center;padding: 11px 30px;justify-content: space-evenly;border-bottom: 1px solid;margin: 0;margin-left: 30px;"><span>' . $cfg['INVOICE_EMAIL'] . '</span><span>|</span><span>' . $cfg['INVOICE_CONTACT'] . '</span><span>|</span><span>' . $cfg['INVOICE_WEBSITE'] . '</span></p>
								</td>
					</tr>
					<tr>
                    <td colspan="2">
                        <p style="display: flex;color: #000000;font-weight: bold;align-items: center;padding: 11px 30px;justify-content: space-evenly;margin: 0;margin-left: 30px;font-size: 14px;
                        ">' . $cfg['INVOICE_FOOTER_TEXT'] . '</p>
                    </td>
                </tr>
				</tbody>
			</table>
		</div>';

	return $contentBody;
}

function getPrintSlipDetailsContent($delegateId, $slipId, $showTransaction = false, $showHeaderFooter = true, $showPendingPayments = false)
{

	global $cfg, $mycms;
	include_once('includes/function.workshop.php');
	include_once('includes/function.delegate.php');
	include_once('includes/function.dinner.php');
	$delegateId;
	$slipId;
	$slipDetails  	 				= slipDetails($slipId);
	$delegateDetails 				= getUserDetails($delegateId);
	$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
	$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
	$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row    		 = $result[0];

	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
	if ($row['header_image'] != '') {
		$emailHeader  = $header_image;
	}

	$message .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#00adde;" operationmode="SlipPrint">';
	$message .= '	<table width="100%" border="0" cellpadding="0" cellspacing="0">';
	if ($showHeaderFooter && $emailHeader) {
		$message .= '	<tr>';
		$message .= '		<td align="center" style="border-collapse:collapse;">';
		//$message .='		<img src="'._BASE_URL_.'images/pvHeader.jpg" width="100%"/>';
		$message .= '		<img src="' . $emailHeader . '" width="100%"/>';
		$message .= '		</td>';
		$message .= '	</tr>';
	}

	$message .= '		<tr>';
	$message .= '			<td align="center" height="' . (($showHeaderFooter) ? '750px' : 'auto') . '" style="border-collapse:collapse; padding:20px;background-color: #fbfbf9;border-radius:5px;" valign="top">';
	if ($showHeaderFooter) {
		$message .= '			<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">';
		$message .= '			Payment Voucher';
		$message .= '			</div>';
	}

	$message .= '				<table width="100%" cellpadding="1" style="font-size:15px;color:black;">';
	$message .= '					<tr>';
	$message .= '						<td width="18%" align="left"><h5 style="color:black;">Billed to</h5></td>';
	$message .= '						<td width="32%"></td>';
	$message .= '						<td width="18%"></td>';
	$message .= '						<td width="32%"></td>';
	$message .= '					</tr>';
	$message .= '					<tr>';
	$message .= '						<td align="left">Name:</td>';
	$message .= '						<td align="left">' . $delegateDetails['user_full_name'] . '</td>';
	$message .= '						<td align="left" width="18%">Date:</td>';
	$message .= '						<td align="left" width="32%">' . date("d/m/Y", strtotime($slipDetails['slip_date'])) . '</td>';
	$message .= '					</tr>';
	$message .= '					<tr>';
	$message .= '						<td align="left">E-mail id:</td>';
	$message .= '						<td align="left">' . $delegateDetails['user_email_id'] . '</td>';
	$message .= '						<td align="left">PV No.</td>';
	$message .= '						<td align="left">' . $slipDetails['slip_number'] . '</td>';
	$message .= '					</tr>';
	$message .= '					<tr>';
	$message .= '						<td align="left">Mobile:</td>';
	$message .= '						<td align="left">' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</td>';
	$message .= '						<td align="left">Payment Status:</td>';
	$message .= '						<td align="left">' . (($slipDetails['payment_status'] == 'UNPAID') ? 'Pending' : (($slipDetails['payment_status'] == 'PAID') ? 'Paid' : (($slipDetails['payment_status'] == 'COMPLIMENTARY') ? 'Complimentary' : 'Zero Value'))) . '</td>';
	$message .= '					</tr>';
	$message .= '				</table>';

	$message .= '				<h5 style="color:black; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">';
	$message .= '				Order Summary';
	$message .= '				</h5>';
	$message .= '				<table width="100%" style="font-size:13px;color:black;">';
	$message .= '					<tr>';
	$message .= '						<td align="center" valign="middle" height="25" style="border:thin solid #00adde; font-weight:bold;" width="20%">Invoice No.</td>';
	$message .= '						<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold; text-align:left;">&nbsp;&nbsp;Invoice for</td>';
	$message .= '						<td width="16%" align="right" valign="middle" style="border:thin solid #00adde; font-weight:bold;  padding-right: 15px;">Amount (' . getInvoiceCurrency($slipId) . ')</td>';
	$message .= '					</tr>';
	$contentBody                    .= '<div style="width: 100%;max-width: 800px;margin: auto; height: 27cm; font-family: sans-serif;">
        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
            <tbody>
                <tr>
                    <td colspan="2" style="padding:20px 0 20px 30px; color: #114C5C;">
                        <p style="display: flex; color: #114C5C; font-weight: bold;
                        align-items: center;
                        justify-content: space-between; margin: 0;">
 <span> &nbsp;</span>
 <span >Date: ' . date("d/m/Y", strtotime($slipDetails['slip_date'])) . '</span>
                        </p>
                       
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-left: 30px;">
                        <p
                            style="background: #BADEC4;padding: 25px;margin: 0;font-size: 35px;font-weight: 600;letter-spacing: .8px;color: #114c5c;display: flex;justify-content: space-between;">
                            Payment Voucher
							<span style="font-size:20px;padding: 10px 0px;">' . $delegateDetails['user_full_name'] . '</span></p>		
                    </td>
                </tr>
                <tr>
                    <td
                        style="background: #114C5C;width: 30%;padding: 45px 15px;text-align: right;color: white;height: 19cm;vertical-align: top;position: relative;">
                        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p
                                            style="margin: 0;font-size: 24px;letter-spacing: .8px;font-weight: 600; margin-bottom: 13px;">
                                            <img style="width: 100%;     filter: brightness(36.5);"
                                                src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '"
                                                alt="logo">
                                        </p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;color: white;">
                                            ' . $cfg['INVOICE_ADDRESS'] . '
                                        </p>
                                    </td>
                                </tr>

                                 <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            EMAIL</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_EMAIL'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            CONTACT</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_CONTACT'] . '
                                        </p>
                                    </td>
                                </tr>
                               
                                <!--<tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            Code</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                           ' . $cfg['INVOICE_STATE_CODE'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            CONTACT</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_CONTACT'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            EMAIL</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                            ' . $cfg['INVOICE_EMAIL'] . '
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            WEBSITE</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 8px;color: white;">
                                             ' . $cfg['INVOICE_WEBSITE'] . '
                                        </p>
                                    </td>
                                </tr> 
                                <tr>
                                    <td style="padding: 10px;">
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600;color: white;">
                                            Payment Status</p>
                                        <p style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;color: white;">
                                            ' . $invoiceDetails['payment_status'] . '
                                        </p>
                                    </td>
                                </tr>-->

                            </tbody>

                        </table>
                        <!--<table style="position: absolute;bottom: 17px;left: 0;margin: 22px;">
                            <tbody>
                                <tr></tr>
                                <tr>
                                    <td>
                                        <p
                                            style="margin: 0;font-size: 14px;letter-spacing: .8px;font-weight: 600; color: white;border-bottom: 1px solid; padding-bottom: 6px;">
                                            Bank Details</p>
                                        <p
                                            style="margin: 0;font-size: 14px;letter-spacing: .8px;margin-top: 7px;margin-top: 0px; line-height: 21px; padding-top: 7px; color: white;">
                                            <b>' . $cfg['INVOICE_BANKNAME'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BENEFECIARY'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKACNO'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKBRANCH'] . '</b>
                                            <br>
                                            <b>' . $cfg['INVOICE_BANKIFSC'] . '</b>
                                        </p>

                                    </td>
                                </tr>
                            </tbody>
                        </table>-->
                    </td>
                    <td style="width: 70%;padding: 0 20px;vertical-align: text-bottom;background: #BADEC4;">
                        <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="text-align: right;font-size: 18px;padding-bottom: 25px;font-weight: 600;color: #114C5C;">
                                        
                                        <p style="margin-top: 2px;font-size: 14px;line-height: 24px;">Reg. Id:
                                            ' . $delegateDetails['user_registration_id'] . '<br>
                                            Email Id: ' . $delegateDetails['user_email_id'] . '
                                        </p>
                                    </td>
                                </tr>';
	$contentBody .= '<tr><td><div style="color: #0b6d99; font-weight:bold; padding:5px; margin:0px; font-size:15px; text-align:center;">';
	$contentBody .= '			ORDER SUMMARY';
	$contentBody .= '			</div><br></tr></td>';

	$contentBody .= '     <tr>
                                    <td>
                                        <table cellspacing="0" cellpadding="0" border="0"
                                            style="width: 100%; color: #114C5C;">
                                            <tbody>
                                                <tr>
                                                    <td
                                                        style="width: 30%;  font-weight: bold ; text-align: left; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                       Invoice No.</td>
                                                    <td
                                                        style="width: 45%;  font-weight: bold ;  font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Invoice for</td>
                                                    <td
                                                        style="width: 25%;  font-weight: bold ; text-align: right; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Amount
                                                    </td>
                                                    
                                                   <!-- <td
                                                        style="width: 25%;  font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: right;">
                                                        Amount (' . getInvoiceCurrencyById($slipId) . ')</td>-->
                                                </tr>';


	$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
	$counter = 0;
	$invoiceDisplaysArr = array();
	foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
		$show = true;
		if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id'], true);
			if ($workShopDetails['display'] == 'N') {
				if ($invoiceDetails['remarks'] == 'Adjusted Workshop') {
					$show = false;
				}
			}
		}

		if ($show) {
			$counter 		 = $counter + 1;
			$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);

			$type			 = "";

			$invoiceServiceType = $invoiceDetails['service_type'];
			if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
			}
			if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				// $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
				$comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
				$type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
			}
			if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
				$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
			}
			if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
			}
			if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
			}
			if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
				$tourDetails = getTourDetails($invoiceDetails['refference_id']);

				$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
			}
			if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
				$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
				$dinnerRefId = $dinnerDetails['refference_id'];
				$dinner_user_type = dinnerForWhome($dinnerRefId);
				if ($dinner_user_type == 'ACCOMPANY') {
					$invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
				} else {
					$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
				}
			}
			$invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
			$invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
			$counter++;
		}
	}

	foreach ($cfg['SERVICE.SEQUENCE'] as $keyServ => $servType) {
		$invoices = $invoiceDisplaysArr[$servType];
		foreach ($invoices as $k => $invDetails) {
			$invoiceDetails = $invDetails['INVOICE_DETAILS'];
			$type			= $invDetails['TYPE'];

			$returnArray 	= discountAmount($invoiceDetails['id']);
			//print_r($returnArray);
			$originalAmount = number_format($returnArray['PRE_DISCOUNT_AMOUNT'], 2);
			$percentage  	= $returnArray['PERCENTAGE'];

			if ($invoiceDetails['invoice_mode'] == 'OFFLINE') {
				$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
			} else {
				$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
			}

			$discountAmount = $returnArray['DISCOUNT'];
			$message .= '				<tr>';
			$message .= '					<td style="border:thin solid #00adde;" align="left"  valign="top" >' . $invoiceDetails['invoice_number'] . '</td>';
			$message .= '					<td style="border:thin solid #00adde; padding-left: 15px; text-align:left;" align="left" valign="top">' . $type . '</td>';

			$contentBody                    .= '<tr>
													<td style="text-align: left; font-size: 13px; padding:10px 2px;">' . $invoiceDetails['invoice_number'] . '
														</td>
													<td style="font-size: 13px; padding:10px 0px;">' . $type . '
														</td>
													
													<!--<td style="text-align: ; font-size: 13px; padding:10px 2px;">
														' . $invoice_price . '
													</td>

													<td style="text-align: right; font-size: 13px; padding:10px 5px;">
														' . $invoice_price . '</td>
												</tr>-->';

			if ($invoiceDetails['service_roundoff_price'] == 0) {
				if ($invoiceDetails['payment_status'] == 'COMPLIMENTARY') {
					$contentBody .= '					<td style="text-align: right; font-size: 13px; padding:10px 2px;">';
					$contentBody .= '						Complimentary';
					$contentBody .= '					</td>';
					$contentBody .= '				</tr>';
				} elseif ($invoiceDetails['payment_status'] == 'ZERO_VALUE') {
					$contentBody .= '					<td style="text-align: right; font-size: 13px; padding:10px 2px;">';
					$contentBody .= '						Zero Value';
					$contentBody .= '					</td>';
					$contentBody .= '				</tr>';
				} else {
					$contentBody .= '					<td style="text-align: right; font-size: 13px; padding:10px 2px;">';
					$contentBody .= '						Inclusive';
					$contentBody .= '					</td>';
					$contentBody .= '				</tr>';
				}
			} else {
				$contentBody .= '					<td style="text-align: right; font-size: 13px; padding:10px 2px;">';
				$contentBody .= '						' . number_format($invoiceVal, 2) . '';
				$contentBody .= '					</td>';
				$contentBody .= '				</tr>';
			}
		}
	}

	$message .= '					<tr>';
	$message .= '						<td align="left" style="border:thin solid #00adde;" valign="middle" colspan="2" height="30" valign="top"><strong style="float:right; padding-right:15px;">Total Amount</strong> <span style="color:#FF0000; float:left; padding-left:15px;">' . ($invoiceDetails['invoice_mode'] == 'ONLINE' ? 'Inclusive of All Taxes and Internet Handling Charges' : '** Inclusive of All Taxes(18% GST)') . '</span></td>';
	$message .= '						<td style="text-align:right; padding-right:15px; border:thin solid #00adde;" align="right" valign="middle">';
	$message .= '							' . getInvoiceCurrency($slipId) . ' ';
	$message .= '							' . number_format($invoiceAmountOfSlip, 2) . '';
	$message .= '						</td>';
	$message .= '					</tr>';

	$contentBody                    .= '      <tr>
												<td colspan="2"
													style="font-size: 14px; padding:10px 5px; border-bottom: 2px solid #114C5C; font-weight: bold;">
													</td>
												<td colspan="4"
													style="font-size: 14px; padding:10px 5px; border-bottom: 2px solid #114C5C; font-weight: bold; text-align: right;">
													Total Amount</td>
											</tr>
											<tr>
												<td colspan="2"
													style="padding:10px 2px; border-bottom: 2px solid #114C5C; font-size: 20px; font-weight: bold;">
													 <span style="color:#FF0000; float:left; padding-left:2px;font-size:13px">'
		. ($invoiceDetails['invoice_mode'] == 'ONLINE' ?
			'Inclusive of All Taxes and Internet Handling Charges' : '** Inclusive of All Taxes(18% GST)') . '</span>
													 </td>
												<td colspan="4"
													style="padding:10px 2px; border-bottom: 2px solid #114C5C; font-size: 18px; text-align: right; font-weight: bold;">
													' . getInvoiceCurrency($slipId) . ' ' . number_format($invoiceAmountOfSlip, 2) . '</td>
											</tr>';
	$contentBody .= '</table></td></tr>';
	if ($showPendingPayments && floatval($pendingAmountOfSlip) > 0) {
		$message .= '					<tr>';
		$message .= '						<td align="left" style="border:thin solid #00adde;" valign="middle" colspan="2" height="30" valign="top"><strong style="float:right; padding-right:15px;">Paid Amount</strong></td>';
		$message .= '						<td style="text-align:right; padding-right:15px; border:thin solid #00adde;" align="right" valign="middle">';
		$message .= '							' . getInvoiceCurrency($slipId) . ' ';
		$message .= '							' . number_format($totalSetPaymentAmountOfSlip, 2) . '';
		$message .= '						</td>';
		$message .= '					</tr>';
		$message .= '					<tr>';
		$message .= '						<td align="left" style="border:thin solid #00adde;" valign="middle" colspan="2" height="30" valign="top"><strong style="float:right; padding-right:15px;">Balance Payable Amount</strong></td>';
		$message .= '						<td style="text-align:right; padding-right:15px; border:thin solid #00adde;" align="right" valign="middle">';
		$message .= '							' . getInvoiceCurrency($slipId) . ' ';
		$message .= '							' . number_format($pendingAmountOfSlip, 2) . '';
		$message .= '						</td>';
		$message .= '					</tr>';
	}

	$message .= '				</table>';



	if ($showTransaction && invoiceAmountOfSlip($slipId) > 0) {
		$resPaymentDetails      = paymentDetails($slipId);

		$paymentDescription     = "NA";

		$contentBody .= '<tr><td><br><div style="color: #0b6d99; font-weight:bold; padding:5px; margin:0px; font-size:15px; text-align:center;">';
		$contentBody .= '			TRANSACTION SUMMARY';
		$contentBody .= '			</div><br></tr></td>';
		if ($resPaymentDetails['payment_mode'] == "Online") {
			$payDate = setDateTimeFormat2($resPaymentDetails['payment_date'], "D");
			$paymentDescription = " Transaction No. <b>" . $resPaymentDetails['atom_atom_transaction_id'] . "</b><br>
									Bank Transaction No. <b>" . $resPaymentDetails['atom_bank_transaction_id'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "Cash") {
			$payDate = setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D");
			$paymentDescription = "NA";
		}
		if ($resPaymentDetails['payment_mode'] == "Card") {
			$payDate = setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D");
			$paymentDescription = "Reference No. <b>" . $resPaymentDetails['card_refference_no'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "Draft") {
			$payDate = setDateTimeFormat2($rowFetchSlip['draft_date'], "D");
			$paymentDescription = "Draft No. <b>" . $resPaymentDetails['draft_number'] . "</b><br>
								   Draft Date: <b>" . setDateTimeFormat2($resPaymentDetails['draft_date'], "D") . "</b>
								   Draft Drawee Bank: <b>" . $resPaymentDetails['draft_bank_name'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "NEFT") {
			$payDate = setDateTimeFormat2($resPaymentDetails['neft_date'], "D");
			$paymentDescription = "Transaction No. <b>" . $resPaymentDetails['neft_transaction_no'] . "</b><br>
								   Transaction Date: <b>" . setDateTimeFormat2($resPaymentDetails['neft_date'], "D") . "</b>
								   Transaction Bank: <b>" . $resPaymentDetails['neft_bank_name'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "RTGS") {
			$payDate = setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D");
			$paymentDescription = "Transaction No. <b>" . $resPaymentDetails['rtgs_transaction_no'] . "</b><br>
								   Transaction Date: <b>" . setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D") . "</b>
								   Transaction Bank: <b>" . $resPaymentDetails['rtgs_bank_name'] . "</b>";
		}
		if ($resPaymentDetails['payment_mode'] == "Cheque") {
			$payDate = setDateTimeFormat2($resPaymentDetails['cheque_date'], "D");
			$paymentDescription = "Cheque/DD No. <b>" . $resPaymentDetails['cheque_number'] . "</b><br>
								    Date: <b>" . setDateTimeFormat2($resPaymentDetails['cheque_date'], "D") . "</b>
								    Drawee Bank: <b>" . $resPaymentDetails['cheque_bank_name'] . "</b>";
		}

		if ($resPaymentDetails['payment_mode'] == "Online") {


			$message .= '			<table width="100%" style="font-size:13px;">';
			$message .= '				<tr>';
			$message .= '					<td align="center" valign="middle" height="25" style="border:thin solid #00adde; font-weight:bold;" width="20%"> PV No.</td>';
			$message .= '					<td width="16%" align="right" valign="middle" style="border:thin solid #00adde; font-weight:bold;  padding-right: 15px;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>';
			$message .= '					<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;" width="16%">Payment Mode</td>';
			$message .= '					<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;">Payment Date</td>';
			$message .= '					<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;">Transaction Details</td>';
			$message .= '				</tr>';
			$message .= '				<tr>';
			$message .= '					<td style="border:thin solid #00adde;" align="center"  valign="top" >' . $slipDetails['slip_number'] . '</td>';
			$message .= '					<td style="border:thin solid #00adde; padding-right: 15px;" align="right" valign="top">';
			$message .= '						' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '';
			$message .= '						' . invoiceAmountOfSlip($slipId) . '';
			$message .= '					</td>';
			$message .= '					<td style="border:thin solid #00adde;" align="center"  valign="top" >' . $resPaymentDetails['payment_mode'] . '</td>';
			$message .= '					<td style="border:thin solid #00adde;" align="center" valign="top">' . $payDate . '</td>';
			$message .= '					<td style="border:thin solid #00adde;" align="center"  valign="top" >' . $paymentDescription . '</td>';
			$message .= '				</tr>';
			$message .= '			</table>';

			$contentBody                    .= '<tr>
                                    <td>
                                        <table cellspacing="0" cellpadding="0" border="0"
                                            style="width: 100%; color: #114C5C;">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 30%; text-transform: uppercase; font-weight: bold ; text-align: left; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                       PV No.</td>
                                                    <td style="width: 25%; text-transform: uppercase; font-weight: bold ;  font-size: 12px; text-align: center; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                        Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>
                                                    <td style="width: 25%; text-transform: uppercase; font-weight: bold ; text-align: center; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                       Payment Mode
                                                    </td>
                                                    <td style="width: 25%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: center;">
                                                       Payment Date
													</td>
													<td style="width: 25%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: center;">
                                                       Transaction Details
													</td>
                                                </tr>
												<tr>
													<td style="text-align: left; font-size: 13px; padding:10px 2px;">' . $slipDetails['slip_number'] . '
													</td>
													<td style="text-align: center; font-size: 13px; padding:10px 2px;">'
				. getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . invoiceAmountOfSlip($slipId)  . '
														</td>
													<td style="text-align: center; font-size: 13px; padding:10px 2px;">' . $resPaymentDetails['payment_mode'] . '
														</td>
													<td style="font-size: 13px; padding:10px 0px;text-align: center;">' . $payDate . '
														</td>
													<td style="text-align:left; font-size: 13px; padding:10px 2px;">
														' . $paymentDescription . '
													</td>
												</tr>';
		}

		if ($resPaymentDetails['payment_mode'] != "Online") {
			$message .= '			<table width="100%" style="font-size:13px;">';
			$message .= '				<tr>';
			$message .= '					<td align="center" valign="middle" height="25" style="border:thin solid #00adde; font-weight:bold;" width="20%"> Payment Status</td>';
			$message .= '					<td width="16%" align="right" valign="middle" style="border:thin solid #00adde; font-weight:bold;  padding-right: 15px;">Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')</td>';
			$message .= '					<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;" width="16%">Payment Mode</td>';
			$message .= '					<td align="center" valign="middle" style="border:thin solid #00adde; font-weight:bold;">Transaction Details</td>';
			$message .= '				</tr>';
			$message .= '				<tr>';
			$contentBody                    .= '<tr>
                                    <td>
                                        <table cellspacing="0" cellpadding="0" border="0"
                                            style="width: 100%; color: #114C5C;">
                                            <tbody>
                                                <tr>
                                                    
                                                    <td style="width: 15%; text-transform: uppercase; font-weight: bold ;  font-size: 12px; text-align: center; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                       Payment Status</td>
                                                    <td style="width: 15%; text-transform: uppercase; font-weight: bold ; text-align: center; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C;">
                                                       Amount (' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ')
                                                    </td>
                                                    <td style="width: 20%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: center;">
                                                       Payment Mode
													</td>
													<td style="width: 50%; text-transform: uppercase; font-weight: bold ; font-size: 12px; padding:10px 5px; border-bottom:2px solid #114C5C; text-align: center;">
                                                       Transaction Details
													</td>
                                                </tr><tr>';

			if ($resPaymentDetails['payment_status'] == "PAID") {
				$contentBody .= '				<td style="text-align: center; font-size: 13px; padding:10px 2px; v-align:top">Paid</td>';
			} else {
				$contentBody .= '				<td style="text-align: center; font-size: 13px; padding:10px 2px;v-align:top">Pending</td>';
			}
			$contentBody .= '					<td style="text-align: center; font-size: 13px; padding:10px 2px;v-align:top">';
			$contentBody .= '						' . getInvoiceCurrency($slip_id) . '';
			$contentBody .= '						' . invoiceAmountOfSlip($slipId) . '';
			$contentBody .= '					</td>';
			$contentBody .= '					<td style="text-align: center; font-size: 13px; padding:10px 2px;v-align:top">';
			if ($resPaymentDetails['payment_mode'] == "") {
				$contentBody .= '					' . $invoiceDetails['invoice_mode'];
			} else {
				$paymentModeDisplay = $resPaymentDetails['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($resPaymentDetails['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $resPaymentDetails['payment_mode']);
				$contentBody .= '					' . $paymentModeDisplay;
			}
			$contentBody .= '					</td>';
			$contentBody .= '					<td style="text-align: center; font-size: 13px; padding:10px 2px;">' . $paymentDescription . '</td>';
			$contentBody .= '				</tr>';
		}
	}
	$contentBody .= '		</table>	</td>';
	$message .= '		</tr>';

	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	if ($row['footer_image'] != '') {
		$emailFooter  = $footer_image;
	}

	if ($showHeaderFooter && $emailFooter) {
		$message .= '	<tr>';
		$message .= '		<td align="center" valign="bottom" style="border-collapse:collapse;">';
		// $message .='		<img src="'._BASE_URL_.'images/PvFooter.jpg" width="100%" />';
		$message .= '		<img src="' . $emailFooter . '" width="100%" />';
		$message .= '		</td>';
		$message .= '	</tr>';
	}

	$message .= '	</table>';
	$message .= '</div>';

	return $contentBody;
}

function insertingComplementaryInvoiceDetails($reqId)
{
	global $cfg, $mycms;

	$details = getUserDetails($reqId);

	$invoiceDetails['delegate_id']					= $details['id'];
	$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
	$invoiceDetails['invoice_number']				= generateNextCode("id", _DB_INVOICE_, "ISAR18/17-18/" . date('dmy') . "/");
	$invoiceDetails['invoice_date']					= date('Y-m-d');
	$invoiceDetails['invoice_request']				= 'GENERAL';
	$invoiceDetails['invoice_mode']					= 'OFFLINE';
	$invoiceDetails['currency']						= getRegistrationCurrency($details['registration_classification_id']);
	$invoiceDetails['registration_type']			= 'GENERAL';
	$invoiceDetails['refference_id']				= $reqId;
	$dtlsArr										= getUserTypeAndRoomType($details['registration_classification_id']);
	if ($dtlsArr['TYPE'] == 'COMBO') {
		$invoiceDetails['service_type']				= "DELEGATE_RESIDENTIAL_REGISTRATION";
	} else {
		$invoiceDetails['service_type']				= "DELEGATE_CONFERENCE_REGISTRATION";
	}

	$invoiceDetails['tariff_ref_id']				= 0;
	$invoiceDetails['service_tariff_cutoff_id']		= $details['registration_tariff_cutoff_id'];

	$invoiceDetails['service_unit_price']			= 0;
	$invoiceDetails['service_consumed_quantity']	= 1;
	$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];


	$applicableTaxPercentage          = 0.00;
	$internetHandlingPercentage       = 0.00;


	$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
	$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
	$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
	$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
	$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
	$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
	$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
	$invoiceDetails['payment_status']				= 'COMPLIMENTARY';

	$sqlInsertInvoiceRequest = array();
	$sqlInsertInvoiceRequest['QUERY']   = "INSERT INTO " . _DB_INVOICE_ . " 
											  SET `delegate_id`	   	 			 = ?,
												  `slip_id` 		 	 		 = ?,
												  `invoice_date` 	 	 		 = ?, 
												  `invoice_request` 			 = ?, 
												  `invoice_mode`	 	 		 = ?,
												  `currency` 					 = ?,
												  `registration_type`	 		 = ?, 
												  `refference_id` 	 	 		 = ?,
												  `service_type`	   	 		 = ?,
												  `tariff_ref_id` 		 	 	 = ?, 
												  `service_tariff_cutoff_id` 	 = ?, 
												  `service_unit_price` 	 	  	 = ?, 
												  `service_consumed_quantity` 	 = ?, 
												  `service_product_price`	 	 = ?,
												  `internet_handling_percentage` = ?,
												  `internet_handling_amount` 	 = ?, 
												  `service_total_price` 	  	 = ?, 
												  `service_grand_price`	 	 	 = ?,
												  `service_roundoff_price`	 	 = ?,
												  `payment_status` 				 = ?,
												  `status` 				 		 = ?,
												  `created_ip`			 		 = ?,
												  `created_sessionId`	 		 = ?,
												  `created_browser`  	 		 = ?,
												  `created_dateTime` 	 		 = ?";

	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'delegate_id',                 'DATA' => $invoiceDetails['delegate_id'],                   'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'slip_id',                     'DATA' => $invoiceDetails['slip_id'],                       'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'invoice_date',                'DATA' => $invoiceDetails['invoice_date'],                  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'invoice_request',             'DATA' => $invoiceDetails['invoice_request'],               'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'invoice_mode',                'DATA' => $invoiceDetails['invoice_mode'],                  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'currency',                    'DATA' => $invoiceDetails['currency'],                      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'registration_type',           'DATA' => $invoiceDetails['registration_type'],             'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'refference_id',               'DATA' => $invoiceDetails['refference_id'],                 'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_type',                'DATA' => $invoiceDetails['service_type'],                  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'tariff_ref_id',               'DATA' => $invoiceDetails['tariff_ref_id'],                 'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_tariff_cutoff_id',    'DATA' => $invoiceDetails['service_tariff_cutoff_id'],      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_unit_price',          'DATA' => $invoiceDetails['service_unit_price'],            'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_consumed_quantity',   'DATA' => $invoiceDetails['service_consumed_quantity'],     'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_product_price',       'DATA' => $invoiceDetails['service_product_price'],         'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_percentage', 'DATA' => $invoiceDetails['internet_handling_percentage'],  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_amount',    'DATA' => $invoiceDetails['internet_handling_amount'],      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_total_price',         'DATA' => $invoiceDetails['service_total_price'],           'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_grand_price',         'DATA' => $invoiceDetails['service_grand_price'],           'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_roundoff_price',      'DATA' => $invoiceDetails['service_roundoff_price'],        'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'payment_status',              'DATA' => $invoiceDetails['payment_status'],                'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'status',                      'DATA' => 'A',                                              'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_ip',                  'DATA' => $_SERVER['REMOTE_ADDR'],                          'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_sessionId',           'DATA' => session_id(),                                     'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_browser',             'DATA' => $_SERVER['HTTP_USER_AGENT'],                      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_dateTime',            'DATA' => date('Y-m-d H:i:s'),                              'TYP' => 's');


	$invoiceId			         = $mycms->sql_insert($sqlInsertInvoiceRequest, false);
	return $invoiceId;
}

function setUserPaymentStatus($delegateId)
{
	global $cfg, $mycms;

	$workshopPaymentStatusArr = registrationPaymentStatus($delegateId, "WORKSHOP");

	$sqlUpdate1 = array();
	$sqlUpdate1['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
							 SET `workshop_payment_status` = ?
						   WHERE `id` = ?";

	$sqlUpdate1['PARAM'][]   = array('FILD' => 'workshop_payment_status', 'DATA' => $workshopPaymentStatusArr['paymentStatus'], 'TYP' => 's');
	$sqlUpdate1['PARAM'][]   = array('FILD' => 'id',                      'DATA' => $delegateId,                                'TYP' => 's');

	$mycms->sql_update($sqlUpdate1, false);

	$accomPaymentStatusArr = registrationPaymentStatus($delegateId, "ACCOMMODATION");
	$sqlUpdate2 = array();
	$sqlUpdate2['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
							 SET `accommodation_payment_status` = ?
						   WHERE `id` = ?";

	$sqlUpdate2['PARAM'][]   = array('FILD' => 'accommodation_payment_status', 'DATA' => $accomPaymentStatusArr['paymentStatus'], 'TYP' => 's');
	$sqlUpdate2['PARAM'][]   = array('FILD' => 'id',                           'DATA' => $delegateId,                                'TYP' => 's');

	$mycms->sql_update($sqlUpdate2, false);

	$tourPaymentStatusArr = registrationPaymentStatus($delegateId, "TOUR");
	$sqlUpdate3 = array();
	$sqlUpdate3['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
								 SET `tour_payment_status` = ?
							   WHERE `id` =?";

	$sqlUpdate3['PARAM'][]   = array('FILD' => 'tour_payment_status', 'DATA' => $tourPaymentStatusArr['paymentStatus'], 'TYP' => 's');
	$sqlUpdate3['PARAM'][]   = array('FILD' => 'id',                  'DATA' => $delegateId,                             'TYP' => 's');

	$mycms->sql_update($sqlUpdate3, false);
}

function getCountAccompanyUser($userID)
{

	global $cfg, $mycms;
	$sqlFetchUser            = array();
	$sqlFetchUser['QUERY']	  = "SELECT id 
							  	   FROM " . _DB_USER_REGISTRATION_ . "  
							 	  WHERE `refference_delegate_id` = ? 
								 ";

	$sqlFetchUser['PARAM'][]   = array('FILD' => 'refference_delegate_id', 'DATA' => $userID,  'TYP' => 's');


	$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
	return $maxRowsUser                = $mycms->sql_numrows($resultFetchUser);
}

function insertingInvoiceDetails($reqId = "", $type, $invReq = 'GENERAL', $date = '', $COUNTER = '',$paymentstatuspre='')
{
	global $cfg, $mycms;
	$loggedUserId       = $mycms->getSession('LOGGED.USER.ID');
	if ($date == '') {
		$date = date('Y-m-d');
	}
	if ($type == "CONFERENCE") {
		$details = getUserDetails($reqId);

		$invoiceDetails['delegate_id']					= $details['id'];
		$invoiceDetails['slip_id']						= ($mycms->getSession('SLIP_ID') == "") ? 0 : $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_date']					= $date;
		$invoiceDetails['invoice_request']				= $invReq;
		$invoiceDetails['invoice_mode']					= $details['registration_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency($details['registration_classification_id']);
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$dtlsArr										= getUserTypeAndRoomType($details['registration_classification_id']);

		if ($dtlsArr['TYPE'] == 'COMBO') {
			$invoiceDetails['service_type']				= "DELEGATE_RESIDENTIAL_REGISTRATION";
		} else {
			$invoiceDetails['service_type']				= "DELEGATE_CONFERENCE_REGISTRATION";
		}
		$invoiceDetails['tariff_ref_id']				= getRegistrationTariffId($details['registration_classification_id'], $details['registration_tariff_cutoff_id']);
		$invoiceDetails['service_tariff_cutoff_id']		= $details['registration_tariff_cutoff_id'];

		// if ($details['operational_area'] == 'EXHIBITOR') {
		// 	$registrationTariffArr						= exhibitorTariffDetailsQuerySet();
		// } else {
			$registrationTariffArr						= getAllRegistrationTariffs("", false);
		// }

		if ($details['isCombo'] == 'Y') {
			$invoiceDetails['service_unit_price'] = getClassificationComboPrice($details['registration_classification_id']);
		} else {
			$invoiceDetails['service_unit_price']			= ($invReq == 'GENERAL') ? $registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['AMOUNT'] : $registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['DISPLAY_AMOUNT'];
		}
		//echo $invoiceDetails['service_unit_price'];
		//die();

		if ($invReq == 'COUNTER') {
			$invoiceDetails['service_basic_price']		= $registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['DISPLAY_AMOUNT'];
		}

		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];
		if ($details['registration_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['registration_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;



		/*if($cfg['GST.FLAG']==1)
		{
			$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount(($invoiceDetails['service_product_price']+$invoiceDetails['applicable_tax_amount']),$internetHandlingPercentage);
		}
		else
		{
			$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'],$internetHandlingPercentage);
		}*/
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';

		// echo '<pre>';print_r($invoiceDetails);
		// die();
	}

    if ($type == "WORKSHOP") {
		$details = getWorkshopDetails($reqId);

		$invoiceDetails['delegate_id']					= $details['delegate_id'];
		$invoiceDetails['slip_id']						= (!$mycms->isSession('SLIP_ID')) ? 0 : $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_date']					= $date;
		$invoiceDetails['invoice_request']				= $invReq;
		$invoiceDetails['invoice_mode']					= $details['booking_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency($details['registration_classification_id']);
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "DELEGATE_WORKSHOP_REGISTRATION";
		$invoiceDetails['tariff_ref_id']				= $details['workshop_tarrif_id'];
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];

		$workshopTariffArr							    = getAllWorkshopTariffs();
			// echo '<pre>'; print_r($details);die;
        if ($isCombo == 'Y') {
		  $invoiceDetails['service_unit_price']			=0;
		}else if($invoiceDetails['invoice_request']=='ONLYWORKSHOP' ||$details['registration_type'] =='ONLYWORKSHOP'){
			$invoiceDetails['service_unit_price']			= getOnlyWorkshopTariffAmount($details['workshop_tarrif_id']);

		} else {
	    	$invoiceDetails['service_unit_price']			= $workshopTariffArr[$details['workshop_id']][$details['registration_classification_id']][$details['tariff_cutoff_id']][$invoiceDetails['currency']];
		}
		// $invoiceDetails['service_unit_price']			= $workshopTariffArr[$details['workshop_id']][$details['registration_classification_id']][$details['tariff_cutoff_id']][$invoiceDetails['currency']];
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];
        // echo '<pre>';print_r($workshopTariffArr);
		// die();
		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		// $invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		// $invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'],$applicableTaxPercentage);
		// $invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		// $invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'],$internetHandlingPercentage);
		// $invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		// $invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		// $invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		// $invoiceDetails['payment_status']				= 'UNPAID';

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';

		// echo '<pre>';print_r($workshopTariffArr);
		// die();
	}

	if ($type == "ACCOMPANY") {
		$details = getUserDetails($reqId);

		// echo '<pre>'; print_r($details);


		if ($details['registration_mode'] == 'OFFLINE' && $details['reg_type'] != 'FRONT') {
			$countAccUser = getCountAccompanyUser($details['refference_delegate_id']);
			$cutoffTarrifAmnt = getCutoffTariffAmnt($details['registration_tariff_cutoff_id']);
			$cutoffAmnt = ($cutoffTarrifAmnt);

		} else {
			$currentCutoffId   = getTariffCutoffId();
			$cutoffAmnt = getCutoffTariffAmnt($currentCutoffId);
		}


		$invoiceDetails['delegate_id']					= $details['refference_delegate_id'];
		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_date']					= $date;
		$invoiceDetails['invoice_request']				= $invReq;
		$invoiceDetails['invoice_mode']					= $details['registration_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency($details['registration_classification_id']);
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "ACCOMPANY_CONFERENCE_REGISTRATION";
		$invoiceDetails['tariff_ref_id']				= getRegistrationTariffId($details['registration_classification_id'], $details['registration_tariff_cutoff_id']);
		$invoiceDetails['service_tariff_cutoff_id']		= $details['registration_tariff_cutoff_id'];
		$registrationTariffArr							= getAllRegistrationTariffs();

		//$invoiceDetails['service_unit_price']			= ($invReq=='GENERAL')?$registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['AMOUNT']:$registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['DISPLAY_AMOUNT'];

		$invoiceDetails['service_unit_price']	= $cutoffAmnt;

		if ($invReq == 'COUNTER') {
			$invoiceDetails['service_basic_price']		= $registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['DISPLAY_AMOUNT'];
		}
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['registration_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['registration_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}

	if ($type == "ACCOMMODATION") {
		$details = getAccomodationDetails($reqId);

		// echo '<pre>'; print_r($details); 

		$origin = date_create($details['checkin_date']);
		$target = date_create($details['checkout_date']);
		$interval = date_diff($origin, $target);
		$days = $interval->format('%a');


		$invoiceDetails['delegate_id']					= $details['user_id'];
		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_date']					= date('Y-m-d');
		$invoiceDetails['invoice_request']				= $invReq;
		$invoiceDetails['invoice_mode']					= $details['booking_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency(getUserClassificationId($details['user_id']));
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "DELEGATE_ACCOMMODATION_REQUEST";

		$tariffId 										= $details['tariff_ref_id'];
		$invoiceDetails['tariff_ref_id']				= $details['tariff_ref_id'];
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];
		//$accommodationTariffArr							= getAccommodationTariffDetails($tariffId);
		$accommodationTariffArr							= getAccommodationPackageDetails($details['tariff_cutoff_id'], $details['hotel_id'], $details['package_id'],$details['roomTypeId']);
		$rowfetchTariffDetails							= $accommodationTariffArr[0];
        if($details['booking_quantity']==0.5){
			$details['booking_quantity']= 1;
		}else{
			$details['booking_quantity'] = $details['booking_quantity'];
		}
		 if ($isCombo == 'Y') {
		  $invoiceDetails['service_unit_price']			=0;
		} else {
		  $invoiceDetails['service_unit_price']			= $rowfetchTariffDetails['inr_amount'] * $days * $details['booking_quantity'];
		}

		//echo '<pre>'; print_r($invoiceDetails);
		//die();
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}

	if ($type == "TOUR") {
		$details = getTourDetails($reqId);

		$invoiceDetails['delegate_id']					= $details['user_id'];
		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_number']				= generateNextCode("id", _DB_INVOICE_, "ISAR18/17-18/");
		$invoiceDetails['invoice_date']					= date('Y-m-d');
		$invoiceDetails['invoice_request']				= 'GENERAL';
		$invoiceDetails['invoice_mode']					= $details['booking_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency(getUserClassificationId($details['user_id']));
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "DELEGATE_TOUR_REQUEST";
		$invoiceDetails['tariff_ref_id']				= getTourTariffId($details['package_id'], $details['tariff_cutoff_id']);
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];
		$tourTariffArr									= tourTariffDetailsQuerySet();
		$invoiceDetails['service_unit_price']			= $tourTariffArr[$details['package_id']][$details['tariff_cutoff_id']][$invoiceDetails['currency']];
		$invoiceDetails['service_consumed_quantity']	= $details['booking_quantity'];
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}

	if ($type == "DINNER") {
		$details = getDinnerDetails($reqId);
		$detailsClass = getUserDetails($details['delegate_id']);

		$dinnerArray 									= $cfg['DINNER_ARRAY'];
		$invoiceDetails['delegate_id']					= $details['delegate_id'];

		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_date']					= $date;
		$invoiceDetails['invoice_request']				= $invReq;
		$invoiceDetails['invoice_mode']					= $details['booking_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency($detailsClass['registration_classification_id']);;
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "DELEGATE_DINNER_REQUEST";
		$invoiceDetails['tariff_ref_id']				= $details['package_id'];
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];

		$dinnerTariffArr						        = getAllDinnerTarrifDetails();

		$invoiceDetails['service_unit_price']			= $dinnerTariffArr[$details['package_id']][$details['tariff_cutoff_id']]['AMOUNT'];

		if ($invReq == 'COUNTER') {
			$invoiceDetails['service_basic_price']		=  $dinnerTariffArr[$details['package_id']][$details['tariff_cutoff_id']]['AMOUNT'];
		}
		$invoiceDetails['service_consumed_quantity']	= $details['booking_quantity'];
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}
	if($paymentstatuspre==''){
      $payment_statusPre=$invoiceDetails['payment_status'];

	}else{
     $payment_statusPre=$paymentstatuspre;

	}


	$sqlInsertInvoiceRequest = array();
	$sqlInsertInvoiceRequest['QUERY']   = "INSERT INTO " . _DB_INVOICE_ . " 
											  SET `delegate_id`	   	 			 = ?,
												  `slip_id` 		 	 		 = ?,
												  `invoice_date` 	 	 		 = ?, 
												  `invoice_request` 			 = ?, 
												  `invoice_mode`	 	 		 = ?,
												  `currency` 					 = ?,
												  `registration_type`	 		 = ?, 
												  `refference_id` 	 	 		 = ?,
												  `service_type`	   	 		 = ?,
												  `service_tariff_cutoff_id` 	 = ?, 
												  `service_unit_price` 	 	  	 = ?, 
												  `service_consumed_quantity` 	 = ?, 
												  `service_product_price`	 	 = ?,
												  `internet_handling_percentage` = ?,
												  `internet_handling_amount` 	 = ?, 
												  `service_total_price` 	  	 = ?, 
												  `service_grand_price`	 	 	 = ?,
												  `service_roundoff_price`	 	 = ?,
												  `payment_status` 				 = ?,
												  `status` 				 		 = ?,
												  `created_by`			 		 = ?,
												  `created_ip`			 		 = ?,
												  `created_sessionId`	 		 = ?,
												  `created_browser`  	 		 = ?,
												  `created_dateTime` 	 		 = ?";

	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'delegate_id',                 'DATA' => $invoiceDetails['delegate_id'],                   'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'slip_id',                     'DATA' => $invoiceDetails['slip_id'],                       'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'invoice_date',                'DATA' => $invoiceDetails['invoice_date'],                  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'invoice_request',             'DATA' => $invoiceDetails['invoice_request'],               'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'invoice_mode',                'DATA' => $invoiceDetails['invoice_mode'],                  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'currency',                    'DATA' => $invoiceDetails['currency'],                      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'registration_type',           'DATA' => $invoiceDetails['registration_type'],             'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'refference_id',               'DATA' => $invoiceDetails['refference_id'],                 'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_type',                'DATA' => $invoiceDetails['service_type'],                  'TYP' => 's');
	//$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'tariff_ref_id',               'DATA' =>$invoiceDetails['tariff_ref_id'],                 'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_tariff_cutoff_id',    'DATA' => $invoiceDetails['service_tariff_cutoff_id'],      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_unit_price',          'DATA' => $invoiceDetails['service_unit_price'],            'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_consumed_quantity',   'DATA' => $invoiceDetails['service_consumed_quantity'],     'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_product_price',       'DATA' => $invoiceDetails['service_product_price'],         'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_percentage', 'DATA' => $invoiceDetails['internet_handling_percentage'],  'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_amount',    'DATA' => $invoiceDetails['internet_handling_amount'],      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_total_price',         'DATA' => $invoiceDetails['service_total_price'],           'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_grand_price',         'DATA' => $invoiceDetails['service_grand_price'],           'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'service_roundoff_price',      'DATA' => $invoiceDetails['service_roundoff_price'],        'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'payment_status',              'DATA' => $payment_statusPre,                'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'status',                      'DATA' => 'A',                                              'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_by',                  'DATA' => $loggedUserId,                          'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_ip',                  'DATA' => $_SERVER['REMOTE_ADDR'],                          'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_sessionId',           'DATA' => session_id(),                                     'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_browser',             'DATA' => $_SERVER['HTTP_USER_AGENT'],                      'TYP' => 's');
	$sqlInsertInvoiceRequest['PARAM'][]   = array('FILD' => 'created_dateTime',            'DATA' => date('Y-m-d H:i:s'),                              'TYP' => 's');
	    // echo "<pre>";
		// print_r($sqlInsertInvoiceRequest);
		// die();

	$invoiceId			       = $mycms->sql_insert($sqlInsertInvoiceRequest, false);

	$sqlInvoiceUpdate = array();
	$sqlInvoiceUpdate['QUERY']	        = "UPDATE " . _DB_INVOICE_ . "
											SET `invoice_number` = ?
										  WHERE `id` = ?";

	//$invoice_number = 'NEOCON2022-'.number_pad($invoiceId, 6);
	$invoice_number = $cfg['invoive_number_format'] . '-' . number_pad($invoiceId, 6);
	$sqlInvoiceUpdate['PARAM'][]   = array('FILD' => 'refference_invoice_id',   'DATA' => $invoice_number,  'TYP' => 's');
	$sqlInvoiceUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => $invoiceId,       'TYP' => 's');

	$mycms->sql_update($sqlInvoiceUpdate, false);

	if ($COUNTER == 'Y') {
		$discountPercentage	= 20;
		UpdateOfferDiscount($mycms->getSession('SLIP_ID'), $discountPercentage);
	}
	// echo $invReq;die;
	// if($invReq=='GENERAL'|| $invReq== 'ONLY-WORKSHOP')
	// {

	if ($invoiceDetails['invoice_mode'] == 'OFFLINE') {

		gstInsertionInInvoiceOffline($invoiceId);
	} else {

		gstInsertionInInvoice($invoiceId);
	}

	// }

	if ($type == "WORKSHOP") {
		// UPDATING USER ACCESS KEY AND REGISTRATION ID
		$sqlUpdate = array();

		$sqlUpdate['QUERY']	              = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
												SET `refference_invoice_id` = ?
											  WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_invoice_id',   'DATA' => $invoiceId,  'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => $reqId,   'TYP' => 's');
		$mycms->sql_update($sqlUpdate, false);
	}

	if ($type == "ACCOMMODATION") {
		$sqlUpdate['QUERY']	                      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
												SET `refference_invoice_id` = '" . $invoiceId . "'
											  WHERE `id` = '" . $reqId . "'";

		$mycms->sql_update($sqlUpdate, false);
	}

	if ($type == "TOUR") {
		$sqlUpdate['QUERY']	                      = "UPDATE " . _DB_REQUEST_TOUR_ . "
												SET `refference_invoice_id` = '" . $invoiceId . "'
											  WHERE `id` = '" . $reqId . "'";

		$mycms->sql_update($sqlUpdate, false);
	}

	if ($type == "DINNER") {
		$sqlUpdate                       = array();
		$sqlUpdate['QUERY']	             = "UPDATE " . _DB_REQUEST_DINNER_ . "
												SET `refference_invoice_id` = ?
											  WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                    'DATA' => $reqId,     'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);
	}


	return $invoiceId;
}

function updateonDiscount($slipId, $discountAmount)
{
	global $cfg, $mycms;
	$totalAmount  = array();
	$hasGST		  = 'N';
	$discountMode = 'PERCENT';
	$totalSlipAmount  = 0;
	$totalAmountAfterDiscount = 0;
	$invoices = invoiceDetailsOfSlip($slipId);
	foreach ($invoices as $k => $invoice) {

		$totalSlipAmount    		 += $invoice['service_unit_price'];
	}
	$discountPercent = ($discountAmount / $totalSlipAmount) * 100;

	foreach ($invoices as $k => $invoice) {

		$invoiceAmount = $invoice['service_unit_price'];
		$whatPercentIsThisInvoice = ($invoiceAmount / $totalSlipAmount) * 100;
		$shareOnTheDiscount = ($discountAmount * $whatPercentIsThisInvoice) / 100;
		$totalAmountAfterDiscount = $invoiceAmount - $shareOnTheDiscount;

		if ($invoice['invoice_mode'] == "ONLINE") {
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} elseif ($invoice['invoice_mode'] == "OFFLINE") {
			$internetHandlingPercentage       = 0.00;
		}

		$internet_handling_amount			   = calculateTaxAmmount($totalAmountAfterDiscount, $internetHandlingPercentage);

		$sqlUpdateInvoiceRequest = array();
		$sqlUpdateInvoiceRequest['QUERY']    = "UPDATE " . _DB_INVOICE_ . " 
												  SET `discount_amount`  = ?,
													  `total_amount_after_discount`  = ?,
													  `discount_percentage`  = ?,
													  `internet_handling_percentage` = ?,
													  `internet_handling_amount` = ?
												WHERE `status` = ?
												  AND `id` = ?";

		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'discount_amount',                 'DATA' => $shareOnTheDiscount,            'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'total_amount_after_discount',     'DATA' => $totalAmountAfterDiscount,      'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'discount_percentage',             'DATA' => $discountPercent,               'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_percentage',    'DATA' => $internetHandlingPercentage,    'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_amount',        'DATA' => $internet_handling_amount,      'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'status',                          'DATA' => 'A',                            'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'id',                              'DATA' => $invoice['id'],                 'TYP' => 's');

		$mycms->sql_update($sqlUpdateInvoiceRequest, false);




		if ($invoice['invoice_mode'] == 'OFFLINE') {
			gstInsertionInInvoiceOfflineDiscount($invoice['id']);
		} else {
			gstInsertionInInvoice($invoice['id']);
		}
	}
}

function discardDiscount($slipId)
{
	global $cfg, $mycms;
	$totalAmount  = array();
	$hasGST		  = 'N';
	$discountMode = 'PERCENT';
	$totalSlipAmount  = 0;
	$totalAmountAfterDiscardingDiscount = 0;
	$invoices = invoiceDetailsOfSlip($slipId);
	foreach ($invoices as $k => $invoice) {

		$totalSlipAmount    		 += $invoice['service_unit_price'];
	}
	$discountPercent = 0;

	foreach ($invoices as $k => $invoice) {

		$invoiceAmount = $invoice['service_unit_price'];
		// $whatPercentIsThisInvoice = ($invoiceAmount/$totalSlipAmount) * 100;
		// $shareOnTheDiscount = ($discountAmount * $whatPercentIsThisInvoice)/100;
		$totalAmountAfterDiscardingDiscount = $invoiceAmount;

		if ($invoice['invoice_mode'] == "ONLINE") {
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} elseif ($invoice['invoice_mode'] == "OFFLINE") {
			$internetHandlingPercentage       = 0.00;
		}

		$internet_handling_amount			   = calculateTaxAmmount($totalAmountAfterDiscardingDiscount, $internetHandlingPercentage);

		$sqlUpdateInvoiceRequest = array();
		$sqlUpdateInvoiceRequest['QUERY']    = "UPDATE " . _DB_INVOICE_ . " 
												  SET `discount_amount`  = ?,
													  `total_amount_after_discount`  = ?,
													  `discount_percentage`  = ?,
													  `internet_handling_percentage` = ?,
													  `internet_handling_amount` = ?
												WHERE `status` = ?
												  AND `id` = ?";

		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'discount_amount',                 'DATA' => 0.00,            'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'total_amount_after_discount',     'DATA' => $totalAmountAfterDiscardingDiscount,      'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'discount_percentage',             'DATA' => 0,               'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_percentage',    'DATA' => $internetHandlingPercentage,    'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'internet_handling_amount',        'DATA' => $internet_handling_amount,      'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'status',                          'DATA' => 'A',                            'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'id',                              'DATA' => $invoice['id'],                 'TYP' => 's');

		$mycms->sql_update($sqlUpdateInvoiceRequest, false);




		if ($invoice['invoice_mode'] == 'OFFLINE') {
			gstInsertionInInvoiceOffline($invoice['id']);
		} else {
			gstInsertionInInvoice($invoice['id']);
		}
	}
}




function updateOnDiscountWithTotal($slipId, $discountAmount, $totalAmount)
{

	global $cfg, $mycms;
	//echo 'total='.$totalAmount;
	//echo 'dis='.$discountAmount;

	$invoices = invoiceDetailsOfSlip($slipId);

	foreach ($invoices as $k => $invoice) {

		$sqlfetchInvoice = array();
		$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_INVOICE_ . "
										  WHERE `id` = ?";

		$sqlfetchInvoice['PARAM'][]   = array('FILD' => 'id',  'DATA' => $invoice['id'],  'TYP' => 's');

		$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
		$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];


		if ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {


			$servicePrice = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountedAmount = round($servicePrice - $discountAmount);

			$discountAmount = $discountAmount;
		} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {


			$servicePrice = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountedAmount = round($servicePrice - $discountAmount);
			$discountAmount = $discountAmount;
		} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {


			$servicePrice = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountedAmount = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountAmount = 0.00;
		} elseif ($rowDetailsfetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {

			$servicePrice = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountedAmount = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountAmount = 0.00;
		} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {


			$servicePrice = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountedAmount = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountAmount = 0.00;
		} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {


			$servicePrice = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountedAmount = $rowDetailsfetchInvoice['service_roundoff_price'];
			$discountAmount = 0.00;
		}

		$sqlUpdateInvoiceRequest = array();
		$sqlUpdateInvoiceRequest['QUERY']    = "UPDATE " . _DB_INVOICE_ . " 
													  SET `discount_amount`  = ?,
														  `total_amount_after_discount`  = ?,
														   `service_gst_total_price`     =?, 
														   `service_roundoff_price`     =?
													WHERE `status` = ?
													  AND `id` = ? ";

		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'discount_amount',                 'DATA' => $discountAmount,            'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'total_amount_after_discount',     'DATA' => $discountedAmount,      'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'service_gst_total_price',     'DATA' => $discountedAmount,      'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'service_roundoff_price',     'DATA' => $discountedAmount,      'TYP' => 's');

		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'status',                          'DATA' => 'A',                            'TYP' => 's');
		$sqlUpdateInvoiceRequest['PARAM'][]   = array('FILD' => 'id',                              'DATA' => $invoice['id'],                 'TYP' => 's');

		//print_r($sqlUpdateInvoiceRequest);


		$mycms->sql_update($sqlUpdateInvoiceRequest, false);
	}
}

function UpdateOfferDiscount($slipId, $discountPercentage)
{
	global $cfg, $mycms;

	$slipAmount = '';
	$invoices = invoiceDetailsOfSlip($slipId);
	foreach ($invoices as $k => $invoice) {

		$slipAmount    		 += $invoice['service_unit_price'];
	}
	$discountAmount = $slipAmount * ($discountPercentage / 100);
	updateonDiscount($slipId, $discountAmount);
}

function updateInvoiceDetails($invoiceId, $reqId, $type = "")
{
	global $cfg, $mycms;

	if ($type == "CONFERENCE") {
		$details = getUserDetails($reqId);


		$invoiceDetails['currency']						= getRegistrationCurrency($details['registration_classification_id']);
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['tariff_ref_id']				= getRegistrationTariffId($details['registration_classification_id'], $details['registration_tariff_cutoff_id']);
		$invoiceDetails['service_tariff_cutoff_id']		= $details['registration_tariff_cutoff_id'];
		$dtlsArr										= getUserTypeAndRoomType($details['registration_classification_id']);
		if ($dtlsArr['TYPE'] == 'COMBO') {
			$invoiceDetails['service_type']				= "DELEGATE_RESIDENTIAL_REGISTRATION";
		} else {
			$invoiceDetails['service_type']				= "DELEGATE_CONFERENCE_REGISTRATION";
		}
		$registrationTariffArr							= getAllRegistrationTariffs();
		$invoiceDetails['service_unit_price']			= $registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['AMOUNT'];
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['registration_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['registration_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
	}

	if ($type == "WORKSHOP") {
		$details = getWorkshopDetails($reqId);

		$invoiceDetails['currency']						= getRegistrationCurrency($details['registration_classification_id']);
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];
		$invoiceDetails['tariff_ref_id']				= $details['workshop_tarrif_id'];
		$workshopTariffArr								= workshopTariffDetailsQuerySet();
		$invoiceDetails['service_unit_price']			= $workshopTariffArr[$details['workshop_id']][$details['registration_classification_id']][$details['tariff_cutoff_id']][$invoiceDetails['currency']];
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		$invoiceDetails['service_type']					= "DELEGATE_WORKSHOP_REGISTRATION";

		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}


	if ($type == "ACCOMPANY") {
		$details = getUserDetails($reqId);

		$invoiceDetails['delegate_id']					= $details['refference_delegate_id'];
		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_number']				= generateNextCode("id", _DB_INVOICE_, "ISAR18/17-18/");
		$invoiceDetails['invoice_date']					= date('Y-m-d');
		$invoiceDetails['invoice_request']				= 'GENERAL';
		$invoiceDetails['invoice_mode']					= $details['registration_mode'];
		$invoiceDetails['currency']						= getCurrency($details['registration_classification_id']);
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "ACCOMPANY_CONFERENCE_REGISTRATION";
		$invoiceDetails['tariff_ref_id']				= getRegistrationTariffId($details['registration_classification_id'], $details['registration_tariff_cutoff_id']);
		$invoiceDetails['service_tariff_cutoff_id']		= $details['registration_tariff_cutoff_id'];
		$registrationTariffArr							= getAllRegistrationTariffs();
		$invoiceDetails['service_unit_price']			= $registrationTariffArr[$details['registration_classification_id']][$details['registration_tariff_cutoff_id']]['AMOUNT'];
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['registration_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['registration_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}

	if ($type == "ACCOMMODATION") {
		$details = getAccomodationDetails($reqId);

		$invoiceDetails['delegate_id']					= $details['user_id'];
		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_number']				= generateNextCode("id", _DB_INVOICE_, "ISAR18/17-18/");
		$invoiceDetails['invoice_date']					= date('Y-m-d');
		$invoiceDetails['invoice_request']				= 'GENERAL';
		$invoiceDetails['invoice_mode']					= $details['booking_mode'];
		$invoiceDetails['currency']						= getCurrency(getUserClassificationId($details['user_id']));
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "DELEGATE_ACCOMMODATION_REQUEST";
		$invoiceDetails['tariff_ref_id']				= getAccommodationTariffId($details['package_id'], $details['checkin_date'], $details['checkout_date'], $details['tariff_cutoff_id']);
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];
		$accommodationTariffArr							= accommodationTariffDetailsQuerySet();
		$invoiceDetails['service_unit_price']			= $accommodationTariffArr[$details['package_id']][$details['checkin_date']][$details['checkout_date']][$details['tariff_cutoff_id']][$invoiceDetails['currency']];
		$invoiceDetails['service_consumed_quantity']	= 1;
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}

	if ($type == "DINNER") {
		$details = getDinnerDetails($reqId);
		$detailsClass = getUserDetails($details['delegate_id']);

		$dinnerArray 									= $cfg['DINNER_ARRAY'];
		$invoiceDetails['delegate_id']					= $details['delegate_id'];
		$invoiceDetails['slip_id']						= $mycms->getSession('SLIP_ID');
		$invoiceDetails['invoice_date']					= $date;
		$invoiceDetails['invoice_request']				= $invReq;
		$invoiceDetails['invoice_mode']					= $details['booking_mode'];
		$invoiceDetails['currency']						= getRegistrationCurrency($detailsClass['registration_classification_id']);;
		$invoiceDetails['registration_type']			= 'GENERAL';
		$invoiceDetails['refference_id']				= $reqId;
		$invoiceDetails['service_type']					= "DELEGATE_DINNER_REQUEST";
		$invoiceDetails['tariff_ref_id']				= $details['package_id'];
		$invoiceDetails['service_tariff_cutoff_id']		= $details['tariff_cutoff_id'];

		$dinnerTariffArr						        = getAllDinnerTarrifDetails();

		$invoiceDetails['service_unit_price']			= $dinnerTariffArr[$details['package_id']][$details['tariff_cutoff_id']]['AMOUNT'];
		$invoiceDetails['service_unit_price'];
		if ($invReq == 'COUNTER') {
			$invoiceDetails['service_basic_price']		=  $dinnerTariffArr[$details['package_id']][$details['tariff_cutoff_id']]['AMOUNT'];
		}
		$invoiceDetails['service_consumed_quantity']	= $details['booking_quantity'];
		$invoiceDetails['service_product_price']		= $invoiceDetails['service_unit_price'] * $invoiceDetails['service_consumed_quantity'];

		if ($details['booking_mode'] == "ONLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		} else if ($details['booking_mode'] == "OFFLINE") {
			$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
			$internetHandlingPercentage       = 0.00;
		}

		$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
		$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
		$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
		$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
		$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
		$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
		$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
		$invoiceDetails['payment_status']				= 'UNPAID';
	}

	$sqlUpdate = array();
	$sqlUpdate['QUERY']        		= " UPDATE  " . _DB_INVOICE_ . " 
											  SET `currency` 					 = ?,
												  `refference_id` 	 	 		 = ?,
												  `tariff_ref_id` 		 	 	 = ?, 
												  `service_type` 		 	 	 = ?, 
												  `service_tariff_cutoff_id` 	 = ?, 
												  `service_unit_price` 	 	  	 = ?,
												  `service_consumed_quantity` 	 = ?, 
												  `service_product_price`	 	 = ?,
												  `internet_handling_percentage` = ?,
												  `internet_handling_amount` 	 = ?, 
												  `service_total_price` 	  	 = ?, 
												  `service_grand_price`	 	 	 = ?,
												  `service_roundoff_price`	 	 = ?,
												  `modified_ip`			 	  	 = ?,
												  `modified_sessionId`	 		 = ?,
												  `modified_browser`  	 		 = ?,											  		
												  `modified_dateTime` 	 		 = ?
											WHERE `id` = ?";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'currency',                    'DATA' => $invoiceDetails['currency'],                          'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_id',               'DATA' => $invoiceDetails['refference_id'],                     'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'tariff_ref_id',               'DATA' => $invoiceDetails['tariff_ref_id'],                     'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_type',                'DATA' => $invoiceDetails['service_type'],                      'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_tariff_cutoff_id',    'DATA' => $invoiceDetails['service_tariff_cutoff_id'],          'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_unit_price',          'DATA' => $invoiceDetails['service_unit_price'],                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_consumed_quantity',   'DATA' => $invoiceDetails['service_consumed_quantity'],         'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_product_price',       'DATA' => $invoiceDetails['service_product_price'],             'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'internet_handling_percentage', 'DATA' => $invoiceDetails['internet_handling_percentage'],      'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'internet_handling_amount',    'DATA' => $invoiceDetails['internet_handling_amount'],          'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_total_price',         'DATA' => $invoiceDetails['service_total_price'],               'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_grand_price',         'DATA' => $invoiceDetails['service_grand_price'],               'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_roundoff_price',      'DATA' => $invoiceDetails['service_roundoff_price'],            'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_ip',                 'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_sessionId',          'DATA' => session_id(),                                         'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_browser',            'DATA' => $_SERVER['HTTP_USER_AGENT'],                          'TYP' => 's');

	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_dateTime',           'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                          'DATA' => $invoiceId,                                           'TYP' => 's');

	$mycms->sql_update($sqlUpdate, false);
	gstInsertionInInvoice($invoiceId);

	return $invoiceId;
}

//INSERTING SLIP DETAILS
function insertingSlipDetails($delegateId, $mode = "", $invoice_request, $date = '', $reg_type = 'FRONT',$paymentstatuspre='')
{
	global $cfg, $mycms;
	if ($date == '') {
		$date = date('Y-m-d');
	}
	$invoiceDetails['delegate_id']						= $delegateId;
	$invoiceDetails['invoice_request']					= $invoice_request;
	$invoiceDetails['registration_token']				= $mycms->getSession('REGISTRATION_TOKEN');
	$invoiceDetails['slip_number']						= generateNextCode("id", _DB_SLIP_, "##SLIP" . date('dmy') . "-");
	$invoiceDetails['slip_date']						= $date;
	$invoiceDetails['invoice_mode']						= $mode;
	$invoiceDetails['invoice_type']						= 'GENERAL';
	$invoiceDetails['currency']							= getRegistrationCurrency(getUserClassificationId($delegateId));
	$invoiceDetails['registration_type']				= 'GENERAL';
	$invoiceDetails['reg_type']				            = $reg_type;
	if ($mode == "ONLINE") {
		$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
		$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
	} else if ($mode == "OFFLINE") {
		$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
		$internetHandlingPercentage       = 0.00;
	}
	$invoiceDetails['applicable_tax_percentage']		= $applicableTaxPercentage;
	$invoiceDetails['internet_handling_percentage']		= $internetHandlingPercentage;

	$invoiceDetails['payment_status']					= 'UNPAID';
	if($paymentstatuspre==''){
      $payment_statusPre=$invoiceDetails['payment_status'];

	}else{
     $payment_statusPre=$paymentstatuspre;

	}
	// echo '<pre>'; print_r($invoiceDetails);die;

	$sqlInsertSlipRequest     = array();
	$sqlInsertSlipRequest['QUERY'] 	       = "INSERT INTO " . _DB_SLIP_ . " 
											  SET `delegate_id`	   	 			  = ?,
												  `invoice_request` 		 	  = ?,
												  `registration_token`  		  = ?, 
												  `slip_date` 	 	 		      = ?, 
												  `invoice_mode` 			      = ?, 
												  `invoice_type`	 	 		  = ?,
												  `currency` 					  = ?,
												  `registration_type`	 		  = ?, 
												  `payment_status` 				  = ?,
												  `status` 				 		  = ?,
												  `reg_type` 				 	  = ?,
												  `created_ip`			 		  = ?,
												  `created_sessionId`	 		  = ?,
												  `created_browser`  	 		  = ?,
												  `created_dateTime` 	 		  = ?";



	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'delegate_id',                     'DATA' => $invoiceDetails['delegate_id'],                   'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'invoice_request',                 'DATA' => $invoiceDetails['invoice_request'],               'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'registration_token',              'DATA' => $invoiceDetails['registration_token'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'slip_date',                       'DATA' => $invoiceDetails['slip_date'],                     'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'invoice_mode',                    'DATA' => $invoiceDetails['invoice_mode'],                  'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'invoice_type',                    'DATA' => $invoiceDetails['invoice_type'],                  'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'currency',                        'DATA' => $invoiceDetails['currency'],                      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'registration_type',               'DATA' => $invoiceDetails['registration_type'],             'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_status',                  'DATA' => $payment_statusPre,                               'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'status',                          'DATA' => 'A',                                              'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'reg_type',                        'DATA' => $invoiceDetails['reg_type'],                      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_ip',                      'DATA' => $_SERVER['REMOTE_ADDR'],                          'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_sessionId',               'DATA' => session_id(),                                     'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_browser',                 'DATA' => $_SERVER['HTTP_USER_AGENT'],                      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_dateTime',                'DATA' => date('Y-m-d H:i:s'),                              'TYP' => 's');




	$lastInsertedSlipId                        = $mycms->sql_insert($sqlInsertSlipRequest, false);

	$sqlInvoiceUpdate = array();
	$sqlInvoiceUpdate['QUERY']	        = "UPDATE " . _DB_SLIP_ . "
											SET `slip_number` = ?
										  WHERE `id` = ?";

	$invoice_number = '##SLIP' . date('dmy') . "-" . number_pad($lastInsertedSlipId, 6);
	$sqlInvoiceUpdate['PARAM'][]   = array('FILD' => 'slip_number',   'DATA' => $invoice_number,  'TYP' => 's');
	$sqlInvoiceUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => $lastInsertedSlipId,       'TYP' => 's');

	$mycms->sql_update($sqlInvoiceUpdate, false);

	$mycms->setSession('SLIP_ID', $lastInsertedSlipId);
	return $lastInsertedSlipId;
}

function zeroValueSlipUpdate($slipId)
{
	global $cfg, $mycms;

	$sqlUpdate = array();

	$sqlUpdate['QUERY']	 = "UPDATE " . _DB_SLIP_ . "
								SET `payment_status` = ?
							  WHERE `id` = ?";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'ZERO_VALUE',  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $slipId,  'TYP' => 's');

	$mycms->sql_update($sqlUpdate, false);
}

function complimentarySlipUpdate($slipId)
{
	global $cfg, $mycms;

	$sqlUpdate = array();

	$sqlUpdate['QUERY']	 = "UPDATE " . _DB_SLIP_ . "
								SET `payment_status` = ?
							  WHERE `id` = ?";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $slipId,  'TYP' => 's');

	$mycms->sql_update($sqlUpdate, false);
}

function zeroValueInvoiceUpdate($reqId = "", $type, $slipId = "")
{
	global $cfg, $mycms;

	if ($type == "WORKSHOP") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';
	}

	if ($type == "ACCOMPANY") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';
	}

	if ($type == "ACCOMMODATION") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';
	}

	if ($type == "DINNER") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';
	}

	if ($type == "CONFERENCE") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';

		$invDetails = getInvoiceDetails($reqId);

		$sqlUpdateUserReg = array();
		$sqlUpdateUserReg['QUERY']	   = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `registration_payment_status` = ?
									  WHERE `id` = ?";

		$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' => 'ZERO_VALUE',               'TYP' => 's');
		$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'id',                            'DATA' => $invDetails['refference_id'],  'TYP' => 's');

		$mycms->sql_update($sqlUpdateUserReg, false);
	}
	$sqlUpdate = array();
	$sqlUpdate['QUERY']	                 = "UPDATE " . _DB_INVOICE_ . "
											SET `service_unit_price` 		 	  = ?,
												`service_product_price`  		  = ?, 
												`internet_handling_percentage` 	  = ?, 
												`internet_handling_amount`	 	  = ?,
												`service_total_price` 			  = ?,
												`service_grand_price`	 		  = ?, 
												`service_roundoff_price`	  	  = ?,
												`payment_status`				  = ?,
												`cgst_int_price`				  = ?,
												`sgst_int_price`				  = ?,
												`service_basic_int_price`		  = ?,
												`service_gst_int_price`			  = ?,
												`cgst_price`				 	  = ?,
												`sgst_price`				 	  = ?,
												`service_basic_price`			  = ?,
												`service_gst_price`				  = ?,
												`service_gst_total_price`		  = ?
										  WHERE `id` 							  = ?";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_unit_price',           'DATA' => $invoiceDetails['service_unit_price'],            'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_product_price',        'DATA' => $invoiceDetails['service_product_price'],         'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'internet_handling_percentage', 'DATA' => $invoiceDetails['internet_handling_percentage'],  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'internet_handling_amount',     'DATA' => $invoiceDetails['internet_handling_amount'],      'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_total_price',          'DATA' => $invoiceDetails['service_total_price'],           'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_grand_price',          'DATA' => $invoiceDetails['service_grand_price'],           'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_roundoff_price',       'DATA' => $invoiceDetails['service_roundoff_price'],        'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',               'DATA' => $invoiceDetails['payment_status'],                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'cgst_int_price',               'DATA' => $invoiceDetails['cgst_int_price'],                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'sgst_int_price',               'DATA' => $invoiceDetails['sgst_int_price'],                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_basic_int_price',      'DATA' => $invoiceDetails['service_basic_int_price'],       'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_gst_int_price',        'DATA' => $invoiceDetails['service_gst_int_price'],         'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'cgst_price',                   'DATA' => $invoiceDetails['cgst_price'],                    'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'sgst_price',                   'DATA' => $invoiceDetails['sgst_price'],                    'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_basic_price',          'DATA' => $invoiceDetails['service_basic_price'],           'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_gst_price',            'DATA' => $invoiceDetails['service_gst_price'],             'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'service_gst_total_price',      'DATA' => $invoiceDetails['service_gst_total_price'],       'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                           'DATA' => $reqId,                                           'TYP' => 's');

	$mycms->sql_update($sqlUpdate, false);
}

function zeroValueComplimentaryInvoiceUpdate($reqId = "", $type, $slipId, $remarkDetail)
{
	global $cfg, $mycms;

	if ($type == "WORKSHOP") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		if ($remarkDetail == '') {
			$invoiceDetails['remarks'] = '';
		} else {
			$invoiceDetails['remarks'] = $remarkDetail;
		}

		$invoiceDetails['payment_status']				= 'ZERO_VALUE';

		$invDetails = getInvoiceDetails($reqId);

		$sqlUpdateUserReg['QUERY']	      = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
										SET `payment_status` = 'ZERO_VALUE'
									  WHERE `id` = '" . $invDetails['refference_id'] . "'";

		$mycms->sql_update($sqlUpdateUserReg, false);
	}

	if ($type == "ACCOMPANY") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['remarks']				        = $remarkDetail;
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';

		$invDetails = getInvoiceDetails($reqId);

		$sqlUpdateUserReg['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `registration_payment_status` = 'ZERO_VALUE'
									  WHERE `id` = '" . $invDetails['refference_id'] . "'";
		$mycms->sql_update($sqlUpdateUserReg, false);
	}

	if ($type == "ACCOMMODATION") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['remarks']				        = $remarkDetail;
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';

		$invDetails = getInvoiceDetails($reqId);

		$sqlUpdateUserReg['QUERY']	      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
										SET `payment_status` = 'ZERO_VALUE'
									  WHERE `id` = '" . $invDetails['refference_id'] . "'";
		$mycms->sql_update($sqlUpdateUserReg, false);
	}

	if ($type == "DINNER") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		$invoiceDetails['remarks']				        = $remarkDetail;
		$invoiceDetails['payment_status']				= 'ZERO_VALUE';

		$invDetails = getInvoiceDetails($reqId);

		$sqlUpdateUserReg['QUERY']	      = "UPDATE " . _DB_REQUEST_DINNER_ . "
										SET `payment_status` = 'ZERO_VALUE'
									  WHERE `id` = '" . $invDetails['refference_id'] . "'";
		$mycms->sql_update($sqlUpdateUserReg, false);
	}

	if ($type == "CONFERENCE") {
		$invoiceDetails['service_unit_price']			= '0.00';
		$invoiceDetails['service_product_price']		= '0.00';
		$invoiceDetails['applicable_tax_percentage']	= '0.00';
		$invoiceDetails['applicable_tax_amount']		= '0.00';
		$invoiceDetails['internet_handling_percentage']	= '0.00';
		$invoiceDetails['internet_handling_amount']		= '0.00';
		$invoiceDetails['service_total_price']			= '0.00';
		$invoiceDetails['service_grand_price']			= '0.00';
		$invoiceDetails['service_roundoff_price']		= '0.00';
		$invoiceDetails['cgst_int_price']				= '0.00';
		$invoiceDetails['sgst_int_price']				= '0.00';
		$invoiceDetails['service_basic_int_price']		= '0.00';
		$invoiceDetails['service_gst_int_price']		= '0.00';
		$invoiceDetails['cgst_price']					= '0.00';
		$invoiceDetails['sgst_price']					= '0.00';
		$invoiceDetails['service_basic_price']			= '0.00';
		$invoiceDetails['service_gst_price']			= '0.00';
		$invoiceDetails['service_gst_total_price']		= '0.00';
		if ($remarkDetail == '') {
			$invoiceDetails['remarks'] = '';
		} else {
			$invoiceDetails['remarks'] = $remarkDetail;
		}
		$invoiceDetails['payment_status']				= 'COMPLIMENTARY';

		$invDetails = getInvoiceDetails($reqId);

		$sqlUpdateUserReg['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `registration_payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $invDetails['refference_id'] . "'";
		$mycms->sql_update($sqlUpdateUserReg, false);
	}

	$sqlUpdate['QUERY']	                      = "UPDATE " . _DB_INVOICE_ . "
											SET `service_unit_price` 		 	  = '" . $invoiceDetails['service_unit_price'] . "',
												`service_product_price`  		  = '" . $invoiceDetails['service_product_price'] . "', 
												`applicable_tax_percentage` 	  = '" . $invoiceDetails['applicable_tax_percentage'] . "', 
												`applicable_tax_amount` 	 	  = '" . $invoiceDetails['applicable_tax_amount'] . "', 
												`internet_handling_percentage` 	  = '" . $invoiceDetails['internet_handling_percentage'] . "', 
												`internet_handling_amount`	 	  = '" . $invoiceDetails['internet_handling_amount'] . "',
												`service_total_price` 			  = '" . $invoiceDetails['service_total_price'] . "',
												`service_grand_price`	 		  = '" . $invoiceDetails['service_grand_price'] . "', 
												`service_roundoff_price`	  	  = '" . $invoiceDetails['service_roundoff_price'] . "',
												`payment_status`				  = '" . $invoiceDetails['payment_status'] . "',
												`cgst_int_price`				  = '" . $invoiceDetails['cgst_int_price'] . "',
												`sgst_int_price`				  = '" . $invoiceDetails['sgst_int_price'] . "',
												`service_basic_int_price`		  = '" . $invoiceDetails['service_basic_int_price'] . "',
												`service_gst_int_price`			  = '" . $invoiceDetails['service_gst_int_price'] . "',
												`cgst_price`				 	  = '" . $invoiceDetails['cgst_price'] . "',
												`sgst_price`				 	  = '" . $invoiceDetails['sgst_price'] . "',
												`service_basic_price`			  = '" . $invoiceDetails['service_basic_price'] . "',
												`service_gst_price`				  = '" . $invoiceDetails['service_gst_price'] . "',
												`service_gst_total_price`		  = '" . $invoiceDetails['service_gst_total_price'] . "',
												`remarks`						  ='" . $invoiceDetails['remarks'] . "'
										  WHERE `id` 							  = '" . $reqId . "'";
	$mycms->sql_update($sqlUpdate, false);
}

//INSERTING PAYMENT DETAILS
function insertingPaymentDetails($frontOffice = true)
{
	global $cfg, $mycms;

	$slipId = $_REQUEST['slip_id'];
	$discountAmount = $_REQUEST['discountAmount'];
    if ($_REQUEST['discountAmount'] > 0) {
	updateonDiscount($slipId, $discountAmount);
	}
	$payment_mode              		= addslashes($_REQUEST['payment_mode']);

	$payment_date              		= NULL;
	$card_payment_date         		= NULL;

	// CASH RELATED VARRIABLES
	$cash_deposit_date         		= NULL;

	// UPI Payment Option Added By Weavers start
	// UPI Related Variables
	$upi_date     		= NULL;
	$txn_no			    = "";
	// UPI Payment Option Added By Weavers end

	// CHEQUE RELATED VARRIABLES
	$cheque_number             		= "";
	$cheque_bank_name         		= "";
	$cheque_date               		= NULL;

	// DRAFT RELATED VARRIABLES
	$draft_number              		= "";
	$draft_bank_name          		= "";
	$draft_date                		= NULL;

	// NEFT RELATED VARRIABLES
	$neft_bank_name            		= "";
	$neft_date                 		= NULL;
	$neft_transaction_no       		= "";

	// RTGS RELATED VARRIABLES 
	$rtgs_bank_name            		= "";
	$rtgs_date                 		= NULL;
	$rtgs_transaction_no       		= "";

	// CREDIT RELATED VARIABLES
	$credit_date             		= NULL;
	$exhibitor_code  				= "";
   
	// CASH RELATED ASSIGNMENTS
	if ($payment_mode == "Cash") {
		$cash_deposit_date     		= addslashes($_REQUEST['cash_deposit_date']);
		$payment_status			    = 'UNPAID';
		$cash_document			= addslashes($_REQUEST['cash_document']);
	}

	// UPI Payment Option Added By Weavers start
	// UPI RELATED ASSIGNMENTS
	if ($payment_mode == "Upi") {
		$upi_date     		=  addslashes($_REQUEST['neft_date']);
		$txn_no			    = addslashes($_REQUEST['txn_no']);
		$payment_status			    = 'UNPAID';
		$neft_document			= addslashes($_REQUEST['neft_document']);
		$upi_bank_name        		= addslashes(strtoupper($_REQUEST['neft_bank_name']));

	}
	// UPI Payment Option Added By Weavers end

	// CHEQUE RELATED ASSIGNMENTS
	if ($payment_mode == "Cheque") {
		$cheque_number         		= addslashes($_REQUEST['cheque_number']);
		$cheque_bank_name     		= addslashes(strtoupper($_REQUEST['cheque_drawn_bank']));
		$cheque_date           		= addslashes($_REQUEST['cheque_date']);

		$payment_status			    = 'UNPAID';
	}

	// DRAFT RELATED ASSIGNMENTS
	if ($payment_mode == "Draft") {
		$draft_number          		= addslashes($_REQUEST['draft_number']);
		$draft_bank_name      		= addslashes(strtoupper($_REQUEST['draft_drawn_bank']));
		$draft_date            		= addslashes($_REQUEST['draft_date']);
		$payment_status			    = 'UNPAID';
	}

	// NEFT RELATED ASSIGNMENTS
	if ($payment_mode == "Neft") {
		$neft_bank_name        		= addslashes(strtoupper($_REQUEST['neft_bank_name']));
		$neft_date             		= addslashes($_REQUEST['neft_date']);
		$neft_transaction_no   		= addslashes($_REQUEST['neft_transaction_no']);
		$neft_document			= addslashes($_REQUEST['neft_document']);
		$payment_status			    = 'UNPAID';
	}

	// CARD RELATED ASSIGNMENTS
	if ($payment_mode == "Card") {
		$card_transaction_no        = addslashes($_REQUEST['card_number']);
		$card_payment_date          = addslashes($_REQUEST['card_date']);
		$payment_status			    = 'UNPAID';
	}

	// RTGS RELATED ASSIGNMENTS
	if ($payment_mode == "RTGS") {
		$rtgs_bank_name        		= addslashes(strtoupper($_REQUEST['rtgs_bank_name']));
		$rtgs_date             		= addslashes($_REQUEST['rtgs_date']);
		$rtgs_transaction_no   		= addslashes($_REQUEST['rtgs_transaction_no']);
		$payment_status			    = 'UNPAID';
	}

	if ($payment_mode == "Credit") {

		$credit_date             	= addslashes($_REQUEST['credit_date']);
		$exhibitor_code   			= addslashes($_REQUEST['exhibitor_name']);
		$payment_status			    = 'PAID';
	}
    if($_REQUEST['paymentstatusPre']!=""){
		$payment_status			    = $_REQUEST['paymentstatusPre'];
	}

	$paymentDetails['delegate_id']				= addslashes($_REQUEST['delegate_id']);
	$paymentDetails['slip_id']					= addslashes($_REQUEST['slip_id']);
	// UPI Payment Option Added By Weavers start
	$paymentDetails['upi_date']				= ($upi_date != '') ? $upi_date : NULL;
	$paymentDetails['txn_no']				= ($txn_no!= '') ? $txn_no : NULL;
	$paymentDetails['upi_bank_name']				= ($upi_bank_name!= '') ? $upi_bank_name : NULL;
	// UPI Payment Option Added By Weavers end
	// $paymentDetails['payment_mode']				= $payment_mode;
	// $paymentDetails['payment_date']				= $payment_date;
	// $paymentDetails['cash_deposit_date']		= $cash_deposit_date;
	// $paymentDetails['cash_document']		= $cash_document;
	// $paymentDetails['card_payment_date']		= $card_payment_date;
	// $paymentDetails['card_transaction_no']		= $card_transaction_no;
	// $paymentDetails['cheque_number']			= $cheque_number;
	// $paymentDetails['cheque_date']				= $cheque_date;
	// $paymentDetails['cheque_bank_name']			= $cheque_bank_name;
	// $paymentDetails['draft_number']				= $draft_number;
	// $paymentDetails['draft_date']				= $draft_date;
	// $paymentDetails['draft_bank_name']			= $draft_bank_name;
	// $paymentDetails['neft_bank_name']			= $neft_bank_name;
	// $paymentDetails['neft_transaction_no']		= $neft_transaction_no;
	// $paymentDetails['neft_date']				= $neft_date;
	// $paymentDetails['neft_document']			= $neft_document;
	// $paymentDetails['rtgs_bank_name']			= $rtgs_bank_name;
	// $paymentDetails['rtgs_transaction_no']		= $rtgs_transaction_no;
	// $paymentDetails['rtgs_date']				= $rtgs_date;
	// $paymentDetails['credit_date']				= $credit_date;
	// $paymentDetails['exhibitor_code']			= $exhibitor_code;
	// $paymentDetails['payment_status']			= $payment_status;
	$paymentDetails['payment_mode']        = ($payment_mode != '') ? $payment_mode : NULL;
	$paymentDetails['payment_date']        = ($payment_date != '') ? $payment_date : NULL;
	$paymentDetails['cash_deposit_date']   = ($cash_deposit_date != '') ? $cash_deposit_date : NULL;
	$paymentDetails['cash_document']       = ($cash_document != '') ? $cash_document : NULL;
	$paymentDetails['card_payment_date']   = ($card_payment_date != '') ? $card_payment_date : NULL;
	$paymentDetails['card_transaction_no'] = ($card_transaction_no != '') ? $card_transaction_no : NULL;
	$paymentDetails['cheque_number']       = ($cheque_number != '') ? $cheque_number : NULL;
	$paymentDetails['cheque_date']         = ($cheque_date != '') ? $cheque_date : NULL;
	$paymentDetails['cheque_bank_name']    = ($cheque_bank_name != '') ? $cheque_bank_name : NULL;
	$paymentDetails['draft_number']        = ($draft_number != '') ? $draft_number : NULL;
	$paymentDetails['draft_date']          = ($draft_date != '') ? $draft_date : NULL;
	$paymentDetails['draft_bank_name']     = ($draft_bank_name != '') ? $draft_bank_name : NULL;
	$paymentDetails['neft_bank_name']      = ($neft_bank_name != '') ? $neft_bank_name : NULL;
	$paymentDetails['neft_transaction_no'] = ($neft_transaction_no != '') ? $neft_transaction_no : NULL;
	$paymentDetails['neft_date']           = ($neft_date != '') ? $neft_date : NULL;
	$paymentDetails['neft_document']       = ($neft_document != '') ? $neft_document : NULL;
	$paymentDetails['rtgs_bank_name']      = ($rtgs_bank_name != '') ? $rtgs_bank_name : NULL;
	$paymentDetails['rtgs_transaction_no'] = ($rtgs_transaction_no != '') ? $rtgs_transaction_no : NULL;
	$paymentDetails['rtgs_date']           = ($rtgs_date != '') ? $rtgs_date : NULL;
	$paymentDetails['credit_date']         = ($credit_date != '') ? $credit_date : NULL;
	$paymentDetails['exhibitor_code']      = ($exhibitor_code != '') ? $exhibitor_code : NULL;
	$paymentDetails['payment_status']      = ($payment_status != '') ? $payment_status : NULL;
	$paymentDetails['currency']					= getRegistrationCurrency(getUserClassificationId($paymentDetails['delegate_id']));
	$paymentDetails['amount']					= invoiceAmountOfSlip($paymentDetails['slip_id']);
	$residentialID                              = getUserClassificationId($paymentDetails['delegate_id']);


	$sqlInsertSlipRequest  = array();
	$sqlInsertSlipRequest['QUERY'] 	       = "INSERT INTO " . _DB_PAYMENT_ . " 
												  SET `delegate_id`	   	 			  = ?,
													  `slip_id` 		 	 		  = ?, 
													  `payment_mode` 	  			  = ?, 
													  `payment_date` 	 	 		  = ?, 
													  `cash_deposit_date` 			  = ?, 
													  `cash_document` 				  = ?, 
													  `card_payment_date`	 	 	  = ?,
													  `card_transaction_no` 		  = ?,
													  `cheque_number`	 		  	  = ?, 
													  `cheque_date`	 				  = ?,
													  `cheque_bank_name`  			  = ?, 
													  `draft_number` 				  = ?,
													  `draft_date` 	 	 		      = ?, 
													  `draft_bank_name` 			  = ?, 
													  `neft_bank_name`	 	 		  = ?,
													  `neft_transaction_no` 		  = ?,
													  `neft_date`	 		 		  = ?, 
													  `neft_document`	 			  = ?, 
													  `rtgs_bank_name`	 			  = ?,
													  `rtgs_transaction_no`  		  = ?, 
													  `rtgs_date` 					  = ?,
													  `credit_date`					  =	?,
													  `upi_date`	 		  		  = ?,
													  `txn_no` 			 	 		  = ?,
													  `exhibitor_code`				  = ?,
													  `currency` 	 	 		      = ?, 
													  `amount` 	 	 		      	  = ?, 
													  `payment_status` 		 		  = ?,
													  `upi_bank_name` 		 		  = ?,
													  `status` 				 		  = ?,
													  `created_ip`			 		  = ?,
													  `created_sessionId`	 		  = ?,
													  `created_browser`  	 		  = ?,
													  `created_dateTime` 	 		  = ?";

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'delegate_id',                'DATA' => $paymentDetails['delegate_id'],           'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'slip_id',                    'DATA' => $paymentDetails['slip_id'],               'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_mode',               'DATA' => $paymentDetails['payment_mode'],          'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_date',               'DATA' => $paymentDetails['payment_date'],          'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cash_deposit_date',          'DATA' => $paymentDetails['cash_deposit_date'],     'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cash_document',         	   'DATA' => $paymentDetails['cash_document'],     'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'card_payment_date',          'DATA' => $paymentDetails['card_payment_date'],     'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'card_transaction_no',        'DATA' => $paymentDetails['card_transaction_no'],   'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_number',              'DATA' => $paymentDetails['cheque_number'],         'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_date',                'DATA' => $paymentDetails['cheque_date'],           'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_bank_name',           'DATA' => $paymentDetails['cheque_bank_name'],      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_number',               'DATA' => $paymentDetails['draft_number'],          'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_date',                 'DATA' => $paymentDetails['draft_date'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_bank_name',            'DATA' => $paymentDetails['draft_bank_name'],       'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_bank_name',             'DATA' => $paymentDetails['neft_bank_name'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_transaction_no',        'DATA' => $paymentDetails['neft_transaction_no'],   'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_date',                  'DATA' => $paymentDetails['neft_date'],             'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_document',       	   'DATA' => $paymentDetails['neft_document'],      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_bank_name',             'DATA' => $paymentDetails['rtgs_bank_name'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_transaction_no',        'DATA' => $paymentDetails['rtgs_transaction_no'],   'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_date',                  'DATA' => $paymentDetails['rtgs_date'],             'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'credit_date',                'DATA' => $paymentDetails['credit_date'],           'TYP' => 's');
	// UPI Payment Option Added By Weavers start
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'upi_date',                  'DATA' => $paymentDetails['upi_date'],             'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'txn_no',                     'DATA' => $paymentDetails['txn_no'],             'TYP' => 's');
	// UPI Payment Option Added By Weavers end
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'exhibitor_code',             'DATA' => $paymentDetails['exhibitor_code'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'currency',                   'DATA' => $paymentDetails['currency'],              'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'amount',                     'DATA' => $paymentDetails['amount'],                'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_status',             'DATA' => $paymentDetails['payment_status'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'upi_bank_name',             'DATA' => $paymentDetails['upi_bank_name'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'status',                     'DATA' => 'A',                                       'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_ip',                 'DATA' => $_SERVER['REMOTE_ADDR'],                   'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_sessionId',          'DATA' => session_id(),                              'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_browser',            'DATA' => $_SERVER['HTTP_USER_AGENT'],               'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_dateTime',           'DATA' => date('Y-m-d H:i:s'),                       'TYP' => 's');

	$paymentId = $mycms->sql_insert($sqlInsertSlipRequest, false);

	if ($frontOffice) {

		offline_registration_acknowledgement_message($paymentDetails['delegate_id'], $paymentDetails['slip_id'], $paymentId, 'SEND', 'FRONT');
	}

	return $paymentId;
}

function counterInsertingPaymentDetails()
{
	global $cfg, $mycms;

	$payment_mode              		= addslashes($_REQUEST['payment_mode']);

	$payment_date              		= "0000-00-00";
	$card_payment_date         		= "0000-00-00";

	// CASH RELATED VARRIABLES
	$cash_deposit_date         		= "0000-00-00";

	// CHEQUE RELATED VARRIABLES
	$cheque_number             		= "";
	$cheque_bank_name         		= "";
	$cheque_date               		= "0000-00-00";

	// DRAFT RELATED VARRIABLES
	$draft_number              		= "";
	$draft_bank_name          		= "";
	$draft_date                		= "0000-00-00";

	// NEFT RELATED VARRIABLES
	$neft_bank_name            		= "";
	$neft_date                 		= "0000-00-00";
	$neft_transaction_no       		= "";

	// RTGS RELATED VARRIABLES 
	$rtgs_bank_name            		= "";
	$rtgs_date                 		= "0000-00-00";
	$rtgs_transaction_no       		= "";

	// CASH RELATED ASSIGNMENTS
	if ($payment_mode == "Cash") {
		$cash_deposit_date     		= addslashes($_REQUEST['cash_deposit_date']);
	}

	// CHEQUE RELATED ASSIGNMENTS
	if ($payment_mode == "Cheque") {
		$cheque_number         		= addslashes($_REQUEST['cheque_number']);
		$cheque_bank_name     		= addslashes(strtoupper($_REQUEST['cheque_drawn_bank']));
		$cheque_date           		= addslashes($_REQUEST['cheque_date']);
	}

	// DRAFT RELATED ASSIGNMENTS
	if ($payment_mode == "Draft") {
		$draft_number          		= addslashes($_REQUEST['draft_number']);
		$draft_bank_name      		= addslashes(strtoupper($_REQUEST['draft_drawn_bank']));
		$draft_date            		= addslashes($_REQUEST['draft_date']);
	}

	// NEFT RELATED ASSIGNMENTS
	if ($payment_mode == "NEFT") {
		$neft_bank_name        		= addslashes(strtoupper($_REQUEST['neft_bank_name']));
		$neft_date             		= addslashes($_REQUEST['neft_date']);
		$neft_transaction_no   		= addslashes($_REQUEST['neft_transaction_no']);
	}

	// RTGS RELATED ASSIGNMENTS
	if ($payment_mode == "RTGS") {
		$rtgs_bank_name        		= addslashes(strtoupper($_REQUEST['rtgs_bank_name']));
		$rtgs_date             		= addslashes($_REQUEST['rtgs_date']);
		$rtgs_transaction_no   		= addslashes($_REQUEST['rtgs_transaction_no']);
	}
	// CARD RELATED ASSIGNMENTS

	if ($payment_mode == "CARD") {
		$card_transaction_no          		= addslashes($_REQUEST['card_number']);
		$card_payment_date            		= addslashes($_REQUEST['card_date']);
	}
	$paymentDetails['delegate_id']				= addslashes($_REQUEST['delegate_id']);
	$paymentDetails['slip_id']					= addslashes($_REQUEST['slip_id']);

	$paymentDetails['payment_mode']				= $payment_mode;
	$paymentDetails['payment_date']				= $payment_date;
	$paymentDetails['cash_deposit_date']		= $cash_deposit_date;
	$paymentDetails['card_payment_date']		= $card_payment_date;
	$paymentDetails['card_transaction_no']		= $card_transaction_no;
	$paymentDetails['cheque_number']			= $cheque_number;
	$paymentDetails['cheque_date']				= $cheque_date;
	$paymentDetails['cheque_bank_name']			= $cheque_bank_name;
	$paymentDetails['draft_number']				= $draft_number;
	$paymentDetails['draft_date']				= $draft_date;
	$paymentDetails['draft_bank_name']			= $draft_bank_name;
	$paymentDetails['neft_bank_name']			= $neft_bank_name;
	$paymentDetails['neft_transaction_no']		= $neft_transaction_no;
	$paymentDetails['neft_date']				= $neft_date;
	$paymentDetails['rtgs_bank_name']			= $rtgs_bank_name;
	$paymentDetails['rtgs_transaction_no']		= $rtgs_transaction_no;
	$paymentDetails['rtgs_date']				= $rtgs_date;
	$paymentDetails['payment_status']			= 'UNPAID';
	$paymentDetails['currency']					= getRegistrationCurrency(getUserClassificationId($paymentDetails['delegate_id']));
	$totalAmount = invoiceAmountOfSlip($paymentDetails['slip_id']);
	$withdiscount = (($totalAmount * $cfg['DISCOUNT']) / 100);
	$paymentDetails['amount']					= $withdiscount;

	$sqlInsertSlipRequest['QUERY'] 	       = "INSERT INTO " . _DB_PAYMENT_ . " 
											  SET `delegate_id`	   	 			  = '" . $paymentDetails['delegate_id'] . "',
												  `slip_id` 		 	 		  = '" . $paymentDetails['slip_id'] . "', 
												  `payment_mode` 	  			  = '" . $paymentDetails['payment_mode'] . "', 
												  `payment_date` 	 	 		  = '" . $paymentDetails['payment_date'] . "', 
												  `cash_deposit_date` 			  = '" . $paymentDetails['cash_deposit_date'] . "', 
												  `card_payment_date`	 	 	  = '" . $paymentDetails['card_payment_date'] . "',
												  `card_transaction_no` 		  = '" . $paymentDetails['card_transaction_no'] . "',
												  `cheque_number`	 		  	  = '" . $paymentDetails['cheque_number'] . "', 
												  `cheque_date`	 				  = '" . $paymentDetails['cheque_date'] . "',
												  `cheque_bank_name`  			  = '" . $paymentDetails['cheque_bank_name'] . "', 
												  `draft_number` 				  = '" . $paymentDetails['draft_number'] . "',
												  `draft_date` 	 	 		      = '" . $paymentDetails['draft_date'] . "', 
												  `draft_bank_name` 			  = '" . $paymentDetails['draft_bank_name'] . "', 
												  `neft_bank_name`	 	 		  = '" . $paymentDetails['neft_bank_name'] . "',
												  `neft_transaction_no` 		  = '" . $paymentDetails['neft_transaction_no'] . "',
												  `neft_date`	 		 		  = '" . $paymentDetails['neft_date'] . "', 
												  `rtgs_bank_name`	 			  = '" . $paymentDetails['rtgs_bank_name'] . "',
												  `rtgs_transaction_no`  		  = '" . $paymentDetails['rtgs_transaction_no'] . "', 
												  `rtgs_date` 					  = '" . $paymentDetails['rtgs_date'] . "',
												  `currency` 	 	 		      = '" . $paymentDetails['currency'] . "', 
												  `amount` 	 	 		      	  = '" . $paymentDetails['amount'] . "', 
												  `payment_status` 		 		  = '" . $paymentDetails['payment_status'] . "',
												   
												  `status` 				 		  = 'A',
												  `created_ip`			 		  = '" . $_SERVER['REMOTE_ADDR'] . "',
												  `created_sessionId`	 		  = '" . session_id() . "',
												  `created_browser`  	 		  = '" . $_SERVER['HTTP_USER_AGENT'] . "',
												  `created_dateTime` 	 		  = '" . date('Y-m-d H:i:s') . "'";

	$paymentId = $mycms->sql_insert($sqlInsertSlipRequest, false);
	insertDiscountDetils($paymentDetails['delegate_id'], $paymentDetails['slip_id'], $withdiscount, $paymentId);
	// offline_registration_acknowledgement_message($paymentDetails['delegate_id'], $paymentDetails['slip_id'],$paymentId, 'SEND');

	return $paymentId;
}

function insertDiscountDetils($delegateId, $slipId, $discountAmount, $paymentId)
{
	global $cfg, $mycms;

	if ($discountAmount == "") {
		$discountAmount = 0;
	}



	$totalAmount  = array();
	$hasGST		  = 'N';
	$discountMode = 'PERCENT';

	$invoices = invoiceDetailsOfSlip($slipId);
	foreach ($invoices as $k => $invoice) {
		if ($invoice['has_gst'] == 'Y') {
			$hasGST = 'Y';
			$discountMode = 'FLAT';
		}
	}

	foreach ($invoices as $k => $invoice) {
		$totalAmount['service_unit_price']     		 += $invoice['service_unit_price'];
		$totalAmount['service_product_price']  		 += $invoice['service_product_price'];
		$totalAmount['service_total_price']    		 += $invoice['service_total_price'];
		$totalAmount['service_grand_price']    		 += $invoice['service_grand_price'];
		$totalAmount['service_roundoff_price'] 		 += $invoice['service_roundoff_price'];
		if ($hasGST == 'Y') {
			$totalAmount['service_basic_price']    		 += $invoice['service_basic_price'];
			$totalAmount['service_gst_price']      		 += $invoice['service_gst_price'];
			$totalAmount['service_gst_total_price']      += $invoice['service_gst_total_price'];
		}
	}

	if ($hasGST == 'Y') {
		$newTotal_service_basic_price = $totalAmount['service_basic_price'] - $discountAmount;
		$averageTaxRate				  = (($totalAmount['service_grand_price'] - $totalAmount['service_basic_price']) / $totalAmount['service_basic_price']);
		$newFinalAmount				  = $newTotal_service_basic_price + $newTotal_service_basic_price * $averageTaxRate;

		$dAmt						  = ($totalAmount['service_roundoff_price'] - $newFinalAmount);
		$discountAmount 			  = $dAmt;
	}

	$percentage  = (floatval($discountAmount) / floatval($totalAmount['service_roundoff_price'])) * 100;

	$sqlInsertDiscount['QUERY'] 	       = "INSERT INTO " . _DB_DISCOUNT_ . " 
											  SET `delegate_id`	   	 			  = '" . $delegateId . "',
												  `slip_id` 		 	 		  = '" . $slipId . "', 
												  `discountMode`				  = '" . $discountMode . "',
												  `hasGST`						  = '" . $hasGST . "',
												  `percentage`					  = '" . $percentage . "',
												  `payment_id` 	  			      = '" . $paymentId . "', 
												  `discount_amount` 	 	 	  = '" . $discountAmount . "', 
												  `status` 				 		  = 'A',
												  `created_ip`			 		  = '" . $_SERVER['REMOTE_ADDR'] . "',
												  `created_sessionId`	 		  = '" . session_id() . "',
												  `created_browser`  	 		  = '" . $_SERVER['HTTP_USER_AGENT'] . "',
												  `created_dateTime` 	 		  = '" . date('Y-m-d H:i:s') . "'";

	$discountId = $mycms->sql_insert($sqlInsertDiscount, false);
}

function insertingPartialPaymentDetails($slip_id = '0')
{
	global $cfg, $mycms;

	// echo '<pre>'; print_r($_REQUEST);

	$payment_mode              		= addslashes($_REQUEST['payment_mode']);

	$payment_date              		= "0000-00-00";


	// CASH RELATED VARRIABLES
	$cash_deposit_date         		= "0000-00-00";

	// CHEQUE RELATED VARRIABLES
	$cheque_number             		= "";
	$cheque_bank_name         		= "";
	$cheque_date               		= "0000-00-00";

	// DRAFT RELATED VARRIABLES
	$draft_number              		= "";
	$draft_bank_name          		= "";
	$draft_date                		= "0000-00-00";

	// NEFT RELATED VARRIABLES
	$neft_bank_name            		= "";
	$neft_date                 		= "0000-00-00";
	$neft_transaction_no       		= "";

	// RTGS RELATED VARRIABLES 
	$rtgs_bank_name            		= "";
	$rtgs_date                 		= "0000-00-00";
	$rtgs_transaction_no       		= "";

	// CARD RELATED VARRIABLES 
	$remarks            			= "";
	$card_date                 		= "0000-00-00";
	$card_number       				= "";

	// CREDIT RELATED VARRIABLES 
	$exhibitor_code           		= "";
	$credit_date                 		= "0000-00-00";

	// CASH RELATED ASSIGNMENTS
	if ($payment_mode == "Cash") {
		$cash_deposit_date     		= addslashes($_REQUEST['cash_deposit_date']);
	}

	// CHEQUE RELATED ASSIGNMENTS
	if ($payment_mode == "Cheque") {
		$cheque_number         		= addslashes($_REQUEST['cheque_number']);
		$cheque_bank_name     		= addslashes(strtoupper($_REQUEST['cheque_drawn_bank']));
		$cheque_date           		= addslashes($_REQUEST['cheque_date']);
	}

	// DRAFT RELATED ASSIGNMENTS
	if ($payment_mode == "Draft") {
		$draft_number          		= addslashes($_REQUEST['draft_number']);
		$draft_bank_name      		= addslashes(strtoupper($_REQUEST['draft_drawn_bank']));
		$draft_date            		= addslashes($_REQUEST['draft_date']);
	}

	// NEFT RELATED ASSIGNMENTS
	if ($payment_mode == "NEFT") {
		$neft_bank_name        		= addslashes(strtoupper($_REQUEST['neft_bank_name']));
		$neft_date             		= addslashes($_REQUEST['neft_date']);
		$neft_transaction_no   		= addslashes($_REQUEST['neft_transaction_no']);
	}

	// RTGS RELATED ASSIGNMENTS
	if ($payment_mode == "RTGS") {
		$rtgs_bank_name        		= addslashes(strtoupper($_REQUEST['rtgs_bank_name']));
		$rtgs_date             		= addslashes($_REQUEST['rtgs_date']);
		$rtgs_transaction_no   		= addslashes($_REQUEST['rtgs_transaction_no']);
	}

	// CARD RELATED ASSIGNMENTS
	if ($payment_mode == "CARD") {
		$remarks        			= addslashes(strtoupper($_REQUEST['remarks']));
		$card_date             		= addslashes($_REQUEST['card_date']);
		$card_number   				= addslashes($_REQUEST['card_number']);
	}

	// CREDIT RELATED ASSIGNMENTS
	if ($payment_mode == "Credit") {
		$exhibitor_code        		= addslashes(strtoupper($_REQUEST['exhibitor_name']));
		$credit_date             	= addslashes($_REQUEST['credit_date']);
	}

	if ($payment_mode == "UPI") {
		$txn_no        		 = addslashes($_REQUEST['txn_no'][0]);
		$upi_bank_name       = addslashes($_REQUEST['upi_bank_name'][0]);
		$upi_date            = addslashes($_REQUEST['upi_date'][0]);
	}

	$paymentDetails['delegate_id']				= addslashes($_REQUEST['delegate_id']);
	$paymentDetails['slip_id']					= $slip_id;
	$paymentDetails['payment_mode']				= $payment_mode;
	$paymentDetails['payment_date']				= $payment_date;
	$paymentDetails['cash_deposit_date']		= $cash_deposit_date;
	$paymentDetails['cheque_number']			= $cheque_number;
	$paymentDetails['cheque_date']				= $cheque_date;
	$paymentDetails['cheque_bank_name']			= $cheque_bank_name;
	$paymentDetails['draft_number']				= $draft_number;
	$paymentDetails['draft_date']				= $draft_date;
	$paymentDetails['draft_bank_name']			= $draft_bank_name;
	$paymentDetails['neft_bank_name']			= $neft_bank_name;
	$paymentDetails['neft_transaction_no']		= $neft_transaction_no;
	$paymentDetails['neft_date']				= $neft_date;
	$paymentDetails['rtgs_bank_name']			= $rtgs_bank_name;
	$paymentDetails['rtgs_transaction_no']		= $rtgs_transaction_no;
	$paymentDetails['rtgs_date']				= $rtgs_date;
	$paymentDetails['card_date']				= $card_date;
	$paymentDetails['card_number']				= $card_number;
	$paymentDetails['remarks']					= $remarks;
	$paymentDetails['exhibitor_code']			= $exhibitor_code;
	$paymentDetails['credit_date']				= $credit_date;

	$paymentDetails['txn_no']				= $txn_no;
	$paymentDetails['upi_bank_name']		= $upi_bank_name;
	$paymentDetails['upi_date']				= $upi_date;

	$paymentDetails['payment_status']			= 'UNPAID';
	$paymentDetails['currency']					= getRegistrationCurrency(getUserClassificationId($paymentDetails['delegate_id']));
	$paymentDetails['amount']					= invoiceAmountOfSlip($paymentDetails['slip_id']);



	$sqlInsertSlipRequest['QUERY'] 	       = "INSERT INTO " . _DB_PAYMENT_ . " 
											  SET `delegate_id`	   	 			  = ?,
												  `slip_id` 		 	 		  = ?, 
												  `payment_mode` 	  			  = ?, 
												  `payment_date` 	 	 		  = ?, 
												  `cash_deposit_date` 			  = ?, 
												  
												  `cheque_number`	 		  	  = ?, 
												  `cheque_date`	 				  = ?,
												  `cheque_bank_name`  			  = ?, 
												  `draft_number` 				  = ?,
												  `draft_date` 	 	 		      = ?, 
												  
												  
												  `draft_bank_name` 			  = ?, 
												  `neft_bank_name`	 	 		  = ?,
												  `neft_transaction_no` 		  = ?,
												  `neft_date`	 		 		  = ?, 
												  `rtgs_bank_name`	 			  = ?,
												  
												  `rtgs_transaction_no`  		  = ?, 
												  `rtgs_date` 					  = ?,
												  `card_payment_date` 			  = ?,
												  `card_transaction_no` 		  = ?,
												  `remarks` 					  = ?,
												  
												  
												  `currency` 	 	 		      = ?, 
												  `amount` 	 	 		      	  = ?, 
												  `payment_status` 		 		  = ?,
												  `exhibitor_code` 		 		  = ?,
												  `credit_date` 		 		  = ?,

												  `txn_no` 		 				  = ?,
												  `upi_bank_name` 		 		  = ?,
												  `upi_date` 		 			  = ?,
												   
												  `status` 				 		  = ?,
												  `created_ip`			 		  = ?,
												  `created_sessionId`	 		  = ?,
												  `created_browser`  	 		  = ?,
												  `created_dateTime` 	 		  = ?";

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'delegate_id',           'DATA' => $paymentDetails['delegate_id'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'slip_id',           	  'DATA' => $paymentDetails['slip_id'],           	  'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_mode',          'DATA' => $paymentDetails['payment_mode'],           'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_date',          'DATA' => $paymentDetails['payment_date'],           'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cash_deposit_date',     'DATA' => $paymentDetails['cash_deposit_date'],      'TYP' => 's');

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_number',         'DATA' => $paymentDetails['cheque_number'],           'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_date',           'DATA' => $paymentDetails['cheque_date'],             'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_bank_name',      'DATA' => $paymentDetails['cheque_bank_name'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_number',          'DATA' => $paymentDetails['draft_number'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_date',     		  'DATA' => $paymentDetails['draft_date'],      		   'TYP' => 's');

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_bank_name',          'DATA' => $paymentDetails['draft_bank_name'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_bank_name',           'DATA' => $paymentDetails['neft_bank_name'],             'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_transaction_no',      'DATA' => $paymentDetails['neft_transaction_no'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_date',          		 'DATA' => $paymentDetails['neft_date'],            		 'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_bank_name',     		 'DATA' => $paymentDetails['rtgs_bank_name'],      		 'TYP' => 's');

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_transaction_no',          'DATA' => $paymentDetails['rtgs_transaction_no'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_date',           		 'DATA' => $paymentDetails['rtgs_date'],             		 'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'card_payment_date',      		 'DATA' => $paymentDetails['card_date'],        				 'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'card_transaction_no',          'DATA' => $paymentDetails['card_number'],            		 'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'remarks',     		 		 'DATA' => $paymentDetails['remarks'],      		 			 'TYP' => 's');

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'currency',          'DATA' => $paymentDetails['currency'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'amount',            'DATA' => $paymentDetails['amount'],              'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_status',    'DATA' => $paymentDetails['payment_status'],      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'exhibitor_code',    'DATA' => $paymentDetails['exhibitor_code'],      'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'credit_date',       'DATA' => $paymentDetails['credit_date'],         'TYP' => 's');

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'txn_no',       'DATA' => $paymentDetails['txn_no'],         'TYP' => 's');

	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'upi_bank_name',       'DATA' => $paymentDetails['upi_bank_name'],         'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'upi_date',       'DATA' => $paymentDetails['upi_date'],         'TYP' => 's');


	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'status',          		'DATA' => 'A',           					 'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_ip',              'DATA' => $_SERVER['REMOTE_ADDR'],            'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_sessionId',       'DATA' => session_id(),      				 'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_browser',         'DATA' => $_SERVER['HTTP_USER_AGENT'],        'TYP' => 's');
	$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_dateTime',        'DATA' => date('Y-m-d H:i:s'),                'TYP' => 's');
	$paymentId = $mycms->sql_insert($sqlInsertSlipRequest, false);


	//offline_conference_payment_acknowledgement_message($paymentDetails['delegate_id'], $paymentDetails['slip_id'], $paymentId, 'SEND');
	return $paymentId;
}

function updatePaymentDetailsProcess()
{
	global $cfg, $mycms, $loggedUserId;


	if (!$loggedUserId || $loggedUserId == "") {
		$loggedUserId	= 0;
	}

	$payment_mode              		= addslashes($_REQUEST['payment_mode']);

	$payment_date              		= "0000-00-00";
	$card_payment_date         		= "0000-00-00";

	// CASH RELATED VARRIABLES
	$cash_deposit_date         		= "0000-00-00";

	// CHEQUE RELATED VARRIABLES
	$cheque_number             		= "";
	$cheque_bank_name         		= "";
	$cheque_date               		= "0000-00-00";

	// DRAFT RELATED VARRIABLES
	$draft_number              		= "";
	$draft_bank_name          		= "";
	$draft_date                		= "0000-00-00";

	// NEFT RELATED VARRIABLES
	$neft_bank_name            		= "";
	$neft_date                 		= "0000-00-00";
	$neft_transaction_no       		= "";

	// RTGS RELATED VARRIABLES 
	$rtgs_bank_name            		= "";
	$rtgs_date                 		= "0000-00-00";
	$rtgs_transaction_no       		= "";

	// CASH RELATED ASSIGNMENTS
	if ($payment_mode == "Cash") {
		$cash_deposit_date     		= addslashes($_REQUEST['cash_deposit_date']);
	}

	// CHEQUE RELATED ASSIGNMENTS
	if ($payment_mode == "Cheque") {
		$cheque_number         		= addslashes($_REQUEST['cheque_number']);
		$cheque_bank_name     		= addslashes(strtoupper($_REQUEST['cheque_drawn_bank']));
		$cheque_date           		= addslashes($_REQUEST['cheque_date']);
	}

	// DRAFT RELATED ASSIGNMENTS
	if ($payment_mode == "Draft") {
		$draft_number          		= addslashes($_REQUEST['draft_number']);
		$draft_bank_name      		= addslashes(strtoupper($_REQUEST['draft_drawn_bank']));
		$draft_date            		= addslashes($_REQUEST['draft_date']);
	}

	// NEFT RELATED ASSIGNMENTS
	if ($payment_mode == "NEFT") {
		$neft_bank_name        		= addslashes(strtoupper($_REQUEST['neft_bank_name']));
		$neft_date             		= addslashes($_REQUEST['neft_date']);
		$neft_transaction_no   		= addslashes($_REQUEST['neft_transaction_no']);
	}

	// RTGS RELATED ASSIGNMENTS
	if ($payment_mode == "RTGS") {
		$rtgs_bank_name        		= addslashes(strtoupper($_REQUEST['rtgs_bank_name']));
		$rtgs_date             		= addslashes($_REQUEST['rtgs_date']);
		$rtgs_transaction_no   		= addslashes($_REQUEST['rtgs_transaction_no']);
	}

	$paymentDetails['delegate_id']				= addslashes($_REQUEST['delegateId']);
	$paymentDetails['slip_id']					= addslashes($_REQUEST['slipId']);
	$paymentDetails['id']						= addslashes($_REQUEST['paymentId']);

	$paymentDetails['payment_mode']				= $payment_mode;
	$paymentDetails['payment_date']				= $payment_date;
	$paymentDetails['cash_deposit_date']		= $cash_deposit_date;
	$paymentDetails['card_payment_date']		= $card_payment_date;
	$paymentDetails['card_transaction_no']		= $card_transaction_no;
	$paymentDetails['cheque_number']			= $cheque_number;
	$paymentDetails['cheque_date']				= $cheque_date;
	$paymentDetails['cheque_bank_name']			= $cheque_bank_name;
	$paymentDetails['draft_number']				= $draft_number;
	$paymentDetails['draft_date']				= $draft_date;
	$paymentDetails['draft_bank_name']			= $draft_bank_name;
	$paymentDetails['neft_bank_name']			= $neft_bank_name;
	$paymentDetails['neft_transaction_no']		= $neft_transaction_no;
	$paymentDetails['neft_date']				= $neft_date;
	$paymentDetails['rtgs_bank_name']			= $rtgs_bank_name;
	$paymentDetails['rtgs_transaction_no']		= $rtgs_transaction_no;
	$paymentDetails['rtgs_date']				= $rtgs_date;
	$paymentDetails['amount']					= invoiceAmountOfSlip($paymentDetails['slip_id']);


	$sql['QUERY']	= "SELECT * FROM " . _DB_PAYMENT_ . " WHERE `id` = '" . $paymentDetails['id'] . "'";
	$res	= $mycms->sql_select($sql);
	$data	= $res[0];
	$rawData['delegate_id']			= $paymentDetails['delegate_id'];
	$rawData['slip_id']				= $paymentDetails['slip_id'];
	$rawData['payment_id']			= $paymentDetails['id'];
	$rawData['data']				= serialize($data);
	$rawData['created_by']			= $loggedUserId;
	$rawData['created_ip']			= $_SERVER['REMOTE_ADDR'];
	$rawData['created_sessionId']	= session_id();
	$rawData['created_browser']		= $_SERVER['HTTP_USER_AGENT'];
	$rawData['created_dateTime']	= date('Y-m-d H:i:s');

	insertRawData(_DB_PAYMENT_DISCARD_HISTORY_, $rawData);


	$sqlUpdatePaymentRequest['QUERY']   = "UPDATE " . _DB_PAYMENT_ . "
									 SET  `payment_mode` 	  			  = '" . $paymentDetails['payment_mode'] . "', 
										  `payment_date` 	 	 		  = '" . $paymentDetails['payment_date'] . "', 
										  `cash_deposit_date` 			  = '" . $paymentDetails['cash_deposit_date'] . "', 
										  `card_payment_date`	 	 	  = '" . $paymentDetails['card_payment_date'] . "',
										  `card_transaction_no` 		  = '" . $paymentDetails['card_transaction_no'] . "',
										  `cheque_number`	 		  	  = '" . $paymentDetails['cheque_number'] . "', 
										  `cheque_date`	 				  = '" . $paymentDetails['cheque_date'] . "',
										  `cheque_bank_name`  			  = '" . $paymentDetails['cheque_bank_name'] . "', 
										  `draft_number` 				  = '" . $paymentDetails['draft_number'] . "',
										  `draft_date` 	 	 		      = '" . $paymentDetails['draft_date'] . "', 
										  `draft_bank_name` 			  = '" . $paymentDetails['draft_bank_name'] . "', 
										  `neft_bank_name`	 	 		  = '" . $paymentDetails['neft_bank_name'] . "',
										  `neft_transaction_no` 		  = '" . $paymentDetails['neft_transaction_no'] . "',
										  `neft_date`	 		 		  = '" . $paymentDetails['neft_date'] . "', 
										  `rtgs_bank_name`	 			  = '" . $paymentDetails['rtgs_bank_name'] . "',
										  `rtgs_transaction_no`  		  = '" . $paymentDetails['rtgs_transaction_no'] . "', 
										  `rtgs_date` 					  = '" . $paymentDetails['rtgs_date'] . "',
										  `amount` 	 	 		      	  = '" . $paymentDetails['amount'] . "',
										  `status` 				 		  = 'A',
										  `modified_by`			 	  	  = '" . $loggedUserId . "',
										  `modified_ip`			 	  	  = '" . $_SERVER['REMOTE_ADDR'] . "',
										  `modified_sessionId`	 		  = '" . session_id() . "',
										  `modified_browser`  	 		  = '" . $_SERVER['HTTP_USER_AGENT'] . "',
										  `modified_dateTime` 	 		  = '" . date('Y-m-d H:i:s') . "'
									WHERE `delegate_id`				  	  = '" . $paymentDetails['delegate_id'] . "'
									  AND `slip_id`					  	  = '" . $paymentDetails['slip_id'] . "'
									  AND `id`						  	  = '" . $paymentDetails['id'] . "'";


	$mycms->sql_update($sqlUpdatePaymentRequest, false);
}

function getPrintInvoiceContent($delegateId, $invoiceId)
{
	global $cfg, $mycms;
	$invoiceDetails  = getInvoiceDetails($invoiceId, true);
	$delegateDetails = getUserDetails($delegateId);

?>
	<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td align="center" style="border-collapse:collapse;">
					<img src="<?= _BASE_URL_ ?>images/header20191011.jpg" width="790px" />
				</td>
			</tr>
			<tr>
				<td align="center" height="820px" style="border-collapse:collapse;" valign="top">
					<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
						INVOICE/RECEIPT
					</div>
					<table width="100%" cellpadding="1" style="font-size:13px;">
						<tr>
							<td width="18%"><strong>Registration Details</strong></td>
							<td width="32%"></td>
							<td width="18%"></td>
							<td width="32%"></td>
						</tr>
						<tr>
							<td>Name:</td>
							<td><?= $delegateDetails['user_full_name'] ?></td>
							<td width="18%">Date:</td>
							<td width="32%"><?= $invoiceDetails['invoice_date'] ?></td>
						</tr>
						<tr>
							<td>Email Id:</td>
							<td><?= $delegateDetails['user_email_id'] ?></td>
							<td>Invoice No:</td>
							<td><?= $invoiceDetails['invoice_number'] ?></td>
						</tr>
						<tr>
							<td>Mobile:</td>
							<td><?= $delegateDetails['user_mobile_isd_code'] ?> - <?= $delegateDetails['user_mobile_no'] ?></td>
							<td>PV No:</td>
							<td><?= $invoiceDetails['slipNO'] ?></td>
						</tr>
					</table>
					<?

					$totalConferenceRegistrationAmount    = 0.00;
					$totalWorkshopRegistrationAmount      = 0.00;
					$totalAccompanyRegistrationAmount     = 0.00;
					$totalAccommodationRegistrationAmount = 0.00;
					$totalTourRegistrationAmount          = 0.00;
					$totalInternetHandlingAmount          = 0.00;
					$totalTaxAmount     			      = 0.00;

					if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
					?>
						<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							Registration Details
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
								<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2">Amount (<?= getRegistrationCurrency(getUserClassificationId($delegateId)) ?>)</td>
							</tr>
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= getRegClsfName(getUserClassificationId($delegateId)) ?></td>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= getCutoffName($delegateDetails['registration_tariff_cutoff_id']) ?></td>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_consumed_quantity'] ?></td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['service_unit_price'] ?></td>
							</tr>
							<tr>
								<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet handling charges</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['internet_handling_amount'] ?></td>
							</tr>
							<tr>
								<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['service_roundoff_price'] ?></td>
							</tr>
						</table>
					<?
						$totalConferenceRegistrationAmount = $invoiceDetails['service_unit_price'];
						$totalInternetHandlingAmount       = $totalInternetHandlingAmount + $invoiceDetails['internet_handling_amount'];
						$totalTaxAmount      			   = $totalTaxAmount + $invoiceDetails['applicable_tax_amount'];
					}

					if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
					?>
						<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							Residetial Registration Details
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
								<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (<?= getRegistrationCurrency(getUserClassificationId($delegateId)) ?>)</td>
							</tr>


							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= getRegClsfName(getUserClassificationId($delegateId)) ?> </td>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= getCutoffName($delegateDetails['registration_tariff_cutoff_id']) ?></td>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_consumed_quantity'] ?></td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['service_unit_price'] ?></td>
							</tr>
							<tr>
								<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet Charge</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['internet_handling_amount'] ?></td>
							</tr>
							<tr>
								<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['service_roundoff_price'] ?></td>
							</tr>


						</table>
					<?
						$totalConferenceRegistrationAmount = $invoiceDetails['service_unit_price'];
						$totalInternetHandlingAmount       = $totalInternetHandlingAmount + $invoiceDetails['internet_handling_amount'];
						$totalTaxAmount      			   = $totalTaxAmount + $invoiceDetails['applicable_tax_amount'];
					}

					if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
						$workShopDetails 				 = getWorkshopDetails($invoiceDetails['refference_id']);
					?>
						<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							Registration Details
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
								<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (<?= getRegistrationCurrency(getUserClassificationId($delegateId)) ?>)</td>
							</tr>


							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= getWorkshopName($workShopDetails['workshop_id']) ?></td>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= getCutoffName($workShopDetails['tariff_cutoff_id']) ?></td>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_consumed_quantity'] ?></td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['service_unit_price'] ?></td>
							</tr>
							<tr>
								<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Internet Charge</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['internet_handling_amount'] ?></td>
							</tr>
							<tr>
								<td height="24" align="right" valign="middle" style="border:thin solid #000;" colspan="3">Total</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;" colspan="2"><?= $invoiceDetails['service_roundoff_price'] ?></td>
							</tr>

						</table>
					<?
						$totalWorkshopRegistrationAmount   = $invoiceDetails['service_unit_price'];
						$totalInternetHandlingAmount       = $totalInternetHandlingAmount + $invoiceDetails['internet_handling_amount'];
						$totalTaxAmount      			   = $totalTaxAmount + $invoiceDetails['applicable_tax_amount'];
					}

					if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
						$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);
					?>
						<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							Accompany Registration Details
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
								<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Details</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (<?= getRegistrationCurrency(getUserClassificationId($delegateId)) ?>)</td>
							</tr>
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= $accompanyDetails['user_full_name'] ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= getCutoffName($accompanyDetails['registration_tariff_cutoff_id']) ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= $invoiceDetails['service_consumed_quantity'] ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_unit_price'] ?></td>
							</tr>
							<tr>
								<td align="center" valign="middle" style="border:thin solid #000;">Service Tax</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['applicable_tax_amount'] ?></td>
							</tr>
							<tr>
								<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['internet_handling_amount'] ?></td>
							</tr>
							<tr>
								<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
								<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_roundoff_price'] ?></td>
							</tr>
						</table>
					<?
						$totalAccompanyRegistrationAmount  = $invoiceDetails['service_unit_price'];
						$totalInternetHandlingAmount       = $totalInternetHandlingAmount + $invoiceDetails['internet_handling_amount'];
						$totalTaxAmount      			   = $totalTaxAmount + $invoiceDetails['applicable_tax_amount'];
					}

					if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
						$accomodationDetails 		= getAccomodationDetails($invoiceDetails['refference_id']);
					?>
						<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							Accommodation Booking Details
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
								<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">-</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (<?= getRegistrationCurrency(getUserClassificationId($delegateId)) ?>)</td>
							</tr>
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="3">
									<?= getAccomPackageName($accomodationDetails['package_id']) ?><br />
									<strong>Check In:</strong> <?= getAccomDate($accomodationDetails['checkin_date']) ?><br />
									<strong>Check In:</strong> <?= getAccomDate($accomodationDetails['checkout_date']) ?>
								</td>
								<td align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= getCutoffName($accomodationDetails['tariff_cutoff_id']) ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= $invoiceDetails['service_consumed_quantity'] ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_unit_price'] ?></td>
							</tr>
							<tr>
								<td align="center" valign="middle" style="border:thin solid #000;">Service Tax</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['applicable_tax_amount'] ?></td>
							</tr>
							<tr>
								<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['internet_handling_amount'] ?></td>
							</tr>
							<tr>
								<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
								<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_roundoff_price'] ?></td>
							</tr>
						</table>
					<?
						$totalAccommodationRegistrationAmount  = $invoiceDetails['service_unit_price'];
						$totalInternetHandlingAmount       = $totalInternetHandlingAmount + $invoiceDetails['internet_handling_amount'];
						$totalTaxAmount      			   = $totalTaxAmount + $invoiceDetails['applicable_tax_amount'];
					}

					if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
						$tourDetails 				= getTourDetails($invoiceDetails['refference_id']);
					?>
						<div style="color:#000; font-weight:bold; padding:5px; margin:0px; font-size:14px; text-align:center;">
							Tour Booking Details
						</div>
						<table width="100%" style="font-size:13px;">
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;">Particular</td>
								<td width="25%" align="center" valign="middle" style="border:thin solid #000;">Registration Cut-off</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">Quantity</td>
								<td width="15%" align="center" valign="middle" style="border:thin solid #000;">-</td>
								<td width="10%" align="center" valign="middle" style="border:thin solid #000;">Amount (<?= getRegistrationCurrency(getUserClassificationId($delegateId)) ?>)</td>
							</tr>
							<tr>
								<td height="24" align="center" valign="middle" style="border:thin solid #000;" rowspan="3">
									<?= getTourName($tourDetails['package_id']) ?><br />
									<strong>Tour Date:</strong> <?= getTourDate($tourDetails['package_id']) ?><br />
									<strong>No Of Person(s):</strong> <?= $tourDetails['booking_quantity'] ?>
								</td>
								<td align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= getCutoffName($tourDetails['tariff_cutoff_id']) ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;" rowspan="3"><?= $invoiceDetails['service_consumed_quantity'] ?></td>
								<td align="center" valign="middle" style="border:thin solid #000;">Amount</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_unit_price'] ?></td>
							</tr>
							<tr>
								<td align="center" valign="middle" style="border:thin solid #000;">Internet Charge</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['internet_handling_amount'] ?></td>
							</tr>
							<tr>
								<td colspan="3" height="24" align="center" valign="middle" style="border:thin solid #000;"></td>
								<td align="center" valign="middle" style="border:thin solid #000;">Total</td>
								<td align="right" valign="middle" style="border:thin solid #000;"><?= $invoiceDetails['service_roundoff_price'] ?></td>
							</tr>
						</table>
					<?
						$totalTourRegistrationAmount  	   = $invoiceDetails['service_unit_price'];
						$totalInternetHandlingAmount       = $totalInternetHandlingAmount + $invoiceDetails['internet_handling_amount'];
						$totalTaxAmount      			   = $totalTaxAmount + $invoiceDetails['applicable_tax_amount'];
					}
					$totalAmount = $totalConferenceRegistrationAmount
						+ $totalWorkshopRegistrationAmount
						+ $totalAccompanyRegistrationAmount
						+ $totalAccommodationRegistrationAmount
						+ $totalTourRegistrationAmount
						+ $totalInternetHandlingAmount
						+ $totalTaxAmount;
					?>
					<table width="100%" style="font-size:13px;">
						<tr>
							<td><br /><br /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" valign="bottom" style="border-collapse:collapse;">
					<img src="<?= _BASE_URL_ ?>images/footer20191011.jpg" width="790px" />
				</td>
			</tr>
		</table>
	</div>
<?
}

function cancelInvoiceProcess($delegateId = "", $invoiceId = "")
{
	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.dinner.php');
	include_once('function.accompany.php');
	include_once('function.workshop.php');
	$delegateId = $_REQUEST['user_id'];
	$invoiceId  = $_REQUEST['invoice_id'];
	$invoiceDetails = getInvoiceDetails($invoiceId);


	if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
		$reqId = $invoiceDetails['delegateId'];
		// REMOVING USER

		$invoiceDetailsArr = invoiceDetailsOfDelegate($reqId, "");
		foreach ($invoiceDetailsArr as $key => $rowInvoiceDetails) {
			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
									 SET `status` = ?
								   WHERE `id` = ?";

			$sqlUpdate['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'C',                     'TYP' => 's');
			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',     'DATA' => $rowInvoiceDetails['id'], 'TYP' => 's');

			$mycms->sql_update($sqlUpdate, false);
		}
		$sqlRemove = array();
		$sqlRemove['QUERY']    = "UPDATE " . _DB_USER_REGISTRATION_ . " 
									   SET `status` = ? 
									 WHERE `id`     =?";

		$sqlRemove['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'F',    'TYP' => 's');
		$sqlRemove['PARAM'][]   = array('FILD' => 'id',     'DATA' => $reqId, 'TYP' => 's');

		$mycms->sql_update($sqlRemove, false);
	}

	if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
		$reqId = $invoiceDetails['delegateId'];
		// REMOVING USER

		$invoiceDetailsArr = invoiceDetailsOfDelegate($reqId, "");
		foreach ($invoiceDetailsArr as $key => $rowInvoiceDetails) {
			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
									 SET `status` = ?
								   WHERE `id` = ?";

			$sqlUpdate['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'C',                      'TYP' => 's');
			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',     'DATA' => $rowInvoiceDetails['id'], 'TYP' => 's');

			$mycms->sql_update($sqlUpdate, false);
		}
		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
								 SET `status` = ?
							   WHERE `user_id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',         'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
								 SET `status` = ?
							   WHERE `delegate_id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'D',    'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $reqId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		$sqlRemove    = array();
		$sqlRemove['QUERY']       = "UPDATE " . _DB_USER_REGISTRATION_ . " 
									   SET `status` = ? 
									 WHERE `id`     =?";

		$sqlRemove['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'F',    'TYP' => 's');
		$sqlRemove['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $reqId, 'TYP' => 's');

		$mycms->sql_update($sqlRemove, false);
	}

	if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
		$reqId 	   = $invoiceDetails['delegateId'];

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
								 SET `status` = ?
							   WHERE `refference_invoice_id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',                'DATA' => 'D',        'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_invoice_id', 'DATA' => $invoiceId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		$paymentStatusArr = registrationPaymentStatus($delegateId, "WORKSHOP");

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
								 SET `workshop_payment_status` = ?
							   WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'workshop_payment_status',  'DATA' => $paymentStatusArr['paymentStatus'],        'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                       'DATA' => $delegateId,                               'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);
		$totalCount = totalWorkshopCountReport($delegatesId);
		if ($totalCount == 0 || $totalCount == "") {
			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
									 SET `isWorkshop` =?,
										 `workshop_payment_status` = ?
								   WHERE `id` = ?";

			$sqlUpdate['PARAM'][]   = array('FILD' => 'isWorkshop',               'DATA' => 'N',          'TYP' => 's');
			$sqlUpdate['PARAM'][]   = array('FILD' => 'workshop_payment_status',  'DATA' => NULL,         'TYP' => 's');
			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                       'DATA' => $delegateId,  'TYP' => 's');

			$mycms->sql_update($sqlUpdate, false);
		}
	}

	if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
		$reqId = $invoiceDetails['refference_id'];
		$dinnerDetails = getDinnerDetailsOfDelegate($reqId);
		$dinnerRequestId = $dinnerDetails['id'];

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
								 SET `status` = ?
							   WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',          'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',      'DATA' => $reqId,       'TYP' => 's');


		$mycms->sql_update($sqlUpdate, false);
		$totalCount = getTotalAccompanyCount($delegatesId);
		if ($totalCount == 0 || $totalCount == "") {
			$sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
									 SET `isAccompany` = ?
								   WHERE `id` = ?";

			$sqlUpdate['PARAM'][]   = array('FILD' => 'isAccompany',  'DATA' => 'N',          'TYP' => 's');
			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',           'DATA' => $delegatesId, 'TYP' => 's');

			$mycms->sql_update($sqlUpdate, false);
		}

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "
								 SET `status` = ?
							   WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',              'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',      'DATA' => $dinnerRequestId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);

		$sqlUpdateDinner = array();
		$sqlUpdateDinner['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
									 SET `status` = ?
								   WHERE `id` = ?";

		$sqlUpdateDinner['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'C',                                     'TYP' => 's');
		$sqlUpdateDinner['PARAM'][]   = array('FILD' => 'id',      'DATA' => $dinnerDetails['refference_invoice_id'], 'TYP' => 's');

		$mycms->sql_update($sqlUpdateDinner, false);
	}

	if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {

		$sqlAccommodationRoom['QUERY'] = "SELECT accommodation_room FROM " . _DB_USER_REGISTRATION_ . " 
									WHERE `id` = '" . $delegateId . "' AND `status` = 'A'";

		$resultAccommodationRoom        = $mycms->sql_select($sqlAccommodationRoom);


		$reqId = $invoiceDetails['refference_id'];
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
						 SET `status` = 'D'
					   WHERE `id` = '" . $reqId . "'";

		$mycms->sql_update($sqlUpdate, false);

		$sqlUpdateRoom = array();
		$sqlUpdateRoom['QUERY'] = "UPDATE " . _DB_MASTER_ROOM_ . "
						 SET `status` = 'D'
					   WHERE `request_accommodation_id` = '" . $reqId . "'";

		$mycms->sql_update($sqlUpdateRoom, false);

		$paymentStatusArr = registrationPaymentStatus($delegateId, "ACCOMMODATION");
		if (trim($paymentStatusArr['PAYMENT_STATUS']) == 'ZERO VALUE') {
			$stt = 'ZERO_VALUE';
		} else {
			$stt = $paymentStatusArr['PAYMENT_STATUS'];
		}


		$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
						 SET `accommodation_payment_status` = '" . $stt . "'
					   WHERE `id` = '" . $delegateId . "'";

		$mycms->sql_update($sqlUpdate, false);
		$totalCount = getTotalAccommodationCount($delegatesId);
		if ($totalCount == 0 || $totalCount == "") {
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
							 SET `isAccommodation` = 'N',
								 `accommodation_payment_status` = NULL
						   WHERE `id` = '" . $delegatesId . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
	}

	if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
		$reqId = $invoiceDetails['refference_id'];
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_TOUR_ . "
						 SET `status` = 'D'
					   WHERE `id` = '" . $reqId . "'";

		$mycms->sql_update($sqlUpdate, false);
		$paymentStatusArr = registrationPaymentStatus($delegateId, "TOUR");
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
						 SET `tour_payment_status` = '" . $paymentStatusArr['PAYMENT_STATUS'] . "'
					   WHERE `id` = '" . $delegateId . "'";

		$mycms->sql_update($sqlUpdate, false);
		$totalCount = getTotalTourCount($delegatesId);
		if ($totalCount == 0 || $totalCount == "") {
			$sqlUpdate['QUERY'] = "UPDATE " . _DB_USER_REGISTRATION_ . "
							 SET `isTour` = 'N',
								 `tour_payment_status` = NULL
						   WHERE `id` = '" . $delegatesId . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
	}

	if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
		$reqId = $invoiceDetails['refference_id'];

		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_REQUEST_DINNER_ . "
								 SET `status` = ?
							   WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',    'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',      'DATA' => $reqId, 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);
	}



	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
							 SET `status` = ?
						   WHERE `id` = ?";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'C',        'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',      'DATA' => $invoiceId, 'TYP' => 's');

	$mycms->sql_update($sqlUpdate, false);

	if ($invoiceDetails['payment_status'] == "UNPAID") {
		// echo $invoiceDetails['slip_id'];die;
		$slip_id = $invoiceDetails['slip_id'];
		$total_amount_after_cancellation = invoiceAmountOfSlip($slip_id);
		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE " . _DB_PAYMENT_ . "
							 SET `amount` = ?
						   WHERE `slip_id` = ? AND `status` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'amount',  'DATA' => $total_amount_after_cancellation,        'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'slip_id',      'DATA' => $slip_id, 'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',      'DATA' => 'A', 'TYP' => 's');

		$mycms->sql_update($sqlUpdate, false);
	}
}


function insertingSlipCopy($slipId = "", $reason = "")
{
	global $cfg, $mycms;

	$sql 	= array();
	$sql['QUERY']		              = "INSERT INTO " . _DB_SLIP_COPY_ . "
												 SET `slip_id`						  = ?,
													 `request_type`					  = ?,
													 `created_ip` 					  = ?,
													 `created_by` 					  = ?,
													 `created_sessionId`			  = ?,
													 `created_browser` 				  = ?,
													 `created_dateTime` 			  = ?";
	$sql['PARAM'][]   = array('FILD' => 'slip_id',        			'DATA' => $slipId, 						'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'request_type',             'DATA' => $reason, 						'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_ip',               'DATA' => $_SERVER['REMOTE_ADDR'], 		'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_by',               'DATA' => $mycms->getLoggedUserId(), 	'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_sessionId',        'DATA' => session_id(), 					'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_browser',          'DATA' => $_SERVER['HTTP_USER_AGENT'],   'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_dateTime',         'DATA' => date('Y-m-d H:i:s'), 			'TYP' => 's');
	$lastInsertedUserId               = $mycms->sql_insert($sql, false);
	return $lastInsertedUserId;
}

function insertingInvoiceCopy($invoiceId = "", $reason = "")
{
	global $cfg, $mycms;

	$sql 	= array();
	$sql['QUERY']			              = "INSERT INTO " . _DB_INVOICE_COPY_ . "
													 SET `invoice_id`					  = ?,
														 `request_type`					  = ?,
														 `created_ip` 					  = ?,
														 `created_by` 					  = ?,
														 `created_sessionId`			  = ?,
														 `created_browser` 				  = ?,
														 `created_dateTime` 			  = ?";

	$sql['PARAM'][]   = array('FILD' => 'invoice_id',        'DATA' => $invoiceId, 			        'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'request_type',      'DATA' => $reason, 						'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $_SERVER['REMOTE_ADDR'], 		'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_by',        'DATA' => $mycms->getLoggedUserId(),    'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => session_id(), 				'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_browser',   'DATA' => $_SERVER['HTTP_USER_AGENT'],  'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'), 			'TYP' => 's');

	$lastInsertedUserId               = $mycms->sql_insert($sql, false);
	return $lastInsertedUserId;
}

function historyOfslip($slipId)
{
	global $cfg, $mycms;
	$historyArr  = array();
	$slipDetails = slipDetails($slipId);

	$sql  = array();
	$sql['QUERY'] 	 = "SELECT * 
						  FROM " . _DB_SLIP_COPY_ . " 
						  WHERE `slip_id` = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'slip_id', 	  'DATA' => $slipId,             'TYP' => 's');

	$result	 = $mycms->sql_select($sql);
	$date 	 = date_create($slipDetails['created_dateTime']);
	$cDate	 = date_format($date, "d/m/Y h:i:s A");

	$historyArr[]  = "Slip Created On " . $cDate;
	if ($result) {
		foreach ($result as $key => $value) {
			$date 	 = date_create($value['created_dateTime']);
			$mDate	 = date_format($date, "d/m/Y h:i:s A");

			$historyArr[]  = "<br>" . ucwords(strtolower($value['request_type'])) . " On " . $mDate;
		}
	}
	return $historyArr;
}

function complementaryPaymentConfirmationProcess($slipId, $delegateId)
{
	global $cfg, $mycms;
	$paymentId 			  = $paymentId;
	$paymentRemark 	 	  = $_REQUEST['remarks'];
	$paymentDate 		  = date('Y-m-d');
	$paidAmmount		  = invoiceAmountOfSlip($slipId);

	$sqlUpdatePayment     = array();
	$sqlUpdatePayment['QUERY']	  = "UPDATE " . _DB_PAYMENT_ . "
									SET `payment_date` = '" . $paymentDate . "',
										`payment_remark` = '" . $paymentRemark . "',
										`amount` = '" . $paidAmmount . "',
										`payment_status` = 'PAID'
								  WHERE `id` = '" . $paymentId . "'";



	$sqlUpdateSlip        = array();
	$sqlUpdateSlip['QUERY']	 = "UPDATE " . _DB_SLIP_ . "
								SET `payment_status` = 'COMPLIMENTARY'
							  WHERE `id` = '" . $slipId . "'";

	$mycms->sql_update($sqlUpdateSlip, false);

	$activeInvoice = invoiceDetailsActiveOfSlip($slipId);
	foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
		if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			$sqlUpdateSlip  = array();
			$sqlUpdateSlip['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `registration_payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			$sqlUpdateSlip        = array();
			$sqlUpdateSlip['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `registration_payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdateWorkshop   = array();
			$sqlUpdateWorkshop['QUERY']    = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
												SET `payment_status` = 'COMPLIMENTARY'
											  WHERE `delegate_id` = '" . $valActiveInvoice['delegate_id'] . "'
												AND `status`='A'";

			$mycms->sql_update($sqlUpdateWorkshop, false);

			$sqlUpdateAccom       = array();
			$sqlUpdateAccom['QUERY']	      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
										SET `payment_status` = 'COMPLIMENTARY'
									  WHERE `user_id` = '" . $valActiveInvoice['delegate_id'] . "'
										AND `status`='A'";

			$mycms->sql_update($sqlUpdateAccom, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
			$sqlUpdateSlip   = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
										SET `payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate   = array();
			$sqlUpdate['QUERY']		= "UPDATE " . _DB_USER_REGISTRATION_ . "
									SET `workshop_payment_status` = 'COMPLIMENTARY'
								  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
		if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$sqlUpdateSlip       = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `registration_payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$sqlUpdateSlip   = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
											SET `payment_status` = 'COMPLIMENTARY'
										  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate = array();
			$sqlUpdate['QUERY']		 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `accommodation_payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}
		if ($valActiveInvoice['service_type'] == 'DELEGATE_TOUR_REQUEST') {
			$sqlUpdateSlip   = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_TOUR_ . "
												SET `payment_status` = 'COMPLIMENTARY'
											  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);

			$sqlUpdate   = array();
			$sqlUpdate['QUERY']		  = "UPDATE " . _DB_USER_REGISTRATION_ . "
										SET `tour_payment_status` = 'COMPLIMENTARY'
									  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

			$mycms->sql_update($sqlUpdate, false);
		}

		// $sqlUpdateSlip      = array();
		// $sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_INVOICE_ . "
		// 									SET `payment_status` = 'COMPLIMENTARY'
		// 								  WHERE `id` = '" . $valActiveInvoice['id'] . "'";

		// $mycms->sql_update($sqlUpdateSlip, false);
		$sqlUpdateInvoice      = array();
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
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'payment_status', 'DATA' =>'COMPLIMENTARY', 'TYP' => 's');
		$sqlUpdateInvoice['PARAM'][] = array('FILD' => 'id', 'DATA' =>$valActiveInvoice['id'], 'TYP' => 'i');
		$mycms->sql_update($sqlUpdateInvoice, false);

	}
	offline_conference_registration_confirmation_message($delegateId,$paymentId="",$slipId , 'SEND');
	// online_senior_citizen_conference_registration_confirmation_message($delegateId, $paymentId = "", $slipId, 'SEND');
}

function getslippaymentDetails($searchCondition)
{
	global $cfg, $mycms;

	$sqlSlip  = array();
	$sqlSlip['QUERY']  = "SELECT DISTINCT (slip.id), IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
											IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount,
											slip.slip_number AS slipNumber,
											slip.id AS slipId,
											slip.payment_status AS paymentStatus,
											slipUser.id AS slipUserid,
											slip.invoice_mode AS invoice_mode,
											slipUser.user_full_name AS slipUserName,
											slipUser.user_unique_sequence AS slipUserUnqsqnce,
											slipUser.user_registration_id AS slipUserRegid,
											slipUser.user_email_id AS slipUserEmailId,
											slipUser.user_type AS userType,
											CONCAT ( slipUser.user_mobile_isd_code, ' - ', slipUser.user_mobile_no) AS slipUserMobile
											
											
									  FROM " . _DB_SLIP_ . " slip	
									  
								INNER JOIN " . _DB_USER_REGISTRATION_ . " slipUser
										ON slip.delegate_id = slipUser.id
										
							LEFT OUTER JOIN " . _DB_PAYMENT_ . " payment
										ON payment.slip_id = slip.id
										
						   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
													`slip_id`
											  FROM  " . _DB_INVOICE_ . " 
											 WHERE `status` = 'A'
										  GROUP BY `slip_id` ) activeInvoice
										ON slip.id = activeInvoice.slip_id 
										
						   LEFT OUTER JOIN ( SELECT SUM(`service_roundoff_price`) AS totalInvoice,
												   `slip_id`
											 FROM  " . _DB_INVOICE_ . " 
											WHERE `status` = 'A'
										 GROUP BY `slip_id` ) activeInvoiceAmount
										ON slip.id = activeInvoiceAmount.slip_id	
												
									 WHERE slip.status = 'A'
									   AND slipUser.status='A'
										
									   AND activeInvoice.invoiceCount>0
									   " . $searchCondition . "
									   ORDER BY slip.id DESC,slipUser.id DESC";
	return $sqlSlip;
}

function getCancelInvoiceDetailsInvoiceWise($InvoiceId)
{
	global $cfg, $mycms;
	$rowfetchrefund = array();
	$rowfetchrefund['QUERY']		=	"SELECT * 
										  FROM " . _DB_CANCEL_INVOICE_ . "
										 WHERE `invoice_id` 	= ?";
	$rowfetchrefund['PARAM'][]  = array('FILD' => 'invoice_id',  'DATA' => $InvoiceId,  'TYP' => 's');
	$rowfetchrefunddetails		  =	$mycms->sql_select($rowfetchrefund);
	$rowfetchretaurn     		  = $rowfetchrefunddetails[0];
	return $rowfetchretaurn;
}

function FTRrefundedInvoiceAmountAndCount($classificationId, $searchCondition = "")
{
	global $cfg, $mycms;


	$sqlInvoiceAmount = array();
	$sqlInvoiceAmount['QUERY'] = " SELECT IFNULL(SUM(cancel.refunded_amount),0) AS totalRfndAmount,
								         IFNULL(COUNT(invoice.id),0) AS totalCount
								
						                 FROM " . _DB_INVOICE_ . " invoice
						   
					                     INNER JOIN " . _DB_SLIP_ . " slip
							             ON invoice.slip_id = slip.id
							 
					 					 INNER JOIN " . _DB_USER_REGISTRATION_ . " invoiceUser
							  			 ON invoice.delegate_id = invoiceUser.id 
							 
					 					 INNER JOIN " . _DB_CANCEL_INVOICE_ . " cancel
							  			 ON invoiceUser.id = cancel.delegate_id 
										 AND slip.id = cancel.slip_id
										 AND invoice.id = cancel.invoice_id
										 AND cancel.Refund_status = 'Refunded'
								 
										 WHERE invoice.payment_status= ? " . $searchCondition . "
										 AND invoiceUser.registration_classification_id= ? 
										 AND invoiceUser.status IN ('A','C')";

	$sqlInvoiceAmount['PARAM'][]  = array('FILD' => 'invoice.payment_status',  'DATA' => 'PAID',  'TYP' => 's');
	$sqlInvoiceAmount['PARAM'][]  = array('FILD' => 'invoiceUser.registration_classification_id',  'DATA' => $classificationId,  'TYP' => 's');

	$resultInvoiceAmt	 = $mycms->sql_select($sqlInvoiceAmount);
	$Arr['TOTAL_AMOUNT'] = $resultInvoiceAmt[0]['totalAmount'];
	$Arr['TOTAL_COUNT'] = $resultInvoiceAmt[0]['totalCount'];
	$Arr['STLMNT_AMT'] 	 = $resultInvoiceAmt[0]['totalSattlementAmmount'];
	$Arr['RFND_AMT'] 	 = $resultInvoiceAmt[0]['totalRfndAmount'];

	return $Arr;
}

function getPaymentDetailsDelegate($delegateId)
{
	global $cfg, $mycms;

	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT * FROM " . _DB_PAYMENT_ . " 
							WHERE status = ? 
							AND `delegate_id` = ?";

	$sqlDetails['PARAM'][]   = array('FILD' => 'status',       'DATA' => 'A',          'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'delegate_id',  'DATA' => $delegateId,  'TYP' => 's');

	$resDetails = $mycms->sql_select($sqlDetails);
	$rowdetails = $resDetails[0];

	return $rowdetails;
}

function gstInsertionInInvoice($invoiceId)
{
	global $mycms, $cfg;

	$sqlfetchInvoice = array();
	$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_INVOICE_ . "
										  WHERE `id` = ?";

	$sqlfetchInvoice['PARAM'][]   = array('FILD' => 'id',  'DATA' => $invoiceId,  'TYP' => 's');

	$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
	$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];
	if ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
		$cgst = $cfg['RESIDENTIAL.CGST'];
		$sgst = $cfg['RESIDENTIAL.SGST'];

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
		$cgst = $cfg['WORKSHOP.CGST'];
		$sgst = $cfg['WORKSHOP.SGST'];

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
		$cgst = $cfg['ACCOMPANY.CGST'];
		$sgst = $cfg['ACCOMPANY.SGST'];

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
		$cgst = $cfg['ACCOMMODATION.CGST'];
		$sgst = $cfg['ACCOMMODATION.SGST'];

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
		$cgst = $cfg['DINNER.CGST'];
		$sgst = $cfg['DINNER.SGST'];

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	}


	if ($rowDetailsfetchInvoice['invoice_mode'] == 'ONLINE') {
		$servicePrice 					= $gstArray['GST.PRICE'];
		$internetHandlingPercentage     = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		$internet_handling_amount		= calculateTaxAmmount($servicePrice, $internetHandlingPercentage);
		$gstIntArray 					= gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $internet_handling_amount, true);
	} else {
		$internet_handling_amount		= 0.00;
		$gstIntArray 					= gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $internet_handling_amount, true);
	}



	$totalGrandPrice = $servicePrice + $internet_handling_amount;

	$sqlUpdateInvoice = array();

	$sqlUpdateInvoice['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
										 SET  `service_total_price` 	     = ?,
											  `cgst_int_percentage` 	     = ?, 
											  `sgst_int_percentage`	         = ?,
											  `cgst_int_price` 	  	         = ?, 
											  `sgst_int_price`	 	         = ?,										 							  
											  `service_gst_int_price`	     = ?,
											  `service_basic_price`          = ?,
											  `cgst_percentage` 	  	 	 = ?, 
											  `sgst_percentage`	 	 	 	 = ?,
											  `cgst_price` 	  	 		 	 = ?, 
											  `sgst_price`	 	 	     	 = ?,										 
											  `service_gst_total_price`	 	 = ?,
											  `service_grand_price`  	     = ?,
											  `service_roundoff_price`  	 = ?,
											  `has_gst`  				 	 = ?
											WHERE `id` 						 = ?";

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_total_price',       'DATA' => $servicePrice,             'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_int_percentage',       'DATA' => $cfg['INT.CGST'],          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_int_percentage',       'DATA' => $cfg['INT.SGST'],          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_int_price',            'DATA' => $cfg['CGST.PRICE'],        'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_int_price',            'DATA' => $cfg['SGST.PRICE'],        'TYP' => 's');

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_gst_int_price',     'DATA' => $gstIntArray['GST.PRICE'], 'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_basic_price',       'DATA' => $gstArray['BASIC.PRICE'],  'TYP' => 's');

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_percentage',           'DATA' => $cgst,                     'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_percentage',           'DATA' => $sgst,                     'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_price',                'DATA' => $gstArray['CGST.PRICE'],   'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_price',                'DATA' => $gstArray['SGST.PRICE'],   'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_gst_total_price',   'DATA' => $gstArray['GST.PRICE'],    'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_grand_price',       'DATA' => $totalGrandPrice,          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_roundoff_price',    'DATA' => round($totalGrandPrice, 0), 'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'has_gst',                   'DATA' => 'Y',                       'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'id',                        'DATA' => $invoiceId,                'TYP' => 's');

	// echo '<pre>'; print_r($sqlUpdateInvoice);die;
	$mycms->sql_update($sqlUpdateInvoice, false);
}


function gstInsertionInInvoiceOffline($invoiceId)
{
	global $mycms, $cfg;

	$sqlfetchInvoice = array();
	$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_INVOICE_ . "
										  WHERE `id` = ?";

	$sqlfetchInvoice['PARAM'][]   = array('FILD' => 'id',  'DATA' => $invoiceId,  'TYP' => 's');

	$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
	$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];
	if ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}


		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

        
		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
		// $cgst = $cfg['RESIDENTIAL.CGST'];
		// $sgst = $cfg['RESIDENTIAL.SGST'];
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
		// $cgst = $cfg['WORKSHOP.CGST'];
		// $sgst = $cfg['WORKSHOP.SGST'];

		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	}


	if ($rowDetailsfetchInvoice['invoice_mode'] == 'ONLINE') {
		$servicePrice 					= $gstArray['GST.PRICE'];
		$internetHandlingPercentage     = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		$internet_handling_amount		= calculateTaxAmmount($servicePrice, $internetHandlingPercentage);
		$gstIntArray 					= gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $internet_handling_amount, true);
	} else {
		$internet_handling_amount		= 0.00;
		//$gstIntArray 					= gstCalculation($cfg['INT.CGST'],$cfg['INT.SGST'] ,$internet_handling_amount,true);
	}



	$totalGrandPrice = $servicePrice + $gstIntArray['GST.PRICE'];

	$sqlUpdateInvoice = array();

	$sqlUpdateInvoice['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
										 SET  `service_total_price` 	     = ?,
											  `cgst_int_percentage` 	     = ?, 
											  `sgst_int_percentage`	         = ?,
											  `cgst_int_price` 	  	         = ?, 
											  `sgst_int_price`	 	         = ?,										 							  
											  `service_gst_int_price`	     = ?,
											  `service_basic_price`          = ?,
											  `cgst_percentage` 	  	 	 = ?, 
											  `sgst_percentage`	 	 	 	 = ?,
											  `cgst_price` 	  	 		 	 = ?, 
											  `sgst_price`	 	 	     	 = ?,										 
											  `service_gst_total_price`	 	 = ?,
											  `service_grand_price`  	     = ?,
											  `service_roundoff_price`  	 = ?,
											  `has_gst`  				 	 = ?
											WHERE `id` 						 = ?";

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_total_price',       'DATA' => $servicePrice,             'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_int_percentage',       'DATA' => $cfg['INT.CGST'],          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_int_percentage',       'DATA' => $cfg['INT.SGST'],          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_int_price',            'DATA' => $cfg['CGST.PRICE'],        'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_int_price',            'DATA' => $cfg['SGST.PRICE'],        'TYP' => 's');

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_gst_int_price',     'DATA' => $gstIntArray['GST.PRICE'], 'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_basic_price',       'DATA' => $gstArray['BASIC.PRICE'],  'TYP' => 's');

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_percentage',           'DATA' => $cgst,                     'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_percentage',           'DATA' => $sgst,                     'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_price',                'DATA' => $gstArray['CGST.PRICE'],   'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_price',                'DATA' => $gstArray['SGST.PRICE'],   'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_gst_total_price',   'DATA' => $gstArray['GST.PRICE'],    'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_grand_price',       'DATA' => $totalGrandPrice,          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_roundoff_price',    'DATA' => round($gstArray['GST.PRICE'], 0), 'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'has_gst',                   'DATA' => 'Y',                       'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'id',                        'DATA' => $invoiceId,                'TYP' => 's');

	

	$mycms->sql_update($sqlUpdateInvoice, false);
}

function gstInsertionInInvoiceOfflineDiscount($invoiceId)
{
	global $mycms, $cfg;

	$sqlfetchInvoice = array();
	$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_INVOICE_ . "
										  WHERE `id` = ?";

	$sqlfetchInvoice['PARAM'][]   = array('FILD' => 'id',  'DATA' => $invoiceId,  'TYP' => 's');

	$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
	$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];
	if ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}


		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

        
		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
		// $cgst = $cfg['RESIDENTIAL.CGST'];
		// $sgst = $cfg['RESIDENTIAL.SGST'];
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
		// $cgst = $cfg['WORKSHOP.CGST'];
		// $sgst = $cfg['WORKSHOP.SGST'];

		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0 || $rowDetailsfetchInvoice['discount_amount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	} elseif ($rowDetailsfetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
		if ($cfg['IGST.FLAG'] == 0) {
			$cgst = $cfg['CONFERENCE.CGST'];
			$sgst = $cfg['CONFERENCE.SGST'];
		} else {
			$cgst = $cfg['CONFERENCE.IGST'] / 2;
			$sgst = $cfg['CONFERENCE.IGST'] / 2;
		}

		$servicePrice = $rowDetailsfetchInvoice['service_unit_price'];
		if ($rowDetailsfetchInvoice['total_amount_after_discount'] > 0) {
			$servicePrice = $rowDetailsfetchInvoice['total_amount_after_discount'];
		}

		$gstArray = gstCalculation($cgst, $sgst, $servicePrice);
	}


	if ($rowDetailsfetchInvoice['invoice_mode'] == 'ONLINE') {
		$servicePrice 					= $gstArray['GST.PRICE'];
		$internetHandlingPercentage     = $cfg['INTERNET.HANDLING.PERCENTAGE'];
		$internet_handling_amount		= calculateTaxAmmount($servicePrice, $internetHandlingPercentage);
		$gstIntArray 					= gstCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $internet_handling_amount, true);
	} else {
		$internet_handling_amount		= 0.00;
		//$gstIntArray 					= gstCalculation($cfg['INT.CGST'],$cfg['INT.SGST'] ,$internet_handling_amount,true);
	}



	// $totalGrandPrice = $servicePrice + $gstIntArray['GST.PRICE'];
     $totalGrandPrice = $rowDetailsfetchInvoice['service_gst_total_price'] - $rowDetailsfetchInvoice['discount_amount'];
	$sqlUpdateInvoice = array();

	$sqlUpdateInvoice['QUERY'] = " UPDATE " . _DB_INVOICE_ . "
										 SET  `service_total_price` 	     = ?,
											  `cgst_int_percentage` 	     = ?, 
											  `sgst_int_percentage`	         = ?,
											  `cgst_int_price` 	  	         = ?, 
											  `sgst_int_price`	 	         = ?,										 							  
											  `service_gst_int_price`	     = ?,
											  `service_basic_price`          = ?,
											  `cgst_percentage` 	  	 	 = ?, 
											  `sgst_percentage`	 	 	 	 = ?,
											  `cgst_price` 	  	 		 	 = ?, 
											  `sgst_price`	 	 	     	 = ?,										 
											  `service_gst_total_price`	 	 = ?,
											  `service_grand_price`  	     = ?,
											  `service_roundoff_price`  	 = ?,
											  `has_gst`  				 	 = ?
											WHERE `id` 						 = ?";

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_total_price',       'DATA' => $servicePrice,             'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_int_percentage',       'DATA' => $cfg['INT.CGST'],          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_int_percentage',       'DATA' => $cfg['INT.SGST'],          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_int_price',            'DATA' => $cfg['CGST.PRICE'],        'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_int_price',            'DATA' => $cfg['SGST.PRICE'],        'TYP' => 's');

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_gst_int_price',     'DATA' => $gstIntArray['GST.PRICE'], 'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_basic_price',       'DATA' => $gstArray['BASIC.PRICE'],  'TYP' => 's');

	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_percentage',           'DATA' => $cgst,                     'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_percentage',           'DATA' => $sgst,                     'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'cgst_price',                'DATA' => $gstArray['CGST.PRICE'],   'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'sgst_price',                'DATA' => $gstArray['SGST.PRICE'],   'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_gst_total_price',   'DATA' => $totalGrandPrice,    'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_grand_price',       'DATA' => $totalGrandPrice,          'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'service_roundoff_price',    'DATA' => round($totalGrandPrice, 0), 'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'has_gst',                   'DATA' => 'Y',                       'TYP' => 's');
	$sqlUpdateInvoice['PARAM'][]   = array('FILD' => 'id',                        'DATA' => $invoiceId,                'TYP' => 's');

	

	$mycms->sql_update($sqlUpdateInvoice, false);
}
function gstCalculation($cgst_percentage, $sgst_percentage, $service_unit_price, $inclusive = true)
{
	global $mycms, $cfg;
	$gstPrice 	= array();
	if ($cfg['GST.FLAG'] == 1) {
		$inclusive = false;
	} elseif ($cfg['GST.FLAG'] == 2) {
		$inclusive = true;
	} elseif ($cfg['GST.FLAG'] == 3) {
		$inclusive = true;
	}
	
	if ($inclusive) {

		$service_basic_price 		= round((100 *  $service_unit_price) / (100 + ($cgst_percentage + $sgst_percentage)), 2);
		
	} else {
		$service_basic_price 		= $service_unit_price;
	}


	$cgst_price 				    = round($service_basic_price * ($cgst_percentage / 100), 2);
	$sgst_price				    = round($service_basic_price * ($sgst_percentage / 100), 2);
	$service_gst_price       		= round($service_basic_price + $cgst_price + $sgst_price, 2);

	$gstPrice['BASIC.PRICE']       = $service_basic_price;
	$gstPrice['CGST.PRICE'] 	    = $cgst_price;
	$gstPrice['SGST.PRICE'] 		= $sgst_price;
	$gstPrice['GST.PRICE']         = $service_gst_price;

	return  $gstPrice;
}

function gstInsertionInExhibitorInvoice($invoiceId)
{
	global $mycms, $cfg;

	$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_EXIBITOR_INVOICE_ . "
										  WHERE `id` = '" . $invoiceId . "'";
	$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
	$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];

	if ($rowDetailsfetchInvoice['service_type'] == "EXHIBITOR_COMPANY_REGISTRATION" || $rowDetailsfetchInvoice['service_type'] == "EXHIBITOR_REPRESENTATIVE_REGISTRATION") {
		$cgst = $cfg['EXHIBITOR.CGST'];
		$sgst = $cfg['EXHIBITOR.SGST'];

		$gstArray = gstPostAddedCalculation($cgst, $sgst, $rowDetailsfetchInvoice['service_unit_price']);	// *****  amount + GST
	}

	$gstIntArray = gstPostAddedCalculation($cfg['INT.CGST'], $cfg['INT.SGST'], $rowDetailsfetchInvoice['internet_handling_amount']);	// *****  amount + GST

	$totalGstPrice = $gstArray['GST.PRICE'] + $gstIntArray['GST.PRICE'];


	$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_EXIBITOR_INVOICE_ . "
								 SET  `cgst_int_percentage` 	 = '" . $cfg['INT.CGST'] . "', 
									  `sgst_int_percentage`	 	 = '" . $cfg['INT.SGST'] . "',
									  `cgst_int_price` 	  	 	 = '" . $gstIntArray['CGST.PRICE'] . "', 
									  `sgst_int_price`	 	 	 = '" . $gstIntArray['SGST.PRICE'] . "',
									  `service_basic_int_price`  = '" . $gstIntArray['BASIC.PRICE'] . "', 
									  
									  `service_gst_int_price`	 = '" . $gstIntArray['GST.PRICE'] . "',
									  `cgst_percentage` 	  	 = '" . $cgst . "', 
									  `sgst_percentage`	 	 	 = '" . $sgst . "',
									  `cgst_price` 	  	 		 = '" . $gstArray['CGST.PRICE'] . "', 
									  `sgst_price`	 	 	     = '" . $gstArray['SGST.PRICE'] . "',
									  `service_basic_price` 	 = '" . $gstArray['BASIC.PRICE'] . "', 
									  `service_gst_price`	 	 = '" . $gstArray['GST.PRICE'] . "',
									  
									  `service_grand_price`  	 = '" . $totalGstPrice . "',
									  `service_roundoff_price`   = '" . round($totalGstPrice, 0) . "',
									  
									  `has_gst`  				 = 'Y'
									 
							   WHERE `id` = '" . $invoiceId . "'";
	$mycms->sql_update($sqlUpdateInvoice, false);
}

function gstPostAddedCalculation($cgst_percentage, $sgst_percentage, $service_unit_price)
{
	global $mycms, $cfg;
	$gstPrice 						= array();
	$service_basic_price 			= $service_unit_price;
	$cgst_price 				    = round($service_basic_price * ($cgst_percentage / 100), 2);
	$sgst_price				    = round($service_basic_price * ($sgst_percentage / 100), 2);
	$service_gst_price       		= round($service_basic_price + $cgst_price + $sgst_price, 0);

	$gstPrice['BASIC.PRICE']       = $service_basic_price;
	$gstPrice['CGST.PRICE'] 	    = $cgst_price;
	$gstPrice['SGST.PRICE'] 		= $sgst_price;
	$gstPrice['GST.PRICE']         = $service_gst_price;

	return  $gstPrice;
}

function insertingExhibitorInvoiceDetails($exhibitorId, $slip_id, $type)
{
	global $cfg, $mycms;

	$searchCondition			 =	"from_insertingInvoiceDetails";

	$sqlExhibitorDetails		 =  getExhibitorDetails($exhibitorId, $searchCondition);
	$resultExhibitorDetails		 =	$mycms->sql_select($sqlExhibitorDetails);
	$exhibitorDetails			 =	$resultExhibitorDetails[0];


	$invoiceDetails['exhibitor_id']					= $exhibitorId;
	$invoiceDetails['slip_id']						= $slip_id;
	$invoiceDetails['invoice_number']				= generateNextCode("id", _DB_EXIBITOR_INVOICE_, "ISAR18/EXHIBITOR/");
	$invoiceDetails['invoice_date']					= date('Y-m-d');
	$invoiceDetails['invoice_request']				= $type;
	$invoiceDetails['invoice_mode']					= 'OFFLINE';
	$invoiceDetails['invoice_currency']				= $exhibitorDetails['currency'];
	$invoiceDetails['registration_type']			= $type;
	$invoiceDetails['refference_id']				= $exhibitorId;
	$invoiceDetails['service_type']					= "EXHIBITOR_COMPANY_REGISTRATION";
	$invoiceDetails['tariff_ref_id']				= $exhibitorDetails['registraion_tariff_id'];
	$invoiceDetails['service_tariff_cutoff_id']		= 0;
	$invoiceDetails['service_unit_price']			= $exhibitorDetails['amount'];
	$invoiceDetails['service_consumed_quantity']	= 1;
	$invoiceDetails['service_product_price']		= $exhibitorDetails['amount'];

	if ($invoiceDetails['invoice_mode'] == "ONLINE") {
		$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
		$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
	} else if ($invoiceDetails['invoice_mode'] == "OFFLINE") {
		$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
		$internetHandlingPercentage       = 0.00;
	}

	$invoiceDetails['applicable_tax_percentage']	= $applicableTaxPercentage;
	$invoiceDetails['applicable_tax_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $applicableTaxPercentage);
	$invoiceDetails['internet_handling_percentage']	= $internetHandlingPercentage;
	$invoiceDetails['internet_handling_amount']		= calculateTaxAmmount($invoiceDetails['service_product_price'], $internetHandlingPercentage);
	$invoiceDetails['service_total_price']			= $invoiceDetails['service_product_price'] + $invoiceDetails['applicable_tax_amount'] + $invoiceDetails['internet_handling_amount'];
	$invoiceDetails['service_grand_price']			= $invoiceDetails['service_total_price'];
	$invoiceDetails['service_roundoff_price']		= intToFloat(round($invoiceDetails['service_total_price']));
	$invoiceDetails['payment_status']				= 'UNPAID';

	$sqlInsertExhibitorInvoiceRequest['QUERY']       = "INSERT INTO " . _DB_EXIBITOR_INVOICE_ . " 
										  SET `exhibitor_id`	   	 		 = '" . $invoiceDetails['exhibitor_id'] . "',
											  `slip_id` 		 	 		 = '" . $invoiceDetails['slip_id'] . "', 
											  `invoice_number` 	  			 = '" . $invoiceDetails['invoice_number'] . "', 
											  `invoice_date` 	 	 		 = '" . $invoiceDetails['invoice_date'] . "', 
											  `invoice_request` 			 = '" . $invoiceDetails['invoice_request'] . "', 
											  `invoice_mode`	 	 		 = '" . $invoiceDetails['invoice_mode'] . "',
											  `currency` 					 = 'INR',	
											  `registration_type`	 		 = '" . $invoiceDetails['registration_type'] . "', 
											  `refference_id` 	 	 		 = '" . $invoiceDetails['refference_id'] . "',
											  `service_type`	   	 		 = '" . $invoiceDetails['service_type'] . "',
											  `tariff_ref_id` 		 	 	 = '" . $invoiceDetails['tariff_ref_id'] . "', 
											  `service_tariff_cutoff_id` 	 = '" . $invoiceDetails['service_tariff_cutoff_id'] . "', 
											  `service_unit_price` 	 	  	 = '" . $invoiceDetails['service_unit_price'] . "', 
											  `service_consumed_quantity` 	 = '" . $invoiceDetails['service_consumed_quantity'] . "', 
											  `service_product_price`	 	 = '" . $invoiceDetails['service_product_price'] . "',
											  `applicable_tax_percentage`	 = '" . $invoiceDetails['applicable_tax_percentage'] . "',
											  `applicable_tax_amount`	 	 = '" . $invoiceDetails['applicable_tax_amount'] . "', 
											  `internet_handling_percentage` = '" . $invoiceDetails['internet_handling_percentage'] . "',
											  `internet_handling_amount` 	 = '" . $invoiceDetails['internet_handling_amount'] . "', 
											  `service_total_price` 	  	 = '" . $invoiceDetails['service_total_price'] . "', 
											  `service_grand_price`	 	 	 = '" . $invoiceDetails['service_grand_price'] . "',
											  `service_roundoff_price`	 	 = '" . $invoiceDetails['service_roundoff_price'] . "',
											  `payment_status` 				 = '" . $invoiceDetails['payment_status'] . "',
											  
											  `status` 				 		 = 'A',
											  `created_ip`			 		 = '" . $_SERVER['REMOTE_ADDR'] . "',
											  `created_sessionId`	 		 = '" . session_id() . "',
											  `created_browser`  	 		 = '" . $_SERVER['HTTP_USER_AGENT'] . "',
											  `created_dateTime` 	 		 = '" . date('Y-m-d H:i:s') . "'";

	$invoiceId			         = $mycms->sql_insert($sqlInsertExhibitorInvoiceRequest, false);

	gstInsertionInExhibitorInvoice($invoiceId);

	$sqlUpdate['QUERY']	                      = "UPDATE " . _DB_EXIBITOR_COMPANY_ . "
											SET `refference_invoice_id` = '" . $invoiceId . "'
										  WHERE `id` = '" . $exhibitorId . "' ";

	$mycms->sql_update($sqlUpdate, false);
}

function insertingExhibitorSlipDetails($insertedExhibitorId, $mode = "", $invoice_request = "", $date = '')
{
	global $cfg, $mycms;
	if ($date == '') {
		$date = date('Y-m-d');
	}
	$invoiceDetails['delegate_id']						= $insertedExhibitorId;
	$invoiceDetails['invoice_request']					= $invoice_request;
	$invoiceDetails['registration_token']				= $mycms->getSession('REGISTRATION_TOKEN');
	$invoiceDetails['slip_number']						= generateNextCode("id", _DB_SLIP_, "##SLIP" . date('dmy') . "-");
	$invoiceDetails['slip_date']						= $date;
	$invoiceDetails['invoice_mode']						= $mode;
	$invoiceDetails['invoice_type']						= 'GENERAL';
	$invoiceDetails['currency']							= 'INR';
	$invoiceDetails['registration_type']				= 'GENERAL';
	if ($mode == "ONLINE") {
		$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
		$internetHandlingPercentage       = $cfg['INTERNET.HANDLING.PERCENTAGE'];
	} else if ($mode == "OFFLINE") {
		$applicableTaxPercentage          = $cfg['SERVICE.TAX.PERCENTAGE'];
		$internetHandlingPercentage       = 0.00;
	}
	$invoiceDetails['applicable_tax_percentage']		= $applicableTaxPercentage;
	$invoiceDetails['internet_handling_percentage']		= $internetHandlingPercentage;

	$invoiceDetails['payment_status']					= 'UNPAID';


	$sqlInsertSlipRequest       = array();
	$sqlInsertSlipRequest['QUERY'] 	      = "INSERT INTO " . _DB_SLIP_ . " 
											  SET `slip_for`	   	 			  = 'EXHIBITOR',
												  `delegate_id`	   	 			  = '" . $invoiceDetails['delegate_id'] . "',
												  `invoice_request` 		 	  = '" . $invoiceDetails['invoice_request'] . "',
												  `registration_token`  		  = '" . $invoiceDetails['registration_token'] . "', 
												  `slip_number` 	  			  = '" . $invoiceDetails['slip_number'] . "', 
												  `slip_date` 	 	 		      = '" . $invoiceDetails['slip_date'] . "', 
												  `invoice_mode` 			      = '" . $invoiceDetails['invoice_mode'] . "', 
												  `invoice_type`	 	 		  = '" . $invoiceDetails['invoice_type'] . "',
												  `currency` 					  = '" . $invoiceDetails['currency'] . "',
												  `registration_type`	 		  = '" . $invoiceDetails['registration_type'] . "', 
												  `payment_status` 				  = '" . $invoiceDetails['payment_status'] . "',
												  
												  `status` 				 		  = 'A',
												  `created_ip`			 		  = '" . $_SERVER['REMOTE_ADDR'] . "',
												  `created_sessionId`	 		  = '" . session_id() . "',
												  `created_browser`  	 		  = '" . $_SERVER['HTTP_USER_AGENT'] . "',
												  `created_dateTime` 	 		  = '" . date('Y-m-d H:i:s') . "'";

	$lastInsertedSlipId                        = $mycms->sql_insert($sqlInsertSlipRequest, false);

	$mycms->setSession('SLIP_ID', $lastInsertedSlipId);

	return $lastInsertedSlipId;
}

function getCancelInvoiceDetails($cancelInvoiceId)
{
	global $cfg, $mycms;

	$rowfetchrefund = array();
	$rowfetchrefund['QUERY']	  =	"SELECT * FROM " . _DB_CANCEL_INVOICE_ . "
										 WHERE `id` 	= ?";

	$rowfetchrefund['PARAM'][]   = array('FILD' => 'id',   'DATA' => $cancelInvoiceId,     'TYP' => 's');

	$rowfetchrefunddetails		  =	$mycms->sql_select($rowfetchrefund);
	$rowfetchretaurn     		  = $rowfetchrefunddetails[0];
	return $rowfetchretaurn;
}

function countOfInvoiceDelegatePlusAccompany($delegateId)
{
	global $cfg, $mycms;
	$sqlfetchinvoice['QUERY']		=	"SELECT * FROM " . _DB_INVOICE_ . "
												 WHERE `delegate_id` 	= '" . $delegateId . "'
													AND (`payment_status` = 'PAID' OR `payment_status` = 'COMPLIMENTARY' OR `payment_status` = 'ZERO_VALUE')
													AND (`service_type` = 'DELEGATE_CONFERENCE_REGISTRATION' OR `service_type` = 'ACCOMPANY_CONFERENCE_REGISTRATION')
													AND `status` = 'A'";
	$resultfetchinvoice		  =	$mycms->sql_select($sqlfetchinvoice);
	$maxRowsInvoice           = $mycms->sql_numrows($resultfetchinvoice);
	return $maxRowsInvoice;
}

function countOfDinnerInvoices($delegateId)
{
	global $cfg, $mycms;
	$sqlfetchinvoice['QUERY']		=	"SELECT * FROM " . _DB_INVOICE_ . "
												  WHERE `delegate_id` 	= '" . $delegateId . "'
													AND `service_type` = 'DELEGATE_DINNER_REQUEST'
													AND `payment_status` != 'UNPAID'
													AND `status` = 'A'";
	$resultfetchinvoice		  =	$mycms->sql_select($sqlfetchinvoice);
	$maxRowsInvoice           = $mycms->sql_numrows($resultfetchinvoice);
	return $maxRowsInvoice;
}

function invoiceDiscountAmountOfSlip($slipId, $onlyActive = false)
{
	global $cfg, $mycms;
	$condition = "";
	if ($onlyActive) {
		$condition = " AND status IN ('A','C')";
	} else {
		$condition = " AND status = 'A'";
	}
	$sqlInvoice['QUERY'] = "SELECT SUM(`discount_amount`) AS totalDiscountAmount 
	 				  FROM  " . _DB_INVOICE_ . "
	 				  WHERE  `slip_id` = ? " . $condition . "";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	  'DATA' => $slipId,        'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$rowInvoice = $resInvoice[0];

	$totalSlipAmount['totalDiscountAmount'] = $rowInvoice['totalDiscountAmount'];
	return $totalSlipAmount['totalDiscountAmount'];
}

function getInvoiceCurrency($slipId = "")
{
	global $cfg, $mycms;

	$$sqlRegClasf 	=	array();
	$sqlRegClasf['QUERY'] = "SELECT DISTINCT `currency`,`slip_id` FROM " . _DB_INVOICE_ . " WHERE status = 'A' AND `slip_id` = '" . $slipId . "'";
	$resRegClasf			= $mycms->sql_select($sqlRegClasf);

	return strip_tags($resRegClasf[0]['currency']);
}

function getInvoiceCurrencyById($invoiceId = "")
{
	global $cfg, $mycms;

	$$sqlRegClasf 	=	array();
	$sqlRegClasf['QUERY'] = "SELECT DISTINCT `currency`,`id` FROM " . _DB_INVOICE_ . " WHERE status IN ('A','C') AND `id` = '" . $invoiceId . "'";
	$resRegClasf			= $mycms->sql_select($sqlRegClasf);

	return strip_tags($resRegClasf[0]['currency']);
}

function getRegistrationInvoiceCancelInvoiceDetails($invoiceId = "", $delegateId = "", $slipId = "", $alterCondition = '')
{
	global $cfg, $mycms;

	$filterCondition           = "";

	if ($invoiceId != "") {
		$filterCondition      .= " AND inv.id = '" . $invoiceId . "'";
	}

	if ($delegateId != "") {
		$filterCondition      .= " AND inv.delegate_id = '" . $delegateId . "'";
	}

	if ($slipId != "") {
		$filterCondition      .= " AND inv.slip_id = '" . $slipId . "'";
	}
	$sqlGetInvoiceDtls 			=	array();
	$sqlGetInvoiceDtls['QUERY'] = "SELECT inv.*,invCancel.refunded_amount,invCancel.Refund_status 
									 FROM " . _DB_INVOICE_ . " inv
						
						   LEFT OUTER JOIN " . _DB_CANCEL_INVOICE_ . " invCancel
								ON inv.id = invCancel.invoice_id
								AND invCancel.status = 'A'
								
								WHERE inv.status IN('A') " . $filterCondition . $alterCondition . " ORDER BY inv.id ASC";

	return 	$sqlGetInvoiceDtls;
}

function getInvoiceWithCancelInvoiceDetails($invoiceId = "", $delegateId = "", $slipId = "", $alterCondition = '')
{
	global $cfg, $mycms;

	$filterCondition           = "";

	if ($invoiceId != "") {
		$filterCondition      .= " AND inv.id = '" . $invoiceId . "'";
	}

	if ($delegateId != "") {
		$filterCondition      .= " AND inv.delegate_id = '" . $delegateId . "'";
	}

	if ($slipId != "") {
		$filterCondition      .= " AND inv.slip_id = '" . $slipId . "'";
	}

	$sqlGetInvoiceDtls['QUERY'] = "SELECT inv.*,invCancel.refunded_amount,invCancel.Refund_status 
									 FROM " . _DB_INVOICE_ . " inv
						
						   LEFT OUTER JOIN " . _DB_CANCEL_INVOICE_ . " invCancel
								ON inv.id = invCancel.invoice_id
								AND invCancel.status = 'A'
								
								WHERE inv.status IN('A','C') " . $filterCondition . $alterCondition . " ORDER BY inv.id ASC";

	return 	$sqlGetInvoiceDtls;
}

function insertingMultiPaymentDetails($paymentDetailsArray)
{
	global $cfg, $mycms;

	$paymentId = array();

	foreach ($paymentDetailsArray as $key => $paymentDetails) {
		$sqlInsertSlipRequest  = array();
		$sqlInsertSlipRequest['QUERY'] 	       = "INSERT INTO " . _DB_PAYMENT_ . " 
												  SET `delegate_id`	   	 			  = ?,
													  `slip_id` 		 	 		  = ?, 
													  `payment_mode` 	  			  = ?, 
													  `payment_date` 	 	 		  = ?, 
													  `cash_deposit_date` 			  = ?, 
													  `card_payment_date`	 	 	  = ?,
													  `card_transaction_no` 		  = ?,
													  `cheque_number`	 		  	  = ?, 
													  `cheque_date`	 				  = ?,
													  `cheque_bank_name`  			  = ?, 
													  `draft_number` 				  = ?,
													  `draft_date` 	 	 		      = ?, 
													  `draft_bank_name` 			  = ?, 
													  `neft_bank_name`	 	 		  = ?,
													  `neft_transaction_no` 		  = ?,
													  `neft_date`	 		 		  = ?, 
													  `rtgs_bank_name`	 			  = ?,
													  `rtgs_transaction_no`  		  = ?, 
													  `rtgs_date` 					  = ?,
													  `credit_date`					  =	?,
													  `upi_date`					  =	?,
													  `upi_bank_name`				  =	?,
													  `txn_no`					  	  =	?,
													  `exhibitor_code`				  = ?,
													  `currency` 	 	 		      = ?, 
													  `amount` 	 	 		      	  = ?, 
													  `payment_remark` 		 		  = ?,
													  `payment_status` 		 		  = ?,
													  `status` 				 		  = ?,
													  `created_ip`			 		  = ?,
													  `created_sessionId`	 		  = ?,
													  `created_browser`  	 		  = ?,
													  `created_dateTime` 	 		  = ?";

		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'delegate_id',                'DATA' => $paymentDetails['delegate_id'],           'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'slip_id',                    'DATA' => $paymentDetails['slip_id'],               'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_mode',               'DATA' => $paymentDetails['payment_mode'],          'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_date',               'DATA' => $paymentDetails['payment_date'],          'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cash_deposit_date',          'DATA' => $paymentDetails['cash_deposit_date'],     'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'card_payment_date',          'DATA' => $paymentDetails['card_payment_date'],     'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'card_transaction_no',        'DATA' => $paymentDetails['card_transaction_no'],   'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_number',              'DATA' => $paymentDetails['cheque_number'],         'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_date',                'DATA' => $paymentDetails['cheque_date'],           'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'cheque_bank_name',           'DATA' => $paymentDetails['cheque_bank_name'],      'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_number',               'DATA' => $paymentDetails['draft_number'],          'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_date',                 'DATA' => $paymentDetails['draft_date'],            'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'draft_bank_name',            'DATA' => $paymentDetails['draft_bank_name'],       'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_bank_name',             'DATA' => $paymentDetails['neft_bank_name'],        'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_transaction_no',        'DATA' => $paymentDetails['neft_transaction_no'],   'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'neft_date',                  'DATA' => $paymentDetails['neft_date'],             'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_bank_name',             'DATA' => $paymentDetails['rtgs_bank_name'],        'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_transaction_no',        'DATA' => $paymentDetails['rtgs_transaction_no'],   'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'rtgs_date',                  'DATA' => $paymentDetails['rtgs_date'],             'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'credit_date',                'DATA' => $paymentDetails['credit_date'],           'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'upi_date',                   'DATA' => $paymentDetails['upi_date'],           	'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'upi_bank_name',              'DATA' => $paymentDetails['upi_bank_name'],          'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'txn_no',                	   'DATA' => $paymentDetails['txn_no'],           'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'exhibitor_code',             'DATA' => $paymentDetails['exhibitor_code'],        'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'currency',                   'DATA' => $paymentDetails['currency'],              'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'amount',                     'DATA' => $paymentDetails['amount'],                'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_remark',             'DATA' => $paymentDetails['payment_remark'],        'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'payment_status',             'DATA' => $paymentDetails['payment_status'],        'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'status',                     'DATA' => 'A',                                       'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_ip',                 'DATA' => $_SERVER['REMOTE_ADDR'],                   'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_sessionId',          'DATA' => session_id(),                              'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_browser',            'DATA' => $_SERVER['HTTP_USER_AGENT'],               'TYP' => 's');
		$sqlInsertSlipRequest['PARAM'][]   = array('FILD' => 'created_dateTime',           'DATA' => date('Y-m-d H:i:s'),                       'TYP' => 's');

		$lastInsertedPaymentId    = $mycms->sql_insert($sqlInsertSlipRequest, false);
		$paymentId[] 			  = $lastInsertedPaymentId;
	}
	return $paymentId;
}

function getTotalPaidAmount($slipId)
{
	global $cfg, $mycms;

	$sqlPayment 	=	array();
	$sqlPayment['QUERY'] = "SELECT SUM(amount) as totalAmount
							 FROM " . _DB_PAYMENT_ . "
							WHERE `slip_id` = ?
							 AND `payment_status` = ?
							  AND `status` = ?";

	$sqlPayment['PARAM'][]   = array('FILD' => 'slip_id',         'DATA' => $slipId,   'TYP' => 's');
	$sqlPayment['PARAM'][]   = array('FILD' => 'payment_status',  'DATA' => 'PAID',    'TYP' => 's');
	$sqlPayment['PARAM'][]   = array('FILD' => 'status',          'DATA' => 'A',   	  'TYP' => 's');

	$resPayment = $mycms->sql_select($sqlPayment);
	$totalAmount = $resPayment[0]['totalAmount'];

	return $totalAmount;
}

function getTotalSetPaymentAmount($slipId)
{
	global $cfg, $mycms;

	$sqlPayment 	=	array();
	$sqlPayment['QUERY'] = "SELECT SUM(amount) as totalAmount
							 FROM " . _DB_PAYMENT_ . "
							WHERE `slip_id` = ?
							  AND `status` = ?";

	$sqlPayment['PARAM'][]   = array('FILD' => 'slip_id',         'DATA' => $slipId,   'TYP' => 's');
	$sqlPayment['PARAM'][]   = array('FILD' => 'status',          'DATA' => 'A',   	  'TYP' => 's');

	$resPayment = $mycms->sql_select($sqlPayment);
	$totalAmount = $resPayment[0]['totalAmount'];

	return $totalAmount;
}

function unpaidCountOfPaymnet($slipId)
{
	global $cfg, $mycms;

	$sqlPayment = array();
	$sqlPayment['QUERY'] = "SELECT COUNT(*) AS totalUnpaid
							  FROM  " . _DB_PAYMENT_ . "
							  WHERE  `slip_id` =?
								AND `payment_status` = ?
								AND `status` = ?";

	$sqlPayment['PARAM'][]   = array('FILD' => 'slip_id',        'DATA' => $slipId,  'TYP' => 's');
	$sqlPayment['PARAM'][]   = array('FILD' => 'payment_status', 'DATA' => 'UNPAID', 'TYP' => 's');
	$sqlPayment['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',      'TYP' => 's');
	$resPayment = $mycms->sql_select($sqlPayment);
	return $resPayment[0]['totalUnpaid'];
}


/////// wallet ////
function userWalletBalance($customerId)
{
	global $cfg, $mycms;

	$balance         = 0;
	$transaction	 = 0;

	$sql['QUERY']             = "SELECT IFNULL(SUM(amount),0.00) AS balance 
						  		   FROM " . _DB_WALLET_IN_ . " WHERE `delegate_id`='" . $customerId . "' AND `status`='A'";

	$result          = $mycms->sql_select($sql);
	if ($result) {
		$row             = $result[0];
		$balance         = $row['balance'];
	}

	$sql1['QUERY']            = "SELECT IFNULL(SUM(amount),0.00) AS transaction 
						  		   FROM " . _DB_WALLET_OUT_ . " WHERE `delegate_id`='" . $customerId . "' AND `status`='A'";

	$result1         = $mycms->sql_select($sql1);
	if ($result1) {
		$row1            = $result1[0];
		$transaction     = $row1['transaction'];
	}
	$wallet          = floatval($balance) - floatval($transaction);


	return $wallet;
}

function walletResponseReimbursementProcess($request_id, $reimburse_amount, $reimburse_date, $reimburse_remarks, $reimburse_through, $reimburse_transaction_no)
{
	global $cfg, $mycms;

	$sqlRequestUpdate['QUERY']    =  "UPDATE " . _DB_WALLET_REIMBURSE_REQUEST_ . " 
										 SET `reimbursement_status` = 'Reimbursed',
											 `reimburse_amount` = '" . $reimburse_amount . "',
											 `reimburse_date` = '" . $reimburse_date . "',
											 `reimburse_remarks` = '" . $reimburse_remarks . "',
											 `reimburse_through` = '" . $reimburse_through . "',
											 `reimburse_transaction_no` = '" . $reimburse_transaction_no . "'
									   WHERE `id`='" . $request_id . "'";
	$mycms->sql_update($sqlRequestUpdate);

	$sqlRequest['QUERY']    =  " SELECT req.*
						  		   FROM " . _DB_WALLET_REIMBURSE_REQUEST_ . " req
						 		  WHERE req.`id` = '" . $id . "'";
	$requestDetails = $mycms->sql_select($sqlRequest);
	$rowRequestDetails = $requestDetails[0];

	$sqlCustomer['QUERY']              =  "SELECT * 
											 FROM " . _DB_USER_REGISTRATION_ . " 
										    WHERE `id` = '" . $rowRequestDetails['delegate_id'] . "'";
	$resultFetchCustomer   	  = $mycms->sql_select($sqlCustomer);
	$rowFetchCustomer      	  = $resultFetchCustomer[0];
}

function walletOutProcess($customer_id, $wallet_amount, $wallet_remarks, $invoiceid = '')
{
	global $cfg, $mycms;

	if ($invoiceid == '') {
		$invoiceid = 'NULL';
	} else {
		$invoiceid = "'" . $invoiceid . "'";
	}
	$sqlCustomerInsert['QUERY']    =  "   INSERT INTO " . _DB_WALLET_OUT_ . " 
												  SET `delegate_id` = '" . $customer_id . "',
													  `invoiceId` = " . $invoiceid . ",
													  `amount` = '" . $wallet_amount . "',
													  `remarks` = '" . $wallet_remarks . "',
													  `status` = 'A',
													  `transaction_date` = '" . date('Y-m-d H:i:s') . "',
													  `created_by` = NULL,
													  `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
													  `created_sessionId` = '" . session_id() . "',
													  `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
	$last_wallet_id = $mycms->sql_insert($sqlCustomerInsert, false);

	$transactionNo = 'WLT/OUT' . '/' . date('dmy') . '/' . number_pad($last_wallet_id, 3);

	$sqlCustomerUpdate['QUERY']    =  "   UPDATE " . _DB_WALLET_OUT_ . " 
											 SET `transaction_no` = '" . $transactionNo . "'
										   WHERE `id`='" . $last_wallet_id . "'";
	$mycms->sql_update($sqlCustomerUpdate, false);

	$sqlCustomer['QUERY']              =  " SELECT * FROM " . _DB_USER_REGISTRATION_ . " 
								  			 WHERE `id` = '" . $customer_id . "'";
	$resultFetchCustomer   	  = $mycms->sql_select($sqlCustomer);
	$rowFetchCustomer      	  = $resultFetchCustomer[0];
}

function walletInProcess($customer_id, $wallet_amount, $wallet_remarks, $invoiceid = '')
{
	global $cfg, $mycms;
	$loggedUserID = $mycms->getLoggedUserId();
	if ($invoiceid == '') {
		$invoiceid = 'NULL';
	} else {
		$invoiceid = "'" . $invoiceid . "'";
	}

	$sqlCustomerInsert['QUERY']    =  "   INSERT INTO " . _DB_WALLET_IN_ . " 
												  SET `delegate_id` = '" . $customer_id . "',
													  `invoiceId` = " . $invoiceid . ",
													  `amount` = '" . $wallet_amount . "',
													  `remarks` = '" . $wallet_remarks . "',
													  `status` = 'A',
													  `transaction_date` = '" . date('Y-m-d H:i:s') . "',
													  `created_by` = '" . $loggedUserID . "',
													  `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
													  `created_sessionId` = '" . session_id() . "',
													  `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
	$last_wallet_id = $mycms->sql_insert($sqlCustomerInsert);

	$transactionNo = 'WLT/IN' . '/' . date('dmy') . '/' . number_pad($last_wallet_id, 3);

	$sqlCustomerUpdate['QUERY']    =  "   UPDATE " . _DB_WALLET_IN_ . " 
											 SET `transaction_no` = '" . $transactionNo . "'
										   WHERE `id`='" . $last_wallet_id . "'";
	$mycms->sql_update($sqlCustomerUpdate);

	$sqlCustomer['QUERY']              =  "SELECT * FROM " . _DB_USER_REGISTRATION_ . " 
								   			WHERE `id` = '" . $customer_id . "'";
	$resultFetchCustomer   	  = $mycms->sql_select($sqlCustomer);
	$rowFetchCustomer      	  = $resultFetchCustomer[0];
}

/////// cancel //

function cancelOnlyInvoice($invoiceId)
{
	global $cfg, $mycms;
	$sqlUpdate			=	array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_INVOICE_ . "
							 SET `status` = ?
						   WHERE `id` = ?";
	$sqlUpdate['PARAM'][]   = array('FILD' => 'status',     'DATA' => 'C',               					  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',         'DATA' => $invoiceId,                			  'TYP' => 's');
	$mycms->sql_update($sqlUpdate, false);
}

////// MISC ////
function getInvoicedServiceTypesOfDelegate($delegateId, $status = false)
{
	global $cfg, $mycms;

	if ($status) {
		$condition = " AND invDetails.status IN (?,?)";
		$conditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' => 'A',  'TYP' => 's');
		$conditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' => 'C',  'TYP' => 's');
	} else {
		$condition = " AND invDetails.status IN (?)";
		$conditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' => 'A',  'TYP' => 's');
	}

	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = "SELECT invDetails.*, invDetails.delegate_id AS delegateId, slipDetails.slip_number AS slipNO 
							 FROM  " . _DB_INVOICE_ . " invDetails
				  LEFT OUTER JOIN " . _DB_SLIP_ . " slipDetails
							   ON slipDetails.id = invDetails.slip_id
							WHERE invDetails.delegate_id =? 
								  " . $condition;
	$sqlInvoice['PARAM'][]   = array('FILD' => 'invDetails.id',  'DATA' => $delegateId, 'TYP' => 's');
	foreach ($conditionPARAM as $k => $val) {
		$sqlInvoice['PARAM'][] = $val;
	}

	$resInvoice = $mycms->sql_select($sqlInvoice);

	$return = array();
	foreach ($resInvoice as $kk => $row) {
		$return[] = $row['service_type'];
	}
	return $return;
}


function updateBulkRegInvoice($companyCode,$invoiceId)
{
	global $mycms, $cfg;

	$sqlUpdate['QUERY']	                      = "SELECT * FROM " . _DB_EXIBITOR_COMPANY_ . "
										  WHERE `exhibitor_company_code` = '" . $companyCode . "' ";

	$resCompany = $mycms->sql_select($sqlUpdate, false);
	$rowCompanyDetails =	$resCompany[0];
	// $invoiceId = $rowCompanyDetails['refference_invoice_id'];

	$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_EXIBITOR_INVOICE_ . "
										  WHERE `id` = '" . $invoiceId . "'";
	$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
	$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];

	if ($rowDetailsfetchInvoice['service_type'] == "EXHIBITOR_COMPANY_REGISTRATION" || $rowDetailsfetchInvoice['service_type'] == "EXHIBITOR_REPRESENTATIVE_REGISTRATION") {
		$cgst = $cfg['EXHIBITOR.CGST'];
		$sgst = $cfg['EXHIBITOR.SGST'];

		$gstArray = gstPostAddedCalculation($cgst, $sgst, $rowDetailsfetchInvoice['service_unit_price']);	// *****  amount + GST
	}

	$invoice_price_arr = calculateExhibitorBulkRegAmount($companyCode,$invoiceId,true);
	if($invoice_price_arr==null){
		echo '<script>alert("Something went wrong!");</script>';
	}

	$totalGstPrice = $invoice_price_arr['CGST.AMOUNT'] + $invoice_price_arr['SGST.AMOUNT'];


	$sqlUpdateInvoice['QUERY'] = "UPDATE " . _DB_EXIBITOR_INVOICE_ . "
								 SET  `service_consumed_quantity`= '" . $invoice_price_arr['COUNT'] . "', 
									  `cgst_price` 	  	 		 = '" . $invoice_price_arr['CGST.AMOUNT'] . "', 
									  `sgst_price`	 	 	     = '" . $invoice_price_arr['SGST.AMOUNT'] . "',
									  `service_basic_price` 	 = '" . $invoice_price_arr['BASIC.AMOUNT'] . "', 
									  `service_gst_price`	 	 = '" . $invoice_price_arr['TOTAL.AMOUNT'] . "',
									  
									  `service_grand_price`  	 = '" . $invoice_price_arr['TOTAL.AMOUNT'] . "',
									  `service_roundoff_price`   = '" . round($invoice_price_arr['TOTAL.AMOUNT'], 0) . "',
									  
									  `has_gst`  				 = 'Y'
									 
							   WHERE `id` = '" . $invoiceId . "'";
							//    echo '<pre>'; print_r($sqlUpdateInvoice);die;
	$mycms->sql_update($sqlUpdateInvoice, false);

	
}

?>