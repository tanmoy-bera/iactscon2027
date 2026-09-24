<?php 
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php');
$delegateId = trim($_REQUEST['delegateId']);
$slipId 	= trim($_REQUEST['slipId']);

$rowFetchUserDetails = getUserDetails($delegateId);
$invoiceDetails 	 = invoiceDetailsOfSlip($slipId);
$paymentDetails		 = paymentDetails($slipId);
$services 			 = servicesOfSlip($slipId);
$totalSlipAmount 	 = invoiceAmountOfSlip($slipId);
$slipDetails		 = slipDetails($slipId);

$paymentId 			 = $paymentDetails['id'];

?>


<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Registration</a></li>
                        <li class="breadcrumb-item">Registration List</li>
                         <li class="breadcrumb-item">Invoice & Mail</li>
                        <li class="breadcrumb-item active" aria-current="page">Send Mail</li>
                    </ol>
                </nav>
                <h2>Send Mail</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
             <div class="page_top_wrap_right">
                <!-- <p><?php printi(); ?>Card Printed: <b>0</b></p>
                <p><?php check(); ?>Delevered: <b>0</b></p>
                <p><?php user(); ?>Total: <b>2</b></p> -->
                <a href="registration.php" class="badge_danger"><i class="fal fa-arrow-left"></i>Back</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Mail Topics</h6>
                <button data-tab="acknowledgementmail" class="com_info_left_click icon_hover badge_primary active"><i class="fal fa-school"></i>Acknowledgement</button>
                <button data-tab="workshopcertificatemail" class="com_info_left_click icon_hover badge_secondary action-transparent"><?php rupee() ?>Workshop Certificate</button>
                <button data-tab="certificatemail" class="com_info_left_click icon_hover badge_success action-transparent"><?php user() ?>Certificate</button>
                <?
                
                  if ($paymentDetails['payment_status'] == "PAID" || $slipDetails['payment_status'] == "COMPLIMENTARY" || $slipDetails['payment_status'] == "ZERO_VALUE") {
                ?>
                <button data-tab="serviceconfirmationmail" class="com_info_left_click icon_hover badge_info action-transparent"><?php workshop() ?>Service Confirmation</button>
                <button data-tab="loginmail" class="com_info_left_click icon_hover badge_info action-transparent"><?php workshop() ?>Send Login Credential</button>
               <?
                 } 
                 ?>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="acknowledgementmail">
                    <?
                    if (in_array('DELEGATE_CONFERENCE_REGISTRATION', $services)) {
                            $msg = 	offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (in_array('DELEGATE_RESIDENTIAL_REGISTRATION', $services)) {
                            $msg = 	offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (in_array('ACCOMPANY_CONFERENCE_REGISTRATION', $services)) {
                            $msg = 	offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION') {
                            $msg = offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST') {
                            $msg = offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_DINNER_REQUEST') {
                            $msg = offline_registration_acknowledgement_message($delegateId, $slipId, $paymentId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        }

                    ?>
                   <form name="sendMail" id="sendMail" action="registration.process.php" method="post">
                    <input type="hidden" name="act" value="sendRegFinalMail" />
                    <input type="hidden" name="delegateId" id="delegateId" value="<?= $_REQUEST['delegateId'] ?>" />
                    <input type="hidden" name="slipId" id="slipId" value="<?= $_REQUEST['slipId'] ?>" />
                    <input type="hidden" name="paymentId" id="paymentId" value="<?= $_REQUEST['paymentId'] ?>" />
                    <input type="hidden" name="buttonForSpot" id="buutonForSpot" value="<?= $_REQUEST['button'] ?>" />

                    <input type="hidden" name="invoice_mode" id="invoice_mode" value="<?= $fetchData['invoice_mode'] ?>" />
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php email() ?></span> Acknowledgement</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                      <textarea name="mail_subject" id="mail_subject" ><?= $msg['MAIL_SUBJECT'] ?></textarea>

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_first_name'] . ' ' . $rowFetchUserDetails['user_middle_name'] . ($rowFetchUserDetails['user_middle_name'] ? ' ' : "") .  $rowFetchUserDetails['user_last_name'] ?>"  />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" value="<?= $rowFetchUserDetails['user_email_id'] ?>"  />

                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <?
                                            echo $mailBody;
                                            ?>
									    <textarea name="mail_body" id="mail_body" style="display:none;"><?= $mailBody ?></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <button type="submit"  value="BACK" name="bttnStep3" id="bttnStep3" class="badge_success formsubmit">
                                            <i class="fal fa-paper-plane"></i> Send Mail
                                        </button>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="com_info_box" id="workshopcertificatemail">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php email() ?></span> Workshop Certificate</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <textarea name="" id=""></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <a href="#" class="badge_success formsubmit"><i class="fal fa-paper-plane"></i>Send Mail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="certificatemail">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php email() ?></span> Certificate</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <textarea name="" id=""></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <a href="#" class="badge_success formsubmit"><i class="fal fa-paper-plane"></i>Send Mail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
                <div class="com_info_box" id="serviceconfirmationmail">
                     <form name="sendMail" id="sendMail" action="<?= $cfg['SECTION_BASE_URL'] ?>registration.process.php" onsubmit="return onSubmitAction();" method="post">
                    <input type="hidden" name="act" value="sendRegFinalMail" />
                    <input type="hidden" name="delegateId" id="delegateId" value="<?= $_REQUEST['delegateId'] ?>" />
                    <input type="hidden" name="slipId" id="slipId" value="<?= $_REQUEST['slipId'] ?>" />
                    <input type="hidden" name="paymentId" id="paymentId" value="<?= $_REQUEST['paymentId'] ?>" />
                    <input type="hidden" name="buttonForSpot" id="buutonForSpot" value="<?= $_REQUEST['button'] ?>" />
                    <input type="hidden" name="invoice_mode" id="invoice_mode" value="<?= $fetchData['invoice_mode'] ?>" />
                    <?
    	         

                    // print_r($services);
                    if ($paymentDetails['payment_mode'] == 'Online') {

                        if (in_array('DELEGATE_CONFERENCE_REGISTRATION', $services)) {
                            $msg = 	online_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (in_array('DELEGATE_RESIDENTIAL_REGISTRATION', $services)) {
                            $msg = 	online_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (in_array('ACCOMPANY_CONFERENCE_REGISTRATION', $services)) {
                            $msg = 	online_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION') {
                            if ($rowFetchUserDetails['registration_classification_id'] == '11') {
                                $msg = complementary_workshop_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                                $mailBody = $msg['MAIL_BODY'];
                            } else {
                                $msg = online_conference_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                                $mailBody = $msg['MAIL_BODY'];
                            }
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST') {
                            $msg = online_accommodation_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_DINNER_REQUEST') {

                            $msg = online_dinner_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        }
                    } else {
                        //print_r($services);
                        if (in_array('DELEGATE_CONFERENCE_REGISTRATION', $services)) {

                            $msg = offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (in_array('DELEGATE_RESIDENTIAL_REGISTRATION', $services)) {
                            $msg = offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (in_array('ACCOMPANY_CONFERENCE_REGISTRATION', $services)) {

                            //$msg =  offline_conference_registration_confirmation_accompany_message($delegateId,$paymentId, $slipId, 'RETURN_TEXT');
                            // $msg = offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $msg = 	online_conference_registration_confirmation_accompany_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');

                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_WORKSHOP_REGISTRATION') {
                            if ($rowFetchUserDetails['registration_classification_id'] == '11') {
                                $msg = complementary_workshop_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                                $mailBody = $msg['MAIL_BODY'];
                            } else {
                                $msg = offline_conference_registration_confirmation_workshop_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                                $mailBody = $msg['MAIL_BODY'];
                            }
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_ACCOMMODATION_REQUEST') {
                            $msg = offline_accommodation_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        } elseif (sizeof($services) == 1 && $services[0] == 'DELEGATE_DINNER_REQUEST') {
                            $msg = offline_dinner_confirmation_message($delegateId, $paymentId, $slipId, 'RETURN_TEXT');
                            $mailBody = $msg['MAIL_BODY'];
                        }
                    }

                    ?>
                    <style>
                        .com_info_box_inner table tr td {
                            background-color: transparent!important;
                        }
                        </style>
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php email() ?></span> Service Confirmation</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                       	<textarea name="mail_subject" id="mail_subject"><?= $msg['MAIL_SUBJECT'] ?></textarea>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_first_name'] . ' ' . $rowFetchUserDetails['user_middle_name'] . ($rowFetchUserDetails['user_middle_name'] ? ' ' : "") .  $rowFetchUserDetails['user_last_name'] ?>"/>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" value="<?= $rowFetchUserDetails['user_email_id'] ?>" />

                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <?
                                        echo $mailBody;
                                        ?>
									   <textarea name="mail_body" id="mail_body" style="display:none;"><?= $mailBody ?></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <button type="submit"  value="BACK" name="bttnStep3" id="bttnStep3" class="badge_success formsubmit">
                                            <i class="fal fa-paper-plane"></i> Send Mail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="com_info_box" id="loginmail">
                     <form name="sendMail" id="sendMail" action="<?= $cfg['SECTION_BASE_URL'] ?>registration.process.php" onsubmit="return onSubmitAction();" method="post">
                    <input type="hidden" name="act" value="sendRegFinalMail" />
                    <input type="hidden" name="delegateId" id="delegateId" value="<?= $_REQUEST['delegateId'] ?>" />
                    <input type="hidden" name="slipId" id="slipId" value="<?= $_REQUEST['slipId'] ?>" />
                    <input type="hidden" name="paymentId" id="paymentId" value="<?= $_REQUEST['paymentId'] ?>" />
                    <input type="hidden" name="buttonForSpot" id="buutonForSpot" value="<?= $_REQUEST['button'] ?>" />
                    <input type="hidden" name="invoice_mode" id="invoice_mode" value="<?= $fetchData['invoice_mode'] ?>" />
                    <?
    	         

                    // print_r($services);
                   
                        $msg = send_uniqueSequence($delegateId, 'RETURN_TEXT');
                        $mailBody = $msg['MAIL_BODY'];
                    ?>
                    <style>
                        .com_info_box_inner table tr td {
                            background-color: transparent!important;
                        }
                        </style>
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php email() ?></span> Service Confirmation</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                       	<textarea name="mail_subject" id="mail_subject"><?= $msg['MAIL_SUBJECT'] ?></textarea>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_first_name'] . ' ' . $rowFetchUserDetails['user_middle_name'] . ($rowFetchUserDetails['user_middle_name'] ? ' ' : "") .  $rowFetchUserDetails['user_last_name'] ?>"/>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" value="<?= $rowFetchUserDetails['user_email_id'] ?>" />

                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <?
                                        echo $mailBody;
                                        ?>
									   <textarea name="mail_body" id="mail_body" style="display:none;"><?= $mailBody ?></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <button type="submit"  value="BACK" name="bttnStep3" id="bttnStep3" class="badge_success formsubmit">
                                            <i class="fal fa-paper-plane"></i> Send Mail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    $('.com_info_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box").removeClass("active");
        $(".com_info_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
    $('.com_info_box_content_sec_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box_content_sec_right_box").removeClass("active");
        $(".com_info_box_content_sec_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
</script>

</html>