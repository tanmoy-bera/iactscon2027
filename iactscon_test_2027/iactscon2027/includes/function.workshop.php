<?php
function getDelegateTotalWorkshopCount($delegatesId)
{
	global $cfg, $mycms;
	
	$totalAccompanyCount  = 0;
	$sqlFetch = array();
	$sqlFetch['QUERY']	  = "SELECT COUNT(*) AS totalWorkshopCount
							   FROM "._DB_REQUEST_WORKSHOP_."
							  WHERE status = ?
								AND delegate_id = ?";
	$sqlFetch['PARAM'][]  = array('FILD' => 'status', 		'DATA' =>'A',  			'TYP' => 's');
	$sqlFetch['PARAM'][]  = array('FILD' => 'delegate_id', 	'DATA' =>$delegatesId,  'TYP' => 's');
	$result			      = $mycms->sql_select($sqlFetch);
	return $result[0]['totalWorkshopCount'];
}

function getTotalWorkshop()
{
	global $cfg, $mycms;
	$sqlWorkshopclsf = array();	
	$sqlWorkshopclsf['QUERY'] = "SELECT COUNT(*) AS totalWorkshop 
								  FROM "._DB_WORKSHOP_CLASSIFICATION_."
						 		  WHERE status = ?";
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>'A',  'TYP' => 's');
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	return $resWorkshopclsf[0]['totalWorkshop'];
}

function getWorkshopRecord($workshoId="",$status=false)
{		
	global $cfg, $mycms;
	
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'I',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	
	$sqlWorkshopclsf 	=	array();
	$sqlWorkshopclsf['QUERY'] = "SELECT *
								   FROM "._DB_WORKSHOP_CLASSIFICATION_." 
								  WHERE `status` = ? ";	
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	// foreach($searchConditionPARAM as $k=>$val)
	// {
	// 	$sqlWorkshopclsf['PARAM'][] = $val;
	// }
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	return $resWorkshopclsf[0];
}

function getAllWorkshopRecord($workshoId="",$status=false)
{		
	global $cfg, $mycms;
	
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'I',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	
	$sqlWorkshopclsf 	=	array();
	$sqlWorkshopclsf['QUERY'] = "SELECT *
								   FROM "._DB_WORKSHOP_CLASSIFICATION_." 
								  WHERE `status` = ? ";	
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	// foreach($searchConditionPARAM as $k=>$val)
	// {
	// 	$sqlWorkshopclsf['PARAM'][] = $val;
	// }
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	return $resWorkshopclsf;
}

function getWorkshopName($workshoId="",$status=false)
{		
	global $cfg, $mycms;
	
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'I',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	
	$sqlWorkshopclsf 	=	array();
	$sqlWorkshopclsf['QUERY'] = "SELECT `classification_title`,`id` 
								   FROM "._DB_WORKSHOP_CLASSIFICATION_." 
								  WHERE `id` = ? ".$searchCondition."";	
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$workshoId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlWorkshopclsf['PARAM'][] = $val;
	}
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	return strip_tags($resWorkshopclsf[0]['classification_title']);
}

function getWorkshopDate($workshoId="")
{		
	global $cfg, $mycms;
	
	$sqlWorkshopclsf 	=	array();		
	$sqlWorkshopclsf['QUERY'] = "SELECT `workshop_date`,`id` FROM "._DB_WORKSHOP_CLASSIFICATION_." WHERE status = ? AND `id` = ?";
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$workshoId,  'TYP' => 's');
		$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);		
		return $resWorkshopclsf[0]['workshop_date'];
}

function getWorkshopDescription($workshoId="")
{
	
	global $cfg, $mycms;
	
	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT `workshop_description`,`id` FROM "._DB_WORKSHOP_CLASSIFICATION_." 
									WHERE status = ? 
									AND `id` = ?";
									
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',         'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id',     'DATA' =>$workshoId,  'TYP' => 's');
	
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	
	return $resWorkshopclsf[0]['workshop_description'];
}

function getAllComboWorkshopList($clsfId = "")
{
	global $mycms;
	$comboWorkshopArr = array();
	$comboTariffArray   = getAllRegistrationComboTariffs();
	foreach ($comboTariffArray as $keys => $rowComboClsId) {
		
		$workshop_ids = json_decode($rowComboClsId['workshop_classification']);
		
		foreach ($workshop_ids as $key => $workshop_id) {

			$sqlWorkshopclsf = array();
			$sqlWorkshopclsf['QUERY'] = "SELECT * FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
									WHERE status = ? 
									AND `id` = ?";

			$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A',         'TYP' => 's');
			$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'id',     'DATA' => $workshop_id,  'TYP' => 's');

			$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);

			$comboWorkshopArr[$rowComboClsId['id']][$key] = $resWorkshopclsf[0];

		}
	}

	return $comboWorkshopArr;
}

function getWorkshopTariffId($workshoId="",$cutoffId="",$clsfId="")
{
	
	global $cfg, $mycms;
	
	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT * 
								  FROM "._DB_TARIFF_WORKSHOP_." 
								 WHERE status = ? 
								   AND `workshop_id` = ? 
								   AND `tariff_cutoff_id` = ? 
								   AND `registration_classification_id` = ?";
	
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status',                         'DATA' =>'A',        'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'workshop_id',                    'DATA' =>$workshoId, 'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',               'DATA' =>$cutoffId,  'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'registration_classification_id', 'DATA' =>$clsfId,    'TYP' => 's');
	
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	
	return $resWorkshopclsf[0]['id'];
}
function getOnlyWorkshopTariffId($workshoId="",$cutoffId="",$clsfId="",$onlyWrkshopclsfId="")
{
	
	global $cfg, $mycms;
	if($onlyWrkshopclsfId!=''){
	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT * 
								  FROM "._DB_TARIFF_WORKSHOP_." 
								 WHERE status = ? 
								   AND `workshop_id` = ? 
								   AND `tariff_cutoff_id` = ? 
								   AND `registration_classification_id` = ?
								   AND OnlyWorkshop_clssId ='".$onlyWrkshopclsfId."'";
	
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status',                         'DATA' =>'A',        'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'workshop_id',                    'DATA' =>$workshoId, 'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',               'DATA' =>$cutoffId,  'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'registration_classification_id', 'DATA' =>$clsfId,    'TYP' => 's');
	
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	}else{
	$sqlWorkshopclsf = array();
	$sqlWorkshopclsf['QUERY'] = "SELECT * 
								  FROM "._DB_TARIFF_WORKSHOP_." 
								 WHERE status = ? 
								   AND `workshop_id` = ? 
								   AND `tariff_cutoff_id` = ? 
								   AND `registration_classification_id` = ?
								   AND OnlyWorkshop_clssId ='0'";
	
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'status',                         'DATA' =>'A',        'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'workshop_id',                    'DATA' =>$workshoId, 'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',               'DATA' =>$cutoffId,  'TYP' => 's');
	$sqlWorkshopclsf['PARAM'][]   = array('FILD' => 'registration_classification_id', 'DATA' =>$clsfId,    'TYP' => 's');
	
	$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
	}
	return $resWorkshopclsf[0]['id'];
}

function getWorkshopDetails($reqId,$status=false)
{
	
	global $cfg, $mycms;
	if($status)
	{	
		$searchCondition = "AND req.status IN (?,?)";
		$conditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');	
		$conditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');
	}
	else
	{
		$searchCondition = "AND req.status = ?";
		$conditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');	
	}
	
	$sqlDetails 	=	array();
	$sqlDetails['QUERY'] = "   SELECT req.*, 
									  clsf.type, clsf.display, clsf.classification_title, clsf.workshop_description, 
									  clsf.seat_limit, clsf.sequence_by, clsf.workshop_date, clsf.display, clsf.showInInvoices
								 FROM "._DB_REQUEST_WORKSHOP_." req 
						   INNER JOIN "._DB_WORKSHOP_CLASSIFICATION_." clsf
								   ON clsf.id = req.workshop_id
								WHERE req.id = ? ".$searchCondition." ";

	$sqlDetails['PARAM'][]	=	array('FILD' => 'req.id' , 	  'DATA' => $reqId ,             'TYP' => 's');

	
	foreach($conditionPARAM as $k=>$val)
	{
		 $sqlDetails['PARAM'][] = $val;
	}					
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0];
}

function getWorkshopDetailsOfDelegate($delegateId,$fullDetails=false)
{
	global $cfg, $mycms;
	
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT req.*,
								   clsf.type, clsf.display, clsf.classification_title, clsf.workshop_description, 
								   clsf.seat_limit, clsf.sequence_by, clsf.workshop_date, clsf.display, clsf.showInInvoices,
								   inv.id AS invoiceId
							  FROM "._DB_REQUEST_WORKSHOP_." req
						INNER JOIN "._DB_WORKSHOP_CLASSIFICATION_." clsf
								ON clsf.id = req.workshop_id
				   LEFT OUTER JOIN "._DB_INVOICE_." inv
				   				ON inv.id = req.refference_invoice_id
							   AND inv.status = 'A'
							   AND inv.service_type IN('DELEGATE_WORKSHOP_REGISTRATION','DELEGATE_CONFERENCE_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION')
							 WHERE req.status = ? 
							   AND req.`delegate_id` = ?";
								
	$sqlDetails['PARAM'][]	=	array('FILD' => 'status' , 	        'DATA' => 'A' ,          'TYP' => 's');	
	$sqlDetails['PARAM'][]	=	array('FILD' => 'delegate_id' , 	'DATA' => $delegateId ,  'TYP' => 's');	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	if($fullDetails)
	{
		$return = array();
		$countr = 0;		
		foreach($resDetails as $k => $row)
		{
			if($row['invoiceId']!= '')
			{
				$invoiceDetails = getInvoiceDetails($row['refference_invoice_id']);
				$return[$countr] = $row;
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

function insertingWorkshopDetails($workshopDetailArray)
{
	global $cfg, $mycms;
	// echo "<pre>";
	// print_r($workshopDetailArray);
	$workshopReqId = array();
	foreach($workshopDetailArray as $workshopId => $workshopDetails)
	{
		// INSERT INTO WORKSHOP REQUEST TABLE
		$sqlInsertTutorialsRequest   = array();
		$sqlInsertTutorialsRequest['QUERY']     = "INSERT INTO "._DB_REQUEST_WORKSHOP_." 
													 SET `delegate_id`					  = ?, 
														 `workshop_id` 					  = ?, 
														 `tariff_cutoff_id`				  = ?,
														 `registration_type` 			  = ?,
														 `workshop_tarrif_id` 			  = ?,
														 `booking_mode` 				  = ?,
														 `payment_status`				  = ?,
														 `registration_classification_id` = ?,
														 `refference_invoice_id` 		  = ?,
														 `refference_slip_id` 			  = ?,
														 `status`						  = ?,
														 `created_ip` 					  = ?,
														 `created_sessionId` 			  = ?,
														 `created_browser`				  = ?,
														 `created_dateTime`				  = ?";
		
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'delegate_id',                           'DATA' =>$workshopDetails['delegate_id'],                    'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'workshop_id',                           'DATA' =>$workshopDetails['workshop_id'],                    'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',                      'DATA' =>$workshopDetails['tariff_cutoff_id'],               'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'registration_type',                     'DATA' =>$workshopDetails['registration_type'],              'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'workshop_tarrif_id',                    'DATA' =>$workshopDetails['workshop_tarrif_id'],             'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'booking_mode',                          'DATA' =>$workshopDetails['booking_mode'],                   'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'payment_status',                        'DATA' =>$workshopDetails['payment_status'],                 'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'registration_classification_id',        'DATA' =>$workshopDetails['registration_classification_id'], 'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'refference_invoice_id',                 'DATA' =>$workshopDetails['refference_invoice_id'],          'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'refference_slip_id',                    'DATA' =>$workshopDetails['refference_slip_id'],             'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'status',                                'DATA' =>'A',                                                'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'created_ip',                            'DATA' =>$_SERVER['REMOTE_ADDR'],                            'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'created_sessionId',                     'DATA' =>session_id(),                                        'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'created_browser',                       'DATA' =>$_SERVER['HTTP_USER_AGENT'],                         'TYP' => 's');
		$sqlInsertTutorialsRequest['PARAM'][]   = array('FILD' => 'created_dateTime',                      'DATA' =>date('Y-m-d H:i:s'),                                 'TYP' => 's');
		

		//   echo '<pre>'; print_r($sqlInsertTutorialsRequest); die; 
		$tutorialspRequestId            = $mycms->sql_insert($sqlInsertTutorialsRequest, false);
		
		$workshopReqId[]				= $tutorialspRequestId;
		// UPDATING USER TABLE
		$sqlUpdateUser = array();
		
	   $sqlUpdateUser['QUERY']         	 = "UPDATE "._DB_USER_REGISTRATION_." 
											SET `isWorkshop` = ?
										  WHERE `id` = ?";
										  
		$sqlUpdateUser['PARAM'][]   = array('FILD' => 'isWorkshop',                      'DATA' =>'Y',                                                'TYP' => 's');
		$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',                              'DATA' =>$workshopDetails['delegate_id'],                    'TYP' => 's');
		$mycms->sql_update($sqlUpdateUser, false);
	}
	return $workshopReqId;
}

function updatingWorkShopDetails($workshopDetails)
{
	global $cfg, $mycms;
	
	$workshopReqId = array();
	
	// INSERT INTO WORKSHOP REQUEST TABLE
	$sqlInsertTutorialsRequest = array();
	$sqlInsertTutorialsRequest['QUERY']         = "UPDATE "._DB_REQUEST_WORKSHOP_." 
												SET `workshop_id` 					  = '".$workshopDetails['workshop_id']."', 
													`workshop_tarrif_id` 			  = '".$workshopDetails['workshop_tarrif_id']."',
													`registration_classification_id` = '".$workshopDetails['registration_classification_id']."'													
											WHERE `id` = '".$workshopDetails['id']."'";
	$tutorialspRequestId             = $mycms->sql_update($sqlInsertTutorialsRequest, false);
}

function WorkshopReportQuery($searchById = '')
{
	
	global $mycms, $cfg;
	$query = array();
	$query['QUERY'] = " SELECT workshopClassification.id AS id, workshopClassification.classification_title, workshopClassification.seat_limit, workshopClassification.workshop_date,
							   IFNULL(appliedFro.appCount,0) AS applied_for,
							   IFNULL(bookedOnline.appCount,0) AS booked_Online,
							   IFNULL(bookedOffLine.appCount,0) AS booked_OffLine,
							   IFNULL(paidFor.appCount,0) AS paid_For,
							   IFNULL(complementary.appCount,0) AS complementary_pay,
							   IFNULL(zerovalue.appCount,0) AS zerovalue_pay,
							   IFNULL(notPaidFor.appCount,0) AS notPaid_For ";
					   
	if(trim($searchById)!='')
	{
		$query['QUERY'] .= " FROM ( SELECT * FROM "._DB_WORKSHOP_CLASSIFICATION_." WHERE id = '".$searchById."'  ) workshopClassification ";
	}
	else
	{
		$query['QUERY'] .= " FROM "._DB_WORKSHOP_CLASSIFICATION_." workshopClassification ";
	}
	
	$query['QUERY'] .= " LEFT OUTER JOIN (
									SELECT `workshop_id`, COUNT(*) AS appCount 
									  FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
									 WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									  AND reqWorkshop.status = 'A'
									 AND refference_slip_id IN ( SELECT invoice.id													   
																	  FROM "._DB_SLIP_." invoice									 
																	 WHERE invoice.status = 'A' 
																	   AND reqWorkshop.status = 'A')  
								  GROUP BY `workshop_id`
								) appliedFro
							 ON workshopClassification.id = appliedFro.workshop_id
				
				
				
				LEFT OUTER JOIN (				
								  SELECT `workshop_id`, COUNT(*) AS appCount 
									FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
								   WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									 AND reqWorkshop.status = 'A'
									 AND reqWorkshop.payment_status = 'PAID'
									 AND refference_slip_id IN ( SELECT invoice.id													   
																	  FROM "._DB_SLIP_." invoice									 
																
																	 WHERE invoice.status = 'A' 
																	   AND invoice.payment_status ='PAID'
																	   AND reqWorkshop.status = 'A')
									 AND booking_mode = 'ONLINE'
								GROUP BY `workshop_id`
								) bookedOnline
							 ON workshopClassification.id = bookedOnline.workshop_id
				
				LEFT OUTER JOIN (
								  SELECT `workshop_id`, COUNT(*) AS appCount 
									FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
								   WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									 AND reqWorkshop.payment_status = 'PAID'
									 AND refference_slip_id IN ( SELECT invoice.id													   
																	  FROM "._DB_SLIP_." invoice									 
																
																	 WHERE invoice.status = 'A' 
																	  AND invoice.payment_status ='PAID'
																	   AND reqWorkshop.status = 'A')
									 AND booking_mode = 'OFFLINE'
								GROUP BY `workshop_id`
								) bookedOffLine
							 ON workshopClassification.id = bookedOffLine.workshop_id
				
				LEFT OUTER JOIN (
								  SELECT `workshop_id`, COUNT(*) AS appCount 
									FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
								   WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									 AND reqWorkshop.payment_status ='PAID'
									 AND refference_slip_id IN ( SELECT invoice.id													   
																	  FROM "._DB_SLIP_." invoice									 
																
																	 WHERE invoice.status = 'A' 
																	   AND invoice.payment_status ='PAID'
																	   AND reqWorkshop.status = 'A')
								GROUP BY `workshop_id`
								) paidFor
							 ON workshopClassification.id = paidFor.workshop_id																		
				
				
							 
				LEFT OUTER JOIN (
								  SELECT `workshop_id`, COUNT(*) AS appCount 
									FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
								   WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									 AND reqWorkshop.status = 'A'
									 AND reqWorkshop.payment_status = 'COMPLIMENTARY'
								GROUP BY `workshop_id`
								) complementary
							 ON workshopClassification.id = complementary.workshop_id	
							 
				
							 
				LEFT OUTER JOIN (
								  SELECT `workshop_id`, COUNT(*) AS appCount 
									FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
								   WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									 
									  AND reqWorkshop.payment_status ='ZERO_VALUE'
									  AND refference_slip_id IN ( SELECT invoice.id													   
																	  FROM "._DB_SLIP_." invoice									 
																
																	 WHERE invoice.status = 'A' 
																	   AND reqWorkshop.status = 'A')
								GROUP BY `workshop_id`
								) zerovalue
							 ON workshopClassification.id = zerovalue.workshop_id	
							 
				
							 
				LEFT OUTER JOIN (
								  SELECT `workshop_id`, COUNT(*) AS appCount 
									FROM "._DB_REQUEST_WORKSHOP_." reqWorkshop
								   WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
									 
									 AND reqWorkshop.payment_status ='UNPAID'
									 AND refference_slip_id IN ( SELECT invoice.id													   
																	  FROM "._DB_SLIP_." invoice									 
																
																	 WHERE invoice.status = 'A' 
																		AND invoice.payment_status ='UNPAID'
																	   AND reqWorkshop.status = 'A' )
								GROUP BY `workshop_id`
								) notPaidFor
							 ON workshopClassification.id = notPaidFor.workshop_id			
				
					";
					
	return $query;
}

function WorkshopReportQueryNew($searchById = '',$searchCondition)
{
	global $mycms, $cfg;
	$return = array();
	$searchCond = "";
	if(trim($searchById)!='')
	{
		 $searchCond .= "  AND workshopClassification.id = '".$searchById."' ";
	}
	
	$sqlWorkShopList['QUERY'] = "SELECT * FROM "._DB_WORKSHOP_CLASSIFICATION_." workshopClassification WHERE 1 ".$searchCond.$searchCondition;
	$resWorkShopList = $mycms->sql_select($sqlWorkShopList);
	foreach($resWorkShopList as $k => $rowWorkShopList)
	{
		$return[$rowWorkShopList['id']]['id'] = $rowWorkShopList['id'];
		$return[$rowWorkShopList['id']]['sessionType'] = $rowWorkShopList['type'];
		$return[$rowWorkShopList['id']]['classification_title'] = $rowWorkShopList['classification_title'];
		$return[$rowWorkShopList['id']]['seat_limit'] = $rowWorkShopList['seat_limit'];			
		$return[$rowWorkShopList['id']]['applied_for'] = 0;
		$return[$rowWorkShopList['id']]['booked_Online'] = 0;
		$return[$rowWorkShopList['id']]['booked_OffLine'] = 0;
		$return[$rowWorkShopList['id']]['paid_For'] = 0;
		$return[$rowWorkShopList['id']]['complementary_pay'] = 0;			
		$return[$rowWorkShopList['id']]['zerovalue_pay'] = 0;
		$return[$rowWorkShopList['id']]['notPaid_For'] = 0;
		$return[$rowWorkShopList['id']]['guest_Cards'] = 0;
					
		$sqlAppliedFro['QUERY'] = " SELECT COUNT(*) AS appCount 
							 FROM "._DB_REQUEST_WORKSHOP_." 
							WHERE workshop_id = '".$rowWorkShopList['id']."'
							  AND status = 'A'
							  AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A'  AND registration_request != 'GUEST')
							  AND refference_slip_id IN (   SELECT invoice.id													   
															  FROM "._DB_SLIP_." invoice									 
														INNER JOIN "._DB_INVOICE_." invoiceDtls
																ON invoice.id = invoiceDtls.slip_id
															   AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
															 WHERE invoice.status = 'A' 
															   AND invoiceDtls.status = 'A')";
		$resAppliedFro = $mycms->sql_select($sqlAppliedFro);
		$return[$rowWorkShopList['id']]['applied_for'] = $resAppliedFro[0]['appCount'];
					
		$sqlBookedOnline['QUERY'] = "SELECT COUNT(*) AS appCount 
							FROM "._DB_REQUEST_WORKSHOP_." 
						   WHERE workshop_id = '".$rowWorkShopList['id']."'
							 AND status = 'A'
							 AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A'  AND registration_request != 'GUEST')
							 AND refference_slip_id IN (    SELECT invoice.id													   
															  FROM "._DB_SLIP_." invoice									 
														INNER JOIN "._DB_INVOICE_." invoiceDtls
																ON invoice.id = invoiceDtls.slip_id
															   AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
															   AND invoiceDtls.payment_status = 'PAID' 
															 WHERE invoice.status = 'A' 
															   AND invoiceDtls.status = 'A')
							 AND booking_mode = 'ONLINE'";
		$resBookedOnline = $mycms->sql_select($sqlBookedOnline);
		$return[$rowWorkShopList['id']]['booked_Online'] = $resBookedOnline[0]['appCount'];
					
		$sqlBookedOffLine['QUERY'] = " SELECT COUNT(*) AS appCount 
								FROM "._DB_REQUEST_WORKSHOP_." 
							   WHERE workshop_id = '".$rowWorkShopList['id']."'
								 AND status = 'A'
								 AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A'  AND registration_request != 'GUEST')
								 AND refference_slip_id IN (SELECT invoice.id													   
															  FROM "._DB_SLIP_." invoice									 
														INNER JOIN "._DB_INVOICE_." invoiceDtls
																ON invoice.id = invoiceDtls.slip_id
															   AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
															   AND invoiceDtls.payment_status = 'PAID' 
															 WHERE invoice.status = 'A' 
															   AND invoiceDtls.status = 'A')
								 AND booking_mode = 'OFFLINE'";
		$resBookedOffLine = $mycms->sql_select($sqlBookedOffLine);
		$return[$rowWorkShopList['id']]['booked_OffLine'] = $resBookedOffLine[0]['appCount'];
		$sqlPaidFor = array();
		$sqlPaidFor['QUERY'] = "SELECT COUNT(*) AS appCount 
						 FROM "._DB_REQUEST_WORKSHOP_." 
						WHERE workshop_id = '".$rowWorkShopList['id']."'
						  AND status = 'A'
						  AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
						  AND refference_slip_id IN (SELECT invoice.id													   
													   FROM "._DB_SLIP_." invoice									 
												 INNER JOIN "._DB_INVOICE_." invoiceDtls
														 ON invoice.id = invoiceDtls.slip_id
														AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
														AND invoiceDtls.payment_status = 'PAID' 
													  WHERE invoice.status = 'A' 
														AND invoiceDtls.status = 'A')";
		$resPaidFor = $mycms->sql_select($sqlPaidFor);
		$return[$rowWorkShopList['id']]['paid_For'] = $resPaidFor[0]['appCount'];
		$sqlComplementary = array();
		$sqlComplementary['QUERY'] = " SELECT COUNT(*) AS appCount 
								FROM "._DB_REQUEST_WORKSHOP_." 
							   WHERE workshop_id = '".$rowWorkShopList['id']."'
								 AND status = 'A'
								 AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
								 AND refference_slip_id IN (SELECT invoice.id													   
															  FROM "._DB_SLIP_." invoice									 
														INNER JOIN "._DB_INVOICE_." invoiceDtls
																ON invoice.id = invoiceDtls.slip_id
															   AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
															   AND invoiceDtls.payment_status = 'COMPLIMENTARY'
															 WHERE invoice.status = 'A' 
															   AND invoiceDtls.status = 'A')";
		$resComplementary = $mycms->sql_select($sqlComplementary);
		$return[$rowWorkShopList['id']]['complementary_pay'] = $resComplementary[0]['appCount'];
		$sqlZerovalue = array();
		$sqlZerovalue['QUERY'] = " SELECT COUNT(*) AS appCount 
							FROM "._DB_REQUEST_WORKSHOP_." 
						   WHERE workshop_id = '".$rowWorkShopList['id']."'
							 AND status = 'A'
							 AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
							 AND refference_slip_id IN (SELECT invoice.id													   
														  FROM "._DB_SLIP_." invoice									 
													INNER JOIN "._DB_INVOICE_." invoiceDtls
															ON invoice.id = invoiceDtls.slip_id
														   AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
														   AND invoiceDtls.payment_status = 'ZERO_VALUE'
														 WHERE invoice.status = 'A' 
														   AND invoiceDtls.status = 'A')";
		$resZerovalue = $mycms->sql_select($sqlZerovalue);
		$return[$rowWorkShopList['id']]['zerovalue_pay'] = $resZerovalue[0]['appCount'];
		
		$sqlNotPaidFor = array();
		$sqlNotPaidFor['QUERY'] = "SELECT `workshop_id`, COUNT(*) AS appCount 
							FROM "._DB_REQUEST_WORKSHOP_."
						   WHERE workshop_id = '".$rowWorkShopList['id']."'
							 AND status = 'A'
							 AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request != 'GUEST')
							 AND refference_slip_id IN (SELECT invoice.id													   
														  FROM "._DB_SLIP_." invoice									 
													INNER JOIN "._DB_INVOICE_." invoiceDtls
															ON invoice.id = invoiceDtls.slip_id
														   AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION' 
														   AND invoiceDtls.payment_status = 'UNPAID' 
														 WHERE invoice.status = 'A' 
														   AND invoiceDtls.status = 'A')";
		$resNotPaidFor = $mycms->sql_select($sqlNotPaidFor);
		$return[$rowWorkShopList['id']]['notPaid_For'] = $resNotPaidFor[0]['appCount'];
		
		$sqlGuestCard = array();
		$sqlGuestCard['QUERY'] = " SELECT COUNT(*) AS appCount 
							FROM "._DB_REQUEST_WORKSHOP_."
						   WHERE workshop_id = '".$rowWorkShopList['id']."'
							 AND status = 'A'
							 AND delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A' AND registration_request = 'GUEST')";
		$resGuestCard = $mycms->sql_select($sqlGuestCard);
		$return[$rowWorkShopList['id']]['guest_Cards'] = $resGuestCard[0]['appCount'];
	}
	return $return;
}

function totalWorkshopCountReport()
{
	global $cfg, $mycms;
	
	
	$query['QUERY'] = "  		 SELECT workshopClassification.id AS id, workshopClassification.classification_title, workshopClassification.seat_limit,
										IFNULL(appliedFro.appCount,0) AS Total,
										IFNULL(appliedFroPaid.appCount,0) AS TotalPaid,
										IFNULL(appliedFroUnpaid.appCount,0) AS TotalUnpaid,
										IFNULL(appliedFroZeroValue.appCount,0) AS TotalZeroValue,
										IFNULL((IFNULL(appliedFroZeroValue.appCount,0)+IFNULL(appliedFroPaid.appCount,0)),0) AS TotalSeat";
	
	$query['QUERY'] .= "   		   FROM "._DB_WORKSHOP_CLASSIFICATION_." workshopClassification ";
	
	$query['QUERY'] .= "LEFT OUTER JOIN (
											SELECT `workshop_id`, COUNT(*) AS appCount 
											  FROM "._DB_REQUEST_WORKSHOP_." 
											 WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A')
										  GROUP BY `workshop_id`
										) appliedFro
									 ON workshopClassification.id = appliedFro.workshop_id ";
							 
	$query['QUERY'] .= "LEFT OUTER JOIN (
											SELECT `workshop_id`, COUNT(*) AS appCount 
											  FROM "._DB_REQUEST_WORKSHOP_." 
											 WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A')
											   AND payment_status= 'PAID'
										  GROUP BY `workshop_id`
										) appliedFroPaid
									 ON workshopClassification.id = appliedFroPaid.workshop_id ";
							 
	$query['QUERY'] .= "LEFT OUTER JOIN (
											SELECT `workshop_id`, COUNT(*) AS appCount 
											  FROM "._DB_REQUEST_WORKSHOP_." 
											 WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A')
											   AND payment_status= 'UNPAID'
										  GROUP BY `workshop_id`
										) appliedFroUnpaid
									 ON workshopClassification.id = appliedFroUnpaid.workshop_id ";
							 
	$query['QUERY'] .= "LEFT OUTER JOIN (
											SELECT `workshop_id`, COUNT(*) AS appCount 
											  FROM "._DB_REQUEST_WORKSHOP_." 
											 WHERE delegate_id IN ( SELECT id FROM "._DB_USER_REGISTRATION_." WHERE user_type = 'DELEGATE' AND status = 'A')
											   AND payment_status= 'ZERO_VALUE'
										  GROUP BY `workshop_id`
										) appliedFroZeroValue
									 ON workshopClassification.id = appliedFroZeroValue.workshop_id 
					           WHERE 1 AND workshopClassification.status = 'A'";
									 
	$resultWorkshopDetails         = $mycms->sql_select($query);	
	
	$workshopCountArr			   = array();
	foreach($resultWorkshopDetails as $key=>$rowWorkshopDetails)
	{
		$workshopCountArr[$rowWorkshopDetails['id']]['WORKSHOP_NAME'] = $rowWorkshopDetails['classification_title'];
		$workshopCountArr[$rowWorkshopDetails['id']]['SEAT_LIMIT'] = $rowWorkshopDetails['seat_limit'];
		$workshopCountArr[$rowWorkshopDetails['id']]['TOTAL'] = $rowWorkshopDetails['Total'];
		$workshopCountArr[$rowWorkshopDetails['id']]['TOTAL_FILL_SIT'] = $rowWorkshopDetails['TotalSeat'];
		$workshopCountArr[$rowWorkshopDetails['id']]['TOTAL_LEFT_SIT'] = $rowWorkshopDetails['seat_limit'] - $rowWorkshopDetails['TotalSeat'];
		$workshopCountArr[$rowWorkshopDetails['id']]['TOTAL_PAID'] = $rowWorkshopDetails['TotalPaid'];
		$workshopCountArr[$rowWorkshopDetails['id']]['TOTAL_UNPAID'] = $rowWorkshopDetails['TotalUnpaid'];			
		$workshopCountArr[$rowWorkshopDetails['id']]['TOTAL_ZERO_VALUE'] = $rowWorkshopDetails['TotalZeroValue'];
	}
	return $workshopCountArr;
}

function cancelAllWorkshoofDelegate($delegateId,$workshopreqDetls = array())
{
	global $cfg, $mycms;
	if(!empty($workshopreqDetls))
	{
		
		//CANCEL WORKSHOP REQUEST
		foreach($workshopreqDetls['workshopId'] as $key=> $rowdetails)
		{
		    $sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE "._DB_REQUEST_WORKSHOP_."
						 SET `status` = 'D'
					   WHERE `id` = '".$rowdetails."'";
					   
			$mycms->sql_update($sqlUpdate, false);
		}
		
		//CANCEL INVOICE 
		foreach($workshopreqDetls['invoiceId'] as $key=> $rowdetails)
		{	
		    $sqlUpdate = array();
			$sqlUpdate['QUERY'] = "UPDATE "._DB_INVOICE_."
						 SET `status` = 'D'
					   WHERE `id` = '".$rowdetails."'
						AND `service_type` = 'DELEGATE_WORKSHOP_REGISTRATION'";
				   
			$mycms->sql_update($sqlUpdate, false);
		}
		
		//UPDATE USER REGISTRATION
		$sqlUpdate = array();
		$sqlUpdate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
							 SET `isWorkshop` = 'N',
								 `workshop_payment_status` = NULL
						   WHERE `id` = '".$delegateId."'";
						   
		$mycms->sql_update($sqlUpdate, false);
	}
	
	
}
?>