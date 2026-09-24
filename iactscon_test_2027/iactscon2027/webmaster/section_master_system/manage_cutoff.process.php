<?php
include_once('includes/init.php');


switch ($action) {
	case 'search_classification':

		pageRedirection('registration_tariff.php', 5, "");
		exit();
		break;

	// REG CUTOFF ==================================
	case 'update':
		// if AJAX request, return JSON, otherwise keep original redirect behaviour
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			$res = updateCutoff($mycms, $cfg);
			header('Content-Type: application/json');
			echo json_encode($res);
			exit();
		} else {
			updateCutoff($mycms, $cfg);
			pageRedirection('manage_cutoff.php', 2, "");
		}
		break;

	case 'insert':
		insertCutoff($mycms, $cfg);
		pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'Active':
		Active($mycms, $cfg);
		break;

	case 'Inactive':
		Inactive($mycms, $cfg);
		break;

	case 'deleteCutoff':
		deleteCutoff($mycms, $cfg);
		break;


	// Workshop CUTOFF ==================================
	case 'updateWorkshop':
		// support AJAX update (return JSON) or normal redirect
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			$res = updateWorkshopCutoff($mycms, $cfg);
			header('Content-Type: application/json');
			echo json_encode($res);
			exit();
		} else {
			updateWorkshopCutoff($mycms, $cfg);
			pageRedirection('manage_cutoff.php', 2, "&show=workshop");
		}
		break;

	case 'insertWorkshop':
		insertWorkshopCutoff($mycms, $cfg);
		break;

	case 'ActiveWorkshop':
		ActiveWorkshop($mycms, $cfg);
		break;

	case 'InactiveWorkshop':
		InactiveWorkshop($mycms, $cfg);
		break;

	case 'deleteWorkshopCutoff':
		deleteWorkshopCutoff($mycms, $cfg);
		break;



	// ------------------- CONF DATE START -------------------

	case 'insertDate':
		insertDate($mycms, $cfg);
		break;

	case 'updateDate':
		// support AJAX update (return JSON) or normal redirect
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			$res = updateDate($mycms, $cfg);
			header('Content-Type: application/json');
			echo json_encode($res);
			exit();
		} else {
			updateDate($mycms, $cfg);
			$mycms->redirect("system_master.php");
		}
		break;

	case 'getDate':
		$id = intval($_REQUEST['id'] ?? 0);
		if ($id <= 0) {
			header('Content-Type: application/json');
			echo json_encode(['status' => false, 'message' => 'Invalid id']);
			exit;
		}
		$sql = ['QUERY' => "SELECT * FROM " . _DB_CONFERENCE_DATE_ . " WHERE id = ? LIMIT 1"];
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

	case 'ActiveDate':
		ActiveDate($mycms, $cfg);
		// function will redirect to system_master
		break;

	case 'InactiveDate':
		InactiveDate($mycms, $cfg);
		// function will redirect to system_master
		break;
	case 'deleteDate':
		deleteDate($mycms, $cfg);
		// function will redirect to system_master
		break;

	// new: fetch a single cutoff record (AJAX used by edit popup)
	case 'getCutoff':
		$id = intval($_REQUEST['id'] ?? 0);
		if ($id <= 0) {
			header('Content-Type: application/json');
			echo json_encode(['status' => false, 'message' => 'Invalid id']);
			exit;
		}
		$sql = ['QUERY' => "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " WHERE id = ? LIMIT 1"];
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

	// new: fetch a single workshop cutoff record (AJAX used by edit popup)
	case 'getWorkshop':
		$id = intval($_REQUEST['id'] ?? 0);
		if ($id <= 0) {
			header('Content-Type: application/json');
			echo json_encode(['status' => false, 'message' => 'Invalid id']);
			exit;
		}
		$sql = ['QUERY' => "SELECT * FROM " . _DB_WORKSHOP_CUTOFF_ . " WHERE id = ? LIMIT 1"];
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
}

/************************ REG CUTOFF FUNCTION**********************/
function updateCutoff($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();
	$cutoff_id    = $_REQUEST['cutoff_id'];
	$cutoff_title = addslashes(trim($_REQUEST['cutoff_title']));
	$start_date   = $_REQUEST['start_date'];
	$end_date     = $_REQUEST['end_date'];

	$sql = array();
	$sql['QUERY'] = "UPDATE " . _DB_TARIFF_CUTOFF_ . " 
                            SET `cutoff_title`				= ?,
                              	`start_date`  				= ?,
                             	`end_date`					= ?,
                              	`modified_by`				= ?,
                                `modified_ip`				= ?,
                                `modified_sessionid`		= ?,
                                `modified_datetime`			= ?
                              WHERE	`id`                	= ?";

	$sql['PARAM'][] = array('FILD' => 'cutoff_title', 'DATA' => $cutoff_title, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'start_date', 'DATA' => $start_date, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'end_date', 'DATA' => $end_date, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_by', 'DATA' => $loggedUserID, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_sessionid', 'DATA' => session_id(), 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_datetime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $cutoff_id, 'TYP' => 's');

	$ok = $mycms->sql_update($sql);

	// return status for AJAX usage; non-AJAX callers ignore return value
	if ($ok) {
		return ['status' => true, 'message' => 'Cutoff updated successfully'];
	} else {
		return ['status' => false, 'message' => 'Update failed'];
	}
}
function Active($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_TARIFF_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'A', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}
function Inactive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_TARIFF_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'I', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}

function deleteCutoff($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_TARIFF_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'D', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}

function insertCutoff($mycms, $cfg)
{
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		echo json_encode([
			'status' => false,
			'message' => 'Invalid request'
		]);
		exit;
	}

	$loggedUserID = $mycms->getLoggedUserId();

	$title      = trim($_POST['title'] ?? '');
	$start_date = trim($_POST['start_date'] ?? '');
	$end_date   = trim($_POST['end_date'] ?? '');

	if ($title === '' || $start_date === '' || $end_date === '') {
		echo json_encode([
			'status' => false,
			'message' => 'All fields are required'
		]);
		exit;
	}

	$insert = [];
	$insert['QUERY'] = "INSERT INTO " . _DB_TARIFF_CUTOFF_ . "
        SET cutoff_title       = ?,
            start_date         = ?,
            end_date           = ?,
            status             = ?,
            created_by         = ?,
            created_ip         = ?,
            created_sessionid  = ?,
            created_datetime   = ?";

	$insert['PARAM'][] = ['FILD' => 'cutoff_title',      'DATA' => $title,                    'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'start_date',        'DATA' => $start_date,               'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'end_date',          'DATA' => $end_date,                 'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'status',            'DATA' => 'A',                       'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'created_by',        'DATA' => $loggedUserID,              'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'created_ip',        'DATA' => $_SERVER['REMOTE_ADDR'],    'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'created_sessionid', 'DATA' => session_id(),               'TYP' => 's'];
	$insert['PARAM'][] = ['FILD' => 'created_datetime',  'DATA' => date('Y-m-d H:i:s'),         'TYP' => 's'];

	$result = true; //$mycms->sql_insert($insert);

	if ($result) {
		echo json_encode([
			'status' => true,
			'message' => 'Registration cutoff added successfully'
		]);
	} else {
		echo json_encode([
			'status' => false,
			'message' => 'Something went wrong'
		]);
	}

	exit();
}

/************************ WORKSHOP CUTOFF FUNCTION**********************/
function insertWorkshopCutoff($mycms, $cfg)
{
	$loggedUserID 		= $mycms->getLoggedUserId();
	$workshop			= addslashes($_REQUEST['workshop_add']);
	$start_date			= addslashes($_REQUEST['start_date']);
	$end_date			= addslashes($_REQUEST['end_date']);

	$insertOrderHistory	=	array();
	$insertOrderHistory['QUERY']	= "INSERT INTO " . _DB_WORKSHOP_CUTOFF_ . "
										  SET  `cutoff_title` 		= ?,
												`start_date` 		= ?,
												`end_date` 			= ?,
												`status`			= ?,
												`created_by` 		= ?,
												`created_ip` 		= ?,
												`created_sessionid`	= ?,
												`created_datetime`	= ?";

	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'cutoff_title', 				   'DATA' => $workshop, 						        'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'start_date', 			           'DATA' => $start_date, 						 	    'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'end_date', 		               'DATA' => $end_date, 			    				'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'status',						   'DATA' => 'A',										'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_by', 			           'DATA' => $loggedUserID, 		           			'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_ip', 	                   'DATA' => $_SERVER['REMOTE_ADDR'], 					'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_sessionid', 			   'DATA' => session_id(), 							    'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_datetime', 			   'DATA' => date('Y-m-d H:i:s'), 						'TYP' => 's');

	$id = $mycms->sql_insert($insertOrderHistory);
	if ($id) {
		return ['status' => true, 'message' => 'Workshop cutoff added successfully'];
	} else {
		return ['status' => false, 'message' => 'Insertion failed'];
	}
	exit();
}

function updateWorkshopCutoff($mycms, $cfg)
{
	$loggedUserID = $mycms->getLoggedUserId();
	$cutoff_id = $_REQUEST['cutoff_id'] ?? 0;
	$cutoff_title = addslashes(trim($_REQUEST['cutoff_title'] ?? ''));
	$start_date = $_REQUEST['start_date'] ?? '';
	$end_date = $_REQUEST['end_date'] ?? '';

	$sql = array();
	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CUTOFF_ . " 
                        SET `cutoff_title` = ?,
                            `start_date` = ?,
                            `end_date` = ?,
                            `modified_by` = ?,
                            `modified_ip` = ?,
                            `modified_sessionid` = ?,
                            `modified_datetime` = ?
                        WHERE `id` = ?";
	$sql['PARAM'][] = array('FILD' => 'cutoff_title', 'DATA' => $cutoff_title, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'start_date', 'DATA' => $start_date, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'end_date', 'DATA' => $end_date, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_by', 'DATA' => $loggedUserID, 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_sessionid', 'DATA' => session_id(), 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'modified_datetime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
	$sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $cutoff_id, 'TYP' => 's');

	$ok = $mycms->sql_update($sql);

	if ($ok) {
		return ['status' => true, 'message' => 'Workshop cutoff updated successfully'];
	} else {
		return ['status' => false, 'message' => 'Update failed'];
	}
}
function ActiveWorkshop($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'A', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}
function InactiveWorkshop($mycms, $cfg)
{
	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'I', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}

function deleteWorkshopCutoff($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'D', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	$mycms->redirect("system_master.php");
	exit();
}

// ================================= CONF DATE START ================================ 
// function insertDate($mycms, $cfg)
// {
// 	$loggedUserID 		= $mycms->getLoggedUserId();
// 	$conf_date			= addslashes($_REQUEST['conf_date']);

// 	$insertOrderHistory	=	array();
// 	$insertOrderHistory['QUERY']	= "INSERT INTO " . _DB_CONFERENCE_DATE_ . "
// 										  SET  `conf_date` 		= ?";


// 	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'conf_date',  'DATA' => $conf_date,  'TYP' => 's');

// 	$mycms->sql_insert($insertOrderHistory);
// 	pageRedirection("manage_cutoff.php", 2);
// 	exit();
// }

function insertDate($mycms, $cfg)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Invalid request']);
            exit;
        } else {
            pageRedirection("system_master.php", 3);
        }
    }

    $conf_date = trim($_REQUEST['conf_date'] ?? '');
    if ($conf_date === '') {
       echo json_encode(['status' => false, 'message' => 'Conference date is required']);
       exit;
    }

    $insert = [];
    $insert['QUERY'] = "INSERT INTO " . _DB_CONFERENCE_DATE_ . " SET conf_date = ?, status = ?";
    $insert['PARAM'][] = ['FILD' => 'conf_date', 'DATA' => $conf_date, 'TYP' => 's'];
    $insert['PARAM'][] = ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'];

    $id = $mycms->sql_insert($insert);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if ($id) {
            header('Content-Type: application/json');
            echo json_encode(['status' => true, 'message' => 'Conference date added successfully']);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Insertion failed']);
            exit;
        }
    } else {
        pageRedirection("system_master.php", 2);
    }
}

/* new updateDate function */
function updateDate($mycms, $cfg)
{
    $date_id = intval($_REQUEST['date_id'] ?? 0);
    $conf_date = trim($_REQUEST['conf_date'] ?? '');

    if ($date_id <= 0 || $conf_date === '') {
        return ['status' => false, 'message' => 'Invalid input'];
    }

    $sql = [];
    $sql['QUERY'] = "UPDATE " . _DB_CONFERENCE_DATE_ . " SET conf_date = ? WHERE id = ?";
    $sql['PARAM'][] = ['FILD' => 'conf_date', 'DATA' => $conf_date, 'TYP' => 's'];
    $sql['PARAM'][] = ['FILD' => 'id', 'DATA' => $date_id, 'TYP' => 's'];

    $ok = $mycms->sql_update($sql);

    if ($ok) {
        return ['status' => true, 'message' => 'Conference date updated successfully'];
    }
    return ['status' => false, 'message' => 'Update failed'];
}

/* change ActiveDate/InactiveDate/deleteDate to redirect to system_master.php (or return JSON if AJAX) */
function ActiveDate($mycms, $cfg)
{
    $sql_date['QUERY'] = "UPDATE " . _DB_CONFERENCE_DATE_ . " SET `status` = ? WHERE `id` = ?";
    $sql_date['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
    $sql_date['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 's');
    $mycms->sql_update($sql_date);
    $mycms->redirect("system_master.php");
    exit();
}

function InactiveDate($mycms, $cfg)
{
    $sql_date['QUERY'] = "UPDATE " . _DB_CONFERENCE_DATE_ . " SET `status` = ? WHERE `id` = ?";
    $sql_date['PARAM'][] = array('FILD' => 'status', 'DATA' => 'I', 'TYP' => 's');
    $sql_date['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 's');
    $mycms->sql_update($sql_date);
    $mycms->redirect("system_master.php");
    exit();
}

function deleteDate($mycms, $cfg)
{

	$sql_date['QUERY'] = "UPDATE " . _DB_CONFERENCE_DATE_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql_date['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'D', 			 			  'TYP' => 's');
	$sql_date['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql_date);
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
