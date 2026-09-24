<?php
include_once("includes/source.php");
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');
include_once('webmaster/includes/function.php');

if (strtolower($_SERVER['HTTP_HOST']) == 'localhost' || $_SESSION['SHOW'] == 'YES') {
    //
} else {

    //header("location: https://www.ruedakolkata.com/aiccrcog2019_ver0/profile.php");
}
$placeName = '';
if (isset($_SESSION['WEATHER_DATA'])) {
    $weatherApiData = $_SESSION['WEATHER_DATA'];
    $placeName = $weatherApiData['place_name'] == '' ? getPlaceName($weatherApiData['latitude'], $weatherApiData['longitude']) : $weatherApiData['place_name'];
} else {
    $weatherApiData = weatherApi();
}

// echo $cfg['CONF_START_DATE'] ; die;
// echo '<pre>'; print_r($weatherApiData); die;

foreach ($weatherApiData['days'] as $k => $val) {
    if ($val['datetime'] == date('Y-m-d', strtotime($cfg['CONF_START_DATE']))) {
        $loc = $weatherApiData['address'];
        $todayMaxTemp = $val['tempmax'];
        $todayMinTemp = $val['tempmin'];
        $todayconditions = $val['conditions'];
        $todayhumidity = $val['humidity'];
        $todayPrecipitation = $val['precipprob'];
        $todaywindspeed = $val['windspeed'];
        $todayDate = $val['datetime'];
        $totayTemp = $val['temp'];
        $todayTempIcon = $val['icon'];
    } else {
    }
}

$firstFiveValuesTemp = array_slice($weatherApiData['days'], 1, 7);

//echo '<pre>'; print_r($firstFiveValues); die;


if ($todayTempIcon == 'partly-cloudy-day') {
    $tempIcon = 'cloud-3.png';
} else if ($todayTempIcon == 'clear-day') {
    $tempIcon = 'cloud-1.png';
} else {
    $tempIcon = 'cloud-2.png';
}

//echo '<pre>'; print_r($weatherApiData['days']);

$title = 'Profile';
// echo $mycms->getSession('LOGGED.USER.ID');
$loginDetails      = login_session_control();

$delegateId      = $loginDetails['DELEGATE_ID'];
$rowUserDetails  = getUserDetails($delegateId);
$invoiceList      = getConferenceContents($delegateId);
$currentCutoffId = getTariffCutoffId();

$accompany_tariff   = getCutoffTariffAmnt($currentCutoffId);
//  echo '<pre>'; print_r($rowUserDetails); die;
$registrationAmount   = getCutoffTariffAmnt($currentCutoffId);


$conferenceInvoiceDetails   = reset($invoiceList[$delegateId]['REGISTRATION']);

$workshopDetails   = $invoiceList[$delegateId]['WORKSHOP'];
$accompanyDtlsArr  = $invoiceList[$delegateId]['ACCOMPANY'];
$delgDinner    = getDinnerDetailsOfDelegate($delegateId);

$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

$dinnerDtls  = array();
if ($delgDinner && !empty($delgDinner)) {
    $dinnerDtls[$delegateId]                = $delgDinner;
    $dinnerDtls[$delegateId]['INVOICE']     = getInvoiceDetails($delgDinner['refference_invoice_id']);
    $dinnerDtls[$delegateId]['USER']        = $rowUserDetails;
}

$dinnerDtlsAccm                 = array();
foreach ($accompanyDtlsArr as $key => $accompanyFullDtls) {
    $accomDtlsForDinnr                                  = $accompanyFullDtls['ROW_DETAIL'];
    $accompDinnrDet                                     = getDinnerDetailsOfDelegate($accomDtlsForDinnr['id']);

    if (!empty($accompDinnrDet)) {
        $dinnerDtlsAccm[$accomDtlsForDinnr['id']]               = $accompDinnrDet;
        $dinnerDtlsAccm[$accomDtlsForDinnr['id']]['INVOICE']    = getInvoiceDetails($accompDinnrDet['refference_invoice_id']);
        $dinnerDtlsAccm[$accomDtlsForDinnr['id']]['USER']       = $accomDtlsForDinnr;
    }
}

	$condition = " AND status IN ('A')";
	$sqlSlip 	= array();
	$sqlSlip['QUERY']  = "SELECT IFNULL(activeInvoice.invoiceCount,0) AS activeInvoiceCount,
								slip.*
						   FROM " . _DB_SLIP_ . " slip			  
			   LEFT OUTER JOIN ( SELECT COUNT(*) AS invoiceCount,
									   `slip_id`
						  FROM  " . _DB_INVOICE_ . " 
						 WHERE 1 " . $condition . "
						GROUP BY `slip_id` ) activeInvoice
							  ON slip.id = activeInvoice.slip_id 
						 WHERE slip.delegate_id = ? 
						   AND slip.status = ?
						   AND activeInvoice.invoiceCount >?";

	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.delegate_id', 	       'DATA' => $delegateId,             'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'slip.status', 	  	       'DATA' => 'A',             		   'TYP' => 's');
	$sqlSlip['PARAM'][]	=	array('FILD' => 'activeInvoice.invoiceCount', 'DATA' => 0,             		   'TYP' => 's');
	$resSlip   = $mycms->sql_select($sqlSlip);


$sqlcutoff['QUERY']   = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " WHERE status = 'A' AND `id` = ?";
$sqlcutoff['PARAM'][] = array('FILD' => 'id', 'DATA' => $currentCutoffId,  'TYP' => 's');
$rescutoff          = $mycms->sql_select($sqlcutoff);
$endDate          = $rescutoff[0]['end_date'];

$sqlFetchCountdown        = array();
$sqlFetchCountdown['QUERY']    = "SELECT * 
                                FROM " . _DB_LANDING_PAGE_SETTING_;
$resultFetchCountdown       = $mycms->sql_select($sqlFetchCountdown);

$dateArr          = explode("-", $rescutoff[0]['end_date']);
$dateCount = new DateTime($rescutoff[0]['end_date']);



// abstract related work by weavers stat
$operate = false;

if ($cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d')) {
    $operate = true;
}

//==================================================== ABSTRACT DATA =================================================================
$sqlInfo  = array();
$sqlInfo['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
             WHERE `status` = ?";
$sqlInfo['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
$resultInfo      = $mycms->sql_select($sqlInfo);
$rowInfo         = $resultInfo[0];
$dateArrAbs          = explode("-", $resultInfo[0]['abstract_submission_date']);
$dateCountAbs = new DateTime($resultInfo[0]['abstract_submission_date']);

$currentDate = date("Y-m-d");
$submissionDate = date("Y-m-d", strtotime($rowInfo['abstract_submission_date']));
$abstract_start_date = date("Y-m-d", strtotime($companyInfo['abstract_start_date']));


$startD = $rowInfo['conf_start_date'];
$endD = $rowInfo['conf_end_date'];
$startDate = new DateTime($startD);
$endDate = new DateTime($endD);

$formatted = $startDate->format('M d Y \a\t g:i a');
$hod_consent_file_types = $rowInfo['hod_consent_file_types']; //JSON array
$abstract_file_types = $rowInfo['abstract_file_types'];
$hod_consent_file_types_decoded = json_decode($rowInfo['hod_consent_file_types']);

$unpaid_offline_temp = $rowInfo['notification_unpaid_offline'];
$unpaid_online_msg = $rowInfo['notification_unpaid_online'];

$find = ['[CONF NAME]', '[PHONE NUMBER]'];
$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline_temp);

$abstract_details = delegateAbstractDetailsSummeryWithoutTopic($delegateId);

$abstract_topic = delegateAbstractDetailsSummery($delegateId);

$sqlAbstractTopic              =    array();
$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                              WHERE `status` IN ('A')
                           ORDER BY `category` ASC";


$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
$topicId = $value['abstract_topic_id'];

$sqlAbstractSubmission              =    array();
$sqlAbstractSubmission['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                              WHERE  `status` IN ('A')
                           ORDER BY `category` ASC";


$resultAbstractSubmission = $mycms->sql_select($sqlAbstractSubmission);

$sqlAbstractPresentation             =    array();
$sqlAbstractPresentation['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_PRESENTATION_ . " 
                              WHERE `status` ='A'
                           ORDER BY `id` ASC";


$resultAbstractPresentation = $mycms->sql_select($sqlAbstractPresentation);


// abstract related work by weavers end

$sqlHeader    =   array();
$sqlHeader['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
	                        WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
$resultHeader = $mycms->sql_select($sqlHeader);
$rowHeader  = $resultHeader[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowHeader['header_image'];


$sqlFetchHotel      = array();
$sqlFetchHotel['QUERY'] = "SELECT * 
                                     FROM " . _DB_MASTER_HOTEL_ . "
                                    WHERE `status` =  ? ";

$sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
$resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);


//echo count($resultFetchHotel);

$countAcc = ($resultFetchHotel) ? '1' : '0';

// setTemplateStyleSheet();
// setTemplateBasicJS();
// backButtonOffJS();




$sqlFlyer    =   array();
$sqlFlyer['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE status='A' AND title='Profile Flyer' ";

$resultFlyer      = $mycms->sql_select($sqlFlyer);

$sqlVenue    =   array();
$sqlVenue['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE status='A' AND `title`='Venue' ";

$resultVenue  = $mycms->sql_select($sqlVenue);

$sqlProfilePic    =   array();
$sqlProfilePic['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE status='A' AND `title`='Profile Picture' ";

$resultProfilePic  = $mycms->sql_select($sqlProfilePic);
$profile_pic_src = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultProfilePic[0]['image'];


$sqlSidePanelicon             =    array();
$sqlSidePanelicon['QUERY']  = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
								   WHERE `id`!='' AND `purpose`='Profile Side Panel Icon' ORDER BY `id`";
$resultSidePanelicon        = $mycms->sql_select($sqlSidePanelicon);
$workshop_icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSidePanelicon[0]['icon'];
$accompanying_icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSidePanelicon[1]['icon'];
$banquet_icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSidePanelicon[2]['icon'];
$accomodation_icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSidePanelicon[3]['icon'];
$abstract_icon = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSidePanelicon[4]['icon'];
if ($rowUserDetails['isRegistration'] == 'N') {
    $disabled = "disabled='disabled' style='filter: blur(1px);'";
} else {
    $disabled = "";
}


$sql   =  array();
$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$result = $mycms->sql_select($sql);
$row         = $result[0];

$site_logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];
$mailer_logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['mailer_logo'];
$cutoffs             = fullCutoffArray();
$currentWorkshopCutoffId     = getWorkshopTariffCutoffId();
$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
?>
<body>
    <!-- 
    <div class="profile_left_menu">
        <div class="logo_wrap">
            <img src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/LOGO_0001_250526185928.png" alt="">
        </div>
        <ul>
            <li><a href="#" class="active"><i class="fal fa-window-alt"></i>Registration Overview</a></li>
            <li><a href="#"><?php user(); ?>Profile Settings</a></li>
            <li><a href="#"><?php credit(); ?>Payment & Invoice</a></li>
            <li><a href="#"><i class="fal fa-question-circle"></i>Contact Support</a></li>
        </ul>
        <p><a href="#"><?php logout(); ?>Log Out</a></p>
        <div id="touch-taget"></div>
    </div> -->
    <img src="<?=$cfg['OUTER_BG_IMG']?>" alt="" class="body_bk">

    <? include_once("header.php");?>
    <header>
        <div class="header_left">
            <div class="logo_wrap">
                <img src="<?= $site_logo ?>" alt="">
               
            </div>
        </div>
        <div class="header_right">
            
            <button type="button"><?php bell(); ?></button>
            <a href="<?= _BASE_URL_ ?>login.process.php?action=logout"><?php logout(); ?>Log Out</a>
        </div>
    </header>
    <div class="profile_body">
        <div class="profile_right_menu">
            <div class="profile_top">
                <div class="profile_top_left">
                    <h6>Dashboard</h6>
                    <h2>Manage Your Booking</h2>
                </div>
                <div class="profile_top_right">
                     <?php
                        $sqlFile = array();
                        $sqlFile['QUERY'] = "SELECT registration_confirm_file FROM " . _DB_USER_REGISTRATION_ . " WHERE id = ?";
                        $sqlFile['PARAM'][] = array('FILD' => 'id', 'DATA' => $delegateId, 'TYP' => 'i');
                        $resultFile = $mycms->sql_select($sqlFile);
                        $filePath = __DIR__ . "/uploads/registration_confirmation/" . $resultFile[0]['registration_confirm_file'];
                           
                         if (!empty($resultFile[0]['registration_confirm_file']) && file_exists($filePath)) {
                         ?>
                         <a href="<?php echo _BASE_URL_; ?>download_confirmation.php?file=<?php echo urlencode($resultFile[0]['registration_confirm_file']); ?>">
                            <i class="fal fa-download"></i> Download Confirmation
                        </a>
                         <!-- <a href="download_confirmation.php"><i class="fal fa-download"></i>Download Confirmation</a> -->
                         <!-- <a href="<?php echo _BASE_URL_; ?>uploads/registration_confirmation/<?php echo $resultFile[0]['registration_confirm_file']; ?>" download><i class="fal fa-download"></i>Download Confirmation</a> -->
                         <? 
                         }
                    ?>                
                </div>
            </div>
            <div class="profile_grid">
                <div class="span_grid span_4">
                    <div class="profile_grid">
                    <!-- <div class="span_grid span_6">
                        <div class="profile_detail profile_detaile_box">
                            <div class="profile_detail_left">
                                <div class="profile_detail_img">
                                    <img src="<?= $profile_pic_src ?>" alt="">
                                    <input type="file" id="profileimg" style="display: none;">
                                </div>
                                <div class="profile_detail_content">
                                    <h2><?= $rowUserDetails['user_full_name'] ?></h2>
                                    <h5><span><?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['REG_TYPE']?> |  <?= ($rowUserDetails['registration_payment_status']=='UNPAID' && $rowUserDetails['isRegistration']=='Y')?'<b style="color: #c70606e6">Processing</b>':( $rowUserDetails['isRegistration']!='Y'?'N/A':$rowUserDetails['user_registration_id']) ?></span> • <n><?= $rowUserDetails['user_email_id'] ?></n>
                                    </h5>
                                    <? if($rowUserDetails['registration_payment_status']=='UNPAID' && $rowUserDetails['isRegistration']=='Y'){
                                ?>
                                <h6><span class="badge_danger"><?php bill(); ?>Registration Pending</span><span class="badge_danger"><?php credit(); ?>Payment Unpaid</span></h6>
                                    <?
                                    }else{
                                    ?>
                                        <h6><span class="badge_success"><?php bill(); ?>Registration Confirmed</span><span class="badge_success"><?php credit(); ?>Paid</span></h6>
                                    <?
                                    }
                                    ?>
                                    </div>
                            </div>
                        
                            <i class="fal fa-shield-check"></i>
                        </div>
                    </div> -->
                 <div class="span_6">
                    <div class="profile_detail profile_detaile_box">
                        <div class="profile_detail_left">
                            <div class="profile_detail_img">
                                <img src="<?= $profile_pic_src ?>" alt="">
                                <input type="file" id="profileimg" style="display: none;">
                                <!-- <label for="profileimg"><?php edit(); ?></label> -->
                            </div>
                            <div class="profile_detail_content">
                                <h2><?= $rowUserDetails['user_full_name'] ?></h2>
                                <h5><span><?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['REG_TYPE']?> |  <?= ($rowUserDetails['registration_payment_status']=='UNPAID' && $rowUserDetails['isRegistration']=='Y')?'<b style="color: #c70606e6">Processing</b>':( $rowUserDetails['isRegistration']!='Y'?'--':$rowUserDetails['user_registration_id']) ?></span>
                                </h5>
                                                       
                                    <h6>
                                        <? if($rowUserDetails['registration_payment_status']=='UNPAID' && $rowUserDetails['isRegistration']=='Y' && $rowUserDetails['account_status']=='REGISTERED'){
                                        ?>
                                        <span class="badge_danger"><?php bill(); ?>Registration Pending</span><span class="badge_danger"><?php credit(); ?>Payment Unpaid</span>
                                            <?
                                            }else if($rowUserDetails['registration_payment_status']=='PAID' && $rowUserDetails['isRegistration']=='Y'){
                                            ?>
                                                <span class="badge_success"><?php bill(); ?>Registration Confirmed</span><span class="badge_success"><?php credit(); ?>Paid</span>
                                            <?
                                            }else if($rowUserDetails['isRegistration']=='N') {?>
                                            
                                             <span class="badge_danger"><?php bill(); ?>Not Yet Registered</span>
                                            <?
                                            }
                                            ?>         
                                            <!-- else -->
                                            <!-- if abstracted Submitted -->
                                             <?php
                                             if(($abstract_details)){
                                                ?>
                                                  <span class="badge_success"><?php abstracts() ?>Abstract Submitted</span>

                                                <?
                                             }else if($submissionDate >= $currentDate && $abstract_start_date <= $currentDate){
                                                ?>
                                                <span class="badge_danger"><?php abstracts() ?>Abstract Not Yet Submitted</span>

                                                <?
                                             }
                                             ?>
                                            <!-- if abstracted Submitted -->
                                            <!-- else -->
                                    </h6>
                                <ul class="profile_detail_list profile_grid">
                                    <li class="span_grid span_3">
                                        <span><?php email() ?> Email Id</span>
                                        <p><?= $rowUserDetails['user_email_id'] ?></p>
                                         <input type="hidden" name="user_email_id" id="user_email_id" value="<?=$rowUserDetails['user_email_id']?>">

                                    </li>
                                    <li class="span_grid span_3">
                                        <span><?php call() ?> Mobile Number</span>
                                        <p><?= $rowUserDetails['user_mobile_no'] ?></p>
                                    </li>
                                    <li class="span_grid span_6">
                                        <span><?php address() ?> Full Address</span>
                                        <p><?= $rowUserDetails['user_address'] ?></p>
                                    </li>
                                    <li class="span_grid span_3">
                                        <span># Unique Sequence</span>
                                        <p><?=strtoupper($rowUserDetails['user_unique_sequence'])?></p>
                                    </li>
                                </ul>
                                 <div class="profile_detail_right">
                                     <input type="hidden" name="abstractDelegateId" id="abstractDelegateId" value="<?=$delegateId?>">
                                     <? if($rowUserDetails['isRegistration']=='N' && $rowInfo['registration_disable']=='no') {?>
                                            <!-- if registration not done -->
                                    <a  href="javascript:void(null)" onclick="sendUrl()"><?php user(); ?>Register Now</a>
                                    <? }else if($rowUserDetails['isRegistration']=='N' && $rowInfo['registration_disable']=='yes'){ ?>
                                    <a style="background: #898989;!important"   href="#"><?php user(); ?>Register Now</a>

                                    <? } ?>
                                    <!-- if registration not done -->
                                    <!-- if unpaid -->
                                     <? if($rowUserDetails['registration_payment_status']=='UNPAID' && $rowUserDetails['isRegistration']=='Y' && $rowUserDetails['account_status']=='REGISTERED'){
                                        ?>
                                    <!-- <a href="javascript:void(null)"    onclick="sendUrlPayment()"><?php credit(); ?>Pay Now</a> -->
                                    <? } ?>
                                    <!-- if unpaid -->
                                    <!-- if abstracted Not Submitted -->
                                    <? if($submissionDate >= $currentDate && $abstract_start_date <= $currentDate){ ?>
                                      <a  href="javascript:void(null)"    onclick="sendUrlabstract()"><?php abstracts() ?>Submit Abstract</a>
                                    <?  } ?>
                                    <!-- if abstracted Not Submitted -->
                                    <a class="d-none" href="#"><?php printi(); ?>Print Badge</a>
                                </div>
                                <!-- <div class="profile_detail_right">
                                    <a href="#"><?php printi(); ?>Print Badge</a>
                                </div> -->
                            </div>

                        </div>


                        <i class="fal fa-shield-check"></i>
                    </div>
                </div>
              
                 
                <? if($rowUserDetails['isRegistration']=='N') {
                    $currentCutoffId  = getCutoffName(getTariffCutoffId());
                ?>
                <div class="span_6 workshop_detail profile_detaile_box">
                    <div class="profile_detaile_box_head">
                        <h3><span><?php clock(); ?></span><?=$currentCutoffId?> Registration Ends In</h3>
                    </div>
                    <div class="hotel_blank">
                        <div class="abstract_submit_deadline">
                            <div class="abstract_inner w-100">
                                <ul id="abstract_countdown">
                                    <li>
                                        <n class="p-0 border-0 bg-transparent"><span id="days">00</span></n><i>days</i>
                                    </li>

                                    <li>
                                        <n class="p-0 border-0 bg-transparent"><span id="hours">00</span></n><i>Hours</i>
                                    </li>

                                    <li>
                                        <n class="p-0 border-0 bg-transparent"><span id="minutes">00</span></n><i>Minutes</i>
                                    </li>

                                    <li>
                                        <n class="p-0 border-0 bg-transparent"><span id="seconds">00</span></n><i>Seconds</i>
                                    </li>
                                </ul>
                            </div>
                        </div>
                       <? if($rowInfo['registration_disable']=='no') {?>

                        <button   onclick="sendUrl()"><?php add(); ?>Register Now</button>
                        <? }else{
                            ?>
                             <button     class=""><?php add(); ?>Register Now</button>

                            <?
                        } ?>
                    </div>
                    <!-- else -->
                </div>
                <? } ?>
           
                    <!-- else -->
                    <?php
                    if((!$abstract_details) && ($submissionDate >= $currentDate) && ($abstract_start_date <= $currentDate)){
                    ?>
                    <div class="span_6 workshop_detail profile_detaile_box">
                        <div class="hotel_blank">
                            <span><?php abstracts() ?></span>
                            <p>Abstract Submission Deadline</p>
                            <div class="abstract_submit_deadline">
                                <div class="abstract_inner w-100">
                                    <ul id="abstract_countdown">
                                        <li>
                                            <n class="p-0 border-0 bg-transparent"><span id="dday">00</span></n><i>days</i>
                                        </li>

                                        <li>
                                            <n class="p-0 border-0 bg-transparent"><span id="dhour">00</span></n><i>Hours</i>
                                        </li>

                                        <li>
                                            <n class="p-0 border-0 bg-transparent"><span id="dmin">00</span></n><i>Minutes</i>
                                        </li>

                                        <li>
                                            <n class="p-0 border-0 bg-transparent"><span id="dsec">00</span></n><i>Seconds</i>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <button  onclick="sendUrlabstract()"><?php add(); ?>Submit Abstract</button>
                        </div>
                    </div>
                    <? } else if($abstract_details){
                        ?>
                        <div class="span_6 guest_detail profile_detaile_box">
                            <div class="profile_detaile_box_head">
                                <h3><span><?php abstracts() ?></span>Submitted Abstract</h3>
                                <!-- if guest added -->
                                <!-- <button class="popup_btn" data-tab="abstractdetails"><?php view(); ?>View</button> -->
                                <!-- if guest added -->
                            </div>
                            <!-- if abstract submitted -->
                            <div class="profile_detaile_box_body">
                                <ul>
                                    <?php
                                        foreach ($abstract_details as $key => $value) {
                                        
                                            $sqlAbstractTopic			  =	array();
                                            $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                                WHERE `id` = ?";

                                            $sqlAbstractTopic['PARAM'][]  = array('FILD' => 'id', 'DATA' =>  $value['abstract_topic_id'],  'TYP' => 's');
                                            //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'category', 'DATA' =>$abstract_topic_id,  'TYP' => 's');
                                            $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
                                        ?>
                                    <li><span><?php abstracts() ?></span>
                                        <p>Submission Code: <?= $value['abstract_submition_code'] ?><n>Title: <?= $value['abstract_title'] ?></n><n>Category: <?= getAbstractCategoryName(intval($value['abstract_cat'])) ?></n>
                                        </p>
                                        
                                         <!-- <button class="popup_btn" data-tab="abstractdetails"><?php view(); ?>View</button> -->
                                        <a href="#" class="popup_btn abstractView"  data-id="<?= $value['abstract_submition_code'] ?>" data-tab="abstractdetails"><?php view(); ?></a>
                                    </li>
                                    <? } ?>
                                </ul>
                                
                            </div>
                            <!-- if abstract submitted -->
                            
                            <!-- else -->
                        </div>
                    <? } ?>
                     <?php
                    // Edit page accommodation section - Auto-populates from existing booking data
             

                    // Fetch existing accommodation bookings for this user
                    $sqlFetchAccommodationBooking = array();
                    $sqlFetchAccommodationBooking['QUERY'] = "SELECT * FROM " . _DB_REQUEST_ACCOMMODATION_ . " 
                                                            WHERE user_id = ? AND status = 'A' 
                                                            ORDER BY id ASC";
                    $sqlFetchAccommodationBooking['PARAM'][] = array('FILD' => 'user_id', 'DATA' => $delegateId, 'TYP' => 's');
                    $resultBooking = $mycms->sql_select($sqlFetchAccommodationBooking);
                   
                
                    if (!empty($resultBooking)) {

                        ?>
                     <div class="span_6 accom_detail profile_detaile_box" >
                        <div class="profile_detaile_box_head">
                            <h3><span><?php hotel(); ?></span>Your Accommodation</h3>
                            <!-- if hotel booked -->
                            <!-- <button type="button" class="popup_btn" data-tab="addstay"><?php edit(); ?>Change Hotel</button> -->
                            <!-- if hotel booked -->
                        </div>
                        <!-- if hotel booked -->
                        <div class="profile_detaile_box_body hotel_box"  style="display: block;">
                            <ul class="profile_accm_list d-none">
                             <?
                              foreach ($resultBooking as $booking) {
                                $sqlFetchHotel = array();
                                $sqlFetchHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " WHERE `status` = ? AND `id` = '".$booking['hotel_id']."'";
                                $sqlFetchHotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                $resultFetchHotel = $mycms->sql_select($sqlFetchHotel); ?>
                                <li>
                                    <div class="profile_accm_list_left">
                                        <div class="hotel_owl owl-carousel owl-theme">
                                            <?php
                                            $sqlRoom = array();
                                            $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
                                                                WHERE `hotel_id` = ? AND status='A' AND purpose='slider' ORDER BY `id` ASC";
                                            $sqlRoom['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' =>$booking['hotel_id'], 'TYP' => 's');
                                            $querySlider = $mycms->sql_select($sqlRoom, false);
                                            if ($querySlider) {
                                                foreach ($querySlider as $row) {
                                                    $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
                                            ?>
                                            <div class="item">
                                                <img src="<?=$icon?>" alt="">
                                            </div>
                                            <? } } ?>
                                        </div>
                                    </div>
                                    <div class="profile_accm_list_right">
                                        <h5><?=$resultFetchHotel[0]['hotel_name']?></h5>
                                        <h6><?php star(); ?><?= $val['hotelRatings'] ?> Star</h6>
                                        <p><?php address(); ?><?=$resultFetchHotel[0]['hotel_address']?></p>
                                        <p><?php calendar(); ?><?=$booking['checkin_date']?> - <?=$booking['checkout_date']?></p>
                                        <? if($booking['booking_quantity']=='0.5'){ ?>
                                            <p><?php hotel(); ?>1 Sharing Room</p> 
                                        <? } else{?>
                                        <p><?php hotel(); ?><?=$booking['booking_quantity']?> Room</p>
                                        <? } ?>
                                        <h4><? if($booking['package_id']!='' && $booking['package_id']!=NULL && $booking['package_id']!=0){?><span class="badge_secondary"><?=getPackageNameById($booking['package_id'])?></span><? } ?>
                                        <? if($booking['payment_status']=='PAID'){?><span class="badge_success"><?php check(); ?>Booking Confirmed</span><? }else{
                                            ?> <span class="badge_success">Booking Pending</span>
                                      <?  } ?></h4>
                                    </div>
                                </li>
                                <? } ?>
                            </ul>
                            <div class="hotel_box_inner">
                                <?
                             
                                $booking = $resultBooking[0];
                                $sqlFetchHotel = array();
                                $sqlFetchHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " WHERE `status` = ? AND `id` = '".$booking['hotel_id']."'";
                                $sqlFetchHotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                $resultFetchHotel = $mycms->sql_select($sqlFetchHotel); ?>
                                <div class="hote_box_inner_top">
                                    <div class="hotel_box_left">
                                        <div class="hote_box_left_top">
                                            <t><i class="fa-solid fa-star"></i><?= $resultFetchHotel[0]['hotelRatings'] ?>-Star Luxury Hotel</t>
                                            <div class="hotel_owl owl-carousel owl-theme">
                                                 <?php
                                                    $sqlRoom = array();
                                                    $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
                                                                        WHERE `hotel_id` = ? AND status='A' AND purpose='slider' ORDER BY `id` ASC";
                                                    $sqlRoom['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' =>$booking['hotel_id'], 'TYP' => 's');
                                                    $querySlider = $mycms->sql_select($sqlRoom, false);
                                                    if ($querySlider) {
                                                        foreach ($querySlider as $row) {
                                                            $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
                                                    ?>
                                                    <div class="item">
                                                        <img src="<?=$icon?>" alt="">
                                                    </div>
                                                    <? } } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hotel_box_right">
                                        <h6><span><?= $resultFetchHotel[0]['hotelRatings'] ?>-Star Modern Business</span> . <n><i
                                                    class="fal fa-map-marker-alt"></i>2.1 km from Venue</n>
                                        </h6>
                                        <h4><?=$resultFetchHotel[0]['hotel_name']?></h4>
                                        <p><?=$resultFetchHotel[0]['hotel_address']?></p>
                                        <p class="accm_date"><i class="fal fa-calendar"></i><?=$booking['checkin_date']?> - <?=$booking['checkout_date']?></p>
                                        <?  if($booking['roomTypeId']=='0'){ 
                                         if($booking['booking_quantity']=='0.5' ){ ?>
                                            <p><?php hotel(); ?>1 Sharing Room</p> 
                                        <? } else{?>
                                        <p><?php hotel(); ?> <?=$booking['booking_quantity']?> Room</p>
                                        <? } 
                                        }?>
                                            <h5 class="prfl_accm_confirm">
                                        <? if($booking['payment_status']=='PAID'){?><span class="badge_success"><?php check(); ?>Booking Confirmed</span><? }else{
                                            ?> <span class="badge_secondary">Booking Pending</span>
                                      <?  } ?></h5>
                                        <ul class="hote_box_left_bottom">
                                           <?php
                                              $sqlAcc = array();
                                              $sqlAcc['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                                                WHERE `hotel_id` = '" . $resultFetchHotel[0]['id'] . "' AND status='A' AND purpose='aminity' ORDER BY `id` ASC";
                                              $queryAcc = $mycms->sql_select($sqlAcc, false);
                                              if ($queryAcc) {
                                                  foreach ($queryAcc as $keyqueryAcc => $aminity) {
                                                      $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $aminity['accessories_icon'];
                                              ?>
                                                      <li><? if($aminity['accessories_icon']!=''){?><img src="<?= $icon ?>" alt=""><? } ?><?= $aminity['accessories_name'] ?></li>
                                              <?php
                                                  }
                                              }
                                              ?>

                                        </ul>
                                    </div>
                                  </div>
                                  <? if ($booking['roomTypeId']>0 && $resultFetchHotel[0]['room_type_status'] == 'yes') {  ?>
                                    <ul class="hotel_room_ul m-0">
                                        <? foreach ($resultBooking as $roomBooked) { 
                                        $sqlRoom = array();
                                        $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                                            WHERE `hotel_id` = '" . $booking['hotel_id'] . "' AND `id` = '" . $roomBooked['roomTypeId'] . "'  AND status='A' AND purpose='room' ORDER BY `id` ASC";
                                        $queryRoom = $mycms->sql_select($sqlRoom, false);
                                        $rowRoom = $queryRoom[0];
                                        ?>
                                        <li>
                                            <div class="hotel_room_ul_top">
                                                 <?php
                                                    // Fetch all room details (size/bed/guest + inclusions) in ONE query
                                                    $sqlRoomDetails = [];
                                                    $sqlRoomDetails['QUERY'] = "
                                                        SELECT accessories_name, description, purpose
                                                        FROM `rcg_accommodation_room_accessories`
                                                        WHERE room_id = ?
                                                    ";
                                                    $sqlRoomDetails['PARAM'][] = ['DATA' => $roomBooked['roomTypeId'], 'TYP' => 's'];

                                                    $roomDetailsRows = $mycms->sql_select($sqlRoomDetails);

                                                    $roomAttrs  = [];
                                                    $inclusions = [];

                                                    foreach ($roomDetailsRows as $row) {
                                                        if ($row['purpose'] === 'inclusion') {
                                                            $inclusions[] = $row['description'];
                                                        } else {
                                                            // room_size, bed_size, guest_size
                                                            $roomAttrs[$row['accessories_name']] = $row['description'];
                                                        }
                                                    }
                                                    ?>
                                                <h5>
                                                    <? if($roomBooked['booking_quantity']=='0.5'){
                                                        $booking_quantity = '1 Sharing';
                                                    } else{
                                                     $booking_quantity =  $roomBooked['booking_quantity'];
                                                     } ?>
                                                     <n><?= htmlspecialchars($rowRoom['accessories_name']) ?></n><span>Room Occupacy - <?=$booking_quantity?></span>
                                                </h5>
                                               <p>
                                                  <n><i class="fal fa-arrows-h"></i><?= htmlspecialchars($roomAttrs['room_size'] ?? '') ?></n>
                                                  <n><i class="fal fa-bed-alt"></i><?= htmlspecialchars($roomAttrs['bed_size'] ?? '') ?></n>
                                                  <n><i class="fal fa-users"></i><?= htmlspecialchars($roomAttrs['guest_size'] ?? '') ?></n>
                                              </p>
                                            </div>
                                            <div class="hotel_room_content">
                                                <div class="hotel_room_content_img">
                                                    <t>Kolkata City Skyline View</t>
                                                    <?php $roomicon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $rowRoom['accessories_icon']; ?>
                                                  <img src="<?= $roomicon ?>" alt="">
                                                </div>
                                                <div class="hotel_room_content_right">
                                                    <?php if ($rowRoom['accessories_name'] != '') { ?>
                                                        <h5><?= htmlspecialchars($rowRoom['room_description']) ?></h5>
                                                    <?php } ?>
                                                    <div class="hotel_room_right">
                                                        <h6>Complimentary Room Inclusions</h6>
                                                        <p>
                                                            <?php if (!empty($inclusions)): ?>
                                                                <?php foreach ($inclusions as $inclusionText): ?>
                                                                    <span><i class="fal fa-check-circle"></i><?= htmlspecialchars($inclusionText) ?></span>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </li>
                                         <? } ?>
                                    </ul>
                                    <? } ?>
                                
                            </div>
                        </div>

                        <!-- if hotel booked -->
                        <!-- else -->
                        <!-- <div class="hotel_blank">
                            <span><?php hotel(); ?></span>
                            <p>Stay at the heart of the action with our partner hotels.</p>
                            <button type="button" class="popup_btn" data-tab="addstay" data-form="frmAddaccomodationfromProfile"><?php add(); ?>Book Hotel</button>
                        </div> -->
                        <!-- else -->
                    </div>
                    <? } else{ ?>
                    <div class="span_6 workshop_detail profile_detaile_box">
                        <div class="profile_detaile_box_head">
                            <h3><span><?php workshop(); ?></span>Accomodation</h3>
                         
                        </div>
                         <div class="hotel_blank">
                                    <span><?php workshop(); ?></span>
                                    <p>No Accomodation added yet.</p>
                                  <button type="button" class="popup_btn" data-tab="addstay" data-form="frmAddaccomodationfromProfile"><?php add(); ?>Book Hotel</button>
                                </div>
                         </div>
                   <? } ?>
                    <?php
                    $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                    if (count($workshopDetailsArray) > 0) {
                    ?>
                    <div class="span_6 workshop_detail profile_detaile_box d-none">
                        <div class="profile_detaile_box_head">
                            <h3><span><?php workshop(); ?></span>Workshops</h3>
                            <!-- if guest added -->
                            <!-- <button type="button" class="popup_btn" data-tab="addworkshop"><?php edit(); ?>Manage</button> -->
                            <!-- if guest added -->
                        </div>
                        <!-- if workshop added -->
                            <?php
                            if($workshopDetails) {
                                ?>
                        <div class="profile_detaile_box_body">
                            <ul>
                                
                                <!-- <li class="abstract-list-content" style="padding-right: 0;">
                                    <h4 class="abs-modal-submission">Workshop</h4> -->

                                    <?php
                                    
                                        $wrksp_Cnt = 0;
                                        $existingWorkShops = array();
                                        foreach ($workshopDetails as $key => $rowWorkshopDetails) {

                                            if ($rowWorkshopDetails && $rowWorkshopDetails['INVOICE']['status'] == 'A') {

                                                $existingWorkShops[] = $rowWorkshopDetails['ROW_DETAIL']['type'];
                                                $workshopStatus = true;
                                                $wrksp_Cnt++;

                                                if ($rowWorkshopDetails['INVOICE']['service_roundoff_price'] > 0) {
                                                    $workshopAmountDisp = $rowWorkshopDetails['INVOICE']['currency'] . ' ' . $rowWorkshopDetails['INVOICE']['service_roundoff_price'];
                                                } else {
                                                    $workshopAmountDisp = 'Included in package';
                                                }


                                                if (!empty($rowWorkshopDetails['ROW_DETAIL']['workshop_date'])) {
                                                    $workshop_date = '(' . $rowWorkshopDetails['ROW_DETAIL']['workshop_date'] . ')';
                                                } else {
                                                    $workshop_date = '';
                                                }

                                                if (!empty($rowWorkshopDetails['ROW_DETAIL'])) {

                                                    if ($rowWorkshopDetails['INVOICE']['id']) { ?>
                                                        <li><span><?php clock(); ?></span>
                                                            <p><?= $rowWorkshopDetails['REG_DETAIL'] ?><n><?= $workshop_date ?></n>
                                                            </p>
                                                            <!-- <a href="#"><?php close(); ?></a> -->
                                                        </li>
                                                        <!-- <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $rowWorkshopDetails['INVOICE']['id'] ?>" target="blank" class="btn" style="background:none">INVOICE</a> -->
                                                        <?php

                                                        if ($rowWorkshopDetails['INVOICE']['payment_status'] == 'UNPAID' && $rowWorkshopDetails['INVOICE']['invoice_mode'] == 'ONLINE') {
                                                        ?>


                                                            <!-- <a onclick="onlinePayNow('<?= $rowWorkshopDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" class="btn" style=" border-radius: 20px;  margin-right: 10px;  display: inline-block; padding: 5px 15px;">PAY NOW</a> -->

                                                <?php

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                <!-- </li> -->
                                <? if($workshopDetails[0]['ROW_DETAIL']['onlyWorkshop']!='Y'){?>
                                <button type="button" class="popup_btn" data-tab="addworkshop" data-form="frmAddWorkshopfromProfile"><?php add(); ?>Add More Workshop</button>
                                <? } ?>
                            </ul>
                        </div>
                        <?php
                        } 
                            else{

                            $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                            //echo count($workshopDetailsArray);
                            
                            if (count($workshopDetailsArray) > 0) {
                                ?>
                                <!-- <div class="hotel_blank">
                                    <span><?php workshop(); ?></span>
                                    <p>No workshops added yet.</p>
                                    <button type="button" class="popup_btn " data-tab="addworkshop" data-form="frmAddWorkshopfromProfile"><?php add(); ?>Add Workshop</button>
                                </div> -->
                                <!-- <br><a href="<?= _BASE_URL_ . "profile-add.php?section=3" ?>" <?= $disabled ?> class="btn">Add Workshop</a> -->
                        
                        <?
                        }
                            }
                        ?>
                        <!-- if workshop added -->
                        <!-- else -->
                        
                        <!-- else -->
                    </div>
                    <?php } ?>

                    <?php 
                    if ($accompany_tariff && $rowUserDetails['isRegistration']=='Y') {
                    ?>
                    <div class="span_6 guest_detail profile_detaile_box ">
                        <div class="profile_detaile_box_head">
                            <h3><span><?php duser(); ?></span>Accompany Management</h3>
                            <!-- if guest added -->
                            <!-- <button type="button" class="popup_btn" data-tab="addguest"><?php edit(); ?>Manage</button> -->
                            <!-- if guest added -->
                        </div>
                        <!-- if guest added -->
                        <?   if (sizeof($accompanyDtlsArr) > 0) { ?>
                        <div class="profile_detaile_box_body">  
                                <ul>
                            <?php
                            
                                foreach ($accompanyDtlsArr as $key => $accompanyFullDtls) {

                                $accompanyDtls        = $accompanyFullDtls['ROW_DETAIL'];

                                $accompanySlipDtls    = $accompanyFullDtls['SLIP_DETAILS'];
                                $accompanInvoiceDtls  = $accompanyFullDtls['INVOICE'];
                                $accompanyPaymentDtls = $accompanyFullDtls['SLIP_PAYMENT'];
                                $dataVal              = $accompanInvoiceDtls['currency'] . ' ' . $accompanInvoiceDtls['service_roundoff_price'];

                                //echo 'ID='. $accompanyDtls['id'];

                                $dinnerDetails  = $dinnerDtlsAccm[$accompanyDtls['id']];

                                // echo '<pre>'; print_r($accompanInvoiceDtls);
                            ?>
                            <li><span><?php duser(); ?></span>
                                <p>Accompany Name<n><?= strtoupper($accompanyDtls['user_full_name']) ?></n>
                                </p>
                                <!-- <a href="#"><?php delete(); ?></a> -->
                            </li>
                                <? } 
                            ?>
                            </ul>
                            <div class="hotel_blank ">

                                <button  type="button" class="popup_btn" data-tab="addguest" data-form="frmAddAccompanyfromProfile"><?php add(); ?>Add More Guest</button>
                            </div>
                            
                            
                        
                        </div>
                            <? }else{ ?> 
                            <div class="hotel_blank">
                                <span><?php duser(); ?></span>
                                <p>Bringing family? Add them to your registration.</p>
                                <button type="button" class="popup_btn" data-tab="addguest" data-form="frmAddAccompanyfromProfile"><?php add(); ?>Add Guest</button>
                            </div>
                            <? }  ?>
                        
                    </div>
                    <?php } ?>
                    
                    <?php
                    $dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
                    if ($dinnerTariffArray) {
                    ?>
                    <div class="span_6 guest_detail profile_detaile_box">
                        <div class="profile_detaile_box_head">
                            <h3><span><?php dinner(); ?></span>Gala Dinner</h3>
                            <!-- if guest added -->
                            <!-- <button type="button" class="popup_btn" data-tab="adddinner"><?php edit(); ?>Manage</button> -->
                            <!-- if guest added -->
                        </div>
                        <!-- if guest added -->
                        <?php
                        if (sizeof($dinnerDtls) > 0) {
                        ?>
                        <div class="profile_detaile_box_body">
                            <ul>
                                <?php


                                foreach ($dinnerDtls as $uId => $dinnerDetails) {
                                    $dinrCount++;
                                    if ($dinrCount % 2 == 1) {
                                        $dinrClass  = "gala_dinner_accompany";
                                        $dinrBG     = "#2393c3";
                                        $dinrInvBG  = "#27a5db";
                                    } else {
                                        $dinrClass  = "gala_dinner_workshop";
                                        $dinrBG     = "#27a5db";
                                        $dinrInvBG  = "#2393c3";
                                    }

                                    $dinrInvAmtDisp = 'Included in Package';
                                    $hasInvoice     = false;
                                    if ($dinnerDetails['INVOICE']['service_type'] == 'DELEGATE_DINNER_REQUEST') {
                                        $dinrInvAmtDisp = $dinnerDetails['INVOICE']['currency'] . ' ' . $dinnerDetails['INVOICE']['service_roundoff_price'];
                                        $hasInvoice     = true;
                                    }


                                ?>
                                <li><span><?php duser(); ?></span>
                                    <p>Dinner Name : <?= $dinnerDetails['dinner_classification_name'] ?><n><?= $dinnerDetails['dinnerDate'] ?></n>
                                    </p>
                                    <!-- <a href="#"><?php delete(); ?></a> -->
                                </li>
                                <? } ?>
                                <!-- <button type="button" class="popup_btn" data-tab="adddinner"><?php add(); ?>Add More Dinner</button> -->
                            </ul>
                        </div>
                        <!-- if guest added -->
                            <? } else {
                            ?>
                            
                        <!-- else -->
                        <div class="hotel_blank d-none">
                            <span><?php dinner(); ?></span>
                            <p>No Dinner added yet.</p>
                            <button type="button" class="popup_btn" data-tab="adddinner"><?php add(); ?>Add Dinner</button>
                        </div>
                            <?
                            }
                            ?>
                        <!-- else -->
                    </div>
                    <? } ?>
                </div>
            </div>
                <div class="span_grid span_2">
                    <div class="profile_bottom">
                        <div class="profile_bottom_box">
                            <h4><?php address(); ?>Venue</h4>
                            <p><?=$rowInfo['company_conf_venue']?></p>
                            <a href="<?= $cfg['WEATHER_CITY'] ?>" target="_blank">Navigation Guide</a>
                        </div>
                        <div class="profile_bottom_box">
                            <h4><?php calendar(); ?>Schedule</h4>
                            <p>Conference starts on <?= $formatted?></p>
                            <!-- <a href="#">Download Itinerary</a> -->
                            <a href="#">Coming Soon</a>

                        </div>
                        <div class="profile_bottom_box">
                            <h4><?php pending(); ?>Inclusions</h4>
                             <?php
                                $sqlMailClassification     =    array();

                                $sqlMailClassification['QUERY']        = "SELECT RC.*, R.id, R.registration_classification_id
                                                        FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " RC 
                                                        INNER JOIN " . _DB_USER_REGISTRATION_ . " R ON
                                                        RC.id = R.registration_classification_id
                                                    WHERE RC.status = ? 
                                                            AND R.status = ?	
                                                        AND  R.id = ? ";

                                $sqlMailClassification['PARAM'][]   = array('FILD' => 'RC.status',   'DATA' => 'A',                             'TYP' => 's');
                                $sqlMailClassification['PARAM'][]   = array('FILD' => 'R.status',   'DATA' => 'A',                             'TYP' => 's');
                                $sqlMailClassification['PARAM'][]   = array('FILD' => 'R.id',       'DATA' => $rowUserDetails['id'],   'TYP' => 's');
                                $resMailClassification    = $mycms->sql_select($sqlMailClassification);
                                $rowaMailClassification = $resMailClassification[0];
                                $selected_inclusion_lunch_date = json_decode($rowaMailClassification['inclusion_lunch_date']);
                                $selected_inclusion_dinner_date = json_decode($rowaMailClassification['inclusion_conference_kit_date']);
                                $lunch = "";
                                $i = 0;
                                foreach ($selected_inclusion_lunch_date as $key => $date) {
                                    if ($i == 0) {
                                        $lunch .= " " . date('d/m/Y', strtotime($date));
                                    } else {
                                        $lunch .= ", " . date('d/m/Y', strtotime($date));
                                    }
                                    $i++;
                                }
                                $dinner = "";
                                $i = 0;
                                foreach ($selected_inclusion_dinner_date as $key => $date) {
                                    if ($i == 0) {
                                        $dinner .= " " . date('d/m/Y', strtotime($date));
                                    } else {
                                        $dinner .= ", " . date('d/m/Y', strtotime($date));
                                    }
                                    $i++;
                                }
                                $sql     =    array();
                                $sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                                    WHERE `id`!='' AND `purpose`='Mailer' AND status IN ('A', 'I')";
                                $result      = $mycms->sql_select($sql);
                                // echo '<pre>'; print_r($result);
                                ?>
                            <ul>
                                <?php if ($rowaMailClassification['inclusion_sci_hall'] == 'Y') { ?>
                                    <li><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'] ?>" alt="" /><?= $result[0]['title'] ?></li>
                                <?php }
                                if ($rowaMailClassification['inclusion_exb_area'] == 'Y') { ?>
                                    <li><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['icon'] ?>" alt="" /><?= $result[1]['title'] ?></li>
                                <?php }
                                if ($rowaMailClassification['inclusion_tea_coffee'] == 'Y') { ?>
                                    <li><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[3]['icon'] ?>" alt="" /><?= $result[3]['title'] ?></li>
                                <?php }
                                if ($rowaMailClassification['inclusion_conference_kit'] == 'Y') { ?>
                                    <li><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[2]['icon'] ?>" alt="" /><?= $result[2]['title'] ?></li>
                                <?php }
                                if (!empty($selected_inclusion_lunch_date)) { ?>
                                    <li><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[4]['icon'] ?>" alt="" /><?= 'Lunch on ' . $lunch  ?></li>
                                <?php }
                                if (!empty($selected_inclusion_dinner_date)) { ?>
                                    <li><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[5]['icon'] ?>" alt="" /><?= 'Dinner on ' . $dinner  ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile_left_menu">
            <div class="help_detail profile_detaile_box">
                <div class="profile_detaile_box_head">
                    <h3><span><i class="fal fa-question-circle"></i></span>Payments & Invoice</h3>
                </div>

                <div class="profile_detaile_box_body">
                     <?php
                        $sqlFetchInvoice                = getRegistrationInvoiceCancelInvoiceDetails("",$rowUserDetails['id'], "");
                        // echo '<pre>'; print_r($rowUserDetails['id']);																
                        $resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
                        $totalAmountAll = 0;
                        $invoiceCounter                 = 0;
                        if ($resultFetchInvoice) {
                            //print_r($resultFetchInvoice);
                            foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
                              if($rowFetchInvoice['service_basic_price']>0){
                                $showTheRecord 		= true;
                                $invoiceCounter++;

                                $slip = getInvoice($rowFetchInvoice['slip_id']);
                                //print_r($slip);
                                $returnArray    = discountAmount($rowFetchInvoice['id']);
                                $percentage     = $returnArray['PERCENTAGE'];

                                $totalAmount    = $returnArray['TOTAL_AMOUNT'];

                                $discountAmount = $returnArray['DISCOUNT'];
                                $thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
                                $type			 = "";
                                if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
                                    $type = "CONFERENCE REGISTRATION ";
                                }
                                if ($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
                                    $workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
                                    $type =  strtoupper(getWorkshopName($workShopDetails['workshop_id'])) . " REGISTRATION - " . $thisUserDetails['user_full_name'];
                                    if ($workShopDetails['showInInvoices'] != 'Y') {
                                        $showTheRecord 		= false;
                                    }
                                }
                                if ($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
                                    $thisUserAccompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
                                    $type = "ACCOMPANY REGISTRATION - " . $thisUserAccompanyDetails['user_full_name'];
                                }
                                if ($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
                                    if ($rowFetchUser['registration_classification_id'] == 3) {
                                        $type = $cfg['RESIDENTIAL_NAME'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 7) {
                                        $type = $cfg['RESIDENTIAL_NAME_IN_2N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 8) {
                                        $type = $cfg['RESIDENTIAL_NAME_IN_3N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 9) {
                                        $type = $cfg['RESIDENTIAL_NAME_SH_2N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 10) {
                                        $type = $cfg['RESIDENTIAL_NAME_SH_3N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 11) {
                                        $type = $cfg['RESIDENTIAL_NAME_IN_2N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 12) {
                                        $type = $cfg['RESIDENTIAL_NAME_IN_3N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 13) {
                                        $type = $cfg['RESIDENTIAL_NAME_SH_2N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 14) {
                                        $type = $cfg['RESIDENTIAL_NAME_SH_3N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 15) {
                                        $type = $cfg['RESIDENTIAL_NAME_IN_2N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 16) {
                                        $type = $cfg['RESIDENTIAL_NAME_IN_3N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 17) {
                                        $type = $cfg['RESIDENTIAL_NAME_SH_2N'] . " - " . $thisUserDetails['user_full_name'];
                                    } else if ($rowFetchUser['registration_classification_id'] == 18) {
                                        $type = $cfg['RESIDENTIAL_NAME_SH_3N'] . " - " . $thisUserDetails['user_full_name'];
                                    }
                                }
                                if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
                                    $type = "ACCOMMODATION BOOKING";
                                }
                                if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
                                    $type = $cfg['BANQUET_DINNER_NAME'] . " - " . getInvoiceTypeStringForMail($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "DINNER");
                                }
                                if ($rowFetchInvoice['status'] == 'C') {
                                    $styleColor = 'background: #FFCCCC;';
                                } else {
                                    $styleColor = 'background: rgb(204, 229, 204);';
                                }

                                if ($rowFetchInvoice['has_gst'] == 'Y' && $rowFetchInvoice['invoice_mode'] == 'ONLINE') {
                                    $invRowSpan = 2;
                                } else {
                                    $invRowSpan = 1;
                                }

                                if ($showTheRecord) {
                        ?>
                    <div class="help_top">
                        <h6><i class="fal fa-question-circle"></i> <?= $type ?></h6>
                        <h6>Invoice No.</h6>
                        <h6> <?= $rowFetchInvoice['invoice_number'] ?></h6>
                        <p>Invoice Date: <?= setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D") ?></p>
                        <h4><a>Invoice Amount: <?= $rowFetchInvoice['currency'] ?> <?= number_format($totalAmount, 2) ?> </a><a href="pdf.download.invoice.php?user_id=<?= $rowUserDetails['id']?>&invoice_id=<?= $rowFetchInvoice['id'] ?>" target="_blank" title="Invoice Download" class="badge_primary icon_hover action-transparent"><i class="fal fa-download"></i> DownLoad Invoice</a></h4>
                    </div>
                    <?
                     } 
                            } 
                            }
                            }
                            ?>
                </div>
            </div>
            <div class="help_detail profile_detaile_box">
                <div class="profile_detaile_box_head">
                    <h3><span><i class="fal fa-question-circle"></i></span>Help & Support</h3>
                </div>
                <?php
                $sqlFooterIcon  = array();
                $sqlFooterIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                        WHERE `status`='A' AND purpose='Footer' order by id ";
                $resultFooterIcon = $mycms->sql_select($sqlFooterIcon);
                 ?>
                <div class="profile_detaile_box_body">
                    <div class="help_top">
                        <h6><i class="fal fa-question-circle"></i>Need Assistance?</h6>
                        <p>If you're having trouble managing your registration, our support desk is active 10 AM to 6 PM IST.</p>
                        <h4>
                            <?      
                            foreach ($resultFooterIcon as $k => $val) {
                                 if ($val['title'] == 'Email') {
                                    $href = 'mailto:' . $val['page_link'];
                                ?>
                                 <a href="<?= $href?>">Email Help : <?=$val['page_link']?></a>
                                <?
                                 }if ($val['title'] == 'Phone') {
                                     $href = 'tel:+:' . $val['page_link'];
                                    ?>
                                <a href="<?= $href?>">Talk to us : <?=$val['page_link']?></a>
                                    <?
                            }
                            }
                            ?>
                            </h4>
                    </div>
                    <!-- <div class="help_bottom badge_danger">
                        <h6><?php pending(); ?>Registration Cancellation</h6>
                        <p><?php echo $rowInfo['cancellation_page_info']; ?></p>
                    </div> -->
                    <div class="help_bottom badge_danger">
                        <h6><?php pending(); ?>Registration Cancellation</h6>
                        <?php
                            $cancellationHtml = $rowInfo['cancellation_page_info'];

                            libxml_use_internal_errors(true); // prevent HTML warnings

                            $dom = new DOMDocument();
                            $dom->loadHTML($cancellationHtml);

                            $rows = $dom->getElementsByTagName('tr');

                            $policies = [];

                            foreach ($rows as $index => $row) {

                                // Skip first row (header row: Date / Deduction)
                                if ($index == 0) continue;

                                $cells = $row->getElementsByTagName('td');

                                if ($cells->length >= 2) {
                                    $date = trim($cells->item(0)->textContent);
                                    $deduction = trim($cells->item(1)->textContent);

                                    $policies[] = $date . " " . $deduction;
                                }
                            }
                            ?>
                       <ul>
                            <?php foreach($policies as $policy) { ?>
                                <li><?= htmlspecialchars($policy); ?></li>
                            <?php } ?>
                        </ul>
                        <h4><a class="popup_btn" style="cursor:pointer;" data-tab="cancelation">View Full Cancellation</a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<div class="popup_wrap">
    <div class="popup_inner">
        
    <form class="profile_pop_form workshop-form" name="frmAddWorkshopfromProfile" id="frmAddWorkshopfromProfile" action="registration.process.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="act" value="add_workshop">
        <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentWorkshopCutoffId ?>" cutoffid="1">
        <input type="hidden" name="delegateClasfId" value="<?= $rowUserDetails['registration_classification_id'] ?>">
        <input type="hidden" id="registrationRequest" name="registrationRequest" value="GENERAL">
        <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />
                    <input type="hidden" name="delegate_id" value="<?= $delegateId?>">

        <div class="popup_body registration_right_wrap" id="addworkshop">
            <div class="registration_right_head">
                <span><?=$cfg['WORKSHOP_TITLE']?></span>
                <button type="button" class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body" id="section3">
                <div class="registration_right_body_head">
                    <div class="registration_right_body_head_left">
                        <h4>Add Workshops</h4>
                        <h5>Hands-on training sessions (Optional).</h5>
                    </div>
                    <div class="registration_right_body_head_right">
                        <a class="selected-count">0 Selected</a>
                    </div>
                </div>
                <div class="registration_right_body_tab">
                    <div class="hotel_link_owl owl-carousel owl-theme">
                        <?
                        if ($currentCutoffId > 0) {

                            $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                            $workshopDetailsArray   = getAllWorkshopTariffs($currentWorkshopCutoffId);
                            $workshopCountArr       = totalWorkshopCountReport();

                            //echo '<pre>'; print_r($workshopCountArr);

                            $comboTariffArray   = getAllRegistrationComboTariffs($currentCutoffId);

                            $workshopRegChoices = array();

                            //echo '<pre>'; print_r($comboTariffArray);
                            $workshoDefinedDate = '';
                            foreach ($workshopDetailsArray as $keyWorkshopclsf => $rowWorkshopclsf) {
                                foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {
                                    //echo '<pre>'; print_r($rowRegClasf['WORKSHOP_DATE']);
                                    $workshopRegChoices[$rowRegClasf['WORKSHOP_TYPE']][$keyWorkshopclsf][$keyRegClasf] = $rowRegClasf;
                                    $workshoDefinedDate = $rowRegClasf['WORKSHOP_DATE'];
                                }
                            }

                            foreach ($conferenceTariffArray as $key => $registrationDetailsVal) {
                                // echo '<pre>'; print_r($registrationDetailsVal);
                                $classificationType = getRegClsfType($key);
                                if ($classificationType == 'DELEGATE') {


                                    $getSeatlimitToClassificationID = getSeatlimitToClassificationID($registrationDetailsVal['REG_CLASSIFICATION_ID']);

                                    if ($getSeatlimitToClassificationID < 0) {
                                        $disableClass = "disabled";
                                        $spanCss = 'style="cursor:not-allowed;"';
                                    } else {
                                        $disableClass = "";
                                        $spanCss = '';
                                    }

                                    $icon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $registrationDetailsVal['ICON'];
                        }
                            }
                        }
                        $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                        $workshopCountArr      = totalWorkshopCountReport();

                        //echo '<pre>'; print_r($workshopDetailsArray);

                        $comboTariffArray   = getAllRegistrationComboTariffs($currentCutoffId);

                        $workshopRegChoices = array();


                        $workshoDefinedDate = '';
                        foreach ($workshopDetailsArray as $keyWorkshopclsf => $rowWorkshopclsf) {
                            foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {

                            $workshopRegChoices[$rowRegClasf['WORKSHOP_TYPE']][$keyWorkshopclsf][$keyRegClasf] = $rowRegClasf;
                            $workshoDefinedDate = $rowRegClasf['WORKSHOP_DATE'];
                            }
                        }
                        // echo "<pre>";
                        // print_r($workshopDetailsArray);
                        $sql_cal  =  array();
                        $sql_cal['QUERY']  =  "SELECT DISTINCT `workshop_date`
                                                FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
                                                WHERE status = 'A' 
                                                ORDER BY `display` ASC";
                        $res_cal = $mycms->sql_select($sql_cal);
                        foreach ($res_cal as $key => $rowsl) {
                        ?>
                            <button type="button" class="active wrkshp_tab_btn" id="wrkshp_tab_btn" data-tab="<?= $rowsl['workshop_date'] ?>"><?= displayDateFormat($rowsl['workshop_date']) ?></button>
                        <?
                        }
                        ?>
                    </div>
                </div>
                <div class="registration_right_body_content" id="workShops">
                      <?
                foreach ($res_cal as $key => $rowsl) {
                  $sql_cal1  =  array();
                  $sql_cal1['QUERY']  =  "SELECT * 
                                              FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
                                              WHERE workshop_date = '" . $rowsl['workshop_date'] . "' 
                                              AND  status = 'A' 
                                              ";
                  $res_cal1 = $mycms->sql_select($sql_cal1);

                ?>

                  <div class="wrkshp_box" id="<?= $rowsl['workshop_date'] ?>" style="display: block;">

                    <div class="cus_check_wrap g2">
                      <?
                      $registration_classification_id = isset($rowUserDetails['registration_classification_id']) ? (int)$rowUserDetails['registration_classification_id'] : '';

                      foreach ($res_cal1 as $key_cal1 => $rowslcal1) {
                       $workshopLimit = getWorkshopClassificationSeatLimit($rowslcal1['id']);
                      //  echo "<pre>";
                      //  print_r($workshopLimit);
                        $isDisabled = ($workshopLimit < 1); 
                        $sqlTarrif = array();
                        $sqlTarrif['QUERY'] = "SELECT *
                                          FROM " . _DB_TARIFF_WORKSHOP_ . " 
                                          WHERE workshop_id = '" . $rowslcal1['id'] . "'
                                          AND tariff_cutoff_id = '" . $currentWorkshopCutoffId . "'
                                          AND registration_classification_id ='" . $registration_classification_id . "'";

                        $resTarrif      = $mycms->sql_select($sqlTarrif);

                        if ($resTarrif && $resTarrif[0]['inr_amount'] > 0) {
                          $rowTarrif    = $resTarrif[0];

                          $amountInr = $rowTarrif['inr_amount'];
                          $amountUsd = $rowTarrif['usd_amount'];
                          $amountDis = $registrationDetailsVal['CURRENCY'] . ' ' . number_format($amountInr);

                        } else {
                          $amountInr = '0.00';
                          $amountUsd =  '0.00';
                          $amountDis = 'Included in Registration';
                        }
                     if($resTarrif[0]['OnlyWorkshopAllow']=='N'){
                       $isDisabled = true;
                      }
                      ?>
                        <label class="cus_check workshop_select">
                          <!-- <input type="checkbox" name="regimood" checked> -->
                          <input type="checkbox" name="workshop_id[]" value="<?= $rowslcal1['id'] ?>" data-date="<?= $rowslcal1['workshop_date'] ?>" id="workshop_id_<?= $key_cal1 . '_' . $keyRegClasf ?>" data-type="<?= $rowslcal1['type'] ?>"  <?= $style ?> workshopName="<?= $rowslcal1['sequence_by'] ?>" operationMode="workshopId" amount="<?= $amountInr ?>" invoiceTitle="Workshop" invoiceName="<?= $rowslcal1['classification_title'] ?>" registrationClassfId="<?= $keyRegClasf ?>" workshopCount="<?= $workshopCount ?>" icon="<?= $workshopIcon ?>" reg="workshop">

                          <span class="checkmark">
                            <n>
                              <?php workshop(); ?>
                              <g><?= $rowslcal1['classification_title'] ?><ii><?= $amountDis ?></ii>
                              </g>
                              <iii></iii>
                            </n>
                            <h>
                              <l><?php address(); ?> <?= $rowslcal1['venue'] ?></l>
                              <l><?php calendar(); ?><?= displayDateFormat($rowslcal1['workshop_date']) ?></l>
                            </h>
                          </span>
                        </label>
                      <?
                      }
                      ?>

                    </div>
                  </div>
                <?php
                }
                ?>

              </div>
            </div>
             <style>
                .disabled-label {
                pointer-events: none;
                opacity: 0.5;
                cursor: not-allowed;
                }
                </style>
            <div class="registration_right_bottom">
                <button type="button" name="previous" class="previous action-button-previous popup_close">Cancel</button>
                <button  type="button" formPay="frmAddWorkshopfromProfile"  class="next action-button next"><?php save(); ?> Save</button>
            </div>
            
        </div>
        <div class="popup_body registration_right_wrap checkout-main-wrap profile_review" id="checkout-main-wrap-workshop" >
            <?php
            $offline_payments = json_decode($cfg['PAYMENT.METHOD']);

            $sql_qr = array();
            $sql_qr['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                                                        WHERE `id`!='' AND `title`IN ('QR Code','Online Payment Logo')";
            $result = $mycms->sql_select($sql_qr);
            $onlinePaymentLogo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
            $QR_code = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['image'];
            // echo $cfg['PAYMENT.METHOD'];
            ?>
             <div class="registration_right_head">
                <span> Review</span>
                <button type="button" class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body">
              <div class="registration_right_body_head">
                <div class="registration_right_body_head_left">
                  <h4>Order Summary</h4>
                  <!-- <h5>Invoice #NC25-2836</h5> -->
                </div>
              </div>
              <div class="registration_right_body_content">

                <div class="review_wrap">
                  <div class="review_left">
                    <ul use="totalAmountTable">
                      <li use=rowCloneable style="display:none;">
                        <span><?php user() ?></span>
                        <n use="invTitle">
                          <h use="invName"></h>
                        </n>
                        <k use="amount"></k>
                      </li>

                          <li use="subtotalRow">
                        <div class="w-100">
                          <p class="frm-head d-flex justify-content-between align-items-center">Subtotal<k use="subtotalAmount">₹ 0.00</k>
                          </p>
                          <? 
                          if($cfg['GST.FLAG']!=3){
                            ?>
                          <p class="frm-head d-flex justify-content-between align-items-center gstcharge">GST (18%)<k use="totalGstAmount">₹ 0.00</k>
                          </p>
                          <?
                          }
                          ?>
                           <p class="frm-head d-flex justify-content-between align-items-center internetcharge">Internet Handling Charges<k use="internetAmount">₹ 0.00</k>
                          </p>
                        </div>
                      </li>
                      <li>
                        <div class="w-100" use="totalAmount">
                          <h5 class="frm-head d-flex justify-content-between align-items-center mb-0">Total Payable<k use="totalAmountpay" style="color: var(--sky);">₹ 0.00</k>
                          </h5>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="review_right">
                  <div class="review_tab">
                    <div class="hotel_link_owl owl-carousel owl-theme">
                     <input type="hidden" name="registrationMode" id="registrationMode">

                      <?php if (in_array("Neft", $offline_payments)) { ?>
                      <button   type="button" class="active"
                        onclick=" var f=$(this).closest('form');
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Neft]').prop('checked',true).trigger('click');
                        ">
                        <i class="fal fa-building"></i> NEFT
                      </button>
                      <?php } ?>

                      <?php if (in_array("Upi", $offline_payments)) { ?>
                      <button type="button"
                        onclick=" var f=$(this).closest('form');
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Upi]').attr('act','Upi').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> UPI
                      </button>
                      <?php } ?>

                      <?php if (in_array("Card", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> <?if ($rowInfo['paymentGateway']=='razorpay') {?>Razorpay<?}else{?>
                          Card
                        <? }  ?>
                      </button>
                      <?php } ?>

                      <?php if (in_array("Cheque/DD", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').hide();$('#DD').show();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Cheque]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> DD
                      </button>
                      <?php } ?>

                      <?php if (in_array("Cash", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').show();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Cash]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Cash
                      </button>
                      <?php } ?>

                      <!-- Hidden radios (logic only – REQUIRED) -->
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Neft" hidden checked>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Upi" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" hidden>

                    </div>
                   </div>
                    <div class="review_right_box" id="banktransfer" style="display: block;">
                      <ul>
                        <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li  class="for-neft-rtgs-only">
                          <h6 class="d-flex justify-content-between align-items-center">Bank Details</h6>
                          <div>
                           <p class="frm-head text_dark d-flex justify-content-between align-items-start" style="flex-direction: column;gap: 2px;border-bottom: 1px dashed var(--border);padding-bottom: 6px;">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p>
                            <!-- <p class="frm-head text_dark d-flex justify-content-between align-items-center">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p> -->
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Bank<span class="text-white"><?= $cfg['INVOICE_BANKNAME'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Account<span class="text-white"><?= $cfg['INVOICE_BANKACNO'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center mb-0">IFSC<span class="text-white"><?= $cfg['INVOICE_BANKIFSC'] ?></span></p>
                          </div>
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Upi", $offline_payments)) {
                          ?>
                                <li class="text-center for-upi-only" style="display: none;">
                                  <img src="<?= $QR_code ?>" alt="">
                                  <h6 class="d-flex justify-content-center align-items-center">Scan QR</h6>
                                </li>
                          <?php } ?>
                            <style>
                      #qrPopupOverlay {
                          display: none;
                          position: fixed;
                          top: 0; left: 0;
                          width: 100%;
                          height: 100%;
                          background: rgba(0,0,0,0.7);
                          justify-content: center;
                          align-items: center;
                          z-index: 9999;
                      }
                      #qrPopupOverlay img {
                          max-width: 40%;
                          max-height: 40%;
                          border: 2px solid #fff;
                          box-shadow: 0 0 10px #fff;
                      }
                      #qrPopupOverlay .closePopup {
                          position: absolute;
                          top: 20px;
                          right: 30px;
                          font-size: 30px;
                          color: #fff;
                          cursor: pointer;
                      }
                      </style>
                        <div id="qrPopupOverlay">
                        <span class="closePopup">&times;</span>
                        <img src="" alt="QR Code">
                    </div>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="neft_bank_name" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                         <?php
                          if (in_array("Upi", $offline_payments)) {
                          ?>
                        <li class="for-upi-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UPI Transaction No.</h6>
                          <input class="for-upi-only" type="text" class="form-control mandatory utrnft" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li class="for-neft-rtgs-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UTR Number</h6>
                          <input type="text" class="form-control mandatory utrnft" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                        <!-- <li>
                          <span id="neft_file_name" style="display:none;"></span>
                          <button type="button" id="neft_remove_btn" class="remove-file" style="display:none;">&times;</button>
                       <input style="display:none;" type="file" accept="image/*,application/pdf" name="neft_document" id="neft_document" class="mandatory" style="display:none" validate="Please upload a image">
                          <label for="neft_document" class="file-label">Upload Payment Receipt</label>
                        </li> -->
                        <li>
                          <span id="neft_file_name" style="display:none;" class="upload_name" style="">download (1).png</span>
                          <button type="button" style="display:none;"  id="neft_remove_btn" class="remove-file upload_delet" style=""><i class="fal fa-trash-alt"></i></button>
                          <input style="display:none;" type="file" accept="image/*,application/pdf" name="neft_document" id="neft_document" class="mandatory" validate="Please upload a image" data-gtm-form-interact-field-id="9">
                          <label for="neft_document" class="file-label" style="display: block;">Upload Payment Receipt</label>
                      </li>

                      </ul>
                    </div>
                    
                    <div class="review_right_box" id="DD">
                      <ul>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">DD No.</h6>
                          <input type="number" class="form-control mandatory" name="cheque_number" id="cheque_number" validate="Please enter cheque/DD number" placeholder="Enter DD No." type="number" maxlength="6" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;">
                        </li>
                      </ul>
                    </div>
                    <div class="review_right_box" id="cash">
                      <ul>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">
                        </li>
                        <!-- <li>
                              <span id="cash_file_name" style="display:none;"></span>
                              <button type="button" id="cash_remove_btn" class="remove-file" style="display:none;">&times;</button>
                          <input type="file" accept="image/*,application/pdf" name="cash_document" class="mandatory" id="cash_document" style="display:none" validate="Please upload a image">
                          <label for="cash_document">Upload Payment Receipt</label>

                        </li> -->
                         <li>
                          <span id="cash_file_name" style="display:none;" class="upload_name" style="">download (1).png</span>
                          <button type="button" style="display:none;"  id="cash_remove_btn" class="remove-file upload_delet" style=""><i class="fal fa-trash-alt"></i></button>
                          <input style="display:none;" type="file" accept="image/*,application/pdf" name="cash_document" id="cash_document" class="mandatory" validate="Please upload a image" data-gtm-form-interact-field-id="9">
                          <label for="cash_document" class="file-label" style="display: block;">Upload Payment Receipt</label>
                      </li>
                      </ul>
                    </div>
                    <div class="review_right_box" id="Cards">
                        
                      <ul>
                         <li>
                            <ol>
                                <li><?=$rowInfo['card_info']?></li>
                            </ol>
                        </li>
                        <li>
                         
                          <!-- <h6 class="d-flex justify-content-between align-items-center">Accepted Cards</h6> -->
                          <p>
                            <img src="<?= $onlinePaymentLogo ?>" style="width: 100%;    object-fit: contain;height: auto;background: transparent;filter: brightness(16.5); margin_bottom:0; padding-bottom:0;">
                          </p>
                        </li>
                   
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
              <button type="submit" name="submit" id="confirmPayment" class="submit action-button">Confirm<?php check(); ?></button>
            </div>
        </div>      
        </form>
        <form  class="profile_pop_form accomodation-form" name="frmAddaccomodationfromProfile" id="frmAddaccomodationfromProfile" action="registration.process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="actInput" name="act" value="add_accommodationfrom_profile" />
            <input type="hidden" name="abstractDelegateId" value="<?= $delegateId?>" />
            <input type="hidden" name="delegate_id" value="<?= $delegateId?>">
            <input type="hidden" name="registration_accompany_cutoff" id="registration_accompany_cutoff"  value="<?= $currentCutoffId ?>">
            <input type="hidden" name="registration_request" id="registration_request_edit" value="GENERAL" />
            <input type="hidden" name="userREGtype" id="userREGtype" value="GENERAL" />
            <input type="hidden" name="registrationMode" id="registrationMode_accom">

        <div class="popup_body registration_right_wrap" id="addstay">
            <div class="registration_right_head">
                <span><?= $cfg['ACCOMODATION_TITLE'] ?></span><button type="button" class="popup_close"><?php close(); ?></button>
            </div>
            <?
              $sqlFetchHotel = array();
              $sqlFetchHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " WHERE `status` = ?";
              $sqlFetchHotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
              $resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
              $hotel_count = count($resultFetchHotel) - 1;
              ?>
              
               <div class="registration_right_body">
                  <div class="registration_right_body_head">
                      <div class="registration_right_body_head_left">
                          <!-- <h4>Choose your accommodation</h4> -->
                          <!-- <h5>Official partner hotels with exclusive rates.</h5> -->
                      </div>
                      <!-- <div class="registration_right_body_head_right">
                          <a class="text_danger text_danger_clear" style="cursor: pointer;">Clear Choise</a>
                      </div> -->
                  </div>
                   <div class="registration_right_body_tab">
                    <div id="hotelDateStore" style="display:none;"></div>
                        <div class="hotel_link_owl owl-carousel owl-theme">

                  <?php
                        if (count($resultFetchHotel)) {
                            foreach ($resultFetchHotel as $k => $val) {
                                $sqlValidHotel = array();
                                $sqlValidHotel['QUERY'] = "SELECT 1 FROM " . _DB_TARIFF_ACCOMMODATION_ . "
                                                          WHERE status = ? AND tariff_cutoff_id = ? AND hotel_id = ? AND inr_amount > 0 LIMIT 1";
                                $sqlValidHotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                $sqlValidHotel['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $currentCutoffId, 'TYP' => 's');
                                $sqlValidHotel['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                $hotelHasValidAmount = $mycms->sql_select($sqlValidHotel);
                              
                                if (empty($hotelHasValidAmount)) {
                                    continue;
                                }
                        ?>
                                <button type="button" class="active hotel_tab_btn hotel_tab_btn_hotel" data-tab="<?= $val['id'] ?>" data-notes="<?= htmlspecialchars($val['hotel_notes'] ?? '', ENT_QUOTES) ?>"><?= $val['hotel_name'] ?><span><i class="fa-solid fa-star"></i><?=$val['hotelRatings']?></span></button>
                        <?php
                            }
                        }
                        ?>
                      </div>
                  </div>
                  <div
                      class="registration_right_body_head registration_right_body_sub_head accommodation_body_head">
                      <!-- <div class="registration_right_body_head_left">
                          <span><?php hotel() ?></span>
                          <div>
                              <h4>Accommodation<n>Optional</n></h4>
                              <h5>Official partner hotels with exclusive delegate rates & complimentary venue
                                  transfers.</h5>
                          </div>
                      </div> -->
                      <div class="registration_right_body_head_right">
                          <div class="hotel_check form_grid g_3" id="accoOp">
                                <?
                                if (isset($_POST['hotelIdDate'])) {
                                $hotelIdDate = $_POST['hotelIdDate'];
                                $dates = array();
                                $dCount = 0;
                                $packageCheckDate = array();
                                $packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
                                                                WHERE `hotel_id` = ? AND `status` = ? ORDER BY check_in_date";
                                $packageCheckDate['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotelIdDate, 'TYP' => 's');
                                $packageCheckDate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                $resCheckIns = $mycms->sql_select($packageCheckDate);

                                foreach ($resCheckIns as $key => $rowCheckIn) {
                                    $packageCheckoutDate = array();
                                    $packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY, ?, `check_out_date`) AS dayDiff
                                                                    FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
                                                                    WHERE `hotel_id` = ? AND `status` = ? AND `check_out_date` > ?
                                                                    ORDER BY check_out_date";
                                    $packageCheckoutDate['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $rowCheckIn['check_in_date'], 'TYP' => 's');
                                    $packageCheckoutDate['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotelIdDate, 'TYP' => 's');
                                    $packageCheckoutDate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                    $packageCheckoutDate['PARAM'][] = array('FILD' => 'check_out_date', 'DATA' => $rowCheckIn['check_in_date'], 'TYP' => 's');

                                    $resCheckOut = $mycms->sql_select($packageCheckoutDate);
                                    foreach ($resCheckOut as $key => $rowCheckOut) {
                                        $dates[$dCount]['CHECKIN'] = $rowCheckIn['check_in_date'];
                                        $dates[$dCount]['CHECKINID'] = $rowCheckIn['id'];
                                        $dates[$dCount]['CHECKOUT'] = $rowCheckOut['check_out_date'];
                                        $dates[$dCount]['CHECKOUTID'] = $rowCheckOut['id'];
                                        $dates[$dCount]['DAYDIFF'] = $rowCheckOut['dayDiff'];
                                        $dCount++;
                                    }
                                }

                                $tempCheckIN  = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));
                                $tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));

                                // ✅ NEW: check the hotel's dateFix flag
                                $sqlHotelDateFix = array();
                                $sqlHotelDateFix['QUERY'] = "SELECT `dateFix` FROM " . _DB_MASTER_HOTEL_ . " WHERE `id` = ?";
                                $sqlHotelDateFix['PARAM'][] = array('FILD' => 'id', 'DATA' => $hotelIdDate, 'TYP' => 's');
                                $resHotelDateFix = $mycms->sql_select($sqlHotelDateFix);
                                $hotelDateFix = isset($resHotelDateFix[0]['dateFix']) ? $resHotelDateFix[0]['dateFix'] : 'N';

                                if ($hotelDateFix === 'Y') {
                                    // Keep only the FIRST (earliest) check-in date
                                    if (!empty($tempCheckIN)) {
                                        $tempCheckIN = array_slice($tempCheckIN, 0, 1, true);
                                    }
                                    // Keep only the LAST (latest) check-out date
                                    if (!empty($tempCheckOUT)) {
                                        $tempCheckOUT = array_slice($tempCheckOUT, -1, 1, true);
                                    }
                                }
                            }
                            ?>
                              <div class="frm_grp span_1">
                                  <p class="frm-head">Check In<span class="required-field">*</span></p>
                                  <select id="accomodation_package_checkin_id_<?= $hotelIdDate ?>" 
                                          name="accomodation_package_checkin_id[<?= $hotelIdDate ?>]" 
                                          onchange="get_checkin_val(this.value, <?= $hotelIdDate ?>)"
                                          class="date-select">
                                      <option value="">Select Check In Date</option>
                                      <?php foreach ($tempCheckIN as $key => $value) { 
                                          $checkInVal = $key . "/" . $value;
                                      ?>
                                          <option value="<?= $checkInVal ?>"><?= date('d M Y', strtotime($value)) ?></option>
                                      <?php } ?>
                                  </select>
                              </div>
                              <div class="frm_grp span_1">
                                  <p class="frm-head">Check Out<span class="required-field">*</span></p>
                                  <select id="accomodation_package_checkout_id_<?= $hotelIdDate ?>" 
                                          name="accomodation_package_checkout_id[<?= $hotelIdDate ?>]" 
                                          onchange="get_checkout_val(this.value, <?= $hotelIdDate ?>)"
                                          class="date-select">
                                      <option value="">Select Check Out Date</option>
                                      <?php foreach ($tempCheckOUT as $key => $value) { 
                                          $checkOutVal = $key . "/" . $value;
                                      ?>
                                          <option value="<?= $checkOutVal ?>"><?= date('d M Y', strtotime($value)) ?></option>
                                      <?php } ?>
                                  </select>
                              </div>
                              <!-- <div class="frm_grp span_1">
                                  <p class="frm-head">Room Quantity</p>
                                  <div class="cus_check_wrap">
                                      <label class="cus_check stay_select">
                                          <input type="radio" name="regimood">
                                          <span class="checkmark">
                                              <n>1</n>
                                          </span>
                                      </label>
                                      <label class="cus_check stay_select">
                                          <input type="radio" name="regimood">
                                          <span class="checkmark">
                                              <n>2</n>
                                          </span>
                                      </label>
                                  </div>
                              </div> -->
                          </div>
                      </div>
                  </div>

                 
                  <div class="registration_right_body_content accommodation_body_content">
                    <input type="hidden" name="hotel_id" id="hotel_id">
                      <input type="hidden" name="hotel_select_acco_id" id="hotel_select_acco_id">
                      <input type="hidden" name="accommodation_room" id="accommodation_room">
                      
                      <!-- Loading Overlay -->
                      <div id="availabilityLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
                          <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                              <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                              <p style="margin-top: 10px;">Checking availability...</p>
                          </div>
                      </div>
                        <?php
                      if (count($resultFetchHotel)) {
                          foreach ($resultFetchHotel as $k => $val) {
                              $delegateId = $_REQUEST['abstractDelegateId'];
                              $getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);
                              $totalCount = 3 - $getAccommodationMaxRoom;

                              $sql_Count_hotel = array();
                              $sql_Count_hotel['QUERY'] = "SELECT COUNT(*) AS countdata FROM " . _DB_REQUEST_ACCOMMODATION_ . "
                                                          WHERE `status` = ? AND `hotel_id` = ? ORDER BY `id` ASC";
                              $sql_Count_hotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                              $sql_Count_hotel['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                              $row_Count_hotel = $mycms->sql_select($sql_Count_hotel);
                              $countSeatLimit = $row_Count_hotel[0]['countdata'];

                              $sql_package = array();
                              $sql_package['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                      WHERE `status` = ? AND `hotel_id` = ? ORDER BY `id` ASC";
                              $sql_package['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                              $sql_package['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                              $row_package = $mycms->sql_select($sql_package);

                              $sql_hotel = array();
                              $sql_hotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . "
                                                    WHERE `status` = ? AND `id` = ? ORDER BY `id` ASC";
                              $sql_hotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                              $sql_hotel['PARAM'][] = array('FILD' => 'id', 'DATA' => $val['id'], 'TYP' => 's');
                              $row_hotel = $mycms->sql_select($sql_hotel);
                              
                              $hotel_seat_limit = $row_hotel[0]['seat_limit'];
                              $presentSeatLimit = $hotel_seat_limit - $countSeatLimit;
                      ?>
                      <div class="hotel_box" id="<?= $val['id'] ?>" style="display: block;">
                          <div class="hotel_box_inner">
                              <div class="hote_box_inner_top">
                                  <div class="hotel_box_left">
                                      <div class="hote_box_left_top">
                                          <t><i class="fa-solid fa-star"></i><?= $val['hotelRatings'] ?>-Star Luxury Hotel</t>
                                          <div class="hotel_owl owl-carousel owl-theme">
                                                <?php
                                                  $sqlRoom = array();
                                                  $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
                                                                      WHERE `hotel_id` = ? AND status='A' AND purpose='slider' ORDER BY `id` ASC";
                                                  $sqlRoom['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                  $querySlider = $mycms->sql_select($sqlRoom, false);
                                                  if ($querySlider) {
                                                      foreach ($querySlider as $row) {
                                                          $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
                                                  ?>
                                                          <div class="item">
                                                              <img src="<?= $icon ?>" alt="Hotel Accessory">
                                                          </div>
                                                  <?php
                                                      }
                                                  }
                                                  ?>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="hotel_box_right">
                                      <h6><span><?= $val['hotelRatings'] ?>-Star Modern Business</span> . <n><i class="fal fa-map-marker-alt"></i><?= $val['distance_from_venue'] ?> km from Venue</n></h6>
                                      <h4><?= $val['hotel_name'] ?></h4>
                                      <p><?= $val['hotel_address'] ?></p>
                                      <ul class="hote_box_left_bottom">
                                        <?php
                                              $sqlAcc = array();
                                              $sqlAcc['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                                                WHERE `hotel_id` = '" . $val['id'] . "' AND status='A' AND purpose='aminity' ORDER BY `id` ASC";
                                              $queryAcc = $mycms->sql_select($sqlAcc, false);
                                              if ($queryAcc) {
                                                  foreach ($queryAcc as $keyqueryAcc => $aminity) {
                                                      $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $aminity['accessories_icon'];
                                              ?>
                                                      <li><? if($aminity['accessories_icon']!=''){?><img src="<?= $icon ?>" alt=""><? } ?><?= $aminity['accessories_name'] ?></li>
                                              <?php
                                                  }
                                              }
                                              ?>

                                      </ul>
                                  </div>
                              </div>

                              <div class="hotel_box_inner_bottom">
                                  <?php
                                      $room_type_status = array();
                                      $room_type_status['QUERY'] = "SELECT `room_type_status` FROM " . _DB_MASTER_HOTEL_ . "
                                                                  WHERE `status` = ? AND `id` = ? ORDER BY `id` ASC";
                                      $room_type_status['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                      $room_type_status['PARAM'][] = array('FILD' => 'id', 'DATA' => $val['id'], 'TYP' => 's');
                                      $row_hotel_status = $mycms->sql_select($room_type_status);
                                      
                                      $sqlRoom = array();
                                      $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                                          WHERE `hotel_id` = '" . $val['id'] . "' AND status='A' AND purpose='room' ORDER BY `id` ASC";
                                      $queryRoom = $mycms->sql_select($sqlRoom, false);
                                      $categoryLabel = '';
                                      $categoryCount = 0;

                                      if ($row_hotel_status[0]['room_type_status'] == 'yes' && !empty($queryRoom)) {
                                          $categoryCount = count($queryRoom);
                                          $categoryLabel = 'Room';
                                      } elseif ($row_hotel_status[0]['room_type_status'] == 'no' && !empty($row_package)) {
                                          $categoryCount = count($row_package);
                                          $categoryLabel = 'Package';
                                      }
                                  ?>
                                  <?php if ($categoryCount > 0) { ?>
                                  <div class="hote_box_inner_bottom_top">
                                      <p>
                                          <n>Available <?= $categoryLabel ?> Categories (<?= $categoryCount ?>)</n>
                                          <!-- <g>All room rates include breakfast, Wi-Fi, and shuttle to venue</g> -->
                                      </p>
                                      <!-- <span>Guaranteed Delegate Rates</span> -->
                                  </div>
                                  <?php } ?>
                                  <ul class="hotel_room_ul">
                                    <?php
                                   
                                          
                                          if ($queryRoom && $row_hotel_status[0]['room_type_status'] == 'yes') {
                                              foreach ($queryRoom as $k => $rowRoom) {
                                                  $hasValidPackage = false;
                                                  $sqlFetchPack = array();
                                                  $sqlFetchPack['QUERY'] = "SELECT `package_name`, `id` FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                          WHERE `status` = ? AND `hotel_id` = ?";
                                                  $sqlFetchPack['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                  $sqlFetchPack['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                  $resultFetchPack = $mycms->sql_select($sqlFetchPack);
                                                  
                                                  foreach ($resultFetchPack as $pkg) {
                                                      $sqlCheckAmount = array();
                                                      $sqlCheckAmount['QUERY'] = "SELECT inr_amount FROM " . _DB_TARIFF_ACCOMMODATION_ . "
                                                                                WHERE status = ? AND tariff_cutoff_id = ? AND hotel_id = ?
                                                                                AND roomTypeId = ? AND package_id = ? AND inr_amount > 0 LIMIT 1";
                                                      $sqlCheckAmount['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                      $sqlCheckAmount['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $currentCutoffId, 'TYP' => 's');
                                                      $sqlCheckAmount['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                      $sqlCheckAmount['PARAM'][] = array('FILD' => 'roomTypeId', 'DATA' => $rowRoom['id'], 'TYP' => 's');
                                                      $sqlCheckAmount['PARAM'][] = array('FILD' => 'package_id', 'DATA' => $pkg['id'], 'TYP' => 's');
                                                      $checkAmount = $mycms->sql_select($sqlCheckAmount);
                                                      
                                                      if (!empty($checkAmount)) {
                                                          $hasValidPackage = true;
                                                          break;
                                                      }
                                                  }
                                          ?>
                                      <li>
                                          <?php
                                          // Fetch all room details (size/bed/guest + inclusions) in ONE query
                                          $sqlRoomDetails = [];
                                          $sqlRoomDetails['QUERY'] = "
                                              SELECT accessories_name, description, purpose
                                              FROM `rcg_accommodation_room_accessories`
                                              WHERE room_id = ?
                                          ";
                                          $sqlRoomDetails['PARAM'][] = ['DATA' => $rowRoom['id'], 'TYP' => 's'];

                                          $roomDetailsRows = $mycms->sql_select($sqlRoomDetails);

                                          $roomAttrs  = [];
                                          $inclusions = [];

                                          foreach ($roomDetailsRows as $row) {
                                              if ($row['purpose'] === 'inclusion') {
                                                  $inclusions[] = $row['description'];
                                              } else {
                                                  // room_size, bed_size, guest_size
                                                  $roomAttrs[$row['accessories_name']] = $row['description'];
                                              }
                                          }
                                          ?>
                                          <div class="hotel_room_ul_top">
                                              <h5>
                                                  <n><?= htmlspecialchars($rowRoom['accessories_name']) ?></n>
                                                  <span><i class="fal fa-check"></i>Selected Choice</span>
                                              </h5>
                                              <p>
                                                  <n><i class="fal fa-arrows-h"></i><?= htmlspecialchars($roomAttrs['room_size'] ?? '') ?></n>
                                                  <n><i class="fal fa-bed-alt"></i><?= htmlspecialchars($roomAttrs['bed_size'] ?? '') ?></n>
                                                  <n><i class="fal fa-users"></i><?= htmlspecialchars($roomAttrs['guest_size'] ?? '') ?></n>
                                              </p>
                                          </div>
                                          <div class="hotel_room_content">
                                              <div class="hotel_room_content_img">
                                                  <t>Kolkata City Skyline View</t>
                                                  <?php $roomicon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $rowRoom['accessories_icon']; ?>
                                                  <img src="<?= $roomicon ?>" alt="">
                                              </div>
                                              <div class="hotel_room_content_right">
                                                  <?php if ($rowRoom['accessories_name'] != '') { ?>
                                                      <h5><?= htmlspecialchars($rowRoom['room_description']) ?></h5>
                                                  <?php } ?>
                                                  <div class="hotel_room_right">
                                                      <h6>Complimentary Room Inclusions</h6>
                                                      <p>
                                                          <?php if (!empty($inclusions)): ?>
                                                              <?php foreach ($inclusions as $inclusionText): ?>
                                                                  <span><i class="fal fa-check-circle"></i><?= htmlspecialchars($inclusionText) ?></span>
                                                              <?php endforeach; ?>
                                                          <?php endif; ?>
                                                      </p>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="hotel_room_content_bottom">
                                              <h5>
                                                  <n><i class="fal fa-users"></i>Select Occupancy & Rate Option
                                                  </n>
                                                  <span>Calculated for <i><span class="night-Count" data-hotel-id="<?= $val['id'] ?>">1</span> Night(s)</i></span>
                                              </h5>
                                              <div class="cus_check_wrap g2">
                                                   <?php if ($resultFetchPack) {
                                                      foreach ($resultFetchPack as $k => $row) {
                                                          $sqlPackageCheckoutDate1 = array();
                                                          $sqlPackageCheckoutDate1['QUERY'] = "SELECT * FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation
                                                                                            WHERE status = ? AND tariff_cutoff_id = ? AND hotel_id = ?
                                                                                            AND roomTypeId = ? AND package_id = ?";
                                                          $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                          $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $currentCutoffId, 'TYP' => 's');
                                                          $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                          $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'roomTypeId', 'DATA' => $rowRoom['id'], 'TYP' => 's');
                                                          $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'package_id', 'DATA' => $row['id'], 'TYP' => 's');
                                                          $resPackageCheckoutDate1 = $mycms->sql_select($sqlPackageCheckoutDate1);
                                                          
                                                          if ($resPackageCheckoutDate1[0]['inr_amount'] <= 0) {
                                                              continue;
                                                          }
                                                          $invoiceTitle = $row['package_name'] . "-" . $rowRoom['accessories_name'];
                                                  ?>
                                                  <label class="cus_check stay_select">
                                                      <input type="checkbox" name="package_id[]" data-hotel-id="<?= $val['id'] ?>" data-max-limit="<?= $val['individualroomLimit'] ?>"  data-hotel-name="<?= htmlspecialchars($val['hotel_name']) ?>"" 
                                                                            value="<?= $resPackageCheckoutDate1[0]['id'] ?>" invoiceTitle="Accomodation" 
                                                                            data-room-name="<?= $rowRoom['accessories_name'] ?>" 
                                                                            invoiceName="<?= $invoiceTitle ?>" amount="<?= $resPackageCheckoutDate1[0]['inr_amount'] ?>" 
                                                                            data-base-amount="<?= $resPackageCheckoutDate1[0]['inr_amount'] ?>" 
                                                                            hotel_id="<?= $val['id'] ?>" hotel_select_acco_id="<?= $val['id'] ?>" 
                                                                            accommodation_room="<?= $rowRoom['id'] ?>" data-package-name="<?= $row['package_name'] ?>">
                                                      <span class="checkmark">
                                                          <n><b><?= $row['package_name'] ?></l></b>
                                                              <j>Total: <span class="package-price">₹ <?= $resPackageCheckoutDate1[0]['inr_amount'] ?? 0.00 ?></span>(<span class="night-Count" data-hotel-id="<?= $val['id'] ?>">1</span> NT)</j>
                                                          </n>
                                                          <h><b><?= $resPackageCheckoutDate1[0]['inr_amount'] ?? 0.00 ?></b>
                                                              <j>/ night</j>
                                                          </h>
                                                      </span>
                                                  </label>
                                                  <?php
                                                        }
                                                    }
                                                    ?>
                                              </div>
                                          </div>
                                      </li>
                                      <?php
                                              }
                                          } else if ($row_hotel_status[0]['room_type_status'] == 'no' && $row_package) {
                                          ?>
                                         <li>
                                          <div class="hotel_room_ul_top d-none">
                                              <h5>
                                                  <n>Premium King / Twin Room</n>
                                                  <span><i class="fal fa-check"></i>Selected Choice</span>
                                              </h5>
                                              <p>
                                                  <n><i class="fal fa-arrows-h"></i>480 sq.ft</n>
                                                  <n><i class="fal fa-bed-alt"></i>1 King Bed</n>
                                                  <n><i class="fal fa-users"></i>Max 2 Guests</n>
                                              </p>
                                          </div>
                                          <div class="hotel_room_content d-none">
                                              <div class="hotel_room_content_img">
                                                  <t>Kolkata City Skyline View</t>
                                                  <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop"
                                                      alt="">
                                              </div>
                                              <div class="hotel_room_content_right">
                                                  <h5>Upper floor room with exclusive Regency Club Lounge
                                                      privileges, complimentary evening refreshments, and express
                                                      check-in.</h5>
                                                  <div class="hotel_room_right">
                                                      <h6>Complimentary Room Inclusions</h6>
                                                      <p>
                                                          <span><i class="fal fa-check-circle"></i>Exclusive Club
                                                              Lounge Access</span>
                                                          <span><i class="fal fa-check-circle"></i>Complimentary
                                                              Evening Hors d-Oeuvres &
                                                              Beverages</span>
                                                          <span><i class="fal fa-check-circle"></i>Buffet
                                                              Breakfast at La Cucina</span>
                                                          <span><i class="fal fa-check-circle"></i>Late Check-out
                                                              until 3 PM</span>
                                                      </p>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="hotel_room_content_bottom">
                                              <h5>
                                                  <n><i class="fal fa-users"></i>Select Occupancy & Rate Option
                                                  </n>
                                                  <span>Calculated for <i><span class="night-Count" data-hotel-id="<?= $val['id'] ?>">1</span> Night(s)</i></span>
                                              </h5>
                                              <div class="cus_check_wrap g2">
                                                   <?php
                                                      $roomId = 0;
                                                      $sqlFetchPack = array();
                                                      $sqlFetchPack['QUERY'] = "SELECT `package_name`, `id` FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                              WHERE `status` = ? AND `hotel_id` = ?";
                                                      $sqlFetchPack['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                      $sqlFetchPack['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                      $resultFetchPack = $mycms->sql_select($sqlFetchPack);
                                                      
                                                      if ($resultFetchPack) {
                                                          foreach ($resultFetchPack as $k => $row) {
                                                              $sqlPackageCheckoutDate1 = array();
                                                              $sqlPackageCheckoutDate1['QUERY'] = "SELECT * FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation
                                                                                                WHERE status = ? AND tariff_cutoff_id = ? AND hotel_id = ?
                                                                                                AND roomTypeId = ? AND package_id = ?";
                                                              $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                              $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $currentCutoffId, 'TYP' => 's');
                                                              $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                              $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'roomTypeId', 'DATA' => '0', 'TYP' => 's');
                                                              $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'package_id', 'DATA' => $row['id'], 'TYP' => 's');
                                                              $resPackageCheckoutDate1 = $mycms->sql_select($sqlPackageCheckoutDate1);
                                                              
                                                              if ($resPackageCheckoutDate1[0]['inr_amount'] <= 0) {
                                                                  continue;
                                                              }
                                                              $invoiceTitle = $row['package_name'];
                                                      ?>
                                                  <label class="cus_check stay_select">
                                                      <input type="checkbox" name="package_id[]" data-hotel-id="<?= $val['id'] ?>" data-max-limit="<?= $val['individualroomLimit'] ?>"  data-is-hotel-level="true" data-hotel-name="<?= htmlspecialchars($val['hotel_name']) ?>" 
                                                                        value="<?= $resPackageCheckoutDate1[0]['id'] ?>" invoiceTitle="Accomodation" 
                                                                        invoiceName="<?= $invoiceTitle ?>" amount="<?= $resPackageCheckoutDate1[0]['inr_amount'] ?>" 
                                                                        data-base-amount="<?= $resPackageCheckoutDate1[0]['inr_amount'] ?>" hotel_id="<?= $val['id'] ?>" 
                                                                        hotel_select_acco_id="<?= $val['id'] ?>" accommodation_room="0" 
                                                                        data-package-name="<?= htmlspecialchars($row['package_name']) ?>">
                                                      <span class="checkmark">
                                                          <n><b><?= $row['package_name'] ?></l></b>
                                                              <j>Total: <span class="package-price">₹ <?= $resPackageCheckoutDate1[0]['inr_amount'] ?? 0.00 ?></span>(<span class="night-Count" data-hotel-id="<?= $val['id'] ?>">1</span> NT)</j>
                                                          </n>
                                                          <h><b><?= $resPackageCheckoutDate1[0]['inr_amount'] ?? 0.00 ?></b>
                                                              <j>/ night</j>
                                                          </h>
                                                      </span>
                                                  </label>
                                                  <?php
                                                          }
                                                      }
                                                      ?>
                                              </div>
                                          </div>
                                      </li>
                                      <?php } else if ($row_hotel_status[0]['room_type_status'] == 'no' && empty($row_package)) { ?>
                                       <li class="d-none">
                                          
                                          <div class="hotel_room_content_bottom">
                                              <h5>
                                                  <n><i class="fal fa-users"></i>Select Occupancy & Rate Option
                                                  </n>
                                                  <span>Calculated for <i><span class="night-Count" data-hotel-id="<?= $val['id'] ?>">1</span>Night(s)</i></span>
                                              </h5>
                                              <div class="cus_check_wrap g2">
                                                  <?php
                                                      $roomId = 0;
                                                      $sqlPackageCheckoutDate1 = array();
                                                      $sqlPackageCheckoutDate1['QUERY'] = "SELECT * FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation
                                                                                        WHERE status = ? AND tariff_cutoff_id = ? AND hotel_id = ?
                                                                                        AND roomTypeId = ? AND package_id = ?";
                                                      $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                      $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $currentCutoffId, 'TYP' => 's');
                                                      $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 's');
                                                      $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'roomTypeId', 'DATA' => '0', 'TYP' => 's');
                                                      $sqlPackageCheckoutDate1['PARAM'][] = array('FILD' => 'package_id', 'DATA' => '0', 'TYP' => 's');
                                                      $resPackageCheckoutDate1 = $mycms->sql_select($sqlPackageCheckoutDate1);
                                                      
                                                      if ($resPackageCheckoutDate1[0]['inr_amount'] > 0) {
                                                          $invoiceTitle = $val['hotel_name'];
                                                      ?>
                                                          <label class="cus_check stay_select">
                                                             <input type="checkbox" name="package_id[]" data-max-limit="<?= $val['individualroomLimit'] ?>"  data-hotel-id="<?= $val['id'] ?>" data-is-hotel-level="true" 
                                                                data-auto-select="true" data-hotel-name="<?= htmlspecialchars($val['hotel_name']) ?>" 
                                                                value="<?= $resPackageCheckoutDate1[0]['id'] ?>" invoiceTitle="Accomodation" 
                                                                invoiceName="<?= $invoiceTitle ?>" amount="<?= $resPackageCheckoutDate1[0]['inr_amount'] ?>" 
                                                                data-base-amount="<?= $resPackageCheckoutDate1[0]['inr_amount'] ?>" hotel_id="<?= $val['id'] ?>" 
                                                                hotel_select_acco_id="<?= $val['id'] ?>" accommodation_room="0">
                                                              <span class="checkmark">
                                                                  <n><?= $val['hotel_name'] ?></n>
                                                                  <h class="package-price">₹ <?= $resPackageCheckoutDate1[0]['inr_amount'] ?? 0.00 ?></h>
                                                              </span>
                                                          </label>
                                                      <?php } ?>
                                                 
                                              </div>
                                          </div>
                                      </li>
                                      <?php } ?>
                                  </ul>
                              </div>
                          </div>
                      </div>
                     <?php
                          }
                      }
                      ?>
                  </div>
                  <div class="hote_box_inner_bottom_top mt-3">
                      <p>
                         <n>Your Accommodation Choices</n> 

                          <!-- <n>Selected Accommodation Choices (<span id="selectedAccoCount">0</span>)</n>  -->
                      </p>
                  </div>
                  <ul class="accomdationprice_total" ></ul>
                  <div class="accomdation_total"  id="hotelNotesBox">
                      <i class="fal fa-info-circle"></i><span id="hotelNotesText">Check-in time is 14:00 hrs and check-out is 12:00 hrs. Complimentary AC shuttle service between official hotels and Biswa Bangla Convention Centre will run daily during conference hours.</span>
                  </div>
             </div>
            <div class="registration_right_bottom">
                <button type="button" name="previous" class="previous action-button-previous popup_close">Cancel</button>
                <button type="button" name="next" class="next action-button"><?php save(); ?> Save</button>
           
            </div>
        </div>
        <div class="popup_body registration_right_wrap checkout-main-wrap profile_review" id="checkout-main-wrap-accommodation" style="display:none;">
            <?php
            $offline_payments = json_decode($cfg['PAYMENT.METHOD']);

            $sql_qr = array();
            $sql_qr['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                                WHERE `id`!='' AND `title` IN ('QR Code','Online Payment Logo')";
            $result = $mycms->sql_select($sql_qr);
            $onlinePaymentLogo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
            $QR_code           = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['image'];
            ?>
            <div class="registration_right_head">
                <span>Review</span>
                <button type="button" class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body">
                <div class="registration_right_body_head">
                    <div class="registration_right_body_head_left">
                        <h4>Order Summary</h4>
                    </div>
                </div>
                <div class="registration_right_body_content">
                    <div class="review_wrap">
                        <div class="review_left">
                            <ul use="totalAmountTable">
                            <li use=rowCloneable style="display:none;">
                                <span><?php user() ?></span>
                                <n use="invTitle">
                                <h use="invName"></h>
                                </n>
                                <k use="amount"></k>
                            </li>

                                <li use="subtotalRow">
                                    <div class="w-100">
                                        <p class="frm-head d-flex justify-content-between align-items-center">Subtotal
                                            <k use="subtotalAmount">₹ 0.00</k>
                                        </p>
                                        <? if ($cfg['GST.FLAG'] != 3) { ?>
                                        <p class="frm-head d-flex justify-content-between align-items-center gstcharge">GST (18%)
                                            <k use="totalGstAmount">₹ 0.00</k>
                                        </p>
                                        <? } ?>
                                        <p class="frm-head d-flex justify-content-between align-items-center internetcharge">Internet Handling Charges
                                            <k use="internetAmount">₹ 0.00</k>
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="w-100">
                                        <h5 class="frm-head d-flex justify-content-between align-items-center mb-0">Total Payable
                                            <k use="totalAmountpay" style="color: var(--sky);">₹ 0.00</k>
                                        </h5>
                                    </div>
                                </li>
                            </ul>
                        </div>
                          <div class="review_right">
                  <div class="review_tab">
                    <div class="hotel_link_owl owl-carousel owl-theme">
                     <input type="hidden" name="registrationMode" id="registrationMode">

                      <?php if (in_array("Neft", $offline_payments)) { ?>
                      <button   type="button" class="active"
                        onclick=" var f=$(this).closest('form');
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Neft]').prop('checked',true).trigger('click');
                        ">
                        <i class="fal fa-building"></i> NEFT
                      </button>
                      <?php } ?>

                      <?php if (in_array("Upi", $offline_payments)) { ?>
                      <button type="button"
                        onclick=" var f=$(this).closest('form');
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Upi]').attr('act','Upi').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> UPI
                      </button>
                      <?php } ?>

                      <?php if (in_array("Card", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> <?if ($rowInfo['paymentGateway']=='razorpay') {?>Razorpay<?}else{?>
                          Card
                        <? }  ?>
                      </button>
                      <?php } ?>

                      <?php if (in_array("Cheque/DD", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').hide();$('#DD').show();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Cheque]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> DD
                      </button>
                      <?php } ?>

                      <?php if (in_array("Cash", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').show();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Cash]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Cash
                      </button>
                      <?php } ?>

                      <!-- Hidden radios (logic only – REQUIRED) -->
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Neft" hidden checked>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Upi" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" hidden>

                    </div>
                   </div>
                    <div class="review_right_box" id="banktransfer" style="display: block;">
                      <ul>
                        <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li  class="for-neft-rtgs-only">
                          <h6 class="d-flex justify-content-between align-items-center">Bank Details</h6>
                          <div>
                           <p class="frm-head text_dark d-flex justify-content-between align-items-start" style="flex-direction: column;gap: 2px;border-bottom: 1px dashed var(--border);padding-bottom: 6px;">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p>
                            <!-- <p class="frm-head text_dark d-flex justify-content-between align-items-center">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p> -->
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Bank<span class="text-white"><?= $cfg['INVOICE_BANKNAME'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Account<span class="text-white"><?= $cfg['INVOICE_BANKACNO'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center mb-0">IFSC<span class="text-white"><?= $cfg['INVOICE_BANKIFSC'] ?></span></p>
                          </div>
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Upi", $offline_payments)) {
                          ?>
                                <li class="text-center for-upi-only" style="display: none;">
                                  <img src="<?= $QR_code ?>" alt="">
                                  <h6 class="d-flex justify-content-center align-items-center">Scan QR</h6>
                                </li>
                          <?php } ?>
                            <style>
                      #qrPopupOverlay {
                          display: none;
                          position: fixed;
                          top: 0; left: 0;
                          width: 100%;
                          height: 100%;
                          background: rgba(0,0,0,0.7);
                          justify-content: center;
                          align-items: center;
                          z-index: 9999;
                      }
                      #qrPopupOverlay img {
                          max-width: 40%;
                          max-height: 40%;
                          border: 2px solid #fff;
                          box-shadow: 0 0 10px #fff;
                      }
                      #qrPopupOverlay .closePopup {
                          position: absolute;
                          top: 20px;
                          right: 30px;
                          font-size: 30px;
                          color: #fff;
                          cursor: pointer;
                      }
                      </style>
                        <div id="qrPopupOverlay">
                        <span class="closePopup">&times;</span>
                        <img src="" alt="QR Code">
                    </div>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="neft_bank_name" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                         <?php
                          if (in_array("Upi", $offline_payments)) {
                          ?>
                        <li class="for-upi-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UPI Transaction No.</h6>
                          <input class="for-upi-only" type="text" class="form-control mandatory utrnft" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li class="for-neft-rtgs-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UTR Number</h6>
                          <input type="text" class="form-control mandatory utrnft" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                        <!-- <li>
                          <span id="neft_file_name" style="display:none;"></span>
                          <button type="button" id="neft_remove_btn" class="remove-file" style="display:none;">&times;</button>
                       <input style="display:none;" type="file" accept="image/*,application/pdf" name="neft_document" id="neft_document" class="mandatory" style="display:none" validate="Please upload a image">
                          <label for="neft_document" class="file-label">Upload Payment Receipt</label>
                        </li> -->
                        <li>
                          <span id="neft_file_name" style="display:none;" class="upload_name" style="">download (1).png</span>
                          <button type="button" style="display:none;"  id="neft_remove_btn" class="remove-file upload_delet" style=""><i class="fal fa-trash-alt"></i></button>
                          <input style="display:none;" type="file" accept="image/*,application/pdf" name="neft_document" id="neft_document" class="mandatory" validate="Please upload a image" data-gtm-form-interact-field-id="9">
                          <label for="neft_document" class="file-label" style="display: block;">Upload Payment Receipt</label>
                      </li>

                      </ul>
                    </div>
                    
                    <div class="review_right_box" id="DD">
                      <ul>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">DD No.</h6>
                          <input type="number" class="form-control mandatory" name="cheque_number" id="cheque_number" validate="Please enter cheque/DD number" placeholder="Enter DD No." type="number" maxlength="6" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;">
                        </li>
                      </ul>
                    </div>
                    <div class="review_right_box" id="cash">
                      <ul>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">
                        </li>
                        <!-- <li>
                              <span id="cash_file_name" style="display:none;"></span>
                              <button type="button" id="cash_remove_btn" class="remove-file" style="display:none;">&times;</button>
                          <input type="file" accept="image/*,application/pdf" name="cash_document" class="mandatory" id="cash_document" style="display:none" validate="Please upload a image">
                          <label for="cash_document">Upload Payment Receipt</label>

                        </li> -->
                         <li>
                          <span id="cash_file_name" style="display:none;" class="upload_name" style="">download (1).png</span>
                          <button type="button" style="display:none;"  id="cash_remove_btn" class="remove-file upload_delet" style=""><i class="fal fa-trash-alt"></i></button>
                          <input style="display:none;" type="file" accept="image/*,application/pdf" name="cash_document" id="cash_document" class="mandatory" validate="Please upload a image" data-gtm-form-interact-field-id="9">
                          <label for="cash_document" class="file-label" style="display: block;">Upload Payment Receipt</label>
                      </li>
                      </ul>
                    </div>
                    <div class="review_right_box" id="Cards">
                        
                      <ul>
                         <li>
                            <ol>
                                <li><?=$rowInfo['card_info']?></li>
                            </ol>
                        </li>
                        <li>
                         
                          <!-- <h6 class="d-flex justify-content-between align-items-center">Accepted Cards</h6> -->
                          <p>
                            <img src="<?= $onlinePaymentLogo ?>" style="width: 100%;    object-fit: contain;height: auto;background: transparent;filter: brightness(16.5); margin_bottom:0; padding-bottom:0;">
                          </p>
                        </li>
                   
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="registration_right_bottom">
                <button type="button" name="previous" class="checkout-back-btn action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
                <button type="submit" name="submit" class="submit action-button">Confirm<?php check(); ?></button>
            </div>
        </div>
        </form>
        <form  class="profile_pop_form accompany-form" name="frmAddAccompanyfromProfile" id="frmAddAccompanyfromProfile" action="registration.process.php" method="post" enctype="multipart/form-data">

        <div class="popup_body registration_right_wrap " id="addguest">
            <div class="registration_right_head">
                <span><?=$cfg['ACCOMAPNY_TITLE']?></span><button type="button" class="popup_close"><?php close(); ?></button>
            </div>
                <div class="registration_right_body"  id="section4">
                    <div class="registration_right_body_head">
                        <div class="registration_right_body_head_left">
                            <h4>Accompanying Persons</h4>
                            <h5>Add family members.</h5>
                        </div>
                    </div>
                    <div class="registration_right_body_content">
                        <div class="guest_wrap"  id="accompanyingTableBody">
                            <?php
                                $accompanyIndex = 0;
                                $accompanyCatagory = 1; // same as old
                                $registrationCurrency = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];

                                // For initial guest row (first accompany)
                                ?>
                                <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" cutoffid="<?= $currentCutoffId ?>" />
                                <input type="hidden" name="accompanyClasfId" value="<?= $accompanyCatagory ?>" />
                                <input type="hidden" name="act" value="add_accompany" />
                                <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
                                 <input type="hidden" name="delegate_id" value="<?= $delegateId?>">

                                <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />

                                <?php
                                //echo '<pre>'; print_r($dinnerTariffArray);
                                foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {
                                    if (floatval($dinnerValue[$currentCutoffId]['AMOUNT']) > 0) {
                                    $dinner_amnt_display = '<strong>' . number_format($dinnerValue[$currentCutoffId]['AMOUNT'], 2) . '</strong> <br>' . $registrationDetailsVal['CURRENCY'];
                                ?>
                                    <input type="hidden" name="dinner_amnt_display" id="dinner_amnt_display" value="<?= $dinner_amnt_display ?>" />

                                    <input type="hidden" name="dinner_classification_id" id="dinner_classification_id" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" />

                                    <input type="hidden" name="dinner_amnt" id="dinner_amnt" value="<?= $dinnerValue[$currentCutoffId]['AMOUNT'] ?>" />

                                    <input type="hidden" name="dinner_title" id="dinner_title" value="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?>" />

                                    <input type="hidden" name="dinner_hotel_name" id="dinner_hotel_name" value="<?= $dinnerValue[$currentCutoffId]['dinner_hotel_name'] ?>" />

                                    <input type="hidden" name="dinner_hotel_link" id="dinner_hotel_link" value="<?= $dinnerValue[$currentCutoffId]['link'] ?>" />

                                    <input type="hidden" name="dinner_date" id="dinner_date" value="<?= $dinnerValue[$currentCutoffId]['DATE'] ?>" />


                                <?php

                                    }
                                }
                                $sql_icon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                                                            WHERE `id`='3' AND `purpose`='Registration' AND status IN ('A', 'I')";
                                $result    = $mycms->sql_select($sql_icon);
                                $accompanyingIcon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];

                                ?>
                            <li>
                                <div class="guest_wrap_box">
                                    <span class="guest_wrap_grp_icon"><?php duser(); ?></span>
                                    <div class="guest_wrap_inner">
                                        <div class="guest_wrap_grp">
                                            <label class="guest_wrap_grp_head">Guest Name</label>
                                            <input type="text" class="form-control accompany_name"
                                                name="accompany_name_add[<?= $accompanyIndex ?>]"
                                                placeholder="Enter full Name"
                                                validate="Enter the accompany name"
                                                countindex="<?= $accompanyIndex ?>" required>
                                            <input type="hidden" name="accompany_selected_add[<?= $accompanyIndex ?>]" value="0" />
                                            <input type="checkbox" style="display:none;" class="accompanyCount" name="accompanyCount" id="accompanyCount" use="accompanyCountSelect" value="1" amount="<?= $registrationAmount ?>" invoiceTitle="Accompanying Person" invoiceName="" icon="<?= $accompanyingIcon ?>" reg="accompany">
                                        </div>
                                        <div class="guest_wrap_grp">
                                            <?php
                                            // Fetch food preference from DB (if any)
                                            $sql_cal = array();
                                            $sql_cal['QUERY'] = "SELECT * FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . " WHERE `status` != 'D'";
                                            $res_cal = $mycms->sql_select($sql_cal);

                                            if (!empty($res_cal) && $res_cal[0]['food_preference'] == 'A') { ?>
                                            <label class="guest_wrap_grp_head">Food Preference</label>
                                            <div class="cus_check_wrap g2">
                                                <label class="cus_check stay_select">
                                                    <input type="radio" name="accompany_food_choice[<?= $accompanyIndex ?>]" id="veg_<?= $accompanyIndex ?>" value="VEG" validate="Please select your food preference" >
                                                    <span class="checkmark">
                                                        <n>Veg</n>
                                                    </span>
                                                </label>
                                                <label class="cus_check stay_select">
                                                    <input type="radio" name="accompany_food_choice[<?= $accompanyIndex ?>]" id="nonveg_<?= $accompanyIndex ?>" value="NON_VEG" validate="Please select your food preference" >
                                                    <span class="checkmark">
                                                        <n>Non-veg</n>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="guest_wrap_grp">
                                            <label class="guest_wrap_grp_head">Tariff</label>
                                            <h><span class="accompanyAmountDisplay"><?=   $registrationDetailsVal['CURRENCY'] . ' ' . number_format($registrationAmount) ?></span></h>
                                        </div>
                                    </div>
                                </div>
                                <a href="#" class="guest_action removeGuest"><?php delete(); ?></a>
                                <input type="hidden" 
                                    name="accompanyAmountSet" 
                                    value="<?= $registrationAmount ?>" 
                                    data-currency="<?= $registrationDetailsVal['CURRENCY'] ?>">
                                <!-- <td class="guest_action removeGuest"><a href="#"><?php delete(); ?></a></td> -->
                                <input type="hidden" name="accompanyAmount" id="accompanyAmount" value="<?= $registrationAmount ?>">
                                <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?= $registrationAmount ?>">
                                <input type="hidden" name="accompanyCounts" id="accompanyCounts" value="1">
                            </li>
                        
                        </div>
                        <button type="button" class="add_guest" id="add-accompany-btn"><?php add(); ?> Add Guest</button>
                    </div>
                </div>
                <div class="registration_right_bottom">
                    <button type="button" name="previous" class="previous action-button-previous popup_close">Cancel</button>
                    <button type="button" name="next" formPay="frmAddAccompanyfromProfile" class="next action-button"><?php save(); ?> Save</button>
                </div>
            </div>
                
            <div class="popup_body registration_right_wrap checkout-main-wrap profile_review" id="checkout-main-wrap-accompany">
            <?php
            $offline_payments = json_decode($cfg['PAYMENT.METHOD']);

            $sql_qr = array();
            $sql_qr['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                                                        WHERE `id`!='' AND `title`IN ('QR Code','Online Payment Logo')";
            $result = $mycms->sql_select($sql_qr);
            $onlinePaymentLogo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
            $QR_code = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['image'];
            // echo $cfg['PAYMENT.METHOD'];
            ?>
             <div class="registration_right_head">
                <span> Review</span>
                <button type="button" class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body">
              <div class="registration_right_body_head">
                <div class="registration_right_body_head_left">
                  <h4>Order Summary</h4>
                  <!-- <h5>Invoice #NC25-2836</h5> -->
                </div>
              </div>
              <div class="registration_right_body_content">

                <div class="review_wrap">
                  <div class="review_left">
                    <ul use="totalAmountTable">
                      <li use=rowCloneable style="display:none;">
                        <span><?php user() ?></span>
                        <n use="invTitle">
                          <h use="invName"></h>
                        </n>
                        <k use="amount"></k>
                      </li>

                          <li use="subtotalRow">
                        <div class="w-100">
                          <p class="frm-head d-flex justify-content-between align-items-center">Subtotal<k use="subtotalAmount">₹ 0.00</k>
                          </p>
                          <? 
                          if($cfg['GST.FLAG']!=3){
                            ?>
                          <p class="frm-head d-flex justify-content-between align-items-center gstcharge">GST (18%)<k use="totalGstAmount">₹ 0.00</k>
                          </p>
                          <?
                          }
                          ?>
                           <p class="frm-head d-flex justify-content-between align-items-center internetcharge">Internet Handling Charges<k use="internetAmount">₹ 0.00</k>
                          </p>
                        </div>
                      </li>
                      <li>
                        <div class="w-100" use="totalAmount">
                          <h5 class="frm-head d-flex justify-content-between align-items-center mb-0">Total Payable<k use="totalAmountpay" style="color: var(--sky);">₹ 0.00</k>
                          </h5>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="review_right">
                  <div class="review_tab">
                    <div class="hotel_link_owl owl-carousel owl-theme">
                     <input type="hidden" name="registrationMode" id="registrationMode">

                      <?php if (in_array("Neft", $offline_payments)) { ?>
                      <button   type="button" class="active"
                        onclick=" var f=$(this).closest('form');
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Neft]').prop('checked',true).trigger('click');
                        ">
                        <i class="fal fa-building"></i> NEFT
                      </button>
                      <?php } ?>

                      <?php if (in_array("Upi", $offline_payments)) { ?>
                      <button type="button"
                        onclick=" var f=$(this).closest('form');
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Upi]').attr('act','Upi').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> UPI
                      </button>
                      <?php } ?>

                      <?php if (in_array("Card", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> <?if ($rowInfo['paymentGateway']=='razorpay') {?>Razorpay<?}else{?>
                          Card
                        <? }  ?>
                      </button>
                      <?php } ?>

                      <?php if (in_array("Cheque/DD", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').hide();$('#DD').show();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Cheque]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> DD
                      </button>
                      <?php } ?>

                      <?php if (in_array("Cash", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').show();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Cash]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Cash
                      </button>
                      <?php } ?>

                      <!-- Hidden radios (logic only – REQUIRED) -->
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Neft" hidden checked>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Upi" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" hidden>
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" hidden>

                    </div>
                   </div>
                    <div class="review_right_box" id="banktransfer" style="display: block;">
                      <ul>
                        <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li  class="for-neft-rtgs-only">
                          <h6 class="d-flex justify-content-between align-items-center">Bank Details</h6>
                          <div>
                           <p class="frm-head text_dark d-flex justify-content-between align-items-start" style="flex-direction: column;gap: 2px;border-bottom: 1px dashed var(--border);padding-bottom: 6px;">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p>
                            <!-- <p class="frm-head text_dark d-flex justify-content-between align-items-center">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p> -->
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Bank<span class="text-white"><?= $cfg['INVOICE_BANKNAME'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Account<span class="text-white"><?= $cfg['INVOICE_BANKACNO'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center mb-0">IFSC<span class="text-white"><?= $cfg['INVOICE_BANKIFSC'] ?></span></p>
                          </div>
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Upi", $offline_payments)) {
                          ?>
                                <li class="text-center for-upi-only" style="display: none;">
                                  <img src="<?= $QR_code ?>" alt="">
                                  <h6 class="d-flex justify-content-center align-items-center">Scan QR</h6>
                                </li>
                          <?php } ?>
                            <style>
                      #qrPopupOverlay {
                          display: none;
                          position: fixed;
                          top: 0; left: 0;
                          width: 100%;
                          height: 100%;
                          background: rgba(0,0,0,0.7);
                          justify-content: center;
                          align-items: center;
                          z-index: 9999;
                      }
                      #qrPopupOverlay img {
                          max-width: 40%;
                          max-height: 40%;
                          border: 2px solid #fff;
                          box-shadow: 0 0 10px #fff;
                      }
                      #qrPopupOverlay .closePopup {
                          position: absolute;
                          top: 20px;
                          right: 30px;
                          font-size: 30px;
                          color: #fff;
                          cursor: pointer;
                      }
                      </style>
                        <div id="qrPopupOverlay">
                        <span class="closePopup">&times;</span>
                        <img src="" alt="QR Code">
                    </div>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="neft_bank_name" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                         <?php
                          if (in_array("Upi", $offline_payments)) {
                          ?>
                        <li class="for-upi-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UPI Transaction No.</h6>
                          <input class="for-upi-only" type="text" class="form-control mandatory utrnft" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li class="for-neft-rtgs-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UTR Number</h6>
                          <input type="text" class="form-control mandatory utrnft" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                        <!-- <li>
                          <span id="neft_file_name" style="display:none;"></span>
                          <button type="button" id="neft_remove_btn" class="remove-file" style="display:none;">&times;</button>
                       <input style="display:none;" type="file" accept="image/*,application/pdf" name="neft_document" id="neft_document" class="mandatory" style="display:none" validate="Please upload a image">
                          <label for="neft_document" class="file-label">Upload Payment Receipt</label>
                        </li> -->
                        <li>
                          <span id="neft_file_name" style="display:none;" class="upload_name" style="">download (1).png</span>
                          <button type="button" style="display:none;"  id="neft_remove_btn" class="remove-file upload_delet" style=""><i class="fal fa-trash-alt"></i></button>
                          <input style="display:none;" type="file" accept="image/*,application/pdf" name="neft_document" id="neft_document" class="mandatory" validate="Please upload a image" data-gtm-form-interact-field-id="9">
                          <label for="neft_document" class="file-label" style="display: block;">Upload Payment Receipt</label>
                      </li>

                      </ul>
                    </div>
                    
                    <div class="review_right_box" id="DD">
                      <ul>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">DD No.</h6>
                          <input type="number" class="form-control mandatory" name="cheque_number" id="cheque_number" validate="Please enter cheque/DD number" placeholder="Enter DD No." type="number" maxlength="6" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;">
                        </li>
                      </ul>
                    </div>
                    <div class="review_right_box" id="cash">
                      <ul>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">
                        </li>
                        <!-- <li>
                              <span id="cash_file_name" style="display:none;"></span>
                              <button type="button" id="cash_remove_btn" class="remove-file" style="display:none;">&times;</button>
                          <input type="file" accept="image/*,application/pdf" name="cash_document" class="mandatory" id="cash_document" style="display:none" validate="Please upload a image">
                          <label for="cash_document">Upload Payment Receipt</label>

                        </li> -->
                         <li>
                          <span id="cash_file_name" style="display:none;" class="upload_name" style="">download (1).png</span>
                          <button type="button" style="display:none;"  id="cash_remove_btn" class="remove-file upload_delet" style=""><i class="fal fa-trash-alt"></i></button>
                          <input style="display:none;" type="file" accept="image/*,application/pdf" name="cash_document" id="cash_document" class="mandatory" validate="Please upload a image" data-gtm-form-interact-field-id="9">
                          <label for="cash_document" class="file-label" style="display: block;">Upload Payment Receipt</label>
                      </li>
                      </ul>
                    </div>
                    <div class="review_right_box" id="Cards">
                        
                      <ul>
                         <li>
                            <ol>
                                <li><?=$rowInfo['card_info']?></li>
                            </ol>
                        </li>
                        <li>
                         
                          <!-- <h6 class="d-flex justify-content-between align-items-center">Accepted Cards</h6> -->
                          <p>
                            <img src="<?= $onlinePaymentLogo ?>" style="width: 100%;    object-fit: contain;height: auto;background: transparent;filter: brightness(16.5); margin_bottom:0; padding-bottom:0;">
                          </p>
                        </li>
                   
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
              <button type="submit" name="submit" id="confirmPayment" class="submit action-button">Confirm<?php check(); ?></button>
            </div>
        </div>      
        </form>
        <div class="popup_body registration_right_wrap" id="adddinner">
            <div class="registration_right_head">
                <span>Gala Dinner</span><button type="button" class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body">
                <div class="registration_right_body_head">
                    <div class="registration_right_body_head_left">
                        <h4>Add Gala Dinner</h4>
                        <h5>Join us for exclusive networking evenings.</h5>
                    </div>
                </div>
                <div class="registration_right_body_content">
                    <div class="cus_check_wrap g2">
                        <label class="cus_check workshop_select gala_select">
                            <input type="checkbox" name="regimood" checked>
                            <span class="checkmark">
                                <n>
                                    <?php dinner(); ?>
                                    <iii></iii>
                                </n>
                                <g>Welcome Gala Night
                                    <ii><?php calendar(); ?>Dec 19, 2025 (Day 1)</ii>
                                    <k>A traditional Bengali themed evening with live classical music.</k>
                                </g>
                                <h>
                                    <l>₹ 2,500</l>
                                </h>
                            </span>
                        </label>
                        <label class="cus_check workshop_select gala_select">
                            <input type="checkbox" name="regimood">
                            <span class="checkmark">
                                <n>
                                    <?php dinner(); ?>
                                    <iii></iii>
                                </n>
                                <g>Welcome Gala Night
                                    <ii><?php calendar(); ?>Dec 19, 2025 (Day 1)</ii>
                                    <k>A traditional Bengali themed evening with live classical music.</k>
                                </g>
                                <h>
                                    <l>₹ 2,500</l>
                                </h>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="registration_right_bottom">
                <button type="button" name="previous" class="previous action-button-previous popup_close">Cancel</button>
                <button type="button" name="next" class="next action-button"><?php save(); ?> Save</button>
            </div>
        </div>
          <div class="popup_body registration_right_wrap" id="cancelation">
            <div class="registration_right_head">
                <span>Cancellation Policy</span><button class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body registration_right_body_withou_bottom">
                <?php echo $rowInfo['cancellation_page_info']; ?>
            </div>
        </div>
         <div class="popup_body registration_right_wrap" id="abstractdetails">
            <div class="registration_right_head">
                <span>Abstract Details</span><button class="popup_close"><?php close(); ?></button>
            </div>
            <div class="registration_right_body registration_right_body_withou_bottom">
                <div class="registration_right_body_tab">
                    <div class="hotel_link_owl owl-carousel owl-theme">
                         <?php
                            foreach ($abstract_details as $key => $value) {
                                 $active = ($key == 0) ? 'active' : '';
                            ?>
                        <button class="hotel_tab_btn <?= $active ?>" type="button"  data-tab="<?= $value['abstract_submition_code'] ?>"><?= $value['abstract_submition_code'] ?></button>
                        <? } ?>
                    </div>
                </div>
                <div class="registration_right_body_content">
                    <?php
                            foreach ($abstract_details as $key => $value) {
                            //    echo "<pre>";
                            //                 print_r($value);
                            ?>
                    <div class="hotel_box hotel_box_abstract" id="<?= $value['abstract_submition_code'] ?>" style="display: none;">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <!-- <h4><?= $value['abstract_submition_code'] ?></h4> -->
                                <?
                                	$sqlAbstractTopic			  =	array();
                                    $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                        WHERE `id` = ?";

                                    $sqlAbstractTopic['PARAM'][]  = array('FILD' => 'id', 'DATA' =>  $value['abstract_topic_id'],  'TYP' => 's');
                                    //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'category', 'DATA' =>$abstract_topic_id,  'TYP' => 's');
                                    $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
                                    ?>
                                <? if($resultAbstractTopic[0]['abstract_topic']!=''){?>
                                         <h5>Topic: <?= $resultAbstractTopic[0]['abstract_topic'] ?></h5>
                                   <?}?>
                            </div>
                            <div class="registration_right_body_head_right">
                                <a href="abstract_user_edit.php?abstractDelegateId=<?= $value['id'] ?>"  target="_blank" class="text_danger">Edit</a>
                            </div>
                        </div>
                        <div class="abstract_review_grid">
                            <li>
                                <h4>
                                    <n><?php user(); ?>Author Details</n>
                                </h4>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Email Address</p>
                                        <h6><?= $value['abstract_author_email_id'] ?></h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mobile Number</p>
                                        <h6>
                                            <span><?= $value['abstract_author_phone_code'] ?><?= $value['abstract_author_phone_no'] ?></span>
                                            
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name</p>
                                        <h6>
                                            <span><?= $value['abstract_author_name'] ?></span>
                                           
                                        </h6>
                                    </div>
                                    
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Address</p>
                                        <h6>
                                            <?php
                                                $sqlFetchCountry   = array();
                                                $sqlFetchCountry['QUERY']    = "SELECT `country_name` FROM " . _DB_COMN_COUNTRY_ . " 
                                                                                WHERE `status` =?
                                                                                AND `country_id` = '".$value['abstract_author_country_id']."'
                                                                            ORDER BY `country_name` ASC";                                                 
                                                $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                 $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                $sqlFetchState  = array();
                                                $sqlFetchState['QUERY']    = "SELECT `state_name` FROM " . _DB_COMN_STATE_ . " 
                                                                     WHERE `status` =?
                                                                 AND `st_id` = '".$value['abstract_author_state_id']."'

                                                                 ORDER BY `state_name` ASC";

                                                $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                $resultFetchState = $mycms->sql_select($sqlFetchState);
                                            ?>
                                            <span><?= $value['abstract_author_address'] ?></span>
                                          
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Country</p>
                                        <h6>
                                            <span><?= $resultFetchCountry[0]['country_name'] ?></span>
                                          
                                        </h6>
                                    </div>
                                     <div class="frm_grp span_2">
                                        <p class="frm-head">State</p>
                                        <h6>
                                            <span ><?=$resultFetchState[0]['state_name'] ?></span>
                                          
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">City</p>
                                        <h6>
                                            <span ><?= $value['abstract_author_city'] ?></span>
                                        </h6>
                                    </div>
                                     <div class="frm_grp span_2">
                                        <p class="frm-head">Pin</p>
                                        <h6>
                                            <span ><?= $value['abstract_author_pin'] ?></span>
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Institute</p>
                                        <h6 ><?= $value['abstract_author_institute_name'] ?></h6>
                                    </div>
                                        <div class="frm_grp span_2">
                                        <p class="frm-head">Department</p>
                                        <h6><?= $value['abstract_author_department'] ?></h6>
                                    </div>
                                        
                                </div>
                            </li>
                            <?php
                                    $sqlCoAuthor   = array();
                                    $sqlCoAuthor['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_COAUTHOR_ . " 
                                            WHERE `status` =? 
                                            AND `abstract_id` =?
                                        ORDER BY `id` ASC";

                                    $sqlCoAuthor['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                    $sqlCoAuthor['PARAM'][]   = array('FILD' => 'abstract_id', 'DATA' =>  $value['id'], 'TYP' => 's');

                                    $resultCoAuthor = $mycms->sql_select($sqlCoAuthor);

                                    if ($resultCoAuthor) {
                                        $i = 1;
                                        foreach ($resultCoAuthor as $k => $val) {
                                            // echo "<pre>";
                                            // print_r($val);
                                            $drChecked = ($val['abstract_coauthor_title'] == 'Dr') ? "checked" : "";
                                    ?>
                            <li>
                                <h4>
                                    <n><?php duser(); ?>Co-Author Details <?= $i ?></n>
                                </h4>
                                    <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Email Address</p>
                                        <h6><?= $val['abstract_coauthor_email'] ?></h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mobile Number</p>
                                        <h6>
                                            <span><?= $val['abstract_coauthor_phone_code'] ?><?= $val['abstract_coauthor_phone_no'] ?></span>
                                            
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name</p>
                                        <h6>
                                            <span><?= $val['abstract_coauthor_name'] ?></span>
                                           
                                        </h6>
                                    </div>
                                    
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Address</p>
                                        <h6>
                                             <?php
                                                $sqlFetchCountry   = array();
                                                $sqlFetchCountry['QUERY']    = "SELECT `country_name` FROM " . _DB_COMN_COUNTRY_ . " 
                                                                                WHERE `status` =?
                                                                                AND `country_id` = '".$val['abstract_coauthor_country_id']."'
                                                                            ORDER BY `country_name` ASC";
                                                $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                $sqlFetchState  = array();
                                                $sqlFetchState['QUERY']    = "SELECT `state_name` FROM " . _DB_COMN_STATE_ . " 
                                                                     WHERE `status` =?
                                                                 AND `st_id` = '".$val['abstract_coauthor_state_id']."'

                                                                 ORDER BY `state_name` ASC";

                                                $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                $resultFetchState = $mycms->sql_select($sqlFetchState);
                                            ?>
                                            <span><?= $val['abstract_coauthor_address'] ?></span>
                                           
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Country</p>
                                        <h6>
                                            <span data-review="user_country"><?= $resultFetchCountry[0]['country_name'] ?></span>
                                           
                                        </h6>
                                    </div>
                                     <div class="frm_grp span_2">
                                        <p class="frm-head">State</p>
                                        <h6>
                                            <span data-review="user_state"><?= $resultFetchState[0]['state_name'] ?></span>
                                         
                                    </div>
                                     <div class="frm_grp span_2">
                                        <p class="frm-head">City</p>
                                        <h6>
                                            <span data-review="user_city"><?= $val['abstract_coauthor_city_name'] ?></span>
                                        </h6>
                                    </div>
                                     <div class="frm_grp span_2">
                                        <p class="frm-head">Pin</p>
                                        <h6>
                                          
                                            <span data-review="user_postal_code"><?= $val['abstract_coauthor_pincode'] ?></span>
                                        </h6>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Institute</p>
                                        <h6 ><?= $val['abstract_coauthor_institute_name'] ?></h6>
                                    </div>
                                        <div class="frm_grp span_2">
                                        <p class="frm-head">Department</p>
                                        <h6><?= $val['abstract_coauthor_department'] ?></h6>
                                    </div>
                                        
                                </div>
                            </li>
                            
                            <?  $i++; } } ?>
                            <li>
                                <h4>
                                    <n><i class="fal fa-tag"></i>Submission Category</n>
                                </h4>
                                <div class="form_grid">
                                    <? if($value['abstract_cat']!=""){ ?>
                                    <div class="frm_grp span_0 span_2">
                                        <p class="frm-head">Category</p>
                                        <h6><?=getCategoryName($value['abstract_cat'])?></h6>
                                    </div>
                                    <? } ?>
                                    <? if($value['abstract_parent_type']!=""){
                                        $sqlAbstractTopicSub			  =	array();
                                        $sqlAbstractTopicSub['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                                                                            WHERE `status` ='A'
                                                                        AND id='" . $value['abstract_parent_type'] . "'";


                                        $resultAbstractTopicSub = $mycms->sql_select($sqlAbstractTopicSub);
                                    ?>
                                    
                                    <div class="frm_grp span_0 span_2">
                                        <p class="frm-head">Sub Category 1</p>
                                        <h6><?=$resultAbstractTopicSub[0]['abstract_submission']?></h6>
                                    </div>
                                      <? } ?>
                                    <? if($value['abstract_child_type']!=""){
                                        $sqlAbstractPresentation             =    array();
                                        $sqlAbstractPresentation['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_PRESENTATION_ . " 
                                                                    WHERE `status` ='A'
                                                                 AND id='" . $value['abstract_child_type'] . "'";


                                        $resultAbstractPresentation = $mycms->sql_select($sqlAbstractPresentation);
                                        ?>
                                    <div class="frm_grp span_0 span_2">
                                        <p class="frm-head">Sub Category 2</p>
                                        <h6><?=$resultAbstractPresentation[0]['abstract_presentation']?></h6>
                                    </div>
                                     <? } ?>
                                </div>
                            </li>
                            <li>
                                <h4>
                                    <n><i class="fal fa-book-open"></i>Abstract Content</n>
                                </h4>
                                <div class="form_grid">
                                    <div class="frm_grp span_0 span_4">
                                        <p class="frm-head">Abstract Title</p>
                                        <h6><?= $value['abstract_title'] ?></h6>
                                    </div>
                                      <?php
                                           
                                            $category_id = $value['abstract_cat']?? null;
                                            $sub_category_id =$value['abstract_parent_type'] ?? null;
                                            $sub_subcategory_Id = $value['abstract_child_type'] ?? null;

                                            $category_fields = [];

                                            /* ================= CATEGORY ================= */
                                            if($category_id){

                                                $sqlCategory1 = [
                                                    'QUERY' => "SELECT category_fields 
                                                                FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
                                                                WHERE id = ?",
                                                    'PARAM' => [
                                                        ['FILD'=>'id','DATA'=>$category_id,'TYP'=>'i']
                                                    ]
                                                ];

                                                $resultCategory1 = $mycms->sql_select($sqlCategory1);
                                            
                                                if($resultCategory1){
                                                    $category_fields = cleanFields($resultCategory1[0]['category_fields']);
                                                }

                                                /* ================= SUB CATEGORY ================= */
                                                if(empty($category_fields) && $sub_category_id){

                                                    $sqlSubmission = [
                                                        'QUERY' => "SELECT category_fields 
                                                                    FROM "._DB_ABSTRACT_SUBMISSION_."
                                                                    WHERE category = ? AND id = ?
                                                                    LIMIT 1",
                                                        'PARAM' => [
                                                            ['FILD'=>'category','DATA'=>$category_id,'TYP'=>'i'],
                                                            ['FILD'=>'id','DATA'=>$sub_category_id,'TYP'=>'i']
                                                        ]
                                                    ];

                                                    $resultSubmission = $mycms->sql_select($sqlSubmission);

                                                    if($resultSubmission){
                                                        $category_fields = cleanFields($resultSubmission[0]['category_fields']);
                                                    }
                                                }

                                                /* ================= SUB SUB CATEGORY ================= */
                                                if(empty($category_fields) && $sub_subcategory_Id){

                                                    $sqlPresentation = [
                                                        'QUERY' => "SELECT category_fields 
                                                                    FROM "._DB_ABSTRACT_PRESENTATION_."
                                                                    WHERE category_id = ? 
                                                                    AND submission_id = ? 
                                                                    AND id = ?
                                                                    LIMIT 1",
                                                        'PARAM' => [
                                                            ['FILD'=>'category_id','DATA'=>$category_id,'TYP'=>'i'],
                                                            ['FILD'=>'submission_id','DATA'=>$sub_category_id,'TYP'=>'i'],
                                                            ['FILD'=>'id','DATA'=>$sub_subcategory_Id,'TYP'=>'i']
                                                        ]
                                                    ];

                                                    $resultPresentation = $mycms->sql_select($sqlPresentation);

                                                    if($resultPresentation){
                                                        $category_fields = cleanFields($resultPresentation[0]['category_fields']);
                                                    }
                                                }
                                            }

                                            /* ================= FINAL FALLBACK ================= */
                                            if(empty($category_fields)){
                                                $category_fields = [];
                                            }

                                            $field_ids = implode(",", $category_fields);
                                            if (!empty($field_ids)) {
                                                $order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
                                            } else {
                                                $order_by = "ORDER BY id ASC";
                                            }
                                        $sqlAbstractFields              =    array();
                                        $sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
                                                                WHERE `status` = ?
                                                                 ".$order_by."";

                                        $sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

                                        $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                        $i = 1;
                                        foreach ($resultAbstractFields as $key => $valueFields) {


                                            $sqlAbstractFieldsVal              =    array();
                                            $sqlAbstractFieldsVal['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . " 
                                                                WHERE  id='" . $value['id'] . "'"; //" . $valueFields['field_key'] . "!='NULL' AND

                                            $resultAbstractFieldsVal = $mycms->sql_select($sqlAbstractFieldsVal);
                                            //echo '<pre>'; print_r( $resultAbstractFieldsVal[0]['COUNTDATA']);

                                      if ($value[$valueFields['field_key']] != '' && $value[$valueFields['field_key']] != NULL && $value[$valueFields['field_key']] != 'NULL') {


                                        ?>
                                 <div class="frm_grp span_0 span_4">
                                        <p class="frm-head"><?=$valueFields['display_name']?></p>
                                        <h6><?=$value[$valueFields['field_key']]?></h6>
                                    </div>
                                    <? }  } ?>
                                     <!-- category File -->
                                     <? if($value['category_required_file']!=''){ ?>
                                        <div class="frm_grp span_4" id="review_abstract_wrapper" >
                                            <p class="frm-head">Category File</p>
                                            <h6 id="review_abstract_file"><a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$value['category_required_file']?>" target="_blank">
                                                    View File (<?=$value['category_required_file']?>)
                                                </a></h6>
                                        </div>
                                   <?  } ?>
                                    <!-- Abstract File -->
                                     <? if($value['abstract_file']!=''){ ?>
                                        <div class="frm_grp span_4" id="review_abstract_wrapper" >
                                            <p class="frm-head">Abstract File</p>
                                            <h6 id="review_abstract_file"><a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$value['abstract_file']?>" target="_blank">
                                                    View File (<?=$value['abstract_file']?>)
                                                </a>
                                            </h6>
                                        </div>
                                   <?  } ?>
                                    
                                    <? if($value['abstract_consent_file']!=''){ ?>
                                    <!-- HOD Consent -->
                                    <div class="frm_grp span_4" id="review_hod_wrapper">
                                        <p class="frm-head">HOD Consent File</p>
                                        <h6 id="review_hod_file"><a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$value['abstract_consent_file']?>" target="_blank">
                                                    View File (<?=$value['abstract_consent_file']?>)
                                                </a>
                                        </h6>
                                        
                                    </div>
                                    <? } ?>
                                     <?php
									$sqlAbstractSubcat    = array();
									$sqlAbstractSubcat['QUERY']    = "SELECT * 
																		  FROM " . _DB_AWARD_MASTER_ . " 
																		 WHERE `status` = ? 
                                                                         AND `related_category_id` = ?
																	  ORDER BY `id` ASC";

									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'related_category_id',  'DATA' =>$value['abstract_cat'],  'TYP' => 's');

									$resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);

                                    $sqlAbstractFile    = array();
									$sqlAbstractFile['QUERY']    = "SELECT * 
																		  FROM " . _DB_AWARD_REQUEST_ . " 
																		 WHERE `status` = 'A' 
                                                                         AND `submission_id` = '".$value['id']."'
																	  ORDER BY `id` ASC";
                                  
									// $sqlAbstractFile['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
									// $sqlAbstractFile['PARAM'][]   = array('FILD' => 'submission_id',  'DATA' =>$value['id'],  'TYP' => 'i');
                                    
									$resultAbstractFile = $mycms->sql_select($sqlAbstractFile);

									if ($resultAbstractSubcat && $resultAbstractFile) { ?>
                                    <div class="frm_grp span_4">
                                     <!-- Nomination Files -->
                                         <div class="frm_grp span_4">
                                            <p class="frm-head">Award Description</p>
                                            <h6><?=$resultAbstractSubcat[0]['award_description']?></h6>
                                        </div>
                                        <? if ($resultAbstractFile[0]['upload_nomination_file']!='') { ?>
                                            <p class="frm-head">Nomination File</p>
                                            <h6>
                                                <a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$resultAbstractFile[0]['upload_nomination_file']?>" target="_blank">
                                                    View File (<?=$resultAbstractFile[0]['upload_nomination_file']?>)
                                                </a>
                                            </h6>
                                        </div>
                                           <? } ?>
                                    </div>
                                    <? } ?>
                               </li>
                         </div>
                      </div>
                     <? } ?>
                </div>  <!-- ADD THIS - closes registration_right_body_content -->
            </div>  <!-- closes registration_right_body -->
        </div>  <!-- closes popup_body -->
    </div>  <!-- closes popup_inner -->
</div>  <!-- closes popup_wrap -->
<?php include_once("includes/js-source.php"); ?>
    <?php include_once('cart.php'); ?>

<script>
    $('.popup_close').click(function() {
        $(".popup_wrap").hide();
        $(".popup_body").hide();
    });
    $('#touch-taget').click(function() {
        $(".profile_left_menu").toggleClass('active');
    });
      $(document).ready(function() {

        $('.hotel_tab_btn').click();
    });
 $(document).ready(function () {

    const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

    let yyyy = <?= $dateArr[0] ?>;
    let dayMonth = '<?= $dateCount->format("m/d/") ?>';

    let [month, dayNum] = dayMonth.split('/');

    // ✅ proper date object (NO STRING)
    let birthday = new Date(yyyy, month - 1, dayNum);
    birthday.setHours(23, 59, 59, 999);

    let today = new Date();

    // if passed → next year
    if (today > birthday) {
        birthday.setFullYear(birthday.getFullYear() + 1);
    }

    const countDown = birthday.getTime();

    const x = setInterval(function () {

        const now = Date.now();
        const distance = countDown - now;

        document.getElementById("days").innerText = Math.max(0, Math.floor(distance / day));
        document.getElementById("hours").innerText = Math.max(0, Math.floor((distance % day) / hour));
        document.getElementById("minutes").innerText = Math.max(0, Math.floor((distance % hour) / minute));
        document.getElementById("seconds").innerText = Math.max(0, Math.floor((distance % minute) / second));

        if (distance <= 0) {
            $("#registration_countdown").hide();
            $("#registration_closed").show();
            $(".register").hide();
            clearInterval(x);
        }

    }, 1000);

});
$(document).ready(function () {

    const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

    let yyyy = <?= $dateArrAbs[0] ?>;
    let dayMonth = '<?= $dateCountAbs->format("m/d/") ?>';

    let [month, dayNum] = dayMonth.split('/');

    // ✅ proper date object
    let birthdayAbs = new Date(yyyy, month - 1, dayNum);
    birthdayAbs.setHours(23, 59, 59, 999);

    let today = new Date();

    if (today > birthdayAbs) {
        birthdayAbs.setFullYear(birthdayAbs.getFullYear() + 1);
    }

    const countDownAbs = birthdayAbs.getTime();

    const x = setInterval(function () {

        const now = Date.now();
        const distanceAbs = countDownAbs - now;

        document.getElementById("dday").innerText = Math.max(0, Math.floor(distanceAbs / day));
        document.getElementById("dhour").innerText = Math.max(0, Math.floor((distanceAbs % day) / hour));
        document.getElementById("dmin").innerText = Math.max(0, Math.floor((distanceAbs % hour) / minute));
        document.getElementById("dsec").innerText = Math.max(0, Math.floor((distanceAbs % minute) / second));

        // ❌ FIXED BUG: was "distance < 0"
        if (distanceAbs <= 0) {
            $("#registration_countdown").hide();
            $("#registration_closed").show();
            $(".register").hide();
            clearInterval(x);
        }

    }, 1000);

});
$(document).ready(function() {

      $('#wrkshp_tab_btn').click(function() {
      var selectedHotelId = $(this).data('tab'); // get $val['id']
      // Call your function / AJAX
      //  fetchHotelData(selectedHotelId);
    });

    // Trigger first tab on page load
    var firstTab = $('#wrkshp_tab_btn').first();
    firstTab.click(); // This triggers the handler
    const workshopCheckboxes = document.querySelectorAll('input[reg="workshop"]');

    workshopCheckboxes.forEach(chk => {
      chk.addEventListener('change', function() {
        const selectedDate = this.dataset.date;
        const selectedType = this.dataset.type;

        if (this.checked) {
          // Disable other checkboxes of the same type on the same date
          workshopCheckboxes.forEach(otherChk => {
            if (otherChk !== this &&
              otherChk.dataset.date === selectedDate &&
              otherChk.dataset.type === selectedType) {
              otherChk.disabled = true;
            }
          });
        } else {
          // Re-enable checkboxes when this one is unchecked
          workshopCheckboxes.forEach(otherChk => {
            if (otherChk.dataset.date === selectedDate &&
              otherChk.dataset.type === selectedType) {
              otherChk.disabled = false;
            }
          });
        }
      });
    });
    $("input[type=checkbox][operationMode=workshopId]").each(function() {
      $(this).click(function() {

        var currChkbxStatus = $(this).attr("chkStatus");

        if (currChkbxStatus == "true") {
          $(this).prop("checked", false);
          $(this).attr("chkStatus", "false");
        } else {
          $(this).prop("checked", true);
          $(this).attr("chkStatus", "true");
        }

        calculateTotalAmount();
        // Count all checked checkboxes
        var selectedCount = $("input[type=checkbox][operationMode=workshopId]:checked").length;

        // Set the value in your input field (replace #selectedCountInput with your input's ID)
        $("a.selected-count").text(selectedCount + " Selected");


      });

    });
      $(document).ready(function() {
        $(document).on('click', '.file-label', function (e) {
            e.preventDefault();                                  // stop native for="" behavior
            $(this).closest('li').find('input[type="file"]').trigger('click');
        });
          $(document).on('change', 'input[type="file"].mandatory', function () {
            var $file = $(this);
            var file  = this.files[0];
            var $li   = $file.closest('li');

            if (file) {
                $li.find('.upload_name').text(file.name).show();
                $li.find('.remove-file').show();
                $li.find('.file-label').hide();
            } else {
                $li.find('.upload_name').hide();
                $li.find('.remove-file').hide();
                $li.find('.file-label').show();
            }
        });

        $(document).on('click', '.remove-file', function () {
            var $li = $(this).closest('li');
            $li.find('input[type="file"]').val('');
            $li.find('.upload_name').hide();
            $(this).hide();
            $li.find('.file-label').show();
        });
        // When a payment radio is clicked
       $("input[type=radio][use=payment_mode_select]").click(function() {
            var val = $(this).val();
            var $form = $(this).closest('form');   // ADD THIS

            if (val === 'Card') {
                $form.find('#registrationMode').val('ONLINE');   // was $('#registrationMode')
            } else {
                $form.find('#registrationMode').val('OFFLINE');
            }

            if ($(this).attr('act') === 'Upi') {
                $form.find('.for-upi-only').show();
                $form.find('.for-neft-rtgs-only').hide();
                $form.find('#neft_transaction_no').removeClass('mandatory').val('');
            } else {
                $form.find('.for-upi-only').hide();
                $form.find('.for-neft-rtgs-only').show();
                $form.find('#neft_transaction_no').addClass('mandatory');
            }
        });
        // Trigger click on page load if you want default selection
       var defaultChecked = $("input[type=radio][use=payment_mode_select]:checked");
        if(defaultChecked.length) { defaultChecked.trigger('click'); }

        // with this — one default per form:
        $('#frmAddWorkshopfromProfile, #frmAddAccompanyfromProfile').each(function () {
            var $checked = $(this).find("input[type=radio][use=payment_mode_select]:checked");
            if ($checked.length) $checked.trigger('click');
        });
    });
    $("form").on("submit", function(e) {
     var $form = $(this);
    var selectedOption = $form.find("input[type=radio][name='payment_mode']:checked").val();
    var flag = 0;

    if (!selectedOption) {
        toastr.error('Please select payment mode', 'Error', {
            "progressBar": true,
            "timeOut": 5000,
            "showMethod": "slideDown",
            "hideMethod": "slideUp"
        });
        flag = 1;
    } else {
        // Validate inputs inside the visible payment box
        $form.find(".review_right_box:visible input.mandatory").each(function() {
            var type = $(this).attr('type');
            if (type === 'radio') {
                if (!$("input[type='radio'][name='card_mode']:checked").length) {
                    toastr.error('Please select the card', 'Error', {
                        "progressBar": true,
                        "timeOut": 5000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    flag = 1;
                    return false; // stop .each loop
                }
            } else {
                if ($(this).val().trim() === '') {
                    toastr.error($(this).attr('validate'), 'Error', {
                        "progressBar": true,
                        "timeOut": 5000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    flag = 1;
                    return false; // stop .each loop
                }
            }
        });
    }

    if (flag > 0) {
        e.preventDefault(); // stop form submission
        return false;
    }
 
    // If we reach here, validation passed → form submits normally
});
});
 $(document).ready(function() {

    // function calculateTotalAmount() {
    //     var total = 0;
    //     alert(total);
    //     $(".accompanyAmountDisplay").each(function() {
    //         total += parseFloat($(this).text());
    //     });
    //     // Example if needed:
    //     // $('#subTotalPrc').text(total.toFixed(2));
    // }
    function addGuest() {

      var $body = $("#accompanyingTableBody");

      // ALWAYS read from the ORIGINAL hidden input (first one)
      var registrationAmount = parseFloat(
        $("input[name='accompanyAmount']:first").val()
      ) || 0;
       var currencyCode = $("input[name='accompanyAmountSet']:first").data("currency") || '';

      var index = $body.find("li").length;

      var $row = $body.find("li:first").clone(false);

      // ❌ remove duplicate ID (CRITICAL)
      $row.find("#accompanyAmount").removeAttr("id");

      // reset name input
      $row.find("input.accompany_name")
        .val("")
        .attr({
          name: "accompany_name_add[" + index + "]",
          countindex: index
        });

      // reset radios
      $row.find("input[type='radio']")
        .prop("checked", false)
        .each(function() {
          var baseId = $(this).attr("id").split("_")[0];
          $(this).attr({
            id: baseId + "_" + index,
            name: "accompany_food_choice[" + index + "]"
          });
        });

      // reset hidden selected flag
      
      $row.find("input[name^='accompany_selected_add']")
        .val(index)
        .attr("name", "accompany_selected_add[" + index + "]");

      // restore amount display (FIXES 00 issue)
     var formattedAmount = new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 0
    }).format(registrationAmount);

     // Set display
     $row.find(".accompanyAmountDisplay").text(currencyCode + ' ' + formattedAmount);
      $row.find(".removeGuest").show();

      $body.append($row);

      $("#accompanyCounts").val(index + 1);
      $("#accompanyCount").prop("checked", true);
      // calculateTotalAmount();
    }
    // $("#accompanyCount").prop("checked", true);
    $("#add-accompany-btn").on("click", function(e) {
      e.preventDefault();
      addGuest();
    });

    $(document).on("click", ".removeGuest", function(e) {
      e.preventDefault();

      var $body = $("#accompanyingTableBody");

      if ($body.find("li").length === 1) return;

      $(this).closest("li").remove();

      $body.find("li").each(function(i) {

        $(this).find("#accompanyAmount").removeAttr("id");
       var currencyCode = $("input[name='accompanyAmountSet']:first").data("currency") || '';

        $(this).find("input.accompany_name").attr({
          name: "accompany_name_add[" + i + "]",
          countindex: i
        });

        $(this).find("input[name^='accompany_selected_add']")
          .attr("name", "accompany_selected_add[" + i + "]");

        $(this).find("input[type='radio']").each(function() {
          var baseId = $(this).attr("id").split("_")[0];
          $(this).attr({
            id: baseId + "_" + i,
            name: "accompany_food_choice[" + i + "]"
          });
        });

        // restore amount text
        var amt = parseFloat(
          $("input[name='accompanyAmount']:first").val()
        ) || 0;
        var formattedAmount = new Intl.NumberFormat('en-IN', {
            maximumFractionDigits: 0
        }).format(registrationAmount);

          // Set display
          $(this).find(".accompanyAmountDisplay").text(currencyCode + ' ' + formattedAmount);

        });

      $("#accompanyCounts").val($body.find("li").length);

      // calculateTotalAmount();
    });


  });
</script>
    <script>
        $(document).ready(function() {
    // ============================================================
    // PREVENT ENTER KEY FROM SUBMITTING FORMS - BUTTON STILL WORKS
    // ============================================================
    
    var targetForms = ['frmAddWorkshopfromProfile', 'frmAddAccompanyfromProfile'];

    // Block Enter key on input fields inside target forms
    $(document).on('keydown', 'input, select', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            var $form = $(this).closest('form');
            var formId = $form.attr('id');
            
            if (targetForms.indexOf(formId) !== -1) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });

    // Block Enter on the form itself (backup for text inputs)
    $('#frmAddWorkshopfromProfile, #frmAddAccompanyfromProfile').on('keydown', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            var target = e.target;
            if (target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });

    // DO NOT block form submission - let the button work normally
    // Remove any submit interception code
});
   $(document).ready(function() {
    var currentSection = '<?= $section ?>';
    showSection(currentSection);
    var activeForm = null;

    // Next button click - FORM SPECIFIC
    $(document).on("click", ".next", function() {
        var formId = $(this).attr("formPay");
        var form = $("#" + formId);
        
        if (!form.length) {
            console.error('Form not found:', formId);
            return false;
        }

        if (validateSection(currentSection, form)) {
            // Hide other sections INSIDE THIS FORM ONLY
            form.find("#addworkshop, #addguest").hide();
            
            // Show checkout ONLY for this form
            form.find(".checkout-main-wrap").show();
            
            // Calculate totals for this specific form
            calculateTotalAmount(form);
            
            // Mark this form as active
            activeForm = formId;
        }
        
        // Check if there is at least one accompanying name filled - FORM SPECIFIC
        var hasName = false;
        form.find(".accompany_name").each(function() {
            if ($(this).val().trim() !== "") {
                hasName = true;
                return false;
            }
        });

        if (hasName) {
            form.find(".accompanyCount").prop("checked", true);
            calculateTotalAmount(form);
        }
    });

    // Previous button click - FORM SPECIFIC
    $(document).on("click", ".previous", function() {
        var form = $(this).closest('form');
        
        if (form.length) {
            // Hide checkout for this form
            form.find(".checkout-main-wrap").hide();
            
            // Show the appropriate section based on form type
            if (form.attr('id') === 'frmAddWorkshopfromProfile') {
                form.find("#addworkshop").show();
            } else if (form.attr('id') === 'frmAddAccompanyfromProfile') {
                form.find("#addguest").show();
            }
        }
    });

    // Close popup - FORM SPECIFIC
    $(document).on("click", ".popup_close", function() {
        var form = $(this).closest('form');
        if (form.length) {
            // Reset and hide all sections for this form
            form.find(".popup_body.registration_right_wrap").hide();
            form.find("#addworkshop, #addguest").hide();
            form.find(".checkout-main-wrap").hide();
            
            // Reset form if needed
            form[0].reset();
        }
    });

    // Confirm Payment - FORM SPECIFIC
    // $(document).on("click", "#confirmPayment", function(e) {
    //     e.preventDefault();
        
    //     var form = $(this).closest('form');
    //     if (!form.length) {
    //         return false;
    //     }

    //     // Get the visible checkout section within THIS form
    //     var visiblePayment = form.find(".checkout-main-wrap:visible");
    //     var flag = 0;

    //     // Validate mandatory fields only in this form's visible section
    //     visiblePayment.find(".mandatory").each(function() {
    //         var $field = $(this);
    //         if ($field.val().trim() === "") {
    //             var validationMsg = $field.attr("validate") || "This field is required";
    //             toastr.error(validationMsg, 'Error', {
    //                 "progressBar": true,
    //                 "timeOut": 3000,
    //                 "showMethod": "slideDown",
    //                 "hideMethod": "slideUp"
    //             });
    //             flag = 1;
    //             return false;
    //         }
    //     });

    //     if (flag === 1) {
    //         return false;
    //     }

    //     // If validation passes, submit the form
    //     form.submit();
    // });

    // Accompany name validation - FORM SPECIFIC
    $(document).on("change", ".accompany_name", function() {
        var form = $(this).closest('form');
        var hasName = false;
        
        form.find(".accompany_name").each(function() {
            if ($(this).val().trim() !== "") {
                hasName = true;
                return false;
            }
        });

        if (hasName) {
            form.find(".accompanyCount").prop("checked", true);
            calculateTotalAmount(form);
        }
    });

    // Payment mode selection - FORM SPECIFIC
    $(document).on("click", ".review_tab button", function() {
        var form = $(this).closest('form');
        var paymentType = $(this).text().trim();
        // Hide all payment sections in THIS form
        form.find(".review_right_box").hide();
        
        // Show the selected payment section
        if (paymentType.includes('NEFT')) {
            form.find("#banktransfer").show();
        } else if (paymentType.includes('UPI')) {
            form.find("#banktransfer").show();
            form.find(".for-upi-only").show();
        } else if (paymentType.includes('DD')) {
            form.find("#DD").show();
        } else if (paymentType.includes('Cash')) {
            form.find("#cash").show();
        } else if (paymentType.includes('Card') || paymentType.includes('Razorpay')) {
            form.find("#Cards").show();
        }
        
        // Update active state
        form.find(".review_tab button").removeClass('active');
        $(this).addClass('active');
    });

    // File upload handlers - FORM SPECIFIC
    $(document).on("click", ".upload_delet", function() {
        var form = $(this).closest('form');
        var fileInput = form.find('input[type="file"]');
        var fileNameSpan = form.find('.upload_name');
        
        fileInput.val('');
        fileNameSpan.hide();
        $(this).hide();
    });

    $(document).on("change", 'input[type="file"]', function() {
        var form = $(this).closest('form');
        var fileName = $(this).val().split('\\').pop();
        var fileNameSpan = form.find('.upload_name');
        var removeBtn = form.find('.upload_delet');
        
        if (fileName) {
            fileNameSpan.text(fileName).show();
            removeBtn.show();
        }
    });

    // Workshop tab switching - FORM SPECIFIC
    $(document).on("click", ".wrkshp_tab_btn", function() {
        var form = $(this).closest('form');
        var tabId = $(this).data('tab');
        
        // Hide all workshop boxes in this form
        form.find(".wrkshp_box").hide();
        
        // Show the selected workshop box
        form.find("#" + tabId).show();
        
        // Update active state
        form.find(".wrkshp_tab_btn").removeClass('active');
        $(this).addClass('active');
    });

    // Add guest button - FORM SPECIFIC
    $(document).on("click", "#add-accompany-btn", function() {
        var form = $(this).closest('form');
        // Your add guest logic here with form-specific selectors
        // Use form.find() for all selectors
    });

    // Check if any checkbox in a class is checked - MODIFIED to accept form parameter
    function isAnyCheckboxChecked(className, form) {
        var checkboxes;
        if (form) {
            checkboxes = form.find('.' + className);
        } else {
            checkboxes = $('.' + className);
        }
        
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                return true;
            }
        }
        return false;
    }

    // Validation function - MODIFIED to use form parameter
    function validateSection(section, formPay) {
        var isValid = true;
        var accomArr = [];
        var galaDinnerDiv = '';
        var hasExecuted = false;
        
        if (!formPay || !formPay.length) {
            console.error('Form not provided for validation');
            return false;
        }
        
        // Get the section within the specific form
        var sectionElement = formPay.find("#section" + section);
        if (!sectionElement.length) {
            return true; // Section not found, skip validation
        }
        
        // Validate inputs within this form's section
        sectionElement.find("input[type='text'], input[type='radio'], input[type='checkbox'], select").each(function(index) {
            var $this = $(this);
            
            if ($this.attr('type') === 'text') {
                if ($.trim($this.val()) === '') {
                    // Check if it's accompany name and section 4
                    if ($this.hasClass('accompany_name') && section == 4) {
                        var hasCheckedAccompany = formPay.find("input[type='checkbox'][name='accompanyCount']:checked").length > 0;
                        if (hasCheckedAccompany) {
                            var msg = $this.attr('validate');
                            toastr.error(msg, 'Error', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            isValid = false;
                            return false;
                        }
                    } else if (section == 2) {
                        var msg = $this.attr('validate');
                        if (msg) {
                            toastr.error(msg, 'Error', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            isValid = false;
                            return false;
                        }
                    }
                }
            } else if ($this.attr('type') === 'radio') {
                // Check if at least one radio button in this group is checked
                if (section == 1) {
                    var radioName = $this.attr('name');
                    if (!formPay.find("input[name='" + radioName + "']:checked").length) {
                        toastr.error('Please select a category', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        isValid = false;
                        return false;
                    }
                }
                if (section == 2) {
                    var radioName = $this.attr('name');
                    if (!formPay.find("input[name='" + radioName + "']:checked").length) {
                        toastr.error('Please select a gender', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        isValid = false;
                        return false;
                    }
                }
            } else if ($this.attr('type') === 'checkbox') {
                if (section == 3) {
                    if (!formPay.find("input[name='workshop_id[]']:checked").length) {
                        toastr.error('Please select a workshop', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        isValid = false;
                        return false;
                    }
                }
                if (section == 4) {
                    if (!formPay.find("input[type='checkbox'][name='accompanyCount']:checked").length) {
                        toastr.error('Please select an accompany', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        isValid = false;
                        return false;
                    }
                }
                if (section == 5) {
                    var isAnyChecked = isAnyCheckboxChecked('checkboxClassDinner', formPay);
                    if (!isAnyChecked) {
                        toastr.error('Please select at least one banquet', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        isValid = false;
                        return false;
                    }
                }
            } else if ($this.prop('tagName').toLowerCase() === 'select') {
                if ($.trim($this.val()) === '') {
                    var msg = $this.attr('validate');
                    if (msg) {
                        toastr.error(msg, 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        isValid = false;
                        return false;
                    }
                }
            }
        });

        formPay.find('.gala-dinner-select').append(galaDinnerDiv);

        return isValid;
    }

    // Show section function
    function showSection(section) {
        $('.section').removeClass('active');
        $('#section' + section).addClass('active');
    }

    // Initialize - hide all checkout sections initially
    $('form .checkout-main-wrap').hide();
});
        function checkUserEmail(obj) {

            var liParent = $(obj).parent().closest("div[use=registrationUserDetails]");
            var emailId = $.trim($(obj).val());
            // alert(emailId);

            if (emailId != '') {
                var filter =
                    /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if (filter.test(emailId)) {
                    // $(obj).hide();

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
                                    $('#abstractDelegateId').val(JSONObject.ID);

                                    toastr.success('You have already registered with this email Id, login now please', 'Success', {
                                        "progressBar": true,
                                        "timeOut": 3000,
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp"
                                    });
                                    setTimeout(function() {

                                        window.location.href = jsBASE_URL;

                                    }, 3000);

                                } else if (JSONObject.STATUS == 'NOT_PAID') {

                                    if (emailId != '') {
                                        $.ajax({
                                            type: "POST",
                                            url: jsBASE_URL + 'login.process.php',
                                            data: 'action=getPaymentVoucharDetails&user_email_id=' + emailId,
                                            dataType: 'json',
                                            async: false,
                                            success: function(JSONObject) {
                                                console.log(JSONObject);

                                                if (JSONObject.error == 400) {
                                                    toastr.error(JSONObject.msg, 'Error', {
                                                        "progressBar": true,
                                                        "timeOut": 3000,
                                                        "showMethod": "slideDown",
                                                        "hideMethod": "slideUp"
                                                    });
                                                } else if (JSONObject.succ == 200) {
                                                    $('#user_email_id').val("");
                                                    $('#loading_indicator').show();
                                                    $('#payModal').hide();
                                                    $('#paymentVoucherBody').append(JSONObject.data);

                                                    $('#slip_id').val(JSONObject.slipId);
                                                    $('#delegate_id').val(JSONObject.delegateId);
                                                    $('#mode').val(JSONObject.invoice_mode);

                                                    $('#loginBtn').prop('disabled', true);

                                                    toastr.success(JSONObject.msg, 'Success', {
                                                        "progressBar": true,
                                                        "timeOut": 2000,
                                                        "showMethod": "slideDown",
                                                        "hideMethod": "slideUp"
                                                    });
                                                    setTimeout(function() {
                                                        $('#loading_indicator').hide();
                                                        $('#paymentVoucherModal').show();
                                                        $('#loginBtn').prop('disabled', false);
                                                        //window.location.href= jsBASE_URL + 'profile.php';

                                                    }, 2000);
                                                }


                                            }
                                        });
                                    }

                                } else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE') {
                                    if (emailId != '') {
                                        $.ajax({
                                            type: "POST",
                                            url: jsBASE_URL + 'login.process.php',
                                            data: 'action=getPaymentVoucharDetails&user_email_id=' + emailId,
                                            dataType: 'json',
                                            async: false,
                                            success: function(JSONObject) {
                                                console.log(JSONObject);

                                                if (JSONObject.error == 400) {
                                                    toastr.error(JSONObject.msg, 'Error', {
                                                        "progressBar": true,
                                                        "timeOut": 3000,
                                                        "showMethod": "slideDown",
                                                        "hideMethod": "slideUp"
                                                    });
                                                } else if (JSONObject.succ == 200) {
                                                    $('#user_email_id').val("");
                                                    $('#loading_indicator').show();
                                                    $('#payModal').hide();
                                                    $('#paymentVoucherBody').append(JSONObject.data);

                                                    $('#slip_id').val(JSONObject.slipId);
                                                    $('#delegate_id').val(JSONObject.delegateId);
                                                    $('#mode').val(JSONObject.invoice_mode);

                                                    $('#loginBtn').prop('disabled', true);

                                                    toastr.success(JSONObject.msg, 'Success', {
                                                        "progressBar": true,
                                                        "timeOut": 2000,
                                                        "showMethod": "slideDown",
                                                        "hideMethod": "slideUp"
                                                    });
                                                    setTimeout(function() {
                                                        $('#loading_indicator').hide();
                                                        $('#paymentVoucherModal').show();
                                                        $('#loginBtn').prop('disabled', false);
                                                        //window.location.href= jsBASE_URL + 'profile.php';

                                                    }, 2000);
                                                }


                                            }
                                        });
                                    }
                                } else if (JSONObject.STATUS == 'PAY_NOT_SET_OFFLINE') {
                                    var payNotSetModalOffline = $('#payNotSetModalOffline');
                                    $(payNotSetModalOffline).modal('show');
                                    $(payNotSetModalOffline).modal('show');

                                    $(obj).show();
                                } else if (JSONObject.STATUS == 'AVAILABLE') {

                                    enableAllFileds(liParent);


                                    var JSONObjectData = JSONObject.DATA;
                                    if (JSONObjectData) {

                                        $('#abstractDelegateId').val(JSONObjectData.ID);
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
                                }
                            }
                        });
                    }, 500);
                } else {
                    toastr.error('Enter Valid Email Id', 'Error', {
                        "progressBar": true,
                        "timeOut": 5000, // 3 seconds
                        "showMethod": "slideDown", // Animation method for showing
                        "hideMethod": "slideUp" // Animation method for hiding
                    });
                }
            } else {
                //popoverAlert(emailIdObj);
            }
        }

        function validateMobile(mobile) {


            if (mobile != '') {
                $.ajax({
                    type: "POST",
                    url: jsBASE_URL + 'returnData.process.php',
                    data: 'act=getMobileValidation&mobile=' + mobile,
                    dataType: 'text',
                    async: false,
                    success: function(returnMessage) {

                        returnMessage = returnMessage.trim();
                        if (returnMessage == 'IN_USE') {
                            //popoverAlert(mobileObj, "Mobile no. is already in use.");
                            toastr.error("Mobile no. is already in use.", 'Error', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });

                        } else {
                            $('#user_details').show();
                            console.log('>>' + $(parent).find(
                                "div[use=mobileProcessing]").find(
                                "input[name=user_mobile_validated]").val());
                        }
                    }
                });
            }

        }

        $(document).on("click", "#pay-button-vouchar", function() {

            //alert(12);
            // Checking if a radio button with name "gender" is checked
            if ($('input[name="card_mode"]:checked').length > 0) {
                $("form[name='frmApplyPayment']").submit();
            } else {
                toastr.error('Please select a payment method', 'Error', {
                    "progressBar": true,
                    "timeOut": 3000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });

                flag = 1;
                return false;
            }
        });


        $("input[type=radio][operationMode=registration_tariff]").each(function() {
            $(this).click(function() {

                $("#bill_details").show();

                var currChkbxStatus = $(this).attr("chkStatus");

                $("input[type=checkbox][operationMode=registration_tariff]").prop(
                    "checked", false);
                $("input[type=checkbox][operationMode=registration_tariff]").attr(
                    "chkStatus", "false");

                $("div[operetionMode=workshopTariffTr]").hide();

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



                if (currChkbxStatus == "true") {
                    $(this).prop("checked", false);
                    $(this).attr("chkStatus", "false");

                    $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                        .hide();
                    $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                        .slideDown();
                    window.location.reload();
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

                        $("div[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                            .show();
                    } else if (regType == 'conference') {
                        $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
                            .hide();
                        $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
                            .hide();

                        $("div[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                            .show();



                        // disable "IAP - NNF NRP FGM" ,"NNF Accredited- Advance NRP" workshop type if registration is selected rather then "Member"


                        $("div[operetionMode=workshopTariffTr][use=" + regClsfId + "]")
                            .find('input[type="radio"]').each(function() {
                                var workshopIDVal = $(this).val();
                                // alert(workshopIDVal);
                                //$(this).attr("disabled","");
                                $(this).removeAttr('disabled');
                                //var workshop_type_id = $(this).val();

                                var workshop_amount = $(this).attr('amount');
                                var workshopCount = $(this).attr('workshopCount');

                                $('.workCombo[operetionDisplay=workshopDisplay' + workshopIDVal + ']').find('.itemPrice').text("INR " + workshop_amount);
                                //console.log(workshop_type_id)
                                //if(workshop_type_id == 11 && regClsfId != 1){
                                if (workshop_amount == 0 && regClsfId != 1) {
                                    /*$(this).attr("disabled", "disabled");
                                    $(this).parent().css({
                                        "cursor": "not-allowed"
                                    })*/
                                    //}else if(workshop_type_id == 21 && regClsfId != 1){
                                } else if (workshop_amount == 0 && regClsfId != 1) {
                                    /*$(this).attr("disabled", "disabled");
                                    $(this).parent().css({
                                        "cursor": "not-allowed"
                                    })*/
                                } else if (workshopCount < 1) {
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


                }

                calculateTotalAmount();
            });
        });

        $("input[type=radio][operationMode=workshopId]").each(function() {
            $(this).click(function() {

                var currChkbxStatus = $(this).attr("chkStatus");



                if (currChkbxStatus == "true") {
                    $(this).prop("checked", false);
                    $(this).attr("chkStatus", "false");
                } else {
                    $(this).prop("checked", true);
                    $(this).attr("chkStatus", "true");
                }

                calculateTotalAmount();


            });
        });

        // $("input[type=checkbox][use=accompanyCountSelect]").click(function() {
        //     var count = parseInt($(this).val());

        //     calculateTotalAmount();
        // });

        $(document).on("click", "input[type=checkbox], input[type=radio]", function() {

            var key = $(this).attr('key');
            if (key != undefined && key != '') {

                if ($("#accomodation_package_checkin_id" + key).is(":checked")) {
                    console.log("Radio button with ID 'radioButton1' is checked");
                    calculateTotalAmount();
                } else {

                    alert('Please select check in date first')
                    return false;
                }
            } else {
                calculateTotalAmount();
            }

            //alert(key);


        });

        // function calculateTotalAmount() {
        //     console.log("====calculateTotalAmount====");

        //     var totalAmount = 0;
        //     var totTable = $("table[use=totalAmountTable]");
        //     $(totTable).children("tbody").find("tr").remove();
        //     var gst_flag = $('#gst_flag').val();


        //     $('input[type=checkbox]:checked,input[type=radio]:checked,#accomodation_package_checkout_id option').each(function() {
        //         var attr = $(this).attr('amount');
        //         var operation = $(this).attr('operationmodetype');
        //         var regtype = $(this).attr('regtype');
        //         var reg = $(this).attr('reg');

        //         var package = $(this).attr('package');
        //         var key = $(this).attr('key');

        //         //alert(11)

        //         if (typeof attr !== typeof undefined && attr !== false) {
        //             var amt = parseFloat(attr);


        //             if (typeof package !== typeof undefined && package !== false) {



        //                 // alert(checkedValue);

        //                 var checkInVal = $('#accomodation_package_checkin_id').val();
        //                 var checkOutVal = $('#accomodation_package_checkout_id').val();

        //                 console.log('checkInVal====', checkInVal)
        //                 console.log('checkOutVal====', checkOutVal)





        //                 if (checkInVal !== undefined && checkOutVal !== undefined) {




        //                     const checkInArray = checkInVal.split("/");
        //                     var checkInID = checkInArray[0];
        //                     var checkInDate = checkInArray[1];

        //                     //alert('checkindate',checkInDate);

        //                     const checkOutArray = checkOutVal.split("/");
        //                     var checkOutID = checkOutArray[0];
        //                     var checkOutDate = checkOutArray[1];


        //                     var date1 = new Date(checkInDate);
        //                     var date2 = new Date(checkOutDate);

        //                     // Calculate the difference in milliseconds
        //                     var differenceMs = Math.abs(date2 - date1);

        //                     var accommodation_room = $('#accommodation_room').val();
        //                     if (typeof accommodation_room !== typeof undefined && accommodation_room !== false && !isNaN(accommodation_room)) {
        //                         var roomQty = accommodation_room;
        //                     } else {
        //                         var roomQty = 1;
        //                     }

        //                     var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));


        //                     console.log('accoAmnt=' + differenceDays);
        //                     var amt = parseFloat(amt) * parseInt(differenceDays) * parseInt(roomQty);

        //                     if (isNaN(amt)) {
        //                         amt = 0;
        //                     }
        //                 }



        //             }
        //             if (regtype !== 'combo') {

        //                 if (gst_flag == 1) {
        //                     var cgstP = <?= $cfg['INT.CGST'] ?>;
        //                     var cgstAmnt = (amt * cgstP) / 100;

        //                     var sgstP = <?= $cfg['INT.SGST'] ?>;
        //                     var sgstAmnt = (amt * sgstP) / 100;

        //                     var totalGst = cgstAmnt + sgstAmnt;
        //                     var totalGstAmount = cgstAmnt + sgstAmnt + amt;
        //                     totalAmount = totalAmount + totalGstAmount;
        //                 } else {
        //                     totalAmount = totalAmount + amt;
        //                 }

        //                 if (reg != undefined && reg == 'reg') {
        //                     $('#confPrc').text((amt).toFixed(2));
        //                 }

        //             }

        //             console.log(">>amt" + amt + ' ==> ' + totalAmount);

        //             var attrReg = $(this).attr('operationMode');
        //             var isConf = false;
        //             if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
        //                 'registration_tariff') {
        //                 isConf = true;
        //             }
        //             var isMastCls = false;
        //             if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
        //                 'workshopId') {
        //                 isMastCls = true;
        //             }

        //             // november22 workshop related work by weavers start

        //             var isNovWorkshop = false;
        //             if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
        //                 'workshopId_nov') {
        //                 isNovWorkshop = true;
        //             }

        //             // november22 workshop related work by weavers end

        //             var cloneIt = false;
        //             var amtAlterTxt = 'Complimentary';

        //             if (amt > 0) {
        //                 cloneIt = true;
        //             } else if (isConf) {
        //                 cloneIt = true;
        //                 amtAlterTxt = 'Complimentary'
        //             } else if (isMastCls || isNovWorkshop) {
        //                 cloneIt = true;
        //                 amtAlterTxt = 'Included in Registration'
        //             }

        //             if (cloneIt) {

        //                 //alert($(this).attr('invoiceTitle'));
        //                 var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
        //                     .clone();
        //                 $(cloned).attr("use", "rowCloned");
        //                 var imageElement = $('<img>').attr('src', "< _BASE_URL_ ?>" + $(this).attr('icon'));
        //                 //alert("< _BASE_URL_ ?>"+$(this).attr('icon'));
        //                 $(cloned).find("span[use=icon]").append(imageElement);
        //                 $(cloned).find("span[use=invTitle]").append($(this).attr('invoiceTitle'));
        //                 if (regtype === 'combo') {

        //                     $(cloned).find("span[use=amount]").text((amt > 0) ? ('Included') : amtAlterTxt);
        //                 } else {

        //                     $(cloned).find("span[use=amount]").text((amt > 0) ? (amt).toFixed(2) : amtAlterTxt);
        //                 }

        //                 $(cloned).show();
        //                 $(totTable).children("tbody").append(cloned);
        //             }

        //             if (regtype !== 'combo') {

        //                 if (gst_flag == 1) {

        //                     if (cloneIt) {

        //                         var cgstP = <?= $cfg['INT.CGST'] ?>;
        //                         var cgstAmnt = (amt * cgstP) / 100;

        //                         var sgstP = <?= $cfg['INT.SGST'] ?>;
        //                         var sgstAmnt = (amt * sgstP) / 100;

        //                         var totalGst = cgstAmnt + sgstAmnt;
        //                         var totalGstAmount = cgstAmnt + sgstAmnt + amt;


        //                         var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
        //                             .clone();
        //                         $(cloned).attr("use", "rowCloned");
        //                         $(cloned).find("span[use=invTitle]").text("GST 18%");
        //                         $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
        //                         $(cloned).show();
        //                         $(totTable).children("tbody").append(cloned);
        //                     }
        //                 }
        //             }
        //         }

        //         if ($(this).attr('operationMode') == 'registrationMode' && $(this).attr('use') ==
        //             'tariffPaymentMode') {

        //             if ($(this).val() == 'ONLINE') {
        //                 var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
        //                 var internetAmount = (totalAmount * internetHandling) / 100;
        //                 totalAmount = totalAmount + internetAmount;

        //                 console.log(">>amt" + internetAmount + ' ==> ' + totalAmount);



        //                 var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
        //                     .clone();

        //                 $(cloned).attr("use", "rowCloned");
        //                 $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
        //                 $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
        //                 $(cloned).show();
        //                 $(totTable).children("tbody").append(cloned);
        //             }
        //         }
        //     });

        //     totalAmount = Math.round(totalAmount, 0);


        //     $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
        //     $("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
        //     $("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount', totalAmount);
        //     $("div[use=totalAmount]").show();

        //     $('#subTotalPrc').text((totalAmount).toFixed(2));

        //     totTable.show();
        // }

          function calculateTotalAmountOld() {
            console.log("====calculateTotalAmount====");
            var totalAmount = 0;
            var totalDinnerAmount = 0;
            var subTotalAmount = 0;
            var totalGstAmt = 0;
            var totTable = $("ul[use=totalAmountTable]");
            $(totTable).find("li[use='rowCloned']").remove();
            // $(totTable).find("li").remove();
            var gst_flag = $('#gst_flag').val();
          
            var dinnerFlag = false;

            $('input[type=checkbox]:checked,input[type=radio]:checked,#accomodation_package_checkout_id option,#accommodation_room option').each(function() {

                var attr = $(this).attr('amount');
                var operation = $(this).attr('operationmodetype');
                var regtype = $(this).attr('regtype');
                var reg = $(this).attr('reg');
                var qty = $(this).attr('qty');
                console.log('Qty=' + qty);
                var hasTotalAmntFlag = false;

                // alert(attr);

                var package = $(this).attr('package');

                //alert(11)

                if (typeof attr !== typeof undefined && attr !== false) {
                    var amt = parseFloat(attr);


                    if (typeof package !== typeof undefined && package !== false) {



                        // alert(checkedValue);

                        /*var checkInVal = $("input[name='accomodation_package_checkin_id']:checked").val();
                        var checkOutVal = $("input[name='accomodation_package_checkout_id']:checked").val();*/

                        var checkInVal = $('#accomodation_package_checkin_id').val();
                        var checkOutVal = $('#accomodation_package_checkout_id').val();

                        console.log('checkInVal====', checkInVal)
                        // alert(checkInVal);


                        if (checkInVal !== undefined && checkOutVal !== undefined) {
                            const checkInArray = checkInVal.split("/");
                            var checkInID = checkInArray[0];
                            var checkInDate = checkInArray[1];

                            //alert('checkindate',checkInDate);

                            const checkOutArray = checkOutVal.split("/");
                            var checkOutID = checkOutArray[0];
                            var checkOutDate = checkOutArray[1];


                            var date1 = new Date(checkInDate);
                            var date2 = new Date(checkOutDate);

                            // Calculate the difference in milliseconds
                            var differenceMs = Math.abs(date2 - date1);

                            var accommodation_room = $('#accommodation_room').val();
                            if (typeof accommodation_room !== typeof undefined && accommodation_room !== false && !isNaN(accommodation_room)) {
                                var roomQty = accommodation_room;
                            } else {
                                var roomQty = 1;
                            }

                            console.log('room qty=' + roomQty);

                            var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));


                            console.log('accoAmnt=' + differenceDays);
                            var amt = parseFloat(amt) * parseInt(differenceDays) * parseInt(roomQty);

                            if (isNaN(amt)) {
                                amt = 0;
                            }

                            hasTotalAmntFlag = true;
                        }



                    }
                    if (regtype !== 'combo') {

                        if (gst_flag == 1) {
                            if (isNaN(amt)) {

                            } else {
                                var cgstP = <?= $cfg['INT.CGST'] ?>;
                                var cgstAmnt = (amt * cgstP) / 100;

                                var sgstP = <?= $cfg['INT.SGST'] ?>;
                                var sgstAmnt = (amt * sgstP) / 100;

                                var totalGst = cgstAmnt + sgstAmnt;
                                var totalGstAmount = cgstAmnt + sgstAmnt + amt;
                                 
                                totalAmount = totalAmount + totalGstAmount;
                                totalGstAmt = totalGstAmt+totalGst;
                                subTotalAmount =subTotalAmount+amt;
                            }

                        } else {
                            if (isNaN(amt)) {

                            } else {
                                totalAmount = totalAmount + amt;
                                subTotalAmount =totalAmount;
                            }


                        }

                        console.log('reg===' + reg);

                        if (reg != undefined && reg == 'reg') {
                            if (isNaN(amt)) {
                                $('#confPrc').text(0.00.toFixed(2));
                            } else {
                                $('#confPrc').text((amt).toFixed(2));
                            }

                        }

                        //alert(reg);

                        if (reg != undefined && reg == 'workshop') {
                            if (isNaN(amt)) {
                                $('#workshopPrc').text(0.00.toFixed(2));
                            } else {
                                $('#workshopPrc').text((amt).toFixed(2));
                            }

                            if (Number(amt) > 0) {
                                $('#wrkshopPrcdiv').show();
                            }

                        } else {
                            $('#wrkshopPrcdiv').hide();

                        }

                        if (reg != undefined && reg == 'accompany') {
                            if (isNaN(amt)) {
                                $('#accompanyPrc').text(0.00.toFixed(2));
                            } else {
                                $('#accompanyPrc').text((amt).toFixed(2));
                            }

                            $('.accompanyPrcdiv').show();
                        } else {
                            $('#accompanyPrcdiv').hide();

                        }

                        if (reg != undefined && reg == 'dinner' && qty != undefined) {

                            var checkedCount = $('.checkboxClassDinner:checked').length;
                            console.log("Number of checked checkboxes: " + checkedCount);

                            var totalDinnerAmounts = checkedCount * amt;

                            $('#dinnerPrc').text((totalDinnerAmounts).toFixed(2));
                            $('.dinnerPrcdiv').show();

                        } else {
                            $('#dinnerPrcdiv').hide();

                        }

                    }


                    console.log(">>amt" + amt + ' ==> ' + totalAmount);

                    var attrReg = $(this).attr('operationMode');
                    var isConf = false;
                    if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'registration_tariff') {
                        isConf = true;
                    }
                    var isMastCls = false;
                    if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId') {
                        isMastCls = true;
                    }

                    // november22 workshop related work by weavers start

                    var isNovWorkshop = false;
                    if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId_nov') {
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
                        // alert($(this).attr('invoiceName'));
                           if(cloneIt) {
                                const el = $(this);

                                const title  = el.attr('invoiceTitle');
                                const name   = el.attr('invoiceName');
                                const amount = parseFloat(el.attr('amount')).toFixed(2);
                                const icon   = el.attr('icon');
                                const reg    = el.attr('reg');
                                // alert(name);
                                const cloned = $(totTable).find("li[use=rowCloneable]").first().clone();
                                $(cloned).attr("use", "rowCloned");

                                // set icon
                                if(icon) {
                                    const img = $('<img>').attr('src', "<?= _BASE_URL_ ?>" + icon);
                                    $(cloned).find("[use=icon]").append(img);
                                }

                                // set title, name, amount
                               cloned.find("[use=invTitle]").html(`${title}<br>${name}`);
                                $(cloned).find("[use=invName]").text(name);
                                $(cloned).find("[use=amount]").text("₹ " + amount);

                                // delete button if needed
                                if(reg != 'reg') {
                                    const deleteBtn = $('<i></i>')
                                        .addClass('fas fa-times delete-accompany-btn')
                                        .attr('reg', reg)
                                        .attr('val', el.val())
                                        .text('delete');
                                    $(cloned).find("[use=deleteIcon]").append(deleteBtn).show();
                                }

                                $(cloned).show();
                               const subtotalRow = $(totTable).find('li[use=subtotalRow]');
                               $(cloned).insertBefore(subtotalRow);
                            }
                            if (reg != 'reg') {
                            // <i class="fas fa-times"></i>
                            var deleteLink = $('<i></i>')
                                .attr('id', 'deleteItem')
                                .attr('class', 'fas fa-times delete-accompany-btn')
                                .attr('reg', reg)
                                .attr('val', $(this).attr('value'))
                                .attr('regClsId', $(this).attr('registrationclassfid'))
                                .text('delete')
                            $(cloned).find("span[use=deleteIcon]").append(deleteLink);
                            $(cloned).find("span[use=deleteIcon]").show();
                        }


                        $(cloned).show();
                        $(totTable).append(cloned);


                    }
                    if (regtype !== 'combo') {
                        if (gst_flag == 1) {
                            if (cloneIt) {
                                var cgstP = <?= $cfg['INT.CGST'] ?>;
                                var cgstAmnt = (amt * cgstP) / 100;

                                var sgstP = <?= $cfg['INT.SGST'] ?>;
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
                }

                if ($(this).attr('operationMode') == 'registrationMode' && $(this).attr('use') ==
                    'tariffPaymentMode') {

                    if ($(this).val() == 'ONLINE') {
                        var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
                        var internetAmount = (totalAmount * internetHandling) / 100;
                        totalAmount = totalAmount + internetAmount;

                        console.log(">>amt" + internetAmount + ' ==> ' + totalAmount);



                        var cloned = $(totTable).find("li[use=rowCloneable]").first()
                            .clone();

                        $(cloned).attr("use", "rowCloned");
                        $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
                        $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
                        $(cloned).show();
                        $(totTable).append(cloned);
                    }
                }
            });

            totalAmount = Math.round(totalAmount, 0);
            totalDinnerAmount = Math.round(totalDinnerAmount, 0);
            subTotalAmount = Math.round(subTotalAmount, 0);
            totalGstAmt= Math.round(totalGstAmt, 0);

            

            $(totTable).find("span[use=totalAmount]").text((totalAmount).toFixed(2));
            $("div[use=totalAmount]").find("h3[use=totalAmount]").text((totalAmount).toFixed(2));
            $("div[use=totalAmount]").find("h3[use=totalAmount]").attr('theAmount', totalAmount);
            $("div[use=totalAmount]").show();
            $('[use=subtotalAmount]').text(
                '₹ ' + subTotalAmount.toLocaleString('en-IN')
            );
             $('[use=totalGstAmount]').text(
                '₹ ' + totalGstAmt.toLocaleString('en-IN')
            );
            $('[use=totalAmountpay]').text(
                '₹ ' + totalAmount.toLocaleString('en-IN')
            );
           $('#subTotalPrc').text(
                '₹ ' + totalAmount.toLocaleString('en-IN')
            );
            
        }
     
        function getAccommodationDetails(hotel_id) {

            if (hotel_id > 0) {

                //alert(jsBASE_URL);
                var abstractDelegateId = '<?= $delegateId ?>';
                $.ajax({
                    type: "POST",
                    url: jsBASE_URL + 'returnData.process.php',
                    data: 'act=getAccommodationDetailsProfile&hotel_id=' + hotel_id + '&abstractDelegateId=' + abstractDelegateId,
                    async: false,
                    dataType: 'html',
                    success: function(JSONObject) {
                        $('#section7').html(JSONObject);
                        $('.section').removeClass('active');
                        $('#section7').addClass('active');
                    }
                });
            }
        }

        function get_checkin_val(val) {
            if (typeof val !== 'undefined' && val != '') {
                var checkOutVal = $('#accomodation_package_checkout_id').val("");
            }
        }

        function get_checkout_val(val) {
            // alert(val);
            var checkInVal = $('#accomodation_package_checkin_id').val();
            var checkOutVal = val;
            const checkInArray = checkInVal.split("/");
            var checkInID = checkInArray[0];
            var checkInDate = checkInArray[1];

            const checkOutArray = checkOutVal.split("/");
            var checkOutID = checkOutArray[0];
            var checkOutDate = checkOutArray[1];

            var date1 = new Date(checkInDate);
            var date2 = new Date(checkOutDate);

            // Calculate the difference in milliseconds
            var differenceMs = Math.abs(date2 - date1);

            // Convert the difference to days
            var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));

            var totalAmount = 0;
            var totTable = $("table[use=totalAmountTable]");
            $(totTable).children("tbody").find("tr").remove();
            var gst_flag = $('#gst_flag').val();
            var cloneIt = false;
            $("input[name=package_id]").each(function() {
                if ($(this).prop('checked') == true) {
                    var packageID = $(this).val();
                    var amount = ($(this).attr('amount'));
                    var amountIncludedDay = parseFloat(amount) * parseInt(differenceDays);
                    calculateTotalAmount();

                }
            });


        }



        function getPackageVal(val) {
            if (typeof val !== 'undefined' && val != '') {

                $("input[name='accomodation_package_checkout_id']").prop('checked', false);
                $("input[name='accomodation_package_checkin_id']").prop('checked', false);
                calculateTotalAmount();
            }
        }


        $(document).on("click", "#paynowbtn", function() {

            $('#checkout-main-wrap').show();

        });

        $(document).on("change", "#accommodation_room", function() {

            calculateTotalAmount();
        });

    





        function payNow(section, formName) {

            var form = $("#" + formName);

            var selectedOption = form.find("input[type=radio][name='payment_mode']:checked").val();
            var specSelected = selectedOption + section;
            var flag = 0;

            // alert(specSelected);

            if (selectedOption) {

                form.find("div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='text'], div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='date'], div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='radio'],div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='file']").each(function() {

                    var form = $(this).closest('form');


                    if ($(this).attr('type') === 'radio') {

                        if (!$("input[type='radio'][name='card_mode']:checked").length) {

                            toastr.error('Please select the card', 'Error', {
                                "progressBar": true,
                                "timeOut": 4000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });


                            flag = 1;
                            return false;
                        }
                    } else {

                        var textBoxValue = $(this).val();
                        // alert(textBoxValue);
                        if (textBoxValue === '') {
                            toastr.error($(this).attr('validate'), 'Error', {
                                "progressBar": true,
                                "timeOut": 4000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });


                            flag = 1;
                            return false;


                        }
                    }


                });
            } else {
                //alert("No option selected!");
                toastr.error('Please select payment mode', 'Error', {
                    "progressBar": true,
                    "timeOut": 4000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });

                flag = 1;
            }

            if (flag == 0) {
                //alert(1212);

                $("form[name='" + formName + "']").submit();
            }

        }


        $(document).ready(function() {
            // Get the URL parameters
            var urlParams = new URLSearchParams(window.location.search);

            // Get the value of the 'id' parameter from the URL
            var idParam = urlParams.get('id');

            // Check if the 'id' parameter is empty or not
            if (idParam === null || idParam.trim() === '') {
                console.log("ID parameter is empty or not found in the URL.");
            } else {
                console.log("ID parameter is present and not empty. ID: " + idParam);
                getAccommodationDetails(idParam);
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Show popup on QR image click
            document.querySelectorAll('.for-upi-only img').forEach(function(img) {
                img.addEventListener('click', function() {
                    var popup = document.getElementById('qrPopupOverlay');
                    popup.querySelector('img').src = this.src;
                    popup.style.display = 'flex';
                });
            });

            // Close popup when clicking X
            document.querySelector('#qrPopupOverlay .closePopup').addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });

            // Close when clicking outside image
            document.getElementById('qrPopupOverlay').addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
        });
        function sendUrlabstract() {
            var user_email_id = $('#user_email_id').val();
            var abstractDelegateId = $('#abstractDelegateId').val();

            localStorage.setItem("user_email_id", user_email_id);
             let url = '<?= _BASE_URL_ ?>abstract_user.php?abstractDelegateId=' + abstractDelegateId;

             window.open(url, '_blank'); // ✅ always opens in new tab
        }
        function sendUrlPayment() {
           
            var user_email_id = $('#user_email_id').val();
                $.ajax({
                    type: "POST",
                    url: '<?= _BASE_URL_ ?>login.process.php',
                    data: 'action=getPaymentVoucharDetails&user_email_id=' + user_email_id + '&slipId=<?= $resSlip[0]['id'] ?>',                    dataType: 'json',
                    async: false,
                    success: function(JSONObject) {
                    if (JSONObject.error == 400) {
                        toastr.error(JSONObject.msg, 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                        });
                    } else if (JSONObject.succ == 200) {
                        
                        toastr.success('Redirecting to payment page...', 'Success', {
                            progressBar: true,
                            timeOut: 1500
                        });

                        setTimeout(function() {
                              let urlPay =  '<?= _BASE_URL_ ?>registration_payment.php';
                            window.open(urlPay, '_blank'); // ✅ always opens in new tab
                        }, 1500);
                     
                    }


                    }
                });
        }
         function sendUrl() {
            var user_email_id = $('#user_email_id').val();
                var abstractDelegateId = $('#abstractDelegateId').val();

            localStorage.setItem("user_email_id", user_email_id);

             let url = '<?= _BASE_URL_ ?>registration.php?abstractDelegateId=' + abstractDelegateId;
            window.open(url, '_blank'); // ✅ always opens in new tab

        }
       document.querySelectorAll('.abstractView').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();

            const submissionCode = this.dataset.id;

            // Show the popup
            const popup = document.getElementById('abstractdetails');
            popup.style.display = 'block';

            // Hide all abstract boxes first
            document.querySelectorAll('.hotel_box_abstract').forEach(box => box.style.display = 'none');

            // Show only the selected abstract box
            const selectedBox = document.getElementById(submissionCode);
            if(selectedBox) selectedBox.style.display = 'block';

            // Hide all tabs first
            document.querySelectorAll('.hotel_tab_btn').forEach(tab => tab.style.display = 'none');

            // Show only the tab corresponding to this submission code
            const activeTab = document.querySelector(`.hotel_tab_btn[data-tab="${submissionCode}"]`);
            if(activeTab) activeTab.style.display = 'inline-block'; // or 'block' depending on your styling
        });
    });

    // Close popup
    document.querySelectorAll('.popup_close').forEach(btn => {
        btn.addEventListener('click', function(){
            const popup = this.closest('#abstractdetails');
            popup.style.display = 'none';

            // Optionally, reset tabs to show all again when popup is closed
            document.querySelectorAll('.hotel_tab_btn').forEach(tab => tab.style.display = 'inline-block');
        });
    });
    </script>
   
</html>
<? 
function getCategoryName($id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `category` 
												   FROM "._DB_ABSTRACT_TOPIC_CATEGORY_."
												 WHERE `id` = '".$id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['category'];
}
function cleanFields($fields){
    $fields = json_decode($fields, true);

    if(!is_array($fields)){
        return [];
    }

    // remove empty values like "", null
    $fields = array_filter($fields, function($val){
        return $val !== null && $val !== '';
    });

    // ensure integers only (security)
    return array_map('intval', $fields);
}
?>

<script>
    $(document).on('click', '#addstay .next.action-button', function (e) {
    e.preventDefault();
    var $form = $(this).closest('form');

    if ($form.find("input[name='package_id[]']:checked").length === 0) {
        toastr.error('Please select at least one accommodation option', 'Error', {
            progressBar: true, timeOut: 3000, showMethod: "slideDown", hideMethod: "slideUp"
        });
        return;
    }

    // clone the already-rendered accommodation summary into the checkout screen
    $form.find('.accom_checkout_summary').html($form.find('.accomdationprice_total').html());

    // pull the grand total already computed by renderAccommodationSummary()
    var grandTotal = parseFloat($form.find('.accomdation_total h5 span').text()) || 0;
    var gstFlag = $('#gst_flag').val();
    var gst = (gstFlag == 1) ? (grandTotal * 0.18) : 0;
    var totalPayable = grandTotal + gst;

    $form.find('[use=subtotalAmount]').text('₹ ' + grandTotal.toFixed(2));
    $form.find('[use=totalGstAmount]').text('₹ ' + gst.toFixed(2));
    $form.find('[use=totalAmountpay]').text('₹ ' + totalPayable.toFixed(2));

    $form.find('#addstay').hide();
    $form.find('.checkout-main-wrap').show();

    $form.find('input[use=payment_mode_select]:first').trigger('click');
});

$(document).on('click', '#addstay .checkout-back-btn, .checkout-main-wrap .checkout-back-btn', function (e) {
    e.preventDefault();
    var $form = $(this).closest('form');
    $form.find('.checkout-main-wrap').hide();
    $form.find('#addstay').show();
});


$('#frmAddaccomodationfromProfile').on('submit', function (e) {
    var $form = $(this);
    var selectedOption = $form.find("input[type=radio][name='payment_mode']:checked").val();
    var flag = 0;

    if (!selectedOption) {
        toastr.error('Please select payment mode', 'Error', { progressBar: true, timeOut: 4000 });
        flag = 1;
    } else {
        $form.find(".review_right_box:visible input.mandatory").each(function () {
            if ($.trim($(this).val()) === '') {
                toastr.error($(this).attr('validate'), 'Error', { progressBar: true, timeOut: 4000 });
                flag = 1;
                return false;
            }
        });
    }

    if (flag > 0) {
        e.preventDefault();
        return false;
    }
});

</script>
<script>
    ///////////accomodation start//////////
var hotelDates = {};
var selectedQty = {}; // NEW: keyed by package checkbox value -> quantity, survives summary re-renders
var availabilityCheckInProgress = false;
var lastToastTime = 0;
var toastCooldown = 2000;
var isAccoPluginsInitialized = false;

$(document).ready(function() {
    $('.hotel_tab_btn_hotel').click(function() {
        var hotelId = $(this).data('tab');
        var notes = $(this).data('notes');
         $('#hotelNotesText').text(notes && notes.trim() !== '' ? notes : '');

        var currentHotelId = $('#hotel_id').val();
        if (currentHotelId) {
            hotelDates[currentHotelId] = {
                checkin: $('#accomodation_package_checkin_id_' + currentHotelId).val(),
                checkout: $('#accomodation_package_checkout_id_' + currentHotelId).val(),
                packages: $("input[name='package_id[]'][data-hotel-id='" + currentHotelId + "']:checked").map(function() {
                    return $(this).val();
                }).get(),
                roomQty: {}
            };
            
            $("input[name^='accmomdation-qty[" + currentHotelId + "]']").each(function() {
                var inputName = $(this).attr('name');
                hotelDates[currentHotelId].roomQty[inputName] = $(this).val();
            });
        }

        $.ajax({
            url: 'profile.php',
            type: 'POST',
            data: { hotelIdDate: hotelId },
            success: function(response) {
                $('#accoOp').html($(response).find('#accoOp').html());
                initializeAccoPlugins();
                $('#hotel_id').val(hotelId);

                if (hotelDates[hotelId]) {
                    $('#accomodation_package_checkin_id_' + hotelId).val(hotelDates[hotelId].checkin || '');
                    $('#accomodation_package_checkout_id_' + hotelId).val(hotelDates[hotelId].checkout || '');

                    if (hotelDates[hotelId].packages) {
                        hotelDates[hotelId].packages.forEach(function(pkgId) {
                            $("input[name='package_id[]'][value='" + pkgId + "']").prop('checked', true);
                        });
                    }

                    if (hotelDates[hotelId].roomQty) {
                        for (var qtyName in hotelDates[hotelId].roomQty) {
                            var $input = $("input[name='" + qtyName + "']");
                            if ($input.length) {
                                $input.val(hotelDates[hotelId].roomQty[qtyName]);
                            }
                        }
                    }

                    hotelDates[hotelId].packages.forEach(function(pkgId) {
                        var $pkg = $("input[name='package_id[]'][value='" + pkgId + "']");
                        if (!$pkg.length) return;
                        
                        var roomId = $pkg.attr('accommodation_room');
                        var isHotelLevel = $pkg.data('is-hotel-level') === true || roomId === '0' || roomId === 0;
                        var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
                        
                        var qty = 1;
                        
                        if (isHotelLevel) {
                            var qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][0]']");
                            if (!qtyInput.length) {
                                qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][]']");
                            }
                            if (qtyInput.length) {
                                qty = parseInt(qtyInput.val()) || 1;
                            }
                        } else if (roomId) {
                            var qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']");
                            if (qtyInput.length) {
                                qty = parseInt(qtyInput.val()) || 1;
                            }
                        }

                        selectedQty[pkgId] = qty; // NEW: keep the qty store in sync when restoring a tab

                        var checkInVal = $('#accomodation_package_checkin_id_' + hotelId).val();
                        var checkOutVal = $('#accomodation_package_checkout_id_' + hotelId).val();
                        var nights = 1;
                        if (checkInVal && checkOutVal) {
                            var checkIn = new Date(checkInVal.split("/")[1]);
                            var checkOut = new Date(checkOutVal.split("/")[1]);
                            nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                            nights = Math.max(1, nights);
                        }

                        var totalAmount = baseAmount * qty * nights;
                        var totalAmountPerNight = baseAmount * nights;

                        $pkg.attr('amount', totalAmount.toFixed(2));
                        $pkg.closest('label').find('.package-price').text('₹ ' + totalAmountPerNight.toFixed(2));
                    });
                }

                calculateTotalAmount();
               renderAccommodationSummary();
            }
        });
    });

    $('.hotel_tab_btn_hotel').first().click();
});

function syncHotelDates(hotelId) {
    var checkin = $('#accomodation_package_checkin_id_' + hotelId).val() || '';
    var checkout = $('#accomodation_package_checkout_id_' + hotelId).val() || '';

    var html = `
        <input type="hidden" name="hotel_dates[${hotelId}][checkin]" value="${checkin}">
        <input type="hidden" name="hotel_dates[${hotelId}][checkout]" value="${checkout}">
    `;

    $('#hotelDateStore')
        .find('[data-hotel="' + hotelId + '"]').remove();

    $('#hotelDateStore').append(
        `<div data-hotel="${hotelId}">${html}</div>`
    );
}
function renderAccommodationSummary() {
    // capture the currently visible hotel's dates before rendering
    var activeHotelId = $('#hotel_id').val();
    if (activeHotelId) {
        hotelDates[activeHotelId] = hotelDates[activeHotelId] || {};
        var ci = $('#accomodation_package_checkin_id_' + activeHotelId).val();
        var co = $('#accomodation_package_checkout_id_' + activeHotelId).val();
        if (ci) hotelDates[activeHotelId].checkin = ci;
        if (co) hotelDates[activeHotelId].checkout = co;
    }

    var $ul = $('.accomdationprice_total').empty();
    var $checked = $("input[type=checkbox][name='package_id[]']:checked");

    if (!$checked.length) {
        $ul.append('<li class="no-selection"><p>No accommodation selected</p></li>');
        $('.accomdation_total h5 span').text((0).toFixed(2));
        $('#selectedAccoCount').text(0); // NEW
        return;
    }

    var grandTotal = 0;

    $checked.each(function() {
        var $chk = $(this);
        var hotelId   = $chk.data('hotel-id');
        var hotelName = $chk.data('hotel-name') || '';
        var roomId    = $chk.attr('accommodation_room');
        var isHotelLevel = $chk.data('is-hotel-level') === true || roomId === '0' || roomId === 0;
        var pkgLower  = $chk.closest('label').find('n').text().trim().toLowerCase();
        var roomLabel = $chk.data('room-name') || $chk.data('package-name') || (isHotelLevel ? 'Hotel Stay' : 'Accommodation');
        var maxLimit = $chk.data('package-name') === 'Sharing' ? 1 : ($chk.data('max-limit') || 2);

        var dates = hotelDates[hotelId] || {};
        var checkIn  = dates.checkin  ? dates.checkin.split('/')[1]  : '';
        var checkOut = dates.checkout ? dates.checkout.split('/')[1] : '';

        var nights = 1;
        if (checkIn && checkOut) {
            nights = Math.max(1, Math.ceil((new Date(checkOut) - new Date(checkIn)) / 86400000));
        }

        var qtyName = "accmomdation-qty[" + hotelId + "][" + (isHotelLevel ? 0 : roomId) + "]";
        var qty = pkgLower === 'sharing'
            ? 1
            : (selectedQty[$chk.val()] || 1); // CHANGED: read from the qty store, not from the (about-to-be-destroyed) DOM input

        var amount = parseFloat($chk.attr('amount')) || 0;
        grandTotal += amount;

        var occupancyLabel = pkgLower === 'sharing'
            ? '1 Person (0.5 Room)'
            : Math.floor(qty) + ' Room' + (qty > 1 ? 's' : '');

        var dateLabel = (checkIn && checkOut) ? (checkIn + ' to ' + checkOut) : 'Dates not selected';

        var $li = $(
            '<li data-package-value="' + $chk.val() + '" data-hotel-id="' + hotelId + '" data-room-id="' + roomId + '">' +
                '<p>' + escapeHtml(hotelName) +
                    '<n>' + escapeHtml(roomLabel) + '</n>' +
                    '<span>' + nights + ' Night(s) Stay (' + dateLabel + ') &middot; ' + occupancyLabel + '</span>' +
                '</p>' +
                '<div class="accomdationroomqty-input">' +
                    '<button class="qty-count qty-count--minus" data-action="minus" type="button"' + (pkgLower === 'sharing' ? ' disabled' : '') + '>-</button>' +
                    '<input class="accmomdation-qty" type="number" name="' + qtyName + '" min="1" max="'+ maxLimit +'" value="' + Math.floor(qty) + '"' + (pkgLower === 'sharing' ? ' disabled' : '') + '>' +
                    '<button class="qty-count qty-count--add" data-action="add" type="button"' + (pkgLower === 'sharing' ? ' disabled' : '') + '>+</button>' +
                '</div>' +
                '<h5>Room Stay Total<span>&#8377; ' + amount.toFixed(2) + '</span></h5>' +
                '<button type="button" class="summary-remove-btn">Remove</button>' +
            '</li>'
        );

        $ul.append($li);
    });

    $('.accomdation_total h5 span').text(grandTotal.toFixed(2));
    $('#selectedAccoCount').text($checked.length); // NEW
}
$(document).on('click', '.accomdationprice_total .summary-remove-btn', function() {
    var $li = $(this).closest('li');
    var pkgValue = $li.data('package-value');
    var $chk = $("input[name='package_id[]'][value='" + pkgValue + "']");

    delete selectedQty[pkgValue]; // NEW: drop the stored qty for the removed selection

    if ($chk.length) {
        $chk.prop('checked', false).trigger('change'); // reuses your existing uncheck logic
    } else {
        $li.remove();
    }

    if ($("input[name='package_id[]']:checked").length === 0) {
        $('.next.action-button').prop('disabled', true);
    }
});
function updateHotelSummary(hotelId) {
    var $ul = $('.accomdationprice_total');
    var $li = $ul.find('li');
    var $totalDiv = $('.accomdation_total h5 span');

    var roomSpansHtml = '';
    var subtotal = 0;
    var selectedPackageName = '';
    
    $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']:checked").each(function() {
        var $this = $(this);
        
        var roomName = $this.data('room-name') || '';
        var packageName = $this.data('package-name') || '';
        var isHotelLevel = $this.data('is-hotel-level') === true;
        var pkgNameText = $this.closest('label').find('n').text().trim().toLowerCase();
        
        var displayName = '';
        if (roomName) {
            displayName = roomName;
            if (packageName) selectedPackageName = packageName;
        } else if (packageName) {
            displayName = packageName;
            selectedPackageName = packageName;
        } else if (isHotelLevel) {
            displayName = $this.closest('label').find('n').text().trim() || 'Hotel Stay';
            selectedPackageName = displayName;
        } else {
            displayName = 'Accommodation';
        }
        
        var roomId = $this.attr('accommodation_room');
        var roomQty = 1;
        
        // For sharing packages, always show 1 person
        if (pkgNameText === 'sharing') {
            roomQty = 1;
        } else if (roomId !== undefined && roomId !== null) {
            var qtySelector = "input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']";
            var $qtyInput = $(qtySelector);
            
            if ($qtyInput.length === 0 && roomId == '0') {
                qtySelector = "input[name='accmomdation-qty[" + hotelId + "][]']";
                $qtyInput = $(qtySelector);
            }
            
            if ($qtyInput.length) {
                roomQty = parseInt($qtyInput.val()) || 1;
            }
        } else {
            var $qtyInput = $("input[name^='accmomdation-qty[" + hotelId + "]']").first();
            if ($qtyInput.length) {
                roomQty = parseInt($qtyInput.val()) || 1;
            }
        }
        
        var checkInRaw = $('#accomodation_package_checkin_id_' + hotelId).val() || (hotelDates[hotelId] && hotelDates[hotelId].checkin);
        var checkOutRaw = $('#accomodation_package_checkout_id_' + hotelId).val() || (hotelDates[hotelId] && hotelDates[hotelId].checkout);
        
        var checkIn = checkInRaw ? checkInRaw.split("/")[1] : null;
        var checkOut = checkOutRaw ? checkOutRaw.split("/")[1] : null;
        
        var nights = 1;
        if (checkIn && checkOut) {
            var date1 = new Date(checkIn);
            var date2 = new Date(checkOut);
            nights = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));
            if (nights < 1) nights = 1;
        }
        
        var amount = parseFloat($this.attr('amount')) || 0;
        subtotal += amount;
        
        if (pkgNameText === 'sharing') {
            roomSpansHtml += `<span>${escapeHtml(displayName)}<br>1 Person (0.5 Room)<br>${nights} Night${nights > 1 ? 's' : ''}</span>`;
        } else {
            // Individual - ensure integer display
            var displayQty = Math.floor(roomQty);
            roomSpansHtml += `<span>${escapeHtml(displayName)}<br>${displayQty} Room${displayQty > 1 ? 's' : ''}<br>${nights} Night${nights > 1 ? 's' : ''}</span>`;
        }
    });
    
    if (!roomSpansHtml) {
        roomSpansHtml = `<span>No rooms selected</span>`;
    }
    
    if ($li.length) {
        $li.find('h6').html(roomSpansHtml);
        $li.find('h5 span').text(subtotal.toFixed(2));
    }
    
    $totalDiv.html(`${subtotal.toFixed(2)}<n><? if($cfg['GST.FLAG']!=3){?>With 18% GST<? } ?></n>`);
    
    var $packageSpan = $('.selected-package-name[data-hotel-id="' + hotelId + '"]');
    if ($packageSpan.length) {
        $packageSpan.text(selectedPackageName);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function checkAvailability(hotelId, roomTypeId, checkInDate, checkOutDate, quantity) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: 'ajax_check_availability.php',
            type: 'POST',
            data: {
                action: 'check_availability',
                hotel_id: hotelId,
                room_type_id: roomTypeId || 0,
                check_in_date: checkInDate,
                check_out_date: checkOutDate,
                quantity: quantity
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response && typeof response.success !== 'undefined') {
                    resolve(response);
                } else {
                    reject(new Error('Invalid response format'));
                }
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

function getSelectedQuantity(hotelId, roomTypeId) {
    var qty = 1;
    if (roomTypeId == 0 || roomTypeId == '0') {
        var qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][0]']");
        if (!qtyInput.length) {
            qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][]']");
        }
        if (qtyInput.length && !qtyInput.prop('disabled')) {
            qty = parseInt(qtyInput.val(), 10) || 1;
        }
    } else {
        var qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][" + roomTypeId + "]']");
        if (qtyInput.length && !qtyInput.prop('disabled')) {
            qty = parseInt(qtyInput.val(), 10) || 1;
        }
    }
    return Math.floor(qty);
}

function showToast(message, type, duration) {
    var now = Date.now();
    if (now - lastToastTime < toastCooldown) {
        return;
    }
    lastToastTime = now;
    toastr.clear();
    
    switch(type) {
        case 'success':
            toastr.success(message, 'Available', { timeOut: duration || 3000, progressBar: true, closeButton: true });
            break;
        case 'error':
            toastr.error(message, 'Error', { timeOut: duration || 5000, progressBar: true, closeButton: true });
            break;
        default:
            toastr.info(message, 'Info', { timeOut: duration || 3000, progressBar: true });
    }
}

function initializeAccoPlugins() {
    // Remove all existing handlers first (cleanup)
    $(document).off('change', "input[type=checkbox][name='package_id[]']");
    $(document).off('click', '.qty-count');
    $(document).off('click', '.accomdationprice_total .qty-count'); // NEW: was missing, caused duplicate handlers stacking on every tab switch
    $(document).off('click', '.text_danger_clear');
    
    // Checkbox change handler
    $(document).on('change', "input[type=checkbox][name='package_id[]']", function() {
        var el = $(this);
        var hotelId = el.data('hotel-id'); 
        var roomId = el.attr('accommodation_room'); 
        var pkgName = el.closest('label').find('n').text().trim().toLowerCase();
        var isHotelLevel = el.data('is-hotel-level') === true || roomId === '0' || roomId === 0;

        var checkInVal = $('#accomodation_package_checkin_id_' + hotelId).val();
        var checkOutVal = $('#accomodation_package_checkout_id_' + hotelId).val();

        if (!checkInVal || !checkOutVal) {
            showToast('Please select check-in and check-out dates first', 'error', 3000);
            el.prop('checked', false);
            return;
        }

        if (!el.is(':checked')) {
            delete selectedQty[el.val()];
            calculateTotalAmount();
            renderAccommodationSummary();
            if ($("input[name='package_id[]']:checked").length === 0) {
                $('.next.action-button').prop('disabled', true);
            }
            return;
        }

        // NEW: enforce single-hotel selection — clear any package checked under a different hotel
       // Enforce single-hotel selection — clear packages AND dates for every other hotel
        $("input[name='package_id[]']:checked").not(el).filter(function() {
            return $(this).data('hotel-id') != hotelId;
        }).each(function() {
            var $otherPkg = $(this);
            var otherHotelId = $otherPkg.data('hotel-id');

            // NEW: keep hotelDates' cached package list in sync, so switching back to this
            // hotel's tab doesn't resurrect a package we just force-unchecked
            if (hotelDates[otherHotelId] && hotelDates[otherHotelId].packages) {
                hotelDates[otherHotelId].packages = hotelDates[otherHotelId].packages.filter(function(pkgId) {
                    return pkgId !== $otherPkg.val();
                });
            }

            $otherPkg.prop('checked', false).trigger('change');
        });
        ///////////////////////////////////////////////
        var checkInDate = checkInVal.split("/")[1];
        var checkOutDate = checkOutVal.split("/")[1];
        var requestedQty = getSelectedQuantity(hotelId, roomId);

        // Convert quantity for availability check based on package type
        var availabilityCheckQty = requestedQty;
        if (pkgName === 'sharing') {
            // Sharing: 1 person = 0.5 rooms
            availabilityCheckQty = requestedQty / 2;
        } else {
            // Individual: always use integer
            availabilityCheckQty = Math.ceil(requestedQty);
        }
        
        var $originalText = el.closest('label').find('.package-price').html();
        el.closest('label').find('.package-price').html('<span class="checking-availability">Checking...</span>');
        el.prop('disabled', true);
        
        checkAvailability(hotelId, roomId, checkInDate, checkOutDate, availabilityCheckQty)
            .then(function(availabilityResult) {
                el.closest('label').find('.package-price').html($originalText);
                el.prop('disabled', false);
                
                if (!availabilityResult.success) {
                    var errorMsg = 'Cannot book: ';
                    if (pkgName === 'sharing') {
                        errorMsg = 'Not enough sharing spots available. ';
                    }
                    if (availabilityResult.unavailable_dates && availabilityResult.unavailable_dates.length > 0) {
                        errorMsg += availabilityResult.unavailable_dates.map(function(d) {
                            var displayAvailable = d.available;
                            if (pkgName !== 'sharing') {
                                displayAvailable = Math.floor(d.available);
                            }
                            return d.date + ' (only ' + displayAvailable + ' rooms)';
                        }).join(', ');
                    } else {
                        errorMsg += availabilityResult.message || 'Not enough rooms available';
                    }
                    
                    showToast(errorMsg, 'error', 5000);
                    el.prop('checked', false);
                    return;
                }
                
                // Mutual exclusivity
                if (roomId && !isHotelLevel) {
                    $("input[name='package_id[]'][data-hotel-id='" + hotelId + "'][accommodation_room='" + roomId + "']")
                        .not(el)
                        .prop('checked', false);
                } else {
                    $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']")
                        .not(el)
                        .prop('checked', false);
                }

                var nights = 1;
                if (checkInVal && checkOutVal) {
                    var checkIn = new Date(checkInDate);
                    var checkOut = new Date(checkOutDate);
                    nights = Math.ceil((checkOut - checkIn) / (1000*60*60*24));
                    nights = Math.max(1, nights);
                }
                
                var baseAmount = parseFloat(el.data('base-amount')) || 0;
                var totalAmount = 0;
                
                // Sharing ALWAYS uses quantity 1 for price calculation
                if (pkgName === 'sharing') {
                    totalAmount = baseAmount * nights * 1;
                } else {
                    totalAmount = baseAmount * nights * requestedQty;
                }
                  var totalAmountPerNight = baseAmount * nights;
                el.attr('amount', totalAmount.toFixed(2));
                el.closest('label').find('.package-price').text('₹ ' + totalAmountPerNight.toFixed(2));

                selectedQty[el.val()] = requestedQty; // NEW: remember the qty at the moment this package was checked

                if (availabilityResult.available_seats) {
                    var minSeats = Math.min(...Object.values(availabilityResult.available_seats));
                    if (pkgName === 'sharing') {
                        var sharingSpots = minSeats * 2;
                        showToast(sharingSpots + ' sharing spots available for all selected dates', 'success', 2000);
                    } else {
                        // For Individual, show whole number only
                        var wholeRooms = Math.floor(minSeats);
                        showToast(wholeRooms + ' rooms available for all selected dates', 'success', 2000);
                    }
                    
                    // Update max limit on quantity input based on availability (only for non-sharing)
                    if (pkgName !== 'sharing') {
                        var $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']");
                        if ($qtyInput.length) {
                            var currentMax = parseInt($qtyInput.attr('max'), 10) || 10;
                            // For Individual, also round down the max
                            var newMax = Math.min(currentMax, Math.floor(minSeats));
                            $qtyInput.attr('max', newMax);
                        }
                    }
                }

                // Handle room quantity inputs - DISABLE for sharing packages
                if (pkgName === 'sharing') {
                    var $qtyInput = null;
                    
                    if (roomId && !isHotelLevel) {
                        $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']");
                    } else {
                        $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][0]']");
                        if (!$qtyInput.length) {
                            $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][]']");
                        }
                    }
                    
                    if ($qtyInput && $qtyInput.length) {
                        var $btnMinus = $qtyInput.closest('.accomdationroomqty-input').find(".qty-count--minus");
                        var $btnPlus = $qtyInput.closest('.accomdationroomqty-input').find(".qty-count--add");
                        
                        $qtyInput.val(1).prop('disabled', true);
                        if ($btnMinus.length) $btnMinus.prop('disabled', true);
                        if ($btnPlus.length) $btnPlus.prop('disabled', true);
                    }
                } else if (roomId && !isHotelLevel) {
                    var $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']");
                    if ($qtyInput.length) {
                        var $btnMinus = $qtyInput.closest('.accomdationroomqty-input').find(".qty-count--minus");
                        var $btnPlus = $qtyInput.closest('.accomdationroomqty-input').find(".qty-count--add");
                        
                        $qtyInput.prop('disabled', false);
                        $btnMinus.prop('disabled', false);
                        $btnPlus.prop('disabled', false);
                    }
                } else if (isHotelLevel && pkgName !== 'sharing') {
                    var $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][0]']");
                    if (!$qtyInput.length) {
                        $qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][]']");
                    }
                    if ($qtyInput && $qtyInput.length) {
                        var $btnMinus = $qtyInput.closest('.accomdationroomqty-input').find(".qty-count--minus");
                        var $btnPlus = $qtyInput.closest('.accomdationroomqty-input').find(".qty-count--add");
                        
                        $qtyInput.prop('disabled', false);
                        $btnMinus.prop('disabled', false);
                        $btnPlus.prop('disabled', false);
                    }
                }

                if (pkgName === 'individual') {
                    var checkedIndividual = $("input[type=checkbox][name='package_id[]']:checked").filter(function() {
                        return $(this).closest('label').find('n').text().trim().toLowerCase() === 'individual';
                    });

                    if (checkedIndividual.length > 3) {
                        showToast('Maximum 3 Individual packages allowed across all hotels', 'error', 4000);
                        el.prop('checked', false);
                        return;
                    }
                }

                $("#hotel_id").val(el.attr("hotel_id"));
                $("#hotel_select_acco_id").val(el.attr("hotel_select_acco_id"));
                $("#accommodation_room").val(el.attr("accommodation_room"));

                calculateTotalAmount();
               renderAccommodationSummary();
                
                if ($("input[name='package_id[]']:checked").length > 0) {
                    $('.next.action-button').prop('disabled', false);
                }
            })
            .catch(function(error) {
                el.closest('label').find('.package-price').html($originalText);
                el.prop('disabled', false);
                el.prop('checked', false);
                console.error('Availability check failed:', error);
                showToast('Unable to check availability. Please try again.', 'error', 3000);
            });
    });
$(document).on('click', '.accomdationprice_total .qty-count', function(e) {
    e.preventDefault();
    var btn = $(this);
    var input = btn.siblings('.accmomdation-qty');
    if (input.prop('disabled')) return;

    var $li = btn.closest('li');
    var hotelId = $li.data('hotel-id');
    var roomId  = $li.data('room-id');
    var pkgValue = $li.data('package-value');
    var $chk = $("input[name='package_id[]'][value='" + pkgValue + "']");

    var min = parseInt(input.attr('min'), 10) || 1;
    var max = parseInt(input.attr('max'), 10) || 10;
    var currentVal = parseInt(input.val(), 10) || 1;
    var newVal = btn.data('action') === 'add' ? currentVal + 1 : currentVal - 1;
    newVal = Math.max(min, Math.min(max, newVal));
    if (newVal === currentVal) return;

    var dates = hotelDates[hotelId] || {};
    var checkIn = dates.checkin ? dates.checkin.split('/')[1] : null;
    var checkOut = dates.checkout ? dates.checkout.split('/')[1] : null;
    if (!checkIn || !checkOut) { input.val(newVal); return; }

    input.css('opacity', '0.5');

    checkAvailability(hotelId, roomId, checkIn, checkOut, newVal)
        .then(function(result) {
            input.css('opacity', '1');
            if (!result.success) {
                var avail = result.available_seats ? Math.floor(Math.min(...Object.values(result.available_seats))) : 0;
                showToast('Only ' + avail + ' rooms available.', 'error', 4000);
                return; // keep old value, don't re-render
            }
            input.val(newVal);

            selectedQty[pkgValue] = newVal; // NEW: persist BEFORE renderAccommodationSummary() wipes this <input>

            var nights = Math.max(1, Math.ceil((new Date(checkOut) - new Date(checkIn)) / 86400000));
            var baseAmount = parseFloat($chk.data('base-amount')) || 0;
            var total = baseAmount * nights * newVal;
            $chk.attr('amount', total.toFixed(2));

            calculateTotalAmount();
            renderAccommodationSummary();
        })
        .catch(function() {
            input.css('opacity', '1');
            showToast('Unable to verify availability. Please try again.', 'error', 3000);
        });
});
    // QUANTITY CHANGE HANDLER - Skip sharing packages
  $(document).on('click', '.qty-count', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      var btn = $(this);
      var input = btn.siblings('.accmomdation-qty');
      
      if (input.prop('disabled')) {
          showToast('Sharing package quantity is fixed at 1 person', 'warning', 2000);
          return;
      }
      
      // CHANGED: derive hotelId/roomTypeId from THIS input's own name attribute,
      // instead of trusting the global #hotel_id / #accommodation_room hidden fields
      // (those only reflect whichever checkbox was checked LAST, not which stepper was clicked)
      var inputName = input.attr('name'); // e.g. "accmomdation-qty[14][137]" or "accmomdation-qty[14][]"
      var nameMatch = inputName.match(/accmomdation-qty\[([^\]]+)\]\[([^\]]*)\]/);
      if (!nameMatch) return;

      var hotelId = nameMatch[1];
      var roomTypeId = nameMatch[2] !== '' ? nameMatch[2] : 0;

      var currentVal = parseInt(input.val(), 10) || 1;
      var min = parseInt(input.attr('min'), 10) || 1;
      var max = parseInt(input.attr('max'), 10) || 10;
      
      var newVal = currentVal;
      
      if (btn.data('action') === 'add') {
          newVal = currentVal + 1;
      } else if (btn.data('action') === 'minus') {
          newVal = currentVal - 1;
      }
      
      if (newVal < min) newVal = min;
      if (newVal > max) newVal = max;
      
      if (newVal === currentVal) return;
      
      var checkInVal = $('#accomodation_package_checkin_id_' + hotelId).val();
      var checkOutVal = $('#accomodation_package_checkout_id_' + hotelId).val();
      
      if (!checkInVal || !checkOutVal) {
          input.val(newVal);
          return;
      }
      
      var checkInDate = checkInVal.split("/")[1];
      var checkOutDate = checkOutVal.split("/")[1];
      var date1 = new Date(checkInDate);
      var date2 = new Date(checkOutDate);
      
      if (date1 >= date2) return;
      
      input.css('opacity', '0.5');
      
      checkAvailability(hotelId, roomTypeId, checkInDate, checkOutDate, newVal)
          .then(function(availabilityResult) {
              input.css('opacity', '1');
              
              if (!availabilityResult.success) {
                  var minAvailable = availabilityResult.available_seats ? 
                      Math.min(...Object.values(availabilityResult.available_seats)) : 0;
                  
                  var displayAvailable = Math.floor(minAvailable);
                  showToast('Only ' + displayAvailable + ' rooms available. Cannot select ' + newVal + ' rooms.', 'error', 4000);
                  input.val(currentVal);
                  
                  if (roomTypeId == 0 || roomTypeId == '0') {
                      $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']:checked").each(function() {
                          var $pkg = $(this);
                          var pkgRoomId = $pkg.attr('accommodation_room');
                          if (pkgRoomId === '0' || pkgRoomId === 0) {
                              $pkg.prop('checked', false);
                              var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
                              $pkg.attr('amount', baseAmount);
                              $pkg.closest('label').find('.package-price').text('₹ ' + baseAmount.toFixed(2));
                              delete selectedQty[$pkg.val()];
                          }
                      });
                  } else {
                      $("input[name='package_id[]'][data-hotel-id='" + hotelId + "'][accommodation_room='" + roomTypeId + "']:checked").each(function() {
                          var $pkg = $(this);
                          $pkg.prop('checked', false);
                          var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
                          $pkg.attr('amount', baseAmount);
                          $pkg.closest('label').find('.package-price').text('₹ ' + baseAmount.toFixed(2));
                          delete selectedQty[$pkg.val()];
                      });
                  }
                  
                  calculateTotalAmount();
                  renderAccommodationSummary();
                  if ($("input[name='package_id[]']:checked").length === 0) {
                      $('.next.action-button').prop('disabled', true);
                  }
                  return;
              }
              
              if (availabilityResult.available_seats) {
                  var minSeats = Math.min(...Object.values(availabilityResult.available_seats));
                  var wholeSeats = Math.floor(minSeats);
                  if (wholeSeats < max) {
                      input.attr('max', wholeSeats);
                  }
              }
              
              input.val(newVal);
              
              var differenceDays = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));
              differenceDays = Math.max(1, differenceDays);
              
              $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']:checked").each(function() {
                  var $pkg = $(this);
                  var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
                  var pkgRoomId = $pkg.attr('accommodation_room');
                  var pkgNameText = $pkg.closest('label').find('n').text().trim().toLowerCase();
                  
                  var roomQty;
                  
                  if (pkgNameText === 'sharing') {
                      roomQty = 1;
                  } else if (pkgRoomId == roomTypeId) {
                      // this is the exact package tied to the stepper that was clicked
                      roomQty = Math.floor(newVal);
                  } else {
                      // every other checked package keeps its own existing quantity
                      roomQty = selectedQty[$pkg.val()] || Math.floor(getSelectedQuantity(hotelId, pkgRoomId)) || 1;
                  }
                  
                  var nightTotal = baseAmount * differenceDays * roomQty;
                  var totalAmountPerNight = baseAmount * differenceDays;
                  $pkg.attr('amount', nightTotal.toFixed(2));
                  $pkg.closest('label').find('.package-price').text('₹ ' + totalAmountPerNight.toFixed(2));

                  selectedQty[$pkg.val()] = roomQty;
              });
              calculateTotalAmount();
              renderAccommodationSummary();
              
              if (availabilityResult.available_seats) {
                  var minSeats = Math.min(...Object.values(availabilityResult.available_seats));
                  var wholeSeats = Math.floor(minSeats);
                  if (wholeSeats === newVal) {
                      showToast('This is the maximum available (' + wholeSeats + ' rooms)', 'warning', 3000);
                  }
              }
          })
          .catch(function(error) {
              input.css('opacity', '1');
              console.error('Availability check failed:', error);
              showToast('Unable to verify availability. Please try again.', 'error', 3000);
              input.val(currentVal);
          });
  });

    // Clear All Hotels button handler
    $(document).on('click', '.text_danger_clear', function(e) {
        e.preventDefault();

        selectedQty = {}; // NEW: reset the qty store along with everything else

        for (var hotelId in hotelDates) {
            if (!hotelDates.hasOwnProperty(hotelId)) continue;

            $("input[type=checkbox][name='package_id[]'][data-hotel-id='" + hotelId + "']").prop('checked', false);
            $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']").each(function() {
                var baseAmount = parseFloat($(this).data('base-amount')) || 0;
                $(this).attr('amount', baseAmount);
                $(this).closest('label').find('.package-price').text('₹ ' + baseAmount.toFixed(2));
            });
            $("input[name^='accmomdation-qty[" + hotelId + "]']").val(1).prop('disabled', false);
            $('#accomodation_package_checkin_id_' + hotelId).val('');
            $('#accomodation_package_checkout_id_' + hotelId).val('');

            hotelDates[hotelId] = {
                checkin: '',
                checkout: '',
                packages: [],
                roomQty: {}
            };

            renderAccommodationSummary();
        }
        calculateTotalAmount();
    });

   window.get_checkin_val = function(val, hotelId) {
      if (!val) {
          // If checkin is cleared, reset night count to 0
          $('#accomodation_package_checkout_id_' + hotelId).val('');
          $('.night-Count[data-hotel-id="' + hotelId + '"]').text('1'); // ✅ ADD THIS
          syncHotelDates(hotelId);
          calculateTotalAmount();
          renderAccommodationSummary();
          return;
      }
      
      $('#accomodation_package_checkout_id_' + hotelId).val('');
      $('.night-Count[data-hotel-id="' + hotelId + '"]').text('1'); // ✅ ADD THIS TOO
      syncHotelDates(hotelId);
      calculateTotalAmount();
      renderAccommodationSummary();
    };
    window.get_checkout_val = function(val, hotelId) {
      var checkInVal = $('#accomodation_package_checkin_id_' + hotelId).val();
      if (!checkInVal || !val) return;

      const checkInDate = checkInVal.split("/")[1];
      const checkOutDate = val.split("/")[1];

      var date1 = new Date(checkInDate);
      var date2 = new Date(checkOutDate);

      if (date1 >= date2) {
          showToast('Please select proper checkout date!', 'error', 4000);
          $('#accomodation_package_checkout_id_' + hotelId).val('');
          return false;
      }

      var differenceDays = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));
      differenceDays = Math.max(1, differenceDays);
      
      // ✅ ADD THIS ONE LINE - Update the night count display
      $('.night-Count[data-hotel-id="' + hotelId + '"]').text(differenceDays);
      
      $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']").each(function() {
          var $pkg = $(this);
          var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
          var roomId = $pkg.attr('accommodation_room');
          var pkgName = $pkg.closest('label').find('n').text().trim().toLowerCase();

          var roomQty = 1;
          var isHotelLevel = (roomId === '0' || roomId === 0);
          
          if (pkgName === 'sharing') {
              roomQty = 1;
          } else if (isHotelLevel) {
              var qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][0]']");
              if (!qtyInput.length) {
                  qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][]']");
              }
              if (qtyInput.length && !qtyInput.prop('disabled')) {
                  roomQty = parseInt(qtyInput.val(), 10) || 1;
              }
          } else if (pkgName === 'individual') {
              var qtyInput = $("input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']");
              if (qtyInput.length && !qtyInput.prop('disabled')) {
                  roomQty = parseInt(qtyInput.val(), 10) || 1;
              }
          }

          var nightTotal = baseAmount * differenceDays * roomQty;
          var totalAmountPerNight = baseAmount * differenceDays;

          $pkg.attr('amount', nightTotal.toFixed(2));
          $pkg.closest('label').find('.package-price').text('₹ ' + totalAmountPerNight.toFixed(2));
      });
      
      syncHotelDates(hotelId);
      calculateTotalAmount();
      renderAccommodationSummary();

      // NEW: auto-select the hotel-level package for hotels with no rooms/packages
      var $autoPkg = $("input[name='package_id[]'][data-hotel-id='" + hotelId + "'][data-auto-select='true']");
      if ($autoPkg.length && !$autoPkg.is(':checked')) {
          $autoPkg.prop('checked', true).trigger('change');
      }
  };
}

// Initialize on DOM load - ONLY ONCE
document.addEventListener("DOMContentLoaded", function() {
    initializeAccoPlugins();
});
////////////accomodation end///////////
$(document).ready(function() {
    // Show the accommodation box if it exists
    $('.profile_detaile_box_body.hotel_box').show();
})
$(document).ready(function() {
    
    // ========================================
    // SHOW ONE FORM, HIDE THE OTHER TWO
    // ========================================
    $('.popup_btn').click(function(e) {
        e.preventDefault();
        
        var tabId = $(this).data('tab');
        
        // HIDE ALL THREE FORMS
        $('#frmAddWorkshopfromProfile').hide();
        $('#frmAddAccompanyfromProfile').hide();
        $('#frmAddaccomodationfromProfile').hide();
        
        // HIDE ALL checkout sections
        $('.checkout-main-wrap').hide();
        
        // SHOW the wrapper
        $('.popup_wrap').show();
        
        // SHOW ONLY the clicked form
        if (tabId === 'addworkshop') {
            $('#frmAddWorkshopfromProfile').show();
        } else if (tabId === 'addguest') {
            $('#frmAddAccompanyfromProfile').show();
        } else if (tabId === 'addstay') {
            $('#frmAddaccomodationfromProfile').show();
        }
    });
    
    // ========================================
    // CLOSE POPUP - Hide everything
    // ========================================
    $('.popup_close').click(function(e) {
        e.preventDefault();
        $('.popup_wrap').hide();
        $('#frmAddWorkshopfromProfile, #frmAddAccompanyfromProfile, #frmAddaccomodationfromProfile').hide();
        $('.checkout-main-wrap').hide();
    });
});
</script>