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
//$mycms->redirect("login.php");
// echo "<pre>";
// print_r(get_included_files());
if (isset($_GET['pathId'])) {
    $_POST['pathId'] = $_GET['pathId'];
    $_SESSION['selectedPathId'] = $_GET['pathId'];
}
$totalSection = 7;

$title = 'Registration';

if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {
  $abstractDelegateId = trim($_REQUEST['abstractDelegateId']);
  $userRec = getUserDetails($abstractDelegateId);
 
 $readonly = 'readonly';
  $redirectUrl = 'registration-combo.php?abstractDelegateId=' . $abstractDelegateId;
} else {
  $mycms->removeAllSession();
  $mycms->removeSession('SLIP_ID');
  $readonly = '';
  $redirectUrl = 'registration-combo.php?';

}

?>
<?
$cutoffs      = fullCutoffArray();
$currentCutoffId  = getTariffCutoffId();
$currentWorkshopCutoffId  = getWorkshopTariffCutoffId();
$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);

$disabled = count($userRec) > 0 ? "" : "disabled='disabled'";

$disabledclass = count($userRec) > 0 ? "" : "disable";

$workshopDetailsArray    = getAllWorkshopTariffs($currentWorkshopCutoffId);
$workshopCountArr      = totalWorkshopCountReport();
$sqlPath 	=	array();
$sqlPath['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                WHERE `id`!='' AND `purpose`='Combo' AND status IN ('A')";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$resultPath 	 = $mycms->sql_select($sqlPath);
//  echo '<pre>'; print_r($currentWorkshopCutoffId);die;
$comboClassifications = getAllRegistrationComboTariffs($currentCutoffId);

$registrationAmount   = getCutoffTariffAmnt($currentCutoffId);
//  echo '<pre>'; print_r($registrationAmount);die;
//echo 'title=='. $userRec['user_title'];
// First, check if there is at least one dinner with amount > 0
$hasDinnerAmount = false;
foreach ($dinnerTariffArray as $dinnerValue) {
    if (isset($dinnerValue[$currentCutoffId]['AMOUNT']) && $dinnerValue[$currentCutoffId]['AMOUNT'] > 0) {
        $hasDinnerAmount = true;
        break; // No need to check further, we found a valid amount
    }
}
// echo '<pre>'; print_r($hasDinnerAmount);die;

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

<body>
  <img src="<?=$cfg['OUTER_BG_IMG']?>" alt="" class="body_bk">

  <style>

  </style>
  <?php


  setTemplateStyleSheet();
  setTemplateBasicJS();
  backButtonOffJS();


  include_once("header.php");

  ?>
  <form class="body-frm" name="registrationForm" enctype="multipart/form-data" method="post" autocomplete="off" action="<?= _BASE_URL_ ?>registration.process.php">
    <input type="hidden" name="act" value="combinedRegistrationProcess" />
    <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?= $currentCutoffId ?>" />
    <input type="hidden" name="reg_area" value="FRONT" />
    <input type="hidden" name="isCombo" id="isCombo" value="N" />
    <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
    <input type="hidden" name="registration_cutoff" id="registration_cutoff" value="<?= $currentCutoffId ?>" />
    <input type="hidden" name="abstractDelegateId" id="abstractDelegateId" value="<?= $abstractDelegateId ?>" />
    <input type="hidden" name="gst_flag" id="gst_flag" value="<?= $cfg['GST.FLAG'] ?>" />
    <div class="registration_wrap">
      <div class="registartion_head">
        <a href="index.php"><i class="fal fa-arrow-left"></i>Back</a>

        <p><span>Registered Email Id</span>
          <script>
            // document.write(localStorage.getItem('user_email_id') || '');

            function setRegistration(id) {
              
              $.ajax({
                url: 'registration-test.php',
                type: 'POST',
                data: {
                  registration_classi_id: id
                },
                success: function(response) {
                  $('#workShops').html($(response).find('#workShops').html());
                  initializeworkShopPlugins(); // re-run your JS for the new content

                  // populate check-in/check-out selects here
                }
              });

            }
          </script>
        </p>
      </div>
      <div class="registration_inner" id="popupBody">
        <div class="registration_left">
          <div class="registration_left_head">
            <h5><?=$rowInfo['company_conf_name']?></h5>
            <h6>Registration Portal</h6>
          </div>
          <ul id="progressbar">
            <li class="active" id="personal"><?php user(); ?><span>Personal</span></li>
              <? if($comboClassifications && $resultPath){ ?>
               <li id="path"><i class="fal fa-road"></i><span>Path</span></li>
               
              <? }
              
              ?>
               <li id="category"><?php conregi(); ?><span>Category</span></li>
                <?
                if (!empty($resultFetchHotel)) {
                ?>
                <li id="stay"><?php hotel(); ?><span>Accommodation</span></li>
                <?
                }
                if ((!empty($workshopDetailsArray))  && $currentWorkshopCutoffId > 0) {
                ?>
                <li id="workshop"><?php workshop(); ?><span>Workshop</span></li>
                <?
                }
                if ($hasDinnerAmount){
                ?>
                <li id="galadinner"><?php dinner(); ?><span>Gala Dinner</span></li>
                <?
                }if ($registrationAmount != '' && $registrationAmount > 0) {
                ?>
                <li id="guests"><?php duser(); ?><span>Accompanying</span></li>
                <?
                }
                ?>
                <li id="review"><?php check(); ?><span>Review</span></li>
             
           
          </ul>
          <div class="registration_left_bottom">
            <h6>Total Payable</h6>
            <? 
              if($cfg['GST.FLAG']==1){
                ?>
              <p class="frm-head d-flex justify-content-between align-items-center gstcharge">GST (18%)
              </p>
              <?
              }
              ?>
            <h5><span id="subTotalPrc" style="color: var(--sky);"></span></h5>
          </div>
        </div>
        <div class="registration_right">


          <!-- persoanl -->
          <fieldset class="registration_right_wrap">
            <div class="registration_right_head">
             <?=$cfg['USER_TITLE']?>
            </div>
            <div class="registration_right_body">
              <div class="registration_right_body_head">
                <div class="registration_right_body_head_left">
                  <h4>Fill your details</h4>
                  <h5>Please provide your details for official records.</h5>
                </div>
              </div>
              <div class="registration_right_body_content" use="registrationUserDetails">
                <div class="form_grid">
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
                  <div class="frm_grp span_0 span_2">
                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                    <div class="d-flex align-items-center">
                      <!-- Hidden field for validation -->
                      <input type="hidden" id="invalidEmail" value="<?= $invalidEmail ?>">

                      <!-- Email icon -->
                      <!-- <span style="margin-right:5px;">
                                            <img src="images/email-R.png" alt="" style="width:24px;height:24px;">
                                        </span> -->

                      <!-- Actual email input -->
                      <input type="text"
                        class="form-control"
                        name="user_email_id"
                        id="user_email_id"
                        value=""
                        placeholder="Email"
                        validate="Please Enter Email Address"
                        autocomplete="nope"
                        style="flex:1;background-color: transparent!important;" >
                    </div>
                  </div>
                  <!-- <div class="frm_grp span_0 span_2">
                      <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                      <input>
                  </div>
                  <div class="frm_grp span_0 span_2">
                      <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                   
                  </div> -->
                  <div class="frm_grp span_0 span_2">
                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                       <div class="sub_frm_grp form_grid">
                          <input type="text"  <?= $readonly ?> value="<?= (!empty($userRec['user_mobile_isd_code']) ? $userRec['user_mobile_isd_code'] : '+91') ?>" name="user_usd_code" required validate="Please Enter Mobile Prefix">
                      
                    <!-- <div class="d-flex align-items-center"> -->
                      <!-- Phone icon -->
                      <!-- <span style="margin-right:5px;">
                                            <img src="images/phone-R.png" alt="" style="width:24px;height:24px;">
                                        </span> -->

                      <!-- Mobile input -->
                      <input type="text"
                        class="span_3"
                        name="user_mobile"
                        id="user_mobile"
                        value=""
                          data-prefill="<?= htmlspecialchars(trim($userRec['user_mobile_no'] ?? '')) ?>"

                        maxlength="10"
                         <?= $readonly ?>
                        onkeypress="return isNumber(event)"
                        required
                        placeholder="Mobile"
                        validate="Please Enter Mobile Number"
                        autocomplete="nope"
                        style="flex:1;">
                    </div>
                  </div>

                  <div class="frm_grp span_1">
                    <p class="frm-head">Title</p>
                    <select name="user_initial_title"
                      class="<?= $disabledclass ?>"
                      <?= $disabled ?>  <?= $readonly ?>
                      validate="Please select your title"
                      style="width:100%; padding:5px;"  required>
                      <option value="">Select Title</option>
                      <option value="Dr" <?= strtoupper($userRec['user_title']) == 'DR' ? 'selected' : '' ?>>Dr.</option>
                      <option value="Prof" <?= strtoupper($userRec['user_title']) == 'PROF' ? 'selected' : '' ?>>Prof.</option>
                      <option value="Mr" <?= strtoupper($userRec['user_title']) == 'MR' ? 'selected' : '' ?>>Mr.</option>
                      <option value="Ms" <?= strtoupper($userRec['user_title']) == 'MS' ? 'selected' : '' ?>>Ms.</option>
                    </select>
                  </div>
                  <div class="frm_grp span_3">
                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                    <input type="text"  <?= $readonly ?> <?= $disabledclass ?>" required placeholder="First Name" name="user_first_name" id="user_first_name" validate="Please Enter First Name" autocomplete="off" <?= $disabled ?> value="<?= ($userRec['user_first_name'] != '') ? ($userRec['user_first_name']) : '' ?>" autocomplete="nope">
                  </div>
                  <div class="frm_grp span_2">
                    <p class="frm-head">Middle Name</p>
                    <input type="text"  <?= $readonly ?> <?= $disabledclass ?>"  placeholder="Middle Name" name="user_middle_name" id="user_middle_name" value="<?= ($userRec['user_middle_name'] != '') ? ($userRec['user_middle_name']) : '' ?>" autocomplete="off" <?= $disabled ?> autocomplete="nope">

                  </div>
                  <div class="frm_grp span_2">
                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                    <input type="text"  <?= $readonly ?> <?= $disabledclass ?>" required placeholder="Last Name" name="user_last_name" id="user_last_name" value="<?= ($userRec['user_last_name'] != '') ? ($userRec['user_last_name']) : '' ?>" validate="Please Enter Last Name" autocomplete="off" <?= $disabled ?> autocomplete="nope">

                  </div>

                  <?php
                  if (in_array("Address", $available_registration_fields)) {
                  ?>
                    <div class="frm_grp span_4">
                      <p class="frm-head">Address</p>
                      <div class="d-flex align-items-center">
                        <input type="text"
                          class=" <?= $disabledclass ?>"  
                          name="user_address"
                          id="user_address" 
                          required
                          value="<?= !empty($userRec['user_address']) ? $userRec['user_address'] : '' ?>"
                          placeholder="Address"
                          autocomplete="off"
                          <?= $disabled ?>
                          validate="Please Enter Your Address"
                          style="flex:1;">
                      </div>
                    </div>

                  <?php
                  }
                  if (in_array("Country", $available_registration_fields)) {
                  ?>
                    <div class="frm_grp span_2">
                      <p class="frm-head">Country</p>
                      <div class="d-flex align-items-center">
                        <!-- Country icon -->
                        <!-- <span style="margin-right:5px;">
                                                <img src="images/country-R.png" alt="" style="width:24px;height:24px;">
                                            </span> -->

                        <!-- Country select -->
                        <select class=" <?= $disabledclass ?>"  <?= $readonly ?>
                          name="user_country"
                          id="user_country"
                          forType="country"
                          required
                          <?= $disabled ?>
                          validate="Please Select Country"
                          style="flex:1;">
                          <option value="">-- Select Country --</option>
                          <?php
                          $sqlFetchCountry = array();
                          $sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                                            WHERE `status` = ? 
                                                                            ORDER BY `country_name` ASC";
                          $sqlFetchCountry['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                          $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                          if ($resultFetchCountry) {
                            foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                          ?>
                              <option value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $userRec['user_country_id']) ? 'selected' : '' ?>>
                                <?= $rowFetchCountry['country_name'] ?>
                              </option>
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

                    <div class="frm_grp span_2">
                      <p class="frm-head">State</p>
                      <div class="d-flex align-items-center">
                        <!-- Country icon -->
                        <!-- <span style="margin-right:5px;">
                                                <img src="images/country-R.png" alt="" style="width:24px;height:24px;">
                                            </span> -->

                        <!-- Country select -->
                        <select class=" <?= $disabledclass ?>"  <?= $readonly ?>
                          name="user_state"
                          id="user_state"
                          forType="state"
                          required
                          <?= $disabled ?>
                          validate="Please Select State"
                          style="flex:1;">
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
                              <option value="<?= $rowFetchState['st_id'] ?>" <?= ($rowFetchState['st_id'] == $userRec['user_state_id']) ? 'selected' : '' ?>>
                                <?= $rowFetchState['state_name'] ?>
                              </option>
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
                    <div class="frm_grp span_2">
                      <p class="frm-head">City</p>
                      <div class="d-flex align-items-center">
                        <!-- Icon -->
                        <!-- <span>
                                                <img src="images/city-R.png" alt="" style="width:24px;height:24px;">
                                            </span> -->
                        <!-- Input field -->
                        <input type="text"
                          class=" <?= $disabledclass ?>"  
                          name="user_city"
                          id="user_city"
                          value="<?= !empty($userRec['user_city']) ? $userRec['user_city'] : '' ?>"
                          placeholder="City"
                          <?= $disabled ?>
                          validate="Please Enter City"
                          autocomplete="nope"
                          style="flex:1;" required>
                      </div>
                    </div>
                  <?php
                  }
                  if (in_array("Pin", $available_registration_fields)) {
                  ?>
                    <div class="frm_grp span_2">
                      <p class="frm-head">Pin</p>
                      <div class="d-flex align-items-center">

                        <input type="text"
                          class=" <?= $disabledclass ?>"  
                          name="user_postal_code"
                          id="user_postal_code"
                          value="<?= !empty($userRec['user_pincode']) ? $userRec['user_pincode'] : '' ?>"
                          placeholder="Postal Code"
                          <?= $disabled ?>
                          validate="Please enter postal code"
                          autocomplete="nope"
                          style="flex:1;" onkeypress="return isNumber(event)" autocomplete="nope" required>
                      </div>
                    </div>
                  <?php
                  }
                  if (in_array("Gender", $available_registration_fields)) {
                  ?>
                    <div class="frm_grp span_2">
                      <p class="frm-head">Gender <i class="mandatory">*</i></p>
                      <div class="cus_check_wrap flex-row" id="radioGender">
                        <label class="cus_check gender_check">
                          <input required  <?= $readonly ?> type="radio"
                            name="user_gender"
                            id="user_gender_male"
                            value="Male"
                            groupname="user_gender"
                            validate="Please select a gender"
                            <?= $disabled ?>
                            <?= (!empty($userRec['user_gender']) && $userRec['user_gender'] == 'Male') ? 'checked' : '' ?>>
                          <span class="checkmark">Male</span>
                        </label>

                        <label class="cus_check gender_check">
                          <input required type="radio"
                            name="user_gender"  <?= $readonly ?>
                            id="user_gender_female"
                            value="Female"
                            groupname="user_gender"
                            validate="Please select a gender"
                            <?= $disabled ?>
                            <?= (!empty($userRec['user_gender']) && $userRec['user_gender'] == 'Female') ? 'checked' : '' ?>>
                          <span class="checkmark">Female</span>
                        </label>

                        <label class="cus_check gender_check">
                          <input required type="radio"
                            name="user_gender"
                            id="user_gender_others"
                            value="Others"  <?= $readonly ?>
                            groupname="user_gender"
                            validate="Please select a gender"
                            <?= $disabled ?>
                            <?= (!empty($userRec['user_gender']) && $userRec['user_gender'] == 'Others') ? 'checked' : '' ?>>
                          <span class="checkmark">Others</span>
                        </label>
                      </div>
                    </div>
                  <?php
                  }
                  if (in_array("Food", $available_registration_fields)) {
                  ?>
                    <div class="frm_grp span_2">
                      <p class="frm-head">Food Preference:</p>
                      <div class="cus_check_wrap flex-row" id="radioGender">
                        <label class="cus_check gender_check">
                          <input type="radio"  <?= $readonly ?> groupname="user_food_choice" name="user_food_choice" id="user_food_choice_veg" validate="Please select your food preference" value="veg" <?= $disabled ?> required="">
                          <span class="checkmark">Veg</span>
                        </label>


                        <label class="cus_check gender_check">
                          <input type="radio"  <?= $readonly ?> groupname="user_food_choice" name="user_food_choice" id="user_food_choice_nonveg" validate="Please select your food preference" value="nonveg" <?= $disabled ?> required>
                          <span class="checkmark">Non-Veg</span>
                        </label>

                      </div>
                    </div>
                  <?php
                  }
                  ?>
                </div>
              </div>
            </div>
            <div class="registration_right_bottom justify-content-end">
                            <button type="button" name="next" class="skip-button skip"><?php skip() ?>Skip</button>

              <!-- <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button> -->
              <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
              <!-- <button class="next action-button <?php if (count($userRec) > 0) {
                                                        echo '';
                                                      } else {
                                                        echo 'disabled-click';
                                                      } ?>" id="user_details" style="display:<?php if (count($userRec) > 0) {
                                                                                                echo 'block';
                                                                                              } else {
                                                                                                echo 'block';
                                                                                              } ?>" workshop-count="<?= count($workshopDetailsArray) ?>" accompany-count="<?= $accompanyCount ?>" banquet-count="<?= count($dinnerTariffArray) ?>" accommodation-count="<?= $countAcc ?>" title="<?= $nextSectionTitle ?>">Continue <i class="fal fa-angle-right"></i>
              </button> -->
            </div>
          </fieldset>
          <!-- persoanl -->
          <? if($comboClassifications && $resultPath){ ?>

          <!-- path -->
          <fieldset class="registration_right_wrap">
            <div class="registration_right_head">
              Path
            </div>

            <div class="registration_right_body">
              <div class="registration_right_body_head">
                <div class="registration_right_body_head_left">
                  <h4>Select Registration Path</h4>
                  <h5>Choose how you would like to attend <?=$rowInfo['company_conf_name']?>.</h5>
                </div>
              </div>
              <div class="registration_right_body_content">
                <div class="cus_check_wrap g2">
                <?  
                 $sql 	=	array();
                  $sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                                  WHERE `id`!='' AND `purpose`='Combo' AND status IN ('A')";
                  //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
                  $result 	 = $mycms->sql_select($sql);
                  if ($result) {
                      foreach ($result as $i => $rowCombo) {
                          $icon_image_combo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowCombo['icon'];
                  ?>
                  <label class="cus_check workshop_select gala_select">
                    <input type="radio" name="pathSelection" class="pathSelection_btn" value="<?=$rowCombo['seq']?>">
                    <span class="checkmark">
                      <n>
                        <i><img src="<?= $icon_image_combo ?>" alt="" style="width:24px;height:24px;"></i>
                        <iii></iii>
                      </n>
                      <g><?=$rowCombo['title']?>
                        <k><?=$rowCombo['description']?></k>
                      </g>
                    </span>
                  </label>
                  <? } } ?>
                </div>
              </div>
            </div>
            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
              <button type="button" name="next" class="next action-button pathselect_continue">Continue<i class="fal fa-angle-right"></i></button>
            </div>
          </fieldset>
            <? } ?>
          <!-- path -->
          <!-- category -->
          <fieldset class="registration_right_wrap category">
            <div class="registration_right_head">
             <?=$cfg['CATEGORY_TITLE']?>
            </div>
            <div class="registration_right_body">
              <div class="registration_right_body_head">
                <div class="registration_right_body_head_left">
                  <h4>Select Category</h4>
                  <h5>Choose the option that best describes your role.</h5>
                </div>
              </div>
              <div class="registration_right_body_content">
                <div class="cus_check_wrap">

                  <?php
                  if ($currentCutoffId > 0) {

                    $conferenceTariffArray = getAllRegistrationTariffs($currentCutoffId);
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
                    ///////////////choose faculty //////////////
                     // Get the abstractDelegateId from request
                      $abstractDelegateId = isset($_REQUEST['abstractDelegateId']) ? trim($_REQUEST['abstractDelegateId']) : '';
                      
                      // Check if user exists in SP_PARTICIPANT_SCHEDULE table
                      $isFacultyUser = false;
                      if (!empty($abstractDelegateId)) {
                          $sqlCheckFaculty = array();
                          $sqlCheckFaculty['QUERY'] = "SELECT COUNT(*) as cnt 
                                                      FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " pd
                                                      INNER JOIN " . _DB_SP_PARTICIPANT_SCHEDULE_ . " ps 
                                                          ON pd.id = ps.participant_id
                                                      WHERE pd.participant_delegate_id = ? 
                                                      AND pd.status = 'A' 
                                                     ";
                          $sqlCheckFaculty['PARAM'][] = array('FILD' => 'participant_delegate_id', 'DATA' => $abstractDelegateId, 'TYP' => 's');
                          $resultCheckFaculty = $mycms->sql_select($sqlCheckFaculty);
                          
                          if ($resultCheckFaculty && $resultCheckFaculty[0]['cnt'] > 0) {
                              $isFacultyUser = true;
                          }
                      }
                      //////////////////faculty end/////////////////////

                    foreach ($conferenceTariffArray as $key => $registrationDetailsVal) {

                      $classificationType = getRegClsfType($key);
                      $isFacultyClassification = (strpos(strtolower($registrationDetailsVal['CLASSIFICATION_TITTLE']), 'faculty') !== false);

                      if ($classificationType == 'DELEGATE') {

                        $getSeatlimitToClassificationID = getSeatlimitToClassificationID($registrationDetailsVal['REG_CLASSIFICATION_ID']);
                        // echo '<pre>'; print_r($getSeatlimitToClassificationID);
                        $disableClass = ($getSeatlimitToClassificationID <= 0) ? "disabled" : "";
                        if ($isFacultyClassification && !$isFacultyUser) {
                            $disableClass = "disabled"; // Disable Faculty option if user not in schedule
                        }
                        $icon = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $registrationDetailsVal['ICON'];
                             
                        $amountText = floatval($registrationDetailsVal['AMOUNT']) > 0
                          ? $registrationDetailsVal['CURRENCY'].' ' . number_format($registrationDetailsVal['AMOUNT'])
                          : "Complimentary";
                  ?>

                        <label class="cus_check regi_category <?= $disableClass ?>" data-aos="fade-up" data-aos-delay="0">
                          <!-- IMPORTANT: Keep original name/id for jQuery support -->
                          <input type="radio" name="registration_classification_id[]" onclick="toggleMemberField(this)" onchange="setRegistration(this.value)" id="registration_classification_id" operationMode="registration_tariff" operationModeType="conference" reg="reg" value="<?= $registrationDetailsVal['REG_CLASSIFICATION_ID'] ?>" currency="<?= $registrationDetailsVal['CURRENCY'] ?>" amount="<?= $registrationDetailsVal['AMOUNT'] ?>" invoiceTitle="Registration" invoiceName="<?= $registrationDetailsVal['CLASSIFICATION_TITTLE'] ?>" <?= $disableClass ?> icon="<?= $icon ?>" data-has-checkbox-desc="<?= !empty(trim($registrationDetailsVal['CHECKBOX_DESCRIPTION'] ?? '')) ? '1' : '0' ?>">

                              <!-- <img src="<?= $icon ?>" -->
                          <span class="checkmark">
                            <n>
                              <i><img src="<?= $icon ?>" alt="" style="width:24px;height:24px;"></i>
                              <g>
                                <?= $registrationDetailsVal['CLASSIFICATION_TITTLE'] ?>
                                <ii><?= $registrationDetailsVal['TITTLE_DESCRIPTION'] ?></ii>
                              </g>
                            </n>
                            <h><?= $amountText ?><i></i></h>
                          </span>
                          <div class="regi_category_sublabel d-none" >
                            <div class="frm_grp">
                              <p class="frm-head">Membership Id <i class="mandatory">*</i></p>
                              <input type="text" name="membership_number" validate="Please Enter Member Id">
                            </div>
                          </div>
                         <?php if (!empty(trim($registrationDetailsVal['CHECKBOX_DESCRIPTION'] ?? ''))) { ?>
                          <div class="regi_category_checkbox_note d-none"
                              data-classification-id="<?= $registrationDetailsVal['REG_CLASSIFICATION_ID'] ?>"
                              onclick="event.stopPropagation();">
                              <label class="regi_note_label">
                                  <input type="checkbox"
                                        class="category_terms_checkbox"
                                        name="category_terms_checkbox[<?= $registrationDetailsVal['REG_CLASSIFICATION_ID'] ?>]"
                                        value="1"
                                        data-classification-id="<?= $registrationDetailsVal['REG_CLASSIFICATION_ID'] ?>">
                                  <span class="regi_checkbox_note_text"><?= htmlspecialchars($registrationDetailsVal['CHECKBOX_DESCRIPTION']) ?></span>
                              </label>
                          </div>
                      <?php } ?>
                      <style>
                      /* Wrapper: sits below the card with breathing room */
                      .regi_category_checkbox_note {
                          margin: 8px 0 0;
                          padding: 0 4px;
                      }

                      /* Checkbox and text on one line */
                      .regi_note_label {
                          display: flex;
                          align-items: flex-start;
                          gap: 10px;
                          margin: 0;
                          cursor: pointer;
                      }

                      /* Small custom checkbox */
                      .regi_note_label .category_terms_checkbox {
                          -webkit-appearance: none;
                          appearance: none;
                          box-sizing: border-box;
                          position: relative;
                          display: inline-block;
                          opacity: 1;
                          flex: 0 0 16px;
                          width: 16px;
                          height: 16px;
                          margin: 2px 0 0;
                          padding: 0;
                          border: 1.5px solid rgba(255, 255, 255, 0.65);
                          border-radius: 3px;
                          background: transparent;
                          cursor: pointer;
                          transition: background .15s, border-color .15s;
                      }

                      /* Checked state: filled box */
                      .regi_note_label .category_terms_checkbox:checked {
                          background: #7fb3ff;          /* match your theme's accent */
                          border-color: #7fb3ff;
                      }

                      /* Tick inside the box */
                      .regi_note_label .category_terms_checkbox:checked::after {
                          content: "";
                          position: absolute;
                          left: 4px;
                          top: 0;
                          width: 4px;
                          height: 9px;
                          border: solid #0a2a55;        /* tick colour, dark so it shows on the light fill */
                          border-width: 0 2px 2px 0;
                          transform: rotate(45deg);
                      }

                      /* Focus ring for keyboard users */
                      .regi_note_label .category_terms_checkbox:focus-visible {
                          outline: 2px solid #7fb3ff;
                          outline-offset: 2px;
                      }

                      .regi_note_label .regi_checkbox_note_text {
                          flex: 1;
                          font-size: 14px;
                          line-height: 1.4;
                          color: #fff;
                      }
                        </style>

                        </label>

                  <?php
                      } // end if DELEGATE
                    } // end foreach
                  }
                  ?>

                </div>
              </div>
            </div>

            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>

              <button name="next" type="button"  value="button" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
            </div>
          </fieldset>
          <!-- category -->
          <!-- accommodation -->
           <? if (!empty($resultFetchHotel)) { ?>
          <fieldset class="registration_right_wrap accomodationFindset">
             <?
              $sqlFetchHotel = array();
              $sqlFetchHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " WHERE `status` = ?";
              $sqlFetchHotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
              $resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
              $hotel_count = count($resultFetchHotel) - 1;
              ?>
              <div class="registration_right_head">
                  <?= $cfg['ACCOMODATION_TITLE'] ?>
              </div>
              <div class="registration_right_body">
                  <div class="registration_right_body_head">
                      <div class="registration_right_body_head_left">
                          <!-- <h4>Choose your accommodation</h4> -->
                          <h5>Official partner hotels with exclusive rates.</h5>
                      </div>
                      <!-- <div class="registration_right_body_head_right">
                          <a class="text_danger text_danger_clear" style="cursor: pointer;">Clear Choise</a>
                      </div> -->
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
                              <?php
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
                                  
                                  $tempCheckIN = array_unique(array_column($dates, 'CHECKIN', 'CHECKINID'));
                                  $tempCheckOUT = array_unique(array_column($dates, 'CHECKOUT', 'CHECKOUTID'));
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
                                <button class="active hotel_tab_btn" data-tab="<?= $val['id'] ?>" data-notes="<?= htmlspecialchars($val['hotel_notes'] ?? '', ENT_QUOTES) ?>"><?= $val['hotel_name'] ?><span><i class="fa-solid fa-star"></i><?=$val['hotelRatings']?></span></button>
                        <?php
                            }
                        }
                        ?>
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
                      </p>
                  </div>
                  <ul class="accomdationprice_total" ></ul>
                  <div class="accomdation_total"  id="hotelNotesBox">
                      <i class="fal fa-info-circle"></i><span id="hotelNotesText">Check-in time is 14:00 hrs and check-out is 12:00 hrs. Complimentary AC shuttle service between official hotels and Biswa Bangla Convention Centre will run daily during conference hours.</span>
                  </div>
              </div>
              <div class="registration_right_bottom">
                  <button type="button" name="previous" class="previous action-button-previous">
                      <i class="fal fa-angle-left"></i>Previous
                  </button>
                  <button type="button" name="next" class="skip-button skip"><?php skip() ?>Skip</button>
                  <button type="button" name="next" class="next action-button" disabled>
                      Continue<i class="fal fa-angle-right"></i>
                  </button>
              </div>
          </fieldset>
           <? } ?>
          <!-- accommodation -->
          <!-- workshop -->
          <?
          if ((!empty($workshopDetailsArray))  && $currentWorkshopCutoffId > 0) {
          ?>
          <fieldset class="registration_right_wrap WorkshopFindset">
            <div class="registration_right_head">
             <?=$cfg['WORKSHOP_TITLE']?>
            </div>

            <div class="registration_right_body">
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
                  $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
                  $workshopCountArr      = totalWorkshopCountReport();

                  // echo '<pre>'; print_r($workshopCountArr);

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
                    <button class="active wrkshp_tab_btn" id="wrkshp_tab_btn" data-tab="<?= $rowsl['workshop_date'] ?>"><?= displayDateFormat($rowsl['workshop_date']) ?></button>
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
                      $registration_classification_id = isset($_POST['registration_classi_id']) ? (int)$_POST['registration_classi_id'] : '';

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
                        <label class="cus_check workshop_select <?= $isDisabled ? 'disabled-label' : '' ?>"  >
                          <!-- <input type="checkbox" name="regimood" checked> -->
                          <input type="checkbox" name="workshop_id[]" value="<?= $rowslcal1['id'] ?>" id="workshop_id_<?= $key_cal1 . '_' . $keyRegClasf ?>" data-type="<?= $rowslcal1['type'] ?>" data-date="<?= $rowslcal1['workshop_date'] ?>" value="<?= $rowslcal1['id'] ?>" <?= $style ?> workshopName="<?= $rowslcal1['sequence_by'] ?>" operationMode="workshopId" amount="<?= $amountInr ?>" invoiceTitle="Workshop" invoiceName="<?= $rowslcal1['classification_title'] ?>" registrationClassfId="<?= $keyRegClasf ?>" workshopCount="<?= $workshopCount ?>" icon="<?= $workshopIcon ?>" reg="workshop">

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
                        <style>
                          .disabled-label {
                            pointer-events: none;
                            opacity: 0.5;
                            cursor: not-allowed;
                          }
                          </style>
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
            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
              <button type="button" name="next" class="skip-button skip"><?php skip() ?>Skip</button>
              <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
            </div>
          </fieldset>
          <?
          }
          ?>
          <!-- workshop -->
          <!-- galadinner -->
          <?
          if (count($dinnerTariffArray) > 0 && $hasDinnerAmount) {

            //  echo '<pre>'; print_r($dinnerTariffArray);        
          ?>

            <fieldset class="registration_right_wrap dinnerFieldset">
              <div class="registration_right_head">
               <?=$cfg['DINNER_TITLE']?>
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
                    <?php
                    foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {
                    if ($dinnerValue[$currentCutoffId]['AMOUNT'] > 0) {

                    ?>
                      <label class="cus_check workshop_select gala_select">
                        <input type="checkbox" class="checkboxClassDinner" name="dinner_value[]" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" operationMode="dinner" use="dinner" amount="<?= $dinnerValue[$currentCutoffId]['AMOUNT'] ?>" invoiceTitle="Gala Dinner" invoiceName="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?>-conference" icon="<?= $banquetIcon ?>" reg="dinner" qty="1">
                        <span class="checkmark">
                          <n>
                            <?php dinner(); ?>
                            <iii></iii>
                          </n>
                          <g><?= $dinnerValue[$currentCutoffId]['dinner_hotel_name'] ?>
                            <ii><?php calendar(); ?><?= $dinnerValue[$currentCutoffId]['DATE'] ?></ii>
                            <k>A traditional Bengali themed evening with live classical music.</k>
                          </g>
                          <h>
                            <l><?=  $registrationDetailsVal['CURRENCY'] . ' ' . number_format($dinnerValue[$currentCutoffId]['AMOUNT']) ?></l>
                          </h>
                        </span>
                      </label>
                    <?
                    }
                    }
                    ?>
                  </div>
                </div>
              </div>
              <div class="registration_right_bottom">
                <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
                <button type="button" name="next" class="skip-button skip"><?php skip() ?>Skip</button>
                <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
              </div>
            </fieldset>
          <?
          }
          ?>
          <!-- galadinner -->
          <!-- guest -->
           <?
           if ($registrationAmount != '' && $registrationAmount > 0) {
            ?>
          <fieldset class="registration_right_wrap accompanyfieldset">
            <div class="registration_right_head">
             <?=$cfg['ACCOMAPNY_TITLE']?>
            </div>
            <div class="registration_right_body">
              <div class="registration_right_body_head">
                <div class="registration_right_body_head_left">
                  <h4>Accompanying Persons</h4>
                  <h5>Add family members.</h5>
                </div>
              </div>
              <div class="registration_right_body_content">
                <div class="guest_wrap" id="accompanyingTableBody">
                  <?php
                  $accompanyIndex = 0;
                  $accompanyCatagory = 1; // same as old
                  $registrationCurrency = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];

                  // For initial guest row (first accompany)
                  ?>
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
                            <!-- <div class="food-preference">
                                                <div class="custom-checkbox accompanying">
                                                    <input type="radio" name="accompany_food_choice[<?= $accompanyIndex ?>]" id="veg_<?= $accompanyIndex ?>" value="VEG">
                                                    <label for="veg_<?= $accompanyIndex ?>"><span>Veg</span></label>
                                                </div>
                                                <div class="custom-checkbox accompanying">
                                                    <input type="radio" name="accompany_food_choice[<?= $accompanyIndex ?>]" id="nonveg_<?= $accompanyIndex ?>" value="NON_VEG">
                                                    <label for="nonveg_<?= $accompanyIndex ?>"><span>Non-Veg</span></label>
                                                </div>
                                            </div> -->

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
                <button type="button" class="add_guest" id="add-accompany-btn">Add Guest</button>
              </div>
            </div>
            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
              <button type="button" name="next" class="skip-button skip"><?php skip() ?>Skip</button>
              <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
            </div>
          </fieldset>
          <?
           }
          ?>
          <!-- guest -->


          <!-- review -->
          <fieldset class="registration_right_wrap">
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
              Review
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
                      <button type="button" class="active"
                        onclick="
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Neft]').prop('checked',true).trigger('click');
                        ">
                        <i class="fal fa-building"></i> NEFT
                      </button>
                      <?php } ?>

                      <?php if (in_array("Upi", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').show();$('#upi').hide();$('#Cards').hide();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Upi]').attr('act','Upi').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> UPI
                      </button>
                      <?php } ?>

                      <?php if (in_array("Card", $offline_payments) && $rowInfo['paymentGateway']=='razorpay') { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Razorpay
                      </button>
                      <?php } ?>
                      <?php if (in_array("Card", $offline_payments) && $rowInfo['paymentGateway']=='atom') { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Card
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
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Neft" hidden>
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
                        <li class="for-neft-rtgs-only">
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
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Drawee Bank</h6>
                          <input type="text" class="form-control mandatory" name="neft_bank_name" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">
                        </li>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Date</h6>
                          <input type="date" class="form-control mandatory" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date">
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
                          <input type="text" class="form-control mandatory utrnft" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction Id" placeholder="Enter Transaction Id">
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
                            <!-- <img src=""> -->
                          </p>
                        </li>
                        <!-- <li>
                          <h6 class="d-flex justify-content-between align-items-center">Transfer via Net Banking or NEFT/IMPS.</h6>
                          <div>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Bank<span class="text-white"><?= $cfg['INVOICE_BANKNAME'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Account<span class="text-white"><?= $cfg['INVOICE_BANKACNO'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Benefeciary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center mb-0">IFSC<span class="text-white"><?= $cfg['INVOICE_BANKIFSC'] ?></span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center mb-0">Branch<span class="text-white"><?= $cfg['INVOICE_BANKBRANCH'] ?></span></p>
                          </div>

                        </li> -->
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="registration_right_bottom">
              <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
              <button type="submit" name="submit" class="submit action-button">Confirm<?php check(); ?></button>
            </div>
          </fieldset>
          <!-- review -->
        </div>
      </div>
    </div>
    <?php include_once('cart.php'); ?>

  </form>
<script>
  // Restore step position and personal data after page load
  $(document).ready(function() {
      // ===== ADD THIS CODE - Check if pathId exists in URL =====
      var urlParams = new URLSearchParams(window.location.search);
      var hasPathId = urlParams.has('pathId');
      
      // If no pathId in URL, clear any saved step to prevent skipping pages
      if (!hasPathId) {
          sessionStorage.removeItem('currentStep');
          sessionStorage.removeItem('personalData');
      }
      // ===== END ADDED CODE =====
      
      // Restore current step from sessionStorage
      var savedStep = sessionStorage.getItem('currentStep');
      if (savedStep && parseInt(savedStep) > 0) {
          var stepToRestore = parseInt(savedStep) - 1;
          var $fieldsets = $('fieldset.registration_right_wrap');
          
          if (stepToRestore < $fieldsets.length && stepToRestore >= 0) {
              $fieldsets.hide();
              $($fieldsets[stepToRestore]).show();
              
              $('#progressbar li').removeClass('active');
              if (stepToRestore < $('#progressbar li').length) {
                  $('#progressbar li').eq(stepToRestore).addClass('active');
              }
              
              window.currentStep = stepToRestore + 1;
              window.current = window.currentStep;
          }
          sessionStorage.removeItem('currentStep');
      }
      
      // Restore personal data
      var savedPersonalData = sessionStorage.getItem('personalData');
      if (savedPersonalData) {
          try {
              var data = JSON.parse(savedPersonalData);
              $('#user_email_id').val('');
              $('#user_mobile').val(data.user_mobile || '');
              $('input[name="user_usd_code"]').val(data.user_usd_code || '+91');
              $('select[name="user_initial_title"]').val(data.user_initial_title || '');
              $('#user_first_name').val(data.user_first_name || '');
              $('#user_middle_name').val(data.user_middle_name || '');
              $('#user_last_name').val(data.user_last_name || '');
              $('#user_address').val(data.user_address || '');
              $('#user_country').val(data.user_country || '');
              $('#user_state').val(data.user_state || '');
              $('#user_city').val(data.user_city || '');
              $('#user_postal_code').val(data.user_postal_code || '');
              if (data.user_gender) {
                  $(`input[name="user_gender"][value="${data.user_gender}"]`).prop('checked', true);
              }
          } catch(e) {
              console.log('Error restoring personal data:', e);
          }
          sessionStorage.removeItem('personalData');
      }
      
      // Restore combo value from URL
      var pathId = urlParams.get('pathId');
      if (pathId) {
          $('#isCombo').val(pathId == 2 ? 'Y' : 'N');
          // Pre-select the radio button that matches
          $('input[name="pathSelection"][value="' + pathId + '"]').prop('checked', true);
      }
      
      // Recalculate total
      if (typeof calculateTotalAmount === 'function') {
          setTimeout(function() {
              calculateTotalAmount();
          }, 100);
      }
  });
</script>
</body>
<?php include_once("includes/js-source.php"); ?>

<script>
  function initializeworkShopPlugins() {
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
 console.log(selectedDate);
             console.log(selectedType);
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
  }
  document.addEventListener("DOMContentLoaded", initializeworkShopPlugins);

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.checkboxClassDinner').forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        calculateTotalAmount();
      });
    });
  });
  $(document).ready(function() {

    $('#subTotalPrc').text((0.00).toFixed(2));

    // Trigger click on the first hotel tab button

  });
///////////////path selction start///////
$(document).on('click', '.pathselect_continue', function() {
    // Get selected path
    var $selectedPath = $('input[name="pathSelection"]:checked');
    
    if (!$selectedPath.length) {
        toastr.error('Please select a registration path', 'Error', {
            progressBar: true,
            timeOut: 3000
        });
        return false;
    }
    
    var pathId = $selectedPath.val();
    var isCombo = (pathId == 2) ? 'Y' : 'N';
    
    // Save current step (Path step index)
    var $visibleFieldset = $('fieldset.registration_right_wrap:visible');
    var currentStepIndex = $('fieldset.registration_right_wrap').index($visibleFieldset);
    if (currentStepIndex === -1) currentStepIndex = 0;
    
    // Save all personal data to preserve after reload
    var personalData = {
        user_email_id: $('#user_email_id').val(),
        user_mobile: $('#user_mobile').val(),
        user_usd_code: $('input[name="user_usd_code"]').val(),
        user_initial_title: $('select[name="user_initial_title"]').val(),
        user_first_name: $('#user_first_name').val(),
        user_middle_name: $('#user_middle_name').val(),
        user_last_name: $('#user_last_name').val(),
        user_address: $('#user_address').val(),
        user_country: $('#user_country').val(),
        user_state: $('#user_state').val(),
        user_city: $('#user_city').val(),
        user_postal_code: $('#user_postal_code').val(),
        user_gender: $('input[name="user_gender"]:checked').val()
    };
    sessionStorage.setItem('personalData', JSON.stringify(personalData));
    
    // Save current step to restore after reload
    sessionStorage.setItem('currentStep', currentStepIndex + 1);
    
    // Reload page with path parameter
    if(pathId == 2){
        var redirectUrl = '<?php echo $redirectUrl; ?>';
        window.location.href = redirectUrl + '&pathId=' + pathId;

    }
});
///////////////path selction end///////
///////////accomodation start//////////
var hotelDates = {};
var selectedQty = {}; // NEW: keyed by package checkbox value -> quantity, survives summary re-renders
var availabilityCheckInProgress = false;
var lastToastTime = 0;
var toastCooldown = 2000;
var isAccoPluginsInitialized = false;

$(document).ready(function() {
    $('.hotel_tab_btn').click(function() {
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
            url: 'registration-test.php',
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

    $('.hotel_tab_btn').first().click();
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
////////////accomodation end///////////

  $(document).ready(function() {
    var storageEmail = localStorage.getItem("user_email_id");
    // var storageMobile = localStorage.getItem("user_mobile");
    if (storageEmail != '' && storageEmail !== undefined) {
      $('#user_email_id').val();
      // checkUserEmail(document.querySelector('.pay-button'));

      // $('input[type=radio][operationmode=registration_tariff][value=3]')


    }
    //////////next,skip,previous start////
    function setProgressBar(curStep) {
      var percent = parseFloat(100 / steps) * curStep;
      percent = percent.toFixed();
      $(".progress-bar")
        .css("width", percent + "%")
    }
    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;
    console.log(steps);
    setProgressBar(current);

    /////////////for continue and previous button start////////////
    $(".next").click(function() {
      console.log('yesNext');
      var current_fs = $(this).closest("fieldset");
      var next_fs = current_fs.next("fieldset");

      var isValid = true;
     if (current_fs.hasClass("category")) {

        // check if any category is selected
        if (!current_fs.find("input[name='registration_classification_id[]']:checked").length) {

            toastr.error('Please select a category', 'Error', {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp"
            });

            isValid = false;
        } else {
            // Check if the selected category has a mandatory checkbox note
            var $selectedCat = current_fs.find("input[name='registration_classification_id[]']:checked");
            var selectedCatId = $selectedCat.val();
            var $note = current_fs.find('.regi_category_checkbox_note[data-classification-id="' + selectedCatId + '"]');
            
            if ($note.length && !$note.hasClass('d-none')) {
                var $cb = $note.find('.category_terms_checkbox');
                if (!$cb.is(':checked')) {
                    toastr.error('Please check the acknowledgement/terms for the selected category', 'Error', {
                        progressBar: true,
                        timeOut: 3000,
                        showMethod: "slideDown",
                        hideMethod: "slideUp"
                    });
                    isValid = false;
                }
            }
        }
    }
      if (current_fs.hasClass("accomodationFindset")) {

        // check if any category is selected
        if (!current_fs.find("input[name='package_id[]']:checked").length) {

          toastr.error('Please select a room', 'Error', {
            progressBar: true,
            timeOut: 3000,
            showMethod: "slideDown",
            hideMethod: "slideUp"
          });

          isValid = false;
          // return false; // ⛔ stop Continue
        }
      }
      if (current_fs.hasClass("WorkshopFindset")) {

        // check if any category is selected
        if (!current_fs.find("input[operationMode=workshopId]:checked").length) {

          toastr.error('Please select a workshop', 'Error', {
            progressBar: true,
            timeOut: 3000,
            showMethod: "slideDown",
            hideMethod: "slideUp"
          });

          isValid = false;
          // return false; // ⛔ stop Continue
        }
      }
      if (current_fs.hasClass("dinnerFieldset")) {

        // check if any category is selected
        if (!current_fs.find("input[operationMode=dinner]:checked").length) {
          toastr.error('Please select at least one banquet', 'Error', {
            progressBar: true,
            timeOut: 3000,
            showMethod: "slideDown",
            hideMethod: "slideUp"
          });

          isValid = false;
          // return false; // ⛔ stop Continue
        }
      }
      if (current_fs.hasClass("accompanyfieldset")) {

        // check if any category is selected
        if (current_fs.find("#accompanyingTableBody li").length === 0) {
          toastr.error('Please select a accompany', 'Error', {
            progressBar: true,
            timeOut: 3000,
            showMethod: "slideDown",
            hideMethod: "slideUp"
          });

          isValid = false;
          // return false; // ⛔ stop Continue
        }
        var foodPreferenceMissing = false;
        var missingRowIndex = -1;
        
        current_fs.find("#accompanyingTableBody li").each(function(index) {
            var $row = $(this);
            // Check if this row has food preference radios
            var $foodRadios = $row.find("input[type='radio'][name^='accompany_food_choice']");
            
            if ($foodRadios.length > 0) {
                // Check if any radio in this row is checked
                if ($row.find("input[type='radio'][name^='accompany_food_choice']:checked").length === 0) {
                    foodPreferenceMissing = true;
                    missingRowIndex = index;
                    return false; // break the loop
                }
            }
        });
        
        if (foodPreferenceMissing) {
            toastr.error('Please select food preference for accompany guest ' + (missingRowIndex + 1), 'Error', {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp"
            });
            isValid = false;
        }

      }
      if (current_fs.hasClass("accompanyfieldset")) {
        // Loop through each guest row
        // console.log(("#accompanyingTableBody li").length);
        $("#accompanyingTableBody li").each(function() {
          $(".accompanyCount").prop("checked", true);

          calculateTotalAmount();

        });
         current_fs.find(".accompany_name").prop("required", true);

        // Recalculate the total
      }
      current_fs.find("input, select, textarea").each(function() {

        if ($(this).prop("required") && $(this).val().trim() === "") {

          var msg = $(this).attr("validate") || "This field is required";

          toastr.error(msg, "Error", {
            progressBar: true,
            timeOut: 3000,
            showMethod: "slideDown",
            hideMethod: "slideUp",
            direction: "ltr"
          });

          $(this).focus();
          isValid = false;
          return false; // ⛔ stop `.each()` loop
        }
      });

      if (!isValid) {
        return false; // ⛔ stop going to next step
      }
      // First, check radio buttons separately
        var radioGroups = {};
        current_fs.find("input[type='radio'][required]").each(function() {
            var name = $(this).attr('name');
            if (!radioGroups[name]) {
                radioGroups[name] = true;
                if ($("input[name='" + name + "']:checked").length === 0) {
                    var msg = $(this).attr("validate") || "Please select an option";
                    toastr.error(msg, "Error", {
                        progressBar: true,
                        timeOut: 3000,
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        direction: "ltr"
                    });
                    isValid = false;
                    return false;
                }
            }
        });

        if (!isValid) {
            return false;
        }
      // -----------------------------
      // MOVE TO NEXT FIELDSET
      // -----------------------------

      if (next_fs.length === 0) return;

      $("#progressbar li")
        .eq($("fieldset").index(next_fs))
        .addClass("active");

      next_fs.show();

      current_fs.animate({
        opacity: 0
      }, {
        step: function(now) {
          var opacity = 1 - now;
          current_fs.css({
            display: "none",
            position: "relative"
          });
          next_fs.css({
            opacity: opacity
          });
        },
        duration: 500
      });

      setProgressBar(++current);
    });


    $(".previous").click(function() {
      var current_fs = $(this).closest("fieldset");
      var previous_fs = current_fs.prev("fieldset");

      if (previous_fs.length === 0) return;

      // Remove class active from progress bar
      $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

      // Show previous fieldset
      previous_fs.show();

      // Hide current fieldset with animation
      current_fs.animate({
        opacity: 0
      }, {
        step: function(now) {
          var opacity = 1 - now;
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
      console.log(current);

    });
    $('.skip').click(function() {


      // $('input[type=radio][operationMode=workshopId]').prop('checked', false);
      // // Uncheck all radio buttons
      // // $('input[type="radio"]').prop('checked', false);

      // $('input[type=checkbox][name=accompanyCount]').prop('checked', false);
      // // Uncheck all radio buttons
      // // $('input[type="radio"]').prop('checked', false);
      // $('input[type=checkbox][operationMode=dinner]').prop('checked', false);

      // Current fieldset
      var current_fs = $(this).closest("fieldset");
      var next_fs = current_fs.next("fieldset"); // next fieldset
      if (current_fs.hasClass("accomodationFindset")) {
        $("input[type=checkbox][name='package_id[]']").prop('checked', false);

        calculateTotalAmount();
      }
      if (current_fs.hasClass("WorkshopFindset")) {
        // $('input[type=radio][operationMode=workshopId]').prop('checked', false);
        $("input[operationMode=workshopId]").prop('checked', false);

        calculateTotalAmount();
      }
      if (current_fs.hasClass("dinnerFieldset")) {

        $('.checkboxClassDinner').prop('checked', false).trigger('change');
        calculateTotalAmount();

      }

      if (current_fs.hasClass("accompanyfieldset")) {

        // 1️⃣ Uncheck the main accompany checkbox
        $("#accompanyCount").prop("checked", false);
        $("#accompanyCounts").prop("checked", false);

        // 2️⃣ Loop through each guest row
        $("#accompanyingTableBody li").each(function(index) {
          var $row = $(this);

          if (index === 0) {
            // First row: reset its values
            $row.find(".accompany_name").val("");
            // $row.find(".accompanyAmountDisplay").text("0.00");
          } else {
            // Remove all other rows
            $row.remove();
          }
        });
         current_fs.find(".accompany_name").prop("required", false);

        // 3️⃣ Reset accompanying count to 1 (only first row remains)
        $("#accompanyCounts").val(1);

        // 4️⃣ Recalculate total
        calculateTotalAmount();
      }
      if (next_fs.length === 0) return; // stop if no next

      // Add class active to progress bar
      $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

      // Show next fieldset
      next_fs.show();

      // Hide current fieldset with animation
      current_fs.animate({
        opacity: 0
      }, {
        step: function(now) {
          var opacity = 1 - now; // fade out current
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

      // Update progress bar function
      setProgressBar(++current);
      console.log(current);
    });
    //////////next,skip,previous end////

    // $(document).ready(function() {
    //   const $mobile = $("#user_mobile");

    //   // Trigger the existing Go button logic automatically when user leaves the field
    //   $mobile.on('blur', function() {
    //     $("#goBtn").trigger('click'); // calls checkUserEmail(this)
    //   });

    //   // Optional: Trigger after typing stops for 500ms (debounce)
    //   let typingTimer;
    //   $mobile.on('input', function() {
    //     clearTimeout(typingTimer);
    //     typingTimer = setTimeout(function() {
    //       $("#goBtn").trigger('click');
    //     }, 500);
    //   });
    // });
    $(document).ready(function() {
      const $mobile = $("#user_mobile");
      const prefill = $mobile.data('prefill');

      if (prefill) {
        $mobile[0].value = prefill; // raw DOM set — does NOT fire input/change/blur
      }

      // Now safely attach your real handlers
      $mobile.on('blur', function() {
        $("#goBtn").trigger('click');
      });

      let typingTimer;
      $mobile.on('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
          $("#goBtn").trigger('click');
        }, 500);
      });
    });
    $("#user_mobile").on('blur', function() {
      checkUserEmail(this); // `this` is the mobile input instead of the button
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
 $('.review_tab button.active').trigger('click');

    $('#neft_document').on('change', function() {
        var file = this.files[0];
        if (file) {
            $('#neft_file_name').text(file.name).show();  // show file name
            $('#neft_remove_btn').show();  // show file name
            $("label[for='neft_document']").hide();       // hide the upload label
        } else {
            $('#neft_file_name').hide();
            $('#neft_remove_btn').hide();  // show file name
            $("label[for='neft_document']").show();
        }
    });

    // Trash button to remove selected file
    $('#neft_remove_btn').on('click', function() {
        $('#neft_document').val('');                 // reset input
        $('#neft_file_name').hide();                 // hide filename
        $('#neft_remove_btn').hide();  // show file name
        $("label[for='neft_document']").show();      // show upload label
    });

   $('#cash_document').on('change', function() {
        var file = this.files[0];

        if (file) {
            $('#cash_file_name').text(file.name).show(); // show filename
           $('#cash_remove_btn').show();
            $("label[for='cash_document']").hide();       // hide upload label
        } else {
            $('#cash_file_name').hide();
            $('#cash_remove_btn').hide();
            $("label[for='cash_document']").show();
        }
    });

    $('#cash_remove_btn').on('click', function() {
        $('#cash_document').val('');                 // clear input
        $('#cash_file_name').hide();     
        $('#cash_remove_btn').hide();            // hide filename
        $("label[for='cash_document']").show();      // show label again
    });
    function validateMobile(mobile) {


    }
    ////////////////////////////end///////////////
    /////////////



    // $(".submit").click(function() {
    //   return false;
    // })
    $("form").on("keydown", function(e) {
        if (e.key === "Enter" && e.target.type !== "textarea") {
            e.preventDefault();
            return false;
        }
    });
    $("form").on("submit", function(e) {
      console.log(steps);
     var selectedOption = $("input[type=radio][name='payment_mode']:checked").val();
    var flag = 0;

    if (selectedOption) {
        // Only validate inputs inside the visible container
        $(".review_right_box:visible input.mandatory").each(function() {
            var type = $(this).attr('type');
              console.log(type);
            if (type === 'radio') {
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
                if ($(this).val() === '') {
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
        toastr.error('Please select payment mode', 'Error', {
            "progressBar": true,
            "timeOut": 5000,
            "showMethod": "slideDown",
            "hideMethod": "slideUp"
        });
        flag = 1;
        return false;
    }

    if (flag === 0) {
        // $("form[name='registrationForm']").submit();
    } else {
        return false;
    }
      // if (current !== steps) {
      //   e.preventDefault();
      //   toastr.error("Please complete all steps before submitting.", "Error", {
      //     progressBar: true,
      //     timeOut: 3000
      //   });
      //   return false;
      // }
      // 🔒 allow submit ONLY from <button type="submit" name="submit">
      const submitter = e.originalEvent.submitter;

      if (!submitter || submitter.type !== "submit" || submitter.name !== "submit" || flag>0) {
        e.preventDefault();
        return false;
      }

      // ✅ FINAL VALIDATION HERE
      // If valid → allow submit
    });
    //////////////for category chooose////
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
    ////////////////////end////////////////////

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
    
  /////
    // var QtyInput = (function() {
    //     var $qtyInputs = $(".accomdationroomqty-input");

    //     if (!$qtyInputs.length) {
    //         return;
    //     }

    //     var $inputs = $qtyInputs.find(".accmomdation-qty");
    //     var $countBtn = $qtyInputs.find(".qty-count");
    //     var qtyMin = parseInt($inputs.attr("min"));
    //     var qtyMax = parseInt($inputs.attr("max"));

    //     $inputs.change(function() {
    //         var $this = $(this);
    //         var $minusBtn = $this.siblings(".qty-count--minus");
    //         var $addBtn = $this.siblings(".qty-count--add");
    //         var qty = parseInt($this.val());

    //         if (isNaN(qty) || qty <= qtyMin) {
    //             $this.val(qtyMin);
    //             $minusBtn.attr("disabled", true);
    //         } else {
    //             $minusBtn.attr("disabled", false);

    //             if (qty >= qtyMax) {
    //                 $this.val(qtyMax);
    //                 $addBtn.attr('disabled', true);
    //             } else {
    //                 $this.val(qty);
    //                 $addBtn.attr('disabled', false);
    //             }
    //         }
    //     });

    //     $countBtn.click(function() {
    //         var operator = this.dataset.action;
    //         var $this = $(this);
    //         var $input = $this.siblings(".accmomdation-qty");
    //         var qty = parseInt($input.val());

    //         if (operator == "add") {
    //             qty += 1;
    //             if (qty >= qtyMin + 1) {
    //                 $this.siblings(".qty-count--minus").attr("disabled", false);
    //             }

    //             if (qty >= qtyMax) {
    //                 $this.attr("disabled", true);
    //             }
    //         } else {
    //             qty = qty <= qtyMin ? qtyMin : (qty -= 1);

    //             if (qty == qtyMin) {
    //                 $this.attr("disabled", true);
    //             }

    //             if (qty < qtyMax) {
    //                 $this.siblings(".qty-count--add").attr("disabled", false);
    //             }
    //         }

    //         $input.val(qty);
    //     });
    // })();
</script>
<script>
$(document).ready(function() {
    // When a payment radio is clicked
    $("input[type=radio][use=payment_mode_select]").click(function() {
        var val = $(this).val(); // Get selected payment value
                calculateTotalAmount();

        // Set registrationMode based on ONLINE/OFFLINE
        if (val === 'Card') {
            $('#registrationMode').val('ONLINE');
        } else {
            $('#registrationMode').val('OFFLINE');
        }

        // Optional: Handle special case for UPI
        if ($(this).attr('act') === 'Upi') {
            $('.for-upi-only').show();
            $('.for-neft-rtgs-only').hide();
            $('#neft_transaction_no').removeClass('mandatory').val('');
            $('#txn_no').addClass('mandatory');

        } else {
            $('.for-upi-only').hide();
            $('.for-neft-rtgs-only').show();
            $('#neft_transaction_no').addClass('mandatory');
            $('#txn_no').removeClass('mandatory').val('');

        }

    });

    // Trigger click on page load if you want default selection
    var defaultChecked = $("input[type=radio][use=payment_mode_select]:checked");
    if(defaultChecked.length) {
        defaultChecked.trigger('click');
    }
});
</script>
<script>
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
function toggleMemberField(element) {

    // Hide all member ID blocks first
    $('.regi_category_sublabel')
        .addClass('d-none')
        .find('input')
        .prop('disabled', true)
        .prop('required', false);   // ✅ remove required

    var title = $(element).attr('invoiceName');

    // Show only if it is Member (NOT Non Member)
    if (title && title.includes('Member') && !title.includes('Non Member') && !title.includes('Non')) {

        $(element).closest('.regi_category')
                  .find('.regi_category_sublabel')
                  .removeClass('d-none')
                  .find('input')
                  .prop('disabled', false)
                  .prop('required', true);   // ✅ add required back
    }
}
</script>
<script>
// Minimal - Show alert for disabled Faculty options
$(document).on('click', '.regi_category.disabled', function(e) {
    var text = $(this).find('.checkmark g').text().toLowerCase();
    if (text.indexOf('faculty') !== -1) {
        e.preventDefault();
        alert('This option is only for Faculty members.');
    }
});
// Show/hide the checkbox note based on selected category
$(document).on('change', "input[type=radio][operationMode=registration_tariff]", function() {
    var $this = $(this);
    var classificationId = $this.val();
    var hasCheckboxDesc = $this.data('has-checkbox-desc') == 1;
    
    // Hide all checkbox notes first
    $('.regi_category_checkbox_note').addClass('d-none');
    // Uncheck all category terms checkboxes
    $('.category_terms_checkbox').prop('checked', false);
    
    if ($this.is(':checked') && hasCheckboxDesc) {
        // Show the note for this specific classification
        $('.regi_category_checkbox_note[data-classification-id="' + classificationId + '"]')
            .removeClass('d-none');
    }
});

// Validation: prevent continue if a category is selected but its terms checkbox isn't checked
function validateCategoryCheckbox() {
    var $selected = $("input[name='registration_classification_id[]']:checked");
    if (!$selected.length) return true; // no category selected, other validation handles this
    
    var classificationId = $selected.val();
    var $note = $('.regi_category_checkbox_note[data-classification-id="' + classificationId + '"]');
    
    if ($note.length && !$note.hasClass('d-none')) {
        // This classification has a checkbox note - check if it's checked
        var $cb = $note.find('.category_terms_checkbox');
        if (!$cb.is(':checked')) {
            toastr.error('Please accept the terms/conditions for the selected category', 'Error', {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp"
            });
            return false;
        }
    }
    return true;
}
</script>
</html>