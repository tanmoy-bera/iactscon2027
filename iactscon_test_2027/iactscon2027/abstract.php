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

$mycms->removeAllSession();
$mycms->removeSession('SLIP_ID');

if ($cfg['ABSTRACT.SUBMIT.LASTDATE'] < date('Y-m-d')) {
  $isAbstractDate = false;
  //   $mycms->redirect("profile.php");
}

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
$sqlMSG   =  array();
$sqlMSG['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
			WHERE `id` = 1";
$result       = $mycms->sql_select($sqlMSG);
$companyInfo =  $result[0];
$startD = $companyInfo['conf_start_date'];
$endD = $companyInfo['conf_end_date'];
$startDate = new DateTime($startD);
$endDate = new DateTime($endD);
$formatted = $startDate->format("M d")."-". $endDate->format("M d");

//========= fetching countdown date from abstract countdown ==========================//
$sqlFetchCountdown           = array();
$sqlFetchCountdown['QUERY']     = "SELECT * 
                    FROM " . _DB_COMPANY_INFORMATION_ . "
                    WHERE `id` != ''";
$resultFetchCountdown         = $mycms->sql_select($sqlFetchCountdown);
$rowFetchCountdownDate         = $resultFetchCountdown[0]['abstract_countdown_date'];
$dateArr          = explode("-", $rowFetchCountdownDate);
//print_r($dateArr);

if ($cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d')) {

  $_SESSION['PROCEED_2_ABSTRACT'] = 'OK';
  $_SESSION['PROCEED_EXPIRY'] = $_REQUEST['EXPIRY'];

  $operate        = true;
  $resultAbstractType   = false;
  if ($delegateId != '') {
    $rowUserDetails   = getUserDetails($delegateId);
    $invoiceList    = getConferenceContents($delegateId);
    $currentCutoffId  = getTariffCutoffId();

    $sql          = array();
    $sql['QUERY']     = " SELECT * 
                    FROM " . _DB_ABSTRACT_REQUEST_ . " 
                     WHERE `status` = ?
                     AND `applicant_id` = ?";
    //AND `abstract_child_type` IN ('Oral','Poster')

    $sql['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
    $sql['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' => $delegateId, 'TYP' => 's');
    $resultAbstractType = $mycms->sql_select($sql);

    $abstractCatArray = array();
    foreach ($resultAbstractType as $key => $cat_val) {
      //echo $cat_val['abstract_cat'];
      array_push($abstractCatArray, trim($cat_val['abstract_cat']));
    }

    //echo '<pre>'; print_r($resultAbstractType);
  }
}
?>

<?php
javaScriptDefinedValue();

include_once('header.php');

$cutoffs       = fullCutoffArray();
$currentCutoffId  = getTariffCutoffId();

$loginDetails   = login_session_control(false);
$delegateId    = $loginDetails['DELEGATE_ID'];

$operate     = false;

if (isset($_REQUEST['TOKEN']) && trim($_REQUEST['TOKEN']) != '') {
  $token = unserialize(base64_decode($_REQUEST['TOKEN']));
  if (is_array($token) && sizeof($token) > 0) {
    foreach ($token as $key => $val) {
      $_REQUEST[$key] = $val;
    }
  }
}

if ($_SESSION['PROCEED_2_ABSTRACT'] == 'OK') {
  $_REQUEST['PROCEED'] = 'OK';
  $_REQUEST['EXPIRY'] = $_SESSION['PROCEED_EXPIRY'];
}


$sqlHeader  = array();
$sqlHeader['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                      WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultHeader = $mycms->sql_select($sqlHeader);
$rowHeader         = $resultHeader[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowHeader['logo_image'];
if ($rowHeader['logo_image'] != '') {
  $emailHeader  = $header_image;
}

?>
<body>
    <div class="registration_wrap abstract_wrap">
        <div class="registartion_head abstract_head">
            <a href="index.php"><i class="fal fa-arrow-left"></i>Back</a>
        </div>
        <div class="login_left abstract_top">
            <h3 class="con_logo"><img src="<?=$emailHeader?>" alt=""></h3>
            <h2>Innovate. Integrate. Inspire</span></h2>
            <p>73rd Annual Conference of the Indian Association of Cardiovascular-Thoracic Surgeons(IACTS).</p>
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
        </div>
        <div class="abstract_inner">
            <h5>Begin Submission</h5>
            <h6>Enter your email to start or resume your abstract submission</h6>
            <?
                // REGISTRATION RELATED OPERATION
                $registrationClassificationId   = $mycms->getSession('CLSF_ID_FRONT');
                $registrationCutoffId           = $mycms->getSession('CUTOFF_ID_FRONT');
                $registrationMode             = $mycms->getSession('REGISTRATION_MODE');

                $sql   =  array();
                $sql['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
                                WHERE `id` = 1";
                $result       = $mycms->sql_select($sql);
                $row         = $result[0];
                $invalidEmail = $row['notification_invalid_email'];
                $registeredEmail = $row['notification_registered_email'];
                $emptyEmail = $row['notification_empty_email'];
            ?>

            <div class="abstract_frm_wrap">
                <div class="abstract_frm_grp">
                    <p class="frm-head">Email Id <i class="mandatory">*</i></p>
                    <div>
                        <i class="fal fa-envelope"></i> 
                        <input type="hidden" id="registeredEmail" value="<?= $registeredEmail ?>">
                        <input type="hidden" id="invalidEmail" value="<?= $invalidEmail ?>">
                        <input type="hidden" id="emptyEmail" value="<?= $emptyEmail ?>">
                        <input type="abstract_presenter_email"  placeholder="Please Enter Email Address" id="abstract_presenter_email" placeholder="E-mail" name="user_email_id" <?php if ($cfg['ABSTRACT.SUBMIT.LASTDATE'] < date('Y-m-d')) {
                                                                                                                                                            echo 'style="pointer-events:none;filter: blur(1.2px)"';
                                                                                                                                                        } ?>>
                    </div>
                </div>
                <a class="abstractbtn"  type="button" id="checkUserEmail" >Proceed</a>
                <!-- <div class="login_frm_wrap"  id="unique" style="display:none;">
                    <label>Code</label>
                    <div>
                        <i class="fal fa-lock"></i>
                        <input placeholder="Uniq Code"  type="text" name="user_unique_sequence" id="user_unique_sequence" value="#" placeholder="Ex: #00000000" onkeypress="return isNumber(event)">
                        <button id="toggleBtn"><i class="fal fa-eye"  id="toggleIcon"></i></button>
                    </div>
                </div>
                 <div class="login_btn_wrap login-buttons">
                    <a href="javascript:void(0)"  class="login-btns disabled-btn" id="login_operation" style="cursor: pointer;" disabled="disabled" ><i class="fal fa-sign-in"></i>Login</a>
                </div> -->
            </div>
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
  <script type="text/javascript">
    $(document).ready(function() {

      // var isAbstractDate = <?= $isAbstractDate ?>;
      // if (isAbstractDate==false) {

      //   toastr.success("The abstract submission deadline has passed!", 'Error', {
      //     "progressBar": true,
      //     "timeOut": 5000,
      //     "showMethod": "slideDown",
      //     "hideMethod": "slideUp"
      //   });

      // }

      $('#checkUserEmail').click(function() {
        var emailId = $('#abstract_presenter_email').val();
        localStorage.setItem("user_email_id", emailId);

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
                  console.log(JSONObject);

                  if (JSONObject.STATUS == 'IN_USE') {
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

                  } else if (JSONObject.STATUS == 'NOT_PAID' || JSONObject.STATUS == 'NOT_PAID_OFFLINE') {
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
                  } else if (JSONObject.STATUS == 'NOT_AVAILABLE') {
                    
                    $('#abstract_basic_details').show();
                    window.location.href = "abstract_user.php";
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


                  } else {

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
                          $('#login_operation').show();
                          $('#login_operation').attr('id', 'login_final_operation');
                          $('#unique').show();
                          $('#checkUserEmail').hide();
                          // window.location.href = jsBASE_URL + 'index.php';

                        }, 3000);
                      }
                    });

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

      $(document).on("click", "#login_final_operation", function(e) {
        e.preventDefault();
        var user_email_id = $('#abstract_presenter_email').val();
        var user_unique_sequence = $('#user_unique_sequence').val();

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
            data: 'action=getLoginValidationAbstract&user_email_id=' + user_email_id + '&user_unique_sequence=' + user_unique_sequence,
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
    });
  </script>

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

    console.log('yy=' + year + 'mm=' + month)

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

    $('#abstractSubmit').click(function() {

      isValid = true;

      $("input[type='text'], input[type='radio'], input[type='checkbox'], select").each(function(index) {

        if ($(this).attr('type') === 'text' && !$.trim($(this).val()) && $(this).attr('id') != 'user_middle_name') {
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
        } else if ($(this).attr('type') === 'radio') {
          if (!$("input[type='radio'][name='user_initial_title']:checked").length) {

            toastr.error('Please select user title', 'Error', {
              "progressBar": true,
              "timeOut": 3000, // 3 seconds
              "showMethod": "slideDown", // Animation method for showing
              "hideMethod": "slideUp",
              "direction": 'ltr', // Animation method for hiding
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

      if (isValid) {
        $('#absRegisterForm').submit();
      }
    });
  </script>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    // $('.stay_li_right').click(function() {
    //     if ($('.stay_li_right').find(':checked')) {
    //         $(this).addClass('selected');
    //     }
    // })

    $(document).ready(function() {

        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        setProgressBar(current);

        $(".next").click(function() {

            current_fs = $(this).parent().parent();
            next_fs = $(this).parent().parent().next();

            //Add Class Active
            $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

            //show the next fieldset
            next_fs.show();
            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;

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
            setProgressBar(++current);
        });

        $(".previous").click(function() {

            current_fs = $(this).parent().parent();
            previous_fs = $(this).parent().parent().prev();

            //Remove class active
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //show the previous fieldset
            previous_fs.show();

            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;

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
        });

        function setProgressBar(curStep) {
            var percent = parseFloat(100 / steps) * curStep;
            percent = percent.toFixed();
            $(".progress-bar")
                .css("width", percent + "%")
        }

        $(".submit").click(function() {
            return false;
        })

    });
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

</html>