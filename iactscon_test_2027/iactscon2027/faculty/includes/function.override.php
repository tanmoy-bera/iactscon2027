<?php 
	/********************************************************************/
	/*                      DISPLAY RELATED METHOD                      */
	/********************************************************************/
	function adminDisplay()
	{	
		global $cfg,$mycms;	
		
		webmaster_adminDisplay();
	}

	function notElligibleDisplay()
	{
		global $cfg,$mycms;	
		
		webmaster_notElligibleDisplay();		
	}
	
	function getAbstractReviewMarks($reviewOptionId)
	{
		global $cfg, $mycms;
		
		$reviewOptionMarks       = 0;
		
		$sqlReviewOption['QUERY']         = "SELECT * FROM "._DB_ABSTRACT_REVIEW_CRITERIA_OPTIONS_." 
		                                    WHERE `status` = 'A' 
											  AND `id` = '".$reviewOptionId."'";
											  
		$resultReviewOption      = $mycms->sql_select($sqlReviewOption);
		$rowReviewOption         = $resultReviewOption[0];
		
		$reviewOptionMarks       = ($rowReviewOption['review_option_marks']=="")?0:$rowReviewOption['review_option_marks'];
		
		return $reviewOptionMarks;
	}
?>