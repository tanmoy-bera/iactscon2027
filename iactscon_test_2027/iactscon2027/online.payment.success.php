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

$cutoffs       = fullCutoffArray();
$currentCutoffId   = getTariffCutoffId();
$slipId         = $mycms->getSession('SLIP_ID');
$delegateId          = $mycms->getSession('LOGGED.USER.ID');
$userDetails      = getUserDetails($delegateId);

$title = 'Payment Success';
?>
<!DOCTYPE html>
<html>

<?php
setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();

include_once("header.php");


 $sqlLogo = array();
  $sqlLogo['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
      WHERE status = 'A'
      AND title = 'Registration Online Success Image'
      ORDER BY id DESC";

  $resultLogo = $mycms->sql_select($sqlLogo);
  $rowLogo    = $resultLogo[0];

  if($rowLogo['title']=='Registration Online Success Image'){
   $success_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowLogo['image'];
  }
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

      
      $result = str_replace($find, $replacement, $cfg['ONLINE_PAYMENT_SUCCESS_INFO']);
      
      ?>

    <div class="success_page_inner success">
        <img src="" alt="" class="success_page_bk">
        <div class="success_page_box">
            <img src="<?=$success_image?>" alt="">
            <h3>Registration Successfull</h3>
            <p> <? echo $result;?></p>
            <div class="success_btn_class">
              <!-- <a href="<?= _BASE_URL_ ?>downloadSlippdf.php?delegateId=<?= $mycms->encoded($resultInvoice[0]['delegate_id']) ?>&slipId=<?= $mycms->encoded($resultInvoice[0]['slip_id']) ?>"  target="blank" >Payment Voucher<i class="fal fa-download"></i></a> -->
              <a href="profile.php" class="note-btns" target="blank" >Visit Profile<?php view() ?></a>
            <div>
        </div>
    </div>
  
</body>
<?php include_once("includes/js-source.php"); ?>

</html>