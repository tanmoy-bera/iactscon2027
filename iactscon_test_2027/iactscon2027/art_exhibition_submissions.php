<?
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

/* ---------------------------------------------------------------------
   Pull active categories for Step 2 (and to drive the Step 3 label).
   Uses the same $mycms->sql_select() convention as the rest of the app.
   --------------------------------------------------------------------- */
$sqlCat = array();
$sqlCat['QUERY']    = "SELECT `id`, `category_name`, `category_description`, `category_notes`
                        FROM `art_exhibition_category`
                        WHERE `status` = ?
                        ORDER BY `id` ASC";
$sqlCat['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
$resultCat          = $mycms->sql_select($sqlCat);

// No icon column in the table yet, so map known names to an icon class.
// Anything not listed falls back to fa-image.
$categoryIconMap = array(
    'NATURE, LANDSCAPE'               => 'fal fa-mountains',
    'WILDLIFE, BIRDS'                 => 'fal fa-dove',
    'STREET & TRAVEL'                 => 'fal fa-compass',
    'PORTRAIT / PAINTING / SKETCHES'  => 'fal fa-palette',
);
//echo '<pre>'; print_r($resultFlyer);
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
$sql   =  array();
$sql['QUERY'] = "SELECT `logo_image` FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$result = $mycms->sql_select($sql);

$row         = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];
if ($row['logo_image'] != '') {
  $emailHeader  = $header_image;
}
?>
<body>
    <div class="registration_wrap art_wrap">

        <div class="upload_photo_inner">
            <div class="upload_photo_head">

                <div class="upload_photo_head_left">
                    <span><img src="<?= $emailHeader ?>"></span>
                    <p>
                        <n>
                            <g><?=$formatted?></g>
                            <k><?=$companyInfo['company_conf_venue']?></k>
                        </n>
                    </p>
                </div>
                <div class="upload_photo_head_right">
                    Beyond The Heart Beat
                </div>
            </div>
            <div class="upload_photography_heading">
                <h2>Submit Your Artwork</h2>
                <h5>Life Through a Surgeon’s Lens, Paintbrush and More</h5>
                <ul class="form-stepper form-stepper-horizontal text-center mx-auto pl-0">
                    <!-- Step 1 -->
                    <li class="form-stepper-active text-center form-stepper-list" step="1">
                        <a class="mx-2">
                            <span class="form-stepper-circle">
                                <span>01</span>
                                <n><i class="fal fa-check"></i></n>
                            </span>
                            <div class="label">DETAILS</div>
                        </a>
                    </li>
                    <!-- Step 2 -->
                    <li class="form-stepper-unfinished text-center form-stepper-list" step="2">
                        <a class="mx-2">
                            <span class="form-stepper-circle ">
                                <span>02</span>
                                <n><i class="fal fa-check"></i></n>
                            </span>
                            <div class="label ">CATEGORY</div>
                        </a>
                    </li>
                    <!-- Step 3 -->
                    <li class="form-stepper-unfinished text-center form-stepper-list" step="3">
                        <a class="mx-2">
                            <span class="form-stepper-circle ">
                                <span>03</span>
                                <n><i class="fal fa-check"></i></n>
                            </span>
                            <div class="label ">LINK</div>
                        </a>
                    </li>
                    <!-- Step 4 -->
                    <li class="form-stepper-unfinished text-center form-stepper-list" step="4">
                        <a class="mx-2">
                            <span class="form-stepper-circle ">
                                <span>04</span>
                            </span>
                            <div class="label ">REVIEW</div>
                        </a>
                    </li>
                </ul>
            </div>
            <form class="upload_photo_right" id="userAccountSetupForm" name="userAccountSetupForm"
                action="<?= _BASE_URL_ ?>art_exhibition_submissions_process.php" enctype="multipart/form-data"
                method="POST">
                <!-- Step 1 Content -->
                <input type="hidden" name="act" value="submitArtwork" />
                <section id="step-1" class="upload_form_box">
                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h5>Step 01 • Contributor Profile</h5>
                                <h4>Personal Details</h4>
                            </div>
                        </div>
                        <div class="registration_right_body_content">
                            <div class="form_grid">

                                <div class="frm_grp span_2">
                                    <p class="frm-head">Full Name <i class="mandatory">*</i></p>
                                    <input id="full_name" name="full_name" placeholder="Enter Full Name" required>
                                </div>

                                 <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                    <input id="email" name="email" type="email" placeholder="Enter Email Address">
                                </div>
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                    <div class="sub_frm_grp form_grid">
                                        <select id="mobile_isd" name="mobile_isd" class="span_1" required >
                                            <option value="+91">+91</option>
                                        </select>
                                        <input id="mobile_no" name="mobile_no" class="span_3"
                                            placeholder="Enter Mobile Number" required>
                                    </div>
                                </div>
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Conference Reg. No.</p>
                                    <input type="hidden" id="delegate_id" name="delegate_id">
                                    <input id="medical_reg_no" name="medical_reg_no" placeholder="Auto-filled from your email" disabled>
                                    <small id="delegate_lookup_hint" class="delegate_lookup_hint"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom justify-content-end">
                        <button type="button" name="next" class="next action-button button btn-navigate-form-step"
                            step_number="2">Next: Category<i class="fal fa-angle-right"></i>
                        </button>
                    </div>

                </section>
                <!-- Step 2 Content, default hidden on page load. -->
                <section id="step-2" class="upload_form_box d-none">
                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h5>Step 02 • Artwork Classification</h5>
                                <h4>Select Category</h4>
                            </div>
                        </div>
                        <div class="registration_right_body_content">
                            <div class="cus_check_wrap g2">
                                <?php if (!empty($resultCat)) : ?>
                                    <?php foreach ($resultCat as $cat) :
                                        $iconClass = isset($categoryIconMap[$cat['category_name']])
                                            ? $categoryIconMap[$cat['category_name']]
                                            : 'fal fa-image';
                                    ?>
                                    <label class="cus_check workshop_select">
                                        <input
                                            type="checkbox"
                                            name="regimood"
                                            value="<?= (int) $cat['id'] ?>"
                                            data-category-name="<?= htmlspecialchars($cat['category_name']) ?>"
                                        >
                                        <span class="checkmark">
                                            <n>
                                                <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                                                <iii></iii>
                                                <g><?= htmlspecialchars($cat['category_name']) ?><ii><?= htmlspecialchars($cat['category_description']) ?></ii>
                                                </g>
                                            </n>
                                            <h>
                                                <l><?= htmlspecialchars($cat['category_notes']) ?></l>
                                            </h>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p>No categories are currently available. Please contact the organizers.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom">
                        <button type="button" name="previous"
                            class="previous action-button-previous button btn-navigate-form-step" step_number="1"><i
                                class="fal fa-angle-left"></i>Previous</button>
                        <button type="button" name="next" class="next action-button button btn-navigate-form-step"
                            step_number="3">Next: Upload<i class="fal fa-angle-right"></i>
                        </button>
                    </div>
                </section>
                <!-- Step 3 Content, default hidden on page load. -->
                <section id="step-3" class="upload_form_box d-none">
                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h5>Step 03 • Artwork & Details</h5>
                                <h4>Upload Artwork</h4>
                            </div>
                            <span id="step3_category_label"></span>
                        </div>
                        <div class="registration_right_body_content">
                            <div class="upload_access">
                                <i class="fal fa-info-circle"></i>
                                <n><b>Public Access Requirement:</b> Please upload your
                                    high-resolution artwork to Google Drive,
                                    iCloud, OneDrive, or Dropbox and ensure the link sharing permission is set to
                                    <b>"Anyone
                                        with the link can view"</b> so the jury can access it.
                                </n>
                            </div>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Google Drive, iCloud, OneDrive, or Dropbox Storage Link<i
                                            class="mandatory">*</i></p>
                                    <input id="gdrive_link" name="gdrive_link" placeholder="Paste your shareable link here">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Artwork / Photograph Title <i class="mandatory">*</i></p>
                                    <input id="artwork_title" name="artwork_title" placeholder="Enter Artwork / Photograph Title">
                                </div>

                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Medium / Camera & EXIF Notes </p>
                                    <input id="medium_notes" name="medium_notes" placeholder="e.g. Canon EOS R5, oil on canvas, etc.">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom">
                        <button type="button" name="previous"
                            class="previous action-button-previous button btn-navigate-form-step" step_number="2"><i
                                class="fal fa-angle-left"></i>Previous</button>
                        <button type="button" name="next" class="next action-button button btn-navigate-form-step"
                            step_number="4">Next: Review<i class="fal fa-angle-right"></i>
                        </button>
                    </div>
                </section>
                <section id="step-4" class="upload_form_box d-none">
                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h5>Step 04 • Final Verification</h5>
                                <h4>Review & Submit</h4>
                            </div>
                        </div>
                        <div class="registration_right_body_content">

                            <div id="form_alert"></div>

                            <div class="review_wrap">
                                <div class="review_left">
                                    <h4>
                                        <n>Contributor Profile</n>
                                        <button type="button" class="btn-navigate-form-step" step_number="1"><?php edit() ?> Edit</button>
                                    </h4>
                                    <ol>
                                        <li class="span_2 span_0">
                                            <n>Name</n>
                                            <g id="review_name"></g>
                                        </li>
                                        <li>
                                            <n>Mobile</n>
                                            <g id="review_mobile"></g>
                                        </li>
                                        <li>
                                            <n>Reg. No</n>
                                            <g id="review_reg_no"></g>
                                        </li>
                                        <li class="span_2 span_0">
                                            <n>Email</n>
                                            <g id="review_email"></g>
                                        </li>
                                    </ol>
                                </div>
                                <div class="review_left review_right">
                                    <h4>
                                        <n>Artwork Details</n>
                                        <button type="button" class="btn-navigate-form-step" step_number="3"><?php edit() ?> Edit</button>
                                    </h4>
                                    <ol>
                                        <li class="span_2 span_0">
                                            <n>Title & Category</n>
                                            <h5 id="review_title"></h5>
                                            <h6 id="review_category"></h6>
                                        </li>
                                        <li class="span_2 span_0">
                                            <n>Cloud Storage Link</n>
                                            <p>
                                                <k id="review_link"></k>
                                                <a id="review_link_open" href="#" target="_blank">Open</a>
                                            </p>
                                        </li>
                                        <li class="span_2 span_0">
                                            <n>Notes</n>
                                            <g id="review_notes"></g>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                            <div class="cus_check_wrap upload_form_box_review_check">
                                <label class="cus_check regi_category">
                                    <input type="checkbox" name="confirm_original" id="confirm_original">
                                    <span class="checkmark">
                                        <h><i></i></h>
                                        <n>
                                            <g>I confirm that this is my original work and the cloud link is publicly
                                                accessible.<ii>I authorize IACTSCON 2027 to access this file for jury
                                                    review, conference exhibitions, and commemorative retrospectives.
                                                </ii>
                                            </g>
                                        </n>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom">
                        <button type="button" name="previous"
                            class="previous action-button-previous button btn-navigate-form-step" step_number="3"><i
                                class="fal fa-angle-left"></i>Previous</button>
                       <button id="submit_entry_btn" class="action-button button submit-btn" type="submit">Submit
                           Entry<i class="fal fa-paper-plane"></i></button>
                    </div>
                </section>
                <!-- Step 5 Content — shown after a successful AJAX submission, default hidden on page load. -->
                <section id="step-5" class="upload_form_box d-none">
                    <div class="upload_photography_successfull">
                        <span><i class="fal fa-check-circle"></i></span>
                        <h6>Submission Successful</h6>
                        <h3>ENTRY RECEIVED</h3>
                        <h5>"Thank you for sharing your vision beyond the heart beat."</h5>
                    </div>
                    <div class="upload_photography_successfull_details">
                        <div class="upload_photography_successfull_details_top">
                            <div class="upload_photography_successfull_details_top_box">
                                <n>Reference ID</n>
                                <h5 id="success_ref_id"></h5>
                            </div>
                            <div class="upload_photography_successfull_details_top_box">
                                <n>Submitted At</n>
                                <p id="success_submitted_at"></p>
                            </div>
                        </div>
                        <div class="upload_photography_successfull_details_mid">
                            <div class="upload_photography_successfull_details_mid_box">
                                <n>Contributor</n>
                                <h4 id="success_contributor_name"></h4>
                                <p id="success_contributor_regno"></p>
                            </div>
                            <div class="upload_photography_successfull_details_mid_box">
                                <n>Category & Title</n>
                                <h4 id="success_category"></h4>
                                <h6 id="success_title"></h6>
                            </div>
                        </div>
                        <div class="upload_photography_successfull_details_bottom">
                            <p>
                                <n>Submitted Cloud Link</n>
                                <g id="success_cloud_link"></g>
                            </p>
                            <h6>
                                <a href="#" id="success_copy_link">Copy<i class="fal fa-copy"></i></a>
                                <a href="#" id="success_open_link" target="_blank" rel="noopener">Open<i class="fal fa-external-link"></i></a>
                            </h6>
                        </div>
                    </div>
                    <div class="upload_photography_successfull_button">
                        <a href="<?= _BASE_URL_ ?>art_exhibition_submissions.php" class="workshopbtn"><i class="fal fa-plus"></i>New Submission</a>
                        <!-- <a href="#" class="abstractbtn" id="success_print_btn"><i class="fal fa-print"></i>Print Slip</a> -->
                    </div>
                </section>
            </form>
        </div>
    </div>
</body>

<style>
    /* Print-friendly slip: on Print Slip click, only the success
       details are shown, everything else in the page is hidden. */
    @media print {
        body.printing-slip .upload_photo_head,
        body.printing-slip .upload_photography_heading,
        body.printing-slip .upload_photography_successfull_button {
            display: none !important;
        }
        body.printing-slip #step-5 {
            display: block !important;
        }
    }
</style>

<?php include_once("includes/js-source.php"); ?>
<script>
    /**
    * Define a function to navigate betweens form steps.
    * It accepts one parameter. That is - step number.
    */
    const navigateToFormStep = (stepNumber) => {
        document.querySelectorAll(".upload_form_box").forEach((formStepElement) => {
            formStepElement.classList.add("d-none");
        });
        document.querySelectorAll(".form-stepper-list").forEach((formStepHeader) => {
            formStepHeader.classList.add("form-stepper-unfinished");
            formStepHeader.classList.remove("form-stepper-active", "form-stepper-completed");
        });
        document.querySelector("#step-" + stepNumber).classList.remove("d-none");
        const formStepCircle = document.querySelector('li[step="' + stepNumber + '"]');
        formStepCircle.classList.remove("form-stepper-unfinished", "form-stepper-completed");
        formStepCircle.classList.add("form-stepper-active");
        for (let index = 0; index < stepNumber; index++) {
            const formStepCircle = document.querySelector('li[step="' + index + '"]');
            if (formStepCircle) {
                formStepCircle.classList.remove("form-stepper-unfinished", "form-stepper-active");
                formStepCircle.classList.add("form-stepper-completed");
            }
        }
    };
    document.querySelectorAll(".btn-navigate-form-step").forEach((formNavigationBtn) => {
        formNavigationBtn.addEventListener("click", () => {
            const stepNumber = parseInt(formNavigationBtn.getAttribute("step_number"));
            navigateToFormStep(stepNumber);
        });
    });
</script>
<script src="<?= _BASE_URL_ ?>js/art-exhibition-form.js"></script>

</html>