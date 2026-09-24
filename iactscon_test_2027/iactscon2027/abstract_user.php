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

?>
<body>
    <style>
    .disabled-btn {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
<style>
/* Eligibility Box - Matching Subcategory Design */
/* ================================
    Eligibility Box
    ================================ */

    .eligibility_box {
        display: none;
        margin-top: 10px;
        padding: 12px 15px 15px;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 5px;
        box-sizing: border-box;
    }

    .eligibility_box.active {
        display: block !important;
    }

    /* Heading */
    .eligibility_title {
        margin: 0 0 12px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    /* Eligibility list */
    .eligibility_list {
        margin: 0;
        padding-left: 18px;
        color: #fff;
    }

    .eligibility_list li {
        margin-bottom: 7px;
        color: #fff;
        font-size: 14px;
        line-height: 1.45;
    }

    .eligibility_list li:last-child {
        margin-bottom: 0;
    }

    .eligibility_list .bullet {
        display: none;
    }


    /* ================================
    Eligibility Checkbox
    ================================ */

    .eligibility_agree_wrap {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.20);
    }

    /* Override generic custom checkbox styles */
    .eligibility_checkbox_label {
        position: relative;
        display: flex !important;
        align-items: flex-start;
        gap: 10px;
        width: 100%;
        margin: 0;
        padding: 0;
        cursor: pointer;
    }

    /* Hide actual checkbox but keep it functional */
    .eligibility_checkbox_label input[type="checkbox"] {
        position: absolute !important;
        opacity: 0 !important;
        width: 1px !important;
        height: 1px !important;
        margin: 0 !important;
        pointer-events: none;
    }

    /* Checkbox square */
    .eligibility_checkbox_label .eligibility_checkmark {
        position: relative !important;
        flex: 0 0 19px;
        width: 19px !important;
        height: 19px !important;
        min-width: 19px;
        margin: 1px 0 0 0 !important;
        padding: 0 !important;

        display: inline-block !important;

        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.65);
        border-radius: 3px;

        box-sizing: border-box;
    }

    /* Remove any generic checkmark pseudo-element */
    .eligibility_checkbox_label .eligibility_checkmark::before,
    .eligibility_checkbox_label .eligibility_checkmark::after {
        content: none !important;
    }

    /* Checked checkbox */
    .eligibility_checkbox_label input[type="checkbox"]:checked
    ~ .eligibility_checkmark {
        background: #dceeff;
        border-color: #dceeff;
    }

    /* White/green tick */
    .eligibility_checkbox_label input[type="checkbox"]:checked
    ~ .eligibility_checkmark::after {
        content: "" !important;
        position: absolute;
        left: 5px;
        top: 2px;
        width: 5px;
        height: 9px;

        border: solid #075c4d;
        border-width: 0 2px 2px 0;

        transform: rotate(45deg);
    }

    /* Checkbox text */
    .eligibility_agree_text {
        display: block;
        color: #fff;
        font-size: 13px;
        line-height: 1.5;
        cursor: pointer;
        padding-top: 0;
    }

    /* Error */
    .eligibility_error_msg {
        display: none;
        margin: 8px 0 0 29px;
        color: #ffb3b3;
        font-size: 12px;
    }

    .eligibility_error_msg.active {
        display: block;
    }
</style>
<?php
//setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();
include_once('header.php');
?>

<?php
// $loginDetails 	 = login_session_control();
    $delegateId 	 = $loginDetails['DELEGATE_ID'];
    if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {
        $abstractDelegateId = trim($_REQUEST['abstractDelegateId']);
        $userRec = getUserDetails($abstractDelegateId);
        $sqlFetch = array();
        $sqlFetch['QUERY'] = "
            SELECT COUNT(ar.id) AS TOTAL, am.id AS award_id, am.award_name
            FROM " . _DB_AWARD_REQUEST_ . " AS ar
            LEFT JOIN " . _DB_AWARD_MASTER_ . " AS am
                ON am.related_category_id = 1
                AND am.status = 'A'
            WHERE ar.applicant_id = ?
            AND ar.status = 'A'
        ";
    $sqlFetch['PARAM'][] = array('FILD' => 'applicant_id', 'DATA' => $abstractDelegateId, 'TYP' => 's');

    $CountCategory = $mycms->sql_select($sqlFetch, false);
    $researchCatCount = $CountCategory[0]['TOTAL'];
    $readonly = 'readonly';
} else {
  $mycms->removeAllSession();
  $mycms->removeSession('SLIP_ID');
   $readonly = '';
   $researchCatCount = 0;
}
// echo "<pre>";
// print_r($userRec);
$operate 		 = false;

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


$sqlHeader 	=	array();
$sqlHeader['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$resultHeader = $mycms->sql_select($sqlHeader);
$rowHeader    		 = $resultHeader[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowHeader['logo_image'];
if ($rowHeader['logo_image'] != '') {
	$emailHeader  = $header_image;
}

//to get abstract file types
$sqlInfo  = array();
$sqlInfo['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
             WHERE `status` = ?";
$sqlInfo['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
$resultInfo      = $mycms->sql_select($sqlInfo);
$rowInfo         = $resultInfo[0];
$hod_consent_file_types = $rowInfo['hod_consent_file_types']; //JSON array
$abstract_file_types = $rowInfo['abstract_file_types'];

//================== To check all available services =======================
$workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
$registrationAccompanyAmount  = getCutoffTariffAmnt($currentCutoffId);
$delgDinner    = getDinnerDetailsOfDelegate($delegateId);
$dinnerDtls  = array();
if ($delgDinner && !empty($delgDinner)) {
	$dinnerDtls[$delegateId]                = $delgDinner;
	$dinnerDtls[$delegateId]['INVOICE']     = getInvoiceDetails($delgDinner['refference_invoice_id']);
	$dinnerDtls[$delegateId]['USER']        = $rowUserDetails;
}
$sqlFetchHotel      = array();
$sqlFetchHotel['QUERY'] = "SELECT * 
                                     FROM " . _DB_MASTER_HOTEL_ . "
                                    WHERE `status` =  ? ";

$sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
$resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);
$countAcc = ($resultFetchHotel) ? '1' : '0';
// =========================================================================


$operate			  = true;
$resultAbstractType	  = false;
if ($delegateId != '') {
	$rowUserDetails   = getUserDetails($delegateId);
	// echo '<pre>'; print_r($rowUserDetails);die;
	$presenter_email = $rowUserDetails['user_email_id'];
	$invoiceList 	  = getConferenceContents($delegateId);
	$currentCutoffId  = getTariffCutoffId();

	$sql_abs  			  = array();
	$sql_abs['QUERY']     = " SELECT * 
								FROM " . _DB_ABSTRACT_REQUEST_ . " 
							   WHERE `status` = ?
								 AND `applicant_id` = ?";
	//AND `abstract_child_type` IN ('Oral','Poster')

	$sql_abs['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql_abs['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' => $delegateId, 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql_abs);

	$abstractCatArray = array();
	$countAbstract = 0;
	foreach ($resultAbstractType as $key => $cat_val) {

		if ($cat_val['abstract_cat'] == 1) {
			$countAbstract++;
		}

		array_push($abstractCatArray, trim($cat_val['abstract_cat']));
	}
	// echo $countPaper;
	// echo '<pre>'; print_r($abstractCatArray);die;
}
?>
<script language="javascript">
        var jsBASE_URL	= "<?=_BASE_URL_?>";
        var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
    </script>
<script type="text/javascript" language="javascript" src="js/website/returnData.process.js"></script>
<script>
$(function () {
    // Stop the generic forType=country handler from firing for these two fields
    $('#user_country, #abstract_author_country').off('change');

    $('#user_country').on('change', function () {
        var countryId = $(this).val();
        if (!countryId) {
            $('#user_state').html('<option value="">-- Select Country First --</option>');
            return;
        }
        $.post('returnData.process.php', { act: 'generateStateList', countryId: countryId }, function (html) {
            $('#user_state').html(html).removeAttr('disabled');
        });
    });

    $('#abstract_author_country').on('change', function () {
        var countryId = $(this).val();
        if (!countryId) {
            $('#abstract_author_state').html('<option value="">-- Select Country First --</option>');
            return;
        }
        $.post('returnData.process.php', { act: 'generateStateList', countryId: countryId }, function (html) {
            $('#abstract_author_state').html(html).removeAttr('disabled');
        });
    });
});
</script>
<form name="abstractRequestForm" id="abstractRequestForm" action="<?= _BASE_URL_ ?>abstract.user.entrypoint.process.php" method="post" enctype="multipart/form-data">
   <input type="hidden" name="act" value="step1" />
   	<input type="hidden" name="report_data" id="report_data" value="Abstract" />
    <input type="hidden" name="reg_area" value="FRONT" />
    <input type="hidden" name="otp_id" id="otp_id" value="" />
    <input type="hidden" name="isPresenter" id="isPresenter" value="" />
    <input type="hidden" name="registration_request" id="registration_request" value="ABSTRACT" />
    <input type="hidden" name="registration_cutoff" id="registration_cutoff" value="<?= $registrationCutoffId ?>" />
    <input type="hidden" name="registration_classification_id[]" id="registration_classification_id" value="<?= $registrationClassificationId ?>" />
    <input type="hidden" name="registrationMode" id="registrationMode" value="<?= $registrationMode ?>" />
    <div class="registration_wrap">
        <div class="registartion_head">
            <a href="index.php"><i class="fal fa-arrow-left"></i>Back</a>
            <p><span>Registered Email Id</span><script>document.write(localStorage.getItem('user_email_id') || '');</script></p>
        </div>
        <div class="registration_inner">
            <div class="registration_left">
                <div class="registration_left_head">
                    <h5><?=$rowInfo['company_conf_name']?></span></h5>
                    <h6>Abstract Submission</h6>
                </div>
                <ul id="progressbar">
                    <li class="active" id="primaryauthor"><?php user(); ?><span>Presenter Details</span></li>
                    <li id="coauthor"><?php duser(); ?><span>Author & Co-Author Details</span></li>
                    <li id="abstractcategory"><i class="fal fa-tag"></i><span>Submission Category</span></li>
                    <li id="abstractcontent"><i class="fal fa-book-open"></i><span>Abstract Content</span></li>
                    <li id="abstractreview"><?php check(); ?><span>Review & Submit</span></li>
                </ul>
                <div class="registration_left_bottom">
                    <h6>Deadline</h6>
                    <h5><?= date("M d, Y", strtotime($rowInfo['abstract_submission_date'])) ?></h5>
                </div>
            </div>
            <div class="registration_right">
                <!-- persoanl -->
  
                <fieldset class="registration_right_wrap" id="fs_personal">
                    <div class="registration_right_head">
                        Presenter Details
                    </div>
                    <div class="registration_right_body">
                        <!-- <div class="registration_right_body_head">
                           
                        </div> -->
                        <div class="registration_right_body_content">
                            <div class="form_grid">
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                    <input  id="user_email_id" <?= $readonly ?> value="<?= !empty($userRec['user_email_id']) ? $userRec['user_email_id'] : '' ?>" validate="Please Enter Email Address"  style="flex:1;background-color: transparent!important;" disabled>
                                    <input  type="hidden" id="user_email_id1" name="user_email_id"  value="<?= !empty($userRec['user_email_id']) ? $userRec['user_email_id'] : '' ?>">  
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function() {
                                                var email = localStorage.getItem('user_email_id1') || '';
                                                document.getElementById('user_email_id1').value = email;
                                            });
                                        </script>
                                </div>
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                    <div class="sub_frm_grp form_grid">
                                        <input type="text" value="+91" <?= $readonly ?> value="<?= (!empty($userRec['user_mobile_isd_code']) ? $userRec['user_mobile_isd_code'] : '') ?>" id="user_usd_code" name="user_usd_code"  required validate="Please Enter Mobile Prefix">
                                        <input class="span_3" validate="Please Enter Mobile Number"  onkeypress="return isNumber(event)" required  name="user_mobile" value="<?= (!empty($userRec['user_mobile_no']) ? $userRec['user_mobile_no'] : '') ?>" id="user_mobile"  onkeypress="return isNumber(event)"  maxlength="10">
                                    </div>
                                </div>
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                                    <select name="user_initial_title" <?= $readonly ?>  id="user_initial_title"
                                        class="<?= $disabledclass ?>"
                                        <?= $disabled ?>
                                        validate="Please select your title"
                                        style="width:100%; padding:5px;" required>
                                        <option value="">Select Title</option>
                                        <option value="Dr" <?= strtoupper($userRec['user_title']) == 'DR' ? 'selected' : '' ?>>Dr.</option>
                                        <option value="Prof" <?= strtoupper($userRec['user_title']) == 'PROF' ? 'selected' : '' ?>>Prof.</option>
                                        <option value="Mr" <?= strtoupper($userRec['user_title']) == 'MR' ? 'selected' : '' ?>>Mr.</option>
                                        <option value="Ms" <?= strtoupper($userRec['user_title']) == 'MS' ? 'selected' : '' ?>>Ms.</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                    <input placeholder="Enter First Name" value="<?= ($userRec['user_first_name'] != '') ? ($userRec['user_first_name']) : '' ?>" required name="user_first_name" id="user_first_name" validate="Please Enter First Name" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Middle Name</p>
                                    <input placeholder="Middle Name"  name="user_middle_name" id="user_middle_name" value="<?= ($userRec['user_middle_name'] != '') ? ($userRec['user_middle_name']) : '' ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                    <input required placeholder="Last Name"  name="user_last_name" id="user_last_name"  validate="Please Enter Last Name" value="<?= ($userRec['user_last_name'] != '') ? ($userRec['user_last_name']) : '' ?>">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address <i class="mandatory">*</i></p>
                                    <input name="user_address"  id="user_address" value="<?= !empty($userRec['user_address']) ? $userRec['user_address'] : '' ?>" validate="Please Enter Your Address" type="text" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country <i class="mandatory">*</i></p>
                                      <select class=" <?= $disabledclass ?>" <?= $readonly ?>
                                        name="user_country"
                                        id="user_country"
                                        forType="country"
                                        required
                                        <?= $disabled ?>
                                        validate="Please Select Country"
                                        style="flex:1;">
                                        <option value="">-- Select Country --</option>
                                        <?php
                                        $sqlFetchCountry = array();
                                        $sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                                                            WHERE `status` = ? 
                                                                                            ORDER BY `country_name` ASC";
                                        $sqlFetchCountry['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                        $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                        if ($resultFetchCountry) {
                                            foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                        ?>
                                            <option value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $userRec['user_country_id']) ? 'selected' : '' ?>>
                                                <?= $rowFetchCountry['country_name'] ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">State <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">
                                        <!-- Country icon -->
                                        <!-- <span style="margin-right:5px;">
                                                                <img src="images/country-R.png" alt="" style="width:24px;height:24px;">
                                                            </span> -->

                                        <!-- Country select -->
                                        <select class=" <?= $disabledclass ?>" <?= $readonly ?>
                                        name="user_state"
                                        id="user_state"
                                        forType="state"
                                        required
                                        <?= $disabled ?>
                                        validate="Please Select State"
                                        style="flex:1;">
                                        <option value="">-- Select Country First --</option>
                                        <?php
                                        $sqlFetchState   = array();
                                        $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
                                                                                                    WHERE `status` =? 
                                                                                                ORDER BY `state_name` ASC";

                                        $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                        $resultFetchState = $mycms->sql_select($sqlFetchState);
                                        if ($resultFetchState) {
                                            foreach ($resultFetchState as $keyState => $rowFetchState) {
                                        ?>
                                            <option value="<?= $rowFetchState['st_id'] ?>" <?= ($rowFetchState['st_id'] == $userRec['user_state_id']) ? 'selected' : '' ?>>
                                                <?= $rowFetchState['state_name'] ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">City <i class="mandatory">*</i></p>
                                     <input type="text"  name="user_city" id="user_city"   value="<?= !empty($userRec['user_city']) ? $userRec['user_city'] : '' ?>" validate="Please Enter City" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Pin <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text"
                                        class=" <?= $disabledclass ?>"
                                        name="user_postal_code"
                                        id="user_postal_code"
                                        value="<?= !empty($userRec['user_pincode']) ? $userRec['user_pincode'] : '' ?>"
                                        placeholder="Postal Code"
                                        <?= $disabled ?>
                                        validate="Please enter postal code"
                                        autocomplete="nope"
                                        style="flex:1;" onkeypress="return isNumber(event)" autocomplete="nope" required>
                                    </div>
                                </div>
                                        <div class="frm_grp span_2">
                                    <p class="frm-head">Institute <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text" 
                                        class=" <?= $disabledclass ?>"
                                        name="user_institution"
                                        id="user_institution"
                                        value="<?= !empty($userRec['user_institute_name']) ? $userRec['user_institute_name'] : '' ?>"
                                        placeholder="Institute"
                                        validate="Please enter institute"
                                        required>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Department <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text"
                                        class=" <?= $disabledclass ?>"
                                        name="user_depertment"
                                        id="user_depertment"
                                        placeholder="Department"
                                        value="<?= !empty($userRec['user_department']) ? $userRec['user_department'] : '' ?>"
                                        validate="Please enter department"
                                        required>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Gender <i class="mandatory">*</i></p>
                                    <div class="cus_check_wrap flex-row">
                                        <label class="cus_check gender_check">
                                            <input required type="radio" <?= $readonly ?>
                                                name="user_gender"
                                                id="user_gender_male"
                                                value="Male"
                                                groupname="user_gender"
                                                validate="Please select a gender"
                                                <?= $disabled ?>
                                                <?= (!empty($userRec['user_gender']) && $userRec['user_gender'] == 'Male') ? 'checked' : '' ?>>
                                            <span class="checkmark">Male</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                             <input required type="radio" <?= $readonly ?>
                                                name="user_gender"
                                                id="user_gender_female"
                                                value="Female"
                                                groupname="user_gender"
                                                validate="Please select a gender"
                                                <?= $disabled ?>
                                                <?= (!empty($userRec['user_gender']) && $userRec['user_gender'] == 'Female') ? 'checked' : '' ?>>
                                            <span class="checkmark">Female</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input required type="radio" <?= $readonly ?>
                                                name="user_gender"
                                                id="user_gender_others"
                                                value="Others"
                                                groupname="user_gender"
                                                validate="Please select a gender"
                                                <?= $disabled ?>
                                                <?= (!empty($userRec['user_gender']) && $userRec['user_gender'] == 'Others') ? 'checked' : '' ?>>
                                            <span class="checkmark">Others</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom justify-content-end">

                        <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
                    </div>
                </fieldset>
                <!-- persoanl -->
                <!-- path -->
               <fieldset class="registration_right_wrap" id="fs_author">
                    <div class="registration_right_head">
                        Author & Co-Author Details
                    </div>
                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h4>Author Details</h4>
                            </div>
                            <div class="registration_right_body_head_right">
                                <label class="toggleswitch">
                                    <input class="toggleswitch-checkbox" type="checkbox" name="willBePresenter" use="willBePresenter" id="willBePresenter" onclick="setAsPresenter(this)">
                                    Same as Presentar Details?<div class="toggleswitch-switch"></div>
                                </label>
                            </div>
                        </div>
                        <div class="registration_right_body_content mb-3 registration_right_body_content_author">
                            <div class="form_grid">
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                    <input type="email" name="abstract_author_email"  id="abstract_author_email"  required  validate="Please Enter Email Address">
                                </div>
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                    <div class="sub_frm_grp form_grid">
                                        <input type="text" value="+91" id="abstract_author_phone_isd_code" name="abstract_author_phone_isd_code" required validate="Please Enter Mobile Prefix">
                                        <input class="span_3"  validate="Please Enter Mobile Number"  required  name="abstract_author_phone_no" id="abstract_author_phone_no"  onkeypress="return isNumber(event)"  maxlength="10">
                                    </div>
                                </div>
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                                    <select name="abstract_author_title" id="abstract_author_title"
                                        class="<?= $disabledclass ?>"
                                        <?= $disabled ?>
                                        validate="Please select your title"
                                        style="width:100%; padding:5px;" required>
                                        <option value="">Select Title</option>
                                        <option value="Dr" >Dr.</option>
                                        <option value="Prof">Prof.</option>
                                        <option value="Mr">Mr.</option>
                                        <option value="Ms">Ms.</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                    <input placeholder="Enter First Name"  required name="abstract_author_first_name" id="abstract_author_first_name" validate="Please Enter First Name" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Middle Name</p>
                                    <input placeholder="Middle Name"  name="abstract_author_middle_name" id="abstract_author_middle_name">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                    <input required placeholder="Last Name" name="abstract_author_last_name" id="abstract_author_last_name"  validate="Please Enter Last Name">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address <i class="mandatory">*</i></p>
                                    <input name="abstract_author_address" id="abstract_author_address"  validate="Please Enter Your Address" type="text" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country <i class="mandatory">*</i></p>
                                      <select class=" <?= $disabledclass ?>"
                                        name="abstract_author_country"
                                        id="abstract_author_country"
                                        forType="country"
                                        required
                                        <?= $disabled ?>
                                        validate="Please Select Country"
                                        style="flex:1;">
                                        <option value="">-- Select Country --</option>
                                        <?php
                                        $sqlFetchCountry = array();
                                        $sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                                                            WHERE `status` = ? 
                                                                                            ORDER BY `country_name` ASC";
                                        $sqlFetchCountry['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                        $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                        if ($resultFetchCountry) {
                                            foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                        ?>
                                            <option  value="<?= $rowFetchCountry['country_id'] ?>" >
                                                <?= $rowFetchCountry['country_name'] ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">State <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">
                                        <!-- Country icon -->
                                        <!-- <span style="margin-right:5px;">
                                                                <img src="images/country-R.png" alt="" style="width:24px;height:24px;">
                                                            </span> -->

                                        <!-- Country select -->
                                        <select class=" <?= $disabledclass ?>"
                                        name="abstract_author_state"
                                        id="abstract_author_state"
                                        forType="state"
                                        required
                                        <?= $disabled ?>
                                        validate="Please Select State"
                                        style="flex:1;">
                                        <option value="">-- Select Country First --</option>
                                        <?php
                                        $sqlFetchState   = array();
                                        $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
                                                                                                    WHERE `status` =? 
                                                                                                ORDER BY `state_name` ASC";

                                        $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                        $resultFetchState = $mycms->sql_select($sqlFetchState);
                                        if ($resultFetchState) {
                                            foreach ($resultFetchState as $keyState => $rowFetchState) {
                                        ?>
                                            <option  value="<?= $rowFetchState['st_id'] ?>">
                                                <?= $rowFetchState['state_name'] ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">City <i class="mandatory">*</i></p>
                                     <input type="text"  name="abstract_author_city" id="abstract_author_city"  validate="Please Enter City" required>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Pin <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text"
                                        class=" <?= $disabledclass ?>"
                                        name="abstract_author_pincode"
                                        id="abstract_author_pincode"
                                       
                                        placeholder="Postal Code"
                                        <?= $disabled ?>
                                        validate="Please enter postal code"
                                        autocomplete="nope"
                                        style="flex:1;" onkeypress="return isNumber(event)" autocomplete="nope" required>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Institute <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text"
                                        class=" <?= $disabledclass ?>"
                                        name="abstract_author_institute"
                                        id="abstract_author_institute"
                                        value=""
                                        placeholder="Institute"
                                        validate="Please enter institute"
                                        autocomplete="nope"
                                      required>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Department <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text"
                                        class=" <?= $disabledclass ?>"
                                        name="abstract_author_department"
                                        id="abstract_author_department"
                                        value=""
                                        placeholder="Department"
                                        validate="Please enter department"
                                       required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration_right_body_head coauthor_body_head">
                            <div class="registration_right_body_head_left">
                                <h4>Co-Author Details</h4>
                                <!-- <h5>Same as Presentar Details?</h5> -->
                            </div>
                        </div>
                        <!-- <div class="coauthor_blank text-center" > -->
                            <!-- <div class="coauthor_blank text-center">
                                <span><i class="fal fa-tv-alt"></i></span>
                                <h6>No Co-Authors Added</h6>
                                <p>If this is a solo submission, you can skip this step.</p> -->
                                <button  id="coauthor_blank_section" class="add_guest coauthor_blank text-center"><?php add(); ?> Add Co-Author</button>
                            <!-- </div> -->
                        <!-- </div> -->
                        <div class="registration_right_body_content" id="coauthor_form_section" style="display:none;">
                            <div class="coauthor_wrap">
                                <!-- <li> -->
                                    <!-- <div class="form_grid">
                                        <div class="frm_grp span_4 coauthor_head">
                                            <span>1</span>
                                            <n>Co-Author 1</n>
                                            <a href="#" class="guest_action"><?php delete(); ?></a>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Title</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_3">
                                            <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                            <input placeholder="Enter First Name">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Middle Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_0 span_2">
                                            <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_0 span_2">
                                            <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                            <div class="sub_frm_grp form_grid">
                                                <select class="span_1">
                                                    <option>+91</option>
                                                </select>
                                                <input class="span_3">
                                            </div>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Mailing Address</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Country</p>
                                            <select></select>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">City</p>
                                            <select></select>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Gender</p>
                                            <div class="cus_check_wrap flex-row">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="gender">
                                                    <span class="checkmark">Male</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="gender">
                                                    <span class="checkmark">Female</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="gender">
                                                    <span class="checkmark">Others</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div> -->

                                <!-- </li> -->
                            </div>
                            <button class="add_guest"><?php add(); ?> Add Co-Author</button>
                        </div>
                    </div>
                    <div class="registration_right_bottom">
                        <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
                        <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
                    </div>
                </fieldset>
                <!-- path -->
                <!-- category -->
                <fieldset class="registration_right_wrap categoryfieldset"  id="fs_category">
                    <div class="registration_right_head">
                       Submission Category
                    </div>
                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h5>Select Your Preferred Presentation Category</h5>
                            </div>
                        </div>
                       <div class="registration_right_body_content">
                            <div class="cus_check_wrap">

                                <h6 class="abstractcategory_subhead">Presentation Type</h6>

                                <?php

                                $sqlAbstractTopic['QUERY'] = "SELECT * FROM "._DB_ABSTRACT_TOPIC_CATEGORY_."
                                WHERE status='A' ORDER BY id ASC";
                                $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

                                $sqlAbstractSubmission['QUERY'] = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_."
                                WHERE status='A' ORDER BY category ASC";
                                $resultAbstractSubmission = $mycms->sql_select($sqlAbstractSubmission);

                                $sqlAbstractPresentation['QUERY'] = "SELECT * FROM "._DB_ABSTRACT_PRESENTATION_."
                                WHERE status='A' ORDER BY id ASC";
                                $resultAbstractPresentation = $mycms->sql_select($sqlAbstractPresentation);

                                foreach ($resultAbstractTopic as $topic){

                                $category_id = $topic['id'];
                                  $category_name = $topic['category'];
                                ?>
                                <input type="hidden" name="category_id" id="category_id" >
                             <!-- CATEGORY -->
                                <label class="cus_check regi_category">
                                    <input type="radio" name="abstract_category"    data-researchCatCount="<?= $researchCatCount ?>"   class="category_radio" value="<?= $category_id ?>">
                                        <span class="checkmark">
                                        <n><g><?= $topic['category'] ?></g></n>
                                        <h><i></i></h>
                                        </span>
                                </label>
                                <?php
                                $hasSubCategory = false;
                                 $isPublishedResearch = (stripos($category_name, 'published original research') !== false);

                                foreach ($resultAbstractSubmission as $sub){
                                    if($sub['category'] == $category_id){
                                        $hasSubCategory = true;
                                        break;
                                    }
                                }
                              if($isPublishedResearch){
                                                                        ?>
                                    <!-- ELIGIBILITY CRITERIA for Published Original Research -->
                                    <div class="eligibility_box " id="eligibility_<?= $category_id ?>">
                                        <div class="eligibility_inner">
                                            <div class="eligibility_header">
                                                <h6 class="eligibility_title">ELIGIBILITY CRITERIA</h6>
                                            </div>
                                            <ul class="eligibility_list">
                                                <li><span class="bullet"></span> Original research conducted in India</li>
                                                <li><span class="bullet"></span> Published online or in print on or after 1 January 2025</li>
                                                <li><span class="bullet"></span> Relevant to cardiac, thoracic or cardiovascular surgery</li>
                                                <li><span class="bullet"></span> Original articles only</li>
                                                <li><span class="bullet"></span> Narrative Reviews, meta-analyses, editorials, letters and case reports are not eligible</li>
                                            </ul>
                                            
                                            <div class="eligibility_agree_wrap">
                                                <label class="eligibility_checkbox_label">
                                                    <input type="checkbox"
                                                        name="eligibility_agreed"
                                                        id="eligibility_agreed"
                                                        value="1"
                                                        required>

                                                    <span class="eligibility_checkmark"></span>

                                                    <span class="eligibility_agree_text">
                                                        I confirm that the submission meets all the above eligibility criteria
                                                    </span>
                                                </label>
                                                <p class="eligibility_error_msg" id="eligibility_error">You must agree to the eligibility criteria to proceed.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                        // For categories with subcategories (but not Published Original Research)
                                         } else if($hasSubCategory){
                               
                                ?>
                               <!-- SUB CATEGORY -->
                                <div class="cus_check_wrap regi_category_sublabel subcategory_box" id="subcat_<?= $category_id ?>" style="display:none;">

                                 <h6 class="abstractcategory_subhead">Sub Category</h6>

                                    <?php
                                    foreach ($resultAbstractSubmission as $sub){

                                        if($sub['category'] == $category_id){

                                        $submission_id = $sub['id'];
                                        $submission_name = $sub['abstract_submission'];
                                        ?>

                                        <label class="cus_check regi_category">

                                            <input type="radio"
                                            name="abstract_parent_type"
                                            class="subcategory_radio"
                                            data-category="<?= $category_id ?>"
                                            value="<?= $submission_id ?>">

                                            <span class="checkmark">
                                                <n><g><?= $submission_name ?><k style="text-align: left;" class="abstractcategory_subhead"><?= $sub['description'] ?></k></g></n>
                                                <h><i></i></h>
                                            </span>

                                        </label>
                                         <?php
                                            $hasSubsubCategory = false;

                                            foreach ($resultAbstractPresentation as $pres){
                                                if($pres['category_id']==$category_id && $pres['submission_id']==$submission_id){

                                                    $hasSubsubCategory = true;
                                                    break;
                                                }
                                            }

                                        if($hasSubsubCategory){
                                        ?>
                                        <!-- SUB SUB CATEGORY -->
                                        <div class="cus_check_wrap regi_category_sublabel subsubcategory_box"
                                            id="presentation_<?= $submission_id ?>" style="display:none;">

                                            <h6 class="abstractcategory_subhead">Sub Category 2</h6>

                                            <?php
                                            foreach ($resultAbstractPresentation as $pres){

                                                if($pres['category_id']==$category_id && $pres['submission_id']==$submission_id){
                                                ?>

                                                <label class="cus_check regi_category">

                                                <input type="radio" name="abstract_child_type" class="sub_subcategory_radio" value="<?= $pres['id'] ?>">

                                                <span class="checkmark">
                                                        <n><g><?= $pres['abstract_presentation'] ?><k style="text-align: left;"  class="abstractcategory_subhead"><?= $pres['description'] ?></k></g></n>
                                                        <h><i></i></h>
                                                    </span>

                                                </label>

                                            <?php } } ?>

                                        </div>

                                        <?php } } } ?>

                                    </div>

                                <?php } } ?>
                            
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom ">
                        <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
                        <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
                    </div>
                </fieldset>
                
                <!-- category -->
                <!-- accommodation -->
                <fieldset class="registration_right_wrap"  id="fs_abstract">
                    <div class="registration_right_head">
                        Abstract Content
                    </div>
                    <div class="registration_right_body">
                        <div class="registration_right_body_content">
                            <div id="abstractFieldsLoader" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%;  z-index:999; text-align:center; padding-top:90px;">
                                <i class="fal fa-spinner fa-spin" style="font-size:30px;"></i>
                                <p>Loading fields, please wait...</p>
                            </div>
                            <?php
                            $category_id = isset($_POST['category_id']) && !empty($_POST['category_id']) 
                                ? $_POST['category_id'] 
                                : '';                                
                              
                            ?>
                            <div class="form_grid" id="fields_area">
                                <?
                                    $sqlAbstractTopic			  =	array();
                                    $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                                                                WHERE `status` = ? 
                                                                                                                AND `category` = ?
                                                                                                            ORDER BY `id` ASC";

                                    $sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');
                                    $sqlAbstractTopic['PARAM'][]  = array('FILD' => 'category', 'DATA' => $category_id,  'TYP' => 's');
                                
                                    $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

                                    if ($resultAbstractTopic) {
                                ?>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Topic</p>
                                    <select name="abstract_topic_id" id="abstract_topic_id" required="required" validate="Please select the topic" onchange="getTopicWiseNomination(this)">
                                        <option value="">--Topic--</option>
                                        <?php

                                        foreach ($resultAbstractTopic as $keyAbstractTopic => $rowAbstractTopic) {
                                        ?>
                                            <option value="<?= $rowAbstractTopic['id'] ?>"><?= $rowAbstractTopic['abstract_topic'] ?></option>
                                        <?php
                                        }

                                        ?>
                                    </select>
                                    <?php if ($cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] != 0) { ?>
                                        <a href="<?= $cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] == '1' ? $cfg['ABSTRACT.GUIDELINE.PDF'] : _BASE_URL_ . "uploads/FILES.ABSTRACT.REQUEST/" . $cfg['ABSTRACT.GUIDELINE.PDF.FILE'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/gide.png" alt=""></a>
                                    <?php }
                                    ?>
                                </div>
                                <div id="abstract_topic_desc" style="font-style: italic; font-size: 14px;font-weight: bolder;"></div> 
                                <? } ?>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Title</p>
                                    <input  name="abstract_title" id="abstract_title" spreadInGroup="abstractTitle" style="text-transform:uppercase;" required validate="Please enter the abstract title">
                                </div>
                               <? if ($category_id !='4') { ?>

                                <div class="frm_grp span_4" >
                                    <p style="color: #f3d178;" class="frm-head text-right">Total: <n id="abstract_title_count">0</n> / <?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?> <?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?></p>
                                </div>
                                <? } ?>
                                 
                                <?php
                                function cleanFields($fields){
                                    $fields = json_decode($fields, true);

                                    if(!is_array($fields)){
                                        return [];
                                    }

                                    // remove empty values like "", null
                                    $fields = array_filter($fields, function($val){
                                        return $val !== null && $val !== '';
                                    });

                                    // ensure integers only (security)
                                    return array_map('intval', $fields);
                                }
                                    $category_id = $category_id ?? null;
                                    $sub_category_id = $_POST['sub_category_id'] ?? null;
                                    $sub_subcategory_Id = $_POST['sub_subcategory_Id'] ?? null;

                                    $category_fields = [];

                                    /* ================= CATEGORY ================= */
                                    if($category_id){

                                        $sqlCategory = [
                                            'QUERY' => "SELECT * 
                                                        FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
                                                        WHERE id = ?",
                                            'PARAM' => [
                                                ['FILD'=>'id','DATA'=>$category_id,'TYP'=>'i']
                                            ]
                                        ];

                                        $resultCategory = $mycms->sql_select($sqlCategory);
                                      
                                        if($resultCategory){
                                            $category_fields = cleanFields($resultCategory[0]['category_fields']);
                                        }

                                        /* ================= SUB CATEGORY ================= */
                                        if(empty($category_fields) && $sub_category_id){

                                            $sqlSubmission = [
                                                'QUERY' => "SELECT category_fields 
                                                            FROM "._DB_ABSTRACT_SUBMISSION_."
                                                            WHERE category = ? AND id = ?
                                                            LIMIT 1",
                                                'PARAM' => [
                                                    ['FILD'=>'category','DATA'=>$category_id,'TYP'=>'i'],
                                                    ['FILD'=>'id','DATA'=>$sub_category_id,'TYP'=>'i']
                                                ]
                                            ];

                                            $resultSubmission = $mycms->sql_select($sqlSubmission);

                                            if($resultSubmission){
                                                $category_fields = cleanFields($resultSubmission[0]['category_fields']);
                                            }
                                        }

                                        /* ================= SUB SUB CATEGORY ================= */
                                        if(empty($category_fields) && $sub_subcategory_Id){

                                            $sqlPresentation = [
                                                'QUERY' => "SELECT category_fields 
                                                            FROM "._DB_ABSTRACT_PRESENTATION_."
                                                            WHERE category_id = ? 
                                                            AND submission_id = ? 
                                                            AND id = ?
                                                            LIMIT 1",
                                                'PARAM' => [
                                                    ['FILD'=>'category_id','DATA'=>$category_id,'TYP'=>'i'],
                                                    ['FILD'=>'submission_id','DATA'=>$sub_category_id,'TYP'=>'i'],
                                                    ['FILD'=>'id','DATA'=>$sub_subcategory_Id,'TYP'=>'i']
                                                ]
                                            ];

                                            $resultPresentation = $mycms->sql_select($sqlPresentation);

                                            if($resultPresentation){
                                                $category_fields = cleanFields($resultPresentation[0]['category_fields']);
                                            }
                                        }
                                    }

                                    /* ================= FINAL FALLBACK ================= */
                                    if(empty($category_fields)){
                                        $category_fields = [];
                                    }

                                    /* ================= FETCH FIELDS ================= */
                                    $resultAbstractFields = [];

                                    if(!empty($category_fields)){

                                        // safe IDs
                                        $placeholders = implode(',', array_fill(0, count($category_fields), '?'));
                                        $field_ids = implode(",", $category_fields);
                                            if (!empty($field_ids)) {
                                                $order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
                                            } else {
                                                $order_by = "ORDER BY id ASC";
                                            }
                                        $sqlAbstractFields = [
                                            'QUERY' => "SELECT * 
                                                        FROM "._DB_ABSTRACT_FIELDS_."
                                                        WHERE status='A'
                                                        AND id IN ($placeholders)
                                                        ".$order_by."",
                                            'PARAM' => []
                                        ];
                                       
                                        foreach($category_fields as $id){
                                            $sqlAbstractFields['PARAM'][] = [
                                                'FILD'=>'id',
                                                'DATA'=>$id,
                                                'TYP'=>'i'
                                            ];
                                        }

                                        $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                    }
                                    $i = 1;
                                    // echo '<pre>'; print_r($resultAbstractFields);
                                    if($resultAbstractFields){
                                        foreach ($resultAbstractFields as $key => $value) {

                                            $msg = "Please enter the " . strtolower($value['display_name']);

                                        ?>
                                    <div class="frm_grp span_0 span_4" >
                                        <p class="frm-head"><?=$value['display_name']?></p>
                                        <textarea required  name="<?= $value['field_key'] ?>[]" id="fieldVal_<?= $value['id'] ?>" checkFor="wordCount" spreadInGroup="abstractContent" displayText="abstract_total_word_display" word_type="<?= htmlspecialchars($cfg['ABSTRACT.TOTAL.WORD.TYPE'], ENT_QUOTES) ?>" validate="<?= $msg ?>" title="<?= $value['display_name'] ?>"></textarea>
                                    </div>
                                    <? if($value['display_name']=='Link of the video' || $value['display_name']=='Link Of The Video'){
                                        ?>
                                        <div class="frm_grp span_4" >
                                           <span style="" class="frm-head text-left">*Keep the given link accessible till you get a notification from the Secretariat.</span><br>
                                          <span style="color: #ff402a;" class="frm-head text-left">*Note: Video length must be of max 8 mins.</span>

                                        </div>
                                         
                                        <?
                                    }
                                    ?>
                                    <? } ?>
                                     <? if ($category_id !='4') { ?>
                                    <div class="frm_grp span_4" >
                                        <p style="color: #f3d178;" class="frm-head text-right">Total: <n id="abstract_body_count">0</n> / <?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?> <?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?></p>
                                    </div>
                                    <? } } ?>
                                 <?php if ($resultCategory[0]['doc_upload'] == 'yes' && $resultCategory[0]['suporting_document_type'] !== 'null') { ?>
                                        <div class="frm_grp span_4 upload-section-cat" id="upload_section-cat_<?= $resultCategory[0]['id'] ?>">
                                            <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                            <div class="file-up-dtls">
                                                <h8><?=$resultCategory[0]['suporting_document_name']?></h8>

                                                <input type="hidden" name="category_file_types" value="<?= $abstract_file_types ?>" />
                                                <input type="hidden" name="original_category_file_name" />
                                                <input type="hidden" name="temp_abstract_filename">
                                                <?php $rowAbstracttypeCat = json_decode($resultCategory[0]['suporting_document_type']); ?>
                                                <input class="form-control category-file" data-types="<?= implode(',', array_filter($rowAbstracttypeCat)) ?>"   type="file"  id="formFileAbstractCat"  required name="category_required_file" validate="Please upload the required file" />
                                                <h6>
                                                    <?= ($rowAbstracttypeCat[0] != '') ? strtoupper($rowAbstracttypeCat[0]) . " | " : '' ?>
                                                    <?= ($rowAbstracttypeCat[1] != '') ? ucfirst($rowAbstracttypeCat[1]) : '' ?>
                                                    <?= ($rowAbstracttypeCat[2] != '') ? " | " . ucfirst($rowAbstracttypeCat[2]) : '' ?>
                                                </h6>

                                                <span></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                 <?php
                                    if ($hod_consent_file_types !== 'null') {
                                    ?>
                                      <div class="frm_grp span_4" >
                                        <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                        <input type="hidden" name="sessionId" id="sessionId" use="sessionId" value="<?= session_id() ?>" />
                                        <div class="file-up-dtls">
                                            <h8>HOD Consent(Only appilicable for trainee/PGT)</h8>

                                            <input type="hidden" name="hod_consent_file_types" id="hod_consent_file_types" value=<?= $hod_consent_file_types ?> />
                                            <input type="hidden" name="original_consent_file_name" id="original_consent_file_name" use="upload_original_fileName" />
                                            <input type="hidden" name="temp_consent_filename" id="temp_consent_filename">
                                            <?php $type = json_decode($hod_consent_file_types); ?>
                                            <input class="form-control consent-file" type="file" id="formFileHod"  data-types="<?= implode(',', array_filter($type)) ?>"   name="upload_consent_abstract_file" validate="Please upload the HOD consent file" />
                                            <h6>
                                                <?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?>
                                                <?= ($type[1] != '') ? ucfirst($type[1]) : '' ?>
                                                <?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?>
                                            </h6>                                            <span id=concentFileNameUploaded></span>
                                        </div>
                                    </div>
                                                                     
                                    <?php }
                                    if ($abstract_file_types !== 'null' && $category_id !='3' && $category_id !='4') { ?>
                                      <div class="frm_grp span_4" >
                                            <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                            <div class="file-up-dtls">
                                                <h8>Abstract File(If any Illustration/Diagram/Picture)</h8>
                                                <input type="hidden" name="abstract_file_types" id="abstract_file_types" value=<?= $abstract_file_types ?> />
                                                <input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name" use="upload_original_fileName" />
                                                <input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename">
                                                <?php $type = json_decode($abstract_file_types); ?>
                                                <input class="form-control abstract-file" type="file" data-types="<?= implode(',', array_filter($type)) ?>"     id="formFileAbstract"  name="upload_abstract_file" validate="Please upload the abstract file" />
                                                 <h6>
                                                    <?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?>
                                                    <?= ($type[1] != '') ? ucfirst($type[1]) : '' ?>
                                                    <?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?>
                                                </h6>
                                                <span id=abstractFileNameUploaded></span>

                                            </div>
                                        </div>  
                                      <?php } ?>
                                     <?php
									$sqlAbstractSubcat    = array();
									$sqlAbstractSubcat['QUERY']    = "SELECT * 
																		  FROM " . _DB_AWARD_MASTER_ . " 
																		 WHERE `status` = ? 
                                                                         AND `related_category_id` = ?
																	  ORDER BY `id` ASC";

									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'related_category_id',  'DATA' => $category_id,  'TYP' => 's');

									$resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);
                                    if ($resultAbstractSubcat[0]['related_subcategoryId']!=''){
                                        $sqlAbstractSubcat    = array();
                                        $sqlAbstractSubcat['QUERY']    = "SELECT * 
                                                                            FROM " . _DB_AWARD_MASTER_ . " 
                                                                            WHERE `status` = ? 
                                                                            AND `related_category_id` = ?
                                                                            AND `related_subcategoryId` = ?

                                                                        ORDER BY `id` ASC";

                                        $sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                                        $sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'related_category_id',  'DATA' => $category_id,  'TYP' => 's');
								     	$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'related_subcategoryId',  'DATA' => $sub_category_id,  'TYP' => 's');

                                        $resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);
                                    }
									if ($resultAbstractSubcat) {
                                     $blockCategory = ($category_id == 1 && $_POST['researchCatCount'] >= 2) ? false : false;
                                    foreach ($resultAbstractSubcat as $keyAbstractTopic => $rowAbstractTopic) {
                                    ?>
                                        <div class="frm_grp span_4" >
                                            <label class="custom-radio " for="nomination_name_<?= $rowAbstractTopic['id'] ?>">
                                                <input  data-block="<?= $blockCategory ? '1' : '0' ?>" class="toggleswitch-checkbox award-toggle"  data-id="<?= $rowAbstractTopic['id'] ?>" type="checkbox" value="<?= $rowAbstractTopic['id'] ?>" id="nomination_name_<?= $rowAbstractTopic['id'] ?>" name="award_request" <?= $disableCategory ?>>
                                                <div class="toggleswitch-switch"></div> <?= "I want to opt for " . $rowAbstractTopic['award_name'] ?><span class="checkmark"></span>
                                                <br><br><span><i>
                                                    <?= $rowAbstractTopic['award_description']  ?></i>
                                                </span>
                                            </label>

                                        </div>
                                       
                                       <?php if ($rowAbstractTopic['doc_upload'] == 'yes' && $rowAbstractTopic['suporting_document_type'] !== 'null') { ?>
                                        <div class="frm_grp span_4 upload-section" id="upload_section_<?= $rowAbstractTopic['id'] ?>" style="display:none;">
                                            <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                            <div class="file-up-dtls">
                                                <h8><?=$rowAbstractTopic['suporting_document_name']?></h8>

                                                <input type="hidden" name="nomination_file_types" value="<?= $abstract_file_types ?>" />
                                                <input type="hidden" name="original_nomination_file_name" />
                                                <input type="hidden" name="temp_abstract_filename">
                                                <?php $rowAbstracttype = json_decode($rowAbstractTopic['suporting_document_type']); ?>
                                                <input class="form-control nomination-file" data-types="<?= implode(',', array_filter($rowAbstracttype)) ?>"   type="file"  id="formFileAbstract_<?= $rowAbstractTopic['id'] ?>"   name="upload_nomination_file" validate="Please upload the nomination required file" />
                                                <h6>
                                                    <?= ($rowAbstracttype[0] != '') ? strtoupper($rowAbstracttype[0]) . " | " : '' ?>
                                                    <?= ($rowAbstracttype[1] != '') ? ucfirst($rowAbstracttype[1]) : '' ?>
                                                    <?= ($rowAbstracttype[2] != '') ? " | " . ucfirst($rowAbstracttype[2]) : '' ?>
                                                </h6>

                                                <span></span>
                                            </div>
                                        </div>
                                    <?php } } } ?>
                            
                            </div>
                        </div>
                    </div>
                    <div class="registration_right_bottom">
                        <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>

                        <!-- <button type="button" name="next" class="skip-button skip"><?php skip() ?>Skip</button> -->
                        <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
                    </div>
                </fieldset>
                <!-- accommodation -->
                <!-- workshop -->
                <fieldset class="registration_right_wrap" id="fs_review">
                    <div class="registration_right_head">
                        Review & Submit
                    </div>

                    <div class="registration_right_body">
                        <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h5>Please review all details before final submission.</h5>
                            </div>
                        </div>

                        <div class="registration_right_body_content">
                            <div class="abstract_review_grid">
                                <li>
                                    <h4>
                                        <n><?php user(); ?>Presentor Details</n><a href="#" class="editStep" data-target="fs_personal">Edit</a>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Email Address</p>
                                            <h6 data-review="user_email_id"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Mobile Number</p>
                                            <h6>
                                                <span data-review="user_usd_code"></span>
                                                <span data-review="user_mobile"></span>
                                               
                                            </h6>
                                        </div>
                                        <!-- <div class="frm_grp span_2">
                                            <p class="frm-head">Title</p>
                                            <h6 data-review="user_initial_title" ></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">First Name</p>
                                            <h6 data-review="user_first_name"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Middle Name</p>
                                            <h6 data-review="user_middle_name"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Last Name</p>
                                            <h6 data-review="user_last_name"></h6>
                                        </div> -->
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Name</p>
                                            <h6>
                                                <span data-review="user_initial_title"></span>
                                                <span data-review="user_first_name"></span>
                                                <span data-review="user_middle_name"></span>
                                                <span data-review="user_last_name"></span>
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Gender</p>
                                            <h6 data-review="user_gender"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Address</p>
                                            <h6>
                                               <span data-review="user_address"></span>
                                              
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Country</p>
                                            <h6>
                                                 <span data-review="user_country"></span>
                                             
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">State</p>
                                            <h6>
                                                <span data-review="user_state"></span>
                                               
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">City</p>
                                            <h6>
                                              
                                                <span data-review="user_city"></span>
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Pin</p>
                                            <h6>
                                            
                                                <span data-review="user_postal_code"></span>
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Institute</p>
                                            <h6 data-review="user_institution"></h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Department</p>
                                            <h6 data-review="user_depertment"></h6>
                                        </div>
                                        
                                    </div>
                                </li>
                                <li>
                                    <h4>
                                        <n><?php duser(); ?>Auhtor Details</n><a href="#" class="editStep" data-target="fs_author">Edit</a>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Email Address</p>
                                            <h6 data-review="abstract_author_email"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Mobile Number</p>
                                            <h6 data-review="abstract_author_phone_no"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Name</p>
                                            <h6>
                                                <span data-review="abstract_author_title"></span>
                                                <span data-review="abstract_author_first_name"></span>
                                                <span data-review="abstract_author_middle_name"></span>
                                                <span data-review="abstract_author_last_name"></span>
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Address</p>
                                            <h6>
                                                <span data-review="abstract_author_address"></span>
                                            
                                            </h6>
                                        </div>
                                            <div class="frm_grp span_2">
                                            <p class="frm-head">Country</p>
                                            <h6>
                                                 <span data-review="abstract_author_country"></span>
                                             
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">State</p>
                                            <h6>
                                                <span data-review="abstract_author_state"></span>
                                               
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">City</p>
                                            <h6>
                                              
                                                <span data-review="abstract_author_city"></span>
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Pin</p>
                                            <h6>
                                            
                                                <span data-review="abstract_author_pincode"></span>
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Institute</p>
                                            <h6 data-review="abstract_author_institute"></h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Department</p>
                                            <h6 data-review="abstract_author_department"></h6>
                                        </div>
                                    </div>
                                </li>
                                 <!-- <li>
                                    <h4>
                                        <n><?php duser(); ?>Co-Auhtor Details</n><a href="#" class="editStep" data-target="fs_author">Edit</a>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Email Address</p>
                                            <h6 data-review="abstract_coauthor_email[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Mobile Number</p>
                                            <h6 data-review="abstract_coauthor_mobile[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Title</p>
                                            <h6 data-review="abstract_coauthor_title[]"></h6>
                                        </div>
                                          <div class="frm_grp span_2">
                                            <p class="frm-head">First Name</p>
                                            <h6 data-review="abstract_coauthor_first_name[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Middle Name</p>
                                            <h6 data-review="abstract_coauthor_middle_name[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Last Name</p>
                                            <h6 data-review="abstract_coauthor_last_name[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Address</p>
                                            <h6 data-review="abstract_coauthor_address[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Country</p>
                                            <h6 data-review="abstract_coauthor_country[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">State</p>
                                            <h6 data-review="abstract_coauthor_state[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">City</p>
                                            <h6 data-review="abstract_coauthor_city[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Pin</p>
                                            <h6 data-review="abstract_coauthor_pincode[]"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Institute</p>
                                            <h6 data-review="abstract_coauthor_institute[]"></h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Department</p>
                                            <h6 data-review="abstract_coauthor_department[]"></h6>
                                        </div>
                                    </div>
                                </li> -->
                                <div class="coauthor_review_wrap">
                                    <!-- Co-author <li> items will be appended here -->
                                </div>
                                <li>
                                    <h4>
                                        <n><i class="fal fa-tag"></i>Submission Category</n><a href="#" class="editStep" data-target="fs_category">Edit</a>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Category</p>
                                            <h6 data-review="abstract_category"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Sub Category</p>
                                            <h6 data-review="abstract_parent_type"></h6>
                                        </div>
                                        <div class="frm_grp span_2 d-none">
                                            <p class="frm-head">Sub Category 2</p>
                                            <h6 data-review="abstract_child_type"></h6>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <h4>
                                        <n><i class="fal fa-book-open"></i>Abstract Content</n><a href="#" class="editStep" data-target="fs_abstract">Edit</a>
                                    </h4>
                                    <div class="form_grid" id="fields_area1">
                                         <?  if ($resultAbstractTopic) { ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Topic</p>
                                            <h6 data-review="abstract_topic_id"></h6>
                                        </div>
                                         <? } ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Title</p>
                                            <h6 data-review="abstract_title"></h6>
                                        </div>
                                        <?php

                                          
                                        // echo '<pre>'; print_r($abstract_file_types);
                                        foreach ($resultAbstractFields as $key => $value) {

                                        ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head"><?=$value['display_name']?></p>
                                            <h6 data-review="<?= $value['field_key'] ?>[]"></h6>
                                        </div>
                                        <? } ?>
                                        <!-- CATEGORY File -->
                                        <div class="frm_grp span_4" id="category_abstract_wrapper" style="display:none;">
                                            <p class="frm-head">Category File</p>
                                            <h6 id="review_category_file"></h6>
                                        </div>
                                        <!-- Abstract File -->
                                        <div class="frm_grp span_4" id="review_abstract_wrapper" style="display:none;">
                                            <p class="frm-head">Abstract File</p>
                                            <h6 id="review_abstract_file"></h6>
                                        </div>

                                        <!-- HOD Consent -->
                                        <div class="frm_grp span_4" id="review_hod_wrapper" style="display:none;">
                                            <p class="frm-head">HOD Consent File</p>
                                            <h6 id="review_hod_file"></h6>
                                        </div>
                                        <div class="frm_grp span_4">
                                        <!-- Nomination Files -->
                                          <div id="review_nomination_files"></div>
                                        </div>
                                    </div>
                                </li>
                               
                            </div>
                            
                        </div>
                    </div>
                    <div class="registration_right_bottom">
                        <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button>
                        <button type="submit" name="submit" class="submit action-button">Confirm<?php check(); ?></button>
                    </div>
                </fieldset>
                <!-- workshop -->
            </div>
        </div>
    </div>
</form>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    $(document).ready(function() {
        var storageEmail = localStorage.getItem("user_email_id");
        console.log(storageEmail);
        if (storageEmail != '' && storageEmail !== undefined) {
        $('#user_email_id').val(storageEmail);
        $('#user_email_id1').val(storageEmail);
        }
   });
</script>
<script>
    // $('.stay_li_right').click(function() {
    //     if ($('.stay_li_right').find(':checked')) {
    //         $(this).addClass('selected');
    //     }
    // })
   function populateCoAuthorsReview() {
        // Clear existing review items
        $(".coauthor_review_wrap").empty();

        $(".coauthor_wrap .coauthor_item").each(function(index) {
            var coauthor = $(this); // current co-author input form

            // Build review <li> dynamically
            var reviewItem = `<li class="coauthor_review_item">
                <h4>
                    <n>Co-Author ${index + 1} Details</n>
                    <a href="#" class="editStep" data-target="fs_author">Edit</a>
                </h4>
                <div class="form_grid">
                    <div class="frm_grp span_2"><p class="frm-head">Email Address</p><h6>${coauthor.find("[name='abstract_coauthor_email[]']").val() || '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Mobile Number</p><h6>${coauthor.find("[name='abstract_coauthor_mobile[]']").val() || '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Name</p><h6>${coauthor.find("[name='abstract_coauthor_title[]']").val()} ${coauthor.find("[name='abstract_coauthor_first_name[]']").val()} ${coauthor.find("[name='abstract_coauthor_middle_name[]']").val()} ${coauthor.find("[name='abstract_coauthor_last_name[]']").val()}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Address</p><h6>${coauthor.find("[name='abstract_coauthor_address[]']").val()|| '-'}</h6></div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Country</p>
                        <h6>${coauthor.find("[name='abstract_coauthor_country[]']").val() ? coauthor.find("[name='abstract_coauthor_country[]'] option:selected").text() : '-'}</h6>
                    </div>

                    <div class="frm_grp span_2">
                        <p class="frm-head">State</p>
                        <h6>${coauthor.find("[name='abstract_coauthor_state[]']").val() ? coauthor.find("[name='abstract_coauthor_state[]'] option:selected").text() : '-'}</h6>
                    </div>
                    <div class="frm_grp span_2"><p class="frm-head">City</p><h6>${coauthor.find("[name='abstract_coauthor_city[]']").val()|| '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Pin</p><h6>${coauthor.find("[name='abstract_coauthor_pincode[]']").val()|| '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Institute</p><h6>${coauthor.find("[name='abstract_coauthor_institute[]']").val() || '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Department</p><h6>${coauthor.find("[name='abstract_coauthor_department[]']").val() || '-'}</h6></div>
                </div>
            </li>`;

            // Append review item
            $(".coauthor_review_wrap").append(reviewItem);
        });
    }
     function populateReview() {
        $("[data-review]").each(function() {
            var field = $(this).data("review"); // e.g., "abstract_content[]"
            var value = "";

            // For array fields
            if(field.slice(-2) === '[]') {
                var baseName = field.slice(0, -2);
                var values = $("textarea[name='"+baseName+"[]'], input[name='"+baseName+"[]'], select[name='"+baseName+"[]']").map(function(){
                    return $(this).val();
                }).get();
                value = values.join(", ") || "";
            }
            else {
                var el = $("[name='"+field+"']");
                if(el.attr("type") === "radio") {
                    value = $("[name='"+field+"']:checked")
                            .next()
                            .clone()                 // ✅ work on a copy
                            .find("k").remove()      // remove <k> only in the copy
                            .end()
                            .text()
                            .trim() || "-";
                }
                else if(el.is("select")) {
                    value = el.find("option:selected").text() || "-";
                }
                else {
                    value = el.val() || "";
                }
            }

            $(this).text(value);
        });
    }
    function checkAbstractLimits() {
        // Skip all word/character limit checks for category id 4
        var category_id = $(".category_radio:checked").val() || '';
        if (category_id === '4') {
            return true;
        }

        var titleType = '<?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>';
        var titleLimit = <?= intval($cfg['ABSTRACT.TITLE.WORD.LIMIT']) ?>;
        var titleVal = $("#abstract_title").val().trim();
        var titleCount = 0;

        if(titleType === 'character'){
            titleCount = titleVal.length;
        } else {
            titleCount = titleVal.split(/\s+/).filter(Boolean).length;
        }

        if(titleCount > titleLimit){
            alert("Abstract Title exceeds the limit of " + titleLimit + " " + titleType + "(s). You cannot continue.");
            $("#abstract_title").focus();
            return false;
        }

        var bodyLimit = <?= intval($cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']) ?>;
        var exceeded = false;

        $("textarea[checkFor='wordCount']").each(function(){
            var type = ($(this).attr("word_type") || "word").trim().toLowerCase();
            var group = $(this).attr("spreadInGroup");
            var total = 0;

            $("textarea[spreadInGroup='"+group+"']").each(function(){
                var val = $(this).val().trim();
                if(type === "character"){
                    total += val.length;
                } else {
                    total += val.split(/\s+/).filter(Boolean).length;
                }
            });

            if(total > bodyLimit){
                alert("Abstract Body exceeds the total limit of " + bodyLimit + " " + type + "(s). You cannot continue.");
                $(this).focus();
                exceeded = true;
                return false;
            }
        });

        if(exceeded) return false;

        return true;
    }
    function initializeabstract() {
        
       $(document).on("input", "#abstract_title", function(){

            var category_id = $(".category_radio:checked").val() || '';
            if (category_id === '4') {
                // no limit for category 4 — just track count, don't truncate
                var valFree = $(this).val();
                $("#abstract_title_count").text(valFree.trim().length ? valFree.trim().split(/\s+/).filter(Boolean).length : 0);
                return;
            }

            var type = '<?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>';
            var limit = <?= intval($cfg['ABSTRACT.TITLE.WORD.LIMIT']) ?>;
            var val = $(this).val();
            var count = 0;

            if(type === 'character') {
                count = val.length;
                if(count > limit){
                    $(this).val(val.substring(0, limit));
                    count = limit;
                    alert("Maximum " + limit + " characters allowed!");
                }
            } else if(type === 'word') {
                var words = val.trim().split(/\s+/).filter(Boolean);
                count = words.length;
                if(count > limit){
                    $(this).val(words.slice(0, limit).join(" "));
                    count = limit;
                    alert("Maximum " + limit + " words allowed!");
                }
            }

            $("#abstract_title_count").text(count);
        });
        // Word / character limit for Abstract Body / fields
        // $("textarea[checkFor='wordCount']").off("input").on("input", function(){

        //     var type = ($(this).attr("word_type") || "word").trim().toLowerCase();
        //     var limit = <?= intval($cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']) ?>;
        //     var group = $(this).attr("spreadInGroup");
         
        //     var total = 0;

        //     $("textarea[spreadInGroup='"+group+"']").each(function(){

        //         var val = $(this).val().trim();
        //         if(type === "character"){
        //             total += val.length;
        //         } 
        //         else if(type === "word"){
        //             if(val !== ""){
        //                 total += val.split(/\s+/).length;
        //             }
        //         }

        //     });

        //     if(total > limit){

        //         var val = $(this).val();

        //         if(type === "character"){
        //             $(this).val(val.substring(0, val.length - (total - limit)));
        //         }
        //         else if(type === "word"){

        //             var words = val.trim().split(/\s+/);
        //             words.pop(); // remove last typed word
        //             $(this).val(words.join(" "));
        //         }

        //         alert("Total limit of " + limit + " " + type + "s exceeded!");
        //     }
        //     $("#abstract_body_count").text(total);

        // });
       // Track if alert has been shown for each group while over limit
        var alertShownForGroup = {};

       $(document).on("input", "textarea[checkFor='wordCount']", function() {

            var category_id = $(".category_radio:checked").val() || '';
            if (category_id === '4') {
                // no limit for category 4 — skip all enforcement/warnings
                return;
            }

            var type = ($(this).attr("word_type") || "word").trim().toLowerCase();
            var limit = <?= intval($cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']) ?>;
            var group = $(this).attr("spreadInGroup");

            var total = 0;

            $("textarea[spreadInGroup='"+group+"']").each(function() {
                var val = $(this).val().trim();
                if(type === "character") {
                    total += val.length;
                } else if(type === "word") {
                    if(val !== "") {
                        total += val.split(/\s+/).length;
                    }
                }
            });

            $("#abstract_body_count").text(total);

            if(total > limit) {
                $("#abstract_body_count").css("color", "red");
                if (!alertShownForGroup[group]) {
                    alertShownForGroup[group] = true;
                    alert("Warning: Total limit of " + limit + " " + type + "s exceeded! Current: " + total + " " + type + "(s). Please reduce the content.");
                }
            } else {
                $("#abstract_body_count").css("color", "#f3d178");
                if (alertShownForGroup[group]) {
                    alertShownForGroup[group] = false;
                }
            }
        });
        // Re-run populateReview if using review section
        populateReview();
    }
    document.addEventListener("DOMContentLoaded", initializeabstract);
    function updateFileReview() {
    // CATEGORY FILE
        let categoryInput = document.getElementById('formFileAbstractCat');

        if (categoryInput && categoryInput.files.length > 0) {
            let file = categoryInput.files[0];
            let url = URL.createObjectURL(file);

            $('#category_abstract_wrapper').show();
            $('#review_category_file').html(
                `<a href="${url}" target="_blank">View File (${file.name})</a>`
            );
        } else {
            $('#category_abstract_wrapper').hide();
        }

    // ABSTRACT FILE
    let abstractInput = document.getElementById('formFileAbstract');

    if (abstractInput && abstractInput.files.length > 0) {
        let file = abstractInput.files[0];
        let url = URL.createObjectURL(file);

        $('#review_abstract_wrapper').show();
        $('#review_abstract_file').html(
            `<a href="${url}" target="_blank">View File (${file.name})</a>`
        );
    } else {
        $('#review_abstract_wrapper').hide();
    }

    // HOD FILE
    let hodInput = document.getElementById('formFileHod');

    if (hodInput && hodInput.files.length > 0) {
        let file = hodInput.files[0];
        let url = URL.createObjectURL(file);

        $('#review_hod_wrapper').show();
        $('#review_hod_file').html(
            `<a href="${url}" target="_blank">View File (${file.name})</a>`
        );
    } else {
        $('#review_hod_wrapper').hide();
    }

    // NOMINATION FILES
    let html = '';

    $('.award-toggle:checked').each(function () {

        let id = $(this).data('id');
        let fileInput = document.getElementById('formFileAbstract_' + id);

        if (fileInput && fileInput.files.length > 0) {

            let file = fileInput.files[0];
            let url = URL.createObjectURL(file);
            let awardText = $(this)
                .closest('label')
                .clone()                        // work on a copy
                .find('input, .toggleswitch-switch')  // remove input and toggle div
                .remove()
                .end()
                .html();        
            html += `

                <div class="frm_grp span_4">
                    <p class="frm-head">Award</p>
                    <h6>${awardText}</h6>
                </div>

                <div class="frm_grp span_4">
                    <p class="frm-head">Nomination File</p>
                    <h6>
                        <a href="${url}" target="_blank">
                            View File (${file.name})
                        </a>
                    </h6>
                </div>

            `;
        }
    });

    $('#review_nomination_files').html(html);
    
}
$(document).ready(function() {
    var current_fs, next_fs, previous_fs; // fieldsets
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;
    var animating = false; // ✅ prevent double clicks

    setProgressBar(current);

    $(".next").click(function() {
        if (animating) return false; // ⛔ stop if animation is in progress
        //   if (fieldsLoading) {
        //         toastr.warning('Please wait, loading fields...', 'Please wait', {
        //             progressBar: true,
        //             timeOut: 2000
        //         });
        //         return false;
        //     }
            animating = true;

        current_fs = $(this).closest("fieldset");
        next_fs = current_fs.next("fieldset");

        if (next_fs.length === 0) {
            animating = false;
            return false; // no next fieldset
        }

        var isValid = true;
        var checkedRadioGroups = [];

        // Populate review functions (if needed)
        populateReview();
        populateCoAuthorsReview();
         updateFileReview(); // ✅ keep separate
        // Required field validation
       
          current_fs.find("input, select, textarea ,checkbox").each(function () {
      
            if (!$(this).prop("required")) return true;

            // skip anything not currently visible/active (hidden eligibility boxes, etc.)
            if (!$(this).is(":visible")) return true;

            var type = $(this).attr("type");

            if (type === "radio") {
                var name = $(this).attr("name");
                if (!checkedRadioGroups.includes(name)) {
                    checkedRadioGroups.push(name);
                    if ($("input[name='" + name + "']:checked").length === 0) {
                        var msg = $(this).attr("validate") || "Please select an option";
                        toastr.error(msg, "Error", { progressBar: true, timeOut: 3000 });
                        isValid = false;
                        return false;
                    }
                }
            }
            else if (type === "checkbox") {
                if (!$(this).prop("checked")) {
                    var msg = $(this).attr("validate") || "Please confirm this field";
                    toastr.error(msg, "Error", { progressBar: true, timeOut: 3000 });
                    isValid = false;
                    return false;
                }
            }
            else {
                var value = ($(this).val() || "").trim();
                if (value === "") {
                    var msg = $(this).attr("validate") || "This field is required";
                    toastr.error(msg, "Error", { progressBar: true, timeOut: 3000 });
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            }
        });

        if (!checkAbstractLimits()) {
            animating = false; // reset flag so user can correct input
            return false;       // stop moving to next step
        }
        // Category validation
        if (current_fs.hasClass("categoryfieldset")) {
            if (!current_fs.find("input[name='abstract_category']:checked").length) {
                toastr.error('Please select a category', 'Error', { progressBar: true, timeOut: 3000 });
                isValid = false;
            }

            var subCategoryBox = current_fs.find(".subcategory_box:visible");
            if (subCategoryBox.length > 0 && !subCategoryBox.find("input[name='abstract_parent_type']:checked").length) {
                toastr.error('Please select a Sub Category', 'Error', { progressBar: true, timeOut: 3000 });
                isValid = false;
            }

            var subSubCategoryBox = current_fs.find(".subsubcategory_box:visible");
            if (subSubCategoryBox.length > 0 && !subSubCategoryBox.find("input[name='abstract_child_type']:checked").length) {
                toastr.error('Please select a Sub Subcategory', 'Error', { progressBar: true, timeOut: 3000 });
                isValid = false;
            }
              // NEW: explicit eligibility checkbox check
            var eligibilityBox = current_fs.find(".eligibility_box:visible");
            if (eligibilityBox.length > 0 && !eligibilityBox.find("#eligibility_agreed").is(":checked")) {
                $("#eligibility_error").addClass('active').show();
                toastr.error('You must agree to the eligibility criteria to proceed', 'Error', { progressBar: true, timeOut: 3000 });
                isValid = false;
            }
        }

        if (!isValid) {
            animating = false; // ✅ reset flag if validation fails
            return false;
        }

        // Add class active to progress bar
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

        // Show next fieldset
        next_fs.show();

        // Animate current fieldset
        current_fs.animate({ opacity: 0 }, {
            step: function(now) {
                opacity = 1 - now;
                current_fs.css({ 'display': 'none', 'position': 'relative' });
                next_fs.css({ 'opacity': opacity });
            },
            duration: 500,
            complete: function() {
                animating = false; // ✅ re-enable next button
            }
        });

        current++;
        setProgressBar(current);
    });

    $(".previous").click(function() {
        if (animating) return false;
        animating = true;

        current_fs = $(this).closest("fieldset");
        previous_fs = current_fs.prev("fieldset");

        if (previous_fs.length === 0) {
            animating = false;
            return false;
        }

        // Remove class active from progress bar
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

        // Show previous fieldset
        previous_fs.show();

        // Animate current fieldset
        current_fs.animate({ opacity: 0 }, {
            step: function(now) {
                opacity = 1 - now;
                current_fs.css({ 'display': 'none', 'position': 'relative' });
                previous_fs.css({ 'opacity': opacity });
            },
            duration: 500,
            complete: function() {
                animating = false;
            }
        });

        current--;
        setProgressBar(current);
    });

    function setProgressBar(curStep) {
        var percent = (curStep / steps) * 100;
        $(".progress-bar").css("width", percent + "%");
    }
});
   
    // $(document).ready(function() {

    //     var current_fs, next_fs, previous_fs; //fieldsets
    //     var opacity;
    //     var current = 1;
    //     var steps = $("fieldset").length;

    //     setProgressBar(current);

    //     $(".next").click(function() {
    //         if (animating) return false; // ⛔ stop if animation is in progress
    //             animating = true;

    //         current_fs = $(this).parent().parent();
    //         next_fs = $(this).parent().parent().next();
    //         var isValid = true;
    //         populateReview();
    //         populateCoAuthorsReview() 
    //          current_fs.find("input, select, textarea").each(function() {

    //             if ($(this).prop("required") && $(this).val().trim() === "") {

    //             var msg = $(this).attr("validate") || "This field is required";

    //             toastr.error(msg, "Error", {
    //                 progressBar: true,
    //                 timeOut: 3000,
    //                 showMethod: "slideDown",
    //                 hideMethod: "slideUp",
    //                 direction: "ltr"
    //             });

    //             $(this).focus();
    //             isValid = false;
    //             return false; // ⛔ stop `.each()` loop
    //             }
    //         });
    //         if (current_fs.hasClass("categoryfieldset")) {

    //             // check if any category is selected
    //             if (!current_fs.find("input[name='abstract_category']:checked").length) {

    //                 toastr.error('Please select a category', 'Error', {
    //                     progressBar: true,
    //                     timeOut: 3000,
    //                     showMethod: "slideDown",
    //                     hideMethod: "slideUp"
    //                 });

    //                 isValid = false;
    //                 // return false; // ⛔ stop Continue
    //             }
    //            // SUB CATEGORY validation
    //             var subCategoryBox = current_fs.find(".subcategory_box:visible");

    //             if (subCategoryBox.length > 0 && !subCategoryBox.find("input[name='abstract_parent_type']:checked").length) {

    //                 toastr.error('Please select a Sub Category', 'Error', {
    //                     progressBar: true,
    //                     timeOut: 3000,
    //                     showMethod: "slideDown",
    //                     hideMethod: "slideUp"
    //                 });

    //                 isValid = false;
    //             }
    //            var subSubCategoryBox = current_fs.find(".subsubcategory_box:visible");

    //             if (subSubCategoryBox.length > 0 && !subSubCategoryBox.find("input[name='abstract_child_type']:checked").length) {

    //                 toastr.error('Please select a Sub Subcategory', 'Error', {
    //                     progressBar: true,
    //                     timeOut: 3000,
    //                     showMethod: "slideDown",
    //                     hideMethod: "slideUp"
    //                 });

    //                 isValid = false;
    //             }
    //         }
    //         if (!isValid) {
    //             return false; // ⛔ stop going to next step
    //         }
    //         //Add Class Active
    //         $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

    //         //show the next fieldset
    //         next_fs.show();
    //         //hide the current fieldset with style
    //         current_fs.animate({
    //             opacity: 0
    //         }, {
    //             step: function(now) {
    //                 // for making fielset appear animation
    //                 opacity = 1 - now;

    //                 current_fs.css({
    //                     'display': 'none',
    //                     'position': 'relative'
    //                 });
    //                 next_fs.css({
    //                     'opacity': opacity
    //                 });
    //             },
    //             duration: 500
    //         });
    //         setProgressBar(++current);
    //     });

    //     $(".previous").click(function() {

    //         current_fs = $(this).parent().parent();
    //         previous_fs = $(this).parent().parent().prev();

    //         //Remove class active
    //         $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

    //         //show the previous fieldset
    //         previous_fs.show();

    //         //hide the current fieldset with style
    //         current_fs.animate({
    //             opacity: 0
    //         }, {
    //             step: function(now) {
    //                 // for making fielset appear animation
    //                 opacity = 1 - now;

    //                 current_fs.css({
    //                     'display': 'none',
    //                     'position': 'relative'
    //                 });
    //                 previous_fs.css({
    //                     'opacity': opacity
    //                 });
    //             },
    //             duration: 500
    //         });
    //         setProgressBar(--current);
    //     });

    //     function setProgressBar(curStep) {
    //         var percent = parseFloat(100 / steps) * curStep;
    //         percent = percent.toFixed();
    //         $(".progress-bar")
    //             .css("width", percent + "%")
    //     }

    //     // $(".submit").click(function() {
    //     //     return false;
    //     // })

    // });
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
   function setAsPresenter(obj) {
    if ($(obj).prop('checked')) {

        $('#abstract_author_email').val($('#user_email_id').val());
        $('#abstract_author_phone_no').val($('#user_mobile').val());
        $('#abstract_author_first_name').val($('#user_first_name').val());
        $('#abstract_author_phone_isd_code').val($('#user_usd_code').val());
        $('#abstract_author_middle_name').val($('#user_middle_name').val());
        $('#abstract_author_last_name').val($('#user_last_name').val());
        $('#abstract_author_city').val($('#user_city').val());
        $('#abstract_author_pincode').val($('#user_postal_code').val());
        $('#abstract_author_institute').val($('#user_institution').val());
        $('#abstract_author_department').val($('#user_depertment').val());
        $('#abstract_author_address').val($('#user_address').val());
        $('#abstract_author_country').val($('#user_country').val());
        $('#abstract_author_state').val($('#user_state').val());
        $('#abstract_author_title').val($('#user_initial_title').val());

        const container = document.querySelector('.registration_right_body_content_author');
        const fields = container.querySelectorAll('input, select, textarea');

         $('.registration_right_body_content_author')
            .find('input, select, textarea')
            .prop('readonly', true)

        setTimeout(function(){
            $('#abstract_author_state').val($('#user_state').val());
        },300);

        $('#isPresenter').val('Y');

    } else {

        $('#abstract_author_email').val('');
        $('#abstract_author_phone_no').val('');
        $('#abstract_author_first_name').val('');
        $('#abstract_author_last_name').val('');
        $('#abstract_author_city').val('');
        $('#abstract_author_pincode').val('');
        $('#abstract_author_institute').val('');
        $('#abstract_author_department').val('');
        $('#abstract_author_middle_name').val('');
        $('#abstract_author_address').val('');
        $('#abstract_author_phone_isd_code').val('+91');
        $('#abstract_author_title').val('');

        $('#abstract_author_country').val('');
        $('#abstract_author_state').val('');
        $('.registration_right_body_content_author')
            .find('input, select, textarea')
            .prop('readonly', false)


        $('#isPresenter').val('N');
    }
}
</script>
<script>
$(document).ready(function () {

    var authorCount = 0;

    // Add Co-Author
    $(document).on("click", ".add_guest", function (e) {
        e.preventDefault();

        $("#coauthor_blank_section").hide();
        $("#coauthor_form_section").show();

        authorCount++;

        var newAuthor = `
        <li class="coauthor_item">
          <div class="form_grid">
                <div class="frm_grp span_4 coauthor_head">
                    <span class="coauthor_number">1</span>
                    <n>Co-Author</n>
                    <a href="#" class="guest_action delete_author"><?php delete(); ?></a>
                </div>
                <div class="frm_grp span_0 span_2">
                    <p class="frm-head">Email Address</p>
                    <input type="email"  name="abstract_coauthor_email[]"  id="abstract_coauthor_email"    validate="Please Enter Email Address" >
                </div>
                <div class="frm_grp span_0 span_2">
                    <p class="frm-head">Mobile Number </p>
                    <div class="sub_frm_grp form_grid">
                        <input type="text" value="+91" name="abstract_coauthor_usd_code[]" required validate="Please Enter Mobile Prefix">
                        <input class="span_3" validate="Please Enter Mobile Number"    name="abstract_coauthor_mobile[]" id="abstract_coauthor_mobile"  onkeypress="return isNumber(event)"  maxlength="10">
                    </div>
                </div>
                <div class="frm_grp span_1">
                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                    <select name="abstract_coauthor_title[]"
                        class="<?= $disabledclass ?>"
                        <?= $disabled ?>
                        validate="Please select your title"
                        style="width:100%; padding:5px;" required >
                        <option value="">Select Title</option>
                        <option value="Dr" >Dr.</option>
                        <option value="Prof">Prof.</option>
                        <option value="Mr" >Mr.</option>
                        <option value="Ms">Ms.</option>
                    </select>
                </div>
                <div class="frm_grp span_3">
                    <p class="frm-head">First Name <i class="mandatory">*</i> </p>
                    <input placeholder="Enter First Name" required  name="abstract_coauthor_first_name[]" id="abstract_coauthor_first_name" validate="Please Enter First Name" >
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Middle Name</p>
                    <input placeholder="Middle Name"  name="abstract_coauthor_middle_name[]" id="abstract_coauthor_middle_name">
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Last Name  <i class="mandatory">*</i></p>
                    <input  placeholder="Last Name" required name="abstract_coauthor_last_name[]" id="abstract_coauthor_last_name"  validate="Please Enter Last Name">
                </div>
                <div class="frm_grp span_4">
                    <p class="frm-head">Address</p>
                    <input name="abstract_coauthor_address[]" id="abstract_coauthor_address"  validate="Please Enter Your Address" type="text" >
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Country </p>
                        <select class="coauthor_country  <?= $disabledclass ?>" 
                        name="abstract_coauthor_country[]"
                        forType="country"
                        
                        <?= $disabled ?>
                        validate="Please Select Country"
                        style="flex:1;">
                        <option value="">-- Select Country --</option>
                        <?php
                        $sqlFetchCountry = array();
                        $sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                                            WHERE `status` = ? 
                                                                            ORDER BY `country_name` ASC";
                        $sqlFetchCountry['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                        $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                        if ($resultFetchCountry) {
                            foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                        ?>
                            <option value="<?= $rowFetchCountry['country_id'] ?>" >
                                <?= $rowFetchCountry['country_name'] ?>
                            </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">State <i class="mandatory">*</i></p>
                    <div class="d-flex align-items-center">
                        <select class="coauthor_state coauthor_state_${authorCount} <?= $disabledclass ?>"
                            name="abstract_coauthor_state[]"
                            data-coauthor-index="${authorCount}"
                            required
                            validate="Please Select State"
                            style="flex:1;">
                            <option value="">-- Select Country First --</option>
                        </select>
                    </div>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">City </p>
                        <input type="text"  name="abstract_coauthor_city[]" id="abstract_coauthor_city"  validate="Please Enter City" >
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Pin </p>
                    <div class="d-flex align-items-center">

                        <input type="text"
                        class=" <?= $disabledclass ?>"
                        name="abstract_coauthor_pincode[]"
                        id="abstract_coauthor_pincode"
                      
                        placeholder="Postal Code"
                        <?= $disabled ?>
                        validate="Please enter postal code"
                        autocomplete="nope"
                        style="flex:1;" onkeypress="return isNumber(event)" autocomplete="nope" >
                    </div>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Institute</p>
                    <div class="d-flex align-items-center">

                        <input type="text"
                        class=" <?= $disabledclass ?>"
                        name="abstract_coauthor_institute[]"
                        id="abstract_coauthor_institute"
                        value=""
                        placeholder="Institute"
                        validate="Please enter institute"
                        autocomplete="nope"
                        >
                    </div>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Department </p>
                    <div class="d-flex align-items-center">

                        <input type="text"
                        class=" <?= $disabledclass ?>"
                        name="abstract_coauthor_department[]"
                        id="abstract_coauthor_department"
                        value=""
                        placeholder="Department"
                        validate="Please enter department"
                        >
                    </div>
                </div>
            </div>
        </li>
        `;

        $(".coauthor_wrap").append(newAuthor);
        populateCoAuthorsReview();
       updateAuthorNumbers();
    });


    // Delete Co-Author
    $(document).on("click", ".delete_author", function (e) {

        e.preventDefault();

        $(this).closest(".coauthor_item").remove();

        authorCount--;

        updateAuthorNumbers();

        if (authorCount <= 0) {
            $("#coauthor_form_section").hide();
            $("#coauthor_blank_section").show();
            authorCount = 0;
        }

    });


    // Update numbering after delete
    function updateAuthorNumbers() {

        var i = 1;

        $(".coauthor_item").each(function () {

            $(this).find(".coauthor_number").text(i);
            $(this).find("n").text("Co-Author " + i);

            i++;

        });

        authorCount = i - 1;

    }

});
</script>
<script>

    $(document).ready(function(){
       $("form").on("keydown", function(e) {
            if (e.key === "Enter" && e.target.type !== "textarea") {
                e.preventDefault();
                return false;
            }
        });

        // Force form submit on Confirm button click
         $("#abstractRequestForm").on("keydown", function(e) {
        if (e.key === "Enter" && e.target.type !== "textarea") {
            e.preventDefault();
            return false;
        }
        });

        // Force form submit on Confirm button click
        $(document).on("click", "button[type='submit'][name='submit']", function(e){
             $("#abstractRequestForm").find(":input").filter(function(){
                return $(this).is(":hidden");
            }).removeAttr("required");

            // Force submit
            HTMLFormElement.prototype.submit.call(document.getElementById("abstractRequestForm"));
        });
    });
    // $(".category_radio").click(function(){

    // var cat_id = $(this).val();

    // $(".subcategory_box").hide();
    // $("#subcat_"+cat_id).show();

    // $(".subsubcategory_box").hide();
    // // Clear previous review fields for subcategory and sub-subcategory
    //    $(".subcategory_box input[type='radio'], .subsubcategory_box input[type='radio']").prop("checked", false);
      
    // });

 // For the category radio button - update this function
    $(document).on("click", ".category_radio", function(){
        var cat_id = $(this).val();
        var cat_name = $(this).data('category-name') || '';
        
        // Get the actual text from the label
        if (!cat_name) {
            cat_name = $(this).closest('label').find('g').text().trim();
        }
        
        // Hide all subcategory boxes and eligibility boxes
        $(".subcategory_box").hide();
        $(".subsubcategory_box").hide();
        $(".eligibility_box").hide();
        $(".eligibility_box").removeClass('active');
        
        // Check if this is "Published Original Research"
        if (cat_name && cat_name.toLowerCase().includes('published original research')) {
            // Show eligibility box
            var $elBox = $("#eligibility_"+cat_id);
            $elBox.show();
            $elBox.addClass('active');
            // Uncheck the checkbox when switching
            $("#eligibility_agreed").prop('checked', false);
            $("#eligibility_error").hide();
        } else {
            // Show regular subcategories
            $("#subcat_"+cat_id).show();
        }
    });
    $(".subcategory_radio").click(function(){

    var sub_id = $(this).val();

    $(".subsubcategory_box").hide();
    $("#presentation_"+sub_id).show();

    });
    var fieldsLoading = false; // global flag
   var fieldsRequestToken = 0;

    $(document)
    .off("change", ".category_radio, .subcategory_radio, .sub_subcategory_radio") // prevent duplicate bindings
    .on("change", ".category_radio, .subcategory_radio, .sub_subcategory_radio", function () {

        let category_id = $(".category_radio:checked").val() || '';
        let hasSubCategory = $("#subcat_" + category_id).length > 0;
        let sub_category_id = '';
        let sub_subcategory_Id = '';

        if (hasSubCategory) {
            sub_category_id = $(".subcategory_radio:checked").val() || '';
            let hasSubSubCategory = $(".sub_subcategory_radio").length > 0;
            if (hasSubSubCategory) {
                sub_subcategory_Id = $(".sub_subcategory_radio:checked").val() || '';
            }
        }

        let researchCatCount = $(".category_radio:checked").data('researchcatcount') || '';

        let postData = {
            category_id: category_id,
            sub_category_id: sub_category_id,
            sub_subcategory_Id: sub_subcategory_Id,
            researchCatCount: researchCatCount
        };

        // --- SHOW LOADER ---
        fieldsLoading = true;
        $("#abstractFieldsLoader").show();

        // this request's unique id — only the LATEST one is allowed to update the DOM
        var thisRequest = ++fieldsRequestToken;

        $.post("abstract_user.php", postData, function (data) {

            // if a newer request has started since this one was sent, ignore this stale response
            if (thisRequest !== fieldsRequestToken) {
                return;
            }

            $('#fields_area').html($(data).find('#fields_area').html());
            $('#fields_area1').html($(data).find('#fields_area1').html());

            fieldsLoading = false;
            $("#abstractFieldsLoader").hide();

        }).fail(function() {
            if (thisRequest === fieldsRequestToken) {
                fieldsLoading = false;
                $("#abstractFieldsLoader").hide();
                toastr.error('Could not load fields for this category. Please try again.', 'Error', {
                    progressBar: true, timeOut: 3000
                });
            }
        });

    });
    $('.skip').click(function() {
      var current_fs = $(this).closest("fieldset");
      var next_fs = current_fs.next("fieldset"); // next fieldset
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
    // $(document).on("input", "#abstract_title", function () {

    //     var type  = "<?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>";   // character / word
    //     var limit = <?= (int)$cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>;

    //     var text = $(this).val();

    //     if(type === "character") {

    //         if(text.length > limit){
    //             alert("Maximum " + limit + " characters allowed");
    //             $(this).val(text.substring(0, limit));
    //         }

    //     } else if(type === "word") {

    //         var words = text.trim().split(/\s+/);

    //         if(words.length > limit){
    //             alert("Maximum " + limit + " words allowed");
    //             words = words.slice(0, limit);
    //             $(this).val(words.join(" "));
    //         }

    //     }

    // });
</script>
<!-- <script id="abstractContentLimit">
    
$(document).on("input", "textarea[spreadInGroup='abstractContent']", function () {

    var type  = "<?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>";
    var limit = <?= (int)$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>;

    var total = 0;
    var fields = $("textarea[spreadInGroup='abstractContent']");
    console.log(type);
        console.log(limit);

    if(type === "character"){

        fields.each(function(){
            total += $(this).val().length;
        });
        console.log(total);

        if(total > limit){
            toastr.error("Maximum " + limit + " characters allowed for Abstract Content");
            $(this).val($(this).val().substring(0, $(this).val().length - (total-limit)));
        }

    } else if(type === "word"){

        fields.each(function(){
            var text = $(this).val().trim();
            if(text.length){
                total += text.split(/\s+/).length;
            }
        });
        console.log(total);

        if(total > limit){

            toastr.error("Maximum " + limit + " words allowed for Abstract Content");

            var words = $(this).val().trim().split(/\s+/);
            words.pop(); // remove last word typed
            $(this).val(words.join(" "));
        }

    }

});

</script> -->
<script>
$(document).ready(function(){
    $(document).on("click", ".editStep", function(e){

        e.preventDefault();

        var target = $(this).data("target");
        var target_fs = $("#" + target);

        if(target_fs.length){

            $("fieldset").hide().css("opacity",1);

            target_fs.show().css({
                "opacity": 1,
                "position": "relative"
            });

            var index = $("fieldset").index(target_fs);

            $("#progressbar li").removeClass("active");

            for (var i = 0; i <= index; i++) {
                $("#progressbar li").eq(i).addClass("active");
            }

            // update progress bar
            current = index + 1;
            setProgressBar(current);
        }

    });
    $(document).on("change", ".coauthor_country", function(){
        var countryId = $(this).val();
        var $this = $(this);
        
        // Method 1: Find the state dropdown by traversing up to the coauthor_item
        var $coauthorItem = $this.closest(".coauthor_item");
        var $stateDropdown = $coauthorItem.find(".coauthor_state");
        
        // OR Method 2: If Method 1 doesn't work, try finding the parent .form_grid first
        // var $formGrid = $this.closest(".form_grid");
        // var $stateDropdown = $formGrid.find(".coauthor_state");
        
        console.log("Co-author item found: ", $coauthorItem.length > 0 ? "Yes" : "No");
        console.log("State dropdown found: ", $stateDropdown.length > 0 ? "Yes" : "No");
        
        if(!$stateDropdown.length){
            // Fallback: Try to find by data attribute
            var coauthorIndex = $this.data('coauthor-index');
            if(coauthorIndex){
                $stateDropdown = $(".coauthor_state_" + coauthorIndex);
                console.log("Fallback state dropdown found: ", $stateDropdown.length > 0 ? "Yes" : "No");
            }
        }
        
        if(countryId != ""){
            $.ajax({
                type: "POST",
                url: "returnData.process.php",
                data: "act=generateStateList&countryId=" + countryId,
                dataType: "html",
                async: false,
                success: function(response){
                    console.log("AJAX Response received, updating state dropdown");
                    $stateDropdown.html(response);
                    $stateDropdown.removeAttr("disabled");
                },
                error: function(xhr, status, error){
                    console.error("AJAX Error:", error);
                    $stateDropdown.html('<option value="">Error loading states</option>');
                }
            });
        }else{
            $stateDropdown.html('<option value="">-- Select Country First --</option>');
            $stateDropdown.attr("disabled", "disabled");
        }
    });
 });
            $('#formFileAbstract').on('change', function() {
				var file = this.files[0];
				var flag = 0;
				if (file) {
					var fileSize = file.size; // in bytes
					var fileType = file.type;

					var validExtensions = new Array();
					var abstract_file_types = $('#abstract_file_types').val();
					if (abstract_file_types.includes("pdf")) {
						validExtensions.push("pdf");
					}
					if (abstract_file_types.includes("image")) {
						validExtensions.push("jpg");
						validExtensions.push("jpeg");
						validExtensions.push("png");
					}
					if (abstract_file_types.includes("word")) {
						validExtensions.push("doc");
						validExtensions.push("docx");
					}
					var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
					var fileTypeErr = "Only " + jsonString + " files are allowed";
					// var validExtensions = ["doc", "pdf", "docx"]
					var fileName = file.name.split('.').pop();

					console.log(fileName);

					if (fileSize > 5 * 1024 * 1024) {

						var prevFile = $('#temp_abstract_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_abstract_filename').val('');
						$('#original_abstract_file_name').val('');
						$('#abstractFileNameUploaded').text('');


						toastr.error('File size exceeds 5MB limit.', 'Error', {
							"progressBar": true,
							"timeOut": 5000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						this.value = ''; // Clear the file input
						return;
					}

					if (validExtensions.indexOf(fileName) == -1) {
						var prevFile = $('#temp_abstract_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_abstract_filename').val('');
						$('#original_abstract_file_name').val('');
						$('#abstractFileNameUploaded').text('');

						toastr.error(fileTypeErr, 'Error', {
							"progressBar": true,
							"timeOut": 4000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						this.value = '';
						return;
					}
					// File is valid
					$('#fileError').text('');
				} else {
					var prevFile = $('#temp_abstract_filename').val();
					if (prevFile != '') {
						deleteTempFile(prevFile);
					}
					$('#temp_abstract_filename').val('');
					$('#original_abstract_file_name').val('');
					$('#abstractFileNameUploaded').text('');


					toastr.error('Please select a file to upload.', 'Error', {
						"progressBar": true,
						"timeOut": 4000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
					flag = 1;
				}

				if (flag == 0) {

					var sessionId = $('#sessionId').val();
					var d = new Date();
					var uploadTime = d.getTime();
					var prevFile = $('#temp_abstract_filename').val();
					// createDynamicFileName
					var dynamicFileName = 'ABSTRACT_' + sessionId + uploadTime + '_' + file['name'];
					// alert(dynamicFileName);
					$('#temp_abstract_filename').val(dynamicFileName);
					$('#original_abstract_file_name').val(file['name']);
					$('#abstractFileNameUploaded').text(file['name']);

					uploadFile(file, 'ABSTRACT_' + sessionId + uploadTime, prevFile); // Upload the file
					toastr.success('File uploaded successfully.', 'Success', {
						"progressBar": true,
						"timeOut": 2000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});

				}


			});


			$('#formFileHod').on('change', function() {
				var file = this.files[0];

				var flag = 0;
				if (file) {
					var fileSize = file.size; // in bytes
					var fileType = file.type;

					var validExtensions = new Array();
					var hod_consent_file_types = $('#hod_consent_file_types').val();
					if (hod_consent_file_types.includes("pdf")) {
						validExtensions.push("pdf");
					}
					if (hod_consent_file_types.includes("image")) {
						validExtensions.push("jpg");
						validExtensions.push("jpeg");
						validExtensions.push("png");
					}
					if (hod_consent_file_types.includes("word")) {
						validExtensions.push("doc");
						validExtensions.push("docx");
					}
					var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
					var fileTypeErr = "Only " + jsonString + " files are allowed";
					// var validExtensions = ["jpg", "pdf", "jpeg", "gif", "png", "pdf"]
					var fileName = file.name.split('.').pop();

					console.log(fileName);

					if (fileSize > 5 * 1024 * 1024) {
						var prevFile = $('#temp_consent_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_consent_filename').val('');
						$('#original_consent_file_name').val('');
						$('#concentFileNameUploaded').text('');


						toastr.error('File size exceeds 5MB limit.', 'Error', {
							"progressBar": true,
							"timeOut": 4000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});
						flag = 1;
						this.value = ''; // Clear the file input
						return;
					}

					if (validExtensions.indexOf(fileName) == -1) {
						var prevFile = $('#temp_consent_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_consent_filename').val('');
						$('#original_consent_file_name').val('');
						$('#concentFileNameUploaded').text('');


						toastr.error(fileTypeErr, 'Error', {
							"progressBar": true,
							"timeOut": 4000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						this.value = '';
						return;
					}
					// File is valid
					$('#fileError').text('');
				} else {
					var prevFile = $('#temp_consent_filename').val();
					if (prevFile != '') {
						deleteTempFile(prevFile);
					}
					$('#temp_consent_filename').val('');
					$('#original_consent_file_name').val('');
					$('#concentFileNameUploaded').text('');


					toastr.error('Please select a file to upload.', 'Error', {
						"progressBar": true,
						"timeOut": 4000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
					flag = 1;
				}

				if (flag == 0) {
					var sessionId = $('#sessionId').val();
					var d = new Date();
					var uploadTime = d.getTime();
					var prevFile = $('#temp_consent_filename').val();
					// createDynamicFileName
					var dynamicFileName = 'CONSENT_' + sessionId + uploadTime + '_' + file['name'];
					// alert(dynamicFileName);
					$('#temp_consent_filename').val(dynamicFileName);
					$('#original_consent_file_name').val(file['name']);
					$('#concentFileNameUploaded').text(file['name']);

					uploadFile(file, 'CONSENT_' + sessionId + uploadTime, prevFile); // Upload the file

					toastr.success('File uploaded successfully.', 'Success', {
						"progressBar": true,
						"timeOut": 2000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
				}
			});
           $(document).on('change', '.award-toggle', function () {
            var id = $(this).data('id');
            var fileInput = $('#formFileAbstract_' + id);
            // === New logic for "already nominated two" ===
                if ($(this).data('block') == '1' && $(this).is(':checked')) {
                    alert("You have already nominated two of your Research Paper submissions.");
                    $(this).prop('checked', false);  // uncheck automatically
                    return; // stop further processing
                }

            if ($(this).is(':checked')) {
                fileInput.attr('required', true);   // make required
                $('#upload_section_' + id).show();  // show section
            } else {
                fileInput.removeAttr('required');   // remove required
                fileInput.val('');                  // clear selected file (optional)
                $('#upload_section_' + id).hide();  // hide section
            }
        });
        // $(document).on('change', '.nomination-file', function () {
        //     var allowedTypes = $(this).data('types').toString().toLowerCase().split(',');
        //     var fileName = $(this).val().split('\\').pop().toLowerCase();
        //     var fileExt = fileName.split('.').pop();

        //     if ($.inArray(fileExt, allowedTypes) === -1) {
        //         alert("Invalid file type. Allowed: " + allowedTypes.join(', '));
        //         $(this).val(''); // clear file
        //     }
        // });
        //  $(document).on('change', '.abstract-file', function () {
        //     var allowedTypes = $(this).data('types').toString().toLowerCase().split(',');
        //     var fileName = $(this).val().split('\\').pop().toLowerCase();
        //     var fileExt = fileName.split('.').pop();

        //     if ($.inArray(fileExt, allowedTypes) === -1) {
        //         alert("Invalid file type. Allowed: " + allowedTypes.join(', '));
        //         $(this).val(''); // clear file
        //     }
        // });
        //  $(document).on('change', '.consent-file', function () {
        //     var allowedTypes = $(this).data('types').toString().toLowerCase().split(',');
        //     var fileName = $(this).val().split('\\').pop().toLowerCase();
        //     var fileExt = fileName.split('.').pop();

        //     if ($.inArray(fileExt, allowedTypes) === -1) {
        //         alert("Invalid file type. Allowed: " + allowedTypes.join(', '));
        //         $(this).val(''); // clear file
        //     }
        // });
        //  $(document).on('change', '.category-file', function () {
        //     var allowedTypes = $(this).data('types').toString().toLowerCase().split(',');
        //     var fileName = $(this).val().split('\\').pop().toLowerCase();
        //     var fileExt = fileName.split('.').pop();

        //     if ($.inArray(fileExt, allowedTypes) === -1) {
        //         alert("Invalid file type. Allowed: " + allowedTypes.join(', '));
        //         $(this).val(''); // clear file
        //     }
        // });
        $(document).on('change', '.nomination-file, .abstract-file, .consent-file, .category-file', function () {

            // Mapping of logical types
            var typeMap = {
                pdf: ['pdf'],
                image: ['jpg', 'jpeg', 'png'],
                word: ['doc', 'docx']
            };

            // Get allowed types from data attribute
            var allowedTypesRaw = $(this).data('types').toString().toLowerCase().split(',');
            var allowedExtensions = [];

            // Convert to actual extensions
            $.each(allowedTypesRaw, function (i, type) {
                type = $.trim(type);

                if (typeMap[type]) {
                    allowedExtensions = allowedExtensions.concat(typeMap[type]);
                } else {
                    // If already extension (fallback)
                    allowedExtensions.push(type);
                }
            });

            // Get selected file
            var fileName = $(this).val().split('\\').pop().toLowerCase();

            if (fileName === '') return; // no file selected

            var fileExt = fileName.split('.').pop();

            // Validate
            if ($.inArray(fileExt, allowedExtensions) === -1) {
                alert("Invalid file type.\nAllowed: " + allowedExtensions.join(', '));
                $(this).val(''); // clear file
            }
        });
 </script>
</html>