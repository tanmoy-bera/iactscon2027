<?php
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');

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
//echo '<pre>'; print_r($invoiceList[$delegateId]['WORKSHOP']); die;
$currentCutoffId = getTariffCutoffId();

$accompany_tariff   = getCutoffTariffAmnt($currentCutoffId);
// echo '<pre>'; print_r($accompany_tariff); die;


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


//echo '<pre>'; print_r($dinnerDtlsAccm); die;

$sqlFetchCountdown        = array();
$sqlFetchCountdown['QUERY']    = "SELECT * 
                                FROM " . _DB_LANDING_PAGE_SETTING_;
$resultFetchCountdown       = $mycms->sql_select($sqlFetchCountdown);

$dateArr          = explode("-", $resultFetchCountdown[0]['countdownDate']);



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
$hod_consent_file_types = $rowInfo['hod_consent_file_types']; //JSON array
$abstract_file_types = $rowInfo['abstract_file_types'];
$hod_consent_file_types_decoded = json_decode($rowInfo['hod_consent_file_types']);


$abstract_details = delegateAbstractDetailsSummeryWithoutTopic($delegateId);


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

$act = $_REQUEST['act'];
switch ($act) {
    case 'paymentSetInit':
        $slip_id = $_REQUEST['slip_id'];
        $delegateId = $_REQUEST['delegate_id'];
        $mode = $_REQUEST['mode'];
        if (!empty($slip_id) && !empty($delegateId) && !empty($mode)) {
            global  $cfg, $mycms;
            $mycms->setSession('LOGGED.USER.ID', $delegateId);
?>
            <center>
                <form action="<?= _BASE_URL_ ?>payment.retry.php" method="post" name="loginUnpaidOnlineFrm" style="display: none;">
                    <!--registration.process.php-->
                    <input type="hidden" id="slip_id" name="slip_id" value="<?= $slip_id ?>" />
                    <input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
                    <input type="hidden" name="act" value="paymentSet" />
                    <input type="hidden" name="mode" value="<?= $mode ?>" />
                    <h5 align="center">Processing Payment Mode<br />Please Wait</h5>
                    <img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
                    <h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
                    <br />
                    <hr />
                </form>
            </center>
            <script type="text/javascript">
                document.loginUnpaidOnlineFrm.submit();
            </script>
<?
        }
        exit();
        break;
}


?>
<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- <link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ihpba2025/util/fontawesome.v5.7.2/css/all.css" />
		<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ihpba2025/util/bootstrap.3.3.7/css/bootstrap.min.css"  />-->
<!-- <link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ihpba2025/css/website/input-material_css.php?link_color=" /> -->
<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ihpba2025/css/website/fronted.template.css" />
<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ihpba2025/css/website/all.css" />
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/parvus@2.3.3/dist/css/parvus.min.css'>

<script type="text/javascript" src="https://ruedakolkata.com/ihpba2025/js/website/include/jquery.3.3.1.min.js"></script>
<!-- <script type="text/javascript" src="https://ruedakolkata.com/ihpba2025/util/bootstrap.3.3.7/js/bootstrap.min.js"></script> -->


<!-- new profile 24/10/2024 -->
<script src="https://unpkg.com/feather-icons"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />



<link href="<?= _BASE_URL_ ?>css/website/profile.css" rel="stylesheet" type="text/css" />
<link href="<?= _BASE_URL_ ?>css/website/profile_css" rel="stylesheet" type="text/css" />
<link href="<?= _BASE_URL_ ?>css/website/profile-responsive.css" rel="stylesheet" type="text/css" />
<!-- ====================================== X ======================================== -->


<style>
    .disable-blur {
        pointer-events: none;
        filter: blur(1.5px) grayscale(1);
    }

    .left-wrap {
        grid-area: leftwrap;
        padding: 60px 70px 0 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .payment-wrap {
        grid-area: paymentwrap;
        background: linear-gradient(#5497a0, #10434d);
        padding: 35px;
        border-bottom-right-radius: 25px;
        border-bottom-left-radius: 25px;
        margin-bottom: 30px;
    }

    .total-bill {
        grid-area: totalbill;
        margin-bottom: 30px;
        padding: 60px 0 0 40px;
    }

    .summery {
        grid-area: summery;
    }

    .summery ul {
        margin: 0;
        max-height: 200px;
        overflow: auto;
        padding: 0;
    }

    .summery ul::-webkit-scrollbar {
        width: 4px;

        border-radius: 50px;
    }

    .summery ul::-webkit-scrollbar-track {
        background: #237383;
    }

    .summery ul::-webkit-scrollbar-thumb {
        background: #28626d;
    }

    .summery ul li {
        list-style: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(#122f33, #104853);
        color: white;
        padding: 12px 24px;
        border-radius: 15px;
        margin-bottom: 10px;
    }

    .order-image {
        margin: 0;
        width: 33px;
    }

    .order-image img {
        width: 100%;
    }

    .order-name {
        margin: 0;
        font-size: 18px;
    }

    .order-amount {
        margin: 0;
        font-size: 18px;
    }

    .order-dlt {
        margin: 0;
        font-size: 18px;
    }

    .block-head {
        margin-top: 0;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
        color: white;
    }

    .bank-info {}

    .bank-info p {
        margin-bottom: 0;
        margin-top: 5px;
        color: #4cc6dd;
    }

    .qr-info p {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 15px;
    }

    .qr-info {
        margin-top: 1em;
    }

    .qr-info p span {
        color: #4cc6dd;
        font-size: 15px;
    }

    .qr-info p .qr-img {
        width: 71px;
        background: white;
        padding: 12px;
        border-radius: 17px;
        display: inline-block;
        cursor: pointer;
    }

    .qr-info p span img {
        width: 100%;
    }

    .card-info {}

    .card-info p {
        display: flex;
        gap: 5px;
    }

    .card-info p img {
        width: 40px;
        height: 40px;
    }

    .payment-wrap {}

    .paymeny-type-wrap {}

    .card-con {}

    .paymnet-box {
        display: none;
    }

    .checkout-main-wrap-box {
        display: grid;
        grid-template-areas:
            'leftwrap leftwrap paymentwrap paymentwrap totalbill'
            'leftwrap leftwrap paymentwrap paymentwrap totalbill'
            'leftwrap leftwrap summery summery summery';
        padding: 50px;
        padding-top: 0;
        background: #164f5a;
        border-radius: 25px;
        width: 1023px;
        grid-template-columns: min-content;
    }

    .payment-type-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
    }

    .card-con {
        display: block;
        position: relative;
        padding-left: 26px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 16px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        color: white;
    }

    /* Hide the browser's default radio button */
    .card-con input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 18px;
        width: 18px;
        background-color: #eeeeee52;
        border-radius: 50%;
    }

    /* On mouse-over, add a grey background color */
    .card-con:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .card-con input:checked~.checkmark {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .card-con input:checked~.checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .card-con .checkmark:after {
        top: 6px;
        left: 6px;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: white;
    }

    .top-input {
        background: linear-gradient(#346f77, #0e3841);
        color: white;
        padding: 12px 15px;
        border-radius: 15px;
        margin-bottom: 15px;
    }

    .top-input label {
        width: 100%;
        display: inline-block;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .top-input input {
        background: transparent;
        border: none;
        width: 100%;
        height: 40px;
        outline: 0;
        color: white;
    }

    .top-input input::placeholder {
        color: white;
    }

    .input-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 2px solid #347e8d;
    }

    .input-box label {
        width: 100%;
        display: inline-block;
        font-size: 16px;
        width: 50%;
        color: white;
    }

    .input-box input {
        width: 50%;
        font-size: 14px;
        background: transparent;
        box-shadow: none;
        border: none;
        text-align: right;
        color: white;
        outline: 0;
    }

    .payment-button {
        margin-top: 55px;
        padding: 10px 25px;
        font-size: 17px;
        border-radius: 15px;
        border: none;
        background: #113e47;
        color: white;
    }

    .close-button {
        margin-top: 55px;
        padding: 10px 25px;
        font-size: 17px;
        border-radius: 15px;
        border: none;
        background: #113e47;
        color: white;
    }

    .total-bill h5 {
        margin-top: 0;
        font-size: 18px;
        margin-bottom: 0;
        color: white;
    }

    .total-bill h3 {
        margin-bottom: 0;
        margin-top: 15px;
        font-size: 40px;
        color: #bdf7ff;
    }

    .file-label {
        text-align: center;
        border: 1px dashed;
        padding: 11px;
        width: 75px !important;
    }

    .qr-large {
        width: 300px;
        position: absolute;
        background: white;
        padding: 20px;
        border-radius: 15px;
        display: none;
    }

    .qr-large img {
        width: 100%;
    }

    .blr {
        filter: blur(5px);
    }

    .qr-large button {
        position: absolute;
        bottom: -43px;
        left: 50%;
        transform: translate(-50%, 0px);
        background: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50px;
        cursor: pointer;
    }

    .bill-info-text {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #347e8d;
    }

    .bill-info-text p {
        color: white;
        font-size: 16px;
        margin-top: 0;
        margin-bottom: 10px;
    }

    .total-bill-amount {}

    @media only screen and (min-width: 300px) and (max-width: 999px) {
        .checkout-main-wrap-box {
            grid-template-areas:
                'leftwrap'
                'paymentwrap '
                'totalbill'
                'summery';
            width: 300px;
            margin: 10px 0;
            padding: 20px;
            padding-top: 0;
        }

        .checkout-main-wrap-inner {
            align-items: start !important;
            justify-content: center !important;
            overflow: auto;
        }

        .payment-type-wrap {
            flex-wrap: wrap;
        }

        .card-con {
            width: 35%;
        }

        .payment-wrap {
            padding: 17px;
            border-radius: 25px;
            margin-bottom: 0;
            margin-top: 30px;
        }

        .total-bill {
            padding: 30px 0 0 0px;
        }

        .summery ul {
            max-height: max-content;
        }

        .summery ul li {
            flex-direction: column;
            align-items: start;
            gap: 6px;
            position: relative;
        }

        .order-dlt {
            position: absolute;
            right: 0;
            top: 0;
        }

        .left-wrap {
            padding: 30px 0px 0 0;
        }

        .qr-large {
            top: 50%;
            transform: translate(0px, -50%);
        }
    }
</style>
<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();

include_once("header.php");



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

?>

<style>
    #loading_indicator {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
        border: 10px solid grey;
        border-radius: 50%;
        border-top: 10px solid blue;
        width: 100px;
        height: 100px;
        /*animation: spinIndicator 1s linear infinite;*/
    }

    @keyframes spinIndicator {
        100% {
            transform: rotate(360deg);
        }
    }

    #toast-container>.toast-error {
        background-image: none;
        background-color: #ff4d4d;
        color: #fff;
    }

    .toast-message {
        font-family: Calibri;
    }

    .toast {
        opacity: 1 !important;
    }
</style>

<body class="p-0">
    <main class="prf-section">
        <div class="prf-inner">
            <div class="prf-header">
                <a class="prf-logo"><img src="<?= $site_logo ?>"></a>
                <div class="prf-head-rt">
                    <div class="prf-info">
                        <p><i class="fa-solid fa-envelope me-2"></i><?= $rowUserDetails['user_email_id'] ?></p>
                        <p><i class="fa-solid fa-phone me-2"></i><?= $rowUserDetails['user_mobile_no'] ?></p>
                    </div>
                    <div class="regi-det">
                        <p><b>Registration Id: <?= $rowUserDetails['user_registration_id'] ?></b></p>
                        <p><b>Unique Sequence: <?= $rowUserDetails['user_unique_sequence'] ?></b></p>
                    </div>
                    <div class="prf-dp" onclick="$('.prf-drop').toggleClass('prf-drop-open');">
                        <p><?= $rowUserDetails['user_full_name'] ?></p>
                        <img src="<?= $profile_pic_src ?>">
                        <div class="prf-drop">
                            <!-- <a><i class="fa-solid fa-pen-to-square"></i><span>Edit Profile</span></a> -->
                            <a href="<?= _BASE_URL_ ?>login.process.php?action=logout"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="prf-body">
                <div class="prf-lft" style="display:none;">
                    <div class="prf-lft-top">
                        <a class="active">
                            <i class="fa-solid fa-house"></i>
                            <span>Home</span>
                        </a>
                        <!-- <a><i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span></a> -->
                    </div>
                    <div class="prf-lft-bottom">
                        <a href="<?= _BASE_URL_ ?>login.process.php?action=logout"><i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span></a>
                    </div>
                </div>
                <div class="prf-rt">
                    <div class="prf-body-top">
                        <div class="prf-registartion-abstract">
                            <!-- ====================================== REGISTRATION ===================================================== -->
                             <?php $reg_cls = getAllRegistrationTariffs();
                             if($reg_cls){
                             ?>
                            <div class="prf-registration">
                                <h3>Registration<a class="cancel-regi" onclick="$('.cancel_view_modal').show();" title="Cancel Registration">Cancel</a></h3>
                                <div class="registration-wrap">
                                    <div class="registration-box">
                                        <ul>
                                            <li>
                                                <p>
                                                    <span style="color: #ff5050;"><i class="fa-regular fa-circle"></i></span>
                                                    <span>Conference</span>
                                                </p>
                                                <span class="check"><i class="fa-solid fa-check"></i></span>
                                            </li>
                                            <?php
                                            $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                                            if (count($workshopDetailsArray) > 0) {
                                            ?>
                                                <li>
                                                    <p>
                                                        <span style="color: #50b5ff;"><i class="fa-regular fa-circle"></i></span>
                                                        <span>Workshop</span>
                                                    </p>
                                                    <span class="check"><i class="fa-solid fa-check"></i></span>
                                                </li>
                                            <?php }

                                            if (!empty($accompany_tariff) && $accompany_tariff > 0) {
                                            ?>
                                                <li>
                                                    <p>
                                                        <span style="color: #fffb50;"><i class="fa-regular fa-circle"></i></span>
                                                        <span>Accompanying Person</span>
                                                    </p>
                                                    <span class="check"><i class="fa-solid fa-check"></i></span>
                                                </li>
                                            <?php } ?>

                                        </ul>
                                    </div>
                                </div>
                                <?php if ($rowUserDetails['isRegistration'] == 'Y') { ?>
                                    <a class="abs-edit " onclick="$('.registration_view_modal').show();">View</a>
                                <?php } else { ?>
                                    <a href="<?= _BASE_URL_ ?>registration.tariff.php?abstractDelegateId=<?= $rowUserDetails['id'] ?>" target="_blank" class="abs-edit ">REGISTER NOW</a>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <!--============================================== abstract registered ==================================================-->
                            <?php if ($abstract_details) { ?>
                                <div class="prf-abstract ">

                                    <h3>Abstract Details<a class="submit-abstract view-submit" title="Submit">Submit</a></h3>
                                    <div class="abstract-wrap ">
                                        <?php

                                        foreach ($abstract_details as $key => $value) {
                                        ?>
                                            <div class="abstract-box">
                                                <h4><b>Submission Code :</b> <?= $value['abstract_submition_code'] ?></h4>
                                                <h4><b>Title :</b> <?= $value['abstract_title'] ?></h4>
                                                <h4><b>Category :</b> <?= getAbstractCategoryName(intval($value['abstract_cat'])) ?> </h4>
                                            </div>
                                        <?php } ?>
                                        <!-- <div class="abstract-box ">
                                            <h4><b>Submission Code :</b> 87080436</h4>
                                            <h4><b>Title :</b> Revolutionizing Glass Ceramic Crystallization: Low-Cost Laser Crystallization for Sustainable Wastewater Treatment</h4>
                                            <h4><b>Category :</b> Poster Presentation</h4>
                                        </div> -->
                                    </div>

                                    <a class="abs-edit abstract-edit">View</a>
                                </div>

                                <!--xxxxxxxxxxxxxxxxxxxx abstract registered end xxxxxxxxxxxxxxxxxxxx-->
                            <?php } else if ($cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d')) { ?>
                                <!--====================================== abstract not registered ======================================================-->
                                <div class="prf-abstract ">
                                    <div class="abstract-wrap-blank">
                                        <h5>Submission Closing On</h5>
                                        <div class="card-body">
                                            <h4><?= date('F', strtotime($cfg['ABSTRACT.SUBMIT.LASTDATE'])) ?></h4>
                                            <h3><?= date('d', strtotime($cfg['ABSTRACT.SUBMIT.LASTDATE'])) ?></h3>
                                            <h4><?= date('Y', strtotime($cfg['ABSTRACT.SUBMIT.LASTDATE'])) ?></h4>
                                        </div>
                                        <h6>
                                            <?php if ($cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] != 0) { ?>
                                                <a href="<?= $cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] == '1' ? $cfg['ABSTRACT.GUIDELINE.PDF'] : _BASE_URL_ . "uploads/FILES.ABSTRACT.REQUEST/" . $cfg['ABSTRACT.GUIDELINE.PDF.FILE'] ?>" target="_blank">
                                                    <i class="fa-solid fa-eye mr-2" style="font-size: 14px;"></i> View Submission Guideline
                                                </a>
                                            <?php } ?>
                                            <a class="submit-abstract">Submit</a>
                                        </h6>

                                    </div>
                                </div>
                            <?php } ?>
                            <!--abstract not registered -->
                        </div>

                        <div class="prf-faculty">
                            <!-- ======================================= FACULTY SPEAKER SLIDER  ======================================== -->
                            <?php

                            $sqlFlyer    =   array();
                            $sqlFlyer['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE status='A' AND title='Profile Flyer' ";

                            $resultFlyer      = $mycms->sql_select($sqlFlyer);

                            $sqlHallListing                     = array();
                            $sqlHallListing['QUERY']         = "SELECT * FROM " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . " WHERE `status` = 'A'";
                            $resParti               = $mycms->sql_select($sqlHallListing);
                            if ($resParti) {
                            ?>
                                <div class="content ">
                                    <div class="bg-shape">
                                        <img src="<?= _BASE_URL_ . $cfg['SP.PARTICIPANT.DOC'] . $resParti[0]['background_image'] ?>" alt="">
                                    </div>
                                    <div class="product-img">
                                        <?php
                                        foreach ($resParti as $k => $val) {
                                        ?>
                                            <div class="product-img__item" id="img1">
                                                <img src="<?= _BASE_URL_ . $cfg['SP.PARTICIPANT.DOC'] . $val['animation_image'] ?>" alt="star wars" class="product-img__img">
                                            </div>
                                        <?php } ?>

                                        <!-- <div class="product-img__item" id="img2">
                                            <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                        </div>

                                        <div class="product-img__item" id="img3">
                                            <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                        </div>

                                        <div class="product-img__item" id="img4">
                                            <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                        </div> -->
                                    </div>
                                    <div class="product-slider">
                                        <button class="prev disabled">
                                            <span class="icon">
                                                <i class="fa-duotone fa-solid fa-arrow-left"></i>
                                            </span>
                                        </button>
                                        <button class="next">
                                            <span class="icon">
                                                <i class="fa-duotone fa-solid fa-arrow-right"></i>
                                            </span>
                                        </button>

                                        <div class="product-slider__wrp swiper-wrapper">
                                            <?php
                                            foreach ($resParti as $k => $val) {
                                            ?>
                                                <div class="product-slider__item swiper-slide" data-target="img1">
                                                    <div class="product-slider__card">
                                                        <img src="<?= _BASE_URL_ . $cfg['SP.PARTICIPANT.DOC'] . $val['front_image'] ?>" alt="star wars" class="product-slider__cover">
                                                        <div class="product-slider__content">
                                                            <h1 class="product-slider__title"><?= $val['speaker_name'] ?></h1>
                                                            <span class="product-slider__price"><?= $val['designation'] ?></span>
                                                            <div class="product-labels__group">
                                                                <!-- <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1" checked>
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>

                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1">
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label> -->
                                                            </div>
                                                            <!-- <div class="product-ctr">
                                                    <div class="product-labels">
                                                        <div class="product-labels__title">ENGINE UNIT</div>

                                                        <div class="product-labels__group">
                                                            <label class="product-labels__item">
                                                                <input type="radio" class="product-labels__checkbox" name="type1" checked>
                                                                <span class="product-labels__txt">P-S4 TWIN</span>
                                                            </label>

                                                            <label class="product-labels__item">
                                                                <input type="radio" class="product-labels__checkbox" name="type1">
                                                                <span class="product-labels__txt">P-W401</span>
                                                            </label>
                                                        </div>

                                                    </div>

                                                    <span class="hr-vertical"></span>

                                                    <div class="product-inf">
                                                        <div class="product-inf__percent">
                                                            <div class="product-inf__percent-circle">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                                                                    <defs>
                                                                        <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                                            <stop offset="0%" stop-color="#0c1e2c" stop-opacity="0" />
                                                                            <stop offset="100%" stop-color="#cb2240" stop-opacity="1" />
                                                                        </linearGradient>
                                                                    </defs>
                                                                    <circle cx="50" cy="50" r="47" stroke-dasharray="225, 300" stroke="#cb2240" stroke-width="4" fill="none" />
                                                                </svg>
                                                            </div>
                                                            <div class="product-inf__percent-txt">
                                                                75%
                                                            </div>
                                                        </div>

                                                        <span class="product-inf__title">DURABILITY</span>
                                                    </div>

                                                </div>

                                                <div class="product-slider__bottom">
                                                    <button class="product-slider__cart">
                                                        ADD TO CART
                                                    </button>

                                                    <button class="product-slider__fav js-fav"><span class="heart"></span> ADD TO WISHLIST</button>
                                                </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } else if ($resultFlyer) { ?>

                                <!-- ======================================= PROFILE FLYER SLIDER  ======================================== -->
                                <div class="flyre_slider owl-carousel owl-theme ">
                                    <?php
                                    foreach ($resultFlyer as $k => $val) {
                                        $icon_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['image'];
                                    ?>
                                        <div>
                                            <a class="lightbox" href="<?= $icon_image ?>" data-group="flyre-slider">
                                                <img src="<?= $icon_image ?>" width="100%">
                                            </a>
                                        </div>
                                    <?php } ?>

                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="prf-body-bottom">
                        <div class="prf-temp">
                            <div class="prf-temp-box">
                                <div class="weather-side">
                                    <div class="weather-gradient"></div>
                                    <div class="date-container">
                                        <?php
                                        $dateString = $todayDate; // Example: Thursday
                                        // Convert date string to a timestamp
                                        $timestamp = strtotime($dateString);
                                        // Get the full day name
                                        $fullDayName = date("l", $timestamp);

                                        ?>
                                        <h2 class="date-dayname"><?= date("l", $timestamp) ?></h2><span class="date-day"><?= date("d M Y", strtotime($dateString)) ?></span><span class="location" style="overflow:auto;"><i class="location-icon" data-feather="map-pin"></i><?= ucwords($placeName == '' ? $loc : $placeName) ?></span>
                                    </div>
                                    <div class="weather-container">
                                        <!-- <i class="weather-icon" data-feather="sun"></i> -->
                                        <img src="<?= _BASE_URL_ ?>images/<?= $tempIcon ?>" style="width:30%">
                                        <h1 class="weather-temp"> <?= $totayTemp ?></h1>
                                        <h3 class="weather-desc"><?= $todayconditions ?></h3>
                                    </div>
                                </div>
                                <div class="info-side">
                                    <div class="today-info-container">
                                        <div class="today-info">
                                            <div class="precipitation"> <span class="title">PRECIPITATION</span><span class="value"><?= $todayPrecipitation ?> %</span>

                                            </div>
                                            <div class="humidity"> <span class="title">HUMIDITY</span><span class="value"><?= $todayhumidity ?> %</span>

                                            </div>
                                            <div class="wind"> <span class="title">WIND</span><span class="value"><?= $todaywindspeed ?> km/h</span>

                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $days = [];
                                    // $date= date('Y-m-d',strtotime($cfg['CONF_START_DATE']));
                                    $date = new DateTime($cfg['CONF_START_DATE']); // Create a DateTime object for today

                                    for ($i = 0; $i < 4; $i++) { // Loop for today + next 3 days
                                        $days[] = $date->format('D'); // Add the day in "Fri" format to the array
                                        $date->modify('+1 day'); // Move to the next day
                                    }
                                    ?>
                                    <div class="week-container">
                                        <ul class="week-list">
                                            <li class="active"><i class="day-icon" data-feather="<?= $weatherApiData['days'][0]['icon'] == 'partly-cloudy-day' ? 'cloud' : ($weatherApiData['days'][0]['icon'] == 'clear-day' ? 'sun' : 'cloud-rain') ?>"></i><span class="day-name"><?= $days[0] ?></span><span class="day-temp"><?= $totayTemp ?></span></li>
                                            <li><i class="day-icon" data-feather="<?= $weatherApiData['days'][1]['icon'] == 'partly-cloudy-day' ? 'cloud' : ($weatherApiData['days'][1]['icon'] == 'clear-day' ? 'sun' : 'cloud-rain') ?>"></i><span class="day-name"><?= $days[1] ?></span><span class="day-temp"><?= $weatherApiData['days'][1]['temp'] ?></span></li>
                                            <li><i class="day-icon" data-feather="<?= $weatherApiData['days'][2]['icon'] == 'partly-cloudy-day' ? 'cloud' : ($weatherApiData['days'][2]['icon'] == 'clear-day' ? 'sun' : 'cloud-rain') ?>"></i><span class="day-name"><?= $days[2] ?></span><span class="day-temp"><?= $weatherApiData['days'][2]['temp'] ?></span></li>
                                            <li><i class="day-icon" data-feather="<?= $weatherApiData['days'][3]['icon'] == 'partly-cloudy-day' ? 'cloud' : ($weatherApiData['days'][3]['icon'] == 'clear-day' ? 'sun' : 'cloud-rain') ?>"></i><span class="day-name"><?= $days[3] ?></span><span class="day-temp"><?= $weatherApiData['days'][3]['temp'] ?></span></li>
                                            <div class="clear"></div>
                                        </ul>
                                    </div>
                                    <div class="location-container">
                                        <!-- <div id="map" style="width:400px; height:300px"></div> -->
                                        <!-- <form action="<?= _BASE_URL_ ?>getWeather.php" method="post"> -->
                                        <!-- <input type="hidden" id="latitude" name="latitude">
                                            <input type="hidden" id="longitude" name="longitude"> -->
                                        <button class="location-button" id="open_map_btn"> <i data-feather="map-pin"></i><span>Change location</span></button>
                                        <!-- </form> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $sqlVenue    =   array();
                        $sqlVenue['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                                                    WHERE status='A' AND `title` IN ('Venue','Venue Background' ) ORDER BY id";

                        $resultVenue  = $mycms->sql_select($sqlVenue);
                        $venue_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultVenue[0]['image'];
                        $venue_bg_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultVenue[1]['image'];

                        $venue_text_arr = explode('/', $cfg['PROFILE_VENUE_TEXT']);
                        ?>
                        <div class="prf-venue">
                            <article>
                                <div class="assets">
                                    <img src="<?= $venue_bg_image ?>" alt="">
                                    <h3><?= $venue_text_arr[0] ?></h3>
                                    <img src="<?= $venue_image ?>" alt="">
                                </div>
                                <div class="blur">
                                    <!-- <img src="osaka.jpeg" alt="" /> -->
                                    <div>
                                        <div class="layer" style="--index:1;"></div>
                                        <div class="layer" style="--index:2;"></div>
                                        <div class="layer" style="--index:3;"></div>
                                        <div class="layer" style="--index:4;"></div>
                                        <div class="layer" style="--index:5;"></div>
                                    </div>
                                </div>
                                <div class="vcontent">
                                    <p>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                            <path d="M15.75 8.25a.75.75 0 0 1 .75.75c0 1.12-.492 2.126-1.27 2.812a.75.75 0 1 1-.992-1.124A2.243 2.243 0 0 0 15 9a.75.75 0 0 1 .75-.75Z"></path>
                                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM4.575 15.6a8.25 8.25 0 0 0 9.348 4.425 1.966 1.966 0 0 0-1.84-1.275.983.983 0 0 1-.97-.822l-.073-.437c-.094-.565.25-1.11.8-1.267l.99-.282c.427-.123.783-.418.982-.816l.036-.073a1.453 1.453 0 0 1 2.328-.377L16.5 15h.628a2.25 2.25 0 0 1 1.983 1.186 8.25 8.25 0 0 0-6.345-12.4c.044.262.18.503.389.676l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 0 1-1.161.886l-.143.048a1.107 1.107 0 0 0-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 0 1-1.652.928l-.679-.906a1.125 1.125 0 0 0-1.906.172L4.575 15.6Z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><?= $venue_text_arr[1] ?></span>
                                    </p>
                                    <p><?= $venue_text_arr[2] ?></p>
                                </div>
                            </article>
                        </div>

                        <div class="prf-weather">
                            <div class="countdown">
                                <canvas width="300" height="300" id="timer"></canvas>
                                <div class="countdown-wrap">
                                    <p><span id="days"></span> days</p>
                                    <p><span id="hours"></span> hours</p>
                                    <p><span id="minutes"></span> minutes</p>
                                    <p><span id="seconds"></span> seconds</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
</body>


<div class="abstract_view_modal" id="map_modal" style="display:none">
    <div class="abstract_view_inner">
        <div class="abstract_view_box">
            <div class="abstract_head">

                <!-- non edit section heading -->
                <!-- <h3 class="non-edit-sub-heading">select a location</h3> -->
                <div id="pac-container" style="width: 100%;">
                    <input id="pac-input" type="text" class='form-control' placeholder="Enter a location" style="width: 100%;height: 47px;border-radius: 30px;color: #ffffff;font-size: 18px;font-weight: 600;background-image: linear-gradient(45deg, #e0dede30, #ffffff54);cursor: pointer;" />
                </div>
                <!-- add section heading -->

                <ul class="abs_mod_head mb-0">
                    <a class="m-0 abs-modal-close"><i class="fa-solid fa-xmark"></i></a>
                </ul>
            </div>
            <div class="map_body">
                <!-- <div id="pac-container">
                    <input id="pac-input" type="text" placeholder="Enter a location" />
                </div> -->

                <div id="map" style="width:100%;height: 600px;"></div>

                <form action="<?= _BASE_URL_ ?>getWeather.php" method="post">
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    <input type="hidden" id="place_name" name="place_name">
                    <input type="hidden" id="place-address" name="place-address">
                    <button class="location-button"> <i data-feather="map-pin"></i><span>Select location</span></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCN4C6WrCAY1CxMkv5lD6zof3kGIhg8YXU&callback=initMap&libraries=places" async defer></script>

<script>
    $('#open_map_btn').click(function(e) {
        $('.no-edit').show();
        $('.has-edit').hide();
        $('#map_modal').show();
        $('.tab-pane').removeClass('active');
        $('.abs_mod_tab li').removeClass('active');
    });
    // let marker;

    function initMap() {
        // Initialize map centered at a default location
        const defaultLocation = {
            lat: 20.5937,
            lng: 78.9629
        }; // Center of India, can be changed
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 5,
            center: defaultLocation,
        });

        const card = document.getElementById("pac-card");
        const input = document.getElementById("pac-input");
        const biasInputElement = document.getElementById("use-location-bias");
        const strictBoundsInputElement = document.getElementById("use-strict-bounds");
        const options = {
            fields: ["formatted_address", "geometry", "name"],
            strictBounds: false,
        };


        map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);

        const autocomplete = new google.maps.places.Autocomplete(input, options);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo("bounds", map);

        const infowindow = new google.maps.InfoWindow();
        const infowindowContent = document.getElementById("infowindow-content");

        infowindow.setContent(infowindowContent);

        const marker = new google.maps.Marker({
            map,
            anchorPoint: new google.maps.Point(0, -29),
        });


        autocomplete.addListener("place_changed", () => {
            infowindow.close();
            marker.setVisible(false);

            const place = autocomplete.getPlace();

            // Get latitude and longitude of the clicked point

            const lat = place.geometry.location.lat(); // Latitude
            const lng = place.geometry.location.lng(); // Longitude

            // Set latitude and longitude in the hidden form fields
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
            document.getElementById("place_name").value = place.name;



            if (!place.geometry || !place.geometry.location) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            infowindowContent.children["place-name"].textContent = place.name;
            infowindowContent.children["place-address"].textContent =
                place.formatted_address;
            infowindow.open(map, marker);
        });



        // Add a click event listener to the map
        map.addListener("click", (event) => {
            // Get latitude and longitude of the clicked point
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();

            // Set latitude and longitude in the hidden form fields
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
            // Place or move the marker on the map
            if (marker) {
                marker.setPosition(event.latLng); // Move the existing marker
            } else {
                marker = new google.maps.Marker({
                    position: event.latLng,
                    map: map,
                });
            }

            // alert(`Selected location: Latitude: ${lat}, Longitude: ${lng}`);
        });
    }
</script>

<!-- ============================================== ADD ABSTRACT MODAL ======================================================= -->
<div class="abstract_view_modal" id="abstract_add">
    <div class="abstract_view_inner">
        <div class="abstract_view_box">
            <div class="abstract_head">
                <div class="abs_modal-prf">
                    <!-- non edit section heading -->
                    <h3 class="non-edit-sub-heading">Submitted Abstract List</h3>
                    <!-- non edit section heading -->
                    <!-- edit section heading -->
                    <h3 class="edit-sub-heading" style="display: none;"><b>Submission Code :</b> 87080437</h3>
                    <!-- edit section heading -->
                    <!-- add section heading -->
                    <h3 class="add-sub-heading" style="display: none;">Submit Abstract
                        <?php if ($cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] != 0) { ?>
                            <a href="<?= $cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] == '1' ? $cfg['ABSTRACT.GUIDELINE.PDF'] : _BASE_URL_ . "uploads/FILES.ABSTRACT.REQUEST/" . $cfg['ABSTRACT.GUIDELINE.PDF.FILE'] ?>" target="_blank">
                                <i class="fa-solid fa-eye mr-2" style="font-size: 14px;"></i> View Abstract Guideline
                            </a>
                        <?php } ?>
                    </h3>
                    <!-- add section heading -->
                </div>
                <ul class="abs_mod_head mb-0">
                    <a class="m-0 abs-modal-close"><i class="fa-solid fa-xmark"></i></a>
                </ul>
            </div>
            <div class="abstract_body">
                <div class="abstract_details no-edit">
                    <ul class="abstract-list-box">
                        <li class="abstract-list-content">
                            <a class="abs-modal-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <h4 class="abs-modal-submission"><b>Submission Code :</b> 87080437</h4>
                            <h4><b>Title :</b> Revolutionizing Glass Ceramic Crystallization: Low-Cost Laser Crystallization for Sustainable Wastewater Treatment</h4>
                            <h4><b>Category :</b> Poster Presentation</h4>
                            <p class="abst_sub_status">Abstract Submitted<span class="bst_sub_date">Date</span></p>
                            <ul class="attched_details">
                                <li>
                                    <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                    <div class="attached_file">
                                        <p>Attached Name<br><span>Date</span></p>
                                        <a><i class="fa-solid fa-download"></i></a>
                                    </div>

                                </li>
                                <li>
                                    <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                    <div class="attached_file">
                                        <p>Attached Name<br><span>Date</span></p>
                                        <a><i class="fa-solid fa-download"></i></a>
                                    </div>

                                </li>

                            </ul>
                            <span class="abs-number">1</span>
                        </li>
                        <li class="abstract-list-content">
                            <a class="abs-modal-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <h4 class="abs-modal-submission"><b>Submission Code :</b> 87080437</h4>
                            <h4><b>Title :</b> Revolutionizing Glass Ceramic Crystallization: Low-Cost Laser Crystallization for Sustainable Wastewater Treatment</h4>
                            <h4><b>Category :</b> Poster Presentation</h4>
                            <p class="abst_sub_status">Abstract Submitted<span class="bst_sub_date">Date</span></p>
                            <ul class="attched_details">
                                <li>
                                    <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                    <div class="attached_file">
                                        <p>Attached Name<br><span>Date</span></p>
                                        <a><i class="fa-solid fa-download"></i></a>
                                    </div>

                                </li>
                            </ul>
                            <span class="abs-number">2</span>
                        </li>
                    </ul>
                </div>
                <div class="abstract_edit_wrap has-edit">
                    <div class="wizard">
                        <!-- <form role="form" action="index.html" class="login-box"> -->
                        <form name="abstractRequestForm" id="abstractRequestForm" action="<?= _BASE_URL_ ?>abstract_request.process.php" method="post" enctype="multipart/form-data">

                            <ul class="abs_mod_tab new_abstract_tab nav nav-tabs" role="tablist">
                                <li role="presentation" style="pointer-events: none;" class="active">
                                    <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Author Details</span></a>
                                </li>
                                <li role="presentation" style="pointer-events: none;" class="disabled">
                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Co-Author Details</span></a>
                                </li>
                                <li role="presentation" style="pointer-events: none;" class="disabled">
                                    <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Submission Category</span></a>
                                </li>
                                <li role="presentation" style="pointer-events: none;" class="disabled">
                                    <a href="#step4" data-toggle="tab" aria-controls="step4" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Details</span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="abstract_add_form">
                                <!-- <form name="abstractRequestForm" id="abstractRequestForm" action="<?= _BASE_URL_ ?>abstract_request.process.php" method="post" enctype="multipart/form-data"> -->
                                <input type="hidden" name="act" value="abstractSubmission" />
                                <input type="hidden" name="applicantId" id="applicantId" value="<?= $delegateId ?>" />
                                <input type="hidden" name="report_data" id="report_data" value="Abstract" />

                                <div class="tab-pane active" role="tabpanel" id="step1">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Author Details</p>

                                        <input type="hidden" name="presenter_email_id" id="presenter_email_id" value="<?= $rowUserDetails['user_email_id'] ?>">
                                        <input type="hidden" name="presenter_mobile" id="presenter_mobile" value="<?= $rowUserDetails['user_mobile_no'] ?>">
                                        <input type="hidden" name="presenter_title" id="presenter_title" value="<?= $rowUserDetails['user_title'] ?>">
                                        <input type="hidden" name="presenter_first_name" id="presenter_first_name" value="<?= $rowUserDetails['user_first_name'] ?>">
                                        <input type="hidden" name="presenter_last_name" id="presenter_last_name" value="<?= $rowUserDetails['user_last_name'] ?>">
                                        <input type="hidden" name="presenter_country" id="presenter_country" value="<?= $rowUserDetails['user_country_id'] ?>">
                                        <input type="hidden" name="presenter_state" id="presenter_state" value="<?= $rowUserDetails['user_state_id'] ?>">
                                        <input type="hidden" name="presenter_city" id="presenter_city" value="<?= $rowUserDetails['user_city'] ?>">
                                        <input type="hidden" name="presenter_pincode" id="presenter_pincode" value="<?= $rowUserDetails['user_pincode'] ?>">
                                        <input type="hidden" name="presenter_institute" id="presenter_institute" value="<?= $rowUserDetails['user_institute_name'] ?>">
                                        <input type="hidden" name="presenter_department" id="presenter_department" value="<?= $rowUserDetails['user_department'] ?>">

                                        <div class="author-wrap">
                                            <div class="author_details_inner" id="abstract_author_details">
                                                <div class="checkbox" style="padding-top:20px;padding-left: 10px;">
                                                    <div>
                                                        <label class="custom-radio" style="float:left; margin-right:20px;">Author and Presenter is the same person
                                                            <input type="checkbox" name="willBePresenter" use="willBePresenter" id="willBePresenter" onclick="setAsPresenter(this)">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        &nbsp;
                                                    </div>
                                                </div>
                                                <input type="hidden" id="isPresenter" name="isPresenter" value="">
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">E-mail</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" name="abstract_author_email" id="abstract_author_email" class="form-control" style="text-transform:lowercase;" usefor="email" value="" validate="Please enter the email id">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Mobile</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" id="abstract_author_mobile" name="abstract_author_mobile" placeholder="" validate="Please enter the mobile No" onkeypress="return isNumber(event)" maxlength="10">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Title</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <div class="checkbox-wrap">
                                                            <label class="custom-radio">
                                                                <input type="radio" name="abstract_author_title" value="DR" checked=""><span style="display:unset;" class="checkbox-text">Dr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_author_title" value="PROF"><span style="display:unset;" class="checkbox-text">Prof.</span><span style="display:unset;" class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_author_title" value="MR"><span style="display:unset;" class="checkbox-text">Mr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_author_title" value="MRS"><span style="display:unset;" class="checkbox-text">Mrs.</span><span style="display:unset;" class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_author_title" value="MS"><span style="display:unset;" class="checkbox-text">Ms.</span><span style="display:unset;" class="checkmark"></span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">First Name</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" id="abstract_author_first_name" name="abstract_author_first_name" placeholder="" validate="Please enter the first name">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Last Name</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" id="abstract_author_last_name" name="abstract_author_last_name" placeholder="" validate="Please enter the last name">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Country</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <select class="form-control select" name="abstract_author_country" id="abstract_author_country" forType="countryx" onchange="generateStateList(this,'abstract_author_state')" style="text-transform:uppercase;" required validate="Please select country">
                                                            <option value="0">-- Select Country --</option>
                                                            <?php
                                                            $sqlFetchCountry   = array();
                                                            $sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
	                                                         WHERE `status` =? 
	                                                      ORDER BY `country_name` ASC";

                                                            $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                            $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                            if ($resultFetchCountry) {
                                                                foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {

                                                            ?>
                                                                    <option value="<?= $rowFetchCountry['country_id'] ?>"><?= $rowFetchCountry['country_name'] ?></option>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">State</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <?php
                                                        // $sqlFetchState   = array();
                                                        // $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
                                                        //  WHERE `status` =? AND `country_id`=?
                                                        // ORDER BY `state_name` ASC";

                                                        // $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                        // $sqlFetchState['PARAM'][]   = array('FILD' => 'country_id', 'DATA' => $rowUserDetails['user_country_id'], 'TYP' => 's');

                                                        // $resultFetchState = $mycms->sql_select($sqlFetchState);

                                                        ?>

                                                        <!-- <select class="form-control select" name="abstract_authors_state" id="abstract_presenter_state" use="country" forType="state" style="text-transform:uppercase;" required validate="Please select state"> -->
                                                        <select class="form-control select" name="abstract_author_state" id="abstract_author_state" forType="statex" style="text-transform:uppercase;" required validate="Please select state">
                                                            <!-- <option value="0">-- Select State --</option> -->
                                                            <option value="">-- Select Country First --</option>
                                                            <!-- <?php

                                                                    if ($resultFetchState) {
                                                                        foreach ($resultFetchState as $keyState => $rowFetchState) {
                                                                    ?>
																		<option value="<?= $rowFetchState['st_id'] ?>"><?= $rowFetchState['state_name'] ?></option>
																<?php
                                                                        }
                                                                    }
                                                                ?> -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">City</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" id="abstract_author_city" name="abstract_author_city" placeholder="" validate="Please enter the city">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Postal Code</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" id="abstract_author_pincode" name="abstract_author_pincode" placeholder="" validate="Please enter the postal code">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Institute</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" use="Institute" id="abstract_author_institute" name="abstract_author_institute" placeholder="" validate="Please enter the institute" autocomplete="nope">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Department</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control" use="Department" id="abstract_author_department" name="abstract_author_department" placeholder="" validate="Please enter the Department" autocomplete="nope">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-inline ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step-abstract" step="1">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step-abstract" step="1">Next</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step2">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Co-Author Details<a id="add-coauthor-btn">Add More</a></p>
                                        <div class="author-wrap">
                                            <div class="author_details_inner" id="accordion-body-coauthor">
                                                <div id="newCoAuthor" class="add_coathor">
                                                    <div class="author_details_inner">
                                                        <div class="col-12 form-floating">
                                                            <p class="coauthor_li" style="justify-content: unset;">Co-Author:&nbsp; <span id="coCount">1</span></p>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">E-mail</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="Email" id="abstract_coauthor_email" name="abstract_coauthor_email[0]" placeholder="" validate="Please enter the coauthor email" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">Mobile</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="Mobile" id="abstract_coauthor_mobile" name="abstract_coauthor_mobile[0]" placeholder="" onkeypress="return isNumber(event)" maxlength="10" validate="Please enter the coauthor mobile" onkeypress="return isNumber(event)" maxlength="10" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 form-floating">
                                                            <label for="floatingInput">Title</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <div class="checkbox-wrap">
                                                                    <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="DR"><span style="display:unset;" class="checkbox-text">Dr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                    <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="PROF"><span style="display:unset;" class="checkbox-text">Prof.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                    <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MR"><span style="display:unset;" class="checkbox-text">Mr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                    <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MRS"><span style="display:unset;" class="checkbox-text">Mrs.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                    <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MS"><span style="display:unset;" class="checkbox-text">Ms.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">First Name</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="First Name" id="abstract_coauthor_first_name" name="abstract_coauthor_first_name[0]" placeholder="" validate="Please enter the coauthor first name" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">Last Name</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="Last Name" id="abstract_coauthor_last_name" name="abstract_coauthor_last_name[0]" placeholder="" validate="Please enter the coauthor last name" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">Country</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <select class="form-control select coauther_details " use="Country" name="abstract_coauthor_country[0]" id="abstract_coauthor_country" forType="countryx" style="text-transform:uppercase;" required validate="Please select the coauthor country">
                                                                    <option value="0">-- Select Country --</option>
                                                                    <?php
                                                                    $sqlFetchCountry   = array();
                                                                    $sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
																                         WHERE `status` =? 
																                      ORDER BY `country_name` ASC";

                                                                    $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                    $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                                    if ($resultFetchCountry) {
                                                                        foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                                    ?>
                                                                            <option value="<?= $rowFetchCountry['country_id'] ?>"><?= $rowFetchCountry['country_name'] ?></option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">State</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <select class="form-control select coauther_details " use="State" name="abstract_coauthor_state[0]" id="abstract_coauthor_state" forType="statex" style="text-transform:uppercase;" required validate="Please select the coauthor state">
                                                                    <option value="0" selected>Select State</option>
                                                                    <?php
                                                                    $sqlFetchState   = array();
                                                                    $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
															                         WHERE `status` =? 
															                      ORDER BY `state_name` ASC";

                                                                    $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                    $resultFetchState = $mycms->sql_select($sqlFetchState);
                                                                    if ($resultFetchState) {
                                                                        foreach ($resultFetchState as $keyState => $rowFetchState) {
                                                                    ?>
                                                                            <option value="<?= $rowFetchState['st_id'] ?>"><?= $rowFetchState['state_name'] ?></option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">City</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="City" id="abstract_coauthor_city" name="abstract_coauthor_city[0]" placeholder="" validate="Please enter the coauthor city" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">Postal Code</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="Postal Code" id="abstract_coauthor_pincode" name="abstract_coauthor_pincode[0]" placeholder="" validate="Please enter the coauthor pincode" onkeypress="return isNumber(event)" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">Institute</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="Institute" id="abstract_coauthor_institute" name="abstract_coauthor_institute[0]" placeholder="" validate="Please enter the coauthor institute" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6 form-floating">
                                                            <label for="floatingInput">Department</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <input type="text" class="form-control coauther_details " use="Department" id="abstract_coauthor_department" name="abstract_coauthor_department[0]" placeholder="" validate="Please enter the coauthor Department" autocomplete="nope">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <button class="btn" id="removeCoauthor1" style="margin-top: 9px;background: #c34747;border:0px solid black; padding: 12px 25px;margin-bottom: 15px;">Delete</button> -->
                                                <input type="hidden" name="coAuthorCounts" id="coAuthorCounts" value="1">
                                            </div>

                                        </div>
                                    </div>
                                    <ul class="list-inline   ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step-abstract" step="2">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step-abstract" step="2">Next</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step3">
                                    <div class="author_details" style="height: calc(50% - 61px);">
                                        <p class="abst_mod_title mb-3">Submission Category</p>
                                        <div class="author-wrap m-0">
                                            <div class="sub-cate-checkbox-wrap" id="abs_category">
                                                <?php
                                                if (count($abstract_details) < 10) {
                                                    foreach ($resultAbstractTopic as $key => $valueCat) {

                                                        $sqlAbstractSubcat['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
																								WHERE `related_category_id`='" . $value['id'] . "' AND `related_topic_id`='' AND
																								`status` = 'A' ORDER BY `id` ASC";

                                                        $resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);
                                                        $nomination_ids = array();
                                                        if ($resultAbstractSubcat) {
                                                            foreach ($resultAbstractSubcat as $key => $nomination) {
                                                                array_push($nomination_ids, $nomination['id']);
                                                            }
                                                        }
                                                        $nomination_ids = json_encode($nomination_ids);
                                                        // print_r($nomination_ids);

                                                        if (in_array(trim($valueCat['id']), $abstractCatArray)) {
                                                            $checked = 'checked';
                                                            $disabled = 'disabled';
                                                        } else {
                                                            $checked = '';
                                                            $disabled = '';
                                                        }
                                                ?>

                                                        <label class="custom-radio">
                                                            <input type="radio" id="flexCheckDefault<?= $valueCat['id'] ?>" name="abstract_category" onclick="abstractAddTypeChange(this,'<?= $valueCat['id'] ?>','<?php echo implode(',', json_decode($valueCat['category_fields'])); ?>','<?php echo implode(',', json_decode($nomination_ids)); ?>')" title="<?= $valueCat['category'] ?>" value="<?= $valueCat['id'] ?>">
                                                            <span class="checkbox-text"><?= $valueCat['category'] ?></span><span class="checkmark"></span>
                                                        </label>
                                                <?php

                                                    }
                                                }

                                                ?>
                                                <input type="hidden" name="absCatTitle" id="absCatTitle">
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($resultAbstractSubmission) { ?>
                                        <div class="author_details" style="height: calc(50% - 61px);">
                                            <p class="abst_mod_title mb-3">Submission Sub Category</p>
                                            <div class="author-wrap m-0">
                                                <div id="collapseTwo2" class="sub-cate-checkbox-wrap">
                                                    <?php
                                                    if (count($abstract_details) < 10) {
                                                        //echo '<pre>'; print_r($resultAbstractSubmission);
                                                        foreach ($resultAbstractSubmission as $key => $valueSubCat) {

                                                    ?>
                                                            <!-- <div class="sub-cate-checkbox-wrap submissionTypeRadio<?= $valueSubCat['category'] ?> hidesub" style="display: none;"> -->
                                                            <label class="custom-radio  submissionTypeRadio<?= $valueSubCat['category'] ?> hidesub" for="abstract_parent_type<?= $valueSubCat['id'] ?>" style="display: none;">
                                                                <input type="radio" class="custom-radio trigger_subCategory" id="abstract_parent_type<?= $valueSubCat['id'] ?>" name="abstract_parent_type" value="<?= $valueSubCat['id'] ?>" titleWordCountLimit="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>" contentWordCountLimit="<?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>" relatedSubmissionSubType="" onclick="abstractSubmissionType(this,'<?php echo $valueSubCat['category']; ?>','<?php echo $valueSubCat['id']; ?>')" relatedSubmissionSubType="">
                                                                <span class="checkbox-text"><?= $valueSubCat['abstract_submission'] ?></span>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                            <!-- </div> -->
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <ul class="list-inline ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step-abstract" step="3">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step-abstract" step="3">Next</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step4" use="abstractDetails0">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Submit Your Abstract</p>
                                        <div class="author-wrap">
                                            <div class="author_details_inner" id="abstract_details">
                                                <?php
                                                $sqlAbstractTopicList              =    array();
                                                $sqlAbstractTopicList['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                                                                              WHERE `status` = ? 
                                                                                                                           ORDER BY `id` ASC";

                                                $sqlAbstractTopicList['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');
                                                $resultAbstractTopicList = $mycms->sql_select($sqlAbstractTopicList);
                                                if ($resultAbstractTopicList) {
                                                ?>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Topic</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <select name="abstract_topic_id" id="abstract_topic_id" class="form-control select" style="text-transform:uppercase; height:60px;" required="required" validate="Please select the topic">
                                                                <option value="">--Select a Topic--</option>
                                                                <?php

                                                                foreach ($resultAbstractTopicList as $keyAbstractTopic => $rowAbstractTopic) {
                                                                ?>
                                                                    <option value="<?= $rowAbstractTopic['id'] ?>"><?= $rowAbstractTopic['abstract_topic'] ?></option>
                                                                <?php
                                                                }

                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div id="abstract_topic_desc" style="font-style: italic; font-size: 14px;font-weight: bolder;">

                                                    </div>
                                                <?php } ?>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Title</label>

                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea class="form-control" name="abstract_title" id="abstract_title" checkFor="wordCount" abs_id="0" spreadInGroup="abstractTitle" displayText="abstract_title_word_count" style="text-transform:uppercase;" <?php if ($cfg['ABSTRACT.TITLE.WORD.TYPE'] == 'character') { ?> maxlength="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>" <?php } ?> required validate="Please enter the abstract title"></textarea>
                                                    </div>
                                                    <div class="alert alert-success" style="display:block;">
                                                        <span style="display:flex;" use="abstract_title_word_count" limit="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>">
                                                            <span style="display:block;" use="total_word_entered">0</span> /
                                                            <span style="display:block;" use="total_word_limit"><?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?></span>
                                                            <span style="color: #D41000;display:block;"> (Title should be within <?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?> <?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>s.)</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php
                                                $sqlAbstractFields              =    array();
                                                $sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
																						  WHERE `status` = ?";

                                                $sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

                                                $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);

                                                $i = 1;
                                                //echo '<pre>'; print_r($resultAbstractFields);
                                                foreach ($resultAbstractFields as $key => $valueFields) {

                                                    $msg = "Please enter the " . strtolower($valueFields['display_name']);

                                                ?>
                                                    <div class="col-12 col-lg-12 form-floating field_div" id="field_div_<?= $valueFields['id'] ?>">
                                                        <label for="floatingInput"><?= $valueFields['display_name'] ?></label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea class="form-control commn hideitem" name="<?= $valueFields['field_key'] ?>[]" id="fieldVal_<?= $valueFields['id'] ?>" checkFor="wordCount" abs_id="0" spreadInGroup="abstractContent" displayText="abstract_total_word_display" word_type="<?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>" validate="<?= $msg ?>" title="<?= $valueFields['display_name'] ?>"></textarea>
                                                        </div>
                                                    </div>

                                                <?php
                                                    $i++;
                                                }
                                                ?>
                                                <div class="col-xs-12 form-group input-material" id="wordCountList0">
                                                    <!-- <div class="checkbox"> -->
                                                    <label class="select-lable">Total Word Count</label>
                                                    <span style="display:unset;" use="abstract_total_word_display" limit="<?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>">
                                                        <span style="display:unset;" use="total_word_entered">0</span> /
                                                        <span style="display:unset;" use="total_word_limit"><?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?></span>
                                                        <span style="color: #ffff;display:unset;">(Total <?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?> are <?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>s allowed.)</span>
                                                    </span>
                                                    <!-- </div> -->
                                                </div>
                                                <div style="display:flex;width: 100%;">

                                                    <!-- <div class="col-6 col-lg-6 form-floating" id="faculty_upload" style="display: none;">
                                                        <label for="floatingInput">Upload File</label>
                                                        <div class="">
                                                            <input type="hidden" name="abstract_file_types" id="abstract_file_types" value=<?= "pdf", "word" ?> />
                                                            <input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name" use="upload_original_fileName" />
                                                            <input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename">
                                                            <input type="file" id="formFileAbstract" class="formFileAbstract hideitem" name="upload_abstract_file" abs_id="" style="display: none;" validate="Please upload abstract file">
                                                            <label for="formFileAbstract" class="input-label"><i class="fas fa-cloud-upload-alt"></i><br>Choose File<br>
                                                                <?php $type = json_decode('"pdf", "word" '); ?>
                                                                <span style="display: unset;">PDF | Word</span>
                                                                <br><span style="display: unset;" id="abstractFileNameUploaded"></span><br>
                                                            </label>

                                                        </div>
                                                    </div> -->
                                                    <?php
                                                    if ($hod_consent_file_types !== 'null') {
                                                    ?>
                                                        <div class="col-6 col-lg-6 form-floating">
                                                            <label for="floatingInput">HOD Consent</label>
                                                            <div class="">
                                                                <input type="hidden" name="hod_consent_file_types" id="hod_consent_file_types" value=<?= $hod_consent_file_types ?> />
                                                                <input type="hidden" name="original_consent_file_name" id="original_consent_file_name" use="upload_original_fileName" />
                                                                <input type="hidden" name="temp_consent_filename" id="temp_consent_filename">
                                                                <input type="file" id="formFileHod" class="formFileHod" name="upload_consent_abstract_file" abs_id="" style="display: none;">
                                                                <label for="formFileHod" class="input-label"><i class="fas fa-cloud-upload-alt"></i><br>Choose File<br>
                                                                    <?php $type = $hod_consent_file_types_decoded; ?>
                                                                    <span style="display: unset;"><?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?><?= ($type[1] != '') ? ucfirst($type[1]) : '' ?><?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?></span>
                                                                    <br><span style="display: unset;" id="concentFileNameUploaded"></span><br>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    if ($abstract_file_types !== 'null') { ?>
                                                        <div class="col-6 col-lg-6 form-floating">
                                                            <label for="floatingInput">Abstract File</label>
                                                            <div class="">
                                                                <input type="hidden" name="abstract_file_types" id="abstract_file_types" value=<?= $abstract_file_types ?> />
                                                                <input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name" use="upload_original_fileName" />
                                                                <input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename">
                                                                <input type="file" id="formFileAbstractx" class="formFileAbstract" name="upload_abstract_file" abs_id="" style="display: none;">
                                                                <label for="formFileAbstract" class="input-label"><i class="fas fa-cloud-upload-alt"></i><br>Choose File<br>
                                                                    <?php $type = json_decode($abstract_file_types); ?>
                                                                    <span style="display: unset;"><?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?><?= ($type[1] != '') ? ucfirst($type[1]) : '' ?><?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?></span>
                                                                    <br><span style="display: unset;" id="abstractFileNameUploaded"></span><br>
                                                                </label>

                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-inline ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step-abstract" step="4">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step-abstract submit-edit" step="4">Submit</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="clearfix"></div>
                        </form>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- ========================================== ABSTRACT EDIT MODAL ================================================ -->
<div class="abstract_view_modal">
    <div class="abstract_view_inner">
        <div class="abstract_view_box">
            <div class="abstract_head">
                <div class="abs_modal-prf">
                    <!-- non edit section heading -->
                    <h3 class="non-edit-sub-heading">Submitted Abstract List</h3>
                    <!-- non edit section heading -->
                    <!-- edit section heading -->
                    <h3 class="edit-sub-heading" style="display: none;"><b>Submission Code :</b> 87080437</h3>
                    <!-- edit section heading -->
                    <!-- add section heading -->
                    <h3 class="add-sub-heading" style="display: none;">Submit Abstract<a><i class="fa-solid fa-eye"></i><span>View Abstract Guidline</span></a></h3>
                    <!-- add section heading -->
                </div>
                <ul class="abs_mod_head mb-0">
                    <a class="m-0 abs-modal-close"><i class="fa-solid fa-xmark"></i></a>
                </ul>
            </div>
            <div class="abstract_body">
                <div class="abstract_details no-edit">
                    <ul class="abstract-list-box">
                        <?php
                        foreach ($abstract_details as $key => $value) {
                        ?>
                            <li class="abstract-list-content">
                                <?php if ($cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d')) { ?>
                                    <a class="abs-modal-edit" abs_id="<?= $value['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                <?php } ?>
                                <h4 class="abs-modal-submission"><b>Submission Code :</b> <?= $value['abstract_submition_code'] ?></h4>
                                <h4><b>Title :</b> <?= $value['abstract_title'] ?></h4>
                                <h4><b>Category :</b> <?= getAbstractCategoryName($value['abstract_cat']) ?></h4>
                                <p class="abst_sub_status">Abstract Submitted<span class="bst_sub_date"><?= date('d/m/Y', strtotime($value['created_dateTime'])) ?></span></p>
                                <ul class="attched_details">
                                    <?php if ($value['abstract_file'] != '') {
                                    ?>
                                        <li>
                                            <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                            <div class="attached_file">
                                                <p><?= $value['abstract_original_file_name'] ?><br><span><?= date('d/m/Y', strtotime($value['created_dateTime'])) ?></span></p>
                                                <a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] . $value['abstract_file'] ?>" target="_blank"><i class="fa-solid fa-download"></i></a>
                                            </div>

                                        </li>
                                    <?php }
                                    if ($value['abstract_consent_file'] != '') { ?>
                                        <li>
                                            <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                            <div class="attached_file">
                                                <p><?= $value['abstract_consent_original_file_name'] ?><br><span>Date</span></p>
                                                <a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] . $value['abstract_consent_file'] ?>" target="_blank"><i class="fa-solid fa-download"></i></a>
                                            </div>

                                        </li>
                                    <?php } ?>

                                </ul>
                                <span class="abs-number">1</span>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
                <?php foreach ($abstract_details as $key => $value) {
                    // echo '<pre>'; print_r($value);
                ?>
                    <!-- ================================= EDIT ==================================== -->
                    <div class="abstract_edit_wrap has-edit" id="abstract_edit_form_<?= $value['id'] ?>" style="padding: 18px 30px;">
                        <div class="wizard">
                            <form role="form" action="<?= _BASE_URL_ ?>abstract_request.process.php" class="login-box" name="abstractEditForm" id="abstractEditForm<?= $value['id'] ?>" enctype="multipart/form-data" method="post">
                                <input type="hidden" name="act" value="abstractUpdate" />
                                <input type="hidden" name="applicantId" id="applicantId" value="<?= $delegateId ?>" />
                                <input type="hidden" name="report_data" id="report_data" value="Abstract" />
                                <input type="hidden" name="abstract_id" value="<?= $value['id'] ?>">
                                <ul class="abs_mod_tab abstract_edit_tab_<?= $value['id'] ?> nav nav-tabs" role="tablist">
                                    <li role="presentation" style="pointer-events: none;" class="active">
                                        <a href="#step1_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step1_<?= $value['id'] ?>" role="tab" aria-expanded="true"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Author Details</span></a>
                                    </li>
                                    <li role="presentation" style="pointer-events: none;" class="disabled">
                                        <a href="#step2_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step2_<?= $value['id'] ?>" role="tab" aria-expanded="false"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Co-Author Details</span></a>
                                    </li>
                                    <li role="presentation" style="pointer-events: none;" class="disabled">
                                        <a href="#step3_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step3_<?= $value['id'] ?>" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Submission Category</span></a>
                                    </li>
                                    <li role="presentation" style="pointer-events: none;" class="disabled">
                                        <a href="#step4_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step4_<?= $value['id'] ?>" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Details</span></a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="abstract_edit_main_form">

                                    <div class="tab-pane active" role="tabpanel" id="step1_<?= $value['id'] ?>">
                                        <div class="author_details">
                                            <p class="abst_mod_title mb-3">Author Details</p>
                                            <div class="author-wrap">
                                                <div class="author_details_inner " id="author_details_edit_<?= $value['id'] ?>" style="padding: 10px 10px;">
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">E-mail</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" name="edit_author_email" id="edit_author_email" class="form-control" style="text-transform:lowercase;" usefor="email" value="<?= $value['abstract_author_email_id'] ?>" validate="Please enter the email id">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Mobile</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="edit_author_mobile" name="edit_author_mobile" value="<?= $value['abstract_author_phone_no'] ?>" placeholder="" validate="Please enter the mobile No" onkeypress="return isNumber(event)" maxlength="10">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Title</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <div class="checkbox-wrap">
                                                                <label class="custom-radio"><input type="radio" name="edit_author_title" value="DR" <?php if ($value['abstract_author_title'] == 'DR') {
                                                                                                                                                        echo 'checked';
                                                                                                                                                    } ?>> <span style="display:unset;" class="checkbox-text">Dr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="edit_author_title" value="PROF" <?php if ($value['abstract_author_title'] == 'PROF') {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>><span style="display:unset;" class="checkbox-text">Prof.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="edit_author_title" value="MR" <?php if ($value['abstract_author_title'] == 'MR') {
                                                                                                                                                        echo 'checked';
                                                                                                                                                    } ?>><span style="display:unset;" class="checkbox-text">Mr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="edit_author_title" value="MRS" <?php if ($value['abstract_author_title'] == 'MRS') {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>><span style="display:unset;" class="checkbox-text">Mrs.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="edit_author_title" value="MS" <?php if ($value['abstract_author_title'] == 'MS') {
                                                                                                                                                        echo 'checked';
                                                                                                                                                    } ?>> <span style="display:unset;" class="checkbox-text">Ms.</span><span style="display:unset;" class="checkmark"></span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">First Name</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="edit_author_first_name" name="edit_author_first_name" value="<?= $value['abstract_author_first_name'] ?>" placeholder="" validate="Please enter the first name">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Last Name</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="edit_author_last_name" name="edit_author_last_name" placeholder="" value="<?= $value['abstract_author_last_name'] ?>" validate="Please enter the last name">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Country</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <select class="form-control select" name="edit_author_country" id="edit_author_country_<?= $value['id'] ?>" forType="countryx" onchange="generateStateList(this,'edit_author_state_<?= $value['id'] ?>')" style="text-transform:uppercase;" required validate="Please select country">
                                                                <option value="">-- Select Country --</option>
                                                                <?php
                                                                $sqlFetchCountry   = array();
                                                                $sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
	                                                         WHERE `status` =? 
	                                                      ORDER BY `country_name` ASC";

                                                                $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                                if ($resultFetchCountry) {
                                                                    foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                                ?>
                                                                        <option value="<?= $rowFetchCountry['country_id'] ?>" <?php if ($rowFetchCountry['country_id'] == $value['abstract_author_country_id']) {
                                                                                                                                    echo 'selected';
                                                                                                                                } ?>><?= $rowFetchCountry['country_name'] ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">State</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <select class="form-control select" name="edit_author_state" id="edit_author_state_<?= $value['id'] ?>" forType="statex" style="text-transform:uppercase;" required validate="Please select state">
                                                                <option value="0">-- Select State --</option>
                                                                <?php
                                                                $sqlFetchCountry   = array();
                                                                $sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
	                                                         WHERE `status` =? 
	                                                      ORDER BY `state_name` ASC";

                                                                $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                                if ($resultFetchCountry) {
                                                                    foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                                ?>
                                                                        <option value="<?= $rowFetchCountry['st_id'] ?>" <?php if ($value['abstract_author_state_id']  == $rowFetchCountry['st_id']) {
                                                                                                                                echo 'selected';
                                                                                                                            } ?>><?= $rowFetchCountry['state_name'] ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">City</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="edit_author_city" name="edit_author_city" placeholder="" value="<?= $value['abstract_author_city'] ?>" validate="Please enter the city">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Postal Code</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_pincode" name="edit_author_pincode" value="<?= $value['abstract_author_pin'] ?>" placeholder="" validate="Please enter the postal code">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Institute</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_institute_name" name="edit_author_institute_name" value="<?= $value['abstract_author_institute_name'] ?>" placeholder="" validate="Please enter the postal code">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Department</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_department" name="edit_author_department" value="<?= $value['abstract_author_department'] ?>" placeholder="" validate="Please enter the postal code">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step-edit" abs_id="<?= $value['id'] ?>" step='1' disabled>Prev</button>
                                                <button type="button" class="default-btn next-btn next-step-edit" abs_id="<?= $value['id'] ?>" step='1' id="author_next_<?= $value['id'] ?>">Next</button></li>
                                            </div>
                                        </ul>
                                    </div>

                                    <div class="tab-pane" role="tabpanel" id="step2_<?= $value['id'] ?>">
                                        <p class="abst_mod_title mb-3">Co-Author Details <a class="add-coauthor-btn" abs_id="<?= $value['id'] ?>">Add More</a></p>
                                        <div class="author_details" id="">
                                            <div class="author-wrap ">
                                                <div class="author_details_inner coauthor-body" id="accordion-body-coauthor-<?= $value['id'] ?>">

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

                                                            $drChecked = ($val['abstract_coauthor_title'] == 'Dr') ? "checked" : "";
                                                    ?>
                                                            <!-- <div class="author-wrap add_coathor">
                                                        <div class="author_details_inner"> -->
                                                            <div id="newCoAuthor<?= $value['id'] ?>" class="add_coathor">
                                                                <div class="author_details_inner">
                                                                    <div class="col-12 form-floating">
                                                                        <p class="coauthor_li" style="justify-content: unset;">Co-Author &nbsp; <span id="coCount<?= $value['id'] ?>"><?= $i ?></span></p>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">E-mail</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control" id="abstract_coauthor_email_<?= $value['id'] ?>" name="abstract_coauthor_email[<?= $k ?>]" placeholder="" validate="Please enter the coauthor email" value="<?= $val['abstract_coauthor_email'] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">Mobile</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control" id="abstract_coauthor_mobile_<?= $value['id'] ?>" name="abstract_coauthor_mobile[<?= $k ?>]" placeholder="" validate="Please enter the coauthor mobile" onkeypress="return isNumber(event)" maxlength="10" value="<?= $val['abstract_coauthor_phone_no'] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 form-floating">
                                                                        <label for="floatingInput">Title</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <div class="checkbox-wrap">
                                                                                <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Dr" <?= $drChecked ?>> <span style="display:unset;" class="checkbox-text">Dr.</span><span class="checkmark" style="display:unset;"></span></label>
                                                                                <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Prof" <?php if (trim($val['abstract_coauthor_title']) == 'Prof') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo "";
                                                                                                                                                                                        } ?>> <span style="display:unset;" class="checkbox-text">Prof.</span><span class="checkmark" style="display:unset;"></span></label>
                                                                                <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Mr" <?php if (trim($val['abstract_coauthor_title']) == 'Mr') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo "";
                                                                                                                                                                                        } ?>> <span style="display:unset;" class="checkbox-text">Mr.</span><span class="checkmark" style="display:unset;"></span></label>
                                                                                <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Mrs" <?php if (trim($val['abstract_coauthor_title']) == 'Mrs') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo "";
                                                                                                                                                                                        } ?>><span style="display:unset;" class="checkbox-text">Mrs.</span><span class="checkmark" style="display:unset;"></span></label>
                                                                                <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Ms" <?php if (trim($val['abstract_coauthor_title']) == 'Ms') {
                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo "";
                                                                                                                                                                                        } ?>><span style="display:unset;" class="checkbox-text">Ms.</span><span class="checkmark" style="display:unset;"></span></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">First Name</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control" id="abstract_coauthor_first_name_<?= $value['id'] ?>" name="abstract_coauthor_first_name[<?= $k ?>]" placeholder="" validate="Please enter the coauthor first name" value="<?= $val['abstract_coauthor_first_name'] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">Last Name</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control" id="abstract_coauthor_last_name_<?= $value['id'] ?>" name="abstract_coauthor_last_name[<?= $k ?>]" placeholder="" validate="Please enter the coauthor last name" value="<?= $val['abstract_coauthor_last_name'] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">Country</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <select class="form-control select" name="abstract_coauthor_country[<?= $k ?>]" id="abstract_coauthor_country_<?= $value['id'] ?>" forType="countryx" onchange="generateStateList(this,'abstract_coauthor_state_<?= $value['id'] ?>')" style="text-transform:uppercase;" required validate="Please select the coauthor country">
                                                                                <option value="0">-- Select Country --</option>
                                                                                <?php
                                                                                $sqlFetchCountry   = array();
                                                                                $sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
																	                         WHERE `status` =? 
																	                      ORDER BY `country_name` ASC";

                                                                                $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                                $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                                                if ($resultFetchCountry) {
                                                                                    foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                                                ?>
                                                                                        <option value="<?= $rowFetchCountry['country_id'] ?>" <?php if ($val['abstract_coauthor_country_id'] == $rowFetchCountry['country_id']) {
                                                                                                                                                    echo 'selected';
                                                                                                                                                } ?>><?= $rowFetchCountry['country_name'] ?></option>
                                                                                <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">State</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <select class="form-control select" name="abstract_coauthor_state[<?= $k ?>]" id="abstract_coauthor_state_<?= $value['id'] ?>" forType="statex" style="text-transform:uppercase;" required validate="Please select the coauthor state">
                                                                                <option value="0" selected>Select State</option>
                                                                                <?php
                                                                                $sqlFetchState   = array();
                                                                                $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
																                         WHERE `status` =? 
																                      ORDER BY `state_name` ASC";

                                                                                $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                                $resultFetchState = $mycms->sql_select($sqlFetchState);
                                                                                if ($resultFetchState) {
                                                                                    foreach ($resultFetchState as $keyState => $rowFetchState) {
                                                                                ?>
                                                                                        <option value="<?= $rowFetchState['st_id'] ?>" <?php if ($val['abstract_coauthor_state_id'] == $rowFetchState['st_id']) {
                                                                                                                                            echo 'selected';
                                                                                                                                        } ?>><?= $rowFetchState['state_name'] ?></option>
                                                                                <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">City</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control" id="abstract_coauthor_city_<?= $value['id'] ?>" name="abstract_coauthor_city[<?= $k ?>]" placeholder="" validate="Please enter the coauthor city" value="<?= $val['abstract_coauthor_city_name'] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">Postal Code</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control" id="abstract_coauthor_pincode_<?= $value['id'] ?>" name="abstract_coauthor_pincode[<?= $k ?>]" placeholder="" validate="Please enter the coauthor pincode" value="<?= $val['abstract_coauthor_pincode'] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">Institute</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control coauther_details " use="Institute" id="abstract_coauthor_institute_<?= $value['id'] ?>" name="abstract_coauthor_institute[<?= $k ?>]" placeholder="" value="<?= $val['abstract_coauthor_institute_name'] ?>" validate="Please enter the institute" autocomplete="nope">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 form-floating">
                                                                        <label for="floatingInput">Department</label>
                                                                        <div class="d-flex">
                                                                            <span><img src="images/email-R.png" alt=""></span>
                                                                            <input type="text" class="form-control coauther_details " use="Department" id="abstract_coauthor_department_<?= $value['id'] ?>" name="abstract_coauthor_department[<?= $k ?>]" placeholder="" value="<?= $val['abstract_coauthor_department'] ?>" validate="Please enter the Department" autocomplete="nope">
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="coAuthorCounts" id="coAuthorCounts<?= $value['id'] ?>" value="<?= $i ?>">
                                                                    <!-- </div> -->

                                                                </div>
                                                            </div>
                                                        <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                        <input type="hidden" id="existingCoAuthorNum<?= $value['id'] ?>" value=<?= $i - 1 ?>>
                                                    <?php
                                                    } else {    // if co author exists end
                                                    ?>
                                                        <div id="newCoAuthor<?= $value['id'] ?>" class="author-wrap add_coathor">
                                                            <div class="author_details_inner">
                                                                <div class="col-12 form-floating">
                                                                    <p class="coauthor_li" style="justify-content: unset;">Co-Author :&nbsp; <span id="coCount<?= $value['id'] ?>">1</span></p>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">E-mail</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="email" class="form-control coauther_details " use="Email" id="abstract_coauthor_email_<?= $value['id'] ?>" name="abstract_coauthor_email[0]" placeholder="" validate="Please enter the coauthor email">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Mobile</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Mobile" id="abstract_coauthor_mobile_<?= $value['id'] ?>" name="abstract_coauthor_mobile[0]" placeholder="" onkeypress="return isNumber(event)" maxlength="10" validate="Please enter the coauthor mobile" onkeypress="return isNumber(event)" maxlength="10">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 form-floating">
                                                                    <label for="floatingInput">Title</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <div class="checkbox-wrap">
                                                                            <label class="custom-radio">
                                                                                <input type="radio" name="abstract_coauthor_title[0]" value="DR" checked=""><span style="display:unset;" class="checkbox-text">Dr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="PROF"><span style="display:unset;" class="checkbox-text">Prof.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MR"><span style="display:unset;" class="checkbox-text">Mr.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MRS"><span style="display:unset;" class="checkbox-text">Mrs.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MS"><span style="display:unset;" class="checkbox-text">Ms.</span><span style="display:unset;" class="checkmark"></span></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">First Name</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="First Name" id="abstract_author_first_name_<?= $value['id'] ?>" name="abstract_coauthor_first_name[0]" placeholder="" validate="Please enter the coauthor first name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Last Name</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Last Name" id="abstract_author_last_name_<?= $value['id'] ?>" name="abstract_coauthor_last_name[0]" placeholder="" validate="Please enter the coauthor last name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Country</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <select class="form-control select coauther_details " use="Country" name="abstract_coauthor_country[0]" id="abstract_coauthor_country_<?= $value['id'] ?>" forType="countryx" onchange="generateStateList(this,'abstract_coauthor_state_<?= $value['id'] ?>')" style="text-transform:uppercase;" required validate="Please select the coauthor country">
                                                                            <option value="0">-- Select Country --</option>
                                                                            <?php
                                                                            $sqlFetchCountry   = array();
                                                                            $sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
																		WHERE `status` =? 
																	ORDER BY `country_name` ASC";

                                                                            $sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                            $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                                            if ($resultFetchCountry) {
                                                                                foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                                            ?>
                                                                                    <option value="<?= $rowFetchCountry['country_id'] ?>"><?= $rowFetchCountry['country_name'] ?></option>
                                                                            <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">State</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <select class="form-control select coauther_details " use="State" name="abstract_coauthor_state[0]" id="abstract_coauthor_state_<?= $value['id'] ?>" forType="state" style="text-transform:uppercase;" required validate="Please select the coauthor state">
                                                                            <option value="0" selected>Select State</option>
                                                                            <?php
                                                                            $sqlFetchState   = array();
                                                                            $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
																	WHERE `status` =? 
																ORDER BY `state_name` ASC";

                                                                            $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                                                            $resultFetchState = $mycms->sql_select($sqlFetchState);
                                                                            if ($resultFetchState) {
                                                                                foreach ($resultFetchState as $keyState => $rowFetchState) {
                                                                            ?>
                                                                                    <option value="<?= $rowFetchState['st_id'] ?>"><?= $rowFetchState['state_name'] ?></option>
                                                                            <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">City</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="City" id="abstract_coauthor_city_<?= $value['id'] ?>" name="abstract_coauthor_city[0]" placeholder="" validate="Please enter the coauthor city">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Postal Code</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Postal Code" id="abstract_author_pincode_<?= $value['id'] ?>" name="abstract_coauthor_pincode[0]" placeholder="" validate="Please enter the coauthor pincode" onkeypress="return isNumber(event)">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Institute</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Institute" id="abstract_coauthor_institute_<?= $value['id'] ?>" name="abstract_coauthor_institute[0]" placeholder="" validate="Please enter the institute" autocomplete="nope">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Department</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Department" id="abstract_coauthor_department_<?= $value['id'] ?>" name="abstract_coauthor_department[0]" placeholder="" validate="Please enter the Department" autocomplete="nope">
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="coAuthorCounts" id="coAuthorCounts<?= $value['id'] ?>" value="1">
                                                            </div>

                                                        </div>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                        </div>
                                        <ul class="list-inline   ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step-edit" abs_id="<?= $value['id'] ?>" step='2'>Prev</button>
                                                <button type="button" class="default-btn next-btn next-step-edit" abs_id="<?= $value['id'] ?>" step='2'>Next</button></li>
                                            </div>
                                        </ul>
                                    </div>

                                    <div class="tab-pane" role="tabpanel" id="step3_<?= $value['id'] ?>">
                                        <div class="author_details" style="height: calc(50% - 61px);">
                                            <p class="abst_mod_title mb-3">Submission Category</p>
                                            <div class="author-wrap m-0">
                                                <div class="sub-cate-checkbox-wrap" id="abs_category_<?= $value['id'] ?>">

                                                    <?php
                                                    // echo "<pre>"; print_r($value);
                                                    $abs_id = $value['id'];
                                                    $Abs_cat_id = $value['abstract_cat'];
                                                    $abs_sub_cat_id = $value['abstract_parent_type'];
                                                    $selected_topic_id = $value['abstract_topic_id'];
                                                    if (count($abstract_details) < 10) {

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


                                                        //echo '<pre>'; print_r($resultAbstractTopic);
                                                        foreach ($resultAbstractTopic as $key => $valueCat) {
                                                            $sqlAbstractSubcat['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
																								WHERE `related_category_id`='" . $value['id'] . "' AND `related_topic_id`='' AND
																								`status` = 'A' ORDER BY `id` ASC";

                                                            $resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);
                                                            $nomination_ids = array();
                                                            if ($resultAbstractSubcat) {
                                                                foreach ($resultAbstractSubcat as $key => $nomination) {
                                                                    array_push($nomination_ids, $nomination['id']);
                                                                }
                                                            }
                                                            $nomination_ids = json_encode($nomination_ids);

                                                            if (in_array(trim($valueCat['id']), $abstractCatArray)) {

                                                                $checked = 'checked';
                                                                $disabled = 'disabled';
                                                            } else {

                                                                $checked = '';
                                                                $disabled = '';
                                                            }
                                                    ?>

                                                            <label class="custom-radio">
                                                                <input type="radio" class="custom-radio trigger_subCategory" value="<?= $valueCat['id'] ?>" id="flexCheckDefault1<?= $abs_id . "_" . $valueCat['id'] ?>" name="abstract_category" onclick="abstractTypeChange(this,'<?= $abs_id ?>','<?= $valueCat['id'] ?>','<?= $selected_topic_id ?>','<?php echo implode(',', json_decode($valueCat['category_fields'])); ?>','<?php echo implode(',', json_decode($nomination_ids)); ?>')" <?= $valueCat['id'] == $value['abstract_cat'] ? "checked" : "" ?> required>
                                                                <span class="checkbox-text"><?= $valueCat['category'] ?></span>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                    <?php

                                                        }
                                                    }

                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($resultAbstractSubmission) { ?>
                                            <div class="author_details" style="height: calc(50% - 61px);">
                                                <p class="abst_mod_title mb-3">Submission Sub Category</p>
                                                <div class="author-wrap m-0">
                                                    <div id="collapseTwo2_<?= $value['id'] ?>" class="sub-cate-checkbox-wrap">
                                                        <?php
                                                        if (count($abstract_details) < 10) {
                                                            //echo '<pre>'; print_r($resultAbstractSubmission);
                                                            foreach ($resultAbstractSubmission as $key => $valueSubCat) {

                                                        ?>
                                                                <!-- <div class="sub-cate-checkbox-wrap submissionTypeRadio<?= $valueSubCat['category'] ?> hidesub" > -->
                                                                <label class="custom-radio  submissionTypeRadio<?= $valueSubCat['category'] ?>_<?= $abs_id ?>  hidesub_<?= $abs_id ?>" for="flexCheckDefault<?= $abs_id . "_" . $valueSubCat['category'] . "_" . $valueSubCat['id'] ?>">
                                                                    <input type="radio" class="custom-radio trigger_subCategory" id="flexCheckDefault<?= $abs_id . "_" . $valueSubCat['category'] . "_" . $valueSubCat['id'] ?>" name="abstract_parent_type" value="<?= $valueSubCat['id'] ?>" titleWordCountLimit="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>" contentWordCountLimit="<?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>" relatedSubmissionSubType="" onclick="abstractSubmissionType(this,'<?php echo $valueSubCat['category']; ?>','<?php echo $valueSubCat['id']; ?>')" relatedSubmissionSubType="" <?= trim($valueSubCat['id']) == trim($value['abstract_parent_type']) ? "checked" : "" ?>>
                                                                    <span class="checkbox-text"><?= $valueSubCat['abstract_submission'] ?></span>
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                                <!-- </div> -->
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <ul class="list-inline ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step-edit" abs_id="<?= $value['id'] ?>" step='3'>Prev</button>
                                                <button type="button" class="default-btn next-btn  next-step-edit" abs_id="<?= $value['id'] ?>" step='3'>Next</button></li>
                                            </div>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step4_<?= $value['id'] ?>" use="abstractDetails<?= $value['id'] ?>">
                                        <div class="author_details">
                                            <p class="abst_mod_title mb-3">Edit Your Abstract</p>
                                            <div class="author-wrap">
                                                <div class="author_details_inner" id="abstract_details_<?= $value['id'] ?>">
                                                    <?php
                                                    $sqlAbstractTopic              =    array();
                                                    $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
																											  WHERE `status` = ? 
																										   ORDER BY `id` ASC";

                                                    $sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');
                                                    $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
                                                    // print_r($resultAbstractTopic);

                                                    if ($resultAbstractTopic) {
                                                    ?>
                                                        <div class="col-12 col-lg-12 form-floating">
                                                            <label for="floatingInput">Topic</label>
                                                            <div class="d-flex">
                                                                <span><img src="images/email-R.png" alt=""></span>
                                                                <select name="abstract_topic_id" id="abstract_topic_id_<?= $value['id'] ?>" class="form-control select" required="required" validate="Please select the topic">
                                                                    <option>--Choose your topic--</option>
                                                                    <?php
                                                                    $sqlAbstractTopic              =    array();
                                                                    $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
																				  WHERE `status` = ? 
																			   ORDER BY `id` ASC";

                                                                    $sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');
                                                                    $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

                                                                    if ($resultAbstractTopic) {
                                                                        foreach ($resultAbstractTopic as $keyAbstractTopic => $rowAbstractTopic) {
                                                                    ?>

                                                                            <option value="<?= $rowAbstractTopic['id'] ?>" <?= $rowAbstractTopic['id'] == $value['abstract_topic_id'] ? "selected" : "" ?>><?= $rowAbstractTopic['abstract_topic'] ?></option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    <?php } ?>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Title</label>
                                                        <?php

                                                        if ($cfg['ABSTRACT.TITLE.WORD.TYPE'] == 'character') {
                                                            $abstract_title_length = strlen(trim($value['abstract_title']));
                                                        } else {
                                                            $abstract_title_length = str_word_count(trim($value['abstract_title']));
                                                        }
                                                        ?>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea name="abstract_title" id="abstract_title_<?= $value['id'] ?>" checkFor="wordCount" abs_id="<?= $value['id'] ?>" spreadInGroup="abstractTitle" displayText="abstract_title_word_count" style="text-transform:uppercase;height: unset;" validate="Please enter the abstract title" required><?= $value['abstract_title'] ?></textarea>
                                                        </div>
                                                        <div class="alert alert-success" style="display:block;">
                                                            <span style="display:flex;" use="abstract_title_word_count" limit="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>">
                                                                <span style="display:block;" use="total_word_entered"><?= $abstract_title_length; ?></span> /
                                                                <span style="display:block;" use="total_word_limit"><?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?></span>
                                                                <span style="color: #D41000;display:block;">(Title should be within <?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?> <?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>s.)</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $sqlAbstractFields              =    array();
                                                    $sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
																		  WHERE `status` = ?";

                                                    $sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

                                                    $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                                    $i = 1;
                                                    foreach ($resultAbstractFields as $key => $valueFields) {


                                                        $sqlAbstractFieldsVal              =    array();
                                                        $sqlAbstractFieldsVal['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . " 
																		  WHERE " . $valueFields['field_key'] . "!='NULL' AND id='" . $value['id'] . "'";

                                                        $resultAbstractFieldsVal = $mycms->sql_select($sqlAbstractFieldsVal);
                                                        //echo '<pre>'; print_r( $resultAbstractFieldsVal[0]['COUNTDATA']);

                                                        if ($resultAbstractFieldsVal[0]['COUNTDATA'] > 0) {

                                                            $msg = "Please enter the " . strtolower($valueFields['display_name']);


                                                    ?>
                                                            <!-- <div class="col-12 col-lg-12 form-floating">
                                                                <label for="floatingInput">Introduction</label>
                                                                <div class="d-flex">
                                                                    <span><img src="images/email-R.png" alt=""></span>
                                                                    <textarea></textarea>
                                                                </div>
                                                            </div> -->
                                                            <div class="col-12 col-lg-12 form-floating field_div_<?= $value['id'] ?>" relatedSubmissionType="" relatedSubmissionSubType="" id="field_div_<?= $valueFields['id'] ?>_<?= $value['id'] ?>" style="display:none">
                                                                <label for="floatingInput"><?= $valueFields['display_name'] ?></label>
                                                                <div class="d-flex">
                                                                    <!-- <span><img src="images/email-R.png" alt=""></span> -->
                                                                    <textarea style="height: unset;" name="<?= $valueFields['field_key'] ?>[]" id="fieldVal_<?= $valueFields['id'] ?>_<?= $value['id'] ?>" checkFor="wordCount" abs_id="<?= $value['id'] ?>" spreadInGroup="abstractContent" displayText="abstract_total_word_display" validate="<?= $msg ?>" title="<?= $valueFields['display_name'] ?>"><?= $value[$valueFields['field_key']] ?></textarea>
                                                                </div>
                                                            </div>



                                                    <?php
                                                        }
                                                    }
                                                    ?>

                                                    <div class="col-xs-12 form-group input-material " id="wordCountList<?= $value['id'] ?>">
                                                        <!-- <div class="checkbox"> -->
                                                        <label class="select-lable">Total Word Count</label>
                                                        <span style="display:unset;" use="abstract_total_word_display" limit="<?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>">
                                                            <span style="display:unset;" use="total_word_entered">0</span> /
                                                            <span style="display:unset;" use="total_word_limit"><?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?></span>
                                                            <span style="color: #ffff;display:unset;">(Total <?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?> are <?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>s allowed.)</span>
                                                        </span>
                                                        <!-- </div> -->
                                                    </div>
                                                    <div style="display:flex;width: 100%;">
                                                        <?php
                                                        if ($hod_consent_file_types !== 'null') {
                                                        ?>
                                                            <div class="col-6 col-lg-6 form-floating">

                                                                <label for="floatingInput">Only one image/graph/table/illustration may be included</label>
                                                                <div class="">
                                                                    <input type="hidden" name="sessionId" id="sessionId<?= $value['id'] ?>" use="sessionId" value="<?= session_id() ?>" />
                                                                    <input type="hidden" name="hod_consent_file_types" id="hod_consent_file_types<?= $value['id'] ?>" value=<?= $hod_consent_file_types ?> />
                                                                    <input type="hidden" name="abstractId" value="<?= $value['id'] ?>" />
                                                                    <input type="hidden" name="original_consent_file_name" id="original_consent_file_name<?= $value['id'] ?>" use="upload_original_fileName" />
                                                                    <input type="hidden" name="temp_consent_filename" id="temp_consent_filename<?= $value['id'] ?>">
                                                                    <input type="file" class="formFileHod" name="upload_consent_abstract_file" id="formFileHod<?= $value['id'] ?>" abs_id="<?= $value['id'] ?>" style="display: none;">
                                                                    <label for="formFileHod<?= $value['id'] ?>" class="input-label">
                                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                                        <?php $type = $hod_consent_file_types_decoded; ?>
                                                                        <br>Choose File<br>
                                                                        <span style="display: unset;"><?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?><?= ($type[1] != '') ? ucfirst($type[1]) : '' ?><?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?></span>
                                                                        <br><span style="display: unset;" id="concentFileNameUploaded<?= $value['id'] ?>"></span><br>
                                                                    </label>

                                                                    <?php
                                                                    if (!empty($value['abstract_consent_file'])) {
                                                                    ?>
                                                                        <ul class="attched_details mt-3" style="grid-template-columns:unset;">
                                                                            <li>
                                                                                <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                                <div class="attached_file">
                                                                                    <p><a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] . $value['abstract_consent_file'] ?>" target="_blank"><?= $value['abstract_consent_original_file_name'] ?></a><br><span>Date</span></p>
                                                                                    <!-- <a><i class="fas fa-trash-alt"></i></a> -->
                                                                                </div>
                                                                            </li>
                                                                            <!-- <li>
                                                                        <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                        <div class="attached_file">
                                                                            <p>Attached Name<br><span>Date</span></p>
                                                                            <a><i class="fas fa-trash-alt"></i></a>
                                                                        </div>
                                                                    </li> -->
                                                                        </ul>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>

                                                        <?php } ?>
                                                        <!-- <div class="col-6 col-lg-6 form-floating" id="faculty_upload_<?= $value['id'] ?>" style="display: none;">
                                                            <label for="floatingInput">Upload File</label>
                                                            <div class="">
                                                                <input type="hidden" name="abstract_file_types" id="abstract_file_types<?= $value['id'] ?>" value='"pdf","word"' />
                                                                <input type="hidden" name="abstractId" value="<?= $value['id'] ?>" />
                                                                <input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name<?= $value['id'] ?>" use="upload_original_fileName" />
                                                                <input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename<?= $value['id'] ?>">
                                                                <input type="file" class="formFileAbstract hideitem" name="upload_abstract_file" id="formFileAbstract<?= $value['id'] ?>" abs_id="<?= $value['id'] ?>" style="display: none;" validate="Please upload abstract file">
                                                                <label for="formFileAbstract<?= $value['id'] ?>" class="input-label">
                                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                                    <?php $type = json_decode('"pdf","word"'); ?>
                                                                    <br>Choose File<br>
                                                                    <span style="display: unset;">PDF | Word</span>
                                                                    <br><span style="display: unset;" id="abstractFileNameUploaded<?= $value['id'] ?>"></span><br>
                                                                </label>
                                                                <?php
                                                                if (!empty($value['abstract_file'])) {
                                                                ?>
                                                                    <ul class="attched_details mt-3" style="grid-template-columns:unset;">
                                                                        <li>
                                                                            <span style="display:unset" class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                            <div class="attached_file">
                                                                                <p><a style="background:unset" href="<?= $cfg['FILES.ABSTRACT.REQUEST'] . $value['abstract_file'] ?>" target="_blank"><?= $value['abstract_original_file_name'] ?></a>
                                                                                    <br><span>Date</span>
                                                                                </p>
                                                                                <a><i class="fas fa-trash-alt"></i></a>
                                                                            </div>
                                                                        </li>
                                                                      
                                                                    </ul>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div> -->
                                                        <?php
                                                        if ($abstract_file_types !== 'null') {
                                                        ?>
                                                            <div class="col-6 col-lg-6 form-floating">

                                                                <label for="floatingInput">Abstract</label>
                                                                <div class="">
                                                                    <input type="hidden" name="sessionId" id="sessionId<?= $value['id'] ?>" use="sessionId" value="<?= session_id() ?>" />
                                                                    <input type="hidden" name="abstract_file_types" id="abstract_file_types<?= $value['id'] ?>" value=<?= $abstract_file_types ?> />
                                                                    <input type="hidden" name="abstractId" value="<?= $value['id'] ?>" />
                                                                    <input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name<?= $value['id'] ?>" use="upload_original_fileName" />
                                                                    <input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename<?= $value['id'] ?>">
                                                                    <input type="file" class="formFileAbstract" name="upload_abstract_file" id="formFileAbstract<?= $value['id'] ?>" abs_id="<?= $value['id'] ?>" style="display: none;">
                                                                    <label for="formFileAbstract<?= $value['id'] ?>" class="input-label">
                                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                                        <?php $type = json_decode($abstract_file_types); ?>
                                                                        <br>Choose File<br>
                                                                        <span style="display: unset;"><?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?><?= ($type[1] != '') ? ucfirst($type[1]) : '' ?><?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?></span>
                                                                        <br><span style="display: unset;" id="abstractFileNameUploaded<?= $value['id'] ?>"></span><br>
                                                                    </label>


                                                                    <?php
                                                                    if (!empty($value['abstract_file'])) {
                                                                    ?>
                                                                        <ul class="attched_details mt-3" style="grid-template-columns:unset;">
                                                                            <li>
                                                                                <span style="display:unset" class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                                <div class="attached_file">
                                                                                    <p><a style="background:unset" href="<?= $cfg['FILES.ABSTRACT.REQUEST'] . $value['abstract_file'] ?>" target="_blank"><?= $value['abstract_original_file_name'] ?></a>
                                                                                        <br><span>Date</span>
                                                                                    </p>
                                                                                    <a><i class="fas fa-trash-alt"></i></a>
                                                                                </div>
                                                                            </li>
                                                                            <!-- <li>
                                                                        <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                        <div class="attached_file">
                                                                            <p>Attached Name<br><span>Date</span></p>
                                                                            <a><i class="fas fa-trash-alt"></i></a>
                                                                        </div>
                                                                    </li> -->
                                                                        </ul>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step-edit" abs_id="<?= $value['id'] ?>" step='4'>Prev</button>
                                                <button type="button" class="default-btn next-btn next-step-edit" abs_id="<?= $value['id'] ?>" step='4'>Submit</button></li>
                                                <!-- submit-edit -->
                                            </div>
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            var selected_topic_id = $('#abstract_topic_id').val();
                            var abs_id = '<?= $abs_id ?>';
                            var cat_id = '<?= $Abs_cat_id ?>';
                            var naomination_id = '<?= $resultSelectedNomination[0]['award_id'] ?>';

                            localStorage.setItem("catId" + abs_id, cat_id);
                            if ('<?= $value['abstract_file'] ?>') {
                                localStorage.setItem("absFile" + abs_id, true);

                            } else {
                                localStorage.setItem("absFile" + abs_id, false);
                            }
                            // alert(localStorage.getItem("absFile" + abs_id));

                            // alert(naomination_id)
                            $('#flexCheckDefault1' + abs_id + "_" + cat_id).click();
                            // $('input[type=radio][name=abstract_category]:checked').click();
                            $('#nomination_name_' + naomination_id).prop('checked', true);

                            var sub_cat = <?= $abs_sub_cat_id == "" ? 0 : $abs_sub_cat_id ?>;
                            $('#flexCheckDefault' + abs_id + "_" + cat_id + "_" + sub_cat).click();
                        });
                    </script>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // var selected_topic_id = $('#abstract_topic_id').val();
        // var abs_id = '<?= $abs_id ?>';
        // var cat_id = '<?= $Abs_cat_id ?>';
        // var naomination_id = '<?= $resultSelectedNomination[0]['award_id'] ?>';
        // // alert(naomination_id)
        // $('#flexCheckDefault1' + abs_id + "_" + cat_id).click();
        // $('input[type=radio][name=abstract_category]:checked').click();
        // $('#nomination_name_' + naomination_id).prop('checked', true);

        // var sub_cat = <?= $abs_sub_cat_id == "" ? 0 : $abs_sub_cat_id ?>;
        // $('#flexCheckDefault' + abs_id + "_" + cat_id + "_" + sub_cat).prop('checked', true);

        // alert(sub_cat);
        if (sub_cat == 0) {
            $.ajax({
                type: "POST",
                url: "abstract_request.process.php",
                data: {
                    action: 'generateTopic',
                    topic: cat_id
                },
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    if (JSONObject) {
                        if (JSONObject.trim() == 'empty') {
                            $('#topicDetails').hide();
                            //document.getElementById("abstract_topic_id").required = false;
                            $('#abstract_topic_id').html("")
                            $('#abstract_topic_id').attr('required', false);
                        } else {
                            $('#topicDetails').show();
                            $('#abstract_topic_id').html(JSONObject)
                        }
                    }
                    // $('#abstract_topic_id').val(selected_topic_id).change();
                    $('#abstract_topic_id').find('option[value^="' + selected_topic_id + '"]').prop('selected', true);




                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                }
            });
        } else {
            // $('#flexCheckDefault' + abs_id + "_" + cat_id + "_" + sub_cat).prop('checked', true);

            $.ajax({
                type: "POST",
                url: "abstract_request.process.php",
                data: {
                    action: 'generateCatSubTopic',
                    cat_id: cat_id,
                    submission_id: sub_cat,
                },
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    if (JSONObject.trim() == 'empty') {
                        $('#topicDetails').hide();
                        //document.getElementById("abstract_topic_id").required = false;
                        $('#abstract_topic_id').attr('required', false);
                    } else {
                        $('#topicDetails').show();
                        $('#abstract_topic_id').html('');
                        $('#abstract_topic_id').html(JSONObject);
                    }
                    $('#abstract_topic_id').find('option[value^="' + selected_topic_id + '"]').prop('selected', true);


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                }

            });
        }
        // var topic_id='';
        // $('#abstract_topic_id').val(topic_id);

        $('.formFileAbstract').on('change', function() {
            var abs_id = $(this).attr('abs_id');
            // function abstract_file_upload(obj,id){
            var file = this.files[0];

            flag = 0;
            if (file) {
                var fileSize = file.size; // in bytes
                var fileType = file.type;

                var validExtensions = new Array();
                var abstract_file_types = $('#abstract_file_types' + abs_id).val();
                if (abstract_file_types.includes("pdf")) {
                    validExtensions.push("pdf");
                }
                if (abstract_file_types.includes("image")) {
                    validExtensions.push("jpg");
                    validExtensions.push("jpeg");
                    validExtensions.push("png");
                }
                if (abstract_file_types.includes("word")) {
                    validExtensions.push("doc");
                    validExtensions.push("docx");
                }
                var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
                var fileTypeErr = "Only " + jsonString + " files are allowed";
                // var validExtensions = ["doc", "docx", "pdf"]
                var fileName = file.name.split('.').pop();

                console.log(fileName);

                if (fileSize > 5 * 1024 * 1024) {

                    var prevFile = $('#temp_abstract_filename' + abs_id).val();
                    if (prevFile != '') {
                        deleteTempFile(prevFile);
                    }
                    $('#temp_abstract_filename' + abs_id).val('');
                    $('#original_abstract_file_name' + abs_id).val('');
                    $('#abstractFileNameUploaded' + abs_id).text('');

                    toastr.error('File size exceeds 5MB limit.', 'Error', {
                        "progressBar": true,
                        "timeOut": 5000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    this.value = ''; // Clear the file input
                    flag = 1;
                    return;
                }

                if (validExtensions.indexOf(fileName) == -1) {
                    var prevFile = $('#temp_abstract_filename' + abs_id).val();
                    if (prevFile != '') {
                        deleteTempFile(prevFile);
                    }
                    $('#temp_abstract_filename' + abs_id).val('');
                    $('#original_abstract_file_name' + abs_id).val('');
                    $('#abstractFileNameUploaded' + abs_id).text('');
                    // alert(fileTypeErr)
                    toastr.error(fileTypeErr, 'Error', {
                        "progressBar": true,
                        "timeOut": 5000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });


                    this.value = '';
                    flag = 1;
                    return;
                }
                // File is valid
                $('#fileError').text('');
            } else {
                var prevFile = $('#temp_abstract_filename' + abs_id).val();
                if (prevFile != '') {
                    deleteTempFile(prevFile);
                }
                $('#temp_abstract_filename' + abs_id).val('');
                $('#original_abstract_file_name' + abs_id).val('');
                $('#abstractFileNameUploaded' + abs_id).text('');
                // toastr.error('Please select a file to upload.', 'Error', {
                //     "progressBar": true,
                //     "timeOut": 4000,
                //     "showMethod": "slideDown",
                //     "hideMethod": "slideUp"
                // });

                flag = 1;
            }
            if (flag == 0) {
                var sessionId = $('#sessionId' + abs_id).val();
                var d = new Date();
                var uploadTime = d.getTime();
                var prevFile = $('#temp_abstract_filename' + abs_id).val();
                // createDynamicFileName
                var dynamicFileName = 'ABSTRACT_' + sessionId + uploadTime + '_' + file['name'];
                // alert(dynamicFileName);
                $('#temp_abstract_filename' + abs_id).val(dynamicFileName);
                $('#original_abstract_file_name' + abs_id).val(file['name']);
                $('#abstractFileNameUploaded' + abs_id).text(file['name']);
                uploadFile(file, 'ABSTRACT_' + sessionId + uploadTime, prevFile); // Upload the file
                toastr.success('File uploaded successfully.', 'Success', {
                    "progressBar": true,
                    "timeOut": 2000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });

            }
        });

        $('.formFileHod').on('change', function() {

            var abs_id = $(this).attr('abs_id');

            var file = this.files[0];
            var flag = 0;
            if (file) {
                var fileSize = file.size; // in bytes
                var fileType = file.type;

                var validExtensions = new Array();

                var hod_consent_file_types = $('#hod_consent_file_types' + abs_id).val();
                if (hod_consent_file_types.includes("pdf")) {
                    validExtensions.push("pdf");
                }
                if (hod_consent_file_types.includes("image")) {
                    validExtensions.push("jpg");
                    validExtensions.push("jpeg");
                    validExtensions.push("png");
                }
                if (hod_consent_file_types.includes("word")) {
                    validExtensions.push("doc");
                    validExtensions.push("docx");
                }

                var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
                var fileTypeErr = "Only " + jsonString + " files are allowed";
                // var validExtensions = ["jpg", "pdf", "jpeg", "gif", "png", "pdf"]
                var fileName = file.name.split('.').pop();

                console.log(fileName);

                if (fileSize > 5 * 1024 * 1024) {
                    var prevFile = $('#temp_consent_filename' + abs_id).val();
                    if (prevFile != '') {
                        deleteTempFile(prevFile);
                    }
                    $('#temp_consent_filename' + abs_id).val('');
                    $('#original_consent_file_name' + abs_id).val('');
                    $('#concentFileNameUploaded' + abs_id).text('');

                    toastr.error('File size exceeds 5MB limit.', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    this.value = ''; // Clear the file input
                    flag = 1;
                    return;

                }
                if (validExtensions.indexOf(fileName) == -1) {
                    var prevFile = $('#temp_consent_filename' + abs_id).val();
                    if (prevFile != '') {
                        deleteTempFile(prevFile);
                    }
                    $('#temp_consent_filename' + abs_id).val('');
                    $('#original_consent_file_name' + abs_id).val('');
                    $('#concentFileNameUploaded' + abs_id).text('');

                    toastr.error(fileTypeErr, 'Error', {
                        "progressBar": true,
                        "timeOut": 4000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });


                    this.value = '';
                    flag = 1;
                    return;
                }
                // File is valid
                $('#fileError').text('');
            } else {
                var prevFile = $('#temp_consent_filename' + abs_id).val();
                if (prevFile != '') {
                    deleteTempFile(prevFile);
                }
                $('#temp_consent_filename' + abs_id).val('');
                $('#original_consent_file_name' + abs_id).val('');
                $('#concentFileNameUploaded' + abs_id).text('');

                toastr.error('Please select a file to upload.', 'Error', {
                    "progressBar": true,
                    "timeOut": 4000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });
                flag = 1;
            }
            console.log(flag);
            if (flag == 0) {
                var sessionId = $('#sessionId' + abs_id).val();
                var d = new Date();
                var uploadTime = d.getTime();
                var prevFile = $('#temp_consent_filename' + abs_id).val();
                // createDynamicFileName
                var dynamicFileName = 'CONSENT_' + sessionId + uploadTime + '_' + file['name'];
                // alert(dynamicFileName);
                $('#temp_consent_filename' + abs_id).val(dynamicFileName);
                $('#original_consent_file_name' + abs_id).val(file['name']);
                $('#concentFileNameUploaded' + abs_id).text(file['name']);
                uploadFile(file, 'CONSENT_' + sessionId + uploadTime, prevFile); // Upload the file

                toastr.success('File uploaded successfully.', 'Success', {
                    "progressBar": true,
                    "timeOut": 2000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });
            }
        });


    }); //

    function abstractTypeChange(obj, abs_id, cat_id, selected_topic_id, fieldArr, nomination_ids) {
        // alert(abs_id)
        // if (cat_id == 1) {  // FOR HOPECON
        //     $('#faculty_upload_' + abs_id).show();
        //     $('#formFileAbstract' + abs_id).removeClass('hideitem');
        // } else {
        //     $('#faculty_upload_' + abs_id).hide();
        //     $('#formFileAbstract' + abs_id).addClass('hideitem');

        // }
        if (nomination_ids) {
            $('#nomination_holder input[type="radio"]').prop('checked', false);
            for (var i = 0; i < nomination_ids.length; i += 2) {
                $('#accordianNomination').show();
                $('#accordianNomination').attr('ishidden', '1');
                // $('.wasCheck').prop('checked', false);
                $('.wasActive').hide();
                $('.catActive').hide();
                $('#nomination_holder_' + nomination_ids[i]).show();
                $('#nomination_holder_' + nomination_ids[i]).addClass('catActive');
                $('#nomination_name_' + nomination_ids[i]).addClass('wasCheck');
            }
        } else {
            $('#accordianNomination').hide();
            $('#accordianNomination').attr('ishidden', '0');

            $('.nomination_list').hide();
        }

        var submissionSubType = $(obj).val();
        var prev_cat_id = '<?= $Abs_cat_id ?>';
        var prev_cat_id = localStorage.getItem("catId" + abs_id);
        if (cat_id != prev_cat_id) {
            // alert(prev_cat_id);
            $('#collapseTwo2_' + abs_id + ' input[type="radio"]').prop('checked', false);
        }
        //alert(cat_id);
        fieldArr = fieldArr.split(",");

        $('.commn-absfields').hide();
        localStorage.setItem("subCat", submissionSubType);
        $('#abstract_description').addClass('hideitem');

        $('.hidesub_' + abs_id).hide();
        $('.hideFieldVal').hide();



        //alert(submissionSubType);
        // if (submissionSubType === "Free Paper") {

        //     $('.submissionTypeRadio' + cat_id).show();
        //     var submissionTypeContainer = $("li[use=leftAccordionSubmissionType]");
        //     $(submissionTypeContainer).find("input[type=radio]").prop("disabled", false);
        //     $(submissionTypeContainer).find("input[type=radio]").prop("checked", false);

        //     $('.abs-submission-type').show();
        //     $('.abs-sub-submission-type').show();


        //     $('#upload_abstract_file').parent().show();
        //     $('#upload_abstract_file').addClass('hideitem');


        // }

        if (submissionSubType === "Poster Presentation X") {
            $('#topicDetails').hide();
        } else {

            //alert(cat_id);
            $('#absCatTitle').val($(obj).attr('title'));
            $('.commn').addClass('hideitem');
            $('.submissionTypeRadio' + cat_id + '_' + abs_id).show();
            // $('#collapseTwo2_' + abs_id).addClass('show');
            // $('#wordCountList' + abs_id).show();
            var submissionTypeContainer = $("div[class=accordion-body]");
            $(submissionTypeContainer).find("input[type=radio]").prop("disabled", false);
            //$(submissionTypeContainer).find("input[type=radio]").prop("checked",false);

            $('.abs-submission-type').show();
            $('.abs-sub-submission-type').show();

            //disableAllFileds($("li[use=abstractPresenterDetails]"));

            $('#upload_abstract_file').parent().show();
            $('#upload_abstract_file').removeClass('hideitem');


            localStorage.setItem("subSType", '');
            localStorage.setItem("subType", '');

            $('#topicDetails').show();

            $('.field_div_' + abs_id).hide();
            for (var i = 0; i < fieldArr.length; i++) {
                //alert(fieldArr[i]);
                $('#field_div_' + fieldArr[i] + '_' + abs_id).show();
                $('#fieldVal_' + fieldArr[i] + "_" + abs_id).removeClass("hideitem");
            }
        }

        // if (submissionSubType !== '' && cat_id != prev_cat_id) {
        if (cat_id != '') {

            $.ajax({
                type: "POST",
                url: "abstract_request.process.php",
                data: {
                    action: 'generateTopic',
                    topic: cat_id
                },
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    if (JSONObject) {
                        if (JSONObject.trim() == 'empty') {
                            $('#topicDetails').hide();
                            //document.getElementById("abstract_topic_id").required = false;
                            $('#abstract_topic_id_' + abs_id).html("");
                            $('#abstract_topic_id_' + abs_id).attr('required', false);
                        } else {
                            $('#topicDetails').show();
                            $('#abstract_topic_id_' + abs_id).html(JSONObject);

                        }
                    }
                    $('#abstract_topic_id_' + abs_id).find('option[value="' + selected_topic_id + '"]').prop('selected', true);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '--' + textStatus + '--' + errorThrown);
                }
            });

            var submission_id = $('input[name="abstract_parent_type"]:checked').val();

            if (submission_id != '' && cat_id != '') {
                $.ajax({
                    type: "POST",
                    url: "abstract_request.process.php",
                    data: {
                        action: 'generateCatSubTopic',
                        cat_id: cat_id,
                        submission_id: submission_id
                    },
                    dataType: "html",
                    async: false,
                    success: function(JSONObject) {
                        if (JSONObject.trim() == 'empty') {
                            $('#topicDetails').hide();
                            //document.getElementById("abstract_topic_id").required = false;
                            $('#abstract_topic_id_' + abs_id).attr('required', false);
                        } else {
                            $('#topicDetails').show();
                            $('#abstract_topic_id_' + abs_id).html(JSONObject);
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                    }
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: "abstract_request.process.php",
                    data: {
                        action: 'checkSubCat',
                        cat_id: cat_id,
                        delegateId: '<?php echo $delegateId; ?>'
                    },
                    dataType: "html",
                    async: false,
                    success: function(JSONObject) {
                        if (JSONObject) {

                        }


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                    }
                });
            }


        }
    }

    function abstractSubmissionType(obj, cat_id, submission_id) {
        var submissionType = $(obj).val();
        // set data in localstorage
        localStorage.setItem("subType", submissionType);
        //alert('cat='+cat_id+'sub='+submission_id+'submissionType='+submissionType);
        //$('.hidesub').hide();
        //$('.hideFieldVal').hide();


        if (cat_id != '' && submission_id != '') {
            $('#abssubCatTitle').val($(obj).attr('title'));
            $.ajax({
                type: "POST",
                url: "abstract_request.process.php",
                data: {
                    action: 'generateCatSubTopic',
                    cat_id: cat_id,
                    submission_id: submission_id
                },
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    if (JSONObject.trim() == 'empty') {
                        $('#topicDetails').hide();
                        //document.getElementById("abstract_topic_id").required = false;
                        $('#abstract_topic_id').attr('required', false);
                    } else {
                        $('#topicDetails').show();
                        $('#abstract_topic_id').html(JSONObject)
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                }
            });
        }
    }

    function deleteTempFile(tempFile) {

        var formData = new FormData();
        formData.append('tempFile', tempFile);
        formData.append('delete', "1");

        $.ajax({
            url: 'abstract.user.entrypoint.process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log("RES= " + response);
            },
            error: function(xhr, status, error) {
                console.error("Error: ", error);
            }
        });
    }

    function uploadFile(file, dynamic_name_prefix, prevFile) {

        var formData = new FormData();
        formData.append('file', file);
        formData.append('dynamic_name_prefix', dynamic_name_prefix);
        formData.append('prevFile', prevFile);

        $.ajax({
            url: 'abstract.user.entrypoint.process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log("RES= " + response);
            },
            error: function(xhr, status, error) {
                console.error("Error: ", error);
            }
        });
    }

    $('.formFileAbstract').on('change', function() {
        var abs_id = $(this).attr('abs_id');
        // function abstract_file_upload(obj,id){
        var file = this.files[0];

        flag = 0;
        if (file) {
            var fileSize = file.size; // in bytes
            var fileType = file.type;

            var validExtensions = new Array();
            var abstract_file_types = $('#abstract_file_types' + abs_id).val();
            if (abstract_file_types.includes("pdf")) {
                validExtensions.push("pdf");
            }
            if (abstract_file_types.includes("image")) {
                validExtensions.push("jpg");
                validExtensions.push("jpeg");
                validExtensions.push("png");
            }
            if (abstract_file_types.includes("word")) {
                validExtensions.push("doc");
                validExtensions.push("docx");
            }
            var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
            var fileTypeErr = "Only " + jsonString + " files are allowed";
            // var validExtensions = ["doc", "docx", "pdf"]
            var fileName = file.name.split('.').pop();

            console.log(fileName);

            if (fileSize > 5 * 1024 * 1024) {

                var prevFile = $('#temp_abstract_filename' + abs_id).val();
                if (prevFile != '') {
                    deleteTempFile(prevFile);
                }
                $('#temp_abstract_filename' + abs_id).val('');
                $('#original_abstract_file_name' + abs_id).val('');
                $('#abstractFileNameUploaded' + abs_id).text('');

                toastr.error('File size exceeds 5MB limit.', 'Error', {
                    "progressBar": true,
                    "timeOut": 5000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });
                this.value = ''; // Clear the file input
                flag = 1;
                return;
            }

            if (validExtensions.indexOf(fileName) == -1) {
                var prevFile = $('#temp_abstract_filename' + abs_id).val();
                if (prevFile != '') {
                    deleteTempFile(prevFile);
                }
                $('#temp_abstract_filename' + abs_id).val('');
                $('#original_abstract_file_name' + abs_id).val('');
                $('#abstractFileNameUploaded' + abs_id).text('');
                // alert(fileTypeErr)
                toastr.error(fileTypeErr, 'Error', {
                    "progressBar": true,
                    "timeOut": 5000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });


                this.value = '';
                flag = 1;
                return;
            }
            // File is valid
            $('#fileError').text('');
        } else {
            var prevFile = $('#temp_abstract_filename' + abs_id).val();
            if (prevFile != '') {
                deleteTempFile(prevFile);
            }
            $('#temp_abstract_filename' + abs_id).val('');
            $('#original_abstract_file_name' + abs_id).val('');
            $('#abstractFileNameUploaded' + abs_id).text('');
            // toastr.error('Please select a file to upload.', 'Error', {
            //     "progressBar": true,
            //     "timeOut": 4000,
            //     "showMethod": "slideDown",
            //     "hideMethod": "slideUp"
            // });

            flag = 1;
        }
        if (flag == 0) {
            var sessionId = $('#sessionId' + abs_id).val();
            var d = new Date();
            var uploadTime = d.getTime();
            var prevFile = $('#temp_abstract_filename' + abs_id).val();
            // createDynamicFileName
            var dynamicFileName = 'ABSTRACT_' + sessionId + uploadTime + '_' + file['name'];
            // alert(dynamicFileName);
            $('#temp_abstract_filename' + abs_id).val(dynamicFileName);
            $('#original_abstract_file_name' + abs_id).val(file['name']);
            $('#abstractFileNameUploaded' + abs_id).text(file['name']);
            uploadFile(file, 'ABSTRACT_' + sessionId + uploadTime, prevFile); // Upload the file
            toastr.success('File uploaded successfully.', 'Success', {
                "progressBar": true,
                "timeOut": 2000,
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
            });

        }
    });


    function abstractAddTypeChange(obj, cat_id, fieldArr, nomination_ids) {
        // if (cat_id == 1) {
        //     $('#faculty_upload').show();
        //     $('#formFileAbstract').removeClass('hideitem');
        // } else {
        //     $('#faculty_upload').hide();
        //     $('#formFileAbstract').addClass('hideitem');

        // }
        if (nomination_ids) {
            for (var i = 0; i < nomination_ids.length; i += 2) {
                $('#accordianNomination').show();
                $('#accordianNomination').attr('ishidden', '1');
                $('.wasActive').hide();
                $('.catActive').hide();
                $('#nomination_holder_' + nomination_ids[i]).show();
                $('#nomination_holder_' + nomination_ids[i]).addClass('catActive');
            }
        } else {
            $('#accordianNomination').hide();
            $('#accordianNomination').attr('ishidden', '0');

            $('.nomination_list').hide();
        }

        var submissionSubType = $(obj).val();

        //alert($(obj).attr('title'));
        fieldArr = fieldArr.split(",");

        $('.commn-absfields').hide();
        localStorage.setItem("subCat", submissionSubType);
        $('#abstract_description').addClass('hideitem');

        $('.hidesub').hide();
        $('.hideFieldVal').hide();

        //alert(submissionSubType);
        // if (submissionSubType === "Free Paper") {

        // 	$('.submissionTypeRadio' + cat_id).show();
        // 	var submissionTypeContainer = $("li[use=leftAccordionSubmissionType]");
        // 	$(submissionTypeContainer).find("input[type=radio]").prop("disabled", false);
        // 	$(submissionTypeContainer).find("input[type=radio]").prop("checked", false);

        // 	$('.abs-submission-type').show();
        // 	$('.abs-sub-submission-type').show();


        // 	$('#upload_abstract_file').parent().show();
        // 	$('#upload_abstract_file').addClass('hideitem')

        // }

        if (submissionSubType === "Poster Presentation1") {
            $('#topicDetails').hide();
        } else {

            //alert(cat_id);
            $('#absCatTitle').val($(obj).attr('title'));
            $('.commn').addClass('hideitem');
            $('.submissionTypeRadio' + cat_id).show();
            // $('#collapseTwo2').addClass('show');
            // var submissionTypeContainer = $("div[class=accordion-body]");
            // $(submissionTypeContainer).find("input[type=radio]").prop("disabled", false);
            var submissionTypeContainer = $("div[id=collapseTwo2]");
            $(submissionTypeContainer).find("input[type=radio]").prop("checked", false);
            //$(submissionTypeContainer).find("input[type=radio]").prop("checked",false);

            $('.abs-submission-type').show();
            $('.abs-sub-submission-type').show();

            //disableAllFileds($("li[use=abstractPresenterDetails]"));

            // $('#upload_abstract_file').parent().show();
            // $('#upload_abstract_file').removeClass('hideitem')


            localStorage.setItem("subSType", '');
            localStorage.setItem("subType", '');

            $('#topicDetails').show();

            $('.field_div').hide();
            for (var i = 0; i < fieldArr.length; i++) {
                //alert(fieldArr[i]);
                $('#field_div_' + fieldArr[i]).show();
                $('#fieldVal_' + fieldArr[i]).removeClass("hideitem");
            }
        }

        if (submissionSubType !== '') {


            $.ajax({
                type: "POST",
                url: "abstract_request.process.php",
                data: {
                    action: 'generateTopic',
                    topic: cat_id
                },
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    if (JSONObject) {
                        if (JSONObject.trim() == 'empty') {
                            $('#topicDetails').hide();
                            //document.getElementById("abstract_topic_id").required = false;
                            $('#abstract_topic_id').html("")
                            $('#abstract_topic_id').attr('required', false);
                        } else {
                            $('#topicDetails').show();
                            $('#abstract_topic_id').html(JSONObject)
                        }
                    }


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                }
            });



            $.ajax({
                type: "POST",
                url: "abstract_request.process.php",
                data: {
                    action: 'checkSubCat',
                    cat_id: cat_id,
                    delegateId: '<?php echo $delegateId; ?>'
                },
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    if (JSONObject) {

                    }


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
                }
            });
        }
    }
</script>


<div class="registration_view_modal">
    <div class="abstract_view_inner">
        <div class="abstract_view_box">
            <div class="abstract_head">
                <div class="abs_modal-prf">
                    <h3 class="non-edit-sub-heading">Registration Id: <?= $rowUserDetails['user_registration_id'] ?></h3>
                </div>
                <ul class="abs_mod_head mb-0">
                    <a class="m-0 abs-modal-close"><i class="fa-solid fa-xmark"></i></a>
                </ul>
            </div>
            <div class="abstract_body">
                <div class="abstract_details">
                    <!-- ********************************************* REGISTRATION DETAILS BOX MODAL ********************************************** -->
                    <ul class="abstract-list-box">
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

                        <!-- ====================================== CONFERENCE ================================== -->
                        <li class="abstract-list-content" style="padding-right: 0;">
                            <h4 class="abs-modal-submission">Conference</h4>
                            <ul class="con_attch">
                                <?php if ($rowaMailClassification['inclusion_sci_hall'] == 'Y') { ?>
                                    <li><?= $result[0]['title'] ?></li>
                                <?php }
                                if ($rowaMailClassification['inclusion_exb_area'] == 'Y') { ?>
                                    <li><?= $result[1]['title'] ?></li>
                                <?php }
                                if ($rowaMailClassification['inclusion_tea_coffee'] == 'Y') { ?>
                                    <li><?= $result[3]['title'] ?></li>
                                <?php }
                                if ($rowaMailClassification['inclusion_conference_kit'] == 'Y') { ?>
                                    <li><?= $result[2]['title'] ?></li>
                                <?php }
                                if (!empty($selected_inclusion_lunch_date)) { ?>
                                    <li><?= 'Lunch on ' . $lunch  ?></li>
                                <?php }
                                if (!empty($selected_inclusion_dinner_date)) { ?>
                                    <li><?= 'Dinner on ' . $dinner  ?></li>
                                <?php } ?>
                            </ul>
                        </li>


                        <!-- ====================================== WORKSHOP ================================== -->
                        <?php
                        $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                        if (count($workshopDetailsArray) > 0) {
                        ?>
                            <li class="abstract-list-content" style="padding-right: 0;">
                                <h4 class="abs-modal-submission">Workshop</h4>

                                <?php
                                if ($workshopDetails) {
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
                                                    <h4><?= $rowWorkshopDetails['REG_DETAIL'] ?> <?= $workshop_date ?></h4>
                                                    <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $rowWorkshopDetails['INVOICE']['id'] ?>" target="blank" class="btn" style="background:none">INVOICE</a>
                                                    <?php

                                                    if ($rowWorkshopDetails['INVOICE']['payment_status'] == 'UNPAID' && $rowWorkshopDetails['INVOICE']['invoice_mode'] == 'ONLINE') {
                                                    ?>


                                                        <a onclick="onlinePayNow('<?= $rowWorkshopDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" class="btn" style=" border-radius: 20px;  margin-right: 10px;  display: inline-block; padding: 5px 15px;">PAY NOW</a>

                                        <?php

                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {

                                    $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                                    //echo count($workshopDetailsArray);
                                    if (count($workshopDetailsArray) > 0) {
                                        ?>
                                        <br><a href="<?= _BASE_URL_ . "profile-add.php?section=3" ?>" <?= $disabled ?> class="btn">Add Workshop</a>
                                <?php
                                    }
                                }
                                ?>
                            </li>
                        <?php
                        } ?>

                        <!-- ====================================== ACCOMPANY ================================== -->
                        <?php
                        if ($accompany_tariff) {
                        ?>
                            <li class="abstract-list-content" style="padding-right: 0;">
                                <h4 class="abs-modal-submission">Accompany</h4><br>
                                <?php
                                if (sizeof($accompanyDtlsArr) > 0) {
                                ?>
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

                                        <span><?= strtoupper($accompanyDtls['user_full_name']) ?><span> &nbsp;- &nbsp;
                                                <a class="btn" href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $accompanInvoiceDtls['id'] ?>" target="_blank" style="border-radius: 20px; padding: 5px 15px; margin-right: 10px;  display: inline-block; ">INVOICE</a>
                                                <?php
                                                if ($accompanInvoiceDtls['payment_status'] == 'UNPAID' && $accompanInvoiceDtls['invoice_mode'] == 'ONLINE') {
                                                ?>
                                                    <a onclick="onlinePayNow('<?= $accompanInvoiceDtls['slip_id'] ?>', '<?= $delegateId ?>')" class="btn" style=" border-radius: 20px;  margin-right: 10px;  display: inline-block; padding: 5px 15px;">PAY NOW</a>

                                                <?php
                                                }
                                                ?>
                                                <?php

                                                if (!empty($dinnerDetails)) {


                                                    $dinrInvAmtDisp = 'Included in Package';
                                                    $hasInvoice     = false;
                                                    if ($dinnerDetails['INVOICE']['service_type'] == 'DELEGATE_DINNER_REQUEST') {
                                                        $dinrInvAmtDisp = $dinnerDetails['INVOICE']['currency'] . ' ' . $dinnerDetails['INVOICE']['service_roundoff_price'];
                                                        $hasInvoice     = true;

                                                ?>
                                                        <br><br><span>BANQUET- </span>
                                                        <?
                                                        if ($hasInvoice) {
                                                        ?>
                                                            <a class="btn" href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $dinnerDetails['INVOICE']['id'] ?>" target="blank" style="border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: white ">INVOICE</a>
                                                            <!--<a>CANCEL</a>-->
                                                        <?
                                                        }
                                                        if ($dinnerDetails['INVOICE']['payment_status'] == 'UNPAID' && $dinnerDetails['INVOICE']['invoice_mode'] == 'ONLINE') {

                                                        ?>
                                                            <!-- <a onclick="onlinePayNow('<?= $dinnerDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" style=" border-radius: 20px;  margin-right: 10px; display: inline-block; margin-top: 6px;padding: 7px;cursor: pointer;">PAY NOW</a> -->
                                                        <?php
                                                        }
                                                        ?>
                                                <?php
                                                    }
                                                }


                                                ?>
                                            <?php

                                        }

                                            ?> <br>
                                        <?php
                                    }
                                        ?>
                                        <br>


                                        <a href="<?= _BASE_URL_ . "profile-add.php?section=4"; ?>" <?= $disabled ?> class="btn">Add Accompaning</a>


                            </li>
                        <?php
                        } ?>
                        <!-- ====================================== BANQUET ================================== -->
                        <?php
                        $dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
                        if ($dinnerTariffArray) {
                        ?>
                            <li class="abstract-list-content" style="padding-right: 0;">
                                <h4 class="abs-modal-submission">Banquet</h4><br>
                                <?php
                                if (sizeof($dinnerDtls) > 0) {
                                    $dinrCount = 0;
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
                                        <span>BANQUET-DINNER <?= $dinnerDetails['USER']['user_full_name'] ?> </span>
                                        <?
                                        if ($hasInvoice) {
                                        ?>
                                            <a class="btn" href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $dinnerDetails['INVOICE']['id'] ?>" target="_blank" style=" border-radius: 20px; padding: 5px 15px; margin-right: 10px; color white ">INVOICE</a>
                                        <?
                                        }
                                        if ($dinnerDetails['INVOICE']['payment_status'] == 'UNPAID' && $dinnerDetails['INVOICE']['invoice_mode'] == 'ONLINE') {
                                        ?>

                                            <a class="btn" onclick="onlinePayNow('<?= $dinnerDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" style=" cursor: pointer;">PAY NOW</a>
                                        <?php
                                        }
                                        ?>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <a href="<?= _BASE_URL_ . "profile-add.php?section=5"; ?>" <?= $disabled ?> class="btn">Add Banquet</a>
                                    <!-- <a href="#" class="btn">Invoice</a> -->
                                <?php
                                }
                                ?>

                            </li>
                        <?php }
                        // ========================================  ACCOMMODATION ========================================
                        if ($countAcc) { ?>
                            <li class="abstract-list-content" style="padding-right: 0;">
                                <h4 class="abs-modal-submission">Accommodation</h4>
                                <?php
                                if (isset($invoiceList[$delegateId]['ACCOMMODATION'])) {
                                    //echo '<pre>'; print_r($invoiceList[$delegateId]['ACCOMMODATION']['ROW_DETAIL']);

                                    global $mycms, $cfg;

                                    $sqlGetAccommodation   = array();
                                    $sqlGetAccommodation['QUERY']  = "SELECT req.id,req.refference_invoice_id,req.user_id,req.accompany_name,req.accommodation_details,req.hotel_id,req.package_id,req.checkin_date,req.checkout_date,req.booking_quantity,R.room_id, req.rooms_no FROM " . _DB_REQUEST_ACCOMMODATION_ . " req INNER JOIN  " . _DB_MASTER_ROOM_ . " R ON req.id = R.request_accommodation_id WHERE req.`user_id` = '" . trim($delegateId) . "' AND req.`status`='A' AND R.`status`='A' ORDER BY R.room_id ASC, req.created_dateTime ASC";

                                    $resultGetAccommodation = $mycms->sql_select($sqlGetAccommodation);
                                    $roomsArr = array();
                                    $countAccoDetils = 0;
                                    $countAccoDetilsInnr = '';
                                    $countAccoDetils += count($roomsArr);
                                    foreach ($resultGetAccommodation as $key => $value) {
                                        $roomsArr[$value['room_id']][] = $value;

                                        $countAccoDetilsInnr = count($value);
                                    }

                                    //echo '<pre>'; print_r($roomsArr); 
                                    //echo $countAccoDetilsInnr;
                                    //echo $countAccoDetils + $countAccoDetilsInnr;

                                    $accommodation_room = '';
                                    $count = 0;

                                    $hotel_id = '';
                                    $distinctArray = [];
                                    foreach ($roomsArr as $key => $accommodationRow) {
                                ?>

                                        <!-- <span>Room <?= $key ?></span> -->

                                        <?php

                                        foreach ($accommodationRow as $key => $value) {
                                            //print_r($value);
                                            $getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);

                                            if (!empty($value['rooms_no'])) {
                                                $accommodations_room = $value['rooms_no'];
                                            } else {
                                                $accommodations_room = 1;
                                            }

                                            $hotel_id =  $value['hotel_id'];
                                            $hotel_name = getHotelNameByID($hotel_id);

                                            if (!in_array($accommodations_room, $distinctArray)) {
                                                $distinctArray[] = $accommodations_room;
                                            }

                                            $getPackageNameById =  getPackageNameById($value['package_id']);
                                            $total_stays = getDaysBetweenDates($value['checkin_date'], $value['checkout_date']);
                                            $total_stay = $getPackageNameById . " for " . $total_stays;

                                        ?>
                                            <br><span style="font-style: italic;font-size: 15px;"><?= (!empty($total_stay) ? str_replace("+", "", $total_stay) . ' at ' : '') . $hotel_name ?></span>
                                            <br>
                                            <h5 style="font-size: 15px;">
                                                <span><sup></sup>Check-In </span>
                                                <span>Date: <?= $mycms->cDate('d/m/Y', $value['checkin_date']) ?></span>

                                                &nbsp;|&nbsp;
                                                <span><sup></sup>Check-Out </span>
                                                <span>Date: <?= $mycms->cDate('d/m/Y', $value['checkout_date']) ?></span>

                                            </h5><br>
                                            <a class="btn" href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $value['refference_invoice_id'] ?>" target="_blank" style=" border-radius: 20px; padding: 5px 15px; margin-right: 10px; display: inline-block; ">INVOICE</a>
                                    <?php

                                        }
                                    }



                                    ?>
                                    <?php

                                    foreach ($distinctArray as $key => $value) {
                                        //echo $value;
                                        //echo $hotel_id;
                                        $minCheckInDateByHotelID = minCheckInDateByHotelID($hotel_id);
                                        $maxCheckInDateByHotelID = maxCheckOutDateByHotelID($hotel_id);
                                        $getNightValidate = getNightValidate($value, $minCheckInDateByHotelID, $maxCheckInDateByHotelID, $delegateId, $count);
                                    }
                                    if ($getNightValidate < 3) {
                                    ?>
                                        <!-- <a class="btn smallhed addCategory" style="text-align: center;border-radius: 20px; padding: 5px 15px;  color: #005e82; cursor:pointer; white-space: nowrap;font-size: 14px;margin-bottom: 7px;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationMoreNight(6,'<?= $hotel_id ?>')">ADD MORE NIGHTS</a> -->
                                    <?php
                                    }
                                    //echo $accommodation_room;
                                    if ($getAccommodationMaxRoom < 3) {
                                    ?>


                                        <!-- <a class="btn smallhed addCategory" style="text-align: center;border-radius: 20px; padding: 5px 15px;  color: #005e82; cursor:pointer; white-space: nowrap;font-size: 14px;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationMoreNight(6,'<?= $hotel_id ?>')">ADD MORE ROOMS</a> -->

                                    <?php
                                    }
                                    ?>
                                <?php
                                } else {
                                ?>
                                    <a href="<?= _BASE_URL_ . "profile-add.php?section=6"; ?>" <?= $disabled ?> class="btn">Add Accomodation</a>
                                <?php
                                }
                                ?>
                            </li>

                        <?php } ?>


                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="cancel_view_modal">
    <div class="cancel_view_inner">
        <div class="cancel_view_box">
            <a class="cancel-close" onclick="$('.cancel_view_modal').hide();"><i class="fa-solid fa-xmark"></i></a>
            <div class="cancel-lt">
                <div class="abstract_head">
                    <h3>Request Cancellation</h3>
                </div>
                <div class="abstract_body">
                    <form class="cancel-frm">
                        <h5 class="cancl-modal-heading">Your Services</h5>
                        <div class="checkbox-wrap" style="height: 145px;flex-wrap: wrap;justify-content: unset;">
                            <a href="javascript:void(0)" onclick="getCancelationModal('DELEGATE_CONFERENCE_REGISTRATION')"><label class="custom-radio"><input type="radio" name="service_type" inv_name="DELEGATE_CONFERENCE_REGISTRATION" value="DR"><span class="checkbox-text">Registration</span><span class="checkmark"></span></label></a>
                            <?php
                            if (count($workshopDetailsArray) > 0) {
                            ?>
                                <a href="javascript:void(0)" onclick="getCancelationModal('DELEGATE_WORKSHOP_REGISTRATION')"><label class="custom-radio"><input type="radio" name="service_type" inv_name="DELEGATE_CONFERENCE_REGISTRATION" value="PROF"><span class="checkbox-text">Workshop</span><span class="checkmark"></span></label></a>
                            <?php }
                            $registrationAccompanyAmount  = getCutoffTariffAmnt($currentCutoffId);
                            if (!empty($registrationAccompanyAmount) && $registrationAccompanyAmount > 0) { ?>
                                <a href="javascript:void(0)" onclick="getCancelationModal('ACCOMPANY_CONFERENCE_REGISTRATION')"><label class="custom-radio"><input type="radio" name="service_type" value="MR"><span class="checkbox-text">Accompany</span><span class="checkmark"></span></label></a>
                            <?php }

                            if ($countAcc) { ?>
                                <a href="javascript:void(0)" onclick="getCancelationModal('DELEGATE_ACCOMMODATION_REQUEST')"><label class="custom-radio"><input type="radio" name="service_type" value="MR"><span class="checkbox-text">Accommodation</span><span class="checkmark"></span></label></a>
                            <?php
                            }
                            if (sizeof($dinnerDtls) > 0) {
                            ?>
                                <a href="javascript:void(0)" onclick="getCancelationModal('DELEGATE_DINNER_REQUEST')"><label class="custom-radio"><input type="radio" name="service_type" value="MR"><span class="checkbox-text">Dinner</span><span class="checkmark"></span></label></a>
                            <?php
                            }
                            ?>
                        </div>
                        <div id="invoice_cancel_body" class="disable-blur">
                            <h5 class="cancl-modal-heading">Reason for cancellation</h5>
                            <textarea name="cancelation_cause" id="cancelation_cause"></textarea>
                            <button class="default-btn next-step">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="cancel-rt">
                <h5 class="cancl-modal-heading">Conclution</h5>
                <div style="font-size: 14px;">
                    <ul>
                        <li>No verbal cancellation requests will be entertained. Cancellation request should be mailed to <a href="<?= $cfg['EMAIL_CONF_EMAIL_US'] ?>"><?= $cfg['EMAIL_CONF_EMAIL_US'] ?></a>.</li>
                        <li>Cancellation will be effective with the acceptance acknowledgment sent from the <strong><?= $cfg['APP_NAME'] ?></strong> Secretariat.</li>
                        <li>Refund will be initiated as per the following, after 30 days of completion of the conference:</li>
                    </ul>
                    <table>
                        <tbody>
                            <tr>
                                <td style="width: 53.9085%;">Date</td>
                                <td style="width: 39.6253%;">Deduction</td>
                            </tr>
                            <tr>
                                <td style="width: 53.9085%;">Cancellation by September 30, 2024</td>
                                <td style="width: 39.6253%;">50% of the Paid Amount</td>
                            </tr>
                            <tr>
                                <td style="width: 53.9085%;">Cancellation after September 30, 2024</td>
                                <td style="width: 39.6253%;">No Refund</td>
                            </tr>
                        </tbody>
                    </table>
                    <ul>
                        <li>No refund will be applicable on internet handling charges, taxes etc.</li>
                        <li>With the cancellation of Conference Registration, all the paid and complimentary services/facilities/entitlements will be cancelled.</li>
                        <li>Mode of Refund Remittance will be by Cheque only.</li>
                        <li>No refund will be applicable in case of eventual No Show.</li>
                        <li>In case of overpayment or double payment, the amount will be refunded to the account of the individual.</li>
                        <li>Neither <strong><?= $cfg['APP_NAME'] ?></strong> committee nor any of its associates will be held responsible if the conference is cancelled or postponed natural calamities, political insurgence, etc. In case of cancellation or postponement of the conference, refund policies would be decided then and delegates will be informed accordingly.</li>
                        <li>No refund will be granted for early termination of attendance, cancellation of speakers or any other incident which are beyond the control of the organisers.</li>
                    </ul>
                    <p>The policy may be changed if situation demands. Please follow <a href="<?= $cfg['INVOICE_WEBSITE'] ?>"><?= $cfg['INVOICE_WEBSITE'] ?></a>&nbsp;for time to time to keep yourself updated.</p>
                </div>
                <h6><i class="fa-solid fa-phone mr-2"></i> 9874563210</h6>
            </div>

        </div>
    </div>
</div>



<div id="paymentVoucherModal" class="checkout-main-wrap"
    style="    position: fixed;width: 100%;height: 100vh;top: 0;left: 0;background: rgba(0, 0, 0, 0.52);z-index: 99;">
    <div class="checkout-main-wrap-inner"
        style="width: 100%;height: 100%;display: flex;align-items: center;justify-content: center;">
        <div class="checkout-main-wrap-box">
            <div class="left-wrap">
                <?php
                $sql_qr = array();
                $sql_qr['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                                         WHERE `id`!='' AND `title`IN ('QR Code','Online Payment Logo')";
                $result = $mycms->sql_select($sql_qr);
                $onlinePaymentLogo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
                $QR_code = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['image'];

                ?>
                <div class="card-info">
                    <h4 class="block-head">Accepted Cards</h4>
                    <p>
                        <img src="<?= $onlinePaymentLogo ?>" style="width: 100%;">
                        <!-- <img src=""> -->
                    </p>
                </div>
                <div class="bank-info">
                    <h4 class="block-head">Bank Details</h4>
                    <p>Bank Name
                        <br><b><?= $cfg['INVOICE_BANKNAME'] ?></b>
                    </p>
                    <p>Account Number
                        <br><b><?= $cfg['INVOICE_BANKACNO'] ?></b>
                    </p>
                    <p>Benefeciary Name
                        <br><b><?= $cfg['INVOICE_BENEFECIARY'] ?></b>
                    </p>
                    <p>IFSC Code
                        <br><b><?= $cfg['INVOICE_BANKIFSC'] ?></b>
                    </p>
                    <p>Branch
                        <br><b><?= $cfg['INVOICE_BANKBRANCH'] ?></b>
                    </p>
                </div>


                <div class="bank-info">
                    <h4 class="block-head">Helpline No.</h4>
                    <p><i class="mr-2"></i><?= $cfg['CART_HELPLINE'] ?></p>
                </div>
            </div>
            <!-- ============================ PAYMENT OPTIONS DIV ============================-->
            <div class="payment-wrap" id="paymentOptions" style="height: 100%;">
                <form name="frmApplyPayment" id="frmApplyPayment" action="registration.process.php" method="post">
                    <div class="paymnet-box" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
                        <input type="hidden" name="act" value="paymentSet" />
                        <input type="hidden" id="slip_id" name="slip_id" />
                        <input type="hidden" id="delegate_id" name="delegate_id" value="<?= $$delegateId ?>" />
                        <input type="hidden" name="mode" id="mode" />
                        <input type="radio" name="card_mode" use="card_mode_select" value="International" checked style="visibility: hidden;">
                    </div>
                    <div class="summery" id="orderSummerySection">
                        <h4 class="block-head">Order Summery</h4>
                        <!-- <ul use="totalAmountTable"> -->
                        <div class="cart-data-row " use="totalAmount" id="paymentVoucherBody">


                            <!-- </ul> -->
                        </div>

                        <input type="submit" class="payment-button" id="pay-button-vouchar" value="Pay Now1">&nbsp;
                        <a class="close-check close-button" style="cursor: pointer;"><b>Close</b></a>
                    </div>
                </form>
            </div>

            <!-- <div class="total-bill">
                    <a class="close-check" style="cursor: pointer;"><span>&#10005;</span></a>

                    <div class="bill-info-text">
                        <?= $cfg['cheque_info'] ?>
                    </div>
                    <div class="total-bill-amount" use="totalAmount">
                        <h5>Total Payable Amount</h5>
                        <h3 use="totalAmount">₹ 0.00</h3>
                    </div>
                </div> -->

            <!-- ============================ ORDER SUMMERY =================================== -->
            <!-- <div class="summery" id="orderSummerySection">
                    <h4 class="block-head">Order Summery</h4>
                    <ul use="totalAmountTable">
                        <div class="cart-data-row add-inclusion" use="totalAmount" id="paymentVoucherBody">


                    </ul>
                </div> -->
        </div>
        <div class="qr-large">
            <img src="<?= $QR_code ?>">
            <button>
                X
            </button>
        </div>
    </div>
</div>
<!-- ================================================================= -->
<script src='https://unpkg.com/gsap@3/dist/gsap.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.5/js/swiper.min.js'></script>
<script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/parvus@2.3.3/dist/js/parvus.min.js"></script>
<script>
    $(".abstract-wrap").slick({
        infinite: true,
        autoplay: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
        fade: true,
    });
    feather.replace();
    // ------------step-wizard-------------
    $(document).ready(function() {
        $('.abstract-edit').click(function(e) {
            $('.no-edit').show();
            $('.has-edit').hide();
            $('.abstract_view_modal').show();
            $('.tab-pane').removeClass('active');
            $('#step1').addClass('active');
            $('.abs_mod_tab li').removeClass('active');
            $('.abs_mod_tab li:first-child').addClass('active');
        });
        // $('.submit-edit').click(function(e) {
        //     $('.abstract_view_modal').hide().removeClass('abstract_add_modal');
        //     $('.non-edit-sub-heading').show();
        //     $('.edit-sub-heading').hide();
        //     $('.add-sub-heading').hide();
        // });
        $('.abs-modal-edit').click(function(e) {
            var id = $(this).attr('abs_id');
            // alert(id);
            $('.no-edit').hide();
            // $('.has-edit-'+id).show();
            $('#abstract_edit_form_' + id).show();
            $('.non-edit-sub-heading').hide();
            $('.edit-sub-heading').show();
            $('.tab-pane').removeClass('active');
            $('#step1_' + id).addClass('active');
            $('.abs_mod_tab li').removeClass('active');
            $('.abs_mod_tab li:first-child').addClass('active');
        });

        //============================================================ NEW ABSTRACT SUBMISSION ========================================================================
        $('.next-step-abstract').click(function() {
            var step = $(this).attr('step');
            var nextstep = Number(step) + 1;
            var flag = 0;
            if (step == 1) {
                $("div[id='abstract_author_details']  input[type='text'], div[id='abstract_author_details'] input[type='date'], div[id='abstract_author_details'] input[type='radio'], div[id='abstract_author_details'] select ").each(function() {

                    if ($(this).attr('type') === 'radio') {

                        if (!$("input[type='radio'][name='abstract_author_title']:checked").length) {

                            toastr.error('Please select the title', 'Error', {
                                "progressBar": true,
                                "timeOut": 5000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            flag = 1;
                            return false;
                        }
                    } else {
                        if (!$(this).hasClass('hideitem')) {
                            var textBoxValue = $(this).val();
                            if (textBoxValue === '') {
                                toastr.error($(this).attr('validate'), 'Error', {
                                    "progressBar": true,
                                    "timeOut": 5000,
                                    "showMethod": "slideDown",
                                    "hideMethod": "slideUp"
                                });
                                flag = 1;
                                return false;
                            }
                        }
                    }

                });
            }

            if (step == 3) {
                $("div[id='abs_category'] input[type='radio'], div[id='collapseTwo2'] input[type='radio']").each(function() {

                    if ($(this).attr('type') === 'radio') {
                        if ($(this).attr('name') == 'abstract_category') {
                            if (!$("div[id='abs_category'] input[name='abstract_category']:checked").length) {
                                toastr.error('Please select category', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown", // Animation method for showing
                                    "hideMethod": "slideUp" // Animation method for hiding
                                });
                                flag = 1;
                                return false;
                            }
                        }

                        var isCheckedFaculty = document.getElementById('flexCheckDefault1').checked;
                        if ($(this).attr('name') == 'abstract_parent_type' && !isCheckedFaculty) {
                            if (!$("div[id='collapseTwo2'] input[name='abstract_parent_type']:checked").length) {
                                toastr.error('Please select submission sub category', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown", // Animation method for showing
                                    "hideMethod": "slideUp" // Animation method for hiding
                                });
                                flag = 1;
                                return false;
                            }
                        }
                    }
                });
            }

            if (step == 4) {


                $("div[id='abstract_details'] input[type='radio'], div[id='abstract_details'] select, div[id='abstract_details'] textarea ").each(function(index) { // input[type='file'][id='formFileAbstract']
                    if ($(this).is('select')) {

                        if ($.trim($(this).val()) == '') {

                            var msg = $(this).attr('validate');
                            toastr.error(msg, 'Error', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });

                            flag = 1;
                            return false;

                        }
                    } else {

                        if (!$(this).hasClass('hideitem')) {
                            //alert(index);
                            if ($.trim($(this).val()) == '') {

                                var idVal = $(this).attr('id').split('_');

                                $('#field_div_' + idVal[1]).addClass('show');

                                var msg = $(this).attr('validate');
                                toastr.error(msg, 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000,
                                    "showMethod": "slideDown",
                                    "hideMethod": "slideUp"
                                });

                                flag = 1;
                                return false;

                            }
                        }

                    }


                });
                var totalWordEnteredTitle = $('[use="total_word_entered"]:first').text();
                var totalWordLimitTitle = $('[use="total_word_limit"]:first').text();
                var totalWordEnteredabstract = $('[use="total_word_entered"]:eq(1)').text();
                var totalWordLimitAbstract = $('[use="total_word_limit"]:eq(1)').text();
                console.log(totalWordLimitTitle + " , " + totalWordLimitAbstract);
                console.log(totalWordEnteredTitle + " , " + totalWordEnteredabstract);
                if (Number(totalWordEnteredTitle) > Number(totalWordLimitTitle)) {
                    toastr.error('Title should be within ' + totalWordLimitTitle + ' words', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    flag = 1;
                    return false;

                }
                if (Number(totalWordEnteredabstract) > Number(totalWordLimitAbstract)) {
                    toastr.error('Abstract details should be within ' + totalWordLimitAbstract + ' words', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    flag = 1;
                    return false;

                }

                if (flag == 0) {
                    //alert('succ');

                    $("#abstractRequestForm")[0].submit(); // Directly calls the native form submit function

                    $('.abstract_view_modal').hide().removeClass('abstract_add_modal');
                    toastr.success('Abstract Updated Successfully!', 'Success', {
                        "progressBar": true,
                        "timeOut": 2000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    // window.location.reload();
                }
            }

            console.log("flag= " + flag);
            if (flag == 0) {
                var active = $('.new_abstract_tab.nav-tabs li.active');
                active.next().removeClass('disabled');
                nextTab(active);
                $('#step' + step).removeClass('active');
                $('#step' + nextstep).addClass('active');
            }

        });

        $(".prev-step-abstract").click(function(e) {
            var step = $(this).attr('step');
            var prevStep = Number(step) - 1;
            var active = $('.new_abstract_tab.nav-tabs li.active');
            prevTab(active);
            $('#step' + step).removeClass('active');
            $('#step' + prevStep).addClass('active');

        });

        $("#add-coauthor-btn").on("click", function(e) {
            e.preventDefault();
            addCoauthorAbstract();
        });

        function addCoauthorAbstract() {
            var accompanyCount = $('#coAuthorCounts').val();
            console.log("accompanyCount= " + accompanyCount);
            if (accompanyCount == 0) {
                $("#accordion-body-coauthor").show();

                $('#coAuthorCounts').val(1);
            } else {
                console.log("count= " + accompanyCount);
                // $("#coAuthor_first").show();
                var incrementedCount = Number(accompanyCount) + 1;
                $('#coAuthorCounts').val(incrementedCount);

                var accompanyCount = $('#coAuthorCounts').val();

                $("#accompanyCount").val(incrementedCount);
                // $('#coCount').text(incrementedCount);


                var newAccompany = $(".add_coathor:first").clone();
                $('#ca1').click();
                // $('.add_coathor').find('.accordion-button').addClass('collapsed');
                // $('.add_coathor').find('.accordion-collapse').attr('aria-expanded', false);
                $('.add_coathor').find('.accordion-collapse').removeClass('show');



                newAccompany.find('.accordion-button').attr('data-bs-target', '#collapseTwo' + accompanyCount);
                newAccompany.find('.accordion-button').removeClass('collapsed');
                newAccompany.find('.accordion-collapse').attr('id', 'collapseTwo' + accompanyCount);
                newAccompany.find('.accordion-collapse').addClass('show');

                newAccompany.find("span#coCount").text(accompanyCount);
                newAccompany.find("input[type='text']").val(''); // Clear the input field
                newAccompany.find("input[type='radio']").prop("checked", false);
                newAccompany.find("select").val(0);
                newAccompany.removeAttr('id');

                //$("#radioOption1").prop("checked", false);

                var fieldSerializeCount = Number(incrementedCount) - 1;

                //alert(fieldSerializeCount);


                newAccompany.find("input[id='abstract_coauthor_email']").attr("name", "abstract_coauthor_email[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_mobile']").attr("name", "abstract_coauthor_mobile[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_first_name']").attr("name", "abstract_coauthor_first_name[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_last_name']").attr("name", "abstract_coauthor_last_name[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_city']").attr("name", "abstract_coauthor_city[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_pincode']").attr("name", "abstract_coauthor_pincode[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_institute']").attr("name", "abstract_coauthor_institute[" + fieldSerializeCount + "]");
                newAccompany.find("input[id='abstract_coauthor_department']").attr("name", "abstract_coauthor_department[" + fieldSerializeCount + "]");

                newAccompany.find("select#abstract_coauthor_country").attr("name", "abstract_coauthor_country[" + fieldSerializeCount + "]");
                newAccompany.find("select#abstract_coauthor_state").attr("name", "abstract_coauthor_state[" + fieldSerializeCount + "]");


                newAccompany.find("input[type='text']").attr("countindex", fieldSerializeCount);
                newAccompany.find("input[type='radio']").attr("name", "abstract_coauthor_title[" + fieldSerializeCount + "]");

                newAccompany.find("input[type='radio'][name='abstract_coauthor_title[" + fieldSerializeCount + "]']").each(function(index, element) {

                    var inputType = $(element).attr("type");
                    var inputId = $(element).attr("id");
                    //alert(inputId)


                });
                $("#accordion-body-coauthor").append(newAccompany);
                // newAccompany.append('<button class="delete-coauthor-btn">Delete</button>');
                newAccompany.append('<button class="delete-coauthor-btn" count="' + accompanyCount + '">Delete</button>');
            }

        }



        //============================================================ ABSTRACT EDIT ========================================================================
        $('.next-step-edit').click(function() {
            var id = $(this).attr('abs_id');
            var step = $(this).attr('step');
            var nextstep = Number(step) + 1;
            var flag = 0;
            if (step == 1) {
                $("div[id='author_details_edit_" + id + "']  input[type='text'], div[id='author_details_edit" + id + "'] input[type='date'], div[id='author_details_edit" + id + "'] input[type='radio']").each(function() {

                    if ($(this).attr('type') === 'radio') {

                        if (!$("input[type='radio'][name='abstract_presenter_title']:checked").length) {

                            toastr.error('Please select the title', 'Error', {
                                "progressBar": true,
                                "timeOut": 5000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            flag = 1;
                            return false;
                        }
                    } else {

                        var textBoxValue = $(this).val();
                        if (textBoxValue === '') {
                            toastr.error($(this).attr('validate'), 'Error', {
                                "progressBar": true,
                                "timeOut": 5000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            flag = 1;
                            return false;
                        }
                    }

                });
            }

            if (step == 3) {
                $("div[id='abs_category_" + id + "'] input[type='radio'], div[id='collapseTwo2_" + id + "'] input[type='radio']").each(function() {

                    if ($(this).attr('type') === 'radio') {

                        //alert($(this).attr('name'));

                        if ($(this).attr('name') == 'abstract_category') {
                            if (!$("div[id='abs_category_" + id + "'] input[name='abstract_category']:checked").length) {

                                toastr.error('Please select category', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown", // Animation method for showing
                                    "hideMethod": "slideUp" // Animation method for hiding
                                });

                                flag = 1;
                                return false;

                            }
                        }
                        var elementId = 'flexCheckDefault1' + id + '_1';
                        var isCheckedFaculty = document.getElementById(elementId).checked;
                        if ($(this).attr('name') == 'abstract_parent_type' && !isCheckedFaculty) {

                            if (!$("div[id='collapseTwo2_" + id + "'] input[name='abstract_parent_type']:checked").length) {
                                toastr.error('Please select submission sub category', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown", // Animation method for showing
                                    "hideMethod": "slideUp" // Animation method for hiding
                                });
                                flag = 1;
                                return false;
                            }
                        }
                    }
                });
            }

            if (step == 4) {
                var flag = 0;

                $("div[id='abstract_details_" + id + "'] input[type='radio'], div[id='abstract_details_" + id + "'] select, div[id='abstract_details_" + id + "'] textarea").each(function(index) {
                    if ($(this).is('select')) {

                        if ($.trim($(this).val()) == '') {

                            var msg = $(this).attr('validate');
                            toastr.error(msg, 'Error', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });

                            flag = 1;
                            return false;

                        }
                    } else {

                        if (!$(this).hasClass('hideitem')) {
                            //alert(index);
                            if ($.trim($(this).val()) == '') {

                                var idVal = $(this).attr('id').split('_');;

                                $('#field_div_' + idVal[1]).addClass('show');

                                var msg = $(this).attr('validate');
                                toastr.error(msg, 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000,
                                    "showMethod": "slideDown",
                                    "hideMethod": "slideUp"
                                });

                                flag = 1;
                                return false;

                            }
                        }
                    }


                });
                // $("#formFileAbstract" + id ).each(function(index) {
                //     var isFile = localStorage.getItem('absFile' + id);
                //     if ((!$("#formFileAbstract" + id).hasClass('hideitem')) && (isFile=='false')) {

                //         if ($.trim($(this).val()) == '') {
                //             var msg = $(this).attr('validate');
                //             toastr.error(msg, 'Error', {
                //                 "progressBar": true,
                //                 "timeOut": 3000,
                //                 "showMethod": "slideDown",
                //                 "hideMethod": "slideUp"
                //             });

                //             flag = 1;
                //             return false;

                //         }
                //     }

                // });
                var totalWordEnteredTitle = $('[use="total_word_entered"]:first').text();
                var totalWordLimitTitle = $('[use="total_word_limit"]:first').text();
                var totalWordEnteredabstract = $('[use="total_word_entered"]:eq(1)').text();
                var totalWordLimitAbstract = $('[use="total_word_limit"]:eq(1)').text();
                console.log(totalWordLimitTitle + " , " + totalWordLimitAbstract);
                console.log(totalWordEnteredTitle + " , " + totalWordEnteredabstract);
                if (Number(totalWordEnteredTitle) > Number(totalWordLimitTitle)) {
                    toastr.error('Title should be within ' + totalWordLimitTitle + ' words', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    flag = 1;
                    return false;

                }
                if (Number(totalWordEnteredabstract) > Number(totalWordLimitAbstract)) {
                    toastr.error('Abstract details should be within ' + totalWordLimitAbstract + ' words', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    flag = 1;
                    return false;

                }

                if (flag == 0) {
                    //alert('succ');

                    $("#abstractEditForm" + id)[0].submit(); // Directly calls the native form submit function

                    $('.abstract_view_modal').hide().removeClass('abstract_add_modal');
                    toastr.success('Abstract Updated Successfully!', 'Success', {
                        "progressBar": true,
                        "timeOut": 2000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                    });
                    // window.location.reload();
                }
            }

            console.log("flag= " + flag);
            if (flag == 0) {
                var active = $('.abstract_edit_tab_' + id + '.nav-tabs li.active');
                active.next().removeClass('disabled');
                nextTab(active);
                $('#step' + step + '_' + id).removeClass('active');
                $('#step' + nextstep + '_' + id).addClass('active');
            }

        });

        $(".prev-step-edit").click(function(e) {
            var id = $(this).attr('abs_id');
            var step = $(this).attr('step');
            var prevStep = Number(step) - 1;
            var active = $('.abstract_edit_tab_' + id + '.nav-tabs li.active');
            prevTab(active);
            $('#step' + step + '_' + id).removeClass('active');
            $('#step' + prevStep + '_' + id).addClass('active');

        });

        $(".add-coauthor-btn").on("click", function(e) {
            e.preventDefault();
            var id = $(this).attr('abs_id');
            addCoauthor(id);
        });

        function addCoauthor(id) {
            var coAuthorCounts = $('#coAuthorCounts' + id).val();
            // alert(coAuthorCounts);
            if (coAuthorCounts == 0) { // X
                $('#accordion-body-coauthor-' + id).show();
                $('#coAuthorCounts' + id).val(1);
                return;
            }
            var existingCoAuthor = $('#existingCoAuthorNum' + id).val();
            // alert(existingCoAuthor);
            if (isNaN(existingCoAuthor)) {
                var accompanyCount = $('#coAuthorCounts' + id).val();

                if (isNaN(Number(accompanyCount))) {
                    accompanyCount = 0;
                }
                console.log("acc= " + accompanyCount);
                var incrementedCount = Number(accompanyCount) + 1;
                console.log("inc== " + incrementedCount);
                //alert("if: " + incrementedCount);
            } else {
                // var accompanyCount = $('#coAuthorCounts').val(); 
                var incrementedCount = Number(existingCoAuthor) + 1;
                console.log(incrementedCount);
                console.log("incElse== " + incrementedCount);
                $('#existingCoAuthorNum' + id).val(incrementedCount);
                //alert("else: " + incrementedCount);
            }
            console.log("incrementedCount== " + incrementedCount);

            $('#coAuthorCounts' + id).val(incrementedCount);
            if (incrementedCount == 1) {
                var accompanyCount = 1;
            } else {
                var accompanyCount = $('#coAuthorCounts' + id).val();
                console.log("coAuthorCounts== " + accompanyCount);
            }

            $("#accompanyCount" + id).val(incrementedCount);
            // $('#coCount').text(incrementedCount);

            // var newAccompany = $(".add_coathor:first").clone();
            var newAccompany = $("#newCoAuthor" + id).clone();
            var selector = 'span#coCount' + id;
            newAccompany.find(selector).html(accompanyCount);
            newAccompany.find("input[type='text']").val(""); // Clear the input field
            newAccompany.find("input[type='radio']").prop("checked", false);
            newAccompany.find("select").val('');

            //$("#radioOption1").prop("checked", false);

            var fieldSerializeCount = Number(incrementedCount) - 1;

            //alert(fieldSerializeCount);
            newAccompany.find("input[id='abstract_coauthor_email_" + id + "']").attr("name", "abstract_coauthor_email[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_mobile_" + id + "']").attr("name", "abstract_coauthor_mobile[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_first_name_" + id + "']").attr("name", "abstract_coauthor_first_name[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_last_name_" + id + "']").attr("name", "abstract_coauthor_last_name[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_city_" + id + "']").attr("name", "abstract_coauthor_city[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_pincode_" + id + "']").attr("name", "abstract_coauthor_pincode[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_institute_" + id + "']").attr("name", "abstract_coauthor_institute[" + fieldSerializeCount + "]");
            newAccompany.find("input[id='abstract_coauthor_department_" + id + "']").attr("name", "abstract_coauthor_department[" + fieldSerializeCount + "]");

            newAccompany.find("select#abstract_author_country_" + id).attr("name", "abstract_author_country[" + fieldSerializeCount + "]");
            newAccompany.find("select#abstract_author_state_" + id).attr("name", "abstract_author_state[" + fieldSerializeCount + "]");

            newAccompany.find("input[type='text']").attr("countindex", fieldSerializeCount);
            newAccompany.find("input[type='radio']").attr("name", "abstract_coauthor_title[" + fieldSerializeCount + "]");

            newAccompany.find("input[type='radio'][name='abstract_coauthor_title[" + fieldSerializeCount + "]']").each(function(index, element) {

                var inputType = $(element).attr("type");
                var inputId = $(element).attr("id");
                //alert(inputId)
            });

            $("#accordion-body-coauthor-" + id).append(newAccompany);

            newAccompany.append('<button class="delete-coauthor-btn" onclick="deleteCoauthor(' + id + ',event)" abs_id="' + id + '" count="' + accompanyCount + '">Delete</button>');

        }


        // =========================================================================================================================================
        $('.abs-modal-close').click(function(e) {
            $('.abstract_view_modal').hide().removeClass('abstract_add_modal');
            $('.non-edit-sub-heading').show();
            $('.edit-sub-heading').hide();
            $('.add-sub-heading').hide();
            $('.registration_view_modal').hide();
        });
        $('.skip-step').click(function(e) {
            $('.no-edit').show();
            $('.has-edit').hide();
            $('.non-edit-sub-heading').show();
            $('.edit-sub-heading').hide();
            $('.tab-pane').removeClass('active');
            $('#step1').addClass('active');
            $('.abs_mod_tab li').removeClass('active');
            $('.abs_mod_tab li:first-child').addClass('active');
        });
        $('.submit-abstract').click(function(e) {
            $('#abstract_add').show().addClass('abstract_add_modal');
            $('.non-edit-sub-heading').hide();
            $('.add-sub-heading').show();
            $('.tab-pane').removeClass('active');
            $('#step1').addClass('active');
            $('.abs_mod_tab li').removeClass('active');
            $('.abs_mod_tab li:first-child').addClass('active');
        });


        $('.nav-tabs > li a[title]').tooltip();

        //Wizard
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {

            var target = $(e.target);

            if (target.parent().hasClass('disabled')) {
                return false;
            }
        });

        $(".next-step").click(function(e) {

            // var active = $('.abs_mod_tab.nav-tabs li.active');
            // active.next().removeClass('disabled');
            // nextTab(active);

        });


        $(".prev-step").click(function(e) {
            var active = $('.abs_mod_tab.nav-tabs li.active');
            prevTab(active);
        });



        $(".coauthor-body").on("click", ".delete-coauthor-btn", function(e) {
            e.preventDefault();
            var id = $(this).attr('abs_id');
            var nextElement = $(this).parent().next();
            var accompanyCount = $('#coAuthorCounts' + id).val();
            if (accompanyCount == 1) {
                $(this).parent().hide();
            } else {
                $(this).parent().remove();
            }
            $('#coAuthorCounts' + id).val(Number(accompanyCount) - 1);
            console.log("coAthrCountdel= " + (Number(accompanyCount) - 1));

            $(this).find("span#coCount" + id).text(Number(accompanyCount) - 1);

            while (nextElement.length > 0) {
                var count = nextElement.find("span#coCount" + id).text();
                nextElement.find("span#coCount" + id).text(count - 1);
                nextElement = nextElement.next();
            }

            var accompanyAmount = $('#accompanyAmount' + id).val();

            var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(Number(accompanyCount) - 1);
            //$('#accompanyAmount').val(amountIncludedDay);

            $("#accompanyCount" + id).attr("amount", amountIncludedDay);
            $("#accompanyCount" + id).val(Number(accompanyCount) - 1);
        })

        $("#accordion-body-coauthor").on("click", ".delete-coauthor-btn", function(e) {
            e.preventDefault();

            var id = $(this).attr('id');
            var accompanyCount = $('#coAuthorCounts').val();
            console.log($(this).parent());

            if (accompanyCount == 1) {
                $(this).parent().hide();
                $(this).parent().find('input[type=text], input[type=radio], select').val('');
                $('#coAuthorCounts').val(0);
            } else if (id === 'delete_first') {

                var $nextElement = $(this).parent().next();
                console.log("yes");
                $('#coAuthor_first').hide();
                $('#delete_first').hide();
                $('#coAuthor_first').find('input[type=text], input[type=radio], select').val('');

                $('#coAuthorCounts').val(Number(accompanyCount) - 1);

                //to reset the serial number of next co-authors after deleting present
                while ($nextElement.length > 0) {
                    var count = $nextElement.find("span#coCount").text();
                    $nextElement.find("span#coCount").text(Number(count) - 1);
                    $nextElement = $nextElement.next();
                }

            } else {

                var $nextElement = $(this).parent().next();
                $(this).parent().remove();

                $('#coAuthorCounts').val(Number(accompanyCount) - 1);

                //to reset the serial number of next co-authors after deleting present
                while ($nextElement.length > 0) {
                    var count = $nextElement.find("span#coCount").text();
                    $nextElement.find("span#coCount").text(Number(count) - 1);
                    $nextElement = $nextElement.next();
                }

                var accompanyAmount = $('#accompanyAmount').val();

                var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(Number(accompanyCount) - 1);
                //$('#accompanyAmount').val(amountIncludedDay);

                $("#accompanyCount").attr("amount", amountIncludedDay);
                $("#accompanyCount").val(Number(accompanyCount) - 1);
            }



        });

        $("textarea[checkFor=wordCount]").keyup(function() {

            wordLimitCounter(this);
        });
        $("textarea[checkFor=wordCount]").hover(function() {
            wordLimitCounter(this);
        });

    }); // 

    function getCancelationModal(type) {

        var delegateId = '<?= $delegateId ?>';

        if (delegateId != '') {
            $('#invoice_cancel_body').html("");
            $.ajax({
                type: "POST",
                url: jsBASE_URL + 'login.process.php',
                data: 'action=getCancelationInvoice&delegateId=' + delegateId + '&type=' + type,
                dataType: 'json',
                async: false,
                success: function(JSONObject) {
                    console.log(JSONObject);
                    if (JSONObject.succ == 200) {
                        $('#invoice_cancel_body').html(JSONObject.data);
                        $('#invoice_cancel_body').removeClass('disable-blur');
                    }

                }
            });
        }



        $('#invoiceCancellation').show();
    }

    function cancelationOperation(invoiceId, delegateId, type, refference_id) {
        // alert('invoiceId='+invoiceId+'delegateId='+delegateId+'type='+type+'refference_id='+refference_id);
        flag = 0;
        if (invoiceId != '') {
            var cancelation_cause = $('#cancelation_cause' + invoiceId).val();
            var refundAmount = $('#refundAmount' + invoiceId).val();
            if (cancelation_cause == '') {
                toastr.error('Please enter the cancelation cause', 'Error', {
                    "progressBar": true,
                    "timeOut": 3000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });

                flag = 1;
                return false;
            }

            if (flag == 0) {

                $.ajax({
                    type: "POST",
                    url: jsBASE_URL + 'login.process.php',
                    data: 'action=cancelationInvoiceProcess&user_id=' + delegateId + '&serviceType=' + type + '&invoiceId=' + invoiceId + '&refference_id=' + refference_id + '&cancelation_cause=' + cancelation_cause + '&refundAmount=' + refundAmount,
                    dataType: 'json',
                    async: false,
                    success: function(JSONObject) {
                        console.log(JSONObject);
                        if (JSONObject.succ == 200) {

                            toastr.success(JSONObject.msg, 'Success', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });

                            $('#invoiceCancellation').hide();

                        } else if (JSONObject.error == 400) {
                            toastr.error('Server error', 'Error', {
                                "progressBar": true,
                                "timeOut": 3000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                        }

                    }
                });
            }

        }
    }

    function setAsPresenter(obj) {
        var presenter_email = $('#presenter_email_id').val();
        var presenter_mobile = $('#presenter_mobile').val();
        var presenter_title = $('#presenter_title').val();
        var presenter_first_name = $('#presenter_first_name').val();
        var presenter_last_name = $('#presenter_last_name').val();
        var presenter_country = $('#presenter_country').val();
        var presenter_state = $('#presenter_state').val();
        var presenter_city = $('#presenter_city').val();
        var presenter_pincode = $('#presenter_pincode').val();
        var presenter_institute = $('#presenter_institute').val();
        var presenter_department = $('#presenter_department').val();
        // alert(presenter_email);


        if ($(obj).prop('checked')) {


            if (presenter_email != '') {
                $('#abstract_author_email').val(presenter_email)
            }

            if (presenter_mobile != '') {
                $('#abstract_author_mobile').val(presenter_mobile);
            }

            if (presenter_title != '') {
                $('#abstract_author_title').val(presenter_mobile);

                $('input[name="abstract_author_title"][value="' + presenter_title + '"]').prop('checked', true);
            }

            if (presenter_first_name != '') {
                $('#abstract_author_first_name').val(presenter_first_name)
            }

            if (presenter_last_name != '') {
                $('#abstract_author_last_name').val(presenter_last_name)
            }

            if (presenter_country != '') {
                $('#abstract_author_country').val(presenter_country);
                generateStateOptionList($('#abstract_author_country'));
            }

            if (presenter_state != '') {
                $('#abstract_author_state').val(presenter_state)
            }

            if (presenter_city != '') {
                $('#abstract_author_city').val(presenter_city)
            }

            if (presenter_pincode != '') {
                $('#abstract_author_pincode').val(presenter_pincode)
            }

            if (presenter_institute != '') {
                $('#abstract_author_institute').val(presenter_institute)
            }

            if (presenter_department != '') {
                $('#abstract_author_department').val(presenter_department)
            }

            $('#isPresenter').val('Y')

        } else {

            // set author state
            $('#abstract_author_email').val('');
            $('#abstract_author_first_name').val('');
            $('#abstract_author_last_name').val('');
            $('input[name="abstract_author_title"]').prop('checked', false);


            // set author country 
            $('#abstract_author_country').val('')

            // set author state
            $('#abstract_author_state').val('')

            // set author city
            $('#abstract_author_city').val('')
            $('#abstract_author_pincode').val('')
            $('#abstract_author_institute').val('')
            $('#abstract_author_department').val('')

            $('#abstract_author_mobile').val('');


        }
    }

    function generateStateOptionList(obj, callback) {
        // var parent = $(obj).parent().closest(".com-country-state");

        var countryId = $(obj).val();

        $.ajax({
            type: "POST",
            url: jsBASE_URL + 'abstract_request.process.php',
            data: 'act=getStateOptionList&countryId=' + countryId,
            dataType: 'html',
            async: false,
            success: function(returnMessage) {
                //console.log(returnMessage+' country-state-data')
                if (returnMessage != '') {
                    $('#abstract_author_state').empty();
                    $('#abstract_author_state').empty().append(returnMessage);

                    // $(parent).find("select[operationMode=stateControl]").html("");
                    // $(parent).find("select[operationMode=stateControl]").html(returnMessage);
                }

                try {
                    callback();
                } catch (e) {}
            }
        });
    }

    function generateStateList(obj, state_selector) {

        selector = "select[id=" + state_selector + "]";
        countryId = $(obj).val();

        console.log(jBaseUrl + "returnData.process.php?act=generateStateList&countryId=" + countryId);

        if (countryId != "") {
            $.ajax({
                type: "POST",
                url: jBaseUrl + "returnData.process.php",
                data: "act=generateStateList&countryId=" + countryId,
                dataType: "html",
                async: false,
                success: function(JSONObject) {
                    $(selector).html(JSONObject);
                    $(selector).removeAttr("disabled");
                }
            });
        } else {
            $(selector).html('<option value="">-- Select Country First --</option>');
            $(selector).attr("disabled", "disabled");
        }
    }

    function countWords(stringValue) {
        //console.log("Length="+stringValue.length);
        s = stringValue;
        s = s.replace(/(^\s*)|(\s*$)/gi, "");
        s = s.replace(/[ ]{2,}/gi, " ");
        s = s.replace(/\n /, "\n");
        return s.split(' ').length;
    }


    function wordLimitCounter(obj) {

        var id = $(obj).attr('abs_id');
        var totalWordCount = 0;
        var totalCharacterCount = 0;
        var parent = $("div[use=abstractDetails" + id + "]");
        var wordCount = parseInt($(obj).attr("wordcount"));

        var group = $(obj).attr("spreadInGroup");
        var showWordCount = $(parent).find("span[use='" + $(obj).attr("displayText") + "']");
        var wordLimit = parseInt($(showWordCount).attr('limit'));
        var count = wordLimit;
        var totalCharacter = '';

        var word_type = '<?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>';

        console.log(count);
        $(parent).find("textarea[spreadInGroup='" + group + "']").each(function() {
            if ($(this).val() != "") {

                totalWordCount = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
                totalCharacterCount = parseInt(totalCharacterCount) + parseInt($(this).val().length);

                if ($("textarea[spreadInGroup='" + group + "']").length > 1) {

                    count = wordLimit - totalWordCount

                    countCharacter = parseInt(wordLimit) - parseInt(totalCharacterCount);

                    totalCharacter += $(this).val();

                    //console.log('countCharacter='+countCharacter);

                }

                if (word_type == 'word') {
                    if (totalWordCount > wordLimit) {
                        // prevent max word
                        //$(obj).val(truncateWords($.trim($(obj).val()),wordLimit));
                        $(showWordCount).css("color", "#D41000");
                        // $(this).val(truncateWords($.trim($(this).val()), count));
                    } else {
                        $(showWordCount).css("color", "");
                    }
                } else {
                    if (totalCharacterCount > wordLimit) {
                        console.log(1212);
                        console.log('totalCharacter=' + totalCharacterCount + 'wordLimit=' + wordLimit);

                        $(showWordCount).css("color", "#D41000");
                        //$(this).val(truncateCharacters($.trim($(this).val()),countCharacter));
                        if ($(this).val().length >= wordLimit) {
                            $(this).val($(this).val().substring($(this).val(), wordLimit));
                        } else if (countCharacter < wordLimit) {

                            $(this).val($(this).val().substring(0, countCharacter));
                            //$(this).val("");
                        } else {
                            //alert(2);
                            $(this).val("");
                        }

                    } else {
                        $(showWordCount).css("color", "");
                    }
                }


            }
        });

        $(showWordCount).find("span[use=total_word_entered]").text("");

        if (word_type == 'word') {
            $(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
        } else {
            $(showWordCount).find("span[use=total_word_entered]").text(totalCharacterCount);
        }

    }

    function deleteCoauthor(id, e) {
        e.preventDefault();
        // var id = $(this).attr('abs_id');
        var nextElement = $(this).parent().next();
        var accompanyCount = $('#coAuthorCounts' + id).val();
        var existingCoauthor = $('#existingCoAuthorNum' + id).val();

        if (accompanyCount == 1) {
            $(this).parent().hide();
        } else {
            $(this).parent().remove();
        }
        $('#coAuthorCounts' + id).val(Number(accompanyCount) - 1);
        $('#existingCoAuthorNum' + id).val(Number(existingCoauthor) - 1);
        console.log("coAthrCountdel= " + (Number(accompanyCount) - 1));

        $(this).find("span#coCount" + id).text(Number(accompanyCount) - 1);

        while (nextElement.length > 0) {
            var count = nextElement.find("span#coCount" + id).text();
            nextElement.find("span#coCount" + id).text(count - 1);
            nextElement = nextElement.next();
        }

        var accompanyAmount = $('#accompanyAmount' + id).val();

        var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(Number(accompanyCount) - 1);
        //$('#accompanyAmount').val(amountIncludedDay);

        $("#accompanyCount" + id).attr("amount", amountIncludedDay);
        $("#accompanyCount" + id).val(Number(accompanyCount) - 1);
    }



    function nextTab(elem) {
        $(elem).next().find('a[data-toggle="tab"]').click();
    }

    function prevTab(elem) {
        $(elem).prev().find('a[data-toggle="tab"]').click();
    }


    $('.nav-tabs').on('click', 'li', function() {
        $('.nav-tabs li.active').removeClass('active');
        $(this).addClass('active');
    });
    var stramble = $('.default-bt');
    var strambleText = stramble.text();
    stramble.each(function(e, i) {

        var length = i.innerText.length;

        var newDom = '';
        var transitionDelay = .03;

        newDom += '<span class="strambable">';
        for (let k = 0; k < length; k++) {
            newDom += '<span data-letter="' + i.innerText[k] + '" style="transition-delay: ' + transitionDelay * k + 's" class="letter">' + i.innerText[k] + '</span>';
        }
        newDom += '</span>';

        i.innerHTML = newDom;

    });
</script>
<script>
    let links = document.querySelectorAll('a');
    let background = document.querySelector('.link-background')


    const clickHandler = (el) => {
        links.forEach(link => {
            link.classList.remove('active');
        })
        el.classList.add('active');
    }
    links.forEach((link, index) => {
        link.addEventListener('click', (e) => {
            // e.preventDefault();
            // Update background position
            // background.style.transform = `translateX(${128.25 * index}%)`
            clickHandler(e.currentTarget);

        });
    })
</script>
<script>
    $(document).ready(function() {

        // set the date we're counting down to
        var target_date = new Date("<?= $resultFetchCountdown[0]['countdownDate'] ?>").getTime();


        // variables for time units
        var days, hours, minutes, seconds;

        var $days = $("#days"),
            $hours = $("#hours"),
            $minutes = $("#minutes"),
            $seconds = $("#seconds");

        var center = 150,
            canvas = document.getElementById('timer'),
            ctx = canvas.getContext("2d"),
            daySetup = {
                radie: 130,
                lineWidth: 25,
                back: 48,
                color: "#ED303C ",
                counter: 0,
                old: 0
            },
            hourSetup = {
                radie: 100,
                lineWidth: 25,
                back: 48,
                color: "#F5634A ",
                counter: 0,
                old: 0
            },
            minSetup = {
                radie: 70,
                lineWidth: 25,
                back: 45,
                color: "#FF9C5B",
                counter: 0,
                old: 0
            },
            secSetup = {
                radie: 40,
                lineWidth: 25,
                back: 65,
                color: "#FAD089",
                counter: 0,
                old: 0
            }


        check = function(count, setup, ctx) {
                if (count < setup.old) {
                    setup.counter++
                }
                draw(setup.radie, setup.color, setup.lineWidth, count, ctx);
            },
            draw = function(radie, color, lineWidth, count, ctx) {
                ctx.beginPath();
                ctx.arc(center, center, radie, 0, count * Math.PI, false);
                ctx.lineWidth = lineWidth;
                ctx.strokeStyle = color;
                ctx.stroke();
            };

        // update the tag with id "countdown" every 1 second
        setInterval(function() {
            canvas.width = canvas.width;

            ctx.beginPath();
            ctx.arc(center, center, 50, 0, 2 * Math.PI, false);
            ctx.lineWidth = 30,
                ctx.fillStyle = 'transparent';
            ctx.fill();

            // find the amount of "seconds" between now and target
            var current_date = new Date().getTime();
            var seconds_left = (target_date - current_date) / 1000;

            // do some time calculations
            days = parseInt(seconds_left / 86400);
            seconds_left = seconds_left % 86400;

            hours = parseInt(seconds_left / 3600);
            seconds_left = seconds_left % 3600;

            minutes = parseInt(seconds_left / 60);
            seconds = parseInt(seconds_left % 60);

            $days.text(days);
            $hours.text(hours);
            $minutes.text(minutes);
            $seconds.text(seconds);

            var dayCount = (2 / 360) * days,
                hourCount = (2 / 24) * hours,
                minCount = (2 / 60) * minutes,
                secCount = (2 / 60) * seconds;

            check(secCount, secSetup, ctx);
            check(minCount, minSetup, ctx);
            check(hourCount, hourSetup, ctx);
            check(dayCount, daySetup, ctx);

            secSetup.old = secCount - 0.01;
            minSetup.old = minCount - 0.01;
            hourSetup.old = hourCount - 0.01;
            daySetup.old = dayCount - 0.01;

        }, 1000);
    });
</script>
<script id="rendered-js" type="module">
    import gsap from 'https://cdn.skypack.dev/gsap@3.12.0';

    const UPDATE = ({
        x,
        y
    }) => {
        gsap.set(document.documentElement, {
            '--x': gsap.utils.mapRange(0, window.innerWidth, -1, 1, x),
            '--y': gsap.utils.mapRange(0, window.innerHeight, -1, 1, y)
        });

    };

    window.addEventListener('mousemove', UPDATE);


    // Want to handle device orientation too

    const handleOrientation = ({
        beta,
        gamma
    }) => {
        const isLandscape = window.matchMedia('(orientation: landscape)').matches;
        gsap.set(document.documentElement, {
            '--x': gsap.utils.clamp(-1, 1, isLandscape ? gsap.utils.mapRange(-45, 45, -1, 1, beta) : gsap.utils.mapRange(-45, 45, -1, 1, gamma)),
            '--y': gsap.utils.clamp(-1, 1, isLandscape ? gsap.utils.mapRange(20, 70, 1, -1, Math.abs(gamma)) : gsap.utils.mapRange(20, 70, 1, -1, beta))
        });

    };

    const START = () => {
        var _DeviceOrientationEve;
        // if (BUTTON) BUTTON.remove();
        if ((_DeviceOrientationEve =
                DeviceOrientationEvent) !== null && _DeviceOrientationEve !== void 0 && _DeviceOrientationEve.requestPermission) {
            Promise.all([
                DeviceOrientationEvent.requestPermission()
            ]).
            then(results => {
                if (results.every(result => result === "granted")) {
                    window.addEventListener("deviceorientation", handleOrientation);
                }
            });
        } else {
            window.addEventListener("deviceorientation", handleOrientation);
        }
    };
    document.body.addEventListener('click', START, {
        once: true
    });
    //# sourceURL=pen.js
</script>
<script>
    var swiper = new Swiper('.product-slider', {
        spaceBetween: 30,
        effect: 'fade',
        // initialSlide: 2,
        loop: false,
        navigation: {
            nextEl: '.next',
            prevEl: '.prev'
        },
        // mousewheel: {
        //     // invert: false
        // },
        on: {
            init: function() {
                var index = this.activeIndex;

                var target = $('.product-slider__item').eq(index).data('target');

                console.log(target);

                $('.product-img__item').removeClass('active');
                $('.product-img__item#' + target).addClass('active');
            }
        }

    });

    swiper.on('slideChange', function() {
        var index = this.activeIndex;

        var target = $('.product-slider__item').eq(index).data('target');

        console.log(target);

        $('.product-img__item').removeClass('active');
        $('.product-img__item#' + target).addClass('active');

        if (swiper.isEnd) {
            $('.prev').removeClass('disabled');
            $('.next').addClass('disabled');
        } else {
            $('.next').removeClass('disabled');
        }

        if (swiper.isBeginning) {
            $('.prev').addClass('disabled');
        } else {
            $('.prev').removeClass('disabled');
        }
    });

    $(".js-fav").on("click", function() {
        $(this).find('.heart').toggleClass("is-active");
    });
</script>
<script>
    const prvs = new Parvus();
    $('.flyre_slider').owlCarousel({
        loop: true,
        margin: 17,
        nav: false,
        dots: true,
        autoplay: true,
        autoplayTimeout: 6000,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 2
            },
            1500: {
                items: 3
            },
        }
    })
</script>
<!-- ========================================================================== -->
<script>
    function onlinePayNow(slipId, delegateId) {

        if (slipId != '' && delegateId != '') {
            $('#paymentVoucherBody').text("");
            $.ajax({
                type: "POST",
                url: jsBASE_URL + 'login.process.php',
                data: 'action=getPaymentVoucharDetailsProfile&delegateId=' + delegateId + '&slipId=' + slipId,
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
                        alert(JSONObject.delegateId)
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
    }
</script>

</html>