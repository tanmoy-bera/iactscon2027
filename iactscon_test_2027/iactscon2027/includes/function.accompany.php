<?php

function getTotalAccompanyCount($delegatesId)
{
	global $cfg, $mycms;
	
	$totalAccompanyCount  = 0;
	
	$sqlFetchAccompany['QUERY']    = "SELECT COUNT(*) AS totalAccompanyCount
									   FROM "._DB_USER_REGISTRATION_." accompany
									  WHERE accompany.status = 'A'
										AND accompany.refference_delegate_id = '".$delegatesId."'
										AND accompany.user_type = 'ACCOMPANY'";
	
	$resultAccompany      = $mycms->sql_select($sqlFetchAccompany);
	$rowAccompany         = $resultAccompany[0];
	
	$totalAccompanyCount  = $rowAccompany['totalAccompanyCount'];
	
	return $totalAccompanyCount;
}

function insertingAccompanyDetails($accompanyDetailsArray, $date='', $accomany_cutoffID='')
{
	global $cfg, $mycms;
	if($date=='')
	{
		$date= date('Y-m-d'); 
	}
	$acmpnyReqId = array();
	 
	foreach($accompanyDetailsArray as $key => $accompanyDetails)
	{		
	
		$sqlInsertAccompany  = array();
		$sqlInsertAccompany['QUERY']       = "INSERT INTO "._DB_USER_REGISTRATION_." 
												 SET `refference_delegate_id`		           = ?,
													 `user_type`					           = ?,
													 `user_full_name` 				           = ?,
													 `user_age`						           = ?,
													 `user_food_preference`			           = ?,
													 `user_food_preference_in_details` 		   = ?,
													 `isRegistration` 				           = ?,
													 `isConference` 				           = ?,
													 `registration_classification_id`          = ?,
													 `registration_tariff_cutoff_id`           = ?,
													 `accompany_tariff_cutoff_id`          	   = ?,
													 `registration_request`  		           = ?,
													 `operational_area`     		           = ?,
													 `registration_payment_status` 	           = ?,
													 `registration_mode`     		           = ?,
													 `account_status`		 		           = ?,
													 `reg_type`						           = ?,
													 `accompany_relationship`		           = ?,
													 `conf_reg_date`		 		           = ?,
													 `status` 						           = ?,
													 `created_ip` 					           = ?,
													 `created_sessionId` 			           = ?,
													 `created_dateTime`				           = ?";
											
	
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'refference_delegate_id',         'DATA' =>$accompanyDetails['refference_delegate_id'],         'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'user_type',                      'DATA' =>'ACCOMPANY',                                         'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'user_full_name',                 'DATA' =>$accompanyDetails['user_full_name'],                 'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'user_age',                       'DATA' =>$accompanyDetails['user_age'],                       'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'user_food_preference',           'DATA' =>$accompanyDetails['user_food_preference'],           'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'user_food_preference_in_details','DATA' =>$accompanyDetails['user_food_details'],              'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'isRegistration',                 'DATA' =>$accompanyDetails['isRegistration'],                 'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'isConference',                   'DATA' =>$accompanyDetails['isConference'],                   'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'registration_classification_id', 'DATA' =>$accompanyDetails['registration_classification_id'], 'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'registration_tariff_cutoff_id',  'DATA' =>$accompanyDetails['registration_tariff_cutoff_id'],  'TYP' => 's');

	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'accompany_tariff_cutoff_id',  'DATA' =>$accomany_cutoffID,  'TYP' => 's');

	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'registration_request',           'DATA' =>$accompanyDetails['registration_request'],           'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'operational_area',               'DATA' =>$accompanyDetails['operational_area'],               'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'registration_payment_status',    'DATA' =>$accompanyDetails['registration_payment_status'],    'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'registration_mode',              'DATA' =>$accompanyDetails['registration_mode'],              'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'account_status',                 'DATA' =>$accompanyDetails['account_status'],                 'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'reg_type',                       'DATA' =>$accompanyDetails['reg_type'],                       'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'accompany_relationship',         'DATA' =>$accompanyDetails['accompany_relationship'],         'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'conf_reg_date',                  'DATA' =>$date,                                                'TYP' => 's');	
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'status',                         'DATA' =>'A',                                                  'TYP' => 's');
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'created_ip',                     'DATA' =>$_SERVER['REMOTE_ADDR'],                              'TYP' => 's');	
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'created_sessionId',              'DATA' =>session_id(),                                         'TYP' => 's');	
	$sqlInsertAccompany['PARAM'][]   = array('FILD' => 'created_dateTime',               'DATA' =>date('Y-m-d H:i:s'),                                  'TYP' => 's');	
	
	$lastInsertedAccompanyId 	 = $mycms->sql_insert($sqlInsertAccompany, false);
	
	
	$sql = array();
	$sql['QUERY']	  = "SELECT *
						  FROM "._DB_USER_REGISTRATION_."
						 WHERE `id` = ?";
	
	$sql['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$accompanyDetails['refference_delegate_id'], 'TYP' => 's');
									 
	$result        	   			 = $mycms->sql_select($sql);
	$rowUserID       			 = $result[0];
	$user_registration_id		 = $rowUserID['user_registration_id']; 
	
	$accompany_registration_id  	  = $user_registration_id."-".number_pad($lastInsertedAccompanyId, 4);
	
	$sqlUpdateAccompany  =  array();	
	$sqlUpdateAccompany['QUERY']       = "UPDATE "._DB_USER_REGISTRATION_." 
											SET `user_registration_id` = ?
										  WHERE `id` = ?";
	
	$sqlUpdateAccompany['PARAM'][]   = array('FILD' => 'user_registration_id', 'DATA' =>$accompany_registration_id, 'TYP' => 's');
	$sqlUpdateAccompany['PARAM'][]   = array('FILD' => 'id',                   'DATA' =>$lastInsertedAccompanyId,   'TYP' => 's');
											  
	$mycms->sql_update($sqlUpdateAccompany, false);
	
	$sqlUpdateUser  = array();
	$sqlUpdateUser['QUERY']   = "UPDATE "._DB_USER_REGISTRATION_." 
									SET `isAccompany` = ?
								  WHERE `id` = ?";
								  
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'isAccompany', 'DATA' =>'Y',                                         'TYP' => 's');	
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',          'DATA' =>$accompanyDetails['refference_delegate_id'], 'TYP' => 's');							  
											  
	$mycms->sql_update($sqlUpdateUser, false);
	
	$acmpnyReqId[] 			  = $lastInsertedAccompanyId;
	
	
	}
	return $acmpnyReqId;
}


?>