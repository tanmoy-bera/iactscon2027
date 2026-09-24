<?php
include_once("includes/frontend.init.php");

include_once("includes/function.invoice.php");
include_once('includes/function.delegate.php');
include_once('includes/function.workshop.php');
include_once('includes/function.dinner.php');
include_once('includes/function.exhibitor.php');
include_once('includes/function.accommodation.php');
include_once('includes/function.registration.php');

$show = $_REQUEST['show'];

$encoded_code = $_GET['id'];

// Secret key for decryption (same as encryption key)
$encryption_key = 'thisisaverysecurekeythatismorethan32';

list($encrypted_data, $hmac) = explode('.', $encoded_code);

// Verify the HMAC to ensure integrity
$calculated_hmac = hash_hmac('sha256', $encrypted_data, $encryption_key);

if (!hash_equals($hmac, $calculated_hmac)) {
    die("HMAC verification failed. Data might have been tampered with.");
}

// Decode the Base64-encoded string
$decoded_data = base64_decode($encrypted_data);

// Extract the IV and encrypted value
$iv = substr($decoded_data, 0, 16);
$encrypted_code = substr($decoded_data, 16);

// Decrypt the company code
$decrypted_code = openssl_decrypt($encrypted_code, 'aes-256-cbc', $encryption_key, 0, $iv);

if ($decrypted_code === false) {
    die("Decryption failed.");
}



$sqlCompany['QUERY']    = " SELECT * FROM " . _DB_EXIBITOR_COMPANY_ . " 
                                WHERE `status` = 'A' AND `exhibitor_company_code`= '" . $decrypted_code . "' ";

$resCompany = $mycms->sql_select($sqlCompany);

if (!$resCompany) {
    $mycms->redirect(_BASE_URL_);
}
$company_code = $resCompany[0]['exhibitor_company_code'];
// echo "Decrypted Company Code: " . $decrypted_code;die;

$sql = array();
$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                                    WHERE `status`='A' order by id desc limit 1";

$result = $mycms->sql_select($sql);
$row    = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
if ($row['header_image'] != '') {
    $emailHeader  = $header_image;
}

$sqlConfDate = array();
$sqlConfDate['QUERY']    = " SELECT MIN(conf_date) AS startDate, MAX(conf_date) AS endDate
                                   FROM " . _DB_CONFERENCE_DATE_ . " 
                                  WHERE `status` = ?";
$sqlConfDate['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
$resConfDate = $mycms->sql_select($sqlConfDate);
$rowConfDate = $resConfDate[0];

$cutoffArray  = array();
$sqlCutoff['QUERY']    = " SELECT * 
                                 FROM " . _DB_TARIFF_CUTOFF_ . " 
                                WHERE `status` = ? 
                             ORDER BY `cutoff_sequence` ASC";
$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
$resCutoff = $mycms->sql_select($sqlCutoff);
if ($resCutoff) {
    foreach ($resCutoff as $i => $rowCutoff) {
        $cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
    }
}


$sqlInfo  = array();
$sqlInfo['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
             WHERE `status` = ?";
$sqlInfo['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
$resultInfo      = $mycms->sql_select($sqlInfo);
$rowInfo         = $resultInfo[0];
$available_registration_fields = json_decode($rowInfo['available_registration_fields']);


$currentCutoffId = getTariffCutoffId();

$conferenceTariffArray   = getAllRegistrationTariffs("", false);

$workshopDetailsArray      = getAllWorkshopTariffs();
$workshopCountArr          = totalWorkshopCountReport();
$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);

$sqlFetchHotel      = array();
$sqlFetchHotel['QUERY'] = "SELECT * 
                     FROM " . _DB_MASTER_HOTEL_ . "
                    WHERE `status` =  ? ";

$sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
$resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);
// echo '<pre>'; print_r($resultFetchHotel);die;
// $hotel_count = count($resultFetchHotel) - 1;
$hotel_count = $mycms->sql_numrows($resultFetchHotel);




$userREGtype                = $_REQUEST['userREGtype'];
$abstractDelegateId      = $_REQUEST['delegateId'];
$userRec                  = getUserDetails($abstractDelegateId);

$exhibitor_company = getExhibitorCompanyDetails();


// $allExhibitorTariff = getExhibitortariff($company_code, 1);
// echo '<pre>'; print_r($allExhibitorTariff);die;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="image/favicon.png" type="favicon">
    <title>:: Exhibitor Registration | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>

    <?php
    setTemplateStyleSheet();
    setTemplateBasicJS();
    //backButtonOffJS();





    ?>
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>roboto_css.css" />
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>terms_cond_refund_privacy.css" />
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>all.css" />
    <!-- <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>login_css.php?link_color=<?= $cfg['link_color'] ?>" /> -->

    <link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>util/fontawesome.v5.7.2/css/all.css" />
    <link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>css/website/input-material_css.php?link_color=" />
    <link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>util/bootstrap.3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.tariff.js"></script>

    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/dinner_registration.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/accompany_registration.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.paymentArea.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/workshop_registration.js"></script>
    <!-- <script src="<?= _BASE_URL_ ?>/js/website/returnData.process.js"></script> -->
    <!-- <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_login/scripts/CountryStateRetriver.js"></script> -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Sacramento&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            --duration: .45s;
            --cubic: cubic-bezier(0.4, 0, 0.2, 1);
            --color-1: #d5dadd;
            --color-2: #51d5c2;
            --b: 2px;
            --h: 41px;
            --frm-lft-width: 400px;
            --fx-position: 30px;
            --header-height: 50px;
            --bottom-height: 75px;
            --color1: #D92027;
            --color2: #C23127;
            --color3: #F7961D;
            --color4: #212A5E;
            --color5: #1862AB;
            --color6: #5975A5;
            /* background: rebeccapurple; */
        }

        .formbold-mb-3 {
            margin-bottom: 15px;
        }

        .formbold-relative {
            position: relative;
        }

        .formbold-opacity-0 {
            opacity: 0;
        }

        .formbold-stroke-current {
            stroke: #ffffff;
            z-index: 999;
        }

        #supportCheckbox:checked~div span {
            opacity: 1;
        }

        #supportCheckbox:checked~div {
            background: #6a64f1;
            border-color: #6a64f1;
        }

        .formbold-main-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .formbold-form-wrapper {
            /* margin: 0 auto; */
            max-width: 1200px;
            width: 100%;
            background: white;
            padding: 0;
        }

        .formbold-img {
            display: block;
            margin: 0 auto 45px;
        }

        .formbold-input-wrapp>div {
            display: flex;
            gap: 20px;
        }

        .formbold-input-flex {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .formbold-input-flex>div {
            width: 50%;
        }

        .formbold-form-input {
            width: 100%;
            font-weight: 500;
            font-size: 16px;
            color: #ffffff !important;
            outline: none;
            resize: none;
            height: 54px;
            border-radius: 10px;
            background: #ffffff1a;
            backdrop-filter: opacity(0.1);
            border: 1px solid #ffffff7d !important;
            padding: 10px 22px;
            box-shadow: none;
            margin-bottom: 15px;
        }

        .formbold-form-input::placeholder,
        select.formbold-form-input,
        .formbold-form-input[type='date']::-webkit-datetime-edit-text,
        .formbold-form-input[type='date']::-webkit-datetime-edit-month-field,
        .formbold-form-input[type='date']::-webkit-datetime-edit-day-field,
        .formbold-form-input[type='date']::-webkit-datetime-edit-year-field {
            color: rgb(255 255 255);
        }

        .formbold-form-input:focus {
            border-color: #6a64f1;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
        }

        .formbold-form-label {
            color: #ffffff;
            font-size: 14px;
            line-height: 1;
            display: block;
            margin-bottom: 12px;
            padding-top: 15px;
            width: 100% !important;
        }

        .formbold-checkbox-label {
            display: flex;
            cursor: pointer;
            user-select: none;
            font-size: 16px;
            line-height: 24px;
            color: #536387;
        }

        .formbold-checkbox-label a {
            margin-left: 5px;
            color: #6a64f1;
        }

        .formbold-input-checkbox {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .formbold-checkbox-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            margin-right: 16px;
            margin-top: 2px;
            border: 0.7px solid #dde3ec;
            border-radius: 3px;
        }

        .formbold-form-file {
            padding: 12px;
            font-size: 14px;
            line-height: 24px;
            color: rgba(83, 99, 135, 0.5);
        }

        .formbold-form-file::-webkit-file-upload-button {
            display: none;
        }

        .formbold-form-file:before {
            content: 'Upload';
            display: inline-block;
            background: #EEEEEE;
            border: 0.5px solid #E7E7E7;
            border-radius: 3px;
            padding: 3px 12px;
            outline: none;
            white-space: nowrap;
            -webkit-user-select: none;
            cursor: pointer;
            color: #637381;
            font-weight: 500;
            font-size: 12px;
            line-height: 16px;
            margin-right: 10px;
        }

        .formbold-btn {
            font-size: 16px;
            border-radius: 5px;
            padding: 14px 25px;
            border: none;
            font-weight: 500;
            background-color: #6a64f1;
            color: white;
            cursor: pointer;
            margin-top: 25px;
        }

        .formbold-btn:hover {
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
        }

        .formbold-w-45 {
            width: 45%;
        }

        .tr {
            font-size: 14px;
            color: #556087;
            font-weight: bolder;
            padding: 5px 5px;
        }

        .banner-wrap {
            padding: 29px 0;
            display: flex;
            justify-content: space-between;
            margin-bottom: 0px;
            gap: 12px;
        }

        .banner-wrap h1 {
            margin-bottom: 0;
        }

        .cus-con {
            /* display: block; */
            position: relative;
            padding-left: 35px;
            margin-bottom: 15px;
            cursor: pointer;
            font-size: 15px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            height: 54px;
            border-radius: 10px;
            background: #ffffff1a;
            backdrop-filter: opacity(0.1);
            border: 1px solid #ffffff7d !important;
            padding: 10px 22px;
            font-weight: 500;
            line-height: 32px;
            padding-left: 54px;
            color: white;
        }

        /* Hide the browser's default checkbox */
        .cus-con input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 15px;
            left: 22px;
            height: 20px;
            width: 20px;
            background-color: #eeeeee4a;
            border-radius: 4px;
        }

        /* On mouse-over, add a grey background color */
        .cus-con:hover input~.checkmark {
            background-color: var(--color6);
        }

        /* When the checkbox is checked, add a blue background */
        .cus-con input:checked~.checkmark {
            background-color: var(--color6);
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .cus-con input:checked~.checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .cus-con .checkmark:after {
            left: 8px;
            top: 3px;
            width: 5px;
            height: 11px;
            border: solid white;
            border-width: 0 2px 2px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .personal_info {
            background: #2e151187;
            padding: 14px;
            /* box-shadow: 10px 10px 15px #00000000; */
            border-radius: 10px;
            /* border: 1px solid #2a0f1f; */
            overflow: hidden;
            backdrop-filter: blur(5px);
            width: 100%;
            height: 100%;
            overflow: auto;
        }

        .sub-heading {
            padding: 14px 29px;
            background: var(--color4);
            margin: -14px;
            margin-bottom: 13px;
            font-size: 18px;
            color: white;
            position: sticky;
            top: -14px;
            z-index: 1;
        }

        .frm-num {}

        .frm-num p {
            color: white;
        }

        .frm-num span {
            font-size: 24px;
            font-weight: bold;
            background: #a5b6ae;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #518d71;
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #21573d;
        }

        .heading {
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
        }

        .heading h1 {
            margin: 0;
            font-size: 31px;
            text-align: right;
            display: flex;
            justify-content: end;
            align-items: center;
            gap: 15px;
            font-weight: bold;
            color: white;
        }

        .heading h1 a {
            font-size: 14px;
            padding: 14px 22px;
            /* border: 1px solid #ffffff; */
            display: inline-block;
            color: #000000;
            border-radius: 10px;
            background: #faba1b;
        }

        video {
            width: 100%;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            object-fit: cover;
        }

        .logo {
            position: relative;
            background: white;
            margin-top: 0;
        }

        .table td,
        .theader th {
            padding: 15px 22px !important;
            color: white;
            vertical-align: middle !important;
            border: 0 !important;
        }

        .table tr td {
            border-top: 1px solid #ffffff7d !important;
            border-right: 1px solid #ffffff7d !important;
        }

        .theader th {
            border-bottom: 1px solid #ffffff7d !important;
            border-right: 1px solid #ffffff7d !important;
        }

        .table tr:first-child td {
            border-top: 0 !important;
        }

        .table tr td:last-child,
        .theader th:last-child {
            border-right: 0 !important;
        }

        .theader td,
        .theader th {
            background: #6c8cbb8a;

        }

        /* .theader td:first-child {
            background: #6c8cbb8a;
            
        } */
        .table-striped>tbody>tr {
            background-color: transparent !important;
        }

        .table-wrap {
            padding: 15px;
        }

        .table {
            margin-bottom: 0;
            border-radius: 10px;
            border: 1px solid #ffffff7d !important;
            border-collapse: separate;
            overflow: hidden;
        }

        .table .cus-con {
            background: transparent;
            border: 0 !important;
            margin: 0;
            padding-top: 0;
            padding-right: 0;
            padding-bottom: 0;
            line-height: 1;
            height: auto;
            line-height: 31px;
            padding-left: 36px;
        }

        .table .cus-con .checkmark {
            left: 0;
            top: 6px;
        }

        .save-btn {
            display: inline-block;
            padding: 13px 34px;
            text-transform: uppercase;
            background: #f7961d;
            color: white !important;
            border: none;
            font-size: 16px;
        }

        select option {
            background: #b18894;
        }

        .formbold-form-input:focus {
            outline: 0;
        }

        .check-in-out {
            display: flex;
            gap: 5px;
        }

        .page-heading {
            color: #f7f7f7;
            z-index: 1;
            position: relative;
            text-align: center;
            /* border-bottom: 2px solid; */
            padding: 20px 0;
            font-weight: 800;
            margin: 0;
            background: #724056c7;
            box-shadow: 0px 4px 8px #00000066;
            backdrop-filter: blur(5px);
            border-radius: 0 0 10px 10px;
        }


        #msform fieldset:not(:first-of-type) {
            display: none
        }

        fieldset {
            text-align: right;
            height: 100%;
        }

        .card {
            z-index: 0;
            border: none;
            position: relative
        }

        .fs-title {
            font-size: 25px;
            color: #673AB7;
            margin-bottom: 15px;
            font-weight: normal;
            text-align: left
        }

        .purple-text {
            color: #673AB7;
            font-weight: normal
        }

        .steps {
            font-size: 25px;
            color: gray;
            margin-bottom: 10px;
            font-weight: normal;
            text-align: right
        }

        .fieldlabels {
            color: gray;
            text-align: left
        }

        #progressbar {
            margin-bottom: 0;
            overflow: hidden;
            color: lightgrey;
            display: flex;
            width: 100%;
            gap: 20px;
            justify-content: space-between;
            /* padding: 0 30px; */
        }



        #progressbar li {
            list-style-type: none;
            font-size: 15px;
            width: 16%;
            float: left;
            position: relative;
            font-weight: 400;
            height: auto;
        }

        .progress {
            height: 20px;
            position: relative;
        }

        .progress-bar {
            background-color: #673AB7
        }

        .fit-image {
            width: 100%;
            object-fit: cover
        }

        .menu__item {
            all: unset;
            flex-grow: 1;
            display: flex;
            cursor: pointer;
            /* overflow: hidden; */
            padding-top: 0;
            position: relative;
            align-items: center;
            color: var(--color-1);
            justify-content: center;
            transition: flex-grow var(--duration) var(--cubic);
            /* margin: auto; */
            /* width: 100%; */
            text-align: center;
            /* height: 100%; */
            padding: 10px;
            border-radius: 50px;
        }

        .menu__icon {

            font-size: 1.05em;
            stroke: currentColor;
            transition: transform var(--duration) var(--cubic);

        }

        .menu__item::before {
            top: 9px;
            left: calc(50% - 13px);
            width: 18px;
            height: 18px;
            content: " ";
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            transform-origin: center;
            background-color: var(--color6);
            transition: transform var(--duration) var(--cubic);
        }

        .menu__item::after {

            left: calc(50% - 31px);
            bottom: 0;
            content: " ";
            height: 0.25em;
            position: absolute;
            border-radius: 2em;
            transform-origin: left center;
            background-color: currentColor;
            width: var(--lineWidth);
            transform: translate3d(3em, 0, 0) scaleX(0);
            transition: transform calc(var(--duration) + .2s) var(--cubic);

        }


        .menu__text {
            left: calc(50% + 13px);
            font-size: 13px;
            position: absolute;
            text-transform: capitalize;
            letter-spacing: .01em;
            transform: translate3d(0, 200%, 0);
            transition: transform calc(var(--duration) / 3.7);
            text-align: left;
        }

        .active .menu__item {
            flex-grow: 2.7;
            color: var(--color3);
            /* background: green; */
            background: #5975a585;
            backdrop-filter: blur(10px);
        }

        .menu__item svg g {
            fill: #d5dadd;
        }

        .active .menu__item svg g {
            fill: var(--color3);
            ;
        }

        .active .menu__item .menu__icon {
            transform: translate3d(-43%, 0, 0);
        }

        .active .menu__item::before {

            transform: scale(1);

        }

        .active .menu__item::after {
            transform: translate3d(3em, -9px, 0) scaleX(1);
            transition: transform var(--duration) var(--cubic);
        }

        .active .menu__text {

            transform: translate3d(0, 0, 0);
            transition: transform calc(var(--duration) / 1.5);
        }

        .icon {

            --duration-icon: 1s;

            fill: none;
            width: 30px;
            height: 30px;
            display: block;
            stroke-width: 15;
            stroke-miterlimit: 10;

        }


        .active #home-anm {

            animation: home var(--duration-icon);
        }

        @keyframes home {

            25% {

                transform: translate3d(0, -.8em, 0);

            }

            50% {

                transform: translate3d(0, .5em, 0);

            }


        }

        #strategy-anm {

            transform: scaleX(.85);
            transform-origin: center;

        }

        .active #strategy-anm {

            animation: strategy var(--duration-icon);

        }

        @keyframes strategy {

            50% {

                transform: scaleX(1);

            }

            100% {

                transform: scaleX(.85);

            }

        }

        .active #strategy-cir1 {

            animation: strategy-cir1 var(--duration-icon);
        }

        .active #strategy-cir2 {

            animation: strategy-cir2 var(--duration-icon) .1s;
        }

        .active #strategy-cir3 {

            animation: strategy-cir3 var(--duration-icon) .2s;
        }

        @keyframes strategy-cir1 {

            50% {

                transform: translate3d(-.7em, -0.7em, 0);

            }

            100% {

                transform: translate3d(0, 0, 0);


            }

        }

        @keyframes strategy-cir2 {

            35% {

                transform: translate3d(0, -0.7em, 0);

            }

            100% {

                transform: translate3d(0, 0, 0);


            }

        }

        @keyframes strategy-cir3 {

            35% {

                transform: translate3d(.7em, -0.7em, 0);

            }

            100% {

                transform: translate3d(0, 0, 0);


            }

        }


        .active #period-anm {

            transform-origin: center 100%;
            animation: period var(--duration-icon);

        }

        .active #period-cir {

            transform-origin: center;
            animation: period-cir var(--duration-icon);

        }

        .active #period-line {

            stroke-dasharray: 66;
            animation: period-line calc(var(--duration-icon) / 2.5) reverse;

        }


        @keyframes period {

            35% {

                transform: scaleY(.85);

            }

            60%,
            70% {

                transform: scaleY(1.2);

            }

            100% {

                transform: scaleY(1);

            }

        }

        @keyframes period-cir {

            0% {

                opacity: 0;

            }

            35% {

                opacity: 1;
                transform: translate3d(15%, -55%, 0);

            }

            60% {

                opacity: 0;
                transform: translate3d(-8%, -50%, 0);

            }

        }

        @keyframes period-line {


            100% {

                stroke-dashoffset: 66;

            }

        }


        .active #security-cir {

            transform-box: fill-box;
            transform-origin: center;
            animation: security-cir calc(var(--duration-icon) / 1.5);

        }

        @keyframes security-cir {


            0% {

                transform: scale(0);

            }

            100% {

                transform: scale(1);

            }

        }

        .active #security-strok {

            stroke-dasharray: 96;
            animation: security-strok calc(var(--duration-icon) / 1.2);

        }

        @keyframes security-strok {


            0% {

                stroke-dashoffset: 60;

            }

            100% {

                stroke-dashoffset: 230;

            }

        }

        .active #settings-anm {

            transform-box: fill-box;
            transform-origin: center;
            animation: settings-anm calc(var(--duration-icon) / 1.5);

        }

        @keyframes settings-anm {


            0% {

                transform: rotate(-60deg);

            }

            50% {

                transform: rotate(60deg);

            }

        }



        .odc-header {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin: 13.5px 0;
        }

        .cta-text {
            margin-top: 40px;
            margin-right: 25px;
            color: white;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.18);
        }

        .cta-button {
            font-size: 14px;
            padding: 14px 22px;
            /* border: 1px solid #ffffff; */
            display: inline-block;
            color: #000000;
            border-radius: 10px;
            background: #faba1b;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.10), 0 6px 6px rgba(0, 0, 0, 0.05);
            animation: shadow-pulse 1s infinite;
            cursor: pointer;
            border: none;
        }

        @keyframes shadow-pulse {
            0% {
                box-shadow: 0 0 0 0px #faba1b6e;
            }

            100% {
                box-shadow: 0 0 0 35px rgba(255, 255, 255, 0);
            }
        }

        .cta-button:focus {
            outline: none;
        }


        .odc-wrapper {
            margin: 30px;
            overflow: auto;
            height: calc(100% - 75px);
        }

        .odc-header-line {
            margin-top: 30px;
            color: white;
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 30px;
        }

        .odc-header-details {
            color: white;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px;
        }

        .product {
            background: #a73a33;
            padding: 20px;
            border-radius: 10px;
        }

        .product table {
            width: 100%;
        }

        .product table tr td {
            color: white;
            width: 50%;
            padding: 1px;
            vertical-align: top;
        }

        .product table tr td span {
            float: right;
        }

        .product table tr td p {
            margin-bottom: 5px;
        }

        .product table tr td:first-child {
            padding-right: 16px;
        }

        .frm-inner {
            height: calc(100% - (calc(var(--h) * 2)));
            text-align: left;
        }



        .action-button {
            --_c: #4d2d2d;
            flex: calc(1.25 + var(--_s, 0));
            min-width: 0;
            font-size: 18px;
            height: var(--h);
            cursor: pointer;
            color: var(--_c);
            border: var(--b) solid var(--_c);
            background: conic-gradient(at calc(100% - 1.3* var(--b)) 0, var(--_c) 209deg, #0000 211deg) border-box;
            clip-path: polygon(0 0, 100% 0, calc(100% - 0.577* var(--h)) 100%, 0 100%);
            padding: 0 calc(0.288* var(--h)) 0 0;
            margin: 0 calc(-0.288* var(--h)) 0 0;
            box-sizing: border-box;
            transition: .5s;
            border-radius: 0 !important;
            padding: 0 30px !important;
            line-height: 35px !important;
            border-top-left-radius: 50px !important;
            border-bottom-left-radius: 50px !important;
            position: relative;
            margin: calc(var(--h) / 2) 0;
        }

        .action-button+.action-button {
            --_c: #4d2d2d;
            flex: calc(.95 + var(--_s, 0));
            background:
                conic-gradient(from -90deg at calc(1.3*var(--b)) 100%, var(--_c) 119deg, #0000 121deg) border-box;
            clip-path: polygon(calc(0.577*var(--h)) 0, 100% 0, 100% 100%, 0 100%);
            margin-left: -20px;
            padding: 0 0 0 calc(0.288*var(--h));
            border-top-left-radius: 0px !important;
            border-bottom-left-radius: 0px !important;
            border-top-right-radius: 50px !important;
            border-bottom-right-radius: 50px !important;
        }

        .action-buttonfocus-visible {
            margin: 0;
            padding: 0;
        }

        .action-button:focus-visible+.action-button {
            color: var(--blue);
            margin: 0;

        }

        .action-button:has(+ .action-button:focus-visible) {
            color: var(--blue);
            margin: 0;

        }

        .action-button:hover,
        .action-button:active:not(:focus-visible) {
            --_s: 1;
            background: #fff;
            color: var(--blue);
        }

        .action-button:active {

            color: var(--blue);
        }

        .action-button.pad-active {
            padding: 0 40px !important;
            transition: .5s;
        }

        .action-button.pad-deactive {
            padding: 0 20px !important;
            transition: .5s;
        }

        header {
            height: var(--header-height);
            position: fixed;
            left: calc(calc(var(--fx-position) * 2) + var(--frm-lft-width));
            width: calc(100% - (calc(calc(var(--fx-position) * 2) + var(--frm-lft-width))) - var(--fx-position));
            top: var(--fx-position);
            /* background: red; */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #msform {
            position: fixed;
            left: calc(calc(var(--fx-position) * 2) + var(--frm-lft-width));
            width: calc(100% - (calc(calc(var(--fx-position) * 2) + var(--frm-lft-width))) - var(--fx-position));
            top: calc(calc(var(--fx-position) * 2) + var(--header-height));
            height: calc(100vh - (calc((var(--fx-position) * 2) + var(--header-height) + var(--bottom-height))));

        }

        .addForm {
            position: fixed;
            left: calc(calc(var(--fx-position) * 2) + var(--frm-lft-width));
            width: calc(100% - (calc(calc(var(--fx-position) * 2) + var(--frm-lft-width))) - var(--fx-position));
            top: calc(calc(var(--fx-position) * 2) + var(--header-height));
            height: calc(100vh - (calc((var(--fx-position) * 2) + var(--header-height) + var(--bottom-height))));

        }

        .order-details-container {
            position: relative;
            z-index: 6;
            background-color: #d9202736;
            border-top-right-radius: 50px;
            border-top-left-radius: 50px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
            cursor: default;
            position: fixed;
            width: 100%;
            left: 0;
            backdrop-filter: blur(10px);
            bottom: 0;
            height: var(--bottom-height);
            transition: .5s;
        }

        .order-details-container.active {
            height: calc(100vh - var(--fx-position));
            transition: .5s;
        }

        .cta-button i,
        .order-details-container.active .cta-button span {
            display: none;
        }

        .order-details-container.active .cta-button i {
            display: block;
        }

        .frm-lft {
            position: fixed;
            top: var(--fx-position);
            left: var(--fx-position);
            padding: 30px;
            height: calc(100vh - (calc(var(--fx-position) * 2 + var(--bottom-height))));
            background: #2e151187;
            border-radius: 10px;
            /* border: 1px solid #2a0f1f; */
            overflow: hidden;
            backdrop-filter: blur(5px);
            width: var(--frm-lft-width);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .frm-lft .frm-logo {
            width: 100%;
            display: inline-block;
        }

        .frm-lft .frm-logo img {
            width: 100%;
        }

        .frm-lft p {
            color: white;
            font-size: 26px;
            margin-top: 25px;
            font-weight: 600;
            background: linear-gradient(to bottom, #fde7fc 0%, #f7ac88 100%) !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent;
            font-family: "Playfair Display", serif;
        }

        .frm-lft h3 {
            font-size: 64px;
            margin-top: 18px;
            color: #cbc3ba00;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 18px;
            background: linear-gradient(to right, #fde7fc 0%, #f7ac88 100%) !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent;
            font-family: "Playfair Display", serif;
            overflow: visible;

        }

        .frm-lft h3+h3 {
            margin-top: -32px;
            line-height: 71px;
        }

        .frm-lft h4 {
            color: white;
            font-size: 19px;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: #724056c7;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: white;
        }



        ::-webkit-scrollbar-track {
            background: #724056;
            border-radius: 10px;
        }

        @media only screen and (min-width: 1201px) and (max-width: 1600px) {
            .cus-con {
                padding: 10px 19px;
                padding-left: 48px;
            }

            .checkmark {
                left: 13px;
            }
        }

        .button-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 25px;
            /* height: 100%; */
        }

        .glass-btn img {
            width: 48px;
            filter: drop-shadow(2px 4px 6px #00000082);
        }

        .glass-btn {
            width: 100%;
            height: 100px;
            background-color: #fafafa33;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            margin-left: 0;
            margin-right: 0;
            cursor: pointer;
            transition: all 0.3s;

            &.blue-btn {
                -webkit-box-shadow: 0px 20px 20px -17px rgba(0, 111, 255, 0.53);
                -moz-box-shadow: 0px 20px 20px -17px rgba(0, 111, 255, 0.53);
                box-shadow: 0px 20px 20px -17px rgba(0, 111, 255, 0.53);

                .content {
                    font-size: 4em;
                    font-family: "Roboto", sans-serif;
                    background: linear-gradient(to right, #006fff, #00acff 70%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 0px 10px 37px rgba(0, 111, 255, 0.8);
                }
            }

            &.red-btn {
                -webkit-box-shadow: 0px 20px 20px -17px rgba(255, 16, 39, 0.5);
                -moz-box-shadow: 0px 20px 20px -17px rgba(255, 16, 39, 0.5);
                box-shadow: 0px 20px 20px -17px rgb(49 138 221 / 69%);

                .content {
                    font-size: 4em;
                    font-family: "Roboto", sans-serif;
                    background: linear-gradient(to right, #ff1027, #ff4f06 70%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 0px 10px 37px rgba(255, 16, 39, 0.6);
                }
            }

            &.amber-btn {
                -webkit-box-shadow: 0px 20px 20px -17px rgba(255, 159, 16, 0.53);
                -moz-box-shadow: 0px 20px 20px -17px rgba(255, 159, 16, 0.53);
                box-shadow: 0px 20px 20px -17px rgb(41 171 226 / 70%);

                .content {
                    font-size: 4em;
                    font-family: "Roboto", sans-serif;
                    background: linear-gradient(to right, #ff8d00, #f7ad07 70%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 0px 10px 37px rgba(255, 94, 16, 0.7);
                }
            }

            &.grn-btn {
                -webkit-box-shadow: 0px 20px 20px -17px rgba(255, 159, 16, 0.53);
                -moz-box-shadow: 0px 20px 20px -17px rgba(255, 159, 16, 0.53);
                box-shadow: 0px 20px 20px -17px rgb(0 209 179 / 43%);

                .content {
                    font-size: 4em;
                    font-family: "Roboto", sans-serif;
                    background: linear-gradient(to right, #ff8d00, #f7ad07 70%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 0px 10px 37px rgba(255, 94, 16, 0.7);
                }
            }

            &:hover {
                background-color: darken(#fafafa, 2%);
                transform: translateY(-5px);
                transition: all 0.3s;
            }
        }

        .frm-lft-btn {
            display: none;
        }

        @media only screen and (min-width: 1201px) and (max-width: 1500px) {

            .col-lg-1,
            .col-lg-10,
            .col-lg-11,
            .col-lg-12,
            .col-lg-2,
            .col-lg-3,
            .col-lg-4,
            .col-lg-5,
            .col-lg-6,
            .col-lg-7,
            .col-lg-8,
            .col-lg-9,
            .col-md-1,
            .col-md-10,
            .col-md-11,
            .col-md-12,
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-5,
            .col-md-6,
            .col-md-7,
            .col-md-8,
            .col-md-9,
            .col-sm-1,
            .col-sm-10,
            .col-sm-11,
            .col-sm-12,
            .col-sm-2,
            .col-sm-3,
            .col-sm-4,
            .col-sm-5,
            .col-sm-6,
            .col-sm-7,
            .col-sm-8,
            .col-sm-9,
            .col-xs-1,
            .col-xs-10,
            .col-xs-11,
            .col-xs-12,
            .col-xs-2,
            .col-xs-3,
            .col-xs-4,
            .col-xs-5,
            .col-xs-6,
            .col-xs-7,
            .col-xs-8,
            .col-xs-9 {
                padding-right: 8px;
                padding-left: 8px;
            }

            .sub-heading {
                margin: -14px -8px;
            }
        }

        @media only screen and (min-width: 1201px) and (max-width: 1440px) {
            body {
                --frm-lft-width: 390px;
                --fx-position: 26px;
            }

            .cus-con {
                padding-left: 43px;
            }

            .frm-lft p {
                font-size: 22px;
                margin-top: 12px;
            }

            .frm-lft h3 {
                font-size: 53px;
                margin-top: 10px;
            }

        }

        @media only screen and (min-width: 1201px) and (max-width: 1400px) {
            body {
                --frm-lft-width: 365px;
                --fx-position: 15px;
            }

            .frm-lft p {
                font-size: 22px;
                margin-top: 12px;
            }

            .frm-lft h3 {
                font-size: 58px;
                margin-top: 10px;
            }
        }

        @media only screen and (min-width: 1201px) and (max-width: 1366px) {
            body {
                --frm-lft-width: 359px;
            }

            .cus-con {
                padding: 10px 15px;
                padding-left: 40px;
            }

            .checkmark {
                left: 11px;
            }

            .frm-lft p {
                font-size: 22px;
                margin-top: 12px;
            }

            .frm-lft h3 {
                font-size: 57px;
                margin-top: 10px;
            }


        }

        @media only screen and (min-width: 1201px) and (max-width: 1280px) {
            body {
                --frm-lft-width: 316px;
                --fx-position: 15px;
            }

            .frm-lft p {
                font-size: 20px;
                margin-top: 12px;
            }

            .frm-lft h3 {
                font-size: 48px;
                margin-top: 10px;
            }

            .frm-lft h4 {
                font-size: 18px;
            }
        }

        @media only screen and (min-width: 1001px) and (max-width: 1200px) {
            body {
                --frm-lft-width: 233px;
                --fx-position: 9px;
            }
        }

        @media only screen and (min-width: 300px) and (max-width: 1000px) {
            body {
                --fx-position: 5px;
            }

            .banner-wrap {
                padding: 15px;
                flex-direction: column;
                align-items: center;
            }

            .frm-num {
                display: flex;
                align-items: center;
                gap: 7px;
            }

            .frm-num p {
                color: white;
                margin: 0;
            }

            .frm-num span {
                font-size: 17px;
                width: 35px;
                height: 35px;
            }

            .heading {
                gap: 15px;
            }

            .heading h1 {
                font-size: 24px;
                text-align: center;
                justify-content: center;
            }

            .cus-con {
                width: 100%;
                font-size: 13px;
            }

            .check-in-out {
                gap: 0;
                flex-direction: column;
            }

            .formbold-form-wrapper {
                float: left;
                padding: 15px;
                padding-top: 0;
            }

            .sub-heading {
                padding: 14px 19px;
                margin: -14px 0;
                margin-bottom: 13px;
            }

            .personal_info {
                padding: 14px 0;
            }

            .table-wrap {
                padding: 3px 15px;

            }

            .theader td:first-child {
                min-width: 200px;
            }

            .page-heading {
                padding: 15px 0;
                font-size: 24px;
            }

            .formbold-form-label {
                padding-top: 3px;
            }

            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate {
                position: sticky;
                left: 0;
            }

            .dataTables_wrapper {
                overflow: auto;
            }

            .icon {
                width: 21px;
                height: 21px;
            }

            #progressbar {
                gap: 4px;
            }

            header {
                width: 100%;
                padding: 0 5px;
                left: 0;
            }

            #progressbar li {
                width: 13%;
            }

            .menu__text {
                display: none;
            }

            .frm-lft {
                left: -100%;
                width: 90%;
                z-index: 1;

            }

            .frm-lft-active {
                left: 1%;
            }

            .odc-wrapper {
                margin: 5px;
            }

            .odc-header-line {
                margin-bottom: 12px;
            }

            .product-container {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            #msform {
                left: 0;
                width: 100%;
                padding: 0 5px;
            }

            .addForm {
                left: 0;
                width: 100%;
                padding: 0 5px;
            }

            .frm-lft p {
                font-size: 16px;
                margin-top: 14px;
            }

            .frm-lft h3 {
                font-size: 50px;
                margin-top: 3px;
            }

            .frm-lft h4 {
                font-size: 16px;
            }

            .frm-lft-btn {
                position: fixed;
                bottom: calc(var(--bottom-height) + 21px);
                background: var(--color4);
                border: 0;
                color: white;
                width: 41px;
                height: 41px;
                font-size: 19px;
                border-top-right-radius: 32px;
                border-bottom-right-radius: 32px;
                display: block;
            }

            .frm-btn-active {
                left: 91%;
                width: 27px;
                font-size: 14px;
            }
        }
    </style>

    <style>
        .error_msg {
            color: #ff0000;
            background: #edb5b5;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .success_msg {
            color: #075803;
            background: #e2ffdb;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _DIR_CM_JSCRIPT_ . "website/" ?>login.js?x=<?php echo rand(0, 100) ?>"></script>
    <!-- <script src="<?= _BASE_URL_ ?>js/website/returnData.process.js"></script> -->
</head>

<body>
    <?php
    $sqlSuccessImg    =   array();
    $sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Profile Page Background Image' AND status='A' ";

    $resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
    $resultSuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image']; ?>
    <?php if (pathinfo($resultSuccessImg, PATHINFO_EXTENSION) != 'mp4') { ?>
        <img class="login-back" src="<?php echo $resultSuccessImg; ?>" style="width:100%">
    <?php } else { ?>
        <video autoplay="" loop="" playsinline="" muted="">
            <source src="<?= $resultSuccessImg ?>" type="video/mp4">
        </video>

    <?php } ?>
    <!-- <video autoplay="" loop="" playsinline="" muted="">
        <source src="Y2meta.app-Sunset 3D Waves Live wallpaper-(480p).mp4" type="video/mp4">
    </video> -->
    <header>
        <ul id="progressbar">
            <li class="active" id="account" section='1'>
                <button class="menu__item">
                    <div class="menu__icon">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                            <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                <path d="M1887 5110 c-289 -52 -520 -225 -651 -487 -53 -106 -77 -201 -83 -330 -16 -307 109 -576 347 -753 476 -352 1154 -147 1351 410 39 110 53 212 46 343 -6 129 -30 224 -83 330 -117 232 -305 390 -551 463 -97 28 -286 41 -376 24z m262 -325 c207 -49 378 -222 420 -423 66 -321 -147 -626 -469 -671 -229 -31 -457 85 -564 288 -47 89 -66 167 -66 266 0 154 54 281 164 390 140 138 330 193 515 150z" />
                                <path d="M1290 3280 c-400 -49 -736 -306 -885 -675 -71 -179 -70 -160 -70 -855 l0 -625 27 -57 c51 -110 162 -194 288 -218 33 -6 373 -10 900 -10 l847 0 18 -47 c186 -495 688 -820 1220 -789 441 25 844 294 1035 690 84 175 120 335 120 536 0 336 -120 630 -355 865 -181 181 -401 298 -649 344 l-89 16 -28 81 c-123 369 -424 640 -804 727 -86 20 -123 21 -800 23 -390 1 -739 -2 -775 -6z m1543 -338 c155 -43 315 -148 405 -264 45 -58 122 -199 122 -223 0 -8 -17 -16 -42 -20 -75 -13 -200 -56 -290 -100 -312 -155 -548 -433 -643 -759 -32 -112 -45 -195 -48 -316 l-3 -95 -464 0 -465 0 -5 470 c-5 466 -5 470 -27 501 -65 91 -216 81 -271 -19 -15 -29 -17 -78 -20 -494 l-3 -463 -185 0 c-180 0 -217 5 -236 34 -4 6 -8 259 -8 562 0 517 1 556 20 630 76 302 325 527 635 573 33 5 373 8 755 7 681 -2 697 -2 773 -24z m962 -832 c326 -86 593 -366 659 -692 20 -97 20 -281 1 -372 -123 -582 -758 -893 -1290 -631 -521 257 -672 930 -309 1383 135 169 331 287 541 327 107 20 292 13 398 -15z" />
                                <path d="M3480 1887 c-19 -12 -43 -38 -54 -57 -20 -33 -21 -50 -21 -380 0 -345 0 -345 24 -383 45 -73 142 -98 214 -54 79 48 77 38 77 437 0 337 -1 357 -20 388 -44 72 -151 96 -220 49z" />
                                <path d="M3493 849 c-109 -54 -116 -207 -13 -276 43 -30 121 -31 168 -3 102 63 95 228 -13 280 -53 26 -89 25 -142 -1z" />
                            </g>
                        </svg>

                    </div>
                    <strong class="menu__text active">Info</strong>
                </button>
            </li>
            <?php if (sizeof($workshopDetailsArray) > 0) { ?>
                <li id="personal">
                    <button class="menu__item">
                        <div class="menu__icon">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">

                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M780 4887 c-117 -40 -204 -119 -257 -234 -22 -47 -28 -76 -31 -153 -4 -108 14 -177 67 -255 30 -45 30 -45 8 -50 -196 -43 -234 -60 -292 -128 -63 -74 -65 -89 -65 -587 l0 -446 25 -50 c39 -79 101 -114 201 -114 l54 0 0 -558 c0 -555 0 -559 21 -578 20 -18 41 -19 404 -19 363 0 384 1 404 19 21 19 21 23 21 578 l0 558 59 0 c88 0 156 39 194 110 l28 54 -3 465 -3 466 -27 51 c-47 90 -108 131 -236 159 -37 9 -77 18 -89 20 -23 5 -23 6 11 58 50 76 69 152 64 257 -3 73 -9 98 -36 152 -17 36 -49 85 -71 109 -107 121 -295 170 -451 116z m270 -153 c213 -116 197 -424 -27 -521 -50 -22 -166 -22 -216 0 -107 46 -177 150 -177 262 0 135 69 233 197 281 60 23 159 13 223 -22z m-269 -660 c61 -23 199 -23 264 -1 50 16 51 16 198 -17 159 -36 191 -51 219 -107 16 -31 18 -75 18 -466 0 -517 12 -473 -134 -473 -77 0 -98 -3 -117 -19 l-24 -19 -3 -561 -2 -562 -108 3 -107 3 -5 563 c-5 543 -6 564 -24 578 -40 29 -88 12 -105 -37 -7 -22 -11 -203 -11 -570 l0 -539 -105 0 -105 0 -2 561 -3 561 -24 19 c-19 16 -40 19 -117 19 -146 0 -134 -44 -134 473 0 475 0 475 62 520 20 15 251 77 319 86 4 1 26 -6 50 -15z" />
                                    <path d="M1882 4894 c-21 -15 -22 -20 -22 -203 0 -221 0 -221 93 -221 l57 0 0 -1020 c0 -1007 0 -1020 20 -1040 20 -20 33 -20 650 -20 l630 0 0 -119 c0 -115 -1 -119 -23 -125 -30 -7 -84 -61 -108 -108 -38 -74 -20 -189 39 -251 103 -109 283 -90 357 37 67 114 27 258 -87 317 l-38 19 0 115 0 115 634 0 635 0 20 26 c21 27 21 30 21 1040 l0 1014 55 0 c92 0 95 6 95 218 0 174 -1 179 -23 200 l-23 22 -1480 0 c-1323 0 -1482 -2 -1502 -16z m2888 -204 l0 -80 -1385 0 -1385 0 0 80 0 80 1385 0 1385 0 0 -80z m-150 -1190 l0 -970 -1235 0 -1235 0 0 970 0 970 1235 0 1235 0 0 -970z m-1179 -1499 c48 -48 36 -111 -26 -137 -58 -24 -115 14 -115 76 0 82 84 118 141 61z" />
                                    <path d="M3216 4229 c-24 -19 -26 -27 -26 -90 l0 -69 -37 -15 -37 -16 -47 47 c-75 74 -88 71 -221 -64 -128 -128 -129 -134 -52 -213 l45 -45 -16 -37 -15 -37 -69 0 c-63 0 -71 -2 -90 -26 -19 -24 -21 -40 -21 -169 0 -180 3 -186 109 -194 70 -6 73 -7 86 -37 13 -31 12 -33 -22 -65 -52 -50 -67 -85 -53 -119 6 -15 56 -71 109 -124 119 -116 133 -120 210 -50 50 45 51 45 86 33 l35 -13 0 -68 c0 -106 4 -108 194 -108 l155 0 20 26 c16 21 21 41 21 90 l0 62 35 12 c35 12 37 11 85 -34 73 -69 83 -66 211 61 130 128 133 138 63 213 -45 48 -46 50 -34 85 l12 35 62 0 c49 0 69 5 90 21 l26 20 0 159 c0 188 -1 190 -107 190 -62 0 -64 1 -80 34 l-16 35 46 51 c71 79 68 91 -54 216 -56 56 -112 105 -125 108 -30 8 -58 -6 -109 -54 -39 -36 -41 -37 -72 -24 -32 14 -33 15 -33 79 0 57 -3 69 -25 90 -23 24 -29 25 -168 25 -131 0 -147 -2 -171 -21z m224 -175 c0 -74 10 -89 72 -110 29 -9 74 -28 99 -41 54 -28 69 -24 124 28 l41 39 39 -40 39 -40 -42 -43 c-23 -23 -42 -52 -42 -64 0 -12 13 -50 30 -85 16 -34 30 -71 30 -81 0 -10 11 -29 25 -42 20 -21 34 -25 80 -25 l55 0 0 -55 0 -55 -58 0 c-70 0 -81 -9 -113 -95 -12 -33 -28 -69 -35 -80 -25 -35 -17 -72 26 -118 l39 -43 -36 -37 -37 -36 -43 39 c-46 43 -83 51 -118 26 -11 -7 -47 -23 -80 -35 -86 -32 -95 -43 -95 -113 l0 -58 -55 0 -55 0 0 55 c0 46 -4 60 -25 80 -13 14 -32 25 -42 25 -10 0 -47 14 -81 30 -35 17 -73 30 -85 30 -12 0 -41 -19 -64 -42 l-43 -42 -40 39 -39 39 44 47 c25 27 45 54 45 61 0 8 -9 32 -20 53 -11 22 -30 65 -41 95 -29 77 -33 80 -105 80 l-64 0 0 55 0 55 54 0 c66 0 97 22 115 82 7 24 24 65 38 91 29 57 25 77 -25 130 l-36 37 36 37 37 36 39 -35 c55 -50 72 -53 129 -25 26 14 67 31 91 38 60 18 82 49 82 115 l0 54 55 0 55 0 0 -56z" />
                                    <path d="M3329 3819 c-103 -15 -198 -90 -244 -192 -24 -51 -27 -68 -23 -140 6 -135 68 -230 187 -286 81 -38 193 -37 272 2 66 32 132 99 162 165 33 71 31 188 -4 262 -14 31 -40 72 -57 91 -64 73 -186 114 -293 98z m166 -173 c115 -88 90 -263 -47 -321 -51 -21 -94 -19 -145 6 -62 30 -95 78 -101 144 -6 69 1 96 35 136 48 56 80 70 154 67 54 -3 73 -9 104 -32z" />
                                    <path d="M811 1534 c-125 -33 -228 -118 -282 -231 -28 -58 -34 -82 -37 -159 -5 -109 12 -178 62 -253 19 -28 31 -52 28 -54 -4 -2 -47 -12 -95 -22 -111 -23 -180 -63 -224 -128 -46 -69 -53 -103 -53 -278 0 -150 1 -156 23 -177 30 -28 68 -28 95 1 21 22 22 33 22 173 0 227 8 236 237 288 148 33 153 33 195 18 58 -22 208 -22 266 0 41 15 47 15 195 -18 229 -51 237 -61 237 -290 0 -144 1 -151 23 -172 30 -29 62 -28 92 3 24 23 25 28 25 177 0 172 -7 206 -53 275 -44 65 -113 105 -224 128 -48 10 -91 20 -95 22 -4 2 6 22 22 45 41 56 70 155 70 238 0 160 -85 303 -222 375 -101 53 -206 66 -307 39z m216 -149 c63 -26 111 -73 144 -140 28 -58 31 -71 27 -137 -6 -118 -55 -194 -161 -250 -33 -18 -59 -23 -122 -23 -70 0 -87 4 -134 30 -99 55 -151 142 -151 255 0 67 13 110 51 166 72 106 224 150 346 99z" />
                                    <path d="M2470 1540 c-202 -43 -354 -246 -337 -450 6 -75 36 -161 72 -210 14 -19 22 -37 18 -41 -5 -4 -44 -14 -88 -24 -44 -9 -94 -23 -111 -30 -50 -21 -115 -86 -140 -140 -22 -46 -24 -63 -24 -227 0 -171 1 -177 22 -192 30 -21 61 -20 89 3 23 19 24 26 29 189 5 165 6 171 30 197 37 40 51 45 209 80 138 31 147 32 185 17 55 -21 217 -21 272 0 38 15 47 14 185 -17 158 -35 172 -40 209 -80 24 -26 25 -32 30 -197 5 -163 6 -170 29 -189 30 -25 66 -24 91 1 18 18 20 33 20 193 0 159 -2 176 -24 222 -25 54 -90 119 -140 140 -17 7 -67 21 -111 30 -44 10 -83 20 -87 24 -4 3 9 31 27 61 138 222 34 528 -211 619 -65 25 -179 34 -244 21z m207 -160 c131 -58 199 -205 158 -345 -23 -80 -107 -166 -185 -189 -116 -34 -206 -12 -291 73 -46 46 -61 70 -74 116 -69 237 166 445 392 345z" />
                                    <path d="M4103 1535 c-101 -28 -181 -82 -244 -165 -102 -136 -106 -349 -9 -487 16 -23 26 -44 22 -46 -4 -2 -47 -12 -95 -22 -111 -23 -180 -63 -224 -128 -46 -69 -53 -103 -53 -275 0 -149 1 -154 25 -177 30 -31 65 -32 93 -2 21 22 22 33 22 173 0 227 8 236 237 288 148 33 153 33 195 18 58 -22 208 -22 266 0 41 15 47 15 195 -18 229 -51 237 -61 237 -290 0 -144 1 -151 23 -172 30 -28 68 -28 95 1 21 22 22 33 22 178 0 173 -7 207 -53 276 -44 65 -113 105 -224 128 -48 10 -91 20 -95 22 -3 2 8 25 26 51 51 76 69 148 64 255 -3 78 -9 102 -37 160 -88 183 -298 283 -488 232z m214 -150 c102 -43 173 -150 173 -260 0 -118 -50 -204 -151 -260 -47 -26 -64 -30 -134 -30 -71 0 -86 4 -135 31 -157 89 -198 283 -91 433 67 94 224 134 338 86z" />
                                </g>
                            </svg>

                        </div>
                        <strong class="menu__text">Workshop</strong>
                    </button>
                </li>
            <?php }

            ?>
            <li id="accmp">
                <button class="menu__item">
                    <div class="menu__icon">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">

                            <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                <path d="M2430 5104 c-213 -46 -399 -229 -451 -444 -20 -84 -16 -222 9 -305 56 -182 195 -329 378 -397 50 -19 79 -22 194 -22 115 0 144 3 194 22 186 69 327 219 382 407 21 73 24 218 5 295 -52 218 -239 399 -456 445 -86 18 -169 18 -255 -1z m222 -169 c260 -55 405 -348 293 -592 -29 -63 -111 -154 -170 -187 -138 -76 -292 -76 -430 0 -59 33 -141 124 -170 187 -111 241 33 536 288 592 81 17 108 18 189 0z" />
                                <path d="M795 4264 c-290 -44 -491 -308 -446 -585 45 -283 323 -481 596 -426 334 68 514 424 366 722 -84 170 -240 275 -426 289 -33 2 -73 2 -90 0z m200 -204 c74 -34 130 -88 164 -160 22 -47 26 -68 26 -150 0 -83 -4 -101 -27 -145 -36 -70 -92 -123 -166 -157 -123 -59 -274 -34 -371 61 -87 84 -121 189 -101 302 15 80 40 130 93 183 102 102 250 127 382 66z" />
                                <path d="M4205 4264 c-64 -10 -137 -36 -194 -69 -231 -135 -319 -424 -202 -661 74 -149 202 -247 367 -281 334 -68 650 239 595 578 -30 192 -179 362 -361 413 -61 17 -159 26 -205 20z m165 -186 c80 -23 162 -94 203 -173 43 -86 41 -218 -5 -309 -78 -154 -283 -223 -440 -148 -74 34 -130 87 -166 157 -23 44 -27 62 -27 145 0 82 4 103 26 150 73 153 242 227 409 178z" />
                                <path d="M2178 3830 c-281 -49 -512 -250 -607 -528 l-26 -77 -3 -802 -3 -803 171 0 170 0 0 -810 0 -810 85 0 85 0 0 980 0 980 -85 0 -85 0 0 -85 0 -85 -85 0 -86 0 3 708 3 707 23 62 c72 192 228 332 428 383 69 18 111 20 394 20 283 0 325 -2 394 -20 200 -51 356 -191 428 -383 l23 -62 3 -707 3 -708 -86 0 -85 0 0 85 0 85 -85 0 -85 0 0 -980 0 -980 85 0 85 0 0 810 0 810 170 0 171 0 -3 803 -3 802 -26 77 c-86 251 -262 422 -524 509 -67 22 -84 23 -435 25 -201 1 -386 -1 -412 -6z" />
                                <path d="M504 3149 c-241 -40 -440 -229 -489 -464 -12 -57 -15 -189 -15 -737 l0 -668 170 0 170 0 0 -640 0 -640 85 0 85 0 0 810 0 810 -85 0 -85 0 0 -85 0 -85 -85 0 -85 0 0 568 c0 619 3 658 57 749 65 113 186 195 315 213 35 5 235 10 446 10 l383 0 -3 83 -3 82 -405 1 c-223 1 -428 -2 -456 -7z" />
                                <path d="M3757 3154 c-4 -4 -7 -43 -7 -86 l0 -78 383 0 c210 0 410 -5 445 -10 129 -18 250 -100 315 -213 54 -91 57 -130 57 -749 l0 -568 -85 0 -85 0 0 85 0 85 -85 0 -85 0 0 -810 0 -810 85 0 85 0 0 640 0 640 170 0 170 0 0 668 c0 548 -3 680 -15 737 -43 203 -197 374 -400 442 l-80 27 -430 4 c-237 2 -434 0 -438 -4z" />
                                <path d="M1190 810 l0 -810 90 0 90 0 0 640 0 640 170 0 170 0 0 85 0 85 -170 0 -170 0 0 85 0 85 -90 0 -90 0 0 -810z" />
                                <path d="M2470 810 l0 -810 90 0 90 0 0 810 0 810 -90 0 -90 0 0 -810z" />
                                <path d="M3750 1535 l0 -85 -170 0 -170 0 0 -85 0 -85 170 0 170 0 0 -640 0 -640 90 0 90 0 0 810 0 810 -90 0 -90 0 0 -85z" />
                                <path d="M770 640 l0 -640 85 0 85 0 0 640 0 640 -85 0 -85 0 0 -640z" />
                                <path d="M4180 640 l0 -640 85 0 85 0 0 640 0 640 -85 0 -85 0 0 -640z" />
                            </g>
                        </svg>
                    </div>
                    <strong class="menu__text">Accompany</strong>
                </button>
            </li>
            <?php
            if ($dinnerTariffArray) {
            ?>
                <li id="payment">
                    <button class="menu__item">
                        <div class="menu__icon">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">

                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M1755 4855 c-24 -23 -25 -28 -25 -175 l0 -150 -253 0 c-212 0 -256 -2 -275 -16 l-22 -15 0 -400 0 -399 -500 0 c-487 0 -501 -1 -520 -20 -20 -20 -20 -33 -20 -1709 l0 -1690 26 -20 27 -21 2367 0 2367 0 27 21 26 20 0 1690 c0 1676 0 1689 -20 1709 -19 19 -33 20 -520 20 l-500 0 0 399 0 400 -22 15 c-19 14 -63 16 -275 16 l-253 0 0 150 c0 147 -1 152 -25 175 l-24 25 -781 0 -781 0 -24 -25z m1495 -220 l0 -105 -690 0 -690 0 0 105 0 105 690 0 690 0 0 -105z m557 -792 c-4 -302 -7 -1204 -7 -2005 l0 -1458 -370 0 -370 0 0 530 0 531 -25 24 -24 25 -451 0 -451 0 -24 -25 -25 -24 0 -531 0 -530 -370 0 -370 0 0 1448 c0 797 -3 1699 -7 2005 l-6 557 1253 0 1253 0 -6 -547z m-2637 -1873 l0 -1590 -445 0 -445 0 0 1590 0 1590 445 0 445 0 0 -1590z m3670 0 l0 -1590 -445 0 -445 0 0 1590 0 1590 445 0 445 0 0 -1590z m-1920 -1105 l0 -485 -360 0 -360 0 0 485 0 485 360 0 360 0 0 -485z" />
                                    <path d="M1812 4043 c-33 -6 -88 -66 -96 -103 -3 -18 -6 -102 -6 -187 0 -167 7 -199 54 -243 27 -25 30 -25 224 -28 182 -2 199 -1 229 17 60 37 65 60 61 276 -3 190 -3 194 -28 221 -45 48 -75 54 -252 53 -90 -1 -173 -4 -186 -6z m328 -278 l0 -145 -145 0 -145 0 0 145 0 145 145 0 145 0 0 -145z" />
                                    <path d="M2925 4036 c-16 -7 -41 -26 -55 -41 -25 -26 -25 -30 -28 -220 -4 -216 1 -239 61 -276 30 -18 47 -19 229 -17 l197 3 31 30 c46 44 52 80 48 270 -3 185 -8 202 -66 242 -23 15 -52 18 -207 21 -136 1 -187 -1 -210 -12z m345 -271 l0 -145 -145 0 -145 0 0 145 0 145 145 0 145 0 0 -145z" />
                                    <path d="M1779 3167 c-61 -40 -69 -70 -69 -262 0 -187 8 -218 63 -259 26 -19 45 -21 212 -24 290 -5 295 0 295 284 0 281 -3 284 -288 284 -169 0 -181 -1 -213 -23z m361 -262 l0 -145 -145 0 -145 0 0 145 0 145 145 0 145 0 0 -145z" />
                                    <path d="M2913 3175 c-18 -8 -42 -29 -53 -47 -18 -30 -20 -50 -20 -222 0 -284 5 -289 295 -284 167 3 186 5 212 24 55 41 63 72 63 259 0 192 -8 222 -69 262 -32 22 -43 23 -215 23 -133 -1 -190 -5 -213 -15z m357 -270 l0 -145 -145 0 -145 0 0 145 0 145 145 0 145 0 0 -145z" />
                                    <path d="M1825 2331 c-97 -25 -115 -69 -115 -281 0 -187 10 -226 69 -262 34 -22 46 -23 221 -23 206 0 221 4 260 68 18 29 20 51 20 217 0 166 -2 188 -20 217 -38 62 -56 68 -240 70 -91 1 -179 -2 -195 -6z m315 -281 l0 -150 -145 0 -145 0 0 150 0 150 145 0 145 0 0 -150z" />
                                    <path d="M2942 2331 c-18 -5 -42 -17 -53 -27 -45 -40 -49 -64 -49 -256 0 -164 2 -186 20 -215 39 -64 54 -68 260 -68 215 0 238 7 271 78 16 36 19 67 19 206 0 144 -2 170 -20 206 -34 71 -60 80 -250 82 -91 1 -180 -2 -198 -6z m328 -281 l0 -150 -145 0 -145 0 0 150 0 150 145 0 145 0 0 -150z" />
                                    <path d="M561 3072 c-68 -34 -76 -56 -79 -220 -3 -168 6 -201 68 -237 31 -18 52 -20 180 -20 162 0 182 6 221 68 18 29 19 47 17 189 -3 151 -4 157 -28 184 -42 45 -78 54 -217 54 -102 0 -135 -4 -162 -18z m269 -232 l0 -110 -105 0 -105 0 0 110 0 110 105 0 105 0 0 -110z" />
                                    <path d="M560 2202 c-19 -9 -45 -32 -57 -51 -21 -31 -23 -45 -23 -173 0 -231 17 -248 245 -248 131 0 154 3 182 20 57 35 65 63 61 235 -3 148 -4 154 -28 181 -42 45 -78 54 -217 54 -102 0 -135 -4 -163 -18z m270 -232 l0 -110 -105 0 -105 0 0 110 0 110 105 0 105 0 0 -110z" />
                                    <path d="M560 1332 c-19 -9 -45 -32 -57 -51 -21 -31 -23 -45 -23 -173 0 -158 10 -194 65 -228 29 -18 49 -20 181 -20 130 0 153 3 181 20 55 33 63 62 63 225 0 167 -8 193 -72 226 -52 27 -285 28 -338 1z m270 -232 l0 -110 -105 0 -105 0 0 110 0 110 105 0 105 0 0 -110z" />
                                    <path d="M4235 3076 c-16 -7 -41 -26 -55 -41 -24 -26 -25 -32 -28 -183 -2 -142 -1 -160 17 -189 39 -62 59 -68 221 -68 128 0 149 2 180 20 59 34 70 70 70 225 0 149 -10 185 -63 224 -25 19 -45 21 -170 23 -104 2 -150 -1 -172 -11z m265 -236 l0 -110 -105 0 -105 0 0 110 0 110 105 0 105 0 0 -110z" />
                                    <path d="M4235 2206 c-16 -7 -41 -26 -55 -41 -24 -26 -25 -32 -28 -180 -4 -172 4 -200 61 -235 28 -17 51 -20 182 -20 171 0 198 9 229 74 15 31 17 60 14 181 -3 161 -12 183 -79 217 -43 23 -276 25 -324 4z m265 -236 l0 -110 -105 0 -105 0 0 110 0 110 105 0 105 0 0 -110z" />
                                    <path d="M4219 1329 c-62 -36 -69 -58 -69 -224 0 -163 8 -192 63 -225 28 -17 51 -20 182 -20 228 0 245 17 245 248 0 128 -2 142 -23 173 -39 58 -74 69 -227 69 -118 0 -139 -3 -171 -21z m281 -229 l0 -110 -105 0 -105 0 0 110 0 110 105 0 105 0 0 -110z" />
                                </g>
                            </svg>
                        </div>
                        <strong class="menu__text">Banquet</strong>
                    </button>
                </li>
            <?php }


            if ($hotel_count) { ?>
                <li id="accd">
                    <button class="menu__item">
                        <div class="menu__icon">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">

                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M3388 4870 c-169 -30 -326 -124 -424 -252 -88 -114 -143 -269 -144 -402 0 -101 79 -308 293 -766 58 -124 157 -335 220 -470 125 -266 142 -289 188 -258 35 23 491 998 605 1293 37 96 39 105 38 215 0 98 -4 126 -26 188 -38 106 -83 176 -168 263 -81 82 -190 145 -300 174 -69 19 -219 27 -282 15z m231 -135 c245 -58 431 -284 431 -524 0 -95 -85 -300 -422 -1014 -72 -152 -132 -276 -134 -275 -8 10 -351 748 -419 903 -139 321 -150 365 -120 494 70 296 371 485 664 416z" />
                                    <path d="M3431 4500 c-123 -25 -227 -140 -246 -271 -9 -61 16 -157 56 -215 136 -198 440 -167 543 55 32 70 29 188 -7 258 -67 130 -208 201 -346 173z m168 -149 c128 -76 114 -269 -24 -332 -185 -85 -357 144 -223 297 68 77 156 90 247 35z" />
                                    <path d="M274 4849 c-17 -19 -19 -41 -22 -278 -3 -305 -3 -304 78 -311 l55 -5 3 -1984 c2 -1829 3 -1986 19 -2003 15 -17 99 -18 2218 -18 l2202 0 19 21 c18 20 19 53 19 1125 0 1046 -1 1104 -18 1121 -17 17 -85 18 -1307 21 l-1290 2 0 860 0 860 48 0 c83 0 82 -4 82 303 0 254 -1 268 -20 287 -20 20 -33 20 -1043 20 l-1024 0 -19 -21z m1986 -284 l0 -185 -945 0 -945 0 0 185 0 185 945 0 945 0 0 -185z m-133 -1642 c-4 -731 -7 -1604 -7 -1940 l0 -613 -140 0 -139 0 -3 366 c-3 349 -4 366 -22 380 -17 12 -101 14 -500 14 -432 0 -481 -2 -498 -17 -17 -15 -18 -40 -18 -380 l0 -363 -145 0 -145 0 0 1940 0 1940 812 0 811 0 -6 -1327z m2621 -1530 l2 -1023 -180 0 -180 0 0 140 0 140 63 0 c98 0 99 1 95 213 -3 194 -8 208 -76 252 l-31 20 -1 350 c0 379 -4 409 -55 504 -37 66 -121 146 -188 177 -91 41 -173 46 -757 42 -532 -3 -546 -4 -603 -25 -115 -43 -214 -139 -258 -250 -23 -57 -24 -68 -27 -429 l-3 -371 -24 -11 c-12 -6 -36 -27 -51 -48 -29 -37 -29 -38 -32 -210 -3 -163 -2 -174 17 -193 17 -17 34 -21 81 -21 l60 0 0 -140 0 -140 -175 0 -175 0 0 1025 0 1025 1248 -2 1247 -3 3 -1022z m-613 669 c63 -29 119 -83 153 -147 l27 -50 3 -357 3 -358 -81 0 -80 0 0 152 c0 127 -3 154 -17 170 -15 17 -37 18 -278 18 -210 0 -265 -3 -281 -14 -17 -13 -19 -30 -22 -170 l-3 -156 -65 0 -64 0 0 148 c0 117 -3 152 -16 170 -15 22 -18 22 -281 22 -234 0 -269 -2 -285 -17 -16 -14 -18 -34 -18 -170 l0 -153 -81 0 -80 0 3 353 c3 333 4 354 24 398 43 93 129 163 221 179 27 4 300 7 608 7 l560 -2 50 -23z m-825 -802 l0 -110 -180 0 -180 0 0 110 0 110 180 0 180 0 0 -110z m730 0 l0 -110 -180 0 -180 0 0 110 0 110 180 0 180 0 0 -110z m378 -252 c8 -8 12 -50 12 -125 l0 -113 -935 0 -935 0 0 113 c0 75 4 117 12 125 17 17 1829 17 1846 0z m-2698 -318 l0 -320 -400 0 -400 0 0 320 0 320 400 0 400 0 0 -320z m1400 -45 c0 -3 -30 -66 -67 -140 l-68 -135 -132 0 -133 0 0 140 0 140 200 0 c110 0 200 -2 200 -5z m679 -131 c39 -75 71 -138 71 -140 0 -2 -169 -4 -375 -4 l-375 0 70 140 70 140 234 0 234 0 71 -136z m471 -4 l0 -140 -133 0 -132 0 -68 135 c-37 74 -67 137 -67 140 0 3 90 5 200 5 l200 0 0 -140z" />
                                    <path d="M666 4014 c-22 -22 -24 -350 -2 -380 13 -18 30 -19 268 -22 246 -3 255 -2 281 18 l27 21 0 173 c0 155 -2 175 -18 189 -16 15 -51 17 -280 17 -224 0 -263 -2 -276 -16z m454 -194 l0 -90 -175 0 -175 0 0 90 0 90 175 0 175 0 0 -90z" />
                                    <path d="M1409 4013 c-11 -12 -14 -57 -14 -189 0 -153 2 -175 18 -191 16 -16 41 -18 278 -18 l261 0 19 24 c17 21 19 41 19 185 0 130 -3 166 -16 184 -15 22 -18 22 -283 22 -238 0 -270 -2 -282 -17z m459 -190 l-3 -88 -175 0 -175 0 -3 88 -3 87 181 0 181 0 -3 -87z" />
                                    <path d="M672 3454 c-21 -15 -22 -21 -22 -193 0 -160 2 -180 18 -194 16 -15 51 -17 280 -17 258 0 261 0 276 22 13 18 16 55 16 190 0 155 -2 170 -20 188 -19 19 -33 20 -273 20 -212 0 -256 -2 -275 -16z m448 -194 l0 -90 -175 0 -175 0 0 90 0 90 175 0 175 0 0 -90z" />
                                    <path d="M1414 3449 c-17 -18 -19 -40 -22 -171 -2 -83 -1 -161 2 -174 14 -53 17 -54 295 -54 206 0 261 3 277 14 17 13 19 30 22 172 2 86 1 167 -2 180 -14 53 -17 54 -295 54 -249 0 -259 -1 -277 -21z m456 -189 l0 -90 -180 0 -180 0 0 90 0 90 180 0 180 0 0 -90z" />
                                    <path d="M666 2884 c-22 -22 -24 -350 -2 -380 13 -18 30 -19 268 -22 246 -3 255 -2 281 18 l27 21 0 173 c0 155 -2 175 -18 189 -16 15 -51 17 -280 17 -224 0 -263 -2 -276 -16z m454 -194 l0 -90 -175 0 -175 0 0 90 0 90 175 0 175 0 0 -90z" />
                                    <path d="M1409 2883 c-11 -12 -14 -57 -14 -189 0 -153 2 -175 18 -191 16 -16 41 -18 278 -18 l261 0 19 24 c17 21 19 41 19 185 0 130 -3 166 -16 184 -15 22 -18 22 -283 22 -238 0 -270 -2 -282 -17z m459 -190 l-3 -88 -175 0 -175 0 -3 88 -3 87 181 0 181 0 -3 -87z" />
                                    <path d="M672 2324 c-21 -15 -22 -21 -22 -193 0 -160 2 -180 18 -194 16 -15 51 -17 280 -17 258 0 261 0 276 22 13 18 16 55 16 190 0 155 -2 170 -20 188 -19 19 -33 20 -273 20 -212 0 -256 -2 -275 -16z m448 -194 l0 -90 -175 0 -175 0 0 90 0 90 175 0 175 0 0 -90z" />
                                    <path d="M1414 2319 c-17 -18 -19 -40 -22 -171 -2 -83 -1 -161 2 -174 14 -53 17 -54 295 -54 206 0 261 3 277 14 17 13 19 30 22 172 2 86 1 167 -2 180 -14 53 -17 54 -295 54 -249 0 -259 -1 -277 -21z m456 -189 l0 -90 -180 0 -180 0 0 90 0 90 180 0 180 0 0 -90z" />
                                    <path d="M666 1754 c-22 -22 -24 -350 -2 -380 13 -18 30 -19 268 -22 246 -3 255 -2 281 18 l27 21 0 173 c0 155 -2 175 -18 189 -16 15 -51 17 -280 17 -224 0 -263 -2 -276 -16z m454 -194 l0 -90 -175 0 -175 0 0 90 0 90 175 0 175 0 0 -90z" />
                                    <path d="M1409 1753 c-11 -12 -14 -57 -14 -189 0 -153 2 -175 18 -191 16 -16 41 -18 278 -18 l261 0 19 24 c17 21 19 41 19 185 0 130 -3 166 -16 184 -15 22 -18 22 -283 22 -238 0 -270 -2 -282 -17z m459 -190 l-3 -88 -175 0 -175 0 -3 88 -3 87 181 0 181 0 -3 -87z" />
                                </g>
                            </svg>
                        </div>
                        <strong class="menu__text">Accommodation</strong>
                    </button>
                </li>
            <?php } ?>
            <!-- <li id="confirm">
                <button class="menu__item">
                    <div class="menu__icon">

                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 179.1 145">

                            <path stroke-linecap="round" d="M94,139c-4.8,1.3-8.8,1.7-11.4,1.8c0,0-18.3,1.1-36.9-11.6c-1.9-1.3-4.7-3.2-7.8-6.2c-1.7-1.6-2.9-2.9-3.4-3.6
                    c0,0-3.6-4.2-6.1-8.6c-4.6-8.4-5.4-18.9-5.5-21l0,0V75.5v-39c0-0.7,0.5-1.3,1.2-1.5l58-14.2c0.2-0.1,0.5-0.1,0.7,0l57.9,14.7
                    c0.7,0.2,1.1,0.8,1.1,1.5v29.7" />
                            <path id="security-cir" stroke-linecap="round" d="M158.3,120.7c0,18.3-14.8,33.1-33.1,33.1s-33-14.8-33-33.1s14.8-33.1,33.1-33.1S158.3,102.4,158.3,120.7z" />
                            <g id="security-strok">
                                <path stroke-linecap="round" d="M151.1,104.5l-25,25c-1.3,1.3-3.5,1.3-4.9,0l-9.1-9.1" />
                                <path stroke-linecap="round" d="M82.6,43L23.1,62.3" />
                                <path stroke-linecap="round" d="M82.6,68.4L23.1,87.6" />
                            </g>

                        </svg>

                    </div>
                    <strong class="menu__text">Confirm</strong>
                </button>
            </li> -->
        </ul>
    </header>
    <div class="frm-lft">
        <div>
            <a class="frm-logo">
                <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $cfg['MAILER.LOGO'] ?>" alt="">
            </a>
            <p><?= $cfg['FULL_CONF_NAME'] ?></p>
            <h3><?= $cfg['EMAIL_CONF_NAME'] ?></h3>
            <!-- <h3>2025</h3> -->
            <?php
            $startDate = new DateTime($cfg['CONF_START_DATE']);
            $endDate = new DateTime($cfg['CONF_END_DATE']);
            ?>
            <h4><i class="fas fa-calendar" style="margin-right: 5px;"></i><?= $startDate->format('F j') . '-' . $endDate->format('jS') . ', ' . $endDate->format('Y'); ?> </h4>
            <h4><i class="fas fa-hotel" style="margin-right: 5px;"></i><?= $cfg['EMAIL_CONF_VENUE'] ?></h4>
            <h4><i class="fas fa-user" style="margin-right: 5px;"></i>Company: <?= $resCompany[0]['exhibitor_company_name'] ?></h4>
        </div>
        <div class="button-container">
            <!-- <a href="<?= $cfg['EMAIL_CONF_WEBSITE'] ?>" target="_blank" title="<?= $cfg['EMAIL_CONF_WEBSITE'] ?>" class="glass-btn blue-btn">
                <img src="globe.png" alt="facebook">
            </a>
            <a href="https://wboacon2025.com/#vnu" target="_blank" title="Venue: Banyan Tree, Mandarman" class="glass-btn red-btn">
                <img src="home.png" alt="youtube">
            </a> -->
            <a href="tel:<?= $cfg['EMAIL_CONF_CONTACT_US'] ?>" title="<?= $cfg['EMAIL_CONF_CONTACT_US'] ?>" class="glass-btn amber-btn">
                <img src="images/phone.png" alt="soundcloud">
            </a>
            <a href="mailto:<?= $cfg['EMAIL_CONF_EMAIL_US'] ?>" title="<?= $cfg['EMAIL_CONF_EMAIL_US'] ?>" class="glass-btn grn-btn">
                <img src="images/email.png" alt="soundcloud">
            </a>
        </div>
    </div>

    <form action="exhibitor.registration.process.php" method="POST" id="msform">
        <input type="hidden" name='act' value='insert'>
        <input type="hidden" name='registration_tariff_cutoff_id' value='<?= $currentCutoffId ?>'>
        <input type="hidden" name='exhibitor_company_code' id='exhibitor_company_code' value='<?= $company_code ?>'>
        <input type="hidden" name='encoded_code' id='encoded_code' value='<?= $encoded_code ?>'>

        <fieldset id="section1">
            <div class="frm-inner">
                <div class="col-sm-12 personal_info">
                    <div class="col-sm-12">
                        <label for="registration_classification_id" class="formbold-form-label">Registration Classification</label>
                        <div>
                            <?php
                            if ($conferenceTariffArray) {
                                foreach ($conferenceTariffArray as $key => $registrationDetailsVal) {
                                    $styleCss = 'style=""';
                                    $reg_tariff = getExhibitortariff($company_code, $key);
                                    $classificationType = getRegClsfType($key);
                                    // echo '<pre>'; print_r($reg_tariff);
                                    if ($classificationType != 'ACCOMPANY' && ($classificationType != 'COMBO' || $key == 3) && $reg_tariff[0]['registration_tariff_inr'] > 0) {
                            ?>
                                        <label class="cus-con">
                                            <?= getRegClsfName($key) ?>
                                            <input type="checkbox" name="registration_classification_id" operationmode="registration_tariff" value="<?= $key ?>" currency="" registrationtype="DELEGATE" validate="Please choose a Registration Classification" accommodationpackageid="">
                                            <span class="checkmark"></span>
                                            <?

                                            foreach ($registrationDetailsVal as $keyCutoff => $rowCutoff) {
                                                //if($keyCutoff !='4') 
                                                //{

                                                $RegistrationTariffDisplay = $rowCutoff['CURRENCY'] . "&nbsp;" . $rowCutoff['AMOUNT'];
                                                if ($rowCutoff['AMOUNT'] <= 0) {
                                                    if ($classificationType == 'FULL_ACCESS') {
                                                        $RegistrationTariffDisplay = "Complimentary";
                                                    } else {
                                                        $RegistrationTariffDisplay = "Zero Value";
                                                    }
                                                }
                                            ?>
                                                <!-- <td align="right" use="registrationTariff" cutoff="<?= $keyCutoff ?>" tariffAmount="<?= $rowCutoff['AMOUNT'] ?>" tariffCurrency="<?= $rowCutoff['CURRENCY'] ?>"><?= $RegistrationTariffDisplay ?></td> -->
                                            <?php
                                                //}
                                            }
                                            ?>

                                        </label>
                                <?
                                    }
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="<?= sizeof($cutoffArray) + 1 ?>" align="center">
                                        <strong style="color:#ffebeb;">Classification not set</strong>
                                    </td>
                                </tr>
                            <?
                            }
                            ?>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="user_email_id" class="formbold-form-label">Email Id<span class="mandatory">*</span></label>
                        <input type="text" class="formbold-form-input" name="email_id" id="email_id" validate="Please Enter Email Address" required="">
                        <!-- <input type="hidden" name="email_id_validation" id="email_id_validation" /> -->
                        <div id="email_div"></div>
                    </div>


                    <div class="col-sm-6">
                        <label for="age" class="formbold-form-label"> Mobile </label>
                        <div style="display:flex">
                            <input type="tel" class="formbold-form-input" style="width: 77px;" name="usd_code" id="mobile_isd_code" value="+91" required="" autocomplete="nope">&nbsp;
                            <input type="text" class="formbold-form-input" name="mobile_no" id="mobile_no" fortype="mobileValidate" pattern="^\d{10}$" value="" maxlength="10" onkeypress="return isNumber(event)" required="" validate="Please Enter Mobile Number">
                        </div>
                        <!-- <input type="hidden" name="mobile_validation" id="mobile_validation" /> -->
                        <div id="mobile_div"></div>


                    </div>
                    <div class="col-sm-12"></div>
                    <div class="col-sm-3">
                        <label for="" class="formbold-form-label">Title</label>
                        <select name="title" id="title" class="formbold-form-input" tabindex="4" required="">
                            <option value="Dr" selected="selected">Dr</option>
                            <option value="Prof">Prof</option>
                            <option value="Mr">Mr</option>
                            <option value="Ms">Ms</option>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label for="first_name" class="formbold-form-label">First Name<span class="mandatory">*</span></label>
                        <input type="text" class="formbold-form-input" name="first_name" id="first_name" style=" text-transform:uppercase;" tabindex="5" validate="Please Enter First Name" required="" autocomplete="nope">
                    </div>
                    <div class="col-sm-3">
                        <label for="user_first_name" class="formbold-form-label">Last Name<span class="mandatory">*</span></label>
                        <input type="text" class="formbold-form-input" name="last_name" id="last_name" tabindex="7" style="text-transform:uppercase;" value="" validate="Please Enter Last Name" required="" implementvalidate="y">
                    </div>
                    <?php
                    if (in_array("Gender", $available_registration_fields)) {
                    ?>
                        <div class="col-sm-3">

                            <label class="formbold-form-label">Gender <span class="mandatory">*</span></label>
                            <div>
                                <label class="cus-con">Male
                                    <input type="radio" name="user_gender" id="user_gender_male" checked="checked" value="MALE" tabindex="8" required="">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="cus-con">Female
                                    <input type="radio" name="user_gender" id="user_gender_female" value="FEMALE" tabindex="9" required="">
                                    <span class="checkmark"></span>
                                </label>
                            </div>

                        </div>
                    <?php } ?>
                    <div class="col-sm-12"></div>
                    <?php
                    if (in_array("Address", $available_registration_fields)) {
                    ?>
                        <div class="col-sm-9">
                            <label for="first_name" class="formbold-form-label">Address<span class="mandatory">*</span></label>
                            <textarea name="address" class="formbold-form-input" id="address" tabindex="10" validate="Please Enter Address" style=" text-transform:uppercase;"></textarea>
                        </div>
                    <?php
                    }
                    if (in_array("Food", $available_registration_fields)) {
                    ?>
                        <div class="col-sm-3">
                            <label for="user_first_name" class="formbold-form-label">Food Preference<span class="mandatory">*</span></label>
                            <div>
                                <label class="cus-con">Veg
                                    <input type="radio" name="food_preference" id="food_preference_veg" checked="checked" value="VEG" tabindex="15">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="cus-con">Non Veg
                                    <inputtype="radio" name="food_preference" id="food_preference_non_veg" value="NON VEG" tabindex="16">
                                        <span class="checkmark"></span>
                                    </inputtype="radio">
                                </label>
                            </div>

                        </div>
                    <?php }
                    if (in_array("Country", $available_registration_fields)) {
                    ?>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-3">
                            <label for="user_country" class="formbold-form-label"> Country </label>
                            <select required="" implementvalidate="y" name="country" id="country" class="formbold-form-input" style="" fortype="country" stateid="user_state" tabindex="11" sequence="1" validate="Please select Country">
                                <option value="">-- Select Country --</option>
                                <?php
                                $sqlCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
																	           WHERE `status` = 'A' AND country_id!='241'  
											                                ORDER BY `country_name`";
                                $resultCountry = $mycms->sql_select($sqlCountry);
                                if ($resultCountry) {
                                    foreach ($resultCountry as $i => $rowFetchUserCountry) {
                                ?>
                                        <option value="<?= $rowFetchUserCountry['country_id'] ?>"><?= $rowFetchUserCountry['country_name'] ?></option>
                                <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    <?php
                    }
                    if (in_array("State", $available_registration_fields)) {
                    ?>
                        <div class="col-sm-3">

                            <label for="state" class="formbold-form-label"> State </label>
                            <div use="stateContainer">
                                <select name="state" class="formbold-form-input" id="user_state" fortype="state" sequence="1" disabled="disabled" required="" validate="Please select State" implementvalidate="y">
                                    <option value="">-- Select Country First --</option>
                                    <!-- <option value="730">WEST BENGAL</option> -->
                                </select>
                            </div>

                        </div>
                    <?php
                    }

                    if (in_array("City", $available_registration_fields)) {
                    ?>
                        <div class="col-sm-3">
                            <label for="first_name" class="formbold-form-label">City<span class="mandatory">*</span></label>
                            <input type="text" class="formbold-form-input" name="city" id="city" tabindex="13" style=" text-transform:uppercase;" validate="Please enter City" value="" required="" implementvalidate="y">
                        </div>
                    <?php
                    }

                    if (in_array("Pin", $available_registration_fields)) {
                    ?>

                        <div class="col-sm-3">
                            <label for="user_first_name" class="formbold-form-label">Postal Code<span class="mandatory">*</span></label>
                            <input type="text" class="formbold-form-input" name="postal_code" id="postal_code" tabindex="14" value="" validate="Please enter postal code" style=" text-transform:uppercase;" required="" implementvalidate="y">
                        </div>
                    <?php
                    }


                    ?>
                </div>
            </div>

            <input type="button" name="previous" class="previous action-button" value="Previous" style="filter:blur(1.3px);pointer-events:none" />
            <input type="button" name="next" class="next action-button" section='1' value="Next" />

        </fieldset>
        <?php if (sizeof($workshopDetailsArray) > 0) { ?>
            <fieldset id="section2">
                <div class="frm-inner">
                    <div class="col-sm-12 personal_info">
                        <div class="link sub-heading" use=""><b>Workshop</b></div>
                        <div class="col-xs-12 table-wrap">
                            <div style="overflow: auto;">
                                <table width="100%" class="table table-striped">
                                    <tbody>
                                        <tr class="theader">
                                            <td align="left">Workshop Name</td>
                                            <td align="center" style="width: 180px;">Price</td>
                                            <!-- <td align="right" style="width: 180px;"></td> -->
                                        </tr>
                                        <?php
                                        foreach ($workshopDetailsArray as $keyWorkshopclsf => $rowWorkshopclsf) {
                                            foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {

                                                $workshopTariff = getExhibitortariff($company_code, $rowRegClasf[2]['REG_ID']);
                                                $workshop_tariff_inr = json_decode($workshopTariff[0]['workshop_tariff_inr']);
                                                $workshop_id = json_decode($workshopTariff[0]['workshop_id']);
                                                $workshop_tariff_arr = array();
                                                foreach ($workshop_id as $key => $id) {
                                                    $workshop_tariff_arr[$id] = $workshop_tariff_inr[$key];
                                                }

                                                // echo '<pre>';
                                                // print_r($workshop_tariff_arr);
                                                // die;
                                                // echo '<pre>'; print_r($rowRegClasf);
                                                // December workshop
                                                if (/*$rowRegClasf[4]['WORKSHOP_TYPE'] == 'MASTER CLASS' || */$rowRegClasf[2]['WORKSHOP_TYPE'] /*== 'WORKSHOP'*/) {

                                                    $workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];

                                                    if ($workshopCount < 1) {
                                                        $style = 'disabled="disabled"';
                                                        $span    = '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
                                                    } else {
                                                        $style = '';
                                                        $span    = '';
                                                    }
                                        ?>
                                                    <tr use="<?= $keyRegClasf ?>" operetionmode="workshopTariffTr" style="display:none;">
                                                        <td align="left">
                                                            <div style="opacity: 1;">
                                                                <label class="cus-con"><?= getWorkshopName($keyWorkshopclsf) . ' (' . $mycms->cDate('m-d-Y', getWorkshopDate($keyWorkshopclsf)) . ')' ?>
                                                                    <input type="radio" class="formbold-form-input" operationmode="workshopId" name="workshop_id" value="<?= $keyWorkshopclsf ?>" chkstat="false">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </div>

                                                        </td>
                                                        <?

                                                        // foreach ($rowRegClasf as $keyCutoff => $cutoffvalue) {
                                                        if (in_array($rowRegClasf[2]['WORKSHOP_ID'], $workshop_id)) {
                                                            $WorkshopTariffDisplay = "INR &nbsp;" . $workshop_tariff_arr[$rowRegClasf[2]['WORKSHOP_ID']];
                                                            if ($workshop_tariff_arr[$rowRegClasf[2]['WORKSHOP_ID']] <= 0 || $workshop_tariff_arr[$rowRegClasf[2]['WORKSHOP_ID']] = "") {
                                                                $WorkshopTariffDisplay = "Included in Registration";
                                                            }
                                                        } else {
                                                            $WorkshopTariffDisplay = "Included in Registration"; // not set
                                                        }
                                                        // $WorkshopTariffDisplay = $cutoffvalue['CURRENCY'] . "&nbsp;" . $cutoffvalue[$cutoffvalue['CURRENCY']];
                                                        // if ($cutoffvalue[$cutoffvalue['CURRENCY']] <= 0) {
                                                        //     $WorkshopTariffDisplay = "Included in Registration";
                                                        // }
                                                        ?>
                                                        <td align="center" use="workshopTariff" cutoff="<?= $keyCutoff ?>" tariffAmount="<?= $cutoffvalue[$cutoffvalue['CURRENCY']] ?>" tariffCurrency="<?= $cutoffvalue['CURRENCY'] ?>"><?= $WorkshopTariffDisplay ?></td>
                                                        <?

                                                        // }
                                                        ?>
                                                    </tr>
                                        <?
                                                }
                                            }
                                        } ?>

                                        <tr use="na" operetionmode="workshopTariffTr" style="display: none;">
                                            <td align="center" colspan="4"><strong style="color:#ffebeb;">Please Select Registration Classification First</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="button" name="previous" class="previous action-button" value="Previous" />
                <input type="button" name="next" class="next action-button" section='2' value="Next" />
            </fieldset>
        <?php }

        ?>

        <fieldset id="section3">
            <div class="frm-inner">
                <div class="col-sm-12 personal_info accompany-wrap" use="registrationAccompanyDetails" style="display:block;">
                    <input type="hidden" name="accompanyClasfId" value="1">
                    <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="8260.00">
                    <div class="link sub-heading" use=""><b>Accompanying Person(s)</b></div>

                    <div class="col-xs-12">
                        <label class="formbold-form-label">Number of Accompanying Person(s)</label>
                        <div style="padding-top:10px">
                            <label class="cus-con">None
                                <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="0" amount="0" invoicetitle="Accompanying Person" checked="checked" required="">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">One
                                <input type="radio" name="accompanyCount" id="accompanyCount1" use="accompanyCountSelect" value="1" amount="8260" invoicetitle="Accompanying - 1 Person">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Two
                                <input type="radio" name="accompanyCount" id="accompanyCount2" use="accompanyCountSelect" value="2" amount="16520" invoicetitle="Accompanying - 2 Person">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Three
                                <input type="radio" name="accompanyCount" id="accompanyCount3" use="accompanyCountSelect" value="3" amount="24780" invoicetitle="Accompanying - 3 Person">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Four
                                <input type="radio" name="accompanyCount" id="accompanyCount4" use="accompanyCountSelect" value="4" amount="33040" invoicetitle="Accompanying - 4 Person">
                                <span class="checkmark"></span>
                            </label>
                            <i class="itemPrice pull-right" id="accompanyAmntDisplay" operetionmode="workshopTariffTr" style="display:none;">
                            </i>
                            &nbsp;
                        </div>

                    </div>

                    <div class="col-xs-12" use="accompanyDetails" index="1" style="display:none;padding: 0px; margin-bottom: 0px;">
                        <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 1</h4>

                        <div class="col-sm-6" actas="fieldContainer">
                            <label for="accompany_name_add_1" class="formbold-form-label">Name</label>
                            <input type="text" class="form-control formbold-form-input" name="accompany_name_add[0]" id="accompany_name_add_1" style="text-transform:uppercase;">
                            <input type="hidden" name="accompany_selected_add[0]" value="0">
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.
                            </div>
                        </div>

                        <!-- <div class="col-xs-8 form-group" actAs='fieldContainer'>
                                    <div class="checkbox">
                                        <label class="select-lable">DINNER</label>
                                                                            </div>
                                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                                </div> -->

                        <div class="col-sm-6" actas="fieldContainer">
                            <label class="formbold-form-label">Food Preference</label>
                            <div>
                                <label class="cus-con">Veg
                                    <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[0]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="cus-con">Non-Veg
                                    <input type="radio" name="accompany_food_choice[0]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="error alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                        </div>
                    </div>

                    <div class="col-xs-12" use="accompanyDetails" index="2" style="display:none;padding: 0px; margin: 15px 0px; border-top: 1px solid rgb(229, 229, 229);">
                        <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 2</h4>

                        <div class="col-sm-6 " actas="fieldContainer">
                            <label for="accompany_name_add_2" class="formbold-form-label">Name</label>
                            <input type="text" class="form-control formbold-form-input" name="accompany_name_add[1]" id="accompany_name_add_2" style="text-transform:uppercase;">
                            <input type="hidden" name="accompany_selected_add[1]" value="1">
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.</div>
                        </div>

                        <div class="col-sm-6" actas="fieldContainer">
                            <label class="formbold-form-label">Food Preference</label>
                            <div>
                                <label class="cus-con">Veg
                                    <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[1]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="cus-con">Non-Veg
                                    <input type="radio" name="accompany_food_choice[1]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                        </div>
                    </div>

                    <div class="col-xs-12" use="accompanyDetails" index="3" style="display:none;padding: 0px; margin: 15px 0px; border-top: 1px solid rgb(229, 229, 229);">
                        <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 3</h4>

                        <div class="col-sm-6" actas="fieldContainer">
                            <label for="accompany_name_add_3" class="formbold-form-label">Name</label>
                            <input type="text" class="form-control formbold-form-input" name="accompany_name_add[2]" id="accompany_name_add_3" style="text-transform:uppercase;">
                            <input type="hidden" name="accompany_selected_add[2]" value="2">
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.</div>
                        </div>

                        <div class="col-sm-6" actas="fieldContainer">
                            <label class="formbold-form-label">Food Preference</label>
                            <div>
                                <label class="cus-con">Veg
                                    <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[2]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="cus-con">Non-Veg
                                    <input type="radio" name="accompany_food_choice[2]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                        </div>
                    </div>

                    <div class="col-xs-12" use="accompanyDetails" index="4" style="display:none;padding: 0px; margin: 15px 0px; border-top: 1px solid rgb(229, 229, 229);">
                        <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 4</h4>

                        <div class="col-sm-6 ">
                            <label for="accompany_name_add_4" class="formbold-form-label">Name</label>
                            <input type="text" class="form-control formbold-form-input" name="accompany_name_add[3]" id="accompany_name_add_4" style="text-transform:uppercase;">
                            <input type="hidden" name="accompany_selected_add[3]" value="3">
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.</div>
                        </div>
                        <div class="col-sm-6" actas="fieldContainer">
                            <label class="formbold-form-label">Food Preference</label>
                            <div>
                                <label class="cus-con">Veg
                                    <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[3]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="cus-con">Non-Veg
                                    <input type="radio" name="accompany_food_choice[3]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                        </div>
                    </div>


                    <div class="clearfix"></div>

                </div>
            </div>
            <input type="button" name="previous" class="previous action-button" value="Previous" />
            <input type="button" name="next" class="next action-button" section='3' value="Next" />
        </fieldset>
        <?php
        if ($dinnerTariffArray) {
        ?>
            <fieldset id="section4">
                <div class="frm-inner">
                    <div class="col-sm-12 personal_info">
                        <div class="link sub-heading" use=""><b>Banquet</b></div>
                        <div class="col-xs-12 table-wrap">
                            <div style="overflow: auto;">
                                <table width="100%" class="table table-striped">
                                    <tbody>
                                        <tr class="theader">
                                            <td align="left">Banquet Name</td>
                                            <td align="center" style="width: 180px;">Price</td>
                                        </tr>
                                        <tr>
                                            <td align="left">Banquet Name</td>
                                            <td align="center">Price</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="button" name="previous" class="previous action-button" value="Previous" />
                <input type="button" name="next" class="next action-button" section='4' value="Next" />
            </fieldset>
        <?php }


        if ($hotel_count) {
            $accommodationDetails = array();
            if (sizeof($hotel_array) > 0) {
                $hotel_ids = array_column($hotel_array, 'HOTEL_ID');
                $package_ids = array_column($hotel_array, 'ACCOMMODATION_PACKAGE_ID');
                $sqlHotel                = array();
                $sqlHotel['QUERY']         = "SELECT
                        tracm.id as ACCOMMODATION_TARIFF_ID,
                        tracm.hotel_id as HOTEL_ID,
                        tracm.package_id as ACCOMMODATION_PACKAGE_ID, 
                        chkindate.check_in_date as CHECKIN_DATE,
                        tracm.checkin_date_id as CHECKIN_DATE_ID,
                        tracm.checkout_date_id as CHECKOUT_DATE_ID,
                        chkoutdate.check_out_date as CHECKOUT_DATE,
                        DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
                        FROM " . _DB_TARIFF_ACCOMMODATION_ . " as tracm
                        INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " as chkindate
                        on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
                        INNER JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " as chkoutdate
                        on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
                        WHERE tracm.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ? 
                        AND tracm.hotel_id IN(" . implode(',', $hotel_ids) . ") AND tracm.package_id IN(" . implode(',', $package_ids) . ")
                        HAVING (DAYS) < 5
                        ORDER BY tracm.hotel_id ASC, DAYS ASC,CHECKIN_DATE ASC, CHECKOUT_DATE ASC"; // HAVING (DAYS) < 4 // remove on 21.09.2022 (user can select hotels more then 3 days)
                $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.status', 'DATA' => 'A',  'TYP' => 's');
                $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
                $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
                $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
                $resultHotel                = $mycms->sql_select($sqlHotel);
                //echo '<pre>';print_r($resultHotel);
                foreach ($resultHotel as $key => $value) {
                    // code...
                    $accommodationDetails[$value['HOTEL_ID']][] = $value;
                }
            }
            $getExistingAccommodationList = getTotalAccommodationWithoutCombo($delegateId);
        ?>

            <fieldset id="section5">
                <div class="frm-inner">
                    <div class="col-xs-12 personal_info accomodation-wrap" use="registrationOptions" style="display:block;">
                        <div class="link sub-heading" use="rightAccordianL1TriggerDiv"><b>Accommodation Details</b></div>
                        <!-- <ul class="submenu" style="display:block;"> -->
                        <!-- <li> -->
                        <div class="col-xs-12" style="display:block; padding:0;" use="residentialOperations" operationmode="chhoseServiceOptions">
                            <div class="col-xs-12 " style="padding: 0; display:block;" use="<?= $hotelId ?>" operetionmode="checkInCheckOutTr">
                                <div class="col-sm-6">
                                    <label for="hotel_select_acco_id" class="formbold-form-label">Select Hotel</label>
                                    <select class="formbold-form-input" operationmode="hotel_select_acco_id" name="hotel_select_acco_id" id="hotel_select_acco_id" room_type="room_type" required="" onchange="hotelRoomRetriver(this);" tabindex="17" validate="Please select a hotel" implementvalidate="y">
                                        <option value="">--Choose a Hotel--</option>
                                        <?php


                                        foreach ($resultFetchHotel as $key => $value) {
                                            if (!empty($getExistingAccommodationList[0]['hotel_id']) && trim($getExistingAccommodationList[0]['hotel_id']) == $value['id']) {

                                        ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['hotel_name'] ?></option>
                                            <?php
                                            } else if (empty($getExistingAccommodationList[0]['hotel_id'])) {

                                            ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['hotel_name'] ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <!-- <div class="col-sm-6">
                                    <label for="" class="formbold-form-label">Room Type</label>
                                    <div use="roomContainer" class="roomContainer">
                                        <select name="room_type" id="room_type" class="formbold-form-input" fortype="room">
                                            <option value="">-- Select Hotel First --</option>
                                        </select>
                                    </div>
                                </div> -->

                                <div id="packageDiv" class="col-xs-12 packageDiv" style="padding: 15px 0px 0px; border-top: 1px solid rgb(229, 229, 229);">


                                    <div class="alert alert-danger" callfor="alert" id="hotelErr" style="display: none;">Please enter a proper value.</div>
                                </div>

                            </div>
                            <!-- <div class="col-xs-12 " style="padding: 0; display:none;" use="ResidentialAccommodationAccompanyOption">
                                    <h4>ROOM SHARING PREFERENCE, IF ANY</h4>
                                    <div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
                                        <label for="preffered_accommpany_name">NAME</label>
                                        <input type="text" class="form-control" name="preffered_accommpany_name" id="preffered_accommpany_name">
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                    <div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
                                        <label for="preffered_accommpany_mobile">MOBILE</label>
                                        <input type="tel" class="form-control" name="preffered_accommpany_mobile" id="preffered_accommpany_mobile">
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                    <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
                                        <label for="preffered_accommpany_email">EMAIL</label>
                                        <input type="email" class="form-control" name="preffered_accommpany_email" id="preffered_accommpany_email">
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                </div> -->
                        </div>

                        <!-- <div class=" col-xs-2 text-center pull-right">
                                <button type="button" class="submit" use='nextButton'>Next</button>
                            </div> -->
                        <div class="clearfix"></div>
                        <!-- </li> -->
                        <!-- </ul> -->
                    </div>
                </div>
                <input type="button" name="previous" class="previous action-button" value="Previous" />
                <input type="button" id="bttnSubmitStep1" name="next" class="next action-button" section='5' value="Submit" />
            </fieldset>
        <?php } ?>
        <!-- <fieldset>
            <div class="frm-inner">
            </div>
            <input type="button" name="previous" class="previous action-button" value="Previous" />
            
        </fieldset> -->
    </form>
    <button class="frm-lft-btn"><i class="fas fa-bars"></i></button>
    <div class="order-details-container">
        <div class="odc-header">
            <button class="cta-button"><span>View Registration List</span><i class="fas fa-times"></i></button>
        </div>
        <div class="odc-wrapper">
            <div class="odc-header-line" style="display: flex;justify-content: space-between;">
                <h3>Registration List</h3>
                <span>
                    <input type="text" name="search_user" id="search_user"
                        style="border-radius: 30px;padding: 4px 18px;background: #7d424273;margin-right: 20px;font-size: 16px;"
                        placeholder="Search by Name or Email-id">
                </span>
            </div>
            <div class="product-container" id="reg_list">
                <!-- ajax -->
            </div>
        </div>
    </div>
</body>

<fieldset id="edit_workshop_form" class="addForm" style="display:none">
    <form action="exhibitor.registration.process.php" method="POST" name="edit_workshop_form">
        <input type="hidden" name="act" value="updateWorkshop">
        <input type="hidden" name="user_id">
        <input type="hidden" name='encoded_code' id='encoded_code' value='<?= $encoded_code ?>'>
        <div class="frm-inner">
            <div class="col-sm-12 personal_info">
                <div class="link sub-heading" use=""><b>Edit Workshop</b></div>
                <div class="col-xs-12 table-wrap">
                    <div style="overflow: auto;">
                        <table width="100%" class="table table-striped">
                            <tbody>
                                <tr class="theader">
                                    <td align="left">Workshop Name</td>
                                    <td align="center" style="width: 180px;">Price</td>
                                    <!-- <td align="right" style="width: 180px;"></td> -->
                                </tr>
                                <?php
                                foreach ($workshopDetailsArray as $keyWorkshopclsf => $rowWorkshopclsf) {
                                    foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {

                                        $workshopTariff = getExhibitortariff($company_code, $rowRegClasf[2]['REG_ID']);
                                        $workshop_tariff_inr = json_decode($workshopTariff[0]['workshop_tariff_inr']);
                                        $workshop_id = json_decode($workshopTariff[0]['workshop_id']);
                                        $workshop_tariff_arr = array();
                                        foreach ($workshop_id as $key => $id) {
                                            $workshop_tariff_arr[$id] = $workshop_tariff_inr[$key];
                                        }

                                        // echo '<pre>';
                                        // print_r($workshop_tariff_arr);
                                        // die;
                                        // echo '<pre>'; print_r($rowRegClasf);
                                        // December workshop
                                        if (/*$rowRegClasf[4]['WORKSHOP_TYPE'] == 'MASTER CLASS' || */$rowRegClasf[2]['WORKSHOP_TYPE'] /*== 'WORKSHOP'*/) {

                                            $workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];

                                            if ($workshopCount < 1) {
                                                $style = 'disabled="disabled"';
                                                $span    = '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
                                            } else {
                                                $style = '';
                                                $span    = '';
                                            }
                                ?>
                                            <tr use="<?= $keyRegClasf ?>" operetionmode="workshopTariffTr" style="display:none;">
                                                <td align="left">
                                                    <div style="opacity: 1;">
                                                        <label class="cus-con"><?= getWorkshopName($keyWorkshopclsf) . ' (' . $mycms->cDate('m-d-Y', getWorkshopDate($keyWorkshopclsf)) . ')' ?>
                                                            <input type="radio" class="formbold-form-input" operationmode="workshopId" name="workshop_id" value="<?= $keyWorkshopclsf ?>" chkstat="false">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>

                                                </td>
                                                <?

                                                // foreach ($rowRegClasf as $keyCutoff => $cutoffvalue) {
                                                if (in_array($rowRegClasf[2]['WORKSHOP_ID'], $workshop_id)) {
                                                    $WorkshopTariffDisplay = "INR &nbsp;" . $workshop_tariff_arr[$rowRegClasf[2]['WORKSHOP_ID']];
                                                    if ($workshop_tariff_arr[$rowRegClasf[2]['WORKSHOP_ID']] <= 0 || $workshop_tariff_arr[$rowRegClasf[2]['WORKSHOP_ID']] = "") {
                                                        $WorkshopTariffDisplay = "Included in Registration";
                                                    }
                                                } else {
                                                    $WorkshopTariffDisplay = "Included in Registration"; // not set
                                                }
                                                // $WorkshopTariffDisplay = $cutoffvalue['CURRENCY'] . "&nbsp;" . $cutoffvalue[$cutoffvalue['CURRENCY']];
                                                // if ($cutoffvalue[$cutoffvalue['CURRENCY']] <= 0) {
                                                //     $WorkshopTariffDisplay = "Included in Registration";
                                                // }
                                                ?>
                                                <td align="center" use="workshopTariff" cutoff="<?= $keyCutoff ?>" tariffAmount="<?= $cutoffvalue[$cutoffvalue['CURRENCY']] ?>" tariffCurrency="<?= $cutoffvalue['CURRENCY'] ?>"><?= $WorkshopTariffDisplay ?></td>
                                                <?

                                                // }
                                                ?>
                                            </tr>
                                <?
                                        }
                                    }
                                } ?>

                                <tr use="na" operetionmode="workshopTariffTr" style="display: none;">
                                    <td align="center" colspan="4"><strong style="color:#ffebeb;">Please Select Registration Classification First</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <input type="button" name="previous" class="close-add-form action-button" value="Back" />
        <input type="submit" name="next" class=" action-button" value="Save" />
    </form>
</fieldset>

<fieldset id="add_accompany_form" class="addForm" style="display:none">
    <form action="exhibitor.registration.process.php" method="POST" name="edit_accompany-form">
        <div class="frm-inner">
            <div class="col-sm-12 personal_info accompany-wrap" use="registrationAccompanyDetails" style="display:block;">
                <input type="hidden" name="act" value="updateAccompany">
                <input type="hidden" name="user_id">
                <input type="hidden" name='encoded_code' id='encoded_code' value='<?= $encoded_code ?>'>

                <div class="link sub-heading" use=""><b>Edit Accompanying Person(s)</b></div>

                <div class="col-xs-12">
                    <label class="formbold-form-label">Number of Accompanying Person(s)</label>
                    <div style="padding-top:10px">
                        <label class="cus-con">None
                            <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="0" amount="0" invoicetitle="Accompanying Person" checked="checked" required="">
                            <span class="checkmark"></span>
                        </label>
                        <label class="cus-con">One
                            <input type="radio" name="accompanyCount" id="accompanyCount1" use="accompanyCountSelect" value="1" amount="8260" invoicetitle="Accompanying - 1 Person">
                            <span class="checkmark"></span>
                        </label>
                        <label class="cus-con">Two
                            <input type="radio" name="accompanyCount" id="accompanyCount2" use="accompanyCountSelect" value="2" amount="16520" invoicetitle="Accompanying - 2 Person">
                            <span class="checkmark"></span>
                        </label>
                        <label class="cus-con">Three
                            <input type="radio" name="accompanyCount" id="accompanyCount3" use="accompanyCountSelect" value="3" amount="24780" invoicetitle="Accompanying - 3 Person">
                            <span class="checkmark"></span>
                        </label>
                        <label class="cus-con">Four
                            <input type="radio" name="accompanyCount" id="accompanyCount4" use="accompanyCountSelect" value="4" amount="33040" invoicetitle="Accompanying - 4 Person">
                            <span class="checkmark"></span>
                        </label>
                        <i class="itemPrice pull-right" id="accompanyAmntDisplay" operetionmode="workshopTariffTr" style="display:none;">
                        </i>
                        &nbsp;
                    </div>

                </div>

                <div class="col-xs-12" use="accompanyDetails" index="1" style="display:none;padding: 0px; margin-bottom: 0px;">
                    <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 1</h4>

                    <div class="col-sm-6" actas="fieldContainer">
                        <label for="accompany_name_add_1" class="formbold-form-label">Name</label>
                        <input type="text" class="form-control formbold-form-input" name="accompany_name_add[0]" id="accompany_name_add_1" style="text-transform:uppercase;">
                        <input type="hidden" name="accompany_selected_add[0]" value="0">
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.
                        </div>
                    </div>

                    <!-- <div class="col-xs-8 form-group" actAs='fieldContainer'>
                                    <div class="checkbox">
                                        <label class="select-lable">DINNER</label>
                                                                            </div>
                                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                                </div> -->

                    <div class="col-sm-6" actas="fieldContainer">
                        <label class="formbold-form-label">Food Preference</label>
                        <div>
                            <label class="cus-con">Veg
                                <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[0]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Non-Veg
                                <input type="radio" name="accompany_food_choice[0]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="error alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                    </div>
                </div>

                <div class="col-xs-12" use="accompanyDetails" index="2" style="display:none;padding: 0px; margin: 15px 0px; border-top: 1px solid rgb(229, 229, 229);">
                    <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 2</h4>

                    <div class="col-sm-6 " actas="fieldContainer">
                        <label for="accompany_name_add_2" class="formbold-form-label">Name</label>
                        <input type="text" class="form-control formbold-form-input" name="accompany_name_add[1]" id="accompany_name_add_2" style="text-transform:uppercase;">
                        <input type="hidden" name="accompany_selected_add[1]" value="1">
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.</div>
                    </div>

                    <div class="col-sm-6" actas="fieldContainer">
                        <label class="formbold-form-label">Food Preference</label>
                        <div>
                            <label class="cus-con">Veg
                                <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[1]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Non-Veg
                                <input type="radio" name="accompany_food_choice[1]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                    </div>
                </div>

                <div class="col-xs-12" use="accompanyDetails" index="3" style="display:none;padding: 0px; margin: 15px 0px; border-top: 1px solid rgb(229, 229, 229);">
                    <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 3</h4>

                    <div class="col-sm-6" actas="fieldContainer">
                        <label for="accompany_name_add_3" class="formbold-form-label">Name</label>
                        <input type="text" class="form-control formbold-form-input" name="accompany_name_add[2]" id="accompany_name_add_3" style="text-transform:uppercase;">
                        <input type="hidden" name="accompany_selected_add[2]" value="2">
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.</div>
                    </div>

                    <div class="col-sm-6" actas="fieldContainer">
                        <label class="formbold-form-label">Food Preference</label>
                        <div>
                            <label class="cus-con">Veg
                                <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[2]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Non-Veg
                                <input type="radio" name="accompany_food_choice[2]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                    </div>
                </div>

                <div class="col-xs-12" use="accompanyDetails" index="4" style="display:none;padding: 0px; margin: 15px 0px; border-top: 1px solid rgb(229, 229, 229);">
                    <h4 class="col-xs-12 formbold-form-label" style="font-weight: 700;">ACCOMPANY 4</h4>

                    <div class="col-sm-6 ">
                        <label for="accompany_name_add_4" class="formbold-form-label">Name</label>
                        <input type="text" class="form-control formbold-form-input" name="accompany_name_add[3]" id="accompany_name_add_4" style="text-transform:uppercase;">
                        <input type="hidden" name="accompany_selected_add[3]" value="3">
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please enter a proper value.</div>
                    </div>
                    <div class="col-sm-6" actas="fieldContainer">
                        <label class="formbold-form-label">Food Preference</label>
                        <div>
                            <label class="cus-con">Veg
                                <input type="radio" groupname="accompany_food_choice" name="accompany_food_choice[3]" id="accompany_food_1_veg" value="VEG" tabindex="15">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-con">Non-Veg
                                <input type="radio" name="accompany_food_choice[3]" groupname="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="alert alert-danger" callfor="alert" style="display: none;">Please choose a proper option.</div>
                    </div>
                </div>


                <div class="clearfix"></div>

            </div>
        </div>
        <input type="button" name="previous" class="close-add-form action-button" value="Back" />
        <input type="submit" name="next" class=" action-button" value="Add" />
    </form>
</fieldset>

<script>
    $(document).on('click', '.editWorkshop', function(e) {
        e.preventDefault();
        $(".addForm form")[0].reset();
        $('.addForm').hide();
        $("#progressbar").hide();
        var userId = $(this).attr('userId');

        $.ajax({
            type: "POST",
            url: jsBASE_URL + 'exhibitor.registration.process.php',
            data: "act=getUserWorkshopDetails&userId=" + userId,
            dataType: "json",
            async: false,
            success: function(response) {
                console.log("Response:", response);

                // ============= Show the workshop form ===============
                $(".order-details-container").toggleClass("active");
                $("#msform").hide();
                $("#edit_workshop_form").show();

                if (response && response.id) {
                    const $form = $('form[name="edit_workshop_form"]');
                    $form.find(`input[name="user_id"]`).val(userId);
                    // Fill full name (if you have an input for it)
                    // if ($form.find('input[name="full_name"]').length) {
                    //     $form.find('input[name="full_name"]').val(response.full_name);
                    // }

                    // Parse workshop_id (remove quotes if any)
                    let workshopIds = response.workshop_id.replace(/"/g, '').split(',');
                    var reg_cls_id = response.registration_classification_id.replace(/"/g, '');

                    // Hide all rows first
                    $form.find('tr[operetionmode="workshopTariffTr"]').hide();
                    $form.find(`tr[use="${reg_cls_id}"]`).show();

                    // Check the corresponding radio input
                    $form.find('input[type="radio"][name="workshop_id"]').prop('checked', false);

                    // Loop through workshop IDs and check corresponding radio(s)
                    workshopIds.forEach(function(id) {
                        $form.find(`tr[use="${reg_cls_id}"] input[type="radio"][name="workshop_id"][value="${id}"]`).prop('checked', true);

                        // $form.find(`input[type="radio"][name="workshop_id"][value="${id}"]`).prop('checked', true);

                    });

                }
            }
        });


        // $(".order-details-container").toggleClass("active");
        // $('#msform').hide();
        // $('#add_accompany_form').show();


    });

    $(document).on('click', '.editAccompany', function(e) {
        e.preventDefault();
        $(".addForm form")[0].reset();
        $('.addForm').hide();
        $("#progressbar").hide();
        var userId = $(this).attr('userId');

        $.ajax({
            type: "POST",
            url: jsBASE_URL + 'exhibitor.registration.process.php',
            data: "act=getAccompanyDetails&userId=" + userId,
            dataType: "json",
            async: false,
            success: function(response) {
                console.log("Response:", response);

                if (!response) return;

                // Parse nested JSON strings safely
                let accompanyNames = [];
                let foodChoices = [];

                try {
                    accompanyNames = JSON.parse(response.accompany_name_add || "[]");
                    foodChoices = JSON.parse(response.accompany_food_choice || "[]");
                } catch (e) {
                    console.error("JSON parse error:", e);
                }

                // ============= Show the accompany form ===============
                $(".order-details-container").toggleClass("active");
                $("#msform").hide();
                $("#add_accompany_form").show();

                const $form = $('form[name="edit_accompany-form"]');
                $form.find(`input[name="user_id"]`).val(userId);
                // ===== Fill Accompany Count =====
                const accompanyCount = parseInt(response.accompanyCount || 0);
                $form.find(`input[name="accompanyCount"][value="${accompanyCount}"]`)
                    .prop("checked", true)
                    .trigger("change");

                // Hide all first
                $('[use="accompanyDetails"]').hide();

                // Show and fill based on count
                for (let i = 0; i < accompanyCount; i++) {

                    const $section = $form.find(`[use="accompanyDetails"][index="${i + 1}"]`);
                    $section.show();

                    // Fill name
                    $section.find(`input[name="accompany_name_add[${i}]"]`).val(accompanyNames[i] || "");

                    // Fill food preference
                    const food = (foodChoices[i] || "").toUpperCase();
                    if (food === "VEG" || food === "NON VEG") {
                        $section.find(`input[name="accompany_food_choice[${i}]"][value="${food}"]`)
                            .prop("checked", true);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });

        // $(".order-details-container").toggleClass("active");
        // $('#msform').hide();
        // $('#add_accompany_form').show();


    });

    $('form[name="edit_accompany-form"]').on('submit', function(e) {
        let isValid = true;

        // Show the category section
        $('.category-right').show();


        // Check if any accompany count is selected
        const $accompanyCountChecked = $(this).find("input[type='radio'][name='accompanyCount']:checked");
        if (!$accompanyCountChecked.length) {
            toastr.error('Please select an accompany', 'Error', {
                "progressBar": true,
                "timeOut": 3000,
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
            });
            isValid = false;
        }
        // If accompanyCount selected and not 0, validate food choice
        else if ($accompanyCountChecked.length && $accompanyCountChecked.val() != 0) {

            //    $("#add_accompany_form input[type='text']").each(function(index) {

            //         if (($(this).attr('type') === 'text') && !$.trim($(this).val())) {

            //             var msg = $(this).attr('validate');
            //             toastr.error('Please enter the name of the accompnaying person', 'Error', {
            //                 "progressBar": true,
            //                 "timeOut": 3000, // 3 seconds
            //                 "showMethod": "slideDown", // Animation method for showing
            //                 "hideMethod": "slideUp",
            //                 "direction": 'ltr', // Animation method for hiding
            //             });

            //             isValid = false;

            //             return false;
            //         }
            //     });



            const accompanyCount = parseInt($accompanyCountChecked.val());
            const $foodChecked = $(this).find("input[type='radio'][groupname='accompany_food_choice']:checked");

            if ($foodChecked.length < accompanyCount) {
                toastr.error('Please select a food preference', 'Error', {
                    "progressBar": true,
                    "timeOut": 3000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });
                isValid = false;
            }


        }





        // Prevent form submission if invalid
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });


    $(document).on('click', '.close-add-form', function(e) {
        $('.addForm').hide();
        $(".order-details-container").toggleClass("active");
        $('#msform').show();

    })

    $(".cta-button").click(function() {
        // $(".order-details-container").toggleClass("active");
        // var company_code= $('#exhibitor_company_code').val();
        $("#progressbar").show();
        var company_code = '<?= $company_code ?>';
        $.ajax({
            type: "POST",
            url: jsBASE_URL + 'exhibitor.registration.process.php',
            data: "act=viewRegistrationList&company_code=" + company_code,
            dataType: "html",
            async: false,
            success: function(response) {
                // console.log(response);
                if (response) {
                    if (response.trim() == 'error') {

                        alert("Seat is not available for this hotel.");
                    } else {

                        $('#reg_list').html(response);
                        $('#search_user').val('');
                        $(".order-details-container").toggleClass("active");

                    }

                }
            }
        });
    });
    $('#search_user').on('keyup', function() {
        var search_user = $('#search_user').val();
        var company_code = '<?= $company_code ?>';
        $.ajax({
            type: "POST",
            url: jsBASE_URL + 'exhibitor.registration.process.php',
            data: "act=viewRegistrationList&search_user=" + search_user + "&company_code=" + company_code,
            dataType: "html",
            async: false,
            success: function(response) {
                // console.log(response);
                if (response) {
                    if (response.trim() == 'error') {

                        alert("Seat is not available for this hotel.");
                    } else {

                        $('#reg_list').html(response);


                    }

                }
            }
        });
    })




    $(document).ready(function() {

        var liCount = $('#progressbar li').length;

        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        // Get all fieldsets with id starting with "section"
        var fieldsets = $('fieldset[id^="section"]');
        var lastFieldset = fieldsets.last();
        var last_section_id = lastFieldset.attr('id');
        // alert(last_section_id)
        var last_section_no = last_section_id.match(/\d+$/); // match last digits

        setProgressBar(current);


        $('#email_id').on('change', function() {
            var user_email_id = $.trim($("#email_id").val());
            var status = false;

            if (user_email_id != "") {

                var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (regex.test(user_email_id) == false) {
                    $('#email_div').removeClass("success_msg");
                    $('#email_div').addClass("error_msg").fadeIn();
                    $('#email_div').html('Please enter a valid e-mail id!');
                    // $("#email_id").val('');
                    $("#loaderImg").hide();
                } else {
                    $.ajax({
                        type: "POST",
                        url: jsBASE_URL + 'exhibitor.registration.process.php',
                        data: 'act=getEmailValidationStatus&email=' + user_email_id,
                        dataType: 'json',
                        async: false,
                        success: function(JSONObject) {
                            if (JSONObject.STATUS == 'IN_USE' || JSONObject.STATUS == 'IN_USE_EXHIBITOR') {
                                $('#email_div').removeClass("success_msg");
                                $('#email_div').addClass("error_msg").html('Email already registered with us!').fadeIn();
                            } else if (JSONObject.STATUS == 'AVAILABLE') {
                                // userdetails();
                                $('#email_div').addClass("success_msg");
                                $('#email_div').html('Available');
                                $('#email_div').fadeIn().delay(4000).fadeOut();
                            }
                        }
                    });
                }
            }

        });

        $('#mobile_no').on('change', function() {
            var mobile = $.trim($("#mobile_no").val());

            var status = false;

            if (mobile != "") {

                var regex = /^[0-9]{10}$/;
                if (regex.test(mobile) == false) {
                    $('#mobile_div').removeClass("success_msg");
                    $('#mobile_div').addClass("error_msg").html('Please enter a valid mobile number!').fadeIn();
                    $("#loaderImg").hide();
                } else {
                    $.ajax({
                        type: "POST",
                        url: jsBASE_URL + 'exhibitor.registration.process.php',
                        data: 'act=getMobileValidationStatus&mobile=' + mobile,
                        dataType: 'json',
                        async: false,
                        success: function(JSONObject) {
                            if (JSONObject.STATUS == 'IN_USE' || JSONObject.STATUS == 'IN_USE_EXHIBITOR') {
                                $('#mobile_div').removeClass("success_msg");
                                $('#mobile_div').addClass("error_msg").html('Mobile number is already registered with us!').fadeIn();
                            } else if (JSONObject.STATUS == 'AVAILABLE') {
                                // userdetails();
                                // $('#mobile_div').removeClass("error_msg");
                                $('#mobile_div').addClass("success_msg");
                                $('#mobile_div').html('Available');
                                $('#mobile_div').fadeIn().delay(4000).fadeOut();
                            }
                        }
                    });
                }
            }

        });

        $(".next").click(function() {

            current_fs = $(this).parent();
            var currentSection = $(this).attr('section');
            // alert(currentSection)

            if (validateSection(currentSection)) {
                if (currentSection != last_section_no) {

                    next_fs = $(this).parent().next();
                    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                    //show the next fieldset
                    next_fs.show();
                    //hide the current fieldset with style
                    current_fs.animate({
                        opacity: 0
                    }, {
                        step: function(now) {
                            // for making fielset appear animation
                            opacity = 1 - now;

                            current_fs.css({
                                'display': 'none',
                                'position': 'relative'
                            });
                            next_fs.css({
                                'opacity': opacity
                            });
                        },
                        duration: 500
                    });
                    setProgressBar(++current);
                } else {
                    $('#msform').submit();
                }

            }
        });

        $(".previous").click(function() {

            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev();

            //Remove class active
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //show the previous fieldset
            previous_fs.show();

            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;

                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previous_fs.css({
                        'opacity': opacity
                    });
                },
                duration: 500
            });
            setProgressBar(--current);
        });

        function validateSection(section) {
            // return true;
            var isValid = true;
            var accomArr = [];
            var galaDinnerDiv = " ";
            var hasExecuted = false;
            var isGalaFlag = false;

            // Mobile validation on Next button
            var mobile = $.trim($("#mobile_no").val());
            if (mobile != "") {
                var regex = /^[0-9]{10}$/;
                if (regex.test(mobile) == false) {
                    toastr.error('Please enter a valid mobile number!', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000, // 3 seconds
                        "showMethod": "slideDown", // Animation method for showing
                        "hideMethod": "slideUp",
                        "direction": 'ltr', // Animation method for hiding
                    });
                    isValid = false;
                    return false;

                } else {
                    $.ajax({
                        type: "POST",
                        url: jsBASE_URL + 'exhibitor.registration.process.php',
                        data: 'act=getMobileValidationStatus&mobile=' + mobile,
                        dataType: 'json',
                        async: false,
                        success: function(JSONObject) {
                            if (JSONObject.STATUS == 'IN_USE' || JSONObject.STATUS == 'IN_USE_EXHIBITOR') {
                                toastr.error('Mobile number is already registered with us!', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown", // Animation method for showing
                                    "hideMethod": "slideUp",
                                    "direction": 'ltr', // Animation method for hiding
                                });
                                isValid = false;
                                return false;
                            }
                        }
                    });
                }
            }

            // Email validation on Next button
            var user_email_id = $.trim($("#email_id").val());
            if (user_email_id != "") {

                var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (regex.test(user_email_id) == false) {
                    toastr.error('Please enter a valid e-mail id!', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp",
                        "direction": 'ltr',
                    });
                    isValid = false;
                    return false;
                } else {
                    $.ajax({
                        type: "POST",
                        url: jsBASE_URL + 'exhibitor.registration.process.php',
                        data: 'act=getEmailValidationStatus&email=' + user_email_id,
                        dataType: 'json',
                        async: false,
                        success: function(JSONObject) {
                            if (JSONObject.STATUS == 'IN_USE' || JSONObject.STATUS == 'IN_USE_EXHIBITOR') {

                                toastr.error('Email already registered with us!', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000,
                                    "showMethod": "slideDown",
                                    "hideMethod": "slideUp",
                                    "direction": 'ltr',
                                });
                                isValid = false;
                                return false;

                            }
                        }
                    });
                }
            }

            $("#section" + section + " input[type='text'],#section" + section + " textarea, #section" + section + " input[type='radio'], #section" + section + " input[type='checkbox'], #section" + section + " select").each(function(index) {

                //alert($(this).attr('type'));
                // alert($("input[type='checkbox'][name='registration_classification_id']:checked").length == 0)

                if (($("input[type='checkbox'][name='registration_classification_id']:checked").length == 0) && section == 1) {
                    var msg = $(this).attr('validate');
                    toastr.error(msg, 'Error', {
                        "progressBar": true,
                        "timeOut": 3000, // 3 seconds
                        "showMethod": "slideDown", // Animation method for showing
                        "hideMethod": "slideUp",
                        "direction": 'ltr', // Animation method for hiding
                    });
                    isValid = false;
                    return false;
                }

                if (($(this).attr('type') === 'text' || $('textarea')) && !$.trim($(this).val())) {

                    if (section == 1) {

                        var msg = $(this).attr('validate');
                        toastr.error(msg, 'Error', {
                            "progressBar": true,
                            "timeOut": 3000, // 3 seconds
                            "showMethod": "slideDown", // Animation method for showing
                            "hideMethod": "slideUp",
                            "direction": 'ltr', // Animation method for hiding
                        });

                        isValid = false;

                        return false;

                    }

                    // if (section == 4) {
                    //     if ($("input[type='checkbox'][name='accompanyCount']:checked").length) {

                    //         var msg = $(this).attr('validate');
                    //         toastr.error(msg, 'Error', {
                    //             "progressBar": true,
                    //             "timeOut": 3000, // 3 seconds
                    //             "showMethod": "slideDown", // Animation method for showing
                    //             "hideMethod": "slideUp",
                    //             "direction": 'ltr', // Animation method for hiding
                    //         });

                    //         isValid = false;

                    //         return false;
                    //     }

                    //     // 
                    // }


                } else if ($(this).attr('type') === 'radio') {

                    if (section == 1) {
                        $('.category-right').show();
                        // Check if at least one radio button is checked
                        if (!$("input[name='" + $(this).attr('name') + "']:checked").length) {

                            //console.log("Please select a value for radio button " + $(this).attr('name'));
                            toastr.error('Please select a gender', 'Error', {
                                "progressBar": true,
                                "timeOut": 3000, // 3 seconds
                                "showMethod": "slideDown", // Animation method for showing
                                "hideMethod": "slideUp" // Animation method for hiding
                            });

                            isValid = false;
                            return false;
                        }
                    }
                    if (section == 2) {
                        $('.category-right').show();
                        // Check if at least one radio button is checked
                        // if (!$("input[name='workshop_id']:checked").length) {

                        //     //console.log("Please select a value for radio button " + $(this).attr('name'));
                        //     toastr.error('Please select a workshop', 'Error', {
                        //         "progressBar": true,
                        //         "timeOut": 3000, // 3 seconds
                        //         "showMethod": "slideDown", // Animation method for showing
                        //         "hideMethod": "slideUp" // Animation method for hiding
                        //     });

                        //     isValid = false;
                        //     return false;
                        // }
                    }

                    if (section == 3) {
                        $('.category-right').show();
                        if (!$("input[type='radio'][name='accompanyCount']:checked").length) {

                            toastr.error('Please select a accompany', 'Error', {
                                "progressBar": true,
                                "timeOut": 3000, // 3 seconds
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });

                            isValid = false;
                            return false;

                        } //end if
                        else if ($("input[type='radio'][name='accompanyCount']:checked").length && $("input[type='radio'][name='accompanyCount']:checked").val() != 0) {

                            if ($("input[type='radio'][groupname='accompany_food_choice']:checked").length < $("input[type='radio'][name='accompanyCount']:checked").length) {
                                toastr.error('Please select a food preference', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown", // Animation method for showing
                                    "hideMethod": "slideUp" // Animation method for hiding
                                });

                                isValid = false;
                                return false;

                            }

                        } //else if



                    }

                    if (section == 5) {

                        // Check if at least one radio button is checked
                        if (!$("input[name='accommodation_details']:checked").length) {

                            //console.log("Please select a value for radio button " + $(this).attr('name'));
                            toastr.error('Please select a package', 'Error', {
                                "progressBar": true,
                                "timeOut": 3000, // 3 seconds
                                "showMethod": "slideDown", // Animation method for showing
                                "hideMethod": "slideUp" // Animation method for hiding
                            });

                            isValid = false;
                            return false;
                        }
                    }


                } else if ($(this).is('select')) {

                    if ($(this).attr('name') == 'hotel_select_acco_id' && $(this).val() == "") {
                        // alert(3)
                        toastr.error('Please select a hotel', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000, // 3 seconds
                            "showMethod": "slideDown", // Animation method for showing
                            "hideMethod": "slideUp" // Animation method for hiding
                        });

                        isValid = false;
                        return false;
                    }

                    if ($(this).attr('name') === 'accomodation_checkin_id' && $(this).val() == "") {

                        toastr.error('Please select a Checkin Date', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000, // 3 seconds
                            "showMethod": "slideDown", // Animation method for showing
                            "hideMethod": "slideUp" // Animation method for hiding
                        });

                        isValid = false;
                        return false;
                    }
                    if ($(this).attr('name') === 'accomodation_checkout_id' && $(this).val() == "") {

                        toastr.error('Please select a Checkout Date', 'Error', {
                            "progressBar": true,
                            "timeOut": 3000, // 3 seconds
                            "showMethod": "slideDown", // Animation method for showing
                            "hideMethod": "slideUp" // Animation method for hiding
                        });

                        isValid = false;
                        return false;
                    }
                    // }
                } else if ($(this).attr('type') === 'checkbox') {

                    if (section == 2) {
                        $('.category-right').show();
                        // Check if at least one radio button is checked
                        if (!$("input[name='workshop_id']:checked").length) {

                            //console.log("Please select a value for radio button " + $(this).attr('name'));
                            toastr.error('Please select a workshop', 'Error', {
                                "progressBar": true,
                                "timeOut": 3000, // 3 seconds
                                "showMethod": "slideDown", // Animation method for showing
                                "hideMethod": "slideUp" // Animation method for hiding
                            });

                            isValid = false;
                            return false;
                        }
                    }

                    if (section == 5) {
                        $('.category-right').hide();
                        //alert(12);
                        var isAnyChecked = isAnyCheckboxChecked('checkboxClassDinner');
                        if (isAnyChecked) {
                            console.log('At least one checkbox is checked.');
                        } else {
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
                } else if ($(this).prop('tagName').toLowerCase() === 'select') {

                    if ($.trim($(this).val()) == '') {

                        var msg = $(this).attr('validate');
                        toastr.error(msg, 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });

                        isValid = false;
                        return false;

                    }
                } else if ($(this).attr('type') === 'text' && $.trim($(this).val()) && section == 4) {


                    var user_first_name = $('#user_first_name').val();
                    var user_last_name = $('#user_last_name').val();
                    var fullname = user_first_name + " " + user_last_name;

                    var countindex = Number($(this).attr('countindex')) + 1;
                    var countQty = Number(countindex) + 1;

                    //console.log(dinnerTariffArray);
                    $('#dinner_name').text(fullname);

                    var dinner_amnt_display = $('#dinner_amnt_display').val();
                    var dinner_amnt = $('#dinner_amnt').val();
                    var dinner_classification_id = $('#dinner_classification_id').val();
                    var dinner_title = $('#dinner_title').val();
                    var dinner_hotel_name = $('#dinner_hotel_name').val();
                    var dinner_hotel_link = $('#dinner_hotel_link').val();
                    var dinner_date = $('#dinner_date').val();

                    var decrementCount = Number(countindex) - 1;

                    //alert(countindex);

                    galaDinnerDiv += '<div class="gala-row"><div class="d-flex align-items-center"><div class="gala-inner-lt"><div class="gala-inner"><div class="gala-box"><img src="<?= _BASE_URL_ ?>images/gala-logo.png" alt="" class="gold-img" /></div><div class="gala-name"><h5 id="dinner_name">' + $(this).val() + '</h5></div></div><div class="gala-location"><ul><li><a  href="' + dinner_hotel_link + '" target="_blank"><img src="<?= _BASE_URL_ ?>images/loction.png" alt="" /> ' + dinner_hotel_name + '</a></li><li><img src="<?= _BASE_URL_ ?>images/calender.png" alt="" /> ' + dinner_date + '</li></ul></div></div><div class="gala-inner-rt"><div class="gala-main"><div class="acc-gala-price">' + dinner_amnt_display + '</div></div></div></div><div class="select-dinner d-flex align-items-center"><div class="custom-checkbox"><input type="checkbox" name="accompany_dinner_value[' + decrementCount + ']" id="dinner_value' + decrementCount + '" value="' + dinner_classification_id + '" operationMode="dinner" use="dinner" amount="' + dinner_amnt + '" invoiceTitle="' + dinner_title + '-Accompany' + countindex + '" icon="images/ac4.png" class="checkboxClassDinner" qty=' + countQty + '><label for="dinner_value' + decrementCount + '" >Please choose Now</label></div> </div></div>';

                    isGalaFlag = true;


                } else {
                    //$('.category-right').hide();

                }
                if ($(this).attr('name') == 'hotel_select_acco_id' && $(this).val() == "" && section == 5) {
                    // alert(3)
                    toastr.error('Please select a hotel', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000, // 3 seconds
                        "showMethod": "slideDown", // Animation method for showing
                        "hideMethod": "slideUp" // Animation method for hiding
                    });

                    isValid = false;
                    return false;
                }
                if ($(this).attr('name') === 'accomodation_checkin_id' && $(this).val() == "") {

                    toastr.error('Please select a Checkin Date', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000, // 3 seconds
                        "showMethod": "slideDown", // Animation method for showing
                        "hideMethod": "slideUp" // Animation method for hiding
                    });

                    isValid = false;
                    return false;
                }
                if ($(this).attr('name') === 'accomodation_checkout_id' && $(this).val() == "") {

                    toastr.error('Please select a Checkout Date', 'Error', {
                        "progressBar": true,
                        "timeOut": 3000, // 3 seconds
                        "showMethod": "slideDown", // Animation method for showing
                        "hideMethod": "slideUp" // Animation method for hiding
                    });

                    isValid = false;
                    return false;
                }


            });

            //$('.gala-dinner-select').append(galaDinnerDiv); 
            if (isGalaFlag) {
                $('#gala-dinner-select1').empty().append(galaDinnerDiv);
            }


            return isValid;


        }

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
            // calculateTotalAmount();
        });



        // $("#hotel_select_id").on('change', function() {
        //     var hotel_id = $(this).val();
        //     $('.holel_list_combo').hide();
        //     if (hotel_id != '') {

        //         $('#hotelErr').hide();
        //         $('.combo_items' + hotel_id).show();
        //     } else {
        //         // alert(12);
        //         $('#hotelErr').show();
        //     }
        // });

        $('#hotel_select_acco_id').on('change', function() {

            var hotel_id = $(this).val();
            var company_code = '<?= $company_code ?>';
            //  alert(company_code)
            var room_type = $('#room_type').val();
            var regClsId = $('input[name="registration_classification_id"]:checked').val();
            // alert(regClsId)


            // act="getGeneralAcommodationPackage";
            act = "getExhibitorAcommodationPackage";

            var url = "https://ruedakolkata.com/wboacon_2025/exhibitor.registration.process.php";
            if (hotel_id != '') {

                $.ajax({
                    type: "POST",
                    url: url,
                    data: "act=" + act + "&hotel_id=" + hotel_id + "&regClsId=" + regClsId + '&company_code=' + company_code,
                    dataType: "html",
                    async: false,
                    success: function(JSONObject) {
                        console.log(JSONObject);
                        if (JSONObject) {
                            console.log(JSONObject);
                            if (JSONObject.trim() == 'error') {

                                alert("Seat is not available for this hotel.");
                            } else {

                                $('#packageDiv').html(JSONObject);
                                $('#packageDiv').show();
                            }

                        } else {
                            $('#packageDiv').html('');
                            $('#packageDiv').hide();
                        }

                    }
                });
            } else {

                $('#packageDiv').html('');
                $('#packageDiv').hide();
            }


        });



        function setProgressBar(curStep) {
            var percent = parseFloat(100 / steps) * curStep;
            percent = percent.toFixed();
            $(".progress-bar")
                .css("width", percent + "%")
        }

        $(".submit").click(function() {
            return false;
        })

    });

    function hotelRoomRetriver(obj) {
        // var sequenceVal = $(obj).attr("sequence");
        var hotelId = $(obj).val();

        var room_control = $(obj).attr("room_type");
        // var roomControlDiv = $('#' + roomControl).parent().closest("div[use=roomContainer]");

        if (hotelId != "") {
            var act = 'getHotelRoomType';
            $.ajax({
                type: "POST",
                url: "https://ruedakolkata.com/wboacon_2025/exhibitor.registration.process.php",
                data: 'act=getHotelRoomType&hotelId=' + hotelId + '&room_control=' + room_control,
                dataType: "html",
                async: false,
                success: function(message) {

                    if (message != "") {
                        // $(roomControlDiv).html('');
                        // $(roomControlDiv).html(message);
                        $('.roomContainer').html('');
                        $('.roomContainer').html(message);
                    }
                }
            });
        }
    }

    function get_checkin_val(val) {
        if (typeof val !== 'undefined' && val != '') {
            var checkOutVal = $('#accomodation_package_checkout_id').val("");
        }
    }

    function getPackageVal(val) {
        if (typeof val !== 'undefined' && val != '') {

            var checkInVal = $('#accomodation_package_checkin_id').val("");
            var checkOutVal = $('#accomodation_package_checkout_id').val("");
            // calculateTotalAmount();
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

        if (date1 >= date2) {
            alert("Please select proper checkout date!");
            $('#accomodation_package_checkout_id').val('');
            return false;
        }

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
                // calculateTotalAmount();

            }
        });


    }

    // Designed by: Hoang Nguyen
    // Original image: https://dribbble.com/shots/5919154-Tab-Bar-Label-Micro-Interaction

    const buttons = document.querySelectorAll(".menu__item");
    let activeButton = document.querySelector(".menu__item.active");

    buttons.forEach(item => {

        const text = item.querySelector(".menu__text");
        setLineWidth(text, item);

        window.addEventListener("resize", () => {
            setLineWidth(text, item);
        })

        item.addEventListener("click", function() {
            if (this.classList.contains("active")) return;

            this.classList.add("active");

            if (activeButton) {
                activeButton.classList.remove("active");
                activeButton.querySelector(".menu__text").classList.remove("active");
            }

            handleTransition(this, text);
            activeButton = this;

        });


    });


    function setLineWidth(text, item) {
        const lineWidth = text.offsetWidth + "px";
        item.style.setProperty("--lineWidth", lineWidth);
    }

    function handleTransition(item, text) {

        item.addEventListener("transitionend", (e) => {

            if (e.propertyName != "flex-grow" ||
                !item.classList.contains("active")) return;

            text.classList.add("active");

        });

    }
    $(".action-button").hover(function() {
        $(".action-button").addClass("pad-deactive")
        $(this).addClass("pad-active").removeClass("pad-deactive");
    }, function() {
        $(".action-button").removeClass("pad-active pad-deactive");
    });
    $(".frm-lft-btn").click(function() {
        $(".frm-lft").toggleClass("frm-lft-active")
        $(this).toggleClass("frm-btn-active")
    });
</script>

</html>