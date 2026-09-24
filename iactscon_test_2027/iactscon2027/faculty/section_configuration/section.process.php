<?php
	include_once('includes/init.php');
	include_once('../../includes/function.section.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
		/******************************************************************/
		/*                    SEARCH SECTION OPERATION                    */
		/******************************************************************/
		case'search':
			
			pageRedirection(5, "");
			exit();
			break;
		
		/******************************************************************/
		/*                    SECTION NAME AVAILABILITY                   */
		/******************************************************************/
		case'isPresent':
			
			sectionNamePresenceChecker($cfg, $mycms);
			exit();
			break;
			
		/******************************************************************/
		/*                    INSERT SECTION OPERATION                    */
		/******************************************************************/
		case'insert':
			
			insertSectionProcess($cfg, $mycms);
			exit();		
			break;
			
		/******************************************************************/
		/*                    UPDATE SECTION OPERATION                    */
		/******************************************************************/
		case'update':
			
			updateSectionProcess($cfg, $mycms);
			exit();	
			break;
	}
?>