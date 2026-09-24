/*******************************************************/
/*                     GET STATE LIST                  */
/*******************************************************/
$(document).ready(function(){
	jBaseUrl = jsBASE_URL;
	$("select[forType=hotel]").change(function(){
		hotelId  = $(this).val();
		generateCheckInDates(hotelId,jBaseUrl);
	});
	 
	$("select[forType=checkInDate]").change(function(){
										
		hotelId  = $("select[forType=hotel]").val();
		dateId  = $(this).val();
		generateCheckOutDates(dateId,hotelId,jBaseUrl);
	});
	
	$("select[forType=checkOutDate]").change(function(){
										
		hotelId    = $("select[forType=hotel]").val();
		inDateId   = $("select[forType=checkInDate]").val();
		outDateId  = $(this).val();
		generateRoomType(hotelId,inDateId,outDateId,jBaseUrl);
	});
	
	$("select[forType=roomType]").change(function(){
		packageId  = $(this).val();
		generateNumberRoom(packageId,jBaseUrl);
	});
	
	$("select[forType=country]").change(function(){
		countryId  = $(this).val();
		generateSateList(countryId,jBaseUrl);
	});
	
	$("input[type=text][forType=emailValidate]").keypress(function(){
		$(this).attr('VALDATD','N');
	});
	
	$("input[type=text][forType=emailValidate]").blur(function(){
		var validteUserDetails = true;
		try{
			if(UPDATING_ABSTRACT_USER) validteUserDetails = false;
		}catch(e){}
		
		if(validteUserDetails || $(this).attr('VALDATD') != 'Y')
		{
			initiateEmailValidation(this);
		}
		
	});
	$("input[type=text][spot=email]").blur(function(){
		spotInitiateEmailValidation(this);
	});
	
	$("input[type=text][forType=mobileValidate]").blur(function(){
		var validteUserDetails = true;
		try{
			if(UPDATING_ABSTRACT_USER) validteUserDetails = false;
		}catch(e){}
		
		if(validteUserDetails)
		{
			mobile  = $(this).val();
			checkMobileValidation(mobile,jBaseUrl);
		}
	});
	
});

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

function generateCheckInDates(hotelId,jBaseUrl)
{
	if(hotelId!=''){
		
		$.ajax({
					type: "POST",
					url: jBaseUrl+"returnData.process.php",
					data: 'act=generateCheckInDate&hotelId='+hotelId,
					dataType: 'html',
					async: false,
					success: function(returnMessage){
						
						if(returnMessage!=''){
							
							$("select[forType=checkInDate]").html("");
							$("select[forType=checkInDate]").html(returnMessage);
							$("select[forType=checkInDate]").prop("disabled", false);
							
							$("select[forType=checkOutDate]").html("");
							$("select[forType=checkOutDate]").html("<option value=''>-- Select Check In Date First --</option>");
							$("select[forType=checkOutDate]").prop("disabled", true);
						}
					}
			  });
	}
	else{
		
		$("select[forType=checkInDate]").html("");
		$("select[forType=checkInDate]").html("<option value=''>-- Select Hotel First --</option>");
		
		$("select[forType=checkInDate]").prop("disabled", true);
		
		return false;
	}
}

function generateCheckOutDates(dateId,hotelId,jBaseUrl)
{
	console.log(jBaseUrl+'returnData.process.php?act=generateCheckOutDate&dateId='+dateId+'&hotelId='+hotelId);
	if(hotelId!=''){
		
		$.ajax({
					type: "POST",
					url: jBaseUrl+"returnData.process.php",
					data: 'act=generateCheckOutDate&dateId='+dateId+'&hotelId='+hotelId,
					dataType: 'html',
					async: false,
					success: function(returnMessage){
						
						if(returnMessage!=''){
							
							$("select[forType=checkOutDate]").html("");
							$("select[forType=checkOutDate]").html(returnMessage);
							$("select[forType=checkOutDate]").prop("disabled", false);
							
							$("select[forType=roomType]").html("");
							$("select[forType=roomType]").html("<option value=''>-- Select Check Out Date First --</option>");
							$("select[forType=roomType]").prop("disabled", true);
						}
					}
			  });
	}
	else{
		
		$("select[forType=checkOutDate]").html("");
		$("select[forType=checkOutDate]").html("<option value=''>-- Select Hotel First --</option>");
		
		$("select[forType=checkOutDate]").prop("disabled", true);
		
		return false;
	}
}

function generateRoomType(hotelId,inDateId,outDateId,jBaseUrl)
{
	console.log(jBaseUrl+'returnData.process.php?act=generateCheckOutDate&dateId='+dateId+'&hotelId='+hotelId);
	if(hotelId!=''){
		
		$.ajax({
					type: "POST",
					url: jBaseUrl+"returnData.process.php",
					data: 'act=generateRoomType&hotelId='+hotelId+'&inDateId='+inDateId+'&outDateId='+outDateId,
					dataType: 'html',
					async: false,
					success: function(returnMessage){
						
						if(returnMessage!=''){
							
							$("select[forType=roomType]").html("");
							$("select[forType=roomType]").html(returnMessage);
							$("select[forType=roomType]").prop("disabled", false);							
						}
					}
			  });
	}
	else{
		
		$("select[forType=roomType]").html("");
		$("select[forType=roomType]").html("<option value=''>-- Select Check Out Date First --</option>");
		
		$("select[forType=roomType]").prop("disabled", true);
		
		return false;
	}
}

function generateNumberRoom(packageId,jBaseUrl)
{
	console.log(jBaseUrl+'returnData.process.php?act=generateCheckOutDate&dateId='+dateId+'&hotelId='+hotelId);
	if(hotelId!=''){
		
		$.ajax({
					type: "POST",
					url: jBaseUrl+"returnData.process.php",
					data: 'act=generateRoomNumber&packageId='+packageId,
					dataType: 'html',
					async: false,
					success: function(returnMessage){
						
						if(returnMessage!=''){
							
							$("select[forType=bookingQuantity]").html("");
							$("select[forType=bookingQuantity]").html(returnMessage);
							$("select[forType=bookingQuantity]").prop("disabled", false);							
						}
					}
			  });
	}
	else{
		
		$("select[forType=roomType]").html("");
		$("select[forType=roomType]").html("<option value=''>-- Select Check Out Date First --</option>");
		
		$("select[forType=roomType]").prop("disabled", true);
		
		return false;
	}
}

function initiateEmailValidation(emailObj)
{
	var emailid =  $.trim($(emailObj).val());
	if(emailid!='')
	{
		$(emailObj).attr('VALDATD','N');
		var div='<div style="float: left;" use="loader"><img src="images/loadinfo.net.gif" /></div>';		
		$("div[use=loader]").remove();
		$('#email_div').html('');
		$("#email_id_validation").after(div);
		setTimeout(function(){
			oldEmail  = $('#oldEmail').val();
			if(emailid != oldEmail)
			{
				checkEmailValidation(emailid,jBaseUrl,function(status){ 
															try{ 
																if(status=='AVAILABLE' || status=='SC_FACULTY')
																{
																	$(emailObj).attr('VALDATD','Y');
																}
																postValidationActivity(status); 
															}catch(e){
																console.log("postValidationActivity not found");
															}
														});
			}
		},500);
	}
}



function checkEmailValidation(emailId,jBaseUrl,callback)
{
	$('#email_id_validation').val("");
	$('#email_div').html("");
	var conf_name = $('#conf_name').val();
	if(emailId!="")
	{
		if(regularExpressionEmail.test(emailId)==false)
		{
			//$('#email_div').html('<span style="color:#D41000">Invalid Email Id '+emailId+'</span>');
			$('#email_div').html('<span style="color:#D41000">Something went wrong. Please check the e-mail id.</span>');
			$('#email_id_validation').val('INVALID');
			$("div[use=loader]").remove();
			return false;
		}
		else
		{
			console.log(jBaseUrl+'returnData.process.php?act=getEmailValidation&email='+emailId);
			$.ajax({
						type: "POST",
						url: jBaseUrl+'returnData.process.php?checkFaculty=YES',
						data: 'act=getEmailValidation&email='+emailId,
						dataType: 'text',
						async: false,
						success:function(returnMessage)
						{
							console.log(returnMessage);
							returnMessage = returnMessage.trim();
							var stat = 'IN_USE';
							var use = $("input[type=text][forType=emailValidate]").attr("use");	
							var msg = {};
							if(use=='add')
							{
								msg.IN_USE 		= '<span style="color:#D41000">Email already registered with us for '+conf_name+'</span>';
								msg.NOT_PAID 	= '<span style="color:#D41000">Email already registered with us but the registration procedure remained incomplete.<br>To complete the same please contact with IMSCON Secretariat Ph no.- 03340015677, 8697064019 or 8335897369.<br>Time: 11:00 - 18:00.</span>';
								msg.AVAILABLE 	= '<span style="color:green">Available</span>';
								msg.SC_FACULTY 	= '<span style="color:green">Available, Is a Faculty </span>';
							}
							else
							{
								msg.IN_USE 		= '<span style="color:#D41000">Email already registered with us for '+conf_name+'</span>';
								msg.NOT_PAID 	= '<span style="color:#D41000">Email already registered with us but the registration procedure remained incomplete.<br>To complete the same please contact with IMSCON Secretariat Ph no.- 03340015677, 8697064019 or 8335897369.<br>Time: 11:00 - 18:00.</span>';
								msg.AVAILABLE 	= '<span style="color:green">Available</span>';
								msg.SC_FACULTY 	= '<span style="color:green">Available, Is a Faculty </span>';
								
							}
							
							if(returnMessage == 'IN_USE')
							{
								$('#email_div').html(msg.IN_USE);
								$("#myemail").val(emailId);
								$("div[use=submit_block]").css('display', 'none');
								$("#logindiv").css('display', 'block');
								
							}
							else if(returnMessage == 'NOT_PAID')
							{
								stat = 'NOT_PAID';
								$('#email_div').html(msg.NOT_PAID);
								$("#unpaidlogin").css('display', 'block');
								
							}
							else
							{
								if(returnMessage == 'SC_FACULTY')
								{
									$('#email_div').html(msg.SC_FACULTY);
								}
								else
								{
								  $('#email_div').html(msg.AVAILABLE);
								}
								
								$("#regdiv").css('display', 'block');
								$("div[userform=userregform]").css('display', 'block');
								$("input[type=text][regmail=mailId]").val(emailId);
								$("div[emaildiv=email_div]").css('display', 'none');
								$("#logindiv").css('display', 'none');
								$("#note").css('display', 'none');
								$("div[use=submit_block]").css('display', 'none');
								stat = 'AVAILABLE';
								var regClassIdVal = $.trim($("input[type=checkbox][operationmode=registration_tariff]:checked").val());
								
								if(regClassIdVal == "15")
								{
									$("#user_usd_code").val('');
								}
								else
								{
									$("#user_usd_code").val('+91');
								}
								if(use=='add')
								{
									returnDelegateDetails(emailId,jBaseUrl);
								}
								//  var successContent   = '<div style="color:#FF0000; font-size:15px; text-align:center;">You are already registered.</div>';
								//	successContent  += '<div style="color:#FF0000; font-size:15px; text-align:center;">For any addition regarding registration, no need to create another account.</div>';
								//	successContent  += '<div style="text-align:center;">Please <a href="login.php" style="padding:1px 5px 1px 5px; color:#FFFFFF; background-color:#FF0000; border:1px solid #660000; cursor:pointer;">Login</a> to you account.</div>';
								//  $('#email_div').html(successContent);
							}
							
							$('#email_id_validation').val(returnMessage); 
							
							try{callback(stat);}catch(e){}
							
							
						}
					});
			$("div[use=loader]").remove();
		}
		
	}
			
}

function checkMobileValidation(mobile,jBaseUrl)
{
	if(mobile!="")
	{
		if(isNaN(mobile) || mobile.toString().length !=10)
		{
			$('#mobile_div').html('');
			$('#mobile_div').html('<span style="color:#D41000">Invalid Mobile No '+mobile+'</span>');
			$('#mobile_validation').val('INVALID');
			$("#user_mobile").focus();
			$("div[use=loader]").remove();
			return false;
		}
		else
		{
			console.log(jBaseUrl+'returnData.process.php?act=getMobileValidation&mobile='+mobile);
			$.ajax({
						type: "POST",
						url: jBaseUrl+'returnData.process.php',
						data: 'act=getMobileValidation&mobile='+mobile,
						dataType: 'text',
						async: false,
						success:function(returnMessage)
						{
							returnMessage = returnMessage.trim();
							if(returnMessage == 'IN_USE')
							{
								$('#mobile_div').html('');
								$('#mobile_div').html('<span style="color:#FF0000">Mobile Number Already In Use</span>');
								$("#user_mobile").val("");
								$("#user_mobile").focus();
								
							}
							else
							{
								$('#mobile_div').html('');			
								$('#mobile_div').html('<span style="color:#009933">Available</span>');
							}
							
							$('#mobile_validation').val(returnMessage);
							
							
						}
					});
			$("div[use=loader]").remove();
		}
		
	}
			
}

function returnDelegateDetails(emailId,jBaseUrl)
{
	
	console.log(jBaseUrl+'returnData.process.php?act=getDelegateDetails&email='+emailId);
	
	$.ajax({
			type: "POST",
			url: jBaseUrl+'returnData.process.php',
			data: 'act=getDelegateDetails&email='+emailId,
			dataType: 'json',
			async: false,
			success:function(JSONObject)
			{
				//$('#user_initial_title_'+JSONObject.TITLE.toLowerCase()).prop("checked",true);
				$('#user_initial_title').val(JSONObject.TITLE);
				$('#user_first_name').val(JSONObject.FIRST_NAME);
				$('#user_middle_name').val(JSONObject.MIDDLE_NAME);
				$('#user_last_name').val(JSONObject.LAST_NAME);
				$('#user_usd_code').val(JSONObject.MOBILE_ISD_CODE);
				$('#user_mobile').val(JSONObject.MOBILE_NO);
				$('#user_address').val(JSONObject.ADDRESS);
				$('#user_city').val(JSONObject.CITY);
				$('#user_postal_code').val(JSONObject.PIN_CODE);
				$('#user_gender_'+JSONObject.GENDER.toLowerCase()).prop("checked",true);
				$('#user_country').val(JSONObject.COUNTRY_ID);
				generateSateList(JSONObject.COUNTRY_ID,jBaseUrl);
				$('#user_state').val(JSONObject.STATE_ID);
				checkMobileValidation(JSONObject.MOBILE_NO,jBaseUrl);
			}
		});	
}

function changeemaildiv()
{
	$("div[userform=userregform]").css('display', 'none');
	$("div[emaildiv=email_div]").css('display', 'block');
	$("div[use=submit_block]").css('display', 'block');
	$("#note").css('display', 'block');
}

function spotInitiateEmailValidation(emailObj)
{
	var emailid =  $.trim($(emailObj).val());
	if(emailid!='')
	{
		$(emailObj).attr('VALDATD','N');
		var div='<div style="float: left;" use="loader"><img style="width:22px;" src="images/ellipsis.gif" /></div>';		
		$("div[use=loader]").remove();
		$('#email_div').html('');
		$("#email_id_validation").after(div);
		setTimeout(function(){
			oldEmail  = $('#oldEmail').val();
			if(emailid != oldEmail)
			{
				checkEmailValidation(emailid,jBaseUrl,function(status){ 
															try{ 
																if(status=='AVAILABLE')
																{
																	$("#userTable").show();
																	$("#userTableRow").show();		
																	$("#accompanyMessage").show();
																	$(emailObj).attr('VALDATD','Y');
																}
																
																postValidationActivity(status); 
															}catch(e){
																console.log("postValidationActivity not found");
															}
														});
			}
		},500);
	}
}
$(document).ready(function(){

		$('.institute').on('keyup', function() {
			
		    // Get textbox value and remove non-alphabet characters
		    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
		    // Set textbox value to cleaned-up value
		    $(this).val(val);
		  });

	});