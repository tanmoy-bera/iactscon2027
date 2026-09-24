<?php
	include_once('includes/init.php');
	include_once('../../includes/function.pages.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	switch($action)
	{		
		case'insert':	
			insertPageProces($cfg, $mycms);
			break;			
		case 'update':
			updatePageProces($cfg, $mycms);
			break;
		case'del':
			deletePageProcess($cfg, $mycms);
			break;		
		case 'getModule':
			getModuleDropDown($cfg, $mycms);
			break;
		case 'getSeq':
			pageSequence($cfg, $mycms);
			break;
		case 'isNamePresent':
			pageNamePresenceChecker($cfg, $mycms);
			break;
		case 'isFilePresent':
			pageFileNamePresenceChecker($cfg, $mycms);
			break;
	}
?>