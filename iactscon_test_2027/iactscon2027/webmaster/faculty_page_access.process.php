<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action){
		
		/****************************************************************************************/
		/*                          FACULTY ACCESS ROLE UPDATE PROCESS                          */
		/****************************************************************************************/
		case'update':
			
			$faculty_id                         = addslashes(trim($_REQUEST['faculty_id']));
			
			// DELETING EXISTING FACULTY ACCESS DETAILS
			$sqlDeleteAccessDetails['QUERY']             = "DELETE FROM "._DB_FACULTY_ACCESS_DETAILS_." 
														 WHERE `faculty_id` = '".$faculty_id."'
														   AND `web_domain_id` = '2'";
			
			$mycms->sql_query($sqlDeleteAccessDetails); 
			
			// FACULTY ACCESS DETAILS
			$webPageArray                       = $_REQUEST['web_page_id_edit'];
			
			foreach($webPageArray as $webPageKey=>$webPageVal)
			{
				$web_page_id_val                = addslashes(trim($_REQUEST['web_page_id_edit'][$webPageKey]));
				
				if($web_page_id_val!="")
				{
					$sqlInsertAccessRoleDetails['QUERY'] = "INSERT INTO "._DB_FACULTY_ACCESS_DETAILS_." 
														   SET `faculty_id` = '".$faculty_id."', 
															   `web_page_id` = '".$web_page_id_val."', 
															   `web_domain_id` = '2', 
															   `status` = 'A',  
															   `created_by` = '".$loggedUserID."', 
															   `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
															   `created_sessionId` = '".session_id()."', 
															   `created_dateTime` = '".date('Y-m-d H:i:s')."'";
															   
					$mycms->sql_insert($sqlInsertAccessRoleDetails); 
				}
			}
			
			$mycms->redirect("faculty_page_access.php?m=2");
			exit();
			break;
				
	}
?>