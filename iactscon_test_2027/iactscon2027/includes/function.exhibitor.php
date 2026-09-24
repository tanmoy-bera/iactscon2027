<?php
function getExhibitorDetails($exhibitorId = "", $searchCondition = "")
{
	global $mycms, $cfg;

	$filterCondition        = "";

	if ($exhibitorId != "") {
		$filterCondition   .= " AND exhibitor.id = '" . $exhibitorId . "'";
	}

	if ($searchCondition == "from_insertingInvoiceDetails") {
		$searchCondition    = "";
	} else {
		$searchCondition   .= " AND invoice.status = 'A' ";
	}

	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT exhibitor.id AS exhibitor_company_id,exhibitor.payment_status AS exhibitorPaymentStatus, exhibitor.*,country.country_name AS countryName,
											state.state_name AS stateName, exTariff.*, slip.id AS slip_id, slip.payment_status AS slipPaymentStatus,
											slip.*,invoice.id AS invoice_id, invoice.payment_status AS invoicePaymentStatus, invoice.*,exTariff.reg_classification_id,
											invoice.currency AS invoice_currency, payment.id AS payment_id, payment.payment_status AS paymentStatus, payment.*										
											
										FROM " . _DB_EXIBITOR_COMPANY_ . " exhibitor																	
											
										INNER JOIN " . _DB_COMN_COUNTRY_ . " country
											ON exhibitor.exhibitor_company_country = country.country_id
			
										INNER JOIN " . _DB_COMN_STATE_ . "	state
											ON exhibitor.exhibitor_company_state = state.st_id	
		
										INNER JOIN " . _DB_EXIBITOR_REGISTRATION_TARIFF_ . "	exTariff
											ON exTariff.reg_classification_id = exhibitor.registraion_tariff_id  
										
										LEFT OUTER JOIN " . _DB_EXIBITOR_SLIP_ . "	slip
											ON exhibitor.id = slip.exhibitor_id
										
										LEFT OUTER JOIN " . _DB_EXIBITOR_INVOICE_ . " invoice
											ON invoice.exhibitor_id = exhibitor.id
										
										LEFT OUTER JOIN " . _DB_EXIBITOR_PAYMENT_ . " payment 
											ON payment.exhibitor_id	= exhibitor.id
										
											WHERE exhibitor.status = 'A'										
											AND country.status = 'A'
											AND state.status = 'A'
											AND exTariff.status = 'A'
											AND slip.status = 'A' " . $searchCondition . " " . $filterCondition . " ORDER BY exhibitor.id DESC ";

	return 	$sqlExhibitorDetails;
}

function getExhibitorRegistrationDetails($company_code = "", $searchCondition = "")
{
	global $mycms, $cfg;

	$filterCondition        = "";

	if ($company_code != "") {
		// $filterCondition   .= " AND reg.id = '" . $exhibitorId . "'";
	}

	if ($searchCondition == "from_insertingInvoiceDetails") {
		$searchCondition    = "";
	} else {
		// $searchCondition   .= " AND invoice.status = 'A' ";
	}

	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT reg.*, comp.`exhibitor_company_code`  FROM " . _DB_EXHIBITOR_REGISTRATION_ . " reg																	
											
										INNER JOIN " . _DB_EXIBITOR_COMPANY_ . " comp
											ON reg.exhibitor_company_code = comp.exhibitor_company_code
											WHERE reg.status = 'A'										
											AND reg.status = 'A'
											AND reg.exhibitor_company_code = '" . $company_code . "' "
		. $searchCondition . " " . $filterCondition . " ORDER BY reg.id DESC ";

	return 	$sqlExhibitorDetails;
}

function calculateExhibitorBulkRegAmount($company_code, $invoiceId = 0, $part_invoice = false)
{
	include_once("function.invoice.php");
	include_once('function.delegate.php');
	include_once('function.exhibitor.php');
	include_once('function.accommodation.php');

	global $mycms, $cfg;
	$displayData 	 = array();
	if ($part_invoice == true) {
		$part_invoice_condition = "AND exb.`invoice_status`= 'I'	";
	} else {
		$part_invoice_condition = "";
	}

	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT exb.*, user.`refference_delegate_id`  FROM " . _DB_EXHIBITOR_REGISTRATION_ . " exb																	
											
										INNER JOIN " . _DB_USER_REGISTRATION_ . " user
											ON exb.delegate_id = user.id
											WHERE exb.status = 'A' AND exb.`account_status`= 'REGISTERED' " . $part_invoice_condition . " 									
											AND user.status = 'A'
											AND exb.exhibitor_company_code = '" . $company_code . "' 
											  ORDER BY exb.id DESC ";

	$resExhibitorDetails      = $mycms->sql_select($sqlExhibitorDetails);
	// echo '<pre>';
	// print_r($resExhibitorDetails);
	// die;
	if (sizeof($resExhibitorDetails) > 0) {
		$overall_amount = 0;

		foreach ($resExhibitorDetails as $key => $rowExhibitorDetails) {
			$rowtariff = calculateExhibitorBulkRegAmountPerUser($rowExhibitorDetails['id']);

			$user_details = getUserDetails($rowExhibitorDetails['delegate_id']);
			$company_detail = getExhibitorCompanyDetails("", $rowExhibitorDetails['exhibitor_company_code']);

			$total = $rowtariff['TOTAL'];

			// $workshop_id_arr = json_decode($rowtariff['workshop_id']);

			// $workshop_price_arr = json_decode($rowtariff['workshop_tariff_inr']);

			// for ($i = 0; $i < count($workshop_id_arr); $i++) {
			// 	if (intval($workshop_id_arr[$i]) == json_decode($rowExhibitorDetails['workshop_id'])) {
			// 		$workshop_price = $workshop_price_arr[$i];
			// 		$total += $workshop_price;
			// 	}
			// }
			// if ($rowExhibitorDetails['accompanyCount'] > 0) {
			// 	$accomp_price = $rowtariff['accompany_tariff_inr'] * $rowExhibitorDetails['accompanyCount'];
			// 	$total += $accomp_price;
			// }
			if ($rowExhibitorDetails['hotel_id'] != '') {
				if ($rowExhibitorDetails['isPackageAccom'] == 'N') {

					$checkinDate = new DateTime(getCheckInDateById($rowExhibitorDetails['checkin_id'], $rowExhibitorDetails['hotel_id']));
					$checkoutDate = new DateTime(getCheckOutDateById($rowExhibitorDetails['checkout_id'], $rowExhibitorDetails['hotel_id']));

					$diff = $checkinDate->diff($checkoutDate);
					$daysDiff = $diff->days;
					$hotel_id_arr = json_decode($rowtariff['hotel_id']);

					$hotel_room_arr = json_decode($rowtariff['room_type']);
					$accommodation_tariff_inr_arr = json_decode($rowtariff['accommodation_tariff_inr']);

					for ($i = 0; $i < count($hotel_id_arr); $i++) {
						if (intval($hotel_id_arr[$i]) == json_decode($rowExhibitorDetails['hotel_id'])) {
							if ($hotel_room_arr[$i] == 0 && $rowExhibitorDetails['accommodation_room'] == '') {
								$accomd_price = $accommodation_tariff_inr_arr[$i] * $daysDiff;
								$total += $accomd_price;
							} else if ($hotel_room_arr[$i] == $rowExhibitorDetails['accommodation_room']) {

								$accomd_price = $accommodation_tariff_inr_arr[$i] * $daysDiff;
								$total += $accomd_price;
							}
						}
					}
				}
			}
			$displayData['INDIVIDUAL.AMOUNT'][$rowExhibitorDetails['delegate_id']] = $total;
			$overall_amount += $total;

			if ($part_invoice == true) {
				$sqlInvUpdate = array();
				$sqlInvUpdate['QUERY'] = "UPDATE " . _DB_EXHIBITOR_REGISTRATION_ . " 
							SET `invoice_status` = ?
							WHERE  `id`=? ";

				$sqlInvUpdate['PARAM'][] = array('FILD' => 'invoice_status',          'DATA' => 'A', 'TYP' => 's');
				$sqlInvUpdate['PARAM'][] = array('FILD' => 'id',          'DATA' => $rowExhibitorDetails['id'], 'TYP' => 's');
				// echo '<pre>'; print_r($sqlInvUpdate); die;
				$mycms->sql_update($sqlInvUpdate);
			}
		}


		// $displayData['TOTAL.AMOUNT'] = intToFloat($overall_amount);
		$displayData['COUNT'] = count($resExhibitorDetails);

		if ($invoiceId != 0) {
			$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_EXIBITOR_INVOICE_ . "
			WHERE `id` = '" . $invoiceId . "'";
			$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
			$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];
			if ($rowDetailsfetchInvoice['gst_calculation'] == 'added') {
				$inclusive = false;
			} else {
				$inclusive = true;
			}
			if ($rowDetailsfetchInvoice['gst_flag'] == 2) {
				$gstArray  = gstCalculation($rowDetailsfetchInvoice['igst_percentage'] / 2, $rowDetailsfetchInvoice['igst_percentage'] / 2, $overall_amount, $inclusive);
			} else {
				$gstArray  = gstCalculation($rowDetailsfetchInvoice['cgst_percentage'], $rowDetailsfetchInvoice['sgst_percentage'], $overall_amount, $inclusive);
			}
		} else {
			$gstArray  = gstCalculation($cfg['CONFERENCE.CGST'], $cfg['CONFERENCE.SGST'], $overall_amount, $inclusive);
		}

		if ($cfg['GST.FLAG'] == 3) {
			$displayData['BASIC.AMOUNT'] = intToFloat($gstArray['GST.PRICE']); //without gst
		} else {
			$displayData['BASIC.AMOUNT'] = intToFloat($gstArray['BASIC.PRICE']);
		}

		$displayData['CGST.AMOUNT'] = intToFloat($gstArray['CGST.PRICE']);
		$displayData['SGST.AMOUNT'] = intToFloat($gstArray['SGST.PRICE']);
		$displayData['TOTAL.AMOUNT'] = intToFloat($gstArray['GST.PRICE']);

		// echo '<pre>';
		// print_r($gstArray);
		// die;

		return $displayData;
	} else {
		return null;
	}
}

function calculateExhibitorBulkRegAmountFront($company_code)
{
	include_once("function.invoice.php");
	include_once('function.delegate.php');
	include_once('function.exhibitor.php');
	include_once('function.accommodation.php');

	global $mycms, $cfg;
	$displayData 	 = array();
	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT exb.* FROM " . _DB_EXHIBITOR_REGISTRATION_ . " exb																	
											WHERE exb.status = 'A' 									
											AND exb.exhibitor_company_code = '" . $company_code . "' 
											  ORDER BY exb.id DESC ";

	$resExhibitorDetails      = $mycms->sql_select($sqlExhibitorDetails);
	// echo '<pre>';
	// print_r($resExhibitorDetails);
	// die;
	if (sizeof($resExhibitorDetails) > 0) {
		$overall_amount = 0;

		foreach ($resExhibitorDetails as $key => $rowExhibitorDetails) {
			$tariffDetails = getExhibitortariff($rowExhibitorDetails['exhibitor_company_code'], $rowExhibitorDetails['registration_classification_id']);
			$rowtariff = $tariffDetails[0];

			$total = $rowtariff['registration_tariff_inr'];

			$workshop_id_arr = json_decode($rowtariff['workshop_id']);

			$workshop_price_arr = json_decode($rowtariff['workshop_tariff_inr']);

			for ($i = 0; $i < count($workshop_id_arr); $i++) {
				if (intval($workshop_id_arr[$i]) == json_decode($rowExhibitorDetails['workshop_id'])) {
					$workshop_price = $workshop_price_arr[$i];
					$total += $workshop_price;
				}
			}
			if ($rowExhibitorDetails['accompanyCount'] > 0) {
				$accomp_price = $rowtariff['accompany_tariff_inr'] * $rowExhibitorDetails['accompanyCount'];
				$total += $accomp_price;
			}
			if ($rowExhibitorDetails['hotel_id'] != '') {
				if ($rowExhibitorDetails['isPackageAccom'] == 'N') {

					$checkinDate = new DateTime(getCheckInDateById($rowExhibitorDetails['checkin_id'], $rowExhibitorDetails['hotel_id']));
					$checkoutDate = new DateTime(getCheckOutDateById($rowExhibitorDetails['checkout_id'], $rowExhibitorDetails['hotel_id']));

					$diff = $checkinDate->diff($checkoutDate);
					$daysDiff = $diff->days;
					$hotel_id_arr = json_decode($rowtariff['hotel_id']);

					$hotel_room_arr = json_decode($rowtariff['room_type']);
					$accommodation_tariff_inr_arr = json_decode($rowtariff['accommodation_tariff_inr']);

					for ($i = 0; $i < count($hotel_id_arr); $i++) {
						if (intval($hotel_id_arr[$i]) == json_decode($rowExhibitorDetails['hotel_id'])) {
							if ($hotel_room_arr[$i] == 0 && $rowExhibitorDetails['accommodation_room'] == '') {
								$accomd_price = $accommodation_tariff_inr_arr[$i] * $daysDiff;
								$total += $accomd_price;
							} else if ($hotel_room_arr[$i] == $rowExhibitorDetails['accommodation_room']) {

								$accomd_price = $accommodation_tariff_inr_arr[$i] * $daysDiff;
								$total += $accomd_price;
							}
						}
					}
				}
			}
			$displayData['INDIVIDUAL.AMOUNT'][$rowExhibitorDetails['id']] = $total;
			$overall_amount += $total;
		}


		$displayData['TOTAL.AMOUNT'] = intToFloat($overall_amount);
		$displayData['COUNT'] = count($resExhibitorDetails);

		if ($invoiceId != 0) {
			$sqlfetchInvoice['QUERY']	      = "SELECT * FROM " . _DB_EXIBITOR_INVOICE_ . "
			WHERE `id` = '" . $invoiceId . "'";
			$rowsqlfetchInvoice      = $mycms->sql_select($sqlfetchInvoice);
			$rowDetailsfetchInvoice  = $rowsqlfetchInvoice[0];
			if ($rowDetailsfetchInvoice['gst_flag'] == 2) {
				$gstArray  = gstCalculation($rowDetailsfetchInvoice['igst_percentage'] / 2, $rowDetailsfetchInvoice['igst_percentage'] / 2, $overall_amount);
			} else {
				$gstArray  = gstCalculation($rowDetailsfetchInvoice['cgst_percentage'], $rowDetailsfetchInvoice['sgst_percentage'], $overall_amount);
			}
		} else {
			$gstArray  = gstCalculation($cfg['CONFERENCE.CGST'], $cfg['CONFERENCE.SGST'], $overall_amount);
		}

		if ($cfg['GST.FLAG'] == 3) {
			$displayData['BASIC.AMOUNT'] = intToFloat($gstArray['GST.PRICE']); //without gst
		} else {
			$displayData['BASIC.AMOUNT'] = intToFloat($gstArray['BASIC.PRICE']);
		}
		$displayData['CGST.AMOUNT'] = intToFloat($gstArray['CGST.PRICE']);
		$displayData['SGST.AMOUNT'] = intToFloat($gstArray['SGST.PRICE']);
		// echo '<pre>';
		// print_r($gstArray);
		// die;

		return $displayData;
	} else {
		return null;
	}
}
function calculateExhibitorBulkRegAmountPerUser($user_id)
{
	include_once("function.invoice.php");
	include_once('function.delegate.php');
	include_once('function.exhibitor.php');
	include_once('function.registration.php');
	include_once('function.accompany.php');
	include_once('function.workshop.php');

	global $mycms;
	$rowtariff = array();
	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT *  FROM " . _DB_EXHIBITOR_REGISTRATION_ . " 															
											WHERE id = '" . $user_id . "' AND status = 'A' ";

	$resultExhibitorUserDetails		= $mycms->sql_select($sqlExhibitorDetails);
	$rowExhibitorDetails = $resultExhibitorUserDetails[0];


	$allWorkshopDetails = getAllWorkshopRecord();
	$total = 0.00;
	// echo '<pre>'; print_r($tariffDetails);echo '</pre>';

	if ($rowExhibitorDetails['registration_tariff_cutoff_id'] != -1 && $rowExhibitorDetails['registration_tariff_cutoff_id'] != 0) {
		//===== general reg cutoff ====
		$workshopTariffDetails 		 = getAllWorkshopTariffs();
		$accompanyTariffDetails = getAllAccompanyTariffs();
		$registrationTariffDetails = getAllRegistrationTariffs($rowExhibitorDetails['registration_tariff_cutoff_id']);

		$rowtariff['registration_tariff_inr'] = $registrationTariffDetails[$rowExhibitorDetails['registration_classification_id']]['AMOUNT'];
		$rowtariff['accompany_tariff_inr'] = $accompanyTariffDetails[1][$rowExhibitorDetails['registration_tariff_cutoff_id']]['AMOUNT'] * $rowExhibitorDetails['accompanyCount'];

		foreach ($allWorkshopDetails as $key => $rowWorkshop) {
			$workshop_id_arr[$key] = $rowWorkshop['id'];
			$workshop_price_arr[$key] = $workshopTariffDetails[$rowWorkshop['id']][$rowExhibitorDetails['registration_classification_id']][$rowExhibitorDetails['registration_tariff_cutoff_id']]['INR'];
		}
		for ($i = 0; $i < count($workshop_id_arr); $i++) {
			if (intval($workshop_id_arr[$i]) == json_decode($rowExhibitorDetails['workshop_id'])) {
				$workshop_price = $workshop_price_arr[$workshop_id_arr[$i]];
				$rowtariff['workshop_price_inr'] = $workshop_price;
				$total += $workshop_price;
			}
		}
		$total += $rowtariff['registration_tariff_inr'];
		$total += $rowtariff['accompany_tariff_inr'];
	} else if ($rowExhibitorDetails['registration_tariff_cutoff_id'] == 0) {
		// ====== ZERO VALUE =============
		$rowtariff['registration_tariff_inr'] = 0.00;
		$rowtariff['accompany_tariff_inr'] = 0.00;
		foreach ($allWorkshopDetails as $key => $rowWorkshop) {
			$workshop_id_arr[$key] = $rowWorkshop['id'];
			$workshop_price_arr[$key] = 0.00;
		}
		$rowtariff['workshop_price_inr'] = 0.00;

		$total = 0.00;
	} else {
		// ======== CUSTOM TARIFF -1 ===============
		$tariffDetails = getExhibitortariff($rowExhibitorDetails['exhibitor_company_code'], $rowExhibitorDetails['registration_classification_id']);
		$rowtariff = $tariffDetails[0];
		$workshop_id_arr = json_decode($rowtariff['workshop_id']);
		$workshop_price_arr = json_decode($rowtariff['workshop_tariff_inr']);
		for ($i = 0; $i < count($workshop_id_arr); $i++) {
			if (intval($workshop_id_arr[$i]) == json_decode($rowExhibitorDetails['workshop_id'])) {
				$workshop_price = $workshop_price_arr[$i];
				$rowtariff['workshop_price_inr'] = $workshop_price;
				$total += $workshop_price;
			}
		}
		$rowtariff['accompany_tariff_inr'] = $rowtariff['accompany_tariff_inr'] * $rowExhibitorDetails['accompanyCount'];
		$total += $rowtariff['registration_tariff_inr'];
		$total += $rowtariff['accompany_tariff_inr'];
	}
	$rowtariff['TOTAL'] = $total;
	return $rowtariff;
}

function getExhibitorCompanyDetails($id = "", $company_code = "")
{
	global $mycms, $cfg;

	$filterCondition        = "";

	if ($id != "") {
		$filterCondition   .= " AND id = '" . $id . "'";
	}

	if ($company_code != "") {
		$filterCondition    = " AND exhibitor_company_code = '" . $company_code . "'";
	}

	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT * FROM " . _DB_EXIBITOR_COMPANY_ . " 
										WHERE `status`='A' " . $filterCondition;

	$resExhibitorDetails      = $mycms->sql_select($sqlExhibitorDetails);
	return $resExhibitorDetails;
}

function exhibitorCompanyQuerySet($exhibitorId = "", $searchCondition = "")
{
	global $cfg, $mycms;

	$filterCondition        = "";

	if ($exhibitorId != "") {
		$filterCondition   .= " AND exhibitor.id = '" . $exhibitorId . "'";
	}

	$sqlFetchExibitor    = array();
	$sqlFetchExibitor['QUERY']   = "SELECT exhibitor.*,
									  
									  country.country_name,
									  country.country_id,
									  state.state_name,
									  state.st_id,
									  orderCount.exhibitor_id,
									  userLimit.exhibitor_id,
									  registeredUserCount.exhibitor_id,
									  
									  IFNULL(totalBookingOrder, 0) AS totalBookingOrder,
									  IFNULL(totalUserLimit, 0) AS totalUserLimit,
									  IFNULL(totalRegisteredUser, 0) AS totalRegisteredUser  
	
								 FROM " . _DB_EXIBITOR_COMPANY_ . " exhibitor 
							
					  LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
								   ON exhibitor.exhibitor_company_country = country.country_id
										 
					  LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
								   ON exhibitor.exhibitor_company_state = state.st_id 
					  
					  LEFT OUTER JOIN (
					  
											SELECT COUNT(*) AS totalBookingOrder, 
												   exhibitor_id 
												   
											  FROM " . _DB_EXIBITOR_BOOKING_ORDER_ . "  
											 WHERE `status` = 'A'
											 
											 GROUP BY `exhibitor_id`
									
									  ) orderCount 
								   ON exhibitor.id = orderCount.exhibitor_id
					  
					  LEFT OUTER JOIN (
											
											SELECT SUM(booking_user_limit) AS totalUserLimit, 
												   exhibitor_id 
												   
											  FROM " . _DB_EXIBITOR_BOOKING_ORDER_ . "  
											 WHERE `status` = 'A'
											 
										  GROUP BY `exhibitor_id` 
									  
									  ) userLimit 
								   ON exhibitor.id = userLimit.exhibitor_id
								   
					  LEFT OUTER JOIN (
											
											SELECT COUNT(*) AS totalRegisteredUser,
												   user.exhibitor_company_id AS exhibitor_id 
											
											  FROM " . _DB_USER_REGISTRATION_ . " user  
												
											 WHERE user.status = 'A' 
											   
										  GROUP BY user.exhibitor_company_id 
											
									  ) registeredUserCount 
								   ON exhibitor.id = registeredUserCount.exhibitor_id
							  
								WHERE exhibitor.status != 'D' " . $filterCondition . " " . $searchCondition . " 
								
							 ORDER BY exhibitor.exhibitor_company_name ASC";

	return $sqlFetchExibitor;
}

function getExhibitortariff($company_code = "", $reg_cls_id = "", $cutoff_id = "")
{
	global $cfg, $mycms;


	$filterCondition        = "";

	if ($company_code != "") {
		$filterCondition   .= " AND company_code = '" . $company_code . "'";
	}

	if ($reg_cls_id != "") {
		$filterCondition   .= " AND reg_classification_id = '" . $reg_cls_id . "'";
	}

	if ($cutoff_id != "") {
		$filterCondition   .= " AND tariff_cutoff_id = '" . $cutoff_id . "'";
	}

	$sqlTarrif         = array();
	$sqlTarrif['QUERY']	 = "SELECT * 
							FROM " . _DB_EXIBITOR_REGISTRATION_TARIFF_ . "
						   WHERE `status` = 'A' " . $filterCondition . "
						ORDER BY `reg_classification_id`";


	$resTarrif			= $mycms->sql_select($sqlTarrif);

	return $resTarrif;
}

function exhibitorTariffDetailsQuerySet()
{
	global $cfg, $mycms;
	$displayData 	 = array();

	$sqlTarrif         = array();
	$sqlTarrif['QUERY']	 = "SELECT * 
							FROM " . _DB_EXIBITOR_REGISTRATION_TARIFF_ . "
						   WHERE `status` = ?
						ORDER BY `id` DESC";

	$sqlTarrif['PARAM'][] = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

	$resTarrif			= $mycms->sql_select($sqlTarrif);

	$sqlcutoff         = array();
	$sqlcutoff['QUERY']		= "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " 
								WHERE status =?";

	$sqlcutoff['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

	$rescutoff			= $mycms->sql_select($sqlcutoff);
	foreach ($resTarrif as $k => $rowTarrif) {
		foreach ($rescutoff as $keycutoff => $cutoffvalue) {
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['REG_CLASSIFICATION_ID'] = '1';
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['CLASSIFICATION_TITTLE'] = 'EXHIBITOR REPRESENTATIVE';
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['CUTOFF_TITTLE'] 		 = $cutoffvalue['cutoff_title'];
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['CUTOFF_ID'] 			 = $cutoffvalue['id'];
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['CURRENCY'] 			 = 'INR';
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['TYPE'] 				 = 'EXHIBITOR';
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['HOTEL'] 				 = '';

			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['CLASSIFICATION_TITTLE'] = $rowTarrif['tariff_name'];
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['AMOUNT'] = $rowTarrif['amount'];
			$displayData[$rowTarrif['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = $rowTarrif['amount'];
		}
	}
	return $displayData;
}

function exhibitorStallBookingQuerySet($orderId = "", $searchCondition = "")
{
	global $cfg, $mycms;


	$filterCondition       = "";

	if ($orderId != "") {
		$filterCondition  .= " AND mainTAB.id = '" . $orderId . "'";
	}

	$sqlFetchOrder         = array();
	$sqlFetchOrder['QUERY']  = "SELECT mainTAB.*,
								  (totalBookingAmount - totalPaidAmount) AS totalPendingAmountWithoutST, 
								 (totalPaybleAmount - totalPaidAmount) AS totalPendingAmountWithST,
								 (totalPaidAmount + totalTdsAmount) AS totalAmount
								 
								FROM (
								
										  SELECT bookingDetails.*,
												 
												 exhibitor.id AS exhibitorId,
												 exhibitor.exhibitor_company_code,
												 exhibitor.exhibitor_company_name,
												 exhibitor.exhibitor_company_email,
												 exhibitor.exhibitor_company_mobile,
												 
												 partnershipCategory.category_title,
												 
												 IFNULL(bookingDetails.booking_amount, 0) AS totalBookingAmount,
												 IFNULL(bookingDetails.payble_amount, 0) AS totalPaybleAmount,
												 IFNULL(totalPaidAmount, 0) AS totalPaidAmount,
												 IFNULL(totalTdsAmount, 0) AS totalTdsAmount  
				
											FROM " . _DB_EXIBITOR_STALL_BOOKING_ . " bookingDetails 
										
									  INNER JOIN " . _DB_EXIBITOR_COMPANY_ . " exhibitor 
											  ON bookingDetails.exhibitor_id = exhibitor.id 
								 
								 LEFT OUTER JOIN " . _DB_EXIBITOR_PARTNERSHIP_CATEGORY_ . " partnershipCategory 
											  ON partnershipCategory.id = bookingDetails.partnership_category_id 
								 
								 LEFT OUTER JOIN (
								
													SELECT SUM(bookingPayment.amount) AS totalPaidAmount,
														   SUM(bookingPayment.tds_amount) AS totalTdsAmount,
														   bookingPayment.exhibitor_stall_booking_id 
													
													  FROM " . _DB_EXIBITOR_STALL_BOOKING_PAYMENT_ . " bookingPayment
													  
												INNER JOIN " . _DB_EXIBITOR_STALL_BOOKING_ . " bookingDetails
														ON bookingDetails.id = bookingPayment.exhibitor_stall_booking_id 
														
													 WHERE bookingPayment.status = 'A' 
													 
												  GROUP BY bookingPayment.exhibitor_stall_booking_id   
												
												 ) bookingPayment
											  ON bookingDetails.id = bookingPayment.exhibitor_stall_booking_id  
										   
										   WHERE bookingDetails.status != 'D' 
											 AND exhibitor.status != 'D' 
									 ) mainTAB 
							   WHERE 1 " . $filterCondition . " " . $searchCondition . "
							   ORDER BY mainTAB.id  DESC ";

	return $sqlFetchOrder;
}


function exhibitorInseringProcess_natcon($exhibitorId)
{
	global $cfg, $mycms;
	include_once("function.accommodation.php");
	include_once("function.workshop.php");
	include_once("function.invoice.php");
	include_once("function.delegate.php");
	include_once("function.accompany.php");
	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT *  FROM " . _DB_EXHIBITOR_REGISTRATION_ . " 																
											WHERE status = 'A'										
											AND id='" . $exhibitorId . "' ";

	$resultUserDetails			= $mycms->sql_select($sqlExhibitorDetails);


	if ($resultUserDetails) {
		$userDetails = $resultUserDetails[0];
		// echo '<pre>';
		// print_r($userDetails);
		// die;

		// $userDetails		= unserialize($rowProcessFlow['step1']);
		$workshopDetails	= json_decode($userDetails['workshop_id']);
		$accompanyCount	= $userDetails['accompanyCount'];
		// $accompanyDetails	= unserialize($rowProcessFlow['step3']);
		// $accommDetails		= unserialize($rowProcessFlow['step4']);
		// $tourDetails		= unserialize($rowProcessFlow['step5']);
		// $dinnerDetails		= $userDetails['dinner_value'];
		// $fileDetails		= $rowProcessFlow['step10'];
		// $hotel_id			= $userDetails['hotel_id'];
		$cutoffId			= $userDetails['registration_tariff_cutoff_id'];
		$clsfId				= $userDetails['registration_classification_id'];




		$clasfDetails	    = getRegClsfDetails($clsfId);

		if ($clasfPayMode == '') {
			$clasfPayMode		= 'COMPLIMENTARY'; //$clasfDetails['payment_type'];
		}

		$clasfType			= $clasfDetails['type'];

		$date				= $userDetails['date'];

		// echo '<pre>';
		// print_r($clasfPayMode);
		// die;

		if ($userDetails) {
			$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($userDetails['email_id'])));
			$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($userDetails['comunication_email'])));
			$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
			$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
			$userDetailsArray['membership_number']                    = addslashes(trim($userDetails['membership_number']));
			$userDetailsArray['user_initial_title']   				  = addslashes(trim(strtoupper($userDetails['title'])));
			$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['first_name'])));
			$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['middle_name'])));
			$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['last_name'])));
			$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title'] . " " . $userDetailsArray['user_first_name'] . " " . $userDetailsArray['user_middle_name'] . " " . $userDetailsArray['user_last_name'];
			$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
			$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['mobile_isd_code'])));
			$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['mobile_no'])));
			$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
			$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['address'])));
			$userDetailsArray['user_country']                         = $userDetails['country_id'] == '' ? '0' : addslashes(trim(strtoupper($userDetails['country_id'])));
			$userDetailsArray['user_state']                           = $userDetails['state_id'] == '' ? '0' : addslashes(trim(strtoupper($userDetails['state_id'])));
			$userDetailsArray['user_city']                            = $userDetails['city'] == '' ? '0' : addslashes(trim(strtoupper($userDetails['city'])));
			$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['pincode'])));
			$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
			$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
			$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));

			$userDetailsArray['isCombo']                            = $userDetails['isCombo'] == '' ? 'N' : addslashes(trim($userDetails['isCombo']));

			if ($userDetailsArray['user_dob_year'] != "" && $userDetailsArray['user_dob_month'] != "" && $userDetailsArray['user_dob_day'] != "") {
				$userDetailsArray['user_dob']                         = number_pad($userDetailsArray['user_dob_year'], 4) . "-" . number_pad($userDetailsArray['user_dob_month'], 2) . "-" . number_pad($userDetailsArray['user_dob_day'], 2);
			}

			$userDetailsArray['user_gender']                          = ($userDetails['gender'] == '') ? 'NA' : $userDetails['gender'];
			$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['designation'])));
			$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['depertment'])));
			$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['institution'])));
			$userDetailsArray['user_food_preference']                 = $userDetails['food_preference'];
			$userDetailsArray['user_other_food_details']              = addslashes(trim(strtoupper($userDetails['food_preference_in_details'])));
			$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['pasport_no'])));

			$userDetailsArray['user_document']						  = $fileDetails;
			$userDetailsArray['isRegistration']						  = 'Y';
			$userDetailsArray['isConference']						  = 'Y';
			$userDetailsArray['isWorkshop']							  = 'N';
			$userDetailsArray['isAccommodation']                      = 'N';
			$userDetailsArray['isTour']								  = 'N';
			$userDetailsArray['IsAbstract']							  = 'N';

			$userDetailsArray['registration_classification_id']		  = $userDetails['registration_classification_id'];
			$userDetailsArray['registration_tariff_cutoff_id']        = $cutoffId;
			$userDetailsArray['registration_request']       		  = 'GENERAL';
			$userDetailsArray['operational_area']   	    		  = 'GENERAL';
			$userDetailsArray['registration_payment_status']		  = 'UNPAID';
			$userDetailsArray['registration_mode']					  = "OFFLINE";
			$userDetailsArray['account_status']						  = 'REGISTERED';
			$userDetailsArray['reg_type']              				  = 'BULK';

			// if (!empty($userDetails['delegateId']) && $userDetails['delegateId'] != '') {
			// 	$userDetailsArray['abstractDelegateId']  = $userDetails['delegateId'];
			// } else {
			// 	$userDetailsArray['abstractDelegateId']  = $userDetails['abstractDelegateId'];
			// }


			if (!empty($userDetails['checkin_id']) && $userDetails['checkin_id'] != '') {
				$userDetailsArray['accoCheckInID'] = $userDetails['checkin_id'];
			}

			if (!empty($userDetails['checkout_id']) && $userDetails['checkout_id'] != '') {
				$userDetailsArray['accoCheckOutID'] = $userDetails['checkout_id'];
			}

			if (!empty($userDetails['hotel_id']) && $userDetails['hotel_id'] != '') {
				$userDetailsArray['accoHotelID'] = $userDetails['hotel_id'];
			}

			if (!empty($userDetails['package_id']) && $userDetails['package_id'] != '') {
				$userDetailsArray['accoPackageID'] = $userDetails['package_id'];
			}



			// if ($userDetailsArray['abstractDelegateId'] != '' && $userDetailsArray['abstractDelegateId'] > 0) {
			// 	$delegateId	= insertingExistingUserDetails($userDetailsArray['abstractDelegateId'], $userDetailsArray, $date);
			// } else {
			// 	$delegateId	= insertingUserDetails($userDetailsArray, $date);
			// }
			// 	echo '<pre>';
			// print_r($userDetailsArray);
			// die;

			$delegateId				= insertingUserDetails($userDetailsArray, date('Y-m-d'));


			// echo $mycms->getSession('SLIP_ID');
			// if ($mycms->getSession('SLIP_ID') == "") {

			$mycms->setSession('LOGGED.USER.ID', $delegateId);
			insertingSlipDetails($delegateId, 'OFFLINE', 'GENERAL', $date, 'BACK');
			// }


			if ($userDetailsArray['isCombo'] == 'Y') {
				$invoiceIdConf = insertingInvoiceDetails($delegateId, 'CONFERENCE', 'GENERAL', $date, '', "Y");
			} else {
				$invoiceIdConf = insertingInvoiceDetails($delegateId, 'CONFERENCE', 'GENERAL', $date);
			}


			$current_SlipAmount	= invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));

			if ($clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE" || $current_SlipAmount == 0) // 
			{
				if ($clasfPayMode == "COMPLIMENTARY") {
					complimentarySlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf, 'CONFERENCE', $mycms->getSession('SLIP_ID'));
				} else if ($clasfPayMode == "ZERO_VALUE") {
					zeroValueSlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf, 'CONFERENCE', $mycms->getSession('SLIP_ID'));
				}

				$sqlUpdate = array();
				$sqlUpdate['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "
											SET `payment_status` = ?
										  WHERE `id` = ?";

				$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $invoiceIdConf,   'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);

				$sqlUpdateUserReg = array();
				$sqlUpdateUserReg['QUERY']	   = "UPDATE " . _DB_USER_REGISTRATION_ . "
													 SET `registration_payment_status` = ?
											  	   WHERE `id` = ?";

				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'id',                            'DATA' => $delegateId,   'TYP' => 's');

				$mycms->sql_update($sqlUpdateUserReg, false);
			}

			if ($clasfType == 'COMBO') {
				$dinnerComboDetails  = array(2);
				foreach ($dinnerComboDetails as $key => $val) {
					$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
					$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
					$dinnerDetailsArray[$val]['package_id']            = $val;
					$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray[$val]['booking_quantity']      = 1;
					$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray[$val]['refference_invoice_id'] = $invoiceIdConf; // Need To Edit
					$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray[$val]['payment_status']        = 'ZERO_VALUE';
				}

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);

				if ($clsfId != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
					$accomodationDetails['user_id']											 = $delegateId;
					$accomodationDetails['hotel_id']										 = $hotel_id;
					$accomodationDetails['package_id']										 = $userDetails['accommodation_package_id'];
					$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
					$accomodationDetails['checkin_date']									 = getCheckInDateById($userDetails['accommodation_checkIn'], 1);
					$accomodationDetails['checkout_date']									 = getCheckOutDateById($userDetails['accommodation_checkOut'], 1);
					$accomodationDetails['booking_quantity']								 = 1;
					$accomodationDetails['type']								 			 = "COMBO";
					$accomodationDetails['refference_invoice_id']							 = $invoiceIdConf;
					$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
					$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];

					$accomodationDetails['preffered_accommpany_name']						 = $userDetails['preffered_accommpany_name'];
					$accomodationDetails['preffered_accommpany_email']						 = $userDetails['preffered_accommpany_email'];
					$accomodationDetails['preffered_accommpany_mobile']						 = $userDetails['preffered_accommpany_mobile'];

					$accomodationDetails['payment_status']									 = 'ZERO_VALUE';

					$accompReqId	 														 = insertingAccomodationDetails($accomodationDetails);
				}
			}

			if ($workshopDetails) {

				// foreach ($workshopDetails as $key => $workshopId) {
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= json_decode($userDetails['workshop_id']);
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $cutoffId;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getWorkshopTariffId($workshopId, $cutoffId, $clsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
				$workshopDetailArray[$workshopId]['booking_mode']        			= $userDetails['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type']       		= 'GENERAL';
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= 'UNPAID';
				// }
				$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);
				if (!empty($userDetailsArray['combo_registration'])) {
				} else {
					foreach ($workshopReqId as $key => $reqId) {
						$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP', 'GENERAL', $date);


						// update invoice payment status
						$sqlUpdate = array();
						$sqlUpdate['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?";

						$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
						$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $invoiceIdWrkshp,   'TYP' => 's');

						$mycms->sql_update($sqlUpdate, false);

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

			if ($accompanyCount > 0) {
				$accompanyNameArr	= json_decode($userDetails['accompany_name_add']);
				$accompany_food_choice_arr	= json_decode($userDetails['accompany_food_choice']);

				for ($i = 0; $i < $accompanyCount; $i++) {
					// foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {
					$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
					$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($accompanyNameArr[$i])));
					$accompanyDetailsArray[$val]['user_age']                    		 = '';
					$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($accompany_food_choice_arr[$i])));
					$accompanyDetailsArray[$val]['user_food_details']                    = '';
					$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));

					$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
					$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
					$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($clsfId)));
					$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
					$accompanyDetailsArray[$val]['registration_request']       		 	 = 'GENERAL';
					$accompanyDetailsArray[$val]['operational_area']   	    		 	 = 'GENERAL';
					$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
					$accompanyDetailsArray[$val]['registration_mode']					 = 'OFFLINE';
					$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
					$accompanyDetailsArray[$val]['reg_type']              				 = 'BULK';




					$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray, $date, $accompanyDetails['registration_acc_cutoff']);

					$accDinnerDetails = $accompanyDetails['dinner_value'];

					// ACCOMPANY DINNNER PENDING
					// if ($accDinnerDetails) {

					// 	foreach ($accDinnerDetails as $key => $val) {
					// 		$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					// 		$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId[$key];
					// 		$dinnerDetailsArray1[$key]['package_id']            = $val;
					// 		$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $cutoffId;
					// 		$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					// 		$dinnerDetailsArray1[$key]['booking_mode']          = $userDetailsArray['registration_mode'];
					// 		$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					// 		$dinnerDetailsArray1[$key]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					// 		$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
					// 	}

					// 	$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);

					// 	foreach ($dinerReqId as $key => $reqId) {

					// 		insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date);

					// 		if ($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE") {
					// 			zeroValueInvoiceUpdate($invoiceIdDinner, 'DINNER', $mycms->getSession('SLIP_ID'));
					// 			$sqlUpdate = array();
					// 			$sqlUpdate['QUERY']	 = "UPDATE " . _DB_REQUEST_DINNER_ . "
					// 										SET `payment_status` = ?
					// 									  WHERE `id` = ?";

					// 			$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'ZERO_VALUE',  'TYP' => 's');
					// 			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $reqId,   'TYP' => 's');
					// 			$mycms->sql_update($sqlUpdate, false);
					// 		}
					// 	}
					// }

					foreach ($accompanyReqId as $key => $reqId) {
						if ($counter == 'Y') {
							$invoiceIdAcompany = insertingInvoiceDetails($reqId, 'ACCOMPANY', 'GENERAL', $date, $counter);
						} else {
							$invoiceIdAcompany = insertingInvoiceDetails($reqId, 'ACCOMPANY', 'GENERAL', $date);
						}
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

			//if($accommDetails)
			if (!empty($userDetails['checkin_id']) && !empty($userDetails['checkout_id']) && !empty($userDetails['hotel_id'])) {
				$check_in_date_id                 = $userDetails['checkin_id'];
				$check_out_date_id                = $userDetails['checkout_id'];
				$accommodation_hotel_id           = $userDetails['hotel_id'];
				//$accommodation_hotel_type_id      = $accommDetails['accommodation_roomType_id'];
				$totalRoom = 0;
				$totalGuestCounter                 = 0;
				/*foreach($accommDetails['room_guest_counter'] as $key=>$resDetails )
			{
				$totalRoom++;
				if($resDetails!=""){
							
					$totalGuestCounter        += $resDetails;
				}
			}*/
				$sqlAccommodationDate['QUERY']           = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
											   WHERE `id` = '" . $check_in_date_id . "'";

				$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate);
				$rowAccommodationDate           = $resultAccommodationDate[0];

				$check_in_date              = $rowAccommodationDate['check_in_date'];

				// GET ACCOMMODATION OUT DATE
				$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
													   WHERE `id` = '" . $check_out_date_id . "'";

				$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate);
				$rowAccommodationOutDate           = $resultAccommodationOutDate[0];

				$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];


				$sqlFetchHotel['QUERY']    = "SELECT id 
								   FROM " . _DB_PACKAGE_ACCOMMODATION_ . "  
								  WHERE  `hotel_id` = '" . $accommodation_hotel_id . "'
									  AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'

				$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
				$resultfetch 	  = $resultFetchHotel[0];
				$packageId 	      = $resultfetch['id'];
				$accTariffId = getAccommodationTariffId($packageId, $check_in_date_id, $check_out_date_id, $cutoffId);

				$accomodationDetails['user_id']											 = $delegateId;
				//$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
				$accomodationDetails['accommodation_details']							 = '';
				$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
				//$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
				//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
				$accomodationDetails['package_id']										 = 0;
				$accomodationDetails['tariff_ref_id']								     = $accTariffId;
				$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
				$accomodationDetails['checkin_date']									 = $check_in_date;
				$accomodationDetails['checkout_date']									 = $check_out_date;
				$accomodationDetails['booking_quantity']								 = 1; //$accommDetails['booking_quantity'];
				$accomodationDetails['refference_invoice_id']							 = 0;
				$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
				$accomodationDetails['booking_mode']									 = $userDetails['registration_mode'];
				$accomodationDetails['payment_status']									 = 'ZERO_VALUE';

				$accompReqId	 = insertingAccomodationDetails($accomodationDetails);


				$accommRoomDetails['user_id']										 = $delegateId;
				$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
				$accommRoomDetails['room_id']										 = 1;
				$accommRoomDetails['checkin_id']								     = $check_in_date_id;
				$accommRoomDetails['checkout_id']								     = $check_out_date_id;
				$accommRoomDetails['checkin_date']								     = $check_in_date;
				$accommRoomDetails['checkout_date']									 = $check_out_date;



				$accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);

				$accomdInvoiceId = insertingInvoiceDetails($accompReqId, 'ACCOMMODATION');
				zeroValueInvoiceUpdate($accomdInvoiceId, 'ACCOMMODATION', $mycms->getSession('SLIP_ID'));

				$sqlProcessUpdateRoom['QUERY']  = " UPDATE  " . _DB_USER_REGISTRATION_ . "
												   SET `accommodation_room` = '1'
												 WHERE `id` = '" . $delegateId . "' AND status='A'";
				$mycms->sql_update($sqlProcessUpdateRoom, false);
			}


			if ($tourDetails) {

				foreach ($tourDetails['tour_id'] as $key => $val) {
					$tourDetailsArray[$val]['user_id']               = $delegateId;
					$tourDetailsArray[$val]['package_id']            = $val;
					$tourDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$tourDetailsArray[$val]['booking_date']          = getTourDate($val);
					$tourDetailsArray[$val]['booking_quantity']      = $tourDetails['number_of_person'][$val];
					$tourDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$tourDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
					$tourDetailsArray[$val]['refference_slip_id']	 = $mycms->getSession('SLIP_ID');
					$tourDetailsArray[$val]['payment_status']        = 'UNPAID';
				}

				$tourReqId    	= insertingTourDetails($tourDetailsArray);
				foreach ($tourReqId as $key => $reqId) {
					insertingInvoiceDetails($reqId, 'TOUR');
				}
			}

			if ($dinnerDetails) {

				foreach ($dinnerDetails as $key => $val) {
					$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
					$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
					$dinnerDetailsArray[$val]['package_id']            = $val;
					$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray[$val]['booking_quantity']      = 1;
					$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray[$val]['payment_status']        = 'UNPAID';
				}

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);
				foreach ($dinerReqId as $key => $reqId) {
					if ($counter == 'Y') {
						$invoiceIdDinner  =  insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date, $counter);
					} else {
						$invoiceIdDinner  =  insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date);
					}

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
			}

			setUserPaymentStatus($delegateId);


			//registration_acknowledgement_message($delegateId, $mycms->getSession('SLIP_ID'), 'SEND');

			if ($userDetails['reg_area'] == "FRONT") {
				online_welcome_message($delegateId, 'SEND');
			}

			if ($userDetails['reg_area'] == "BACK") {
				$mycms->removeSession('PROCESS_FLOW_ID');
				$mycms->removeSession('USER_DETAILS');
				$mycms->getSession('USER_DETAILS', array());
			} else if ($userDetails['reg_area'] == "FRONT") {
				$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
				$mycms->removeSession('CUTOFF_ID_FRONT');
				$mycms->removeSession('CLSF_ID_FRONT');
				$mycms->removeSession('USER_DETAILS_FRONT');
				$mycms->setSession('USER_DETAILS_FRONT', array());
			}
		}
	}
	if ($delegateId) {
		$sqlExhibitorDetails    = array();
		$sqlExhibitorDetails['QUERY']	= 	"UPDATE " . _DB_EXHIBITOR_REGISTRATION_ . " SET `account_status`='REGISTERED', `delegate_id`= '" . $delegateId . "'  															
											WHERE status = 'A'										
											AND id='" . $exhibitorId . "' ";

		$resultUserDetails			= $mycms->sql_update($sqlExhibitorDetails);
	}
	return $delegateId;
}

function exhibitorInseringProcess($exhibitorId)
{
	global $cfg, $mycms;
	include_once("function.accommodation.php");
	include_once("function.workshop.php");
	include_once("function.invoice.php");
	include_once("function.delegate.php");
	include_once("function.accompany.php");
	$sqlExhibitorDetails    = array();
	$sqlExhibitorDetails['QUERY']	= 	"SELECT *  FROM " . _DB_EXHIBITOR_REGISTRATION_ . " 																
											WHERE status = 'A'										
											AND id='" . $exhibitorId . "' ";

	$resultUserDetails			= $mycms->sql_select($sqlExhibitorDetails);


	if ($resultUserDetails) {
		$userDetails = $resultUserDetails[0];

		$companyDetails = getExhibitorCompanyDetails('', $userDetails['exhibitor_company_code']);
		$exb_company_name = $companyDetails[0]['exhibitor_company_name'];
		// echo '<pre>';
		// print_r($exb_company_name);
		// die;

		// $userDetails		= unserialize($rowProcessFlow['step1']);
		$workshopDetails	= json_decode($userDetails['workshop_id']);
		$accompanyCount	= $userDetails['accompanyCount'];
		// $accompanyDetails	= unserialize($rowProcessFlow['step3']);
		// $accommDetails		= unserialize($rowProcessFlow['step4']);
		// $tourDetails		= unserialize($rowProcessFlow['step5']);
		// $dinnerDetails		= $userDetails['dinner_value'];
		// $fileDetails		= $rowProcessFlow['step10'];
		// $hotel_id			= $userDetails['hotel_id'];
		if ($userDetails['registration_tariff_cutoff_id'] != -1 &&  $userDetails['registration_tariff_cutoff_id'] != 0) {
			$cutoffId			= $userDetails['registration_tariff_cutoff_id'];
		} else {
			$cutoffId			= getTariffCutoffId();
		}

		$clsfId				= $userDetails['registration_classification_id'];




		$clasfDetails	    = getRegClsfDetails($clsfId);

		if ($clasfPayMode == '') {
			$clasfPayMode		= 'COMPLIMENTARY'; //$clasfDetails['payment_type'];
		}

		$clasfType			= $clasfDetails['type'];

		$date				= $userDetails['date'];

		// echo '<pre>';
		// print_r($clasfPayMode);
		// die;

		if ($userDetails) {
			$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($userDetails['email_id'])));
			$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($userDetails['comunication_email'])));
			$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
			$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
			$userDetailsArray['membership_number']                    = addslashes(trim($userDetails['membership_number']));
			$userDetailsArray['user_initial_title']   				  = addslashes(trim(strtoupper($userDetails['title'])));
			$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['first_name'])));
			$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['middle_name'])));
			$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['last_name'])));
			$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title'] . " " . $userDetailsArray['user_first_name'] . " " . $userDetailsArray['user_middle_name'] . " " . $userDetailsArray['user_last_name'];
			$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
			$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['mobile_isd_code'])));
			$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['mobile_no'])));
			$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
			$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['address'])));
			$userDetailsArray['user_country']                         = $userDetails['country_id'] == '' ? '0' : addslashes(trim(strtoupper($userDetails['country_id'])));
			$userDetailsArray['user_state']                           = $userDetails['state_id'] == '' ? '0' : addslashes(trim(strtoupper($userDetails['state_id'])));
			$userDetailsArray['user_city']                            = $userDetails['city'] == '' ? '0' : addslashes(trim(strtoupper($userDetails['city'])));
			$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['pincode'])));
			$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
			$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
			$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));

			$userDetailsArray['isCombo']                            = $userDetails['isCombo'] == '' ? 'N' : addslashes(trim($userDetails['isCombo']));

			if ($userDetailsArray['user_dob_year'] != "" && $userDetailsArray['user_dob_month'] != "" && $userDetailsArray['user_dob_day'] != "") {
				$userDetailsArray['user_dob']                         = number_pad($userDetailsArray['user_dob_year'], 4) . "-" . number_pad($userDetailsArray['user_dob_month'], 2) . "-" . number_pad($userDetailsArray['user_dob_day'], 2);
			}

			$userDetailsArray['user_gender']                          = ($userDetails['gender'] == '') ? 'NA' : $userDetails['gender'];
			$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['designation'])));
			$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['depertment'])));
			$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['institution'])));
			$userDetailsArray['user_food_preference']                 = $userDetails['food_preference'];
			$userDetailsArray['user_other_food_details']              = addslashes(trim($exb_company_name));
			$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['pasport_no'])));

			$userDetailsArray['user_document']						  = $fileDetails;
			$userDetailsArray['isRegistration']						  = 'Y';
			$userDetailsArray['isConference']						  = 'Y';
			$userDetailsArray['isWorkshop']							  = 'N';
			$userDetailsArray['isAccommodation']                      = 'N';
			$userDetailsArray['isTour']								  = 'N';
			$userDetailsArray['IsAbstract']							  = 'N';

			$userDetailsArray['registration_classification_id']		  = $userDetails['registration_classification_id'];
			$userDetailsArray['registration_tariff_cutoff_id']        = $cutoffId;
			$userDetailsArray['registration_request']       		  = 'GENERAL';
			$userDetailsArray['operational_area']   	    		  = 'GENERAL';
			$userDetailsArray['registration_payment_status']		  = 'UNPAID';
			$userDetailsArray['registration_mode']					  = "OFFLINE";
			$userDetailsArray['account_status']						  = 'REGISTERED';
			$userDetailsArray['reg_type']              				  = 'BULK';

			// if (!empty($userDetails['delegateId']) && $userDetails['delegateId'] != '') {
			// 	$userDetailsArray['abstractDelegateId']  = $userDetails['delegateId'];
			// } else {
			// 	$userDetailsArray['abstractDelegateId']  = $userDetails['abstractDelegateId'];
			// }


			if (!empty($userDetails['checkin_id']) && $userDetails['checkin_id'] != '') {
				$userDetailsArray['accoCheckInID'] = $userDetails['checkin_id'];
			}

			if (!empty($userDetails['checkout_id']) && $userDetails['checkout_id'] != '') {
				$userDetailsArray['accoCheckOutID'] = $userDetails['checkout_id'];
			}

			if (!empty($userDetails['hotel_id']) && $userDetails['hotel_id'] != '') {
				$userDetailsArray['accoHotelID'] = $userDetails['hotel_id'];
			}

			if (!empty($userDetails['package_id']) && $userDetails['package_id'] != '') {
				$userDetailsArray['accoPackageID'] = $userDetails['package_id'];
			}



			// if ($userDetailsArray['abstractDelegateId'] != '' && $userDetailsArray['abstractDelegateId'] > 0) {
			// 	$delegateId	= insertingExistingUserDetails($userDetailsArray['abstractDelegateId'], $userDetailsArray, $date);
			// } else {
			// 	$delegateId	= insertingUserDetails($userDetailsArray, $date);
			// }
			// 	echo '<pre>';
			// print_r($userDetailsArray);
			// die;

			$delegateId				= insertingUserDetails($userDetailsArray, date('Y-m-d'));


			// echo $mycms->getSession('SLIP_ID');
			// if ($mycms->getSession('SLIP_ID') == "") {

			$mycms->setSession('LOGGED.USER.ID', $delegateId);
			insertingSlipDetails($delegateId, 'OFFLINE', 'GENERAL', $date, 'BACK');
			// }


			if ($userDetailsArray['isCombo'] == 'Y') {
				$invoiceIdConf = insertingInvoiceDetails($delegateId, 'CONFERENCE', 'GENERAL', $date, '', "Y");
			} else {
				$invoiceIdConf = insertingInvoiceDetails($delegateId, 'CONFERENCE', 'GENERAL', $date);
			}


			$current_SlipAmount	= invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));

			if ($clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE" || $current_SlipAmount == 0) // 
			{
				if ($clasfPayMode == "COMPLIMENTARY") {
					complimentarySlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf, 'CONFERENCE', $mycms->getSession('SLIP_ID'));
				} else if ($clasfPayMode == "ZERO_VALUE") {
					zeroValueSlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf, 'CONFERENCE', $mycms->getSession('SLIP_ID'));
				}

				$sqlUpdate = array();
				$sqlUpdate['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "
											SET `payment_status` = ?
										  WHERE `id` = ?";

				$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $invoiceIdConf,   'TYP' => 's');

				$mycms->sql_update($sqlUpdate, false);

				$sqlUpdateUserReg = array();
				$sqlUpdateUserReg['QUERY']	   = "UPDATE " . _DB_USER_REGISTRATION_ . "
													 SET `registration_payment_status` = ?
											  	   WHERE `id` = ?";

				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' => 'COMPLIMENTARY', 'TYP' => 's');
				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'id',                            'DATA' => $delegateId,   'TYP' => 's');

				$mycms->sql_update($sqlUpdateUserReg, false);
			}

			if ($clasfType == 'COMBO') {
				$dinnerComboDetails  = array(2);
				foreach ($dinnerComboDetails as $key => $val) {
					$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
					$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
					$dinnerDetailsArray[$val]['package_id']            = $val;
					$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray[$val]['booking_quantity']      = 1;
					$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray[$val]['refference_invoice_id'] = $invoiceIdConf; // Need To Edit
					$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray[$val]['payment_status']        = 'ZERO_VALUE';
				}

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);

				if ($clsfId != $cfg['INAUGURAL_OFFER_CLASF_ID']) {
					$accomodationDetails['user_id']											 = $delegateId;
					$accomodationDetails['hotel_id']										 = $hotel_id;
					$accomodationDetails['package_id']										 = $userDetails['accommodation_package_id'];
					$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
					$accomodationDetails['checkin_date']									 = getCheckInDateById($userDetails['accommodation_checkIn'], 1);
					$accomodationDetails['checkout_date']									 = getCheckOutDateById($userDetails['accommodation_checkOut'], 1);
					$accomodationDetails['booking_quantity']								 = 1;
					$accomodationDetails['type']								 			 = "COMBO";
					$accomodationDetails['refference_invoice_id']							 = $invoiceIdConf;
					$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
					$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];

					$accomodationDetails['preffered_accommpany_name']						 = $userDetails['preffered_accommpany_name'];
					$accomodationDetails['preffered_accommpany_email']						 = $userDetails['preffered_accommpany_email'];
					$accomodationDetails['preffered_accommpany_mobile']						 = $userDetails['preffered_accommpany_mobile'];

					$accomodationDetails['payment_status']									 = 'ZERO_VALUE';

					$accompReqId	 														 = insertingAccomodationDetails($accomodationDetails);
				}
			}

			if ($workshopDetails) {

				// foreach ($workshopDetails as $key => $workshopId) {
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= json_decode($userDetails['workshop_id']);
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $cutoffId;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getWorkshopTariffId($workshopId, $cutoffId, $clsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
				$workshopDetailArray[$workshopId]['booking_mode']        			= $userDetails['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type']       		= 'GENERAL';
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= 'UNPAID';
				// }
				$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);
				if (!empty($userDetailsArray['combo_registration'])) {
				} else {
					foreach ($workshopReqId as $key => $reqId) {
						$invoiceIdWrkshp = insertingInvoiceDetails($reqId, 'WORKSHOP', 'GENERAL', $date);


						// update invoice payment status
						$sqlUpdate = array();
						$sqlUpdate['QUERY']	 = "UPDATE " . _DB_INVOICE_ . "
														SET `payment_status` = ?
													  WHERE `id` = ?";

						$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'COMPLIMENTARY',  'TYP' => 's');
						$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $invoiceIdWrkshp,   'TYP' => 's');

						$mycms->sql_update($sqlUpdate, false);

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

			if ($accompanyCount > 0) {
				$accompanyNameArr	= json_decode($userDetails['accompany_name_add']);
				$accompany_food_choice_arr	= json_decode($userDetails['accompany_food_choice']);

				for ($i = 0; $i < $accompanyCount; $i++) {
					// foreach ($accompanyDetails['accompany_selected_add'] as $key => $val) {
					$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
					$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($accompanyNameArr[$i])));
					$accompanyDetailsArray[$val]['user_age']                    		 = '';
					$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($accompany_food_choice_arr[$i])));
					$accompanyDetailsArray[$val]['user_food_details']                    = '';
					$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));

					$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
					$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
					$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($clsfId)));
					$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
					$accompanyDetailsArray[$val]['accompany_tariff_cutoff_id']           = '1';
					$accompanyDetailsArray[$val]['registration_request']       		 	 = 'GENERAL';
					$accompanyDetailsArray[$val]['operational_area']   	    		 	 = 'GENERAL';
					$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
					$accompanyDetailsArray[$val]['registration_mode']					 = 'OFFLINE';
					$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
					$accompanyDetailsArray[$val]['reg_type']              				 = 'BULK';




					$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray, $date, '1');

					$accDinnerDetails = $accompanyDetails['dinner_value'];

					// ACCOMPANY DINNNER PENDING
					// if ($accDinnerDetails) {

					// 	foreach ($accDinnerDetails as $key => $val) {
					// 		$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					// 		$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId[$key];
					// 		$dinnerDetailsArray1[$key]['package_id']            = $val;
					// 		$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $cutoffId;
					// 		$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					// 		$dinnerDetailsArray1[$key]['booking_mode']          = $userDetailsArray['registration_mode'];
					// 		$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					// 		$dinnerDetailsArray1[$key]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					// 		$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
					// 	}

					// 	$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);

					// 	foreach ($dinerReqId as $key => $reqId) {

					// 		insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date);

					// 		if ($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE") {
					// 			zeroValueInvoiceUpdate($invoiceIdDinner, 'DINNER', $mycms->getSession('SLIP_ID'));
					// 			$sqlUpdate = array();
					// 			$sqlUpdate['QUERY']	 = "UPDATE " . _DB_REQUEST_DINNER_ . "
					// 										SET `payment_status` = ?
					// 									  WHERE `id` = ?";

					// 			$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' => 'ZERO_VALUE',  'TYP' => 's');
					// 			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' => $reqId,   'TYP' => 's');
					// 			$mycms->sql_update($sqlUpdate, false);
					// 		}
					// 	}
					// }

					foreach ($accompanyReqId as $key => $reqId) {
						if ($counter == 'Y') {
							$invoiceIdAcompany = insertingInvoiceDetails($reqId, 'ACCOMPANY', 'GENERAL', $date, $counter);
						} else {
							$invoiceIdAcompany = insertingInvoiceDetails($reqId, 'ACCOMPANY', 'GENERAL', $date);
						}
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

			//if($accommDetails)
			if (!empty($userDetails['checkin_id']) && !empty($userDetails['checkout_id']) && !empty($userDetails['hotel_id'])) {
				$check_in_date_id                 = $userDetails['checkin_id'];
				$check_out_date_id                = $userDetails['checkout_id'];
				$accommodation_hotel_id           = $userDetails['hotel_id'];
				//$accommodation_hotel_type_id      = $accommDetails['accommodation_roomType_id'];
				$totalRoom = 0;
				$totalGuestCounter                 = 0;
				/*foreach($accommDetails['room_guest_counter'] as $key=>$resDetails )
			{
				$totalRoom++;
				if($resDetails!=""){
							
					$totalGuestCounter        += $resDetails;
				}
			}*/
				$sqlAccommodationDate['QUERY']           = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
											   WHERE `id` = '" . $check_in_date_id . "'";

				$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate);
				$rowAccommodationDate           = $resultAccommodationDate[0];

				$check_in_date              = $rowAccommodationDate['check_in_date'];

				// GET ACCOMMODATION OUT DATE
				$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
													   WHERE `id` = '" . $check_out_date_id . "'";

				$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate);
				$rowAccommodationOutDate           = $resultAccommodationOutDate[0];

				$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];


				$sqlFetchHotel['QUERY']    = "SELECT id 
								   FROM " . _DB_PACKAGE_ACCOMMODATION_ . "  
								  WHERE  `hotel_id` = '" . $accommodation_hotel_id . "'
									  AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'

				$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
				$resultfetch 	  = $resultFetchHotel[0];
				$packageId 	      = $resultfetch['id'];
				$accTariffId = getAccommodationTariffId($packageId, $check_in_date_id, $check_out_date_id, $cutoffId);

				$accomodationDetails['user_id']											 = $delegateId;
				//$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
				$accomodationDetails['accommodation_details']							 = '';
				$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
				//$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
				//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
				$accomodationDetails['package_id']										 = 0;
				$accomodationDetails['tariff_ref_id']								     = $accTariffId;
				$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
				$accomodationDetails['checkin_date']									 = $check_in_date;
				$accomodationDetails['checkout_date']									 = $check_out_date;
				$accomodationDetails['booking_quantity']								 = 1; //$accommDetails['booking_quantity'];
				$accomodationDetails['refference_invoice_id']							 = 0;
				$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
				$accomodationDetails['booking_mode']									 = $userDetails['registration_mode'];
				$accomodationDetails['payment_status']									 = 'ZERO_VALUE';

				$accompReqId	 = insertingAccomodationDetails($accomodationDetails);


				$accommRoomDetails['user_id']										 = $delegateId;
				$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
				$accommRoomDetails['room_id']										 = 1;
				$accommRoomDetails['checkin_id']								     = $check_in_date_id;
				$accommRoomDetails['checkout_id']								     = $check_out_date_id;
				$accommRoomDetails['checkin_date']								     = $check_in_date;
				$accommRoomDetails['checkout_date']									 = $check_out_date;



				$accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);

				$accomdInvoiceId = insertingInvoiceDetails($accompReqId, 'ACCOMMODATION');
				zeroValueInvoiceUpdate($accomdInvoiceId, 'ACCOMMODATION', $mycms->getSession('SLIP_ID'));

				$sqlProcessUpdateRoom['QUERY']  = " UPDATE  " . _DB_USER_REGISTRATION_ . "
												   SET `accommodation_room` = '1'
												 WHERE `id` = '" . $delegateId . "' AND status='A'";
				$mycms->sql_update($sqlProcessUpdateRoom, false);
			}


			if ($tourDetails) {

				foreach ($tourDetails['tour_id'] as $key => $val) {
					$tourDetailsArray[$val]['user_id']               = $delegateId;
					$tourDetailsArray[$val]['package_id']            = $val;
					$tourDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$tourDetailsArray[$val]['booking_date']          = getTourDate($val);
					$tourDetailsArray[$val]['booking_quantity']      = $tourDetails['number_of_person'][$val];
					$tourDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$tourDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
					$tourDetailsArray[$val]['refference_slip_id']	 = $mycms->getSession('SLIP_ID');
					$tourDetailsArray[$val]['payment_status']        = 'UNPAID';
				}

				$tourReqId    	= insertingTourDetails($tourDetailsArray);
				foreach ($tourReqId as $key => $reqId) {
					insertingInvoiceDetails($reqId, 'TOUR');
				}
			}

			if ($dinnerDetails) {

				foreach ($dinnerDetails as $key => $val) {
					$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
					$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
					$dinnerDetailsArray[$val]['package_id']            = $val;
					$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray[$val]['booking_quantity']      = 1;
					$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray[$val]['payment_status']        = 'UNPAID';
				}

				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);
				foreach ($dinerReqId as $key => $reqId) {
					if ($counter == 'Y') {
						$invoiceIdDinner  =  insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date, $counter);
					} else {
						$invoiceIdDinner  =  insertingInvoiceDetails($reqId, 'DINNER', $userDetails['registration_request'], $date);
					}

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
			}

			setUserPaymentStatus($delegateId);


			//registration_acknowledgement_message($delegateId, $mycms->getSession('SLIP_ID'), 'SEND');

			if ($userDetails['reg_area'] == "FRONT") {
				online_welcome_message($delegateId, 'SEND');
			}

			if ($userDetails['reg_area'] == "BACK") {
				$mycms->removeSession('PROCESS_FLOW_ID');
				$mycms->removeSession('USER_DETAILS');
				$mycms->getSession('USER_DETAILS', array());
			} else if ($userDetails['reg_area'] == "FRONT") {
				$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
				$mycms->removeSession('CUTOFF_ID_FRONT');
				$mycms->removeSession('CLSF_ID_FRONT');
				$mycms->removeSession('USER_DETAILS_FRONT');
				$mycms->setSession('USER_DETAILS_FRONT', array());
			}
		}
	}
	if ($delegateId) {
		$sqlExhibitorDetails    = array();
		$sqlExhibitorDetails['QUERY']	= 	"UPDATE " . _DB_EXHIBITOR_REGISTRATION_ . " SET `account_status`='REGISTERED', `delegate_id`= '" . $delegateId . "'  															
											WHERE status = 'A'										
											AND id='" . $exhibitorId . "' ";

		$resultUserDetails			= $mycms->sql_update($sqlExhibitorDetails);
	}
	return $delegateId;
}
