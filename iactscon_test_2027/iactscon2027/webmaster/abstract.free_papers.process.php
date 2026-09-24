<?php
include_once('includes/init.php');
$loggedUserID     = $mycms->getLoggedUserId();
$loggedUserType   = $mycms->getLoggedUserType();

// category wise topic listing by weavers start
if (isset($_POST['action']) && !empty($_POST['topic']) && $_POST['action'] === 'generateTopic') {
	$abstract_topic = '';
	$sql  			  = array();
	$sql['QUERY']     = " SELECT * 
								FROM " . _DB_ABSTRACT_TOPIC_ . " 
							   	WHERE `status` = ?
								AND `category` = ?
								ORDER BY `abstract_topic` ASC";
	$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql['PARAM'][]   = array('FILD' => 'category',   'DATA' => trim($_POST['topic']), 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql);
	if ($resultAbstractType) {
		$abstract_topic .= "<option value=''>-- Select Topic --</option>";
		foreach ($resultAbstractType as $key => $value) {
			$abstract_topic .= "<option value='" . $value['id'] . "'>" . $value['abstract_topic'] . "</option>";
		}
	}
	echo $abstract_topic;
	exit;
}
// category wise topic listing by weavers end

switch ($action) {

	case 'search_abstract':
		pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'email_update':
		email_update();
		exit();
		break;

	case 'edit_abstract_setup':
		edit_abstract_setup($cfg, $mycms);
		exit();
		break;

	case 'downloadAgreementFile':

		$fileName         = $_REQUEST['fileName'];
		$fullPath         = "../../" . $cfg['FILES.ABSTRACT.REQUEST'] . $fileName;

		fileDownloaderExtender($fullPath);
		exit();
		break;

	case 'downloadCasestudyFile':
		$fileName         = $_REQUEST['fileName'];
		$fullPath         = "../../" . $cfg['FILES.CASE.STUDY'] . $fileName;

		fileDownloaderExtender($fullPath);
		exit();
		break;

	case 'search_abstract_tags_u':
		pageRedirection("abstract.free_papers.taging.php", 5, "&sta=U");
		exit();
		break;

	case 'AskToRemove':
		$delegateId      = $_REQUEST['id'];
		// REMOVING USER

		$sqlRemove       = array();
		$sqlRemove['QUERY']       = "UPDATE " . _DB_USER_REGISTRATION_ . " 
										   SET `status` = 'D' 
										 WHERE `id`     ='" . $delegateId . "'";

		$mycms->sql_update($sqlRemove);
		pageRedirection("abstract.free_papers_unregistereduser.php", 3);
		exit();
		break;

	case 'RemoveAbstract':
		echo $abstractId      = $_REQUEST['id'];
		// REMOVING USER

		$sqlRemove       = array();
		$sqlRemove['QUERY']       = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
										   SET `status` = 'D' 
										 WHERE `id`     ='" . $abstractId . "'";

		$mycms->sql_update($sqlRemove);
		pageRedirection("abstract_submited.php", 3);
		exit();
		break;

	case 'search_abstract_tags':
		pageRedirection("abstract.free_papers.taging.php", 5, "");
		exit();
		break;

	case 'updateTag':
		$abstractIdArray  = $_REQUEST['abstractId'];
		$abs_status		  = $_REQUEST['tag'];
		if ($abs_status != "") {
			if ($abstractIdArray) {
				foreach ($abstractIdArray as $i => $abstractId) {
					$sqlActive['QUERY']   = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
										   SET `abs_status` = '" . $abs_status . "' 
										 WHERE `id` = '" . $abstractId . "'";

					$mycms->sql_update($sqlActive);
				}
			}
		}
		$mycms->redirect("abstract.free_papers.taging.php" . $_REQUEST['string']);
		exit();
		break;

	case 'updateResult':
		$abstractId  		= $_REQUEST['abstractId'];
		$abstract_result	= $_REQUEST['abstract_result'];
		if ($abstract_result != "") {
			$sqlActive['QUERY']   = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
										    SET `abstract_result` = '" . $abstract_result . "' 
										  WHERE `id` = '" . $abstractId . "'";

			$mycms->sql_update($sqlActive);
		}
		//pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'updatePresentationDecision':
		$abstractId  					= $_REQUEST['abstractId'];
		$abstract_presentation_decision	= $_REQUEST['abstract_presentation_decision'];
		if ($abstract_presentation_decision != "") {
			$sqlActive['QUERY']    = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
										     SET `abstract_presentation_decision` = '" . $abstract_presentation_decision . "' 
										   WHERE `id` = '" . $abstractId . "'";

			$mycms->sql_update($sqlActive);
		}
		//pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'updateInspectionTime':
		$abstractId  					= $_REQUEST['abstractId'];
		$inspection_date				= $_REQUEST['inspection_date'];
		$inspection_time				= $_REQUEST['inspection_time'];
		$composedDateTime				= $inspection_date . ' ' . $inspection_time;
		$inspection_date_time			= $mycms->cDate('Y-m-d H:i:00', $composedDateTime);
		if ($inspection_date_time != "") {
			$sqlActive['QUERY']    = "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
										     SET `inspection_date_time` = '" . $inspection_date_time . "' 
										   WHERE `id` = '" . $abstractId . "'";
			$mycms->sql_update($sqlActive);
		}
		//pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'NS':
		$mycms->redirect("abstract.free_papers.taging.php");
		exit();
		break;

	case 'proofRead':
		proofRead();
		pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'revertProofRead':
		revertProofRead();
		pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'allocateAbstract':
		allocateAbstract();
		pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;

	case 'sendMail':
		sendMail($mycms, $cfg);
		exit();
		break;

	case 'sendAbstractConfirmationMail':
		sendAbstractConfirmationMail($mycms, $cfg);
		exit();
		break;

	case 'sendRegAbstractFinalMail':
		sendRegAbstractFinalMail($mycms, $cfg);
		exit();
		break;

	case 'edit_nomination':
		edit_nomination($mycms, $cfg);
		exit();
		break;

	case 'add_nomination':
		add_nomination($mycms, $cfg);
		exit();
		break;

	case 'removeNomination':
		remove_nomination($mycms, $cfg);
		exit();
		break;





	default:
		pageRedirection("abstract.free_papers.php", 5, "");
		exit();
		break;
}

function edit_abstract_setup($cfg, $mycms)
{
	if ($_REQUEST['guideline_pdf_flag'] == '0') {
		removeGuideLinePdf(1);
		$abstract_guideline_pdf = "";
	} else {
		$abstract_guideline_pdf = $_REQUEST['abstract_guideline_pdf'];
	}
		

	$sql 	=	array();

	$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
									SET 
									`abstract_submission_date`=?,
									`abstract_confirmation_date`=?,
									`abstract_title_word_limit`=?,
									`abstract_total_word_limit`=?,
									`abstract_submission_type`=?,
									`abstract_presentation_type`=?,
									`abstract_word_title_type`=?,
									`abstract_total_word_type`=?,
									`abstract_field_type`=?,
									`abstract_sender_email`=?,
									`abstract_guideline_pdf`=?,
									`guideline_pdf_flag`=?,
									`hod_consent_file_types`=?,
									`abstract_file_types`=?
									WHERE `id` = ?
									";
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_submission_date',    	 'DATA' => $_REQUEST['abstract_submission_date'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_confirmation_date',    	 'DATA' => $_REQUEST['abstract_confirmation_date'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_title_word_limit',    	 'DATA' => $_REQUEST['abstract_title_word_limit'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_total_word_limit',    	 'DATA' => $_REQUEST['abstract_total_word_limit'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_submission_type',    	 'DATA' => json_encode($_REQUEST['submission_type']), 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_presentation_type',    	 'DATA' => json_encode($_REQUEST['presentation_type']), 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_word_title_type',    	 'DATA' => $_REQUEST['abstract_word_title_type'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_total_word_type',    	 'DATA' => $_REQUEST['abstract_total_word_type'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_field_type',    	 'DATA' => json_encode($_REQUEST['abstract_field_type']), 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_sender_email',    	 'DATA' => $_REQUEST['abstract_sender_email'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_guideline_pdf',    	 'DATA' => $abstract_guideline_pdf, 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'guideline_pdf_flag',    	 'DATA' => $_REQUEST['guideline_pdf_flag'], 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'hod_consent_file_types',    	 'DATA' => json_encode($_REQUEST['consent_files']), 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'abstract_file_types',    	 'DATA' => json_encode($_REQUEST['abstract_files']), 		 'TYP' => 's');
	$sql['PARAM'][]		  =	array('FILD' => 'id',    	 'DATA' => 1, 		 'TYP' => 's');

	$mycms->sql_update($sql);



	if ($_FILES['abstract_guideline_pdf_file']['name'] && $_REQUEST['guideline_pdf_flag'] != '0') {

		guidelineImageUpload(1, $_FILES['abstract_guideline_pdf_file']);
	}

	$mycms->redirect('abstract.free_papers.php?show=editSetup');
}

function edit_nomination($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();
	$id      = $_REQUEST['id'];
      $upload_doc = isset($_POST['upload_doc']) ? 'yes' : 'no';

	$sqlEditNomination       = array();
	$sqlEditNomination['QUERY']       = "UPDATE " . _DB_AWARD_MASTER_ . " 
										   SET  `award_name` = ? ,
										    	`award_description` = ? ,
										  		`related_topic_id` = ? ,
										    	`related_category_id` = ? ,
												`doc_upload`=?,
												`suporting_document_name`=?,
												`suporting_document_type`=?,
										  		`modified_by` = ?,
											    `modified_ip` = ?,
											    `modified_session_id` = ?,
											    `modified_on` = ?,
										   		`status` = ?
										 WHERE `id`     =?";

	$sqlEditNomination['PARAM'][]   = array('FILD' => 'award_name',            		 'DATA' => $_REQUEST['award_name'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'award_description',           'DATA' => $_REQUEST['award_description'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'related_topic_id',            'DATA' => $_REQUEST['related_topic_id'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'related_category_id',         'DATA' => $_REQUEST['related_category_id'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]		  =	array('FILD' => 'doc_upload',    	 'DATA' => $upload_doc, 		 'TYP' => 's');
	$sqlEditNomination['PARAM'][]		  =	array('FILD' => 'suporting_document_name',    	 'DATA' =>$_REQUEST['doc_type'], 		 'TYP' => 's');
	$sqlEditNomination['PARAM'][]		  =	array('FILD' => 'suporting_document_type',    	 'DATA' => json_encode($_REQUEST['abstract_files']), 		 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'modified_by',            	 'DATA' => $loggedUserID,  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'modified_ip',                 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'modified_session_id',          'DATA' => session_id(),  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'modified_on',           'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'status',           			 'DATA' =>  $_REQUEST['status'],   	 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'id',           			 'DATA' =>  $id,   	 'TYP' => 's');

	$mycms->sql_update($sqlEditNomination);
	  $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#absnom");
}

function add_nomination($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();
	$id      = $_REQUEST['id'];
	// echo '<pre>'; print_r($_REQUEST);die;
      $upload_doc = isset($_POST['upload_doc']) ? 'yes' : 'no';

	$sqlEditNomination       = array();
	$sqlEditNomination['QUERY']       = "INSERT INTO " . _DB_AWARD_MASTER_ . " 
										   SET  `award_name` = ? ,
										    	`award_description` = ? ,
										  		`related_topic_id` = ? ,
										    	`related_category_id` = ? ,
												`doc_upload`=?,
												`suporting_document_name`=?,
												`suporting_document_type`=?,
										  		`created_by` = ?,
											    `created_ip` = ?,
											    `created_session_id` = ?,
											    `created_on` = ?,
										   		`status` = ?";

	$sqlEditNomination['PARAM'][]   = array('FILD' => 'award_name',            		 'DATA' => $_REQUEST['award_name'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'award_description',           'DATA' => $_REQUEST['award_description'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'related_topic_id',            'DATA' => $_REQUEST['related_topic_id'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'related_category_id',         'DATA' => $_REQUEST['related_category_id'],  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]		  =	array('FILD' => 'doc_upload',    	 'DATA' => $upload_doc, 		 'TYP' => 's');
	$sqlEditNomination['PARAM'][]		  =	array('FILD' => 'suporting_document_name',    	 'DATA' =>$_REQUEST['doc_type'], 		 'TYP' => 's');
	$sqlEditNomination['PARAM'][]		  =	array('FILD' => 'suporting_document_type',    	 'DATA' => json_encode($_REQUEST['abstract_files']), 		 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'created_by',            	 'DATA' => $loggedUserID,  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'created_ip',                 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'created_session_id',          'DATA' => session_id(),  			 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'created_on',           'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$sqlEditNomination['PARAM'][]   = array('FILD' => 'status',           			 'DATA' =>  $_REQUEST['status'],   	 'TYP' => 's');
	$mycms->sql_insert($sqlEditNomination);
	   $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#absnom");
}

function remove_nomination($mycms, $cfg)
{
	$id      = $_REQUEST['ID'];
	$sqlEditNomination       = array();
	$sqlEditNomination['QUERY']       = "UPDATE " . _DB_AWARD_MASTER_ . " 
										   SET  `status` = 'D' 
										 WHERE `id`     ='" . $id . "'";
	$mycms->sql_update($sqlEditNomination);
	  $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#absnom");
}

function guidelineImageUpload($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'GUIDELINE_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['FILES.ABSTRACT.REQUEST'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_COMPANY_INFORMATION_ . "
														  SET `abstract_guideline_pdf_file` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function removeGuideLinePdf($id)
{
	global $mycms, $cfg;
	$sqlPdfFile = array();
	$sqlPdfFile['QUERY']           = "SELECT abstract_guideline_pdf_file FROM " . _DB_COMPANY_INFORMATION_ . "
														WHERE `id` = '" . $id . "'";
	$res = $mycms->sql_select($sqlPdfFile, false);
	$imgpath = '../../' . $cfg['FILES.ABSTRACT.REQUEST'] . $res[0]['abstract_guideline_pdf_file'];
	unlink($imgpath);
	$sqlPdfFile = array();
	$sqlPdfFile['QUERY']           = "   UPDATE " . _DB_COMPANY_INFORMATION_ . "
														  SET `abstract_guideline_pdf_file` = '' 
														WHERE `id` = '" . $id . "'";
	$mycms->sql_update($sqlPdfFile, false);
}

function sendMail($mycms, $cfg)
{
	global $loggedUserID;

	$userId   				= trim($_REQUEST['userId']);
	$abstractId   			= trim($_REQUEST['abstractId']);
	$user_email_ids		    = $_REQUEST['user_email_id'];
	$user_full_name 		= trim($_REQUEST['user_full_name']);
	$mail_subject  			= trim($_REQUEST['mail_subject']);

	$mailType   		    = trim($_REQUEST['mailType']);
	$submission   		    = trim($_REQUEST['submission']);

	$mail_body				= trim($_REQUEST['mail_body']);

	if ($submission === 'SEND MAIL') {
		foreach ($user_email_ids as $k => $user_email_id) {
			if (trim($user_email_id) != '') {
				$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', '', '', 'AICC RCOG 2019', 'secretariat@aiccrcog2019.com'); ////$cfg['ADMIN_EMAIL']

				$sqlInsert = array();
				$sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_ABSTRACT_COMMUNICATION_MAIL_ . " 
													   SET `delegateId`					= '" . $userId . "', 
													   	   `abstractId`					= '" . $abstractId . "', 
														   `emailId`					= '" . $user_email_id . "', 
														   `emailType`					= '" . $mailType . "', 
														   `emailSubject`				= '" . addslashes($mail_subject) . "', 
														   `emailContent` 				= '" . addslashes($mail_body) . "', 
														   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
														   `created_by` 				= '" . $loggedUserID . "',
														   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
														   `created_sessionId` 			= '" . session_id() . "',
														   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
														   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
				$lastInsertId 	= $mycms->sql_insert($sqlInsert);
			}
		}
		pageRedirection("abstract.free_papers.php", 'Mail sent successfully');
	} elseif (false && $submission === 'DOWNLOAD PDF') {
		include_once('../../includes/pdfcrowd.php');
		try {
			$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);
			$client->enableImages(true);
			$client->setPageWidth("210mm");
			$client->setPageHeight("330mm");
			$pdf = $client->convertHtml($mail_body);
			header("Content-Type: application/pdf");
			header("Cache-Control: no-cache");
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=\"email_to_" . str_replace(' ', '_', $user_full_name) . '_' . date('his') . ".pdf\"");
			echo $pdf;
		} catch (Exception $e) {
			echo $mail_body;
?>
			<script>
				window.print();
			</script>
		<?
		}
	} elseif (false && $submission === 'DOWNLOAD DOC') {
		try {
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="email_to_' . str_replace(' ', '_', $user_full_name) . '_' . date('his') . '.doc"');
			header("Content-type: application/vnd.ms-word;"); // charset=Windows-1252
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0'); //
			echo $mail_body;
		} catch (Exception $e) {
			echo $mail_body;
		?>
			<script>
				window.print();
			</script>
<?
		}
	}
}

function sendRegAbstractFinalMail($mycms, $cfg)
{

	$loggedUserID = $mycms->getLoggedUserId();

	$delegateId   		    = $_REQUEST['delegateId'];
	$user_email_id		    = $_REQUEST['user_email_id'];
	$cc_email_ids		        = $_REQUEST['cc_email_id'];
	$user_full_name 		= $_REQUEST['user_full_name'];
	$mail_subject  			= $_REQUEST['mail_subject'];
	$mail_body   		    = $_REQUEST['mail_body'];
	$abstractId   		    = $_REQUEST['abstractId'];
	// $buttonForSpot       = $_REQUEST['buttonForSpot'];

	//echo '<pre>'; print_r($_REQUEST); die;

	$ccEmails = [];
	$cc_emails_display = '';
	foreach ($cc_email_ids as $cc_email) {
		if (!empty($cc_email)) {
			// If the CC email is not empty, add it to the array
			$ccEmails[] = [
				'email' => $cc_email,
				'name' => '' // Optionally, you can include names if available
			];
			$cc_emails_display .= " " . $cc_email;
		}
	}

	$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', $ccEmails, '', '', '', '', $cfg['ADMIN_EMAIL']);
	pageRedirection("abstract.free_papers.php?goto=abstract", 'Mail sent successfully', "");

	if (trim($user_email_id) != '') {
		if ($cc_emails_display != '') {
			$all_mails = $user_email_id . ", CC:" . $cc_emails_display;
		} else {
			$all_mails = $user_email_id;
		}
		$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', '', '', 'AICC RCOG 2019', 'secretariat@aiccrcog2019.com'); ////$cfg['ADMIN_EMAIL']

		$sqlInsert = array();
		$sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_ABSTRACT_COMMUNICATION_MAIL_ . " 
											   SET `delegateId`					= '" . $delegateId . "', 
													`abstractId`					= '" . $abstractId . "', 
												   `emailId`					= '" . $all_mails . "', 
												   `emailType`					= 'Abstract Acceptance Mail', 
												   `emailSubject`				= '" . addslashes($mail_subject) . "', 
												   `emailContent` 				= '" . addslashes($mail_body) . "', 
												   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
												   `created_by` 				= '" . $loggedUserID . "',
												   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
												   `created_sessionId` 			= '" . session_id() . "',
												   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
												   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
		$lastInsertId 	= $mycms->sql_insert($sqlInsert);
	}
}

function sendAbstractConfirmationMail($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();

	$delegateId   		    = $_REQUEST['delegateId'];

	$user_email_id	    = $_REQUEST['user_email_id'];
	$cc_email_ids		        = $_REQUEST['cc_email_id'];
	$user_full_name 		= $_REQUEST['user_full_name'];
	$mail_subject  			= $_REQUEST['mail_subject'];
	$mail_body   		    = $_REQUEST['mail_body'];
	$abstractId   		    = $_REQUEST['abstractId'];
	$buttonForSpot   	    = $_REQUEST['buttonForSpot'];

	//echo '<pre>'; print_r($_REQUEST); die;

	$ccEmails = [];
	$cc_emails_display = '';
	foreach ($cc_email_ids as $cc_email) {
		if (!empty($cc_email)) {
			// If the CC email is not empty, add it to the array
			$ccEmails[] = [
				'email' => $cc_email,
				'name' => '' // Optionally, you can include names if available
			];
			$cc_emails_display .= " " . $cc_email;
		}
	}
	// echo '<pre>'; print_r($mail_body); die;
	$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', $ccEmails, '', '', '', '', $cfg['ADMIN_EMAIL']);

	// foreach ($user_email_ids as $k => $user_email_id) {
	// 	if (trim($user_email_id) != '') {
	// 		$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', '', '', '', '', '', $cfg['ADMIN_EMAIL']);
	// 	}
	// }

	if (trim($user_email_id) != '') {
		if ($cc_emails_display != '') {
			$all_mails = $user_email_id . ", CC:" . $cc_emails_display;
		} else {
			$all_mails = $user_email_id;
		}
		$mycms->send_mail($user_full_name, $user_email_id, $mail_subject, $mail_body, '', '', '', 'AICC RCOG 2019', 'secretariat@aiccrcog2019.com'); ////$cfg['ADMIN_EMAIL']

		$sqlInsert = array();
		$sqlInsert['QUERY']	  			= "INSERT INTO " . _DB_ABSTRACT_COMMUNICATION_MAIL_ . " 
											   SET `delegateId`					= '" . $delegateId . "', 
													`abstractId`					= '" . $abstractId . "', 
												   `emailId`					= '" . $all_mails . "', 
												   `emailType`					= 'Abstract Submission Confirmation Mail', 
												   `emailSubject`				= '" . addslashes($mail_subject) . "', 
												   `emailContent` 				= '" . addslashes($mail_body) . "', 
												   `emailDate` 					= '" . date('Y-m-d H:i:s') . "', 
												   `created_by` 				= '" . $loggedUserID . "',
												   `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "', 
												   `created_sessionId` 			= '" . session_id() . "',
												   `created_browser` 			= '" . $_SERVER['HTTP_USER_AGENT'] . "',
												   `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";
		$lastInsertId 	= $mycms->sql_insert($sqlInsert);
	}
	$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Mail Sent successfully!' // dynamic message
	    	];
     $mycms->redirect("invoice_send_mail_abstract.php?show=sendMail&id=" . $abstractId . "&applicantId=" . $delegateId);
}



function email_update()
{
	global $mycms, $cfg;

	$loggedUserID 		 = $mycms->getLoggedUserId();

	$email				 = trim($_REQUEST['new_email_id']);
	$oldEmail			 = $_REQUEST['old_email_id'];
	$delegateId			 = $_REQUEST['user_id'];


	$sqlUpdateAbstractDtls = array();
	$sqlUpdateAbstractDtls['QUERY']         =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
													   SET `applicant_email_id`	= '" . $email . "' 												
													 WHERE `applicant_id` 		= '" . $delegateId . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);

	$sqlUpdateRecord 			 =	array();
	$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_USER_REGISTRATION_ . "
										   SET `user_email_id` = ?, 
											   `modified_by` = ?,
											   `modified_ip` = ?,
											   `modified_sessionId` = ?,
											   `modified_dateTime` = ?
										 WHERE `id` = ?";
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'user_email_id',            	 'DATA' => addslashes($email),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 'DATA' => $loggedUserID,  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            'DATA' => session_id(),  			 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 'DATA' => $delegateId,   			 'TYP' => 's');

	$mycms->sql_update($sqlUpdateRecord);

	$sqlInsertRecord 			 =	array();
	$sqlInsertRecord['QUERY']	 = "INSERT INTO " . _DB_OLD_CONTACT_HISTORY_ . "
										SET `delegate_id` = ?,
											`old_email_id` = ?,
											`status` = ?, 
											`created_by` = ?,
											`created_ip` = ?,
											`created_sessionId` = ?,
											`created_dateTime` = ?";

	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'delegate_id',            	 'DATA' => addslashes($delegateId),   'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'old_email_id',            	 'DATA' => addslashes($oldEmail),   	 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'status',            	 	 'DATA' => 'A',   	 				 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_by',            	 'DATA' => $loggedUserID,   			 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_ip',            	 'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_sessionId',       	 'DATA' => session_id(),   	 		 'TYP' => 's');
	$sqlInsertRecord['PARAM'][]   = array('FILD' => 'created_dateTime',          'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
	$lastInsertedId = $mycms->sql_insert($sqlInsertRecord);
}

function proofRead()
{
	global $mycms, $cfg;

	$abstract_id 							 		= $_REQUEST['abstractId'];
	$delegate_id 							 		= $_REQUEST['delegateId'];

	// echo '<pre>'; print_r($_REQUEST); die;

	$proof_abstract_title							= addslashes(trim($_REQUEST['proof_abstract_title']));



	/*$proof_abstract_background                 	  	= addslashes(trim($_REQUEST['proof_abstract_background']));
		$proof_abstract_background_aims                 = addslashes(trim($_REQUEST['proof_abstract_background_aims']));
		$proof_abstract_material_methods                = addslashes(trim($_REQUEST['proof_abstract_material_methods']));
		$proof_abstract_results             			= addslashes(trim($_REQUEST['proof_abstract_results']));
		$proof_abstract_conclution              		= addslashes(trim($_REQUEST['proof_abstract_conclution']));
		
		$proof_abstract_description   				 	= addslashes(trim($_REQUEST['proof_abstract_description']));*/

	$proof_fields1 = (!empty($_REQUEST['proof_fields1'])) ? addslashes(trim($_REQUEST['proof_fields1'])) : 'NULL';
	$proof_fields2 = (!empty($_REQUEST['proof_fields2'])) ? addslashes(trim($_REQUEST['proof_fields2'])) : 'NULL';
	$proof_fields3 = (!empty($_REQUEST['proof_fields3'])) ? addslashes(trim($_REQUEST['proof_fields3'])) : 'NULL';
	$proof_fields4 = (!empty($_REQUEST['proof_fields4'])) ? addslashes(trim($_REQUEST['proof_fields4'])) : 'NULL';
	$proof_fields5 = (!empty($_REQUEST['proof_fields5'])) ? addslashes(trim($_REQUEST['proof_fields5'])) : 'NULL';
	$proof_fields6 = (!empty($_REQUEST['proof_fields6'])) ? addslashes(trim($_REQUEST['proof_fields6'])) : 'NULL';
	$proof_fields7 = (!empty($_REQUEST['proof_fields7'])) ? addslashes(trim($_REQUEST['proof_fields7'])) : 'NULL';
	
	// $proof_fields1 = (!empty($_REQUEST['proof_fields1'][0])) ? addslashes(trim($_REQUEST['proof_fields1'][0])) : 'NULL';
	// $proof_fields2 = (!empty($_REQUEST['proof_fields2'][0])) ? addslashes(trim($_REQUEST['proof_fields2'][0])) : 'NULL';
	// $proof_fields3 = (!empty($_REQUEST['proof_fields3'][0])) ? addslashes(trim($_REQUEST['proof_fields3'][0])) : 'NULL';
	// $proof_fields4 = (!empty($_REQUEST['proof_fields4'][0])) ? addslashes(trim($_REQUEST['proof_fields4'][0])) : 'NULL';
	// $proof_fields5 = (!empty($_REQUEST['proof_fields5'][0])) ? addslashes(trim($_REQUEST['proof_fields5'][0])) : 'NULL';
	// $proof_fields6 = (!empty($_REQUEST['proof_fields6'][0])) ? addslashes(trim($_REQUEST['proof_fields6'][0])) : 'NULL';
	// $proof_fields7 = (!empty($_REQUEST['proof_fields7'][0])) ? addslashes(trim($_REQUEST['proof_fields7'][0])) : 'NULL';



	$sqlUpdateAbstractDtls = array();
	$sqlUpdateAbstractDtls['QUERY']         =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
													   SET `proof_abstract_title`		= '" . $proof_abstract_title . "' , 
													      `proof_fields1`		= '" . $proof_fields1 . "' ,
														   `proof_fields2` 		= '" . $proof_fields2 . "', 
														   `proof_fields3` 		= '" . $proof_fields3 . "', 
														   `proof_fields4` 		= '" . $proof_fields4 . "', 
														   `proof_fields5` 		= '" . $proof_fields5 . "', 
														   `proof_fields6` 		= '" . $proof_fields6 . "', 
														   `proof_fields7` 		= '" . $proof_fields7 . "'															
													 WHERE `id` = '" . $abstract_id . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);
}


function revertProofRead()
{
	global $mycms, $cfg;

	$abstract_id 							 		= $_REQUEST['abstractId'];
	$delegate_id 							 		= $_REQUEST['delegateId'];

	$sqlUpdateAbstractDtls = array();
	$sqlUpdateAbstractDtls['QUERY']         =  "UPDATE " . _DB_ABSTRACT_REQUEST_ . " 
													   SET `proof_abstract_title`					= NULL ,
														   `proof_abstract_background` 				= NULL, 
														   `proof_abstract_background_aims` 		= NULL, 
														   `proof_abstract_material_methods` 		= NULL, 
														   `proof_abstract_results` 				= NULL, 
														   `proof_abstract_conclution` 				= NULL, 
														   `proof_abstract_description` 			= NULL															
													 WHERE `id` = '" . $abstract_id . "'  ";
	$mycms->sql_update($sqlUpdateAbstractDtls, false);
}

function allocateAbstractOld()
{
	global $mycms, $cfg;

	$abstract_id 							 		= $_REQUEST['abstractId'];
	$reviewerIds 							 		= $_REQUEST['reviewerId'];
    echo "<pre>";
	print_r($_REQUEST);
	die();
	$sqlDelete = array();
	$sqlDelete  = "  DELETE FROM " . _DB_ABSTRACT_ALLOTMENT_ . " 
										 WHERE `abstract_id` = '" . $abstract_id . "'";
	$mycms->sql_query($sqlDelete);


	// FETCHING EARLY REVIEW RESULTS
	$sqlFetchPrevReviewResult['QUERY']           = "  SELECT * 
															FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
														   WHERE `abstract_id` = '" . $abstract_id . "'
															 AND `faculty_id` = '" . $reviewerId . "'";
	$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);
	if ($resultFetchPrevReviewResult) {
		foreach ($resultFetchPrevReviewResult as $keyPrevReviewResult => $rowPrevReviewResult) {
			// MAKE EARLY REVIEW INACTIVE
			$sqlUpdatePrevReviewResult['QUERY']  = "   UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
															  SET `status` = 'I' 
															WHERE `id` = '" . $rowPrevReviewResult['id'] . "'";

			$mycms->sql_update($sqlUpdatePrevReviewResult);

			// MAKE EARLY REVIEW DETAILS INACTIVE
			$sqlUpdatePrevReviewDetails['QUERY'] = "   UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " 
															  SET `status` = 'I'
															WHERE `review_result_id` = '" . $rowPrevReviewResult['id'] . "'";

			$mycms->sql_update($sqlUpdatePrevReviewDetails);
		}
	}


	foreach ($reviewerIds as $kkl => $reviewerId) {
		$sqlInsert = array();
		$sqlInsert['QUERY']         =  "   INSERT INTO " . _DB_ABSTRACT_ALLOTMENT_ . " 
													   SET `abstract_id` = '" . $abstract_id . "' ,
														   `review_user_id` = '" . $reviewerId . "' ";
		$mycms->sql_insert($sqlInsert, false);
	}
}
function allocateAbstract()
{
	global $mycms, $cfg;

	$abstract_id = $_REQUEST['abstractId'];
	$reviewerId  = $_REQUEST['reviewerId'];
	$status      = $_REQUEST['status']; // 1 = ON, 0 = OFF
  
	if ($status == 1) {
		// ✅ Assign reviewer (only if not already assigned)
		$check['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_ALLOTMENT_ . " 
						WHERE abstract_id = ? AND review_user_id = ?";
		$check['PARAM'][] = array('FILD'=>'abstract_id','DATA'=>$abstract_id,'TYP'=>'i');
		$check['PARAM'][] = array('FILD'=>'review_user_id','DATA'=>$reviewerId,'TYP'=>'i');

		$result = $mycms->sql_select($check);

		if (!$result) {
			$insert['QUERY'] = "INSERT INTO " . _DB_ABSTRACT_ALLOTMENT_ . " 
								SET abstract_id = ?, review_user_id = ?";
			$insert['PARAM'][] = array('FILD'=>'abstract_id','DATA'=>$abstract_id,'TYP'=>'i');
			$insert['PARAM'][] = array('FILD'=>'review_user_id','DATA'=>$reviewerId,'TYP'=>'i');
			$mycms->sql_insert($insert, false);

			// 🔹 Deactivate previous review results (only for this reviewer)
			$sqlFetchPrevReviewResult =  array();
			$sqlFetchPrevReviewResult['QUERY'] = "
				SELECT * FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
				WHERE abstract_id = ? AND faculty_id = ?";
			$sqlFetchPrevReviewResult['PARAM'][] = array('FILD'=>'abstract_id','DATA'=>$abstract_id,'TYP'=>'i');
			$sqlFetchPrevReviewResult['PARAM'][] = array('FILD'=>'faculty_id','DATA'=>$reviewerId,'TYP'=>'i');

			$resultFetchPrevReviewResult = $mycms->sql_select($sqlFetchPrevReviewResult);

			if ($resultFetchPrevReviewResult) {
				foreach ($resultFetchPrevReviewResult as $row) {
					// Make review inactive
					$update1 = array();
					$update1['QUERY'] = "UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
										SET status = 'A' WHERE id = ?";
					$update1['PARAM'][] = array('FILD'=>'id','DATA'=>$row['id'],'TYP'=>'i');
					$mycms->sql_update($update1);

					// Make review details inactive
					$update2 = array();
					$update2['QUERY'] = "UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " 
										SET status = 'A' WHERE review_result_id = ?";
					$update2['PARAM'][] = array('FILD'=>'review_result_id','DATA'=>$row['id'],'TYP'=>'i');
					$mycms->sql_update($update2);
				}
			}
		}

	} else {
		// ❌ Remove reviewer
		$delete['QUERY'] = "DELETE FROM " . _DB_ABSTRACT_ALLOTMENT_ . " 
							WHERE abstract_id = ? AND review_user_id = ?";
		$delete['PARAM'][] = array('FILD'=>'abstract_id','DATA'=>$abstract_id,'TYP'=>'i');
		$delete['PARAM'][] = array('FILD'=>'review_user_id','DATA'=>$reviewerId,'TYP'=>'i');
		$mycms->sql_delete($delete);

		// 🔹 Deactivate previous review results (only for this reviewer)
		$sqlFetchPrevReviewResult['QUERY'] = "
			SELECT * FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
			WHERE abstract_id = ? AND faculty_id = ?";
		$sqlFetchPrevReviewResult['PARAM'][] = array('FILD'=>'abstract_id','DATA'=>$abstract_id,'TYP'=>'i');
		$sqlFetchPrevReviewResult['PARAM'][] = array('FILD'=>'faculty_id','DATA'=>$reviewerId,'TYP'=>'i');

		$resultFetchPrevReviewResult = $mycms->sql_select($sqlFetchPrevReviewResult);

		if ($resultFetchPrevReviewResult) {
			foreach ($resultFetchPrevReviewResult as $row) {
				// Make review inactive
				$update1['QUERY'] = "UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_ . " 
									SET status = 'I' WHERE id = ?";
				$update1['PARAM'][] = array('FILD'=>'id','DATA'=>$row['id'],'TYP'=>'i');
				$mycms->sql_update($update1);

				// Make review details inactive
				$update2['QUERY'] = "UPDATE " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " 
									SET status = 'I' WHERE review_result_id = ?";
				$update2['PARAM'][] = array('FILD'=>'review_result_id','DATA'=>$row['id'],'TYP'=>'i');
				$mycms->sql_update($update2);
			}
		}
	}

echo "success";
exit;
}

/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey                       		       		 = "_pgn_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();

	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['src_full_name']  		             = trim($_REQUEST['src_full_name']);
	$searchArray['src_applicant_middle_name']  		 = trim($_REQUEST['src_applicant_middle_name']);
	$searchArray['src_applicant_last_name']  		 = trim($_REQUEST['src_applicant_last_name']);
	$searchArray['src_applicant_unique_sequence']  	 = trim($_REQUEST['src_applicant_unique_sequence']);
	$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
	$searchArray['src_applicant_email_id']  		 = trim($_REQUEST['src_applicant_email_id']);
	$searchArray['src_paper_presentation_category']  = trim($_REQUEST['src_paper_presentation_category']);
	$searchArray['src_apply_from_date']  			 = trim($_REQUEST['src_apply_from_date']);
	$searchArray['src_apply_to_date']  				 = trim($_REQUEST['src_apply_to_date']);
	$searchArray['src_abstract_topic_id']  			 = trim($_REQUEST['src_abstract_topic_id']);

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
?>