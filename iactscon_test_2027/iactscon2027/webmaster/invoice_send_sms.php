<?php include_once("includes/source.php"); ?>

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
                        <li class="breadcrumb-item active" aria-current="page">Send SMS</li>
                    </ol>
                </nav>
                <h2>Send SMS</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
             <div class="page_top_wrap_right">
                <!-- <p><?php printi(); ?>Card Printed: <b>0</b></p>
                <p><?php check(); ?>Delevered: <b>0</b></p>
                <p><?php user(); ?>Total: <b>2</b></p> -->
                <a href="invoice_mail.php" class="badge_danger"><i class="fal fa-arrow-left"></i>Back</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>SMS Topics</h6>
                <button data-tab="acknowledgementsms" class="com_info_left_click icon_hover badge_primary active"><i class="fal fa-school"></i>Acknowledgement</button>
                <button data-tab="serviceconfirmationsms" class="com_info_left_click icon_hover badge_info action-transparent"><?php workshop() ?>Service Confirmation</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="acknowledgementsms">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php email() ?></span> Acknowledgement</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Number <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">SMS Body <i class="mandatory">*</i></p>
                                        <textarea name="" id=""></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <a href="#" class="badge_success formsubmit"><i class="fal fa-paper-plane"></i>Send SMS</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="serviceconfirmationsms">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php email() ?></span> Service Confirmation</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_6">
                                   <div class="frm_grp span_2">
                                        <p class="frm-head">Name <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Number <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_6">
                                        <p class="frm-head">SMS Body <i class="mandatory">*</i></p>
                                        <textarea name="" id=""></textarea>
                                    </div>
                                    <div class="frm_grp span_6 d-flex justify-content-end gp-10">
                                        <a href="#" class="badge_success formsubmit"><i class="fal fa-paper-plane"></i>Send SMS</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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