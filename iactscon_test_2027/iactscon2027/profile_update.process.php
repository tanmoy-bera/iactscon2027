<?php
	include_once('includes/frontend.init.php');
	
	if($mycms->getSession('LOGGED.USER.ID')=="" || $mycms->isSession('LOGGED.USER.ID')==false)
	{
		login_session_control();
	}

	$delegateId           = $mycms->getSession('LOGGED.USER.ID');
	$action               = $_REQUEST['action'];
	
	
	
	switch($action)
	{
		case'cancel_service':		
			cancel_service($mycms,$cfg);
			exit();
			break;
			
		case'accompany_cancel_service':
			accompany_cancel_service($mycms,$cfg);
			exit();
			break;	
				
		case'workshop_change':
			workshop_change($mycms,$cfg);
			exit();
			break;
		
		case'update_profile':
			update_profile($mycms,$cfg);
			exit();
			break;
			
		
		case'update_image':
			update_image($mycms,$cfg);
			exit();
			break;
	
		case 'edit_accompany_profile':	
			edit_accompany_profile($mycms,$cfg);				
			exit();
			break;
			
		case 'message_admin':	
			message_admin($mycms,$cfg);				
			exit();
			break;
	
	}
	
	
	function cancel_service($mycms,$cfg)
	{
		$delegateId           		= $mycms->getSession('LOGGED.USER.ID');
		
		$invoiceId					= $_REQUEST['invoiceId'];
		$delegateId					= $_REQUEST['delegateId'];
		$user_comment				= addslashes(trim(strtoupper($_REQUEST['user_cancellation_reason'])));
		
		$sqlInsertQuery  = array();
		$sqlInsertQuery['QUERY']  	= "INSERT INTO "._DB_INVOICE_CANCEL_REQUEST_FROM_PROFILE_." 
											   SET `delegate_id` = ?, 
												   `invoice_id` = ?, 
												   `request_date` = ?, 
												   `request_message` = ?, 
												   `status` =?, 
												   `created_ip` = ?,
												   `created_sessionId` = ?,
												   `created_dateTime` = ?";
												   
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'delegate_id',      'DATA' =>$delegateId,   	         'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'invoice_id',       'DATA' =>$invoiceId,   	         'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'request_date',     'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'request_message',  'DATA' =>$user_comment,   	     'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'status',           'DATA' =>'A',   	                 'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'created_ip',       'DATA' =>$_SERVER['REMOTE_ADDR'], 'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' =>session_id(),   	     'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' =>date('Y-m-d H:i:s'),    'TYP' => 's');
		
		$mycms->sql_insert($sqlInsertQuery);	
		
		online_service_cancellation_message($invoiceId,$user_comment,'SEND');
		
		$mycms->redirect('profile.php?canceldone=Y');
	}
	
	function accompany_cancel_service($mycms,$cfg)
	{
		$delegateId           		= $mycms->getSession('LOGGED.USER.ID');
		
		$invoiceId					= $_REQUEST['invoiceId'];
		$delegateId					= $_REQUEST['delegateId'];
		$user_comment				= addslashes(trim(strtoupper($_REQUEST['user_cancellation_reason'])));
		
		$sqlInsertQuery  = array();
		$sqlInsertQuery['QUERY']  	= "INSERT INTO "._DB_INVOICE_CANCEL_REQUEST_FROM_PROFILE_." 
											   SET `delegate_id` = ?, 
												   `invoice_id` = ?, 
												   `request_date` = ?, 
												   `request_message` = ?, 
												   `status` =?, 
												   `created_ip` = ?,
												   `created_sessionId` = ?,
												   `created_dateTime` = ?";
												   
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'delegate_id',      'DATA' =>$delegateId,   	         'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'invoice_id',       'DATA' =>$invoiceId,   	         'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'request_date',     'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'request_message',  'DATA' =>$user_comment,   	     'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'status',           'DATA' =>'A',   	                 'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'created_ip',       'DATA' =>$_SERVER['REMOTE_ADDR'], 'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' =>session_id(),   	     'TYP' => 's');
		$sqlInsertQuery['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' =>date('Y-m-d H:i:s'),    'TYP' => 's');
		
		$mycms->sql_insert($sqlInsertQuery);			
		online_service_cancellation_message($invoiceId,$user_comment,'SEND');	
			
		$mycms->redirect('profile.php?accomCanceldone=Y');
	}
		
	function workshop_change($mycms,$cfg)
	{
		$delegateId           		= $mycms->getSession('LOGGED.USER.ID');		
		$invoiceId					= $_REQUEST['invoiceId'];
		$user_comment				= addslashes(trim(strtoupper($_REQUEST['change_content'])));
		
		workshop_change_request_mail($invoiceId,$user_comment,'SEND');
		
		$mycms->redirect('profile.php?whChangedone=Y');
	}
	
	function update_profile($mycms,$cfg)
	{
		$delegateId           			= $mycms->getSession('LOGGED.USER.ID');
		
		// VARRIABLE DECLARATION
		$user_initial                   = addslashes(trim(strtoupper($_REQUEST['user_initial'])));
		$user_first_name                = addslashes(trim(strtoupper($_REQUEST['user_first_name'])));
		$user_middle_name               = addslashes(trim(strtoupper($_REQUEST['user_middle_name'])));
		$user_last_name                 = addslashes(trim(strtoupper($_REQUEST['user_last_name'])));
		
		$user_address                   = addslashes(trim(strtoupper($_REQUEST['user_address'])));
		$user_country    		        = addslashes(trim(($_REQUEST['user_country']=="")?0:$_REQUEST['user_country']));
		$user_state      		        = addslashes(trim(($_REQUEST['user_state']=="")?0:$_REQUEST['user_state']));
		$user_city                      = addslashes(trim(strtoupper($_REQUEST['user_city'])));
		$user_pincode		            = addslashes(trim(strtoupper($_REQUEST['user_postal_code'])));
		$user_food_preference     		= addslashes(trim(strtoupper($_REQUEST['user_food_preference'])));
		
		
		// UPDATING USER DETAILS PHASE TWO
		$sqlUpdateUserPhaseTwo           = array();
		$sqlUpdateUserPhaseTwo['QUERY']  = "UPDATE "._DB_USER_REGISTRATION_." 
											  SET `user_address` = ?,
												  `user_country_id` = ?,
												  `user_state_id` = ?,
												  `user_city`     = ?,
												  `user_pincode` = ?,
												  `user_food_preference` = ?,
												  `user_dob` = ?,
												  `modified_ip` = ?,
												  `modified_sessionId` = ?,
												  `modified_browser` = ?,
												  `modified_dateTime` = ?
											WHERE `id` = ?";
											
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_address',         'DATA' =>$user_address,                'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_country_id',      'DATA' =>$user_country,                'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_state_id',        'DATA' =>$user_state,                  'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_city',            'DATA' =>$user_city,                   'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_pincode',         'DATA' =>$user_pincode,                'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_food_preference', 'DATA' =>$user_food_preference,        'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'user_dob',             'DATA' =>$user_dob,                    'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_ip',          'DATA' =>$_SERVER['REMOTE_ADDR'],      'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_sessionId',   'DATA' =>session_id(),                 'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_browser',     'DATA' =>$_SERVER['HTTP_USER_AGENT'],  'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'modified_dateTime',    'DATA' =>date('Y-m-d H:i:s'),          'TYP' => 's');
		$sqlUpdateUserPhaseTwo['PARAM'][]   = array('FILD' => 'id',                   'DATA' =>$delegateId,                  'TYP' => 's');
									
		$mycms->sql_update($sqlUpdateUserPhaseTwo, false);			
		
		$userImage                      = $_FILES['user_file_upload']['name'];
		$userImageTempFile              = $_FILES['user_file_upload']['tmp_name'];
		$userImageFileName              = $mycms->getRandom(6, 'snum')."_".time().$userImage;
		
		if($userImageTempFile!="")
		{
			$userImagePath              = $cfg['USER.ID.CARD'].$userImageFileName;
			
			chmod($userImagePath, 0777);
			copy($userImageTempFile, $userImagePath);
			chmod($userImagePath, 0777);
			
			$sqlUpdateFile['QUERY']      		= "UPDATE "._DB_USER_REGISTRATION_." 
											  SET `user_document` = '".$userImageFileName."',
												  `user_document_org_name` = '".$userImage."'
											  WHERE `id` = '".$delegateId."' ";
											   
			$sqlUpdateFile['PARAM'][]   = array('FILD' => 'user_document',             'DATA' =>$userImageFileName,           'TYP' => 's');
			$sqlUpdateFile['PARAM'][]   = array('FILD' => 'user_document_org_name',    'DATA' =>$userImage,                   'TYP' => 's');
			$sqlUpdateFile['PARAM'][]   = array('FILD' => 'id',                        'DATA' =>$delegateId,                  'TYP' => 's');
												  
			$mycms->sql_update($sqlUpdateFile, false);			
		}
		
		$mycms->redirect('profile.php?menuId=update_user_details&pUpdate=Y');
	}
	
	function update_image($mycms,$cfg)
	{
		$delegateId           			= $mycms->getSession('LOGGED.USER.ID');
		
		$current_page                   = $_REQUEST['current_page'];
		$userImage                      = $_FILES['user_profile_image']['name'];
		$userImageTempFile              = $_FILES['user_profile_image']['tmp_name'];
		$userImageFileName              = $delegateId."_".time().strstr($userImage,'.');
		
		if($userImageTempFile!="")
		{
			$userImagePath              = $cfg['USER.PROFILE.IMAGE'].$userImageFileName;
			
			chmod($userImagePath, 0777);
			copy($userImageTempFile, $userImagePath);
			chmod($userImagePath, 0777);
			
			$sqlUserImage = array();
			$sqlUserImage['QUERY']   = "UPDATE "._DB_USER_REGISTRATION_." 
										  SET `user_image` = ? 
										WHERE `id` = ?";
											
			$sqlUserImage['PARAM'][]   = array('FILD' => 'user_image',    'DATA' =>$userImageFileName,           'TYP' => 's');
			$sqlUserImage['PARAM'][]   = array('FILD' => 'id',            'DATA' =>$delegateId,                  'TYP' => 's');
			
			$mycms->sql_update($sqlUserImage, false);
			
			$mycms->redirect($current_page);
		}
	}
	
	function edit_accompany_profile($mycms,$cfg)
	{
		$delegateId           					= $mycms->getSession('LOGGED.USER.ID');
		
		$accompany_id                   		= trim($_REQUEST['accompany_id']);
		$accompany_edit_name            		= addslashes(trim(strtoupper($_REQUEST['accompany_person_edit_name'])));			
		$accompany_edit_age             		= addslashes(trim(strtoupper($_REQUEST['accompany_person_edit_age'])));
		$accompany_edit_food_pref       		= addslashes(trim(strtoupper($_REQUEST['accompany_person_edit_food_preference'])));				
		
		$sqlUpdateAccompanyDetails  			= array();
		$sqlUpdateAccompanyDetails['QUERY']   	= "UPDATE "._DB_USER_REGISTRATION_." 
											  		  SET `user_full_name` = ?,
											   			  `user_food_preference` = ?,
											  			  `modified_browser` = ?,
											  			  `modified_dateTime` = ?
												    WHERE `id` = ?";
											
		$sqlUpdateAccompanyDetails['PARAM'][]   = array('FILD' => 'user_full_name',             'DATA' =>$accompany_edit_name,           'TYP' => 's');
		$sqlUpdateAccompanyDetails['PARAM'][]   = array('FILD' => 'user_food_preference',       'DATA' =>$accompany_edit_food_pref,      'TYP' => 's');
		$sqlUpdateAccompanyDetails['PARAM'][]   = array('FILD' => 'modified_browser',           'DATA' =>$_SERVER['HTTP_USER_AGENT'],    'TYP' => 's');
		$sqlUpdateAccompanyDetails['PARAM'][]   = array('FILD' => 'modified_dateTime',          'DATA' =>date('Y-m-d H:i:s'),            'TYP' => 's');
		$sqlUpdateAccompanyDetails['PARAM'][]   = array('FILD' => 'id',                         'DATA' =>$accompany_id,                  'TYP' => 's');
									
		$mycms->sql_update($sqlUpdateAccompanyDetails, false);			
		$mycms->redirect('profile.php');
	}
	
	function message_admin($mycms,$cfg)
	{
		
		
		$delegateId     = $mycms->getSession('LOGGED.USER.ID');
		
		$call_subject	= 'REGISTRATION';
		$call_contents	= addslashes(trim($_REQUEST['user_text']));
		$call_datetime	= date("Y-m-d H:i:s");
		$callDate		= date("Y-m-d");
		$call_time		= date("H:i:s");
		
		$sqlupdatetravelpickup				=	array();
		$sqlupdatetravelpickup['QUERY']  	=   "   INSERT INTO "._DB_USER_CALLDETAILS_	." 
															SET `delegate_id` = ?,
																`logged_user_id` = ?,
																`call_subject` = ?,
																`call_datetime` = ?,
																`call_date` = ?,
																`call_time` = ?,
																`call_contents` = ?";
														
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'delegate_id',  	  	'DATA' =>$delegateId,		'TYP' => 's');		
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'logged_user_id',   	'DATA' =>'-1', 				'TYP' => 's');	
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_subject',   	'DATA' =>$call_subject, 	'TYP' => 's');	
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_datetime',   	'DATA' =>$call_datetime, 	'TYP' => 's');	
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'callDate',  	  		'DATA' =>$callDate, 		'TYP' => 's');
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_time',  	  	'DATA' =>$call_time, 	  	'TYP' => 's');
		$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_contents',   	'DATA' =>$call_contents,	'TYP' => 's');	
		$mycms->sql_insert($sqlupdatetravelpickup);
		
	}
	
	///////////////////////////////////////
	
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		       = "_pgn_";
		$pageKeyVal                    		       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 		       = "";
		$searchArray                   		       = array();
		
		$searchArray[$pageKey]         		       = $pageKeyVal;
		$searchArray['src_exhibitor_email_id']     = trim($_REQUEST['src_exhibitor_email_id']);
		$searchArray['src_exhibitor_code']         = trim($_REQUEST['src_exhibitor_code']);
		$searchArray['src_exhibitor_name']         = trim($_REQUEST['src_exhibitor_name']);
		$searchArray['src_exhibitor_mobile_no']    = trim($_REQUEST['src_exhibitor_mobile_no']);
		
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