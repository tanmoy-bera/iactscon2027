<?php
include_once("includes/frontend.init.php");
include_once("includes/function.invoice.php");
?>
<html>
	<head>
		<title>:: Invoice ::</title>
		<script src="<?=$cfg['BASE_URL']?>js/adminPanel/all.js"></script>
		<script src="<?=$cfg['BASE_URL']?>js/table/stupidtable.js"></script>
		<script src="<?=$cfg['BASE_URL']?>js/graph/dx.chartjs.js"></script>
		<script src="<?=$cfg['BASE_URL']?>js/graph/globalize.min.js"></script>
		<script src="<?=$cfg['BASE_URL']?>js/graph/knockout-2.2.1.js"></script>
	</head>
	<body style="margin:0px; padding:0px;">
	<script type="text/javascript" language="javascript">
	$(document).ready(function(){	
	   window.print();
	   
	   setTimeout(function()
	   {
		window.open('', '_self', '');
		},1500);
	});
	</script>
	<?php
	
	$accessKeyArr 		= array_keys($_REQUEST);
	$accessKey			= $mycms->decoded($accessKeyArr[0]);
	$accessKeyArray     = explode("#####", $accessKey);
	
	$userId				= $accessKeyArray[0];
	$invoiceId          = $accessKeyArray[1];
	
	echo mailInvoiceContent($userId, $invoiceId);
	
	?>
	</body>
</html>