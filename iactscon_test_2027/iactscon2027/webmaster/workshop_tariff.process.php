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
		case'update':
		
			updateTarrif($mycms,$cfg);			
	        echo '<script>window.location.href="system_master.php#workshop";</script>';
			exit();
			break;
		case 'getTariffAmountForOnlyworkshop':
			// Handle AJAX request to fetch tariff amounts
			$workshopId = isset($_POST['workshop_id']) ? $_POST['workshop_id'] : 0;
			$classificationId = isset($_POST['classification_id']) ? $_POST['classification_id'] : 0;
			
			$response = array();
			
			if ($workshopId > 0 && $classificationId > 0) {
				// Get all cutoff IDs
				$sqlCutoff = array();
				$sqlCutoff['QUERY'] = "SELECT id FROM " . _DB_WORKSHOP_CUTOFF_ . " WHERE status = 'A' ORDER BY cutoff_sequence ASC";
				$resCutoff = $mycms->sql_select($sqlCutoff);
				
				foreach ($resCutoff as $cutoff) {
					$cutoffId = $cutoff['id'];
					
					// Fetch tariff for this workshop, classification, and cutoff
					$sqlTariff = array();
					$sqlTariff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_WORKSHOP_ . " 
										WHERE `workshop_id` = ? 
										AND `tariff_cutoff_id` = ?
										AND `registration_classification_id` = ?";
					$sqlTariff['PARAM'][] = array('FILD' => 'workshop_id', 'DATA' => $workshopId, 'TYP' => 's');
					$sqlTariff['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $cutoffId, 'TYP' => 's');
					$sqlTariff['PARAM'][] = array('FILD' => 'registration_classification_id', 'DATA' => $classificationId, 'TYP' => 's');
					$resTariff = $mycms->sql_select($sqlTariff);
					
					if (!empty($resTariff)) {
						$response[$cutoffId] = array(
							'inr_amount' => $resTariff[0]['inr_amount'],
							'usd_amount' => $resTariff[0]['usd_amount']
						);
					} else {
						$response[$cutoffId] = array(
							'inr_amount' => '0.00',
							'usd_amount' => '0.00'
						);
					}
				}
			}
			
			// Return JSON response
			header('Content-Type: application/json');
			echo json_encode($response);
			exit();
			break;
	}
	
	
	function updateTarrif($mycms,$cfg)
	{
		$loggedUserID = $mycms->getLoggedUserId();
		$workshop_classification_id         = addslashes($_REQUEST['workshop_classification_id']);
		$registration_classification_id     = addslashes($_REQUEST['registration_classification_id']);
		$tariff_inr_edit         			= $_REQUEST['tariff_inr_cutoff_id_edit'];
		$tariff_usd_edit        			= $_REQUEST['tariff_usd_cutoff_id_edit'];
		
		$currencyArr       					= $_REQUEST['currency'];
	    $tariff_cutoff_id                   = $_REQUEST['tariff_cutoff_id_edit']; 			

	    $classification_ids                   = $_REQUEST['classification_ids']; 			
	    // echo "<pre>";
		// print_r($_REQUEST);
		// die();
        if($_REQUEST['workshopType']== 'Onlyworkshop'){
				if($currency==''){
			$currency = 'INR';
		}
		    $sql = array();
			$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CLASSIFICATION_ . " 
						    SET `commonTariff`	 = ?,
							  	`modified_by`= ?,
							  	`modified_ip`= ?,
							  	`modified_sessionId`= ?,
							  	`modified_dateTime`= ?
								WHERE `id`		 = ?";
			$sql['PARAM'][]	=	array('FILD' => 'commonTariff', 					 'DATA' => $_REQUEST['commonTariff'], 							'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			 'DATA' => $loggedUserID, 				        'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			 'DATA' => $_SERVER['REMOTE_ADDR'], 				        'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId', 		  'DATA' => session_id(), 				        'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 		  'DATA' => date('Y-m-d H:i:s'), 				        'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id', 						 'DATA' => $workshop_classification_id, 				'TYP' => 's');
			$mycms->sql_update($sql);
			if($_REQUEST['commonTariff']=='N'){
				foreach($classification_ids as $classification_key=>$classification_id)
				{
				
					$tariff_inr_edit_only         			= $_REQUEST['tariff_inr_cutoff_id_edit'][$classification_id];
					$tariff_usd_edit_only         			= $_REQUEST['tariff_usd_cutoff_id_edit'][$classification_id];
					$tariff_cutoff_id_only                   = $_REQUEST['tariff_cutoff_id_edit'][$classification_id]; 			
					$currencyArr_only       					= $_REQUEST['currency'][$classification_id];
					foreach($tariff_cutoff_id_only as $key=>$cutoff_id){
						
						$inr_amount = $tariff_inr_edit_only[$cutoff_id];
						$usd_amount = $tariff_usd_edit_only[$cutoff_id];
						
						$currency   = $currencyArr_only[$cutoff_id];
						if($currency==''){
								$currency = 'INR';
						}
						$sqlFetchTariffAmount	=	array();
						$sqlFetchTariffAmount['QUERY']     =   "SELECT * 
																FROM "._DB_TARIFF_WORKSHOP_." 
																WHERE `workshop_id` = ? 
																AND `tariff_cutoff_id` = ?
																AND `registration_classification_id` = ? 
																AND `OnlyWorkshop_clssId` = ? ";
						$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id',  	 				'DATA' => $workshop_classification_id,  'TYP' => 's');	
						$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 				'DATA' => $cutoff_id,  'TYP' => 's');	
						$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id',   'DATA' => 0,  'TYP' => 's');	
						$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId',   'DATA' => $classification_id,  'TYP' => 's');	
					
						$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
						$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);    
				
						if($maxRowsTariffAmount > 0)
						{
							$sqlUpdateTariffAmount	=	array();
							$sqlUpdateTariffAmount['QUERY']  = "UPDATE "._DB_TARIFF_WORKSHOP_."
																SET `inr_amount` 		   = ?,
																	`usd_amount`		   = ?,
																	`curency` 		   = ?,
																	`modified_by` 	   = ?,
																	`modified_ip`		   = ?,
																	`modified_sessionId` = ?,
																	`modified_dateTime`  = ? 
																WHERE `workshop_id`		   = ? 
																AND `registration_classification_id` = ?
																AND `tariff_cutoff_id`   = ? 
																AND `OnlyWorkshop_clssId`   = ? ";
							
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => $inr_amount,  	'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		 			   'DATA' => $usd_amount,  	'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'curency',    		  			   'DATA' => $currency,  	'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_by',		  			   'DATA' => $loggedUserID,   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_ip',		  			   'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_sessionId',			   'DATA' => session_id(),   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 	  			   'DATA' => $workshop_classification_id,   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => 0,   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 			   'DATA' => $cutoff_id,   'TYP' => 's');	
							$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId', 			   'DATA' => $classification_id,   'TYP' => 's');	

							$mycms->sql_update($sqlUpdateTariffAmount);
						}
						else
						{
							$sqlInsertTariffAmount = array();
							$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_TARIFF_WORKSHOP_."
																SET `workshop_id` = ? ,
																	`registration_classification_id` = ? ,
																	`tariff_cutoff_id` = ? ,
																	`OnlyWorkshop_clssId`   = ?,
																	`inr_amount` = ? , 
																	`usd_amount` = ? ,
																	`curency` = ? ,
																	`status` = ? ,
																	`created_by` = ?,
																	`created_ip` = ? ,
																	`created_sessionId` = ?,
																	`created_dateTime` = ? ";
							
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 		  		   'DATA' => $workshop_classification_id,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => 0,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 		  	   'DATA' => $cutoff_id,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId', 		  	   'DATA' => $classification_id,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => $inr_amount,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		  			   'DATA' => $usd_amount,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'curency', 		  			   'DATA' => $currency,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'status', 		  			  	   'DATA' => 'A',  			'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_by', 		  			   'DATA' => $loggedUserID,  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_ip', 		  			   'DATA' => $_SERVER['REMOTE_ADDR'],  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_sessionId',  			   'DATA' => session_id(),  	'TYP' => 's');	
							$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),  	'TYP' => 's');	
							$mycms->sql_insert($sqlInsertTariffAmount);
							// echo "<pre>";

							// print_r($sqlInsertTariffAmount);
							// die();
						}
					}
				}
			}else{
					$tariff_inr_edit_only         			= $_REQUEST['tariff_inr_cutoff_id_edit'][0];
					$tariff_usd_edit_only         			= $_REQUEST['tariff_usd_cutoff_id_edit'][0];
					$tariff_cutoff_id_only                   = $_REQUEST['tariff_cutoff_id_edit'][0]; 			
					$currencyArr_only       					= $_REQUEST['currency'][0];
				foreach($tariff_cutoff_id_only as $key=>$cutoff_id){
					
						$inr_amount = $tariff_inr_edit_only[$cutoff_id];
					$usd_amount = $tariff_usd_edit_only[$cutoff_id];
					
					$currency   = $currencyArr_only[$cutoff_id];
					if($currency==''){
							$currency = 'INR';
					}
					$sqlFetchTariffAmount	=	array();
					$sqlFetchTariffAmount['QUERY']     =   "SELECT * 
															FROM "._DB_TARIFF_WORKSHOP_." 
															WHERE `workshop_id` = ? 
															AND `tariff_cutoff_id` = ?
															AND `registration_classification_id` = ? 
															AND `OnlyWorkshop_clssId` = ? ";
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id',  	 				'DATA' => $workshop_classification_id,  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 				'DATA' => $cutoff_id,  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id',   'DATA' => 0,  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId',   'DATA' => 0,  'TYP' => 's');	
					
					$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
					$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);    
			
					if($maxRowsTariffAmount > 0)
					{
						$sqlUpdateTariffAmount	=	array();
						$sqlUpdateTariffAmount['QUERY']  = "UPDATE "._DB_TARIFF_WORKSHOP_."
															SET `inr_amount` 		   = ?,
																`usd_amount`		   = ?,
																`curency` 		   = ?,
																`modified_by` 	   = ?,
																`modified_ip`		   = ?,
																`modified_sessionId` = ?,
																`modified_dateTime`  = ? 
															WHERE `workshop_id`		   = ? 
															AND `registration_classification_id` = ?
															AND `tariff_cutoff_id`   = ? 
															AND `OnlyWorkshop_clssId`   = ? ";
						
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => $inr_amount,  	'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		 			   'DATA' => $usd_amount,  	'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'curency',    		  			   'DATA' => $currency,  	'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_by',		  			   'DATA' => $loggedUserID,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_ip',		  			   'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_sessionId',			   'DATA' => session_id(),   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 	  			   'DATA' => $workshop_classification_id,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => 0,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 			   'DATA' => $cutoff_id,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId', 			   'DATA' => 0,   'TYP' => 's');	

						$mycms->sql_update($sqlUpdateTariffAmount);
					}
					else
					{
						$sqlInsertTariffAmount = array();
						$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_TARIFF_WORKSHOP_."
															SET `workshop_id` = ? ,
																`registration_classification_id` = ? ,
																`tariff_cutoff_id` = ? ,
																`OnlyWorkshop_clssId`   = ?,
																`inr_amount` = ? , 
																`usd_amount` = ? ,
																`curency` = ? ,
																`status` = ? ,
																`created_by` = ?,
																`created_ip` = ? ,
																`created_sessionId` = ?,
																`created_dateTime` = ? ";
						
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 		  		   'DATA' => $workshop_classification_id,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => 0,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 		  	   'DATA' => $cutoff_id,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId', 		  	   'DATA' => 0,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => $inr_amount,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		  			   'DATA' => $usd_amount,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'curency', 		  			   'DATA' => $currency,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'status', 		  			  	   'DATA' => 'A',  			'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_by', 		  			   'DATA' => $loggedUserID,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_ip', 		  			   'DATA' => $_SERVER['REMOTE_ADDR'],  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_sessionId',  			   'DATA' => session_id(),  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),  	'TYP' => 's');	
						$mycms->sql_insert($sqlInsertTariffAmount);
						// echo "<pre>";

						// print_r($sqlInsertTariffAmount);
						// die();
					}
				}
			}
			
		}else{
			foreach($tariff_cutoff_id as $key=>$cutoff_id)
			{
				$inr_amount = $tariff_inr_edit[$cutoff_id];
				$usd_amount = $tariff_usd_edit[$cutoff_id];
				
				$currency   = $currencyArr[$cutoff_id];
			
				 if($inr_amount!="" && $usd_amount!="")
				{
					$sqlFetchTariffAmount	=	array();
					$sqlFetchTariffAmount['QUERY']     =   "SELECT * 
															FROM "._DB_TARIFF_WORKSHOP_." 
															WHERE `workshop_id` = ? 
															AND `tariff_cutoff_id` = ?
															AND `registration_classification_id` = ? ";
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id',  	 				'DATA' => $workshop_classification_id,  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 				'DATA' => $cutoff_id,  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id',   'DATA' => $registration_classification_id,  'TYP' => 's');	
					
					$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
					$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);    
			
					if($maxRowsTariffAmount > 0)
					{
						$sqlUpdateTariffAmount	=	array();
						$sqlUpdateTariffAmount['QUERY']  = "UPDATE "._DB_TARIFF_WORKSHOP_."
															SET `inr_amount` 		   = ?,
																`usd_amount`		   = ?,
																`curency` 		   = ?,
																`modified_by` 	   = ?,
																`modified_ip`		   = ?,
																`modified_sessionId` = ?,
																`modified_dateTime`  = ? 
															WHERE `workshop_id`		   = ? 
															AND `registration_classification_id` = ?
															AND `tariff_cutoff_id`   = ? ";
						
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => $inr_amount,  	'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		 			   'DATA' => $usd_amount,  	'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'curency',    		  			   'DATA' => $currency,  	'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_by',		  			   'DATA' => $loggedUserID,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_ip',		  			   'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_sessionId',			   'DATA' => session_id(),   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'modified_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 	  			   'DATA' => $workshop_classification_id,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => $registration_classification_id,   'TYP' => 's');	
						$sqlUpdateTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 			   'DATA' => $cutoff_id,   'TYP' => 's');	
							
						$mycms->sql_update($sqlUpdateTariffAmount);
					}
					else
					{
						$sqlInsertTariffAmount = array();
						$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_TARIFF_WORKSHOP_."
															SET `workshop_id` = ? ,
																`registration_classification_id` = ? ,
																`tariff_cutoff_id` = ? ,
																`inr_amount` = ? , 
																`usd_amount` = ? ,
																`curency` = ? ,
																`status` = ? ,
																`created_by` = ?,
																`created_ip` = ? ,
																`created_sessionId` = ?,
																`created_dateTime` = ? ";
						
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 		  		   'DATA' => $workshop_classification_id,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => $registration_classification_id,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 		  	   'DATA' => $cutoff_id,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => $inr_amount,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		  			   'DATA' => $usd_amount,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'curency', 		  			   'DATA' => $currency,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'status', 		  			  	   'DATA' => 'A',  			'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_by', 		  			   'DATA' => $loggedUserID,  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_ip', 		  			   'DATA' => $_SERVER['REMOTE_ADDR'],  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_sessionId',  			   'DATA' => session_id(),  	'TYP' => 's');	
						$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),  	'TYP' => 's');	
						$mycms->sql_insert($sqlInsertTariffAmount);
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
