<?php
/**
 * IACTSCON 2027 — Art Exhibition Submission Processor
 * Expects a POST from userAccountSetupForm (act = submitArtwork).
 * Uses the $mycms->sql_select() / $mycms->sql_insert() wrapper
 * convention: a QUERY string with `?` placeholders and a PARAM array
 * of {FILD, DATA, TYP} bound positionally, matching the working
 * pattern from function.abstract.php.
 */

header('Content-Type: application/json');
include_once('includes/frontend.init.php');

function respond($status, $message, $extra = array()) {
    echo json_encode(array_merge(array('status' => $status, 'message' => $message), $extra));
    exit;
}
 
/* ---------------------------------------------------------------------
   0. Basic request checks
   --------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'Invalid request method.');
}
 
if (!isset($_POST['act']) || $_POST['act'] !== 'submitArtwork') {
    respond('error', 'Invalid submission action.');
}
 
/* ---------------------------------------------------------------------
   1. Collect + sanitize inputs
   --------------------------------------------------------------------- */
$firstName    = trim($_POST['full_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$mobileIsd    = trim($_POST['mobile_isd'] ?? '+91');
$mobileNo     = trim($_POST['mobile_no'] ?? '');
$categoryId   = trim($_POST['regimood'] ?? '');
$gdriveLink   = trim($_POST['gdrive_link'] ?? '');
$artworkTitle = trim($_POST['artwork_title'] ?? '');
$mediumNotes  = trim($_POST['medium_notes'] ?? '');
$confirmed    = isset($_POST['confirm_original']);
 
/* ---------------------------------------------------------------------
   2. Validate
   --------------------------------------------------------------------- */
$errors = array();
 
if ($firstName === '') {
    $errors[] = 'First name is required.';
}
 
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
 
if ($mobileNo === '' || !preg_match('/^[0-9]{7,15}$/', $mobileNo)) {
    $errors[] = 'A valid mobile number is required.';
}
 
if ($categoryId === '' || !ctype_digit((string) $categoryId)) {
    $errors[] = 'Please select a valid artwork category.';
}
 
if ($gdriveLink === '' || !filter_var($gdriveLink, FILTER_VALIDATE_URL)) {
    $errors[] = 'A valid cloud storage link is required.';
} else {
    $allowedHosts = array('drive.google.com', 'icloud.com', 'onedrive.live.com', '1drv.ms', 'dropbox.com');
    $host = parse_url($gdriveLink, PHP_URL_HOST);
    $hostOk = false;
    foreach ($allowedHosts as $allowed) {
        if ($host !== null && strpos($host, $allowed) !== false) {
            $hostOk = true;
            break;
        }
    }
    if (!$hostOk) {
        $errors[] = 'Cloud storage link must be a Google Drive, iCloud, OneDrive, or Dropbox link.';
    }
}
 
if ($artworkTitle === '') {
    $errors[] = 'Artwork / photograph title is required.';
}
 
if (!$confirmed) {
    $errors[] = 'You must confirm this is your original work and the link is publicly accessible.';
}
 
if (!empty($errors)) {
    respond('error', implode(' ', $errors));
}
 
/* ---------------------------------------------------------------------
   3. Verify the category actually exists and is active
   --------------------------------------------------------------------- */
$sqlCatCheck = array();
$sqlCatCheck['QUERY']   = "SELECT `id`, `category_name`
                            FROM `art_exhibition_category`
                            WHERE `id` = ? AND `status` = ?";
$sqlCatCheck['PARAM'][] = array('FILD' => 'id',     'DATA' => $categoryId, 'TYP' => 'i');
$sqlCatCheck['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A',         'TYP' => 's');
$catResult = $mycms->sql_select($sqlCatCheck);
 
if (empty($catResult)) {
    respond('error', 'The selected category is not valid. Please go back and choose again.');
}
 
$categoryName = $catResult[0]['category_name'];
 
/* ---------------------------------------------------------------------
   5. Resolve the delegate: match an existing registration by email,
      or create a minimal one if none exists.
   --------------------------------------------------------------------- */
$sqlDelegateLookup = array();
$sqlDelegateLookup['QUERY']   = "SELECT `id`, `user_registration_id`
                                   FROM " . _DB_USER_REGISTRATION_ . "
                                   WHERE `user_email_id` = ? AND `status` = ?
                                   ORDER BY `id` DESC
                                   LIMIT 1";
$sqlDelegateLookup['PARAM'][] = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
$sqlDelegateLookup['PARAM'][] = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');
$delegateResult = $mycms->sql_select($sqlDelegateLookup);
 
if (!empty($delegateResult)) {
    // Existing delegate found — server re-checks by email rather than
    // trusting any delegate_id the client may have posted.
    $delegateId      = (int) $delegateResult[0]['id'];
    $registrationNo  = $delegateResult[0]['user_registration_id'];
} else {
    /*
       No match: create a minimal delegate record.
 
       NOTE: this field list is inferred from insertingUserDetails() in
       function.registration.php, not the actual table schema for
       _DB_USER_REGISTRATION_. That table may have additional NOT NULL
       columns not covered here (e.g. user_dob, user_gender,
       user_country_id) that don't have defaults. If this insert fails
       on a NOT NULL constraint, tell me which column and I'll add it
       with a sensible placeholder value.
    */
    $now = date('Y-m-d H:i:s');
 
    $sqlNewDelegate = array();
    $sqlNewDelegate['QUERY'] = "INSERT INTO " . _DB_USER_REGISTRATION_ . "
                                    SET `refference_delegate_id` = ?,
                                        `user_type`               = ?,
                                        `user_email_id`           = ?,
                                        `user_full_name`          = ?,
                                        `user_mobile_isd_code`    = ?,
                                        `user_mobile_no`          = ?,
                                        `isRegistration`          = ?,
                                        `combo_registration`      = ?,
                                        `accDateCombo`            = ?,
                                        `accommodation_room`      = ?,
                                        `isConference`            = ?,
                                        `isWorkshop`              = ?,
                                        `isAccommodation`         = ?,
                                        `isTour`                  = ?,
                                        `isAbstract`              = ?,
                                        `isCombo`                 = ?,
                                        `registration_request`    = ?,
                                        `account_status`          = ?,
                                        `status`                  = ?,
                                        `conf_reg_date`           = ?,
                                        `created_ip`              = ?,
                                        `created_sessionId`       = ?,
                                        `created_dateTime`        = ?";
 
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'refference_delegate_id', 'DATA' => '0',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'user_type',              'DATA' => 'DELEGATE',    'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'user_email_id',          'DATA' => $email,        'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'user_full_name',         'DATA' => $firstName,    'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'user_mobile_isd_code',   'DATA' => $mobileIsd,    'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'user_mobile_no',        'DATA' => $mobileNo,      'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isRegistration',         'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'combo_registration',     'DATA' => '',            'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'accDateCombo',           'DATA' => '',            'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'accommodation_room',     'DATA' => 0,             'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isConference',           'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isWorkshop',             'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isAccommodation',        'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isTour',                 'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isAbstract',             'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'isCombo',                'DATA' => 'N',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'registration_request',   'DATA' => 'GENERAL', 'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'account_status',         'DATA' => 'UNREGISTERED',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'status',                 'DATA' => 'A',           'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'conf_reg_date',          'DATA' => $now,          'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'created_ip',             'DATA' => $_SERVER['REMOTE_ADDR'] ?? '', 'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'created_sessionId',      'DATA' => session_id() ?: '', 'TYP' => 's');
    $sqlNewDelegate['PARAM'][] = array('FILD' => 'created_dateTime',       'DATA' => $now,          'TYP' => 's');
 
    try {
        $delegateId = $mycms->sql_insert($sqlNewDelegate, false);
    } catch (Throwable $e) {
        respond('error', 'Could not create your delegate record. Please try again shortly.');
    }
 
    if (!$delegateId) {
        respond('error', 'Could not create your delegate record. Please try again shortly.');
    }
 
    // Mirror insertingUserDetails(): generate a registration id + unique sequence.
    $registrationNo = $cfg['invoive_number_format'] . "-" . $mycms->getRandom(4, 'snum') . "-" . number_pad($delegateId, 4);
    $uniqueSequence  = "#" . $mycms->getRandom(4, 'snum') . number_pad($delegateId, 4);
 
    $sqlUpdateDelegate = array();
    $sqlUpdateDelegate['QUERY']   = "UPDATE " . _DB_USER_REGISTRATION_ . "
                                       SET `user_unique_sequence` = ?,
                                           `user_registration_id` = ?
                                     WHERE `id` = ?";
    $sqlUpdateDelegate['PARAM'][] = array('FILD' => 'user_unique_sequence', 'DATA' => $uniqueSequence, 'TYP' => 's');
    $sqlUpdateDelegate['PARAM'][] = array('FILD' => 'user_registration_id', 'DATA' => $registrationNo, 'TYP' => 's');
    $sqlUpdateDelegate['PARAM'][] = array('FILD' => 'id',                   'DATA' => $delegateId,     'TYP' => 's');
 
    $mycms->sql_update($sqlUpdateDelegate, false);
}
 
/* ---------------------------------------------------------------------
   6. Insert the submission
   --------------------------------------------------------------------- */
$createdIp        = $_SERVER['REMOTE_ADDR'] ?? '';
$createdBrowser   = $_SERVER['HTTP_USER_AGENT'] ?? '';
$createdSessionId = session_id() ?: '';
 
$sqlSubmission = array();
$sqlSubmission['QUERY'] = "INSERT INTO art_exhibition_submissions
                                SET `delegate_id`          = ?,
                                    `user_email_id`         = ?,
                                    `user_full_name`        = ?,
                                    `user_mobile_isd_code`  = ?,
                                    `user_mobile_no`        = ?,
                                    `user_registration_no`  = ?,
                                    `artCategoryId`         = ?,
                                    `artCategoryName`       = ?,
                                    `gDriveLink`            = ?,
                                    `photographTitle`       = ?,
                                    `notes`                 = ?,
                                    `status`                = ?,
                                    `created_by`            = ?,
                                    `created_ip`            = ?,
                                    `created_sessionId`     = ?,
                                    `created_browser`       = ?,
                                    `created_dateTime`      = ?";
 
$sqlSubmission['PARAM'][] = array('FILD' => 'delegate_id',          'DATA' => $delegateId,        'TYP' => 'i');
$sqlSubmission['PARAM'][] = array('FILD' => 'user_email_id',        'DATA' => $email,             'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'user_full_name',       'DATA' => $firstName,         'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'user_mobile_isd_code', 'DATA' => $mobileIsd,         'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'user_mobile_no',       'DATA' => $mobileNo,          'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'user_registration_no', 'DATA' => $registrationNo,    'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'artCategoryId',        'DATA' => $categoryId,        'TYP' => 'i');
$sqlSubmission['PARAM'][] = array('FILD' => 'artCategoryName',      'DATA' => $categoryName,      'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'gDriveLink',           'DATA' => $gdriveLink,        'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'photographTitle',      'DATA' => $artworkTitle,      'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'notes',                'DATA' => $mediumNotes,       'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'status',                'DATA' => 'A',               'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'created_by',            'DATA' => $delegateId,       'TYP' => 'i');
$sqlSubmission['PARAM'][] = array('FILD' => 'created_ip',            'DATA' => $createdIp,        'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'created_sessionId',     'DATA' => $createdSessionId, 'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'created_browser',       'DATA' => $createdBrowser,   'TYP' => 's');
$sqlSubmission['PARAM'][] = array('FILD' => 'created_dateTime',      'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
 
try {
    $newId = $mycms->sql_insert($sqlSubmission, false);

} catch (Throwable $e) {
    respond('error', 'Could not save your submission. Please try again shortly.');
}
         

if (!$newId) {
    respond('error', 'Could not save your submission. Please try again shortly.');
}
 
/*
   Submission code is derived from the insert id itself (zero-padded,
   # prefixed) — e.g. id 8 -> "#00008". It's only known after the
   insert, so it's written back with a follow-up UPDATE.
*/
$submissionCode = '#' . str_pad((string) $newId, 5, '0', STR_PAD_LEFT);
 
$sqlUpdateCode = array();
$sqlUpdateCode['QUERY']   = "UPDATE art_exhibition_submissions SET `submissionCode` = ? WHERE `id` = ?";
$sqlUpdateCode['PARAM'][] = array('FILD' => 'submissionCode', 'DATA' => $submissionCode, 'TYP' => 's');
$sqlUpdateCode['PARAM'][] = array('FILD' => 'id',             'DATA' => $newId,          'TYP' => 'i');
 
try {
    $mycms->sql_update($sqlUpdateCode, false);
} catch (Throwable $e) {
    // Non-fatal: the submission itself already saved successfully.
    // The code just won't be persisted to the row if this call fails.
}
 
/* ---------------------------------------------------------------------
   7. Mark the delegate record as having submitted artwork
   --------------------------------------------------------------------- */
$sqlMarkSubmitted = array();
$sqlMarkSubmitted['QUERY']   = "UPDATE " . _DB_USER_REGISTRATION_ . " SET `isArtWorkSubmitted` = ? WHERE `id` = ?";
$sqlMarkSubmitted['PARAM'][] = array('FILD' => 'isArtWorkSubmitted', 'DATA' => 'Y',         'TYP' => 's');
$sqlMarkSubmitted['PARAM'][] = array('FILD' => 'id',                 'DATA' => $delegateId, 'TYP' => 's');
 
try {
    $mycms->sql_update($sqlMarkSubmitted, false);
} catch (Throwable $e) {
    // Non-fatal — the submission itself already succeeded.
}


$sqlLookup = array();
$sqlLookup['QUERY']   = "SELECT `id`, `user_registration_id`, `user_full_name`
                          FROM " . _DB_USER_REGISTRATION_ . "
                          WHERE `user_email_id` = ? AND `status` = ?  AND `isRegistration` = 'Y'
                          ORDER BY `id` DESC
                          LIMIT 1";
$sqlLookup['PARAM'][] = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
$sqlLookup['PARAM'][] = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

$result = $mycms->sql_select($sqlLookup);

 artwork_message_send($delegateId,$firstName,$categoryName,$gdriveLink,$result[0]['user_registration_id'], $operation = 'SEND');

respond('success', 'Your artwork has been submitted successfully. Thank you for participating in IACTSCON 2027.', array(
    'id'                  => $newId,
    'reference_id'        => $submissionCode,
    'submitted_at'        => date('d M Y, h:i a'),
    'contributor_name'    => $firstName,
    'contributor_reg_no'  => $result[0]['user_registration_id'] !== '' ? $result[0]['user_registration_id'] : '—',
    'category'            => $categoryName,
    'title'               => $artworkTitle,
    'cloud_link'          => $gdriveLink,
    
));
 function artwork_message_send($delegateId,$firstName='',$categoryName='',$gdriveLink='',$user_registration_id='', $operation = 'SEND')
 {
	global $mycms, $cfg;
	include_once('includes/function.delegate.php');
	include_once('includes/function.registration.php');

	$loginUrl = _BASE_URL_;

	$rowFetchUserDetails = getUserDetails($delegateId);
	$color               = '80ec91';

	$sqlMSG = array();
	$sqlMSG['QUERY'] = "SELECT `conf_start_date`,`conf_end_date` FROM " . _DB_COMPANY_INFORMATION_ . " WHERE `id` = 1";
	$result      = $mycms->sql_select($sqlMSG);
	$companyInfo = $result[0];

	$startD    = $companyInfo['conf_start_date'];
	$endD      = $companyInfo['conf_end_date'];
	$startDate = new DateTime($startD);
	$endDate   = new DateTime($endD);

	if ($startDate->format('F Y') == $endDate->format('F Y')) {
		$formatted = $startDate->format("F jS") . " - " . $endDate->format("jS");
	} else {
		$formatted = $startDate->format("F jS") . " - " . $endDate->format("F jS");
	}

	$sqlSlip = array();
	$sqlSlip['QUERY']    = "SELECT * FROM " . _DB_SLIP_ . " WHERE status = ? AND id = ? ";
	$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' => $rowFetchPayment['slip_id'], 'TYP' => 's');
	$resSlip   = $mycms->sql_select($sqlSlip);
	$rowaSlip  = $resSlip[0];

	$user_password    = $mycms->decoded($rowFetchUserDetails['user_password']);
	$delagateCatagory = getUserClassificationId($delegateId);

	$sqlaccommodation = array();
	$sqlaccommodation['QUERY']   = "SELECT * FROM " . _DB_REQUEST_ACCOMMODATION_ . " WHERE status = ? AND user_id = ? ";
	$sqlaccommodation['PARAM'][] = array('FILD' => 'status',  'DATA' => 'A', 'TYP' => 's');
	$sqlaccommodation['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');
	$resaccom  = $mycms->sql_select($sqlaccommodation);
	$rowaccomm = $resaccom[0];


	$sqlMail = array();
	$sqlMail['QUERY']   = "SELECT * FROM " . _DB_EMAIL_TEMPLATE_ . " WHERE status = ? AND id = ? ";
	$sqlMail['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
	$sqlMail['PARAM'][] = array('FILD' => 'id',     'DATA' => 22,   'TYP' => 's');
	$resMail  = $mycms->sql_select($sqlMail);
	$rowaMail = $resMail[0];

	$sql = array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " WHERE `status`='A' order by id desc limit 1";
	$result = $mycms->sql_select($sql);
	$row = $result[0];

	$sqlUserImage = array();
	$sqlUserImage['QUERY'] = "SELECT * From " . _DB_ICON_SETTING_ . " WHERE `title` = 'Payment Successful'";
	$fetchData = $mycms->sql_select($sqlUserImage, false);
	$img = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

	$logo       = '<img src="' . _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] . '" style="display: inline-block;width: 115px;">';
	$line       = _BASE_URL_ . 'images/mailer/title-line-bottom.png';
	$line1      = _BASE_URL_ . 'images/mailer/line-before.png';
	$line2      = _BASE_URL_ . 'images/mailer/line-after.png';
	$reg_link   = _BASE_URL_;
	$footer_img = _BASE_URL_ . 'images/mailer/footer-bg.png';



	
	$mailTemplateDescription = htmlspecialchars_decode($rowaMail['description']);
	
	$find = [
		'[LOGO]', '[SUBMISSION_CAT]','[DRIVE_LINK]','[CONF_DATE]', '[CONF_VENUE]', '[UNIQUE_ID]', '[username]',
		'[PROFILE_LINK]', '[WEBSITE_LINK]', '[IMG]', '[REG_ID]', '[MAIL]',
		'[MOBILE]', '[REG_CLS_NAME]', '[payment_details]', '[invoice_details]',
		'[amount]', '[inr]', '[invoice_order_details]', '[LINE]', '[LINE1]',
		'[LINE2]', '[REG_LINK]', '[CONF_EMAIL]', '[CONF_MOBILE]', '[REPLY_EMAIL]',
		'[CONF_NAME]', '[VENUE_ADDRESS]', '[HOTEL_NAME]', '[CHECKINDATE]',
		'[CHECKOUTDATE]', '[HOTEL_ADDRESS]', '[HOTEL_MOBILE]', '[QTY]',
		'[CANCELATION_POLICY]', '[REFUND_POLICY]', '[REGISTRATION_DETAILS]',
		'[ACCOMODATION_DETAILS]', '[ACCOMPANY_DETAILS]', '[WORKSHOP_DETAILS]',
		'[INVOICE_DETAILS]', '[APP_INFO]','[CHECK_PNG]','[CIRCLE_PNG]', '[PAID_BY]','[ACCOMODATION_CANCEL_POLICY]','[PAYMENT_LOGO]','[FOOTER_IMG]'
	];

	$regClsId = $rowFetchUserDetails['registration_classification_id'];

	$replacement = [
		$logo,
        $categoryName,
        $gdriveLink,
		$formatted,
		$cfg['EMAIL_CONF_VENUE'],
		$rowFetchUserDetails['user_unique_sequence'],
		$firstName,
		_BASE_URL_ . 'profile.php',
		$cfg['SITE_LINK'],
		$img,
		$user_registration_id,
		$rowFetchUserDetails['user_email_id'],
		$rowFetchUserDetails['user_mobile_no'],
		getRegClsfName($regClsId),
		$mailPaymentDetails,
		$mailInvoiceDetails,
		number_format($financialSummaryOfSlip['AMOUNT'], 2),
		$currr,
		$invoiceOrderSummary,
		$line,
		$line1,
		$line2,
		$reg_link,
		$cfg['EMAIL_CONF_EMAIL_US'],
		$cfg['EMAIL_CONF_CONTACT_US'],
		$cfg['EMAIL_CONF_REPLY_US'],
		$cfg['EMAIL_CONF_NAME'],
		$cfg['EMAIL_CONF_VENUE'],
		$accommodationDetails['HOTEL_DETAILS']['hotel_name'],
		$accommodationDetails['BOOKING_DETAILS']['checkin_date'],
		$accommodationDetails['BOOKING_DETAILS']['checkout_date'],
		$accommodationDetails['HOTEL_DETAILS']['hotel_address'],
		$accommodationDetails['HOTEL_DETAILS']['hotel_phone_no'],
		$accommodationDetails['BOOKING_DETAILS']['booking_quantity'],
		$cfg['CANCELLATION_PAGE_INFO'],
		$cfg['TERMS_PAGE_INFO'],
		$registrationDetails,
		$accomodationDetails,
		$accompanyDetails,
		$workshopDetails,
		$invoiceDetails,
		$appInfo,
		_BASE_URL_ . 'images/check.png',
		_BASE_URL_ . 'images/circle-in-black-of-a-drum-top-view.png',
		$paid_by,
		$cfg['ACCOMODATION_CANCEL_POLICY'],
		$payment_logo,
		$footer_img
	];

	$result  = str_replace($find, $replacement, $mailTemplateDescription);
	$message = $result;

	
	$subject = $rowaMail['subject'];

	
	if ($operation == 'SEND') {
		$mycms->send_mail($rowFetchUserDetails['user_full_name'], $rowFetchUserDetails['user_email_id'], $subject, $message, '', $cfg['ADMIN_EMAIL']);


		return true;
	} else if ($operation == 'RETURN_TEXT') {
		$array = array();
		$array['MAIL_SUBJECT'] = $subject;
		$array['MAIL_BODY']    = $message;
		$array['SMS_NO']       = $rowFetchUserDetails['user_mobile_no'];
		$array['SMS_BODY'][0]  = $payRegSms;
		return $array;
	} else {
		return false;
	}
}