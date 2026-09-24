<?php
	function accgetSateList($countryId="")
	{
		global $cfg, $mycms;
		?>
		<option value="">-- Choose State --</option>
		<?php
		
		$sqlFetchUser['QUERY']		  = "SELECT DISTINCT(delegate.user_state_id) AS stateId,
														   delegate.user_country_id AS countryId,
														   state.state_name AS stateName,state.st_id AS st_id
													  FROM "._DB_USER_REGISTRATION_." delegate 
											   INNER JOIN "._DB_COMN_STATE_." state
															ON delegate.user_state_id = state.st_id
															AND state.country_id = '".$countryId."'
												INNER JOIN "._DB_INVOICE_." invoice
															ON invoice.delegate_id = delegate.id
															AND invoice.payment_status ='PAID'
															AND invoice.service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION'
															AND invoice.status = 'A'
														  WHERE delegate.user_country_id = '".$countryId."'
													  ORDER BY state.state_name ASC";
		$resultState	= $mycms->sql_select($sqlFetchUser);
		if($resultState)
		{
			foreach($resultState as $keyState=>$rowState)
			{
			?>
				<option value="<?=$rowState['st_id']?>"><?=$rowState['stateName']?></option>
			<?php
			}
		}
	}
												   
	function refundCancelInvoiceDetails($cfg, $mycms)
	{   
		global $cfg, $mycms;
		
		$delegateId 	 =  $_REQUEST['id'];
		$unregisterReqId =  $_REQUEST['unregisterReqId'];
		$invoice_id      =  $_REQUEST['invoice_id'];
		$processPage     = "registration.process.php";
		$requestPage     = $_REQUEST['requestPage'];
		
		
		
		$searchCondition = "AND invoice.id = '".$invoice_id."'";
		$userDetail 	 = getcancelUserdetails($searchCondition);			   
		$resSlip    	 = $mycms->sql_select($userDetail);
		
		$invoiceDetails  = $resSlip[0];
		$rowFetchUser    = getUnregisterUserDetails($invoiceDetails['invoiceDelegateId']);
		
		$invoiceHistory  = getCancelInvoiceHistory($invoice_id);
		
		$unpaidstatus 	= "<span style=color:#CC0000;>Invoice is Unpaid ,So refund is not possible.</span>";
		
		$green 			= "style=color:#006600";
		$blue 			= "style=color:#000099";
		$sky 			= "style=color:#6600FF";
		$red 			= "style=color:#CC0000;";
		
		?>
		<script>
			$(document).ready(function(){
				$("#onlinemode").hide();
			});
			function validateAmount()
			{
				
				$amount = $("#amount").val();
				$refunded_amount = $("#refunded_amount").val();
				
				if(parseFloat($refunded_amount)>parseFloat($amount))
				{
					alert("Refunded amount should be less then invoice amount");
					return false;
					
				}
				if($refunded_amount == "")
				{
					alert("Please enter refund amount");
					return false;
					
				}
				
			}
			function changeMode($ob)
			{
				if($ob=="OFFLINE")
				{
					$("#offlinemode").show();
					$("#onlinemode").hide();
					$("#offlinedetails").show();
				}
				if($ob=="ONLINE")
				{
					$("#offlinedetails").hide();
					$("#offlinemode").hide();
					$("#onlinemode").show();
				}
			}
		</script>
		<table width="100%" class="tborder">
			<tr>
				<td class="tcat">
					<span style="float:left">Profile</span>
				</td>
			</tr>
		</table>
		<table width="100%" align="center" class="tborder"> 
			<tbody>
				<tr>
				
					<td colspan="2" style="margin:0px; padding:0px;">  
						<form action="refund_process.php" onsubmit="return validateAmount();">
							<table width="100%">
								<tr class="thighlight" bgcolor="#CCCCCC">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left">
										<?=strtoupper($rowFetchUser['user_full_name'])?> 
										
									</td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left">Registration Id:</td>
									<td align="left">	
									<?php
									if($rowFetchUser['registration_payment_status']!="UNPAID")
									{
										echo $rowFetchUser['user_registration_id'];
									}
									else
									{
										echo "-";
									}
									?>
									</td>
									<td align="left">Unique Sequence:</td>
									<td align="left">
									<?php
									if($rowFetchUser['registration_payment_status']!="UNPAID")
									{
										echo $rowFetchUser['user_unique_sequence'];
									}
									else
									{
										echo "-";
									}
									?></td>
								</tr>
								<tr>
									<td align="left">Mobile:</td>
									<td align="left"><?=$rowFetchUser['user_mobile_isd_code']." - ".$rowFetchUser['user_mobile_no']?></td>
									<td align="left">Email Id:</td>
									<td align="left"><?=$rowFetchUser['user_email_id']?></td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left"><?=strtoupper($rowFetchUser['registration_mode'])?></td>
									<td align="left">Registration Date:</td>
									<td align="left"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?></td>
								</tr>
							</table>
							<table width="100%">
								<tr class="thighlight"  bgcolor="#CCCCCC">
									<td colspan="4" align="left">Invoice Details</td>
								</tr>
								<tr>
									
									<td width="20%" align="left">Slip Number:</td>
									<td width="30%" align="left"><?=$invoiceDetails['slipNumber']?></td>
									<td width="20%" align="left">Invoice Number:</td>
									<td width="30%" align="left">
										<?=$invoiceDetails['invoiceNumber']?>
									</td>
								</tr>
								<tr>
									<td align="left">Slip User Name:</td>
									<td align="left">	
									<?=$invoiceDetails['slipUserName']?>
									</td>
									<td align="left">Invoice Cancellation Date:</td>
									<td align="left">
									<?=$invoiceDetails['created_dateTime']?></td>
								</tr>
								<tr>
									<td align="left">Payment Mode:</td>
									<td align="left"><?=$invoiceDetails['invoice_mode']?></td>
									<?
									if($invoiceDetails['invoice_mode']=="ONLINE")
									{
									?>
										<td align="left">Transection Id:</td>
										<td align="left"><?=$invoiceDetails['atom_atom_transaction_id']?></td>
									<?
									}
									else
									{
									?>
										<td align="left">Offline Mode:</td>
										<td align="left"><?=$invoiceDetails['payment_mode']?></td>
									<?
									}
									?>
								</tr>
							</table>
							<table width="100%">
								<tr class="thighlight"  bgcolor="#CCCCCC">
									<td colspan="" align="left">Invoice History</td>
									<td colspan="" align="left">Date & Time</td>
									<td colspan="" align="left">User Name</td>
								</tr>
								<tr>
									<td width="20%" align="left" <?=$green?>>Invoice Created </td>
									<td width="30%" align="left"><?=$invoiceDetails['invoiceCreatedDate']?></td>
									<td width="30%" align="left">
										<?=$invoiceDetails['slipUserName']?> 
										
									</td>
								</tr>
								<?
								foreach($invoiceHistory as $key=>$rowfetch)
								{
									if($rowfetch['REQUEST_TYPE']=='Request For Cancellation')
									{
										$color 		 = $red;
										$requestType = $rowfetch['REQUEST_TYPE'];
										$createdBy = strtoupper($rowFetchUser['user_first_name'].' ' . $rowFetchUser['user_middle_name'].$rowFetchUser['user_last_name']);
									}
									if($rowfetch['REQUEST_TYPE']=='Approved Cancel Invoice')
									{
										$color 		 = $blue;
										$requestType = $rowfetch['REQUEST_TYPE'];
										$createdBy   = $rowfetch['CREATET_BY'];
									}
									if($rowfetch['REQUEST_TYPE']=='Refund Invoice Amount')
									{
										$color 		 = $sky;
										$requestType = $rowfetch['REQUEST_TYPE'].' '.'(INR)'.' '.$rowfetch['REFUND_AMOUNT'];
										$createdBy   = $rowfetch['CREATET_BY'];
									}
								?>
									<tr>
									<td width="25%" align="left" <?=$color?>><?=$requestType?></td>
									<td width="30%" align="left"><?=$rowfetch['ACTION_DATE']?></td>
									<td width="30%" align="left"><?=$createdBy?></td>
								</tr>
								<?
								}
								?>
							</table>
							<? 
							if(($invoiceDetails['request_status']=="Approve" && $invoiceDetails['paymentStatus'] =='PAID'))
							{
							?>
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left" width="20%">Refund Mode</td>
									
									
								</tr>
								<tr>
									<td align="left" valign="top"  width="20%">
										Refund Mode : 	
									</td>
									<td align="left" valign="top" >
										<select onchange="changeMode(this.value);" style="width:30%;" name="payment_mode">
											<option value="OFFLINE">OTHER</option>
											<option value="ONLINE">Via ATOM </option>
										</select>
									</td>
									<td align="left" valign="top" ></td>
								</tr>
								<tr id="offlinemode">
									<td colspan="6">
										<?=cancelpaymentArea();?>
										
									</td>						
								</tr>
								<tr id="onlinemode">
									<td colspan="4">
										<table width="100%">
											<tr class="thighlight">
												<td align="left" width="20%">Payment Record</td>
											</tr>
											<tr>
											
												<td align="left">
													Transection Number :  &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="" value="" style="width:35%;" placeholder="Transection Number" />
												</td>					
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<?
							}
							elseif($invoiceDetails['request_status']=='Refunded')
							{
								
							?>
							<table width="100%">
								<tr class="thighlight"  bgcolor="#CCCCCC">
								  <td colspan="4">Payment Details</td>
								</tr>
								<tr >
									<td colspan="" align="left" width="20%">Refund Mode :</td><td colspan="" align="left" width="20%"><?=$invoiceDetails['refund_payment_mode']?></td>
									<td colspan="" align="left"  width="20%"> Date :</td><td colspan="" align="left" width="20%"><?=$invoiceDetails['payment_date']?></td>
									
								</tr>
								<?
									if($invoiceDetails['refund_payment_mode']!="Cash")
									{
								?>
								<tr>
									<td colspan="" align="left"  width="20%">Number :</td><td colspan="" align="left" width="20%"><?=$invoiceDetails['transection_number']?></td>
									<td colspan="" align="left"  width="20%">Bank Name :</td><td colspan="" align="left" width="20%"><?=$invoiceDetails['bank_name']?></td>
								</tr>
								<?
									}
								?>
							</table>
							<?
								}
							?>	
							
							<table width="100%">
								<tr class="thighlight"  bgcolor="#CCCCCC">
									<td colspan="" align="left" width="20%">Request For</td>
									<td colspan="" align="left"  width="20%">Invoice Amount</td>
									
									<td colspan="" align="left"  width="20%"><?=$invoiceDetails['request_status']=='Request'?'Action':'Refund Amount'?></td>
									
									
								</tr>
								<?
									$sql['QUERY'] = "SELECT * 
													 FROM "._DB_CANCEL_INVOICE_." 
													WHERE  `delegate_id` = '".$delegateId."'
													  AND `status` = 'A'
												 ORDER BY id DESC";
													
									$resultUnregisterConference 	= $mycms->sql_select($sql);
									
									?>
								<tr bgcolor="#FFCC99">
										<td align="left" valign="top" >
										<?
											if($invoiceDetails['invoiceFor']=="DELEGATE_CONFERENCE_REGISTRATION")
											{
												echo getRegClsfName($invoiceDetails['registrationClassificationId']);
											}
											if($invoiceDetails['invoiceFor']=="DELEGATE_RESIDENTIAL_REGISTRATION")
											{
												echo getRegClsfName($invoiceDetails['registrationClassificationId']);
											}
											if($invoiceDetails['invoiceFor']=="DELEGATE_WORKSHOP_REGISTRATION")
											{
												$workShopDetails = getWorkshopDetails($invoiceDetails['reqId']);
												$sqlDetails['QUERY'] = "SELECT * FROM "._DB_REQUEST_WORKSHOP_." WHERE `id` = '".$invoiceDetails['reqId']."'";
												$resDetails = $mycms->sql_select($sqlDetails);
												$rowDetails = $resDetails[0];
												
												echo getWorkshopName($rowDetails['workshop_id']);
											}
										 ?>
										 </td>
										<td align="left" valign="top" width="30%">
										<?=$invoiceDetails['amount']?>
										</td>
										<?
										if($invoiceDetails['request_status']!='Request')
										{
										?>
											<td align="left" valign="top" width="30%">
										<?
											if($invoiceDetails['paymentStatus'] =='PAID'){
										?>
										
											<input type="hidden" name="amount"  id="amount" value="<?=$invoiceDetails['amount']?>" />
											<input type="hidden" name="unregisterReqId" value="<?=$unregisterReqId?>" />
											<input type="hidden" name="delegateId" value="<?=$invoiceDetails['invoiceDelegateId']?>" />
											<input type="text" name="refunded_amount" id="refunded_amount" placeholder="Enter refund amount" value="<?=$invoiceDetails['refunded_amount'] !=""?$invoiceDetails['refunded_amount']:$invoiceDetails['amount']?>"/>
											 <input type="submit" value="Submit" class="btn btn-small btn-red" />
										
										<?
										}
										
										else
										{
											echo $unpaidstatus;
										}
										
										?>
										</td>
										
										<?
										}
										else
										{
											
										?>
											<td align="left" valign="top" width="30%">
											<?
											
												$userCancelURL = $processPage."?act=Unregister&user_id=".$invoiceDetails['slipDelegateId']."&unregisterReqId=".$invoiceDetails['cancelationId']."&invoice_id=".$invoiceDetails['invoiceId'];
											?>
												<a href="<?=$userCancelURL?>" >
													<input type="button" name="bttnUnregister" id="bttnUnregister" class="ticket ticket-important" value="Approve" />
												</a>
											</td>
											
										<?
										}
										?>	
									</tr>
							</table>
						</form>	
						<table width="100%">
							<tr>
								<td align="right">
									<input type="button" value="Back" class="btn btn-small btn-blue" onclick="window.location.href=<?=$requestPage?>" />
								</td>
							</tr>
						</table>
					</td>
					
				
				</tr>
			</tbody> 
		</table>
	<?
	}	
	
	function complimentryRegistrationSummeryTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		$slipId	 			= $mycms->getSession('SLIP_ID');
		
		$sqlSlip['QUERY']		    = "SELECT * FROM "._DB_SLIP_." WHERE `status` = 'A' AND `id` = '".$slipId."'";
		$resSlip			= $mycms->sql_select($sqlSlip);
		$rowSlip			= $resSlip[0];
		
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		
		?>
		<script>
		function validateRegistrationSummary(obj)
		{
			var parent = $(obj).find("table").first();
			var discount = $(parent).find("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
			if(discount>0)
			{		
				if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 	
					return false;
				}	
				
				var discountAmount = $("input[type=text][operationMode=discountAmount]");		
				if(isNaN((discountAmount).val()))
				{
					alert('Enter Discount Amount correctly');
					$(discountAmount).focus();
					status = false;
					return false;
				}
				
				var total = parseFloat($("#totalBillAmount").val());	
				if(total <  parseFloat((discountAmount).val()))
				{
					alert('Enter Discount Amount correctly');
					$(discountAmount).focus();
					status = false;
					return false;
				}
			}	
			return true;
		}
		
		function makeInvoiceZeroValue(obj)
		{
			var parent = $(obj).parent().closest('tr');
			if($(obj).is(":checked"))	
			{
				$(parent).find("span[use=serviceRoundOffAmount]").html("0.00");
				$(parent).find("span[use=serviceRoundOffAmount]").attr("dVal","0.00");
			}
			else
			{
				var serviceRoundOffAmount = $(parent).find("input[operationMode=invoiceRoundOfPrice]").val();
				$(parent).find("span[use=serviceRoundOffAmount]").html(serviceRoundOffAmount);
				$(parent).find("span[use=serviceRoundOffAmount]").attr("dVal",serviceRoundOffAmount);
			}
			calculateZeroTotal();
		}
		
		function calculateZeroTotal()
		{
			var totalAmount = 0;
			$.each($("table[use=InvoiceTable]").find("span[use=serviceRoundOffAmount]"),function(){
				var amt = parseFloat($(this).html());
				totalAmount += amt;
			});
			$("table[use=InvoiceTable]").find("span[use=totalPayableAmount]").text(totalAmount.toFixed(2));
		}
		</script>
		<form name="frmApplyPayment" id="frmApplyPayment"  action="<?=$processPage?>" method="post" onsubmit="return validateRegistrationSummary(this);">
			<!--<input type="hidden" name="act" value="setPaymentTerms" />-->
			<input type="hidden" name="act" value="makeZeroValue" />
			<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Set Payment</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$userDetails['user_full_name']?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$userDetails['user_mobile_isd_code']?> - <?=$userDetails['user_mobile_no']?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$userDetails['user_email_id']?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($userDetails['registration_tariff_cutoff_id'])?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($userDetails['registration_classification_id'])?></td>
								</tr>
							</table>							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Slip Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Slip Number:</td>
									<td width="30%" align="left"><?=$rowSlip['slip_number']?></td>
									<td width="20%" align="left">Number Of Active Invoice</td>
									<td width="30%" align="left"><?=invoiceCountOfSlip($rowSlip['id'])?></td>
								</tr>
							</table>
							<table width="100%" use="InvoiceTable">
								<tr class="thighlight">
									<td colspan="6" align="left">Invoice Details</td>
								</tr>
								<tr class="theader">
									<td width="30px" align="center">Sl No</td>
									<td width="10%" align="left">Inv. Number</td>
									<td align="left">Invoice For</td>
									<td width="15%" align="right">Invoice Amount</td>
									<td width="20%" align="right">Make Complimentary</td>
									<td width="20%" align="right">Remarks</td>
								</tr>
									<?
									$invoiceDetailsArr = invoiceDetailsOfSlip($rowSlip['id']);
									$counter = 0;
									foreach($invoiceDetailsArr as $key => $invoiceDetails)
									{
										$counter 		 = $counter + 1;
										$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
										$type			 = "";
										if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"CONFERENCE");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"RESIDENTIAL");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"WORKSHOP");
										}
										if($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMPANY");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMMODATION");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($invoiceDetails['refference_id']);
											
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"TOUR");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"DINNER");
										}
										?>
										<input type="hidden" id="invoiceId" name="invoiceId[<?=$invoiceDetails['id']?>]" value="<?=$invoiceDetails['id']?>" />
										<tr class="tlisting">
											<td align="center" valign="top"><?=$counter?></td>
											<td align="left" valign="top"><?=$invoiceDetails['invoice_number']?></td>
											<td align="left" valign="top"><?=$type?></td>
											<td align="right" valign="top">
												<input type="hidden" id="invoiceRoundOfPrice" operationMode="invoiceRoundOfPrice" name="invoiceRoundOfPrice[<?=$invoiceDetails['id']?>]" value="<?=$invoiceDetails['service_roundoff_price']?>" />
												<?=$invoiceDetails['currency']?> &nbsp;&nbsp;
												<span use="serviceRoundOffAmount"><?=$invoiceDetails['service_roundoff_price']?></span>
											</td>
											<td align="right" valign="top">
										 <?
										 if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										 {
										 ?>
										 	<script>
											$(document).ready(function(){
												$("input[type=checkbox][use=setPayDelg]").click(function(){
													$("input[type=checkbox][use=setPayDelg]").prop("checked",false);
													if($(this).attr("chkStat")=='checked')
													{
														$(this).attr("chkStat","unChecked");
													}
													else
													{
														$(this).attr("chkStat","checked");
														$(this).prop("checked",true);
													}
												});
											});
											</script>
										 	Make it Zero
										 	<input type="checkbox" use="<?=($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")?'setPayDelg':''?>" id="registration_type_complementary_add" name="registration_type_add[<?=$invoiceDetails['id']?>]" value="Z" operationMode="registration_type" style="width: 18px; height: 18px;" onclick="makeInvoiceZeroValue(this);"/>
											<br />
											Make it Complimentary
										 <?
										 }
										 ?>
											 <input type="checkbox" use="<?=($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")?'setPayDelg':''?>" id="registration_type_complementary_add" name="registration_type_add[<?=$invoiceDetails['id']?>]" value="Y" operationMode="registration_type" style="width: 18px; height: 18px;" onclick="makeInvoiceZeroValue(this);"/>
											</td>
											<td align="right" valign="top">
												<textarea type="text" name="remarks[<?=$invoiceDetails['id']?>]" id="remarks" 
												 style="width:90%; text-transform:uppercase;" use='remarks' /></textarea>
											</td>
										</tr>
									<?
									}
									?>
								<tr>
									<td align="center" valign="top"></td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"><strong>Total Amount</strong>&nbsp;<span style="font-size:12px; color:#993300">(Including GST)</span></td>
									<td align="right" valign="top">
										<?
											$totalBillAmount = invoiceAmountOfSlip($rowSlip['id']);
										?>
										<?=$rowSlip['currency']?> &nbsp;&nbsp;
										<span use="totalPayableAmount"><?=number_format($totalBillAmount,2)?></span>
										<input type="hidden" name="totalBillAmount" id="totalBillAmount" value="<?=$totalBillAmount?>"/>
									</td>
								</tr>
							</table>
																					
							<table width="100%">
								<tr>
									<td colspan="2" align="left">										 
										<!--<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Register" 
										 class="btn btn-midium btn-blue" style="float:left; margin-left:39%;"  />-->
										 <input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Update" 
										 class="btn btn-midium btn-blue" style="float:left; margin-left:39%;"  />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function complimentryRegistrationSubmitSummeryTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		$slipId	 			= $mycms->getSession('SLIP_ID');
		
		$sqlSlip['QUERY']		    = "SELECT * FROM "._DB_SLIP_." WHERE `status` = 'A' AND `id` = '".$slipId."'";
		$resSlip			= $mycms->sql_select($sqlSlip);
		$rowSlip			= $resSlip[0];
		
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		
		?>
		<script>
		function validateRegistrationSummary(obj)
		{
			var parent = $(obj).find("table").first();
			var discount = $(parent).find("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
			if(discount>0)
			{		
				if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 	
					return false;
				}	
				
				var discountAmount = $("input[type=text][operationMode=discountAmount]");		
				if(isNaN((discountAmount).val()))
				{
					alert('Enter Discount Amount correctly');
					$(discountAmount).focus();
					status = false;
					return false;
				}
				
				var total = parseFloat($("#totalBillAmount").val());	
				if(total <  parseFloat((discountAmount).val()))
				{
					alert('Enter Discount Amount correctly');
					$(discountAmount).focus();
					status = false;
					return false;
				}
			}	
			return true;
		}
		</script>
		<form name="frmApplyPayment" id="frmApplyPayment"  action="<?=$processPage?>" method="post" onsubmit="return validateRegistrationSummary(this);">
			<input type="hidden" name="act" value="setPaymentTerms" />
			<!--<input type="hidden" name="act" value="makeZeroValue" />-->
			<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$userDetails['user_full_name']?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$userDetails['user_mobile_isd_code']?> - <?=$userDetails['user_mobile_no']?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$userDetails['user_email_id']?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($userDetails['registration_tariff_cutoff_id'])?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($userDetails['registration_classification_id'])?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Slip Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Slip Number:</td>
									<td width="30%" align="left"><?=$rowSlip['slip_number']?></td>
									<td width="20%" align="left">Number Of Active Invoice</td>
									<td width="30%" align="left"><?=invoiceCountOfSlip($rowSlip['id'])?></td>
								</tr>
							</table>
							<table width="100%">
								<tr class="thighlight">
									<td colspan="5" align="left">Invoice Details</td>
								</tr>
								<tr class="theader">
									<td width="10%" align="center">Sl No</td>
									<td width="20%" align="left">Invoice Number</td>
									<td width="40%" align="left">Invoice For</td>
									<td width="30%" align="right">Invoice Amount</td>
									<td width="40%" align="right">Remarks</td>
								</tr>
									<?
									$invoiceDetailsArr = invoiceDetailsOfSlip($rowSlip['id']);
									$counter = 0;
									foreach($invoiceDetailsArr as $key => $invoiceDetails)
									{
										$counter 		 = $counter + 1;
										$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
										$type			 = "";
										if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"CONFERENCE");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"RESIDENTIAL");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"WORKSHOP");
										}
										if($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMPANY");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMMODATION");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($invoiceDetails['refference_id']);
											
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"TOUR");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"DINNER");
										}
										?>
										<input type="hidden" id="invoiceId" name="invoiceId[<?=$invoiceDetails['id']?>]" value="<?=$invoiceDetails['id']?>" />
										<tr class="tlisting">
											<td align="center" valign="top"><?=$counter?></td>
											<td align="left" valign="top"><?=$invoiceDetails['invoice_number']?></td>
											<td align="left" valign="top"><?=$type?></td>
											<td align="right" valign="top">
												<?=$invoiceDetails['currency']?> &nbsp;&nbsp;
												<?=$invoiceDetails['service_roundoff_price']?>
											</td>
											<!--<td align="right" valign="top">
											 <!--<input type="radio" name="registration_type_add" id="registration_type_complementary_add" value="ZERO_VALUE" 
														 operationMode="registration_type" />-->
											<!-- <input type="checkbox" id="registration_type_complementary_add" name="registration_type_add[<?=$invoiceDetails['id']?>]" value="Y" operationMode="registration_type" style="width: 18px; height: 18px;"/>Make Zero Value
											</td>-->
											<td align="right" valign="top">
												<?=$invoiceDetails['remarks']?>
											</td>
										</tr>
									<?
									}
									?>
								<tr>
									<td align="center" valign="top"></td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"><strong>Total Amount</strong>&nbsp;<span style="font-size:12px; color:#993300">(Including GST)</span></td>
									<td align="right" valign="top">
										<?
											$totalBillAmount = invoiceAmountOfSlip($rowSlip['id']);
										?>
										<?=$rowSlip['currency']?> &nbsp;&nbsp;
										<?=number_format($totalBillAmount,2)?>
										<input type="hidden" name="totalBillAmount" id="totalBillAmount" value="<?=$totalBillAmount?>"/>
									</td>
								</tr>
							</table>
							<?
							if(number_format(invoiceAmountOfSlip($rowSlip['id']),2)!=0)
							{
								paymentArea2();
							}
							else
							{ 
							?>
								<input type="hidden" id="payment_mode" name="payment_mode" value="Cash" />
								<input type="hidden" id="cash_deposit_date" name="cash_deposit_date" value="<?=date('Y-m-d')?>" />
							<?
							}	
							?>
							
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
										 
										<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Register" 
										 class="btn btn-midium btn-blue" style="float:left; margin-left:39%;"  />
										
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function conferenceRegistrationPaymentStatus($delegateId)
	{
		global $cfg, $mycms;
		
		if($delegateId==""){
			
			$mycms->kill("Please Select Delegate Id");
		}
		
		// FETCHING TOUR DETAILS
		$sqlInvoiceConferenceDtls        = conferenceInvoiceDetailsQuerySet($delegateId, "", "");
		$resultInvoiceConferenceDtls     = $mycms->sql_select($sqlInvoiceConferenceDtls);
		$rowInvoiceConferenceDtls        = $resultInvoiceConferenceDtls[0];  
		
		$invoicePaymentStatus            = array();
									
		if($rowInvoiceConferenceDtls['invoicePaymentStatus']!=""){
		
			$invoicePaymentStatusTemp    = explode(",", $rowInvoiceConferenceDtls['invoicePaymentStatus']);
			
			
			if(in_array("UNPAID", $invoicePaymentStatusTemp)){
			
				$invoicePaymentStatus['FONT.COLOR']              = "#C70505";
				$invoicePaymentStatus['INVOICE.STATUS']          = "UNPAID";
			}
			else if(in_array("COMPLIMENTARY", $invoicePaymentStatusTemp) 
			        && !in_array("UNPAID", $invoicePaymentStatusTemp) 
					&& !in_array("PAID", $invoicePaymentStatusTemp)){
					
				$invoicePaymentStatus['FONT.COLOR']              = "#5E8A26";
				$invoicePaymentStatus['INVOICE.STATUS']          = "COMPLIMENTARY";
			}
			else if(in_array("ZERO_VALUE", $invoicePaymentStatusTemp) 
			        && !in_array("UNPAID", $invoicePaymentStatusTemp) 
					&& !in_array("PAID", $invoicePaymentStatusTemp)){
					
				$invoicePaymentStatus['FONT.COLOR']              = "#0033FF";
				$invoicePaymentStatus['INVOICE.STATUS']          = "ZERO VALUE";
			}
			else if(in_array("PAID", $invoicePaymentStatusTemp) 
			        && !in_array("UNPAID", $invoicePaymentStatusTemp)){
					
				$invoicePaymentStatus['FONT.COLOR']              = "#5E8A26";
				
				if(in_array("COMPLIMENTARY", $invoicePaymentStatusTemp) && in_array("PAID", $invoicePaymentStatusTemp)){
				
					$invoicePaymentStatus['INVOICE.STATUS']      = "PAID/COMPLIMENTARY";
				}
			
				else{
				
					$invoicePaymentStatus['INVOICE.STATUS']      = "PAID";
				}
				
				$invoicePaymentStatus['ATOM.TRANSACTION.ID']     = $rowInvoiceConferenceDtls['atomTransactionId'];
			}
		}
		else{
		
			$invoicePaymentStatus['FONT.COLOR']                  = "#000000";
			$invoicePaymentStatus['INVOICE.STATUS']              = "-";
		}
		
		return $invoicePaymentStatus;
	}
	
	function registrationTarifTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="", $isFaculty="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * 
								    FROM "._DB_TARIFF_CUTOFF_." 
								    WHERE `status` = 'A' 
							     ORDER BY `cutoff_sequence` ASC";	
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		$userREGtype           =$_REQUEST['userREGtype'];
		$abstractDelegateId    =$_REQUEST['delegateId'];
		$userRec 		       = getUserDetails($abstractDelegateId);
		$currentCutoffId       = getTariffCutoffId();
		if($abstractDelegateId!='')
		{
		?>
		<script>
			var UPDATING_ABSTRACT_USER = true;
			$(document).ready(function(){
				$("input[type=text][forType=emailValidate]").attr('VALDATD','Y');
				$("#mobile_validation").val('AVAILABLE');
			});
		</script>
		<?
		}
		
		if($isFaculty=='Y')
		{
		?>
		<script>
		var registration_IS_faculty = true;
		function selectAllWrkShops(obj)
		{
			$("tr[operetionMode=workshopTariffTr]").find("input[type=checkbox]").prop("checked",false);
			$("input[type=checkbox][operationMode=registration_tariff]").each(function(){
				if($(this).is(":checked") && $(obj).is(":checked"))
				{
					var regClsfId = $(this).val();	
					$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").find("input[type=checkbox]").prop("checked",true);
					return false;
				}
			});
		}
		</script>
		<?
		}
		?>
		
		
		<form name="frmRegistrationStep1" id="frmRegistrationStep1" action="<?=$cfg['SECTION_BASE_URL'].$processPage?>" 
		 	  enctype="multipart/form-data" method="post" onsubmit="return backEndFromValidation();">
			<input type="hidden" name="act" value="step1" />
			<input type="hidden" name="reg_area" value="BACK" />
			<input type="hidden" name="userREGtype" value="<?=$_REQUEST['userREGtype']?>" />
			<input type="hidden" name="abstractDelegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="registration_request" id="registration_request" value="<?=$registrationRequest?>" />
			<input type="hidden" name="registration_complementary" id="registration_complementary" value="<?=$isComplementary?>" />
			<input type="hidden" name="user_password" id="user_password" 
			style="width:90%;" value="<?=rand(0,99999)?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Counter Registration</td> 
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Conference 7-9 September, 2019</td>
								</tr>
								<?
								if($isComplementary != 'Y')
								{
								?>
								<tr>
									<td width="20%" align="left" valign="top">Select Cutoff <span class="mandatory">*</span></td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<select id="registration_cutoff" name="registration_cutoff"  style="width:30%;">
											<option value="">-- Select Cutoff --</option>
											<?
											if($cutoffArray)
											{
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{
													
												?>
													<option  value="<?=$cutoffId?>" <?=$currentCutoffId==$cutoffId?'selected="selected"':'' ?>><?=$cutoffName?></option>
												<?
													
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
								<?
								}
								?>								
								<tr>
									<td width="20%" align="left" valign="top">Registration Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%" use="registration_tariff">
											<tr class="theader">
												<td width="35%">Registration Classification</td>
												<?
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{	
													if($cutoffId!=4)
													{
												?>
													<td width="100" align="right" style="width: 200px;"><?=strip_tags($cutoffName)?></td>
												<?
													}
												}
												?>
											</tr>
											<?php
											$conferenceTariffArray = array();            
											$array = array(1,2,3,4,5,6,12,13,14,15,18);
											$registrationDetails   = registrationTariffDetailsQuerySet();
											//echo '<pre>';print_r($registrationDetails);
											if($registrationDetails)
											{
												foreach($registrationDetails as $key=>$registrationDetailsVal)
												{
													if(in_array($key,$array))
													{
													?>
													<tr class="tlisting" <?=$styleCss?>>
														<td align="left">
															<input type="checkbox" name="registration_classification_id[]" id="registration_classification_id" 
																 operationMode="registration_tariff" value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" />
														
															&nbsp;&nbsp;&nbsp;
															<?=getRegClsfName($key)?>
															<?
															if($key==11)
															{
															?>
															<span style="color:#FF0000; margin-left: 37px;"><br>(Free Workshop)</span>
															<?
															}
															?>
														</td>
														<?
														foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
														{
															if($rowCutoff['CUTOFF_ID']!=4)
															{
														?>
															<td align="right"><?=$rowCutoff['CURRENCY']?> <?=$rowCutoff['DISPLAY_AMOUNT']?></td>
														<?php
															}
														}
														?>
													</tr>
													<?		
												}
											}	
											}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
							</table>							
							<?
							if($isComplementary != 'Y')
							{
							?>
								<table width="100%" id="offconference" >
									<tr>
										<td width="20%" align="left" valign="top">Workshop Tariff</td>
										<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
											<table width="100%">
												<tr class="theader">
													<td align="center" width="53%" colspan="2">
													<?
													if($isFaculty=='Y')
													{
													?>
														<input type="checkbox" name="selectAll" id="selectAll" onclick="selectAllWrkShops(this);" style="float:left;"/>
													<?
													}
													?>
													Workshop Classification
													</td>
													<?
													$sql['QUERY'] = "SELECT cutoff.cutoff_title  
																	   FROM "._DB_TARIFF_CUTOFF_." cutoff
																	   WHERE status = 'A' 
																	 	 AND id != 4";
													$res = $mycms->sql_select($sql);
													foreach($res as $key=>$title)
													{	
													?>
														<td align="right"><?=strip_tags($title['cutoff_title'])?></td>
													<?
													}
													?>
												</tr>												
												<?php
												
												
												 $workshopDetails = workshopTariffDetailsQuerySet();
												 $workshopCountArr = totalWorkshopCount();	
												 if(sizeof($workshopDetails)>0)
												 {
													 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
													 {
														foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
														{
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
											
															if($workshopCount<1)
															{
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															foreach($rowRegClasf as $keyCutoff=>$sessionType)
															{
																$sessiontype = $sessionType['WORKSHOP_TYPE'];
																$workshopName = $sessionType['WORKSHOP_GRP'];
															}
															?>
															 <tr class="tlisting" use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
																<td align="left">
																	<div class="tooltip">
																		<?=$span?>
																		<input type="checkbox"  <?=$style?> name="workshop_id[]" id="workshop_id" operationMode="<?=$sessiontype?>" workshopName="<?=$workshopName?>" opmode ="workshopId" value="<?=$keyWorkshopclsf?>" />
																	</div>
																</td>
																<td align="left">&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?></td>
																<?
																foreach($rowRegClasf as $keyCutoff=>$cutoffvalue)
																{
																?>
																<td align="right">
																<?	
																	if($cutoffvalue['REG_ID']==11 && $cutoffvalue['CUTOFF_ID']!=4)
																	{
																	?>
																	Free Registration
																	<?
																	}
																	elseif($cutoffvalue['CUTOFF_ID']!=4)
																	{
																	?>
																	<?=$cutoffvalue['CURRENCY']?> &nbsp;<?=$cutoffvalue[$cutoffvalue['CURRENCY']]?>
																	<?
																	}
																	?>
																</td>
																<?  
																}
																?>
															 </tr>
															<? 
														}
													 }
												  }
												?>	
												<tr use="na" operetionMode="workshopTariffTr">
													<td align="center" colspan="<?=$keyCutoff +1?>"><strong style="color:#FF0000;">Please Select Registration Classification First</strong></td>
												</tr>
												<tr operetionMode="workshopTr"  align="center" >
													<td colspan="<?=$keyCutoff +1?>" align="center" >
														<strong style="color:#FF0000;">All workshop are included in Residential Package </strong>
													</td>	
												</tr>	
														
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4" align="left">&nbsp;</td>
									</tr>
								</table>								
								<table width="100%" id="complementaryWorkshop" >
									<tr>
										<td align="center">
											<strong style="color:#FF0000;">Workshop is not available this type of user</strong>
										</td>	
									</tr>
								</table>								
							<?
							}
							?>
							<!-- Login Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Login Details</td>
								</tr>
								<tr>
									<td width="20%" align="left" valign="top">
										Email Id
										<span class="mandatory">*</span>
									</td>
									<td width="30%" align="left" valign="top">
										<input type="text" name="user_email_id" id="user_email_id" forType="emailValidate" use="add"
										 style="width:90%;" value="<?=($userRec['user_email_id']!='')?($userRec['user_email_id']):''?>" />
										<input type="hidden" name="email_id_validation" id="email_id_validation" />
										
										<div id="email_div"></div>
									</td>
									<td align="right" valign="top" ></td>
									<td align="right" valign="top"></td>
								</tr>
							</table>
							
							<!-- User Details -->
							<table width="100%">
							
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Title <span class="mandatory">*</span></td>
									<td width="30%" align="left">
										<select name="user_initial_title" id="user_initial_title" style="width:95%;">
											<option value="Dr" selected="selected">Dr</option>
											<option value="Mr">Prof</option>
											<option value="Mr">Mr</option>
											<option value="Ms">Ms</option>
											<option value="Mrs">Mrs</option>
										</select>
									</td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>	
								<tr>
									<td align="left" width="20%">First Name <span class="mandatory">*</span></td>
									<td align="left" width="30%">
										<input type="text" name="user_first_name" id="user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=($userRec['user_first_name']!='')?($userRec['user_first_name']):''?>"/>
									</td>
									<td width="20%" align="left">Address</td>
									<td width="30%" align="left" rowspan="3">
										<textarea name="user_address" id="user_address" 
										 style="height:75px; width:90%; text-transform:uppercase;"><?=($userRec['user_address']!='')?($userRec['user_address']):''?></textarea>
									</td>
								</tr>
								<tr>
									<td align="left">Middle Name</td>
									<td align="left">
										<input type="text" name="user_middle_name" id="user_middle_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=($userRec['user_middle_name']!='')?($userRec['user_middle_name']):''?>" />
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Last Name <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_last_name" id="user_last_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=($userRec['user_last_name']!='')?($userRec['user_last_name']):''?>" />
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Gender <span class="mandatory">*</span></td>
									<td align="left">
										<input type="radio" name="user_gender" id="user_gender_male" 
										 checked="checked" value="MALE" /> Male
										 
										<input type="radio" name="user_gender" id="user_gender_female" 
										 value="FEMALE"/> Female
									</td>
									<td align="left">Country <span class="mandatory">*</span></td>
									<td align="left">
										<select name="user_country" id="user_country" style="width:90%;" forType="country"
										 sequence="1">
											<option value="">-- Select Country --</option>
											<?php
											$sqlCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																	           WHERE `status` = 'A' 
											                                ORDER BY `country_name`";
											$resultCountry = $mycms->sql_select($sqlCountry);
											if($resultCountry)
											{
												foreach($resultCountry as $i=>$rowFetchUserCountry)
												{
											?>
													<option value="<?=$rowFetchUserCountry['country_id']?>" <?=($rowFetchUserCountry['country_id']==$userRec['country_id'])?'selected':''?>><?=$rowFetchUserCountry['country_name']?></option>
											<?php
												}
											}
											?>
										</select>
										<?
										if($userRec['country_id']!='')
										{
										?>
										<script>
										$(document).ready(function(){
											jBaseUrl = jsBASE_URL;
											generateSateList(<?=$userRec['country_id']?>,jBaseUrl);
											$('#user_state option[value="<?=$userRec['st_id']?>"]').prop('selected', true);
										});
										</script>
										<?php
										}
										?>
									</td>
								</tr>
								<tr>
									<td align="left">Mobile <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_mobile_isd_code" id="user_mobile_isd_code" 
										 style="width:10%; text-transform:uppercase;" value="<?=($userRec['user_mobile_isd_code']!='')?($userRec['user_mobile_isd_code']):'+91'?>" maxlength="4" />
										 
										<input type="text" name="user_mobile_no" id="user_mobile_no" forType="mobileValidate"
										 style="width:75%; text-transform:uppercase;" value="<?=($userRec['user_mobile_no']!='')?($userRec['user_mobile_no']):''?>"/>
										 
										 <input type="hidden" name="mobile_validation" id="mobile_validation" />
										 <div id="mobile_div"></div>
									</td>
									<td align="left">Select State<span class="mandatory">*</span></td>
									<td align="left">
										<select name="user_state" id="user_state" style="width:90%;" forType="state"
										 sequence="1" disabled="disabled">
											<option value="">-- Select Country First --</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left">Postal Code <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_postal_code" id="user_postal_code" 
										 style="width:90%; text-transform:uppercase;" value="<?=($userRec['user_pincode']!='')?($userRec['user_pincode']):''?>"/>
									</td>
									<td align="left">Enter City <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_city" id="user_city" 
										 style="width:90%; text-transform:uppercase;" value="<?=($userRec['user_city_id']!='')?($userRec['user_city_id']):''?>"/>
									</td>
									
								</tr>
								<tr>
									<td align="left" valign="top">Food Preference </td>
									<td align="left" valign="top">									
										<input type="checkbox" name="user_food_preference" id="user_food_preference_veg" groupName="user_food_choice"
										 value="VEG" <?=($userRec['user_food_preference']=='VEG')?'checked':''?>/> Veg
										
										<input type="checkbox" name="user_food_preference" id="user_food_preference_non_veg" groupName="user_food_choice"
										 value="NON_VEG" <?=($userRec['user_food_preference']=='NON_VEG')?'checked':''?> /> Non Veg 									
									</td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"></td>									
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="20.5%"></td>
						<td align="right">
							<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Submit":"Proceed"?>" 
							 class="btn btn-large btn-blue" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="tfooter">&nbsp;</td>
					</tr>
				</tbody> 
			</table>
		</form>
	<?php
	}
	
	function counterRegistrationTarifTemplate($requestPage, $processPage, $registrationRequest="COUNTER", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` = 'A' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		$currentCutoffId = getTariffCutoffId();
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		?>
		<form name="frmRegistrationStep1" id="frmRegistrationStep1" action="<?=$cfg['SECTION_BASE_URL'].$processPage?>" 
		 enctype="multipart/form-data" method="post" onsubmit="return backEndFromValidation();">
			<input type="hidden" name="act" value="counter" />
			<input type="hidden" name="reg_area" value="BACK" />
			<input type="hidden" name="registration_request" id="registration_request" value="<?=$registrationRequest?>" />
			<input type="hidden" name="registration_complementary" id="registration_complementary" value="<?=$isComplementary?>" />
			<input type="hidden" name="user_password" id="user_password" style="width:90%;" value="<?=rand(0,99999)?>" />
			<input type="hidden" name="registration_cutoff" id="registration_cutoff" value="1" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Counter Registration</td> 
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Conference <?=$cfg['EMAIL_CONF_HELD_FROM']?></td>
								</tr>
								<?
								if($isComplementary != 'Y')
								{
								?>
								<tr>
									<td width="20%" align="left" valign="top">Select Date <span class="mandatory">*</span></td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
									
										<select id="date" name="date"  style="width:30%;">
											<!--<option value="">-- Select Date --</option>-->
											<option  value="2017-05-04">2017-05-04</option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
								<?
								}
								?>
								
								<tr>
									<td width="20%" align="left" valign="top">Registration Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%" use="registration_tariff">
											<tr class="theader">
												<td width="35%">Registration Classification</td>												
												<td width="100" align="right" style="width: 200px;">Counter Registration</td>
											</tr>
											<?php
											$conferenceTariffArray = array();            
											$array = array(1,2,9,10,11,12,13,14,15);
											$registrationDetails   = registrationTariffDetailsQuerySet();
											
											if($registrationDetails)
											{
												foreach($registrationDetails as $key=>$registrationDetailsVal)
												{
													if(in_array($key,$array))
													{
												
													$styleCss = 'style="display:none;"';
													if($key != 3 && $key != 6 && $key != 5 && $key != 4)
													{
														$styleCss = '';
													}
													?>
													<tr class="tlisting" <?=$styleCss?>>
														<td align="left">
															<input type="checkbox" name="registration_classification_id[]" id="registration_classification_id" 
																 operationMode="registration_tariff" value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" />
														
															&nbsp;&nbsp;&nbsp;
															<?=getRegClsfName($key)?>
														</td>
														<?
														$rowCutoff = $registrationDetailsVal[1];
														
														?>
															<td align="right"><?=$rowCutoff['CURRENCY']?> <?=$rowCutoff['DISPLAY_AMOUNT']?></td>
														<?php
														
														?>
													</tr>
													<?		
												}
											}	
											}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
							</table>
							<?
							if($isComplementary != 'Y')
							{
							?>
								<table width="100%" id="offconference" >
									<tr>
										<td width="20%" align="left" valign="top">Workshop Tariff</td>
										<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
											<table width="100%">
												<tr class="theader">
													<td align="center" width="53%" colspan="2">Workshop Classification</td>
													<?
													$sql['QUERY'] = "SELECT cutoff.cutoff_title  
															  FROM "._DB_TARIFF_CUTOFF_." cutoff
															 WHERE status = 'A' 
															 		AND id != 4";
													$res = $mycms->sql_select($sql);
													$title = $res[0];
													?>
														<td align="right">Counter Registration</td>
												</tr>
												
												<?php
												 $workshopDetails = workshopTariffDetailsQuerySet();
												 $workshopCountArr = totalWorkshopCount();	
												 												
												 if(sizeof($workshopDetails)>0)
												 {
													 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
													 {
													 ?>
													 <?
														foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
														{
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
											
															if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															foreach($rowRegClasf as $keyCutoff=>$sessionType)
															{
																$sessiontype = $sessionType['WORKSHOP_TYPE'];
																$workshopName = $sessionType['WORKSHOP_GRP'];
															}
															?>
															
															 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
																<td align="left">
																	<div class="tooltip">
																		<?=$span?>
																		<input type="checkbox"  <?=$style?> name="workshop_id[]" id="workshop_id" operationMode="<?=$sessiontype?>" workshopName="<?=$workshopName?>" opmode ="workshopId" value="<?=$keyWorkshopclsf?>" />
																	</div>
																	
																</td>
																<td align="left">&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?></td>
																<?
																foreach($rowRegClasf as $keyCutoff=>$cutoffvalue)
																{
																	if($cutoffvalue['CUTOFF_ID']==1)
																	{
																?>
																<td align="right"><?=$cutoffvalue['CURRENCY']?> &nbsp;<?=$cutoffvalue[$cutoffvalue['CURRENCY']]?></td>
																<?
																     }	
																}
																?>
															 </tr>
															<? 
														}
													 }
												  }
												  
												 ?>	
												<tr use="na" operetionMode="workshopTariffTr">
													<td align="center" colspan="<?=$keyCutoff +1?>"><strong style="color:#FF0000;">Please Select Registration Classification First</strong></td>
												</tr>
												<tr operetionMode="workshopTr"  align="center" >
												<td colspan="<?=$keyCutoff +1?>" align="center" >
													<strong style="color:#FF0000;">All workshop are included in Residential Package </strong>
												</td>	
												</tr>	
														
											</table>
										</td>
										
										
									</tr>
									<tr>
										<td colspan="4" align="left">&nbsp;</td>
									</tr>
								</table>
								
								<table width="100%" id="complementaryWorkshop" >
									<tr>
										<td align="center">
											<strong style="color:#FF0000;">Workshop is not available this type of user</strong>
										</td>	
									</tr>
								</table>
								
							<?
							}
							?>
							
							
							
							<table width="100%" >
									<tr>
										<td width="20%" align="left" valign="top">Dinner</td>
										<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
											<table width="100%">
												<tr>
													<td align="left"  width="5%" valign="top"> <input  name="dinner_value[]" id="dinner_value"  value="" type="checkbox" style="width: 18px;height: 18px;"/></td><td width="53%" align="left" valign="top"> BANQUET DINNER</td>
													<td align="right"> INR 3000</td>
												</tr>
												
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4" align="left">&nbsp;</td>
									</tr>
								</table>
								
								
								
								
								
								
								
								
								
							<!-- Login Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Login Details</td>
								</tr>
								
								<tr>
									<td width="20%" align="left" valign="top">
										Email Id 
										<span class="mandatory">*</span>
									</td>
									<td width="30%" align="left" valign="top">
										<input type="text" name="user_email_id" id="user_email_id" forType="emailValidate" use="add"
										 style="width:90%;" />
										<input type="hidden" name="email_id_validation" id="email_id_validation" />
										
										<div id="email_div"></div>
									</td>
									<td align="right" valign="top" ></td>
									<td align="right" valign="top"></td>
								</tr>								
							</table>
							
							<!-- User Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Title <span class="mandatory">*</span></td>
									<td width="30%" align="left">
										<select name="user_initial_title" id="user_initial_title" style="width:95%;">
											<option value="Dr" selected="selected">Dr.</option>
											<option value="Mr">Mr.</option>
											<option value="Ms">Ms.</option>
											<option value="Mrs">Mrs.</option>
										</select>
									</td>
									<td width="20%" align="left">Address</td>
									<td width="30%" align="left" rowspan="3">
										<textarea name="user_address" id="user_address" 
										 style="height:75px; width:90%; text-transform:uppercase;"></textarea>
									</td>
										
									</td>
								</tr>
								<tr>
									<td align="left" width="20%">First Name <span class="mandatory">*</span></td>
									<td align="left" width="30%">
										<input type="text" name="user_first_name" id="user_first_name" 
										 style="width:90%; text-transform:uppercase;" />
										 
									</td>
									
								</tr>
								<tr>
									<td align="left">Middle Name</td>
									<td align="left">
										<input type="text" name="user_middle_name" id="user_middle_name" 
										 style="width:90%; text-transform:uppercase;" />
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Last Name <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_last_name" id="user_last_name" 
										 style="width:90%; text-transform:uppercase;" />
									</td>
									<td align="left">Country <span class="mandatory">*</span></td>
									<td align="left">
										<select name="user_country" id="user_country" style="width:90%;" forType="country"
										 sequence="1">
											<option value="">-- Select Country --</option>
											<?php
											$sqlCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																	  WHERE `status` = 'A' 
											                       ORDER BY `country_name`";
											$resultCountry = $mycms->sql_select($sqlCountry);
											if($resultCountry)
											{
												foreach($resultCountry as $i=>$rowFetchUserCountry)
												{
											?>
													<option value="<?=$rowFetchUserCountry['country_id']?>"><?=$rowFetchUserCountry['country_name']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left">Gender <span class="mandatory">*</span></td>
									<td align="left">
										<input type="radio" name="user_gender" id="user_gender_male" 
										 checked="checked" value="MALE" /> Male
										 
										<input type="radio" name="user_gender" id="user_gender_female" 
										 value="FEMALE" /> Female
									</td>
									<td align="left">Select State<span class="mandatory">*</span></td>
									<td align="left">
										<select name="user_state" id="user_state" style="width:90%;" forType="state"
										 sequence="1" disabled="disabled">
											<option value="">-- Select Country First --</option>
										</select>
									</td>
								</tr>
								<!--<tr>
									<td align="left">DOB <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_dob_add" id="user_dob_add" rel="tcal"
										 style="width:90%;" placeholder="YYYY-MM-DD" />
									</td>
									
									
								</tr>-->
								<tr>
									<td align="left">Mobile <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_mobile_isd_code" id="user_mobile_isd_code" 
										 style="width:10%; text-transform:uppercase;" value="+91" maxlength="4" />
										 
										<input type="text" name="user_mobile_no" id="user_mobile_no" forType="mobileValidate"
										 style="width:75%; text-transform:uppercase;" />
										 
										 <input type="hidden" name="mobile_validation" id="mobile_validation" />
										 <div id="mobile_div"></div>
									</td>
									<td align="left">Enter City <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_city" id="user_city" 
										 style="width:90%; text-transform:uppercase;" />
									</td>
								</tr>
								<tr>
									<td align="left">Postal Code <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_postal_code" id="user_postal_code" 
										 style="width:90%; text-transform:uppercase;"/>
									</td>
								<!--	<td align="left">Food Preference  <span class="mandatory">*</span></td>
									<td align="left">
										<input type="checkbox" groupName="user_food_choice" name="user_food_choice[]" id="user_food_veg" value="VEG" /> Veg 
										&nbsp;
										<input type="checkbox" groupName="user_food_choice" name="user_food_choice[]" id="user_food_nonveg" value="NON_VEG"  /> Non-veg
									</td>
									
								</tr>
								
									<tr id="idstudent_idcard" style="display:none;">
									<td align="left">File Upload  <!--<span class="mandatory">*</span>--></td>
									<td align="left">
										<input type="file" style="width:90%;"  name="student_idcard" id="student_idcard" autocomplete="off" tabindex="19">
									</td>
								</tr>							
							</table>							
						</td>
					</tr>
					<tr>
						<td width="20.5%"></td>
						<td align="right">
							<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Submit":"Proceed"?>" 
							 class="btn btn-large btn-blue" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="tfooter">&nbsp;</td>
					</tr>
				</tbody> 
			</table>
		</form>
	<?php
	}	
	
	function ComplementaryRegistrationTarifTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		?>
		<form name="frmRegistrationStep1" id="frmRegistrationStep1" action="<?=$cfg['SECTION_BASE_URL'].$processPage?>" 
		 enctype="multipart/form-data" method="post" onsubmit="return backEndFromValidation();">
			<input type="hidden" name="act" value="step1" />
			<input type="hidden" name="reg_area" value="BACK" />
			<input type="hidden" name="registration_request" id="registration_request" value="<?=$registrationRequest?>" />
			<input type="hidden" name="registration_complementary" id="registration_complementary" value="<?=$isComplementary?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Offline Registration</td> 
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
							<?
								if($isComplementary != 'Y')
								{
								?>

								<tr class="thighlight">
									<td colspan="4" align="left">Conference May 19 - 21, 2017</td>
								</tr>
								<tr>
									<td width="20%" align="left" valign="top">Select Cutoff <span class="mandatory">*</span></td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<select id="registration_cutoff" name="registration_cutoff" style="width:30%;">
											<option value="">-- Select Cutoff --</option>
											<?
											if($cutoffArray)
											{
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{
												?>
													<option value="<?=$cutoffId?>"><?=$cutoffName?></option>
												<?
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
								<?
								}
								?>
								<tr>
									<td width="20%" align="left" valign="top">Registration Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%" use="registration_tariff">
											<tr class="theader">
												<td width="30%">Registration Classification</td>
												<?
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{	
												?>
													<td width="100" align="right" style="width: 200px;"><?=strip_tags($cutoffName)?></td>
												<?
												}
												?>
											</tr>
											<?php
											$conferenceTariffArray = array();            
											
											$registrationDetails   = registrationTariffDetailsQuerySet();
											
											if($registrationDetails)
											{
												foreach($registrationDetails as $key=>$registrationDetailsVal)
												{
													$styleCss = 'style="display:none;"';
													if($key != 3 && $key != 6 && $key != 5 && $key != 4)
													{
														$styleCss = '';
													}
													if($key == 16 || $key == 17)
													{
														$styleCss = 'style="display:none;"';
													}
													?>
													<tr class="tlisting" <?=$styleCss?>>
														<td align="left">
															<input type="checkbox" name="registration_classification_id[]" id="registration_classification_id" 
																 operationMode="registration_tariff" value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" />
														
															&nbsp;&nbsp;&nbsp;
															<?=getRegClsfName($key)?>
														</td>
														<?
														foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
														{
														?>
															<td align="right"><?=$rowCutoff['CURRENCY']?> <?=$rowCutoff['AMOUNT']?></td>
														<?php
														}
														?>
													</tr>
													<?		
												}
											}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
							</table>
							<?
								if($isComplementary != 'Y')
								{
								?>
								<table width="100%">
									<tr>
										<td width="20%" align="left" valign="top">Workshop Tariff</td>
										<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
											<table width="100%">
												<tr class="theader">
													<td align="center" width="30%">Workshop</td>
													<?
													$sql['QUERY'] = "SELECT cutoff.cutoff_title  
															  FROM "._DB_TARIFF_CUTOFF_." cutoff
															 WHERE status = 'A'";
													$res = $mycms->sql_select($sql);
													foreach($res as $key=>$title)
													{	
													?>
														<td align="right"><?=strip_tags($title['cutoff_title'])?></td>
													<?
													}
													?>
												</tr>
												
												<?php
												 $workshopDetails = workshopTariffDetailsQuerySet();
												 $workshopCountArr = totalWorkshopCount();	
												 if(sizeof($workshopDetails)>0)
												 {
													 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
													 {	
														foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
														{
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
											
															if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															?>
															 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
																<td align="left" width="34%">
																	<div class="tooltip">
																		<?=$span?>
																		<input type="checkbox"  <?=$style?> name="workshop_id[]" id="workshop_id" value="<?=$keyWorkshopclsf?>" />
																	</div>
																	&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?>
																</td>
																<?
																foreach($rowRegClasf as $keyCutoff=>$cutoffvalue)
																{
																?>
																<td align="right"><?=$cutoffvalue['CURRENCY']?> &nbsp;<?=$cutoffvalue[$cutoffvalue['CURRENCY']]?></td>
																<?
																}
																?>
															 </tr>
															<?
														}
													 }
												  }
												  
												 ?>	
												<tr use="na" operetionMode="workshopTariffTr">
													<td align="center" colspan="<?=$keyCutoff +1?>"><strong style="color:#FF0000;">Please Select Registration Classification First</strong></td>
												</tr>
												<tr operetionMode="workshopTr"  align="center" >
												<td colspan="<?=$keyCutoff +1?>" align="center" >
													<strong style="color:#FF0000;">All workshops are included in Residential Package </strong>
												</td>	
												</tr>			
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4" align="left">&nbsp;</td>
									</tr>
								</table>
								<?
								}
							if(false)
							{
							?>
							<table width="100%">
									<tr>
										<td width="20%" align="left" valign="top">Residential Tariff</td>
										<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
											<table width="100%" use="residential_tariff">
												<tr class="theader">
												<td width="30%">Residential Package Registration </td>
												<?
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{	
												?>
													<td width="100" align="right" style="width: 200px;"><?=strip_tags($cutoffName)?></td>
												<?
												}
												?>
											</tr>
											<?php
											$conferenceTariffArray = array();            
											
											$registrationDetails   = registrationTariffDetailsQuerySet();
											
											
											if($registrationDetails)
											{
												foreach($registrationDetails as $key=>$registrationDetailsVal)
												{
													$styleCss = 'style="display:none;"';
													if($key == 5 || $key == 4)
													{
														$styleCss = '';
													}
													?>
													<tr class="tlisting" <?=$styleCss?>>
														<td align="left">
															<input type="checkbox" name="registration_classification_id[]" id="registration_classification_id" 
																 operationMode="residential_tariff" value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" />
														
															&nbsp;&nbsp;&nbsp;
															<?=getRegClsfName($key)?>
														</td>
														<?
														foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
														{
														?>
															<td align="right"><?=$rowCutoff['CURRENCY']?> <?=$rowCutoff['AMOUNT']?></td>
														<?php
														}
														?>
													</tr>
													<?		
												}
											}
											?>
															
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="4" align="left">&nbsp;</td>
									</tr>
								</table>
							<?
							}
							?>
							<!-- Login Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Login Details</td>
								</tr>
								<tr>
									<td width="20%" align="left" valign="top">
										Email Id 
										<span class="mandatory">*</span>
									</td>
									<td width="30%" align="left" valign="top">
										<input type="text" name="user_email_id" id="user_email_id" forType="emailValidate" use="add"
										 style="width:90%;" />
										<input type="hidden" name="email_id_validation" id="email_id_validation" />
										
										<div id="email_div"></div>
									</td>
									<td>
									</td>
									<?php /*?><td width="20%" align="left" valign="top">Password</td>
									<td width="30%" align="left" valign="top">
										<input type="text" name="user_password" id="user_password" 
										 style="width:90%;" value="<?=rand(0,99999)?>" />
										
										<div id="password_div"></div>
									</td><?php */?>
								</tr>
							</table>
							
							<!-- User Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Title <span class="mandatory">*</span></td>
									<td width="30%" align="left">
										<select name="user_initial_title" id="user_initial_title" style="width:95%;">
											<option value="Dr" selected="selected">Dr.</option>
											<option value="Mr">Prof.</option>
											<option value="Mr">Mr.</option>
											<option value="Ms">Ms.</option>
											<option value="Mrs">Mrs.</option>
										</select>
									</td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left" ></td>
								</tr>
								<tr>
									<td align="left" width="20%">First Name <span class="mandatory">*</span></td>
									<td align="left" width="30%">
										<input type="text" name="user_first_name" id="user_first_name" 
										 style="width:90%; text-transform:uppercase;" />
										 
									</td>
									<td width="20%" align="left">Address</td>
									<td width="30%" align="left" rowspan="3">
										<textarea name="user_address" id="user_address" 
										 style="height:75px; width:90%; text-transform:uppercase;"></textarea>
									</td>
								</tr>
								<tr>
									<td align="left">Middle Name</td>
									<td align="left">
										<input type="text" name="user_middle_name" id="user_middle_name" 
										 style="width:90%; text-transform:uppercase;" />
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Last Name <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_last_name" id="user_last_name" 
										 style="width:90%; text-transform:uppercase;" />
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Gender <span class="mandatory"></span></td>
									<td align="left">
										<input type="radio" name="user_gender" id="user_gender_male" 
										 checked="checked" value="MALE" /> Male
										 
										<input type="radio" name="user_gender" id="user_gender_female" 
										 value="FEMALE" /> Female
									</td>
									<td align="left">Country <span class="mandatory"></span></td>
									<td align="left">
										<select name="user_country" id="user_country" style="width:90%;" forType="country"  onchange="stateRetriver();"
										 sequence="1">
											<option value="">-- Select Country --</option>
											<?php
											$sqlCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																	  WHERE `status` = 'A' 
											                       ORDER BY `country_name`";
											$resultCountry = $mycms->sql_select($sqlCountry);
											if($resultCountry)
											{
												foreach($resultCountry as $i=>$rowFetchUserCountry)
												{
											?>
													<option value="<?=$rowFetchUserCountry['country_id']?>"><?=$rowFetchUserCountry['country_name']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								</tr>
								<!--<tr>
									<td align="left">DOB <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_dob_add" id="user_dob_add" rel="tcal"
										 style="width:90%;" placeholder="YYYY-MM-DD" />
									</td>
									
									
								</tr>-->
								<tr>
									<td align="left">Mobile <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_mobile_isd_code" id="user_mobile_isd_code" 
										 style="width:10%; text-transform:uppercase;" value="+91" maxlength="4" />
										 
										<input type="text" name="user_mobile_no" id="user_mobile_no" forType="mobileValidate"
										 style="width:75%; text-transform:uppercase;" />
										 
										 <input type="hidden" name="mobile_validation" id="mobile_validation" />
										 <div id="mobile_div"></div>
									</td>
									<td align="left">Select State <span class="mandatory"></span></td>
									<td align="left">
										<select name="user_state" id="user_state" style="width:90%;" forType="state"
										 sequence="1" disabled="disabled">
											<option value="">-- Select Country First --</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left">Postal Code <span class="mandatory"></span></td>
									<td align="left">
										<input type="text" name="user_postal_code" id="user_postal_code" 
										 style="width:90%; text-transform:uppercase;"/>
									</td>
									<td align="left">Enter City <span class="mandatory"></span></td>
									<td align="left">
										<input type="text" name="user_city" id="user_city" 
										 style="width:90%; text-transform:uppercase;" />
									</td>
									
								</tr>
								<tr>
								<td align="left" valign="top">Food Preference</td>
									<td align="left" valign="top">									
										<input type="checkbox" name="user_food_preference" id="user_food_preference_veg" groupName="user_food_choice"
										 value="VEG" /> Veg
										
										<input type="checkbox" name="user_food_preference" id="user_food_preference_non_veg" groupName="user_food_choice"
										 value="NON_VEG" /> Non Veg 									
									</td>
								</tr>								
							</table>
						</td>
					</tr>
					<tr>
						<td width="20.5%"></td>
						<td align="right">
							<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="Submit" 
							 class="btn btn-large btn-blue" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="tfooter">&nbsp;</td>
					</tr>
				</tbody> 
			</table>
		</form>
	<?php
	}
	
	function addWorkshopFormTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$delegateId 	=  $_REQUEST['id'];
		$spotUser		= $_REQUEST['userREGtype'];	
		$rowFetchUser   = getUserDetails($delegateId);
		$delagateCatagory = getUserClassificationId($delegateId);
		?>
		
		<form name="frmApplyForworkshop" id="frmApplyForworkshop"  action="<?=$processPage?>" method="post" onsubmit="return validateWorkshopFrom(this);">
			<input type="hidden" name="act" value="applyWorkshop" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
			<input type="hidden" id="type" value="Workshop" />
			<input type="hidden" id="prevValue" value="0" />
			<input type="hidden" name="delegate_id" value="<?=$delegateId?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Workshop</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%" shortData="on" >
								<tr class="thighlight">
								<td colspan="6" align="left">User Details</td>
								</tr>
								<tr >
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left">
										<?=$rowFetchUser['user_title']?> 
										<?=$rowFetchUser['user_first_name']?> 
										<?=$rowFetchUser['user_middle_name']?> 
										<?=$rowFetchUser['user_last_name']?>
									</td>
									<td width="20%" align="left">Email Id:</td>
									<td width="30%" align="left"><?=$rowFetchUser['user_email_id']?></td>
								</tr>								
								<tr>
									<td align="left">Registration Id:</td>
									<td align="left">
									<?php
									if($rowFetchUser['registration_payment_status']=="PAID" 
									   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE"
									   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
									   || $rowFetchUser['sub_registration_type']=="GOVERNMENT_EMPLOYEE")
									{
										echo $rowFetchUser['user_registration_id'];
									}
									else
									{
										echo "-";
									}
									?>
									</td>
									<td align="left">Unique Sequence:</td>
									<td align="left"><?=$rowFetchUser['user_unique_sequence']?></td>
								</tr>
								<tr>
									<td align="left" valign="top">Registration Classification:</td>
									<td align="left" valign="top"><?=getRegClsfName($delagateCatagory)?></td>
									<td align="left" valign="top">Mobile No:</td>
									<td align="left" valign="top"><?=$rowFetchUser['user_mobile_isd_code']?> - <?=$rowFetchUser['user_mobile_no']?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="3" align="left">Workshop Registration Details</td>
								</tr>
								<?
								$resultWorkshopDtls 	= getWorkshopDetailsOfDelegate($delegateId);
								$workshopIdArr			= array();
								//echo "<pre>"; print_r($resultAccommodationDtls); echo "<pre>";
								if($resultWorkshopDtls)
								{
								?>
								<tr class="theader">
									<td align="left">Workshop Name</td>
									<td align="left" width="400" >Booking Cut-off</td>
									<td align="center" width="150">Payment Status</td>
								</tr>	
								<?
									$cutoff = "";
									foreach($resultWorkshopDtls as $keyWorkshopDtls=>$rowWorkshopDtls)
									{
										$workshopIdArr[] = $rowWorkshopDtls['workshop_id'];
									?>
									<tr>
										<td align="left"><?=getWorkshopName($rowWorkshopDtls['workshop_id'])?></td>
										<td align="left"><?=getCutoffName($rowWorkshopDtls['tariff_cutoff_id'])?></td>
										<td align="center">
											<?
											if($rowWorkshopDtls['payment_status'] == 'UNPAID')
											{
												echo '<span class="unpaidStatus">UNPAID</span>';
											}
											else if($rowWorkshopDtls['payment_status'] == 'PAID')
											{
												echo '<span class="paidStatus">PAID</span>';
											}
											else if($rowWorkshopDtls['payment_status'] == 'ZERO_VALUE')
											{
												echo '<span class="paidStatus">ZERO VALUE</span>';
											}
											?>
										</td>
									</tr>
									<?
									}
								}
								else
								{
								?>
								<tr>
									<td colspan="3" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>
								<?
								}
							?>
							</table>
							<? //echo "<pre>"; print_r($workshopIdArr); echo "<pre>"; ?>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Registration Cutoff</td>
								</tr>
								<tr>
									<td align="left" style="width:20%;">
										Cutoff Title: <span class="mandatory">*</span>
									</td>
									<td align="left" style="width:30%;">
										<select style="width:90%;" name="cutoff_id_add" id="cutoff_id_add" operationMode="cutoff_id" onchange="calculationWorkshopAmount();">
											<option value="">-- Choose Cutoff --</option>
											<?php
												$sqlFetchCutoff['QUERY'] = "SELECT * FROM "._DB_TARIFF_CUTOFF_."
																	WHERE `status` = 'A' ORDER BY id";
												
												$resultCutoff	= $mycms->sql_select($sqlFetchCutoff);
												if($resultCutoff){
													if($spotUser == 'SPOT')
													{
												?>
													 <option value="4" selected="selected">Spot</option>
												<?
													}
													else
													{
														foreach($resultCutoff as $keyCutoff=>$rowCutoff){
														?>
															<option value="<?=$rowCutoff['id']?>"><?=$rowCutoff['cutoff_title']?></option>
														<?php
														}
													}
												}
											?>
										</select>
									</td>
									<td width="50%"></td>
								</tr>
							</table>
							
							
							<table width="100%" use="Tariff">
								<tr class="theader">
									<td align="center" width="30%">Workshop</td>
									<?
									$sql['QUERY'] = "SELECT cutoff.cutoff_title  
											  FROM "._DB_TARIFF_CUTOFF_." cutoff
											 WHERE status = 'A'";
									$res = $mycms->sql_select($sql);
									foreach($res as $key=>$title)
									{	
									?>
										<td align="right"><?=strip_tags($title['cutoff_title'])?></td>
									<?
									}
									?>
								</tr>
								
								<?php
								$workshopCountArr = totalWorkshopCount();
								$workshopDetails = workshopTariffDetailsQuerySet();								
								 if(sizeof($workshopDetails)>0)
								 {
									 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
									 {	
									
									 ?>
									 <tr>
										<td align="left">
											<?
											$style = '';
											$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
											 $span	= '';
											if(in_array($keyWorkshopclsf, $workshopIdArr) && $workshopCount<1)
										    {
										 		 $style = 'disabled="disabled"';
												 $span	= '<span class="tooltiptext">You Already Chose This Workshop & <br>No More Seat Avalable For This Workshop</span>';
										    }
											else if($workshopCount<1)
										    {
										 		 //$style = 'disabled="disabled"';
												 $span	= '<span class="tooltiptext">No More Seat Avalable For This Workshop</span>';
										    }
											else if(in_array($keyWorkshopclsf, $workshopIdArr))
										    {
										 		 $style = 'disabled="disabled"';
												 $span	= '<span class="tooltiptext">You Already Chose This Workshop</span>';
										    }
											//onclick="calculationWorkshopAmount();"
											?>
												<div class="tooltip">
													<?=$span?>
													<input type="checkbox"  <?=$style?> name="workshop_id[]" id="workshop_id" value="<?=$keyWorkshopclsf?>" operationMode="workshop"   />
												</div>
												&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?>
											 	
											
										</td>
										
										<?
										foreach($rowWorkshopclsf[$delagateCatagory] as $key=>$cutoffvalue)
										{	
										 
										?>
											<td align="right">
											<?
											if($cutoffvalue['REG_ID']=='11')
											{
											?>
												<input type="hidden" workshop_id="<?=$keyWorkshopclsf?>" cutoff_id="<?=$key?>" value="0.00" />
												Free Workshop
											<?
											}
											else
											{
											?>
											<input type="hidden" workshop_id="<?=$keyWorkshopclsf?>" cutoff_id="<?=$key?>" value="<?=$cutoffvalue[$cutoffvalue['CURRENCY']]?>" />
												<?=$cutoffvalue['CURRENCY']?> &nbsp;<?=$cutoffvalue[$cutoffvalue['CURRENCY']]?>
											<?
											}
											?>
											</td>
										<?
										}
										?>
									 <tr>
									 <?
									 }
								 }
								 ?>					
							</table>
							
							<?php
								
								setPaymentTermsRecord("add");
							?>
							
							<table width="100%">
								<tr>
									<td align="left" width="20%">
										<div class="paymentArea">
											Total Amount
											<span style="color:#FFF;">
												
												 <?=getCurrency(getUserClassificationId($delegateId))?>
												
												 <span class="registrationPaybleAmount" id="amount">0.00</span>
											</span>
										</div>
									</td>
								</tr>
							</table>
								
						</td>
					</tr>
					<tr>
						<td colspan="2" align="left">
							<input type="submit" value="Proceed" style="margin-left:20%;" class="btn btn-small btn-blue" />
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function registrationComplimentaryAccompanyTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
		
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		if($delagateCatagory == 15)
		{
			$accompanyCatagory = 17;
		}
		else
		{
			$accompanyCatagory = 16;
		}
		?>
		<form name="frmApplyForAccompany" id="frmApplyForAccompany"  action="<?=$processPage?>" method="post" onsubmit="return formAccompanyValidation();">
			<input type="hidden" name="act" value="step3" />
			<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
			<?
			$registrationDetails = registrationTariffDetailsQuerySet();
			$registrationAmount = $registrationDetails[$accompanyCatagory][$delagateCutoff]['DISPLAY_AMOUNT'];
			?>
			<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Accompany</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($delagateCutoff)?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%">
								<div id="companion_display_div">
									<table width="100%">
										<tr class="thighlight">
											<td align="left">Accompany Information</td>
										</tr>
									</table>
									
									<div id="addMoreAccompany_placeholder_add" operationMode="addMoreAccompany_placeholder"></div>
									
									<table width="100%">
										<tr>
											<td align="right">
												<a onclick="addMoreAccompany('addMoreAccompany_placeholder_add','addMoreAccompany_template')" 
												 operationMode="addAccompanyButton">Add More Accompany</a>
											</td>
										</tr>
									</table>
									
									<table width="100%">
										<tr>
											<td align="left" width="20%">
												<div style="background-color:#fcfae3;border-bottom:2px solid #FE6F06;padding:12px;font-size:22px;font-weight:bold;color:#000;margin:15px 0 0 0;">
													Total Amount :&nbsp;&nbsp;
													<?php
														if($isComplementary!="Y")
														{
															?>
															<span style="color:#cc0000;"><?=getCurrency($delagateCatagory)?> <span id="amount" class="registrationPaybleAmount">0.00</span></span>
															<?php
														}
														else
														{
															?>
															<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> 0.00</span>
															<?php
														}
													?>
													
												</div>
											</td>
										</tr>
									</table>
									
									<table width="100%">
										<tr>
											<td colspan="2" align="left">
												 
												<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Next Step":"Proceed"?>" 
												 class="btn btn-small btn-blue" style="float:right; margin-right:10%;" />
												 
												 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
													 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
													 onclick="window.location.href = 'complimentary_registration.php?show=step4'"  />
											</td>
										</tr>
									</table>
								</div>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
		addMoreAccompanyTemplate("add");
	}
	
	function counterRegistrationAccompanyTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
		
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		if($delagateCatagory == 15)
		{
			$accompanyCatagory = 17;
		}
		else
		{
			$accompanyCatagory = 16;
		}
		?>
		<form name="frmApplyForAccompany" id="frmApplyForAccompany"  action="<?=$processPage?>" method="post" onsubmit="return formAccompanyValidation();">
			<input type="hidden" name="act" value="counterstep3" />
			<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
			<?
			$registrationDetails = registrationTariffDetailsQuerySet();
			$registrationAmount = $registrationDetails[$accompanyCatagory][$delagateCutoff]['AMOUNT'];
			?>
			<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Accompany</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left">Counter Registration</td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%">
								<div id="companion_display_div">
									<table width="100%">
										<tr class="thighlight">
											<td align="left">Accompany Information</td>
										</tr>
									</table>
									
									<div id="addMoreAccompany_placeholder_add" operationMode="addMoreAccompany_placeholder"></div>
									
									<table width="100%">
										<tr>
											<td align="right">
												<a onclick="addMoreAccompany('addMoreAccompany_placeholder_add','addMoreAccompany_template')" 
												 operationMode="addAccompanyButton">Add More Accompany</a>
											</td>
										</tr>
									</table>
									
									<table width="100%">
										<tr>
											<td align="left" width="20%">
												<div style="background-color:#fcfae3;border-bottom:2px solid #FE6F06;padding:12px;font-size:22px;font-weight:bold;color:#000;margin:15px 0 0 0;">
													Total Amount :&nbsp;&nbsp;
													<?php
														if($isComplementary!="Y")
														{
															?>
															<span style="color:#cc0000;"><?=getCurrency($delagateCatagory)?> <span id="amount" class="registrationPaybleAmount">0.00</span></span>
															<?php
														}
														else
														{
															?>
															<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> 0.00</span>
															<?php
														}
													?>
													
												</div>
											</td>
										</tr>
									</table>
									
									<table width="100%">
										<tr>
											<td colspan="2" align="left">
												 
												<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Next Step":"Proceed"?>" 
												 class="btn btn-small btn-blue" style="float:right; margin-right:10%;" />
												 
												 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
													 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
													 onclick="window.location.href = 'registration.php?show=notification'"  />
											</td>
										</tr>
									</table>
								</div>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
		addMoreAccompanyTemplate("add");
	}
	
	function addAccompanyFormTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg,$mycms;
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
		$delegateId 	=  $_REQUEST['id'];
		$spotUser		= $_REQUEST['userREGtype'];	
		$rowFetchUser   = getUserDetails($delegateId);
		$delagateCatagory = getUserClassificationId($delegateId);
			$accompanyCatagory = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type  //2;
	?>
		<form name="frmApplyForAccompany" method="post" action="registration.process.php" onSubmit="return accompanyFromValidatior(this);">
			<input type="hidden" name="act" value="addAccompny" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
			<input type="hidden" id="type" name="type" value="Accompny" />
			<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
			<input type="hidden" name="delegate_id" value="<?=$rowFetchUser['id']?>" />
			<?
			$registrationDetails   = getAllRegistrationTariffs();
			$registrationAmountArr = $registrationDetails[$accompanyCatagory];
		
			foreach($registrationAmountArr as $key => $value)
			{
				$registrationAmount = $value['AMOUNT'];
				$registrationBasicAmount = $value['DISPLAY_AMOUNT'];
				
			?>
			<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount<?=$key?>" value="<?=$registrationAmount?>" BasicAmount="<?=$registrationBasicAmount?>" />
			<?
			}
			?>
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Add Accompany</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<table width="100%" shortData="on" >
							<thead>
								<tr class="thighlight">
								<td colspan="6" align="left">User Details</td>
								</tr>
								<tr >
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left">
										<?=$rowFetchUser['user_title']?> 
										<?=$rowFetchUser['user_first_name']?> 
										<?=$rowFetchUser['user_middle_name']?> 
										<?=$rowFetchUser['user_last_name']?>
									</td>
									<td width="20%" align="left">Email Id:</td>
									<td width="30%" align="left"><?=$rowFetchUser['user_email_id']?></td>
								</tr>								
								<tr>
									<td align="left">Registration Id:</td>
									<td align="left">
									<?php
									if($rowFetchUser['registration_payment_status']=="PAID" 
									   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE"
									   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
									   || $rowFetchUser['sub_registration_type']=="GOVERNMENT_EMPLOYEE")
									{
										echo $rowFetchUser['user_registration_id'];
									}
									else
									{
										echo "-";
									}
									?>
									</td>
									<td align="left">Unique Sequence:</td>
									<td align="left"><?=$rowFetchUser['user_unique_sequence']?></td>
								</tr>
								<tr>
									<td align="left" valign="top">Registration Classification:</td>
									<td align="left" valign="top"><?=getRegClsfName($delagateCatagory)?></td>
									<td align="left" valign="top">Mobile No:</td>
									<td align="left" valign="top"><?=$rowFetchUser['user_mobile_isd_code']?> - <?=$rowFetchUser['user_mobile_no']?></td>
								</tr>
							</thead>
							
							<tbody>
								<tr class="thighlight">
								<td colspan="6" align="left">Accompany Details</td>
								</tr>
								<?
								$resultAccompanyDetails = getAcompanyDetailsOfDelegate($delegateId);
								$accompanyCounter=0;
								if($resultAccompanyDetails)
								{
								?>
								<tr class="theader">
										<td width="50" align="center">Sl No</td>
										<td width="170" align="center">Accompany Registration Id</td>
										<td width="250" align="center">Tariff Cutoff</td>
										<td align="left">Accompany Name</td>
										<td width="150" align="center">Relationship</td>
										<td width="70" align="center">Age</td>
								</tr>
								<?
								
								 								
								foreach($resultAccompanyDetails as $key=>$rowFetchUser)
								{
										$accompanyCounter++;
										//echo "<pre>";print_r($rowAccompany);echo "</pre>";
								?>
										<tr class="tlisting">
											<td align="center"><?=$accompanyCounter?></td>
											<td align="center">
												<?=($rowFetchUser['registration_payment_status']=="UNPAID")?"-":$rowFetchUser['user_registration_id']?>
											</td>
											<td align="left"><?=getCutoffName($rowFetchUser['registration_tariff_cutoff_id'])?></td>
											<td align="left"><?=strtoupper($rowFetchUser['user_full_name'])?></td>
											<td align="center"><?=strtoupper($rowFetchUser['accompany_relationship'])?></td>
											<td align="center"><?=strtoupper($rowFetchUser['user_age'])?></td>
										</tr>
								<?php
									}
								}
								else
								{	
								?>
									<tr>
										<td colspan="6" align="center">
											<span class="mandatory">No Record Present.</span>
										</td>
									</tr>
								<?php
								}	
									?>
							</tbody>
							
						</table>
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">Registration Cutoff</td>
							</tr>
							<tr>
								<td align="left" style="width:20%;">
									Cutoff Title: <span class="mandatory">*</span>
								</td>
								<td align="left" style="width:30%;">
									<select style="width:90%;" name="cutoff_id_add" id="cutoff_id_add" operationMode="cutoff_id" onchange="calculateAccompanyAmount();">
										<option value="">-- Choose Cutoff --</option>
										<?php
											$sqlFetchCutoff 	=	array();
											$sqlFetchCutoff['QUERY'] = "SELECT * 
																		  FROM "._DB_TARIFF_CUTOFF_."
																          WHERE `status` = 'A' 
																	   ORDER BY id";
											
											$resultCutoff	= $mycms->sql_select($sqlFetchCutoff);
											if($resultCutoff){
												if($spotUser == 'SPOT')
												{
											?>
												 <option value="4" selected="selected">Spot</option>
											<?
												}
												else
												{
												 foreach($resultCutoff as $keyCutoff=>$rowCutoff){
												?>
													<option value="<?=$rowCutoff['id']?>"><?=$rowCutoff['cutoff_title']?></option>
												<?php
												}
											    }
											}
										?>
									</select>
								</td>
								<td width="50%"></td>
							</tr>
						</table>
						<table width="100%">
							<div id="companion_display_div">
								<table width="100%">
									<tr class="thighlight">
										<td align="left">Accompany Information</td>
									</tr>
								</table>
								
								<div id="addMoreAccompany_placeholder_add" operationMode="addMoreAccompany_placeholder"></div>
								
								<table width="100%" style=" <?=($accompanyCounter>=3)?"display:none;":""?>">
									<tr>
										<td align="left">
											<a onclick="addMoreAccompany('addMoreAccompany_placeholder_add','addMoreAccompany_template',<?=$accompanyCounter?>)" 
											 operationMode="addAccompanyButton">Add More Accompany</a>
										</td>
									</tr>
								</table>
								<?php
									setPaymentTermsRecord("add");
								?>	
								<table width="100%">
									<tr>
										<td align="left" width="20%">
											<div class="paymentArea">
												Total Amount
												<span style="color:#FFF;">
													 <?=getRegistrationCurrency(getUserClassificationId($delegateId))?>
													 <span class="registrationPaybleAmount" id="amount">0.00</span>
												</span><span style="font-size:15px; color:#993300">(Including GST)</span>
											</div>
										</td>
									</tr>
								</table>
								
								<table width="100%">
									<tr>
										<td colspan="2" align="left">
											<button style="margin-left:20%;" class="btn btn-small btn-blue" type="submit">Proceed</button>
										</td>
									</tr>
								</table>
								
							</div>
						</table>
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						
					</td>
				</tr>			
			</table>
		</form>
	<?
	addMoreAccompanyTemplate("add",$currentCutoffId);	
	}
	
	function addGuestAccompanyFormTemplate($requestPage, $processPage, $registrationRequest="GUEST")
	{
		global $cfg,$mycms;
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
		
		$delegateId 		= $_REQUEST['id'];
		$spotUser			= $_REQUEST['userREGtype'];	
		$rowFetchUser   	= getUserDetails($delegateId);
		$delagateCatagory 	= getUserClassificationId($delegateId);
		$accompanyCatagory 	= 2;
		$currentCutoffId    = getTariffCutoffId();
	?>
		<form name="frmApplyForAccompany" method="post" action="registration.process.php">
			<input type="hidden" name="act" value="addGuestAccompany" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
			<input type="hidden" id="type" name="type" value="GUEST" />
			<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
			<input type="hidden" name="delegate_id" value="<?=$rowFetchUser['id']?>" />
			<?
			$registrationDetails   = getAllRegistrationTariffs();
			$registrationAmountArr = $registrationDetails[$accompanyCatagory];
		
			foreach($registrationAmountArr as $key => $value)
			{
				$registrationAmount = $value['AMOUNT'];
				$registrationBasicAmount = $value['DISPLAY_AMOUNT'];
				
			?>
			<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount<?=$key?>" value="0" BasicAmount="0" />
			<?
			}
			
			$currentDAmount		 = 0;
			$dinnerTariffArray   = getAllDinnerTarrifDetails();
			foreach($dinnerTariffArray as $keyDinner=>$dinnerValues)
			{
				if($keyDinner==2)
				{
					foreach($dinnerValues as $cut=>$val)
					{
						if($cut==$currentCutoffId)
						{
							$currentDAmount = $val['AMOUNT'];
						}
			?>
			<input type="hidden" use="storedTariffAmount" cutoffId="<?=$cut?>" name="storedTariffAmount" id="storedTariffAmount" value="<?=$val['AMOUNT']?>"/>
			<?
					}
				}
			}
			?>
			<input type="hidden" use="dinner_TariffAmount" name="dinner_TariffAmount" id="dinnerTariffAmount" value="<?=$currentDAmount?>"/>
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Add Guest</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<table width="100%" shortData="on" >
							<thead>
								<tr class="thighlight">
								<td colspan="6" align="left">User Details</td>
								</tr>
								<tr >
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left">
										<?=$rowFetchUser['user_title']?> 
										<?=$rowFetchUser['user_first_name']?> 
										<?=$rowFetchUser['user_middle_name']?> 
										<?=$rowFetchUser['user_last_name']?>
									</td>
									<td width="20%" align="left">Email Id:</td>
									<td width="30%" align="left"><?=$rowFetchUser['user_email_id']?></td>
								</tr>								
								<tr>
									<td align="left">Registration Id:</td>
									<td align="left">
									<?php
									if($rowFetchUser['registration_payment_status']=="PAID" 
									   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE"
									   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
									   || $rowFetchUser['sub_registration_type']=="GOVERNMENT_EMPLOYEE")
									{
										echo $rowFetchUser['user_registration_id'];
									}
									else
									{
										echo "-";
									}
									?>
									</td>
									<td align="left">Unique Sequence:</td>
									<td align="left"><?=$rowFetchUser['user_unique_sequence']?></td>
								</tr>
								<tr>
									<td align="left" valign="top">Registration Classification:</td>
									<td align="left" valign="top"><?=getRegClsfName($delagateCatagory)?></td>
									<td align="left" valign="top">Mobile No:</td>
									<td align="left" valign="top"><?=$rowFetchUser['user_mobile_isd_code']?> - <?=$rowFetchUser['user_mobile_no']?></td>
								</tr>
							</thead>
							
							<tbody>
								<tr class="thighlight">
								<td colspan="6" align="left">Guest Details</td>
								</tr>
								<?
								$resultAccompanyDetails = getAcompanyDetailsOfDelegate($delegateId);
								$accompanyCounter=0;
								if($resultAccompanyDetails)
								{
								?>
								<tr class="theader">
										<td width="50" align="center">Sl No</td>
										<td width="170" align="center">Accompany Registration Id</td>
										<td width="250" align="center">Tariff Cutoff</td>
										<td align="left">Accompany Name</td>
								</tr>
								<?						 								
									foreach($resultAccompanyDetails as $key=>$rowFetchUser)
									{
										if($rowFetchUser['registration_request']=='GUEST')
										{
											$accompanyCounter++;
											//echo "<pre>";print_r($rowAccompany);echo "</pre>";
								?>
										<tr class="tlisting">
											<td align="center"><?=$accompanyCounter?></td>
											<td align="center"><?=$rowFetchUser['user_registration_id']?></td>
											<td align="left"><?=getCutoffName($rowFetchUser['registration_tariff_cutoff_id'])?></td>
											<td align="left"><?=strtoupper($rowFetchUser['user_full_name'])?></td>
										</tr>
								<?php
										}
									}
								}
								else
								{	
								?>
									<tr>
										<td colspan="4" align="center">
											<span class="mandatory">No Record Present.</span>
										</td>
									</tr>
								<?php
								}	
									?>
							</tbody>
							
						</table>
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">Registration Cutoff</td>
							</tr>
							<tr>
								<td align="left" style="width:20%;">
									Cutoff Title: <span class="mandatory">*</span>
								</td>
								<td align="left" style="width:30%;">
									<select style="width:90%;" name="cutoff_id_add" id="cutoff_id_add" operationMode="cutoff_id" onchange="changeInCutoff(this)">
										<option value="">-- Choose Cutoff --</option>
										<?php
											$sqlFetchCutoff 	=	array();
											$sqlFetchCutoff['QUERY'] = "SELECT * 
																		  FROM "._DB_TARIFF_CUTOFF_."
																          WHERE `status` = 'A' 
																	   ORDER BY id";
											
											$resultCutoff	= $mycms->sql_select($sqlFetchCutoff);
											if($resultCutoff)
											{
												if($spotUser == 'SPOT')
												{
											?>
												 <option value="4" selected="selected">Spot</option>
											<?
												}
												else
												{
												 	foreach($resultCutoff as $keyCutoff=>$rowCutoff)
													{
												?>
													<option value="<?=$rowCutoff['id']?>"><?=$rowCutoff['cutoff_title']?></option>
												<?php
													}
											    }
											}
										?>
									</select>
								</td>
								<td width="50%"></td>
							</tr>
						</table>
						<script>
						function changeInCutoff(obj)
						{
							var cutoff = $(obj).val();
							$("input[type=hidden][use=dinner_TariffAmount]").val($("input[use=storedTariffAmount][cutoffId='"+cutoff+"']").val());
							calculateAccompanyAmount();
						}
						$(document).ready(function(){
							changeInCutoff($("#cutoff_id_add"));
						});
						</script>
						<table width="100%">
							<div id="companion_display_div">
								<table width="100%">
									<tr class="thighlight">
										<td align="left">Guest Information</td>
									</tr>
								</table>
								
								<div id="addMoreAccompany_placeholder_add" operationMode="addMoreAccompany_placeholder"></div>
								
								<table width="100%">
									<tr>
										<td align="left">
											<a onclick="addMoreAccompany('addMoreAccompany_placeholder_add','addMoreAccompany_template',<?=$accompanyCounter?>)" 
											 operationMode="addAccompanyButton">Add More Guest</a>
										</td>
									</tr>
								</table>
								<?php
									setPaymentTermsRecord("add");
								?>	
								<table width="100%">
									<tr>
										<td align="left" width="20%">
											<div class="paymentArea">
												Total Amount
												<span style="color:#FFF;">
													 <?=getRegistrationCurrency(getUserClassificationId($delegateId))?>
													 <span class="registrationPaybleAmount" id="amount">0.00</span>
												</span><span style="font-size:15px; color:#993300">(Including GST)</span>
											</div>
										</td>
									</tr>
								</table>
								
								<table width="100%">
									<tr>
										<td colspan="2" align="left">
											<button style="margin-left:20%;" class="btn btn-small btn-blue" type="submit">Proceed</button>
										</td>
									</tr>
								</table>
								
							</div>
						</table>
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						
					</td>
				</tr>			
			</table>
		</form>
	<?
		addMoreAccompanyTemplate("add",$currentCutoffId);	
	}
	
	function addMoreAccompanyTemplate($formType,$currentCutoffId)
	{
		global $cfg, $mycms;
	?>
		<div id="addMoreAccompany_template" style="display:none;">
			<div operationMode="accompanyPerson" sequenceBy="#COUNTER">
				
				<table width="100%">
					<tr class="theader">
						<td colspan="4" align="left" style="border-top: dotted 1px #D5D5D5;">
							<b style="font-size:12px;">Accompany #COUNTER</b>
							<span style="float:right;"><a title="Remove" operationMode="removeRow" style="cursor:pointer;" sequenceBy="#COUNTER">&times;</a></span>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">
							Name 
							<span class="mandatory">*</span>
						</td>
						<td width="30%" align="left">
							<input type="hidden" name="accompany_selected_<?=$formType?>[]" value="#COUNTER" />
						
							<input type="text" name="accompany_name_<?=$formType?>[#COUNTER]" id="accompany_name_<?=$formType?>_#COUNTER" 
							 style="width:90%; text-transform:uppercase;" operationMode="accompany_name" required/>
						</td>
						<td></td>
						<td></td>
                        
					</tr>
					<tr>
						
						<td align="left" valign="top">Food Preference</td>
						<td align="left" valign="top">
							<input type="radio" name="accompany_food_choice[#COUNTER]" id="accompany_food_preference_<?=$formType?>_#COUNTER" 
							 value="VEG"  checked="checked"/> Veg
							
							<input type="radio" name="accompany_food_choice[#COUNTER]" id="accompany_food_preference_<?=$formType?>_#COUNTER" 
							 value="NON VEG" /> Non Veg 
						</td>
					</tr>
                    <? /*
                    <tr>
						
						<td align="left" valign="top">Gala Dinner <span style="color:#FF0000;"></span></td>
                        <?
				        $dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
                        //echo '<pre>';print_r($dinnerTariffArray);die('kk');
				        foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
				        {
				        ?> 
                        <td>
                            <input type="checkbox" groupName="dinner_value[]" name="dinner_value[#COUNTER]" value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
                            operationMode="dinner_choice"  ammount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"/> Yes 
				            &nbsp;
				        </td>	
                        <?
                        }
                        ?>			
					</tr>
					*/ ?>
				</table>
			
			</div>
		</div>
	<?php
	}
	
	function registrationAccommodationTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
										  WHERE `status` != 'D' 
									   ORDER BY `cutoff_sequence` ASC";	
														  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
				
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		?>
		<form name="frmApplyForAccommodation" id="frmApplyForAccommodation"  action="<?=$processPage?>" method="post" onsubmit="return formAccommodationValidation();">
			<input type="hidden" name="act" value="step4" />
			<?	
			$sqlAccommodationTariff['QUERY']    = "SELECT accommodationTariff.*,
													 accommodationPackage.* 
												
												FROM "._DB_TARIFF_ACCOMMODATION_." accommodationTariff 
												
										  INNER JOIN "._DB_ACCOMMODATION_PACKAGE_." accommodationPackage 
												  ON accommodationPackage.id = accommodationTariff.package_id 
											   
											   WHERE accommodationTariff.tariff_cutoff_id = '".$delagateCutoff."'";
											   
			$resultAccommodationTariff = $mycms->sql_select($sqlAccommodationTariff);
			
			if($resultAccommodationTariff)
			{
				foreach($resultAccommodationTariff as $keyTariff=>$rowAccommodationTariff)
				{
			?>
					<input type="hidden" operationMode="accommodation_tariff_details" checkInId="<?=$rowAccommodationTariff['checkin_date_id']?>" checkOutId="<?=$rowAccommodationTariff['checkout_date_id']?>" roomTypeId="<?=$rowAccommodationTariff['package_id']?>" hotelId="<?=$rowAccommodationTariff['hotel_id']?>"  packageId="<?=$rowAccommodationTariff['package_id']?>" cutoff ="<?=$rowAccommodationTariff['tariff_cutoff_id']?>" priceTag="<?=($registrationClassificationId==4)?"USD":"INR"?>" value="<?=($registrationClassificationId==4)?intval($rowAccommodationTariff['usd_amount']):intval($rowAccommodationTariff['inr_amount']);?>" />
			<?php	
				}
			}
			
			?>
			<input type="hidden" name="cutoff_id_add" id="cutoff_id_add" value="<?=$delagateCutoff?>" />
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Accommodation</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($delagateCutoff)?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%">
								<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Accommodation Information</td>
								</tr>
								<tr>
									<td align="left" valign="top">Choose Hotel: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_hotel_id" id="accommodation_hotel_id" forType="hotel" class="drpdwn" style="text-transform:uppercase;width:90%;">
											<option value="">-- Select --</option>
											<?php
											$sqlFetchHotel['QUERY']    = "SELECT hotel.* 
																		    FROM "._DB_MASTER_HOTEL_." hotel 
																		    WHERE `status` = 'A'"; 
																	  
											$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
											if($resultFetchHotel)
											{
												foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
												{
											?>
													<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['hotel_name']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left" width="20%" valign="top">Check In Date: <span class="mandatory">*</span></td>
									<td align="left" width="30%" valign="top">
										<select name="check_in_date" id="check_in_date" class="drpdwn" style="width:90%;"   >
											<option value="">-- Select Date  --</option>
											<?php		//	forType="checkInDate"
											// FETCHING ACCOMMODATION BOOKING DATES
											$sqlAccommodationDate['QUERY']    = "SELECT * 
																					FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
																					WHERE `status` = 'A'";
																				
											$resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate); 
											
											if($resultAccommodationDate)
											{
												foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationDate)
												{
											?>
													<option value="<?=$rowAccommodationDate['id']?>"><?=$rowAccommodationDate['check_in_date']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
									<td align="" width="20%" valign="top">Check Out Date:</td>
									<td align="" width="30%" valign="top">
										 <select name="check_out_date" id="check_out_date" class="drpdwn" style="width:90%;"   >
											<option value="">-- Select Date  --</option>
											<?php		//	forType="checkOutDate"
												// FETCHING ACCOMMODATION BOOKING DATES
												$sqlAccommodationDate['QUERY']    = "SELECT * 
																					  FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
																					WHERE `status` = 'A'";
																					
												$resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate); 
												
												if($resultAccommodationDate)
												{
													foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationDate)
													{
												?>
														<option value="<?=$rowAccommodationDate['id']?>"><?=$rowAccommodationDate['check_out_date']?></option>
												<?php
													}
												}
												?>
										</select>
									</td>
								</tr>
								
								<tr>
									<td align="left" valign="top">Choose Room Type: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_roomType_id" id="accommodation_roomType_id" class="drpdwn" 
										        style="width:90%;">
											<option value="">-- Select --</option>
											<?php		//	disabled="disabled" forType="roomType"
											$sqlFetchHotel['QUERY']    = "SELECT * 
																		    FROM "._DB_ACCOMMODATION_PACKAGE_."  
																		   WHERE `status` = 'A'
																		     AND `hotel_id` = '".$hotel_id."'	"; 
											 
											$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
											if($resultFetchHotel)
											{
											foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
											{
											?>
											<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['room_type']?></option>
											<?php
											}
											}
											?>
										</select>
									</td>
									<td align="left" valign="top">No of Rooms: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="booking_quantity" id="booking_quantity" operationarea="booking_quantity" class="drpdwn" style="text-transform:uppercase;width:90%;" forType="bookingQuantity">
											<option value="1" selected="selected">1</option>
											<?php /*?><option value="2">2</option>
											<option value="3">3</option><?php */?>
										</select>
									</td>
								</tr>
							</table>
							<div id="accommodationDetailsPlaceholder">
								<table width="100%" id="accommodationDetailsPlaceholderTable">
									<tr class="thighlight">
										<td colspan="4" align="left">Select Guests</td>
									</tr>
									<tr class="theader">
										<td align="center" valign="top">ROOM #</td>
										<td align="center" valign="top">GUESTS</td>
										<td align="center" valign="top">TARIFF</td>
										<td align="right" valign="top">AMOUNT</td>
									</tr>
								</table>
							</div>
							<br />
							<span class="tab-text" id="accName">
								<table width="100%">
									<tr>
										<td width="20%" >
											<label>Accompany Name <span class="mandatory">*</span> :</label> 
										</td>										
										<td colspan="3" >									
											<input type="text" name="accmName" id="accmName" value="" style="width:30%; text-transform:uppercase;" >
										</td>
									</tr>
								</table>							
							</span>	
								<table width="100%">
									<tr>
										<td align="left" width="20%">
											<div style="background-color:#00ADC6;border-bottom:2px solid #FE6F06;padding:12px;font-size:22px;font-weight:bold;color:#000;margin:15px 0 0 0;">
												Total Amount &nbsp;&nbsp;&nbsp;
												<?php
													if($isComplementary!="Y")
													{
														?>
														<!--span class="amount"><?=($registrationClassificationId==4)?"USD":"INR"?> 
															<span id="amount" operationMode="totalAccomodationAmount">0.00</span>
														</span-->
														<span style="color:#FFF;"><?=getRegistrationCurrency($delagateCatagory)?> <span id="amount" operationMode="totalAccomodationAmount">0.00</span> <!--span id="amount" class="registrationPaybleAmount">0.00</span--></span><span style="font-size:15px; color:#993300">(Including GST)</span>
														<?php
													}
													else
													{
														?>
														<span style="color:#FFF;"><?=getRegistrationCurrency($delagateCatagory)?> 0.00</span><span style="font-size:15px; color:#993300">(Including GST)</span>
														<?php
													}
												?>
												
											</div>
										</td>
									</tr>
								</table>
								<div id="accommodationDetailsTemplate" style="display:none;">
								<table width="100%">
									<tr class="tlisting" operationMode="roomDetailsCell" sequenceBy="#COUNTER">
										
										<input type="hidden" name="room_guest_counter[]" operationMode="room_guest_counter" sequenceBy="#COUNTER" value="1" />
										<input type="hidden" name="room_tariff_amount[]" operationMode="room_tariff_amount" sequenceBy="#COUNTER" value="0" />
										
										<td align="center" valign="top">#COUNTER</td>
										<td align="center" valign="top">
											
											<button type="button" style="background:#fff;border-radius:50%;border:thin solid #dfdddd;cursor:pointer;" operationMode="guestMinus" sequenceBy="#COUNTER">-</button>
											&nbsp;&nbsp;
											<span operationMode="guestDisplay" sequenceBy="#COUNTER">1</span>
											&nbsp;&nbsp;
											<button type="button" style="background:#fff;border-radius:50%;border:thin solid #dfdddd;cursor:pointer;" operationMode="guestPlus" sequenceBy="#COUNTER">+</button>
											
										</td>
										<td align="center" valign="top" operationMode="tariffAmount" sequenceBy="#COUNTER">INR 0</td>
										
										<td align="right" valign="top" operationMode="totalAmount" sequenceBy="#COUNTER">INR 0</td>
										
									</tr>
								</table>
							</div>	
								<table width="100%">
									<tr>
										<td colspan="2" align="left">
											 
											
											<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Next Step":"Proceed"?>" 
											 class="btn btn-small btn-blue" style="float:right; margin-right:10%;" />
											 
											 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
												 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
												 onclick="window.location.href = 'registration.process.php?act=step6'"  />
										</td>
									</tr>
								</table>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function registrationAccommodationTemplateForAbstract($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		$userREGtype           =$_REQUEST['userREGtype'];
		$abstractDelegateId    =$_REQUEST['abstractDelegateId'];
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
				
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		?>
		<form name="frmApplyForAccommodation" id="frmApplyForAccommodation"  action="<?=$processPage?>" method="post" onsubmit="return formAccommodationValidation();">
			<input type="hidden" name="userREGtype" value="<?=$userREGtype?>" />
			<input type="hidden" name="abstractDelegateId" value="<?=$abstractDelegateId?>" />
			<input type="hidden" name="act" value="step4_abstractRegistration" />
			<?					
			$sqlAccommodationTariff['QUERY']    = "SELECT accommodationTariff.*,
													 accommodationPackage.roomType_id 
												
												FROM "._DB_TARIFF_ACCOMMODATION_." accommodationTariff 
												
										  INNER JOIN "._DB_PACKAGE_ACCOMMODATION_." accommodationPackage 
												  ON accommodationPackage.id = accommodationTariff.package_id 
											   
											   WHERE accommodationTariff.tariff_cutoff_id = '".$delagateCutoff."'";
											   
			$resultAccommodationTariff = $mycms->sql_select($sqlAccommodationTariff);
			
			if($resultAccommodationTariff)
			{
				foreach($resultAccommodationTariff as $keyTariff=>$rowAccommodationTariff)
				{
			?>
					<input type="hidden" operationMode="accommodation_tariff_details" checkInId="<?=$rowAccommodationTariff['booking_date_id']?>" checkOutId="<?=$rowAccommodationTariff['checkout_date_id']?>" roomTypeId="<?=$rowAccommodationTariff['roomType_id']?>" hotelId="<?=$rowAccommodationTariff['hotel_id']?>"  packageId="<?=$rowAccommodationTariff['package_id']?>" cutoff ="<?=$rowAccommodationTariff['tariff_cutoff_id']?>" priceTag="<?=($registrationClassificationId==4)?"USD":"INR"?>" value="<?=($registrationClassificationId==4)?intval($rowAccommodationTariff['usd_amount']):intval($rowAccommodationTariff['inr_amount']);?>" />
			<?php	
				}
			}
			
			?>
			<input type="hidden" name="cutoff_id_add" id="cutoff_id_add" value="<?=$delagateCutoff?>" />
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Accommodation</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($delagateCutoff)?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%">
								<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Accommodation Information</td>
								</tr>
								<tr>
									<td align="left" valign="top">Choose Hotel: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_hotel_id" id="accommodation_hotel_id" forType="hotel" class="drpdwn" style="text-transform:uppercase;width:90%;">
											<option value="">-- Select --</option>
											<?php
											$sqlFetchHotel['QUERY']    = "SELECT hotel.* 
																   FROM "._DB_MASTER_HOTEL_." hotel 
																  WHERE `status` = 'A'"; 
																	  
											$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
											if($resultFetchHotel)
											{
												foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
												{
											?>
													<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['hotel_name']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left" width="20%" valign="top">Check In Date: <span class="mandatory">*</span></td>
									<td align="left" width="30%" valign="top">
										<select name="check_in_date" id="check_in_date" class="drpdwn" style="width:90%;"   >
											<option value="">-- Select Date  --</option>
											<?php		//	forType="checkInDate"
											// FETCHING ACCOMMODATION BOOKING DATES
											$sqlAccommodationDate['QUERY']    = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
																				WHERE `status` = 'A'";
																				
											$resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate); 
											
											if($resultAccommodationDate)
											{
												foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationDate)
												{
											?>
													<option value="<?=$rowAccommodationDate['id']?>"><?=$rowAccommodationDate['check_in_date']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
									<td align="" width="20%" valign="top">Check Out Date:</td>
									<td align="" width="30%" valign="top">
										 <select name="check_out_date" id="check_out_date" class="drpdwn" style="width:90%;"   >
											<option value="">-- Select Date  --</option>
											<?php		//	forType="checkOutDate"
												// FETCHING ACCOMMODATION BOOKING DATES
												$sqlAccommodationDate['QUERY']    = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
																					WHERE `status` = 'A'";
																					
												$resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate); 
												
												if($resultAccommodationDate)
												{
													foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationDate)
													{
												?>
														<option value="<?=$rowAccommodationDate['id']?>"><?=$rowAccommodationDate['check_out_date']?></option>
												<?php
													}
												}
												?>
										</select>
									</td>
								</tr>
								
								<tr>
									<td align="left" valign="top">Choose Room Type: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_roomType_id" id="accommodation_roomType_id" class="drpdwn" 
										        style="width:90%;">
											<option value="">-- Select --</option>
											<?php		//	disabled="disabled" forType="roomType"
											$sqlFetchHotel['QUERY']    = "SELECT * 
											  FROM "._DB_MASTER_HOTEL_ROOM_TYPE_."  
											 WHERE `status` = 'A'"; 
											 
											$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
											if($resultFetchHotel)
											{
											foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
											{
											?>
											<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['room_type']?></option>
											<?php
											}
											}
											?>
										</select>
									</td>
									<td align="left" valign="top">No of Rooms: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="booking_quantity" id="booking_quantity" class="drpdwn" style="text-transform:uppercase;width:90%;" forType="bookingQuantity">
											<option value="1" selected="selected">1</option>
										</select>
									</td>
								</tr>
							</table>
							<div id="accommodationDetailsPlaceholder">
								<table width="100%" id="accommodationDetailsPlaceholderTable">
									<tr class="thighlight">
										<td colspan="4" align="left">Select Guests</td>
									</tr>
									<tr class="theader">
										<td align="center" valign="top">ROOM #</td>
										<td align="center" valign="top">GUESTS</td>
										<td align="center" valign="top">TARIFF</td>
										<td align="right" valign="top">AMOUNT</td>
									</tr>
								</table>
							</div>
							<br />
							<span class="tab-text" id="accName">
								<table width="100%">
									<tr>
										<td width="20%" >
											<label>Accompany Name <span class="mandatory">*</span> :</label> 
										</td>										
										<td colspan="3" >									
											<input type="text" name="accmName" id="accmName" value="" style="width:30%; text-transform:uppercase;" >
										</td>
									</tr>
								</table>							
							</span>	
								<table width="100%">
									<tr>
										<td align="left" width="20%">
											<div style="background-color:#00ADC6;border-bottom:2px solid #FE6F06;padding:12px;font-size:22px;font-weight:bold;color:#000;margin:15px 0 0 0;">
												Total Amount &nbsp;&nbsp;&nbsp;
												<?php
													if($isComplementary!="Y")
													{
														?>
														<!--span class="amount"><?=($registrationClassificationId==4)?"USD":"INR"?> 
															<span id="amount" operationMode="totalAccomodationAmount">0.00</span>
														</span-->
														<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> <span id="amount" operationMode="totalAccomodationAmount">0.00</span> <!--span id="amount" class="registrationPaybleAmount">0.00</span--></span><span style="font-size:15px; color:#993300">(Including GST)</span>
														<?php
													}
													else
													{
														?>
														<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> 0.00</span><span style="font-size:15px; color:#993300">(Including GST)</span>
														<?php
													}
												?>
												
											</div>
										</td>
									</tr>
								</table>
								<div id="accommodationDetailsTemplate" style="display:none;">
								<table width="100%">
									<tr class="tlisting" operationMode="roomDetailsCell" sequenceBy="#COUNTER">
										
										<input type="hidden" name="room_guest_counter[]" operationMode="room_guest_counter" sequenceBy="#COUNTER" value="1" />
										<input type="hidden" name="room_tariff_amount[]" operationMode="room_tariff_amount" sequenceBy="#COUNTER" value="0" />
										
										<td align="center" valign="top">#COUNTER</td>
										<td align="center" valign="top">
											
											<button type="button" style="background:#fff;border-radius:50%;border:thin solid #dfdddd;cursor:pointer;" operationMode="guestMinus" sequenceBy="#COUNTER">-</button>
											&nbsp;&nbsp;
											<span operationMode="guestDisplay" sequenceBy="#COUNTER">1</span>
											&nbsp;&nbsp;
											<button type="button" style="background:#fff;border-radius:50%;border:thin solid #dfdddd;cursor:pointer;" operationMode="guestPlus" sequenceBy="#COUNTER">+</button>
											
										</td>
										<td align="center" valign="top" operationMode="tariffAmount" sequenceBy="#COUNTER">INR 0</td>
										
										<td align="right" valign="top" operationMode="totalAmount" sequenceBy="#COUNTER">INR 0</td>
										
									</tr>
								</table>
							</div>	
								<table width="100%">
									<tr>
										<td colspan="2" align="left">
											 
											
											<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Next Step":"Proceed"?>" 
											 class="btn btn-small btn-blue" style="float:right; margin-right:10%;" />
											 
											 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
												 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
												 onclick="window.location.href = 'registration.process.php?act=step6_abstractRegistration&abstractDelegateId=<?=$_REQUEST['abstractDelegateId']?>&userREGtype=<?=$_REQUEST['userREGtype']?>'"  />
										</td>
									</tr>
								</table>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function registrationComplimentaryAccommodationTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
				
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		?>
		<form name="frmApplyForAccommodation" id="frmApplyForAccommodation"  action="<?=$processPage?>" method="post" onsubmit="return formAccommodationValidation();">
			<input type="hidden" name="act" value="step4" />
			<?
			//$accommodationDetails = accommodationTariffDetailsQuerySet();
			/*foreach($accommodationDetails as $keyPackage=>$rowPackage)
			{
				foreach($rowPackage as $keyDates=>$rowDates)
				{
					foreach($rowDates as $keyEndDates=>$rowEndDates)
					{
						foreach($rowEndDates as $keyCutOff=>$rowCutOff)
						{
						?>
							<input type="hidden" operationMode="accommodation_tariff_details" 
							packageId="<?=$keyPackage?>" 
							inDates="<?=$keyDates?>" 
							outDates="<?=$keyEndDates?>" 
							cutOff="<?=$keyCutOff?>" 
							priceTag="<?=getCurrency($delagateCatagory)?>" 
							value="<?=$rowCutOff[getCurrency($delagateCatagory)]?>"  />
						<?	
						}	
					}	
				}
			}*/
			
					
			$sqlAccommodationTariff['QUERY']    = "SELECT accommodationTariff.*,
													 accommodationPackage.roomType_id 
												
												FROM "._DB_TARIFF_ACCOMMODATION_." accommodationTariff 
												
										  INNER JOIN "._DB_PACKAGE_ACCOMMODATION_." accommodationPackage 
												  ON accommodationPackage.id = accommodationTariff.package_id 
											   
											   WHERE accommodationTariff.tariff_cutoff_id = '".$delagateCutoff."'";
											   
			$resultAccommodationTariff = $mycms->sql_select($sqlAccommodationTariff);
			
			if($resultAccommodationTariff)
			{
				foreach($resultAccommodationTariff as $keyTariff=>$rowAccommodationTariff)
				{
			?>
					<input type="hidden" operationMode="accommodation_tariff_details" checkInId="<?=$rowAccommodationTariff['booking_date_id']?>" checkOutId="<?=$rowAccommodationTariff['checkout_date_id']?>" roomTypeId="<?=$rowAccommodationTariff['roomType_id']?>" hotelId="<?=$rowAccommodationTariff['hotel_id']?>"  packageId="<?=$rowAccommodationTariff['package_id']?>" cutoff ="<?=$rowAccommodationTariff['tariff_cutoff_id']?>" priceTag="<?=($registrationClassificationId==4)?"USD":"INR"?>" value="<?=($registrationClassificationId==4)?intval($rowAccommodationTariff['usd_amount']):intval($rowAccommodationTariff['inr_amount']);?>" />
			<?php	
				}
			}
			
			?>
			<input type="hidden" name="cutoff_id_add" id="cutoff_id_add" value="<?=$delagateCutoff?>" />
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Accommodation</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($delagateCutoff)?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%">
								<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Accommodation Information</td>
								</tr>
								<tr>
									<td align="left" valign="top">Choose Hotel: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_hotel_id" id="accommodation_hotel_id" forType="hotel" class="drpdwn" style="text-transform:uppercase;width:90%;">
											<option value="">-- Select --</option>
											<?php
											$sqlFetchHotel['QUERY']    = "SELECT hotel.* 
																   FROM "._DB_MASTER_HOTEL_." hotel 
																  WHERE `status` = 'A'"; 
																	  
											$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
											if($resultFetchHotel)
											{
												foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
												{
											?>
													<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['hotel_name']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left" width="20%" valign="top">Check In Date: <span class="mandatory">*</span></td>
									<td align="left" width="30%" valign="top">
										<select name="check_in_date" id="check_in_date" class="drpdwn" style="width:90%;"   >
											<option value="">-- Select Date  --</option>
											<?php		//	forType="checkInDate"
											// FETCHING ACCOMMODATION BOOKING DATES
											$sqlAccommodationDate['QUERY']    = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
																				WHERE `status` = 'A'";
																				
											$resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate); 
											
											if($resultAccommodationDate)
											{
												foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationDate)
												{
											?>
													<option value="<?=$rowAccommodationDate['id']?>"><?=$rowAccommodationDate['check_in_date']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
									<td align="" width="20%" valign="top">Check Out Date:</td>
									<td align="" width="30%" valign="top">
										 <select name="check_out_date" id="check_out_date" class="drpdwn" style="width:90%;"   >
											<option value="">-- Select Date  --</option>
											<?php		//	forType="checkOutDate"
												// FETCHING ACCOMMODATION BOOKING DATES
												$sqlAccommodationDate['QUERY']    = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
																					WHERE `status` = 'A'";
																					
												$resultAccommodationDate = $mycms->sql_select($sqlAccommodationDate); 
												
												if($resultAccommodationDate)
												{
													foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationDate)
													{
												?>
														<option value="<?=$rowAccommodationDate['id']?>"><?=$rowAccommodationDate['check_out_date']?></option>
												<?php
													}
												}
												?>
										</select>
									</td>
								</tr>
								
								<tr>
									<td align="left" valign="top">Choose Room Type: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_roomType_id" id="accommodation_roomType_id" class="drpdwn" 
										        style="width:90%;">
											<option value="">-- Select --</option>
											<?php		//	disabled="disabled" forType="roomType"
											$sqlFetchHotel['QUERY']    = "SELECT * 
											  FROM "._DB_MASTER_HOTEL_ROOM_TYPE_."  
											 WHERE `status` = 'A'"; 
											 
											$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
											if($resultFetchHotel)
											{
											foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
											{
											?>
											<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['room_type']?></option>
											<?php
											}
											}
											?>
										</select>
									</td>
									<td align="left" valign="top">No of Rooms: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="booking_quantity" id="booking_quantity" class="drpdwn" style="text-transform:uppercase;width:90%;" forType="bookingQuantity">
											<option value="1" selected="selected">1</option>
										</select>
									</td>
								</tr>
							</table>
							<div id="accommodationDetailsPlaceholder">
								<table width="100%" id="accommodationDetailsPlaceholderTable">
									<tr class="thighlight">
										<td colspan="4" align="left">Select Guests</td>
									</tr>
									<tr class="theader">
										<td align="center" valign="top">ROOM #</td>
										<td align="center" valign="top">GUESTS</td>
										<td align="center" valign="top">TARIFF</td>
										<td align="right" valign="top">AMOUNT</td>
									</tr>
								</table>
							</div>
							<br />
							<span class="tab-text" id="accName">
								<table width="100%">
									<tr>
										<td width="20%" >
											<label>Accompany Name <span class="mandatory">*</span> :</label> 
										</td>										
										<td colspan="3" >									
											<input type="text" name="accmName" id="accmName" value="" style="width:30%; text-transform:uppercase;" >
										</td>
									</tr>
								</table>							
							</span>	
								<table width="100%">
									<tr>
										<td align="left" width="20%">
											<div style="background-color:#00ADC6;border-bottom:2px solid #FE6F06;padding:12px;font-size:22px;font-weight:bold;color:#000;margin:15px 0 0 0;">
												Total Amount &nbsp;&nbsp;&nbsp;
												<?php
													if($isComplementary!="Y")
													{
														?>
														<!--span class="amount"><?=($registrationClassificationId==4)?"USD":"INR"?> 
															<span id="amount" operationMode="totalAccomodationAmount">0.00</span>
														</span-->
														<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> <span id="amount" operationMode="totalAccomodationAmount">0.00</span> <!--span id="amount" class="registrationPaybleAmount">0.00</span--></span><span style="font-size:15px; color:#993300">(Including GST)</span>
														<?php
													}
													else
													{
														?>
														<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> 0.00</span><span style="font-size:15px; color:#993300">(Including GST)</span>
														<?php
													}
												?>
												
											</div>
										</td>
									</tr>
								</table>
								<div id="accommodationDetailsTemplate" style="display:none;">
								<table width="100%">
									<tr class="tlisting" operationMode="roomDetailsCell" sequenceBy="#COUNTER">
										
										<input type="hidden" name="room_guest_counter[]" operationMode="room_guest_counter" sequenceBy="#COUNTER" value="1" />
										<input type="hidden" name="room_tariff_amount[]" operationMode="room_tariff_amount" sequenceBy="#COUNTER" value="0" />
										
										<td align="center" valign="top">#COUNTER</td>
										<td align="center" valign="top">
											
											<button type="button" style="background:#fff;border-radius:50%;border:thin solid #dfdddd;cursor:pointer;" operationMode="guestMinus" sequenceBy="#COUNTER">-</button>
											&nbsp;&nbsp;
											<span operationMode="guestDisplay" sequenceBy="#COUNTER">1</span>
											&nbsp;&nbsp;
											<button type="button" style="background:#fff;border-radius:50%;border:thin solid #dfdddd;cursor:pointer;" operationMode="guestPlus" sequenceBy="#COUNTER">+</button>
											
										</td>
										<td align="center" valign="top" operationMode="tariffAmount" sequenceBy="#COUNTER">INR 0</td>
										
										<td align="right" valign="top" operationMode="totalAmount" sequenceBy="#COUNTER">INR 0</td>
										
									</tr>
								</table>
							</div>	
								<table width="100%">
									<tr>
										<td colspan="2" align="left">
											 
											
											<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Next Step":"Proceed"?>" 
											 class="btn btn-small btn-blue" style="float:right; margin-right:10%;" />
											 
											 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
												 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
												 onclick="window.location.href = 'complimentary_general_registration_registration.process.php?act=step6'"  />
										</td>
									</tr>
								</table>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function registrationTourTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
										  WHERE `status` != 'D' 
									   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
		
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		?>
		<form name="frmApplyForTour" id="frmApplyForTour"  action="<?=$processPage?>" method="post" onsubmit="return tourformvalidation();">
			<input type="hidden" name="act" value="step5" />
			<input type="hidden" id="cutoffId" name="cutoffId" value="<?=$delagateCutoff?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Tour</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($delagateCutoff)?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%" id="addMore">
								<tr class="thighlight">
									<td colspan="5" align="left">Tour Information</td>
								</tr>
								<tr class="tcat">
									<td align="left">Tour Information</td>
									<?
									$sqlcutoff['QUERY']		   = "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = 'A'";
									$rescutoff			= $mycms->sql_select($sqlcutoff);
									
									if($rescutoff)
									{
										foreach($rescutoff as $keycutoff=>$cutoffvalue)
										{
										?>
											<td align="right"><?=$cutoffvalue['cutoff_title']?></td>
										<?
										}
									}
									?>
									<td align="left">Number Of Person</td>
								</tr>
								<?
								$tourTariff          = tourTariffDetailsQuerySet();
								
								if($tourTariff)
								{
									foreach($tourTariff as $key=>$rowTouPackage) 
									{
									?>
										<tr class="tlisting">
											<td align="left">
												<input type="checkbox" id="tour_td_<?=$key?>" name="tour_id[]" value="<?=$key?>" onclick="disableTourNoOfPerson(<?=$key?>);" operationMode="tour_td"/> &nbsp;&nbsp;&nbsp;
												<?=getTourName($key)?>
											</td>
											<?
											foreach($rowTouPackage as $keyCutoff=>$cutoffvalue) 
											{	
											?>			
												<td align="right">
													<?=$cutoffvalue[getCurrency($delagateCatagory)]?>
													<input type="hidden" 
														   operationMode="tour_tariff_details" 
														   packageId="<?=$key?>" 
														   cutoffId="<?=$keyCutoff?>" 
														   currency="<?=getCurrency($delagateCatagory)?>"
														   value="<?=$cutoffvalue[getCurrency($delagateCatagory)]?>" />
												</td>
											<?
											}	
											?>
											<td align="center" width="15%">
												<select name="number_of_person[<?=$key?>]" id="number_of_person_<?=$key?>" class="drpdwn" use="person_<?=$key?>" disabled="disabled" onchange="calculateTotalTourTariffAmount();" style="text-transform:uppercase;width:90%;">
													<option value="1" selected="selected">1</option>
													<option value="2">2</option>
													<option value="3">3</option>
													<option value="4">4</option>
													<option value="5">5</option>
												</select>
											</td>
										</tr>
								<?php
									}
								}
								?>
							</table>
							
							<table width="100%">
								<tr>
									<td align="left" width="20%">
										<div style="background-color:#00ADC6;border-bottom:2px solid #FE6F06;padding:12px;font-size:22px;font-weight:bold;color:#000;margin:15px 0 0 0;">
											Total Amount &nbsp;&nbsp;
											<?php
												if($isComplementary!="Y")
												{
													?>
													<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?> <span id="amount" class="registrationPaybleAmount">0.00</span></span>
													<?php
												}
												else
												{
													?>
													<span style="color:#FFF;"><?=getCurrency($delagateCatagory)?>  0.00</span>
													<?php
												}
											?>
										</div>
									</td>
								</tr>
							</table>
							
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
									
										<input type="submit" name="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Next Step":"Proceed"?>" 
										 class="btn btn-small btn-blue" style="float:right; margin-right:10%;" />
										 
										 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
												 class="btn btn-small btn-green" style="float:right; margin-right:5%;"
												 onclick="window.location.href = 'registration.php?show=step6'"  />
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function rcounterRegCompleteNotificationTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = "SELECT * FROM "._DB_TARIFF_CUTOFF_." 
								  WHERE `status` != 'D' 
							   ORDER BY `cutoff_sequence` ASC";	
												  
		$resCutoff = $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
		
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		?>
		<form name="frmApplyRegistration" id="frmApplyRegistration"  action="<?=$processPage?>" method="post" onsubmit="return validateWorkshop();">
			<input type="hidden" name="act" value="countersummery" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$delagateName?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$delagateMobile?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$delagateEmail?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left">Counter Registration</td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($delagateCatagory)?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr>
									<td colspan="2" align="center">
										<b style="color:#FF6600">Your registration is acknowledged at AICC RCOG 2019 .</b>
									</td>
								</tr>
							</table>
							
							
							
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
										 
										<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Proceed" 
										 class="btn btn-midium btn-blue" style="float:right; margin-right:10%;"  />
										 
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}	

	function AdditionalRegistrationSummeryTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		include_once('../../includes/function.registration.php');
		//$slipId	 			= $mycms->getSession('SLIP_ID');
		$encodedId = $_REQUEST['sxxi'];
		$slipId = base64_decode($encodedId);
		$userREGtype =  $_REQUEST['userREGtype'];
		
		$sqlSlip					= array();
		$sqlSlip['QUERY']		    = "SELECT * 
										FROM "._DB_SLIP_." 
									    WHERE `status` = ? 
									     AND `id` = ?";
		$sqlSlip['PARAM'][]    = array('FILD' => 'status',             'DATA' =>'A',               	  'TYP' => 's');
		$sqlSlip['PARAM'][]    = array('FILD' => 'id',            	   'DATA' =>$slipId,               'TYP' => 's');
		$resSlip			= $mycms->sql_select($sqlSlip);
		$rowSlip			= $resSlip[0];
		
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		$types = $userDetails['operational_area'];
		
		?>
		
		<form name="frmApplyPayment" id="frmApplyPayment"  action="<?=$processPage?>" method="post" onsubmit="return validateAddRegistration();">
		
			<input type="hidden" name="act" value="<?=($types =='COUNTER'?'counterSetPaymentTerms':'additionalSetPaymentTerms')?>" />
			<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
			<input type="hidden" id="userREGtype" name="userREGtype" value="<?=$userREGtype?>" />
			
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$userDetails['user_full_name']?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$userDetails['user_mobile_isd_code']?> - <?=$userDetails['user_mobile_no']?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$userDetails['user_email_id']?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left"><?=getCutoffName($userDetails['registration_tariff_cutoff_id'])?></td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($userDetails['registration_classification_id'])?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Slip Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Slip Number:</td>
									<td width="30%" align="left"><?=$rowSlip['slip_number']?></td>
									<td width="20%" align="left">Number Of Active Invoice</td>
									<td width="30%" align="left"><?=invoiceCountOfSlip($rowSlip['id'])?></td>
								</tr>
							</table>
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Invoice Details</td>
								</tr>
								<tr class="theader">
									<td width="10%" align="center">Sl No</td>
									<td width="20%" align="left">Invoice Number</td>
									<td width="40%" align="left">Invoice For</td>
									<td width="30%" align="right">Invoice Amount</td>
								</tr>
									<?
									$invoiceDetailsArr = invoiceDetailsOfSlip($rowSlip['id']);
									$counter = 0;
									foreach($invoiceDetailsArr as $key => $invoiceDetails)
									{
										$counter 		 = $counter + 1;
										$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
										
										$type			 = "";
										if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"CONFERENCE");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"RESIDENTIAL");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"WORKSHOP");
										}
										if($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMPANY");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMMODATION");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($invoiceDetails['refference_id']);
											
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"TOUR");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"DINNER");
										}
										
										?>
										<tr class="tlisting">
											<td align="center" valign="top"><?=$counter?></td>
											<td align="left" valign="top"><?=$invoiceDetails['invoice_number']?></td>
											<td align="left" valign="top"><?=$type?></td>
											<td align="right" valign="top">
												<?=$invoiceDetails['currency']?> &nbsp;&nbsp;
												<?=number_format($invoiceDetails['service_roundoff_price'],2)?>
											</td>
										</tr>
									<?
									}
									?>
								<tr>
									<td align="center" valign="top"></td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"><strong>Total Amount</strong></td>
									<td align="right" valign="top">
										<?=$rowSlip['currency']?> 
										&nbsp;&nbsp;
										<?php
										$totalInvoiceAmount = invoiceAmountOfSlip($rowSlip['id']);
										echo number_format($totalInvoiceAmount,2); ?>
										
									</td>
								</tr>
								<?
								if($types =='COUNTER')
								{
								?>
								<tr>
									<td align="center" valign="top"></td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"><strong>Total Amount (With 50% Discount)</strong></td>
									<td align="right" valign="top">
										<?=$rowSlip['currency']?> &nbsp;&nbsp;
										<?php
										 $totalInvoiceAmount2 = (invoiceAmountOfSlip($rowSlip['id'])/2);
										 echo number_format($totalInvoiceAmount2,2);
										 ?>
									</td>
								</tr>
								<?
								}
								?>
							</table>
							<?php
							if($types =='COUNTER')
							{
								$invoiceAmount = $totalInvoiceAmount2;
							}
							else
							{
								$invoiceAmount = $totalInvoiceAmount;
							}
							?>
							<div id="totalInvoiceAmount" style="display:none;"><?=$invoiceAmount?></div>
							<?
							if($totalInvoiceAmount <= 0)
							{
								$style 	 = "style='display:none;'";
							}
							?>
							<div <?=$style?>>
								<?	
									 paymentArea2();
								?>
							</div>
							<table width="100%">
							<br />
								<?
								if($types !='COUNTER' && $totalInvoiceAmount > 0)
								{
								?>
								<tr>
									<td align="left">
										<input type="checkbox" name="fullPayment" id="fullPayment" value="Y" style="width:15px; height:15px;border:2px dotted #00f;" operationMode="fullPayment"/>
									</td>
									<td align="left" valign="top"><font style=" color:#FF0000;margin-left: -87px;;">Please check here if you want to complete the payment process.</font></td>
								</tr>
								<?
								}
								?>
								<tr>
									<td colspan="2" align="left">
										 
										<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="SUBMIT" 
										 class="btn btn-midium btn-blue" style="float:left; margin-left:39%;"  />
										 
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function counterRegistrationSummeryTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		$slipId	 			= $mycms->getSession('SLIP_ID');
		
		$sqlSlip['QUERY']		    = "SELECT * FROM "._DB_SLIP_." WHERE `status` = 'A' AND `id` = '".$slipId."'";
		$resSlip			= $mycms->sql_select($sqlSlip);
		$rowSlip			= $resSlip[0];
		
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		
		?>
		<form name="frmApplyPayment" id="frmApplyPayment"  action="<?=$processPage?>" method="post" onsubmit="return validateWorkshop();">
			<input type="hidden" name="act" value="counterSetPaymentTerms" />
			<input type="hidden" id="slip_id" name="slip_id" value="<?=$slipId?>" />
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left"><?=$userDetails['user_full_name']?></td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left" valign="top">Mobile:</td>
									<td align="left" valign="top"><?=$userDetails['user_mobile_isd_code']?> - <?=$userDetails['user_mobile_no']?></td>
									<td align="left" valign="top">Email Id:</td>
									<td align="left" valign="top"><?=$userDetails['user_email_id']?></td>
								</tr>
								
								<tr>
									<td align="left">Cut Off:</td>
									<td align="left">Counter Registration</td>
									<td align="left">Category:</td>
									<td align="left"><?=getRegClsfName($userDetails['registration_classification_id'])?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Slip Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Slip Number:</td>
									<td width="30%" align="left"><?=$rowSlip['slip_number']?></td>
									<td width="20%" align="left">Number Of Active Invoice</td>
									<td width="30%" align="left"><?=invoiceCountOfSlip($rowSlip['id'])?></td>
								</tr>
							</table>
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Invoice Details</td>
								</tr>
								<tr class="theader">
									<td width="10%" align="center">Sl No</td>
									<td width="20%" align="left">Invoice Number</td>
									<td width="40%" align="left">Invoice For</td>
									<td width="30%" align="right">Invoice Amount</td>
								</tr>
									<?
									$invoiceDetailsArr = invoiceDetailsOfSlip($rowSlip['id']);
									$counter = 0;
									foreach($invoiceDetailsArr as $key => $invoiceDetails)
									{
										$counter 		 = $counter + 1;
										$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);
										
										$type			 = "";
										if($invoiceDetails['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"CONFERENCE");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"RESIDENTIAL");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"WORKSHOP");
										}
										if($invoiceDetails['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMPANY");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"ACCOMMODATION");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($invoiceDetails['refference_id']);
											
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"TOUR");
										}
										if($invoiceDetails['service_type'] == "DELEGATE_DINNER_REQUEST")
										{
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'],$invoiceDetails['refference_id'],"DINNER");
										}
										
										?>
										<tr class="tlisting">
											<td align="center" valign="top"><?=$counter?></td>
											<td align="left" valign="top"><?=$invoiceDetails['invoice_number']?></td>
											<td align="left" valign="top"><?=$type?></td>
											<td align="right" valign="top">
												<?=$invoiceDetails['currency']?> &nbsp;&nbsp;
												<?=$invoiceDetails['service_roundoff_price']?>
											</td>
										</tr>
									<?
									}
									?>
								<tr>
									<td align="center" valign="top"></td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"><strong>Total Amount</strong></td>
									<td align="right" valign="top">
										<?=$rowSlip['currency']?> 
										<?=number_format(invoiceAmountOfSlip($rowSlip['id']),2)?>
									</td>
								</tr>
								<tr>
									<td align="center" valign="top"></td>
									<td align="left" valign="top"></td>
									<td align="left" valign="top"><strong>Total Amount (With 50% Discount)</strong></td>
									<td align="right" valign="top">
										<?=$rowSlip['currency']?> &nbsp;&nbsp;
										<?=number_format((invoiceAmountOfSlip($rowSlip['id'])/2),2)?>
									</td>
								</tr>
							</table>
							<?
								paymentArea();
							?>
							
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
										 
										<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Register" 
										 class="btn btn-midium btn-blue" style="float:left; margin-left:39%;"  />
										 
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function paymentArea()
	{
		global $cfg, $mycms;
?>
		<script>
				function paymentModeRetriver(type)
				{
					var paymentType = type;
					if(paymentType == "Cash")
					{
						$("#cashPaymentDiv").css("display","block");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","none");
						$("#cardPaymentDiv").css("display","none");
					}
					
					if(paymentType == "Cheque")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","block");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","none");
						$("#cardPaymentDiv").css("display","none");
					}
					
					if(paymentType == "Draft")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","block");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","none");
						$("#cardPaymentDiv").css("display","none");
					}
					
					if(paymentType == "NEFT")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","block");
						$("#rtgsPaymentDiv").css("display","none");
						$("#cardPaymentDiv").css("display","none");
					}
					
					if(paymentType == "RTGS")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","block");
						$("#cardPaymentDiv").css("display","none");
					}
					if(paymentType == "CARD")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","none");
						$("#cardPaymentDiv").css("display","block");																		
					}
				}
			</script>
		<script>
		 $( function() {
			$("input[rel=tcal]").datepicker({
				 maxDate: new Date(),
				 changeMonth: true,
				 changeYear: true,
				 yearRange: "c-100:c",
				 dateFormat: "yy-mm-dd"
			});
		 });
		 
		</script>
		<table width="100%">
			<tr>
				<td align="left" colspan="4" class="thighlight">Discount</td>
			</tr>
			<tr>
				<td align="left" colspan="4" valign="top">
					<input type="checkbox" id="discount" name="discount" value="INR" operationMode="discountCheckbox" style="width: 18px; height: 18px;" /> Give Discount
				</td>			
			</tr>
			<tr style="display:none;" operationMode="discount">
				<td>Discount Amount</td>
				<td valign="top" colspan="3" ><input type="text" id="discountAmount" name="discountAmount" operationMode="discountAmount" style="width:32%;"/></td>
			</tr>
			<tr class="thighlight">
				<td colspan="4" align="left">Payment Record</td>
			</tr>								
			<tr>
				<td width="20%" align="left">Payment Mode</td>
				<td width="30%" align="left">
					<select name="payment_mode" id="payment_mode" style="width:95%;" 
					 onchange="paymentModeRetriver(this.value)" use="payment_mode">
						<option value="Cash" selected="selected">Cash</option>
						<option value="Cheque">Cheque</option>
						<option value="Draft">Draft</option>
						<option value="NEFT">NEFT</option>
						<option value="RTGS">RTGS</option>
						<option value="CARD">CARD</option>
					</select>
				</td>
				<td width="20%" align="left"></td>
				<td width="30%" align="left"></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">
					
					<div id="cashPaymentDiv">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Date of Deposit</td>
								<td width="30%" align="left">
									<input type="text" name="cash_deposit_date" id="cash_deposit_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use="cash_deposit_date" />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="chequePaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Cheque No</td>
								<td width="30%" align="left">
									<input type="text" name="cheque_number" id="cheque_number" 
									 style="width:90%; text-transform:uppercase;" use="cheque_number" />
								</td>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="cheque_drawn_bank" id="cheque_drawn_bank" 
									 style="width:90%; text-transform:uppercase;" use='cheque_drawn_bank'/>
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Cheque Date</td>
								<td width="30%" align="left">
									<input type="text" name="cheque_date" id="cheque_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='cheque_date' />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="draftPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Draft No</td>
								<td width="30%" align="left">
									<input type="text" name="draft_number" id="draft_number" 
									 style="width:90%; text-transform:uppercase;" use='draft_number' />
								</td>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="draft_drawn_bank" id="draft_drawn_bank" 
									 style="width:90%; text-transform:uppercase;" use="draft_drawn_bank" />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Draft Date</td>
								<td width="30%" align="left">
									<input type="text" name="draft_date" id="draft_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='draft_date' />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="neftPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="neft_bank_name" id="neft_bank_name" 
									 style="width:90%; text-transform:uppercase;" use='neft_bank_name' />
								</td>
								<td align="left">Transaction Id</td>
								<td align="left">
									<input type="text" name="neft_transaction_no" id="neft_transaction_no" 
									 style="width:90%; text-transform:uppercase;" use='neft_transaction_no' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date</td>
								<td width="30%" align="left">
									<input type="text" name="neft_date" id="neft_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='neft_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="cardPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Card Number</td>
								<td width="30%" align="left">
									<input type="number" name="card_number" id="card_number" 
									 style="width:90%; text-transform:uppercase;" use='card_number' />
								</td>
								<td align="left">Remarks</td>
								<td align="left">
									<input type="text" name="remarks" id="remarks" 
									 style="width:90%; text-transform:uppercase;" use='remarks' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date</td>
								<td width="30%" align="left">
									<input type="text" name="card_date" id="card_date" 
									 style="width:90%; text-transform:uppercase;"  rel="tcal" value="<?=date('Y-m-d')?>" use='card_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="rtgsPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="rtgs_bank_name" id="rtgs_bank_name" 
									 style="width:90%; text-transform:uppercase;" use='rtgs_bank_name' />
								</td>
								<td align="left">Transaction Id</td>
								<td align="left">
									<input type="text" name="rtgs_transaction_no" id="rtgs_transaction_no" 
									 style="width:90%; text-transform:uppercase;" use='rtgs_transaction_no' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date</td>
								<td width="30%" align="left">
									<input type="text" name="rtgs_date" id="rtgs_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='rtgs_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
				</td>
			</tr>
		</table>
		<?
	}
	
	function paymentArea2()
	{
		global $cfg, $mycms;
	?>
		<script>
			function paymentModeRetriver(type)
			{
				var paymentType = type;
				if(paymentType == "Cash")
				{
					$("#cashPaymentDiv").css("display","block");
					$("#chequePaymentDiv").css("display","none");
					$("#draftPaymentDiv").css("display","none");
					$("#neftPaymentDiv").css("display","none");						
					$("#rtgsPaymentDiv").css("display","none");
					$("#cardPaymentDiv").css("display","none");
					$("#creditPaymentDiv").css("display","none");
				}
				
				if(paymentType == "Cheque")
				{
					$("#cashPaymentDiv").css("display","none");
					$("#chequePaymentDiv").css("display","block");
					$("#draftPaymentDiv").css("display","none");
					$("#neftPaymentDiv").css("display","none");						
					$("#rtgsPaymentDiv").css("display","none");
					$("#cardPaymentDiv").css("display","none");
					$("#creditPaymentDiv").css("display","none");
				}
				
				if(paymentType == "Draft")
				{
					$("#cashPaymentDiv").css("display","none");
					$("#chequePaymentDiv").css("display","none");
					$("#draftPaymentDiv").css("display","block");
					$("#neftPaymentDiv").css("display","none");						
					$("#rtgsPaymentDiv").css("display","none");
					$("#cardPaymentDiv").css("display","none");
					$("#creditPaymentDiv").css("display","none");
				}
				
				if(paymentType == "NEFT")
				{
					$("#cashPaymentDiv").css("display","none");
					$("#chequePaymentDiv").css("display","none");
					$("#draftPaymentDiv").css("display","none");
					$("#neftPaymentDiv").css("display","block");
					$("#rtgsPaymentDiv").css("display","none");
					$("#cardPaymentDiv").css("display","none");
					$("#creditPaymentDiv").css("display","none");
					
				}
				
				if(paymentType == "RTGS")
				{
					$("#cashPaymentDiv").css("display","none");
					$("#chequePaymentDiv").css("display","none");
					$("#draftPaymentDiv").css("display","none");
					$("#neftPaymentDiv").css("display","none");						
					$("#rtgsPaymentDiv").css("display","block");
					$("#cardPaymentDiv").css("display","none");
					$("#creditPaymentDiv").css("display","none");
				}
				if(paymentType == "CARD")
				{
					$("#cashPaymentDiv").css("display","none");
					$("#chequePaymentDiv").css("display","none");
					$("#draftPaymentDiv").css("display","none");
					$("#neftPaymentDiv").css("display","none");
					$("#rtgsPaymentDiv").css("display","none");
					$("#cardPaymentDiv").css("display","block");
					$("#creditPaymentDiv").css("display","none");												
				}
				
				if(paymentType == "Credit")
				{
					$("#cashPaymentDiv").css("display","none");
					$("#chequePaymentDiv").css("display","none");
					$("#draftPaymentDiv").css("display","none");
					$("#neftPaymentDiv").css("display","none");
					$("#rtgsPaymentDiv").css("display","none");
					$("#cardPaymentDiv").css("display","none");
					$("#creditPaymentDiv").css("display","block");
					$("#exhibitorBalMsg").hide();
					$("#exhibitorRemainBal").hide();
					$("#exhibitorTotalRemainBalMsg").hide();
					$("#exhibitorTotalRemainBal").hide();
				}
			}
			
		</script>
		<script>
		 $( function() {
			$("input[rel=tcal]").datepicker({
				 maxDate: new Date(),
				 changeMonth: true,
				 changeYear: true,
				 yearRange: "c-100:c",
				 dateFormat: "yy-mm-dd"
			});
		 });
		</script>
		<table width="100%">
		<!--	<tr>
				<td align="left" colspan="4" class="thighlight">Discount</td>
			</tr>
			<tr>
				<td align="left" colspan="4" valign="top">
					<input type="checkbox" id="discount" name="discount" value="INR" operationMode="discountCheckbox" style="width: 18px; height: 18px;"/> Give Discount
				</td>			
			</tr>
			<tr style="display:none;" operationMode="discount">
				<td>Discount Amount</td>
				<td valign="top" colspan="3" ><input type="text" id="discountAmount" name="discountAmount" operationMode="discountAmount" style="width:32%;"/></td>
			</tr>-->
			
			<tr class="thighlight">
				<td colspan="4" align="left">Payment Record</td>
			</tr>								
			<tr>
				<td width="20%" align="left">Payment Mode <span class="mandatory">*</span></td>
				<td width="30%" align="left">
					<select name="payment_mode" id="payment_mode" style="width:95%;" 
					 onchange="paymentModeRetriver(this.value)" use="payment_mode">
						<option value="Cash" selected="selected">Cash</option>
						<option value="Cheque">Cheque</option>
						<option value="Draft">Draft</option>
						<option value="NEFT">NEFT</option>
						<option value="RTGS">RTGS</option>
						<option value="CARD">CARD</option>
						<option value="Credit">EXHIBITOR CREDIT</option>
					</select>
				</td>
				<td width="20%" align="left"></td>
				<td width="30%" align="left"></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">
					
					<div id="cashPaymentDiv">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Date of Deposit <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="cash_deposit_date" id="cash_deposit_date" 
									 style="width:90%; text-transform:uppercase;" value="<?=date('Y-m-d')?>" use="cash_deposit_date" />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="chequePaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Cheque No <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="number" name="cheque_number" id="cheque_number" 
									 style="width:90%; text-transform:uppercase;" use="cheque_number" />
								</td>
								<td width="20%" align="left">Drawee Bank <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="text" name="cheque_drawn_bank" id="cheque_drawn_bank" 
									 style="width:90%; text-transform:uppercase;" use='cheque_drawn_bank'/>
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Cheque Date <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="cheque_date" id="cheque_date" 
									 style="width:90%; text-transform:uppercase;"  value="<?=date('Y-m-d')?>" use='cheque_date' />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="draftPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Draft No <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="text" name="draft_number" id="draft_number" 
									 style="width:90%; text-transform:uppercase;" use='draft_number' />
								</td>
								<td width="20%" align="left">Drawn Bank <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="text" name="draft_drawn_bank" id="draft_drawn_bank" 
									 style="width:90%; text-transform:uppercase;" use="draft_drawn_bank" />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Draft Date <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="draft_date" id="draft_date" 
									 style="width:90%; text-transform:uppercase;" value="<?=date('Y-m-d')?>" use='draft_date' />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="neftPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Drawee Bank <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="text" name="neft_bank_name" id="neft_bank_name" 
									 style="width:90%; text-transform:uppercase;" use='neft_bank_name' />
								</td>
								<td align="left">Transaction Id <span class="mandatory">*</span></td>
								<td align="left">
									<input type="text" name="neft_transaction_no" id="neft_transaction_no" 
									 style="width:90%; text-transform:uppercase;" use='neft_transaction_no' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="neft_date" id="neft_date" 
									 style="width:90%; text-transform:uppercase;"  value="<?=date('Y-m-d')?>" use='neft_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="rtgsPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Drawee Bank <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="text" name="rtgs_bank_name" id="rtgs_bank_name" 
									 style="width:90%; text-transform:uppercase;" use='rtgs_bank_name' />
								</td>
								<td align="left">Transaction Id <span class="mandatory">*</span></td>
								<td align="left">
									<input type="text" name="rtgs_transaction_no" id="rtgs_transaction_no" 
									 style="width:90%; text-transform:uppercase;" use='rtgs_transaction_no' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="rtgs_date" id="rtgs_date" 
									 style="width:90%; text-transform:uppercase;"  value="<?=date('Y-m-d')?>" use='rtgs_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="cardPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Card Number <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="number" name="card_number" id="card_number" 
									 style="width:90%; text-transform:uppercase;" use='card_number' />
								</td>
								<td align="left">Remarks</td>
								<td align="left">
									<input type="text" name="remarks" id="remarks" 
									 style="width:90%; text-transform:uppercase;" use='remarks' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="card_date" id="card_date" 
									 style="width:90%; text-transform:uppercase;"   value="<?=date('Y-m-d')?>" use='card_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="creditPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">						
							<tr>
								<td width="20%" align="left">Credit Date <span class="mandatory">*</span></td>
								<td width="30%" align="left">
									<input type="date" name="credit_date" id="credit_date" 
									 style="width:90%; text-transform:uppercase;"  value="<?=date('Y-m-d')?>" use='credit_date' />
								</td>						
								<td align="left" rowspan="2"><div id="exhibitorTotalRemainBal" style="border:thin solid #FF0000; color:#FF0000; text-align:center; height:50px; width:100%;"></div></td>						
							</tr>							
												
						</table>
					</div>
					
				</td>
			</tr>
		</table>
		<?
	}	
		
	function cancelpaymentArea()
	{
		global $cfg, $mycms;
		?>
		<table width="100%">
			<script>
				function paymentModeRetriver(type)
				{
					var paymentType = type;
					if(paymentType == "Cash")
					{
						$("#cashPaymentDiv").css("display","block");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","none");
					}
					
					if(paymentType == "Cheque")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","block");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","none");
					}
					
					if(paymentType == "NEFT")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","block");
						$("#rtgsPaymentDiv").css("display","none");
					}
					
					if(paymentType == "RTGS")
					{
						$("#cashPaymentDiv").css("display","none");
						$("#chequePaymentDiv").css("display","none");
						$("#draftPaymentDiv").css("display","none");
						$("#neftPaymentDiv").css("display","none");
						$("#rtgsPaymentDiv").css("display","block");
					}
				}
			</script>
			<script>
			 $( function() {
				$("input[rel=tcal]").datepicker({
					 maxDate: new Date(),
					 changeMonth: true,
					 changeYear: true,
					 yearRange: "c-100:c",
					 dateFormat: "yy-mm-dd"
				});
			 });
		    </script>
			<tr class="thighlight">
				<td colspan="4" align="left">Payment Record</td>
			</tr>								
			<tr>
				<td width="20%" align="left">Payment Mode</td>
				<td width="30%" align="left">
					<select name="payment_mode" id="payment_mode" style="width:95%;" 
					 onchange="paymentModeRetriver(this.value)" use="payment_mode">
						<option value="Cash" selected="selected">Cash</option>
						<option value="Cheque">Cheque</option>
						<option value="NEFT">NEFT</option>
						<option value="RTGS">RTGS</option>
						
					</select>
				</td>
				<td width="20%" align="left"></td>
				<td width="30%" align="left"></td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">
					
					<div id="cashPaymentDiv">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Date of Deposit</td>
								<td width="30%" align="left">
									<input type="text" name="cash_deposit_date" id="cash_deposit_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use="cash_deposit_date" />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="chequePaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Cheque No</td>
								<td width="30%" align="left">
									<input type="text" name="cheque_number" id="cheque_number" 
									 style="width:90%; text-transform:uppercase;" use="cheque_number" />
								</td>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="cheque_drawn_bank" id="cheque_drawn_bank" 
									 style="width:90%; text-transform:uppercase;" use='cheque_drawn_bank'/>
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Cheque Date</td>
								<td width="30%" align="left">
									<input type="text" name="cheque_date" id="cheque_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='cheque_date' />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="draftPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Draft No</td>
								<td width="30%" align="left">
									<input type="text" name="draft_number" id="draft_number" 
									 style="width:90%; text-transform:uppercase;" use='draft_number' />
								</td>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="draft_drawn_bank" id="draft_drawn_bank" 
									 style="width:90%; text-transform:uppercase;" use="draft_drawn_bank" />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Draft Date</td>
								<td width="30%" align="left">
									<input type="text" name="draft_date" id="draft_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='draft_date' />
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="neftPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="neft_bank_name" id="neft_bank_name" 
									 style="width:90%; text-transform:uppercase;" use='neft_bank_name' />
								</td>
								<td align="left">Transaction Id</td>
								<td align="left">
									<input type="text" name="neft_transaction_no" id="neft_transaction_no" 
									 style="width:90%; text-transform:uppercase;" use='neft_transaction_no' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date</td>
								<td width="30%" align="left">
									<input type="text" name="neft_date" id="neft_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='neft_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="rtgsPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Drawee Bank</td>
								<td width="30%" align="left">
									<input type="text" name="rtgs_bank_name" id="rtgs_bank_name" 
									 style="width:90%; text-transform:uppercase;" use='rtgs_bank_name' />
								</td>
								<td align="left">Transaction Id</td>
								<td align="left">
									<input type="text" name="rtgs_transaction_no" id="rtgs_transaction_no" 
									 style="width:90%; text-transform:uppercase;" use='rtgs_transaction_no' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date</td>
								<td width="30%" align="left">
									<input type="text" name="rtgs_date" id="rtgs_date" 
									 style="width:90%; text-transform:uppercase;" rel="tcal" value="<?=date('Y-m-d')?>" use='rtgs_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
					<div id="cardPaymentDiv" style="display:none;">
						<table width="100%" class="noborder">
							<tr>
								<td width="20%" align="left">Card Number</td>
								<td width="30%" align="left">
									<input type="number" name="number" id="card_number" 
									 style="width:90%; text-transform:uppercase;" use='card_number' />
								</td>
								<td align="left">Remarks</td>
								<td align="left">
									<input type="text" name="remarks" id="remarks" 
									 style="width:90%; text-transform:uppercase;" use='remarks' />
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Date</td>
								<td width="30%" align="left">
									<input type="text" name="card_date" id="card_date" 
									 style="width:90%; text-transform:uppercase;"  rel="tcal" value="<?=date('Y-m-d')?>" use='rtgs_date' />
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
						</table>
					</div>
					
				</td>
			</tr>
		</table>
		<?
	}
	
	function viewDelegateRegistrationDetailsView($delegateId,$maxHight)
	{
		include_once("../../includes/function.accommodation.php");
		include_once("../../includes/function.workshop.php");
		include_once("../../includes/function.invoice.php");
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.registration.php');
		
		global $cfg, $mycms;
		
		
		$rowFetchUser           =  getUserDetails1($delegateId); 
		
		$loggedUserId			= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser = array();
		$sqlSystemUser['QUERY']      	= "SELECT * FROM "._DB_CONF_USER_." 
											  WHERE `a_id` = ? ";
		
		$sqlSystemUser['PARAM'][]   = array('FILD' => 'a_id', 'DATA' =>$loggedUserId,     'TYP' => 's');
									   
		$resultSystemUser   	= $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      	= $resultSystemUser[0];
	?>
		
		<div>
			<table width="100%" class="tborder">
				<tr>
					<td class="tcat">
						<span style="float:left">Profile</span>
						<span class="close" onclick="closeProfileDetailsPopUp()" use="closeThisPopup"><strong>X</strong></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ScrollStyle" id="popup_profile_details" style="overflow-y: scroll; max-height:<?=$maxHight?>px;">
		<table width="100%" align="center" class="tborder"> 
			 
			<tbody>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_full_name'])?> 
									
								</td>
								<td align="left" width="20%">Member Type:</td>
								<td align="left" width="30%"><?=$rowFetchUser['classification_title']?></td>
							</tr>
							<tr>
								<td align="left">Registration Id:</td>
								<td align="left">
								<?php
								if($rowFetchUser['registration_payment_status']!="UNPAID")
								{
									echo $rowFetchUser['user_registration_id'];
								}
								else
								{
									echo "-";
								}
								?>
								</td>
								<td align="left">Unique Sequence:</td>
								<td align="left">
								<?	
								if($rowFetchUser['registration_payment_status']=="PAID" 
								   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
								   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
									{
										echo strtoupper($rowFetchUser['user_unique_sequence']);
										echo "<br />";
									}
									else
									{
										echo "-";
									}
								?>
								</td>
							</tr>
							<tr>
								<td width="20%" align="left">Email Id:</td>
								<td width="30%" align="left"><?=$rowFetchUser['user_email_id']?></td>
								<td align="left">Mobile:</td>
								<td align="left"><?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?></td>
							</tr>
							<tr>
								<td align="left">Tags:</td>
								<td align="left"><?=$rowFetchUser['tags']?></td>
								<td align="left">Registration Date:</td>
								<td align="left"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?></td>
							</tr>
							
							<tr>
								<td align="left">Country:</td>
								<td align="left">
									<?=$rowFetchUser['country_name']?> 
								</td>
								<td align="left">State:</td>
								<td align="left"><?=$rowFetchUser['state_name']?> </td>
							</tr>
							<tr>
								<td align="left">Address:</td>
								<td align="left">
									<?=$rowFetchUser['user_address']?> 
								</td>
								<td align="left">Pin Code:</td>
								<td align="left"><?=$rowFetchUser['user_pincode']?> </td>
							</tr>
							<tr>
								<td align="left">City:</td>
								<td align="left">
									<?=$rowFetchUser['user_city_id']?> 
								</td>
								<?
								$image = $cfg['BASE_URL'].$cfg['USER.ID.CARD'].$rowFetchUser['user_document'];
								
								?>
								<td></td>
								<td></td>
							</tr>
							<?php
								if($rowFetchUser['user_food_preference']!='')
								{
								?>
							<tr>
								<td align="left">Food preference:</td>
								<td align="left">
									<?=$rowFetchUser['user_food_preference']=='NON_VEG'?'Non veg':'Veg'?> 
								</td>
								<td align="left"></td>
								<td align="left"></td>
							</tr>
								<?
								}
								?>
						</table>
						
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">Registration Tariff</td>
							</tr>
							<tr>
								<td width="20%" align="left" valign="top">Tariff Classification:</td>
								<td width="30%" align="left" valign="top"><?=getRegClsfName($rowFetchUser['registration_classification_id'])?></td>
								<td width="20%" align="left" valign="top">Tariff Cut Off:</td>
								<td width="30%" align="left" valign="top"><?=getCutoffName($rowFetchUser['registration_tariff_cutoff_id'])?></td>
							</tr>
						</table>
						
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="3" align="left">Workshop Registration Details</td>
							</tr>
							<?
							
							$resultWorkshopDtls 	= getWorkshopDetailsOfDelegate($delegateId);
							//echo "<pre>"; print_r($resultWorkshopDtls); echo "<pre>";
							if($resultWorkshopDtls)
							{
								
							?>
							<tr class="theader">
								<td align="left">Workshop Name</td>
								<td align="left" width="400" >Booking Cut-off</td>
								<td align="center" width="150">Payment Status</td>
							</tr>	
							<?
								$cutoff = "";
								foreach($resultWorkshopDtls as $keyWorkshopDtls=>$rowWorkshopDtls)
								{
									if($theWorkshopInvoiceNo = slipDetailsOfUser($delegateId))
									{
									//echo '<pre>';print_r($theWorkshopInvoiceNo);echo '</pre>';
							?>
								<tr>
									<td align="left"><?=getWorkshopName($rowWorkshopDtls['workshop_id'])?></td>
									<td align="left"><?=getCutoffName($rowWorkshopDtls['tariff_cutoff_id'])?></td>
									<td align="center">
										<?
									}
										//echo '<pre>';print_r(getWorkshopName($rowWorkshopDtls['workshop_id']));echo '</pre>';die('kk');
										if($rowWorkshopDtls['payment_status'] == 'UNPAID')
										{
											echo '<span class="unpaidStatus">UNPAID</span>';
										}
										else if($rowWorkshopDtls['payment_status'] == 'PAID')
										{
											echo '<span class="paidStatus">PAID</span>';
										}
										else if($rowWorkshopDtls['payment_status'] == 'ZERO_VALUE')
										{
											echo '<span class="paidStatus">ZERO VALUE</span>';
										}
										?>
									</td>
								</tr>
								<?
								}
							}
							else
							{
							?>
							<tr>
								<td colspan="3" align="center">
									<span class="mandatory">No Record Present.</span>
								</td>
							</tr>
							<?
							}
						?>
						</table>
						
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="3" align="left">Accommodation Details</td>
							</tr>
							<?
							
							$resultAccommodationDtls 	= accmodation_details($delegateId);
							//echo "<pre>"; print_r($resultWorkshopDtls); echo "<pre>";
							if($resultAccommodationDtls)
							{
								
							?>
							<tr class="theader">
								<td align="left">Hotel Name</td>
								<td align="left" width="400" >Checkin</td>
								<td align="center" width="150">Checkout</td>
							</tr>	
							<tr>
								<td align="left"><?=$resultAccommodationDtls['HOTEL_NAME']?></td>
								<td align="left"><?=$resultAccommodationDtls['CHECKIN_DATE']?></td>
								<td align="left"><?=$resultAccommodationDtls['CHECKOUT_DATE']?></td>
							</tr>	
							<?
							}
							else
							{
							?>
							<tr>
								<td colspan="3" align="center">
									<span class="mandatory">No Record Present.</span>
								</td>
							</tr>
							<?
							}
						?>
						</table>
						
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="6" align="left">Accompany Registration Details</td>
							</tr>
							<?
							$resultAccompanyDtls 	= getAcompanyDetailsOfDelegate($delegateId);
							//echo "<pre>"; print_r($resultAccommodationDtls); echo "<pre>";
							if($resultAccompanyDtls)
							{
								
							?>
							<tr class="theader">
								<td align="left">Accompany Name</td>
								<td align="center" width="150" >Accompany Registration Id</td>
								<td align="center" width="200" >Cutoff</td>
								<td align="center" width="150">Payment Status</td>
							</tr>	
							<?
								$cutoff = "";
								foreach($resultAccompanyDtls as $keyAccompanyDtls=>$rowAccompanyDtls)
								{
								?>
								<tr>
									<td align="left"><?=$rowAccompanyDtls['user_full_name']?></td>
									<td align="center">
										<?
										if($rowWorkshopDtls['registration_payment_status'] != 'UNPAID')
										{
											echo $rowAccompanyDtls['user_registration_id'];
										}
										else
										{
											echo '-';
										}
										?>
									</td>
									<td align="center"><?=getCutoffName($rowAccompanyDtls['registration_tariff_cutoff_id'])?></td>
									<td align="center">
										<?
										if($rowAccompanyDtls['registration_payment_status'] == 'UNPAID')
										{
											echo '<span class="unpaidStatus">UNPAID</span>';
										}
										else if($rowAccompanyDtls['registration_payment_status'] == 'PAID')
										{
											echo '<span class="paidStatus">PAID</span>';
										}
										else if($rowAccompanyDtls['registration_payment_status'] == 'ZERO_VALUE')
										{
											echo '<span class="paidStatus">ZERO VALUE</span>';
										}
										?>
									</td>
								</tr>
								<?
								}
							}
							else
							{
							?>
							<tr>
								<td colspan="6" align="center">
									<span class="mandatory">No Record Present.</span>
								</td>
							</tr>
							<?
							}
						?>
						</table>
						
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="7" align="left">Pay Slip Details</td>
							</tr>
							<?php
							$paymentCounter   = 0;
							$resFetchSlip	  = slipDetailsOfUser($delegateId);	
							
							if($resFetchSlip)
							{
							?>
								<tr class="theader">
									<td width="50" align="center">Sl No</td>
									<td width="150" align="left">PV Number</td>
									<td width="100" align="center">Slip Date</td>
									<td width="100" align="center">Payment Mode</td>
									<td width="100" align="right">Slip Amt.</td>
									<td width="100" align="right">Paid Amt.</td>
									<td align="center">Payment Status</td>
								</tr>
								<?
								$styleCss = 'style="display:none;"';
								foreach($resFetchSlip as $key=>$rowFetchSlip)
								{
									$counter++;
									$resPaymentDetails      = paymentDetails($rowFetchSlip['id']);
									
									$paymentDescription     = "-";
									if($resPaymentDetails['payment_mode']=="Cash")
									{
										$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>".setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Online")
									{
										$paymentDescription = "Paid by <b>Online</b>. Date of Payment: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.<br>
																Transaction Number: <b>".$resPaymentDetails['atom_atom_transaction_id']."</b>.<br>
																Bank Transaction Number: <b>".$resPaymentDetails['atom_bank_transaction_id']."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Card")
									{
										$paymentDescription = "Paid by <b>Card</b>. Reference Number: <b>".$resPaymentDetails['card_transaction_no']."</b>.<br>
																Date of Payment: <b>".setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Draft")
									{
										$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>".$resPaymentDetails['draft_number']."</b>.<br>
															   Draft Date: <b>".setDateTimeFormat2($resPaymentDetails['draft_date'], "D")."</b>.
															   Draft Drawee Bank: <b>".$resPaymentDetails['draft_bank_name']."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="NEFT")
									{
										$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>".$resPaymentDetails['neft_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['neft_date'], "D")."</b>.
															   Transaction Bank: <b>".$resPaymentDetails['neft_bank_name']."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="RTGS")
									{
										$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>".$resPaymentDetails['rtgs_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D")."</b>.
															   Transaction Bank: <b>".$resPaymentDetails['rtgs_bank_name']."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Cheque")
									{
										$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>".$resPaymentDetails['cheque_number']."</b>.<br>
															   Cheque Date: <b>".setDateTimeFormat2($resPaymentDetails['cheque_date'], "D")."</b>.
															   Cheque Drawee Bank: <b>".$resPaymentDetails['cheque_bank_name']."</b>.";
									}
									
									if($resPaymentDetails['payment_mode']=="Card")
									{
										$paymentDescription = "Paid by <b>Card</b>. Card Number: <b>".$resPaymentDetails['card_transaction_no']."</b>.<br>
															   Card Payment Date: <b>".setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D")."</b>.
															   Card Remark: <b>".$resPaymentDetails['aaa']."</b>.";
									}
									
									if($resPaymentDetails['payment_mode']=="Credit")
									{
										$sqlExhibitorName['QUERY']	=	"SELECT `exhibitor_company_name` FROM "._DB_EXIBITOR_COMPANY_." 
																	WHERE `exhibitor_company_code` = '".$resPaymentDetails['exhibitor_code']."' ";
													
										$exhibitorName		=	$mycms->sql_select($sqlExhibitorName, false);
										
										$paymentDescription = "Paid by <b>Credit</b>. Exhibitor Code: <b>".$resPaymentDetails['exhibitor_code']."</b>.<br>
															   Credit Payment Date: <b>".setDateTimeFormat2($resPaymentDetails['credit_date'], "D")."</b>.<br>
															   Exhibitor Name: <b>".$exhibitorName[0]['exhibitor_company_name']."</b>.";
									}										
									
									?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$counter?></td>
										<td align="left" valign="top"><?=$rowFetchSlip['slip_number']?></td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchSlip['slip_date'], "D")?></td>
										<td align="center" valign="top"><?=$rowFetchSlip['invoice_mode']?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <? $amount = invoiceAmountOfSlip($rowFetchSlip['id']);
																		  echo number_format($amount,2); ?></td>
										<td align="right" valign="top">
										<?
										
										if($resPaymentDetails['payment_status'] == "PAID")
										{
											echo number_format($resPaymentDetails['amount'],2);
										}
										else
										{
											echo "0.00";
										}
										?>
										</td>
										<td align="center" valign="top">
										<?
										if($rowFetchSlip['payment_status']=="COMPLIMENTARY")
										{
										?>
											<span style="color:#5E8A26;"><strong>Complimentary</strong></span>
										<?
										}
										else
										{
											if($resPaymentDetails['payment_status'] == "UNPAID")
											{
												if($resPaymentDetails['status']=="D")
												{
													echo "<span style='color:red;'>Payment Terms Not Set</span>";
												}
												else
												{
													echo "<span style='color:red;'>Unpaid</span>";
												}
											}
											else if($resPaymentDetails['payment_status'] == "PAID")
											{
												echo $paymentDescription;
											}
											else
											{
												echo "<span style='color:red;'>Payment Terms Not Set</span>";
											}
										}
										?>
										
										</td>
									</tr>
									<tr id="invoiceDetails<?=$rowFetchSlip['id']?>">
										<td colspan="7"  style="border:1px dashed #D47326;">
											<table width="100%">
												<tr class="theader">
													<td width="30" align="center">Sl No</td>
													<td align="left"  width="120">Invoice No</td>
													<td width="80" align="center">Invoice Mode</td>
													
													<td align="center">Invoice For</td>
													<td width="70" align="center">Invoice Date</td>
													<td width="110" align="right">Invoice Amount</td>
													<td width="100" align="center">Payment Status</td>
												</tr>
												<?php
												
												$invoiceCounter                 = 0;
												$sqlFetchInvoice                = getRegistrationInvoiceCancelInvoiceDetails("","",$rowFetchSlip['id']);
																				
												$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
												
												$ffff = getInvoice($rowFetchSlip['id']);
												//echo "<pre>";print_r($ffff);echo "</pre>";
												if($resultFetchInvoice)
												{
													foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
													{
														$returnArray = discountAmount($rowFetchInvoice['id']);
														$percentage  = $returnArray['PERCENTAGE'];
														$totalAmount = $returnArray['TOTAL_AMOUNT'];
														$discountAmount = $returnArray['DISCOUNT'];
														
														$invoiceCounter++;
														$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
														$type			 = "";
														if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"CONFERENCE");
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"RESIDENTIAL");
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
														{
															$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"WORKSHOP");
														}
														if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMPANY");
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMMODATION");
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
														{
															$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
															
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"TOUR");
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER");
														}
														$styleColor = 'background: rgb(204, 229, 204);';
														$isCancel	= 'NO';
														if($rowFetchInvoice['status'] =='C')
														{
															$styleColor = 'background: rgb(255, 204, 204);';
															$isCancel	= 'YES';
															$type = "Invoice Cancelled";
														}
													?>
														<tr class="tlisting">
															<td align="center"><?=$invoiceCounter?></td>
															<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
															<td align="center"><?=$rowFetchInvoice['invoice_mode']?></td>
															<td align="left">
																<?=$type?>
															</td>
															<td align="center"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
															<td align="right">
															<?=$rowFetchInvoice['currency']?> <?=number_format($totalAmount,2)?>
															</td>
															
															
															<td align="center">
																<?php
																if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
																{
																?>
																	<span style="color:#5E8A26;">Complimentary</span>
																<?php
																}
																elseif($rowFetchInvoice['payment_status']=="ZERO_VALUE")
																{
																?>
																	<span style="color:#009900;">Zero Value	</span>
																<?php
																}
																else if($rowFetchInvoice['payment_status']=="PAID")
																{
																?>
																	<span style="color:#5E8A26;">Paid</span>
																<?php		
																}
																else if($rowFetchInvoice['payment_status']=="UNPAID")
																{
																?>
																	<span style="color:#C70505;">UNPAID</span>
																<?php		
																}
																?>
															</td>
														</tr>
												<?php
													}
												}
												?>
											</table>
										</td>
									</tr>
							<?php
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="7" align="center"><span class="mandatory">No Record Present.</span></td>
								</tr>
							<?php
							}
							?>
						</table>
						
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="9" align="left"> 
								<?=ucwords(strtolower($rowFetchUser['user_full_name']))?> Invoice
								</td>
							</tr>
							<tr class="theader"  use="all">
								<td width="30" align="center">Sl No</td>
								<td align="left"  width="120">Invoice No</td>
								<td align="left"  width="120">PV No</td>
								<td width="80" align="center">Invoice Mode</td>
								<td align="center">Invoice For</td>
								<td width="70" align="center">Invoice Date</td>
								<td width="110" align="right">Invoice Amount</td>
								<td width="100" align="center">Payment Status</td>
							</tr>
							<?php
							$invoiceCounter                 = 0;
							$sqlFetchInvoice                = getRegistrationInvoiceCancelInvoiceDetails("",$delegateId,"");
															
							$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
							
							
							if($resultFetchInvoice)
							{
								foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
								{
									$invoiceCounter++;
									$slip = getInvoice($rowFetchInvoice['slip_id']);
									$returnArray = discountAmount($rowFetchInvoice['id']);
									$percentage  = $returnArray['PERCENTAGE'];
									$totalAmount = $returnArray['TOTAL_AMOUNT'];
									$discountAmount = $returnArray['DISCOUNT'];
									
									
									
									$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
									$type			 = "";
									if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
									{
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"CONFERENCE");
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
									{
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"RESIDENTIAL");
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
									{
										$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"WORKSHOP");
									}
									if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
									{
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMPANY");
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
									{
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMMODATION");
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
									{
										$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
										
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"TOUR");
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
									{
										$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER");
									}
									
									$styleColor = 'background: rgb(204, 229, 204);';
									$isCancel	= 'NO';
									if($rowFetchInvoice['status'] =='C')
									{
										$styleColor = 'background: rgb(255, 204, 204);';
										$isCancel	= 'YES';
										$type = "Invoice Cancelled";
									}
							?>
									<tr class="tlisting" use="all" style=" <?=$styleColor?>">
										<td align="center"><?=$invoiceCounter?></td>
										<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
										<td align="left"><?=$slip['slip_number']?></td>
										<td align="center"><?=$rowFetchInvoice['invoice_mode']?></td>
										<td align="left"><?=$type?></td>
										<td align="center"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
										<td align="right">
										<?=$rowFetchInvoice['currency']?> <?=number_format($totalAmount,2)?>
										</td>
										<td align="center">
											<?php
											if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
											{
											?>
												<span style="color:#5E8A26;">Complimentary</span>
											<?php
											}
											elseif($rowFetchInvoice['payment_status']=="ZERO_VALUE")
											{
											?>
												<span style="color:#009900;">Zero Value	</span>
											<?php
											}
											else if($rowFetchInvoice['payment_status']=="PAID")
											{
											?>
												<span style="color:#5E8A26;">Paid</span>
											<?php		
											}
											else if($rowFetchInvoice['payment_status']=="UNPAID")
											{
											?>
												<span style="color:#C70505;">UNPAID</span>
											<?php		
											}
											?>
										</td>
									</tr>
							<?php
								}
							}
							?>
						</table>
						
					</td>
				</tr>
			</tbody> 
		</table>
		
		
		
		</div>
		<div>
			<table width="100%" class="tborder">
				<tr>
					<td class="tfooter">&nbsp;</td>
				</tr>
			</table>
		</div>
	<?php
	}
	
	function viewPGTDetails()
	{   
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['id'];
		$loggedUserId	= $mycms->getLoggedUserId();
		$rowFetchUser   = getUserDetails($delegateId);
		?>
		<table width="100%" class="tborder">
			<tr>
				<td class="tcat">
					<span style="float:left">(Postgraduate Student / PHD Student) Document</span>
				</td>
			</tr>
		</table>
		<table width="100%" align="center" class="tborder"> 
			 
			<tbody>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					<?
						$sqlViewDocument['QUERY'] = "SELECT `user_document` FROM "._DB_USER_REGISTRATION_." 
												WHERE status = 'A' 
												AND `id` = '".$delegateId."'
												AND `registration_classification_id` IN (11,12)";
						$resultViewDocument 	= $mycms->sql_select($sqlViewDocument);
						
						if($resultViewDocument)
						{
							foreach($resultViewDocument as $key=> $rowViewDocument)
							{
					?>
						<table width="100%">
							<tr>
								<td width="30%" align="left">									
									<img src="<?=$cfg['BASE_URL'].$cfg['USER.ID.CARD'].$rowViewDocument['user_document']?>" width="60%"/>
								</td>
								
							</tr>
						</table>
						<?
							}			
						}
						else
						{
						?>
						<table width="100%">
							<tr>
								<td width="30%" align="center">
									<span style="color:#e4181f; font-size:20px"><b>No documents submitted yet.</b></span>
								</td>
								
							</tr>
						</table>		
						<?
						}
						?>		
		
					</td>
				</tr>
				
			</tbody> 
		</table>
		
	<?
	}	
	
	function setPaymentTermsRecord($formType)
	{
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['id'];
		$delagateCatagory = getUserClassificationId($delegateId);
	?>
		<script>
		$(document).ready(function(){	
			$("input[type=radio][operationMode=registration_type]").change(function(){
			$("input[type=checkbox][operationMode=discountCheckbox]").prop("checked", false);
				var regType = $(this).val();
				if(regType=='GENERAL')
				{
					$("div[operationMode=paymentTermsSetUnit]").show();
				}
				if(regType=='ZERO_VALUE')
				{
					$("div[operationMode=paymentTermsSetUnit]").hide();
					$("#amount2").text("");
					$("#amount2").text("0.00");
				}
			});	
		});
		</script>
		<table width="100%">
			<tr>
				<td colspan="2" align="left" class="thighlight">Registration Type</td>
			</tr>
			<tr>
				<td width="20%" align="left">Registration Type <span class="mandatory">*</span></td>
				<td align="left">
					<?
						if($delagateCatagory =='11' && $_REQUEST['show']=='addWorkshop')
						{
							$radioSelect ='checked="checked"';
							$radioDisable = 'disabled="disabled"';
						}
						
					?>
					<input type="radio" name="registration_type_add" id="registration_type_general_add" value="GENERAL" 
					 operationMode="registration_type" checked="checked" onclick="calculateAccompanyAmount();" <?=$radioDisable?>/> General 
					
					&nbsp;
					 <input type="radio" name="registration_type_add" id="registration_type_complementary_add" value="ZERO_VALUE" 
						 operationMode="registration_type" <?=$radioSelect?>/>ZERO VALUE
				</td>
			</tr>
		</table>
		
		<div operationMode="paymentTermsSetUnit">
			<table width="100%">
				<?php
				setPaymentTemplate("add");
				?>	
			</table>
		</div>
	<?php
	}
	
	function getInvoice($Id)
	{
		global $cfg, $mycms;
			$sqlInvoice[QUERY] = "SELECT * 
									FROM  "._DB_SLIP_." 
		 						    WHERE  id = '".$Id."' AND status = 'A'";
		$resInvoice = $mycms->sql_select($sqlInvoice);
		return $resInvoice[0];
	}
	
	function getSlipId($delegateId)
	{
		global $cfg, $mycms;
		
		$mycms->kill("Deprecated Function getSlipId <br>USE</br>getAllSlipsOfDelegate");
			global $cfg, $mycms;
			$sqlSlip['QUERY']  = "SELECT slip.id
						  FROM "._DB_SLIP_." slip			  
			   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
									   `slip_id`
								 FROM  "._DB_INVOICE_." 
								WHERE `status` = 'A'
							 GROUP BY `slip_id` ) activeInvoice
								   ON slip.id = activeInvoice.slip_id 
						 WHERE slip.delegate_id ='".$delegateId."' 
						   AND slip.status = 'A'
						   AND activeInvoice.invoiceCount>0";
			$resSlip   = $mycms->sql_select($sqlSlip);
			$slipIdArr=array();
			if($resSlip)
			{
				foreach($resSlip as $key=>$slipId)
				{
					$slipIdArr[] = $slipId['id'];
				}
			}
			return $slipIdArr;
	}
		
	function editWorkshopFormTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		$loggedUserId		= $mycms->getLoggedUserId();
		$delegateId 	=  $_REQUEST['id'];
		//$spotUser		= $_REQUEST['userREGtype'];	
		$rowFetchUser   = getUserDetails($delegateId);
		//echo '<pre>'; print_r($rowFetchUser);exit;
		$delagateCatagory = getUserClassificationId($delegateId);
		?>
		
		<form name="frmApplyForworkshop" id="frmApplyForworkshop"  action="<?=$processPage?>" method="post" onsubmit="return EditWorkshopValidation();">
			<input type="hidden" name="act" value="editWorkshop" />	
			<input type="hidden" name="delegate_id" value="<?=$delegateId?>" />
			<input type="hidden" name="reg_tariff_cutoff" value="<?=$rowFetchUser['registration_tariff_cutoff_id']?>" />
			<input type="hidden" name="reg_classif_id" value="<?=$rowFetchUser['registration_classification_id']?>" />
			<input type="hidden" name="logged_user" value="<?=$loggedUserId?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Edit Workshop</td>
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">  
							
							<table width="100%" shortData="on" >
								<tr class="thighlight">
								<td colspan="6" align="left">User Details</td>
								</tr>
								<tr >
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left">
										<?=$rowFetchUser['user_title']?> 
										<?=$rowFetchUser['user_first_name']?> 
										<?=$rowFetchUser['user_middle_name']?> 
										<?=$rowFetchUser['user_last_name']?>
									</td>
									<td width="20%" align="left">Email Id:</td>
									<td width="30%" align="left"><?=$rowFetchUser['user_email_id']?></td>
								</tr>								
								<tr>
									<td align="left">Registration Id:</td>
									<td align="left">
									<?php
									if($rowFetchUser['registration_payment_status']=="PAID" 
									   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE"
									   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
									   || $rowFetchUser['sub_registration_type']=="GOVERNMENT_EMPLOYEE")
									{
										echo $rowFetchUser['user_registration_id'];
									}
									else
									{
										echo "-";
									}
									?>
									</td>
									<td align="left">Unique Sequence:</td>
									<td align="left"><?=$rowFetchUser['user_unique_sequence']?></td>
								</tr>
								<tr>
									<td align="left" valign="top">Registration Classification:</td>
									<td align="left" valign="top"><?=getRegClsfName($delagateCatagory)?></td>
									<td align="left" valign="top">Mobile No:</td>
									<td align="left" valign="top"><?=$rowFetchUser['user_mobile_isd_code']?> - <?=$rowFetchUser['user_mobile_no']?></td>
								</tr>
							</table>
							
							<? 
								$sqlWorkshopRequest['QUERY']    = "SELECT * FROM "._DB_REQUEST_WORKSHOP_." 
																	 WHERE `delegate_id` = '".$delegateId."'
																	 AND `status` = 'A'";												  
								$resWorkshopRequest = $mycms->sql_select($sqlWorkshopRequest);	
								$resultWorkshopRequest = $resWorkshopRequest[0];							
							?>
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Registration Cutoff</td>
								</tr>
								<tr>
									<td align="left" style="width:20%;">
										Cutoff Title: <span class="mandatory">*</span>
									</td>
									<td align="left" style="width:30%;">
										
											<?php
												$sqlFetchCutoff['QUERY'] = "SELECT * FROM "._DB_TARIFF_CUTOFF_."
																		WHERE id = '".$rowFetchUser['registration_tariff_cutoff_id']."' 
																		AND `status` = 'A' ORDER BY id";
												
												$resultCutoff	= $mycms->sql_select($sqlFetchCutoff);
												if($resultCutoff)
												{ 
												$fetchCutoff = $resultCutoff[0];
												?>	
													<input type="hidden" name="cutoff_id_add" value="<?=strip_tags($fetchCutoff['cutoff_title'])?>"/>
													<?=strip_tags($fetchCutoff['cutoff_title'])?>
												<?php
												}	
											?>
									</td>
									<td width="50%"></td>
								</tr>
								<tr>
									<td align="left" style="width:20%;">
										Currnet Workshop: <span class="mandatory">*</span>
									</td>
									<td align="left" style="width:30%;">
										
											<?php
												$sqlWorkshopclsfs['QUERY'] = "SELECT *, IFNULL(type,'GENERAL') as type  FROM "._DB_WORKSHOP_CLASSIFICATION_." WHERE id = '".$resultWorkshopRequest['workshop_id']."' AND display= 'Y' AND status='A' ORDER BY sequence_by ASC, workshop_date ASC, id ASC";
												$resWorkshopclsfs = $mycms->sql_select($sqlWorkshopclsfs);
												if($resWorkshopclsfs)
												{ 
												$fetchWorkshopclsfs = $resWorkshopclsfs[0];
												?>	
													<?=strip_tags($fetchWorkshopclsfs['classification_title'])?>
												<?php
												}	
											?>
									</td>
								</tr>
							</table>														
								<div class="reg_dtl_tbl">
									<table width="100%">
										<thead>
											<tr>
												<th colspan="2" align="left" class="tcat"  >
													Select Workshop
												</th>	
										</thead>
										<tbody>
											<tr>
												<td></td>
											</tr>
											<tr>
												<td align="left" style="width:40%;">
													<select style="width:90%;" name="workshop_id" id="workshop_id" operationMode="workshop_id">
														<option value="">-- Choose Workshop --</option>
														<?php
															$sqlWorkshopclsf['QUERY'] = "SELECT *, IFNULL(type,'GENERAL') as type  FROM "._DB_WORKSHOP_CLASSIFICATION_." WHERE display= 'Y' AND status='A' ORDER BY sequence_by ASC, workshop_date ASC, id ASC";
															$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
															if($resWorkshopclsf){
																foreach($resWorkshopclsf as $keyworkshopoff=>$rowWorkshopoff){
																	if($rowWorkshopoff['id'] !=$resultWorkshopRequest['workshop_id'])
																	{
																	?>
																		<option value="<?=$rowWorkshopoff['id']?>"><?=$rowWorkshopoff['classification_title']?></option>
																	<?php
																	}
																}
															}
														?>
													</select>
												</td>
												<td align="left" style="width:60%;"><input name="" type="submit" value="Proceed" style="margin-left:20%; " class="btn btn-medium btn-blue" /></td>
											</tr>
										</tbody>
									</table>
											
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<script>
		function EditWorkshopValidation()
		{ 	
			accessValidation	 = true;
			
			if($("#workshop_id").val().length==0)
			{
				alert('rcg_');
				accessValidation	 = false;
				return false;				
			}
						
			return accessValidation;	
		}
		</script>
	<?php
	}
?>

