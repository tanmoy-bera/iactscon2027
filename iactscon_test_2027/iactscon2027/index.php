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

$dateArr          = explode("-",$rescutoff[0]['end_date']);
$dateCount = new DateTime($rescutoff[0]['end_date']);

$sqlFlyer  = array();
$sqlFlyer['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
            WHERE `id`!='' AND `title`='Flyer' AND status IN ('A', 'I')";

$resultFlyer    = $mycms->sql_select($sqlFlyer);

//echo '<pre>'; print_r($resultFlyer);
$sqlMSG   =  array();
$sqlMSG['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
			WHERE `id` = 1";
$result       = $mycms->sql_select($sqlMSG);
$companyInfo =  $result[0];
$dateArrAbs          = explode("-", $result[0]['abstract_submission_date']);
$dateCountAbs = new DateTime($result[0]['abstract_submission_date']);

$startD = $companyInfo['conf_start_date'];
$endD = $companyInfo['conf_end_date'];
$startDate = new DateTime($startD);
$endDate = new DateTime($endD);
$formatted = $startDate->format("M d")."-". $endDate->format("M d");
// $dayMonth = $startDate->format("d/m/");
//  echo $dayMonth;
// print_r($dayMonth);
// die();
$row         = $result[0];
$invalidEmail = $row['notification_invalid_email'];
$registeredEmail = $row['notification_registered_email'];
$emptyEmail = $row['notification_empty_email'];
$unpaid_offline = $row['notification_unpaid_offline'];
$unpaid_online = $row['notification_unpaid_online'];

$find = ['[CONF NAME]', '[PHONE NUMBER]'];
$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
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
<script language="javascript">
			var jsBASE_URL	= "<?=_BASE_URL_?>";
			var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
		</script>

<body>
<img src="<?=$cfg['OUTER_BG_IMG']?>" alt="" class="body_bk">
    <div class="container index_box position-relative">
        <div class="row">
            <div class="login_head d-none">
              
                <h6><span><?=$formatted?></span></h6>
                <h2><?=$companyInfo['company_conf_name']?></h2>
                <p><?=$companyInfo['invoice_address']?></p>
            </div>
            <div class="login_wrap">
                <div class="login_left">
                    <h3 class="con_logo"><img src="<?= $emailHeader ?>" alt=""></h3>
                    <h2><?=$companyInfo['header_text']?></h2>
                    <p><?=$companyInfo['sub_header_text']?></p>
                    <ol>
                        <li>
                            <span><?php address(); ?></span>
                            <h6>
                                <n>venue</n>
                                <g><?=$companyInfo['company_conf_venue']?></g>
                            </h6>
                        </li>
                        <hr>
                        <li>
                            <span><?php calendar(); ?></span>
                            <h6>
                                <n>date</n>
                                <g><?=$formatted?></g>
                            </h6>
                        </li>
                    </ol>
                    
   
                    <ul id="registration_countdown">
                      <? $currentCutoffId  = getCutoffName(getTariffCutoffId()); ?>
                          <h4><?=$currentCutoffId?> Registration Ends In</j></h4>
                        <li>
                            <n><span id="days">00</span></n><i>days</i>
                        </li>
                        <hr>
                        <li>
                            <n><span id="hours">00</span></n><i>Hours</i>
                        </li>
                        <hr>
                        <li>
                            <n><span id="minutes">00</span></n><i>Minutes</i>
                        </li>
                        <hr>
                        <li>
                            <n><span id="seconds">00</span></n><i>Seconds</i>
                        </li>
                    </ul>
                    <h3 id="registration_closed" style="display: none;">Registration Closed</h3>
                </div>
                <div class="login_right">
                    <h4>Delegate Access</h4>
                    <p>Secure your spot or manage your submission</p>
                    <div class="login_frm_wrap">
                        <label>Email Address</label>
                        <div>
                            <!-- <?php email(); ?> -->
                            <?php
                            $sql   =  array();
                            $sql['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
                                            WHERE `id` = 1";
                            $result       = $mycms->sql_select($sql);
                            $row         = $result[0];
                            $invalidEmail = $row['notification_invalid_email'];
                            $registeredEmail = $row['notification_registered_email'];
                            $emptyEmail = $row['notification_empty_email'];

                            $sqlRegClasf      = array();
                            $sqlRegClasf['QUERY']  = "SELECT * FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " WHERE status = 'A' ";

                            $resRegClasf       = $mycms->sql_select($sqlRegClasf);
                            ?>
                            <input type="hidden" id="isCombo" value="<?= empty($resRegClasf) ? 'N' : 'Y' ?>">
                            <input type="hidden" id="invalidEmail" value="<?= $invalidEmail ?>">
                            <input type="hidden" id="registeredEmail" value="<?= $registeredEmail ?>">
                            <input type="hidden" id="emptyEmail" value="<?= $emptyEmail ?>">
                            <input type="email" name="user_email_id" id="user_email_id" placeholder="Please Enter Email Address">
                            <button type="button" id="checkUserEmail"><i class="fal fa-long-arrow-right"></i></button>
                        </div>
                    </div>
                    <div class="login_frm_wrap"  id="unique" style="display:none;">
                        <label>Code</label>
                        <div>
                            <i class="fal fa-lock"></i>
                            <input placeholder="Uniq Code"  type="text" name="user_unique_sequence" id="user_unique_sequence" value="#" placeholder="Ex: #00000000" onkeypress="return isNumber(event)">
                            <button id="toggleBtn"><i class="fal fa-eye"  id="toggleIcon"></i></button>
                        </div>
                    </div>
                    <input type="hidden" name="abstractDelegateId" id="abstractDelegateId">
                    <div class="login_btn_wrap login-buttons">
                      <? if($row['registration_disable']=='yes'){?>
                          <a href="javascript:void(0)" class="login-btns disabled-btn"  style="cursor: pointer;" id="registration_operation_disabled" disabled="disabled"><i class="fal fa-user-plus"></i>Register</a>

                       <? } else{
                        ?> 
                               <a href="javascript:void(0)" class="login-btns disabled-btn" onclick="sendUrl()" style="cursor: pointer;" id="registration_operation" disabled="disabled"><i class="fal fa-user-plus"></i>Register</a>
                         <? } ?>
                        <a href="javascript:void(0)"  class="login-btns disabled-btn" id="login_operation" style="cursor: pointer;" disabled="disabled" ><i class="fal fa-sign-in"></i>Login</a>
                        <? if($row['registration_disable']=='yes'){?>
                                <a href="javascript:void(0)"  class="login-btns disabled-btn" id="pay-button-modal_disabled" style="cursor: pointer;"  disabled="disabled" ><i class="fal fa-sign-in"></i>Pay Now</a>

                        <? } else{
                        ?> 
                        <a href="javascript:void(0)"  class="login-btns disabled-btn" id="pay-button-modal" style="cursor: pointer;"  disabled="disabled" ><i class="fal fa-sign-in"></i>Pay Now</a>
                         <? } ?>
                    </div>
                    <?php
                      $sqlFooterIcon  = array();
                      $sqlFooterIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                              WHERE `status`='A' AND purpose='Footer' AND title='Phone' order by id ";
                      $resultFooterIcon = $mycms->sql_select($sqlFooterIcon);

                      $currentDate = date("Y-m-d");
                      $submissionDate = date("Y-m-d", strtotime($companyInfo['abstract_submission_date']));
                      $abstract_start_date = date("Y-m-d", strtotime($companyInfo['abstract_start_date']));

                      if ($submissionDate >= $currentDate && $abstract_start_date <= $currentDate) {
                      ?>
                     <a onclick="sendUrlabstract()" class="abstractbtn disabled-btn" id="abstractbtn" ><i class="fal fa-book-open"></i>Submit Your Abstract</a>
                     <? } ?>
                    <a  href="http://wa.me/+917596071511"  target="_blank"   class="need_help">Need help? Contact Support</a>
                </div>
            </div>
            <?
            if ($submissionDate >= $currentDate && $abstract_start_date <= $currentDate) {
                      ?>
             <div class="abstract_sec">
                <div class="abstratc_wrap">
                    <div class="abstratc_left">
                       <?  $sqlParticipantDate  = array();
                        $sqlParticipantDate['QUERY'] = "SELECT `conf_date` FROM "._DB_PROGRAM_SCHEDULE_DATE_." 
                                              WHERE `status`='A' order by conf_date ";
                                  //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
                        $resultsParticipantDate = $mycms->sql_select($sqlParticipantDate); 
                        ?>
                        <?
                          if ($resultsParticipantDate[0]['conf_date']) {
                        ?>
                        <span>Scientific Program</span>
                        <? } ?>
                        <? if($companyInfo['abstract_guideline_website']!=''){ ?>
                         <span><a target="_blank" href="<?=$companyInfo['abstract_guideline_website']?>">Abstract Guideline</a></span>

                        <? } ?>
                        <!-- <h3>Call for Abstracts</h3>
                        <p>Share your research with the global pulmonary community. We invite submissions for Oral Presentations and E-Posters across various categories including TB, COPD, and Critical Care.
                        </p>
                        <ul>
                            <li>Oral Presentations</li>
                            <li>E-Posters</li>
                            <li>Awards & Recognition</li>
                        </ul> -->
                        <?= $companyInfo['abstract_header_text']?>
                    </div>
                    <div class="abstract_inner">
                        <h6 class="abstract_deadline_head">Submission Deadline</h6>
                        <ul id="abstract_countdown">
                           <li>
                                <n><span id="dday">00</span></n><i>days</i>
                            </li>

                            <li>
                                <n><span id="dhour">00</span></n><i>Hours</i>
                            </li>

                            <li>
                                <n><span id="dmin">00</span></n><i>Minutes</i>
                            </li>

                            <li>
                                <n><span id="dsec">00</span></n><i>Seconds</i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <? } ?>
            <style>
              .disabled-btn {
                    pointer-events: none;
                    opacity: 0.5;
                    cursor: not-allowed;
                }
            </style>
            <?
                  $sqlMSG   =  array();
                $sqlMSG['QUERY']    = "SELECT `speakerStatus` FROM " . _DB_COMPANY_INFORMATION_ . " 
                            WHERE `id` = 1";
                $result       = $mycms->sql_select($sqlMSG);
                if($result[0]['speakerStatus']=='A'){
            ?>
            <div class="speaker_wrap">
                <div class="speaker_head">
                    <h2><span>World Class Faculty</span>Keynote Speakers</h2>
                    <a href="#">View Full Schedule<i class="fal fa-arrow-right"></i></a>
                </div>
                
                <div class="speaker_owl owl-carousel owl-theme">
                  <?php
                      $sql_cal			=	array();
                      $sql_cal['QUERY']	=	"SELECT * 
                                                  FROM `rcg_keynote_highlight_speaker`
                                                  WHERE `status` 	= 		'A'
                                                  ORDER BY `id` ASC";


                      $res_cal = $mycms->sql_select($sql_cal);
                      if($res_cal){
                          foreach ($res_cal as $key => $rows) {
                          $icon_image =$cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rows['profileImage'];
                      ?>
                    <div class="item">
                       <? 
                        if(!empty($rows['profileImage'])){
                            ?>
                        <span><img  src="<?= _BASE_URL_ ?><?= $icon_image ?>" alt="">
                            <n><i class="fal fa-award"></i></n>
                        </span>
                        <?php
                          } else{
                            ?>
                              <span><img src="images/image 79.png" alt="">
                                  <n><i class="fal fa-award"></i></n>
                              </span>
                            <?php 
                            }
                            ?>
                        <h4><?=$rows['speaker_name']?></h4>
                        <h5><?=$rows['designation']?></h5>
                        <h6><?=$rows['workInfo']?></h6>
                        <p><?=$rows['tagLine']?></p>
                    </div>
                   <?php
                      }
                      }
                      ?>
                </div>
            </div> 
            
        </div>
        <?php
          }
          ?>
    </div>
    </div>
   <?php
      //Fetching Footer Text
      $sql 	=	array();
      $sql['QUERY'] = "SELECT `display_footer_text` FROM "._DB_COMPANY_INFORMATION_." 
                        WHERE `id`!=''";
      $result 	 = $mycms->sql_select($sql);
      if($result)
			{
      ?>
    <div class="update">
        <span><i class="fal fa-megaphone"></i>Updates</span>
        <marquee behavior="scroll" direction="left" >
           <div class="ticker-content">
            <n><?=$result[0]['display_footer_text']?></n>
            <?php circle(); ?>
        </div>
        </marquee>
    </div>
    <img src="images/positivessl_trust_seal_lg_222x54.png" alt="" class="ssl">
    <?php } ?>
    <div class="float_btn">
        <?php
          $sqlFooterIcon  = array();
          $sqlFooterIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                  WHERE `status`='A' AND purpose='Side Icon' order by id ";
          $resultFooterIcon = $mycms->sql_select($sqlFooterIcon);
          foreach ($resultFooterIcon as $k => $val) {
            
              $title = $val['title'];
          $filePath =$cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['icon'];
         if (file_exists($filePath)) {        
          ?>
        <a class="popup_btn"  data-tab="<?=$val['id']?>" >
            <img 
                src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $val['icon'] ?>" 
                alt="<?= htmlspecialchars($title) ?>" 
                class="menu_icon"
            />
            <span><?= $title ?></span>
          </a>   
           <?php
              }
          }
          ?>
        <button><i class="fal fa-comment-alt"></i><i class="fal fa-times"></i></button>
    </div>
    
</body>
<div class="popup_wrap">
      <div class="popup_inner">
        <?php
          $sqlFooterIcon  = array();
          $sqlFooterIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                  WHERE `status`='A' AND purpose='Side Icon' order by id ";
          $resultFooterIcon = $mycms->sql_select($sqlFooterIcon);
          foreach ($resultFooterIcon as $k => $val) {   
          ?>
          <div class="popup_body registration_right_wrap" id="<?=$val['id']?>" style="width: 747px;">
              <div class="registration_right_head">
                  <span><?=$val['title']?></span><button class="popup_close"><?php close(); ?></button>
              </div>
              <div class="registration_right_body registration_right_body_withou_bottom">
                <?
                  if($val['title']=='Cancellation & Refund'){
                       $content_popup = $companyInfo['cancellation_page_info'];
                  }else if($val['title']=='T & C'){
                      $content_popup = $companyInfo['terms_page_info'];
                  }else if($val['title']=='Privacy Policy'){
                      $content_popup = $companyInfo['privacy_page_info'];
                  }
                  ?>
                  <?php echo  $content_popup; ?>
              </div>
          </div>
           <?php
              }
          ?> 
     </div>  
  </div>  
<?php include_once("includes/js-source.php"); ?>
<script>
   $('.popup_close').click(function() {
        $(".popup_wrap").hide();
        $(".popup_body").hide();
    });
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    })
    $('.float_btn button').click(function() {
            $('.float_btn').toggleClass('active');
        })
      $(document).ready(function () {

          const second = 1000,
              minute = second * 60,
              hour = minute * 60,
              day = hour * 24;

          let yyyy = <?= $dateArr[0] ?>;
          let dayMonth = '<?= $dateCount->format("m/d/") ?>';

          let [month, dayNum] = dayMonth.split('/');

          // create proper date (IMPORTANT FIX)
          let birthday = new Date(yyyy, month - 1, dayNum);
          birthday.setHours(23, 59, 59, 999);

          let today = new Date();

          // if already passed → next year
          if (today > birthday) {
              birthday.setFullYear(birthday.getFullYear() + 1);
          }

          const countDown = birthday.getTime();

          const x = setInterval(function () {

              const now = Date.now();
              const distance = countDown - now;

              document.getElementById("days").innerText = Math.max(0, Math.floor(distance / day));
              document.getElementById("hours").innerText = Math.max(0, Math.floor((distance % day) / hour));
              document.getElementById("minutes").innerText = Math.max(0, Math.floor((distance % hour) / minute));
              document.getElementById("seconds").innerText = Math.max(0, Math.floor((distance % minute) / second));

              if (distance <= 0) {
                  $("#registration_countdown").hide();
                  $("#registration_closed").show();
                  $(".register").hide();
                  clearInterval(x);
              }

          }, 1000);

      });
      $(document).ready(function () {

          const second = 1000,
              minute = second * 60,
              hour = minute * 60,
              day = hour * 24;

          let yyyy = <?= $dateArrAbs[0] ?>;
          let dayMonth = '<?= $dateCountAbs->format("m/d/") ?>';

          let [month, dayNum] = dayMonth.split('/');

          let birthdayAbs = new Date(yyyy, month - 1, dayNum);
          birthdayAbs.setHours(23, 59, 59, 999);

          let today = new Date();

          if (today > birthdayAbs) {
              birthdayAbs.setFullYear(birthdayAbs.getFullYear() + 1);
          }

          const countDownAbs = birthdayAbs.getTime();

          const x = setInterval(function () {

              const now = Date.now();
              const distanceAbs = countDownAbs - now;

              document.getElementById("dday").innerText = Math.max(0, Math.floor(distanceAbs / day));
              document.getElementById("dhour").innerText = Math.max(0, Math.floor((distanceAbs % day) / hour));
              document.getElementById("dmin").innerText = Math.max(0, Math.floor((distanceAbs % hour) / minute));
              document.getElementById("dsec").innerText = Math.max(0, Math.floor((distanceAbs % minute) / second));

              // FIXED condition (was wrong before)
              if (distanceAbs <= 0) {
                  $("#registration_countdown").hide();
                  $("#registration_closed").show();
                  $(".register").hide();
                  clearInterval(x);
              }

          }, 1000);

      });
              
</script>
<script>
  $("input[type=radio][use=payment_mode_select]").click(function() {
    var val = $(this).val();

    $("div[use=offlinePaymentOption]").hide();
    if (val != undefined) {
      $("div[use=offlinePaymentOption][for=" + val + "]").show();
      if (val === 'Card') {
        $('#registrationMode').val('ONLINE');
        $('#paymentDetailsSection').hide();
      } else {
        if ($(this).attr('act') == 'Upi') {
          $('.for-upi-only').show();
          $('.for-neft-rtgs-only').hide();
        } else {
          $('.for-upi-only').hide();
          $('.for-neft-rtgs-only').show();
        }
        $('#registrationMode').val('OFFLINE');
        $('#paymentDetailsSection').show();
      }
    }

  });

  $('#neft_document').on('change', function() {
    var file = this.files[0];
    if (file) {
      $('#neft_file_name').html(file['name']);
    } else {
      $('#neft_file_name').html('');
    }
  })

  $('#cash_document').on('change', function() {
    var file = this.files[0];
    if (file) {
      $('#cash_file_name').html(file['name']);
    } else {
      $('#cash_file_name').html('');
    }
  })

  $("#pay-button-offline").click(function() {

    var selectedOption = $("input[type=radio][name='payment_mode']:checked").val();
    var flag = 0;

    if (selectedOption) {

      $("div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='text'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='date'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='radio'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='number'],div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='file']").each(function() {

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

          if ($(this).hasClass('mandatory')) {
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
      return false;
    }

    if (flag == 0) {
      //alert(1212);
      $("form[name='frmApplyPaymentOffline']").submit();
    } else {
      return false;
    }

  });
</script>
<!-- =============================== OFFLINE PAYMENT MODAL END ============================================ -->

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
        data: 'action=getPaymentVoucharDetailsIndex&user_email_id=' + user_email_id,
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
            $('#loginBtn').prop('disabled', true);

              toastr.success('Redirecting to payment page...', 'Success', {
                  progressBar: true,
                  timeOut: 1500
              });

              setTimeout(function() {
                  window.location.href = '<?= _BASE_URL_ ?>registration_payment.php';
              }, 1500);
            // if (JSONObject.mode == 'OFFLINE') {
            //   $('#user_email_id').val("");
            //   $('#loading_indicator').show();
            //   $('#payModal').hide();
            //   // $('#paymentVoucherBody').append(JSONObject.data);
            //   $('#orderSummeryInvoicesOffline').html(JSONObject.orderSummeryInvoices);
            //   $('#order_details_offline').html(JSONObject.data);

            //   $('#slip_id_offline').val(JSONObject.slipId);
            //   $('#delegate_id_offline').val(JSONObject.delegateId);
            //   $('#mode').val(JSONObject.invoice_mode);

            //   $('#loginBtn').prop('disabled', true);

            //   toastr.success(JSONObject.msg, 'Success', {
            //     "progressBar": true,
            //     "timeOut": 2000,
            //     "showMethod": "slideDown",
            //     "hideMethod": "slideUp"
            //   });
            //   setTimeout(function() {
            //     $('#loading_indicator').hide();
            //     $('#paymentVoucherModalOffline').show();
            //     $('#loginBtn').prop('disabled', false);
            //     //window.location.href= jsBASE_URL + 'profile.php';

            //   }, 2000);
            // } else {
            //   $('#user_email_id').val("");
            //   $('#loading_indicator').show();
            //   $('#payModal').hide();
            //   // $('#paymentVoucherBody').append(JSONObject.data);
            //   $('#orderSummeryInvoices').html(JSONObject.orderSummeryInvoices);
            //   $('#order_details').html(JSONObject.data);

            //   $('#slip_id').val(JSONObject.slipId);
            //   $('#delegate_id').val(JSONObject.delegateId);
            //   $('#mode').val(JSONObject.invoice_mode);

            //   $('#loginBtn').prop('disabled', true);

            //   toastr.error(JSONObject.msg, 'Success', {
            //     "progressBar": true,
            //     "timeOut": 2000,
            //     "showMethod": "slideDown",
            //     "hideMethod": "slideUp"
            //   });
            //   setTimeout(function() {
            //     $('#loading_indicator').hide();
            //     $('#paymentVoucherModal').show();
            //     $('#loginBtn').prop('disabled', false);
            //     //window.location.href= jsBASE_URL + 'profile.php';

            //   }, 2000);
            // }

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
      // window.location.href = "<?= _BASE_URL_ ?>registration.php";
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
    $("#registration_operation").removeClass("disabled-btn");
    $("#login_operation").removeClass("disabled-btn");
    $("#pay-button-modal").removeClass("disabled-btn");
    $('#registration_operation').removeClass('register');
    $('#login_operation').removeClass('register');
    $('#pay-button-modal').removeClass('register');
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
              data: 'act=getEmailValidationStatusIndex&email=' + emailId,
              dataType: 'json',
              async: false,
              success: function(JSONObject) {

                if (JSONObject.STATUS == 'IN_USE') {
                  $('#login_operation').removeAttr('disabled');
                  $('#registration_operation').prop('disabled', true);
                  $('#pay-button-modal').prop('disabled', true);
                  $('#login_operation').addClass('register');
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
                    // $('#login_operation').removeAttr('disabled');
                    // $('#login_operation').addClass('register');
                    // $('#login_operation').click();
                    $('#pay-button-modal').removeAttr('disabled');
                      $("#abstractbtn").removeClass("disabled-btn");
                    $('#login_operation').attr('disabled', true);
                      $("#login_operation").addClass("disabled-btn");
                    $('#pay-button-modal').addClass('register');
                    // $('#registration_operation').prop('disabled', true);
                    $("#registration_operation").addClass("disabled-btn");
                     $('#abstractDelegateId').val(JSONObject.ID);
                    // $('#pay-button-modal').removeAttr('disabled');
                  }
                } else if (JSONObject.STATUS == 'ABSTRACT_UNPAID_OFFLINE') {
                  $('#login_operation').removeAttr('disabled');
                  $('#login_operation').addClass('register');
                  $('#abstractDelegateId').val(JSONObject.ID);
                  $("#registration_operation").addClass("disabled-btn");
                    $("#pay-button-modal").addClass("disabled-btn");
                  var registeredEmail = $("#registeredEmail").val();
                  setTimeout(function() {
                    $('#login_operation').show();
                    $('#login_operation').attr('id', 'login_final_operation');
                    $('#unique').show();
                    $('#checkUserEmail').hide();
                    //  window.location.href= jsBASE_URL;

                  }, 3000);
                } else if (JSONObject.STATUS == 'ABSTRACT_UNPAID_ONLINE') {
                  $('#login_operation').removeAttr('disabled');
                  $('#login_operation').addClass('register');
                   $("#registration_operation").addClass("disabled-btn");
                    $("#pay-button-modal").addClass("disabled-btn");
                  $('#abstractDelegateId').val(JSONObject.ID);
                  var registeredEmail = $("#registeredEmail").val();

                  setTimeout(function() {
                    $('#login_operation').show();
                    $('#login_operation').attr('id', 'login_final_operation');
                    $('#unique').show();
                    $('#checkUserEmail').hide();
                    //  window.location.href= jsBASE_URL;

                  }, 3000);

                  $('#pay-button-modal').removeAttr('disabled');

                } else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE') {
                  if (emailId != '') {
                      $("#registration_operation").addClass("disabled-btn");
                      $("#login_operation").addClass("disabled-btn");
                      $("#pay-button-modal").addClass("disabled-btn");
                        $("#abstractbtn").removeClass("disabled-btn");
                         $('#abstractDelegateId').val(JSONObject.ID);
                    toastr.error('<?= $unpaid_offline_msg ?>', 'Payment Pending', {
                      "progressBar": true,
                      "timeOut": 4000,
                      "showMethod": "slideDown",
                      "hideMethod": "slideUp"
                    });

                  }
                } else if (JSONObject.STATUS == 'NOT_PAID_OFFLINE_NOT_SET') {
                    $('#pay-button-modal').removeAttr('disabled');
                    $('#pay-button-modal').addClass('register');
                    $('#registration_operation').prop('disabled', true);
                    $('#login_operation').prop('disabled', true);
                    $("#registration_operation").addClass("disabled-btn");
                    $("#login_operation").addClass("disabled-btn");
                    $("#abstractbtn").removeClass("disabled-btn");
                    $('#abstractDelegateId').val(JSONObject.ID);
                } else if (JSONObject.STATUS == 'NOT_AVAILABLE' && JSONObject.ARTWRK == 'YES') {

                  $('#registration_operation').removeAttr('disabled');
                  $('#registration_operation').addClass('register');
                  $('#login_operation').prop('disabled', true);
                  $('#pay-button-modal').prop('disabled', true);
                    $("#abstractbtn").removeClass("disabled-btn");
                  $('#abstractDelegateId').val(JSONObject.ID);
                  var registeredEmail = $("#registeredEmail").val();
                  $('#abstract_basic_details').show();
                  $('#abstractSubmit').show();
                  $('#checkUserEmail').hide();
                  var registerDiv = $("#registerModal");

                
                }else if (JSONObject.STATUS == 'NOT_AVAILABLE') {

                  $('#registration_operation').removeAttr('disabled');
                  $('#registration_operation').addClass('register');
                  $('#login_operation').prop('disabled', true);
                  $('#pay-button-modal').prop('disabled', true);
                    $("#abstractbtn").removeClass("disabled-btn");

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
                  $('#registration_operation').addClass('register');
                  $('#checkUserEmail').hide();
                    $("#abstractbtn").removeClass("disabled-btn");

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
                        $('#registration_operation').addClass('register');
                        $('#login_operation').addClass('register');
                        $('#login_operation').removeAttr('disabled');
                        $('#unique').show();
                        // $('#login_operation').show();
                        $('#login_operation').attr('id', 'login_final_operation');

                        $('#checkUserEmail').hide();
                        $("#abstractbtn").removeClass("disabled-btn");

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
    localStorage.setItem("user_email_id", user_email_id);
    if (abstractDelegateId != '' && abstractDelegateId > 0) {
          //  alert("hi");

            window.location.href = '<?= _BASE_URL_ ?>registration.php?abstractDelegateId=' + abstractDelegateId;
    } else {
               window.location.href = '<?= _BASE_URL_ ?>registration.php';


      // window.location.href='<?= _BASE_URL_ ?>registration.tariff.php';
    }



  }
  function sendUrlabstract() {
    var user_email_id = $('#user_email_id').val();
    var abstractDelegateId = $('#abstractDelegateId').val();
    localStorage.setItem("user_email_id", user_email_id);
    if (abstractDelegateId != '' && abstractDelegateId > 0) {

      window.location.href = '<?= _BASE_URL_ ?>abstract_user.php?abstractDelegateId=' + abstractDelegateId;
    } else {
        window.location.href = '<?= _BASE_URL_ ?>abstract_user.php';
    }



  }
</script>
<script>
    const toggleBtn = document.getElementById("toggleBtn");
    const input = document.getElementById("user_unique_sequence");
    const icon = document.getElementById("toggleIcon");

    toggleBtn.addEventListener("click", function () {
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    });
</script>
</html>
