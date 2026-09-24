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
			pageRedirection('accomodation_tariff.php', 2, "");
			break;
	}
	
	
	function updateTariff($mycms,$cfg)
	{
		global $loggedUserID ;
		
			
		$cutOff   				    = $_REQUEST['cutoff'];
		$package_idArr         		= $_REQUEST['package_id'];
		$check_in_dateArr           = $_REQUEST['checkin_date'];
		$check_out_dateArr          = $_REQUEST['checkout_date'];
		$hotelArr                   = $_REQUEST['hotel_id'];		
		$inrAmountArr  				= $_REQUEST['INRAmt'];
		$usdAmountArr				= $_REQUEST['USDAmt'];
		///echo '<pre>'; print_r($_REQUEST);
		//die();
				 /*echo '<pre>';
				 print_r($_REQUEST);
				 echo '<pre>';
				exit('Kalyan');*/
				$sqlFetchTariffAmount 	=		array();
				$sqlFetchTariffAmount['QUERY']     = "SELECT * 
						 								FROM "._DB_TARIFF_ACCOMMODATION_." 
													   WHERE `package_id` 			= ?
														 AND `hotel_id`				= ?
														 AND `tariff_cutoff_id`		= ?
														 AND `status`				= ?";
				
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'package_id' , 				'DATA' => $package_idArr , 	  'TYP' => 's');	
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'hotel_id' , 				'DATA' => $hotelArr , 	      'TYP' => 's');	
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' , 		'DATA' => $cutOff , 	      'TYP' => 's');	
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 					'DATA' => 'A' , 	      	  'TYP' => 's');	
				//echo '<br><br>'.nl2br($sqlFetchTariffAmount);
				
				$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
				$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);  


				$sqlFetchPackageAmount 	=		array();
				$sqlFetchPackageAmount['QUERY']     = "SELECT * 
						 								FROM "._DB_ACCOMMODATION_PACKAGE_PRICE_." 
													   WHERE `package_id` 			= ?
														 AND `hotel_id`				= ?
														 AND `tariff_cutoff_id`		= ?
														 AND `status`				= ?";
				
				$sqlFetchPackageAmount['PARAM'][]	=	array('FILD' => 'package_id' , 				'DATA' => $package_idArr , 	  'TYP' => 's');	
				$sqlFetchPackageAmount['PARAM'][]	=	array('FILD' => 'hotel_id' , 				'DATA' => $hotelArr , 	      'TYP' => 's');	
				$sqlFetchPackageAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' , 		'DATA' => $cutOff , 	      'TYP' => 's');	
				$sqlFetchPackageAmount['PARAM'][]	=	array('FILD' => 'status' , 					'DATA' => 'A' , 	      	  'TYP' => 's');

				$resultFetchPackageAmount  = $mycms->sql_select($sqlFetchPackageAmount);
				 $maxRowsPackageAmount      = $mycms->sql_numrows($resultFetchPackageAmount);
				

				if($maxRowsTariffAmount > 0)
				{
					$sqlUpdateTariffAmount 		     =		array();
					$sqlUpdateTariffAmount['QUERY']  = "UPDATE "._DB_TARIFF_ACCOMMODATION_."
														  SET `status` 				= ?,
														  		`type`				= ?,
														  		`modified_ip`		= ?,
														  		`modified_dateTime` = ?
														WHERE `package_id` 			= ?
														  AND `hotel_id`	        = ?
														  AND `tariff_cutoff_id`	= ?"; 


														  //AND `status` 				= ?
					
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'D' , 	  			 'TYP' => 's');	
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'type' , 			'DATA' => 'old' , 	  			 'TYP' => 's');	
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_ip' , 			'DATA' => $_SERVER['REMOTE_ADDR'] , 	  			 'TYP' => 's');	
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 			'DATA' => date('Y-m-d H:i:s') , 	  			 'TYP' => 's');	
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $package_idArr , 	     'TYP' => 's');	  
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelArr , 	         'TYP' => 's');	
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $cutOff , 	      	 'TYP' => 's');								  
					//$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 	      	     'TYP' => 's');								  
					//echo '<br><br>'.nl2br($sqlUpdateTariffAmount);
					 				
					$mycms->sql_update($sqlUpdateTariffAmount);
				}

			 if($maxRowsPackageAmount>0)
			 {
			 	$sqlUpdatePackageAmount 		     =		array();

				$sqlUpdatePackageAmount['QUERY']  = "UPDATE "._DB_ACCOMMODATION_PACKAGE_PRICE_."
																   SET `inr_amount` = '".$_REQUEST['rate_per_night_inr']."' WHERE
																   `hotel_id` = '".$_REQUEST['hotel_id']."' AND
																	   `package_id` = '".$_REQUEST['package_id']."' AND
																	   `tariff_cutoff_id` = '".$cutOff."'";
																	  		   
						//$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $_REQUEST['hotel_id'] ,   'TYP' => 's');
						//$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $_REQUEST['package_id'], 	  'TYP' => 's');
						//$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'inr_amount' , 		'DATA' => $cutOff , 	  'TYP' => 's');
						//$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'usd_amount' , 		'DATA' => $_REQUEST['rate_per_night_inr'] , 	  'TYP' => 's');
						
						
						//$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'status' , 		    'DATA' => 'A' , 	 	   'TYP' => 's');
						
												 				
						$mycms->sql_update($sqlUpdatePackageAmount);
			 }
			 else{

			 	$sqlUpdatePackageAmount 		     =		array();

				$sqlUpdatePackageAmount['QUERY']  = "INSERT INTO "._DB_ACCOMMODATION_PACKAGE_PRICE_."
																   SET `hotel_id` 		= ?,
																	   `package_id` 	= ?,
																	   `tariff_cutoff_id` 		= ?,
																	 	`inr_amount` 		= ?,
																	   	`status` 			= ?";
																	  		   
						$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $_REQUEST['hotel_id'] ,   'TYP' => 's');
						$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $_REQUEST['package_id'], 	  'TYP' => 's');
						$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'inr_amount' , 		'DATA' => $cutOff , 	  'TYP' => 's');
						$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'usd_amount' , 		'DATA' => $_REQUEST['rate_per_night_inr'] , 	  'TYP' => 's');
						
						
						$sqlUpdatePackageAmount['PARAM'][]	=	array('FILD' => 'status' , 		    'DATA' => 'A' , 	 	   'TYP' => 's');
						
												 				
						$mycms->sql_insert($sqlUpdatePackageAmount);

			 }	
		
			foreach($check_in_dateArr as $key=>$check_in)
			{
				$sqlInsertTariffAmount  = array();
				 $cutoffId				= $cutOff;	
			     $package_id         	= $package_idArr;
				 $check_in_date         = $check_in_dateArr[$key];
				 $check_out_date        = $check_out_dateArr[$key];
				 $hotel                 = $hotelArr;		
				 $inrAmount				= ($inrAmountArr[$key]!='' && !empty($inrAmountArr[$key]))?($inrAmountArr[$key]):"0.00";
				 $usdAmount				= ($usdAmountArr[$key]!='' && !empty($usdAmountArr[$key]))?($usdAmountArr[$key]):"0.00";	

				
													  
					// check if same record exit then update else insert	
				 	// new
					$sqlFetchTariffAmount 	=		array();
					$sqlFetchTariffAmount['QUERY']     = "SELECT * 
							 								FROM "._DB_TARIFF_ACCOMMODATION_." 
														   WHERE `package_id` 			= ?
															 AND `hotel_id`				= ?
															 AND `tariff_cutoff_id`		= ?
															 AND `status`				= ?
															 AND `checkin_date_id`		= ?
															 AND `checkout_date_id`		= ?
															 ";
					
					$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'package_id' , 				'DATA' => $package_id , 	  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'hotel_id' , 				'DATA' => $hotel , 	      'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' , 		'DATA' => $cutoffId , 	      'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 					'DATA' => 'A' , 	      	  'TYP' => 's');
					$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'checkin_date_id' , 					'DATA' => $check_in_date , 	      	  'TYP' => 's');	
					$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'checkout_date_id' , 					'DATA' => $check_out_date , 	      	  'TYP' => 's');	
					
					
					$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
					$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount); 
						
					// new
					if($maxRowsTariffAmount > 0)
					{
						$sqlUpdateTariffAmount 		     =		array();

						$sqlUpdateTariffAmount['QUERY']  = "UPDATE "._DB_TARIFF_ACCOMMODATION_."
																   SET `package_id` 		= ?,
																	   `tariff_cutoff_id` 	= ?,
																	   `inr_amount` 		= ?,
																	   `usd_amount` 		= ?,
																	   `hotel_id`	        = ?, 
																	   `checkin_date_id` 	= ?,
																 	   `checkout_date_id` 	= ?,
																	   `status` 			= ?,
																	   `modified_by` 		= ?,
																	   `modified_ip` 		= ?,
																	   `modified_sessionId` 		= ?,
																	   `modified_dateTime` 		= ?,
																	   `type`  = ?";							   
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $package_id ,   'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $cutoffId , 	  'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'inr_amount' , 		'DATA' => $inrAmount , 	  'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'usd_amount' , 		'DATA' => $usdAmount , 	  'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotel , 	  	  'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'checkin_date_id' ,	'DATA' => $check_in_date , 'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'checkout_date_id' ,'DATA' => $check_out_date ,'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 		    'DATA' => 'A' , 	 	   'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_by' , 		    'DATA' => $loggedUserId , 	 	   'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_ip' , 		    'DATA' => $_SERVER['REMOTE_ADDR'] , 	 	   'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_sessionId' , 		    'DATA' => session_id() , 	 	   'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 		    'DATA' => date('Y-m-d H:i:s') , 	 	   'TYP' => 's');
						$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'type' , 		    'DATA' => 'new' , 	 	   'TYP' => 's');
												 				
						$mycms->sql_update($sqlUpdateTariffAmount);
					}else{
						
						
						$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_TARIFF_ACCOMMODATION_."
																   SET 	`package_id` 		= ?,
																	   	`tariff_cutoff_id` 	= ?,
																	   	`inr_amount` 		= ?,
																	   	`usd_amount` 		= ?,
																	   	`hotel_id`	        = ?, 
																	   	`checkin_date_id` 	= ?,
																 	   	`checkout_date_id` 	= ?,
																	   	`status` 			= ?, 
																	   	`created_ip`     	= ?,
																		`created_by`        = ?,
																		`created_sessionId` = ?,
																		`created_dateTime`  = ?,				
																		`type`  = ?";							   
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $package_id ,   'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $cutoffId , 	  'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'inr_amount' , 		'DATA' => $inrAmount , 	  'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'usd_amount' , 		'DATA' => $usdAmount , 	  'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotel , 	  	  'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'checkin_date_id' ,	'DATA' => $check_in_date , 'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'checkout_date_id' ,'DATA' => $check_out_date ,'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 		    'DATA' => 'A' , 	 	   'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_ip' , 		    'DATA' => $_SERVER['REMOTE_ADDR'] , 	 	   'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_by' , 		    'DATA' => $loggedUserId , 	 	   'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_sessionId' , 		    'DATA' => session_id() , 	 	   'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_dateTime' , 		    'DATA' => date('Y-m-d H:i:s') , 	 	   'TYP' => 's');
						$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'type' , 		    'DATA' => 'new' , 	 	   'TYP' => 's');
						
						$mycms->sql_insert($sqlInsertTariffAmount);
						
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
