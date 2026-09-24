<?php
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__ . "/../includes/function.delegate.php");
include_once(__DIR__ . "/../includes/function.invoice.php");
include_once(__DIR__ . "/../includes/function.workshop.php");
include_once(__DIR__ . "/../includes/function.dinner.php");
include_once(__DIR__ . "/../includes/function.accompany.php");
include_once(__DIR__ . "/../includes/function.accommodation.php");
include_once(__DIR__ . "/../includes/function.abstract.php");
include_once('includes/function.php');
$delegateId = trim($_REQUEST['delegateId']);
$slipId     = trim($_REQUEST['slipId']);

$rowFetchUserDetails = getUserDetails($delegateId);
$invoiceDetails      = invoiceDetailsOfSlip($slipId);
$paymentDetails         = paymentDetails($slipId);
$services              = servicesOfSlip($slipId);
$totalSlipAmount      = invoiceAmountOfSlip($slipId);
$slipDetails         = slipDetails($slipId);

$paymentId              = $paymentDetails['id'];
$participantId            = trim($_REQUEST['id']);

$sqlListing = array();
$sqlListing['QUERY']         = "SELECT *
										  FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
										 WHERE `status` = 'A' 
										   AND `id` = '" . $participantId . "'";
$resultsListing     = $mycms->sql_select($sqlListing);
$rowParticipant     = $resultsListing[0];

//echo '<pre>'; print_r($rowParticipant);

//echo _BASE_URL_.'webmaster/';

?>
<link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>/webmaster/util/pellEditor/dist/pell.css">
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.8.5/tinymce.min.js"></script> -->
<script src="<?= _BASE_URL_ ?>/webmaster/util/pellEditor/dist/pell.js"></script>

<script src="<?= _BASE_URL_ ?>webmaster/lib/tinymce/tinymce.min.js"></script>
<script>
    $(document).ready(function() {
        $('input[type=radio][use=mailTypeChooser]').click(function() {
            var forWhat = $(this).attr('for');
            $('div[use=mailContainer]').hide();
            $("div[use=mailContainer][for='" + forWhat + "']").fadeIn('slow');
        });
    });

    function clearEmailAddr(obj) {
        var parent = $(obj).parent().closest('td');
        var seqCnt = $(obj).attr("seqCnt");
        $(parent).find("input[type=text][seqCnt='" + seqCnt + "']").val('');
    }
</script>




<script>
    tinymce.init({
        selector: '.mail_description',
        relative_urls: false,
        remove_script_host: false,
        width: 1000,
        height: 700,
        plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak ' +
            'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking ' +
            'table emoticons template paste help',
        toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | removeformat | help | ' +
            'link image media | code preview',
        menubar: 'file edit view insert format tools table help'
    });
</script>

<script>
    function clearEmailAddr(obj) {
        var parent = $(obj).parent().closest('td');
        var seqCnt = $(obj).attr("seqCnt");
        $(parent).find("input[type=text][seqCnt='" + seqCnt + "']").val('');
    }
</script>


<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Send Mail</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Sc.Programme</a></li>
                        <li class="breadcrumb-item">Manage Participant</li>
                        <li class="breadcrumb-item active" aria-current="page">Send Mail</li>
                    </ol>
                </nav>
                <h2>Send Mail</h2>
                <h6>Manage invitation, involvement,and sent mail.</h6>
            </div>
            <div class="page_top_wrap_right">
                <!-- <p><?php printi(); ?>Card Printed: <b>0</b></p>
                <p><?php check(); ?>Delevered: <b>0</b></p>
                <p><?php user(); ?>Total: <b>2</b></p> -->
                <a href="manage_participant.php" class="badge_danger"><i class="fal fa-arrow-left"></i>Back</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Mail Topics</h6>
                <button data-tab="participantInvitationMail" class="com_info_left_click icon_hover badge_default active"><?php workshop() ?></i>Invitation Mail</button>
                <?
                $sqlParticipantSchdule = array();
                $sqlParticipantSchdule['QUERY']         = "SELECT COUNT(*) AS schdl FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " WHERE `participant_id` = '" . $participantId . "'";
                $resultsParticipantSchdule     = $mycms->sql_select($sqlParticipantSchdule);
                $rowDetailsTotalCount         = $resultsParticipantSchdule[0];
                if ($rowDetailsTotalCount['schdl'] > 0) {
                ?>
                    <button data-tab="sendRegConfirmMail" class="com_info_left_click icon_hover badge_default action-transparent"><?php workshop() ?>Involvement Mail</button>
                <?
                }
                ?>
                <?

                $sqlEmails = array();
                $sqlEmails['QUERY']         = "SELECT COUNT(*) AS schdlMail FROM " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . "  WHERE `participantId` = '" . $participantId . "'";
                $resultsEmails     = $mycms->sql_select($sqlEmails);
                $rowEmails         = $resultsEmails[0];

                if ($rowEmails['schdlMail'] > 0) {
                ?>
                    <button data-tab="participantMailHistory" class="com_info_left_click icon_hover badge_default action-transparent"><?php workshop() ?>Sent Mail</button>
                <?
                }
                ?>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="participantInvitationMail">
                    <?
                    $msg = faculty_invitation_message($participantId, 'RETURN_TEXT');
                    $mailBody = trim($msg['MAIL_BODY']);
                    $mailBody = str_replace('[NAME]',  $rowParticipant['participant_full_name'], $mailBody);
                    // $smsBody = trim($msg['SMS_BODY'][0]);
                    ?>
                    <form name="sendMail" id="sendMail" action="manage_participant.process.php" method="post">
                        <input type="hidden" name="act" value="sendParticipantInvitationMail" />
                        <input type="hidden" name="participantId" id="participantId" value="<?= $participantId ?>" />
                        <input type="hidden" name="mailType" id="mailType" value="Invitation Letter" />

                        <div class="com_info_box_grid">
                            <div class="com_info_box_grid_box">
                                <h5 class="com_info_box_head">
                                    <n><span class="text_primary"><?php email() ?></span> Invitation </n>
                                </h5>
                                <div class="com_info_box_inner">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                            <textarea name="mail_subject" id="mail_subject"><?= $msg['MAIL_SUBJECT'] ?></textarea>

                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Name <i class="mandatory">*</i></p>
                                            <input type="text" name="user_full_name" value="<?=  $rowParticipant['participant_full_name'] ?>" />

                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                            <input type="text" name="user_email_id" value="<?= $rowParticipant['participant_email_id'] ?>" />

                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">CC <i class="mandatory">*</i></p>
                                            <div class="form_grid g_5">
                                                <div class="span_1">
                                                    <input type="text" name="cc_email_id[]" value="<?= $cfg['SC_SENDER_EMAIL'] ?>">
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
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                            <textarea name="mail_body" id="mail_description1" class="mail_description"><?= $mailBody ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4 d-flex justify-content-end gp-10">
                                            <button type="submit" value="SEND MAIL" name="submission" id="submissionbtn8" class="badge_success formsubmit">
                                                <i class="fal fa-paper-plane"></i> Send Mail
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="com_info_box" id="sendRegConfirmMail">
                    <?
                    $msg = national_faculty_residential_schedule_details_message($participantId, 'RETURN_TEXT');
                    $mailBody = trim($msg['MAIL_BODY']);
                    $mailBody = str_replace('[NAME]', $rowParticipant['participant_full_name'], $mailBody);
                    $smsBody = trim($msg['SMS_BODY'][0]);
                    ?>
                    <form name="sendMail" id="sendMail" action="manage_participant.process.php" method="post">
                        <input type="hidden" name="act" value="sendParticipantMail" />
                        <input type="hidden" name="participantId" id="participantId" value="<?= $participantId ?>" />
                        <input type="hidden" name="mailType" id="mailType" value="National Faculty (Registered with Residential Package)" />
                        <div class="com_info_box_grid">
                            <div class="com_info_box_grid_box">
                                <h5 class="com_info_box_head">
                                    <n><span class="text_secondary"><?php email() ?></span>Schedule Details Mail</n>
                                </h5>
                                <div class="com_info_box_inner">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Subject <i class="mandatory">*</i></p>
                                            <textarea name="mail_subject" id="mail_subject"><?= $msg['MAIL_SUBJECT'] ?></textarea>

                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Name <i class="mandatory">*</i></p>
                                            <input type="text" name="user_full_name" value="<?= $rowParticipant['participant_full_name'] ?>" />

                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                            <input type="text" name="user_email_id" value="<?= $rowParticipant['participant_email_id'] ?>" />

                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">SMS<i class="mandatory">*</i></p>
                                            <textarea name="sms_body" id="sms_body"><?= $smsBody ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">CC <i class="mandatory">*</i></p>
                                            <div class="form_grid g_5">
                                                <div class="span_1">
                                                    <input type="text" name="cc_email_id[]" value="<?= $cfg['SC_SENDER_EMAIL'] ?>">
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
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Mail Body <i class="mandatory">*</i></p>
                                            <textarea name="mail_body" id="national_faculty_residential_mail_body" class="mail_description"><?= $mailBody ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4 d-flex justify-content-end gp-10">
                                            <button type="submit" value="SEND MAIL" name="submission" id="submissionbtn8" class="badge_success formsubmit">
                                                <i class="fal fa-paper-plane"></i> Send Mail
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="com_info_box" id="participantMailHistory">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_success"><?php email() ?></span> Sent Mail</n>
                        </h5>
                        <div class="com_info_box_inner mb-3">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Name <i class="mandatory">*</i></p>
                                    <input value="<?= $rowParticipant['participant_full_name'] ?>" disabled>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail ID <i class="mandatory">*</i></p>
                                    <input value="<?= nl2br($rowParticipant['participant_email_id']) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <?
                        $sqlListing = array();
                        $sqlListing['QUERY']         = "SELECT *
                                                        FROM " . _DB_SP_PARTICIPANT_SCHEDULE_MAIL_ . " 
                                                        WHERE `participantId` = '" . $participantId . "'";
                        $resultsListing     = $mycms->sql_select($sqlListing);

                        if ($resultsListing) {
                        ?>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Sent On</th>
                                            <th>Email Id</th>
                                            <th>Email Type</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
                                        foreach ($resultsListing as $k => $rowMsg) {
                                            $mainEmail = trim(explode(',', $rowMsg['emailId'])[0]);

                                            $ccPart = trim(explode('CC: ', $rowMsg['emailId'])[1]);
                                            // Split the CC part into an array of email addresses by spaces
                                            $ccEmails = array_map('trim', explode(' ', $ccPart));
                                            // echo '<pre>';
                                            // print_r($ccEmails);
                                            $cnt++;
                                        ?>
                                            <tr>
                                                <td class="sl"><?= $cnt ?></td>
                                                <td align="left"><a href="javascript:void();" onclick="$('tr[use=mail<?= $rowMsg['id'] ?>]').slideToggle();"><?= $rowMsg['emailDate'] ?></a></td>
                                                <td align="left"><a href="javascript:void();" onclick="$('tr[use=mail<?= $rowMsg['id'] ?>]').slideToggle();"><?= $rowMsg['emailId'] ?></a></td>
                                                <td align="left"><?= $rowMsg['emailType'] ?></td>
                                            </tr>
                                            <tr class="tlisting" style="display:none;" use="mail<?= $rowMsg['id'] ?>">
                                                <td colspan="4">
                                                    <form use="resendEmail" name="sendMail" id="sendMail" action="manage_participant.process.php" method="post">
                                                        <?php if ($rowMsg['emailType'] == 'Invitation Mail') {
                                                        ?>
                                                            <input type="hidden" name="act" value="sendParticipantInvitationMail" />
                                                        <?php } else {
                                                        ?>
                                                            <input type="hidden" name="act" value="sendParticipantMail" />

                                                        <?php
                                                        } ?>
                                                        <input type="hidden" name="participantId" id="participantId" value="<?= $rowMsg['participantId'] ?>" />
                                                        <input type="hidden" name="mailType" id="mailType" value="<?= $rowMsg['emailType'] ?>" />
                                                        <input type="hidden" name="submission" id="submission" value="SEND MAIL" />
                                                        <table width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left">Subject</td>
                                                                    <td align="left">
                                                                        <?= $rowMsg['emailSubject'] ?>
                                                                        <textarea name="mail_subject" id="mail_subject" style="width:46%; padding:6px; display:none;"><?= $rowMsg['emailSubject'] ?></textarea>
                                                                        <input type="hidden" name="user_email_id" value="<?= $mainEmail ?>" style="width:46%; padding:6px;" seqCnt="1" />
                                                                        <?php if ($ccEmails[0] != '') {
                                                                            foreach ($ccEmails as $key => $ccEmail) {
                                                                        ?>
                                                                                <input type="hidden" name="cc_email_id[]" value="<?= $ccEmail ?>" style="width:46%; padding:6px;" seqCnt="1" />

                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left">&nbsp;</td>
                                                                    <td align="left">
                                                                        <table>
                                                                            <tr>
                                                                                <td style="-moz-box-shadow: 0px 0px 8px #000000; -webkit-box-shadow: 0px 0px 8px #000000; box-shadow: 0px 0px 8px #000000;">
                                                                                    <?= $rowMsg['emailContent'] ?>
                                                                                    <textarea name="mail_body" style="display:none;"><?= $rowMsg['emailContent'] ?></textarea>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left">&nbsp;</td>
                                                                    <td align="left">
                                                                        <div class="d-flex justify-content-end gp-10">
                                                                            <input type="submit" value="RE-SEND MAIL" name="submission" id="submissionbtn8" class="badge_success formsubmit">

                                                                            </input>
                                                                            <input type="submit" value="DOWNLOAD PDF" name="submission" id="submissionbtn9" class="badge_secondary formsubmit">
                                                                            </input>
                                                                            <input type="submit" value="DOWNLOAD DOC" name="submission" id="submissionbtn9" class="badge_secondary formsubmit">
                                                                            </input>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </form>
                                                </td>
                                            </tr>
                                        <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        <? } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    function resendMail(obj) {
        var frm = $(obj).parent().closest("form");

        console.log($(frm).attr('action'));
        console.log($(frm).serialize());

        $.ajax({
                type: 'POST',
                url: $(frm).attr('action'),
                data: $(frm).serialize()
            })
            .done(function(data) {
                console.log(data);
                window.location.reload();
            })
            .fail(function(data) {
                console.log("submit address >> fail");
            });
    }
</script>
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