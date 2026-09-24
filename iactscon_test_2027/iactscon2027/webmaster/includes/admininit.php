<?php

	@session_start();

	if(true)
	{
		@ob_start();
		$obst = true;
	}
	header("Cache-Control: no-cache, must-revalidate");
	@date_default_timezone_set('Asia/Kolkata');

	include_once('links.php');	

	if(!isset($cfg['THEME'])||trim($cfg['THEME'])=='')
	{ 
		$cfg['THEME'] = 'default';
	}
	
	$cfg['THEME_IMAGE_PATH'] = _DIR_CM_IMAGES_.$cfg['THEME'].'/';
	$cfg['THEME_CSS_PATH']   = _DIR_CM_CSS_.$cfg['THEME'].'/';
	$cfg['__USRWORKDOMAIN__'] = "WEBMASTER";
	
	$mycms = new CMSextended();
	$mycms->del_cache();
	//$processStartTime = $mycms->microtime_float();
	
	//$mycms->setDefaultMailSender('STANDARD');
	$mycms->setDefaultMailSender('SENDGRID');	

	if($mycms->getPageName()=='full_program_schedule.process.php' && $_REQUEST['pass']==intval(date('Ymd'))*intval(date('d')))
	{
		//pass
	}
	elseif($mycms->getPageName()=='message.pushing.process.php' && $_REQUEST['pass']==intval(date('Ymd'))*intval(date('d')))
	{
		//pass
	}
	else
	{
		accessValidation();
	}
		
	$action  = (isset($_REQUEST['act']))?$_REQUEST['act']:"";
	$show    = (isset($_REQUEST['show']))?$_REQUEST['show']:"";
	$msg     = (isset($_REQUEST['m']))?$_REQUEST['m']:"";
	
	$searchArray = $mycms->getSearchArray();
	$$searchString = $mycms->getSearchString();
	
	switch(trim($msg))
	{
		case '0':
			$msg = "Operation Failed";
			break;
		case '1':
			$msg = "Data Inserted Successfully";
			break;
		case '2':
			$msg = "Data Updated Successfully";
			break;
		case '3':
			$msg = "Data Removed Successfully";
			break;
		case '4':
			$msg = "Status Updated Successfully";
			break;
		case '5':
			$msg = "Filter Result";
			break;
		case '6':
			$msg = "Payment Successfully";
			break;
		case '7':
			$msg = "Message Sent Successfully";
			break;
		default:
			break;
	}
	$mycms->setDisplayMessage($msg);
	
	$sqlCListing['QUERY'] = "SELECT * 
						  	   FROM "._DB_CONSTANTS_."";	
	$resultsCListing	 = $mycms->sql_select($sqlCListing);
	foreach($resultsCListing as $k=>$rowCListing)
	{
		$cfg[trim($rowCListing['key'])]  = trim($rowCListing['value']);
	}
?>