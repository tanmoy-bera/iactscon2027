<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action){
		
	
	}
	
	/*************************************************************************/
	/*                              UTILITY METHOD                           */
	/*************************************************************************/
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		            = "_pgn1_";
		$pageKeyVal                    		            = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 		            = "";
		$searchArray                   		            = array();
		
		$searchArray[$pageKey]                          = $pageKeyVal;
		$searchArray['src_delegates_full_name']         = trim($_REQUEST['src_delegates_full_name']);
		$searchArray['src_delegates_access_key']        = trim($_REQUEST['src_delegates_access_key']);
		$searchArray['src_delegates_mobile_no']         = trim($_REQUEST['src_delegates_mobile_no']);
		$searchArray['user_delegates_email_id']         = trim($_REQUEST['user_delegates_email_id']);
		$searchArray['src_accompany_full_name']         = trim($_REQUEST['src_accompany_full_name']);
		$searchArray['src_accompany_registration_id']   = trim($_REQUEST['src_accompany_registration_id']);
		$searchArray['src_from_date']                   = trim($_REQUEST['src_from_date']);
		$searchArray['src_to_date']                     = trim($_REQUEST['src_to_date']);
		
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