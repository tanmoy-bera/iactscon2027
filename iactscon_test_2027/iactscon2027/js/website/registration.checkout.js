$(document).ready(function(){
	calculateTotalAmount();
	formValidation();
	
	$("#user_mobile").blur(function(){									
		mobile  = $(this).val();
		$('#mobile_div').html('<img src="images/loadinfo.net.gif" style="margin-left: 10px;" id="loaderImg"/>');
		setTimeout(function(){checkMobileValidation(mobile,jsBASE_URL);}, 2000);
		
	});
	
});

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
								$("#user_mobile").val("");
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

function helpNote(ob,x)
{
	//val div = $(this);
	//alert(div);
	//this div will be inside helpkit
	//<div style=" background-image:url(images/uparrow.png);height: 20px; width: 20px; float: right;margin-right: 23%;left: 60%;position: absolute;"></div>
	var helpkit = '<div id="note" style="background-color:#47a9dd;color:#FFFFFF; position: absolute;left: 60%;padding: 5px;border-radius: 7px; max-width:36%;z-index: 10;margin-top: 7px;">'+x+'</div>';
		
	$(ob).closest("span").after(helpkit);
}

function removeHelpNote()
{
	$("#note").remove();
}

/**********************************************************************/
/*                        CALCULATE TOTAL AMOUNT                      */
/**********************************************************************/
function calculateTotalAmount()
{
	var totalSum        = 0;
	
	if($("#registrationTariffAmount").val()!="")
	{
		totalSum        = parseFloat(totalSum) + parseFloat($("#registrationTariffAmount").val());
	}
	$("input[operationMode=workshopTariffAmount]").each(function(){
		
		if($(this).val()!="")
		{
			totalSum    = parseFloat(totalSum) + parseFloat($(this).val());
		}
	});
	
	serviceTaxAmount    = parseFloat((totalSum * 0)/100);
	totalSum            = parseFloat(totalSum) + parseFloat(serviceTaxAmount);
	
	$("#amount").text("");
	$("#amount").text(totalSum.toFixed(2));
}

function formValidation()
{
	$("#registerForm").submit(function(){
		
		var accessValidation = 1;
		var permission       = 1;
		
		if(formOtherFieldValidation()==0){ 
			
			accessValidation = 0;
			return false; 
		}
	});
}

/******************************************************************************/
/*                        REGISTRATION FORM VALIDATION                        */
/******************************************************************************/
function formOtherFieldValidation()
{
	
	var accessValidation	 = 1;
	
	if($('#mobile_validation').val() != "AVAILABLE"){		
		$('#user_mobile').focus();
		$('#user_mobile').css('border-color','#D41000');
		//alert("Please Confirm Password Correctly");
		//cssAlert('#user_mobile','Please Confirm Mobile Correctly');		
		accessValidation	 = 0;
		return false;
	}
	if($("input[type=checkbox][groupName=user_food_choice]").length>0)
	{
		if($("input[type=checkbox][groupName=user_food_choice]:checked").length==0){
			cssAlert('input[type=checkbox][groupName=user_food_choice]','Please Select Food Preference');
			$('input[type=checkbox][groupName=user_food_choice]').css('outline-color', '#D41000');
			$('input[type=checkbox][groupName=user_food_choice]').css('outline-style', 'solid');
			$('input[type=checkbox][groupName=user_food_choice]').css('outline-width', 'thin');		
			accessValidation	 = 0;
			return false;
		}
	}
	return accessValidation;
}

function emailCheck(form)
{
	var emailIdObj = $(form).find("#user_email_id");
	var emailId = $.trim($(emailIdObj).val());
	var emailIdObjContainer = $(emailIdObj).parent().closest("td");
	$(emailIdObjContainer).find("img[use=loaderImg]").show();
	
	console.log(jsBASE_URL+'returnData.process.php?act=getEmailValidationStatus&email='+emailId);			
	setTimeout(function(){
	   $.ajax({
				type: "POST",
				url: jsBASE_URL+'returnData.process.php',
				data: 'act=getEmailValidationStatus&email='+emailId,
				dataType: 'json',
				async: false,
				success:function(JSONObject)
				{
					console.log(JSONObject.STATUS);
					emailChooserPostAction(JSONObject);
					$(emailIdObjContainer).find("img[use=loaderImg]").hide();
				}
			});
	 },500);
	
	/*
	var emailIdObj = $(form).find("#user_email_id");
	var emailId = $.trim($(emailIdObj).val());
	$(form).find('#email_div').html('');
	if($.trim(emailId)!="")
	{
		$(form).find("#loaderImg").show();
		if(regularExpressionEmail.test(emailId)==false)
		{						
			$(form).find('#email_div').html('<span style="color:#D41000; margin-left: 10px;">Something went wrong. Please check the e-mail id.</span>');
			$(form).find("#loaderImg").hide();
		}
		else
		{						
			console.log(jsBASE_URL+'returnData.process.php?act=getEmailValidationStatus&email='+emailId+'&MembershipNo='+MembershipNo);			
			setTimeout(function(){
						   $.ajax({
									type: "POST",
									url: jsBASE_URL+'returnData.process.php',
									data: 'act=getEmailValidationStatus&email='+emailId+'&MembershipNo='+MembershipNo,
									dataType: 'json',
									async: false,
									success:function(JSONObject)
									{
										console.log(JSONObject.STATUS);
										emailChooserPostAction(JSONObject);
										$($("div[emaildiv=email_div]")).find("#loaderImg").hide();
									}
								});
						 },500);
		}
	}*/
	return false;
}

function emailChooserPostAction(JSONObject)
{
	//alert(JSONObject.STATUS);
	if (JSONObject.STATUS == 'IN_USE')
	{
		$("div[emaildiv=email_div]").hide();
		
		$($("div[emaildiv=inUSEDiv]")).find("#user_details").val($($("div[emaildiv=email_div]")).find("#user_email_id").val());	
		
		$($("div[emaildiv=email_div]")).find("#user_email_id").val('');
		
		$("div[emaildiv=inUSEDiv]").show();
	}
	
	else if (JSONObject.STATUS == 'NOT_PAID')
	{
		$("div[emaildiv=email_div]").hide();
		$("div[emaildiv=uppaidDiv]").find("#user_details").val($("div[emaildiv=email_div]").find("#user_email_id").val());	
		$("div[emaildiv=email_div]").find("#user_email_id").val('');
		$("div[emaildiv=uppaidDiv]").show();
	}
	else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE')
	{
		$("div[emaildiv=email_div]").hide();
		$("div[emaildiv=uppaidOfflineDiv]").find("#user_details").val($("div[emaildiv=email_div]").find("#user_email_id").val());	
		$("div[emaildiv=email_div]").find("#user_email_id").val('');
		$("div[emaildiv=uppaidOfflineDiv]").show();
	}
	else if (JSONObject.STATUS == 'PAY_NOT_SET_OFFLINE')
	{
		$("div[emaildiv=email_div]").hide();
		$("div[emaildiv=uppaidOfflinePayNotSetDiv]").find("#user_details").val($("div[emaildiv=email_div]").find("#user_email_id").val());	
		$("div[emaildiv=email_div]").find("#user_email_id").val('');
		$("div[emaildiv=uppaidOfflinePayNotSetDiv]").show();
	}
	else if (JSONObject.STATUS == 'AVAILABLE')
	{
		
		$($("div[emaildiv=email_div]")).hide();	
		var registerDiv = $("div[emaildiv=userregform]");
		try
		{
			var JSONObjectData = JSONObject.DATA;
			
			console.log("BHASKAR");
			$(registerDiv).find('#email_div').html('');
			$(registerDiv).find('#user_first_name').val(JSONObjectData.FIRST_NAME);
			$(registerDiv).find('#user_middle_name').val(JSONObjectData.MIDDLE_NAME);
			$(registerDiv).find('#user_last_name').val(JSONObjectData.LAST_NAME);
			$(registerDiv).find('#user_mobile').val(JSONObjectData.MOBILE_NO);
			$(registerDiv).find('#user_usd_code').val(JSONObjectData.MOBILE_ISD_CODE);
			checkMobileValidation(JSONObjectData.MOBILE_NO,jBaseUrl)
			$(registerDiv).find('#user_phone_no').val(JSONObjectData.PHONE_NO);
			$(registerDiv).find('#user_address').val(JSONObjectData.ADDRESS);
			$(registerDiv).find('#user_city').val(JSONObjectData.CITY);
			$(registerDiv).find('#user_postal_code').val(JSONObjectData.PIN_CODE);
			
			$(registerDiv).find('#user_country').val(JSONObjectData.COUNTRY_ID);											
			$(registerDiv).find('#user_country').trigger("change");
			$(registerDiv).find('#user_state').val(JSONObjectData.STATE_ID);
		} catch (e){
			$(registerDiv).find('#email_div').html('');
			$(registerDiv).find('input[type=text]').val('');
			$(registerDiv).find("input[type=checkbox]").prop("checked",false);
			$(registerDiv).find("input[type=checkbox]").prop("checked",false);
			$(registerDiv).find('#user_country').val('');											
			$(registerDiv).find('#user_country').trigger("change");
		}	
			
		$(registerDiv).find("#user_email_id").val($("div[emaildiv=email_div]").find("#user_email_id").val());	
		$(registerDiv).show();	
		$($("div[emaildiv=email_div]")).find("#user_email_id").val('');	
		$($("div[emaildiv=email_div]")).find('#email_div').html('');
		$($("div[emaildiv=email_div]")).find('#mobile_div').html('');
		
		var regClassIdVal = $.trim($(registerDiv).find("#regClassId").val());
		if(regClassIdVal == "15")
		{
			$(registerDiv).find("#user_usd_code").val('');
		}
		else
		{
			$(registerDiv).find("#user_usd_code").val('+91');
		}
		$(registerDiv).show();
	}
}

function revert()
{
	$("div[div=all]").hide();
	$("div[emaildiv=email_div]").show();
	$($("div[emaildiv=email_div]")).find('#email_div').html('');
	$($("div[emaildiv=userregform]")).find('#email_div').html('');
	$($("div[emaildiv=userregform]")).find('#mobile_div').html('');
}
