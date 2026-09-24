<?php
	include_once('includes/init.php');
	
	$loggedUserID  = $mycms->getLoggedUserId();
	
	switch($action){		
		
		/************************************************************************************/
		/*                           RETRIVE LOGGED FACULTY PASSWORD                        */
		/************************************************************************************/
		case'loggedUserPassword':
			
			$dataString           = ""; 
			
			header('Content-type: application/json');
			
			$sqlFetchFaculty['QUERY']      = "SELECT * FROM "._DB_FACULTY_ACCOUNT_." 
											 WHERE `id` = '".$mycms->getLoggedUserId()."'";
			
			$resultFaculty        = $mycms->sql_select($sqlFetchFaculty);
			$rowFaculty           = $resultFaculty[0];
			
			$currentPassword      = $mycms->decoded($rowFaculty['faculty_login_password']);
			
			$dataString           = '{';
			$dataString          .= '"GET_LOGGED_PASSWORD": "'.$currentPassword.'"';	
			$dataString          .= '}';
			
			echo $dataString;
			exit();
			break;
		
		/************************************************************************************/
		/*                           MODIFY LOGGED FACULTY PASSWORD                         */
		/************************************************************************************/
		case md5("changepass"):
		
			$old_password 			= $mycms->encoded($_REQUEST['old_password']);
			$new_password 			= $mycms->encoded($_REQUEST['new_password']);
			
			$sqlFetchFaculty['QUERY'] 		= "SELECT * FROM "._DB_FACULTY_ACCOUNT_." 
											   WHERE `faculty_login_password` = '".$old_password."' 
												 AND `id` = '".$mycms->getLoggedUserId()."'";
			
			$resultFaculty      	= $mycms->sql_select($sqlFetchFaculty);
			
			if($resultFaculty)
			{
				$sqlUpdatePassword['QUERY']  = "UPDATE "._DB_FACULTY_ACCOUNT_." 
										  SET `faculty_login_password` = '".$new_password."', 
											  `modified_by` = '".$mycms->getLoggedUserId()."',
											  `modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
											  `modified_sessionId` = '".session_id()."',
											  `modified_dateTime` = '".date('Y-m-d H:i:s')."'
										WHERE `id` = '".$mycms->getLoggedUserId()."' ";
													
				$mycms->sql_update($sqlUpdatePassword);
				
				$mycms->redirect('changePassword.php?m=Password Changed Successfully');
			}
			else
			{
				$mycms->redirect('changePassword.php?m=Please enter correct existing password');
			}
			break; 
			exit();
	}
?>