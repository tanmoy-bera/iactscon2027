$(document).ready(function(){
	$("input[type=checkbox][operationMode=workshop_cutoff]").each(function(){
		$(this).click(function(){
			
			if($(this).is(":checked"))
			{
				$("input[type=checkbox][operationMode=workshop_cutoff]").prop("checked",false);	
				$(this).prop("checked",true);
			}
			else
			{
				$(this).prop("checked",false);
			}
			
		});
	});
});

$(document).ready(function(){
	$("input[type=checkbox][operationMode=registration_cutoff]").each(function(){
		$(this).click(function(){
			
			if($(this).is(":checked"))
			{
				$("input[type=checkbox][operationMode=registration_cutoff]").prop("checked",false);	
				$(this).prop("checked",true);
			}
			else
			{
				$(this).prop("checked",false);
			}
			
		});
	});
});

$(document).ready(function(){
	$("input[type=checkbox][operationMode=registration_tariff]").each(function(){
		$(this).click(function(){
			
			if($(this).is(":checked"))
			{
				$("input[type=checkbox][operationMode=registration_tariff]").prop("checked",false);	
				$(this).prop("checked",true);
			}
			else
			{
				$(this).prop("checked",false);
			}
			formDisplayDecissionMaker();
			
		});
	});
});

$(document).ready(function(){
	$("input[type=checkbox][operationMode=workshop_tariff]").each(function(){
		$(this).click(function(){
			
			if($(this).is(":checked"))
			{
				$("input[type=checkbox][operationMode=workshop_tariff]").prop("checked",false);	
				$(this).prop("checked",true);
			}
			else
			{
				$(this).prop("checked",false);
			}
			formDisplayDecissionMaker();
			
		});
	});
});

function formDisplayDecissionMaker()
{
	$("#user_membership_no_add").prop("disabled",true);
	$("#companion_display_div").css("display", "none");
		
	$("input[operationMode=registration_tariff]").each(function(){
				
		if($(this).is(":checked"))
		{
			var registrationTariffId  = $(this).val();
			
			if(registrationTariffId==1)
			{
				$("#user_membership_no_add").prop("disabled",false);
				$("#companion_display_div").css("display", "block");
			}
			else if(registrationTariffId==2 || registrationTariffId==3 || registrationTariffId==4)
			{
				$("#user_membership_no_add").prop("disabled",true);
				$("#companion_display_div").css("display", "block");
			}
			else
			{
				$("#user_membership_no_add").prop("disabled",true);
				$("#companion_display_div").css("display", "none");
			}
		}
		
	});
}

/**********************************************************************/
/*                GET REGISTRATION TARIFF DETAILS CUSTOM              */
/**********************************************************************/
function getRegistrationTariffDetailsCustom(object)
{
	var registrationTypeId     = $("select[operationMode=registration_tariff]").val();
	var registrationCutOff     = $("select[operationMode=registration_cut_off]").val();
	
	if(registrationTypeId != "")
    {
		$.ajax({
					type: "POST",
					url: "general_registration.process.php",
					data: 'act=getRegistrationTariffDetailsCustom&registrationTypeId='+registrationTypeId+'&registrationCutOff='+registrationCutOff,
					dataType: "json",
					async: false,
					success: function(JSONObject){
					
						$("#registration_classification_id").val(JSONObject.PARENT_CLASSIFICATION_ID);
						$("#registration_tariff_amount").val(JSONObject.PARENT_TARIFF_AMOUNT);
						$("#accompany_classification_id").val(JSONObject.ACCOMPANY_CLASSIFICATION_ID);
						$("#accompany_tariff_amount").val(JSONObject.ACCOMPANY_TARIFF_AMOUNT);
						$("#registration_tariff_cutoff_id").val(JSONObject.PARENT_TARIFF_CUTOFF_ID);
						
						if(JSONObject.PARENT_CLASSIFICATION_ID == 1)
						{
							$("#user_membership_no_add").prop("disabled",false);
						} 
						else
						{
							$("#user_membership_no_add").prop("disabled",true);
						}
						
						if(JSONObject.PARENT_CLASSIFICATION_ID == 3)
						{
							$("#companion_display_div").css("display","none");
						}
						else
						{
							$("#companion_display_div").css("display","block");
						}
					}
			  });
    }
	else
	{
		resetRegistrationTariffDetails();
	}
}

/**********************************************************************/
/*                         ADD MORE TEMPLATE                          */
/**********************************************************************/
function addMoreTemplate(placeholderDiv, templateDiv)
{
	var totalTables  = parseInt($('#'+placeholderDiv+' table').length);
	var nextCounter  = totalTables + 1;
	var getTemplate  = $("#"+templateDiv).clone();
	var modifiedTemp = getTemplate.html().replace(/\#COUNTER/g,nextCounter);
	$('#'+placeholderDiv).append(modifiedTemp);
	removeTableRow();
	
	if(nextCounter>=4)
	{
		$('a[operationMode=addAccompanyButton]').css("display","none");
	}
}

function addMoreAccompany(placeholderDiv, templateDiv)
{
	addMoreTemplate(placeholderDiv, templateDiv);
}

/************************************************************************/
/*                           REMOVE HOTEL ADDMORE                       */
/************************************************************************/
function removeTableRow()
{
	$("a[operationMode=removeRow]").each(function(){
		$(this).click(function(){
			var sequenceBy = $(this).attr("sequenceBy");
			$("table[sequenceBy="+sequenceBy+"]").remove();
			
			var totalTables  = parseInt($('#addMoreAccompany_placeholder_add table').length);	
			var nextCounter  = totalTables + 1;
		
			if(nextCounter<5)
			{
				$('a[operationMode=addAccompanyButton]').css("display","block");
			}
		});
	});
}

/*********************************************************/
/*            OPEN OTHER FOOD TEXT AREA                  */
/*********************************************************/
function showOtherFoodTextBox(val)
{
	if(val == "OTHERS")
	{
        alert("1");
		$("#showOtherFoodtext").slideDown("fast");
	}
	else
	{
		$("#showOtherFoodtext").slideUp("fast");
	}
}

/*********************************************************/
/*                 OPEN PAYMENT POPUP                    */
/*********************************************************/
function openPaymentPopup(user_id, invoice_id)
{
	//alert(invoice_id);
	$("#fade_popup").fadeIn("slow");
	$("#popup_form").fadeIn("slow");
	
	if(user_id!="" && invoice_id!="")
	{
		console.log('http://localhost/icgst/dev/developer/webmaster/section_registration/general_registration.process.php?act=getPaymentDetails&user_id='+user_id+'&invoice_id='+invoice_id);
		$.ajax({
					type: "POST",
					url: "general_registration.process.php",
					data: 'act=getPaymentDetails&user_id='+user_id+'&invoice_id='+invoice_id,
					dataType: "json",
					async:false,
					success: function(JSONObject){
						
						paymentModeRetriver(JSONObject.PAYMENT_MODE);
						
						$("#invoice_id").val(JSONObject.INVOICE_ID);
						$("#payment_terms_id").val(JSONObject.PAYMENT_TERMS_ID);
						
						$("#isRegistrationService").val(JSONObject.IS_REGISTRATION_SERVICE);
						$("#isAccompanyService").val(JSONObject.IS_ACCOMPANY_SERVICE);
						$("#isWorkshopService").val(JSONObject.IS_WORKSHOP_SERVICE);
						
						$("#payment_amount").val(JSONObject.INVOICE_PENDING_AMOUNT);
						$("#payment_mode").val(JSONObject.PAYMENT_MODE);
						
						$("#cash_deposit_date").val(JSONObject.PAYMENT_CASH_DEPOSIT_DATE);
						
						$("#cheque_number").val(JSONObject.PAYMENT_CHEQUE_NUMBER);
						$("#cheque_drawn_bank").val(JSONObject.PAYMENT_CHEQUE_DRAWN_BANK);
						$("#cheque_date").val(JSONObject.PAYMENT_CHEQUE_DATE);
						
						$("#draft_number").val(JSONObject.PAYMENT_DRAFT_NUMBER);
						$("#draft_drawn_bank").val(JSONObject.PAYMENT_DRAFT_DRAWN_BANK);
						$("#draft_date").val(JSONObject.PAYMENT_DRAFT_DATE);
						
						$("#neft_bank_name").val(JSONObject.PAYMENT_NEFT_BANK_NAME);
						$("#neft_date").val(JSONObject.PAYMENT_NEFT_DATE);
						$("#neft_transaction_no").val(JSONObject.PAYMENT_NEFT_TRANSACTION_NO);
						
						$("#rtgs_bank_name").val(JSONObject.PAYMENT_RTGS_BANK_NAME);
						$("#rtgs_date").val(JSONObject.PAYMENT_RTGS_DATE);
						$("#rtgs_transaction_no").val(JSONObject.PAYMENT_RTGS_TRANSACTION_NO);
						
						$("td[forType=invoice_no]").text(JSONObject.INVOICE_NUMBER);
						$("td[forType=invoice_date]").text(JSONObject.INVOICE_DATE);
						$("td[forType=invoice_amount]").text(JSONObject.INVOICE_AMOUNT);
						$("td[forType=pending_amount]").text(JSONObject.INVOICE_PENDING_AMOUNT);
						$("td[forType=payment_mode]").text(JSONObject.PAYMENT_MODE);
						
						$("td[forType=cash_deposit_date]").text(JSONObject.PAYMENT_CASH_DEPOSIT_DATE);
						
						$("td[forType=cheque_number]").text(JSONObject.PAYMENT_CHEQUE_NUMBER);
						$("td[forType=cheque_drawn_bank]").text(JSONObject.PAYMENT_CHEQUE_DRAWN_BANK);
						$("td[forType=cheque_date]").text(JSONObject.PAYMENT_CHEQUE_DATE);
						
						$("td[forType=draft_number]").text(JSONObject.PAYMENT_DRAFT_NUMBER);
						$("td[forType=draft_drawn_bank]").text(JSONObject.PAYMENT_DRAFT_DRAWN_BANK);
						$("td[forType=draft_date]").text(JSONObject.PAYMENT_DRAFT_DATE);
						
						$("td[forType=neft_bank_name]").text(JSONObject.PAYMENT_NEFT_BANK_NAME);
						$("td[forType=neft_date]").text(JSONObject.PAYMENT_NEFT_DATE);
						$("td[forType=neft_transaction_no]").text(JSONObject.PAYMENT_NEFT_TRANSACTION_NO);
						
						$("td[forType=rtgs_bank_name]").text(JSONObject.PAYMENT_RTGS_BANK_NAME);
						$("td[forType=rtgs_date]").text(JSONObject.PAYMENT_RTGS_DATE);
						$("td[forType=rtgs_transaction_no]").text(JSONObject.PAYMENT_RTGS_TRANSACTION_NO);
						
						$("td[forType=payble_amount]").text(JSONObject.INVOICE_PENDING_AMOUNT);
						
					}
			  });
	}
}

/*********************************************************/
/*                 CLOSE PAYMENT POPUP                   */
/*********************************************************/
function closePaymentPaymentPopUp()
{
	$("#fade_popup").fadeOut("slow");
	$("#popup_form").fadeOut("slow");
}

/*********************************************************/
/*                 CLOSE PAYMENT POPUP                   */
/*********************************************************/
function openRemarkPopUp()
{
	$("#fade_popup").fadeOut("slow");
	$("#popup_form").fadeOut("slow");
}

/*********************************************************/
/*             CLOSE REGRADE PAYMENT POPUP               */
/*********************************************************/
function closeRegradePaymentPopUp()
{
	$("#fade_popup").fadeOut("slow");
	$("#popup_form_regradePayment").fadeOut("slow");
	
	$("#hotelName_regrade").text("");
}

/*********************************************************/
/*                   PROCEED PAYMENT                     */
/*********************************************************/
function proceedToPayment(actionValue)
{
	$("#payment_action").val(actionValue);
	$("#frmPaymentPopup").submit();
}

/*********************************************************/
/*               OFFLINE PAYMENT MODE POPUP              */
/*********************************************************/
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
	
	if(paymentType == "Draft")
	{
		$("#cashPaymentDiv").css("display","none");
		$("#chequePaymentDiv").css("display","none");
		$("#draftPaymentDiv").css("display","block");
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

/*********************************************************/
/*               OPEN PAYMENT TERMS POPUP                */
/*********************************************************/
function openPaymentTermsPopup(user_id, invoice_id)
{
	$("#fade_popup").fadeIn("slow");
	$("#popup_form_paymentTerms").fadeIn("slow");
	
	if(user_id!="" && invoice_id!="")
	{
		$.ajax({
					type: "POST",
					url: "general_registration.process.php",
					data: 'act=getPaymentDetails&user_id='+user_id+'&invoice_id='+invoice_id,
					dataType: "json",
					async:false,
					success: function(JSONObject){
						
						$("#invoice_id_paymentTerms").val(JSONObject.INVOICE_ID);
						
						$("td[forType=invoice_no_paymentTerms]").text(JSONObject.INVOICE_NUMBER);
						$("td[forType=invoice_date_paymentTerms]").text(JSONObject.INVOICE_DATE);
						$("td[forType=invoice_amount_paymentTerms]").text(JSONObject.INVOICE_AMOUNT);
						$("td[forType=pending_amount_paymentTerms]").text(JSONObject.INVOICE_PENDING_AMOUNT);
						
					}
			  });
	}	
}

/*********************************************************/
/*               CLOSE PAYMENT TERMS POPUP               */
/*********************************************************/
function closePaymentTermsPopup()
{
	$("#fade_popup").fadeOut("slow");
	$("#popup_form_paymentTerms").fadeOut("slow");
}

/*********************************************************/
/*           OFFLINE PAYMENT TERMS MODE POPUP            */
/*********************************************************/
function paymentTermsModeRetriver(paymentType)
{
	if(paymentType == "Cash")
	{
		$("#cashPaymentTermsDiv").css("display","block");
		$("#chequePaymentTermsDiv").css("display","none");
		$("#draftPaymentTermsDiv").css("display","none");
		$("#neftPaymentTermsDiv").css("display","none");
		$("#rtgsPaymentTermsDiv").css("display","none");
	}
	
	if(paymentType == "Cheque")
	{
		$("#cashPaymentTermsDiv").css("display","none");
		$("#chequePaymentTermsDiv").css("display","block");
		$("#draftPaymentTermsDiv").css("display","none");
		$("#neftPaymentTermsDiv").css("display","none");
		$("#rtgsPaymentTermsDiv").css("display","none");
	}
	
	if(paymentType == "Draft")
	{
		$("#cashPaymentTermsDiv").css("display","none");
		$("#chequePaymentTermsDiv").css("display","none");
		$("#draftPaymentTermsDiv").css("display","block");
		$("#neftPaymentTermsDiv").css("display","none");
		$("#rtgsPaymentTermsDiv").css("display","none");
	}
	
	if(paymentType == "NEFT")
	{
		$("#cashPaymentTermsDiv").css("display","none");
		$("#chequePaymentTermsDiv").css("display","none");
		$("#draftPaymentTermsDiv").css("display","none");
		$("#neftPaymentTermsDiv").css("display","block");
		$("#rtgsPaymentTermsDiv").css("display","none");
	}
	
	if(paymentType == "RTGS")
	{
		$("#cashPaymentTermsDiv").css("display","none");
		$("#chequePaymentTermsDiv").css("display","none");
		$("#draftPaymentTermsDiv").css("display","none");
		$("#neftPaymentTermsDiv").css("display","none");
		$("#rtgsPaymentTermsDiv").css("display","block");
	}
}

/******************************************************************************/
/*                        REGISTRATION FORM VALIDATION                        */
/******************************************************************************/
function validation()
{
	var workshopSelectedCount     = $("input[operationMode=workshop_tariff]:checked").length;
	var registrationSelectedCount = $("input[operationMode=registration_tariff]:checked").length;
	var residential_tariff = $("input[operationMode=residential_tariff]:checked").length;
	
	if(workshopSelectedCount==0 && registrationSelectedCount==0 && residential_tariff == 0)
	{
		alert("Please Check Atleast One Category");
		return false;
	}
	
	if($("#operation_mode").val()=="CUSTOM")
	{
		var workshopCutoff        = $("input[operationMode=workshop_cutoff]:checked").length;
		if(workshopCutoff==0 && workshopSelectedCount>0)
		{
			alert("Please Check Workshop Cutoff");
			return false;
		}
		
		var registrationCutoff   = $("input[operationMode=registration_cutoff]:checked").length;
		if(registrationCutoff==0 && registrationSelectedCount>0)
		{
			alert("Please Check Registration Cutoff");
			return false;
		}
	}
	
	if(fieldNotEmpty('#user_email_id_add', "Please Enter Email Id") == false){ return false; }
	
	if(fieldShouldEmailValidate('#user_email_id_add', "Please Provide Valid Email Id") == false){ return false; }
	if($('#email_id_validation').val()=="IN_USE")
	{
		$('#user_email_id_add').focus();
		$('#user_email_id_add').css('border-color','#D41000');
		alert("Email Id Already In Use");
		return false;
	}
	if(fieldNotEmpty('#user_password_add', "Please Enter Password") == false){ return false; }
	if($('#password_validation').val()=="IN_USE")
	{
		$('#user_password_add').focus();
		$('#user_password_add').css('border-color','#D41000');
		alert("Insecure Password");
		return false;
	}
	if(fieldNotEmpty('#user_filtration_id_add', "Please Select Registration Type") == false){ return false; }
	if(fieldNotEmpty('#user_first_name_add', "Please Enter First Name") == false){ return false; }
	if(fieldNotEmpty('#user_last_name_add', "Please Enter Last Name") == false){ return false; }
	
	if(fieldNotEmpty('#user_address_add', "Please Enter Address") == false){ return false; }
	if(fieldNotEmpty('#user_country_add', "Please Select Country") == false){ return false; }
	if(fieldNotEmpty('#user_state_add', "Please Select State") == false){ return false; }
	if(fieldNotEmpty('#user_city_add', "Please Enter City") == false){ return false; }
	
	if(fieldNotEmpty('#user_mobile_add', "Please Enter Mobile No") == false){ return false; }
	if(isNaN($('#user_mobile_add').val()))
	{
		$('#user_mobile_add').focus();
		$('#user_mobile_add').css('border-color','#D41000');
		alert("Invalid Mobile Number");
		return false;
	}
	
	if(fieldNotEmpty('#user_postal_code_add', "Please Enter Postal Code") == false){ return false; }
}

$(document).ready(function(){
	$('#user_email_id_add').blur(function(){
		checkEmailValidation('#user_email_id_add');
	});
	$('#user_password_add').blur(function(){
		checkPasswordValidation('#user_email_id_add','#user_password_add');
	});
});

function checkEmailValidation(emailControl)
{
	$(document).ready(function(){
							   
		var user_email    = $(emailControl).val();
		
		$('#email_id_validation').val("");
		$('#email_div').html("");
		
		if(user_email!="")
		{
			if(regularExpressionEmail.test($(emailControl).val())==false)
			{
				$('#email_div').html('<span style="color:#D41000">Invalid Email Id</span>');
				$('#email_id_validation').val('INVALID');
				$("#bttnSubmitStep1").prop('disabled', true);
				return false;
			}
			else
			{
				$.ajax({
							type: "POST",
							url: 'general_registration.process.php',
							data: 'act=getEmailValidation&email='+user_email,
							dataType: 'text',
							async: false,
							success:function(returnMessage)
							{
								returnMessage = returnMessage.trim();
								if(returnMessage == 'IN_USE')
								{
									$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
									$("#bttnSubmitStep1").prop('disabled', true);
								}
								else 
								{
									$('#email_div').html('<span style="color:#009933">Available</span>');
									$("#bttnSubmitStep1").prop('disabled', false);
								}
								$('#email_id_validation').val(returnMessage);
							}
				});
			}
		}
			
	});
}

function checkPasswordValidation(emailControl, passwordControl)
{
	$(document).ready(function(){
							   
		var user_email    = $(emailControl).val();
		var user_password = $(passwordControl).val();
		
		if(user_email=="")
		{
			alert("Please Enter Email Id");
			$(passwordControl).val("");
			$(passwordControl).focus();
			return false;
		}
		if(user_email!="" && user_password!="")
		{
			$.ajax({
						type: "POST",
						url: 'general_registration.process.php',
						data: 'act=getUniqueLogin&email='+user_email+'&password='+user_password,
						dataType: 'text',
						async: false,
						success:function(returnMessage)
						{
							returnMessage = returnMessage.trim();
							if(returnMessage == 'IN_USE')
							{
								$('#password_div').html('<span style="color:#FF0000">Insecure Password</span>');
							}
							else
							{
								$('#password_div').html('');
							}
							$('#password_validation').val(returnMessage);
						}
			});
		}
	});
}

$(document).ready(function(){
	
	$("input[type=radio][operationMode=registration_type]").change(function(){
		var registrationType = $("input[type=radio][operationMode=registration_type]:checked").val();
		
		if(registrationType=="COMPLIMENTARY")
		{
			$("div[operationMode=paymentTermsSetUnit]").css("display", "none");
		}
		else
		{
			$("div[operationMode=paymentTermsSetUnit]").css("display", "block");
		}
	});
	
});

/******************************************************************************/
/*                    REGISTRATION SUMMARY FORM VALIDATION                    */
/******************************************************************************/
function validateRegistrationSummarySubmission(obj)
{
	var form = $(obj);
	
	var act = $(form).find('input[type=hidden][name=act]').val();
		
	if(act=="zero_value_registration")
	{
		return true;
	}
	else
	{
		try
		{
			return validatePaymentTermsSubmission(form);
		}
		catch(e)
		{
			console.log(e.message);
			return false;
		}
	}
	return true;
	
}

function openResetPasswordPopup()
{
	$("#popup_form_password").fadeIn("slow");
}

function closeResetPasswordPopup()
{
	$("#popup_form_password").fadeOut("slow"); 
}


function validateConfaranceSubmission()
{
	alert("OKKKKKKKKKKKKKKkkk");
	if(fieldNotEmpty('#registration_cutoff', "Please Select One Cut Off") == false){ return false; }
	
	var registrationSelectedCount = $("input[operationMode=registration_tariff]:checked").length;
	//alert(totalCount);
	if(registrationSelectedCount == "0"){
		alert("Please Check Atleast One Registration Category");
		return false;
	}
	
	var form = $('#frmApplyForConfarance');
	
	var registrationType = $("input[type=radio][operationMode=registration_type]:checked").val();			
	if(registrationType=="COMPLIMENTARY" || registrationType=="ZERO_VALUE")
	{
		return true;
	}
	else
	{
		try
		{
			return validatePaymentTermsSubmission(form);
		}
		catch(e)
		{
			console.log(e.message);
			return false;
		}
	}
}