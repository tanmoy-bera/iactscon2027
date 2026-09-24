<?php
	include_once('includes/init.php');
	$loggedUserID 	= $mycms->getLoggedUserId();
	$loggedUserType = $mycms->getLoggedUserType();
	
	switch($action)
	{
		case'search_abstract':
			
			pageRedirection("admin.php", 5, "");
			exit();
			break;
		
		/***************************************************************************/
		/*                         ABSTRACT REVIEW PROCESS                         */
		/***************************************************************************/
		case'review':
			
			$abstract_id      = addslashes(trim($_POST['abstract_id']));
			
			abstractReviewProcess($abstract_id);
			
			pageRedirection('admin.php', 2, '&cat='.$_POST['cat_id']);
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
		$searchArray['goto']  						 	 = trim($_REQUEST['goto']);
		$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
		$searchArray['src_abstract_topic_id']  		 	 = trim($_REQUEST['src_abstract_topic_id']);
		$searchArray['src_paper_presentation_category']  = trim($_REQUEST['src_paper_presentation_category']);
		
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
