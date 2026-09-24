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

$weatherApiData = weatherApi();

// echo '<pre>'; print_r($weatherApiData['days']); die;

foreach ($weatherApiData['days'] as $k => $val) {
    if ($val['datetime'] == date('Y-m-d')) {
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
// abstract related work by weavers end

$sqlHeader    =   array();
$sqlHeader['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
	                        WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
$resultHeader = $mycms->sql_select($sqlHeader);
$rowHeader  = $resultHeader[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowHeader['header_image'];

$abstract_details = delegateAbstractDetailsSummeryWithoutTopic($delegateId);

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

<style>
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
                        <p><b>Uniq Sechequence: <?= $rowUserDetails['user_unique_sequence'] ?></b></p>
                    </div>
                    <div class="prf-dp" onclick="$('.prf-drop').toggleClass('prf-drop-open');">
                        <p><?= $rowUserDetails['user_full_name'] ?></p>
                        <img src="149071.png">
                        <div class="prf-drop">
                            <a><i class="fa-solid fa-pen-to-square"></i><span>Edit Profile</span></a>
                            <a><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="prf-body">
                <div class="prf-lft">
                    <div class="prf-lft-top">
                        <a class="active"><i class="fa-solid fa-house"></i> <span>Home</span></a>
                        <a><i class="fa-solid fa-pen-to-square"></i><span>Edit Profile</span></a>

                    </div>
                    <div class="prf-lft-bottom">
                        <a><i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span></a>
                    </div>
                </div>
                <div class="prf-rt">
                    <div class="prf-body-top">
                        <div class="prf-registartion-abstract">
                            <!-- ====================================== REGISTRATION ===================================================== -->
                            <!-- <div class="prf-registration">
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
                                            <li>
                                                <p>
                                                    <span style="color: #50b5ff;"><i class="fa-regular fa-circle"></i></span>
                                                    <span>Workshop</span>
                                                </p>
                                                <span class="check"><i class="fa-solid fa-check"></i></span>
                                            </li>
                                            <li>
                                                <p>
                                                    <span style="color: #fffb50;"><i class="fa-regular fa-circle"></i></span>
                                                    <span>Accompainy Person</span>
                                                </p>
                                                <span class="check"><i class="fa-solid fa-check"></i></span>
                                            </li>
                                            <li>
                                                <p>
                                                    <span style="color: #de50ff;"><i class="fa-regular fa-circle"></i></span>
                                                    <span>Accommodation</span>
                                                </p>
                                                <span class="check"><i class="fa-solid fa-check"></i></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <a class="abs-edit " onclick="$('.registration_view_modal').show();">View</a>
                            </div> -->
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
                                                <h4><b>Category :</b> <?= strtoupper($value['abstract_category']) ?> <?= $value['abstract_parent_type'] ?></h4>
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
                            <?php } else { ?>
                                <!--====================================== abstract not registered ======================================================-->
                                <div class="prf-abstract ">
                                    <div class="abstract-wrap-blank">
                                        <h5>Submission Closing On</h5>
                                        <div class="card-body">
                                            <h4><?= date('F', strtotime($cfg['ABSTRACT.EDIT.LASTDATE'])) ?></h4>
                                            <h3><?= date('d', strtotime($cfg['ABSTRACT.EDIT.LASTDATE'])) ?></h3>
                                            <h4><?= date('Y', strtotime($cfg['ABSTRACT.EDIT.LASTDATE'])) ?></h4>
                                        </div>
                                        <h6><a><i class="fa-solid fa-eye mr-2" style="font-size: 14px;"></i> View Submission Guidline</a><a class="submit-abstract">Submit</a></h6>
                                    </div>
                                </div>
                            <?php } ?>
                            <!--abstract not registered -->
                        </div>

                        <div class="prf-faculty">
                            <div class="content d-none">
                                <div class="bg-shape">
                                    <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405214/starwars/logo.webp" alt="">
                                </div>
                                <div class="product-img">
                                    <div class="product-img__item" id="img1">
                                        <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                    </div>

                                    <div class="product-img__item" id="img2">
                                        <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                    </div>

                                    <div class="product-img__item" id="img3">
                                        <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                    </div>

                                    <div class="product-img__item" id="img4">
                                        <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt="star wars" class="product-img__img">
                                    </div>
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
                                        <div class="product-slider__item swiper-slide" data-target="img1">
                                            <div class="product-slider__card">
                                                <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405222/starwars/item-1-bg.webp" alt="star wars" class="product-slider__cover">
                                                <div class="product-slider__content">
                                                    <h1 class="product-slider__title">Prof. Arun K. Varshneya</h1>
                                                    <span class="product-slider__price">From</span>
                                                    <div class="product-labels__group">
                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1" checked>
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>

                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1">
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>
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
                                        <div class="product-slider__item swiper-slide" data-target="img2">
                                            <div class="product-slider__card">
                                                <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405222/starwars/item-2-bg.webp" alt="star wars" class="product-slider__cover">
                                                <div class="product-slider__content">
                                                    <h1 class="product-slider__title">Prof. Arun K. Varshneya</h1>
                                                    <span class="product-slider__price">From</span>
                                                    <div class="product-labels__group">
                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1" checked>
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>
                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1">
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>
                                                    </div>
                                                    <!-- <div class="product-ctr">
                                                        <div class="product-labels">
                                                            <div class="product-labels__title">ENGINE UNIT</div>
                                                            
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
                                        <div class="product-slider__item swiper-slide" data-target="img3">
                                            <div class="product-slider__card">
                                                <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405215/starwars/item-3-bg.webp" alt="star wars" class="product-slider__cover">
                                                <div class="product-slider__content">
                                                    <h1 class="product-slider__title">Prof. Arun K. Varshneya</h1>
                                                    <span class="product-slider__price">From</span>
                                                    <div class="product-labels__group">
                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1" checked>
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>
                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1">
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>
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

                                        <div class="product-slider__item swiper-slide" data-target="img4">
                                            <div class="product-slider__card">
                                                <img src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405223/starwars/item-4-bg.webp" alt="star wars" class="product-slider__cover">
                                                <div class="product-slider__content">
                                                    <h1 class="product-slider__title">Prof. Arun K. Varshneya</h1>
                                                    <span class="product-slider__price">From</span>
                                                    <div class="product-labels__group">
                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1" checked>
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>

                                                        <label class="product-labels__item">
                                                            <input type="radio" class="product-labels__checkbox" name="type1">
                                                            <span class="product-labels__txt"><img style="width: 48px;" src="https://res.cloudinary.com/muhammederdem/image/upload/q_60/v1536405217/starwars/item-1.webp" alt=""></span>
                                                        </label>
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
                                    </div>
                                </div>
                            </div>
                            <div class="flyre_slider owl-carousel owl-theme">
                                <div>
                                    <a class="lightbox" href="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0034_241019162524.jpg" data-group="flyre-slider">
                                        <img src="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0034_241019162524.jpg" width="100%">
                                    </a>
                                </div>
                                <div>
                                    <a class="lightbox" href="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0035_241019162534.jpg" data-group="flyre-slider">
                                        <img src="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0035_241019162534.jpg" width="100%">
                                    </a>
                                </div>
                                <div>
                                    <a class="lightbox" href="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0034_241019162524.jpg" data-group="flyre-slider">
                                        <img src="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0034_241019162524.jpg" width="100%">
                                    </a>
                                </div>
                                <div>
                                    <a class="lightbox" href="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0035_241019162534.jpg" data-group="flyre-slider">
                                        <img src="https://ruedakolkata.com/ihpba2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/FLYER_0035_241019162534.jpg" width="100%">
                                    </a>
                                </div>
                            </div>
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
                                        <h2 class="date-dayname"><?= date("l", $timestamp) ?></h2><span class="date-day"><?= date("d M Y", strtotime($dateString)) ?></span><i class="location-icon" data-feather="map-pin"></i><span class="location"><?= ucwords($loc) ?></span>
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
                                                <div class="clear"></div>
                                            </div>
                                            <div class="humidity"> <span class="title">HUMIDITY</span><span class="value"><?= $todayhumidity ?> %</span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="wind"> <span class="title">WIND</span><span class="value"><?= $todaywindspeed ?> km/h</span>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="week-container">
                                        <ul class="week-list">
                                            <li class="active"><i class="day-icon" data-feather="sun"></i><span class="day-name">Tue</span><span class="day-temp">29°C</span></li>
                                            <li><i class="day-icon" data-feather="cloud"></i><span class="day-name">Wed</span><span class="day-temp">21°C</span></li>
                                            <li><i class="day-icon" data-feather="cloud-snow"></i><span class="day-name">Thu</span><span class="day-temp">08°C</span></li>
                                            <li><i class="day-icon" data-feather="cloud-rain"></i><span class="day-name">Fry</span><span class="day-temp">19°C</span></li>
                                            <div class="clear"></div>
                                        </ul>
                                    </div>
                                    <div class="location-container">
                                        <button class="location-button"> <i data-feather="map-pin"></i><span>Change location</span></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="prf-venue">
                            <article>
                                <div class="assets">
                                    <img src="https://assets.codepen.io/605876/do-not-copy-osaka-sky.jpeg" alt="">
                                    <h3>Osaka</h3>
                                    <img src="https://assets.codepen.io/605876/do-not-copy-osaka-tower.png" alt="">
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
                                        <span>Osaka Castle</span>
                                    </p>
                                    <p>Osaka, Japan</p>
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

<!-- ============================================== ADD ABSTRACT ======================================================= -->
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
                        <form role="form" action="index.html" class="login-box">
                            <ul class="abs_mod_tab nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Author Details</span></a>
                                </li>
                                <li role="presentation" class="disabled">
                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Co-Author Details</span></a>
                                </li>
                                <li role="presentation" class="disabled">
                                    <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Submission Category</span></a>
                                </li>
                                <li role="presentation" class="disabled">
                                    <a href="#step4" data-toggle="tab" aria-controls="step4" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Details</span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="abstract_add_form">
                                <div class="tab-pane active" role="tabpanel" id="step1">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Author Details</p>
                                        <div class="author-wrap">
                                            <div class="author_details_inner">
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">E-mail</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Mobile</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Title</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <div class="checkbox-wrap">
                                                            <label class="custom-radio">
                                                                <input type="radio" name="abstract_presenter_title" value="DR" checked=""><span class="checkbox-text">Dr.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="PROF"><span class="checkbox-text">Prof.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="MR"><span class="checkbox-text">Mr.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="MRS"><span class="checkbox-text">Mrs.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="MS"><span class="checkbox-text">Ms.</span><span class="checkmark"></span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">First Name</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Last Name</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Country</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <select>
                                                            <option>--Select Country--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">State</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <select>
                                                            <option>--Select State--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">City</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Postal Code</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Institute</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Department</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-inline ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step">Next</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step2">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Co-Author Details<a>Add More</a></p>
                                        <div class="author-wrap">
                                            <div class="author_details_inner">
                                                <div class="col-12 form-floating">
                                                    <p class="coauthor_li">Co-Author 1</p>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">E-mail</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Mobile</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 form-floating">
                                                    <label for="floatingInput">Title</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <div class="checkbox-wrap">
                                                            <label class="custom-radio">
                                                                <input type="radio" name="abstract_presenter_title" value="DR" checked=""><span class="checkbox-text">Dr.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="PROF"><span class="checkbox-text">Prof.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="MR"><span class="checkbox-text">Mr.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="MRS"><span class="checkbox-text">Mrs.</span><span class="checkmark"></span></label>
                                                            <label class="custom-radio"><input type="radio" name="abstract_presenter_title" value="MS"><span class="checkbox-text">Ms.</span><span class="checkmark"></span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">First Name</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Last Name</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Country</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <select>
                                                            <option>--Select Country--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">State</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <select>
                                                            <option>--Select State--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">City</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Postal Code</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Institute</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 form-floating">
                                                    <label for="floatingInput">Department</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <ul class="list-inline   ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step">Next</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step3">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Submission Category</p>
                                        <div class="author-wrap m-0">
                                            <div class="sub-cate-checkbox-wrap">
                                                <label class="custom-radio">
                                                    <input type="radio" name="abstract_presenter_title" checked=""><span class="checkbox-text">Abstract</span><span class="checkmark"></span></label>
                                                <label class="custom-radio"><input type="radio" name="abstract_presenter_title"><span class="checkbox-text">Case Reports</span><span class="checkmark"></span></label>
                                                <label class="custom-radio"><input type="radio" name="abstract_presenter_title"><span class="checkbox-text">Video Abstract</span><span class="checkmark"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-inline ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step">Next</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step4">
                                    <div class="author_details">
                                        <p class="abst_mod_title mb-3">Submit Your Abstract</p>
                                        <div class="author-wrap">
                                            <div class="author_details_inner">
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Topic</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <select>
                                                            <option>--Choose your topic--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Title</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Introduction</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Methods & Materials</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Result</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Conclutions</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Introduction</label>
                                                    <div class="d-flex">
                                                        <span><img src="images/email-R.png" alt=""></span>
                                                        <textarea></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-12 form-floating">
                                                    <label for="floatingInput">Attach Content</label>
                                                    <div class="">
                                                        <input type="file" id="input-abs" style="display: none;">
                                                        <label for="input-abs" class="input-label"><i class="fas fa-cloud-upload-alt"></i><br>Choose File<br><span>PDF/Imgae</span></label>
                                                        <ul class="attched_details mt-3">
                                                            <li>
                                                                <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                <div class="attached_file">
                                                                    <p>Attached Name<br><span>Date</span></p>
                                                                    <a><i class="fas fa-trash-alt"></i></a>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                <div class="attached_file">
                                                                    <p>Attached Name<br><span>Date</span></p>
                                                                    <a><i class="fas fa-trash-alt"></i></a>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-inline ">
                                        <div class="next-prev-btn-box">
                                            <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                            <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                            <button type="button" class="default-btn next-btn next-step submit-edit">Submit</button></li>
                                        </div>
                                    </ul>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== ABSTRACT EDIT ================================================ -->
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
                                <h4><b>Category :</b> <?= strtoupper($value['abstract_category']) ?></h4>
                                <p class="abst_sub_status">Abstract Submitted<span class="bst_sub_date"><?= date('d/m/Y', strtotime($value['created_dateTime'])) ?></span></p>
                                <ul class="attched_details">
                                    <li>
                                        <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                        <div class="attached_file">
                                            <p>Attached Name<br><span><?= date('d/m/Y', strtotime($value['created_dateTime'])) ?></span></p>
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
                        <?php } ?>

                    </ul>
                </div>
                <?php foreach ($abstract_details as $key => $value) {
                    // echo '<pre>'; print_r($value);
                ?>
                    <!-- ================================= EDIT ==================================== -->
                    <div class="abstract_edit_wrap has-edit" id="abstract_edit_form_<?= $value['id'] ?>" style="padding: 18px 30px;">
                        <div class="wizard">
                            <form role="form" action="<?= _BASE_URL_ ?>abstract_request.process.php" class="login-box">
                                <input type="hidden" name="act" value="abstractUpdate" />
                                <input type="hidden" name="applicantId" id="applicantId" value="<?= $delegateId ?>" />
                                <input type="hidden" name="report_data" id="report_data" value="Abstract" />
                                <input type="hidden" name="abstract_id" value="<?= $value['id'] ?>">
                                <ul class="abs_mod_tab nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#step1_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Author Details</span></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step2_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Co-Author Details</span></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step3_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step3" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Submission Category</span></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step4_<?= $value['id'] ?>" data-toggle="tab" aria-controls="step4" role="tab"><i class="fa-solid fa-pen-to-square"></i><span class="round-tab">Details</span></a>
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
                                                                <label class="custom-radio"><input type="radio" name="abstract_author_title" value="DR" <?php if ($value['abstract_author_title'] == 'DR') {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>> Dr.<span class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="abstract_author_title" value="PROF" <?php if ($value['abstract_author_title'] == 'PROF') {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?>> Prof..<span class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="abstract_author_title" value="MR" <?php if ($value['abstract_author_title'] == 'MR') {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>> Mr.<span class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="abstract_author_title" value="MRS" <?php if ($value['abstract_author_title'] == 'MRS') {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?>> Mrs.<span class="checkmark"></span></label>
                                                                <label class="custom-radio"><input type="radio" name="abstract_author_title" value="MS" <?php if ($value['abstract_author_title'] == 'MS') {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>> Ms.<span class="checkmark"></span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">First Name</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_first_name" name="abstract_author_first_name" value="<?= $author_fname ?>" placeholder="" validate="Please enter the first name">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Last Name</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_last_name" name="abstract_author_last_name" placeholder="" value="<?= $author_lname ?>" validate="Please enter the last name">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Country</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <select class="form-control select" name="abstract_presenter_country" id="abstract_presenter_country_<?= $value['id'] ?>" forType="country" style="text-transform:uppercase;" required validate="Please select country">
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
                                                                        <option value="<?= $rowFetchCountry['country_id'] ?>" <?php if ($rowFetchCountry['country_id'] == $author_countryId) {
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
                                                            <select class="form-control select" name="abstract_presenter_state" id="abstract_presenter_state" forType="state" style="text-transform:uppercase;" required validate="Please select state">
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
                                                                        <option value="<?= $rowFetchCountry['st_id'] ?>" <?php if ($author_stateId == $rowFetchCountry['st_id']) {
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
                                                            <input type="text" class="form-control" id="abstract_author_city" name="abstract_author_city" placeholder="" value="<?= $author_city ?>" validate="Please enter the city">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Postal Code</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_pincode" name="abstract_author_pincode" value="<?= $author_pin ?>" placeholder="" validate="Please enter the postal code">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Institute</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_institute_name" name="abstract_author_institute_name" value="<?= $author_pin ?>" placeholder="" validate="Please enter the postal code">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 form-floating">
                                                        <label for="floatingInput">Department</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <input type="text" class="form-control" id="abstract_author_department" name="abstract_author_department" value="<?= $author_pin ?>" placeholder="" validate="Please enter the postal code">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step">Prev</button>
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
                                                    $sqlCoAuthor['PARAM'][]   = array('FILD' => 'abstract_id', 'DATA' => $_REQUEST['abstract_id'], 'TYP' => 's');

                                                    $resultCoAuthor = $mycms->sql_select($sqlCoAuthor);

                                                    if ($resultCoAuthor) {
                                                        $i = 1;
                                                        foreach ($resultCoAuthor as $k => $val) {

                                                            $drChecked = ($val['abstract_coauthor_title'] == 'Dr') ? "checked" : "";
                                                    ?>
                                                            <!-- <div class="author-wrap add_coathor">
                                                        <div class="author_details_inner"> -->
                                                            <div class="add_coathor">
                                                                <div class="col-12 form-floating">
                                                                    <p class="coauthor_li">Co-Author <span id="coCount<?= $value['id'] ?>"><?= $i ?></span></p>
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
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Dr" <?= $drChecked ?>> Dr.<span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Prof" <?php if (trim($val['abstract_coauthor_title']) == 'Prof') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo "";
                                                                                                                                                                                    } ?>> Prof.<span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Mr" <?php if (trim($val['abstract_coauthor_title']) == 'Mr') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo "";
                                                                                                                                                                                    } ?>> Mr.<span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Mrs" <?php if (trim($val['abstract_coauthor_title']) == 'Mrs') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo "";
                                                                                                                                                                                    } ?>> Mrs.<span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[<?= $k ?>]" value="Ms" <?php if (trim($val['abstract_coauthor_title']) == 'Ms') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo "";
                                                                                                                                                                                    } ?>> Ms.<span class="checkmark"></span></label>
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
                                                                        <select class="form-control select" name="abstract_coauthor_country[<?= $k ?>]" id="abstract_coauthor_country_<?= $value['id'] ?>" forType="country" style="text-transform:uppercase;" required validate="Please select the coauthor country">
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
                                                                        <select class="form-control select" name="abstract_coauthor_state[<?= $k ?>]" id="abstract_coauthor_state_<?= $value['id'] ?>" forType="state" style="text-transform:uppercase;" required validate="Please select the coauthor state">
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
                                                                    <p class="coauthor_li" style="justify-content: unset;">Co Author -&nbsp; <span id="coCount<?= $value['id'] ?>">1</span></p>
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
                                                                                <input type="radio" name="abstract_coauthor_title[0]" value="DR" checked=""><span class="checkbox-text">Dr.</span><span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="PROF"><span class="checkbox-text">Prof.</span><span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MR"><span class="checkbox-text">Mr.</span><span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MRS"><span class="checkbox-text">Mrs.</span><span class="checkmark"></span></label>
                                                                            <label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="MS"><span class="checkbox-text">Ms.</span><span class="checkmark"></span></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">First Name</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="First Name" id="abstract_author_first_name_<?= $value['id'] ?>" name="abstract_author_first_name[0]" placeholder="" validate="Please enter the coauthor first name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Last Name</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Last Name" id="abstract_author_last_name_<?= $value['id'] ?>" name="abstract_author_last_name[0]" placeholder="" validate="Please enter the coauthor last name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Country</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <select class="form-control select coauther_details " use="Country" name="abstract_coauthor_country[0]" id="abstract_coauthor_country_<?= $value['id'] ?>" forType="country" style="text-transform:uppercase;" required validate="Please select the coauthor country">
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
                                                                        <input type="text" class="form-control coauther_details " use="City" id="abstract_coauthor_city_<?= $value['id'] ?>" name="abstract_author_city[0]" placeholder="" validate="Please enter the coauthor city">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6 form-floating">
                                                                    <label for="floatingInput">Postal Code</label>
                                                                    <div class="d-flex">
                                                                        <span><img src="images/email-R.png" alt=""></span>
                                                                        <input type="text" class="form-control coauther_details " use="Postal Code" id="abstract_author_pincode_<?= $value['id'] ?>" name="abstract_author_pincode[0]" placeholder="" validate="Please enter the coauthor pincode" onkeypress="return isNumber(event)">
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
                                                <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                                <button type="button" class="default-btn next-btn next-step">Next</button></li>
                                            </div>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step3_<?= $value['id'] ?>">
                                        <div class="author_details">
                                            <p class="abst_mod_title mb-3">Submission Category</p>
                                            <div class="author-wrap m-0">
                                                <div class="sub-cate-checkbox-wrap">
                                                    <label class="custom-radio">
                                                        <input type="radio" name="abstract_presenter_title" checked=""><span class="checkbox-text">Abstract</span><span class="checkmark"></span></label>
                                                    <label class="custom-radio"><input type="radio" name="abstract_presenter_title"><span class="checkbox-text">Case Reports</span><span class="checkmark"></span></label>
                                                    <label class="custom-radio"><input type="radio" name="abstract_presenter_title"><span class="checkbox-text">Video Abstract</span><span class="checkmark"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                                <button type="button" class="default-btn next-btn next-step">Next</button></li>
                                            </div>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step4_<?= $value['id'] ?>">
                                        <div class="author_details">
                                            <p class="abst_mod_title mb-3">Submit Your Abstract</p>
                                            <div class="author-wrap">
                                                <div class="author_details_inner">
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Topic</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <select>
                                                                <option>--Choose your topic--</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Title</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Introduction</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Methods & Materials</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Result</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Conclutions</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Introduction</label>
                                                        <div class="d-flex">
                                                            <span><img src="images/email-R.png" alt=""></span>
                                                            <textarea></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12 form-floating">
                                                        <label for="floatingInput">Attach Content</label>
                                                        <div class="">
                                                            <input type="file" id="input-abs" style="display: none;">
                                                            <label for="input-abs" class="input-label"><i class="fas fa-cloud-upload-alt"></i><br>Choose File<br><span>PDF/Imgae</span></label>
                                                            <ul class="attched_details mt-3">
                                                                <li>
                                                                    <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                    <div class="attached_file">
                                                                        <p>Attached Name<br><span>Date</span></p>
                                                                        <a><i class="fas fa-trash-alt"></i></a>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <span class="attached_file_icon"><i class="fa-solid fa-file"></i></span>
                                                                    <div class="attached_file">
                                                                        <p>Attached Name<br><span>Date</span></p>
                                                                        <a><i class="fas fa-trash-alt"></i></a>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline ">
                                            <div class="next-prev-btn-box">
                                                <button type="button" class="default-btn skip-step"><i class="fa-solid fa-rotate-left"></i> Skip</button>
                                                <button type="button" class="default-btn next-btn prev-step">Prev</button>
                                                <button type="button" class="default-btn next-btn next-step submit-edit">Submit</button></li>
                                            </div>
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


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
                    <!-- ********************************************* REGISTRATION DETAILS BOX ********************************************** -->
                    <ul class="abstract-list-box">

                        <!-- ====================================== CONFERENCE ================================== -->
                        <li class="abstract-list-content" style="padding-right: 0;">
                            <h4 class="abs-modal-submission">Conference</h4>
                            <ul class="con_attch">
                                <li>Entry to Scientific Halls</li>
                                <li>Conference Kit</li>
                                <li>Entry to Exhibition Area</li>
                                <li>Tea/Coffee during the Conference</li>
                                <li>Lunch on 02.09.2024, 03.09.2024</li>
                                <li>Dinner on 02.12.2024</li>
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

                                        ?>
                                    <?php
                                }
                                    ?>
                                    <br><br><a href="<?= _BASE_URL_ . "profile-add.php?section=4"; ?>" <?= $disabled ?> class="btn">Add Accompaning</a>

                        </li>

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

                                        <span>Room <?= $key ?></span>

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
                                            <span style="font-style: italic;font-size: 14px;"><?= (!empty($total_stay) ? str_replace("+", "", $total_stay) . ' at ' : '') . $hotel_name ?></span>
                                            <br>
                                            <h5>
                                                <span><sup>*</sup>Check In -</span>
                                                <span>DATE: <?= $mycms->cDate('d/m/Y', $value['checkin_date']) ?></span>

                                                <br>
                                                <span><sup>*</sup>Check Out -</span>
                                                <span>DATE: <?= $mycms->cDate('d/m/Y', $value['checkout_date']) ?></span>

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
                                        <a class="btn smallhed addCategory" style="text-align: center;border-radius: 20px; padding: 5px 15px;  color: #005e82; cursor:pointer; white-space: nowrap;font-size: 14px;margin-bottom: 7px;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationMoreNight(6,'<?= $hotel_id ?>')">ADD MORE NIGHTS</a>
                                    <?php
                                    }
                                    //echo $accommodation_room;
                                    if ($getAccommodationMaxRoom < 3) {
                                    ?>


                                        <a class="btn smallhed addCategory" style="text-align: center;border-radius: 20px; padding: 5px 15px;  color: #005e82; cursor:pointer; white-space: nowrap;font-size: 14px;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationMoreNight(6,'<?= $hotel_id ?>')">ADD MORE ROOMS</a>

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
                        <div class="checkbox-wrap">
                            <label class="custom-radio"><input type="checkbox" name="abstract_presenter_title" value="DR" checked=""><span class="checkbox-text">Registration</span><span class="checkmark"></span></label>
                            <label class="custom-radio"><input type="checkbox" name="abstract_presenter_title" value="PROF" checked=""><span class="checkbox-text">Workshop</span><span class="checkmark"></span></label>
                            <label class="custom-radio"><input type="checkbox" name="abstract_presenter_title" value="MR"><span class="checkbox-text">Dinner</span><span class="checkmark"></span></label>
                        </div>
                        <h5 class="cancl-modal-heading">Reason for cancellation</h5>
                        <textarea></textarea>
                        <button class="default-btn next-step">Submit Request</button>
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
                        <input type="radio" name="card_mode" use="card_mode_select" value="Indian" checked style="visibility: hidden;">
                    </div>
                    <div class="summery" id="orderSummerySection">
                        <h4 class="block-head">Order Summery</h4>
                        <!-- <ul use="totalAmountTable"> -->
                        <div class="cart-data-row " use="totalAmount" id="paymentVoucherBody">


                            <!-- </ul> -->
                        </div>

                        <input type="submit" class="payment-button" id="pay-button-vouchar" value="Pay Now">&nbsp;
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
        $('.submit-edit').click(function(e) {
            $('.abstract_view_modal').hide().removeClass('abstract_add_modal');
            $('.non-edit-sub-heading').show();
            $('.edit-sub-heading').hide();
            $('.add-sub-heading').hide();
        });
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

        // First Author section button click function
        $('.next-step-edit').click(function() {
            var id = $(this).attr('abs_id');
            var step = $(this).attr('step');
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

            console.log("flag= " + flag);
            if (flag == 0) {
                var active = $('.abs_mod_tab.nav-tabs li.active');
                active.next().removeClass('disabled');
                nextTab(active);
            }

        });

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

            var active = $('.abs_mod_tab.nav-tabs li.active');
            active.next().removeClass('disabled');
            nextTab(active);

        });
        $(".prev-step").click(function(e) {

            var active = $('.abs_mod_tab.nav-tabs li.active');
            prevTab(active);

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
                // alert(incrementedCount);
            } else {
                // var accompanyCount = $('#coAuthorCounts').val(); 
                var incrementedCount = Number(existingCoAuthor) + 1;
                console.log(incrementedCount);
                console.log("incElse== " + incrementedCount);
                $('#existingCoAuthorNum' + id).val(incrementedCount);
                // alert(incrementedCount);
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

            var newAccompany = $(".add_coathor:first").clone();
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
            newAccompany.find("input[type='radio']").attr("name", "abstract_author_title[" + fieldSerializeCount + "]");

            newAccompany.find("input[type='radio'][name='abstract_author_title[" + fieldSerializeCount + "]']").each(function(index, element) {

                var inputType = $(element).attr("type");
                var inputId = $(element).attr("id");
                //alert(inputId)
            });

            $("#accordion-body-coauthor-" + id).append(newAccompany);

            newAccompany.append('<button class="delete-coauthor-btn" onclick="deleteCoauthor(' + id + ',event)" abs_id="' + id + '" count="' + accompanyCount + '">Delete</button>');

        }

        $(".add-coauthor-btn").on("click", function(e) {
            e.preventDefault();
            var id = $(this).attr('abs_id');
            addCoauthor(id);
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



    }); // 

    function deleteCoauthor(id, e) {
        e.preventDefault();
        // var id = $(this).attr('abs_id');
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