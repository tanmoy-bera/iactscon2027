<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {
		/************************************************************************/
		/*                             SEARCH HOTEL                             */
		/************************************************************************/
	case 'search_hotel':

		pageRedirection(1, "add_accomodation_date.php", 5);
		exit();
		break;


		/************************************************************************/
		/*                            REMOVE HOTEL                              */
		/************************************************************************/
	case 'Remove':
		$hotel_id               = addslashes(trim($_REQUEST['id']));
		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
									      SET `status` = ?
										WHERE `hotel_id` = ? ";
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,     'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
									      SET `status` = ? 
										WHERE `hotel_id` = ?";
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_TARIFF_ACCOMMODATION_ . " 
								      SET `status` = ? 
									WHERE `hotel_id` = ?";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		pageRedirection(1, "add_accomodation_date.php", 3);
		exit();
		break;

	case 'insert':

		$hotel_id 			    = addslashes(trim($_REQUEST['hotel_id']));
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

		pageRedirection(1, "add_accomodation_date.php", 1);
		exit();
		break;

	case 'update':

		$hotel_id               = addslashes(trim($_REQUEST['id']));

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
									      SET `status` = ? 
										WHERE `hotel_id` = ? ";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
									      SET `status` = ? 
										WHERE `hotel_id` = ? ";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);

		$sqlUpdateHotel	=	array();
		$sqlUpdateHotel['QUERY'] = "UPDATE " . _DB_TARIFF_ACCOMMODATION_ . " 
									      SET `status` = ?  
										WHERE `hotel_id` = ? ";

		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status', 	  'DATA' => 'D',             'TYP' => 's');
		$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'hotel_id',  'DATA' => $hotel_id,       'TYP' => 's');
		$mycms->sql_update($sqlUpdateHotel);



		$hotel_id               = addslashes(trim($_REQUEST['id']));
		$hotel_id 			    = addslashes(trim($_REQUEST['hotel_id']));
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
														       `hotel_id` = ? , 
															   `status` = ? ";
			$sqlInsertout['PARAM'][]	=	array('FILD' => 'check_out_date', 	  'DATA' => $dates[$i],       	  'TYP' => 's');
			$sqlInsertout['PARAM'][]	=	array('FILD' => 'hotel_id', 	  	  'DATA' => $hotel_id,           'TYP' => 's');
			$sqlInsertout['PARAM'][]	=	array('FILD' => 'status',  		  'DATA' => 'A',       		  'TYP' => 's');
			$mycms->sql_insert($sqlInsertout);
		}

		// pageRedirection(1, "add_accomodation_date.php", 2);
		$mycms->redirect('add_accomodation_date.php?show=edit&id='.$hotel_id.'&status=updateSeat');
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
