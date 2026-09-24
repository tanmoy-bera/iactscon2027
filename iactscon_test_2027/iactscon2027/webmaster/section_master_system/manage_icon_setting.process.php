<?php
session_start();
?>
<?php
include_once('includes/init.php');

$act = @$_REQUEST['act'];
switch ($act) {
	//============================= UPDATE FOOTER TEXT ==================================
	case 'updateFooterText':
		$footer_text 			    = addslashes(trim($_REQUEST['footer_text']));

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
                                        SET `display_footer_text`         = ?";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'display_footer_text', 'DATA' => $footer_text, 'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		pageRedirection("manage_landing_page.php", 2);
		exit();
		break;
	/*================= ACTIVE ICON ==================*/
	case 'Active':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'A',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("manage_icon_setting.php", 2);
		exit();
		break;

	/*================ INACTIVE COUNTRY =================*/
	case 'Inactive':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'I',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("manage_icon_setting.php", 2);
		exit();
		break;

	/*================= REMOVE ICON ==================*/
	case 'Remove':
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'D',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);


		if (!empty($_REQUEST['id'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
				                                         
				                                        WHERE `id` = '" . $_REQUEST['id'] . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);


			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];
			unlink($imgpath);
		}



		pageRedirection("manage_icon_setting.php", 3);
		exit();
		break;

	/*================= ADD ICON ============================*/
	case 'add_icon':

		global $mycms, $cfg;
		$sql_seq['QUERY']       = "SELECT COUNT(id) AS max 
									FROM " . _DB_ICON_SETTING_ . " 
								WHERE `purpose` = ? AND `status`!='D'";

		$sql_seq['PARAM'][]	=	array('FILD' => 'purpose', 	  'DATA' => 'Registration',       'TYP' => 's');
		$resultCnt = $mycms->sql_select($sql_seq);
		$count   = $resultCnt[0]['max'];
		$count = $count + 1;


		$sql 	=	array();

		$sql['QUERY'] = "INSERT INTO " . _DB_ICON_SETTING_ . " 
									SET `title`=?,
									`purpose`=?,
									`page_link`=?,
									`seq`=?,
									 `created_dateTime`=?";


		$sql['PARAM'][]		  =	array('FILD' => 'title',    	 'DATA' => $_REQUEST['iconTitle'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'purpose',    	 'DATA' => $_REQUEST['purpose'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'page_link',    	 'DATA' => $_REQUEST['page_link'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'seq',    	 'DATA' => $count, 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'created_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($sql);

		headerImageUpload($lastInsertId, $_FILES['iconImage']);

		if ($_REQUEST['purpose'] == 'Footer') {
			pageRedirection('manage_icon_setting.php', 2);
		} else {
			pageRedirection('manage_icon_setting.php', 2);
		}


		exit();
		break;

	/*================= EDIT ICON ===========================*/
	case 'edit_icon':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];
		$seq = $_REQUEST['seq'];
		$presentSeq = trim($_REQUEST['presentSeq']);
		// echo '<pre>'; print_r($_REQUEST);die;

		if (!empty($_REQUEST['title']) && $_REQUEST['area'] == 'reg_icon') {
			// echo '<pre>'; print_r($_REQUEST);die;
			$sql_seq 	=	array();
			$sql_seq['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							SET seq = (seq-1) 
						  WHERE  `purpose` = ? AND `seq` > ? AND `status`!=?";
			$sql_seq['PARAM'][]	=	array('FILD' => 'purpose', 	  'DATA' => 'Registration',      'TYP' => 's');
			$sql_seq['PARAM'][]	=	array('FILD' => 'seq', 	      'DATA' => $presentSeq,         'TYP' => 's');
			$sql_seq['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',         'TYP' => 's');

			$mycms->sql_update($sql_seq);

			$sql 	=	array();
			$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
							SET seq 		= (seq+1)
						  WHERE  purpose 	= ? 
						    AND seq 		>= ? AND status!='D'";

			$sql['PARAM'][]	=	array('FILD' => 'purpose', 	  'DATA' => 'Registration',           'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'seq', 	      'DATA' => $seq,                'TYP' => 's');
			$mycms->sql_update($sql);

			$sql 	=	array();
			$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
										SET `title`=?,
										`page_link`=?,
										`seq` 	   =?, 
										`modified_dateTime`=? WHERE id=?";


			$sql['PARAM'][]		  =	array('FILD' => 'title',    	'DATA' => $_REQUEST['title'], 		'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'page_link',    'DATA' => $_REQUEST['page_link'], 	'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'seq', 	      	'DATA' => $seq,                  	'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'modified_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'id',    	 'DATA' => $id, 		 'TYP' => 's');

			$mycms->sql_update($sql);
		}
		if (!empty($_REQUEST['title']) && $_REQUEST['area'] == 'footer') {
			$sql 	=	array();
			$sql['QUERY'] = "UPDATE " . _DB_ICON_SETTING_ . " 
										SET `title`=?,
										`page_link`=?,
										`modified_dateTime`=? WHERE id=?";


			$sql['PARAM'][]		  =	array('FILD' => 'title',    	'DATA' => $_REQUEST['title'], 		'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'page_link',    'DATA' => $_REQUEST['page_link'], 	'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'modified_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'id',    	 'DATA' => $id, 		 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['headerImage'])) {



			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   SELECT * From  " . _DB_ICON_SETTING_ . "
														 
														WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);


			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['icon'];

			headerImageUpload($id, $_FILES['headerImage']);
		}
		if ($_REQUEST['area'] == 'footer') {
			pageRedirection('manage_landing_page.php', 2);
		} else {
			pageRedirection('manage_icon_setting.php', 2);
		}
		exit();
		break;

	case 'edit_online_payment_logo':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];

		if (!empty($_REQUEST['onlinePaymentLogo'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
												SET `modified_dateTime`=? 
												WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['onlinePaymentLogo'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
															WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			regFailureImageUpload($id, $_FILES['onlinePaymentLogo'], 'ONLINE_PAYMENT_LOGO_');
		}
		$mycms->redirect('manage_company_information.php?show=edit&id=1');
		// pageRedirection('manage_icon_setting.php?show=payment', 2);
		exit();
		break;

	case 'edit_QR_code':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];

		if (!empty($_REQUEST['QR_code'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
													SET `modified_dateTime`=? 
													WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['QR_code'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
																WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			regFailureImageUpload($id, $_FILES['QR_code'], 'UPI_QR_');
		}

		// pageRedirection('manage_icon_setting.php', 2);
		$mycms->redirect('manage_company_information.php?show=edit&id=1');
		exit();
		break;

	case 'edit_reg_failure_img':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];
		$regFailureImgText = $_REQUEST['regFailureImgText'];

		if (!empty($_REQUEST['regFailureImgText'])) {
			$sql	=	array();
			$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
												SET `reg_failure_img_text`         = ?";
			$sql['PARAM'][]	=	array('FILD' => 'reg_failure_img_text', 'DATA' => $regFailureImgText,         'TYP' => 's');
			$mycms->sql_update($sql);
		}

		if (!empty($_REQUEST['regFailureImg'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
												SET `modified_dateTime`=? 
												WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['regFailureImg'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
															WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			regFailureImageUpload($id, $_FILES['regFailureImg'], 'REGISTRATION_FAILURE_');
		}

		// pageRedirection('manage_icon_setting.php', 2);
		$mycms->redirect('manage_company_information.php?show=edit&id=1');
		exit();
		break;

	case 'edit_invoice_signature':

		global $mycms, $cfg;
		$id = $_REQUEST['id'];
		$invoice_sign_name = $_REQUEST['invoice_sign_name'];
		$invoice_sign_designation = $_REQUEST['invoice_sign_designation'];
		$authorized_person = $invoice_sign_name . "," . $invoice_sign_designation;

		if (!empty($_REQUEST['invoice_sign_name'])) {
			$sql	=	array();
			$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
													SET `invoice_sign_name`         = ? WHERE `id`='1'";
			$sql['PARAM'][]	=	array('FILD' => 'invoice_sign_name', 'DATA' => $authorized_person,         'TYP' => 's');
			$mycms->sql_update($sql);
		}

		if (!empty($_REQUEST['invoice_signature_img'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
													SET `modified_dateTime`=? 
													WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['invoice_signature_img'])) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
																WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserImage, false);
			// echo "<pre>"; print_r($fetchData);
			$imgpath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			// echo $imgpath;
			unlink($imgpath);
			regFailureImageUpload($id, $_FILES['invoice_signature_img'], 'INVOICE_SIGN_');
		}

		// pageRedirection('manage_icon_setting.php', 2);
		$mycms->redirect('manage_company_information.php?show=edit&id=1');
		exit();
		break;

	case 'edit_webmaster_background':
		// echo "<pre>"; print_r($_FILES['videoOrGif']);die;

		global $mycms, $cfg;
		$id = $_REQUEST['id'];

		if (!empty($_REQUEST['videoOrGif'])) {
			$sql 	=	array();
			$sql['QUERY']   =   "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . " 
																SET `modified_dateTime`=? 
																WHERE id=?";
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $id, 'TYP' => 's');

			$mycms->sql_update($sql);
		}

		if (!empty($_FILES['videoOrGif'])) {
			$sqlUserMedia = array();
			$sqlUserMedia['QUERY']           = "SELECT * From  " . _DB_LANDING_FLYER_IMAGE_ . "
																			WHERE `id` = '" . $id . "'";
			$fetchData = $mycms->sql_select($sqlUserMedia, false);
			$mediaPath = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $fetchData[0]['image'];
			unlink($mediaPath); // Remove the existing file

			// Upload the new video or GIF
			regFailureImageUpload($id, $_FILES['videoOrGif'], 'WEBMASTER_BACKGROUND', $mycms, $cfg);
		}

		$mycms->redirect('manage_company_information.php?show=edit&id=1');
		exit();
		break;

case 'update_branding_images':
	

    global $mycms, $cfg;

    $brandingInputs = [
        'webmaster_background' => 'WEBMASTER_BACKGROUND',
        'invoice_signature'    => 'INVOICE_SIGNATURE',
        'payment_gateway'      => 'ONLINE_PAYMENT_LOGO',
        'failure_image'        => 'FAILURE_PAGE_IMAGE',
        'qr_code'              => 'QR_CODE'
    ];
 
    foreach ($brandingInputs as $inputName => $uploadType) {

        if (!empty($_FILES[$inputName]['name'])) {

            $id = $_POST['branding_ids'][$inputName];

            if (empty($id)) continue;

            // Fetch old image
            $sql = array();
            $sql['QUERY'] = "SELECT image FROM " . _DB_LANDING_FLYER_IMAGE_ . " WHERE id=?";
            $sql['PARAM'][] = array('FILD'=>'id','DATA'=>$id,'TYP'=>'s');

            $fetchData = $mycms->sql_select($sql, false);

            if (!empty($fetchData[0]['image'])) {

                $oldPath = '../../uploads/' . $fetchData[0]['image'];

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Upload new image
            regFailureImageUpload(
                $id,
                $_FILES[$inputName],
                $uploadType
               
            );

            // Update modified time
            $sqlUpdate = array();
            $sqlUpdate['QUERY'] = "UPDATE " . _DB_LANDING_FLYER_IMAGE_ . "
                                   SET modified_dateTime=?
                                   WHERE id=?";
            $sqlUpdate['PARAM'][] = array(
                'FILD'=>'modified_dateTime',
                'DATA'=>date('Y-m-d H:i:s'),
                'TYP'=>'s'
            );
            $sqlUpdate['PARAM'][] = array(
                'FILD'=>'id',
                'DATA'=>$id,
                'TYP'=>'s'
            );

            $mycms->sql_update($sqlUpdate);
        }
    }
	$_SESSION['success_message'] = "Images Updated Successfully";
	$mycms->redirect('company_info.php');
	exit;
	



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

function headerImageUpload($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'ICON_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_ICON_SETTING_ . "
														  SET `icon` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
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
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_ICON_SETTING_ . "
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
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_ICON_SETTING_ . "
														  SET `logo_Image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function regFailureImageUpload($participantId, $header_Image, $purpose)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= $purpose . '_' . $ids . '_' . date('ymdHis');
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
///branding image upload new pop up design
