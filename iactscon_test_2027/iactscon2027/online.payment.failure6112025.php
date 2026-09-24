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

$slipId   = $mycms->getSession('SLIP_ID');

$sqlSlip            = array();
$sqlSlip['QUERY']   = "SELECT * FROM " . _DB_SLIP_ . " 
						WHERE `status` =? 
						AND `id` =?";
$sqlSlip['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',      'TYP' => 's');
$sqlSlip['PARAM'][]  = array('FILD' => 'id',     'DATA' => $slipId,  'TYP' => 's');

$resSlip      = $mycms->sql_select($sqlSlip);
$rowSlip      = $resSlip[0];

$userDetails    = getUserDetails($rowSlip['delegate_id']);

$title = 'Payment Failure';

$sqlRegfailureImg    =   array();
$sqlRegfailureImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE title='Registration Failure Image' ";

$resultRegfailureImg      = $mycms->sql_select($sqlRegfailureImg);
$resultRegfailureImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultRegfailureImg[0]['image']; ?>


<!DOCTYPE html>
<html>

<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();

include_once("header.php");


?>


<body class="single inner-page">


  <?php //include_once('sidebar_icon.php'); 
  ?> <!--//paymentVoucherModal -->

  <div class="body-frm">
    <section class="main-section note-section">
      <div class="col-12">
        <div class="note-icon">
          <div class="note-icon-mx">
            <img src="<?= $resultRegfailureImg ?>" alt="" />
            <h2><?= $cfg['REGISTRATION.FAILURE.IMAGE.TEXT'] ?></h2>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="note-details">
          <!-- <h1>OOPS !</h1>

                <p>Dear <?= $userDetails['user_full_name'] ?>,</p>

                <p>TRANSACTION <span>UNSUCCESSFUL.</span></p>

                <p>The payment procedure for <?= $cfg['EMAIL_CONF_NAME'] ?> registration has failed.</p> -->

          <?php

          $find = ['[USER]', '[CONF_NAME]'];
          $replacement = [$userDetails['user_full_name'], $cfg['EMAIL_CONF_NAME']];
          // $find = ['[USER]', '[AMOUNT]', '[PAYMENT_MODE]','[PAYPENT_DESCRIPTION]'];
          // $replacement = [$userDetails['user_full_name'],$totalRoundPrc,$paymentMode,$paymentDescription];
          echo str_replace($find, $replacement, $cfg['PAYMENT_FAILURE_INFO']);
          ?>

          <div class="d-flex">
            <a class="note-btns-fails" onclick="getPaymentVouchar('<?= $userDetails['user_email_id'] ?>')" style="cursor: pointer;"><img src="<?= _BASE_URL_ ?>images/loader.png" /> RETRY</a>


            <!-- <a href="<?= _BASE_URL_ ?>profile.php" class="note-btns"><img src="<?= _BASE_URL_ ?>images/arrow5.png" /> Visit
                    Profile</a> -->
          </div>

        </div>
      </div>

    </section>
  </div>
  <?php include_once('footer.php'); ?>


</body>

<!-- css for checkout -->
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

  .order-id {
    margin: 0;
    width: 179px;
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
            <a class="close-check close-button" style="cursor: pointer;"><b>Close</b></a>
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

</html>




<script type="text/javascript">
  function getPaymentVouchar(user_email_id) {
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

            $('#orderSummeryInvoices').html(JSONObject.orderSummeryInvoices);
            $('#order_details').html(JSONObject.data);

            $('#slip_id').val(JSONObject.slipId);
            $('#delegate_id').val(JSONObject.delegateId);
            console.log(JSONObject.slipId + " , " + JSONObject.delegateId);
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
              // alert(JSONObject.delegateId);
              $('#paymentVoucherModal').show();
              $('#loginBtn').prop('disabled', false);
              //window.location.href= jsBASE_URL + 'profile.php';

            }, 2000);
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
</script>