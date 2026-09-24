<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');
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
	<body style="background:#2a7454;"> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">
                <div style="width:820px; margin: 0 auto;  padding-top:10px;"> 
					 <?

						if(isset($_REQUEST['slip_id']) &&  $_REQUEST['slip_id'] != '')
						{
							$slipId	 			= trim($_REQUEST['slip_id']);
						}
						else
						{
							$slipId	 			= $mycms->getSession('SLIP_ID');
						}
						
						$sqlSlip            = array();				
						$sqlSlip['QUERY']   = "SELECT * FROM "._DB_SLIP_." 
												WHERE `status` =? 
												AND `id` =?";										
						$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',      'TYP' => 's');
						$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' =>$slipId,  'TYP' => 's');										
						
						$resSlip			= $mycms->sql_select($sqlSlip);
						$rowSlip			= $resSlip[0];
						
						$userDetails		= getUserDetails($rowSlip['delegate_id']);
						
						$pendingAmountOfSlip  = pendingAmountOfSlip($slipId);

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

					?>               
                    <div class="col-xs-12" style="border:solid thin #ccc; background:#fff;">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="border-collapse:collapse;">
								<!-- <img src="<?=_BASE_URL_?>images/pvHeader.jpg" width="100%"/> -->
								<img src="<?=$emailHeader?>" width="100%"/>
							</td>
						</tr>
						<tr>
							<td align="center" height="'.(($showHeaderFooter)?'750px':'auto').'" style="border-collapse:collapse; padding:2px;" valign="top">
								<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
								Payment Voucher
								</div>
						<?
						echo getPrintSlipDetailsContent($rowSlip['delegate_id'], $slipId, false, false, true);
						?>
							</td>
						</tr>
						</table>
						
					<?
						if(floatval($pendingAmountOfSlip)>0)
						{
							if($rowSlip['invoice_mode']=='ONLINE' || $_REQUEST['request_payment_mode']==='ONLINE')
							{
					?>	
						<form name="frmApplyPayment" id="frmApplyPayment"  action="registration.process.php" method="post" onSubmit="return validateRegistrationPayment(this)">
						<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
						<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
						<input type="hidden" name="act" value="paymentSet" />
						<input type="hidden" name="mode" value="<?=$rowSlip['invoice_mode']?>" />
						
						<div class="col-xs-12 form-group" use="paymentOption" for="Card" actAs='fieldContainer' style="margin-top:10px;">
							<!-- <div style="display: block; padding: 10px 15px; color: white; background: #2a7454; font-size: 16px; font-weight: normal;">COMPLETE PAYMENT</div> -->
							<!-- <div class="checkbox">
								<label class="select-lable" >Choose Your Card Type</label>
								<div>
									<label class="container-box" style="float:left; margin-right:30px;">
									  <img src="<?=_BASE_URL_?>images/international_globe.png" height="20px;">
									  International Card
									  <input type="radio" name="card_mode" use="card_mode_select" value="International">
									  <span class="checkmark"></span>											   
									</label>
									<label class="container-box" style="float:left; margin-right:30px;">
									  <img src="<?=_BASE_URL_?>images/india_globe.png" height="20px;">
									  Indian Card
									  <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
									  <span class="checkmark"></span>
									</label>
									&nbsp;
								</div>	
							</div> -->
							<input type="radio" name="card_mode" use="card_mode_select" value="Indian" style="display:none" checked>
							<div class = "alert alert-danger" callFor='alert' style="display:none">Please choose a proper option.</div>
							<button type="submit" style="padding: 8px 25px; background: #2a7454; border: 0; color: white;">MAKE PAYMENT</button>
						</div>
						</form>
						
						<script>
						function validateRegistrationPayment(obj)
						{									
							var returnVal 				= true;
							$.each($("div[use=paymentOption][for='Card']"),function(){
								var thiObj = $(this);
								$.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
									if($.trim($(validateObj).val())=='')
									{
										console.log('pay details>>BLANK');
										popoverAlert(validateObj);
										$(validateObj).focus();
										returnVal = false;
										return false;
									}
								});	
								$.each($(thiObj).find("input[type=radio],input[type=checkbox]"),function(i,validateObj){
									var name 		= $(validateObj).attr("name")
									var type 		= $(validateObj).attr("type")
									var parent 		= $(validateObj).parent().closest("div[actAs=fieldContainer]");
									
									var checkedObj 	= $(parent).find("input[type='"+type+"'][name='"+name+"']:checked");
									
									if($(checkedObj).length == 0)
									{
										var checkedOptionObj 	= $(parent).find("input[type='"+type+"'][name='"+name+"']").first();																
										console.log('checkedOptionObj>>NOT SELECTED');
										popoverAlert(checkedOptionObj);
										$(checkedOptionObj).focus();
										returnVal = false;
										return false;
									}
								});	
								if(!returnVal) return false;	
							});
																
							return returnVal;
						}
						
						function popoverAlert(obj, msg)
						{
							var parent 		= $(obj).parent().closest("div[actAs=fieldContainer]");
							var alertObj 	= $(parent).children("div[callFor=alert]");
							
							var attr = $(alertObj).attr('defaultAlert');
							if (typeof attr === typeof undefined || attr === false) 
							{
								$(alertObj).attr('defaultAlert', $(alertObj).text());
								$(alertObj).click(function(){
									popDownAlert(this);
								});
							}						
							
							if(typeof msg !== typeof undefined && $.trim(msg) !== '')
							{
								$(alertObj).text(msg);
							}
							else
							{
								$(alertObj).text($(alertObj).attr('defaultAlert'));
							}
							
							$(alertObj).show();
						}
						</script>
					<?
							}
							elseif($rowSlip['invoice_mode']=='OFFLINE')
							{
					?>	
						<form name="frmApplyPayment" id="frmApplyPayment"  action="registration.process.php" method="post" onSubmit="return validateRegistrationPayment(this)">
						<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
						<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
						<input type="hidden" name="act" value="paymentSet" />
						<input type="hidden" name="mode" value="<?=$rowSlip['invoice_mode']?>" />
						
						<div class="col-xs-12 form-group" use="paymentOption" for="Card" actAs='fieldContainer' style="margin-top:10px;">
							<div style="display: block; padding: 10px 15px; color: white; background: #2a7454; font-size: 16px; font-weight: normal;">COMPLETE PAYMENT</div>
							
							<div class="col-xs-12 form-group" use="offlinePaymentOptionChoice" actAs='fieldContainer'>
								<div class="checkbox">
									<label class="select-lable" >Payment Option</label>
									<div>
										<label class="container-box" style="float:left; margin-right:30px;">Cheque
										  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" paymentMode='OFFLINE'>
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:30px;">Draft
										  <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft" paymentMode='OFFLINE'>
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:30px;">NEFT
										  <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT" paymentMode='OFFLINE'>
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:30px;">RTGS
										  <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS" paymentMode='OFFLINE'>
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:30px;">Cash
										  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash" paymentMode='OFFLINE'>
										  <span class="checkmark"></span>
										</label>
										&nbsp;
									</div>																				
								</div>
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
								
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash" actAs='fieldContainer'>
								<label for="user_first_name">Money Order Sent Date</label>
								<input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash">
								Payment can be sent by money order to the NEOCON 2022 Registration Secretariat. 
								Direct deposition will not be accepted.<br>
								For any query, please write at neocon2022@gmail.com or call at 9674387711<br><br>
							</div>
							
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
								<label for="user_first_name">Cheque No</label>
								<input type="text" class="form-control" name="cheque_number" id="cheque_number">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
								<label for="user_first_name">Drawee Bank</label>
								<input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
								<label for="user_first_name">Cheque Date</label>
								<input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque">
								Cheque or DD should be made in favor of "<b>Neonatology Society West Bengal</b>", Payable at Kolkata<br> 
								The same should be posted to the address, mentioned below - <br> 
								NEOCON 2022 Secretariat <br> 
								c/o RUEDA <br> 
								DL 220, Sector II, Salt Lake, Kolkata 700091<br>
								For any query, please write at neocon2022@gmail.com or call at 9674387711<br><br>
							</div>
							
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
								<label for="user_first_name">Draft No</label>
								<input type="text" class="form-control" name="draft_number" id="draft_number">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
								<label for="user_first_name">Drawee Bank</label>
								<input type="text" class="form-control" name="draft_drawn_bank" id="draft_drawn_bank">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
								<label for="user_first_name">Draft Date</label>
								<input type="date" class="form-control" name="draft_date" id="draft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft">
								Demand Draft should be made in favor of "<b>Neonatology Society West Bengal</b>", Payable at Kolkata<br>
								The same should be posted to the address, mentioned below - <br> 
								NEOCON 2022 Secretariat <br> 
								c/o RUEDA <br> 
								DL 220, Sector II, Salt Lake, Kolkata 700091 <br>
								For any query, please write at neocon2022@gmail.com or call at 9674387711<br><br>	
							</div>
							
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
								<label for="user_first_name">Transaction Id</label>
								<input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
								<label for="user_first_name">Drawee Bank</label>
								<input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
								<label for="user_first_name">Date</label>
								<input type="date" class="form-control" name="neft_date" id="neft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT">
								NEFT or RTGS will be accepted for payment.<br>
								<u>Bank Details</u> <br>
								Bank Name: ICICI Bank.<br>
								Branch Address:  92/1 B Jatin Das Road, Kolkata 700029<br>
								Account No.: 919020002426962<br> 
								IFSC: ICIC0004411<br>
								For any query, please write at neocon2022@gmail.com or call at 9674387711<br><br>  
							</div>
							
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
								<label for="user_first_name">Transaction Id</label>
								<input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
								<label for="user_first_name">Drawee Bank</label>
								<input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name">
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
								<label for="user_first_name">Date</label>
								<input type="date" class="form-control" name="rtgs_date" id="rtgs_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
							<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS">
								NEFT or RTGS will be accepted for payment.<br>
								<u>Bank Details</u> <br>
								Bank Name: ICICI Bank.<br>
								Branch Address:  92/1 B Jatin Das Road, Kolkata 700029<br>
								Account No.: 919020002426962<br> 
								IFSC: ICIC0004411<br>
								For any query, please write at neocon2022@gmail.com or call at 9674387711<br><br>  
							</div>
							<button type="submit" style="padding: 8px 25px; background: #2a7454; border: 0; color: white;">MAKE PAYMENT</button>
						</div>
						</form>
						
						<script>
						$(document).ready(function(){
							$("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").click(function(){							
								var forPay = $(this).attr("for");
								$("div[use=offlinePaymentOption]").hide();
								$("div[use=offlinePaymentOption][for='"+forPay+"']").slideDown();
								
								var paymentMode = $(this).attr("paymentMode");							
								$("input[type=radio][use=tariffPaymentMode]").prop("checked",false);
								$("input[type=radio][use=tariffPaymentMode][value='"+paymentMode+"']").first().prop("checked",true);
							});				
						});
						
						function validateRegistrationPayment(obj)
						{									
							var returnVal 				= true;
							
							var paymentOptionCheckedOb 	= $("div[use=offlinePaymentOptionChoice]").find("input[type=radio][name=payment_mode]:checked");
							
							var paymentOptionChecked	= $(paymentOptionCheckedOb).attr("for");
							
							$.each($("div[use=offlinePaymentOption][for='"+paymentOptionChecked+"']"),function(){
								var thiObj = $(this);
								$.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
									if($.trim($(validateObj).val())=='')
									{
										console.log('pay details>>BLANK');
										popoverAlert(validateObj);
										$(validateObj).focus();
										returnVal = false;
										return false;
									}
								});	
								if(!returnVal) return false;	
							});
																
							return returnVal;
						}
						
						function popoverAlert(obj, msg)
						{
							var parent 		= $(obj).parent().closest("div[actAs=fieldContainer]");
							var alertObj 	= $(parent).children("div[callFor=alert]");
							
							var attr = $(alertObj).attr('defaultAlert');
							if (typeof attr === typeof undefined || attr === false) 
							{
								$(alertObj).attr('defaultAlert', $(alertObj).text());
								$(alertObj).click(function(){
									popDownAlert(this);
								});
							}						
							
							if(typeof msg !== typeof undefined && $.trim(msg) !== '')
							{
								$(alertObj).text(msg);
							}
							else
							{
								$(alertObj).text($(alertObj).attr('defaultAlert'));
							}
							
							$(alertObj).show();
						}
						</script>
					<?
							}
						}
					?>	
					</div>
					
                </div>
            </div>
        </div>
    </div>
	</body>
</html>