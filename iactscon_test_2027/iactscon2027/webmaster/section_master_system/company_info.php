<?php
session_start();
?>
<?php

include_once('includes/init.php');
include_once("../../includes/function.invoice.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.registration.php');

page_header('System Master');
include_once("includes/popup.php");

global $cfg, $mycms;
$processPage = "manage_company_information.process.php";

?>


<script src="<?= _BASE_URL_ ?>webmaster/lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	tinymce.init({
		selector: '.textArea',
		width: 500,
		height: 250,
		plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak ' +
			'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking ' +
			'table emoticons template paste help',
		toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
			'alignleft aligncenter alignright alignjustify | ' +
			'bullist numlist outdent indent | removeformat | help | ' +
			'link image media | code preview',
		menubar: 'file edit view insert format tools table'
	});
	
</script>




<div class="body_wrap">
    <div class="page_top_wrap mb-3">
        <div class="page_top_wrap_left">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Settings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Company Information</li>
                </ol>
            </nav>
            <h2>Event Configuration</h2>
            <h6>Manage master data, invoice settings, and system content.</h6>
        </div>
        <form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] . $processPage ?>" id="frmtypeedit"
            enctype="multipart/form-data">

            <input type="hidden" name="act" value="edit_template" />
            <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>" />
            <input type="hidden" name="chk_country_edit" id="chk_country_edit" value="3" />
            <input type="hidden" name="country_name_or" id="country_name_or" value="<?= $row['country_name'] ?>" />
            <div class="page_top_wrap_right">
                <input type="submit" name="Save" id="typeEditBtn" value="Save Changes" class="badge_success" />
                <!-- <a href="#" type="submit" name="Save" id="Save" class="badge_success"><?php save(); ?>Save Changes</a> -->
            </div>
    </div>

    <div class="com_info_wrap">
        <div class="com_info_left">
            <h6>Settings Menu</h6>
            <button type="button" data-tab="ef"
                class="com_info_left_click icon_hover badge_primary active" onclick="openTypeEdit()"><?php event() ?>Event Profile</button>
            <button type="button" data-tab="fi"
                class="com_info_left_click icon_hover badge_secondary action-transparent" onclick="openTypeEdit()"><?php credit() ?>Finance &
                Invoice</button>
            <button type="button" data-tab="md"
                class="com_info_left_click icon_hover badge_success action-transparent" onclick="openTypeEdit()"><?php calendar() ?>Modules &
                Dates</button>
            <button type="button" data-tab="cc"
                class="com_info_left_click icon_hover badge_info action-transparent" onclick="openTypeEdit()"><?php invoive() ?>Content
                CMS</button>
            <button type="button" data-tab="br"
                class="com_info_left_click icon_hover badge_danger action-transparent" onclick="openBranding()"><?php branding() ?>Branding</button>
        </div>

        <div class="com_info_right">


            <div class="com_info_box active" id="ef">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_primary"><?php event() ?></span> Profile</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <div class="form_grid g_6">
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Conference Name <i class="mandatory">*</i></p>
                                    <input type="text" id="company_conf_name" name="company_conf_name"
                                        value="<?= $row['company_conf_name'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Conference Full Name <i class="mandatory">*</i></p>
                                    <input type="text" id="company_conf_full_name" name="company_conf_full_name"
                                        value="<?= $row['company_conf_full_name'] ?>">
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Contact Number <i class="mandatory">*</i></p>
                                    <input type="text" id="company_conf_mobileno" name="company_conf_mobileno"
                                        value="<?= $row['company_conf_mobileno'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Conference Venue <i class="mandatory">*</i></p>
                                    <input type="text" id="company_conf_venue" name="company_conf_venue"
                                        value="<?= $row['company_conf_venue'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Registration Email <i class="mandatory">*</i></p>
                                    <input type="text" id="company_conf_email" name="company_conf_email"
                                        value="<?= $row['company_conf_email'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Scientific Email <i class="mandatory">*</i></p>
                                    <input type="text" id="scientific_sender_email" name="scientific_sender_email"
                                        value="<?= $row['scientific_sender_email'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Conference Start Date <i class="mandatory">*</i></p>
                                    <input type="date" id="conf_start_date" name="conf_start_date"
                                        value="<?= $row['conf_start_date'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Conference End Date <i class="mandatory">*</i></p>
                                    <input type="date" id="conf_end_date" name="conf_end_date"
                                        value="<?= $row['conf_end_date'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">CGST/SGST <i class="mandatory">*</i></p>
                                    <input type="text" id="cgst_percentage" name="cgst_percentage"
                                        value="<?= $row['cgst_percentage'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">IGST <i class="mandatory">*</i></p>
                                    <input type="text" id="igst_percentage" name="igst_percentage"
                                        value="<?= $row['igst_percentage'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">GST Type</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" type="radio" id="html" class="unique-class"
                                                name="igst_flag" value="0" <?php if ($row['igst_flag'] == 0) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">COST/SGST</span>
                                        </label>
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" class="unique-class" id="css" name="igst_flag" value="1"
                                                <?php if ($row['igst_flag'] == 1) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">IGST</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">GST Flag</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" id="html" class="unique-class" name="gst_flag" value="1"
                                                <?php if ($row['gst_flag'] == 1) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Added GST</span>
                                        </label>
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" class="unique-class" id="css" name="gst_flag" value="2"
                                                <?php if ($row['gst_flag'] == 2) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Inclusive GST</span>
                                        </label>
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" class="unique-class" id="javascript" name="gst_flag"
                                                value="3" <?php if ($row['gst_flag'] == 3) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Without GST</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Handling Percentage</p>
                                    <input type="text" id="internet_handling_percentage"
                                        name="internet_handling_percentage"
                                        value="<?= $row['internet_handling_percentage'] ?>" required>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Website Link <i class="mandatory">*</i></p>
                                    <input type="text" name="conference_site_url_link" id="conference_site_url_link"
                                        value="<?php echo $row['conference_site_url_link']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Color <i class="mandatory">*</i></p>
                                    <input type="color" name="color" id="color" value="#<?php echo $row['color']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dark Color <i class="mandatory">*</i></p>
                                    <input type="color" name="dark_color" id="dark_color"
                                        value="#<?php echo $row['dark_color']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Light Color <i class="mandatory">*</i></p>
                                    <input type="color" name="light_color" id="light_color"
                                        value="#<?php echo $row['light_color']; ?>">
                                </div>
                                <div class="frm_grp span_6">
                                    <p class="frm-head">Registration Fields</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" type="checkbox" id="address" name="fields[]"
                                                value="Address" <?php if (in_array("Address", $available_registration_fields)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">ADDRESS</span>
                                        </label>
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" id="country" name="fields[]" value="Country" <?php if (in_array("Country", $available_registration_fields)) {
                                                echo 'checked';
                                            } ?>>
                                            <span class="checkmark">COUNTRY</span>
                                        </label>
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" id="state" name="fields[]" value="State" <?php if (in_array("State", $available_registration_fields)) {
                                                echo 'checked';
                                            } ?>>
                                            <span class="checkmark">STATE</span>
                                        </label>
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" id="city" name="fields[]" value="City" <?php if (in_array("City", $available_registration_fields)) {
                                                echo 'checked';
                                            } ?>>
                                            <span class="checkmark">CITY</span>
                                        </label>
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" id="pin" name="fields[]" value="Pin" <?php if (in_array("Pin", $available_registration_fields)) {
                                                echo 'checked';
                                            } ?>>
                                            <span class="checkmark">POSTAL CODE</span>
                                        </label>
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" id="gender" name="fields[]" value="Gender" <?php if (in_array("Gender", $available_registration_fields)) {
                                                echo 'checked';
                                            } ?>>
                                            <span class="checkmark">GENDER</span>
                                        </label>
                                        <label class="cus_check outfrm_check flex-unset">
                                            <input type="checkbox" id="Food" name="fields[]" value="Food" <?php if (in_array("Food", $available_registration_fields)) {
                                                echo 'checked';
                                            } ?>>
                                            <span class="checkmark flex-unset">FOOD PREFERENCE</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_primary"><i class="fal fa-grip-lines"></i></span> Page Headers
                        </h5>
                        <div class="com_info_box_inner">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Registration Category Title</p>
                                    <input type="text" name="tariff_category_title" id="tariff_category_title"
                                        value="<?php echo $row['tariff_category_title']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Registration Form Title</p>
                                    <input type="text" name="tariff_user_details_title" id="tariff_user_details_title"
                                        value="<?php echo $row['tariff_user_details_title']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Workshop Title</p>
                                    <input type="text" name="tariff_workshop_title" id="tariff_workshop_title"
                                        value="<?php echo $row['tariff_workshop_title']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">ACCompant Title</p>
                                    <input type="text" name="tariff_accompany_title" id="tariff_accompany_title"
                                        value="<?php echo $row['tariff_accompany_title']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Accommodation Title</p>
                                    <input type="text" name="tariff_accommodation_title" id="tariff_accommodation_title"
                                        value="<?php echo $row['tariff_accommodation_title']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Banquet Title</p>
                                    <input type="text" name="tariff_banquet_title" id="tariff_banquet_title"
                                        value="<?php echo $row['tariff_banquet_title']; ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Cart Title</p>
                                    <input type="text" name="tariff_cart_title" id="tariff_cart_title"
                                        value="<?php echo $row['tariff_cart_title']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="com_info_box" id="fi">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_secondary"><?php credit() ?></span> Finance & Invoice</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Invoice Company Name <i class="mandatory">*</i></p>
                                    <input type="text" id="invoice_company_name" name="invoice_company_name"
                                        value="<?= $row['invoice_company_name'] ?>" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Invoice Prefix <i class="mandatory">*</i></p>
                                    <input type="text" id="invoice_company_name_prefix"
                                        name="invoice_company_name_prefix"
                                        value="<?= $row['invoice_company_name_prefix'] ?>" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Invoice Number Format <i class="mandatory">*</i></p>
                                    <input type="text" id="invoive_number_format" name="invoive_number_format"
                                        value="<?= $row['invoive_number_format'] ?>" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Invoice PAN Number <i class="mandatory">*</i></p>
                                    <input type="text" id="pan_number" name="pan_number"
                                        value="<?= $row['pan_number'] ?>" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Invoice GST Number <i class="mandatory">*</i></p>
                                    <input type="text" id="gst_number" name="gst_number"
                                        value="<?= $row['gst_number'] ?>" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">HSN/SAC Code</p>
                                    <input type="text" id="invoice_conf_hsn" name="invoice_conf_hsn"
                                        value="<?= $row['invoice_conf_hsn'] ?>">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Invoice Address</p>
                                    <input type="text" id="invoice_address" name="invoice_address"
                                        value="<?= $row['invoice_address'] ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_secondary"><i class="fal fa-university"></i></span>Bank Details
                        </h5>
                        <div class="com_info_box_inner">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Bank Name</p>
                                    <input type="text" id="invoice_bank_name" name="invoice_bank_name"
                                        value="<?= $row['invoice_bank_name'] ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Account Number</p>
                                    <input type="text" id="invoice_bank_account_number"
                                        name="invoice_bank_account_number"
                                        value="<?= $row['invoice_bank_account_number'] ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Branch Name</p>
                                    <input type="text" id="invoice_bank_branch_name" name="invoice_bank_branch_name"
                                        value="<?= $row['invoice_bank_branch_name'] ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">IFSC Code</p>
                                    <input type="text" id="invoice_bank_ifsc_code" name="invoice_bank_ifsc_code"
                                        value="<?= $row['invoice_bank_ifsc_code'] ?>">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Beneficiary Name</p>
                                    <input type="text" id="invoice_beneficiary" name="invoice_beneficiary"
                                        value="<?= $row['invoice_beneficiary'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="com_info_box" id="md">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_success"><?php invoive() ?></span> Configuration</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <div class="form_grid g_3">
                                <div class="frm_grp">
                                    <p class="frm-head">Submission Deadline</p>
                                    <input type="date" id="abstract_submission_date" name="abstract_submission_date"
                                        value="<?= $row['abstract_submission_date'] ?>">
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Confirmation Date</p>
                                    <input type="date" id="abstract_confirmation_date" name="abstract_confirmation_date"
                                        value="<?= $row['abstract_confirmation_date'] ?>">
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Abstract Email</p>
                                    <input type="text" id="abstract_sender_email" name="abstract_sender_email"
                                        value="<?= $row['abstract_sender_email'] ?>" required>
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Title Word Limit</p>
                                    <input type="number" id="abstract_title_word_limit" name="abstract_title_word_limit"
                                        value="<?= $row['abstract_title_word_limit'] ?>" />
                                    <select name="abstract_word_title_type" id="abstract_word_title_type">
                                        <option value="" selected="">Select Type</option>
                                        <option value="word" <?php if ($row['abstract_word_title_type'] == 'word') {
                                            echo 'selected';
                                        } ?>>Word</option>
                                        <option value="character" <?php if ($row['abstract_word_title_type'] == 'character') {
                                            echo 'selected';
                                        } ?>>
                                            Character</option>

                                    </select>
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Abstract Word Limit</p>
                                    <input type="number" id="abstract_total_word_limit" name="abstract_total_word_limit"
                                        value="<?= $row['abstract_total_word_limit'] ?>" />
                                    <select name="abstract_total_word_type" id="abstract_total_word_type">
                                        <option value="" selected="">Select Type</option>
                                        <option value="word" <?php if ($row['abstract_total_word_type'] == 'word') {
                                            echo 'selected';
                                        } ?>>Word</option>
                                        <option value="character" <?php if ($row['abstract_total_word_type'] == 'character') {
                                            echo 'selected';
                                        } ?>>
                                            Character</option>

                                    </select>
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">HOD consent file type</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check outfrm_check">
                                            <input type="checkbox" type="checkbox" id="" name="consent_files[]"
                                                value="pdf" <?php if (in_array("pdf", $hod_consent_file_types)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">PDF</span>
                                        </label>
                                        <label class="cus_check outfrm_check">
                                            <input type="checkbox" id="" name="consent_files[]" style="width: 20px;"
                                                value="image" <?php if (in_array("image", $hod_consent_file_types)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Image</span>
                                        </label>
                                        <label class="cus_check outfrm_check">
                                            <input type="checkbox" id="" name="consent_files[]" style="width: 20px;"
                                                value="word" <?php if (in_array("word", $hod_consent_file_types)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Word</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Abstract file type</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check outfrm_check">
                                            <input type="checkbox" id="" name="abstract_files[]" style="width: 20px;"
                                                value="pdf" <?php if (in_array("pdf", $abstract_file_types)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">PDF</span>
                                        </label>
                                        <label class="cus_check outfrm_check">
                                            <input type="checkbox" id="" name="abstract_files[]" style="width: 20px;"
                                                value="image" <?php if (in_array("image", $abstract_file_types)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Image</span>
                                        </label>
                                        <label class="cus_check outfrm_check">
                                            <input type="checkbox" type="checkbox" id="" name="abstract_files[]"
                                                style="width: 20px;" value="word" <?php if (in_array("word", $abstract_file_types)) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Word</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Set Guideline PDF</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" id="html" class="unique-class1" style="width: 30px;"
                                                name="guideline_pdf_flag" value="1" <?php if ($row['guideline_pdf_flag'] == 1) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">PDF Link</span>
                                        </label>
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" class="unique-class1" id="css" style="width: 30px;"
                                                name="guideline_pdf_flag" value="2" <?php if ($row['guideline_pdf_flag'] == 2) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">PDF File</span>
                                        </label>
                                        <label class="cus_check outfrm_radio">
                                            <input type="radio" class="unique-class1" id="css" style="width: 30px;"
                                                name="guideline_pdf_flag" value="0" <?php if ($row['guideline_pdf_flag'] == 0) {
                                                    echo 'checked';
                                                } ?>>
                                            <span class="checkmark">Remove Guideline</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp">

                                    <input type="text" id="abstract_guideline_pdf" name="abstract_guideline_pdf" style="width:60%;display: <?php if (!empty($row['abstract_guideline_pdf'])) {
                                        echo "block";
                                    } else {
                                        echo "none";
                                    } ?>;" value="<?= $row['abstract_guideline_pdf'] ?>" />

                                    <span style="display: <?php if (!empty($row['abstract_guideline_pdf_file'])) {
                                        echo "block";
                                    } else {
                                        echo "none";
                                    } ?>;" id="pdf_file">
                                        <input type="file" id="abstract_guideline_pdf_file"
                                            name="abstract_guideline_pdf_file" style="width:60%;" accept=".pdf" />
                                        <?php
                                        if (!empty($row['abstract_guideline_pdf_file'])) {
                                            ?>
                                            <a href="<?= '../../' . $cfg['FILES.ABSTRACT.REQUEST'] . $row['abstract_guideline_pdf_file']; ?>"
                                                target="_blank">Download</a>
                                            <?php
                                        }
                                        ?>
                                    </span>
                                </div>
                                <!-- <div class="frm_grp">
                                            <p class="frm-head">PDF File</p>
                                            <input type="text">
                                        </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_success"><?php calendar() ?></span>Lunch & Dinner Dates</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span>
                            
                            <a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=addDate&purpose=Lunch" class="add mi-1"><?php add(); ?>Add Date</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Date</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $sql_cal = array();
                                            $sql_cal['QUERY']	=	"SELECT *  
                                                            FROM " . _DB_INCLUSION_DATE_ . " 
                                                            WHERE status != 'D' AND `purpose`='Lunch'";
                                            $res_cal			=	$mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                        <tr>
                                            <td class="sl"><?= $i ?></td>
                                            <td><?= $rowsl['date'] ?></td>
                                            <td>
                                                <div class="action_div">
                                                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_company_information.process.php?act=<?= ($rowsl['status'] == 'A') ? 'InactiveDate' : 'ActiveDate' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>
                                                    <!-- <span
                                                        class="badge_padding badge_success w-max-con text-uppercase">Active</span> -->
                                                </div>
                                            </td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="javascript:void(null);" class="icon_hover badge_danger action-transparent br-5 w-auto" onclick="redirectionOfLink(this)" ehref="manage_company_information.process.php?act=deleteDate&id=<?= $rowsl['id']; ?>">
											<span title="Remove" class="icon-trash-stroke" onclick="return confirm('Do you really want to remove this record');"></span><?php delete(); ?></a>
                                                    <!-- <a href="#"
                                                        class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                                                </div>
                                            </td>
                                        </tr>
                                        <?
								$i++;
							}
							?>
                                    </tbody>
                                </table>
                                <br>
			  <table>
                  <h4 class="com_info_box_inner_sub_head"><span>Manage Dinner Dates</span>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Date</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
							$sql_cal = array();
							$sql_cal['QUERY']	=	"SELECT *  
											FROM " . _DB_INCLUSION_DATE_ . " 
											WHERE status != 'D' AND `purpose`='Dinner'";
							$res_cal			=	$mycms->sql_select($sql_cal);
							$i = 1;

							foreach ($res_cal as $key => $rowsl) {
							?>
                                        <tr>
                                            <td class="sl"><?= $i ?></td>
                                            <td><?= $rowsl['date'] ?></td>
                                            <td>
                                                <div class="action_div">
                                                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_company_information.process.php?act=<?= ($rowsl['status'] == 'A') ? 'InactiveDate' : 'ActiveDate' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>
                                                    <!-- <span
                                                        class="badge_padding badge_success w-max-con text-uppercase">Active</span> -->
                                                </div>
                                            </td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="javascript:void(null);" class="icon_hover badge_danger action-transparent br-5 w-auto" onclick="redirectionOfLink(this)" ehref="manage_company_information.process.php?act=deleteDate&id=<?= $rowsl['id']; ?>">
											<span title="Remove" class="icon-trash-stroke" onclick="return confirm('Do you really want to remove this record');"></span><?php delete(); ?></a>
                                                    <!-- <a href="#"
                                                        class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                                                </div>
                                            </td>
                                        </tr>
                                        <?
								$i++;
							}
							?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="com_info_box" id="cc">
                <h5 class="com_info_box_head w-100">
                    <n><span class="text_info"><?php invoive() ?></span> Content CMS</n>
                </h5>
                <div class="com_info_box_content_sec">
                    <div class="com_info_box_content_sec_left">
                        <h6>Content Sections</h6>
                        <button type="button" data-tab="pi"
                            class="com_info_box_content_sec_left_click icon_hover badge_default active">Payment
                            Instructions</button>
                        <button type="button" data-tab="tp"
                            class="com_info_box_content_sec_left_click icon_hover badge_default action-transparent">Terms
                            & Policies</button>
                        <button type="button" data-tab="en"
                            class="com_info_box_content_sec_left_click icon_hover badge_default action-transparent">Email
                            Notifications</button>
                    </div>
                    <div class="com_info_box_content_sec_right">
                        <div class="com_info_box_content_sec_right_box active" id="pi">
                            <div class="com_info_box_grid_box">
                                <div class="com_info_box_inner">
                                    <h5 class="com_info_box_content_sec_right_box_head">Payment Methods</h5>
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Cheque Information</p>
                                            <textarea name="cheque_info" class="textArea "
                                                id="cheque_info"><?php echo $row['cheque_info']; ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">UPI Information</p>
                                            <textarea name="neft_info" class="txtarea textArea "
                                                id="neft_info"><?php echo $row['neft_info']; ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">NEFT/RTGS Information</p>
                                            <textarea name="neft_info" class="txtarea textArea "
                                                id="neft_info"><?php echo $row['neft_info']; ?></textarea>
                                        </div>
                                        <?php 
                                        $offline_payments = json_decode($cfg['PAYMENT.METHOD']); 
											//print_r($offline_payments);
                                        ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Offline Payment Options Enabled</p>
                                            <div class="cus_check_wrap">
                                                <label class="cus_check outfrm_check">
                                                    <input type="checkbox" id="card" name="payments[]" value="Card" <?php if (in_array("Card", $offline_payments)) {
                                                        echo 'checked';
                                                    } ?>>
                                                    <span class="checkmark">Card</span>
                                                </label>
                                                <label class="cus_check outfrm_check">
                                                    <input type="checkbox" id="cash" name="payments[]" value="Cash" <?php if (in_array("Cash", $offline_payments)) {
                                                        echo 'checked';
                                                    } ?>>
                                                    <span class="checkmark">Cash</span>
                                                </label>
                                                <label class="cus_check outfrm_check">
                                                    <input type="checkbox" id="cheque/dd" name="payments[]"
                                                        value="Cheque/DD" <?php if (in_array("Cheque/DD", $offline_payments)) {
                                                            echo 'checked';
                                                        } ?>>
                                                    <span class="checkmark">Cheque/DD</span>
                                                </label>
                                                <label class="cus_check outfrm_check">
                                                    <input type="checkbox" id="neft" name="payments[]" value="Neft" <?php if (in_array("Neft", $offline_payments)) {
                                                        echo 'checked';
                                                    } ?>>
                                                    <span class="checkmark">NEFT</span>
                                                </label>
                                                <label class="cus_check outfrm_check">
                                                    <input type="checkbox" id="upi" name="payments[]" value="Upi" <?php if (in_array("Upi", $offline_payments)) {
                                                        echo 'checked';
                                                    } ?>>
                                                    <span class="checkmark">UPI</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_content_sec_right_box" id="tp">
                            <div class="com_info_box_grid_box">
                                <div class="com_info_box_inner">
                                    <h5 class="com_info_box_content_sec_right_box_head">Terms & Privacy</h5>
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Terms & Condition Information</p>
                                            <textarea name="terms_info" class="textArea"
                                                id="terms_info"><?php echo $row['terms_info']; ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Privacy Policy Information</p>
                                            <textarea name="privacy_info" class="textArea"
                                                id="privacy_info"><?php echo $row['privacy_info']; ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Cancellation Policy</p>
                                            <textarea name="cancellation_page_info" class="textArea"
                                                id="cancellation_page_info"><?php echo $row['cancellation_page_info']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_content_sec_right_box" id="en">
                            <div class="com_info_box_grid_box">
                                <div class="com_info_box_inner">
                                    <h5 class="com_info_box_content_sec_right_box_head">System Notifications</h5>
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Success Page Message</p>
                                            <textarea name="success_page_info" class="textArea"
                                                id="success_page_info"><?php echo $row['success_page_info']; ?></textarea>
                                            </textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Payment Failure Message</p>
                                            <textarea name="payment_failure_info" class="textArea"
                                                id="payment_failure_info"><?php echo $row['payment_failure_info']; ?></textarea>></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Abstract Submission Success</p>
                                            <textarea name="abstract_submission_success_info" class="textArea"
                                                id="abstract_submission_success_info"><?php echo $row['abstract_submission_success_info']; ?></textarea>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Email: Registration Confirmation</p>
                                            <textarea></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <form name="frmbrandingimages" method="post"
                action="<?= $cfg['SECTION_BASE_URL'] ?>manage_icon_setting.process.php" id="frmbrandingimages"
                enctype="multipart/form-data">

                <input type="hidden" name="act" value="update_branding_images" />


                <div class="com_info_box" id="br">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_danger"><?php branding() ?></span> Images & Logistics</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <?php
                                $sql = array();
                                $sql['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                                    WHERE status IN ('A','I')
                                    ORDER BY id DESC";

                                $result = $mycms->sql_select($sql);

                                $brandingImages = array();


                                if ($result) {
                                    foreach ($result as $row) {

                                        $brandingImages[$row['title']] = [
                                            'id' => $row['id'],
                                            'image' => '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['image']
                                        ];
                                    }
                                }

                                /* Define all branding fields */
                                $brandingFields = [
                                    [
                                        'title' => 'Webmaster Background',
                                        'input_id' => 'webmaster_background',
                                        'note' => '1920x1080px recommended'
                                    ],
                                    [
                                        'title' => 'Invoice Signature',
                                        'input_id' => 'invoice_signature',
                                        'note' => 'Transparent PNG signature'
                                    ],
                                    [
                                        'title' => 'Online Payment Logo',
                                        'input_id' => 'payment_gateway',
                                        'note' => 'Payment gateway logos'
                                    ],
                                    [
                                        'title' => 'Registration Failure Image',
                                        'input_id' => 'failure_image',
                                        'note' => 'Shown on error pages'
                                    ],
                                    [
                                        'title' => 'QR Code',
                                        'input_id' => 'qr_code',
                                        'note' => 'For UPI payments'
                                    ],
                                ];


                                ?>

                                <div class="com_info_branding_wrap form_grid g_3">

                                    <?php foreach ($brandingFields as $field):

                                        $title = $field['title'];
                                        $inputId = $field['input_id'];
                                        $note = $field['note'];

                                        $imageData = isset($brandingImages[$title]) ? $brandingImages[$title] : null;

                                        $imagePath = $imageData ? $imageData['image'] : '';
                                        $imageId = $imageData ? $imageData['id'] : '';
                                        ?>

                                        <div class="com_info_branding_box">
                                            <input type="hidden" name="branding_ids[<?= $inputId ?>]"
                                                value="<?= $imageId ?>">
                                            <!-- PREVIEW -->
                                            <div class="branding_image_preview"
                                                style="<?= !empty($imagePath) ? 'display:block;' : 'display:none;' ?>">

                                                <!-- TITLE ON PREVIEW -->
                                                <div class="preview_title">
                                                    <?= $title ?>
                                                </div>

                                                <?php if (!empty($imagePath)):
                                                    $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                                                    ?>

                                                    <?php if (in_array($ext, ['mp4', 'webm'])): ?>
                                                        <video controls>
                                                            <source src="<?= $imagePath ?>">
                                                        </video>
                                                    <?php else: ?>
                                                        <img src="<?= $imagePath ?>" alt="<?= $title ?>">
                                                    <?php endif; ?>

                                                <?php endif; ?>

                                                <button type="button" class="preview_delete_btn">
                                                    <i class="fal fa-trash-alt"></i>
                                                </button>

                                            </div>

                                            <!-- UPLOAD -->
                                            <div class="branding_image_upload"
                                                style="<?= !empty($imagePath) ? 'display:none;' : 'display:block;' ?>">

                                                <input style="display:none;" id="<?= $inputId ?>" type="file"
                                                    name="<?= $inputId ?>" accept=".mp4,.gif,.jpg,.jpeg,.png,.webm"
                                                    onchange="loadFilePreview(event)">

                                                <label for="<?= $inputId ?>">
                                                    <span><?php upload(); ?></span>
                                                    <n><?= $title ?></n>
                                                    <g><?= $note ?></g>
                                                </label>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" name="Save" id="brandingBtn" value="Save Changes" class="mt-2 badge_success" />
            </form>
        </div>



    </div>

</div>

<?php include_once("includes/popup.php"); ?>
<?php
// ======================== ADD INCLUSION DATES =====================
function addDate($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_company_information.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="insertDate" />
		<input type="hidden" name="purpose" value="<?= $_REQUEST['purpose'] ?>" />
		<input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
		<?php
		foreach ($searchArray as $key => $val) {
		?>
			<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
		<?php
		}
		?>
		<table width="50%" class="tborder">
			<thead>
				<tr>
					<td colspan="2" align="left" class="tcat">Add <?= $_REQUEST['purpose'] ?> Date</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr>
								<td width="40%" align="left">
									Date <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="date" name="date" id="date" style="width:80%;" required />
								</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td width="40%"></td>
					<td align="left">
						<a href="<?= $_SERVER['PHP_SELF'] . "?show=edit&id=1" ?>?pageno=<?= $_REQUEST['pageno'] ?>">
							<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
						&nbsp;
						<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</form>
<?php
}

 if (!empty($_SESSION['success_message'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    toastr.success("<?= $_SESSION['success_message']; ?>");
});
</script>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>
<style>
    /* .branding_image_preview {
        position: relative;
        aspect-ratio: 1 / .6;
        border-radius: 10px;
        overflow: hidden;

    } */
    .preview_title {
        position: absolute;
        left: 15px;
        bottom: 15px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 6px;
        z-index: 2;
    }

    .branding_image_preview img,
    .branding_image_preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* .preview_delete_btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,0,0,0.9);
    color: #fff;
    border: none;
    padding: 6px 8px;
    border-radius: 6px;
    cursor: pointer;
} */
</style>
<script>

    function loadFilePreview(event) {

        var input = event.target;
        var file = input.files[0];
        if (!file) return;

        var brandingBox = input.closest('.com_info_branding_box');
        var previewContainer = brandingBox.querySelector('.branding_image_preview');
        var uploadContainer = brandingBox.querySelector('.branding_image_upload');

        previewContainer.style.setProperty('display', 'block');
        uploadContainer.style.setProperty('display', 'none');

        var fileURL = URL.createObjectURL(file);
        var fileType = file.type;

        previewContainer.innerHTML = '';

        // Get title from upload label
        var titleText = brandingBox.querySelector('label n').innerText;

        var titleDiv = document.createElement('div');
        titleDiv.classList.add('preview_title');
        titleDiv.innerText = titleText;

        previewContainer.appendChild(titleDiv);

        var media;

        if (fileType.startsWith('image/')) {
            media = document.createElement('img');
        } else if (fileType.startsWith('video/')) {
            media = document.createElement('video');
            media.controls = true;
        } else {
            alert("Unsupported file type");
            return;
        }

        media.src = fileURL;
        media.style.width = "100%";
        media.style.height = "100%";
        media.style.objectFit = "cover";

        var deleteBtn = document.createElement('button');
        deleteBtn.type = "button";
        deleteBtn.classList.add('preview_delete_btn');
        deleteBtn.innerHTML = '<i class="fal fa-trash-alt"></i>';

        previewContainer.appendChild(media);
        previewContainer.appendChild(deleteBtn);

        setTimeout(function () {
            URL.revokeObjectURL(fileURL);
        }, 1000);
    }


    // DELETE PREVIEW (UI ONLY)
    document.addEventListener('click', function (e) {

        if (e.target.closest('.preview_delete_btn')) {

            e.preventDefault();

            var brandingBox = e.target.closest('.com_info_branding_box');
            var previewContainer = brandingBox.querySelector('.branding_image_preview');
            var uploadContainer = brandingBox.querySelector('.branding_image_upload');
            var fileInput = brandingBox.querySelector('input[type="file"]');

            previewContainer.innerHTML = '';
            previewContainer.style.setProperty('display', 'none');
            uploadContainer.style.setProperty('display', 'block');

            fileInput.value = '';
        }

    });

</script>



<!-- <script>
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
</script> -->
<script>
    window.onload = function () {
         document.getElementById('frmbrandingimages').style.display = "none";
        var brandingBtn = document.querySelector('[data-tab="br"]');
        if (brandingBtn && brandingBtn.classList.contains('active')) {

            document.getElementById('frmbrandingimages').style.display = "block";
            document.getElementById('frmtypeedit').style.display = "none";
        } else {
            document.getElementById('frmtypeedit').style.display = "block";
        }



    }
    function openBranding() {

    document.getElementById('frmbrandingimages').style.display = "block";
    document.getElementById('frmtypeedit').style.display = "none";

  
}
function openTypeEdit() {

    document.getElementById('frmbrandingimages').style.display = "none";
    document.getElementById('frmtypeedit').style.display = "block";

}
</script>
<?php
page_footer();