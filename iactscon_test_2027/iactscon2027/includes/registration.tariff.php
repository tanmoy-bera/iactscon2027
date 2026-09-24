<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");

$mycms->removeAllSession();
$mycms->removeSession('SLIP_ID');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Registration | AICC RCOG 2019 ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>registration.tariff.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<script src="<?=_BASE_URL_?>js/website/returnData.process.js"></script>
	</head>
	<body> 
<?
		$cutoffs 			= fullCutoffArray();	
		$currentCutoffId 	= getTariffCutoffId();	
?>       
		<form name="registrationForm" action="<?=_BASE_URL_?>registration.process.php">
		<input type="hidden" name="act" value="combinedRegistrationProcess" />
		<input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>"/>
		<input type="hidden" name="reg_area" value="FRONT" />
		<input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
		<input type="hidden" name="registration_cutoff" id="registration_cutoff" value="<?=$currentCutoffId?>" /> 
        <div class="col-xs-4 left-container-box regTarrif_leftPanel">
            <div class="col-xs-4 logo">
                <img src="<?=_BASE_URL_?>images/logo_white.png" alt="logo" style="width: 100%;">              
            </div>
            <div class="col-xs-7 col-xs-offset-1 timer" style="padding: 0">
				<div>
                    <h5 class="cutoffNameTop">Register</h5>
                    <h4 style="color: white" class="cutoffName"><?=getCutoffName($currentCutoffId)?></h4>
                </div>
                <div class="timeLeftWrapper">
					<div style="font-size: 20px;" class="timeLeft">
                        <div class="col-xs-3 timeLeftDays" style="padding: 8px 18px; background: #41bff5; line-height: 18px; text-align: center">
                            <p style="margin-bottom: 0; color: white; font-style: italic;"><span id="dday">000</span> <sub style="bottom: 0; font-size: 12px;">DAYS</sub></p>
						</div>
                        <div class="col-xs-3 timeLeftHours" style="padding: 8px 18px; background: #56c6f6; line-height: 18px; text-align: center">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dhour">00</span> <sub style="bottom: 0; font-size: 12px;">HRS.</sub></p>
						</div>
                        <div class="col-xs-3 timeLeftMinutes" style="padding: 8px 18px; background:#6bcdf7; line-height: 18px; text-align: center">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dmin">00</span> <sub style="bottom: 0; font-size: 12px;">MIN.</sub></p>
						</div>
                        <div class="col-xs-3 timeLeftSeconds" style="padding: 8px 18px;  background: #80d4f8; line-height: 18px; text-align: center">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dsec">00</span> <sub style="bottom: 0; font-size: 12px;">SEC.</sub></p>
						</div>
                    </div>
                </div>
				<?	
				$sqlcutoff['QUERY']   = "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = 'A' AND `id` = ?";
				$sqlcutoff['PARAM'][] = array('FILD' => 'id', 'DATA' =>$currentCutoffId,  'TYP' => 's');
				$rescutoff		  	  = $mycms->sql_select($sqlcutoff);
				$endDate		  	  = $rescutoff[0]['end_date'];
				$dateArr		   	  = explode("-",$endDate);
				?>
				<script>
				//  Change the items below to create your countdown target date and announcement once the target date and time are reached.  
				var current="";     			 //enter what you want the script to display when the target date and time are reached, limit to 20 characters
				var year= <?=$dateArr[0]?>;      //Enter the count down target date YEAR
				var month= <?=$dateArr[1]?>;     //Enter the count down target date MONTH
				var day= <?=$dateArr[2]?>;       //>Enter the count down target date DAY
				var hour=23;          			 //Enter the count down target date HOUR (24 hour clock)
				var minute=59;        			 //Enter the count down target date MINUTE
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
						document.getElementById('dday').innerHTML=dday;
						document.getElementById('dhour').innerHTML=dhour;
						document.getElementById('dmin').innerHTML=dmin;
						document.getElementById('dsec').innerHTML=dsec;
						setTimeout("countdown(theyear,themonth,theday,thehour,theminute)",1000);
					}
				}
				countdown(year,month,day,hour,minute);
				</script>
            </div>
            
            <div class="col-xs-12 menu" style="padding: 0">
<?
		if($currentCutoffId > 0)
		{
			$conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
			$workshopDetailsArray 	 = getAllWorkshopTariffs($currentCutoffId);
			$workshopCountArr 		 = totalWorkshopCountReport();	 
?>
                <ul use="leftAccordion" class="accordion">
                  <li>
                    <div class="link" use="accordianL1TriggerDiv">CONFERENCE REGISTRATION</div>
                    <ul class="submenu" style="display:block;">
<?
			foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
			{
				$classificationType = getRegClsfType($key);
				if($classificationType =='DELEGATE')
				{
?>
						<li>
						<a>
							<label class="container-box menu-container-box">  
								<i class="itemTitle"><?=$registrationDetailsVal['CLASSIFICATION_TITTLE']?></i> 
								<i class="itemPrice pull-right"> 
								<?
									if(floatval($registrationDetailsVal['AMOUNT'])>0)
									{
										echo $registrationDetailsVal['CURRENCY'].' '.number_format(($registrationDetailsVal['AMOUNT']));
									}
									else
									{
										echo "Complimentary";
									}
								?>
								</i>
								<input type="checkbox" 
									 name="registration_classification_id[]" id="registration_classification_id"  
									 operationMode="registration_tariff" operationModeType="conference" 
									 value="<?=$registrationDetailsVal['REG_CLASSIFICATION_ID']?>" 
									 currency="<?=$registrationDetailsVal['CURRENCY']?>" 
									 amount="<?=$registrationDetailsVal['AMOUNT']?>"
									 invoiceTitle="Registration - <?=$registrationDetailsVal['CLASSIFICATION_TITTLE']?>"> 
								<span class="checkmark menu-checkmark"></span>
							</label>
						</a>
						</li>
<?
				}
			}
?>		
                    </ul>
                  </li>
                  <li>
                   <div class="link" use="accordianL1TriggerDiv">RESIDENTIAL PACKAGE</div>
				   <input type="hidden" name="hotel_id">
				   <input type="hidden" name="accomPackId" value=""/>
<?
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
					
					$residentialPackDataOrganizer[$registrationDetailsVal['CLASSIFICATION_TITTLE']][] = $registrationDetailsVal;
				}
			}
?>
                    <ul class="submenu" style="display:none;">
<?
			foreach($residentialPackDataOrganizer as $PackName=>$packDetailsVal)
			{
?>
						<li>
						  <div class="submenuHead" use="accordianL2TriggerDiv"><?=$PackName?></div>						  	
							<ul class="submenu" style="display: none">
<?
				foreach($packDetailsVal as $kkl=>$registrationDetailsVal)
				{
?>
								<li>
									<a>
										<label class="container-box menu-container-box"> 
										<i class="itemTitle"><?=$registrationDetailsVal['HOTEL_NAME']?></i> 
										<i class="itemPrice pull-right"> <?=$registrationDetailsVal['CURRENCY']?> <?=number_format(($registrationDetailsVal['AMOUNT']))?></i>
											<input  type="checkbox" 
													name="registration_classification_id[]" id="registration_classification_id"  
													operationMode="registration_tariff" value="<?=$registrationDetailsVal['REG_CLASSIFICATION_ID']?>" 
													operationModeType="residential" 
													accommodationType="<?=in_array($registrationDetailsVal['REG_CLASSIFICATION_ID'],$cfg['RESIDENTIAL_SHARING_CLASF_ID'])?"SHARED":"INDIVIDUAL"?>"
													currency="<?=$registrationDetailsVal['CURRENCY']?>" 
													amount="<?=$registrationDetailsVal['AMOUNT']?>"
													invoiceTitle="Residential Pack - <?=$PackName?>@<?=$registrationDetailsVal['HOTEL_NAME']?>"
													offer="<?=$registrationDetailsVal['ISOFFER']?>"
													accommodationPackageId = "<?=$residentialAccommodationPackageId[$registrationDetailsVal['REG_CLASSIFICATION_ID']]?>"
													hotel_id="<?=$registrationDetailsVal['HOTEL_ID']?>">
											<span class="checkmark menu-checkmark"></span>
										</label>
									</a>
								</li>
<?
				}
?>		
						  </ul>
						</li>  
<?
			}
?>	
                    </ul>
                  </li>
<?
			$workshopRegChoices	= array();
			foreach($workshopDetailsArray as $keyWorkshopclsf=>$rowWorkshopclsf)
			{	
				foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
				{
					$workshopRegChoices[$rowRegClasf['WORKSHOP_TYPE']][$keyWorkshopclsf][$keyRegClasf] = $rowRegClasf;
				}
			}
			
			if(false && isset($workshopRegChoices['POST-CONFERENCE']) && sizeof($workshopRegChoices['POST-CONFERENCE']) > 0)
			{
?>
				  <li>
                    <div class="link" use="accordianL1TriggerDiv">POST-CONFERENCE WORKSHOP</div>
                    <ul class="submenu" style="display:block;">
<?
				foreach($workshopRegChoices['POST-CONFERENCE'] as $keyWorkshopclsf=>$rowWorkshopclsf )
				{
					foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
					{
						$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
						
						if($workshopCount<1)
						{
							 $style = 'disabled="disabled"';
							 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
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
?>
						<li use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr">
							<a>
							  <i>
							  <input type="checkbox" name="workshop_id[]" id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"  
								     value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
								     operationMode="workshopId"  
									 amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
									 invoiceTitle="Post Conference Workshop - <?=$rowRegClasf['WORKSHOP_NAME']?>"
								     registrationClassfId="<?=$keyRegClasf?>" />
							  &nbsp; &nbsp; <?=$rowRegClasf['WORKSHOP_NAME']?>
							  </i>
							  <i class="pull-right">
								<?=$workshopRateDisplay?>
							  </i>
							</a>
						</li>
<?
					}
				}
?>	
                    </ul>
                  </li>
<?
			}
?>
                </ul>
				<script>
					$(document).ready(function(){						
						$.each($("ul[use=leftAccordion]").children("li"), function(i,level1){	
							$(level1).attr("parent","0");						
							$(level1).attr("level","1");
							$(level1).attr("sequence",(i+1));
							$(level1).children("div[use=accordianL1TriggerDiv]").attr("level","1");
							$(level1).children("div[use=accordianL1TriggerDiv]").attr("sequence",(i+1));
							
							$.each($(level1).children("ul").children("li"), function(j,level2){
								$(level2).attr("parent",(i+1));						
								$(level2).attr("level","2");
								$(level2).attr("sequence",(j+1));								
								$(level2).children("div[use=accordianL2TriggerDiv]").attr("level","2");
								$(level2).children("div[use=accordianL2TriggerDiv]").attr("sequence",(j+1));
								
								$.each($(level2).children("ul").children("li"), function(k,level3){	
									$(level3).attr("parent",(j+1));						
									$(level3).attr("level","3");
									$(level3).attr("sequence",(k+1));
								});
							});							
						});	
						
						$("ul[use=leftAccordion]").find("div[use=accordianL1TriggerDiv]").click(function(){
							var trigr 		= $(this);
							var li			= $(trigr).parent().closest("li");		
							var level 		= $(trigr).attr("level");		
							var sequence 	= $(trigr).attr("sequence");	
							
							$.each($("ul[use=leftAccordion]").find("div[use=accordianL1TriggerDiv]"),function(){
								var div 			= $(this);
								var divParent 		= $(div).parent().closest("li");		
								var divLevel 		= $(div).attr("level");		
								var divSequence 	= $(div).attr("sequence");
								
								console.log(divLevel+'>>'+divSequence);
								
								if(divLevel==level && divSequence!=sequence)
								{
									$(divParent).children("ul").slideUp();
								}	
							});	
							$(li).children("ul").slideDown();
							
						});						
						
						$("ul[use=leftAccordion]").find("div[use=accordianL2TriggerDiv]").click(function(){
							console.log("ss");
							
							var trigr 		= $(this);
							var li			= $(trigr).parent().closest("li");		
							var level 		= $(trigr).attr("level");		
							var sequence 	= $(trigr).attr("sequence");	
							
							$.each($("ul[use=leftAccordion]").find("div[use=accordianL2TriggerDiv]"),function(){
								var div 			= $(this);
								var divParent 		= $(div).parent().closest("li");		
								var divLevel 		= $(div).attr("level");		
								var divSequence 	= $(div).attr("sequence");
								
								if(divLevel==level && divSequence!=sequence)
								{
									$(divParent).children("ul").slideUp();
								}	
							});	
							$(li).children("ul").slideDown();
						});
						
						$("input[type=checkbox],input[type=radio]").click(function(){
							calculateTotalAmount();
						});
						
						$("input[type=checkbox][operationMode=registration_tariff]").each(function(){
							$(this).click(function(){	
															
								if($("ul[use=rightAccordion]").children("li[use=registrationOptions]").find("ul[level=1]").attr("displayStatus")=='N')
								{
									$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
									$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');
									$("ul[use=rightAccordion]").children("li[use=registrationOptions]").find("ul[level=1]").slideDown();
									$("ul[use=rightAccordion]").children("li[use=registrationOptions]").find("ul[level=1]").attr("displayStatus",'Y');
									
									$("div[use=Introduction]").hide();
									$("ul[use=rightAccordion]").show();
									$("div[use=totalAmount]").show();
								}
								
								var currChkbxStatus = $(this).attr("chkStatus");
								
								$("input[type=checkbox][operationMode=registration_tariff]").prop("checked",false);	
								$("input[type=checkbox][operationMode=registration_tariff]").attr("chkStatus","false");
								
								$("div[operetionMode=checkInCheckOutTr]").hide();
								$("div[use=ResidentialAccommodationAccompanyOption]").hide();
								
								if(currChkbxStatus=="true")
								{
									$(this).prop("checked",false);	
									$(this).attr("chkStatus","false");
									
									$("div[operationMode=chhoseServiceOptions][use=conferenceOptions]").hide();
									$("div[operationMode=chhoseServiceOptions][use=residentialOperations]").hide();
									$("div[operationMode=chhoseServiceOptions][use=defaultChoices]").slideDown();	
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
										
										$("div[operationMode=chhoseServiceOptions][use=defaultChoices]").hide();
										$("div[operationMode=chhoseServiceOptions][use=conferenceOptions]").hide();
										$("div[operationMode=chhoseServiceOptions][use=residentialOperations]").slideDown();
										
										$("input[type=hidden][name=accomPackId]").attr("value",packageId);
										$("input[type=hidden][name=hotel_id]").attr("value",hotel_id);
										
										$("div[operetionMode=checkInCheckOutTr][use='"+packageId+"']").slideDown();
										
										if(accommodationType=='SHARED')
										{
											$("div[use=ResidentialAccommodationAccompanyOption]").slideDown();
										}
									}
									else if(regType=='conference')
									{										
										$("div[operationMode=chhoseServiceOptions][use=defaultChoices]").hide();
										$("div[operationMode=chhoseServiceOptions][use=residentialOperations]").hide();
										$("div[operationMode=chhoseServiceOptions][use=conferenceOptions]").slideDown();
										
										$("label[operetionMode=workshopTariffTr]").hide();
										$("label[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
									}
									else
									{
										$("div[operationMode=chhoseServiceOptions][use=conferenceOptions]").hide();
										$("div[operationMode=chhoseServiceOptions][use=residentialOperations]").hide();
										$("div[operationMode=chhoseServiceOptions][use=defaultChoices]").slideDown();
									}
								}
								
								//calculateTotalAmount();
							});
						});		
						
						$.each($("ul[use=rightAccordion]").children("li"), function(i,level){	
							$(level).attr("sequence",(i+1));							
							$(level).children("div[use=rightAccordianTriggerDiv]").attr("sequence",(i+1));
							$(level).children("ul").attr("displayStatus",'N');
							$(level).children("ul").attr("level",'1');
							
							$(level).find("button[use=nextButton]").attr("sequence",(i+1));
							$(level).find("button[use=nextButton]").attr("goto",(i+1+1));
							
							$(level).children("div[use=rightAccordianL1TriggerDiv]").click(function(){	
								var seq = $(this).attr("sequence");
								var divParnt = $(this).parent().closest("li");
								var target = $(level).children("ul");
								if($(target).attr("displayStatus")=='N')
								{
									$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
									$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');
									$(level).children("ul").slideDown();
									$(level).children("ul").attr("displayStatus",'Y');
								}
							});
							
							$(level).find("button[use=nextButton]").click(function(){
								var thisSeq = $(this).attr('sequence');
								var nextSeq = $(this).attr('goto');
								
								if(validateOnNextButton(thisSeq))
								{
									var thisLi = $("ul[use=rightAccordion]").children("li[sequence='"+thisSeq+"']");
									$(thisLi).children("ul").slideUp();
									$(thisLi).children("ul").attr("displayStatus",'N');
									
									var nextLi = $("ul[use=rightAccordion]").children("li[sequence='"+nextSeq+"']");
									$(nextLi).show();
									$(nextLi).children("ul").slideDown();
									$(nextLi).children("ul").attr("displayStatus",'Y');
								}
							});		
							
							$(level).find("input[id=user_mobile]").blur(function(){
								checkMobileNo(this);
							});					
						});
						
						$("input[type=radio][use=accompanyCountSelect]").click(function(){
							var count = parseInt($(this).val());
							var haveCount = $("div[use=accompanyDetails]").length;							
							for(var i = 1; i<= count; i++)
							{
								$("div[use=accompanyDetails][index='"+i+"']").slideDown();
							}
							for(var j = (count+1); j <= haveCount; j++)
							{
								var accomDiv = $("div[use=accompanyDetails][index='"+j+"']");
								$(accomDiv).slideUp();
								$(accomDiv).find("input[type=text]").val('');
								$(accomDiv).find("input[type=radio]").prop('checked',false);
								$(accomDiv).find("input[type=checkbox]").prop('checked',false);
							}
						});
						
						$("input[type=radio][use=tariffPaymentMode]").click(function(){
							var forPay = $(this).attr("for");
							$("div[use=payRules]").hide();
							$("div[use=payRules][for='"+forPay+"']").slideDown();
							
							$("div[use=offlinePaymentOptionChoice]").hide();
							$("div[use=offlinePaymentOption]").hide();
							
							$("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").prop("checked",false);
							$("div[use=offlinePaymentOption]").find("input[type=text]").val('');
							$("div[use=offlinePaymentOption]").find("input[type=date]").val('');
							
							if($(this).val()=='OFFLINE')
							{
								$("div[use=offlinePaymentOptionChoice]").slideDown();
							}
						});	
						
						$("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").click(function(){							
							var forPay = $(this).attr("for");
							$("div[use=offlinePaymentOption]").hide();
							$("div[use=offlinePaymentOption][for='"+forPay+"']").slideDown();
						});
						
						$("form[name=registrationForm]").submit(function(evnt){							
							$.each($("ul[use=rightAccordion]").children("li"), function(i,level){
								try{
									var seq = $(this).attr("sequence");
									if(!validateOnNextButton(seq))
									{
										console.log('ERROR @ '+seq);
										evnt.preventDefault();
									}
								}
								catch(e)
								{
									console.log('ERROR : '+e.message);
									evnt.preventDefault();
								}
							});
						});						
										
					});
					
					function showChekinChekoutDate(obj)
					{
						$("input[type=radio][use=accoStartDate]").prop("checked",false);
						$(obj).prop("checked",true);
												
						var parent 			= $(obj).parent().closest("div[operetionMode=checkInCheckOutTr]");
						var checkoutDate 	= $(obj).attr("checkoutDate");
						$("input[type=radio][use=accoEndDate]").prop("checked",false);
						$(parent).find("input[use=accoEndDate][value='"+checkoutDate+"']").prop("checked",true);
					}
					
					function validateOnNextButton(seq)
					{
						var returnVal 	= true;
						var thisLi 		= $("ul[use=rightAccordion]").children("li[sequence='"+seq+"']");
						var thisLiUse 	= $(thisLi).attr("use");
						
						console.clear();
						console.log('initial>>'+returnVal);
						
						$.each($(thisLi).find("input[type=text],input[type=email],input[type=tel],input[type=number],input[type=date],textarea"),function(){
							var attr = $(this).attr('required');
							if (typeof attr !== typeof undefined && attr !== false) {
								console.log('hasReq>>'+$(this).attr('name'));
																
								if($(this).attr('validatePopOverIsSet')!='Y')
								{
									$(this).popover({content: 'Please enter proper value to this field.', trigger: 'focus', placement: 'auto '});
									$(this).attr("validatePopOverIsSet",'Y');
								}
								
								if($.trim($(this).val())=='')
								{
									$(this).focus();
									$(this).popover('show');
									returnVal = false;
									return false;
								}
							}
						});	
						console.log('text>>'+returnVal);
						if(!returnVal) return false;
						
						$.each($(thisLi).find("select"),function(){
							var attr = $(this).attr('required');
							if (typeof attr !== typeof undefined && attr !== false) {
								console.log('hasReq>>'+$(this).attr('name'));
								if($(this).attr('validatePopOverIsSet')!='Y')
								{
									$(this).popover({content: 'Please select a value for this field.', trigger: 'focus', placement: 'auto '});
									$(this).attr("validatePopOverIsSet",'Y');
								}
								
								if($.trim($(this).val())=='')
								{
									$(this).focus();
									$(this).popover('show');
									returnVal = false;
									return false;
								}
							}
						});	
						console.log('select>>'+returnVal);	
						if(!returnVal) return false;		
							
						$.each($(thisLi).find("input[type=radio]"),function(){
							var hasRequired = false;
							var attr = $(this).attr('required');
							if (typeof attr !== typeof undefined && attr !== false) 
							{
								console.log('hasReq>>'+$(this).attr('name'));
								hasRequired = true;
								if($(this).attr('validatePopOverIsSet')!='Y')
								{
									$(this).popover({content: 'Please select a field.', trigger: 'focus', placement: 'auto '});
									$(this).attr("validatePopOverIsSet",'Y');
								}
							}
							if(hasRequired)
							{
								var name = $(this).attr("name");
								if($("input[type=radio][name='"+name+"']:checked").length == 0)
								{
									$("input[type=radio][name='"+name+"']").popover('show');
									returnVal = false;
									return false;
								}
							}
						});
						console.log('radio>>'+returnVal);	
						if(!returnVal) return false;	
						
						if(thisLiUse=='registrationOptions')
						{
							returnVal = validateRegistrationOptions(thisLi);
							console.log('registrationOptions>>'+returnVal);		
							if(!returnVal) return false;	
						}
						else if(thisLiUse=='registrationUserDetails')
						{
							returnVal = validateRegistrationUserDetails(thisLi);
							console.log('registrationUserDetails>>'+returnVal);	
							if(!returnVal) return false;		
						}
						else if(thisLiUse=='registrationAccompanyDetails')
						{
							returnVal = validateRegistrationAccompanyDetails(thisLi);
							console.log('registrationAccompanyDetails>>'+returnVal);	
							if(!returnVal) return false;		
						}
						else if(thisLiUse=='registrationPaymentOption')
						{
							returnVal = validateRegistrationPaymentOption(thisLi);
							console.log('registrationPaymentOption>>'+returnVal);	
							if(!returnVal) return false;		
						}
						else if(thisLiUse=='registrationPayment')
						{
							returnVal = validateRegistrationPayment(thisLi);
							console.log('registrationPayment>>'+returnVal);	
							if(!returnVal) return false;		
						}
						console.log('final>>'+returnVal);						
						return returnVal;
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
								if($(this).attr('validatePopOverIsSet')!='Y')
								{
									$(this).popover({content: 'Please select a field.', trigger: 'focus', placement: 'auto '});
									$(this).attr("validatePopOverIsSet",'Y');
								}
								
								var name = $(this).attr("name");
								if($("input[type=radio][name='"+name+"']:checked").length == 0)
								{
									$("input[type=radio][name='"+name+"']").popover('show');
									returnVal = false;
									return false;
								}
								if(accommodationType=='SHARED')
								{
									var preferredUserBlk = $("div[use=ResidentialAccommodationAccompanyOption]");
									$.each($(preferredUserBlk).find("input[type=text],input[type=email],input[type=tel],input[type=number]"),function(){										
										if($(this).attr('validatePopOverIsSet')!='Y')
										{
											$(this).popover({content: 'Please fill this field.', trigger: 'focus', placement: 'auto '});
											$(this).attr("validatePopOverIsSet",'Y');
										}
										
										if($.trim($(this).val())=='')
										{
											$(this).focus();
											$(this).popover('show');
											returnVal = false;
											return false;
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
					
					function checkUserEmail(obj)
					{
						var liParent 			= $(obj).parent().closest("li[use=registrationUserDetails]");
						var emailIdObj 			= $(liParent).find("#user_email_id");
						var emailId 			= $.trim($(emailIdObj).val());
						
						if($(emailIdObj).attr('validatePopOverIsSet')!='Y')
						{
							$(emailIdObj).popover({content: 'Please select a field.', trigger: 'focus', placement: 'auto '});
							$(emailIdObj).attr("validatePopOverIsSet",'Y');
						}
						
						if(emailId != '')
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
										if (JSONObject.STATUS == 'IN_USE')
										{
											var loginModal = $('#loginModal');
											$(loginModal).find("#user_email_id").val(emailId);
											
											$.ajax({
												type: "POST",
												url: jsBASE_URL+'login.process.php',
												data: 'act=loginUniqueSequence&user_email_id='+emailId,
												dataType: 'text',
												async: false,
												success:function(JSONObject)
												{
													$(loginModal).modal('show');
													$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();		
													$(obj).show();						
												}
											});
										}
										else if (JSONObject.STATUS == 'NOT_PAID')
										{
											var unpaidModalOnline = $('#unpaidModalOnline');
											$(unpaidModalOnline).find("#user_email_id").val(emailId);
											$(unpaidModalOnline).modal('show');
											$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();		
											$(obj).show();						
										}
										else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE')
										{
											var unpaidModalOffline = $('#unpaidModalOffline');
											$(unpaidModalOffline).modal('show');
											$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();		
											$(obj).show();	
										}
										else if (JSONObject.STATUS == 'PAY_NOT_SET_OFFLINE')
										{
											var payNotSetModalOffline = $('#payNotSetModalOffline');
											$(payNotSetModalOffline).modal('show');
											$(payNotSetModalOffline).modal('show');
											$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();		
											$(obj).show();
										}
										else if (JSONObject.STATUS == 'AVAILABLE')
										{
											enableAllFileds(liParent);
											$(liParent).find("div[use=emailProcessing]").find("img[use=loader]").hide();			
											
											var JSONObjectData = JSONObject.DATA;
											if(JSONObjectData)
											{
												$(liParent).find('#user_first_name').val(JSONObjectData.FIRST_NAME);
												$(liParent).find('#user_middle_name').val(JSONObjectData.MIDDLE_NAME);
												$(liParent).find('#user_last_name').val(JSONObjectData.LAST_NAME);
												$(liParent).find('#user_mobile').val(JSONObjectData.MOBILE_NO);
																							
												checkMobileNo($(liParent).find('#user_mobile'));
												
												$(liParent).find('#user_phone_no').val(JSONObjectData.PHONE_NO);
												$(liParent).find('#user_address').val(JSONObjectData.ADDRESS);
												$(liParent).find('#user_city').val(JSONObjectData.CITY);
												$(liParent).find('#user_postal_code').val(JSONObjectData.PIN_CODE);
												
												$(liParent).find('#user_country').val(JSONObjectData.COUNTRY_ID);											
												$(liParent).find('#user_country').trigger("change");
												
												$(liParent).find('#user_state').val(JSONObjectData.STATE_ID);
											}
											$(liParent).find("button[use=nextButton]").show();
										}
									}
								});
							 },500);
						 }
						 else
						 {
						 	$(emailIdObj).popover('show');						 	
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
					
					function checkMobileNo(mobileObj)
					{
						var mobile = $(mobileObj).val();
						var parent = $('ul[use=rightAccordion]').children('li[use=registrationUserDetails]');
						
						$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();
						$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").show();
						$(parent).find("input[id=user_mobile_validated]").val("N");
						
						if(mobile!="")
						{
							if(isNaN(mobile) || mobile.toString().length != 10)
							{
								$(mobileObj).popover('show');	
								$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();	
								$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();				
							}
							else
							{
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
											$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();	
											$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();				
											returnMessage = returnMessage.trim();
											if(returnMessage == 'IN_USE')
											{
												$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability][state=used]").show();
												$(parent).find("input[id=user_mobile_validated]").val("N");
											}
											else
											{
												$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability][state=available]").show();
												$(parent).find("div[use=mobileProcessing]").find("input[id=user_mobile_validated]").val("Y");
												console.log('>>'+$(parent).find("div[use=mobileProcessing]").find("input[name=user_mobile_validated]").val());
											}
										}
									});
								},500);
							}
						}	
					}
										
					function validateRegistrationUserDetails(obj)
					{
						if($(obj).find("input[id=user_mobile_validated]").val()!="Y")
						{
							$(obj).popover('show');	
							return false;
						}
						return true;
					}
					
					function validateRegistrationAccompanyDetails(obj)
					{
						var countObj 	= $(obj).find("input[type=radio][name=accompanyCount]:checked");						
						var count 		= $(countObj).val();
						
						for(var i = 0; i < count; i++)
						{
							var accomDiv 			= $("div[use=accompanyDetails][index='"+(i+1)+"']");
							
							var accomPanyNameObj 	= $(accomDiv).find("input[name='accompany_name_add["+i+"]']");
							var accomPanyName		= $(accomPanyNameObj).val();
							
							if($(accomPanyNameObj).attr('validatePopOverIsSet')!='Y')
							{
								$(accomPanyNameObj).popover({content: 'Please fill this field.', trigger: 'focus', placement: 'auto '});
								$(accomPanyNameObj).attr("validatePopOverIsSet",'Y');
							}
							
							if($.trim(accomPanyName) == '')
							{
								console.log('accomPanyName>>BLANK');	
								$(accomPanyNameObj).focus();
								$(accomPanyNameObj).popover('show');
								return false;
							}
							
							var accomPanyDinnrObj 	= $(accomDiv).find("input[name='accompany_dinner_value["+i+"]']");
							
							if($(accomPanyDinnrObj).is(":checked"))
							{
								var accomPanyfoodChecked 	= $(accomDiv).find("input[name='accompany_food_choice["+i+"]']:checked");
								
								if($(accomPanyfoodChecked).length == 0)
								{
									var accomPanyfoodObj 	= $(accomDiv).find("input[name='accompany_food_choice["+i+"]']").first();
									
									if($(accomPanyfoodObj).attr('validatePopOverIsSet')!='Y')
									{
										$(accomPanyfoodObj).popover({content: 'Please select a option.', trigger: 'focus', placement: 'auto '});
										$(accomPanyfoodObj).attr("validatePopOverIsSet",'Y');
									}
									
									console.log('accomPanyfoodChecked>>NOT CHEK');	
									$(accomPanyfoodObj).focus();
									$(accomPanyfoodObj).popover('show');
									return false;
								}
							}
						}
						return true;
					}
					
					function validateRegistrationPaymentOption(obj)
					{
						return true;
					}
					
					function validateRegistrationPayment(obj)
					{
						var paymentModeObj 	= $("li[use=registrationPaymentOption]").find("input[type=radio][name=registrationMode]:checked");
						var paymentMode		= $(paymentModeObj).val();
						
						if(paymentMode=='OFFLINE')
						{
							var paymentOptionCheckedOb = $("li[use=registrationPayment]").find("input[type=radio][name=payment_mode]:checked");
							
							if($(paymentOptionCheckedOb).length == 0)
							{
								var paymentOptionObj 	= $("li[use=registrationPayment]").find("input[type=radio][name=payment_mode]").first();
									
								if($(paymentOptionObj).attr('validatePopOverIsSet')!='Y')
								{
									$(paymentOptionObj).popover({content: 'Please select a option.', trigger: 'focus hover', placement: 'auto'});
									$(paymentOptionObj).attr("validatePopOverIsSet",'Y');
								}
								
								console.log('paymentOptionCheckedOb>>NOT SELECTED');
								$(paymentOptionObj).popover('show');
								$(paymentOptionObj).focus();
								return false;
							}
							
							var returnVal 				= true;
							var paymentOptionChecked	= $(paymentOptionCheckedOb).attr("for");
							
							$.each($("li[use=registrationPayment]").find("div[use=offlinePaymentOption][for='"+paymentOptionChecked+"']"),function(){
								var thiObj = $(this);
								$.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
									if($(validateObj).attr('validatePopOverIsSet')!='Y')
									{
										$(validateObj).popover({content: 'Please enter proper value to this field.', trigger: 'focus hover', placement: 'auto'});
										$(validateObj).attr("validatePopOverIsSet",'Y');
									}
									
									if($.trim($(validateObj).val())=='')
									{
										console.log('pay details>>BLANK');
										$(validateObj).popover('show');
										$(validateObj).focus();
										returnVal = false;
										return false;
									}
								});	
								if(!returnVal) return false;	
							});
							if(!returnVal) return false;	
						}
						
						if(!$("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]").is(":checked"))
						{
							var valOb1 = $("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]");
							if($(valOb1).attr('validatePopOverIsSet')!='Y')
							{
								$(valOb1).popover({content: 'Please select this field.', trigger: 'focus hover', placement: 'auto'});
								$(valOb1).attr("validatePopOverIsSet",'Y');
							}
							
							$(valOb1).popover('show');
							$(valOb1).focus();
							return false;
						}
						
						if(!$("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]").is(":checked"))
						{
							var valOb2 = $("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]");
							if($(valOb2).attr('validatePopOverIsSet')!='Y')
							{
								$(valOb2).popover({content: 'Please select this field.', trigger: 'focus hover', placement: 'auto'});
								$(valOb2).attr("validatePopOverIsSet",'Y');
							}
							
							$(valOb2).popover('show');
							$(valOb2).focus();
							return false;
						}
						
						return true;
					}
					
					function calculateTotalAmount()
					{
						console.log("====calculateTotalAmount====");
						var totalAmount = 0;
						var totTable  	= $("table[use=totalAmountTable]");
						$(totTable).children("tbody").find("tr").remove();
						$.each($("input[type=checkbox]:checked,input[type=radio]:checked"),function(){
							var attr = $(this).attr('amount');
							if (typeof attr !== typeof undefined && attr !== false) 
							{
								var amt 	= parseFloat(attr);	
								totalAmount = totalAmount+amt;
								
								console.log(">>amt"+amt+' ==> '+totalAmount);
								
								if(amt > 0)
								{							
									var cloned 	= $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();	
													
									$(cloned).attr("use","rowCloned");
									$(cloned).find("span[use=invTitle]").text($(this).attr('invoiceTitle'));
									$(cloned).find("span[use=amount]").text((amt).toFixed(2));
									$(cloned).show();
									$(totTable).children("tbody").append(cloned);
								}
							}
							
							if($(this).attr('operationMode')=='registrationMode' && $(this).attr('use')=='tariffPaymentMode')
							{
								if($(this).val()=='ONLINE')
								{
									var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
									var internetAmount = (totalAmount*internetHandling)/100;
									totalAmount = totalAmount+internetAmount;
									
									console.log(">>amt"+internetAmount+' ==> '+totalAmount);
									
									var cloned 	= $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();	
													
									$(cloned).attr("use","rowCloned");
									$(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
									$(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
									$(cloned).show();
									$(totTable).children("tbody").append(cloned);
								}
							}
						});
						$(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
						$("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
					}
				</script>
<?
		}
?>
            </div>
            
            <div class="col-xs-12 menu-links">                
                <p><a href="<?=_BASE_URL_?>terms.php"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Terms & Conditions</a></p>
                <p><a href="<?=_BASE_URL_?>privacy.php"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Privacy Policy</a></p>
                <p><a href="<?=_BASE_URL_?>cancellation.php"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Cancellation & Refund Policy</a></p>
                <p class="menu-login"><a href="<?=_BASE_URL_?>login.php">LOGIN</a></p>
            </div>            
        </div>        
        
        <div class="col-xs-8 right-container regTarrif_rightPanel">
            <div class="col-xs-12 col-xs-offset-0" style="padding: 0; margin-top: 15px;">
                <div use="Introduction" class="col-xs-12" style="padding: 0">
                	WELCOME to AICC RCOG
                </div>
				<ul use="rightAccordion" class="accordion" style="padding:0 0 0  0px; margin: 0; display:none;">
                    <li use="registrationOptions" class="rightPanel_chooseOption">
                        <div class="link" use="rightAccordianL1TriggerDiv">CHOOSE FROM OPTIONS</div>
						<ul class="submenu" style="display:block;" displayStatus="Y">
                            <li>								
								<div class="col-xs-12 form-group rPnl_chooseOption_conference" style="display:none;" use="conferenceOptions" operationMode="chhoseServiceOptions">
<?
		if(isset($workshopRegChoices['MASTER CLASS']) && sizeof($workshopRegChoices['MASTER CLASS']) > 0)
		{
?>
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >MASTER CLASS</label>
<?
			foreach($workshopRegChoices['MASTER CLASS'] as $keyWorkshopclsf=>$rowWorkshopclsf )
			{
				foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
				{
					$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
					
					if($workshopCount<1)
					{
						 $style = 'disabled="disabled"';
						 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
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
?>
											<label class="container-box" use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
												<?=$rowRegClasf['WORKSHOP_NAME']?>
												<input type="checkbox" name="workshop_id[]" id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"  
													   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
													   operationMode="workshopId"  
													   amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
													   invoiceTitle="<?=$rowRegClasf['WORKSHOP_NAME']?>"
													   registrationClassfId="<?=$keyRegClasf?>" />
												<span class="checkmark"></span>
											</label>
<?
				}
			}
?>		
										</div>
									</div>
<?
		}
		
		if(isset($workshopRegChoices['WORKSHOP']) && sizeof($workshopRegChoices['WORKSHOP']) > 0)
		{
?>
			 					<div class="col-xs-12 form-group rPnl_chooseOption_residential">
									<div class="checkbox">
										<label class="select-lable" >WORKSHOP</label>
<?
			foreach($workshopRegChoices['WORKSHOP'] as $keyWorkshopclsf=>$rowWorkshopclsf )
			{
				foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
				{
					$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
					
					if($workshopCount<1)
					{
						 $style = 'disabled="disabled"';
						 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
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
?>
										<label class="container-box" use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
											<?=$rowRegClasf['WORKSHOP_NAME']?>
											<input type="checkbox" name="workshop_id[]" id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"  
												   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
												   operationMode="workshopId"  
												   amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
												   invoiceTitle="Workshop - <?=$rowRegClasf['WORKSHOP_NAME']?>"
												   registrationClassfId="<?=$keyRegClasf?>" />
											<span class="checkmark"></span>
										</label>
<?
				}
			}
?>	
								</div>
							  </div>
<?
		}
?>
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >DINNER</label>
<?
		$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
		foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
		{
?>
											<label class="container-box">
												<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
												<i class="itemPrice pull-right right-i"> 
<?
			if(floatval($registrationDetailsVal['AMOUNT'])>0)
			{
				echo $registrationDetailsVal['CURRENCY'].' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
			}
									
?>
												</i>
												<input type="checkbox" name="dinner_value[]" id="dinner_value" 
													   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
													   operationMode="dinner"  
													   amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
													   invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?>"/>
												<span class="checkmark"></span>
												
											</label>
<?
		}
?>						
										</div>
									</div>
								</div>
								
								<div class="col-xs-12 form-group" style="display:none;" use="residentialOperations" operationMode="chhoseServiceOptions">
<?php
		$accommodationDetails = $cfg['ACCOMMODATION_PACKAGE_ARRAY'];	
		foreach($accommodationDetails as $packageId=>$rowAccommodation)
		{
?>
									<div class="col-xs-12 " style="padding: 0; display:none;" use="<?=$packageId?>" operetionMode="checkInCheckOutTr">
										<div class="col-xs-6 form-group ">
											<div class="radio">
												 <label class="select-lable" >CHECK-IN DATE</label>
<?
			$chchi = 0;
			foreach($rowAccommodation as $seq=>$accPackDet)
			{
				if($i==0)
				{
					$chout = $accPackDet['ENDDATE']['DATE'];
					$chchi++;
				}
?>
												<label class="container-box"><?=$accPackDet['STARTDATE']['DATE']?>
													<input type="radio" use="accoStartDate"  name="accDate[<?=$packageId?>]" value="<?=$accPackDet['STARTDATE']['ID']?>-<?=$accPackDet['ENDDATE']['ID']?>" checkoutDate="<?=$accPackDet['ENDDATE']['ID']?>" onClick="showChekinChekoutDate(this);">
													<span class="checkmark"></span>
												</label>
<?
			}
?>
											</div>
										</div>								
										
										<div class="col-xs-6 form-group ">
											<div class="radio">
												 <label class="select-lable" >CHECK-OUT DATE</label>
<?
			$chchi = 0;
			foreach($rowAccommodation as $seq=>$accPackDet)
			{
				if($i==0)
				{
					$chout = $accPackDet['ENDDATE']['DATE'];
					$chchi++;
				}
?>
												<label class="container-box"><?=$accPackDet['ENDDATE']['DATE']?>
													<input type="radio" value="<?=$accPackDet['ENDDATE']['ID']?>" use="accoEndDate" disabled="disabled">
													<span class="checkmark"></span>
												</label>
<?
			}
?>
											</div>
										</div>
									</div>
<?php
		}
?>
									<div class="col-xs-12 " style="padding: 0; display:none;" use="ResidentialAccommodationAccompanyOption">	
										<h4>ROOM SHARING PREFERENCE, IF ANY</h4>
										<div class="col-xs-6 form-group input-material">
											<input type="text" class="form-control" name="preffered_accommpany_name" id="preffered_accommpany_name">
											<label for="preffered_accommpany_name">NAME</label>
										</div>
										<div class="col-xs-6 form-group input-material">
											<input type="text" class="form-control" name="preffered_accommpany_mobile" id="preffered_accommpany_mobile">
											<label for="preffered_accommpany_mobile">MOBILE</label>
										</div>
										<div class="col-xs-12 form-group input-material">
											<input type="text" class="form-control" name="preffered_accommpany_email" id="preffered_accommpany_email">
											<label for="preffered_accommpany_email">EMAIL</label>
										</div>
									</div>	
								</div>
								
								<div class=" col-xs-2 text-center pull-right">
									<button type="button" class="submit" use='nextButton'>Next</button>
								</div>
								<div class="clearfix"></div>
                       		</li>
                        </ul>
                    </li>
                    <li use="registrationUserDetails" style="display:none;" class="rightPanel_userDetails">
                        <div class="link" use="rightAccordianL1TriggerDiv">USER DETAILS</div>
                        <ul class="submenu" style="display: none">
                            <li>
								<div class="col-xs-10 form-group input-material">
									<input type="email" style="text-transform:lowercase;" class="form-control" name="user_email_id"  id="user_email_id" required>
									<label for="user_email_id">E-mail</label>
								</div>
								<div class=" col-xs-2 form-group input-material" use="emailProcessing">
									<button type="button" class="submit" onClick="checkUserEmail(this);">GO</button>
									<img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;">
								</div>
                        		<div class="col-xs-10 form-group input-material dis_abled">
									<label for="user_mobile">Mobile</label>
									<input type="text" class="form-control" name="user_mobile" id="user_mobile" disabled="disabled" required>
								</div>
								<div class=" col-xs-2 form-group input-material" use="mobileProcessing">
									<img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;">
									<span class="alert alert-success" use='mobileAvailability' state='available' style="display:none;">Available</span>
									<span class="alert alert-danger" use='mobileAvailability' state='used' style="display:none;">Already in use</span>
									<input type="hidden" name="user_mobile_validated" id="user_mobile_validated" value="N" />
								</div>
								<div class="col-xs-12 form-group dis_abled">
									<div class="checkbox">
										<label class="select-lable" >Title</label>
										<div>
											<label class="container-box" style="float:left; margin-right:20px;">Dr
											  <input type="radio" name="user_initial_title" value="Dr" disabled="disabled" required>
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Prof
											  <input type="radio" name="user_initial_title" value="Prof" disabled="disabled">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Mr
											  <input type="radio" name="user_initial_title" value="Mr" disabled="disabled">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Ms
											  <input type="radio" name="user_initial_title" value="Ms" disabled="disabled">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Mrs
											  <input type="radio" name="user_initial_title" value="Mrs" disabled="disabled">
											  <span class="checkmark"></span>
											</label>
											&nbsp;
										</div>
									</div>
								</div>
                        		<div class="col-xs-6 form-group input-material dis_abled">
									<input type="text" class="form-control" name="user_first_name"  id="user_first_name" style="text-transform:uppercase;" disabled="disabled" required>
									<label for="user_first_name">First Name</label>
								</div>
								<div class="col-xs-6 form-group input-material dis_abled">
									<input type="text" class="form-control" name="user_last_name"  id="user_last_name" style="text-transform:uppercase;"  disabled="disabled" required>
									<label for="user_last_name">Last Name</label>
								</div>
								
								<div class="col-xs-12 form-group input-material dis_abled">
									<textarea class="form-control" name="user_address" id="user_address" style="text-transform:uppercase;"  disabled="disabled" required></textarea>
									<label for="user_address">Address</label>
								</div>
								<div class="col-xs-6 form-group dis_abled">
									<select class="form-control select" name="user_country" id="user_country" forType="country" style="text-transform:uppercase;"  disabled="disabled" required>
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
												<option value="<?=$rowFetchCountry['country_id']?>"><?=$rowFetchCountry['country_name']?></option>
										<?php
											}
										}
										?>
									</select>
									<label class="select-lable dis_abled">Country</label>
								</div>
								<div class="col-xs-6 form-group dis_abled">
									<select class="form-control select" name="user_state" id="user_state" forType="state" style="text-transform:uppercase;"  disabled="disabled" required>
										<option value="">-- Select Country First --</option>
									</select>
									<label class="select-lable dis_abled">State</label>
								</div>
								<div class="col-xs-6 form-group input-material dis_abled">
									<input type="text" class="form-control" ame="user_city" id="user_city" value="" style="text-transform:uppercase;" disabled="disabled" required>
									<label for="user_city">City</label>
								</div>
								<div class="col-xs-6 form-group input-material dis_abled">
									<input type="text" class="form-control" name="user_postal_code" id="user_postal_code" style="text-transform:uppercase;" disabled="disabled" required>
									<label for="user_postal_code">Postal Code</label>
								</div>
								
								<div class="col-xs-6 form-group dis_abled">
									<div class="checkbox ">
										<div>
											  <label class="container-box" style="float:left; margin-right:20px;">Male
												  <input type="radio" groupName="user_gender" name="user_gender" id="user_gender_male" value="Male" disabled="disabled" required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Female
												  <input type="radio" groupName="user_gender" name="user_gender" id="user_gender_female" value="Female" disabled="disabled">
												  <span class="checkmark"></span>
												</label>
												&nbsp;
											</div>
										<label class="select-lable" >Gender</label>
									</div>
								</div>									
								<div class="col-xs-6 form-group dis_abled">
									<div class="checkbox">
										<div>
											  <label class="container-box" style="float:left; margin-right:20px;">Veg
												  <input type="radio" groupName="user_food_choice" name="user_food_preference" id="user_food_veg" value="VEG" disabled="disabled"  required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Non-Veg
												  <input type="radio" groupName="user_food_choice" name="user_food_preference" id="user_food_nonveg" value="NON_VEG" disabled="disabled" >
												  <span class="checkmark"></span>
												</label>
												&nbsp;
											</div>
										<label class="select-lable" >Food Preference</label>
									</div>
								</div>	
                        
								<div class=" col-xs-2 text-center pull-right">
									<button type="button" class="submit" use='nextButton' style="display:none;">Next</button>
								</div>
                
        						<div class="clearfix"></div>
                            </li>
                        </ul>
                    </li>
					<li use="registrationAccompanyDetails" style="display:none;" class="rightPanel_accompany">
						<?
							//$accompanyCatagory      = 2;
							$accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 
							$registrationAmount 	= $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
							$registrationCurrency 	= $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
							//$conferenceTariffArray
						?>
						<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
						<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
                        <div class="link" use="rightAccordianL1TriggerDiv">ACCOMPANY</div>
                        <ul class="submenu" style="display: none">
                            <li>
								<div class="col-xs-12 form-group ">
									<div class="checkbox">
										<label class="select-lable" >Number of Accompanying Person(s)</label>
										<div>
											<label class="container-box" style="float:left; margin-right:20px;">None
											  <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="0" 
											  		 amount="<?=0?>" 
													 invoiceTitle="Accompanying Person"
													 checked="checked" required>
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">One
											  <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="1" 
											  		 amount="<?=floatval($registrationAmount)*1?>"
													 invoiceTitle="Accompanying - 1 Person">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Two
											  <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="2" 
											  		 amount="<?=floatval($registrationAmount)*2?>"
													 invoiceTitle="Accompanying - 2 Person">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Three
											  <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="3" 
											  		 amount="<?=floatval($registrationAmount)*3?>"
													 invoiceTitle="Accompanying - 3 Person">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:20px;">Four
											  <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="4" 
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
								                        		
								<div class="col-xs-12" use="accompanyDetails" index="1" style="display:none;">
									<h4>ACCOMPANY 1</h4>
									<div class="col-xs-12 form-group input-material">
										<input type="text" class="form-control" name="accompany_name_add[0]"  id="accompany_name_add_1" style="text-transform:uppercase;">
										<input type="hidden" name="accompany_selected_add[0]" value="0" />
										<label for="accompany_name_add_1">Name</label>
									</div>	
									<div class="col-xs-8 form-group ">
										<div class="checkbox">
											<label class="select-lable" >DINNER</label>
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
												<input type="checkbox" name="accompany_dinner_value[0]" id="dinner_value" 
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
									</div>								
									<div class="col-xs-4 form-group ">
										<div class="checkbox">
											<div>
												  <label class="container-box" style="float:left; margin-right:20px;">Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]" id="accompany_food_1_veg" value="VEG">
													  <span class="checkmark"></span>
													</label>
													<label class="container-box" style="float:left; margin-right:20px;">Non-Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]" id="accompany_food_1_nonveg" value="NON_VEG">
													  <span class="checkmark"></span>
													</label>
													&nbsp;
												</div>
											<label class="select-lable" >Food Preference</label>
										</div>
									</div>
								</div>
								<div class="col-xs-12" use="accompanyDetails" index="2" style="display:none;">
									<h4>ACCOMPANY 2</h4>
									<div class="col-xs-12 form-group input-material">
										<input type="text" class="form-control" name="accompany_name_add[1]"  id="accompany_name_add_2" style="text-transform:uppercase;">
										<input type="hidden" name="accompany_selected_add[1]" value="1" />
										<label for="accompany_name_add_2">Name</label>
									</div>	
									<div class="col-xs-8 form-group ">
										<div class="checkbox">
											<label class="select-lable" >DINNER</label>
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
												<input type="checkbox" name="accompany_dinner_value[1]" id="dinner_value" 
													   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
													   operationMode="dinner"  
													   amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
													   invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 2"/>
												<span class="checkmark"></span>
											</label>
<?
		}
?>						
										</div>
									</div>								
									<div class="col-xs-4 form-group">
										<div class="checkbox">
											<div>
												  <label class="container-box" style="float:left; margin-right:20px;">Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]" id="accompany_food_1_veg" value="VEG">
													  <span class="checkmark"></span>
													</label>
													<label class="container-box" style="float:left; margin-right:20px;">Non-Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]" id="accompany_food_1_nonveg" value="NON_VEG">
													  <span class="checkmark"></span>
													</label>
													&nbsp;
												</div>
											<label class="select-lable" >Food Preference</label>
										</div>
									</div>	
								</div>
								<div class="col-xs-12" use="accompanyDetails" index="3" style="display:none;">
									<h4>ACCOMPANY 3</h4>
									<div class="col-xs-12 form-group input-material">
										<input type="text" class="form-control" name="accompany_name_add[2]"  id="accompany_name_add_3" style="text-transform:uppercase;">
										<input type="hidden" name="accompany_selected_add[2]" value="2" />
										<label for="accompany_name_add_3">Name</label>
									</div>	
									<div class="col-xs-8 form-group ">
										<div class="checkbox">
											<label class="select-lable" >DINNER</label>
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
												<input type="checkbox" name="accompany_dinner_value[2]" id="dinner_value" 
													   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
													   operationMode="dinner"  
													   amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
													   invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 3"/>
												<span class="checkmark"></span>
												
											</label>
<?
		}
?>						
										</div>
									</div>								
									<div class="col-xs-4 form-group ">
										<div class="checkbox">
											<div>
												  <label class="container-box" style="float:left; margin-right:20px;">Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]" id="accompany_food_3_veg" value="VEG">
													  <span class="checkmark"></span>
													</label>
													<label class="container-box" style="float:left; margin-right:20px;">Non-Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]" id="accompany_food_3_nonveg" value="NON_VEG">
													  <span class="checkmark"></span>
													</label>
													&nbsp;
												</div>
											<label class="select-lable" >Food Preference</label>
										</div>
									</div>	
								</div>
								<div class="col-xs-12" use="accompanyDetails" index="4" style="display:none;">
									<h4>ACCOMPANY 4</h4>
									<div class="col-xs-12 form-group input-material">
										<input type="text" class="form-control" name="accompany_name_add[3]"  id="accompany_name_add_4" style="text-transform:uppercase;">
										<input type="hidden" name="accompany_selected_add[3]" value="3" />
										<label for="accompany_name_add_4">Name</label>
									</div>						
									<div class="col-xs-8 form-group ">
										<div class="checkbox">
											<label class="select-lable" >DINNER</label>
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
												<input type="checkbox" name="accompany_dinner_value[3]" id="dinner_value" 
													   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
													   operationMode="dinner"  
													   amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
													   invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 4"/>
												<span class="checkmark"></span>
											</label>
<?
		}
?>						
										</div>
									</div>
									<div class="col-xs-4 form-group ">
										<div class="checkbox">
											<div>
												  <label class="container-box" style="float:left; margin-right:20px;">Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]" id="accompany_food_4_veg" value="VEG">
													  <span class="checkmark"></span>
													</label>
													<label class="container-box" style="float:left; margin-right:20px;">Non-Veg
													  <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]" id="accompany_food_4_nonveg" value="NON_VEG">
													  <span class="checkmark"></span>
													</label>
													&nbsp;
												</div>
											<label class="select-lable" >Food Preference</label>
										</div>
									</div>	
								</div>                        
								<div class=" col-xs-2 text-center pull-right">
									<button type="button" class="submit" use='nextButton'>Next</button>
								</div>                
        						<div class="clearfix"></div>
                            </li>
                        </ul>
                    </li>
					<li use="registrationPaymentOption" style="display:none;" class="rightPanel_payment">
                        <div class="link" use="rightAccordianL1TriggerDiv">PAYMENT OPTION</div>
                        <ul class="submenu" style="display: none">
                            <li>
								<div class="col-xs-12 form-group ">
									<div class="radio">
										<label class="select-lable" >Payment Options</label>
										<label class="container-box">CREDIT / DEBIT CARD / ONLINE PAYMENT
											<input type="radio" name="registrationMode" value="ONLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CC" required>
											<span class="checkmark"></span>
										</label>
										<div class="rightPanel_payment_CC" style="display:none; padding-left:20px;" for="CC" use='payRules'>
											Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
											MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
											RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
											Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
											MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
											Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png">
											American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"><br><br>
										</div>
										
										<label class="container-box">CHEQUE
											<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CHQ">
											<span class="checkmark"></span>
										</label>
										<div class="rightPanel_payment_CHQ" style="display:none; padding-left:39px;" for="CHQ" use='payRules'>
											Cheque or DD should be made in favor of "<b>RCOG EASTERN ZONE REPRESENTATIVE COMMITTEE - AICC RCOG 2019</b>", Payable at Kolkata<br> 
											The same should be posted to the address, mentioned below - <br> 
											AICC RCOG 2019 Registration Secretariat <br> 
											c/o RUEDA <br> 
											DL 220, Sector II, Salt Lake, Kolkata 700091<br><br>
										</div>
										
										<label class="container-box">DEMAND DRAFT
											<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="DD">
											<span class="checkmark"></span>
										</label>
										<div class="rightPanel_payment_DD" style="display:none; padding-left:39px;" for="DD" use='payRules'>
											Demand Draft should be made in favor of "<b>RCOG EASTERN ZONE REPRESENTATIVE COMMITTEE - AICC RCOG 2019</b>", Payable at Kolkata<br>
											The same should be posted to the address, mentioned below - <br> 
											AICC RCOG 2019 Registration Secretariat <br> 
											c/o RUEDA <br> 
											DL 220, Sector II, Salt Lake, Kolkata 700091 <br><br>															
										</div>														
														
										<label class="container-box">NEFT / RTGS / BANK TRANSFER / NET BANKING
											<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="WIRE">
											<span class="checkmark"></span>
										</label>
										<div class="rightPanel_payment_NEFT" style="display:none; padding-left:39px;" for="WIRE" use='payRules'>															
											NEFT or RTGS will be accepted for payment.<br>
											<u>Bank Details</u> <br>
											Bank Name: AXIS Bank Ltd.<br>
											Branch Address: IB 201, Sector III, Salt Lake City, Kolkata 700106, West Bengal.<br>
											Account No.: 919020002426962<br> 
											IFSC: UTIB0000775<br> 
											MICR: 700211046<br><br> 
										</div>
														
										<label class="container-box">CASH PAYMENT
											<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CASH">
											<span class="checkmark"></span>
										</label>
										<div class="rightPanel_payment_CASH" style="display:none; padding-left:39px;" for="CASH" use='payRules'>															
											Payment can be sent by money order to the AICC RCOG 2019 Registration Secretariat. 
											Direct deposition will not be accepted.<br><br>
										</div>	
									</div>
								</div>  
								<div class=" col-xs-2 text-center pull-right">
									<button type="button" class="submit" use='nextButton'>Next</button>
								</div>          
        						<div class="clearfix"></div>
                            </li>
                        </ul>
                    </li>
					
					<li use="registrationPayment" style="display:none;" class="rightPanel_payment">
                        <div class="link" use="rightAccordianL1TriggerDiv">PAYMENT</div>
                        <ul class="submenu" style="display: none">
                            <li>
								<table class="table bill" use="totalAmountTable">
									<thead>
										<tr>
											<th style="padding: 16px;background: #80d4f8; color: white; font-weight: normal; font-size: 18px; border-right: 0; border-radius: 2px 0 0 0px ">DETAIL</th>
											<th style="padding: 16px; border:0;  background: #80d4f8; color: white; font-weight: normal; font-size: 18px; border-left: 0; border-radius: 0px 2px 0 0px; text-align:right;" align="right">AMOUNT (INR)</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="padding: 16px; font-weight: normal; font-size: 18px; border-bottom: 2px dotted #80d4f8;">&bull; &nbsp Gala Dinner</td>
											<td style="padding: 16px; font-weight: normal; font-size: 18px; border-bottom: 2px dotted #80d4f8;">INR 4000</td>
										</tr>
										<tr>
											<td style="padding: 16px; font-weight: normal; font-size: 18px; border-bottom: 0;">&bull; &nbsp Gala Dinner</td>
											<td style="padding: 16px; font-weight: normal; font-size: 18px; border-bottom: 0;">INR 4000</td>
										</tr>
									</tbody>
									<tfoot>
										<tr style="display:none;" use='rowCloneable'>
											<td style="padding: 16px; font-weight: normal; font-size: 18px; border-bottom: 0;">&bull; &nbsp <span use="invTitle">Gala Dinner</span></td>
											<td style="padding: 16px; font-weight: normal; font-size: 18px; border-bottom: 0;" align="right"><span use="amount">0.00</span></td>
										</tr>
										<tr>
											<td style="padding: 16px; background: #80d4f8; color: white; font-weight: normal; font-size: 18px;border-top: 0; border-bottom: 0; border-radius: 0 0 0 2px">Total</td>
											<td style="padding: 16px; background: #80d4f8; color: white; font-weight: normal; font-size: 18px;border-top: 0; border-bottom: 0; border-radius: 0 0 2px 0px" align="right"><span use='totalAmount'>0.00</span></td>
										</tr>
									</tfoot>
								</table>   
								
								<div class="col-xs-12 form-group" use="offlinePaymentOptionChoice" style="display:none;">
									<div class="checkbox">
										<label class="select-lable" >Payment Option</label>
										<div>
											<label class="container-box" style="float:left; margin-right:30px;">Cash
											  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash">
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:30px;">Cheque
											  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" >
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:30px;">Draft
											  <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft" >
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:30px;">NEFT
											  <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT" >
											  <span class="checkmark"></span>
											</label>
											<label class="container-box" style="float:left; margin-right:30px;">RTGS
											  <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS" >
											  <span class="checkmark"></span>
											</label>
											&nbsp;
										</div>																				
									</div>
								</div>
								
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash">
									<label for="user_first_name">Cash Diposit Date</label>
									<input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								</div>
								
								<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque">
									<label for="user_first_name">Cheque No</label>
									<input type="text" class="form-control" name="cheque_number" id="cheque_number">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque">
									<label for="user_first_name">Drawee Bank</label>
									<input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque">
									<label for="user_first_name">Cheque Date</label>
									<input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								</div>
								
								<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft">
									<label for="user_first_name">Draft No</label>
									<input type="text" class="form-control" name="draft_number" id="draft_number">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft">
									<label for="user_first_name">Drawee Bank</label>
									<input type="text" class="form-control" name="draft_drawn_bank" id="draft_drawn_bank">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft">
									<label for="user_first_name">Draft Date</label>
									<input type="date" class="form-control" name="draft_date" id="draft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								</div>
								
								<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT">
									<label for="user_first_name">Transaction Id</label>
									<input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT">
									<label for="user_first_name">Drawee Bank</label>
									<input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT">
									<label for="user_first_name">Date</label>
									<input type="date" class="form-control" name="neft_date" id="neft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								</div>
								
								<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS">
									<label for="user_first_name">Transaction Id</label>
									<input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS">
									<label for="user_first_name">Drawee Bank</label>
									<input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name">
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS">
									<label for="user_first_name">Date</label>
									<input type="date" class="form-control" name="rtgs_date" id="rtgs_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
								</div>
								
								<div class="col-xs-12 form-group ">
									<div class="checkbox">
										<label class="container-box"> 
											By Clicking, you hereby agree to receive all promotional SMS and e-mails related to AICC RCOG 2019. To unsubscribe, send us a mail at secretariat@aiccrcog2019.com.
											<input type="checkbox" name="acceptance1" value="acceptance1" required>
											<span class="checkmark"></span>
										</label>
										
										<label class="container-box">
											I have read and clearly understood the 
											<a href="<?=_BASE_URL_?>terms.php" title="Click to View 'Terms &amp; Conditions'" target="_blank" class="anclink">Terms &amp; Conditions</a> 
											and 
											<a href="<?=_BASE_URL_?>cancellation.php" title="Click to View 'Cancellation &amp; Refund Policy'" target="_blank" class="anclink">Cancellation &amp; Refund Policy</a> 
											and agree with the same. 
											<input type="checkbox" name="acceptance2" value="acceptance2" required>
											<span class="checkmark"></span>
										</label>										
									</div>
								</div>  
								<div class=" col-xs-12 text-center pull-right">
									<button type="submit" class="submit" use='nextButton'>Proceed to Payment</button>
								</div>      
        						<div class="clearfix"></div>
                            </li>
                        </ul>
                    </li>
                </ul> 
				<div use="totalAmount" class="col-xs-12 totalAmount pull-right" style="padding: 0;  display:none;">
					<div class=" col-xs-2 text-center pull-right" style="display:none;">
						INR <span use='totalAmount'></span>
					</div> 
                </div>           
            </div>
       	</div>
	   	</form>
		
	  	<div id="loginModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="frmLoginUniqueSequence" id="frmLoginUniqueSequence" action="<?=_BASE_URL_?>login.process.php" method="post">
				<input type="hidden" name="action" value="uniqueSeqVerification" />
				<div class="modal-content">
					<div class="modal-header">
						<!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
						<div class="log"><h3>YOU ARE REGISTERED</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>LOGIN with the unique sequence sent to you.</span></h2></div>
					
					<div class="col-xs-12 profileright-section">							
						<div class="login-user" style="margin-top: 25px;"><h4><input type="email" name="user_email_id" id="user_email_id" value="" style="text-transform:lowercase; border:0px;" readonly="" /></h4></div>		
						<div class="login-user" style="margin-top: 5px;"><h4><input type="text" name="user_otp" id="user_otp" value="#" required /></h4></div>			   
						<div class="bttn" style="margin-top: 25px;"><button type="submit" >Login</button>&nbsp;<button type="button" style="background:#7f8080;" use='cancel'>Cancel</button></div>
					</div>				
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
		<div id="unpaidModalOnline" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post" action="<?=$cfg['BASE_URL']?>login.process.php">
				<input type="hidden" name="action" value="loginRegToken" />
				<div class="modal-content">
					<div class="modal-header">
						<!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
						<div class="log"><h3>PAYMENT PENDING</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Your e-mail id is already registered with us but the payment procedure remained incomplete.To complete, please pay the registration fees.</span></h2></div>
					
					<div class="col-xs-12 profileright-section">							
						<div class="login-user" style="margin-top: 25px;"><h4><input type="email" name="user_email_id" id="user_email_id" value=""  style="text-transform:lowercase; border:0px;" readonly="" /></h4></div>		
						<div class="bttn" style="margin-top: 25px;"><button type="submit" >Proceed to pay</button>&nbsp;<button type="button" style="background:#7f8080;" use='cancel'>Cancel</button></div>
					</div>				
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
		<div id="unpaidModalOffline" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post" action="<?=$cfg['BASE_URL']?>login.process.php">
				<input type="hidden" name="action" value="loginRegToken" />
				<div class="modal-content">
					<div class="modal-header">
						<!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
						<div class="log"><h3>PAYMENT IN PROCESS</h3></div>
					</div>					
					<div class="modal_subHead"><h2><span>Your e-mail id is already registered with us but the payment procedure is ongoing. Pleasr contact the registration secretariat for further details.</span></h2></div>
					<div class="col-xs-12 profileright-section">
						<div class="bttn" style="margin-top: 25px;"><button type="button" style="background:#7f8080;" use='cancel'>Close</button></div>
					</div>				
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
		<div id="payNotSetModalOffline" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post" action="<?=$cfg['BASE_URL']?>login.process.php">
				<input type="hidden" name="action" value="loginRegToken" />
				<div class="modal-content">
					<div class="modal-header">
						<!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
						<div class="log"><h3>PAYMENT PENDING</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Your e-mail id is already registered with us but the payment procedure remained incomplete.To complete, please pay the registration fees.</span></h2></div>
					
					<div class="col-xs-12 profileright-section">							
						<div class="login-user" style="margin-top: 25px;"><h4><input type="email" name="user_email_id" id="user_email_id" value=""  style="text-transform:lowercase; border:0px;" readonly="" /></h4></div>		
						<div class="bttn" style="margin-top: 25px;"><button type="submit" >Proceed to pay</button>&nbsp;<button type="button" style="background:#7f8080;" use='cancel'>Cancel</button></div>
					</div>				
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
		<script>
		$(document).ready(function(){
			$('#loginModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: false
			});
			$('#loginModal').find("button[use=cancel]").click(function(){
				disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
				$('#loginModal').modal('hide');
			});
			
			$('#unpaidModalOnline').modal({
				backdrop: 'static',
				keyboard: false,
				show	: false
			});
			$('#unpaidModalOnline').find("button[use=cancel]").click(function(){
				disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
				$('#unpaidModalOnline').modal('hide');
			});
			
			$('#unpaidModalOffline').modal({
				backdrop: 'static',
				keyboard: false,
				show	: false
			});
			$('#unpaidModalOffline').find("button[use=cancel]").click(function(){
				disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
				$('#unpaidModalOffline').modal('hide');
			});
			
			$('#payNotSetModalOffline').modal({
				backdrop: 'static',
				keyboard: false,
				show	: false
			});
			$('#payNotSetModalOffline').find("button[use=cancel]").click(function(){
				disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
				$('#payNotSetModalOffline').modal('hide');
			});
		});
		</script>
	</body>
	
</html>
<?php
	
?>