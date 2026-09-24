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

$mycms->removeAllSession();
$mycms->removeSession('SLIP_ID');

if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {

  $abstractDelegateId  = trim($_REQUEST['abstractDelegateId']);
  $userRec = getUserDetails($abstractDelegateId);
}


$sql   =  array();
$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$result = $mycms->sql_select($sql);
$row         = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];
if ($row['logo_image'] != '') {
  $emailHeader  = $header_image;
}


$currentCutoffId  = getTariffCutoffId();

$sqlcutoff['QUERY']   = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " WHERE status = 'A' AND `id` = ?";
$sqlcutoff['PARAM'][] = array('FILD' => 'id', 'DATA' => $currentCutoffId,  'TYP' => 's');
$rescutoff          = $mycms->sql_select($sqlcutoff);
$endDate          = $rescutoff[0]['end_date'];



$sqlFetchCountdown        = array();
$sqlFetchCountdown['QUERY']    = "SELECT * 
                                FROM " . _DB_LANDING_PAGE_SETTING_;
$resultFetchCountdown       = $mycms->sql_select($sqlFetchCountdown);

$dateArr          = explode("-", $resultFetchCountdown[0]['countdownDate']);

$sqlFlyer  = array();
$sqlFlyer['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
            WHERE `id`!='' AND `title`='Flyer' AND status IN ('A', 'I')";

$resultFlyer    = $mycms->sql_select($sqlFlyer);

//echo '<pre>'; print_r($resultFlyer);
?>



<!DOCTYPE html>
<html>

<?php
javaScriptDefinedValue();

include_once('header.php');

$cutoffs       = fullCutoffArray();
$currentCutoffId  = getTariffCutoffId();

$sqlBanner    =   array();
$sqlBanner['QUERY'] = "SELECT * FROM " . _DB_SITE_ICON_SETTING_ . " 
                          WHERE status='A' AND title='Home Banner' ";

$resultBanner  = $mycms->sql_select($sqlBanner);

//fetching website name from company information
$sql   =  array();
$sql['QUERY'] = "SELECT `invoice_website_name` FROM " . _DB_COMPANY_INFORMATION_ . " 
              WHERE `id`!=''";
$result    = $mycms->sql_select($sql);
$websiteName = $result[0]['invoice_website_name'];

?>



<body>

  <main>

    <?php include_once('sidebar_icon.php');
    ?>

    <div class="mail-home"><a href="<?= $cfg['SITE_LINK'] ?>">

        <!--  <img src="<?= _BASE_URL_ ?>images/arrow-mail.png"
        alt="" /> -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
          <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
        </svg>

        <?= " " . $cfg['SITE_URL'] ?></a>

      <!-- <span><?= $cfg['SITE_URL'] ?></span> -->
    </div>



    <!-- ============= PRIVACY SECTION ======================== -->
    <?php $sql   =  array();
    $sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
											WHERE `id`!='' AND `purpose`='Side Icon' AND status IN ('A', 'I') ORDER BY `id`";
    // $sql['PARAM'][]	=	array('FILD' => 'status' ,  'DATA' => 'A' , 'TYP' => 's');					 
    $result    = $mycms->sql_select($sql);
    if ($result) {
    ?>
      <button class="cpvc">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
          <path d="M16 132h416c8.8 0 16-7.2 16-16V76c0-8.8-7.2-16-16-16H16C7.2 60 0 67.2 0 76v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16z" />
        </svg>
      </button>
      <div class="policy-section">
        <div class="policy-section-div" id="pvc">

          <ul>
            <?php
            foreach ($result as $i => $row) {
              @$i++;
              $icon_image = $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['icon']; ?>
              <li>
                <a href="<?= _BASE_URL_ . $row['page_link'] ?>"><img src="<?= $icon_image ?>" alt="" data-aos="zoom-in"
                    data-aos-delay="100" data-aos-duration="500" /> <span><?= $row['title'] ?></span></a>
              </li>
            <?php } ?>
          </ul>
        </div>
      <?php }
    $sql   =  array();
    $sql['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
                       WHERE `id` = 1";
    $result       = $mycms->sql_select($sql);
    $row         = $result[0];
    $invalidEmail = $row['notification_invalid_email'];
    $registeredEmail = $row['notification_registered_email'];
    $emptyEmail = $row['notification_empty_email'];
    // echo "msg= ".$invalidEmail;
      ?>
      </div>

      <section class="login-main-section mt-5">
        <div class="container">
          <div class="login-greadient-sec">
            <div class="row">

              <div class="col-lg-6">
                <div class="login-section">
                  <div class="logo">
                    <img src="<?= $emailHeader ?>" />
                  </div>
                  <div class="log-in-heading">
                    <div class="morefildinfo">

                      <div class="form-group mb-4">
                        <label for="floatingInput">Email Address</label>
                        <div class="index-go align-items-center d-flex">
                          <span class="emailicon"> <img src="images/email-R.png" alt=""></span>
                          <?php
                          $sqlRegClasf      = array();
                          $sqlRegClasf['QUERY']  = "SELECT * FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " WHERE status = 'A' ";

                          $resRegClasf       = $mycms->sql_select($sqlRegClasf);
                          ?>
                          <input type="hidden" id="isCombo" value="<?= empty($resRegClasf) ? 'N' : 'Y' ?>">
                          <input type="hidden" id="invalidEmail" value="<?= $invalidEmail ?>">
                          <input type="hidden" id="registeredEmail" value="<?= $registeredEmail ?>">
                          <input type="hidden" id="emptyEmail" value="<?= $emptyEmail ?>">
                          <input type="text" name="user_email_id" id="user_email_id" value="<?= base64_decode($_REQUEST['key'])?>" class="form-control" placeholder="Please enter email address">
                        </div>
                         <div class="d-flex justify-content-end">
                          <button type="button" id="checkUserEmail" class="btn">Go</button>
                        </div>
                      </div>
                      <div class="form-group mb-4" id="unique" style="display:none;">
                        <label for="floatingInput">unique Sequence</label>
                        <div class="index-go align-items-center d-flex">
                          <span class="emailicon"> <img src="images/password-line-icon.png" alt=""></span>
                          <input class="form-control" type="text" name="user_unique_sequence" id="user_unique_sequence" value="#" placeholder="Ex: #00000000" onkeypress="return isNumber(event)">
                        </div>


                      </div>
                      <input type="hidden" name="abstractDelegateId" id="abstractDelegateId">
                    </div>
                  </div>

                  <div class="login-buttons">
                    <a href="<?= _BASE_URL_ ?>" class="login-btns" id="login_operation" style="cursor: pointer;" disabled="disabled">Login</a>

                    <a href="javascript:void(0)" onclick="sendUrl()" class="login-btns" style="cursor: pointer;" id="registration_operation" disabled="disabled">Register Now</a>

                    <a class="login-btns" id="pay-button-modal" style="cursor: pointer;" disabled="disabled">Pay Now</a>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="countdown-wraper">
                  <h5><?= $resultFetchCountdown[0]['countdownText'] ?></h5>
                  <div class="countdown">


                    <div class="col-xs-3 timeLeftDays">
                      <p style="margin-bottom: 0; color: white;"><span id="dday">000</span>

                      </p>
                      <p>DAY</p>
                    </div>
                    <div class="col-xs-3 bloc-time hours">
                      <p style="margin-bottom: 0;color: white;"><span id="dhour">00</span>

                      </p>
                      <p>HOUR</p>
                    </div>
                    <div class="col-xs-3 bloc-time min">
                      <p style="margin-bottom: 0;color: white;"><span id="dmin">00</span>
                      <p>MIN</p>
                    </div>
                    <div class="col-xs-3 bloc-time sec">
                      <p style="margin-bottom: 0;color: white;"><span id="dsec">00</span>
                      <p>SEC</p>
                    </div>

                  </div>
                  <div class="hour-text">


                  </div>
                </div>
                <div class="slider-home">
                  <div class="slider-home-inner" id="popup-slider">
                    <?php
                    foreach ($resultFlyer as $k => $row) {
                      $icon_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['image'];
                    ?>
                      <div class="slider-home-inner-popup"> <img src="<?= $icon_image ?>" width="100%" />
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


      </section>
      <?php include_once('footer.php'); ?>
  </main>
  <div class="reg-type-popup" id="combo_modal" style="display:none">
    <?php
    $sql   =  array();
    $sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                    WHERE `id`!='' AND `purpose`='Combo' AND status IN ('A', 'I') ORDER BY seq";
    //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
    $result    = $mycms->sql_select($sql);

    ?>
    <div class="reg-type-popup-inner">
      <div class="reg-type-popup-wrap">
        <button onclick="$('.reg-type-popup').hide();"><img src="<?= _BASE_URL_ ?>images/close.png"></button>
        <div class="reg-type-popup-box">
          <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon'] ?>">
          <h5><?= $result[0]['title'] ?></h5>
          <?= $cfg['IND_REG_POPUP'] ?>
          <a href="<?= _BASE_URL_ ?>registration.tariff.php">Proceed</a>
        </div>
        <div class="reg-type-popup-box">
          <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['icon'] ?>">
          <h5><?= $result[1]['title'] ?></h5>
          <?= $cfg['COMBO_REG_POPUP']   ?>
          <a href="<?= _BASE_URL_ ?>accomodation-package.php">Proceed</a>
        </div>
      </div>
    </div>
  </div>
  <script>
    //  Change the items below to create your countdown target date and announcement once the target date and time are reached.  
    var current =
      ""; //enter what you want the script to display when the target date and time are reached, limit to 20 characters
    var year = <?= $dateArr[0] ?>; //Enter the count down target date YEAR
    var month = <?= $dateArr[1] ?>; //Enter the count down target date MONTH
    var day = <?= $dateArr[2] ?>; //>Enter the count down target date DAY
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

</body>

</html>

<div class="checkout-main-wrap" id="payModal">
  <div class="checkout-popup">
    <div class="card-details">
      <div class="card-details-inner">
        <label>Email Address</label>
        <input type="hidden" id="invalidEmail" value="<?= $invalidEmail ?>">
        <input type="hidden" id="emptyEmail" value="<?= $emptyEmail ?>">
        <input type="text" name="user_email_id" id="user_email_id" class="form-control">
        <div class="righr-btn"><input type="button" class="pay-button" id="pay-button-modal" value="Submit"></div>
      </div>
      <a class="close-check"><span>&#10005;</span> close</a>
    </div>
  </div>
</div>

<!-- <div class="checkout-main-wrap" id="paymentVoucherModal">
  <?php
  $sqlLogo    =   array();
  $sqlLogo['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
			    WHERE title='Online Payment Logo' ";

  $resultLogo      = $mycms->sql_select($sqlLogo);
  $logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultLogo[0]['image'];
  ?>
  <div class="checkout-popup">
    <div class="card-details">
      <div class="card-details-inner">
        <img src="<?= $logo ?>" alt="" />

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
</div> -->

<!-- css for checkout -->


<div id="paymentVoucherModal" class="checkout-main-wrap">
  <div class="checkout-main-wrap-inner">
    <div class="checkout-main-wrap-box">
      <a class="close-check" style="cursor: pointer;"><span>✕</span></a>
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
          <h4 class="block-head">Billed to</h4>
          <div id="order_details">
            <!-- ajax -->
          </div>
          <!-- <p>Bank Name
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
                    </p> -->
        </div>


        <div class="bank-info">
          <h4 class="block-head">Helpline No.</h4>
          <p><i class="mr-2"></i><?= $cfg['CART_HELPLINE'] ?></p>
        </div>
      </div>
      <!-- ============================ PAYMENT OPTIONS DIV ============================-->
      <div class="payment-wrap" id="paymentOptions" style="height: 100%;width: 682px;">
        <form name="frmApplyPayment" id="frmApplyPayment" action="registration.process.php" method="post">
          <div class="paymnet-box" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
            <input type="hidden" id="slip_id" name="slip_id" />
            <input type="hidden" id="delegate_id" name="delegate_id" />
            <input type="hidden" name="act" value="paymentSet" />
            <input type="hidden" name="mode" id="mode" />
            <input type="radio" name="card_mode" use="card_mode_select" value="Indian" checked style="visibility: hidden;">
          </div>
          <div class="summery" id="orderSummerySection">
            <h4 class="block-head">Order Summery</h4>

            <div class="cart-data-row " use="totalAmount" id="orderSummeryInvoices">

              <!-- ajax -->

            </div>


            <button class="payment-button" id="pay-button-vouchar">Pay Now</button>&nbsp;
          </div>
        </form>
      </div>

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
<script type="text/javascript">
  $('#payModalBtn').click(function() {
    $('#payModal').show();
  });


  //================================= Pay now button empty email pop-up ==================================
  $(document).on("click", "#pay-button-modal", function() {

    var user_email_id = $('#user_email_id').val();
    var flag = 0;

    if (user_email_id == '') {
      var emptyEmail = $("#emptyEmail").val();
      toastr.error(emptyEmail, 'Error', {
        "progressBar": true,
        "timeOut": 3000,
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
      });

      flag = 1;
      return false;
    }
    if (user_email_id != '') {
      var invalidEmail = $("#invalidEmail").val();
      var filter =
        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
      if (!filter.test(user_email_id)) {

        toastr.error(invalidEmail, 'Error', {
          "progressBar": true,
          "timeOut": 3000,
          "showMethod": "slideDown",
          "hideMethod": "slideUp"
        });

        flag = 1;
        return false;

      }
    }

    if (flag == 0) {
      $.ajax({
        type: "POST",
        url: jsBASE_URL + 'login.process.php',
        data: 'action=getPaymentVoucharDetails&user_email_id=' + user_email_id,
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
            // $('#paymentVoucherBody').append(JSONObject.data);
            $('#orderSummeryInvoices').html(JSONObject.orderSummeryInvoices);
            $('#order_details').html(JSONObject.data);

            $('#slip_id').val(JSONObject.slipId);
            $('#delegate_id').val(JSONObject.delegateId);
            $('#mode').val(JSONObject.invoice_mode);

            $('#loginBtn').prop('disabled', true);

            toastr.error(JSONObject.msg, 'Success', {
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
  });

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

  $(document).on("click", "#login_operation", function(e) {
    e.preventDefault();
    var user_email_id = $('#user_email_id').val();
    //alert(user_email_id);

    var flag = 0;

    if (user_email_id == '') {
      var emptyEmail = $("#emptyEmail").val();
      toastr.error(emptyEmail, 'Error', {
        "progressBar": true,
        "timeOut": 3000,
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
      });

      flag = 1;
      return false;
    }

    if (user_email_id != '') {
      var filter =
        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
      if (!filter.test(user_email_id)) {
        var invalidEmail = $("#invalidEmail").val();
        toastr.error(invalidEmail, 'Error', {
          "progressBar": true,
          "timeOut": 3000,
          "showMethod": "slideDown",
          "hideMethod": "slideUp"
        });

        flag = 1;
        return false;

      }
    }

    if (flag == 0) {
      $.ajax({
        type: "POST",
        url: jsBASE_URL + 'login.process.php',
        data: 'action=getLoginValidation&user_email_id=' + user_email_id + '&user_unique_sequence=',
        dataType: 'json',
        async: false,
        success: function(JSONObject) {
          console.log(JSONObject);

          if (JSONObject.error == 400) {
            toastr.error(JSONObject.msg, 'Error', {
              "progressBar": true,
              "timeOut": 5000,
              "showMethod": "slideDown",
              "hideMethod": "slideUp"
            });
          } else if (JSONObject.succ == 200) {
            $('#loading_indicator').show();

            $('#loginBtn').prop('disabled', true);


            if (!JSONObject.msg && JSONObject.msg != '') {

            } else {
              $('#login_operation').attr('id', 'login_final_operation');
              $('#unique').show();
            }

          }


        }
      });
    }
  });

  $(document).on("click", "#login_final_operation", function(e) {
    e.preventDefault();
    var user_email_id = $('#user_email_id').val();
    var user_unique_sequence = $('#user_unique_sequence').val();

    var flag = 0;

    if (user_email_id == '') {
      toastr.error('Please enter the email id', 'Error', {
        "progressBar": true,
        "timeOut": 3000,
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
      });

      flag = 1;
      return false;
    }
    if (user_unique_sequence != '') {

      var regex = /^#\d+$/;
      if (regex.test(user_unique_sequence)) {
        //console.log("String matches the pattern.");
      } else {
        //console.log("String does not match the pattern.");
        toastr.error('Please enter the unique sequence', 'Error', {
          "progressBar": true,
          "timeOut": 3000,
          "showMethod": "slideDown",
          "hideMethod": "slideUp"
        });

        flag = 1;
        return false;
      }

    }

    if (user_email_id != '') {
      var filter =
        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
      if (!filter.test(user_email_id)) {

        toastr.error('Please enter valid email id', 'Error', {
          "progressBar": true,
          "timeOut": 3000,
          "showMethod": "slideDown",
          "hideMethod": "slideUp"
        });

        flag = 1;
        return false;

      }
    }

    if (flag == 0) {
      $.ajax({
        type: "POST",
        url: jsBASE_URL + 'login.process.php',
        data: 'action=getLoginValidation&user_email_id=' + user_email_id + '&user_unique_sequence=' + user_unique_sequence,
        dataType: 'json',
        async: false,
        success: function(JSONObject) {
          console.log(JSONObject);

          if (JSONObject.error == 400) {
            if (JSONObject.msg) {
              toastr.error(JSONObject.msg, 'Error', {
                "progressBar": true,
                "timeOut": 5000,
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
              });
            }

          } else if (JSONObject.succ == 200) {

            $('#loading_indicator').show();
            $('#login_final_operation').prop('disabled', true);
            if (JSONObject.msg) {
              toastr.success(JSONObject.msg, 'Success', {
                "progressBar": true,
                "timeOut": 3000,
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
              });
            }

            setTimeout(function() {
              $('#loading_indicator').hide();
              $('#login_final_operation').prop('disabled', false);
              window.location.href = jsBASE_URL + 'profile.php';

            }, 1500);
          }


        }
      });
    }
  });


  $(document).on("click", "#registration_operation", function(e) {
    e.preventDefault();

    var isCombo = $('#isCombo').val();
    if (isCombo == 'Y') {
      $('#combo_modal').show();
      var user_email_id = $('#user_email_id').val();
      var emailId = $('#user_email_id').val();
    } else {
      window.location.href = "<?= _BASE_URL_ ?>registration.tariff.php";
    }


    //============================= reg check =========================
    // if(emailId==""){
    //   window.location.href = "https://ruedakolkata.com/isnezcon2024/registration.tariff.php";
    // }
    // if (emailId != '') {
    //       var filter =
    //           /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    //       if (filter.test(emailId)) {
    //          // $(obj).hide();

    //           console.log(jsBASE_URL + 'returnData.process.php?act=getEmailValidationStatus&email=' +
    //               emailId);
    //           setTimeout(function() {
    //               $.ajax({
    //                   type: "POST",
    //                   url: jsBASE_URL + 'returnData.process.php',
    //                   data: 'act=getEmailValidationStatus&email=' + emailId,
    //                   dataType: 'json',
    //                   async: false,
    //                   success: function(JSONObject) {
    //                       console.log("response: "+JSONObject);

    //                       if (JSONObject.STATUS == 'IN_USE') {
    //                          $('#abstractDelegateId').val(JSONObject.ID);
    //                          var registeredEmail=$("#registeredEmail").val();
    //                            toastr.success(registeredEmail, 'Success', {
    //                             "progressBar": true,
    //                             "timeOut": 3000, 
    //                             "showMethod": "slideDown", 
    //                             "hideMethod": "slideUp"    
    //                          });
    //                           setTimeout(function () {
    //                             $('#login_operation').attr('id', 'login_final_operation');
    //                             $('#unique').show();
    //                              //  window.location.href= jsBASE_URL;

    //                             },3000);

    //                       } else if (JSONObject.STATUS == 'NOT_PAID') {

    //                           if(emailId!='')
    //                           {
    //                               $.ajax({
    //                               type: "POST",
    //                               url: jsBASE_URL + 'login.process.php',
    //                               data: 'action=getPaymentVoucharDetails&user_email_id=' + emailId,
    //                               dataType: 'json',
    //                               async: false,
    //                               success: function(JSONObject) {
    //                                   console.log(JSONObject);

    //                                   if(JSONObject.error==400)
    //                                   {
    //                                        toastr.error(JSONObject.msg, 'Error', {
    //                                         "progressBar": true,
    //                                         "timeOut": 3000, 
    //                                         "showMethod": "slideDown", 
    //                                         "hideMethod": "slideUp"    
    //                                      });
    //                                   }
    //                                   else if(JSONObject.succ==200)
    //                                   {
    //                                     $('#user_email_id').val("");
    //                                     $('#loading_indicator').show();
    //                                     $('#payModal').hide();
    //                                     $('#paymentVoucherBody').append(JSONObject.data);

    //                                     $('#slip_id').val(JSONObject.slipId);
    //                                     $('#delegate_id').val(JSONObject.delegateId);
    //                                     $('#mode').val(JSONObject.invoice_mode);

    //                                     $('#loginBtn').prop('disabled', true);

    //                                     toastr.success(JSONObject.msg, 'Success', {
    //                                         "progressBar": true,
    //                                         "timeOut": 2000, 
    //                                         "showMethod": "slideDown", 
    //                                         "hideMethod": "slideUp"    
    //                                      });
    //                                     setTimeout(function () {
    //                                       $('#loading_indicator').hide();
    //                                        $('#paymentVoucherModal').show();
    //                                       $('#loginBtn').prop('disabled', false);
    //                                          //window.location.href= jsBASE_URL + 'profile.php';

    //                                       },2000);
    //                                   }


    //                                 }
    //                             });
    //                           }

    //                       } 
    //                       else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE') 
    //                       {
    //                           if(emailId!='')
    //                           {
    //                               $.ajax({
    //                               type: "POST",
    //                               url: jsBASE_URL + 'login.process.php',
    //                               data: 'action=getPaymentVoucharDetails&user_email_id=' + emailId,
    //                               dataType: 'json',
    //                               async: false,
    //                               success: function(JSONObject) {
    //                                   console.log(JSONObject);

    //                                   if(JSONObject.error==400)
    //                                   {
    //                                        toastr.error(JSONObject.msg, 'Error', {
    //                                         "progressBar": true,
    //                                         "timeOut": 3000, 
    //                                         "showMethod": "slideDown", 
    //                                         "hideMethod": "slideUp"    
    //                                      });
    //                                   }
    //                                   else if(JSONObject.succ==200)
    //                                   {
    //                                     $('#user_email_id').val("");
    //                                     $('#loading_indicator').show();
    //                                     $('#payModal').hide();
    //                                     $('#paymentVoucherBody').append(JSONObject.data);

    //                                     $('#slip_id').val(JSONObject.slipId);
    //                                     $('#delegate_id').val(JSONObject.delegateId);
    //                                     $('#mode').val(JSONObject.invoice_mode);

    //                                     $('#loginBtn').prop('disabled', true);

    //                                     toastr.success(JSONObject.msg, 'Success', {
    //                                         "progressBar": true,
    //                                         "timeOut": 2000, 
    //                                         "showMethod": "slideDown", 
    //                                         "hideMethod": "slideUp"    
    //                                      });
    //                                     setTimeout(function () {
    //                                       $('#loading_indicator').hide();
    //                                        $('#paymentVoucherModal').show();
    //                                       $('#loginBtn').prop('disabled', false);
    //                                          //window.location.href= jsBASE_URL + 'profile.php';

    //                                       },2000);
    //                                   }


    //                                 }
    //                             });
    //                           }
    //                       } else if (JSONObject.STATUS == 'PAY_NOT_SET_OFFLINE') {
    //                           var payNotSetModalOffline = $('#payNotSetModalOffline');
    //                           $(payNotSetModalOffline).modal('show');
    //                           $(payNotSetModalOffline).modal('show');

    //                           $(obj).show();
    //                       } else if (JSONObject.STATUS == 'AVAILABLE') {
    //                         window.location.href = "https://ruedakolkata.com/isnezcon2024/registration.tariff.php";
    //                         //  enableAllFileds(liParent);



    //                         //   var JSONObjectData = JSONObject.DATA;
    //                         //   if (JSONObjectData) {

    //                         //        $('#abstractDelegateId').val(JSONObjectData.ID);
    //                         //       $(liParent).find('#user_first_name').val(JSONObjectData
    //                         //           .FIRST_NAME);
    //                         //       $(liParent).find('#user_middle_name').val(JSONObjectData
    //                         //           .MIDDLE_NAME);
    //                         //       $(liParent).find('#user_last_name').val(JSONObjectData
    //                         //           .LAST_NAME);
    //                         //       $(liParent).find('#user_mobile').val(JSONObjectData
    //                         //           .MOBILE_NO);

    //                         //       if ($(liParent).find('#user_mobile').val() != '') {
    //                         //           checkMobileNo($(liParent).find('#user_mobile'));
    //                         //       }

    //                         //       $(liParent).find('#user_phone_no').val(JSONObjectData
    //                         //           .PHONE_NO);
    //                         //       $(liParent).find('#user_address').val(JSONObjectData
    //                         //           .ADDRESS);
    //                         //       $(liParent).find('#user_city').val(JSONObjectData.CITY);
    //                         //       $(liParent).find('#user_postal_code').val(JSONObjectData
    //                         //           .PIN_CODE);

    //                         //       $(liParent).find('#user_country').val(JSONObjectData
    //                         //           .COUNTRY_ID);
    //                         //       $(liParent).find('#user_country').trigger("change");

    //                         //       $(liParent).find('#user_state').val(JSONObjectData
    //                         //           .STATE_ID);
    //                         //   }
    //                       }
    //                   }
    //               });
    //           }, 500);
    //       } else {
    //         var invalidEmail=$("#invalidEmail").val();
    //         console.log("msg= "+invalidEmail);
    //                  toastr.error(invalidEmail, 'Error', {
    //                             "progressBar": true,
    //                             "timeOut": 5000, // 3 seconds
    //                             "showMethod": "slideDown", // Animation method for showing
    //                             "hideMethod": "slideUp"    // Animation method for hiding
    //                           });
    //       }
    //   } else {
    //       //popoverAlert(emailIdObj);
    //   }
  });


  $(document).ready(function() {


    $('#checkUserEmail').click(function() {
      var emailId = $('#user_email_id').val();
      var flag = 0;
      if (emailId != '') {
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

        if (filter.test(emailId)) {
          console.log(jsBASE_URL + 'returnData.process.php?act=getEmailValidation&email=' + emailId);

          setTimeout(function() {

            $.ajax({

              type: "POST",
              url: jsBASE_URL + 'returnData.process.php',
              data: 'act=getEmailValidationStatusAbstract&email=' + emailId,
              dataType: 'json',
              async: false,
              success: function(JSONObject) {
                //console.log(JSONObject);

                if (JSONObject.STATUS == 'IN_USE') {
                  $('#login_operation').removeAttr('disabled');
                  $('#abstractDelegateId').val(JSONObject.ID);
                  var registeredEmail = $("#registeredEmail").val();
                  toastr.success(registeredEmail, 'Success', {
                    "progressBar": true,
                    "timeOut": 1000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                  });
                  setTimeout(function() {
                    $('#login_operation').show();
                    $('#login_operation').attr('id', 'login_final_operation');
                    $('#unique').show();
                    $('#checkUserEmail').hide();
                    //  window.location.href= jsBASE_URL;

                  }, 3000);

                } else if (JSONObject.STATUS == 'NOT_PAID') {
                  if (emailId != '') {
                    // alert(JSONObject.msg);
                    $('#login_operation').removeAttr('disabled');
                    $('#login_operation').click();
                    $('#pay-button-modal').removeAttr('disabled');
                    $('#login_operation').attr('disabled', true);
                    // $('#pay-button-modal').removeAttr('disabled');
                  }
                } else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE') {
                  if (emailId != '') {
                    $('#pay-button-modal').removeAttr('disabled');
                    $('#login_operation').removeAttr('disabled');
                  }
                } else if (JSONObject.STATUS == 'NOT_AVAILABLE') {

                  $('#registration_operation').removeAttr('disabled');

                  $('#abstract_basic_details').show();
                  $('#abstractSubmit').show();
                  $('#checkUserEmail').hide();
                  var registerDiv = $("#registerModal");

                  try {
                    var JSONObjectData = JSONObject.DATA;
                    $(registerDiv).find('#email_div').html('');
                    $(registerDiv).find('#user_first_name').val(JSONObjectData.FIRST_NAME);
                    $(registerDiv).find('#user_middle_name').val(JSONObjectData.MIDDLE_NAME);
                    $(registerDiv).find('#user_last_name').val(JSONObjectData.LAST_NAME);
                    $(registerDiv).find('#user_mobile').val(JSONObjectData.MOBILE_NO);
                    $(registerDiv).find('#user_usd_code').val(JSONObjectData.MOBILE_ISD_CODE);

                    $(registerDiv).find('#user_phone_no').val(JSONObjectData.PHONE_NO);
                    $(registerDiv).find('#user_address').val(JSONObjectData.ADDRESS);
                    $(registerDiv).find('#user_city').val(JSONObjectData.CITY);
                    $(registerDiv).find('#user_postal_code').val(JSONObjectData.PIN_CODE);

                    $(registerDiv).find('#user_country').val(JSONObjectData.COUNTRY_ID);
                    $(registerDiv).find('#user_country').trigger("change");
                    $(registerDiv).find('#user_state').val(JSONObjectData.STATE_ID);
                  } catch (e) {
                    $(registerDiv).find('#email_div').html('');
                    $(registerDiv).find('input[type=text]').val('');
                    $(registerDiv).find("input[type=checkbox]").prop("checked", false);
                    $(registerDiv).find("input[type=checkbox]").prop("checked", false);
                    $(registerDiv).find('#user_country').val('');
                    $(registerDiv).find('#user_country').trigger("change");
                  }

                  $(registerDiv).find("#user_email_id").val(emailId);

                  var regClassIdVal = $.trim($(registerDiv).find("#regClassId").val());


                } else if (JSONObject.STATUS == 'FACULTY') {
                  var JSONObjectData = JSONObject.DATA;
                  $('#abstractDelegateId').val(JSONObject.ID);
                  $('#registration_operation').removeAttr('disabled');
                  $('#checkUserEmail').hide();

                } else {
                  var JSONObjectData = JSONObject.DATA;
                  $('#abstractDelegateId').val(JSONObject.ID);
                  $.ajax({
                    type: "POST",
                    url: jsBASE_URL + 'abstract.user.entrypoint.process.php',
                    data: 'act=triggerOTPSMS&id=' + JSONObject.ID,
                    dataType: 'text',
                    async: false,
                    success: function(dataObj) {
                      var registeredEmail = $("#registeredEmail").val();
                      toastr.error(registeredEmail, 'Error', {
                        "progressBar": true,
                        "timeOut": 3000,
                        "showMethod": "slideDown",
                        "hideMethod": "slideUp"
                      });
                      setTimeout(function() {
                        $('#registration_operation').removeAttr('disabled');
                        $('#login_operation').removeAttr('disabled');
                        $('#unique').show();
                        // $('#login_operation').show();
                        $('#login_operation').attr('id', 'login_final_operation');

                        $('#checkUserEmail').hide();

                        // window.location.href = jsBASE_URL + 'index.php';

                      }, 3000);
                    }
                  });

                  // var JSONObjectData = JSONObject.DATA;
                  // $('#abstractDelegateId').val(JSONObject.ID);

                  // $('#registration_operation').removeAttr('disabled');
                  // $('#login_operation').removeAttr('disabled');

                  //   console.log('new')
                }


              }
            });
          }, 500);
        } else {

          var invalidEmail = $("#invalidEmail").val();
          toastr.error(invalidEmail, 'Error', {
            "progressBar": true,
            "timeOut": 3000,
            "showMethod": "slideDown",
            "hideMethod": "slideUp"
          });
        }
      } else {

        var emptyEmail = $("#emptyEmail").val();
        toastr.error(emptyEmail, 'Error', {
          "progressBar": true,
          "timeOut": 3000,
          "showMethod": "slideDown",
          "hideMethod": "slideUp"
        });
      }


    });
  });

  function sendUrl() {
    var user_email_id = $('#user_email_id').val();
    var abstractDelegateId = $('#abstractDelegateId').val();
    //alert(user_email_id);
    localStorage.setItem("user_email_id", user_email_id);

    if (abstractDelegateId != '' && abstractDelegateId > 0) {
      window.location.href = '<?= _BASE_URL_ ?>registration.tariff.php?abstractDelegateId=' + abstractDelegateId;
    } else {
      // window.location.href='<?= _BASE_URL_ ?>registration.tariff.php';
    }



  }
            let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
</script>