<?php
include_once("includes/source.php");
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once("../../includes/function.registration.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accompany.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.abstract.php');
include_once('includes/function.php');
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Footer Text & Google Map</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
    <script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
    <script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/manage_country.js"></script>
    <script language="javascript" src="<?=_BASE_URL_?>js/adminPanel/architecture.js"></script>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                         <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Footer Text & Google Map</li>
                    </ol>
                </nav>
                <h2>Manage Footer Text & Google Map</h2>
                <h6>Manage mail titles, and subjects.</h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="#" id="saveChanges"  class="badge_success"><i class="fal fa-save"></i>Save Changes</a>
            </div>
        </div>
        <div class="com_info_box" style="display:block">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <!-- <h5 class="com_info_box_head">
                        <n><span class="text_danger"><?php invoive() ?></span> Info Setup</n>
                    </h5> -->
                    <div class="com_info_box_inner span_4">
                        <h4 class="com_info_box_inner_sub_head"><span>Footer Text</span><a class="add mi-1"><?php add() ?> Add</a></h4>
                        <div class="form_grid g_3">
                            <div class="accm_add_box">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Test <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Status</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="food">
                                                <span class="checkmark">Active</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="food">
                                                <span class="checkmark">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                            </div>
                            <div class="accm_add_box">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Test <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Status</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="food">
                                                <span class="checkmark">Active</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="food">
                                                <span class="checkmark">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                            </div>
                            <div class="accm_add_box">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Test <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Status</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="food">
                                                <span class="checkmark">Active</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="food">
                                                <span class="checkmark">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <div class="com_info_box_inner span_4">
                        <h4 class="com_info_box_inner_sub_head"><span>Google Map Link</span><a class="add mi-1"></h4>
                        <div class="frm_grp span_">
                            <input>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>


</html>
<script>
  ///////////////////Section Edit///////////////////////////
   $(document).on('click', '.edimailsettinglistingBtn', function () {

    let editTemplateId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { editTemplateId: editTemplateId },
        success: function (response) {

            $('#edimailsettinglisting').html($(response).find('#edimailsettinglisting').html());

            // Re-initialize after DOM replacement
            initedimailsettinglisting();
               $('#edimailsettinglisting').fadeIn();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Section edit end///////////////////////////
         ///////////////////Section Edit///////////////////////////
   $(document).on('click', '.editdocheaderfooterBtn', function () {

    let editDocId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { editDocId: editDocId },
        success: function (response) {

            $('#editdocheaderfooter').html($(response).find('#editdocheaderfooter').html());

            // Re-initialize after DOM replacement
            initeditdocheaderfooter();
               $('#editdocheaderfooter').fadeIn();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Section edit end///////////////////////////
</script>