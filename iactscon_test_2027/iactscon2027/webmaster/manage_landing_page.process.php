<?php
include_once('includes/init.php');

$act = @$_REQUEST['act'];
switch ($act) {

	/*================ COUNTRY AVAILABILITY ====================*/
	case 'countryAvailability':

		$getVal = addslashes($_REQUEST['getVal']);
		$sql 	=	array();
		$sql['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
									    WHERE `country_name`=? 
									      AND `status`!= ?";
		$sql['PARAM'][]	=	array('FILD' => 'country_name',    	 'DATA' => $getVal,            'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'status',    			'DATA' => 'D',         		'TYP' => 's');
		$res    		 = $mycms->sql_select($sql);
		$maxrow 		 = $mycms->sql_numrows($res);

		if ($maxrow > 0) {
			echo 1;
		} else {
			echo 0;
		}
		exit();
		break;

	//============================= UPDATE COUNTDOWN DETAILS ==================================
	case 'update':

		$countdown_id               	= addslashes(trim($_REQUEST['id']));
		$countdown_date 		    	= addslashes(trim($_REQUEST['countdown_date']));

		$countdown_idAbs               	= addslashes(trim($_REQUEST['absId']));
		$countdown_dateAbs 		    	= addslashes(trim($_REQUEST['countdown_date_abs']));

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_LANDING_PAGE_SETTING_ . " 
                                        SET 
                                            `countdownDate` 		= ?, 
                                            `modified_by`			= ?,
                                            `modified_ip`			= ?,
                                            `modified_sessionId`	= ?,
                                            `modified_dateTime`		= ?
                                      WHERE `id` 					= ?";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'countdownDate',  				'DATA' => $countdown_date,      		'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_by', 				'DATA' => $loggedUserID, 			'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_sessionId', 		    'DATA' => session_id(), 			'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_dateTime', 			'DATA' => date('Y-m-d'), 			'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $countdown_id, 			'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		$sqlUpdateAbs	=	array();
		$sqlUpdateAbs['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
                                        SET 
                                            `abstract_countdown_date` 		= ?";

		$sqlUpdateAbs['PARAM'][]	=	array('FILD' => 'abstract_countdown_date', 'DATA' => $countdown_dateAbs,      		'TYP' => 's');
		$mycms->sql_update($sqlUpdateAbs);


		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
		];

		// pageRedirection(1, "hotel_listing.php", 1);
		echo '<script>window.location.href="information_setup.php#countdownsetup";</script>';

		exit();
		break;

	/*================= EDIT BACKGROUND IMAGE ===========================*/
	case 'edit_outer_background':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];

		if (!empty($_REQUEST['outerBackgroundImage'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
										SET `modified_dateTime`=? 
                                        WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['outerBackgroundImage'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
			                                        WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			headerImageUpload($id, $_FILES['outerBackgroundImage'], 'LANDING_OUTER_BACKGROUND_');
		}

		pageRedirection('manage_landing_page.php', 2);
		exit();
		break;



	/*================= EDIT BACKGROUND IMAGE ===========================*/
	case 'edit_background':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];

		if (!empty($_REQUEST['backgroundImage'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
										SET `modified_dateTime`=? 
                                        WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['backgroundImage'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
			                                        WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			headerImageUpload($id, $_FILES['backgroundImage'], 'LANDING_BACKGROUND_');
		}

		pageRedirection('manage_landing_page.php', 2);
		exit();
		break;

	/*================= ACTIVE FLYER ==================*/
	case 'Active':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'A',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("manage_landing_page.php", 2);
		exit();
		break;

	/*================ INACTIVE FLYER =================*/
	case 'Inactive':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'I',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("manage_landing_page.php", 2);
		exit();
		break;

	/*================= REMOVE FLYER ==================*/
	case 'Remove':
	
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'D',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$result = $mycms->sql_update($sql);

		if (!empty($_REQUEST['id'])) {

			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_ICON_SETTING_ . "
			                                         
			                                        WHERE `id` = '" . $_REQUEST['id'] . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);

			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];
			unlink($imgpath);
		}


	    if ($result) {
            echo 'success';
            exit; // important: stop outputting any HTML
        } else {
            error_log("Delete failed");
            echo 'error';
            exit;
        }
		break;

	/*================= ADD FLYER ============================*/
	case 'add_Flyer':

		global $mycms, $cfg;


		$sql 	=	array();

		$sql['QUERY'] = "INSERT INTO " . _DB_LANDING_FLYER_IMAGE_ . " 
							 SET `created_dateTime`=?";
		$sql['PARAM'][]		  =	array('FILD' => 'created_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($sql);

		headerImageUpload($lastInsertId, $_FILES['flyerImage'], 'FLYER_');

		pageRedirection('manage_landing_page.php', 2);

		exit();
		break;

	/*================= EDIT FLYER FORM ===========================*/
	case 'edit_flyer':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];

		if (!empty($_REQUEST['flyerImage'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
										SET `modified_dateTime`=? 
                                        WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['flyerImage'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
			                                        WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			headerImageUpload($id, $_FILES['flyerImage'], 'FLYER_');
		}

		pageRedirection('manage_landing_page.php', 2);
		exit();
		break;

	/*================= ADD SIDE ICON  ============================*/
	case 'add_side_icon':

		global $mycms, $cfg;


		$sql 	=	array();

		$sql['QUERY'] = "INSERT INTO " . _DB_ICON_SETTING_ . " 
									SET `title`=?,
									`purpose`='Side Icon',
									`page_link`=?,
									`created_dateTime`=?";


		$sql['PARAM'][]		  =	array('FILD' => 'title',    	 'DATA' => $_REQUEST['iconTitle'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'page_link',    	 'DATA' => $_REQUEST['page_link'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'created_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($sql);

		sideIconUpload($lastInsertId, $_FILES['iconImage'], 'SIDE_ICON_');

		pageRedirection('manage_landing_page.php', 2);

		exit();
		break;

	/*================= EDIT SIDE ICON ===========================*/
	// case 'edit_side_icon':

	// 	global $mycms, $cfg;
	// 	$id = $_REQUEST['id'];
	// 	if (!empty($_REQUEST['title'])) {
	// 		$sql 	=	array();

	// 		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
	// 									SET `title`=?,
	// 									`page_link`=?,
	// 									 `modified_dateTime`=? WHERE id=?";


	// 		$sql['PARAM'][]		  =	array('FILD' => 'title',    	 'DATA' => $_REQUEST['title'], 		 'TYP' => 's');
	// 		$sql['PARAM'][]		  =	array('FILD' => 'page_link',    	 'DATA' => $_REQUEST['page_link'], 		 'TYP' => 's');
	// 		$sql['PARAM'][]		  =	array('FILD' => 'modified_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
	// 		$sql['PARAM'][]		  =	array('FILD' => 'id',    	 'DATA' => $id, 		 'TYP' => 's');

	// 		$mycms->sql_update($sql);
	// 	}

	// 	if (!empty($_FILES['headerImage'])) {
	// 		$sqlUserImage = array();
	// 		$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
														 
	// 													WHERE `id` = '" . $id . "'";
	// 		$fetchData = $mycms->sql_select($sqlUserImage, false);


	// 		$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

	// 		sideIconUpload($id, $_FILES['headerImage'], 'SIDE_ICON_');
	// 	}

	// 	echo '<script>window.location.href="information_setup.php#iconsetup";</script>';
	// 	exit();
	// 	break;
	case 'edit_side_icon':

		global $mycms, $cfg;

		// Ensure arrays exist
		$ids        = $_POST['id'] ?? [];
		$titles     = $_POST['title'] ?? [];
		$page_links = $_POST['page_link'] ?? [];
		$statuses   = $_POST['status'] ?? [];
		$files      = $_FILES['headerImage'] ?? null;

		foreach ($titles as $index => $title) {

			$id        = $ids[$index] ?? '';
			$page_link = $page_links[$index] ?? '';
			$status    = $statuses[$index] ?? 'A';
			$file_name = $files['name'][$index] ?? '';

			if (!empty($id)) {
				// ---------------- Existing row → UPDATE ----------------
				$sql = [];
				$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
									SET `title`=?, `page_link`=?, `status`=?, `modified_dateTime`=? 
									WHERE id=?";
				$sql['PARAM'][] = ['FILD'=>'title', 'DATA'=>$title, 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'page_link', 'DATA'=>$page_link, 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'status', 'DATA'=>$status, 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'modified_dateTime', 'DATA'=>date('Y-m-d H:i:s'), 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'id', 'DATA'=>$id, 'TYP'=>'s'];

				$mycms->sql_update($sql);

				// ---------------- File Upload for existing row ----------------
				if (!empty($file_name) && isset($files['tmp_name'][$index])) {
					sideIconUpload($id, [
						'name' => [$files['name'][$index]],
						'tmp_name' => [$files['tmp_name'][$index]],
						'type' => [$files['type'][$index]],
						'error' => [$files['error'][$index]],
						'size' => [$files['size'][$index]]
					], 'SIDE_ICON_');
				}

			} else {
				// ---------------- New row → INSERT ----------------
				$sql = [];
				$sql['QUERY'] = "INSERT INTO " . _DB_ICON_SETTING_ . " (`title`, `page_link`, `status`, `purpose`, `created_dateTime`) 
								VALUES (?, ?, ?, 'Side Icon', ?)";
				$sql['PARAM'][] = ['FILD'=>'title', 'DATA'=>$title, 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'page_link', 'DATA'=>$page_link, 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'status', 'DATA'=>$status, 'TYP'=>'s'];
				$sql['PARAM'][] = ['FILD'=>'created_dateTime', 'DATA'=>date('Y-m-d H:i:s'), 'TYP'=>'s'];

				$newId = $mycms->sql_insert($sql);

				// ---------------- File Upload for new row ----------------
				if (!empty($file_name) && isset($files['tmp_name'][$index])) {
					sideIconUpload($newId, [
						'name' => [$files['name'][$index]],
						'tmp_name' => [$files['tmp_name'][$index]],
						'type' => [$files['type'][$index]],
						'error' => [$files['error'][$index]],
						'size' => [$files['size'][$index]]
					], 'SIDE_ICON_');
				}
			}
		}

		echo '<script>window.location.href="information_setup.php#iconsetup";</script>';
		exit();
	break;
	case 'ActiveSideIcon':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'A',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("manage_landing_page.php", 2);
		exit();
		break;

	/*================ INACTIVE  =================*/
	case 'InactiveSideIcon':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'I',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("manage_landing_page.php", 2);
		exit();
		break;
}
/*========== UTILITY METHOD ===============*/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey = "_pgn_";

	$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString = "";
	$searchArray  = array();

	$searchArray[$pageKey]                 = $pageKeyVal;
	$searchArray['src_country_name']       = trim($_REQUEST['src_country_name']);

	foreach ($searchArray as $searchKey => $searchVal) {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}

function headerImageUpload($participantId, $header_Image, $title)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= $title . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . "
														SET `image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
// function sideIconUpload($participantId, $header_Image, $title)
// {
// 	global $mycms, $cfg;
// 	$userImage 			= str_replace(" ", "", $header_Image['name']);
// 	$userImageTempFile 	= $header_Image['tmp_name'];
// 	if ($userImageTempFile != "") {
// 		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
// 		$rand							= $title . $ids . '_' . date('ymdHis');
// 		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

// 		$userImageFileName				= $rand . '.' . $ext;

// 		// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
//     	$uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
// 		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	

// 		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
// 			$sqlUserImage = array();
// 			$sqlUserImage['QUERY']           = "UPDATE " . _DB_ICON_SETTING_ . "
// 														SET `icon` = '" . $userImageFileName . "' 
// 														WHERE `id` = '" . $participantId . "'";
// 			$mycms->sql_update($sqlUserImage, false);
// 		}
// 	}
// }
function sideIconUpload($participantId, $header_Image, $title)
{
    global $mycms, $cfg;

    // Loop through each file (supports multiple)
    foreach ($header_Image['name'] as $key => $userImage) {
        $userImageTempFile = $header_Image['tmp_name'][$key];
        if ($userImageTempFile != "") {
            $userImage = str_replace(" ", "", $userImage);

            $ids = str_pad($participantId, 4, '0', STR_PAD_LEFT);
            $rand = $title . $ids . '_' . date('ymdHis');
            $ext  = pathinfo($userImage, PATHINFO_EXTENSION);

            $userImageFileName = $rand . '.' . $ext;

            $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']);
            $userImagePath = $uploadDir . "/" . $userImageFileName;

            if (move_uploaded_file($userImageTempFile, $userImagePath)) {
                // Update DB for this participant
                $sqlUserImage = [];
                $sqlUserImage['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . "
                                          SET `icon` = '" . $userImageFileName . "' 
                                          WHERE `id` = '" . $participantId . "'";
                $mycms->sql_update($sqlUserImage, false);
            }
        }
    }
}
function footerImageUpload($participantId, $footer_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $footer_Image['name']);
	$userImageTempFile 	= $footer_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'FOOTER_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_SITE_ICON_SETTING_ . "
														  SET `footer_Image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function logoImageUpload($participantId, $logo_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $logo_Image['name']);
	$userImageTempFile 	= $logo_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'LOGO_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_SITE_ICON_SETTING_ . "
														  SET `logo_Image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
