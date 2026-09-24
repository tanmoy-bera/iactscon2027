<?php
@session_start();
if(true){
	@ob_start();
	$obst = true;
}
header("Cache-Control: no-cache, must-revalidate");
@date_default_timezone_set('Asia/Kolkata');

include_once('frontend.links.php');

$cfg['__USRWORKDOMAIN__'] = "front";
$mycms = new CMS();
$mycms->del_cache();
//$processStartTime=$mycms->microtime_float();

//$mycms->setDefaultMailSender('SENDGRID');	
$mycms->setDefaultMailSender('SENDGRID');	

//$sqlBigJoin = "SET OPTION SQL_BIG_SELECTS = 1";
//mysql_query($sqlBigJoin);


if($_REQUEST['DEKHABO']=='OK' || $_SESSION['DEKHABO']=='OK')
{
	$_SESSION['DEKHABO']='OK';
}
if($_REQUEST['DEKHARE']=='ISAR' || $_SESSION['DEKHARE']=='ISAR')
{
	$_SESSION['DEKHARE']='ISAR';
}
if($_REQUEST['DEKHARE']=='PANN' || $_SESSION['DEKHARE']=='PANN')
{
	$_SESSION['DEKHARE']='PANN';
}
if($_REQUEST['SHOW']=='YES' || $_SESSION['SHOW']=='YES')
{
	$_SESSION['SHOW']='YES';
}
else if($_REQUEST['SHOW']=='NO' || $_SESSION['SHOW']=='NO')
{
	unset($_SESSION['SHOW']);
}
if($_REQUEST['SHOW']=='GST' || $_SESSION['SHOW']=='GST')
{
	$_SESSION['SHOW']='GST';
}
if($_REQUEST['ACCOMMODATION']=='BOOK' || $_SESSION['ACCOMMODATION']=='BOOK')
{
	$_SESSION['ACCOMMODATION']='BOOK';
}
//unset($_SESSION['DEKHARE']);
//unset($_SESSION['DEKHABO']);
//$mycms->removeAllSession();

?>