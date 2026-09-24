<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.exhibitor.php");

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: BULK ENTRY | AICC RCOG 2019 ::</title>
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>abstract.user.entrypoint.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<script src="<?=_BASE_URL_?>js/website/returnData.process.js"></script>
	</head>
<?php
		if(isset($_REQUEST['TOKEN']) && trim($_REQUEST['TOKEN']) != '')
		{
			$token = unserialize(base64_decode($_REQUEST['TOKEN']));
			if(is_array($token) && sizeof($token) > 0)
			{
				foreach($token as $key=>$val)
				{
					$_REQUEST[$key] = $val;
				}
			}
		}
		
		$uploadExhibitorId = $_REQUEST['exhibitorId'];
		$uploadCutoff 	   = $_REQUEST['cutoffid'];
		
		$sqlExhibitorCompany      = exhibitorCompanyQuerySet($uploadExhibitorId, "");	
		$resultExhibitorCompany   = $mycms->sql_select($sqlExhibitorCompany);
		$rowExhibitorCompany      = $resultExhibitorCompany[0];
		
?>
	<body>
<?
		if($cfg['EXHIBITOR.BULK.SUBMIT.LASTDATE'] >= date('Y-m-d') && $_REQUEST['exhibitorId'] != '' && $_REQUEST['cutoffid'] != '')
		{
			$sqlInsertIntoSessionDetails['QUERY']	= "INSERT INTO "._DB_BLUK_REGISTRATION_SESSION_." 
															   SET `entry_type`						= 'ENTRY',
																   `registration_tariff_cutoff_id`	= '".$uploadCutoff."',
																   `exhibitor_company_id` 			= '".$uploadExhibitorId."',
																   `created_by` 					= '-1',
																   `created_ip` 					= '".$_SERVER['REMOTE_ADDR']."',
																   `created_sessionId` 				= '".session_id()."',
																   `created_dateTime` 				= '".date('Y-m-d H:i:s')."'";
			$lastInsertedSessionDetails	= $mycms->sql_insert($sqlInsertIntoSessionDetails);
		}
?>       
		<form name="registrationForm" id="registrationForm" 
			  action="<?=_BASE_URL_?>exhibitor.bulkentry.screen.process.php" method="post" enctypeX="multipart/form-data" 
			  onSubmit="return bulkInsertFormValidation(this);">
		<input type="hidden" name="act" value="bulkInsert" />
		<input type="hidden" name="exhibitorId" id="exhibitorId" value="<?=trim($_REQUEST['exhibitorId'])?>" />
		<input type="hidden" name="entry_type" id="entry_type" value="ENTRY" />		
		<input type="hidden" name="cutoffId" id="cutoffId" value="<?=trim($_REQUEST['cutoffid'])?>" />		
		<input type="hidden" name="bulkUploadSessionId" id="bulkUploadSessionId" value="<?=$lastInsertedSessionDetails?>" />		
		<input type="hidden" name="operationIsFinalized" id="operationIsFinalized" value="N" />		
        <div class="col-xs-2 left-container-box regTarrif_leftPanel" style="position:relative;">
            <div class="col-xs-4 logo">
                <img src="<?=_BASE_URL_?>images/logo_white.png" alt="logo" style="width: 100%;">              
            </div>
            <div class="col-xs-7 col-xs-offset-1 timer" style="padding: 0">
				<div>
                    <h5 class="cutoffNameTop">BULK</h5>
                    <h4 style="color: white" class="cutoffName">USER ENTRY</h4>
                </div>
            </div>
			<div class="timeLeftWrapper">
				<div style="font-size: 12px; color:#FFFFFF;" class="timeLeft">
					Session Expires In
				</div>
			</div>			
			<div class="timeLeftWrapper">
				<div style="font-size: 12px;" class="timeLeft">
					<div class="col-xs-3 timeLeftDays">
						<p style="margin-bottom: 0; color: white; font-style:italic;"><span id="dday">000</span> <sub style="bottom: 0; font-size: 12px;">DAYS</sub></p>
					</div>
					<div class="col-xs-3 timeLeftHours">
						<p style="margin-bottom: 0;color: white; font-style:italic;"><span id="dhour">00</span> <sub style="bottom: 0; font-size: 12px;">HRS.</sub></p>
					</div>
					<div class="col-xs-3 timeLeftMinutes">
						<p style="margin-bottom: 0;color: white; font-style:italic;"><span id="dmin">00</span> <sub style="bottom: 0; font-size: 12px;">MIN.</sub></p>
					</div>
					<div class="col-xs-3 timeLeftSeconds">
						<p style="margin-bottom: 0;color: white; font-style:italic;"><span id="dsec">00</span> <sub style="bottom: 0; font-size: 12px;">SEC.</sub></p>
					</div>
				</div>
			</div>
            <?		
			
			$ExpPeriod 		= 121;
						
			$endDate		= $mycms->cDate('Y-m-d-H-i-s', "+".$ExpPeriod." MINS");
			$dateArr		= explode("-",$endDate);
			?>
			<script>
			//  Change the items below to create your countdown target date and announcement once the target date and time are reached.  
			var current="";     			 //enter what you want the script to display when the target date and time are reached, limit to 20 characters
			var year= <?=$dateArr[0]?>;      //Enter the count down target date YEAR
			var month= <?=$dateArr[1]?>;     //Enter the count down target date MONTH
			var day= <?=intval($dateArr[2])?>;       //>Enter the count down target date DAY
			var hour=<?=intval($dateArr[3])?>;          			 //Enter the count down target date HOUR (24 hour clock)
			var minute=<?=intval($dateArr[4])?>;        			 //Enter the count down target date MINUTE
			var tz=5.5;            			 //Offset for your timezone in hours from UTC (see http://wwp.greenwichmeantime.com/index.htm to find the timezone offset for your location)
			
			// DO NOT CHANGE THE CODE BELOW! 
			var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
			
			function countdown(yr,m,d,hr,min){
				theyear=yr;themonth=m;theday=d;thehour=hr;theminute=min;
				var today=new Date();
				var todayy=today.getYear();
				if (todayy < 1000) { 
					todayy+=1900; 
				}
				var todaym=today.getMonth();
				var todayd=today.getDate();
				var todayh=today.getHours();
				var todaymin=today.getMinutes();
				var todaysec=today.getSeconds();
				var todaystring1=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
				var todaystring=Date.parse(todaystring1)+(tz*1000*60*60);
				var futurestring1=(montharray[m-1]+" "+d+", "+yr+" "+hr+":"+min);
				var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
				var dd=futurestring-todaystring;
				var dday=Math.floor(dd/(60*60*1000*24)*1);
				var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
				var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
				var dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);
				if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=0){
					document.getElementById('dday').style.display="none";
					document.getElementById('dhour').style.display="none";
					document.getElementById('dmin').style.display="none";
					document.getElementById('dsec').style.display="none";
					return;
				}
				else 
				{
					document.getElementById('dday').innerHTML=(dday<10)?('0'+dday):dday;
					document.getElementById('dhour').innerHTML=(dhour<10)?('0'+dhour):dhour;
					document.getElementById('dmin').innerHTML=(dmin<10)?('0'+dmin):dmin;
					document.getElementById('dsec').innerHTML=(dsec<10)?('0'+dsec):dsec;
					setTimeout("countdown(theyear,themonth,theday,thehour,theminute)",1000);
				}
			}
			countdown(year,month,day,hour,minute);
			</script>
			
            <div class="col-xs-12 menu" style="padding: 0">
<?
		if($cfg['EXHIBITOR.BULK.SUBMIT.LASTDATE'] >= date('Y-m-d'))
		{
?>
                <ul use="leftAccordion" class="accordion" style="border-left: 5px solid #0078b3">
				  <li use='leftAccordionAbstractCategory'>
				 	<div class="link masterClass" use="accordianL1TriggerDiv">DIRECTIVES</div>
					<p style="color:#FFFFFF; padding-left:20px; padding-right:10px; text-align:left; font-size:smaller;">
					Dear Executive from <?=$rowExhibitorCompany['exhibitor_company_name']?>, <br/>
					Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
					</p>
				  </li>
                </ul>
				
				<script>
					$(document).ready(function(){	
						appendNewForm();												
						$("form[name=registrationForm]").submit(function(evnt){							
							console.log('triggered submit @ ');
							if($("#operationIsFinalized").val()=='Y')
							{
								$("li[use=registrationProcess]").show();
								$.each($("li[use=regFormStructure]").find("div[use=registrationFormContainer]"), function(counter,databody){
									try{
										var seq = $(this).attr("sequence");
										if(!validateOnNextButton(databody))
										{
											console.log('ERROR @ '+seq);
											$("li[use=registrationProcess]").hide();
											evnt.preventDefault();
										}
									}
									catch(e)
									{
										console.log('ERROR : '+e.message);
										$("li[use=registrationProcess]").hide();
										evnt.preventDefault();
									}
								});
							}
							else
							{
								console.log('unauthorised submit : ');
								$("li[use=registrationProcess]").hide();
								evnt.preventDefault();
							}
						});
						
					});
					
					function appendNewForm()
					{
						var template = $("div[use=registrationFormTemplate]").find("div[use=registrationFormContainer]").clone();						
						var destination = $("li[use=regFormStructure]");
						
						$(template).find("input[type=text][defaultname=user_first_name],input[type=text][defaultname=user_last_name]").keyup(function(){
							var parent 	  = $(this).parent().closest("div[use=registrationFormContainer]");	
							
							var firstName =	$(parent).find("input[type=text][defaultname=user_first_name]").val();
							var lastName =	$(parent).find("input[type=text][defaultname=user_last_name]").val();
							
							$(parent).find("span[use=delegateName]").text(firstName+' '+lastName)
						});
						
						$(template).find("select[forType=country]").change(function(){
							countryId  = $(this).val();
							generateBulkSateList(this, countryId, jBaseUrl);
						});
						
						$(template).find("select[forType=country]").find('option[value="1"]').prop('selected', true);
						$(template).find("select[forType=country]").trigger("change");
																
						$(template).find("input[type=radio][operationMode=registration_tariff]").each(function(){
							$(this).click(function(){									
								var parent 			= $(this).parent().closest("div[use=registrationFormContainer]");								
								var defaultname 	= $(this).attr("defaultname");									
								var currChkbxStatus = $(this).attr("chkStatus");
																
								$(parent).find("label[operetionMode=workshopTariffTr]").hide();
								
								$(parent).find("input[type=checkbox][operationMode=workshopId]").prop("checked",false);	
								$(parent).find("input[type=checkbox][operationMode=workshopId_postconference]").prop("checked",false);	
								
								$(parent).find("label[operetionMode=checkInCheckOutTr]").hide();
								$(parent).find("div[use=workshopContainers]").hide();
								
								$(parent).find("div[use=dinnerContainers]").hide();								
								
								if(currChkbxStatus=="true")
								{
									$(this).prop("checked",false);	
									$(this).attr("chkStatus","false");	
								}
								else
								{	
									$(this).prop("checked",true);	
									$(this).attr("chkStatus","true");	
									
									var regType 	= $(this).attr('operationModeType');
									var regClsfId 	= $(this).val();
									var currency 	= $(this).attr('currency');
									var offer 		= $(this).attr('offer');
									
									if(regType=='residential')
									{
										var accommodationType 	= $(this).attr("accommodationType");
										var packageId 			= $(this).attr("accommodationPackageId");
										var hotel_id 			= $(this).attr("hotel_id");
										
										/*$(parent).find("div[operationMode=chhoseServiceOptions][use=defaultChoices]").hide();
										$((parent).find"div[operationMode=chhoseServiceOptions][use=residentialOperations]").slideDown();
										
										$(parent).find("input[type=hidden][name=accomPackId]").attr("value",packageId);
										$(parent).find("input[type=hidden][name=hotel_id]").attr("value",hotel_id);
										
										$(parent).find("div[operetionMode=checkInCheckOutTr][use='"+packageId+"']").slideDown();
										
										if(accommodationType=='SHARED')
										{
											$(parent).find("div[use=ResidentialAccommodationAccompanyOption]").slideDown();
										}
										
										$(parent).find("li[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();*/
									}
									else if(regType=='conference')
									{											
										$(parent).find("div[use=workshopContainers]").slideDown();
										$(parent).find("label[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
																				
										$(parent).find("div[use=dinnerContainers]").slideDown();
									}
									else
									{
										$(parent).find("label[operetionMode=checkInCheckOutTr]").hide();
										$(parent).find("div[use=workshopContainers]").hide();
										
										$(parent).find("div[use=dinnerContainers]").hide();	
									}
								}
								
								calculateTotalAmount();
							});
						});		
						
						$(template).find("input[type=radio][operationMode=workshopId]").each(function(){
							$(this).click(function(){	
								var parent 			= $(this).parent().closest("div[use=registrationFormContainer]");
								var currChkbxStatus = $(this).attr("chkStatus");
								
								$(parent).find("input[type=checkbox][operationMode=workshopId]").prop("checked",false);	
								$(parent).find("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");
								
								if(currChkbxStatus=="true")
								{
									$(this).prop("checked",false);	
									$(this).attr("chkStatus","false");
								}
								else
								{	
									$(this).prop("checked",true);	
									$(this).attr("chkStatus","true");	
								}
								calculateTotalAmount();
							});
						});
							
						$(template).find("input[type=radio][operationMode=dinner]").each(function(){
							$(this).click(function(){	
								var parent 			= $(this).parent().closest("div[use=registrationFormContainer]");
								var currChkbxStatus = $(this).attr("chkStatus");
								
								$(parent).find("input[type=checkbox][operationMode=dinner]").prop("checked",false);	
								$(parent).find("input[type=checkbox][operationMode=dinner]").attr("chkStatus","false");
								
								if(currChkbxStatus=="true")
								{
									$(this).prop("checked",false);	
									$(this).attr("chkStatus","false");
								}
								else
								{	
									$(this).prop("checked",true);	
									$(this).attr("chkStatus","true");	
								}
								calculateTotalAmount();
							});
						});
						
						$(template).find("input[type=radio][use=accompanyCountSelect]").click(function(){
							var parent 			= $(this).parent().closest("div[use=registrationFormContainer]");
							var count 			= parseInt($(this).val());
							var haveCount 		= $("div[use=accompanyDetails]").length;							
							for(var i = 1; i<= count; i++)
							{
								$(parent).find("div[use=accompanyDetails][index='"+i+"']").slideDown();
							}
							for(var j = (count+1); j <= haveCount; j++)
							{
								var accomDiv = $(parent).find("div[use=accompanyDetails][index='"+j+"']");
								$(accomDiv).slideUp();
								$(accomDiv).find("input[type=text]").val('');
								$(accomDiv).find("input[type=radio]").prop('checked',false);
								$(accomDiv).find("input[type=checkbox]").prop('checked',false);
							}
							calculateTotalAmount();
						});
						
						$(template).find("div[use=accompanyDetails]").find("input[type=checkbox][operationMode=dinner]").click(function(){
							calculateTotalAmount();
						});
						
						$(destination).append(template);
						
						organizeFileds(destination);
								
					}
					
					function organizeFileds(destination)
					{
						var tabCounter	=	1;
						
						$.each($(destination).find("div[use=registrationFormContainer]"), function(counter,template){
							
							
							$(template).attr("sequence",counter);
							
							if(counter==0)
							{
								$(template).find("i[use=removeDelegate]").hide();
							}
							else
							{
								$(template).find("i[use=removeDelegate]").show();
							}
							
							$.each($(template).find("input[type=text],input[type=hidden],input[type=radio],input[type=checkbox],textarea,select"),function(){
								var obj 		= $(this);
								var defaultName = $(obj).attr('defaultname');
								
								var parts = new Array();
								parts[0]  = defaultName;
								parts[1]  = '';
								
								try
								{
									parts = defaultName.split('-');
									if(parts.length<2)
									{
										parts[1]  = '';
									}
								}catch(e){}
																
								var newName = parts[0]+'['+counter+']'+parts[1];																
								$(obj).attr('name',newName);
								
								if($(obj).attr('type')!='hidden')
								{
									$(obj).attr('tabindex',tabCounter);
									tabCounter++;
								}
							});
						});				
					}					
										
					function checkUserEmail(obj)
					{
						popDownAlert();
						
						var liParent 			= $(obj).parent().closest("ul.submenu");
						var emailIdObj 			= $(obj);
						var emailId 			= $.trim($(emailIdObj).val());
						
						$(liParent).find("input[use=email_validated]").val("N");
						
						if(emailId != '')
						{
							var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
							if (filter.test(emailId)) 
							{
								$(obj).hide();	
								$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").show();									
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
											console.log(JSONObject);
											if (JSONObject.STATUS == 'IN_USE' 
												|| JSONObject.STATUS == 'NOT_PAID' 
												|| JSONObject.STATUS == 'NOT_PAID_OFFLINE'
												|| JSONObject.STATUS == 'PAY_NOT_SET_OFFLINE')
											{
												console.log('returnData:'+JSONObject);			
												$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();
												popoverAlert(emailIdObj,"Email already in use");	
											}
											else if (JSONObject.STATUS == 'AVAILABLE')
											{
												$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();
												$(liParent).find("input[use=email_validated]").val("Y");
											}
											
										}
									}).always(function() {
										$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();
										$(obj).show();	
									});
								 },500);
							}
							else
							{
								popoverAlert(emailIdObj);					 	
							}
						}
						else
						{
						 	popoverAlert(emailIdObj);					 	
						}
					}
					
					function checkMobileNo(mobileObj)
					{
						popDownAlert();
						
						var mobile 		= $(mobileObj).val();
						var parent 		= $(mobileObj).parent().closest("ul.submenu");
						
						$(parent).find("input[use=mobile_validated]").val("N");					
						
						if(mobile!="")
						{
							if(isNaN(mobile) || mobile.toString().length < 10 || isNaN(mobile) || mobile.toString().length > 10)
							{
								popoverAlert(mobileObj);					 	
								$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();			
							}
							else
							{
								$(mobileObj).hide();	
								$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").show();			
								console.log(jsBASE_URL+'returnData.process.php?act=getMobileValidation&mobile='+mobile);
								setTimeout(function(){
									$.ajax({
										type: "POST",
										url: jsBASE_URL+'returnData.process.php',
										data: 'act=getMobileValidation&mobile='+mobile,
										dataType: 'text',
										async: false,
										success:function(returnMessage)
										{	
											console.log('mobile >>'+returnMessage);
											returnMessage = returnMessage.trim();
											if(returnMessage == 'IN_USE')
											{
												popoverAlert(mobileObj,"Mobile no. is already in use.");					 	
												$(parent).find("input[use=mobile_validated]").val("N");
											}
											else
											{	
												console.log('mobile has been validated>>');
												$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();
												$(parent).find("input[use=mobile_validated]").val("Y");
											}
										}
									}).always(function() {
										$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();
										$(mobileObj).show();	
									});
								},500);
							}
						}	
						else
						{
							popoverAlert(mobileObj);	
							$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();								 	
						}
					}
															
					function validateOnNextButton(parent)
					{
						popDownAlert();
						
						var returnVal 	= true;
						
						console.clear();						
						console.log('initial>>'+returnVal);
						
						$.each($(parent).find("input[type=text],input[type=email],input[type=tel],input[type=number],input[type=date],input[type=file],textarea"),function(){
							console.log('function validateOnNextButton>>'+$(this).attr("name"));
							var attr = $(this).attr('required');
							if (typeof attr !== typeof undefined && attr !== false) 
							{
								console.log('hasReq>>'+$(this).attr('name'));
								if($.trim($(this).val())=='')
								{
									$(this).focus();
									popoverAlert(this);
									returnVal = false;
									return false;
								}
							}
						});	
						console.log('text>>'+returnVal);
						if(!returnVal) return false;
						
						$.each($(parent).find("select"),function(){
							var attr = $(this).attr('required');
							if (typeof attr !== typeof undefined && attr !== false) 
							{
								console.log('hasReq>>'+$(this).attr('name'));																
								if($.trim($(this).val())=='')
								{
									$(this).focus();
									popoverAlert(this);
									returnVal = false;
									return false;
								}
							}
						});	
						console.log('select>>'+returnVal);	
						if(!returnVal) return false;		
							
						$.each($(parent).find("input[type=radio]"),function(){
							var hasRequired = false;
							var attr = $(this).attr('required');
							if (typeof attr !== typeof undefined && attr !== false) 
							{
								console.log('hasReq>>'+$(this).attr('name'));
								hasRequired = true;
							}
							if(hasRequired)
							{
								var name = $(this).attr("name");
								if($("input[type=radio][name='"+name+"']:checked").length == 0)
								{
									popoverAlert(this);
									returnVal = false;
									return false;
								}
							}
						});
						console.log('radio>>'+returnVal);	
						if(!returnVal) return false;	
						
						returnVal = validateRegistrationUserDetails(parent);
						console.log('validateRegistrationUserDetails>>'+returnVal);	
						if(!returnVal) return false;	
												
						/*returnVal = validateRegistrationOptions(parent);
						console.log('validateRegistrationOptions>>'+returnVal);	
						if(!returnVal) return false;*/	
						
						returnVal = validateRegistrationAccompanyDetails(parent);
						console.log('validateRegistrationAccompanyDetails>>'+returnVal);	
						if(!returnVal) return false;	
						
						console.log('final>>'+returnVal);						
						return returnVal;
					}
										
					function validateRegistrationUserDetails(obj)
					{
						if($(obj).find("input[use=email_validated]").val()!="Y")
						{
							
							popoverAlert($(obj).find("input[defaultname=user_email]"));
							console.log('function validateRegistrationUserDetails>>failed email_validated');
							return false;
						}
						
						if($(obj).find("input[use=mobile_validated]").val()!="Y")
						{
							popoverAlert($(obj).find("input[defaultname=user_mobile]"));
							console.log('function validateRegistrationUserDetails>>failed mobile_validated');
							return false;
						}
								
						return true;
					}
					
					function validateRegistrationOptions(obj)
					{
						var returnVal 		= true;
						var checkedTariffOb = $("input[type=checkbox][operationMode=registration_tariff]:checked");
						var checkedTariff	= $(checkedTariffOb).val();
						var regType 		= $(checkedTariffOb).attr('operationModeType');
						var regClsfId 		= $(checkedTariffOb).val();
						var currency 		= $(checkedTariffOb).attr('currency');
						var offer 			= $(checkedTariffOb).attr('offer');
						
						if(regType=='residential')
						{
							var accommodationType 	= $(checkedTariffOb).attr("accommodationType");
							var packageId 			= $(checkedTariffOb).attr("accommodationPackageId");
							var hotel_id 			= $(checkedTariffOb).attr("hotel_id");
							
							$.each($("input[type=radio][name='accDate["+packageId+"]']"),function(){																
								var name = $(this).attr("name");
								if($("input[type=radio][name='"+name+"']:checked").length == 0)
								{
									popoverAlert(this);
									returnVal = false;
									return false;
								}
								if(accommodationType=='SHARED')
								{
									var preferredUserBlk = $("div[use=ResidentialAccommodationAccompanyOption]");
																		
									$.each($(preferredUserBlk).find("input[type=text],input[type=email],input[type=tel],input[type=number]"),function(){
										return false; // blocked
										if($.trim($(this).val())=='')
										{
											$(this).focus();
											popoverAlert(this);
											returnVal = false;
											return false;
										}
									});
									
									$.each($(preferredUserBlk).find("input[type=email]"),function(){										
										var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
										var emailVal = $(this).val();
										if(emailVal!='')
										{
											if(!filter.test(emailVal))
											{
												$(this).focus();
												popoverAlert(this);
												returnVal = false;
												return false;
											}
										}
									});
									
									$.each($(preferredUserBlk).find("input[type=tel]"),function(){										
										var mobile = $(this).val();	
										if(mobile!='')
										{									
											if(isNaN(mobile) || mobile.toString().length != 10)
											{
												$(this).focus();
												popoverAlert(this);
												returnVal = false;
												return false;
											}
										}
									});
								}
							});
							
						}
						else if(regType=='conference')
						{
							// no mandatory
						}
						
						return returnVal;
					}
										
					function validateRegistrationAccompanyDetails(obj)
					{
						var countObj 	= $(obj).find("input[type=radio][name=accompanyCount]:checked");						
						var count 		= $(countObj).val();
						
						for(var i = 0; i < count; i++)
						{
							var accomDiv 			= $(obj).find("div[use=accompanyDetails][index='"+(i+1)+"']");
							
							var accomPanyNameObj 	= $(accomDiv).find("input[name='accompany_name_add["+i+"]']");
							var accomPanyName		= $(accomPanyNameObj).val();
							
							if($.trim(accomPanyName) == '')
							{
								console.log('accomPanyName>>BLANK');	
								$(accomPanyNameObj).focus();
								popoverAlert(accomPanyNameObj);
								return false;
							}
							
							var accomPanyDinnrObj 	= $(accomDiv).find("input[name='accompany_dinner_value["+i+"]']");
							
							if($(accomPanyDinnrObj).is(":checked"))
							{
								var accomPanyfoodChecked 	= $(accomDiv).find("input[name='accompany_food_choice["+i+"]']:checked");
								
								if($(accomPanyfoodChecked).length == 0)
								{
									var accomPanyfoodObj 	= $(accomDiv).find("input[name='accompany_food_choice["+i+"]']").first();
									
									console.log('accomPanyfoodChecked>>NOT CHEK');	
									$(accomPanyfoodObj).focus();
									popoverAlert(accomPanyfoodObj);
									return false;
								}
							}
						}
						return true;
					}						
					
					function triggerNextButton(obj)
					{
						var parent = $(obj).parent().closest("div[use=registrationFormContainer]");
												
						if(validateOnNextButton(parent))
						{
							$.each($("li[use=regFormStructure]").find("div[use=registrationFormContainer]"), function(counter,databody){
								$(databody)	.find("ul[use=indvDataContainer]").hide();						
							});
							
							console.log("submit draft >> start");		
							
							var formStr = $("form[name=registrationForm]");
							$.ajax({
								type : 'POST',
								url  : $(formStr).attr("action")+'?DRAFT=Y',
								data : $(formStr).serialize()
							})
							.done(function(data){
								console.log("submit draft >> done");				
								console.log(data);								
							})
							.fail(function(data){	
								console.log("submit address >> fail");
							});						
							appendNewForm();
						}
					}
					
					function triggerFinalizeButton(obj)
					{
						var parent = $(obj).parent().closest("div[use=registrationFormContainer]");
						
						if(confirm("Do you really want to FINALIZE?"))
						{
							if(validateOnNextButton(parent))
							{
								$.each($("li[use=regFormStructure]").find("div[use=registrationFormContainer]"), function(counter,databody){
									$(databody)	.find("ul[use=indvDataContainer]").hide();	
								});
								
								var formStr = $("form[name=registrationForm]");
								$.ajax({
									type : 'POST',
									url  : $(formStr).attr("action")+'?DRAFT=Y',
									data : $(formStr).serialize()
								})
								.done(function(data){
									console.log("submit draft >> done");				
									console.log(data);								
								})
								.fail(function(data){	
									console.log("submit address >> fail");
								});	
								
								$("li[use=registrationSubmit]").show();
								$("#operationIsFinalized").val('Y');
							}
						}
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
					
					function popDownAlert(obj)
					{
						if(typeof obj === typeof undefined)
						{
							$("div[callFor=alert]").hide();
						}
						else
						{
							$(obj).hide();
						}
					}
					
					function enableAllFileds(obj)
					{
						$.each($(obj).find("input,select,textarea"),function(){
							var attr = $(this).attr('disabled');
							if (typeof attr !== typeof undefined && attr !== false) {
								$(this).prop("disabled",false);
								$(this).attr("wasDisabled",'Y');
								$(obj).find(".dis_abled").attr("hasDisableCSS","Y");
								$(obj).find(".dis_abled").removeClass("dis_abled");
							}
						});
					}
					
					function disableAllFileds(obj)
					{
						$.each($(obj).find("input,select,textarea"),function(){
							if($(this).attr("wasDisabled")=='Y')
							{
								$(this).prop("disabled",true);
								$(obj).find("span[hasDisableCSS=Y],div[hasDisableCSS=Y]").addClass("dis_abled");
							}
						});
					}
					
					function calculateTotalAmount()
					{
						console.log("====calculateTotalAmount====");
						var grandtotalAmount = 0;
						$.each($("li[use=regFormStructure]").find("div[use=registrationFormContainer]"), function(counter,databody){
							var totalAmount = 0;
							$.each($(databody).find("input[type=checkbox]:checked,input[type=radio]:checked"),function(){
								var attr = $(this).attr('amount');
								if (typeof attr !== typeof undefined && attr !== false) 
								{
									var amt 	= parseFloat(attr);	
									totalAmount = totalAmount+amt;
									
									console.log(">>amt"+amt+' ==> '+totalAmount);
									
									var attrReg = $(this).attr('operationMode');
									var isConf  = false;
									if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'registration_tariff') 
									{
										isConf = true;
									}
									var isMastCls  = false;
									if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId') 
									{
										isMastCls = true;
									}
								}
							});
							totalAmount = Math.round(totalAmount,0);
							grandtotalAmount = grandtotalAmount+totalAmount;
							
							$(databody).find("span[use=subTotalAmount]").text('INR '+(totalAmount).toFixed(2));
							
						});
						
						grandtotalAmount = Math.round(grandtotalAmount,0);
						$("span[use=TOTTALamountDisp]").text((grandtotalAmount).toFixed(2));
					}
					
					function generateBulkSateList(obj, countryId,jBaseUrl)
					{
						var parent = $(obj).parent().closest("div[use=registrationFormContainer]");
						
						console.log(jBaseUrl+"returnData.process.php?act=generateStateList&countryId="+countryId);
						
						if(countryId!=""){
							$.ajax({
										type: "POST",
										url: jBaseUrl+"returnData.process.php",
										data: "act=generateStateList&countryId="+countryId,
										dataType: "html",
										async: false,
										success: function(JSONObject){
											$(parent).find("select[forType=state]").html(JSONObject);
											$(parent).find("select[forType=state]").removeAttr("disabled");
										}
							});
						}else{
							$(parent).find("select[forType=state]").html('<option value="">-- Select Country First --</option>');
							$(parent).find("select[forType=state]").attr("disabled","disabled");
						}
					}
									
				</script>
<?
		}
?>
            </div>
            
            <div class="col-xs-12 menu-links">  
				 <p class="menu-login" style="position:fixed; bottom: 20px;"><a>TOTAL : <span use="TOTTALamountDisp">0.00</span></a></p>
            </div> 
			
			<br/><br/>           
        </div>        
        
        <div class="col-xs-10 right-container regTarrif_rightPanel">
            <div class="col-xs-12 col-xs-offset-0" style="padding: 0; margin-top: 15px;">                
				<ul use="rightAccordion" class="accordion" style="padding:0 0 0  0px; margin: 0; "> 
                    <li use="regFormStructure" class="rightPanel_userDetails"></li>
					
					<li use="registrationSubmit" class="rightPanel_payment" style="text-align:center; display:none;" >
						<button type="submit" class="link" use='nextButton' style="border:none; width:100%;">SUBMIT</button>
						<button type="button" class="submit" style="border:none; width:100%;" onClick="appendNewForm()">Add Another Delegate</button>
					</li>
															
					<li use="registrationProcess" class="rightPanel_payment" style="display:none; text-align:center;" >
						<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
					</li>
                </ul>      
            </div>
       	</div>
	   	</form>
		
		<div use="registrationFormTemplate" style="display:none;">
			<div use="registrationFormContainer">
			<?
				$cutoffs 			= fullCutoffArray();	
				$currentCutoffId 	= trim($_REQUEST['cutoffid']);
				if($currentCutoffId > 0)
				{
					$conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
					$workshopDetailsArray 	 = getAllWorkshopTariffs($currentCutoffId);
					$workshopCountArr 		 = totalWorkshopCountReport();	 
					
					$workshopRegChoices	= array();
					foreach($workshopDetailsArray as $keyWorkshopclsf=>$rowWorkshopclsf)
					{	
						foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
						{
							$workshopRegChoices[$rowRegClasf['WORKSHOP_TYPE']][$keyWorkshopclsf][$keyRegClasf] = $rowRegClasf;
						}
					}	
				}
			?>
				<div class="link" use="delegateNameFld" style="text-align:left;">
					<span use="delegateName" style="text-transform:uppercase;">DELEGATE</span>
					<i class="fas fa-times pull-right" use="removeDelegate" onClick="$(this).parent().closest('div[use=registrationFormContainer]').remove();"></i>
					<span class="pull-right" use="subTotalAmount" style="font-style:italic; margin-right:20px;"></span> 
				</div>
				<ul class="submenu" style="display: block"  displayStatus="Y" use="indvDataContainer">
					<li>
						<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
							<label for="user_first_name">First Name</label>
							<input type="text" class="form-control" name="user_first_name" defaultname="user_first_name" value="" style="text-transform:uppercase;" required>
							<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
						</div>
						<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
							<label for="user_last_name">Last Name</label>
							<input type="text" class="form-control" name="user_last_name" defaultname="user_last_name" value="" style="text-transform:uppercase;" required>
							<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
						</div>								
						<div class="col-xs-6 form-group input-material"  actAs='fieldContainer'>
							<label for="user_mobile">Email-id</label>
							<input type="text" class="form-control" style="text-transform:lowercase;" onChange="checkUserEmail(this)" id="user_email_id" name="user_email" defaultname="user_email" usefor="email" value="" required>
							<input type="hidden" use="email_validated" name="email_validated" defaultname="email_validated" value="N">
							<div class = "alert alert-danger" callFor='alert'>Invalid Data.</div>
							<div class = "alert alert-primary" use="emailProcessing"><img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;"></div>
						</div>
						<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
							<label for="user_mobile">Mobile</label>
							<input type="text" class="form-control" style="text-transform:uppercase;" onChange="checkMobileNo(this)" name="user_mobile" defaultname="user_mobile" usefor="mobile" value="" required>
							<input type="hidden" use="mobile_validated" name="mobile_validated" defaultname="mobile_validated" value="N">
							<div class = "alert alert-danger" callFor='alert'>Invalid Data.</div>
							<div class = "alert alert-primary" use="mobileProcessing"><img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;"></div>
						</div>
						<div class="col-xs-6 form-group" actAs='fieldContainer'>
							<label class="select-lable dis_abled">Country</label>
							<select class="form-control select" name="user_country" defaultname="user_country" forType="country" style="text-transform:uppercase;"  required>
								<option value="">-- Select Country --</option>
								<?php
								$sqlFetchCountry   = array();
								$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																   WHERE `status` =? 
																ORDER BY `country_name` ASC";
																
								$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A', 'TYP' => 's');	
								
								$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
								if($resultFetchCountry)
								{
									foreach($resultFetchCountry as $keyCountry=>$rowFetchCountry)
									{
								?>
										<option value="<?=$rowFetchCountry['country_id']?>" <?=($rowFetchCountry['country_id']==$userRec['user_country_id'])?'selected':''?>><?=$rowFetchCountry['country_name']?></option>
								<?php
									}
								}
								?>
							</select>
							<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
						</div>
						<div class="col-xs-6 form-group" actAs='fieldContainer'>
							<label class="select-lable dis_abled">State</label>
							<select class="form-control select" name="user_state" defaultname="user_state" forType="state" style="text-transform:uppercase;" required>
								<option value="">-- Select Country First --</option>
							</select>
							<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
						</div>
						
						<div class="col-xs-12 form-group " actAs='fieldContainer'>
							<div class="checkbox">
								<label class="select-lable">Conference Registration</label>
								<div>
			<?
				foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
				{
					$classificationType = getRegClsfType($key);
					if($classificationType =='DELEGATE')
					{
						$rate = '';
						if(floatval($registrationDetailsVal['AMOUNT'])>0)
						{
							$rate = $registrationDetailsVal['CURRENCY'].' '.number_format(($registrationDetailsVal['AMOUNT']));
						}
						else
						{
							$rate = "Complimentary";
						}
			?>
									<label class="container-box" style="float:left; margin-right:20px;">
									  <?=$registrationDetailsVal['CLASSIFICATION_TITTLE'].'('.$rate.')'?>
									  <input type="radio" name="registration_classification_id" defaultname="registration_classification_id"  
											 operationMode="registration_tariff" operationModeType="conference" 
											 value="<?=$registrationDetailsVal['REG_CLASSIFICATION_ID']?>" 
											 currency="<?=$registrationDetailsVal['CURRENCY']?>" 
											 amount="<?=$registrationDetailsVal['AMOUNT']?>"
											 invoiceTitle="Registration - <?=$registrationDetailsVal['CLASSIFICATION_TITTLE']?>" required>
									  <span class="checkmark"></span>
									</label>
			<?	
					}
				}
			?>				
									&nbsp;
								</div>																				
							</div>
							<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
						</div>
						
						<input type="hidden" name="hotel_id" defaultname="hotel_id">
						<input type="hidden" name="accomPackId" defaultname="accomPackId" value=""/>
			<?
				/*
				$packCount = 0;
				$residentialAccommodationPackageId 	= $cfg['RESIDENTIAL_PACKAGE_ARRAY'];
				$residentialPackDataOrganizer		= array();
				foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
				{
					$classificationType = getRegClsfType($key);
					if($classificationType =='COMBO' && $registrationDetailsVal['ISOFFER'] == 'N')
					{
						$packCount++;
						$sqlHotel				= array();
						$sqlHotel['QUERY']	 	= "SELECT * 
													 FROM "._DB_MASTER_HOTEL_."
													WHERE id = ?";
						$sqlHotel['PARAM'][]    = array('FILD' => 'status', 'DATA' =>$registrationDetailsVal['HOTEL_ID'],  'TYP' => 's');
						$resHotel		    	= $mycms->sql_select($sqlHotel);
						$rowHotel				= $resHotel[0];
						
						$registrationDetailsVal['HOTEL_NAME'] = $rowHotel['hotel_name'];
						
						$nights					= '';					
						switch($registrationDetailsVal['REG_CLASSIFICATION_ID'])
						{
							case '7':
							case '9':
							case '11':
							case '13':
							case '15':
							case '17':
								$nights = '2 Nights Stay';
								break;
							case '8':
							case '10':
							case '12':
							case '14':
							case '16':
							case '18':
								$nights = '3 Nights Stay';
								break;
						}
						
						$sharing				= '';
						switch($registrationDetailsVal['REG_CLASSIFICATION_ID'])
						{
							case '7':
							case '8':
							case '11':
							case '12':
							case '15':
							case '16':
								$sharing = 'Individual';
								break;
							case '9':
							case '10':
							case '13':
							case '14':
							case '17':
							case '18':
								$sharing = 'Sharing';
								break;
						}
						
						$residentialPackDataOrganizer[$nights][$sharing][] = $registrationDetailsVal;
					}
				}
				
				foreach($residentialPackDataOrganizer as $StayName=>$nightDetailsVal)
				{
					$staykeys = array_keys($nightDetailsVal);
					
					foreach($nightDetailsVal as $ShareState=>$packDetailsVal)
					{
			?>
						<div class="col-xs-12 form-group ">
							<div class="checkbox">
								<label class="select-lable" ><?=$StayName?> - <?=strtoupper($ShareState)?></label>
								<div>
			<?
						
						$residentialDisplayName = '';
						foreach($packDetailsVal as $kkl=>$registrationDetailsVal)
						{
							$residentialDisplayName = $registrationDetailsVal['HOTEL_NAME'];
			?>
									<label class="container-box" style="float:left; margin-right:20px;">
										 <?=$residentialDisplayName.'('.$registrationDetailsVal['CURRENCY'].' '.number_format(($registrationDetailsVal['AMOUNT'])).')'?>
										 <input type="radio" name="registration_classification_id" id="registration_classification_id"  
												operationMode="registration_tariff" value="<?=$registrationDetailsVal['REG_CLASSIFICATION_ID']?>" 
												operationModeType="residential" 
												accommodationType="<?=in_array($registrationDetailsVal['REG_CLASSIFICATION_ID'],$cfg['RESIDENTIAL_SHARING_CLASF_ID'])?"SHARED":"INDIVIDUAL"?>"
												currency="<?=$registrationDetailsVal['CURRENCY']?>" 
												amount="<?=$registrationDetailsVal['AMOUNT']?>"
												invoiceTitle="Residential Package - <?=$StayName?>-<?=$ShareState?>@<?=$registrationDetailsVal['HOTEL_NAME']?>"
												offer="<?=$registrationDetailsVal['ISOFFER']?>"
												accommodationPackageId = "<?=$residentialAccommodationPackageId[$registrationDetailsVal['REG_CLASSIFICATION_ID']]?>"
												hotel_id="<?=$registrationDetailsVal['HOTEL_ID']?>" required>
									  <span class="checkmark"></span>
									</label>
			<?
						}
			?>
								&nbsp;
								</div>																				
							</div>
						</div>	
			<?
						
					}
				}							
				*/
				
				if(isset($workshopRegChoices['MASTER CLASS']) && sizeof($workshopRegChoices['MASTER CLASS']) > 0)
				{
			?>
						<div class="col-xs-12 form-group" use="workshopContainers" style="display:none;" actAs='fieldContainer'>
							<div class="checkbox">
								<label class="select-lable">Master Class</label>
								<div>
			<?
					foreach($workshopRegChoices['MASTER CLASS'] as $keyWorkshopclsf=>$rowWorkshopclsf )
					{
						foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
						{
							$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
							
							$style 	= "";
							$span	= "";
							$spanCss =  '';
							if($workshopCount<1)
							{
								 $style = 'disabled="disabled"';
								 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
								 $spanCss = 'style="cursor:not-allowed;"';
							}
							
							$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
							
							if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 && $rowRegClasf['WORKSHOP_ID']!=5)
							{
								$workshopRateDisplay = "Included in Registration";
							}
							else if( $rowRegClasf['WORKSHOP_ID'] == 5)
							{
								$workshopRateDisplay = "";
							}
							
							if($workshopCount>0)
							{
			?>
								
									<label class="container-box" style="float:left; margin-right:20px; display:none;" use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr">
									  <?=$rowRegClasf['WORKSHOP_NAME']?>
									  <input type="radio" name="masterClassId" defaultname="masterClassId"  
											   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
											   operationMode="workshopId"  
											   amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
											   invoiceTitle="<?=$rowRegClasf['WORKSHOP_NAME']?>"
											   registrationClassfId="<?=$keyRegClasf?>" required>
									  <span class="checkmark"></span>
									</label>
			<?		
							}
						}
					}
			?>
								&nbsp;
								</div>																				
							</div>
							<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
						</div>
			<?
				}
				
				if(isset($workshopRegChoices['WORKSHOP']) && sizeof($workshopRegChoices['WORKSHOP']) > 0)
				{
			?>
						<div class="col-xs-12 form-group" use="workshopContainers" style="display:none;" actAs='fieldContainer'>
							<div class="checkbox">
								<label class="select-lable">Workshop</label>
								<div>
			<?
					foreach($workshopRegChoices['WORKSHOP'] as $keyWorkshopclsf=>$rowWorkshopclsf )
					{
						foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
						{
							$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
							
							$style 	= "";
							$span	= "";
							$spanCss =  '';
							if($workshopCount<1)
							{
								 $style = 'disabled="disabled"';
								 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
								 $spanCss = 'style="cursor:not-allowed;"';
							}
							
							$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
							
							if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 && $rowRegClasf['WORKSHOP_ID']!=5)
							{
								$workshopRateDisplay = "Included in Registration";
							}
							else if( $rowRegClasf['WORKSHOP_ID'] == 5)
							{
								$workshopRateDisplay = "";
							}
							
							if($workshopCount>0)
							{
			?>
								
									<label class="container-box" style="float:left; margin-right:20px; display:none;" use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr">
									  <?=$rowRegClasf['WORKSHOP_NAME']?>
									  <input type="radio" name="workshopId" defaultname="workshopId"
											   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
											   operationMode="workshopId"  
											   amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
											   invoiceTitle="<?=$rowRegClasf['WORKSHOP_NAME']?>"
											   registrationClassfId="<?=$keyRegClasf?>" required>
									  <span class="checkmark"></span>
									</label>
			<?	
							}	
						}
					}
			?>
								&nbsp;
								</div>																				
							</div>
							<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
						</div>
			<?
				}
				
				if(isset($workshopRegChoices['POST-CONFERENCE']) && sizeof($workshopRegChoices['POST-CONFERENCE']) > 0)
				{
			?>
						<div class="col-xs-12 form-group" use="workshopContainers" style="display:none;" actAs='fieldContainer'>
							<div class="checkbox">
								<label class="select-lable">Post-Congress Workshop</label>
								<div>
			<?
					foreach($workshopRegChoices['POST-CONFERENCE'] as $keyWorkshopclsf=>$rowWorkshopclsf )
					{
						foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
						{
							$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
							
							$style 	= "";
							$span	= "";
							$spanCss =  '';
							if($workshopCount<1)
							{
								 $style = 'disabled="disabled"';
								 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
								 $spanCss = 'style="cursor:not-allowed;"';
							}
							
							$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
							
							if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 && $rowRegClasf['WORKSHOP_ID']!=5)
							{
								$workshopRateDisplay = "Included in Registration";
							}
							else if( $rowRegClasf['WORKSHOP_ID'] == 5)
							{
								$workshopRateDisplay = "";
							}
							
							if($workshopCount>0)
							{
			?>
								
									<label class="container-box" style="float:left; margin-right:20px; display:none;" use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr">
									  <?=$rowRegClasf['WORKSHOP_NAME']?>
									  <input type="radio" name="postConference" defaultname="postConference" 
											   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
											   operationMode="workshopId"  
											   amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
											   invoiceTitle="<?=$rowRegClasf['WORKSHOP_NAME']?>"
											   registrationClassfId="<?=$keyRegClasf?>">
									  <span class="checkmark"></span>
									</label>
			<?	
							}	
						}
					}
			?>
								&nbsp;
								</div>																				
							</div>
							<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
						</div>
			<?
				}
			?>	
						<div class="col-xs-12 form-group" use="dinnerContainers" style="display:none;">
							<div class="checkbox">
								<label class="select-lable">Dinner</label>
								<div>
			<?
				$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
				foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
				{
					$rate = '';
					if(floatval($registrationDetailsVal['AMOUNT'])>0)
					{
						$rate = $registrationDetailsVal['CURRENCY'].' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
					}
			?>
									<label class="container-box" style="float:left; margin-right:20px;">
									  <?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE'].'('.$rate.')'?>
									  <input type="radio" name="dinner_value" defaultname="dinner_value" 
											 value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
											 operationMode="dinner"  
											 amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
											 invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?>">
									  <span class="checkmark"></span>
									</label>
			<?
				}
			?>
									&nbsp;
								</div>																				
							</div>
						</div>
						
			<?
				$accompanyCatagory      = 2;
				$registrationAmount 	= $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
				$registrationCurrency 	= $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
				//$conferenceTariffArray
			?>			
						<div class="col-xs-12 form-group ">
							<div class="checkbox">
								<label class="select-lable" >Number of Accompanying Person(s)</label>
								<div>
									<label class="container-box" style="float:left; margin-right:20px;">None
									  <input type="radio" name="accompanyCount" defaultname="accompanyCount" use="accompanyCountSelect" value="0" 
											 amount="<?=0?>" 
											 invoiceTitle="Accompanying Person"
											 checked="checked" required>
									  <span class="checkmark"></span>
									</label>
									<label class="container-box" style="float:left; margin-right:20px;">One
									  <input type="radio" name="accompanyCount" defaultname="accompanyCount" use="accompanyCountSelect" value="1" 
											 amount="<?=floatval($registrationAmount)*1?>"
											 invoiceTitle="Accompanying - 1 Person">
									  <span class="checkmark"></span>
									</label>
									<label class="container-box" style="float:left; margin-right:20px;">Two
									  <input type="radio" name="accompanyCount" defaultname="accompanyCount" use="accompanyCountSelect" value="2" 
											 amount="<?=floatval($registrationAmount)*2?>"
											 invoiceTitle="Accompanying - 2 Person">
									  <span class="checkmark"></span>
									</label>
									<label class="container-box" style="float:left; margin-right:20px;">Three
									  <input type="radio" name="accompanyCount" defaultname="accompanyCount" use="accompanyCountSelect" value="3" 
											 amount="<?=floatval($registrationAmount)*3?>"
											 invoiceTitle="Accompanying - 3 Person">
									  <span class="checkmark"></span>
									</label>
									<label class="container-box" style="float:left; margin-right:20px;">Four
									  <input type="radio" name="accompanyCount" defaultname="accompanyCount" use="accompanyCountSelect" value="4" 
											 amount="<?=floatval($registrationAmount)*4?>"
											 invoiceTitle="Accompanying - 4 Person">
									  <span class="checkmark"></span>
									</label>
									<i class="itemPrice pull-right"> 
									<?
									if(floatval($registrationAmount)>0)
									{
										echo '@ '.$registrationCurrency.' '.number_format($registrationAmount,2);
									}						
									?>
									</i>
									&nbsp;
								</div>																				
							</div>
						</div>
			<?
				for($i=0; $i<4	;$i++)
				{
			?>							
						<div class="col-xs-12" use="accompanyDetails" index="<?=$i+1?>" style="display:none;">
							<h4>ACCOMPANY <?=$i+1?></h4>
							<div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
								<label for="accompany_name_add_1">Name</label>
								<input type="text" class="form-control" name="accompanyNameAdd[<?=$i?>]"  defaultname="accompanyNameAdd-[<?=$i?>]" style="text-transform:uppercase;">
								<input type="hidden" name="accompanySelectedAdd[<?=$i?>]"  defaultname="accompanySelectedAdd-[<?=$i?>]" value="0" />
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>	
							<div class="col-xs-8 form-group" actAs='fieldContainer'>
								<div class="checkbox">
									<label class="select-lable">DINNER</label>
							<?
							$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
							foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
							{
							?>
									<label class="container-box">
										<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
										<i class="itemPrice right-i pull-right"> 
							<?
								if(floatval($registrationDetailsVal['AMOUNT'])>0)
								{
									echo $registrationDetailsVal['CURRENCY'].' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
								}
														
							?>
										</i>
										<input type="checkbox" name="accompanyDinnerValue[<?=$i?>]" defaultname="accompanyDinnerValue-[<?=$i?>]" id="dinner_value" 
											   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
											   operationMode="dinner"  
											   amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
											   invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 1"/>
										<span class="checkmark"></span>
									</label>
							<?
							}
							?>						

								</div>
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>								
							<div class="col-xs-4 form-group" actAs='fieldContainer'>
								<div class="checkbox">
									<div>
										  <label class="container-box" style="float:left; margin-right:20px;">Veg
											  <input type="radio" groupName="accompany_food_choice" name="accompanyFoodChoice[<?=$i?>]" defaultname="accompanyFoodChoice-[<?=$i?>]" value="VEG">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Non-Veg
											  <input type="radio" groupName="accompany_food_choice" name="accompanyFoodChoice[<?=$i?>]" defaultname="accompanyFoodChoice-[<?=$i?>]" value="NON_VEG">
											  <span class="checkmark"></span>
											</label>
											&nbsp;
										</div>
									<label class="select-lable" >Food Preference</label>
								</div>
								<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
							</div>
						</div>
			<?
				}
			?>		
						<div class=" col-xs-12 text-center pull-right">
							<button type="button" class="submit pull-left" style="background:#0078b3;" use='finalizeButton' onClick="triggerFinalizeButton(this);">All Done! Finalize Now</button>
							<button type="button" class="submit pull-right" use='nextButton' onClick="triggerNextButton(this);">Next</button>
						</div>
																	 
						<div class="clearfix"></div>
					</li>
				</ul>
			</div>
		</div>
			
		<div id="uploadModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post">
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>Uploading</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Uploading Data</span></h2></div>
					
					<div class="col-xs-10 profileright-section">							
						<div class="login-user" style="margin-top: 25px;">
							<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
						</div>	
					</div>	
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
<?
		$passThrough = true;
		if(trim($_REQUEST['exhibitorId'])=='' || trim($_REQUEST['cutoffid'])=='')
		{
			$passThrough = false;
?>
		<div id="invalidModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>Unauthorized Access</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>You are not authorized to access this section</span></h2></div>
					
					<div class="col-xs-10 profileright-section">							
						<div class="login-user" style="margin-top: 25px;">
							<button type="button" use='submitBtn' onClick="window.location.href='https://www.aiccrcog2019.com/sponsor.php';">Back</button>
						</div>	
					</div>	
					<div class="modal-footer"></div>
				</div>
		  </div>
		</div>
		
		<script>
		$(document).ready(function(){
			$('#invalidModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: true
			});
		});	
		</script>
<?
		}

		if($passThrough && $cfg['EXHIBITOR.BULK.SUBMIT.LASTDATE'] < date('Y-m-d'))
		{
?>
		<div id="expiredModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>SUBMISSION CLOSED</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Exhibitor bulk submission has been closed</span></h2></div>
					
					<div class="col-xs-10 profileright-section">							
						<div class="login-user" style="margin-top: 25px;">
							<button type="button" use='submitBtn' onClick="window.location.href='https://www.aiccrcog2019.com/sponsor.php';">Back</button>
						</div>	
					</div>	
					<div class="modal-footer"></div>
				</div>
		  </div>
		</div>
		
		<script>
		$(document).ready(function(){
			$('#expiredModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: true
			});
		});	
		</script>
<?
		}
?>		
		
		<script>
		$(document).ready(function(){
			setTimeout(function(){
				$('#uploadModal').modal({
					backdrop: 'static',
					keyboard: false,
					show	: true
				});
				setTimeout(function(){
					window.document.location.href = "<?=_BASE_URL_?>exhibitor.bulkentry.screen.process.php?FORCED=Y&bulkUploadSessionId=<?=$lastInsertedSessionDetails?>"	
				},1000);
			},<?=$ExpPeriod*60*1000?>);	
		});
		</script>

		<script>
			$(document).ready(function(){
				setInterval( function(){
					var windowHieght = $( document ).height();
					$(".left-container-box").css('min-height',windowHieght+'px');
				}, 1000);
			});
		</script>
	</body>
	
</html>
<?php
	
?>