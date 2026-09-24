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

		/*==================== SEARCH COUNTRY =======================*/
	case 'search_country':
		pageRedirection('manage_mail_setting.php', 5);
		exit();
		break;

		/*================= ACTIVE COUNTRY ==================*/
	case 'Active':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_EMAIL_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'A',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_conference_mail.php";</script>';
		exit();
		break;

		/*================ INACTIVE COUNTRY =================*/
	case 'Inactive':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_EMAIL_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'I',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_conference_mail.php";</script>';
		exit();
		break;

		/*================= REMOVE COUNTRY ==================*/
	case 'Remove':
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_EMAIL_SETTING_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'D',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_conference_mail.php";</script>';
		exit();
		break;

		/*================= ADD COUNTRY ============================*/
	case 'add_image':

		global $mycms, $cfg;


		$sql 	=	array();

		$sql['QUERY'] = "INSERT INTO " . _DB_EMAIL_SETTING_ . " 
									SET `created_dateTime`=?";

		$sql['PARAM'][]		  =	array('FILD' => 'created_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($sql);

		headerImageUpload($lastInsertId, $_FILES['headerImage']);
		footerImageUpload($lastInsertId, $_FILES['footerImage']);
		logoImageUpload($lastInsertId, $_FILES['logoImage']);
		mailerlogoImageUpload($lastInsertId, $_FILES['mailer_logo']);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_mail_settings.php";</script>';
		exit();
		break;

		/*================= EDIT COUNTRY ===========================*/
	case 'edit_country':

		$id = $_REQUEST['id'];

		$sqllogo['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `logo_size`='" . $_REQUEST['logo_size'] . "'
						   WHERE `id` = '" . $id . "'";
		$mycms->sql_update($sqllogo);
		if (!empty($_FILES['headerImage'])) {
			headerImageUpload($id, $_FILES['headerImage']);
		}
		if (!empty($_FILES['footerImage'])) {
			footerImageUpload($id, $_FILES['footerImage']);
		}
		if (!empty($_FILES['mailer_logo'])) {
			mailerlogoImageUpload($id, $_FILES['mailer_logo']);
		}
		if (!empty($_FILES['logoImage'])) {
			logoImageUpload($id, $_FILES['logoImage']);
		}
		$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Image updated successfully!' // dynamic message
			];


			echo '<script>window.location.href="manage_conference_mail.php";</script>';
		exit();
		break;

	case 'edit_scientific':

		$id = $_REQUEST['id'];
		if (!empty($_FILES['sc_headerImage'])) {
			headerImageUploadScientific($id, $_FILES['sc_headerImage']);
		}
		if (!empty($_FILES['sc_sidebar_image'])) {
			sidebarImageUploadScientific($id, $_FILES['sc_sidebar_image']);
		}
		if (!empty($_FILES['sc_footerImage'])) {
			footerImageUploadScientific($id, $_FILES['sc_footerImage']);
		}

		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'edit_exhibitor':

		$id = $_REQUEST['id'];
		if (!empty($_FILES['exb_headerImage'])) {
			headerImageUploadExhibitor($id, $_FILES['exb_headerImage']);
		}
		if (!empty($_FILES['exb_sidebar_image'])) {
			sidebarImageUploadExhibitor($id, $_FILES['exb_sidebar_image']);
		}
		if (!empty($_FILES['exb_footerImage'])) {
			footerImageUploadExhibitor($id, $_FILES['exb_footerImage']);
		}

		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeDocHeader':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `header_image`=''
						   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);
		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeDocFooter':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `footer_image`=''
							   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);
		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeScHeader':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `sc_header_image`=''
								   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);
		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeScSidebar':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `sc_sidebar_image`=''
									   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);
		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeScFooter':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `sc_footer_image`=''
										   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);

		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeExbHeader':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `exb_header_image`=''
									   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);
		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeExbSidebar':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `exb_sidebar_image`=''
										   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);
		pageRedirection('manage_mail_setting.php', 2);
		exit();
		break;

	case 'removeExbFooter':

		$sqlremove['QUERY']    = "UPDATE  " . _DB_EMAIL_SETTING_ . " SET `exb_footer_image`=''
											   WHERE `id` = '1'";
		$mycms->sql_update($sqlremove);

		pageRedirection('manage_mail_setting.php', 2);
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

function headerImageUpload($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'HEADER_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
        $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	
		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `header_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function headerImageUploadScientific($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'SC_HEADER_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `sc_header_image` = '" . $userImageFileName . "' 
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

		// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
        $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	
		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `footer_Image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function sidebarImageUploadScientific($participantId, $footer_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $footer_Image['name']);
	$userImageTempFile 	= $footer_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'SC_SIDEBAR_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `sc_sidebar_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function footerImageUploadScientific($participantId, $footer_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $footer_Image['name']);
	$userImageTempFile 	= $footer_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'SC_FOOTER_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `sc_footer_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function headerImageUploadExhibitor($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'EXB_HEADER_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `exb_header_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function sidebarImageUploadExhibitor($participantId, $footer_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $footer_Image['name']);
	$userImageTempFile 	= $footer_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'EXB_SIDEBAR_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `exb_sidebar_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function footerImageUploadExhibitor($participantId, $footer_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $footer_Image['name']);
	$userImageTempFile 	= $footer_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'EXB_FOOTER_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `exb_footer_image` = '" . $userImageFileName . "' 
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

		// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
        $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	
		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `logo_Image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
function mailerlogoImageUpload($participantId, $logo_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $logo_Image['name']);
	$userImageTempFile 	= $logo_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'MAILER_LOGO_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		// $userImagePath     				= '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $userImageFileName;
         $uploadDir = realpath(__DIR__ . "/../" . $cfg['EMAIL.HEADER.FOOTER.IMAGE']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	
		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_EMAIL_SETTING_ . "
														  SET `mailer_logo` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
