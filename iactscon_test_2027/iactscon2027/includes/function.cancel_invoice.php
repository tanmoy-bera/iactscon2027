<?php
function cancelGetInvoiceDetails($invoiceId)
{
	global $cfg, $mycms;
	$sqlInvoice 		=	array();
	$sqlInvoice['QUERY'] = "SELECT *,invDetails.delegate_id AS delegateId,invDetails.id AS id,slipDetails.slip_number AS slipNO 
							  FROM  "._DB_INVOICE_." invDetails
				   LEFT OUTER JOIN "._DB_SLIP_." slipDetails
								ON slipDetails.id = invDetails.slip_id
							 WHERE  invDetails.id = ?";
	$sqlInvoice['PARAM'][]   = array('FILD' => invDetails.id,         'DATA' =>$invoiceId,                'TYP' => 's');							 
	$resInvoice = $mycms->sql_select($sqlInvoice);
	return $resInvoice[0];
}

function approveProveProcess($cancelId,$delegate_id,$invoice_id)
{
	global $cfg, $mycms;
	$loggedUserID 		= $delegate_id;
	//$invoice_id 	    = $_REQUEST['invoice_id'];
	//$delegate_id 		= $_REQUEST['user_id'];
	$sessionId	   	    = session_id();
	$userBrowser        = $_SERVER['HTTP_USER_AGENT'];
	  
	  $sqlUpdatePayment 			  = array();
	  $sqlUpdatePayment['QUERY']	  = "UPDATE "._DB_CANCEL_INVOICE_."
											SET `request_status` = ?,
												`approve_datetime` = ?,
												`created_by`      = ?,
												`created_sessionId` = ?,
												`created_browser` = ?,
												`created_dateTime` = ?
										  WHERE `id` = ?";
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'request_status',           	  'DATA' =>'Approve',               		   'TYP' => 's');	
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'approve_datetime',           	  'DATA' =>date('Y-m-d H:i:s'),                'TYP' => 's');
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'created_by',           	 	  'DATA' =>$loggedUserID,               	   'TYP' => 's');
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'created_sessionId',    	  	  'DATA' =>$sessionId,               		   'TYP' => 's');
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'created_browser',           	  'DATA' =>$userBrowser,               		   'TYP' => 's');
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'created_dateTime',           	  'DATA' =>date('Y-m-d H:i:s'),                'TYP' => 's');
	$sqlUpdatePayment['PARAM'][]   = array('FILD' => 'id',           	  			  'DATA' =>$cancelId,               		   'TYP' => 's');					  
	$mycms->sql_update($sqlUpdatePayment, false);
	
	 $sqlProcessInsertStep 			 = array();
	 $sqlProcessInsertStep['QUERY']  = "INSERT INTO "._DB_INVOICE_COPY_."
										   SET `invoice_id` = ?,
											   `request_type` = ?,
											   `created_by`      = ?,
											   `created_sessionId` = ?,
											   `created_browser` = ?,
											   `created_ip` = ?,
											   `created_dateTime` = ?";
											   
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'invoice_id',           	  'DATA' =>$invoice_id,               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'request_type',         	  'DATA' =>'Request For Cancellation',         'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_by',         	 	  'DATA' =>$loggedUserID,         			   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId',         'DATA' =>session_id(),               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',           'DATA' =>$_SERVER['HTTP_USER_AGENT'],        'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',    			  'DATA' =>$_SERVER['REMOTE_ADDR'],            'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',          'DATA' =>date('Y-m-d H:i:s'),                'TYP' => 's');										   
	$mycms->sql_insert($sqlProcessInsertStep, false);
}

function cancelInvoice($delegate_id,$invoice_id, $cancel_request)
{
	global $cfg, $mycms;
	//$invoice_id 	    = $_REQUEST['invoice_id'];
	//$delegate_id 		= $_REQUEST['user_id'];
	$sqlFetchInvoice			=	array();
    $sqlFetchInvoice['QUERY']    = "SELECT * 
									  FROM "._DB_INVOICE_."
								     WHERE `id` = '".$invoice_id."'";
		
	$resultDetails	    = $mycms->sql_select($sqlFetchInvoice);
	$rowfetchDetails    = $resultDetails[0];

	//echo '<pre>'; print_r($rowfetchDetails) die;
	
	
	
	$slip_id 		    = $rowfetchDetails['slip_id'];
	$request_for 	    = $rowfetchDetails['service_type'];
	$payment_mode 	    = $rowfetchDetails['invoice_mode'];
	$amount_refunded    = $rowfetchDetails['service_unit_price']==""?0.00:$rowfetchDetails['service_unit_price'];
	$request_for_ref_id = $rowfetchDetails['refference_id'];
	
	   $sqlProcessInsertStep 			 =	array();
	   $sqlProcessInsertStep['QUERY']    = "INSERT INTO "._DB_CANCEL_INVOICE_."
											   SET `delegate_id` = ?,
												   `slip_id`     = ?,
												   `invoice_id` = ?,
												   `request_for` = ?,
												   `request_for_ref_id` = ?,
												   `cancel_request` = ?,
												 
												   `payment_mode` = ?,
												   `transection_number`= ?,
												   `request_date` = ?,
												   `created_sessionId` = ?,
												   `created_browser` = ?,
												   `created_ip` = ?,
												   `created_dateTime` = ?";
												   
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'delegate_id',           'DATA' =>$delegate_id,               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'slip_id',         	  'DATA' =>$slip_id,               		  	   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'invoice_id',         	  'DATA' =>$invoice_id,               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'request_for',           'DATA' =>$request_for,               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'request_for_ref_id',    'DATA' =>$request_for_ref_id,                'TYP' => 's');

	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'cancel_request',    		'DATA' =>$cancel_request,'TYP' => 's');

	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'payment_mode',          'DATA' =>$payment_mode,               	   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'transection_number',    'DATA' =>$sessionId,               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'request_date',          'DATA' =>date('Y-m-d'),              		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId',  	  'DATA' =>session_id(),               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',       'DATA' =>$_SERVER['HTTP_USER_AGENT'],        'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',         	  'DATA' =>$_SERVER['REMOTE_ADDR'],            'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',      'DATA' =>date('Y-m-d H:i:s'),                'TYP' => 's');
	$cancelId = $mycms->sql_insert($sqlProcessInsertStep, false);
	
	$sqlProcessInsertStep				 =	array();
	$sqlProcessInsertStep['QUERY']       = "INSERT INTO "._DB_INVOICE_COPY_."
											   SET `invoice_id` = ?,
												   `request_type` = ?,
												   `created_sessionId` = ?,
												   `created_browser` = ?,
												   `created_ip` = ?,
												   `created_dateTime` = ?";
												   
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'invoice_id',           	  'DATA' =>$invoice_id,               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'request_type',         	  'DATA' =>'Request For Cancellation',         'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId',         'DATA' =>session_id(),               		   'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',           'DATA' =>$_SERVER['HTTP_USER_AGENT'],        'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',    			  'DATA' =>$_SERVER['REMOTE_ADDR'],            'TYP' => 's');
	$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',          'DATA' =>date('Y-m-d H:i:s'),                'TYP' => 's');
	$mycms->sql_insert($sqlProcessInsertStep, false);
	
	return $cancelId;
	//$mycms->redirect('profile.php');
}

function refundCancelInvoiceProcess($delegateId,$invoiceId,$curntUserId=0)
{
	global $cfg, $mycms;
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.registration.php');
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.accompany.php');
	$invoiceDetails = getInvoiceDetails($invoiceId);
	
   
	if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
	{
		 $reqId = $invoiceDetails['delegateId'];
		// REMOVING USER			
		$invoiceDetailsArr = invoiceDetailsOfDelegate($reqId,"");
		foreach($invoiceDetailsArr as $key => $rowInvoiceDetails)
		{
			$sqlUpdate			=	array();
			$sqlUpdate['QUERY'] = "UPDATE "._DB_INVOICE_."
									 SET `status` = ?
								   WHERE `id` = ?";
			$sqlUpdate['PARAM'][]   = array('FILD' => 'status',     'DATA' =>'C',               					  'TYP' => 's');
			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',         'DATA' =>$rowInvoiceDetails['id'],                'TYP' => 's');			   
			$mycms->sql_update($sqlUpdate, false);

		}

			$sqlRemove			=	array();
			$sqlRemove['QUERY']       = "UPDATE "._DB_USER_REGISTRATION_." 
										   SET `status` = ?,
												`account_status` = ?
										 WHERE `id`     = ?"; 
			$sqlRemove['PARAM'][]   = array('FILD' => 'status',     			'DATA' =>'C',               			'TYP' => 's');
			$sqlRemove['PARAM'][]   = array('FILD' => 'account_status',         'DATA' =>'UNREGISTERED',                'TYP' => 's');	
			$sqlRemove['PARAM'][]   = array('FILD' => 'id',         			'DATA' =>$reqId,                		'TYP' => 's');				
			$mycms->sql_update($sqlRemove,false);
			
			
	}
	
	if($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
	{
		$reqId = $invoiceDetails['delegateId'];			
		$invoiceDetailsArr = invoiceDetailsOfDelegate($reqId,"");
		foreach($invoiceDetailsArr as $key => $rowInvoiceDetails)
		{
			$sqlUpdate = "UPDATE "._DB_INVOICE_."
							 SET `status` = 'C'
						   WHERE `id` = '".$rowInvoiceDetails['id']."'";
						   
			$mycms->sql_update($sqlUpdate, false);
		}
		$sqlUpdate = "UPDATE "._DB_REQUEST_ACCOMMODATION_."
						 SET `status` = 'C'
					   WHERE `user_id` = '".$delegateId."'";
					   
		     $mycms->sql_update($sqlUpdate, false);
		
		$sqlUpdate = "UPDATE "._DB_REQUEST_WORKSHOP_."
						 SET `status` = 'C'
					   WHERE `delegate_id` = '".$reqId."'";
					   
		$mycms->sql_update($sqlUpdate, false);
		$sqlRemove       = "UPDATE "._DB_USER_REGISTRATION_." 
							   SET `status` = 'C',
							  	   `account_status` = 'UNREGISTERED'
							 WHERE `id`     ='".$reqId."'"; 
		
		$mycms->sql_update($sqlRemove,false);
	}
	
	if($invoiceDetails['service_type'] =="DELEGATE_WORKSHOP_REGISTRATION")
	{
		$reqId 	   = $invoiceDetails['delegateId'];
		$sqlUpdate			=	array();
		$sqlUpdate['QUERY'] = "UPDATE "._DB_REQUEST_WORKSHOP_."
								 SET `status` = ?
							   WHERE `refference_invoice_id` = ?";
		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',     			'DATA' =>'C',               							'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'refference_invoice_id',     			'DATA' =>$invoiceId,               			'TYP' => 's');			   
		$mycms->sql_update($sqlUpdate, false);
		
		$paymentStatusArr = registrationPaymentStatus($delegateId,"WORKSHOP");
		
		if($paymentStatusArr['PAYMENT_STATUS'] != 'ZERO VALUE')
		{
		$sqlUpdate			=	array();
		$sqlUpdate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
								 SET `workshop_payment_status` = ?
							   WHERE `id` = ?";
		$sqlUpdate['PARAM'][]   = array('FILD' => 'workshop_payment_status',     	'DATA' =>$paymentStatusArr['PAYMENT_STATUS'],  		'TYP' => 's');	
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',     						'DATA' =>$delegateId,  								'TYP' => 's');			   
		$mycms->sql_update($sqlUpdate, false);
		}
		$totalCount = getTotalWorkshopCount($delegatesId);
		if($totalCount == 0 || $totalCount == "")
		{
			$sqlUpdate			=	array();
			$sqlUpdate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
									 SET `isWorkshop` = ?,
										 `workshop_payment_status` = ?
								   WHERE `id` = ?";
			$sqlUpdate['PARAM'][]   = array('FILD' => 'isWorkshop',     				'DATA' =>'N',  			'TYP' => 's');	
			$sqlUpdate['PARAM'][]   = array('FILD' => 'workshop_payment_status',     	'DATA' =>'NULL',  		'TYP' => 's');	
			$sqlUpdate['PARAM'][]   = array('FILD' => 'id',     						'DATA' =>$delegatesId,  'TYP' => 's');				   
			$mycms->sql_update($sqlUpdate, false);
		}
		
	}
	
	if($invoiceDetails['service_type'] =="ACCOMPANY_CONFERENCE_REGISTRATION")
	{
		$reqId = $invoiceDetails['refference_id'];
		$sqlUpdate			=	array();
		$sqlUpdate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
								 SET `status` = 'C'
							   WHERE `id` = '".$reqId."'";
					   
		$mycms->sql_update($sqlUpdate, false);
		$totalCount = getTotalAccompanyCount($delegatesId);
		if($totalCount == 0 || $totalCount == "")
		{
			$sqlUpdate			=	array();
			$sqlUpdate['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
									 SET `isAccompany` = 'N'
								   WHERE `id` = '".$delegatesId."'";
						   
			$mycms->sql_update($sqlUpdate, false);
		}
	}
	
	if($invoiceDetails['service_type'] =="DELEGATE_ACCOMMODATION_REQUEST")
	{

		$sqlUpdate			=	array();
		$reqId = $invoiceDetails['refference_id'];
		$sqlUpdate['QUERY'] = "UPDATE "._DB_REQUEST_ACCOMMODATION_."
						 SET `status` = ?
					   WHERE `id` = ?";

		$sqlUpdate['PARAM'][]   = array('FILD' => 'status',     'DATA' =>'C',               					  'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'id',         'DATA' =>$reqId,                'TYP' => 's');					   
					   
		$mycms->sql_update($sqlUpdate, false);

		$paymentStatusArr = registrationPaymentStatus($delegateId,"ACCOMMODATION");

		$sqlUpdateUser			=	array();
		$sqlUpdateUser['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
						 SET `accommodation_payment_status` = ?
					   WHERE `id` = ?";


		$sqlUpdateUser['PARAM'][]   = array('FILD' => 'accommodation_payment_status',     'DATA' =>$paymentStatusArr['PAYMENT_STATUS'],               					  'TYP' => 's');
		$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',         'DATA' =>$delegateId,                'TYP' => 's');		

		$mycms->sql_update($sqlUpdateUser, false);

		$totalCount = getTotalAccommodationCount($delegatesId);
		if($totalCount == 0 || $totalCount == "")
		{
			$sqlUpdateUserReg			=	array();
			$sqlUpdateUserReg['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
							 SET `isAccommodation` = 'N',
							 	 `accommodation_payment_status` = ?
						   WHERE `id` = ?";

			$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'accommodation_payment_status',     'DATA' =>NULL,               					  'TYP' => 's');
			$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'id',         'DATA' =>$delegateId,                'TYP' => 's');			   
						   
			$mycms->sql_update($sqlUpdateUserReg, false);
		}
	}
	
	$sqlUpdate			=	array();
	$sqlUpdate['QUERY'] = "UPDATE "._DB_INVOICE_."
						 SET `status` = ?
					   WHERE `id` = ?";	
	$sqlUpdate['PARAM'][]   = array('FILD' => 'status',     			'DATA' =>'C',               			'TYP' => 's');	
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',     				'DATA' =>$invoiceId,            		'TYP' => 's');				   					   
	$mycms->sql_update($sqlUpdate, false);
}