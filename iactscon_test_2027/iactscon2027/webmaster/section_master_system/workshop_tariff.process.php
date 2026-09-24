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
			pageRedirection('workshop_tariff.php', 2, "");
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
