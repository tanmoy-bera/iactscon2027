<?php
include_once('includes/init.php');
session_start(); // make sure sessions are started
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

		echo '<script>window.location.href="system_master.php#accommodation";</script>';

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

	   echo '<script>window.location.href="system_master.php#accommodation";</script>';

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


		echo '<script>window.location.href="system_master.php#accommodation";</script>';

		exit();
		break;
		case 'RemoveAminity':

		$sqlRemoveHotel['QUERY']      = "UPDATE `accommodation_aminity_master`
							                   SET `status` = 'D'
							                 WHERE `id` IN (" . $_REQUEST['id'] . ")";
		$mycms->sql_update($sqlRemoveHotel);
		
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Deleted successfully!' // dynamic message
		];

		// pageRedirection(1, "hotel_listing.php", 1);
		echo '<script>window.location.href="system_master.php#aminity";</script>';

		exit();
		break;
		case 'InactiveAminity':

		$sqlRemoveHotel['QUERY']      = "UPDATE `accommodation_aminity_master`
							                   SET `status` = 'I'
							                 WHERE `id` IN (" . $_REQUEST['id'] . ")";
		$mycms->sql_update($sqlRemoveHotel);
		
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
		];

		// pageRedirection(1, "hotel_listing.php", 1);
		echo '<script>window.location.href="system_master.php#aminity";</script>';

		exit();
		break;
		case 'ActiveAminity':

		$sqlRemoveHotel['QUERY']      = "UPDATE `accommodation_aminity_master`
							                   SET `status` = 'A'
							                 WHERE `id` IN (" . $_REQUEST['id'] . ")";
		$mycms->sql_update($sqlRemoveHotel);
		
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
		];

		// pageRedirection(1, "hotel_listing.php", 1);
		echo '<script>window.location.href="system_master.php#aminity";</script>';

		exit();
		break;
    case 'addAminity':
		$accessories_name = trim($_REQUEST['accessories_name']);

		if ($accessories_name === '') {
			continue;
		}

		$iconName = $_FILES['accessories_icon']['name'] ?? '';
		$iconTmp  = $_FILES['accessories_icon']['tmp_name'] ?? '';
		$finalIconName = '';
	    $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder

		if ($iconTmp !== '') {

			$ids  = str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
			$rand = 'HOTELICON_' . $i . '_' . $ids . '_' . date('ymdHis');
			$ext  = pathinfo($iconName, PATHINFO_EXTENSION);

			$finalIconName = $rand . '.' . $ext;
			// $path = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $finalIconName;
               $path = $uploadDir ."/". $finalIconName;
			move_uploaded_file($iconTmp, $path);
		}
		$sqlAcc = [];
		$sqlAcc['QUERY'] = "
			INSERT INTO `accommodation_aminity_master`
			SET accessories_name = ?,
				accessories_icon = ?,
				purpose = 'aminity'
		";

		$sqlAcc['PARAM'][] = ['DATA' => $accessories_name, 'TYP' => 's'];
		$sqlAcc['PARAM'][] = ['DATA' => $finalIconName,    'TYP' => 's'];
      
		$mycms->sql_insert($sqlAcc);
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
		];

		// pageRedirection(1, "hotel_listing.php", 1);
		echo '<script>window.location.href="system_master.php#aminity";</script>';
    	exit();
	break;
	case 'editAminity':
    $aminity_id = isset($_POST['aminity_id']) ? intval($_POST['aminity_id']) : 0;
    $accessories_name = trim($_POST['accessories_name'] ?? '');

    if ($aminity_id <= 0 || $accessories_name === '') {
        $_SESSION['toaster'] = [
            'type' => 'error',
            'message' => 'Invalid data!'
        ];
        echo '<script>window.location.href="system_master.php#aminity";</script>';
        exit();
    }

    // Get existing record to check current icon
     $sql = [];
            // Fetch data from database
	$sql['QUERY'] = "SELECT id, accessories_name, accessories_icon FROM accommodation_aminity_master WHERE id = '".$aminity_id."' AND purpose = 'aminity'";
	$existing = $mycms->sql_select($sql);

    if (!$existing) {
        $_SESSION['toaster'] = [
            'type' => 'error',
            'message' => 'Record not found!'
        ];
        echo '<script>window.location.href="system_master.php#aminity";</script>';
        exit();
    }

    $existingIcon = $existing[0]['accessories_icon'] ?? '';
    $finalIconName = $existingIcon; // Keep existing icon by default

    // Handle file upload
    if (isset($_FILES['accessories_icon']) && $_FILES['accessories_icon']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['accessories_icon']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        if (in_array($ext, $allowedExts)) {
            // Delete old icon if exists
            if ($existingIcon && file_exists($uploadDir . DIRECTORY_SEPARATOR . $existingIcon)) {
                unlink($uploadDir . DIRECTORY_SEPARATOR . $existingIcon);
            }

            // Upload new icon
            $rand = 'HOTELICON_' . str_pad($aminity_id, 4, '0', STR_PAD_LEFT) . '_' . date('ymdHis') . '_' . rand(100, 999);
            $finalIconName = $rand . '.' . $ext;
            move_uploaded_file($_FILES['accessories_icon']['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $finalIconName);
        } else {
            $_SESSION['toaster'] = [
                'type' => 'error',
                'message' => 'Invalid file type! Allowed: JPG, PNG, GIF, WEBP, SVG'
            ];
            echo '<script>window.location.href="system_master.php#aminity";</script>';
            exit();
        }
    }

    // Update the record
    $updateSql = [];
    $updateSql['QUERY'] = "UPDATE accommodation_aminity_master SET accessories_name = ?, accessories_icon = ? WHERE id = ? AND purpose = 'aminity'";
    $updateSql['PARAM'][] = ['DATA' => $accessories_name, 'TYP' => 's'];
    $updateSql['PARAM'][] = ['DATA' => $finalIconName, 'TYP' => 's'];
    $updateSql['PARAM'][] = ['DATA' => $aminity_id, 'TYP' => 'i'];

    $mycms->sql_update($updateSql);

    $_SESSION['toaster'] = [
        'type' => 'success',
        'message' => 'Aminity updated successfully!'
    ];
    echo '<script>window.location.href="system_master.php#aminity";</script>';
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
		$package_name           =$_REQUEST['package_name'];
		$hotel_notes 			= addslashes(trim($_REQUEST['hotel_notes_add']));
        $hotel_policy 			= addslashes(trim($_REQUEST['hotel_policy']));
		$hotelRatings           = addslashes(trim($_REQUEST['hotelRatings']));
	    $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$seat_limit 	= addslashes(trim($_REQUEST['seat_limit']));
		$maxRooomLimit 	= addslashes(trim($_REQUEST['maxRooomLimit']));
        if($hotelRatings==''){
			$hotelRatings = NULL;
		}
		if($maxRooomLimit==''){
			$maxRooomLimit = NULL;
		}

		// INSERTING HOTEL DETAILS
		$insertHotel	=	array();
		$insertHotel['QUERY']	= "INSERT INTO " . _DB_MASTER_HOTEL_ . "
											  SET  `hotel_name` 			= ?,
													`hotel_address` 		= ?,
													`hotel_phone_no` 		= ?,
													`hotelRatings` 		    = ?,
													`distance_from_venue` 	= ?,
													`seat_limit` 	        = ?,
													`room_type_status` 	   = ?,
													`individualroomLimit`  = ?,
													`hotel_notes`  = ?,
													`hotel_policy`  = ?,
													`status` 			    = ?,
													`created_by` 		    = ?,
													`created_ip` 			= ?,
													`created_sessionid`		= ?,
													`dateFix`		= ?,
													`created_datetime`		= ?";

		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_name', 				       'DATA' => $hotel_name, 						        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_address', 			       'DATA' => $hotel_address, 						    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_phone_no', 		           'DATA' => $hotel_phone, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'hotelRatings', 		           'DATA' => $hotelRatings, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'distance_from_venue', 	           'DATA' => $distance_from_venue, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'seat_limit', 	                   'DATA' => $seat_limit, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'room_type_status', 	            'DATA' => $_REQUEST['room_type_status'], 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'individualroomLimit', 	            'DATA' => $maxRooomLimit, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'hotel_notes', 	            'DATA' => $hotel_notes, 			    'TYP' => 's');
	    $insertHotel['PARAM'][]	=	array('FILD' => 'hotel_policy', 	            'DATA' => $hotel_policy, 			    'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'status', 		         		   'DATA' => 'A', 'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_by', 			           'DATA' => $loggedUserID, 		            'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_ip', 	                   'DATA' => $_SERVER['REMOTE_ADDR'], 					        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_sessionid', 			   'DATA' => session_id(), 							        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'dateFix', 			   'DATA' => $_REQUEST['dateFix'], 							        'TYP' => 's');
		$insertHotel['PARAM'][]	=	array('FILD' => 'created_datetime', 			   'DATA' => date('Y-m-d H:i:s'), 							    'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($insertHotel);

		// $hotel_id 			    = addslashes(trim($_REQUEST['hotel_id']));
		$hotel_id 			    = $lastInsertId;
		$check_in 			    = addslashes(trim($_REQUEST['check_in']));
		$check_out 		    	= addslashes(trim($_REQUEST['check_out']));
		$packageNameArray = array_filter($_REQUEST['package_name']); // removes empty strings
		$packageNameArray = array_unique($packageNameArray);
				////////////////////////////////package insert///////////
		// INSERTING PACKAGE DETAILS
		if ($_REQUEST['package_name']) {

			foreach ($packageNameArray as $p => $packageName) {
				$insertPackage	=	array();
				$insertPackage['QUERY']	= "INSERT INTO "._DB_ACCOMMODATION_PACKAGE_."
												SET  `hotel_id` 				= ?,
														`package_name` 			= ?,
														`status` 				= ?,
														`created_by` 		    = ?,
														`created_ip` 			= ?,
														`created_sessionid`		= ?,
														`created_datetime`		= ?";
														
				$insertPackage['PARAM'][]	=	array('FILD' => 'hotel_id' , 				       'DATA' => $hotel_id , 	            'TYP' => 's');
				$insertPackage['PARAM'][]	=	array('FILD' => 'package_name' , 			       'DATA' => $packageName , 	        'TYP' => 's');
				$insertPackage['PARAM'][]	=	array('FILD' => 'status' , 		        		   'DATA' => 'A' , 	        			'TYP' => 's');
				$insertPackage['PARAM'][]	=	array('FILD' => 'created_by' , 			           'DATA' => $loggedUserID ,        	'TYP' => 's');
				$insertPackage['PARAM'][]	=	array('FILD' => 'created_ip' , 	                   'DATA' => $_SERVER['REMOTE_ADDR'] ,  'TYP' => 's');
				$insertPackage['PARAM'][]	=	array('FILD' => 'created_sessionid' , 			   'DATA' => session_id() ,		        'TYP' => 's');
				$insertPackage['PARAM'][]	=	array('FILD' => 'created_datetime' , 			   'DATA' => date('Y-m-d H:i:s') , 	    'TYP' => 's');
				
				$mycms->sql_insert($insertPackage);
			}
		}
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
															    `seat_limit` = ?, 
															   `status` = ? ";
			$sqlInsertHotel['PARAM'][]	=	array('FILD' => 'check_in_date', 	  'DATA' => $dates[$i],       	  'TYP' => 's');
			$sqlInsertHotel['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
			$sqlInsertHotel['PARAM'][]	=	array('FILD' => 'seat_limit', 	  	  'DATA' => $_REQUEST['seat_limit'][$i],           'TYP' => 's');
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

		if (!empty($_REQUEST['accessories_name'])) {

			$total = count($_REQUEST['accessories_name']);
			$existingIds = isset($_REQUEST['existing_aminity_id']) ? $_REQUEST['existing_aminity_id'] : array();
			$existingIcons = isset($_REQUEST['existing_icon']) ? $_REQUEST['existing_icon'] : array();

			for ($i = 0; $i < $total; $i++) {

				$accessories_name = trim($_REQUEST['accessories_name'][$i]);

				if ($accessories_name === '') {
					continue;
				}

				$isExisting = isset($existingIds[$i]) && $existingIds[$i] > 0;
				$finalIconName = '';

				// ============================================
				// CHECK IF A NEW FILE WAS UPLOADED
				// ============================================
				$iconName = $_FILES['accessories_icon']['name'][$i] ?? '';
				$iconTmp  = $_FILES['accessories_icon']['tmp_name'][$i] ?? '';
				$hasNewFile = ($iconTmp !== '' && $_FILES['accessories_icon']['error'][$i] === 0);

				if ($hasNewFile) {
					// ============================================
					// NEW FILE UPLOADED - Save it
					// ============================================
					$ids  = str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
					$rand = 'HOTELICON_' . $i . '_' . $ids . '_' . date('ymdHis');
					$ext  = pathinfo($iconName, PATHINFO_EXTENSION);
					$finalIconName = $rand . '.' . $ext;
					$path = $uploadDir . "/" . $finalIconName;
					move_uploaded_file($iconTmp, $path);
					
				} else {
					// ============================================
					// NO NEW FILE - Check for existing icon
					// ============================================
					if ($isExisting && isset($existingIcons[$i]) && !empty($existingIcons[$i])) {
						// Copy existing icon to new filename
						$oldPath = $uploadDir . "/" . $existingIcons[$i];
						if (file_exists($oldPath)) {
							$ids  = str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
							$rand = 'HOTELICON_' . $i . '_' . $ids . '_' . date('ymdHis');
							$ext  = pathinfo($existingIcons[$i], PATHINFO_EXTENSION);
							$finalIconName = $rand . '.' . $ext;
							$newPath = $uploadDir . "/" . $finalIconName;
							copy($oldPath, $newPath); // Copy the file
						}
					}
					// If no existing icon, $finalIconName stays empty
				}

				// ============================================
				// INSERT AMINITY RECORD
				// ============================================
				$sqlAcc = [];
				$sqlAcc['QUERY'] = "
					INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
					SET hotel_id = ?,
						accessories_name = ?,
						accessories_icon = ?,
						purpose = 'aminity'
				";

				$sqlAcc['PARAM'][] = ['DATA' => $lastInsertId,      'TYP' => 's'];
				$sqlAcc['PARAM'][] = ['DATA' => $accessories_name, 'TYP' => 's'];
				$sqlAcc['PARAM'][] = ['DATA' => $finalIconName,    'TYP' => 's'];

				$mycms->sql_insert($sqlAcc);
			}
		}
		if (!empty($_REQUEST['room_type']) && $_REQUEST['room_type_status']=='yes') {
 

			$total = count($_REQUEST['room_type']);

			for ($i = 0; $i < $total; $i++) {

				$accessories_name = trim($_REQUEST['room_type'][$i]);
				$room_description = trim($_REQUEST['room_description'][$i]);

				// Skip empty room names
				if ($accessories_name === '') {
					continue;
				}

				$iconName = $_FILES['room_type_image']['name'][$i] ?? '';
				$iconTmp  = $_FILES['room_type_image']['tmp_name'][$i] ?? '';
				$finalIconName = '';

				// If image exists
				if ($iconTmp !== '') {

					$ids  = str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
					$rand = 'HOTELROOM_' . $i . '_' . $ids . '_' . date('ymdHis');
					$ext  = pathinfo($iconName, PATHINFO_EXTENSION);

					$finalIconName = $rand . '.' . $ext;
					// $path = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $finalIconName;
                    $path = $uploadDir ."/". $finalIconName;
					move_uploaded_file($iconTmp, $path);
				}

				// Insert (image or empty image — order preserved)
				$sqlAcc = [];
				$sqlAcc['QUERY'] = "
					INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
					SET hotel_id = ?,
						accessories_name = ?,
						accessories_icon = ?,
						room_description = ?,
						purpose = 'room'
				";

				$sqlAcc['PARAM'][] = ['DATA' => $lastInsertId,      'TYP' => 's'];
				$sqlAcc['PARAM'][] = ['DATA' => $accessories_name, 'TYP' => 's'];
				$sqlAcc['PARAM'][] = ['DATA' => $finalIconName,    'TYP' => 's'];
 				$sqlAcc['PARAM'][] = ['DATA' => $room_description,    'TYP' => 's'];

				$lastInsertRoomId = $mycms->sql_insert($sqlAcc);

				/////////////////////////room details/////////
				
                $room_size   = trim($_REQUEST['room_size'][$i]);
				$bed_size    = trim($_REQUEST['bed_size'][$i]);
				$guest_size  = trim($_REQUEST['guest_size'][$i]);
				$inclusion1  = trim($_REQUEST['inclusion1'][$i]);
				$inclusion2  = trim($_REQUEST['inclusion2'][$i]);
				$inclusion3  = trim($_REQUEST['inclusion3'][$i]);
				$inclusion4  = trim($_REQUEST['inclusion4'][$i]);

				// Build an associative array of field name => value
				$fields = [
					'room_size'  => $room_size,
					'bed_size'   => $bed_size,
					'guest_size' => $guest_size,
					'inclusion1' => $inclusion1,
					'inclusion2' => $inclusion2,
					'inclusion3' => $inclusion3,
					'inclusion4' => $inclusion4,
				];

				foreach ($fields as $fieldName => $fieldValue) {

					// Skip empty values if you don't want blank rows inserted
					if ($fieldValue === '') {
						continue;
					}

					// Determine purpose based on field name
					$purpose = (strpos($fieldName, 'inclusion') === 0) ? 'inclusion' : 'aminity';

					$sqlAccRoom = [];
					$sqlAccRoom['QUERY'] = "
						INSERT INTO `rcg_accommodation_room_accessories`
						SET hotel_id = ?,
							room_id = ?,
							accessories_name = ?,
							description = ?,
							purpose = ?
					";

					$sqlAccRoom['PARAM'][] = ['DATA' => $lastInsertId,     'TYP' => 's'];
					$sqlAccRoom['PARAM'][] = ['DATA' => $lastInsertRoomId, 'TYP' => 's'];
					$sqlAccRoom['PARAM'][] = ['DATA' => $fieldName,        'TYP' => 's'];
					$sqlAccRoom['PARAM'][] = ['DATA' => $fieldValue,       'TYP' => 's'];
					$sqlAccRoom['PARAM'][] = ['DATA' => $purpose,          'TYP' => 's'];

					$lastInsertRoomAcc = $mycms->sql_insert($sqlAccRoom);
                }
				/////////////////////////////////////////////////////
                if($lastInsertRoomId){
					for ($j = 0; $j < $count - 1; $j++) {
				    $seatLimit = $_REQUEST['seat_limit_room'][$i][$j] ?? 0; // ✔ integer per date

						$sqlInsertLimit	=	array();
						// INSERTING HOTEL DETAILS
						$sqlInsertLimit['QUERY'] = "INSERT INTO `rcg_accommodation_seat_limit`
																	SET `check_in_date` = ?, 
																		`hotel_id` = ?, 
																		`room_id` = ?, 
																		`seat_limit` = ?, 
																		`status` = ? ";
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'check_in_date', 	  'DATA' => $dates[$j],       	  'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'room_id', 	  	  'DATA' => $lastInsertRoomId,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'seat_limit', 	  	  'DATA' => $seatLimit,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'status',  		  'DATA' => 'A',       		  'TYP' => 's');
						$mycms->sql_insert($sqlInsertLimit);
					}
				}
			}
		}

        if ($_FILES['slider_image']) {

			foreach ($_FILES['slider_image']['name'] as $k => $accessories_name) {
                if ($accessories_name === '') {
					continue;
				}

				$accessories_icon = $_FILES['slider_image']['name'][$k];
				$userImage 			= str_replace(" ", "", $accessories_icon);
				$userImageTempFile = $_FILES['slider_image']['tmp_name'][$k];

				if ($userImageTempFile != "") {
					$ids 							= str_pad($hotel_id, 4, '0', STR_PAD_LEFT);
					$rand							= 'HOTELSLIDER_' . $k . '_' . $ids . '_' . date('ymdHis');
					$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

					$userImageFileName				= $rand . '.' . $ext;

					// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
                    $userImagePath = $uploadDir ."/". $userImageFileName;
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
				} 
			}
		}
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
		];

		// pageRedirection(1, "hotel_listing.php", 1);
		echo '<script>window.location.href="system_master.php#accommodation";</script>';
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
	    $check_in 			    = addslashes(trim($_REQUEST['check_in']));
		$check_out 		    	= addslashes(trim($_REQUEST['check_out']));
		$uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$maxRooomLimit 	= addslashes(trim($_REQUEST['maxRooomLimit']));
       	$hotel_notes 			= addslashes(trim($_REQUEST['hotel_notes_update']));
        $hotel_policy 			= addslashes(trim($_REQUEST['hotel_policy']));
        $dateFix 			= addslashes(trim($_REQUEST['dateFix']));

		if($maxRooomLimit==''){
			$maxRooomLimit = NULL;
		}

		
         if ($_REQUEST['hotelRatings']) {
					$hotelRatings           = addslashes(trim($_REQUEST['hotelRatings']));

		 }else{
		$hotelRatings           = null;

		 }
		// UPDATING HOTEL DETAILS

		$sql		  =	array();
		$sql['QUERY'] = "UPDATE" . _DB_MASTER_HOTEL_ . " SET
								`hotel_name`			= ?,
								`hotel_address` 		= ?, 
							    `hotel_phone_no`	    = ?, 
							    `distance_from_venue`   = ?,
							    `checkin_checkout_time`   = ?,
							    `seat_limit`   = ?,
								`room_type_status`   = ?,
								`individualroomLimit`   = ?,
								`hotelRatings` 		    = ?,
								`hotel_notes` 		    = ?,
								`hotel_policy` 		    = ?,
								`dateFix`		= ?,
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
	   	$sql['PARAM'][]	=	array('FILD' => 'room_type_status', 					'DATA' => $_REQUEST['room_type_status'], 			   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'individualroomLimit', 	            'DATA' => $maxRooomLimit, 			    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'hotelRatings', 		           'DATA' => $hotelRatings, 			    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'hotel_notes', 		           'DATA' => $hotel_notes, 			    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'hotel_policy', 		           'DATA' => $hotel_policy, 			    'TYP' => 's');
	    $sql['PARAM'][]	=	array('FILD' => 'dateFix', 		           'DATA' => $dateFix, 			    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_by', 				'DATA' => $loggedUserID, 					   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 		   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_sessionid', 		    'DATA' => session_id(), 					   'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_datetime', 			'DATA' => date('Y-m-d'), 				       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $hotel_id, 						   'TYP' => 's');

		$mycms->sql_update($sql);
 
		// if ($_FILES['hotel_image']) {
		// 	headerImageUpload($hotel_id, $_FILES['hotel_image']);
		// }
		
		// if ($_FILES['hotel_back_image']) {
		// 	uploadBackgroundImage($hotel_id, $_FILES['hotel_back_image']);
		// }
		// if ($_FILES['hotel_animation_image']) {
		// 	uploadAnimationImage($hotel_id, $_FILES['hotel_animation_image']);
		// }
		
		if ($_REQUEST['package_name']) {

		    $packageNames = $_POST['package_name'] ?? [];
			$packageIds   = $_POST['package_id'] ?? []; // hidden inputs for existing packages

			// Fetch existing packages from DB
			$sqlExisting = [
				'QUERY' => "SELECT id, package_name 
							FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
							WHERE hotel_id = ? AND status = 'A'",
				'PARAM' => [
					['FILD'=>'hotel_id','DATA'=>$hotel_id,'TYP'=>'s']
				]
			];
			$existingPackages = $mycms->sql_select($sqlExisting);

			$existingMap = [];
			if ($existingPackages) {
				foreach ($existingPackages as $row) {
					$existingMap[$row['id']] = $row['package_name'];
				}
			}

			$submittedIds   = [];
			$toInsert       = [];
			$toUpdate       = [];
			$submittedNames = array_map('trim', $packageNames);

			foreach ($submittedNames as $key => $name) {
				if (empty($name)) continue; // ignore empty input
				$id = $packageIds[$key] ?? null;

				if ($id && isset($existingMap[$id])) {
					// Existing package
					if ($existingMap[$id] != $name) {
						// Name changed → update
						$toUpdate[$id] = $name;
					}
					$submittedIds[] = $id; // mark as "keep"
				} else {
					// New package → insert
					$toInsert[] = $name;
				}
			}

			// 1️⃣ Delete removed packages (soft delete)
			$existingIds = array_keys($existingMap);
			$toDelete = array_diff($existingIds, $submittedIds);

			if (!empty($toDelete)) {
				$deleteQuery = [];
				$deleteQuery['QUERY'] = "
					UPDATE " . _DB_ACCOMMODATION_PACKAGE_ . "
					SET status = 'D'
					WHERE id IN (".implode(',', $toDelete).")
				";
				$mycms->sql_update($deleteQuery);
			}

			// 2️⃣ Update changed packages
			foreach ($toUpdate as $id => $newName) {
				$updateQuery = [];
				$updateQuery['QUERY'] = "
					UPDATE " . _DB_ACCOMMODATION_PACKAGE_ . "
					SET package_name = ?
					WHERE id = ?
				";
				$updateQuery['PARAM'][] = ['FILD'=>'package_name','DATA'=>$newName,'TYP'=>'s'];
				$updateQuery['PARAM'][] = ['FILD'=>'id','DATA'=>$id,'TYP'=>'s'];

				$mycms->sql_update($updateQuery);
			}

			// 3️⃣ Insert new packages
			foreach ($toInsert as $packageName) {
				$insertPackage = [];
				$insertPackage['QUERY'] = "
					INSERT INTO "._DB_ACCOMMODATION_PACKAGE_."
					SET hotel_id          = ?,
						package_name      = ?,
						status            = 'A',
						created_by        = ?,
						created_ip        = ?,
						created_sessionid = ?,
						created_datetime  = ?
				";
				$insertPackage['PARAM'][] = ['FILD'=>'hotel_id','DATA'=>$hotel_id,'TYP'=>'s'];
				$insertPackage['PARAM'][] = ['FILD'=>'package_name','DATA'=>$packageName,'TYP'=>'s'];
				$insertPackage['PARAM'][] = ['FILD'=>'created_by','DATA'=>$loggedUserID,'TYP'=>'s'];
				$insertPackage['PARAM'][] = ['FILD'=>'created_ip','DATA'=>$_SERVER['REMOTE_ADDR'],'TYP'=>'s'];
				$insertPackage['PARAM'][] = ['FILD'=>'created_sessionid','DATA'=>session_id(),'TYP'=>'s'];
				$insertPackage['PARAM'][] = ['FILD'=>'created_datetime','DATA'=>date('Y-m-d H:i:s'),'TYP'=>'s'];

				$mycms->sql_insert($insertPackage);
			}


		}
           //////////////////////seat limit///////////

		// $sqlUpdateHotel	=	array();
		// $sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
		// 							      SET `status` = ? 
		// 								WHERE `hotel_id` = ? ";

		// $sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		// $sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		// $mycms->sql_update($sqlUpdateHotel);

		// $sqlUpdateHotel	=	array();
		// $sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
		// 							      SET `status` = ? 
		// 								WHERE `hotel_id` = ? ";

		// $sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		// $sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		// $mycms->sql_update($sqlUpdateHotel);

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE `rcg_accommodation_seat_limit`
									      SET `status` = ?  
										WHERE `hotel_id` = ? ";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);
		// Set timezone
		
	    date_default_timezone_set('UTC');
		// Start date
		$start_date = $check_in;
		// End date
		$end_date = $check_out;
		date_default_timezone_set('UTC');

		while (strtotime($start_date) <= strtotime($end_date)) {
			$dates[] = $start_date;
			$start_date = date("Y-m-d", strtotime("+1 days", strtotime($start_date)));
		}
		$count = count($dates);

		// Get existing check-in dates for this hotel
		$sqlGetExistingCheckin = array();
		$sqlGetExistingCheckin['QUERY'] = "SELECT id, check_in_date, seat_limit FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
										WHERE `hotel_id` = ? AND `status` = 'A'";
		$sqlGetExistingCheckin['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
		$existingCheckins = $mycms->sql_select($sqlGetExistingCheckin);

		// Create array of existing check-in dates
		$existingCheckinDates = array();
		foreach ($existingCheckins as $existing) {
			$existingCheckinDates[$existing['check_in_date']] = $existing['id'];
		}

		// Get existing check-out dates for this hotel
		$sqlGetExistingCheckout = array();
		$sqlGetExistingCheckout['QUERY'] = "SELECT id, check_out_date FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
										WHERE `hotel_id` = ? AND `status` = 'A'";
		$sqlGetExistingCheckout['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
		$existingCheckouts = $mycms->sql_select($sqlGetExistingCheckout);

		// Create array of existing check-out dates
		$existingCheckoutDates = array();
		foreach ($existingCheckouts as $existing) {
			$existingCheckoutDates[$existing['check_out_date']] = $existing['id'];
		}

		// Process check-in dates
		$newCheckinDates = array();
		for ($i = 0; $i < $count - 1; $i++) {
			$checkinDate = $dates[$i];
			$seatLimit = $_REQUEST['seat_limit'][$i] ?? 0;
			$newCheckinDates[] = $checkinDate;
			
			if (isset($existingCheckinDates[$checkinDate])) {
				// Update existing check-in date
				$sqlUpdateCheckin = array();
				$sqlUpdateCheckin['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
											SET `seat_limit` = ?
											WHERE `hotel_id` = ? AND `check_in_date` = ? AND `status` = 'A'";
				$sqlUpdateCheckin['PARAM'][] = array('FILD' => 'seat_limit', 'DATA' => $seatLimit, 'TYP' => 's');
				$sqlUpdateCheckin['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
				$sqlUpdateCheckin['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $checkinDate, 'TYP' => 's');
				$mycms->sql_update($sqlUpdateCheckin);
			} else {
				// Insert new check-in date
				$sqlInsertCheckin = array();
				$sqlInsertCheckin['QUERY'] = "INSERT INTO " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
											SET `check_in_date` = ?, `hotel_id` = ?, `seat_limit` = ?, `status` = 'A'";
				$sqlInsertCheckin['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $checkinDate, 'TYP' => 's');
				$sqlInsertCheckin['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
				$sqlInsertCheckin['PARAM'][] = array('FILD' => 'seat_limit', 'DATA' => $seatLimit, 'TYP' => 's');
				$mycms->sql_insert($sqlInsertCheckin);

				$sqlUpdateTariffAmount = array();
				$sqlUpdateTariffAmount['QUERY'] = "UPDATE " . _DB_TARIFF_ACCOMMODATION_ . "
												SET `status` = ?, `type` = ?, `modified_ip` = ?, `modified_dateTime` = ?
												WHERE  `hotel_id` = ?";
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'D', 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'type', 'DATA' => 'old', 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's'];


				$mycms->sql_update($sqlUpdateTariffAmount);
				$sqlUpdatePackageAmount = array();
				$sqlUpdatePackageAmount['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . " 
												SET `status` = ? 
												WHERE `hotel_id` = ?";
				$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'D', 'TYP' => 's'];
				$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's'];
				$mycms->sql_update($sqlUpdatePackageAmount);
			}
		}

		// Soft delete check-in dates that are no longer in the new range
		$checkinDatesToDelete = array_diff(array_keys($existingCheckinDates), $newCheckinDates);
		if (!empty($checkinDatesToDelete)) {
			$placeholders = implode(',', array_fill(0, count($checkinDatesToDelete), '?'));
			$sqlDeleteCheckin = array();
			$sqlDeleteCheckin['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
										SET `status` = 'D'
										WHERE `hotel_id` = ? AND `check_in_date` IN ($placeholders)";
			$sqlDeleteCheckin['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
			foreach ($checkinDatesToDelete as $date) {
				$sqlDeleteCheckin['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $date, 'TYP' => 's');
			}
			$mycms->sql_update($sqlDeleteCheckin);
		}

		// Process check-out dates
		$newCheckoutDates = array();
		for ($i = 1; $i < $count; $i++) {
			$checkoutDate = $dates[$i];
			$newCheckoutDates[] = $checkoutDate;
			
			if (isset($existingCheckoutDates[$checkoutDate])) {
				// Update existing check-out date (just update timestamp, no other fields)
				
			} else {
				// Insert new check-out date
				$sqlInsertCheckout = array();
				$sqlInsertCheckout['QUERY'] = "INSERT INTO " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
											SET `check_out_date` = ?, `hotel_id` = ?, `status` = 'A'";
				$sqlInsertCheckout['PARAM'][] = array('FILD' => 'check_out_date', 'DATA' => $checkoutDate, 'TYP' => 's');
				$sqlInsertCheckout['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
				$mycms->sql_insert($sqlInsertCheckout);

				$sqlUpdateTariffAmount = array();
				$sqlUpdateTariffAmount['QUERY'] = "UPDATE " . _DB_TARIFF_ACCOMMODATION_ . "
												SET `status` = ?, `type` = ?, `modified_ip` = ?, `modified_dateTime` = ?
												WHERE  `hotel_id` = ?";
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'D', 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'type', 'DATA' => 'old', 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];
				$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's'];


				$mycms->sql_update($sqlUpdateTariffAmount);
				$sqlUpdatePackageAmount = array();
				$sqlUpdatePackageAmount['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . " 
												SET `status` = ? 
												WHERE `hotel_id` = ?";
				$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'D', 'TYP' => 's'];
				$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's'];
				$mycms->sql_update($sqlUpdatePackageAmount);
			}
		}

		// Soft delete check-out dates that are no longer in the new range
		$checkoutDatesToDelete = array_diff(array_keys($existingCheckoutDates), $newCheckoutDates);
		if (!empty($checkoutDatesToDelete)) {
			$placeholders = implode(',', array_fill(0, count($checkoutDatesToDelete), '?'));
			$sqlDeleteCheckout = array();
			$sqlDeleteCheckout['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
										SET `status` = 'D'
										WHERE `hotel_id` = ? AND `check_out_date` IN ($placeholders)";
			$sqlDeleteCheckout['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotel_id, 'TYP' => 's');
			foreach ($checkoutDatesToDelete as $date) {
				$sqlDeleteCheckout['PARAM'][] = array('FILD' => 'check_out_date', 'DATA' => $date, 'TYP' => 's');
			}
			$mycms->sql_update($sqlDeleteCheckout);
		}

		   ///////////////////////////////////////////
	
		if (!empty($_REQUEST['slider_id']) || !empty($_FILES['slider_image'])) {

			/* -------------------------------------------------
			* 1. FETCH EXISTING ACTIVE SLIDER IDS FROM DB
			* ------------------------------------------------- */
			$sqlOld = [];
			$sqlOld['QUERY'] = "
				SELECT id 
				FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "
				WHERE hotel_id = ? 
				AND purpose = 'slider' 
				AND status = 'A'
			";
			$sqlOld['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];

			$oldRows = $mycms->sql_select($sqlOld, false);
			$oldIds  = array_column($oldRows ?? [], 'id');


			/* -------------------------------------------------
			* 2. IDS SENT FROM FORM (FILTER EMPTY)
			* ------------------------------------------------- */
			$slider_ids      = $_REQUEST['slider_id'] ?? [];
			$existing_icons  = $_REQUEST['slider_exist_icon'] ?? [];

			$postedIds = array_filter($slider_ids); // remove empty values


			/* -------------------------------------------------
			* 3. DETECT DELETED IDS (BACKEND ONLY)
			* ------------------------------------------------- */
			$deletedIds = array_diff($oldIds, $postedIds);

			if (!empty($deletedIds)) {

				$placeholders = implode(',', array_fill(0, count($deletedIds), '?'));

				$sqlDel = [];
				$sqlDel['QUERY'] = "
					UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
					SET status = 'D'
					WHERE id IN ($placeholders)
					AND hotel_id = ?
					AND purpose = 'slider'
				";

				foreach ($deletedIds as $did) {
					$sqlDel['PARAM'][] = ['DATA' => $did, 'TYP' => 'i'];
				}
				$sqlDel['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];

				$mycms->sql_update($sqlDel);
			}


			/* -------------------------------------------------
			* 4. INSERT / UPDATE LOGIC (YOUR ORIGINAL CODE)
			* ------------------------------------------------- */
			foreach ($slider_ids as $k => $id) {

				$existing_icon = $existing_icons[$k] ?? '';
				$fileName = $existing_icon; // default: keep existing

				// NEW FILE UPLOAD
				if (
					isset($_FILES['slider_image']['tmp_name'][$k]) &&
					is_uploaded_file($_FILES['slider_image']['tmp_name'][$k]) &&
					!empty($_FILES['slider_image']['name'][$k])
				) {
					$ext = pathinfo($_FILES['slider_image']['name'][$k], PATHINFO_EXTENSION);
					$fileName = 'HOTELSLIDER_' . uniqid() . '_' . str_pad($hotel_id, 4, '0', STR_PAD_LEFT) . '.' . $ext;


					
					move_uploaded_file(
						$_FILES['slider_image']['tmp_name'][$k],
						$uploadDir. '/' . $fileName
					);
				}

				// UPDATE EXISTING
				if (!empty($id) && !empty($fileName)) {

					$sql = [];
					$sql['QUERY'] = "
						UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
						SET accessories_icon = ?, status = 'A'
						WHERE id = ? AND hotel_id = ? AND purpose = 'slider'
					";
					$sql['PARAM'][] = ['DATA' => $fileName, 'TYP' => 's'];
					$sql['PARAM'][] = ['DATA' => $id, 'TYP' => 'i'];
					$sql['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];

					$mycms->sql_update($sql);

				}
				// INSERT NEW
				elseif (empty($id) && !empty($fileName)) {

					$sql = [];
					$sql['QUERY'] = "
						INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
						SET hotel_id = ?, accessories_icon = ?, purpose='slider', status='A'
					";
					$sql['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];
					$sql['PARAM'][] = ['DATA' => $fileName, 'TYP' => 's'];

					$mycms->sql_insert($sql, true);
				}
			}
		}
		if (!empty($_REQUEST['accessories_name'])) {
			$submitted_ids = [];

			foreach ($_REQUEST['accessories_name'] as $k => $accessories_name) {
				$accessories_name = trim($accessories_name);
				if ($accessories_name === '') continue;

				$accessories_id = $_REQUEST['accessories_id'][$k] ?? null;
				$existing_icon  = $_REQUEST['accessories_exist_icon'][$k] ?? '';

				// By default, keep the existing icon
				$fileName = $existing_icon;

				// Only overwrite if a new file is uploaded
				if (
					isset($_FILES['accessories_icon']['error'][$k]) &&
					$_FILES['accessories_icon']['error'][$k] === UPLOAD_ERR_OK
				) {
					$ext = pathinfo($_FILES['accessories_icon']['name'][$k], PATHINFO_EXTENSION);
					$fileName = 'HOTELICON_' . uniqid() . '_' . str_pad($hotel_id, 4, '0', STR_PAD_LEFT) . '.' . $ext;

					move_uploaded_file(
						$_FILES['accessories_icon']['tmp_name'][$k],
						$uploadDir. '/' . $fileName
					);
				}

				if ($accessories_id) {
					// UPDATE
					$sqlUpdate = [];
					$sqlUpdate['QUERY'] = "
						UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
						SET accessories_name = ?,
							accessories_icon = ?,
							status = 'A'
						WHERE id = ? AND hotel_id = ?
					";

					$sqlUpdate['PARAM'][] = ['DATA' => $accessories_name, 'TYP' => 's'];
					$sqlUpdate['PARAM'][] = ['DATA' => $fileName, 'TYP' => 's'];
					$sqlUpdate['PARAM'][] = ['DATA' => $accessories_id, 'TYP' => 'i'];
					$sqlUpdate['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];

					$mycms->sql_update($sqlUpdate);
					$submitted_ids[] = (int)$accessories_id;

				} else {
					// INSERT
					$sqlInsert = [];
					$sqlInsert['QUERY'] = "
						INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
						SET hotel_id = ?,
							accessories_name = ?,
							accessories_icon = ?,
							purpose = 'aminity',
							status = 'A'
					";
					$sqlInsert['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];
					$sqlInsert['PARAM'][] = ['DATA' => $accessories_name, 'TYP' => 's'];
					$sqlInsert['PARAM'][] = ['DATA' => $fileName, 'TYP' => 's'];

					$new_id = $mycms->sql_insert($sqlInsert, true);
					if ($new_id) $submitted_ids[] = (int)$new_id;
				}
			}
           
			// Soft delete removed amenities
			if (!empty($submitted_ids)) {
				$ids_str = implode(',', $submitted_ids);
				$sqlDelete = [];
				$sqlDelete['QUERY'] = "
					UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
					SET status = 'D'
					WHERE hotel_id = ?
					AND purpose = ?
					AND id NOT IN ($ids_str)
				";
				$sqlDelete['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];
				$sqlDelete['PARAM'][] = ['DATA' => 'aminity', 'TYP' => 's'];

				$mycms->sql_update($sqlDelete);
			}
		}
		if (!empty($_REQUEST['room_type']) || $_REQUEST['room_type_status']=='yes') {
			$submittedRomm_ids = [];
 
			foreach ($_REQUEST['room_type'] as $k2 => $room_name) {
			
				$room_name = trim($room_name);
				if ($room_name === '') continue;

				$room_id = $_REQUEST['room_type_id'][$k2] ?? null;
				$existing_icon  = $_REQUEST['room_exist_icon'][$k2] ?? '';
				$room_description = trim($_REQUEST['room_description'][$k2]);

				// By default, keep the existing icon
				$fileName = $existing_icon;
 
				// Only overwrite if a new file is uploaded
				if (
					isset($_FILES['room_type_image']['error'][$k2]) &&
					$_FILES['room_type_image']['error'][$k2] === UPLOAD_ERR_OK
				) {
					$ext = pathinfo($_FILES['room_type_image']['name'][$k2], PATHINFO_EXTENSION);
					$fileName = 'HOTELICON_' . uniqid() . '_' . str_pad($hotel_id, 4, '0', STR_PAD_LEFT) . '.' . $ext;

					move_uploaded_file(
						$_FILES['room_type_image']['tmp_name'][$k2],
						$uploadDir. '/' . $fileName
					);
				}
  
				if ($room_id) {
					// UPDATE
					$sqlUpdate = [];
					$sqlUpdate['QUERY'] = "
						UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
						SET accessories_name = ?,
							accessories_icon = ?,
							room_description = ?,
							status = 'A'
						WHERE id = ? AND hotel_id = ?
					";

					$sqlUpdate['PARAM'][] = ['DATA' => $room_name, 'TYP' => 's'];
					$sqlUpdate['PARAM'][] = ['DATA' => $fileName, 'TYP' => 's'];
				    $sqlUpdate['PARAM'][] = ['DATA' => $room_description, 'TYP' => 's'];
					$sqlUpdate['PARAM'][] = ['DATA' => $room_id, 'TYP' => 'i'];
					$sqlUpdate['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];

					$mycms->sql_update($sqlUpdate);

					/////////////////////////room details (UPDATE existing room)/////////

					// Clear old room-level accessory rows for this room before re-inserting
					$sqlDeleteRoomAcc = [];
					$sqlDeleteRoomAcc['QUERY'] = "DELETE FROM `rcg_accommodation_room_accessories` WHERE room_id = ?";
					$sqlDeleteRoomAcc['PARAM'][] = ['DATA' => $room_id, 'TYP' => 's'];
					$mycms->sql_delete($sqlDeleteRoomAcc);

					$room_size   = trim($_REQUEST['room_size'][$k2] ?? '');
					$bed_size    = trim($_REQUEST['bed_size'][$k2] ?? '');
					$guest_size  = trim($_REQUEST['guest_size'][$k2] ?? '');
					$inclusion1  = trim($_REQUEST['inclusion1'][$k2] ?? '');
					$inclusion2  = trim($_REQUEST['inclusion2'][$k2] ?? '');
					$inclusion3  = trim($_REQUEST['inclusion3'][$k2] ?? '');
					$inclusion4  = trim($_REQUEST['inclusion4'][$k2] ?? '');

					$fields = [
						'room_size'  => $room_size,
						'bed_size'   => $bed_size,
						'guest_size' => $guest_size,
						'inclusion1' => $inclusion1,
						'inclusion2' => $inclusion2,
						'inclusion3' => $inclusion3,
						'inclusion4' => $inclusion4,
					];

					foreach ($fields as $fieldName => $fieldValue) {

						if ($fieldValue === '') {
							continue;
						}

						$purpose = (strpos($fieldName, 'inclusion') === 0) ? 'inclusion' : 'aminity';

						$sqlAccRoom = [];
						$sqlAccRoom['QUERY'] = "
							INSERT INTO `rcg_accommodation_room_accessories`
							SET hotel_id = ?,
								room_id = ?,
								accessories_name = ?,
								description = ?,
								purpose = ?
						";

						$sqlAccRoom['PARAM'][] = ['DATA' => $hotel_id,  'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $room_id,   'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $fieldName, 'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $fieldValue,'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $purpose,   'TYP' => 's'];
 
						$lastInsertRoomAcc = $mycms->sql_insert($sqlAccRoom);
					}
					/////////////////////////////////////////////////////

					$submittedRomm_ids[] = (int)$room_id;
                    for ($j = 0; $j < $count - 1; $j++) {
						$seatLimit = $_REQUEST['seat_limit_room'][$k2][$j] ?? 0; // ✔ integer per date

						$sqlInsertLimit	=	array();
						// INSERTING HOTEL DETAILS
						$sqlInsertLimit['QUERY'] = "INSERT INTO `rcg_accommodation_seat_limit`
																	SET `check_in_date` = ?, 
																		`hotel_id` = ?, 
																		`room_id` = ?, 
																		`seat_limit` = ?, 
																		`status` = ? ";
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'check_in_date', 	  'DATA' => $dates[$j],       	  'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'room_id', 	  	  'DATA' => $room_id,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'seat_limit', 	  	  'DATA' => $seatLimit,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'status',  		  'DATA' => 'A',       		  'TYP' => 's');
						
						$mycms->sql_insert($sqlInsertLimit);
					
					}
				} else {
					// INSERT
					$sqlInsert = [];
					$sqlInsert['QUERY'] = "
						INSERT INTO " . _DB_ACCOMMODATION_ACCESSORIES_ . "
						SET hotel_id = ?,
							accessories_name = ?,
							accessories_icon = ?,
							room_description = ?,
							purpose = 'room',
							status = 'A'
					";
					$sqlInsert['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];
					$sqlInsert['PARAM'][] = ['DATA' => $room_name, 'TYP' => 's'];
					$sqlInsert['PARAM'][] = ['DATA' => $fileName, 'TYP' => 's'];
                 	$sqlInsert['PARAM'][] = ['DATA' => $room_description, 'TYP' => 's'];

					$new_id = $mycms->sql_insert($sqlInsert, true);

					/////////////////////////room details (INSERT new room)/////////

					$room_size   = trim($_REQUEST['room_size'][$k2] ?? '');
					$bed_size    = trim($_REQUEST['bed_size'][$k2] ?? '');
					$guest_size  = trim($_REQUEST['guest_size'][$k2] ?? '');
					$inclusion1  = trim($_REQUEST['inclusion1'][$k2] ?? '');
					$inclusion2  = trim($_REQUEST['inclusion2'][$k2] ?? '');
					$inclusion3  = trim($_REQUEST['inclusion3'][$k2] ?? '');
					$inclusion4  = trim($_REQUEST['inclusion4'][$k2] ?? '');

					$fields = [
						'room_size'  => $room_size,
						'bed_size'   => $bed_size,
						'guest_size' => $guest_size,
						'inclusion1' => $inclusion1,
						'inclusion2' => $inclusion2,
						'inclusion3' => $inclusion3,
						'inclusion4' => $inclusion4,
					];

					foreach ($fields as $fieldName => $fieldValue) {

						if ($fieldValue === '') {
							continue;
						}

						$purpose = (strpos($fieldName, 'inclusion') === 0) ? 'inclusion' : 'aminity';

						$sqlAccRoom = [];
						$sqlAccRoom['QUERY'] = "
							INSERT INTO `rcg_accommodation_room_accessories`
							SET hotel_id = ?,
								room_id = ?,
								accessories_name = ?,
								description = ?,
								purpose = ?
						";

						$sqlAccRoom['PARAM'][] = ['DATA' => $hotel_id,  'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $new_id,    'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $fieldName, 'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $fieldValue,'TYP' => 's'];
						$sqlAccRoom['PARAM'][] = ['DATA' => $purpose,   'TYP' => 's'];

						$lastInsertRoomAcc = $mycms->sql_insert($sqlAccRoom);
					}
					/////////////////////////////////////////////////////

					if ($new_id) $submittedRomm_ids[] = (int)$new_id;
					if($new_id){

					for ($j = 0; $j < $count - 1; $j++) {
						$seatLimit = $_REQUEST['seat_limit_room'][$k2][$j] ?? 0; // ✔ integer per date

						$sqlInsertLimit	=	array();
						// INSERTING HOTEL DETAILS
						$sqlInsertLimit['QUERY'] = "INSERT INTO `rcg_accommodation_seat_limit`
																	SET `check_in_date` = ?, 
																		`hotel_id` = ?, 
																		`room_id` = ?, 
																		`seat_limit` = ?, 
																		`status` = ? ";
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'check_in_date', 	  'DATA' => $dates[$j],       	  'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'room_id', 	  	  'DATA' => $new_id,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'seat_limit', 	  	  'DATA' => $seatLimit,           'TYP' => 's');
						$sqlInsertLimit['PARAM'][]	=	array('FILD' => 'status',  		  'DATA' => 'A',       		  'TYP' => 's');
						
						// echo "<pre>";
						// print_r($sqlInsertLimit);
						// die();
						$mycms->sql_insert($sqlInsertLimit);
					}
				}
				}
			}
 
			// Soft delete removed amenities
			if (!empty($submittedRomm_ids)) {
				$ids_str = implode(',', $submittedRomm_ids);
				$sqlDelete = [];
				$sqlDelete['QUERY'] = "
					UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
					SET status = 'D'
					WHERE hotel_id = ?
					AND `purpose` = ?
					AND id NOT IN ($ids_str)
				";
				$sqlDelete['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];
				$sqlDelete['PARAM'][] = ['DATA' => 'room', 'TYP' => 's'];

				$mycms->sql_update($sqlDelete);
			}else {
				$sqlDelete = [];
				// No rooms survived this submission -> mark ALL existing rooms for this hotel as deleted
					$sqlDelete['QUERY'] = "
						UPDATE " . _DB_ACCOMMODATION_ACCESSORIES_ . "
						SET status = 'D'
						WHERE hotel_id = ?
						AND `purpose` = ?
					";
				$sqlDelete['PARAM'][] = ['DATA' => $hotel_id, 'TYP' => 's'];
				$sqlDelete['PARAM'][] = ['DATA' => 'room', 'TYP' => 's'];

				$mycms->sql_update($sqlDelete);
			}

		}

		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
		];


		echo '<script>window.location.href="system_master.php#accommodation";</script>';

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
     
		// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
        $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	

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
