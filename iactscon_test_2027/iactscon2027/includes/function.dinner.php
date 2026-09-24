<?
function getUserDetailsByDinnerRefferenceId($reqId,$reqStatus = 'A')
{
	global $cfg, $mycms;
	$sqlDetails 	= array();
	$sqlDetails['QUERY'] = "SELECT *  
							  FROM "._DB_REQUEST_DINNER_."
							 WHERE status = ?
							   AND id = ?";

	$sqlDetails['PARAM'][]   = array('FILD' => 'status',  'DATA' =>$status,  'TYP' => 's');						   
	$sqlDetails['PARAM'][]   = array('FILD' => 'id',  	 'DATA' =>$reqId,  'TYP' => 's');
				   
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0];
}

function getDinnerDetails($reqId,$onlyActive=false)
{
	global $cfg, $mycms;
	
	$condition = "";		
	if($onlyActive)
	{
		
		$condition = " AND status IN ('A','C')";
	}
	else
	{
		$condition = " AND status = 'A'";
	}
	
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT * FROM "._DB_REQUEST_DINNER_." 
							WHERE `id` = ?
							 ".$condition."";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'id',     'DATA' =>$reqId,     'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0];
}

function dinnerForWhome($reqId)
{
	global $cfg, $mycms;
	
	$sqlUser = array();
	$sqlUser['QUERY'] = "SELECT * FROM "._DB_USER_REGISTRATION_."
							 WHERE `status` = ? 
							 AND `id` = ?";
							 
	$sqlUser['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',     'TYP' => 's');
	$sqlUser['PARAM'][]  = array('FILD' => 'id',     'DATA' =>$reqId,  'TYP' => 's');
	
	$resUser = $mycms->sql_select($sqlUser);
	$rowUser = $resUser[0];
	$user_type = $rowUser['user_type'];
	return $user_type;
}

function getDinnerDetailsOfDelegate($refference_id)
{
	global $cfg, $mycms;
	
	$sqlDetails  = array();
	  $sqlDetails['QUERY'] = "SELECT dinnerReq.*,  
	  								 dinner.dinner_classification_name, dinner.date AS dinnerDate,
	  								 user.user_full_name, dinner.dinner_hotel_name
	  							FROM "._DB_REQUEST_DINNER_." dinnerReq
						  INNER JOIN "._DB_DINNER_CLASSIFICATION_." dinner
						  		  ON dinner.id = dinnerReq.package_id
						  INNER JOIN "._DB_USER_REGISTRATION_." user
						  		  ON user.id = dinnerReq.refference_id
	  							WHERE dinnerReq.status = ?
								AND dinnerReq.`refference_id` = ?";
								
	$sqlDetails['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',            'TYP' => 's');
	$sqlDetails['PARAM'][]  = array('FILD' => 'status', 'DATA' =>$refference_id, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0];
}

function getDinnerOrderedDetailsOfDelegate($delegateId, $fullDetails=false)
{
	global $cfg, $mycms;
	
	$sqlDetails  = array();
	$sqlDetails['QUERY'] = "  SELECT dinnerReq.*,  
	  								 dinner.dinner_classification_name, dinner.date AS dinnerDate,
	  								 user.user_full_name,
									 inv.id AS invoiceId
	  							FROM "._DB_REQUEST_DINNER_." dinnerReq
						  INNER JOIN "._DB_DINNER_CLASSIFICATION_." dinner
						  		  ON dinner.id = dinnerReq.package_id
						  INNER JOIN "._DB_USER_REGISTRATION_." user
						  		  ON user.id = dinnerReq.refference_id
					 LEFT OUTER JOIN "._DB_INVOICE_." inv
				   				  ON inv.id = dinnerReq.refference_invoice_id
								 AND inv.status = 'A'
							     AND inv.service_type IN('DELEGATE_DINNER_REQUEST','DELEGATE_CONFERENCE_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION','ACCOMPANY_CONFERENCE_REGISTRATION')
	  						   WHERE dinnerReq.status = ?
								 AND dinnerReq.`delegate_id` = ?";
								
	$sqlDetails['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',            	'TYP' => 's');
	$sqlDetails['PARAM'][]  = array('FILD' => 'status', 'DATA' =>$delegateId,  		'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	if($fullDetails)
	{
		$return = array();
		$countr = 0;		
		foreach($resDetails as $k => $row)
		{
			if($row['invoiceId']!= '')
			{
				$invoiceDetails 					= getInvoiceDetails($row['refference_invoice_id']);
				$return[$countr] 					= $row;
				$return[$countr]['INVOICE_DETAILS'] = $invoiceDetails;
				$countr++;
			}
		}
	}
	else
	{
		$return = $resDetails;
	}	
	return $return;
}

function insertingDinnerDetails($dinnerDetailsArray)
{
	global $cfg, $mycms;
	foreach($dinnerDetailsArray as $key => $dinnerDetails)
	{
		$sqlInsertRequest              = array();
		$sqlInsertRequest['QUERY']	   = "INSERT INTO "._DB_REQUEST_DINNER_." 
												  SET `delegate_id`	   	 	  = ?,
													  `refference_id`         = ?,
													  `package_id` 		 	  = ?, 
													  `tariff_cutoff_id` 	  = ?, 
													  `booking_quantity` 	  = ?, 
													  `booking_mode`	 	  = ?,
													  `refference_invoice_id` = ?,
													  `refference_slip_id`	  = ?, 
													  `payment_status` 	 	  = ?,
													  `status` 				  = ?,
													  `created_ip`			  = ?,
													  `created_sessionId`	  = ?,
													  `created_browser`  	  = ?,
													  `created_dateTime` 	  = ?";
													  
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'delegate_id',           'DATA' =>$dinnerDetails['delegate_id'],            'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'refference_id',         'DATA' =>$dinnerDetails['refference_id'],          'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'package_id',            'DATA' =>$dinnerDetails['package_id'],             'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',      'DATA' =>$dinnerDetails['tariff_cutoff_id'],       'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'booking_quantity',      'DATA' =>$dinnerDetails['booking_quantity'],       'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'booking_mode',          'DATA' =>$dinnerDetails['booking_mode'],           'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'refference_invoice_id', 'DATA' =>$dinnerDetails['refference_invoice_id'],  'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'refference_slip_id',    'DATA' =>$dinnerDetails['refference_slip_id'],     'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'payment_status',        'DATA' =>$dinnerDetails['payment_status'],         'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'status',                'DATA' =>'A',                                      'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'created_ip',            'DATA' =>$dinnerDetails['created_ip'],             'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'created_sessionId',     'DATA' =>$dinnerDetails['created_sessionId'],      'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'created_browser',       'DATA' =>$dinnerDetails['created_browser'],        'TYP' => 's');
		$sqlInsertRequest['PARAM'][]   = array('FILD' => 'created_dateTime',      'DATA' =>$dinnerDetails['created_dateTime'],       'TYP' => 's');
							   
		$lastInsertedId        = $mycms->sql_insert($sqlInsertRequest, false);	
		
		$reqId[] 			   = $lastInsertedId;
	}
	
	return $reqId;
}

function removeDinnerOfUser($dinnerUserId)
{
	$sqlDetails 			 = array();
	$sqlDetails['QUERY'] 	 = "SELECT id  
							  	  FROM "._DB_REQUEST_DINNER_."
							 	 WHERE refference_id = ?";
	   
	$sqlDetails['PARAM'][]   = array('FILD' => 'refference_id', 'DATA' =>$dinnerUserId, 'TYP' => 's');
				   
	$resDetails = $mycms->sql_select($sqlDetails);
	
	foreach($resDetails as $k=>$row)
	{
		removeDinner($row['id']);
	}
}

function removeDinner($dinnerId)
{
	global $cfg, $mycms;

	$sqlupdateRequest              = array();
	$sqlupdateRequest['QUERY']	   = "	   UPDATE "._DB_REQUEST_DINNER_." 
											  SET `status` = ?
											WHERE `id` = ?";
											
	$sqlupdateRequest['PARAM'][]   = array('FILD' => 'delegate_id',           'DATA' =>'D',            'TYP' => 's');
	$sqlupdateRequest['PARAM'][]   = array('FILD' => 'refference_id',         'DATA' =>$dinnerId,      'TYP' => 's');
						   
	$mycms->sql_update($sqlupdateRequest, false);	
}

function getAllDinnerTarrifDetails()
{
	global $cfg, $mycms;
	$displayData 	 = array();
	
	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT `dinner_classification_name`,	`dinner_hotel_name`,`link`,`id`,`service_type`,`date` 
								 FROM "._DB_DINNER_CLASSIFICATION_." 
								WHERE status = ?";
											
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');	
			
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);			
	foreach($resRegClasf as $key=>$rowRegClasf)
	{
		$sqlcutoff				= array();
		$sqlcutoff['QUERY']		= "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = ?";
		$sqlcutoff['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');						 
		$rescutoff				= $mycms->sql_select($sqlcutoff);
	
		foreach($rescutoff as $keycutoff=>$cutoffvalue)
		{
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['ID']           = $rowRegClasf['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DINNER_TITTLE'] = $rowRegClasf['dinner_classification_name'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_TITTLE'] = $cutoffvalue['cutoff_title'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_ID'] = $cutoffvalue['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['TYPE'] = $rowRegClasf['service_type'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DATE']  = $rowRegClasf['date'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['dinner_hotel_name']  = $rowRegClasf['dinner_hotel_name'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['link']  = $rowRegClasf['link'];
			
			$sqlTarrif				= array();
			$sqlTarrif['QUERY'] 	= "SELECT *
									 	 FROM "._DB_DINNER_TARIFF_." 
										WHERE dinner_classification_id = ?
									  	  AND cutoff_id = ?";
			$sqlTarrif['PARAM'][]   = array('FILD' => 'cutoff_id',  	     'DATA' =>$rowRegClasf['id'],  'TYP' => 's');		
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',    'DATA' =>$cutoffvalue['id'],  'TYP' => 's');		
			$resTarrif				= $mycms->sql_select($sqlTarrif);
			
			if($resTarrif)
			{
				$rowTarrif		= $resTarrif[0];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['AMOUNT'] = $rowTarrif['inr_amount'];
			}
			else
			{
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['AMOUNT'] = "0.00";
			}
		}
	}
	return $displayData;
}


?>