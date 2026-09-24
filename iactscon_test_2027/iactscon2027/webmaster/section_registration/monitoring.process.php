<?php

	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	switch($action){
		
		/*********************************************************************/
		/*                     SEARCH GENERAL REGISTRATION                   */
		/*********************************************************************/
		case'search_date':
		
			pageRedirection("registration_monitoring.php",5,"&show=registration"); 
			exit();
			break;
			
		case'search_month':
		
			pageRedirection("registration_monitoring.php",5,"&show=view_monthwise_report"); 
			exit();
			break;
		
		case'search_location':
		
			pageRedirection("registration_monitoring.php",5,"&show=registration_location"); 
			exit();
			break;
			
		case'search_state':
		
			pageRedirection("registration_monitoring.php",5,"&show=registration_state"); 
			exit();
			break;
			
		case'search_registration':
		
			pageRedirection("registration_monitoring.php",5,"&show=view_registration"); 
			exit();
			break;
				
		
	}
	
	/******************************************************************************/
	/*                                 UTILITY METHOD                             */
	/******************************************************************************/
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		       = "_pgn_";
		$pageKeyVal                    		       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 		       = "";
		$searchArray                   		       = array();
		
		$searchArray[$pageKey]         		       = $pageKeyVal;
		$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
		$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key'],'#'));
		$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
		$searchArray['src_user_first_name']        = addslashes(trim($_REQUEST['src_user_first_name']));
		$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
		$searchArray['src_invoice_no']     		   = addslashes(trim($_REQUEST['src_invoice_no']));
		$searchArray['src_slip_no']       		   = addslashes(trim($_REQUEST['src_slip_no'],'##'));
		$searchArray['src_registration_mode']      = addslashes(trim($_REQUEST['src_registration_mode']));
		$searchArray['src_user_last_name']         = addslashes(trim($_REQUEST['src_user_last_name']));
		$searchArray['src_atom_transaction_ids']   = addslashes(trim($_REQUEST['src_atom_transaction_ids']));
		$searchArray['src_transaction_ids']        = addslashes(trim($_REQUEST['src_transaction_ids']));
		$searchArray['src_conf_reg_category']      = addslashes(trim($_REQUEST['src_conf_reg_category']));
		$searchArray['src_reg_category']           = addslashes(trim($_REQUEST['src_reg_category']));
		$searchArray['src_registration_id']        = addslashes(trim($_REQUEST['src_registration_id']));
		$searchArray['src_reg_date']               = addslashes(trim($_REQUEST['src_reg_date']));
		$searchArray['src_reg_month']               = addslashes(trim($_REQUEST['src_reg_month']));
		$searchArray['src_country_id']             = addslashes(trim($_REQUEST['src_country_id']));
		$searchArray['src_state_id']               = addslashes(trim($_REQUEST['src_state_id']));
		$searchArray['c']              			   = addslashes(trim($_REQUEST['c']));
		
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