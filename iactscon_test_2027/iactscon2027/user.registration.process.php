<?php
	include_once('includes/frontend.init.php');
	include_once("includes/function.frontend.registration.php");
	include_once("includes/function.edit.php");
	include_once("includes/function.registration.php");
	$act = $_REQUEST['act'];
	
	switch($act)
	{
		case'serviceSelection':	
			$mycms->setSession('WILL-HAVE-STEPS', 	'Y');
			$response = array();
			if(serviceSelection())
			{
				$response['status'] = 'SUCCESS';
			}
			else
			{
				$response['status'] = 'FAIL';
			}
			echo json_encode($response);
			exit();
			break;
		
		case'step1':
			$response = array();
			if(step1())
			{
				$response['status'] = 'SUCCESS';
			}
			else
			{
				$response['status'] = 'FAIL';
			}
			echo json_encode($response);
			exit();
			break;
			
		case'step3':
			$response = array();
			if(step3())
			{
				$response['status'] = 'SUCCESS';
			}
			else
			{
				$response['status'] = 'FAIL';
			}
			echo json_encode($response);
			exit();
			break;
			
		case'step6':
			
			include_once('includes/function.delegate.php');
			include_once('includes/function.invoice.php');
			include_once('includes/function.accompany.php');
			include_once('includes/function.workshop.php');
			include_once('includes/function.registration.php');
			include_once('includes/function.messaging.php');
			include_once('includes/function.accommodation.php');
			include_once("includes/function.dinner.php");
			$mycms->setSession('CURRENT_REG_USER','FINISH');	
							
			detailsInseringProcess($mycms->getSession('PROCESS_FLOW_ID_FRONT'));
							
		    $slipId	 		 = $mycms->getSession('SLIP_ID');
			
			$sqlWorkshopclsf = array();
			$sqlWorkshopclsf['QUERY'] = " SELECT *, IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount, 
												user.registration_classification_id AS registration_classification_id
										   FROM "._DB_SLIP_." slip 
									 INNER JOIN "._DB_USER_REGISTRATION_." user
											 ON slip.delegate_id = user.id
								LEFT OUTER JOIN ( SELECT SUM(`service_roundoff_price`) AS totalInvoice, `slip_id`
													FROM "._DB_INVOICE_." 
												   WHERE `status` = 'A'
												GROUP BY `slip_id` ) activeInvoiceAmount
											 ON slip.id = activeInvoiceAmount.slip_id
										  WHERE slip.status = ? 
											AND slip.id =?";
			
			$sqlWorkshopclsf['PARAM'][]  = array('FILD' => 'slip.status', 'DATA' =>'A',     'TYP' => 's');
			$sqlWorkshopclsf['PARAM'][]  = array('FILD' => 'slip.id',     'DATA' =>$slipId, 'TYP' => 's');
			
			$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);	
			
			$resWorkshopclsfres =$resWorkshopclsf[0];
			$clafId = $resWorkshopclsfres['registration_classification_id'];
			$delegateId = $resWorkshopclsfres['delegate_id'];
			$activeInvoiceAmount =$resWorkshopclsfres['activeInvoiceAmount'];
			
			$ADD_MORE_COUNTER = $mycms->getSession('ADD_MORE_COUNTER');
			if($ADD_MORE_COUNTER=='')
			{ 
				$ADD_MORE_COUNTER = 0;	
			}	
			 $mycms->setSession('ADD_MORE_COUNTER',intval($ADD_MORE_COUNTER)+1);
		
			$sqlProcessUpdateStep    = array();
			$sqlProcessUpdateStep['QUERY']       = " UPDATE  "._DB_PROCESS_STEP_."
												   SET `regitration_status` = ?
												 WHERE `id` = ?";
			
			$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'regitration_status',  'DATA' =>'COMPLETE',                              'TYP' => 's');
			$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',                  'DATA' =>$mycms->getSession('PROCESS_FLOW_ID'),   'TYP' => 's');	
														 
			$mycms->sql_update($sqlProcessUpdateStep, false);
			
			$mycms->getSession('REGISTRATION_MODE');
			if($activeInvoiceAmount ==0.00)
			{
				$payment_mode = $_REQUEST['payment_mode'];
		   		complementaryPaymentConfirmationProcess($slipId,$delegateId);
				$mycms->redirect("registration.success.php?did=".$mycms->encoded($delegateId));
				exit();
			}
			else
			{
				$mycms->getSession('REGISTRATION_MODE');
				if($mycms->getSession('REGISTRATION_MODE') == "OFFLINE")
				{
					$mycms->redirect("registration.offline.checkout.php");
					exit();
			 	}
			 	else if($mycms->getSession('REGISTRATION_MODE') == "ONLINE")
			 	{
					$mycms->redirect("registration.online.checkout.php");
					exit();
			 	}
			} 
			exit();
			break;
	}
	
	function serviceSelection()
	{
		global $mycms, $cfg;
		
		$dataArray = array();
		foreach($_REQUEST as $key=>$value)
		{
			$dataArray[$key] = $value;
		}
		
		$mycms->setSession('CUTOFF_ID_FRONT', 	$_REQUEST['cutoff_id']);
		$mycms->setSession('CLSF_ID_FRONT', 	$_REQUEST['registration_classification_id'][0]);
		
		$mycms->setSession('REGISTRATION_MODE', $_REQUEST['registrationMode']);
		
		$mycms->setSession('WORKSHOP_ID', 		$_REQUEST['workshop_id']);
		$mycms->setSession('DINNER_VALUE', 		$_REQUEST['dinner_value']);
		$mycms->setSession('HOTEL_ID', 			$_REQUEST['hotel_id']);			
		
		$regClsfId 								= $_REQUEST['registration_classification_id'][0];
		$dataArray['regClsfId'] 				= $regClsfId;
	
		$accmodationPackageId 					= $_REQUEST['accomPackId'];
		$accmodationDateSet 					= $_REQUEST['accDate'][$accmodationPackageId];
		
		$dataArray['accmodationPackageId'] 		= $accmodationPackageId;
		$dataArray['accmodationDateSet'] 		= $accmodationDateSet;
		
		
		$accDates 			= explode('-',$accmodationDateSet);
		$accCheckinDateId 	= $accDates[0];
		$accCheckOutDateId 	= $accDates[1];
		
		$dataArray['accCheckinDateId'] 			= $accCheckinDateId;
		$dataArray['accCheckOutDateId'] 		= $accCheckOutDateId;
		
		$mycms->setSession('STEP0_ACCM_PACKID', 		$accmodationPackageId);
		$mycms->setSession('STEP0_ACCM_CHECKINDATE',	$accCheckinDateId);
		$mycms->setSession('STEP0_ACCM_CHECKOUTDATE',	$accCheckOutDateId);
		
		$preffered_accommpany_name 				=  $_REQUEST['preffered_accommpany_name'];
		$preffered_accommpany_email 			=  $_REQUEST['preffered_accommpany_email'];
		$preffered_accommpany_mobile 			=  $_REQUEST['preffered_accommpany_mobile'];
		
		$dataArray['preffered_accommpany_name'] 	= $preffered_accommpany_name;
		$dataArray['preffered_accommpany_email'] 	= $preffered_accommpany_email;
		$dataArray['preffered_accommpany_mobile'] 	= $preffered_accommpany_mobile;
		
		$mycms->setSession('STEP0_PREF_ACMP_NAME', 	$preffered_accommpany_name);
		$mycms->setSession('STEP0_PREF_ACMP_EMAIL',	$preffered_accommpany_email);
		$mycms->setSession('STEP0_PREF_ACMP_MOB',	$preffered_accommpany_mobile);
		
		insertIntoProcessFlow("step0",$dataArray);
		
		return true;
	}
	
	function step1()
	{
		global $mycms, $cfg;
		
		$dataArray = array();
		foreach($_REQUEST as $key=>$value)
		{
			$dataArray[$key] = $value;
		}
		
		$dataArray['registration_classification_id'][]   	= $mycms->getSession('CLSF_ID_FRONT');						
		$dataArray['registration_cutoff'] 	        	 	= $mycms->getSession('CUTOFF_ID_FRONT');
		$dataArray['registrationMode']        			 	= $mycms->getSession('REGISTRATION_MODE');
		
		$dataArray['preffered_accommpany_name'] 	 		= $mycms->getSession('STEP0_PREF_ACMP_NAME');
		$dataArray['preffered_accommpany_email']  			= $mycms->getSession('STEP0_PREF_ACMP_EMAIL');
		$dataArray['preffered_accommpany_mobile'] 			= $mycms->getSession('STEP0_PREF_ACMP_MOB');	
		
		if($mycms->isSession('WORKSHOP_ID'))
		{
			foreach($mycms->getSession('WORKSHOP_ID') as $key=>$workshopId)
			{
			 	$dataArray['workshop_id'][] = $workshopId;	
			}
		}		
		if($mycms->isSession('DINNER_VALUE'))
		{
			foreach($mycms->getSession('DINNER_VALUE') as $key=>$dinnerValue)
			{
				$dataArray['dinner_value'][] = $dinnerValue;	
			}
		}		
		if($mycms->isSession('HOTEL_ID'))
		{
			$dataArray['hotel_id'] = $mycms->getSession('HOTEL_ID');	
		}		
		if($mycms->isSession('STEP0_ACCM_PACKID'))
		{
			$dataArray['accommodation_package_id'] = $mycms->getSession('STEP0_ACCM_PACKID');
		}
		if($mycms->isSession('STEP0_ACCM_CHECKINDATE'))
		{
			$dataArray['accommodation_checkIn'] = $mycms->getSession('STEP0_ACCM_CHECKINDATE');	
		}
		if($mycms->isSession('STEP0_ACCM_CHECKOUTDATE'))
		{
			$dataArray['accommodation_checkOut'] = $mycms->getSession('STEP0_ACCM_CHECKOUTDATE');
		}
				
		$USER_DETAILS_FRONT['NAME'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'].". ".$_REQUEST['user_first_name']." ".$_REQUEST['user_middle_name']." ".$_REQUEST['user_last_name'])));
		$USER_DETAILS_FRONT['EMAIL']	  = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
		$USER_DETAILS_FRONT['PH_NO'] 	  = addslashes(trim($_REQUEST['user_usd_code']." - ".$_REQUEST['user_mobile']));
		$USER_DETAILS_FRONT['CUTOFF']     = addslashes(trim($mycms->getSession('CUTOFF_ID_FRONT'))); 
		$USER_DETAILS_FRONT['CATAGORY']   = addslashes(trim($mycms->getSession('CLSF_ID_FRONT'))); 
		
		$accDetails	= getUserTypeAndRoomType($mycms->getSession('CLSF_ID_FRONT'));
		
		$mycms->setSession('USER_DETAILS_FRONT', $USER_DETAILS_FRONT);			
		
		insertIntoProcessFlow("step1",$dataArray);
		
		$mycms->removeSession('WORKSHOP_ID');
		$mycms->removeSession('DINNER_VALUE');
		$mycms->removeSession('HOTEL_ID');
		$mycms->removeSession('STEP0_ACCM_PACKID');
		$mycms->removeSession('STEP0_ACCM_CHECKINDATE');
		$mycms->removeSession('STEP0_ACCM_CHECKOUTDATE');		
		$mycms->removeSession('STEP0_PREF_ACMP_NAME');
		$mycms->removeSession('STEP0_PREF_ACMP_EMAIL');
		$mycms->removeSession('STEP0_PREF_ACMP_MOB');
		
		return true;
	}
	
	function step3()
	{
		global $mycms, $cfg;
		
		$dataArray = array();
		foreach($_REQUEST as $key=>$value)
		{
			$dataArray[$key] = $value;
		}
				
		$registrationClassificationId   			= $mycms->getSession('CLSF_ID_FRONT');
		$registrationCutoffId  	        			= $mycms->getSession('CUTOFF_ID_FRONT');
		$isAccompany	                			= $mycms->getSession('IS_ACCOMPANY');
		$no_accompany	                			= $mycms->getSession('NO_ACCOMPANY');
		//$accompanyCatagory              			= 2;
		$accompanyCatagory      					= 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 
		
		$registrationDetails 						= getAllRegistrationTariffs();	
		$registrationAmount 						= $registrationDetails[$accompanyCatagory][$registrationCutoffId]['AMOUNT'];
		$registrationCurrency 						= $registrationDetails[$accompanyCatagory][$registrationCutoffId]['CURRENCY'];
				
		$dataArray['accompanyTariffAmount']        	= $registrationAmount;
		$dataArray['registration_cutoff']        	= $registrationCutoffId;
		$dataArray['accompanyClasfId']        		= $accompanyCatagory;
		
		insertIntoProcessFlow("step3",$dataArray);
		
		$mycms->redirect("registration.notification.php");
		
		return true;
	}
	
	function insertIntoProcessFlow($stepName,$requestValues)
	{
		global $mycms, $cfg;
		
		$sessionId	    = session_id();
		$userIp		    = $_SERVER['REMOTE_ADDR'];
		$userBrowser    = $_SERVER['HTTP_USER_AGENT'];
		
		if($mycms->getSession('PROCESS_FLOW_ID_FRONT') == "")
		{
			$sqlProcessInsertStep = array();
			$sqlProcessInsertStep['QUERY']	  = "INSERT INTO "._DB_PROCESS_STEP_."  
												  SET `".$stepName."` 	    = ?,
													  `created_ip`          = ?,
													  `reg_area`            = ?,
													  `created_sessionId`   = ?,
													  `created_browser`     = ?,
													  `created_dateTime`    = ?";
										  
			$sqlProcessInsertStep['PARAM'][]   = array('FILD' => $stepName,           'DATA' =>serialize($requestValues),    'TYP' => 's');
			$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' =>$userIp,                      'TYP' => 's');
			$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'reg_area',          'DATA' =>'FRONT',                      'TYP' => 's');
			$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' =>$sessionId,                   'TYP' => 's');
			$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',   'DATA' =>$userBrowser,                 'TYP' => 's');
			$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' =>date('Y-m-d H:i:s'),          'TYP' => 's');
			
			$id = $mycms->sql_insert($sqlProcessInsertStep, false);
			$mycms->setSession('PROCESS_FLOW_ID_FRONT',$id);	
			$mycms->setSession('REGISTRATION_TOKEN', "#".$mycms->getSession('PROCESS_FLOW_ID_FRONT'));		
		}
		else
		{	
			$sql  			= array();
			$sql['QUERY'] 	= "SELECT `".$stepName."` AS theData 
								 FROM "._DB_PROCESS_STEP_."
								WHERE `id` = ?";	
			$sql['PARAM'][] = array('FILD' => 'id', 	'DATA' =>$mycms->getSession('PROCESS_FLOW_ID_FRONT'),	'TYP' => 's');	
			$result       	= $mycms->sql_select($sql); 
			$row			= $result[0];
			if(trim($row['theData'])!='')
			{
				$theData =  unserialize(trim($row['theData']));
			}
			else
			{
				$theData =  array();
			}
			
			foreach($requestValues as $key=>$value)
			{
				$theData[$key] = $value;
			}
			
			$sqlProcessUpdateStep = array();
			$sqlProcessUpdateStep['QUERY']       = "UPDATE  "._DB_PROCESS_STEP_."
													   SET `".$stepName."` 		= ?,
														   `created_dateTime`   = ?
													 WHERE `id` = ?";												 
			$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => $stepName,          'DATA' =>serialize($theData),     				 		'TYP' => 's');
			$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'created_dateTime', 'DATA' =>date('Y-m-d H:i:s'), 							'TYP' => 's');
			$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$mycms->getSession('PROCESS_FLOW_ID_FRONT'),	'TYP' => 's');												 
			$mycms->sql_update($sqlProcessUpdateStep, false);			
		}
	}
?>