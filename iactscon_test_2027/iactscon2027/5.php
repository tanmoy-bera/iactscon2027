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

//$mycms->redirect("login.php");

$loginDetails    = login_session_control();
$delegateId    = $loginDetails['DELEGATE_ID'];
$rowUserDetails  = getUserDetails($delegateId);
$title = 'Profile';

$invoiceList   = getConferenceContents($delegateId);
$accompanyDtlsArr  = $invoiceList[$delegateId]['ACCOMPANY'];

$section = $_REQUEST['section'];
if (empty($section) && $section == '') {

    $mycms->redirect(_BASE_URL_ . "profile.php");
}

if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {
    $abstractDelegateId    = trim($_REQUEST['abstractDelegateId']);
    $userRec = getUserDetails($abstractDelegateId);
}


$sql     =    array();
$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$result = $mycms->sql_select($sql);
$row             = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];
if ($row['logo_image'] != '') {
    $emailHeader  = $header_image;
}


$sqlIcon  = array();
$sqlIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                      WHERE `status`='A' AND `purpose`='Registration' order by id ";
//$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultIcon = $mycms->sql_select($sqlIcon);

$sqlLogo    =   array();
$sqlLogo['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
			    WHERE title='Online Payment Logo' ";

$resultLogo      = $mycms->sql_select($sqlLogo);
$logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultLogo[0]['image'];

?>
<!DOCTYPE html>
<html lang="en">

<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();

include_once("header.php");

?>

<style type="text/css">
    .section {
        display: none;
    }

    .active {
        display: block;
    }
</style>

<body class="single inner-page">
    <div id="loading_indicator" style="display:none;"> </div>
    <?
    $cutoffs             = fullCutoffArray();
    $currentCutoffId     = getTariffCutoffId();
    $dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);

    //echo '<pre>'; print_r($dinnerTariffArray);
    ?>


    <main>

        <?php include_once('sidebar_icon.php'); ?>

        <section class="main-section profile-add-area">
            <div class="container">
                <div class="inner-greadient-sec">
                    <div class="row">

                        <div class="col-lg-6 p-0">
                            <!-- <div class="cart1111">
                            <img src="<?= _BASE_URL_ ?>images/cart.png" alt="">
                          </div> -->
                            <?php /*?>
                            <div class="category-head">
                                <div class="category-left">
                                    <h3 id="pageTitle"><?php if(!empty($section) && $section==3){ echo 'Register to workshop'; } ?></h3>
                                </div>
                                <div class="category-right">
                                    <h4><i><?=getCutoffName($currentCutoffId)?></i></h4>
                                    <p>till <?php 
                              $endDate = getCutoffEndDate($currentCutoffId);
                              echo date('jS M `y', strtotime($endDate));?>
                                    </p>
                                </div>
                            </div> <?php */ ?>

                            <div class="category-table-row section" id="section1" style="display: none;">

                                <?php
                                if ($currentCutoffId > 0) {

                                    $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                                    $workshopDetailsArray   = getAllWorkshopTariffs($currentCutoffId);
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
                                ?>

                                            <label class="category-check" data-aos="fade-up" data-aos-delay="0" data-aos-duration="100">
                                                <input type="radio" name="registration_classification_id[]" id="registration_classification_id" operationMode="registration_tariff" operationModeType="conference" reg="reg" value="<?= $registrationDetailsVal['REG_CLASSIFICATION_ID'] ?>" currency="<?= $registrationDetailsVal['CURRENCY'] ?>" amount="<?= $registrationDetailsVal['AMOUNT'] ?>" invoiceTitle="Registration - <?= $registrationDetailsVal['CLASSIFICATION_TITTLE'] ?>" <?= $disableClass ?> icon="<?= $icon ?>">
                                                <div class="category-select-div">
                                                    <div class="cate-select-img">
                                                        <img src="<?= $icon ?>" alt="" />
                                                        <h4><?= $registrationDetailsVal['CLASSIFICATION_TITTLE'] ?></h4>
                                                    </div>
                                                    <h5>
                                                        <?
                                                        if (floatval($registrationDetailsVal['AMOUNT']) > 0) {
                                                            echo $registrationDetailsVal['CURRENCY'] . ' <b>' . number_format(($registrationDetailsVal['AMOUNT'])) . '</b>';
                                                        } else {
                                                            echo "Complimentary";
                                                        }
                                                        ?>
                                                    </h5>
                                                </div>
                                            </label>

                                <?php

                                        } // end if

                                    }  //end foreach
                                }
                                ?>



                                <div class="next-prev-btn-wrap d-flex justify-content-between pr-5 pt-20">
                                    <a class="btn next-btn prev"><img src="images/left-arrow.png" alt=""></a>
                                    <a class="btn next-btn next" title="User Details"><img src="images/right-arrow.png" alt=""></a>
                                </div>

                            </div>
                            <div class="pd-row section" use="registrationUserDetails" id="section2" style="display: none;">

                                <div class="row">
                                    <div class="col-lg-12 form mb-3">

                                        <input type="text" class="form-control" name="user_email_id" id="user_email_id" value="<?= ($userRec['user_email_id'] != '') ? ($userRec['user_email_id']) : '' ?>" placeholder="Email" validate="Please Enter Email Address" onChange="checkUserEmail(this);" onblur="checkUserEmail(this);" autocomplete="off">
                                    </div>

                                    <div class="col-lg-12 form mb-3 select-title">
                                        <div class="d-flex">
                                            <select name="user_initial_title" class="col-lg-2">
                                                <option value="Mr">Mr.</option>
                                                <option value="Mrs">Mrs.</option>
                                                <option value="Dr">Dr.</option>
                                            </select>
                                            <div class="input-wrap col-lg-10">
                                                <div class="form-floating">

                                                    <input type="text" class="form-control" name="user_mobile" id="user_mobile" value="<?= ($userRec['user_mobile_isd_code'] != '') ? ($userRec['user_mobile_isd_code']) : '' ?><?= ($userRec['user_mobile_no'] != '') ? ($userRec['user_mobile_no']) : '' ?>" disabled="disabled" maxlength="10" onkeypress="return isNumber(event)" required placeholder="Mobile" validate="Please Enter Mobile Number" onChange="validateMobile(this.value)" onblur="validateMobile(this.value)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 form mb-3">

                                        <input type="text" class="form-control" placeholder="First Name" name="user_first_name" id="user_first_name" validate="Please Enter First Name" autocomplete="off" disabled="disabled">
                                    </div>

                                    <div class="col-lg-12 form mb-3">

                                        <input type="text" class="form-control" placeholder="Last Name" name="user_last_name" id="user_last_name" value="<?= ($userRec['user_last_name'] != '') ? ($userRec['user_last_name']) : '' ?>" validate="Please Enter Last Name" autocomplete="off" disabled="disabled">
                                    </div>

                                    <div class="col-lg-6 form mb-3">

                                        <select class="form-control select" name="user_country" id="user_country" forType="country" required disabled="disabled" validate="Please Select Country">
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
                                                    <option value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $userRec['user_country_id']) ? 'selected' : '' ?>>
                                                        <?= $rowFetchCountry['country_name'] ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 form mb-3">

                                        <select class="form-control select" name="user_state" id="user_state" forType="state" required disabled="disabled" validate="Please Select State">
                                            <option value="">-- Select Country First --</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-12 form mb-3">

                                        <input type="text" class="form-control" name="user_city" id="user_city" value="<?= ($userRec['user_city'] != '') ? ($userRec['user_city']) : '' ?>" placeholder="City" autocomplete="off" disabled="disabled" validate="Please Enter City">
                                    </div>

                                    <div class="col-lg-6 mb-3 d-flex align-items-center">
                                        <label>Gender : </label>
                                        <label class="custom-radio"> Male <input type="radio" groupname="user_gender" name="user_gender" id="user_gender_male" value="Male" disabled="disabled" required=""><span class="checkmark"></span></label>



                                        <label class="custom-radio">Female <input type="radio" groupname="user_gender" name="user_gender" id="user_gender_female" value="Female" disabled="disabled"><span class="checkmark"></span> </label>
                                    </div>
                                    <div class="col-lg-6 mb-3 total-price">
                                        <strong>&#8377; <span id="confPrc">27000.00</span></strong>
                                    </div>
                                </div>


                                <div class="next-prev-btn-wrap d-flex justify-content-between pr-5">
                                    <a class="btn next-btn prev" title="Select Category"><img src="images/left-arrow.png" alt=""></a>
                                    <a class="btn next-btn btn-w next" id="user_details" style="display: none;" title="Select Workshop"><img src="images/right-arrow.png" alt=""></a>
                                </div>


                            </div>
                            <div class="category-table-rowxx drama-total-holder add-inclusion section" id="section3">
                                <form name="frmAddWorkshopfromProfile" id="frmAddWorkshopfromProfile" action="registration.process.php" method="post">
                                    <input type="hidden" name="act" value="add_workshop">
                                    <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" cutoffid="1">
                                    <input type="hidden" name="delegateClasfId" value="<?= $rowUserDetails['registration_classification_id'] ?>">
                                    <input type="hidden" id="registrationRequest" name="registrationRequest" value="GENERAL">
                                    <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />
                                    <?php

                                    $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                                    $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
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

                                    if (isset($workshopRegChoices['MASTER CLASS']) && sizeof($workshopRegChoices['MASTER CLASS']) > 0) {

                                        $loopcount = 0;
                                        foreach ($workshopRegChoices['MASTER CLASS'] as $keyWorkshopclsf => $rowWorkshopclsf) {

                                            foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {
                                                //echo '<pre>'; print_r($rowRegClasf);
                                                if ($keyRegClasf == $rowUserDetails['registration_classification_id']) {



                                                    $workshopCount = getWorkshopClassificationSeatLimit($rowRegClasf['WORKSHOP_ID']);

                                                    $style  = "";
                                                    $span = "";
                                                    $spanCss =  '';
                                                    if ($workshopCount < 1) {
                                                        $style = 'disabled="disabled"';
                                                        $span  = '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
                                                        $spanCss = 'style="cursor:not-allowed;"';
                                                    }

                                                    $workshopRateDisplay = $rowRegClasf[$rowRegClasf['CURRENCY']] . '<br>' . $rowRegClasf['CURRENCY'];

                                                    if ($rowRegClasf[$rowRegClasf['CURRENCY']] == 0 /*&& $rowRegClasf['WORKSHOP_ID']!=5*/) {
                                                        $workshopRateDisplay = "Included in Registration";
                                                    }

                                                    $displayName = '';
                                                    $len     = strlen($rowRegClasf['WORKSHOP_NAME']);
                                                    if ($len > 40) {
                                                        $charCount = 0;
                                                        $lines = array();
                                                        $words = explode(' ', $rowRegClasf['WORKSHOP_NAME']);
                                                        foreach ($words as $kk => $word) {
                                                            $charCount += strlen($word);
                                                            if ($charCount > 40) {
                                                                $charCount = strlen($word);
                                                                $lines[] = '<br/>';
                                                            }
                                                            $lines[] = $word;
                                                        }
                                                        $displayName = implode(' ', $lines);
                                                    } else {
                                                        $displayName = $rowRegClasf['WORKSHOP_NAME'];
                                                    }

                                                    $workshop_date = new DateTime($rowRegClasf['WORKSHOP_DATE']);

                                                    // Format the date as "jS M" (day with suffix and abbreviated month)
                                                    $formattedDate = $workshop_date->format('jS');

                                                    //echo $workshop_date->format('F');
                                    ?>

                                                    <div class="accompany-row " use="<?= $keyRegClasf ?>" operetionMode="workshopTariffTr" style="display:block;" operetionDisplay="workshopDisplay<?= $rowRegClasf['WORKSHOP_ID'] ?>" class="workCombo">
                                                        <div class="custom-checkbox accompanying ">
                                                            <input type="radio" name="workshop_id[]" id="workshop_id_<?= $keyWorkshopclsf . '_' . $keyRegClasf ?>" value="<?= $rowRegClasf['WORKSHOP_ID'] ?>" <?= $style ?> workshopName="<?= $rowRegClasf['WORKSHOP_GRP'] ?>" operationMode="workshopId" amount="<?= $rowRegClasf[$rowRegClasf['CURRENCY']] ?>" invoiceTitle="Workshop - <?= $rowRegClasf['WORKSHOP_NAME'] ?>" registrationClassfId="<?= $keyRegClasf ?>" workshopCount="<?= $workshopCount ?>" icon="images/cat-ic-2.png">
                                                            <label for="workshop_id_<?= $keyWorkshopclsf . '_' . $keyRegClasf ?>">
                                                                <div class="por-row">
                                                                    <div class="por-lt">
                                                                        <div class="por-date">
                                                                            <h3><?= $formattedDate ?></h3>
                                                                            <p><?= $workshop_date->format('F'); ?></p>
                                                                        </div>

                                                                        <div class="por-inner">
                                                                            <h3><?= $displayName ?></h3>
                                                                        </div>

                                                                        <div class="por-location">
                                                                            <ul>
                                                                                <li><a href="<?= $rowRegClasf['VENUE_ADDRESS'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/loction.png" alt="" /> <?= $rowRegClasf['VENUE'] ?></a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="por-rt">
                                                                        <div class="gala-main">
                                                                            <div class="acc-gala-price">
                                                                                <span><?= $workshopRateDisplay ?>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>



                                        <?php
                                                }
                                            }
                                        }
                                        ?>


                                        <div class="next-prev-btn-wrap d-flex justify-content-between pr-5 pt-20">
                                            <a href="<?= _BASE_URL_ ?>profile.php" class="btn next-btn prev"><img src="images/left-arrow.png" alt=""></a>
                                            <!--<a  class="btn next-btn skip" >Skip</a> -->
                                            <a class="btn next-btn next" formPay="frmAddWorkshopfromProfile">Pay Now</a>
                                        </div>

                                    <?php
                                    }
                                    ?>

                                    <div class="checkout-main-wrap" id="checkout-main-wrap">
                                        <div class="checkout-popup">
                                            <div class="card-details">
                                                <div class="card-details-inner custom-radio-holder">
                                                    <img src="<?= $logo ?>" alt="" />

                                                    <div class="redio-select" style="display: flex;">

                                                        <input type="hidden" name="registrationMode" id="registrationMode">
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card<?= $section ?>" paymentMode='ONLINE' formPay="frmAddWorkshopfromProfile">
                                                            Card<span class="checkmark"></span>
                                                        </label>
                                                        <?php
                                                        $offline_payments = json_decode($cfg['PAYMENT.METHOD']);
                                                        if (in_array("Cheque", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddWorkshopfromProfile">
                                                                Cheque<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Draft", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddWorkshopfromProfile">
                                                                Draft<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Neft", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddWorkshopfromProfile">
                                                                NEFT<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Rtgs", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddWorkshopfromProfile">
                                                                RTGS<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Cash", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddWorkshopfromProfile">
                                                                Cash<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Upi", $offline_payments)) {
                                                        ?>

                                                            <!-- UPI Payment Option Added By Weavers start -->
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI<?= $section ?>" paymentMode='OFFLINE' section="<?= $section ?>" formPay="frmAddWorkshopfromProfile">
                                                                UPI<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        ?>
                                                        <!-- UPI Payment Option Added By Weavers end -->
                                                        &nbsp;
                                                    </div>
                                                    <div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card<?= $section ?>" actAs='fieldContainer'>
                                                        <div class="checkbox">

                                                            <div>
                                                                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                    <img src="<?= _BASE_URL_ ?>images/international_globe.png" height="20px;">
                                                                    International Card
                                                                    <input type="radio" name="card_mode" use="card_mode_select" value="International">
                                                                    <span class="checkmark">
                                                                </label>
                                                                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                    <img src="<?= _BASE_URL_ ?>images/india_globe.png" height="20px;">
                                                                    Indian Card
                                                                    <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
                                                                    <span class="checkmark">
                                                                </label>
                                                                &nbsp;
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="card-form" id="paymentDetailsSection" style="display: none;">

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <h6>Payment Details</h6>
                                                            </div>
                                                            <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Cash<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Money Order Sent Date</label>  -->
                                                                <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">

                                                            </div>
                                                            <!-- UPI Payment Option Added By Weavers start -->
                                                            <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="txn_no">UPI Transaction ID</label>  -->
                                                                <input type="text" class="form-control" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="UPI Transaction">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                                <!--  <label for="txn_no">UPI Payment Date</label>  -->
                                                                <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select upi date" placeholder="Payment Date">

                                                            </div>
                                                            <!-- Cheque Payment Option Added By Weavers start -->

                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Cheque No</label> -->
                                                                <input type="text" class="form-control" name="cheque_number" id="cheque_number" validate="Please enter cheque number" placeholder="Cheque No">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Drawee Bank</label>  -->
                                                                <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Bank Name">

                                                            </div>
                                                            <div class="col-lg-6 " style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Cheque Date</label> -->
                                                                <input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">

                                                            </div>

                                                            <!-- NEFT Payment Option Added By Weavers start -->
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Transaction Id</label> -->
                                                                <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Transaction Id">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Drawee Bank</label> -->
                                                                <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name" validate="Please enter neft bank" placeholder="Bank Name">

                                                            </div>
                                                            <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!--  <label for="user_first_name">Date</label> -->
                                                                <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">

                                                            </div>
                                                        </div>

                                                    </div>


                                                    <div class="policy-div">
                                                        <ul>
                                                            <li><a href="">Cancellation Policy</a></li>
                                                            <li><a href="">Privacy Policy</a></li>
                                                        </ul>
                                                    </div>

                                                    <div class="righr-btn"><input type="button" class="pay-button" id="pay-button" onclick="payNow('3','frmAddWorkshopfromProfile')" value="Pay Now" style="display:none;" section="<?= $section ?>" formName="frmAddWorkshopfromProfile"></div>


                                                </div>
                                            </div>
                                            <div class="cart-details">
                                                <div class="cart-heading">
                                                    <h5>Order Summary</h5>

                                                    <a href="#" class="close-check"><span>&#10005;</span> close</a>
                                                </div>

                                                <div class="cart-data-row add-inclusion" use="totalAmount">

                                                    <table class="table bill" use="totalAmountTable" id="bill_details" style="display:none;">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>DETAIL</th>
                                                                <th align="right" style="text-align:right;">AMOUNT (INR)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                        <tfoot>
                                                            <tr style="display:none;" use='rowCloneable'>
                                                                <td> <span use="icon"></span></td>
                                                                <td> <span use="invTitle"></span></td>
                                                                <td align="right"><span use="amount">0.00</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Total</td>
                                                                <td align="right"><span use='totalAmount'>0.00</span></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>

                            </div>

                            <div class="category-table-rowxx drama-total-holder section" id="section4">

                                <form name="frmAddAccompanyfromProfile" id="frmAddAccompanyfromProfile" action="registration.process.php" method="post">



                                    <?php
                                    $accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 


                                    $registrationAmount   = getCutoffTariffAmnt($currentCutoffId);
                                    $registrationCurrency   = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];



                                    ?>

                                    <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" cutoffid="<?= $currentCutoffId ?>" />
                                    <input type="hidden" name="accompanyClasfId" value="<?= $accompanyCatagory ?>" />
                                    <input type="hidden" name="act" value="add_accompany" />
                                    <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />

                                    <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />

                                    <div class="heading-ar">
                                        <h4>Accompanying Registration</h4>
                                    </div>
                                    <div class="note text-center">
                                        <p>Add Accompanying Person</p>
                                    </div>

                                    <?php
                                    foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {
                                        if (floatval($registrationDetailsVal['AMOUNT']) > 0) {
                                            $dinner_amnt_display = '<strong>' . number_format($dinnerValue[$currentCutoffId]['AMOUNT'], 2) . '</strong> <br>' . $registrationDetailsVal['CURRENCY'];
                                    ?>
                                            <input type="hidden" name="dinner_amnt_display" id="dinner_amnt_display" value="<?= $dinner_amnt_display ?>" />

                                            <input type="hidden" name="dinner_classification_id" id="dinner_classification_id" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" />

                                            <input type="hidden" name="dinner_amnt" id="dinner_amnt" value="<?= $dinnerValue[$currentCutoffId]['AMOUNT'] ?>" />

                                            <input type="hidden" name="dinner_title" id="dinner_title" value="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?>" />

                                    <?php

                                        }
                                    }
                                    ?>

                                    <!--  <form class="add-inclusion"> -->
                                    <div class="accompany-row add-inclusion">
                                        <div class="custom-checkbox accompanying ">
                                            <input type="checkbox" name="accompanyCount" id="accompanyCount" use="accompanyCountSelect" value="1" amount="<?= $registrationAmount ?>" invoiceTitle="Accompanying Person" icon="images/cat-ic-3.png">
                                            <label for="accompanyCount"><span>1. *Inclusion</span>
                                                <div class="accompany-inner">
                                                    <div class="acc-name-wrap">
                                                        <div class="acc-inclusion">
                                                            <div class="acc-icons">
                                                                <img src="<?= _BASE_URL_ ?>images/ac1.png" alt="" class="acc-icons-tooltip" title="Image Title1" />
                                                                <img src="<?= _BASE_URL_ ?>images/ac2.png" alt="" class="acc-icons-tooltip" title="Image Title2" />
                                                                <img src="<?= _BASE_URL_ ?>images/ac3.png" alt="" class="acc-icons-tooltip" title="Image Title3" />
                                                                <img src="<?= _BASE_URL_ ?>images/ac4.png" alt="" class="acc-icons-tooltip" title="Image Title4" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="acc-main-price">
                                                        <span><?= $registrationAmount ?></span> <?= $registrationCurrency ?>
                                                    </div>
                                                </div>
                                            </label>
                                            <input type="hidden" name="accompanyClasfId" value="<?= $accompanyCatagory ?>" />

                                        </div>

                                        <div id="accompany-container">
                                            <div class="add-accompany">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <p>Accompany <span id="accomCount">1</span></p>
                                                        <input type="text" class="form-control accompany_name" name="accompany_name_add[0]" placeholder="Name" validate="Enter the accompany name" countindex="0">
                                                        <input type="hidden" name="accompany_selected_add[0]" value="0" />
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <p>Food Preference</p>
                                                        <div class="custom-checkbox accompanying">
                                                            <input type="radio" name="accompany_food_choice[0]" id="veg" value="VEG">
                                                            <label for="veg"><span>Veg</span></label>
                                                        </div>
                                                        <div class="custom-checkbox accompanying">
                                                            <input type="radio" name="accompany_food_choice[0]" id="nonveg" value="NON_VEG">
                                                            <label for="nonveg"><span>Non-Veg</span></label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <input type="hidden" name="accompanyAmount" id="accompanyAmount" value="<?= $registrationAmount ?>">
                                        <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?= $registrationAmount ?>">

                                        <input type="hidden" name="accompanyCounts" id="accompanyCounts" value="1">

                                    </div>

                                    <button class="btn" id="add-accompany-btn">Add More</button>
                                    <!-- </form> -->

                                    <div class="category-table-row pr-5 next-prev-btn-wrap d-flex justify-content-between">
                                        <!--  <a class="btn btn-w prev" title="Select Workshop">Privious</a>
                                  <a  class="btn next-btn skip" >Skip</a> -->
                                        <a href="<?= _BASE_URL_ ?>profile.php" class="btn next-btn prev"><img src="images/left-arrow.png" alt=""></a>
                                        <a class="btn btn-w next" formPay="frmAddAccompanyfromProfile">Pay Now</a>
                                    </div>

                                    <div class="checkout-main-wrap" id="checkout-main-wrap">
                                        <div class="checkout-popup">
                                            <div class="card-details">
                                                <div class="card-details-inner">
                                                    <img src="<?= $logo ?>" alt="" />

                                                    <div class="redio-select custom-radio-holder" style="display: flex;">

                                                        <input type="hidden" name="registrationMode" id="registrationMode">
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card<?= $section ?>" paymentMode='ONLINE' formPay="frmAddAccompanyfromProfile">
                                                            Card<span class="checkmark">
                                                        </label>
                                                        <?php
                                                        $offline_payments = json_decode($cfg['PAYMENT.METHOD']);
                                                        if (in_array("Cheque", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio" >
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccompanyfromProfile">
                                                                Cheque<span class="checkmark">
                                                            </label >
                                                        <?php
                                                        }
                                                        if (in_array("Draft", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccompanyfromProfile">
                                                                Draft<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Neft", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccompanyfromProfile">
                                                                NEFT<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Rtgs", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccompanyfromProfile">
                                                                RTGS<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Cash", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccompanyfromProfile">
                                                                Cash<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Upi", $offline_payments)) {
                                                        ?>

                                                            <!-- UPI Payment Option Added By Weavers start -->
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI<?= $section ?>" paymentMode='OFFLINE' section="<?= $section ?>" formPay="frmAddAccompanyfromProfile">
                                                                UPI<span class="checkmark">
                                                            </label>
                                                        <?php
                                                        }
                                                        ?>
                                                        <!-- UPI Payment Option Added By Weavers end -->
                                                        &nbsp;
                                                    </div>
                                                    <div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card<?= $section ?>" actAs='fieldContainer'>
                                                        <div class="checkbox custom-radio-holder">

                                                            <div>
                                                                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                    <img src="<?= _BASE_URL_ ?>images/international_globe.png" height="20px;">
                                                                    International Card
                                                                    <input type="radio" name="card_mode" use="card_mode_select" value="International">
                                                                    <span class="checkmark">
                                                                </label>
                                                                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                    <img src="<?= _BASE_URL_ ?>images/india_globe.png" height="20px;">
                                                                    Indian Card
                                                                    <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
                                                                    <span class="checkmark">
                                                                </label>
                                                                &nbsp;
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="card-form" id="paymentDetailsSection" style="display: none;">

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <h6>Payment Details</h6>
                                                            </div>
                                                            <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Cash<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Money Order Sent Date</label>  -->
                                                                <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">

                                                            </div>
                                                            <!-- UPI Payment Option Added By Weavers start -->
                                                            <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="txn_no">UPI Transaction ID</label>  -->
                                                                <input type="text" class="form-control" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="UPI Transaction">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                                <!--  <label for="txn_no">UPI Payment Date</label>  -->
                                                                <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select upi date" placeholder="Payment Date">

                                                            </div>
                                                            <!-- Cheque Payment Option Added By Weavers start -->

                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Cheque No</label> -->
                                                                <input type="text" class="form-control" name="cheque_number" id="cheque_number" validate="Please enter cheque number" placeholder="Cheque No">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Drawee Bank</label>  -->
                                                                <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Bank Name">

                                                            </div>
                                                            <div class="col-lg-6 " style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Cheque Date</label> -->
                                                                <input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">

                                                            </div>

                                                            <!-- NEFT Payment Option Added By Weavers start -->
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Transaction Id</label> -->
                                                                <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Transaction Id">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Drawee Bank</label> -->
                                                                <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name" validate="Please enter neft bank" placeholder="Bank Name">

                                                            </div>
                                                            <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!--  <label for="user_first_name">Date</label> -->
                                                                <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">

                                                            </div>
                                                        </div>

                                                    </div>


                                                    <div class="policy-div">
                                                        <ul>
                                                            <li><a href="">Cancellation Policy</a></li>
                                                            <li><a href="">Privacy Policy</a></li>
                                                        </ul>
                                                    </div>

                                                    <div class="righr-btn"><input type="button" class="pay-button" id="pay-button" onclick="payNow('4','frmAddAccompanyfromProfile')" value="Pay Now" style="display:none;" section="<?= $section ?>" formName="frmAddAccompanyfromProfile"></div>


                                                </div>
                                            </div>
                                            <div class="cart-details">
                                                <div class="cart-heading">
                                                    <h5>Order Summary</h5>

                                                    <a href="#" class="close-check"><span>&#10005;</span> close</a>
                                                </div>

                                                <div class="cart-data-row add-inclusion" use="totalAmount">

                                                    <table class="table bill" use="totalAmountTable" id="bill_details" style="display:none;">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>DETAIL</th>
                                                                <th align="right" style="text-align:right;">AMOUNT (INR)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                        <tfoot>
                                                            <tr style="display:none;" use='rowCloneable'>
                                                                <td> <span use="icon"></span></td>
                                                                <td> <span use="invTitle"></span></td>
                                                                <td align="right"><span use="amount">0.00</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Total</td>
                                                                <td align="right"><span use='totalAmount'>0.00</span></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div class="category-table-rowxx drama-total-holder section" id="section5">
                                <form name="frmAddDinnerfromProfile" id="frmAddDinnerfromProfile" action="<?= $cfg['BASE_URL'] ?>registration.process.php" method="post">
                                    <input type="hidden" name="act" value="add_dinner_profile" />
                                    <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" cutoffid="<?= $currentCutoffId ?>" />
                                    <input type="hidden" name="delegateClasfId" value="<?= $rowUserDetails['registration_classification_id'] ?>" />
                                    <input type="hidden" id="registrationRequest" name="registrationRequest" value="<?= $rowUserDetails['registration_request'] ?>" />
                                    <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />

                                    <?php
                                    $sql  = array();
                                    $sql['QUERY']    = "SELECT * FROM " . _DB_EMAIL_CONSTANT_ . " 
                                                 WHERE `id` = ?";
                                    $sql['PARAM'][] = array('FILD' => 'id',         'DATA' => 1,                   'TYP' => 's');
                                    $result      = $mycms->sql_select($sql);
                                    $row         = $result[0];

                                    ?>

                                    <div class="heading-ar2">
                                        <h4>Gala Dinner</h4>
                                    </div>

                                    <div class="gala-dinner-select add-inclusion">
                                        <div class="gala-row">
                                            <div class="d-flex align-items-center">
                                                <div class="gala-inner-lt">
                                                    <div class="gala-inner">
                                                        <div class="gala-box">
                                                            <img src="<?= _BASE_URL_ ?>images/gala-logo.png" alt="" class="gold-img" />
                                                        </div>
                                                        <div class="gala-name">
                                                            <h5 id="dinner_name"><?= strtoupper($rowUserDetails['user_full_name']) ?></h5>
                                                        </div>
                                                    </div>

                                                    <div class="gala-location">
                                                        <ul>
                                                            <li><a href="<?= $row['gala_dinner_venue_address'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/loction.png" alt="" /> <?= $row['dinner_venue_name'] ?></a></li>
                                                            <li><img src="<?= _BASE_URL_ ?>images/calender.png" alt="" /> <?= $row['dinner_details'] ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="gala-inner-rt">

                                                    <div class="gala-main">
                                                        <div class="acc-gala-price">

                                                            <?php
                                                            foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {

                                                                //echo '<pre>'; print_r($dinnerValue[$currentCutoffId]['AMOUNT']);
                                                                if (floatval($dinnerValue[$currentCutoffId]['AMOUNT']) > 0) {
                                                                    echo '<strong>' . number_format($dinnerValue[$currentCutoffId]['AMOUNT'], 2) . '</strong> <br>' . $registrationDetailsVal['CURRENCY'];
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <?php
                                            foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {

                                            ?>

                                                <div class="select-dinner d-flex align-items-center">
                                                    <div class="custom-checkbox">
                                                        <input type="checkbox" class="checkboxClassDinner" name="dinner_value[0]" id="dinner_value" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" operationMode="dinner" use="dinner" amount="<?= $dinnerValue[$currentCutoffId]['AMOUNT'] ?>" invoiceTitle="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?>-conference" icon="images/ac4.png" />

                                                        <label for="dinner_value">Please choose Now</label>
                                                    </div>
                                                </div>


                                            <?php
                                            }
                                            ?>
                                        </div>


                                        <?php
                                        if (sizeof($accompanyDtlsArr) > 0) {
                                            $i = 0;
                                            foreach ($accompanyDtlsArr as $key => $accompanyFullDtls) {

                                                $accompanyDtls        = $accompanyFullDtls['ROW_DETAIL'];

                                                //echo '<pre>'; print_r($accompanyDtls); 
                                                $accompanyID =  $key;
                                                $j = $i + 1;
                                        ?>

                                                <div class="gala-row">
                                                    <div class="d-flex align-items-center">
                                                        <div class="gala-inner-lt">
                                                            <div class="gala-inner">
                                                                <div class="gala-box">
                                                                    <img src="<?= _BASE_URL_ ?>images/gala-logo.png" alt="" class="gold-img" />
                                                                </div>
                                                                <div class="gala-name">
                                                                    <h5 id="dinner_name"><?= strtoupper($accompanyDtls['user_full_name']) ?></h5>

                                                                </div>
                                                            </div>

                                                            <div class="gala-location">
                                                                <ul>
                                                                    <li><a href="<?= $row['gala_dinner_venue_address'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/loction.png" alt="" /> <?= $row['dinner_venue_name'] ?></a></li>
                                                                    <li><img src="<?= _BASE_URL_ ?>images/calender.png" alt="" /> <?= $row['dinner_details'] ?></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="gala-inner-rt">

                                                            <div class="gala-main">
                                                                <div class="acc-gala-price">

                                                                    <?php
                                                                    foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {
                                                                        if (floatval($registrationDetailsVal['AMOUNT']) > 0) {
                                                                            echo '<strong>' . number_format($dinnerValue[$currentCutoffId]['AMOUNT'], 2) . '</strong> <br>' . $registrationDetailsVal['CURRENCY'];
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <?php
                                                    foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {

                                                    ?>

                                                        <div class="select-dinner d-flex align-items-center">
                                                            <div class="custom-checkbox">
                                                                <input type="checkbox" class="checkboxClassDinner" name="accompany_dinner_value[<?= $i ?>]" id="dinner_value<?= $i ?>" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" operationMode="dinner" use="dinner" amount="<?= $dinnerValue[$currentCutoffId]['AMOUNT'] ?>" invoiceTitle="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?>-Accompany (<?= strtoupper($accompanyDtls['user_full_name']) ?>)" icon="images/ac4.png" />

                                                                <label for="dinner_value<?= $i ?>">Please choose Now</label>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="accompanyID[<?= $i ?>]" value="<?= $accompanyID ?>">


                                                    <?php
                                                    }
                                                    ?>
                                                </div>


                                        <?php
                                                $i++;
                                            }
                                        }
                                        ?>

                                    </div>

                                    <div class="category-table-row pr-5 next-prev-btn-wrap d-flex justify-content-between">
                                        <!-- <a class="btn btn-w prev">Privious</a>
                                    <a  class="btn btn-w skip">Skip</a> -->
                                        <a href="<?= _BASE_URL_ ?>profile.php" class="btn next-btn prev"><img src="images/left-arrow.png" alt=""></a>
                                        <a class="btn btn-w next" formPay="frmAddDinnerfromProfile">Pay Now</a>
                                    </div>

                                    <div class="checkout-main-wrap" id="checkout-main-wrap">
                                        <div class="checkout-popup">
                                            <div class="card-details">
                                                <div class="card-details-inner">
                                                    <img src="<?= $logo ?>" alt="" />

                                                    <div class="redio-select custom-radio-holder" style="display: flex;">

                                                        <input type="hidden" name="registrationMode" id="registrationMode">
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card<?= $section ?>" paymentMode='ONLINE' formPay="frmAddDinnerfromProfile">
                                                            Card<span class="checkmark"></span>
                                                        </label>
                                                        <?php
                                                        $offline_payments = json_decode($cfg['PAYMENT.METHOD']);
                                                        if (in_array("Cheque", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddDinnerfromProfile">
                                                                Cheque<span class="checkmark"></span>
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Draft", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddDinnerfromProfile">
                                                                Draft<span class="checkmark"></span>
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Neft", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddDinnerfromProfile">
                                                                NEFT<span class="checkmark"></span>
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Rtgs", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddDinnerfromProfile">
                                                                RTGS<span class="checkmark"></span>
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Cash", $offline_payments)) {
                                                        ?>
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddDinnerfromProfile">
                                                                Cash<span class="checkmark"></span>
                                                            </label>
                                                        <?php
                                                        }
                                                        if (in_array("Upi", $offline_payments)) {
                                                        ?>

                                                            <!-- UPI Payment Option Added By Weavers start -->
                                                            <label class="custom-radio">
                                                                <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI<?= $section ?>" paymentMode='OFFLINE' section="<?= $section ?>" formPay="frmAddDinnerfromProfile">
                                                                UPI<span class="checkmark"></span>
                                                            </label>
                                                        <?php
                                                        }
                                                        ?>
                                                        <!-- UPI Payment Option Added By Weavers end -->
                                                        &nbsp;
                                                    </div>
                                                    <div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card<?= $section ?>" actAs='fieldContainer'>
                                                        <div class="checkbox custom-radio-holder">

                                                            <div>
                                                                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                    <img src="<?= _BASE_URL_ ?>images/international_globe.png" height="20px;">
                                                                    International Card
                                                                    <input type="radio" name="card_mode" use="card_mode_select" value="International">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                    <img src="<?= _BASE_URL_ ?>images/india_globe.png" height="20px;">
                                                                    Indian Card
                                                                    <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                                &nbsp;
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="card-form" id="paymentDetailsSection" style="display: none;">

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <h6>Payment Details</h6>
                                                            </div>
                                                            <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Cash<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Money Order Sent Date</label>  -->
                                                                <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">

                                                            </div>
                                                            <!-- UPI Payment Option Added By Weavers start -->
                                                            <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="txn_no">UPI Transaction ID</label>  -->
                                                                <input type="text" class="form-control" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="UPI Transaction">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                                <!--  <label for="txn_no">UPI Payment Date</label>  -->
                                                                <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select upi date" placeholder="Payment Date">

                                                            </div>
                                                            <!-- Cheque Payment Option Added By Weavers start -->

                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Cheque No</label> -->
                                                                <input type="text" class="form-control" name="cheque_number" id="cheque_number" validate="Please enter cheque number" placeholder="Cheque No">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Drawee Bank</label>  -->
                                                                <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Bank Name">

                                                            </div>
                                                            <div class="col-lg-6 " style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Cheque Date</label> -->
                                                                <input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">

                                                            </div>

                                                            <!-- NEFT Payment Option Added By Weavers start -->
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Transaction Id</label> -->
                                                                <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Transaction Id">

                                                            </div>
                                                            <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!-- <label for="user_first_name">Drawee Bank</label> -->
                                                                <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name" validate="Please enter neft bank" placeholder="Bank Name">

                                                            </div>
                                                            <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                                <!--  <label for="user_first_name">Date</label> -->
                                                                <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">

                                                            </div>
                                                        </div>

                                                    </div>


                                                    <div class="policy-div">
                                                        <ul>
                                                            <li><a href="">Cancellation Policy</a></li>
                                                            <li><a href="">Privacy Policy</a></li>
                                                        </ul>
                                                    </div>

                                                    <div class="righr-btn"><input type="button" class="pay-button" id="pay-button" onclick="payNow('5','frmAddDinnerfromProfile')" value="Pay Now" style="display:none;" section="<?= $section ?>" formName="frmAddDinnerfromProfile"></div>


                                                </div>
                                            </div>
                                            <div class="cart-details">
                                                <div class="cart-heading">
                                                    <h5>Order Summary</h5>

                                                    <a href="#" class="close-check"><span>&#10005;</span> close</a>
                                                </div>

                                                <div class="cart-data-row add-inclusion" use="totalAmount">

                                                    <table class="table bill" use="totalAmountTable" id="bill_details" style="display:none;">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>DETAIL</th>
                                                                <th align="right" style="text-align:right;">AMOUNT (INR)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                        <tfoot>
                                                            <tr style="display:none;" use='rowCloneable'>
                                                                <td> <span use="icon"></span></td>
                                                                <td> <span use="invTitle"></span></td>
                                                                <td align="right"><span use="amount">0.00</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>Total</td>
                                                                <td align="right"><span use='totalAmount'>0.00</span></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>



                            </div>
                            <form name="frmAddAccommodationfromProfile" id="frmAddAccommodationfromProfile" action="<?= $cfg['BASE_URL'] ?>registration.process.php" method="post">
                                <div class="drama-total-holder section" id="section6">

                                    <input type="hidden" name="act" value="add_accommodationfrom_profile" />
                                    <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" cutoffid="<?= $currentCutoffId ?>" />
                                    <input type="hidden" name="delegateClasfId" value="<?= $rowUserDetails['registration_classification_id'] ?>" />
                                    <input type="hidden" id="delegate_id" name="delegate_id" value="<?= $delegateId ?>" />
                                    <input type="hidden" id="accommodation_pkg_id" name="accommodation_pkg_id" value="" />
                                    <input type="hidden" id="accommodation_details" name="accommodation_details" value="" />

                                    <?php

                                    $sqlFetchHotel      = array();
                                    $sqlFetchHotel['QUERY'] = "SELECT * 
                                         FROM " . _DB_MASTER_HOTEL_ . "
                                        WHERE `status` =  ? ";

                                    $sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
                                    $resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);
                                    //echo '<pre>'; print_r($resultFetchHotel);
                                    $hotel_count = count($resultFetchHotel) - 1;
                                    if (count($resultFetchHotel)) {
                                    ?>
                                        <div class="carv-slider">
                                            <?php

                                            foreach ($resultFetchHotel as $k => $val) {
                                                $hotel_background_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['hotel_background_image'];
                                            ?>
                                                <div class="carv-slide-item">
                                                    <div class="drama-slide-holder">
                                                        <div class="carv-slide-info">
                                                            <div class="carv-slide-info-img"><img src="<?= $hotel_background_image ?>" alt=""></div>
                                                            <div class="carv-slide-info-ctn">
                                                                <div class="starrating">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                                        <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z" />
                                                                    </svg>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                                        <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z" />
                                                                    </svg>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                                        <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z" />
                                                                    </svg>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                                        <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z" />
                                                                    </svg>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                                        <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z" />
                                                                    </svg>
                                                                </div>
                                                                <h4><?= $val['hotel_name'] ?></h4>
                                                                <p><?= nl2br($val['hotel_address']) ?></p>
                                                                <!-- <p>Breakfast Included</p> -->
                                                                <div class="btnholder d-flex">
                                                                    <a class="btn btn-border next11" onclick="getAccommodationDetails('<?= $val['id'] ?>')">Book Now</a>
                                                                    <a href="<?= _BASE_URL_ ?>profile.php" class="btn ml-10 next-btn prev" accompany-count="<?= $countAcc ?>" banquet-count="<?= count($dinnerTariffArray) ?>"><img src="images/left-arrow.png" alt=""></a>
                                                                    <a class="btn next-btn skipnow skip" id="paynowbtn" role="button">
                                                                        <div class="icon"><img src="images/skip-icon.png" alt=""></div>
                                                                        <span>Skip</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }


                                            ?>

                                        </div>
                                        <div class='carv-nav'>
                                            <?php

                                            foreach ($resultFetchHotel as $k => $val) {
                                                $hotel_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['hotel_image'];
                                            ?>
                                                <div class="carv-slide-img-main">
                                                    <img src="<?= $hotel_image ?>" alt="">
                                                    <div class="thminfo">
                                                        <div class="starrating">
                                                            <i class="fas fa-star"></i>
                                                            <i class="fas fa-star"></i>
                                                            <i class="fas fa-star"></i>
                                                            <i class="fas fa-star"></i>
                                                            <i class="fas fa-star"></i>
                                                        </div>
                                                        <h6><?= $val['hotel_name'] ?></h6>
                                                        <!-- <p><?= nl2br($val['hotel_address']) ?></p> -->
                                                        <!-- <p>Breakfast Included</p> -->
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>


                                </div>

                                <div class="smobilexx drama-total-holder section" id="section7">
                                </div>
                                <div class="checkout-main-wrap" id="checkout-main-wrap">
                                    <div class="checkout-popup">
                                        <div class="card-details">
                                            <div class="card-details-inner">
                                                <img src="<?= $logo ?>" alt="" />

                                                <div class="redio-select custom-radio-holder" style="display: flex;">

                                                    <input type="hidden" name="registrationMode" id="registrationMode">
                                                    <label class="custom-radio">
                                                        <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card<?= $section ?>" paymentMode='ONLINE' formPay="frmAddAccommodationfromProfile">
                                                        Card<span class="checkmark"></span>
                                                    </label>
                                                    <?php
                                                    $offline_payments = json_decode($cfg['PAYMENT.METHOD']);
                                                    if (in_array("Cheque", $offline_payments)) {
                                                    ?>
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccommodationfromProfile">
                                                            Cheque<span class="checkmark"></span>
                                                        </label>
                                                    <?php
                                                    }
                                                    if (in_array("Draft", $offline_payments)) {
                                                    ?>
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccommodationfromProfile">
                                                            Draft<span class="checkmark"></span>
                                                        </label>
                                                    <?php
                                                    }
                                                    if (in_array("Neft", $offline_payments)) {
                                                    ?>
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccommodationfromProfile">
                                                            NEFT<span class="checkmark"></span>
                                                        </label>
                                                    <?php
                                                    }
                                                    if (in_array("Rtgs", $offline_payments)) {
                                                    ?>
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccommodationfromProfile">
                                                            RTGS<span class="checkmark"></span>
                                                        </label>
                                                    <?php
                                                    }
                                                    if (in_array("Cash", $offline_payments)) {
                                                    ?>
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash<?= $section ?>" paymentMode='OFFLINE' formPay="frmAddAccommodationfromProfile">
                                                            Cash<span class="checkmark"></span>
                                                        </label>
                                                    <?php
                                                    }
                                                    if (in_array("Upi", $offline_payments)) {
                                                    ?>

                                                        <!-- UPI Payment Option Added By Weavers start -->
                                                        <label class="custom-radio">
                                                            <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI<?= $section ?>" paymentMode='OFFLINE' section="<?= $section ?>" formPay="frmAddAccommodationfromProfile">
                                                            UPI<span class="checkmark"></span>
                                                        </label>
                                                    <?php
                                                    }
                                                    ?>
                                                    <!-- UPI Payment Option Added By Weavers end -->
                                                    &nbsp;
                                                </div>
                                                <div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card<?= $section ?>" actAs='fieldContainer'>
                                                    <div class="checkbox custom-radio-holder">

                                                        <div>
                                                            <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                <img src="<?= _BASE_URL_ ?>images/international_globe.png" height="20px;">
                                                                International Card
                                                                <input type="radio" name="card_mode" use="card_mode_select" value="International">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                            <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                                                                <img src="<?= _BASE_URL_ ?>images/india_globe.png" height="20px;">
                                                                Indian Card
                                                                <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                            &nbsp;
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="card-form" id="paymentDetailsSection" style="display: none;">

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <h6>Payment Details</h6>
                                                        </div>
                                                        <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Cash<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="user_first_name">Money Order Sent Date</label>  -->
                                                            <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">

                                                        </div>
                                                        <!-- UPI Payment Option Added By Weavers start -->
                                                        <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="txn_no">UPI Transaction ID</label>  -->
                                                            <input type="text" class="form-control" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="UPI Transaction">

                                                        </div>
                                                        <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="UPI<?= $section ?>" actAs='fieldContainer'>
                                                            <!--  <label for="txn_no">UPI Payment Date</label>  -->
                                                            <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select upi date" placeholder="Payment Date">

                                                        </div>
                                                        <!-- Cheque Payment Option Added By Weavers start -->

                                                        <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="user_first_name">Cheque No</label> -->
                                                            <input type="text" class="form-control" name="cheque_number" id="cheque_number" validate="Please enter cheque number" placeholder="Cheque No">

                                                        </div>
                                                        <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="user_first_name">Drawee Bank</label>  -->
                                                            <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Bank Name">

                                                        </div>
                                                        <div class="col-lg-6 " style="display:none;" use="offlinePaymentOption" for="Cheque<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="user_first_name">Cheque Date</label> -->
                                                            <input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">

                                                        </div>

                                                        <!-- NEFT Payment Option Added By Weavers start -->
                                                        <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="user_first_name">Transaction Id</label> -->
                                                            <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Transaction Id">

                                                        </div>
                                                        <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                            <!-- <label for="user_first_name">Drawee Bank</label> -->
                                                            <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name" validate="Please enter neft bank" placeholder="Bank Name">

                                                        </div>
                                                        <div class="col-lg-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT<?= $section ?>" actAs='fieldContainer'>
                                                            <!--  <label for="user_first_name">Date</label> -->
                                                            <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">

                                                        </div>
                                                    </div>

                                                </div>


                                                <div class="policy-div">
                                                    <ul>
                                                        <li><a href="">Cancellation Policy</a></li>
                                                        <li><a href="">Privacy Policy</a></li>
                                                    </ul>
                                                </div>

                                                <div class="righr-btn"><input type="button" class="pay-button" id="pay-button" onclick="payNow('5','frmAddAccommodationfromProfile')" value="Pay Now" style="display:none;" section="<?= $section ?>" formName="frmAddAccommodationfromProfile"></div>


                                            </div>
                                        </div>
                                        <div class="cart-details">
                                            <div class="cart-heading">
                                                <h5>Order Summary</h5>

                                                <a href="#" class="close-check"><span>&#10005;</span> close</a>
                                            </div>

                                            <div class="cart-data-row add-inclusion" use="totalAmount">

                                                <table class="table bill" use="totalAmountTable" id="bill_details" style="display:none;">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>DETAIL</th>
                                                            <th align="right" style="text-align:right;">AMOUNT (INR)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot>
                                                        <tr style="display:none;" use='rowCloneable'>
                                                            <td> <span use="icon"></span></td>
                                                            <td> <span use="invTitle"></span></td>
                                                            <td align="right"><span use="amount">0.00</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Total</td>
                                                            <td align="right"><span use='totalAmount'>0.00</span></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col-lg-6">

                            <div class="logo-section" data-aos="fade-left" data-aos-duration="800">

                                <div class="site-logo">
                                    <a href="javascript:void(0)"><img src="<?= $emailHeader ?>" alt="" style="height: 120px;width:120px" /></a>
                                </div>
                            </div>
                            <div class="site-menu-holder" id="site-menu-holder">
                                <a onclick="myFunction()" class="clicknow"><img src="<?= _BASE_URL_ ?>images/toggle-nav-up.png"></a>
                                <div class="site-menu">

                                    <?php
                                    if ($resultIcon) {
                                        $i = 1;

                                        foreach ($resultIcon as $k => $val) {

                                            /*if($i==($_REQUEST['section']-1) && ($val['title']=='Workshop' || $val['title']=='Accompanying'))
                                {
                                  $activeclass = 'active';
                                }
                                else if($i==$_REQUEST['section'] && ($val['title']=='Accommodation' || $val['title']=='Banquet'))
                                {
                                  $activeclass = 'active';
                                }*/

                                            if ($i == $_REQUEST['section'] - 1) {
                                                $activeclass = 'active';
                                            } else {
                                                $activeclass = '';
                                            }

                                    ?>

                                            <a class="main-menu <?= $activeclass ?>" id="item<?= $i; ?>">
                                                <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500">
                                                    <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE']; ?><?= $val['icon'] ?>" alt="" />
                                                    <p><?= $val['title'] ?></p>
                                                </div>
                                            </a>

                                    <?php
                                            $i++;
                                        }
                                    }
                                    ?>



                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </section>


        <?php include_once('footer.php'); ?>



    </main>



    <div id="loginModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="frmLoginUniqueSequence" id="frmLoginUniqueSequence" action="<?= _BASE_URL_ ?>login.process.php" method="post">
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
                            <h4><input type="email" name="user_email_id" id="user_email_id" value="" style="text-transform:lowercase; border:0px;" readonly="" /></h4>
                        </div>
                        <div class="login-user" style="margin-top: 5px;">
                            <h4><input type="text" name="user_otp" id="user_otp" value="#" required /></h4>
                        </div>
                        <div class="bttn" style="margin-top: 25px;"><button type="submit">Login</button>&nbsp;<button type="button" style="background:#7f8080;" use='cancel' data-bs-dismiss="modal">Cancel</button></div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="unpaidModalOnline" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post" action="<?= $cfg['BASE_URL'] ?>login.process.php">
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
                            <h4><input type="email" name="user_details" id="user_details" value="" style="text-transform:lowercase; border:0px;" readonly="" /></h4>
                        </div>
                        <div class="bttn" style="margin-top: 25px;"><button type="submit">Proceed to
                                pay</button>&nbsp;<button type="button" style="background:#7f8080;" use='cancel'>Cancel</button></div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="unpaidModalOffline" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post" action="<?= $cfg['BASE_URL'] ?>login.process.php">
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
            <form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post" action="<?= $cfg['BASE_URL'] ?>login.process.php">
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
                            <h4><input type="email" name="user_email_id" id="user_email_id" value="" style="text-transform:lowercase; border:0px;" readonly="" /></h4>
                        </div>
                        <div class="bttn" style="margin-top: 25px;"><button type="submit">Proceed to
                                pay</button>&nbsp;<button type="button" style="background:#7f8080;" use='cancel'>Cancel</button>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="checkout-main-wrap" id="paymentVoucherModal">
        <div class="checkout-popup">
            <div class="card-details">
                <div class="card-details-inner">
                    <img src="<?= $logo ?>" alt="" />

                    <form name="frmApplyPayment" id="frmApplyPayment" action="registration.process.php" method="post">
                        <div class="col-xs-12 form-group" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>

                            <div class="checkbox">


                                <input type="hidden" id="slip_id" name="slip_id" />
                                <input type="hidden" id="delegate_id" name="delegate_id" />
                                <input type="hidden" name="act" value="paymentSet" />
                                <input type="hidden" name="mode" id="mode" />
                                <label class="container-box" style="float:left; margin-right:30px;">
                                    <img src="<?= _BASE_URL_ ?>images/international_globe.png" height="20px;">
                                    International Card
                                    <input type="radio" name="card_mode" use="card_mode_select" value="International">

                                </label>
                                <label class="container-box" style="float:left; margin-right:30px;">
                                    <img src="<?= _BASE_URL_ ?>images/india_globe.png" height="20px;">
                                    Indian Card
                                    <input type="radio" name="card_mode" use="card_mode_select" value="Indian">

                                </label>
                                &nbsp;

                            </div>

                        </div>

                        <div class="policy-div">
                            <ul>
                                <li><a href="">Cancellation Policy</a></li>
                                <li><a href="">Privacy Policy</a></li>
                            </ul>
                        </div>

                        <div class="righr-btn"><input type="button" class="pay-button" id="pay-button-vouchar" value="Pay Now"></div>
                    </form>

                </div>
            </div>
            <div class="cart-details">
                <div class="cart-heading">
                    <h5>Order Summary</h5>

                    <a class="close-check" style="cursor: pointer;"><span>&#10005;</span> close</a>
                </div>

                <div class="cart-data-row add-inclusion" use="totalAmount" id="paymentVoucherBody">


                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var currentSection = '<?= $section ?>';
            showSection(currentSection);
            //alert(currentSection);
            //$('.next').click(function() {
            $(document).on("click", ".next", function() {
                var formPay = $(this).attr('formPay');

                var formPay = $('#' + formPay);

                if (validateSection(currentSection, formPay)) {

                    $('#pageTitle').text("");
                    $('#pageTitle').text($(this).attr('title'));


                    if (formPay.length && formPay.find('#checkout-main-wrap').length) {
                        // Show the element with ID 'checkout-main-wrap'
                        formPay.find('#checkout-main-wrap').show();
                        formPay.find('#pay-button').show();
                    } else {
                        console.error('Form or element not found.');
                    }




                } // end validation if 
            });



            function isAnyCheckboxChecked(className) {
                var checkboxes = document.querySelectorAll('.' + className);

                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        return true; // At least one checkbox is checked
                    }
                }

                return false; // No checkbox is checked
            }

            function validateSection(section, formPay) {
                //alert(section); 
                var isValid = true;
                var accomArr = [];
                var galaDinnerDiv = '';
                var hasExecuted = false;
                //alert(formPay);
                $("#section" + section + " input[type='text'], #section" + section + " input[type='radio'], #section" + section + " input[type='checkbox'], #section" + section + " select").each(function(index) {


                    if ($(this).attr('type') === 'text' && !$.trim($('.accompany_name').val())) {


                        //alert(1);

                        if (section == 2) {

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

                        if (section == 4) {
                            if ($("input[type='checkbox'][name='accompanyCount']:checked").length) {
                                //alert($(this).attr('name'))

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

                            // 
                        }


                    } else if ($(this).attr('type') === 'radio') {





                        if (section == 1) {
                            // Check if at least one radio button is checked
                            if (!$("input[name='" + $(this).attr('name') + "']:checked").length) {
                                // Perform validation, show an error message, or take necessary action
                                console.log("Please select a value for radio button " + $(this).attr('name'));
                                toastr.error('Please select a category', 'Error', {
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

                        if (section == 3) {
                            //alert($(this).attr('name'));
                            if (!$("input[name='workshop_id[]']:checked").length) {

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

                        if (section == 4) {

                            if (!$("input[type='checkbox'][name='accompanyCount']:checked").length) {

                                toastr.error('Please select a accompany', 'Error', {
                                    "progressBar": true,
                                    "timeOut": 3000, // 3 seconds
                                    "showMethod": "slideDown",
                                    "hideMethod": "slideUp"
                                });

                                isValid = false;
                                return false;

                            } //end if
                            else if ($("input[type='checkbox'][name='accompanyCount']:checked").length) {

                                var totalAccomCount = Number($("input[type='checkbox'][name='accompanyCount']:checked").length) - 1;

                                //alert(totalAccomCount);

                                if (!$("input[name='accompany_food_choice[" + totalAccomCount + "]']:checked").length) {

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


                    } else if ($(this).attr('type') === 'checkbox' && section == 5) {
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
                    }



                });

                $('.gala-dinner-select').append(galaDinnerDiv);

                return isValid;


            } //end if validation

            function showSection(section) {
                $('.section').removeClass('active');
                $('#section' + section).addClass('active');
            }

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

        $("input[type=checkbox][use=accompanyCountSelect]").click(function() {
            var count = parseInt($(this).val());

            calculateTotalAmount();
        });

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

        function calculateTotalAmount() {
            console.log("====calculateTotalAmount====");

            var totalAmount = 0;
            var totTable = $("table[use=totalAmountTable]");
            $(totTable).children("tbody").find("tr").remove();
            var gst_flag = $('#gst_flag').val();


            $('input[type=checkbox]:checked,input[type=radio]:checked,#accomodation_package_checkout_id option').each(function() {
                var attr = $(this).attr('amount');
                var operation = $(this).attr('operationmodetype');
                var regtype = $(this).attr('regtype');
                var reg = $(this).attr('reg');

                var package = $(this).attr('package');
                var key = $(this).attr('key');

                //alert(11)

                if (typeof attr !== typeof undefined && attr !== false) {
                    var amt = parseFloat(attr);


                    if (typeof package !== typeof undefined && package !== false) {



                        // alert(checkedValue);

                        var checkInVal = $('#accomodation_package_checkin_id').val();
                        var checkOutVal = $('#accomodation_package_checkout_id').val();

                        console.log('checkInVal====', checkInVal)
                        console.log('checkOutVal====', checkOutVal)





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

                            var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));


                            console.log('accoAmnt=' + differenceDays);
                            var amt = parseFloat(amt) * parseInt(differenceDays) * parseInt(roomQty);

                            if (isNaN(amt)) {
                                amt = 0;
                            }
                        }



                    }
                    if (regtype !== 'combo') {

                        if (gst_flag == 1) {
                            var cgstP = <?= $cfg['INT.CGST'] ?>;
                            var cgstAmnt = (amt * cgstP) / 100;

                            var sgstP = <?= $cfg['INT.SGST'] ?>;
                            var sgstAmnt = (amt * sgstP) / 100;

                            var totalGst = cgstAmnt + sgstAmnt;
                            var totalGstAmount = cgstAmnt + sgstAmnt + amt;
                            totalAmount = totalAmount + totalGstAmount;
                        } else {
                            totalAmount = totalAmount + amt;
                        }

                        if (reg != undefined && reg == 'reg') {
                            $('#confPrc').text((amt).toFixed(2));
                        }

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

                        //alert($(this).attr('invoiceTitle'));
                        var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
                            .clone();
                        $(cloned).attr("use", "rowCloned");
                        var imageElement = $('<img>').attr('src', "<?= _BASE_URL_ ?>" + $(this).attr('icon'));
                        //alert("<?= _BASE_URL_ ?>"+$(this).attr('icon'));
                        $(cloned).find("span[use=icon]").append(imageElement);
                        $(cloned).find("span[use=invTitle]").append($(this).attr('invoiceTitle'));
                        if (regtype === 'combo') {

                            $(cloned).find("span[use=amount]").text((amt > 0) ? ('Included') : amtAlterTxt);
                        } else {

                            $(cloned).find("span[use=amount]").text((amt > 0) ? (amt).toFixed(2) : amtAlterTxt);
                        }

                        $(cloned).show();
                        $(totTable).children("tbody").append(cloned);
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

            $('#subTotalPrc').text((totalAmount).toFixed(2));

            totTable.show();
        }


        $(document).ready(function() {
            // Counter to keep track of the number of accompanies
            var accompanyCount = $('#accompanyCounts').val();


            function addAccompany() {


                var accompanyCount = $('#accompanyCounts').val();
                var accompanyAmount = $('#accompanyAmount').val();

                var incrementedCount = Number(accompanyCount) + 1;

                $('#accompanyCounts').val(incrementedCount);


                var accompanyCount = $('#accompanyCounts').val();

                var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(incrementedCount);

                $("#accompanyCount").attr("amount", amountIncludedDay);
                $("#accompanyCount").val(incrementedCount);

                var newAccompany = $(".add-accompany:first").clone();
                newAccompany.find("span#accomCount").text(accompanyCount);
                newAccompany.find("input[type='text']").val(""); // Clear the input field
                newAccompany.find("input[type='radio']").prop("checked", false);
                //$("#radioOption1").prop("checked", false);

                var fieldSerializeCount = Number(incrementedCount) - 1;



                newAccompany.find("input[type='text']").attr("name", "accompany_name_add[" + fieldSerializeCount + "]");
                newAccompany.find("input[type='hidden']").attr({
                    "name": "accompany_selected_add[" + fieldSerializeCount + "]",
                    "value": fieldSerializeCount
                });

                newAccompany.find("input[type='text']").attr("countindex", fieldSerializeCount);
                newAccompany.find("input[type='radio']").attr("name", "accompany_food_choice[" + fieldSerializeCount + "]");

                newAccompany.find("input[type='radio'][name='accompany_food_choice[" + fieldSerializeCount + "]']").each(function(index, element) {

                    var inputType = $(element).attr("type");
                    var inputId = $(element).attr("id");
                    //alert(inputId)
                    if (inputId != undefined && inputId == 'veg') {
                        var newId = "veg" + fieldSerializeCount;
                        $(this).attr("id", newId);
                        $(this).attr("value", 'VEG');
                        $(this).siblings("label").attr("for", newId);
                    } else if (inputId != undefined && inputId == 'nonveg') {
                        var newId = "nonveg" + fieldSerializeCount;
                        $(this).attr("id", newId);
                        $(this).attr("value", 'NON_VEG');
                        $(this).siblings("label").attr("for", newId);
                    }

                });



                $("#accompany-container").append(newAccompany);


                newAccompany.append('<button class="delete-accompany-btn">Delete</button>');
                calculateTotalAmount();
            }


            $("#add-accompany-btn").on("click", function(e) {
                e.preventDefault();
                addAccompany();
            });



            // Event handler for dynamically added "Delete" buttons
            $("#accompany-container").on("click", ".delete-accompany-btn", function(e) {
                e.preventDefault();
                $(this).parent().remove();
                var accompanyCount = $('#accompanyCounts').val();

                $('#accompanyCounts').val(Number(accompanyCount) - 1);

                $(this).find("span#accomCount").text(Number(accompanyCount) - 1);

                var accompanyAmount = $('#accompanyAmount').val();

                var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(Number(accompanyCount) - 1);
                //$('#accompanyAmount').val(amountIncludedDay);
                $("#accompanyCount").attr("amount", amountIncludedDay);
                $("#accompanyCount").val(Number(accompanyCount) - 1);


                calculateTotalAmount();

            });


        });

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

        $("input[type=radio][use=payment_mode_select]").click(function() {

            var val = $(this).val();
            var forVal = $(this).attr('for');
            var formPay = $(this).attr('formPay');
            var form = $("#" + formPay);
            //alert(formPay);

            $("div[use=offlinePaymentOption]").hide();
            if (val != undefined) {
                form.find("div[use=offlinePaymentOption][for=" + forVal + "]").show();
                if (val === 'Card') {
                    form.find('#registrationMode').val('ONLINE');
                    form.find('#paymentDetailsSection').hide();
                } else {
                    form.find('#registrationMode').val('OFFLINE');
                    form.find('#paymentDetailsSection').show();
                }
            }

        });

        function payNow(section, formName) {

            var form = $("#" + formName);
            var selectedOption = form.find("input[type=radio][name='payment_mode']:checked").val();
            var specSelected = selectedOption + section;
            var flag = 0;

            //alert(selectedOption);

            if (selectedOption) {

                form.find("div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='text'], div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='date'], div[use='offlinePaymentOption'][for='" + specSelected + "'] input[type='radio']").each(function() {
                    //alert($(this).attr('type'));

                    var form = $(this).closest('form');
                    //alert(form);

                    if ($(this).attr('type') === 'radio') {

                        if (!$("input[type='radio'][name='card_mode']:checked").length) {

                            toastr.error('Please select the card', 'Error', {
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
                        // alert(textBoxValue);
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
            } else {
                //alert("No option selected!");
                toastr.error('Please select payment mode', 'Error', {
                    "progressBar": true,
                    "timeOut": 5000,
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
    </script>
</body>

</html>