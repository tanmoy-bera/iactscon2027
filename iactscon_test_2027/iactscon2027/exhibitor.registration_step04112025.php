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
// echo '<pre>'; print_r($conferenceTariffArray);die;
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
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>new_style.css" />
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _DIR_CM_JSCRIPT_ . "website/" ?>login.js?x=<?php echo rand(0, 100) ?>"></script>
    <!-- <script src="<?= _BASE_URL_ ?>js/website/returnData.process.js"></script> -->
</head>

<body>
    <?php
    $sqlSuccessImg    =   array();
    $sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Exhibitor Bulk background' AND status='A' ";

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
                                    <path d="M811 1534 c-125 -33 -228 -118 -282 -231 -28 -58 -34 -82 -37 -159 -5 -109 12 -178 62 -253 19 -28 31 -52 28 -54 -4 -2 -47 -12 -95 -22 -111 -23 -180 -63 -224 -128 -46 -69 -53 -103 -53 -278 0 -150 1 -156 23 -177 30 -28 68 -28 95 1 21 22 22 33 22 173 0 227 8 236 237 288 148 33 153 33 195 18 58 -22 208 -22 266 0 41 15 47 15 195 -18 229 -51 237 -61 237 -290 0 -144 1 -151 23 -172 30 -29 62 -28 92 3 24 23 25 28 25 177 0 172 -7 206 -53 275 -44 65 -113 105 -224 128 -48 10 -91 20 -95 22 -4 2 6 22 22 45 41 56 70 155 70 238 0 160 -85 303 -222 375 -101 53 -206 66 -307 39z m216 -149 c63 -26 111 -73 144 -140 28 -58 31 -