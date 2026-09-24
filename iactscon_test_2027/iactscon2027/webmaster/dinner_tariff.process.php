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
			
			// pageRedirection('dinner_tariff.php', 2, "");
			break;
	}
	
	
	function updateTarrif($mycms,$cfg)
	{
		$loggedUserID = $mycms->getLoggedUserId();
		//	echo'<pre>';print_r($_REQUEST);echo'</pre>';die();		
		$dinner_classification_id         = addslashes($_REQUEST['dinner_classification_id']);
		$tariff_inr_edit         			= $_REQUEST['tariff_inr_cutoff_id_edit'];
		$tariff_usd_edit        			= $_REQUEST['tariff_usd_cutoff_id_edit'];	
		
	    $tariff_cutoff_id                   = $_REQUEST['cutoff_id']; 			
		
		foreach($tariff_cutoff_id as $key=>$cutoff_id)
		{
			$inr_amount = $tariff_inr_edit[$cutoff_id];
			$usd_amount = $tariff_usd_edit[$cutoff_id];
			if($inr_amount!="" && $usd_amount!="")
			{
				$sqlFetchTariffAmount = array();
				$sqlFetchTariffAmount['QUERY']     = "SELECT * 
														 FROM "._DB_DINNER_TARIFF_." 
														 WHERE `dinner_classification_id` = ? 
														   AND `cutoff_id` = ?";
				
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'dinner_classification_id' , 'DATA' => $dinner_classification_id ,         'TYP' => 's');
				$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'cutoff_id' ,  				 'DATA' => $cutoff_id ,   	  'TYP' => 's');
				
				$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
				$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);     
				
				if($maxRowsTariffAmount > 0)
				{
					$sqlUpdateTariffAmount = array();
					$sqlUpdateTariffAmount['QUERY']  = "UPDATE "._DB_DINNER_TARIFF_."
														  SET `inr_amount` = ?,
														  	  `usd_amount` = ?,
															  `modified_by` = ?,
															  `modified_ip` = ?,
															  `modified_sessionId` = ?,
															  `modified_dateTime` = ? 
														WHERE `dinner_classification_id` = ? 
														  AND `cutoff_id` = ?";
					
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'inr_amount' , 		  		 'DATA' => $inr_amount ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'usd_amount' , 		  		 'DATA' => $usd_amount ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_by' , 	  		 'DATA' => $loggedUserID ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_ip' , 	  		 'DATA' => $_SERVER['REMOTE_ADDR'] ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_sessionId' ,		 'DATA' => session_id() ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 		 'DATA' => date('Y-m-d H:i:s') ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'dinner_classification_id' , 'DATA' => $dinner_classification_id ,     'TYP' => 's');
					$sqlUpdateTariffAmount['PARAM'][]	=	array('FILD' => 'cutoff_id' ,				 'DATA' => $cutoff_id ,     'TYP' => 's');
					$mycms->sql_update($sqlUpdateTariffAmount);
				}
				else
				{
					$sqlInsertTariffAmount = array();
					$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_DINNER_TARIFF_."
															   SET `dinner_classification_id` = ?,
																   `cutoff_id` = ?,
																   `inr_amount` = ?,
																   `usd_amount` = ?, 
																   `status` = ?,
																   `created_by` = ?,
																   `created_ip` = ?,
																   `created_sessionId` = ?,
																   `created_dateTime` = ?";
					
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'dinner_classification_id' , 		  		 'DATA' => $dinner_classification_id ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'cutoff_id' , 		  	'DATA' => $cutoff_id ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'inr_amount' , 		  	'DATA' => $inr_amount ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'usd_amount' , 		  	 'DATA' => $usd_amount ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'status' , 		  		 'DATA' =>'A',     			  'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_by' , 		  	 'DATA' => $loggedUserID ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_ip' , 		  	 'DATA' => $_SERVER['REMOTE_ADDR'] ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_sessionId' , 	 'DATA' => session_id() ,     'TYP' => 's');
					$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_dateTime' , 	  'DATA' => date('Y-m-d H:i:s') ,     'TYP' => 's');
					$mycms->sql_insert($sqlInsertTariffAmount);
				}
			}
		}
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="system_master.php#dinner";</script>';
		
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
