<?php   
	include_once('includes/init.php');
	page_header("Offline Unpaid User");
	
	$indexVal          = 1;
	$pageKey           = "_pgn".$indexVal."_";
	
	$pageKeyVal        = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString     = "";
	$searchArray       = array();
	
	$searchArray[$pageKey]         		       = $pageKeyVal;
	$searchArray['src_start_date']             = trim($_REQUEST['src_start_date']);
	$searchArray['src_end_date']               = trim($_REQUEST['src_end_date']);
	$searchArray['src_slip_no']            	   = addslashes(trim($_REQUEST['src_slip_no'],'##'));
	$searchArray['src_invoice_no']             = addslashes(trim($_REQUEST['src_invoice_no']));
	$searchArray['src_payment_status']         = addslashes(trim($_REQUEST['src_payment_status']));
	$searchArray['src_registration_status']    = addslashes(trim($_REQUEST['src_registration_status']));
	$searchArray['src_payment_mode_off']       = addslashes(trim($_REQUEST['src_payment_mode_off']));
	$searchArray['src_payment_mode_on']    	   = addslashes(trim($_REQUEST['src_payment_mode_on']));
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
	?>
	<script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
	<div class="container">
		<?php 
			switch($show){
			
				case'step1':
					
					
					break;
				
				default:	
					viewInvoiceDetialsReports($cfg, $mycms);
					break;
			} 
		?>
	</div>
<?php
	page_footer();
	
	function viewInvoiceDetialsReports($cfg, $mycms)
	{
		global $searchArray, $searchString;
		include_once("../../includes/function.invoice.php");
		include_once("../../includes/function.delegate.php");
		include_once("../../includes/function.workshop.php");
		
		
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access				= buttonAccess($loggedUserId);
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser['QUERY']     = "SELECT * FROM "._DB_CONF_USER_." 
		                               			WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<form name="frmSearch" method="post" action="registration.process.php">
			<input type="hidden" name="act" value="search_slip_wise" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Offline Unpaid User Make Payment</span>
						<!--<span class="tsearchTool" forType="tsearchTool"></span>-->
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									
									<td align="left" width="150">Slip No:</td>
									<td align="left" width="250">
										<input type="text" name="src_slip_no" id="src_slip_no"
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
									<td align="left" width="150"></td>
									<td align="left" width="250"></td>
									<td align="right" rowspan="2">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</form>
		
		<table width="100%" class="tborder" align="center" >	
				<tr>	
					<td align="left">		
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="5%" align="center" data-sort="int">Sl No</td>
								<td width="15%" align="left">Slip No</td>
								<td align="left" width="25%">Slip User Dtls</td>
								<td align="left" width="15%">Slip User Reg Dtls</td>
								<td width="10%" align="center">Payment Status</td>
								<td width="10%" align="center" width="15%">Invoice Amount</td>
								</tr>
							</thead>
		
						<tbody>
							<?php
							@$searchCondition       = "";
							
							if($_REQUEST['src_slip_no']!='')
							{
								$searchCondition   .= " AND slip.slip_number LIKE '%".$_REQUEST['src_slip_no']."%'";
							}
							if($_REQUEST['src_invoice_no']!='')
							{
								$searchCondition   .= " AND invoice.invoice_number LIKE '%".$_REQUEST['src_invoice_no']."%'";
							}
							if($_REQUEST['src_payment_status']!='')
							{
								$searchCondition   .= " AND slip.payment_status = '".$_REQUEST['src_payment_status']."'";
							}
							if($_REQUEST['src_registration_status']!='')
							{
								$searchCondition   .= " AND invoice.invoice_mode = '".$_REQUEST['src_registration_status']."'";
							}
							if($_REQUEST['src_payment_mode']!='')
							{
								if($_REQUEST['src_payment_mode']=='PTNS')
								{	 
									$searchCondition   .= " AND payment.id IS NULL";
								}
								else
								{					   
									$searchCondition   .= " AND payment.payment_mode = '".$_REQUEST['src_payment_mode']."'";
								}
							}
							
							
							$sqlSlip = array();
							$sqlSlip['QUERY']  = "SELECT IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
												IFNULL(activeInvoiceAmount.totalInvoice,0.00) AS activeInvoiceAmount,
												slip.slip_number AS slipNumber,
												slip.id AS slipId,
												slip.payment_status AS paymentStatus,
												slipUser.id AS slipUserid,
												slipUser.user_full_name AS slipUserName,
												slipUser.user_unique_sequence AS slipUserUnqsqnce,
												slipUser.user_registration_id AS slipUserRegid,
												slipUser.user_email_id AS slipUserEmailId,
												CONCAT ( slipUser.user_mobile_isd_code, ' - ', slipUser.user_mobile_no) AS slipUserMobile
												
												
										  FROM "._DB_SLIP_." slip	
										  
									INNER JOIN "._DB_USER_REGISTRATION_." slipUser
											ON slip.delegate_id = slipUser.id
											
							   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
														`slip_id`
												  FROM  "._DB_INVOICE_." 
												 WHERE `status` = 'A'
											  GROUP BY `slip_id` ) activeInvoice
											ON slip.id = activeInvoice.slip_id 
											
							   LEFT OUTER JOIN ( SELECT SUM(`service_roundoff_price`) AS totalInvoice,
													   `slip_id`
												 FROM  "._DB_INVOICE_." 
												WHERE `status` = 'A'
											 GROUP BY `slip_id` ) activeInvoiceAmount
											ON slip.id = activeInvoiceAmount.slip_id	
													
										 WHERE slip.status = 'A'
										   AND slipUser.status='A'
										   AND slip.payment_status = 'UNPAID'
										   AND activeInvoice.invoiceCount>0
										   ".$searchCondition."
										   ORDER BY slip.id DESC,slipUser.id DESC";			   
							$resSlip   = $mycms->sql_select($sqlSlip);	
							echo "<pre>";
							//print_r($resSlip);echo "</pre>";die('pp');
							//die($resSlip);
							if($resSlip)
							{
								foreach($resSlip as $i=>$invoiceDetails) 
								{
									$counter      = $counter + 1;
								?>
								<table width="100%" style="border:1px solid green;">
									<tr class="tlisting">
								
										<td align="center" valign="top" width="5%"><?=$counter?></td>
										<td align="left" valign="top" width="15%"><?=$invoiceDetails['slipNumber']?></td>
										<td align="left" valign="top" width="25%">
											<?=$invoiceDetails['slipUserName']?><br />
											<?=$invoiceDetails['slipUserEmailId']?><br />
											<?=$invoiceDetails['slipUserMobile']?>
										</td>
										<td align="left" valign="top" width="15%">
											<?=$invoiceDetails['slipUserRegid']?><br />
											<?=$invoiceDetails['slipUserUnqsqnce']?>
										</td>
										<td align="right" width="10%"  valign="top" style="color:<?=$invoiceDetails['paymentStatus']=="PAID"?"green":"red"?>" ><?=$invoiceDetails['activeInvoiceAmount']?></td>
										<td align="center" width="10%" >
											<?
											
												$resPaymentDetails      = paymentDetails($invoiceDetails['slipId']);
												
												if($resPaymentDetails['payment_status'] != "PAID" && $resPaymentDetails['status']=="A")
												{
												?>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
												 onclick="openPaymentPopup('<?=$invoiceDetails['slipUserid']?>','<?=$invoiceDetails['slipId']?>','<?=$resPaymentDetails['id']?>','Y')">Pay Now</a>
												<?
												}
												else
												{
												?>
													<a class="ticket ticket-important" href="registration.php?show=additionalRegistrationSummery&sxxi=<?=base64_encode($invoiceDetails['slipId'])?>" target="_blank">Set Payment Terms</a>
													<?
												}
												?>
										</td>
									</tr>
									<tr>
										<td colspan="6" style="border-bottom:1px dashed green;"></td>
									</tr>
								<?
											
									$invoiceCounter                 = 0;
									$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$invoiceDetails['slipId']);
																	
									$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
									
									$ffff = getInvoice($rowFetchSlip['id']);
									//echo "<pre>";print_r($ffff);echo "</pre>";
									if($resultFetchInvoice)
									{
										foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
										{
											$invoiceCounter++;
										?>
											<tr class="tlisting" style=" <?=$styleColor?>" >
												<td align="center"><?=$counter?>.<?=$invoiceCounter?></td>
												<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
												<td align="left" colspan="2">
												<?
													$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
													if($rowFetchInvoice['service_type']=="DELEGATE_CONFERENCE_REGISTRATION")
													{
														echo getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']))." - ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type']=="DELEGATE_RESIDENTIAL_REGISTRATION")
													{
														echo getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']))." - ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type']=="DELEGATE_WORKSHOP_REGISTRATION")
													{
														$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
														echo getWorkshopName($workShopDetails['workshop_id'])." - ".$thisUserDetails['user_full_name'];
													}
												?>
												</td>
												<td align="right">
												<?=number_format($rowFetchInvoice['service_roundoff_price'],2)?>
												</td>
												<td align="center">
													<?php
													if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
													{
													?>
														<span style="color:#5E8A26;">Complementary</span>
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
								<?
								}
							} 
							else 
							{
							?>
								<tr>
									<td colspan="7" align="center">
										<span class="mandatory">No Record Present.</span>												
									</td>
								</tr>  
							<?php 
							} 
							?>
							
						</tbody>
					</table>
					
				</td>
			</tr>
		</table>
		
		
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form" id="payment_popup"></div>
		<div class="popup_form" id="SetPaymentTermsPopup"></div>
		<div class="popup_form" id="settlementPopup"></div>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	
	}
	
	
	function viewInvoiceDetialsReports2($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access				= buttonAccess($loggedUserId);
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser = array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                               			 WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<form name="frmSearch" method="post" action="registration.process.php">
			<input type="hidden" name="act" value="search_mode_wise" />
			<table width="100%" class="tborder" align="center" >	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Offline Unpaid Invoice </span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<div class="tsearch" style="display:block;">
							<script>
								$( document ).ready(function() {
									selectPaymentMode();
								});
								function selectPaymentMode()
								{
									var val = $("#src_registration_status").val();
									$("span[ok=ok]").css('display','none');
									$("span[use="+val+"]").css('display','block');
									
								}
							</script>
						</div>
					</td>
				</tr>
			</table>
		</form>
		
			<table width="100%" class="tborder" align="center" >	
				<tr>	
					<td align="left">		
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="5%" align="center" data-sort="int">Sl No</td>
									<td align="left" width="15%">Invoice Dtls</td>
									<td width="15%" align="left">Slip Dtls</td>
									<td align="left" width="20%">Invoice For</td>
									<td width="15%" align="left">Invoice Amount</td>
									<td width="10%" align="right">Action</td>
								</tr>
							</thead>
							<tbody>
								<?php
								@$searchCondition       = "";
								
								if($_REQUEST['src_slip_no']!='')
								{
									$searchCondition   .= " AND slip.slip_number LIKE '%".$_REQUEST['src_slip_no']."%'";
								}
								if($_REQUEST['src_invoice_no']!='')
								{
									$searchCondition   .= " AND invoice.invoice_number LIKE '%".$_REQUEST['src_invoice_no']."%'";
								}
								if($_REQUEST['src_payment_status']!='')
								{
									$searchCondition   .= " AND invoice.payment_status = '".$_REQUEST['src_payment_status']."'";
								}
								if($_REQUEST['src_registration_status']!='')
								{
									$searchCondition   .= " AND invoice.invoice_mode = '".$_REQUEST['src_registration_status']."'";
								}
								if($_REQUEST['src_payment_mode_off']!='')
								{
									$searchCondition   .= " AND payment.payment_mode = '".$_REQUEST['src_payment_mode_off']."'";
								}
								if($_REQUEST['src_payment_mode_off']!='')
								{
									if($_REQUEST['src_payment_mode_off']=='PTNS')
									{	 
										$searchCondition   .= " AND payment.id IS NULL";
									}
									else if($_REQUEST['src_payment_mode_off']=='COMPLIMENTARY')
									{	 
										$searchCondition   .= " AND invoice.payment_status = 'COMPLIMENTARY'";
									}
									else
									{					   
										$searchCondition   .= " AND payment.payment_mode = '".$_REQUEST['src_payment_mode_off']."'";
									}
								}
								
								if($_REQUEST['src_payment_mode_on']!='')
								{
									if($_REQUEST['src_payment_mode_on']=='PTNS')
									{	 
										$searchCondition   .= " AND payment.id IS NULL";
									}
								}
								$userId['QUERY'] = "SELECT DISTINCT(user.id) FROM "._DB_USER_REGISTRATION_." user
											    LEFT OUTER JOIN "._DB_INVOICE_." invoice
															ON user.id = invoice.delegate_id
															AND invoice.status = 'A'
													  WHERE user.status = 'A'
														 AND invoice.payment_status = 'UNPAID'
														 AND invoice.invoice_mode = 'OFFLINE'
															 ";
								$userIds   = $mycms->sql_select($userId);	
														
									
								if($userIds)
								{
									foreach($userIds as $key=> $userdetails)
									{
										
											
												
												$sqlSlip['QUERY'] 		 = "SELECT invoice.invoice_number AS invoiceNumber,
															   						slip.slip_number AS slipNumber,
															   						slip.id AS slip_id,
															   						payment.id AS paymentId,
															   						invoiceUser.user_full_name AS invoiceUserName,
															   						invoiceUser.registration_classification_id AS registrationClassificationId,
															   						invoice.service_type AS invoiceFor,
															   						invoice.refference_id AS reqId,
															   						invoice.service_roundoff_price AS amount,
															   						invoiceUser.user_full_name AS slipUserName,
															   						invoice.payment_status AS paymentStatus,
															   						invoiceUser.id AS delegate_id,
															   						payment.payment_mode AS payment_mode,
															   						invoice.delegate_id AS delegateId
															  
																
														  FROM "._DB_INVOICE_." invoice			  
											   
													INNER JOIN "._DB_SLIP_." slip
															ON invoice.slip_id = slip.id
															AND invoice.invoice_mode = 'OFFLINE'
															
													INNER JOIN "._DB_USER_REGISTRATION_." invoiceUser
															ON invoice.delegate_id = invoiceUser.id
															AND slip.delegate_id = invoiceUser.id
													
											   LEFT OUTER JOIN "._DB_PAYMENT_." payment
															ON payment.slip_id = slip.id	
																			
														 WHERE invoice.status = 'A'
														   AND invoice.payment_status = 'UNPAID'
														   AND slip.status = 'A'
														   AND invoiceUser.status = 'A'
														   AND invoice.delegate_id= '".$userdetails['id']."'
															
															   ".$searchCondition."
													  ORDER BY slip.id DESC";
									//echo  nl2br($sqlSlip);			   
									$resSlip   = $mycms->sql_select($sqlSlip);
										foreach($resSlip as $i=>$invoiceDetails) 
										{
											$counter      = $counter + 1;
											
											if($invoiceDetails['delegateId']== $userdetails['id'])
											{
												$color ="style=background-color:#FFFFCC";
											}
											else
											{
												$color ="style=background-color:#FFFFFF";
											}
											
										?>
											<tr class="tlisting" <?=$color?>> 
											
												<td align="center"><?=$counter?></td>
												
												<td align="left"><?=$invoiceDetails['invoiceNumber']?><br /><span style="color:#FF3300;"><?=$invoiceDetails['invoiceUserName']?></span></td>
												
												<td align="left"><?=$invoiceDetails['slipNumber']?><br /><span style="color:#FE6F06;"><?=$invoiceDetails['slipUserName']?></span></td>
												
												<td align="left">
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
														echo getWorkshopName($workShopDetails['workshop_id']);
													}
												?>
												</td>
												<td align="left" > <span style="color:<?=$invoiceDetails['paymentStatus']=="UNPAID"?"red":"green"?>" ><?=$invoiceDetails['amount']?></span></td>
												<td align="right" ><a class="ticket ticket-important" operationMode="proceedPayment" 
														 onclick="openPaymentPopup('<?=$invoiceDetails['delegate_id']?>','<?=$invoiceDetails['slip_id']?>','<?=$invoiceDetails['paymentId']?>')">Pay Now</a>
													</td>
											</tr>
										</div>	
										<?
										}
										
										
									}
								} 
								else 
								{
								?>
									<tr>
										<td colspan="7" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
								<?php 
								} 
								?>
								
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form2" id="payment_popup"></div>
		<div class="popup_form2" id="SetPaymentTermsPopup"></div>
		<div class="popup_form2" id="settlementPopup"></div>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	
	}
	
	
?>