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
	$cfg['__USRWORKDOMAIN__'] = "faculty";
	
	if(!isset($cfg['THEME'])||trim($cfg['THEME'])=='')
	{ 
		$cfg['THEME'] = 'default';
	}
	
	$cfg['THEME_IMAGE_PATH'] = $cfg['DIR_CM_IMAGES'].$cfg['THEME'].'/';
	$cfg['THEME_CSS_PATH']   = $cfg['DIR_CM_CSS'].$cfg['THEME'].'/';
	
	$mycms = new CMSextended();
	$mycms->del_cache();
	
	$mycms->setDefaultMailSender('STANDARD');
	
	accessValidation();
	
	$action  = (isset($_REQUEST['act']))?$_REQUEST['act']:"";
	$show    = (isset($_REQUEST['show']))?$_REQUEST['show']:"";
	$msg     = (isset($_REQUEST['m']))?$_REQUEST['m']:"";
	
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
		default:
			break;
	}
	$mycms->setDisplayMessage($msg);

?>