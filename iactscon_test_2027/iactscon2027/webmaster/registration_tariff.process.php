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
			updateTariff($mycms,$cfg);
			// pageRedirection('registration_tariff.php', 2, "");
			break;
	}
	
	
	function updateTariff($mycms,$cfg)
	{
		global $loggedUserID ;
		
		//echo'<pre>';print_r($_REQUEST);echo'</pre>';die();		
		
		$classification_id              = addslashes($_REQUEST['classification_id']);
		$currencyArr			        = $_REQUEST['currency'];
		
		// TARIFF CLASSIFICATION AMOUNT UPDATION
		$tariff_cutoff_id               = $_REQUEST['tariff_cutoff_id']; 			
		
		foreach($tariff_cutoff_id as $key=>$amount)
		{
			$currency  = $currencyArr[$key];  	
			if($amount!="")
			{
			
				$sqlFetchTariffAmount			=	array();
				$sqlFetchTariffAmount['QUERY']	=	"SELECT * 
														FROM "._DB_TARIFF_REGISTRATION_."
														WHERE `tariff_classification_id` 	= 	?
														  AND `tariff_cutoff_id`			=   ? "; 
												
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_classification_id' , 'DATA' => $classification_id , 	'TYP' => 's');
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' , 		 'DATA' => $key , 					'TYP' => 's');
						
				$resultFetchTariffAmount  	= $mycms->sql_select($sqlFetchTariffAmount);
				$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);   
				
				if($maxRowsTariffAmount > 0)
				{
					$sql 	=	array();
					$sql['QUERY'] = "UPDATE "._DB_TARIFF_REGISTRATION_." 
										SET `amount`		 		    = ?,
										    `currency`				    = ?,
										    `modified_by`			    = ?,
										    `modified_ip` 	 	 	    = ?,
										    `modified_sessionId` 	    = ?,
										    `modified_dateTime` 	    = ? 
									  WHERE `tariff_classification_id`  = ? 
									    AND `tariff_cutoff_id` 			= ? ";
					 $sql['PARAM'][]	=	array('FILD' => 'amount' , 					'DATA' => $amount , 				  'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'currency' , 				'DATA' => $currency , 		          'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'modified_by' , 			'DATA' => $loggedUserID ,       	  'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'modified_ip' , 			'DATA' => $_SERVER['REMOTE_ADDR'] ,	  'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'modified_sessionId' , 		'DATA' => session_id() , 			  'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 		'DATA' => date('Y-m-d H:i:s') , 	  'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'tariff_classification_id' ,'DATA' => $classification_id , 		   'TYP' => 's');
					 $sql['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' , 		'DATA' => $key , 					  'TYP' => 's');
					$mycms->sql_update($sql);
				}
				else
				{
			
					$sqlInsertTariffAmount	=	array();
					$sqlInsertTariffAmount['QUERY']	= "INSERT INTO "._DB_TARIFF_REGISTRATION_."
															  SET  `tariff_classification_id`   = ?,
																	`tariff_cutoff_id` 			= ?,
																	`amount` 			        = ?,
																	`currency`                  = ?, 
																    `status`					= ?,
																	`created_by` 				= ?,
																	`created_ip` 				= ?,
																	`created_sessionId`			= ?,
																	`created_dateTime`			= ?";
																	
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_classification_id' ,       'DATA' => $classification_id , 						        'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' , 			   'DATA' => $key , 						   			 'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'amount' , 		                   'DATA' => $amount , 			  						  'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'currency' , 		               'DATA' => $currency , 			    				   'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 		                   'DATA' => 'A' , 			   							  'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_by' , 			           'DATA' => $loggedUserID , 		           				 'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_ip' , 	                   'DATA' => $_SERVER['REMOTE_ADDR'] , 					 'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_sessionId' , 			   'DATA' => session_id() , 							    'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_dateTime' , 			   'DATA' => date('Y-m-d H:i:s') , 							'TYP' => 's');
					
					$mycms->sql_insert($sqlInsertTariffAmount);
				}
			}
		}
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="system_master.php#registration";</script>';
		
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
