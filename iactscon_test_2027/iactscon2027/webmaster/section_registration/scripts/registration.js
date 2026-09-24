$(document).ready(function(){
	
	jBaseUrl = jsBASE_URL;				   
	$("#accommodation_hotel_id, #accommodation_roomType_id, #check_in_date, #check_out_date, #booking_quantity").change(function(){
		calculateTotalTariffAccomAmount();
	});
	$("tr[operetionMode=residenTariffTr]").hide();
	var default_hotal_id = $("select[operationMode=hotel_select_id]").val();
	$("tr[operetionMode=residenTariffTr][hotel_id="+default_hotal_id+"]").show();
	
	$("input[type=checkbox][operationMode=discountCheckbox]").click(function(){
																		//	 alert(22);
			if($(this).is(":checked")){
				
				$('tr[operationMode=discount]').css("display","table-row");
				$("input[type=text][operationMode=discountAmount]").focus();				
			}
			else
			{

				$('tr[operationMode=discount]').css("display","none");
				$("input[type=text][operationMode=discountAmount]").val('');
				var serviceType = $('input[type=hidden][id=type]').attr('value');
				//alert(serviceType);
				if(serviceType == 'Workshop')
				{
					calculationWorkshopAmount();
				}
				else if(serviceType == 'Accompny')
				{
					calculateAccompanyAmount();
				}
				if(serviceType == 'Accommodation')
				{
					calculateTotalTariffAmount();
				}
				if(serviceType == 'registrationSummery')
				{
					calculationRegistrationSummeryAmount();
				}
				//calculationWorkshopAmount();
			}
																			 
	 });
	$("select[operationMode=hotel_select_id]").each(function(){
		$(this).change(function(){
			var hotal_id = $(this).val();
			$("tr[operetionMode=residenTariffTr]").hide();
			$("tr[operetionMode=residenTariffTr][hotel_id="+hotal_id+"]").show();
		});
	 });
	$("input[type=checkbox][operationMode=registration_tariff]").each(function(){
		$(this).click(function(){
			// alert(1)
			/*var emptyUserDetails = true;
			try{
				if(UPDATING_ABSTRACT_USER){
					emptyUserDetails = false;
				}
			}catch(e){}
			
			if(emptyUserDetails)
			{
				userdetails();
			}*/
			if($(this).is(":checked"))
			{
				var regClsfId = $(this).val();
				if(regClsfId ==1 || regClsfId == 2 )
				{
					$('#membership_number').prop("disabled",false);
				}
				else
				{
				
					$('#membership_number').prop("disabled",true);
				}
			}
			
		});
	});
	$("select[forType=country]").change(function(){
		countryId  = $(this).val();
		generateSateList(countryId,jBaseUrl);
	});
	$("input[type=text][forType=mobileValidate]").blur(function(){
		var validteUserDetails = true;
		try{
			if(UPDATING_ABSTRACT_USER) validteUserDetails = false;
		}catch(e){}
		
		if(validteUserDetails || $(this).attr('VALDATD') != 'Y')
		{
			mobile  = $(this).val();
			checkMobileValidation(mobile,jBaseUrl);
		}
		
	});
	$("input[type=radio][operationMode=registration_type]").change(function(){
		var regType = $(this).val();
		var type 	= $("#type").val();
		if(regType=='GENERAL')
		{
			if(type=='Accompany')
			{
				calculateAccompanyAmount();
			}
			if(type=='Workshop')
			{
				calculationWorkshopAmount2();
			}
		}
		if(regType=='ZERO_VALUE')
		{
			$("#amount").text("0.00");
		}
	});
	
});

function validateWorkshop()
{	
	var accessValidation = true;
	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	
	if(discount>0)
	{		
		if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 
		
		accessValidation = false;
		return false;
		}		
	}	
		
	var paymentMode = $('#payment_mode').val();	
	
	if(paymentMode == 'Cheque')
	{	
		if(fieldNotEmpty('#cheque_number', "Please Enter cheque number") == false){ 
			
			accessValidation = false;
			return false;
		}	
		
		if(fieldNotEmpty('#cheque_drawn_bank', "Please Enter cheque drawee bank") == false){ 
			
			accessValidation = false;
			return false;
		}
	}	
	
	if(paymentMode == 'Draft')
	{	
		if(fieldNotEmpty('#draft_number', "Please Enter draft number") == false){ 
			
			accessValidation = false;
			return false;
		}	
		
		if(fieldNotEmpty('#draft_drawn_bank', "Please Enter draft drawee bank") == false){ 
			
			accessValidation = false;
			return false;
		}
	}	
	
	if(paymentMode == 'NEFT')
	{	
		if(fieldNotEmpty('#neft_bank_name', "Please Enter neft bank name") == false){ 
			
			accessValidation = false;
			return false;
		}	
		
		if(fieldNotEmpty('#neft_transaction_no', "Please Enter neft transaction no") == false){ 
			
			accessValidation = false;
			return false;
		}
	}
	
	if(paymentMode == 'RTGS')
	{	
		if(fieldNotEmpty('#rtgs_bank_name', "Please Enter rtgs bank name") == false){ 
			
			accessValidation = false;
			return false;
		}	
		
		if(fieldNotEmpty('#rtgs_transaction_no', "Please Enter rtgs transaction no") == false){ 
			
			accessValidation = false;
			return false;
		}
	}
	
	if(paymentMode == 'CARD')
	{	
		if(fieldNotEmpty('#card_number', "Please Enter card number") == false){ 
			
			accessValidation = false;
			return false;
		}
	}
	
	return accessValidation;
}



function openrofileDetailsPopUp()
{
	 //$("#fade_popup").slideToggle(500);
	 $("#fade_popup").toggle(500);
	 $("#popup_profile_full_details").toggle(500);
}

function closeProfileDetailsPopUp()
{
	$("#popup_profile_full_details").toggle(1000);
	$("#fade_popup").toggle(1000);
}

function openInvoiceDetailsPopup()
{
	$("#invoiceDetails").toggle(1000);
}

function openPaymentPopup(delegateId,slipId,paymentId,isRid='N',userREGtype)
{

	$("#fade_popup").fadeIn(1000);
	$("#payment_popup").fadeIn(1000);
	$('#payment_popup').html('<div style="text-align:center;"><img src="http://localhost/kasscon/dev/developer/webmaster/images/loader.gif"/></div>');
	console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/registration.process.php?act=paymentDetails&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId+'&userREGtype='+userREGtype);
	
	$.ajax({
			type: "POST",
			url: 'registration.process.php',
			data: 'act=paymentDetails&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId+'&userREGtype='+userREGtype,
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


function validatePaymentTermsSubmission(form)
{    
	//alert(66);
	//var serviceType = $('input[type=hidden][id=type]').attr('value');
	//alert(serviceType);
	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	
	if(discount>0)
	{		
		if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 
		
		accessValidation = false;
		return false;
		}		
	}	
	//return false;    
	var paymode = $(form).find("select[use=payment_mode]");
	var validationStatus = true;
	
	if($(paymode).val()=='')
	{
		alert('Select Payment Terms');
		//cssAlert(paymode,"Select Payment Terms");
		validationStatus = false;
		return false;
	}
	// alert($(paymode).val());
	if($(paymode).val()=='Cash')
	{ 
		var cash_deposit_date = $(form).find("input[type=text][use=cash_deposit_date]");
		if($(cash_deposit_date).val()=='')
		{
			alert('Select Cash Deposit Date');
			//cssAlert(cash_deposit_date,"Select Cash Deposit Date");
			validationStatus = false;
			return false;
		}
	} 
	else if($(paymode).val()=='Cheque')
	{
		var cheque_number = $(form).find("input[type=number][use=cheque_number]");
		if($(cheque_number).val()=='')
		{
			alert('Enter Cheque No. Only Numeric Value');
			//cssAlert(cheque_number,"Enter Cheque No.");
			validationStatus = false;
			return false;
		}
		
		var cheque_drawn_bank = $(form).find("input[type=text][use=cheque_drawn_bank]");
		if($(cheque_drawn_bank).val()=='')
		{
			alert('Enter Drawee Bank');
			//cssAlert(cheque_drawn_bank,"Enter Drawn Bank");
			validationStatus = false;
			return false;
		}
		
		var cheque_date = $(form).find("input[type=text][use=cheque_date]");
		if($(cheque_date).val()=='')
		{
			alert('Enter Cheque Date');
			//cssAlert(cheque_date,"Enter Cheque Date.");
			validationStatus = false;
			return false;
		}
	}
	else if($(paymode).val()=='Draft')
	{
		var draft_number = $(form).find("input[type=text][use=draft_number]");
		if($(draft_number).val()=='')
		{
			alert('Enter Draft No');
			//cssAlert(draft_number,"Enter Draft No.");
			validationStatus = false;
			return false;
		}
		
		var draft_drawn_bank = $(form).find("input[type=text][use=draft_drawn_bank]");
		if($(draft_drawn_bank).val()=='')
		{
			alert('Enter Drawee Bank');
			//cssAlert(draft_drawn_bank,"Enter Drawn Bank.");
			validationStatus = false;
			return false;
		}
		
		var draft_date = $(form).find("input[type=text][use=draft_date]");
		if($(draft_date).val()=='')
		{
			alert('Enter Draft Date');
			//cssAlert(draft_date,"Enter Draft Date.");
			validationStatus = false;
			return false;
		}
	}
	else if($(paymode).val()=='NEFT')
	{
		var neft_bank_name = $(form).find("input[type=text][use=neft_bank_name]");
		if($(neft_bank_name).val()=='')
		{
			alert('Enter Drawee Bank');
			//cssAlert(neft_bank_name,"Enter Drawn Bank.");
			validationStatus = false;
			return false;
		}
		
		var neft_date = $(form).find("input[type=text][use=neft_date]");
		if($(neft_date).val()=='')
		{
			alert('Enter Date');
			//cssAlert(neft_date,"Enter Date.");
			validationStatus = false;
			return false;
		}
		
		var neft_transaction_no = $(form).find("input[type=text][use=neft_transaction_no]");
		if($(neft_transaction_no).val()=='')
		{
			alert('Enter Transaction Id');
			//cssAlert(neft_transaction_no,"Enter Transaction Id.");
			validationStatus = false;
			return false;
		}
	}
	else if($(paymode).val()=='RTGS')
	{    
		var rtgs_bank_name = $(form).find("input[type=text][use=rtgs_bank_name]");    alert($(rtgs_bank_name).val());
		if($(rtgs_bank_name).val()=='')
		{
			alert('Enter Drawee Bank');
			//cssAlert(rtgs_bank_name,"Enter Drawn Bank.");
			return false;
		}
		
		var rtgs_date = $(form).find("input[type=text][use=rtgs_date]");
		if($(rtgs_date).val()=='')
		{
			alert('Enter Date');
			//cssAlert(rtgs_date,"Enter Date.");
			validationStatus = false;
			return false;
		}
		
		var rtgs_transaction_no = $(form).find("input[type=text][use=rtgs_transaction_no]");
		if($(rtgs_transaction_no).val()=='')
		{
			alert('Enter Transaction Id');
			//cssAlert(rtgs_transaction_no,"Enter Transaction Id.");
			validationStatus = false;
			return false;
		}
	}
	
	else if($(paymode).val()=='CARD')
	{ 
		var card_number = $(form).find("input[type=number][use=card_number]");   //alert($(card_number).val());
		if($(card_number).val()=='')
		{
			alert('Enter Card No. Only Numeric Value');
			//cssAlert(cheque_number,"Enter Cheque No.");
			validationStatus = false;
			return false;
		}
		
		var card_date = $(form).find("input[type=text][use=card_date]");
		if($(card_date).val()=='')
		{
			alert('Enter Card Date');
			//cssAlert(cheque_date,"Enter Cheque Date.");
			validationStatus = false;
			return false;
		}
	}
	return validationStatus;
}

function openSetPaymentTermsPopup(delegateId,slipId,paymentId)
{
	$("#fade_popup").fadeIn(1000);
	$("#SetPaymentTermsPopup").fadeIn(1000);
	$('#SetPaymentTermsPopup').html('<div style="text-align:center;"><img src="http://localhost/kasscon/dev/developer/webmaster/images/loader.gif"/></div>');
	console.log('http://localhost/kasscon/dev/developer/webmaster/section_registration/registration.process.php?act=setNewPaymentTerms&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId    );
	$.ajax({
			type: "POST",
			url: 'registration.process.php',
			data: 'act=setNewPaymentTerms&delegateId='+delegateId+'&slipId='+slipId+'&paymentId='+paymentId,
			dataType: 'html',
			async: false,
			success:function(returnMessage)
			{
				$('#SetPaymentTermsPopup').html(returnMessage);
			}
	});
	
}

function closeSetPaymentTermsPopup()
{
	$("#SetPaymentTermsPopup").fadeOut();
	$("#fade_popup").fadeOut();
}

function closePaymentPaymentPopUp()
{
	$("#payment_popup").fadeOut();
	$("#fade_popup").fadeOut();
}

function backEndFromValidation()
{ 	
	var complementaryStatus = $('#registration_complementary').attr('value');	// Y/N
	
	accessValidation	 = true;
	
	if(fieldNotEmpty('#registration_cutoff', "Please Select Cutoff..") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	
	var registration_tariff = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
	var residential_tariff = $("input[type=checkbox][operationMode=residential_tariff]:checked").length;
	//var regId = $('input[type=checkbox][operationMode=registration_tariff]').val();
	if($("input[type=checkbox][operationMode=registration_tariff]").length > 0 && $("input[type=checkbox][operationMode=residential_tariff]").length > 0 && registration_tariff < 1 && residential_tariff < 1){ 
		$('input[type=checkbox][operationMode=registration_tariff]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=registration_tariff]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=registration_tariff]').css('outline-width', 'thin');
		$("html, body").animate({ scrollTop: 50 }, 1000);
		alert('Please Select Conference');
		
		$('input[type=checkbox][operationMode=residential_tariff]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=residential_tariff]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=residential_tariff]').css('outline-width', 'thin');
		$("html, body").animate({ scrollTop: 50 }, 1000);
		alert('Please Select Residential Package');
		
		accessValidation	 = false;
		return false; 
	}
	
	if(fieldNotEmpty('#user_email_id', "Please Enter Email") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if($('#email_id_validation').val()=="IN_USE"){
		alert('Email Id Already In Use');
		accessValidation	 = 0;
		return false;
	}
		
	if(fieldNotEmpty('#user_password', "Please Enter Password") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#user_first_name', "Please Enter First Name") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#user_last_name', "Please Enter Last Name") == false){ 
		accessValidation	 = false;
		return false; 
	}
	if(complementaryStatus != 'Y')
	{
		if(fieldNotEmpty('#user_address', "Please Enter Address") == false){ 
			
			accessValidation	 = false;
			return false; 
		}
		if(fieldNotEmpty('#user_country', "Please Select Country") == false){ 
			
			accessValidation	 = false;
			return false; 
		}
		if(fieldNotEmpty('#user_state', "Please Select State") == false){ 
			//alert("erter");
			
			accessValidation	 = false;
			return false; 
		}
		if(fieldNotEmpty('#user_city', "Please Enter City") == false){ 
			
			accessValidation	 = false;
			return false; 
		}
	}
	if(fieldNotEmpty('#user_mobile_no', "Please Enter Mobile No") == false)
	{ 
		accessValidation	 = false;
		return false; 
	}
	if(fieldShouldIntegerValidate("#user_mobile_no","Invalid Mobile Number") == false)
	{	
		accessValidation	 = false;
		return false;
	}
	if($('#mobile_validation').val() != "AVAILABLE"){
		
		$('#user_mobile_no').focus();
		$('#user_mobile_no').css('border-color','#D41000');
		//alert("Please Confirm Password Correctly");
		alert('Please Confirm Mobile Correctly');
		
		accessValidation	 = 0;
		return false;
	}
	if(complementaryStatus != 'Y')
	{
		if(fieldNotEmpty('#user_postal_code', "Please Enter Postal Code") == false){ 
			
			accessValidation	 = false;
			return false; 
		}
	}
		
	var checkValue = $("input[type=checkbox][operationmode=registration_tariff]:checked").val();
	
	if(checkValue == 11 || checkValue == 12)
	{}
	return accessValidation;	
}

function newBackEndFromValidation()
{ 	
	//alert('123');
	
	
	accessValidation	 = true;
	
	if($("input[type=checkbox][operationMode=Workshopclsf]:checked").length==0)
	{
		
		alert('rcg_');
		accessValidation	 = false;
		return false;
		
	}
	
	return accessValidation;	
}

function checkMembernumber()
{
	//var membershipNoObj = $(form).find("#membership_number");
	var membershipNo = $.trim($("#membership_number").val());
	var user_email_id  = $.trim($("#user_email_id").val());
	var status = false;
	var regTariffId = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
	//$(form).find('#membershipId').html('');
	
	if(user_email_id!="")
	{
		//$(form).find("#loaderImg").show();
		if(regularExpressionEmail.test(user_email_id)==false)
		{							
			$('#email_div').html('<span style="color:#D41000; margin-left: 10px;">Something went wrong. Please check the e-mail id.</span>');
			$("#loaderImg").hide();
		}
		else
		{	//userdetails();
			console.log(jsBASE_URL+'returnData.process.php?act=getEmailValidationStatus&MembershipNo='+membershipNo+'&regcla='+regTariffId+'&email='+user_email_id);	
		   $.ajax({
					type: "POST",
					url: jsBASE_URL+'returnData.process.php',
					data: 'act=getEmailValidationStatus&MembershipNo='+membershipNo+'&regcla='+regTariffId+'&email='+user_email_id,
					dataType: 'json',
					async: false,
					success:function(msg)
					{
							console.log(msg.STATUS);
							
						emailChooserPostAction(msg)
					}
				});
		}
	}
	
}

function emailChooserPostAction(JSONObject)
{

	if (JSONObject.STATUS == 'IN_USE')
	{
		$("div[emaildiv=email_div]").hide();
		
		$($("div[emaildiv=inUSEDiv]")).find("#user_details").val($($("div[emaildiv=email_div]")).find("#user_email_id").val());	
		
		$($("div[emaildiv=email_div]")).find("#user_email_id").val('');
		
		$("div[emaildiv=inUSEDiv]").show();
		$('#email_div').html('<span style="color:#D41000; margin-left: 10px;">Email already registered with us for AICC RCOG 2023.</span>');
	}
	else if (JSONObject.STATUS == 'NOT_MATCH')
	{
		userdetails();
		$('#email_div').html('<span style="color:#D41000; margin-left: 10px;">Please check the e-mail id & Membership Number. Your Details Not Register For RCOG Member.</span>');
	}
	else if (JSONObject.STATUS == 'NOT_PAID')
	{	
		userdetails();
		$("div[emaildiv=email_div]").hide();
		$("div[emaildiv=uppaidDiv]").find("#user_details").val($("div[emaildiv=email_div]").find("#user_email_id").val());	
		$("div[emaildiv=email_div]").find("#user_email_id").val('');
		$("div[emaildiv=uppaidDiv]").show();
		$('#email_div').html('<span style="color:#D41000;font-size: 13px;">Email already registered with us but the registration procedure remained incomplete.<br>To complete the same please contact with AICC RCOG Secretariat Ph no.- 03340015677, 8697064019 or 8335897369.<br>Time: 11:00 - 18:00.</span>');
	}
	else if (JSONObject.STATUS == 'AVAILABLE')
	{
		userdetails();
		$('#email_div').html('<span style="color:#009933; margin-left: 10px;">Available</span>');
		$('#email_id_validation').val('AVAILABLE');
		//$($("table[emaildiv=email_div]")).hide();	
		var registerDiv = $("div[emaildiv=userregform]");
		
			var JSONObjectData = JSONObject.DATA;
			console.log(JSONObject.STATUS);
			
			console.log(registerDiv);
			//$('#email_div').html('');
			$('#user_first_name').val(JSONObjectData.FIRST_NAME);
			$('#user_middle_name').val(JSONObjectData.MIDDLE_NAME);
			$('#user_last_name').val(JSONObjectData.LAST_NAME);
			$('#user_mobile_no').val(JSONObjectData.MOBILE_NO);
			$('#user_mobile_isd_code').val(JSONObjectData.MOBILE_ISD_CODE);
			checkMobileValidation(JSONObjectData.MOBILE_NO,jBaseUrl)
			$('#user_phone_no').val(JSONObjectData.PHONE_NO);
			$('#user_address').val(JSONObjectData.ADDRESS);
			$('#user_city').val(JSONObjectData.CITY);
			$('#user_postal_code').val(JSONObjectData.PIN_CODE);
			
			$('#user_country').val(JSONObjectData.COUNTRY_ID);											
			//$('#user_country').trigger("change");
			generateSateList(JSONObjectData.COUNTRY_ID,jBaseUrl);
			$('#user_state').val(JSONObjectData.STATE_ID);
	}
}

function userdetails()
{
	$('#user_first_name').val('');	
	$('#user_middle_name').val('');	
	$('#user_last_name').val('');	
	$('#user_mobile_no').val('');	
	//$('#user_usd_code').val('');	
	$('#user_phone_no').val('');	
	$('#user_address').val('');	
	$('#user_city').val('');	
	$('#user_postal_code').val('');	
	$('#user_country').val('');											
	$('#user_state').val('');
	$('#mobile_div').html('');
	$('#email_id_validation').val('');
}

function generateSateList(countryId,jBaseUrl)
{
	console.log(jBaseUrl+"returnData.process.php?act=generateStateList&countryId="+countryId);
	
	if(countryId!=""){
		$.ajax({
					type: "POST",
					url: jBaseUrl+"returnData.process.php",
					data: "act=generateStateList&countryId="+countryId,
					dataType: "html",
					async: false,
					success: function(JSONObject){
						$("select[forType=state]").html(JSONObject);
						$("select[forType=state]").removeAttr("disabled");
					}
		});
	}else{
		$("select[forType=state]").html('<option value="">-- Select Country First --</option>');
		$("select[forType=state]").attr("disabled","disabled");
	}
}

function checkMobileValidation(mobile,jBaseUrl)
{
	if(mobile!="")
	{
		if(isNaN(mobile) || mobile.toString().length !=10)
		{
			$('#mobile_div').html('<span style="color:#D41000">Invalid Mobile No '+mobile+'</span>');
			$('#mobile_validation').val('INVALID');
			$("div[use=loader]").remove();
			return false;
		}
		else
		{
			console.log(jsBASE_URL+'returnData.process.php?act=getMobileValidation&mobile='+mobile);
			$.ajax({
						type: "POST",
						url: jsBASE_URL+'returnData.process.php',
						data: 'act=getMobileValidation&mobile='+mobile,
						dataType: 'text',
						async: false,
						success:function(returnMessage)
						{
							returnMessage = returnMessage.trim();
							if(returnMessage == 'IN_USE')
							{
								$('#mobile_div').html('<span style="color:#FF0000">Mobile Number Already In Use</span>');
								$("#user_mobile_no").val("");
							}
							else
							{
								$('#mobile_div').html('<span style="color:#009933">Available</span>');
							}
							
							$('#mobile_validation').val(returnMessage);
							
							
						}
					});
			$("div[use=loader]").remove();
		}
		
	}
			
}

function mainRegistrationCalidation()
{
	
	return onSubmitAction(function(){
									
		accessValidation	 = true;

		
		var registration_type = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr("registrationType");
		var registration_tariff = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
		// commented below code by weavers as per the requirement, that workshop is not mandetory for account creation from backend 09.08.2022 start
		//var workshop_tariff = $("input[type=checkbox][operationMode=workshopId]:checked").length;
		// commented below code by weavers as per the requirement, that workshop is not mandetory for account creation from backend 09.08.2022 end
		if( $("input[type=checkbox][operationMode=registration_tariff]").length > 0 && registration_tariff < 1){ 
			$('input[type=checkbox][operationMode=registration_tariff]').css('outline-color', '#D41000');
			$('input[type=checkbox][operationMode=registration_tariff]').css('outline-style', 'solid');
			$('input[type=checkbox][operationMode=registration_tariff]').css('outline-width', 'thin');
			$("html, body").animate({ scrollTop: 50 }, 1000);
			alert("Please Select Conference");
			//cssAlert('table[use=registration_tariff]','Please Select Conference');
			
			accessValidation	 = false;
			return false; 
		}
		// commented below code by weavers as per the requirement, that workshop is not mandetory for account creation from backend 09.08.2022 start
		/*if($("input[type=checkbox][operationMode=workshopId]").length > 0 && workshop_tariff < 1){ 
			$('input[type=checkbox][operationMode=workshopId]').css('outline-color', '#D41000');
			$('input[type=checkbox][operationMode=workshopId]').css('outline-style', 'solid');
			$('input[type=checkbox][operationMode=workshopId]').css('outline-width', 'thin');
			$("html, body").animate({ scrollTop: 70 }, 1000);
			alert("Please Select Workshop");
			accessValidation	 = false;
			return false; 
		}*/
		// commented below code by weavers as per the requirement, that workshop is not mandetory for account creation from backend 09.08.2022 end
		if($('#email_id_validation').val()=="IN_USE"){
			alert("Please Check Your Email Id");
			//cssAlert('#user_email_id','Email Id Already In Use');
			accessValidation	 = 0;
			return false;
		}
		if($('#mobile_validation').val() != "IN_USE"){
			var user_mobile_no = $('#user_mobile_no').val();
			if(user_mobile_no=='')
			{
				$('#user_mobile_no').focus();
				$('#user_mobile_no').css('border-color','#D41000');
				alert("Please Check Your Mobile No");
				//cssAlert('#user_mobile','Please Confirm Mobile Correctly');
				
				accessValidation	 = 0;
				return false;
			}
			
		}
		return accessValidation;	
	});
}

function formAccompanyValidation()
{
	return onSubmitAction();
}

function formAccommodationValidation()
{
	accessValidation = true;
	
	if(fieldNotEmpty('#accommodation_hotel_id', "Please Select Hotel") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#check_in_date', "Please Select Check In Date") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#check_out_date', "Please Select Check Out Date") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#accommodation_roomType_id', "Please Select Room Type") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	return accessValidation;	
}

function calculateTotalTariffAccomAmount()
{
	
	var grandTariffAmount     = 0;	
	
	var hotelId	              = $("#accommodation_hotel_id").val();
	var roomTypeId	          = $("#accommodation_roomType_id").val();
	var check_in_date	      = $("#check_in_date").val();
	var check_out_date	      = $("#check_out_date").val();
	var booking_quantity	  = $("#booking_quantity").val();
	var cutOff				  = $("#cutOffId").val();
	
	if(hotelId!="" && roomTypeId!="" && check_in_date!=""&& check_out_date!="" && booking_quantity!=""){
		var tariffAmount      = $("input[operationMode=accommodation_tariff_details][packageId="+roomTypeId+"][inDates="+check_in_date+"][outDates="+check_out_date+"][cutOff="+cutOff+"]").val();
		
		var priceTag      	  = $("input[operationMode=accommodation_tariff_details][packageId="+roomTypeId+"][inDates="+check_in_date+"][outDates="+check_out_date+"][cutOff="+cutOff+"]").attr("priceTag");
		
		 grandTariffAmount   = parseFloat(tariffAmount) * parseFloat(booking_quantity);
		 grandTariffAmount   = grandTariffAmount.toFixed(2);
		 $("#amount").text(grandTariffAmount);
	}
	else{
		$("#amount").text('0.00');
	}
}

function disableTourNoOfPerson(id)
{
	if($('#tour_td_'+id).prop('checked'))
	{
		$('select[use=person_'+id+']').attr("disabled",false);
	}
	else
	{
		$('select[use=person_'+id+']').attr("disabled",true);
	}
	calculateTotalTourTariffAmount();
}

function calculateTotalTourTariffAmount()
{
	
	var grandTariffAmount         = 0;
	
	$("input[type=checkbox][operationMode=tour_td]:checked").each(function(){
		
		var packageId	          = $(this).val();
		var cutoffId	          = $("#cutoffId").val();
		
		if(packageId!=""){
			
			var bookingQuantity   = $("select[use=person_"+packageId+"]").val();
			
			var tariffAmount      = 0;
				tariffAmount      = $("input[operationMode=tour_tariff_details][packageId="+packageId+"][cutoffId="+cutoffId+"]").val();
			
			var totalTariffAmount = 0;
			    totalTariffAmount = parseFloat(tariffAmount) * parseFloat(bookingQuantity);
				
			    grandTariffAmount = parseFloat(grandTariffAmount) + parseFloat(totalTariffAmount);
		}
	});
	
	grandTariffAmount   = grandTariffAmount.toFixed(2);
	$("#amount").text(grandTariffAmount);
}

function tourformvalidation()
{
	accessValidation	 = true;
	if($("input[type=checkbox][operationMode=tour_td]:checked").length==0){
		
		$('input[type=checkbox][operationMode=tour_id]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=tour_id]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=tour_id]').css('outline-width', 'thin');
		alert('Please Select Atleast One Tour');
		accessValidation	 = false;
		return false;
	}
	return accessValidation;
}

function validateAddRegistration()
{		
	var accessValidation	 = true;
	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	
	if(discount>0)
	{	
		if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 	
			accessValidation = false;
			return false;
		}	
		
		var discountAmount = $("input[type=text][operationMode=discountAmount]");		
		if(isNaN((discountAmount).val()))
		{
			alert('Enter Discount Amount correctly');
			$(discountAmount).focus();
			accessValidation = false;
			return false;
		}
		
		var total = parseFloat($("#amount").text());	
		if(total <  parseFloat((discountAmount).val()))
		{
			alert('Enter Discount Amount correctly');
			$(discountAmount).focus();
			accessValidation = false;
			return false;
		}
	}
	
	var paymentType = $("#payment_mode").val();						
	
	if(paymentType == "Cash")
	{
		if(fieldNotEmpty('#cash_deposit_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}							
	}
	
	if(paymentType == "Cheque")
	{
		if(fieldNotEmpty('#cheque_number', "Please Enter Cheque Number") == false){ 				
			accessValidation	 = false;
			return false; 
		}
		
		if(fieldNotEmpty('#cheque_drawn_bank', "Please Enter Cheque Drawee Bank") == false){ 				
			accessValidation	 = false;
			return false; 
		}	
		
		if(fieldNotEmpty('#cheque_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}	
	}
	
	if(paymentType == "Draft")
	{
		if(fieldNotEmpty('#draft_number', "Please Enter Draft Number") == false){ 				
			accessValidation	 = false;
			return false; 
		}
		
		if(fieldNotEmpty('#draft_drawn_bank', "Please Enter Draft Drawee Bank") == false){ 				
			accessValidation	 = false;
			return false;
		}
		
		if(fieldNotEmpty('#draft_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}	
	}
	
	if(paymentType == "NEFT")
	{
		if(fieldNotEmpty('#neft_bank_name', "Please Enter Neft Bank Name") == false){ 				
			accessValidation	 = false;
			return false; 
		}
		
		if(fieldNotEmpty('#neft_transaction_no', "Please Enter neft transaction id") == false){ 				
			accessValidation	 = false;
			return false; 
		}	
		
		if(fieldNotEmpty('#neft_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}	
	}
	
	if(paymentType == "RTGS")
	{
		if(fieldNotEmpty('#rtgs_bank_name', "Please Enter Rtgs Bank Name") == false){ 				
			accessValidation	 = false;
			return false; 
		}
		
		if(fieldNotEmpty('#rtgs_transaction_no', "Please Enter Rtgs Transaction id") == false){ 				
			accessValidation	 = false;
			return false; 
		}
		
		if(fieldNotEmpty('#rtgs_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}
	}
	
	if(paymentType == "CARD")
	{
		if(fieldNotEmpty('#card_number', "Please Enter card number") == false){ 				
			accessValidation	 = false;
			return false; 
		}			
		
		if(fieldNotEmpty('#card_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}
	}
	
	if(paymentType == "Credit")
	{
		if(fieldNotEmpty('#credit_date', "Please select date") == false){ 				
			accessValidation	 = false;
			return false; 
		}		
		
		if(fieldNotEmpty('#exhibitor_name', "Please select exhibitor name") == false)
		{ 
			return false; 
		}
	}
	
	
	var ramount = $("#exhibitorRemainBal").text();
			
	return true;	
}

function openRemarkPopup(paymentRemark)
{
	var html = '<table width="100%" class="tborder"><tr><td class="tcat"><span style="float:left">Remarks</span><span class="close" onclick="closePaymentPaymentPopUp()">X</span></td></tr><tr><td width="20%" align="left">'+paymentRemark+'</td></tr></table>';
	$("#fade_popup").fadeIn(1000);
	$("#payment_popup").fadeIn(1000);
	$('#payment_popup').html("");
	$('#payment_popup').html(html);
	
	
}
