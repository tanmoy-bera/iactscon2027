<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.dinner.php");
	
ini_set('max_execution_time', 600);
if($mycms->getSession('LOGGED.USER.ID')=="")
{
	$mycms->redirect("login.php");
}

$user_id      = $mycms->getSession('LOGGED.USER.ID');
$key  	  	  = array_keys($_REQUEST);
$slipId	 	  = $mycms->decoded($key[0]);

$original = $_REQUEST['original'];
if($original=='true')
{
	$resPaymentDetails      = paymentDetails($slipId);
	if($resPaymentDetails['payment_status']=="UNPAID")
	{ 
		echo getPrintSlipDetailsContent($user_id, $slipId,true,true,true);
	}
	else
	{ 
		echo getPrintSlipDetailsContent($user_id, $slipId,true,true,false);
	}
}
else
{ 
	echo getPrintSlipDetailsContent($user_id, $slipId,true,true,true);
}
			
?>	
