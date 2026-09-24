<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	$action		  = $_REQUEST['act'];
	switch($action){
		
		/*********************************************************************/
		/*                          SEARCH DELEGATE                          */
		/*********************************************************************/
		case'addCallDetails':
			
			$delegateId		= addslashes(trim($_REQUEST['delegateId']));
			$participantId 	= addslashes(trim($_REQUEST['participantId']));
			$callDate		= addslashes(trim($_REQUEST['callDate']));
			$callTimeHour	= addslashes(trim($_REQUEST['callTimeHour']));
			$callTimeMin	= addslashes(trim($_REQUEST['callTimeMin']));
			$call_subject	= addslashes(trim($_REQUEST['call_subject']));
			$call_contents	= addslashes(trim($_REQUEST['call_contents']));
			
			if(trim($callTimeHour) == '' || !is_numeric($callTimeHour) || $callTimeHour > 23 || $callTimeHour < 0)
			{
				$callTimeHour = 0;
			}	
			if(trim($callTimeMin) == '' || !is_numeric($callTimeMin) || $callTimeMin > 59 || $callTimeMin < 0)
			{
				$callTimeMin = 0;
			}	
			
			$call_time		= number_pad(intval(trim($callTimeHour)),2).':'.number_pad(intval(trim($callTimeMin)),2).':'.'00';
			
			$call_datetime 	= $callDate.' '.number_pad(intval(trim($callTimeHour)),2).':'.number_pad(intval(trim($callTimeMin)),2).':'.'00';
			
			$sqlupdatetravelpickup			=	array();
			$sqlupdatetravelpickup['QUERY']  =   "  INSERT INTO "._DB_USER_CALLDETAILS_	." 
															SET `delegate_id` = ?,
																`participant_id` = ?,
																`logged_user_id` = ?,
																`call_subject` = ?,
																`call_datetime` = ?,
																`call_date` = ?,
																`call_time` = ?,
																`call_contents` = ?";
															
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'delegate_id',  	  	'DATA' =>$delegateId,		'TYP' => 's');	
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'participant_id',   	'DATA' =>$participantId,	'TYP' => 's');	
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'logged_user_id',   	'DATA' =>$loggedUserID, 	'TYP' => 's');	
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_subject',   	'DATA' =>$call_subject, 	'TYP' => 's');	
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_datetime',   	'DATA' =>$call_datetime, 	'TYP' => 's');	
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'callDate',  	  		'DATA' =>$callDate, 		'TYP' => 's');
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_time',  	  	'DATA' =>$call_time, 	  	'TYP' => 's');
			$sqlupdatetravelpickup['PARAM'][]   = array('FILD' => 'call_contents',   	'DATA' =>$call_contents,	'TYP' => 's');										
			$mycms->sql_insert($sqlupdatetravelpickup);
			
			if($delegateId != "")
			{
				$addlPram = "delegateId=".$delegateId;
			}
			elseif($participantId != "")
			{
				$addlPram = "participantId=".$participantId;
			}
			
			$mycms->redirect(_BASE_URL_."webmaster/section_registration/call_datalist.php?".$addlPram);
				
			exit();
			break;
			
		case'getEmailValidation':
			emailIdValidationProcess();
			exit();
			break;
			
		case'getMobileNoValidation':
			//print_r($_REQUEST);
			editMobileNoValidationProcess();
			//die();
			break;
			
		case'mobile_update':
			$delegateId  =  $_REQUEST['delegateId'];
			mobileNoUpdateProcess();
			pageRedirection("edit_email_phone_no.php", 2, $_REQUEST['search_string']);
			exit();
			break;
			
		case'name_update':
			nameUpdateProcess();
			pageRedirection("edit_email_phone_no.php", 2, $_REQUEST['search_string']);
			exit();
			break;
				
		case'email_update':
		
			emailUpdateProcess();
			pageRedirection("edit_email_phone_no.php", 2, $_REQUEST['search_string']);
			exit();
			break;		
	}
	
	
	function mobileNoUpdateProcess()
	{
		global $cfg, $mycms;
		
		$loggedUserID 		 = $mycms->getLoggedUserId();
		
		$isd_code			 =$_REQUEST['new_isd_code'];
		$mobile_no			 = $_REQUEST['new_mobile_no'];
		$old_mobile_no		 = $_REQUEST['old_mobile_no'];
		$delegateId			 = $_REQUEST['delegate_id'];
		$sqlUpdateRecord['QUERY'] = "   UPDATE "._DB_USER_REGISTRATION_."
										   SET `user_mobile_no` = '".$mobile_no."',
											   `user_mobile_isd_code`='".$isd_code."',  
											   `modified_by` = '".$loggedUserID."',
											   `modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
											   `modified_sessionId` = '".session_id()."',
											   `modified_dateTime` = '".date('Y-m-d H:i:s')."'
										 WHERE `id` = '".$delegateId."'";
										   
		$mycms->sql_update($sqlUpdateRecord);
		
		
		$sqlInsertRecord['QUERY']	 = "INSERT INTO "._DB_OLD_CONTACT_HISTORY_."
										SET `delegate_id` = '".$delegateId."',
											`old_mobile_no` = '".$old_mobile_no."',
											`status` = 'A', 
											`created_by` = '".$loggedUserID."',
											`created_ip` = '".$_SERVER['REMOTE_ADDR']."',
											`created_sessionId` = '".session_id()."',
											`created_dateTime` = '".date('Y-m-d H:i:s')."'";
										   
		$lastInsertedId = $mycms->sql_insert($sqlInsertRecord);
		
		//spotPhNoUpdateMessage($delegateId,'SEND');
	}
	
	function nameUpdateProcess()
	{
		global $cfg, $mycms;
		
		$loggedUserID 		        = $mycms->getLoggedUserId();
		
		//print_r($_REQUEST);
		$delegateId					= $_REQUEST['user_id'];
		$user_title					= addslashes(trim(strtoupper($_REQUEST['user_title'])));
		$user_first_name			= addslashes(trim(strtoupper($_REQUEST['user_first_name'])));
		$user_middle_name		    = addslashes(trim(strtoupper($_REQUEST['user_middle_name'])));
		$user_last_name			    = addslashes(trim(strtoupper($_REQUEST['user_last_name'])));
		$user_full_name             = $user_title." ".$user_first_name." ".$user_middle_name." ".$user_last_name;
		
		
		$sqlUpdateRecord['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
									   SET  `user_title` = '".$user_title."', 
											`user_first_name` = '".$user_first_name."', 
											`user_middle_name` = '".$user_middle_name."', 
											`user_last_name` = '".$user_last_name."',
											`user_full_name` = '".$user_full_name."', 
											`modified_by` = '".$loggedUserID."',
											`modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
											`modified_sessionId` = '".session_id()."',
											`modified_dateTime` = '".date('Y-m-d H:i:s')."'
									 WHERE  `id` = '".$delegateId."'";
										   
		$mycms->sql_update($sqlUpdateRecord);
		
		//spotNameUpdateMessage($delegateId,'SEND');
		
	}
	
	function emailUpdateProcess()
	{
		global $cfg, $mycms;
		
		$loggedUserID 		 = $mycms->getLoggedUserId();
		
		$email				 = $_REQUEST['new_email_id'];
		$oldEmail			 = $_REQUEST['old_email_id'];
		$delegateId			 = $_REQUEST['user_id'];
		
		$sqlUpdateRecord['QUERY'] = "UPDATE "._DB_USER_REGISTRATION_."
									   SET `user_email_id` = '".$email."', 
										   `modified_by` = '".$loggedUserID."',
										   `modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
										   `modified_sessionId` = '".session_id()."',
										   `modified_dateTime` = '".date('Y-m-d H:i:s')."'
									 WHERE `id` = '".$delegateId."'";
										   
		$mycms->sql_update($sqlUpdateRecord);
		
		
		 $sqlInsertRecord['QUERY']	 = "INSERT INTO "._DB_OLD_CONTACT_HISTORY_."
										SET `delegate_id` = '".$delegateId."',
											`old_email_id` = '".$oldEmail."',
											`status` = 'A', 
											`created_by` = '".$loggedUserID."',
											`created_ip` = '".$_SERVER['REMOTE_ADDR']."',
											`created_sessionId` = '".session_id()."',
											`created_dateTime` = '".date('Y-m-d H:i:s')."'";
										   
		$lastInsertedId = $mycms->sql_insert($sqlInsertRecord);
		
		
		
		//spotEmailUpdateMessage($delegateId,'SEND');
	}
	
	function editMobileNoValidationProcess()
	{	
		global $cfg, $mycms;		
		$newMobile = trim($_REQUEST['mobile']);
		
		$sqlSelectRecord['QUERY'] = "SELECT * FROM "._DB_USER_REGISTRATION_."								  
								 WHERE `user_mobile_no` = '".$newMobile."'
								 AND `status` = 'A' ";
										   
		$resultRecord  =  $mycms->sql_select($sqlSelectRecord);
		
		if($resultRecord)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}		
	}
	
	function editemailIdValidationProcess()
	{
		global $cfg, $mycms;		
		$newEmailId = trim($_REQUEST['email']);
		
		$sqlSelectRecord['QUERY'] = "SELECT * FROM "._DB_USER_REGISTRATION_."								  
								 WHERE `user_email_id` = '".$newEmailId."'
								 AND `status` = 'A' ";
										   
		$resultRecord  =  $mycms->sql_select($sqlSelectRecord);
		
		if($resultRecord)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}		
	}
	
	/******************************************************************************/
	/*                                 UTILITY METHOD                             */
	/******************************************************************************/
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		@$searchString                 		       = "";
		$searchArray                   		       = array();
		
		$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
		$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key']));
		$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
		$searchArray['src_user_first_name']        = addslashes(trim($_REQUEST['src_user_first_name']));
		$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
		$searchArray['src_user_last_name']         = addslashes(trim($_REQUEST['src_user_last_name']));
		$searchArray['src_registration_id']        = addslashes(trim($_REQUEST['src_registration_id']));
		
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