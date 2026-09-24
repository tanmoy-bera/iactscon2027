<?php
	include_once('includes/frontend.init.php');
	include_once("includes/function.frontend.registration.php");
	include_once("includes/function.edit.php");
	include_once('includes/function.delegate.php');
	include_once('includes/function.invoice.php');
	include_once('includes/function.accompany.php');
	include_once('includes/function.workshop.php');
	include_once('includes/function.registration.php');
	include_once('includes/function.messaging.php');
	include_once('includes/function.accommodation.php');
	include_once("includes/function.dinner.php");
	global $cfg;
	$act = $_REQUEST['act'];

	// uploadFile() callback
	if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
		// Handle the file upload
		$tempFile = $_FILES['file']['tmp_name'];
		// echo $tempFile;die;
		$fileName = $_POST['dynamic_name_prefix']."_".$_FILES['file']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST.TEMP'];
		if($_POST['prevFile']!=""){
			unlink($cfg['FILES.ABSTRACT.REQUEST.TEMP'].$_POST['prevFile']);
		}
	
		if(move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			// File uploaded successfully
			echo "File uploaded successfully.";
		} else {
			// Error uploading file
			echo "Error: Unable to move file to destination directory.";
		}
	}
	
	//deleteTempFile() callback-> 
	if(isset($_POST['delete']) && $_POST['tempFile']){
		if($_POST['tempFile']!=""){
			unlink($cfg['FILES.ABSTRACT.REQUEST.TEMP'].$_POST['tempFile']);
		}
	}
		
	switch($act)
	{			
		case "triggerOTPSMS" :
			$sqlFetchval['QUERY'] = "SELECT * 
									  FROM "._DB_USER_REGISTRATION_."
									 WHERE `id` = ?";
							   
			$sqlFetchval['PARAM'][]   = array('FILD' => 'user_email_id',        	   'DATA' =>trim($_REQUEST['id']),  'TYP' => 's');
			$resultUserval              = $mycms->sql_select($sqlFetchval);							 
			$rowUserval                 = $resultUserval[0];	
			
			$sms = "Dear ".$rowUserval['user_full_name'].".  ".$rowUserval['user_unique_sequence']." is your unique sequence to submit your abstract at ".$cfg['EMAIL_CONF_NAME'].".";
			$status = $mycms->send_sms($rowUserval['user_mobile_no'], $sms);
			send_uniqueSequence($_REQUEST['id'], 'SEND');			
			exit();
			break;
		
		case "enter":
			$sqlFetchval = array();
			$sqlFetchval['QUERY'] = "SELECT * 
									  FROM "._DB_USER_REGISTRATION_."
									 WHERE `user_email_id` = ? 
									   AND `status` = ?";
							   
			$sqlFetchval['PARAM'][]   = array('FILD' => 'user_email_id',        	   'DATA' =>trim($_REQUEST['email']),  'TYP' => 's');
			$sqlFetchval['PARAM'][]   = array('FILD' => 'status',                      'DATA' =>'A',                       'TYP' => 's');
			
			
			
			$resultUserval              = $mycms->sql_select($sqlFetchval);	 				 
			$rowUserval                 = $resultUserval[0];
			//echo '<pre>'; print_r($rowUserval);
			//die();					
			if($resultUserval)
			{
				$sqlAb  			  = array();
				$sqlAb['QUERY']     = " SELECT COUNT(*) AS COUNTDATA 
										FROM "._DB_ABSTRACT_REQUEST_." 
									   WHERE `status` = ?
										 AND `applicant_id` = ?";
										 //AND `abstract_child_type` IN ('Oral','Poster')
										
				$sqlAb['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
				$sqlAb['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' =>$rowUserval['id'] , 'TYP' => 's');

				// echo '<pre>'; print_r($sqlAb);
				$resultAbstractType = $mycms->sql_select($sqlAb);

				// echo '<pre>';print_r($resultAbstractType[0]['COUNTDATA']);

				//echo count($resultAbstractType);
				//die();
				
				$mycms->setSession('LOGIN.TYPE','ABSTRACT');
				$mycms->setSession('LOGGED.USER.ID',$rowUserval['id']);
				$mycms->setSession('IS_LOGIN',"YES");

				if($resultAbstractType[0]['COUNTDATA']>0)
				{
					// $mycms->redirect("profile.php");
					$processId = isset($_REQUEST['processId']) ? intval($_REQUEST['processId']) : 0;
                    $mycms->redirect("abstract_request.process.php?act=abstractSubmission&applicantId=" . intval($rowUserval['id']) . "&processId=" . $processId);
				}
				else
				{
					$processId = isset($_REQUEST['processId']) ? intval($_REQUEST['processId']) : 0;
                    $mycms->redirect("abstract_request.process.php?act=abstractSubmission&applicantId=" . intval($rowUserval['id']) . "&processId=" . $processId);
				}
				
				
				//$loginDetails 	 = login_session_control(false);
				//print_r($loginDetails);
				//$mycms->redirect('profile.php?menuId=user_add_abstract');
				
				//$mycms->redirect('profile.php');
			}
			else
			{
				$mycms->redirect('index.php?m=1');
				//$mycms->redirect('abstract.user.entrypoint.php');
			}
			exit();
			break;		
		case "enterBack":
			$sqlFetchval = array();
			$sqlFetchval['QUERY'] = "SELECT * 
									  FROM "._DB_USER_REGISTRATION_."
									 WHERE `user_email_id` = ? 
									   AND `status` = ?";
							   
			$sqlFetchval['PARAM'][]   = array('FILD' => 'user_email_id',        	   'DATA' =>trim($_REQUEST['email']),  'TYP' => 's');
			$sqlFetchval['PARAM'][]   = array('FILD' => 'status',                      'DATA' =>'A',                       'TYP' => 's');
			
			
			
			$resultUserval              = $mycms->sql_select($sqlFetchval);	 				 
			$rowUserval                 = $resultUserval[0];
			//echo '<pre>'; print_r($rowUserval);
			//die();					
			if($resultUserval)
			{
				$sqlAb  			  = array();
				$sqlAb['QUERY']     = " SELECT COUNT(*) AS COUNTDATA 
										FROM "._DB_ABSTRACT_REQUEST_." 
									   WHERE `status` = ?
										 AND `applicant_id` = ?";
										 //AND `abstract_child_type` IN ('Oral','Poster')
										
				$sqlAb['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
				$sqlAb['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' =>$rowUserval['id'] , 'TYP' => 's');

				// echo '<pre>'; print_r($sqlAb);
				$resultAbstractType = $mycms->sql_select($sqlAb);

				// echo '<pre>';print_r($resultAbstractType[0]['COUNTDATA']);

				//echo count($resultAbstractType);
				//die();
				
				$mycms->setSession('LOGIN.TYPE','ABSTRACT');
				$mycms->setSession('LOGGED.USER.ID',$rowUserval['id']);
				$mycms->setSession('IS_LOGIN',"YES");

				if($resultAbstractType[0]['COUNTDATA']>0)
				{
					// $mycms->redirect("profile.php");
					$processId = isset($_REQUEST['processId']) ? intval($_REQUEST['processId']) : 0;
                    $mycms->redirect("abstract_request.process.php?act=abstractSubmissionBack&applicantId=" . intval($rowUserval['id']) . "&processId=" . $processId);
				}
				else
				{
					$processId = isset($_REQUEST['processId']) ? intval($_REQUEST['processId']) : 0;
                    $mycms->redirect("abstract_request.process.php?act=abstractSubmissionBack&applicantId=" . intval($rowUserval['id']) . "&processId=" . $processId);
				}
				
				
				//$loginDetails 	 = login_session_control(false);
				//print_r($loginDetails);
				//$mycms->redirect('profile.php?menuId=user_add_abstract');
				
				//$mycms->redirect('profile.php');
			}
			else
			{
				$mycms->redirect('index.php?m=1');
				//$mycms->redirect('abstract.user.entrypoint.php');
			}
			exit();
			break;	
		case'step1':
           	
			$sessionId	    = session_id();
			if(isset($_REQUEST['fields1'][0]) || isset($_REQUEST['fields2'][0]) || isset($_REQUEST['fields3'][0])  || isset($_REQUEST['fields4'][0])  || isset($_REQUEST['fields5'][0])  || isset($_REQUEST['fields6'][0])  || isset($_REQUEST['fields7'][0])  || isset($_REQUEST['fields8'][0]) || isset($_REQUEST['fields9'][0])|| isset($_REQUEST['fields10'][0]) || isset($_REQUEST['fields11'][0])){

			$sqlFetchUser                = array(); 
			$sqlFetchUser['QUERY']       = "SELECT * 
											 FROM "._DB_USER_REGISTRATION_."
											WHERE `user_email_id` = ? 
											  AND `status` = ?";	
										   // AND `registration_request` = 'GENERAL'	
											  
			$sqlFetchUser['PARAM'][]    = array('FILD' => 'user_email_id', 'DATA' =>$_REQUEST['user_email_id'], 'TYP' => 's');	
			$sqlFetchUser['PARAM'][]    = array('FILD' => 'status',        'DATA' =>'A',    'TYP' => 's');
									
			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$row 						= $resultFetchUser [0];
		
	          	////////// doc upload////////////////

			    if (isset($_FILES['upload_abstract_file'])) {
					// Handle the file upload
					$tempFile = $_FILES['upload_abstract_file']['tmp_name'];
					// echo $tempFile;die;
					$fileName = "DOC_" .date('ymdHis')."_". $_FILES['upload_abstract_file']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					

					if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
						$_REQUEST['upload_abstract_file'] 	= $fileName;
					} else {
						$_REQUEST['upload_abstract_file'] 	= $fileName;
					}
				}
				if (isset($_FILES['upload_consent_abstract_file'])) {
					// Handle the file upload
					$tempFile = $_FILES['upload_consent_abstract_file']['tmp_name'];
					// echo $tempFile;die;
					$fileName = "DOC_" .date('ymdHis')."_". $_FILES['upload_consent_abstract_file']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					

					if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
						$_REQUEST['upload_consent_abstract_file'] 	= $fileName;
					} else {
						$_REQUEST['upload_consent_abstract_file'] 	= $fileName;
					}
				}
				if (isset($_FILES['upload_nomination_file'])) {
					// Handle the file upload
					$tempFile = $_FILES['upload_nomination_file']['tmp_name'];
					// echo $tempFile;die;
					$fileName = "DOC_" .date('ymdHis')."_". $_FILES['upload_nomination_file']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					

					if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
						$_REQUEST['upload_nomination_file'] 	= $fileName;
					} else {
						$_REQUEST['upload_nomination_file'] 	= $fileName;
					}
				}
				if (isset($_FILES['category_required_file'])) {
					// Handle the file upload
					$tempFile = $_FILES['category_required_file']['tmp_name'];
					// echo $tempFile;die;
					$fileName = "DOC_" .date('ymdHis')."_". $_FILES['category_required_file']['name'];
					$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
					

					if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
						$_REQUEST['category_required_file'] 	= $fileName;
					} else {
						$_REQUEST['category_required_file'] 	= $fileName;
					}
				}
				////////// doc upload////////////////
		
			$userIp		    = $_SERVER['REMOTE_ADDR'];
			$userBrowser    = $_SERVER['HTTP_USER_AGENT'];
			$requestValues  = serialize($_REQUEST);
			
			
			$USER_DETAILS_FRONT['NAME'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'].". ".$_REQUEST['user_first_name']." ".$_REQUEST['user_middle_name']." ".$_REQUEST['user_last_name'])));
			$USER_DETAILS_FRONT['TITLE'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'])));
			$USER_DETAILS_FRONT['EMAIL']	  = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
			$USER_DETAILS_FRONT['PH_NO'] 	  = addslashes(trim($_REQUEST['user_usd_code']." - ".$_REQUEST['user_mobile']));
			$USER_DETAILS_FRONT['CUTOFF']     = addslashes(trim($mycms->getSession('CUTOFF_ID_FRONT'))); 
			$USER_DETAILS_FRONT['CATAGORY']   = addslashes(trim($mycms->getSession('CLSF_ID_FRONT'))); 
			// $title=strtoupper($_REQUEST['user_initial_title']);
			
			$mycms->setSession('USER_DETAILS_FRONT', $USER_DETAILS_FRONT);
			// $mycms->setSession('USER_TITLE', $title);
						
			if($mycms->getSession('PROCESS_FLOW_ID_FRONT') == "")
			{
				$sqlProcessInsertStep            = array();
				$sqlProcessInsertStep['QUERY']   = "INSERT INTO "._DB_PROCESS_STEP_."
													   SET `step1` 		= ?,
														   `created_ip` = ?,
														   `reg_area` = ?,
														   `created_sessionId` = ?,
														   `created_browser` = ?,
														   `created_dateTime` = ?";	
															   
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'step1',             'DATA' =>addslashes($requestValues),   'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' =>$userIp,                      'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'reg_area',          'DATA' =>'FRONT',                      'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' =>$sessionId,                   'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',   'DATA' =>$userBrowser,                 'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' =>date('Y-m-d H:i:s'),          'TYP' => 's');
							
				$id = $mycms->sql_insert($sqlProcessInsertStep, false);
				$mycms->setSession('PROCESS_FLOW_ID_FRONT',$id);
				if($userImageTempFile!="")
				{
					$sqlProcessUpdateStep['QUERY']           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
														   SET  `step10` = '".$userImageFileName."'
														 WHERE `id` = '".$id."'";															 
					$mycms->sql_update($sqlProcessUpdateStep, false);
				}
			}
			else
			{	
				$sqlProcessUpdateStep = array();
				$sqlProcessUpdateStep['QUERY']      = " UPDATE  "._DB_PROCESS_STEP_."
														   SET `step1` 		= ?,
															   `created_dateTime` = ?
														 WHERE `id` = ?";
													 
				$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'step1',            'DATA' =>$requestValues,                               'TYP' => 's');
				$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'created_dateTime', 'DATA' =>date('Y-m-d H:i:s'),                          'TYP' => 's');
				$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$mycms->getSession('PROCESS_FLOW_ID_FRONT'),  'TYP' => 's');
				
				$mycms->sql_update($sqlProcessUpdateStep, false);
			}
			$mycms->removeSession('WORKSHOP_ID');
			
			if(!$mycms->isSession('REGISTRATION_TOKEN'))
			{
				$mycms->setSession('REGISTRATION_TOKEN', "#".$mycms->getSession('PROCESS_FLOW_ID_FRONT'));
			}
			if($resultFetchUser)
			{
				$processId = 0; // default
				$sessionProcessId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');

				if (!empty($sessionProcessId)) {
					$processId = intval($sessionProcessId);
				}
				$mycms->setSession('LOGGED.USER.ID',$row['delegateId']);
				$mycms->setSession('IS_LOGIN',"YES");
                 $mycms->redirect(
					"abstract.user.entrypoint.process.php?act=enter&email=" 
					.$_REQUEST['user_email_id'] 
					. "&processId=" . $processId
				);				
				exit();
			}
			$mycms->redirect("abstract.user.entrypoint.process.php?act=step6");
			exit();
		}else{
			header("Location: " . _BASE_URL_ );
	        
		}
		break;
		
		case'step6':
			$mycms->setSession('CURRENT_REG_USER','FINISH');			
			
			// DETAILS INSERTING PROCESS	
			$sqlProcessFlow          = array();	
			$sqlProcessFlow['QUERY'] = "SELECT * FROM "._DB_PROCESS_STEP_." 
										 WHERE `id` = ?";
										
			$sqlProcessFlow['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$mycms->getSession('PROCESS_FLOW_ID_FRONT'), 'TYP' => 's');
			
			$resProcessFlow			= $mycms->sql_select($sqlProcessFlow);
			if($resProcessFlow)
			{
				$rowProcessFlow 	= $resProcessFlow[0];

				//echo '<pre>'; print_r($rowProcessFlow);
				
				$userDetails		= unserialize($rowProcessFlow['step1']);

				// echo '<pre>'; print_r($userDetails);
				// die();
				
				if($userDetails)
				{
					$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($userDetails['user_email_id'])));
					$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($userDetails['comunication_email'])));
					$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
					$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
					$userDetailsArray['membership_number']                    = addslashes(trim($userDetails['membership_number']));
					$userDetailsArray['user_initial_title']   				 		  = addslashes(trim(strtoupper($userDetails['user_initial_title'])));
					$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['user_first_name'])));
					$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['user_middle_name'])));
					$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['user_last_name'])));
					$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title']." ".$userDetailsArray['user_first_name']." ".$userDetailsArray['user_middle_name']." ".$userDetailsArray['user_last_name'];
					$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
					$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['user_usd_code'])));
					$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['user_mobile'])));
					$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
					$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['user_address'])));
					$userDetailsArray['user_country']                         = addslashes(trim(strtoupper($userDetails['user_country'])));
					$userDetailsArray['user_state']                           = addslashes(trim(strtoupper($userDetails['user_state'])));
					$userDetailsArray['user_city']                            = addslashes(trim(strtoupper($userDetails['user_city'])));
					$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['user_postal_code'])));
					$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
					$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
					$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));
					if($userDetailsArray['user_dob_year'] != "" && $userDetailsArray['user_dob_month'] != "" && $userDetailsArray['user_dob_day'] != "")
					{
						$userDetailsArray['user_dob']                         = number_pad($userDetailsArray['user_dob_year'],4)."-".number_pad($userDetailsArray['user_dob_month'], 2)."-".number_pad($userDetailsArray['user_dob_day'], 2);
					}
					
					$userDetailsArray['user_gender']                          = ($userDetails['user_gender']=='')?'NA':$userDetails['user_gender'];
					$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['user_designation'])));
					$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['user_depertment'])));
					$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['user_institution'])));
					$userDetailsArray['user_food_preference']                 = $userDetails['user_food_preference'];
					$userDetailsArray['user_other_food_details']              = addslashes(trim(strtoupper($userDetails['user_food_details'])));
					$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['user_pasport_no'])));
					$userDetailsArray['passport_expiry_date']                 = number_pad($userDetails['user_pasport_exp_year'], 4)."-".number_pad($userDetails['user_pasport_exp_month'], 2)."-".number_pad($userDetails['user_pasport_exp_day'], 2);
					
					$userDetailsArray['user_document']						  = $fileDetails;
					$userDetailsArray['isRegistration']						  = 'N';
					$userDetailsArray['isConference']						  = 'N';
					$userDetailsArray['isWorkshop']							  = 'N';
					$userDetailsArray['isAccommodation']                      = 'N';
					$userDetailsArray['isTour']								  = 'N';
					$userDetailsArray['isCombo']							  = 'N';
					$userDetailsArray['IsAbstract']							  = 'N';
					
					$userDetailsArray['registration_classification_id']		  = '0';
					$userDetailsArray['registration_tariff_cutoff_id']        = '0';
					$userDetailsArray['registration_request']       		  = 'ABSTRACT';
					$userDetailsArray['operational_area']   	    		  = 'ABSTRACT';
					$userDetailsArray['registration_payment_status']		  = 'UNPAID';
					$userDetailsArray['registration_mode']					  = "OFFLINE";
					$userDetailsArray['account_status']						  = 'REGISTERED';
					$userDetailsArray['reg_type']              				  = addslashes(trim(strtoupper($userDetails['reg_area'])));

					// echo '<pre>'; print_r($userDetailsArray);die();
									
					$delegateId												  = insertingUserDetails($userDetailsArray,$date);
					
					$userRec 												  = getUserDetails($delegateId);
					// echo '<pre>'; print_r($userRec);die;
				}
			}
			
			//echo json_encode(array("STATUS"=>"SUCCESS","REDIRECT"=>_BASE_URL_."abstract.user.entrypoint.process.php?act=enter&email=".$userDetailsArray['user_email_id']));
			$processId = 0; // default
			$sessionProcessId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');

			if (!empty($sessionProcessId)) {
				$processId = intval($sessionProcessId);
			}

			$mycms->redirect(
				"abstract.user.entrypoint.process.php?act=enter&email=" 
				. urlencode($userDetailsArray['user_email_id']) 
				. "&processId=" . $processId
			);
			exit();
			break;
		case'step1Back':
           	
			$sessionId	    = session_id();
			
			$sqlFetchUser                = array(); 
			$sqlFetchUser['QUERY']       = "SELECT * 
											 FROM "._DB_USER_REGISTRATION_."
											WHERE `user_email_id` = ? 
											  AND `status` = ?";	
										   // AND `registration_request` = 'GENERAL'	
											  
			$sqlFetchUser['PARAM'][]    = array('FILD' => 'user_email_id', 'DATA' =>$_REQUEST['user_email_id'], 'TYP' => 's');	
			$sqlFetchUser['PARAM'][]    = array('FILD' => 'status',        'DATA' =>'A',    'TYP' => 's');
									
			$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
			$row 						= $resultFetchUser [0];
		
	
			
			$userIp		    = $_SERVER['REMOTE_ADDR'];
			$userBrowser    = $_SERVER['HTTP_USER_AGENT'];
			$requestValues  = serialize($_REQUEST);
			
			
			$USER_DETAILS_FRONT['NAME'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'].". ".$_REQUEST['user_first_name']." ".$_REQUEST['user_middle_name']." ".$_REQUEST['user_last_name'])));
			$USER_DETAILS_FRONT['TITLE'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'])));
			$USER_DETAILS_FRONT['EMAIL']	  = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
			$USER_DETAILS_FRONT['PH_NO'] 	  = addslashes(trim($_REQUEST['user_usd_code']." - ".$_REQUEST['user_mobile']));
			$USER_DETAILS_FRONT['CUTOFF']     = addslashes(trim($mycms->getSession('CUTOFF_ID_FRONT'))); 
			$USER_DETAILS_FRONT['CATAGORY']   = addslashes(trim($mycms->getSession('CLSF_ID_FRONT'))); 
			// $title=strtoupper($_REQUEST['user_initial_title']);
			
			$mycms->setSession('USER_DETAILS_FRONT', $USER_DETAILS_FRONT);
			// $mycms->setSession('USER_TITLE', $title);
			
			if($mycms->getSession('PROCESS_FLOW_ID_FRONT') == "")
			{
				$sqlProcessInsertStep            = array();
				$sqlProcessInsertStep['QUERY']   = "INSERT INTO "._DB_PROCESS_STEP_."
													   SET `step1` 		= ?,
														   `created_ip` = ?,
														   `reg_area` = ?,
														   `created_sessionId` = ?,
														   `created_browser` = ?,
														   `created_dateTime` = ?";	
															   
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'step1',             'DATA' =>addslashes($requestValues),   'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' =>$userIp,                      'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'reg_area',          'DATA' =>'FRONT',                      'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' =>$sessionId,                   'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',   'DATA' =>$userBrowser,                 'TYP' => 's');
				$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' =>date('Y-m-d H:i:s'),          'TYP' => 's');
							
				$id = $mycms->sql_insert($sqlProcessInsertStep, false);
				$mycms->setSession('PROCESS_FLOW_ID_FRONT',$id);
				if($userImageTempFile!="")
				{
					$sqlProcessUpdateStep['QUERY']           = " UPDATE  ".$cfg['DB.PROCESS.STEP']."
														   SET  `step10` = '".$userImageFileName."'
														 WHERE `id` = '".$id."'";															 
					$mycms->sql_update($sqlProcessUpdateStep, false);
				}
			}
			else
			{	
				$sqlProcessUpdateStep = array();
				$sqlProcessUpdateStep['QUERY']      = " UPDATE  "._DB_PROCESS_STEP_."
														   SET `step1` 		= ?,
															   `created_dateTime` = ?
														 WHERE `id` = ?";
													 
				$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'step1',            'DATA' =>$requestValues,                               'TYP' => 's');
				$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'created_dateTime', 'DATA' =>date('Y-m-d H:i:s'),                          'TYP' => 's');
				$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$mycms->getSession('PROCESS_FLOW_ID_FRONT'),  'TYP' => 's');
				
				$mycms->sql_update($sqlProcessUpdateStep, false);
			}
			$mycms->removeSession('WORKSHOP_ID');
			
			if(!$mycms->isSession('REGISTRATION_TOKEN'))
			{
				$mycms->setSession('REGISTRATION_TOKEN', "#".$mycms->getSession('PROCESS_FLOW_ID_FRONT'));
			}
			if($resultFetchUser)
			{
				$processId = 0; // default
				$sessionProcessId = $mycms->getSession('PROCESS_FLOW_ID_FRONT');

				if (!empty($sessionProcessId)) {
					$processId = intval($sessionProcessId);
				}
				$mycms->setSession('LOGGED.USER.ID',$row['delegateId']);
				$mycms->setSession('IS_LOGIN',"YES");
                 $mycms->redirect(
					"abstract.user.entrypoint.process.php?act=enterBack&email=" 
					.$_REQUEST['user_email_id'] 
					. "&processId=" . $processId
				);				
				exit();
			}
			$mycms->redirect("abstract.user.entrypoint.process.php?act=step6");
			exit();
		break;
		
	}
?>