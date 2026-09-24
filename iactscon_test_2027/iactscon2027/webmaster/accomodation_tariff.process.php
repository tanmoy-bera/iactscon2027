<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
		case'search_classification':
			
			pageRedirection('registration_tariff.php', 5, "");
			exit();
			break;
			
		/***************************************************************************/
		/*                      UPDATE TARIFF CLASSIFICATION                       */
		/***************************************************************************/
		case'edit':
			updateTariff($mycms,$cfg);
					echo '<script>window.location.href="system_master.php#accommodation";</script>';

			break;
	}
	
	
	function updateTariff($mycms, $cfg)
    {
		global $loggedUserID;

		$roomId               = $_REQUEST['roomId'];
		$hotelId              = $_REQUEST['hotel_id'];
		$tariffArr          = $_REQUEST['tariff'];
		$rates_AdaterrAll  = $_REQUEST['rates'];   // nested arrays keyed by tarrif
        // echo "<pre>";
		// print_r($_REQUEST);
		// 	die();
        foreach ($tariffArr as $cutoffId => $packages) {
		  $tarrifArr  = $rates_AdaterrAll[$cutoffId];   // nested arrays keyed by tarrif
 
            foreach ($packages as $packageId => $package) {
				if($package['inr']!=''){
					$hotelId = $package['hotel_id'];
					// $roomId = $package['room_id'];
					$roomId = !empty($package['room_id']) ? $package['room_id'] : (int)0;
					$inrAmount = $package['inr'];
					$usdAmount = $package['usd'];
					// FETCH existing tariff amount
					$sqlFetchTariffAmount = array();
					$sqlFetchTariffAmount['QUERY'] = "SELECT * 
													FROM " . _DB_TARIFF_ACCOMMODATION_ . " 
													WHERE `package_id` = ? 
														AND `hotel_id` = ? 
														AND `tariff_cutoff_id` = ? 
														AND `roomTypeId` = ? 
														AND `status` = ?";
					$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $packageId, 'TYP' => 's'];
					$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
					$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
					$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];
					$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'];
					$resultFetchTariffAmount = $mycms->sql_select($sqlFetchTariffAmount);
					$maxRowsTariffAmount = $mycms->sql_numrows($resultFetchTariffAmount);
                    // echo "<pre>";
					// print_r($sqlFetchTariffAmount);
					// FETCH existing package amount
					$sqlFetchPackageAmount = array();
					$sqlFetchPackageAmount['QUERY'] = "SELECT * 
													FROM " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . " 
													WHERE `package_id` = ? 
														AND `hotel_id` = ? 
														AND `tariff_cutoff_id` = ? 
														AND `roomTypeId` = ? 
														AND `status` = ?";
					$sqlFetchPackageAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $packageId, 'TYP' => 's'];
					$sqlFetchPackageAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
					$sqlFetchPackageAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
					$sqlFetchPackageAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];
					$sqlFetchPackageAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'];
					$resultFetchPackageAmount = $mycms->sql_select($sqlFetchPackageAmount);
					$maxRowsPackageAmount = $mycms->sql_numrows($resultFetchPackageAmount);

					// UPDATE old tariff if exists
					if ($maxRowsTariffAmount > 0) {
						$sqlUpdateTariffAmount = array();
						$sqlUpdateTariffAmount['QUERY'] = "UPDATE " . _DB_TARIFF_ACCOMMODATION_ . "
														SET `status` = ?, `type` = ?, `modified_ip` = ?, `modified_dateTime` = ?
														WHERE `package_id` = ? AND `hotel_id` = ? AND `tariff_cutoff_id` = ? AND `roomTypeId` = ?";
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'D', 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'type', 'DATA' => 'old', 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $packageId, 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
						$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];

						$mycms->sql_update($sqlUpdateTariffAmount);
					}

					// UPDATE or INSERT package amount
					if ($maxRowsPackageAmount > 0) {
						$sqlUpdatePackageAmount = array();
						$sqlUpdatePackageAmount['QUERY'] = "UPDATE " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . " 
														SET `inr_amount` = ? 
														WHERE `hotel_id` = ? AND `package_id` = ? AND `tariff_cutoff_id` = ? AND `roomTypeId` = ?";
						$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'inr_amount', 'DATA' => $inrAmount, 'TYP' => 's'];
						$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
						$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $packageId, 'TYP' => 's'];
						$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
						$sqlUpdatePackageAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];
						$mycms->sql_update($sqlUpdatePackageAmount);
					} else {
						$sqlInsertPackageAmount = array();
						$sqlInsertPackageAmount['QUERY'] = "INSERT INTO " . _DB_ACCOMMODATION_PACKAGE_PRICE_ . " 
														SET `hotel_id` = ?, `package_id` = ?, `tariff_cutoff_id` = ?, `inr_amount` = ?, `roomTypeId` = ?,`status` = 'A'";
						$sqlInsertPackageAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
						$sqlInsertPackageAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $packageId, 'TYP' => 's'];
						$sqlInsertPackageAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
						$sqlInsertPackageAmount['PARAM'][] = ['FILD' => 'inr_amount', 'DATA' => $inrAmount, 'TYP' => 's'];
						$sqlInsertPackageAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];

						$mycms->sql_insert($sqlInsertPackageAmount);
					}
			    }
			}
			// LOOP over checkin dates for this cutoff
			foreach ($tarrifArr as $check_inkey => $check_In_dateArr) {

				foreach ($check_In_dateArr as $check_inId => $dateArr) {

                    $check_in = $dateArr['checkin_date_id'];
                    $check_out = $dateArr['checkout_date_id'];
                   
				 	foreach ($dateArr['packages'] as $package_Id => $packageAmt) {
                       	if($package['inr']!=''){
							$inrAmount = $packageAmt['inr'] ?? "0.00";
							$usdAmount = $packageAmt['usd'] ?? "0.00";

							// CHECK if tariff record exists
							$sqlFetchTariffAmount = array();
							$sqlFetchTariffAmount['QUERY'] = "SELECT * 
															FROM " . _DB_TARIFF_ACCOMMODATION_ . " 
															WHERE `package_id` = ? AND `hotel_id` = ? AND `tariff_cutoff_id` = ? 
																AND `status` = ? AND `checkin_date_id` = ? AND `checkout_date_id` = ? AND `roomTypeId` = ?";
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $package_Id, 'TYP' => 's'];
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'];
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'checkin_date_id', 'DATA' => $check_in, 'TYP' => 's'];
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'checkout_date_id', 'DATA' => $check_out, 'TYP' => 's'];
							$sqlFetchTariffAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];

							$resultFetchTariffAmount = $mycms->sql_select($sqlFetchTariffAmount);
							$maxRowsTariffAmount = $mycms->sql_numrows($resultFetchTariffAmount);

							if ($maxRowsTariffAmount > 0) {
								// UPDATE existing tariff
								$sqlUpdateTariffAmount = array();
								$sqlUpdateTariffAmount['QUERY'] = "UPDATE " . _DB_TARIFF_ACCOMMODATION_ . "
																SET `inr_amount` = ?, `usd_amount` = ?, `modified_by` = ?, 
																	`modified_ip` = ?, `modified_sessionId` = ?, `modified_dateTime` = ?, `type` = ?
																WHERE `package_id` = ? AND `hotel_id` = ? AND `tariff_cutoff_id` = ? 
																	AND `checkin_date_id` = ? AND `checkout_date_id` = ? AND `roomTypeId` = ?";
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'inr_amount', 'DATA' => $inrAmount, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'usd_amount', 'DATA' => $usdAmount, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_by', 'DATA' => $loggedUserID, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_sessionId', 'DATA' => session_id(), 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'modified_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'type', 'DATA' => 'new', 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $package_Id, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'checkin_date_id', 'DATA' => $check_in, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'checkout_date_id', 'DATA' => $check_out, 'TYP' => 's'];
								$sqlUpdateTariffAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];

								$mycms->sql_update($sqlUpdateTariffAmount);
							} else {
								// INSERT new tariff
								$sqlInsertTariffAmount = array();
								$sqlInsertTariffAmount['QUERY'] = "INSERT INTO " . _DB_TARIFF_ACCOMMODATION_ . " 
																SET `package_id` = ?, `tariff_cutoff_id` = ?, `inr_amount` = ?, `usd_amount` = ?, 
																	`hotel_id` = ?, `checkin_date_id` = ?, `checkout_date_id` = ?, `status` = ?, 
																	`created_ip` = ?, `created_by` = ?, `created_sessionId` = ?, `created_dateTime` = ?, `type` = ? , `roomTypeId` = ?";
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'package_id', 'DATA' => $package_Id, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'inr_amount', 'DATA' => $inrAmount, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'usd_amount', 'DATA' => $usdAmount, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'checkin_date_id', 'DATA' => $check_in, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'checkout_date_id', 'DATA' => $check_out, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'created_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'created_by', 'DATA' => $loggedUserID, 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'created_sessionId', 'DATA' => session_id(), 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'created_dateTime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'type', 'DATA' => 'new', 'TYP' => 's'];
								$sqlInsertTariffAmount['PARAM'][] = ['FILD' => 'roomTypeId', 'DATA' => $roomId, 'TYP' => 's'];

								$mycms->sql_insert($sqlInsertTariffAmount);
							}
						}
					}
				}
			}

		}
			

	}

	/******************************************************************************/
	/*                                 UTILITY METHOD                             */
	/******************************************************************************/
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		       = "_pgn_";
		$pageKeyVal                    		       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 		       = "";
		$searchArray                   		       = array();
		
		$searchArray[$pageKey]         		       = $pageKeyVal;
		$searchArray['src_tariff_classification']  = trim($_REQUEST['src_tariff_classification']);
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$searchString .= "&".$searchKey."=".$searchVal;
			}
		}
		
		$mycms->redirect($fileName."?m=".$messageCode.$additionalString.$searchString);
	}
?>
