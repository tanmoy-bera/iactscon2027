<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
//echo $action. "<pre>";print_r($_REQUEST);echo "</pre>";die;
switch ($action) {
	case 'search_classification':

		pageRedirection('registration_tariff.php', 5, "");
		// exit();
		break;

		/***************************************************************************/
		/*                      UPDATE TARIFF CLASSIFICATION                       */
		/***************************************************************************/
	case 'update':
		$classification_title 	     	 = addslashes($_REQUEST['classification_title']);


		$editseatlimit 	           		 = addslashes($_REQUEST['seat_limit_add']);
		$sequence_by            	     = addslashes($_REQUEST['sequence_by']);
		$type 	                   		 = addslashes($_REQUEST['type']);
		$currency 	              		 = addslashes($_REQUEST['currency']);
		$inclusion_lunch_date 	       = $_REQUEST['inclusion_lunch_date'];
		$inclusion_dinner_date = $_REQUEST['inclusion_dinner_date'];
		$inclusion_conference_kit = $_REQUEST['inclusion_conference_kit'];
		$inclusion_sci_hall 	    		 = $_REQUEST['inclusion_sci_hall'];
		$inclusion_exb_area 	    		 = $_REQUEST['inclusion_exb_area'];
		$inclusion_tea_coffee 	    		 = $_REQUEST['inclusion_tea_coffee'];
		$id 			          		 = addslashes($_REQUEST['id']);

		$json_inclusion_lunch_date = json_encode($inclusion_lunch_date);
		$json_inclusion_dinner_date = json_encode($inclusion_dinner_date);

		$sql		  =	array();
		$sql['QUERY'] = "UPDATE " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
								SET `classification_title`	  		= ?,
									`inclusion_lunch_date`      	= ?,
									`inclusion_dinner_date` = ?,
									`inclusion_conference_kit`= ?,
									`inclusion_sci_hall`= ?,
									 `inclusion_exb_area`= ?,
									 `inclusion_tea_coffee`= ?
									
								  WHERE	`id`               			= ?";

		$sql['PARAM'][]	=	array('FILD' => 'classification_title', 'DATA' => $classification_title,   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'inclusion_lunch_date', 'DATA' => $json_inclusion_lunch_date,   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'inclusion_dinner_date', 'DATA' => $json_inclusion_dinner_date,   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'inclusion_conference_kit', 'DATA' => $inclusion_conference_kit,   'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_sci_hall', 'DATA' => $inclusion_sci_hall,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_exb_area', 'DATA' => $inclusion_exb_area,    'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'inclusion_tea_coffee', 'DATA' => $inclusion_tea_coffee,    'TYP' => 's');

		$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');



		$mycms->sql_update($sql);

		//fetching existing icons 
		// $sqlFetchSession = array();
		// $sqlFetchSession['QUERY'] 			= "SELECT `inclusion_lunch_icon`, `inclusion_conference_kit_icon` FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . " 

		// 										WHERE `id` = ?";

		// $sqlFetchSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		// $rowFetchSession = $mycms->sql_select($sqlFetchSession);
		// //echo "<pre>"; print_r($rowFetchSession[0]['icon']);die;


		// if (!empty($_FILES['inclusion_lunch_icon']['name']) && $_FILES['inclusion_lunch_icon']['name'] != '') {
		// 	headerImageUpload($id, $_FILES['inclusion_lunch_icon'], "inclusion_lunch_icon");
		// 	$lunchIconPath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchSession[0]['inclusion_lunch_icon'];
		// 	unlink($lunchIconPath);
		// }
		// if (!empty($_FILES['inclusion_conference_kit_icon']['name']) && $_FILES['inclusion_conference_kit_icon']['name'] != '') {
		// 	headerImageUpload($id, $_FILES['inclusion_conference_kit_icon'], "inclusion_conference_kit_icon");
		// 	$conferenceKitIconPath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchSession[0]['inclusion_conference_kit_icon'];
		// 	unlink($conferenceKitIconPath);
		// }

		pageRedirection('manage_reg_accompany.php', 2, "");
		break;
		/***************************************************************************/
		/*                      Insert TARIFF CLASSIFICATION                       */
		/***************************************************************************/

	case 'add':
		$classification_title 	    = addslashes($_REQUEST['classification_title']);
		$inclusion_lunch_date 	    	= $_REQUEST['inclusion_lunch_date'];
		$inclusion_dinner_date 	= $_REQUEST['inclusion_dinner_date'];
		$inclusion_conference_kit  = $_REQUEST['inclusion_conference_kit'];
		$inclusion_sci_hall 	    		 = $_REQUEST['inclusion_sci_hall'];
		$inclusion_exb_area 	    		 = $_REQUEST['inclusion_exb_area'];
		$inclusion_tea_coffee 	    		 = $_REQUEST['inclusion_tea_coffee'];

		$editseatlimit 	 = addslashes($_REQUEST['seat_limit_add']);
		$sequence_by     = addslashes($_REQUEST['sequence_by']);
		$type 	      	 = addslashes($_REQUEST['type']);
		$currency 	     = addslashes($_REQUEST['currency']);
		$sessionId	     = session_id();
		$userIp		     = $_SERVER['REMOTE_ADDR'];
		$userBrowser     = $_SERVER['HTTP_USER_AGENT'];

		$json_inclusion_lunch_date = json_encode($inclusion_lunch_date);
		$json_inclusion_dinner_date = json_encode($inclusion_dinner_date);

		$sql = array();
		$sql['QUERY'] = "INSERT INTO " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
				                SET  `classification_title`= ?, 
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

		$res         = $mycms->sql_insert($sql, false);
		// headerImageUpload($res, $_FILES['inclusion_lunch_icon'], "inclusion_lunch_icon");
		// headerImageUpload($res, $_FILES['inclusion_conference_kit_icon'], "inclusion_conference_kit_icon");

		pageRedirection('manage_reg_accompany.php', 2, "");
		break;
		/************************ACTIVE**********************/
	case 'Active':
		Active($mycms, $cfg);
		
		break;
		/************************INACTIVE**********************/
	case 'Inactive':
		Inactive($mycms, $cfg);
		
		break;
		/************************ACTIVE FOOD**********************/
	case 'foodPrefActive':
		foodPrefActive($mycms, $cfg);
		
		break;
		/************************INACTIVE FOOD**********************/
	case 'foodPrefInactive':
		foodPrefInactive($mycms, $cfg);
		
		break;

		/************************Check Title**********************/
	case 'checktitle':
		checkTitle($mycms, $cfg);
		//pageRedirection('manage_reg_accompany.php', 2, "");
		break;
}

/*================ ACTIVE WORKSHOP ==================*/
function Active($mycms, $cfg)
{
	$sql['QUERY'] = "UPDATE " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
						SET `status`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'A', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
	// break;
}

/*================ INACTIVE  =================*/
function Inactive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
						SET `status`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'I', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
	// break;
}
/*================ INACTIVE  =================*/
function checkTitle($mycms, $cfg)
{

	$sql = array();
	$sql['QUERY'] = "SELECT count(*) as totalCount FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
					 WHERE `classification_title` = '" . trim($_REQUEST['title']) . "'";
	$result = $mycms->sql_select($sql, false);
	//var_dump($result);
	if ($result[0]['totalCount'] == 0) {
		echo "successs";
	} else {
		echo "error";
	}
	exit();
}
/*================ INACTIVE Food Preference =================*/
function foodPrefInactive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
						SET `food_preference`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'food_preference', 					'DATA' => 'I', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
	// break;
}
/*================ ACTIVE Food Preference =================*/
function foodPrefActive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
						SET `food_preference`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'food_preference', 					'DATA' => 'A', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
	// break;
}

function headerImageUpload($participantId, $header_Image, $column_name)
{
	global $mycms, $cfg;

	//echo 'ID='.$participantId; die;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId . "_" . $column_name, 4, '0', STR_PAD_LEFT);
		$rand							= 'ACCOMPCLASS_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_ACCOMPANY_CLASSIFICATION_ . "
													SET `" . $column_name . "` = '" . $userImageFileName . "' 
													WHERE `id` = '" . $participantId . "'";


			$mycms->sql_update($sqlUserImage, false);
		}
	}
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
