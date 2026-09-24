<?php
include_once("includes/frontend.init.php");
include_once('lib/pdfcrowd.php');
include_once('includes/function.invoice.php');
?>
<html>
	<head>
	</head>
	<body style="margin:0px; padding:0px;">
	<?php
	$delegateId = $mycms->decoded($_REQUEST['delegateId']);
    $slipId = $mycms->decoded($_REQUEST['slipId']);
	
	
	if($_REQUEST['SUMAN']== 'ME')
	{
		echo getPrintSlipDetailsContent($delegateId, $slipId, true, true);
	}
	else
	{
		try
		{
		$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);	
		$client->enableImages(true);
		
		$pdf = $client->convertHtml(getPrintSlipDetailsContent($delegateId, $slipId, true, true));
		
		
		header("Content-Type: application/pdf");
		header("Cache-Control: no-cache");
		header("Accept-Ranges: none");
		header("Content-Disposition: attachment; filename=\"RCOG19".number_pad($invoiceId, 6).".pdf\"");
		
		echo $pdf;
		}
		
		catch(Exception $e)
	    {
		echo getPrintSlipDetailsContent($delegateId, $slipId, true, true);
	    ?>
		<script>
		window.print();
		</script>
	    <?
	    }
	  
	   
	}
	    ?>
	</body>
</html>

