<?php

/***********************************************************************************/
/*                         OFFLINE CONFERENCE REGISTRATION MASSAGING               */
/***********************************************************************************/
//REGISTRATION ACKNOWLOGYMENT

function offline_welcome_message($delegateId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$rowFetchUserDetails   = getUserDetails($delegateId);
	$sms = "Welcome to " . $cfg['EMAIL_CONF_NAME'] . " which is to be held from " . $cfg['EMAIL_CONF_HELD_FROM'] . " at " . $cfg['EMAIL_CONF_VENUE'] . ". We have received your details for registration. Kindly proceed to complete the payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'] . ".";
	if ($operation == 'SEND') {
		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms);
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['SMS_BODY'] = $sms;
		return $array;
	} else {
		return false;
	}
}

function online_welcome_message($delegateId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$rowFetchUserDetails   = getUserDetails($delegateId);

	$sms = "Welcome to " . $cfg['EMAIL_CONF_NAME'] . " which is to be held from " . $cfg['EMAIL_CONF_HELD_FROM'] . " at " . $cfg['EMAIL_CONF_VENUE'] . ". We have received your details for registration. Kindly proceed to complete the payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'] . ".";
	if ($operation == 'SEND') {
		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms);
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}

/***********************************************************SENIOR CITIZEN MAIL*****************************************************/
function online_senior_citizen_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$loginUrl     		   		= _BASE_URL_;
	$rowFetchUserDetails   		= getUserDetails($delegateId);
	$invoiceOrderSummary   		= generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	   		= getPaymentDetails($paymentId);
	$user_password     	   		= $mycms->decoded($rowFetchUserDetails['user_password']);
	$delagateCatagory      		= getUserClassificationId($delegateId);
	$sqlaccommodation['QUERY'] 	= "SELECT * 
										 FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
									    WHERE status = 'A' 
										  AND user_id = '" . $delegateId . "' ";

	$resaccom			   		= $mycms->sql_select($sqlaccommodation);
	$rowaccomm             		= $resaccom[0];

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';
	$message .= '      Welcome to the ' . $cfg['EMAIL_WELCOME_TO'] . ' to be held between ' . $cfg['EMAIL_CONF_HELD_FROM'] . ' at ' . $cfg['EMAIL_CONF_VENUE'] . '';
	$message .= '    <br /><br />';

	$message .= '    You are <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';
	$message .= '    <br /><br />';
	$message .= ' 	 <u>Please note the following:-</u>';
	$message .= ' 	 <br />';
	$message .= ' 	 <table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border="0">';
	$message .= '			<tr><td><strong>Registered E-mail Id:</strong> </td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
	$message .= '			<tr><td><strong>Registered Phone Number:</strong> </td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
	$message .= '			<tr><td><strong>Unique Sequence:</strong> </td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
	$message .= '			<tr><td><strong>Registration Id:</strong> </td><td>' . $rowFetchUserDetails['user_registration_id'] . '</td></tr>';
	$message .= ' 	 </table>';
	$message .= ' 	 <br /><br />';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '		<u><strong>TRANSACTION DETAILS</strong></u>';
		$message .= '		<br /><br />';
		$message .=	'		Payment has been done through online process.';
		$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '		<tr><td>Receiver</td><td>' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
		$message .= '		<tr><td>Transaction Date</td><td>' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
		$message .=	'		<tr><td>Amount</td><td>  ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
		$message .=	'		<tr><td>Mode of Payment</td><td> ' . $rowFetchPayment['payment_mode'] . '</td></tr>';
		$message .=	'		<tr><td>Transaction ID</td><td>' . $rowFetchPayment['atom_atom_transaction_id'] . '</td></tr>';
		$message .= '		</table>';
		$message .= '		<br /><br />';
	}

	$message .= '	 <u><b>REGISTRATION DETAILS</b></u>';
	$message .= ' 	<br /><br />';
	$message .= 	$invoiceOrderSummary;
	$message .= '	<br /><br />';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
											<td width="15%"><b>Invoice No.</b></td>
											<td width="70%"><b>Invoice for</b></td>
											<td width="20%" align="center"><b>Download</b></td>
									  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '			<br /><br />';
	}




	$message .= '			<br/><br /><a href="' . $loginUrl . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong> or call us at <strong>' . $cfg['EMAIL_CONF_CONTACT_US'] . '</strong>';
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';
	$subject  = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";

	$regsms      = "Welcome to " . $cfg['EMAIL_CONF_NAME'] . ". You are now registered for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence : " . $rowFetchUserDetails['user_unique_sequence'] . " Registration Id : " . $rowFetchUserDetails['user_registration_id'] . ", Registered E-mail Id : " . $rowFetchUserDetails['user_email_id'] . ". For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'] . ".";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		if ($rowFetchPayment['amount'] != 0.00) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}
		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms, 'Informative');

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

/***********************************************************CANCEL INVOICE MAIL TO ADMIN*********************************************/
function online_service_cancellation_message($invoiceId, $user_comment,  $operation = 'SEND')
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$loginUrl     		   = _BASE_URL_;

	$getDetailsOfInvoice   = getInvoiceDetails($invoiceId);
	$rowFetchUserDetails   = getUserDetails($getDetailsOfInvoice['delegate_id']);

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message = "";
	$message .= "Dear Admin,<br />";
	$message .= "An user has left the following message.<br />";
	$message .= "<table cellpadding='0' cellspacing='0' border='0'>";
	$message .= "	<tr>";
	$message .= "		<td>Name</td><td>:</td><td>" . $rowFetchUserDetails['user_full_name'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td>Unique Sequence No.</td><td>:</td><td>" . $rowFetchUserDetails['user_unique_sequence'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td>Invoice No.</td><td>:</td><td>" . $getDetailsOfInvoice['invoice_number'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td>Invoice For</td><td>:</td><td>" . $getDetailsOfInvoice['service_type'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td valign='top'>Reason for cancellation</td><td valign='top'>:</td><td>" . nl2br($user_comment) . "</td>";
	$message .= "	</tr>";
	$message .= "</table><br />";
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Service cancellation Request";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $cfg['EMAIL_CONF_EMAIL_US'], $subject, $message, '', 'payal.d@encoders.co.in');
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		return $array;
	} else {
		return false;
	}
}


/***********************************************************Workshop Service Change MAIL TO ADMIN*********************************************/
function workshop_change_request_mail($invoiceId, $user_comment,  $operation = 'SEND')
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	include_once('function.workshop.php');


	$loginUrl     		   = _BASE_URL_;
	$getDetailsOfInvoice   = getInvoiceDetails($invoiceId);
	$rowFetchUserDetails   = getUserDetails($getDetailsOfInvoice['delegate_id']);
	$rowWorkshopDetailsOfDelegate = getWorkshopDetailsOfDelegate($getDetailsOfInvoice['delegate_id']);

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();


	$message = "";
	$message .= "Dear Admin,<br />";
	$message .= "An user has left the following message.<br />";
	$message .= "<table cellpadding='0' cellspacing='0'>";
	$message .= "	<tr>";
	$message .= "		<td>Name</td><td>:</td><td>" . $rowFetchUserDetails['user_full_name'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td>Unique Sequence No.</td><td>:</td><td>" . $rowFetchUserDetails['user_unique_sequence'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td>Invoice No.</td><td>:</td><td>" . $getDetailsOfInvoice['invoice_number'] . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td>Workshop Name</td><td>:</td><td>" . getWorkshopName($rowWorkshopDetailsOfDelegate[0]['workshop_id']) . "</td>";
	$message .= "	</tr>";
	$message .= "	<tr>";
	$message .= "		<td valign='top'>Message</td><td valign='top'>:</td><td>" . nl2br($user_comment) . "</td>";
	$message .= "	</tr>";
	$message .= "</table><br />";
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Workshop Change Request";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $cfg['EMAIL_CONF_EMAIL_US'], $subject, $message, '', 'payal.d@encoders.co.in');
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		return $array;
	} else {
		return false;
	}
}

/*****************************************************ACCOMMODATION DETAILS MAIL *********************************************/
function accommodation_booking_slip_message($delegateId, $operation = 'SEND')
{
	global $mycms, $cfg;
	return false;
	$loginUrl     		   = "" . $cfg['IAACON_BASE_URL'] . "gotoportal.php?goto=" . base64_encode('login.php') . "";
	$rowFetchUserDetails   = getUserDetails($delegateId);

	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);
	$delagateCatagory      = getUserClassificationId($delegateId);

	$sqlUser['QUERY'] = "		  SELECT delegate.id AS userId,
								 delegate.user_full_name, 
								 delegate.user_email_id, 
								 delegate.user_address,
								 delegate.user_mobile_isd_code, 
								 delegate.user_mobile_no, 
								 delegate.user_unique_sequence,
								 delegate.user_registration_id,
								 accom.checkin_date AS CheckInDate,
								 accom.checkout_date AS CheckOutDate,
								 accom.accompany_name AS accompany_name,
								 date.check_in_date AS CheckInDate ,
								 country.country_name AS country,
								 country.country_id, 
								 state.state_name AS state,
								 ht.hotel_name,
								 pack.package_name As RoomType
						 
							FROM " . _DB_REQUEST_ACCOMMODATION_ . " accom
							
				 LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " delegate
							  ON accom.user_id = delegate.id
							  
					  INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " date
							  ON accom.checkin_date = date.check_in_date
				
				 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
							  ON delegate.user_country_id = country.country_id
							  
				 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
							  ON delegate.user_state_id = state.st_id
							  
				      INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " pack
							  ON pack.`id` = accom.`package_id`
							  
					  INNER JOIN " . _DB_MASTER_HOTEL_ . " ht
							  ON ht.`id` = accom.`hotel_id`
								
						   WHERE accom.status= 'A'  AND delegate.status= 'A'
							 AND delegate.id ='" . $delegateId . "'";

	$resultUser	= $mycms->sql_select($sqlUser);
	$rowFetchUser = $resultUser[0];

	// COMPOSING EMAIL
	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . '<br/>';
	$message .= '    <u> The following is your accommodation booking details </u> :-';

	$message .= '<br />';
	$message .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">';
	$message .= '<div style="color:#DA251C; font-weight:bold; padding:8px; margin-top:5px; font-size:16px; text-align:left;">Accommodation Details</div>';
	$message .= '<a href="' . $cfg['BASE_URL'] . 'webmaster/section_accommodation/downloadAccommodationReport.php?user_id=' . $delegateId . '" title="Download Coupon"><img src="' . $cfg['IAACON_BASE_URL'] . 'images/mailerImage/download.png" alt="download"/></a>';
	$message .= '<table width="100%" border="1" cellspacing="1" cellpadding="5" style="font-size:13px; border:thin solid #000;">';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Name:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">' . $rowFetchUser['user_full_name'] . '</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Registration Details:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">' . $rowFetchUser['user_unique_sequence'] . '<br />' . $rowFetchUser['user_registration_id'] . '</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">E-mail:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">';
	if ($rowFetchUser['user_email_id'] != '') {
		$message .=	               $rowFetchUser['user_email_id'];
	} else {
		$message .=	           'NA';
	}


	$message .=			'</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Contact No.:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">';
	if ($rowFetchUser['user_mobile_no'] != '') {
		$message .=	               $rowFetchUser['user_mobile_isd_code'] . '-' . $rowFetchUser['user_mobile_no'];
	} else {
		$message .=	           'NA';
	}
	$message .=			'</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Address:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">';
	if ($rowFetchUser['user_address'] != '') {
		$message .=	               $rowFetchUser['user_address'];
	} else {
		$message .=	           'NA';
	}
	$message .=			'</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td style="border-bottom:dotted thin #ccc;">State:</td>';
	$message .=          '<td width="25%" style="border-bottom:dotted thin #ccc;">';
	if ($rowFetchUser['state'] != '') {
		$message .=	               $rowFetchUser['state'];
	} else {
		$message .=	           'NA';
	}


	$message .=			'</td>';
	$message .=          '<td width="25%" style="border-bottom:dotted thin #ccc;">Country:</td>';
	$message .=          '<td width="25%" style="border-bottom:dotted thin #ccc;">';
	if ($rowFetchUser['country'] != '') {
		$message .=	               $rowFetchUser['country'];
	} else {
		$message .=	           'NA';
	}


	$message .=			'</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Hotel Name:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">' . $rowFetchUser['hotel_name'] . '</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Room Name:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">' . $rowFetchUser['RoomType'] . '</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Accompany Name:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">';
	if ($rowFetchUser['accompany_name'] != '') {
		$message .=	               $rowFetchUser['accompany_name'];
	} else {
		$message .=	           'NA';
	}
	$message .=			'</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%" style="border-bottom:dotted thin #ccc;">Check-In Date:</td>';
	$message .=          '<td colspan="3" style="border-bottom:dotted thin #ccc;">' . $rowFetchUser['CheckInDate'] . '</td>';
	$message .=     '</tr>';
	$message .=    '<tr>';
	$message .=         '<td width="25%">Check-Out Date:</td>';
	$message .=          '<td colspan="3">' . $rowFetchUser['CheckOutDate'] . '</td>';
	$message .=     '</tr>';
	$message .= '</table>';
	$message .= '</div>';

	$message .= '			<br /><br/>';
	$message .=   	accommodation_note();
	$message .= '			For more information please write at <u><strong><u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u></strong></u>';
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Your Accommodation Details at " . $cfg['EMAIL_CONF_NAME'] . ".";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}


/*****************************************************PARTICIPANT SCHEDULE DETAILS MAIL *********************************************/
// function national_faculty_residential_schedule_details_message($participantId, $operation = 'SEND')
// {
// 	global $mycms, $cfg;
// 	$sqlListing['QUERY']		 = "SELECT * 
// 										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
// 										 WHERE `status` = 'A' 
// 										   AND `id` = '" . $participantId . "'";
// 	$resultsListing	 = $mycms->sql_select($sqlListing);
// 	$rowParticipant	 = $resultsListing[0];

// 	// COMPOSING EMAIL
// 	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
// 	$message .= get_invitation_email_header();
// 	$message .= '<tr>';
// 	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
// 	$message .= get_invitation_email_side_1();
// 	$message .= '	 </td>';
// 	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
// 	$message .= '</tr>';
// 	$message .= '<tr>';
// 	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
// 	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
// 	$message .= '	 <br /><br />';
// 	$message .= '    Warm Greetings from ISNEZCON 2024.';
// 	$message .= '    <br /><br />';
// 	$message .= '    We feel privileged and honoured to invite you as a Faculty at ISNEZCON 2024, which is to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
// 	$message .= '    <br /><br />';
// 	$message .= '    We would like to have your participation in the Scientific Session as follows:';
// 	$message .= '    <br /><br />';

// 	$scheduleInArray = programScheduleStructure($participantId);

// 	$message .= implode('<br />', $scheduleInArray['DATA']);
// 	//$message .= '    <br />';

// 	if ($scheduleInArray['HASCHAIR'] == 'Y') {
// 		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
// 		//$message .= '    <br />';
// 	}

// 	$message .= '    <ul>';
// 	$message .= '    	<li>As per NEOCON norms, all Faculty need to make their own arrangement for Travel</li>';
// 	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation if not sent already</li>';
// 	$message .= '    	<li>Please find attached the presentation guidelines</li>';
// 	$message .= '    	<li>For any query, you may call on 7595949353 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com</li>';
// 	$message .= '    </ul>';

// 	//$message .= '    <br /><br/>';
// 	$message .= '	 We truly look forward to welcoming you to Kolkata.';
// 	$message .= get_invitation_email_footer();

// 	$message .= '		<br /><br /><br /><br />';
// 	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
// 	$message .= '		<br /><br /><br /><br />';
// 	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
// 	$message .= '		<br /><br /><br /><br />';
// 	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
// 	$message .= '		<br /><br /><br /><br />';
// 	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

// 	$message .= ' 	</td>';
// 	$message .= ' </tr>';
// 	$message .= '<tr>';
// 	$message .= '	 <td align="center" valign="bottom">';
// 	$message .= get_invitation_email_side_2();
// 	$message .= '	 </td>';
// 	$message .= '</tr>';
// 	$message .= '</table>';

// 	$subject  = "Session Involvement, NEOCON 2022";

// 	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

// 	if ($operation == 'SEND') {
// 		return true;
// 	} else if ($operation == 'RETURN_TEXT') {
// 		$array = array();
// 		$array['MAIL_SUBJECT'] = $subject;
// 		$array['MAIL_BODY']   = trim($message);
// 		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
// 		$array['SMS_BODY'][0] = $confsms;
// 		return $array;
// 	} else {
// 		return false;
// 	}
// }
function national_faculty_residential_schedule_details_message($participantId, $operation = 'SEND')
{?>

<?php
	global $mycms, $cfg;

	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	

	// COMPOSING EMAIL
	
	$message  = '<table class="test" border="0" align="left"  cellpadding="2" cellspacing="0" width="900px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	
	$message .= get_invitation_email_side_top();
	

	$message .= '<tr>';

	$message .= '<td>';

	// $message .= '	 Dear '.$rowParticipant['participant_full_name'].'';
	$message .= '<p style=\'padding-left: 15px !important;\'><span style="font-family: Cambria, serif;"><span lang="en-US"><strong>To</strong></span></span> <br><span style="font-family: Cambria, serif;"><span lang="en-US"><strong>[NAME]</strong></span></span></p>
		
		<p style="padding-left: 15px !important;">Respected Faculty,</p>
		<p style="padding-left: 15px !important;">It is our distinct pleasure to reveal the upcoming HOPECON 2024, the third edition of the esteemed&nbsp;<strong>
		National Medi-Educare Conference </strong>organized by<strong> the Kolkata Nurture Foundation &amp; UNIEDUHEALTH</strong>,
		 in the dynamic <strong>city of joy Kolkata</strong> from 28th - 30th June, 2024 at ITC Royal Bengal.
		  The Theme of the conference is "Comprehensive Healthcare - The Complete Care".<br><strong>We feel privileged to invite 
		  you as our esteemed faculty for HOPECON 2024, Kolkata.
		</strong><br><br><strong>Please find the academic involvement below:</strong></p>';
	

	$scheduleInArray = programScheduleStructure($participantId);

	//echo '<pre>'; print_r($schprogramScheduleStructureeduleInArray);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '<p>In need of any assistance or enquiry please feel free to contact<br><strong> </strong> <br><strong>Conference Secretariat</strong> &ndash; 7596071517</p>
		<p>In this grand academic event, growing beyond the horizon of last year, nearly around&nbsp;5000 decorated delegates hailing from all over the country making it a cherishable&nbsp;academic experience.</p>
		<p>Here, Clinical Medicine with all its branches from Cardiology to Oncology, from&nbsp;Nephrology to Respiratory Medicine, from Pharmacology to Psychiatry will be&nbsp;presented in a manner that enriches every physician, junior or senior by ladening them&nbsp;with knowledge from conventional to recent most advances in a singular platform.</p>
		<p>The initiative will encapsulate a package of clinical knowledge from bench-side to&nbsp;bedside and from books to the real world. In this platform, dialogues instead of&nbsp;monologues and practical real-world approach over theoretical oration will be given&nbsp;preference. This year, we are thrilled to introduce new disciplines such as <strong>Artificial&nbsp;Intelligence, Biomedical Research and Clinical Trials, Public Health &amp; many more&nbsp;in addition to our existing 25 fraternities.</strong></p>
		<p>The conference will have intriguing lectures, interactive sessions, panel discussions and exchange of knowledge between the experts from all over the world and the rising stars of the Fraternity. We will be leaving no stone unturned in making this an academic&nbsp;extravaganza, giving it the due pedestal it deserves.</p>
<p><strong>Your prompt reply on acceptance by returning email by 5 days would be much&nbsp;appreciated.</strong><br></p>';


	$message .= get_invitation_email_footer();
	$message.='<table width="100%" border="0">
	<tr>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
			<a href="'._BASE_URL_.'images/DERMATOLOGY.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="DERMATOLOGY.pdf" /><br>
				<b>DERMATOLOGY.pdf</b>
			</div>
		</a>
		</td>
		<td align="left"  valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/DIGITAL-HEALTH.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="DIGITAL-HEALTH.pdf" /><br>
				<b>DIGITAL-HEALTH.pdf</b>
			</div></a>
		</td>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/RHEUMATOLOGY.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="RHEUMATOLOGY.pdf" /><br>
				<b>RHEUMATOLOGY.pdf</b></a>
			</div>
		</td>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/GERIATRICS.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="GERIATRICS.pdf" /><br>
				<b>GERIATRICS.pdf</b></a>
			</div>
		</td>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/FMT.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="FMT.pdf" /><br>
				<b>FMT.pdf</b></a>
			</div>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
			<a href="'._BASE_URL_.'images/MEDICAL-EDUCATION.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="MEDICAL EDUCATION.pdf" /><br>
				<b>MEDICAL EDUCATION.pdf</b>
			</div>
		</a>
		</td>
		<td align="left"  valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/NEUROLOGY.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="NEUROLOGY.pdf" /><br>
				<b>NEUROLOGY.pdf</b>
			</div></a>
		</td>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/PATHOLOGY-LAB-MEDICINE.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="PATHOLOGY-LAB-MEDICINE.pdf" /><br>
				<b>PATHOLOGY & LAB MEDICINE.pdf</b></a>
			</div>
		</td>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/RESPIRATORY.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="RESPIRATORY.pdf" /><br>
				<b>RESPIRATORY.pdf</b></a>
			</div>
		</td>
		<td align="left" valign="top" style="border-style:none;text-align: center;">
		<a href="'._BASE_URL_.'images/INTERNAL-MEDICINE.pdf">
			<div style="float:left; margin:3px;">
				<img style="height: 60px;" src="' ._BASE_URL_.'images/pdfIcon.png"  alt="INTERNAL-MEDICINE.pdf" /><br>
				<b>INTERNAL MEDICINE.pdf</b></a>
			</div>
		</td>
	</tr></table>';

// $message.='<br><a href="'._BASE_URL_.'images/DERMATOLOGY.pdf">DERMATOLOGY.pdf</a><br>
// <a href="'._BASE_URL_.'images/RHEUMATOLOGY.pdf"><img src="'._BASE_URL_.'images/pdfIcon.png" width="100px">RHEUMATOLOGY.pdf</a><br>
// <a href="'._BASE_URL_.'images/DIGITAL-HEALTH.pdf">DIGITAL HEALTH.pdf</a><br>
// <a href="'._BASE_URL_.'images/GERIATRICS.pdf">GERIATRICS.pdf</a><br>
// <a href="'._BASE_URL_.'images/MEDICAL-EDUCATION.pdf">MEDICAL EDUCATION.pdf</a><br>
// <a href="'._BASE_URL_.'images/NEUROLOGY.pdf">NEUROLOGY.pdf</a><br>
// <a href="'._BASE_URL_.'images/PATHOLOGY-LAB-MEDICINE.pdf">PATHOLOGY & LAB MEDICINE.pdf</a><br>
// <a href="'._BASE_URL_.'images/RESPIRATORY.pdf">RESPIRATORY.pdf</a><br>
// <a href="'._BASE_URL_.'images/FMT.pdf">FMT.pdf</a>';

	$message .= ' 	</td></tr>';

	$sql_footer 	=	array();
	$sql_footer['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql_footer);
	$row    		 = $result[0];
	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['sc_footer_image'];
	
	$message .= ' <tr>
	<td align="left" valign="top" colspan=7><img src="'.$footer_image.'" alt="Footer" style="width:100%"/></td>
</tr>';

	$message .= '</table><br>
	';

	$subject  = "Faculty Invitation _ HOPECON 2024";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your academic involvements in the Scientific Sessions of ISAR 2024 Bhubaneswar has been sent to " . $rowParticipant['participant_email_id'] . "
Kindly check spam if you do not receive the mail in your inbox. Please confirm your participation by reverting the mail within 5 days. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function national_faculty_registered_schedule_details_message($participantId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
	$message .= '	 <br /><br />';
	$message .= '    Warm Greetings from NEOCON 2022.';
	$message .= '    <br /><br />';
	$message .= '    We feel privileged and honoured to invite you as a Faculty at NEOCON 2022, which is to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
	$message .= '    <br /><br />';
	$message .= '    We would like to have your participation in the Scientific Session as follows:';
	$message .= '    <br /><br />';

	$scheduleInArray = programScheduleStructure($participantId);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '    <ul>';
	$message .= '    	<li>As per NEOCON norms, all Faculty need to make their own arrangements for Travel & Accommodation </li>';
	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation if not sent already</li>';
	$message .= '    	<li>Please find attached the presentation guidelines</li>';
	$message .= '    	<li>For any query, you may call on 7595949353 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com</li>';
	$message .= '    </ul>';

	//$message .= '    <br /><br/>';
	$message .= '	 We truly look forward to welcoming you to Kolkata.';
	$message .= get_invitation_email_footer();

	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';
	$message .= '</table>';

	$subject  = "Session Involvement, NEOCON 2022";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function local_faculty_registered_schedule_details_message($participantId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
	$message .= '	 <br /><br />';
	$message .= '    Warm Greetings from NEOCON 2022.';
	$message .= '    <br /><br />';
	$message .= '    We feel privileged and honoured to invite you as a Faculty at NEOCON 2022, which is to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
	$message .= '    <br /><br />';
	$message .= '    We would like to have your participation in the Scientific Session as follows:';
	$message .= '    <br /><br />';

	$scheduleInArray = programScheduleStructure($participantId);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '    <ul>';
	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation if not sent already</li>';
	$message .= '    	<li>Please find attached the presentation guidelines</li>';
	$message .= '    	<li>For any query, you may call on 7595949353 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com</li>';
	$message .= '    </ul>';

	//$message .= '    <br /><br/>';
	$message .= '	 We truly look forward to welcoming you to the conference.';
	$message .= get_invitation_email_footer();

	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';
	$message .= '</table>';

	$subject  = "Session Involvement, NEOCON 2022";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function national_faculty_complementary_schedule_details_message($participantId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
	$message .= '	 <br /><br />';
	$message .= '    Warm Greetings from NEOCON 2022.';
	$message .= '    <br /><br />';
	$message .= '    We feel privileged and honoured to invite you as a Faculty at NEOCON 2022, which is to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
	$message .= '    <br /><br />';
	$message .= '    We would like to have your participation in the Scientific Session as follows:';
	$message .= '    <br /><br />';

	$scheduleInArray = programScheduleStructure($participantId);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '    <ul>';
	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation if not sent already</li>';
	$message .= '    	<li>Please find attached the presentation guidelines</li>';
	$message .= '    	<li>For any query, you may call on 7595949353 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com</li>';
	$message .= '    </ul>';

	//$message .= '    <br /><br/>';
	$message .= '	 We truly look forward to welcoming you to Kolkata.';
	$message .= get_invitation_email_footer();

	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';
	$message .= '</table>';

	$subject  = "Session Involvement, NEOCON 2022";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function national_faculty_unregistered_schedule_details_message($participantId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
	$message .= '	 <br /><br />';
	$message .= '    Warm Greetings from NEOCON 2022.';
	$message .= '    <br /><br />';
	$message .= '    We feel privileged and honoured to invite you as a Faculty at NEOCON 2022, which is to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
	$message .= '    <br /><br />';
	$message .= '    We would like to have your participation in the Scientific Session as follows:';
	$message .= '    <br /><br />';

	$scheduleInArray = programScheduleStructure($participantId);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '    <ul>';
	$message .= '    	<li>As per NEOCON norms, all Faculty need to Register for the conference & make their own arrangements for Travel & Accommodation</li>';
	$message .= '    	<li>It is mandatory for all Faculty to register by August 15</li>';
	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation if not sent already</li>';
	$message .= '    	<li>Please find attached the presentation guidelines</li>';
	$message .= '    	<li>For any query, you may call on 7595949353 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com</li>';
	$message .= '    </ul>';

	//$message .= '    <br /><br/>';
	$message .= '	 We truly look forward to welcoming you to Kolkata.';
	$message .= get_invitation_email_footer();

	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';

	$message .= '</table>';

	$subject  = "Session Involvement, NEOCON 2022";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function local_faculty_unregistered_schedule_details_message($participantId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0"  width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
	$message .= '	 <br /><br />';
	$message .= '    Warm Greetings from NEOCON 2022.';
	$message .= '    <br /><br />';
	$message .= '    We feel privileged and honoured to invite you as a Faculty at NEOCON 2022, which is to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
	$message .= '    <br /><br />';
	$message .= '    We would like to have your participation in the Scientific Session as follows:';
	$message .= '    <br /><br />';

	$scheduleInArray = programScheduleStructure($participantId);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '    <ul>';
	$message .= '    	<li>It is mandatory for all Faculty to register by August 15</li>';
	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation if not sent already</li>';
	$message .= '    	<li>Please find attached the presentation guidelines</li>';
	$message .= '    	<li>For any query, you may call on 7595949353 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com</li>';
	$message .= '    </ul>';

	//$message .= '    <br /><br/>';
	$message .= '	 We truly look forward to welcoming you to the conference.';
	$message .= get_invitation_email_footer();

	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';

	$message .= '</table>';

	$subject  = "Session Involvement, NEOCON 2022";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function oral_presenter_message($participantId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$sqlListing['QUERY']		 = "SELECT * 
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowParticipant	 = $resultsListing[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0"  width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear ' . $rowParticipant['participant_full_name'] . '';
	$message .= '	 <br /><br />';
	$message .= '    CONGRATULATIONS!  ';
	$message .= '    <br /><br />';
	$message .= '    We are delighted to inform you that your paper has been selected for <b>ORAL PRESENTATION</b> in the Free Communication Session at NEOCON 2022, to be held from September 5 - 8, 2019 at ITC Royal Bengal, Kolkata.';
	$message .= '    <br /><br />';
	//$message .= '   <b>Submission Code:</b> ....................................................';
	//$message .= '    <br /><br />';

	$scheduleInArray = programScheduleStructure($participantId, true);

	$message .= implode('<br />', $scheduleInArray['DATA']);
	//$message .= '    <br />';

	if ($scheduleInArray['HASCHAIR'] == 'Y') {
		$message .= implode('<br />', $scheduleInArray['THEMEDATA']);
		//$message .= '    <br />';
	}

	$message .= '   <b> Please Note: </b> Papers will be judged by an expert panel to select the best one on the basis of:';
	$message .= '   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp; Quality of Study ';
	$message .= '   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp; Quality of Slides ';
	$message .= '   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp; Delivery  ';
	$message .= '   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp; Time Keeping ';
	$message .= '   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp; Interaction  ';
	$message .= '   <br /><br/>';

	$message .= '    <ul>';
	$message .= '    	<li>It is mandatory for all Paper Presenters to register by August 15</li>';
	$message .= '    	<li>Kindly send your short CV with photograph on a single PPT presentation immediately on your acceptance if not sent already. </li>';
	$message .= '    	<li>Please find attached the presentation guidelines</li>';
	$message .= '    </ul>';

	$message .= '    For any query, you may call on 7596071510 or mail at secretariat@aiccrcog2019.com or visit www.aiccrcog2019.com';
	$message .= '    <br /><br/>';
	$message .= '	 We truly look forward to welcome you to the conference.';
	$message .= get_invitation_email_footer();

	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_001<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_FC<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_FC_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Faculty Presentation Guidelines<a href="' . _BASE_URL_ . 'images/Faculty Presentation Guidelines_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';
	$message .= '		<br /><br /><br /><br />';
	$message .= '		Presentation Guidelines_Speakers_002<a href="' . _BASE_URL_ . 'images/Presentation Guidelines_Speakers_001.pdf' . '"><img src="' . _BASE_URL_ . 'images/pdf.png' . ' " width="50" alt="download" /></a>';

	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';

	$message .= '</table>';

	$subject  = "Session Involvement, NEOCON 2022";

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

// function programScheduleStructure($participantId, $onlyAbstracts = false)
// {
// 	global $mycms, $cfg;

// 	if ($onlyAbstracts) {
// 		$searchCondition = " AND participant_schedule.topic_id IN ( SELECT id FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " WHERE isAbstract = 'YES' AND topic_abstract_id IS NOT NULL AND topic_abstract_id != '')";
// 	}

// 	$sqlParti['QUERY'] 	   = "        SELECT participant_schedule.*, 
// 												 DATE_FORMAT(program_date.conf_date,'%d.%m.%Y (%W)') AS conf_date, 
// 												 program_session.session_start_time, program_session.session_end_time, 
// 												 (TIME_TO_SEC(CONCAT(program_session.session_start_time,':00'))/60) AS session_start_time_mins,
// 												 (TIME_TO_SEC(CONCAT(program_session.session_end_time,':00'))/60)-1 AS session_end_time_mins,
// 												 program_topic.topic_title, program_topic.reference_tag,  program_theme.theme_title, program_session.session_title, 												
// 												 IFNULL( IFNULL(program_hallTempname.hall_name, program_hall.hall_title) , IFNULL(session_hallTempname.hall_name, session_hall.hall_title) ) AS hall_title,												 
// 												 participant_details.participant_full_name, participant_details.participant_email_id
// 											FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
// 									  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
// 											  ON participant_schedule.date_id = program_date.id
// 									  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
// 											  ON participant_schedule.participant_id = participant_details.id
// 								 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
// 											  ON participant_schedule.topic_id = program_topic.id
// 								 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
// 											  ON participant_schedule.session_id = program_session.id
// 								 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " session_hall
// 											  ON program_session.session_hall_id = session_hall.id			
// 								 LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " session_hallTempname
// 											  ON session_hall.id = session_hallTempname.hall_id  
// 											 AND session_hallTempname.date_id = program_date.id
// 								 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
// 											  ON participant_schedule.theme_id = program_theme.id
// 								 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
// 											  ON participant_schedule.hall_id = program_hall.id
// 								 LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " program_hallTempname
// 											  ON session_hall.id = program_hallTempname.hall_id  
// 											 AND program_hallTempname.date_id = program_date.id
// 										   WHERE program_date.status = 'A' 
// 											 AND participant_schedule.participant_id = '" . $participantId . "' 
// 											 " . $searchCondition . "
// 										ORDER BY program_date.conf_date, program_session.session_start_time, program_session.session_end_time";
// 	$resParti		= $mycms->sql_select($sqlParti);
// 	$rowPartiDetail = array();
// 	foreach ($resParti as $key => $rowaccomm) {
// 		if ($rowaccomm['hall_id'] != '') {
// 			$rowaccomm['display_topic_title'] = $rowaccomm['hall_title'];
// 		}
// 		if ($rowaccomm['session_id'] != '') {
// 			$rowaccomm['display_topic_title'] = $rowaccomm['session_title'];
// 		}
// 		if ($rowaccomm['theme_id'] != '') {
// 			$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'];
// 		}
// 		if ($rowaccomm['topic_id'] != '') {
// 			$rowaccomm['display_topic_title'] = $rowaccomm['topic_title'];
// 			if (strtolower(trim($rowaccomm['participant_type'])) == 'speaker for') {
// 				if (trim($rowaccomm['display_topic_title']) != '') {
// 					$rowaccomm['display_topic_title'] = $rowaccomm['display_topic_title'] . ' [FOR]';
// 				} else {
// 					$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'] . ' [FOR]';
// 				}
// 			} elseif (strtolower(trim($rowaccomm['participant_type'])) == 'speaker against') {
// 				if (trim($rowaccomm['display_topic_title']) != '') {
// 					$rowaccomm['display_topic_title'] = $rowaccomm['display_topic_title'] . ' [AGAINST]';
// 				} else {
// 					$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'] . ' [AGAINST]';
// 				}
// 			}
// 		}

// 		$startTmExpld				= explode(":", $rowaccomm['start_time']);
// 		$endTmExpld					= explode(":", $rowaccomm['end_time']);

// 		$start_time					= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
// 			. ":"
// 			. (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

// 		$end_time					= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
// 			. ":"
// 			. (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

// 		$start_time_mins			= ($startTmExpld[0] * 60) + $startTmExpld[1];
// 		$end_time_mins				= ($endTmExpld[0] * 60) + $endTmExpld[1];
// 		$duration_mins		  		= $end_time_mins - $start_time_mins;

// 		$rowaccomm['start_time']	= $start_time;
// 		$rowaccomm['end_time']		= $end_time;
// 		$rowaccomm['duration']		= $duration_mins;

// 		// Session Time

// 		$startTmExpld				= explode(":", $rowaccomm['session_start_time']);
// 		$endTmExpld					= explode(":", $rowaccomm['session_end_time']);

// 		$start_time					= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
// 			. ":"
// 			. (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

// 		$end_time					= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
// 			. ":"
// 			. (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

// 		$start_time_mins			= ($startTmExpld[0] * 60) + $startTmExpld[1];
// 		$end_time_mins				= ($endTmExpld[0] * 60) + $endTmExpld[1];
// 		$duration_mins		  		= $end_time_mins - $start_time_mins;

// 		$rowaccomm['session_start_time']	= $start_time;
// 		$rowaccomm['session_end_time']		= $end_time;
// 		$rowaccomm['session_duration']		= $duration_mins;

// 		$rowPartiDetail[$rowaccomm['conf_date']][$rowaccomm['session_id']][] = $rowaccomm;
// 	}

// 	$scheduleInArray = array();
// 	$themeArray 	 = array();
// 	$hasChair		 = 'N';

// 	foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
// 		foreach ($dateWiseDetail as $sessId => $theSession) {
// 			foreach ($theSession as $key => $schedule) {
// 				$abstractData	   = getAbstractInformationOfSchedule($schedule);
// 				if ($abstractData && is_array($abstractData) && !empty($abstractData)) {
// 					foreach ($abstractData as $key => $val) {
// 						$schedule[$key] = $val;
// 					}
// 				}

// 				$scheduleInArray[] = format_programSchedule_mail($schedule);
// 				if ((strtolower(trim($schedule['participant_type'])) == 'chairperson' && strtolower(trim($schedule['isAlso'])) != 'speaker')) {

// 					//|| strtolower(trim($schedule['participant_type'])) == 'moderator'
// 					//|| strtolower(trim($schedule['participant_type'])) == 'orator'

// 					$hasChair		 = 'Y';
// 					if ($schedule['theme_id'] != '') {
// 						$scheduleInArray[] 	 = formate_wholeTheme($schedule);
// 						//$themeArray[] 	 = formate_wholeTheme($schedule);
// 					}
// 				} elseif (strtolower(trim($schedule['participant_type'])) == 'speaker for' || strtolower(trim($schedule['participant_type'])) == 'speaker against') {
// 					$hasChair		 = 'Y';
// 					$scheduleInArray[] 	 = getDebateCoParticipants($schedule, $participantId);
// 				} elseif (strtolower(trim($schedule['participant_type'])) == 'moderator' || strtolower(trim($schedule['participant_type'])) == 'panellist') {
// 					$hasChair		 = 'Y';
// 					$scheduleInArray[] 	 = getPanelCoParticipants($schedule, $participantId);
// 				}
// 				$scheduleInArray[] = "<hr/>";
// 			}
// 		}
// 	}
// 	return array("DATA" => $scheduleInArray, "HASCHAIR" => $hasChair, 'THEMEDATA' => $themeArray);
// }

function programScheduleStructure($participantId, $onlyAbstracts = false)
{
	global $mycms, $cfg;

	if ($onlyAbstracts) {
		$searchCondition = " AND participant_schedule.topic_id IN ( SELECT id FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " WHERE isAbstract = 'YES' AND topic_abstract_id IS NOT NULL AND topic_abstract_id != '')";
	}

	$sqlParti['QUERY'] 	   = "        SELECT participant_schedule.*, 
												 DATE_FORMAT(program_date.conf_date,'%d.%m.%Y (%W)') AS conf_date, 
												 program_session.session_start_time, program_session.session_end_time, 
												 (TIME_TO_SEC(CONCAT(program_session.session_start_time,':00'))/60) AS session_start_time_mins,
												 (TIME_TO_SEC(CONCAT(program_session.session_end_time,':00'))/60)-1 AS session_end_time_mins,
												 program_topic.topic_title, program_topic.reference_tag,  program_theme.theme_title, program_session.session_title, 												
												 IFNULL( IFNULL(program_hallTempname.hall_name, program_hall.hall_title) , IFNULL(session_hallTempname.hall_name, session_hall.hall_title) ) AS hall_title,												 
												 participant_details.participant_full_name, participant_details.participant_email_id
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
								 LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " session_hallTempname
											  ON session_hall.id = session_hallTempname.hall_id  
											 AND session_hallTempname.date_id = program_date.id
								 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
											  ON participant_schedule.theme_id = program_theme.id
								 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
											  ON participant_schedule.hall_id = program_hall.id
								 LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " program_hallTempname
											  ON session_hall.id = program_hallTempname.hall_id  
											 AND program_hallTempname.date_id = program_date.id
										   WHERE program_date.status = 'A' 
											 AND participant_schedule.participant_id = '" . $participantId . "' 
											 " . $searchCondition . "
										ORDER BY program_date.conf_date, program_session.session_start_time, program_session.session_end_time";
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
			if (strtolower(trim($rowaccomm['participant_type'])) == 'speaker for') {
				if (trim($rowaccomm['display_topic_title']) != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['display_topic_title'] . ' [FOR]';
				} else {
					$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'] . ' [FOR]';
				}
			} elseif (strtolower(trim($rowaccomm['participant_type'])) == 'speaker against') {
				if (trim($rowaccomm['display_topic_title']) != '') {
					$rowaccomm['display_topic_title'] = $rowaccomm['display_topic_title'] . ' [AGAINST]';
				} else {
					$rowaccomm['display_topic_title'] = $rowaccomm['theme_title'] . ' [AGAINST]';
				}
			}
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

		// Session Time

		$startTmExpld				= explode(":", $rowaccomm['session_start_time']);
		$endTmExpld					= explode(":", $rowaccomm['session_end_time']);

		$start_time					= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
			. ":"
			. (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

		$end_time					= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
			. ":"
			. (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

		$start_time_mins			= ($startTmExpld[0] * 60) + $startTmExpld[1];
		$end_time_mins				= ($endTmExpld[0] * 60) + $endTmExpld[1];
		$duration_mins		  		= $end_time_mins - $start_time_mins;

		$rowaccomm['session_start_time']	= $start_time;
		$rowaccomm['session_end_time']		= $end_time;
		$rowaccomm['session_duration']		= $duration_mins;

		$rowPartiDetail[$rowaccomm['conf_date']][$rowaccomm['session_id']][] = $rowaccomm;
	}

	$scheduleInArray = array();
	$themeArray 	 = array();
	$hasChair		 = 'N';

	//echo '<pre>'; print_r($rowPartiDetail);

	foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
		foreach ($dateWiseDetail as $sessId => $theSession) {
			foreach ($theSession as $key => $schedule) {
				$abstractData	   = getAbstractInformationOfSchedule($schedule);
				if ($abstractData && is_array($abstractData) && !empty($abstractData)) {
					foreach ($abstractData as $key => $val) {
						$schedule[$key] = $val;
					}
				}

				//echo strtolower(trim($schedule['participant_type']));

				//echo $schedule['participant_type'];
				$scheduleInArray[] = format_programSchedule_mail($schedule);
				if ((strtolower(trim($schedule['participant_type'])) == 'chairperson' && strtolower(trim($schedule['isAlso'])) != 'speaker')) {

					//|| strtolower(trim($schedule['participant_type'])) == 'moderator'
					//|| strtolower(trim($schedule['participant_type'])) == 'orator'

					$hasChair		 = 'Y';
					if ($schedule['theme_id'] != '') {


						$scheduleInArray[] 	 = formate_wholeTheme($schedule);
						//$themeArray[] 	 = formate_wholeTheme($schedule);
					}
				} elseif (strtolower(trim($schedule['participant_type'])) == 'speaker for' || strtolower(trim($schedule['participant_type'])) == 'speaker against' || strtolower(trim($schedule['participant_type'])) == 'debate moderator') {

					$hasChair		 = 'Y';
					$scheduleInArray[] 	 = getDebateCoParticipants($schedule, $participantId);
				} elseif (strtolower(trim($schedule['participant_type'])) == 'moderator' || trim($schedule['participant_type']) == 'Panelist' || strtolower(trim($schedule['participant_type'])) == 'panel moderator' || strtolower(trim($schedule['participant_type'])) == 'expert') {


					$hasChair		 = 'Y';
					$scheduleInArray[] 	 = getPanelCoParticipants($schedule, $participantId);
				}
				$scheduleInArray[] = "<hr/>";
			}
		}
	}
	return array("DATA" => $scheduleInArray, "HASCHAIR" => $hasChair, 'THEMEDATA' => $themeArray);
}

function getPanelCoParticipants($schedule, $theParticipantId)
{
	global $mycms, $cfg;

	$formatttedContent = "";

	if ($schedule['topic_id'] != '') {
		$sqlParticipantTheme = array();
		$sqlParticipantTheme['QUERY']		 = "SELECT prt.*, sch.participant_type
													  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
												INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
														ON sch.participant_id = prt.id
													 WHERE sch.`session_id` = '" . $schedule['session_id'] . "'
													   AND sch.`theme_id` = '" . $schedule['theme_id'] . "'
													   AND sch.`topic_id` = '" . $schedule['topic_id'] . "'
												  ORDER BY prt.participant_full_name";
		$resultsParticipant	 				= $mycms->sql_select($sqlParticipantTheme);
	} elseif ($schedule['theme_id'] != '') {
		$sqlParticipantTheme 				 = array();
		$sqlParticipantTheme['QUERY']		 = "SELECT prt.*, sch.participant_type
													  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
												INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
														ON sch.participant_id = prt.id
													 WHERE sch.`session_id` = '" . $schedule['session_id'] . "'
													   AND sch.`theme_id` = '" . $schedule['theme_id'] . "'
													   AND (sch.topic_id IS NULL OR sch.topic_id = '')";
		$resultsParticipant	 		 		= $mycms->sql_select($sqlParticipantTheme);
	}

	if ($resultsParticipant) {
		$participantArr = array();
		foreach ($resultsParticipant as $kPt => $rowParticipant) {
			if (trim($rowParticipant['participant_type']) == '') {
				$participantArr['none'][] = $rowParticipant['participant_full_name'];
			} else {
				$participantArr[strtolower(trim($rowParticipant['participant_type']))][] = $rowParticipant;
			}
		}


		if (strtolower(trim($schedule['participant_type'])) == 'moderator') {
			$moderatorLabel = 'Co-moderator(s)';
			$isModerator = true;
		} else {
			$moderatorLabel = 'Moderator(s)';
			$isModerator = false;
		}

		if (strtolower(trim($schedule['participant_type'])) == 'panellist') {
			$panellistLabel = 'Co-panellist(s)';
			$isPannellist = true;
		} else {
			$panellistLabel = 'Panellist(s)';
			$isPannellist = false;
		}



		if (false) {

			$formatttedContent 				.= '<b>Panel Details</b>';
			$formatttedContent 				.= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';

			if (($isModerator && sizeof($participantArr[strtolower(trim('moderator'))]) > 1) || !$isModerator) {


				$formatttedContent 			.= '	<tr>';
				$formatttedContent 			.= '		<td style="font-weight:bold;" valign="top">' . $moderatorLabel . '</td>';
				$formatttedContent 			.= '		<td style="font-weight:bold;" valign="top" width="100px" >Phone No.</td>';
				$formatttedContent 			.= '		<td style="font-weight:bold;" valign="top" width="280px">Email Id</td>';
				$formatttedContent			.= '	</tr>';

				foreach ($participantArr[strtolower(trim('moderator'))] as $kPt => $rowParticipant) {
					if ($rowParticipant['id'] != $theParticipantId) {
						$formatttedContent 	.= '	<tr>';
						$formatttedContent 	.= '		<td valign="top">' . $rowParticipant['participant_full_name'] . '</td>';
						$formatttedContent 	.= '		<td valign="top">' . $rowParticipant['participant_mobile_no'] . '</td>';
						$formatttedContent 	.= '		<td valign="top">' . $rowParticipant['participant_email_id'] . '</td>';
						$formatttedContent 	.= '	</tr>';
					}
				}
			}

			if (!$isPannellist) //($isPannellist && sizeof($participantArr[strtolower(trim('panellist'))]) > 1) || 
			{

				$formatttedContent 			.= '	<tr>';
				$formatttedContent 			.= '		<td colspan="3">&nbsp;</td>';
				$formatttedContent 			.= '	</tr>';

				$formatttedContent 			.= '	<tr>';
				$formatttedContent 			.= '		<td style="font-weight:bold;" valign="top">' . $panellistLabel . '</td>';
				$formatttedContent 			.= '		<td style="font-weight:bold;" valign="top">Phone No.</td>';
				$formatttedContent 			.= '		<td style="font-weight:bold;" valign="top">Email Id</td>';
				$formatttedContent 			.= '	</tr>';

				foreach ($participantArr[strtolower(trim('panellist'))] as $kPt => $rowParticipant) {
					if ($rowParticipant['id'] != $theParticipantId) {
						$formatttedContent 	.= '	<tr>';
						$formatttedContent 	.= '		<td valign="top">' . $rowParticipant['participant_full_name'] . '</td>';
						$formatttedContent 	.= '		<td valign="top">' . $rowParticipant['participant_mobile_no'] . '</td>';
						$formatttedContent 	.= '		<td valign="top">' . $rowParticipant['participant_email_id'] . '</td>';
						$formatttedContent 	.= '	</tr>';
					}
				}
			}

			$formatttedContent 				.= '</table>';
		} else {
			$formatttedContent 				.= '<b>Panel Details</b>';
			//$formatttedContent 				.= '<hr/>';

			if (($isModerator && sizeof($participantArr[strtolower(trim('moderator'))]) > 1) || !$isModerator) {

				$formatttedContent 				.= '<br/><br/><b>' . $moderatorLabel . '</b>';

				foreach ($participantArr[strtolower(trim('moderator'))] as $kPt => $rowParticipant) {
					if ($rowParticipant['id'] != $theParticipantId) {
						$formatttedContent 		.= '<br/><br/><b>' . $rowParticipant['participant_full_name'] . '</b>';
						$formatttedContent 		.= '<br/><i>mobile : ' . $rowParticipant['participant_mobile_no'] . '</i>';
						$formatttedContent 		.= '<br/><i>email : ' . $rowParticipant['participant_email_id'] . '</i>';
					}
				}

				//$formatttedContent 				.= '<hr/>';
			}

			if (!$isPannellist) //($isPannellist && sizeof($participantArr[strtolower(trim('panellist'))]) > 1) ||
			{

				$formatttedContent 				.= '<br/><br/><b>' . $panellistLabel . '</b>';

				foreach ($participantArr[strtolower(trim('panellist'))] as $kPt => $rowParticipant) {
					if ($rowParticipant['id'] != $theParticipantId) {
						$formatttedContent 		.= '<br/><br/><b>' . $rowParticipant['participant_full_name'] . '</b>';
						$formatttedContent 		.= '<br/><i>mobile : ' . $rowParticipant['participant_mobile_no'] . '</i>';
						$formatttedContent 		.= '<br/><i>email : ' . $rowParticipant['participant_email_id'] . '</i>';
					}
				}
				//$formatttedContent 				.= '<hr/>';
			}
		}
	}
	return $formatttedContent;
}

function getDebateCoParticipants($schedule, $theParticipantId)
{
	global $mycms, $cfg;

	$formatttedContent = "";

	$sqlParticipantTheme 				 = array();
	$sqlParticipantTheme['QUERY']		 = "SELECT prt.*, sch.participant_type
												  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
											INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
													ON sch.participant_id = prt.id
												 WHERE sch.`session_id` = '" . $schedule['session_id'] . "'
												   AND sch.`theme_id` = '" . $schedule['theme_id'] . "'";
	$resultsParticipant	 		 		= $mycms->sql_select($sqlParticipantTheme);


	if ($resultsParticipant) {
		$participantArr = array();
		foreach ($resultsParticipant as $kPt => $rowParticipant) {
			if ($rowParticipant['id'] != $theParticipantId) {
				if (trim($rowParticipant['participant_type']) == '') {
					$participantArr['none'][] = $rowParticipant['participant_full_name'];
				} else {
					$participantArr[strtolower(trim($rowParticipant['participant_type']))][] = $rowParticipant['participant_full_name'];
				}
			}
		}

		if (strtolower(trim($schedule['participant_type'])) == 'speaker against') {
			$data1 = $participantArr[strtolower(trim('speaker against'))];
			$data2 = $participantArr[strtolower(trim('speaker for'))];
			$data1Label = $schedule['theme_title'] . " [AGAINST]";
			$data2Label = $schedule['theme_title'] . " [FOR]";
		} elseif (strtolower(trim($schedule['participant_type'])) == 'speaker for') {
			$data2 = $participantArr[strtolower(trim('speaker against'))];
			$data1 = $participantArr[strtolower(trim('speaker for'))];
			$data2Label = $schedule['theme_title'] . " [AGAINST]";
			$data1Label = $schedule['theme_title'] . " [FOR]";
		}

		$formatttedContent 				.= '<b>Debate Details</b>';
		$formatttedContent 				.= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="60%">';

		if (!empty($data1)) {
			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td>' . $data1Label . '</td>';
			$formatttedContent 			.= '	</tr>';
			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td style="font-weight:normal;" valign="top"  width="50px" >Speaker(s) :' . implode(', ', $data1) . '</td>';
			$formatttedContent			.= '	</tr>';
			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td style="font-weight:normal;" valign="top">Duration : ' . $schedule['duration'] . ' Mins</td>';
			$formatttedContent			.= '	</tr>';

			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td>&nbsp;</td>';
			$formatttedContent 			.= '	</tr>';
		}

		if (!empty($data2)) {
			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td>' . $data2Label . '</td>';
			$formatttedContent 			.= '	</tr>';
			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td style="font-weight:normal;" valign="top">Speaker(s) :' . implode(', ', $data2) . ' </td>';
			$formatttedContent			.= '	</tr>';
			$formatttedContent 			.= '	<tr>';
			$formatttedContent 			.= '		<td style="font-weight:normal;" valign="top">Duration : ' . $schedule['duration'] . ' Mins</td>';
			$formatttedContent			.= '	</tr>';
		}
		$formatttedContent 				.= '</table>';
	}
	return $formatttedContent;
}

function format_programSchedule_mail($schedule)
{
	$formatttedContent = "";
	switch (strtolower(trim($schedule['participant_type']))) {
		case "speaker":
		case "speaker for":
		case "speaker against":
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">Speaker</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session Date & Time</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['conf_date'] . ' ' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['session_title']) != '') {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Topic</td>';
			$formatttedContent .= '		<td valign="top">' . (trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Duration of Presentation</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['start_time'] . ' - ' . $schedule['end_time'] . ' [' . $schedule['duration'] . '  minutes] </td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '</table>';
			break;
		case "moderator":
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">Moderator</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session Date & Time</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['conf_date'] . ' ' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['session_title']) != '') {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Topic of Panel</td>';
			$formatttedContent .= '		<td valign="top">' . (trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Duration of Panel </td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['start_time'] . ' - ' . $schedule['end_time'] . ' [' . $schedule['duration'] . '  minutes] </td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '</table>';
			break;
		case "chairperson":
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">Chairperson</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session Date & Time</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['conf_date'] . ' ' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['session_title']) != '') {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Duration of Session </td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['start_time'] . ' - ' . $schedule['end_time'] . ' [' . $schedule['duration'] . '  minutes] </td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '</table>';
			break;
		case "panellist":
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">Panellist</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session Date & Time</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['conf_date'] . ' ' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['session_title']) != '') {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Topic of Panel</td>';
			$formatttedContent .= '		<td valign="top">' . (trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Duration of Panel </td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['start_time'] . ' - ' . $schedule['end_time'] . ' [' . $schedule['duration'] . '  minutes] </td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '</table>';
			break;
		case "paper presenter":
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['participant_type'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session Date & Time</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['conf_date'] . ' ' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['session_title']) != '') {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Topic</td>';
			$formatttedContent .= '		<td valign="top">' . (trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Duration</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['start_time'] . ' - ' . $schedule['end_time'] . ' [' . $schedule['duration'] . '  minutes] </td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['NOMINATIONS'])) {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Nomination</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['NOMINATIONS'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '</table>';
			break;
		case "expert":
			$formatttedContent .= '<p><span style="color:blue;">Date: </span>' . $schedule['conf_date'] . '</p>';
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">Expert</td>';
			$formatttedContent .= '	</tr>';

			// $formatttedContent .= '	<tr>';
			// $formatttedContent .= '		<td style="font-weight:bold;" valign="top">Panel Topic</td>';
			// $formatttedContent .= '		<td valign="top">' . (trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title'] . '</td>';
			// $formatttedContent .= '	</tr>';
			// $formatttedContent .= '	<tr>';
			// $formatttedContent .= '		<td style="font-weight:bold;" valign="top">Panel Duration</td>';
			// $formatttedContent .= '		<td valign="top"> ' . $schedule['duration'] . '  minutes </td>';
			// $formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session </td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . ' (' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . ')</td>';
			$formatttedContent .= '	</tr>';

			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			/*if(trim($schedule['session_title'])!='')
							{
							$formatttedContent .= '	<tr>';
							$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
							$formatttedContent .= '		<td valign="top">'.$schedule['session_title'].'</td>';
							$formatttedContent .= '	</tr>';
							}*/

			$formatttedContent .= '</table>';
			break;
		default:
			$formatttedContent .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;" border=1 width="100%">';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" width="180px" valign="top">Nature of Participation</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['participant_type'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session Date & Time</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['conf_date'] . ' ' . $schedule['session_start_time'] . ' - ' . $schedule['session_end_time'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Hall</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['hall_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			if (trim($schedule['session_title']) != '') {
				$formatttedContent .= '	<tr>';
				$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Session</td>';
				$formatttedContent .= '		<td valign="top">' . $schedule['session_title'] . '</td>';
				$formatttedContent .= '	</tr>';
			}
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Topic</td>';
			$formatttedContent .= '		<td valign="top">' . (trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title'] . '</td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '	<tr>';
			$formatttedContent .= '		<td style="font-weight:bold;" valign="top">Duration</td>';
			$formatttedContent .= '		<td valign="top">' . $schedule['start_time'] . ' - ' . $schedule['end_time'] . ' [' . $schedule['duration'] . '  minutes] </td>';
			$formatttedContent .= '	</tr>';
			$formatttedContent .= '</table>';
			break;
	}
	return $formatttedContent;
}

function formate_wholeTheme($schedule, $theParticipantId = '')
{
	global $mycms, $cfg;

	$themeId = $schedule['theme_id'];
	$return = "";

	$sqlFetchTheme['QUERY']   = "SELECT *, 
										   (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
										   (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
									  FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
									 WHERE id = '" . $themeId . "'
									   AND status = 'A'  
								  ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";
	$resultTheme   = $mycms->sql_select($sqlFetchTheme);

	foreach ($resultTheme as $keyTheme => $rowtheme) {

		$return = '<table width="700px;" style="background:#DBDBDB;" border="1">
							<tr>
							<td align="center" valign="top">
							<!--<span style="font-weight:bold;">' . $schedule['conf_date'] . ' ' . $rowtheme['theme_time_slot'] . '</span><br>
							<span style="font-weight:bold;">' . $schedule['session_title'] . ' ' . $rowtheme['theme_title'] . '</span>-->
							<span style="font-weight:bold;">Session Details</span>
							<div use="participantsList">
								<table width="100%">
									<tr>
									<td align="center" style="margin:0px; padding:0px;font-weight:normal;font-size:11px;">';

		$sqlParticipantTheme['QUERY'] = "   SELECT prt.*, sch.participant_type
												  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
											INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
													ON sch.participant_id = prt.id
												 WHERE sch.`theme_id` = '" . $themeId . "'
												   AND (sch.topic_id IS NULL OR sch.topic_id = '')";
		$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme);
		$themePartArr	= array();
		if ($resultsParticipantTheme) {
			$participantArr = array();
			foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
				if (trim($rowParticipant['participant_type']) == '') {
					$participantArr['none'][] = $rowParticipant['participant_full_name'];
				} else {
					$participantArr[strtolower(trim($rowParticipant['participant_type']))][] = $rowParticipant['participant_full_name'];
				}
			}

			$keys = array_keys($participantArr);

			foreach ($participantTypes as $kPt => $rowParticipantType) {
				$rowParticipant = implode(', ', $participantArr[$rowParticipantType]);
				if (trim($rowParticipant) != '') {
					$themePartArr[] = "<b>" . ucwords($rowParticipantType) . "</b>:" . $rowParticipant;
				}
			}

			foreach ($participantArr as $prtTyp => $rowParticipant) {
				if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
					sort($rowParticipant);
					$themePartArr[] = "<b>" . ucwords($prtTyp) . "</b>:" . implode(', ', $rowParticipant);
				}
			}

			if ($participantArr['none'] != '') {
				sort($participantArr['none']);
				$themePartArr[] = "<b>Participant</b>:" . implode(', ', $participantArr['none']);
			}


			$return .= implode('<br/>', $themePartArr);
		}

		$return .= '			</td>
									</tr>
								</table>
							</div>
							</td>
							</tr>';

		$sqlFetchTopic['QUERY']  = "SELECT *, 
											   topic_time_duration AS topic_time_duration_mins,
											   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
											   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
										  FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
										 WHERE schedule_theme_id = '" . $themeId . "'
										   AND status = 'A'
									  ORDER BY sequence, id";
		$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);

		if ($resultTopic) {
			$return .= '<tr>
							<td style="padding:1px 0px;">';


			foreach ($resultTopic as $keyTopic => $rowTopic) {
				$return .= '<table width="100%" style="background:#FFFFFF;">
									<tr>
									<td align="center" valign="top">
									<span style="float:left; font-weight:bold;">' . $rowTopic['topic_time_duration'] . ' m</span> 
									<span style="padding:0px 0px 0px 20px;">' . $rowTopic['topic_title'] . '</span>
									<div use="participantsList">
									<table width="100%">
									<tr>
										<td align="center" style="margin:0px; padding:0px; font-weight:normal;font-size:11px;">';

				$sqlParticipantTheme['QUERY'] = "	SELECT prt.*, sch.participant_type
														  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
													INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
															ON sch.participant_id = prt.id
														 WHERE sch.`session_id` = '" . $rowTopic['schedule_session_id'] . "'
														   AND sch.`theme_id` = '" . $rowTopic['schedule_theme_id'] . "'
														   AND sch.`topic_id` = '" . $rowTopic['id'] . "'
													  ORDER BY prt.participant_full_name";
				$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme);
				$topicPartArr = array();
				if ($resultsParticipantTheme) {
					$participantArr = array();
					foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
						if (trim($rowParticipant['participant_type']) == '') {
							$participantArr['none'][] = $rowParticipant['participant_full_name'];
						} else {
							$participantArr[strtolower(trim($rowParticipant['participant_type']))][] = $rowParticipant['participant_full_name'];
						}
					}

					$keys = array_keys($participantArr);

					foreach ($participantTypes as $kPt => $rowParticipantType) {
						$rowParticipant = implode(', ', $participantArr[$rowParticipantType]);
						if (trim($rowParticipant) != '') {
							$topicPartArr[] = "<b>" . ucwords($rowParticipantType) . "</b>:" . $rowParticipant;
						}
					}

					foreach ($participantArr as $prtTyp => $rowParticipant) {
						if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
							sort($rowParticipant);
							$topicPartArr[] = "<b>" . ucwords($prtTyp) . "</b>:" . implode(', ', $rowParticipant);
						}
					}

					if ($participantArr['none'] != '') {
						sort($participantArr['none']);
						$topicPartArr[] = "<b>Participant</b>:" . implode(', ', $participantArr['none']);
					}

					$return .= implode('<br/>', $topicPartArr);
				}

				$return .= '		</td>
									</tr>
									</table>
									</td>
									</tr>
								</table>';
			}

			$return .= '</td>
							</tr>';
		}
		$return .= '</table>';
	}

	return $return;
}

function getAbstractInformationOfSchedule($schedule)
{
	global $mycms, $cfg;

	$return = array();

	$sqlAbs['QUERY'] 	   = "        SELECT abstract.*, 
												 abstractTopic.abstract_topic,
												 abstractAward.award_ids AS award_ids, 
												 abstractAward.award_names AS award_names												 
											FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " scheduleTopic
									  INNER JOIN " . _DB_ABSTRACT_REQUEST_ . " abstract
											  ON scheduleTopic.topic_abstract_id = abstract.id
											 AND abstract.status = 'A'
								 LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
											  ON abstract.abstract_topic_id = abstractTopic.id 
							     LEFT OUTER JOIN ( SELECT GROUP_CONCAT(award_id) AS award_ids, GROUP_CONCAT(award_name) AS award_names, req.submission_id AS submission_id
												     FROM " . _DB_AWARD_REQUEST_ . " req
											   INNER JOIN " . _DB_AWARD_MASTER_ . " awrd
													   ON awrd.id = req.award_id
												    WHERE req.status = 'A'
											     GROUP BY req.submission_id) abstractAward 
											  ON abstract.id = abstractAward.submission_id 									  
										   WHERE scheduleTopic.id = '" . $schedule['topic_id'] . "'
										   	 AND scheduleTopic.isAbstract = 'YES'";

	$resAbs				  = $mycms->sql_select($sqlAbs);
	if ($resAbs) {
		$rowAbs						= $resAbs[0];
		$rowAbs['SUBMISSION_CODE']	= $rowAbs['abstract_submition_code'];
		$rowAbs['ABSTRACT_DETAILS']	= getAbstractContent($rowAbs['id']);
		$rowAbs['NOMINATIONS']		= $rowAbs['award_names'];
		return $rowAbs;
	} else {
		return false;
	}
}

/******************************************************Exhibitor*******************************************************************/
function exhibitor_bulk_upload_details_message($exhibitorId, $cutoffId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$sqlExhibitorCompany      = exhibitorCompanyQuerySet($exhibitorId, "");
	$resultExhibitorCompany   = $mycms->sql_select($sqlExhibitorCompany);
	$rowExhibitorCompany      = $resultExhibitorCompany[0];

	$TOKEN = base64_encode(serialize(array('exhibitorId' => $exhibitorId, 'cutoffid' => $cutoffId)));

	$sqlExhibitorContactPerson['QUERY']	= " SELECT *
												  FROM " . _DB_EXIBITOR_MAILHISTORY_ . " 
												 WHERE `exhibitorId` = '" . $exhibitorId . "' ORDER BY id limit 1 ";
	$contactPersonRow = $mycms->sql_select($sqlExhibitorContactPerson);

	// COMPOSING EMAIL
	/*$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= get_invitation_email_header();
		$message .= '<tr>';
		$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
		$message .= get_invitation_email_side_1();
		$message .= '	 </td>';
		$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>'.date('d.m.Y').'</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
		$message .= '	 Dear Sir / Madam';
		$message .= '	 <br /><br />';
		$message .= '    Warm Greetings from AICC RCOG 2019.';
		$message .= '    <br /><br />';
		$message .= '    Please Follow the following link to enter your users.';
		$message .= '    <br /><br />';
		$message .= '    '._BASE_URL_.'exhibitor.bulkentry.screen.php?TOKEN='.$TOKEN;
		$message .= '    <br /><br />';
		
		$message .= '    Regards';
		$message .= '    <br /><br />';
		$message .= '    AICC RCOG 2019 Team';
		
		$message .= ' 	</td>';
		$message .= ' </tr>';		
		$message .= '<tr>';
		$message .= '	 <td align="center" valign="bottom">';
		$message .= get_invitation_email_side_2();
		$message .= '	 </td>';
		$message .= '</tr>';		
		$message .= '</table>';*/

	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '	 <br /><br />';
	$message .= $contactPersonRow[0]['emailContent'];
	$message .= '    <br /><br />';

	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = $contactPersonRow[0]['emailSubject'];

	$confsms	 = "Dear " . $rowParticipant['participant_full_name'] . ". An email containing the details of your involvements in the Scientific Sessions of NEOCON 2022 has been sent to you. Kindly check your spam once if you do not receive the mail in inbox. Have a nice day.";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

/******************************************************Abstracts*******************************************************************/
function abstractUnregisteredUserReminderMail($uerId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$sqlAbstractDetails['QUERY']		= " SELECT registeredDelegates.*,
													   country.country_name AS author_country_name,
													   state.state_name AS author_state_name
													   						
												  FROM " . _DB_USER_REGISTRATION_ . " registeredDelegates 
												  
									   LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
													ON registeredDelegates.user_country_id = country.country_id
													
									   LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
													ON registeredDelegates.user_state_id = state.st_id
													
												 WHERE registeredDelegates.id = '" . $userId  . "'
												   AND registeredDelegates.status = 'A'
												   AND registeredDelegates.registration_request = 'ABSTRACT'  " . $searchCondition . "
											  ORDER BY registeredDelegates.id DESC ";
	$resultAbstractUser         	 	= $mycms->sql_select($sqlAbstractDetails);
	$rowAbstractUser				 	= $resultAbstractUser[0];

	// COMPOSING EMAIL
	$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_invitation_email_header();
	$message .= '<tr>';
	$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
	$message .= get_invitation_email_side_1();
	$message .= '	 </td>';
	$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>' . date('d.m.Y') . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
	$message .= '	 Dear Doctor';
	$message .= '	 <br /><br />';
	$message .= '    We have not yet received any information regarding your registration in our system. It is a gentle reminder that as per the norms of NEOCON 2022, all Faculty need to register for the conference. ';
	$message .= '    <br /><br />';
	$message .= '    Kindly register yourself at the earliest to avail the Regular rates. ';
	$message .= '    <br /><br />';

	$message .= '    Regards';
	$message .= '    <br />';
	$message .= '    Scientific Committee';
	$message .= '    <br />';
	$message .= '    NEOCON 2022';

	//$message .= get_invitation_email_footer();
	$message .= ' 	</td>';
	$message .= ' </tr>';
	$message .= '<tr>';
	$message .= '	 <td align="center" valign="bottom">';
	$message .= get_invitation_email_side_2();
	$message .= '	 </td>';
	$message .= '</tr>';
	$message .= '</table>';

	$subject  = "A gentle reminder";

	$confsms	 = "We have not yet received any information regarding your registration in our system. It is a gentle reminder that as per the norms of NEOCON 2022, all Faculty need to register for the conference. ";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

function abstractPosterMail($abstractId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$sqlAbstractDetails			   = abstractDetailsQuerySet($abstractId, '');
	$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
	$rowAbstractDetails            = $resultAbstractDetails[0];
	$delegateId					   = $rowAbstractDetails['applicant_id'];
	$rowAbstractUser			   = getUserDetails($rowAbstractDetails['applicant_id']);

	$abstractDetailsArray 		   = getAbstractDetailsArray($abstractId);

	//echo '<pre>'; print_r($rowAbstractDetails);

	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM " . _DB_EMAIL_TEMPLATE_ . " 
					       WHERE status = ? 
						     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 8,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row = $result[0];



	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	if ($row['header_image'] != '') {
		$emailHeader  = $header_image;
	}

	if ($row['footer_image'] != '') {
		$emailFooter  = $footer_image;
	}

	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);
	$fullName = $rowAbstractUser['user_first_name'] . " " . $rowAbstractUser['user_middle_name'] . " " . $rowAbstractUser['user_last_name'];
	//print_r($rowAbstractUser);

	$find = ['[Name]', '[AWARD_NAME]', '[ABSTRACT_TITLE]'];

	$replacement = [$fullName, $rowAbstractDetails['abstract_cat'], $rowAbstractDetails['abstract_title']];


	$result = str_replace($find, $replacement, $mailTemplateDescription);

	$message = '';

	$message .= '<img src="' . $emailHeader . ' " width="100%" alt="header" /><br/><br/>';
	$message .= $result;
	$message .= '<img src="' . $emailFooter . ' " width="100%" alt="header" /><br/><br/>';

	// COMPOSING EMAIL
	/*$message  = '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= get_invitation_email_header();
		$message .= '<tr>';
		$message .= '	 <td width="28%" align="center" valign="top" rowspan="2">';
		//$message .= get_invitation_email_side_1();
		$message .= '	 </td>';
		$message .= '	 <td align="right" valign="top" width="72%"><strong>Date: </strong>'.date('d.m.Y').'</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '	 <td align="left" valign="top" rowspan="2" style="padding-left:20px;">';
		$message .= '	 	Dear '.$rowAbstractUser['user_first_name']." ".$rowAbstractUser['user_middle_name']." ".$rowAbstractUser['user_last_name']." ";
		$message .= '		 <br /><br />';
		$message .= '   	 CONGRATULATIONS!  ';
		$message .= '    	<br /><br />';
		$message .= '    	We are happy to inform you that your poster has been accepted for the <b>'.$rowAbstractDetails['award_names'].'</b>';
		$message .= '    	<br /><br />';
		$message .= '    	Title: '.$rowAbstractDetails['abstract_title'];
		$message .= '    	<br /><br />';
		$message .= '    	Posters will be judged by an expert panel to select the best one in the respective prize category on the basis of:';
		$message .= '    	<ul>';
		$message .= '   		<li>Quality of Study</li>';
		$message .= '   		<li>Quality of Poster</li>';
		$message .= '   		<li>Interaction</li>';
		$message .= '    	</ul>';
		$message .= '    	All Poster Presenters are requested to <b>register by August 15</b>, if you have not done so already.';
		$message .= '    	<br /><br />';
		$message .= '    	<b>IT IS MANDATORY TO SEND YOUR E-POSTER AT secretariat@aiccrcog2019.com WITHIN 10 DAYS OF RECEIPT OF THIS MAIL.  </b>';
		$message .= '    	<br /><br />';
		$message .= '    	Please find attached the <b>E-POSTER Presentation Guidelines.</b>';
		$message .= '    	<br /><br />';
		$message .= '    	We look forward to welcoming you to the conference.';
		$message .= '   	<br /><br />';
		//$message .= get_invitation_email_footer();
		$message .= '		<br /><br /><br /><br />';
		//$message .= '		<a href="'._BASE_URL_.'images/E_poster_Guidelines_001.pdf'.'"><img src="'._BASE_URL_.'images/pdf.png'.' " width="50" alt="download" /></a>';
		$message .= ' 	</td>';
		$message .= ' </tr>';		
		$message .= '<tr>';
		$message .= '	 <td align="center" valign="bottom">';
		//$message .= get_invitation_email_side_2();
		$message .= '	 </td>';
		$message .= '</tr>';		
		$message .= '</table>';
		
		$message  .= '<table border="0" align="left" cellpadding="0" cellspacing="0" width="800px" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '<tr>';
		$message .= '	 <td align="left" valign="bottom">';
		
		$message .= '	 </td>';
		$message .= '</tr>';		
		$message .= '</table>';*/

	$subject  = $rowaMail['subject'];

	$confsms	 = "";

	if ($operation == 'SEND') {
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = trim($message);
		$array['SMS_NO'] 	  = $rowParticipant['participant_mobile_no'];
		$array['SMS_BODY'][0] = $confsms;
		return $array;
	} else {
		return false;
	}
}

/******************************************************CANCELLATION MAIL*******************************************************************/
function generateCancelInvoiceOrderSummary($delegateId, $invoice_ids)
{
	global $cfg, $mycms;
	return false;

	$sqlInvoice = array();
	$sqlInvoice['QUERY'] = "SELECT * 
	    						  FROM  " . _DB_INVOICE . " 
	    						 WHERE  `id` = ?";

	$resInvoice['PARAM'][]	=	array('FILD' => 'id',  'DATA' => $invoice_ids,     'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$userDetails = getUserDetails($delegateId);
	$userCutoffId = getUserCutoffId($delegateId);
	$cutOffName   = getCutoffName($userCutoffId);

	$templateStringArray               = array();
	$templateStringArray['HEAD'][]     = '<table width="100%" border="1"  cellpadding="5">';
	$templateStringArray['HEAD'][]     = '<tr>';
	$templateStringArray['HEAD'][]     = '<td width="5%" valign="top">Services/Facilities Taken</td>';
	$templateStringArray['HEAD'][]     = '<td width="5%" valign="top">Specifications</td>';
	$templateStringArray['HEAD'][]     = '<td width="5%" valign="top">Invoice Amount</td>';
	$templateStringArray['HEAD'][]     = '<td width="5%" valign="top">Registration Status</td>';

	$templateStringArray['HEAD'][] = '</tr>';
	$breakast = array();
	$breakastAmt = array();
	$workshop = array();
	$status = false;
	if ($resInvoice) {
		$counter = 0;
		$totalAmt = 0;
		foreach ($resInvoice as $keyInvoice => $rowInvoice) {

			if ($rowInvoice['payment_status'] == 'PAID' || $rowInvoice['payment_status'] == 'UNPAID') {
				$invAmt = $rowInvoice['service_roundoff_price'];
				$totalAmt += $invAmt;
			} else {
				if ($rowInvoice['payment_status'] == "ZERO_VALUE") {
					$invAmt = "Zero Value";
				} else if ($rowInvoice['payment_status'] == "COMPLIMENTARY") {
					$invAmt = "Complementary";
				}
			}
			if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {

				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<tr>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">Conference Registration</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%" align="left">' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">' . $invAmt . '</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">CANCELLED</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '</tr>';
			}
			if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {

				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<tr>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">' . $cfg['RESIDENTIAL_NAME'] . '</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%" align="left">' . getRegClsfName(getUserClassificationId($delegateId)) . '</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">' . $invAmt . '</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">CANCELLED</td>';
				$templateStringArray['DELEGATE_CONFERENCE_REGISTRATION'][]                 = '</tr>';
			}
			if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$workShopDetails 				 = getWorkshopDetails($rowInvoice['refference_id'], true);

				$breakast[$counter]                      = getWorkshopName($workShopDetails['workshop_id']) . '';
				$breakastAmt[$counter]                   = $invAmt;
				$counter++;
			}
			if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$accompanyDetails 				 = getUserDetails($rowInvoice['refference_id']);
				$acmponyStatus = true;
				$acmponyCounter++;
				$acmpony[]                       = $accompanyDetails['user_full_name'];
			}
			if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$sqlaccommodationDetails   = array();
				$sqlaccommodationDetails['QUERY']  = "SELECT accommodation.*,package.package_name 
				  										    FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
												      INNER JOIN " . _DB_ACCOMMODATION_PACKAGE_ . " package
														      ON accommodation.package_id = package.id
													       WHERE accommodation.status IN ('A','C')
														     AND accommodation.user_id = ? 
														     AND accommodation.refference_invoice_id = ?";
				$sqlaccommodationDetails['PARAM'][]	=	array('FILD' => 'accommodation.user_id',    'DATA' => $delegateId,     'TYP' => 's');
				$sqlaccommodationDetails['PARAM'][]	=	array('FILD' => 'accommodation.refference_invoice_id',    'DATA' => $rowInvoice['id'],     'TYP' => 's');
				$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
				$rowaccommodation = $resaccommodation[0];


				$workshop[] 					 = getWorkshopName($workShopDetails['workshop_id']);
				$templateStringArray['DELEGATE_ACCOMMODATION_REQUEST'][]                 = '<tr>';
				$templateStringArray['DELEGATE_ACCOMMODATION_REQUEST'][]                 = '<td width="5%" valign="top">Accommodation</td><td width="5%">ITC Sonar, Kolkata<br>14:00 hrs. on ' . $rowaccommodation['checkin_date'] . '<br>12:00 hrs. on ' . $rowaccommodation['checkout_date'] . '<br>' . $rowaccommodation['package_name'] . '</td>';
				$templateStringArray['DELEGATE_ACCOMMODATION_REQUEST'][]                 = '<td width="35%">' . $invAmt . '</td>';
				$templateStringArray['DELEGATE_ACCOMMODATION_REQUEST'][]                 = '<td width="35%">CANCELLED</td>';

				$templateStringArray['DELEGATE_ACCOMMODATION_REQUEST'][]                 = '</tr>';
			}
		}


		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '<tr>';

		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '<td width="35%" valign="top">Workshop Name</td>';
		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '<td>';

		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = implode("<br><br>", $breakast);
		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '<td width="35%">' . implode("<br><br><br>", $breakastAmt) . '</td>';
		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '<td width="35%">CANCELLED</td>';
		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '</td>';
		$templateStringArray['DELEGATE_BREAKFAST_REGISTRATION'][]                 = '</tr>';

		if ($acmponyStatus) {
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '<tr>';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '<td style="width:40%;" valign="top">Accompanying Person<br /><br />';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '</td>';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '<td valign="top">';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '<u >' . convert_number($acmponyCounter) . ' Person' . ($acmponyCounter > 1 ? 's' : '') . '</u>';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">' . $invAmt . '</td>';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '<td width="35%">CANCELLED</td>';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '</td>';
			$templateStringArray['ACCOMPANY_CONFERENCE_REGISTRATION'][]                 = '</tr>';
		}
	}
	$templateStringArray['FOOT'][]                 = '<td width="5%" colspan=2><b>Total Amount</b></td>';
	$templateStringArray['FOOT'][]                 .= '<td width="95%" colspan=2>' . number_format($totalAmt, 2) . '</td>';
	$templateStringArray['FOOT'][]                 .= '</table>';

	$sequence = array('HEAD', 'DELEGATE_RESIDENTIAL_REGISTRATION', 'DELEGATE_CONFERENCE_REGISTRATION', 'DELEGATE_BREAKFAST_REGISTRATION', 'DELEGATE_WORKSHOP_REGISTRATION', 'ACCOMPANY_CONFERENCE_REGISTRATION', 'DELEGATE_ACCOMMODATION_REQUEST', 'FOOT');

	$templateString = "";

	foreach ($sequence as $key) {
		foreach ($templateStringArray[$key] as $k => $row) {
			$templateString .= $row;
		}
	}


	return $templateString;
}

/*
	function registration_cancellation_confirmation_message($delegateId, $invoice_ids, $slipId, $operation='SEND')
	{	 
	
		global $mycms, $cfg;
		include_once('function.delegate.php');
		include_once('function.invoice.php');
		include_once('function.registration.php');
		include_once('function.delegate.php');
		 return false;  
		$invIds     =  implode(',',$invoice_ids);

		$sqlInvoice 	=	array();
		$sqlInvoice['QUERY'] = "SELECT * FROM  "._DB_INVOICE_." WHERE  `id` = ?";
		$sqlInvoice['PARAM'][]	=	array('FILD' => 'id' , 	  'DATA' => $invoice_ids ,             'TYP' => 's');	
		$resInvoice = $mycms->sql_select($sqlInvoice);	
		$status = false;
		foreach($resInvoice as $key=>$rowdetails)
		{
			
			if($rowdetails['service_type']=='DELEGATE_CONFERENCE_REGISTRATION')
			{
				$service_type ='Conference registration'; 
				$status = true;
			}
			elseif($rowdetails['service_type']=='DELEGATE_RESIDENTIAL_REGISTRATION')
			{
				$service_type = $cfg['RESIDENTIAL_NAME']; 
				$status = true;
			}
			elseif($rowdetails['service_type']=='ACCOMPANY_CONFERENCE_REGISTRATION')
			{
				$service_type ='Accompany registration'; 
			}
			elseif($rowdetails['service_type']=='DELEGATE_WORKSHOP_REGISTRATION')
			{
			
				$service_type ='Workshop registration'; 
			}
			elseif($rowdetails['service_type']=='DELEGATE_ACCOMMODATION_REQUEST')
			{
				$service_type ='Accommodation booking'; 
			}
		
		}
		$loginUrl     		   = $cfg['BASE_URL']."login.php";
		$rowFetchUserDetails   = getUserDetails($delegateId);
		
		$invoiceOrderSummary   = generateCancelInvoiceOrderSummary($delegateId, $invoice_ids);
		
	    // COMPOSING EMAIL
		$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= get_email_header();
		$message .= '<tr>';
		$message .= '	 <td align="left" valign="top">';
		$message .= '	 <strong>Dear '.$rowFetchUserDetails['user_full_name'].',</strong>';
		$message .= '	 <br /><br />';
		if($status)
		{
		$service_type ='Conference registration'; 
		$message .= '    As requested, '.$cfg['EMAIL_CONF_NAME'].' registration of <strong> '.$rowFetchUserDetails['user_full_name'].'</strong>  has been cancelled.';
		$message .= '    <br /><br />';
		$message .= '	You are not entitled for any other facility and service provided by the course.';
		}
		else
		{
			$message .= '    As requested,the '.$service_type.' of <strong> '.$rowFetchUserDetails['user_full_name'].'</strong> for '.$cfg['EMAIL_CONF_NAME'].' has been cancelled.';
		}
		$message .= '    <br /><br />';
		$message .= ' 	 Refunds will be initiated as per the Cancellation &amp; Refund Policy mentioned for respective services in the conference website.';
		$message .= ' 	 <br /><br />';
		
		$message .= $invoiceOrderSummary ;
		
		$message .= ' 	 </td>';
		$message .= ' </tr>';
		$message .= get_email_footer();
		$message .= '</table>';
		
		$subject  = "Registration and Payment Confirmation _".$cfg['EMAIL_CONF_NAME'] ;
		if($status)
		{
			$sms	 = "Your registration for ".$cfg['EMAIL_CONF_NAME']." has been cancelled.You are not entitled for any other facility and service provided by the course.";	
		}
		else
		{
			$sms	 = "Your ".$service_type." for ".$cfg['EMAIL_CONF_NAME']." has been cancelled.";	
		}
		
		if($operation=='SEND')
		{
			$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_REGISTRATION_EMAIL']);
			//	ADMIN_REGISTRATION_EMAIL
			
			$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms, 'Transaction');
			
			
			return true;
		}
		else if($operation=='RETURN_TEXT')
		{
			$array = array();
			$array['MAIL_SUBJECT'] = $subject;
			$array['MAIL_BODY'] = $message;
			$array['SMS_BODY'] = $sms;
			
			return $array;
		}
		else
		{
			return false;
		}
	}
	*/

function registration_cancellation_confirmation_message($delegateId, $invoice_ids, $slipId, $operation = 'SEND')
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	$invIds     =  implode(',', $invoice_ids);

	$sqlInvoice 	=	array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `id` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'id', 	  'DATA' => $invoice_ids,             'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$status = false;
	foreach ($resInvoice as $key => $rowdetails) {

		if ($rowdetails['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
			$service_type = 'Conference registration';
			$status = true;
		} elseif ($rowdetails['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
			$service_type = 'Residential registration';
			$status = true;
		} elseif ($rowdetails['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
			$service_type = 'Accompany registration';
		} elseif ($rowdetails['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {

			$service_type = 'Workshop registration';
		} elseif ($rowdetails['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
			$service_type = 'Accommodation booking';
		}
	}
	$loginUrl     		   = $cfg['BASE_URL'];
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$invoiceOrderSummary   = generateCancelInvoiceOrderSummary($delegateId, $invoice_ids);

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 <strong>Dear ' . $rowFetchUserDetails['user_full_name'] . ',</strong>';
	$message .= '	 <br /><br />';
	if ($status) {
		$service_type = 'Conference registration';
		$message .= '    As requested, ' . $cfg['EMAIL_CONF_NAME'] . ' registration of <strong> ' . $rowFetchUserDetails['user_full_name'] . '</strong>  has been cancelled.';
		$message .= '    <br /><br />';
		$message .= '	You are not entitled for any other facility and service provided by the course.';
	} else {
		$message .= '    As requested,the ' . $service_type . ' of <strong> ' . $rowFetchUserDetails['user_full_name'] . '</strong> for ' . $cfg['EMAIL_CONF_NAME'] . ' has been cancelled.';
	}
	$message .= '    <br /><br />';
	$message .= ' 	 Refunds will be initiated as per the Cancellation &amp; Refund Policy mentioned for respective services in the conference website.';
	$message .= ' 	 <br /><br />';

	$message .= $invoiceOrderSummary;

	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';
	$subject  = "Registration and Payment Confirmation _" . $cfg['EMAIL_CONF_NAME'];
	if ($status) {
		$sms	 = "Your registration for " . $cfg['EMAIL_CONF_NAME'] . " has been cancelled.You are not entitled for any other facility and service provided by the course.";
	} else {
		$sms	 = "Your " . $service_type . " for " . $cfg['EMAIL_CONF_NAME'] . " has been cancelled.";
	}

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_REGISTRATION_EMAIL']);
		//	ADMIN_REGISTRATION_EMAIL

		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms, 'Informative');
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $sms, "SEND",$status);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}

/******************************************************ONLINE MAIL*******************************************************************/
function online_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{

	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	$loginUrl     		   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);

	$sqlInvoice =	array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` =? AND `delegate_id` =? AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',    'DATA' => $slipId,           'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId,       'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',	 'DATA' => 'A',       		 'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$acmponyCounter = 0;
	$acmponyCounter = 0;
	foreach ($resInvoice as $keyInvoice => $rowInvoice) {
		if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails  = getUserDetails($rowInvoice['refference_id']);
			$sqlBanquet =	array();
			$sqlBanquet['QUERY'] = "SELECT * 
										  FROM  " . _DB_REQUEST_DINNER_ . " 
										 WHERE  `refference_id` =? 
										   AND `delegate_id` =? 
										   AND `status` = ?";
			$sqlBanquet['PARAM'][]   = array('FILD' => 'refference_id',     'DATA' => $accompanyDetails['id'],           'TYP' => 's');
			$sqlBanquet['PARAM'][]   = array('FILD' => 'delegate_id',   	'DATA' => $delegateId,           'TYP' => 's');
			$sqlBanquet['PARAM'][]   = array('FILD' => 'status',    		'DATA' => 'A',           'TYP' => 's');
			$resBanquet = $mycms->sql_select($sqlBanquet);
			$acmponyCounter++;

			$acmpany[]                       = $accompanyDetails['user_full_name'];
			if ($resBanquet) {
				$var = 'Taken';
			} else {
				$var = '-';
			}
			$Accompanymessage .= '			<tr><td><strong>' . $accompanyDetails['user_full_name'] . '</strong> </td><td> ' . $accompanyDetails['user_registration_id'] . ' </td></tr>';
		}
	}


	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br />';
	$message .= '			<br />';
	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '		Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your accompanying person(s).';
		$message .= '		<br /><br />';
	}
	$message .= '			Your Accompanying Person(s) is/are <strong>REGISTERED</strong> for  ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u>Registration Details:-</u>';
	$message .= '			<br /><br />';
	$message .= '      		<table border="0" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	//$message .= '			<tr><td><strong>Registered Accompanying Person</strong> </td><td width="35%" align="center"><strong> Registration Id </strong></td></tr>';
	//$message .= '         '.$Accompanymessage.'';
	$message .= '	 <tr><td><b>Registered Accompanying Person :</b></td><td>' . $accompanyDetails['user_full_name'] . '</td></tr>';
	$message .= '	 <tr><td><b>Registration Id :</b></td><td>' . $accompanyDetails['user_registration_id'] . '</td></tr>';
	$message .= '			</table><br />';
	$message .= '			<br /><br />';
	$message .= '			 <u><strong>Accompanying Persons are entitled for</strong></u>';
	$message .= '				<ul style="list-style-type:square;">';
	$message .= '					<li >Entry to Scientific Halls and Exhibition Area</li>';
	$message .= '				  	<li> Tea/Coffee during the Conference at the venue</li>';
	$message .= '				  	<li> Lunch on 5th, 6th and 7th September 2019.</li>';
	$message .= '				</ul>';
	$message .= '			<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
	$message .= '       	<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td>Receiver</td><td>' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
	$message .= '			<tr><td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
	$message .= '			<tr><td>Amount</td><td> ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
	$message .=	'			<tr><td>Mode of Payment</td><td> ' . $rowFetchPayment['payment_mode'] . '</td></tr>';
	$message .=	'			<tr><td>Transaction ID</td><td> ' . $rowFetchPayment['atom_atom_transaction_id'] . '</td></tr>';
	$message .= '			</table><br /><br />';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
											<td width="15%"><b>Invoice No.</b></td>
											<td width="70%"><b>Invoice for</b></td>
											<td width="20%" align="center"><b>Download</b></td>
									  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '			<br /><br />';
	}
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for accompanying person registration of " . $rowFetchUserDetails['user_full_name'] . ". Transaction Id " . $rowFetchPayment['atom_atom_transaction_id'] . ". Have a nice day.";
	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . " as accompanying person(s) of " . $rowFetchUserDetails['user_full_name'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}
		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function online_conference_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$loginUrl     		   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice		  =	array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',    		'DATA' => $slipId,       'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id',    	'DATA' => $delegateId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',    		'DATA' => 'A',   		'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {

		$sqlWorkshop		  =	array();
		$sqlWorkshop['QUERY'] = "SELECT request.*,classf.classification_title,classf.type,classf.workshop_date
									  FROM " . _DB_REQUEST_WORKSHOP_ . " request
								INNER JOIN " . _DB_WORKSHOP_CLASSIFICATION_ . " classf
										ON request.workshop_id = classf.id
									 WHERE  request.delegate_id = ? 
									   AND request.refference_slip_id = ? 
									   AND request.status = ?
									   AND classf.status = ?";
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.delegate_id',    		'DATA' => $delegateId,       'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.refference_slip_id',   'DATA' => $slipId,          	'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.status',   			'DATA' => 'A',           	'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'classf.status',    			'DATA' => 'A',           	'TYP' => 's');
		$resWorkshop = $mycms->sql_select($sqlWorkshop);

		$rowWorkshopDetails = $resWorkshop[0];
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br />';
	$message .= '			<br />';

	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '		Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your Workshop.';
		$message .= '		<br />Your workshop has been <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '.';
	}

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Workshop Details:-</strong></u>';
	$message .= '			<br /><br />';

	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td width="30%">Workshop Name:</td><td>' . $rowWorkshopDetails['classification_title'] . '<br></td></tr>';
	$message .= '			<tr><td width="30%">Date</td><td>' . $rowWorkshopDetails['workshop_date'] . '</td></tr>';
	$message .= '		</table>';
	$message .= '		<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td>Receiver</td><td> ' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
	$message .= '			<tr><td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
	$message .= '			<tr><td>Amount</td><td>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>'; //  
	$message .=	'			<tr><td>Mode of Payment</td><td> ' . $rowFetchPayment['payment_mode'] . '</td></tr>';
	$message .=	'			<tr><td>Transaction ID</td><td> ' . $rowFetchPayment['atom_atom_transaction_id'] . '</td></tr>';
	$message .= '			</table><br />';

	if (floatval($rowFetchPayment['amount']) > 0.00) {
		$message .= '		<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '		<br /><br />';
		$message .= '		<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			 <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
									 </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '		</table>';
		$message .= '		<br /><br />';
	}
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for Workshop registration of " . $rowFetchUserDetails['user_full_name'] . ". Transaction Id " . $rowFetchPayment['atom_atom_transaction_id'] . ". For details, please check the Registration Confirmation mail. Have a nice day.";
	$regsms	 = "Dear Delegate, you are now REGISTERED for " . $rowWorkshopDetails['classification_title'] . ". Your Unique Sequence:" . $rowFetchUserDetails['user_unique_sequence'] . " , Registered Email ID:" . $rowFetchUserDetails['user_email_id'] . ". For details, please check the Registration Confirmation mail. Invoice will be sent at registered mail id. Have a nice day.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']    = $message;
		$array['SMS_NO']	   = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0]  = $paysms;
		$array['SMS_BODY'][1]  = $regsms;

		return $array;
	} else {
		return false;
	}
}

function online_onlyworkshop_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$loginUrl     		   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice		  	   = array();
	$sqlInvoice['QUERY']   = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',    		'DATA' => $slipId,       'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id',    	'DATA' => $delegateId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',    		'DATA' => 'A',   		'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {

		$sqlWorkshop		  =	array();
		$sqlWorkshop['QUERY'] = "SELECT request.*,classf.classification_title,classf.type,classf.workshop_date
									  FROM " . _DB_REQUEST_WORKSHOP_ . " request
								INNER JOIN " . _DB_WORKSHOP_CLASSIFICATION_ . " classf
										ON request.workshop_id = classf.id
									 WHERE  request.delegate_id = ? 
									   AND request.refference_slip_id = ? 
									   AND request.status = ?
									   AND classf.status = ?";
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.delegate_id',    		'DATA' => $delegateId,       'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.refference_slip_id',   'DATA' => $slipId,          	'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.status',   			'DATA' => 'A',           	'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'classf.status',    			'DATA' => 'A',           	'TYP' => 's');
		$resWorkshop = $mycms->sql_select($sqlWorkshop);

		$rowWorkshopDetails = $resWorkshop[0];
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br />';
	$message .= '			<br />';

	$message .= '	 <strong>Welcome to the ' . $cfg['EMAIL_WELCOME_TO'] . ' which is to be held from ' . $cfg['EMAIL_CONF_HELD_FROM'] . ' at ' . $cfg['EMAIL_CONF_VENUE'] . '</strong>';
	$message .= '    <br /><br />';

	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '		Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your Workshop.';
		$message .= '		<br />You are <strong>REGISTERED</strong> for ' . $rowWorkshopDetails['classification_title'] . '.<br/><br/>';
		$message .= ' 		<u>Please note the following :-</u>';
		$message .= ' 		<br /><table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '		<tr><td>Your registered e-mail id is:</td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
		$message .= '		<tr><td>Your registered mobile number is: </td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
		$message .= '		<tr><td>Unique Sequence: </td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
		$message .= ' 	 	</table><br /><br />';
	}

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Workshop Details:-</strong></u>';
	$message .= '			<br /><br />';

	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td width="30%">Workshop Name:</td><td>' . $rowWorkshopDetails['classification_title'] . '<br></td></tr>';
	$message .= '			<tr><td width="30%">Date</td><td>' . $rowWorkshopDetails['workshop_date'] . '</td></tr>';
	$message .= '		</table>';
	$message .= '		<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td>Receiver</td><td> ' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
	$message .= '			<tr><td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
	$message .= '			<tr><td>Amount</td><td> ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
	$message .=	'			<tr><td>//Mode of Payment</td><td> ' . $rowFetchPayment['payment_mode'] . '</td></tr>';
	$message .=	'			<tr><td>Transaction ID</td><td> ' . $rowFetchPayment['atom_atom_transaction_id'] . '</td></tr>';
	$message .= '			</table><br />';

	if (floatval($rowFetchPayment['amount']) > 0.00) {
		$message .= '		<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '		<br /><br />';
		$message .= '		<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			 <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
									 </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '		</table>';
		$message .= '		<br /><br />';
	}

	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for Workshop registration of " . $rowFetchUserDetails['user_full_name'] . ". Transaction Id " . $rowFetchPayment['atom_atom_transaction_id'] . ". For details, please check the Registration Confirmation mail. Have a nice day.";
	$regsms	 = "Dear Delegate, " . $rowFetchUserDetails['user_full_name'] . " is now REGISTERED for " . $rowWorkshopDetails['classification_title'] . " Workshop in " . $cfg['EMAIL_CONF_NAME'] . ".";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']    = $message;
		$array['SMS_NO']	   = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0]  = $paysms;
		$array['SMS_BODY'][1]  = $regsms;

		return $array;
	} else {
		return false;
	}
}

function mailRegistrationDetails($delegateId)
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.dinner.php');
	$rowFetchUserDetails   = getUserDetails($delegateId);

	$msg = '';
	$msg .= '<table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$msg .= '	<tr><td>Your registered e-mail id is:</td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
	$msg .= '	<tr><td>Your registered mobile number is: </td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
	$msg .= '	<tr><td>Unique Sequence: </td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
	$msg .= '	<tr><td>Registration ID: </td><td>' . $rowFetchUserDetails['user_registration_id'] . '</td></tr>';

	$msg .= ' 	 </table>';

	return $msg;
}

function mailPaymentDetails($delegateId,  $paymentId)
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.dinner.php');

	$rowFetchPayment 	   = getPaymentDetails($paymentId);

	$sqlSlip 			   = array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlSlip['PARAM'][]    = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]    = array('FILD' => 'id',   	'DATA' => $rowFetchPayment['slip_id'],   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$rowaSlip              = $resSlip[0];

	$paymentStatus = ($rowFetchPayment['payment_status'] == 'UNPAID') ? 'Pending' : 'Paid';
	$paymentModeDisplay = $rowFetchPayment['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($rowFetchPayment['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $rowFetchPayment['payment_mode']);
	$msg = '<tr><td colspan="2">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr style="background: #' . $cfg['color'] . ';">
					<td style="text-align: left;padding-left: 30px;">
						<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Voucher Number</p>
					</td>
					<td style="text-align: left;">
						<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $rowaSlip['slip_number'] . '</p>
					</td>
				</tr>
				<tr>
					<td style="text-align: left;padding-left: 30px;">
						<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Receiver</p>
					</td>
					<td style="text-align: left;">
						<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $cfg['EMAIL_CONF_NAME'] . '</p>
					</td>
				</tr>
				<tr style="background: #' . $cfg['color'] . ';">
					<td style="text-align: left;padding-left: 30px;">
						<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Date </p>
					</td>
					<td style="text-align: left;">
						<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . date("d-m-Y", strtotime($rowFetchPayment['payment_date'])) . '</p>
					</td>
				</tr>
				<tr>
					<td style="text-align: left;padding-left: 30px;">
						<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Amount </p>
					</td>
					<td style="text-align: left;">
						<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</p>
					</td>
				</tr>
				<tr style="background: #' . $cfg['color'] . ';">
					<td style="text-align: left;padding-left: 30px;">
						<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Mode</p>
					</td>
					<td style="text-align: left;">
						<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $paymentModeDisplay . '</p>
					</td>
				</tr>
				<tr>
					<td style="text-align: left;padding-left: 30px;">
						<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Status</p>
					</td>
					<td style="text-align: left;">
						<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $rowFetchPayment['payment_status'] . '</p>
					</td>
				</tr>
				</tbody>
			</table>
		</td></tr>';

	return $msg;
}

function mailInvoiceDetails($delegateId, $slipId)
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.dinner.php');


	$message = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 30px 30px 60px;">
				<tbody>';
	$message .= getInvoiceMailerDetails($delegateId, $slipId);
	$message .= ' </tbody>
		</table>';


	return $message;
}

function online_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{

	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.dinner.php');

	$loginUrl     		   = _BASE_URL_;

	$rowFetchUserDetails   = getUserDetails($delegateId);

	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);

	$rowFetchPayment 	   = getPaymentDetails($paymentId);


	$sqlSlip 			   = array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlSlip['PARAM'][]    = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]    = array('FILD' => 'id',   	'DATA' => $rowFetchPayment['slip_id'],   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$rowaSlip              = $resSlip[0];


	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlaccommodation 	   = array();
	$sqlaccommodation['QUERY'] 	   = "SELECT * 
											FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
									       WHERE status = ? 
										    AND user_id = ? ";
	$sqlaccommodation['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  'TYP' => 's');
	$sqlaccommodation['PARAM'][]   = array('FILD' => 'user_id',   'DATA' => $delegateId,   'TYP' => 's');
	$resaccom			   = $mycms->sql_select($sqlaccommodation);
	$rowaccomm             = $resaccom[0];



	// $mailRegDetails = mailRegistrationDetails($delegateId);

	$mailPaymentDetails = mailPaymentDetails($delegateId,  $paymentId);

	$mailInvoiceDetails = mailInvoiceDetails($delegateId, $slipId);

	// echo "<pre>"; 
	// print_r($mailPaymentDetails);
	// echo "</pre><pre>"; 

	// print_r($mailInvoiceDetails);
	// echo "</pre>";

	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM " . _DB_EMAIL_TEMPLATE_ . " 
					       WHERE status = ? 
						     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 3,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row = $result[0];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
										WHERE `title` = 'Payment Successful'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];


	$logo = '<a href="#" target="_blank" text-decoration: none; border: 0;"><img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" alt="logo" width="310" height="" style="display: block; border: 0;" /></a>';
	// $img = _BASE_URL_ . 'images/payment-success.png';

	$line = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1 = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2 = _BASE_URL_ . 'images/mailer/line-after.png';
	$reg_link = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';


	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);
	$find = [
		'[LOGO]', '[UNIQUE_ID]', '[username]', '[IMG]', '[REG_ID]', '[MAIL]', '[MOBILE]',
		'[payment_details]', '[invoice_details]', '[amount]',
		'[inr]', '[invoice_order_details]','[LINE]', '[LINE1]', '[LINE2]', '[REG_LINK]',
		'[CONF_EMAIL]', '[CONF_MOBILE]', '[FOOTER_IMG]', 'cdf0c2', '317031', '80ec91', '053426'
	];

	$replacement = [
		$logo, $rowFetchUserDetails['user_unique_sequence'], $rowFetchUserDetails['user_full_name'], $img,
		$rowFetchUserDetails['user_registration_id'], $rowFetchUserDetails['user_email_id'], $rowFetchUserDetails['user_mobile_no'],
		$mailPaymentDetails,
		$mailInvoiceDetails, number_format($financialSummaryOfSlip['AMOUNT'], 2), $currr, $invoiceOrderSummary,
		$line,$line1, $line2, $reg_link, $cfg['EMAIL_CONF_EMAIL_US'], $cfg['EMAIL_CONF_CONTACT_US'],
		$footer_img, $cfg['light_color'], $cfg['dark_color'], $cfg['color'], $cfg['dark_color']
	];

	// $find = ['[username]', '[inr]', '[amount]', '[registration_details]', '[registration_entitled]', '[payment_details]', '[invoice_details]'];

	// $replacement = [$rowFetchUserDetails['user_full_name'],getRegistrationCurrency(getUserClassificationId($delegateId)),number_format($rowFetchPayment['amount'],2), $mailRegDetails,$invoiceOrderSummary,$mailPaymentDetails,$mailInvoiceDetails];


	$result = str_replace($find, $replacement, $mailTemplateDescription);


	$message = $result;

	// COMPOSING EMAIL

	$subject  = $rowaMail['subject'];

	$payRegSms	= "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received the payment of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . " for the registration of " . $rowFetchUserDetails['user_full_name'] . ". You are now SUCCESSFULLY REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence:" . $rowFetchUserDetails['user_unique_sequence'] . " , Registration ID:" . $rowFetchUserDetails['user_registration_id'] . " , Registered Email ID:" . $rowFetchUserDetails['user_email_id'] . ". For details, please check the Registration Confirmation mail. Invoice will be sent at registered mail id. Have a nice day.";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $payRegSms);
		}
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $payRegSms;
		return $array;
	} else {
		return false;
	}
}


function online_conference_registration_certificate_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{

	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.dinner.php');

	$loginUrl     		   = _BASE_URL_;

	$rowFetchUserDetails   = getUserDetails($delegateId);

	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);

	$rowFetchPayment 	   = getPaymentDetails($paymentId);

	$sqlSlip 			   = array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlSlip['PARAM'][]    = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]    = array('FILD' => 'id',   	'DATA' => $rowFetchPayment['slip_id'],   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$rowaSlip              = $resSlip[0];


	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlaccommodation 	   = array();
	$sqlaccommodation['QUERY'] 	   = "SELECT * 
											FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
									       WHERE status = ? 
										    AND user_id = ? ";
	$sqlaccommodation['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  'TYP' => 's');
	$sqlaccommodation['PARAM'][]   = array('FILD' => 'user_id',   'DATA' => $delegateId,   'TYP' => 's');
	$resaccom			   = $mycms->sql_select($sqlaccommodation);
	$rowaccomm             = $resaccom[0];

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';
	$message .= '	 <strong>Thanks a lot for making NEOCON 2022 Kolkata a stellar success.</strong>';
	$message .= '    <br /><br />';

	$message .= 'Kindly find the attached your conference participation certificate.';
	$message .= '   <br /><br />';


	$message .= '<strong><u>CERTIFICATE DETAILS</u></strong>';
	$message .= '<br /><br />';

	$message .= '			<table style="font-family: Arial, Helvetica, sans-serif;font-size: 14px;border: 1px solid #000;  width: 90%; margin: auto; border-collapse:collapse">';
	$message .= '			  <tr>	
										<td style="width:30%; border-right:1px solid #000; border-bottom:1px solid #000;">Participation Certificate</td>
										<td style="width:45%; border-right:1px solid #000; border-bottom:1px solid #000;"><strong>DOWNLOAD: &nbsp;&nbsp;</strong> <a href="' . _BASE_URL_ . 'pdf.download.certificate.php?user_id=' . $delegateId . '" target="_blank"><img src="' . _BASE_URL_ . 'images/download.png" alt="download"/></a></td>
										
								  </tr></table>';




	$message .= '			<br /><br />';



	$intArr	 = array(5, 6, 7, 10, 11, 12); // Combo Ids

	$message .= '			For any assistance or query, please contact us at ' . $cfg['EMAIL_CONF_CONTACT_US'] . ' or mail us at ' . $cfg['EMAIL_CONF_EMAIL_US'];
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_certificate_footer();
	$message .= '</table>';
	$subject  = "Participation Certificate _ NEOCON 2022";

	$payRegSms	= "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received the payment of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . " for the registration of " . $rowFetchUserDetails['user_full_name'] . ". You are now SUCCESSFULLY REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence:" . $rowFetchUserDetails['user_unique_sequence'] . " , Registration ID:" . $rowFetchUserDetails['user_registration_id'] . " , Registered Email ID:" . $rowFetchUserDetails['user_email_id'] . ". For details, please check the Registration Confirmation mail. Invoice will be sent at registered mail id. Have a nice day.";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		if (floatval($rowFetchPayment['amount']) > 0) {
			//$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $payRegSms);			
		}
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $payRegSms;
		return $array;
	} else {
		return false;
	}
}

function online_accommodation_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $mycms, $cfg;
	return false;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	return false;
	$loginUrl     		   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);

	$sqlaccommodationDetails = array();
	$sqlaccommodationDetails['QUERY']  = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,
	   										masterHotel.hotel_address AS hotel_address,package.package_name
												 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
													INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
														ON accommodation.package_id = package.id
													INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
														ON masterHotel.id = package.hotel_id
											   WHERE accommodation.status = ? 
												 AND accommodation.user_id = ?";

	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',   'DATA' => 'A',   		  'TYP' => 's');
	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',   'DATA' => $delegateId,   		  'TYP' => 's');

	$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
	$rowaccomm             = $resaccommodation[0];
	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';
	$message .= '    Payment <strong>SUCCESSFUL.</strong> ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . ' for your accommodation booking at ' . $rowaccomm['hotel_name'] . ', ' . $rowaccomm['hotel_address'] . '.';
	$message .= '    <br /><br />';
	$message .= '    Reservation <strong> CONFIRMED</strong>. You have booked your stay at ' . $rowaccomm['hotel_name'] . ', ' . $rowaccomm['hotel_address'] . ' for attending ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';
	$message .= '    <br /><br />';
	$message .= '	 <u><b>RESERVATION DETAILS</b></u>';
	$message .= ' 	<br /><br />';
	$message .= 	$invoiceOrderSummary;


	$message .= '			<br /><br />';
	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '		<u><strong>TRANSACTION DETAILS</strong></u>';
		$message .= '		<br /><br />';
		$message .=	'		Payment has been done through online process.';
		$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '		<tr><td>Receiver</td><td>  ' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
		$message .= '		<tr><td>Transaction Date</td><td>' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
		$message .=	'		<tr><td>Amount</td><td>  ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
		$message .=	'		<tr><td>Mode of Payment</td><td> ' . $rowFetchPayment['payment_mode'] . '</td></tr>';
		$message .=	'		<tr><td>Transaction ID</td><td>' . $rowFetchPayment['atom_atom_transaction_id'] . '</td></tr>';
		$message .= '		</table><br /><br />';
	}

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
											<td width="15%"><b>Invoice No.</b></td>
											<td width="70%"><b>Invoice for</b></td>
											<td width="20%" align="center"><b>Download</b></td>
									  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '			<br /><br />';
	}

	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<br/><br /><a href="' . $loginUrl . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . '  user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong><u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u></strong></u>';
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Stay Booking Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$paysms  = "Transaction SUCCESSFUL " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . "." . number_format($rowFetchPayment['amount'], 2) . " to book a stay for " . $rowaccomm['hotel_name'] . " Transaction ID " . $rowFetchPayment['atom_atom_transaction_id'] . " Have a nice day.";
	$regsms = "Your reservation at " . $rowaccomm['hotel_name'] . ", " . $rowaccomm['hotel_address'] . " is CONFIRMED. Check-in 14:00 hrs. " . $rowaccomm['checkin_date'] . ", Check-out 12:00 hrs. on " . $rowaccomm['checkout_date'] . ".";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}
		$regstatus = $mycsm->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']    = $message;
		$array['SMS_NO']       = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0]  = $paysms;
		$array['SMS_BODY'][1]  = $regsms;
		return $array;
	} else {
		return false;
	}
}

function online_conference_payment_failure_message($delegateId, $operation = 'SEND')
{
	global $mycms;
	$sqlSlip 	=	array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  `delegate_id` = ? ";
	$sqlSlip['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'delegate_id',   	'DATA' => $delegateId,   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$slipId              = $resSlip[0]['id'];
	$rowFetchUserDetails   = getUserDetails($delegateId);

	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);

	$rowFetchPayment 	   = getPaymentDetails($paymentId);

	$voucherLink = _BASE_URL_ . "downloadSlippdf.php?delegateId=" . $mycms->encoded($delegateId) . "&slipId=" . $mycms->encoded($slipId);
	// $mailPaymentDetails = mailPaymentDetails($delegateId,  $paymentId);
	// $mailInvoiceDetails = mailInvoiceDetails($delegateId, $slipId);

	$offlineConferencePaymentDetails = onlinePaymentFailedPaymentDetails($delegateId, $slipId);
	$offlineConferenceInvoiceDetails = offlineConferenceInvoiceDetails($delegateId, $slipId);

	$offlineConferenceRegistrationDetails = offlineConferenceRegistrationDetails($delegateId);
	$financialSummaryOfSlip = getFinancialSummaryOfSlip($slipId);

	$currr 		= getRegistrationCurrency(getUserClassificationId($delegateId));

	global $cfg, $mycms;

	$rowFetchUserDetails   	= getUserDetails($delegateId);

	$retryLink          	= _BASE_URL_;

	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM " . _DB_EMAIL_TEMPLATE_ . " 
					       WHERE status = ? 
						     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 6,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row = $result[0];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
										WHERE `title` = 'Payment Failed'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

	
	$logo = '<a href="#" target="_blank" text-decoration: none; border: 0;"><img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" alt="logo" width="310" height="" style="display: block; border: 0;" /></a>';
	// $img = _BASE_URL_ . 'images/mailer/payment-failed.png';

	$line = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1 = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2 = _BASE_URL_ . 'images/mailer/line-after.png';
	$reg_link = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';

	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);

	$find = [
		'[LOGO]', '[USERNAME]', '[IMG]', '[MAIL]', '[MOBILE]',
		'[registration_details]', '[invoice_details]', '[payment_details]',  '[amount]',
		'[inr]', '[invoice_order_details]', '[LINE]', '[LINE1]', '[LINE2]', '[VOUCHER_LINK]', '[REG_LINK]',
		'[CONF_EMAIL]', '[CONF_MOBILE]', '[FOOTER_IMG]', 'fedad2', '80ec91', '053426'
	];

	$replacement = [
		$logo, $rowFetchUserDetails['user_full_name'], $img,
		$rowFetchUserDetails['user_email_id'], $rowFetchUserDetails['user_mobile_no'],
		$offlineConferenceRegistrationDetails, $offlineConferenceInvoiceDetails, $offlineConferencePaymentDetails,
		number_format($financialSummaryOfSlip['AMOUNT'], 2), $currr, $invoiceOrderSummary, $line,
		$line1, $line2, $voucherLink, $reg_link, $cfg['EMAIL_CONF_EMAIL_US'], $cfg['EMAIL_CONF_CONTACT_US'],
		$footer_img, $cfg['light_color'], $cfg['color'], $cfg['dark_color']
	];

	// echo "<pre>";print_r($mailInvoiceDetails);
	// print_r($offlineConferencePaymentDetails);
	$result = str_replace($find, $replacement, $mailTemplateDescription);

	// $message = '';

	// $message .= '<img src="' . $emailHeader . ' " width="100%" alt="header" /><br/><br/>';
	$message = $result;
	// $message .= '<img src="' . $emailFooter . ' " width="100%" alt="Footer" /><br/><br/>';

	/*$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">';
		$message .= get_email_header();
		$message .= '	<tr>';
		$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
		$message .= '	</tr>';
		$message .= '	<tr>';
		$message .= '		<td align="left" valign="top">';
		$message .= '			<strong>Dear '.$rowFetchUserDetails['user_full_name'].',</strong>';
		$message .= '			<br /><br />';
		$message .= '			TRANSACTION UNSUCCESSFUL.The payment procedure for '.$cfg['EMAIL_CONF_NAME'].' registration has failed. ';
		$message .= '			<br /><br />';
		$message .= '			Please <a href="'.$retryLink.'" target="_blank">click here</a> to retry. ';
		$message .= '			<br /><br />';
		$message .= '			<br /><br />';
		$message .= '		</td>';
		$message .= '	</tr>';
		$message .= get_email_footer();
		$message .= '</table>';*/

	$subject  = $rowaMail['subject'];

	$sms	  = "Transaction UNSUCCESSFUL. The payment procedure for " . $cfg['EMAIL_CONF_NAME'] . " registration has failed. Please try again.";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms);
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $sms;

		return $array;
	} else {
		return false;
	}
}

function online_dinner_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	return false;
	$loginUrl          	= _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);

	$sqlInvoice 			=	array();
	$sqlInvoice['QUERY']    = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	    'DATA' => $slipId,         'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'delegate_id', 	'DATA' => $delegateId,     'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'status', 	  		'DATA' => 'A',             'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
		$sqlDinner 			=	array();
		$sqlDinner['QUERY'] = "SELECT request.*,user.user_full_name
									 FROM  " . _DB_REQUEST_DINNER_ . " request
							  INNER JOIN " . _DB_USER_REGISTRATION_ . " user
									  ON request.refference_id = user.id
								   WHERE request.delegate_id =? 
									 AND request.refference_slip_id = ? 
									 AND request.status = ?
									 AND user.status = ?
									 AND user.registration_payment_status != ?
									 AND request.payment_status != ?";
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.delegate_id', 	    	'DATA' => $delegateId,    'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.refference_slip_id',  	'DATA' => $slipId,        'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.status', 	    			'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.status', 	   				'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.registration_payment_status', 'DATA' => 'UNPAID',       'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.payment_status', 	    	'DATA' => 'UNPAID',       'TYP' => 's');
		$resDinner = $mycms->sql_select($sqlDinner);
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr use="canvas"><td align="left" valign="top">';
	$message .= '		Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '		<br />';
	$message .= '		<br />';
	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '		Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your banquet dinner.';
	}
	$message .= '		<br />Your dinner has been <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'];
	$message .= '		<br /><br />';
	$message .= '		Please save this e-mail for further reference.';
	$message .= '		<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Dinner Details:-</strong></u>';
	$message .= '		<br /><br />';

	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td width="30%">Date:</td><td>' . $cfg['BANQUET_DINNER_DATE'] . '</td></tr>';

	$dinnercnt = 0;
	$acmponyCounter = 0;
	foreach ($resDinner as $key => $rowDinner) {
		if ($dinnercnt > 0) {
			$message .= '	<tr><td>' . $rowDinner['user_full_name'] . '</td></tr>';
			$acmponyCounter++;

			$acmpany[] = $rowDinner['user_full_name'];
		} else {
			$message .= '	<tr><td rowspan="' . sizeof($resDinner) . '" width="30%">Guest(s):</td><td>' . $rowDinner['user_full_name'] . '</td></tr>';
			$acmponyCounter++;

			$acmpany[]  = $rowDinner['user_full_name'];
		}
		$dinnercnt++;
	}
	$message .= '		</table>';

	$message .= '		<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr>  <td>Receiver</td><td> "' . $cfg['EMAIL_CONF_NAME'] . '"</td></tr>';
	$message .= '			<tr>  <td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
	$message .=	'			<tr>  <td>Amount ' . ($offlineFrom == 'BACK' ? '(Discounted)' : '') . ' : </td> <td>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
	$message .=	'			<tr>  <td>Mode of Payment :</td> <td>' . $rowFetchPayment['payment_mode'] . '</td></tr> ';
	$message .=	'			<tr>  <td>Transaction ID</td><td>' . $rowFetchPayment['atom_atom_transaction_id'] . '</td></tr>';
	$message .= '		</table>';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '	<br /><br />';
		$message .= '   <table border="0" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .=	'		<tr> <td><strong><u>INVOICE DETAILS</u></strong></td></tr> ';
		$message .= '	</table>';
		$message .= '	<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '		<tr>	
									<td width="15%"><b>Invoice No.</b></td>
									<td width="70%"><b>Invoice for</b></td>
									<td width="20%" align="center"><b>Download</b></td>
								</tr>';
		$message .= 		getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '	</table>';
	}

	$message .= '		<br /><br />';
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '		<br /><a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '		<br /><br/>';
	$message .= '		<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '		<br /><br/>';
	$message .= '		<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_EMAIL_US'] . ' 2019 user account.';
	$message .= '		<br /><br/>';
	$message .= '		For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '		<br /><br/>';
	$message .= '	</td></tr>';

	$message .= get_email_footer();
	$message .= '	</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for banquet dinner registration. Transaction Id " . $rowFetchPayment['atom_atom_transaction_id'] . ". Have a nice day.";

	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " now REGISTERED for banquet dinner of " . $cfg['EMAIL_CONF_NAME'] . " on " . $cfg['BANQUET_DINNER_DATE'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['EMAIL_CONF_EMAIL_US']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}
		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);


		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		/*
			$array['SMS_BODY1'] = $paysms;
			$array['SMS_BODY2'] = $regsms;
			*/
		return $array;
	} else {
		return false;
	}
}

/******************************************************OFFLINE MAIL*******************************************************************/
function offline_registration_acknowledgement_transaction($delegateId, $slipId, $paymentId)
{
	global $mycms, $cfg;
	$rowFetchPayment = getPaymentDetails($paymentId);
	$paymentModeDisplay = $rowFetchPayment['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($rowFetchPayment['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $rowFetchPayment['payment_mode']);

	$message = '';

	$message .=	'<span>You have selected offline payment procedure.</span>';
	$message .=	'<span style="float:right; font-weight:bold;">Download Payment Voucher <a href="' . _BASE_URL_ . 'downloadSlippdf.php?delegateId=' . $mycms->encoded($delegateId) . '&slipId=' . $mycms->encoded($slipId) . '"><img src="' . _BASE_URL_ . 'images/download.png" alt="download"/></a></span>';
	$message .=	'	<br />';
	$message .= '   <table align="left" style="font-family: Arial, Helvetica, sans-serif;font-size: 14px;border: 0px solid #000;  width: 100%; border-collapse:collapse" border="0">';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Payment Status </td><td style="border: 1px solid #000; width:50%;"> Pending</td></tr>';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Receiver </td><td style="border: 1px solid #000; width:50%;"> ' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Date </td><td style="border: 1px solid #000; width:50%;">';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cash":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '';
			break;

		case "Cheque":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '';
			break;

		case "Draft":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '';
			break;

		case "NEFT":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '';
			break;

		case "RTGS":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '';
			break;

		case "UPI":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['upi_date'])) . '';
			break;
	}
	$message .= '		</td></tr>';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Amount</td><td style="border: 1px solid #000; width:50%;"> ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Mode of Payment </td><td style="border: 1px solid #000; width:50%;"> ' . $paymentModeDisplay . '</td></tr>';

	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$message .= '		<tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">Cheque No. :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['cheque_number'] . '</td>';
			$message .= '		</tr>';
			$message .= '	    <tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">Drawee Bank :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['cheque_bank_name'] . '</td>';
			$message .= '		</tr>';
			break;
		case "Draft":
			$message .= '		<tr>';
			$message .= '		   	<td style="border: 1px solid #000; width:50%;">DD No. :</td>';
			$message .= '		 	<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['draft_number'] . '</td>';
			$message .= '       </tr>';
			$message .= '	    <tr>';
			$message .= '		  	<td style="border: 1px solid #000; width:50%;">Drawee Bank :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['draft_bank_name'] . '</td>';
			$message .= '		</tr>';
			break;
		case "NEFT":
			$message .= '		<tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">NEFT Transaction Id :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['neft_transaction_no'] . '</td>';
			$message .= '		</tr>';
			$message .= '	    <tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">Drawee Bank :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['neft_bank_name'] . '</td>';
			$message .= '		</tr>';
			break;
		case "RTGS":
			$message .= '		<tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">RTGS Transaction Id :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['rtgs_transaction_no'] . '</td>';
			$message .= '		</tr>';
			$message .= '	    <tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">Drawee Bank :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['rtgs_bank_name'] . '</td>';
			$message .= '		</tr>';
			break;

		case "UPI":
			$message .= '		<tr>';
			$message .= '			<td style="border: 1px solid #000; width:50%;"> Transaction Id :</td>';
			$message .= '			<td style="border: 1px solid #000; width:50%;">' . $rowFetchPayment['txn_no'] . '</td>';
			$message .= '		</tr>';

			break;
	}

	$message .= '	</table>';
	$message .= '	<br />';

	return $message;
}

function offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, $operation = 'SEND')
{

	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	include_once('function.workshop.php');

	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$intArr	 		       = array(); // Combo Ids
	$delagateCatagory      = getUserClassificationId($delegateId);

	$sqlaccommodation 	   = array();
	$sqlaccommodation['QUERY'] 	   = "SELECT * FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
												   WHERE status = ? 
													 AND user_id = ?";
	$sqlaccommodation['PARAM'][]	=	array('FILD' => 'status', 	      'DATA' => 'A',     	   'TYP' => 's');
	$sqlaccommodation['PARAM'][]	=	array('FILD' => 'user_id', 	  'DATA' => $delegateId,   'TYP' => 's');
	$resaccom			   			= $mycms->sql_select($sqlaccommodation);
	$rowaccomm             			= $resaccom[0];
	$conference_registration_Flag 	= false;
	$workshop_registration_Flag 	= false;
	$accompany_registration_Flag 	= false;

	$sqlInvoice 		 	= array();
	$sqlInvoice['QUERY'] 	= " SELECT * FROM  " . _DB_INVOICE_ . " 
									 WHERE  `slip_id` =? 
									   AND `delegate_id` = ? 
									   AND `status` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	      'DATA' => $slipId,     	   'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'delegate_id', 	  'DATA' => $delegateId,      'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'status', 	      	  'DATA' => 'A',     		   'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	foreach ($resInvoice as $key => $resAcc) {
		if ($resAcc['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
			$conference_registration_Flag = true;
		}
		if ($resAcc['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workshop_registration_Flag = true;
		}
		if ($resAcc['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompany_registration_Flag = true;
		}
		if ($resAcc['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$delegate_dinner_Flag = true;
		}
	}
	$offlineConferencePaymentDetails = offlineConferencePaymentDetails($delegateId, $paymentId, $slipId);

	$offlineConferenceInvoiceDetails = offlineConferenceInvoiceDetails($delegateId, $slipId);
	$offline_registration_transaction = offline_registration_acknowledgement_transaction($delegateId, $slipId, $paymentId);
	$voucherLink = _BASE_URL_ . "downloadSlippdf.php?delegateId=" . $mycms->encoded($delegateId) . "&slipId=" . $mycms->encoded($slipId);


	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_EMAIL_TEMPLATE_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 2,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
										WHERE `title` = 'Payment Pending'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

	$logo = '<a href="#" target="_blank" text-decoration: none; border: 0;"><img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" alt="logo" width="310" height="" style="display: block; border: 0;" /></a>';

	// $img = _BASE_URL_ . 'images/mailer/payment-pending.png';
	$line = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1 = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2 = _BASE_URL_ . 'images/mailer/line-after.png';
	$reg_link = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';

	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);
	$find = [
		'[LOGO]', '[IMG]', '[USERNAME]', '[invoice_order_details]', '[payment_details]', '[invoice_details]', '[VOUCHER_LINK]',
		'[transaction_details]', 'fff9ae', '850d12', 'f8ed62','[LINE]', '[LINE1]', '[LINE2]', '[REG_LINK]',
		'[CONF_EMAIL]', '[CONF_MOBILE]', '[FOOTER_IMG]'
	];

	$replacement = [
		$logo, $img, $rowFetchUserDetails['user_full_name'], $invoiceOrderSummary, $offlineConferencePaymentDetails,
		$offlineConferenceInvoiceDetails, $voucherLink, $offline_registration_transaction,
		$cfg['light_color'], $cfg['dark_color'], $cfg['color'], $line,$line1, $line2, $reg_link, $cfg['EMAIL_CONF_EMAIL_US'], $cfg['EMAIL_CONF_CONTACT_US'],
	];


	$result = str_replace($find, $replacement, $mailTemplateDescription);

	$message = '';

	// $message .= '<img src="' . $emailHeader . ' " width="100%" alt="header" /><br/><br/>';
	$message = $result;
	// $message .= '<img src="' . $emailFooter . ' " width="100%" alt="header" /><br/><br/>';

	// COMPOSING EMAIL
	/*$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= get_email_header();
		$message .= '<tr>';
		$message .= '	 <td align="left" valign="top">';
		$message .= '	 Dear '.$rowFetchUserDetails['user_full_name'].',';
		$message .= '	 <br /><br />';
		$message .= '	 <b>Welcome to the '.$cfg['EMAIL_WELCOME_TO'].' ('.$cfg['EMAIL_CONF_NAME'].') which is to be held from '.$cfg['EMAIL_CONF_HELD_FROM'].' at '.$cfg['EMAIL_CONF_VENUE'].'. Your data has been received successfully. </b>';
		$message .= '    <br /><br />';
		
		if($conference_registration_Flag)
		{
			
	
			$message .= '	 <b><u>REGISTRATION DETAILS</u></b>';
		
		}		
		elseif($workshop_registration_Flag || $accompany_registration_Flag || $delegate_dinner_Flag)
		{
			$message .= '	 <b><u>REGISTRATION DETAILS</u></b>';
		}
		
		$message .= ' 	 <br /><br />';
		$message .= 	$invoiceOrderSummary;		
		$message .= ' 	 <br /><span>&nbsp;</span><br />';
		
		$message .=	'	<b><u>TRANSACTION DETAILS:</u></b>';

		$message .=$offline_registration_transaction;
				
		if($offlineFrom !="BACK")
		{

			$message .= '	<b>NOTE - Post your cash/DD/cheque</b>  or <b>email your RTGS/NEFT receipt</b> with the <b>Payment Voucher</b> to the '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat within 10 working days.';   
			$message .= '	<br /><br />';
		}
		
		$message .= get_office_address();
		
		if($offlineFrom=='FRONT')
		{
			$message .= '		<br /><br />';
			$message .= '		You will receive a Confirmation mail after the realisation of your payment.<br>';
		}
		
		$message .= '		<br>Registration confirmation will be mailed to your registered e-mail id after the realisation of payment. In case, the payment procedure fails, please contact '.$cfg['EMAIL_CONF_NAME']	.' Registration Secretariat.<br /><br />';
		$message .= '		For any assistance or query, please contact us at '.$cfg['EMAIL_CONF_CONTACT_US'].' or mail us at <u>'.$cfg['EMAIL_CONF_EMAIL_US'].'</u><br /><br />';
		$message .= ' 	 </td>';
		$message .= ' </tr>';
		$message .= get_email_footer();
		$message .= '</table>';*/

	$subject  = $rowaMail['subject'];
	switch ($rowFetchPayment['payment_mode']) {

		case "Cheque":
			$paymentType = "Cheque";
			$paymentPayment  = $rowFetchPayment['cheque_number'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your Cheque number is " . $rowFetchPayment['cheque_number'] . " for the amount of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . ". " . $rowFetchPayment['amount'] . ". Download your Payment Voucher and send it to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "Draft":
			$paymentType = "Draft";
			$paymentPayment = $rowFetchPayment['draft_number'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your DD number is " . $rowFetchPayment['draft_number'] . " for the amount of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . ". " . $rowFetchPayment['amount'] . ". Download your Payment Voucher and send it to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "NEFT":
			$paymentType = "NEFT";
			$paymentPayment = $rowFetchPayment['neft_transaction_no'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your NEFT number is " . $rowFetchPayment['neft_transaction_no'] . " for the amount of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . ". " . $rowFetchPayment['amount'] . ". Download your Payment Voucher and send it to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "RTGS":
			$paymentType = "RTGS";
			$paymentPayment = $rowFetchPayment['rtgs_transaction_no'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your RTGS number is " . $rowFetchPayment['rtgs_transaction_no'] . " for the amount of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . ". " . $rowFetchPayment['amount'] . ". Download your Payment Voucher and send it to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "Cash":
			$paymentType = "Cash";
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your payable amount is " . getRegistrationCurrency(getUserClassificationId($delegateId)) . ". " . $rowFetchPayment['amount'] . " in cash. Download your Payment Voucher and send it to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realisation of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;
	}


	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paymentSms, 'Informative');
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'] = $paymentSms;
		return $array;
	} else {
		return false;
	}
}

function residential_registration_acknowledgement_message($delegateId, $slipId, $paymentId, $operation = 'SEND', $offlineFrom = 'FRONT')
{
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$loginUrl          		= _BASE_URL_;
	$rowFetchUserDetails   	= getUserDetails($delegateId);
	$rowFetchPayment 	   	= getPaymentDetails($paymentId);
	$invoiceOrderSummary  	= generateInvoiceOrderSummary($delegateId, $slipId);

	$intArr	 		       	= array(); // Combo Ids
	$delagateCatagory      	= getUserClassificationId($delegateId);

	$sqlaccommodation 				= array();
	$sqlaccommodation['QUERY'] 	   	= "SELECT * FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
										    WHERE status = ? 
											  AND user_id = ? ";

	$sqlaccommodation['PARAM'][]	=	array('FILD' => 'status', 	      'DATA' => 'A',     	   'TYP' => 's');
	$sqlaccommodation['PARAM'][]	=	array('FILD' => 'user_id', 	  'DATA' => $delegateId,   'TYP' => 's');

	$resaccom			   = $mycms->sql_select($sqlaccommodation);
	$rowaccomm             = $resaccom[0];
	$conference_registration_Flag = false;
	$workshop_registration_Flag = false;
	$accompany_registration_Flag = false;
	$sqlInvoice 		 = array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " 
								 WHERE  `slip_id` =? 
								   AND `delegate_id` = ? 
								   AND `status` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	      'DATA' => $slipId,     	   'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'delegate_id', 	  'DATA' => $delegateId,      'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'status', 	      	  'DATA' => 'A',     		   'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	foreach ($resInvoice as $key => $resAcc) {
		if ($resAcc['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
			$conference_registration_Flag = true;
		}
		if ($resAcc['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workshop_registration_Flag = true;
		}
		if ($resAcc['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompany_registration_Flag = true;
		}
		if ($resAcc['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$delegate_dinner_Flag = true;
		}
	}

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';
	$message .= '	 <strong>Welcome to the "' . $cfg['EMAIL_WELCOME_TO'] . '" ("' . $cfg['EMAIL_CONF_NAME'] . '") which is to be held from between "' . $cfg['EMAIL_CONF_HELD_FROM'] . '" at "' . $cfg['EMAIL_CONF_VENUE'] . '".</strong> Your data has been received successfully. ';
	$message .= '    <br /><br />';

	if ($conference_registration_Flag) {
		$message .= '    <strong>Please note the following : -</strong>';
		$message .= '	 <br />';
		$message .= '    <table border="0" width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '	 	<tr><td width="56%"><strong>Registered E-mail Id: </strong></td><td align="left">' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
		$message .= '	 	<tr><td width="56%"><strong>Registered Phone Number: </strong></td><td align="left">' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
		$message .= '    </table>';
		$message .= '    <br /><br />';
		$message .= '	 <b><u>REGISTRATION DETAILS</u></b>';
	} elseif ($workshop_registration_Flag || $accompany_registration_Flag || $delegate_dinner_Flag) {
		$message .= '	 <b><u>REGISTRATION DETAILS</u></b>';
	}

	$message .= ' 	 <br /><br />';
	$message .= 	$invoiceOrderSummary;

	$message .= ' 	 <br />';
	$message .=	'		<b><u>TRANSACTION DETAILS:</u></b><br /><br />You have selected offline payment procedure.';
	$message .=	'		<br />';
	$message .= '       <table style="font-family: Arial, Helvetica, sans-serif;font-size: 14px;border: 1px solid #000;  width: 90%; margin: auto; border-collapse:collapse">';
	$message .= '			<tr><td style="border: 1px solid #000; width:50%;">Payment Status </td><td style="border: 1px solid #000; width:50%;"> Pending</td>';
	$message .= '			<tr><td style="border: 1px solid #000; width:50%;">Receiver </td><td style="border: 1px solid #000; width:50%;"> "' . $cfg['EMAIL_CONF_NAME'] . '"</td>';
	$message .= '			<tr><td style="border: 1px solid #000; width:50%;">Date </td><td>';

	switch ($rowFetchPayment['payment_mode']) {
		case "Cash":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '';
			break;

		case "Cheque":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '';
			break;

		case "Draft":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '';
			break;

		case "NEFT":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '';
			break;

		case "RTGS":
			$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '';
			break;
	}
	$paymentModeDisplay = $rowFetchPayment['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($rowFetchPayment['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $rowFetchPayment['payment_mode']);

	$message .= '			</td>';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Amount ' . ($offlineFrom == 'BACK' ? '(Discounted)' : '') . '</td><td style="border: 1px solid #000; width:50%;"> ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '</td>';
	$message .= '		<tr><td style="border: 1px solid #000; width:50%;">Mode of Payment </td><td style="border: 1px solid #000; width:50%;"> ' . $paymentModeDisplay . '</td>';

	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$message .= '		<tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '			Cheque/DD No. :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '				' . $rowFetchPayment['cheque_number'] . '';
			$message .= '	    </td></tr><tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '				Drawee Bank :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '				' . $rowFetchPayment['cheque_bank_name'] . '';
			$message .= '		</td></tr>';
			break;
		case "Draft":
			$message .= '		<tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '		   DD No. :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '        ' . $rowFetchPayment['draft_number'] . '';
			$message .= '	    </td></tr><tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '		  Drawee Bank :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '         ' . $rowFetchPayment['draft_bank_name'] . '';
			$message .= '		</td></tr>';
			break;
		case "NEFT":
			$message .= '		<tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '		Transaction Id :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '				' . $rowFetchPayment['neft_transaction_no'] . '';
			$message .= '	    </td></tr><tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '				Drawee Bank :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '				' . $rowFetchPayment['neft_bank_name'] . '';
			$message .= '		</td></tr>';
			break;
		case "RTGS":
			$message .= '		<tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '		RTGS Transaction Id :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '				' . $rowFetchPayment['rtgs_transaction_no'] . '';
			$message .= '	    </td></tr><tr><td style="border: 1px solid #000; width:50%;">';
			$message .= '		Drawee Bank :';
			$message .= '		</td><td style="border: 1px solid #000; width:50%;">';
			$message .= '				' . $rowFetchPayment['rtgs_bank_name'] . '';
			$message .= '		</td></tr>';
			break;
	}
	$message .= '        </table>';
	$message .= '		<br />';

	if (in_array($delagateCatagory, $intArr)) {
		$termAndcondition 	=  _BASE_URL_ . 'terms.php';
		$message .= ' &nbsp;<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
		$message .= '		<br /><br/>';
	}

	if ($offlineFrom != "BACK") {
		$message .= '<strong>NOTE - Post your cash/DD/cheque/RTGS or NEFT receipt with the Payment Voucher to the ' . $cfg['EMAIL_CONF_NAME'] . ' Registration Secretariat within 10 working days.</strong>';
		$message .= '		<br /><br />';
	}

	$message .= get_office_address();

	if ($offlineFrom == 'FRONT') {
		$message .= '		<br /><br />';
		$message .= '		You will receive a Confirmation mail after the realisation of your payment.<br>';
	}

	$message .= '		<br>Registration confirmation will be mailed to your registered e-mail id after the realisation of payment. In case, the payment procedure fails, please contact "' . $cfg['EMAIL_CONF_NAME']	. '" Registration Secretariat.<br /><br />';
	$message .= 'For any assistance or query, please contact us at "' . $cfg['EMAIL_CONF_CONTACT_US'] . '" or mail us at <u>"' . $cfg['EMAIL_CONF_EMAIL_US'] . '"</u><br /><br />';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Registration Acknowledgement_" . $cfg['EMAIL_CONF_NAME'];
	switch ($rowFetchPayment['payment_mode']) {

		case "Cheque":
			$paymentType = "Cheque";
			$paymentPayment  = $rowFetchPayment['cheque_number'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your Cheque number is " . $rowFetchPayment['cheque_number'] . " for the amount of " . $rowFetchPayment['amount'] . ". Please send your cheque along with the Payment Voucher to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "Draft":
			$paymentType = "Draft";
			$paymentPayment = $rowFetchPayment['draft_number'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your DD number is " . $rowFetchPayment['draft_number'] . " for the amount of " . $rowFetchPayment['amount'] . ". Please send your Payment Voucher to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "NEFT":
			$paymentType = "NEFT";
			$paymentPayment = $rowFetchPayment['neft_transaction_no'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your NEFT number is " . $rowFetchPayment['neft_transaction_no'] . " for the amount of " . $rowFetchPayment['amount'] . ". Please send your Payment Voucher to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "RTGS":
			$paymentType = "RTGS";
			$paymentPayment = $rowFetchPayment['rtgs_transaction_no'];
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your RTGS number is " . $rowFetchPayment['rtgs_transaction_no'] . " for the amount of " . $rowFetchPayment['amount'] . ". Please send your Payment Voucher to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;

		case "Cash":
			$paymentType = "Cash";
			$paymentSms = $cfg['EMAIL_CONF_NAME'] . " has successfully received your registration details. Your  Cash reciept is for the amount of " . $rowFetchPayment['amount'] . ". Please send your Payment Voucher to the " . $cfg['EMAIL_CONF_NAME'] . " Registration Secretariat. Registration confirmation will be sent to the registered e-mail id and phone number after the realization of your payment. For further assistance feel free to contact us at " . $cfg['EMAIL_CONF_CONTACT_US'] . " or mail us at " . $cfg['EMAIL_CONF_EMAIL_US'];
			break;
	}


	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paymentSms, 'Informative');

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}

function offline_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');
	$loginUrl          	= _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice =	array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` =? AND `delegate_id` =? AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',    'DATA' => $slipId,           'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' => $delegateId,       'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',	 'DATA' => 'A',       		 'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$acmponyCounter = 0;
	foreach ($resInvoice as $keyInvoice => $rowInvoice) {
		if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails 				 = getUserDetails($rowInvoice['refference_id']);
			$sqlBanquet =	array();
			$sqlBanquet['QUERY'] = "SELECT * 
										  FROM  " . _DB_REQUEST_DINNER_ . " 
										 WHERE  `refference_id` =? 
										   AND `delegate_id` =? 
										   AND `status` = ?";
			$sqlBanquet['PARAM'][]   = array('FILD' => 'refference_id',     'DATA' => $accompanyDetails['id'],           'TYP' => 's');
			$sqlBanquet['PARAM'][]   = array('FILD' => 'delegate_id',   	'DATA' => $delegateId,           'TYP' => 's');
			$sqlBanquet['PARAM'][]   = array('FILD' => 'status',    		'DATA' => 'A',           'TYP' => 's');
			$resBanquet = $mycms->sql_select($sqlBanquet);
			$acmponyCounter++;

			$acmpany[]                       = $accompanyDetails['user_full_name'];
			if ($resBanquet) {
				$var = 'Taken';
			} else {
				$var = '-';
			}
			$Accompanymessage .= '			<tr><td><strong>' . $accompanyDetails['user_full_name'] . '</strong><td> ' . $accompanyDetails['user_registration_id'] . ' </td></tr>';
		}
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br />';
	$message .= '			<br />';
	if (floatval($rowFetchPayment['amount']) > 0.00) {
		$message .= '			Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your accompanying person(s).';
	}
	$message .= '			<br /><br />';
	$message .= '			Your Accompanying Person(s) is/are <strong>REGISTERED</strong> for  ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Registration Details-</strong></u>';
	$message .= '			<br /><br />';
	$message .= '       <table border="0" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	//$message .= '			<tr><td><strong>Registered Accompanying Person</strong> </td><td width="35%" align="center"><strong> Registration Id </strong></td></tr>';
	//$message .= '         '.$Accompanymessage.'';

	$message .= '	 <tr><td><b>Registered Accompanying Person :</b></td><td>' . $accompanyDetails['user_full_name'] . '</td></tr>';
	$message .= '	 <tr><td><b>Registration Id :</b></td><td>' . $accompanyDetails['user_registration_id'] . '</td></tr>';
	$message .= '			</table><br />';
	$message .= '			<br /><br />';
	$message .= '			 <u><strong>Accompanying Persons are entitled for</strong></u>';
	$message .= '				<ul style="list-style-type:square;">';
	$message .= '					<li > Entry to Scientific Halls and Exhibition Area</li>';
	$message .= '				  	<li> Tea/Coffee during the Conference at the venue</li>';
	$message .= '				  	<li> Lunch on 5th, 6th and 7th September 2019.</li>';
	$message .= '				</ul>';
	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '			<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
		$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			<tr><td>Receiver</td><td> ' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
		$message .= '			<tr><td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
		$message .= '			<tr><td>Amount</td><td> ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
		$message .=	'			<tr><td>Mode of Payment</td><td> ' . $rowFetchPayment['payment_mode'] . '</td></tr>';
		$message .= '		</table><br /><br />';

		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
								  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '			<br />';
	}
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];
	$transDetails = '';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque/DD no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for accompanying person registration of " . $rowFetchUserDetails['user_full_name'] . ". " . $transDetails . ". Have a nice day.";

	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . " as accompanying person(s) of " . $rowFetchUserDetails['user_full_name'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if ($rowFetchPayment['amount'] != 0.00) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;

		return $array;
	} else {
		return false;
	}
}

function onlinePaymentFailedPaymentDetails($delegateId, $slipId)
{
	global $mycms, $cfg;

	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$slipAmount = invoiceAmountOfSlip($slipId);
	$financialSummaryOfSlip = getFinancialSummaryOfSlip($slipId);
	// $rowFetchPayment 	  	= getPaymentDetails($paymentId);

	//echo '<pre>'; print_r($financialSummaryOfSlip);

	$sqlSlip 	=	array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlSlip['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'id',   	'DATA' => $slipId,   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$rowaSlip              = $resSlip[0];
	// $paymentStatus = ($rowFetchPayment['payment_status'] == 'UNPAID') ? 'Pending' : 'Paid';
	//$rowaSlip['slip_number'],$cfg['EMAIL_CONF_NAME']
	if ($slipAmount > 0) {
		// $message .= '		<u><strong>PAYMENT DETAILS</strong></u>';
		// $message .= '		<br /><br />';
		$message .= '<tr><td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr style="background: #' . $cfg['color'] . '">
						<td style="text-align: left;padding-left: 30px;">
							<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Voucher Number</p>
						</td>
						<td style="text-align: left;">
							<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $rowaSlip['slip_number'] . '</p>
						</td>
					</tr>
					<tr>
						<td style="text-align: left;padding-left: 30px;">
							<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Receiver</p>
						</td>
						<td style="text-align: left;">
							<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $cfg['EMAIL_CONF_NAME'] . '</p>
						</td>
					</tr><tr>';

		$message .= '<tr style="background: #' . $cfg['color'] . ';">
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Date </p>
				</td>
				<td style="text-align: left;">
					<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $rowaSlip['slip_date'] . '</p>
				</td>
			</tr>';
		// $message .= '<tr>
		// 	<td style="text-align: left;padding-left: 30px;">
		// 		<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Amount </p>
		// 	</td>
		// 	<td style="text-align: left;">
		// 		<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</p>
		// 	</td>
		// </tr>';
		$message .= '<tr >
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Mode</p>
				</td>
				<td style="text-align: left;">
					<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $rowaSlip['invoice_mode'] . '</p>
				</td>
			</tr>';
		$message .= '<tr style="background: #' . $cfg['color'] . ';">
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Status</p>
				</td>
				<td style="text-align: left;">
					<p style="color: red; font-size: 18px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Pending</p>
				</td>
			</tr>';
	}
	$message .=	'	</tbody>
			</table>
		</td></tr>';


	return $message;
}

function offlineConferencePaymentDetails($delegateId, $paymentId, $slipId)
{
	global $mycms, $cfg;

	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$slipAmount = invoiceAmountOfSlip($slipId);
	$financialSummaryOfSlip = getFinancialSummaryOfSlip($slipId);
	$rowFetchPayment 	  	= getPaymentDetails($paymentId);

	//echo '<pre>'; print_r($financialSummaryOfSlip);

	$sqlSlip 	=	array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlSlip['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'id',   	'DATA' => $rowFetchPayment['slip_id'],   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$rowaSlip              = $resSlip[0];
	//$rowaSlip['slip_number'],$cfg['EMAIL_CONF_NAME']
	if ($slipAmount > 0) {
		// $message .= '		<u><strong>PAYMENT DETAILS</strong></u>';
		// $message .= '		<br /><br />';
		$message .= '<tr><td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr style="background: #' . $cfg['color'] . '">
						<td style="text-align: left;padding-left: 30px;">
							<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Voucher Number</p>
						</td>
						<td style="text-align: left;">
							<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $rowaSlip['slip_number'] . '</p>
						</td>
					</tr>
					<tr>
						<td style="text-align: left;padding-left: 30px;">
							<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Receiver</p>
						</td>
						<td style="text-align: left;">
							<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $cfg['EMAIL_CONF_NAME'] . '</p>
						</td>
					</tr><tr>';
		if ($financialSummaryOfSlip['PAYMENTS']['COUNT'] > 1) {
			$message .= '	<tr><td style="border: 1px solid #000; padding:1px;" colspan="2">';
			$message .= '       <table style="font-family: Arial, Helvetica, sans-serif;font-size: 14px;border: 1px solid #000; width: 100%; margin: auto; border-collapse:collapse">';
			$message .= '			<tr>';
			$message .= '				<td style="border: 1px solid #000; width:5%; font-weight:bold;" align="center">Sl.</td>';
			$message .= '				<td style="border: 1px solid #000; width:25%; font-weight:bold;" align="center">Date</td>';
			$message .= '				<td style="border: 1px solid #000; width:15%; font-weight:bold;" align="right">Amount</td>';
			$message .= '				<td style="border: 1px solid #000; width:20%; font-weight:bold;" align="center">Mode</td>';
			$message .= '				<td style="border: 1px solid #000; width:20%; font-weight:bold;" align="center">Ref. No.</td>';
			$message .= '				<td style="border: 1px solid #000; width:15%; font-weight:bold;" align="center">Status</td>';
			$message .= '			</tr>';

			$payKaunter = 1;
			foreach ($financialSummaryOfSlip['PAYMENTS']['RAW']['paymentDetails'] as $kk => $payment) {

				$message .= '			<tr>';
				$message .= '				<td style="border: 1px solid #000;" align="center">' . $payKaunter . '</td>';
				$message .= '				<td style="border: 1px solid #000;" align="center">';
				switch ($payment['payment_mode']) {
					case "Cash":
						$message .=	'					' . date("jS F, Y", strtotime($payment['cash_deposit_date'])) . '';
						break;

					case "Cheque":
						$message .=	'					' . date("jS F, Y", strtotime($payment['cheque_date'])) . '';
						break;

					case "Draft":
						$message .=	'					' . date("jS F, Y", strtotime($payment['draft_date'])) . '';
						break;

					case "NEFT":
						$message .=	'					' . date("jS F, Y", strtotime($payment['neft_date'])) . '';
						break;

					case "RTGS":
						$message .=	'					' . date("jS F, Y", strtotime($payment['rtgs_date'])) . '';
						break;
					case "Card":
						$message .=	'					' . date("jS F, Y", strtotime($payment['card_payment_date'])) . '';
						break;
					case "Credit":
						$message .=	'					' . date("jS F, Y", strtotime($payment['credit_date'])) . '';
						break;

					case "UPI":
						$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['upi_date'])) . '';
						break;
				}
				$paymentModeDisplay = $payment['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($payment['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $payment['payment_mode']);

				$message .= '				</td>';
				$message .= '				<td style="border: 1px solid #000;" align="right">' . $payment['amount'] . '</td>';
				$message .= '				<td style="border: 1px solid #000;" align="center">' . $paymentModeDisplay . '</td>';
				$message .= '				<td style="border: 1px solid #000;" align="center">';
				switch ($payment['payment_mode']) {
					case "Cash":
						$message .=	'					-';
						break;

					case "Cheque":
						$message .=	'					' . $payment['cheque_number'] . '';
						break;

					case "Draft":
						$message .=	'					' . $payment['draft_number'] . '';
						break;

					case "NEFT":
						$message .=	'					' . $payment['neft_transaction_no'] . '';
						break;

					case "RTGS":
						$message .=	'					' . $payment['rtgs_transaction_no'] . '';
						break;
					case "Card":
						$message .=	'					' . $payment['card_transaction_no'] . '';
						break;
					case "Credit":
						$message .=	'					-';
						break;

					case "UPI":
						$message .=	'					' . $payment['txn_no'] . '';
						break;
				}
				$message .= '				</td>';
				$message .= '				<td style="border: 1px solid #000;" align="center">' . $payment['payment_status'] . '</td>';
				$message .= '			</tr>';
				$payKaunter++;
			}

			$message .= '		</table>';
			$message .= '	</td></tr>';
		} else {
			$message .= '<tr style="background: #' . $cfg['color'] . ';">
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Date </p>
				</td>
				<td style="text-align: left;">
					<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">';

			switch ($rowFetchPayment['payment_mode']) {
				case "Cash":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '';
					break;

				case "Cheque":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '';
					break;

				case "Draft":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '';
					break;

				case "NEFT":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '';
					break;

				case "RTGS":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '';
					break;
				case "Card":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['card_payment_date'])) . '';
					break;
				case "Credit":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['credit_date'])) . '';
					break;

				case "UPI":
					$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['upi_date'])) . '';
					break;
			}
			$paymentStatus = ($rowFetchPayment['payment_status'] == 'UNPAID') ? 'Pending' : (($rowFetchPayment['payment_status'] == 'PAID') ? 'PAID' : '');
			$paymentModeDisplay = $rowFetchPayment['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($rowFetchPayment['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $rowFetchPayment['payment_mode']);
			$message .=	'</p>
				</td>
			</tr>';
			$message .= '<tr>
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Amount </p>
				</td>
				<td style="text-align: left;">
					<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . '</p>
				</td>
			</tr>';
			$message .= '<tr style="background: #' . $cfg['color'] . ';">
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['light_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Mode</p>
				</td>
				<td style="text-align: left;">
					<p style="color: #' . $cfg['light_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $paymentModeDisplay . '</p>
				</td>
			</tr>';
			$message .= '<tr>
				<td style="text-align: left;padding-left: 30px;">
					<p style="font-weight: 500; color: #' . $cfg['dark_color'] . '; font-size: 15px;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">Payment Status</p>
				</td>
				<td style="text-align: left;">
					<p style="color: #' . $cfg['dark_color'] . '; font-size: 18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;">' . $paymentStatus . '</p>
				</td>
			</tr>';
		}
		$message .=	'	</tbody>
			</table>
		</td></tr>';
	}

	return $message;
}

function offlineConferenceInvoiceDetails($delegateId, $slipId)
{
	global $mycms, $cfg;

	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$slipAmount = invoiceAmountOfSlip($slipId);
	if ($slipAmount > 0) {
		// 	$message .= '			<strong><u>INVOICE DETAILS</u></strong>';	
		// 	$message .= '			<br /><br />';	
		// 	$message .= '			<table style="font-family: Arial, Helvetica, sans-serif;font-size: 14px;border: 1px solid #000;  width: 100%; margin: auto; border-collapse:collapse">';
		// 	$message .= '			  <tr>	
		// 									<td style="width:30%; border-right:1px solid #000; border-bottom:1px solid #000;"><b>INVOICE NUMBER</b></td>
		// 									<td style="width:45%; border-right:1px solid #000; border-bottom:1px solid #000;"><b>INVOICE FOR</b></td>
		// 									<td style="width:30%; border-right:1px solid #000; border-bottom:1px solid #000;" align="center"><b>DOWNLOAD</b></td>
		// 							  </tr>';
		$message .= '
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 30px 30px 60px;">
				<tbody>';
		$message .= getInvoiceMailerDetails($delegateId, $slipId);
		$message .= ' </tbody>
		</table>
	</td>
</tr>';
		// $message .= '			</table>';	
		// $message .= '			<br /><br />';

		return $message;
	}
}

function offlineConferenceRegistrationDetails($delegateId)
{
	global $mycms, $cfg;
	$rowFetchUserDetails   	= getUserDetails($delegateId);

	$message = '';
	$message .= ' 	 <table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	 	<tr><td><b>Registered e-mail id :</b></td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
	$message .= '	 	<tr><td><b>Registered mobile number :</b></td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
	$message .= '	 	<tr><td><b>Unique Sequence:</b></td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
	$message .= '	 	<tr><td><b>Registration ID:</b></td><td>' . $rowFetchUserDetails['user_registration_id'] . '</td></tr>';
	$message .= ' 	 </table>';

	return $message;
}

function offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND', $offlineFrom = 'FRONT', $by = 'DELEGATE')
{	?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Noticia+Text:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<?php
	//echo 2121;
	global $mycms, $cfg;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$loginUrl          		= _BASE_URL_;
	$rowFetchUserDetails   	= getUserDetails($delegateId);
	$invoiceOrderSummary   	= generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	  	= getPaymentDetails($paymentId);
	$user_password     	   	= $mycms->decoded($rowFetchUserDetails['user_password']);

	$financialSummaryOfSlip = getFinancialSummaryOfSlip($slipId);



	$sqlSlip 	=	array();
	$sqlSlip['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_SLIP_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlSlip['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlSlip['PARAM'][]   = array('FILD' => 'id',   	'DATA' => $rowFetchPayment['slip_id'],   'TYP' => 's');
	$resSlip			   = $mycms->sql_select($sqlSlip);
	$rowaSlip              = $resSlip[0];


	$sqlSlip 	=	array();
	$sqlaccommodation['QUERY'] 	   = "SELECT * 
											FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
									  	   WHERE status = ? 
										    AND user_id = ? ";
	$sqlaccommodation['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		 		 'TYP' => 's');
	$sqlaccommodation['PARAM'][]   = array('FILD' => 'user_id',   'DATA' => $delegateId,   		  'TYP' => 's');
	$resaccom			   = $mycms->sql_select($sqlaccommodation);
	$rowaccomm             = $resaccom[0];
	$slipAmount = invoiceAmountOfSlip($slipId);

	$offlineConferencePaymentDetails = offlineConferencePaymentDetails($delegateId, $paymentId, $slipId);

	$offlineConferenceInvoiceDetails = offlineConferenceInvoiceDetails($delegateId, $slipId);

	$offlineConferenceRegistrationDetails = offlineConferenceRegistrationDetails($delegateId);


	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_EMAIL_TEMPLATE_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 1,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];
	//print_r($rowaMail);
	//echo $rowaMail['title'];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
										WHERE `title` = 'Payment Successful'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];
	
	$logo = '<a href="#" target="_blank" text-decoration: none; border: 0;"><img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" alt="logo" width="310" height="" style="display: block; border: 0;" /></a>';
	// $img = _BASE_URL_ . 'images/payment-success.png';

	$line = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1 = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2 = _BASE_URL_ . 'images/mailer/line-after.png';
	$reg_link = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';

	
	$currr 		= getRegistrationCurrency(getUserClassificationId($delegateId));
	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);
	// echo "<pre>"; 
	// print_r($invoiceOrderSummary);
	// echo "</pre><pre>"; 

	// print_r($offlineConferenceInvoiceDetails);
	// echo "</pre>";

	$find = [
		'[LOGO]', '[UNIQUE_ID]', '[username]', '[IMG]', '[REG_ID]', '[MAIL]', '[MOBILE]',
		'[registration_details]', '[payment_details]', '[invoice_details]', '[amount]',
		'[inr]', '[invoice_order_details]','[LINE]', '[LINE1]', '[LINE2]', '[REG_LINK]',
		'[CONF_EMAIL]', '[CONF_MOBILE]', '[FOOTER_IMG]', 'cdf0c2', '52cc65', '317031'
	];

	$replacement = [
		$logo, $rowFetchUserDetails['user_unique_sequence'], $rowFetchUserDetails['user_full_name'], $img,
		$rowFetchUserDetails['user_registration_id'], $rowFetchUserDetails['user_email_id'], $rowFetchUserDetails['user_mobile_no'],
		$offlineConferenceRegistrationDetails, $offlineConferencePaymentDetails,
		$offlineConferenceInvoiceDetails, number_format($financialSummaryOfSlip['AMOUNT'], 2), $currr, $invoiceOrderSummary,
		$line,$line1, $line2, $reg_link, $cfg['EMAIL_CONF_EMAIL_US'], $cfg['EMAIL_CONF_CONTACT_US'],
		$footer_img, $cfg['light_color'], $cfg['color'], $cfg['dark_color']
	];


	$result = str_replace($find, $replacement, $mailTemplateDescription);

	$message = $result;

	$subject  = $rowaMail['subject'];
	$transDetails = '';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no :' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no :' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no :' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no :' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no :' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	if ($slipAmount > 0) {
		if ($financialSummaryOfSlip['PAYMENTS']['COUNT'] > 1) {
			$lastAmount = $financialSummaryOfSlip['PAYMENTS']['paymentDetails'][$financialSummaryOfSlip['PAYMENTS']['COUNT'] - 1]['amount'];
			$currr 		= getRegistrationCurrency(getUserClassificationId($delegateId));
			$payRegSms	= "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . $currr . ' ' . number_format($financialSummaryOfSlip['AMOUNT'], 2) . " for the registration of " . $rowFetchUserDetails['user_full_name'] . ". You are now SUCCESSFULLY REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence:" . $rowFetchUserDetails['user_unique_sequence'] . ", " . $transDetails . " Registration ID:" . $rowFetchUserDetails['user_registration_id'] . ", Registered Email ID:" . $rowFetchUserDetails['user_email_id'] . ". For details, please check the Registration Confirmation mail. Invoice will be sent at registered mail id. Have a nice day.";
		} else {
			$payRegSms	= "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received the payment of " . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . " for the registration of " . $rowFetchUserDetails['user_full_name'] . ". You are now SUCCESSFULLY REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence:" . $rowFetchUserDetails['user_unique_sequence'] . ", " . $transDetails . " Registration ID:" . $rowFetchUserDetails['user_registration_id'] . ", Registered Email ID:" . $rowFetchUserDetails['user_email_id'] . ". For details, please check the Registration Confirmation mail. Invoice will be sent at registered mail id. Have a nice day.";
		}
	} else {
		$payRegSms	= "You are now SUCCESSFULLY REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence:" . $rowFetchUserDetails['user_unique_sequence'] . ",  Registration ID:" . $rowFetchUserDetails['user_registration_id'] . ", Registered Email ID:" . $rowFetchUserDetails['user_email_id'] . ". For details, please check the Registration Confirmation mail. Have a nice day.";
	}
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $payRegSms, 'Informative');
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $payRegSms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function offline_conference_complimentry_registration_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND', $offlineFrom = 'FRONT')
{
	global $mycms, $cfg;
	$loginUrl          	= _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	  = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);
	$sqlaccommodation     = array();
	$sqlaccommodation['QUERY'] 	 = "SELECT * FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
									   WHERE status = 'A' 
										 AND user_id = '" . $delegateId . "' ";

	$resaccom			   = $mycms->sql_select($sqlaccommodation);
	$rowaccomm             = $resaccom[0];
	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';
	if ($rowFetchUserDetails['operational_area'] == "COUNTER" && ($rowFetchPayment['payment_mode'] == 'Cheque' || $rowFetchPayment['payment_mode'] == 'Draft')) {
	} else {
		$message .= ' <strong>Welcome to the ' . $cfg['EMAIL_WELCOME_TO'] . ' which is to be held from ' . $cfg['EMAIL_CONF_HELD_FROM'] . ' at ' . $cfg['EMAIL_CONF_VENUE'] . '</strong>';
		$message .= '    <br /><br />';
	}

	$message .= '    You are <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';

	$message .= '    <br /><br />';
	$message .= ' 	 <u>Please note the following :-</u>';
	$message .= ' 	 <br /><table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	 <tr><td><strong>Registered E-mail Id: </strong></td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registered Phone Number: </strong></td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Unique Sequence: </strong></td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registration Id: </strong></td><td>' . $rowFetchUserDetails['user_registration_id'] . '</td></tr>';
	$message .= ' 	 </table><br /><br />';
	$message .= '	 <u><b>REGISTRATION DETAILS</b></u><br /><br>';
	$message .= ' 	 ';
	$message .= 	$invoiceOrderSummary;

	$message .= '			<br /><br />';
	$slipAmount = invoiceAmountOfSlip($slipId);
	if ($slipAmount > 0) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
								  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '    <br /><br />';
	}



	$delagateCatagory      = getUserClassificationId($delegateId);
	$message .=            registration_note();

	$intArr	 = array(5, 6, 7, 10, 11, 12); // Combo Ids


	$message .= '<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('terms_n_condition.php') . '">Click here to know the terms and condition.</a>';
	$message .= '<br /><br/>';
	$message .= '<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('cancellation_n_refund.policy.php') . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $loginUrl . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . '  user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at   ' . $cfg['ADMIN_REGISTRATION_EMAIL'];
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";
	$transDetails = '';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	$paysms = "" . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . " from " . $rowFetchUserDetails['user_full_name'] . ". " . $transDetails . ". Have a nice day.";

	$regsms      = "You are now REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence : " . $rowFetchUserDetails['user_unique_sequence'] . ", Registration Id : " . $rowFetchUserDetails['user_registration_id'] . ", Registered E-mail Id : " . $rowFetchUserDetails['user_email_id'] . ".";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms, 'Informative');

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function offline_exhibitor_registration_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND', $offlineFrom = 'FRONT')
{
	global $mycms, $cfg;
	return false;
	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';

	$message .= '    Welcome to the ' . $cfg['EMAIL_WELCOME_TO'] . ' to be held from ' . $cfg['EMAIL_CONF_HELD_FROM'] . ' </strong>at <strong>' . $cfg['EMAIL_CONF_VENUE'] . '</strong>';
	$message .= '    <br /><br />';

	$message .= '    You are <strong>REGISTERED as EXHIBITOR REPRESENTATIVE</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';
	$message .= '    <br /><br />';
	$message .= ' 	 <u>Please note the following :-</u>';
	$message .= ' 	 <br /><table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	 <tr><td><strong>Unique Sequence: </strong></td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registration Id: </strong></td><td>' . $rowFetchUserDetails['user_registration_id'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registered E-mail Id: </strong></td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registered Phone Number: </strong></td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Company Name: </strong></td><td>' . getExhibitorName($rowFetchUserDetails['exhibitor_company_id']) . '</td></tr>';
	$message .= ' 	 </table><br /><br />';

	$message .= '	 <u><b>REGISTRATION DETAILS</b></u><br /><br>';
	$message .= ' 	 ';
	$message .= 	$invoiceOrderSummary;

	$message .= 	exhibitor_registration_note();

	$message .= '<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('terms_n_condition.php') . '">Click here to know the terms and condition.</a>';
	$message .= '<br /><br/>';
	$message .= '<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('cancellation_n_refund.policy.php') . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $loginUrl . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . '  user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at   ' . $cfg['ADMIN_REGISTRATION_EMAIL'];
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject  = "Registration Confirmation as Exhibitor Representative_" . $cfg['EMAIL_CONF_NAME'] . "";
	$transDetails = '';

	$regsms      = "You are now REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence : " . $rowFetchUserDetails['user_unique_sequence'] . ", Registration Id : " . $rowFetchUserDetails['user_registration_id'] . ", Registered E-mail Id : " . $rowFetchUserDetails['user_email_id'] . ".";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");

		$status2 = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $sms, "SEND",$status2);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function offline_conference_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	include_once('function.delegate.php');
	include_once('function.invoice.php');
	include_once('function.registration.php');
	include_once('function.delegate.php');

	$loginUrl              = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice		  =	array();
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',    		'DATA' => $slipId,       'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id',    	'DATA' => $delegateId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',    		'DATA' => 'A',   		'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
		$sqlWorkshop		  =	array();
		$sqlWorkshop['QUERY'] = "SELECT request.*,classf.classification_title,classf.type,classf.workshop_date
									  FROM " . _DB_REQUEST_WORKSHOP_ . " request
								INNER JOIN " . _DB_WORKSHOP_CLASSIFICATION_ . " classf
										ON request.workshop_id = classf.id
									 WHERE  request.delegate_id =? 
									   AND request.refference_slip_id = ?
									   AND request.status = ?
									   AND classf.status = ?";
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.delegate_id',    		'DATA' => $delegateId,       'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.refference_slip_id',   'DATA' => $slipId,          	'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'request.status',   			'DATA' => 'A',           	'TYP' => 's');
		$sqlWorkshop['PARAM'][]   = array('FILD' => 'classf.status',    			'DATA' => 'A',           	'TYP' => 's');
		$resWorkshop = $mycms->sql_select($sqlWorkshop);

		$rowWorkshopDetails = $resWorkshop[0];
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br />';
	$message .= '			<br />';
	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '			Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your Workshop.';
		$message .= '			<br />Your workshop has been <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '.';
	}

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Workshop Details:-</strong></u>';
	$message .= '			<br /><br />';

	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td width="30%">Workshop Name:</td><td>' . $rowWorkshopDetails['classification_title'] . '</td></tr>';
	$message .= '			<tr><td width="30%">Date</td><td>' . $rowWorkshopDetails['workshop_date'] . '</td></tr>';
	$message .= '		</table>';
	$message .= '		<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td>Receiver</td><td> ' . $cfg['EMAIL_CONF_NAME'] . '</td></tr>';
	$message .= '			<tr><td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';

	if (false) {
		switch ($rowFetchPayment['payment_mode']) {
			case "Cash":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '';
				break;

			case "Cheque":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '';
				break;

			case "Draft":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '';
				break;

			case "NEFT":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '';
				break;

			case "RTGS":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '';
				break;
			case "Card":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['card_payment_date'])) . '';
				break;
		}
	}

	$message .=	'	<tr> <td>Amount ' . ($offlineFrom == 'BACK' ? '(Discounted)' : '') . ' : </td> <td> ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>'; //'.getRegistrationCurrency(getUserClassificationId($delegateId)).'.
	$message .=	'	<tr>  <td>Mode of Payment :</td> <td>' . $rowFetchPayment['payment_mode'] . '</td></tr> ';

	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$message .= '		<tr><td>Cheque No. :</td><td> ' . $rowFetchPayment['cheque_number'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td> ' . $rowFetchPayment['cheque_bank_name'] . '</td></tr>';
			break;
		case "Draft":
			$message .= '		<tr><td>DD No. :</td><td>' . $rowFetchPayment['draft_number'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['draft_bank_name'] . '</td></tr>';
			break;
		case "NEFT":
			$message .= '		<tr><td>NEFT Transaction Id :</td><td>' . $rowFetchPayment['neft_transaction_no'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['neft_bank_name'] . '</td></tr>';
			break;
		case "RTGS":
			$message .= '		<tr><td>RTGS Transaction Id :</td><td>' . $rowFetchPayment['rtgs_transaction_no'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['rtgs_bank_name'] . '</td></tr>';
			break;
		case "Card":
			$message .= '		<tr><td>Card Number :</td><td>' . $rowFetchPayment['card_transaction_no'] . '</td></tr>';

			break;
	}



	$message .= '			</table><br />';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
								  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '			<br />';
	}
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<br /><a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$transDetails = '';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for Workshop registration of " . $rowFetchUserDetails['user_full_name'] . ". " . $transDetails . " Have a nice day.";

	$regsms	 = "Dear Delegate, " . $rowFetchUserDetails['user_full_name'] . " is now REGISTERED for " . $rowWorkshopDetails['classification_title'] . " Workshop in " . $cfg['EMAIL_CONF_NAME'];
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}
		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function getAccommodatioPaymentDetails($paymentId, $delegateId, $slipId)
{
	$rowFetchPayment = getPaymentDetails($paymentId);
	$message = '';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '		<u><strong>TRANSACTION DETAILS</strong></u>';
		$message .= '		<br /><br />';
		$message .=	'		Payment has been done through offline payment process.';
		$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		//$message .= '		<tr><td>Receiver</td><td>  "'.$cfg['EMAIL_CONF_NAME'].'"</td></tr>';
		$message .= '		<tr><td>Date </td><td>';
		switch ($rowFetchPayment['payment_mode']) {
			case "Cash":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '';
				break;

			case "Cheque":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '';
				break;

			case "Draft":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '';
				break;

			case "NEFT":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '';
				break;

			case "RTGS":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '';
				break;
		}
		$message .= '		</td></tr>';
		$message .= '		<tr><td>Amount ' . ($offlineFrom == 'BACK' ? '(Discounted)' : '') . '</td><td> ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '</td>';
		$message .= '		<tr><td>Mode of Payment </td><td> ' . $rowFetchPayment['payment_mode'] . '</td>';
		switch ($rowFetchPayment['payment_mode']) {
			case "Cheque":
				$message .= '		<tr><td>';
				$message .= '			Cheque No. :';
				$message .= '		</td><td>';
				$message .= '				' . $rowFetchPayment['cheque_number'] . '';
				$message .= '	    </td></tr><tr><td>';
				$message .= '				Drawee Bank :';
				$message .= '		</td><td>';
				$message .= '				' . $rowFetchPayment['cheque_bank_name'] . '';
				$message .= '		</td></tr>';
				break;
			case "Draft":
				$message .= '		<tr><td>';
				$message .= '		   DD No. :';
				$message .= '		</td><td>';
				$message .= '        ' . $rowFetchPayment['draft_number'] . '';
				$message .= '	    </td></tr><tr><td>';
				$message .= '		  Drawee Bank :';
				$message .= '		</td><td>';
				$message .= '         ' . $rowFetchPayment['draft_bank_name'] . '';
				$message .= '		</td></tr>';
				break;
			case "NEFT":
				$message .= '		<tr><td>';
				$message .= '		NEFT Transaction Id :';
				$message .= '		</td><td>';
				$message .= '				' . $rowFetchPayment['neft_transaction_no'] . '';
				$message .= '	    </td></tr><tr><td>';
				$message .= '				Drawee Bank :';
				$message .= '		</td><td>';
				$message .= '				' . $rowFetchPayment['neft_bank_name'] . '';
				$message .= '		</td></tr>';
				break;
			case "RTGS":
				$message .= '		<tr><td>';
				$message .= '		RTGS Transaction Id :';
				$message .= '		</td><td>';
				$message .= '				' . $rowFetchPayment['rtgs_transaction_no'] . '';
				$message .= '	    </td></tr><tr><td>';
				$message .= '		Drawee Bank :';
				$message .= '		</td><td>';
				$message .= '				' . $rowFetchPayment['rtgs_bank_name'] . '';
				$message .= '		</td></tr>';
				break;
		}
		$message .= '		</table><br /><br />';
	}

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
													<td width="15%"><b>Invoice No.</b></td>
													<td width="70%"><b>Invoice for</b></td>
													<td width="20%" align="center"><b>Download</b></td>
											  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '			<br /><br />';
	}


	return $message;
}

function offline_accommodation_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $mycms, $cfg;

	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);

	$invoiceOrderSummary   = generateInvoiceOrderSummaryForAccommodation($delegateId, $slipId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);

	$sqlaccommodationDetails = array();
	$sqlaccommodationDetails['QUERY']  = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,
	   												 masterHotel.hotel_address AS hotel_address,package.package_name
											    FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
										  INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
												  ON accommodation.package_id = package.id
										  INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
												  ON masterHotel.id = package.hotel_id
											   WHERE accommodation.status = ? 
												 AND accommodation.user_id = ?";

	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',   'DATA' => 'A',   		  'TYP' => 's');
	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',   'DATA' => $delegateId,   		  'TYP' => 's');
	$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
	$rowaccomm             = $resaccommodation[0];

	$sqlInvoice 	=	array();
	$sqlInvoice['QUERY'] = "SELECT * 
								  FROM " . _DB_INVOICE_ . " 
								 WHERE `slip_id` =? 
								   AND `delegate_id` =? 
								   AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',            'DATA' => $slipId,   	 'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id',        'DATA' => $delegateId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',             'DATA' => 'A',   		 'TYP' => 's');
	$resInvoice 		= $mycms->sql_select($sqlInvoice);



	$getAccommodatioPaymentDetails = getAccommodatioPaymentDetails($paymentId, $delegateId, $slipId);


	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM " . _DB_EMAIL_TEMPLATE_ . " 
					       WHERE status = ? 
						     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 10,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row = $result[0];


	if ($resInvoice) {
		$counter = 0;
		$acmponyCounter = 0;
		foreach ($resInvoice as $keyInvoice => $rowInvoice) {


			if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$RegData['REGISTRATION_DATA']['REGTYP']  = getRegClsfName(getUserClassificationId($delegateId));
				$RegData['ACCOMODATION_DETAILS'] 		 = accmodation_details($delegateId);

				$getAccommodationDetails['ACCOMODATION_DETAILS_ROOM'] = getAccommodationDetails($delegateId, $rowInvoice['id']);
			}
		}
	}


	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	if ($row['header_image'] != '') {
		$emailHeader  = $header_image;
	}

	if ($row['footer_image'] != '') {
		$emailFooter  = $footer_image;
	}

	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);

	$find = ['[USERNAME]', '[RESERVATION_DETAILS]', '[PAYMENT_DETAILS]'];

	$replacement = [$rowFetchUserDetails['user_full_name'], $invoiceOrderSummary, $getAccommodatioPaymentDetails];


	$result = str_replace($find, $replacement, $mailTemplateDescription);

	$message = '';

	$message .= '<img src="' . $emailHeader . ' " width="100%" alt="header" /><br/><br/>';
	$message .= $result;
	$message .= '<img src="' . $emailFooter . ' " width="100%" alt="header" /><br/><br/>';



	//echo '<pre>'; print_r($getAccommodationDetails); die;

	if ($getAccommodationDetails['ACCOMODATION_DETAILS_ROOM']['ACCOMMODATION_DATA']['ROOM_TYPE'] == 'Y') {
		$subject  = "Additional accommodation is confirmed_" . $cfg['EMAIL_CONF_NAME'];
	} else {
		$subject  = "Your accommodation is revised_" . $cfg['EMAIL_CONF_NAME'];
	}

	/*echo $message;
		echo $rowFetchUserDetails['user_email_id'];
		die();*/

	$paysms  = "Transaction SUCCESSFUL " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . "." . number_format($rowFetchPayment['amount'], 2) . " to book a stay for " . $rowaccomm['hotel_name'] . " Transaction ID " . $rowFetchPayment['atom_atom_transaction_id'] . " Have a nice day.";
	$regsms = "Your reservation at " . $rowaccomm['hotel_name'] . ", " . $rowaccomm['hotel_address'] . " is CONFIRMED. Check-in 14:00 hrs. " . $rowaccomm['checkin_date'] . ", Check-out 12:00 hrs. on " . $rowaccomm['checkout_date'] . ".";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function generateInvoiceOrderSummaryForAccommodation($delegateId, $slipId)
{


	global $cfg, $mycms;
	include_once('function.workshop.php');
	include_once('function.dinner.php');
	include_once('function.accommodation.php');
	$sqlInvoice 	=	array();
	$sqlInvoice['QUERY'] = "SELECT * 
								  FROM " . _DB_INVOICE_ . " 
								 WHERE `slip_id` =? 
								   AND `delegate_id` =? 
								   AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',            'DATA' => $slipId,   	 'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id',        'DATA' => $delegateId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',             'DATA' => 'A',   		 'TYP' => 's');
	$resInvoice 		= $mycms->sql_select($sqlInvoice);

	//echo '<pre>'; print_r($resInvoice); die();

	$sqlGetAccommodation   = array();
	$sqlGetAccommodation['QUERY']	= "SELECT req.id,req.refference_invoice_id,req.user_id,req.accompany_name,req.accommodation_details,req.hotel_id,req.package_id,req.checkin_date,req.checkout_date,req.booking_quantity,R.room_id, req.rooms_no FROM " . _DB_REQUEST_ACCOMMODATION_ . " req INNER JOIN  " . _DB_MASTER_ROOM_ . " R ON req.id = R.request_accommodation_id WHERE req.`user_id` = '" . trim($delegateId) . "' AND req.refference_slip_id = '" . $slipId . "' AND req.`status`='A' AND R.`status`='A' ORDER BY R.room_id ASC, req.created_dateTime ASC";

	$resultGetAccommodation = $mycms->sql_select($sqlGetAccommodation);

	//echo '<pre>'; print_r($resultGetAccommodation); die();
	$userDetails 	    = getUserDetails($delegateId);



	$templateString		= "";
	$acmponyStatus 		= false;
	$RegData 			= array();

	if ($resInvoice) {
		$counter = 0;
		$acmponyCounter = 0;
		foreach ($resInvoice as $keyInvoice => $rowInvoice) {
			if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
				$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
				if ($userDetails['registration_request'] == 'EXHIBITOR') {
					$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
				} else {
					$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($delegateId));
				}
			}

			if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
				$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($delegateId));
				$RegData['ACCOMODATION_DETAILS'] = accmodation_details($delegateId);
			}

			if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
				$Wstatus 						 	 = true;
				$Wcounter++;
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']	 = getWorkshopDetails($rowInvoice['refference_id']);
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name']         	 = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date']         	 = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']          = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
			}

			if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
				$acmponyStatus 					      = true;
				$acmponyCounter++;
				$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']  	   = getUserDetails($rowInvoice['refference_id']);
				$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name']        = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
			}

			if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
				$Dstatus 						 = true;
				$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
				$Dcounter++;
				$dinner[]                     	 = getInvoiceTypeStringForMail($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
			}

			if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$RegData['REGISTRATION_DATA']['REGTYP']  = getRegClsfName(getUserClassificationId($delegateId));
				$RegData['ACCOMODATION_DETAILS'] 		 = accmodation_details($delegateId);



				$getAccommodationDetails['ACCOMODATION_DETAILS_ROOM'] = getAccommodationDetails($delegateId, $rowInvoice['id']);
			}
		}
	}

	//echo '<pre>'; print_r($getAccommodationDetails['ACCOMODATION_DETAILS']['ACCOMMODATION_DATA']); die();

	$templateString                  .= '<table style="width:100%; border:1px solid #000; font-family: Arial, Helvetica, sans-serif; font-size: 14px; margin: auto; border-collapse: collapse;">';



	if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {

		// echo '<pre>'; print_r($RegData); die();

		foreach ($resultGetAccommodation as $key => $value) {

			$hotel_name = getHotelNameByID($value['hotel_id']);
			$getPackageNameById =  getPackageNameById($value['package_id']);

			$templateString                 .= '<tr>';
			$templateString                 .= '<td valign="top" align="left" style="padding: 5px;width:30%; border-bottom:dashed thin #ccc;"><strong>Accommodation :</strong></td>';
			$templateString                 .= '<td valign="top" align="left" style="padding: 5px;border-bottom:dashed thin #ccc;">';

			if (!empty($RegData['ACCOMODATION_DETAILS']['ACCOMDA_DETAILS'])) {

				$details = explode('@', $RegData['ACCOMODATION_DETAILS']['ACCOMDA_DETAILS']);
				$accommodation_deatils = $details[0];
			} else {
				$accommodation_deatils = '';
			}

			//$templateString                 .= $accommodation_deatils; // $RegData['REGISTRATION_DATA']['REGTYP'];



			$templateString                 .= '<br>';
			$templateString                 .= 'Hotel : ' . $hotel_name . ' (' . $getPackageNameById . ')<br>';
			$templateString                 .= 'Room : ' . $value['room_id'] . '<br>';
			$templateString                 .= 'Check-in Date :' . $mycms->cDate("d/m/Y", $value['checkin_date']) . '<br>';
			$templateString                 .= 'Check-out Date :' . $mycms->cDate("d/m/Y", $value['checkout_date']) . '<br>';

			if (!empty($cfg['ACCOMMODATION_INCLUSION'])) {
				$templateString                 .= 'Inclusion : ' . $cfg['ACCOMMODATION_INCLUSION'] . '<br>';
			}


			$templateString                 .= '</td>';
			$templateString                 .= '</tr>';
		}
	}

	$templateString                 .= '</table>';

	//echo $templateString;die();

	return $templateString;
}

function offline_dinner_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;

	$loginUrl              = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice 			=	array();
	$sqlInvoice['QUERY']    = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	    'DATA' => $slipId,         'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'delegate_id', 	'DATA' => $delegateId,     'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'status', 	  		'DATA' => 'A',             'TYP' => 's');
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
		$sqlDinner 			=	array();
		$sqlDinner['QUERY'] = "SELECT request.*,user.user_full_name
									 FROM  " . _DB_REQUEST_DINNER_ . " request
							  INNER JOIN " . _DB_USER_REGISTRATION_ . " user
									  ON request.refference_id = user.id
								   WHERE request.delegate_id =? 
									 AND request.refference_slip_id = ? 
									 AND request.status = ?
									 AND user.status = ?
									 AND user.registration_payment_status != ?
									 AND request.payment_status != ?";
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.delegate_id', 	    	'DATA' => $delegateId,    'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.refference_slip_id',  	'DATA' => $slipId,        'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.status', 	    			'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.status', 	   				'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.registration_payment_status', 'DATA' => 'UNPAID',       'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.payment_status', 	    	'DATA' => 'UNPAID',       'TYP' => 's');
		$resDinner = $mycms->sql_select($sqlDinner);
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr use="canvas"><td align="left" valign="top">';
	$message .= '		Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '		<br />';
	$message .= '		<br />';
	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '		Transaction <strong>SUCCESSFUL</strong>. ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . ' ' . number_format($rowFetchPayment['amount'], 2) . ' for registration of your GALA DINNER.';
	}
	$message .= '		<br />Your dinner has been <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '.';
	$message .= '		<br /><br />';
	$message .= '		Please save this e-mail for further reference.';
	$message .= '		<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Dinner Details:-</strong></u>';
	$message .= '		<br /><br />';

	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td width="30%">Date:</td><td>' . $cfg['BANQUET_DINNER_DATE'] . '</td></tr>';

	$dinnercnt = 0;
	$acmponyCounter = 0;
	foreach ($resDinner as $key => $rowDinner) {
		if ($dinnercnt > 0) {
			$message .= '	<tr><td>' . $rowDinner['user_full_name'] . '</td></tr>';
			$acmponyCounter++;

			$acmpany[] = $rowDinner['user_full_name'];
		} else {
			$message .= '	<tr><td rowspan="' . sizeof($resDinner) . '" width="30%">Guest(s):</td><td>' . $rowDinner['user_full_name'] . '</td></tr>';
			$acmponyCounter++;

			$acmpany[]  = $rowDinner['user_full_name'];
		}
		$dinnercnt++;
	}
	$message .= '		</table>';

	$message .= '		<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Transaction Details :-</strong></u><br /><br />';
	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td>Receiver</td><td> "' . $cfg['EMAIL_CONF_NAME'] . '"</td></tr>';
	$message .= '			<tr><td>Transaction Date</td><td> ' . date("jS F, Y", strtotime($rowFetchPayment['payment_date'])) . '</td></tr>';
	$message .=	'			<tr> <td>Amount ' . ($offlineFrom == 'BACK' ? '(Discounted)' : '') . ' : </td> <td>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
	$message .=	'			<tr>  <td>Mode of Payment :</td> <td>' . $rowFetchPayment['payment_mode'] . '</td></tr> ';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$message .= '		<tr><td>Cheque No. :</td><td> ' . $rowFetchPayment['cheque_number'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td> ' . $rowFetchPayment['cheque_bank_name'] . '</td></tr>';
			break;
		case "Draft":
			$message .= '		<tr><td>DD No. :</td><td>' . $rowFetchPayment['draft_number'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['draft_bank_name'] . '</td></tr>';
			break;
		case "NEFT":
			$message .= '		<tr><td>NEFT Transaction Id :</td><td>' . $rowFetchPayment['neft_transaction_no'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['neft_bank_name'] . '</td></tr>';
			break;
		case "RTGS":
			$message .= '		<tr><td>RTGS Transaction Id :</td><td>' . $rowFetchPayment['rtgs_transaction_no'] . '</td></tr>';
			$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['rtgs_bank_name'] . '</td></tr>';
			break;
		case "Card":
			$message .= '		<tr><td>Card Number :</td><td>' . $rowFetchPayment['card_transaction_no'] . '</td></tr>';

			break;
	}
	$message .= '		</table>';

	if ($rowFetchPayment['amount'] != 0.00) {
		$message .= '	<br /><br />';
		$message .= '   <table border="0" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .=	'		<tr> <td><strong><u>INVOICE DETAILS</u></strong></td></tr> ';
		$message .= '	</table>';
		$message .= '	<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '		<tr>	
									<td width="15%"><b>Invoice No.</b></td>
									<td width="70%"><b>Invoice for</b></td>
									<td width="20%" align="center"><b>Download</b></td>
								</tr>';
		$message .= 		getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '	</table>';
	}

	$message .= '		<br /><br />';
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '		<br /><a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '		<br /><br/>';
	$message .= '		<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '		<br /><br/>';
	$message .= '		<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '		<br /><br/>';
	$message .= '		For more information please write at <u><strong>' . $cfg['EMAIL_CONF_EMAIL_US'] . '</strong></u>';
	$message .= '		<br /><br/>';
	$message .= '	</td></tr>';

	$message .= get_email_footer();
	$message .= '	</table>';
	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'];

	$transDetails = '';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for banquet dinner registration. " . $transDetails . ". Have a nice day.";

	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " now REGISTERED for banquet dinner of " . $cfg['EMAIL_CONF_NAME'] . " on " . $cfg['BANQUET_DINNER_DATE'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function certificate_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;

	$loginUrl              = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice 			=	array();
	$sqlInvoice['QUERY']    = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	    'DATA' => $slipId,         'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'delegate_id', 	'DATA' => $delegateId,     'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'status', 	  		'DATA' => 'A',             'TYP' => 's');

	$resInvoice = $mycms->sql_select($sqlInvoice);

	$fetchInvoice	=	$resInvoice[0];

	//$fetchInvoice	=	$resInvoice[0];		

	if ($fetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {

		$sqlDinner 		 =	array();
		$sqlDinner['QUERY'] = "SELECT request.*,user.user_full_name
									 FROM  " . _DB_REQUEST_DINNER_ . " request
							  INNER JOIN " . _DB_USER_REGISTRATION_ . " user
									  ON request.refference_id = user.id
								   WHERE request.delegate_id =? 
									 AND request.refference_slip_id = ? 
									 AND request.status = ?
									 AND user.status = ?
									 AND user.registration_payment_status != ?
									 AND request.payment_status != ?";

		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.delegate_id', 	    	'DATA' => $delegateId,    'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.refference_slip_id',  	'DATA' => $slipId,        'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.status', 	    			'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.status', 	   				'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.registration_payment_status', 'DATA' => 'UNPAID',       'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.payment_status', 	    	'DATA' => 'UNPAID',       'TYP' => 's');
		$resDinner = $mycms->sql_select($sqlDinner);
	}

	$certificateLink = _BASE_URL_ . 'print_certificate_pdf.php?id=' . $delegateId . "&type=delegate";

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr use="canvas"><td align="left" valign="top">';
	$message .= '		Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '		<br /><br />';

	$message .= '<p>New Year Greetings from ISAR 2024.</p>';

	$message .= '<p>ISAR 2024 gave us a unique opportunity to meet and interact with so many friends in Odisha. It was a few days of intense learning spiced with food and fun. The hangover of pleasant memories is still lingering.</p>';

	$message .= '<p>We take this opportunity to offer our heartiest thanks for taking time out from your busy schedules to join us here. Your active participation in the interactive sessions helped all the delegates immensely. Our words may not be adequate to communicate our sense of gratitude for making the conference a success.</p>';

	$message .= '<p>Of course, in a conference of this magnitude, there would have been flaws and mistakes. We hope you will pardon us for these deficiencies.</p>';

	//$message .= '<p>We hope the over-riding and lasting memories will be happy and pleasant.</p>';

	$message .= '<p>Kindly click on the download icon below, to download your certificate.</p>';

	$message .= '<table border="1" style="border: 1px solid black;width: 100%;"><tr><th style="border: 1px solid black;">Registration ID</th><th style="border: 1px solid black;">Certificate </th><th style="border: 1px solid black;">DOWNLOAD</th></tr><tr><td style="border: 1px solid black;">' . $rowFetchUserDetails['user_registration_id'] . '</td><td style="border: 1px solid black;">Conference Registration</td><td style="border: 1px solid black;"><a href="' . $certificateLink . '" target="_blank"><img src="' . _BASE_URL_ . 'images/download.png"></a></td></tr></table>';

	$message .= '<br />';

	$message .= '<p>Once again, a big thanks to all of you. Wish you good health and happiness in the new year. </p>';



	//$message .= '		<br /><a href="'.$certificateLink.'">Click here to know the certification.</a>'; 


	$message .= '		<br /><br/>';
	$message .= '	</td></tr>';

	$message .= get_email_footer_certificate();
	$message .= '	</table>';

	$subject = "Participation Certificate_" . $cfg['EMAIL_CONF_NAME'];

	$transDetails = '';

	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for banquet dinner registration. " . $transDetails . ". Have a nice day.";

	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " now REGISTERED for banquet dinner of " . $cfg['EMAIL_CONF_NAME'] . " on " . $cfg['BANQUET_DINNER_DATE'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function certificate_workshop_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;

	$loginUrl              = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice 			=	array();

	$sqlInvoice['QUERY']    = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` = ? AND `delegate_id` = ? AND `status` = ?";
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'slip_id', 	    'DATA' => $slipId,         'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'delegate_id', 	'DATA' => $delegateId,     'TYP' => 's');
	$sqlInvoice['PARAM'][]	=	array('FILD' => 'status', 	  		'DATA' => 'A',             'TYP' => 's');

	$resInvoice = $mycms->sql_select($sqlInvoice);

	$fetchInvoice	=	$resInvoice[0];

	//$fetchInvoice	=	$resInvoice[0];		

	if ($fetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {

		$sqlDinner 		 =	array();
		$sqlDinner['QUERY'] = "SELECT request.*,user.user_full_name
									 FROM  " . _DB_REQUEST_DINNER_ . " request
							  INNER JOIN " . _DB_USER_REGISTRATION_ . " user
									  ON request.refference_id = user.id
								   WHERE request.delegate_id =? 
									 AND request.refference_slip_id = ? 
									 AND request.status = ?
									 AND user.status = ?
									 AND user.registration_payment_status != ?
									 AND request.payment_status != ?";

		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.delegate_id', 	    	'DATA' => $delegateId,    'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.refference_slip_id',  	'DATA' => $slipId,        'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.status', 	    			'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.status', 	   				'DATA' => 'A',            'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'user.registration_payment_status', 'DATA' => 'UNPAID',       'TYP' => 's');
		$sqlDinner['PARAM'][]	=	array('FILD' => 'request.payment_status', 	    	'DATA' => 'UNPAID',       'TYP' => 's');
		$resDinner = $mycms->sql_select($sqlDinner);
	}

	$certificateLink = _BASE_URL_ . 'print_workshop_certificate_pdf.php?id=' . $delegateId . "&type=workshop";

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr use="canvas"><td align="left" valign="top">';
	$message .= '		Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '		<br /><br />';

	$message .= '<p>New Year Greetings from ISAR 2024.</p>';

	$message .= '<p>ISAR 2024 gave us a unique opportunity to meet and interact with so many friends in Odisha. It was a few days of intense learning spiced with food and fun. The hangover of pleasant memories is still lingering.</p>';

	$message .= '<p>We take this opportunity to offer our heartiest thanks for taking time out from your busy schedules to join us here. Your active participation in the interactive sessions helped all the delegates immensely. Our words may not be adequate to communicate our sense of gratitude for making the conference a success.</p>';

	$message .= '<p>Of course, in a conference of this magnitude, there would have been flaws and mistakes. We hope you will pardon us for these deficiencies.</p>';

	//$message .= '<p>We hope the over-riding and lasting memories will be happy and pleasant.</p>';

	$message .= '<p>Kindly click on the download icon below, to download your certificate.</p>';

	$message .= '<table border="1" style="border: 1px solid black;width: 100%;"><tr><th style="border: 1px solid black;">Registration ID</th><th style="border: 1px solid black;">Certificate </th><th style="border: 1px solid black;">DOWNLOAD</th></tr><tr><td style="border: 1px solid black;">' . $rowFetchUserDetails['user_registration_id'] . '</td><td style="border: 1px solid black;">Workshop Registration</td><td style="border: 1px solid black;"><a href="' . $certificateLink . '" target="_blank"><img src="' . _BASE_URL_ . 'images/download.png"></a></td></tr></table>';

	$message .= '<br />';

	$message .= '<p>Once again, a big thanks to all of you. Wish you good health and happiness in the new year. </p>';



	//$message .= '		<br /><a href="'.$certificateLink.'">Click here to know the certification.</a>'; 


	$message .= '<br /><br/>';
	$message .= '</td></tr>';

	$message .= get_email_footer_certificate();
	$message .= '</table>';

	$subject = "Participation Workshop Certificate_" . $cfg['EMAIL_CONF_NAME'];

	$transDetails = '';

	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	$paysms	 = "Transaction SUCCESSFUL. " . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . " " . number_format($rowFetchPayment['amount'], 2) . " for banquet dinner registration. " . $transDetails . ". Have a nice day.";

	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " now REGISTERED for banquet dinner of " . $cfg['EMAIL_CONF_NAME'] . " on " . $cfg['BANQUET_DINNER_DATE'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$paystatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
		}

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

/****************************************************** Workshop Adjustment *******************************************************************/
function workshop_adjustment_confirmation_message($delegateId, $paymentId, $slipId, $invoiceId, $operation = 'SEND')
{
	global $cfg, $mycms;
	return false;
	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$fetchInvoice		   = getInvoiceDetails($invoiceId);

	$sqlWorkshop['QUERY'] = "SELECT request.*,classf.classification_title,classf.type,classf.workshop_date
						  FROM " . _DB_REQUEST_WORKSHOP_ . " request
					INNER JOIN " . _DB_WORKSHOP_CLASSIFICATION_ . " classf
							ON request.workshop_id = classf.id
						 WHERE request.delegate_id ='" . $delegateId . "' 
						   AND request.refference_slip_id = '" . $slipId . "'
						   AND request.refference_invoice_id = '" . $invoiceId . "'
						   AND request.status = 'A'
						   AND classf.status = 'A'";
	$resWorkshop = $mycms->sql_select($sqlWorkshop);
	$rowWorkshopDetails = $resWorkshop[0];


	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br /><br />';
	$message .= '      		Welcome to the ' . $cfg['EMAIL_WELCOME_TO'] . ' to be held from <strong>' . $cfg['EMAIL_CONF_HELD_FROM'] . ' </strong>at <strong>' . $cfg['EMAIL_CONF_VENUE'] . '</strong>';
	$message .= '    		<br /><br />';
	$message .= '			The workshop changes have been successfully incorporated. You are now registered for "' . $rowWorkshopDetails['classification_title'] . '" workshop.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Workshop Details:-</strong></u>';
	$message .= '			<br /><br />';

	$message .= '      		<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '				<tr><td width="30%">Workshop Name:</td><td>' . $rowWorkshopDetails['classification_title'] . '<br>* included dinner on ' . $cfg['BANQUET_DINNER_DATE'] . '</td></tr>';

	$message .= '			</table>';
	$message .= '			<br /><br />';

	if (floatval($rowFetchPayment['amount']) > 0.00) {
		$message .= '		<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '		<br /><br />';
		$message .= '		<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			 <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
									 </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId, $invoiceId);
		$message .= '		</table>';
		$message .= '		<br /><br />';
	}

	$message .= '			<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('terms_n_condition.php') . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('cancellation_n_refund.policy.php') . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cfg['BASE_URL'] . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Workshop Modification_Confirmed_" . $cfg['EMAIL_CONF_NAME'] . "";

	$regsms	 = 'Dear Delegate, the necessary workshop changes have been incorporated. You are now registered for "' . $rowWorkshopDetails['classification_title'] . '" workshop. Thank you for the co-operation. See you in Kolkata!';
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $regsms, "SEND",$regstatus);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']    = $message;
		$array['SMS_NO']	   = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0]  = $regsms;
		return $array;
	} else {
		return false;
	}
}

function complementary_workshop_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;

	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` ='" . $slipId . "' AND `delegate_id` ='" . $delegateId . "' AND `status` = 'A'";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
		$sqlWorkshop['QUERY'] = "SELECT request.*,classf.classification_title,classf.type,classf.workshop_date
							  FROM " . _DB_REQUEST_WORKSHOP_ . " request
						INNER JOIN " . _DB_WORKSHOP_CLASSIFICATION_ . " classf
								ON request.workshop_id = classf.id
							 WHERE request.delegate_id ='" . $delegateId . "' 
							   AND request.refference_slip_id = '" . $slipId . "' 
							   AND request.status = 'A'
							   AND classf.status = 'A'";
		$resWorkshop = $mycms->sql_select($sqlWorkshop);
		$rowWorkshopDetails = $resWorkshop[0];
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br /><br />';
	$message .= '			<br />Your workshop has been <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '.';

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Workshop Details:-</strong></u>';
	$message .= '			<br /><br />';

	$message .= '       	<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '				<tr><td width="30%">Workshop Name:</td><td>' . $rowWorkshopDetails['classification_title'] . '</td></tr>';
	$message .= '				<tr><td width="30%">Date</td><td>' . $rowWorkshopDetails['workshop_date'] . '</td></tr>';
	$message .= '		     <tr><td width="50%">Payment Mode</td><td>COMPLIMENTARY</td></tr>';
	$message .= '			</table>';
	$message .= '			<br /><br />';
	$message .= '		<br /><br />';

	$message .= '			<br /><a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('terms_n_condition.php') . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('cancellation_n_refund.policy.php') . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cfg['BASE_URL'] . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";

	$transDetails = '';

	$regsms	 = "Dear Delegate, " . $rowFetchUserDetails['user_full_name'] . " is now REGISTERED for " . $rowWorkshopDetails['classification_title'] . " Workshop in " . $cfg['EMAIL_CONF_NAME'] . ".";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $regsms, "SEND",$regstatus);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function complementary_dinner_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	return false;
	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$delagateCatagory      = getUserClassificationId($delegateId);
	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` ='" . $slipId . "' AND `delegate_id` ='" . $delegateId . "' AND `status` = 'A'";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$fetchInvoice	=	$resInvoice[0];

	if ($fetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
		$sqlDinner['QUERY'] = "SELECT request.*,user.user_full_name
							 FROM  " . _DB_REQUEST_DINNER_ . " request
							INNER JOIN " . _DB_USER_REGISTRATION_ . " user
							ON request.refference_id = user.id
							WHERE  request.delegate_id ='" . $delegateId . "' 
							AND request.refference_slip_id = '" . $slipId . "' 
							AND request.status = 'A'
							AND user.status = 'A'
							AND user.registration_payment_status != 'UNPAID'
							AND request.payment_status != 'UNPAID'";
		$resDinner = $mycms->sql_select($sqlDinner);
		$rowDinnerDetails = $resDinner[0];
	}


	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br /><br />';
	$message .= '			<br />Your dinner has been <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '.';

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '		<u><strong style="text-transform:uppercase;">Dinner Details:-</strong></u>';
	$message .= '		<br /><br />';

	$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td width="30%">Date:</td><td>' . $cfg['BANQUET_DINNER_DATE'] . '</td></tr>';
	$dinnercnt = 0;
	$acmponyCounter = 0;
	foreach ($resDinner as $key => $rowDinner) {
		if ($dinnercnt > 0) {
			$message .= '	<tr><td>' . $rowDinner['user_full_name'] . '</td></tr>';
			$acmponyCounter++;

			$acmpany[] = $rowDinner['user_full_name'];
		} else {
			$message .= '	<tr><td rowspan="' . sizeof($resDinner) . '" width="30%">Guest(s):</td><td>' . $rowDinner['user_full_name'] . '</td></tr>';
			$acmponyCounter++;

			$acmpany[]  = $rowDinner['user_full_name'];
		}
		$dinnercnt++;
	}
	$message .= '		<tr><td width="50%">Payment Mode</td><td>ZERO VALUE</td></tr>';
	$message .= '		</table>';

	$message .= '		<br /><br />';
	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<br /><a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cfg['BASE_URL'] . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";

	$transDetails = '';

	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " now REGISTERED for banquet dinner of " . $cfg['EMAIL_CONF_NAME'] . " on " . $cfg['BANQUET_DINNER_DATE'] . ". See you in Kolkata.";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $regsms, "SEND",$regstatus);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function complementary_accommodation_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $mycms, $cfg;
	//return false;  
	$loginUrl          	= _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);
	$sqlaccommodationDetails['QUERY']  = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,
	   										masterHotel.hotel_address AS hotel_address,package.package_name
												 FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
													INNER JOIN " . _DB_PACKAGE_ACCOMMODATION_ . " package
														ON accommodation.package_id = package.id
													INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
														ON masterHotel.id = package.hotel_id
											   WHERE accommodation.status = 'A' 
												 AND accommodation.user_id = '" . $delegateId . "'";

	$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
	$rowaccomm             = $resaccommodation[0];

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br /><br />';
	$message .= '    		Reservation <strong> CONFIRMED</strong>. You have booked your stay at ' . $rowaccomm['hotel_name'] . ', ' . $rowaccomm['hotel_address'] . ' for attending ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '	 <u><b>RESERVATION DETAILS</b></u>';
	$message .= ' 	<br /><br />';
	$message .= 	$invoiceOrderSummary;

	$message .= '		<br /><br />';

	$message .= '			<a href="' . $cfg['BASE_URL'] . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Stay Booking Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";

	$transDetails = '';
	$regsms	 = "Your reservation at " . $rowaccomm['hotel_name'] . ", " . $rowaccomm['hotel_address'] . " is CONFIRMED. Check-in 14:00 hrs. " . $rowaccomm['checkin_date'] . ", Check-out 12:00 hrs. on " . $rowaccomm['checkout_date'] . ".";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $regsms, "SEND",$regstatus);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function complementary_acompany_confirmation_message($delegateId, $paymentId, $slipId, $operation = 'SEND')
{
	global $cfg, $mycms;
	return false;
	$loginUrl          	= _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$rowFetchPayment 	   = getPaymentDetails($paymentId);
	$delagateCatagory      = getUserClassificationId($delegateId);

	$sqlInvoice['QUERY'] = "SELECT * FROM  " . _DB_INVOICE_ . " WHERE  `slip_id` ='" . $slipId . "' AND `delegate_id` ='" . $delegateId . "' AND `status` = 'A'";
	$resInvoice = $mycms->sql_select($sqlInvoice);
	$acmponyCounter = 0;
	foreach ($resInvoice as $keyInvoice => $rowInvoice) {
		if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails 				 = getUserDetails($rowInvoice['refference_id']);
			$sqlBanquet['QUERY'] = "SELECT * FROM  " . _DB_REQUEST_DINNER_ . " WHERE  `refference_id` ='" . $accompanyDetails['id'] . "' AND `delegate_id` ='" . $delegateId . "' AND `status` = 'A'";
			$resBanquet = $mycms->sql_select($sqlBanquet);
			$acmponyCounter++;

			$acmpany[]                       = $accompanyDetails['user_full_name'];
			if ($resBanquet) {
				$var = 'Taken';
			} else {
				$var = '-';
			}
			$Accompanymessage .= '			<tr><td><strong>' . $accompanyDetails['user_full_name'] . '</strong> </td><td align="center">' . $var . '</td><td> ' . $accompanyDetails['user_registration_id'] . ' </td></tr>';
		}
	}

	$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br /><br />';
	$message .= '			Your Accompanying Person(s) is/are <strong>REGISTERED</strong> for  ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';

	$message .= '			<br /><br />';
	$message .= '			Please save this e-mail for further reference.';
	$message .= '			<br /><br />';
	$message .= '			<u><strong style="text-transform:uppercase;">Registration Details-</strong></u>';
	$message .= '			<br /><br />';
	$message .= '      		<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '			<tr><td><strong>Registered Accompanying Person</strong> </td><td width="28%" align="center"><strong> Gala Dinner</strong></td><td width="35%" align="center"><strong> Registration Id </strong></td></tr>';
	$message .= '         ' . $Accompanymessage . '';
	$message .= '			<tr><td width="50%">Payment Mode</td><td>ZERO VALUE</td></tr>';
	$message .= '			</table><br />';
	$message .= '			<br /><br />';
	$message .= '			 <u><strong>Accompanying Persons are entitled for</strong></u>';
	$message .= '				<ul style="list-style-type:square;">';
	$message .= '					<li > Entry to Scientific Halls and Exhibition Area</li>';
	$message .= '				  	<li> Tea/Coffee during the Conference at the venue</li>';
	$message .= '				  	<li> Lunch on 5th, 6th and 7th September 2019. </li>';
	$message .= '				</ul>';

	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '			<br /><a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . _BASE_URL_ . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at <u><strong>' . $cfg['ADMIN_REGISTRATION_EMAIL'] . '</strong></u>';
	$message .= '			<br /><br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";
	$transDetails = '';
	$regsms	 = "Dear Delegate, " . implode(", ", $acmpany) . " " . ($acmponyCounter == 1 ? 'is' : 'are') . " REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . " as accompanying person(s) of " . $rowFetchUserDetails['user_full_name'] . ". See you in Kolkata.";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");

		$regstatus = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $regsms, "SEND",$regstatus);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']   = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $regsms;
		return $array;
	} else {
		return false;
	}
}

/****************************************************** Spot *******************************************************************/
function offline_spot_conference_payment_confirmation_message($delegateId, $slipId, $paymentId, $operation = 'SEND')
{

	global $mycms, $cfg;
	return false;
	$rowFetchUserDetails  = getUserDetails($delegateId);
	$rowFetchPayment 	  = getPaymentDetails($paymentId);
	$smsString = " ";

	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	<td align="left" valign="top">';
	$message .= '	<strong>Dear ' . $rowFetchUserDetails['user_full_name'] . ',</strong>';
	$message .= '	<br /><br />';
	if (floatval($rowFetchPayment['amount']) > 0) {
		$message .= '	Transaction <strong>SUCCESSFUL</strong>.' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . $rowFetchPayment['amount'] . ' has been received by ' . $cfg['ADMIN_NAME1'] . ' for the registration in
						 ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';
	}
	$message .= '   <br /><br />';
	$message .= '	** Invoice and Registration Confirmation will be mailed to the e-mail id of each registrant, individually. ';
	$message .= '   <br />';
	$message .= '	<strong><u>PAYMENT DETAILS</u></strong>';
	$message .= '	<br /><br />';
	$message .= '	<table width="100%" border="1">';
	$message .= '		<tr>';
	$message .= '			<td width="35%">Receiver :</td>';
	$message .= '			<td>' . $cfg['EMAIL_CONF_NAME'] . '</td>';
	$message .= '		</tr>';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cash":
			$message .=	'				<tr>';
			$message .=	'					<td>Date  :</td>';
			$message .=	'					<td>' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '</td>';
			$message .=	'				</tr>';
			break;

		case "Cheque":
			$message .=	'				<tr>';
			$message .=	'					<td>Date  :</td>';
			$message .=	'					<td>' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '</td>';
			$message .=	'				</tr>';
			break;

		case "Draft":
			$message .=	'				<tr>';
			$message .=	'					<td>Date  :</td>';
			$message .=	'					<td>' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '</td>';
			$message .=	'				</tr>';
			break;

		case "NEFT":
			$message .=	'				<tr>';
			$message .=	'					<td>Date  :</td>';
			$message .=	'					<td>' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '</td>';
			$message .=	'				</tr>';
			break;

		case "RTGS":
			$message .=	'				<tr>';
			$message .=	'					<td>Date  :</td>';
			$message .=	'					<td>' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '</td>';
			$message .=	'				</tr>';
			break;
	}

	$message .= '		<tr>';
	$message .= '			<td>Slip Amount :</td>';
	$message .= '			<td>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . $rowFetchPayment['amount'] . '</td>';
	$message .= '		</tr>';
	$message .= '		<tr>';
	$message .= '			<td>Payment Voucher. :</td>';
	$message .= '			<td>' . getSlipNumber($slipId) . '</td>';
	$message .= '		</tr>';
	$message .= '		<tr>';
	$message .= '			<td>Payment Mode:</td>';
	$message .= '			<td>' . $rowFetchPayment['payment_mode'] . '</td>';
	$message .= '		</tr>';

	switch ($rowFetchPayment['payment_mode']) {


		case "Cheque":
			$message .= '	<tr>';
			$message .= '		<td>Cheque No. :</td>';
			$message .= '		<td>' . $rowFetchPayment['cheque_number'] . '</td>';
			$message .= '	</tr>';
			$message .= '	<tr>';
			$message .= '		<td>Drawee Bank :</td>';
			$message .= '		<td>' . $rowFetchPayment['cheque_bank_name'] . '</td>';
			$message .= '	</tr>';
			$smsString = "Cheque No. " . $rowFetchPayment['neft_transaction_no'];
			break;
		case "Draft":
			$message .= '	<tr>';
			$message .= '		<td>DD No. :</td>';
			$message .= '		<td>' . $rowFetchPayment['draft_number'] . '</td>';
			$message .= '	</tr>';
			$message .= '	<tr>';
			$message .= '		<td>Drawee Bank :</td>';
			$message .= '		<td>' . $rowFetchPayment['draft_bank_name'] . '</td>';
			$message .= '	</tr>';
			$smsString = "DD No. " . $rowFetchPayment['neft_transaction_no'];
			break;
		case "NEFT":
			$message .= '	<tr>';
			$message .= '		<td>NEFT Transaction Id :</td>';
			$message .= '		<td>' . $rowFetchPayment['neft_transaction_no'] . '</td>';
			$message .= '	</tr>';
			$message .= '	<tr>';
			$message .= '		<td>Drawee Bank :</td>';
			$message .= '		<td>' . $rowFetchPayment['neft_bank_name'] . '</td>';
			$message .= '	</tr>';
			$smsString = "NEFT Transaction Id " . $rowFetchPayment['neft_transaction_no'];
			break;
		case "RTGS":
			$message .= '	<tr>';
			$message .= '		<td>RTGS Transaction Id :</td>';
			$message .= '		<td>' . $rowFetchPayment['rtgs_transaction_no'] . '</td>';
			$message .= '	</tr>';
			$message .= '	<tr>';
			$message .= '		<td>Drawee Bank :</td>';
			$message .= '		<td>' . $rowFetchPayment['rtgs_bank_name'] . '</td>';
			$message .= '	</tr>';
			$smsString = "RTGS Transaction Id " . $rowFetchPayment['rtgs_transaction_no'];
			break;
	}

	$message .= '		</table>';
	$message .= '			<br /><br />';
	$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
	$message .= '			<br /><br />';
	$message .= '			<table width="100%" border="1">';
	$message .= '			  <tr>	
										<td width="15%"><b>Invoice No.</b></td>
										<td width="70%"><b>Invoice for</b></td>
										<td width="20%" align="center"><b>Download</b></td>
								  </tr>';
	$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
	$message .= '			</table>';
	$message .= '	</td>';
	$message .= '</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject .= 'Payment Confirmation _' . $cfg['EMAIL_CONF_NAME'] . '';

	// COMPOSING SMS

	$sms	  = "Transaction SUCCESSFUL. " . $rowFetchPayment['currency'] . ' ' . $rowFetchPayment['amount'] . " has been recieved for " . $cfg['ADMIN_NAME1'] . " for the registration in " . $cfg['EMAIL_CONF_NAME'] . ". " . $smsString . ". Have a nice day.";
	if ($operation == 'SEND') {

		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message);

		if (floatval($rowFetchPayment['amount']) > 0) {
			$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms);
		}
		return true;
	} elseif ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}

function offline_sopt_conference_registration_confirmation_message($delegateId, $slipId, $paymentId, $operation = 'SEND')
{
	global $mycms, $cfg;
	return false;
	$loginUrl          	   = _BASE_URL_;
	$rowFetchUserDetails   = getUserDetails($delegateId);
	$invoiceOrderSummary   = generateInvoiceOrderSummary($delegateId, $slipId);
	$rowFetchPayment 	  = getPaymentDetails($paymentId);
	$user_password     	   = $mycms->decoded($rowFetchUserDetails['user_password']);
	$sqlaccommodation['QUERY'] 	   = "SELECT * FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
									   WHERE status = 'A' 
										 AND user_id = '" . $delegateId . "' ";

	$resaccom			   = $mycms->sql_select($sqlaccommodation);
	$rowaccomm             = $resaccom[0];
	$slipAmount = invoiceAmountOfSlip($slipId);
	// COMPOSING EMAIL
	$message = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= get_email_header();

	$message .= '<tr>';
	$message .= '	 <td align="left" valign="top">';
	$message .= '	 Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '	 <br /><br />';
	if ($rowFetchUserDetails['operational_area'] == "COUNTER" && ($rowFetchPayment['payment_mode'] == 'Cheque' || $rowFetchPayment['payment_mode'] == 'Draft')) {
	} else {
		$message .= '      Welcome to the ' . $cfg['EMAIL_WELCOME_TO'] . ' to be held from <strong>' . $cfg['EMAIL_CONF_HELD_FROM'] . ' </strong>at <strong>' . $cfg['EMAIL_CONF_VENUE'] . '</strong>';
		$message .= '    <br /><br />';
	}
	if ($rowFetchUserDetails['registration_classification_id'] != 14 && ($slipAmount > 0)) {
		$message .= '    Payment <strong>SUCCESSFUL.</strong> ' . $cfg['EMAIL_CONF_NAME'] . ' has received ' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '  for your registration.';
		$message .= '    <br /><br />';
	}
	$message .= '    You are <strong>REGISTERED</strong> for ' . $cfg['EMAIL_CONF_NAME'] . '. Please save this e-mail for further reference.';

	$message .= '    <br /><br />';
	$message .= ' 	 <u>Please note the following :-</u>';
	$message .= ' 	 <br /><table style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	 <tr><td><strong>Registered E-mail Id: </strong></td><td>' . $rowFetchUserDetails['user_email_id'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registered Phone Number: </strong></td><td>' . $rowFetchUserDetails['user_mobile_no'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Unique Sequence: </strong></td><td>' . $rowFetchUserDetails['user_unique_sequence'] . '</td></tr>';
	$message .= '	 <tr><td><strong>Registration Id: </strong></td><td>' . $rowFetchUserDetails['user_registration_id'] . '</td></tr>';
	$message .= ' 	 </table><br /><br />';

	if ($rowFetchUserDetails['registration_classification_id'] != 14 && ($slipAmount > 0)) {
		$message .=	'<b><u>TRANSACTION DETAILS</u></b>';
		$message .=	'	 <br /><br />';
		$message .=	'	 Payment has been done through offline process.';
		$message .= '       <table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			<tr><td>Receiver :</td><td> ' . $cfg['EMAIL_CONF_NAME'] . '</td>';
		$message .= '			<tr><td>Date :</td><td>';




		switch ($rowFetchPayment['payment_mode']) {
			case "Cash":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cash_deposit_date'])) . '';
				break;

			case "Cheque":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['cheque_date'])) . '';
				break;

			case "Draft":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['draft_date'])) . '';
				break;

			case "NEFT":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['neft_date'])) . '';
				break;

			case "RTGS":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['rtgs_date'])) . '';
				break;
			case "Card":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['card_payment_date'])) . '';
				break;
			case "Credit":
				$message .=	'					' . date("jS F, Y", strtotime($rowFetchPayment['credit_date'])) . '';
				break;
		}
		$message .=	'	 </td>';
		$message .=	'	<tr> <td>Amount ' . ($offlineFrom == 'BACK' ? '(Discounted)' : '') . ' : </td> <td>' . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . '</td></tr>';
		$message .=	'	<tr>  <td>Mode of Payment :</td> <td>' . $rowFetchPayment['payment_mode'] . '</td></tr> ';
		$message .=	'	 <tr>';
		switch ($rowFetchPayment['payment_mode']) {
			case "Cheque":
				$message .= '		<tr><td>Cheque No. :</td><td> ' . $rowFetchPayment['cheque_number'] . '</td></tr>';
				$message .= '		<tr><td>Drawee Bank :</td><td> ' . $rowFetchPayment['cheque_bank_name'] . '</td></tr>';
				break;
			case "Draft":
				$message .= '		<tr><td>DD No. :</td><td>' . $rowFetchPayment['draft_number'] . '</td></tr>';
				$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['draft_bank_name'] . '</td></tr>';
				break;
			case "NEFT":
				$message .= '		<tr><td>NEFT Transaction Id :</td><td>' . $rowFetchPayment['neft_transaction_no'] . '</td></tr>';
				$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['neft_bank_name'] . '</td></tr>';
				break;
			case "RTGS":
				$message .= '		<tr><td>RTGS Transaction Id :</td><td>' . $rowFetchPayment['rtgs_transaction_no'] . '</td></tr>';
				$message .= '		<tr><td>Drawee Bank :</td><td>' . $rowFetchPayment['rtgs_bank_name'] . '</td></tr>';
				break;
			case "Card":
				$message .= '		<tr><td>Card Number :</td><td>' . $rowFetchPayment['card_transaction_no'] . '</td></tr>';

				break;
		}
		$message .=	'	 </table>';
		$message .= '		<br /><br />';
	}
	$message .= '	 <u><b>REGISTRATION DETAILS</b></u><br /><br>';
	$message .= ' 	 ';
	$message .= 	$invoiceOrderSummary;

	$message .= '			<br /><br />';

	if ($slipAmount > 0) {
		$message .= '			<strong><u>INVOICE DETAILS</u></strong>';
		$message .= '			<br /><br />';
		$message .= '			<table border="1" cellpadding="1" cellspacing="0"  width = "60%" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= '			  <tr>	
										<td style="width:35%; border-right:1px solid #000; border-bottom:1px solid #000;"><b>Invoice No.</b></td>
										<td style="width:45%; border-right:1px solid #000; border-bottom:1px solid #000;"><b>Invoice for</b></td>
										<td style="width:30%; border-right:1px solid #000; border-bottom:1px solid #000;" align="center"><b>Download</b></td>
								  </tr>';
		$message .= 			getInvoiceMailerDetails($delegateId, $slipId);
		$message .= '			</table>';
		$message .= '    <br /><br />';
	}
	$delagateCatagory      = getUserClassificationId($delegateId);
	$message .=            registration_note();

	$intArr	 = array(5, 6, 7, 10, 11, 12); // Combo Ids



	$termAndcondition     		   = _BASE_URL_ . 'terms.php';
	$cancellAndcondition     	   = _BASE_URL_ . 'cancellation.php';
	$message .= '<a href="' . $termAndcondition . '">Click here to know the terms and condition.</a>';
	$message .= '<br /><br/>';
	$message .= '<a href="' . $cancellAndcondition . '">Click here to know the cancellation and refund policy.</a>';
	$message .= '			<br /><br/>';
	$message .= '			<a href="' . $loginUrl . '">Click here</a> to log into your ' . $cfg['EMAIL_CONF_NAME'] . ' user account.';
	$message .= '			<br /><br/>';
	$message .= '			For more information please write at   ' . $cfg['ADMIN_REGISTRATION_EMAIL'];
	$message .= '			<br /><br/>';
	$message .= ' 	 </td>';
	$message .= ' </tr>';
	$message .= get_email_footer();
	$message .= '</table>';
	$subject  = "Registration Confirmation_" . $cfg['EMAIL_CONF_NAME'] . "";
	$transDetails = '';
	switch ($rowFetchPayment['payment_mode']) {
		case "Cheque":
			$transDetails = 'cheque no ' . $rowFetchPayment['cheque_number'] . '.';
			break;
		case "Draft":
			$transDetails = 'DD no ' . $rowFetchPayment['draft_number'] . '.';
			break;
		case "NEFT":
			$transDetails = 'UTR no ' . $rowFetchPayment['neft_transaction_no'] . '.';
			break;
		case "RTGS":
			$transDetails = 'UTR no ' . $rowFetchPayment['rtgs_transaction_no'] . '.';
			break;
		case "CARD":
			$transDetails = 'Card no ' . $rowFetchPayment['card_transaction_no'] . '.';
			break;
	}

	if ($rowFetchUserDetails['registration_classification_id'] != 14 && ($slipAmount > 0)) {
		$paysms = "" . $cfg['EMAIL_CONF_NAME'] . " has received " . getRegistrationCurrency(getUserClassificationId($delegateId)) . '. ' . number_format($rowFetchPayment['amount'], 2) . " from " . $rowFetchUserDetails['user_full_name'] . ". " . $transDetails . ". Have a nice day.";
	}
	$regsms      = "You are now REGISTERED for " . $cfg['EMAIL_CONF_NAME'] . ". Your Unique Sequence : " . $rowFetchUserDetails['user_unique_sequence'] . ", Registration Id : " . $rowFetchUserDetails['user_registration_id'] . ", Registered E-mail Id : " . $rowFetchUserDetails['user_email_id'] . ".";
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $subject, $message, "SEND");
		if ($rowFetchUserDetails['registration_classification_id'] != 14 && $by == 'DELEGATE') {
			$status1 = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $paysms);
			//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $sms, "SEND",$status1);
		}
		$status2 = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $regsms);
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $sms, "SEND",$status2);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_NO'] 	  = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0] = $paysms;
		$array['SMS_BODY'][1] = $regsms;
		return $array;
	} else {
		return false;
	}
}

function abstract_submission_acknowledgement($id, $delegateId, $operation = 'SEND')
{
	global $cfg, $mycms;
	return false;
	$loginUrl          	   = _BASE_URL_; //"http://imscon2019.com/gotoportal.php";
	$rowFetchUserDetails   = getUserDetails($delegateId);

	$sqlAbstractDetails['QUERY']		=	"SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
									  WHERE `id` = '" . $id . "' ";

	$abstractDetails		=	$mycms->sql_select($sqlAbstractDetails, false);

	$rowAbstractDetails  	=	$abstractDetails[0];

	$sqlAbstractTopic['QUERY']		=	"SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
									  WHERE `id` = '" . $rowAbstractDetails['abstract_topic_id'] . "' ";

	$abstractTopic		=	$mycms->sql_select($sqlAbstractTopic, false);
	$tag  = ($rowAbstractDetails['tags'] == 'CASE REPORT' ? 'Case Report' : 'Abstract');

	$message  = '<table width="800"  align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			Your <strong>' . ucwords($tag) . '</strong> has been submitted.';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			<strong>Submission Reference No. : </strong>' . $rowAbstractDetails['abstract_submition_code'];
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			<strong>Topic:</strong> ' . $abstractTopic[0]['abstract_topic'];
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			<strong>Title: </strong>' . $rowAbstractDetails['abstract_title'];
	$message .= '			<br/><br/><br/>';
	$message .= '			<i>Please note :-</i>';
	$message .= '				<ul >';
	$message .= '					<li>Only <strong>1 Abstract / Case Report</strong> will be accepted from a delegate.</li>';
	$message .= '					<li>For any <strong>edit/update</strong>, please visit your user profile.</li>';
	$message .= '					<li>Your <strong>Submission Code</strong> will be needed for further references.<br /></li>';
	$message .= '					<br />';
	$message .= '				</ul>';
	$message .= '			<i>Timeline:</i>';
	$message .= '				<ul style="list-style-type:none;">';
	$message .= '					<li>Last date of Abstract Submission 20.02.2019</li>';
	$message .= '					<li>Last date of Abstract Revision 20.02.2019</li>';
	$message .= '					<li>Acceptance Notification will be sent by 05.03.2019</li>';
	$message .= '					<li>Presentation Guidelines will be sent by 15.03.2019</li>';
	$message .= '				</ul>';
	$message .= '			If needed, you may write us to abstract@aiccrcog2019.com<br /><br /><br />';
	$message .= '	 	    <a href="' . $loginUrl . '" target="_blank">Click here</a> to log into your account.<br /><br /><br />';

	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_email_footer();
	$message .= '</table>';

	$subject = "Abstract Submission Acknowledgement_" . $cfg['EMAIL_CONF_NAME'] . " ";

	$sms	 = "Your " . ($rowAbstractDetails['tags'] == 'CASE REPORT' ? 'Case Report' : 'Abstract') . " has been submitted for " . $abstractTopic[0]['abstract_topic'] . ". Submission Reference No. " . $rowAbstractDetails['abstract_submition_code'] . ". Please mention the SR No. for further queries. Keep visiting www.aiccrcog2019.com for updates.";
	//Your #field1# has been submitted for #field2#. Submission Reference No. #field3#. Please mention the #field4# for further queries. Keep visiting #field5# for updates.		
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['comunication_email'], $cfg['ADMIN_NAME'], 'secretariat@aiccrcog2019.com', $subject, $message, "SEND");

		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms, 'Trans');
		//insertSMSRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_mobile_no'], $sms, "SEND",$status);

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}

/*function abstract_submission_message($delegateId, $submissionId, $operation='SEND')
	{  	  
		global $mycms, $cfg; 
		$loginUrl     		    = _BASE_URL_."login.php";
		$rowFetchUserDetails    = getUserDetails($delegateId);
		$invoiceOrderSummary    = generateInvoiceOrderSummary($delegateId, $slipId);
		$abstractDetails        = abstractDetailsQuerySet($submissionId);
		$resultabstractDelegate = $mycms->sql_select($abstractDetails);
		$abstractQueryDetails   = $resultabstractDelegate[0];
		
		$sqlAbstractTopic				= array();
		$sqlAbstractTopic['QUERY']		= "SELECT * FROM "._DB_ABSTRACT_TOPIC_." 
									  		WHERE `id` = '".$abstractQueryDetails['abstract_topic_id']."' ";									
		$abstractTopic					= $mycms->sql_select($sqlAbstractTopic, false);
		
		$sql				  = array();
		$sql['QUERY']         = " SELECT award_name 
									FROM "._DB_AWARD_REQUEST_." awardReq
							  INNER JOIN "._DB_AWARD_MASTER_." award
									  ON award.id = awardReq.award_id
								   WHERE awardReq.`applicant_id` = '".$delegateId."'
								     AND awardReq.`submission_id` = '".$submissionId."'
									 AND awardReq.`status`= 'A'";													   
		$resultUsertAward 	  = $mycms->sql_select($sql, false);
		$nominations 		  = array();
		foreach($resultUsertAward as $kk=>$row)
		{
			$nominations[] = $row['award_name'];
		}
		if(empty($nominations))
		{
			$nominations[] = '-';
		}

		$sqlMail 	=	array();
		$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM "._DB_EMAIL_TEMPLATE_." 
					       WHERE status = ? 
						     AND  id = ? ";
			$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' =>'A',   		  				'TYP' => 's');	
			$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' =>9,   'TYP' => 's');														 
			$resMail			   = $mycms->sql_select($sqlMail);
			$rowaMail              = $resMail[0];

		$sql 	=	array();
		$sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
													WHERE `status`='A' order by id desc limit 1";
							 //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
		$result = $mycms->sql_select($sql);
		$row = $result[0];

		$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['header_image'];
		$footer_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['footer_image'];
		if($row['header_image']!='')
		{
			$emailHeader  = $header_image;
		}

		if($row['footer_image']!='')
		{
			$emailFooter  = $footer_image;
		}

		$mailTemplateDescription = $rowaMail['description'];

		$abstrctType = ($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?'Case Report':'Abstract');
		$abstrctDate = ($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?$cfg['CASEREPORT.SUBMIT.LASTDATE']:$cfg['ABSTRACT.SUBMIT.LASTDATE']);

		$find = ['[NAME]', '[ABSTRACT_TYPE]', '[REFERENCE_NO]', '[ABSTRACT_DATE]', '[TOPIC]', '[ABSTRACT_TITLE]'];

		$replacement = [$rowFetchUserDetails['user_full_name'],$abstrctType,$abstractQueryDetails['abstract_submition_code'], $abstrctDate,$abstractTopic[0]['abstract_topic'],$abstractQueryDetails['abstract_title']];
		

		 $result = str_replace($find, $replacement, $mailTemplateDescription);

		 $message = '';
		
		$message .= '<img src="'.$emailHeader.' " width="100%" alt="header" /><br/><br/>';
		$message.=$result;
		$message .= '<img src="'.$emailFooter.' " width="100%" alt="header" /><br/><br/>';
		
		
	    // COMPOSING EMAIL
		$message  = '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
		$message .= get_email_header();
		
		$message .= '	<tr>';
		$message .= '		<td height="" align="left" valign="top">&nbsp;</td>';
		$message .= '	</tr>';
		$message .= '	<tr>';
		$message .= '		<td align="left" valign="top">';
		$message .= '			<strong>Dear '.$rowFetchUserDetails['user_full_name'].'</strong>';
		$message .= '			<br /><br />';
		$message .= '	         Your '.($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?'Case Report':'Abstract').' has been submitted.';
		$message .= '			<br /><br />';
		$message .= ' 	 		<table>';
		$message .= ' 	 			<tr>';
		$message .= ' 	 				<td colspan="2">Submission Reference No';
		$message .= ' 	 				: <strong>'.$abstractQueryDetails['abstract_submition_code'].'</strong></td>';
		$message .= '    			</tr>';		
		$message .= ' 	 			<tr>';
		$message .= ' 	 				<td colspan="2"><b><i>Registration is mandatory for all oral/poster presenters by '.$mycms->cDate('F d, Y',(($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?$cfg['CASEREPORT.SUBMIT.LASTDATE']:$cfg['ABSTRACT.SUBMIT.LASTDATE']))).'.<br>';
		$message .= ' 	 				The Presenter must be one of the Authors.</i></b></td>';
		$message .= '    			</tr>';	
		$message .= ' 	 			<tr>';
		$message .= ' 	 				<td colspan="2">Topic:';
		$message .= ' 	 				 <strong>'.$abstractTopic[0]['abstract_topic'].'</strong></td>';
		$message .= '    			</tr>';
		$message .= ' 	 			<tr>';
		$message .= ' 	 				<td colspan="2">Title';
		$message .= '  	  				: <strong>'.$abstractQueryDetails['abstract_title'].'</strong></td>';
		$message .= '     			</tr>';
		//$message .= ' 	 			<tr>';
		//$message .= ' 	 				<td colspan="2">Nomination';
		//$message .= '  	  				: <strong>'.implode(",",$nominations).'</strong></td>';
		//$message .= '     			</tr>';
		$message .= ' 	 		</table>';		
		$message .= ' 	 <br />';
		$message .= '    Please note :-  ';
		$message .= '    <br />  ';
		$message .= ' 	 <div style="padding-left:5%;">';
		//$message .= ' 	 	&bull;&nbsp;&nbsp;	There is no limitation to the number of abstracts that can be submitted by a delegate.';
		//$message .= ' 	 	<br />';
		$message .= ' 		 &bull;&nbsp;&nbsp;	For<b> any edit/update</b> in your submitted '.($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?'Case Report':'Abstract').', please visit your user profile. Resubmission of the same abstract must be avoided.';
		$message .= ' 	 	<br />';
		$message .= ' 	 	&bull;&nbsp;&nbsp;	<b>Last date</b> of submission/revision '.($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?$cfg['CASEREPORT.SUBMIT.LASTDATE']:$cfg['ABSTRACT.SUBMIT.LASTDATE']).'.';
		$message .= '    </div>';
		$message .= ' 	 <br /><br />';
		$message .= '	 Your <b>Submission Code</b> may be needed for further references.';
		$message .= '	 <br /><br />';
		$message .= '	 If needed, you may write at neocon2022@gmail.com';
		$message .= '	 <br /><br />';
		$message .= '		</td>';
		$message .= '	</tr>';
		
		$message .= get_email_footer();
		$message .= '</table>';
		$subject  = $rowaMail['subject'];
		
		$sms	  = "Your ".($abstractQueryDetails['abstract_parent_type']=='CASE REPORT'?'Case Report':'Abstract')." has been submitted for ".$cfg['EMAIL_CONF_NAME'].".Ref No. ".$abstractQueryDetails['abstract_submition_code'].". Please mention the Ref No. for further queries. Keep visiting ".$cfg['EMAIL_CONF_WEBSITE']." for updates. Submitted abstract can be revised by ".$cfg['ABSTRACT.SUBMIT.LASTDATE'].".";
		
		if($operation=='SEND')
		{
			$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);
			
			$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms, 'Informative');
			
			
			return true;
		}
		else if($operation=='RETURN_TEXT')
		{
			$array = array();
			$array['MAIL_SUBJECT'] = $subject;
			$array['MAIL_BODY'] = $message;
			$array['SMS_BODY'] = $sms;
			
			return $array;
		}
		else
		{
			return false;
		}
	
	
	}*/

/****************************************************** Abstract *******************************************************************/
function abstract_submission_message($delegateId, $submissionId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$loginUrl     		    = _BASE_URL_;
	$rowFetchUserDetails    = getUserDetails($delegateId);
	$invoiceOrderSummary    = generateInvoiceOrderSummary($delegateId, $slipId);
	$abstractDetails        = abstractDetailsQuerySet($submissionId);
	$resultabstractDelegate = $mycms->sql_select($abstractDetails);
	// echo "<pre>";
	// print_r($resultabstractDelegate);
	// die;
	$abstractQueryDetails   = $resultabstractDelegate[0];

	$sqlAbstractTopic				= array();
	$sqlAbstractTopic['QUERY']		= "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
									  		WHERE `id` = '" . $abstractQueryDetails['abstract_topic_id'] . "' ";
	$abstractTopic					= $mycms->sql_select($sqlAbstractTopic, false);

	//echo '<pre>'; print_r($abstractQueryDetails['abstract_cat']);

	$sql				  = array();
	$sql['QUERY']         = " SELECT award_name 
									FROM " . _DB_AWARD_REQUEST_ . " awardReq
							  INNER JOIN " . _DB_AWARD_MASTER_ . " award
									  ON award.id = awardReq.award_id
								   WHERE awardReq.`applicant_id` = '" . $delegateId . "'
								     AND awardReq.`submission_id` = '" . $submissionId . "'
									 AND awardReq.`status`= 'A'";
	$resultUsertAward 	  = $mycms->sql_select($sql, false);
	$nominations 		  = array();
	foreach ($resultUsertAward as $kk => $row) {
		$nominations[] = $row['award_name'];
	}
	if (empty($nominations)) {
		$nominations[] = '-';
	}


	$sqlSelectCategory	= array();
	$sqlSelectCategory['QUERY']  = "SELECT `category` 
												   FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . "
												 WHERE `id` = '" . $abstractQueryDetails['abstract_cat'] . "' AND status='A'";



	$resultSelectCategory = $mycms->sql_select($sqlSelectCategory);


	$sqlSubCat			 = array();
	$sqlSubCat['QUERY']  = "SELECT `abstract_submission` 
												   FROM " . _DB_ABSTRACT_SUBMISSION_ . "
												 WHERE `id` = '" . $abstractTopic[0]['sub_category'] . "' AND status='A'";


	$resultSubCat = $mycms->sql_select($sqlSubCat);

	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM " . _DB_EMAIL_TEMPLATE_ . " 
					       WHERE status = ? 
						     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 8,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
										WHERE `title` = 'Abstract Submission'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

	$logo = '<a href="#" target="_blank" text-decoration: none; border: 0;"><img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" alt="logo" width="310" height="" style="display: block; border: 0;" /></a>';

	// $img =  _BASE_URL_ . 'images/mailer/abstract-success.png';
	$footer_div = '<div class="alignment" align="center" style="line-height:10px">
                                    <img src="' . $footer_image . '" style="display:block;height:auto;border:0;max-width:150px;width:100%" width="150" alt="Side Ornaments" title="Side Ornaments">
                                  </div>';
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';
	$line = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1 = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2 = _BASE_URL_ . 'images/mailer/line-after.png';

	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);

	$find = [
		'[UNIQUE_ID]', '[USERNAME]', '[REG_ID]', '[EMAIL]', '[MOBILE]', '[ABSTRACT_TYPE]', '[REFFERENCE_NO]',
		'[TOPIC]', '[ABSTRACT_TITLE]', '[ABSTRACT_DATE]', '[LINE]','[LINE1]','[LINE2]',
		 '[LOGO]', '[IMG]', '[FOOTER_IMG]', '[REG_LINK]','[CONF_MOBILE]','[CONF_EMAIL]', 'c7fafc', '84eef3', '173cb8'
	];

	$replacement = [
		$rowFetchUserDetails['user_unique_sequence'], $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_registration_id'],
		$rowFetchUserDetails['user_email_id'], $rowFetchUserDetails['user_mobile_no'],
		$abstractQueryDetails['tags'], $abstractQueryDetails['abstract_submition_code'],
		$abstractTopic[0]['abstract_topic'], $abstractQueryDetails['abstract_title'], $cfg['ABSTRACT.SUBMIT.LASTDATE'],
		$line,$line1,$line2,
		$logo, $img, $footer_img, _BASE_URL_, $cfg['EMAIL_CONF_CONTACT_US'],$cfg['EMAIL_CONF_EMAIL_US'], $cfg['light_color'], $cfg['color'], $cfg['dark_color']
	];


	$result = str_replace($find, $replacement, $mailTemplateDescription);
	$message = '';

	//$message .= '<img src="'.$emailHeader.' " width="100%" alt="header" /><br/><br/>';
	$message .= $result;
	//$message .= '<img src="'.$emailFooter.' " width="100%" alt="header" /><br/><br/>';

	//echo $message; 

	//die;


	$subject  = $rowaMail['subject'];

	$sms	  = "Your " . ($abstractQueryDetails['abstract_parent_type'] == 'CASE REPORT' ? 'Case Report' : 'Abstract') . " has been submitted for " . $cfg['EMAIL_CONF_NAME'] . ".Ref No. " . $abstractQueryDetails['abstract_submition_code'] . ". Please mention the Ref No. for further queries. Keep visiting " . $cfg['EMAIL_CONF_WEBSITE'] . " for updates. Submitted abstract can be revised by " . $cfg['ABSTRACT.SUBMIT.LASTDATE'] . ".";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);

		$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms, 'Informative');


		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}


function abstract_acceptance_message($id, $delegateId, $operation = 'SEND')
{
	global $cfg, $mycms;
	return false;
	$loginUrl          	    = _BASE_URL_; //"http://imscon2019.com/gotoportal.php";
	$rowFetchUserDetails    = getUserDetails($delegateId);

	$sqlAbstractDetails['QUERY']		=	"SELECT * FROM " . _DB_ABSTRACT_REQUEST_ . " 
									  WHERE `id` = '" . $id . "' ";
	$abstractDetails		=	$mycms->sql_select($sqlAbstractDetails, false);
	$rowAbstractDetails  	=	$abstractDetails[0];

	$sqlAbstractTopic['QUERY']		=	"SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
									  WHERE `id` = '" . $rowAbstractDetails['abstract_topic_id'] . "' ";

	$abstractTopic			=	$mycms->sql_select($sqlAbstractTopic, false);
	$tag  					= ($rowAbstractDetails['tags'] == 'CASE REPORT' ? 'Case Report' : 'Abstract');

	$message  = '<table width="800"  align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">';
	$message .= get_email_header();
	$message .= '	<tr>';
	$message .= '		<td align="left" valign="top">';
	$message .= '			Dear ' . $rowFetchUserDetails['user_full_name'] . ',';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			Thank you for having submitted an abstract for ' . $cfg['EMAIL_WELCOME_TO'] . ', which will be held ' . $cfg['EMAIL_CONF_VENUE'] . ' from ' . $cfg['EMAIL_CONF_HELD_FROM'] . '. Please note that your abstract entitled <b>"' . $rowAbstractDetails['abstract_title'] . '"</b> was evaluated anonymously by 3 referees and as a result, your abstract has been accepted for <b>' . $rowAbstractDetails['abstract_parent_type'] . ' presentation.</b>';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			<b>Slot allocation will be informed shortly along with the Presentation Guidelines.</b>';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			If for any reason you are unable to attend the meeting, please arrange for one of the co-authors of your abstract to give the presentation. Please notify the ' . $cfg['EMAIL_CONF_NAME'] . ' Secretariat as early as possible of such a replacement. <b>Presenters who fail to show up for their presentation without due written notice within 7 days from this confirmation will be sanctioned.</b>';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			Changes/corrections in the list of authors, the presenting author etc. should be sent to the ' . $cfg['EMAIL_CONF_NAME'] . ' Secretariat within 7 days of this confirmation. Updates in the abstract book and the final programme cannot be guaranteed for changes received after those 7 days. ';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			<b>Registration</b>';
	$message .= '			<br/>';
	$message .= '			We would be grateful if you would arrange your registration to the meeting, if you have not already done so. Online registration is available via our website at the following URL www.aiccrcog2019.com. The full scientific programme is available online.';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '			Congratulations with your selection and looking forward to seeing you in Kolkata! ';
	$message .= '			<br/>';
	$message .= '			<b>All correspondence related to this message should be directed to: abstract@aiccrcog2019.com</b>';
	$message .= '			<br/>';
	$message .= '			<br/>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= get_scientific_commitee_email_footer();
	$message .= '</table>';

	if ($rowAbstractDetails['tags'] == 'Abstract') {
		$subject = "Abstract Acceptance_" . $cfg['EMAIL_CONF_NAME'] . "";
	} elseif ($rowAbstractDetails['tags'] == 'CASE REPORT') {
		$subject = "Case Report Acceptance_" . $cfg['EMAIL_CONF_NAME'] . "";
	}

	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message);
		//insertEmailRecord($delegateId, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['comunication_email'], $cfg['ADMIN_NAME'], 'secretariat@aiccrcog2019.com', $subject, $message, "SEND");

		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;
		return $array;
	} else {
		return false;
	}
}

/******************************************************UTILITY FUNCTION*******************************************************************/
function getInvoiceMailerDetails($delegateId, $slipId)
{
	global $cfg, $mycms;
	include_once('function.workshop.php');
	include_once('function.dinner.php');
	$total_del_count = $_SESSION['ADD_MORE_DEL_COUNT'];


	$rowSlip = slipDetails($slipId);

	$invoiceArr = invoiceDetailsOfSlip($slipId);
	$counter = 0;
	$delgId = "";
	$innerBody = "";
	$unavalableForPaymentStatus = 'NO';
	$invoiceDetailsArr = array();
	$wrkshpName		= array();

	foreach ($invoiceArr as $key => $invoiceDetails) {
		$counter 		 = $counter + 1;
		$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);

		$type			 = "";
		$btnCss = '';

		if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
			if ($mycms->getSession('LOGGED.USER.ID') == $invoiceDetails['delegate_id']) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['btnCss'] = 'style="display:none;"';
			}
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = "CONFERENCE REGISTRATION";
			$showInvoice = 'Y';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
			if ($thisUserDetails['registration_classification_id'] == 3) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 7) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_2N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 8) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_3N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 9) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_2N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 10) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_3N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 11) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_2N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 12) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_3N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 13) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_2N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 14) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_3N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 15) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_2N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 16) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_3N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 17) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_2N'] . "- " . $thisUserDetails['user_full_name'];
			} else if ($thisUserDetails['registration_classification_id'] == 18) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_3N'] . "- " . $thisUserDetails['user_full_name'];
			}

			$showInvoice = 'Y';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workshopCountArr = totalWorkshopCountReport();
			$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
			$showInvoice = $workShopDetails['showInInvoices'];	//echo '<pre>';print_r($showInvoice);die('zzzz');
			$workshopCount = $workshopCountArr[$workShopDetails['workshop_id']]['TOTAL_LEFT_SIT'];
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = strtoupper(getWorkshopName($workShopDetails['workshop_id']));
			if ($workshopCount < 1) {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['chkStatus'] =  "NOT_AVALABLE";
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['span'] =  ""; //"<span style='color: red;'>** No More Seat Available For This Workshop</span>";
				$unavalableForPaymentStatus = 'YES';
				$wrkshpName[]	=  getWorkshopName($workShopDetails['workshop_id']);
			}
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
			$dinnerRefId = $dinnerDetails['refference_id'];
			$dinner_user_type = dinnerForWhome($dinnerRefId);
			if ($dinner_user_type == 'ACCOMPANY') {
				$invoiceDetails['service_type'] = 'ACCOMPANY_DINNER_REQUEST';
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
			} else {
				$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
			}
			$showInvoice = 'Y';
		}

		if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = "ACCOMPANYING PERSON  - " . $accompanyDetails['user_full_name'];
			$showInvoice = 'Y';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = "ACCOMMODATION - " . $thisUserDetails['user_full_name'];
			$showInvoice = 'Y';
		}
		if ($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST") {
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['tourDetails'] = getTourDetails($invoiceDetails['refference_id']);
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = getTourName($tourDetails['package_id']) . " REGISTRATION OF " . $thisUserDetails['user_full_name'];
			$showInvoice = 'Y';
		}
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cssStyle'] = 'style="display:none;"';
		if ($invoiceDetails['status'] == "A") {
			$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cssStyle'] = '';
		}

		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['delgId'] 				= $invoiceDetails['delegate_id'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['invoice_number'] 		= $invoiceDetails['invoice_number'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['currency'] 				= $invoiceDetails['currency'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['SHOWINVOICE'] 			= $showInvoice;
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['service_roundoff_price'] = $invoiceDetails['service_roundoff_price'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['service_type']			= $invoiceDetails['service_type'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['delegate_id'] 			= $invoiceDetails['delegate_id'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['id'] 					= $invoiceDetails['id'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['refference_id'] 			= $invoiceDetails['refference_id'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['invoice_mode'] 			= $rowSlip['invoice_mode'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['counter'] 				= $counter;
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['service_unit_price'] 	= $invoiceDetails['service_unit_price'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['applicable_tax_amount'] 	= $invoiceDetails['applicable_tax_amount'];
		$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['internet_handling_amount'] = $invoiceDetails['internet_handling_amount'];
	}

	if (!$invoiceDetailsArr) {
		$mycms->redirect("profile.php");
	}
	$sequence = $cfg['SERVICE.SEQUENCE'];

	$count = 0;

	foreach ($invoiceDetailsArr as $key1 => $serviceList) {
		$sqlUserDetails['QUERY'] = "SELECT `user_full_name` 
										 FROM " . _DB_USER_REGISTRATION_ . "
										WHERE `id` = '" . $key1 . "' ";
		$userName = $mycms->sql_select($sqlUserDetails);


		// echo "<pre>"; 
		// print_r($invoiceList);
		// echo "</pre>";			
		foreach ($sequence as $key2 => $seqName) {
			$invoiceList = $serviceList[$seqName];

			foreach ($invoiceList as $key => $invoiceDetails) {

				if ($invoiceDetails['SHOWINVOICE'] == 'Y' && $invoiceDetails['service_roundoff_price'] > 0) {
					$count = $count + 1;

					// $innerBody .= '		<tr class="tlisting" '.$invoiceDetails['cssStyle'].'>';
					// $innerBody .= '		<td style="width:35%; border-right:1px solid #000; border-bottom:1px solid #000;">'.$invoiceDetails['invoice_number'].'</td>';
					// $innerBody .= '		<td style="width:35%; border-right:1px solid #000; border-bottom:1px solid #000;">'.$invoiceDetails['type'].'<br />'.$invoiceDetails['span'].'</td>';
					// $innerBody .= '		<td style="width:35%; border-right:1px solid #000; border-bottom:1px solid #000;">'.($invoiceDetails['service_roundoff_price'] == 0 ? 'Inclusive' : '<a href="'._BASE_URL_.'pdf.download.invoice.php?user_id='.$invoiceDetails['delegate_id'].'&invoice_id='.$invoiceDetails['id'].'"><img src="'._BASE_URL_.'images/download.png" alt="download"/></a>').'</td>';
					// $innerBody .= '		</tr>';
					// echo "<pre>";print_r($invoiceDetails);
					if ($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
						$sql 	=	array();
						$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `title`='DELEGATE CONFERENCE REGISTRATION' AND `purpose`='Mailer Invoice Icon' AND status IN ('A', 'I')";
						$result 	 = $mycms->sql_select($sql);
						$icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];
					}
					if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
						$sql 	=	array();
						$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `title`='DELEGATE WORKSHOP REGISTRATION' AND `purpose`='Mailer Invoice Icon' AND status IN ('A', 'I')";
						$result 	 = $mycms->sql_select($sql);
						$icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];
					}
					if ($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
						$sql 	=	array();
						$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `title`='ACCOMPANY CONFERENCE REGISTRATION' AND `purpose`='Mailer Invoice Icon' AND status IN ('A', 'I')";
						$result 	 = $mycms->sql_select($sql);
						$icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];
					}
					if ($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
						$sql 	=	array();
						$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `title`='DELEGATE DINNER' AND `purpose`='Mailer Invoice Icon' AND status IN ('A', 'I')";
						$result 	 = $mycms->sql_select($sql);
						$icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];
					}
					if ($invoiceDetails['service_type'] == "ACCOMPANY_DINNER_REQUEST") {
						$sql 	=	array();
						$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `title`='DELEGATE DINNER' AND `purpose`='Mailer Invoice Icon' AND status IN ('A', 'I')";
						$result 	 = $mycms->sql_select($sql);
						$icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];
					}
					$innerBody .= '<tr style="position: relative;border-radius: 15px;background:#' . $cfg['color'] . ';" >
						<td style="text-align: left; align-items: center; display: flex; padding-left: 20px;">
							<span><img style="display: inline-block; vertical-align: middle; padding-right: 20px;" src="' . $icon . '" alt=""></span>
							<h2 style="line-height: 1; color: black; font-size:18px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding:20px 0;margin: 0;display: inline-block; vertical-align: middle;">' . $invoiceDetails['type'] . '<br />' . $invoiceDetails['span'] . '
								<span style="color: black; font-size: 14px;font-weight: 500;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding:0;margin: 0;display: inline-block; vertical-align: middle;">INVOICE NUMBER - ' . $invoiceDetails['invoice_number'] . '</span>
							</h2>
						</td>
						<td style="padding: 0 30px;">
							<p style="color: black; font-size: 16px;font-weight: 700;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0; display: inline-block; vertical-align: middle;">INR<br>' . $invoiceDetails['service_unit_price'] . '
							</p>
						</td>
						<td style="vertical-align: bottom;  ">
							<span>' . ($invoiceDetails['service_roundoff_price'] == 0 ? 'Inclusive' : '<a href="' . _BASE_URL_ . 'pdf.download.invoice.php?user_id=' . $invoiceDetails['delegate_id'] . '&invoice_id=' . $invoiceDetails['id'] . '"><img style=" width: 40px;vertical-align: bottom;" src="' . _BASE_URL_ . 'images/mailer/down-arrow.png" alt="download"/></a>') . '</span>
						</td>
					</tr><tr>
					<td style="padding: 16px 0;"></td>
					<td></td>
				</tr>';
				}
			}
		}
	}

	return $innerBody;
}

function getExhibitorInvoiceMailerDetails($delegateId, $slipId, $invoiceId = '')
{
	global $cfg, $mycms;
	return false;
	$innerBody = "";
	if ($invoiceId != '') {
		$searchString = " AND id = '" . $invoiceId . "'";
	}
	$downloadUrl     		   = _BASE_URL_;
	$sqlFetchInvoiceDetails['QUERY']    = " SELECT * 
									 	 FROM " . _DB_EXIBITOR_INVOICE_ . " 
										WHERE `payment_status` IN ('PAID','COMPLIMENTARY')
									  	  AND `status` = 'A' 
									  	  AND `slip_id` = '" . $slipId . "'";
	$resultInvDetails          = $mycms->sql_select($sqlFetchInvoiceDetails);



	foreach ($resultInvDetails as $key => $rowdetails) {

		$sqlFetchUserDetails['QUERY']    = " SELECT * 
											 FROM " . _DB_USER_REGISTRATION_ . " 
											WHERE `registration_payment_status` IN ('PAID','COMPLIMENTARY')
											  AND `status` = 'A' 
											  AND `exhibitor_company_id` = '" . $rowdetails['exhibitor_id'] . "'";

		$resultFetchUserDetails          = $mycms->sql_select($sqlFetchUserDetails);
		$ROWFetchUserDetails 			 = $resultFetchUserDetails[0];

		if ($rowdetails['has_gst'] == 'Y' && $rowdetails['invoice_mode'] == 'ONLINE') {
			$invRowSpan = 2;
		} else {
			$invRowSpan = 1;
		}

		if ($rowdetails['service_type'] == "EXHIBITOR_REPRESENTATIVE_REGISTRATION") {
			$service_type = getInvoiceTypeString($rowdetails['delegate_id'], $rowdetails['refference_id'], "EXHIBITOR");
		}
		if ($rowdetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
			$workShopDetails = getWorkshopDetails($rowdetails['refference_id']);
			$service_type = getInvoiceTypeString($rowdetails['delegate_id'], $rowdetails['refference_id'], "WORKSHOP");
		}
		if ($rowdetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {

			$service_type = getInvoiceTypeString($rowdetails['delegate_id'], $rowdetails['refference_id'], "ACCOMMODATION");
		}
		if ($rowdetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
			$service_type = getInvoiceTypeString($rowdetails['delegate_id'], $rowdetails['refference_id'], "ACCOMPANY");
		}

		if ($rowdetails['service_type'] == "DELEGATE_DINNER_REQUEST") {
			$service_type = getInvoiceTypeString($rowdetails['delegate_id'], $rowdetails['refference_id'], "DINNER");
		}


		$thisUserDetails = getUserDetails($rowdetails['delegate_id']);
		if ($rowdetails['service_roundoff_price'] > 0.00) {
			$innerBody .= '			<tr>	
											<td>' . $rowdetails['invoice_number'] . '</td>
											<td rowspan=' . $invRowSpan . '>' . $service_type . '</td>
											<td align="center"><a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('pdf.download.invoice.Exhibitor.php?user_id=' . $ROWFetchUserDetails['id'] . '&invoice_id=' . $rowdetails['id']) . '"><img src="' . $cfg['IAACON_BASE_URL'] . 'images/mailerImage/download.png" alt="download"/></a></td>
									    </tr>';
			if ($rowdetails['has_gst'] == 'Y' && $rowdetails['invoice_mode'] == 'ONLINE') {
				$innerBody .= '		<tr>
											<td>' . getRuedaInvoiceNo($rowdetails['id']) . '</td>
											<td align="center"><a href="' . $cfg['IAACON_BASE_URL'] . 'gotoportal.php?goto=' . base64_encode('pdf.download.invoice.Exhibitor.php?show=intHand&user_id=' . $ROWFetchUserDetails['id'] . '&invoice_id=' . $rowdetails['id']) . '"><img src="' . $cfg['IAACON_BASE_URL'] . 'images/mailerImage/download.png" alt="download"/></a></td>
										</tr>';
			}
		} else {
			$innerBody .= '			<tr>	
											<td align="center">-</td>
											<td>' . $service_type . '</td>
											<td align="center">Complimentary</td>
									    </tr>';
		}
	}
	return $innerBody;
}

function userInvoiceMailer($delegateId, $invoiceId, $operation)
{
	global $cfg, $mycms;
	return false;
	$sqlFetchUserDetails['QUERY'] 			= "SELECT * FROM " . _DB_USER_REGISTRATION_ . " 
														WHERE id = '" . $delegateId . "'";
	$resultUserDetails              = $mycms->sql_select($sqlFetchUserDetails);
	$rowUserDetails 				= $resultUserDetails[0];
	$invoiceContent                 = mailInvoiceContent($delegateId, $invoiceId);
	$invoiceSubject                 = "Invoice";

	if ($operation == 'SEND') {
		$mycms->send_mail($rowUserDetails['user_full_name'], $rowUserDetails['user_email_id'], $invoiceSubject, $invoiceContent);

		//insertEmailRecord($delegateId, $rowUserDetails['user_full_name'], $rowUserDetails['user_email_id'], $cfg['ADMIN_NAME'], $cfg['ADMIN_REGISTRATION_EMAIL'], $invoiceSubject, $invoiceContent, "SEND");


		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $invoiceSubject;
		$array['MAIL_BODY'] = $invoiceContent;
		return $array;
	} else {
		return false;
	}
}

function send_uniqueSequence($delegateId, $operation = 'SEND')
{
	global $mycms, $cfg;
	$rowFetchUserDetails  = getUserDetails($delegateId);

	// COMPOSING SMS
	//$sms = "Dear Delegate, Your Unique sequence at ".$cfg['EMAIL_CONF_NAME']." is ".$rowFetchUserDetails['user_unique_sequence'].".";

	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
						    FROM " . _DB_EMAIL_TEMPLATE_ . " 
					       WHERE status = ? 
						     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 5,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlUserImage = array();
	$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
										WHERE `title` = 'Login Credentials'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

	$logo = '<a href="#" target="_blank" text-decoration: none; border: 0;"><img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" alt="logo" width="310" height="" style="display: block; border: 0;" /></a>';
	// $img = _BASE_URL_ . 'images/mailer/login-success.png';

	
	$reg_link = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';
	$line = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1 = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2 = _BASE_URL_ . 'images/mailer/line-after.png';
	$reg_link = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';

	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);

	$find = [
		'[LOGO]', '[IMG]', '[USERNAME]', '[REG_NO]', '[EMAIL]', '[MOBILE]', '[CONF_EMAIL]','[CONF_MOBILE]', '[UNIQUE_ID]','[LINE]','[LINE1]', '[LINE2]',
		'[TERMS-ICON]', '[TEMS-LINK]', '[CANCEL-ICON]', '[CANCEL-LINK]', '[LOGIN-ICON]',
		'[FOOTER_IMG]', '[REG_LINK]', "fce5e8", "fdcdd3", "945934"
	];

	$replacement = [
		$logo, $img, $rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_registration_id'], $rowFetchUserDetails['user_email_id'], $rowFetchUserDetails['user_mobile_no'],
		$cfg['EMAIL_CONF_EMAIL_US'],$cfg['EMAIL_CONF_CONTACT_US'], $rowFetchUserDetails['user_unique_sequence'],$line,$line1,$line2,
		_BASE_URL_ . 'images/mailer/terms.png', _BASE_URL_ . 'terms.php', _BASE_URL_ . 'images/mailer/cancel.png', _BASE_URL_ . 'cancellation.php', _BASE_URL_ . 'images/mailer/login.png',
		$footer_img, $reg_link, $cfg['light_color'], $cfg['color'], $cfg['dark_color']
	];


	$result = str_replace($find, $replacement, $mailTemplateDescription);

	$message = '';

	// $message .= '<img src="' . $emailHeader . ' " width="100%" alt="header" /><br/><br/>';
	$message = $result;
	// $message .= '<img src="' . $emailFooter . ' " width="100%" alt="Footer" /><br/><br/>';


	$newSub = $rowaMail['subject'];
	if ($operation == 'SEND') {
		//$status = $mycms->send_sms($rowFetchUserDetails['user_mobile_no'], $sms,'Informative');
		$status = $mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $newSub, $message, $cfg['ADMIN_EMAIL']);
		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY'] = $message;
		$array['SMS_BODY'] = $sms;

		return $array;
	} else {
		return false;
	}
}

function generateInvoiceOrderSummary($delegateId, $slipId)
{
	global $cfg, $mycms;
	include_once('function.workshop.php');
	include_once('function.dinner.php');
	$sqlInvoice 	=	array();
	$sqlInvoice['QUERY'] = "SELECT * 
								  FROM " . _DB_INVOICE_ . " 
								 WHERE `slip_id` =? 
								   AND `delegate_id` =? 
								   AND `status` = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => 'slip_id',            'DATA' => $slipId,   	 'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'delegate_id',        'DATA' => $delegateId,   'TYP' => 's');
	$sqlInvoice['PARAM'][]   = array('FILD' => 'status',             'DATA' => 'A',   		 'TYP' => 's');
	$resInvoice 		= $mycms->sql_select($sqlInvoice);
	$userDetails 	    = getUserDetails($delegateId);




	$templateString		= "";
	$acmponyStatus 		= false;
	$RegData 			= array();

	if ($resInvoice) {
		$counter = 0;
		$acmponyCounter = 0;
		foreach ($resInvoice as $keyInvoice => $rowInvoice) {
			if ($rowInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
				$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
				if ($userDetails['registration_request'] == 'EXHIBITOR') {
					$RegData['REGISTRATION_DATA']['REGTYP'] = "Exhibitor Representative";
				} else {
					$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($delegateId));
				}
			}

			if ($rowInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
				$RegData['REGISTRATION_DATA']['RAWDATA'] = $rowInvoice;
				$RegData['REGISTRATION_DATA']['REGTYP'] = getRegClsfName(getUserClassificationId($delegateId));
				$RegData['ACCOMODATION_DETAILS'] = accmodation_details($delegateId);
			}

			if ($rowInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
				$RegData['WORKSHOP_DATA']['RAWDATA'] = $rowInvoice;
				$Wstatus 						 	 = true;
				$Wcounter++;
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']	 = getWorkshopDetails($rowInvoice['refference_id']);
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['name']         	 = getWorkshopName($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['date']         	 = getWorkshopDate($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
				$RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['RAWDATA']          = getWorkshopRecord($RegData['WORKSHOP_DATA']['WORKSHOP'][$Wcounter]['workShopDetails']['workshop_id']);
			}

			if ($rowInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
				$RegData['ACCOMPANY_DATA']['RAWDATA'] = $rowInvoice;
				$acmponyStatus 					      = true;
				$acmponyCounter++;
				$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']  	   = getUserDetails($rowInvoice['refference_id']);
				$RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['user_full_name']        = $RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'][$acmponyCounter]['accompanyDetails']['user_full_name'];
			}

			if ($rowInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
				$Dstatus 						 = true;
				$RegData['DINNER_DATA']['RAWDATA'] = $rowInvoice;
				$Dcounter++;
				$dinner[]                     	 = getInvoiceTypeStringForMail($rowInvoice['delegate_id'], $rowInvoice['refference_id'], "DINNER");
			}

			if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
				$RegData['REGISTRATION_DATA']['REGTYP']  = getRegClsfName(getUserClassificationId($delegateId));
				$RegData['ACCOMODATION_DETAILS'] 		 = accmodation_details($delegateId);

				//print_r($RegData);

				/*$sqlaccommodationDetails 				 = 	array();	
					$sqlaccommodationDetails['QUERY'] 				 = "SELECT accommodation.*,masterHotel.hotel_name AS hotel_name,masterHotel.hotel_address AS hotel_address, package.package_name 
																 FROM "._DB_REQUEST_ACCOMMODATION_." accommodation
														   INNER JOIN "._DB_PACKAGE_ACCOMMODATION_." package
																   ON accommodation.package_id = package.id
														   INNER JOIN "._DB_MASTER_HOTEL_." masterHotel
																   ON masterHotel.id = package.hotel_id
																 WHERE accommodation.status = ? 
																   AND accommodation.user_id = ? 
																   AND accommodation.refference_invoice_id = ?";
					$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',                'DATA' =>'A',   				'TYP' => 's');											 
					$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',               'DATA' =>$delegateId,   		'TYP' => 's');											 
					$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.refference_invoice_id', 'DATA' =>$rowInvoice['id'],   'TYP' => 's');											 
					$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
					$rowaccommodation = $resaccommodation[0];
					
					$RegData['ACCOMMODATION_DATA']['RAWDATA'] 	    = $rowInvoice;
					$RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']  = $rowaccommodation['checkin_date'];
					$RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] = $rowaccommodation['checkout_date'];
					$RegData['ACCOMMODATION_DATA']['PACKAGE_NAME']  = $rowaccommodation['package_name'];*/
			}
		}
	}


	$templateString                  .= '<table style="width:90%; border:1px solid #000; font-family: Arial, Helvetica, sans-serif; font-size: 14px; margin: auto; border-collapse: collapse;">';

	if ($RegData['REGISTRATION_DATA']) {
		$templateString                 .= '<tr>';
		$templateString                 .= '<td style="width: 50%; border: 1px solid #000;padding:5px;" valign="top"><strong>REGISTRATION FOR</strong></td>';
		$templateString                 .= '<td style="width: 50%; border: 1px solid #000;padding:5px;" valign="top"><strong>Conference Registration - </strong>';
		if ($RegData['REGISTRATION_DATA']['RAWDATA']['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {

			$templateString                 .= $RegData['REGISTRATION_DATA']['REGTYP'];
		} elseif ($RegData['REGISTRATION_DATA']['RAWDATA']['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {

			$templateString                 .= $RegData['REGISTRATION_DATA']['REGTYP'];
			if (sizeof($RegData['ACCOMODATION_DETAILS']) > 0) {
				$templateString                 .= '<br>';
				$templateString                 .= 'Hotel : ' . $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] . '<br>';
				$templateString                 .= 'Check-in Date :' . $mycms->cDate("m/d/Y", $RegData['ACCOMODATION_DETAILS']['CHECKIN_DATE']) . '<br>';
				$templateString                 .= 'Check-out Date :' . $mycms->cDate("m/d/Y", $RegData['ACCOMODATION_DETAILS']['CHECKOUT_DATE']) . '';
			}
		}
		$templateString                 .= '</td></tr>';
	}

	$templateString                 .= '<tr>';
	$templateString                 .= '<td style="width: 50%; padding: 5px;text-decoration: underline;border: 1px solid #000;" valign="top"><strong>REGISTRATION INCLUSIONS</strong></td><td style="width: 50%; padding: 5px;text-decoration: underline;border: 1px solid #000;">';



	if ($RegData['REGISTRATION_DATA']['RAWDATA']['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {

		$templateString             .=  '' . registration_entitlements($workshopNamList, $AccomodationPackageList, $dinnerEntitlement, $delegateId) . '';
		$entitlements                =  '' . registration_entitlements($workshopNamList, $AccomodationPackageList, $dinnerEntitlement, $delegateId) . '';
	} else {

		$templateString             .=  '' . registration_entitlements($workshopNamList, $AccomodationPackageList, $delegateId) . '';
		$entitlements                =  '' . registration_entitlements($workshopNamList, $AccomodationPackageList, $delegateId) . '';
	}




	$workshopNamList = array();
	if ($userDetails['isCombo'] == 'Y') {
		$sqlCombo['QUERY'] = "SELECT c.classification_title, w.created_dateTime ,c.workshop_date
								  FROM " . _DB_REQUEST_WORKSHOP_ . " w 
								  INNER JOIN  " . _DB_WORKSHOP_CLASSIFICATION_ . " c ON w.workshop_id = c.id
								 WHERE  w.`delegate_id` =? 
								   AND c.`status` = ?";

		$sqlCombo['PARAM'][]   = array('FILD' => 'delegate_id',        'DATA' => $delegateId,   'TYP' => 's');
		$sqlCombo['PARAM'][]   = array('FILD' => 'status',             'DATA' => 'A',   		 'TYP' => 's');
		$resCombo 		= $mycms->sql_select($sqlCombo);
		//echo '<pre>'; print_r($resCombo[0]);
		foreach ($resCombo as $key => $value) {
			$workshopNamList[] = $value['classification_title'] . " on " . $mycms->cDate("F j, Y", $value['workshop_date']);
		}
		$templateString                 .= '<tr>';
		$templateString                 .= '<td valign="top" align="left" style="padding: 5px; border-bottom:dashed thin #ccc; width:30%;"><strong>Workshop Registration:</strong></td>';
		$templateString                 .= '<td valign="top" align="left" style="padding: 5px;border-bottom:dashed thin #ccc;"><font style="font-size: small;">' . implode(", ", $workshopNamList) . '</font> </td>';
		$templateString                 .= '</tr>';
	} else {
		if (sizeof($RegData['WORKSHOP_DATA']['WORKSHOP']) > 0) {
			foreach ($RegData['WORKSHOP_DATA']['WORKSHOP'] as $k => $valU) {
				$val = $valU['workShopDetails'];
				if ($val['showInInvoices'] == 'Y') {
					$workshopNamList[] = $val['classification_title'] . " on " . $mycms->cDate("F j, Y", $val['workshop_date']);
				}
			}
			$templateString                 .= '<tr>';
			$templateString                 .= '<td valign="top" align="left" style="padding: 5px; border-bottom:dashed thin #ccc; width:30%;"><strong>Workshop Registration:</strong></td>';
			$templateString                 .= '<td valign="top" align="left" style="padding: 5px;border-bottom:dashed thin #ccc;"><font style="font-size: small;">' . implode(", ", $workshopNamList) . '</font> </td>';
			$templateString                 .= '</tr>';
		}
	}



	/*if($RegData['ACCOMPANY_DATA'])
		{

			$acmpony = array();
			foreach($RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'] as $k=>$val)
			{
				$acmpony[] = $val['user_full_name'];
			}
			$templateString                 .= '<tr>';
			$templateString                 .= '	<td valign="top" align="left" style="padding: 5px; border-bottom:dashed thin #ccc; width:30%;"><b>Accompanying Person :</b></td>';
			$templateString                 .= '	<td valign="top" align="left" style="padding: 5px;border-bottom:dashed thin #ccc;">';					
			$templateString                 .= '		<font style="font-size: small;">'.implode(", ",$acmpony).'</font>';
			$templateString                 .= '	</td>';
			$templateString                 .= '</tr>';
		}*/

	if ($RegData['DINNER_DATA']) {
		$dinnerEntitlement['TYPE']   = 'GALA DINNER';
		$dinnerEntitlement['DATE']   = '06-09-2019';
		$dinnerEntitlement['COUNT']  = $Dcounter;
		$dinnerEntitlement['DETAIL'] = $dinner;
	}

	if ($rowInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
		$templateString                 .= '<tr>';
		$templateString                 .= '<td valign="top" align="left" style="padding: 5px;width:30%; border-bottom:dashed thin #ccc;"><strong>Category(Accommodation) :</strong></td>';
		$templateString                 .= '<td valign="top" align="left" style="padding: 5px;border-bottom:dashed thin #ccc;">';

		if (!empty($RegData['ACCOMODATION_DETAILS']['ACCOMDA_DETAILS'])) {
			$details = explode('@', $RegData['ACCOMODATION_DETAILS']['ACCOMDA_DETAILS']);
			$accommodation_deatils = $details[0];
		} else {
			$accommodation_deatils = '';
		}

		$templateString                 .= $accommodation_deatils; // $RegData['REGISTRATION_DATA']['REGTYP'];
		if (sizeof($RegData['ACCOMODATION_DETAILS']) > 0) {
			$templateString                 .= '<br>';
			$templateString                 .= 'Hotel : ' . $RegData['ACCOMODATION_DETAILS']['HOTEL_NAME'] . '<br>';
			$templateString                 .= 'Check-in Date :' . $mycms->cDate("m/d/Y", $RegData['ACCOMODATION_DETAILS']['CHECKIN_DATE']) . '<br>';
			$templateString                 .= 'Check-out Date :' . $mycms->cDate("m/d/Y", $RegData['ACCOMODATION_DETAILS']['CHECKOUT_DATE']) . '';
		}
		$templateString                 .= '</td>';
		$templateString                 .= '</tr>';
	}


	if ($userDetails['registration_request'] == 'EXHIBITOR') {
		$templateString             .=  '';
	} else if ($RegData['REGISTRATION_DATA']['RAWDATA']['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
		$templateString                 .= '<td style="width: 50%; border-bottom:1px solid #000;" align="left"><font style="font-size: small;">  </font><br>';
		$AccomodationPackageList = $RegData['ACCOMODATION_DETAILS'];
		$templateString             .=  '' . residential_registration_entitlements($workshopNamList, $AccomodationPackageList, getUserClassificationId($delegateId)) . '';

		$templateString                 .= '</td><td style="width: 50%; border-bottom:1px solid #000;"></td></tr>';
	}
	/*else if($RegData['REGISTRATION_DATA']['RAWDATA']['service_type']=="DELEGATE_CONFERENCE_REGISTRATION")
		{
			$templateString             .=  ''.registration_entitlements($workshopNamList,$AccomodationPackageList,$dinnerEntitlement).'';
		}
		else
		{
			$templateString             .=  ''.registration_entitlements($workshopNamList,$AccomodationPackageList).'';
		}*/

	if ($RegData['ACCOMPANY_DATA']) {

		$acmpony = array();
		foreach ($RegData['ACCOMPANY_DATA']['ACCOMPANY_NAME'] as $k => $val) {
			$acmpony[] = $val['user_full_name'];
		}
		$templateString                 .= '<tr>';
		$templateString                 .= '	<td ><b>Accompanying Person :</b></td>';
		$templateString                 .= '	<td>';
		$templateString                 .= '		<font style="font-size: small;">' . implode(", ", $acmpony) . '</font>';
		$templateString                 .= '	</td>';
		$templateString                 .= '</tr>';
	}

	$templateString                 .= '</table>';
	// return $templateString;
	return $entitlements;
}

function get_email_header()
{
	global $mycms, $cfg;
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
	//$emailHeader          = ""._BASE_URL_."images/NeoconHeader.jpg";

	return '<tr>
					<td align="left" valign="top"><img src="' . $emailHeader . ' " width="800" alt="header" /><br/><br/></td>
				</tr>';
}

// function get_invitation_email_header()
// {

// 	global $mycms, $cfg;
// 	$sql 	=	array();
// 	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
// 													WHERE `status`='A' order by id desc limit 1";
// 	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
// 	$result = $mycms->sql_select($sql);
// 	$row    		 = $result[0];

// 	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
// 	if ($row['header_image'] != '') {
// 		$emailHeader  = $header_image;
// 	}

// 	return '<tr>
// 					<td align="left" valign="top" colspan=2><img src="' . $emailHeader . '" alt="header" style="width:100%"/><br/><br/></td>
// 				</tr>';
// }
function get_invitation_email_header()
{

	global $mycms, $cfg;
	$sql_header 	=	array();
	$sql_header['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql_header);
	$row    		 = $result[0];
	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['sc_header_image'];
	// if ($row['header_image'] != '') {
	// 	$emailHeader  = $header_image;
	// }
	// $emailHeader=_BASE_URL_.'images/scientific_mailer_header.jpg';

	return '<tr>
					<td align="left" valign="top" colspan=2><img src="' . $header_image . '" alt="header" style="width:100%"/></td>
				</tr>';
}

function get_invitation_email_side_top()
{
	global $mycms, $cfg;
	// $emailSide          = "" . _BASE_URL_ . "images/lettre_head_left_bar.jpg";
	// $emailSide2          = "" . _BASE_URL_ . "images/lettre_head_left_down_section.jpg";
	$emailSide          = "" . _BASE_URL_ . "images/sidebar.jpg";


	return '<tr><td width="30%" align="center" valign="top" rowspan="2">
                <img src="' . $emailSide . '" width="220px" style="max-width:220px !important;"><br>
			</td>
			<td align="right" valign="top" width="70%"><img src="' . _BASE_URL_ . 'images/header2.png" width="100%" ></td>
			</tr>';
}


function get_invitation_email_side_1()
{
	global $mycms, $cfg;
	$emailSide          = "" . _BASE_URL_ . "images/lettre_head_left_bar.jpg";
	$emailSide2          = "" . _BASE_URL_ . "images/lettre_head_left_down_section.jpg";

	return '
				 <p style="text-align: center;margin: 0 0 9px 0;font-size: 12px;font-weight: bold;">
                        September 5 - 8, 2019
						<br>
						ITC Royal Bengal, Kolkata
                </p>
                <img src="' . $emailSide . '" width="224px"><br>
				<img src="' . $emailSide2 . '" width="224px">
			   ';
}

function get_invitation_email_side_2()
{
	global $mycms, $cfg;
	$emailSide          = "" . _BASE_URL_ . "images/lettre_head_left_down_section.jpg";

	//return '<img src="'.$emailSide.'" style="width:100%;">';
	return '';
}

function get_email_footer()
{
	global $mycms, $cfg;
	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row    		 = $result[0];

	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	if ($row['footer_image'] != '') {
		$emailFooter  = $footer_image;
	}



	//$emailFooter           = ""._BASE_URL_."images/NeoconFooter.jpg";

	return '<tr>
					<td height="" align="left" valign="top">
						Thanks
						<br />
						<strong>
							Organising Committee
							<br />
							' . $cfg['EMAIL_CONF_NAME'] . '
						</strong>
						<br /><br />
					</td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="' . $emailFooter . '" width="800" alt="footer" /></td>
				</tr>';
}

function get_email_footer_certificate()
{
	global $mycms, $cfg;
	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
													WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row    		 = $result[0];

	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	if ($row['footer_image'] != '') {
		$emailFooter  = $footer_image;
	}



	//$emailFooter           = ""._BASE_URL_."images/NeoconFooter.jpg";

	return '<tr>
					<td height="" align="left" valign="top">
						Thanks
						<br />
						<strong>
							Organising Committee
							<br />
							' . $cfg['EMAIL_CONF_NAME'] . ', Bhubaneswar
						</strong>
						<br /><br />
					</td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="' . $emailFooter . '" width="800" alt="footer" /></td>
				</tr>';
}

function get_email_certificate_footer()
{
	global $mycms, $cfg;

	$emailFooter           = "" . _BASE_URL_ . "images/NeoconFooter.jpg";

	return '<tr>
					<td height="" align="left" valign="top">
						Warm regards
						<br />
						<strong>
							Dr Brajagopal Ray
							<br />
							Chief Organising Secretary
							<br />
							NEOCON 2022, Kolkata
						</strong>
						<br /><br />
					</td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="' . $emailFooter . '" width="800" alt="footer" /></td>
				</tr>';
}

function get_abstract_email_footer()
{
	global $mycms, $cfg;
	//$emailFooter           = ""._BASE_URL_."images/emailFooter.jpg";
	$emailFooter           = "" . _BASE_URL_ . "images/NeoconFooter.jpg";

	return '<tr>
					<td height="" align="left" valign="top">
						Thanks
						<br />
						<strong>
							Scientific Team
							<br />
							' . $cfg['EMAIL_CONF_NAME'] . '
						</strong>
						<br /><br />
					</td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="' . $emailFooter . '" width="800" alt="footer" /></td>
				</tr>';
}

// function get_invitation_email_footer()
// {

// 	global $mycms, $cfg;

// 	$imagePath 			   = "" . _BASE_URL_ . "images/";
// 	$emailFooter           = "" . _BASE_URL_ . "images/emailFooter.jpg";


// 	return '<br /><br /><br />
// 				Warm regards
// 				<br /><br />
// 				<table width="100%" border="0">
// 					<tr>
// 						<td align="left" valign="top">
// 							<div style="float:left; margin:3px;">
// 								<img src="' . $imagePath . 'BHASKAR_PAL.png" width="150" alt="BHASKAR PAL" /><br>
// 								<b>BHASKAR PAL</b><br>
// 								Chair<br>
// 								Organising Committee
// 							</div>
// 						</td>
// 						<td align="left" valign="top">
// 							<div style="float:left; margin:3px;">
// 								<img src="' . $imagePath . 'BASAB_MUKHERJEE_20190510.png" width="150" alt="BASAB MUKHERJEE" /><br>
// 								<b>BASAB MUKHERJEE</b><br>
// 								Secretary<br>
// 								Organising Committee
// 							</div>
// 						</td>
// 						<td align="left" valign="top">
// 							<div style="float:left; margin:3px;">
// 								<img src="' . $imagePath . 'M_M_SAMSUZZOHA.png" width="150" alt="M M SAMSUZZOHA" /><br>
// 								<b>M M SAMSUZZOHA</b><br>
// 								Secretary<br>
// 								Organising Committee
// 							</div>
// 						</td>
// 					</tr>
// 					<tr>
// 						<td align="left" valign="top">
// 							<div style="float:left; margin:3px;">
// 								<img src="' . $imagePath . 'ALOK_BASU.png" width="150" alt="ALOK BASU" /><br>
// 								<b>ALOK BASU</b><br>
// 								Chair<br>
// 								Scientific Committee 
// 							</div>
// 						</td>
// 						<td align="left" valign="top">
// 							<div style="float:left; margin:3px;">
// 								<img src="' . $imagePath . 'BISWAJYOTI_GUHA.png" width="150" alt="BISWAJYOTI GUHA" /><br>
// 								<b>BISWAJYOTI GUHA</b><br>
// 								Secretary<br>
// 								Scientific Committee 
// 							</div>
// 						</td>
// 						<td align="left" valign="top">
// 							<div style="float:left; margin:3px;">
// 								<img src="' . $imagePath . 'SEETHA_RAMAMURTHY_PAL.png" width="150" alt="SEETHA RAMAMURTHY PAL" /><br>
// 								<b>SEETHA RAMAMURTHY PAL</b><br>
// 								Secretary<br>
// 								Scientific Committee 
// 							</div>
// 						</td>
// 					</tr>
// 				</table>
// 				';
// }
function get_invitation_email_footer()
{
	global $mycms, $cfg;

	$imagePath 			   = "" . _BASE_URL_ . "images/signatures/";
	$emailFooter           = "" . _BASE_URL_ . "images/Bottom.png";


	return '<br />
				<b>Warm regards</b>
				<br /><br />
				<table width="100%" border="0">
					<tr>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
								<img style="height: 60px;" src="' . $imagePath . 'Apurba-Kumar-Mukherjee.jpeg"  alt="Dr Apurba Kumar Mukherjee" /><br>
								<b>Dr Apurba Kumar Mukherjee</b><br><b>Co-Founder & Organising Chairman</b><br>
								<b>HOPECON 2024</b><br>
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
								<img style="height: 60px;" src="' . $imagePath . 'Payodhi-Dhar.jpeg"  alt="Dr Payodhi Dhar" /><br>
								<b>Dr Payodhi Dhar</b><br><b>Founder & Treasurer</b><br>
								<b>HOPECON 2024</b><br>
							</div>
						</td>
						
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Pradip-Kumar-Mitra.jpeg"  alt="Dr Pradip Kumar Mitra" /><br>
							<b>Dr Pradip Kumar Mitra</b><br><b>Convenor</b><br>
							<b>HOPECON 2024</b><br>
							</div>
						</td>
					</tr>
					<tr>
						
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Arup-Das-Biswas.jpeg"  alt="Dr Arup Das Biswas" /><br>
							<b>Dr Arup Das Biswas</b><br><b>Organising President</b><br>
							<b>HOPECON 2024</b><br>
 
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Hari-Shankar-Pathak.jpeg"  alt="Dr Hari Shankar Pathak" /><br>
							<b>Dr Hari Shankar Pathak</b><br><b>Organising Vice-President</b><br>
							<b>HOPECON 2024</b><br>
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;width:120px" src="' . $imagePath . 'Gautam-Saha.jpeg"  alt="Dr Gautam Saha" /><br>
							<b>Dr Gautam Saha</b><br><b>Organising Secretary</b><br>
							<b>HOPECON 2024</b><br>

							</div>
						</td>
					</tr>
					<tr>
						
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;width:120px" src="' . $imagePath . 'Anjan-Adhikari.jpeg"  alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Anjan Adhikari</b><br><b>Editor & Asst Treasurer</b><br>
							<b>HOPECON 2024</b><br>
 
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Raja-Dhar.jpeg"  alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Raja Dhar</b><br><b>Joint Organising Secretary</b><br>
							<b>HOPECON 2024</b><br>
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Upal-Sengupta.jpeg"  alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Upal Sengupta</b><br><b>Joint Organising Secretary</b><br>
							<b>HOPECON 2024</b><br>

							</div>
						</td>
					</tr>
					<tr>
						
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Arup-Kumar-Kundu.jpeg" alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Arup Kumar Kundu</b><br><b>Scientific Chairman</b><br>
							<b>HOPECON 2024</b><br>
 
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Rana-Bhattacharjee.jpeg" alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Rana Bhattacharjee</b><br><b>Scientific Secretary</b><br>
							<b>HOPECON 2024</b><br>
							</div>
						</td>
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;" src="' . $imagePath . 'Arunansu-Talukdar.jpeg"  alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Arunansu Talukdar</b><br><b>Conference<br>Coordination Chairman</b><br>
							<b>HOPECON 2024</b><br>

							</div>
						</td>
					</tr>
					<tr>
						
						<td align="left" valign="top" style="border-style:none;">
							<div style="float:left; margin:3px;">
							<img style="height: 60px;width:120px" src="' . $imagePath . 'Tirthankar-Dasgupta.jpeg"  alt="Dr Apurba Kumar Mukherjee" /><br>
							<b>Dr Tirthankar Dasgupta</b><br><b>Conference<br>Coordination Secretary</b><br>
							<b>HOPECON 2024</b><br>
 
							</div>
						</td>
						
					</tr>
					
				</table>
				';
}

function get_scientific_commitee_email_footer()
{
	global $mycms, $cfg;
	$emailFooter           = "" . $cfg['IAACON_BASE_URL'] . "images/mailerImage/mailFooter.jpg";

	return '<tr>
					<td height="" align="left" valign="top">
						<br />
						<strong>
							' . $cfg['EMAIL_CONF_NAME'] . '
						</strong>
						<br />
						Tel: 033 4001 5677
						<br />
						Web address: www.aiccrcog2019.com
						<br />
						<strong>
							Find out more about our 23rd Annual Meeting at Science City 
							<br />
							<span style="color:green;">
							Please consider our environment and think before you print. Thank you! 
							</span>
						</strong>
						<br />
						<br />
					</td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="' . $emailFooter . '" width="800" alt="footer" /></td>
				</tr>';
}

function get_office_address()
{
	global $mycms, $cfg;
	return '<strong>
					<u>ADDRESS</u></strong>
					<br />
				 	' . $cfg['EMAIL_CONF_NAME'] . ' Registration Secretariat
					<br/>
					DL 220, Sector II, Salt Lake City, Kolkata, 700091.';
}

function get_user_details_for_messaging($delegateId)
{
	global $mycms, $cfg;
	// FETCHING DELEGATE DETAILS
	$sqlFetchUserDetails       		 = getAllDelegates($delegateId, "");
	$resultFetchUserDetails    		 = $mycms->sql_select($sqlFetchUserDetails);
	$rowFetchUserDetails       		 = $resultFetchUserDetails[0];

	$regClassificationId   		 	 = $rowFetchUserDetails['registration_classification_id'];
	$rowFetchInvoice['currency'] = ($regClassificationId == 4) ? "USD" : "INR";

	return $rowFetchUserDetails;
}

function registration_entitlements($workshopNamList, $AccomodationPackageList, $dinnerEntitlement = null, $delegateId = "")			// new
{
	global $mycms, $cfg;


	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_EMAIL_CONSTANT_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 1,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlMailClassification 	=	array();

	$sqlMailClassification['QUERY'] 	   = "SELECT RC.mail_lunch_details,
												RC.mail_dinner_details,
												RC.mail_gala_dinner_details,
												RC.mail_inaugural_dinner_details
								    FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " RC 
								    INNER JOIN " . _DB_USER_REGISTRATION_ . " R ON
								    RC.id = R.registration_classification_id
							       WHERE RC.status = ? 
							       	 AND R.status = ?	
								     AND  R.id = ? ";

	$sqlMailClassification['PARAM'][]   = array('FILD' => 'RC.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.id',   	'DATA' => $delegateId,   'TYP' => 's');
	$resMailClassification	= $mycms->sql_select($sqlMailClassification);
	$rowaMailClassification = $resMailClassification[0];
	echo '<pre>'; print_r($resMailClassification);die;

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
					WHERE `id`!='' AND `purpose`='Mailer' AND status IN ('A', 'I')";
	$result 	 = $mycms->sql_select($sql);

	$message = '<tr><td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tbody><tr>
			<td style="text-align: left;padding-left: 30px;padding-bottom: 20px;padding-right: 30px;">
				<span style="display:flex"><img style="display: inline-block; vertical-align: middle; padding-right: 30px;" src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'] . '" alt="">
				<p style="color: #' . $cfg['dark_color'] . '; font-size: 15px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;display: inline-block; vertical-align: middle;">' . $result[0]['title'] . '</p></span>
			</td>
			<td style="text-align: left;padding-left: 30px;padding-bottom: 20px;padding-right: 30px;"><span style="display:flex"><img style="display: inline-block; vertical-align: middle; padding-right: 30px;" src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['icon'] . '" alt="">
				<p style="color: #' . $cfg['dark_color'] . '; font-size: 15px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0; display: inline-block; vertical-align: middle;">' . $result[1]['title'] . '
				</p></span>
			</td>
			</tr>
			<tr>
				<td style="text-align: left;padding-left: 30px;padding-bottom: 20px;padding-right: 30px;">
					<span style="display:flex"><img style="display: inline-block; vertical-align: middle; padding-right: 30px;" src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[2]['icon'] . '" alt="">
					<p style="color: #' . $cfg['dark_color'] . '; font-size: 15px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;display: inline-block; vertical-align: middle;">' . $result[2]['title'] . '</p></span>
				</td>
				<td style="text-align: left;padding-left: 30px;padding-bottom: 20px;padding-right: 30px;"><span style="display:flex"><img style="display: inline-block; vertical-align: middle; padding-right: 30px;" src="' . _BASE_URL_ .  $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[3]['icon'] . '" alt="">
					<p style="color: #' . $cfg['dark_color'] . '; font-size: 15px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0; display: inline-block; vertical-align: middle;">' . $result[3]['title'] . '
				</p></span>
				</td>
			</tr><tr>';

	if (!empty($rowaMailClassification['mail_lunch_details'])) {
		$message .= '<td style="text-align: left;padding-left: 30px;padding-bottom: 20px;padding-right: 30px;">
			<span style="display:flex"><img style="display: inline-block; vertical-align: middle; padding-right: 30px;" src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[4]['icon'] . '" alt="">
			<p style="color: #' . $cfg['dark_color'] . '; font-size: 15px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0;display: inline-block; vertical-align: middle;">' . $rowaMailClassification['mail_lunch_details'] . '</p></span>
		</td>';
	}

	if (!empty($rowaMailClassification['mail_dinner_details'])) {
		$message .= '<td style="text-align: left;padding-left: 30px;padding-bottom: 20px;padding-right: 30px;"><span style="display:flex"><img style="display: inline-block; vertical-align: middle; padding-right: 30px;" src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[5]['icon'] . '" alt="">
			<p style="color: #' . $cfg['dark_color'] . '; font-size: 15px;font-weight: 600;font-family: Montserrat, sans-serif,  Arial, Helvetica, sans-serif;padding: 8px 0;margin: 0; display: inline-block; vertical-align: middle;">' . $rowaMailClassification['mail_dinner_details'] . ' </p></span>
		</td>';
	}

	$message .= '</tr> </tbody>
		</table>
	</td></tr>';
	return $message;
}

function registration_entitlements_accompanies($workshopNamList, $AccomodationPackageList, $dinnerEntitlement = null)
{
	global $mycms, $cfg;


	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_EMAIL_CONSTANT_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 1,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlMailClassification 	=	array();

	$sqlMailClassification['QUERY'] 	   = "SELECT RC.mail_lunch_details,
												RC.mail_dinner_details,
												RC.mail_gala_dinner_details,
												RC.mail_inaugural_dinner_details
								    FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " RC 
								    INNER JOIN " . _DB_USER_REGISTRATION_ . " R ON
								    RC.id = R.registration_classification_id
							       WHERE RC.status = ? 
							       	 AND R.status = ?	
								     AND  R.id = ? ";

	$sqlMailClassification['PARAM'][]   = array('FILD' => 'RC.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.id',   	'DATA' => $delegateId,   'TYP' => 's');
	$resMailClassification	= $mycms->sql_select($sqlMailClassification);
	$rowaMailClassification = $resMailClassification[0];

	$message .= '<table  border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top" style="padding: 5px;" width="60%"> <b><u>You are entitled for:</b></u></td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td style="padding: 5px;">';
	$message .= '			<ul style="list-style-type:square;">';
	$message .= '				<li>Entry to Scientific Halls and Exhibition Area</li>';

	$message .= '				<li>Tea/Coffee during the Conference at the venue</li>';
	if (!empty($rowaMailClassification['mail_lunch_details'])) {
		$message .= '<li>' . $rowaMailClassification['mail_lunch_details'] . '</li>';
	}

	if (!empty($rowaMailClassification['mail_dinner_details'])) {
		$message .= '				<li>' . $rowaMailClassification['mail_dinner_details'] . '</li>';
	}

	$message .= '			</ul>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function residential_registration_entitlements($workshopNamList, $AccomodationPackgeList, $classificationId)
{

	global $mycms, $cfg;
	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_EMAIL_CONSTANT_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 1,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlMailClassification 	=	array();

	$sqlMailClassification['QUERY'] 	   = "SELECT RC.mail_lunch_details,
												RC.mail_dinner_details,
												RC.mail_gala_dinner_details,
												RC.mail_inaugural_dinner_details
								    FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " RC 
								    INNER JOIN " . _DB_USER_REGISTRATION_ . " R ON
								    RC.id = R.registration_classification_id
							       WHERE RC.status = ? 
							       	 AND R.status = ?	
								     AND  R.id = ? ";

	$sqlMailClassification['PARAM'][]   = array('FILD' => 'RC.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.id',   	'DATA' => $delegateId,   'TYP' => 's');
	$resMailClassification	= $mycms->sql_select($sqlMailClassification);
	$rowaMailClassification = $resMailClassification[0];

	$message .= '<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td valign="top" align="left" style="width:30%; border-bottom:dashed thin #ccc;"> <b>Dinner : </b></td>';
	$message .= '		<td valign="top" align="left" style="border-bottom:dashed thin #ccc;"> ' . $rowaMail['inaugural_dinner_details'] . ' <br/>' . $rowaMailClassification['mail_gala_dinner_details'] . ' </td>';
	$message .= '	</tr>';

	$inclussionsArray = array();



	$inclussionsArray[] = $rowaMailClassification['mail_lunch_details'];
	$inclussionsArray[] = 'Entry to Scientific Halls and Exhibition Area';
	$inclussionsArray[] = 'Conference Kit';
	$inclussionsArray[] = 'Tea/Coffee during the Conference at the venue';



	$message .= '	<tr>';
	$message .= '		<td valign="top" align="left"> <b>Other inclusions: </b></td>';
	$message .= '		<td valign="top" align="left">' . implode(", ", $inclussionsArray) . '</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function registration_accompany_entitlements()			// new
{

	global $mycms, $cfg;
	$sqlMail 	=	array();
	$sqlMail['QUERY'] 	   = "SELECT * 
								    FROM " . _DB_EMAIL_CONSTANT_ . " 
							       WHERE status = ? 
								     AND  id = ? ";
	$sqlMail['PARAM'][]   = array('FILD' => 'status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMail['PARAM'][]   = array('FILD' => 'id',   	'DATA' => 1,   'TYP' => 's');
	$resMail			   = $mycms->sql_select($sqlMail);
	$rowaMail              = $resMail[0];

	$sqlMailClassification 	=	array();

	$sqlMailClassification['QUERY'] 	   = "SELECT RC.mail_lunch_details,
												RC.mail_dinner_details,
												RC.mail_gala_dinner_details,
												RC.mail_inaugural_dinner_details
								    FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " RC 
								    INNER JOIN " . _DB_USER_REGISTRATION_ . " R ON
								    RC.id = R.registration_classification_id
							       WHERE RC.status = ? 
							       	 AND R.status = ?	
								     AND  R.id = ? ";

	$sqlMailClassification['PARAM'][]   = array('FILD' => 'RC.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.status',   'DATA' => 'A',   		  				'TYP' => 's');
	$sqlMailClassification['PARAM'][]   = array('FILD' => 'R.id',   	'DATA' => $delegateId,   'TYP' => 's');
	$resMailClassification	= $mycms->sql_select($sqlMailClassification);
	$rowaMailClassification = $resMailClassification[0];

	$message .= '<table  border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top" width="60%"> <b><u>You are entitled for:</b></u></td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td>';
	$message .= '			<ul style="list-style-type:square;">';
	$message .= '				<li>Entry to Scientific Halls and Exhibition Area</li>';

	$message .= '				<li>Tea/Coffee during the Conference at the venue</li>';
	$message .= '				<li>' . $rowaMailClassification['mail_lunch_details'] . '</li>';

	$message .= '			</ul>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function registration_note()			// new
{
	$message .= '<table  border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top" width="60%"> NOTE - The Unique Sequence will serve as your personal password to access your ' . $cfg['EMAIL_CONF_NAME'] . ' user account. Please log into your account to</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td>';
	$message .= '			<ul style="list-style-type:square;">';
	$message .= '				<li>add accompany</li>';
	$message .= '				<li>add workshop and banquet dinner</li>';
	$message .= '				<li>download invitation letter</li>';
	$message .= '				<li>check your registration status </li>';
	$message .= '				<li>view, edit and update your personal data </li>';
	$message .= '			</ul>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function exhibitor_registration_note()			// new
{
	$message .= '<table  border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top" width="60%"> NOTE - The Unique Sequence will serve as your personal password to access your ' . $cfg['EMAIL_CONF_NAME'] . ' user account. Please log into your account to</td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td>';
	$message .= '			<ul style="list-style-type:square;">';
	$message .= '				<li>add workshop and banquet dinner</li>';
	$message .= '				<li>check your registration status </li>';
	$message .= '				<li>view, edit and update your personal data </li>';
	$message .= '			</ul>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function accommodation_note()			// new
{
	$message .= '<table  border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top" width="60%"><b>TERMS -</b></td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td>';
	$message .= '			<ul style="list-style-type:square;">';
	$message .= '				<li>Check-in: 14:00 hrs. 
									    Check-out: 12:00 hrs.
										Check-in/check-out timing will be followed strictly.<br>
							            Early check-in and late check-out are subject to room availability and as per the hotel policy.       
									</li>';
	$message .= '				<li>One must carry his/her ID PROOF at the time of check-in.</li>';
	$message .= '				<li>All additional expenditures incurred on telephone calls, room service, wi-fi,<br> laundry, mini bar, tobacco, additional cot, restaurant bills etc. should be settled by the guests before departure.
										<br>Extra room nights will be subject to room availability and as per the hotel policy. Room nights will be charged as per the hotel rate.
									</li>';

	$message .= '			</ul>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function registration_accompanying_category()			// new
{

	$message .= '<table width="800" border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$message .= '	<tr>';
	$message .= '		<td height="" align="left" valign="top" width="60%"><strong>Accompanying Persons are entitled for</strong><br/><br/></td>';
	$message .= '	</tr>';
	$message .= '	<tr>';
	$message .= '		<td>';
	$message .= '			<ul style="list-style-type:square;">';
	$message .= '				<li>Entry to Scientific Halls and Exhibition Area</li>';
	$message .= '				<li>Tea/Coffee during the Conference at the venue</li>';
	$message .= '				<li>Lunch on 5th, 6th and 7th September 2019.</li>';
	$message .= '			</ul>';
	$message .= '		</td>';
	$message .= '	</tr>';
	$message .= '</table>';

	return $message;
}

function getInvoiceTypeStringForMail($delegateId = "", $reqId = "", $type = "", $reqStatus = "A")
{
	global $mycms, $cfg, $searchArray, $searchString;
	include_once('function.delegate.php');
	include_once('function.dinner.php');
	$dinnerDetails = getDinnerDetails($reqId);
	$dinner_user_type = dinnerForWhome($dinnerDetails['refference_id']);
	$dinnerUserDetails     = getUserDetails($dinnerDetails['refference_id']);
	if ($dinner_user_type == 'ACCOMPANY') {
		$string  = "<u>" . $dinnerUserDetails['user_full_name'] . "</u>";
	} else {
		$string  = "<u>" . $dinnerUserDetails['user_full_name'] . "</u>";
	}
	return $string;
}

function getAccommodationDetails($delegateId, $invoiceID)
{
	global $mycms, $cfg;
	$sqlaccommodationDetails 				 = 	array();
	$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*, masterHotel.hotel_name AS hotel_name,package.package_name AS package_name, masterHotel.hotel_address AS hotel_address, 
														   inv.service_type
													  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
											    INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
													    ON masterHotel.id = accommodation.hotel_id

												INNER JOIN " . _DB_ACCOMMODATION_PACKAGE_ . " package

													ON package.id = accommodation.package_id
													    
										   LEFT OUTER JOIN " . _DB_INVOICE_ . " inv
										   				ON inv.id = accommodation.refference_invoice_id
													 WHERE accommodation.status = ? 
													  AND inv.id = ?
													   AND accommodation.user_id = ?";

	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',                'DATA' => 'A',   'TYP' => 's');

	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'inv.id',                'DATA' => $invoiceID,   'TYP' => 's');
	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',               'DATA' => $delegateId,   'TYP' => 's');

	$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
	foreach ($resaccommodation as $k => $rowaccommodation) {
		echo '<pre>';
		print_r($rowaccommodation);
		$RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']  = $rowaccommodation['checkin_date'];
		$RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE']  = $rowaccommodation['checkout_date'];
		$RegData['ACCOMMODATION_DATA']['ROOM_NO']  = $rowaccommodation['rooms_no'];
		$RegData['ACCOMMODATION_DATA']['HOTEL_NAME']  = $rowaccommodation['hotel_name'];

		$RegData['ACCOMMODATION_DATA']['PACKAGE_NAME']  = $rowaccommodation['package_name'];

		$RegData['ACCOMMODATION_DATA']['ROOM_TYPE']  = $rowaccommodation['more_rooms'];
	}

	return $RegData;
}

function accmodation_details($delegateId)
{
	global $mycms, $cfg;
	$sqlaccommodationDetails 				 = 	array();
	$sqlaccommodationDetails['QUERY'] 		 = "SELECT accommodation.*, masterHotel.hotel_name AS hotel_name, masterHotel.hotel_address AS hotel_address, 
														   inv.service_type
													  FROM " . _DB_REQUEST_ACCOMMODATION_ . " accommodation
											    INNER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
													    ON masterHotel.id = accommodation.hotel_id
										   LEFT OUTER JOIN " . _DB_INVOICE_ . " inv
										   				ON inv.id = accommodation.refference_invoice_id
													 WHERE accommodation.status = ? 
													   AND accommodation.user_id = ?";

	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.status',                'DATA' => 'A',   'TYP' => 's');
	$sqlaccommodationDetails['PARAM'][]   = array('FILD' => 'accommodation.user_id',               'DATA' => $delegateId,   'TYP' => 's');

	$resaccommodation = $mycms->sql_select($sqlaccommodationDetails);
	if ($resaccommodation) {
		//$rowaccommodation = $resaccommodation[0];
		foreach ($resaccommodation as $k => $rowaccommodation) {
			if ($rowaccommodation['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
				$residentialAccommodationPackageId = $cfg['RESIDENTIAL_PACKAGE_ARRAY'];

				$packageId = $rowaccommodation['package_id'];

				$accommodationDetails 	= $cfg['ACCOMMODATION_PACKAGE_ARRAY'];
				$resultAccomDetails  	= $accommodationDetails[$packageId];

				if ($RegData['ACCOMMODATION_DATA']['CHECKIN_DATE'] != '') {
					if ($rowaccommodation['checkin_date'] < $RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']) {
						$RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']  				 	= $rowaccommodation['checkin_date'];
					}
				} else {
					$RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']  				 		= $rowaccommodation['checkin_date'];
				}


				if ($RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] != '') {
					if ($rowaccommodation['checkout_date'] > $RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE']) {
						$RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] 				 	= $rowaccommodation['checkout_date'];
					}
				} else {
					$RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] 				 		= $rowaccommodation['checkout_date'];
				}

				$RegData['ACCOMMODATION_DATA']['HOTEL_NAME']    				 = $rowaccommodation['hotel_name'];
				$RegData['ACCOMMODATION_DATA']['PREFERRED_ACCOMPANY']['NAME']    = $rowaccommodation['preffered_accommpany_name'];
				$RegData['ACCOMMODATION_DATA']['PREFERRED_ACCOMPANY']['EMAIL']   = $rowaccommodation['preffered_accommpany_email'];
				$RegData['ACCOMMODATION_DATA']['PREFERRED_ACCOMPANY']['MOBILE']  = $rowaccommodation['preffered_accommpany_mobile'];
			} else {
				if ($RegData['ACCOMMODATION_DATA']['CHECKIN_DATE'] != '') {
					if ($rowaccommodation['checkin_date'] < $RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']) {
						$RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']  				 	= $rowaccommodation['checkin_date'];
					}
				} else {
					$RegData['ACCOMMODATION_DATA']['CHECKIN_DATE']  				 		= $rowaccommodation['checkin_date'];
				}


				if ($RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] != '') {
					if ($rowaccommodation['checkout_date'] > $RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE']) {
						$RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] 				 	= $rowaccommodation['checkout_date'];
					}
				} else {
					$RegData['ACCOMMODATION_DATA']['CHECKOUT_DATE'] 				 		= $rowaccommodation['checkout_date'];
				}
			}
			$RegData['ACCOMMODATION_DATA']['HOTEL_NAME'] = $rowaccommodation['hotel_name'];
			$RegData['ACCOMMODATION_DATA']['ACCOMDA_DETAILS'] = $rowaccommodation['accommodation_details'];
		}
	}
	return $RegData['ACCOMMODATION_DATA'];
}

?>