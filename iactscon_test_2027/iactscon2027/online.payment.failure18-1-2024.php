<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
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
	</head>
	<body> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">                
				<?
				$slipId	 			= $mycms->getSession('SLIP_ID');
				
				$sqlSlip            = array();				
				$sqlSlip['QUERY']   = "SELECT * FROM "._DB_SLIP_." 
										WHERE `status` =? 
										AND `id` =?";										
				$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',      'TYP' => 's');
				$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' =>$slipId,  'TYP' => 's');										
				
				$resSlip			= $mycms->sql_select($sqlSlip);
				$rowSlip			= $resSlip[0];
				
				$userDetails		= getUserDetails($rowSlip['delegate_id']);


				$sql 	=	array();
							$sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
													WHERE `status`='A' order by id desc limit 1";
							 //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
					$result = $mycms->sql_select($sql);
					$row    		 = $result[0];

					$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['header_image'];
					if($row['header_image']!='')
					{
						$emailHeader  = $header_image;
					}
				// workshop related work for profile by weavers start
				$mycms->setSession('LOGGED.USER.ID',$rowSlip['delegate_id']);
				// workshop related work for profile by weavers end
				leftCommonMenu();

				?>
				
                <div class="col-xs-11 profileright-section" style="width: 85%">
                	<div class="col-xs-12" style="padding: 0;"><img src="<?php echo $header_image; ?>" style="width: 100%"></div>
                    <div class="log1" style="padding-bottom: 6px;"><h1>INCONVENIENCE REGRETTED</h1></div>
                    <div class="login-reg"><h3>Payment Procedure Failed </h3></div>                    
                    <div class="paynow-msg" style="margin-top: 25px;">
						<h5>Please try again to complete the process.</h5>
                    </div>
                    <div class="bttn" style="margin-top: 25px;">
						<form name="frmApplyPayment" id="frmApplyPayment"  action="payment.retry.php" method="post">
							<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
							<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
							<input type="hidden" name="act" value="paymentSet" />
							<input type="hidden" name="mode" value="<?=$rowSlip['invoice_mode']?>" />
							<button type="submit">RETRY</button>
						</form>	
					</div>					
                </div>
            </div>
        </div>
    </div>
	</body>
</html>