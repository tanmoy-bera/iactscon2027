
<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {
	case 'search_classification':

		pageRedirection('registration_tariff.php', 5, "");
		exit();
		break;

		/***************************************************************************/
		/*                      UPDATE TARIFF CLASSIFICATION                       */
	/***************************************************************************/
	case 'update':
		$classification_title 	    = addslashes($_REQUEST['classification_title']);
		$editseatlimit 	            = addslashes($_REQUEST['seat_limit_add']);
		$sequence_by                = addslashes($_REQUEST['sequence_by']);
		$type 	                    = addslashes($_REQUEST['type']);
		$currency 	                = addslashes($_REQUEST['currency']);
		$mail_lunch_details 	    = addslashes($_REQUEST['mail_lunch_details']);
		$mail_dinner_details 	    = addslashes($_REQUEST['mail_dinner_details']);
		$mail_gala_dinner_details 	 = addslashes($_REQUEST['mail_gala_dinner_details']);
		$mail_inaugural_dinner_details = addslashes($_REQUEST['mail_inaugural_dinner_details']);
		$inclusion_lunch_date 	       = $_REQUEST['inclusion_lunch_date'];
		$inclusion_dinner_date = $_REQUEST['inclusion_dinner_date'];
		$inclusion_conference_kit = $_REQUEST['inclusion_conference_kit'];
		$inclusion_sci_hall 	    		 = $_REQUEST['inclusion_sci_hall'];
		$inclusion_exb_area 	    		 = $_REQUEST['inclusion_exb_area'];
		$inclusion_tea_coffee 	    		 = $_REQUEST['inclusion_tea_coffee'];
		$id 			            = addslashes($_REQUEST['id']);

		$json_inclusion_lunch_date = json_encode($inclusion_lunch_date);
		$json_inclusion_dinner_date = json_encode($inclusion_dinner_date);
		//print_r($json_inclusion_lunch_date);




		$sql1['QUERY'] = "UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . " 
				                SET  `classification_title`='" . $classification_title . "', 
				                     `seat_limit`='" . $editseatlimit . "', 
				                     `type`='" . $type . "',
									 `currency`='" . $currency . "',
									 `mail_lunch_details`='" . $mail_lunch_details . "',
									 `mail_dinner_details`='" . $mail_dinner_details . "',
									 `mail_gala_dinner_details`='" . $mail_gala_dinner_details . "',
									 `mail_inaugural_dinner_details`='" . $mail_inaugural_dinner_details . "',
									 `inclusion_lunch_date`='" . $json_inclusion_lunch_date . "',
									 `inclusion_dinner_date`='" . $json_inclusion_dinner_date . "',
									 `inclusion_conference_kit`='" . $inclusion_conference_kit . "',
									 `inclusion_sci_hall`='" . $inclusion_sci_hall . "',
									 `inclusion_exb_area`='" . $inclusion_exb_area . "',
									 `inclusion_tea_coffee`='" . $inclusion_tea_coffee . "',
								  	 `sequence_by`='" . $sequence_by . "'
				               WHERE `id`='" . $id . "'";

		$res         = $mycms->sql_update($sql1);

		//fetching existing icons 
		$sqlFetchSession = array();
		$sqlFetchSession['QUERY'] 			= "SELECT `icon`, `inclusion_lunch_icon`, `inclusion_conference_kit_icon` FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
												
												WHERE `id` = ?";

		$sqlFetchSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$rowFetchSession = $mycms->sql_select($sqlFetchSession);
		//echo "<pre>"; print_r($rowFetchSession[0]['icon']);die;

		if (!empty($_FILES['icon']['name']) && $_FILES['icon']['name'] != '') {
			headerImageUpload($id, $_FILES['icon'], "icon");
			$iconPath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchSession[0]['icon'];
			unlink($iconPath);
		}
		if (!empty($_FILES['inclusion_lunch_icon']['name']) && $_FILES['inclusion_lunch_icon']['name'] != '') {
			headerImageUpload($id, $_FILES['inclusion_lunch_icon'], "inclusion_lunch_icon");
			$lunchIconPath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchSession[0]['inclusion_lunch_icon'];
			unlink($lunchIconPath);
		}
		if (!empty($_FILES['inclusion_conference_kit_icon']['name']) && $_FILES['inclusion_conference_kit_icon']['name'] != '') {
			headerImageUpload($id, $_FILES['inclusion_conference_kit_icon'], "inclusion_conference_kit_icon");
			$conferenceKitIconPath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchSession[0]['inclusion_conference_kit_icon'];
			unlink($conferenceKitIconPath);
		}

		if ($res) {
			echo json_encode(['status' => true, 'message' => 'Classification added successfully', 'id' => $lastInsertId]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Insertion failed']);
		}

		break;
		/***************************************************************************/
		/*                      Insert TARIFF CLASSIFICATION                       */
	/***************************************************************************/

	case 'add':
		$inclusion_lunch_date = "";
		$classification_title 	    = addslashes($_REQUEST['classification_title']);
		$editseatlimit 	 = addslashes($_REQUEST['seat_limit_add']);
		$sequence_by     = addslashes($_REQUEST['sequence_by']);
		$type 	      	            		 = addslashes($_REQUEST['type']);
		$currency 	     					 = addslashes($_REQUEST['currency']);
		$mail_lunch_details 	    		 = addslashes($_REQUEST['mail_lunch_details']);
		$mail_dinner_details 	    		 = addslashes($_REQUEST['mail_dinner_details']);
		$mail_gala_dinner_details 	 		 = addslashes($_REQUEST['mail_gala_dinner_details']);
		$mail_inaugural_dinner_details 	     = addslashes($_REQUEST['mail_inaugural_dinner_details']);
		$inclusion_lunch_date 	    		 = $_REQUEST['inclusion_lunch_date'];
		$inclusion_dinner_date 	    		 = $_REQUEST['inclusion_dinner_date'];
		$inclusion_conference_kit 	    		 = $_REQUEST['inclusion_conference_kit'];
		$inclusion_sci_hall 	    		 = $_REQUEST['inclusion_sci_hall'];
		$inclusion_exb_area 	    		 = $_REQUEST['inclusion_exb_area'];
		$inclusion_tea_coffee 	    		 = $_REQUEST['inclusion_tea_coffee'];

		$json_inclusion_lunch_date = json_encode($inclusion_lunch_date);
		$json_inclusion_dinner_date = json_encode($inclusion_dinner_date);
		//print_r($jsonSelectedDates);die;

		$sessionId	     = session_id();

		$userIp		     = $_SERVER['REMOTE_ADDR'];
		$userBrowser     = $_SERVER['HTTP_USER_AGENT'];

		$sql = array();
		$sql['QUERY'] = "INSERT INTO " . _DB_REGISTRATION_CLASSIFICATION_ . " 
				                SET  `classification_title`= ?, 
				                     `seat_limit`= ?, 
				                     `type`= ?,
									 `currency`= ?,
								  	 `sequence_by`= ?,
								  	 `mail_lunch_details`= ?,
								  	 `mail_dinner_details`= ?,
								  	 `mail_gala_dinner_details`= ?,
								  	 `mail_inaugural_dinner_details`= ?,
								  	 `inclusion_lunch_date`= ?,
									 `inclusion_dinner_date`= ?,
									 `inclusion_conference_kit`= ?,
									 `inclusion_sci_hall`= ?,
									 `inclusion_exb_area`= ?,
									 `inclusion_tea_coffee`= ?,
								  	 `created_by` = ?, 
								  	 `created_ip` = ?, 
								  	 `created_sessionId` = ?,
								  	 `created_dateTime` = ?";
		$sql['PARAM'][]   = array('FILD' => 'classification_title', 'DATA' => $classification_title,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'seat_limit', 'DATA' => $editseatlimit,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'type', 'DATA' => $type,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'currency', 'DATA' => $currency,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'sequence_by', 'DATA' => $sequence_by,    'TYP' => 's');

		$sql['PARAM'][]   = array('FILD' => 'mail_lunch_details', 'DATA' => $mail_lunch_details,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'mail_dinner_details', 'DATA' => $mail_dinner_details,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'mail_gala_dinner_details', 'DATA' => $mail_gala_dinner_details,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'mail_inaugural_dinner_details', 'DATA' => $mail_inaugural_dinner_details,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_lunch_date', 'DATA' => $json_inclusion_lunch_date,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_dinner_date', 'DATA' => $json_inclusion_dinner_date,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_conference_kit', 'DATA' => $inclusion_conference_kit,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_sci_hall', 'DATA' => $inclusion_sci_hall,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_exb_area', 'DATA' => $inclusion_exb_area,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_tea_coffee', 'DATA' => $inclusion_tea_coffee,    'TYP' => 's');

		$sql['PARAM'][]   = array('FILD' => 'created_by',        'DATA' => $loggedUserID,                      'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

		$lastInsertId         = $mycms->sql_insert($sql);

		headerImageUpload($lastInsertId, $_FILES['icon'], "icon");
		headerImageUpload($lastInsertId, $_FILES['inclusion_lunch_icon'], "inclusion_lunch_icon");
		headerImageUpload($lastInsertId, $_FILES['inclusion_conference_kit_icon'], "inclusion_conference_kit_icon");

		// AJAX response

		if ($lastInsertId) {
			echo json_encode(['status' => true, 'message' => 'Classification added successfully', 'id' => $lastInsertId]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Insertion failed']);
		}
		exit;


		break;

	case 'getRegClassification':
		$id = intval($_REQUEST['id'] ?? 0);
		if ($id <= 0) {
			header('Content-Type: application/json');
			echo json_encode(['status' => false, 'message' => 'Invalid id']);
			exit;
		}
		$sql = ['QUERY' => "SELECT * FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " WHERE id = ? LIMIT 1"];
		$sql['PARAM'][] = ['FILD' => 'id', 'DATA' => $id, 'TYP' => 's'];
		$row = $mycms->sql_select($sql);
		
		if (!empty($row[0])) {
			header('Content-Type: application/json');
			echo json_encode(['status' => true, 'data' => $row[0]]);
			exit;
		}
		header('Content-Type: application/json');
		echo json_encode(['status' => false, 'message' => 'Record not found']);
		exit;
		break;

	/************************ACTIVE**********************/
	case 'Active':
		Active($mycms, $cfg);
		break;
	/************************ACTIVE**********************/
	case 'Inactive':
		Inactive($mycms, $cfg);
		break;
}

/*================ ACTIVE WORKSHOP ==================*/
function Active($mycms, $cfg)
{
	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . " 
						SET `status`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'A', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}
/*================ INACTIVE WORKSHOP =================*/
function Inactive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . " 
						SET `status`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'I', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}
/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey                       		       = "_pgn_";
	$pageKeyVal                    		       = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString                 		       = "";
	$searchArray                   		       = array();

	$searchArray[$pageKey]         		       = $pageKeyVal;
	$searchArray['src_tariff_classification']  = trim($_REQUEST['src_tariff_classification']);

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}


function headerImageUpload($participantId, $header_Image, $column_name)
{
	global $mycms, $cfg;

	//echo 'ID='.$participantId; die;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId . "_" . $column_name, 4, '0', STR_PAD_LEFT);
		$rand							= 'REGCLASS_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . "
													  SET `" . $column_name . "` = '" . $userImageFileName . "' 
													WHERE `id` = '" . $participantId . "'";


			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
?>