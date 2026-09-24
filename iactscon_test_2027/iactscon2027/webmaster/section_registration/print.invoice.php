<?php
include_once('includes/init.php');
include_once('lib/pdfcrowd.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.registration.php');
include_once('../../includes/function.accommodation.php');

?>
<html>
	<head>
		<title>:: Invoice ::</title>
		<script src="<?=$cfg['BASE_URL']?>js/jquery.js"></script>
	</head>
	<body style="margin:0px; padding:0px;">
	
	<?php
	$userId        = addslashes(trim($_REQUEST['user_id']));
	$invoiceId     = addslashes(trim($_REQUEST['invoice_id']));
	if(isset($_REQUEST['show']) && $_REQUEST['show']=='intHand')
	{
		echo ruedaMailInvoiceContent($userId, $invoiceId);
	}
	else
	{
		echo mailInvoiceContent($userId, $invoiceId);
	}
	/*
	//echo $cfg['CROWD.PDF.USERNAME'];
	$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);	
	$client->enableImages(true);
	$pdf = $client->convertHtml(mailInvoiceContent($userId, $invoiceId, true));
	
	
	header("Content-Type: application/pdf");
	header("Cache-Control: no-cache");
	header("Accept-Ranges: none");
	header("Content-Disposition: attachment; filename=\"CASSCON_INVOICE.pdf\"");
	
	echo $pdf;*/
	?>
	</body>
	<script type="text/javascript" language="javascript">
		$(document).ready(function(){	
		  window.print();
		  
		  setTimeout(function()
		  {
		//window.close();
		window.open('', '_self', '');
		//window.close();
		},1500);
		 
		});
	</script>
</html>