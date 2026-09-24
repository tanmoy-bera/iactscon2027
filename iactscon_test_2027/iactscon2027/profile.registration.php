<?php
include_once("includes/frontend.init.php");
include_once("includes/function.delegate.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");

//$mycms->redirect("login.php");

//$mycms->removeAllSession();
//$mycms->removeSession('SLIP_ID');

if(isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId'])!='')
{
	$abstractDelegateId	= trim($_REQUEST['abstractDelegateId']);
	$userRec = getUserDetails($abstractDelegateId);
}


$sql 	=	array();
					$sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
											WHERE `status`='A' order by id desc limit 1";
					 //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$result = $mycms->sql_select($sql);
$row    		 = $result[0];

$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['logo_image'];
if($row['logo_image']!='')
{
	$emailHeader  = $header_image;
}

$loginDetails    = login_session_control();
$delegateId      = $loginDetails['DELEGATE_ID'];
$rowUserDetails  = getUserDetails($delegateId);
//echo '<pre>'; print_r($rowUserDetails['user_state_id']);

$state_id = $rowUserDetails['user_state_id'];

$sqlState    =   array();
$sqlState['QUERY'] = "SELECT state_name FROM "._DB_COMN_STATE_." WHERE `status`='A' AND st_id='".$state_id."' ";
                                  
$resultState = $mycms->sql_select($sqlState);
$rowState             = $resultState[0];
$state_name = $rowState['state_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="images/favicon.png" type="favicon">
    <title>:: Registration | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>

    <?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
    <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>registration.tariff.css" />
    <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" /> 
    <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
    <script type="text/javascript" language="javascript" src="<?=_DIR_CM_JSCRIPT_."website/"?>login.js?x=<?php echo rand(0,100)?>"></script> 
    <script src="<?=_BASE_URL_?>js/website/returnData.process.js"></script>

    <style>
        .profile-left-section { width: 50%; position: fixed; z-index: 999; background-color: #fff; left: 0; right: 0; bottom: 0; top: 0; overflow: auto; transform: translateX(-100%); transition: transform 0.3s ease-in-out; } 
        .mobile-menu-open { top: 10px; right: 10px; color: #ffffff; }
        .profile-left-section.toggole-menu { transform: translateX(0%); box-shadow: 0 0 10px #00000038;}
        .menucross .fa-bars:before { content: "\f00d";}
    </style>
</head>

<body>
    <?
		$cutoffs 			= fullCutoffArray();	
		$currentCutoffId 	= getTariffCutoffId();
?>
    <form name="registrationForm" action="<?=_BASE_URL_?>registration.process.php">
        <input type="hidden" name="act" value="combinedRegistrationProcess" />
        <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>" />
        <input type="hidden" name="reg_area" value="FRONT" />
        <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
        <input type="hidden" name="registration_cutoff" id="registration_cutoff" value="<?=$currentCutoffId?>" />
        <input type="hidden" name="abstractDelegateId" id="abstractDelegateId" value="<?=$abstractDelegateId?>" />
            
            <?
                leftCommonMenu();
            ?>

        <div class="col-lg-4 col-md-12 left-container-box regTarrif_leftPanel" style="position:relative;">
                <button type="button" class="mobile-menu-open mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button> 
            <div class="col-xs-4 logo">
                <!--  <a href="<?=_WEBSITE_BASE_URL_?>"><img src="<?=_BASE_URL_?>images/logo_white.png" alt="logo" style="width: 100%;" /></a>   -->
                <a href="<?=_WEBSITE_BASE_URL_?>"><img src="<?php echo $emailHeader; ?>" alt="logo"
                        style="width: 100%;" /></a>         
            </div>
            <div class="col-xs-7 col-xs-offset-1 timer" style="padding: 0">
                <div>
                    <h5 class="cutoffNameTop">Register</h5>
                    <h4 style="color: white" class="cutoffName"><?=getCutoffName($currentCutoffId)?></h4>
                </div>
                <div class="timeLeftWrapper">
                    <div style="font-size: 20px;" class="timeLeft">
                        <div class="col-xs-3 timeLeftDays">
                            <p style="margin-bottom: 0; color: white; font-style: italic;"><span id="dday">000</span>
                                <sub style="bottom: 0; font-size: 12px;">DAYS</sub>
                            </p>
                        </div>
                        <div class="col-xs-3 timeLeftHours">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dhour">00</span>
                                <sub style="bottom: 0; font-size: 12px;">HRS.</sub>
                            </p>
                        </div>
                        <div class="col-xs-3 timeLeftMinutes">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dmin">00</span> <sub
                                    style="bottom: 0; font-size: 12px;">MIN.</sub></p>
                        </div>
                        <div class="col-xs-3 timeLeftSeconds">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dsec">00</span> <sub
                                    style="bottom: 0; font-size: 12px;">SEC.</sub></p>
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
                var current =
                    ""; //enter what you want the script to display when the target date and time are reached, limit to 20 characters
                var year = <?=$dateArr[0]?>; //Enter the count down target date YEAR
                var month = <?=$dateArr[1]?>; //Enter the count down target date MONTH
                var day = <?=$dateArr[2]?>; //>Enter the count down target date DAY
                var hour = 23; //Enter the count down target date HOUR (24 hour clock)
                var minute = 59; //Enter the count down target date MINUTE
                var tz =
                    5.5; //Offset for your timezone in hours from UTC (see http://wwp.greenwichmeantime.com/index.htm to find the timezone offset for your location)

                // DO NOT CHANGE THE CODE BELOW! 
                var montharray = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
                    "Dec");

                function countdown(yr, m, d, hr, min) {
                    theyear = yr;
                    themonth = m;
                    theday = d;
                    thehour = hr;
                    theminute = min;
                    var today = new Date();
                    var todayy = today.getYear();
                    if (todayy < 1000) {
                        todayy += 1900;
                    }
                    var todaym = today.getMonth();
                    var todayd = today.getDate();
                    var todayh = today.getHours();
                    var todaymin = today.getMinutes();
                    var todaysec = today.getSeconds();
                    var todaystring1 = montharray[todaym] + " " + todayd + ", " + todayy + " " + todayh + ":" +
                        todaymin + ":" + todaysec;
                    var todaystring = Date.parse(todaystring1) + (tz * 1000 * 60 * 60);
                    var futurestring1 = (montharray[m - 1] + " " + d + ", " + yr + " " + hr + ":" + min);
                    var futurestring = Date.parse(futurestring1) - (today.getTimezoneOffset() * (1000 * 60));
                    var dd = futurestring - todaystring;
                    var dday = Math.floor(dd / (60 * 60 * 1000 * 24) * 1);
                    var dhour = Math.floor((dd % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
                    var dmin = Math.floor(((dd % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
                    var dsec = Math.floor((((dd % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
                    if (dday <= 0 && dhour <= 0 && dmin <= 0 && dsec <= 0) {
                        document.getElementById('dday').style.display = "none";
                        document.getElementById('dhour').style.display = "none";
                        document.getElementById('dmin').style.display = "none";
                        document.getElementById('dsec').style.display = "none";
                        return;
                    } else {
                        document.getElementById('dday').innerHTML = (dday < 10) ? ('0' + dday) : dday;
                        document.getElementById('dhour').innerHTML = (dhour < 10) ? ('0' + dhour) : dhour;
                        document.getElementById('dmin').innerHTML = (dmin < 10) ? ('0' + dmin) : dmin;
                        document.getElementById('dsec').innerHTML = (dsec < 10) ? ('0' + dsec) : dsec;
                        setTimeout("countdown(theyear,themonth,theday,thehour,theminute)", 1000);
                    }
                }
                countdown(year, month, day, hour, minute);
                </script>
            </div>

            <div class="col-xs-12 menu" style="padding: 0">
                <?
		if($currentCutoffId > 0)
		{
			$conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
			$workshopDetailsArray 	 = getAllWorkshopTariffs($currentCutoffId);
			$workshopCountArr 		 = totalWorkshopCountReport();	 
			
			$workshopRegChoices	= array();
			
			//echo '<pre>'; print_r($conferenceTariffArray);
			foreach($workshopDetailsArray as $keyWorkshopclsf=>$rowWorkshopclsf)
			{	
				foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
				{
					$workshopRegChoices[$rowRegClasf['WORKSHOP_TYPE']][$keyWorkshopclsf][$keyRegClasf] = $rowRegClasf;
				}
			}
				
			
?>
                <ul use="leftAccordion" class="accordion">
                    <li use='leftAccordionConference'>
                        <div class="link_alter conferenceRegistration" use="accordianL1TriggerDiv" useSpec1="stage1">
                            <i><i class="fas fa-ticket-alt"></i>&nbsp;&nbsp;CONFERENCE REGISTRATION</i>
                            <i class="fas dropdown-icon"></i>
                        </div>
                        <ul class="submenu" style="display:block;padding-left:25px;">
                            <?
			foreach($conferenceTariffArray as $key=>$registrationDetailsVal)
			{
				$classificationType = getRegClsfType($key);
				if($classificationType =='DELEGATE')
				{
?>
                            <li style="border-bottom:0px;">
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
                                        <input type="checkbox" name="registration_classification_id[]"
                                            id="registration_classification_id" operationMode="registration_tariff"
                                            operationModeType="conference"
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

			// accommodation related work by weavers start
			 // accommodation part is hide for now 22.07.2022 by weavers
			$hotel_array = array();
			$sqlHotel				= array();
			$sqlHotel['QUERY']	 	= "SELECT tracm.id as ACCOMMODATION_TARIFF_ID,
										tracm.hotel_id as HOTEL_ID,
										tracm.package_id as ACCOMMODATION_PACKAGE_ID,
										tracm.tariff_cutoff_id as CUTOFF_ID,
										tracm.checkin_date_id as CHECKIN_DATE_ID,
										tracm.checkout_date_id as CHECKOUT_DATE_ID,
										tracm.currency as CURRENCY,
										tracm.inr_amount as AMOUNT,
										tracm.usd_amount as USD_AMOUNT,
										tracm.status as STATUS,
										hotel_master.hotel_name as HOTEL_NAME,
										chkindate.check_in_date as CHECKIN_DATE,
										chkoutdate.check_out_date as CHECKOUT_DATE,
										DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS
										FROM "._DB_MASTER_HOTEL_." as hotel_master
										INNER JOIN "._DB_TARIFF_ACCOMMODATION_." as tracm 
										on tracm.hotel_id = hotel_master.id AND tracm.status = 'A'
										LEFT JOIN "._DB_ACCOMMODATION_CHECKIN_DATE_." as chkindate
										on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
										LEFT JOIN "._DB_ACCOMMODATION_CHECKOUT_DATE_." as chkoutdate
										on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
										WHERE hotel_master.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ?
										GROUP BY DAYS,tracm.hotel_id
										HAVING (DAYS) < 5
										 ORDER BY tracm.hotel_id ASC, DAYS ASC";  // HAVING (DAYS) < 4  // remove on 21.09.2022 (user can select hotels more then 3 days)

			$sqlHotel['PARAM'][]    = array('FILD' => 'hotel_master.status', 'DATA' => 'A',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
			$resHotel		    	= $mycms->sql_select($sqlHotel);			
			
			
			foreach ($resHotel as $key => $value) {
				$nights			= '';
				$temp_array 	= array();
				$temp_array['HOTEL_ID'] = $value['HOTEL_ID'];
				$temp_array['ACCOMMODATION_PACKAGE_ID'] = $value['ACCOMMODATION_PACKAGE_ID'];

				/*  commented on 21.09.2022 (user can select hotels more then 3 days)
				switch($value['DAYS'])
				{
					case '1':
						$nights = '1 Night Stay';
						break;
					case '2':
						$nights = '2 Nights Stay';
						break;
					case '3':
						$nights = '3 Nights Stay';
						break;
				}
				*/

				$nights = $value['DAYS'].' Night Stay';
				
				$sharing				= 'Individual';
				$residentialPackDataOrganizer[$nights][$sharing][] = $value;
				$hotel_array[$value['HOTEL_ID']] = $temp_array;
				 	
			}

			// accommodation related work by weavers end
			
			 // accommodation part is hide for now 22.07.2022 by weavers
			if(sizeof($residentialPackDataOrganizer) > 0)
			{ 
?>
                    <li use='leftAccordionAccommodation'>
                        <div class="link residentialRegistration" use="accordianL1TriggerDiv" useSpec1="stage1">
                            <i><i class="fas fa-hotel"></i>&nbsp;&nbsp;ACCOMMODATION</i>
                            <i class="fas fa-angle-down dropdown-icon"></i>
                        </div>
                        <ul class="submenu" style="display:none;padding-left:25px;">

                            <input type="hidden" name="hotel_id">
                            <input type="hidden" name="accomPackId" value="" />
                            <input type="hidden" name="accomDetails" value="" />
                            <?
				foreach($residentialPackDataOrganizer as $StayName=>$nightDetailsVal)
				{
					$staykeys = array_keys($nightDetailsVal);
				
?>
                            <li style="border-bottom:0px;">
                                <div class="submenuHead" use="accordianL2TriggerDiv">
                                    <i>
                                        &nbsp;&nbsp;<i class="fas fa-cloud-moon"></i>
                                        &nbsp;<?=$StayName?>
                                    </i>
                                    <i class="fas fa-angle-down dropdown-icon"></i>
                                </div>
                                <ul class="submenu1" style="display: none;padding-left:25px;">
                                    <li style="border-bottom:0px; margin: 10p 0px;">
                                        <!-- commented Individaul label drop-down by weavers start -->
                                        <? /* <div class="submenuHead2" use="accordianL3TriggerDivParallel">
<?
					foreach($staykeys as $k=>$ShareState)
					{
						if($ShareState=='Sharing')
						{
							$fas = '<i class="fas fa-user-friends"></i>';
						}
						else
						{
							$fas = '<i class="fas fa-user"></i>';
						}
?>
                                        <div class="paralellHead pull-left" style="width:49%; margin-left:3px;"
                                            use='parallelShareStateTab' shareState='<?=$ShareState?>'>
                                            <i>
                                                <?=$fas?>
                                                &nbsp;<?=$ShareState?>
                                            </i>
                                            <i class="fas fa-angle-down dropdown-icon"></i>
                                        </div>
                                        <?
					}
?>
                                        <br />
            </div> */ ?>
            <!-- commented Individaul label drop-down by weavers end -->
            <?
					foreach($nightDetailsVal as $ShareState=>$packDetailsVal)
					{
?>
            <ul class="submenu2" style="padding-left:25px;" use='parallelShareStateBody' shareState='<?=$ShareState?>'>
                <?
						$residentialDisplayName = '';
						foreach($packDetailsVal as $kkl=>$registrationDetailsVal)
						{
							$residentialDisplayName = $registrationDetailsVal['HOTEL_NAME'];
?>
                <li style="border-bottom:0px;">
                    <a>
                        <label class="container-box menu-container-box">
                            <i class="itemTitle"><?=$residentialDisplayName?></i>
                            <i class="itemPrice pull-right"> <?=$registrationDetailsVal['CURRENCY']?>
                                <?=number_format(($registrationDetailsVal['AMOUNT']))?></i>
                            <input type="checkbox" name="accomodation_classification_id[]"
                                id="accomodation_classification__<?=$registrationDetailsVal['ACCOMMODATION_TARIFF_ID'].'_'.$registrationDetailsVal['ACCOMMODATION_PACKAGE_ID'].'_'.$registrationDetailsVal['HOTEL_ID']?>"
                                operationMode="accomodation_tariff"
                                value="<?=$registrationDetailsVal['ACCOMMODATION_TARIFF_ID']?>"
                                operationModeType="residential" accommodationType="<?="INDIVIDUAL"?>"
                                currency="<?=$registrationDetailsVal['CURRENCY']?>"
                                amount="<?=$registrationDetailsVal['AMOUNT']?>"
                                invoiceTitle="Residential Package - <?=$StayName?>-<?=$ShareState?>@<?=$registrationDetailsVal['HOTEL_NAME']?>"
                                offer="<?=$registrationDetailsVal['ISOFFER']?>"
                                accommodationPackageId="<?=$registrationDetailsVal['ACCOMMODATION_PACKAGE_ID']?>"
                                hotel_id="<?=$registrationDetailsVal['HOTEL_ID']?>"
                                cutoff_id="<?=$registrationDetailsVal['CUTOFF_ID']?>"
                                hotelDays="<?=$registrationDetailsVal['DAYS']?>">
                            <span class="checkmark menu-checkmark"></span>
                        </label>
                    </a>
                </li>
                <?
						}
?>
            </ul>

            <?
					}
?>
            </li>
            </ul>
            </li>
            <?
				}
?>
            </ul>
            </li>
            <?
			}
			

			
			if(isset($workshopRegChoices['MASTER CLASS']) && sizeof($workshopRegChoices['MASTER CLASS']) > 0)
			{
?>
            <li use='leftAccordionMasterClass'>
                <div class="link masterClass" use="accordianL1TriggerDiv" useSpec1="stage2">
                    <i>WORKSHOP(01-12-2022)</i>
                    <i class="fas fa-angle-down dropdown-icon"></i>
                </div>
                <ul class="submenu" style="display:none; padding-left:25px;">
                    <?				
				$loopcount = 0;
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
						
						if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 /*&& $rowRegClasf['WORKSHOP_ID']!=5*/)
						{
							$workshopRateDisplay = "Included in Registration";
						}
						/*else if( $rowRegClasf['WORKSHOP_ID'] == 5)
						{
							$workshopRateDisplay = "";
						}*/
						$displayName = '';
						$len 		 = strlen($rowRegClasf['WORKSHOP_NAME']);
						if($len > 40)
						{
							$charCount = 0;
							$lines = array();
							$words = explode(' ',$rowRegClasf['WORKSHOP_NAME']);
							foreach($words as $kk=>$word)
							{
								$charCount += strlen($word);
								if($charCount > 40)
								{
									$charCount = strlen($word);
									$lines[] = '<br/>';
								}
								$lines[] = $word;
							}
							$displayName = implode(' ',$lines);
						}
						else
						{
							$displayName = $rowRegClasf['WORKSHOP_NAME'];
						}
						
?>
                    <li use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
                        <a>
                            <label class="container-box menu-container-box">
                                <i class="itemTitle"><?=$displayName?></i>
                                <i class="itemPrice pull-right"><?=$workshopRateDisplay?></i>
                                <input type="checkbox" name="workshop_id[]"
                                    id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"
                                    value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>
                                    workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>" operationMode="workshopId"
                                    amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
                                    invoiceTitle="Workshop - <?=$rowRegClasf['WORKSHOP_NAME']?>"
                                    registrationClassfId="<?=$keyRegClasf?>" />
                                <span class="checkmark" <?=$spanCss?>></span>
                            </label>
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
			
			if(isset($workshopRegChoices['WORKSHOP']) && sizeof($workshopRegChoices['WORKSHOP']) > 0)
			{
?>
            <li use='leftAccordionWorkshop'>
                <div class="link workshop" use="accordianL1TriggerDiv" useSpec1="stage2">
                    <i>WORKSHOP(30-11-2022)</i>
                    <i class="fas fa-angle-down dropdown-icon"></i>
                </div>
                <ul class="submenu" style="display:none; padding-left:25px;">
                    <?
				foreach($workshopRegChoices['WORKSHOP'] as $keyWorkshopclsf=>$rowWorkshopclsf )
				{
					foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
					{
						$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
						
						$style 	= "";
						$span	= "";
						$spanCss	= "";
						if($workshopCount<1)
						{
							 $style = 'disabled="disabled"';
							 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
							 $spanCss = 'style="cursor:not-allowed;"';
						}
						
						$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
						
						if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 /*&& $rowRegClasf['WORKSHOP_ID']!=5*/)
						{
							$workshopRateDisplay = "Included in Registration";
						}
						/*else if( $rowRegClasf['WORKSHOP_ID'] == 5)
						{
							$workshopRateDisplay = "";
						}*/
?>
                    <li use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
                        <a>
                            <label class="container-box menu-container-box">
                                <i class="itemTitle"><?=$rowRegClasf['WORKSHOP_NAME']?></i>
                                <i class="itemPrice pull-right"><?=$workshopRateDisplay?></i>
                                <input type="checkbox" name="workshop_id[]"
                                    id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"
                                    value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>
                                    workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>" operationMode="workshopId_nov"
                                    amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
                                    invoiceTitle="Workshop - <?=$rowRegClasf['WORKSHOP_NAME']?>"
                                    registrationClassfId="<?=$keyRegClasf?>" />
                                <span class="checkmark" <?=$spanCss?>></span>
                            </label>
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
			
			$allowOnlyTotPlus = false;
			if(isset($workshopRegChoices['POST-CONFERENCE']) && sizeof($workshopRegChoices['POST-CONFERENCE']) > 0)
			{
?>
            <li use='leftAccordionPostConference'>
                <div class="link postConfWorkshop" use="accordianL1TriggerDiv" useSpec1="stage2">
                    <i>POST-CONGRESS WORKSHOP</i>
                    <i class="fas fa-angle-down dropdown-icon"></i>
                </div>
                <ul class="submenu" style="display:none; padding-left:25px;">
                    <?
				foreach($workshopRegChoices['POST-CONFERENCE'] as $keyWorkshopclsf=>$rowWorkshopclsf )
				{
					foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
					{
						$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
						
						$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
						
						if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 && $rowRegClasf['WORKSHOP_ID']!=5)
						{
							$workshopRateDisplay = "Included in Registration";
						}
						else if( $rowRegClasf['WORKSHOP_ID'] == 5)
						{
							$workshopRateDisplay = "";
						}
						
						$displayName = '';
						$len 		 = strlen($rowRegClasf['WORKSHOP_NAME']);
						if($len > 40)
						{
							$charCount = 0;
							$lines = array();
							$words = explode(' ',$rowRegClasf['WORKSHOP_NAME']);
							foreach($words as $kk=>$word)
							{
								$charCount += strlen($word);
								if($charCount > 40)
								{
									$charCount = strlen($word);
									$lines[] = '<br/>';
								}
								$lines[] = $word;
							}
							$displayName = implode(' ',$lines);
						}
						else
						{
							$displayName = $rowRegClasf['WORKSHOP_NAME'];
						}	
						
						$style 	= "";
						$span	= "";
						$spanCss	= "";
						if($workshopCount<1)
						{
							 $style = 'disabled="disabled"';
							 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
							 $spanCss = 'style="cursor:not-allowed;"';
						}
						elseif(in_array($rowRegClasf['WORKSHOP_ID'],$cfg['INDEPENDANT.WORKSHOPS']))
						{
							$allowOnlyTotPlus 							= true;
							$totPlusData['id'] 							= $rowRegClasf['WORKSHOP_ID'];
							$totPlusData['displayName'] 				= $displayName;
							$totPlusData['workshopName'] 				= $rowRegClasf['WORKSHOP_GRP'];
							$totPlusData['amount'] 						= $rowRegClasf[$rowRegClasf['CURRENCY']];
							$totPlusData['invoiceTitle'] 				= 'Post Congress Workshop - '.$rowRegClasf['WORKSHOP_NAME'];
							$totPlusData['registrationClassfId'] 		= '';
						}
?>
                    <li use="<?=$keyRegClasf?>" operetionMode="workshopTariffTr" style="display:none;">
                        <a>
                            <label class="container-box menu-container-box">
                                <i class="itemTitle" style="width:70%;"><?=$displayName?></i>
                                <i class="itemPrice pull-right"><?=$workshopRateDisplay?></i>
                                <input type="checkbox" name="workshop_id[]"
                                    id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"
                                    value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>
                                    workshopName="<?=$rowRegClasf['WORKSHOP_GRP']?>"
                                    operationMode="workshopId_postconference"
                                    amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>"
                                    invoiceTitle="Post Congress Workshop - <?=$rowRegClasf['WORKSHOP_NAME']?>"
                                    registrationClassfId="<?=$keyRegClasf?>" />
                                <span class="checkmark" <?=$spanCss?>></span>
                            </label>
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
            <? /* diner section is closed 14.07.2022 

				  <li use='leftAccordionDinner' style="display:none;">
				  	<div class="link dinner" use="accordianL1TriggerDiv" useSpec1="stage2">
						<i>DINNER</i>
						<i class="fas fa-angle-down dropdown-icon"></i>
					</div>
					<ul class="submenu" style="padding-left:25px;">
				  					
<?
			$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);

			foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
			{
?>
            <li>
                <a>
                    <label class="container-box menu-container-box">
                        <i class="itemTitle" style="width:70%;"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
                        <i class="itemPrice pull-right">
                            <?
				if(floatval($registrationDetailsVal['AMOUNT'])>0)
				{
					echo $registrationDetailsVal['CURRENCY'].' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
				}
									
?>
                        </i>
                        <input type="checkbox" name="dinner_value[]" id="dinner_value"
                            value="<?=$dinnerValue[$currentCutoffId]['ID']?>" operationMode="dinner"
                            amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
                            invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?>" />
                        <span class="checkmark"></span>
                    </label>
                </a>
            </li>
            <?
			}
?>

            </ul>
            </li>
            */?>
            <?			
			
			if($allowOnlyTotPlus)
			{
?>
            <li use='leftAccordionOnlyTotPlus'>
                <div class="link dinner" use="accordianL1TriggerDiv" useSpec1="stage2">
                    <i>POST-CONGRESS REGISTRATION</i>
                    <i class="fas fa-angle-down dropdown-icon"></i>
                </div>
                <ul class="submenu" style="display:block; padding-left:25px;">
                    <li>
                        <a>
                            <label class="container-box menu-container-box">
                                <i class="itemTitle" style="width:70%;"><?=$totPlusData['displayName']?></i>
                                <i class="itemPrice pull-right"><?=$totPlusData['amount']?></i>
                                <input type="checkbox" name="workshop_id[]" id="workshop_id_only_tot"
                                    value="<?=$totPlusData['id']?>" workshopName="<?=$totPlusData['workshopName']?>"
                                    operationMode="workshopId_postconference" amount="<?=$totPlusData['amount']?>"
                                    invoiceTitle="<?=$totPlusData['invoiceTitle']?>"
                                    registrationClassfId="<?=$totPlusData['registrationClassfId']?>" />
                                <span class="checkmark"></span>
                            </label>
                        </a>
                    </li>
                </ul>
            </li>
            <?
			}
?>
            </ul>
            <script>
            $(document).ready(function() {
                $.each($("ul[use=leftAccordion]").children("li"), function(i, level1) {
                    $(level1).attr("parent", "0");
                    $(level1).attr("level", "1");
                    $(level1).attr("sequence", (i + 1));
                    $(level1).children("div[use=accordianL1TriggerDiv]").attr("level", "1");
                    $(level1).children("div[use=accordianL1TriggerDiv]").attr("sequence", (i + 1));
                    //console.log('level1'+level1);
                    $.each($(level1).children("ul").children("li"), function(j, level2) {
                        $(level2).attr("parent", (i + 1));
                        $(level2).attr("level", "2");
                        $(level2).attr("sequence", (j + 1));
                        $(level2).children("div[use=accordianL2TriggerDiv]").attr("level", "2");
                        $(level2).children("div[use=accordianL2TriggerDiv]").attr("sequence", (
                            j + 1));

                        $.each($(level2).children("ul").children("li"), function(k, level3) {
                            $(level3).attr("parent", (j + 1));
                            $(level3).attr("level", "3");
                            $(level3).attr("sequence", (k + 1));
                            $(level3).children("div[use=accordianL3TriggerDiv]").attr(
                                "level", "3");
                            $(level3).children("div[use=accordianL3TriggerDiv]").attr(
                                "sequence", (k + 1));
                        });
                    });
                });

                $("ul[use=leftAccordion]").find("div[use=accordianL1TriggerDiv]").click(function() {
                    var proceed = false;
                    var trigr = $(this);
                    var stage = $(trigr).attr("useSpec1");

                    if (stage == 'stage2') {
                        if ($(trigr).attr("hasAoptionSelected") == 'Y') {
                            proceed = true;
                        }
                    } else {
                        proceed = true;
                    }

                    if (proceed) {
                        var li = $(trigr).parent().closest("li");
                        var level = $(trigr).attr("level");
                        var sequence = $(trigr).attr("sequence");

                        $.each($("ul[use=leftAccordion]").find("div[use=accordianL1TriggerDiv]"),
                            function() {
                                var div = $(this);
                                var divParent = $(div).parent().closest("li");
                                var divLevel = $(div).attr("level");
                                var divSequence = $(div).attr("sequence");

                                if (divLevel == level && divSequence != sequence) {
                                    $(divParent).children("ul").slideUp();
                                    $(div).find(".dropdown-icon").removeClass("fa-angle-down")
                                        .addClass("fa-angle-down");
                                    $(div).removeClass("link_alter").addClass("link");

                                }
                            });
                        $(li).children("ul").slideDown();
                        $(trigr).find(".dropdown-icon").removeClass("fa-angle-down");
                        $(trigr).removeClass("link").addClass("link_alter");
                    }
                });

                $("ul[use=leftAccordion]").find("div[use=accordianL2TriggerDiv]").click(function() {
                    var trigr = $(this);
                    var li = $(trigr).parent().closest("li");
                    var level = $(trigr).attr("level");
                    var sequence = $(trigr).attr("sequence");

                    $.each($("ul[use=leftAccordion]").find("div[use=accordianL2TriggerDiv]"),
                        function() {
                            var div = $(this);
                            var divParent = $(div).parent().closest("li");
                            var divLevel = $(div).attr("level");
                            var divSequence = $(div).attr("sequence");

                            if (divLevel == level && divSequence != sequence) {
                                $(divParent).children("ul").slideUp();
                                $(div).find(".dropdown-icon").removeClass("fa-angle-down").addClass(
                                    "fa-angle-down");
                                $(div).removeClass("submenuHead_alter").addClass("submenuHead");
                            }
                        });
                    $(li).children("ul").slideDown();
                    $(trigr).find(".dropdown-icon").removeClass("fa-angle-down");
                    $(trigr).removeClass("submenuHead").addClass("submenuHead_alter");
                });

                $("ul[use=leftAccordion]").find("div[use=accordianL3TriggerDiv]").click(function() {
                    var trigr = $(this);
                    var li = $(trigr).parent().closest("li");
                    var level = $(trigr).attr("level");
                    var sequence = $(trigr).attr("sequence");

                    $.each($("ul[use=leftAccordion]").find("div[use=accordianL3TriggerDiv]"),
                        function() {
                            var div = $(this);
                            var divParent = $(div).parent().closest("li");
                            var divLevel = $(div).attr("level");
                            var divSequence = $(div).attr("sequence");



                            if (divLevel == level && divSequence != sequence) {
                                console.log($(divParent).children("ul").length);
                                $(divParent).children("ul").slideUp();
                                $(div).find(".dropdown-icon").removeClass("fa-angle-down").addClass(
                                    "fa-angle-down");
                            }
                        });
                    $(li).children("ul").slideDown();
                    $(trigr).find(".dropdown-icon").removeClass("fa-angle-down");
                });

                $("ul[use=leftAccordion]").find("div[use=parallelShareStateTab]").click(function() {
                    var trigr = $(this);
                    var li = $(trigr).parent().closest("li");
                    var statename = $(trigr).attr('shareState');

                    $(li).children("ul").slideUp();
                    $(li).find(".dropdown-icon").addClass("fa-angle-down");
                    $(li).find("div[use=parallelShareStateTab]").removeClass("paralellHead_alter")
                        .addClass("paralellHead");

                    $(li).children("ul[shareState='" + statename + "']").slideDown();
                    $(trigr).find(".dropdown-icon").removeClass("fa-angle-down");
                    $(trigr).removeClass("paralellHead").addClass("paralellHead_alter");
                });

                $("input[type=checkbox],input[type=radio]").click(function() {
                    calculateTotalAmount();
                });

                $("input[type=checkbox][operationMode=registration_tariff]").each(function() {
                    $(this).click(function() {
                        //alert(12);
                    	if ($(window).width() <= 800) {
							 $('html, body').animate({
						        scrollTop: $("#userDetailsDiv").offset().top
						    }, 2000);
						}
                        $("ul[use=leftAccordion]").find(
                            "div[use=accordianL1TriggerDiv][useSpec1=stage2]").attr(
                            "hasAoptionSelected", "Y");

                        var emailObjContainer = $("ul[use=rightAccordion]").find(
                            "li[use=registrationUserDetails]");
                        $(emailObjContainer).find("input[type=email][name=user_email_id]")
                            .removeAttr("disabled");

                         $(emailObjContainer).find("button[use=nextButton]").show();   

                         enableAllFileds(emailObjContainer); 
                        // $(emailObjContainer).find("button[name=checkEmail]").show();
                        $(emailObjContainer).find("input[type=email][name=user_email_id]")
                            .parent().closest('.dis_abled').removeClass("dis_abled");
                        if ($(emailObjContainer).find("input[type=email][name=user_email_id]")
                            .val() != '') {
                            $(emailObjContainer).find("button[name=checkEmail]").trigger(
                                "click");
                        }

                        $("ul[use=rightAccordion]").children("li").hide();
                        $("ul[use=rightAccordion]").children("li[use=registrationUserDetails]")
                            .show();

                        if ($("ul[use=rightAccordion]").children(
                                "li[use=registrationUserDetails]").find("ul[level=1]").attr(
                                "displayStatus") == 'N') //kept ineffective for later use
                        {
                            $("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
                            $("ul[use=rightAccordion]").find("ul[level=1]").attr(
                                "displayStatus", 'N');
                            $("ul[use=rightAccordion]").children(
                                    "li[use=registrationUserDetails]").find("ul[level=1]")
                                .slideDown();
                            $("ul[use=rightAccordion]").children(
                                "li[use=registrationUserDetails]").find("ul[level=1]").attr(
                                "displayStatus", 'Y');

                            $("div[use=Introduction]").hide();
                            $("ul[use=rightAccordion]").show();
                            $("div[use=totalAmount]").show();
                        }

                        var currChkbxStatus = $(this).attr("chkStatus");

                        $("input[type=checkbox][operationMode=registration_tariff]").prop(
                            "checked", false);
                        $("input[type=checkbox][operationMode=registration_tariff]").attr(
                            "chkStatus", "false");

                        $("li[operetionMode=workshopTariffTr]").hide();

                        $("input[type=checkbox][operationMode=workshopId]").prop("checked",
                            false);
                        $("input[type=checkbox][operationMode=workshopId_postconference]").prop(
                            "checked", false);
                        // november22 workshop related work by weavers start	
                        $("input[type=checkbox][operationMode=workshopId_nov]").prop("checked",
                            false);
                        // november22 workshop related work by weavers end
                        $("div[operetionMode=checkInCheckOutTr]").hide();
                        $("div[use=ResidentialAccommodationAccompanyOption]").hide();

                        $("li[use=leftAccordionDinner]").hide();

                        if (currChkbxStatus == "true") {
                            $(this).prop("checked", false);
                            $(this).attr("chkStatus", "false");

                            $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                                .hide();
                            $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                                .slideDown();
                        } else {
                            $(this).prop("checked", true);
                            $(this).attr("chkStatus", "true");

                            var regType = $(this).attr('operationModeType');
                            var regClsfId = $(this).val();
                            var currency = $(this).attr('currency');
                            var offer = $(this).attr('offer');

                            if (regType == 'residential') {
                                var accommodationType = $(this).attr("accommodationType");
                                var packageId = $(this).attr("accommodationPackageId");
                                var hotel_id = $(this).attr("hotel_id");
                                var accomDetails = $(this).attr("invoiceTitle");
                                $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                                    .hide();
                                $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                                    .slideDown();

                                $("input[type=hidden][name=accomPackId]").attr("value",
                                    packageId);
                                $("input[type=hidden][name=hotel_id]").attr("value", hotel_id);
                                $("input[type=hidden][name=accomDetails]").attr("value",
                                    accomDetails);


                                $("div[operetionMode=checkInCheckOutTr][use='" + packageId +
                                    "']").slideDown();

                                if (accommodationType == 'SHARED') {
                                    $("div[use=ResidentialAccommodationAccompanyOption]")
                                        .slideDown();
                                }

                                $("li[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                                    .show();
                            } else if (regType == 'conference') {
                                $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                                    .hide();
                                $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                                    .hide();

                                $("li[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                                    .show();

                                // hide diner section 14.07.2022
                                //$("li[use=leftAccordionDinner]").slideDown();
                                //$("li[use=leftAccordionDinner]").children("ul").slideDown();
                                // hide diner section 14.07.2022

                                // disable "IAP - NNF NRP FGM" ,"NNF Accredited- Advance NRP" workshop type if registration is selected rather then "Member"
                                $("li[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                                    .find('input[type="checkbox"]').each(function() {

                                        //$(this).attr("disabled","");
                                        $(this).removeAttr('disabled');
                                        //var workshop_type_id = $(this).val();

                                        var workshop_amount = $(this).attr('amount');
                                        //console.log(workshop_type_id)
                                        //if(workshop_type_id == 11 && regClsfId != 1){
                                        if (workshop_amount == 0 && regClsfId != 1) {
                                            $(this).attr("disabled", "disabled");
                                            $(this).parent().css({
                                                "cursor": "not-allowed"
                                            })
                                            //}else if(workshop_type_id == 21 && regClsfId != 1){
                                        } else if (workshop_amount == 0 && regClsfId != 1) {
                                            $(this).attr("disabled", "disabled");
                                            $(this).parent().css({
                                                "cursor": "not-allowed"
                                            })
                                        }
                                    });

                            } else {
                                $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                                    .hide();
                                $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                                    .slideDown();
                            }

                            $("li[use=leftAccordionConference]").children("ul").slideUp();
                            $("li[use=leftAccordionConference]").find('.link_alter')
                                .removeClass("link_alter").addClass("link");
                            $("li[use=leftAccordionMasterClass]").children("ul").slideDown();
                            $("li[use=leftAccordionMasterClass]").find(
                                    'div[use=accordianL1TriggerDiv]').removeClass("link")
                                .addClass("link_alter");
                            $("li[use=leftAccordionWorkshop]").children("ul").slideDown();
                            $("li[use=leftAccordionWorkshop]").find(
                                    'div[use=accordianL1TriggerDiv]').removeClass("link")
                                .addClass("link_alter");
                            $("li[use=leftAccordionPostConference]").children("ul").slideDown();
                            $("li[use=leftAccordionPostConference]").find(
                                    'div[use=accordianL1TriggerDiv]').removeClass("link")
                                .addClass("link_alter");

                            // accommodation related work by weavers start (commented for now 22.07.2022)
                            $("li[use=leftAccordionAccommodation]").children("ul").slideDown();
                            $("li[use=leftAccordionAccommodation]").find(
                                    'div[use=accordianL1TriggerDiv]').removeClass("link")
                                .addClass("link_alter");
                            // accommodation related work by weavers end 
                        }

                        calculateTotalAmount();
                    });
                });

                // accommodation related work by weavers start
                $("input[type=checkbox][operationMode=accomodation_tariff]").each(function() {
                    $(this).click(function() {

                        var currChkbxStatus = $(this).attr("chkStatus");

                        $("input[type=checkbox][operationMode=accomodation_tariff]").prop(
                            "checked", false);
                        $("input[type=checkbox][operationMode=accomodation_tariff]").attr(
                            "chkStatus", "false");

                        if (currChkbxStatus == "true") {
                            $(this).prop("checked", false);
                            $(this).attr("chkStatus", "false");
                        } else {
                            $(this).prop("checked", true);
                            $(this).attr("chkStatus", "true");
                        }

                        var regType = $(this).attr('operationModeType');
                        var regClsfId = $(this).val();
                        var currency = $(this).attr('currency');
                        var offer = $(this).attr('offer');

                        if (regType == 'residential') {
                            var accommodationType = $(this).attr("accommodationType");
                            var packageId = $(this).attr("accommodationPackageId");
                            var hotel_id = $(this).attr("hotel_id");
                            var accomDetails = $(this).attr("invoiceTitle");

                            $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                                .hide();
                            $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                                .slideDown();

                            $("input[type=hidden][name=accomPackId]").attr("value", packageId);
                            $("input[type=hidden][name=hotel_id]").attr("value", hotel_id);
                            $("input[type=hidden][name=accomDetails]").attr("value",
                                accomDetails);

                            $("div[operetionMode=checkInCheckOutTr][use='" + hotel_id + "']")
                                .slideDown();

                            if (accommodationType == 'SHARED') {
                                $("div[use=ResidentialAccommodationAccompanyOption]")
                                    .slideDown();
                            }

                            $("li[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                                .show();
                        }

                        calculateTotalAmount();

                        $("li[use=leftAccordionMasterClass]").children("ul").slideUp();
                        $("li[use=leftAccordionMasterClass]").find(
                                'div[use=accordianL1TriggerDiv]').removeClass("link_alter")
                            .addClass("link");
                        $("li[use=leftAccordionWorkshop]").children("ul").slideUp();
                        $("li[use=leftAccordionWorkshop]").find(
                                'div[use=accordianL1TriggerDiv]').removeClass("link_alter")
                            .addClass("link");
                        $("li[use=leftAccordionPostConference]").children("ul").slideDown();
                        $("li[use=leftAccordionPostConference]").find(
                            'div[use=accordianL1TriggerDiv]').removeClass("link").addClass(
                            "link_alter");
                    });
                });

                $("input[type=checkbox][operationMode=accomodation_tariff]").change(function() {
                    if (this.checked) {
                        var checkedTariffOb = $(this);
                        var packageId = $(checkedTariffOb).attr("accommodationPackageId");
                        var hotel_id = $(checkedTariffOb).attr("hotel_id");
                        var cutoffId = $(checkedTariffOb).attr("cutoff_id");
                        var hotelDays = $(checkedTariffOb).attr("hotelDays");
                        var combindedName = packageId + '_' + hotel_id + '_' + hotelDays + '_' +
                            cutoffId;

                        $("div[operetionmode=checkInCheckOutTr]").hide();
                        //alert(combindedName)
                        //$.each($("input[type=radio][name='accDate["+combindedName+"]']"),function(){	
                        /*$.each($("#accDate_"+combindedName),function(i,obj){	
                        	$(obj).parent().show();
                        	$(obj).parent().closest('[operetionmode=checkInCheckOutTr]').show();
                        	var checkoutValue = $(obj).attr('checkoutdate');
                        	$("input[type=radio][use=accoEndDate][value=" + checkoutValue + "]").parent().show();
                        	
                        })*/
                        $("div[operetionmode=checkInCheckOutTr]").find(
                            "input[type=radio][use=accoStartDate]").parent().hide();
                        $("div[operetionmode=checkInCheckOutTr]").find(
                            "input[type=radio][use=accoEndDate]").parent().hide();
                        //$("#accDate_"+combindedName).parent().show();
                        $("div[operetionMode=checkInCheckOutTr]").find(
                            "input[type=radio][operetionmode='checkInCheckOut_" + hotel_id + "_" +
                            hotelDays + "']").parent().show();
                        $("#accDate_" + combindedName).parent().closest(
                            '[operetionmode=checkInCheckOutTr]').show();
                        var checkoutValue = $("#accDate_" + combindedName).attr('checkoutdate');
                        $("input[type=radio][use=accoEndDate][value=" + checkoutValue + "]").parent()
                            .show();


                    }
                });
                // accommodation related work by weavers end

                $("input[type=checkbox][operationMode=workshopId]").each(function() {
                    $(this).click(function() {

                        var currChkbxStatus = $(this).attr("chkStatus");

                        $("input[type=checkbox][operationMode=workshopId]").prop("checked",
                            false);
                        $("input[type=checkbox][operationMode=workshopId]").attr("chkStatus",
                            "false");

                        if (currChkbxStatus == "true") {
                            $(this).prop("checked", false);
                            $(this).attr("chkStatus", "false");
                        } else {
                            $(this).prop("checked", true);
                            $(this).attr("chkStatus", "true");
                        }

                        calculateTotalAmount();

                        $("li[use=leftAccordionMasterClass]").children("ul").slideUp();
                        $("li[use=leftAccordionMasterClass]").find(
                                'div[use=accordianL1TriggerDiv]').removeClass("link_alter")
                            .addClass("link");
                        $("li[use=leftAccordionWorkshop]").children("ul").slideUp();
                        $("li[use=leftAccordionWorkshop]").find(
                                'div[use=accordianL1TriggerDiv]').removeClass("link_alter")
                            .addClass("link");
                        $("li[use=leftAccordionPostConference]").children("ul").slideDown();
                        $("li[use=leftAccordionPostConference]").find(
                            'div[use=accordianL1TriggerDiv]').removeClass("link").addClass(
                            "link_alter");
                    });
                });

                // november22 workshop related work by weavers start
                $("input[type=checkbox][operationMode=workshopId_nov]").each(function() {
                    $(this).click(function() {

                        var currChkbxStatus = $(this).attr("chkStatus");

                        $("input[type=checkbox][operationMode=workshopId_nov]").prop("checked",
                            false);
                        $("input[type=checkbox][operationMode=workshopId_nov]").attr(
                            "chkStatus", "false");

                        if (currChkbxStatus == "true") {
                            $(this).prop("checked", false);
                            $(this).attr("chkStatus", "false");
                        } else {
                            $(this).prop("checked", true);
                            $(this).attr("chkStatus", "true");
                        }

                        calculateTotalAmount();

                        $("li[use=leftAccordionMasterClass]").children("ul").slideUp();
                        $("li[use=leftAccordionMasterClass]").find(
                                'div[use=accordianL1TriggerDiv]').removeClass("link_alter")
                            .addClass("link");
                        $("li[use=leftAccordionWorkshop]").children("ul").slideUp();
                        $("li[use=leftAccordionWorkshop]").find(
                                'div[use=accordianL1TriggerDiv]').removeClass("link_alter")
                            .addClass("link");
                    });
                });
                // november22 workshop related work by weavers end

                $("input[type=checkbox][operationMode=workshopId_postconference]").each(function() {
                    $(this).click(function() {

                        var currChkbxStatus = $(this).attr("chkStatus");

                        $("input[type=checkbox][operationMode=workshopId_postconference]").prop(
                            "checked", false);
                        $("input[type=checkbox][operationMode=workshopId_postconference]").attr(
                            "chkStatus", "false");

                        if (currChkbxStatus == "true") {
                            $(this).prop("checked", false);
                            $(this).attr("chkStatus", "false");
                        } else {
                            $(this).prop("checked", true);
                            $(this).attr("chkStatus", "true");

                            if ($(this).attr("id") == 'workshop_id_only_tot') {
                                var parent = $(this).parent().closest(
                                    "li[use=leftAccordionOnlyTotPlus]");
                                $("ul[use=leftAccordion]").find(
                                    "div[use=accordianL1TriggerDiv][useSpec1=stage2]").attr(
                                    "hasAoptionSelected", "Y");

                                var emailObjContainer = $("ul[use=rightAccordion]").find(
                                    "li[use=registrationUserDetails]");
                                $(emailObjContainer).find(
                                    "input[type=email][name=user_email_id]").removeAttr(
                                    "disabled");
                                $(emailObjContainer).find("button[name=checkEmail]").show();
                                $(emailObjContainer).find(
                                        "input[type=email][name=user_email_id]").parent()
                                    .closest('.dis_abled').removeClass("dis_abled");

                                $("input[type=checkbox][operationMode=registration_tariff]")
                                    .prop("checked", false);
                                $("input[type=checkbox][operationMode=registration_tariff]")
                                    .attr("chkStatus", "false");

                                $("li[operetionMode=workshopTariffTr]").hide();

                                $("input[type=checkbox][operationMode=workshopId]").prop(
                                    "checked", false);
                                $("input[type=checkbox][operationMode=workshopId_postconference]")
                                    .prop("checked", false);

                                $(this).prop("checked", true);
                                $(this).attr("chkStatus", "true");

                                $("div[operetionMode=checkInCheckOutTr]").hide();
                                $("div[use=ResidentialAccommodationAccompanyOption]").hide();

                                $("li[use=leftAccordionDinner]").hide();

                                $(parent).find("div[use=accordianL1TriggerDiv]").trigger(
                                    "click");
                            }
                        }
                        calculateTotalAmount();
                    });
                });

                $.each($("ul[use=rightAccordion]").children("li"), function(i, level) {
                    $(level).attr("sequence", (i + 1));
                    $(level).children("div[use=rightAccordianTriggerDiv]").attr("sequence", (i + 1));
                    $(level).children("ul").attr("displayStatus", 'N');
                    $(level).children("ul").attr("level", '1');

                    $(level).find("button[use=nextButton]").attr("sequence", (i + 1));
                    $(level).find("button[use=nextButton]").attr("goto", (i + 1 + 1));

                    $(level).children("div[use=rightAccordianL1TriggerDiv]").click(function() {

                        var seq = $(this).attr("sequence");
                        var divParnt = $(this).parent().closest("li");
                        var target = $(level).children("ul");
                        if ($(target).attr("displayStatus") == 'N') {
                            $("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
                            $("ul[use=rightAccordion]").find("ul[level=1]").attr(
                                "displayStatus", 'N');
                            $(level).children("ul").slideDown();
                            $(level).children("ul").attr("displayStatus", 'Y');
                        }
                    });

                    $(level).find("button[use=nextButton]").click(function() {
                        var thisSeq = $(this).attr('sequence');
                        var nextSeq = $(this).attr('goto');
                        var regType = '';
                        if (validateOnNextButton(thisSeq)) {
                            var checkedRegistration = $(
                                "input[type=checkbox][operationMode=registration_tariff]:checked"
                            );
                            regType = $(checkedRegistration).attr('operationModeType');
                            var regAmount = parseFloat($(checkedRegistration).attr('amount'));
                            var totalPayAmount = 0;

                            try {
                                totalPayAmount = parseFloat($("div[use=totalAmount]").find(
                                    "span[use=totalAmount]").text());
                            } catch (e) {
                                totalPayAmount = 0;
                            }

                            var thisLi = $("ul[use=rightAccordion]").children("li[sequence='" +
                                thisSeq + "']");
                            $(thisLi).children("ul").slideUp();
                            $(thisLi).children("ul").attr("displayStatus", 'N');

                            var nextLi = $("ul[use=rightAccordion]").children("li[sequence='" +
                                nextSeq + "']");

                            $("div[use=totalAmount]").show();

                            // accommodation related work by weavers start 
                            if ($(
                                    "input[type=checkbox][operationMode=accomodation_tariff]:checked"
                                )
                                .length > 0) {
                                //if($("input[type=checkbox][operationMode=accomodation_tariff]").prop('checked') === true){

                                regType = $(
                                    "input[type=checkbox][operationMode=accomodation_tariff]:checked"
                                ).attr('operationModeType');

                            }

                            // accommodation related work by weavers end

                            if ($(thisLi).attr('use') == 'registrationUserDetails') {

                                if (regType == 'conference') {
                                    nextLi = $("ul[use=rightAccordion]").find(
                                        "li[use=registrationAccompanyDetails]");
                                } else if (regType == 'residential') {
                                    nextLi = $("ul[use=rightAccordion]").find(
                                        "li[use=registrationOptions]");

                                    // accommodation related work by weavers start 

                                    var checkedTariffOb = $(
                                        "input[type=checkbox][operationMode=accomodation_tariff]:checked"
                                    );
                                    var packageId = $(checkedTariffOb).attr(
                                        "accommodationPackageId");
                                    var hotel_id = $(checkedTariffOb).attr("hotel_id");
                                    var cutoffId = $(checkedTariffOb).attr("cutoff_id");
                                    var hotelDays = $(checkedTariffOb).attr("hotelDays");
                                    var combindedName = packageId + '_' + hotel_id + '_' +
                                        hotelDays + '_' + cutoffId;

                                    //$.each($("input[type=radio][name='accDate["+combindedName+"]']"),function(){	
                                    /*$.each($("#accDate_"+combindedName),function(){
                                    	$(this).parent().show();
                                    	var checkoutValue = $(this).attr('checkoutdate');
                                    	$("input[type=radio][use=accoEndDate][value=" + checkoutValue + "]").parent().show();
                                    	
                                    })*/

                                    $("#accDate_" + combindedName).parent().show();
                                    var checkoutValue = $("#accDate_" + combindedName).attr(
                                        'checkoutdate');
                                    $("input[type=radio][use=accoEndDate][value=" +
                                        checkoutValue + "]").parent().show();

                                    // accommodation related work by weavers end
                                } else if ($("#workshop_id_only_tot").is(":checked")) {
                                    nextLi = $("ul[use=rightAccordion]").find(
                                        "li[use=registrationPayment]");
                                }

                            } else if ($(thisLi).attr('use') == 'registrationOptions') {
                                var accommodationType = $(checkedRegistration).attr(
                                    'accommodationType');

                                if (accommodationType == 'SHARED') {
                                    nextLi = $("ul[use=rightAccordion]").find(
                                        "li[use=registrationPaymentOption]");
                                } else {
                                    nextLi = $("ul[use=rightAccordion]").find(
                                        "li[use=registrationAccompanyDetails]");
                                }
                            } else if ($(thisLi).attr('use') ==
                                'registrationAccompanyDetails') {
                                if (totalPayAmount == 0) {
                                    $("ul[use=rightAccordion]").find(
                                        "li[use=registrationPaymentOption]").find(
                                        "input[type=radio][for=CC]").trigger("click");
                                    nextLi = $("ul[use=rightAccordion]").find(
                                        "li[use=registrationPayment]");
                                }
                            }

                            if ($(nextLi).attr('use') == 'registrationPaymentOption') {
                                nextLi = $("ul[use=rightAccordion]").find(
                                    "li[use=registrationPayment]");
                            }

                            if ($(nextLi).attr('use') == 'registrationPayment') {
                                //$("div[use=totalAmount]").hide();
                                console.log($(thisLi).attr('use'));
                            }

                            if ($(nextLi).attr('use') == 'registrationProcess') {
                                //$("div[use=totalAmount]").hide();
                                console.log($(thisLi).attr('use'));
                            }

                            $("window").scrollTop(0);
                            window.scrollTo(0, 0);

                            setTimeout(function() {
                                $(nextLi).show();
                                $(nextLi).children("ul").slideDown();
                                $(nextLi).children("ul").attr("displayStatus", 'Y');
                            }, 500);
                        }
                    });

                    $(level).find("input[id=user_mobile]").blur(function() {
                        checkMobileNo(this);
                    });
                });

                $("input[type=radio][use=accompanyCountSelect]").click(function() {
                    var count = parseInt($(this).val());
                    var haveCount = $("div[use=accompanyDetails]").length;
                    for (var i = 1; i <= count; i++) {
                        $("div[use=accompanyDetails][index='" + i + "']").slideDown();
                    }
                    for (var j = (count + 1); j <= haveCount; j++) {
                        var accomDiv = $("div[use=accompanyDetails][index='" + j + "']");
                        $(accomDiv).slideUp();
                        $(accomDiv).find("input[type=text]").val('');
                        $(accomDiv).find("input[type=radio]").prop('checked', false);
                        $(accomDiv).find("input[type=checkbox]").prop('checked', false);
                    }
                    calculateTotalAmount();
                });

                $("input[type=radio][use=tariffPaymentMode]").click(function() {
                    var forPay = $(this).attr("for");
                    $("div[use=payRules]").hide();
                    $("div[use=payRules][for='" + forPay + "']").slideDown();

                    $("div[use=offlinePaymentOptionChoice]").hide();
                    $("div[use=offlinePaymentOption]").hide();

                    $("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").prop("checked",
                        false);
                    $("div[use=offlinePaymentOption]").find("input[type=text]").val('');
                    $("div[use=offlinePaymentOption]").find("input[type=date]").val('');

                    if ($(this).val() == 'OFFLINE') {
                        $("div[use=offlinePaymentOptionChoice]").slideDown();
                        if (forPay == 'CHQ') {
                            $("div[use=offlinePaymentOptionChoice]").find(
                                "input[type=radio][use=payment_mode_select][for=Cheque]").trigger(
                                "click");
                        } else if (forPay == 'DD') {
                            $("div[use=offlinePaymentOptionChoice]").find(
                                "input[type=radio][use=payment_mode_select][for=Draft]").trigger(
                                "click");
                        } else if (forPay == 'WIRE') {
                            $("div[use=offlinePaymentOptionChoice]").find(
                                "input[type=radio][use=payment_mode_select][for=NEFT]").trigger(
                                "click");
                        } else if (forPay == 'CASH') {
                            //$("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=Cash]").prop("checked",true);
                            $("div[use=offlinePaymentOptionChoice]").find(
                                "input[type=radio][use=payment_mode_select][for=Cash]").trigger(
                                "click");
                        }
                    }
                });

                $("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").click(function() {
                    var forPay = $(this).attr("for");
                    $("div[use=offlinePaymentOption]").hide();
                    $("div[use=offlinePaymentOption][for='" + forPay + "']").slideDown();

                    var paymentMode = $(this).attr("paymentMode");
                    $("input[type=radio][use=tariffPaymentMode]").prop("checked", false);
                    $("input[type=radio][use=tariffPaymentMode][value='" + paymentMode + "']").first()
                        .prop("checked", true);
                    calculateTotalAmount();
                });

                $("form[name=registrationForm]").submit(function(evnt) {
                    console.log('triggered submit @ ');
                    $.each($("ul[use=rightAccordion]").children("li"), function(i, level) {
                        try {
                            var seq = $(this).attr("sequence");
                            if (!validateOnNextButton(seq)) {
                                console.log('ERROR @ ' + seq);
                                evnt.preventDefault();
                            }
                        } catch (e) {
                            console.log('ERROR : ' + e.message);
                            evnt.preventDefault();
                        }
                    });
                });

                $("div[use=rightAccordianL1TriggerDiv]").first().trigger("click");
            });

            function showChekinChekoutDate(obj) {
                popDownAlert();

                $("input[type=radio][use=accoStartDate]").prop("checked", false);
                $(obj).prop("checked", true);

                var parent = $(obj).parent().closest("div[operetionMode=checkInCheckOutTr]");
                var checkoutDate = $(obj).attr("checkoutDate");
                $("input[type=radio][use=accoEndDate]").prop("checked", false);
                $(parent).find("input[use=accoEndDate][value='" + checkoutDate + "']").prop("checked", true);
            }

            function validateOnNextButton(seq) {
                popDownAlert();

                var returnVal = true;
                var thisLi = $("ul[use=rightAccordion]").children("li[sequence='" + seq + "']");
                var thisLiUse = $(thisLi).attr("use");


                //console.clear();

                $.each($(thisLi).find(
                    "input[type=text],input[type=email],input[type=tel],input[type=number],input[type=date],textarea"
                ), function() {
                    var attr = $(this).attr('required');
                    if (typeof attr !== typeof undefined && attr !== false) {
                        console.log('hasReq>>' + $(this).attr('name'));
                        if ($.trim($(this).val()) == '') {
                            $(this).focus();
                            popoverAlert(this);
                            returnVal = false;
                            return false;
                        }
                    }
                });

                if (!returnVal) return false;

                $.each($(thisLi).find("select"), function() {
                    var attr = $(this).attr('required');
                    if (typeof attr !== typeof undefined && attr !== false) {

                        if ($.trim($(this).val()) == '') {
                            $(this).focus();
                            popoverAlert(this);
                            returnVal = false;
                            return false;
                        }
                    }
                });

                if (!returnVal) return false;

                $.each($(thisLi).find("input[type=radio]"), function() {
                    var hasRequired = false;
                    var attr = $(this).attr('required');
                    if (typeof attr !== typeof undefined && attr !== false) {

                        hasRequired = true;
                    }
                    if (hasRequired) {
                        var name = $(this).attr("name");
                        if ($("input[type=radio][name='" + name + "']:checked").length == 0) {
                            popoverAlert(this);
                            returnVal = false;
                            return false;
                        }
                    }
                });

                if (!returnVal) return false;

                if (thisLiUse == 'registrationUserDetails') {
                    returnVal = validateRegistrationUserDetails(thisLi);
                    console.log('return val: ' + returnVal)
                    if (!returnVal) return false;
                } else if (thisLiUse == 'registrationOptions') {
                    returnVal = validateRegistrationOptions(thisLi);

                    if (!returnVal) return false;
                } else if (thisLiUse == 'registrationAccompanyDetails') {
                    returnVal = validateRegistrationAccompanyDetails(thisLi);

                    if (!returnVal) return false;
                } else if (thisLiUse == 'registrationPaymentOption') {
                    returnVal = validateRegistrationPaymentOption(thisLi);

                    if (!returnVal) return false;
                } else if (thisLiUse == 'registrationPayment') {
                    returnVal = validateRegistrationPayment(thisLi);

                    if (!returnVal) return false;
                }

                return returnVal;
            }

            /*function checkUserEmail(obj) {

                popDownAlert();

                var liParent = $(obj).parent().closest("li[use=registrationUserDetails]");
                var emailIdObj = $(liParent).find("#user_email_id");
                var emailId = $.trim($(emailIdObj).val());

                if (emailId != '') {
                    var filter =
                        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                    if (filter.test(emailId)) {
                        $(obj).hide();
                        $(liParent).find("div[use=emailProcessing]").find("img[use=loader]").show();
                        console.log(jsBASE_URL + 'returnData.process.php?act=getEmailValidationStatus&email=' +
                            emailId);
                        setTimeout(function() {
                            $.ajax({
                                type: "POST",
                                url: jsBASE_URL + 'returnData.process.php',
                                data: 'act=getEmailValidationStatus&email=' + emailId,
                                dataType: 'json',
                                async: false,
                                success: function(JSONObject) {
                                    console.log(JSONObject);

                                    if (JSONObject.STATUS == 'IN_USE') {
                                        var loginModal = $('#loginModal');
                                        $(loginModal).find("#user_email_id").val(emailId);
                                        $.ajax({
                                            type: "POST",
                                            url: jsBASE_URL + 'login.process.php',
                                            data: 'action=loginUniqueSequence&user_details=' +
                                                emailId,
                                            dataType: 'text',
                                            async: false,
                                            success: function(returnData) {
                                                console.log('returnData:' + returnData);
                                                $(loginModal).modal('show');
                                                $(liParent).find(
                                                        "div[use=emailProcessing]")
                                                    .find("img[use=loader]").hide();
                                                $(obj).show();
                                            }
                                        });
                                    } else if (JSONObject.STATUS == 'NOT_PAID') {
                                        var unpaidModalOnline = $('#unpaidModalOnline');
                                        $(unpaidModalOnline).find("#user_details").val(emailId);
                                        $(unpaidModalOnline).modal('show');
                                        $(liParent).find("div[use=emailProcessing]").find(
                                            "img[use=loader]").hide();
                                        $(obj).show();
                                    } else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE') {
                                        var unpaidModalOffline = $('#unpaidModalOffline');
                                        $(unpaidModalOffline).modal('show');
                                        $(liParent).find("div[use=emailProcessing]").find(
                                            "img[use=loader]").hide();
                                        $(obj).show();
                                    } else if (JSONObject.STATUS == 'PAY_NOT_SET_OFFLINE') {
                                        var payNotSetModalOffline = $('#payNotSetModalOffline');
                                        $(payNotSetModalOffline).modal('show');
                                        $(payNotSetModalOffline).modal('show');
                                        $(liParent).find("div[use=emailProcessing]").find(
                                            "img[use=loader]").hide();
                                        $(obj).show();
                                    } else if (JSONObject.STATUS == 'AVAILABLE') {
                                        enableAllFileds(liParent);
                                        $(liParent).find("div[use=emailProcessing]").find(
                                            "img[use=loader]").hide();

                                        var JSONObjectData = JSONObject.DATA;
                                        if (JSONObjectData) {
                                            $(liParent).find('#user_first_name').val(JSONObjectData
                                                .FIRST_NAME);
                                            $(liParent).find('#user_middle_name').val(JSONObjectData
                                                .MIDDLE_NAME);
                                            $(liParent).find('#user_last_name').val(JSONObjectData
                                                .LAST_NAME);
                                            $(liParent).find('#user_mobile').val(JSONObjectData
                                                .MOBILE_NO);

                                            if ($(liParent).find('#user_mobile').val() != '') {
                                                checkMobileNo($(liParent).find('#user_mobile'));
                                            }

                                            $(liParent).find('#user_phone_no').val(JSONObjectData
                                                .PHONE_NO);
                                            $(liParent).find('#user_address').val(JSONObjectData
                                                .ADDRESS);
                                            $(liParent).find('#user_city').val(JSONObjectData.CITY);
                                            $(liParent).find('#user_postal_code').val(JSONObjectData
                                                .PIN_CODE);

                                            $(liParent).find('#user_country').val(JSONObjectData
                                                .COUNTRY_ID);
                                            $(liParent).find('#user_country').trigger("change");

                                            $(liParent).find('#user_state').val(JSONObjectData
                                                .STATE_ID);
                                        }
                                        $(liParent).find("button[use=nextButton]").show();
                                    }
                                }
                            });
                        }, 500);
                    } else {
                        popoverAlert(emailIdObj);
                    }
                } else {
                    popoverAlert(emailIdObj);
                }
            }*/

            function enableAllFileds(obj) {
                $.each($(obj).find("input,select,textarea"), function() {
                    var attr = $(this).attr('disabled');
                    if (typeof attr !== typeof undefined && attr !== false) {
                        $(this).prop("disabled", false);
                        $(this).attr("wasDisabled", 'Y');
                        $(obj).find(".dis_abled").attr("hasDisableCSS", "Y");
                        $(obj).find(".dis_abled").removeClass("dis_abled");
                    }
                });
            }

            function disableAllFileds(obj) {
                $.each($(obj).find("input,select,textarea"), function() {
                    if ($(this).attr("wasDisabled") == 'Y') {
                        $(this).prop("disabled", true);
                        $(obj).find("span[hasDisableCSS=Y],div[hasDisableCSS=Y]").addClass("dis_abled");
                    }
                });
            }

            /*function checkMobileNo(mobileObj) {
                popDownAlert();

                var mobile = $(mobileObj).val();
                var parent = $('ul[use=rightAccordion]').children('li[use=registrationUserDetails]');

                $(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();
                $(parent).find("div[use=mobileProcessing]").find("img[use=loader]").show();
                $(parent).find("input[id=user_mobile_validated]").val("N");

                if (mobile != "") {
                    if (isNaN(mobile) || mobile.toString().length < 10 || isNaN(mobile) || mobile.toString().length >
                        15) {
                        popoverAlert(mobileObj);
                        $(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();
                        $(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();
                    } else {
                        console.log(jsBASE_URL + 'returnData.process.php?act=getMobileValidation&mobile=' + mobile);
                        setTimeout(function() {
                            $.ajax({
                                type: "POST",
                                url: jsBASE_URL + 'returnData.process.php',
                                data: 'act=getMobileValidation&mobile=' + mobile,
                                dataType: 'text',
                                async: false,
                                success: function(returnMessage) {
                                    $(parent).find("div[use=mobileProcessing]").find(
                                        "span[use=mobileAvailability]").hide();
                                    $(parent).find("div[use=mobileProcessing]").find(
                                        "img[use=loader]").hide();
                                    returnMessage = returnMessage.trim();
                                    if (returnMessage == 'IN_USE111') {
                                        popoverAlert(mobileObj, "Mobile no. is already in use.");
                                        $(parent).find("input[id=user_mobile_validated]").val("N");
                                    } else {
                                        $(parent).find("div[use=mobileProcessing]").find(
                                                "span[use=mobileAvailability][state=available]")
                                            .show();
                                        $(parent).find("div[use=mobileProcessing]").find(
                                            "input[id=user_mobile_validated]").val("Y");
                                        console.log('>>' + $(parent).find(
                                            "div[use=mobileProcessing]").find(
                                            "input[name=user_mobile_validated]").val());
                                    }
                                }
                            });
                        }, 500);
                    }
                } else {
                    popoverAlert(mobileObj);
                    $(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();
                }
            }*/

            function validateRegistrationUserDetails(obj) {

                // prevent the form to go to next step if user exist by weavers start
                var emailIdObj = $(obj).find("#user_email_id");
                var emailId = $.trim($(emailIdObj).val());
                var mobileObj = $(obj).find("#user_mobile");
                var mobileNo = $.trim($(mobileObj).val());

                var returnVal = true;
                if (emailId == "" && mobileNo == "") {
                   // popoverAlert($(obj).find("input[name=user_email_id]"));
                    //popoverAlert($(obj).find("input[name=user_mobile]"));
                    //returnVal = false;
                    //return false;
                }

                $.ajax({
                    type: "POST",
                    url: jsBASE_URL + 'registration.process.php',
                    data: 'act=getUserValidated&email=' + emailId + '&mobile=' + mobileNo,
                    async: false,
                    success: function(response) {
                        console.log(response);

                        /*if ($.trim(response) == 'EMAIL_IN_USE') {
                            console.log($(obj).find("div[use=emailProcessing] span[state=used]"))
                            $(obj).find("div[use=emailProcessing] span[state=used]").show();
                            $(obj).find("div[use=mobileProcessing] span[state=used]").hide();
                            returnVal = false;
                            return false;
                        } else if ($.trim(response) == 'MOBILE_IN_USE') {
                            $(obj).find("div[use=mobileProcessing] span[state=used]").show();
                            $(obj).find("div[use=emailProcessing] span[state=used]").hide();
                            returnVal = false;
                            return false;
                        } else if ($.trim(response) == 'NONE') {
                            alret(
                                "All fields are mandatory.Please fill up all the fields with proper value."
                            )
                            returnVal = false;
                            return false;
                        } else if ($.trim(response) == 'AVAILABLE') {
                            $(obj).find("div[use=emailProcessing] span[state=used]").hide();
                            $(obj).find("div[use=mobileAvailability] span[state=used]").hide();
                            returnVal = true;
                            return true;
                        }*/

                        $(obj).find("div[use=emailProcessing] span[state=used]").hide();
                            $(obj).find("div[use=mobileAvailability] span[state=used]").hide();
                            returnVal = true;
                            return true;
                    }
                });
                // prevent the form to go to next step if user exist by weavers end
               /* if ($(obj).find("input[id=user_mobile_validated]").val() != "Y") {
                    popoverAlert($(obj).find("input[name=user_mobile]"));
                    returnVal = false;
                    return false;
                }*/
                return returnVal;
            }

            // reload page after 10 min by weavers start
            setTimeout(function() {
                alert("Page get expired after 15 minutes of inactivity.");
                window.location.reload();
            }, 900000);
            // reload page after 10 min by weavers end

            function validateRegistrationOptions(obj) {
                var returnVal = true;
                //var checkedTariffOb = $("input[type=checkbox][operationMode=registration_tariff]:checked");
                // accommodation related work by weavers start
                var checkedTariffOb = $("input[type=checkbox][operationMode=accomodation_tariff]:checked");
                // accommodation related work by weavers end
                var checkedTariff = $(checkedTariffOb).val();
                var regType = $(checkedTariffOb).attr('operationModeType');
                var regClsfId = $(checkedTariffOb).val();
                var currency = $(checkedTariffOb).attr('currency');
                var offer = $(checkedTariffOb).attr('offer');

                if (regType == 'residential') {
                    var accommodationType = $(checkedTariffOb).attr("accommodationType");
                    var packageId = $(checkedTariffOb).attr("accommodationPackageId");
                    var hotel_id = $(checkedTariffOb).attr("hotel_id");
                    // accommodation related work by weavers start
                    var cutoffId = $(checkedTariffOb).attr("cutoff_id");
                    var hotelDays = $(checkedTariffOb).attr("hotelDays");
                    var combindedName = packageId + '_' + hotel_id + '_' + hotelDays + '_' + cutoffId;
                    // accommodation related work by weavers end
                    //$.each($("input[type=radio][name='accDate["+hotel_id+"]']"),function(){
                    // accommodation related work by weavers start			 													
                    //$.each($("input[type=radio][name='accDate["+combindedName+"]']"),function(){
                    $("div[operetionmode=checkInCheckOutTr]").find("input[type=radio][use=accoStartDate]").parent()
                        .hide();
                    $("div[operetionmode=checkInCheckOutTr]").find("input[type=radio][use=accoEndDate]").parent()
                        .hide();

                    //$("div[operetionMode=checkInCheckOutTr]").find("input[type=radio][operetionmode='checkInCheckOut_"+hotel_id+"_"+hotelDays+"']").parent().show();
                    /*$.each($("#accDate_"+combindedName),function(){
                    	
                    	$(this).parent().show();
                    	// accommodation related work by weavers end
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
                    });*/

                    $("div[operetionMode=checkInCheckOutTr]").find("input[type=radio][operetionmode='checkInCheckOut_" +
                        hotel_id + "_" + hotelDays + "']").parent().show();
                    var name = $("#accDate_" + combindedName).attr("name");
                    if ($("input[type=radio][name='" + name + "']:checked").length == 0) {
                        //$("#accDate_"+combindedName).parent().show();
                        $("div[operetionMode=checkInCheckOutTr]").find(
                                "input[type=radio][operetionmode='checkInCheckOut_" + hotel_id + "_" + hotelDays + "']")
                            .parent().show();

                        popoverAlert($("#accDate_" + combindedName));
                        returnVal = false;
                        return false;
                    } //else{
                    //$("#accDate_"+combindedName).parent().hide();
                    //}
                } else if (regType == 'conference') {
                    // no mandatory
                }

                return returnVal;
            }

            function validateRegistrationAccompanyDetails(obj) {
                var countObj = $(obj).find("input[type=radio][name=accompanyCount]:checked");
                var count = $(countObj).val();

                for (var i = 0; i < count; i++) {
                    var accomDiv = $("div[use=accompanyDetails][index='" + (i + 1) + "']");

                    var accomPanyNameObj = $(accomDiv).find("input[name='accompany_name_add[" + i + "]']");
                    var accomPanyName = $(accomPanyNameObj).val();

                    if ($.trim(accomPanyName) == '') {
                        console.log('accomPanyName>>BLANK');
                        $(accomPanyNameObj).focus();
                        popoverAlert(accomPanyNameObj);
                        return false;
                    }

                    var accomPanyDinnrObj = $(accomDiv).find("input[name='accompany_dinner_value[" + i + "]']");

                    if ($(accomPanyDinnrObj).is(":checked")) {
                        var accomPanyfoodChecked = $(accomDiv).find("input[name='accompany_food_choice[" + i +
                            "]']:checked");

                        if ($(accomPanyfoodChecked).length == 0) {
                            var accomPanyfoodObj = $(accomDiv).find("input[name='accompany_food_choice[" + i + "]']")
                                .first();

                            console.log('accomPanyfoodChecked>>NOT CHEK');
                            $(accomPanyfoodObj).focus();
                            popoverAlert(accomPanyfoodObj);
                            return false;
                        }
                    }
                }
                return true;
            }

            function validateRegistrationPaymentOption(obj) {
                return true;
            }

            function validateRegistrationPayment(obj) {
                var theAmount = parseFloat($("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount'));

                if (theAmount > 0) {
                    var paymentModeObj = $("li[use=registrationPaymentOption]").find(
                        "input[type=radio][name=registrationMode]:checked");
                    var paymentMode = $(paymentModeObj).val();

                    var paymentOptionCheckedOb = $("li[use=registrationPayment]").find(
                        "input[type=radio][name=payment_mode]:checked");

                    if ($(paymentOptionCheckedOb).length == 0) {
                        var paymentOptionObj = $("li[use=registrationPayment]").find(
                            "input[type=radio][name=payment_mode]").first();
                        console.log('paymentOptionCheckedOb>>NOT SELECTED');
                        popoverAlert(paymentOptionObj);
                        $(paymentOptionObj).focus();
                        return false;
                    }

                    if (paymentMode == 'OFFLINE') {
                        var returnVal = true;
                        var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");

                        $.each($("li[use=registrationPayment]").find("div[use=offlinePaymentOption][for='" +
                            paymentOptionChecked + "']"), function() {
                            var thiObj = $(this);
                            $.each($(thiObj).find("input[type=text],input[type=date]"), function(i,
                                validateObj) {
                                if ($.trim($(validateObj).val()) == '') {
                                    console.log('pay details>>BLANK');
                                    popoverAlert(validateObj);
                                    $(validateObj).focus();
                                    returnVal = false;
                                    return false;
                                }
                            });
                            if (!returnVal) return false;
                        });
                        if (!returnVal) return false;
                    } else if (paymentMode == 'ONLINE') {
                        var returnVal = true;
                        var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");

                        $.each($("li[use=registrationPayment]").find("div[use=offlinePaymentOption][for='" +
                            paymentOptionChecked + "']"), function() {
                            var thiObj = $(this);
                            $.each($(thiObj).find("input[type=text],input[type=date]"), function(i,
                                validateObj) {
                                if ($.trim($(validateObj).val()) == '') {
                                    console.log('pay details>>BLANK');
                                    popoverAlert(validateObj);
                                    $(validateObj).focus();
                                    returnVal = false;
                                    return false;
                                }
                            });
                            $.each($(thiObj).find("input[type=radio],input[type=checkbox]"), function(i,
                                validateObj) {
                                var name = $(validateObj).attr("name")
                                var type = $(validateObj).attr("type")
                                var parent = $(validateObj).parent().closest(
                                    "div[actAs=fieldContainer]");

                                var checkedObj = $(parent).find("input[type='" + type + "'][name='" +
                                    name + "']:checked");

                                if ($(checkedObj).length == 0) {
                                    var checkedOptionObj = $(parent).find("input[type='" + type +
                                        "'][name='" + name + "']").first();
                                    console.log('checkedOptionObj>>NOT SELECTED');
                                    popoverAlert(checkedOptionObj);
                                    $(checkedOptionObj).focus();
                                    return false;
                                }
                            });
                            if (!returnVal) return false;
                        });
                        if (!returnVal) return false;
                    }

                    if (!$("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]").is(
                            ":checked")) {
                        var valOb1 = $("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]");
                        popoverAlert(valOb1);
                        $(valOb1).focus();
                        return false;
                    }

                    if (!$("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]").is(
                            ":checked")) {
                        var valOb2 = $("li[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]");
                        popoverAlert(valOb2);
                        $(valOb2).focus();
                        return false;
                    }
                } else {
                    $("li[use=registrationPayment]").find("input[type=radio][name=payment_mode]").last().prop("checked",
                        true);
                }
                return true;
            }

            function calculateTotalAmount() {
                //console.log("====calculateTotalAmount====");
                var totalAmount = 0;
                var totTable = $("table[use=totalAmountTable]");
                $(totTable).children("tbody").find("tr").remove();
                var gst_flag = $('#gst_flag').val();
                
                $.each($("input[type=checkbox]:checked,input[type=radio]:checked"), function() {
                    var attr = $(this).attr('amount');
                    if (typeof attr !== typeof undefined && attr !== false) {
                        var amt = parseFloat(attr);
                        if(gst_flag==1)
                        {
                        	var cgstP = <?=$cfg['INT.CGST']?>;
                        	var cgstAmnt = (amt * cgstP) / 100;

                       		var sgstP = <?=$cfg['INT.SGST']?>;
                        	var sgstAmnt = (amt * sgstP) / 100;

                        	var totalGst = cgstAmnt + sgstAmnt;
                        	var totalGstAmount = cgstAmnt + sgstAmnt + amt;
                        	totalAmount = totalAmount + totalGstAmount;
                        }
                        else
                        {
                        	totalAmount = totalAmount + amt;
                        }
                        

                        console.log(">>amt" + amt + ' ==> ' + totalAmount);

                        var attrReg = $(this).attr('operationMode');
                        var isConf = false;
                        if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
                            'registration_tariff') {
                            isConf = true;
                        }
                        var isMastCls = false;
                        if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
                            'workshopId') {
                            isMastCls = true;
                        }

                        // november22 workshop related work by weavers start

                        var isNovWorkshop = false;
                        if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
                            'workshopId_nov') {
                            isNovWorkshop = true;
                        }

                        // november22 workshop related work by weavers end

                        var cloneIt = false;
                        var amtAlterTxt = 'Complimentary';

                        if (amt > 0) {
                            cloneIt = true;
                        } else if (isConf) {
                            cloneIt = true;
                            amtAlterTxt = 'Complimentary'
                        } else if (isMastCls || isNovWorkshop) {
                            cloneIt = true;
                            amtAlterTxt = 'Included in Registration'
                        }

                        if (cloneIt) {
                            var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
                                .clone();
                            $(cloned).attr("use", "rowCloned");
                            $(cloned).find("span[use=invTitle]").text($(this).attr('invoiceTitle'));
                            $(cloned).find("span[use=amount]").text((amt > 0) ? (amt).toFixed(2) : amtAlterTxt);
                            $(cloned).show();
                            $(totTable).children("tbody").append(cloned);
                        }

                        if(gst_flag==1)
                        {
	                        if (cloneIt) {

	                            var cgstP = <?=$cfg['INT.CGST']?>;
	                            var cgstAmnt = (amt * cgstP) / 100;

	                            var sgstP = <?=$cfg['INT.SGST']?>;
	                            var sgstAmnt = (amt * sgstP) / 100;

	                            var totalGst = cgstAmnt + sgstAmnt;
	                            var totalGstAmount = cgstAmnt + sgstAmnt + amt;


	                            var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
	                                .clone();
	                            $(cloned).attr("use", "rowCloned");
	                            $(cloned).find("span[use=invTitle]").text("GST 18%");
	                            $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
	                            $(cloned).show();
	                            $(totTable).children("tbody").append(cloned);
	                        }
	                    }
                    }

                    if ($(this).attr('operationMode') == 'registrationMode' && $(this).attr('use') ==
                        'tariffPaymentMode') {
                        if ($(this).val() == 'ONLINE') {
                            var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
                            var internetAmount = (totalAmount * internetHandling) / 100;
                            totalAmount = totalAmount + internetAmount;

                            console.log(">>amt" + internetAmount + ' ==> ' + totalAmount);

                            var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
                                .clone();

                            $(cloned).attr("use", "rowCloned");
                            $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
                            $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
                            $(cloned).show();
                            $(totTable).children("tbody").append(cloned);
                        }
                    }
                });

                totalAmount = Math.round(totalAmount, 0);

                $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
                $("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
                $("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount', totalAmount);
                $("div[use=totalAmount]").show();
            }

            function popoverAlert(obj, msg) {
                var parent = $(obj).parent().closest("div[actAs=fieldContainer]");
                var alertObj = $(parent).children("div[callFor=alert]");

                var attr = $(alertObj).attr('defaultAlert');
                if (typeof attr === typeof undefined || attr === false) {
                    $(alertObj).attr('defaultAlert', $(alertObj).text());
                    $(alertObj).click(function() {
                        popDownAlert(this);
                    });
                }

                if (typeof msg !== typeof undefined && $.trim(msg) !== '') {
                    $(alertObj).text(msg);
                } else {
                    $(alertObj).text($(alertObj).attr('defaultAlert'));
                }

                $(alertObj).show();
            }

            function popDownAlert(obj) {
                if (typeof obj === typeof undefined) {
                    $("div[callFor=alert]").hide();
                } else {
                    $(obj).hide();
                }
            }
            </script>
            <?
		}
?>
        </div>

        <div class="col-xs-12 menu-links">
           <!--  <p style="color:#FFFFFF;"><i><b>NOTE:</b> 18% GST will be added extra.</i></p>
            <p><a href="<?=_BASE_URL_?>terms.php"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Terms &
                    Conditions</a></p>
            <p><a href="<?=_BASE_URL_?>privacy.php"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Privacy Policy</a>
            </p>
            <p><a href="<?=_BASE_URL_?>cancellation.php"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Cancellation
                    & Refund Policy</a></p>-->
           <!-- <div><a href="<?=_BASE_URL_?>login.process.php?action=logout"><i class="fas fa-sign-out-alt"></i><br>Logout</a></div> -->
        </div> 
         <div class="col-xs-12 menu-links">                
                <p><a href="profile.php" target="_blank"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Rules and Regulations</a></p>              
                <p class="menu-login"><a href="<?=_BASE_URL_?>profile.php">BACK TO MY PROFILE</a></p>
            </div> 

        <br /><br />
        </div>

        <div class="col-lg-8 col-md-12 right-container regTarrif_rightPanel">
            <div class="col-xs-12 col-xs-offset-0" style="padding: 0; margin-top: 15px;">
                <div use="Introduction" class="col-xs-12" style="padding: 0; display:none;">
                    WELCOME to KNEE360 DEGREE
                </div>
                <ul use="rightAccordion" class="accordion" style="padding:0 0 0  0px; margin: 0; " id="userDetailsDiv">

                    <li use="registrationUserDetails" class="rightPanel_userDetails">
                        <div class="link" use="rightAccordianL1TriggerDiv">USER DETAILS</div>
                        <ul class="submenu" style="display: block" displayStatus="Y">
                            <li>
                                <div class="col-lg-10 col-md-12  form-group input-material dis_abled"
                                    actAs='fieldContainer'>
                                    <label for="user_email_id">E-mail <?php echo $rowUserDetails['user_first_name']; ?></label>
                                    <input type="email" style="text-transform:lowercase;" class="form-control"
                                        name="user_email_id" id="user_email_id"
                                        value="<?=($rowUserDetails['user_email_id']!='')?($rowUserDetails['user_email_id']):''?>"
                                        disabled="disabled" required>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>

                                    <input type="hidden" name="delegateId" id="delegateId" value="<?php echo $rowUserDetails['id']; ?>">
                                </div>
                                <div class=" col-lg-2 col-md-12 form-group input-material" use="emailProcessing">
                                    <button name="checkEmail" type="button" class="submit" style="display:none;"
                                        onClick="checkUserEmail(this);">GO</button>
                                    <img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing"
                                        style="width: 20px; display:none;">
                                    <span class="alert alert-success" use='emailAvailability' state='available'
                                        style="display:none;">Available</span>
                                    <span class="alert alert-danger" use='emailAvailability' state='used'
                                        style="display:none;">Already in use</span>
                                </div>
                                <div class="col-lg-10 col-md-12 form-group input-material dis_abled"
                                    actAs='fieldContainer'>
                                    <label for="user_mobile">Mobile</label>
                                    <input type="text" class="form-control" name="user_mobile" id="user_mobile"
                                        value="<?=($rowUserDetails['user_mobile_no']!='')?($rowUserDetails['user_mobile_no']):''?>"
                                        disabled="disabled" maxlength="10" required>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                                </div>
                                <div class=" col-lg-2 col-md-12 form-group input-material" use="mobileProcessing">
                                    <img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing"
                                        style="width: 20px; display:none;">
                                    <span class="alert alert-success" use='mobileAvailability' state='available'
                                        style="display:none;">Available</span>
                                    <span class="alert alert-danger" use='mobileAvailability' state='used'
                                        style="display:none;">Already in use</span>
                                    <input type="hidden" name="user_mobile_validated" id="user_mobile_validated"
                                        value="N" />
                                </div>
                                <div class="col-lg-12 form-group dis_abled" actAs='fieldContainer'>
                                    <div class="checkbox">
                                        <label class="select-lable">Title</label>
                                        <?php 
                                            $exp= explode(" ",$rowUserDetails['user_full_name']);
                                           
                                        ?>
                                        <div>
                                            <label class="container-box" style="float:left; margin-right:20px;">Dr
                                                <input type="radio" name="user_initial_title" value="Dr"
                                                    disabled="disabled" <?php if($exp[0]=='DR'){ echo 'checked'; } ?> required>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Prof
                                                <input type="radio" name="user_initial_title" value="Prof"
                                                    disabled="disabled" <?php if($exp[0]=='Prof'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Mr
                                                <input type="radio" name="user_initial_title" value="Mr"
                                                    disabled="disabled" <?php if($exp[0]=='Mr'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Ms
                                                <input type="radio" name="user_initial_title" value="Ms"
                                                    disabled="disabled" <?php if($exp[0]=='Ms'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Mrs
                                                <input type="radio" name="user_initial_title" value="Mrs"
                                                    disabled="disabled" <?php if($rowUserDetails=='Mrs'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="alert alert-danger" callFor='alert'>Please select a proper option.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group input-material dis_abled"
                                    actAs='fieldContainer'>
                                    <label for="user_first_name">First Name</label>
                                    <input type="text" class="form-control" name="user_first_name" id="user_first_name"
                                        value="<?=($rowUserDetails['user_first_name']!='')?($rowUserDetails['user_first_name']):''?> <?=($rowUserDetails['user_middle_name']!='')?($rowUserDetails['user_middle_name']):''?>"
                                        style="text-transform:uppercase;" disabled="disabled" required>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group input-material dis_abled"
                                    actAs='fieldContainer'>
                                    <label for="user_last_name">Last Name</label>
                                    <input type="text" class="form-control" name="user_last_name" id="user_last_name"
                                        value="<?=($rowUserDetails['user_last_name']!='')?($rowUserDetails['user_last_name']):''?>"
                                        style="text-transform:uppercase;" disabled="disabled" required>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                                </div>

                                <div class="col-lg-12 form-group input-material dis_abled" actAs='fieldContainer'>
                                    <label for="user_address">Address</label>
                                    <textarea class="form-control" name="user_address" id="user_address"
                                        style="text-transform:uppercase;" disabled="disabled"
                                        required><?=($rowUserDetails['user_address']!='')?($rowUserDetails['user_address']):''?></textarea>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group dis_abled" actAs='fieldContainer'>
                                    <label class="select-lable dis_abled">Country</label>
                                    <select class="form-control select" name="user_country" id="user_country"
                                        forType="country" style="text-transform:uppercase;" disabled="disabled"
                                        required>
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
                                        <option value="<?=$rowFetchCountry['country_id']?>"
                                            <?=($rowFetchCountry['country_id']==$rowUserDetails['user_country_id'])?'selected':''?>>
                                            <?=$rowFetchCountry['country_name']?></option>
                                        <?php
											}
										}
										?>
                                    </select>
                                    <div class="alert alert-danger" callFor='alert'>Please select a proper option.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group dis_abled" actAs='fieldContainer'>
                                    <label class="select-lable dis_abled">State</label>
                                    <select class="form-control select" name="user_state" id="user_state"
                                        forType="state" style="text-transform:uppercase;" disabled="disabled" required>
                                        <option value="<?=$state_id?>"><?=$state_name?></option>
                                    </select>
                                    <div class="alert alert-danger" callFor='alert'>Please select a proper option.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group input-material dis_abled"
                                    actAs='fieldContainer'>
                                    <label for="user_city">City</label>
                                    <input type="text" class="form-control" ame="user_city" id="user_city"
                                        value="<?=($rowUserDetails['user_city']!='')?($rowUserDetails['user_city']):''?>"
                                        style="text-transform:uppercase;" disabled="disabled" required>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group input-material dis_abled"
                                    actAs='fieldContainer'>
                                    <label for="user_postal_code">Postal Code</label>
                                    <input type="text" class="form-control" name="user_postal_code"
                                        id="user_postal_code"
                                        value="<?=($rowUserDetails['user_pincode']!='')?($rowUserDetails['user_pincode']):''?>"
                                        style="text-transform:uppercase;" disabled="disabled" required>
                                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                                </div>

                                <div class="col-lg-6 col-md-12 form-group dis_abled" actAs='fieldContainer'>
                                    <div class="checkbox">
                                        <label class="select-lable">Gender</label>
                                        <div>
                                            <label class="container-box" style="float:left; margin-right:20px;">Male
                                                <input type="radio" groupName="user_gender" name="user_gender"
                                                    id="user_gender_male" value="Male" disabled="disabled" required <?php if($rowUserDetails['user_gender']=='Male'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Female
                                                <input type="radio" groupName="user_gender" name="user_gender"
                                                    id="user_gender_female" value="Female" disabled="disabled" <?php if($rowUserDetails['user_gender']=='Female'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="alert alert-danger" callFor='alert'>Please select a proper option.</div>
                                </div>
                                <div class="col-lg-6 col-md-12 form-group dis_abled" actAs='fieldContainer'>
                                    <div class="checkbox">
                                        <label class="select-lable">Food Preference</label>
                                        <div>
                                            <label class="container-box" style="float:left; margin-right:20px;">Veg
                                                <input type="radio" groupName="user_food_choice"
                                                    name="user_food_preference" id="user_food_veg" value="VEG"
                                                    disabled="disabled" required <?php if($rowUserDetails['user_food_preference']=='VEG'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
                                                <input type="radio" groupName="user_food_choice"
                                                    name="user_food_preference" id="user_food_nonveg" value="NON_VEG"
                                                    disabled="disabled" <?php if($rowUserDetails['user_food_preference']=='NON_VEG'){ echo 'checked'; } ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            &nbsp;
                                            <input type="hidden" name="gst_flag" id="gst_flag" value="<?php echo $cfg['GST.FLAG']; ?>">
                                        </div>
                                    </div>
                                    <div class="alert alert-danger" callFor='alert'>Please select a proper option.</div>
                                </div>

                                <div class=" col-lg-2 text-center pull-right">
                                    <button type="button" class="submit" use='nextButton'
                                        style="display:none;">Next</button>
                                </div>

                                <div class="clearfix"></div>
                            </li>
                        </ul>
                    </li>

                    <li use="registrationOptions" style="display:none;" class="rightPanel_chooseOption">
                        <div class="link" use="rightAccordianL1TriggerDiv">ACCOMMODATION DETAILS</div>
                        <ul class="submenu" style="display:none;">
                            <li>
                                <div class="col-xs-12 form-group" style="display:none;" use="residentialOperations"
                                    operationMode="chhoseServiceOptions">
                                    <?php
		//$accommodationDetails = $cfg['ACCOMMODATION_PACKAGE_ARRAY'];

		// accommodation related work by weavers start
		$accommodationDetails = array();
		if( sizeof($hotel_array) > 0 ){
			$hotel_ids = array_column($hotel_array, 'HOTEL_ID');
			$package_ids = array_column($hotel_array, 'ACCOMMODATION_PACKAGE_ID');
			$sqlHotel				= array();
			$sqlHotel['QUERY']	 	= "SELECT
											tracm.id as ACCOMMODATION_TARIFF_ID,
											tracm.hotel_id as HOTEL_ID,
											tracm.package_id as ACCOMMODATION_PACKAGE_ID, 
											chkindate.check_in_date as CHECKIN_DATE,
											tracm.checkin_date_id as CHECKIN_DATE_ID,
											tracm.checkout_date_id as CHECKOUT_DATE_ID,
											chkoutdate.check_out_date as CHECKOUT_DATE,
											DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
											FROM "._DB_TARIFF_ACCOMMODATION_." as tracm
											INNER JOIN "._DB_ACCOMMODATION_CHECKIN_DATE_." as chkindate
											on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
											INNER JOIN "._DB_ACCOMMODATION_CHECKOUT_DATE_." as chkoutdate
											on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
											WHERE tracm.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ? 
											AND tracm.hotel_id IN(".implode(',',$hotel_ids).") AND tracm.package_id IN(".implode(',',$package_ids).")
											HAVING (DAYS) < 5
											ORDER BY tracm.hotel_id ASC, DAYS ASC,CHECKIN_DATE ASC, CHECKOUT_DATE ASC"; // HAVING (DAYS) < 4 // remove on 21.09.2022 (user can select hotels more then 3 days)
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.status', 'DATA' => 'A',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
			$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
			$resultHotel		    	= $mycms->sql_select($sqlHotel);

			foreach ($resultHotel as $key => $value) {
				// code...
				$accommodationDetails[$value['HOTEL_ID']][] = $value;
			}
		}
		
		// accommodation related work by weavers end $packageId
		foreach($accommodationDetails as $hotelId=>$rowAccommodation)
		{
?>
                                    <div class="col-xs-12 " style="padding: 0; display:block;" use="<?=$hotelId?>"
                                        operetionMode="checkInCheckOutTr">
                                        <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                            <div class="radio">
                                                <label class="select-lable">CHECK-IN DATE</label>
                                                <?
							foreach($rowAccommodation as $seq=>$accPackDet)
							{
								
						?>
                                                <label class="container-box"
                                                    style="display:block"><?=$accPackDet['CHECKIN_DATE']?>
                                                    <input type="radio"
                                                        operetionMode="checkInCheckOut_<?=$accPackDet['HOTEL_ID'].'_'.$accPackDet['DAYS']?>"
                                                        use="accoStartDate"
                                                        id="accDate_<?=$accPackDet['ACCOMMODATION_PACKAGE_ID'].'_'.$accPackDet['HOTEL_ID'].'_'.$accPackDet['DAYS'].'_'.$currentCutoffId?>"
                                                        name="accDate[]"
                                                        value="<?=$accPackDet['CHECKIN_DATE_ID'].'-'.$accPackDet['CHECKOUT_DATE_ID'].'-'.$accPackDet['HOTEL_ID']?>"
                                                        checkoutDate="<?=$accPackDet['CHECKOUT_DATE_ID'].'_'.$accPackDet['ACCOMMODATION_TARIFF_ID']?>"
                                                        onClick="showChekinChekoutDate(this);">
                                                    <span class="checkmark"></span>
                                                </label>
                                                <?
							}
						?>
                                            </div>
                                            <div class="alert alert-danger" callFor='alert'>Please select a proper
                                                option.</div>
                                        </div>
                                        <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                            <div class="radio">
                                                <label class="select-lable">CHECK-OUT DATE</label>
                                                <?
								foreach($rowAccommodation as $seq=>$accPackDet)
								{
							?>
                                                <label class="container-box"
                                                    style="display:none;"><?=$accPackDet['CHECKOUT_DATE']?>
                                                    <input type="radio"
                                                        operetionMode="checkInCheckOut_<?=$accPackDet['HOTEL_ID'].'_'.$accPackDet['DAYS']?>"
                                                        value="<?=$accPackDet['CHECKOUT_DATE_ID'].'_'.$accPackDet['ACCOMMODATION_TARIFF_ID']?>"
                                                        use="accoEndDate" disabled="disabled">
                                                    <span class="checkmark"></span>
                                                </label>
                                                <?	
								}
							?>
                                            </div>
                                            <div class="alert alert-danger" callFor='alert'>Please select a proper
                                                option.</div>
                                        </div>
                                    </div>
                                    <?
	}
?>
                                    <div class="col-xs-12 " style="padding: 0; display:none;"
                                        use="ResidentialAccommodationAccompanyOption">
                                        <h4>ROOM SHARING PREFERENCE, IF ANY</h4>
                                        <div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
                                            <label for="preffered_accommpany_name">NAME</label>
                                            <input type="text" class="form-control" name="preffered_accommpany_name"
                                                id="preffered_accommpany_name">
                                            <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                            </div>
                                        </div>
                                        <div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
                                            <label for="preffered_accommpany_mobile">MOBILE</label>
                                            <input type="tel" class="form-control" name="preffered_accommpany_mobile"
                                                id="preffered_accommpany_mobile">
                                            <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                            </div>
                                        </div>
                                        <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
                                            <label for="preffered_accommpany_email">EMAIL</label>
                                            <input type="email" class="form-control" name="preffered_accommpany_email"
                                                id="preffered_accommpany_email">
                                            <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                            </div>
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

                    <li use="registrationAccompanyDetails" style="display:none;" class="rightPanel_accompany">
                        <?
                        	//echo 'currentId='.$currentCutoffId;
							//$accompanyCatagory      = 2;

							$accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 

							//$registrationAmount 	= $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
							$registrationAmount 	= getCutoffTariffAmnt($currentCutoffId);
							$registrationCurrency 	= $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
							//$conferenceTariffArray
						?>
                        <input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
                        <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount"
                            value="<?=$registrationAmount?>" />
                        <div class="link" use="rightAccordianL1TriggerDiv">ACCOMPANY1</div>
                        <ul class="submenu" style="display: none">
                            <li>
                                <div class="col-xs-12 form-group ">
                                    <div class="checkbox">
                                        <label class="select-lable">Number of Accompanying Person(s)</label>
                                        <div>
                                            <label class="container-box" style="float:left; margin-right:20px;">None
                                                <input type="radio" name="accompanyCount" use="accompanyCountSelect"
                                                    value="0" amount="<?=0?>" invoiceTitle="Accompanying Person"
                                                    checked="checked" required>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">One
                                                <input type="radio" name="accompanyCount" use="accompanyCountSelect"
                                                    value="1" amount="<?=floatval($registrationAmount)*1?>"
                                                    invoiceTitle="Accompanying - 1 Person">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Two
                                                <input type="radio" name="accompanyCount" use="accompanyCountSelect"
                                                    value="2" amount="<?=floatval($registrationAmount)*2?>"
                                                    invoiceTitle="Accompanying - 2 Person">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Three
                                                <input type="radio" name="accompanyCount" use="accompanyCountSelect"
                                                    value="3" amount="<?=floatval($registrationAmount)*3?>"
                                                    invoiceTitle="Accompanying - 3 Person">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container-box" style="float:left; margin-right:20px;">Four
                                                <input type="radio" name="accompanyCount" use="accompanyCountSelect"
                                                    value="4" amount="<?=floatval($registrationAmount)*4?>"
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
                                    <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
                                        <label for="accompany_name_add_1">Name</label>
                                        <input type="text" class="form-control" name="accompany_name_add[0]"
                                            id="accompany_name_add_1" style="text-transform:uppercase;">
                                        <input type="hidden" name="accompany_selected_add[0]" value="0" />
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                    <?/* 
									<div class="col-xs-8 form-group" actAs='fieldContainer'>
										<div class="checkbox">
											<label class="select-lable" >DINNER</label>
<?
		$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
		foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
		{
?>
                                    <label class="container-box">
                                        <i
                                            class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
                                        <i class="itemPrice right-i pull-right">
                                            <?
			if(floatval($registrationDetailsVal['AMOUNT'])>0)
			{
				echo $registrationDetailsVal['CURRENCY'].' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
			}
									
?>
                                        </i>
                                        <input type="checkbox" name="accompany_dinner_value[0]" id="dinner_value"
                                            value="<?=$dinnerValue[$currentCutoffId]['ID']?>" operationMode="dinner"
                                            amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
                                            invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 1" />
                                        <span class="checkmark"></span>
                                    </label>
                                    <?
		}
?>
                                </div>
                                <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
            </div>
            */?>
            <div class="col-xs-12 form-group" actAs='fieldContainer'>
                <div class="checkbox">
                    <div>
                        <label class="container-box" style="float:left; margin-right:20px;">Veg
                            <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]"
                                id="accompany_food_1_veg" value="VEG">
                            <span class="checkmark"></span>
                        </label>
                        <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
                            <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]"
                                id="accompany_food_1_nonveg" value="NON_VEG">
                            <span class="checkmark"></span>
                        </label>
                        &nbsp;
                    </div>
                    <label class="select-lable">Food Preference</label>
                </div>
                <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
            </div>
        </div>

        <div class="col-xs-12" use="accompanyDetails" index="2" style="display:none;">
            <h4>ACCOMPANY 2</h4>
            <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
                <label for="accompany_name_add_2">Name</label>
                <input type="text" class="form-control" name="accompany_name_add[1]" id="accompany_name_add_2"
                    style="text-transform:uppercase;">
                <input type="hidden" name="accompany_selected_add[1]" value="1" />
                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
            </div>
            <?/*	
									<div class="col-xs-8 form-group" actAs='fieldContainer'>
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
                    value="<?=$dinnerValue[$currentCutoffId]['ID']?>" operationMode="dinner"
                    amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
                    invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 2" />
                <span class="checkmark"></span>
            </label>
            <?
		}
?>
        </div>
        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
        </div>
        */?>
        <div class="col-xs-12 form-group" actAs='fieldContainer'>
            <div class="checkbox">
                <div>
                    <label class="container-box" style="float:left; margin-right:20px;">Veg
                        <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]"
                            id="accompany_food_1_veg" value="VEG">
                        <span class="checkmark"></span>
                    </label>
                    <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
                        <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]"
                            id="accompany_food_1_nonveg" value="NON_VEG">
                        <span class="checkmark"></span>
                    </label>
                    &nbsp;
                </div>
                <label class="select-lable">Food Preference</label>
            </div>
            <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
        </div>
        </div>

        <div class="col-xs-12" use="accompanyDetails" index="3" style="display:none;">
            <h4>ACCOMPANY 3</h4>
            <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
                <label for="accompany_name_add_3">Name</label>
                <input type="text" class="form-control" name="accompany_name_add[2]" id="accompany_name_add_3"
                    style="text-transform:uppercase;">
                <input type="hidden" name="accompany_selected_add[2]" value="2" />
                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
            </div>
            <?/*	
									<div class="col-xs-8 form-group" actAs='fieldContainer'>
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
                    value="<?=$dinnerValue[$currentCutoffId]['ID']?>" operationMode="dinner"
                    amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
                    invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 3" />
                <span class="checkmark"></span>

            </label>
            <?
		}
?>
        </div>
        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
        </div>
        */?>
        <div class="col-xs-12 form-group" actAs='fieldContainer'>
            <div class="checkbox">
                <div>
                    <label class="container-box" style="float:left; margin-right:20px;">Veg
                        <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]"
                            id="accompany_food_3_veg" value="VEG">
                        <span class="checkmark"></span>
                    </label>
                    <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
                        <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]"
                            id="accompany_food_3_nonveg" value="NON_VEG">
                        <span class="checkmark"></span>
                    </label>
                    &nbsp;
                </div>
                <label class="select-lable">Food Preference</label>
            </div>
            <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
        </div>
        </div>

        <div class="col-xs-12" use="accompanyDetails" index="4" style="display:none;">
            <h4>ACCOMPANY 4</h4>
            <div class="col-xs-12 form-group input-material">
                <label for="accompany_name_add_4">Name</label>
                <input type="text" class="form-control" name="accompany_name_add[3]" id="accompany_name_add_4"
                    style="text-transform:uppercase;">
                <input type="hidden" name="accompany_selected_add[3]" value="3" />
                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
            </div>
            <?/*						
									<div class="col-xs-8 form-group" actAs='fieldContainer'>
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
                    value="<?=$dinnerValue[$currentCutoffId]['ID']?>" operationMode="dinner"
                    amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
                    invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 4" />
                <span class="checkmark"></span>
            </label>
            <?
		}
?>
        </div>
        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
        </div>
        */?>
        <div class="col-xs-12 form-group" actAs='fieldContainer'>
            <div class="checkbox">
                <div>
                    <label class="container-box" style="float:left; margin-right:20px;">Veg
                        <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]"
                            id="accompany_food_4_veg" value="VEG">
                        <span class="checkmark"></span>
                    </label>
                    <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
                        <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]"
                            id="accompany_food_4_nonveg" value="NON_VEG">
                        <span class="checkmark"></span>
                    </label>
                    &nbsp;
                </div>
                <label class="select-lable">Food Preference</label>
            </div>
            <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
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
                    <div class="col-xs-12 form-group" actAs='fieldContainer'>
                        <div class="radio">
                            <label class="select-lable">Payment Options</label>
                            <label class="container-box">
                                CREDIT / DEBIT CARD / ONLINE PAYMENT
                                <input type="radio" name="registrationMode" value="ONLINE"
                                    operationMode="registrationMode" use='tariffPaymentMode' for="CC">
                                <span class="checkmark"></span>
                            </label>
                            <div class="rightPanel_payment_CC" style="display:none; padding-left:20px;" for="CC"
                                use='payRules'>
                                Payment will be accepted only through VISA <img
                                    src="<?=_BASE_URL_?>images/visa_card_cion.png">
                                MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
                                RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
                                Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
                                MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
                                <!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png">
											American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"> --><br>
                                For any query, please write at kasscourse@gmail.com or call at 7596071519<br><br>
                            </div>


                            <label class="container-box">
                                CHEQUE
                                <input type="radio" name="registrationMode" value="OFFLINE"
                                    operationMode="registrationMode" use='tariffPaymentMode' for="CHQ">
                                <span class="checkmark"></span>
                            </label>
                            <div class="rightPanel_payment_CHQ" style="display:none; padding-left:39px;" for="CHQ"
                                use='payRules'><?='1'; ?>
                                Cheque or DD should be made in favor of "<b>ISOT 2023</b>",
                                Payable at Kolkata<br>

                                The same should be posted to the address, mentioned below - <br>
                                KNEE360 DEGREE Secretariat <br>
                                c/o RUEDA <br>
                                DL 220, Sector II, Salt Lake, Kolkata 700091<br>
                                For any query, please write at kasscourse@gmail.com or call at 7596071519<br><br>
                            </div>

                            <label class="container-box">
                                DEMAND DRAFT
                                <input type="radio" name="registrationMode" value="OFFLINE"
                                    operationMode="registrationMode" use='tariffPaymentMode' for="DD">
                                <span class="checkmark"></span>
                            </label>
                            <div class="rightPanel_payment_DD" style="display:none; padding-left:39px;" for="DD"
                                use='payRules'><?//$cfg['DRAFT_INFO'];?><?='1'; ?>
                                Demand Draft should be made in favor of "<b>ISOT 2023</b>",
                                Payable at Kolkata<br>
                                The same should be posted to the address, mentioned below - <br>
                                KNEE360 DEGREE Secretariat <br>
                                c/o RUEDA <br>
                                DL 220, Sector II, Salt Lake, Kolkata 700091 <br>
                                For any query, please write at kasscourse@gmail.com or call at 7596071519<br><br>
                            </div>

                            <label class="container-box">
                                NEFT / RTGS / BANK TRANSFER / NET BANKING
                                <input type="radio" name="registrationMode" value="OFFLINE"
                                    operationMode="registrationMode" use='tariffPaymentMode' for="WIRE">
                                <span class="checkmark"></span>
                            </label>
                            <div class="rightPanel_payment_NEFT" style="display:none; padding-left:39px;" for="WIRE"
                                use='payRules'><?='1'; ?>
                                NEFT or RTGS will be accepted for payment.<br>
                                <u>Bank Details</u> <br>
                               Account Name: ISOT 2023<br>
							   Bank Name : HDFC Bank<br>
							   A/c. No. : 50200073894372<br>
							   IFSC: HDFC0007431<br>
							   Branch Name: Mukundapur<br>
                            </div>

                            <label class="container-box">
                                CASH PAYMENT
                                <input type="radio" name="registrationMode" value="OFFLINE"
                                    operationMode="registrationMode" use='tariffPaymentMode' for="CASH">
                                <span class="checkmark"></span>
                            </label>
                            <div class="rightPanel_payment_CASH" style="display:none; padding-left:39px;" for="CASH"
                                use='payRules'>
                                Payment can be sent by money order to the KNEE360 DEGREE Secretariat.
                                Direct deposition will not be accepted.<br>
                                For any query, please write at kasscourse@gmail.com or call at 7596071519<br><br>
                            </div>

                        </div>
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
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
                    <table class="table bill" use="totalAmountTable" style="display:none;">
                        <thead>
                            <tr>
                                <th>DETAIL</th>
                                <th align="right" style="text-align:right;">AMOUNT (INR)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr style="display:none;" use='rowCloneable'>
                                <td>&bull; &nbsp <span use="invTitle">Something</span></td>
                                <td align="right"><span use="amount">0.00</span></td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td align="right"><span use='totalAmount'>0.00</span></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="col-xs-12 form-group">
                        <i><b>NOTE:</b> Rates are inclusive of all Taxes.</i>
                    </div>

                    <div class="col-xs-12 form-group" use="offlinePaymentOptionChoice" actAs='fieldContainer'>
                        <div class="checkbox">
                            <label class="select-lable">Payment Option</label>
                            <div>
                                <label class="container-box" style="float:left; margin-right:30px;">Online Payment
                                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Card"
                                        for="Card" paymentMode='ONLINE'>
                                    <span class="checkmark"></span>
                                </label>

                                <label class="container-box" style="float:left; margin-right:30px;">Cheque
                                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque"
                                        for="Cheque" paymentMode='OFFLINE'>
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container-box" style="float:left; margin-right:30px;">Draft
                                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft"
                                        for="Draft" paymentMode='OFFLINE'>
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container-box" style="float:left; margin-right:30px;">NEFT
                                    <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT"
                                        for="NEFT" paymentMode='OFFLINE'>
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container-box" style="float:left; margin-right:30px;">RTGS
                                    <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS"
                                        for="RTGS" paymentMode='OFFLINE'>
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container-box" style="float:left; margin-right:30px;">Cash
                                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash"
                                        for="Cash" paymentMode='OFFLINE'>
                                    <span class="checkmark"></span>
                                </label>

                                <!-- UPI Payment Option Added By Weavers start -->
                                <!-- <label class="container-box" style="float:left; margin-right:30px;">UPI
											  <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI" paymentMode='OFFLINE'>
											  <span class="checkmark"></span>
											</label> -->
                                <!-- UPI Payment Option Added By Weavers end -->
                                &nbsp;
                            </div>
                        </div>
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>

                    <? /* commented as per the feedback on 09.09.2022
								<div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
									<div class="checkbox">
										<label class="select-lable" >Card Type</label>
										<div>
											<!-- <label class="container-box" style="float:left; margin-right:30px;">
											  <img src="<?=_BASE_URL_?>images/international_globe.png" height="20px;">
                    International Card
                    <input type="radio" name="card_mode" use="card_mode_select" value="International">
                    <span class="checkmark"></span>
                    </label> -->
                    <label class="container-box" style="float:left; margin-right:30px;">
                        <img src="<?=_BASE_URL_?>images/india_globe.png" height="20px;">
                        Indian Card
                        <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
                        <span class="checkmark"></span>
                    </label>
                    &nbsp;
                    </div>
                    </div>
                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>
                    */ ?>
                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Card">
                        Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">
                        MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
                        RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
                        Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
                        MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
                        <!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png">
									American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"> --><br>
                        For any query, please write at kasscourse@gmail.com or call at 7596071519<br><br>
                    </div>

                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Cash" actAs='fieldContainer'>
                        <label for="user_first_name">Money Order Sent Date</label>
                        <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date"
                            max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>
                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Cash">
                        Payment can be sent by money order to the ISOT 2023 Secretariat.
                        Direct deposition will not be accepted.<br>
                        For any query, please write at isot2023kolkata@gmail.com or call at 7596071510<br><br>
                    </div>

                    <!-- UPI Payment Option Added By Weavers start -->
                    <? /*<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>
									<label for="txn_no">UPI Transaction ID</label>
									<input type="text" class="form-control" name="txn_no" id="txn_no">
									<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
								</div>
								<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>
									<label for="txn_no">UPI Payment Date</label>
									<input type="date" class="form-control" name="upi_date" id="upi_date" max="<?=$mycms->cDate("Y-m-d")?>"
                    min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div> */ ?>
                    <!-- UPI Payment Option Added By Weavers end -->

                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Cheque" actAs='fieldContainer'>
                        <label for="user_first_name">Cheque No</label>
                        <input type="text" class="form-control" name="cheque_number" id="cheque_number">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Cheque" actAs='fieldContainer'>
                        <label for="user_first_name">Drawee Bank</label>
                        <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Cheque" actAs='fieldContainer'>
                        <label for="user_first_name">Cheque Date</label>
                        <input type="date" class="form-control" name="cheque_date" id="cheque_date"
                            max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>
                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Cheque"><?//='1'; ?>
                        Cheque or DD should be made in favor of "<b>ISNCON 2023</b>", Payable at
                        Kolkata<br>
                        <u>Bank Details</u> <br>
                        Account Name: ISNCON 2023<br>
                        Bank Name : HDFC Bank<br>
                        A/c. No. : 50200073894372<br>
                        IFSC: HDFC0007431<br>
                        Branch Name: Mukundapur<br>


                    </div>

                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Draft" actAs='fieldContainer'>
                        <label for="user_first_name">Draft No</label>
                        <input type="text" class="form-control" name="draft_number" id="draft_number">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Draft" actAs='fieldContainer'>
                        <label for="user_first_name">Drawee Bank</label>
                        <input type="text" class="form-control" name="draft_drawn_bank" id="draft_drawn_bank">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Draft" actAs='fieldContainer'>
                        <label for="user_first_name">Draft Date</label>
                        <input type="date" class="form-control" name="draft_date" id="draft_date"
                            max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>
                    
                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="Draft">
                        	<?//='1'; ?>
                        Demand Draft should be made in favor of "<b>ISNCON 2023</b>", Payable at
                        Kolkata<br>
                        The same should be posted to the address, mentioned below - <br>
                        ISNCON 2023 Secretariat <br>
                        c/o RUEDA <br>
                        DL 220, Sector II, Salt Lake, Kolkata 700091 <br>
                        For any query, please write at isot2023kolkata@gmail.com or call at 7596071510<br><br>
                    </div>

                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="NEFT" actAs='fieldContainer'>
                        <label for="user_first_name">Transaction Id</label>
                        <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="NEFT" actAs='fieldContainer'>
                        <label for="user_first_name">Drawee Bank</label>
                        <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="NEFT" actAs='fieldContainer'>
                        <label for="user_first_name">Date</label>
                        <input type="date" class="form-control" name="neft_date" id="neft_date"
                            max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>
                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="NEFT"><?//='1'; ?>
                        NEFT or RTGS will be accepted for payment.<br>
                        <u>Bank Details</u> <br>
                        Account Name: ISNCON 2023<br>
                        Bank Name : HDFC Bank<br>
                        A/c. No. : 50200073894372<br>
                        IFSC: HDFC0007431<br>
                        Branch Name: Mukundapur<br>
                    </div>

                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="RTGS" actAs='fieldContainer'>
                        <label for="user_first_name">Transaction Id</label>
                        <input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="RTGS" actAs='fieldContainer'>
                        <label for="user_first_name">Drawee Bank</label>
                        <input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name">
                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                    </div>
                    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="RTGS" actAs='fieldContainer'>
                        <label for="user_first_name">Date</label>
                        <input type="date" class="form-control" name="rtgs_date" id="rtgs_date"
                            max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
                        <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                    </div>
                    <div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption"
                        for="RTGS">
                        NEFT or RTGS will be accepted for payment.<?//='1'; ?><br>
                        <u>Bank Details</u> <br>
                        Account Name: ISNCON 2023<br>
                        Bank Name : HDFC Bank<br>
                        A/c. No. : 50200073894372<br>
                        IFSC: HDFC0007431<br>
                        Branch Name: Mukundapur<br>
                    </div>

                    <div class="col-xs-12 form-group" actAs='fieldContainer'>
                        <div class="checkbox">
                            <label class="container-box">
                                By Clicking, you hereby agree to receive all promotional SMS and e-mails related to
                                ISOT 2023. To unsubscribe, send us a mail at isot2023kolkata@gmail.com.
                                <input type="checkbox" name="acceptance1" value="acceptance1" required>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="alert alert-danger" callFor='alert'>Please choose option.</div>
                    </div>
                    <div class="col-xs-12 form-group" actAs='fieldContainer'>
                        <div class="checkbox">
                            <label class="container-box">
                                I have read and clearly understood the
                                <a href="<?=_BASE_URL_?>terms.php" title="Click to View 'Terms &amp; Conditions'"
                                    target="_blank" class="anclink">Terms &amp; Conditions</a>
                                and
                                <a href="<?=_BASE_URL_?>cancellation.php"
                                    title="Click to View 'Cancellation &amp; Refund Policy'" target="_blank"
                                    class="anclink">Cancellation &amp; Refund Policy</a>
                                and agree with the same.
                                <input type="checkbox" name="acceptance2" value="acceptance2" required>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="alert alert-danger" callFor='alert'>Please choose option.</div>
                    </div>

                    <div class=" col-xs-12 text-center pull-right">
                        <button type="submit" class="submit" use='nextButton'>Proceed to Payment</button>
                    </div>
                    <div class="clearfix"></div>
                </li>
            </ul>
        </li>

        <li use="registrationProcess" style="display:none; text-align:center;" class="rightPanel_payment">
            <img src="<?=_BASE_URL_?>images/PaymentPreloader.gif" />
        </li>
        </ul>

        <div use="totalAmount" class="col-xs-12 totalAmount pull-right" style="padding: 0;  display:none;">
            <div class=" col-xs-12" style="display:none1;">
                <table class="table bill" use="totalAmountTable">
                    <thead>
                        <tr>
                            <th align="left" style="text-align:left;">DETAIL</th>
                            <th align="right" style="text-align:right;">AMOUNT (INR)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr style="display:none;" use='rowCloneable'>
                            <td align="left" style="text-align:left;">&bull; &nbsp <span use="invTitle"
                                    class="pull-left">Something</span></td>
                            <td align="right" style="text-align:right;"><span use="amount">0.00</span></td>
                        </tr>
                        <tr>
                            <td align="left" style="text-align:left;">Total</td>
                            <td align="right" style="text-align:right;"><span use='totalAmount'>0.00</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        </div>
        </div>
    </form>

    <div id="loginModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="frmLoginUniqueSequence" id="frmLoginUniqueSequence" action="<?=_BASE_URL_?>login.process.php"
                method="post">
                <input type="hidden" name="action" value="uniqueSeqVerification" />
                <div class="modal-content">
                    <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                        <div class="log">
                            <h3>YOU ARE REGISTERED</h3>
                        </div>
                    </div>

                    <div class="modal_subHead">
                        <h2><span>LOGIN with the unique sequence sent to you.</span></h2>
                    </div>

                    <div class="col-xs-12 profileright-section">
                        <div class="login-user" style="margin-top: 25px;">
                            <h4><input type="email" name="user_email_id" id="user_email_id" value=""
                                    style="text-transform:lowercase; border:0px;" readonly="" /></h4>
                        </div>
                        <div class="login-user" style="margin-top: 5px;">
                            <h4><input type="text" name="user_otp" id="user_otp" value="#" required /></h4>
                        </div>
                        <div class="bttn" style="margin-top: 25px;"><button type="submit">Login</button>&nbsp;<button
                                type="button" style="background:#7f8080;" use='cancel'>Cancel</button></div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="unpaidModalOnline" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post"
                action="<?=$cfg['BASE_URL']?>login.process.php">
                <input type="hidden" name="action" value="loginRegToken" />
                <div class="modal-content">
                    <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                        <div class="log">
                            <h3>PAYMENT PENDING</h3>
                        </div>
                    </div>

                    <div class="modal_subHead">
                        <h2><span>Your e-mail id is already registered with us but the payment procedure remained
                                incomplete.To complete, please pay the registration fees.</span></h2>
                    </div>

                    <div class="col-xs-12 profileright-section">
                        <div class="login-user" style="margin-top: 25px;">
                            <h4><input type="email" name="user_details" id="user_details" value=""
                                    style="text-transform:lowercase; border:0px;" readonly="" /></h4>
                        </div>
                        <div class="bttn" style="margin-top: 25px;"><button type="submit">Proceed to
                                pay</button>&nbsp;<button type="button" style="background:#7f8080;"
                                use='cancel'>Cancel</button></div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="unpaidModalOffline" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post"
                action="<?=$cfg['BASE_URL']?>login.process.php">
                <input type="hidden" name="action" value="loginRegToken" />
                <div class="modal-content">
                    <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                        <div class="log">
                            <h3>PAYMENT IN PROCESS</h3>
                        </div>
                    </div>
                    <div class="modal_subHead">
                        <h2><span>Your e-mail id is already registered with us but the payment procedure is ongoing.
                                Pleasr contact the registration secretariat for further details.</span></h2>
                    </div>
                    <div class="col-xs-12 profileright-section">
                        <div class="bttn" style="margin-top: 25px;"><button type="button" style="background:#7f8080;"
                                use='cancel'>Close</button></div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="payNotSetModalOffline" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post"
                action="<?=$cfg['BASE_URL']?>login.process.php">
                <input type="hidden" name="action" value="loginRegToken" />
                <div class="modal-content">
                    <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                        <div class="log">
                            <h3>PAYMENT PENDING</h3>
                        </div>
                    </div>

                    <div class="modal_subHead">
                        <h2><span>Your e-mail id is already registered with us but the payment procedure remained
                                incomplete.To complete, please pay the registration fees.</span></h2>
                    </div>

                    <div class="col-xs-12 profileright-section">
                        <div class="login-user" style="margin-top: 25px;">
                            <h4><input type="email" name="user_email_id" id="user_email_id" value=""
                                    style="text-transform:lowercase; border:0px;" readonly="" /></h4>
                        </div>
                        <div class="bttn" style="margin-top: 25px;"><button type="submit">Proceed to
                                pay</button>&nbsp;<button type="button" style="background:#7f8080;"
                                use='cancel'>Cancel</button></div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        $('#loginModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        $('#loginModal').find("button[use=cancel]").click(function() {
            disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
            $('#loginModal').modal('hide');
        });

        $('#unpaidModalOnline').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        $('#unpaidModalOnline').find("button[use=cancel]").click(function() {
            disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
            $('#unpaidModalOnline').modal('hide');
        });

        $('#unpaidModalOffline').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        $('#unpaidModalOffline').find("button[use=cancel]").click(function() {
            disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
            $('#unpaidModalOffline').modal('hide');
        });

        $('#payNotSetModalOffline').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        $('#payNotSetModalOffline').find("button[use=cancel]").click(function() {
            disableAllFileds($('ul[use=rightAccordion]').children('li[use=registrationUserDetails]'));
            $('#payNotSetModalOffline').modal('hide');
        });

        setInterval(function() {
            var windowHieght = $(document).height();
            $(".left-container-box").css('min-height', windowHieght + 'px');
        }, 1000);
    });
    </script>
</body>

</html>
<?php
	
?>