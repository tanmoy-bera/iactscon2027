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

<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();


include_once("header.php");

?>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css'>
<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
<link rel='stylesheet' id='elementor-frontend-css' href='css/website/accm-slider-style.css' type='text/css' media='all' />
<style type="text/css">
  .section {
    display: none;
  }

  .active {
    display: block;
  }




  /*#toast-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}*/

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
</style>

<body class="single inner-page">
  <div id="loading_indicator" style="display:none;"> </div>
  <?
  $cutoffs      = fullCutoffArray();
  $currentCutoffId  = getTariffCutoffId();
  $dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);

  $disabled = count($userRec) > 0 ? "" : "disabled='disabled'";

  $disabledclass = count($userRec) > 0 ? "" : "disable";

  $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);

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
  <form class="body-frm" name="registrationForm" action="<?= _BASE_URL_ ?>registration.process.php">
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
                <div class="smobilexx drama-total-holder" id="section7">
                  <div class="vanue-image aos-init aos-animate"><img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTEL_0011_240508123809.png" alt=""></div>
                  <div class="vanue-form">
                    <div class="pxl-item--content">
                      <div class="pxl-item--popular pxl-flex " >
                        <span class="accm-rating">
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                          <i class="fas fa-star stared"></i>
                        </span>
                      </div>
                      <h3 class="pxl-item--title">Hyatt Regency</h3>
                      <div class="pxl-item--desc"><i class="far fa-map-marker-alt"></i> Salt Lake</div>
                      <div class="pxl-item--desc"><i class="fal fa-utensils"></i> Breakfast Included</div>
                    </div>
                    <div class="vanue-date">
                      <select operationmode="accomodation_package_checkin_id" name="accomodation_package_checkin_id" id="accomodation_package_checkin_id" style="color:#FFFFFF !important; height: 38px !important;" onchange="get_checkin_val(this.value)">
                        <option value="">Check In Date</option>
                        <option value="30/2024-06-25">2024-06-25</option>
                      </select>
                      <select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_id" id="accomodation_package_checkout_id" style="color:#ffffff !important; height: 38px !important;margin-inline: 3px;" onchange="get_checkout_val(this.value)" accommodation="checkout">
                        <option value="">Check Out Date</option>
                        <option value="30/2024-06-26">2024-06-26</option>
                      </select>
                      <select name="accommodation_room" id="accommodation_room">
                        <option value="" selected="">Room</option>
                        <option value="1" selected="">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                      </select>
                    </div>
                    <div class="col-lg-8 custom-radio-holder vf-form aos-init aos-animate" data-aos="fade-right" data-aos-duration="500"><label class="custom-radio">
                        <input type="radio" name="package_id" id="package_id" value="14" checked="" currency="INR" amount="5000.00" onchange="getPackageVal(this.value)" invoicetitle="Residential package Single@ITC ROYAL" package="accomodation" icon="uploads/EMAIL.HEADER.FOOTER.IMAGE/">
                        <i><b>Single</b>: INR 5000.00</i><span class="checkmark"></span>
                      </label><label class="custom-radio">
                        <input type="radio" name="package_id" id="package_id" value="15" currency="INR" amount="6000.00" onchange="getPackageVal(this.value)" invoicetitle="Residential package Double@ITC ROYAL" package="accomodation" icon="uploads/EMAIL.HEADER.FOOTER.IMAGE/">
                        <i><b>Double</b>: INR 6000.00</i><span class="checkmark"></span>
                      </label>
                    </div>
                  </div>
                  <div class="bottom-fx">
                    <div class="total-price">
                      <h6>TOTAL :</h6>
                      <strong>&#8377; <span id="confPrc">27000.00</span></strong>
                    </div>
                    <div class="next-prev-btn-box">
                      <a href="https://ruedakolkata.com/newdesign2023/profile-add.php?section=6" class="btn next-btn prev">Prev</a>
                      <a class="btn next-btn next" formpay="frmAddAccommodationfromProfile">Next</a>
                    </div>
                  </div>
                 
                  

               
                </div>
              </div>
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
                    <a href="#"><img src="<?= $header_image ?>" style="height: 120px;width:120px" alt="" /></a>
                  </div>
                </div>
                <div class="minhgt">
                  <div class="site-menu-holder" id="site-menu-holder">
                    <a onclick="myFunction()" class="clicknow"><img src="<?= _BASE_URL_ ?>images/toggle-nav-up.png"></a>
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

                          $url = (($val['title'] == 'Abstract') ? _BASE_URL_ . $val['page_link'] : 'javascript:void(0)');
                          $url = (($val['title'] == 'Faculty') ?  $val['page_link'] : 'javascript:void(0)');
                          $url = (($val['title'] == 'Venue') ?  $val['page_link'] : 'javascript:void(0)');


                          $sql_cal['QUERY']  = "SELECT COUNT(*) COUNTDATA
                                                       FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
                                                      WHERE `icon_id` = " . $val['id'] . " AND status='A'";

                          $res_cal = $mycms->sql_select($sql_cal);
                          $row    = $res_cal[0];

                          //echo $row['COUNTDATA'];
                      ?>

                          <a href="<?= $url ?>" class="main-menu <?= $activeclass ?>" id="item<?= $i; ?>">
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
                    <div class="slider slider-nav">
                      <div>
                        <div class="site-menu">
                          <a href="javascript:void(0)" class="main-menu active" id="item1">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0001_240401155025.png" alt="">

                              <p>Registration</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item2">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0002_240401155352.png" alt="">

                              <p>Workshop</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item3">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0003_240401155613.png" alt="">

                              <p>Accompanying</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item4">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0006_240401155822.png" alt="">

                              <p>Accommodation</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item5">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0007_231205152407.png" alt="">

                              <p>Faculty</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item6">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0008_231205152448.png" alt="">

                              <p>Highlights</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item7">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0009_231205152522.png" alt="">

                              <p>Venue</p>
                            </div>
                          </a>
                          <a href="https://ruedakolkata.com/newdesign2023/abstract.php" class="main-menu " id="item8">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0011_240401162006.png" alt="">

                              <p>Abstract</p>
                            </div>
                          </a>
                          <input type="hidden" name="banquetIconCount" id="banquetIconCount">
                          <input type="hidden" name="workshopIconCount" id="workshopIconCount">
                        </div>
                      </div>
                      <div>
                        <div class="site-menu">
                          <a href="javascript:void(0)" class="main-menu active" id="item1">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0001_240401155025.png" alt="">

                              <p>Registration</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item2">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0002_240401155352.png" alt="">

                              <p>Workshop</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item3">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0003_240401155613.png" alt="">

                              <p>Accompanying</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item4">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0006_240401155822.png" alt="">

                              <p>Accommodation</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item5">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0007_231205152407.png" alt="">

                              <p>Faculty</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item6">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0008_231205152448.png" alt="">

                              <p>Highlights</p>
                            </div>
                          </a>
                          <a href="javascript:void(0)" class="main-menu " id="item7">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0009_231205152522.png" alt="">

                              <p>Venue</p>
                            </div>
                          </a>
                          <a href="https://ruedakolkata.com/newdesign2023/abstract.php" class="main-menu " id="item8">
                            <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500" class="aos-init aos-animate">
                              <img src="https://ruedakolkata.com/newdesign2023/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0011_240401162006.png" alt="">

                              <p>Abstract</p>
                            </div>
                          </a>
                          <input type="hidden" name="banquetIconCount" id="banquetIconCount">
                          <input type="hidden" name="workshopIconCount" id="workshopIconCount">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>


      <?php include_once('footer.php'); ?>

      <div class="checkout-main-wrap" id="checkout-main-wrap">
        <div class="checkout-popup">
          <div class="card-details" id="paymentOptions" style="filter: blur(1.6px);width: 40%;">
            <div class="card-details-inner">
              <?= $cfg['CART_TITLE'] ?>

              <div class="redio-select custom-radio-holder" style="display: flex;">

                <input type="hidden" name="registrationMode" id="registrationMode">
                <label class="custom-radio">
                  <input type="radio" class="payRadioBtn" name="payment_mode" use="payment_mode_select" value="Card" for="Card" paymentMode='ONLINE' disabled>
                  Card<span class="checkmark"></span>
                </label>
                <?php
                $offline_payments = json_decode($cfg['PAYMENT.METHOD']);
                if (in_array("Cheque/DD", $offline_payments)) {
                ?>
                  <label class="custom-radio">
                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                    Chq/DD<span class="checkmark"></span>
                  </label>
                <?php
                }
                if (in_array("Draft", $offline_payments)) {
                ?>
                  <!-- <label class="custom-radio">
                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                    Draft<span class="checkmark"></span>
                  </label> -->
                <?php
                }
                if (in_array("Neft", $offline_payments)) {
                ?>
                  <label class="custom-radio">
                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Neft" for="Neft" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                    NEFT<span class="checkmark"></span>
                  </label>
                <?php
                }
                if (in_array("Rtgs", $offline_payments)) {
                ?>
                  <!-- <label class="custom-radio">
                    <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                    RTGS<span class="checkmark"></span>
                  </label> -->
                <?php
                }
                if (in_array("Cash", $offline_payments)) {
                ?>
                  <label class="custom-radio">
                    <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                    Cash<span class="checkmark"></span>
                  </label>
                <?php
                }
                if (in_array("Upi", $offline_payments)) {
                ?>
                  <!-- UPI Payment Option Added By Weavers start -->
                  <label class="custom-radio">
                    <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                    UPI<span class="checkmark"></span>
                  </label>
                <?php
                }
                ?>
                <!-- UPI Payment Option Added By Weavers end -->
                &nbsp;
              </div>



              <div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
                <div class="checkbox custom-radio-holder">
                  <span><img src="<?= $logo ?>" alt="" /></span>
                  <!-- <div>
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
                          </div> -->
                  <input type="radio" name="card_mode" use="card_mode_select" value="International" checked style="visibility: hidden;">
                </div>

              </div>
              <div class="card-form" id="paymentDetailsSection" style="display: none;">

                <div class="row">
                  <!--<div class="col-lg-12">
                    <h6>Payment Details</h6>
                  </div>-->
                  <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Cash" actAs='fieldContainer'>
                    <div class="cashbox" style="background:url(/isnezcon2024/images/cash-note-1.png);">
                      <!--<input type="number" maxlength="2" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" class="form-control" placeholder="DD">
                            <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" maxlength="2" class="form-control" placeholder="MM">
                            <input type="number" maxlength="4" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==4) return false;" class="form-control" placeholder="YY">  -->

                      <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">
                    </div>
                  </div>


                  <div class="col-lg-12 input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>


                    <div class="upi-scanner-holder">
                      <div class="scanner-img">
                        <img src="<?= $QR_code ?>" alt="">
                        <p>SCAN TO PAY</p>
                      </div>
                      <div class="form-group">
                        <label for="txn_no">UPI Transaction ID</label>
                        <input type="text" class="form-control" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="UPI Transaction">
                      </div>
                      <div class="form-group">
                        <label for="txn_no">UPI Payment Date</label>
                        <div class="datemmyy d-flex align-items-center">
                          <!-- <input type="number" name="day" maxlength="2" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" class="form-control" placeholder="DD" validate="Please enter day">
                                    <input type="number" name="month" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" maxlength="2" class="form-control" placeholder="MM" validate="Please enter month">
                                    <input type="number" name="year" maxlength="4" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==4) return false;" class="form-control" placeholder="YY" validate="Please enter year"> -->
                          <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select upi date" placeholder="Payment Date">
                        </div>
                      </div>
                    </div>





                    <?php /*? subrata>
                        <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>
                          <!--  <label for="txn_no">UPI Payment Date</label>  -->
                          <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select upi date" placeholder="Payment Date">
                        </div>
                        <?php subrata */ ?>


                  </div>





                  <!-- UPI Payment Option Added By Weavers end -->

                  <!-- Cheque Payment Option Added By Weavers start -->

                  <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
                    <div class="chequebox">

                      <div class="form-group movecorner d-flex align-items-center">
                        <label for="user_first_name">Chq/DD No.</label>
                        <input type="number" class="form-control" name="cheque_number" id="cheque_number" validate="Please enter cheque number" placeholder="123456" type="number" maxlength="6" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;">
                      </div>

                      <div class="form-group d-flex align-items-center">
                        <label for="user_first_name">Drawee Bank:</label>
                        <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="">
                      </div>

                      <div class="form-group d-flex align-items-center">
                        <label for="user_first_name">Date:</label>
                        <input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                      </div>


                    </div>

                  </div>

                  <!-- NEFT Payment Option Added By Weavers start -->

                  <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Neft" actAs='fieldContainer'>
                    <div class="neftbox upi-scanner-holder">
                      <div class="form-group">
                        <div class="datemmyy d-flex align-items-center">
                          <label for="user_first_name">Date</label>
                          <!-- <input type="number" name="day" maxlength="2" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" class="form-control" placeholder="DD" validate="Please enter day">
                               <input type="number" name="month" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" maxlength="2" class="form-control" placeholder="MM" validate="Please enter month">
                               <input type="number" name="year" maxlength="4" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==4) return false;" class="form-control" placeholder="YY" validate="Please enter year"> -->

                          <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">
                          <div class="nefticon"><img src="/isnezcon2024/images/neftbg.png" alt=""></div>
                        </div>
                        <? php/*?>
                    <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date"> <?php*/ ?>

                      </div>
                      <div class="form-group">
                        <label for="user_first_name">Transaction Id</label>
                        <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Transaction Id">
                      </div>
                      <div class="form-group">
                        <label for="user_first_name">Drawee Bank</label>
                        <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name" validate="Please enter neft bank" placeholder="Bank Name">
                      </div>

                    </div>
                  </div>
                  <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Neft" actAs='fieldContainer'>


                  </div>
                  <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="Neft" actAs='fieldContainer'>

                  </div>
                  <!-- NEFT Payment Option Added By Weavers start -->




                  <!-- <div class="col-lg-12" style="display:none;" use="offlinePaymentOption" for="Neft" actAs='fieldContainer'>
                     <label for="neft_upi_file">Attach Screenshot</label>
                    <input type="file" class="form-control" name="neft_upi_file" id="neft_upi_file" validate="Please select neft date" placeholder="Attach Screenshot">
                  </div> -->

                  <!-- RTGS Payment Option Added By Weavers start -->
                  <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
                    <!-- <label for="user_first_name">Transaction Id</label> -->
                    <input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no" validate="Please enter transaction number" placeholder="Transaction Id">

                  </div>
                  <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
                    <!-- <label for="user_first_name">Drawee Bank</label> -->
                    <input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name" validate="Please enter neft bank" placeholder="Bank Name">

                  </div>
                  <div class="col-lg-6" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
                    <!--  <label for="user_first_name">Date</label> -->
                    <input type="date" class="form-control" name="rtgs_date" id="rtgs_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">
                  </div>
                </div>

              </div>

              <div class="righr-btn"><input type="button" class="pay-button" id="pay-button" value="Pay Now" style="display:none;"></div>
            </div>
            <div class="policy-div">
              <ul>
                <li><a href="<?= _BASE_URL_ ?>cancellation.php">Cancellation Policy</a></li>
                <li><a href="<?= _BASE_URL_ ?>privacy-policy.php">Privacy Policy</a></li>
              </ul>
            </div>


          </div>
          <div class="cart-details" id="orderSummerySection">
            <div class="cart-heading">
              <h5>Order Summary</h5>

              <a href="#" class="close-check"><span>&#10005;</span> close</a>
            </div>

            <div class="cart-data-row add-inclusion accompanying-bill" use="totalAmount">

              <table class="table bill" use="totalAmountTable" id="bill_details" style="display:none;">
                <thead>
                  <tr>
                    <th></th>
                    <th>DETAIL</th>
                    <th align="right" style="text-align:right;">AMOUNT (₹)</th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr style="display:none;" use='rowCloneable'>
                    <td> <span use="icon"></span>
                      <span class="btndelete" use="deleteIcon"></span>
                    </td>
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

            <div class="policy-div">
              <ul>
                <li><a href="<?= _BASE_URL_ ?>cancellation.php">Cancellation Policy</a></li>
                <li><a href="<?= _BASE_URL_ ?>privacy-policy.php">Privacy Policy</a></li>
              </ul>
            </div>

            <div class="righr-btn"><input type="button" class="button-40" id="pay-button-vouchar" value="Pay Now"></div>
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
  <script>
    $('.slider-nav').slick({
      slidesToScroll: 1,
      dots: false,


      infinite: true,


      slidesPerRow: 1,
      slidesToShow: 1,


      arrows: true,
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
          arrows: false,
        }
      }]
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

          //alert(accompany_count);

          $('#pageTitle').text("");
          $('#pageTitle').text($(this).attr('title'));


          $('.main-menu').removeClass('active');
          if (currentSection < $('.section').length) {
            currentSection++;
            console.log('next=' + currentSection);
            //

            if (currentSection == 3) {
              $('#paymentOptions').css('filter', '');
              $('#pay-button').show();
              $('.payRadioBtn').prop('disabled', false);

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

              $('#pageTitle').text("");
              currentSection++;

              showSection(Number(currentSection));

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

            $('#pageTitle').text("");
            currentSection++;

            showSection(Number(currentSection));

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

        if (currentSection > 1) {



          if (sec != undefined && sec != '') {
            showSection(sec);
          } else {
            currentSection--;
            if (workshop_count == 0) {

              $('#pageTitle').text("");
              currentSection--;
              showSection(Number(currentSection));

            } else if (accompany_count == 0) {
              $('#pageTitle').text("");
              currentSection--;
              showSection(Number(currentSection));

            } else if (banquet_count == 0) {

              $('#pageTitle').text("");
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
        console.log('section=' + section);
        $('.section').removeClass('active');
        $('#section' + section).addClass('active');
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
                      });
                      $('#user_details').hide();
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
                            $('#user_details').show();

                            // console.log('>>' + $(parent).find(
                            //   "div[use=mobileProcessing]").find(
                            //   "input[name=user_mobile_validated]").val());

                            // if(emailflag==1){

                            enableAllFileds(liParent);
                            $('#radioGender').removeClass('blur_bw');

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

    $(document).on("click change", "input[type=checkbox], input[type=radio]", function() {
      calculateTotalAmount();

    });


    $(document).on("change", "#accommodation_room", function() {

      calculateTotalAmount();
    })

    $(document).on("click", "#deleteItem", function() {

      var reg = $(this).attr('reg');
      var val = $(this).attr('val');
      var regClsId = $(this).attr('regClsId');

      if (reg === 'workshop') {
        var workshop = 'workshop_id_' + val + '_' + regClsId;

        $('#' + workshop).prop('checked', false);
        calculateTotalAmount();
      }
      if (reg === 'accompany') {
        $('#accompanyCount').prop('checked', false);
        $('.form-control accompany_name').val("");
        calculateTotalAmount();
      }
      $(this).closest('tr').remove();

    });

    function calculateTotalAmount() {
      console.log("====calculateTotalAmount====");

      var totalAmount = 0;
      var totalDinnerAmount = 0;
      var totTable = $("table[use=totalAmountTable]");
      $(totTable).children("tbody").find("tr").remove();
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

        //alert(reg);

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
              }

            } else {
              if (isNaN(amt)) {

              } else {
                totalAmount = totalAmount + amt;
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

            if (reg != 'reg') {

              var deleteLink = $('<a></a>')
                .attr('href', 'javascript:void(0)')
                .attr('id', 'deleteItem')
                .attr('class', 'delete-accompany-btn')
                .attr('reg', reg)
                .attr('val', $(this).attr('value'))
                .attr('regClsId', $(this).attr('registrationclassfid'))
                .text('delete')
                .css('border-radius', '20px')
                .css('border', '1px solid red');

              $(cloned).find("span[use=deleteIcon]").append(deleteLink);
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
      totalDinnerAmount = Math.round(totalDinnerAmount, 0);




      $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
      $("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
      $("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount', totalAmount);
      $("div[use=totalAmount]").show();

      $('#subTotalPrc').text((totalAmount).toFixed(2));

    }


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

        var wrapper = $("<div class='accompany-wrapper'></div>");
        wrapper.append(clonedIntro);
        wrapper.append(newAccompany);
        newAccompany.append('<button class="delete-accompany-btn">Delete</button>');

        $("#accompany-container").append(wrapper);

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

    $("#pay-button").click(function() {

      var selectedOption = $("input[type=radio][name='payment_mode']:checked").val();
      var flag = 0;

      if (selectedOption) {

        $("div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='text'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='date'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='radio'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='number']").each(function() {


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

        $("form[name='registrationForm']").submit();
      }

    });
  </script>
</body>

</html>