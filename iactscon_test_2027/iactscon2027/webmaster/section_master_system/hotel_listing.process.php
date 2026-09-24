<?php
include_once('includes/init.php');

$loggedUserID = $mycms->getLoggedUserId();


if ($_REQUEST['action'] == 'deleteAccessories') {

	if ($_REQUEST['id'] != '' && $_REQUEST['id'] > 0) {
		$sql		  =	array();
		$sql['QUERY'] = "UPDATE" . _DB_ACCOMMODATION_ACCESSORIES_ . " 
								SET status 		= ?
							  WHERE	`id`        = ?";

		$sql['PARAM'][]	=	array('FILD' => 'status', 						'DATA' => 'D', 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $_REQUEST['id'], 		   'TYP' => 's');

		$mycms->sql_update($sql);
	}


	//pageRedirection(1,"hotel_listing.php",2);
	exit();
}

switch ($action) {
		/************************************************************************/
		/*                             SEARCH HOTEL                             */
		/************************************************************************/
	case 'search_hotel':

		pageRedirection(1, "hotel_listing.php", 5);
		exit();
		break;

		/************************************************************************/
		/*                             ACTIVE HOTEL                             */
		/************************************************************************/
	case 'Active':

		$sql		  =	array();
		$sql['QUERY'] = "UPDATE" . _DB_MASTER_HOTEL_ . " 
								SET status 		= ?
							  WHERE	`id`        = ?";

		$sql['PARAM'][]	=	array('FILD' => 'status', 						'DATA' => 'A', 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $_REQUEST['id'], 		   'TYP' => 's');

		$mycms->sql_update($sql);

		pageRedirection(1, "hotel_listing.php", 2);
		exit();
		break;

		/************************************************************************/
		/*                           INACTIVE HOTEL                             */
		/************************************************************************/
	case 'Inactive':

		$sql		  =	array();
		$sql['QUERY'] = "UPDATE" . _DB_MASTER_HOTEL_ . " 
								SET status 		= ?
							  WHERE	`id`        = ?";

		$sql['PARAM'][]	=	array('FILD' => 'status', 						'DATA' => 'I', 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $_REQUEST['id'], 		   'TYP' => 's');

		$mycms->sql_update($sql);

		pageRedirection(1, "hotel_listing.php", 2);
		exit();
		break;

	case 'deleteAccessories':

		echo $_REQUEST['id'];
		die;

		$sql		  =	array();
		$sql['QUERY'] = "UPDATE" . _DB_ACCOMMODATION_ACCESSORIES_ . " 
								SET status 		= ?
							  WHERE	`id`        = ?";

		$sql['PARAM'][]	=	array('FILD' => 'status', 						'DATA' => 'D', 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $_REQUEST['id'], 		   'TYP' => 's');

		$mycms->sql_update($sql);

		//pageRedirection(1,"hotel_listing.php",2);
		exit();
		break;

		/************************************************************************/
		/*                            REMOVE HOTEL                              */
		/************************************************************************/
	case 'Remove':

		$sqlRemoveHotel['QUERY']      = "UPDATE " . _DB_MASTER_HOTEL_ . "
							                   SET `status` = 'D'
							                 WHERE `id` IN (" . $_REQUEST['id'] . ")";

		$mycms->sql_update($sqlRemoveHotel);
		$sqlRemovcheckIn['QUERY']      = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
							                   SET `status` = 'D'
							                 WHERE `hotel_id` IN (" . $_REQUEST['id'] . ")";

		$mycms->sql_update($sqlRemovcheckIn);
		$sqlRemovcheckOut['QUERY']      = "UPDATE " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
								                  SET `status` = 'D'
								                 WHERE `hotel_id` IN (" . $_REQUEST['id'] . ")";

		$mycms->sql_update($sqlRemovcheckIn);

		$sqlDeleteACC = array();
		$sqlDeleteACC['QUERY']   = "  DELETE FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
    										 WHERE `hotel_id` = '" . $_REQUEST['id'] . "'";
		$mycms->sql_delete($sqlDeleteACC);


		pageRedirection(1, "hotel_listing.php", 3);
		exit();
		break;

	case 'insert':

		$hotel_name 			= addslashes(trim($_REQUEST['hotel_name_add']));
		$hotel_address 			= addslashes(trim($_REQUEST['hotel_address_add']));
		$hotel_phone 			= addslashes(trim($_REQUEST['hotel_phone_add']));
		// $room_type   			= addslashes(trim($_REQUEST['room_type']));
		$distance_from_venue 	= addslashes(trim($_REQUEST['distance_from_venue_add']));
		$pickup_availability 	= addslashes(trim($_REQUEST['pickup_availability_add']));
		$pickup_complementary 	= addslashes(trim($_REQUEST['pickup_complementary_add']));

		$seat_limit 	= addslashes(trim($_REQUEST['seat_limit']));


		// INSERTING HOTEL DETAILS
		$insertHotel	=	array();
		$insertHotel['QUERY']	= "INSERT INTO " . _DB_MASTER_HOTEL_ . "
											  SET  `hotel_name` 			= ?,
													`hotel_address` 		= ?,
													`hotel_phone_no` 		= ?,
													`distance_from_venue` 	= ?,
													`seat_limit` 	= ?,
													`status` 			    = ?,
													`created_by` 		    = ?,
													`created_ip` 			= ?,
													`created_sessionid`		= ?,
													`created_datetime`		= ?";

		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_name', 				       'DATA' => $hotel_name, 						        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_address', 			       'DATA' => $hotel_address, 						    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_phone_no', 		           'DATA' => $hotel_phone, 			    'TYP' => 's');
		// $insertHotel['PARAM'][]	=	array('FILD' => 'room_type', 		           'DATA' => $room_type, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'distance_from_venue', 	       'DATA' => $distance_from_venue, 			    'TYP' => 's');

		$insertHotel['PARAM'][]	=	array('FILD' => 'seat_limit', 	       'DATA' => $seat_limit, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'status', 		         		   'DATA' => 'A', 'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_by', 			           'DATA' => $loggedUserID, 		            'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_ip', 	                   'DATA' => $_SERVER['REMOTE_ADDR'], 					        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_sessionid', 			   'DATA' => session_id(), 							        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_datetime', 			   'DATA' => date('Y-m-d H:i:s'), 							    'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($insertHotel);

		// $hotel_id 			    = addslashes(trim($_REQUEST['hotel_id']));
		$hotel_id 			    = $lastInsertId;
		$check_in 			    = addslashes(trim($_REQUEST['check_in']));
		$check_out 		    	= addslashes(trim($_REQUEST['check_out']));

		// Set timezone
		date_default_timezone_set('UTC');
		// Start date
		$start_date = $check_in;
		// End date
		$end_date = $check_out;
		date_default_timezone_set('UTC');

		while (strtotime($start_date) <= strtotime($end_date)) {
			$dates[] =  $start_date;
			$start_date = date("Y-m-d", strtotime("+1 days", strtotime($start_date)));
		}

		$count = count($dates);
		for ($i = 0; $i < $count - 1; $i++) {
			$sqlInsertHotel	=	array();
			// INSERTING HOTEL DETAILS
			$sqlInsertHotel['QUERY'] = "INSERT INTO " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
						                                   SET `check_in_date` = ?, 
														       `hotel_id` = ?, 
															   `status` = ? ";
			$sqlInsertHotel['PARAM'][]	=	array('FILD' => 'check_in_date', 	  'DATA' => $dates[$i],       	  'TYP' => 's');
			$sqlInsertHotel['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
			$sqlInsertHotel['PARAM'][]	=	array('FILD' => 'status',  		  'DATA' => 'A',       		  'TYP' => 's');
			$mycms->sql_insert($sqlInsertHotel);
		}
		for ($i = 1; $i < $count; $i++) {
			$sqlInsertout	=	array();
			// INSERTING HOTEL DETAILS
			$sqlInsertout['QUERY'] = "INSERT INTO " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
						                                   SET `check_out_date` = ? , 
														       `hotel_id` = ?, 
															   `status` = ? ";
			$sqlInsertout['PARAM'][]	=	array('FILD' => 'check_out_date', 	  'DATA' => $dates[$i],       	  'TYP' => 's');
			$sqlInsertout['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
			$sqlInsertout['PARAM'][]	=	array('FILD' => 'status',  		  'DATA' => 'A',       		  'TYP' => 's');
			$mycms->sql_insert($sqlInsertout);
		}

		headerImageUpload($lastInsertId, $_FILES['hotel_image']);
		uploadBackgroundImage($lastInsertId, $_FILES['hotel_back_image']);
		uploadAnimationImage($lastInsertId, $_FILES['hotel_animation_image']);


		/*$sqlDelete = array();
  	 		$sqlDelete['QUERY']   = "  DELETE FROM "._DB_ABSTRACT_ALLOTMENT_." 
    										 WHERE `abstract_id` = '".$abstract_id."'";
    		$mycms->sql_query($sqlDelete); */

		if ($_REQUEST['accessories_name']) {

			foreach ($_REQUEST['accessories_name'] as $k => $accessories_name) {


				$accessories_icon = $_FILES['accessories_icon']['name'][$k];
				$userImage 			= str_replace(" ", "", $accessories_icon);
				$userImageTempFile = $_FILES['accessories_icon']['tmp_name'][$k];

				if ($userImageTempFile != "") {
					$ids 							= str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
					$rand							= 'HOTELICON_' . $k . '_' . $ids . '_' . date('ymdHis');
					$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

					$userImageFileName				= $rand . '.' . $ext;

					$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

					if (move_uploaded_file($userImageTempFile, $userImagePath)) {
						$sqlAcc = array();
						$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $lastInsertId . "',
																	  `accessories_name` = '" . $accessories_name . "',
																	  `accessories_icon` = '" . $userImageFileName . "' 
																	";
						$mycms->sql_insert($sqlAcc, false);
					}
				}
			}
		}

		if ($_REQUEST['room_type']) {

			foreach ($_REQUEST['room_type'] as $k => $accessories_name) {


				$accessories_icon = $_FILES['room_type_image']['name'][$k];
				$userImage 			= str_replace(" ", "", $accessories_icon);
				$userImageTempFile = $_FILES['room_type_image']['tmp_name'][$k];

				if ($userImageTempFile != "") {
					$ids 							= str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
					$rand							= 'HOTELROOM_' . $k . '_' . $ids . '_' . date('ymdHis');
					$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

					$userImageFileName				= $rand . '.' . $ext;

					$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

					if (move_uploaded_file($userImageTempFile, $userImagePath)) {
						$sqlAcc = array();
						$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $lastInsertId . "',
																	  `accessories_name` = '" . $accessories_name . "',
																	  `accessories_icon` = '" . $userImageFileName . "',
																	  `purpose` = 'room'  
																	";
						$mycms->sql_insert($sqlAcc, false);
					}
				}
			}
		}



		// pageRedirection(1, "hotel_listing.php", 1);
		$mycms->redirect('hotel_listing.php?show=updateSeatLimit&id=' . $hotel_id);
		exit();
		break;

	case 'update':

		$hotel_id               = addslashes(trim($_REQUEST['id']));
		$hotel_name 			= addslashes(trim($_REQUEST['hotel_name_update']));
		$hotel_address 			= addslashes(trim($_REQUEST['hotel_address_update']));
		$hotel_phone 			= addslashes(trim($_REQUEST['hotel_phone_update']));
		$distance_from_venue 	= addslashes(trim($_REQUEST['distance_from_venue_update']));
		$checkin_checkout_time 	= addslashes(trim($_REQUEST['checkin_checkout_time_update']));
		$pickup_availability 	= addslashes(trim($_REQUEST['pickup_availability_update']));
		$pickup_complementary 	= addslashes(trim($_REQUEST['pickup_complementary_update']));
		$seat_limit 	= addslashes(trim($_REQUEST['seat_limit']));

		// echo '<pre>'; print_r($_FILES['slider_image']); die;

		// UPDATING HOTEL DETAILS

		$sql		  =	array();
		$sql['QUERY'] = "UPDATE" . _DB_MASTER_HOTEL_ . " SET
								`hotel_name`			= ?,
								`hotel_address` 		= ?, 
							    `hotel_phone_no`	    = ?, 
							    `distance_from_venue`   = ?,
							    `checkin_checkout_time`   = ?,
							    `seat_limit`   = ?,
							    `modified_by`			= ?,
								`modified_ip`			= ?,
								`modified_sessionid`	= ?,
								`modified_datetime`		= ?
							  WHERE	`id`                = ?";

		$sql['PARAM'][]	=	array('FILD' => 'hotel_name', 				    'DATA' => $hotel_name, 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'hotel_address', 			    'DATA' => $hotel_address, 					   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'hotel_phone_no', 				'DATA' => $hotel_phone, 					   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'distance_from_venue', 		'DATA' => $distance_from_venue, 			   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'checkin_checkout_time', 		'DATA' => $checkin_checkout_time, 			   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seat_limit', 					'DATA' => $seat_limit, 			   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_by', 				'DATA' => $loggedUserID, 					   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 		   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_sessionid', 		    'DATA' => session_id(), 					   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_datetime', 			'DATA' => date('Y-m-d'), 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $hotel_id, 						   'TYP' => 's');

		$mycms->sql_update($sql);

		if ($_FILES['hotel_image']) {
			headerImageUpload($hotel_id, $_FILES['hotel_image']);
		}
		
		if ($_FILES['hotel_back_image']) {
			uploadBackgroundImage($hotel_id, $_FILES['hotel_back_image']);
		}
		if ($_FILES['hotel_animation_image']) {
			uploadAnimationImage($hotel_id, $_FILES['hotel_animation_image']);
		}

		$sqlDeleteACC = array();
		$sqlDeleteACC['QUERY']   = "  DELETE FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
    										 WHERE `hotel_id` = '" . $hotel_id . "'";
		$mycms->sql_delete($sqlDeleteACC);

		if ($_FILES['slider_image']) {

			foreach ($_FILES['slider_image']['name'] as $k => $accessories_name) {


				$accessories_icon = $_FILES['slider_image']['name'][$k];
				$userImage 			= str_replace(" ", "", $accessories_icon);
				$userImageTempFile = $_FILES['slider_image']['tmp_name'][$k];

				if ($userImageTempFile != "") {
					$ids 							= str_pad($hotel_id, 4, '0', STR_PAD_LEFT);
					$rand							= 'HOTELSLIDER_' . $k . '_' . $ids . '_' . date('ymdHis');
					$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

					$userImageFileName				= $rand . '.' . $ext;

					$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

					if (move_uploaded_file($userImageTempFile, $userImagePath)) {
						$sqlAcc = array();
						$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $hotel_id . "',
																	  `accessories_name` = '',
																	  `accessories_icon` = '" . $userImageFileName . "' ,
																	  `purpose` = 'slider' 
																	";
						$mycms->sql_insert($sqlAcc, false);
					}
				} else {
					$room_exist_icon = $_REQUEST['slider_exist_icon'][$k];
					$sqlAcc = array();
					$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $hotel_id . "',
																	  `accessories_name` = '',
																	  `purpose` = 'slider',
																	  `accessories_icon` = '" . $room_exist_icon . "' 
																	";
					$mycms->sql_insert($sqlAcc, false);
				}
			}
		}

		if ($_REQUEST['accessories_name']) {

			foreach ($_REQUEST['accessories_name'] as $k => $accessories_name) {


				$accessories_icon = $_FILES['accessories_icon']['name'][$k];
				$userImage 			= str_replace(" ", "", $accessories_icon);
				$userImageTempFile = $_FILES['accessories_icon']['tmp_name'][$k];

				if ($userImageTempFile != "") {
					$ids 							= str_pad($hotel_id, 4, '0', STR_PAD_LEFT);
					$rand							= 'HOTELICON_' . $k . '_' . $ids . '_' . date('ymdHis');
					$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

					$userImageFileName				= $rand . '.' . $ext;

					$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

					if (move_uploaded_file($userImageTempFile, $userImagePath)) {
						$sqlAcc = array();
						$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $hotel_id . "',
																	  `accessories_name` = '" . $accessories_name . "',
																	  `accessories_icon` = '" . $userImageFileName . "', 
																	  `purpose` = 'aminity' 
																	";
						$mycms->sql_insert($sqlAcc, false);
					}
				} else {
					$accessories_exist_icon = $_REQUEST['accessories_exist_icon'][$k];
					$sqlAcc = array();
					$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $hotel_id . "',
																	  `accessories_name` = '" . $accessories_name . "',
																	  `purpose` = 'aminity',
																	  `accessories_icon` = '" . $accessories_exist_icon . "' 
																	";
					$mycms->sql_insert($sqlAcc, false);
				}
			}
		}

		if ($_REQUEST['room_type']) {

			foreach ($_REQUEST['room_type'] as $k => $accessories_name) {


				$accessories_icon = $_FILES['room_type_image']['name'][$k];
				$userImage 			= str_replace(" ", "", $accessories_icon);
				$userImageTempFile = $_FILES['room_type_image']['tmp_name'][$k];

				if ($userImageTempFile != "") {
					$ids 							= str_pad($hotel_id, 4, '0', STR_PAD_LEFT);
					$rand							= 'HOTELROOM_' . $k . '_' . $ids . '_' . date('ymdHis');
					$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

					$userImageFileName				= $rand . '.' . $ext;

					$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

					if (move_uploaded_file($userImageTempFile, $userImagePath)) {
						$sqlAcc = array();
						$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $hotel_id . "',
																	  `accessories_name` = '" . $accessories_name . "',
																	  `accessories_icon` = '" . $userImageFileName . "' ,
																	  `purpose` = 'room' 
																	";
						$mycms->sql_insert($sqlAcc, false);
					}
				} else {
					$room_exist_icon = $_REQUEST['room_exist_icon'][$k];
					$sqlAcc = array();
					$sqlAcc['QUERY']           = "INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
																	  SET `hotel_id` = '" . $hotel_id . "',
																	  `accessories_name` = '" . $accessories_name . "',
																	  `purpose` = 'room',
																	  `accessories_icon` = '" . $room_exist_icon . "' 
																	";
					$mycms->sql_insert($sqlAcc, false);
				}
			}
		}

		pageRedirection(1, "hotel_listing.php", 2);
		// $mycms->redirect('hotel_listing.php?show=updateSeatLimit&id=' . $hotel_id);
		exit();
		break;

	case 'updateSeat':

		$seat_limit = $_REQUEST['seat_limit'];
		$id = $_REQUEST['id'];
		
		foreach ($seat_limit as $key => $rowSeat) {
			$sqlUpdateSeat	=	array();
			$sqlUpdateSeat['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
									      SET `seat_limit` = ? 
										WHERE `id` = ? ";

			$sqlUpdateSeat['PARAM'][]	=	array('FILD' => 'seat_limit', 	  'DATA' => $rowSeat,  'TYP' => 's');
			$sqlUpdateSeat['PARAM'][]	=	array('FILD' => 'id',  'DATA' => $id[$key],       'TYP' => 's');
			
			$mycms->sql_update($sqlUpdateSeat);
		}

		pageRedirection(1, "hotel_listing.php", 2);
		exit();
		break;
}

/******************************************************************************/
/*                               PAGE REDIRECTION METHOD                      */
/******************************************************************************/
function pageRedirection($indexVal, $fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey         = "_pgn1_";
	$pageKeyVal      = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString   = "";
	$searchArray     = array();

	$searchArray[$pageKey]                = $pageKeyVal;
	$searchArray['src_hotel_name']        = addslashes(trim($_REQUEST['src_hotel_name']));

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}

function headerImageUpload($holelID, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($holelID, 4, '0', STR_PAD_LEFT);
		$rand							= 'HOTEL_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_MASTER_HOTEL_ . "
														  SET `hotel_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $holelID . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function uploadBackgroundImage($holelID, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($holelID, 4, '0', STR_PAD_LEFT);
		$rand							= 'HOTEL_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_MASTER_HOTEL_ . "
														  SET `hotel_background_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $holelID . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function uploadAnimationImage($holelID, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($holelID, 4, '0', STR_PAD_LEFT);
		$rand							= 'HOTEL_ANIMATION_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_MASTER_HOTEL_ . "
														  SET `hotel_animation_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $holelID . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
