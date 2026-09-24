<?php
include_once("includes/frontend.init.php");
include_once("includes/function.invoice.php");
include_once('lib/pdfcrowd.php');
?>
<html>
	<head>
	</head>
	<body style="margin:0px; padding:0px;">
	<?php
	$userId        = addslashes(trim($_REQUEST['user_id']));
	 $invoiceId     = addslashes(trim($_REQUEST['invoice_id'])); 
	
	if($_REQUEST['SSK']=='OK')
	{
		
		echo mailInvoiceContent($userId, $invoiceId, true);
	}
	
	try
	{
		
		// $client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);	
		// $client->enableImages(true);
		// $pdf = $client->convertHtml(mailInvoiceContent($userId, $invoiceId, true));
	
	
		// header("Content-Type: application/pdf");
		// header("Cache-Control: no-cache");
		// header("Accept-Ranges: none");
		// header("Content-Disposition: attachment; filename=\"RCOG19".number_pad($invoiceId, 6).".pdf\"");
		
		// echo $pdf;
		echo mailInvoiceContent($userId, $invoiceId, true);
	
	}
	catch(Exception $e)
	{
		
		echo mailInvoiceContent($userId, $invoiceId, true);
	?>
		<script>
		window.print();
		</script>
	<?
	}
	?>
	</body>
</html>



