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


		pageRedirection('manage_reg_classification.php', 2, "");
		break;
		/***************************************************************************/
		/*                      Insert TARIFF CLASSIFICATION                       */
		/***************************************************************************/

		/************************ACTIVE**********************/
	case 'Active':
		Active($mycms, $cfg);
		pageRedirection('manage_reg_classification.php', 2, "");
		break;
		/************************ACTIVE**********************/
	case 'Inactive':
		Inactive($mycms, $cfg);
		pageRedirection('manage_reg_classification.php', 2, "");
		break;

	case 'Remove':
		Remove($mycms, $cfg);
		pageRedirection('manage_reg_classification.php', 2, "");
		break;
}

/*================ ACTIVE WORKSHOP ==================*/
function Active($mycms, $cfg)
{
	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . " 
					SET `status`	 = 'A' 
				  WHERE `id`		 = " . $_REQUEST['id'] . "";
	$mycms->sql_update($sql);
	pageRedirection("manage_reg_classification.php", 2, "");
	exit();
}
/*================ INACTIVE WORKSHOP =================*/
function Inactive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . " 
					   SET `status` = 'I' 
					 WHERE `id`	    = " . $_REQUEST['id'] . "";
	$mycms->sql_update($sql);
	pageRedirection("manage_reg_classification.php", 2, "");
	exit();
}

function Remove($mycms, $cfg)
{
	//echo $_REQUEST['id']; die;
	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_CLASSIFICATION_ . " 
					   SET `status` = 'D' 
					 WHERE `id`	    = " . $_REQUEST['id'] . "";
	$mycms->sql_update($sql);
	pageRedirection("manage_reg_classification.php", 2, "");
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
