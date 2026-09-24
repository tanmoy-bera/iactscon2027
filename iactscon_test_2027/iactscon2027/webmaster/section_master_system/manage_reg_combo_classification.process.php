<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {
	case 'search_classification':

		pageRedirection('registration_tariff.php', 5, "");
		exit();
		break;

		/***************************************************************************/
		/*                      UPDATE TARIFF CLASSIFICATION                       */
		/***************************************************************************/

		/***************************************************************************/
		/*                      Insert TARIFF CLASSIFICATION                       */
		/***************************************************************************/

	case 'add':
		$classification_title 	    = addslashes($_REQUEST['classification_title']);
		$editseatlimit 	 = addslashes($_REQUEST['seat_limit_add']);
		$sequence_by     = addslashes($_REQUEST['sequence_by']);
		$hotel_id 	      	 = addslashes($_REQUEST['type']);
		$room_type 	      	 = addslashes($_REQUEST['room_type']);
		$currency 	     = addslashes($_REQUEST['currency']);
		$sessionId	     = session_id();
		$userIp		     = $_SERVER['REMOTE_ADDR'];
		$userBrowser     = $_SERVER['HTTP_USER_AGENT'];
		$workshop_price  = !empty($_REQUEST['workshop_price']) ? $_REQUEST['workshop_price'] : '0.00';
		$dinner_price 	 = !empty($_REQUEST['dinner_price']) ? $_REQUEST['dinner_price'] : '0.00';
		$inclusion_lunch_date= $_REQUEST['inclusion_lunch_date'];
		$inclusion_dinner_date= $_REQUEST['inclusion_dinner_date'];
		$inclusion_conference_kit = $_REQUEST['inclusion_conference_kit'];
		$inclusion_sci_hall 	    		 = $_REQUEST['inclusion_sci_hall'];
		$inclusion_exb_area 	    		 = $_REQUEST['inclusion_exb_area'];
		$inclusion_tea_coffee 	    		 = $_REQUEST['inclusion_tea_coffee'];

		$accommodation_price_individual = !empty($_REQUEST['accommodation_price_individual']) ? $_REQUEST['accommodation_price_individual'] : '0.00';
		$accommodation_price_shared = !empty($_REQUEST['accommodation_price_shared']) ? $_REQUEST['accommodation_price_shared'] : '0.00';
		$registration_price = !empty($_REQUEST['registration_price']) ? $_REQUEST['registration_price'] : '0.00';
		$round_of_price_individual = !empty($_REQUEST['round_of_price_individual']) ? $_REQUEST['round_of_price_individual'] : '0.00';
		$round_of_price_shared = !empty($_REQUEST['round_of_price_shared']) ? $_REQUEST['round_of_price_shared'] : '0.00';
		$total_price = !empty($_REQUEST['total_price']) ? $_REQUEST['total_price'] : '0.00';
		$total_price_shared = !empty($_REQUEST['total_price_shared']) ? $_REQUEST['total_price_shared'] : '0.00';
		$total_round_price = !empty($_REQUEST['total_round_price']) ? $_REQUEST['total_round_price'] : '0.00';
		$total_round_price_shared = !empty($_REQUEST['total_round_price_shared']) ? $_REQUEST['total_round_price_shared'] : '0.00';

		if (!empty($_REQUEST['workshop_classification']) && count($_REQUEST['workshop_classification']) > 0) {
			$workshopClssification = json_encode($_REQUEST['workshop_classification']);
		} else {
			$workshopClssification = '';
		}

		$cutoff_id 			            = addslashes($_REQUEST['cutoff_id']);
		$registration_classification_id = addslashes($_REQUEST['registration_classification_id']);
		$no_of_night 			        = addslashes($_REQUEST['no_of_night']);


		$sqlCutoff['QUERY'] = "SELECT count(*) COUNT FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
														   WHERE `cutoff_id` = ?
														   	AND `classification_title` = ?
															 AND `status` = ?
													    ORDER BY  id";

		$sqlCutoff['PARAM'][]	=	array('FILD' => 'cutoff_id', 		'DATA' => $cutoff_id, 	'TYP' => 's');
		$sqlCutoff['PARAM'][]	=	array('FILD' => 'classification_title', 		'DATA' => $classification_title, 	'TYP' => 's');
		$sqlCutoff['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
		$resCheckCutoff = $mycms->sql_select($sqlCutoff);

		//echo '<pre>'; print_r($resCheckCutoff[0]['COUNT']);
		if ($resCheckCutoff[0]['COUNT'] > 0) {
			pageRedirection('manage_reg_combo_classification.php', " ", "This cutoff has already exist according to title");
		} else {
			//echo $cutoff_id;
			//exit();



			$sql = array();
			$sql['QUERY'] = "INSERT INTO " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
					                SET  `classification_title`= ?, 
					                     `seat_limit`= ?, 
				                      	 `cutoff_id`= ?, 
				                      	 `registration_classification_id`= ?, 
					                     `residential_hotel_id`= ?,
										 `room_type` =?,
					                     `registration_price`= ?,
					                     `workshop_price`= ?,
					                     `workshop_classification`= ?,
					                     `dinner_price`= ?,
					                     `inclusion_lunch_date`= ?,
					                     `inclusion_dinner_date`= ?,
					                     `inclusion_conference_kit`= ?,
					                     `inclusion_sci_hall`= ?,
					                     `inclusion_exb_area`= ?,
					                     `inclusion_tea_coffee`= ?,
					                     `accommodation_price_individual`= ?,
					                     `accommodation_price_shared`= ?,
					                     `no_of_night`= ?,
					                     `round_of_price_individual`= ?,
					                     `round_of_price_shared`= ?,
					                     `total_price`= ?,
					                     `total_price_shared`= ?,
					                     `total_round_price`= ?,
					                     `total_round_price_shared`= ?,
										 `currency`= ?,
									  	 `sequence_by`= ?,
									  	 `created_by` = ?, 
									  	 `created_ip` = ?, 
									  	 `created_sessionId` = ?,
									  	 `created_dateTime` = ?";
			$sql['PARAM'][]   = array('FILD' => 'classification_title', 'DATA' => $classification_title,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'seat_limit', 'DATA' => $editseatlimit,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'cutoff_id', 'DATA' => $cutoff_id,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'registration_classification_id', 'DATA' => $registration_classification_id,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'residential_hotel_id', 'DATA' => $hotel_id,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'room_type', 'DATA' => $room_type,    'TYP' => 's');

			$sql['PARAM'][]   = array('FILD' => 'registration_price', 'DATA' => $registration_price,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'workshop_price', 'DATA' => $workshop_price,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'workshop_classification', 'DATA' => $workshopClssification,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'dinner_price', 'DATA' => $dinner_price,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'inclusion_lunch_date', 'DATA' => json_encode($inclusion_lunch_date),    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'inclusion_dinner_date', 'DATA' => json_encode($inclusion_dinner_date),    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'inclusion_conference_kit', 'DATA' => json_encode($inclusion_conference_kit),    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'inclusion_sci_hall', 'DATA' => json_encode($inclusion_sci_hall),    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'inclusion_exb_area', 'DATA' => json_encode($inclusion_exb_area),    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'inclusion_tea_coffee', 'DATA' => json_encode($inclusion_tea_coffee),    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'accommodation_price_individual', 'DATA' => $accommodation_price_individual,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'accommodation_price_shared', 'DATA' => $accommodation_price_shared,    'TYP' => 's');

			$sql['PARAM'][]   = array('FILD' => 'no_of_night', 'DATA' => $no_of_night,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'round_of_price_individual', 'DATA' => $round_of_price_individual,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'round_of_price_shared', 'DATA' => $round_of_price_shared,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'total_price', 'DATA' => $total_price,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'total_price_shared', 'DATA' => $total_price_shared,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'total_round_price', 'DATA' => $total_round_price,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'total_round_price_shared', 'DATA' => $total_round_price_shared,    'TYP' => 's');

			$sql['PARAM'][]   = array('FILD' => 'currency', 'DATA' => $currency,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'sequence_by', 'DATA' => $sequence_by,    'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'created_by',        'DATA' => $loggedUserID,                      'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
			$sql['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

			$lastInsertedId   = $mycms->sql_insert($sql, false);

			//$lastInsertedId =45;
			//--------Insert into combo tariff start-----------------------------//

			if (!empty($lastInsertedId)) {

				$sqlClassiExist['QUERY'] = "SELECT id  FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
														   WHERE  `classification_title` = '" . trim($classification_title) . "'
															 AND `status` = 'A'
													    ORDER BY `created_dateTime` asc limit 1";

				$resClassiExist = $mycms->sql_select($sqlClassiExist);
				//print_r($sqlClassiExist);
				//echo '<pre>'; print_r($resClassiExist);
				//echo $resClassiExist[0]['id'];
				//die();

				if (!empty($resClassiExist[0]['id'])) {

					$sqlTariff = array();
					$sqlTariff['QUERY'] = "INSERT INTO " . _DB_TARIFF_COMBO_REGISTRATION_ . " 
								                SET  `tariff_classification_id`= ?, 
								                     `tariff_cutoff_id`= ?, 
													 `currency`= ?,
													 `amount`= ?,
												  	 `created_by` = ?, 
												  	 `created_ip` = ?, 
												  	 `created_sessionId` = ?,
												  	 `created_dateTime` = ?";

					$sqlTariff['PARAM'][]   = array('FILD' => 'tariff_classification_id', 'DATA' => $resClassiExist[0]['id'],    'TYP' => 's');

					$sqlTariff['PARAM'][]   = array('FILD' => 'tariff_cutoff_id', 'DATA' => $cutoff_id,    'TYP' => 's');
					$sqlTariff['PARAM'][]   = array('FILD' => 'currency', 'DATA' => $currency,    'TYP' => 's');

					$sqlTariff['PARAM'][]   = array('FILD' => 'amount', 'DATA' => $total_round_price,    'TYP' => 's');

					$sqlTariff['PARAM'][]   = array('FILD' => 'created_by',        'DATA' => $loggedUserID,                      'TYP' => 's');
					$sqlTariff['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
					$sqlTariff['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
					$sqlTariff['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

					$res  = $mycms->sql_insert($sqlTariff, false);
				}

				if ($hotel_id != '') {
					$dates = array();
					$dCount = 0;
					$packageCheckDate = array();
					$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
															   WHERE `hotel_id` = ?
																 AND `status` = ?
														    ORDER BY  check_in_date";
					$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotel_id, 	'TYP' => 's');
					$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
					$resCheckIns = $mycms->sql_select($packageCheckDate);

					foreach ($resCheckIns as $key => $rowCheckIn) {
						$packageCheckoutDate = array();
						$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
																   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
																  WHERE `hotel_id` = ?
																    AND `status` = ?
																    AND `check_out_date` > ?
															   ORDER BY check_out_date";
						$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotel_id, 	    'TYP' => 's');
						$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
						$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');

						//echo '<pre>'; print_r($packageCheckoutDate);
						$resCheckOut = $mycms->sql_select($packageCheckoutDate);

						foreach ($resCheckOut as $key => $rowCheckOut) {
							$checkinDate 	  =  $rowCheckIn['check_in_date'];
							$checkinId  =  $rowCheckIn['id'];
							$checkoutDate   =  $rowCheckOut['check_out_date'];
							$checkoutId =  $rowCheckOut['id'];
							$dayDiff    =  $rowCheckOut['dayDiff'];

							$amount_individual = $accommodation_price_individual * $rowCheckOut['dayDiff'];
							$amount_shared = $accommodation_price_shared * $rowCheckOut['dayDiff'];
							if ($dayDiff == $no_of_night) {

								$sqlAccoTariff = array();
								$sqlAccoTariff['QUERY'] = "INSERT INTO " . _DB_TARIFF_COMBO_ACCOMODATION_ . " 
									    SET  `hotel_id`= ?, 
									         `classification_id`= ?, 
											 `tariff_cutoff_id`= ?,
											 `checkin_date_id`= ?,
											 `checkout_date_id`= ?,
											 `currency`= ?,
											 `inr_amount_individual`= ?,
											 `inr_amount_shared`= ?,
										  	 `created_by` = ?, 
										  	 `created_ip` = ?, 
										  	 `created_sessionId` = ?,
										  	 `created_dateTime` = ?";

								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'hotel_id', 'DATA' => $hotel_id,    'TYP' => 's');

								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'classification_id', 'DATA' => $lastInsertedId,    'TYP' => 's');

								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'tariff_cutoff_id', 'DATA' => $cutoff_id,    'TYP' => 's');
								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'checkin_date_id', 'DATA' => $checkinId,    'TYP' => 's');
								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'checkout_date_id', 'DATA' => $checkoutId,    'TYP' => 's');

								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'currency', 'DATA' => $currency,    'TYP' => 's');

								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'inr_amount', 'DATA' => $amount_individual,    'TYP' => 's');
								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'inr_amount', 'DATA' => $amount_shared,    'TYP' => 's');

								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_by',        'DATA' => $loggedUserID,                      'TYP' => 's');
								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
								$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

								$mycms->sql_insert($sqlAccoTariff, false);

								$dCount++;
							}
						}
					}
				}
			}


			//--------Insert into combo tariff end-----------------------------//


			pageRedirection('manage_reg_combo_classification.php', 2, "");
		}



		break;

	case 'update':

		$classification_title 	    = addslashes($_REQUEST['classification_title']);
		$editseatlimit 	            = addslashes($_REQUEST['seat_limit_add']);
		$sequence_by                = addslashes($_REQUEST['sequence_by']);
		$type 	                    = addslashes($_REQUEST['type']);
		$room_type 	                = addslashes($_REQUEST['room_type']);
		$currency 	                = addslashes($_REQUEST['currency']);
		$id 			            = addslashes($_REQUEST['id']);

		$cutoff_id 			        = addslashes($_REQUEST['para_cutoff_id']);


		$residential_hotel_id 	= addslashes($_REQUEST['residential_hotel_id']);
		$previous_hotel_id 	= addslashes($_REQUEST['previous_hotel_id']);
		$no_of_night 	= addslashes($_REQUEST['no_of_night']);
		$workshop_price  = !empty($_REQUEST['workshop_price']) ? $_REQUEST['workshop_price'] : '0.00';
		$dinner_price 	 = !empty($_REQUEST['dinner_price']) ? $_REQUEST['dinner_price'] : '0.00';
		$inclusion_lunch_date= $_REQUEST['inclusion_lunch_date'];
		$inclusion_dinner_date= $_REQUEST['inclusion_dinner_date'];
		$inclusion_conference_kit = $_REQUEST['inclusion_conference_kit'];
		$inclusion_sci_hall 	    		 = $_REQUEST['inclusion_sci_hall'];
		$inclusion_exb_area 	    		 = $_REQUEST['inclusion_exb_area'];
		$inclusion_tea_coffee 	    		 = $_REQUEST['inclusion_tea_coffee'];
		$accommodation_price_individual = !empty($_REQUEST['accommodation_price_individual']) ? $_REQUEST['accommodation_price_individual'] : '0.00';
		$accommodation_price_shared = !empty($_REQUEST['accommodation_price_shared']) ? $_REQUEST['accommodation_price_shared'] : '0.00';
		$registration_price = !empty($_REQUEST['registration_price']) ? $_REQUEST['registration_price'] : '0.00';
		$round_of_price_individual = !empty($_REQUEST['round_of_price_individual']) ? $_REQUEST['round_of_price_individual'] : '0.00';
		$round_of_price_shared = !empty($_REQUEST['round_of_price_shared']) ? $_REQUEST['round_of_price_shared'] : '0.00';
		$total_price = !empty($_REQUEST['total_price']) ? $_REQUEST['total_price'] : '0.00';
		$total_price_shared = !empty($_REQUEST['total_price_shared']) ? $_REQUEST['total_price_shared'] : '0.00';
		$total_round_price = !empty($_REQUEST['total_round_price']) ? $_REQUEST['total_round_price'] : '0.00';
		$total_round_price_shared = !empty($_REQUEST['total_round_price_shared']) ? $_REQUEST['total_round_price_shared'] : '0.00';

		if (!empty($_REQUEST['workshop_classification']) && count($_REQUEST['workshop_classification']) > 0) {
			$workshopClssification = json_encode($_REQUEST['workshop_classification']);
		} else {
			$workshopClssification = '';
		}

		$sql1['QUERY'] = "UPDATE " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
				                SET  `classification_title`='" . $classification_title . "', 
				                     `seat_limit`='" . $editseatlimit . "', 
				                      `cutoff_id`='" . $cutoff_id . "', 
				                     `residential_hotel_id`='" . $residential_hotel_id . "',
									 `room_type`='" . $room_type . "',
									 `currency`='" . $currency . "',
								  	 `sequence_by`='" . $sequence_by . "',
								  	 `registration_price`='" . $registration_price . "',
								  	 `workshop_price`='" . $workshop_price . "',
								  	 `workshop_classification`='" . $workshopClssification . "',
								  	 `dinner_price`='" . $dinner_price . "',
								  	 `inclusion_lunch_date`='" . json_encode($inclusion_lunch_date) . "',
								  	 `inclusion_dinner_date`='" . json_encode($inclusion_dinner_date) . "',
									 `inclusion_conference_kit`='" . $inclusion_conference_kit . "',
									 `inclusion_sci_hall`='" . $inclusion_sci_hall . "',
									 `inclusion_exb_area`='" . $inclusion_exb_area . "',
									 `inclusion_tea_coffee`='" . $inclusion_tea_coffee . "',
								  	 `no_of_night`='" . $no_of_night . "',
								  	 `round_of_price_individual`='" . $round_of_price_individual . "',
								  	 `round_of_price_shared`='" . $round_of_price_shared . "',
								  	 `total_price`='" . $total_price . "',
								  	 `total_price_shared`='" . $total_price_shared . "',
								  	 `total_round_price`='" . $total_round_price . "',
								  	 `total_round_price_shared`='" . $total_round_price_shared . "',
								  	 `accommodation_price_individual`='" . $accommodation_price_individual . "',
								  	 `accommodation_price_shared`='" . $accommodation_price_shared . "'
				               WHERE `id`='" . $id . "'";

		$res   = $mycms->sql_update($sql1);

		$sqlComboTariff['QUERY'] = "UPDATE " . _DB_TARIFF_COMBO_REGISTRATION_ . " 
				                SET  `amount`='" . $total_round_price . "'
				                     
				               WHERE `tariff_classification_id`='" . $id . "' AND tariff_cutoff_id='" . $cutoff_id . "'";

		$resComboTariff   = $mycms->sql_update($sqlComboTariff);

		//delete old dates
		$sqlAccoDate = array();
		$sqlAccoDate['QUERY'] = "UPDATE  " . _DB_TARIFF_COMBO_ACCOMODATION_ . " 
									    SET  `status`= 'D' WHERE  `hotel_id`='" . $previous_hotel_id . "'
										AND `classification_id`= '" . $id . "' 
										AND `status`='A'";
		$resAccoDate   = $mycms->sql_update($sqlAccoDate);

		//insert new dates
		if ($residential_hotel_id != '') {
			$dates = array();
			$dCount = 0;
			$packageCheckDate = array();
			$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
															   WHERE `hotel_id` = ?
																 AND `status` = ?
														    ORDER BY  check_in_date";
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $residential_hotel_id, 	'TYP' => 's');
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
			$resCheckIns = $mycms->sql_select($packageCheckDate);

			foreach ($resCheckIns as $key => $rowCheckIn) {
				$packageCheckoutDate = array();
				$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
																   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
																  WHERE `hotel_id` = ?
																    AND `status` = ?
																    AND `check_out_date` > ?
															   ORDER BY check_out_date";
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $residential_hotel_id, 	    'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');

				//echo '<pre>'; print_r($packageCheckoutDate);
				$resCheckOut = $mycms->sql_select($packageCheckoutDate);

				foreach ($resCheckOut as $key => $rowCheckOut) {
					$checkinDate 	  =  $rowCheckIn['check_in_date'];
					$checkinId  =  $rowCheckIn['id'];
					$checkoutDate   =  $rowCheckOut['check_out_date'];
					$checkoutId =  $rowCheckOut['id'];
					$dayDiff    =  $rowCheckOut['dayDiff'];

					$amount_individual = $accommodation_price_individual * $rowCheckOut['dayDiff'];
					$amount_shared = $accommodation_price_shared * $rowCheckOut['dayDiff'];
					if ($dayDiff == $no_of_night) {

						$sqlAccoTariff = array();
						$sqlAccoTariff['QUERY'] = "INSERT INTO " . _DB_TARIFF_COMBO_ACCOMODATION_ . " 
									    SET  `hotel_id`= ?, 
									         `classification_id`= ?, 
											 `tariff_cutoff_id`= ?,
											 `checkin_date_id`= ?,
											 `checkout_date_id`= ?,
											 `currency`= ?,
											 `inr_amount_individual`= ?,
											 `inr_amount_shared`= ?,
										  	 `created_by` = ?, 
										  	 `created_ip` = ?, 
										  	 `created_sessionId` = ?,
										  	 `created_dateTime` = ?";

						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'hotel_id', 'DATA' => $residential_hotel_id,    'TYP' => 's');

						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'classification_id', 'DATA' => $id,    'TYP' => 's');

						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'tariff_cutoff_id', 'DATA' => $cutoff_id,    'TYP' => 's');
						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'checkin_date_id', 'DATA' => $checkinId,    'TYP' => 's');
						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'checkout_date_id', 'DATA' => $checkoutId,    'TYP' => 's');

						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'currency', 'DATA' => $currency,    'TYP' => 's');

						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'inr_amount', 'DATA' => $amount_individual,    'TYP' => 's');
						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'inr_amount', 'DATA' => $amount_shared,    'TYP' => 's');

						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_by',        'DATA' => $loggedUserID,                      'TYP' => 's');
						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
						$sqlAccoTariff['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

						$mycms->sql_insert($sqlAccoTariff, false);

						$dCount++;
					}
				}
			}
		}



		pageRedirection('manage_reg_combo_classification.php', 2, "");
		break;


	case 'getHotelRoomType':

		$hotelId            = $_REQUEST['hotelId'];
		$room_control            = $_REQUEST['room_control'];
?>
		<select name="<?= $room_control ?>" id="<?= $room_control ?>" style='width:80%;' forType="room">
			<option value="">-- Select Room --</option>
			<?php
			getHotelRoomList($hotelId, $room_control);
			?>
		</select>
	<?php
		exit();
		break;

	case 'compose_entry':
		$hotel_id = $_POST['hotel_id'];
		$no_of_night = $_POST['no_of_night'];
		if ($hotel_id != '') {
			$dates = array();
			$dCount = 0;
			$packageCheckDate = array();
			$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
													   WHERE `hotel_id` = ?
														 AND `status` = ?
												    ORDER BY  check_in_date";
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotel_id, 	'TYP' => 's');
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
			$resCheckIns = $mycms->sql_select($packageCheckDate);

			foreach ($resCheckIns as $key => $rowCheckIn) {
				$packageCheckoutDate = array();
				$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
														   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
														  WHERE `hotel_id` = ?
														    AND `status` = ?
														    AND `check_out_date` > ?
													   ORDER BY check_out_date";
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotel_id, 	    'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
				$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


				$resCheckOut = $mycms->sql_select($packageCheckoutDate);
				//echo '<pre>'; print_r($resCheckOut);
				foreach ($resCheckOut as $key => $rowCheckOut) {
					if ($rowCheckOut['dayDiff'] == $no_of_night) {
						$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
						$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
						$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
						$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
						$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

						$dCount++;
					}
				}
			}
			/*$tempCheckIN = array_unique(array_column($dates, 'CHECKIN','CHECKINID'));
				foreach ($tempCheckIN as $key => $value) {
					$dates[$dCount]['CHECKINID']  =  $key;

					$dates[$dCount]['CHECKIN']  =  $value;

					$dCount++;
				}*/

			/*$tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT','CHECKOUTID')); 	
			     foreach ($tempCheckOUT as $key => $value) {
			     	$dates[$dCount]['CHECKOUTID']  =  $key;

					$dates[$dCount]['CHECKOUT']  =  $value;

					$dCount++;
			     }
*/
			//echo '<pre>'; print_r($dates);

			$pDateArr = array();
			foreach ($dates as $k => $vals) {
				$pDateArr[] = '"checkin":"' . $vals['CHECKIN'] . '","checkinId":"' . $vals['CHECKINID'] . '", "checkout":"' . $vals['CHECKOUT'] . '","checkoutId":"' . $vals['CHECKOUTID'] . '", "days":"' . $vals['DAYDIFF'] . '"';
			}
			//echo implode(",",$pDateArr);
			echo json_encode($dates);
			//return $dates;
			exit;
		}
		//pageRedirection('manage_reg_combo_classification.php', 2, "");
		break;

		/************************ACTIVE**********************/
	case 'Active':
		Active($mycms, $cfg);
		pageRedirection('manage_reg_combo_classification.php', 2, "");
		break;
		/************************ACTIVE**********************/
	case 'Inactive':
		Inactive($mycms, $cfg);
		pageRedirection('manage_reg_combo_classification.php', 2, "");
		break;

	case 'Remove':
		$id 			            = addslashes($_REQUEST['id']);

		$cutoff_id 			        = addslashes($_REQUEST['para_cutoff_id']);
		$sqlRemoveHotel['QUERY']      = "UPDATE " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
							                   SET `status` = 'D'
							                 WHERE `id`= '" . $id . "' ";

		$mycms->sql_update($sqlRemoveHotel);


		$sqlComboTariff['QUERY'] = "UPDATE " . _DB_TARIFF_COMBO_REGISTRATION_ . " 
				                SET `status` = 'D'
				                     
				               WHERE `tariff_classification_id`='" . $id . "' AND tariff_cutoff_id='" . $cutoff_id . "'";

		$resComboTariff   = $mycms->sql_update($sqlComboTariff);


		pageRedirection('manage_reg_combo_classification.php', 2, "");
		exit();
		break;


	case 'updatetariff':
		updateTariff($mycms, $cfg);
		pageRedirection('registration_combo_tariff.php', 2, "");
		break;
}


function getHotelRoomList($hotelId, $room_name)
{
	global $cfg, $mycms;
	$sqlAcc = array();
	$sqlAcc['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='room'";

	$resRooms = $mycms->sql_select($sqlAcc, false);
	?>
	<!-- <option value="">-- Choose Room --</option> -->
	<?php
	if ($resRooms) {
		foreach ($resRooms as $key => $rowRoom) {
	?>
			<option value="<?= $rowRoom['accessories_name'] ?>" <?= ($rowRoom['accessories_name'] == $room_name) ? "selected" : "" ?>><?= $rowRoom['accessories_name'] ?></option>
<?php
		}
	}
}

function updateTariff($mycms, $cfg)
{
	global $loggedUserID;

	//echo'<pre>';print_r($_REQUEST);echo'</pre>';die();		

	$classification_id              = addslashes($_REQUEST['classification_id']);
	$currencyArr			        = $_REQUEST['currency'];

	// TARIFF CLASSIFICATION AMOUNT UPDATION
	$tariff_cutoff_id               = $_REQUEST['tariff_cutoff_id'];

	foreach ($tariff_cutoff_id as $key => $amount) {
		$currency  = $currencyArr[$key];
		if ($amount != "") {

			$sqlFetchTariffAmount			=	array();
			$sqlFetchTariffAmount['QUERY']	=	"SELECT * 
														FROM " . _DB_TARIFF_COMBO_REGISTRATION_ . "
														WHERE `tariff_classification_id` 	= 	?
														  AND `tariff_cutoff_id`			=   ? ";

			$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_classification_id', 'DATA' => $classification_id, 	'TYP' => 's');
			$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 		 'DATA' => $key, 					'TYP' => 's');

			$resultFetchTariffAmount  	= $mycms->sql_select($sqlFetchTariffAmount);
			$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);

			if ($maxRowsTariffAmount > 0) {
				$sql 	=	array();
				$sql['QUERY'] = "UPDATE " . _DB_TARIFF_COMBO_REGISTRATION_ . " 
										SET `amount`		 		    = ?,
										    `currency`				    = ?,
										    `modified_by`			    = ?,
										    `modified_ip` 	 	 	    = ?,
										    `modified_sessionId` 	    = ?,
										    `modified_dateTime` 	    = ? 
									  WHERE `tariff_classification_id`  = ? 
									    AND `tariff_cutoff_id` 			= ? ";
				$sql['PARAM'][]	=	array('FILD' => 'amount', 					'DATA' => $amount, 				  'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'currency', 				'DATA' => $currency, 		          'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			'DATA' => $loggedUserID,       	  'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			'DATA' => $_SERVER['REMOTE_ADDR'],	  'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId', 		'DATA' => session_id(), 			  'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 		'DATA' => date('Y-m-d H:i:s'), 	  'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'tariff_classification_id', 'DATA' => $classification_id, 		   'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 		'DATA' => $key, 					  'TYP' => 's');
				$mycms->sql_update($sql);
			} else {

				$sqlInsertTariffAmount	=	array();
				$sqlInsertTariffAmount['QUERY']	= "INSERT INTO " . _DB_TARIFF_COMBO_REGISTRATION_ . "
															  SET  `tariff_classification_id`   = ?,
																	`tariff_cutoff_id` 			= ?,
																	`amount` 			        = ?,
																	`currency`                  = ?, 
																    `status`					= ?,
																	`created_by` 				= ?,
																	`created_ip` 				= ?,
																	`created_sessionId`			= ?,
																	`created_dateTime`			= ?";

				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_classification_id',       'DATA' => $classification_id, 						        'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 			   'DATA' => $key, 						   			 'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'amount', 		                   'DATA' => $amount, 			  						  'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'currency', 		               'DATA' => $currency, 			    				   'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'status', 		                   'DATA' => 'A', 			   							  'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_by', 			           'DATA' => $loggedUserID, 		           				 'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_ip', 	                   'DATA' => $_SERVER['REMOTE_ADDR'], 					 'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_sessionId', 			   'DATA' => session_id(), 							    'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_dateTime', 			   'DATA' => date('Y-m-d H:i:s'), 							'TYP' => 's');

				$mycms->sql_insert($sqlInsertTariffAmount);
			}
		}
	}
}

/*================ ACTIVE WORKSHOP ==================*/
function Active($mycms, $cfg)
{
	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
						SET `status`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'A', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	pageRedirection("manage_reg_combo_classification.php", 2, "");
	exit();
}
/*================ INACTIVE WORKSHOP =================*/
function Inactive($mycms, $cfg)
{

	$sql['QUERY'] = "UPDATE " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
						SET `status`	 = ?
					  WHERE `id`		 = ? ";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'I', 					'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						'DATA' => $_REQUEST['id'], 		'TYP' => 's');

	$mycms->sql_update($sql);
	pageRedirection("manage_reg_combo_classification.php", 2, "");
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
?>