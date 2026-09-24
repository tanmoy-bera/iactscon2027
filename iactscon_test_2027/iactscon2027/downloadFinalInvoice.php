<?php
include_once("includes/frontend.init.php");
include_once('includes/pdfcrowd.php');
include_once('includes/function.invoice.php');
include_once('includes/function.delegate.php');
?>
<html>
	<head>
	<script src="<?=$cfg['BASE_URL']?>js/jquery.js"></script>
	</head>
	<body style="margin:0px; padding:0px;">
	<?php
	$delegateId = $mycms->decoded($_REQUEST['delegateId']);
	$slipId = $mycms->decoded($_REQUEST['slipId']);
	$resPaymentDetails      = paymentDetails($slipId);
	
	if($original=='true')
	{
		if($resPaymentDetails['payment_status']=="PAID")
		{ 
			
			echo getPrintSlipDetailsContent($delegateId, $slipId,true,true,false);
		}
		else
		{ 
			echo getPrintSlipDetailsContent($delegateId, $slipId,true,true,true);
		}
	}
	else
	{ 
		echo getPrintSlipDetailsContent($delegateId, $slipId, true, true);
	}
	?>	
	</body>
</html>
