<?php
	include_once('includes/init.php');
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.invoice.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.registration.php');
	$loggedUserID = $mycms->getLoggedUserId();
	$delegateId                     =  addslashes($_REQUEST['delegateId']);
	$slipId		                    =  addslashes($_REQUEST['slipId']);
	// FETCHING USER DETAILS
	$rowFetchUserDetails        	= getUserDetails($delegateId);
	
	$user_id                    	= $rowFetchUserDetails['id'];
	$user_first_name            	= $rowFetchUserDetails['user_first_name'];
	$user_middle_name           	= $rowFetchUserDetails['user_middle_name'];
	$user_last_name             	= $rowFetchUserDetails['user_last_name'];
	$user_full_name             	= $rowFetchUserDetails['user_full_name'];
	$user_password              	= $mycms->decoded($rowFetchUserDetails['user_password']);
	$user_email_id              	= $rowFetchUserDetails['user_email_id'];
	$user_mobile_no             	= $rowFetchUserDetails['user_mobile_no'];
	
	$user_unique_sequence           = $rowFetchUserDetails['user_unique_sequence'];
	$user_registration_id           = $rowFetchUserDetails['user_registration_id'];
	
	$registerForArray               = array();
	$payCurrency 					= getRegistrationCurrency(getUserClassificationId($delegateId));
	$user_conference_title          = $rowFetchUserDetails['classification_title'];
	$user_workshop_title            = "-";
	$user_accompany_count           = "0";
	$user_accommodation_details     = "-";
	$user_tour_details              = "-";
	
	 $sqlFetchRawPayment 			=	array();
     $sqlFetchRawPayment['QUERY']   = "SELECT * FROM "._DB_PAYMENT_REQUEST_." 
													   WHERE `delegate_id` = ? 
													     AND `slip_id` = ?";
	$sqlFetchRawPayment['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' =>$delegateId,  'TYP' => 's');
	$sqlFetchRawPayment['PARAM'][]   = array('FILD' => 'slip_id', 	   'DATA' =>$slipId,  'TYP' => 's');
											 
	$resultRawPayment          	= $mycms->sql_select($sqlFetchRawPayment);
	$rowfetchDetails            = $resultRawPayment[0];

	// PAYMENT GATEWAY [ATOM] PARAMETERS
	$online_transaction_req_id      = $rowfetchDetails['id'];
	$mmp_txn                    	= addslashes($_REQUEST['mmp_txn']);
	$mer_txn                    	= addslashes($_REQUEST['mer_txn']);
	$amt                        	= invoiceAmountOfSlip($slipId);
	$surcharge                  	= addslashes($_REQUEST['surcharge']);
	$prodid                     	= addslashes($_REQUEST['prodid']);
	$date                       	= addslashes($_REQUEST['date']);
	$bank_txn                   	= addslashes($_REQUEST['bank_txn']);
	$f_code                     	= 'Ok';
	$clientcode                 	= addslashes($_REQUEST['clientcode']);
	$bank_name                  	= addslashes($_REQUEST['bank_name']);	
	$discriminator              	= addslashes($_REQUEST['discriminator']);
	$cardNumber                 	= addslashes($_REQUEST['CardNumber']);
	$paymentAmount              	= 0; 
	
	$transactionRawData         	= $mycms->_getValueOf($_REQUEST);
	$transactionRawData         	= addslashes($transactionRawData);
	$resultInvoice 					= invoiceDetailsActiveOfSlip($slipId);
	
	// INSERTING PAYMENT RAW DATA
	$paymentStatus                  = "";
	
	if($f_code=="Ok")
	{	
		$paymentStatus              = "SUCCESS";
	}
	else
	{		
		$paymentStatus              = "FAILURE";
	}
	
	$sqlInsertPaymentData 			=	array();
	$sqlInsertPaymentData['QUERY']   		= "INSERT INTO "._DB_PAYMENT_RAW_DATA_." 
													   SET `slip_id` = ?, 
														   `delegate_id` = ?,
														   `raw_data` = ?,
														   `payment_status` = ?,
														   `payment_mode` = ?, 
														   `payment_date` = ?,
														   `online_transaction_gateway` = ?,
														   `atom_atom_transaction_id` = ?,
														   `atom_merchant_transaction_id` = ?,
														   `atom_transaction_amount` = ?,
														   `atom_surcharge` = ?,
														   `atom_product_id` = ?,
														   `atom_bank_transaction_id` = ?,
														   `atom_f_code` = ?,
														   `atom_transaction_bank_name` = ?,
														   `atom_discriminator` = ?,
														   `atom_transaction_card_no` = ?,
														   `amount` = ?,
														   `status` = ?, 
														   `created_ip` = ?, 
														   `created_sessionId` = ?,
														   `created_browser` = ?,
														   `created_dateTime` = ?";
			  
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'slip_id', 					   'DATA' =>$slipId,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'delegate_id',				   'DATA' =>$delegateId,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'raw_data', 					   'DATA' =>$transactionRawData,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'payment_status', 			   'DATA' =>$paymentStatus,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'payment_mode', 				   'DATA' =>'Online',  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'payment_date', 				   'DATA' =>date('Y-m-d'),  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'online_transaction_gateway',   'DATA' =>'ATOM',  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_atom_transaction_id',     'DATA' =>$mmp_txn,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_merchant_transaction_id', 'DATA' =>$mer_txn,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_transaction_amount', 	   'DATA' =>$paymentAmount,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_surcharge', 			   'DATA' =>$surcharge,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_product_id',              'DATA' =>$prodid,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_bank_transaction_id', 	   'DATA' =>$bank_txn,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_f_code', 				   'DATA' =>$f_code,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_transaction_bank_name',   'DATA' =>$bank_name,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_discriminator', 		   'DATA' =>$discriminator,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'atom_transaction_card_no', 	   'DATA' =>$cardNumber,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'amount', 					   'DATA' =>$paymentAmount,  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'status', 					   'DATA' =>'A',  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'created_ip', 				   'DATA' =>$_SERVER['REMOTE_ADDR'],  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'created_sessionId', 		   'DATA' =>session_id(),  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'created_browser', 			   'DATA' =>$_SERVER['HTTP_USER_AGENT'],  'TYP' => 's');
	  $sqlInsertPaymentData['PARAM'][]   = array('FILD' => 'created_dateTime', 			   'DATA' =>date('Y-m-d H:i:s'),  'TYP' => 's');
	  $mycms->sql_insert($sqlInsertPaymentData, false);
	// INSERTING PAYMENT DETAILS TO PROJECT DB
	if($f_code=="Ok")
	{
		// COUNTING PREVIOUS PAYMENT RECORD
		$sqlFetchPayment 			=	array();
		$sqlFetchPayment['QUERY']        	= "SELECT * FROM "._DB_PAYMENT_." 
													   WHERE `delegate_id` = ? 
													     AND `slip_id` = ? 
														 AND `payment_mode` = ? 
														 AND `online_transaction_gateway` = ?
														 AND `atom_atom_transaction_id` = ?
														 AND `atom_merchant_transaction_id` = ?
														 AND `atom_transaction_amount` = ?
														 AND `atom_bank_transaction_id` = ?
														 AND `atom_f_code` = ?";

		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'delegate_id', 'DATA' =>$delegateId,  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'slip_id', 'DATA' =>$slipId,  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'payment_mode', 'DATA' =>'Online',  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'online_transaction_gateway', 'DATA' =>'ATOM',  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'atom_atom_transaction_id', 'DATA' =>$mmp_txn,  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'atom_merchant_transaction_id', 'DATA' =>$mer_txn,  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'atom_transaction_amount', 'DATA' =>$paymentAmount,  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'atom_bank_transaction_id', 'DATA' =>$bank_txn,  'TYP' => 's');
		 $sqlFetchPayment['PARAM'][]   = array('FILD' => 'atom_f_code', 'DATA' =>$f_code,  'TYP' => 's');

		$resultPayment          	= $mycms->sql_select($sqlFetchPayment);
		$paymentNumRows         	= $mycms->sql_numrows($resultPayment);
		
		if($paymentNumRows==0)
		{
			$paymentAmount          = $amt;
			
			// INSERTING PAYMENT DETAILS
			$sqlInsertPayment               = array();
			$sqlInsertPayment['QUERY']    	= "INSERT INTO "._DB_PAYMENT_." 
											   SET `delegate_id` = ?,
											       `slip_id` = ?, 
												   `payment_mode` = ?, 
												   `payment_date` = ?,
												   `online_transaction_gateway` = ?,
												   `online_transaction_req_id`  = ?,
												   `atom_atom_transaction_id` = ?,
												   `atom_merchant_transaction_id` = ?,
												   `atom_transaction_amount` = ?,
												   `atom_surcharge` = ?,
												   `atom_product_id` = ?,
												   `atom_transaction_date` = ?,
												   `atom_bank_transaction_id` = ?,
												   `atom_f_code` = ?,
												   `atom_transaction_bank_name` = ?,
												   `atom_discriminator` = ?,
												   `atom_transaction_card_no` = ?,
												   `currency` = ?,
												   `amount` = ?,
												   `payment_status` = ?,
												   `status` = ?, 
												   `created_ip` = ?, 
												   `created_sessionId` = ?,
												   `created_browser` = ?,
												   `created_dateTime` = ?";
			
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'delegate_id',                    'DATA' =>$delegateId,                  'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'slip_id',                        'DATA' =>$slipId,                      'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'payment_mode',                   'DATA' =>'Online',                     'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'payment_date',                   'DATA' =>date('Y-m-d'),                'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'online_transaction_gateway',     'DATA' =>'ATOM',                       'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'online_transaction_req_id',      'DATA' =>$online_transaction_req_id,   'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_atom_transaction_id',        'DATA' =>$mmp_txn,                    'TYP' => 's');
	   	    $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_merchant_transaction_id',    'DATA' =>$mer_txn,                    'TYP' => 's');
		    $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_amount',         'DATA' =>$paymentAmount,              'TYP' => 's');
	        $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_surcharge',                  'DATA' =>$surcharge,                  'TYP' => 's');
	        $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_product_id',                 'DATA' =>$prodid,                     'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_date',           'DATA' =>$date,                       'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_bank_transaction_id',        'DATA' =>$bank_txn,                   'TYP' => 's');
	        $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_f_code',                     'DATA' =>$f_code,                     'TYP' => 's');
	        $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_bank_name',      'DATA' =>$bank_name,                  'TYP' => 's');
	        $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_discriminator',              'DATA' =>$discriminator,              'TYP' => 's');
	        $sqlInsertPayment['PARAM'][]  = array('FILD' => 'atom_transaction_card_no',        'DATA' =>$cardNumber,                 'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'currency',                        'DATA' =>$payCurrency,                'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'amount',                          'DATA' =>$paymentAmount,              'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'payment_status',                  'DATA' =>'PAID',                      'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'status',                          'DATA' =>'A',                         'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_ip',                      'DATA' =>$_SERVER['REMOTE_ADDR'],     'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_sessionId',               'DATA' =>session_id(),                'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_browser',                 'DATA' =>$_SERVER['HTTP_USER_AGENT'], 'TYP' => 's');
			$sqlInsertPayment['PARAM'][]  = array('FILD' => 'created_dateTime',                'DATA' =>date('Y-m-d H:i:s'),         'TYP' => 's');
			
			
			$insertedPayId = $mycms->sql_insert($sqlInsertPayment, false);
			
			
			$sqlUpdateSlip        = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE "._DB_SLIP_."
												SET `payment_status` = ?
											  WHERE `id` = ?";		
											  
			$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'PAID',         'TYP' => 's');
			$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                'DATA' =>$slipId,         'TYP' => 's');									  
			$mycms->sql_update($sqlUpdateSlip, false);
			
			$activeInvoice 		  = invoiceDetailsActiveOfSlip($slipId);
			$isResidential		  = 'NO';
			$isConference 		  = "NO";
			$whoToMailnWhat 	  = array();
			$isOnlyWorkshop		  = "NO";
			foreach($activeInvoice as $keyActiveInvoice => $valActiveInvoice)
			{
				if($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION')
				{
					$isConference 		= "YES";
					$isResidential	    = 'NO';
					$sqlUpdateSlip       = array();				
					$sqlUpdateSlip['QUERY']	      = "UPDATE "._DB_USER_REGISTRATION_."
														SET `registration_payment_status` = ?
													  WHERE `id` = ?";	
											  
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'registration_payment_status',    'DATA' =>'PAID',                             'TYP' => 's');
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                             'DATA' =>$valActiveInvoice['refference_id'],  'TYP' => 's');										  										  
					$mycms->sql_update($sqlUpdateSlip, false);
					
					$whoToMailnWhat[$valActiveInvoice['id']]['CONF_TYP'] = $valActiveInvoice['service_type'];
					$whoToMailnWhat[$valActiveInvoice['id']]['DELG_ID'] = $valActiveInvoice['delegate_id'];
				}
				
				if($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION')
				{
					$isResidential		  = 'YES';
					$sqlUpdateSlip       = array();				
					$sqlUpdateSlip['QUERY']	      = "UPDATE "._DB_USER_REGISTRATION_."
														SET `registration_payment_status` = ?,
															`accommodation_payment_status` = ?
															WHERE `id` = ?";	
											  
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'registration_payment_status',    'DATA' =>'PAID',                             'TYP' => 's');
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'accommodation_payment_status',   'DATA' =>'PAID',                             'TYP' => 's');
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                             'DATA' =>$valActiveInvoice['refference_id'],  'TYP' => 's');										  
											  
					$mycms->sql_update($sqlUpdateSlip, false);

					$sqlUpdate1 = array();
					$sqlUpdate1['QUERY'] = "UPDATE "._DB_REQUEST_ACCOMMODATION_."  
												SET `payment_status` = ? 
												WHERE `user_id` = ? 
												AND `status` = ?";
												
					$sqlUpdate1['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'PAID',                               'TYP' => 's');
					$sqlUpdate1['PARAM'][]  = array('FILD' => 'user_id',           'DATA' =>$valActiveInvoice['refference_id'],   'TYP' => 's');
					$sqlUpdate1['PARAM'][]  = array('FILD' => 'status',            'DATA' =>'A',                                  'TYP' => 's');
					$mycms->sql_update($sqlUpdate1,false);
					
					$sqlUpdateSlip   = array();				
					$sqlUpdateSlip['QUERY']	     = "UPDATE "._DB_REQUEST_WORKSHOP_."
														SET `payment_status` = ?
													  WHERE `id` = ?";	
													  
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'PAID',                               'TYP' => 's');	
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                   'DATA' =>$valActiveInvoice['refference_id'],   'TYP' => 's');

					$mycms->sql_update($sqlUpdateSlip,false);
					
					$whoToMailnWhat[$valActiveInvoice['id']]['CONF_TYP'] = $valActiveInvoice['service_type'];
					$whoToMailnWhat[$valActiveInvoice['id']]['DELG_ID'] = $valActiveInvoice['delegate_id'];
				}
				
				if($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION')
				{
					$isOnlyWorkshop		  = "YES";
					$workshopId			  = $valActiveInvoice['refference_id'];
					$sqlUpdateSlip   = array();				
					$sqlUpdateSlip['QUERY']	     = "UPDATE "._DB_REQUEST_WORKSHOP_."
														SET `payment_status` = ?
													  WHERE `id` = ?";	
													  
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'PAID',                               'TYP' => 's');	
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                   'DATA' =>$valActiveInvoice['refference_id'],   'TYP' => 's');
														  									  
					$mycms->sql_update($sqlUpdateSlip, false);
					
					$sqlUpdate = array();
					$sqlUpdate['QUERY']		 = "UPDATE "._DB_USER_REGISTRATION_."
												SET `workshop_payment_status` = ?
											  WHERE `id` = ?";	
											  
					$sqlUpdate['PARAM'][]  = array('FILD' => 'workshop_payment_status',    'DATA' =>'PAID',                               'TYP' => 's');	
					$sqlUpdate['PARAM'][]  = array('FILD' => 'id',                         'DATA' =>$valActiveInvoice['delegate_id'],   'TYP' => 's');										  
					$mycms->sql_update($sqlUpdate, false);
				}
				
				if($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION')
				{
					$isOnlyAccompany	  = 'YES';
					$sqlUpdateSlip = array();				
					$sqlUpdateSlip['QUERY']	      = "UPDATE "._DB_USER_REGISTRATION_."
														SET `registration_payment_status` = ?
													  WHERE `id` = ?";
					
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'registration_payment_status',    'DATA' =>'PAID',                            'TYP' => 's');
					$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                             'DATA' =>$valActiveInvoice['refference_id'], 'TYP' => 's');					
											  
					$mycms->sql_update($sqlUpdateSlip, false);
					
				}
				if($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST')
				{					
					$isOnlyAccommodation  = 'YES';	
					
					/*					
					$sqlUpdateSlip	      = "UPDATE ".$cfg['DB.REQUEST.ACCOMMODATION']."
												SET `payment_status` = 'PAID'
											  WHERE `id` = '".$valActiveInvoice['refference_id']."'";
											  
					$mycms->sql_update($sqlUpdateSlip, false);
					*/


					$sqlUpdateSlip = array();
					$sqlUpdateSlip['QUERY']	 = "UPDATE "._DB_REQUEST_ACCOMMODATION_."
												SET `payment_status` = ?
											  WHERE `id` = ?";
														  
					$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'PAID',  'TYP' => 's');
					$sqlUpdateSlip['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$valActiveInvoice['refference_id'],   'TYP' => 's');		
										 
					$mycms->sql_update($sqlUpdateSlip, false);

					/*
					$sqlUpdate		      = "UPDATE ".$cfg['DB.USER.REGISTRATION']."
												SET `accommodation_payment_status` = 'PAID'
											  WHERE `id` = '".$valActiveInvoice['delegate_id']."'";
											  
					$mycms->sql_update($sqlUpdate, false);
					*/

					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE "._DB_USER_REGISTRATION_."
												SET `accommodation_payment_status` = ?
											  WHERE `id` = ?";
														  
					$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'PAID',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$valActiveInvoice['delegate_id'],   'TYP' => 's');		
										 
					$mycms->sql_update($sqlUpdate, false);


					$sqlUpdateSlip1	     = array();
					$sqlUpdateSlip1['QUERY']	      = "UPDATE "._DB_INVOICE_."
														SET `payment_status` = ?
													  WHERE `id` = ?";	
												
					$sqlUpdateSlip1['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'PAID',                            'TYP' => 's');
					$sqlUpdateSlip1['PARAM'][]  = array('FILD' => 'id',                'DATA' =>$valActiveInvoice['id'],           'TYP' => 's');
										  
					$mycms->sql_update($sqlUpdateSlip1, false);
				}
				
				if($valActiveInvoice['service_type'] == 'DELEGATE_DINNER_REQUEST')
				{					
					if($isOnlyAccompany == 'NO')
					{
						$isOnlyDinner  		  = 'YES';	
					}	
					$sqlUpdateSlip 	=	array();								
					$sqlUpdateSlip['QUERY']	      = "UPDATE "._DB_REQUEST_DINNER_."
												SET `payment_status` = ?
											  WHERE `id` = ?";
					$sqlUpdateSlip['PARAM'][]	=	array('FILD' => 'payment_status' , 	  'DATA' => 'PAID' ,   'TYP' => 's');						  
					$sqlUpdateSlip['PARAM'][]	=	array('FILD' => 'id' , 	  			   'DATA' => $valActiveInvoice['refference_id'] ,   'TYP' => 's');						  
					$mycms->sql_update($sqlUpdateSlip, false);
				}
				
				$sqlUpdateSlip	     = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE "._DB_INVOICE_."
													SET `payment_status` = ?
												  WHERE `id` = ?
													AND `payment_status` = ?";	
											
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'PAID',                            'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'id',                'DATA' =>$valActiveInvoice['id'],           'TYP' => 's');
				$sqlUpdateSlip['PARAM'][]  = array('FILD' => 'payment_status',    'DATA' =>'UNPAID',                          'TYP' => 's');										  
				$mycms->sql_update($sqlUpdateSlip, false);
			}
			
			
			foreach($whoToMailnWhat as $invId => $rowdetails)
			{
				$delegate_id = $rowdetails['DELG_ID'];
				
				//send_registration_ack_message($delegate_id, 'SEND');
				
				if($rowdetails['CONF_TYP']=='DELEGATE_CONFERENCE_REGISTRATION')
				{
					$isOnlyWorkshop		  = "NO";
					$isOnlyAccompany	  = 'NO';
					$isOnlyAccommodation  = 'NO';	
					$isOnlyDinner  		  = 'NO';	
					
					online_conference_registration_confirmation_message($delegate_id,$insertedPayId, $slipId, 'SEND');
				}
				elseif($rowdetails['CONF_TYP']=='DELEGATE_RESIDENTIAL_REGISTRATION')
				{
					$isOnlyWorkshop		  = "NO";
					$isOnlyAccompany	  = 'NO';
					$isOnlyAccommodation  = 'NO';	
					$isOnlyDinner  		  = 'NO';	
					
					online_conference_registration_confirmation_message($delegate_id,$insertedPayId, $slipId, 'SEND');
				}
					
			}
			if($isConference == "NO" && $isOnlyAccompany == 'YES')
			{
				online_conference_registration_confirmation_accompany_message($delegateId,$insertedPayId, $slipId, 'SEND');
			}
			elseif($isConference == "NO" && $isOnlyWorkshop == 'YES')
			{
				online_conference_registration_confirmation_workshop_message($delegateId,$insertedPayId, $slipId, 'SEND');
			}
			elseif($isConference == "NO" && $isOnlyAccommodation == 'YES')
			{
				online_accommodation_confirmation_message($delegateId,$insertedPayId, $slipId, 'SEND');
			}
			elseif($isConference == "NO" && $isOnlyDinner == 'YES')
			{
				online_dinner_confirmation_message($delegateId,$insertedPayId, $slipId, 'SEND');
			}
			// online_conference_payment_confirmation_message($delegateId, $slipId, $insertedPayId, 'SEND');
			
			$mycms->removeSession('REGISTRATION_MODE');
			$mycms->removeSession('CURRENCY');
		}
		
		  $mycms->redirect("registration.php?show=invoice&id=".$delegateId."");
	}     
	else
	{ 
		online_conference_payment_failure_message($delegateId, $slipId, $insertedPayId,'SEND');
		//$mycms->redirect("online.payment.failure.php");
	}
?>
	