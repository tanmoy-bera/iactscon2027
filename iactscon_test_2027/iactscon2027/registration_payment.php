<?php
session_start();
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
if (!isset($_SESSION['REG_PAYMENT_DATA'])) {
    // Redirect back if no data
    header("Location: index.php");
    exit;
}
$payment = $_SESSION['REG_PAYMENT_DATA'];
if($_REQUEST['slipId']==''){
  $_REQUEST['slipId'] = $payment['slipId'];
}
$totalSection = 7;

$title = 'Registration';

if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {
  $abstractDelegateId = trim($_REQUEST['abstractDelegateId']);
  $userRec = getUserDetails($abstractDelegateId);
} else {
  $mycms->removeAllSession();
  $mycms->removeSession('SLIP_ID');
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

//  echo '<pre>'; print_r($currentWorkshopCutoffId);die;


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
  <style>

  </style>
  <?php


  setTemplateStyleSheet();
  setTemplateBasicJS();
  backButtonOffJS();


  include_once("header.php");

  ?>
  <? if($payment['invoice_mode']=='OFFLINE'){
  ?>
 <form name="frmApplyPaymentOffline" id="frmApplyPaymentOffline" action="registration.process.php" method="post">
    <input type="hidden" id="slip_id_offline" name="slip_id" value="<?= $payment['slipId']; ?>"/>
    <input type="hidden" id="delegate_id_offline" name="delegate_id"  value="<?= $payment['delegateId']; ?>"/>
    <input type="hidden" id="user_email_id" name="user_email_id"  value="<?= $payment['user_email_id']; ?>"/>
    <input type="hidden" name="act" value="setPaymentTerms"  />
    <input type="hidden" name="mode" id="mode" />
    <div class="registration_wrap">
      <div class="registartion_head">
        <a href="index.php"><i class="fal fa-arrow-left"></i>Back</a>

        <p><span>Registered Email Id</span>
            <?= $payment['user_email_id']; ?>
          <script>

            function setRegistration(id) {
              $.ajax({
                url: 'registration.php',
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
      <div class="registration_inner">
       
          <!-- review -->
          <fieldset class="registration_right_wrap w-100">
            <?
            $user_email_id = addslashes(trim($payment['user_email_id']));
            $sqlFetchUser 					 = array();
            $sqlFetchUser['QUERY']          = "SELECT user.id AS delegateId,
                                                        slip.id AS slipId, 
                                                        user.registration_payment_status,
                                                        slip.registration_token
                                                    FROM " . _DB_SLIP_ . " slip
                                                LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " user
                                                        ON slip.delegate_id = user.id
                                                    WHERE user.user_email_id = ?
                                                    AND slip.status = ?
                                                    AND user.status = ?
                                                    AND slip.id = ?";

            $sqlFetchUser['PARAM'][]   = array('FILD' => 'user.user_email_id',  'DATA' => $user_email_id,  'TYP' => 's');
            $sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.status',         'DATA' => 'A',                              'TYP' => 's');
            $sqlFetchUser['PARAM'][]   = array('FILD' => 'user.status',         'DATA' => 'A',                              'TYP' => 's');
            $sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.id',         'DATA' =>$_REQUEST['slipId'],                              'TYP' => 's');

            $resultUser              = $mycms->sql_select($sqlFetchUser);
            // print_r($resultUser);
		    ?>
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
                     <?php
                        if ($resultUser) {
                            foreach ($resultUser as $rowUser) {

                                $mycms->setSession('LOGGED.USER.ID', $rowUser['delegateId']);
                                $mycms->setSession('IS_LOGIN', "YES");
                                $mycms->removeSession('OTP.ID');
                                $mycms->removeSession('TEMP.ID');
                                $mycms->removeSession('USER.EM');
                                $mycms->removeSession('USER.ID');
                                $mycms->removeSession('USER.PWD');
                                $mycms->removeSession('USER.TOKEN');

			                        	$resFetchSlip = slipDetailsOfUserSpecific($rowUser['delegateId'],'',$_REQUEST['slipId']);
                                $rowFetchSlip = $resFetchSlip[0];
                                $mycms->setSession('SLIP_ID', $rowFetchSlip['id']);

                                if ($currentCutoffId > 0 && $rowFetchSlip['invoice_mode'] == "OFFLINE") {

                                    $isSetPayment = getPaymentDetailsDelegate($rowUser['id']);
                                    if ($isSetPayment == '') {

                                        $delegateId = $rowUser['delegateId'];
                                        $slipId = $rowUser['slipId'];

                                        $slipDetails = slipDetails($slipId);
                                        $delegateDetails = getUserDetails($delegateId);
                                        $invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
                                        $pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
                                        $invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
                                        $totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);
                                        // $totalGst = 0;
                                        // $totalsubtotal = 0;
                                        // $internetCharges = 0;
                                       
                                        foreach ($invoiceDetailsArr as $invoiceDetails) {
                                            	$type			 = "";
                                          $totalGst +=$totalGst + $invoiceDetails['cgst_price']+$invoiceDetails['sgst_price'];
                                          $totalsubtotal +=$totalsubtotal + $invoiceDetails['service_basic_price'];
                                          $internetCharges +=$internetCharges + $invoiceDetails['internet_handling_amount'];
                                          $invoiceServiceType = $invoiceDetails['service_type'];
                                           
                                          if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
                                            $type =  "Registration<br>" . getRegClsfName(getUserClassificationId($delegateId, true), true) . "";
                                          }
                                          if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
                                            // $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
                                            $comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
                                            $type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
                                          }
                                          if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
                                            $workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
                                            // $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
                                            $type =  'Workshop<br>' . strtoupper(getWorkshopName($workShopDetails['workshop_id'], true) . '');
                                          }
                                          if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
                                            // $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
                                            $accompanyDetails = getUserDetails($reqId);
                                            if ($accompanyDetails['registration_request'] == 'GUEST') {
                                              $type  = "Accompanying Guest<br>  <u>" . $accompanyDetails['user_full_name'] . "</u>";
                                            } else {
                                              $type  = "Accompanying Person Registration<br>  <u>" . $accompanyDetails['user_full_name'] . "</u>";
                                            }
                                          }
                                          if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
                                          }
                                          if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
                                            $tourDetails = getTourDetails($invoiceDetails['refference_id']);

                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
                                          }
                                          if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
                                            $dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
                                            $dinnerRefId = $dinnerDetails['refference_id'];
                                            $dinner_user_type = dinnerForWhome($dinnerRefId);
                                            if ($dinner_user_type == 'ACCOMPANY') {
                                              $invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
                                              $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
                                            } else {
                                              $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
                                            }
                                          }
                                          $invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
                                          $invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
                                          $counter++;
                                          
                                    ?>
                            
                                      <li use=rowCloneable >
                                      <span><?php user() ?></span>
                                      <n use="invTitle">
                                      <h use="invName"><?= $type ?> - <?=$invoiceDetails['invoice_number']?></h>
                                      </n>
                                      <k use="amount">₹ <?= number_format(discountAmount($invoiceDetails['id'])['TOTAL_AMOUNT'], 2) ?></k>
                                  </li>
                                <?php
                                
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>

                      <li use="subtotalRow">
                        <div class="w-100">
                          <p class="frm-head d-flex justify-content-between align-items-center">Subtotal<k use="subtotalAmount">₹ <?=number_format($totalsubtotal, 2)?></k>
                          </p>
                          <? 
                          if($cfg['GST.FLAG']!=3){
                            ?>
                          <p class="frm-head d-flex justify-content-between align-items-center">GST (18%)<k use="totalGstAmount">₹ <?=number_format($totalGst, 2)?></k>
                          </p>
                          <? } ?>
                        </div>
                      </li>
                      <li>
                        <div class="w-100" use="totalAmount">
                          <h5 class="frm-head d-flex justify-content-between align-items-center mb-0">Total Payable<k use="totalAmountpay" style="color: var(--sky);">₹ <?=number_format($pendingAmountOfSlip, 2)?></k>
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

                      <!-- <?php if (in_array("Card", $offline_payments)) { ?>
                      <button type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Cards
                      </button>
                      <?php } ?> -->

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
                      <!-- <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" hidden> -->
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
                          <div >
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Beneficiary Name<span class="text-white"><?= $cfg['INVOICE_BENEFECIARY'] ?></span></p>
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
                          <input class="for-upi-only"   type="text" class="form-control mandatory utrnft" name="txn_no" id="txn_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
                        </li>
                         <?php } ?>
                         <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li class="for-neft-rtgs-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UTR Number</h6>
                          <input type="text" class="form-control mandatory utrnft" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction id" placeholder="Enter Transaction Id">
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
            <div class="registration_right_bottom justify-content-end">
              <!-- <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button> -->
              <button type="submit" name="submit" class="submit action-button">Confirm<?php check(); ?></button>
            </div>
          </fieldset>
          <!-- review -->
        </div>
      </div>
    </div>
    <?php include_once('cart.php'); ?>

  </form>
  <? }else{
    ?>
 <form name="frmApplyPaymentOffline" id="frmApplyPaymentOffline" action="registration.process.php" method="post">
    <input type="hidden" id="slip_id_offline" name="slip_id" value="<?= $payment['slipId']; ?>"/>
    <input type="hidden" id="delegate_id_offline" name="delegate_id"  value="<?= $payment['delegateId']; ?>"/>
    <input type="hidden" id="user_email_id" name="user_email_id"  value="<?= $payment['user_email_id']; ?>"/>
    <input type="hidden" name="act" value="paymentSet"  />
    <input type="hidden" name="mode" id="mode" />
    <input type="radio" name="card_mode" use="card_mode_select" value="Indian" checked style="visibility: hidden;">
    <div class="registration_wrap">
      <div class="registartion_head">
        <a href="index.php"><i class="fal fa-arrow-left"></i>Back</a>

        <p><span>Registered Email Id</span>
            <?= $payment['user_email_id']; ?>
          <script>

            function setRegistration(id) {
              $.ajax({
                url: 'registration.php',
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
      <div class="registration_inner">
       
          <!-- review -->
          <fieldset class="registration_right_wrap w-100">
            <?
            $user_email_id = addslashes(trim($payment['user_email_id']));
            $sqlFetchUser 					 = array();
            $sqlFetchUser['QUERY']          = "SELECT user.id AS delegateId,
                                                        slip.id AS slipId, 
                                                        user.registration_payment_status,
                                                        slip.registration_token
                                                    FROM " . _DB_SLIP_ . " slip
                                                LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " user
                                                        ON slip.delegate_id = user.id
                                                    WHERE user.user_email_id = ?
                                                    AND slip.status = ?
                                                    AND user.status = ?
                                                     AND slip.id = ?";

            $sqlFetchUser['PARAM'][]   = array('FILD' => 'user.user_email_id',  'DATA' => $user_email_id,  'TYP' => 's');
            $sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.status',         'DATA' => 'A',                              'TYP' => 's');
            $sqlFetchUser['PARAM'][]   = array('FILD' => 'user.status',         'DATA' => 'A',                              'TYP' => 's');
            $sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.id',         'DATA' =>$_REQUEST['slipId'],                              'TYP' => 's');

            $resultUser              = $mycms->sql_select($sqlFetchUser);
             
		    ?>
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
                     <?php
                        if ($resultUser) {
                            foreach ($resultUser as $rowUser) {

                                $mycms->setSession('LOGGED.USER.ID', $rowUser['delegateId']);
                                $mycms->setSession('IS_LOGIN', "YES");
                                $mycms->removeSession('OTP.ID');
                                $mycms->removeSession('TEMP.ID');
                                $mycms->removeSession('USER.EM');
                                $mycms->removeSession('USER.ID');
                                $mycms->removeSession('USER.PWD');
                                $mycms->removeSession('USER.TOKEN');

                               	$resFetchSlip = slipDetailsOfUserSpecific($rowUser['delegateId'],'',$_REQUEST['slipId']);

                                
                                $rowFetchSlip = $resFetchSlip[0];
                                $mycms->setSession('SLIP_ID', $rowFetchSlip['id']);

                                if ($currentCutoffId > 0 && $rowFetchSlip['invoice_mode'] == "ONLINE") {

                                    $isSetPayment = getPaymentDetailsDelegate($rowUser['id']);
                                    if ($isSetPayment == '') {

                                        $delegateId = $rowUser['delegateId'];
                                        $slipId = $rowUser['slipId'];

                                        $slipDetails = slipDetails($slipId);
                                        $delegateDetails = getUserDetails($delegateId);
                                        $invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
                                        $pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
                                        $invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
                                        $totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);
                                        // $totalGst = 0;
                                        // $totalsubtotal = 0;
                                        // $internetCharges = 0;
                                        
                                        foreach ($invoiceDetailsArr as $invoiceDetails) {
                                            	$type			 = "";
                                          $totalGst +=$totalGst + $invoiceDetails['cgst_price']+$invoiceDetails['sgst_price'];
                                          $totalsubtotal +=$totalsubtotal + $invoiceDetails['service_basic_price'];
                                          $internetCharges +=$internetCharges + $invoiceDetails['internet_handling_amount'];
                                          $invoiceServiceType = $invoiceDetails['service_type'];
                                          if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
                                          }
                                          if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
                                            // $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
                                            $comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
                                            $type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
                                          }
                                          if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
                                            $workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
                                          }
                                          if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
                                          }
                                          if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
                                          }
                                          if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
                                            $tourDetails = getTourDetails($invoiceDetails['refference_id']);

                                            $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
                                          }
                                          if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
                                            $dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
                                            $dinnerRefId = $dinnerDetails['refference_id'];
                                            $dinner_user_type = dinnerForWhome($dinnerRefId);
                                            if ($dinner_user_type == 'ACCOMPANY') {
                                              $invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
                                              $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
                                            } else {
                                              $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
                                            }
                                          }
                                          $invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
                                          $invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
                                          $counter++;
                                      ?>
                              
                                        <li use=rowCloneable >
                                        <span><?php user() ?></span>
                                        <n use="invTitle">
                                        <h use="invName"><?=$invoiceDetails['invoice_number']?>-<?= $type ?></h>
                                        </n>
                                        <k use="amount">₹ <?= number_format(discountAmount($invoiceDetails['id'])['TOTAL_AMOUNT'], 2) ?></k>
                                    </li>
                                <?php
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>

                      <li use="subtotalRow">
                        <div class="w-100">
                          <p class="frm-head d-flex justify-content-between align-items-center">Subtotal<k use="subtotalAmount">₹ <?=number_format($totalsubtotal, 2)?></k>
                          </p>
                          <? 
                          if($cfg['GST.FLAG']!=3){
                            ?>
                          <p class="frm-head d-flex justify-content-between align-items-center">GST (18%)<k use="totalGstAmount">₹ <?=number_format($totalGst, 2)?></k>
                          </p>
                          <? } ?>
                          <p class="frm-head d-flex justify-content-between align-items-center">Internet Handling Charges<k use="totalGstAmount">₹ <?=number_format($internetCharges, 2)?></k>
                          </p>
                        </div>
                      </li>
                      <li>
                        <div class="w-100" use="totalAmount">
                          <h5 class="frm-head d-flex justify-content-between align-items-center mb-0">Total Payable<k use="totalAmountpay" style="color: var(--sky);">₹ <?=number_format($pendingAmountOfSlip, 2)?></k>
                          </h5>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="review_right">
                  <div class="review_tab">
                    <div class="hotel_link_owl owl-carousel owl-theme">
                     <input type="hidden" name="registrationMode" id="registrationMode">

                    
                     <?php if (in_array("Card", $offline_payments)) { ?>
                      <button class="active" type="button"
                        onclick="
                          $('#banktransfer').hide();$('#upi').hide();$('#Cards').show();$('#DD').hide();$('#cash').hide();
                          $('.review_tab button').removeClass('active');$(this).addClass('active');
                          $('input[type=radio][use=payment_mode_select][value=Card]').prop('checked',true).trigger('click');
                        ">
                        <?php qr(); ?> Razorpay
                      </button>
                      <?php } ?> 

                      <!-- Hidden radios (logic only – REQUIRED) -->
                     
                      <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" hidden>
                      
                    </div>
                   </div>
                    <div class="review_right_box" id="banktransfer" style="display: block;">
                      <ul>
                        
                        <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li>
                          <h6 class="d-flex justify-content-between align-items-center">Transfer via Net Banking or NEFT/IMPS.</h6>
                          <div>
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
                          <input type="date" class="form-control mandatory" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                        </li>
                         <?php
                          if (in_array("Neft", $offline_payments)) {
                          ?>
                        <li class="for-neft-rtgs-only"  style="display: none;" >
                          <h6 class="d-flex justify-content-between align-items-center">UTR Number</h6>
                          <input type="text" class="form-control mandatory utrnft" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">
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
            <div class="registration_right_bottom justify-content-end">
              <!-- <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button> -->
              <button type="submit" name="submit" class="submit action-button">Confirm<?php check(); ?></button>
            </div>
          </fieldset>
          <!-- review -->
        </div>
      </div>
    </div>
    <?php include_once('cart.php'); ?>

  </form>
    <?
  }
  ?>
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
  // Object to store selected dates per hotel
  var hotelDates = {};

  $(document).ready(function() {
    $('.hotel_tab_btn').click(function() {
      var hotelId = $(this).data('tab');
      var hotelName = $(this).text(); 

      // Save current hotel's dates before switching
      var currentHotelId = $('#hotel_id').val();
      if (currentHotelId) {
        hotelDates[currentHotelId] = {
          checkin: $('#accomodation_package_checkin_id_' + currentHotelId).val(),
          checkout: $('#accomodation_package_checkout_id_' + currentHotelId).val()
        };
      }

      // Load new hotel's content
      $.ajax({
        url: 'registration.php',
        type: 'POST',
        data: { hotelIdDate: hotelId },
        success: function(response) {
          $('#accoOp').html($(response).find('#accoOp').html());
          initializeAccoPlugins();

          // Update hidden hotel id
          $('#hotel_id').val(hotelId);

          // Restore previously selected dates if available
          if (hotelDates[hotelId]) {
            $('#accomodation_package_checkin_id_' + hotelId).val(hotelDates[hotelId].checkin);
            $('#accomodation_package_checkout_id_' + hotelId).val(hotelDates[hotelId].checkout);
          } else {
            $('#accomodation_package_checkin_id_' + hotelId).val('');
            $('#accomodation_package_checkout_id_' + hotelId).val('');
          }
          
        }
      });
    });

    // Trigger first tab on page load
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
  function initializeAccoPlugins() {
    $(document).on('change', "input[type=checkbox][name='package_id[]']", function() {
      var el = $(this);
      var hotelId = el.data('hotel-id'); // get hotel id for this checkbox

      var checkInVal = $('#accomodation_package_checkin_id_' + hotelId).val();
      var checkOutVal = $('#accomodation_package_checkout_id_' + hotelId).val();

      if (!checkInVal || !checkOutVal) {
        if (!$(".toast:contains('Please select check-in and check-out dates')").length) {
          toastr.error('Please select check-in and check-out dates', 'Error', { progressBar: true, timeOut: 3000 });
        }
        el.prop('checked', false);
        return;
      }

      var pkgName = el.closest('label').find('n').text().trim().toLowerCase(); // get package name
      var checkedAll = $("input[type=checkbox][name='package_id[]']:checked");

      if (pkgName === 'individual') {
        var checkedIndividual = checkedAll.filter(function() {
          return $(this).closest('label').find('n').text().trim().toLowerCase() === 'individual';
        });

        if (checkedIndividual.length > 3) {
          toastr.error('Maximum 3 Individual packages allowed across all hotels', 'Limit reached');
          el.prop('checked', false);
          return;
        }
      } else {
        $("input[type=checkbox][name='package_id[]']").not(el).prop('checked', false);
      }

      console.log('Selected package:', {
        hotel_id: el.attr("hotel_id"),
        hotel_select_acco_id: el.attr("hotel_select_acco_id"),
        accommodation_room: el.attr("accommodation_room")
      });
      var subtotal = $(this).attr('amount');
      $("#hotel_id").val(el.attr("hotel_id"));
      $("#hotel_select_acco_id").val(el.attr("hotel_select_acco_id"));
      $("#accommodation_room").val(el.attr("accommodation_room"));

      calculateTotalAmount();
    });
    $(document).on('click', '.qty-count', function () {
      var btn = $(this);
      var input = btn.siblings('.accmomdation-qty');

      var currentVal = parseInt(input.val()) || 1;
      var min = parseInt(input.attr('min')) || 1;
      var max = parseInt(input.attr('max')) || 10;

      // if (btn.data('action') === 'add' && currentVal < max) {
      //   input.val(currentVal + 1);
      // }

      // if (btn.data('action') === 'minus' && currentVal > min) {
      //   input.val(currentVal - 1);
      // }

      // 🔥 Trigger recalculation using existing checkout logic
      var hotelId = $('#hotel_id').val();
      var checkoutVal = $('#accomodation_package_checkout_id_' + hotelId).val();

      if (checkoutVal) {
        get_checkout_val(checkoutVal, hotelId);
      }
    });
    // Clear button handler
    $(document).on('click', '.registration_right_body_head_right .text_danger', function(e) {
      e.preventDefault();

      var hotelId = $(this).closest('.registration_right_body_head_right').find('select[id^="accomodation_package_checkin_id_"]').data('hotel-id');

      $("input[type=checkbox][name='package_id[]'][data-hotel-id='" + hotelId + "']").prop('checked', false);

      $("input[name='package_id[]'][data-hotel-id='" + hotelId + "']").each(function() {
        var baseAmount = parseFloat($(this).data('base-amount')) || 0;
        $(this).attr('amount', baseAmount);
        $(this).closest('label').find('.package-price').text('₹ ' + baseAmount.toFixed(2));
      });

      $('#accommodation_room').val(1);
      $('#accomodation_package_checkin_id_' + hotelId).val('');
      $('#accomodation_package_checkout_id_' + hotelId).val('');

      calculateTotalAmount();
    });
    window.get_checkin_val = function(val, hotelId) {
      if (!val) return;
      $('#accomodation_package_checkout_id_' + hotelId).val('');
        syncHotelDates(hotelId);
    };

    window.get_checkout_val = function(val, hotelId) {
      var checkInVal = $('#accomodation_package_checkin_id_' + hotelId).val();
      if (!checkInVal || !val) return;

      const checkInDate = checkInVal.split("/")[1];
      const checkOutDate = val.split("/")[1];

      var date1 = new Date(checkInDate);
      var date2 = new Date(checkOutDate);

      if (date1 >= date2) {
        toastr.error('Please select proper checkout date!', 'Error', { progressBar: true, timeOut: 4000 });
        $('#accomodation_package_checkout_id_' + hotelId).val('');
        return false;
      }

      var differenceDays = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));
      // var roomQty = 1;
      
      $("input[name='package_id[]']").each(function() {

        var baseAmount = parseFloat($(this).data('base-amount')) || 0;

        var roomId = $(this).attr('accommodation_room');
        var roomQty = parseInt(
          $("input[name='accmomdation-qty[" + hotelId + "][" + roomId + "]']").val()
        ) || 1;

        var nightTotal = baseAmount * differenceDays * roomQty;

        $(this).attr('amount', nightTotal.toFixed(2));
        $(this).closest('label').find('.package-price')
          .text('₹ ' + nightTotal.toFixed(2));
      });
       syncHotelDates(hotelId);

       calculateTotalAmount();
    };
  }
  document.addEventListener("DOMContentLoaded", initializeAccoPlugins);

  $(document).ready(function() {
    var storageEmail = localStorage.getItem("user_email_id");
    // var storageMobile = localStorage.getItem("user_mobile");
    if (storageEmail != '' && storageEmail !== undefined) {
      $('#user_email_id').val(storageEmail);
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
          // return false; // ⛔ stop Continue
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
        // if (!current_fs.find("input[name='user_food_choice']:checked").length) {
        //   toastr.error('Please select a food preference', 'Error', {
        //     progressBar: true,
        //     timeOut: 3000,
        //     showMethod: "slideDown",
        //     hideMethod: "slideUp"
        //   });

        //   isValid = false;
        //   // return false; // ⛔ stop Continue
        // }

      }
      if (current_fs.hasClass("accompanyfieldset")) {
        // Loop through each guest row
        // console.log(("#accompanyingTableBody li").length);
        $("#accompanyingTableBody li").each(function() {
          $(".accompanyCount").prop("checked", true);

          calculateTotalAmount();

        });

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

    $(document).ready(function() {
      const $mobile = $("#user_mobile");

      // Trigger the existing Go button logic automatically when user leaves the field
      $mobile.on('blur', function() {
        $("#goBtn").trigger('click'); // calls checkUserEmail(this)
      });

      // Optional: Trigger after typing stops for 500ms (debounce)
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
    var QtyInput = (function() {
        var $qtyInputs = $(".accomdationroomqty-input");

        if (!$qtyInputs.length) {
            return;
        }

        var $inputs = $qtyInputs.find(".accmomdation-qty");
        var $countBtn = $qtyInputs.find(".qty-count");
        var qtyMin = parseInt($inputs.attr("min"));
        var qtyMax = parseInt($inputs.attr("max"));

        $inputs.change(function() {
            var $this = $(this);
            var $minusBtn = $this.siblings(".qty-count--minus");
            var $addBtn = $this.siblings(".qty-count--add");
            var qty = parseInt($this.val());

            if (isNaN(qty) || qty <= qtyMin) {
                $this.val(qtyMin);
                $minusBtn.attr("disabled", true);
            } else {
                $minusBtn.attr("disabled", false);

                if (qty >= qtyMax) {
                    $this.val(qtyMax);
                    $addBtn.attr('disabled', true);
                } else {
                    $this.val(qty);
                    $addBtn.attr('disabled', false);
                }
            }
        });

        $countBtn.click(function() {
            var operator = this.dataset.action;
            var $this = $(this);
            var $input = $this.siblings(".accmomdation-qty");
            var qty = parseInt($input.val());

            if (operator == "add") {
                qty += 1;
                if (qty >= qtyMin + 1) {
                    $this.siblings(".qty-count--minus").attr("disabled", false);
                }

                if (qty >= qtyMax) {
                    $this.attr("disabled", true);
                }
            } else {
                qty = qty <= qtyMin ? qtyMin : (qty -= 1);

                if (qty == qtyMin) {
                    $this.attr("disabled", true);
                }

                if (qty < qtyMax) {
                    $this.siblings(".qty-count--add").attr("disabled", false);
                }
            }

            $input.val(qty);
        });
    })();
</script>
<script>
$(document).ready(function() {
    // When a payment radio is clicked
    $("input[type=radio][use=payment_mode_select]").click(function() {
        var val = $(this).val(); // Get selected payment value

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
</script>
</html>