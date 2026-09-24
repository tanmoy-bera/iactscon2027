<?php
include_once('includes/init.php');


switch ($action) {
	case 'search_classification':

		pageRedirection('registration_tariff.php', 5, "");
		exit();
		break;

	// REG CUTOFF ==================================
	case 'update':
		updateCutoff($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'insert':
		insertCutoff($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'Active':
		Active($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'Inactive':
		Inactive($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'deleteCutoff':
		deleteCutoff($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;


	// Workshop CUTOFF ==================================
	case 'updateWorkshop':
		updateWorkshopCutoff($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "&show=workshop");
		break;

	case 'insertWorkshop':
		insertWorkshopCutoff($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "&show=workshop");
		break;

	case 'ActiveWorkshop':
		ActiveWorkshop($mycms, $cfg);
		break;

	case 'InactiveWorkshop':
		InactiveWorkshop($mycms, $cfg);
		break;

	case 'deleteWorkshopCutoff':
		deleteWorkshopCutoff($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 1, "&show=workshop");
		break;



	// ------------------- CONF DATE START -------------------

	case 'insertDate':
		insertDate($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'ActiveDate':
		ActiveDate($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'InactiveDate':
		InactiveDate($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;
	case 'deleteDate':
		deleteDate($mycms, $cfg);
		// pageRedirection('manage_cutoff.php', 2, "");
		break;
}

/************************ REG CUTOFF FUNCTION**********************/
function updateCutoff($mycms, $cfg)
{
	$loggedUserID 					 = $mycms->getLoggedUserId();
	$cutoff_id						 = $_REQUEST['cutoff_id'];
	$cutoff_title                 	 = addslashes(trim($_REQUEST['cutoff_title']));
	$start_date         			 = $_REQUEST['start_date'];
	$end_date         				 = $_REQUEST['end_date'];

	$sql		  =	array();
	$sql['QUERY'] = "UPDATE" . _DB_TARIFF_CUTOFF_ . " 
							SET `cutoff_title`				= ?,
							  	`start_date`  				= ?,
							 	`end_date`					= ?,
							  	`modified_by`				= ?,
								`modified_ip`				= ?,
								`modified_sessionid`		= ?,
								`modified_datetime`			= ?
							  WHERE	`id`                	= ?";

	$sql['PARAM'][]	=	array('FILD' => 'cutoff_title', 				'DATA' => $cutoff_title, 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'start_date', 				    'DATA' => $start_date, 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'end_date', 				    'DATA' => $end_date, 						    'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			    'DATA' => $loggedUserID, 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 			'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_sessionid', 		    'DATA' => session_id(), 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_datetime', 			'DATA' => date('Y-m-d'), 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $cutoff_id, 							'TYP' => 's');
   
	$mycms->sql_update($sql);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
    exit();
}
function Active($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_TARIFF_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'A', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
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
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
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
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
	exit();
}

function insertCutoff($mycms, $cfg)
{
	$loggedUserID 		= $mycms->getLoggedUserId();
	$workshop			= addslashes($_REQUEST['workshop_add']);
	$seatlimit			= addslashes($_REQUEST['seat_limit_add']);
	$workshop_date_add  = addslashes($_REQUEST['workshop_date_add']);

	$insertOrderHistory	=	array();
	$insertOrderHistory['QUERY']	= "INSERT INTO " . _DB_TARIFF_CUTOFF_ . "
										  SET  `cutoff_title` 		= ?,
												`start_date` 		= ?,
												`end_date` 			= ?,
												`status`			= ?,
												`created_by` 		= ?,
												`created_ip` 		= ?,
												`created_sessionid`	= ?,
												`created_datetime`	= ?";

	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'cutoff_title', 				   'DATA' => $workshop, 						        'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'start_date', 			           'DATA' => $seatlimit, 						    'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'end_date', 		               'DATA' => $workshop_date_add, 			    'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array(
		'FILD' => 'status',
		'DATA' => 'A',
		'TYP' => 's'
	);
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_by', 			           'DATA' => $loggedUserID, 		            'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_ip', 	                   'DATA' => $_SERVER['REMOTE_ADDR'], 					        'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_sessionid', 			   'DATA' => session_id(), 							        'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_datetime', 			   'DATA' => date('Y-m-d H:i:s'), 							    'TYP' => 's');

	$mycms->sql_insert($insertOrderHistory);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
	exit();
}

/************************ WORKSHOP CUTOFF FUNCTION**********************/
function updateWorkshopCutoff($mycms, $cfg)
{
	$loggedUserID 					 = $mycms->getLoggedUserId();
	$cutoff_id						 = $_REQUEST['cutoff_id'];
	$cutoff_title                 	 = addslashes(trim($_REQUEST['cutoff_title']));
	$start_date         			 = $_REQUEST['start_date'];
	$end_date         				 = $_REQUEST['end_date'];

	$sql		  =	array();
	$sql['QUERY'] = "UPDATE" . _DB_WORKSHOP_CUTOFF_ . " 
							SET `cutoff_title`				= ?,
							  	`start_date`  				= ?,
							 	`end_date`					= ?,
							  	`modified_by`				= ?,
								`modified_ip`				= ?,
								`modified_sessionid`		= ?,
								`modified_datetime`			= ?
							  WHERE	`id`                	= ?";

	$sql['PARAM'][]	=	array('FILD' => 'cutoff_title', 				'DATA' => $cutoff_title, 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'start_date', 				    'DATA' => $start_date, 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'end_date', 				    'DATA' => $end_date, 						    'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			    'DATA' => $loggedUserID, 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 			'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_sessionid', 		    'DATA' => session_id(), 						'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_datetime', 			'DATA' => date('Y-m-d'), 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $cutoff_id, 							'TYP' => 's');

	$mycms->sql_update($sql);
		echo '<script>window.location.href="system_master.php#cutoff";</script>';

}
function ActiveWorkshop($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CUTOFF_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'A', 			 			  'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
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
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
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
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
	exit();
}

function insertWorkshopCutoff($mycms, $cfg)
{
	$loggedUserID 		= $mycms->getLoggedUserId();
	$workshop			= addslashes($_REQUEST['workshop_add']);
	$seatlimit			= addslashes($_REQUEST['seat_limit_add']);
	$workshop_date_add  = addslashes($_REQUEST['workshop_date_add']);

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
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'start_date', 			           'DATA' => $seatlimit, 						    'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'end_date', 		               'DATA' => $workshop_date_add, 			    'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array(
		'FILD' => 'status',
		'DATA' => 'A',
		'TYP' => 's'
	);
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_by', 			           'DATA' => $loggedUserID, 		            'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_ip', 	                   'DATA' => $_SERVER['REMOTE_ADDR'], 					        'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_sessionid', 			   'DATA' => session_id(), 							        'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'created_datetime', 			   'DATA' => date('Y-m-d H:i:s'), 							    'TYP' => 's');

	$mycms->sql_insert($insertOrderHistory);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
	exit();
}


// ================================= CONF DATE START ================================ 
function insertDate($mycms, $cfg)
{
	$loggedUserID 		= $mycms->getLoggedUserId();
	$conf_date			= addslashes($_REQUEST['conf_date']);

	$insertOrderHistory	=	array();
	$insertOrderHistory['QUERY']	= "INSERT INTO " . _DB_CONFERENCE_DATE_ . "
										  SET  `conf_date` 		= ?";


	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'conf_date',  'DATA' => $conf_date,  'TYP' => 's');

	$mycms->sql_insert($insertOrderHistory);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
	exit();
}

// function updateDate($mycms, $cfg)
// {
// 	$loggedUserID 					 = $mycms->getLoggedUserId();
// 	$cutoff_id						 = $_REQUEST['cutoff_id'];
// 	$cutoff_title                 	 = addslashes(trim($_REQUEST['cutoff_title']));
// 	$start_date         			 = $_REQUEST['start_date'];
// 	$end_date         				 = $_REQUEST['end_date'];

// 	$sql		  =	array();
// 	$sql['QUERY'] = "UPDATE" . _DB_TARIFF_CUTOFF_ . " 
// 							SET `cutoff_title`				= ?,
// 							  	`start_date`  				= ?,
// 							 	`end_date`					= ?,
// 							  	`modified_by`				= ?,
// 								`modified_ip`				= ?,
// 								`modified_sessionid`		= ?,
// 								`modified_datetime`			= ?
// 							  WHERE	`id`                	= ?";

// 	$sql['PARAM'][]	=	array('FILD' => 'cutoff_title', 				'DATA' => $cutoff_title, 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'start_date', 				    'DATA' => $start_date, 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'end_date', 				    'DATA' => $end_date, 						    'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			    'DATA' => $loggedUserID, 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 			'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_sessionid', 		    'DATA' => session_id(), 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_datetime', 			'DATA' => date('Y-m-d'), 				        'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $cutoff_id, 							'TYP' => 's');

// 	$mycms->sql_update($sql);
// }

function ActiveDate($mycms, $cfg)
{

	$sql_date['QUERY'] = "UPDATE " . _DB_CONFERENCE_DATE_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql_date['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'A', 			 			  'TYP' => 's');
	$sql_date['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql_date);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
	exit();
}

function InactiveDate($mycms, $cfg)
{

	$sql_date['QUERY'] = "UPDATE " . _DB_CONFERENCE_DATE_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql_date['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'I', 			 			  'TYP' => 's');
	$sql_date['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql_date);
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
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
	echo '<script>window.location.href="system_master.php#cutoff";</script>';
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
