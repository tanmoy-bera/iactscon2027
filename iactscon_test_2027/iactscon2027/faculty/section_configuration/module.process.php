<?php
	include_once('includes/init.php');
	include_once('../../includes/function.module.php');
	$loggedUserID = $mycms->getLoggedUserId();
	switch($action){
		/*============== INSERTING DATA IN MODULE TABLE =================*/	
		case'insert':	
			insertModuleProcess($cfg, $mycms);
			break;	
		/*============== UPDATE DATA IN MODULE TABLE =================*/	
		case 'update':
			updateModuleProcess($cfg, $mycms);
			break;
		/*================== MODULE NAME AVAILABILITY ===================*/	
		case 'isPresent':
			moduleNamePresenceChecker($cfg, $mycms);
			break;
		
		/*================== MODULE SEQUENCE ===================*/	
		case 'getSeq':
			moduleSequence($cfg, $mycms);
			break;
	}	
?>