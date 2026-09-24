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
$totalSection = 7;

$title = 'Registration';

if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {
  $abstractDelegateId = trim($_REQUEST['abstractDelegateId']);
  $userRec = getUserDetails($abstractDelegateId);
} else {
  $mycms->removeAllSession();
  $mycms->removeSession('SLIP_ID');
}



//echo 'title=='. $userRec['user_title'];


$sql_logo  = array();
$sql_logo['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                      WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$result = $mycms->sql_select($sql_logo);
$row         = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];

if ($row['logo_image'] != '') {
  $emailHeader  = $header_image;
}


$sqlIcon  = array();
$sqlIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                      WHERE `status`='A' AND purpose='Registration' order by seq ";
//$sql['PARAM'][]  = array('FILD' => 'status' ,'DATA' => 'A' ,'TYP' => 's');          
$resultIcon = $mycms->sql_select($sqlIcon);

//echo '<pre>'; print_r($resultIcon);

$sqlInfo  = array();
$sqlInfo['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
             WHERE `status` = ?";
$sqlInfo['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
$resultInfo      = $mycms->sql_select($sqlInfo);
$rowInfo         = $resultInfo[0];
$available_registration_fields = json_decode($rowInfo['available_registration_fields']);

$sqlSocialIcon  = array();
$sqlSocialIcon['QUERY'] = "SELECT * FROM " . _DB_SOCIAL_ICON_SETTING_ . " 
              WHERE `id`!='' AND `purpose`='Regular Icon' AND status='A' ";

$resultSocialIcon    = $mycms->sql_select($sqlSocialIcon);

$sqlSocialButtonIcon  = array();
$sqlSocialButtonIcon['QUERY'] = "SELECT * FROM " . _DB_SOCIAL_ICON_SETTING_ . " 
              WHERE `id`!='' AND `purpose`='Button Icon' AND status='A' ";

$resultSocialButtonIcon    = $mycms->sql_select($sqlSocialButtonIcon);


$sqlFooterIcon  = array();
$sqlFooterIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                      WHERE `status`='A' AND purpose='Footer' order by id ";
//$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultFooterIcon = $mycms->sql_select($sqlFooterIcon);

$sqlLogo    =   array();
$sqlLogo['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE title='Online Payment Logo' ";

$resultLogo      = $mycms->sql_select($sqlLogo);
$logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultLogo[0]['image'];


$sqlAccompany   = array();
$sqlAccompany['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
                     WHERE `status` = ?";
$sqlAccompany['PARAM'][]  = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
$resultAccompany       = $mycms->sql_select($sqlAccompany);

$accompanyCount = $resultAccompany[0]['COUNTDATA'];


$sqlLogo    =   array();
$sqlLogo['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                        WHERE title='Online Payment Logo' ";

$resultLogo      = $mycms->sql_select($sqlLogo);
$logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultLogo[0]['image'];


?>
<!DOCTYPE html>
<html lang="en">
<script>
  // setInterval(function() {
  //     window.history.pushState(null, "", window.location.href);
  // }, 500); // Push state every 500 ms
</script>
<?php


setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();


include_once("header.php");

?>

<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css'>
<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
<link rel='stylesheet' id='elementor-frontend-css' href='<?= _BASE_URL_ ?>css/website/accm-slider-style.css' type='text/css' media='all' />

<style type="text/css">
  .section {
    /* display: none; */
    position: absolute;
    left: 1500%;
    top: 0;
    width: 100%;
    opacity: 0;
    visibility: hidden;
  }

  .active {
    opacity: 1;
    visibility: visible;
    left: 0;
  }



  .blur_bw {
    filter: blur(1.5px) grayscale(1);
  }

  .disabled-user-input {
    background-color: #f5f5f5;
    /* Change background color to indicate disabled state */
    cursor: not-allowed;
    /* Change cursor to indicate not-allowed */
    pointer-events: none;
    /* Disable pointer events to prevent interaction */
  }

  .disabled-click {
    pointer-events: none;
    filter: blur(1.2px)
      /* Disable mouse events */
  }
</style>


<!-- <script src='https://ruedakolkata.com/wboacon2025/js/website/accm-slider-style.js'></script> -->

<body class="single inner-page">
  <div id="loading_indicator" style="display:none;"> </div>
  <?
  $cutoffs      = fullCutoffArray();
  $currentCutoffId  = getTariffCutoffId();
  $dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);

  $disabled = count($userRec) > 0 ? "" : "disabled='disabled'";

  $disabledclass = count($userRec) > 0 ? "" : "disable";

  $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
  $workshopCountArr      = totalWorkshopCountReport();

  // echo '<pre>'; print_r($workshopDetailsArray);die;


  $registrationAmount   = getCutoffTariffAmnt($currentCutoffId);

  $accomCount = ($registrationAmount) ? '1' : '0';

  $sqlFetchHotel      = array();
  $sqlFetchHotel['QUERY'] = "SELECT * 
                                         FROM " . _DB_MASTER_HOTEL_ . "
                                        WHERE `status` =  ? ";

  $sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
  $resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);


  //echo count($resultFetchHotel);

  $countAcc = ($resultFetchHotel) ? '1' : '0';
  $blurclass = count($userRec) > 0 ? "" : "blur_bw";



  //echo '<pre>'; print_r($userRec);


  ?>
  <form class="body-frm" name="registrationForm" enctype="multipart/form-data" method="post" autocomplete="off" action="<?= _BASE_URL_ ?>registration.process.php">
    <input type="hidden" name="act" value="combinedRegistrationProcess" />
    <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" />
    <input type="hidden" name="reg_area" value="FRONT" />
    <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
    <input type="hidden" name="registration_cutoff" id="registration_cutoff" value="<?= $currentCutoffId ?>" />
    <input type="hidden" name="abstractDelegateId" id="abstractDelegateId" value="<?= $abstractDelegateId ?>" />
    <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />

    <main>

      <?php include_once('sidebar_dkstop_icon.php'); ?>

      <section class="main-section">
        <div class="container">
          <div class="inner-greadient-sec">
            <div class="row">
              <?php
              $mycms->setSession('EMAIL', "email");
              $email = $mycms->getSession('EMAIL');
              // echo "email: ".$email;
              ?>
              <div class="col-lg-6 carvslider-holder bdrRight">

                <div class="cart disabled-click" id="cart" style="display:none; filter: blur(1.2px);">
                  <img src="<?= _BASE_URL_ ?>images/cart.png" alt="">
                </div>
                <div id="header" class="category-head">
                  <div class="category-left">
                    <h3 id="pageTitle"><?= $cfg['CATEGORY_TITLE'] ?></h3>

                  </div>
                  <div class="headtimer">
                    <div class="category-right">
                      <h4><i><?= getCutoffName($currentCutoffId) ?></i></h4>
                      <p>till <strong><?php
                                      $endDate = getCutoffEndDate($currentCutoffId);
                                      echo date('jS M`  y', strtotime($endDate)); ?></strong>
                      </p>
                    </div>
                    <div class='cards'>

                      <div class='card days'>
                        <div class='flip-card'>
                          <div class='top-half'></div>
                          <div class='bottom-half'></div>
                        </div>
                        <p>Days</p>
                      </div>

                      <div class='card hours'>
                        <div class='flip-card'>
                          <div class='top-half'></div>
                          <div class='bottom-half'></div>
                        </div>
                        <p>Hours</p>
                      </div>

                      <div class='card minutes'>
                        <div class='flip-card'>
                          <div class='top-half'></div>
                          <div class='bottom-half'></div>
                        </div>
                        <p>Minutes</p>
                      </div>

                      <div class='card seconds'>
                        <div class='flip-card'>
                          <div class='top-half'></div>
                          <div class='bottom-half'></div>
                        </div>
                        <p>Seconds</p>
                      </div>

                    </div>
                  </div>
                </div>
                <div class="lftminhgt">

                  <div class="category-table-row section" id="section1">
                    <div class="lftinrminH-holder">
                      <div class="lftinrminH row align-items-center">
                        <div class="lftinrminH-inner">
                          <?php
                          if ($currentCutoffId > 0) {

                            $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                            //$workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                            $workshopCountArr      = totalWorkshopCountReport();

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

                                $icon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $registrationDetailsVal['ICON']


                          ?>

                                <label class="category-check" data-aos="fade-up" data-aos-delay="0" data-aos-duration="100">
                                  <input type="radio" name="registration_classification_id[]" id="registration_classification_id" operationMode="registration_tariff" operationModeType="conference" reg="reg" value="<?= $registrationDetailsVal['REG_CLASSIFICATION_ID'] ?>" currency="<?= $registrationDetailsVal['CURRENCY'] ?>" amount="<?= $registrationDetailsVal['AMOUNT'] ?>" invoiceTitle="Registration - <?= $registrationDetailsVal['CLASSIFICATION_TITTLE'] ?>" <?= $disableClass ?> icon="<?= $icon ?>">
                                  <div class="category-select-div">
                                    <div class="cate-select-img">
                                      <img src="<?= $icon ?>" alt="" />
                                      <h4><?= $registrationDetailsVal['CLASSIFICATION_TITTLE'] ?></h4>
                                    </div>
                                    <h5 style="font-size:16px"><?
                                                                if (floatval($registrationDetailsVal['AMOUNT']) > 0) {
                                                                  echo '₹ <b>' . number_format(($registrationDetailsVal['AMOUNT'])) . '</b>';
                                                                } else {
                                                                  echo "Complimentary";
                                                                }
                                                                ?>
                                    </h5>
                                  </div>
                                </label>

                            <?php

                              } // end if

                            } //end foreach 
                            ?>
                            <!-- <span style="font-size:14px;font-weight: bolder;"><b><i>*Recent issued HOD Consent Letter along with any valid Govt. 
                            ID Proof is mandatory to collect Registration ID Card before attending the Conference. </i></b></span> -->
                          <?php      }
                          ?>


                        </div>
                      </div>
                    </div>

                    <!-- <div class="next-prev-btn-wrap d-flex justify-content-end pr-5 pt-20">
                      <a class="btn next-btn prev"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                          <path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z" />
                        </svg></a>
                      <a class="btn next-btn next" title="<?= $cfg['USER_TITLE'] ?>"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                          <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                        </svg></a>
                    </div> -->

                    <div class="bottom-fx">
                      <div class="next-prev-btn-box ">
                        <a class="btn next-btn prev">Prev</a>
                        <a class="btn next-btn next" title="<?= $cfg['USER_TITLE'] ?>">Next</a>
                      </div>
                    </div>

                  </div>
                  <div class="pd-row section" use="registrationUserDetails" id="section2">
                    <div class="lftinrminH-holder">
                      <?php
                      if (in_array("Address", $available_registration_fields)) {
                        $disply = 'block';
                      } else {
                        $disply = 'none';
                      }
                      if (in_array("Country", $available_registration_fields)) {
                        $disply = 'block';
                      } else {
                        $disply = 'none';
                      }
                      if (in_array("State", $available_registration_fields)) {
                        $disply = 'block';
                      } else {
                        $disply = 'none';
                      }
                      if (in_array("City", $available_registration_fields)) {
                        $disply = 'block';
                      } else {
                        $disply = 'none';
                      }
                      if (in_array("Pin", $available_registration_fields)) {
                        $disply = 'block';
                      } else {
                        $disply = 'none';
                      }
                      if (in_array("Gender", $available_registration_fields)) {
                        $disply = 'block';
                      } else {
                        $disply = 'none';
                      }

                      $sql_notification   =  array();
                      $sql_notification['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
                                       WHERE `id` = ?";
                      $sql_notification['PARAM'][]  =  array('FILD' => 'id',          'DATA' => $_REQUEST['id'],                    'TYP' => 's');
                      $result       = $mycms->sql_select($sql_notification);
                      $row         = $result[0];
                      $invalidEmail = $row['notification_invalid_email'];

                      ?>
                      <div class="lftinrminH">
                        <div class="row">
                          <div class="col-lg-12 form mb-3">
                            <div class="d-flex">
                              <input type="hidden" id="invalidEmail" value="<?= $invalidEmail ?>">
                              <span> <img src="images/email-R.png" alt=""></span>
                              <input type="text" class="form-control" name="user_email_id" id="user_email_id" value="<?= ($userRec['user_email_id'] != '') ? ($userRec['user_email_id']) : '' ?>" placeholder="Email" validate="Please Enter Email Address" autocomplete="nope">
                            </div>
                          </div>

                          <div class="col-lg-12 form mb-3 ">
                            <div class="d-flex">
                              <span><img src="images/phone-R.png" alt=""></span>
                              <input type="text" class="form-control " name="user_mobile" id="user_mobile" value="<?= ($userRec['user_mobile_isd_code'] != '') ? ($userRec['user_mobile_isd_code']) : '' ?><?= ($userRec['user_mobile_no'] != '') ? ($userRec['user_mobile_no']) : '' ?>" maxlength="10" onkeypress="return isNumber(event)" required placeholder="Mobile" validate="Please Enter Mobile Number" autocomplete="nope">
                              <div class="col-lg-1">

                                <input type="button" class="pay-button" onclick="checkUserEmail(this)" value="Go" style="margin: 4px 5px;padding-left: 15px;">
                              </div>
                            </div>
                          </div>

                          <div class="col-lg-12 form mb-3 select-title <?= $blurclass ?>">
                            <div class="d-flex">
                              <span><img src="images/Name-R.png" alt=""></span>
                              <select name="user_initial_title" class="col-lg-1 <?= $disabledclass ?>" <?= $disabled ?>>
                                <option value="Mr" <?php if ($userRec['user_title'] == 'MR') {
                                                      echo 'selected';
                                                    } ?>>Mr.</option>
                                <option value="Mrs" <?php if ($userRec['user_title'] == 'MRS') {
                                                      echo 'selected';
                                                    } ?>>Mrs.</option>
                                <option value="Dr" <?php if ($userRec['user_title'] == 'DR') {
                                                      echo 'selected';
                                                    } ?>>Dr.</option>
                              </select>
                              <div class="input-wrap col-lg-10">
                                <div class="form-floating">

                                  <input type="text" class="form-control <?= $disabledclass ?>" placeholder="First Name" name="user_first_name" id="user_first_name" validate="Please Enter First Name" autocomplete="off" <?= $disabled ?> value="<?= ($userRec['user_first_name'] != '') ? ($userRec['user_first_name']) : '' ?>" autocomplete="nope">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-12 form mb-3 <?= $blurclass ?>">
                            <div class="d-flex">
                              <span><img src="images/Name-R.png" alt=""></span>
                              <input type="text" class="form-control <?= $disabledclass ?>" placeholder="Middle Name" name="user_middle_name" id="user_middle_name" value="<?= ($userRec['user_middle_name'] != '') ? ($userRec['user_middle_name']) : '' ?>" autocomplete="off" <?= $disabled ?> autocomplete="nope">
                            </div>
                          </div>
                          <div class="col-lg-12 form mb-3 <?= $blurclass ?>">
                            <div class="d-flex">
                              <span><img src="images/Name-R.png" alt=""></span>
                              <input type="text" class="form-control <?= $disabledclass ?>" placeholder="Last Name" name="user_last_name" id="user_last_name" value="<?= ($userRec['user_last_name'] != '') ? ($userRec['user_last_name']) : '' ?>" validate="Please Enter Last Name" autocomplete="off" <?= $disabled ?> autocomplete="nope">
                            </div>
                          </div>
                          <?php
                          if (in_array("Address", $available_registration_fields)) {
                          ?>
                            <div class="col-lg-12 form mb-3 <?= $blurclass ?>">
                              <div class="d-flex">
                                <span><img src="images/address-R.png" alt=""></span>
                                <input type="text" class="form-control <?= $disabledclass ?>" name="user_address" id="user_address" value="<?= ($userRec['user_address'] != '') ? ($userRec['user_address']) : '' ?>" placeholder="Address" autocomplete="off" <?= $disabled ?> validate="Please Enter Your Address" autocomplete="nope">
                              </div>
                            </div>
                          <?php
                          }

                          if (in_array("Country", $available_registration_fields)) {
                          ?>

                            <div class="col-lg-12 form mb-3 <?= $blurclass ?>">
                              <div class="d-flex">
                                <span><img src="images/country-R.png" alt=""></span>
                                <select class="form-control select <?= $disabledclass ?>" name="user_country" id="user_country" forType="country" required <?= $disabled ?> validate="Please Select Country">
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
                            </div>
                          <?php
                          }
                          if (in_array("State", $available_registration_fields)) {
                          ?>

                            <div class="col-lg-12 form mb-3 <?= $blurclass ?>">
                              <div class="d-flex">
                                <span><img src="images/state-R.png" alt=""></span>
                                <select class="form-control select <?= $disabledclass ?>" name="user_state" id="user_state" forType="state" required <?= $disabled ?> validate="Please Select State">
                                  <option value="">-- Select Country First --</option>
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
                                      <option value="<?= $rowFetchState['st_id'] ?>" <?= ($rowFetchState['st_id'] == $userRec['user_state_id']) ? 'selected' : '' ?>><?= $rowFetchState['state_name'] ?></option>
                                  <?php
                                    }
                                  }
                                  ?>
                                </select>
                              </div>
                            </div>
                          <?php
                          }

                          if (in_array("City", $available_registration_fields)) {
                          ?>
                            <div class="col-lg-6 form mb-3 <?= $blurclass ?>">
                              <div class="d-flex">
                                <span><img src="images/city-R.png" alt=""></span>
                                <input type="text" class="form-control <?= $disabledclass ?>" name="user_city" id="user_city" value="<?= ($userRec['user_city'] != '') ? ($userRec['user_city']) : '' ?>" placeholder="City" autocomplete="off" <?= $disabled ?> validate="Please Enter City" autocomplete="nope">
                              </div>
                            </div>
                          <?php
                          }

                          if (in_array("Pin", $available_registration_fields)) {
                          ?>

                            <div class="col-lg-6 form mb-3 <?= $blurclass ?>" actas="fieldContainer" hasdisablecss="Y">
                              <div class="d-flex">
                                <span><img src="images/postal-R.png" alt=""></span>
                                <input type="text" class="form-control <?= $disabledclass ?>" name="user_postal_code" id="user_postal_code" value="<?= ($userRec['user_pincode'] != '') ? ($userRec['user_pincode']) : '' ?>" <?= $disabled ?> placeholder="Postal Code" validate="Please enter postal code" onkeypress="return isNumber(event)" autocomplete="nope">
                              </div>
                            </div>

                          <?php
                          }

                          if (in_array("Gender", $available_registration_fields)) {
                          ?>
                            <div class="col-lg-6 mb-3 d-flex align-items-center p-0 <?= $blurclass ?>" id="radioGender">
                              <label>Gender: </label>
                              <label class="custom-radio"> Male <input type="radio" validate="Please select a gender" groupname="user_gender" name="user_gender" id="user_gender_male" value="Male" <?= $disabled ?> required=""><span class="checkmark"></span></label>

                              <label class="custom-radio">Female <input type="radio" validate="Please select a gender" groupname="user_gender" name="user_gender" id="user_gender_female" value="Female" <?= $disabled ?> required><span class="checkmark"></span> </label>
                            </div>

                          <?php
                          }
                          if (in_array("Food", $available_registration_fields)) {
                          ?>
                            <!-- <div class="col-lg-6 mb-3 d-flex align-items-center p-0 <?= $blurclass ?>" id="radioFood">
                              <label>Food Preference: </label>
                              <label class="custom-radio"> Veg <input type="radio" groupname="user_food_choice" name="user_food_choice" id="user_food_choice_veg" value="veg" <?= $disabled ?> required=""><span class="checkmark"></span></label>

                              <label class="custom-radio">Non-Veg <input type="radio" groupname="user_food_choice" name="user_food_choice" id="user_food_choice_nonveg" value="nonveg" <?= $disabled ?> required><span class="checkmark"></span> </label>
                            </div> -->
                            <div class="col-lg-6 mb-3 d-flex align-items-center p-0 <?= $blurclass ?>" id="radioFood">
                              <label>Food Preference: </label>
                              <label class="custom-radio"> Veg <input type="radio" groupname="user_food_choice" name="user_food_choice" id="user_food_choice_veg" validate="Please select your food preference" value="veg" <?= $disabled ?> required=""><span class="checkmark"></span></label>

                              <label class="custom-radio">Non-Veg <input type="radio" groupname="user_food_choice" name="user_food_choice" id="user_food_choice_nonveg"  validate="Please select your food preference" value="nonveg" <?= $disabled ?> required><span class="checkmark"></span> </label>
                            </div>


                            <!-- <div class="col-lg-12">
                              <p>Food Preference</p>
                              <div class="custom-checkbox ">
                                <input type="radio" name="user_food_choice[0]" id="veg" value="VEG">
                                <label for="veg"><span>Veg</span></label>
                              </div>
                              <div class="custom-checkbox">
                                <input type="radio" name="user_food_choice[0]" id="nonveg" value="NON_VEG">
                                <label for="nonveg"><span>Non-Veg</span></label>
                              </div>
                            </div> -->
                          <?php
                          } ?>

                        </div>




                        <?php

                        ?>
                      </div>
                    </div>
                    <div class="bottom-fx">
                      <div class="total-price">
                        <h6>TOTAL</h6>
                        <hr>
                        <strong>&#8377; <span id="confPrc">27000.00</span></strong>
                      </div>
                      <!-- <div class="next-prev-btn-wrap pr-5 d-flex justify-content-end">
                      <a class="btn next-btn prev" title="<?= $cfg['CATEGORY_TITLE'] ?>"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                          <path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z" />
                        </svg></a>

                      <a class="btn next-btn btn-w next" id="user_details" style="display:<?php if (count($userRec) > 0) {
                                                                                            echo 'block';
                                                                                          } else {
                                                                                            echo 'none';
                                                                                          } ?>" workshop-count="<?= count($workshopDetailsArray) ?>" accompany-count="<?= $accompanyCount ?>" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>" title="<?= $cfg['WORKSHOP_TITLE'] ?>"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                          <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                        </svg>
                      </a>


                    </div> -->

                      <?php
                      if (!empty($workshopDetailsArray)) {
                        $nextSectionTitle = $cfg['WORKSHOP_TITLE'];
                      } else {
                        $nextSectionTitle = $cfg['ACCOMAPNY_TITLE'];
                      }
                      ?>

                      <div class="next-prev-btn-box ">

                        <a class="btn next-btn prev" title="<?= $cfg['CATEGORY_TITLE'] ?>">Prev</a>
                        <a class="btn next-btn btn-w next <?php if (count($userRec) > 0) {
                                                            echo '';
                                                          } else {
                                                            echo 'disabled-click';
                                                          } ?>" id="user_details" style="display:<?php if (count($userRec) > 0) {
                                                                                                    echo 'block';
                                                                                                  } else {
                                                                                                    echo 'block';
                                                                                                  } ?>" workshop-count="<?= count($workshopDetailsArray) ?>" accompany-count="<?= $accompanyCount ?>" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>" title="<?= $nextSectionTitle ?>">Next
                        </a>
                      </div>

                    </div>
                  </div>
                  <div class="category-table-rowxx drama-total-holder add-inclusion section" id="section3">
                    <!-- <div class="lftinrminH-holder"> -->
                    <?php

                    $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                    // $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
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

                    if ((isset($workshopRegChoices['MASTER CLASS']) && sizeof($workshopRegChoices['MASTER CLASS']) > 0) || (isset($workshopRegChoices['WORKSHOP']) && sizeof($workshopRegChoices['WORKSHOP']) > 0)) {
                    ?>
                      <div class="lftinrminH-holder">
                        <div class="lftinrminH">
                          <?php

                          $loopcount = 0;
                          foreach ($workshopRegChoices['WORKSHOP'] as $keyWorkshopclsf => $rowWorkshopclsf) {

                            foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {
                              //echo '<pre>'; print_r($rowRegClasf);

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

                              $sql_icon   =  array();
                              $sql_icon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `id`='2' AND `purpose`='Registration' AND status IN ('A', 'I')";
                              $result    = $mycms->sql_select($sql_icon);
                              $workshopIcon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];

                              //echo $workshop_date->format('F');
                          ?>


                              <div class="accompany-row " use="<?= $keyRegClasf ?>" operetionMode="workshopTariffTr" style="display:none;" operetionDisplay="workshopDisplay<?= $rowRegClasf['WORKSHOP_ID'] ?>" class="workCombo">
                                <div class="custom-checkbox accompanying ">
                                  <input type="radio" name="workshop_id[]" id="workshop_id_<?= $keyWorkshopclsf . '_' . $keyRegClasf ?>" value="<?= $rowRegClasf['WORKSHOP_ID'] ?>" <?= $style ?> workshopName="<?= $rowRegClasf['WORKSHOP_GRP'] ?>" operationMode="workshopId" amount="<?= $rowRegClasf[$rowRegClasf['CURRENCY']] ?>" invoiceTitle="Workshop - <?= $rowRegClasf['WORKSHOP_NAME'] ?>" registrationClassfId="<?= $keyRegClasf ?>" workshopCount="<?= $workshopCount ?>" icon="<?= $workshopIcon ?>" reg="workshop">
                                  <label for="workshop_id_<?= $keyWorkshopclsf . '_' . $keyRegClasf ?>">
                                    <div class="por-row">
                                      <div class="por-lt">
                                        <div class="por-inner">
                                          <h3><?= $displayName ?></h3>
                                        </div>
                                        <div class="por-location">
                                          <ul>
                                            <li><a href="<?= $rowRegClasf['VENUE_ADDRESS'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/loction.png" alt="" /> <?= $rowRegClasf['VENUE'] ?></a>
                                            </li>
                                          </ul>
                                        </div>
                                        <div class="por-date">
                                          <h3><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                <path d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z" />
                                              </svg></span> <?= $formattedDate ?> <?= $workshop_date->format('F'); ?></h3>

                                        </div>


                                      </div>
                                      <div class="por-rt">
                                        <div class="gala-main">
                                          <div class="acc-gala-price">
                                            <span><?= $workshopRateDisplay ?></span>
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
                          foreach ($workshopRegChoices['MASTER CLASS'] as $keyWorkshopclsf => $rowWorkshopclsf) {

                            foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {
                              //echo '<pre>'; print_r($rowRegClasf);

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

                              $sql_icon   =  array();
                              $sql_icon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `id`='2' AND `purpose`='Registration' AND status IN ('A', 'I')";
                              $result    = $mycms->sql_select($sql_icon);
                              $workshopIcon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];

                              //echo $workshop_date->format('F');
                            ?>


                              <div class="accompany-row " use="<?= $keyRegClasf ?>" operetionMode="workshopTariffTr" style="display:none;" operetionDisplay="workshopDisplay<?= $rowRegClasf['WORKSHOP_ID'] ?>" class="workCombo">
                                <div class="custom-checkbox accompanying ">
                                  <input type="radio" name="workshop_id[]" id="workshop_id_<?= $keyWorkshopclsf . '_' . $keyRegClasf ?>" value="<?= $rowRegClasf['WORKSHOP_ID'] ?>" <?= $style ?> workshopName="<?= $rowRegClasf['WORKSHOP_GRP'] ?>" operationMode="workshopId" amount="<?= $rowRegClasf[$rowRegClasf['CURRENCY']] ?>" invoiceTitle="Workshop - <?= $rowRegClasf['WORKSHOP_NAME'] ?>" registrationClassfId="<?= $keyRegClasf ?>" workshopCount="<?= $workshopCount ?>" icon="<?= $workshopIcon ?>" reg="workshop">
                                  <label for="workshop_id_<?= $keyWorkshopclsf . '_' . $keyRegClasf ?>">
                                    <div class="por-row">
                                      <div class="por-lt">
                                        <div class="por-inner">
                                          <h3><?= $displayName ?></h3>
                                        </div>
                                        <div class="por-location">
                                          <ul>
                                            <li><a href="<?= $rowRegClasf['VENUE_ADDRESS'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/loction.png" alt="" /> <?= $rowRegClasf['VENUE'] ?></a>
                                            </li>
                                          </ul>
                                        </div>
                                        <div class="por-date">
                                          <h3><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                <path d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z" />
                                              </svg></span> <?= $formattedDate ?> <?= $workshop_date->format('F'); ?></h3>

                                        </div>


                                      </div>
                                      <div class="por-rt">
                                        <div class="gala-main">
                                          <div class="acc-gala-price">
                                            <span><?= $workshopRateDisplay ?></span>
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
                          ?>



                        </div>
                      </div>
                      <div class="bottom-fx">
                        <div class="total-price" id="wrkshopPrcdiv" style="display: none;">

                          <h6>TOTAL :</h6>
                          <strong>&#8377; <span id="workshopPrc"></span></strong>
                        </div>
                        <div class="next-prev-btn-box">

                          <a class="btn next-btn prev" title="<?= $cfg['USER_TITLE'] ?>">Prev</a>
                          <a class="btn next-btn next" title="<?= $cfg['ACCOMAPNY_TITLE'] ?>" accompany-count="<?= $accompanyCount ?>" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>">Next</a>
                          <a class="btn skipnow skip" title="<?= $cfg['ACCOMAPNY_TITLE'] ?>" accompany-count="<?= $accompanyCount ?>" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>" role="button">
                            <div class="icon"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path d="M463.5 224H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1c-87.5 87.5-87.5 229.3 0 316.8s229.3 87.5 316.8 0c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0c-62.5 62.5-163.8 62.5-226.3 0s-62.5-163.8 0-226.3c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5z" />
                              </svg></div>
                            <span>Skip</span>
                          </a>
                        </div>
                      </div>


                    <?php
                    }
                    ?>

                  </div>
                  <div class="category-table-rowxx drama-total-holder section" id="section4">
                    <div class="lftinrminH-holder">
                      <?php
                      if ($registrationAmount != '') {
                      ?>
                        <div class="lftinrminH">
                          <?php

                          $accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 


                          //$registrationAmount   = getCutoffTariffAmnt($currentCutoffId);
                          $registrationCurrency   = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];



                          ?>

                          <!-- <div class="heading-ar">
                          <h4><?= $cfg['ACCOMAPNY_TITLE'] ?></h4>
                        </div> -->
                          <div class="note text-center">
                            <p></p>
                          </div>

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

                          <!--  <form class="add-inclusion"> -->
                          <div class="accompany-row">
                            <div class="custom-checkbox accompanying " id="cloneIntro">
                              <input type="checkbox" name="accompanyCount" id="accompanyCount" use="accompanyCountSelect" value="1" amount="<?= $registrationAmount ?>" invoiceTitle="Accompanying Person" icon="<?= $accompanyingIcon ?>" reg="accompany">

                              <label for="accompanyCount"><span>Accompany <span id="accomCount">1</span></span>
                                <div class="acc-main-price">
                                  <span><?= $registrationAmount ?></span> <?= $registrationCurrency ?>
                                </div>

                              </label>
                              <input type="hidden" name="accompanyClasfId" value="<?= $accompanyCatagory ?>" />

                            </div>

                            <div id="accompany-container">

                              <div class="add-accompany">

                                <div class="row">
                                  <div class="col-lg-12">

                                    <input type="text" class="form-control accompany_name" name="accompany_name_add[0]" placeholder="Name" validate="Enter the accompany name" countindex="0">
                                    <input type="hidden" name="accompany_selected_add[0]" value="0" />
                                  </div>
                                  <?php
                                  $sql_cal      =  array();
                                  $sql_cal['QUERY']  =  "SELECT * 
                                              FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . "
                                              WHERE `status` 	!= 	'D'";
                                  $res_cal = $mycms->sql_select($sql_cal);
                                  if ($res_cal[0]['food_preference'] == 'A') {
                                  ?>
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
                                  <?php } ?>
                                </div>
                                <?php
                                $sql_cal   =  array();
                                $sql_cal['QUERY']  =  "SELECT * 
                                             FROM " . _DB_ACCOMPANY_CLASSIFICATION_;
                                // $sql['PARAM'][]		=	array('FILD' => 'id', 		  'DATA' => $_REQUEST['id'],				   'TYP' => 's');
                                $res_cal = $mycms->sql_select($sql_cal);
                                $row    = $res_cal[0];
                                ?>
                                <div class="accompany-inner">
                                  <?php if ($row['inclusion_conference_kit'] != "N" || json_decode($row['inclusion_lunch_date']) != "" ||  json_decode($row['inclusion_dinner_date']) != "") {

                                    $sql   =  array();
                                    $sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                                      WHERE `id`!='' AND `purpose`='Mailer' AND status IN ('A', 'I')";
                                    //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
                                    $result    = $mycms->sql_select($sql);

                                  ?>
                                    <p>Inclusion</p>
                                    <div class="acc-name-wrap">
                                      <div class="acc-inclusion">
                                        <div class="acc-icons">
                                          <!-- <img src="<?= _BASE_URL_ ?>images/ac1.png" alt="" class="acc-icons-tooltip" title="Inclusions" /> -->
                                          <?php
                                          // if (json_decode($row['inclusion_conference_kit_date']) != "") {
                                          if ($res_cal[0]['inclusion_conference_kit'] == 'Y') {
                                            // echo ($row['inclusion_conference_kit_date']); 
                                          ?>
                                            <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[2]['icon'] ?>" alt="" class="acc-icons-tooltip" title="Conference Kit" />
                                          <?php } ?>
                                          <?php if (json_decode($res_cal[0]['inclusion_lunch_date']) != "") { ?>
                                            <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[4]['icon'] ?>" alt="" class="acc-icons-tooltip" title="Lunch" />
                                          <?php } ?>
                                          <?php if (json_decode($res_cal[0]['inclusion_dinner_date']) != "") { ?>
                                            <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[5]['icon'] ?>" alt="" class="acc-icons-tooltip" title="Dinner" />
                                          <?php } ?>
                                          <!-- <img src="<?= _BASE_URL_ ?>images/ac4.png" alt="" class="acc-icons-tooltip" title="Image Title4" /> -->
                                        </div>
                                      </div>
                                    </div>
                                  <?php } ?>

                                </div>

                              </div>
                            </div>




                            <input type="hidden" name="accompanyAmount" id="accompanyAmount" value="<?= $registrationAmount ?>">
                            <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?= $registrationAmount ?>">

                            <input type="hidden" name="accompanyCounts" id="accompanyCounts" value="1">

                          </div>

                          <button class="btn" id="add-accompany-btn">Add More</button>
                          <!-- </form> -->


                        </div>
                    </div>

                    <div class="bottom-fx">
                      <div class="total-price " class="accompanyPrcdiv" style="display: none;">

                        <h6>TOTAL :</h6>

                        <strong>&#8377; <span id="accompanyPrc"></span></strong>

                      </div>
                      <?php
                        if (!empty($workshopDetailsArray)) {
                          $previousSectionTitle = $cfg['WORKSHOP_TITLE'];
                        } else {
                          $previousSectionTitle = $cfg['USER_TITLE'];
                        }


                      ?>
                      <div class="next-prev-btn-box">



                        <a class="btn next-btn prev" title="<?= $previousSectionTitle ?>" workshop-count="<?= count($workshopDetailsArray) ?>">Prev</a>

                        <a class="btn next-btn next" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>">Next</a>
                        <a class="btn  skipnow skip" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>" role="button">
                          <div class="icon"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                              <path d="M463.5 224H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1c-87.5 87.5-87.5 229.3 0 316.8s229.3 87.5 316.8 0c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0c-62.5 62.5-163.8 62.5-226.3 0s-62.5-163.8 0-226.3c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5z" />
                            </svg></div>
                          <span>Skip</span>
                        </a>
                      </div>

                    </div>



                  <?php
                      }

                  ?>
                  </div>
                  <!-- ========================= BANQUET ======================================= -->
                  <div class="category-table-rowxx drama-total-holder section add-inclusion" id="section5">

                    <?php
                    $sql_email  = array();
                    $sql_email['QUERY']    = "SELECT * FROM " . _DB_EMAIL_CONSTANT_ . " 
                                 WHERE `id` = ?";
                    $sql_email['PARAM'][] = array('FILD' => 'id',         'DATA' => 1,                   'TYP' => 's');
                    $result      = $mycms->sql_select($sql_email);
                    $row         = $result[0];


                    if (count($dinnerTariffArray) > 0) {

                      //echo '<pre>'; print_r($dinnerTariffArray);        
                    ?>

                      <div class="heading-ar2">
                        <h4>Gala Dinner</h4>
                      </div>

                      <div class="gala-dinner-select ">
                        <div class="gala-row">
                          <div class="d-flex align-items-center">
                            <div class="gala-inner-lt">
                              <div class="gala-inner">
                                <div class="gala-box">
                                  <img src="<?= _BASE_URL_ ?>images/gala-logo.png" alt="" class="gold-img" />
                                </div>
                                <div class="gala-name">
                                  <h5 id="dinner_name">Saswata Dasgupta</h5>
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


                                    if (floatval($dinnerValue[$currentCutoffId]['AMOUNT']) > 0) {
                                      echo '<strong>' . number_format($dinnerValue[$currentCutoffId]['AMOUNT'], 2) . '</strong> <br>' . $registrationDetailsVal['CURRENCY'];
                                    }
                                  }
                                  $sql1['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
													WHERE `id`='4' AND `purpose`='Registration' AND status IN ('A', 'I')";
                                  $result    = $mycms->sql_select($sql1);
                                  $banquetIcon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'];

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
                                <input type="checkbox" class="checkboxClassDinner" name="dinner_value[0]" id="dinner_value" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" operationMode="dinner" use="dinner" amount="<?= $dinnerValue[$currentCutoffId]['AMOUNT'] ?>" invoiceTitle="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?>-conference" icon="<?= $banquetIcon ?>" reg="dinner" qty="1" />

                                <label for="dinner_value">Please choose Now</label>
                              </div>
                            </div>
                          <?php
                          }
                          ?>
                        </div>

                      </div>
                      <div class="gala-dinner-select " id="gala-dinner-select1">
                      </div>
                      <div class="row mt-4 align-items-center">
                        <div class="col-lg-6 mb-3 total-price ">
                          <div class="dinnerPrcdiv" style="display: none;">
                            <h6>TOTAL</h6>
                            <hr>
                            <strong>&#8377; <span id="dinnerPrc"></span></strong>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="next-prev-btn-box">
                            <a class="btn next-btn prev" accompany-count="<?= $countAcc ?>">Prev</a>



                            <a class="btn next-btn next" accommodation-count="<?= $countAcc ?>">Next</a>
                            <a class="btn skipnow skip" accommodation-count="<?= $countAcc ?>" role="button">
                              <div class="icon"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                  <path d="M463.5 224H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1c-87.5 87.5-87.5 229.3 0 316.8s229.3 87.5 316.8 0c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0c-62.5 62.5-163.8 62.5-226.3 0s-62.5-163.8 0-226.3c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5z" />
                                </svg></div>
                              <span>Skip</span>
                            </a>
                          </div>
                        </div>
                      </div>

                    <?php
                    }
                    ?>

                  </div>
                  <div class="carvslider-holder drama-total-holder  section no-border accm-wrap" id="section6">
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

                      <div class="accom-box">
                        <img src="https://ruedakolkata.com/newdesign2023/css/images/Semi%20CIRCLE.png" class="accm-rot">
                        <div class="elementor-container h-100">
                          <div class="row pxl-content-wrap no-sidebar h-100">
                            <div id="pxl-content-area" class="pxl-content-area pxl-content-page col-12 h-100">
                              <div id="pxl-content-main " class="h-100">
                                <article id="pxl-post-7" class="post-7 page type-page status-publish hentry h-100">
                                  <div class="pxl-entry-content clearfix h-100">
                                    <div data-elementor-type="wp-page" data-elementor-id="7" class="elementor elementor-7 h-100">
                                      <section class="h-100 accm-sldr-box elementor-section elementor-top-section elementor-element elementor-element-a03876a elementor-section-stretched elementor-section-full_width elementor-section-height-default elementor-section-height-default pxl-row-scroll-none pxl-section-overflow-visible  " data-id="a03876a" data-element_type="section" data-settings="{&quot;stretch_section&quot;:&quot;section-stretched&quot;,&quot;background_background&quot;:&quot;classic&quot;}">
                                        <div class="h-100 elementor-container elementor-column-gap-no ">
                                          <div class="h-100 elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-9e06ed6 pxl-column-none" data-id="9e06ed6" data-element_type="column">
                                            <div class="h-100 elementor-widget-wrap elementor-element-populated">
                                              <div class="h-100 elementor-element elementor-element-6c27130 elementor-widget elementor-widget-pxl_slider" data-id="6c27130" data-element_type="widget" data-widget_type="pxl_slider.default">
                                                <div class="h-100 elementor-widget-container">
                                                  <div class="h-100 pxl-element-slider pxl-swiper-sliders pxl-slider pxl-slider1">
                                                    <div class="bg-image bg-image-parallax" data-parallax='{"y":-70}'></div>
                                                    <div class="pxl-carousel-inner">
                                                      <div class="pxl-swiper-container" dir="ltr" data-settings="{&quot;slide_direction&quot;:&quot;horizontal&quot;,&quot;slide_percolumn&quot;:&quot;1&quot;,&quot;slide_mode&quot;:&quot;fade&quot;,&quot;slides_to_show&quot;:&quot;1&quot;,&quot;slides_to_show_xxl&quot;:&quot;1&quot;,&quot;slides_to_show_lg&quot;:&quot;1&quot;,&quot;slides_to_show_md&quot;:&quot;1&quot;,&quot;slides_to_show_sm&quot;:&quot;1&quot;,&quot;slides_to_show_xs&quot;:&quot;1&quot;,&quot;slides_to_scroll&quot;:&quot;1&quot;,&quot;arrow&quot;:&quot;true&quot;,&quot;pagination&quot;:&quot;false&quot;,&quot;pagination_type&quot;:&quot;bullets&quot;,&quot;autoplay&quot;:&quot;&quot;,&quot;pause_on_hover&quot;:&quot;&quot;,&quot;pause_on_interaction&quot;:&quot;true&quot;,&quot;delay&quot;:5000,&quot;loop&quot;:&quot;false&quot;,&quot;drap&quot;:&quot;false&quot;,&quot;speed&quot;:500}">
                                                        <div class="pxl-swiper-wrapper">

                                                          <?php

                                                          foreach ($resultFetchHotel as $k => $val) {
                                                            $hotel_background_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['hotel_animation_image'];
                                                          ?>

                                                            <div class="pxl-swiper-slide">
                                                              <div class="pxl-item--inner">
                                                                <div class="pxl-item--left">
                                                                  <div class="pxl-item--image">
                                                                    <div class="pxl-image--main wow zoomInSmall" data-wow-delay="0ms">
                                                                      <img decoding="async" class="no-lazyload " src="<?= $hotel_background_image ?>" width="493" height="493" alt="slider-dish-1" title="slider-dish-1" />
                                                                    </div>
                                                                    <div class="pxl-image--rotate wow PXLrotateIn" data-wow-delay="0ms">
                                                                      <img decoding="async" class="no-lazyload " src="<?= $hotel_background_image ?>" width="493" height="493" alt="slider-dish-1" title="slider-dish-1" />
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <div class="pxl-item--right">
                                                                  <div class="pxl-item--content">

                                                                    <div class="pxl-item--popular pxl-flex wow skewIn" data-wow-delay="700ms">
                                                                      <span class="accm-rating">
                                                                        <i class="fas fa-star stared"></i>
                                                                        <i class="fas fa-star stared"></i>
                                                                        <i class="fas fa-star stared"></i>
                                                                        <i class="fas fa-star stared"></i>
                                                                        <i class="fas fa-star stared"></i>
                                                                      </span>
                                                                    </div>
                                                                    <h3 class="pxl-item--title wow skewIn" data-wow-delay="700ms"><?= $val['hotel_name'] ?></h3>
                                                                    <div class="pxl-item--desc wow skewInRight" data-wow-delay="700ms"><i class="far fa-map-marker-alt"></i><?= $val['hotel_address'] ?></div>
                                                                    <!-- <div class="pxl-item--desc wow skewInRight" data-wow-delay="700ms"><i class="fal fa-utensils"></i> Breakfast Included</div> -->
                                                                    <div class="pxl-item--button wow skewIn" data-wow-delay="700ms">
                                                                      <a href="#" class="button-40" onclick="getAccommodationDetails('<?= $val['id'] ?>')">Book Now</a>
                                                                      <a href="#" class="button-40">Explore</a>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                          <?php }
                                                          ?>

                                                        </div>
                                                      </div>
                                                    </div>

                                                    <div class="pxl-swiper-thumbs-wrap">
                                                      <div class="pxl-swiper-thumbs">
                                                        <div class="swiper-wrapper">
                                                          <?php
                                                          $sqlSlider = array();
                                                          $sqlSlider['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $val['id'] . "' AND status='A' AND purpose='slider'";

                                                          $querySlider = $mycms->sql_select($sqlSlider, false);

                                                          foreach ($querySlider as $k => $val) {
                                                            $hotel_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['accessories_icon'];

                                                            // foreach ($resultFetchHotel as $k => $val) {
                                                            //   $hotel_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['hotel_image'];
                                                          ?>


                                                            <div class="swiper-slide accm-slider-swipe">
                                                              <img class="accm-img" src="<?= $hotel_image ?>" data-no-retina="">
                                                            </div>

                                                          <?php }
                                                          ?>
                                                        </div>
                                                      </div>
                                                      <div class="pxl-swiper-arrow-wrap">
                                                        <div class="pxl-swiper-arrow pxl-swiper-arrow-prev style-1">
                                                          <i class="fal fa-chevron-left"></i>
                                                        </div>
                                                        <div class="pxl-swiper-arrow pxl-swiper-arrow-next style-1 active">
                                                          <i class="fal fa-chevron-right"></i>
                                                        </div>
                                                      </div>
                                                    </div>


                                                  </div>

                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                    </div>
                                  </div>
                                </article>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="bottom-fx">
                        <div class="total-price" id="wrkshopPrcdiv" style="display: none;">
                          <h6>TOTAL :</h6>
                          <strong>&#8377; <span id="workshopPrc"></span></strong>
                        </div>

                        <?php
                        if ($countAcc > 0) {
                          $previousSectionTitle = $cfg['ACCOMAPNY_TITLE'];
                        } else if (!empty($workshopDetailsArray)) {
                          $previousSectionTitle = $cfg['WORKSHOP_TITLE'];
                        } else {
                          $previousSectionTitle = $cfg['USER_TITLE'];
                        }


                        ?>
                        <div class="next-prev-btn-box">
                          <a class="btn next-btn prev" title="<?= $previousSectionTitle ?>" accompany-count="<?= $countAcc ?>" banquet-count="<?= count($dinnerTariffArray) ?>">Prev</a>
                          <a class="btn next-btn next1" id="paynowbtn">Next</a>
                          <a class="btn skipnow skip1" id="paynowbtn" role="button">
                            <div class="icon"><svg class="icon-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path d="M463.5 224H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1c-87.5 87.5-87.5 229.3 0 316.8s229.3 87.5 316.8 0c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0c-62.5 62.5-163.8 62.5-226.3 0s-62.5-163.8 0-226.3c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5z" />
                              </svg></div>
                            <span>Skip</span>
                          </a>
                        </div>
                      </div>
                    <?php
                    }

                    if ($countAcc == 0) {
                    ?>
                      <!-- <a  class="btn btn-white prev">Previous</a> -->
                    <?php
                    }


                    ?>


                  </div>

                  <div class="smobilexx drama-total-holder section" id="section7">
                  </div>
                </div>


              </div>
              <!-- </div> -->

              <div class="col-lg-6 right-slider">
                <?php

                $sql_email   =  array();
                $sql_email['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                                        WHERE `status`='A' order by id desc limit 1";
                //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
                $result = $mycms->sql_select($sql_email);
                $row         = $result[0];

                $header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];
                ?>
                <div class="logo-section" data-aos="fade-left" data-aos-duration="800">

                  <div class="site-logo">
                    <a href="#"><img src="<?= $header_image ?>" alt="" /></a>
                  </div>
                </div>
                <div class="minhgt">
                  <div class="site-menu-holder" id="site-menu-holder">
                    <a onclick="myFunction()" class="clicknow"><img src="<?= _BASE_URL_ ?>images/toggle-nav-up.png"></a>

                    <input type="hidden" name="iconCount" id="iconCount" value="<?php echo count($resultIcon); ?>">
                    <div class="site-menu d-none">

                      <?php
                      $sqlWorkShop  = array();
                      $sqlWorkShop['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
                                         WHERE `status` = ?";
                      $sqlWorkShop['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
                      $resultWorkshop      = $mycms->sql_select($sqlWorkShop);
                      if ($resultWorkshop[0]['COUNTDATA'] == 0) {
                        $totalSection--;
                      }

                      $sqlAccompany   = array();
                      $sqlAccompany['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
                                         WHERE `status` = ?";
                      $sqlAccompany['PARAM'][]  = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
                      $resultAccompany       = $mycms->sql_select($sqlAccompany);
                      if ($resultAccompany[0]['COUNTDATA'] == 0) {
                        $totalSection--;
                      }
                      $sqlDinner  = array();
                      $sqlDinner['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_DINNER_CLASSIFICATION_ . " 
                                         WHERE `status` = ?";
                      $sqlDinner['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
                      $resultDinner      = $mycms->sql_select($sqlDinner);
                      if ($resultDinner[0]['COUNTDATA'] == 0) {
                        $totalSection--;
                      }
                      $sqlAccommodation  = array();
                      $sqlAccommodation['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_MASTER_HOTEL_ . " 
                                         WHERE `status` = ?";
                      $sqlAccommodation['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
                      $resultAccommodation      = $mycms->sql_select($sqlAccommodation);
                      if ($resultAccommodation[0]['COUNTDATA'] == 0) {
                        $totalSection--;
                        $totalSection--;
                      }
                      //echo $resultWorkshop[0]['COUNTDATA'];

                      // echo '<pre>'; print_r($resultIcon);
                      $target_workshop = null;
                      $target_accompany = null;
                      $target_dinner = null;
                      $target_accommodation = null;
                      foreach ($resultIcon as $k => $val) {
                        if ($val['title'] == 'Workshop' && $resultWorkshop[0]['COUNTDATA'] == 0) {
                          $target_workshop = $k;
                        }

                        if ($val['title'] == 'Accompanying' && $resultAccompany[0]['COUNTDATA'] == 0) {
                          $target_accompany = $k;
                        }

                        if ($val['title'] == 'Banquet' && $resultDinner[0]['COUNTDATA'] == 0) {
                          $target_dinner = $k;
                        }

                        if ($val['title'] == 'Accommodation' && $resultAccommodation[0]['COUNTDATA'] == 0) {
                          $target_accommodation = $k;
                        }
                      }

                      if ($target_workshop !== null) {
                        unset($resultIcon[$target_workshop]);
                      }

                      if ($target_accompany !== null) {
                        unset($resultIcon[$target_accompany]);
                      }

                      if ($target_dinner !== null) {
                        unset($resultIcon[$target_dinner]);
                      }

                      if ($target_accommodation !== null) {
                        unset($resultIcon[$target_accommodation]);
                      }

                      // Output the modified array
                      //echo '<pre>'; print_r($resultIcon);


                      if ($resultIcon) {
                        $i = 0;



                        foreach ($resultIcon as $k => $val) {

                          $i++;

                          if ($i == 1) {
                            $activeclass = 'active';
                          } else {
                            $activeclass = '';
                          }


                          if ($val['title'] == 'Abstract') {
                            $url = _BASE_URL_ . $val['page_link'];
                          } else if ($val['title'] == 'Faculty') {
                            $url = $val['page_link'];
                          } else if ($val['title'] == 'Venue') {
                            $url = $val['page_link'];
                          } else {
                            $url = 'javascript:void(0)';
                          }

                          $sql_cal['QUERY']  = "SELECT COUNT(*) COUNTDATA
                                                       FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
                                                      WHERE `icon_id` = " . $val['id'] . " AND status='A'";

                          $res_cal = $mycms->sql_select($sql_cal);
                          $row    = $res_cal[0];

                          //echo $row['COUNTDATA'];
                      ?>

                          <a href="<?= $url ?>" class="main-menu <?= $activeclass ?>" id="item<?= $i; ?>" target="blank">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500">
                              <?php
                              if ($val['icon'] != '') {

                              ?>
                                <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE']; ?><?= $val['icon'] ?>" alt="" />
                              <?php
                              }
                              // if($val['icon'] != '' && $val['title'] != 'Abstract' && $val['title'] != 'Faculty'){
                              //   $totalSection++;
                              // }
                              ?>

                              <p><?= $val['title'] ?></p>
                            </div>
                          </a>



                      <?php

                        }
                      }
                      // echo $totalSection;
                      ?>
                      <input type="hidden" name="banquetIconCount" id="banquetIconCount">
                      <input type="hidden" name="workshopIconCount" id="workshopIconCount">


                    </div>
                    <?php
                    $firstArray = [];
                    $secondArray = [];
                    $j = 0;
                    foreach ($resultIcon as $k => $val) {
                      $j++;
                      if ($j > 9) {
                        $secondArray[] = $val;
                      } else {
                        $firstArray[] = $val;
                      }
                    }

                    // Optionally, print the arrays to check the results
                    //echo '<pre>'; print_r($firstArray);
                    //echo '<pre>'; print_r($secondArray);
                    ?>

                    <div class="slider slider-nav">
                      <div>
                        <?php
                        if (count($firstArray) > 0) {
                        ?>
                          <div class="site-menu">
                            <div class="site-map-inner">
                              <?php
                              $i = 0;
                              foreach ($firstArray as $k => $val) {

                                $i++;

                                if ($i == 1) {
                                  $activeclass = 'active';
                                } else {
                                  $activeclass = '';
                                }

                                if ($val['title'] == 'Abstract') {
                                  $url = _BASE_URL_ . $val['page_link'];
                                } else if ($val['title'] == 'Faculty') {
                                  $url = $val['page_link'];
                                } else if ($val['title'] == 'Venue') {
                                  $url = $val['page_link'];
                                } else {
                                  $url = 'javascript:void(0)';
                                }
                              ?>

                                <a href="<?= $url ?>" class="main-menu <?= $activeclass ?>" id="item<?= $i; ?>">
                                  <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                                    <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE']; ?><?= $val['icon'] ?>" alt="" />

                                    <p><?= $val['title']; ?></p>
                                  </div>
                                </a>
                              <?php
                              }
                              ?>

                            </div>
                          </div>
                        <?php
                        }
                        ?>
                      </div>

                      <div>
                        <?php
                        if (count($secondArray) > 0) {
                        ?>
                          <div class="site-menu">
                            <?php
                            $i = 0;
                            foreach ($secondArray as $k => $val) {

                              $i++;

                              if ($i == 1) {
                                $activeclass = 'active';
                              } else {
                                $activeclass = '';
                              }

                              if ($val['title'] == 'Abstract') {
                                $url = _BASE_URL_ . $val['page_link'];
                              } else if ($val['title'] == 'Faculty') {
                                $url = $val['page_link'];
                              } else if ($val['title'] == 'Venue') {
                                $url = $val['page_link'];
                              } else {
                                $url = 'javascript:void(0)';
                              }
                            ?>

                              <a href="<?= $url ?>" class="main-menu <?= $activeclass ?>" id="item<?= $i; ?>">
                                <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                                  <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE']; ?><?= $val['icon'] ?>" alt="" />

                                  <p><?= $val['title']; ?></p>
                                </div>
                              </a>
                            <?php
                            }
                            ?>


                          </div>
                        <?php
                        }
                        ?>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- <| -->
          </div>
        </div>
        </div>
      </section>


      <?php include_once('footer.php'); ?>
      <?php include_once('cart.php'); ?>



    </main>

  </form>

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
                Please contact the registration secretariat for further details.</span></h2>
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
          <?= $cfg['CART_TITLE'] ?>

          <form name="frmApplyPayment" id="frmApplyPayment" action="registration.process.php" method="post">
            <div class="col-xs-12 form-group" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>

              <div class="checkbox custom-radio-holder">

                <input type="hidden" id="slip_id" name="slip_id" />
                <input type="hidden" id="delegate_id" name="delegate_id" />
                <input type="hidden" name="act" value="paymentSet" />
                <input type="hidden" name="mode" id="mode" />
                <!-- <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                  <img src="<?= _BASE_URL_ ?>images/international_globe.png" height="20px;">
                  International Card
                  <input type="radio" name="card_mode" use="card_mode_select" value="International">
                  <span class="checkmark"></span>
                </label> -->
                <label class="container-box custom-radio" style="float:left; margin-right:30px;">
                  <img src="<?= _BASE_URL_ ?>images/india_globe.png" height="20px;">
                  Indian Card
                  <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
                  <span class="checkmark"></span>
                </label>
                &nbsp;

              </div>

            </div>

            <div class="policy-div">
              <ul>
                <li><a href="<?= _BASE_URL_ ?>cancellation.php">Cancellation Policy</a></li>
                <li><a href="<?= _BASE_URL_ ?>privacy-policy.php">Privacy Policy</a></li>
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
  <script src='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js'></script>
  <script type='text/javascript' src='css/website/wow.min.js?ver=1.0.0' id='wow-animate-js'></script>

  <script type='text/javascript' src='css/website/pxl-main.min.js?ver=1.0.0' id='pxl-main-js'></script>
  <script type='text/javascript' src='css/website/swiper.min.js?ver=5.3.6' id='swiper-js'></script>
  <script type='text/javascript' src='css/website/carousel.js?ver=1.0.0' id='pxl-swiper-js'></script>
  <script type='text/javascript' src='css/website/webpack.runtime.min.js?ver=3.14.1' id='elementor-webpack-runtime-js'></script>
  <script type='text/javascript' src='css/website/frontend-modules.min.js?ver=3.14.1' id='elementor-frontend-modules-js'></script>

  <script type='text/javascript' id='elementor-frontend-js-before'>
    var elementorFrontendConfig = {
      "environmentMode": {
        "edit": false,
        "wpPreview": false,
        "isScriptDebug": false
      },
      "i18n": {
        "shareOnFacebook": "Share on Facebook",
        "shareOnTwitter": "Share on Twitter",
        "pinIt": "Pin it",
        "download": "Download",
        "downloadImage": "Download image",
        "fullscreen": "Fullscreen",
        "zoom": "Zoom",
        "share": "Share",
        "playVideo": "Play Video",
        "previous": "Previous",
        "next": "Next",
        "close": "Close",
        "a11yCarouselWrapperAriaLabel": "Carousel | Horizontal scrolling: Arrow Left & Right",
        "a11yCarouselPrevSlideMessage": "Previous slide",
        "a11yCarouselNextSlideMessage": "Next slide",
        "a11yCarouselFirstSlideMessage": "This is the first slide",
        "a11yCarouselLastSlideMessage": "This is the last slide",
        "a11yCarouselPaginationBulletMessage": "Go to slide"
      },
      "is_rtl": false,
      "breakpoints": {
        "xs": 0,
        "sm": 480,
        "md": 768,
        "lg": 1025,
        "xl": 1440,
        "xxl": 1600
      },
      "responsive": {
        "breakpoints": {
          "mobile": {
            "label": "Mobile Portrait",
            "value": 767,
            "default_value": 767,
            "direction": "max",
            "is_enabled": true
          },
          "mobile_extra": {
            "label": "Mobile Landscape",
            "value": 880,
            "default_value": 880,
            "direction": "max",
            "is_enabled": false
          },
          "tablet": {
            "label": "Tablet Portrait",
            "value": 1024,
            "default_value": 1024,
            "direction": "max",
            "is_enabled": true
          },
          "tablet_extra": {
            "label": "Tablet Landscape",
            "value": 1200,
            "default_value": 1200,
            "direction": "max",
            "is_enabled": true
          },
          "laptop": {
            "label": "Laptop",
            "value": 1366,
            "default_value": 1366,
            "direction": "max",
            "is_enabled": false
          },
          "widescreen": {
            "label": "Widescreen",
            "value": 2400,
            "default_value": 2400,
            "direction": "min",
            "is_enabled": true
          }
        }
      },
      "version": "3.14.1",
      "is_static": false,
      "experimentalFeatures": {
        "e_dom_optimization": true,
        "e_optimized_assets_loading": true,
        "e_optimized_css_loading": true,
        "a11y_improvements": true,
        "additional_custom_breakpoints": true,
        "e_swiper_latest": true,
        "landing-pages": true
      },
      "urls": {
        "assets": "https:\/\/demo.casethemes.net\/savour\/wp-content\/plugins\/elementor\/assets\/"
      },
      "swiperClass": "swiper",
      "settings": {
        "page": [],
        "editorPreferences": []
      },
      "kit": {
        "active_breakpoints": ["viewport_mobile", "viewport_tablet", "viewport_tablet_extra", "viewport_widescreen"],
        "global_image_lightbox": "yes",
        "lightbox_enable_counter": "yes",
        "lightbox_enable_fullscreen": "yes",
        "lightbox_enable_zoom": "yes",
        "lightbox_enable_share": "yes",
        "lightbox_title_src": "title",
        "lightbox_description_src": "description"
      },
      "post": {
        "id": 23,
        "title": "Savour%20%E2%80%93%20Restaurant%20WordPress%20Theme",
        "excerpt": "",
        "featuredImage": false
      }
    };
  </script>

  <script type='text/javascript' src='css/website/frontend.min.js?ver=3.14.1' id='elementor-frontend-js'></script>
  <?php
  $sqlFetchCountdown        = array();
  $sqlFetchCountdown['QUERY']    = "SELECT * 
                                FROM " . _DB_LANDING_PAGE_SETTING_;
  $resultFetchCountdown       = $mycms->sql_select($sqlFetchCountdown);

  $dateArr          = explode("-", $resultFetchCountdown[0]['countdownDate']);
  $date          = $resultFetchCountdown[0]['countdownDate'];
  ?>
  <script>
    // const countdown = new Date(Date.parse(new Date()) + 18 * 24 * 60 * 60 * 1000);
    const countdown = new Date("<?= $endDate ?>");

    const days = document.querySelector(".days").querySelector(".flip-card");
    const hours = document.querySelector(".hours").querySelector(".flip-card");
    const minutes = document.querySelector(".minutes").querySelector(".flip-card");
    const seconds = document.querySelector(".seconds").querySelector(".flip-card");

    // ** get the time totals, return them
    function getTimeRemaining(countdown) {
      const now = new Date();

      const diff = countdown - now;

      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
      const minutes = Math.floor((diff / 1000 / 60) % 60);
      const seconds = Math.floor((diff / 1000) % 60);

      return {
        diff,
        days,
        hours,
        minutes,
        seconds
      };
    }

    function initializeClock(countdown) {
      function updateClock() {
        const t = getTimeRemaining(countdown);
        addFlip(days, t.days);
        addFlip(hours, t.hours);
        addFlip(minutes, t.minutes);
        addFlip(seconds, t.seconds);

        if (t.diff <= 0) {
          clearInterval(timeinterval);
        }
      }

      updateClock();
      const timeinterval = setInterval(updateClock, 1000);
    }

    const addFlip = (card, time) => {
      // ** confirm time has changed
      const currTime = card.querySelector(".top-half").innerText;
      if (time == currTime) return;

      let t = time <= 9 ? `0${time}` : time;
      const topHalf = card.querySelector(".top-half");
      const bottomHalf = card.querySelector(".bottom-half");
      const topFlip = document.createElement("div");
      const bottomFlip = document.createElement("div");

      // ** add animation, populate with current time
      topFlip.classList.add("top-flip");
      topFlip.innerText = currTime;

      bottomFlip.classList.add("bottom-flip");

      // ** animation begins, update top-half to new time
      topFlip.addEventListener("animationstart", () => {
        topHalf.innerText = t;
      });

      // ** animation ends, remove animated div, update bottom animation to new time
      topFlip.addEventListener("animationend", () => {
        topFlip.remove();
        bottomFlip.innerText = t;
      });

      // ** animation ends, update bottom-half to new time, remove animated div
      bottomFlip.addEventListener("animationend", () => {
        bottomHalf.innerText = t;
        bottomFlip.remove();
      });

      card.appendChild(topFlip);
      card.appendChild(bottomFlip);
    };

    initializeClock(countdown);
  </script>

  <!--  -->

  <script>
    $(document).ready(function() {
      var iconCount = $('#iconCount').val();
      //alert(iconCount);

      $('.slider-nav').slick({
        slidesToScroll: 1,
        dots: false,


        infinite: true,


        slidesPerRow: 1,
        slidesToShow: 1,


        arrows: iconCount > 9,
        accessibility: true,
        onAfterChange: function(slide, index) {
          console.log("slider-nav change");
          console.log(this.$slides.get(index));
          $('.current-slide').removeClass('current-slide');
          $(this.$slides.get(index)).addClass('current-slide');
        },
        onInit: function(slick) {
          $(slick.$slides.get(0)).addClass('current-slide');
        },
        responsive: [{
          breakpoint: 991,
          settings: {

          }
        }]
      });
    });

    var emailflag = 0;
    $(document).ready(function() {

      $('.category-select-div').click(function() {
        $('#cart').show();

      })


      var currentSection = 1;
      showSection(currentSection);


      $('.next').click(function() {


        if (validateSection(currentSection)) {
          totalSec = <?= $totalSection ?>;
          if (currentSection == totalSec) {
            showSection(currentSection);
            $('#checkout-main-wrap').show();
            return;
          }

          var workshop_count = $(this).attr('workshop-count');
          var accompany_count = $(this).attr('accompany-count');
          var banquet_count = $(this).attr('banquet-count');
          var accommodation_count = $(this).attr('accommodation-count');

          // alert(accommodation_count);

          $('#pageTitle').text("");
          $('#pageTitle').text($(this).attr('title'));


          $('.main-menu').removeClass('active');
          if (currentSection < $('.section').length) {
            currentSection++;
            console.log('next=' + currentSection);


            if (currentSection == 3) {

              $('#cart').removeClass('disabled-click');
              $('#cart').css('filter', '');

              $('#paymentOptions').css('filter', '');
              $('#pay-button').show();
              $('.payRadioBtn').prop('disabled', false);

            }



            if (workshop_count == 0) {

              // $('#pageTitle').text("");
              currentSection++;
              showSection(Number(currentSection));

            } else if (workshop_count > 0) {
              $('#item' + Number(currentSection - 1)).addClass('active');

              showSection(currentSection);
              $('#workshopIconCount').val(workshop_count);
              return false;
            } else if (accompany_count == 0) {

              // $('#pageTitle').text("");
              currentSection++;
              showSection(Number(currentSection));

            } else if (accompany_count > 0) {
              $('#item' + Number(currentSection - 1)).addClass('active');
              showSection(currentSection);
              return false;
            } else if (banquet_count == 0) {

              // $('#pageTitle').text("");
              if (accommodation_count == 0) {
                $('#checkout-main-wrap').show();
                return false;
              } else {
                currentSection++;

                showSection(Number(currentSection));

              }


            } else if (banquet_count > 0) {
              $('#banquetIconCount').val(banquet_count);
              var banquetIconCount = $('#banquetIconCount').val();
              var workshopIconCount = $('#workshopIconCount').val();
              //alert(currentSection);
              if (banquetIconCount > 0 && workshopIconCount > 0) {
                //alert(currentSection);
                $('#item' + Number(currentSection - 1)).addClass('active');
              } else if (banquetIconCount > 0) {
                //alert(1);
                $('#item' + Number(currentSection - 2)).addClass('active');
              }


              showSection(currentSection);
              return false;
            } else {

              showSection(currentSection);
            }

            //alert(currentSection);



            if (currentSection == 1 || currentSection == 2) {
              $('#item1').addClass('active');



            } else if (currentSection == 6) {


              if (accommodation_count == 0) {
                $('#checkout-main-wrap').show();
                return false;
              }

              $('#loading_indicator').show();


              setTimeout(() => {
                $('#loading_indicator').hide();
                $('.drama-nav .slick-prev').click();

              }, 200)


              if (accommodation_count > 0) {

                var banquetIconCount = $('#banquetIconCount').val();
                var workshopIconCount = $('#workshopIconCount').val();

                if (banquetIconCount > 0 && workshopIconCount > 0) {
                  $('#item' + Number(currentSection - 1)).addClass('active');
                } else if (banquetIconCount > 0) {
                  $('#item' + Number(currentSection - 2)).addClass('active');
                } else if (banquetIconCount == '' && workshopIconCount == '') {
                  $('#item' + Number(currentSection - 3)).addClass('active');
                } else if (banquetIconCount == 0) {
                  $('#item' + Number(currentSection - 2)).addClass('active');
                } else {
                  $('#item' + Number(currentSection - 1)).addClass('active');
                }

              } else {
                $('#item' + Number(currentSection)).addClass('active');
              }

            } else {

              if (workshop_count == 0) {

                $('#item' + Number(currentSection - 2)).addClass('active');
              } else if (accompany_count == 0) {
                //alert(2);
                $('#item' + Number(currentSection - 2)).addClass('active');
              } else if (banquet_count == 0) {

                $('#item' + Number(currentSection - 2)).addClass('active');
              } else {
                //$('#item'+Number(currentSection)).addClass('active');
              }


            }

          }


        } // end validation if 
      });

      $('.skip').click(function() {


        $('#pageTitle').text("");
        $('#pageTitle').text($(this).attr('title'));

        if (currentSection == 3) {
          $('input[type=radio][operationMode=workshopId]').prop('checked', false);
          // Uncheck all radio buttons
          // $('input[type="radio"]').prop('checked', false);
          calculateTotalAmount();
        }
        if (currentSection == 4) {
          $('input[type=checkbox][name=accompanyCount]').prop('checked', false);
          // Uncheck all radio buttons
          // $('input[type="radio"]').prop('checked', false);
          calculateTotalAmount();
        }
        if (currentSection == 5) {
          $('input[type=checkbox][operationMode=dinner]').prop('checked', false);
          // Uncheck all radio buttons
          // $('input[type="radio"]').prop('checked', false);
          calculateTotalAmount();
        }
        // if(currentSection==6){
        // $('input[type=checkbox][operationMode=dinner]').prop('checked', false);
        // // Uncheck all radio buttons
        // // $('input[type="radio"]').prop('checked', false);
        // calculateTotalAmount();
        // }


        if (currentSection == 5) {
          $('.category-right').hide();
        } else {
          $('.category-right').show();
        }

        totalSec = <?= $totalSection ?>;
        if (currentSection == totalSec) {
          showSection(currentSection);
          $('#checkout-main-wrap').show();
          calculateTotalAmount();
          return;
        }

        var workshop_count = $(this).attr('workshop-count');
        var accompany_count = $(this).attr('accompany-count');
        var banquet_count = $(this).attr('banquet-count');
        var accommodation_count = $(this).attr('accommodation-count');

        $('.main-menu').removeClass('active');
        if (currentSection < $('.section').length) {
          currentSection++;
          console.log('next=' + currentSection);
          var user_first_name = $('#user_first_name').val();
          var user_last_name = $('#user_last_name').val();
          var fullname = user_first_name + " " + user_last_name;
          if (currentSection == 5) {
            $('#dinner_name').text(fullname);
          }

          if (workshop_count == 0) {

            $('#pageTitle').text("");
            currentSection++;
            showSection(Number(currentSection));

          } else if (workshop_count > 0) {
            $('#item' + Number(currentSection - 1)).addClass('active');
            showSection(currentSection);
            $('#workshopIconCount').val(workshop_count);
            return false;
          } else if (accompany_count == 0) {

            $('#pageTitle').text("");
            currentSection++;
            showSection(Number(currentSection));

          } else if (accompany_count > 0) {
            $('#item' + Number(currentSection - 1)).addClass('active');
            showSection(currentSection);
            return false;
          } else if (banquet_count == 0) {
            if (accommodation_count == 0) {
              $('#checkout-main-wrap').show();
              return false;
            } else {
              $('#pageTitle').text("");
              currentSection++;

              showSection(Number(currentSection));
            }



          } else if (banquet_count > 0) {
            $('#banquetIconCount').val(banquet_count);
            var banquetIconCount = $('#banquetIconCount').val();
            var workshopIconCount = $('#workshopIconCount').val();
            //alert(currentSection);
            if (banquetIconCount > 0 && workshopIconCount > 0) {
              //alert(currentSection);
              $('#item' + Number(currentSection - 1)).addClass('active');
            } else if (banquetIconCount > 0) {
              //alert(1);
              $('#item' + Number(currentSection - 2)).addClass('active');
            }


            showSection(currentSection);
            return false;
          } else {

            showSection(currentSection);
          }

          //alert(currentSection);



          if (currentSection == 1 || currentSection == 2) {
            $('#item1').addClass('active');


          } else if (currentSection == 6) {
            //alert(111);
            $('.drama-nav .slick-prev').click();

            if (accommodation_count == 0) {
              $('#checkout-main-wrap').show();
              return false;
            }

            $('#loading_indicator').show();


            setTimeout(() => {
              $('#loading_indicator').hide();
              $('.drama-nav .slick-prev').click();

            }, 100)


            if (accommodation_count > 0) {

              var banquetIconCount = $('#banquetIconCount').val();
              var workshopIconCount = $('#workshopIconCount').val();

              if (banquetIconCount > 0 && workshopIconCount > 0) {
                $('#item' + Number(currentSection - 1)).addClass('active');
              } else if (banquetIconCount > 0) {
                $('#item' + Number(currentSection - 2)).addClass('active');
              } else if (banquetIconCount == '' && workshopIconCount == '') {
                $('#item' + Number(currentSection - 3)).addClass('active');
              } else if (banquetIconCount == 0) {
                $('#item' + Number(currentSection - 2)).addClass('active');
              } else {
                $('#item' + Number(currentSection - 1)).addClass('active');
              }

            } else {
              $('#item' + Number(currentSection)).addClass('active');
            }

          } else {

            if (workshop_count == 0) {

              $('#item' + Number(currentSection - 2)).addClass('active');
            } else if (accompany_count == 0) {
              //alert(2);
              $('#item' + Number(currentSection - 2)).addClass('active');
            } else if (banquet_count == 0) {

              $('#item' + Number(currentSection - 2)).addClass('active');
            } else {
              //$('#item'+Number(currentSection)).addClass('active');
            }


          }

        }

        $('.drama-nav .slick-prev').click();

      });



      //$('.prev').click(function() {
      $(document).on("click", ".prev", function() {

        $('#pageTitle').text("");
        $('#pageTitle').text($(this).attr('title'));

        var workshop_count = $(this).attr('workshop-count');
        var accompany_count = $(this).attr('accompany-count');
        var banquet_count = $(this).attr('banquet-count');
        var accommodation_count = $(this).attr('accommodation-count');
        var workshopIconCount = $('#workshopIconCount').val();

        var sec = $(this).attr('sec');
        console.log('sec=' + sec);
        // alert(currentSection)
        if (currentSection > 1) {



          if (sec != undefined && sec != '') {
            showSection(sec);
          } else {
            currentSection--;
            if (workshop_count == 0) {

              // $('#pageTitle').text("");
              currentSection--;
              showSection(Number(currentSection));

            } else if (accompany_count == 0) {
              // $('#pageTitle').text("");
              currentSection--;
              showSection(Number(currentSection));

            } else if (banquet_count == 0) {

              // $('#pageTitle').text("");
              currentSection--;
              showSection(Number(currentSection));

            } else {

              console.log('else=' + currentSection);
              showSection(currentSection);
            }
          }


          //alert(currentSection)

          $('.main-menu').removeClass('active');
          if (currentSection == 1 || currentSection == 2) {
            $('#item1').addClass('active');
          } else if (workshopIconCount > 0) {
            $('#item' + Number(currentSection - 1)).addClass('active');
          } else if (banquet_count > 0 && workshopIconCount == '') {
            //alert(1)
            $('#item' + Number(currentSection - 2)).addClass('active');
          } else if (banquet_count == 0) {
            $('#item' + Number(currentSection - 2)).addClass('active');
          } else if (accompany_count > 0) {
            $('#item' + Number(currentSection - 2)).addClass('active');
          } else {
            $('#item' + Number(currentSection - 2)).addClass('active');
          }
        }

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

      function validateSection(section) {
        //alert(section); 
        var isValid = true;
        var accomArr = [];
        var galaDinnerDiv = " ";
        var hasExecuted = false;
        var isGalaFlag = false

        $("#section" + section + " input[type='text'], #section" + section + " input[type='radio'], #section" + section + " input[type='checkbox'], #section" + section + " select").each(function(index) {

          //alert($(this).attr('type'));

          if ($(this).attr('type') === 'text' && !$.trim($(this).val())) {

            if (section == 2 && $(this).attr('id') != 'user_middle_name') {

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
              $('.category-right').show();
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
              $('.category-right').show();
              // Check if at least one radio button is checked
              if (!$("input[name='" + $(this).attr('name') + "']:checked").length) {
                var msg = $(this).attr('validate');
                //console.log("Please select a value for radio button " + $(this).attr('name'));
                toastr.error(msg, 'Error', {
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
              $('.category-right').show();
              // Check if at least one radio button is checked
              if (!$("input[name='" + $(this).attr('name') + "']:checked").length) {

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
              $('.category-right').show();

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

                if (!$("input[name='" + $(this).attr('name') + "']:checked").length) {

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

        });

        //$('.gala-dinner-select').append(galaDinnerDiv); 
        if (isGalaFlag) {
          $('#gala-dinner-select1').empty().append(galaDinnerDiv);
        }


        return isValid;


      } //end if validation

      function showSection(section) {
        if (section == 1) {
          $('.prev').addClass('disabled-click');
        } else {
          $('.prev').removeClass('disabled-click');
        }
        console.log('section=' + section);
        $('.section').removeClass('active');
        $('#section' + section).addClass('active');
        if (section == 6) {
          $('#header').hide();
        }
        if (section < 6) {
          $('#header').show();
        }
      }

      var storageEmail = localStorage.getItem("user_email_id");
      // var storageMobile = localStorage.getItem("user_mobile");
      if (storageEmail != '' && storageEmail !== undefined) {
        $('#user_email_id').val(storageEmail);
        // checkUserEmail(document.querySelector('.pay-button'));
      }


    });

    function checkUserEmail(obj) {

      var liParent = $(obj).parent().closest("div[use=registrationUserDetails]");
      // var emailIdObj = $(liParent).find("#user_email_id");
      // var emailId = $.trim($(emailIdObj).val());
      var emailId = $.trim($("#user_email_id").val());
      // alert(emailId);

      //var emailId = $.trim($(obj).val());
      // alert(emailId);

      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (emailRegex.test(emailId)) {} else {
        toastr.error("Please enter valid email address", 'Error', {
          "progressBar": true,
          "timeOut": 3000,
          "showMethod": "slideDown",
          "hideMethod": "slideUp"
        });
        return false;
      }

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
                  emailflag = 1;
                  //Mobile validation 
                  var mobile = $("#user_mobile").val();
                  if (mobile == '') {
                    toastr.error("Please enter your mobile number.", 'Error', {
                      "progressBar": true,
                      "timeOut": 2000,
                      "showMethod": "slideDown",
                      "hideMethod": "slideUp"
                    });
                    return false;
                  }
                  if (mobile != '') {
                    if (mobile.length < 10) {
                      toastr.error("Please enter a valid mobile number.", 'Error', {
                        "progressBar": true,
                        "timeOut": 2000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                      })
                      $('#user_details').addClass('disabled-click'); //
                    } else {
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
                            $('#user_mobile').val("");

                          } else {
                            // $('#user_details').show();
                            $('#user_details').removeClass('disabled-click');

                            // console.log('>>' + $(parent).find(
                            //   "div[use=mobileProcessing]").find(
                            //   "input[name=user_mobile_validated]").val());

                            // if(emailflag==1){

                            enableAllFileds(liParent);
                            $('#radioGender').removeClass('blur_bw');
                            $('#radioFood').removeClass('blur_bw');

                            $('#user_email_id').addClass('disabled-user-input');
                            $('#user_mobile').addClass('disabled-user-input');



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

                            // }
                          }
                        }
                      });
                    }
                  }


                }
              }
            });
          }, 500);
        } else {
          var invalidEmail = $("#invalidEmail").val();
          toastr.error('Enter Valid Emailll Id', 'Error', {
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
          "chkStatus", false);

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
          $(this).attr("chkStatus", false);

          $("div[operationMode=chhoseServiceOptions][use=residentialOperations]")
            .hide();
          $("div[operationMode=chhoseServiceOptions][use=defaultChoices]")
            .slideDown();
          // window.location.reload();
        } else {
          $(this).prop("checked", true);
          $(this).attr("chkStatus", true);

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

    $(document).on("click change", "input[type=checkbox], input[type=radio]", function() {
      calculateTotalAmount();

    });


    $(document).on("change", "#accommodation_room", function() {

      calculateTotalAmount();
    })

    // $(document).on("click", "#deleteItem", function() {

    //   var reg = $(this).attr('reg');
    //   var val = $(this).attr('val');
    //   var regClsId = $(this).attr('regClsId');

    //   if (reg === 'workshop') {
    //     var workshop = 'workshop_id_' + val + '_' + regClsId;

    //     $('#' + workshop).prop('checked', false);
    //     calculateTotalAmount();
    //   }
    //   if (reg === 'accompany') {
    //     $('#accompanyCount').prop('checked', false);
    //     $('.form-control accompany_name').val("");
    //     calculateTotalAmount();
    //   }
    //   $(this).closest('tr').remove();

    // });

    // function calculateTotalAmount_x() {
    //   console.log("====calculateTotalAmount====");

    //   var totalAmount = 0;
    //   var totalDinnerAmount = 0;
    //   var totTable = $("table[use=totalAmountTable]");
    //   $(totTable).children("tbody").find("tr").remove();
    //   var gst_flag = $('#gst_flag').val();
    //   var dinnerFlag = false;

    //   $('input[type=checkbox]:checked,input[type=radio]:checked,#accomodation_package_checkout_id option,#accommodation_room option').each(function() {

    //     var attr = $(this).attr('amount');
    //     var operation = $(this).attr('operationmodetype');
    //     var regtype = $(this).attr('regtype');
    //     var reg = $(this).attr('reg');
    //     var qty = $(this).attr('qty');
    //     console.log('Qty=' + qty);
    //     var hasTotalAmntFlag = false;

    //     //alert(reg);

    //     var package = $(this).attr('package');

    //     //alert(11)

    //     if (typeof attr !== typeof undefined && attr !== false) {
    //       var amt = parseFloat(attr);


    //       if (typeof package !== typeof undefined && package !== false) {



    //         // alert(checkedValue);

    //         /*var checkInVal = $("input[name='accomodation_package_checkin_id']:checked").val();
    //         var checkOutVal = $("input[name='accomodation_package_checkout_id']:checked").val();*/

    //         var checkInVal = $('#accomodation_package_checkin_id').val();
    //         var checkOutVal = $('#accomodation_package_checkout_id').val();

    //         console.log('checkInVal====', checkInVal)
    //         // alert(checkInVal);


    //         if (checkInVal !== undefined && checkOutVal !== undefined) {
    //           const checkInArray = checkInVal.split("/");
    //           var checkInID = checkInArray[0];
    //           var checkInDate = checkInArray[1];

    //           //alert('checkindate',checkInDate);

    //           const checkOutArray = checkOutVal.split("/");
    //           var checkOutID = checkOutArray[0];
    //           var checkOutDate = checkOutArray[1];


    //           var date1 = new Date(checkInDate);
    //           var date2 = new Date(checkOutDate);

    //           // Calculate the difference in milliseconds
    //           var differenceMs = Math.abs(date2 - date1);

    //           var accommodation_room = $('#accommodation_room').val();
    //           if (typeof accommodation_room !== typeof undefined && accommodation_room !== false && !isNaN(accommodation_room)) {
    //             var roomQty = accommodation_room;
    //           } else {
    //             var roomQty = 1;
    //           }

    //           console.log('room qty=' + roomQty);

    //           var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));


    //           console.log('accoAmnt=' + differenceDays);
    //           var amt = parseFloat(amt) * parseInt(differenceDays) * parseInt(roomQty);

    //           if (isNaN(amt)) {
    //             amt = 0;
    //           }

    //           hasTotalAmntFlag = true;
    //         }



    //       }
    //       if (regtype !== 'combo') {

    //         if (gst_flag == 1) {
    //           if (isNaN(amt)) {

    //           } else {
    //             var cgstP = <?= $cfg['INT.CGST'] ?>;
    //             var cgstAmnt = (amt * cgstP) / 100;

    //             var sgstP = <?= $cfg['INT.SGST'] ?>;
    //             var sgstAmnt = (amt * sgstP) / 100;

    //             var totalGst = cgstAmnt + sgstAmnt;
    //             var totalGstAmount = cgstAmnt + sgstAmnt + amt;
    //             totalAmount = totalAmount + totalGstAmount;
    //           }

    //         } else {
    //           if (isNaN(amt)) {

    //           } else {
    //             totalAmount = totalAmount + amt;
    //           }


    //         }

    //         console.log('reg===' + reg);

    //         if (reg != undefined && reg == 'reg') {
    //           if (isNaN(amt)) {
    //             $('#confPrc').text(0.00.toFixed(2));
    //           } else {
    //             $('#confPrc').text((amt).toFixed(2));
    //           }

    //         }

    //         //alert(reg);

    //         if (reg != undefined && reg == 'workshop') {
    //           if (isNaN(amt)) {
    //             $('#workshopPrc').text(0.00.toFixed(2));
    //           } else {
    //             $('#workshopPrc').text((amt).toFixed(2));
    //           }

    //           if (Number(amt) > 0) {
    //             $('#wrkshopPrcdiv').show();
    //           }

    //         } else {
    //           $('#wrkshopPrcdiv').hide();

    //         }

    //         if (reg != undefined && reg == 'accompany') {
    //           if (isNaN(amt)) {
    //             $('#accompanyPrc').text(0.00.toFixed(2));
    //           } else {
    //             $('#accompanyPrc').text((amt).toFixed(2));
    //           }

    //           $('.accompanyPrcdiv').show();

    //         } else {
    //           $('#accompanyPrcdiv').hide();

    //         }

    //         if (reg != undefined && reg == 'dinner' && qty != undefined) {

    //           var checkedCount = $('.checkboxClassDinner:checked').length;
    //           console.log("Number of checked checkboxes: " + checkedCount);

    //           var totalDinnerAmounts = checkedCount * amt;

    //           $('#dinnerPrc').text((totalDinnerAmounts).toFixed(2));
    //           $('.dinnerPrcdiv').show();

    //         } else {
    //           $('#dinnerPrcdiv').hide();

    //         }

    //       }


    //       console.log(">>amt" + amt + ' ==> ' + totalAmount);

    //       var attrReg = $(this).attr('operationMode');
    //       var isConf = false;
    //       if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
    //         'registration_tariff') {
    //         isConf = true;
    //       }
    //       var isMastCls = false;
    //       if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
    //         'workshopId') {
    //         isMastCls = true;
    //       }

    //       // november22 workshop related work by weavers start

    //       var isNovWorkshop = false;
    //       if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg ===
    //         'workshopId_nov') {
    //         isNovWorkshop = true;
    //       }

    //       // november22 workshop related work by weavers end

    //       var cloneIt = false;
    //       var amtAlterTxt = 'Complimentary';

    //       if (amt > 0) {
    //         cloneIt = true;
    //       } else if (isConf) {
    //         cloneIt = true;
    //         amtAlterTxt = 'Complimentary'
    //       } else if (isMastCls || isNovWorkshop) {
    //         cloneIt = true;
    //         amtAlterTxt = 'Included in Registration'
    //       }

    //       if (cloneIt) {
    //         //alert($(this).attr('invoiceTitle'));
    //         var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
    //           .clone();
    //         $(cloned).attr("use", "rowCloned");
    //         var imageElement = $('<img>').attr('src', "<?= _BASE_URL_ ?>" + $(this).attr('icon'));
    //         //alert("<?= _BASE_URL_ ?>"+$(this).attr('icon'));
    //         $(cloned).find("span[use=icon]").append(imageElement);
    //         $(cloned).find("span[use=invTitle]").append($(this).attr('invoiceTitle'));
    //         if (regtype === 'combo') {

    //           $(cloned).find("span[use=amount]").text((amt > 0) ? ('Included') : amtAlterTxt);
    //         } else {

    //           $(cloned).find("span[use=amount]").text((amt > 0) ? (amt).toFixed(2) : amtAlterTxt);
    //         }

    //         if (reg != 'reg') {

    //           var deleteLink = $('<a></a>')
    //             .attr('href', 'javascript:void(0)')
    //             .attr('id', 'deleteItem')
    //             .attr('class', 'delete-accompany-btn')
    //             .attr('reg', reg)
    //             .attr('val', $(this).attr('value'))
    //             .attr('regClsId', $(this).attr('registrationclassfid'))
    //             .text('delete')
    //             .css('border-radius', '20px')
    //             .css('border', '1px solid red');

    //           $(cloned).find("span[use=deleteIcon]").append(deleteLink);
    //         }


    //         $(cloned).show();
    //         $(totTable).children("tbody").append(cloned);
    //       }
    //       if (regtype !== 'combo') {
    //         if (gst_flag == 1) {
    //           if (cloneIt) {
    //             var cgstP = <?= $cfg['INT.CGST'] ?>;
    //             var cgstAmnt = (amt * cgstP) / 100;

    //             var sgstP = <?= $cfg['INT.SGST'] ?>;
    //             var sgstAmnt = (amt * sgstP) / 100;

    //             var totalGst = cgstAmnt + sgstAmnt;
    //             var totalGstAmount = cgstAmnt + sgstAmnt + amt;


    //             var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
    //               .clone();
    //             $(cloned).attr("use", "rowCloned");
    //             $(cloned).find("span[use=invTitle]").text("GST 18%");
    //             $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
    //             $(cloned).show();
    //             $(totTable).children("tbody").append(cloned);
    //           }
    //         }
    //       }
    //     }

    //     if ($(this).attr('operationMode') == 'registrationMode' && $(this).attr('use') ==
    //       'tariffPaymentMode') {

    //       if ($(this).val() == 'ONLINE') {
    //         var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
    //         var internetAmount = (totalAmount * internetHandling) / 100;
    //         totalAmount = totalAmount + internetAmount;

    //         console.log(">>amt" + internetAmount + ' ==> ' + totalAmount);



    //         var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
    //           .clone();

    //         $(cloned).attr("use", "rowCloned");
    //         $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
    //         $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
    //         $(cloned).show();
    //         $(totTable).children("tbody").append(cloned);
    //       }
    //     }
    //   });

    //   totalAmount = Math.round(totalAmount, 0);
    //   totalDinnerAmount = Math.round(totalDinnerAmount, 0);




    //   $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
    //   $("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
    //   $("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount', totalAmount);
    //   $("div[use=totalAmount]").show();

    //   $('#subTotalPrc').text((totalAmount).toFixed(2));

    // }


    $(document).ready(function() {
      // Counter to keep track of the number of accompanies
      var accompanyCount = $('#accompanyCounts').val();


      /*function addAccompany() {


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

        const clonedIntro = $("#cloneIntro").clone();

        console.log('fieldSerializeCount',fieldSerializeCount);
        $("#accompany-container").append(clonedIntro);

        $("#accompany-container").append(newAccompany);

        newAccompany.append('<button class="delete-accompany-btn">Delete</button>');
        calculateTotalAmount();
      }
*/

      function addAccompany() {
        var accompanyCount = $('#accompanyCounts').val();
        var accompanyAmount = $('#accompanyAmount').val();

        var incrementedCount = Number(accompanyCount) + 1;
        $('#accompanyCounts').val(incrementedCount);
        accompanyCount = $('#accompanyCounts').val();

        var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(incrementedCount);
        $("#accompanyCount").attr("amount", amountIncludedDay);
        $("#accompanyCount").val(incrementedCount);

        var newAccompany = $(".add-accompany:first").clone();
        newAccompany.find("span#accomCount").text(accompanyCount);
        newAccompany.find("input[type='text']").val(""); // Clear the input field
        newAccompany.find("input[type='radio']").prop("checked", false);

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

        const clonedIntro = $("#cloneIntro").clone();
        clonedIntro.attr("id", "cloneIntro_" + fieldSerializeCount);

        clonedIntro.find("#accompanyCount").attr("id", "accompanyCount_" + fieldSerializeCount).attr("name", "accompanyCount_" + fieldSerializeCount);
        clonedIntro.find("#accomCount").attr("id", "accomCount_" + fieldSerializeCount).text(accompanyCount);
        clonedIntro.find("label").attr("for", "accompanyCount_" + fieldSerializeCount);

        // Remove the checkbox from the clonedIntro
        clonedIntro.find("input[type='checkbox']").remove();
        //alert(fieldSerializeCount);
        $("#cloneIntro_" + fieldSerializeCount + " label:before").css('display', 'none');

        var wrapper = $("<div class='accompany-wrapper'></div>");
        wrapper.append(clonedIntro);
        wrapper.append(newAccompany);
        newAccompany.append('<button class="delete-accompany-btn" id="delete_' + fieldSerializeCount + '" sl_no="' + fieldSerializeCount + '">Delete</button>');

        $("#accompany-container").append(wrapper);
        $("#cloneIntro_" + fieldSerializeCount).removeClass('custom-checkbox');

        calculateTotalAmount();
      }

      $("#add-accompany-btn").on("click", function(e) {
        e.preventDefault();
        addAccompany();
      });

      // Event handler for dynamically added "Delete" buttons
      /*$("#accompany-container").on("click", ".delete-accompany-btn", function(e) {
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

      });*/

      $("#accompany-container").on("click", ".delete-accompany-btn", function(e) {
        e.preventDefault();
        var current_no = $(this).attr('sl_no');
        var total = $('#accompanyCount').val();
        current_no = Number(current_no);
        total = Number(total);
        // alert(current_no);
        var i;
        for (i = current_no + 1; i <= total; i++) {
          // alert(total);accomCount_2
          $("#cloneIntro_" + i).attr('id', "cloneIntro_" + (i - 1));
          $("#accomCount_" + i).attr('id', "accomCount_" + (i - 1)).text(i);
          $("#delete_" + i).attr('sl_no', (i - 1));
          $("#delete_" + i).attr('id', 'delete_' + (i - 1));

        }
        $(this).closest('.accompany-wrapper').remove();

        var accompanyCount = $('#accompanyCounts').val();
        var newCount = Number(accompanyCount) - 1;

        $('#accompanyCounts').val(newCount);
        $("#accompanyCount").val(newCount);

        var accompanyAmount = $('#accompanyAmount').val();
        var amountIncludedDay = parseFloat(accompanyAmount) * newCount;

        $("#accompanyCount").attr("amount", amountIncludedDay);

        calculateTotalAmount();
      });




    });

    function getAccommodationDetails(hotel_id) {

      if (hotel_id > 0) {
        //alert(jsBASE_URL);
        var abstractDelegateId = $('#abstractDelegateId').val()
        $.ajax({
          type: "POST",
          url: jsBASE_URL + 'returnData.process.php',
          data: 'act=getAccommodationDetails&hotel_id=' + hotel_id + '&abstractDelegateId=' + abstractDelegateId,
          async: false,
          dataType: 'html',
          success: function(JSONObject) {
            $('#section7').html(JSONObject);
            $('.section').removeClass('active');
            $('#section7').addClass('active');
            $('#header').hide();
          }
        });
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
        toastr.error('Please select proper checkout date!', 'Error', {
          "progressBar": true,
          "timeOut": 4000, // 3 seconds
          "showMethod": "slideDown", // Animation method for showing
          "hideMethod": "slideUp",
          "direction": 'ltr', // Animation method for hiding
        });
        // alert("");
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
          calculateTotalAmount();

        }
      });


    }

    /*function get_checkin_val(val)
    {
       if(typeof val !== 'undefined' && val!='')
       {
         
          $("input[name='accomodation_package_checkout_id']").prop('checked', false);
       }
    }*/

    function get_checkin_val(val) {
      if (typeof val !== 'undefined' && val != '') {
        var checkOutVal = $('#accomodation_package_checkout_id').val("");
      }
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

    $("input[type=radio][use=payment_mode_select]").click(function() {
      var val = $(this).val();

      $("div[use=offlinePaymentOption]").hide();
      if (val != undefined) {
        $("div[use=offlinePaymentOption][for=" + val + "]").show();
        if (val === 'Card') {
          $('#registrationMode').val('ONLINE');
          $('#paymentDetailsSection').hide();
        } else {
          $('#registrationMode').val('OFFLINE');
          $('#paymentDetailsSection').show();
        }
      }

    });

    // $("#pay-button").click(function() {

    //   var selectedOption = $("input[type=radio][name='payment_mode']:checked").val();
    //   var flag = 0;

    //   if (selectedOption) {

    //     $("div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='text'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='date'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='radio'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='number']").each(function() {


    //       if ($(this).attr('type') === 'radio') {

    //         if (!$("input[type='radio'][name='card_mode']:checked").length) {

    //           toastr.error('Please select the card', 'Error', {
    //             "progressBar": true,
    //             "timeOut": 5000,
    //             "showMethod": "slideDown",
    //             "hideMethod": "slideUp"
    //           });


    //           flag = 1;
    //           return false;
    //         }
    //       } else {

    //         var textBoxValue = $(this).val();
    //         if (textBoxValue === '') {
    //           toastr.error($(this).attr('validate'), 'Error', {
    //             "progressBar": true,
    //             "timeOut": 5000,
    //             "showMethod": "slideDown",
    //             "hideMethod": "slideUp"
    //           });


    //           flag = 1;
    //           return false;


    //         }
    //       }


    //     });
    //   } else {
    //     //alert("No option selected!");
    //     toastr.error('Please select payment mode', 'Error', {
    //       "progressBar": true,
    //       "timeOut": 5000,
    //       "showMethod": "slideDown",
    //       "hideMethod": "slideUp"
    //     });

    //     flag = 1;
    //   }

    //   if (flag == 0) {
    //     //alert(1212);

    //     $("form[name='registrationForm']").submit();
    //   }

    // });
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  </script>



  <!-- <script src='https://ruedakolkata.com/wboacon2025/js/website/accm-slider-style.js'></script> -->
</body>

</html>