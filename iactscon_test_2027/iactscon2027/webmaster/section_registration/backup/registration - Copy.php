<?php  

	include_once('includes/init.php');
	page_header("General Registration");
	include_once("../../includes/function.invoice.php");
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.registration.php');
?>
	
	<script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/dinner_registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/accompany_registration.js"></script>
	<script>
		function onlineOpenPaymentPopup (delegateId,slipId,paymentId,isRid='N')
		{
		
		//egistrationDetailsCompressedQuery
			$("#fade_popup").fadeIn(1000);
			$("#onlinePayment_popup").fadeIn(1000);
			$('#onlinePayment_popup').html('<div style="text-align:center;"><img src="http://localhost/kasscon/dev/developer/webmaster/images/loader.gif"/></div>');
			
			$.ajax({
					type: "POST",
					url: 'registration.process.php',
					data: 'act=onlinePaymentDetails&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId,
					dataType: 'html',
					async: false,
					success:function(returnMessage)
					{
						$('#onlinePayment_popup').html(returnMessage);
						$('#redirect').val(isRid);
						$("input[rel=tcal]").datepicker({
							 dateFormat :"yy-mm-dd",
							 changeMonth: true,
							 changeYear: true,
							 maxDate: new Date()
						});	
					}
			});	
		}
		function closeSetPaymentTermsPopup()
		{
            
            
			$("#onlinePayment_popup").fadeOut();
			$("#fade_popup").fadeOut();
		}
		function multiPaymentPopup(delegateId,slipId,paymentId,isRid='N',userREGtype)
		{
		
			$("#fade_popup").fadeIn(1000);
			$("#payment_popup").fadeIn(1000);
			$('#payment_popup').html('<div style="text-align:center;"><img src="http://localhost/kasscon/dev/developer/webmaster/images/loader.gif"/></div>');
			console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/registration.process.php?act=paymentDetails&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId+'&userREGtype='+userREGtype);
			
			$.ajax({
					type: "POST",
					url: 'registration.process.php',
					data: 'act=multiPaymentDetails&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId+'&userREGtype='+userREGtype,
					dataType: 'html',
					async: false,
					success:function(returnMessage)
					{
						$('#payment_popup').html(returnMessage);
						$('#redirect').val(isRid);
						$("input[rel=tcal]").datepicker({
							 dateFormat :"yy-mm-dd",
							 changeMonth: true,
							 changeYear: true,
							 maxDate: new Date()
						});	
					}
			});
			
		}
	</script>
	<style>
		.paymentDtls{
			width: 50%;
			float: left;
		}
		.paidStatus{
			width: 50%;
			color: green;
		}
		.unpaidStatus{
			width: 50%;
			color: red;
		}
		.paymentArea{
			background-color:#1e7bac;
			border-bottom:2px solid #DB5600;
			border-radius: 5px;
			padding:12px;
			font-size:22px;
			font-weight:bold;
			color:#000;
			margin:15px 0 0 0;
		}
		.online_popup_form {
			display: none;
			background-color: navajowhite;
			width: 60%;
			position: fixed;
			top: 20%;
			left: 19%;
			height: 330px;
			padding: 10px;
			z-index: 100;
			box-shadow: 2px 2px 20px rgba(0, 0, 0, 1);
			border-radius: 6px;
		}
		.ticket {
		background-color: #999999;
		font-size: 11px;
		font-weight: 600;
		color: #FFF;
		padding: 3px 8px;
		margin-bottom: 1em;
		text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
		}
		.ticket-important {
			background-color: #C70505;
		}
	
	</style>
	<?php 
		$indexVal          = 1;
		$pageKey           = "_pgn".$indexVal."_";
		
		$pageKeyVal        = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString     = "";
		$searchArray       = array();
		
		$searchArray[$pageKey]                     = $pageKeyVal;
		
		if(isset($_REQUEST['src_registration']) && trim($_REQUEST['src_registration'])!='') { 					    $searchArray['src_registration']                   = addslashes(trim($_REQUEST['src_registration'])); }
		if(isset($_REQUEST['src_email_id']) && trim($_REQUEST['src_email_id'])!='') { 					            $searchArray['src_email_id']                       = addslashes(trim($_REQUEST['src_email_id'])); }
		if(isset($_REQUEST['src_access_key']) && trim($_REQUEST['src_access_key'])!='') { 					        $searchArray['src_access_key']                     = addslashes(trim($_REQUEST['src_access_key'],'#')); }
		if(isset($_REQUEST['src_mobile_no']) && trim($_REQUEST['src_mobile_no'])!='') { 					        $searchArray['src_mobile_no']                      = addslashes(trim($_REQUEST['src_mobile_no'])); }
		if(isset($_REQUEST['src_user_first_name']) && trim($_REQUEST['src_user_first_name'])!='') { 				$searchArray['src_user_first_name']                = addslashes(trim($_REQUEST['src_user_first_name'])); }
		if(isset($_REQUEST['src_user_middle_name']) && trim($_REQUEST['src_user_middle_name'])!='') { 				$searchArray['src_user_middle_name']               = addslashes(trim($_REQUEST['src_user_middle_name'])); }
		if(isset($_REQUEST['src_invoice_no']) && trim($_REQUEST['src_invoice_no'])!='') { 							$searchArray['src_invoice_no']                     = addslashes(trim($_REQUEST['src_invoice_no'])); }
		if(isset($_REQUEST['src_slip_no']) && trim($_REQUEST['src_slip_no'])!='') { 							    $searchArray['src_slip_no']                        = addslashes(trim($_REQUEST['src_slip_no'],'##')); }
		if(isset($_REQUEST['src_registration_mode']) && trim($_REQUEST['src_registration_mode'])!='') { 			$searchArray['src_registration_mode']              = addslashes(trim($_REQUEST['src_registration_mode'])); }
		if(isset($_REQUEST['src_user_last_name']) && trim($_REQUEST['src_user_last_name'])!='') { 			        $searchArray['src_user_last_name']                 = addslashes(trim($_REQUEST['src_user_last_name'])); }
		if(isset($_REQUEST['src_atom_transaction_ids']) && trim($_REQUEST['src_atom_transaction_ids'])!='') {       $searchArray['src_atom_transaction_ids']           = addslashes(trim($_REQUEST['src_atom_transaction_ids'])); }
		if(isset($_REQUEST['src_transaction_ids']) && trim($_REQUEST['src_transaction_ids'])!='') { 				$searchArray['src_transaction_ids']                = addslashes(trim($_REQUEST['src_transaction_ids'])); }
		if(isset($_REQUEST['src_conf_reg_category']) && trim($_REQUEST['src_conf_reg_category'])!='') { 			$searchArray['src_conf_reg_category']              = addslashes(trim($_REQUEST['src_conf_reg_category'])); }
		if(isset($_REQUEST['src_reg_category']) && trim($_REQUEST['src_reg_category'])!='') { 					    $searchArray['src_reg_category']        		   = addslashes(trim($_REQUEST['src_reg_category'])); }
		if(isset($_REQUEST['src_registration_id']) && trim($_REQUEST['src_registration_id'])!='') { 				$searchArray['src_registration_id']                = addslashes(trim($_REQUEST['src_registration_id'])); }
		if(isset($_REQUEST['src_workshop_classf']) && trim($_REQUEST['src_workshop_classf'])!='') { 				$searchArray['src_workshop_classf']                = addslashes(trim($_REQUEST['src_workshop_classf'])); }
		if(isset($_REQUEST['src_transaction_id']) && trim($_REQUEST['src_transaction_id'])!='') { 					$searchArray['src_transaction_id']                 = addslashes(trim($_REQUEST['src_transaction_id'])); }
		if(isset($_REQUEST['src_payment_mode']) && trim($_REQUEST['src_payment_mode'])!='') { 					    $searchArray['src_payment_mode']                   = addslashes(trim($_REQUEST['src_payment_mode'])); }
		if(isset($_REQUEST['src_payment_status']) && trim($_REQUEST['src_payment_status'])!='') { 					$searchArray['src_payment_status']                 = addslashes(trim($_REQUEST['src_payment_status'])); }	
		if(isset($_REQUEST['src_accommodation']) && trim($_REQUEST['src_accommodation'])!='') { 					$searchArray['src_accommodation']                  = addslashes(trim($_REQUEST['src_accommodation'])); }
		if(isset($_REQUEST['src_mobile_isd_code']) && trim($_REQUEST['src_mobile_isd_code'])!='') { 			    $searchArray['src_mobile_isd_code']                = addslashes(trim($_REQUEST['src_mobile_isd_code'])); }	
		if(isset($_REQUEST['src_registration_type']) && trim($_REQUEST['src_registration_type'])!='') { 		    $searchArray['src_registration_type']              = addslashes(trim($_REQUEST['src_registration_type'])); }	
		if(isset($_REQUEST['src_payment_date']) && trim($_REQUEST['src_payment_date'])!='') { 		                $searchArray['src_payment_date']                   = addslashes(trim($_REQUEST['src_payment_date'])); }	
		if(isset($_REQUEST['src_cancel_invoice_id']) && trim($_REQUEST['src_cancel_invoice_id'])!='') { 		    $searchArray['src_cancel_invoice_id']              = addslashes(trim($_REQUEST['src_cancel_invoice_id'])); }
		if(isset($_REQUEST['src_transaction_slip_no']) && trim($_REQUEST['src_transaction_slip_no'])!='') { 	    $searchArray['src_transaction_slip_no']            = addslashes(trim($_REQUEST['src_transaction_slip_no'])); }	
		if(isset($_REQUEST['src_registration_from_date']) && trim($_REQUEST['src_registration_from_date'])!='') { 	$searchArray['src_registration_from_date']         = addslashes(trim($_REQUEST['src_registration_from_date'])); }
		if(isset($_REQUEST['src_registration_to_date']) && trim($_REQUEST['src_registration_to_date'])!='') { 	    $searchArray['src_registration_to_date']           = addslashes(trim($_REQUEST['src_registration_to_date'])); }		
		
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$searchString .= "&".$searchKey."=".$searchVal;
			}
		}
	
	?>
	<div class="container">
		<?php 
			switch($show){
				//REGISTRATION
				case'step1':
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					$mycms->removeSession('PROCESS_FLOW_ID');
					$mycms->removeSession('SLIP_ID');
					registrationStep1Template($requestPage, $processPage, $registrationRequest);
					break;
				
				case'step3':
					//ACCOMPANY
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";			
					registrationAccompanyTemplate($requestPage, $processPage, $registrationRequest);
					break;
					
				case'step6':
					//NOTIFICATION
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					regCompleteNotificationTemplate($requestPage, $processPage, $registrationRequest);
					break;
					
				case'paymentArea':
					//NOTIFICATION
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					regPaymnetAreaTemplate($requestPage, $processPage, $registrationRequest);
					break;
					
				case'additionalPaymentArea':
					//NOTIFICATION
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					regAdditionalPaymnetAreaTemplate($requestPage, $processPage, $registrationRequest);
					break;
					
				case'makePaymentArea':
					//NOTIFICATION
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					makePaymnetAreaTemplate($requestPage, $processPage, $registrationRequest);
					break;
					
				case'registrationSummery':
					//REGISTRATION SUMMERY
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					registrationSummeryTemplate($requestPage, $processPage, $registrationRequest);
					break;
				
				case'invoice':					
					viewInvoiceDetails();
					break;
				
				case'sendRegConfirmMail':					
					ResendRegConfirmationMail($cfg, $mycms);
					break;
				
				case'sendInvoiceMail':					
					ResendInvoiceDetailsMail($cfg, $mycms);
					break;	
				
				case'AskToRemove':						
					viewDeletedInvoiceDetails();
					break;
				case'additionalRegistrationSummery':
					//REGISTRATION SUMMERY
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "GENERAL";
					$isComplementary		= "N";					
					AdditionalRegistrationSummeryTemplate($requestPage, $processPage, $registrationRequest,$isComplementary);
					break;
				case'addDinner':					
					$requestPage  			= "registration.php";
					$processPage  			= "registration.process.php";
					$registrationRequest	= "GENERAL";					
					addDinnerFormTemplate($requestPage, $processPage, $registrationRequest);					
					break;
				case'addAccompany':	
					$requestPage  			= "registration.php";
					$processPage  			= "registration.process.php";
					$registrationRequest	= "GENERAL";					
					addAccompanyFormTemplate($requestPage, $processPage, $registrationRequest);				
					break;
				case'trash':								
					viewAllDeletedRegistration($cfg, $mycms);
					break;
				case'encodersUsers':							
					viewencodersUsers($cfg, $mycms);
					break;
					
				case'sendRegConfirmSMS':					
					ResendRegConfirmationSMS($cfg, $mycms);
					break;
					
				case'sendAccknowledgementMail':
					ResendAccknowledgementConfirmationMail($cfg, $mycms);
					break;
					
				case'sendAccknowledgementSMS':
					ResendAccknowledgementConfirmationSMS($cfg, $mycms);
					break;
					
				case'reallocationOfWorkshop':					
					reallocationOfWorkshop($cfg, $mycms);
					break;
					
				case'editReallocationOfWorkshop':					
					viewEditWorkshop();
					break;		
								
				
				default:				
					viewAllGeneralRegistration($cfg, $mycms);
					break;
			} 
		?>
	</div>
<?php
	page_footer();
	
	/****************************************************************************/
	/*                      SHOW ALL GENERAL REGISTRATION                       */
	/****************************************************************************/	
	function viewAllGeneralRegistration($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
		
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access				= buttonAccess($loggedUserId);
		
		
	?>
			<script>
			$(document).ready(function(){
				$("td[use=registrationDetailsList]").attr("dataStat","noDisplay");
				loadUserDetails();
			});
			
			function loadUserDetails()
			{
				if($("td[use=registrationDetailsList][dataStat=noDisplay]").length > 0)
				{
					var detailsTd = $("td[use=registrationDetailsList][dataStat=noDisplay]").first();
					var userId = $(detailsTd).attr("userId");
					
					var param = "act=registrationList&id="+userId;
					$.ajax({
						  url: "registration.process.php",
						  type: "POST",
						  data: param,
						  dataType: "html",
						  success: function(data){
							 $(detailsTd).html(data);
							 $(detailsTd).attr("dataStat","Display");
							 loadUserDetails();
						  }
					   }
					);
				}
			}
			</script>
		
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
						<a href="registration.process.php?act=downloadUserListExcel&<?=$searchString?>"><img src="../images/Excel-icon.png"  style="float:right; padding-right: 10px;"/></a>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<!--Advanced Search	-->		
						<div class="tsearch" style='display:block;'>
							<form name="frmSearch" method="post" action="registration.php" onSubmit="return FormValidator.validate(this);">
							<input type="hidden" name="act" value="search_registration" />
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` 
																					  FROM "._DB_REGISTRATION_CLASSIFICATION_."
																					 WHERE status = 'A'
																					 AND `id` != 2";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "Conference Registration - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo "Offer - ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Payment Status:</td>
									<td align="left">
									
										<select name="src_payment_status" id="src_payment_status" style="width:96%;">
											<option value="">-- Select Payment Status --</option>
											<option value="PAID" <?=(trim($_REQUEST['src_payment_status']=="PAID"))?'selected="selected"':''?>>PAID</option>
											<option value="UNPAID" <?=(trim($_REQUEST['src_payment_status']=="UNPAID"))?'selected="selected"':''?>>UNPAID</option>
											<option value="COMPLIMENTARY" <?=(trim($_REQUEST['src_payment_status']=="COMPLEMENTARY"))?'selected="selected"':''?>>COMPLIMENTARY</option>
											<option value="ZERO_VALUE" <?=(trim($_REQUEST['src_payment_status']=="ZERO_VALUE"))?'selected="selected"':''?>>ZERO VALUE</option>
											<option value="CREDIT" <?=(trim($_REQUEST['src_payment_status']=="CREDIT"))?'selected="selected"':''?>>CREDIT</option>
										</select>
										
									</td>
									
									<td align="left">Registration type:</td>
									<td align="left">
									
										<select name="src_registration_type" id="src_registration_type" style="width:96%;">
											<option value="">-- Select Registration type --</option>
											<option value="GENERAL" <?=(trim($_REQUEST['src_registration_type']=="GENERAL"))?'selected="selected"':''?>>GENERAL</option>
											<option value="SPOT" <?=(trim($_REQUEST['src_registration_type']=="SPOT"))?'selected="selected"':''?>>SPOT</option>
											<!--<option value="COUNTER" <?=(trim($_REQUEST['src_registration_type']=="COUNTER"))?'selected="selected"':''?>>COUNTER</option>-->
										</select>
										
									</td>
								</tr>									
								<tr>
									<td align="left">Payment Date:</td>
									<td align="left">
									
										<input type="date" name="src_payment_date" id="src_payment_date" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_payment_date']?>" />
										
									</td>
									<td align="left">Workshop Type:</td>
									<?php
									$sqlWorkshopclsf	=	array();
									$sqlWorkshopclsf['QUERY'] = "SELECT `classification_title`,`id` FROM "._DB_WORKSHOP_CLASSIFICATION_." WHERE status = 'A' ORDER BY id ASC ";
										$resWorkshopclsf = $mycms->sql_select($sqlWorkshopclsf);
										
									//echo "<pre>";
								//	print_r($resWorkshopclsf);
									//echo "</pre>";
									//echo trim($_REQUEST['src_workshop_classf']);
									$status = substr($_REQUEST['src_workshop_classf'],1);
									?>
									<td align="left">	
										<!--input type="text" name="sabya" /-->								
										<select name="src_workshop_classf" id="src_workshop_classf" style="width:96%;">
											<option value="">-- Select Workshop Type --</option>
											<?
												foreach($resWorkshopclsf as $key => $rowWorkshopclsf)
												{
												?>
												<optgroup label="<?=$rowWorkshopclsf['classification_title']?>">
												<option value="<?=$rowWorkshopclsf['id'].'P'?>" <?=(trim($status=='P'))?'selected="selected"':''?>>paid</option>
												<option value="<?=$rowWorkshopclsf['id'].'U'?>" <?=(trim($status=='U'))?'selected="selected"':''?>>unpaid</option>
												<option value="<?=$rowWorkshopclsf['id'].'C'?>" <?=(trim($status=='C'))?'selected="selected"':''?>>complimentary</option>
												<option value="<?=$rowWorkshopclsf['id'].'A'?>" <?=(trim($status=='A'))?'selected="selected"':''?>>all</option>
												</optgroup>												
												<?
												}
											?>											
										</select>										
									</td>		
								</tr>
								<tr>
									<td align="left">Pay Mode:</td>
									<td align="left">									
										<select name="src_payment_mode" id="src_payment_mode" style="width:96%;">
											<option value="">-- Select Payment Mode --</option>
											<option value="Cash" <?=(trim($_REQUEST['src_payment_mode']=="Cash"))?'selected="selected"':''?>>Cash</option>
											<option value="Card" <?=(trim($_REQUEST['src_payment_mode']=="Card"))?'selected="selected"':''?>>Card</option>
											<option value="Cheque" <?=(trim($_REQUEST['src_payment_mode']=="Cheque"))?'selected="selected"':''?>>Cheque</option>
											<option value="Draft" <?=(trim($_REQUEST['src_payment_mode']=="Draft"))?'selected="selected"':''?>>Draft</option>
											<option value="NEFT" <?=(trim($_REQUEST['src_payment_mode']=="NEFT"))?'selected="selected"':''?>>NEFT/RTGS</option>
										</select>
									</td>									
									<td align="left">Cancel Invoice Id:</td>
									<td align="left">
										<input type="text" name="src_cancel_invoice_id" id="src_cancel_invoice_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_cancel_invoice_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left" colspan=3>CHEQUE / DEMAND DRAFT / NEFT / RTGS / CARD / TRANSACTION No:</td>
										<td align="left" >
										<input type="text" name="src_transaction_slip_no" id="src_transaction_slip_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_transaction_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration From Date:</td>
									<td align="left">
										<input type="date" name="src_registration_from_date" id="src_registration_from_date" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_from_date']?>" />
									</td>
									<td align="left">Registration To Date:</td>
									<td align="left">
										<input type="date" name="src_registration_to_date" id="src_registration_to_date" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_to_date']?>" />
									</td>
								</tr>
							</table>
							</form>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</td>
									<td align="left">Name & Contact</td>
									<td width="110" align="left">Registration Type</td>
									<td width="180" align="left">Registration Details</td>
									<td width="460" align="center">Service Dtls</td>
									<td width="90" align="center">Action</td>
								</tr>
							</thead>
							<tbody>
								<?php
								
								$alterCondition = "";							    
								$sqlFetchUser         = "";
								
								$idArr = getAllDelegates("","",$alterCondition);
								
								if($idArr)
								{									
									foreach($idArr as $i=>$id) 
									{
										$status = true;
										$rowFetchUser = getUserDetails($id);
										$counter      = $counter + 1;
										$color = "#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}										
										$totalAccompanyCount = 0;
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_full_name'])?> 
												<?php
												if($rowFetchUser['suggested_by']!='')
												{
												?>
													<br><span style="color:#7b0f75;">Sugg. By:<br /> <?=$rowFetchUser['suggested_by']?>	</span>
												<?
												}
												?>
												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												NOTES:
												<?=$rowFetchUser['user_food_preference_in_details']?>
											<?
												$array = $rowFetchUser['tags'];
												$var = (explode(",",$array));
												foreach($var as $key=>$val)
												{
													
													if($val =='Executive Committee')
													{
													?>
														<span style="color:#990033;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}
													if($val =='Organizing Committee')
													{
													?>
														<span style="color:#009966;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}
													if($val =='Guest Faculty')
													{
													?>
														<span style="color:#CC3333;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}
													if($val =='Special Faculty')
													{
													?>
														<span style="color:#FF0066;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}
													if($val =='Regional Faculty')
													{
													?>
														<span style="color:#007700;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}
													if($val =='National Faculty')
													{
													?>
														<span style="color:#660066;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}	
													
													if($val =='International Faculty')
													{
													?>
													<span style="color:#770000;"><b><?=$val?></b></span>
													<br>
													<?
													}
													if($val =='Special Guest')
													{
													?>
														<span style="color:#663399;"><b><?=$val?></b>&nbsp;</span>
														<br>
													<?
													}
													
												}
												
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
												
												}
												?>
											</td>											
											<td align="left" valign="top">
											<span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>;"><b><?=$rowFetchUser['registration_request']?></b></span>
												<br />
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Reg Id : ".$rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Us No : ".strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												$totalPaid = 0;
												$totalUnpaid = 0;
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
												<?
												if($rowFetchUser['reg_type'] == "BULK")
												{
												    $sqlFetchExhibitorCommitmentSlip = array();
													$sqlFetchExhibitorCommitmentSlip['QUERY']	=	" SELECT exb.exhibitor_company_name
																								FROM "._DB_BLUK_REGISTRATION_DATA_." dta
																						  INNER JOIN isar_exhibitor_company exb
																								  ON exb.id = dta.exhibitor_company_id
																							   WHERE RIGHT(TRIM(dta.errorComments), 4) = '".$rowFetchUser['id']."'
																								 AND dta.status = 'INSERT'
																								 AND exb.status='A'";	
													$exhibitorCommitmentSlip	=	$mycms->sql_select($sqlFetchExhibitorCommitmentSlip, false);
													echo'<br><span style="color:#CC66FF;">'.strtoupper($exhibitorCommitmentSlip[0]['exhibitor_company_name']).'</span>';
												}
												?>
											</td>
											<td align="left" valign="top" use="registrationDetailsList" userId="<?=$rowFetchUser['id']?>">
												<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" />
											</td>
											<!--<td align="center" valign="top">Payment</td-->
											<td align="center" valign="top">
											<a onclick="openDetailsPopup(<?=$rowFetchUser['id']?>);"><span title="View" class="icon-eye" /></a>
											<a href="registration.php?show=invoice&id=<?=$rowFetchUser['id']?>"><span title="Invoice" class="icon-book"/></a>
											<?php											
											if($loggedUserId=='1' || $loggedUserId=='6'  || $loggedUserId=='4')
											{
											?>	
												<a href="registration.php?show=AskToRemove&id=<?=$rowFetchUser['id']?>">
												<span alt="Remove" title="Remove" class="icon-trash-stroke"/>
												</a>													
											<?php
											}
											$invoice = countOfInvoiceDelegatePlusAccompany($rowFetchUser['id']);
											$dinnerInvoice = countOfDinnerInvoices($rowFetchUser['id']); 
											//$totalDinnerCount   =  getTotalWorkshopCount($rowFetchUser['id']);
											//if($invoice > $dinnerInvoice  &&  $rowFetchUser['registration_payment_status']!= 'UNPAID')
//											{
											?>
												<!--<a href="registration.php?show=addDinner&id=<?=$rowFetchUser['id'] ?>">
												<span title="Apply Dinner" class="icon-award-stroke" /></a>-->
											<?php
											//}
											$totalAccompanyCount   = getTotalAccompanyCount($rowFetchUser['id']);
											
											if($totalAccompanyCount<=4 &&  $rowFetchUser['registration_payment_status']!= 'UNPAID')
											{
											?>
												<a href="registration.php?show=addAccompany&id=<?=$rowFetchUser['id'] ?>">
													<span title="Add Accompany" class="icon-user" /></a>
											<?php
											}											
											?>
											</td>
										</tr>
								<?php
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
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	}
	
	function registrationStep1Template($requestPage, $processPage, $registrationRequest="GENERAL")
	{ 
	
		global $cfg, $mycms;
		
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.workshop.php');
		
		$sqlConfDate = array();
		$sqlConfDate['QUERY']    = " SELECT MIN(conf_date) AS startDate, MAX(conf_date) AS endDate
									   FROM "._DB_CONFERENCE_DATE_." 
									  WHERE `status` = ?";		
		$sqlConfDate['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
												  
		$resConfDate = $mycms->sql_select($sqlConfDate);
		
		$rowConfDate = $resConfDate[0];
		


		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = " SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
									WHERE `status` != ? 
								 ORDER BY `cutoff_sequence` ASC";		
		$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');												  
		$resCutoff = $mycms->sql_select($sqlCutoff);		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$currentCutoffId = getTariffCutoffId();
		
		$conferenceTariffArray   = getAllRegistrationTariffs();
		
		$workshopDetailsArray 	 = getAllWorkshopTariffs();
		$workshopCountArr 		 = totalWorkshopCountReport();	
		
		?>
		
		<script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
		<script type="text/javascript" language="javascript" src="<?=_BASE_URL_?>webmaster/section_login/scripts/CountryStateRetriver.js"></script>
		<script>
			$(document).ready(function(){
				$('#bttnSubmitStep1').click(function(){
					var validaCheck = $("input[type=checkbox][operationmode=validateCheck]:checked").length;
						if(validaCheck == 0){
							 $('#frmRegistrationStep1').find('input[implementvalidate="y"], select[implementvalidate="y"]').prop('required', false);
						}
				});
					
			});
		</script>
		<form name="frmRegistrationStep1" id="frmRegistrationStep1" action="<?=$cfg['SECTION_BASE_URL'].$processPage?>" 
		 enctype="multipart/form-data" method="post" onsubmit="return mainRegistrationCalidation();">
			<input type="hidden" name="act" value="step1" />
			<input type="hidden" name="reg_area" value="BACK" />
			<input type="hidden" name="reg_type" value="BACK" />
			<input type="hidden" name="registration_request" id="registration_request" value="<?=$registrationRequest?>" />
			<?php if($_REQUEST['COUNTER']=='Y'){ ?>
				<input type="hidden" name="counter" id="counter" value="Y" />
			<?php } ?>	
			<table width="100%" align="center" class="tborder">  
				<thead> 
					<tr> 
							<td colspan="2" align="left" class="tcat">Delegate Registration
								<input type="checkbox" style="float:right;" checked="checked" operationmode="validateCheck">
							</td> 
					</tr> 
				</thead> 
				<tbody> 
					<tr>  
						<td colspan="2" style="margin:0px; padding:0px;">    
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Conference <?=$mycms->cDate('F d', $rowConfDate['startDate'])?> - <?=$mycms->cDate('F d, Y', $rowConfDate['endDate'])?></td>
								</tr>								
								<tr>
									<td width="20%" align="left" valign="top">Select Cutoff <span class="mandatory">*</span></td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<select id="registration_cutoff" name="registration_cutoff" operationMode='regCutoff' style="width:30%;" required >
											<option value="">-- Select Cutoff --</option>
											<?
											if($cutoffArray)
											{
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{
												?>
													<option value="<?=$cutoffId?>" <?=($currentCutoffId==$cutoffId)?"selected":""?>><?=$cutoffName?></option>
												<?
												}
											}
											?>
										</select>
									</td>
								</tr>								
								<tr>
									<td width="20%" align="left" valign="top">Registration Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%" use="registration_tariff">
											<tr class="theader">
												<td>Registration Classification</td>
												<?
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{	
													if($cutoffId !='4') 
													{
												?>
													<td align="right" style="width: 180px;"><?=strip_tags($cutoffName)?></td>
												<?
													}
												}
												?>
											</tr>
											<?php												
											$residentialAccommodationPackageId = $cfg['RESIDENTIAL_PACKAGE_ARRAY'];																					
											if($conferenceTariffArray)
											{
												foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
												{
													$styleCss = 'style=""';
													$classificationType = getRegClsfType($key);
													
													if($classificationType !='ACCOMPANY' && ($classificationType !='COMBO' || $key == 3) )
													{
													?>
													<tr class="tlisting" <?=$styleCss?>>
														<td align="left">
														
															<input type="checkbox" name="registration_classification_id[]" operationMode="registration_tariff" 
																   value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" 
																   registrationType="<?=$classificationType?>" accommodationPackageId = "<?=$residentialAccommodationPackageId[$key]?>"/>
															&nbsp;&nbsp;&nbsp;
															
															<?=getRegClsfName($key)?>
														</td>
														<?
														
														foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
														{
															if($keyCutoff !='4') 
															{
															
																$RegistrationTariffDisplay = $rowCutoff['CURRENCY']."&nbsp;".$rowCutoff['AMOUNT'];
																if($rowCutoff['AMOUNT']<=0)
																{
																	if($classificationType == 'FULL_ACCESS')
																	{
																		$RegistrationTariffDisplay = "Complimentary";
																	}
																	else
																	{
																		$RegistrationTariffDisplay = "Zero Value";
																	}
																}
														?>
															<td align="right" use="registrationTariff" cutoff="<?=$keyCutoff?>" tariffAmount="<?=$rowCutoff['AMOUNT']?>" tariffCurrency="<?=$rowCutoff['CURRENCY']?>"><?=$RegistrationTariffDisplay?></td>
														<?php
															}
														}
														?>
													</tr>
													<?	
													}	
												}
											}
											else
											{
											?>
												<tr>
													<td colspan="<?=sizeof($cutoffArray)+1?>" align="center" >
														<strong style="color:#FF0000;">Classification not set</strong>
													</td>	
												</tr>
											<?
											}
											?>
										</table>
									</td>
								</tr>
								<tr> 
									<td width="20%" align="left" valign="top"></td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder"> 
										<table width="100%" use="registration_tariff">
											<tr class="theader"> 
												<td>Residential Registration Classification</td>
												<td colspan="2" align="right">Choose Hotel</td>
												<td> 
													<select operationMode="hotel_select_id" name="hotel_id"> 
														<?php
														$sqlHotel['QUERY']	 	= "SELECT * 
																					 FROM "._DB_MASTER_HOTEL_."
																					WHERE status = ?";
														$sqlHotel['PARAM'][]    = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
														$resHotel		    	= $mycms->sql_select($sqlHotel);
														foreach($resHotel as $key=> $rowHotel)
														{
														?>
															<option value="<?=$rowHotel['id']?>"><?=$rowHotel['hotel_name']?></option>
														<?php
														}		
														?>
													</select>
												</td> 
											</tr> 
											<?php	
											//print_r($conferenceTariffArray);
											$residentialAccommodationPackageId = $cfg['RESIDENTIAL_PACKAGE_ARRAY'];																					
											if($conferenceTariffArray)
											{
												$reghotel_id = "";
												foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
												{
													$styleCss = 'style=""';
													$classificationType = getRegClsfType($key);
													$RegClsfDetails = getRegClsfDetails($key);
													$reghotel_id = $RegClsfDetails['residential_hotel_id'];
													if($classificationType !='ACCOMPANY' && ($classificationType !='DELEGATE' && $key != 3))
													{
													?>
													<tr class="tlisting" <?=$styleCss?> operetionMode="residenTariffTr" hotel_id="<?=$reghotel_id?>"> 
														<td align="left"> 														
															<input type="checkbox" name="registration_classification_id[]" operationMode="registration_tariff" 
																   value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" 
																   registrationType="<?=$classificationType?>" 
																   accommodationPackageId = "<?=$residentialAccommodationPackageId[$key]?>"/>
															&nbsp;&nbsp;&nbsp;
															<?=getRegClsfName($key)?>
														</td> 
														<?
														foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
														{
															if($keyCutoff !='4') 
															{															
																$RegistrationTariffDisplay = $rowCutoff['CURRENCY']."&nbsp;".$rowCutoff['AMOUNT'];
																if($rowCutoff['AMOUNT']<=0)
																{
																	if($classificationType == 'FULL_ACCESS')
																	{
																		$RegistrationTariffDisplay = "Complimentary";
																	}
																	else
																	{
																		$RegistrationTariffDisplay = "Zero Value";
																	}
																}
														?>
															<td align="right" use="registrationTariff" cutoff="<?=$keyCutoff?>" 
																tariffAmount="<?=$rowCutoff['AMOUNT']?>" tariffCurrency="<?=$rowCutoff['CURRENCY']?>"><?=$RegistrationTariffDisplay?></td>
														<?php
															}
														}
														?>
													</tr> 
													<?	
													}	
												}
											}
											else
											{
											?>
												<tr> 
													<td colspan="<?=sizeof($cutoffArray)+1?>" align="center" > 
														<strong style="color:#FF0000;">Classification not set</strong> 
													</td> 	
												</tr> 
											<?
											}
											?>
										</table>
									</td> 
								</tr>
								<tr> 
									<td width="20%" align="left" valign="top"></td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder"> 
										<table width="100%">										
											<?php
											$accommodationDetails = $cfg['ACCOMMODATION_PACKAGE_ARRAY'];											
											foreach($accommodationDetails as $packageId=>$rowAccommodation)
											{
											?>
											<tr use="<?=$packageId?>" operetionMode="checkInCheckOutTr" style="display:none;">
												<td width="20%">
													CHECK IN - CHECK OUT DATE :
													<input type="hidden" name="accommodation_package_id" id="accommodation_id" value="" />
													<select name="accDate[<?=$packageId?>]" operationMode="accomodationPackage">
			
													<?
													foreach($rowAccommodation as $seq=>$accPackDet)
													{
													?>
													<option checkInDate="<?=$accPackDet['STARTDATE']['ID']?>" checkOutDate ="<?=$accPackDet['ENDDATE']['ID']?>" value="<?=$accPackDet['STARTDATE']['ID']?>-<?=$accPackDet['ENDDATE']['ID']?>"><?=$accPackDet['STARTDATE']['DATE']?> to <?=$accPackDet['ENDDATE']['DATE']?></option>
													<?
													}
													?>
												</select>
												<input type="hidden" name="accommodation_checkIn" 	 id="accommodation_checkIn_date" value="<?=$rowAccommodation['STARTDATE']['DATE']?>" />
												<input type="hidden" name="accommodation_checkOut"   id="accommodation_checkOut_date" value="<?=$rowAccommodation['ENDDATE']['DATE']?>" />
												</td>
											</tr>
											<?php
											}
											?>
										</table>
									</td>
								</tr>
							</table>
							
							
							
							<table width="100%">
								<tr>
									<td width="20%" align="left" valign="top">Workshop Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%">
											<tr class="theader">
												<td align="left">Workshop</td>
												<?
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{	
													if($cutoffId !='4') 
													{
												?>
													<td align="right" style="width: 180px;"><?=strip_tags($cutoffName)?></td>
												<?
													}
												}
												?>
											</tr>											
											<?php											
											 if(sizeof($workshopDetailsArray)>0)
											 {
												 foreach($workshopDetailsArray as $keyWorkshopclsf=>$rowWorkshopclsf)
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
															<td align="left">
																<div class="tooltip">
																	<?=$span?>
																	<input type="checkbox" operationMode='workshopId'  <?=$style?> name="workshop_id[]" id="workshop_id" value="<?=$keyWorkshopclsf?>" />
																</div>
																&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?>
															</td>
															<?
															foreach($rowRegClasf as $keyCutoff=>$cutoffvalue)
															{
																if($keyCutoff !='4') 
																{
																	$WorkshopTariffDisplay = $cutoffvalue['CURRENCY']."&nbsp;".$cutoffvalue[$cutoffvalue['CURRENCY']];
																	if($cutoffvalue[$cutoffvalue['CURRENCY']]<=0)
																	{
																		$WorkshopTariffDisplay = "Included in Registration";
																	}
															?>
															<td align="right" use="workshopTariff" cutoff="<?=$keyCutoff?>" tariffAmount="<?=$cutoffvalue[$cutoffvalue['CURRENCY']]?>" tariffCurrency="<?=$cutoffvalue['CURRENCY']?>"><?=$WorkshopTariffDisplay?></td>
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
												<td align="center" colspan="<?=sizeof($cutoffArray)+1?>"><strong style="color:#FF0000;">Please Select Registration Classification First</strong></td>
											</tr>
											<tr use="gs" operetionMode="workshopTariffTr"  align="center" style="display:none;">
											<td colspan="<?=$keyCutoff +1?>" align="center" >
												<strong style="color:#FF0000;">Workshop not available for this user type</strong>
											</td>	
											</tr>
											<tr use="ai" operetionMode="workshopTariffTr"  align="center" style="display:none;">
												<td colspan="<?=$keyCutoff +1?>" align="center" >
													<strong style="color:#FF0000;">All workshop are included</strong>
												</td>	
											</tr>				
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
							</table>		
							
							
							
							<table width="100%">
								<tr>
									<td width="20%" align="left" valign="top">Dinner</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%">
											<?
											$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
											
							
											foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
											{
											?>
											<tr use="all" operetionMode="dinnerTariffTr"  align="center">
												<td align="left"  width="5%" valign="top"> <input type="checkbox" name="dinner_value[]" id="dinner_value" 
												   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
												   operationMode="dinner"  tariffAmount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"/></td><td width="53%" align="left" valign="top"> <?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> </td>
							
												<td align="right" use="dinnerTariff" cutoff="<?=$dinnerValue[$currentCutoffId]['CUTOFF_ID']?>"  tariffAmount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>">INR <?=number_format(floatval($dinnerValue[$currentCutoffId]['AMOUNT']),2)?></td>
											</tr>
											<tr use="ai" operetionMode="dinnerTariffTr"  align="center" style="display:none;">
												<td align="left"  width="5%" valign="top"> <input type="checkbox" name="dinner_value[]" id="dinner_value" 
												   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
												   operationMode="dinner"  ammount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>" disabled="disabled"/></td><td width="53%" align="left" valign="top"> 
												   	<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> 
												</td>
												<td align="right"><strong style="color:#FF0000;">Included in Offer</strong></td>	
											</tr>
											<?php
											}
											?>
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
									<td colspan="4" align="left">Login Details <span class="mandatory" style="font-weight:normal; float:right;">[*=mandatory]</span></td>
								</tr>
								<tr>
									<td width="20%" align="left" valign="top">
										Email Id 
										<span class="mandatory">*</span>
									</td>
									<td width="30%" align="left" valign="top">
										<input type="email" name="user_email_id" id="user_email_id" forType="emailValidate" style="width:90%; text-transform:lowercase;" tabindex="1" required/>
										<input type="hidden" name="email_id_validation" id="email_id_validation" />
										<div id="email_div"></div>
									</td>
									<td align="left" width="20%">
										Mobile No
										<span class="mandatory">*</span>
									</td>
									<td align="left" width="30%" >
										<input type="tel" name="user_usd_code" id="user_mobile_isd_code" style="width:30px; text-align:right;" value="+91" tabindex="2" required/> - 
										<input type="tel" name="user_mobile" id="user_mobile_no" forType="mobileValidate" style="width:70%;" pattern="^\d{10}$" tabindex="3" required/>
										<input type="hidden" name="mobile_validation" id="mobile_validation" />
										<div id="mobile_div"></div>
									</td>
								</tr>
							</table>
							
							<!-- User Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details <span class="mandatory" style="font-weight:normal; float:right;">[*=mandatory]</span></td>
								</tr>
								<tr>
									<td width="20%" align="left">Title <span class="mandatory">*</span></td>
									<td width="30%" align="left">
										<select name="user_initial_title" id="user_initial_title" style="width:90%;" tabindex="4" required>
											<option value="Dr" selected="selected">Dr</option>
											<option value="Prof">Prof</option>
											<option value="Mr">Mr</option>
											<option value="Ms">Ms</option>											
										</select>
									</td>
									<td width="20%" align="left" rowspan="2">Address</td>
									<td width="30%" align="left">
										<textarea name="user_address" id="user_address" tabindex="10"
										 style="height:75px; width:90%; text-transform:uppercase;"></textarea>
									</td>
								</tr>
								<tr>
									<td align="left">First Name <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_first_name" id="user_first_name" 
										 style="width:90%; text-transform:uppercase;" tabindex="5" required />
										 
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Middle Name</td>
									<td align="left">
										<input type="text" name="user_middle_name" id="user_middle_name" 
										 style="width:90%; text-transform:uppercase;"  implementvalidate="y" tabindex="6" />
									</td>
									<td align="left">Country</td>
									<td align="left">
										<select required implementvalidate="y" name="user_country" id="user_country" style="width:90%;" forType="countryState"  
												stateId="user_state" onchange="stateRetriver(this);" tabindex="11"
										 sequence="1">
											<? getCountryList("1") ?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left">Last Name</td>
									<td align="left">
										<input type="text" name="user_last_name" id="user_last_name" tabindex="7"
										 style="width:90%; text-transform:uppercase;" required implementvalidate="y"/>
									</td>
									<td align="left">Select State</td>
									<td align="left">
										<div use='stateContainer'>
											<select name="user_state" id="user_state" style="width:90%;" forType="state" tabindex="12"
											 		sequence="1" disabled="disabled" required implementvalidate="y">
												<option value="">-- Select Country First --</option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td align="left">Gender <span class="mandatory">*</span></td>
									<td align="left">
										<input type="radio" name="user_gender" id="user_gender_male" 
										 checked="checked" value="MALE" tabindex="8" required/> Male										 
										<input type="radio" name="user_gender" id="user_gender_female" 
										 value="FEMALE" tabindex="9" required/> Female
									</td>
									<td align="left">Enter City </td>
									<td align="left">
										<input type="text" name="user_city" id="user_city" tabindex="13"
										 style="width:90%; text-transform:uppercase;" required implementvalidate="y"/>
									</td>
								</tr>
								<tr>
									<!--<td align="left">Phone No</td>
									<td align="left">
										<input type="text" name="user_phone" id="user_phone" 
										 style="width:90%; text-transform:uppercase;" />
									</td>-->
									<td align="left">Postal Code</td>
									<td align="left">
										<input type="text" name="user_postal_code" id="user_postal_code" tabindex="14"
										 style="width:90%; text-transform:uppercase;" required implementvalidate="y"/>
									</td>
									<td align="left" valign="top">Food Preference</td>
									<td align="left" valign="top">									
										<input type="radio" name="user_food_preference" id="user_food_preference_veg" 
										 checked="checked" value="VEG" tabindex="15" /> Veg										
										<input type="radio" name="user_food_preference" id="user_food_preference_non_veg" 
										 value="NON VEG" tabindex="16" /> Non Veg 									
									</td>
								</tr>
								
							</table>
							
							<!-- Other Details -->
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Other Details <span class="mandatory" style="font-weight:normal; float:right;">[*=mandatory]</span></td>
								</tr>
								
								<tr>
									<!--<td align="left" valign="top">Institution</td>
									<td align="left" valign="top">
										<input type="text" name="user_institution" id="user_institution" 
										 style="width:90%; text-transform:uppercase;" />
									</td>-->	
									
								<!--	<td align="left" valign="top">Food Preference</td>
									<td align="left" valign="top">									
										<input type="radio" name="user_food_preference" id="user_food_preference_veg" 
										 checked="checked" value="VEG" /> Veg										
										<input type="radio" name="user_food_preference" id="user_food_preference_non_veg" 
										 value="NON VEG" /> Non Veg 									
									</td>
								</tr>-->
								<tr>
									<!--<td width="20%" align="left" valign="top">Department</td>
									<td width="30%" align="left" valign="top">
										<input type="text" name="user_depertment" id="user_depertment" 
										 style="width:90%; text-transform:uppercase;" />
									</td>-->
																
									<td width="20%" align="left" valign="top"></td>
									<td width="30%" align="left" valign="top" rowspan="2">
										<input type="text" name="user_food_details" id="user_food_details" tabindex="17"
							 			 style="width:90%; text-transform:uppercase;" placeholder='notes...'/>
									</td>
								</tr>
								<!--<tr>	
									<td width="20%" align="left" valign="top">Designation</td>
									<td width="30%" align="left" valign="top">
										<input type="text" name="user_designation" id="user_designation" 
										 style="width:90%; text-transform:uppercase;" />
									</td>		
									<td width="20%" align="left" valign="top"></td>
								</tr>-->
								
							</table>
							
							<table width="100%">
								<tr class="paymentArea">
									<td align="left" width="20%">
										<div >
											Total Amount
											<span style="color:#FFF;">
												 <span class="registrationPaybleAmount" use="TOTCUR">INR</span>
												 <span class="registrationPaybleAmount" use="TOTAMT">0.00</span>
											</span>
										</div>
									</td>
									<td align="right">
										<input type="submit" name="bttnSubmitStep1" operationmode="bttnSubmitStep1" id="bttnSubmitStep1" value="<?=($isComplementary != 'Y')?"Submit":"Proceed"?>" 
										 class="btn btn-blue" />
									</td>
								</tr>
							</table>
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
	
	function registrationAccompanyTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		//print_r( $mycms->getSession('USER_DETAILS'));
		include_once('../../includes/function.registration.php');
		
		$cutoffArray  = array();
	
		$sqlCutoff['QUERY']    = " SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
									WHERE `status` != ? 
								 ORDER BY `cutoff_sequence` ASC";		
		$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');												  
		$resCutoff = $mycms->sql_select($sqlCutoff);	
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$USER_DETAILS 		= $mycms->getSession('USER_DETAILS');
//print_r($USER_DETAILS);
		$delagateName 		= $USER_DETAILS['NAME'];
		$delagateEmail 		= $USER_DETAILS['EMAIL'];
		$delagateMobile 	= $USER_DETAILS['PH_NO'];
		$delagateCutoff 	= $USER_DETAILS['CUTOFF'];
		$delagateCatagory	= $USER_DETAILS['CATAGORY'];
		$accompanyCatagory = 2;
		?>
		<form name="frmApplyForAccompany" id="frmApplyForAccompany"  action="<?=$processPage?>" method="post" onsubmit="return formAccompanyValidation();">
			<input type="hidden" name="act" value="step3" />
			<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
			<input type="hidden" name="userREGtype" value="<?=$_REQUEST['userREGtype']?>" />
			<input type="hidden" name="abstractDelegateId" value="<?=$_REQUEST['abstractDelegateId']?>" />
			<?php if($_REQUEST['COUNTER']=='Y'){ ?>
				<input type="hidden" name="counter" id="counter" value="Y" />
			<?php } ?>
			<?
			$registrationDetails = getAllRegistrationTariffs();
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
															<span style="color:#cc0000;"><?=getRegistrationCurrency($delagateCatagory)?> <span id="amount" class="registrationPaybleAmount">0.00</span></span>
															<?php
														}
														else
														{
															?>
															<span style="color:#FFF;"><?=getRegistrationCurrency($delagateCatagory)?> 0.00</span>
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
												<?php if($_REQUEST['COUNTER']=='Y'){?>	
												<input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
														 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
														 onclick="window.location.href = 'registration.php?show=step6&COUNTER=Y'"  />
												<?php }
												else{
												?>
													<input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
														 class="btn btn-small btn-green" style=" float:right; margin-right:5%;"
														 onclick="window.location.href = 'registration.php?show=step6'"  />
												<?php }
												?>	
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
	
	function registrationSummeryTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		//die("hi");
		global $cfg, $mycms;
		
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
		
		$slipId	 			= $mycms->getSession('SLIP_ID');
		
		$sqlSlip = array();
		$sqlSlip['QUERY']		= "SELECT * FROM "._DB_SLIP_." 
										WHERE `status` = ? 
										AND `id` = ?";
										
		$sqlSlip['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',     'TYP' => 's');
		$sqlSlip['PARAM'][]   = array('FILD' => 'id',     'DATA' =>$slipId, 'TYP' => 's');
		
		$resSlip			= $mycms->sql_select($sqlSlip);
		
		$updateReg       = array();
		$updateReg['QUERY']       = "UPDATE "._DB_SLIP_." 
										   SET `reg_type` = ? 
										 WHERE `id`     = ?"; 
										 
		$updateReg['PARAM'][]   = array('FILD' => 'reg_type', 'DATA' =>'BACK',     'TYP' => 's');
		$updateReg['PARAM'][]   = array('FILD' => 'id',     'DATA' =>$slipId, 'TYP' => 's');
			
		$updateReg = $mycms->sql_update($updateReg);
				
		$rowSlip			= $resSlip[0];
		
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		//print_r($userDetails);
		?>
		<script>
		function validateRegistrationSummary(obj)
		{
			return onSubmitAction(function(){ 
			var parent = $(obj).find("table").first();
			var discount = $(parent).find("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
			if(discount>0)
			{		
				if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 	
					return false;
				}	
				
				var discountAmount = $("input[type=text][operationMode=discountAmount]");	
				var discountAmountVal = $("input[type=text][operationMode=discountAmount]").val();	
				var total = $("input[type=hidden][name=totalAmount]").val();	
				
				if(isNaN(discountAmountVal))
				{
					alert("Enter Discount Amount correctly");
					$(discountAmount).focus();
					return false;
				}
				
				if(parseFloat(total) <  parseFloat((discountAmount).val()))
				{
					alert("Enter Discount Amount correctly");
					$(discountAmount).focus();
					status = false;
					return false;
				}
			}
			return 	status;
			});
		}
		</script>
		<script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
		<form name="frmApplyPayment" id="frmApplyPayment"  action="<?=$processPage?>" method="post" onsubmit="return validateRegistrationSummary(this);">
			<input type="hidden" name="act" value="setPaymentTerms" />
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
												<?=$invoiceDetails['service_roundoff_price'] != 0?$invoiceDetails['currency'] : ''?> &nbsp;&nbsp;
												<?=$invoiceDetails['service_roundoff_price'] != 0 ? $invoiceDetails['service_roundoff_price'] : 'Inclusive'?>
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
										<input type="hidden" name="totalAmount" value="<?=invoiceAmountOfSlip($rowSlip['id'])?>" operationMode="totalAmount" totalAmount="<?=invoiceAmountOfSlip($rowSlip['id'])?>"/>
									</td>
								</tr>
							</table>	
							<?php
							if(number_format(invoiceAmountOfSlip($rowSlip['id']),2)!=0)
							{
							?>
							<table width="100%">
								<tr>
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
								</tr>
							</table>
							<?php
							}
							?>
							
							<table width="100%">
								<tr>
									<td align="left" width="20%" colspan="2">
										<div class="paymentArea">
											Total Amount
											<span style="color:#FFF;">
												<?=$rowSlip['currency']?> 
												 <span class="registrationPaybleAmount" id="reg_amount"><?=invoiceAmountOfSlip($rowSlip['id'])?></span>
											</span>
										</div>
									</td>
								</tr>
							</table>
							
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
		
		<script>
			$(document).ready(function(){
				CalculateTotalAmountForRegistrationSummery();
					
			});
			$("input[operationMode=discountCheckbox]").click(function(){
				var serviceType = $('input[type=hidden][name=act]').attr('value');		
					//alert(serviceType);
					if(serviceType == 'setPaymentTerms')
					{
						CalculateTotalAmountForRegistrationSummery();
					}
			
			});
			$("input[operationMode=discountAmount]").keyup(function(){
						var serviceType = $('input[type=hidden][name=act]').attr('value');		
						//alert(serviceType);
						if(serviceType == 'setPaymentTerms')
						{
							CalculateTotalAmountForRegistrationSummery();
						}
				});
			function CalculateTotalAmountForRegistrationSummery()
			{
				var total = 0;
				var totalSlipAmount = 0;
				var discountAmount = 0;
				var postDiscountAmount = 0;
									
				totalSlipAmount = $("input[type=hidden][operationMode=totalAmount]").attr('totalAmount'); 
				
				var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
				discountAmount = $("input[type=text][operationMode=discountAmount]").val();
				if(discount>0 && !isNaN(discountAmount) && discountAmount>0)
				{	
					 postDiscountAmount =  (parseFloat(totalSlipAmount) -  parseFloat(discountAmount));
					
					total = parseFloat(postDiscountAmount);
					
				}
				else
				{						
					total = parseFloat(totalSlipAmount);
				}
				
				
				if(isNaN(total))
				{
					var total = 0;
				}
				$("#reg_amount").text("");
				$("#reg_amount").text(total.toFixed(2));
			}
		</script>
	<?php
	}
	
	function regPaymnetAreaTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		include_once('../../includes/function.registration.php');
		
		
		$slipId	 			= $mycms->getSession('SLIP_ID');
		$sqlSlip = array();
		$sqlSlip['QUERY']		= "SELECT * FROM "._DB_SLIP_." 
										WHERE `status` = ? 
										AND `id` = ?";
										
		$sqlSlip['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',     'TYP' => 's');
		$sqlSlip['PARAM'][]   = array('FILD' => 'id',     'DATA' =>$slipId, 'TYP' => 's');
		
		$resSlip			= $mycms->sql_select($sqlSlip);
		$rowSlip            = $resSlip[0];
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		?>
		<script type="text/javascript" language="javascript" src="scripts/registration.paymentArea.js"></script>
		<form name="frmApplyRegistration" id="frmApplyRegistration"  action="<?=$processPage?>" onsubmit="return multiPaymentValidation(this)" method="post">
			<input type="hidden" name="act" value="setPaymentArea" />
			<input type="hidden" name="slip_id" value="<?=$slipId?>" />
			<input type="hidden" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
			<input type="hidden" name="slipAmount" value="<?=invoiceAmountOfSlip($slipId);?>" />
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Set Payment Terms</td>
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
									<td width="20%" align="left">Total Slip Amount</td>
									<td width="30%" align="left"><?=invoiceAmountOfSlip($slipId);?></td>
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
						</td>
					</tr>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Payment Record (Pending Amount :: <span use="pending_amount"><?=invoiceAmountOfSlip($slipId);?></span>)</td>
								</tr>
								<tr>
									<td colspan="4" align="left">
									<div id="addMorePayment_placeholder"></div>	
									</td>
								</tr>	
							</table>				
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<a onClick="addMorePayment('addMorePayment_placeholder','addMorePayment_template')" style="float:left;" 
								 class="btn btn-warning viobtn slow" operationmode="addPaymentButton">Add More Payment
							</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Proceed" 
										 class="btn btn-midium btn-blue" style="float:right; margin-right:10%;" operationMode="registrationMode"   />
										 
							 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
											 class="btn btn-midium btn-grey" style=" float:right; margin-right:5%;"
											 onclick="window.location.href = 'registration.php'"  />
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
			seperatePaymentArea();
		?>
	<?php
	}
	
	function regAdditionalPaymnetAreaTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		include_once('../../includes/function.registration.php');
		
		$encodedId = $_REQUEST['sxxi'];
		$slipId = base64_decode($encodedId);
		$sqlSlip = array();
		$sqlSlip['QUERY']		= "SELECT * FROM "._DB_SLIP_." 
										WHERE `status` = ? 
										AND `id` = ?";
										
		$sqlSlip['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',     'TYP' => 's');
		$sqlSlip['PARAM'][]   = array('FILD' => 'id',     'DATA' =>$slipId, 'TYP' => 's');
		
		$resSlip			= $mycms->sql_select($sqlSlip);
		$rowSlip            = $resSlip[0];
		$userDetails		= getUserDetails($rowSlip['delegate_id']);
		$totalSetPaymentAmount = getTotalSetPaymentAmount($slipId) 
		?>
		<script type="text/javascript" language="javascript" src="scripts/registration.paymentArea.js"></script>
		<form name="frmApplyRegistration" id="frmApplyRegistration"  action="<?=$processPage?>" onsubmit="return multiPaymentValidation(this)" method="post">
			<input type="hidden" name="act" value="setPaymentArea" />
			<input type="hidden" name="slip_id" value="<?=$slipId?>" />
			<input type="hidden" name="delegate_id" value="<?=$rowSlip['delegate_id']?>" />
			<input type="hidden" name="slipAmount" value="<?=invoiceAmountOfSlip($slipId);?>" />
			<input type="hidden" name="totalSetPaymentAmount" value="<?=$totalSetPaymentAmount?>" />
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Set Payment Terms</td>
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
									<td width="20%" align="left">Total Slip Amount</td>
									<td width="30%" align="left"><?=invoiceAmountOfSlip($slipId);?></td>
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
						</td>
					</tr>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Payment Record (Pending Amount :: <span use="pending_amount"><?=number_format(pendingAmountOfSlip($slipId),2);?></span>)</td>
								</tr>
								<tr>
									<td colspan="4" align="left">
									<div id="addMorePayment_placeholder"></div>	
									</td>
								</tr>	
							</table>				
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<a onClick="addMorePayment('addMorePayment_placeholder','addMorePayment_template')" style="float:left;" 
								 class="btn btn-warning viobtn slow" operationmode="addAccompanyButton">Add More Payment
							</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Proceed" 
										 class="btn btn-midium btn-blue" style="float:right; margin-right:10%;" operationMode="registrationMode"   />
										 
							 <input type="button" name="bttnAdd" id="bttnAdd" value="Skip" 
											 class="btn btn-midium btn-grey" style=" float:right; margin-right:5%;"
											 onclick="window.location.href = 'registration.php'"  />
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<?php
			seperatePaymentArea();
		?>
	<?php
	}
	
	function seperatePaymentArea()
	{
		global $cfg, $mycms;
	?>
		<script>
			function amountValidation(obj)
			{
				var parent = $(obj).parent().closest("table[use=thePaymentTable]")
				var slipAmount  = $("input[type=hidden][name=slipAmount]").val();
				
				var totalAmount = 0;
				var pendingAmount = 0;
				
				$.each($("div[id=addMorePayment_placeholder]").find("table[use=thePaymentTable]"),function(){
					var table = $(this);
					var thisAmount = $(table).find("input[type=number][name=amount[]]").val();
					if(isNaN(thisAmount) || thisAmount=='')
					{
						thisAmount = 0;
					}					
					totalAmount += parseInt(thisAmount);
					pendingAmount = parseInt(slipAmount) - parseInt(totalAmount);
				});				
				if(parseInt(totalAmount) > parseInt(slipAmount))
				{
					alert("input amount should be less than slip amount");
					$(obj).val('');
					$(obj).focus();
				}
				$("span[use=pending_amount]").text(pendingAmount);
			}
			function paymentModeRetriver(obj)
			{
				var parent = $(obj).parent().closest("table[use=thePaymentTable]")
				var paymentType = $(obj).val();
				if(paymentType == "Cash")
				{
					$(parent).find("#cashPaymentDiv").css("display","block");
					$(parent).find("#chequePaymentDiv").css("display","none");
					$(parent).find("#draftPaymentDiv").css("display","none");
					$(parent).find("#neftPaymentDiv").css("display","none");						
					$(parent).find("#rtgsPaymentDiv").css("display","none");
					$(parent).find("#cardPaymentDiv").css("display","none");
					$(parent).find("#creditPaymentDiv").css("display","none");
				}
				
				if(paymentType == "Cheque")
				{
					$(parent).find("#cashPaymentDiv").css("display","none");
					$(parent).find("#chequePaymentDiv").css("display","block");
					$(parent).find("#draftPaymentDiv").css("display","none");
					$(parent).find("#neftPaymentDiv").css("display","none");						
					$(parent).find("#rtgsPaymentDiv").css("display","none");
					$(parent).find("#cardPaymentDiv").css("display","none");
					$(parent).find("#creditPaymentDiv").css("display","none");
				}
				
				if(paymentType == "Draft")
				{
					$(parent).find("#cashPaymentDiv").css("display","none");
					$(parent).find("#chequePaymentDiv").css("display","none");
					$(parent).find("#draftPaymentDiv").css("display","block");
					$(parent).find("#neftPaymentDiv").css("display","none");						
					$(parent).find("#rtgsPaymentDiv").css("display","none");
					$(parent).find("#cardPaymentDiv").css("display","none");
					$(parent).find("#creditPaymentDiv").css("display","none");
				}
				
				if(paymentType == "NEFT")
				{
					$(parent).find("#cashPaymentDiv").css("display","none");
					$(parent).find("#chequePaymentDiv").css("display","none");
					$(parent).find("#draftPaymentDiv").css("display","none");
					$(parent).find("#neftPaymentDiv").css("display","block");
					$(parent).find("#rtgsPaymentDiv").css("display","none");
					$(parent).find("#cardPaymentDiv").css("display","none");
					$(parent).find("#creditPaymentDiv").css("display","none");
					
				}
				
				if(paymentType == "RTGS")
				{
					$(parent).find("#cashPaymentDiv").css("display","none");
					$(parent).find("#chequePaymentDiv").css("display","none");
					$(parent).find("#draftPaymentDiv").css("display","none");
					$(parent).find("#neftPaymentDiv").css("display","none");						
					$(parent).find("#rtgsPaymentDiv").css("display","block");
					$(parent).find("#cardPaymentDiv").css("display","none");
					$(parent).find("#creditPaymentDiv").css("display","none");
				}
				if(paymentType == "CARD")
				{
					$(parent).find("#cashPaymentDiv").css("display","none");
					$(parent).find("#chequePaymentDiv").css("display","none");
					$(parent).find("#draftPaymentDiv").css("display","none");
					$(parent).find("#neftPaymentDiv").css("display","none");
					$(parent).find("#rtgsPaymentDiv").css("display","none");
					$(parent).find("#cardPaymentDiv").css("display","block");
					$(parent).find("#creditPaymentDiv").css("display","none");												
				}
				
				if(paymentType == "Credit")
				{
					$(parent).find("#cashPaymentDiv").css("display","none");
					$(parent).find("#chequePaymentDiv").css("display","none");
					$(parent).find("#draftPaymentDiv").css("display","none");
					$(parent).find("#neftPaymentDiv").css("display","none");
					$(parent).find("#rtgsPaymentDiv").css("display","none");
					$(parent).find("#cardPaymentDiv").css("display","none");
					$(parent).find("#creditPaymentDiv").css("display","block");
					$(parent).find("#exhibitorBalMsg").hide();
					$(parent).find("#exhibitorRemainBal").hide();
					$(parent).find("#exhibitorTotalRemainBalMsg").hide();
					$(parent).find("#exhibitorTotalRemainBal").hide();
					
				}
			}
		</script>
										
		<div id="addMorePayment_template" style="display:none;">
			<div operationMode="paymentRow" sequenceBy="#COUNTER" style="margin-bottom:10px;">
				<table width="100%" use="thePaymentTable">										
					<tr>						
						<td width="20%" align="left">Payment Mode <span class="mandatory">*</span></td>
						<td width="30%" align="left">
							<input type="hidden" name="payment_selected[]" value="#COUNTER" />
							<select name="payment_mode[]" id="payment_mode" style="width:95%;" 
							 		onchange="paymentModeRetriver(this)" use="payment_mode">
								<option value="Cash" selected="selected">Cash</option>
								<option value="Cheque">Cheque</option>
								<option value="Draft">Draft</option>
								<option value="NEFT">NEFT</option>
								<option value="RTGS">RTGS</option>
								<option value="CARD">CARD</option>
							</select>
						</td>
						<td width="20%" align="left">Pay Amount <span class="mandatory">*</span></td>
						<td width="30%" align="left">
							<input type="number" name="amount[]" operationMode="amount" value="" onkeyup="amountValidation(this)"/>
						</td>
						<td>
							<span style="float:right;"><a title="Remove" operationMode="removePaymentRow" style="cursor:pointer;" sequenceBy="#COUNTER">X</a></span>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="margin:0px; padding:0px;">
							
							<div id="cashPaymentDiv">
								<table width="100%" class="noborder">
									<tr>
										<td width="20%" align="left">Date of Deposit <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="Date" name="cash_deposit_date[]" id="cash_deposit_date" 
											 style="width:90%; text-transform:uppercase;"  value="<?=date('Y-m-d')?>" use="cash_deposit_date" />
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
											<input type="number" name="cheque_number[]" id="cheque_number" 
											 style="width:90%; text-transform:uppercase;" use="cheque_number" />
										</td>
										<td width="20%" align="left">Drawee Bank <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="text" name="cheque_drawn_bank[]" id="cheque_drawn_bank" 
											 style="width:90%; text-transform:uppercase;" use='cheque_drawn_bank'/>
										</td>
									</tr>
									<tr>
										<td width="20%" align="left">Cheque Date <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="date" name="cheque_date[]" id="cheque_date" 
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
											<input type="text" name="draft_number[]" id="draft_number" 
											 style="width:90%; text-transform:uppercase;" use='draft_number' />
										</td>
										<td width="20%" align="left">Drawn Bank <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="text" name="draft_drawn_bank[]" id="draft_drawn_bank" 
											 style="width:90%; text-transform:uppercase;" use="draft_drawn_bank" />
										</td>
									</tr>
									<tr>
										<td width="20%" align="left">Draft Date <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="date" name="draft_date[]" id="draft_date" 
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
											<input type="text" name="neft_bank_name[]" id="neft_bank_name" 
											 style="width:90%; text-transform:uppercase;" use='neft_bank_name' />
										</td>
										<td align="left">Transaction Id <span class="mandatory">*</span></td>
										<td align="left">
											<input type="text" name="neft_transaction_no[]" id="neft_transaction_no" 
											 style="width:90%; text-transform:uppercase;" use='neft_transaction_no' />
										</td>
									</tr>
									<tr>
										<td width="20%" align="left">Date <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="date" name="neft_date[]" id="neft_date" 
											 style="width:90%; text-transform:uppercase;" value="<?=date('Y-m-d')?>" use='neft_date' />
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
											<input type="text" name="rtgs_bank_name[]" id="rtgs_bank_name" 
											 style="width:90%; text-transform:uppercase;" use='rtgs_bank_name' />
										</td>
										<td align="left">Transaction Id <span class="mandatory">*</span></td>
										<td align="left">
											<input type="text" name="rtgs_transaction_no[]" id="rtgs_transaction_no" 
											 style="width:90%; text-transform:uppercase;" use='rtgs_transaction_no' />
										</td>
									</tr>
									<tr>
										<td width="20%" align="left">Date <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="date" name="rtgs_date[]" id="rtgs_date" 
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
											<input type="number" name="card_number[]" id="card_number" 
											 style="width:90%; text-transform:uppercase;" use='card_number' />
										</td>
										<td align="left">Remarks</td>
										<td align="left">
											<input type="text" name="remarks[]" id="remarks" 
											 style="width:90%; text-transform:uppercase;" use='remarks' />
										</td>
									</tr>
									<tr>
										<td width="20%" align="left">Date <span class="mandatory">*</span></td>
										<td width="30%" align="left">
											<input type="date" name="card_date[]" id="card_date" 
											 style="width:90%; text-transform:uppercase;" value="<?=date('Y-m-d')?>" use='card_date' />
										</td>
										<td align="left"></td>
										<td align="left"></td>
									</tr>
								</table>
							</div>
							
						</td>
					</tr>					
				</table>
			</div>
		</div>
		<?
	}
	
	function makePaymnetAreaTemplate()
	{   
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.workshop.php');
		include_once('../../includes/function.dinner.php');
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['id'];
		$loggedUserId	= $mycms->getLoggedUserId();
		$rowFetchUser   = getUserDetails($delegateId);
		?>
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
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_title'])?> 
									<?=strtoupper($rowFetchUser['user_first_name'])?> 
									<?=strtoupper($rowFetchUser['user_middle_name'])?> 
									<?=strtoupper($rowFetchUser['user_last_name'])?>
								</td>
								<td width="20%" align="left">Registration Type:</td>
								<td width="30%" align="left"><span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>;"><b><?=$rowFetchUser['registration_request']?></b></span></td>
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
							<tr class="thighlight">
								<td colspan="7" align="left">Payment Voucher Details</td>
							</tr>
							<?php
							$paymentCounter   = 0;
							$resFetchSlip	  = slipDetailsOfUser($delegateId,true);
							if($resFetchSlip)
							{
							?>
								<tr class="theader">
									<td width="50" align="center">Sl No</td>
									<td width="100" align="left">PV Number</td>
									<td width="100" align="center">Slip Date</td>
									<td width="100" align="center">Payment Mode</td>
									<td width="100" align="right">Discount Amt.</td>
									<td width="100" align="center">Slip Amt.</td>
									<td width="100" align="right">Paid Amt.</td>
									<td align="center">Payment Status</td>
								</tr>
								<?
								$styleCss = 'style="display:none;"';
								
								foreach($resFetchSlip as $key=>$rowFetchSlip)
								{
								//print_r($rowFetchSlip);
									$counter++;
									$resPaymentDetails      = paymentDetails($rowFetchSlip['id']);
									$paymentDescription     = "-";
									if($key==0)
									{
										$paymentId = $resPaymentDetails['payment_id'];
										$slipId    =$rowFetchSlip['id'];
									}
											
									$isChange 		="YES";
									$excludedAmount = invoiceAmountOfSlip($rowFetchSlip['id'],false,false);
							
									$amount 		= invoiceAmountOfSlip($rowFetchSlip['id']);
									$discountDeductionAmount = ($excludedAmount-$amount);
									//$discountAmountofSlip= ($discountDeductionAmount/1.18);
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										if($rowFetchInvoice['service_type']== "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$discountAmountofSlip= $discountDeductionAmount;
										}
										else
										{
											$discountAmountofSlip= ($discountDeductionAmount/1.18);
										}
									}
								?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$counter?></td>
										<td align="left" valign="top"><?=$rowFetchSlip['slip_number']?>
										</td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchSlip['slip_date'], "D")?></td>
										<td align="center" valign="top"><?=$rowFetchSlip['invoice_mode']?></td>
										<!--<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($discountAmountofSlip)==''?'0.00':number_format($discountAmountofSlip,2)?></td>-->
										<td align="center" valign="top"><?=$rowFetchSlip['currency']?> <? $DiscountAmount = invoiceDiscountAmountOfSlip($rowFetchSlip['id'],true);
																		  echo number_format($DiscountAmount,2); ?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($amount,2)?></td>

										<td align="right" valign="top">
										<?
										if($resPaymentDetails['totalAmountPaid'] > 0)
										{
											echo number_format($resPaymentDetails['totalAmountPaid'],2);
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
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
											?>
											<a class="ticket ticket-important" operationMode="proceedPayment" 
												 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
											<?
											}
											else
											{
										?>
											<span style="color:#5E8A26;"><strong>Complimentary</strong></span>
										<?
											}
										}
										else if($rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
										?>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
										<?
											}
											else
											{
										?>
											<span style='color:#009900;'>Zero Value</span>
											
										<?
											}
										}
										else
										{
										?>
											<a onclick="$('#paymentDetails<?=$rowFetchSlip['id']?>').toggle();">
											Payment</a>
										<?php
										}
										?>
										</td>
									</tr>
									<tr id="paymentDetails<?=$rowFetchSlip['id']?>">
										<td colspan="8"  style="border:1px dashed #E56200;">
											<table width="100%">
												<tr class="theader">
													<td  align="center">Sl No</td>
													<td align="center">Slip No</td>
													<td align="center">Payment Mode</td>
													<td align="center">Amount</td>
													<td align="center">Action</td>
												</tr>
												<?php
												$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);
												$paymentCounter                 = 0;
												
												foreach($resPaymentDetails['paymentDetails'] as $key=>$rowPayment)
												{
													$paymentCounter++;
													
													if($rowPayment['payment_mode']=="Cash")
													{
														$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>".setDateTimeFormat2($rowPayment['cash_deposit_date'], "D")."</b>.";
													}
													if($rowPayment['payment_mode']=="Online")
													{
														$paymentDescription = "Paid by <b>Online</b>. Date of Payment: <b>".setDateTimeFormat2($rowPayment['payment_date'], "D")."</b>.<br>
																				Transaction Number: <b>".$rowPayment['atom_atom_transaction_id']."</b>.<br>
																				Bank Transaction Number: <b>".$rowPayment['atom_bank_transaction_id']."</b>.";
													}
													if($rowPayment['payment_mode']=="Card")
													{
														$paymentDescription = "Paid by <b>Card</b>. Reference Number: <b>".$rowPayment['card_transaction_no']."</b>.<br>
																				Date of Payment: <b>".setDateTimeFormat2($rowPayment['card_payment_date'], "D")."</b>.
																				Remarks: <b>".$rowPayment['payment_remark']."</b> ";
													}
													if($rowPayment['payment_mode']=="Draft")
													{
														$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>".$rowPayment['draft_number']."</b>.<br>
																			   Draft Date: <b>".setDateTimeFormat2($rowPayment['draft_date'], "D")."</b>.
																			   Draft Drawee Bank: <b>".$rowPayment['draft_bank_name']."</b>.";
													}
													if($rowPayment['payment_mode']=="NEFT")
													{
														$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>".$rowPayment['neft_transaction_no']."</b>.<br>
																			   Transaction Date: <b>".setDateTimeFormat2($rowPayment['neft_date'], "D")."</b>.
																			   Transaction Bank: <b>".$rowPayment['neft_bank_name']."</b>.";
													}
													if($rowPayment['payment_mode']=="RTGS")
													{
														$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>".$rowPayment['rtgs_transaction_no']."</b>.<br>
																			   Transaction Date: <b>".setDateTimeFormat2($rowPayment['rtgs_date'], "D")."</b>.
																			   Transaction Bank: <b>".$rowPayment['rtgs_bank_name']."</b>.";
													}
													if($rowPayment['payment_mode']=="Cheque")
													{
														$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>".$rowPayment['cheque_number']."</b>.<br>
																			   Cheque Date: <b>".setDateTimeFormat2($rowPayment['cheque_date'], "D")."</b>.
																			   Cheque Drawee Bank: <b>".$rowPayment['cheque_bank_name']."</b>.";
													}
												?>
													<tr class="tlisting">
														<td align="center"><?=$paymentCounter?></td>
														<td align="center"><?=getSlipNumber($resPaymentDetails['slip_id'])?></td>
														<td align="center"><?=$rowPayment['payment_mode']?></td>
														<td align="center">
															<?=$rowPayment['amount']?>
														</td>
														<td align="center">
														<?php
														if($rowPayment['payment_status'] == "UNPAID")
														{
															
															if($rowPayment['status']=="D")
															{ 
															?>
																<a class="ticket ticket-important" operationMode="proceedPayment" 
																	onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['payment_id']?>')">Set Payment Terms</a>
															<?
															}
															else
															{
															?>
																<a class="ticket ticket-important" operationMode="proceedPayment" 
																 onclick="multiPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['payment_id']?>')">Unpaid</a>
															<?
															}
														}
														else if($rowPayment['payment_status'] == "PAID")
														{
															echo $paymentDescription;
															$isChange="NO";
														}
														?>
														</td>
													</tr>
											<?php
												}
												
												if($resPaymentDetails['has_to_set_payment'] == 'Yes')
												{
													if($resPaymentDetails['slip_invoice_mode']=='ONLINE')
													{
													?>
													<tr class="tlisting">
														<td colspan="3">&nbsp;</td>
														<td>
															<a class="ticket ticket-important" operationMode="proceedPayment" 
															 onclick="changePaymentPopup('<?=$rowFetchSlip['id']?>','<?=$rowFetchUser['id']?>','OFFLINE')">Change Payment Mode</a>
														</td>  
													<?
														if($loggedUserId == 1 )
														{ 
													?>
														<td>
															<a class="ticket ticket-important" style="background-color:#0000FF;"operationMode="proceedPayment" 
															onclick="onlineOpenPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['id']?>')">Complete Online Payment</a>
														</td>
													<?
														}
													?>
													</tr>
													<?php
													}
													elseif($resPaymentDetails['slip_invoice_mode']=='OFFLINE' && ($totalNoOfUnpaidCount==0))
													{
													?>
														<tr class="tlisting">
															<td colspan="5" align="right">
																<a class="ticket ticket-important" href="registration.php?show=additionalPaymentArea&sxxi=<?=base64_encode($rowFetchSlip['id'])?>" target="_blank">Set Payment Terms</a>
															</td>
														</tr>
													<?
													}
												}
												?>
											</table>
										</td>
									</tr>
							<?php
								}
							}
							
							?>
							</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter">&nbsp;
						
					</td>
				</tr>
			</tbody> 
		</table>
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form2" id="payment_popup"></div>
		<div class="online_popup_form" id="onlinePayment_popup"></div>
		<div class="popup_form2" id="SetPaymentTermsPopup"></div>
		<div class="popup_form2" id="settlementPopup"></div>
		<div class="overlay" id="fade_change_popup" ></div>
		<div class="popup_form2" id="change_payment_popup" style="width:auto; height:auto; display:none; left:50%; margin-left: -210px;">
		<form action="registration.process.php" method="post" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="changePaymentMode" />
		<input type="hidden" name="slip_id" id="slip_id" value="" />
		<input type="hidden" name="registrationMode" id="registrationMode" value=""/>
		<input type="hidden" name="delegate_id" id="delegate_id" value="" />
		<table>
			<tr>
				<td align="right"><span class="close" onclick="closechangePaymentPopup()">X</span></td>
			</tr>
			<tr>
				<td align="center"><h2 style="color:#CC0000;">Do you want to change payment mode<br /><br />to offline?</h2></td>
			</tr>
			<tr>
				<td align="center"><input type="submit" class="btn btn-small btn-red" value="Change" /></td>
			</tr>	
		</table>
		</form>
		</div>
		<script>
		function changePaymentPopup(slipId,delegateId,regMode)
		{
			$("#fade_change_popup").fadeIn(1000);
			$("#change_payment_popup").fadeIn(1000);
			$('#slip_id').val(slipId);
			$('#registrationMode').val(regMode);
			$('#delegate_id').val(delegateId);
		}
		function closechangePaymentPopup()
		{
			$("#fade_change_popup").fadeOut();
			$("#change_payment_popup").fadeOut();
		}
		</script>		
	<?
	}
	
	function regCompleteNotificationTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		include_once('../../includes/function.registration.php');
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = " SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
									WHERE `status` != ? 
								 ORDER BY `cutoff_sequence` ASC";		
		$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');												  
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
		$delegateClsType	= getRegClsfType($delagateCatagory);
		?>
		<script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
		<form name="frmApplyRegistration" id="frmApplyRegistration"  action="<?=$processPage?>" method="post" onsubmit="return validateWorkshop();">
			<input type="hidden" name="act" value="step6" />
			<?php if($_REQUEST['COUNTER']=='Y'){ ?>
				<input type="hidden" name="counter" id="counter" value="Y" />
			<?php } ?>
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Choose Registration Mode</td>
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
								<tr class="thighlight">
									<td colspan="4" align="left">Select Registration Mode</td>
								</tr>
								<tr>
									<?
									if($delegateClsType != 'FULL_ACCESS' && $delegateClsType != 'GUEST')
									{
									?>
									<td><input type="checkbox" name="regsitaion_mode" operationMode="MakeZeroValue" value="GENERAL" /> GENERAL</td>
									<?
									}
									if($delegateClsType != 'GUEST')
									{
									?>
									<td><input type="checkbox" name="regsitaion_mode" operationMode="MakeZeroValue" value="COMPLIMENTARY" /> COMPLIMENTARY</td>
									<?
									}
									if($delegateClsType != 'FULL_ACCESS')
									{
									?>
									<td><input type="checkbox" name="regsitaion_mode" operationMode="MakeZeroValue" value="ZERO_VALUE" /> ZERO VALUE</td>
									<?
									}
									?>
								</tr>
							</table>
							
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
										 
										<input type="Submit" name="bttnCotinue" id="bttnCotinue" value="Proceed" 
										 class="btn btn-midium btn-blue" style="float:left; margin-right:10%;" operationMode="registrationMode"   />
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
										<span><b style="color:#FF6600">**Your registration is acknowledged at <?=$cfg['EMAIL_CONF_NAME']?> .</b></span>
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
	
	function viewCancelInvoiceDetails()
	{   
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['id'];
		$rowFetchUser   = getUserDetails($delegateId);
		$processPage = "registration.process.php";
		?>
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
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_title'])?> 
									<?=strtoupper($rowFetchUser['user_first_name'])?> 
									<?=strtoupper($rowFetchUser['user_middle_name'])?> 
									<?=strtoupper($rowFetchUser['user_last_name'])?>
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
							<tr class="thighlight">
								<td colspan="" align="left" width="20%">Registration Status</td>
								<td colspan="" align="left" width="20%">Request Date Time</td>
								<td colspan="" align="left"  width="20%">Action</td>
								
								
							</tr>
							<?
								$sql['QUERY'] = "   SELECT * 
												 FROM ".$cfg['DB.CANCEL.INVOICE']." 
												WHERE  `delegate_id` = '".$delegateId."'
												  AND `status` = 'A'
											 ORDER BY id DESC";
												
									$resultUnregisterConference 	= $mycms->sql_select($sql);
								foreach($resultUnregisterConference as $key=> $rowdetails)
								{
								
									$invoiceDetails = invoiceDetailsQuerySet($rowdetails['invoice_id'],"","");
									$rowfetchinvoiceDetails 	= $mycms->sql_select($invoiceDetails);									
									$request_for = $rowdetails['request_for'];
									$requestType = "";
									if($request_for =='DELEGATE_CONFERENCE_REGISTRATION')
									{
										$requestType = 'Conference';
									}
									if($request_for =='DELEGATE_WORKSHOP_REGISTRATION')
									{
										$requestType = 'Workshop';
									}
									if($request_for =='DELEGATE_RESIDENTIAL_REGISTRATION')
									{
										$requestType = 'Recidenctial Package';
									}
									$userCancelConfirmMessage =  'Do You Really Want To Approve ?';
									$userCancelURL 			  = $processPage."?act=Unregister&user_id=".$rowFetchUser['id']."&unregisterReqId=".$rowdetails['id']."&invoice_id=".$rowdetails['invoice_id']."&redirect=".$requestPage;
									
								?>
								<tr>
										<td align="left" valign="top" ><?=$requestType?> </td>
										<td align="left" valign="top" ><?=$rowdetails['created_dateTime']?></td>
										<td align="left" valign="top" width="30%">
										<?
											if($rowdetails['request_status']=='Request')
											{
										?>
											<a href="<?=$userCancelURL?>" 
												   onclick="return confirm('<?=$userCancelConfirmMessage?>')">
												<input type="button" name="bttnUnregister" id="bttnUnregister" class="btn btn-small btn-red" value="Approve Request" /><br /><br />
											</a>
											<?
											}
											else
											{
											?>
												<input type="button" name="bttnUnregister" id="bttnUnregister" class="btn btn-small btn-red" value="Approved" />
											<?
											}
											?>
										</td>
										
									</tr>
								<?
								}
							?>							
						</table>
							
						
					</td>
				</tr>
				
			</tbody> 
		</table>
		
		
	<?
	}
	
	function viewUnregisterUserRegistration($cfg, $mycms)
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
		<form name="frmSearch" method="post" action="registration.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
						<a href="download_excel.php?search=search&<?=$searchString?>"><img src="../images/Excel-icon.png"  style="float:right; padding-right: 10px;"/></a>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` FROM ".$cfg['DB.REGISTRATION.CLASSIFICATION']." WHERE status = 'A' AND `id`  NOT IN (3,6)";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "Course Only - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo $cfg['RESIDENTIAL_NAME']."- ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>								
							</table>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</th>
									<td align="left">Name & Contact</th>
									<td width="110" align="left">Registration Type</th>
									<td width="180" align="left">Registration Details</th>
									<td width="480" align="center">Service Dtls</th>
									<td width="70" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								
								$alterCondition = "";
								if(strtolower($_SERVER['HTTP_HOST'])!='localhost')
								{
									 $alterCondition = "AND delegate.user_email_id NOT LIKE '%@encoder%'";
														
								}
								$alterCondition .= "AND delegate.status = 'D'";
							    
								$sqlFetchUser         = "";
								
								$idArr = getAllDelegates("","",$alterCondition);
								
								if($idArr)
								{
									
									foreach($idArr as $i=>$id) 
									{
										$status = true;
										$rowFetchUser = getUserDetails($id);
										$counter      = $counter + 1;
										$color = "#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										//$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
										//echo ($rowFetchUser['user_mobile_isd_code']);
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_title'])?> 
												<?=strtoupper($rowFetchUser['user_first_name'])?> 
												<?=strtoupper($rowFetchUser['user_middle_name'])?> 
												<?=strtoupper($rowFetchUser['user_last_name'])?>

												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
												}
												?>
											</td>
											
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Reg Id : ".$rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Us No : ".strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											<td align="left" valign="top">
												<table width="100%" style="border: 1px solid black;">
													<?php
														$sqlFetchInvoice                = invoiceDetailsQuerySet("",$rowFetchUser['id'],"");
																	
														$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
														if($resultFetchInvoice)
														{
															foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
															{
																$invoiceCounter++;
																$slip = getInvoice($rowFetchInvoice['slip_id']);
																
																
																$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
																getRegClsfName(getUserClassificationId($delegateId));
																$type			 = "";
																if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
																{
																	$type = "Course Only - ".getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']));
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
																{
																	$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
																	$type =  getWorkshopName($workShopDetails['workshop_id']);
																}
																if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
																{
																	$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
																{
																	$type = $cfg['RESIDENTIAL_NAME']." - ".getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']));
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
																{
																	$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
																{
																	$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
																	
																	$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
																}
														?>
																<tr class="tlisting">
																	<td align="left" width="30%" valign="top">
																		<?=$rowFetchInvoice['invoice_number']?><br />
																		<?=$slip['slip_number']?><br />
																		<strong style="color:#FE6F06;">by <?=getSlipOwner($slip['id'])?></strong>
																	</td>
																	<td align="left" valign="top">
																		<?=$type?><br />
																		<span style="color:<?=$rowFetchInvoice['invoice_mode']=='ONLINE'?'#D77426':'#007FFF'?>;"><?=$rowFetchInvoice['invoice_mode']?></span>
																		
																	</td>
																	<td align="right" width="21%" valign="top">
																		<?=$rowFetchInvoice['currency']?> <?=number_format($rowFetchInvoice['service_roundoff_price'],2)?><br />
																		<?php
																		if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
																		{
																		?>
																			<span style="color:#5E8A26;"><strong style="font-size: 15px;">Complementary</strong></span>
																		<?php
																		}
																		elseif($rowFetchInvoice['payment_status']=="ZERO_VALUE")
																		{
																		?>
																			<span style="color:#009900;"><strong style="font-size: 15px;">Zero Value</strong></span>
																		<?php
																		}
																		else if($rowFetchInvoice['payment_status']=="PAID")
																		{
																		?>
																			<span style="color:#5E8A26;"><strong style="font-size: 15px;">Paid</strong></span>
																		<?php	
																			$resPaymentDetails      = paymentDetails($rowFetchInvoice['slip_id']);
																			if($resPaymentDetails['payment_mode']=="Online")
																			{
																				echo "[".$resPaymentDetails['atom_atom_transaction_id']."]";
																			}	
																		}
																		else if($rowFetchInvoice['payment_status']=="UNPAID")
																		{
																		?>
																			<span style="color:#C70505;"><strong style="font-size: 15px;">Unpaid</strong></span>
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
											<td align="center" valign="top">
											<?php
												if($rowFetchUser['isWorkshop']=="N" && $rowFetchUser['isAccommodation']=='N')
												{
												?>
													<a href="registration.php?show=addWorkshop&id=<?=$rowFetchUser['id'] ?>">
													<span title="Apply Workshop" class="icon-layers" /></a>
												<?php
												}												
												
												if(isSlipOfDelegate($rowFetchUser['id']))
												{
													if(isUnpaidSlipOfDelegate($rowFetchUser['id']))
													{
														$class = "iconRed-book";
													}
													else
													{
														$class = "iconGreen-book";
													}
													
												}
												else
												{
													$class = "icon-book";
												}
												?>
												<a onclick="openDetailsPopup(<?=$rowFetchUser['id']?>);"><span title="View" class="icon-eye" /></a>
												
												<a href="registration.php?show=invoice&id=<?=$rowFetchUser['id']?>"><span title="Invoice" class="icon-book"/></a>
												
												<?php
												if($loggedUserId=='1' || $loggedUserId=='6')
												{
												?>
													<a href="registration.php?show=AskToRemove&id=<?=$rowFetchUser['id']?>">
													<span alt="Remove" title="Remove" class="icon-trash-stroke"/>
													</a>
												<?php
												}
												
												?>	
											</td>
										</tr>
								<?php
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
				<tr class="tfooter">
					<td colspan="2">						
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		</form>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	}
	
	function viewDeletedInvoiceDetails()
	{   
		global $cfg, $mycms;
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
		$delegateId 	=  $_REQUEST['id'];
		$rowFetchUser   = getUserDetails($delegateId);
		?>
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
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_full_name'])?> 
								</td>
								<td align="left">Registration Date:</td>
								<td align="left"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?></td>
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
								?>
								</td>
							</tr>
						</table>
									
						<table width="100%">
							<tr class="thighlight">
								<td colspan="9" align="left"> 
								<?=ucwords(strtolower($rowFetchUser['user_full_name']))?> Invoice
								</td>
							</tr>
							<tr class="theader"  use="all" >
								<td width="30" align="center">Sl No</td>
								<td align="left"  width="100">Invoice No</td>
								<td align="left"  width="100">PV No</td>
								<td width="80" align="center">Invoice Mode</td>
								<td align="center">Invoice For</td>
								<td width="70" align="center">Invoice Date</td>
								<td width="110" align="right">Invoice Amount</td>
								<td width="100" align="center">Payment Status</td>
							</tr>
							<?php
							$invoiceCounter                 = 0;
							$isDelete ="YES";
							$slipIdArr = getAllSlipsOfDelegate($delegateId);
							
							if($slipIdArr)
							{
								foreach($slipIdArr as $key=>$slipId)
								{
									$resultFetchInvoice             = getInvoiceRecords("","",$slipId);									
									
									if($resultFetchInvoice)
									{
										foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
										{
											$invoiceCounter++;
											$slip = getInvoice($rowFetchInvoice['slip_id']);
											$returnArray    = discountAmount($rowFetchInvoice['id']);
											$percentage     = $returnArray['PERCENTAGE'];
											$totalAmount    = $returnArray['TOTAL_AMOUNT'];
											$discountAmount = $returnArray['DISCOUNT'];
											
											if($delegateId != $rowFetchInvoice['delegate_id'])
											{
												$isDelete ="NO";
											}
											$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
											$type			 = "";
											if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
											{
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"CONFERENCE"). ' - '.$thisUserDetails['user_full_name'];
											}
											if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
											{
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"RESIDENTIAL"). ' - '.$thisUserDetails['user_full_name'];
											}
											
											if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
											{
												$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"WORKSHOP"). ' - '.$thisUserDetails['user_full_name'];
											}
											if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
											{
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMPANY"). ' - '.$thisUserDetails['user_full_name'];
											}
											if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
											{
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMMODATION"). ' - '.$thisUserDetails['user_full_name'];
											}
											if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
											{
												$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
												
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"TOUR"). ' - '.$thisUserDetails['user_full_name'];
											}
											if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
											{
												$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER"). ' - '.$thisUserDetails['user_full_name'];
											}
											
											$styleColor = 'background: rgb(204, 229, 204);';
											$isCancel	= 'NO';
											if($rowFetchInvoice['status'] =='C')
											{
												$styleColor = 'background: rgb(255, 204, 204);';
												$isCancel	= 'YES';
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
								}
							}
							else
							{
								$resultFetchInvoice                = getInvoiceRecords("",$delegateId,"");
								
								if($resultFetchInvoice)
								{
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										$invoiceCounter++;
										$slip = getInvoice($rowFetchInvoice['slip_id']);
										$returnArray    = discountAmount($rowFetchInvoice['id']);
										$percentage     = $returnArray['PERCENTAGE'];
										$totalAmount    = $returnArray['TOTAL_AMOUNT'];
										$discountAmount = $returnArray['DISCOUNT'];
										
										$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
										$type			 = "";
										if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = "COURSE REGISTRATION - ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
											$type =  strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION - ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = $cfg['RESIDENTIAL_NAME']." - ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
											
											$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
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
							}
							?>
						</table>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" align="center">
					<input type="button" value="Delete User" class="btn btn-small btn-blue" onclick="window.location.href='registration.process.php?act=Trash&id=<?=$delegateId?>&userType=<?=$userType?>'" />
					</td>
				</tr>
				
				<tr>
					<td colspan="2" class="tfooter">&nbsp;
						<span style="float: right; color:red;">&nbsp;&nbsp;Cancelled Invoice&nbsp;&nbsp;</span>
						<span style="float: right; background: #FFCCCC; height: 15px; width: 15px;">&nbsp;</span>
						<span style="float: right; color:red;">&nbsp;&nbsp;Active Invoice&nbsp;&nbsp;</span>
						<span style="float: right; background: #CCE5CC; height: 15px; width: 15px;">&nbsp;</span>
					</td>
				</tr>
			</tbody> 
		</table>
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form2" id="payment_popup"></div>
		<div class="popup_form2" id="SetPaymentTermsPopup"></div>
		
	<?
	}
	
	/****************************************************************************/
	/*                      SHOW ALL CANCELATION GENERAL REGISTRATION           */
	/****************************************************************************/	
	function viewAllCancellationRequests($cfg, $mycms)
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
		<form name="frmSearch" method="post" action="registration.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Invoice Cancelation Request</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch" style="display:none;">
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` FROM ".$cfg['DB.REGISTRATION.CLASSIFICATION']." WHERE status = 'A' AND `id`  NOT IN (3,6)";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "Course Only - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo $cfg['RESIDENTIAL_NAME']." - ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>
								
								
							</table>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</th>
									<td align="left" width="110">Name & Contact</th>
									<td width="110" align="left">Rquest For</th>
									<td width="110" align="left">Registration Type</th>
									<td width="130" align="left">Registration Details</th>
									<td width="90" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								
								$alterCondition = "";
							    $alterCondition = "AND delegate.id IN ( SELECT DISTINCT delegate_id FROM ".$cfg['DB.CANCEL.INVOICE']." )";
								$sqlFetchUser         = "";
								
								$idArr = getAllDelegates("","",$alterCondition);
								
								if($idArr)
								{
									
									foreach($idArr as $i=>$id) 
									{
										$status = true;
										$rowFetchUser = getUserDetails($id);
										$counter      = $counter + 1;
										$color = "#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										//$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
										//echo ($rowFetchUser['user_mobile_isd_code']);
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_title'])?> 
												<?=strtoupper($rowFetchUser['user_first_name'])?> 
												<?=strtoupper($rowFetchUser['user_middle_name'])?> 
												<?=strtoupper($rowFetchUser['user_last_name'])?>

												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
												}
												?>
											</td>
											
											<td align="left" valign="top">
											<?
											$sql['QUERY'] 			  	= "SELECT * 
																	 FROM ".$cfg['DB.CANCEL.INVOICE']." 
																	WHERE `delegate_id` = '".$rowFetchUser['id']."'";
											$resultUnregister 	= $mycms->sql_select($sql);
											
											if($resultUnregister)
											{
												foreach($resultUnregister as $i=>$rowReqFor) 
												{
													if ($rowReqFor['request_for'] == "DELEGATE_CONFERENCE_REGISTRATION")
													{
														echo 'Conference'."<br/>";
													}
													elseif ($rowReqFor['request_for'] == "DELEGATE_WORKSHOP_REGISTRATION")
													{
														echo 'Workshop'."<br/>";
													}
													elseif ($rowReqFor['request_for'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
													{
														echo 'Residential Package'."<br/>";
													}
													
												}
											}		
											?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Reg Id : ".$rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Us No : ".strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											
											<!--<td align="center" valign="top">Payment</td-->
											<td align="center" valign="top">
											<?
												
												if(isSlipOfDelegate($rowFetchUser['id']))
												{
													if(isUnpaidSlipOfDelegate($rowFetchUser['id']))
													{
														$class = "iconRed-book";
													}
													else
													{
														$class = "iconGreen-book";
													}
													
												}
												else
												{
													$class = "icon-book";
												}
												?>
												<a  href="registration.php?show=viewCancelRequests&id=<?=$rowFetchUser['id']?>"><span title="View" class="icon-eye" /></a>
											</td>
										</tr>
								<?php
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
				<tr class="tfooter">
					<td colspan="2">													
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		</form>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	}
	
	function viewChangePaymentMode($cfg, $mycms)
	{
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['delegateId'];
		$rowFetchUser   = getUserDetails($delegateId);
		
		$slipId		 	=  $_REQUEST['slipId'];
		$slipDetails    = slipDetails($slipId);
		?>
		<form action="registration.process.php" method="post" name="paymentChangeMethod" id="paymentChangeMethod" onsubmit="return fromValidation(this);">
			<input type="hidden" name="act" id="act" value="changePaymentMode" />
			<input type="hidden" name="delegate_id" id="delegate_id" value="<?=$delegateId?>" />
			<input type="hidden" name="slip_id" id="slip_id" value="<?=$slipId?>" />
		
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
						
							<table width="100%">
								<tr class="thighlight">
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
								<tr class="thighlight">
									<td colspan="4" align="left">Slip Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">PV Number:</td>
									<td width="30%" align="left">
										<?=$slipDetails['slip_number']?> 
									</td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr>
									<td align="left">Slip Date:</td>
									<td align="left">	
										<?=setDateTimeFormat2($slipDetails['slip_date'], "D")?>
									</td>
									<td align="left">Payment Mode:</td>
									<td align="left">
									<?php
										echo $slipDetails['invoice_mode'];
									?>
									</td>
								</tr>
								
								<tr>
									<td align="left">Slip Amount:</td>
									<td align="left">
										<?=$slipDetails['currency']?> 
										<? $amount = invoiceAmountOfSlip($slipDetails['id']);
										   echo number_format($amount,2); ?>
									</td>
									<td align="left">Total Number Of Invoice:</td>
									<td align="left"><?=invoiceCountOfSlip($slipDetails['id'])?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="7" align="left">Slip Details</td>
								</tr>
								<tr class="theader">
									<td width="30" align="center">Sl No</td>
									<td align="left"  width="100">Invoice No</td>
									<td width="60" align="center">Invoice Mode</td>
									
									<td align="center">Invoice For</td>
									<td width="70" align="center">Invoice Date</td>
									<td width="230" align="right">Service Amt + Internet Handling Charge = Total AMount</td>
									<td width="100" align="center">Payment Status</td>
								</tr>
								<?php
								
								$invoiceCounter                 = 0;
								$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$slipId);
																
								$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
								
								//$ffff = getInvoice($rowFetchSlip['id']);
								//echo "<pre>";print_r($ffff);echo "</pre>";
								$intAmt							= 0; 
								if($resultFetchInvoice)
								{
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										$invoiceCounter++;
										$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
										$type			 = "";
										$isConfarance	 = "";
										if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = "CONFERENCE REGISTRATION OF ".$thisUserDetails['user_full_name'];
											if($rowFetchInvoice['delegate_id'] == $delegateId)
											{
												$isConfarance = "CONFERENCE";
											}
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
											$type =  strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION  OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = $cfg['RESIDENTIAL_NAME']." OF ".$thisUserDetails['user_full_name'];
											if($rowFetchInvoice['delegate_id'] == $delegateId)
											{
												$isConfarance = "CONFERENCE";
											}
										}
										if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
											
											$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
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
										<tr class="tlisting" style=" <?=$styleColor?>">
											<td align="center">
												<?=$invoiceCounter?>
												<input type="hidden" name="invoiceId[]" id="invoiceId_<?=$rowFetchInvoice['id']?>" value="<?=$rowFetchInvoice['id']?>" />
											</td>
											<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
											<td align="center"><?=$rowFetchInvoice['invoice_mode']?></td>
											<td align="left">
												<?=$type?>
											</td>
											<td align="center"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
											<td align="right">
												<?=number_format($rowFetchInvoice['service_product_price'],2)?> + 
												<?=number_format($rowFetchInvoice['internet_handling_amount'],2)?> = 
												<?=$rowFetchInvoice['currency']?> <?=number_format($rowFetchInvoice['service_roundoff_price'],2)?>
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
								<tr class="theader">
									<td width="30" align="right" colspan="5">Previous Amount:</td>
									<td width="110" align="right">
									 <strong style="float:right;"><u><?=$slipDetails['currency']?> 
											<? $amount = invoiceAmountOfSlip($slipDetails['id']);
											echo number_format($amount,2); ?></u></strong>
									</td>
									<td width="100" align="center"></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Payment Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Payment Mode:</td>
									<td width="30%" align="left">
										<?
										if($slipDetails['invoice_mode']=='OFFLINE' && $slipDetails['payment_status']!="COMPLIMENTARY")
										{
										?>
										<input type="radio" name="registrationMode" operationMode="registrationMode" id="registrationMode_online" value="ONLINE" <?=$slipDetails['invoice_mode']=='ONLINE'?'disabled="disabled"':'checked="checked"'?>  /> Online 
										<?
										}
										if($slipDetails['invoice_mode']=='ONLINE' && $slipDetails['payment_status']!="COMPLIMENTARY")
										{
										?>
										
										<input type="radio" name="registrationMode" operationMode="registrationMode" id="registrationMode_offline" value="OFFLINE" <?=$slipDetails['invoice_mode']=='OFFLINE'?'disabled="disabled"':'checked="checked"'?> /> Offline
										<?
										}
										if($slipDetails['payment_status']=="COMPLIMENTARY")
										{
										?>
										
										<input type="radio" name="registrationMode" operationMode="registrationMode" id="registrationMode_offline" value="OFFLINE" checked="checked" /> Offline
										<?
										}
										?>
									</td>
									<td width="20%" align="left"></td>
									<td width="30%" align="left"></td>
								</tr>
								<tr style="display:none;" use="onlineTr">
									<td align="left" colspan="4">
										 
										 <table width="100%">
											<tr class="thighlight">
												<td colspan="7" align="left">Slip Details</td>
											</tr>
											<tr class="theader">
												<td width="30" align="center">Sl No</td>
												<td align="left"  width="100">Invoice No</td>
												<td width="60" align="center">Invoice Mode</td>
												
												<td align="center">Invoice For</td>
												<td width="70" align="center">Invoice Date</td>
												<td width="230" align="right">Service Amt + Internet Handling Charge = Total AMount</td>
												<td width="100" align="center">Payment Status</td>
											</tr>
											<?php
											
											$invoiceCounter                 = 0;
											$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$slipId);
																			
											$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
											
											//$ffff = getInvoice($rowFetchSlip['id']);
											//echo "<pre>";print_r($ffff);echo "</pre>";
											$totAmt							= 0; 
											if($resultFetchInvoice)
											{
												foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
												{
													$invoiceCounter++;
													$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
													$type			 = "";
													$isConfarance	 = "";
													if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
													{
														$type = "CONFERENCE REGISTRATION OF ".$thisUserDetails['user_full_name'];
														if($rowFetchInvoice['delegate_id'] == $delegateId)
														{
															$isConfarance = "CONFERENCE";
														}
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
													{
														$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
														$type =  strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION  OF ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
													{
														$type = $cfg['RESIDENTIAL_NAME']." OF ".$thisUserDetails['user_full_name'];
														if($rowFetchInvoice['delegate_id'] == $delegateId)
														{
															$isConfarance = "CONFERENCE";
														}
													}
													if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
													{
														$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
													{
														$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
													{
														$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
														
														$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
													}
													$styleColor = 'background: rgb(204, 229, 204);';
													$isCancel	= 'NO';
													if($rowFetchInvoice['status'] =='C')
													{
														$styleColor = 'background: rgb(255, 204, 204);';
														$isCancel	= 'YES';
														$type = "Invoice Cancelled";
													}
													//$intAmt += calculateTaxAmmount($rowFetchInvoice['service_product_price'],$cfg['INTERNET.HANDLING.PERCENTAGE']);
												?>
													<tr class="tlisting" style=" <?=$styleColor?>">
														<td align="center"><?=$invoiceCounter?></td>
														<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
														<td align="center">ONLINE</td>
														<td align="left">
															<?=$type?>
														</td>
														<td align="center"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
														
														<td align="right">
														<?=number_format($rowFetchInvoice['service_product_price'],2)?> + <?=number_format($taxAmt = calculateTaxAmmount($rowFetchInvoice['service_product_price'],$cfg['INTERNET.HANDLING.PERCENTAGE']),2)?> = <?=$slipDetails['currency']?> <?=number_format($totAmt += $rowFetchInvoice['service_product_price']+$taxAmt,2)?>
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
											<tr class="theader">
												<td width="30" align="right" colspan="5">Current Amount:</td>
												<td width="110" align="right">
												 <strong style="float:right;"><u><?=$slipDetails['currency']?>
														<?=number_format(($totAmt),2)?></u></strong>
												</td>
												<td width="100" align="center"></td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr style="display:none;" use="offlineTr">
									<td align="left" colspan="4">
										 <table width="100%">
											<tr class="thighlight">
												<td colspan="7" align="left">New Slip Details</td>
											</tr>
											<tr class="theader">
												<td width="30" align="center">Sl No</td>
												<td align="left"  width="100">Invoice No</td>
												<td width="60" align="center">Invoice Mode</td>
												
												<td align="center">Invoice For</td>
												<td width="70" align="center">Invoice Date</td>
												<td width="230" align="right">Service Amt + Internet Handling Charge = Total AMount</td>
												<td width="100" align="center">Payment Status</td>
											</tr>
											<?php
											
											$invoiceCounter                 = 0;
											$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$slipId);
																			
											$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
											
											//$ffff = getInvoice($rowFetchSlip['id']);
											//echo "<pre>";print_r($ffff);echo "</pre>";
											$intAmt							= 0; 
											$total 							= 0;
											if($resultFetchInvoice)
											{
												foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
												{
													$invoiceCounter++;
													$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
													$type			 = "";
													$isConfarance	 = "";
													if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
													{
														$type = "CONFERENCE REGISTRATION OF ".$thisUserDetails['user_full_name'];
														if($rowFetchInvoice['delegate_id'] == $delegateId)
														{
															$isConfarance = "CONFERENCE";
														}
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
													{
														$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
														$type =  strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION  OF ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
													{
														$type = $cfg['RESIDENTIAL_NAME']." OF ".$thisUserDetails['user_full_name'];
														if($rowFetchInvoice['delegate_id'] == $delegateId)
														{
															$isConfarance = "CONFERENCE";
														}
													}
													if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
													{
														$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
													{
														$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
													}
													if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
													{
														$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
														
														$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
													}
													$styleColor = 'background: rgb(204, 229, 204);';
													$isCancel	= 'NO';
													if($rowFetchInvoice['status'] =='C')
													{
														$styleColor = 'background: rgb(255, 204, 204);';
														$isCancel	= 'YES';
														$type = "Invoice Cancelled";
													}
													$intAmt += calculateTaxAmmount($rowFetchInvoice['service_product_price'],$cfg['INTERNET.HANDLING.PERCENTAGE']);
												?>
													<tr class="tlisting" style=" <?=$styleColor?>">
														<td align="center"><?=$invoiceCounter?></td>
														<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
														<td align="center">OFFLINE</td>
														<td align="left">
															<?=$type?>
														</td>
														<td align="center"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
														<td align="right">
														<?
														if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
														{
															$clsfId	   = getUserClassificationId($rowFetchInvoice['delegate_id']);
															$cutoffId  = getUserCutoffId($rowFetchInvoice['delegate_id']);
															$tariffAmt = getRegistrationTariffAmount($clsfId,$cutoffId);
															$total 	  += $tariffAmt + 0 ;
														?>
														<?=number_format($tariffAmt,2)?> + 0.00 = <?=$slipDetails['currency']?> <?=number_format(($tariffAmt),2)?>
														<?
														}
														else
														{
															$total 	  += $rowFetchInvoice['service_product_price'] + 0 ;
														?>
														<?=number_format($rowFetchInvoice['service_product_price'],2)?> + 0.00 = <?=$slipDetails['currency']?> <?=number_format($rowFetchInvoice['service_product_price'],2)?>
														<?
														}
														?>
														</td>
														
														
														<td align="center">
															<?php
															if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
															{
															?>
																<span style="color:#C70505;">UNPAID</span>
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
											<tr class="theader">
												<td width="30" align="right" colspan="5">Current Amount:</td>
												<td width="110" align="right">
												 <strong style="float:right;"><u><?=$slipDetails['currency']?> <?=number_format($total,2)?></u></strong>
												</td>
												<td width="100" align="center"></td>
											</tr>
											<?php
												setPaymentTermsRecord("add");
											?>
										</table>
									</td>
								</tr>
								
								<tr>
									<td align="right" colspan="4">
										 <input type="submit" value="Update" class="btn btn-large btn-blue" />
									</td>
								</tr>
							</table>			
						</td>
					</tr>
				</tbody> 
			</table>
		</form>
		<?
	}
	
	/****************************************************************************/
	/*                      SHOW PAYMENT CONFIRMATION EMAIL CONTENT                      */
	/****************************************************************************/
	function ResendInvoiceDetailsMail($cfg, $mycms)
	{
		
		global $cfg, $mycms;
		$delegateId = trim($_REQUEST['delegateId']);
		$slipId 	= trim($_REQUEST['slipId']);
		
		$rowFetchUserDetails = getUserDetails($delegateId);
		$invoiceDetails 	 = invoiceDetailsOfSlip($slipId);		
		$paymentDetails		 = paymentDetails($slipId);
		$services 			 = servicesOfSlip($slipId);
		$totalSlipAmount 	 = invoiceAmountOfSlip($slipId);
		
		$paymentId 			 = $paymentDetails['id'];
		?>
		<form name="sendMail" id="sendMail" action="registration.process.php">		
			<input type="hidden" name="act" value="sendFinalMail" />
			<input type="hidden" name="delegateId" id="delegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="slipId" id="slipId" value="<?=$_REQUEST['slipId']?>" />
			<input type="hidden" name="paymentId" id="paymentId" value="<?=$_REQUEST['paymentId']?>" />
		
			<?
			$sqlFetchSlipData =	array();
			$sqlFetchSlipData['QUERY'] = "SELECT * FROM "._DB_SLIP_." WHERE id= ? AND payment_status = ?";
			$sqlFetchSlipData['PARAM'][]   = array('FILD' => 'id',             'DATA' =>$_REQUEST['slipId'],       'TYP' => 's');
			$sqlFetchSlipData['PARAM'][]   = array('FILD' => 'payment_status', 'DATA' =>'PAID',   'TYP' => 's');
			$fetchDataArr = $mycms->sql_select($sqlFetchSlipData);
			
			$fetchData =  $fetchDataArr[0];
			if($fetchData['invoice_mode']=='OFFLINE')
			{ 
			$msg = offline_registration_acknowledgement_message($_REQUEST['delegateId'],$_REQUEST['slipId'] , $_REQUEST['paymentId'], 'RETURN_TEXT');
			}
			else
			{ 
			$msg = online_conference_payment_confirmation_message($_REQUEST['delegateId'],$_REQUEST['slipId'] , $_REQUEST['paymentId'], 'RETURN_TEXT');
			}
			?>
			<input type="hidden" name="invoice_mode" id="invoice_mode" value="<?=$fetchData['invoice_mode']?>" />
			<table width="100%" align="center" class="tborder" cellpadding="3px"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Invoice Mail</td> 
					</tr> 
				</thead>
				<tbody>
					<!--<tr>
						<td align="left">Subject</td>
						<td align="left"><?=$msg['MAIL_SUBJECT']?></td>
					</tr>-->
					<tr>
						<td align="left">Subject</td>
						<td align="left">
						<textarea name="mail_subject" id="mail_subject" style="width:46%; padding:6px;" ><?=$msg['MAIL_SUBJECT']?></textarea>
						</td>
					</tr>
					<tr>
						<td align="left">To Name</td>
						<td align="left">
							<input type="text" name="user_full_name" value="<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					<tr>
						<td align="left">To Email</td>
						<td align="left">
							<input type="text" name="user_email_id" value="<?=$rowFetchUserDetails['user_email_id']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<table>
								<tr>
									<td style="-moz-box-shadow: 0px 0px 8px #000000; -webkit-box-shadow: 0px 0px 8px #000000; box-shadow: 0px 0px 8px #000000;">
									<?
										echo $msg['MAIL_BODY'];									
									?>
									</td>
								</tr>
							</table>			
						</td>
					</tr>					
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&id=<?=$_REQUEST['delegateId']?>';" />
						
							<input type="submit" name="bttnStep3" id="bttnStep3" value="SEND MAIL" 
							 class="btn btn-small btn-blue" />
						
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
		
	function ResendRegConfirmationMail($cfg, $mycms)
	{		
		global $cfg, $mycms;
		
		
		
		$delegateId = trim($_REQUEST['delegateId']);
		$slipId 	= trim($_REQUEST['slipId']);
		
		$rowFetchUserDetails = getUserDetails($delegateId);
		$invoiceDetails 	 = invoiceDetailsOfSlip($slipId);		
		$paymentDetails		 = paymentDetails($slipId);
		$services 			 = servicesOfSlip($slipId);
		$totalSlipAmount 	 = invoiceAmountOfSlip($slipId);
		
		$paymentId 			 = $paymentDetails['id'];
				
		if($paymentDetails['payment_mode']=='Online')
		{
			if(in_array('DELEGATE_CONFERENCE_REGISTRATION',$services))
			{
				$msg = 	online_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(in_array('DELEGATE_RESIDENTIAL_REGISTRATION',$services))
			{
				$msg = 	online_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(in_array('ACCOMPANY_CONFERENCE_REGISTRATION',$services))
			{
				$msg = 	online_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION')
			{
				if($rowFetchUserDetails['registration_classification_id']=='11')
				{
					$msg = complementary_workshop_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$mailBody = $msg['MAIL_BODY'];
				}
				else
				{				
					$msg = online_conference_registration_confirmation_workshop_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$mailBody = $msg['MAIL_BODY'];
				}
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST')
			{
				$msg = online_accommodation_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_DINNER_REQUEST')
			{
				$msg = online_dinner_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
		}
		else
		{
			if(in_array('DELEGATE_CONFERENCE_REGISTRATION',$services))
			{
				$msg = offline_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(in_array('DELEGATE_RESIDENTIAL_REGISTRATION',$services))
			{
				$msg = offline_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(in_array('ACCOMPANY_CONFERENCE_REGISTRATION',$services))
			{
				//$msg =  offline_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$msg = offline_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION')
			{
				if($rowFetchUserDetails['registration_classification_id']=='11')
				{
					$msg = complementary_workshop_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$mailBody = $msg['MAIL_BODY'];
				}
				else
				{	
					$msg = offline_conference_registration_confirmation_workshop_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$mailBody = $msg['MAIL_BODY'];
				}
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST')
			{
				$msg = offline_accommodation_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$mailBody = $msg['MAIL_BODY'];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_DINNER_REQUEST')
			{
				$msg = offline_dinner_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');	
				$mailBody = $msg['MAIL_BODY'];	
			}
		}
		
		
		
		?>
		<form name="sendMail" id="sendMail" action="registration.process.php" method="post">
			<input type="hidden" name="act" value="sendRegFinalMail" />
			<input type="hidden" name="delegateId" id="delegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="slipId" id="slipId" value="<?=$_REQUEST['slipId']?>" />
			<input type="hidden" name="paymentId" id="paymentId" value="<?=$_REQUEST['paymentId']?>" />
			<input type="hidden" name="buttonForSpot" id="buutonForSpot" value="<?=$_REQUEST['button']?>" />
			
			<input type="hidden" name="invoice_mode" id="invoice_mode" value="<?=$fetchData['invoice_mode']?>" />
			<table width="100%" align="center" class="tborder" cellpadding="3px"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration Confirmation Mail</td> 
					</tr> 
				</thead>
				<tbody>
					
					<tr>
						<td align="left">Subject</td>
						<td align="left">
						<textarea name="mail_subject" id="mail_subject" style="width:46%; padding:6px;" ><?=$msg['MAIL_SUBJECT']?></textarea>
						</td>
					</tr>
					<tr>
						<td align="left">To Name</td>
						<td align="left">
							<input type="text" name="user_full_name" value="<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					<tr>
						<td align="left">To Email</td>
						<td align="left">
							<input type="text" name="user_email_id" value="<?=$rowFetchUserDetails['user_email_id']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<table>
								<tr>
									<td style="-moz-box-shadow: 0px 0px 8px #000000; -webkit-box-shadow: 0px 0px 8px #000000; box-shadow: 0px 0px 8px #000000;">
									<?
										echo $mailBody;									
									?>
									<textarea name="mail_body" id="mail_body" style="display:none;"><?=$mailBody?></textarea>
									</td>
								</tr>
							</table>			
						</td>
					</tr>					
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<?
							if($_REQUEST['button'] =='backToSpot')
							{
							?>
								<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&button=backToSpot&id=<?=$_REQUEST['delegateId']?>';" />
							<?
							}
							else
							{
							?>
								<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&id=<?=$_REQUEST['delegateId']?>';" />
							<?
							}
							?>
						
							<input type="submit" name="bttnStep3" id="bttnStep3" value="SEND MAIL" 
							 class="btn btn-small btn-blue" />
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
		
	function ResendRegConfirmationSMS($cfg, $mycms)
	{  
	  
		global $cfg, $mycms;
		
		$delegateId = trim($_REQUEST['delegateId']);
		$slipId 	= trim($_REQUEST['slipId']);
		
		
		$rowFetchUserDetails = getUserDetails($delegateId);
		$invoiceDetails 	 = invoiceDetailsOfSlip($slipId);	
		$paymentDetails		 = paymentDetails($slipId);
		$services 			 = servicesOfSlip($slipId);
		$totalSlipAmount 	 = invoiceAmountOfSlip($slipId);
		
		$paymentId 			 = $paymentDetails['id'];
		
		if($paymentDetails['payment_mode']=='Online')
		{
			if(in_array('DELEGATE_CONFERENCE_REGISTRATION',$services))
			{
				$msg = 	online_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(in_array('DELEGATE_RESIDENTIAL_REGISTRATION',$services))
			{
				$msg = 	online_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(in_array('ACCOMPANY_CONFERENCE_REGISTRATION',$services))
			{
				$msg = 	online_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION')
			{
				if($rowFetchUserDetails['registration_classification_id']=='11')
				{
					$msg = complementary_workshop_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$smsNo = $msg['SMS_NO'];
					$registrSms = $msg['SMS_BODY'][0];
				}
				else
				{				
					$msg = online_conference_registration_confirmation_workshop_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$smsNo = $msg['SMS_NO'];
					$paymentSms = $msg['SMS_BODY'][0];
					$registrSms = $msg['SMS_BODY'][1];
				}
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST')
			{
				$msg = online_accommodation_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_DINNER_REQUEST')
			{
				$msg = online_dinner_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
		}
		else
		{ 
			if(in_array('DELEGATE_CONFERENCE_REGISTRATION',$services))
			{
				$msg = offline_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				
				//$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(in_array('DELEGATE_RESIDENTIAL_REGISTRATION',$services))
			{
				$msg = 	offline_conference_registration_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(in_array('ACCOMPANY_CONFERENCE_REGISTRATION',$services))
			{ 
				$msg =  offline_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION')
			{
				if($rowFetchUserDetails['registration_classification_id']=='11')
				{
					$msg = complementary_workshop_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$smsNo = $msg['SMS_NO'];
					$registrSms = $msg['SMS_BODY'][0];
				}
				else
				{	
					$msg = offline_conference_registration_confirmation_workshop_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
					$smsNo = $msg['SMS_NO'];
					$paymentSms = $msg['SMS_BODY'][0];
					$registrSms = $msg['SMS_BODY'][1];
				}
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST')
			{
				$msg = offline_accommodation_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];
			}
			elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_DINNER_REQUEST')
			{
				$msg = offline_dinner_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');	
				$smsNo = $msg['SMS_NO'];
				$paymentSms = $msg['SMS_BODY'][0];
				$registrSms = $msg['SMS_BODY'][1];		
			}
		}
		   $sqlFetchSlipData['QUERY'] = "SELECT * FROM "._DB_SLIP_." WHERE id= ? AND payment_status = ?";
			 $sqlFetchSlipData['PARAM'][]   = array('FILD' => 'id',             'DATA' =>$_REQUEST['slipId'],       'TYP' => 's');
			 $sqlFetchSlipData['PARAM'][]   = array('FILD' => 'payment_status', 'DATA' =>'PAID',   'TYP' => 's');
			$fetchDataArr = $mycms->sql_select($sqlFetchSlipData);
			//print_r($fetchData);
			$fetchData =  $fetchDataArr[0];
		
		?>
		<form name="sendMail" id="sendMail" action="registration.process.php" method="post">
			<input type="hidden" name="act" value="sendRegFinalSMS" />
			<input type="hidden" name="delegateId" id="delegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="slipId" id="slipId" value="<?=$_REQUEST['slipId']?>" />
			<input type="hidden" name="paymentId" id="paymentId" value="<?=$_REQUEST['paymentId']?>" />
			<input type="hidden" name="invoice_mode" id="invoice_mode" value="<?=$fetchData['invoice_mode']?>" />
			<table width="100%" align="center" class="tborder" cellpadding="3px"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration Confirmation SMS</td> 
					</tr> 
				</thead>
				
				<tbody>
					<tr>
						<td align="left" style="font-weight:bold;" width="100px;">Name</td>
						<td align="left">
							<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>
							<input type="text" name="user_full_name" id="user_full_name" value="<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>"  style="width:46%; padding:6px;display:none;"/>
						</td>
					</tr>					
					<tr>
						<td align="left" style="font-weight:bold;">Contact No.</td>
						<td align="left">
						
							<input type="text" name="user_number" id="user_number" value="<?=$rowFetchUserDetails['user_mobile_isd_code']." - ".$rowFetchUserDetails['user_mobile_no']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					<tr>
						<td align="left" colspan="2" style="font-weight:bold;">Payment Confirmation SMS </td>
					</tr>
					<tr>
						<td align="left" colspan="2">
						<?
							echo $paymentSms;									
						?>
						<textarea name="payment_sms_body" id="payment_sms_body" style="display:none;"><?=$paymentSms?></textarea>
						</td>
					</tr>
					<!--<tr>
						<td align="left" colspan="2" style="font-weight:bold;">Registration Confirmation SMS</td>
					</tr>
					<tr>
						<td align="left" colspan="2">
						<?
							echo $registrSms;									
						?>
						<textarea name="registration_sms_body" id="registration_sms_body" style="display:none;"><?=$registrSms?></textarea>
						</td>
					</tr>	-->				
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&id=<?=$_REQUEST['delegateId']?>';" />
						
							<input type="submit" name="bttnStep3" id="bttnStep3" value="SEND SMS" 
							 class="btn btn-small btn-blue" />
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
	
	function ResendAccknowledgementConfirmationMail($cfg, $mycms)
	{		
		global $cfg, $mycms;
		
		
		
		$delegateId = trim($_REQUEST['delegateId']);
		$slipId 	= trim($_REQUEST['slipId']);
		
		$rowFetchUserDetails = getUserDetails($delegateId);
		$invoiceDetails 	 = invoiceDetailsOfSlip($slipId);		
		$paymentDetails		 = paymentDetails($slipId);
		$services 			 = servicesOfSlip($slipId);
		$totalSlipAmount 	 = invoiceAmountOfSlip($slipId);
		
		$paymentId 			 = $paymentDetails['id'];
		
		if(in_array('DELEGATE_CONFERENCE_REGISTRATION',$services))
		{
			$msg = 	offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');	
			$mailBody = $msg['MAIL_BODY'];
		}
		elseif(in_array('DELEGATE_RESIDENTIAL_REGISTRATION',$services))
		{
			$msg = 	offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');	
			$mailBody = $msg['MAIL_BODY'];
		}
		elseif(in_array('ACCOMPANY_CONFERENCE_REGISTRATION',$services))
		{
			$msg = 	offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');	
			$mailBody = $msg['MAIL_BODY'];
		}
		elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION')
		{
			$msg = offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');
			$mailBody = $msg['MAIL_BODY'];
		}
		elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST')
		{
			$msg = offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');
			$mailBody = $msg['MAIL_BODY'];
		}
		elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_DINNER_REQUEST')
		{
			$msg = offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');
			$mailBody = $msg['MAIL_BODY'];
		}
		
		?>
		<form name="sendMail" id="sendMail" action="registration.process.php" method="post">
			<input type="hidden" name="act" value="sendRegFinalMail" />
			<input type="hidden" name="delegateId" id="delegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="slipId" id="slipId" value="<?=$_REQUEST['slipId']?>" />
			<input type="hidden" name="paymentId" id="paymentId" value="<?=$_REQUEST['paymentId']?>" />
			<input type="hidden" name="buttonForSpot" id="buutonForSpot" value="<?=$_REQUEST['button']?>" />
			
			<input type="hidden" name="invoice_mode" id="invoice_mode" value="<?=$fetchData['invoice_mode']?>" />
			<table width="100%" align="center" class="tborder" cellpadding="3px"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration Confirmation Mail</td> 
					</tr> 
				</thead>
				<tbody>
					
					<tr>
						<td align="left">Subject</td>
						<td align="left">
						<textarea name="mail_subject" id="mail_subject" style="width:46%; padding:6px;" ><?=$msg['MAIL_SUBJECT']?></textarea>
						</td>
					</tr>
					<tr>
						<td align="left">To Name</td>
						<td align="left">
							<input type="text" name="user_full_name" value="<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					<tr>
						<td align="left">To Email</td>
						<td align="left">
							<input type="text" name="user_email_id" value="<?=$rowFetchUserDetails['user_email_id']?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<table>
								<tr>
									<td style="-moz-box-shadow: 0px 0px 8px #000000; -webkit-box-shadow: 0px 0px 8px #000000; box-shadow: 0px 0px 8px #000000;">
									<?
										echo $mailBody;									
									?>
									<textarea name="mail_body" id="mail_body" style="display:none;"><?=$mailBody?></textarea>
									</td>
								</tr>
							</table>			
						</td>
					</tr>					
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<?
							if($_REQUEST['button'] =='backToSpot')
							{
							?>
								<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&button=backToSpot&id=<?=$_REQUEST['delegateId']?>';" />
							<?
							}
							else
							{
							?>
								<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&id=<?=$_REQUEST['delegateId']?>';" />
							<?
							}
							?>
						
							<input type="submit" name="bttnStep3" id="bttnStep3" value="SEND MAIL" 
							 class="btn btn-small btn-blue" />
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
	
	function ResendAccknowledgementConfirmationSMS($cfg, $mycms)
	{
        
		global $cfg, $mycms;
		
		$delegateId = trim($_REQUEST['delegateId']);
		$slipId 	= trim($_REQUEST['slipId']);
		
		$rowFetchUserDetails = getUserDetails($delegateId);
		$invoiceDetails 	 = invoiceDetailsOfSlip($slipId);		
		$paymentDetails		 = paymentDetails($slipId);
		$services 			 = servicesOfSlip($slipId);
		$totalSlipAmount 	 = invoiceAmountOfSlip($slipId);
		
		$paymentId 			 = $paymentDetails['id'];
		
		if(in_array('DELEGATE_CONFERENCE_REGISTRATION',$services))
		{
			$msg = 	offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');	
			$smsNo = $msg['SMS_NO'];
			$paymentSms = $msg['SMS_BODY'];
			$registrSms = $msg['SMS_BODY'][1];
		}
		elseif(in_array('DELEGATE_RESIDENTIAL_REGISTRATION',$services))
		{
			$msg = 	offline_registration_acknowledgement_message($delegateId, $slipId,$paymentId, 'RETURN_TEXT');	
			$smsNo = $msg['SMS_NO'];
			$paymentSms = $msg['SMS_BODY'];
			$registrSms = $msg['SMS_BODY'][1];
		}
		elseif(in_array('ACCOMPANY_CONFERENCE_REGISTRATION',$services))
		{
			$msg = 	online_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
			$smsNo = $msg['SMS_NO'];
			$paymentSms = $msg['SMS_BODY'][0];
			$registrSms = $msg['SMS_BODY'][1];
		}
		elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION')
		{
			$msg = online_conference_registration_confirmation_workshop_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
			$smsNo = $msg['SMS_NO'];
			$paymentSms = $msg['SMS_BODY'][0];
			$registrSms = $msg['SMS_BODY'][1];
		}
		elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST')
		{
			$msg = online_accommodation_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
			$smsNo = $msg['SMS_NO'];
			$paymentSms = $msg['SMS_BODY'][0];
			$registrSms = $msg['SMS_BODY'][1];
		}
		elseif(sizeof($services)==1 && $services[0] == 'DELEGATE_DINNER_REQUEST')
		{
			$msg = online_dinner_confirmation_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
			$smsNo = $msg['SMS_NO'];
			$paymentSms = $msg['SMS_BODY'][0];
			$registrSms = $msg['SMS_BODY'][1];
		}
		
		?>
		<form name="sendMail" id="sendMail" action="registration.process.php" method="post">
			<input type="hidden" name="act" value="sendRegFinalSMS" />
			<input type="hidden" name="delegateId" id="delegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="slipId" id="slipId" value="<?=$_REQUEST['slipId']?>" />
			<input type="hidden" name="paymentId" id="paymentId" value="<?=$_REQUEST['paymentId']?>" />
			<input type="hidden" name="invoice_mode" id="invoice_mode" value="<?=$fetchData['invoice_mode']?>" />
			<table width="100%" align="center" class="tborder" cellpadding="3px"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Registration Confirmation SMS</td> 
					</tr> 
				</thead>
				<tbody>
					<tr>
						<td align="left" style="font-weight:bold;" width="100px;">Name</td>
						<td align="left">
							<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>
							<input type="text" name="user_full_name" id="user_full_name" value="<?=$rowFetchUserDetails['user_first_name'].' ' .$rowFetchUserDetails['user_last_name']?>"  style="width:46%; padding:6px;display:none;"/>
						</td>
					</tr>					
					<tr>
						<td align="left" style="font-weight:bold;">Contact no.</td>
						<td align="left">
							<input type="text" name="user_number" id="user_number" value="<?=$smsNo?>"  style="width:46%; padding:6px;"/>
						</td>
					</tr>
					<?
						if($totalSlipAmount > 0)
						{
					?>
					<tr>
						<td align="left" colspan="2" style="font-weight:bold;">Acknowledgement SMS </td>
					</tr>
					<tr>
						<td align="left" colspan="2">
						<?
							echo $paymentSms;									
						?>
						<textarea name="payment_sms_body" id="payment_sms_body" style="display:none;"><?=$paymentSms?></textarea>
						</td>
					</tr>
					<?
						}
					?>										
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">
							<input type="button" name="bttnStep3" id="bttnStep3" value="BACK" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=invoice&id=<?=$_REQUEST['delegateId']?>';" />
						
							<input type="submit" name="bttnStep3" id="bttnStep3" value="SEND SMS" 
							 class="btn btn-small btn-blue" />
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
	
	function viewTokenMailDetails($cfg, $mycms)
	{
		
		global $cfg, $mycms;
		
		$slipId				  	  = addslashes(trim($_REQUEST['id']));
		
		$slipResults	          = slipDetails($slipId);
		$userResults	          = getUserDetails($slipResults['delegate_id']);	
		
		$mail 					  = registration_token_request_message($slipResults['delegate_id'], $slipResults['registration_token'],'RETURN_TEXT');
		$emailBody_1			  = $mail['MAIL_BODY'];
		?>
		<form name="sendMail" id="sendMail" action="registration.process.php">
			<input type="hidden" name="act" value="sendTokenMail" />
			<input type="hidden" name="delegate_id" id="delegate_id" value="<?=$slipResults['delegate_id']?>">
			<input type="hidden" name="registration_token" id="registration_token" value="<?=$slipResults['registration_token']?>" >
			
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Confirmation Mail</td> 
					</tr> 
				</thead>
				<tbody>
					<tr>
						<td style="margin:0px; padding:0px;" align="center"> 							
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">User Details</td>
								</tr>
								<tr>
									<td width="20%" align="left">Name:</td>
									<td width="30%" align="left">
										<?=$userResults['user_title']?> 
										<?=$userResults['user_first_name']?> 
										<?=$userResults['user_middle_name']?> 
										<?=$userResults['user_last_name']?>
									</td>
									<td width="20%" align="left">Email Id:</td>
									<td width="30%" align="left"><?=$userResults['user_email_id']?></td>
								</tr>
								<tr>
									<td align="left">Registration Id:</td>
									<td align="left">
									<?php
									if($userResults['registration_payment_status']=="PAID" 
									   || $userResults['registration_payment_status']=="ZERO_VALUE"
									   || $userResults['registration_payment_status']=="COMPLIMENTARY")
									{
										echo $userResults['user_registration_id'];
									}
									else
									{
										echo "-";
									}
									?>
									</td>
									<td align="left">Unique Sequence:</td>
									<td align="left"><?
									if($userResults['registration_payment_status']=="PAID" 
									   || $userResults['registration_payment_status']=="ZERO_VALUE"
									   || $userResults['registration_payment_status']=="COMPLIMENTARY")
									{
										echo $userResults['user_unique_sequence'];
									}
									else
									{
										echo "-";
									}
									?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td >							
						<br /><br /><br />
						<center>
							<table>
								<tr>
									<td style="-moz-box-shadow: 0px 0px 8px #000000; -webkit-box-shadow: 0px 0px 8px #000000; box-shadow: 0px 0px 8px #000000;">
									<?=$emailBody_1?>
									</td>
								</tr>
							</table>
						</center>					
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="button" name="bttnStep3" id="bttnStep3" value="Back" 
							 class="btn btn-small btn-red" onclick="window.location.href='registration.php';" />
						
							<input type="submit" name="bttnStep3" id="bttnStep3" value="SEND MAIL" 
							 class="btn btn-small btn-blue" />
						
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
	
	function reallocationOfWorkshop($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access			= buttonAccess($loggedUserId);
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser = array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                               WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<form name="frmSearch" method="post" action="registration.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="show" value="reallocationOfWorkshop" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Re-allocation of Workshop</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus();
									//	searchStatus('?show=encodersUsers');
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification = array();
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` 
																							FROM "._DB_REGISTRATION_CLASSIFICATION_." 
																							WHERE status = 'A' AND `id`  NOT IN (3,6)";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "Course Only - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo $cfg['RESIDENTIAL_NAME']." - ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Payment Status:</td>
									<td align="left">
									
										<select name="src_payment_status" id="src_payment_status" style="width:96%;">
											<option value="">-- Select Payment Status --</option>
											<option value="PAID" <?=(trim($_REQUEST['src_payment_status']=="PAID"))?'selected="selected"':''?>>PAID</option>
											<option value="UNPAID" <?=(trim($_REQUEST['src_payment_status']=="UNPAID"))?'selected="selected"':''?>>UNPAID</option>
											<option value="COMPLIMENTARY" <?=(trim($_REQUEST['src_payment_status']=="COMPLIMENTARY"))?'selected="selected"':''?>>COMPLIMENTARY</option>
										</select>
										
									</td>
									
									<td align="left">Registration type:</td>
									<td align="left">
									
										<select name="src_registration_type" id="src_registration_type" style="width:96%;">
											<option value="">-- Select Registration type --</option>
											<option value="GENERAL" <?=(trim($_REQUEST['src_registration_type']=="GENERAL"))?'selected="selected"':''?>>GENERAL</option>
											<option value="COUNTER" <?=(trim($_REQUEST['src_registration_type']=="COUNTER"))?'selected="selected"':''?>>COUNTER</option>
										</select>
										
									</td>
								</tr>	
								
								<tr>
									<td align="left">Payment Date:</td>
									<td align="left">
									
										<input type="text" name="src_payment_date" id="src_payment_date" readonly="readonly"  rel="tcal"
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_payment_date']?>" />
										
									</td>
									<td align="left">Workshop:</td>
									<td align="left">
									<?
									    $sqlGetWorkshop = array();
										$sqlGetWorkshop['QUERY'] = "SELECT * 
																		FROM "._DB_WORKSHOP_CLASSIFICATION_." 
																		WHERE status = 'A' ";
										
										$resultGetWorkshop = $mycms->sql_select($sqlGetWorkshop);
										//echo "<pre>"; print_r($resultGetWorkshop); echo "</pre>";
										$session ="";
									?>
										<select name="src_workshop_type" id="src_workshop_type" style="width:96%;" >
										 <option value=""> -- Select Workshop Type -- </option>
										 <?	
										 	foreach($resultGetWorkshop as $keyWorkshop => $rowGetWorkshop)
											{												
												if($rowGetWorkshop['id'] == 1 || $rowGetWorkshop['id'] == 11 || $rowGetWorkshop['id'] == 21)
												{ 													
													if($rowGetWorkshop['id'] == 1)
													{
														$type = "Morning(Old)";
													}
													else if($rowGetWorkshop['id'] == 11)
													{
														$type = "Afternoon(Old)";
													}
													else if($rowGetWorkshop['id'] == 21)
													{
														$type = "New";
													}													
												?>
													 <optgroup label="workshop - <?=$type?>">
												<?
												}
												?>												
												<option value="<?=$rowGetWorkshop['id']?>" <?=($_REQUEST['src_workshop_type']==$rowGetWorkshop['id'])?'selected="selected"':''?>><?=$rowGetWorkshop['classification_title']?></option>
												<?
												if($rowGetWorkshop['id'] == 10 || $rowGetWorkshop['id'] == 20 || $rowGetWorkshop['id'] == 31)
												{ 
												?>
													</optgroup>
												<?
												}
												$session = $rowGetWorkshop['type'] ;
											}
										 ?>										 									 
										</select>
									</td>								
								</tr>
								<tr>
									<td align="left">Pay Mode:</td>
									<td align="left">									
										<select name="src_payment_mode" id="src_payment_mode" style="width:96%;">
											<option value="">-- Select Payment Mode --</option>
											<option value="Cash" <?=(trim($_REQUEST['src_payment_mode']=="Cash"))?'selected="selected"':''?>>Cash</option>
											<option value="Card" <?=(trim($_REQUEST['src_payment_mode']=="Card"))?'selected="selected"':''?>>Card</option>
											<option value="Cheque" <?=(trim($_REQUEST['src_payment_mode']=="Cheque"))?'selected="selected"':''?>>Cheque</option>
											<option value="Draft" <?=(trim($_REQUEST['src_payment_mode']=="Draft"))?'selected="selected"':''?>>Draft</option>
											<option value="NEFT" <?=(trim($_REQUEST['src_payment_mode']=="NEFT"))?'selected="selected"':''?>>NEFT/RTGS</option>
										</select>
									</td>									
									<td>Payment Related No.</td>
									<td><input type="text" name="src_payment_no" id="src_payment_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_payment_no']?>" />
									</td>
								</tr>	
								
							</table>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</th>
									<td align="left">Name & Contact</th>
									<td width="110" align="left">Registration Type</th>
									<td width="180" align="left">Registration Details</th>
									<td width="460" align="center">Workshop Dtls</th>
								</tr>
							</thead>
							<tbody>
								<?php
								
								
								
								$idArr = getAllDelegates("","",$alterCondition);
								
								if($idArr)
								{
									
									foreach($idArr as $i=>$id) 
									{
										$status = true;
										$rowFetchUser = getUserDetails($id);
										$counter      = $counter + 1;
										$color = "#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_full_name'])?> 
												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												?>
											</td>											
											<td align="left" valign="top">
												<span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>;"><b><?=$rowFetchUser['registration_request']?></b></span>
												<br />
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
													
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Reg Id : ".$rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Us No : ".strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												$totalPaid = 0;
												$totalUnpaid = 0;
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											<td align="left" valign="top">
												<table width="100%" style="border: 1px solid black;">
													<?php
														$invoiceAlterCondition 			= " AND inv.service_type = 'DELEGATE_WORKSHOP_REGISTRATION'";
														$sqlFetchInvoice                = getInvoiceWithCancelInvoiceDetails("",$rowFetchUser['id'],"",$invoiceAlterCondition);
																	
														$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
														
														if($resultFetchInvoice)
														{
															$oneAlreadyShifted = false;
															foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
															{
																$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'],true);
																if($workShopDetails['display']=='N')
																{
																	if($rowFetchInvoice['remarks'] == 'Adjusted Workshop')
																	{
																		$oneAlreadyShifted = true;
																	}
																}
															}	
															
															foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
															{
																//echo "<pre>"; print_r($rowFetchInvoice); echo "</pre>";
																$invoiceCounter++;
																$slip = getInvoice($rowFetchInvoice['slip_id']);
																$returnArray    = discountAmount($rowFetchInvoice['id']);
																$percentage     = $returnArray['PERCENTAGE'];
																$totalAmount    = $returnArray['TOTAL_AMOUNT'];
																$discountAmount = $returnArray['DISCOUNT'];
																
																
																$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
																getRegClsfName(getUserClassificationId($delegateId));
																
																$type			 = "";
																
																if(isset($rowFetchInvoice['Refund_status']) && $rowFetchInvoice['Refund_status'] == 'Not_refunded')
																{
																	$rowBackGround = "#FFFFCA";
																}
																elseif(isset($rowFetchInvoice['Refund_status']) && $rowFetchInvoice['Refund_status'] == 'Refunded')
																{
																	$rowBackGround = "#FFCCCC";
																}
																else
																{
																	$rowBackGround = "#FFFFFF";
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
																{
																	$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'],true);																	
																	$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"WORKSHOP");
																	if($workShopDetails['display']=='Y')
																	{
														?>
																<tr class="tlisting" bgcolor="<?=$rowBackGround?>">
																	<td align="left" valign="top">
																		<?=$type?><br />
																		<span style="color:<?=$rowFetchInvoice['invoice_mode']=='ONLINE'?'#D77426':'#007FFF'?>;"><?=$rowFetchInvoice['invoice_mode']?></span>		
																		<br/><span style="color:#FF00CC;"><?=$rowFetchInvoice['remarks']?></span>																
																	</td>
																	<td align="right" width="21%" valign="top">																		
																		<?php
																		if($rowFetchInvoice['payment_status']=="UNPAID")
																		{
																		?>
																			<span style="color:#FF0000;"><strong style="font-size: 15px;">Unpaid</strong></span>
																		<?php
																		}
																		elseif($rowFetchInvoice['Refund_status']=="Not_refunded")
																		{
																		?>
																			<span style="color:#C70505;"><strong style="font-size: 15px;">Cancelled</strong></span>
																		<?php		
																		}
																		elseif($rowFetchInvoice['Refund_status']=="Refunded")
																		{
																		?>
																			<span style="color:#C70505;"><strong style="font-size: 15px;">Refunded</strong></span>
																			<br />
																			<?=$rowFetchInvoice['currency']?> <?=number_format($rowFetchInvoice['refunded_amount'],2)?>
																		<?php		
																		}
																		elseif(!$oneAlreadyShifted)
																		{ 
																		?>
																			<a href="registration.php?show=editReallocationOfWorkshop&id=<?=$rowFetchUser['id']?>&requestWorkshop=<?=$rowFetchInvoice['refference_id']?>&invoiceId=<?=$rowFetchInvoice['id']?><?=$searchString?>">
																			<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
																		<?php
																		}
																		?>
																	</td>
																</tr>
														<?php
																	}
																}
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
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		</form>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	}	
	
	function viewencodersUsers($cfg, $mycms)
	{
		global $searchArray, $searchString;
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access				= buttonAccess($loggedUserId);
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser				 = 	array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                               			 WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<form name="frmSearch" method="post" action="registration.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="encoders_search_registration" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
						<a href="download_excel.php?search=search&<?=$searchString?>"><img src="../images/Excel-icon.png"  style="float:right; padding-right: 10px;"/></a>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus('?show=encodersUsers');
										//searchStatus('?show=encodersUsers');
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification				 =	array();
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` 
																							FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A'";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "Course Only - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo $cfg['RESIDENTIAL_NAME']." - ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>								
							</table>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</th>
									<td align="left">Name & Contact</th>
									<td width="110" align="left">Registration Type</th>
									<td width="180" align="left">Registration Details</th>
									<td width="480" align="center">Service Dtls</th>
									<td width="70" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								
								$alterCondition = "";
								
								$alterCondition = "AND delegate.user_email_id LIKE '%@encoder%'";
								
								$sqlFetchUser         = "";
								
								$idArr = getAllDelegates("","",$alterCondition);
																
								if($idArr)
								{
									
									foreach($idArr as $i=>$id) 
									{
										$status = true;
										$rowFetchUser = getUserDetails($id);
										$counter      = $counter + 1;
										$color = "#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										//$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
										//echo ($rowFetchUser['user_mobile_isd_code']);
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_full_name'])?> 
												

												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
												
												}
												?>
											</td>
											
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Reg Id : ".$rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Us No : ".strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											<td align="left" valign="top">
												<table width="100%" style="border: 1px solid black;">
													<?php
														$sqlFetchInvoice                = getRegistrationInvoiceCancelInvoiceDetails("",$rowFetchUser['id'],"");
																	
														$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
														if($resultFetchInvoice)
														{
															foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
															{
																$invoiceCounter++;
																$slip = getInvoice($rowFetchInvoice['slip_id']);
																
																
																$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
																getRegClsfName(getUserClassificationId($delegateId));
																$type			 = "";
																if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
																{
																	$type = "Course Only - ".getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']));
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
																{
																	$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
																	$type =  getWorkshopName($workShopDetails['workshop_id']);
																}
																if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
																{
																	$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
																{
																	$type = $cfg['RESIDENTIAL_NAME']." - ".getRegClsfName(getUserClassificationId($rowFetchInvoice['delegate_id']));
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
																{
																	$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
																}
																if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
																{
																	$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
																	
																	$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
																}
														?>
																<tr class="tlisting">
																	<td align="left" width="30%" valign="top">
																		<?=$rowFetchInvoice['invoice_number']?><br />
																		<?=$slip['slip_number']?><br />
																		<strong style="color:#FE6F06;">by <?=getSlipOwner($slip['id'])?></strong>
																	</td>
																	<td align="left" valign="top">
																		<?=$type?><br />
																		<span style="color:<?=$rowFetchInvoice['invoice_mode']=='ONLINE'?'#D77426':'#007FFF'?>;"><?=$rowFetchInvoice['invoice_mode']?></span>
																		
																	</td>
																	<td align="right" width="21%" valign="top">
																		<?=$rowFetchInvoice['currency']?> <?=number_format($rowFetchInvoice['service_roundoff_price'],2)?><br />
																		<?php
																		if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
																		{
																		?>
																			<span style="color:#5E8A26;"><strong style="font-size: 15px;">Complimentary</strong></span>
																		<?php
																		}
																		elseif($rowFetchInvoice['payment_status']=="ZERO_VALUE")
																		{
																		?>
																			<span style="color:#009900;"><strong style="font-size: 15px;">Zero Value</strong></span>
																		<?php
																		}
																		else if($rowFetchInvoice['payment_status']=="PAID")
																		{
																		?>
																			<span style="color:#5E8A26;"><strong style="font-size: 15px;">Paid</strong></span>
																		<?php	
																			$resPaymentDetails      = paymentDetails($rowFetchInvoice['slip_id']);
																			if($resPaymentDetails['payment_mode']=="Online")
																			{
																				echo "[".$resPaymentDetails['atom_atom_transaction_id']."]";
																			}	
																		}
																		else if($rowFetchInvoice['payment_status']=="UNPAID")
																		{
																		?>
																			<span style="color:#C70505;"><strong style="font-size: 15px;">Unpaid</strong></span>
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
											<!--<td align="center" valign="top">Payment</td-->
											<td align="center" valign="top">
											<?php
												if($rowFetchUser['isWorkshop']=="N" && $rowFetchUser['isAccommodation']=='N')
												{
												?>
													<a href="registration.php?show=addWorkshop&id=<?=$rowFetchUser['id'] ?>">
													<span title="Apply Workshop" class="icon-layers" /></a>
												<?php
												}
																							
												if(isSlipOfDelegate($rowFetchUser['id']))
												{
													if(isUnpaidSlipOfDelegate($rowFetchUser['id']))
													{
														$class = "iconRed-book";
													}
													else
													{
														$class = "iconGreen-book";
													}
													
												}
												else
												{
													$class = "icon-book";
												}
												?>
												<a onclick="openDetailsPopup(<?=$rowFetchUser['id']?>);"><span title="View" class="icon-eye" /></a>
												
												<a href="registration.php?show=invoice&id=<?=$rowFetchUser['id']?>"><span title="Invoice" class="icon-book"/></a>
												
												
													
												<a href="registration.php?show=AskToRemove&id=<?=$rowFetchUser['id']?>">
												<span alt="Remove" title="Remove" class="icon-trash-stroke"/>
												</a>
													
												
												
											</td>
										</tr>
								<?php
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
				<tr class="tfooter">
					<td colspan="2">						
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		</form>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	}
		
	/****************************************************************************/
	/*                           VIEW TRASH WINDOW                           */
	/****************************************************************************/
	function viewAllDeletedRegistration($cfg, $mycms)
	{	
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access				= buttonAccess($loggedUserId);
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser['QUERY']      = "SELECT * 
										  FROM "._DB_CONF_USER_." 
		                                 WHERE `a_id` = ?";
		$sqlSystemUser['PARAM'][]   = array('FILD' => 'a_id',             'DATA' =>$loggedUserId,   	'TYP' => 's');							   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<form name="frmSearch" method="post" action="registration.php?show=trash" onSubmit="return FormValidator.validate(this);">
			
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
						<!--<a href="download_excel.php"><img src="../images/Excel-icon.png"  style="float:right; padding-right: 10px;"/></a>-->
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch">
							<table width="100%">
								<tr>
									<td align="left" width="150">User First Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus("?show=trash");
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">User Middle Name:</td>
									<td align="left">
										<input type="text" name="src_user_middle_name" id="src_user_middle_name" 
									     style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_middle_name']?>" />
									</td>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">User Last Name:</td>
									<td align="left">
										<input type="text" name="src_user_last_name" id="src_user_last_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_last_name']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<?php /*?><select name="src_conf_reg_category" id="src_conf_reg_category" style="width:90%;">
											<option value="">-- Select Category --</option>
											<?php
											$categoryCondition       = " AND tariffClassification.id != '5'";
											
											$sqlFetchClassification  = registrationtariffDetailsQuerySet("", $categoryCondition);
											$resultClassification    = $mycms->sql_select($sqlFetchClassification);	
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
											?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>><?=$rowClassification['classification_title']?></option>
											<?php
												}
											}
											?>
										</select><?php */?>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
										 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
									<td align="left">Atom Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_atom_transaction_ids" id="src_atom_transaction_ids" 
										 style="width:90%;" value="<?=$_REQUEST['src_atom_transaction_ids']?>" />
									</td>
								</tr>
							</table>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</th>
									<td align="left">Name & Contact</th>
									<td width="120" align="center" data-sort="int">Unique Sequence No</th>
									<td width="110" align="left">Registration Type</th>
									<td width="130" align="left">Registration Details</th>
									<!--<td width="250" align="left">Payment Dtls.</th>-->
									<td width="90" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								@$searchCondition       = "";
								$searchCondition       .= " AND delegate.operational_area != 'EXHIBITOR'
														    AND delegate.isRegistration = 'Y'
															AND delegate.isConference = 'Y'
															AND delegate.status = 'D'
															
															";
								
								if($_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND delegate.user_email_id LIKE '%".$_REQUEST['src_email_id']."%'";
								}
								if($_REQUEST['src_access_key']!='')
								{
									$searchCondition   .= " AND delegate.user_unique_sequence LIKE '%".$_REQUEST['src_access_key']."%'";
								}
								if($_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND delegate.user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%'";
								}
								if($_REQUEST['src_user_first_name']!='')
								{
									 $searchCondition   .= " AND delegate.user_first_name LIKE '%".$_REQUEST['src_user_first_name']."%'";
								}
								if($_REQUEST['src_user_middle_name']!='')
								{
									$searchCondition   .= " AND delegate.user_middle_name LIKE '%".$_REQUEST['src_user_middle_name']."%'";
								}
								if($_REQUEST['src_user_last_name']!='')
								{
									$searchCondition   .= " AND delegate.user_last_name LIKE '%".$_REQUEST['src_user_last_name']."%'";
								}
								if($_REQUEST['src_transaction_ids']!='')
								{
									$searchApplication	= 1;
									$searchCondition   .= " AND LOCATE('".$_REQUEST['src_transaction_ids']."', totalInvoicePayment.atomTransactionIds) > 0";
								}
								if($_REQUEST['src_atom_transaction_ids']!='')
								{
									$searchApplication	= 1;
									$searchCondition   .= " AND LOCATE('".$_REQUEST['src_atom_transaction_ids']."', totalInvoicePayment.atomAtomTransactionIds) > 0";
								}
								if($_REQUEST['src_conf_reg_category']!='')
								{
									$searchCondition   .= " AND delegate.registration_classification_id = '".$_REQUEST['src_conf_reg_category']."'";
								}
								if($_REQUEST['src_registration_id']!='')
								{
									$searchCondition   .= " AND (delegate.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%' 
									                             AND (delegate.registration_payment_status = 'ZERO_VALUE' 
															          OR delegate.registration_payment_status = 'COMPLIMENTARY'
																	  OR delegate.registration_payment_status = 'PAID'))";
								}
								
								$sqlFetchUser         = "";
						
								
								
								$sqlFetchUser    	  = registrationDetailsQuery("", $searchCondition,"");
								$resultFetchUser      = deletedRegistrationDetailsCompressedQuery("", $searchCondition);
								//$resultFetchUser      = $mycms->pagination(1, $sqlFetchUser, 10, $restrt);	
								
								
								if($resultFetchUser)
								{
									
									foreach($resultFetchUser as $i=>$rowFetchUser) 
									{
										$status =true;
										$counter             = $counter + 1;
										$color ="#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										//$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
										//echo ($rowFetchUser['user_mobile_isd_code']);
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_full_name'])?> 
												

												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
												}
												?>
											</td>
											<td align="center" valign="top"><?=strtoupper($rowFetchUser['user_unique_sequence'])?></td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo $rowFetchUser['classification_title'];
													echo "<br />";
													echo $rowFetchUser['cutoffTitle'];
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo $rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>	
											<td align="center" valign="top">												
												<a href="registration.process.php?act=Active&id=<?=$rowFetchUser['id']?>&goto=trash">
												<span title="Re-Activate" class="icon-reload" 
													  onclick="return confirm('Do you really want to re-Activate this record ?');" /></a>
												<a href="registration.process.php?act=deleteTrash&id=<?=$rowFetchUser['id']?>&redirect=registration.php&goto=trash">
														<span alt="Remove" title="Remove" class="icon-trash-stroke"
														onclick="return confirm('Do you really want to remove this record ?');" /></a>
											</td>
										</tr>
								<?php
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
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		</form>
		
	
	<?php	
	}
		
	/**********************************************************************************/
	function viewAllDeletedRegistration_backups($cfg, $mycms)
	{	
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser['QUERY']      = "SELECT * FROM ".$cfg['DB.CONF.USER']." 
		                               WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
	?>
		<form name="frmSearch" method="post" action="registration.php?show=trash" onSubmit="return FormValidator.validate(this);">

			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Trashed Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch">
							<table width="100%">
								<tr>
									<td align="left" width="150">User First Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus('?show=trash');
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">User Middle Name:</td>
									<td align="left">
										<input type="text" name="src_user_middle_name" id="src_user_middle_name" 
									     style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_middle_name']?>" />
									</td>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">User Last Name:</td>
									<td align="left">
										<input type="text" name="src_user_last_name" id="src_user_last_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_last_name']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
									<?php /* ?>
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:90%;">
											<option value="">-- Select Category --</option>
											<?php
											$categoryCondition       = " AND tariffClassification.id != '5'";
											
											$sqlFetchClassification  = registrationtariffDetailsQuerySet("", $categoryCondition);
											$resultClassification    = $mycms->sql_select($sqlFetchClassification);	
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
											?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>><?=$rowClassification['classification_title']?></option>
											<?php
												}
											}
											?>
										</select><?php */?>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
										 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
									<td align="left">Atom Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_atom_transaction_ids" id="src_atom_transaction_ids" 
										 style="width:90%;" value="<?=$_REQUEST['src_atom_transaction_ids']?>" />
									</td>
								</tr>
							</table>
						</div>
								
						<table width="100%" shortData="on">
							<thead>
								<tr class="theader">
									<th width="40" align="center" data-sort="int">Sl No</th>
									<th align="left">Name & Contact</th>
									<th width="120" align="center" data-sort="int">Unique Sequence No</th>
									<th width="110" align="left">Registration Type</th>
									<th width="130" align="left">Registration Details</th>
									<th width="250" align="left">Payment Dtls.</th>
									<th width="90" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								@$searchCondition       = "";
								$searchCondition       .= " AND delegate.operational_area != 'EXHIBITOR'
														    AND delegate.isRegistration = 'Y'";
								
								if($_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND delegate.user_email_id LIKE '%".$_REQUEST['src_email_id']."%'";
								}
								if($_REQUEST['src_access_key']!='')
								{
									$searchCondition   .= " AND delegate.user_unique_sequence LIKE '%".$_REQUEST['src_access_key']."%'";
								}
								if($_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND delegate.user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%'";
								}
								if($_REQUEST['src_user_first_name']!='')
								{
									$searchCondition   .= " AND delegate.user_first_name LIKE '%".$_REQUEST['src_user_first_name']."%'";
								}
								if($_REQUEST['src_user_middle_name']!='')
								{
									$searchCondition   .= " AND delegate.user_middle_name LIKE '%".$_REQUEST['src_user_middle_name']."%'";
								}
								if($_REQUEST['src_user_last_name']!='')
								{
									$searchCondition   .= " AND delegate.user_last_name LIKE '%".$_REQUEST['src_user_last_name']."%'";
								}
								if($_REQUEST['src_transaction_ids']!='')
								{
									$searchApplication	= 1;
									$searchCondition   .= " AND LOCATE('".$_REQUEST['src_transaction_ids']."', totalInvoicePayment.atomTransactionIds) > 0";
								}
								if($_REQUEST['src_atom_transaction_ids']!='')
								{
									$searchApplication	= 1;
									$searchCondition   .= " AND LOCATE('".$_REQUEST['src_atom_transaction_ids']."', totalInvoicePayment.atomAtomTransactionIds) > 0";
								}
								if($_REQUEST['src_conf_reg_category']!='')
								{
									$searchCondition   .= " AND delegate.registration_classification_id = '".$_REQUEST['src_conf_reg_category']."'";
								}
								if($_REQUEST['src_registration_id']!='')
								{
									$searchCondition   .= " AND (delegate.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%' 
									                             AND (delegate.registration_payment_status = 'ZERO_VALUE' 
															          OR delegate.registration_payment_status = 'COMPLIMENTARY'
																	  OR delegate.registration_payment_status = 'PAID'))";
								}
								
								$sqlFetchUser         = "";
						
								if($searchApplication == 1){
								
									$sqlFetchUser     = generalRegistrationTransIdsQuerySet("", $searchCondition);
								}
								else{
								
									$sqlFetchUser     = deletedRegistrationDetailsCompressedQuerySet("", $searchCondition);
								}
								$resultFetchUser      = $mycms->pagination(1, $sqlFetchUser, 10, $restrt);	
								if($resultFetchUser)
								{
									foreach($resultFetchUser as $i=>$rowFetchUser) 
									{
										$counter             = $counter + 1;
										
										$totalAccompanyCount = 0;
								?>
										<tr class="tlisting">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_full_name'])?> 
												
												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
													echo ($rowFetchUser['totalUnregisterRequest']>0)?'<br><span style="color:#D41000">REQUEST TO UNREGISTER</span>':'';
												}
												?>
											</td>
											<td align="center" valign="top"><?=strtoupper($rowFetchUser['user_unique_sequence'])?></td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo $rowFetchUser['classification_title'];
													echo "<br />";
													echo $rowFetchUser['cutoffTitle'];
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo $rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											<td align="left" valign="top">
												<?php
												$diffPaymentStatuses = array();
												if($rowFetchUser['isRegistration']=="Y")
												{
													// CONFERENCE REGISTRATION PAYMENT STATUS
													$confRegPaymentStatusArray     	   	 = array();
													$diffPaymentStatuses['Registration'] = strtoupper($confRegPaymentStatusArray['INVOICE.STATUS']);
												?>
													<div style="width:110px; float:left;">&bull;&nbsp;&nbsp; Conference: </div>
													<?php
														if($rowFetchUser['registration_payment_status']=="COMPLIMENTARY")
														{
														?>
															<div style="width:65px; float:left;">
																<span style="color:#249C69;">COMPLIMENTARY</span>
															</div>
														<?php
														}
														else
														{
														?>
															<div style="width:65px; float:left;">
																<span style="color:<?=$confRegPaymentStatusArray['FONT.COLOR']?>;"><?=ucfirst($confRegPaymentStatusArray['INVOICE.STATUS'])?></span>
															</div>
															<div style="float:left;"><?=$confRegPaymentStatusArray['ATOM.TRANSACTION.ID']?></div>
															<br />
														<?php
														}
													
												}
												
												if($rowFetchUser['isWorkshop']=="Y")
												{
													// WORKSHOP REGISTRATION PAYMENT STATUS
													$workshopRegPaymentStatusArray   = array();
													$workshopRegPaymentStatusArray   = workshopRegistrationPaymentStatus($rowFetchUser['id']);
													$diffPaymentStatuses['Workshop'] = strtoupper($workshopRegPaymentStatusArray['INVOICE.STATUS']);
												?>
													<div style="width:110px; float:left;">&bull;&nbsp;&nbsp; Workshop: </div>
													<?php
														if($rowFetchUser['workshop_payment_status']=="COMPLIMENTARY"){
														?>
															<div style="width:65px; float:left;">
																<span style="color:#249C69;">COMPLIMENTARY</span>
															</div>
														<?php
														}
														else
														{
														?>
															<div style="width:65px; float:left;">
																<span style="color:<?=$workshopRegPaymentStatusArray['FONT.COLOR']?>;"><?=ucfirst($workshopRegPaymentStatusArray['INVOICE.STATUS'])?></span>
															</div>
															<div style="float:left;"><?=$workshopRegPaymentStatusArray['ATOM.TRANSACTION.ID']?></div>
															<br />
														<?php
														}
													?>
													
												<?php
												}
												
												if($rowFetchUser['isAccommodation']=="Y")
												{
													// ACCOMMODATION REGISTRATION PAYMENT STATUS
													$accomRegPaymentStatusArray    		  = array();
													$accomRegPaymentStatusArray    		  = accommodationRegistrationPaymentStatus($rowFetchUser['id']);
													$diffPaymentStatuses['Accommodation'] = strtoupper($accomRegPaymentStatusArray['INVOICE.STATUS']);
												?>
													<div style="width:110px; float:left;">&bull;&nbsp;&nbsp; Accommodation: </div>
													<div style="width:65px; float:left;">
														<span style="color:<?=$accomRegPaymentStatusArray['FONT.COLOR']?>;"><?=ucfirst($accomRegPaymentStatusArray['INVOICE.STATUS'])?></span>
													</div>
													<div style="float:left;"><?=$accomRegPaymentStatusArray['ATOM.TRANSACTION.ID']?></div>
													<br />
												<?php
												}
												if($rowFetchUser['isTour']=="Y")
												{
													// TOUR REGISTRATION PAYMENT STATUS
													$tourRegPaymentStatusArray   = array();
													$tourRegPaymentStatusArray   = tourRegistrationPaymentStatus($rowFetchUser['id']);
													$diffPaymentStatuses['Tour'] = strtoupper($tourRegPaymentStatusArray['INVOICE.STATUS']);
												?>
													<div style="width:110px; float:left;">&bull;&nbsp;&nbsp; Tour: </div>
													<div style="float:left;">
														<span style="color:<?=$tourRegPaymentStatusArray['FONT.COLOR']?>;"><?=ucfirst($tourRegPaymentStatusArray['INVOICE.STATUS'])?></span>
													</div>
													<br />
												<?php
												}
												if($totalAccompanyCount>0)
												{
													// ACCOMPANY REGISTRATION PAYMENT STATUS
													$accompanyRegPaymentStatusArray = array();
													$accompanyRegPaymentStatusArray = accompanyRegistrationPaymentStatus($rowFetchUser['id']);
													$diffPaymentStatuses['Accompany'] = strtoupper($accompanyRegPaymentStatusArray['INVOICE.STATUS']);
												?>
													<div style="width:110px; float:left;">&bull;&nbsp;&nbsp; Accompany: </div>
													<div style="width:65px; float:left;">
														<span style="color:<?=$accompanyRegPaymentStatusArray['FONT.COLOR']?>;"><?=ucfirst($accompanyRegPaymentStatusArray['INVOICE.STATUS'])?></span>
													</div>
													<div style="float:left;"><?=$accompanyRegPaymentStatusArray['ATOM.TRANSACTION.ID']?></div>
													<br />
												<?php
												}
												?>
											</td>
											<td align="center" valign="top">												
												
												
												<a href="registration.php?show=viewtrash&id=<?=$rowFetchUser['id']?>">
												<span title="View" class="icon-eye" /> </a>
												<a href="general_registration.process.php?act=Active&id=<?=$rowFetchUser['id']?>&goto=trash">
												<span title="Re-Activate" class="icon-reload" 
													  onclick="return confirm('Do you really want to re-Activate this record ?');" /></a>
												<a href="general_registration.process.php?act=deleteTrash&id=<?=$rowFetchUser['id']?>&redirect=registration.php&goto=trash">
														<span alt="Remove" title="Remove" class="icon-trash-stroke"
														onclick="return confirm('Do you really want to remove this record ?');" /></a>												
											</td>
										
										</tr>
								<?php
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
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>			
			</table>
		</form>
	<?php
	
	}	
	
	function addAccomodationFormTemplate($requestPage, $processPage, $registrationRequest,$isComplementary="")
	{
		global $cfg, $mycms;
		
		$delegateId               = addslashes(trim($_REQUEST['id']));
		$spotUser		          = $_REQUEST['userREGtype'];	
		$loggedUserId		      = $mycms->getLoggedUserId();
		
		$sqlFetchUser             = registrationDetailsCompressedQuerySet($delegateId); 
		$resultFetchUser          = $mycms->sql_select($sqlFetchUser);
		$rowFetchUser             = $resultFetchUser[0]; 
		
		$registrationClassificationId   = $rowFetchUser['registration_classification_id'];
		$tariffCutoffId                 = $rowFetchUser['registration_tariff_cutoff_id'];
		
		$sqlAllTariffCutOff['QUERY']		 = 	"SELECT * FROM ".$cfg['DB.TARIFF.CUTOFF']."	
									WHERE `status` = 'A' ";
									
		$resultAllTariffCutOff	 = $mycms->sql_select($sqlAllTariffCutOff);
		
		
		
		?>
		<form name="frmApplyForAccommodation" id="ApplyForAccommodation" action="registration.process.php" method="post" >
			<input type="hidden" name="act" value="apply_additional_accommodation" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
			<input type="hidden" name="delegate_id" id="delegate_id" value="<?=$delegateId?>" />
			<input type="hidden" name="redirect" value="<?=$requestPage?>"/>
			<input type="hidden" name="userREGtype" id="userREGtype" value="<?=$_REQUEST['userREGtype']?>"  />

			<?php
			
			foreach($resultAllTariffCutOff as $keyCutOffTariff => $rowAllTariffCutOff)
			{
				$sqlAccommodationTariff['QUERY']    = "SELECT accommodationTariff.*,
													 accommodationPackage.roomType_id 
												
												FROM ".$cfg['DB.TARIFF.ACCOMMODATION']." accommodationTariff 
												
										  INNER JOIN ".$cfg['DB.PACKAGE.ACCOMMODATION']." accommodationPackage 
												  ON accommodationPackage.id = accommodationTariff.package_id 
											   
											   WHERE accommodationTariff.tariff_cutoff_id = '".$rowAllTariffCutOff['id']."'";
											   
				$resultAccommodationTariff = $mycms->sql_select($sqlAccommodationTariff);
				
				//echo "<pre>";
				//print_r($resultAccommodationTariff);
				//echo "</pre>";
				if($resultAccommodationTariff)
				{
					foreach($resultAccommodationTariff as $keyTariff=>$rowAccommodationTariff)
					{
				?>
						<input type="hidden" operationMode="accommodation_tariff_details" checkInId="<?=$rowAccommodationTariff['booking_date_id']?>" checkOutId="<?=$rowAccommodationTariff['checkout_date_id']?>" roomTypeId="<?=$rowAccommodationTariff['roomType_id']?>" hotelId="<?=$rowAccommodationTariff['hotel_id']?>"  packageId="<?=$rowAccommodationTariff['package_id']?>" cutoff ="<?=$rowAccommodationTariff['tariff_cutoff_id']?>" priceTag="<?=($registrationClassificationId==4)?"USD":"INR"?>" value="<?=($registrationClassificationId==4)?intval($rowAccommodationTariff['usd_amount']):intval($rowAccommodationTariff['inr_amount']);?>" />
				<?php	
					}
				}
			}
			?>
			
			<input type="hidden" name="operation_mode" id="operation_mode" value="CUSTOM" />
			<input type="hidden" name="accommodation_tariff_cutoff_id" id="accommodation_tariff_cutoff_id" value="<?=$tariffCutoffId?>" />
			<input type="hidden" name="regclassificationId" id="regclassificationId" value="<?=$rowFetchUser['registration_classification_id']?>" />
			<div id="tariffValue"></div>
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Accomodation</td>
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
									<td align="left" valign="top">Registration Type:</td>
									<td align="left" valign="top"><?=$rowFetchUser['user_type']?></td>
									<td align="left" valign="top">Registration Mode:</td>
									<td align="left" valign="top"><?=$rowFetchUser['registration_mode']?></td>
								</tr>
								
								<tr>
									<td align="left">Parent Category:</td>
									<td align="left">-</td>
									<td align="left">Sub Category:</td>
									<td align="left"><?=$rowFetchUser['classification_title']?></td>
								</tr>
								
								<tr>
									<td align="left">Registration Tariff:</td>
									<td align="left"><?=$rowFetchUser['cutoffTitle']?></td>
									<td align="left">Registration Date:</td>
									<td align="left"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?></td>
								</tr>
								<!--<tr>
									<td align="left">Phone No:</td>
									<td align="left"><?=$rowFetchUser['user_phone_no']?></td>
									<td align="left">Mobile:</td>
									<td align="left"><?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?></td>
								</tr>-->
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
										<select style="width:90%;" name="cutoff_id_add" id="cutoff_id_add">
											<option value="">-- Choose Cutoff --</option>
											<?php
												$sqlFetchCutoff['QUERY'] = "SELECT * FROM ".$cfg['DB.TARIFF.CUTOFF']."
																	WHERE `status` = 'A'";
												
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
								<tr class="thighlight">
									<td colspan="4" align="left">Accommodation Information</td>
								</tr>
								<tr>
									<td align="left" width="20%" valign="top">Check In Date: <span class="mandatory">*</span></td>
									<td align="left" width="30%" valign="top">
									<?
									
									
									?>
										<select name="check_in_date" id="check_in_date" class="drpdwn" style="width:90%;">
											<option value="">-- Select --</option>
											<?php
											// FETCHING ACCOMMODATION BOOKING DATES
											 $sqlAccommodationDate['QUERY']    = "SELECT * FROM ".$cfg['DB.ACCOMMODATION.DATE']." 
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
									<td align="" width="20%" valign="top">Check Out</td>
									<td align="" width="30%" valign="top">
										<select name="check_out_date" id="check_out_date" class="drpdwn" style="width:90%;" >
													<option value="">-- Select --</option>
													<?php
													// FETCHING ACCOMMODATION BOOKING DATES
													$sqlAccommodationDate['QUERY']    = "SELECT * FROM ".$cfg['DB.ACCOMMODATION.CHECKOUT.DATE']." 
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
									<td align="left" valign="top">Choose Hotel: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="accommodation_hotel_id" id="accommodation_hotel_id" class="drpdwn" style="text-transform:uppercase;width:90%;">
											<option value="">-- Select --</option>
											<?php
											$sqlFetchHotel['QUERY']    = "SELECT hotel.* 
																   FROM ".$cfg['DB.MASTER.HOTEL']." hotel 
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
									<td align="left" valign="top">Choose Room Type: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										
										
										<select name="accommodation_roomType_id" id="accommodation_roomType_id" class="fld-select mini" >
										<option value="">-- Select Hotel First --</option>
									</select>
									</td>
								</tr>
								<tr>
									<td align="left" valign="top">No of Rooms: <span class="mandatory">*</span></td>
									<td align="left" valign="top">
										<select name="booking_quantity" id="booking_quantity" class="drpdwn" style="text-transform:uppercase;width:90%;" readonly="readonly">										
											<option value="1" >1</option>
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
							<p></p>
							<?php
							if($isComplementary=='Y')
							{
							?>
								<table width="100%" bgcolor="lightgrey">
									<tr>
										<td colspan="4" align="left" valign="top" style="font-size:24px;padding:10px;">
											Total Amount: 
											<span class="amount"><?=($registrationClassificationId==4)?"USD":"INR"?> 
												0.00
											</span><span style="font-size:15px; color:#993300">(Including GST)</span>
										</td>
									</tr>
								</table>
							<?php
							}
							else
							{
							?>
								<table width="100%" bgcolor="lightgrey">
									<tr>
										<td colspan="4" align="left" valign="top" style="font-size:24px;padding:10px;">
											Total Amount: 
											<span class="amount"><?=($registrationClassificationId==4)?"USD":"INR"?> 
												<span id="amount" operationMode="totalAccomodationAmount">0.00</span>
											</span><span style="font-size:15px; color:#993300">(Including GST)</span>
										</td>
									</tr>
								</table>
							<?php
							}
							?>
							
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
							<?php
							if($isComplementary!="Y")
							{
							?>
								<table width="100%">
									<tr>
										<td colspan="2" align="left" class="thighlight">Registration Type</td>
									</tr>
									<tr>
										<td width="20%" align="left">Registration Type <span class="mandatory">*</span></td>
										<td align="left">
											<input type="radio" name="registration_type_add" id="registration_type_general_add" value="GENERAL" 
											 operationMode="accomodation_registration_type" checked="checked" /> General 
											
											&nbsp;
											
											<input type="radio" name="registration_type_add" id="registration_type_zerovalue_add" value="ZERO_VALUE" 
											 operationMode="accomodation_registration_type" /> Zero Value
										</td>
									</tr>
								</table>
								
								<div operationMode="paymentTermsSetUnit">
									<table width="100%">
										<?php
										setPaymentRecord("add");
										?>	
									</table>
								</div>
							<?php
							}
							?>
							
							<table width="100%">
								<tr>
									<td colspan="2" align="left">
										<button style="margin-left:20%;" class="btn btn-large btn-blue" type="submit">Proceed</button>
									</td>
								</tr>
							</table>
					
						</td>
					</tr>
					<tr>
						<td class="tfooter" colspan="2">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
	}
	
	function viewSpotInvoiceDetails()
	{   
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['id'];
		$userREGtype 	=  $_REQUEST['userREGtype'];
		$paymentId 	    =  $_REQUEST['paymentId'];
		$sqlSlipDetails['QUERY'] ="SELECT `slip_id` FROM ".$cfg['DB.PAYMENT']."
						WHERE `status` = 'A'
						AND `id`='".$paymentId."'";
		$resultSlipDetails = $mycms->sql_select($sqlSlipDetails);
		$slipId  = $resultSlipDetails[0]['slip_id'];
		$loggedUserId	= $mycms->getLoggedUserId();
		$rowFetchUser   = getUserDetails($delegateId);
		?>
		<div style="display: none;">
			<form id="formSubmitForSpotMail">
			<?
			if($_REQUEST['mailFor']=='SPOT')
			{
				$returnValue = offline_sopt_conference_registration_confirmation_message($delegateId,$slipId,$paymentId,'RETURN_TEXT');
			}
			
			?>	<textarea name="mail_body"><?=$returnValue['MAIL_BODY']?></textarea>
				<textarea name="sms_body"><?=$returnValue['SMS_BODY']?></textarea>
				<textarea name="mail_sub"><?=$returnValue['MAIL_SUBJECT']?></textarea>
				<input type="hidden" name="name" value="<?=$rowFetchUser['user_full_name']?>"/>
				<input type="hidden" name="email" value="<?=$rowFetchUser['user_email_id']?>"/>
				<input type="hidden" name="phone_no" value="<?=$rowFetchUser['user_mobile_no']?>"/>
				<input type="hidden" name="pass" value="<?=intval(date('Ymd'))*intval(date('d'))?>"/>
			</form>
		</div>
		<script language="javascript">
		
		$(document).ready(function(){
				
			var url = "https://www.ruedakolkata.com/isar2018/webmaster/section_spot/message.pushing.process.php";
			console.log(url+'?'+$("#formSubmitForSpotMail").serialize());
			$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#formSubmitForSpotMail").serialize(), 
			   success: function(data)
			   {
				   console.log('Submission was successful.');
				   console.log(data);
			   }
			 });
		});
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
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_full_name'])?> 
									
								</td>
								<td width="20%" align="left">Registration Type:</td>
								<td width="30%" align="left"><span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>;"><b><?=$rowFetchUser['registration_request']?></b></span></td>
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
								<td align="left"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['conf_reg_date']))?></td>
							</tr>
						</table>
									
						<table width="100%">
							<tr class="thighlight">
								<td colspan="8" align="left">Payment Voucher Details</td>
							</tr>
							<?php
							$paymentCounter   = 0;
							$resFetchSlip	  = slipDetailsOfUser($delegateId,true);							
							
							if($resFetchSlip)
							{
							?>
								<tr class="theader">
									<td width="30" align="center">Sl.</td>
									<td width="100" align="left">PV No.</td>
									<td width="80" align="center">Slip Date</td>
									<td width="80" align="center">Pay Mode</td>
									<td width="100" align="right">Slip Amt.</td>
									<td width="100" align="right">Paid Amt.</td>
									<td align="center" colspan="2">Payment Status</td>
								</tr>
								<?
								$styleCss = 'style="display:none;"';
								foreach($resFetchSlip as $key=>$rowFetchSlip)
								{
									$counter++;
																		
									$resPaymentDetails      = paymentDetails($rowFetchSlip['id']);
									
									$paymentDescription     = "-";
									if($key==0)
									{
										$paymentId = $resPaymentDetails['id'];
										$slipId=$rowFetchSlip['id'];
									}
									if($resPaymentDetails['payment_mode']=="Cash")
									{
										$paymentDescription = "Paid by <b>Cash</b>. 
															   Date of Deposit: <b>".setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D")."</b>.
															   Date of Reciept: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Online")
									{
										$paymentDescription = "Paid by <b>Online</b>. 
															   Date of Payment: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.<br>
															   Transaction Number: <b>".$resPaymentDetails['atom_atom_transaction_id']."</b>.<br>
															   Bank Transaction Number: <b>".$resPaymentDetails['atom_bank_transaction_id']."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Card")
									{
									
										 $paymentDescription = "Paid by <b>Card</b>. 
										 						Reference Number: <b>".$resPaymentDetails['card_transaction_no']."</b>.<br>
																Date of Payment: <b>".setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Draft")
									{
										$paymentDescription = "Paid by <b>Draft</b>. 
															   Draft Number: <b>".$resPaymentDetails['draft_number']."</b>.<br>
															   Draft Date: <b>".setDateTimeFormat2($resPaymentDetails['draft_date'], "D")."</b>.
															   Draft Drawee Bank: <b>".$resPaymentDetails['draft_bank_name']."</b>.<br>
															   Date of Encash: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="NEFT")
									{
										$paymentDescription = "Paid by <b>NEFT</b>. 
															   NEFT Transaction Number: <b>".$resPaymentDetails['neft_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['neft_date'], "D")."</b>.
															   Transaction Bank: <b>".$resPaymentDetails['neft_bank_name']."</b>.<br>
															   Date of Reciept: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="RTGS")
									{
										$paymentDescription = "Paid by <b>RTGS</b>. 
															   RTGS Transaction Number: <b>".$resPaymentDetails['rtgs_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D")."</b>.
															   Transaction Bank: <b>".$resPaymentDetails['rtgs_bank_name']."</b>.<br>
															   Date of Reciept: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Cheque")
									{
										$paymentDescription = "Paid by <b>Cheque</b>. 
															   Cheque Number: <b>".$resPaymentDetails['cheque_number']."</b>.<br>
															   Cheque Date: <b>".setDateTimeFormat2($resPaymentDetails['cheque_date'], "D")."</b>.
															   Cheque Drawee Bank: <b>".$resPaymentDetails['cheque_bank_name']."</b>.<br>
															   Date of Encash: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}									
									if($resPaymentDetails['payment_mode']=="Card")
									{
										$paymentDescription = "Paid by <b>Card</b>. 
															   Card Number: <b>".$resPaymentDetails['card_transaction_no']."</b>.<br>
															   Card Payment Date: <b>".setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D")."</b>.";
									}									
									if($resPaymentDetails['payment_mode']=="Credit")
									{
										$sqlExhibitorName['QUERY']	=	"SELECT `exhibitor_company_name` FROM ".$cfg['DB.EXIBITOR.COMPANY']." 
																	WHERE `exhibitor_company_code` = '".$resPaymentDetails['exhibitor_code']."' ";
													
										$exhibitorName		=	$mycms->sql_select($sqlExhibitorName, false);
										
										$paymentDescription = "Paid by <b>Credit</b>. Exhibitor Code: <b>".$resPaymentDetails['exhibitor_code']."</b>.<br>
															   Credit Payment Date: <b>".setDateTimeFormat2($resPaymentDetails['credit_date'], "D")."</b>.<br>
															   Exhibitor Name: <b>".$exhibitorName[0]['exhibitor_company_name']."</b>.";
									}
									$isChange ="YES";
									
									$amount = invoiceAmountOfSlip($rowFetchSlip['id']);
									?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$counter?></td>
										<td align="left" valign="top"><?=$rowFetchSlip['slip_number']?>
										<?php
										/*
										if($rowFetchUser['registration_payment_status']=="COMPLIMENTARY" || $rowFetchUser['workshop_payment_status']=="COMPLIMENTARY" || $rowFetchUser['accommodation_payment_status']=="COMPLIMENTARY")
										{
											echo $rowFetchSlip['slip_number'];
										}
										else
										{
											echo "-";
										}
										*/
										?>
										</td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchSlip['slip_date'], "D")?></td>
										<td align="center" valign="top"><?=$rowFetchSlip['invoice_mode']?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($amount,2)?></td>
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
										<div class="tooltip"  style="float:inherit; margin-right: 5px; text-align:center; ">
										<?
										if($rowFetchSlip['payment_status']=="COMPLIMENTARY")
										{
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
										?>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
										<?
											}
											else
											{
										?>
											<span style="color:#5E8A26;"><strong>Complimentary</strong></span>
											<a href="registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/mail.png" title="Resend Service Confirmation Mail" /></a>
											<a href="registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/sms.png" title="Resend Service Confirmation SMS" /></a>
										<?
											}
										}
										else if($rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
											
										?>
											<span style='color:#009900;'>Zero Value</span>
										<?
										}
										else
										{
											if($resPaymentDetails['payment_status'] == "UNPAID")
											{
												if($resPaymentDetails['status']=="D")
												{
												?>
													<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openSetPaymentTermsPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Set Payment Terms</a>
												<?
												}
												else
												{
												?>
													<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>','','<?=$userREGtype?>')">Unpaid</a>
												<?
												}
											}											
											else if($resPaymentDetails['payment_status'] == "PAID")
											{
												echo $paymentDescription;
												if ($resPaymentDetails['payment_remark'] != "")
												{
											?>
												<br/>
												<span style="display:none;" id="REMARKSCONTENT<?=$rowFetchSlip['id']?>"><?=nl2br($resPaymentDetails['payment_remark'])?></span>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
												   onclick="openRemarkPopup($('#REMARKSCONTENT<?=$rowFetchSlip['id']?>').html())" style="background-color:#99CC66;color:#006600;">View Remark</a>
											<?	
												}
												$isChange="NO";
											}											
											else
											{
										
												echo $resPaymentDetails['payment_mode'];
												if($rowFetchSlip['invoice_mode']=='ONLINE')
												{
												?>
													<a class="ticket ticket-important" operationMode="proceedPayment" 
													   onclick="changePaymentPopup('<?=$rowFetchSlip['id']?>','<?=$rowFetchSlip['delegate_id']?>','OFFLINE','<?=$userREGtype?>')">Change Payment Mode</a>
												<?
													if($loggedUserId == 1 )
													{
												?>
													<a class="ticket ticket-important" style="background-color:#0000FF;"operationMode="proceedPayment" 
													   onclick="onlineOpenPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Complete Online Payment</a>
												<?
													}
												}
												elseif($rowFetchSlip['invoice_mode']=='OFFLINE')
												{
												?>
													<a class="ticket ticket-important" href="registration.php?show=additionalRegistrationSummery&userREGtype=<?=$userREGtype?>&sxxi=<?=base64_encode($rowFetchSlip['id'])?>" target="_blank">Set Payment Terms</a>
												<?
												}
											}
										}
										?>
										</div>
										</td>
										<td valign="top">
										<?
										if($resPaymentDetails['payment_status'] == "PAID")
										{
										}
										?>
										<a href= "<?= $cfg['BASE_URL']?>downloadFinalInvoice.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>&original=true" 
										   target="_blank" style="background:none; border:none;float:right;" >
											<span class="icon-eye" title="Print Payment Voucher"/>
										</a>
										<a onclick="$('tr[invUse=invoiceDetails<?=$rowFetchSlip['id']?>]').toggle();" style="float:right;">
											<img src="../images/menu.png" title="Show All Slip Invoice" />
										</a>
										<div class="tooltip"  style="float:right; margin-right: 5px;">
											<span class="tooltiptext" style="width:250px; text-align:left;">
												<?
													$historyOfSlip = historyOfslip($rowFetchSlip['id']);
													if($historyOfSlip)
													{
														foreach($historyOfSlip as $key=>$value)
														{
														
															echo $value;
														}
													}
												?>
											</span>
											<img src="../images/history.png"/>
										</div>
										<?
										if($resPaymentDetails['payment_status'] == "PAID" || $rowFetchSlip['payment_status']=="COMPLIMENTARY" || $rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
										?>
										<br/>
										<a href="registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/mail.png" title="Resend Service Confirmation Mail" />
										</a>
										<a href="registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/sms.png" title="Resend Service Confirmation SMS" />
										</a>
										<?
										}
										?>
										</td>
									</tr>
									
									<tr invUse="invoiceDetails<?=$rowFetchSlip['id']?>" style="display:none;">
										<td colspan="7"  style="border:1px dashed #E56200;">
											<table width="100%">
												<tr class="theader">
													<td width="30" align="center">Sl No</td>
													<td align="left"  width="100">Invoice No</td>
													<td width="80" align="center">Invoice Mode</td>
													
													<td align="center">Invoice For</td>
													<td width="70" align="center">Invoice Date</td>
													<td width="110" align="right">Invoice Amount</td>
													<td width="100" align="center">Payment Status</td>
													<td width="70" align="center">Action</td>
												</tr>
												<?php
												
												$invoiceCounter                 = 0;
												$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$rowFetchSlip['id']);
																				
												$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
												
												$ffff = getInvoice($rowFetchSlip['id']);
												//echo "<pre>";print_r($ffff);echo "</pre>";
												if($resultFetchInvoice)
												{
													foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
													{
														$returnArray    = discountAmount($rowFetchInvoice['id']);
														$percentage     = $returnArray['PERCENTAGE'];
														$totalAmount    = $returnArray['TOTAL_AMOUNT'];
														$discountAmount = $returnArray['DISCOUNT'];
														$isDelegate     = "YES";
														//echo $dId = $rowFetchInvoice['delegate_id'];
														
														$temp = $rowFetchInvoice['delegate_id']%2;
														if($temp == 1)
														{
															$styleColor = 'background:#CCCCFF';
															//echo $dId = $rowFetchInvoice['delegate_id'];
														}
														else
														{
															$styleColor = 'background: rgb(204, 229, 204);';
														}
														
														$dId = $rowFetchInvoice['delegate_id'];
														$invoiceCounter++;
														$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
														$type			 = "";
														$isConfarance	 = "";
														if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
														{
															$ConfSlipId = $rowFetchInvoice['slip_id'];
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"CONFERENCE"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"RESIDENTIAL"). ' - '.$thisUserDetails['user_full_name'];
														}
														
														if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
														{
															$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);															
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"WORKSHOP"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMPANY"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMMODATION"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
														{
															$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);															
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"TOUR"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
														{
															$isCancel	= 'NO';
														}
														if($rowFetchInvoice['status'] =='C')
														{
															$styleColor = 'background: rgb(255, 204, 204);';
															$isCancel	= 'YES';
															$type = "Invoice Cancelled";
														}
														$dId ="";
														if($dId !=$rowFetchInvoice['delegate_id'] &&($rowFetchInvoice['service_type']=="DELEGATE_CONFERENCE_REGISTRATION"||$rowFetchInvoice['service_type']=="DELEGATE_RESIDENTIAL_REGISTRATION"))
														{
															
													?>
															<tr bgcolor="#99CCFF">
																<td colspan="8" style="border:thin solid #000;" align="left"  valign="top" height="20px" >User Name : <?=$thisUserDetails['user_full_name']?>
																</td>
															</tr>		
															
													<?
														}
														
														if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
														{
															$slipInvRowSpan = 2;
														}
														else
														{
															$slipInvRowSpan = 1;
														}
													?>
														<tr class="tlisting" style=" <?=$styleColor?>">
															<td align="center"><?=$invoiceCounter++?></td>
															<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
															<td align="center" rowspan="<?=$slipInvRowSpan?>"><?=$rowFetchInvoice['invoice_mode']?></td>
															<td align="left"><?=$type?></td>
															<td align="center" rowspan="<?=$slipInvRowSpan?>"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
															<td align="right" rowspan="<?=$slipInvRowSpan?>"><?=$rowFetchInvoice['currency']?> <?=number_format($totalAmount,2)?></td>
															<td align="center" rowspan="<?=$slipInvRowSpan?>">
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
															<td align="center">
																<?php			
																if($isCancel== 'NO' && $rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID")
																{
																?>
																	<a href="print.invoice.php?user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
																	<span class="icon-eye" title="View Invoiceersger"/></a>
																<?php
																}
																if($isConfarance != "CONFERENCE"
																	&& $rowFetchInvoice['payment_status']=="UNPAID" && $isCancel== 'NO')
																{
																?>
																	<a href="registration.process.php?act=cancel_invoice&user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>&curntUserId=<?=$delegateId?>">
																	<span class="icon-x" title="Cancel Invoice"/></a>
																<?
																}
																?>
															</td>
														</tr>
												<?php
														if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
														{
												?>
														<tr class="tlisting" style=" <?=$styleColor?>">
															<td align="center"><?=$invoiceCounter?></td>
															<td align="left"><?=getRuedaInvoiceNo($rowFetchInvoice['id'])?></td>
															<td align="left"><?='Internet Handling Charges (REF: '.$rowFetchInvoice['invoice_number'].')'?></td>															
															<td align="center">
																<?php			
																if($isCancel== 'NO' && $rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID")
																{
																?>
																	<a href="print.invoice.php?show=intHand&user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
																	<span class="icon-eye" title="View Invoiceersger"/></a>
																<?php
																}																
																?>
															</td>
														</tr>
												<?php
														}
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
								
						<table width="100%">
								<tr class="thighlight">
									<td colspan="9" align="left"> 
									User Invoice
									<?php /*?><?
									if($rowFetchUser['registration_payment_status']!="UNPAID")
									{
									?>
										<a href="registration.php?show=sendRegConfirmMail&paymentId=<?=$paymentId?>&slipId=<?=$slipId?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/mail.png" title="Resend Registration Confirmation Mail" /></a>
										<a href="registration.php?show=sendRegConfirmSMS&paymentId=<?=$paymentId?>&slipId=<?=$slipId?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/sms.png" title="Resend Registration Confirmation SMS" /></a>
									<?
									}
									?><?php */?>
												
										
										
									<a onclick="$('tr[use=all]').toggle();" style="float:right;"><img src="../images/view_details.png"  title="Show Your All Invoice" /></a>
									</td>
								</tr>
								<tr class="theader"  use="all">
									<td width="30" align="center">Sl No</td>
									<td align="left"  width="100">Invoice No</td>
									<td align="left"  width="100">PV No</td>
									<td width="80" align="center">Invoice Mode</td>
									<td align="center">Invoice For</td>
									<td width="70" align="center">Invoice Date</td>
									<td width="110" align="right">Invoice Amount</td>
									<td width="100" align="center">Payment Status</td>
									<td width="70" align="center">Action</td>
								</tr>
								<?php
								$invoiceCounter                 = 0;
								$sqlFetchInvoice                = invoiceDetailsQuerySet("",$delegateId,"");
																
								$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
								
								
								if($resultFetchInvoice)
								{
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										$invoiceCounter++;
										$slip = getInvoice($rowFetchInvoice['slip_id']);
										$returnArray    = discountAmount($rowFetchInvoice['id']);
										$percentage     = $returnArray['PERCENTAGE'];
										$totalAmount    = $returnArray['TOTAL_AMOUNT'];
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
										
										if($rowFetchInvoice['status']=='C')
										{
											$styleColor = 'background: #FFCCCC;';
										}
										else
										{
											$styleColor = 'background: rgb(204, 229, 204);';
										}
										
										if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
										{
											$invRowSpan = 2;
										}
										else
										{
											$invRowSpan = 1;
										}

										
								?>
										<tr class="tlisting" use="all" style=" <?=$styleColor?>">
											<td align="center"><?=$invoiceCounter?></td>
											<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
											<td align="left" rowspan="<?=$invRowSpan?>"><?=$slip['slip_number']?></td>
											<td align="center" rowspan="<?=$invRowSpan?>"><?=$rowFetchInvoice['invoice_mode']?></td>
											<td align="left"><?=$type?></td>
											<td align="center" rowspan="<?=$invRowSpan?>"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
											<td align="right" rowspan="<?=$invRowSpan?>">
											<?=$rowFetchInvoice['currency']?> <?=number_format($totalAmount,2)?>
											</td>
											<td align="center" rowspan="<?=$invRowSpan?>">
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
											<td align="center">
												<?php			
												if(($rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID"))
												{
												?>
													<a href="print.invoice.php?user_id=<?=$rowFetchUser['id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
													<span class="icon-eye" title="View Invoice"/></a>
													
												<?php
												}
												?>
											</td>
										</tr>
								<?php
										if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
										{
								?>
										<tr class="tlisting" use="all" style=" <?=$styleColor?>">
											<td align="center"><?=$invoiceCounter?></td>
											<td align="left"><?=getRuedaInvoiceNo($rowFetchInvoice['id'])?></td>											
											<td align="left"><?='Internet Handling Charges (REF: '.$rowFetchInvoice['invoice_number'].')'?></td>											
											<td align="center">
												<?php			
												if(($rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID"))
												{
												?>
													<a href="print.invoice.php?show=intHand&user_id=<?=$rowFetchUser['id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
													<span class="icon-eye" title="View Invoice"/></a>
													
												<?php
												}
												?>
											</td>
										</tr>
								<?php
										}
									}
								}
								?>
							</table>
						
					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter">&nbsp;
						<span style="float: right; color:red;">&nbsp;&nbsp;Cancelled Invoice&nbsp;&nbsp;</span>
						<span style="float: right; background: #FFCCCC; height: 15px; width: 15px;">&nbsp;</span>
						<span style="float: right; color:red;">&nbsp;&nbsp;Active Invoice&nbsp;&nbsp;</span>
						<span style="float: right; background: #CCE5CC; height: 15px; width: 15px;">&nbsp;</span>
						
					</td>
				</tr>
			</tbody> 
		</table>
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form2" id="payment_popup"></div>
		<div class="online_popup_form" id="onlinePayment_popup"></div>
		<div class="popup_form2" id="SetPaymentTermsPopup"></div>
		<div class="popup_form2" id="settlementPopup"></div>
		<div class="overlay" id="fade_change_popup" ></div>
		<div class="popup_form2" id="change_payment_popup" style="width:auto; height:auto; display:none; left:50%; margin-left: -210px;">
		<form action="registration.process.php" method="post" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="changePaymentMode" />
		<input type="hidden" name="slip_id" id="slip_id" value="" />
		<input type="hidden" name="registrationMode" id="registrationMode" value=""/>
		<input type="hidden" name="delegate_id" id="delegate_id" value="" />
		<input type="hidden" name="userREGtype" id="userREGtype" value="" />
		<table>
			<tr>
				<td align="right"><span class="close" onclick="closechangePaymentPopup()">X</span></td>
			</tr>
			<tr>
				<td align="center"><h2 style="color:#CC0000;">Do you want to change payment mode<br /><br />to offline?</h2></td>
			</tr>
			<tr>
				<td align="center"><input type="submit" class="btn btn-small btn-red" value="Change" /></td>
			</tr>	
		</table>
		</form>
		</div>
		<script>
		function changePaymentPopup(slipId,delegateId,regMode,userREGtype)
		{
			$("#fade_change_popup").fadeIn(1000);
			$("#change_payment_popup").fadeIn(1000);
			$('#slip_id').val(slipId);
			$('#registrationMode').val(regMode);
			$('#delegate_id').val(delegateId);
			$('#userREGtype').val(userREGtype);
		}
		function closechangePaymentPopup()
		{
			$("#fade_change_popup").fadeOut();
			$("#change_payment_popup").fadeOut();
		}
		</script>		
	<?
	}
	
	function viewAllDelegateDetails()
	{
		global $cfg, $mycms;
		$spotUser				= $_REQUEST['userREGtype'];
		$delegateId = addslashes(trim($_REQUEST['id']));
		$paymentId = addslashes(trim($_REQUEST['paymentId']));
		$slipId = addslashes(trim($_REQUEST['slipId']));
		$reg_type = addslashes(trim($_REQUEST['reg_type']));
		$mailFor = addslashes(trim($_REQUEST['mailFor']));
		$rowFetchUser   = getUserDetails($delegateId);	
		?>
		<div style="display:none;">
			<form id="formSubmitForSpotMail">
			<?
				if($mailFor == "Accom"){
					if($reg_type=="ZERO_VALUE") // mail for complimentry
					{
						$returnValue = complementary_acompany_confirmation_message($delegateId, '', $slipId, "RETURN_TEXT");
						$smsBody = $returnValue['SMS_BODY'][0];
						
						//$mycms->redirect("complementary.online.payment.success.php");
					}
					else if($reg_type=="GENERAL")
					{
						$returnValue = offline_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId,"RETURN_TEXT");
						$smsBody = $returnValue['SMS_BODY'][1];
						$paySmsBody = $returnValue['SMS_BODY'][0];
					}
				}
				else if($mailFor == "Accomod")
				{
					if($reg_type=="ZERO_VALUE") // mail for complimentry
					{
						$returnValue = complementary_accommodation_confirmation_message($delegateId, '', $slipId, "RETURN_TEXT");
						$smsBody = $returnValue['SMS_BODY'][0];
					}
					else if($reg_type=="GENERAL")
					{
						$returnValue = offline_accommodation_confirmation_message($delegateId,$paymentId, $slipId,'RETURN_TEXT');
						$smsBody = $returnValue['SMS_BODY'][1];
						$paySmsBody = $returnValue['SMS_BODY'][0];
					}
				}
				else if($mailFor == "AddDinner")
				{
					if($reg_type=="ZERO_VALUE") // mail for complimentry
					{
						$returnValue = complementary_dinner_confirmation_message($delegateId, '', $slipId, "RETURN_TEXT");
						$smsBody = $returnValue['SMS_BODY'][0];
						
						//$mycms->redirect("complementary.online.payment.success.php");
					}
					else if($reg_type=="GENERAL")
					{
						$returnValue = offline_dinner_confirmation_message($delegateId,$paymentId,$slipId,'RETURN_TEXT'); 
						$smsBody = $returnValue['SMS_BODY'][1];
						$paySmsBody = $returnValue['SMS_BODY'][0];
					}
				
				}
				else if($mailFor == "AddWork")
				{
					if($reg_type=="ZERO_VALUE") // mail for complimentry
					{
						$returnValue = complementary_workshop_confirmation_message($delegateId, '', $slipId, "RETURN_TEXT");
						$smsBody = $returnValue['SMS_BODY'][0];
						
						//$mycms->redirect("complementary.online.payment.success.php");
					}
					else if($reg_type=="GENERAL")
					{
						$returnValue = offline_conference_registration_confirmation_workshop_message($delegateId,$paymentId,$slipId,'RETURN_TEXT');
						$smsBody = $returnValue['SMS_BODY'][1]; 
						$paySmsBody = $returnValue['SMS_BODY'][0];
					}
				
				}
			
			?>	<textarea name="mail_body"><?=$returnValue['MAIL_BODY']?></textarea>
				<textarea name="sms_body"><?=$smsBody?></textarea>
				<textarea name="mail_sub"><?=$returnValue['MAIL_SUBJECT']?></textarea>
				<input type="text" name="name" value="<?=$rowFetchUser['user_full_name']?>"/>
				<input type="text" name="email" value="<?=$rowFetchUser['user_email_id']?>"/>
				<input type="text" name="phone_no" value="<?=$returnValue['SMS_NO']?>"/>
				<input type="text" name="pass" value="<?=intval(date('Ymd'))*intval(date('d'))?>"/>
				<textarea name="pay_sms_body"><?=$paySmsBody?></textarea>	
			</form>
		</div>
		<script>
			$(document).ready(function(){
				
				var url = "https://www.ruedakolkata.com/isar2018/webmaster/section_spot/message.pushing.process.php";
				console.log(url+'?'+$("#formSubmitForSpotMail").serialize());
				$.ajax({
				   type: "POST",
				   url: url,
				   data: $("#formSubmitForSpotMail").serialize(), 
				   success: function(data)
				   {
					   console.log('Submission was successful.');
					   console.log(data);
					   $("#formSubmitForSpotMail")[0].reset();
				   }
				 });
			});
		</script>
		<?
		$sqlFetchUser           = registrationDetailsQuerySet($delegateId); 
		$resultFetchUser        = $mycms->sql_select($sqlFetchUser);
		$rowFetchUser           = $resultFetchUser[0];
		
		$loggedUserId			= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser = array();
		$sqlSystemUser['QUERY']      	= "SELECT * FROM "._DB_CONF_USER_." 
		                               	   WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   	= $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      	= $resultSystemUser[0];
	?>
		
		<div>
			<table width="100%" class="tborder">
				<tr>
					<td class="tcat">
						<span style="float:left">Profile</span>
						<span class="close" ><strong></strong></span>
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
									<?=strtoupper($rowFetchUser['user_title'])?> 
									<?=strtoupper($rowFetchUser['user_first_name'])?> 
									<?=strtoupper($rowFetchUser['user_middle_name'])?> 
									<?=strtoupper($rowFetchUser['user_last_name'])?>
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
								<td align="left"></td>
								<td align="left"></td>
							</tr>
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
								<td width="30%" align="left" valign="top"><?=$rowFetchUser['classification_title']?></td>
								<td width="20%" align="left" valign="top">Tariff Cut Off:</td>
								<td width="30%" align="left" valign="top"><?=$rowFetchUser['cutoffTitle']?></td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr class="thighlight">	
					<td colspan="2">&nbsp;</td>
				</tr>
				
				<tr class="thighlight">					
					<td align="center" valign="top" colspan="2" style="margin-left:20%; ">
						<?php							
						$totalWorkshopCount   =  getTotalWorkshopCount($rowFetchUser['id']);
						if($totalWorkshopCount ==0 &&  $rowFetchUser['registration_payment_status']!= 'UNPAID' 
								&& (($rowFetchUser['registration_request']=='EXHIBITOR' && $cfg['EXHIBITOR.WORKSHOP.SERVICE'] == 'true') || ($rowFetchUser['registration_request']!='EXHIBITOR' && $cfg['DELEGATE.WORKSHOP.SERVICE'] == 'true')))
						{
						?>
							<a href="registration.php?show=addWorkshop&id=<?=$rowFetchUser['id']?>&userREGtype=<?=$spotUser?>" class="btn btn-large btn-orange">
							<span title="Apply Workshop" style="color:#FFF;" />Add Workshop</a>
						<?php
						}
						
						$totalAccompanyCount   = getTotalAccompanyCount($rowFetchUser['id']);
						if($totalAccompanyCount<4 &&  $rowFetchUser['registration_payment_status']!= 'UNPAID' 
							&& (($rowFetchUser['registration_request']=='EXHIBITOR' && $cfg['EXHIBITOR.ACCOMPANY.SERVICE'] == 'true') || ($rowFetchUser['registration_request']!='EXHIBITOR' && $cfg['DELEGATE.ACCOMPANY.SERVICE'] == 'true')))
						{
						?>
							<a href="registration.php?show=addAccompany&id=<?=$rowFetchUser['id'] ?>&userREGtype=<?=$spotUser?>" class="btn btn-large btn-pink">
							<span title="Add Accompany" />Add Accompany</a>
						<?php
						}
						$totalAccommodationCount   =  getTotalAccommodationCount($rowFetchUser['id']);
						if($totalAccommodationCount ==0 &&  $rowFetchUser['registration_payment_status']!= 'UNPAID' 
								&& $cfg['EXHIBITOR.ACCOMMODATION.SERVICE'] == 'true' && $cfg['DELEGATE.ACCOMMODATION.SERVICE'] == 'true')
						{
						?>
							<a href="registration.php?show=addAccomodation&id=<?=$rowFetchUser['id']?>&userREGtype=<?=$spotUser?>" class="btn btn-large btn-green">								
							<span title="Add Accomodation" />Add Accomodation</a>								
						<?php
						}
						$invoice = countOfInvoiceDelegatePlusAccompany($rowFetchUser['id']);
						$dinnerInvoice = countOfDinnerInvoices($rowFetchUser['id']); 
						//$totalDinnerCount   =  getTotalWorkshopCount($rowFetchUser['id']);
						if($invoice > $dinnerInvoice  &&  $rowFetchUser['registration_payment_status']!= 'UNPAID' 
							&& (($rowFetchUser['registration_request']=='EXHIBITOR' && $cfg['EXHIBITOR.DINNER.SERVICE'] == 'true') || ($rowFetchUser['registration_request']!='EXHIBITOR' && $cfg['DELEGATE.DINNER.SERVICE'] == 'true')))
						{
						?>
							<a href="registration.php?show=addDinner&id=<?=$rowFetchUser['id']?>&userREGtype=<?=$spotUser?>" class="btn btn-large btn-grey">								
							<span title="Add Dinner" />Add Dinner</a>
						<?php
						}
						if($_REQUEST['mode']=='spotSearch')
						{
						?>
							<br/><input type="button" name="bttnSubmitStep1" class="btn btn-large btn-black" value="Close Window" onclick="window.close();" style="margin:5px;"/>
						<?
						}						
						?>								
					</td>
				</tr>
				
				<?
				if($totalWorkshopCount !=0)
				{
				?>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="3" align="left">Workshop Registration Details</td>
							</tr>
							<?
							$resultWorkshopDtls 	= getWorkshopDetailsOfDelegate($delegateId);
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
						
					</td>
				</tr>	
				<?
				}
				?>			
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="8" align="left">Accommodation Details</td>
							</tr>
							<?
							$resultAccommodationDtls 	= getAccomodationDetailsOfDelegate($delegateId);	//getAcompanyDetailsOfDelegate($delegateId);
							//echo "<pre>"; print_r($resultAccommodationDtls); echo "<pre>";
							if($resultAccommodationDtls)
							{
								
							?>
							<tr class="theader">
								<td align="center" width="150">Check In Date</td>
								<td align="center" width="150" >Check Out Date</td>
								<td align="center" >Accompany Name</td>
								<td align="center" width="150">Room Type</td>
								<td align="center" width="150">No. Of Room</td>
								<td align="center" width="150">No. Of Guest</td>
								<td align="center" width="200" >Cutoff</td>
								<td align="center" width="150">Payment Status</td>
							</tr>	
							<?
								$cutoff = "";
								foreach($resultAccommodationDtls as $keyAccommodationDtls=>$rowAccommodationDtls)
								{
									$roomType = getAccomPackageName($rowAccommodationDtls['package_id']);	
								?>
								<tr>
									<td align="center"><?=$rowAccommodationDtls['checkin_date']?>
									</td>
									<td align="center"><?=$rowAccommodationDtls['checkout_date']?></td>
									<td align="center">
										<?
										if($rowAccommodationDtls['accompany_name'] == '')
										{
											echo '-';
										}
										else
										{
											echo $rowAccommodationDtls['accompany_name'];
										}
										?>
									</td>
									<td align="center"><?=$roomType?></td>
									<td align="center"><?=$rowAccommodationDtls['booking_quantity']?></td>
									<td align="center"><?=$rowAccommodationDtls['guest_counter']?></td>
									<td align="center"><?=getCutoffName($rowAccommodationDtls['tariff_cutoff_id'])?></td>
									<td align="center">
										<?
										if($rowAccommodationDtls['payment_status'] != 'UNPAID')
										{
											echo '<span class="unpaidStatus">UNPAID</span>';
										}
										else if($rowAccommodationDtls['payment_status'] == 'PAID')
										{
											echo '<span class="paidStatus">PAID</span>';
										}
										else if($rowAccommodationDtls['payment_status'] == 'ZERO_VALUE')
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
								<td align="center" width="100">Accompany Age</td>
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
									<td align="center"><?
									if($rowAccompanyDtls['user_age'] == '')
										{
											echo '-';
										}
										else
										{
											echo $rowAccompanyDtls['user_age'];
										}
										?></td>
									<td align="center"><?=getCutoffName($rowAccompanyDtls['registration_tariff_cutoff_id'])?></td>
									<td align="center">
										<?
										if($rowAccompanyDtls['registration_payment_status'] != 'UNPAID')
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
								<td colspan="8" align="left">Payment Voucher Details</td>
							</tr>
							<?php
							$paymentCounter   = 0;
							$resFetchSlip	  = slipDetailsOfUser($delegateId,true);							
							
							if($resFetchSlip)
							{
							?>
								<tr class="theader">
									<td width="30" align="center">Sl.</td>
									<td width="100" align="left">PV No.</td>
									<td width="80" align="center">Slip Date</td>
									<td width="80" align="center">Pay Mode</td>
									<td width="100" align="right">Discount Amt.</td>
									<td width="100" align="right">Slip Amt.</td>
									<td width="100" align="right">Paid Amt.</td>
									<td align="center" colspan="2">Payment Status</td>
								</tr>
								<?
								$styleCss = 'style="display:none;"';
								foreach($resFetchSlip as $key=>$rowFetchSlip)
								{
									$counter++;
																		
									$resPaymentDetails      = paymentDetails($rowFetchSlip['id']);
									
									$paymentDescription     = "-";
									if($key==0)
									{
										$paymentId = $resPaymentDetails['id'];
										$slipId=$rowFetchSlip['id'];
									}
									if($resPaymentDetails['payment_mode']=="Cash")
									{
										$paymentDescription = "Paid by <b>Cash</b>. 
															   Date of Deposit: <b>".setDateTimeFormat2($resPaymentDetails['cash_deposit_date'], "D")."</b>.
															   Date of Reciept: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Online")
									{
										$paymentDescription = "Paid by <b>Online</b>. 
															   Date of Payment: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.<br>
															   Transaction Number: <b>".$resPaymentDetails['atom_atom_transaction_id']."</b>.<br>
															   Bank Transaction Number: <b>".$resPaymentDetails['atom_bank_transaction_id']."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Card")
									{
									
										 $paymentDescription = "Paid by <b>Card</b>. 
										 						Reference Number: <b>".$resPaymentDetails['card_transaction_no']."</b>.<br>
																Date of Payment: <b>".setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Draft")
									{
										$paymentDescription = "Paid by <b>Draft</b>. 
															   Draft Number: <b>".$resPaymentDetails['draft_number']."</b>.<br>
															   Draft Date: <b>".setDateTimeFormat2($resPaymentDetails['draft_date'], "D")."</b>.
															   Draft Drawee Bank: <b>".$resPaymentDetails['draft_bank_name']."</b>.<br>
															   Date of Encash: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="NEFT")
									{
										$paymentDescription = "Paid by <b>NEFT</b>. 
															   NEFT Transaction Number: <b>".$resPaymentDetails['neft_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['neft_date'], "D")."</b>.
															   Transaction Bank: <b>".$resPaymentDetails['neft_bank_name']."</b>.<br>
															   Date of Reciept: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="RTGS")
									{
										$paymentDescription = "Paid by <b>RTGS</b>. 
															   RTGS Transaction Number: <b>".$resPaymentDetails['rtgs_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($resPaymentDetails['rtgs_date'], "D")."</b>.
															   Transaction Bank: <b>".$resPaymentDetails['rtgs_bank_name']."</b>.<br>
															   Date of Reciept: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}
									if($resPaymentDetails['payment_mode']=="Cheque")
									{
										$paymentDescription = "Paid by <b>Cheque</b>. 
															   Cheque Number: <b>".$resPaymentDetails['cheque_number']."</b>.<br>
															   Cheque Date: <b>".setDateTimeFormat2($resPaymentDetails['cheque_date'], "D")."</b>.
															   Cheque Drawee Bank: <b>".$resPaymentDetails['cheque_bank_name']."</b>.<br>
															   Date of Encash: <b>".setDateTimeFormat2($resPaymentDetails['payment_date'], "D")."</b>.";
									}									
									if($resPaymentDetails['payment_mode']=="Card")
									{
										$paymentDescription = "Paid by <b>Card</b>. 
															   Card Number: <b>".$resPaymentDetails['card_transaction_no']."</b>.<br>
															   Card Payment Date: <b>".setDateTimeFormat2($resPaymentDetails['card_payment_date'], "D")."</b>.";
									}									
									if($resPaymentDetails['payment_mode']=="Credit")
									{
										$sqlExhibitorName['QUERY']	=	"SELECT `exhibitor_company_name` FROM ".$cfg['DB.EXIBITOR.COMPANY']." 
																	WHERE `exhibitor_company_code` = '".$resPaymentDetails['exhibitor_code']."' ";
													
										$exhibitorName		=	$mycms->sql_select($sqlExhibitorName, false);
										
										$paymentDescription = "Paid by <b>Credit</b>. Exhibitor Code: <b>".$resPaymentDetails['exhibitor_code']."</b>.<br>
															   Credit Payment Date: <b>".setDateTimeFormat2($resPaymentDetails['credit_date'], "D")."</b>.<br>
															   Exhibitor Name: <b>".$exhibitorName[0]['exhibitor_company_name']."</b>.";
									}
									$isChange ="YES";
									
									$excludedAmount = invoiceAmountOfSlip($rowFetchSlip['id'],false,false);
									$amount = invoiceAmountOfSlip($rowFetchSlip['id']);
									
									$discountDeductionAmount = ($excludedAmount-$amount);
									$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$rowFetchSlip['id']);
																				
									$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										if($rowFetchInvoice['service_type']== "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$discountAmountofSlip= $discountDeductionAmount;
										}
										else
										{
											$discountAmountofSlip= ($discountDeductionAmount/1.18);
										}
									}
									?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$counter?></td>
										<td align="left" valign="top"><?=$rowFetchSlip['slip_number']?></td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchSlip['slip_date'], "D")?></td>
										<td align="center" valign="top"><?=$rowFetchSlip['invoice_mode']?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($discountAmountofSlip)==''?'0.00':number_format($discountAmountofSlip,2)?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($amount,2)?></td>
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
										if($resPaymentDetails['payment_status'] == "PAID")
										{
										?>
											<a href="general_registration.php?show=sendInvoiceMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/communication-email.png" title="Resend Invoice Mail" /></a>
										<?
										}
										if($rowFetchSlip['payment_status']=="COMPLIMENTARY"){
										}
										else { 
										?>
										<a href= "<?= $cfg['BASE_URL']?>downloadFinalInvoice.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>&original=true" 
										   target="_blank" style="background:none; border:none;float:right;" >
											<span class="icon-eye" title="Print Payment Voucher"/>
										</a>
										<? } ?>
										<a onclick="$('#invoiceDetails<?=$rowFetchSlip['id']?>').toggle();" style="float:right;">
											<img src="../images/menu.png" title="Show All Slip Invoice" /></a>
											<div class="tooltip"  style="float:right; margin-right: 5px;">
											<span class="tooltiptext" style="width:250px; text-align:left;">
											<?
												$historyOfSlip = historyOfslip($rowFetchSlip['id']);
												if($historyOfSlip)
												{
													foreach($historyOfSlip as $key=>$value)
													{
														echo $value;
													}
												}
												 
											?>											
											</span>
											<img src="../images/history.png"/>
										</div>	
																			
										<?
										if($rowFetchSlip['payment_status']=="COMPLIMENTARY")
										{
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
										?>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
										<?
											}
											else
											{
										?>
											<span style="color:#5E8A26;"><strong>Complimentary</strong></span>
											<!--<a href="general_registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/mail.png" title="Resend Service Confirmation Mail" /></a>
											<a href="general_registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/sms.png" title="Resend Service Confirmation SMS" /></a>-->
										<?
											}
										}
										else if($rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
										?>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
										<?
											}
											else
											{
										?>
											<span style='color:#009900;'>Zero Value</span>
											
										<?
											}
										}
										else
										{
											if($resPaymentDetails['payment_status'] == "UNPAID")
											{
												if($resPaymentDetails['status']=="D")
												{ 
												?>
													<a class="ticket ticket-important" operationMode="proceedPayment" 
													    onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Set Payment Terms</a>
														
												<?
												}
												else
												{
												?>
													<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
												<?
												}
											}
											else if($resPaymentDetails['payment_status'] == "PAID")
											{
												echo $paymentDescription;
												$isChange="NO";
											}
											else
											{
												if($rowFetchSlip['invoice_mode']=='ONLINE')
												{
												?>
													<a class="ticket ticket-important" operationMode="proceedPayment" 
													   onclick="changePaymentPopup('<?=$rowFetchSlip['id']?>','<?=$rowFetchUser['id']?>','OFFLINE')">Change Payment Mode</a>
													  
												<?
													if($loggedUserId == 1 )
													{ 
												?>
													<a class="ticket ticket-important" style="background-color:#0000FF;"operationMode="proceedPayment" 
													   onclick="onlineOpenPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id
													   ']?>')">Complete Online Payment</a>
												<?
													}
												}
												elseif($rowFetchSlip['invoice_mode']=='OFFLINE')
												{
													?>
														<a class="ticket ticket-important" href="general_registration.php?show=additionalRegistrationSummery&sxxi=<?=base64_encode($rowFetchSlip['id'])?>" target="_blank">Set Payment Terms</a>
													<?
												}
											}
										}										
										if($resPaymentDetails['payment_status'] == "PAID" || $rowFetchSlip['payment_status']=="COMPLIMENTARY" || $rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
										?>
										<a href="general_registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/mail.png" title="Resend Service Confirmation Mail" />
										</a>
										<a href="general_registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/sms.png"title="Resend Service Confirmation SMS" />
										</a>
										<?
										}
										?>
										</td>
										<td valign="top">
										<?
										if($resPaymentDetails['payment_status'] == "PAID")
										{
										}
										?>
										<a href= "<?= $cfg['BASE_URL']?>downloadFinalInvoice.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>&original=true" 
										   target="_blank" style="background:none; border:none;float:right;" >
											<span class="icon-eye" title="Print Payment Voucher"/>
										</a>
										<a onclick="$('tr[invUse=invoiceDetails<?=$rowFetchSlip['id']?>]').toggle();" style="float:right;">
											<img src="../images/menu.png" title="Show All Slip Invoice" />
										</a>
										<div class="tooltip"  style="float:right; margin-right: 5px;">
											<span class="tooltiptext" style="width:250px; text-align:left;">
												<?
													$historyOfSlip = historyOfslip($rowFetchSlip['id']);
													if($historyOfSlip)
													{
														foreach($historyOfSlip as $key=>$value)
														{
														
															echo $value;
														}
													}
												?>
											</span>
											<img src="../images/history.png"/>
										</div>
										<?
										if($resPaymentDetails['payment_status'] == "PAID" || $rowFetchSlip['payment_status']=="COMPLIMENTARY" || $rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
										?>
										<br/>
										<a href="registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/mail.png" title="Resend Service Confirmation Mail" />
										</a>
										<a href="registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/sms.png" title="Resend Service Confirmation SMS" />
										</a>
										<?
										}
										?>
										</td>
									</tr>
									
									<tr invUse="invoiceDetails<?=$rowFetchSlip['id']?>" style="display:none;">
										<td colspan="8"  style="border:1px dashed #E56200;">
											<table width="100%">
												<tr class="theader">
													<td width="30" align="center">Sl No</td>
													<td align="left"  width="100">Invoice No</td>
													<td width="80" align="center">Invoice Mode</td>
													
													<td align="center">Invoice For</td>
													<td width="70" align="center">Invoice Date</td>
													<td width="110" align="right">Invoice Amount</td>
													<td width="100" align="center">Payment Status</td>
													<td width="70" align="center">Action</td>
												</tr>
												<?php
												
												$invoiceCounter                 = 0;
												$sqlFetchInvoice                = invoiceDetailsQuerySet("","",$rowFetchSlip['id']);
																				
												$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
												
												$ffff = getInvoice($rowFetchSlip['id']);
												//echo "<pre>";print_r($ffff);echo "</pre>";
												if($resultFetchInvoice)
												{
													foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
													{
														$returnArray    = discountAmount($rowFetchInvoice['id']);
														$percentage     = $returnArray['PERCENTAGE'];
														$totalAmount    = $returnArray['TOTAL_AMOUNT'];
														$discountAmount = $returnArray['DISCOUNT'];
														$isDelegate     = "YES";
														//echo $dId = $rowFetchInvoice['delegate_id'];
														
														$temp = $rowFetchInvoice['delegate_id']%2;
														if($temp == 1)
														{
															$styleColor = 'background:#CCCCFF';
															//echo $dId = $rowFetchInvoice['delegate_id'];
														}
														else
														{
															$styleColor = 'background: rgb(204, 229, 204);';
														}
														
														$dId = $rowFetchInvoice['delegate_id'];
														$invoiceCounter++;
														$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
														$type			 = "";
														$isConfarance	 = "";
														if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
														{
															$ConfSlipId = $rowFetchInvoice['slip_id'];
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"CONFERENCE"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"RESIDENTIAL"). ' - '.$thisUserDetails['user_full_name'];
														}
														
														if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
														{
															$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);															
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"WORKSHOP"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMPANY"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"ACCOMMODATION"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
														{
															$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);															
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"TOUR"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
														{
															$type = getInvoiceTypeString($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER"). ' - '.$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
														{
															$isCancel	= 'NO';
														}
														if($rowFetchInvoice['status'] =='C')
														{
															$styleColor = 'background: rgb(255, 204, 204);';
															$isCancel	= 'YES';
															$type = "Invoice Cancelled";
														}
														$dId ="";
														if($dId !=$rowFetchInvoice['delegate_id'] &&($rowFetchInvoice['service_type']=="DELEGATE_CONFERENCE_REGISTRATION"||$rowFetchInvoice['service_type']=="DELEGATE_RESIDENTIAL_REGISTRATION"))
														{
															
													?>
															<tr bgcolor="#99CCFF">
																<td colspan="8" style="border:thin solid #000;" align="left"  valign="top" height="20px" >User Name : <?=$thisUserDetails['user_full_name']?>
																</td>
															</tr>		
															
													<?
														}
														
														if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
														{
															$slipInvRowSpan = 2;
														}
														else
														{
															$slipInvRowSpan = 1;
														}
													?>
														<tr class="tlisting" style=" <?=$styleColor?>">
															<td align="center"><?=$invoiceCounter++?></td>
															<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
															<td align="center" rowspan="<?=$slipInvRowSpan?>"><?=$rowFetchInvoice['invoice_mode']?></td>
															<td align="left"><?=$type?></td>
															<td align="center" rowspan="<?=$slipInvRowSpan?>"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
															<td align="right" rowspan="<?=$slipInvRowSpan?>"><?=$rowFetchInvoice['currency']?> <?=number_format($totalAmount,2)?></td>
															<td align="center" rowspan="<?=$slipInvRowSpan?>">
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
															<td align="center">
																<?php			
																if($isCancel== 'NO' && $rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID")
																{
																?>
																	<a href="print.invoice.php?user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
																	<span class="icon-eye" title="View Invoiceersger"/></a>
																<?php
																}
																if($isConfarance != "CONFERENCE"
																	&& $rowFetchInvoice['payment_status']=="UNPAID" && $isCancel== 'NO')
																{
																?>
																	<a href="registration.process.php?act=cancel_invoice&user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>&curntUserId=<?=$delegateId?>">
																	<span class="icon-x" title="Cancel Invoice"/></a>
																<?
																}
																?>
															</td>
														</tr>
												<?php
														if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
														{
												?>
														<tr class="tlisting" style=" <?=$styleColor?>">
															<td align="center"><?=$invoiceCounter?></td>
															<td align="left"><?=getRuedaInvoiceNo($rowFetchInvoice['id'])?></td>
															<td align="left"><?='Internet Handling Charges (REF: '.$rowFetchInvoice['invoice_number'].')'?></td>															
															<td align="center">
																<?php			
																if($isCancel== 'NO' && $rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID")
																{
																?>
																	<a href="print.invoice.php?show=intHand&user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
																	<span class="icon-eye" title="View Invoiceersger"/></a>
																<?php
																}																
																?>
															</td>
														</tr>
												<?php
														}
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
									User Invoice
									<a onclick="$('tr[use=all]').toggle();" style="float:right;"><img src="../images/view_details.png"  title="Show Your All Invoice" /></a>
									</td>
								</tr>
								<tr class="theader"  use="all">
									<td width="30" align="center">Sl No</td>
									<td align="left"  width="100">Invoice No</td>
									<td align="left"  width="100">PV No</td>
									<td width="80" align="center">Invoice Mode</td>
									<td align="center">Invoice For</td>
									<td width="70" align="center">Invoice Date</td>
									<td width="110" align="right">Invoice Amount</td>
									<td width="100" align="center">Payment Status</td>
									<td width="70" align="center">Action</td>
								</tr>
								<?php
								$invoiceCounter                 = 0;
								$sqlFetchInvoice                = invoiceDetailsQuerySet("",$delegateId,"");
																
								$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
								
								
								if($resultFetchInvoice)
								{
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										$invoiceCounter++;
										$slip = getInvoice($rowFetchInvoice['slip_id']);
										$returnArray    = discountAmount($rowFetchInvoice['id']);
										$percentage     = $returnArray['PERCENTAGE'];
										$totalAmount    = $returnArray['TOTAL_AMOUNT'];
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
										
										if($rowFetchInvoice['status']=='C')
										{
											$styleColor = 'background: #FFCCCC;';
										}
										else
										{
											$styleColor = 'background: rgb(204, 229, 204);';
										}
										
										if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
										{
											$invRowSpan = 2;
										}
										else
										{
											$invRowSpan = 1;
										}

										
								?>
										<tr class="tlisting" use="all" style=" <?=$styleColor?>">
											<td align="center"><?=$invoiceCounter++?></td>
											<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
											<td align="left" rowspan="<?=$invRowSpan?>"><?=$slip['slip_number']?></td>
											<td align="center" rowspan="<?=$invRowSpan?>"><?=$rowFetchInvoice['invoice_mode']?></td>
											<td align="left"><?=$type?></td>
											<td align="center" rowspan="<?=$invRowSpan?>"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
											<td align="right" rowspan="<?=$invRowSpan?>">
											<?=$rowFetchInvoice['currency']?> <?=number_format($totalAmount,2)?>
											</td>
											<td align="center" rowspan="<?=$invRowSpan?>">
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
											<td align="center">
												<?php			
												if(($rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID"))
												{
												?>
													<a href="print.invoice.php?user_id=<?=$rowFetchUser['id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
													<span class="icon-eye" title="View Invoice"/></a>
													
												<?php
												}
												?>
											</td>
										</tr>
								<?php
										if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
										{
								?>
										<tr class="tlisting" use="all" style=" <?=$styleColor?>">
											<td align="center"><?=$invoiceCounter?></td>
											<td align="left"><?=getRuedaInvoiceNo($rowFetchInvoice['id'])?></td>											
											<td align="left"><?='Internet Handling Charges (REF: '.$rowFetchInvoice['invoice_number'].')'?></td>											
											<td align="center">
												<?php			
												if(($rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID"))
												{
												?>
													<a href="print.invoice.php?show=intHand&user_id=<?=$rowFetchUser['id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
													<span class="icon-eye" title="View Invoice"/></a>
													
												<?php
												}
												?>
											</td>
										</tr>
								<?php
										}
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
		//$mycms->removeSession('SLIP_ID');
	}


	function viewInvoiceDetails()
	{ 
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.workshop.php');
		include_once('../../includes/function.dinner.php');
		global $cfg, $mycms;
		$delegateId 	=  $_REQUEST['id'];
		$loggedUserId	= $mycms->getLoggedUserId();
		$rowFetchUser   = getUserDetails($delegateId);
		?>
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
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_title'])?> 
									<?=strtoupper($rowFetchUser['user_first_name'])?> 
									<?=strtoupper($rowFetchUser['user_middle_name'])?> 
									<?=strtoupper($rowFetchUser['user_last_name'])?>
								</td>
								<td width="20%" align="left">Registration Type:</td>
								<td width="30%" align="left"><span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>;"><b><?=$rowFetchUser['registration_request']?></b></span></td>
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
							<tr class="thighlight">
								<td colspan="7" align="left">Payment Voucher Details</td>
							</tr>
							<?php
							$paymentCounter   = 0;
							$resFetchSlip	  = slipDetailsOfUser($delegateId,true);
							if($resFetchSlip)
							{
							?>
								<tr class="theader">
									<td width="50" align="center">Sl No</td>
									<td width="100" align="left">PV Number</td>
									<td width="100" align="center">Slip Date</td>
									<td width="100" align="center">Payment Mode</td>
									<td width="100" align="right">Discount Amt.</td>
									<td width="100" align="center">Slip Amt.</td>
									<td width="100" align="right">Paid Amt.</td>
									<td align="center">Payment Status</td>
								</tr>
								<?
								$styleCss = 'style="display:none;"';
								
								foreach($resFetchSlip as $key=>$rowFetchSlip)
								{
								//print_r($rowFetchSlip);
									$counter++;
									$resPaymentDetails      = paymentDetails($rowFetchSlip['id']);
									$paymentDescription     = "-";
									if($key==0)
									{
										$paymentId = $resPaymentDetails['id'];
										$slipId    =$rowFetchSlip['id'];
									}	
									$isChange 		="YES";
									$excludedAmount = invoiceAmountOfSlip($rowFetchSlip['id'],false,false);
							
									$amount 		= invoiceAmountOfSlip($rowFetchSlip['id']);
									$discountDeductionAmount = ($excludedAmount-$amount);
									//$discountAmountofSlip= ($discountDeductionAmount/1.18);
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										if($rowFetchInvoice['service_type']== "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$discountAmountofSlip= $discountDeductionAmount;
										}
										else
										{
											$discountAmountofSlip= ($discountDeductionAmount/1.18);
										}
									}
								?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$counter?></td>
										<td align="left" valign="top"><?=$rowFetchSlip['slip_number']?>
										</td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchSlip['slip_date'], "D")?></td>
										<td align="center" valign="top"><?=$rowFetchSlip['invoice_mode']?></td>
										<!--<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($discountAmountofSlip)==''?'0.00':number_format($discountAmountofSlip,2)?></td>-->
										<td align="center" valign="top"><?=$rowFetchSlip['currency']?> <? $DiscountAmount = invoiceDiscountAmountOfSlip($rowFetchSlip['id'],true);
																		  echo number_format($DiscountAmount,2); ?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency']?> <?=number_format($amount,2)?></td>

										<td align="right" valign="top">
										<?
										if($resPaymentDetails['totalAmountPaid'] > 0)
										{
											echo number_format($resPaymentDetails['totalAmountPaid'],2);
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
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
											?>
											<a class="ticket ticket-important" operationMode="proceedPayment" 
												 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
											<?
											}
											else
											{
										?>
											<span style="color:#5E8A26;"><strong>Complimentary</strong></span>
										<?
											}
										}
										else if($rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
											$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
											if($slipInvoiceAmount > 0)
											{
										?>
												<a class="ticket ticket-important" operationMode="proceedPayment" 
													 onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$resPaymentDetails['id']?>')">Unpaid</a>
										<?
											}
											else
											{
										?>
											<span style='color:#009900;'>Zero Value</span>
											
										<?
											}
										}
										else
										{
										?>
											<a onclick="$('#paymentDetails<?=$rowFetchSlip['id']?>').toggle();">
											Payments</a>
										<?php
										}
										?>
										<a href= "<?=_BASE_URL_?>downloadFinalInvoice.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>&original=true" 
										   target="_blank" style="background:none; border:none;float:right;" >
											<span class="icon-eye" title="Print Payment Voucher"/>
										</a>
										
										<a onclick="$('#invoiceDetails<?=$rowFetchSlip['id']?>').toggle();" style="float:right;">
											<img src="../images/menu.png" title="Show All Slip Invoice" /></a>
											<div class="tooltip"  style="float:right; margin-right: 5px;">
											<span class="tooltiptext" style="width:250px; text-align:left;">
											<?
												$historyOfSlip = historyOfslip($rowFetchSlip['id']);
												if($historyOfSlip)
												{
													foreach($historyOfSlip as $key=>$value)
													{
														echo $value;
													}
												}
												 
											?>											
											</span>
											<img src="../images/history.png"/>
											
										</div>	
										
										<span style="float:right; font-weight:bold;">
											<a href="<?=_BASE_URL_?>downloadSlippdf.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>" target="_blank">
											<img src="../../images/download.png" alt="download" title="Download Payment Voucher"/>
											</a></span>
																			
										<?
										if($resPaymentDetails['payment_status'] == "PAID" || $rowFetchSlip['payment_status']=="COMPLIMENTARY" || $rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
										?>
										<a href="registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/mail.png" title="Resend Service Confirmation Mail" />
										</a>
										<a href="registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
										   style="float:right; margin-left: 5px;">
										   <img src="../images/sms.png" title="Resend Service Confirmation SMS" />
										</a>
										<?
										}
										if($resPaymentDetails != '' && $resPaymentDetails['payment_status'] != "UNPAID" && $rowFetchSlip['invoice_mode']=='OFFLINE')
										{
										$slipInvoiceAmount = invoiceAmountOfSlip($rowFetchSlip['id']);
										if(($rowFetchUser['registration_classification_id']!=6) || $slipInvoiceAmount != 0) 
										{
										?>
											<a href="registration.php?show=sendAccknowledgementSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
											   style="margin-left: 5px;">
											   <img src="../images/sms.png" title="Resend Acknowledgement SMS" />
											</a>
											<a href="registration.php?show=sendAccknowledgementMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/communication-email.png" title="Resend Acknowledgement Mail" /></a>
											<?
											
										}
										?>
											
										<?
										}
										?>
										</td>
									</tr>
									
									<tr id="invoiceDetails<?=$rowFetchSlip['id']?>" style="display:none;">
										<td colspan="8"  style="border:1px dashed #E56200;">
											<table width="100%">
												<tr class="theader">
													<td width="30" align="center">Sl No</td>
													<td align="left"  width="100">Invoice No</td>
													<td width="80" align="center">Invoice Mode</td>
													
													<td align="center">Invoice For</td>
													<td width="70" align="center">Invoice Date</td>
													<td width="110" align="right">Invoice Amount</td>
													<td width="100" align="center">Payment Status</td>
													<td width="70" align="center">Action</td>
												</tr>
												<?php
												
												$invoiceCounter                 = 0;
												$resultFetchInvoice             = getInvoiceDetailsquery("","",$rowFetchSlip['id']);
										
												$ffff = getInvoice($rowFetchSlip['id']);
												if($resultFetchInvoice)
												{
													foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
													{
														 $adonSlipId = $rowFetchInvoice['id'];
														$isDelegate = "YES";
														//echo $dId = $rowFetchInvoice['delegate_id'];
														
														 $temp = $rowFetchInvoice['delegate_id']%2;
														if($temp == 1)
														{
															$styleColor = 'background:#CCCCFF';
															//echo $dId = $rowFetchInvoice['delegate_id'];
														}
														else
														{
															$styleColor = 'background: rgb(204, 229, 204);';
														}
														
														$dId = $rowFetchInvoice['delegate_id'];
														$invoiceCounter++;
														$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
														$type			 = "";
														$isConfarance	 = "";
														if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
														{
															$type = "CONFERENCE REGISTRATION - ".$thisUserDetails['user_full_name'];
															if($rowFetchInvoice['delegate_id'] == $delegateId)
															{
																$isConfarance = "CONFERENCE";
															}
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
														{
															$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
															$type =  strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION  OF ".$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
														{
															//$type = $cfg['RESIDENTIAL_NAME']." OF".$thisUserDetails['user_full_name'];
															
															if($rowFetchUser['registration_classification_id'] == 3)
															{
																$type = $cfg['RESIDENTIAL_NAME']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 7)
															{
																$type = $cfg['RESIDENTIAL_NAME_IN_2N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 8)
															{
																$type = $cfg['RESIDENTIAL_NAME_IN_3N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 9)
															{
																$type = $cfg['RESIDENTIAL_NAME_SH_2N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 10)
															{
																$type = $cfg['RESIDENTIAL_NAME_SH_3N']." - ".$thisUserDetails['user_full_name'];
															}
															
															else if($rowFetchUser['registration_classification_id'] == 11)
															{
																$type = $cfg['RESIDENTIAL_NAME_IN_2N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 12)
															{
																$type = $cfg['RESIDENTIAL_NAME_IN_3N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 13)
															{
																$type = $cfg['RESIDENTIAL_NAME_SH_2N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 14)
															{
																$type = $cfg['RESIDENTIAL_NAME_SH_3N']." - ".$thisUserDetails['user_full_name'];
															}
															
															else if($rowFetchUser['registration_classification_id'] == 15)
															{
																$type = $cfg['RESIDENTIAL_NAME_IN_2N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 16)
															{
																$type = $cfg['RESIDENTIAL_NAME_IN_3N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 17)
															{
																$type = $cfg['RESIDENTIAL_NAME_SH_2N']." - ".$thisUserDetails['user_full_name'];
															}
															else if($rowFetchUser['registration_classification_id'] == 18)
															{
																$type = $cfg['RESIDENTIAL_NAME_SH_3N']." - ".$thisUserDetails['user_full_name'];
															}
														}
														if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
														{
															$thisUserAccompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
															$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name']." - ".$thisUserAccompanyDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
														{
															$type = "ACCOMMODATION BOOKING - ".$thisUserDetails['user_full_name'];
														}
														if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
														{
															$type = $cfg['BANQUET_DINNER_NAME']." - ".getInvoiceTypeStringForMail($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER")	;
														}
														$isCancel	= 'NO';
														if($rowFetchInvoice['status'] =='C')
														{
															$styleColor = 'background: rgb(255, 204, 204);';
															$isCancel	= 'YES';
															$type = "Invoice Cancelled";
														}
														$dId ="";
														if($dId !=$rowFetchInvoice['delegate_id'] &&($rowFetchInvoice['service_type']=="DELEGATE_CONFERENCE_REGISTRATION"||$rowFetchInvoice['service_type']=="DELEGATE_RESIDENTIAL_REGISTRATION"))
														 {
															
															?>
															<tr bgcolor="#99CCFF">
																<td colspan="8" style="border:thin solid #000;" align="left"  valign="top" height="20px" >User Name : <?=$thisUserDetails['user_full_name']?>
																</td>
															</tr>		
															
															<?
														 }
													?>
														<tr class="tlisting" style=" <?=$styleColor?>">
															<td align="center"><?=$invoiceCounter?></td>
															<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
															<td align="center"><?=$rowFetchInvoice['invoice_mode']?></td>
															<td align="left">
																<?=$type?>
															</td>
															<td align="center"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
															<td align="right">
															<?=$rowFetchInvoice['currency']?> <?=number_format($rowFetchInvoice['service_roundoff_price'],2)?>
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
															<td align="center">
																<?php			
																if($isCancel== 'NO')
																{
																?>
																	<a href="print.invoice.php?user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
																	<span class="icon-eye" title="View Invoice"/></a>
																<?php
																}
																if($isConfarance != "CONFERENCE"
																	&& $rowFetchInvoice['payment_status']=="UNPAID" && $isCancel== 'NO')
																{
																?>
																	<a href="registration.process.php?act=cancel_invoice&user_id=<?=$rowFetchInvoice['delegate_id']?>&invoice_id=<?=$rowFetchInvoice['id']?>&curntUserId=<?=$delegateId?>">
																	<span class="icon-x" title="Cancel Invoice"/></a>
																<?
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
									
									<tr id="paymentDetails<?=$rowFetchSlip['id']?>">
										<td colspan="8"  style="border:1px dashed #E56200;">
											<table width="100%">
												<tr class="theader">
													<td width="50px" align="center">Sl No</td>
													<td width="150px" align="center">Slip No</td>
													<td align="center">Payment Mode</td>
													<td width="150px" align="center">Amount</td>
													<td width="150px" align="center">Action</td>
												</tr>
												<?php
												$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);
												$paymentCounter                 = 0;
												
												foreach($resPaymentDetails['paymentDetails'] as $key=>$rowPayment)
												{
													$paymentCounter++;
													
													if($rowPayment['payment_mode']=="Cash")
													{
														$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>".setDateTimeFormat2($rowPayment['cash_deposit_date'], "D")."</b>.";
													}
													if($rowPayment['payment_mode']=="Online")
													{
														$paymentDescription = "Paid by <b>Online</b>. Date of Payment: <b>".setDateTimeFormat2($rowPayment['payment_date'], "D")."</b>.<br>
																				Transaction Number: <b>".$rowPayment['atom_atom_transaction_id']."</b>.<br>
																				Bank Transaction Number: <b>".$rowPayment['atom_bank_transaction_id']."</b>.";
													}
													if($rowPayment['payment_mode']=="Card")
													{
														$paymentDescription = "Paid by <b>Card</b>. Reference Number: <b>".$rowPayment['card_transaction_no']."</b>.<br>
																				Date of Payment: <b>".setDateTimeFormat2($rowPayment['card_payment_date'], "D")."</b>.
																				Remarks: <b>".$rowPayment['payment_remark']."</b> ";
													}
													if($rowPayment['payment_mode']=="Draft")
													{
														$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>".$rowPayment['draft_number']."</b>.<br>
																			   Draft Date: <b>".setDateTimeFormat2($rowPayment['draft_date'], "D")."</b>.
																			   Draft Drawee Bank: <b>".$rowPayment['draft_bank_name']."</b>.";
													}
													if($rowPayment['payment_mode']=="NEFT")
													{
														$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>".$rowPayment['neft_transaction_no']."</b>.<br>
																			   Transaction Date: <b>".setDateTimeFormat2($rowPayment['neft_date'], "D")."</b>.
																			   Transaction Bank: <b>".$rowPayment['neft_bank_name']."</b>.";
													}
													if($rowPayment['payment_mode']=="RTGS")
													{
														$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>".$rowPayment['rtgs_transaction_no']."</b>.<br>
																			   Transaction Date: <b>".setDateTimeFormat2($rowPayment['rtgs_date'], "D")."</b>.
																			   Transaction Bank: <b>".$rowPayment['rtgs_bank_name']."</b>.";
													}
													if($rowPayment['payment_mode']=="Cheque")
													{
														$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>".$rowPayment['cheque_number']."</b>.<br>
																			   Cheque Date: <b>".setDateTimeFormat2($rowPayment['cheque_date'], "D")."</b>.
																			   Cheque Drawee Bank: <b>".$rowPayment['cheque_bank_name']."</b>.";
													}
												?>
													<tr class="tlisting">
														<td align="center"><?=$paymentCounter?></td>
														<td align="center"><?=getSlipNumber($resPaymentDetails['slip_id'])?></td>
														<td align="center"><?=$paymentDescription?></td>
														<td align="center">
															<?=$rowPayment['amount']?>
														</td>
														<td align="center">
														<?php
														if($rowPayment['payment_status'] == "UNPAID")
														{
															
															if($rowPayment['status']=="D")
															{ 
															?>
																<a class="ticket ticket-important" operationMode="proceedPayment" 
																	onclick="openPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['payment_id']?>')">Set Payment Terms</a>
															<?
															}
															else
															{
															?>
																<a class="ticket ticket-important" operationMode="proceedPayment" 
																 onclick="multiPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['payment_id']?>')">Unpaid</a>
															<?
															}
														}
														else if($rowPayment['payment_status'] == "PAID")
														{
															echo $paymentDescription;
															$isChange="NO";
														}
														?>
														</td>
													</tr>
											<?php
												}
												
												if($resPaymentDetails['has_to_set_payment'] == 'Yes')
												{
													if($resPaymentDetails['slip_invoice_mode']=='ONLINE')
													{
													?>
													<tr class="tlisting">
														<td colspan="3">&nbsp;</td>
														<td>
															<a class="ticket ticket-important" operationMode="proceedPayment" 
															 onclick="changePaymentPopup('<?=$rowFetchSlip['id']?>','<?=$rowFetchUser['id']?>','OFFLINE')">Change Payment Mode</a>
														</td>  
													<?
														if($loggedUserId == 1 )
														{ 
													?>
														<td>
															<a class="ticket ticket-important" style="background-color:#0000FF;"operationMode="proceedPayment" 
															onclick="onlineOpenPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['id']?>')">Complete Online Payment</a>
														</td>
													<?
														}
													?>
													</tr>
													<?php
													}
													elseif($resPaymentDetails['slip_invoice_mode']=='OFFLINE' && ($totalNoOfUnpaidCount==0))
													{
													?>
														<tr class="tlisting">
															<td colspan="5" align="right">
																<a class="ticket ticket-important" href="registration.php?show=additionalPaymentArea&sxxi=<?=base64_encode($rowFetchSlip['id'])?>">Set Payment Terms</a>
															</td>
														</tr>
													<?
													}
												}
												?>
											</table>
										</td>
									</tr>
							<?php
								}
							}
							if($rowfetchAdon['delegate_id']!= $varData['delegate_id'])
							{
							?>
							<tr class="tlisting" style=" <?=$styleColor?>">
								<td align="center" colspan="2"><?=$varData['slip_number']?></td>
								<td align="center"><?=setDateTimeFormat2($varData['slip_date'], "D")?></td>
								<td align="center"><?=$varData['invoice_mode']?></td>
								<td align="left" colspan="3" bgcolor="#9999FF">Paid by - <?=getSlipOwner($rowfetchAdon['slip_id'])?></td>
							</tr
							><?
							}
							
							$resultFetchInvoice                = getInvoiceDetailsquery("",$delegateId,"");
							?>
							</table>
								
						<table width="100%">
							<tr class="thighlight">
								<td colspan="9" align="left"> 
								User Invoice
								<?
								if($rowFetchUser['registration_payment_status']=="PAID")
								{
								?>
									<!--<a href="general_registration.php?show=sendRegConfirmMail&paymentId=<?=$paymentId?>&slipId=<?=$rowfetchAdon['slip_id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin-left: 5px;"><img src="../images/mail.png" title="Resend Registration Confirmation Mail" /></a>-->
								<?
								}
								?>
											
									
									
								<a onclick="$('tr[use=all]').toggle();" style="float:right;"><img src="../images/view_details.png"  title="Show Your All Invoice" /></a>
								</td>
							</tr>
							<tr class="theader"  use="all">
								<td width="30" align="center">Sl No</td>
								<td align="left"  width="100">Invoice No</td>
								<td align="left"  width="100">PV No</td>
								<td width="80" align="center">Invoice Mode</td>
								<td align="center">Invoice For</td>
								<td width="70" align="center">Invoice Date</td>
								<td width="110" align="right">Invoice Amount</td>
								<td width="100" align="center">Payment Status</td>
								<td width="70" align="center">Action</td>
							</tr>
							<?php
							$invoiceCounter                 = 0;
							if($resultFetchInvoice)
							{
								foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
								{
									$invoiceCounter++;
									$slip = getInvoice($rowFetchInvoice['slip_id']);
									$returnArray    = discountAmount($rowFetchInvoice['id']);
									$percentage     = $returnArray['PERCENTAGE'];
								
									$totalAmount    = $returnArray['TOTAL_AMOUNT'];
									
									$discountAmount = $returnArray['DISCOUNT'];
									$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
									$type			 = "";
								if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
									{
										$type = "CONFERENCE REGISTRATION - ".$thisUserDetails['user_full_name'];
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
									{
										$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
										$type =  strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION - ".$thisUserDetails['user_full_name'];
									}
									if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
									{
										$thisUserAccompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
										$type = "ACCOMPANY REGISTRATION - ".$thisUserAccompanyDetails['user_full_name'];
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
									{
										if($rowFetchUser['registration_classification_id'] == 3)
										{
											$type = $cfg['RESIDENTIAL_NAME']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 7)
										{
											$type = $cfg['RESIDENTIAL_NAME_IN_2N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 8)
										{
											$type = $cfg['RESIDENTIAL_NAME_IN_3N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 9)
										{
											$type = $cfg['RESIDENTIAL_NAME_SH_2N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 10)
										{
											$type = $cfg['RESIDENTIAL_NAME_SH_3N']." - ".$thisUserDetails['user_full_name'];
										}
										
										else if($rowFetchUser['registration_classification_id'] == 11)
										{
											$type = $cfg['RESIDENTIAL_NAME_IN_2N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 12)
										{
											$type = $cfg['RESIDENTIAL_NAME_IN_3N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 13)
										{
											$type = $cfg['RESIDENTIAL_NAME_SH_2N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 14)
										{
											$type = $cfg['RESIDENTIAL_NAME_SH_3N']." - ".$thisUserDetails['user_full_name'];
										}
										
										else if($rowFetchUser['registration_classification_id'] == 15)
										{
											$type = $cfg['RESIDENTIAL_NAME_IN_2N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 16)
										{
											$type = $cfg['RESIDENTIAL_NAME_IN_3N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 17)
										{
											$type = $cfg['RESIDENTIAL_NAME_SH_2N']." - ".$thisUserDetails['user_full_name'];
										}
										else if($rowFetchUser['registration_classification_id'] == 18)
										{
											$type = $cfg['RESIDENTIAL_NAME_SH_3N']." - ".$thisUserDetails['user_full_name'];
										}
										
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
									{
										$type = "ACCOMMODATION BOOKING - ".$thisUserDetails['user_full_name'];
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST")
									{
										$type = $cfg['BANQUET_DINNER_NAME']." - ".getInvoiceTypeStringForMail($rowFetchInvoice['delegate_id'],$rowFetchInvoice['refference_id'],"DINNER")	;
									}
									if($rowFetchInvoice['status']=='C')
									{
										$styleColor = 'background: #FFCCCC;';
									}
									else
									{
										$styleColor = 'background: rgb(204, 229, 204);';
									}
									
									if($rowFetchInvoice['has_gst']=='Y' && $rowFetchInvoice['invoice_mode']=='ONLINE')
									{
										$invRowSpan = 2;
									}
									else
									{
										$invRowSpan = 1;
									}
									
							?>
								<tr class="tlisting" use="all" style=" <?=$styleColor?>">
									<td align="center"><?=$invoiceCounter?></td>
									<td align="left"><?=$rowFetchInvoice['invoice_number']?></td>
									<td align="left" ><?=$slip['slip_number']?></td>
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
									<td align="center">
										<?php			
										if(($rowFetchInvoice['payment_status']=="PAID" || $rowFetchInvoice['payment_status']=="UNPAID"))
										{
										?>
											<a href="print.invoice.php?user_id=<?=$rowFetchUser['id']?>&invoice_id=<?=$rowFetchInvoice['id']?>" target="_blank">
											<span class="icon-eye" title="View Invoice"/></a>
											
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
				<tr>
					<td colspan="2" class="tfooter">&nbsp;
						<span style="float: right; color:red;">&nbsp;&nbsp;Cancelled Invoice&nbsp;&nbsp;</span>
						<span style="float: right; background: #FFCCCC; height: 15px; width: 15px;">&nbsp;</span>
						<span style="float: right; color:red;">&nbsp;&nbsp;Active Invoice&nbsp;&nbsp;</span>
						<span style="float: right; background: #CCE5CC; height: 15px; width: 15px;">&nbsp;</span>
						
					</td>
				</tr>
			</tbody> 
		</table>
		<?
		if($_REQUEST['button'] == 'backToSpot')
		{
		?>
			<table align="center">
				<tr align="center">
					<td align="center">
						<a href="<?=$cfg['BASE_URL']?>webmaster/section_spot/spot_create_delegate.php?show=spotUsers" class="btn btn-large btn-red" >BACK</a>
					</td>
				</tr>
			</table>
		<?
		}
		?>
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form2" id="payment_popup"></div>
		<div class="online_popup_form" id="onlinePayment_popup"></div>
		<div class="popup_form2" id="SetPaymentTermsPopup"></div>
		<div class="popup_form2" id="settlementPopup"></div>
		<div class="overlay" id="fade_change_popup" ></div>
		<div class="popup_form2" id="change_payment_popup" style="width:auto; height:auto; display:none; left:50%; margin-left: -210px;">
		<form action="registration.process.php" method="post" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="changePaymentMode" />
		<input type="hidden" name="slip_id" id="slip_id" value="" />
		<input type="hidden" name="registrationMode" id="registrationMode" value=""/>
		<input type="hidden" name="delegate_id" id="delegate_id" value="" />
		<table>
			<tr>
				<td align="right"><span class="close" onclick="closechangePaymentPopup()">X</span></td>
			</tr>
			<tr>
				<td align="center"><h2 style="color:#CC0000;">Do you want to change payment mode<br /><br />to offline?</h2></td>
			</tr>
			<tr>
				<td align="center"><input type="submit" class="btn btn-small btn-red" value="Change" /></td>
			</tr>	
		</table>
		</form>
		</div>
		<script>
		function changePaymentPopup(slipId,delegateId,regMode)
		{
			$("#fade_change_popup").fadeIn(1000);
			$("#change_payment_popup").fadeIn(1000);
			$('#slip_id').val(slipId);
			$('#registrationMode').val(regMode);
			$('#delegate_id').val(delegateId);
		}
		function closechangePaymentPopup()
		{
			$("#fade_change_popup").fadeOut();
			$("#change_payment_popup").fadeOut();
		}
		</script>		
	<?
	}
	
	?>


