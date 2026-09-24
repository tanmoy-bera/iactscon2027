<?php  
 
	include_once('includes/init.php');
	page_header("General Registration");
	include_once("../../includes/function.invoice.php");
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.workshop.php');
	include_once('../../includes/function.dinner.php');
	include_once('../../includes/function.accommodation.php');
	include_once('../../includes/function.registration.php');
?>
	<link rel="stylesheet" type="text/css" href="<?=_BASE_URL_."webmaster/util/"?>font-awesome-4.7.0/css/font-awesome.min.css" />
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
		
		if(isset($_REQUEST['src_hasPickup']) && trim($_REQUEST['src_hasPickup'])!='') { 	    					$searchArray['src_hasPickup']           		   = addslashes(trim($_REQUEST['src_hasPickup'])); }		
		if(isset($_REQUEST['src_hasDropoff']) && trim($_REQUEST['src_hasDropoff'])!='') { 	    					$searchArray['src_hasDropoff']           		   = addslashes(trim($_REQUEST['src_hasDropoff'])); }		
		if(isset($_REQUEST['src_hasTotPlus']) && trim($_REQUEST['src_hasTotPlus'])!='') { 	    					$searchArray['src_hasTotPlus']           		   = addslashes(trim($_REQUEST['src_hasTotPlus'])); }	
		if(isset($_REQUEST['src_hasLapSutur']) && trim($_REQUEST['src_hasLapSutur'])!='') { 	    				$searchArray['src_hasLapSutur']           		   = addslashes(trim($_REQUEST['src_hasLapSutur'])); }	
		if(isset($_REQUEST['src_has3rd4th']) && trim($_REQUEST['src_has3rd4th'])!='') { 	    					$searchArray['src_has3rd4th']           		   = addslashes(trim($_REQUEST['src_has3rd4th'])); }	
		if(isset($_REQUEST['src_hasCerviCancer']) && trim($_REQUEST['src_hasCerviCancer'])!='') { 	    			$searchArray['src_hasCerviCancer']           	   = addslashes(trim($_REQUEST['src_hasCerviCancer'])); }	
		if(isset($_REQUEST['src_hasGalaDinner']) && trim($_REQUEST['src_hasGalaDinner'])!='') { 	    			$searchArray['src_hasGalaDinner']           	   = addslashes(trim($_REQUEST['src_hasGalaDinner'])); }	
		if(isset($_REQUEST['src_hasAccompany']) && trim($_REQUEST['src_hasAccompany'])!='') { 	    				$searchArray['src_hasAccompany']           		   = addslashes(trim($_REQUEST['src_hasAccompany'])); }	
		if(isset($_REQUEST['src_hasAbstract']) && trim($_REQUEST['src_hasAbstract'])!='') { 	    				$searchArray['src_hasAbstract']           		   = addslashes(trim($_REQUEST['src_hasAbstract'])); }	
		if(isset($_REQUEST['src_hasAccommodation']) && trim($_REQUEST['src_hasAccommodation'])!='') { 	    		$searchArray['src_hasAccommodation']           	   = addslashes(trim($_REQUEST['src_hasAccommodation'])); }	
		
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
					
				case'onlyWorkshopReg':
					backButtonOffJS();
					$requestPage  			= "registration.php"; 
					$processPage  			= "registration.process.php"; 
					$registrationRequest	= "ONLY-WORKSHOP";
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
				
				case'addGuestAccompany':	
					$requestPage  			= "registration.php";
					$processPage  			= "registration.process.php";
					$registrationRequest	= "GENERAL";					
					addGuestAccompanyFormTemplate($requestPage, $processPage, $registrationRequest);				
					break;
				
				case'addWorkshop':	
					$requestPage  			= "registration.php";
					$processPage  			= "registration.process.php";
					$registrationRequest	= "GENERAL";					
					registrationWorkshopTemplate($requestPage, $processPage, $registrationRequest);				
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
					
				case'editReallocationOfMasterWorkshop':					
					editReallocationOfMasterWorkshop();
					break;
					
				case'editReallocationOfWorkshop':					
					editReallocationOfWorkshop();
					break;		
								
				case'viewOnlyWorkshopRegistration':					
					viewOnlyWorkshopRegistration($cfg, $mycms);
					break;				
				
				case'upgradeRegistrationPack':					
					upgradeRegistrationPack($cfg, $mycms);
					break;
					
				case'addAccomodation':					
					addAccomodationFormTemplate($requestPage, $processPage, $registrationRequest);
					break;
				
				case'listECMembers':					
					listECMembers($cfg, $mycms);
					break;
					
				case'listNFMembers':					
					listNFMembers($cfg, $mycms);
					break;
				
				case'listUserRegTable':					
					listUserRegTable($cfg, $mycms);
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
		
		
		$accommodationPackageArray = $cfg['ACCOMMODATION_PACKAGE_ARRAY'];
		
		$accommodationPackageArray[3][0]['STARTDATE']['ID']   = 4;
		$accommodationPackageArray[3][0]['STARTDATE']['DATE'] = "2019-09-04";
		$accommodationPackageArray[3][0]['ENDDATE']['ID']     = 1;
		$accommodationPackageArray[3][0]['ENDDATE']['DATE']   = "2019-09-06";
		
		$accommodationPackageArray[3][1]['STARTDATE']['ID']   = 1;
		$accommodationPackageArray[3][1]['STARTDATE']['DATE'] = "2019-09-05";
		$accommodationPackageArray[3][1]['ENDDATE']['ID']     = 2;
		$accommodationPackageArray[3][1]['ENDDATE']['DATE']   = "2019-09-07";
		
		$accommodationPackageArray[3][2]['STARTDATE']['ID']   = 2;
		$accommodationPackageArray[3][2]['STARTDATE']['DATE'] = "2019-09-06";
		$accommodationPackageArray[3][2]['ENDDATE']['ID']     = 3;
		$accommodationPackageArray[3][2]['ENDDATE']['DATE']   = "2019-09-08";
		
		$accommodationPackageArray[3][3]['STARTDATE']['ID']   = 3;
		$accommodationPackageArray[3][3]['STARTDATE']['DATE'] = "2019-09-07";
		$accommodationPackageArray[3][3]['ENDDATE']['ID']     = 5;
		$accommodationPackageArray[3][3]['ENDDATE']['DATE']   = "2019-09-09";
		
		$accommodationPackageArray[4][0]['STARTDATE']['ID']   = 4;
		$accommodationPackageArray[4][0]['STARTDATE']['DATE'] = "2019-09-04";
		$accommodationPackageArray[4][0]['ENDDATE']['ID']     = 2;
		$accommodationPackageArray[4][0]['ENDDATE']['DATE']   = "2019-09-07";
		
		$accommodationPackageArray[4][1]['STARTDATE']['ID']   = 1;
		$accommodationPackageArray[4][1]['STARTDATE']['DATE'] = "2019-09-05";
		$accommodationPackageArray[4][1]['ENDDATE']['ID']     = 3;
		$accommodationPackageArray[4][1]['ENDDATE']['DATE']   = "2019-09-08";
		
		$accommodationPackageArray[4][2]['STARTDATE']['ID']   = 2;
		$accommodationPackageArray[4][2]['STARTDATE']['DATE'] = "2019-09-06";
		$accommodationPackageArray[4][2]['ENDDATE']['ID']     = 5;
		$accommodationPackageArray[4][2]['ENDDATE']['DATE']   = "2019-09-09";
		
		$cfg['ACCOMMODATION_PACKAGE_ARRAY'] = $accommodationPackageArray;
		
		
	?>
			<script>
			$(document).ready(function(){
				$("td[use=registrationDetailsList]").attr("dataStat","noDisplay");
				
				loadUserDetails();
				
				$("div[operation=userRegistrationPopupOverlay]").click(function(){
					$("div[operation=userRegistrationPopupOverlay]").hide(500);
			    	$("div[operation=userRegistrationPopup]").hide(500);
				});
			});
			
			function loadUserDetails()
			{
				if($("td[use=registrationDetailsList][dataStat=noDisplay]").length > 0)
				{
					var detailsTd = $("td[use=registrationDetailsList][dataStat=noDisplay]").first();
					var userId = $(detailsTd).attr("userId");
					
					var param = "act=getRegistrationDetails&id="+userId;
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
			
			function openUserDetailsViewPopup(obj)
			{				
				var id = $(obj).attr('userId');
								
				$.ajax({
					type: "POST",
					url: 'registration.process.php',
					data: 'act=viewDetails&delegateId='+id,
					dataType: 'html',
					async: false,
					success:function(returnMessage)
					{
						$('div[identifier=popup_profile_full_details]').html(returnMessage);						
						$('div[identifier=popup_profile_full_details]').find('span[use=closeThisPopup]').removeAttr("onClick");
						$('div[identifier=popup_profile_full_details]').find('span[use=closeThisPopup]').click(function(){
							closeUserRegistrationPopupForms(this)
						});
					}
				});
				
				openUserRegistrationPopupForms('popup_profile_full_details');
			}	
			
			function openUserRegistrationPopupForms(identifier)
			{
				 $("div[operation=userRegistrationPopupOverlay]").toggle(500);
				 $("div[identifier='"+identifier+"']").toggle(500);
			}
			
			function closeUserRegistrationPopupForms(obj)
			{
				var parent =  $(obj).parent().closest("div[operation=userRegistrationPopup]");				
				$("div[operation=userRegistrationPopupOverlay]").hide(500);
			    $("div[operation=userRegistrationPopup]").hide(500);
			}			
			
			function openCallDetailData(obj)
			{	
				var delegateId = $(obj).attr('userId');
				var identifier = 'user_call_popup_form';
				
				$("div[identifier='"+identifier+"']").find("input[type=text]").val('');
				$("div[identifier='"+identifier+"']").find("input[type=date]").val('');
				$("div[identifier='"+identifier+"']").find("select").val('');				
				$("div[identifier='"+identifier+"']").find("input[id=delegateId]").val(delegateId);				
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}
			
			function openEditNamePopup(obj)
			{
				
				var id  			= $(obj).attr('userId');
				var user_title  	= $(obj).attr('userTitle'); 
				var firstname  		= $(obj).attr('userFirstName');  
				var middlename  	= $(obj).attr('userMiddleName');   
				var lastname  		= $(obj).attr('userLastName');  
				var identifier 		= 'edit_name_popup_form';  
								
				$("div[identifier='"+identifier+"']").find('#delegateId').val(id);
				$("div[identifier='"+identifier+"']").find('#user_title').val(user_title);
				$("div[identifier='"+identifier+"']").find('#user_first_name').val(firstname);
				$("div[identifier='"+identifier+"']").find('#user_middle_name').val(middlename);
				$("div[identifier='"+identifier+"']").find('#user_last_name').val(lastname);
								
				$("div[identifier='"+identifier+"']").find('#email_status').val("");
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}
			
			function openEditEmailPopup(obj)
			{
				var id  			= $(obj).attr('userId');
				var email  			= $(obj).attr('userEmail');
				var identifier 		= 'edit_email_popup_form';  
								
				$("div[identifier='"+identifier+"']").find('#user_id').val(id);
				$("div[identifier='"+identifier+"']").find('#new_email_id').val(email);
				$("div[identifier='"+identifier+"']").find('#old_email_id').val(email);
				$("div[identifier='"+identifier+"']").find('#email_status').html("");
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this);
				});
			}
			
			function openEditMobilePopup(obj)
			{
				var id  			= $(obj).attr('userId');
				var mobile  		= $(obj).attr('userMobile');
				var isd  			= $(obj).attr('userIsd');
				var identifier 		= 'edit_mobile_popup_form';  				
				
				$("div[identifier='"+identifier+"']").find('#delegate_id').val(id);
				$("div[identifier='"+identifier+"']").find('#new_isd_code').val(isd);
				$("div[identifier='"+identifier+"']").find('#old_isd_code').val(isd);
				$("div[identifier='"+identifier+"']").find('#new_mobile_no').val(mobile);
				$("div[identifier='"+identifier+"']").find('#old_mobile_no').val(mobile);
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}
						
			function openNotesEditPopup(obj)
			{
				
				var id  			= $(obj).attr('userId');
				var notes  			= $(obj).attr('userNote');
				var identifier 		= 'user_notes';  
								
				$("div[identifier='"+identifier+"']").find('#delegateId').val(id);
				$("div[identifier='"+identifier+"']").find('#note').val(notes);
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}		
			
			function openSharePrefEditPopup(obj)
			{
				
				var id  			= $(obj).attr('accomId');
				var name  			= $(obj).attr('prefName');
				var mobile  		= $(obj).attr('prefMobile');
				var email  			= $(obj).attr('prefEmail');
				var identifier 		= 'user_room_share_pref';  
								
				$("div[identifier='"+identifier+"']").find('#accomId').val(id);
				$("div[identifier='"+identifier+"']").find('#preffered_accommpany_name').val(name);
				$("div[identifier='"+identifier+"']").find('#preffered_accommpany_email').val(mobile);
				$("div[identifier='"+identifier+"']").find('#preffered_accommpany_mobile').val(email);
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}	
			
			function openAccmDateEditPopup(obj)
			{
				
				var id  			= $(obj).attr('accomId');
				var packageId  		= $(obj).attr('packageId');
				
				var identifier 		= 'accom_date_change';  
								
				$("div[identifier='"+identifier+"']").find('#accomId').val(id);
				
				var submitContainer = $("div[identifier='"+identifier+"']").find('div[use=dateContainer]');				
				var contenter = $("div[identifier='"+identifier+"']").find('div[use=SelectContainer]');
				var selectOb = $(contenter).find("select[use='"+packageId+"']").clone();
				
				$(submitContainer).find('select').remove();
				$(submitContainer).append(selectOb);
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}			
						
			</script>
		
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
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
									<td valign="top" align="left" rowspan="4" style="padding-left:20px;">
										 <input type="checkbox" name="src_hasPickup" id="src_hasPickup" 
									 	  value="Y" <?=$_REQUEST['src_hasPickup']=='Y'?'checked':''?> /> Has Pickup
										 <br />
										 <input type="checkbox" name="src_hasDropoff" id="src_hasDropoff" 
									 	  value="Y" <?=$_REQUEST['src_hasDropoff']=='Y'?'checked':''?> /> Has Dropoff
										 <br />
										  <input type="checkbox" name="src_hasNotes" id="src_hasNotes" 
									 	  value="Y" <?=$_REQUEST['src_hasNotes']=='Y'?'checked':''?> /> Has Notes
										 <br /> 
										  <input type="checkbox" name="src_hasPayentTerSetButNotPaid" id="src_hasPayentTerSetButNotPaid" 
									 	  value="Y" <?=$_REQUEST['src_hasPayentTerSetButNotPaid']=='Y'?'checked':''?> /> Has Only Pay-Terms
										 <br />
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
									<td align="left">Conf. Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT classf.`classification_title`, classf.`id`, classf.`currency`, classf.`type`,  masterHotel.hotel_name
																					  FROM "._DB_REGISTRATION_CLASSIFICATION_." classf
																		   LEFT OUTER JOIN "._DB_MASTER_HOTEL_." masterHotel
																						ON classf.residential_hotel_id = masterHotel.id
																					 WHERE classf.status = 'A'
																					   AND classf.`id` != 2
																				  ORDER BY classf.type DESC, IFNULL(classf.residential_hotel_id,0) ASC, classf.sequence_by ASC";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											$optgroupName = "";
											
											if($resultClassification)
											{
												?>
													<optgroup label="Conference Registration">
												<?
												foreach($resultClassification as $key=>$rowClassification) 
												{
													if($optgroupName != $rowClassification['hotel_name'])
													{
														$optgroupName = $rowClassification['hotel_name'];
														echo "</optgroup><optgroup label='"."Residential Package - ".$optgroupName."'>";
													}
													
													if($rowClassification['type']=="DELEGATE")
													{
														$clasNm = $rowClassification['classification_title'];
													}
													elseif($rowClassification['type']=="COMBO")
													{
														$clasNm = str_replace("Residential Package - ","",$rowClassification['classification_title']);
													}
													else
													{
														$clasNm = $rowClassification['classification_title'];
													}
													
													if($rowClassification['type']=="COMBO" && $rowClassification['hotel_name'] != '')
													{
														$classfId = $rowClassification['id'].'-G';
													}
													else
													{
														$classfId = $rowClassification['id'];
													}
												?>
														<option value="<?=$classfId?>" <?=($classfId==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
														<?=$clasNm?>
														</option>
												<?php
													if($rowClassification['type']=="COMBO" && $rowClassification['hotel_name'] != '')
													{
														$classfId = $rowClassification['id'].'-I';
												?>
														<option value="<?=$classfId?>" <?=($classfId==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
														<?=$clasNm.' - Inaugural Offer'?>
														</option>
												<?php
													}	
												}
												?>
													</optgroup>
												<?php
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
									<td align="left">Registration Date:</td>
									<td align="left">
										<input type="date" name="src_registration_from_date" id="src_registration_from_date" 
										 style="width:40%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_from_date']?>" />
										 -
										<input type="date" name="src_registration_to_date" id="src_registration_to_date" 
										 style="width:40%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_to_date']?>" /> 
									</td>
									<td align="left">Tags:</td>
									<td align="left">
										<?
										$tagInKeys = array();
										$sqlAllGeneralUser['QUERY']   = "   SELECT DISTINCT tags 
																			  FROM "._DB_USER_REGISTRATION_." 
																			 WHERE `status` = 'A'";
										$resAllGeneralUser   		  = $mycms->sql_select($sqlAllGeneralUser);
										if($resAllGeneralUser)
										{
											foreach($resAllGeneralUser as $k=>$val)
											{
												if(trim($val['tags']!=''))
												{
													$tagInKeys[$val['tags']] = $val['tags'];
												}
											}
										}
										?>
											<select name="src_user_tags"  style="width:96%;">
												<option value=""> -- Select Tags -- </option>										
										<?
										foreach($tagInKeys as $k=>$tag)
										{
										?>
												<option value="<?=$tag?>" <?=$tag==$_REQUEST['src_user_tags']?'selected':''?>><?=$tag?></option>	
										<?
										}
										?>		
											</select>
									</td>
								</tr>
								<tr>
									<td align="left" colspan=3>CHEQUE / DEMAND DRAFT / NEFT / RTGS / CARD / TRANSACTION No:</td>
										<td align="left" >
										<input type="text" name="src_transaction_slip_no" id="src_transaction_slip_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_transaction_slip_no']?>" />
									</td>
									<td align="right">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left" colspan=4>
									<a href="registration.process.php?act=downloadUserListExcel&FILTER=PAID&<?=$searchString?>"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#0066FF;" title="paid users"></i></a>
									<a href="registration.process.php?act=downloadUserListExcel&FILTER=UNPAID&<?=$searchString?>"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#FF3300;" title="unpaid users"></i></a>
									<a href="registration.process.php?act=downloadUserListAccommodationExcel&<?=$searchString?>"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#33FF99;" title="accommodations"></i></a>
									<a href="registration.process.php?act=downloadAccompanyListExcel&<?=$searchString?>"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#800080;" title="Accompany"></i></a>
									<a href="registration.process.php?act=downloadUserListSummaryExcel&<?=$searchString?>"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#FF33CC;" title="summary"></i></a>
									<a href="registration.process.php?act=downloadCountryWiseUserListExcel&<?=$searchString?>"><i class="fa fa-file-excel-o" aria-hidden="true" style="color:#FF0000;" title="Country State Wise"></i></a>
									</td>
								</tr>
							</table>
							</form>
						</div>
								
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</td>
									<td align="left">Name & Details</td>
									<td width="500" align="center">Service Dtls</td>
									<td width="120" align="center">Action</td>
								</tr>
							</thead>
							<tbody>
								<?php								
								$searchCondition 		= "";		
								
								if(isset($_REQUEST['src_conf_reg_category']) && $_REQUEST['src_conf_reg_category']!='')
								{
									$exploded = explode("-",$_REQUEST['src_conf_reg_category']);
									if(sizeof($exploded)==2)
									{
										$_REQUEST['src_conf_reg_category'] = $exploded[0];
										
										if($exploded[1]=='I')
										{
											$searchCondition  .= " AND delegate_id IN ( SELECT id 
																						  FROM "._DB_USER_REGISTRATION_." 
																						 WHERE status IN ('A','C')
																						   AND user_type = 'DELEGATE'
																						   AND operational_area NOT IN ('EXHIBITOR')
																						   AND isRegistration = 'Y'
																						   AND conf_reg_date <=  '2019-01-15 23:59:59' )";	
										}
										else
										{
											$searchCondition  .= " AND delegate_id IN ( SELECT id 
																						  FROM "._DB_USER_REGISTRATION_." 
																						 WHERE status IN ('A','C')
																						   AND user_type = 'DELEGATE'
																						   AND operational_area NOT IN ('EXHIBITOR')
																						   AND isRegistration = 'Y'
																						   AND conf_reg_date >  '2019-01-15 23:59:59' )";	
										}
									}
								}								
								if($_REQUEST['src_user_tags']!='')
								{
									$alterCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND LOCATE(',".$_REQUEST['src_user_tags'].",', CONCAT(',',tags,',') ) > 0 )";
								}								
								if($_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_email_id LIKE '%".$_REQUEST['src_email_id']."%')";
								}
								if($_REQUEST['src_access_key']!='')
								{
									$searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_unique_sequence LIKE '%".$_REQUEST['src_access_key']."%')";
								}
								if($_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%')";
								}
								if($_REQUEST['src_user_first_name']!='')
								{		
									$searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND (user_first_name  LIKE '%".$_REQUEST['src_user_first_name']."%'
																						 OR user_middle_name LIKE '%".$_REQUEST['src_user_first_name']."%'
																						 OR user_last_name   LIKE '%".$_REQUEST['src_user_first_name']."%'
																						 OR user_full_name LIKE '%".$_REQUEST['src_user_first_name']."%'))";
								}
								if($_REQUEST['src_payment_mode']!='')
								{
									 $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
																			    FROM "._DB_PAYMENT_." 
																			   WHERE status IN ('A')
																			     AND payment_mode = '".$_REQUEST['src_payment_mode']."')";
								}
								if($_REQUEST['src_transaction_id']!='')
								{
									 $searchCondition   .= " AND ( slip_id IN ( SELECT DISTINCT slip_id 
																			      FROM "._DB_PAYMENT_." 
																				 WHERE status IN ('A')
																			       AND ( atom_atom_transaction_id = '".$_REQUEST['src_transaction_id']."'
																				 	     OR atom_merchant_transaction_id = '".$_REQUEST['src_transaction_id']."'
																					     OR atom_bank_transaction_id = '".$_REQUEST['src_transaction_id']."'))
																	OR 
																	slip_id IN ( SELECT DISTINCT slip_id 
																				   FROM "._DB_PAYMENT_REQUEST_." 
																				  WHERE status IN ('A')
																				    AND transaction_id = '".$_REQUEST['src_transaction_id']."'))";
								}
								if($_REQUEST['src_registration_mode']!='')
								{
									$searchCondition   .= " AND invoice_mode LIKE '%".$_REQUEST['src_registration_mode']."%'";
								}
								if($_REQUEST['src_user_last_name']!='')
								{
									$searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND delegate.user_last_name LIKE '%".$_REQUEST['src_user_last_name']."%')";
								}
								if($_REQUEST['src_invoice_no']!='')
								{
									$searchCondition   .= " AND invoice_number LIKE '%".$_REQUEST['src_invoice_no']."%'";
								}
								if($_REQUEST['src_slip_no']!='')
								{
									$searchCondition   .= " AND slip_id IN ( SELECT id
																			   FROM "._DB_SLIP_." 
																			  WHERE status IN ('A')
																			    AND slip_number LIKE '%".$_REQUEST['src_slip_no']."%' )";
								}
								if($_REQUEST['src_transaction_ids']!='')
								{
									$searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
																			   FROM "._DB_PAYMENT_." 
																			  WHERE status IN ('A')
																			    AND atom_atom_transaction_id LIKE '%".$_REQUEST['src_transaction_ids']."%')";
								}
								if($_REQUEST['src_transaction_slip_no']!='')
								{
									$searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
																			   FROM "._DB_PAYMENT_." 
																			  WHERE status IN ('A')
																			    AND (card_transaction_no LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR rrn_number LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR cheque_number LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR draft_number LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR neft_transaction_no LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR rtgs_transaction_no LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR atom_transaction_card_no LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR atom_bank_transaction_id LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR atom_atom_transaction_id LIKE '%".$_REQUEST['src_transaction_slip_no']."%'
																					 OR remarks LIKE '%".$_REQUEST['src_transaction_slip_no']."%' ))";
								}								
								if($_REQUEST['src_conf_reg_category']!='')
								{
									 $searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND registration_classification_id = '".$_REQUEST['src_conf_reg_category']."')";
								}
								if($_REQUEST['src_reg_category']!='')
								{
									if($_REQUEST['src_reg_category']=='Conference')
									{
										 $searchCondition   .= " AND delegate_id IN ( SELECT id 
																					    FROM "._DB_USER_REGISTRATION_." 
																					   WHERE status IN ('A','C')
																					     AND user_type = 'DELEGATE'
																					     AND operational_area NOT IN ('EXHIBITOR')
																					     AND isRegistration = 'Y'
																					     AND registration_classification_id IN (1,3,4,5,6))";
									}
									elseif($_REQUEST['src_reg_category']=='Residential')
									{
										$searchCondition   .= " AND delegate_id IN ( SELECT id 
																					    FROM "._DB_USER_REGISTRATION_." 
																					   WHERE status IN ('A','C')
																					     AND user_type = 'DELEGATE'
																					     AND operational_area NOT IN ('EXHIBITOR')
																					     AND isRegistration = 'Y'
																					     AND registration_classification_id IN (7,8,9,10,11,12,13,14,15,16,17,18))";
									}
								}
								if($_REQUEST['src_registration_type']!='')
								{
									$searchCondition   .= " AND delegate_id IN ( SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND registration_request LIKE '%".$_REQUEST['src_registration_type']."%')";
								}
								if($_REQUEST['src_registration_id']!='')
								{
									$searchCondition   .= " AND delegate_id IN (SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_registration_id LIKE '%".$_REQUEST['src_registration_id']."' 
																 				   AND registration_payment_status IN ('ZERO_VALUE', 'COMPLEMENTARY', 'PAID'))";
								}
								if($_REQUEST['src_payment_status']!='')
								{
									$searchCondition   .= " AND delegate_id IN (SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_registration_id LIKE '%".$_REQUEST['src_registration_id']."' 
																 				   AND registration_payment_status = '".$_REQUEST['src_payment_status']."')";
								}		
								if($_REQUEST['src_workshop_classf']!='')
								{		
									$id =  trim($_REQUEST['src_workshop_classf']);
									$workshop_id = substr($id,0,1);		
									$payment_status = substr($id,1);
									if($payment_status == "P")
									{
										$status = "PAID";
									}
									else if($payment_status == "U")
									{
										$status = "UNPAID";
									}
									else if($payment_status == "C")
									{
										$status = "COMPLEMENTARY";
									}
									else
									{
										$status = "ALL";
									}
									
									if($status != "ALL")
									{			
										  $searchCondition   .= " AND id IN (   SELECT refference_invoice_id 
																				  FROM "._DB_REQUEST_WORKSHOP_." 
																				 WHERE status IN ('A')
																			   	   AND workshop_id = '".$workshop_id."' 
																				   AND status = 'A' 
																				   AND payment_status = '".$status."')";
									 }
									 else
									 {
										 $searchCondition   .= " AND id IN (SELECT refference_invoice_id 
																			  FROM "._DB_REQUEST_WORKSHOP_." 
																			 WHERE status IN ('A')
																			   AND workshop_id = '".$workshop_id."' 
																			   AND status = 'A' )";
									 }			 
								}		
								if($_REQUEST['src_accommodation']!='')
								{
										$searchCondition   .= " AND service_type = 'DELEGATE_ACCOMMODATION_REQUEST' 
																AND payment_status = '".$_REQUEST['src_accommodation']."' 
																AND status = 'A' ";
								}
								if($_REQUEST['src_country_name']!="")
								{
									$searchCondition .= " AND delegate_id IN (  SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_country_id = '".$_REQUEST['src_country_name']."%')";
								}
								if($_REQUEST['src_state_name']!="")
								{
									$searchCondition .= " AND delegate_id IN (  SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND user_state_id = '".$_REQUEST['src_state_name']."')";
								}										
								if($_REQUEST['src_payment_date']!='')
								{
									$searchCondition   .= "  AND slip_id IN ( SELECT DISTINCT slip_id 
																			    FROM "._DB_PAYMENT_." 
																			   WHERE status IN ('A')
																			     AND payment_date = '".$_REQUEST['src_payment_date']."')";
								}								
								if($_REQUEST['src_registration_from_date']!='')
								{
									$searchCondition   .= " AND delegate_id IN (  SELECT id 
																				  FROM "._DB_USER_REGISTRATION_." 
																				 WHERE status IN ('A','C')
																				   AND user_type = 'DELEGATE'
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = 'Y'
																				   AND conf_reg_date BETWEEN  '".$_REQUEST['src_registration_from_date']." 00:00:00'
																				   AND '".$_REQUEST['src_registration_to_date']." 23:59:59')";
								}								
								if($_REQUEST['src_cancel_invoice_id']!='')
								{
									 $searchCondition   .= " AND invoice_number LIKE '%".$_REQUEST['src_cancel_invoice_id']."%' 
									 						 AND status = 'C'";
								}									
								if($_REQUEST['src_hasPickup']!='')
								{
									 $searchCondition   .= " AND delegate_id IN (SELECT user_id FROM "._DB_REQUEST_PICKUP_DROPOFF_." WHERE pikup_time IS NOT NULL)";
								}								
								if($_REQUEST['src_hasDropoff']!='')
								{
									 $searchCondition   .= " AND delegate_id IN (SELECT user_id FROM "._DB_REQUEST_PICKUP_DROPOFF_." WHERE dropoff_time IS NOT NULL)";
								}								
								if($_REQUEST['src_hasNotes']!='')
								{
									 $searchCondition   .= " AND delegate_id IN (   SELECT id 
																					  FROM "._DB_USER_REGISTRATION_." 
																					 WHERE status IN ('A','C')
																					   AND user_type = 'DELEGATE'
																					   AND operational_area NOT IN ('EXHIBITOR')
																					   AND isRegistration = 'Y'
																					   AND conf_reg_date BETWEEN  '".$_REQUEST['src_registration_from_date']." 00:00:00'
																					   AND TRIM(delegate.user_food_preference_in_details) != '')";
								}									
								if($_REQUEST['src_hasPayentTerSetButNotPaid']!='')
								{
									 $searchCondition   .= " AND slip_id IN (SELECT slip_id	 FROM "._DB_PAYMENT_." payment	WHERE status = 'A' AND payment_status = 'UNPAID')";
								}
								
								
								
								$sqlDelegateQueryset 			   = array();
								$sqlDelegateQueryset['QUERY']      = "SELECT DISTINCT delegate_id AS delegate_id									 
																		FROM "._DB_INVOICE_."
																	   WHERE status IN ('A','C') 
																	     AND delegate_id IN ( SELECT id 
																							    FROM "._DB_USER_REGISTRATION_." 
																							   WHERE status IN ('A','C')
																							     AND user_type = 'DELEGATE'
																							     AND operational_area NOT IN ('EXHIBITOR')
																							     AND isRegistration = 'Y')
																	   ".$searchCondition."  ORDER BY delegate_id DESC";														    
								
								//$sqlFetchUser         	= "";								
								//$idArr 					= getAllDelegates("","",$alterCondition,'','R001',true);
								
								$resultFetchUser     	  = $mycms->sql_select_paginated('R001', $sqlDelegateQueryset, 10);	
								
								if($resultFetchUser) //$idArr['IDS']
								{									
									foreach($resultFetchUser as $kkl=>$idRow) //$idArr['IDS'] as $i=>$id
									{
										$id = $idRow['delegate_id'];
										
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
										
										if($rowFetchUser['user_food_preference'] == 'VEG')
										{
											$foodcolor ="#00CC00";
										}
										else
										{
											$foodcolor ="#FF0000";
										}
								?>
										<tr class="tlisting" bgcolor="<?=$color?>" style="color:#000000;">
											<td align="center" valign="top" style="border-bottom:thin dashed #0a5372;">
											<?=$counter + ($_REQUEST['_pgnR001_']*10)?><br />											
											<span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>; cursor:default;" title="<?=$rowFetchUser['registration_request']?>"><b><?=strtoupper(substr($rowFetchUser['registration_request'],0,1))?></b></span><br />
											<?
											if($rowFetchUser['user_gender']=='Male')
											{
											?>
											<i class="fa fa-male" aria-hidden="true" style="color:#FF0000;" title="Male"></i>
											<?
											}
											else
											{
											?>
											<i class="fa fa-female" aria-hidden="true" style="color:#660099;" title="Female"></i>
											<?
											}
											?>
											<br />
											<i class="fa fa-circle" aria-hidden="true" style="color:<?=$foodcolor?>; border:thin solid <?=$foodcolor?>; padding:1px;" title="<?=$rowFetchUser['user_food_preference']?>"></i><br/>
											<?
											if($rowFetchUser['participation_type']=='FACULTY')
											{
											?>
											<br />
											<span style="color:#cc0000; cursor:default;" title="<?=$rowFetchUser['participation_type']?>"><b><?=strtoupper(substr($rowFetchUser['participation_type'],0,1))?></b></span>
											<?
											}
											
											$ecMember = getECMember($rowFetchUser['user_email_id'],$rowFetchUser['user_mobile_no']);
											if($ecMember)
											{
											?>
											<br/>
											<i class="fa fa-certificate" aria-hidden="true" title="<?=$ecMember['designation']?>" style="color:#6666FF;"></i>
											<?
											}
											?>
											
											</td>
											<td align="left" valign="top" style="border-bottom:thin dashed #0a5372;">
												<i class="fa fa-user-md" aria-hidden="true" title="User"></i>&nbsp;<b><?=strtoupper($rowFetchUser['user_full_name'])?></b> 
												<br />
												<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$rowFetchUser['user_mobile_no']?>
												<br />
												<i class="fa fa-envelope" aria-hidden="true" title="Email"></i>&nbsp;<?=$rowFetchUser['user_email_id']?>
												<br />												
												<div style="font-size:13px;">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo '<i class="fa fa-user-o" aria-hidden="true" title="Classification"></i>&nbsp;'. getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo '<i class="fa fa-scissors" aria-hidden="true" title="Cutoff"></i>&nbsp;'.getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
												<br />
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID"  
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo '<i class="fa fa-id-badge" aria-hidden="true" title="Registration Id"></i>&nbsp;'.$rowFetchUser['user_registration_id'];
													echo "<br />";
												}												
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo '<i class="fa fa-universal-access" aria-hidden="true"  title="Unique Sequence No."></i>&nbsp;'.strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												?>
												<i class="fa fa-clock-o" aria-hidden="true" title="Registration Date"></i>&nbsp;<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
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
												
												if($rowFetchUser['suggested_by']!='')
												{
												?>
													<br><span style="color:#7b0f75;">Sugg. By:<br /> <?=$rowFetchUser['suggested_by']?>	</span>
												<?
												}
												
												if($rowFetchUser['user_food_preference_in_details']!='')
												{
												?>
												<br />
												<i class="fa fa-sticky-note" aria-hidden="true" style="color:#FF0000; cursor:pointer;" 
												   onclick="openNotesEditPopup(this);"
												   userId="<?=$rowFetchUser['id']?>"
												   userNote="<?=$rowFetchUser['user_food_preference_in_details']?>"></i>&nbsp;
												<?=$rowFetchUser['user_food_preference_in_details']?>
												<?
												}
												else
												{
												?>
												<br />
												<i class="fa fa-sticky-note-o" aria-hidden="true" style="cursor:pointer;" 
												   onclick="openNotesEditPopup(this);"
												   userId="<?=$rowFetchUser['id']?>"></i>
												<?
												}
												
												$array = $rowFetchUser['tags'];
												$var = (explode(",",$array));
												if($rowFetchUser['tags']!='' && sizeof($var)>0)
												{
												?>
												<br/><i class="fa fa-tags" aria-hidden="true"></i>&nbsp;
												<?
													$tagText = array();
													foreach($var as $key=>$val)
													{
														if($val =='Executive Committee')
														{
															$tagText[] = '<span style="color:#990033;"><b>'.$val.'</b></span>';
														}
														if($val =='Organizing Committee Member')
														{
															$tagText[] = '<span style="color:#009966;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Guest Faculty')
														{
															$tagText[] = '<span style="color:#CC3333;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Special Faculty')
														{
															$tagText[] = '<span style="color:#FF0066;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Regional Faculty')
														{
															$tagText[] = '<span style="color:#007700;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='National Faculty')
														{
															$tagText[] = '<span style="color:#660066;"><b>'.$val.'</b>&nbsp;</span>';
														}															
														if($val =='International Faculty')
														{
															$tagText[] = '<span style="color:#770000;"><b>'.$val.'</b></span>';
														}
														if($val =='Special Guest')
														{
															$tagText[] = '<span style="color:#663399;"><b>'.$val.'</b>&nbsp;</span>';														
														}
														
													}
													
													echo implode(', ',$tagText);
												}	
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">UNREGISTERED</span>
												<?php
												}
												
												?>
												</div>
											</td>												
											<td align="left" valign="top" use="registrationDetailsList" userId="<?=$rowFetchUser['id']?>" colspan="2" style="border-bottom:thin dashed #0a5372; padding:0;">
												<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" />
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
						<?
						echo '<!--'; print_r($idArr['ALL-IDS']); echo '-->';
						?>
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo('R001')?></span>
						<span class="paginationDisplay"><?=$mycms->paginate('R001','pagination')?></span>
					</td>
				</tr>			
			</table>
		
			<div class="overlay" id="fade_popup" operation="userRegistrationPopupOverlay"></div>
			
			<div class="popup_form" identifier="popup_profile_full_details" operation="userRegistrationPopup"></div>
			
			<div class="popup_form2" identifier="user_call_popup_form" use="callPopupForm" operation="userRegistrationPopup">
				<form action="<?=_BASE_URL_?>webmaster/section_registration/registration.process.php" name="callUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return submitCallDetails();">
					<input type="hidden" name="act" value="addCallDetails" />	
					<input type="hidden" name="delegateId" id="delegateId">
					<input type="hidden" name="participantId" id="participantId">
					<?
					foreach($searchArray as $key=>$value)
					{
					?>
					<input type="hidden" name="<?=$key?>" value="<?=$value?>">
					<?
					}
					?>
					<table width="100%" class="tborder">
						<tr>
							<td colspan="2" class="tcat">
								<span style="float:left" >Call Records Entry Screen</span>
								<span class="close" forType="tsearchTool" use="closeThisPopup" >X</span>
							</td>
						</tr>
						<tr>
							<td align="left" width="140"><b>Call DateTime</b></td>
							<td valign="top">
								<input type="date" name="callDate" id="callDate" value= "<?php echo "date(Y/m/d)"?>" style="width:35%" placeholder="Choose Date" required />&nbsp; -&nbsp; 
								<span rel="timeChooser">
									<input type="number" name="callTimeHour" id="callTimeHour" min="0" max="23" specific="hour" value="<?=date('h')?>" style="width:15%;  text-align:center;" required/> : 
									 
									<input type="number" name="callTimeMin" id="callTimeMin" min="0" max="59" specific="min" value="<?=date('i')?>" style="width:15%;   text-align:center;"required/>
								</span>
							</td>
						</tr>
						<tr>
							<td align="left" width="140"><b>Subject</b></td>	
							<td align="left" width="140">
								<select name="call_subject" id="call_subject" required>
									<option value="">-- Select --</option>
									<option value="REGISTRATION">Registration</option>
									<option value="WORKSHOP">Workshop</option>
									<option value="ACCOMPANY">Accompany</option>
									<option value="ACCOMMODATION">Accommodation</option>
									<option value="PICKUP-DROP">Pick Up / Drop Off</option>
									<option value="DINNER">Dinner</option>
									<option value="ABSTRACT">Abstract</option>
									<option value="SCIENTIFIC-PROGRAM">Scientific Program</option>
									<option value="OTHERS">Others</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="left" width="140"><b>Discussion</b></td>	
							<td width="22%" align="left" valign="top"><textarea name="call_contents" id="call_contents" style="width:440px; height:100px; text-transform:uppercase;" required /></textarea>
							<br>
								<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
						</tr>
					</table>		
				</form>
				<script>
					$(document).ready(function(){
						$("form[name=callUpdatePopup]").submit(function(e){
							
							e.preventDefault();
						
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){alert("Data Updated.");}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
				</script>
			</div>
			
			<div class="popup_form" identifier="edit_name_popup_form" operation="userRegistrationPopup">
				<form action="edit_userDetails.process.php" name="nameUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return formNameValidation();">
					<input type="hidden" name="act" id="name_update" value="name_update" />
					<input type="hidden" name="delegateId" id="delegateId" value=""/>
					<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
					<table width="100%" class="tborder">
						<tr>
							<td colspan="5" class="tcat">
								<span style="float:left">Name Update</span>
								<span class="close" forType="tsearchTool" use='closeThisPopup'>&times;</span>
							</td>
						</tr>
						<tr>
							<td width="20%" align="left">Delegate Name:</td>
							<td width="22%" align="left"><input type="text" name="user_first_name" id="user_first_name" style="width:95%; text-transform:uppercase;" value="<?=$_REQUEST['user_full_name']?>"/></td>
							<td width="22%" align="left"><input type="text" name="user_middle_name" id="user_middle_name" style="width:95%; text-transform:uppercase;" value=""/></td>
							<td width="22%" align="left"><input type="text" name="user_last_name" id="user_last_name" style="width:95%; text-transform:uppercase;" value=""/></td>
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 20px;" /></td>
						</tr>
						<tr>
							<td colspan="5"></td>
						</tr>
					</table>		
				</form>
				<script>
					$(document).ready(function(){
						$("form[name=nameUpdatePopup]").submit(function(e){
							
							e.preventDefault();
						
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){
									alert("Data Updated.");
									window.location.reload();
								}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
					
					function formNameValidation()
					{
						var identifier 		= 'edit_name_popup_form';  
						
						if(fieldNotEmpty($("div[identifier='"+identifier+"']").find('#user_first_name'), "Please First Name") == false){ 
							return false; 
						}
						return true;			
					}	
			
				</script>
			</div>
			
			<div class="popup_form" identifier="edit_email_popup_form" operation="userRegistrationPopup">
				<form action="edit_userDetails.process.php" name="emailUpdatePopup" id="emailUpdatePopup" method="post" onsubmit="return formEmailValidation();">
					<input type="hidden" name="act" id="email_update" value="email_update" />
					<input type="hidden" name="user_id" id="user_id" />
					<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
					<input type="hidden" name="email_id_validation" id="email_id_validation" value="" />
					<input type="hidden" name="old_email_id" id="old_email_id" value="" />
					<table width="100%" class="tborder">
						<tr>
							<td colspan="4" class="tcat">
								<span style="float:left">Email Update</span>
								<span class="close" forType="tsearchTool" use='closeThisPopup'>&times;</span>
							</td>
						</tr>
						<tr>
							<td width="15%" align="left">Email Id:</td>
							<td width="35%" align="right"><input type="text" id="new_email_id" name="new_email_id" style="width:100%" /></td>
							<td colspan="2" align="left"><div id="email_status"></div></td>
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="right"><input type="submit" class="ticket ticket-important" value="Update" /></td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4"></td>
						</tr>
					</table>		
				</form>
				
				<script>
					$(document).ready(function(){
						$("div[identifier=edit_email_popup_form]").find('#new_email_id').blur(function(){
							checkEmailValidation($("div[identifier=edit_email_popup_form]").find('#new_email_id'));
						});
						
						$("form[name=emailUpdatePopup]").submit(function(e){							
							
							e.preventDefault();						
							
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){
									alert("Data Updated.");
									window.location.reload();
								}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
					
					function checkEmailValidation(emailControl)
					{
						var identifier 	  = 'edit_email_popup_form';  				   
						var user_email    = $(emailControl).val();
						
						$("div[identifier='"+identifier+"']").find('#email_id_validation').val("");
						$("div[identifier='"+identifier+"']").find('#email_status').html("");
						
						if(user_email!="")
						{
							if(regularExpressionEmail.test($(emailControl).val())==false)
							{
								$("div[identifier='"+identifier+"']").find('#email_status').html('<span style="color:#D41000">Invalid Email Id</span>');
								$("div[identifier='"+identifier+"']").find('#email_id_validation').val('INVALID');
								return false;
							}
							else
							{
								$.ajax({
									type: "POST",
									url: 'edit_userDetails.process.php',
									data: 'act=getEmailValidation&email='+user_email,
									dataType: 'text',
									async: false,
									success:function(returnMessage)
									{
									console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/edit_userDetails.process.php?act=getEmailValidation&email='+user_email);
										returnMessage = returnMessage.trim();
										if(returnMessage == 'IN_USE')
										{
											//$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
											var successContent   = '<div style="font-size:16px; text-align:center;">This Email ID Already Registered.</div>';
												successContent  += '<div style="color:#FF0000; font-size:15px; text-align:center;">Please Use Another Email ID.</div>';
											
											$("div[identifier='"+identifier+"']").find('#email_status').html(successContent);
										}
										else
										{
											$("div[identifier='"+identifier+"']").find('#email_status').html('<span style="color:#009933">Available</span>');
										}
										$("div[identifier='"+identifier+"']").find('#email_id_validation').val(returnMessage);
									}
								});
							}
						}
					}
					
					function formEmailValidation()
					{
						var identifier 		= 'edit_email_popup_form';  
						
						if($("div[identifier='"+identifier+"']").find('#email_id_validation').val()=="IN_USE"){
							
							$("div[identifier='"+identifier+"']").find('#new_email_id').focus();
							$("div[identifier='"+identifier+"']").find('#new_email_id').css('border-color','#D41000');
							alert("Email Id Already In Use");
							return false;
						}
						
						if(fieldNotEmpty($("div[identifier='"+identifier+"']").find('#new_email_id'), "Please Enter Valid Email Id") == false){ 
							return false; 
						}
						
						if(fieldShouldEmailValidate($("div[identifier='"+identifier+"']").find('#new_email_id'), "Please Provide Valid Email Id") == false){ 
							return false; 
						}
					}					
				</script>
			</div>
			
			<div class="popup_form" identifier="edit_mobile_popup_form" operation="userRegistrationPopup">
				<form action="edit_userDetails.process.php" name="mobileUpdatePopup" id="mobileUpdatePopup" method="post" onsubmit="return formMobileValidation();">
					<input type="hidden" name="act" id="mobile_update" value="mobile_update" />
					<input type="hidden" name="delegate_id" id="delegate_id"/>
					<input type="hidden" name="old_isd_code" id="old_isd_code" value="" />
					<input type="hidden" name="old_mobile_no" id="old_mobile_no" value="" />				
					<input type="hidden" name="mobile_id_validation" id="mobile_id_validation" value="" />				
					<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
					<table width="100%" class="tborder">
						<tr>
							<td colspan="4" class="tcat">
								<span style="float:left">Mobile No Update</span>
								<span class="close" forType="tsearchTool" use='closeThisPopup'>&times;</span>
							</td>
						</tr>
						<tr>
							<td width="20%" align="left">Mobile No:</td>
							<td width="10%" align="right">
								<input type="text" id="new_isd_code" name="new_isd_code" forType="new_isd_code" style="width:30%"/>
							</td>
							<td width="30%" align="left">
								<input type="text" id="new_mobile_no" name="new_mobile_no" forType="new_mobile_no"/> 
								<input type="hidden" name="mobile_validation" id="mobile_validation" />
							</td>
							<td><div id="mobile_div"></div></td>
							
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td align="left">&nbsp;</td>
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="right"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 20px;" /></td>
							<td align="left">&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4"></td>
						</tr>
					</table>		
				</form>
				<script>				
					$(document).ready(function(){
						$("form[name=mobileUpdatePopup]").submit(function(e){
							
							e.preventDefault();
						
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){
									alert("Data Updated.");
									window.location.reload();
								}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
				
					function formMobileValidation()
					{
						var identifier 		= 'edit_mobile_popup_form';  
						
						if(fieldNotEmpty($("div[identifier='"+identifier+"']").find('#new_mobile_no,#new_isd_code'), "Please Enter Mobile No") == false){ 
							return false; 
						}
						
						if(isNaN($("div[identifier='"+identifier+"']").find('#new_mobile_no').val()))
						{
							$("div[identifier='"+identifier+"']").find('#new_mobile_no').focus();
							$("div[identifier='"+identifier+"']").find('#new_mobile_no').css('border-color','#D41000');
							$("div[identifier='"+identifier+"']").find('#new_mobile_no').val("");
							alert("Invalid Mobile Number");
							return false;
						}
						
						var mobile_no = $("div[identifier='"+identifier+"']").find('#new_mobile_no').val();
						
						if( mobile_no.length > 10 || mobile_no.length < 10 )
						{
							alert("Mobile Number must be 10 digits");
							return false;
						}	
					}
					
					function checkMobileValidation(mobileControl)
					{
						var identifier 		= 'edit_mobile_popup_form';  
						var user_mobile_no 	= $(mobileControl).val();
						$("div[identifier='"+identifier+"']").find('#mobile_id_validation').val('');
						if(user_mobile_no!="")
						{
							$.ajax({
								type: "POST",
								url: "edit_userDetails.process.php",
								data: "act=getMobileValidation&mobile="+user_mobile_no,
								dataType: "text",
								async: false,
								success:function(returnMessage){
									console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/edit_userDetails.process.php?act=getMobileValidation&mobile='+user_mobile_no);
									returnMessage = returnMessage.trim();
									if(returnMessage == 'IN_USE')
									{
										//$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
										var successContent   = '<div style="color:#FF0000";font-size:16px; text-align:center;">This Mobile No Already Registered.</div>';
										
										$("div[identifier='"+identifier+"']").find('#mobile_div').html(successContent);
									}
									else
									{
										$("div[identifier='"+identifier+"']").find('#mobile_div').html('<span style="color:#009933">Available</span>');
									}
									$("div[identifier='"+identifier+"']").find('#mobile_id_validation').val(returnMessage);
								}
							});
						}
					}
				</script>
			</div>
			
			<div class="popup_form2" identifier="user_notes" operation="userRegistrationPopup">
				<form action="<?=_BASE_URL_?>webmaster/section_registration/registration.process.php" name="notesUpdatePopup" id="notesUpdatePopup" method="post">
					<input type="hidden" name="act" value="updateNote" />	
					<input type="hidden" name="delegateId" id="delegateId">
					<?
					foreach($searchArray as $key=>$value)
					{
					?>
					<input type="hidden" name="<?=$key?>" value="<?=$value?>">
					<?
					}
					?>
					<table width="100%" class="tborder">
						<tr>
							<td colspan="2" class="tcat">
								<span style="float:left" >Notes Entry Screen</span>
								<span class="close" forType="tsearchTool" use="closeThisPopup" >X</span>
							</td>
						</tr>
						<tr>
							<td align="left" width="50" valign="top">
							<b>Note</b>
							<br /><br />
							<input type="checkbox" onclick="$(this).parent().closest('tr').find('#note').val('')" /> CLEAN UP
							</td>	
							<td width="22%" align="left" valign="top">
							<textarea name="note" id="note" style="width:440px; height:100px; text-transform:uppercase;"/></textarea>
							<br>
							<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
						</tr>
					</table>		
				</form>
				<script>
					$(document).ready(function(){
						$("form[name=notesUpdatePopup]").submit(function(e){
							
							e.preventDefault();
						
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){
									alert("Data Updated.");
									window.location.reload();
								}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
				</script>
			</div>
			
			<div class="popup_form2" identifier="user_room_share_pref" operation="userRegistrationPopup">
				<form action="<?=_BASE_URL_?>webmaster/section_registration/registration.process.php" name="sharePrefUpdatePopup" id="sharePrefUpdatePopup" method="post">
					<input type="hidden" name="act" value="updateSharePref" />	
					<input type="hidden" name="accomId" id="accomId">
					<?
					foreach($searchArray as $key=>$value)
					{
					?>
					<input type="hidden" name="<?=$key?>" value="<?=$value?>">
					<?
					}
					?>
					<table width="100%" class="tborder">
						<tr>
							<td colspan="2" class="tcat">
								<span style="float:left" >Room Sharing Preference</span>
								<span class="close" forType="tsearchTool" use="closeThisPopup" >X</span>
							</td>
						</tr>
						<tr>
							<td align="left" width="50" valign="top">
							<b>Name</b>
							</td>	
							<td align="left" valign="top">
							<input type="text" name="preffered_accommpany_name" id="preffered_accommpany_name" style="text-transform:uppercase; width:90%"required/>
							</td>
						</tr>
						<tr>
							<td align="left" width="50" valign="top">
							<b>Mobile</b>
							</td>	
							<td align="left" valign="top">
							<input type="text" name="preffered_accommpany_mobile" id="preffered_accommpany_mobile" style=" width:90%" required/>
							</td>
						</tr>
						<tr>
							<td align="left" width="50" valign="top">
							<b>Email</b>
							</td>	
							<td align="left" valign="top">
							<input type="email" name="preffered_accommpany_email" id="preffered_accommpany_email" style=" width:90%"/>
							<br>
							<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
						</tr>
					</table>		
				</form>
				<script>
					$(document).ready(function(){
						$("form[name=sharePrefUpdatePopup]").submit(function(e){
							
							e.preventDefault();
						
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){
									alert("Data Updated.");
									window.location.reload();
								}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
				</script>
			</div>
			
			<div class="popup_form2" identifier="accom_date_change" operation="userRegistrationPopup">
				<form action="<?=_BASE_URL_?>webmaster/section_registration/registration.process.php" name="accmDateUpdatePopup" id="accmDateUpdatePopup" method="post">
					<input type="hidden" name="act" value="updateAccomDate" />	
					<input type="hidden" name="accomId" id="accomId">
					<?
					foreach($searchArray as $key=>$value)
					{
					?>
					<input type="hidden" name="<?=$key?>" value="<?=$value?>">
					<?
					}
					?>
					<table width="100%" class="tborder">
						<tr>
							<td colspan="2" class="tcat">
								<span style="float:left" >Accommodation Date</span>
								<span class="close" forType="tsearchTool" use="closeThisPopup" >X</span>
							</td>
						</tr>
						<tr>
							<td align="left" width="40%" valign="top">
							<b>CHECK IN - CHECK OUT DATE :</b>
							</td>	
							<td align="left" valign="top">
							<div use="dateContainer"></div>
							<br>
							<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
						</tr>
					</table>		
				</form>
				<div style="display:none;" use="SelectContainer">
				<?php
				$accommodationDetails = $cfg['ACCOMMODATION_PACKAGE_ARRAY'];											
				foreach($accommodationDetails as $packageId=>$rowAccommodation)
				{
				?>
				<select name="accDate" operationMode="accomodationPackage" use="<?=$packageId?>">
					<?
					foreach($rowAccommodation as $seq=>$accPackDet)
					{
					?>
					<option value="<?=$accPackDet['STARTDATE']['DATE']?>|<?=$accPackDet['ENDDATE']['DATE']?>"><?=$accPackDet['STARTDATE']['DATE']?> to <?=$accPackDet['ENDDATE']['DATE']?></option>
					<?
					}
					?>
				</select>
				<?php
				}
				?>
				</div>
				
				<script>
					$(document).ready(function(){
						$("form[name=accmDateUpdatePopup]").submit(function(e){
							
							e.preventDefault();
						
							var theForm		= $(this);
							var dataValue 	= $(theForm).serialize();
							var action 		= $(theForm).attr('action');							
							
							$.ajax({
								type : 'POST',
								data: dataValue,
								url : action,
								success: function(data){
									alert("Data Updated.");
									window.location.reload();
								}
							}).fail(function() {
								alert("Something Wrong!! Could not update.");
							}).always(function() {
								closeUserRegistrationPopupForms(theForm);
							});							
						});
					});
				</script>
			</div>
	<?php
	}
	
	function viewOnlyWorkshopRegistration($cfg, $mycms)
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
				
				$("div[operation=userRegistrationPopupOverlay]").click(function(){
					$("div[operation=userRegistrationPopupOverlay]").hide(500);
			    	$("div[operation=userRegistrationPopup]").hide(500);
				});
			});
			
			function loadUserDetails()
			{
				if($("td[use=registrationDetailsList][dataStat=noDisplay]").length > 0)
				{
					var detailsTd = $("td[use=registrationDetailsList][dataStat=noDisplay]").first();
					var userId = $(detailsTd).attr("userId");
					
					var param = "act=getRegistrationDetails&id="+userId;
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
			
			function openCallDetailData(delegateId)
			{	
				$("div[use=callPopupForm]").find("input[type=text]").val('');
				$("div[use=callPopupForm]").find("input[type=date]").val('');
				$("div[use=callPopupForm]").find("select").val('');
				
				$("div[use=callPopupForm]").find("input[id=delegateId]").val(delegateId);
				
				$("div[use=callPopupForm]").fadeIn("slow");
			}
			
			function openUserDetailsViewPopup(obj)
			{				
				var id = $(obj).attr('userId');
								
				$.ajax({
					type: "POST",
					url: 'registration.process.php',
					data: 'act=viewDetails&delegateId='+id,
					dataType: 'html',
					async: false,
					success:function(returnMessage)
					{
						$('div[identifier=popup_profile_full_details]').html(returnMessage);						
						$('div[identifier=popup_profile_full_details]').find('span[use=closeThisPopup]').removeAttr("onClick");
						$('div[identifier=popup_profile_full_details]').find('span[use=closeThisPopup]').click(function(){
							closeUserRegistrationPopupForms(this)
						});
					}
				});
				
				openUserRegistrationPopupForms('popup_profile_full_details');
			}	
			
			function openUserRegistrationPopupForms(identifier)
			{
				 $("div[operation=userRegistrationPopupOverlay]").toggle(500);
				 $("div[identifier='"+identifier+"']").toggle(500);
			}
			
			function closeUserRegistrationPopupForms(obj)
			{
				var parent =  $(obj).parent().closest("div[operation=userRegistrationPopup]");				
				$("div[operation=userRegistrationPopupOverlay]").hide(500);
			    $("div[operation=userRegistrationPopup]").hide(500);
			}			
			
			function openCallDetailData(obj)
			{	
				var delegateId = $(obj).attr('userId');
				var identifier = 'user_call_popup_form';
				
				$("div[identifier='"+identifier+"']").find("input[type=text]").val('');
				$("div[identifier='"+identifier+"']").find("input[type=date]").val('');
				$("div[identifier='"+identifier+"']").find("select").val('');				
				$("div[identifier='"+identifier+"']").find("input[id=delegateId]").val(delegateId);				
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}
			
			function openEditNamePopup(obj)
			{
				
				var id  			= $(obj).attr('userId');
				var user_title  	= $(obj).attr('userTitle'); 
				var firstname  		= $(obj).attr('userFirstName');  
				var middlename  	= $(obj).attr('userMiddleName');   
				var lastname  		= $(obj).attr('userLastName');  
				var identifier 		= 'edit_name_popup_form';  
								
				$("div[identifier='"+identifier+"']").find('#delegateId').val(id);
				$("div[identifier='"+identifier+"']").find('#user_title').val(user_title);
				$("div[identifier='"+identifier+"']").find('#user_first_name').val(firstname);
				$("div[identifier='"+identifier+"']").find('#user_middle_name').val(middlename);
				$("div[identifier='"+identifier+"']").find('#user_last_name').val(lastname);
								
				$("div[identifier='"+identifier+"']").find('#email_status').val("");
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}
			
			function openEditEmailPopup(obj)
			{
				var id  			= $(obj).attr('userId');
				var email  			= $(obj).attr('userEmail');
				var identifier 		= 'edit_email_popup_form';  
								
				$("div[identifier='"+identifier+"']").find('#user_id').val(id);
				$("div[identifier='"+identifier+"']").find('#new_email_id').val(email);
				$("div[identifier='"+identifier+"']").find('#old_email_id').val(email);
				$("div[identifier='"+identifier+"']").find('#email_status').html("");
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this);
				});
			}
			
			function openEditMobilePopup(obj)
			{
				var id  			= $(obj).attr('userId');
				var mobile  		= $(obj).attr('userMobile');
				var isd  			= $(obj).attr('userIsd');
				var identifier 		= 'edit_mobile_popup_form';  				
				
				$("div[identifier='"+identifier+"']").find('#delegate_id').val(id);
				$("div[identifier='"+identifier+"']").find('#new_isd_code').val(isd);
				$("div[identifier='"+identifier+"']").find('#old_isd_code').val(isd);
				$("div[identifier='"+identifier+"']").find('#new_mobile_no').val(mobile);
				$("div[identifier='"+identifier+"']").find('#old_mobile_no').val(mobile);
				
				openUserRegistrationPopupForms(identifier);
				
				$("div[identifier='"+identifier+"']").find('span[use=closeThisPopup]').click(function(){
					closeUserRegistrationPopupForms(this)
				});
			}
			
			</script>
		
		<table width="100%" class="tborder" align="center">	
			<tr>
				<td class="tcat" colspan="2" align="left">
					<span style="float:left">RCOG USG tot plus (inc 3D & 4D USG)</span>
					<span class="tsearchTool" forType="tsearchTool"></span>
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
									<input type="button" name="add" value="CREATE USG TOT PLUS ACCOUNT" 
									 class="btn btn-small btn-red" onclick="window.location.href='registration.php?show=onlyWorkshopReg'" />
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
						</table>
						</form>
					</div>
							
					<table width="100%" shortData="on" >
						<thead>
							<tr class="theader">
								<td width="40" align="center" data-sort="int">Sl No</td>
								<td align="left">Name & Contact</td>
								<td width="460" align="center">Service Dtls</td>
								<td width="90" align="center">Action</td>
							</tr>
						</thead>
						<tbody>
							<?php
							
							$sqlDetails 			=	array();
							$sqlDetails['QUERY'] 	= "SELECT * FROM "._DB_USER_REGISTRATION_." WHERE status = 'A' AND `registration_request` = 'ONLYWORKSHOP'";
							$resDetails = $mycms->sql_select($sqlDetails);
							
							$idArr	= array();
							if($resDetails)
							{
								foreach($resDetails as $k=>$rowDetails)
								{
									$idArr[] = $rowDetails['id'];
								}
							}
							else
							{
								$idArr = false;
							}
							
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
									<tr class="tlisting" bgcolor="<?=$color?>" style="color:#000;">
										<td align="center" valign="top" style="border-bottom:thin dashed #0a5372;">
											<?=$counter + ($_REQUEST['_pgn1_']*10)?><br />											
											<span style="color:#cc0000; cursor:default;" title="USG TOT PLUS"><b>TOT</b></span><br />
											<?
											if($rowFetchUser['user_gender']=='Male')
											{
											?>
											<i class="fa fa-male" aria-hidden="true" style="color:#FF0000;" title="Male"></i>
											<?
											}
											else
											{
											?>
											<i class="fa fa-female" aria-hidden="true" style="color:#660099;" title="Female"></i>
											<?
											}
											?>											
										</td>
										<td align="left" valign="top" style="border-bottom:thin dashed #0a5372;">
											<i class="fa fa-user-md" aria-hidden="true" title="User"></i>&nbsp;<b><?=strtoupper($rowFetchUser['user_full_name'])?></b> 
											<br />
											<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$rowFetchUser['user_mobile_no']?>
											<br />
											<i class="fa fa-envelope" aria-hidden="true" title="Email"></i>&nbsp;<?=$rowFetchUser['user_email_id']?>
											<br />
											<i class="fa fa-clock-o" aria-hidden="true" title="Registration Date"></i>&nbsp;<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
										</td>	
										<td align="left" valign="top" colspan="2" use="registrationDetailsList" userId="<?=$rowFetchUser['id']?>" style="padding:0;border-bottom:thin dashed #0a5372;">
											<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" />
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
		
		<div class="overlay" id="fade_popup" operation="userRegistrationPopupOverlay"></div>
		
		<div class="popup_form" identifier="popup_profile_full_details" operation="userRegistrationPopup"></div>
		
		<div class="popup_form2" identifier="user_call_popup_form" use="callPopupForm" operation="userRegistrationPopup">
			<form action="<?=_BASE_URL_?>webmaster/section_registration/registration.process.php" name="callUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return submitCallDetails();">
				<input type="hidden" name="act" value="addCallDetails" />	
				<input type="hidden" name="delegateId" id="delegateId">
				<input type="hidden" name="participantId" id="participantId">
				<?
				foreach($searchArray as $key=>$value)
				{
				?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>">
				<?
				}
				?>
				<table width="100%" class="tborder">
					<tr>
						<td colspan="2" class="tcat">
							<span style="float:left" >Call Records Entry Screen</span>
							<span class="close" forType="tsearchTool" use="closeThisPopup" >X</span>
						</td>
					</tr>
					<tr>
						<td align="left" width="140"><b>Call DateTime</b></td>
						<td valign="top">
							<input type="date" name="callDate" id="callDate" value= "<?php echo "date(Y/m/d)"?>" style="width:35%" placeholder="Choose Date" required />&nbsp; -&nbsp; 
							<span rel="timeChooser">
								<input type="number" name="callTimeHour" id="callTimeHour" min="0" max="23" specific="hour" value="<?=date('h')?>" style="width:15%;  text-align:center;" required/> : 
								 
								<input type="number" name="callTimeMin" id="callTimeMin" min="0" max="59" specific="min" value="<?=date('i')?>" style="width:15%;   text-align:center;"required/>
							</span>
						</td>
					</tr>
					<tr>
						<td align="left" width="140"><b>Subject</b></td>	
						<td align="left" width="140">
							<select name="call_subject" id="call_subject" required>
								<option value="">-- Select --</option>
								<option value="REGISTRATION">Registration</option>
								<option value="WORKSHOP">Workshop</option>
								<option value="ACCOMPANY">Accompany</option>
								<option value="ACCOMMODATION">Accommodation</option>
								<option value="PICKUP-DROP">Pick Up / Drop Off</option>
								<option value="DINNER">Dinner</option>
								<option value="ABSTRACT">Abstract</option>
								<option value="SCIENTIFIC-PROGRAM">Scientific Program</option>
								<option value="OTHERS">Others</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" width="140"><b>Discussion</b></td>	
						<td width="22%" align="left" valign="top"><textarea name="call_contents" id="call_contents" style="width:440px; height:100px; text-transform:uppercase;" required /></textarea>
						<br>
							<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
				</table>		
			</form>
			<script>
				$(document).ready(function(){
					$("form[name=callUpdatePopup]").submit(function(e){
						
						e.preventDefault();
					
						var theForm		= $(this);
						var dataValue 	= $(theForm).serialize();
						var action 		= $(theForm).attr('action');							
						
						$.ajax({
							type : 'POST',
							data: dataValue,
							url : action,
							success: function(data){alert("Data Updated.");}
						}).fail(function() {
							alert("Something Wrong!! Could not update.");
						}).always(function() {
							closeUserRegistrationPopupForms(theForm);
						});							
					});
				});
			</script>
		</div>
		
		<div class="popup_form" identifier="edit_name_popup_form" operation="userRegistrationPopup">
			<form action="edit_userDetails.process.php" name="nameUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return formNameValidation();">
				<input type="hidden" name="act" id="name_update" value="name_update" />
				<input type="hidden" name="delegateId" id="delegateId" value=""/>
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<table width="100%" class="tborder">
					<tr>
						<td colspan="5" class="tcat">
							<span style="float:left">Name Update</span>
							<span class="close" forType="tsearchTool" use='closeThisPopup'>&times;</span>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Delegate Name:</td>
						<td width="22%" align="left"><input type="text" name="user_first_name" id="user_first_name" style="width:95%; text-transform:uppercase;" value="<?=$_REQUEST['user_full_name']?>"/></td>
						<td width="22%" align="left"><input type="text" name="user_middle_name" id="user_middle_name" style="width:95%; text-transform:uppercase;" value=""/></td>
						<td width="22%" align="left"><input type="text" name="user_last_name" id="user_last_name" style="width:95%; text-transform:uppercase;" value=""/></td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 20px;" /></td>
					</tr>
					<tr>
						<td colspan="5"></td>
					</tr>
				</table>		
			</form>
			<script>
				$(document).ready(function(){
					$("form[name=nameUpdatePopup]").submit(function(e){
						
						e.preventDefault();
					
						var theForm		= $(this);
						var dataValue 	= $(theForm).serialize();
						var action 		= $(theForm).attr('action');							
						
						$.ajax({
							type : 'POST',
							data: dataValue,
							url : action,
							success: function(data){
								alert("Data Updated.");
								window.location.reload();
							}
						}).fail(function() {
							alert("Something Wrong!! Could not update.");
						}).always(function() {
							closeUserRegistrationPopupForms(theForm);
						});							
					});
				});
				
				function formNameValidation()
				{
					var identifier 		= 'edit_name_popup_form';  
					
					if(fieldNotEmpty($("div[identifier='"+identifier+"']").find('#user_first_name'), "Please First Name") == false){ 
						return false; 
					}
					return true;			
				}	
		
			</script>
		</div>
		
		<div class="popup_form" identifier="edit_email_popup_form" operation="userRegistrationPopup">
			<form action="edit_userDetails.process.php" name="emailUpdatePopup" id="emailUpdatePopup" method="post" onsubmit="return formEmailValidation();">
				<input type="hidden" name="act" id="email_update" value="email_update" />
				<input type="hidden" name="user_id" id="user_id" />
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<input type="hidden" name="email_id_validation" id="email_id_validation" value="" />
				<input type="hidden" name="old_email_id" id="old_email_id" value="" />
				<table width="100%" class="tborder">
					<tr>
						<td colspan="4" class="tcat">
							<span style="float:left">Email Update</span>
							<span class="close" forType="tsearchTool" use='closeThisPopup'>&times;</span>
						</td>
					</tr>
					<tr>
						<td width="15%" align="left">Email Id:</td>
						<td width="35%" align="right"><input type="text" id="new_email_id" name="new_email_id" style="width:100%" /></td>
						<td colspan="2" align="left"><div id="email_status"></div></td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="right"><input type="submit" class="ticket ticket-important" value="Update" /></td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>		
			</form>
			
			<script>
				$(document).ready(function(){
					$("div[identifier=edit_email_popup_form]").find('#new_email_id').blur(function(){
						checkEmailValidation($("div[identifier=edit_email_popup_form]").find('#new_email_id'));
					});
					
					$("form[name=emailUpdatePopup]").submit(function(e){							
						
						e.preventDefault();						
						
						var theForm		= $(this);
						var dataValue 	= $(theForm).serialize();
						var action 		= $(theForm).attr('action');							
						
						$.ajax({
							type : 'POST',
							data: dataValue,
							url : action,
							success: function(data){
								alert("Data Updated.");
								window.location.reload();
							}
						}).fail(function() {
							alert("Something Wrong!! Could not update.");
						}).always(function() {
							closeUserRegistrationPopupForms(theForm);
						});							
					});
				});
				
				function checkEmailValidation(emailControl)
				{
					var identifier 	  = 'edit_email_popup_form';  				   
					var user_email    = $(emailControl).val();
					
					$("div[identifier='"+identifier+"']").find('#email_id_validation').val("");
					$("div[identifier='"+identifier+"']").find('#email_status').html("");
					
					if(user_email!="")
					{
						if(regularExpressionEmail.test($(emailControl).val())==false)
						{
							$("div[identifier='"+identifier+"']").find('#email_status').html('<span style="color:#D41000">Invalid Email Id</span>');
							$("div[identifier='"+identifier+"']").find('#email_id_validation').val('INVALID');
							return false;
						}
						else
						{
							$.ajax({
								type: "POST",
								url: 'edit_userDetails.process.php',
								data: 'act=getEmailValidation&email='+user_email,
								dataType: 'text',
								async: false,
								success:function(returnMessage)
								{
								console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/edit_userDetails.process.php?act=getEmailValidation&email='+user_email);
									returnMessage = returnMessage.trim();
									if(returnMessage == 'IN_USE')
									{
										//$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
										var successContent   = '<div style="font-size:16px; text-align:center;">This Email ID Already Registered.</div>';
											successContent  += '<div style="color:#FF0000; font-size:15px; text-align:center;">Please Use Another Email ID.</div>';
										
										$("div[identifier='"+identifier+"']").find('#email_status').html(successContent);
									}
									else
									{
										$("div[identifier='"+identifier+"']").find('#email_status').html('<span style="color:#009933">Available</span>');
									}
									$("div[identifier='"+identifier+"']").find('#email_id_validation').val(returnMessage);
								}
							});
						}
					}
				}
				
				function formEmailValidation()
				{
					var identifier 		= 'edit_email_popup_form';  
					
					if($("div[identifier='"+identifier+"']").find('#email_id_validation').val()=="IN_USE"){
						
						$("div[identifier='"+identifier+"']").find('#new_email_id').focus();
						$("div[identifier='"+identifier+"']").find('#new_email_id').css('border-color','#D41000');
						alert("Email Id Already In Use");
						return false;
					}
					
					if(fieldNotEmpty($("div[identifier='"+identifier+"']").find('#new_email_id'), "Please Enter Valid Email Id") == false){ 
						return false; 
					}
					
					if(fieldShouldEmailValidate($("div[identifier='"+identifier+"']").find('#new_email_id'), "Please Provide Valid Email Id") == false){ 
						return false; 
					}
				}					
			</script>
		</div>
		
		<div class="popup_form" identifier="edit_mobile_popup_form" operation="userRegistrationPopup">
			<form action="edit_userDetails.process.php" name="mobileUpdatePopup" id="mobileUpdatePopup" method="post" onsubmit="return formMobileValidation();">
				<input type="hidden" name="act" id="mobile_update" value="mobile_update" />
				<input type="hidden" name="delegate_id" id="delegate_id"/>
				<input type="hidden" name="old_isd_code" id="old_isd_code" value="" />
				<input type="hidden" name="old_mobile_no" id="old_mobile_no" value="" />				
				<input type="hidden" name="mobile_id_validation" id="mobile_id_validation" value="" />				
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<table width="100%" class="tborder">
					<tr>
						<td colspan="4" class="tcat">
							<span style="float:left">Mobile No Update</span>
							<span class="close" forType="tsearchTool" use='closeThisPopup'>&times;</span>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Mobile No:</td>
						<td width="10%" align="right">
							<input type="text" id="new_isd_code" name="new_isd_code" forType="new_isd_code" style="width:30%"/>
						</td>
						<td width="30%" align="left">
							<input type="text" id="new_mobile_no" name="new_mobile_no" forType="new_mobile_no"/> 
							<input type="hidden" name="mobile_validation" id="mobile_validation" />
						</td>
						<td><div id="mobile_div"></div></td>
						
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="right"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 20px;" /></td>
						<td align="left">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>		
			</form>
			<script>				
				$(document).ready(function(){
					$("form[name=mobileUpdatePopup]").submit(function(e){
						
						e.preventDefault();
					
						var theForm		= $(this);
						var dataValue 	= $(theForm).serialize();
						var action 		= $(theForm).attr('action');							
						
						$.ajax({
							type : 'POST',
							data: dataValue,
							url : action,
							success: function(data){
								alert("Data Updated.");
								window.location.reload();
							}
						}).fail(function() {
							alert("Something Wrong!! Could not update.");
						}).always(function() {
							closeUserRegistrationPopupForms(theForm);
						});							
					});
				});
			
				function formMobileValidation()
				{
					var identifier 		= 'edit_mobile_popup_form';  
					
					if(fieldNotEmpty($("div[identifier='"+identifier+"']").find('#new_mobile_no,#new_isd_code'), "Please Enter Mobile No") == false){ 
						return false; 
					}
					
					if(isNaN($("div[identifier='"+identifier+"']").find('#new_mobile_no').val()))
					{
						$("div[identifier='"+identifier+"']").find('#new_mobile_no').focus();
						$("div[identifier='"+identifier+"']").find('#new_mobile_no').css('border-color','#D41000');
						$("div[identifier='"+identifier+"']").find('#new_mobile_no').val("");
						alert("Invalid Mobile Number");
						return false;
					}
					
					var mobile_no = $("div[identifier='"+identifier+"']").find('#new_mobile_no').val();
					
					if( mobile_no.length > 10 || mobile_no.length < 10 )
					{
						alert("Mobile Number must be 10 digits");
						return false;
					}	
				}
				
				function checkMobileValidation(mobileControl)
				{
					var identifier 		= 'edit_mobile_popup_form';  
					var user_mobile_no 	= $(mobileControl).val();
					$("div[identifier='"+identifier+"']").find('#mobile_id_validation').val('');
					if(user_mobile_no!="")
					{
						$.ajax({
							type: "POST",
							url: "edit_userDetails.process.php",
							data: "act=getMobileValidation&mobile="+user_mobile_no,
							dataType: "text",
							async: false,
							success:function(returnMessage){
								console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/edit_userDetails.process.php?act=getMobileValidation&mobile='+user_mobile_no);
								returnMessage = returnMessage.trim();
								if(returnMessage == 'IN_USE')
								{
									//$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
									var successContent   = '<div style="color:#FF0000";font-size:16px; text-align:center;">This Mobile No Already Registered.</div>';
									
									$("div[identifier='"+identifier+"']").find('#mobile_div').html(successContent);
								}
								else
								{
									$("div[identifier='"+identifier+"']").find('#mobile_div').html('<span style="color:#009933">Available</span>');
								}
								$("div[identifier='"+identifier+"']").find('#mobile_id_validation').val(returnMessage);
							}
						});
					}
				}
			</script>
		</div>
		
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
		
		$conferenceTariffArray   = getAllRegistrationTariffs("",false);
		
		$workshopDetailsArray 	 = getAllWorkshopTariffs();
		$workshopCountArr 		 = totalWorkshopCountReport();	
		
		$userREGtype           	 = $_REQUEST['userREGtype'];
		$abstractDelegateId      = $_REQUEST['delegateId'];
		$userRec 		         = getUserDetails($abstractDelegateId);
		
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
			<?
			if($registrationRequest=='ONLY-WORKSHOP')
			{
			?>
			<input type="hidden" name="act2nd" value="onlyWorkshopReg" />
			<?
			}
			elseif($userREGtype=='Abstract')
			{
			?>
			<input type="hidden" name="act2nd" value="registerAbstractUser" />
			<?
			}
			?>			
			<input type="hidden" name="reg_area" value="BACK" />
			<input type="hidden" name="reg_type" value="BACK" />
			<input type="hidden" name="userREGtype" value="<?=$_REQUEST['userREGtype']?>" />
			<input type="hidden" name="abstractDelegateId" value="<?=$_REQUEST['delegateId']?>" />
			<input type="hidden" name="registration_request" id="registration_request" value="<?=$registrationRequest?>" />
			<?php 
			if($_REQUEST['COUNTER']=='Y')
			{ 
			?>
			<input type="hidden" name="counter" id="counter" value="Y" />
			<?php 
			} 
			
			if($_REQUEST['delegateId']!='' && trim($_REQUEST['delegateId']) > 0)
			{
			?>
			<script>
			$(document).ready(function(){
				$("#user_email_id").prop("readonly",true);
				$("#email_id_validation").val("AVAILABLE");
				$("#user_mobile_isd_code").prop("readonly",true);
				$("#user_mobile_no").prop("readonly",true);
				$("#mobile_validation").val("AVAILABLE");
				$("#mobile_validation").val("AVAILABLE");
			});
			</script>
			<?
			}
			?>	
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
									<td colspan="4" align="left">Conference <? echo $cfg['EMAIL_CONF_HELD_FROM']?></td>
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
								<?
								if($registrationRequest=='ONLY-WORKSHOP')
								{
								?>	
								<tr style="display:none;">
									<td colspan="4" align="left">
										
										<input type="checkbox" name="registration_classification_id[]" operationMode="registration_tariff" 
											   value="1" currency="INR" checked="checked"
											   registrationType="GENERAL" accommodationPackageId = "0"/>
										<input type="checkbox" operationMode='workshopId'  name="workshop_idxx" id="workshop_id" value="0" checked="checked"/>
															
									</td>
								</tr>
								<?
								}
								else
								{
								?>					
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
								<?
								}
								?>
							</table>
							
							<table width="100%">
								<tr>
									<td width="20%" align="left" valign="top">Workshop Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
									<?
									if($registrationRequest=='ONLY-WORKSHOP')
									{
									?>	
										<table width="100%">
											 <tr class="theader">
												<td align="left">POST CONGRESS WORKSHOP</td>
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
														if($rowRegClasf[1]['WORKSHOP_TYPE']=='POST-CONFERENCE' && $keyRegClasf == '1' && in_array($keyWorkshopclsf,$cfg['INDEPENDANT.WORKSHOPS']))
														{
														
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
														
															/*if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															else*/
															{
																 $style = '';
																 $span	= '';
															}
														?>
														 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr">
															<td align="left">
																<div class="tooltip">
																	<?=$span?>
																	<input type="checkbox" operationMode='workshopId_postConference' <?=$style?> name="workshop_id[]" id="workshop_id" value="<?=$keyWorkshopclsf?>" />
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
											  }
											 ?>		
										</table>
									<?
									}
									else
									{
									?>	
										<table width="100%">
											<tr class="theader">
												<td align="left">WORKSHOP / MASTER CLASS</td>
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
														//echo '<!--'; print_r($rowRegClasf); echo '-->';
														if($rowRegClasf[1]['WORKSHOP_TYPE']!='POST-CONFERENCE')
														{
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
														
															/*if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															else*/
															{
																 $style = '';
																 $span	= '';
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
											  }
											 ?>	
											 <tr use="na" operetionMode="workshopTariffTr">
												<td align="center" colspan="<?=sizeof($cutoffArray)+1?>"><strong style="color:#FF0000;">Please Select Registration Classification First</strong></td>
											 </tr>
										</table>
										<table width="100%">
											 <tr class="theader">
												<td align="left">POST CONGRESS WORKSHOP</td>
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
														if($rowRegClasf[1]['WORKSHOP_TYPE']=='POST-CONFERENCE')
														{
														
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
														
															/*if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															else*/
															{
																 $style = '';
																 $span	= '';
															}
														?>
														 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
															<td align="left">
																<div class="tooltip">
																	<?=$span?>
																	<input type="checkbox" operationMode='workshopId_postConference' <?=$style?> name="workshop_id[]" id="workshop_id" value="<?=$keyWorkshopclsf?>" />
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
									<?
									}
									?>
									</td>
								</tr>
								<tr>
									<td colspan="4" align="left">&nbsp;</td>
								</tr>
							</table>	
							<?
							if($registrationRequest!='ONLY-WORKSHOP')
							{
							?>							
							<table width="100%">
								<tr>
									<td width="20%" align="left" valign="top">Dinner</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
										<table width="100%">
											<tr class="theader">
												<td colspan=2>Dinner Classification</td>
												<?
												foreach($cutoffArray as $cutoffId=>$cutoffName)
												{	
													if($cutoffId !='4') 
													{
												?>
													<td align="right" style="width: 200px;"><?=strip_tags($cutoffName)?></td>
												<?
													}
												}
												?>
											</tr>
											<?
											$dinnerTariffArray   = getAllDinnerTarrifDetails();
											
											echo '<!--'; print_r($dinnerTariffArray); echo '-->'; 
							
											foreach($dinnerTariffArray as $keyDinner=>$dinnerValues)
											{
												
											?>
											<tr use="PaidDinners" operetionMode="dinnerTariffTr"  align="center">
												<td align="left"  width="5%" valign="top"> 
													<input type="checkbox" name="dinner_value[]" id="dinner_value" 
														   value="<?=$keyDinner?>" operationMode="dinner"  
														   tariffAmount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"/>
												</td>
												<td width="20%" align="left" valign="top"> 
													<?=$dinnerValues[$currentCutoffId]['DINNER_TITTLE']?> 
												</td>
											<?
												foreach($dinnerValues as $dinnerCutof=>$dinnerValue)
												{
											?>
												<td align="right" use="dinnerTariff" cutoff="<?=$dinnerCutof?>"  tariffAmount="<?=$dinnerValue['AMOUNT']?>">INR <?=number_format(floatval($dinnerValue['AMOUNT']),2)?></td>
											<?
												}
											?>
											</tr>
											<tr use="FreeDinners" operetionMode="dinnerTariffTr"  align="center" style="display:none;">
												<td align="left" valign="top">
													<i class="fa fa-check-square-o" aria-hidden="true"></i>
												</td>
												<td align="left" valign="top"> 
												   	<?=$dinnerValues[$currentCutoffId]['DINNER_TITTLE']?> 
												</td>
											<?
												foreach($dinnerValues as $dinnerCutof=>$dinnerValue)
												{
											?>
												<td align="right" use="dinnerTariff" cutoff="<?=$dinnerCutof?>"  tariffAmount="0.00">Included in package</td>	
											<?
												}
											?>
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
							<?
							}
							?>
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
										<input type="email" name="user_email_id" id="user_email_id" forType="emailValidate" style="width:90%; text-transform:lowercase;" 
											   value="<?=($userRec['user_email_id']!='')?($userRec['user_email_id']):''?>"
											   tabindex="1" required/>
										<input type="hidden" name="email_id_validation" id="email_id_validation" />
										<div id="email_div"></div>
									</td>
									<td align="left" width="20%">
										Mobile No
										<span class="mandatory">*</span>
									</td>
									<td align="left" width="30%" >
										<input type="tel" name="user_usd_code" id="user_mobile_isd_code" style="width:30px; text-align:right;" tabindex="2" 
											   value="<?=($userRec['user_mobile_isd_code']!='')?($userRec['user_mobile_isd_code']):'+91'?>" required/> - 
										<input type="tel" name="user_mobile" id="user_mobile_no" forType="mobileValidate" style="width:70%;" pattern="^\d{10}$" tabindex="3" 
											   value="<?=($userRec['user_mobile_no']!='')?($userRec['user_mobile_no']):''?>" required/>
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
										 		  style="height:75px; width:90%; text-transform:uppercase;"><?=($userRec['user_address']!='')?($userRec['user_address']):''?></textarea>
									</td>
								</tr>
								<tr>
									<td align="left">First Name <span class="mandatory">*</span></td>
									<td align="left">
										<input type="text" name="user_first_name" id="user_first_name" 
										 	   style="width:90%; text-transform:uppercase;" tabindex="5" 
											   value="<?=($userRec['user_first_name']!='')?($userRec['user_first_name']):''?>"
											   required />
										 
									</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Middle Name</td>
									<td align="left">
										<input type="text" name="user_middle_name" id="user_middle_name" 
										 	   style="width:90%; text-transform:uppercase;"  
											   value="<?=($userRec['user_middle_name']!='')?($userRec['user_middle_name']):''?>"
											   implementvalidate="y" tabindex="6" />
									</td>
									<td align="left">Country</td>
									<td align="left">
										<select required implementvalidate="y" name="user_country" id="user_country" style="width:90%;" forType="countryState"  
												stateId="user_state" onchange="stateRetriver(this);" tabindex="11"
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
													<option value="<?=$rowFetchUserCountry['country_id']?>" <?=($rowFetchUserCountry['country_id']==$userRec['user_country_id'])?'selected':''?>><?=$rowFetchUserCountry['country_name']?></option>
											<?php
												}
											}
											?>
											<? //getCountryList("1") ?>
										</select>
										<?
										if($userRec['user_country_id']!='')
										{
										?>
										<script>
											$(document).ready(function(){
												jBaseUrl = jsBASE_URL;
												generateSateList(<?=$userRec['user_country_id']?>,jBaseUrl);
												$('#user_state option[value="<?=$userRec['user_state_id']?>"]').prop('selected', true);
											});
										</script>
										<?php
										}
										?>
									</td>
								</tr>
								<tr>
									<td align="left">Last Name</td>
									<td align="left">
										<input type="text" name="user_last_name" id="user_last_name" tabindex="7"
										 	   style="width:90%; text-transform:uppercase;" 
										  	   value="<?=($userRec['user_last_name']!='')?($userRec['user_last_name']):''?>"
										 	   required implementvalidate="y"/>
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
										 style="width:90%; text-transform:uppercase;" 
										 value="<?=($userRec['user_city']!='')?($userRec['user_city']):''?>"
										 required implementvalidate="y"/>
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
											   value="<?=($userRec['user_pincode']!='')?($userRec['user_pincode']):''?>"
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
		include_once('../../includes/function.registration.php');
		
		$cutoffArray  		   = array();	
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
			$registrationDetails 	= getAllRegistrationTariffs();
			$registrationAmount 	= $registrationDetails[$accompanyCatagory][$delagateCutoff]['AMOUNT'];
			$dinnerTariffArray   	= getAllDinnerTarrifDetails();
			$dinnerAmmount			= $dinnerTariffArray[2][$delagateCutoff]['AMOUNT'];
			?>
			<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
			<input type="hidden" name="dinnerTariffAmount" id="dinnerTariffAmount" value="<?=$dinnerAmmount?>" />
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
		addMoreAccompanyTemplate("add",$delagateCutoff);
	}
		
	function registrationSummeryTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
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
			<input type="hidden" name="pendingAmount" value="<?=pendingAmountOfSlip($slipId);?>" />
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
				var pendingSlipAmount  = $("input[type=hidden][name=pendingAmount]").val();
				
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
				if(parseInt(totalAmount) > parseInt(pendingSlipAmount))
				{
					alert("input amount should be less than slip amount");
					$(obj).val('');
					$(obj).focus();
				}
				$("span[use=pending_amount]").text((parseInt(pendingSlipAmount)-parseInt(totalAmount)).toFixed(2));
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
		global $cfg, $mycms;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.workshop.php');
		include_once('../../includes/function.dinner.php');
		
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
														//if($loggedUserId == 1 )
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
			<?php 
			if($_REQUEST['COUNTER']=='Y')
			{ 
			?>
				<input type="hidden" name="counter" id="counter" value="Y" />
			<?php 
			} 
			if($_REQUEST['act2nd']!='')
			{
			?>
				<input type="hidden" name="act" value="<?=$_REQUEST['act2nd']?>" />
				<input type="hidden" name="abstractDelegateId" value="<?=$_REQUEST['abstractDelegateId']?>" />
			<?php
			}
			else
			{
			?>
				<input type="hidden" name="act" value="step6" />
			<?
			}
			?>
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
												 FROM "._DB_CANCEL_INVOICE_." 
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
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A' AND `id`  NOT IN (3,6)";
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
	
	function registrationWorkshopTemplate($requestPage, $processPage, $registrationRequest="GENERAL", $isComplementary="")
	{
		global $cfg, $mycms;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
		include_once('../../includes/function.workshop.php');
		
		$delegateId 			= $_REQUEST['id'];
		$spotUser				= $_REQUEST['userREGtype'];	
		$rowFetchUser   		= getUserDetails($delegateId);
		$delagateCatagory 		= getUserClassificationId($delegateId);
			
		$cutoffArray  			= array();
		$sqlCutoff['QUERY']    	= " SELECT * 
									  FROM "._DB_TARIFF_CUTOFF_." 
									 WHERE `status` != ? 
								  ORDER BY `cutoff_sequence` ASC";		
		$sqlCutoff['PARAM'][]  	= array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');												  
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
		
		
		$workShopOfDelegate 	 = getWorkshopDetailsOfDelegate($delegateId);
		$selectedWorkshops 		 = array();	
		foreach($workShopOfDelegate as $lk=>$selWrkShp)
		{
			$selectedWorkshops[] = $selWrkShp['workshop_id'];
		}		
		?>
		<script type="text/javascript" language="javascript" src="scripts/workshop_registration.js"></script>
		<form name="frmApplyForworkshop" id="frmApplyForworkshop"  action="<?=$processPage?>" method="post" onsubmit="return validateWorkshop();">
			<input type="hidden" name="act" value="applyWorkshop" />
			<input type="hidden" name="delegate_id" value="<?=$delegateId?>" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
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
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Conference <? echo $cfg['EMAIL_CONF_HELD_FROM']?></td>
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
							</table>
							
							<table width="100%">
								<tr>
									<td width="20%" align="left" valign="top">Workshop Tariff</td>
									<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">										
										<table width="100%">
											 <tr class="theader">
												<td align="left">POST CONGRESS WORKSHOP</td>
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
														if($rowRegClasf[1]['WORKSHOP_TYPE']=='POST-CONFERENCE')
														{
														
															if($keyRegClasf==$delagateCatagory)
															{
															
																$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
															
																/*if($workshopCount<1)
																{
																	 $style = 'disabled="disabled"';
																	 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
																}
																else*/
																if(in_array($keyWorkshopclsf,$selectedWorkshops))
																{
																	 $style = 'disabled="disabled"';
																	 $span	= '<span class="tooltiptext">Already selected This Workshop</span>';
																}
																else
																{
																	 $style = '';
																	 $span	= '';
																}
														?>
														 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr">
															<td align="left">
																<div class="tooltip">
																	<?=$span?>
																	<input type="checkbox" operationMode='workshopId_postConference' <?=$style?> name="workshop_id[]" id="workshop_id" value="<?=$keyWorkshopclsf?>" />
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
												 }
											  }
											 ?>	
											<tr use="na" operetionMode="workshopTariffTr" style="display:none;">
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
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	
	function editReallocationOfMasterWorkshop()
	{   
		global $cfg, $mycms, $searchArray, $searchString;
		
		$delegateId 	    =  $_REQUEST['id'];
		$requestWorkshop 	=  $_REQUEST['requestWorkshop'];
		$invoiceId 	        =  $_REQUEST['invoiceId'];
		
		$loggedUserId	    = $mycms->getLoggedUserId();
		$rowFetchUser       = getUserDetails($delegateId);
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
						
						<form action="registration.process.php" method="post">
						<input type="hidden" name="act" value="editReallocationOfMasterWorkshop" />
						<input type="hidden" name="delegateId" value="<?=$delegateId?>" />
						<?php
						foreach($searchArray as $key=>$val)
						{
						?>
							<input type="hidden" name="<?=$key?>" id="<?=$key?>" value="<?=$val?>" />
						<?php
						}
						?>		
							<table width="100%">
								<tr class="thighlight">
									<td colspan="2" align="left"> 
									Re-allocate Workshop
									</td>
								</tr>
								<tr class="theader"  use="all">
									<td width="50%" align="left">Old Workshop</td>
									<td width="50%" align="left">New Workshop</td>
								</tr>
								<?php
									$workShopOfDelegate = getWorkshopDetailsOfDelegate($delegateId);
									$selectedWorkshops = array();	
									foreach($workShopOfDelegate as $lk=>$selWrkShp)
									{
										$selectedWorkshops[] = $selWrkShp['workshop_id'];
									}	
								?>
								<tr class="tlisting" use="all">
									<td valign="top">
									<table style="width:100%;">
									<?php
									 $workshopDetails = getAllWorkshopTariffs();
									 $workshopCountArr = totalWorkshopCountReport();											
									 if(sizeof($workshopDetails)>0)
									 {
										 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
										 {
											foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
											{
												if($rowRegClasf[1]['WORKSHOP_TYPE']!='POST-CONFERENCE')
												{
													if($keyRegClasf==$rowFetchUser['registration_classification_id'])
													{
													?>
													 <tr use="<?=$keyRegClasf?>" >
														<td align="left">
														<?
														if(in_array($keyWorkshopclsf,$selectedWorkshops))
														{
														?>
														<i class="fa fa-check-square-o" aria-hidden="true"></i>
														<?
														}
														else
														{
														?>
														<i class="fa fa-square-o" aria-hidden="true"></i>
														<?
														}
														?>	
														</td>
														<td align="left">&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?></td>
													 </tr>
													<? 
													}
												}
											}
										 }
									  }
									?>	
									</table>
									</td>
									<td align="left">
										<table style="width:100%;">
										<?php
											 $workshopDetails = getAllWorkshopTariffs();
											 $workshopCountArr = totalWorkshopCountReport();											
											 if(sizeof($workshopDetails)>0)
											 {
												 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
												 {
													foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
													{
														if($rowRegClasf[1]['WORKSHOP_TYPE']!='POST-CONFERENCE')
														{
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
															
															$style 	= '';
															$span	= '';
															
															if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															elseif(in_array($keyWorkshopclsf,$selectedWorkshops))
															{
																$style 	= 'disabled="disabled"';
																$span	= '<span class="tooltiptext">You have taken this</span>';
															}
															
															foreach($rowRegClasf as $keyCutoff=>$sessionType)
															{
																$sessiontype = $sessionType['WORKSHOP_TYPE'];
																$workshopName = $sessionType['WORKSHOP_GRP'];
															}
															
															if($keyRegClasf==$rowFetchUser['registration_classification_id'])
															{
															?>
															 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" >
																<td align="left">
																	<div class="tooltip">
																		<?=$span?>
																		<input type="radio"  <?=$style?> name="workshop_id[]" id="workshop_id" operationMode="Workshopclsf" workshopName="<?=$workshopName?>" opmode="workshopId" value="<?=$keyWorkshopclsf?>" 
																			   style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" />
																	</div>
																</td>
																<td align="left">&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?></td>
															 </tr>
															<? 
															}
														}
													}
												 }
											  }
											 ?>	
										</table>	
									</td>
								</tr>
								<tr>
									<td></td>
									<td><input type="submit" valus="Submit" class="btn btn-large btn-blue" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>				
			</tbody> 
		</table>
	<?
	}
	
	function editReallocationOfWorkshop()
	{   
		global $cfg, $mycms, $searchArray, $searchString;
		
		$delegateId 	    =  $_REQUEST['id'];
		$requestWorkshop 	=  $_REQUEST['requestWorkshop'];
		$invoiceId 	        =  $_REQUEST['invoiceId'];
		
		$loggedUserId	    = $mycms->getLoggedUserId();
		$rowFetchUser       = getUserDetails($delegateId);
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
						
						<form action="registration.process.php" method="post">
						<input type="hidden" name="act" value="editReallocationOfWorkshop" />
						<input type="hidden" name="delegateId" value="<?=$delegateId?>" />
						<?php
						foreach($searchArray as $key=>$val)
						{
						?>
							<input type="hidden" name="<?=$key?>" id="<?=$key?>" value="<?=$val?>" />
						<?php
						}
						?>		
							<table width="100%">
								<tr class="thighlight">
									<td colspan="2" align="left"> 
									Re-allocate Workshop
									</td>
								</tr>
								<tr class="theader"  use="all">
									<td width="50%" align="left">Old Workshop</td>
									<td width="50%" align="left">New Workshop</td>
								</tr>
								<?php
									$workShopOfDelegate = getWorkshopDetailsOfDelegate($delegateId);
									$selectedWorkshops = array();	
									foreach($workShopOfDelegate as $lk=>$selWrkShp)
									{
										$selectedWorkshops[] 							= $selWrkShp['workshop_id'];
										$wrkshopInvoiceId[$selWrkShp['workshop_id']]	= $selWrkShp['refference_invoice_id'];				
									}
								?>
								<tr class="tlisting" use="all">
									<td valign="top">
									<table style="width:100%;">
									<?php
									 $workshopDetails = getAllWorkshopTariffs();
									 $workshopCountArr = totalWorkshopCountReport();											
									 if(sizeof($workshopDetails)>0)
									 {
										 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
										 {
											foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
											{
												if($rowRegClasf[1]['WORKSHOP_TYPE']=='POST-CONFERENCE')
												{
													if($keyRegClasf==$rowFetchUser['registration_classification_id'])
													{
													?>
													 <tr use="<?=$keyRegClasf?>" >
														<td align="left">
														<?
														if(in_array($keyWorkshopclsf,$selectedWorkshops))
														{
														?>
														<input type="hidden" name="invoiceId[]" value="<?=$wrkshopInvoiceId[$keyWorkshopclsf]?>" />
														<i class="fa fa-check-square-o" aria-hidden="true"></i>
														<?
														}
														else
														{
														?>
														<i class="fa fa-square-o" aria-hidden="true"></i>
														<?
														}
														?>	
														</td>
														<td align="left">&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?></td>
													 </tr>
													<? 
													}
												}
											}
										 }
									  }
									?>	
									</table>
									<?php /*?>
									<input type="checkbox" checked="checked" disabled="disabled" />
									<?=$workshopName?>
									<?php */?>
									</td>
									<td align="left">
										<table style="width:100%;">
										<?php
											 $workshopDetails = getAllWorkshopTariffs();
											 $workshopCountArr = totalWorkshopCountReport();											
											 if(sizeof($workshopDetails)>0)
											 {
												 foreach($workshopDetails as $keyWorkshopclsf=>$rowWorkshopclsf)
												 {
													foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
													{
														if($rowRegClasf[1]['WORKSHOP_TYPE']=='POST-CONFERENCE'  && !in_array($keyWorkshopclsf, $cfg['INDEPENDANT.WORKSHOPS']))
														{
															$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
											
															$style 	= '';
															$span	= '';
															/*if($workshopCount<1)
															{
																 $style = 'disabled="disabled"';
																 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
															}
															else*/
															if(in_array($keyWorkshopclsf,$selectedWorkshops))
															{
																$style 	= 'disabled="disabled"';
																$span	= '<span class="tooltiptext">You have taken this</span>';
															}
															
															foreach($rowRegClasf as $keyCutoff=>$sessionType)
															{
																$sessiontype = $sessionType['WORKSHOP_TYPE'];
																$workshopName = $sessionType['WORKSHOP_GRP'];
															}
															if($keyRegClasf==$rowFetchUser['registration_classification_id'])
															{
															?>
															 <tr use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" >
																<td align="left">
																	<div class="tooltip">
																		<?=$span?>
																		<input type="radio"  <?=$style?> name="workshop_id[]" id="workshop_id" operationMode="Workshopclsf" workshopName="<?=$workshopName?>" opmode="workshopId" value="<?=$keyWorkshopclsf?>" 
																			   style="-webkit-appearance: checkbox;  -moz-appearance: checkbox; -ms-appearance: checkbox;" />
																	</div>
																</td>
																<td align="left">&nbsp;&nbsp;&nbsp;<?=getWorkshopName($keyWorkshopclsf)?></td>
															 </tr>
															<? 
															}
														}
													}
												 }
											  }
											 ?>	
										</table>	
									</td>
								</tr>
								<tr>
									<td></td>
									<td><input type="submit" valus="Submit" class="btn btn-large btn-blue" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>				
			</tbody> 
		</table>
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
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A' AND `id`  NOT IN (3,6)";
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
							    $alterCondition = "AND delegate.id IN ( SELECT DISTINCT delegate_id FROM "._DB_CANCEL_INVOICE_." )";
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
																	 FROM "._DB_CANCEL_INVOICE_." 
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
	
	function addDinnerFormTemplate($requestPage, $processPage, $registrationRequest="GENERAL")
	{
		global $cfg, $mycms;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.dinner.php');
		
		$cutoffArray  			= array();
		$sqlCutoff 		 		= array();
		$sqlCutoff['QUERY']    	= "SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
								    WHERE `status` != 'D' 
							     ORDER BY `cutoff_sequence` ASC";	
		$resCutoff 				= $mycms->sql_select($sqlCutoff);
		
		if($resCutoff)
		{
			foreach($resCutoff as $i=>$rowCutoff) 
			{
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}
		
		$currentCutoffId 		= getTariffCutoffId();
		
		$delegateId 			= $_REQUEST['id'];
		$spotUser				= $_REQUEST['userREGtype'];	
		$rowFetchUser   		= getUserDetails($delegateId);
		$delagateCatagory 		= getUserClassificationId($delegateId);
		?>
		
		<form name="frmApplyForworkshop" id="frmApplyForworkshop"  action="<?=$processPage?>" method="post" onsubmit="return validateDinnerFrom(this);">
			<input type="hidden" name="act" value="applyDinner" />			
			<input type="hidden" id="type" value="Dinner" />
			<input type="hidden" id="prevValue" value="0" />
			<input type="hidden" name="delegate_id" value="<?=$delegateId?>" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
			<input type="hidden" name="dinnerAmount" id="dinnerAmount" value="<?=$cfg['DINNER_ARRAY'][1]['DISPLAY_AMOUNT']?>" />
			<table width="100%" align="center" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Dinner</td>
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
												$sqlFetchCutoff 		 =	array();
												$sqlFetchCutoff['QUERY'] = "SELECT * 
																			  FROM "._DB_TARIFF_CUTOFF_."
																			 WHERE `status` = 'A' ORDER BY id";
												
												$resultCutoff	= $mycms->sql_select($sqlFetchCutoff);
												if($resultCutoff){
														foreach($resultCutoff as $keyCutoff=>$rowCutoff){
														?>
															<option value="<?=$rowCutoff['id']?>" <?=$rowCutoff['id']==$currentCutoffId?"selected":""?>><?=$rowCutoff['cutoff_title']?></option>
														<?php
													}
												}
											?>
										</select>
									</td>
									<td width="50%"></td>
								</tr>
							</table>
							
							<div operationMode="regClassId" style="display:none;"><?=$registration_classification_id?></div>																		
								<div class="reg_dtl_tbl">
									<table width="100%">
										<tbody>
											<tr>
												<th class="tcat" >
													DINNER REGISTRATION 
												</th>
											<?
												$sql['QUERY'] = "SELECT cutoff.cutoff_title, cutoff.id 
														 		   FROM "._DB_TARIFF_CUTOFF_." cutoff
														 		  WHERE status = 'A'";
												$res = $mycms->sql_select($sql);
												foreach($res as $key=>$title)
												{	
											?>
													<th class="tcat"><?=strip_tags($title['cutoff_title'])?></th>
											<?
												}
											?>
											</tr>											
											<?
												$sqlDetails	        = array();
												$sqlDetails['QUERY'] = "SELECT *  
																		 FROM "._DB_USER_REGISTRATION_."
																		WHERE status = ?
																		  AND id= ?";
												$sqlDetails['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',                'TYP' => 's');	
												$sqlDetails['PARAM'][]   = array('FILD' => 'id',         	'DATA' =>$delegateId,        'TYP' => 's');						
												$resDetails = $mycms->sql_select($sqlDetails);
												$userDetails = $resDetails[0];
											
											?>
											<tr>
												<td>
											<?  
													$dinnerNotTaken = 0;
													$dinnerDetailsUser	        	= array();
													$dinnerDetailsUser['QUERY'] 	= "SELECT *  
																						 FROM "._DB_REQUEST_DINNER_."
																						WHERE status = ?
																						  AND refference_id = ?";
													$dinnerDetailsUser['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',         'TYP' => 's');
													$dinnerDetailsUser['PARAM'][]   = array('FILD' => 'refference_id',  'DATA' =>$delegateId, 'TYP' => 's');								   
													$resDinnerDetailsUser   = $mycms->sql_select($dinnerDetailsUser);
													$rowsDinnerDetailsUser	= $mycms->sql_numrows($resDinnerDetailsUser);
													$userDinnerDetail       = $resDinnerDetailsUser[0];
													
													if ($rowsDinnerDetailsUser == 0)
													{   //echo "<pre>";print_r($cfg['DINNER_ARRAY']);echo "</pre>";
														$dinnerNotTaken ++;
											?>
														<input type="checkbox" name="dinner_value[]" id="dinner_value" operationMode="dinner_value" ammount="" value="<?=$delegateId?>" />&nbsp;&nbsp;&nbsp;
											<?
													}
											?>		
													<?=$userDetails['user_full_name']?> 
												</td> 												
											<?
												if ($rowsDinnerDetailsUser > 0)
												{
											?>
												<td>
													<span  style="color:#00a200;">TAKEN</span>
											<?
														if ($userDinnerDetail['payment_status'] == 'UNPAID')
														{
											?>	
															<span  style="color:#FF0000">(Payment Incomplete)</span>
											<?
														}
											?>
												</td>
											<?
												}
												else
												{
													$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
													foreach($res as $key=>$title)
													{	
														$dinnerTariffArray   = getAllDinnerTarrifDetails($key);
														foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)

														{	
											?>
														<td align="center" use="dinnerRate" rate="<?=$dinnerValue[$title['id']]['AMOUNT']?>" cutoff="<?=$title['id']?>">
														<input type="hidden" name="diiner_classification_id[]" value="<?=$dinnerValue[$title['id']]['ID']?>"/>
														<?=$dinnerValue[$currentCutoffId]['ID']?>
															INR <?=number_format(floatval($dinnerValue[$title['id']]['AMOUNT']),2)?>
														</td>
											<?
														}
													}
												}
											?>
											</tr>
											<?
												$accomDetails		  = 	array();
												$accomDetails['QUERY'] = "SELECT *  
																		   FROM "._DB_USER_REGISTRATION_."
																		   WHERE status = ?
																			 AND refference_delegate_id= ?
																			 AND (registration_payment_status= 'PAID' OR registration_payment_status= 'ZERO_VALUE')";
																			 
												$accomDetails['PARAM'][]   = array('FILD' => 'status',         				   'DATA' =>'A',         		 'TYP' => 's');							 
												$accomDetails['PARAM'][]   = array('FILD' => 'refference_delegate_id',         'DATA' =>$delegateId,         'TYP' => 's');
																				 
												$resAccomDetails = $mycms->sql_select($accomDetails);
												foreach($resAccomDetails as $keyAccomDetails=>$rowAccomDetails)
												{
											?>
													<tr>
														<td>
											<?
															$dinnerDetailsAccom		  = 	array();
															$dinnerDetailsAccom['QUERY'] = "SELECT *  
																							  FROM "._DB_REQUEST_DINNER_."
																							 WHERE status = ?
																							   AND refference_id= ?";
															$dinnerDetailsAccom['PARAM'][]   = array('FILD' => 'status',   		'DATA' =>'A',   				   'TYP' => 's');							 
															$dinnerDetailsAccom['PARAM'][]   = array('FILD' => 'refference_id', 'DATA' =>$rowAccomDetails['id'],   'TYP' => 's');
																				   
															$resDinnerDetailsAccom   = $mycms->sql_select($dinnerDetailsAccom);
															$accomDinnerDetail       = $resDinnerDetailsAccom[0];
															$rowsDinnerDetailsAccom	= $mycms->sql_numrows($resDinnerDetailsAccom);
															if ($rowsDinnerDetailsAccom == 0)
															{
																$dinnerNotTaken ++;
											?>
																<input type="checkbox" name="dinner_value[]" id="dinner_value" operationMode="dinner_value" ammount="" value="<?=$rowAccomDetails['id']?>" />&nbsp;&nbsp;&nbsp;
											<?
															}
											?>		   
															<?=$rowAccomDetails['user_full_name']?> 
														</td>
											<?
															if ($rowsDinnerDetailsAccom > 0)
															{
											?>
															<td><span  style="color:#00a200;">TAKEN</span>
											<?
																if ($accomDinnerDetail['payment_status'] == 'UNPAID')
																{
											?>	
															<span  style="color:#FF0000;">(Payment Incomplete)</span>
											<?
																}
											?>	
															</td>
											<?
															}
															else
															{
													
																$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
												
																foreach($res as $key=>$title)
																{	
																	$dinnerTariffArray   = getAllDinnerTarrifDetails($key);
																	foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
																	{	
											?>
																	<td align="center" use="dinnerRate" rate="<?=$dinnerValue[$title['id']]['AMOUNT']?>" cutoff="<?=$title['id']?>">
																	<input type="hidden" name="diiner_classification_id[]" value="<?=$dinnerValue[$title['id']]['ID']?>"/>
																	<?=$dinnerValue[$currentCutoffId]['ID']?>
																		INR <?=number_format(floatval($dinnerValue[$title['id']]['AMOUNT']),2)?>
																	</td>
											<?
																	}
																}
												   			}
											?>
														</tr>
											<?
												}
											?>
										</tbody> 
									</table>
								</div>
							</div>
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
												 <span class="registrationPaybleAmount" id="amount2">0.00</span>
											</span>
										</div>
									</td>
								</tr>
							</table>
								
						</td>
					</tr>
					<tr>
						<td colspan="2" align="left">
							<input name="" type="submit" value="Proceed" style="margin-left:20%; " class="btn btn-small btn-blue" />
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
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
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
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
		
		/*$sqlFetchUser             = registrationDetailsCompressedQuerySet($delegateId); 
		$resultFetchUser          = $mycms->sql_select($sqlFetchUser);*/
		$rowFetchUser             = getUserDetails($delegateId); 
		
		//echo '<!--'; print_r($rowFetchUser ); echo '-->-';
		
		$registrationClassificationId   = $rowFetchUser['registration_classification_id'];
		$tariffCutoffId                 = $rowFetchUser['registration_tariff_cutoff_id'];
		
		$invoiceCondition				= " AND (service_type = 'DELEGATE_CONFERENCE_REGISTRATION' OR service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION') ";
		$registrationInvoiceDetails		= invoiceDetailsOfDelegate($delegateId,$invoiceCondition);
		$invoiceId 						= $registrationInvoiceDetails[0]['id'];
		
		$sqlAccomm = array();
		$sqlAccomm['QUERY']    = " SELECT accomm.*, hotel.hotel_name
									 FROM "._DB_REQUEST_ACCOMMODATION_." accomm
							   INNER JOIN "._DB_MASTER_HOTEL_." hotel
									   ON accomm.hotel_id = hotel.id
									WHERE accomm.`user_id` = '".$delegateId."'
									  AND accomm.`refference_invoice_id` = '".$invoiceId."'";	
		$resAccomm 				= $mycms->sql_select($sqlAccomm);
		$rowAccomm				= $resAccomm[0];
		echo '<!--'; print_r($resAccomm ); echo '-->';
		
		$residentialHotelId 	= $rowAccomm['hotel_id'];
		$residentialHotelName 	= $rowAccomm['hotel_name'];
		$residentialCheckin 	= $rowAccomm['checkin_date'];
		$residentialCheckout 	= $rowAccomm['checkout_date'];
		
		$residentialType = getInvoiceTypeString($delegateId,$invoiceId,"RESIDENTIAL");
		
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
		
		?>
		<script>
		
		
		function calculateSelectedAccomodationAmoynt()
		{
			var frm    					= $("form[name=frmApplyForAccommodation]");
			var tariff_cutoff_id		= $(frm).find("select[name=cutoff_id_add]").val();
			var hotel_id 				= $(frm).find("input[name=accommodation_hotel_id]").val();
			var package_id 				= $(frm).find("input[type=radio][name=accommodation_pack]:checked").val();
			var checkin_date_id 		= $(frm).find("select[name=check_in_date]").val();
			var checkout_date_id 		= $(frm).find("select[name=check_out_date]").val();
						
			console.log('hotel_id>>'+hotel_id+';package_id>>'+package_id+';checkin_date_id>>'+checkin_date_id+';checkout_date_id>>'+checkout_date_id+';tariff_cutoff_id>>'+tariff_cutoff_id);
			console.log('-----------------------------------------------');
			var amountOB	= null;
			$.each($("div[use=checkinDataContainers]").find("input[operationMode=accommodation_tariff_details][hotel_id='"+hotel_id+"']"),function(){
				var ob1 = $(this);
				
				console.log('hotel_id>>'+$(ob1).attr("hotel_id")+';package_id>>'+$(ob1).attr("package_id")+';checkin_date_id>>'+$(ob1).attr("checkin_date_id")+';checkout_date_id>>'+$(ob1).attr("checkout_date_id")+';tariff_cutoff_id>>'+$(ob1).attr("tariff_cutoff_id"));
				
				if( $.trim($(ob1).attr("hotel_id")) == $.trim(hotel_id)
					&& $.trim($(ob1).attr("package_id")) == $.trim(package_id)
					&& $.trim($(ob1).attr("checkin_date_id")) == $.trim(checkin_date_id)
					&& $.trim($(ob1).attr("checkout_date_id")) == $.trim(checkout_date_id)
					&& $.trim($(ob1).attr("tariff_cutoff_id")) == $.trim(tariff_cutoff_id))
				{
					console.log('found');
					amountOB				= ob1;
					return false;
				}
			});
			
			console.log(amountOB);
			var amount					= 0;
			if(amountOB!=null)
			{
				amount = $(amountOB).val();
				
				$("#accommodationDetailsPlaceholderTable").find("td[use=packName]").html($(frm).find("input[type=radio][name=accommodation_pack]:checked").attr("packName"));
				$("#accommodationDetailsPlaceholderTable").find("td[use=ammount]").html(amount);
			}
			
			setTimeout(function(){
				$("span[operationmode=totalAccomodationAmount]").html(amount);
			},2000);
		}
		</script>
		<div use="checkinDataContainers" style="display:none;">
		<?			
			$sqlAccommodationTariff['QUERY']    = " SELECT *
													  FROM "._DB_TARIFF_ACCOMMODATION_." 
													 WHERE status = 'A'";
			$resultAccommodationTariff = $mycms->sql_select($sqlAccommodationTariff);
			
			if($resultAccommodationTariff)

			{
				foreach($resultAccommodationTariff as $keyTariff=>$rowAccommodationTariff)
				{
			?>
					<input type="hidden" operationMode="accommodation_tariff_details" 
						   checkin_date_id="<?=$rowAccommodationTariff['checkin_date_id']?>" 
						   checkout_date_id="<?=$rowAccommodationTariff['checkout_date_id']?>"
						   hotel_id="<?=$rowAccommodationTariff['hotel_id']?>"  
						   package_id="<?=$rowAccommodationTariff['package_id']?>" 
						   tariff_cutoff_id ="<?=$rowAccommodationTariff['tariff_cutoff_id']?>" 
						   currency="<?=$rowAccommodationTariff['currency']?>" 
						   value="<?=$rowAccommodationTariff['inr_amount']?>" />
			<?php	
				}
			}
		?>
		</div>
		<form name="frmApplyForAccommodation" id="ApplyForAccommodation" action="registration.process.php" method="post" >
			<input type="hidden" name="act" value="apply_additional_accommodation" />
			<input type="hidden" name="userREGtype" value="<?=$spotUser?>" />
			<input type="hidden" name="delegate_id" id="delegate_id" value="<?=$delegateId?>" />
			<input type="hidden" name="redirect" value="<?=$requestPage?>"/>
			<input type="hidden" name="userREGtype" id="userREGtype" value="<?=$_REQUEST['userREGtype']?>"  />
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
									<td colspan="4" align="left">Accommodation Information</td>
								</tr>
								<tr>
									<td align="left" valign="top" style="width:20%;">Registration: </td>
									<td align="left" valign="top"style="width:30%;"> <?=$residentialType?></td>
									<td align="left" valign="top" style="width:20%;">Hotel :</td>
									<td align="left" valign="top" style="width:30%;"><?=$residentialHotelName?></td>
								</tr>
								<tr>
									<td align="left" valign="top" style="width:20%;">Checkin : </td>
									<td align="left" valign="top"style="width:30%;"> <?=$residentialCheckin?></td>
									<td align="left" valign="top" style="width:20%;">Checkout :</td>
									<td align="left" valign="top" style="width:30%;"><?=$residentialCheckout?></td>
								</tr>
							</table>
							
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Accommodation</td>
								</tr>
								<tr>
									<td align="left" style="width:20%;">
										Cutoff: <span class="mandatory">*</span>
									</td>
									<td align="left" style="width:30%;">
										<select style="width:90%;" name="cutoff_id_add" id="cutoff_id_add" required>
											<option value="">-- Choose Cutoff --</option>
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
										<input type="hidden" name="accommodation_hotel_id" id="accommodation_hotel_id" value="<?=$residentialHotelId?>" />
										<input type="hidden" name="booking_quantity" value="1" />
									</td>
									<td align="left" valign="top" width="20%" >Package</td>
									<td align="left" valign="top" use="selectAccmPack" width="30%" >
										<span  use="packageSelecter" hotel_id="<?=$residentialHotelId?>">
											<?
											// FETCHING ACCOMMODATION BOOKING DATES
											 $sqlAccommodationDate['QUERY']    = "SELECT * 
																					FROM "._DB_ACCOMMODATION_PACKAGE_." 
																				   WHERE `status` = 'A'
																					 AND `hotel_id` = '".$residentialHotelId."'";
											$resultAccommodationDate 		   = $mycms->sql_select($sqlAccommodationDate); 
											foreach($resultAccommodationDate as $keyAccommodationDate=>$rowAccommodationPack)
											{
											?>
											<input type="radio" name="accommodation_pack" value="<?=$rowAccommodationPack['id']?>" 
													packName = "<?=$rowAccommodationPack['package_name']?>"
												  autocomplete="off" required onclick="calculateSelectedAccomodationAmoynt()"> <?=$rowAccommodationPack['package_name']?> 
											<?
											}
											?>
										</span>
									</td>
								</tr>
								<tr>
									<td align="left" width="20%" valign="top">Check In Date: <span class="mandatory">*</span></td>
									<td align="left" width="30%" valign="top" use="selectAccmCheckin">
										<select name="check_in_date" id="check_in_date" class="drpdwn" style="width:90%;" use="checkInSelecter" hotel_id="<?=$residentialHotelId?>" required onchange="calculateSelectedAccomodationAmoynt()">
											<option value="">-- Select --</option>
											<?php
											// FETCHING ACCOMMODATION BOOKING DATES
											 $sqlAccommodationDate['QUERY']    = "SELECT * 
																					FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
																				   WHERE `status` = 'A'
																					 AND `hotel_id` = '".$residentialHotelId."'
																				ORDER BY check_in_date ASC";
											$resultAccommodationDate 		   = $mycms->sql_select($sqlAccommodationDate); 
											
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
									<td align="" width="30%" valign="top" use="selectAccmCheckout">
										<select name="check_out_date" id="check_out_date" class="drpdwn" style="width:90%;" use="checkOutSelecter" hotel_id="<?=$residentialHotelId?>" required onchange="calculateSelectedAccomodationAmoynt()">
											<option value="">-- Select --</option>
											<?php
											// FETCHING ACCOMMODATION BOOKING DATES
											$sqlAccommodationDate['QUERY']    = " SELECT * 
																					FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
																				   WHERE `status` = 'A'
																					 AND `hotel_id` = '".$residentialHotelId."'
																				ORDER BY check_out_date ASC";
																				
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
							</table>
							
							<div id="accommodationDetailsPlaceholder">
								<table width="100%" id="accommodationDetailsPlaceholderTable">
									<tr class="thighlight">
										<td colspan="4" align="left">Select Guests</td>
									</tr>
									<tr class="theader">
										<td align="left" valign="top">PACKAGE</td>
										<td align="right" valign="top">AMOUNT</td>
									</tr>
									<tr>
										<td align="left" valign="top" use="packName"></td>
										<td align="right" valign="top" use="ammount"></td>
									</tr>
								</table>
							</div>
							
							<br />
							<span class="tab-text" id="accName" style="display:none;">
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
										setPaymentTemplate("add");
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
		$sqlSlipDetails['QUERY'] ="SELECT `slip_id` FROM "._DB_PAYMENT_."
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
										$sqlExhibitorName['QUERY']	=	"SELECT `exhibitor_company_name` FROM "._DB_EXIBITOR_COMPANY_." 
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
													//if($loggedUserId == 1 )
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
										$sqlExhibitorName['QUERY']	=	"SELECT `exhibitor_company_name` FROM "._DB_EXIBITOR_COMPANY_." 
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
													//if($loggedUserId == 1 )
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
		global $cfg, $mycms;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.workshop.php');
		include_once('../../includes/function.dinner.php');
		
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
								<td colspan="8" align="left">Payment Voucher Details</td>
							</tr>
							<?php
							$paymentCounter   = 0;
							$resFetchSlip	  = slipDetailsOfUser($delegateId,true);
							if($resFetchSlip)
							{
							?>
								<tr class="theader">
									<td width="30" align="center">Sl</td>
									<td width="180" align="left">PV No.</td>
									<td width="100" align="center">PV Date</td>
									<td width="80" align="center">Mode</td>
									<td width="100" align="right">Discount</td>
									<td width="100" align="center">PV Amt.</td>
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
											$discountAmountofSlip = $discountDeductionAmount;
										}
										else
										{
											$discountAmountofSlip = ($discountDeductionAmount/1.18);
										}
									}
									
									$DiscountAmount = invoiceDiscountAmountOfSlip($rowFetchSlip['id'],true);
								?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$counter?></td>
										<td align="left" valign="top"><?=$rowFetchSlip['slip_number']?>
										</td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchSlip['slip_date'], "D")?></td>
										<td align="center" valign="top"><?=$rowFetchSlip['invoice_mode']?></td>
										<td align="center" valign="top"><?=($DiscountAmount>0)?($rowFetchSlip['currency'].'&nbsp;'.number_format($DiscountAmount,2)):'-'?></td>
										<td align="right" valign="top"><?=$rowFetchSlip['currency'].'&nbsp;'.number_format($amount,2)?></td>
										<td align="right" valign="top">
											<?
											if($resPaymentDetails['totalAmountPaid'] > 0)
											{
												echo $rowFetchSlip['currency'].'&nbsp;'.number_format($resPaymentDetails['totalAmountPaid'],2);
											}
											else
											{
												echo $rowFetchSlip['currency'].'&nbsp;'."0.00";
											}
											?>
										</td>
										<td align="center" valign="top">
										<div class="tooltip"  style="float:left; margin-right: 5px;">
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
											<span class="ticket ticket-important" style="background-color:#5E8A26;"><strong>Complimentary</strong></span>
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
											<span class="ticket ticket-important" style='background-color:#009900;'>Zero Value</span>
											
										<?
											}
										}
										else
										{
										?>
											<a onclick="$('#paymentDetails<?=$rowFetchSlip['id']?>').toggle();">
											<i class="fa fa-money" style="color:#660033;" aria-hidden="true"></i>
											</a>
										<?php
										}
										?>
										</div>
										<div class="tooltip"  style="float:right; margin-right: 5px;">
											<a href= "<?=_BASE_URL_?>downloadFinalInvoice.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>&original=true" 
											   target="_blank" style="background:none; border:none;float:right; margin:3px;" >
												<i class="fa fa-eye" aria-hidden="true" style="color:#000" title="Print Payment Voucher"></i>
											</a>											
											<a onclick="$('#invoiceDetails<?=$rowFetchSlip['id']?>').toggle();" style="float:right;  margin:3px;">
												<i class="fa fa-bars" aria-hidden="true"  style="color:#000" title="Show All Slip Invoice"></i>
											</a>
										</div>
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
											<i class="fa fa-hourglass" aria-hidden="true"  style="color:#000"></i>
										</div>	
										<div class="tooltip"  style="float:right; margin-right: 5px;">
											<span style="float:right; font-weight:bold; margin: 3px;">
												<a href="<?=_BASE_URL_?>downloadSlippdf.php?delegateId=<?=$mycms->encoded($rowFetchUser['id'])?>&slipId=<?=$mycms->encoded($rowFetchSlip['id'])?>" target="_blank">
													<i class="fa fa-download" aria-hidden="true"  style="color:#000" title="Download Payment Voucher"></i>
												</a>
											</span>
																			
										<?
										if($resPaymentDetails['payment_status'] == "PAID" || $rowFetchSlip['payment_status']=="COMPLIMENTARY" || $rowFetchSlip['payment_status']=="ZERO_VALUE")
										{
										?>
											<a href="registration.php?show=sendRegConfirmMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
											   style="float:right; margin: 3px;">
											   <i class="fa fa-envelope" aria-hidden="true" style="color:#FF0000" title="Resend Service Confirmation Mail"></i>
											</a>
											<a href="registration.php?show=sendRegConfirmSMS&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" 
											   style="float:right; margin: 3px;">
											   <i class="fa fa-commenting-o" aria-hidden="true" style="color:#FF0000" title="Resend Service Confirmation SMS"></i>
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
											   style="margin:3px;">
											   <i class="fa fa-commenting-o" aria-hidden="true" style="color:#0066FF" title="Resend Acknowledgement SMS"></i>
											</a>
											<a href="registration.php?show=sendAccknowledgementMail&paymentId=<?=$resPaymentDetails['id']?>&slipId=<?=$rowFetchSlip['id']?>&delegateId=<?=$rowFetchUser['id']?>" style="float:right; margin: 3px;">
											<i class="fa fa-envelope" aria-hidden="true" style="color:#0066FF" title="Resend Acknowledgement Mail"></i>
											</a>
										<?
											}
										}
										?>
										</div>
										
										
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
															$type 		= "Invoice Cancelled";
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
												<?php
												$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);
												$paymentCounter                 = 0;
												$lastPaymentStatus              = 'UNPAID';
												
												foreach($resPaymentDetails['paymentDetails'] as $key=>$rowPayment)
												{
													$paymentCounter++;
													$lastPaymentStatus = $rowPayment['payment_status'];
													
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
													<tr class="tlisting" style="background:#FFCEFF; color:#000000 !important;">
														<td align="center" valign="top" width="30px;"><?=$paymentCounter?>.</td>
														<td align="Left" valign="top">
														Paid Amount :  <b><?=$rowPayment['amount']?></b><br />
														<?=$paymentDescription?>
														</td>
														<td align="center" valign="top" width="200px">
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
														?>
														</td>
													</tr>
											<?php
												}
												
												if($resPaymentDetails['has_to_set_payment'] == 'Yes')
												{
													$onlinePaymentLink = _BASE_URL_."payment.retry.php?request_payment_mode=ONLINE&slip_id=".$rowFetchSlip['id'];
													if($resPaymentDetails['slip_invoice_mode']=='ONLINE')
													{
													?>
													<tr class="tlisting">
														<td colspan="2">Payment Link : <a href="<?=$onlinePaymentLink?>" style="font-size:smaller;" target="_blank"><?=$onlinePaymentLink?></a></td>
														<td align="right">
													<?
														if($paymentCounter<=1 && $totalNoOfUnpaidCount<=1 && $lastPaymentStatus=='UNPAID')
														{
													?>	
														
															<a class="ticket ticket-important" operationMode="proceedPayment" 
															 onclick="changePaymentPopup('<?=$rowFetchSlip['id']?>','<?=$rowFetchUser['id']?>','OFFLINE')">Change Payment Mode</a>&nbsp;
													<?
														}
													?>
															<a class="ticket ticket-important" style="background-color:#0000FF;" operationMode="proceedPayment" 
															onclick="onlineOpenPaymentPopup('<?=$rowFetchUser['id']?>','<?=$rowFetchSlip['id']?>','<?=$rowPayment['id']?>')">Complete Online Payment</a>
														</td>
													
													</tr>
													<?php
													}
													elseif($resPaymentDetails['slip_invoice_mode']=='OFFLINE' && ($totalNoOfUnpaidCount==0))
													{
														
													?>
														<tr class="tlisting">
															<td>&nbsp;</td>
															<td align="right">
													<?
														if($paymentCounter<=1 && $totalNoOfUnpaidCount<=1 && $lastPaymentStatus=='UNPAID')
														{
													?>
															
																<a class="ticket ticket-important" operationMode="proceedPayment" 
																 onclick="changePaymentPopup('<?=$rowFetchSlip['id']?>','<?=$rowFetchUser['id']?>','ONLINE')">Change Payment Mode</a>&nbsp;
													<?
														}
													?> 
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
							</tr>
							<?
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
									$showTheRecord 		= true;
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
										if($workShopDetails['showInInvoices']!='Y')
										{
											$showTheRecord 		= false;
										}
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
									
									if($showTheRecord)
									{
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
				<td align="center"><h2 style="color:#CC0000;">Do you want to change payment mode<br /><br />to <span use="changeTo"></span>?</h2></td>
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
			$('span[use=changeTo]').html(regMode);
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
	
	function upgradeRegistrationPack()
	{ 		
		global $cfg, $mycms;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.workshop.php');
		include_once('../../includes/function.dinner.php');
		
		$delegateId 	=  $_REQUEST['id'];
		$loggedUserId	= $mycms->getLoggedUserId();
		$rowFetchUser   = getUserDetails($delegateId);		
		$regClassfId 	= getUserClassificationId($delegateId);
		$regCutoffId 	= '0';
		$regInvoiceId	= '0';
		?>
		<script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
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
								<td colspan="9" align="left"> 
								User Invoice
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
							</tr>
							<?php
							
							$resultFetchInvoice             = getInvoiceDetailsquery("",$delegateId,"");
							$invoiceCounter                 = 0;
							if($resultFetchInvoice)
							{
								foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
								{									
									$showTheRecord 		= true;
									$invoiceCounter++;
									$slip 				= getInvoice($rowFetchInvoice['slip_id']);
									$returnArray    	= discountAmount($rowFetchInvoice['id']);
									$percentage     	= $returnArray['PERCENTAGE'];
								
									$totalAmount    	= $returnArray['TOTAL_AMOUNT'];
									
									$discountAmount		= $returnArray['DISCOUNT'];
									$thisUserDetails 	= getUserDetails($rowFetchInvoice['delegate_id']);
									$type			 	= "";
									if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
									{
										$type 			= "CONFERENCE REGISTRATION - ".$thisUserDetails['user_full_name'];
										$regCutoffId	= $rowFetchInvoice['service_tariff_cutoff_id'];
										$regInvoiceId	= $rowFetchInvoice['id'];
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
									{
										$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
										$type 			 = strtoupper(getWorkshopName($workShopDetails['workshop_id']))." REGISTRATION - ".$thisUserDetails['user_full_name'];
										if($workShopDetails['showInInvoices']!='Y')
										{
											$showTheRecord 		= false;
										}
									}
									if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
									{
										$thisUserAccompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
										$type = "ACCOMPANY REGISTRATION - ".$thisUserAccompanyDetails['user_full_name'];
									}
									if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
									{
										$regCutoffId	= $rowFetchInvoice['service_tariff_cutoff_id'];
										$regInvoiceId	= $rowFetchInvoice['id'];
										
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
									
									if($showTheRecord)
									{
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
								</tr>
							<?php
									}
								}
							}
							?>	
					</table>
		<?
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
		
		$conferenceTariffArray   = getAllRegistrationTariffs("",false);
		
		$workshopDetailsArray 	 = getAllWorkshopTariffs();
		$workshopCountArr 		 = totalWorkshopCountReport();	
		?>
						<table width="100%">	
							<tr class="thighlight">
								<td colspan="9" align="left"> 
								UPGRADE
								</td>
							</tr>
							<tr style="display:none1;">
								<td colspan="9" align="right">
									<form name="frmUpgradeRegistration" id="frmUpgradeRegistration" action="registration.process.php" 
										  enctype="multipart/form-data" method="post">
										<input type="hidden" name="act" value="changeRegClassification" />
										<input type="hidden" name="delegateId" value="<?=$delegateId?>" />
										<input type="hidden" name="regCutoffId" value="<?=$regCutoffId?>" />
										<input type="hidden" name="regInvoiceId" value="<?=$regInvoiceId?>" />
										<input type="hidden" name="regClassfId" value="<?=$regClassfId?>" />
									<table width="100%">	
										<tr>
											<td width="20%" align="left" valign="top">Registration Tariff</td>
											<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
												<table width="100%" use="registration_tariff">
													<tr class="theader">
														<td>Registration Classification</td>
														<?
														foreach($cutoffArray as $cutoffId=>$cutoffName)
														{	
															if($cutoffId == $regCutoffId) 
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
															$style 	= '';
															$span	= '';
															if($regClassfId==$key)
															{
																$style 	= 'disabled="disabled"';
																$span	= '<span class="tooltiptext" style="color:red;font-size:11px;"> [Current registration]</span>';
															}
															
															$styleCss = 'style=""';
															$classificationType = getRegClsfType($key);
															
															if($classificationType !='ACCOMPANY' && ($classificationType !='COMBO' || $key == 3) && $key != $cfg['INAUGURAL_OFFER_CLASF_ID'])
															{
															?>
															<tr class="tlisting" <?=$styleCss?>>
																<td align="left">
																	
																	<input type="checkbox" name="registration_classification_id[]" operationMode="registration_tariff" 
																		   value="<?=$key?>" currency="<?=$registrationDetailsVal[1]['CURRENCY']?>" 
																		   <?=$style?>
																		   registrationType="<?=$classificationType?>" 
																		   accommodationPackageId = "<?=$residentialAccommodationPackageId[$key]?>"/>
																	&nbsp;&nbsp;&nbsp;
																	<?=getRegClsfName($key)?><?=$span?>
																</td>
																<?
																
																foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
																{
																	if($keyCutoff == $regCutoffId ) 
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
													$residentialAccommodationPackageId = $cfg['RESIDENTIAL_PACKAGE_ARRAY'];																					
													if($conferenceTariffArray)
													{
														$reghotel_id = "";
														foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
														{
															$style 	= '';
															$span	= '';
															if($regClassfId==$key)
															{
																$style 	= 'disabled="disabled"';
																$span	= '<span class="tooltiptext" style="color:red;font-size:11px;"> [Current registration]</span>';
															}
															
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
																		   <?=$style?>
																		   registrationType="<?=$classificationType?>" 
																		   accommodationPackageId = "<?=$residentialAccommodationPackageId[$key]?>"/>
																	&nbsp;&nbsp;&nbsp;
																	<?=getRegClsfName($key)?><?=$span?>	
																</td> 
																<td></td>
																<td></td>
																<?
																foreach($registrationDetailsVal as $keyCutoff=>$rowCutoff)
																{
																	if($keyCutoff ==$regCutoffId ) 
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
										<tr> 
											<td width="20%" align="left" valign="top">Payment Mode</td>
											<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
												<input type="radio" groupName="paymentMode" name="paymentMode" id="paymentMode" value="ONLINE" required/> Online 
												&nbsp;
												<input type="radio" groupName="paymentMode" name="paymentMode" id="paymentMode" value="OFFLINE"  /> Offline
											</td>
										</tr>
										<tr> 
											<td width="20%" align="left" valign="top">Discount</td>
											<td width="80%" colspan="3" style="margin:0px; padding:0px;" class="tborder">
												<input type="number" name="give_discount" id="give_discount" value="0" style="width:100px; text-align:center;" required/> 
												<span style="font-size:smaller; color:#FF0000;">Validations not in place please be carefull while putting discount.</span>
											</td>
										</tr>
										<tr>
											<td></td>
											<td colspan="3"><input type="submit" valus="Submit" class="btn btn-large btn-blue" /></td>
										</tr>
									</table>
									</form>
								</td>
							</tr>	
							<tr style="display:none;">
								<td colspan="9" align="left"> 
								<h1>System upgradation in progress... <input type="button" value="BACK" class="btn btn-large btn-blue" onclick="window.location.href='registration.php';" /></h1>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody> 
		</table>			
	<?
	}
		
	function listECMembers($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
	?>
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Executive Committee Members</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<div class="tsearch" style='display:block;'>
							<form name="frmSearch" method="post" action="registration.php?show=listECMembers">
							<table width="100%">								
								<tr>
									<td align="left" width="150">Mobile No:</td>
									<td align="left" width="250">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left" width="150">Email Id:</td>
									<td align="left" width="250">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
									<td align="left" width="250">
										Linked ?
										 <input type="radio" name="src_isLinked" id="src_isLinked" 
									 	  value="Y" <?=$_REQUEST['src_isLinked']=='Y'?'checked':''?> /> Yes
										 <input type="radio" name="src_isLinked" id="src_isLinked" 
									 	  value="N" <?=$_REQUEST['src_isLinked']=='N'?'checked':''?> /> No
									</td>
								</tr>
								<tr>
									<td align="left" >User Name:</td>
									<td align="left" >
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" colspan=3>
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
							</form>
						</div>
													
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</td>
									<td align="left">Name & Details</td>
									<td width="300"  align="left">Registration Details</td>
								</tr>
							</thead>
							<tbody>
								<?php	
								
								if(isset($_REQUEST['src_mobile_no']) && $_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND ( ecMember.mobile1 =  '".trim($_REQUEST['src_mobile_no'])."' OR ecMember.mobile2 =  '".trim($_REQUEST['src_mobile_no'])."')";	
								}
								if(isset($_REQUEST['src_email_id']) && $_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND ecMember.email =  '".trim($_REQUEST['src_email_id'])."' ";	
								}
								if(isset($_REQUEST['src_user_first_name']) && $_REQUEST['src_user_first_name']!='')
								{
									$searchCondition   .= " AND ecMember.name LIKE  '%".trim($_REQUEST['src_user_first_name'])."%' ";	
								}
								if(isset($_REQUEST['src_isLinked']) && $_REQUEST['src_isLinked']=='Y')
								{
									$searchCondition   .= " AND user.id IS NOT NULL";	
								}
								if(isset($_REQUEST['src_isLinked']) && $_REQUEST['src_isLinked']=='N')
								{
									$searchCondition   .= " AND user.id IS NULL";	
								}
								
								if($_REQUEST['src_user_tags']!='')
								{
									$alterCondition   .= " AND LOCATE(',".$_REQUEST['src_user_tags'].",', CONCAT(',',delegate.tags,',') ) > 0 ";
								}							
								
								$sqlDetails	= array();
								/*$sqlDetails['QUERY'] = "SELECT ecMember.*, user.id as userId
														  FROM "._DB_EC_MEMBERS_."	ecMember
											   LEFT OUTER JOIN "._DB_USER_REGISTRATION_." user
														    ON ( user.user_mobile_no = ecMember.mobile1
															OR user.user_mobile_no = ecMember.mobile2
															OR user.user_email_id = ecMember.email)
														   AND user.status = 'A'
														 WHERE ecMember.status = 'A' ".$searchCondition."
													  ORDER BY ecMember.id";*/
								$sqlDetails['QUERY'] = "SELECT ecMember.*
														  FROM "._DB_EC_MEMBERS_."	ecMember											  
														 WHERE ecMember.status = 'A' ".$searchCondition."
													  ORDER BY ecMember.id";
								$resDetails          = $mycms->sql_select($sqlDetails);
								
								if($resDetails)
								{									
									foreach($resDetails as $kk=>$ecMemRow) 
									{
											$sqlsDetails['QUERY'] = "SELECT user.id as userId
																	  FROM "._DB_USER_REGISTRATION_." user
																	 WHERE (user.user_mobile_no = '".$ecMemRow['mobile1']."'
																		OR user.user_mobile_no = '".$ecMemRow['mobile2']."'
																		OR user.user_email_id = '".$ecMemRow['email']."')
																	   AND user.status = 'A'
																	   ORDER BY id ASC";
											$resSDetails           = $mycms->sql_select($sqlsDetails);
																					
											$id = $resSDetails[0]['userId'];
										}
										if($id!='')
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
											
											if($rowFetchUser['user_food_preference'] == 'VEG')
											{
												$foodcolor ="#00CC00";
											}
											else
											{
												$foodcolor ="#FF0000";
											}
										}
								?>
										<tr class="tlisting" bgcolor="<?=$color?>" style="color:#000000;">
											<td align="center" valign="top" style="border-bottom:thin dashed #0a5372;">
											<?=$ecMemRow['__SRL__']?><br />	
											<?
											if($id!='')
											{
											?>										
											<span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>; cursor:default;" title="<?=$rowFetchUser['registration_request']?>"><b><?=strtoupper(substr($rowFetchUser['registration_request'],0,1))?></b></span><br />
											<?
												if($rowFetchUser['user_gender']=='Male')
												{
											?>
											<i class="fa fa-male" aria-hidden="true" style="color:#FF0000;" title="Male"></i>
											<?
												}
												else
												{
											?>
											<i class="fa fa-female" aria-hidden="true" style="color:#660099;" title="Female"></i>
											<?
												}
											?>
											<br />
											<i class="fa fa-circle" aria-hidden="true" style="color:<?=$foodcolor?>; border:thin solid <?=$foodcolor?>; padding:1px;" title="<?=$rowFetchUser['user_food_preference']?>"></i><br/>
											<?
												if($rowFetchUser['participation_type']=='FACULTY')
												{
											?>
											<br />
											<span style="color:#cc0000; cursor:default;" title="<?=$rowFetchUser['participation_type']?>"><b><?=strtoupper(substr($rowFetchUser['participation_type'],0,1))?></b></span>
											<?
												}
											}
											?>
											</td>
											<td align="left" valign="top" style="border-bottom:thin dashed #0a5372;">
												<i class="fa fa-user-md" aria-hidden="true" title="User"></i>&nbsp;<b><?=strtoupper($ecMemRow['name'])?></b> 
												<br />
												<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$ecMemRow['mobile1']?>
												<?
												if($ecMemRow['mobile2']!='')
												{
												?>
												<br />
												<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$ecMemRow['mobile2']?>
												<?
												}
												?>
												<br />
												<i class="fa fa-envelope" aria-hidden="true" title="Email"></i>&nbsp;<?=$ecMemRow['email']?>
												<br />												
												<i class="fa fa-certificate" aria-hidden="true" style="color:#6666FF;"></i>&nbsp;<?=$ecMemRow['designation']?>
												<br />
											</td>
											<td align="left" valign="top" style="border-bottom:thin dashed #0a5372;">
											<?
											if($id!='')
											{
											?>
												<i class="fa fa-user-md" aria-hidden="true" title="User"></i>&nbsp;<b><?=strtoupper($rowFetchUser['user_full_name'])?></b> 
												<br />
												<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$rowFetchUser['user_mobile_no']?>
												<br />
												<i class="fa fa-envelope" aria-hidden="true" title="Email"></i>&nbsp;<?=$rowFetchUser['user_email_id']?>
												<br />												
												<div style="font-size:13px;">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo '<i class="fa fa-user-o" aria-hidden="true" title="Classification"></i>&nbsp;'. getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo '<i class="fa fa-scissors" aria-hidden="true" title="Cutoff"></i>&nbsp;'.getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
												<br />
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID"  
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo '<i class="fa fa-id-badge" aria-hidden="true" title="Registration Id"></i>&nbsp;'.$rowFetchUser['user_registration_id'];
													echo "<br />";
												}												
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo '<i class="fa fa-universal-access" aria-hidden="true"  title="Unique Sequence No."></i>&nbsp;'.strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												?>
												<i class="fa fa-clock-o" aria-hidden="true" title="Registration Date"></i>&nbsp;<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
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
												
												if($rowFetchUser['suggested_by']!='')
												{
												?>
													<br><span style="color:#7b0f75;">Sugg. By:<br /> <?=$rowFetchUser['suggested_by']?>	</span>
												<?
												}
												
												if($rowFetchUser['user_food_preference_in_details']!='')
												{
												?>
												<br />
												<i class="fa fa-sticky-note" aria-hidden="true" style="color:#FF0000; cursor:pointer;" 
												   onclick="openNotesEditPopup(this);"
												   userId="<?=$rowFetchUser['id']?>"
												   userNote="<?=$rowFetchUser['user_food_preference_in_details']?>"></i>&nbsp;
												<?=$rowFetchUser['user_food_preference_in_details']?>
												<?
												}
												
												$array = $rowFetchUser['tags'];
												$var = (explode(",",$array));
												if($rowFetchUser['tags']!='' && sizeof($var)>0)
												{
												?>
												<br/><i class="fa fa-tags" aria-hidden="true"></i>&nbsp;
												<?
													$tagText = array();
													foreach($var as $key=>$val)
													{
														if($val =='Executive Committee')
														{
															$tagText[] = '<span style="color:#990033;"><b>'.$val.'</b></span>';
														}
														if($val =='Organizing Committee Member')
														{
															$tagText[] = '<span style="color:#009966;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Guest Faculty')
														{
															$tagText[] = '<span style="color:#CC3333;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Special Faculty')
														{
															$tagText[] = '<span style="color:#FF0066;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Regional Faculty')
														{
															$tagText[] = '<span style="color:#007700;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='National Faculty')
														{
															$tagText[] = '<span style="color:#660066;"><b>'.$val.'</b>&nbsp;</span>';
														}															
														if($val =='International Faculty')
														{
															$tagText[] = '<span style="color:#770000;"><b>'.$val.'</b></span>';
														}
														if($val =='Special Guest')
														{
															$tagText[] = '<span style="color:#663399;"><b>'.$val.'</b>&nbsp;</span>';														
														}
														
													}
													
													echo implode(', ',$tagText);
												}	
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">UNREGISTERED</span>
												<?php
												}
												
												?>
												</div>
											<?
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
			</table>
	<?php
	}
	
	function listNFMembers($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
	?>
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">National Facullty Members</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<div class="tsearch" style='display:block;'>
							<form name="frmSearch" method="get" action="registration.php">
								<input type="hidden" name="show" value="listNFMembers" />
							<table width="100%">								
								<tr>
									<td align="left" width="150">Mobile No:</td>
									<td align="left" width="250">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left" width="150">Email Id:</td>
									<td align="left" width="250">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
									<td align="left" width="250">
										Linked ?
										 <input type="radio" name="src_isLinked" id="src_isLinked" 
									 	  value="Y" <?=$_REQUEST['src_isLinked']=='Y'?'checked':''?> /> Yes
										 <input type="radio" name="src_isLinked" id="src_isLinked" 
									 	  value="N" <?=$_REQUEST['src_isLinked']=='N'?'checked':''?> /> No
									</td>
								</tr>
								<tr>
									<td align="left" >User Name:</td>
									<td align="left" >
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" colspan=3>
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
							</form>
						</div>
														
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</td>
									<td align="left">Name & Details</td>
									<td width="300"  align="left">Registration Details</td>
								</tr>
							</thead>
							<tbody>
								<?php		
								if(isset($_REQUEST['src_mobile_no']) && $_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND ( ecMember.mobile1 =  '".trim($_REQUEST['src_mobile_no'])."' OR ecMember.mobile2 =  '".trim($_REQUEST['src_mobile_no'])."')";	
								}
								if(isset($_REQUEST['src_email_id']) && $_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND ecMember.email =  '".trim($_REQUEST['src_email_id'])."' ";	
								}
								if(isset($_REQUEST['src_user_first_name']) && $_REQUEST['src_user_first_name']!='')
								{
									$searchCondition   .= " AND ecMember.name LIKE  '%".trim($_REQUEST['src_user_first_name'])."%' ";	
								}
								if(isset($_REQUEST['src_isLinked']) && $_REQUEST['src_isLinked']=='Y')
								{
									$searchCondition   .= " AND user.id IS NOT NULL";	
								}
								if(isset($_REQUEST['src_isLinked']) && $_REQUEST['src_isLinked']=='N')
								{
									$searchCondition   .= " AND user.id IS NULL";	
								}
								
								$sqlDetails	= array();
								$sqlDetails['QUERY'] = "SELECT ecMember.*, user.id as userId
														  FROM "._DB_NF_MEMBERS_."	ecMember
											   LEFT OUTER JOIN "._DB_USER_REGISTRATION_." user
														    ON ( user.user_mobile_no = ecMember.mobile1
															OR user.user_mobile_no = ecMember.mobile2
															OR user.user_email_id = ecMember.email)
														   AND user.status = 'A'
														 WHERE ecMember.status = 'A' ".$searchCondition."
													  ORDER BY ecMember.id";
								$resDetails          = $mycms->sql_select($sqlDetails);
								
								if($resDetails)
								{									
									foreach($resDetails as $kk=>$ecMemRow) 
									{										
										$id = $ecMemRow['userId'];
										
										if($id!='')
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
											
											if($rowFetchUser['user_food_preference'] == 'VEG')
											{
												$foodcolor ="#00CC00";
											}
											else
											{
												$foodcolor ="#FF0000";
											}
										}
								?>
										<tr class="tlisting" bgcolor="<?=$color?>" style="color:#000000;">
											<td align="center" valign="top" style="border-bottom:thin dashed #0a5372;">
											<?=$ecMemRow['__SRL__']?><br />	
											<?
											if($id!='')
											{
											?>										
											<span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>; cursor:default;" title="<?=$rowFetchUser['registration_request']?>"><b><?=strtoupper(substr($rowFetchUser['registration_request'],0,1))?></b></span><br />
											<?
												if($rowFetchUser['user_gender']=='Male')
												{
											?>
											<i class="fa fa-male" aria-hidden="true" style="color:#FF0000;" title="Male"></i>
											<?
												}
												else
												{
											?>
											<i class="fa fa-female" aria-hidden="true" style="color:#660099;" title="Female"></i>
											<?
												}
											?>
											<br />
											<i class="fa fa-circle" aria-hidden="true" style="color:<?=$foodcolor?>; border:thin solid <?=$foodcolor?>; padding:1px;" title="<?=$rowFetchUser['user_food_preference']?>"></i><br/>
											<?
												if($rowFetchUser['participation_type']=='FACULTY')
												{
											?>
											<br />
											<span style="color:#cc0000; cursor:default;" title="<?=$rowFetchUser['participation_type']?>"><b><?=strtoupper(substr($rowFetchUser['participation_type'],0,1))?></b></span>
											<?
												}
											}
											?>
											</td>
											<td align="left" valign="top" style="border-bottom:thin dashed #0a5372;">
												<div use="display">
													<i class="fa fa-user-md" aria-hidden="true" title="User"></i>&nbsp;<b><?=strtoupper($ecMemRow['name'])?></b> 
													<br />
													<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$ecMemRow['mobile1']?>
													<?
													if($ecMemRow['mobile2']!='')
													{
													?>
													<br />
													<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$ecMemRow['mobile2']?>
													<?
													}
													?>
													<br />
													<i class="fa fa-envelope" aria-hidden="true" title="Email"></i>&nbsp;<?=$ecMemRow['email']?>
													<br />												
													<i class="fa fa-certificate" aria-hidden="true" style="color:#6666FF;"></i>&nbsp;<?=$ecMemRow['designation']?>
													<br />
												</div>
												<div use="edit" style="display:none;">
													<form name="editNFmember" use="editNFmember" method="post">
													<input type="hidden" name="act" value="updateNFmember" />
													<table width="100%">
													<tr>
														<td style="width:80px;">Name: </td><td><input type="text" name="name" required /></td>
													</tr>
													<tr>
														<td>Email: </td><td><input type="text" name="email" required /></td>
													</tr>
													<tr>
														<td>Mobile: </td><td><input type="text" name="mobile1" required /></td>
													</tr>
													<tr>
														<td colspan="2"><input type="submit" value="Update" /></td>
													</tr>
													</table>
													</form>
												</div>
											</td> 
											<td align="left" valign="top" style="border-bottom:thin dashed #0a5372;">
											<?
											if($id!='')
											{
											?>
												<i class="fa fa-user-md" aria-hidden="true" title="User"></i>&nbsp;<b><?=strtoupper($rowFetchUser['user_full_name'])?></b> 
												<br />
												<i class="fa fa-phone" aria-hidden="true" title="Mobile"></i>&nbsp;<?=$rowFetchUser['user_mobile_no']?>
												<br />
												<i class="fa fa-envelope" aria-hidden="true" title="Email"></i>&nbsp;<?=$rowFetchUser['user_email_id']?>
												<br />												
												<div style="font-size:13px;">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo '<i class="fa fa-user-o" aria-hidden="true" title="Classification"></i>&nbsp;'. getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo '<i class="fa fa-scissors" aria-hidden="true" title="Cutoff"></i>&nbsp;'.getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
												<br />
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID"  
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo '<i class="fa fa-id-badge" aria-hidden="true" title="Registration Id"></i>&nbsp;'.$rowFetchUser['user_registration_id'];
													echo "<br />";
												}												
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo '<i class="fa fa-universal-access" aria-hidden="true"  title="Unique Sequence No."></i>&nbsp;'.strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												?>
												<i class="fa fa-clock-o" aria-hidden="true" title="Registration Date"></i>&nbsp;<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
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
												
												if($rowFetchUser['suggested_by']!='')
												{
												?>
													<br><span style="color:#7b0f75;">Sugg. By:<br /> <?=$rowFetchUser['suggested_by']?>	</span>
												<?
												}
												
												if($rowFetchUser['user_food_preference_in_details']!='')
												{
												?>
												<br />
												<i class="fa fa-sticky-note" aria-hidden="true" style="color:#FF0000; cursor:pointer;" 
												   onclick="openNotesEditPopup(this);"
												   userId="<?=$rowFetchUser['id']?>"
												   userNote="<?=$rowFetchUser['user_food_preference_in_details']?>"></i>&nbsp;
												<?=$rowFetchUser['user_food_preference_in_details']?>
												<?
												}
												
												$array = $rowFetchUser['tags'];
												$var = (explode(",",$array));
												if($rowFetchUser['tags']!='' && sizeof($var)>0)
												{
												?>
												<br/><i class="fa fa-tags" aria-hidden="true"></i>&nbsp;
												<?
													$tagText = array();
													foreach($var as $key=>$val)
													{
														if($val =='Executive Committee')
														{
															$tagText[] = '<span style="color:#990033;"><b>'.$val.'</b></span>';
														}
														if($val =='Organizing Committee Member')
														{
															$tagText[] = '<span style="color:#009966;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Guest Faculty')
														{
															$tagText[] = '<span style="color:#CC3333;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Special Faculty')
														{
															$tagText[] = '<span style="color:#FF0066;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='Regional Faculty')
														{
															$tagText[] = '<span style="color:#007700;"><b>'.$val.'</b>&nbsp;</span>';
														}
														if($val =='National Faculty')
														{
															$tagText[] = '<span style="color:#660066;"><b>'.$val.'</b>&nbsp;</span>';
														}															
														if($val =='International Faculty')
														{
															$tagText[] = '<span style="color:#770000;"><b>'.$val.'</b></span>';
														}
														if($val =='Special Guest')
														{
															$tagText[] = '<span style="color:#663399;"><b>'.$val.'</b>&nbsp;</span>';														
														}
														
													}
													
													echo implode(', ',$tagText);
												}	
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">UNREGISTERED</span>
												<?php
												}
												
												?>
												</div>
											<?
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
			</table>
	<?php
	}
	
	function listUserRegTable()
	{
		global $cfg, $mycms, $searchArray, $searchString;
		
		include_once('../../includes/function.delegate.php');
		include_once('../../includes/function.registration.php');
		include_once('../../includes/function.invoice.php');
		include_once('../../includes/function.dinner.php');
		include_once('../../includes/function.accompany.php');
	    include_once('../../includes/function.workshop.php');
				
		$loggedUserId		= $mycms->getLoggedUserId();
		
	?>
			<script>
			$(document).ready(function(){
				$("td[use=registrationDetailsList]").attr("dataStat","noDisplay");
				
				loadUserDetails();
				
				$("div[operation=userRegistrationPopupOverlay]").click(function(){
					$("div[operation=userRegistrationPopupOverlay]").hide(500);
			    	$("div[operation=userRegistrationPopup]").hide(500);
				});
			});
			
			function loadUserDetails()
			{
				if($("td[use=registrationDetailsList][dataStat=noDisplay]").length > 0)
				{
					var detailsTd = $("td[use=registrationDetailsList][dataStat=noDisplay]").first();
					var userId = $(detailsTd).attr("userId");
					
					var param = "act=getUserRegTableDetails&id="+userId;
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
			
			function toggleDetails(obj)
			{
				var indx = $(obj).attr('indx');
				var td = $(obj).parent().closest('td[use=registrationDetailsList]');
				$(td).find('table[use=userDetails]').hide();
				$(td).find("table[use=userDetails][indx='"+indx+"']").show();
			}				
			</script>
		
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">User Registration List</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;"> 
						
						<!--Advanced Search	-->		
						<div class="tsearch" style='display:block;'>
							<form name="frmSearch" method="post" action="registration.php">
							<input type="hidden" name="show" value="listUserRegTable" />
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
									<td valign="top" align="left" rowspan="4" style="padding-left:20px;">
										 <input type="checkbox" name="src_hasPickup" id="src_hasPickup" 
									 	  value="Y" <?=$_REQUEST['src_hasPickup']=='Y'?'checked':''?> /> Has Pickup
										 <br />
										 <input type="checkbox" name="src_hasDropoff" id="src_hasDropoff" 
									 	  value="Y" <?=$_REQUEST['src_hasDropoff']=='Y'?'checked':''?> /> Has Dropoff
										 <br />
										 <input type="checkbox" name="src_hasGalaDinner" id="src_hasTotPlus" 
									 	  value="Y" <?=$_REQUEST['src_hasGalaDinner']=='Y'?'checked':''?> /> Has Gala Dinner
										 <br />
										<input type="checkbox" name="src_hasAccompany" id="src_hasTotPlus" 
									 	  value="Y" <?=$_REQUEST['src_hasAccompany']=='Y'?'checked':''?> /> Has Accompany
										 <br />
										 <input type="checkbox" name="src_hasAbstract" id="src_hasTotPlus" 
									 	  value="Y" <?=$_REQUEST['src_hasAbstract']=='Y'?'checked':''?> /> Has Abstract
										 <br />
										 <input type="checkbox" name="src_hasAccommodation" id="src_hasTotPlus" 
									 	  value="Y" <?=$_REQUEST['src_hasAccommodation']=='Y'?'checked':''?> /> Has Accommodation
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
									<td align="left">Conf. Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT classf.`classification_title`, classf.`id`, classf.`currency`, classf.`type`,  masterHotel.hotel_name
																					  FROM "._DB_REGISTRATION_CLASSIFICATION_." classf
																		   LEFT OUTER JOIN "._DB_MASTER_HOTEL_." masterHotel
																						ON classf.residential_hotel_id = masterHotel.id
																					 WHERE classf.status = 'A'
																					   AND classf.`id` != 2
																				  ORDER BY classf.type DESC, IFNULL(classf.residential_hotel_id,0) ASC, classf.sequence_by ASC";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											$optgroupName = "";
											
											if($resultClassification)
											{
												?>
													<optgroup label="Conference Registration"> 
												<?
												foreach($resultClassification as $key=>$rowClassification) 
												{
													if($optgroupName != $rowClassification['hotel_name'])
													{
														$optgroupName = $rowClassification['hotel_name'];
														echo "</optgroup><optgroup label='"."Residential Package - ".$optgroupName."'>";
													}
													
													if($rowClassification['type']=="DELEGATE")
													{
														$clasNm = $rowClassification['classification_title'];
													}
													elseif($rowClassification['type']=="COMBO")
													{
														$clasNm = str_replace("Residential Package - ","",$rowClassification['classification_title']);
													}
													else
													{
														$clasNm = $rowClassification['classification_title'];
													}
													
													if($rowClassification['type']=="COMBO" && $rowClassification['hotel_name'] != '')
													{
														$classfId = $rowClassification['id'].'-G';
													}
													else
													{
														$classfId = $rowClassification['id'];
													}
												?>
														<option value="<?=$classfId?>" <?=($classfId==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
														<?=$clasNm?>
														</option>
												<?php
													if($rowClassification['type']=="COMBO" && $rowClassification['hotel_name'] != '')
													{
														$classfId = $rowClassification['id'].'-I';
												?>
														<option value="<?=$classfId?>" <?=($classfId==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
														<?=$clasNm.' - Inaugural Offer'?>
														</option>
												<?php
													}	
												}
												?>
													</optgroup> 
												<?php
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
								<!--
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
								
									$status = substr($_REQUEST['src_workshop_classf'],1);
									?>
									<td align="left">	
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
								-->
								<tr>
									<td align="left">Registration Date:</td>
									<td align="left">
										<input type="date" name="src_registration_from_date" id="src_registration_from_date" 
										 style="width:40%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_from_date']?>" />
										 -
										<input type="date" name="src_registration_to_date" id="src_registration_to_date" 
										 style="width:40%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_to_date']?>" /> 
									</td>
									<td align="left">Tags:</td>
									<td align="left">
										<?
										$tagInKeys = array();
										$sqlAllGeneralUser['QUERY']   = "   SELECT DISTINCT tags 
																			  FROM "._DB_USER_REGISTRATION_." 
																			 WHERE `status` = 'A'";
										$resAllGeneralUser   		  = $mycms->sql_select($sqlAllGeneralUser);
										if($resAllGeneralUser)
										{
											foreach($resAllGeneralUser as $k=>$val)
											{
												if(trim($val['tags']!=''))
												{
													$tagInKeys[$val['tags']] = $val['tags'];
												}
											}
										}
										?>
											<select name="src_user_tags"  style="width:96%;">
												<option value=""> -- Select Tags -- </option>										
										<?
										foreach($tagInKeys as $k=>$tag)
										{
										?>
												<option value="<?=$tag?>" <?=$tag==$_REQUEST['src_user_tags']?'selected':''?>><?=$tag?></option>	
										<?
										}
										?>		
											</select>
									</td>
								</tr>
								<tr>
									<td align="left" colspan=2>
										<input type="checkbox" name="src_hasTotPlus" id="src_hasTotPlus" 
									 	  value="Y" <?=$_REQUEST['src_hasTotPlus']=='Y'?'checked':''?> /> Has RCOG USG TOT Plus
										 <br />
										 <input type="checkbox" name="src_hasLapSutur" id="src_hasLapSutur" 
									 	  value="Y" <?=$_REQUEST['src_hasLapSutur']=='Y'?'checked':''?> /> Has Laparoscopic Suturing										
									</td>
									<td align="left" colspan=3>
										 <input type="checkbox" name="src_has3rd4th" id="src_has3rd4th" 
									 	  value="Y" <?=$_REQUEST['src_has3rd4th']=='Y'?'checked':''?> /> Has Diagnosis & Repair of 3rd & 4th Degree Perineal Tears
										 <br />
										 <input type="checkbox" name="src_hasCerviCancer" id="src_hasCerviCancer" 
									 	  value="Y" <?=$_REQUEST['src_hasCerviCancer']=='Y'?'checked':''?> /> Has Early Detection of Cervical & Breast Cancer (Inc Colposcopy & LLETZ)
									</td>
								</tr>
								<tr>
									<td align="left" colspan=4>
									<input type="radio" name="src_registration_type" value="DELEGATE" <?=($_REQUEST['src_registration_type']=='DELEGATE')?'checked':''?> /> DELEGATE &nbsp;&nbsp;
									<input type="radio" name="src_registration_type" value="COMBO" <?=($_REQUEST['src_registration_type']=='COMBO')?'checked':''?> /> RESIDENTIAL &nbsp;&nbsp;
									<input type="radio" name="src_registration_type" value="ACCOMPANY" <?=($_REQUEST['src_registration_type']=='ACCOMPANY')?'checked':''?>  /> ACCOMPANY &nbsp;&nbsp;
									<input type="radio" name="src_registration_type" value="ONLYWORKSHOP" <?=($_REQUEST['src_registration_type']=='ONLYWORKSHOP')?'checked':''?>  /> ONLY WORKSHOP &nbsp;&nbsp;
									<input type="radio" name="src_registration_type" value="ABSTRACT" <?=($_REQUEST['src_registration_type']=='ABSTRACT')?'checked':''?>  /> ABSTRACT USER &nbsp;&nbsp;
									<input type="radio" name="src_registration_type" value="EXHIBITOR" <?=($_REQUEST['src_registration_type']=='EXHIBITOR')?'checked':''?>  /> EXHIBITOR &nbsp;&nbsp;
									<input type="radio" name="src_registration_type" value="ALL" <?=($_REQUEST['src_registration_type']=='ALL')?'checked':''?>  /> ALL &nbsp;&nbsp;
									</td>										
									<td align="right">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left" colspan=5>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#FFCCFF;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=basic]').show();">Basic Details</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#E0E0C2;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=TotPlus]').show();">RCOG Tot Plus</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#FFFFB0;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=CervicalBreastCancer]').show();">Cervical Breast Cancer</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#DEDEEF;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=PerinealTears]').show();">Diagnosis of Perineal Tears</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#FFDFDF;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=LapSuture]').show();">Laparoscopic Suturing</span>
									
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#CCCCFF;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=Accommodation]').show();">Accommodation</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#B6DADA;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=GalaDinner]').show();">Gala Dinner</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#FFD9B3;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=abstract]').show();">Abstract</span>
									<span style="border:dashed thin #ccc; cursor:pointer; padding:3px; margin:2px;  background:#CCCCCC;" onclick="$('table[use=userDetails]').hide(),$('table[use=userDetails][indx=closeall]').show();">Close All</span>
									</td>
								</tr>
							</table>
							</form>
						</div>
								
						<table width="100%" style="font-size:10px;">
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</td>
									<td align="left">Name & Details</td>
									<td width="60%" align="left">Service Dtls</td>
								</tr>
							</thead>
							<tbody>
								<?php	
								$searchCondition = '';
								if($_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND user_email_id LIKE '%".$_REQUEST['src_email_id']."%'";
								}
								if($_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%'";
								}
								if($_REQUEST['src_user_first_name']!='')
								{		
									$searchCondition   .= " AND (user_first_name  LIKE '%".$_REQUEST['src_user_first_name']."%'
																 OR user_middle_name LIKE '%".$_REQUEST['src_user_first_name']."%'
																 OR user_last_name   LIKE '%".$_REQUEST['src_user_first_name']."%'
																 OR user_full_name LIKE '%".$_REQUEST['src_user_first_name']."%')";
								}
								if($_REQUEST['src_registration_from_date']!='')
								{
									$searchCondition   .= " AND conf_reg_date BETWEEN  '".$_REQUEST['src_registration_from_date']." 00:00:00'
															
															AND '".$_REQUEST['src_registration_to_date']." 23:59:59'";
								}		
								if($_REQUEST['src_registration_type']!='')
								{
									switch(trim($_REQUEST['src_registration_type']))
									{
										case 'DELEGATE':
											$searchCondition   .= " AND user_type = 'DELEGATE' AND registration_request = 'GENERAL' AND registration_classification_id IN(1,3,4,5,6)";
											break;
										case 'COMBO':
											$searchCondition   .= " AND user_type = 'DELEGATE' AND registration_request = 'GENERAL' AND registration_classification_id IN(7,8,9,10,11,12,13,14,15,16,17,18)";
											break;
										case 'ACCOMPANY':
											$searchCondition   .= " AND user_type = 'ACCOMPANY'";
											break;
										case 'ABSTRACT':
											$searchCondition   .= " AND user_type = 'DELEGATE' AND registration_request = 'ABSTRACT'";
											break;
										case 'ONLYWORKSHOP':
											$searchCondition   .= " AND user_type = 'DELEGATE' AND registration_request = 'ONLYWORKSHOP'";
											break;
										case 'EXHIBITOR':
											$searchCondition   .= " AND user_type = 'DELEGATE' AND registration_request = 'EXHIBITOR'";
											break;
									}
								}
								if($_REQUEST['src_conf_reg_category']!='')
								{
									$exploded = explode("-",$_REQUEST['src_conf_reg_category']);
									if(sizeof($exploded)==2)
									{
										$_REQUEST['src_conf_reg_category'] = $exploded[0];
										
										if($exploded[1]=='I')
										{
											$alterCondition   .= " AND conf_reg_date <=  '2019-01-15 23:59:59'";	
										}
										else
										{
											$alterCondition   .= " AND conf_reg_date >  '2019-01-15 23:59:59'";	
										}
									}
									$searchCondition   .= " AND registration_classification_id = '".$_REQUEST['src_conf_reg_category']."'";
								}
								if($_REQUEST['src_registration_id']!='')
								{
									$searchCondition   .= " AND user_registration_id LIKE '%".$_REQUEST['src_registration_id']."')";
								}
								if($_REQUEST['src_user_tags']!='')
								{
									$searchCondition   .= " AND LOCATE(',".$_REQUEST['src_user_tags'].",', CONCAT(',',tags,',') ) > 0 ";
								}
								if($_REQUEST['src_hasPickup']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT user_id FROM "._DB_REQUEST_PICKUP_DROPOFF_." WHERE pikup_time IS NOT NULL)";
								}								
								if($_REQUEST['src_hasDropoff']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT user_id FROM "._DB_REQUEST_PICKUP_DROPOFF_." WHERE dropoff_time IS NOT NULL)";
								}
								if($_REQUEST['src_hasTotPlus']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT delegate_id 
									 									  FROM "._DB_REQUEST_WORKSHOP_." 
																		 WHERE workshop_id='9' 
																		   AND status = 'A' 
																		   AND id IN (SELECT refference_id FROM "._DB_INVOICE_." WHERE service_type = 'DELEGATE_WORKSHOP_REGISTRATION' AND status ='A' ))";
								}
								if($_REQUEST['src_hasLapSutur']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT delegate_id 
									 									  FROM "._DB_REQUEST_WORKSHOP_." 
																		 WHERE workshop_id='6' 
																		   AND status = 'A' 
																		   AND id IN (SELECT refference_id FROM "._DB_INVOICE_." WHERE service_type = 'DELEGATE_WORKSHOP_REGISTRATION' AND status ='A' ))";
								}
								if($_REQUEST['src_has3rd4th']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT delegate_id 
									 									  FROM "._DB_REQUEST_WORKSHOP_." 
																		 WHERE workshop_id='7' 
																		   AND status = 'A' 
																		   AND id IN (SELECT refference_id FROM "._DB_INVOICE_." WHERE service_type = 'DELEGATE_WORKSHOP_REGISTRATION' AND status ='A' ))";
								}
								if($_REQUEST['src_hasCerviCancer']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT delegate_id 
									 									  FROM "._DB_REQUEST_WORKSHOP_." 
																		 WHERE workshop_id='8' 
																		   AND status = 'A' 
																		   AND id IN (SELECT refference_id FROM "._DB_INVOICE_." WHERE service_type = 'DELEGATE_WORKSHOP_REGISTRATION' AND status ='A' ))";
								}
								if($_REQUEST['src_hasGalaDinner']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT refference_id 
									 									  FROM "._DB_REQUEST_DINNER_." 
																		 WHERE package_id='2' 
																		   AND status = 'A' 
																		   AND refference_invoice_id IN (SELECT id FROM "._DB_INVOICE_." WHERE service_type IN ('DELEGATE_DINNER_REQUEST','DELEGATE_RESIDENTIAL_REGISTRATION','DELEGATE_CONFERENCE_REGISTRATION') AND status ='A' ))";
								}
								if($_REQUEST['src_hasAccompany']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT refference_delegate_id 
									 									  FROM "._DB_USER_REGISTRATION_." 
																		 WHERE status = 'A' 
																		   AND id IN (SELECT refference_id FROM "._DB_INVOICE_." WHERE service_type = 'ACCOMPANY_CONFERENCE_REGISTRATION' AND status ='A' ))";
								}
								if($_REQUEST['src_hasAbstract']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT applicant_id 
									 									  FROM "._DB_ABSTRACT_REQUEST_." 
																		 WHERE status = 'A')";
								}
								if($_REQUEST['src_hasAccommodation']=='Y')
								{
									 $searchCondition   .= " AND id IN (SELECT user_id 
									 									  FROM "._DB_REQUEST_ACCOMMODATION_." 
																		 WHERE status = 'A' 
																		   AND refference_invoice_id IN (SELECT id FROM "._DB_INVOICE_." WHERE service_type IN ('DELEGATE_ACCOMMODATION_REQUEST','DELEGATE_RESIDENTIAL_REGISTRATION') AND status ='A' ))";
								}
										
										
								$sqlDelegateQueryset			   = array();
								$sqlDelegateQueryset['QUERY']      = "SELECT id, refference_delegate_id, user_full_name, user_mobile_no, user_email_id,
																			 user_registration_id, user_unique_sequence, registration_tariff_cutoff_id,
																			 user_type, registration_request, operational_area, created_dateTime, status
																		FROM "._DB_USER_REGISTRATION_." 
																	   WHERE status IN ('A','C')
																		     ".$searchCondition." ".$alterCondition."
																	ORDER BY id";
															
								$resultFetchUser     	  		   = $mycms->sql_select($sqlDelegateQueryset);
								
								if($resultFetchUser)
								{					
									foreach($resultFetchUser as $i=>$row) 
									{
										$id = $row['id'];		
										
										$userIcon = '';
										$otherIconColor = '';
										if($row['user_type']=='ACCOMPANY')
										{
											$userIcon = '<i class="fa fa-users" aria-hidden="true" style="color:#0066FF" title="Accompany"></i>';
											$otherIconColor = '#0066FF';
										}
										elseif($row['user_type']=='DELEGATE')
										{
											if($row['registration_request']=='GENERAL')
											{
												$userIcon = '<i class="fa fa-user-md" aria-hidden="true" style="color:#009900;" title="Delegate"></i>';
												$otherIconColor = '#009900';
											}
											elseif($row['registration_request']=='ABSTRACT')
											{
												$userIcon = '<i class="fa fa-user-md" aria-hidden="true" style="color:#FF9900;" title="Abstract"></i>';
												$otherIconColor = '#FF9900';
											}
											elseif($row['registration_request']=='ONLYWORKSHOP')
											{
												$userIcon = '<i class="fa fa-user-md" aria-hidden="true" style="color:#FF00FF;" title="RCOG ToT"></i>';
												$otherIconColor = '#FF00FF';
											}
											elseif($row['registration_request']=='EXHIBITOR')
											{
												$userIcon = '<i class="fa fa-user-secret" aria-hidden="true" style="color:#666633;" title="Exhibitor"></i>';
												$otherIconColor = '#666633';
											}
											else
											{
												$userIcon = '<i class="fa fa-question" aria-hidden="true" style="color:#FF6600;" title="Unknown"></i>';
												$otherIconColor = '#FF6600';
											}
										}	
										else
										{
											$userIcon = '<i class="fa fa-question" aria-hidden="true" style="color:#9900CC;" title="Basic"></i>';
											$otherIconColor = '#9900CC';
										}	
										
										$textDecoration = "none";
										if($row['status']=='C')
										{
											$textDecoration = "line-through";
										}						
								?>
										
										<tr class="tlisting" bgcolor="<?=$color?>" style="color:#000000;">
											<td align="center" valign="top" style="border-bottom:thin dashed #0a5372;">
												<?=++$counter?><br/>
												#<?=$id?>
											</td>
											<td align="left" valign="top" style="border-bottom:thin dashed #0a5372; text-decoration:<?=$textDecoration?>;">
												<?=$userIcon?>&nbsp;<b><?=strtoupper($row['user_full_name'])?></b> 
												<br />	
												<i class="fa fa-id-badge" aria-hidden="true" style="color:<?=$otherIconColor?>;" title="Registration Id"></i>&nbsp;<?=$row['user_registration_id']?>
												<br />
											<?
												if($row['user_type']=='DELEGATE')
												{
											?>
												<i class="fa fa-universal-access" aria-hidden="true"  style="color:<?=$otherIconColor?>;" title="Unique Sequence No."></i>&nbsp;<?=strtoupper($row['user_unique_sequence'])?>	
												<br />
											<?
													if($row['registration_request']!='EXHIBITOR')
													{
											?>
												<i class="fa fa-phone" aria-hidden="true" style="color:<?=$otherIconColor?>;" title="Mobile"></i>&nbsp;<?=$row['user_mobile_no']?>
												<br />
												<i class="fa fa-envelope" aria-hidden="true" style="color:<?=$otherIconColor?>;" title="Email"></i>&nbsp;<?=$row['user_email_id']?>
												<br />
											<?
													}
												}
												
												if($row['registration_request']=='GENERAL')
												{
											?>
												<i class="fa fa-scissors" aria-hidden="true" style="color:<?=$otherIconColor?>;" title="Cutoff"></i>&nbsp;<?=getCutoffName($row['registration_tariff_cutoff_id'])?>
												<br/>
											<?
												}
											?>
												<i class="fa fa-clock-o" aria-hidden="true" style="color:<?=$otherIconColor?>;" title="Recording Date"></i>&nbsp;<?=date('d/m/Y h:i A', strtotime($row['created_dateTime']))?>																			
											<?
												if($row['account_status']=="UNREGISTERED")
												{
											?>
												<br><span style="color:#D41000; font-weight:bold;">UNREGISTERED</span>
											<?php
												}
											?>
											</td>												
											<td align="left" valign="top" use="registrationDetailsList" userId="<?=$id?>" style="border-bottom:thin dashed #0a5372; padding:0;">
												<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" />
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
			</table>
	<?php
	}
	?>