<?php
function getTotalWorkshopCount($delegatesId)
{
	global $cfg, $mycms;

	$totalAccompanyCount  = 0;

	$sqlFetch            = array();
	$sqlFetch['QUERY']	= "SELECT COUNT(*) AS totalWorkshopCount
							   FROM " . _DB_REQUEST_WORKSHOP_ . "
							  WHERE status = ?
								AND delegate_id = ?";

	$sqlFetch['PARAM'][]   = array('FILD' => 'status',          'DATA' => 'A',           'TYP' => 's');
	$sqlFetch['PARAM'][]   = array('FILD' => 'delegate_id',     'DATA' => $delegatesId,  'TYP' => 's');


	$result			      = $mycms->sql_select($sqlFetch);
	return $result[0]['totalWorkshopCount'];
}

function registrationDetailsQuery($delegateId = "", $searchCondition = "", $orderCondition = '')
{
	global $cfg, $mycms;



	if ($delegateId != "") {

		$filterCondition 		  = " AND delegate.id = ?";
		$filterConditionPARAM[]   = array('FILD' => 'delegate.id',  'DATA' => $delegateId,  'TYP' => 's');
	}
	$sqlDelegateQueryset 		=	array();
	$sqlDelegateQueryset['QUERY']       = "SELECT delegate.*,
										 
										 country.country_name,
										 state.state_name,
										 
										 tariffCutoff.cutoff_title AS cutoffTitle,
										 registrationClassification.classification_title
										 
				 
									FROM " . _DB_USER_REGISTRATION_ . " delegate 
									
						 LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
									  ON delegate.registration_classification_id = registrationClassification.id
									 
						 LEFT OUTER JOIN " . _DB_TARIFF_CUTOFF_ . " AS tariffCutoff
									  ON delegate.registration_tariff_cutoff_id = tariffCutoff.id
						 
						 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
									  ON delegate.user_country_id = country.country_id
											 
						 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
									  ON delegate.user_state_id = state.st_id
						 
								   WHERE delegate.user_type = ?
									AND delegate.status != ? " . $filterCondition . " " . $searchCondition . " ";

	$sqlDelegateQueryset['PARAM'][]	=	array('FILD' => 'delegate.user_type', 	  'DATA' => 'DELEGATE',             'TYP' => 's');
	$sqlDelegateQueryset['PARAM'][]	=	array('FILD' => 'delegate.status ', 	  'DATA' => 'D',             		 'TYP' => 's');
	foreach ($filterConditionPARAM as $k => $val) {
		$sqlDelegateQueryset['PARAM'][] = $val;
	}
	$resDetails          = $mycms->sql_select($sqlDelegateQueryset);
	return $resDetails[0];
}

function registrationDetailsCompressedQuery($delegateId = "", $searchCondition = "")
{
	global $cfg, $mycms;

	$filterCondition           = "";

	if ($delegateId != "") {
		$filterCondition 		  = " AND delegate.id = ?";
		$filterConditionPARAM[]   = array('FILD' => 'delegate.id',  'DATA' => $delegateId,  'TYP' => 's');
	}
	$sqlDelegateQueryset				=	array();
	$sqlDelegateQueryset['QUERY']       = "SELECT delegate.*,
										 
											 country.country_name,
											 state.state_name,
										 
											 tariffCutoff.cutoff_title AS cutoffTitle,
											 registrationClassification.classification_title
											 
											FROM " . _DB_USER_REGISTRATION_ . " delegate 
										
							 LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
										  ON delegate.registration_classification_id = registrationClassification.id
										 
							 LEFT OUTER JOIN " . _DB_TARIFF_CUTOFF_ . " AS tariffCutoff
										  ON delegate.registration_tariff_cutoff_id = tariffCutoff.id
							 
							 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
										  ON delegate.user_country_id = country.country_id
												 
							 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
										  ON delegate.user_state_id = state.st_id
																	
									   WHERE delegate.user_type = ?
										 AND delegate.status = ? " . $filterCondition . " " . $searchCondition . " 
											  
									ORDER BY created_dateTime DESC";

	$sqlDelegateQueryset['PARAM'][]	=	array('FILD' => 'delegate.user_type', 	  'DATA' => 'DELEGATE',             'TYP' => 's');
	$sqlDelegateQueryset['PARAM'][]	=	array('FILD' => 'delegate.status', 	  'DATA' => 'A',             		 'TYP' => 's');
	foreach ($filterConditionPARAM as $k => $val) {
		$sqlDelegateQueryset['PARAM'][] = $val;
	}
	$resDetails          = $mycms->sql_select_paginated(1, $sqlDelegateQueryset, 10, $restrt);
	return $resDetails;
}

function deletedRegistrationDetailsCompressedQuery($delegateId = "", $searchCondition = "")
{
	global $cfg, $mycms;

	$filterCondition           = "";

	if ($delegateId != "") {
		$filterCondition 		  = " AND delegate.id = ?";
		$filterConditionPARAM[]   = array('FILD' => 'delegate.id',  'DATA' => $delegateId,  'TYP' => 's');
	}
	$sqlDelegateQueryset				=	array();
	$sqlDelegateQueryset['QUERY']       = "SELECT delegate.*,
										 
											 country.country_name,
											 state.state_name,
										 
											 tariffCutoff.cutoff_title AS cutoffTitle,
											 registrationClassification.classification_title
											 
											FROM " . _DB_USER_REGISTRATION_ . " delegate 
										
							 LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
										  ON delegate.registration_classification_id = registrationClassification.id
										 
							 LEFT OUTER JOIN " . _DB_TARIFF_CUTOFF_ . " AS tariffCutoff
										  ON delegate.registration_tariff_cutoff_id = tariffCutoff.id
							 
							 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
										  ON delegate.user_country_id = country.country_id
												 
							 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
										  ON delegate.user_state_id = state.st_id
																	
									   WHERE delegate.user_type = ?
										 AND delegate.status = ? " . $filterCondition . " " . $searchCondition . " 
											  
									ORDER BY created_dateTime DESC";

	$sqlDelegateQueryset['PARAM'][]	=	array('FILD' => 'delegate.user_type', 	  'DATA' => 'DELEGATE',             'TYP' => 's');
	$sqlDelegateQueryset['PARAM'][]	=	array('FILD' => 'delegate.status', 	  'DATA' => 'D',             		 'TYP' => 's');
	foreach ($filterConditionPARAM as $k => $val) {
		$sqlDelegateQueryset['PARAM'][] = $val;
	}
	$resDetails          = $mycms->sql_select_paginated(1, $sqlDelegateQueryset, 10, $restrt);
	return $resDetails;
}

function getAllDelegates($delegateId = "", $orderCondition = '', $alterCondition = "", $serach = "", $Qindex = '1', $organised = false)
{
	global $cfg, $mycms;
	$detailsArray			   = array();
	$ids					   = array();

	@$searchCondition       = "";
	$searchCondition       .= " AND delegate.operational_area !='EXHIBITOR'
								AND delegate.isRegistration = 'Y'
								AND delegate.status IN('A','C')
								";

	if ($_REQUEST['src_email_id'] != '') {
		$searchCondition   .= " AND delegate.user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%'";
	}
	if ($_REQUEST['src_access_key'] != '') {
		$searchCondition   .= " AND delegate.user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%'";
	}
	if ($_REQUEST['src_mobile_no'] != '') {
		$searchCondition   .= " AND delegate.user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%'";
	}
	if ($_REQUEST['src_user_first_name'] != '') {
		$searchCondition   .= " AND (delegate.user_first_name  LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
									 OR delegate.user_middle_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
									 OR delegate.user_last_name   LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
									 OR delegate.user_full_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%')";
	}
	if ($_REQUEST['src_payment_mode'] != '') {
		$searchCondition   .= " AND payment.payment_mode = '" . $_REQUEST['src_payment_mode'] . "'";
	}
	if ($_REQUEST['src_transaction_id'] != '') {
		$searchCondition   .= " AND paymentReq.transaction_id = '" . $_REQUEST['src_transaction_id'] . "'";
	}
	if ($_REQUEST['src_registration_mode'] != '') {
		$searchCondition   .= " AND invoice.invoice_mode LIKE '%" . $_REQUEST['src_registration_mode'] . "%'";
	}
	if ($_REQUEST['src_user_last_name'] != '') {
		$searchCondition   .= " AND delegate.user_last_name LIKE '%" . $_REQUEST['src_user_last_name'] . "%'";
	}
	if ($_REQUEST['src_invoice_no'] != '') {
		$searchCondition   .= " AND invoice.invoice_number LIKE '%" . $_REQUEST['src_invoice_no'] . "%'";
	}
	if ($_REQUEST['src_slip_no'] != '') {
		$searchCondition   .= " AND slip.slip_number LIKE '%" . $_REQUEST['src_slip_no'] . "%'";
	}
	if ($_REQUEST['src_transaction_ids'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND payment.atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_ids'] . "%'";
	}
	if ($_REQUEST['src_transaction_slip_no'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND (   payment.card_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.rrn_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.cheque_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.draft_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.neft_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.rtgs_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.atom_transaction_card_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.atom_bank_transaction_id LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
									 OR payment.remarks LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%' )";
	}
	if ($_REQUEST['src_atom_transaction_ids'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND LOCATE('" . $_REQUEST['src_atom_transaction_ids'] . "', totalInvoicePayment.atomAtomTransactionIds) > 0";
	}
	if ($_REQUEST['src_conf_reg_category'] != '') {
		$searchCondition   .= " AND delegate.registration_classification_id = '" . $_REQUEST['src_conf_reg_category'] . "'";
	}
	if ($_REQUEST['src_reg_category'] != '') {
		if ($_REQUEST['src_reg_category'] == 'Conference') {
			$searchCondition   .= " AND delegate.isAccommodation != 'Y'";
		} elseif ($_REQUEST['src_reg_category'] == 'Residential') {
			$searchCondition   .= " AND delegate.isAccommodation = 'Y'";
		}
	}
	if ($_REQUEST['src_registration_type'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND delegate.registration_request LIKE '%" . $_REQUEST['src_registration_type'] . "%'";
	}
	if ($_REQUEST['src_registration_id'] != '') {
		$searchCondition   .= " AND (delegate.user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "%' 
									 AND (delegate.registration_payment_status = 'ZERO_VALUE' 
										  OR delegate.registration_payment_status = 'COMPLEMENTARY'
										  OR delegate.registration_payment_status = 'PAID'))";
	}
	if ($_REQUEST['src_payment_status'] != '') {
		$searchCondition   .= " AND delegate.registration_payment_status = '" . $_REQUEST['src_payment_status'] . "'";
	}
	if ($_REQUEST['src_workshop_classf'] != '') {
		$id =  trim($_REQUEST['src_workshop_classf']);
		$workshop_id = substr($id, 0, 1);
		$payment_status = substr($id, 1);
		if ($payment_status == "P") {
			$status = "PAID";
		} else if ($payment_status == "U") {
			$status = "UNPAID";
		} else if ($payment_status == "C") {
			$status = "COMPLEMENTARY";
		} else {
			$status = "ALL";
		}

		if ($status != "ALL") {
			$searchCondition   .= " AND workshop.workshop_id = '" . $workshop_id . "' AND workshop.payment_status = '" . $status . "' AND workshop.status = 'A' ";
		} else {
			$searchCondition   .= " AND workshop.workshop_id = '" . $workshop_id . "' AND workshop.status = 'A' ";
		}
	}
	if ($_REQUEST['src_accommodation'] != '') {
		$searchCondition   .= " AND invoice.service_type = 'DELEGATE_ACCOMMODATION_REQUEST' AND invoice.payment_status = '" . $_REQUEST['src_accommodation'] . "' 
									AND invoice.status = 'A' ";
	}
	if ($_REQUEST['src_country_name'] != "") {
		$searchCondition .= "AND delegate.user_country_id = '" . $_REQUEST['src_country_name'] . "%'";
	}
	if ($_REQUEST['src_state_name'] != "") {
		$searchCondition .= "AND delegate.user_state_id = '" . $_REQUEST['src_state_name'] . "'";
	}

	if ($_REQUEST['src_payment_date'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND payment.payment_date = '" . $_REQUEST['src_payment_date'] . "'";
	}

	if ($_REQUEST['src_registration_from_date'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND delegate.conf_reg_date BETWEEN  '" . $_REQUEST['src_registration_from_date'] . " 00:00:00'
								
								AND '" . $_REQUEST['src_registration_to_date'] . " 23:59:59'";
	}

	// if($_REQUEST['src_registration_to_date']!='')
	// {
	// $searchApplication	= 1;
	// $searchCondition   .= " AND delegate.conf_reg_date = '".$_REQUEST['src_registration_to_date']."'";
	// }

	if ($_REQUEST['src_cancel_invoice_id'] != '') {
		$searchCondition   .= " AND invoice.invoice_number LIKE '%" . $_REQUEST['src_cancel_invoice_id'] . "%' AND  invoice.status = 'C'";
	}

	if ($_REQUEST['src_hasPickup'] != '') {
		$searchCondition   .= " AND delegate.id IN (SELECT user_id FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " WHERE pikup_time IS NOT NULL)";
	}

	if ($_REQUEST['src_hasDropoff'] != '') {
		$searchCondition   .= " AND delegate.id IN (SELECT user_id FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " WHERE dropoff_time IS NOT NULL)";
	}

	if ($_REQUEST['src_hasNotes'] != '') {
		$searchCondition   .= " AND TRIM(delegate.user_food_preference_in_details) != ''";
	}

	if ($_REQUEST['src_hasPayentTerSetButNotPaid'] != '') {
		$searchCondition   .= " AND slip.id IN (SELECT slip_id	 FROM " . _DB_PAYMENT_ . " payment	WHERE status = 'A' AND payment_status = 'UNPAID')";
	}


	$sqlDelegateQueryset = array();
	$sqlDelegateQueryset['QUERY']      = "SELECT DISTINCT delegate.id
				 
											FROM " . _DB_INVOICE_ . " invoice 
								
									  INNER JOIN " . _DB_USER_REGISTRATION_ . " AS delegate
											  ON delegate.id = invoice.delegate_id
											  
									 INNER JOIN " . _DB_SLIP_ . " AS slip
											  ON slip.id = invoice.slip_id
											  
									 LEFT OUTER JOIN " . _DB_CANCEL_INVOICE_ . " AS cancelInvoice
											  ON cancelInvoice.invoice_id = invoice.id
											  
									 LEFT OUTER JOIN " . _DB_PAYMENT_ . " AS payment
											  ON payment.slip_id = slip.id		 
											  
									 LEFT OUTER  JOIN " . _DB_REQUEST_WORKSHOP_ . " AS workshop
											  ON workshop.delegate_id = delegate.id	 
											  
									 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
											  ON delegate.user_country_id = country.country_id
													 
								 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
											  ON delegate.user_state_id = state.st_id
								
								 WHERE delegate.user_type = 'DELEGATE'
											 AND delegate.status != 'D'" . $searchCondition . " " . $alterCondition . "";

	if (trim($orderCondition) != '') {
		$sqlDelegateQueryset['QUERY']   .=  " " . $orderCondition . " ";
	} else {
		$sqlDelegateQueryset['QUERY']   .=  " ORDER BY delegate.id DESC";
	}

	//echo '<!--'; print_r($sqlDelegateQueryset['QUERY']); echo '-->';

	if ($organised) {
		$resultFetchUserAll     	  = $mycms->sql_select($sqlDelegateQueryset);

		if ($resultFetchUserAll) {
			foreach ($resultFetchUserAll as $i => $rowFetchUserAll) {
				$ids['ALL-IDS'][] = $rowFetchUserAll['id'];
			}
		}
	}

	if ($serach == "serach") {
		$resultFetchUser     	  = $mycms->sql_select_paginated($Qindex, $sqlDelegateQueryset, 10);
	} else {
		$resultFetchUser     	  = $mycms->sql_select_paginated($Qindex, $sqlDelegateQueryset, 30);
	}

	if ($resultFetchUser) {
		foreach ($resultFetchUser as $i => $rowFetchUser) {
			if ($organised) {
				$ids['IDS'][] = $rowFetchUser['id'];
			} else {
				$ids[] = $rowFetchUser['id'];
			}
		}
	}
	return $ids;
}

function getAllRegistrations($delegateId = "", $orderCondition = '', $alterCondition = "", $serach = "", $Qindex = '1')
{
	global $cfg, $mycms;
	$sqlBigJoin                = "SET OPTION SQL_BIG_SELECTS = 1";
	mysql_query($sqlBigJoin);
	$detailsArray			   = array();
	$ids					   = array();

	@$searchCondition       = "";
	$searchCondition       .= " AND delegate.operational_area !='EXHIBITOR'
								AND delegate.isRegistration = 'Y'
								AND delegate.status IN('A','C')
								AND delegate.registration_request != 'GUEST'
								";


	if ($_REQUEST['src_email_id'] != '') {
		$searchCondition   .= " AND delegate.user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%'";
	}
	if ($_REQUEST['src_access_key'] != '') {
		$searchCondition   .= " AND delegate.user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%'";
	}
	if ($_REQUEST['src_mobile_no'] != '') {
		$searchCondition   .= " AND delegate.user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%'";
	}
	if ($_REQUEST['src_user_first_name'] != '') {


		$searchCondition   .= " AND (delegate.user_first_name  LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
									 OR delegate.user_middle_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
									 OR delegate.user_last_name   LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
									 OR delegate.user_full_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%')";
	}
	if ($_REQUEST['src_registration_mode'] != '') {
		$searchCondition   .= " AND delegate.registration_mode LIKE '%" . $_REQUEST['src_registration_mode'] . "%'";
	}
	if ($_REQUEST['src_user_last_name'] != '') {
		$searchCondition   .= " AND delegate.user_last_name LIKE '%" . $_REQUEST['src_user_last_name'] . "%'";
	}
	if ($_REQUEST['src_invoice_no'] != '') {
		$searchCondition   .= " AND invoice.invoice_number LIKE '%" . $_REQUEST['src_invoice_no'] . "%'";
	}
	if ($_REQUEST['src_slip_no'] != '') {
		$searchCondition   .= " AND slip.slip_number LIKE '%" . $_REQUEST['src_slip_no'] . "%'";
	}
	if ($_REQUEST['src_payment_mode'] != '') {
		$searchApplication	 = 1;
		$searchCondition   .= " AND payment.payment_mode = '" . $_REQUEST['src_payment_mode'] . "'";
	}
	if ($_REQUEST['src_payment_no'] != '') {
		$searchApplication	 = 1;
		$searchCondition   .= " AND (payment.card_transaction_no LIKE '%" . $_REQUEST['src_payment_no'] . "%'
										OR payment.cheque_number LIKE '%" . $_REQUEST['src_payment_no'] . "%'
										OR payment.draft_number LIKE '%" . $_REQUEST['src_payment_no'] . "%'
										OR payment.neft_transaction_no LIKE '%" . $_REQUEST['src_payment_no'] . "%'
										OR payment.rtgs_transaction_no LIKE '%" . $_REQUEST['src_payment_no'] . "%'
										OR payment.atom_atom_transaction_id LIKE '%" . $_REQUEST['src_payment_no'] . "%')";
	}
	if ($_REQUEST['src_transaction_ids'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND payment.atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_ids'] . "%'";
	}
	if ($_REQUEST['src_payment_date'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND payment.payment_date = '" . $_REQUEST['src_payment_date'] . "'";
	}
	if ($_REQUEST['src_workshop_type'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND workshop.workshop_id = '" . $_REQUEST['src_workshop_type'] . "' AND workshop.status = 'A' ";
	}
	if ($_REQUEST['src_atom_transaction_ids'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND LOCATE('" . $_REQUEST['src_atom_transaction_ids'] . "', totalInvoicePayment.atomAtomTransactionIds) > 0";
	}
	if ($_REQUEST['src_registration_type'] != '') {
		$searchApplication	= 1;
		$searchCondition   .= " AND delegate.registration_request LIKE '%" . $_REQUEST['src_registration_type'] . "%'";
	}
	if ($_REQUEST['src_payment_status'] != '') {
		$searchApplication	= 1;
		if ($_REQUEST['src_payment_status'] == "CREDIT") {
			$searchCondition   .= " AND delegate.id IN (SELECT DISTINCT delegate_id FROM " . _DB_PAYMENT_ . " WHERE payment_mode = 'Credit')";
		} else {
			$searchCondition   .= " AND delegate.registration_payment_status = '" . $_REQUEST['src_payment_status'] . "'";
		}
	}
	if ($_REQUEST['src_conf_reg_category'] != '') {
		$searchCondition   .= " AND delegate.registration_classification_id = '" . $_REQUEST['src_conf_reg_category'] . "'";
	}
	if ($_REQUEST['src_cancel_invoice_id'] != '') {
		$searchCondition   .= " AND invoice.invoice_number LIKE '%" . $_REQUEST['src_cancel_invoice_id'] . "%' AND  invoice.status = 'C'";
	}
	if ($_REQUEST['src_invoice_remark'] != '') {
		$searchCondition   .= " AND invoice.remarks LIKE '%" . $_REQUEST['src_invoice_remark'] . "%'";
	}
	if ($_REQUEST['src_reg_category'] != '') {
		if ($_REQUEST['src_reg_category'] == 'Conference') {
			$searchCondition   .= " AND delegate.isAccommodation != 'Y'";
		} elseif ($_REQUEST['src_reg_category'] == 'Residential') {
			$searchCondition   .= " AND delegate.isAccommodation = 'Y'";
		}
	}
	if ($_REQUEST['src_registration_id'] != '') {
		$searchCondition   .= " AND (delegate.user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "' 
									 AND (delegate.registration_payment_status = 'ZERO_VALUE' 
										  OR delegate.registration_payment_status = 'COMPLIMENTARY'
										  OR delegate.registration_payment_status = 'PAID'))";
	}
	if ($_REQUEST['src_workshop_classf'] != '') {
		$id =  trim($_REQUEST['src_workshop_classf']);
		$workshop_id = substr($id, 0, 1);
		$payment_status = substr($id, 1);
		if ($payment_status == "P") {
			$status = "PAID";
		} else if ($payment_status == "U") {
			$status = "UNPAID";
		} else if ($payment_status == "C") {
			$status = "COMPLIMENTARY";
		} else {
			$status = "ALL";
		}

		if ($status != "ALL") {
			$searchCondition   .= " AND workshop.workshop_id = '" . $workshop_id . "' AND workshop.payment_status = '" . $status . "' AND workshop.status = 'A' ";
		} else {
			$searchCondition   .= " AND workshop.workshop_id = '" . $workshop_id . "' AND workshop.status = 'A' ";
		}
	}

	$filterCondition           = "";

	if ($delegateId != "") {
		$filterCondition      .= " AND delegate.id = '" . $delegateId . "'";
	}

	$sqlDelegateQueryset['QUERY']       = "SELECT DISTINCT delegate.id
				 
											FROM " . _DB_INVOICE_ . " invoice 
								
									  INNER JOIN " . _DB_TARIFF_REGISTRATION_ . " AS delegate
											  ON delegate.id = invoice.delegate_id
											  
									  INNER JOIN " . _DB_SLIP_ . " AS slip
											  ON slip.id = invoice.slip_id
											  
								 LEFT OUTER JOIN " . _DB_CANCEL_INVOICE . " AS cancelInvoice
											  ON cancelInvoice.invoice_id = invoice.id
											  
								 LEFT OUTER JOIN " . _DB_PAYMENT_ . " AS payment
											  ON payment.slip_id = slip.id
														
								 LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
											  ON delegate.registration_classification_id = registrationClassification.id
											 
								 LEFT OUTER JOIN " . _DB_TARIFF_CUTOFF_ . " AS tariffCutoff
											  ON delegate.registration_tariff_cutoff_id = tariffCutoff.id
								 
								 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
											  ON delegate.user_country_id = country.country_id
													 
								 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
											  ON delegate.user_state_id = state.st_id
											  
								 LEFT OUTER  JOIN " . _DB_REQUEST_WORKSHOP_ . " AS workshop
											  ON workshop.delegate_id = delegate.id	
											  
										   WHERE delegate.user_type = 'DELEGATE'
											 AND delegate.status != 'D' " . $filterCondition . " " . $searchCondition . " " . $alterCondition . "";


	if (trim($orderCondition) != '') {
		$sqlDelegateQueryset  .=  " " . $orderCondition . " ";
	} else {
		$sqlDelegateQueryset  .=  " ORDER BY delegate.id DESC";
	}


	if ($serach == "serach") {
		$resultFetchUser     	  = $mycms->sql_select($sqlDelegateQueryset);
	} else {
		$resultFetchUser     	  = $mycms->pagination($Qindex, $sqlDelegateQueryset, 10, $restrt);
	}
	if ($resultFetchUser) {
		foreach ($resultFetchUser as $i => $rowFetchUser) {
			$ids[] = $rowFetchUser['id'];
		}
	}
	return $ids;
}

function getUserClassificationId($delegateId, $status = false)
{
	global $cfg, $mycms;
	if ($status) {
		$searchCondition = "AND status IN('A','C')";
	} else {
		$searchCondition = "AND status = 'A'";
	}
	$sqlDetails['QUERY'] = "SELECT * 
							  FROM " . _DB_USER_REGISTRATION_ . " 
							  WHERE `id` = '" . $delegateId . "' " . $searchCondition . "";
	$resDetails = $mycms->sql_select($sqlDetails);

	return $resDetails[0]['registration_classification_id'];
}

function getUserCutoffId($delegateId)
{
	global $cfg, $mycms;

	$sqlDetails 	=	array();
	$sqlDetails['QUERY'] = "SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE status = 'A' AND `id` = '" . $delegateId . "'";
	$resDetails = $mycms->sql_select($sqlDetails);

	return $resDetails[0]['registration_tariff_cutoff_id'];
}

function getAcompanyDetailsOfDelegate($delegateId, $fullDetails = false)
{
	global $cfg, $mycms;
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT reg.*, inv.id AS invoiceId
							  FROM " . _DB_USER_REGISTRATION_ . " reg
				   LEFT OUTER JOIN " . _DB_INVOICE_ . " inv
				   				ON inv.`delegate_id` = reg.refference_delegate_id
							   AND inv.`refference_id` = reg.id
							   AND inv.`status` = 'A'
							   AND inv.`service_type` IN('ACCOMPANY_CONFERENCE_REGISTRATION','DELEGATE_CONFERENCE_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION')
							 WHERE reg.`user_type` = ? 
							   AND reg.`refference_delegate_id` =? 
							   AND reg.status = ?";

	$sqlDetails['PARAM'][]   = array('FILD' => 'user_type',               'DATA' => 'ACCOMPANY',  'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'refference_delegate_id',  'DATA' => $delegateId,  'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',                  'DATA' => 'A',          'TYP' => 's');

	$resDetails = $mycms->sql_select($sqlDetails);

	if ($fullDetails) {
		$return = array();
		$countr = 0;
		foreach ($resDetails as $k => $row) {
			if ($row['invoiceId'] != '') {
				$invoiceDetails 					= getInvoiceDetails($row['invoiceId']);
				$return[$countr] 					= $row;
				$return[$countr]['INVOICE_DETAILS'] = $invoiceDetails;
				$countr++;
			}
		}
	} else {
		$return = $resDetails;
	}

	return $return;
}

function getAddOnAccompanyDetails($delegateId)
{
	global $cfg, $mycms;
	$sqlDetails['QUERY'] = "SELECT delegate.id AS delegateId,
						  delegate.user_email_id AS delegateEmailId,
						  delegate.user_unique_sequence AS delegateUniqueSequence,
						  delegate.user_registration_id AS delegateRegId,
						  delegate.user_full_name AS delegateName,
						  accompany.*
						  
					 FROM " . _DB_USER_REGISTRATION_ . " accompany
			   INNER JOIN " . _DB_USER_REGISTRATION_ . " delegate
					   ON delegate.id = accompany.refference_delegate_id
					WHERE delegate.status = 'A' 
					  AND accompany.status = 'A' 
					  AND accompany.accompany_relationship = 'ADD_ON' 
					  AND delegate.id = '" . $delegateId . "'";
	$resDetails = $mycms->sql_select($sqlDetails);

	return $resDetails[0];
}

function insertingUserDetails($userDetailsArray, $date = '')
{
	//  echo '<pre>'; print_r($userDetailsArray); die;

	global $cfg, $mycms;
	if ($date == '') {
		$date = date('Y-m-d H:i:s');
	}
    if(!empty($userDetailsArray['combo_registration']))
	{
		$userDetailsArray['isCombo'] = 'Y';
	}else{
			$userDetailsArray['isCombo'] = 'N';
	}
	$sqlInsertUser = array();

	$sqlInsertUser['QUERY']                    = "INSERT INTO " . _DB_USER_REGISTRATION_ . "
													 SET `refference_delegate_id`		        = ?,
														 `user_type` 	        		        = ?,
														 `user_email_id`        		        = ?,
														 `user_title`            		        = ?,
														 `user_first_name`       		        = ?,
														 `user_middle_name`     		        = ?,
														 `user_last_name`       		        = ?,
														 `user_full_name` 				        = ?,
														 `user_mobile_isd_code`  		        = ?,
														 `user_mobile_no`				        = ?,
														 `user_address`					        = ?,	
														 `user_country_id`		 		        = ?,
														 `user_state_id`				        = ?,	
														 `user_city`					        = ?,
														 `user_pincode`					        = ?,
														 `user_dob` 					        = ?,
														 `user_gender`					        = ?,
														 `user_designation` 			        = ?,
														 `user_department` 			 	        = ?,
														 `user_institute_name`			        = ?,
														 `user_food_preference` 		        = ?,
														 `user_food_preference_in_details`      = ?,
														 `passport_no`     				        = ?,
														 `user_document`     			        = ?,
														 `isRegistration`       		        = ?,
														
														 `combo_registration`       		    = ?,
														 `accDateCombo`       		        	= ?,
														 `accommodation_room`                   = ?,
														 `isConference`         		        = ?,
														 `isWorkshop` 					        = ?,
														 `isAccommodation`				        = ?,
														 `isTour` 						        = ?,
														 `isAbstract`   	    		        = ?,
														 `isCombo`   	    		       		= ?,
														 `registration_classification_id`       = ?,
														 `registration_tariff_cutoff_id`        = ?,
														 `workshop_tariff_cutoff_id`       		= ?,
														 `membership_number`  		  	        = ?,
														 `registration_request`  		        = ?,
														 `operational_area`        		        = ?,
														 `registration_payment_status` 	        = ?,
														 `registration_mode`    		        = ?,
														 `account_status`				        = ?,
														 `reg_type`						        = ?,
														 `status`						        = ?,
														 `conf_reg_date`        		        = ?,
														 `created_ip` 					        = ?,
														 `created_sessionId`			        = ?,
														 `created_dateTime` 			        = ?";

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'refference_delegate_id',              'DATA' => '0',                                                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_type',                           'DATA' => 'DELEGATE',                                           'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_email_id',                       'DATA' => $userDetailsArray['user_email_id'],                   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_title',                          'DATA' => $userDetailsArray['user_initial_title'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_first_name',                     'DATA' => $userDetailsArray['user_first_name'],                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_middle_name',                    'DATA' => $userDetailsArray['user_middle_name'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_last_name',                      'DATA' => $userDetailsArray['user_last_name'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_full_name',                      'DATA' => $userDetailsArray['user_full_name'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_mobile_isd_code',                'DATA' => $userDetailsArray['user_mobile_isd_code'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_mobile_no',                      'DATA' => $userDetailsArray['user_mobile_no'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_address',                        'DATA' => $userDetailsArray['user_address'],                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_country_id',                     'DATA' => $userDetailsArray['user_country'],                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_state_id',                       'DATA' => $userDetailsArray['user_state'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_city',                           'DATA' => $userDetailsArray['user_city'],                       'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_pincode',                        'DATA' => $userDetailsArray['user_postal_code'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_dob',                            'DATA' => $userDetailsArray['user_dob'],                        'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_gender',                         'DATA' => $userDetailsArray['user_gender'],                     'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_designation',                    'DATA' => $userDetailsArray['user_designation'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_department',                     'DATA' => $userDetailsArray['user_depertment'],                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_institute_name',                 'DATA' => $userDetailsArray['user_institution_name'],           'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_food_preference',                'DATA' => $userDetailsArray['user_food_preference'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_food_preference_in_details',     'DATA' => $userDetailsArray['user_other_food_details'],         'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'passport_no',                         'DATA' => $userDetailsArray['passport_no'],                     'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_document',                       'DATA' => $userDetailsArray['user_document'],                   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isRegistration',                      'DATA' => $userDetailsArray['isRegistration'],                  'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'combo_registration',                      		 'DATA' => $userDetailsArray['combo_registration'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accDateCombo',                     	 'DATA' => $userDetailsArray['accDateCombo'],                  'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accommodation_room',                  'DATA' => 0, 'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isConference',                        'DATA' => $userDetailsArray['isConference'],                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isWorkshop',                          'DATA' => $userDetailsArray['isWorkshop'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isAccommodation',                     'DATA' => $userDetailsArray['isAccommodation'],                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isTour',                              'DATA' => $userDetailsArray['isTour'],                          'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isAbstract',                          'DATA' => $userDetailsArray['IsAbstract'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isCombo',                          	 'DATA' => $userDetailsArray['isCombo'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_classification_id',      'DATA' => $userDetailsArray['registration_classification_id'],  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_tariff_cutoff_id',       'DATA' => $userDetailsArray['registration_tariff_cutoff_id'],   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'workshop_tariff_cutoff_id',       	 'DATA' => $userDetailsArray['workshop_tariff_cutoff_id'],   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'membership_number',                   'DATA' => $userDetailsArray['membership_number'],               'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_request',                'DATA' => $userDetailsArray['registration_request'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'operational_area',                    'DATA' => $userDetailsArray['operational_area'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_payment_status',         'DATA' => $userDetailsArray['registration_payment_status'],     'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_mode',                   'DATA' => $userDetailsArray['registration_mode'],               'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'account_status',                      'DATA' => $userDetailsArray['account_status'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'reg_type',                            'DATA' => $userDetailsArray['reg_type'],                        'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'status',                              'DATA' => 'A',                                                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'conf_reg_date',                       'DATA' => $date,                                                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_ip',                          'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_sessionId',                   'DATA' => session_id(),                                         'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_dateTime',                    'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');
	//  echo "<pre>"; print_r($sqlInsertUser);die;

	$lastInsertedUserId               = $mycms->sql_insert($sqlInsertUser, false);

	if ($userDetailsArray['registration_request'] == 'EXHIBITOR') {
		//$registrationId                   = "RCOG19-E-".number_pad($lastInsertedUserId, 4);//$mycms->getRandom(4, 'snum')."-".
		$registrationId                   = $cfg['invoive_number_format'] . "-E" . $mycms->getRandom(4, 'snum') . "-" . number_pad($lastInsertedUserId, 4);
	} else {
		//$registrationId                   = "RCOG19-".number_pad($lastInsertedUserId, 4);//$mycms->getRandom(4, 'snum')."-".
		$registrationId                   = $cfg['invoive_number_format'] . "-" . $mycms->getRandom(4, 'snum') . "-" . number_pad($lastInsertedUserId, 4);
	}
	$uniqueSequence  	              = "#" . $mycms->getRandom(4, 'snum') . number_pad($lastInsertedUserId, 4);

	// UPDATING USER ACCESS KEY AND REGISTRATION ID
	$sqlUpdateUser['QUERY']                    = "UPDATE " . _DB_USER_REGISTRATION_ . "
													 SET `user_unique_sequence` = ?,
														 `user_registration_id` = ?
												   WHERE `id` = ?";

	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'user_unique_sequence',                'DATA' => $uniqueSequence,  'TYP' => 's');
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'user_registration_id',                'DATA' => $registrationId,  'TYP' => 's');
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',                                  'DATA' => $lastInsertedUserId,  'TYP' => 's');

	$mycms->sql_update($sqlUpdateUser, false);

	return $lastInsertedUserId;
}

function insertingExistingUserDetails($existingUserId, $userDetailsArray, $date = '')
{
	global $cfg, $mycms;

	if ($date == '') {
		$date = date('Y-m-d H:i:s');
	}

	$existingDetails = getUserDetails($existingUserId);

	if ($existingDetails['registration_request'] == 'FACULTY') {
		$full_name = $userDetailsArray['user_initial_title']. " " .$userDetailsArray['user_first_name'] . " " . ($userDetailsArray['user_middle_name'] == '' ? '' : ($userDetailsArray['user_middle_name'] . " ")) . $userDetailsArray['user_last_name'];
		$sqlUpdateFaculty = array();
		$sqlUpdateFaculty['QUERY'] 			= " UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
													   SET `participant_title`					= '" . ucwords(strtolower($userDetailsArray['user_initial_title'])) . "', 
														   `participant_first_name`				= '" . ucwords(strtolower($userDetailsArray['user_first_name'])) . "', 
														   `participant_middle_name`			= '" . ucwords(strtolower($userDetailsArray['user_middle_name'])) . "', 
														   `participant_last_name`				= '" . ucwords(strtolower($userDetailsArray['user_last_name'])) . "', 
														   `participant_full_name`				= '" . ucwords(strtolower($full_name)) . "'
														  
													 WHERE `participant_delegate_id` = '" . $existingUserId . "'";
		$mycms->sql_update($sqlUpdateFaculty);
	}
														//  `user_email_id`        		        = ?,

	$sqlInsertUser = array();

	$sqlInsertUser['QUERY']                    = "UPDATE " . _DB_USER_REGISTRATION_ . "
													 SET `refference_delegate_id`		        = ?,
														 `user_type` 	        		        = ?,
														 `user_title`            		        = ?,
														 `user_first_name`       		        = ?,
														 `user_middle_name`     		        = ?,
														 `user_last_name`       		        = ?,
														 `user_full_name` 				        = ?,
														 `user_mobile_isd_code`  		        = ?,
														 `user_mobile_no`				        = ?,
														 `user_address`					        = ?,	
														 `user_country_id`		 		        = ?,
														 `user_state_id`				        = ?,	
														 `user_city`					        = ?,
														 `user_pincode`					        = ?,
														 `user_dob` 					        = ?,
														 `user_gender`					        = ?,
														 `user_designation` 			        = ?,
														 `user_department` 			 	        = ?,
														 `user_institute_name`			        = ?,
														 `user_food_preference` 		        = ?,
														 `user_food_preference_in_details`      = ?,
														 `passport_no`     				        = ?,
														 `user_document`     			        = ?,
														 `isRegistration`       		        = ?,
														 `isCombo`       		        		= ?,
														 `combo_registration`       		        		= ?,
														 `accDateCombo`       		        	= ?,
														 `isConference`         		        = ?,
														 `isWorkshop` 					        = ?,
														 `isAccommodation`				        = ?,
														 `isTour` 						        = ?,
														 `isAbstract`   	    		        = ?,
														 `registration_classification_id`       = ?,
														 `registration_tariff_cutoff_id`        = ?,
														 `membership_number`  		  	        = ?,
														 `registration_request`  		        = ?,
														 `operational_area`        		        = ?,
														 `registration_payment_status` 	        = ?,
														 `registration_mode`    		        = ?,
														 `account_status`				        = ?,
														 `reg_type`						        = ?,
														 `status`						        = ?,
														 `conf_reg_date`        		        = ?,
														 `modified_ip` 					        = ?,
														 `modified_sessionId`			        = ?,
														 `modified_browser` 			        = ?
												   WHERE `id`									= ?";

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'refference_delegate_id',              'DATA' => '0',                                                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_type',                           'DATA' => 'DELEGATE',                                           'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'user_email_id',                       'DATA' => $userDetailsArray['user_email_id'],                   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_title',                          'DATA' => $userDetailsArray['user_initial_title'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_first_name',                     'DATA' => $userDetailsArray['user_first_name'],                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_middle_name',                    'DATA' => $userDetailsArray['user_middle_name'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_last_name',                      'DATA' => $userDetailsArray['user_last_name'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_full_name',                      'DATA' => $userDetailsArray['user_full_name'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_mobile_isd_code',                'DATA' => $userDetailsArray['user_mobile_isd_code'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_mobile_no',                      'DATA' => $userDetailsArray['user_mobile_no'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_address',                        'DATA' => $userDetailsArray['user_address'],                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_country_id',                     'DATA' => $userDetailsArray['user_country'],                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_state_id',                       'DATA' => $userDetailsArray['user_state'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_city',                           'DATA' => $userDetailsArray['user_city'],                       'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_pincode',                        'DATA' => $userDetailsArray['user_postal_code'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_dob',                            'DATA' => $userDetailsArray['user_dob'],                        'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_gender',                         'DATA' => $userDetailsArray['user_gender'],                     'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_designation',                    'DATA' => $userDetailsArray['user_designation'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_department',                     'DATA' => $userDetailsArray['user_depertment'],                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_institute_name',                 'DATA' => $userDetailsArray['user_institution_name'],           'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_food_preference',                'DATA' => $userDetailsArray['user_food_preference'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_food_preference_in_details',     'DATA' => $userDetailsArray['user_other_food_details'],         'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'passport_no',                         'DATA' => $userDetailsArray['passport_no'],                     'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_document',                       'DATA' => $userDetailsArray['user_document'],                   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isRegistration',                      'DATA' => $userDetailsArray['isRegistration'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isCombo',                      		 'DATA' => $userDetailsArray['isCombo'],                  'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'combo_registration',                      		 'DATA' => $userDetailsArray['combo_registration'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accDateCombo',                     	 'DATA' => $userDetailsArray['accDateCombo'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isConference',                        'DATA' => $userDetailsArray['isConference'],                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isWorkshop',                          'DATA' => $userDetailsArray['isWorkshop'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isAccommodation',                     'DATA' => $userDetailsArray['isAccommodation'],                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isTour',                              'DATA' => $userDetailsArray['isTour'],                          'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isAbstract',                          'DATA' => $userDetailsArray['IsAbstract'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_classification_id',      'DATA' => $userDetailsArray['registration_classification_id'],  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_tariff_cutoff_id',       'DATA' => $userDetailsArray['registration_tariff_cutoff_id'],   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'membership_number',                   'DATA' => $userDetailsArray['membership_number'],               'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_request',                'DATA' => $userDetailsArray['registration_request'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'operational_area',                    'DATA' => $userDetailsArray['operational_area'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_payment_status',         'DATA' => $userDetailsArray['registration_payment_status'],     'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_mode',                   'DATA' => $userDetailsArray['registration_mode'],               'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'account_status',                      'DATA' => $userDetailsArray['account_status'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'reg_type',                            'DATA' => $userDetailsArray['reg_type'],                        'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'status',                              'DATA' => 'A',                                                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'conf_reg_date',                       'DATA' => $date,                                                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_ip',                          'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_sessionId',                   'DATA' => session_id(),                                         'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_dateTime',                    'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'id',                    				 'DATA' => $existingUserId,                                      'TYP' => 's');

	$mycms->sql_update($sqlInsertUser, false);



	/*if($userDetailsArray['registration_request']=='EXHIBITOR')
	{
		//$registrationId                   = "RCOG19-E-".number_pad($lastInsertedUserId, 4);//$mycms->getRandom(4, 'snum')."-".
		$registrationId                   = $cfg['invoive_number_format']."-E".$mycms->getRandom(4, 'snum')."-".number_pad($lastInsertedUserId, 4);
	}
	else
	{
		//$registrationId                   = "RCOG19-".number_pad($lastInsertedUserId, 4);//$mycms->getRandom(4, 'snum')."-".
		$registrationId                   = $cfg['invoive_number_format']."-".$mycms->getRandom(4, 'snum')."-".number_pad($lastInsertedUserId, 4);
	}
	
	$uniqueSequence  	              	  = "#".$mycms->getRandom(4, 'snum').number_pad($lastInsertedUserId, 4);
	
	// UPDATING USER ACCESS KEY AND REGISTRATION ID
	$sqlUpdateUser['QUERY']                    = "UPDATE "._DB_USER_REGISTRATION_."
													 SET `user_unique_sequence` = ?,
														 `user_registration_id` = ?
												   WHERE `id` = ?";
										  
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'user_unique_sequence',                'DATA' =>$uniqueSequence,  	'TYP' => 's');
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'user_registration_id',                'DATA' =>$registrationId,  	'TYP' => 's');
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',                                  'DATA' =>$lastInsertedUserId,  'TYP' => 's');
						 
	$mycms->sql_update($sqlUpdateUser, false);*/

	return $existingUserId;
}

function updatingUserDetails($userDetailsArray)
{
	global $cfg, $mycms;
	// UPDATING USER ACCESS KEY AND REGISTRATION ID
	$sqlUpdateUser  = array();
	$sqlUpdateUser['QUERY']    = "UPDATE " . _DB_USER_REGISTRATION_ . "
									SET `registration_classification_id` = ?
								  WHERE `id` = ?";

	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'registration_classification_id',  'DATA' => $userDetailsArray['registration_classification_id'],  'TYP' => 's');
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',                              'DATA' => $userDetailsArray['id'],                              'TYP' => 's');
	$mycms->sql_update($sqlUpdateUser, false);
}

function getUserDetails($delegateId, $onlyActive = false)
{
	global $cfg, $mycms;

	$condition = "";
	if ($onlyActive) {
		$condition = " AND registration_list.status = 'A'";
	} else {
		$condition = " AND registration_list.status IN ('A','C')";
	}

	$sqlDetails	= array();
	$sqlDetails['QUERY'] = "SELECT registration_list.* ,country_list.country_name, state_list.state_name, spParticipant.id AS participantId, spParticipant.participation_type
							  FROM " . _DB_USER_REGISTRATION_ . " registration_list
				   LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country_list
								ON registration_list.user_country_id = country_list . country_id
				   LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state_list
								ON registration_list.user_state_id = state_list.st_id 
				   LEFT OUTER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " spParticipant
								ON spParticipant.participant_delegate_id = registration_list.id 
							 WHERE registration_list.id = ? 
								   " . $condition . "";

	$sqlDetails['PARAM'][]	=	array('FILD' => 'registration_list.id', 	  'DATA' => $delegateId,             'TYP' => 's');

	$resDetails          = $mycms->sql_select($sqlDetails);

	if ($resDetails) {
		return $resDetails[0];
	} else {
		return false;
	}
}

function getUserDetailsByEmail($userEmail, $onlyActive = false)
{
	global $cfg, $mycms;

	$condition = "";
	if ($onlyActive) {
		$condition = " AND status = 'A'";
	} else {
		$condition = " AND status IN ('A','C')";
	}

	$sqlDetails	= array();
	$sqlDetails['QUERY'] = "SELECT id
							  FROM " . _DB_USER_REGISTRATION_ . " 
							 WHERE user_email_id = ? 
								   " . $condition . "";

	$sqlDetails['PARAM'][]	=	array('FILD' => 'user_email_id', 	  'DATA' => trim($userEmail),             'TYP' => 's');

	$resDetails          = $mycms->sql_select($sqlDetails);

	if ($resDetails) {
		return $resDetails[0]['id'];
	} else {
		return false;
	}
}

function getUserDetails1($delegateId, $onlyActive = false)
{
	global $cfg, $mycms;

	$condition = "";
	if ($onlyActive) {
		$condition = " AND registration_list.status = 'A'";
	} else {
		$condition = " AND registration_list.status IN ('A','C')";
	}

	$sqlDetails	= array();
	$sqlDetails['QUERY'] = "SELECT * ,country_list.country_name,state_list.state_name 
							  FROM " . _DB_USER_REGISTRATION_ . " registration_list
				   LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
							    ON registration_list.registration_classification_id = registrationClassification.id
				   LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country_list
								ON registration_list.user_country_id = country_list . country_id
				   LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state_list
								ON registration_list.user_state_id = state_list.st_id 
						
							 WHERE registration_list.id = ? 
								  " . $condition . "";
	$sqlDetails['PARAM'][]	=	array('FILD' => 'registration_list.id', 	  'DATA' => $delegateId,             'TYP' => 's');
	$resDetails          = $mycms->sql_select($sqlDetails);

	if ($resDetails) {
		return $resDetails[0];
	} else {
		return false;
	}
}

function getConferenceContents($delegateId)
{
	global $cfg, $mycms;
	$invoiceList					= array();
	$resultFetchInvoice             = getInvoiceRecords("", $delegateId, "");
	$rowUserDetails  = getUserDetails($delegateId);

	//print_r($resultFetchInvoice);

	foreach ($resultFetchInvoice as $key => $invoiceDetails) {
		$service = '-';
		//echo $invoiceDetails['service_type'];
		if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
			$service = 'REGISTRATION';
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_DETAIL'] = "Conference - " . getRegClsfName(getUserClassificationId($invoiceDetails['delegate_id']));
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_TYPE']   = "CONFERENCE";

			if ($rowUserDetails['isCombo'] == 'Y') {

				$service1 = 'ACCOMMODATION';
				$sqlAccommodation1['QUERY'] = "SELECT accommodation.*,package.package_name 
								   FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
							 INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
									 ON accommodation.`package_id` = package.`id`
								  WHERE accommodation.`status` = 'A' 
									AND accommodation.`user_id` = '" . $delegateId . "'";
				$resAccommodation1 = $mycms->sql_select($sqlAccommodation1);
				$rowAccommodation1	 = $resAccommodation1[0];
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$invoiceDetails['refference_id']]['REG_DETAIL'] = 'ACCOMMODATION';
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$invoiceDetails['refference_id']]['ROW_DETAIL'] = $rowAccommodation1;


				$service2 = 'WORKSHOP';
				$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
				$sqlWorkshopclsf = array();

				$sqlWorkshopclsf['QUERY'] = "SELECT * FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
											WHERE `status` = ? 
											AND `id` = ?";

				$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status',  		'DATA' => 'A', 									'TYP' => 's');
				$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id',  			'DATA' => $workShopDetails['workshop_id'], 		'TYP' => 's');

				$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
				$rowWorkshopclsf	 = $resWorkshopclsf[0];

				$invoiceList[$invoiceDetails['delegate_id']][$service2][$invoiceDetails['refference_id']]['REG_DETAIL'] = getWorkshopName($workShopDetails['workshop_id']);
				$invoiceList[$invoiceDetails['delegate_id']][$service2][$invoiceDetails['refference_id']]['ROW_DETAIL'] = $rowWorkshopclsf;
			}
		} elseif ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
			$service = 'REGISTRATION';
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_DETAIL'] = "Registration - " . getRegClsfName(getUserClassificationId($invoiceDetails['delegate_id']));
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_TYPE']   = "RESIDENTIAL";

			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['COMBO-ACCOMODATION'] 	= accmodation_details($invoiceDetails['delegate_id']);
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['COMBO-DINNER'] 		= getDinnerDetailsOfDelegate($invoiceDetails['delegate_id']);

			/*
			$AllWorkShopDetails = getWorkshopDetailsOfDelegate($delegateId);
			foreach($AllWorkShopDetails as $key=>$workShopDetails)
			{
				$sqlWorkshopclsf 		=	array();
				$sqlWorkshopclsf['QUERY'] = "SELECT * 
											   FROM "._DB_WORKSHOP_CLASSIFICATION_." 
											  WHERE `status` = ? 
											    AND `id` = ?";
												
				$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status',  		'DATA' =>'A', 									'TYP' => 's');
				$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id',  			'DATA' =>$workShopDetails['workshop_id'], 		'TYP' => 's');	
					
				$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
				$rowWorkshopclsf = $resWorkshopclsf[0];
				
				$service1 = $rowWorkshopclsf['type'];
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$workShopDetails['id']]['REG_DETAIL'] = getWorkshopName($workShopDetails['workshop_id']);
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$workShopDetails['id']]['ROW_DETAIL'] = $rowWorkshopclsf;
				
				$slipId = $invoiceDetails['slip_id'];
		
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$workShopDetails['id']]['INVOICE'] = $invoiceDetails;
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$workShopDetails['id']]['SLIP_DETAILS'] = slipDetails($slipId);
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$workShopDetails['id']]['SLIP_PAYMENT'] = paymentDetails($slipId);
				$invoiceList[$invoiceDetails['delegate_id']][$service1][$workShopDetails['id']]['USER']	= getUserDetails($invoiceDetails['delegate_id']);
			}
			*/
		} elseif ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$service = 'WORKSHOP';
			$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
			$sqlWorkshopclsf = array();
			$sqlWorkshopclsf['QUERY'] = "SELECT * FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
											WHERE `status` = ? 
											AND `id` = ?";

			$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status',  		'DATA' => 'A', 									'TYP' => 's');
			$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id',  			'DATA' => $workShopDetails['workshop_id'], 		'TYP' => 's');

			$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
			$rowWorkshopclsf	 = $resWorkshopclsf[0];

			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_DETAIL'] = getWorkshopName($workShopDetails['workshop_id']);
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['ROW_DETAIL'] = $rowWorkshopclsf;
		} elseif ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails = getUserDetails($invoiceDetails['refference_id']);

			$service = 'ACCOMPANY';
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_DETAIL'] = 'ACCOMPANY';
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['ROW_DETAIL'] = $accompanyDetails;
		} elseif ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
			$service = 'ACCOMMODATION';
			$sqlAccommodation['QUERY'] = "SELECT accommodation.*,package.package_name 
								   FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
							 INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
									 ON accommodation.`package_id` = package.`id`
								  WHERE accommodation.`status` = 'A' 
									AND accommodation.`id` = '" . $invoiceDetails['refference_id'] . "'";
			$resAccommodation = $mycms->sql_select($sqlAccommodation);
			$rowAccommodation	 = $resAccommodation[0];
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_DETAIL'] = 'ACCOMMODATION';
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['ROW_DETAIL'] = $rowAccommodation;
		} elseif ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$BanquetDetails = getDinnerDetails($invoiceDetails['refference_id']);

			$service = 'DINNER';
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['REG_DETAIL'] = $BanquetDetails;
			$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['ROW_DETAIL'] = 'DINNER';
			if ($invoiceDetails['status'] == 'A') {
				$invoiceList[$invoiceDetails['delegate_id']]['SUMMARY']['DINNER_REGISTRATION'][] = $BanquetDetails['delegate_id'];
			}
		}
		$slipId = $invoiceDetails['slip_id'];

		$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['INVOICE'] = $invoiceDetails;
		if ($rowUserDetails['isCombo'] == 'Y') {
			//$invoiceList[$invoiceDetails['delegate_id']][$service1][$invoiceDetails['refference_id']]['INVOICE'] = $invoiceDetails;
			//$invoiceList[$invoiceDetails['delegate_id']][$service2][$invoiceDetails['refference_id']]['INVOICE'] = $invoiceDetails;
		}

		$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['SLIP_DETAILS'] = slipDetails($slipId);
		$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['SLIP_PAYMENT'] = paymentDetails($slipId);
		$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['USER']	= getUserDetails($invoiceDetails['delegate_id']);
		$invoiceList[$invoiceDetails['delegate_id']][$service][$invoiceDetails['refference_id']]['USER']['REG_TYPE'] = getRegClsfName(getUserClassificationId($invoiceDetails['delegate_id']));

		$invoiceList[$invoiceDetails['delegate_id']]['PAYMENT_SLIPS'][$slipId] = slipDetails($slipId);

		if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION" || $invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {

			$mainSlipId = $invoiceDetails['slip_id'];
			$addOnInvoiceDetail = invoiceDetailsActiveOfSlip($mainSlipId);

			$mainSlipDetails = slipDetails($slipId);
			$addonInvoiceList = array();

			foreach ($addOnInvoiceDetail as $ky => $addOnInvoiceDetails) {
				if ($addOnInvoiceDetails['delegate_id'] != $delegateId && $mainSlipDetails['delegate_id'] == $delegateId) {

					if ($addOnInvoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
						$service = 'REGISTRATION';
						$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['REG_DETAIL'] = "Conference - " . getRegClsfName(getUserClassificationId($addOnInvoiceDetails['delegate_id']));
						$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['REG_TYPE']   = "CONFERENCE";
					} elseif ($addOnInvoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
						$service = 'REGISTRATION';
						$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['REG_DETAIL'] = "AI Registration - " . getRegClsfName(getUserClassificationId($addOnInvoiceDetails['delegate_id']));
						$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['REG_TYPE']   = "RESIDENTIAL";
					} elseif ($addOnInvoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
						$workShopDetails = getWorkshopDetails($addOnInvoiceDetails['refference_id']);
						$sqlWorkshopclsf['QUERY'] = "SELECT * FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " WHERE status = 'A' AND `id` = '" . $workShopDetails['workshop_id'] . "'";
						$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
						$rowWorkshopclsf	 = $resWorkshopclsf[0];
						$service = $rowWorkshopclsf['type'];
						$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['REG_DETAIL'] = getWorkshopName($workShopDetails['workshop_id']);
						$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['ROW_DETAIL'] = $rowWorkshopclsf;
					}
					$slipId = $addOnInvoiceDetails['slip_id'];
					$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['INVOICE'] 		= $addOnInvoiceDetails;
					$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['SLIP_DETAILS'] = slipDetails($slipId);
					$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['SLIP_PAYMENT'] = paymentDetails($slipId);
					$addonInvoiceList[$addOnInvoiceDetails['delegate_id']][$service][$addOnInvoiceDetails['refference_id']]['USER']			= getUserDetails($addOnInvoiceDetails['delegate_id']);

					$addonInvoiceList[$addOnInvoiceDetails['delegate_id']]['PAYMENT_SLIPS'][$slipId] = slipDetails($slipId);
				}
			}
			$invoiceList[$delegateId]['ADDON'] = $addonInvoiceList;
		}
	}
	return $invoiceList;
}

function getECMember($email, $mobile)
{
	global $cfg, $mycms;

	$sqlDetails	= array();
	$sqlDetails['QUERY'] = "SELECT * 
							  FROM " . _DB_EC_MEMBERS_ . "						
							 WHERE email = '" . $email . "'
							 	OR mobile1 = '" . $mobile . "'
							 	OR mobile2 = '" . $mobile . "'";
	$resDetails          = $mycms->sql_select($sqlDetails);

	if ($resDetails) {
		return $resDetails[0];
	} else {
		return false;
	}
}

function getClassificationComboTitle($classification_id = "")
{
	global $cfg, $mycms;



	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT `classification_title`
								 FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
								WHERE status = ?  AND id=?";
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'id',  'DATA' => $classification_id,  'TYP' => 's');
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);



	return $resRegClasf[0]['classification_title'];
}

function getAllClassificationCombo($classification_id = "")
{
	global $cfg, $mycms;



	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT *
								 FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
								WHERE status = ?  AND id=?";
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'id',  'DATA' => $classification_id,  'TYP' => 's');
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);



	return $resRegClasf[0];
}
