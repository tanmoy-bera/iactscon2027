<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.dinner.php");
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
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>online.payment.success.css" />
	</head>
	<body> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">                
				<?
				leftCommonMenu();
				
				$cutoffs 			= fullCutoffArray();	
				$currentCutoffId 	= getTariffCutoffId();
				$slipId	 			= $mycms->getSession('SLIP_ID');
				$delegateId	        = $mycms->getSession('LOGGED.USER.ID');
				$userDetails	    = getUserDetails($delegateId);
				?>
                <div class="col-xs-11 profileright-section" style="width: 85%">
                    <div class="log"><h1>THANK YOU</h1></div>
                    <div class="summery">
						<h3>
							PAYMENT PROCEDURE IS SUCCESSFUL 							
							<a class="download pull-right" style="padding:5px 10px;" href="downloadSlippdf.php?delegateId=<?=$mycms->encoded($delegateId)?>&slipId=<?=$mycms->encoded($slipId)?>" target="_blank"><i class="fas fa-download"></i></a>
							<a class="download pull-right" style="padding:5px 10px;" href="downloadFinalInvoice.php?delegateId=<?=$mycms->encoded($delegateId)?>&slipId=<?=$mycms->encoded($slipId)?>" target="_blank"><i class="fas fa-print"></i></a>
						</h3>
					</div>                    
                    <div class="paynow-msg" style="margin-top: 25px;">
						<h5>Welcome to the <?=$cfg['EMAIL_CONF_NAME']?></h5>
						<h5>Invoice and Registration Confirmation will be mailed to the registered E-mail id</h5>
                        <h5>Payment Confirmation will be mailed at <b><?=$userDetails['user_email_id']?></b></h5>
                    </div>
					<?
					$rowSlip = slipDetails($slipId);
					$invoiceArr = invoiceDetailsOfSlip($slipId);
					$counter = 0;
					$delgId = "";
					$unavalableForPaymentStatus = 'NO';
					$invoiceDetailsArr = array();
					$wrkshpName		= array();
					foreach($invoiceArr as $key => $invoiceDetails)
					{
						$counter 		 = $counter + 1;
						$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
						
						$type			 = "";
						$btnCss ='';
						
						if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
						{
							if($mycms->getSession('LOGGED.USER.ID') == $invoiceDetails['delegate_id'])
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['btnCss'] ='style="display:none;"';
							}
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = "CONFERENCE REGISTRATION";
						}
						if($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
						{
							if($mycms->getSession('LOGGED.USER.ID') == $invoiceDetails['delegate_id'])
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['btnCss'] ='style="display:none;"';
							}
							
							
							if($thisUserDetails['registration_classification_id'] == 3)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 7)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_2N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 8)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_3N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 9)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_2N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 10)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_3N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 11)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_2N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 12)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_3N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 13)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_2N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 14)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_3N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 15)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_2N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 16)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_IN_3N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 17)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_2N']."- ".$thisUserDetails['user_full_name'];
							}
							else if($thisUserDetails['registration_classification_id'] == 18)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME_SH_3N']."- ".$thisUserDetails['user_full_name'];
							}
							
							//$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] = $cfg['RESIDENTIAL_NAME']."- ".$thisUserDetails['user_full_name'];
						}
						if($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
						{
							$workshopCountArr = totalWorkshopCountReport();
							$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
							$workshopCount = $workshopCountArr[$workShopDetails['workshop_id']]['TOTAL_LEFT_SIT'];
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type'] =  strtoupper(getWorkshopName($workShopDetails['workshop_id']));
							if($workshopCount<1)
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['chkStatus'] =  "NOT_AVALABLE";
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['span'] =  "<span style='color: red;'>** No More Seat Available For This Workshop</span>";
								$unavalableForPaymentStatus = 'YES';
								$wrkshpName[]	=  getWorkshopName($workShopDetails['workshop_id']);
							}
						}
						if($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST")
						{
							$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
							$dinnerRefId = $dinnerDetails['refference_id'];
							$dinner_user_type = dinnerForWhome($dinnerRefId);
							if($dinner_user_type=='ACCOMPANY')
							{
								$invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"DINNER");
							}
							else
							{
								$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"DINNER");
							}
						}
						if($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
						{
							$accompanyDetails 				 = getUserDetails($invoiceDetails['refference_id']);
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = "ACCOMPANYING PERSON  - ".$accompanyDetails['user_full_name'];
						}
						if($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
						{
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = "ACCOMMODATION BOOKING - ".$thisUserDetails['user_full_name'];
						}
						if($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST")
						{
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['tourDetails'] = getTourDetails($invoiceDetails['refference_id']);
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['type']  = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
						}
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cssStyle'] ='style="display:none;"';
						if($invoiceDetails['status'] == "A")
						{
							$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cssStyle'] ='';
						}			
						
									
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['delgId'] 				= $invoiceDetails['delegate_id'];			
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['invoice_number'] 		= $invoiceDetails['invoice_number'];	
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['currency'] 				= $invoiceDetails['currency'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['service_roundoff_price'] = $invoiceDetails['service_roundoff_price'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['service_type']			= $invoiceDetails['service_type'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['delegate_id'] 			= $invoiceDetails['delegate_id'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['id'] 					= $invoiceDetails['id'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['refference_id'] 			= $invoiceDetails['refference_id'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['invoice_mode'] 			= $rowSlip['invoice_mode'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['counter'] 				= $counter;
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['service_unit_price'] 	= $invoiceDetails['service_unit_price'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['applicable_tax_amount'] 	= $invoiceDetails['applicable_tax_amount'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['internet_handling_amount'] = $invoiceDetails['internet_handling_amount'];
						
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cgst_percentage'] 		  = $invoiceDetails['cgst_percentage'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cgst_price'] 			  = $invoiceDetails['cgst_price'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['sgst_percentage'] 		  = $invoiceDetails['sgst_percentage'];			
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['sgst_price'] 			  = $invoiceDetails['sgst_price'];
						
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cgst_int_percentage'] 	  = $invoiceDetails['cgst_int_percentage'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['cgst_int_price'] 		  = $invoiceDetails['cgst_int_price'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['sgst_int_percentage'] 	  = $invoiceDetails['sgst_int_percentage'];
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['sgst_int_price'] 		  = $invoiceDetails['sgst_int_price'];
						
						$invoiceDetailsArr[$invoiceDetails['delegate_id']][$invoiceDetails['service_type']][$counter]['invoice_mode'] 			  = $invoiceDetails['invoice_mode'];
						
					}
				?>
					<div class="paydel spaceless timer">
						<div class="billing">
							<table class="table">
								<tr>
									<th colspan="4">Billed to</th>
								</tr>
								<tr>
									<td>Name:</td>
									<td><?=$thisUserDetails['user_full_name']?></td>
									<td>Date:</td>
									<td><?=date("d/m/Y",strtotime($rowSlip['slip_date']))?></td>
								</tr>
								<tr>
									<td>E-mail id:</td>
									<td><?=$thisUserDetails['user_email_id']?></td>
									<td>PV No:</td>
									<td><?=$rowSlip['slip_number']?></td>
								</tr>
								<tr>
									<td>Mobile:</td>
									<td><?=$thisUserDetails['user_mobile_isd_code']?> <?=$thisUserDetails['user_mobile_no']?></td>
									<td>Payment Status:</td>
									<td><?=(($rowSlip['payment_status']=="UNPAID")?"PENDING":"PAID")?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="orangetab table-heading-black" style="text-align:center; background: #72cff7">
						<h3>Order Summary</h3>
					</div>
					<div class="orangetab">
						<div class="table">
							<table class="table">
								<tr>
									<th colspan="2" class="table-heading" width="200px" style="font-weight:bold; width:200px;">Invoice No.</th>
									<th colspan="1" class="table-heading" style="font-weight:bold; text-align:left;">Invoice for</th>
									<th colspan="2" class="table-heading" width="120px" style="font-weight:bold; text-align:right;" align="right">Amount&nbsp;(<?=$invoiceDetails['currency']?>)</th>
								</tr>
								<tr>
									<td colspan="5" style="padding:0px; border:none;">
										<div class="innerordertab">
											<table class="table" style="border:none;">
												<?
												
												if(!$invoiceDetailsArr)
												{							
													$mycms->redirect("profile.php");
												}
												$sequence = $cfg['SERVICE.SEQUENCE'];
							
												$count = 0;
												
												foreach($invoiceDetailsArr as $key1 => $serviceList)
												{				
													$sqlUserDetails = array();
													$sqlUserDetails['QUERY'] = "SELECT `user_full_name` 
																				 FROM "._DB_USER_REGISTRATION_."
																				WHERE `id` = ?";	
																				
													$sqlUserDetails['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$key1, 'TYP' => 's');
																									
													$userName = $mycms->sql_select($sqlUserDetails);							
												?>
													<tr>									
														<td colspan="3" class="table-body" ><h5><?=$userName[0]['user_full_name']?></h5></td>
													</tr>
												<?
													foreach($sequence as $key2 => $seqName)
													{
														$invoiceList = $serviceList[$seqName];
														foreach($invoiceList as $key => $invoiceDetails)
														{ 
															
															$count = $count +1;	
												?>
															<tr>
																<td class="table-body-section" width="200px" style=" width:200px;"><?=$invoiceDetails['invoice_number']?></td>
																<td class="table-body-section" align="center"><?=$invoiceDetails['type']?><br/><?=$invoiceDetails['span']?></td>
																<td class="table-price" width="120px" style="font-weight:bold; " align="right"><?=$invoiceDetails['service_roundoff_price']?></td>
															</tr>
												<?
														}
													}
												}
												
												?>
											</table>
										</div>
									</td>
								</tr>
								<tr>
                                    <td colspan="2"  class="table-tax-text"><h5>Inclusive of All Taxes</h5></td>
                                    <td colspan="1" class="table-total-text"><h5>Total Amount</h5></td>
                                    <td colspan="2"  class="table-total-price">
										<span>
										<?=$rowSlip['currency']?> &nbsp;
										<?=invoiceAmountOfSlip($rowSlip['id'])?>
										</span>
                                    </td>
								</tr>
							</table>
						</div>
					</div>
					<?php
					$resPaymentDetails      = paymentDetails($slipId);
				
					$paymentDescription     = "-";
					if($resPaymentDetails['payment_mode']=="Online")
					{
						$payDate = setDateTimeFormat2($resPaymentDetails['payment_date'], "D");
						$paymentDescription = " Transaction No. <b>".$resPaymentDetails['atom_atom_transaction_id']."</b><br>
												Bank Transaction No. <b>".$resPaymentDetails['atom_bank_transaction_id']."</b>";
					}
					if($resPaymentDetails['payment_mode']=="Cash")
					{
						$payDate = setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D");
						$paymentDescription = "--";
					}
					if($resPaymentDetails['payment_mode']=="Card")
					{
						$payDate = setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D");
						$paymentDescription = "Reference No. <b>".$resPaymentDetails['card_refference_no']."</b>";
					}
					if($resPaymentDetails['payment_mode']=="Draft")
					{
						$payDate = setDateTimeFormat2($rowFetchSlip['draft_date'], "D");
						$paymentDescription = "Draft No. <b>".$rowFetchSlip['draft_number']."</b><br>
											   Draft Date: <b>".setDateTimeFormat2($rowFetchSlip['draft_date'], "D")."</b>
											   Draft Drawn Bank: <b>".$rowFetchSlip['draft_bank_name']."</b>";
					}
					if($resPaymentDetails['payment_mode']=="NEFT")
					{
						$payDate = setDateTimeFormat2($resPaymentDetails['neft_date'], "D");
						$paymentDescription = "Transaction No. <b>".$resPaymentDetails['neft_transaction_no']."</b><br>
											   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['neft_date'], "D")."</b>
											   Transaction Bank: <b>".$resPaymentDetails['neft_bank_name']."</b>";
					}
					if($resPaymentDetails['payment_mode']=="RTGS")
					{
						$payDate = setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D");
						$paymentDescription = "Transaction No. <b>".$resPaymentDetails['rtgs_transaction_no']."</b><br>
											   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D")."</b>
											   Transaction Bank: <b>".$resPaymentDetails['rtgs_bank_name']."</b>";
					}
					if($resPaymentDetails['payment_mode']=="Cheque")
					{
						$payDate = setDateTimeFormat2($resPaymentDetails['cheque_date'], "D");
						$paymentDescription = "Cheque No. <b>".$resPaymentDetails['cheque_number']."</b><br>
											   Cheque Date: <b>".setDateTimeFormat2($resPaymentDetails['cheque_date'], "D")."</b>
											   Cheque Drawee Bank: <b>".$resPaymentDetails['cheque_bank_name']."</b>";
					}
				?>
					<div class="orangetab table-heading-black" style="text-align:center;">
						<h3>Transaction Summary</h3>
					</div>
					<div class="orangetab">
						<div class="table">
							<table class="table">
								<tr>
									<th class="table-body"> <h3>PV No.<br>
                                        <span><?=$rowSlip['slip_number']?></span></h3>
                                    </th>
									<th  class="table-body"><h3>Amount (<?=getRegistrationCurrency(getUserClassificationId($delegateId))?>)<br>
                                        <span><?=getRegistrationCurrency(getUserClassificationId($delegateId))?> 
										<?=invoiceAmountOfSlip($slipId)?></span></h3>
                                    </th>
									<th  class="table-body"><h3>Payment Mode<br>
                                        <span><?=$resPaymentDetails['payment_mode']?></span></h3>
                                    </th>
									<th  class="table-body"><h3>Payment Date<br>
                                        <span><?=$payDate?></span></h3>
                                    </th>
									<th  class="table-body"><h3>Transaction Details<br>
                                        <span><?=$paymentDescription?></span></h3>
                                    </th>
								</tr>
							</table>
						</div>
					</div>
                    <div class="bttn" style="margin-top: 25px;">
						<form name="onlinePaymenttoProfileFrm" id="onlinePaymenttoProfileFrm" method="post" action="<?=$cfg['BASE_URL']?>login.process.php">
							<input type="hidden" name="action" value="loginUniqueSequence" />
							<input type="hidden" name="user_details" value="<?=$userDetails['user_unique_sequence']?>" />
							<button onClick="window.location.href='login.php'" style="background:#7f8080">Proceed to your Profile</button>
						</form>
					</div>
                </div>
            </div>
        </div>
    </div>
	</body>
</html>