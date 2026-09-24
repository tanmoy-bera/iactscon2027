<?php
	include_once('includes/frontend.init.php');
	include_once("includes/function.frontend.registration.php");
	include_once("includes/function.edit.php");
		
	$act     = $_REQUEST['act'];
	
	switch($act)
	{
		case'regTariffSelection':							
			$mycms->setSession('CUTOFF_ID_FRONT', $_REQUEST['cutoff_id']);
			$mycms->setSession('CLSF_ID_FRONT', $_REQUEST['registration_classification_id'][0]);
			$mycms->setSession('REGISTRATION_MODE', $_REQUEST['registrationMode']);
			$mycms->setSession('WORKSHOP_ID', $_REQUEST['workshop_id']);
						
			if($mycms->getSession('CURRENT_REG_USER')== 'ADDON')
			{
				$mycms->redirect("registration.php");
			}
			else
			{
				$mycms->redirect("registration-checkout.php");
			}
			
			exit();
			break;
			
		case'registrationCheckout':		
			$mycms->setSession('COMMUNICATION_EMAIL',addslashes(trim(strtolower($_REQUEST['user_email_id']))));
			if($_REQUEST['email_id_validation']=='AVAILABLE')
			{
				$mycms->redirect("registration.php");
			}
			else if($_REQUEST['email_id_validation']=='IN_USE')
			{
				$sqlFetch['QUERY'] = "SELECT * FROM ".$cfg['DB.USER.REGISTRATION']."
													WHERE `user_email_id` = '".$mycms->getSession('COMMUNICATION_EMAIL')."' 
												  AND `status` = 'A' 
												  AND `registration_payment_status` = 'PAID' ";
				$resultUser              = $mycms->sql_select($sqlFetch);							 
			
			
				if($resultUser)
				{
					$rowUser                = $resultUser[0];
					$mycms->setSession('OTP.ID',$mycms->decoded($rowUser['user_password']));
					$mycms->setSession('TEMP.ID',$rowUser['id']);
					$mycms->redirect('login.php');
													  
				}
				else
				{
					$mycms->redirect('notpaid.php');
				}
			}
			else if($_REQUEST['email_id_validation']=='NOT_PAID')
			{
			   $sqlFetch['QUERY'] = "SELECT id AS delegateId FROM ".$cfg['DB.USER.REGISTRATION']."
													WHERE `user_email_id` = '".addslashes(trim($_REQUEST['user_email_id']))."' 
												  AND `status` = 'A' 
												  AND `registration_payment_status` = 'UNPAID' ";
				$resultUser              = $mycms->sql_select($sqlFetch);
				$rowUser                = $resultUser[0];
				
				$mycms->setSession('LOGGED.USER.ID',$rowUser['delegateId']);
				$mycms->setSession('IS_LOGIN',"YES");
				$mycms->removeSession('OTP.ID');
				$mycms->removeSession('TEMP.ID');
				$mycms->removeSession('USER.EM');
				$mycms->removeSession('USER.ID');
				$mycms->removeSession('USER.PWD');
				$mycms->removeSession('USER.TOKEN');	
				$mycms->redirect('invoice.php');
			}
			exit();
			break;
			
		case'step1':
			$sessionId	    = session_id();
			$userIp		    = $_SERVER['REMOTE_ADDR'];
			$userBrowser    = $_SERVER['HTTP_USER_AGENT'];
			$requestValues  = serialize($_REQUEST);

			$USER_DETAILS_FRONT['NAME'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'].". ".$_REQUEST['user_first_name']." ".$_REQUEST['user_middle_name']." ".$_REQUEST['user_last_name'])));
			$USER_DETAILS_FRONT['EMAIL']	  = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
			$USER_DETAILS_FRONT['PH_NO'] 	  = addslashes(trim($_REQUEST['user_usd_code']." - ".$_REQUEST['user_mobile']));
			$USER_DETAILS_FRONT['CUTOFF']     = addslashes(trim($mycms->getSession('CUTOFF_ID_FRONT'))); 
			$USER_DETAILS_FRONT['CATAGORY']   = addslashes(trim($mycms->getSession('CLSF_ID_FRONT'))); 
			
			$mycms->setSession('USER_DETAILS_FRONT', $USER_DETAILS_FRONT);
						
			if($mycms->getSession('PROCESS_FLOW_ID_FRONT') == "")
			{
				$sqlProcessInsertStep           = "INSERT INTO ".$cfg['DB.PROCESS.STEP']."
														   SET `step1` 		= '".addslashes($requestValues)."',
														   	   `created_ip` = '".$userIp."',
															   `reg_area` = 'FRONT',
															   `created_sessionId` = '".$sessionId."',
															   `created_browser` = '".$userBrowser."',
															   `created_dateTime` = '".date('Y-m-d H:i:s')."'";
				$mycms->setSession('PROCESS_FLOW_ID_FRONT',$mycms->sql_insert($sqlProcessInsertStep, false));
				
			}
			else
			{	
				$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
													   SET `step1` 		= '".$requestValues."',
														   `created_dateTime` = '".date('Y-m-d H:i:s')."'
													 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";
														 
				$mycms->sql_update($sqlProcessUpdateStep, false);
			}
			$mycms->removeSession('WORKSHOP_ID');
			
			if(!$mycms->isSession('REGISTRATION_TOKEN'))
			{
				$mycms->setSession('REGISTRATION_TOKEN', "#".$mycms->getSession('PROCESS_FLOW_ID_FRONT'));
			}
						
			$mycms->redirect("registration.accompany.php");
			
			exit();
			break;
			
		case'step2':
			$requestValues  = serialize($_REQUEST);
			
			$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
												   SET `step2` = '".addslashes($requestValues)."'
												 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";													 
			$mycms->sql_update($sqlProcessUpdateStep, false);
			
			$mycms->redirect("registration.accomodation.php");
			
			exit();
			break;
			
		case'step3':
			$requestValues  = serialize($_REQUEST);
			$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
												   SET `step3` = '".addslashes($requestValues)."'
												 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";												 
			$mycms->sql_update($sqlProcessUpdateStep, false);
			$mycms->redirect("registration.notification.php");			 
			exit();
			break;
			
		case'step4':
			$requestValues  = serialize($_REQUEST);			
			$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
												   SET `step4` = '".addslashes($requestValues)."'
												 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";													 
			$mycms->sql_update($sqlProcessUpdateStep, false);			
			$mycms->redirect("registration.notification.php");			 
			exit();
			break;
			
		case'step5':
			$requestValues  = serialize($_REQUEST);
			$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
												   SET `step5` = '".addslashes($requestValues)."'
												 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";													 
			$mycms->sql_update($sqlProcessUpdateStep, false);			
			$mycms->redirect("registration.notification.php");			 
			exit();
			break;
			
		case'step6':
			$mycms->setSession('CURRENT_REG_USER','FINISH');			
			// DETAILS INSERTING PROCESS			
			detailsInseringProcess($mycms->getSession('PROCESS_FLOW_ID_FRONT'));
			
			$ADD_MORE_COUNTER = $mycms->getSession('ADD_MORE_COUNTER');
			if($ADD_MORE_COUNTER=='')
			{ 
				$ADD_MORE_COUNTER = 0;	
			}	
			 $mycms->setSession('ADD_MORE_COUNTER',intval($ADD_MORE_COUNTER)+1);
		
			$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
												   SET `regitration_status` = 'COMPLETE'
												 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";													 
			$mycms->sql_update($sqlProcessUpdateStep, false);			
			
			if($mycms->getSession('REGISTRATION_MODE') == "OFFLINE")
			{
				$mycms->redirect("registration.offline.checkout.php");
			}
			else if($mycms->getSession('REGISTRATION_MODE') == "ONLINE")
			{
				$mycms->redirect("registration.online.checkout.php");
			}			 
			exit();
			break;
			
		case'addMoreDelegate':
			$mycms->setSession('CURRENT_REG_USER','ADDON');
						
			$USER_DETAILS_FRONT = $mycms->getSession('USER_DETAILS_FRONT');			
			$mycms->setSession('CURRENCY',getCurrency($USER_DETAILS_FRONT['CATAGORY']));
			detailsInseringProcess($mycms->getSession('PROCESS_FLOW_ID_FRONT'));
			
			$ADD_MORE_COUNTER = $mycms->getSession('ADD_MORE_COUNTER');
			if($ADD_MORE_COUNTER=='')
			{ 
				$ADD_MORE_COUNTER = 0;	
			}		
			$mycms->setSession('ADD_MORE_COUNTER',intval($ADD_MORE_COUNTER)+1);
			
			$sqlProcessUpdateStep           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
												   SET `regitration_status` = 'COMPLETE'
												 WHERE `id` = '".$mycms->getSession('PROCESS_FLOW_ID_FRONT')."'";													 
			$mycms->sql_update($sqlProcessUpdateStep, false);			
			
			// DETAILS INSERTING PROCESS
			$mycms->removeSession('PROCESS_FLOW_ID_FRONT');			
			$mycms->redirect("registration.tariff.php?".$mycms->encoded('addMore'));
			 
			exit();
			break;
		
		case'gotoAddMoreDelegate':
			$mycms->setSession('CURRENT_REG_USER','ADDON');			
			$mycms->removeSession('PROCESS_FLOW_ID_FRONT');			
			$mycms->redirect("registration.tariff.php?".$mycms->encoded('addMore'));			 
			exit();
			break;
			
		case'updateRegistration':
			$delegateId												  = $_REQUEST['delegateId'];
			$invoiceId												  = $_REQUEST['invoiceId'];
			$userDetailsArray['id']			                          = addslashes(trim(strtoupper($_REQUEST['delegateId'])));
			$userDetailsArray['registration_classification_id']		  = $_REQUEST['registration_classification_id'][0];
			$invoiceId												  = addslashes(trim($_REQUEST['invoiceId']));
			updatingUserDetails($userDetailsArray);
			
			$accompanyClasfId = 3;
			
			//CONFERENCE UPDATE
			updateInvoiceDetails($invoiceId,$userDetailsArray['id'],'CONFERENCE');
			
			$details	 = getUserDetails($userDetailsArray['id']);
			$accDetails  = getUserTypeAndRoomType($userDetailsArray['registration_classification_id']);
			$delegateId	 = $userDetailsArray['id'];
			$cutoffId    = $details['registration_tariff_cutoff_id'];
			
			
			//WORKSHOP UPDATE
			$workshopDetailArray['workshop_id']      				 = $_REQUEST['workshop_id'];
			if($workshopDetailArray['workshop_id']!="")	
			{
				$workshopreqDetls										 = array();
				$userWorkshopDetails 									 = getWorkshopDetailsOfDelegate($delegateId);
				foreach($userWorkshopDetails as $key => $rowdetails)
				{
					$workshopreqDetls['workshopId'][$key]				 = $rowdetails['id'];
					$workshopreqDetls['invoiceId'][$key]				 = $rowdetails['refference_invoice_id'];
				}
				$cancelProcess											 =  cancelAllWorkshoofDelegate($delegateId,$workshopreqDetls);
				
				$workshopinsert											 = insertWorkshopDetails($delegateId,$workshopDetailArray);
			}
			//ACCOMPANY UPDATE
			
			if($_REQUEST['accompany_selected_add'] !="")
			{
				$sqlUpdate4 = "UPDATE ".$cfg['DB.USER.REGISTRATION']."  SET `status` = 'D' WHERE `refference_delegate_id` = '".$delegateId."' AND `accompany_relationship` != 'ADD_ON'";						   
				$mycms->sql_update($sqlUpdate4,false);
				
				
				foreach($_REQUEST['accompany_selected_add'] as $key => $val)
				{
					if(addslashes(trim(strtoupper($_REQUEST['accompany_name_add'][$val]))) !="")
					{
						$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
						$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($_REQUEST['accompany_name_add'][$val])));
						$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($_REQUEST['accompany_age_add'][$val])));
						$accompanyDetailsArray[$val]['user_food_preference']                 = $_REQUEST['accompany_food_choice'][$val];
						$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($_REQUEST['accompany_food_details_add'][$val])));
						$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper($_REQUEST['accompany_relationship_add'][$val])));
						
						$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
						$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
						$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($accompanyClasfId)));
						$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
						$accompanyDetailsArray[$val]['registration_request']       		 	 = $details['registration_request'];
						$accompanyDetailsArray[$val]['operational_area']   	    		 	 = $details['registration_request'];
						$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
						$accompanyDetailsArray[$val]['registration_mode']					 = $details['registration_mode'];
						$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
						$accompanyDetailsArray[$val]['reg_type']              				 = addslashes(trim(strtoupper($details['reg_type'])));
					}	
				}
				
					
				if($accompanyDetailsArray)
				{
					$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray);
				}	
				 $sqlUpdateInv2	 = "UPDATE ".$cfg['DB.INVOICE']."  
									   SET `status` = 'D' 
									 WHERE `delegate_id` = '".$delegateId."' 
									   AND `service_type`='ACCOMPANY_CONFERENCE_REGISTRATION'";
			
				$mycms->sql_update($sqlUpdateInv2,false);
				foreach($accompanyReqId as $key => $reqId)
				{
					insertingInvoiceDetails($reqId,'ACCOMPANY');
				}
			}	
			//UPDATE ACCOMMODATION
			if($_REQUEST['accommodation_hotel_id']!="" && $_REQUEST['accommodation_roomType_id']!=""
				&& $_REQUEST['check_in_date']!="" && $_REQUEST['check_out_date']!="")
			{
				$sqlUpdate1 = "UPDATE ".$cfg['DB.REQUEST.ACCOMMODATION']."  SET `status` = 'D' WHERE `user_id` = '".$delegateId."'";						   
				$mycms->sql_update($sqlUpdate1,false);
				
				$sqlUpdateInv4	 = "UPDATE ".$cfg['DB.INVOICE']."  
										   SET `status` = 'D' 
										 WHERE `delegate_id` = '".$delegateId."' 
										   AND `service_type`='DELEGATE_ACCOMMODATION_REQUEST'";
				
				$mycms->sql_update($sqlUpdateInv4,false);
				
				$check_in_date_id                = $_REQUEST['check_in_date'];
				$check_out_date_id               = $_REQUEST['check_out_date'];
				$accommodation_hotel_id          = $_REQUEST['accommodation_hotel_id'];
				$accommodation_hotel_type_id     = $_REQUEST['accommodation_roomType_id'];
					
				$sqlAccommodationDate['QUERY']            = "SELECT * FROM ".$cfg['DB.ACCOMMODATION.DATE']." 
														   WHERE `id` = '".$check_in_date_id."'";
																		
				$resultAccommodationDate         = $mycms->sql_select($sqlAccommodationDate); 
				$rowAccommodationDate            = $resultAccommodationDate[0];
				
				$check_in_date                   = $rowAccommodationDate['check_in_date'];
				
				// GET ACCOMMODATION OUT DATE
				$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM ".$cfg['DB.ACCOMMODATION.CHECKOUT.DATE']."
														   WHERE `id` = '".$check_out_date_id."'";
																		
				$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
				$rowAccommodationOutDate           = $resultAccommodationOutDate[0];
				
				$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];
				
					
				$sqlFetchHotel['QUERY']    = "SELECT id 
									   FROM ".$cfg['DB.PACKAGE.ACCOMMODATION']."  
									  WHERE  `hotel_id` = '".$accommodation_hotel_id."'
										  AND `roomType_id` = '".$accommodation_hotel_type_id."'
										  AND `status` = 'A'"; 
										  
				$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
				$resultfetch 	  = $resultFetchHotel[0];
				$packageId 	      = $resultfetch['id'];
				
				
				$totalRoom = 0;
				$totalGuestCounter                 = 0;
				foreach($_REQUEST['room_guest_counter'] as $key=>$resDetails )
				{
					$totalRoom++;
					if($resDetails!=""){
								
						$totalGuestCounter        += $resDetails;
					}
				}
				 $accTariffId = getAccommodationTariffId($packageId,$check_in_date_id,$check_out_date_id,$cutoffId);
				$accomodationDetails['user_id']											 = $delegateId;
				$accomodationDetails['accompany_name']									 = $_REQUEST['accmName'];
				$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
				$accomodationDetails['guest_counter']									 = $_REQUEST['room_guest_counter'][0];
				$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
				$accomodationDetails['package_id']										 = $packageId;
				$accomodationDetails['tariff_ref_id']								     = $accTariffId;
				$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
				$accomodationDetails['checkin_date']									 = $check_in_date;
				$accomodationDetails['checkout_date']									 = $check_out_date;
				$accomodationDetails['booking_quantity']								 = $_REQUEST['booking_quantity'];
				$accomodationDetails['refference_invoice_id']							 = 0;
				$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
				$accomodationDetails['booking_mode']									 = $details['registration_mode'];
				$accomodationDetails['payment_status']									 = 'UNPAID';
				
				$accompReqId	 = insertingAccomodationDetails($accomodationDetails);
				
				
				insertingInvoiceDetails($accompReqId,'ACCOMMODATION');	
				
			}
			$mycms->redirect("registration.".strtolower($details['registration_mode']).".checkout.php");
			 
			exit();
			break;
			
		case'updateWorkshop':
			$currentCutoffId = getTariffCutoffId();
			$workshopDetailArray['id']      						= $_REQUEST['reqId'];	
			$details 	   	 = getWorkshopDetails($workshopDetailArray['id']);		
			$workshopDetailArray['workshop_id']      				= $_REQUEST['workshop_id'][0];
			
			$workshopDetailArray['registration_classification_id']  = getUserClassificationId($details['delegate_id']);
			$workshopDetailArray['workshop_tarrif_id']       		= getWorkshopTariffId($workshopDetailArray['workshop_id'],$currentCutoffId,$workshopDetailArray['registration_classification_id']);
			$invoiceId												= $_REQUEST['invoiceId'];
			
			updatingWorkShopDetails($workshopDetailArray);
			updateInvoiceDetails($invoiceId,$workshopDetailArray['id'],'WORKSHOP');
			$details = getUserDetails($details['delegate_id']);			  
			$mycms->redirect("registration.".strtolower($details['registration_mode']).".checkout.php");			 
			exit();
			break;
			
		case'setPaymentTerms':			
			$paymentId = insertingPaymentDetails();
			$mycms->setSession('TEMP_SLIP_ID',$mycms->getSession('SLIP_ID'));
			$mycms->removeSession('REGISTRATION_MODE');
			$mycms->removeSession('REGISTRATION_TOKEN');
			$mycms->removeSession('CURRENCY');
			$mycms->removeSession('COMMUNICATION_EMAIL');			
			$mycms->redirect("registration.success.php");			 
			exit();
			break;
		
		case'paymentSet':		  
			if($_REQUEST['mode']=='ONLINE')
			{
				if($_SERVER['HTTP_HOST'] == 'underconstruction.in' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SESSION['PAYMENT']=='SSK')
				{
					$mycms->redirect("demo.payment.php");
					
				}
				else
				{
					$mycms->redirect("atom_payment_do.php?delegate_id=".$_REQUEST['delegate_id']."&slip_id=".$_REQUEST['slip_id']);
				}
			}
			if($_REQUEST['mode']=='OFFLINE')
			{
				$mycms->redirect("registration.offline.checkout.php?".$mycms->encoded('offline.checkout'));
			}
			exit();
			break;
		 
		case'cancel_invoice':			
			cancelInvoiceProcess();			
			$mycms->redirect($_REQUEST['rtrnurl']);
			exit();
			break;
			
		case'cancel_invoice_from_profile':			
			cancelInvoiceProcess();	
			$returnto = $_REQUEST['from'];
			if($returnto=='')
			{
				$returnto = "unpaid_list.php";
			}
			$mycms->redirect($returnto);
			exit();
			break;
			
		case'onlinePayment':			
			$mycms->redirect("atom_payment_do.php?delegate_id=".$_REQUEST['delegate_id']."&slip_id=".$_REQUEST['slip_id']);			
			exit();
			break;
			
		case'add_accompany':		
			$accompanyDetails = $_REQUEST;
			//echo "<pre>"; print_r($accompanyDetails); echo "</pre>";die();
			$delegateId                     = $mycms->getSession('LOGGED.USER.ID');
			if($accompanyDetails)
			{
				$registrationMode					 = ($accompanyDetails['registrationMode']=="OFFLINE")?"OFFLINE":"ONLINE";
				foreach($accompanyDetails['accompany_selected_add'] as $key => $val)
				{
					$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
					$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
					$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
					$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
					$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
					$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));
					
					$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
					$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
					$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
					$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $accompanyDetails['cutoff_id'];
					$accompanyDetailsArray[$val]['registration_request']       		 	 = 'GENERAL';
					$accompanyDetailsArray[$val]['operational_area']   	    		 	 = 'GENERAL';
					$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
					$accompanyDetailsArray[$val]['registration_mode']					 = ($accompanyDetails['registrationMode']=="OFFLINE")?"OFFLINE":"ONLINE";
					$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
					$accompanyDetailsArray[$val]['reg_type']              				 = addslashes(trim(strtoupper('FRONT')));
				
				}
				//echo "<pre>";
				//print_r($_REQUEST);
				//print_r($accompanyDetailsArray);
				//echo "</pre>";
				insertingSlipDetails($delegateId,$accompanyDetails['registrationMode']);
				$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray);
				
				foreach($accompanyReqId as $key =>$reqId)
				{
					insertingInvoiceDetails($reqId,'ACCOMPANY');
				}
				
			}		  
			$mycms->redirect("registration.".strtolower($registrationMode).".checkout.php?type=".$mycms->encoded("profile"));			 
			exit();
			break;
}
?>