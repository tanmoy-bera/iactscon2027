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
include_once("includes/source.php");

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


<body class="success_page">
    <?php

      $sqlInvoice   = array();
      $sqlInvoice['QUERY']   = "SELECT *                        
        FROM " . _DB_INVOICE_ . " 
          WHERE slip_id =?
          AND status = ?";

      $sqlInvoice['PARAM'][]  = array('FILD' => 'slip_id', 'DATA' =>$slipId,  'TYP' => 's');
      $sqlInvoice['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',                                 'TYP' => 's');

      $resultInvoice = $mycms->sql_select($sqlInvoice);
    
      $totalRoundPrc = 0;
      $paymentStatus = '';
    
      foreach ($resultInvoice as $key => $rowInvoice) {


        $serviceType = $rowInvoice['service_type'];
        $invoice_mode = $rowInvoice['invoice_mode'];
        $totalRoundPrc += $rowInvoice['service_roundoff_price'];
        $paymentStatus = $rowInvoice['payment_status'];
        $delegateId = $rowInvoice['delegate_id'];
      }
      $find = ['[USER]', '[AMOUNT]','[CONF_NAME]', '[UNIQUE_ID]', '[REG_ID]'];

      $replacement = [$userDetails['user_full_name'], $totalRoundPrc, $cfg['EMAIL_CONF_NAME'], $userDetails['user_unique_sequence'], $userDetails['user_registration_id']];

  
      $result = str_replace($find, $replacement, $cfg['PAYMENT_FAILURE_INFO']);
      online_conference_payment_failure_message($delegateId, 'SEND');

      ?>
    <div class="success_page_inner fail">
        <img src="images/regifail.png" alt="" class="success_page_bk">
        <div class="success_page_box">
            <img src="<?= $resultRegfailureImg ?>" alt="">
            <h3>Registration Failed!</h3>
            <p><? echo $result;?></p>
            <div class="success_btn_class">

              <a onclick="getPaymentVouchar('<?= $userDetails['user_email_id'] ?>')" style="cursor: pointer;">Retry Payment<?php reseti() ?></a>
             </div>
          </div>
    </div>
  
</body>
<?php include_once("includes/js-source.php"); ?>

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
   var slipId = <?= $slipId ?>;
    if (flag == 0) {
      $.ajax({
        type: "POST",
        url: jsBASE_URL + 'login.process.php',
        data: 'action=getPaymentVoucharDetails&user_email_id=' + user_email_id +'&slipId='+slipId,
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
            
              toastr.success('Redirecting to payment page...', 'Success', {
                  progressBar: true,
                  timeOut: 1500
              });

              setTimeout(function() {
                  window.location.href = '<?= _BASE_URL_ ?>registration_payment.php?slipId=<?= $slipId ?>';
              }, 1500);
            // $('#user_email_id').val("");
            // $('#loading_indicator').show();
            // $('#payModal').hide();

            // $('#orderSummeryInvoices').html(JSONObject.orderSummeryInvoices);
            // $('#order_details').html(JSONObject.data);

            // $('#slip_id').val(JSONObject.slipId);
            // $('#delegate_id').val(JSONObject.delegateId);
            // console.log(JSONObject.slipId + " , " + JSONObject.delegateId);
            // $('#mode').val(JSONObject.invoice_mode);

            // $('#loginBtn').prop('disabled', true);

            // toastr.error(JSONObject.msg, 'Success', {
            //   "progressBar": true,
            //   "timeOut": 2000,
            //   "showMethod": "slideDown",
            //   "hideMethod": "slideUp"
            // });
            // setTimeout(function() {
            //   $('#loading_indicator').hide();
            //   // alert(JSONObject.delegateId);
            //   $('#paymentVoucherModal').show();
            //   $('#loginBtn').prop('disabled', false);
            //   //window.location.href= jsBASE_URL + 'profile.php';

            // }, 2000);
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