<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {

	/**************************************************************************/
	/*                             EDIT USER PROFILE                          */
	/**************************************************************************/
	case 'tag_add':

		editTagProcess($cfg, $mycms);
		$mycms->redirect("tags_registration.php");
		exit();
		break;

	case 'search_registration_tag':

		pageRedirection("tags_registration.php", 5);
		exit();
		break;

	//////////////////////////

	case 'insertTag':

		$tag_name	 					= addslashes(trim($_REQUEST['tag_name']));
		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']			= "INSERT INTO " . _DB_TAG_MASTER_ . " 
											  	   SET `tag_name` = '" . $tag_name . "', 
												   `color` = '" . $_REQUEST['color'] . "',
													   `status` = 'A'";

		$mycms->sql_insert($sqlInsertSession);
		pageRedirection("tags_registration.php", 1, "&show=tagMaster");
		break;

	case 'updateTagMaster':
		$id					= addslashes(trim($_REQUEST['id']));
		$tag_name 				= addslashes(trim($_REQUEST['tag_name']));
		// UPDATING SESSION DETAILS
		$sqlUpdateSession = array();
		$sqlUpdateSession['QUERY'] 			= "UPDATE " . _DB_TAG_MASTER_ . "
											  SET `tag_name` = '" . $tag_name . "' ,
											   `color` = '" . $_REQUEST['color'] . "' 
											WHERE `id` = ?";

		$sqlUpdateSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sqlUpdateSession);

		pageRedirection("tags_registration.php", 2, "&show=tagMaster");
		exit();
		break;

	case 'InactiveTag':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_TAG_MASTER_ . "
					   SET `status`='I'
					 WHERE id = ?";

		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		pageRedirection("tags_registration.php", 2, "&show=tagMaster");
		break;

	case 'ActiveTag':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_TAG_MASTER_ . "
					   SET `status`='A'
					 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("tags_registration.php", 2, "&show=tagMaster");
		break;
	case 'removeTag':
		$id 				= addslashes(trim($_REQUEST['id']));

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "UPDATE " . _DB_TAG_MASTER_ . " 
													  SET   `status` ='D'  WHERE `id`=?";

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sqlInsertSession);
		pageRedirection("tags_registration.php", 2, "&show=tagMaster");
		break;

	////////////////////////////////////////////////////////////

	default:
		break;
}


function editTagProcess($cfg, $mycms)
{
	$userId			   = $_REQUEST['user_id'];
	$userTags   	   = $_REQUEST['tag'];
	//$userAcchivement   = $_REQUEST['achievement'];
	$allTags		   = implode(",", $userTags);
	$allAcchivement	   = implode(",", $userAcchivement);

	$sqlEditTags				=	array();
	$sqlEditTags['QUERY']       =      "UPDATE " . _DB_USER_REGISTRATION_ . "
											  SET `tags` = ?
											WHERE `id`	= ?";
	$sqlEditTags['PARAM'][]	=	array('FILD' => 'tags', 	  		  'DATA' => $allTags,            'TYP' => 's');
	$sqlEditTags['PARAM'][]	=	array('FILD' => 'id', 	  			  'DATA' => $userId,             'TYP' => 's');

	$mycms->sql_update($sqlEditTags, false);
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
	$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key'], '#'));
	$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_user_full_name']         = addslashes(trim($_REQUEST['src_user_full_name']));
	$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
	$searchArray['src_registration_tag']       = addslashes(trim($_REQUEST['src_registration_tag']));
	$searchArray['src_atom_transaction_ids']   = trim($_REQUEST['src_atom_transaction_ids']);
	$searchArray['src_transaction_ids']        = trim($_REQUEST['src_transaction_ids']);
	$searchArray['src_conf_reg_category']      = trim($_REQUEST['src_conf_reg_category']);
	$searchArray['src_registration_id']        = trim($_REQUEST['src_registration_id']);

	if (isset($_REQUEST['goto']) &&  trim($_REQUEST['goto']) != '') {
		$goto = '&show=' . trim($_REQUEST['goto']);
	}

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString . $goto);
}
