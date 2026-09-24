<?php
	include_once('includes/init.php');
	$loggedUserID 	= $mycms->getLoggedUserId();
	$loggedUserType = $mycms->getLoggedUserType();
	
	switch($action)
	{
		case'search_award':
			
			pageRedirection("reviewed_award.jg_jolly.php", 5, "");
			exit();
			break;
		
		/***************************************************************************/
		/*                           AWARD REVIEW PROCESS                          */
		/***************************************************************************/
		case'review':
			
			$award_id      = addslashes(trim($_POST['award_id']));
			
			awardReviewProcess($award_id);
			
			pageRedirection('reviewed_award.jg_jolly.php', 2, "");
			exit();
			break;	
	}
	
	/******************************************************************************/
	/*                                 UTILITY METHOD                             */
	/******************************************************************************/
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		       		 = "_pgn1_";
		$pageKeyVal                    		       		 = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 		       		 = "";
		$searchArray                   		       		 = array();
		
		$searchArray[$pageKey]         		       		 = $pageKeyVal;
		$searchArray['src_award_submission_code']  	     = trim($_REQUEST['src_award_submission_code']);
		
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
