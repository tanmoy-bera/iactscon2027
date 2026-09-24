<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");

		$sql = array();
		$sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
								WHERE `status`='A' order by id desc limit 1";
							 //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
		$result = $mycms->sql_select($sql);
		$row    = $result[0];

		$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['header_image'];
		if($row['header_image']!='')
		{
			$emailHeader  = $header_image;
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Registration | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>registration.success.css" />
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
	</head>
	<body> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">                
				<?
				leftCommonMenu();
				
				$userEmailId 	= $_REQUEST['email'];
				$userId 		= base64_decode($userEmailId);
				
				$delegateId 	= $mycms->decoded($_REQUEST['did']);
				$userDetails	= getUserDetails($delegateId);

				if($userDetails['registration_classification_id'] == '6')
				{
				?>
	                <div class="col-xs-11 profileright-section" style="width: 85%">
	                    <div class="log"><h1>THANK TOU</h1></div>
	                    <div class="summery"><h3>REGISTRATION PROCEDURE IS SUCCESSFUL</h3></div>                    
	                    <div class="paynow-msg" style="margin-top: 25px;">
							<h5>Welcome to the <?=$cfg['EMAIL_CONF_NAME']?></h5>
							<h5>Registration Confirmation will be mailed to the registered E-mail id</h5>
							<h5>For any assistance or query, please write to us at <?=$cfg['EMAIL_CONF_EMAIL_US']?></h5>
	                    </div>
	                    <div class="bttn" style="margin-top: 25px;"><button onClick="window.location.href='login.php'" style="background:#7f8080">Proceed to your Profile</button></div>
	                </div>
				<?
				}
				else
				{
				?>
					<div class="col-xs-11 profileright-section" style="width: 85%">
	                   <!--  <div class="log"> -->
	                    	<img src="<?php echo $emailHeader; ?>" width="100%"/>
	                    	<h1>PAYMENT DETAILS ARE NOTED</h1>

	                   <!--  </div> -->
	                    <div class="summery-pending"><h3>PAYMENT STATUS: PENDING</h3></div>
	                    
	                    <div class="paynow-msg" style="margin-top: 25px;">
							<h5>Please follow the instructions given below to complete your payment</h5>
							<h6>Download the <i>Payment Voucher</i></h6>
							<h6>Post the Payment Voucher along with your DD/cheque/cash deposition counterfoil/NEFT/RTGS receipt to the secretariat office.You can also mail us the same at <span><?=$cfg['EMAIL_CONF_EMAIL_US']?>.</span></h6>
						</div>
						<div class="paynow-msg postal" style="margin-top: 15px;">
							<h5><u>POSTAL ADDRESS</u></h5>
							<h6>
								<?php echo $cfg['REG_SUCCESS_PAGE_INFO']; ?>
							</h6>
						</div>
						<div class="paynow-msg" style="margin-top: 15px;">
							<h6>Post it within 10 working days.</h6>
							<h6>The payment procedure will be completed only after the realization of your payment.</h6>
							<h6>Payment Confirmation will be mailed at <strong><span style="color:#003399;"><?=$userId?></span></strong></h6>
	                    </div>
						<div class="paynow-msg" style="margin-top: 15px;">
							<h6>For any assistance or query, please write to us at <b> <?=$cfg['EMAIL_CONF_EMAIL_US']?> </b></h6>
						</div>
	                   
	                    <div class="bttn" style="margin-top: 25px;">
						<?
							 $sqlInvoice   = array();
							 $sqlInvoice['QUERY']   = "SELECT *												 
														FROM "._DB_INVOICE_." 
													   WHERE slip_id =?
														 AND status = ?";
														 
							$sqlInvoice['PARAM'][]  = array('FILD' => 'slip_id', 'DATA' =>$mycms->getSession('TEMP_SLIP_ID'),  'TYP' => 's');
							$sqlInvoice['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',                                 'TYP' => 's');
							
							$resultInvoice = $mycms->sql_select($sqlInvoice);
							
							foreach($resultInvoice AS $key=>$rowInvoice)
							{
								if($rowInvoice['service_type']== 'DELEGATE_CONFERENCE_REGISTRATION')
								{
									$serviceType = $rowInvoice['service_type'];
									$invoice_mode = $rowInvoice['invoice_mode'];
								}
							}
							
							$slipDetails = slipDetails($mycms->getSession('TEMP_SLIP_ID'));

						?>
							<form method="post" target="_blank" action="downloadSlippdf.php" >
								<input type="hidden" name="delegateId" value="<?=$mycms->encoded($slipDetails['delegate_id'])?>" />
								<input type="hidden" name="slipId" value="<?=$mycms->encoded($mycms->getSession('TEMP_SLIP_ID'))?>" />						
								<button type="submit">Download Payment Voucher</button>
							</form>
						<?
						//echo '<pre>';var_dump($serviceType);echo "</pre>";exit('hi Kalyan. I m in registration success page');
						if( ($serviceType='DELEGATE_CONFERENCE_REGISTRATION' || $serviceType='DELEGATE_RESIDENTIAL_REGISTRATION') && $invoice_mode == 'ONLINE' )
						{
						?>
							<button type="button" onClick="window.location.href='login.php'" >Proceed to your Profile</button>
						<?
						}
						?>
						</div>
	                </div>
				<?
				}
				?>
            </div>
        </div>
    </div>
	</body>
</html>