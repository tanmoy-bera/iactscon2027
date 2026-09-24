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

$applicant_id = trim($_REQUEST['applicantId']);
$abstract_id = trim($_REQUEST['id']);

$rowFetchUserDetails    = getUserDetails($applicant_id);

?>


<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Abstract Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Abstract Registration</a></li>
                        <li class="breadcrumb-item">Abstract List</li>
                         <li class="breadcrumb-item">Submited Abstract</li>
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
                <a href="abstract_submited.php" class="badge_danger"><i class="fal fa-arrow-left"></i>Back</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Mail Topics</h6>
                <button data-tab="serviceconfirmationmail" class="com_info_left_click icon_hover badge_info active"><?php workshop() ?>Service Confirmation</button>
                <button data-tab="certificatemail" class="com_info_left_click icon_hover badge_success action-transparent"><?php user() ?>Acceptance</button>
          
                <button data-tab="loginmail" class="com_info_left_click icon_hover badge_info action-transparent"><?php user() ?>Rejection</button>
                <button data-tab="workshopcertificatemail" class="com_info_left_click icon_hover badge_secondary action-transparent"><?php user() ?>Abstract Certificate</button>

            </div>
            <div class="com_info_right">
               <div class="com_info_box active" id="serviceconfirmationmail">
                <form name="sendMail" id="sendMail" action="abstract.free_papers.process.php" method="post">                    
                   <input type="hidden" name="act" value="sendAbstractConfirmationMail" />
			      <input type="hidden" name="delegateId" id="delegateId" value="<?= $applicant_id ?>" />
			      <input type="hidden" name="abstractId" id="delegateId" value="<?= $abstract_id ?>" />
			       <input type="hidden" name="user_full_name" id="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>" />

                    <?
    	         

                      $msg = abstract_submission_message($applicant_id, $abstract_id, 'RETURN_TEXT');
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
                                       	<textarea name="mail_subject" id="mail_subject" required><?= $msg['MAIL_SUBJECT'] ?></textarea>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>"/>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" value="<?= $rowFetchUserDetails['user_email_id'] ?>" />

                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">CC <i class="mandatory">*</i></p>
                                        <div class="form_grid g_5">
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                        </div>
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
                    
                   <form name="sendMail" id="sendMail" action="abstract.free_papers.process.php" method="post">                    
                   <input type="hidden" name="act" value="sendAbstractConfirmationMail" />
			        <input type="hidden" name="delegateId" id="delegateId" value="<?= $applicant_id ?>" />
			        <input type="hidden" name="abstractId" id="delegateId" value="<?= $abstract_id ?>" />
			        <input type="hidden" name="user_full_name" id="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>" />

                      <?php
                      
                            // $msg = abstract_submission_message($applicant_id, $abstract_id, 'RETURN_TEXT');

                            // $mailBody = $msg['MAIL_BODY'];
                      ?>
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php email() ?></span> Abstract Certificate</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                       	<textarea name="mail_subject" id="mail_subject" required></textarea>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>"/>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" required value="<?= $rowFetchUserDetails['user_email_id'] ?>">
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">CC <i class="mandatory">*</i></p>
                                        <div class="form_grid g_5">
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <?
                                        // echo $mailBody;
                                        ?>
									   <textarea name="mail_body" id="mail_body" required></textarea>
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
                <div class="com_info_box" id="certificatemail">
                  <form name="sendMail" id="sendMail" action="abstract.free_papers.process.php" method="post">                    
                   <input type="hidden" name="act" value="sendAbstractConfirmationMail" />
			      <input type="hidden" name="delegateId" id="delegateId" value="<?= $applicant_id ?>" />
			      <input type="hidden" name="abstractId" id="delegateId" value="<?= $abstract_id ?>" />
			       <input type="hidden" name="user_full_name" id="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>" />

                      <?php
                      
                            // $msg = abstract_acceptance_message_new($applicant_id, $abstract_id, 'RETURN_TEXT');

                            // $mailBody = $msg['MAIL_BODY'];
                      ?>
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php email() ?></span> Abstract Acceptance</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                       	<textarea name="mail_subject" id="mail_subject" required></textarea>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>"/>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" required value="<?= $rowFetchUserDetails['user_email_id'] ?>">
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">CC <i class="mandatory">*</i></p>
                                        <div class="form_grid g_5">
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <?
                                        // echo $mailBody;
                                        ?>
									   <textarea name="mail_body" id="mail_body" required></textarea>
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
                 <form name="sendMail" id="sendMail" action="abstract.free_papers.process.php" method="post">                    
                   <input type="hidden" name="act" value="sendAbstractConfirmationMail" />
			      <input type="hidden" name="delegateId" id="delegateId" value="<?= $applicant_id ?>" />
			      <input type="hidden" name="abstractId" id="delegateId" value="<?= $abstract_id ?>" />
			       <input type="hidden" name="user_full_name" id="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>" />

                    <?
    	         

                    // print_r($services);
                   
                        // $msg = send_uniqueSequence($delegateId, 'RETURN_TEXT');
                        // $mailBody = $msg['MAIL_BODY'];
                    ?>
                    <style>
                        .com_info_box_inner table tr td {
                            background-color: transparent!important;
                        }
                        </style>
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php email() ?></span>Abstract Rejection</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                       	<textarea name="mail_subject" id="mail_subject" required></textarea>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input type="text" name="user_full_name" value="<?= $rowFetchUserDetails['user_full_name'] ?>"/>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                        <input type="text" name="user_email_id" required value="<?= $rowFetchUserDetails['user_email_id'] ?>" />

                                    </div>
                                     <div class="frm_grp span_6">
                                        <p class="frm-head">CC <i class="mandatory">*</i></p>
                                        <div class="form_grid g_5">
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                            <div class="span_1">
                                                <input type="text" name="cc_email_id[]">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                        <?
                                        // echo $mailBody;
                                        ?>
									   <textarea name="mail_body" id="mail_body" required></textarea>
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
    <?php include_once("includes/popup.php"); ?>
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