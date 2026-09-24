<?php
include_once("includes/source.php");
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
?>
<body>
    
<?php
//setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();
include_once('header.php');
?>

<?php
// $loginDetails 	 = login_session_control();

$sql_abs_dele  			  = array();
$sql_abs_dele['QUERY']     = " SELECT `applicant_id` 
                            FROM " . _DB_ABSTRACT_REQUEST_ . " 
                            WHERE `status` = ?
                                AND `id` = ?";
//AND `abstract_child_type` IN ('Oral','Poster')

$sql_abs_dele['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
$sql_abs_dele['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' => $_REQUEST['abstractDelegateId'], 'TYP' => 's');
$res_abs_dele = $mycms->sql_select($sql_abs_dele);
$delegateId 	 = $res_abs_dele[0]['applicant_id'];

if (isset($_REQUEST['abstractDelegateId']) && trim($_REQUEST['abstractDelegateId']) != '') {
  $abstractDelegateId = trim($_REQUEST['abstractDelegateId']);
  $userRec = getUserDetails($delegateId);
   $readonly = '';
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
    $sqlFetch['PARAM'][] = array('FILD' => 'applicant_id', 'DATA' => $delegateId, 'TYP' => 's');

    $CountCategory = $mycms->sql_select($sqlFetch, false);
    $researchCatCount = $CountCategory[0]['TOTAL'];
} else {
  $mycms->removeAllSession();
  $mycms->removeSession('SLIP_ID');
   $readonly = '';
    $researchCatCount = 0;
}
// echo "<pre>";
// print_r($researchCatCount);
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
  <?php
    $abstractId 				   = addslashes(trim($editabstractId));
    $abstractCounter               = 0;

    $sqlAbstractDetails           = array();
    $sqlAbstractDetails['QUERY']            =  "    SELECT abstractRequest.*,
                                                            abstractTopic.abstract_topic AS abstract_topic,
                                                            
                                                            registeredDelegates.user_email_id,
                                                            registeredDelegates.user_unique_sequence,
                                                            registeredDelegates.user_registration_id,
                                                            
                                                            IFNULL(registeredDelegates.user_title, '') AS user_title,
                                                            IFNULL(registeredDelegates.user_first_name, '') AS user_first_name,
                                                            IFNULL(registeredDelegates.user_middle_name, '') AS user_middle_name,
                                                            IFNULL(registeredDelegates.user_last_name, '') AS user_last_name,
                                                            
                                                            registeredDelegates.isRegistration,
                                                            registeredDelegates.isWorkshop,
                                                            
                                                            
                                                            registeredDelegates.registration_payment_status,
                                                            registeredDelegates.workshop_payment_status,
                                                            
                                                            country.country_name AS author_country_name,
                                                            state.state_name AS author_state_name,
                                                            
                                                            IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
                                                            IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
                                                            IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName
                                                        
                                                        FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest 
                                                        
                                            LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
                                                            ON abstractRequest.abstract_topic_id = abstractTopic.id 
                                            
                                            LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
                                                            ON abstractRequest.applicant_id = registeredDelegates.id 
                                            
                                            LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
                                                            ON abstractRequest.abstract_author_country_id = country.country_id
                                                        
                                            LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
                                                            ON abstractRequest.abstract_author_state_id = state.st_id 
                                                            
                                                        WHERE abstractRequest.status != ?
                                                            AND abstractRequest.tags = ?
                                                        AND abstractRequest.id =?";

    $sqlAbstractDetails['PARAM'][]    = array('FILD' => 'abstractRequest.status',  'DATA' => 'D',         'TYP' => 's');
    $sqlAbstractDetails['PARAM'][]    = array('FILD' => 'abstractRequest.tags',    'DATA' => 'Abstract',  'TYP' => 's');
    $sqlAbstractDetails['PARAM'][]    = array('FILD' => 'abstractRequest.id',      'DATA' => $abstractDelegateId, 'TYP' => 's');

    $resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
if ($resultAbstractDetails) {
    foreach ($resultAbstractDetails as $i => $rowAbstractDetails) {
        $abstractCounter++;
        $rowUserDetails = getUserDetails($rowAbstractDetails['applicant_id']);


?>
<script language="javascript">
    
 var currentAbstractCategoryId = "<?= $rowAbstractDetails['abstract_cat'] ?>"; // NEW

</script>
<form name="caseRequestEditForm" id="caseRequestEditForm" action="<?= _BASE_URL_ ?>abstract_request.process.php" method="post" enctype="multipart/form-data" onSubmit="return abstractEditValidationform(this);" indx='<?= $rowAbstractDetails['abstract_submition_code'] ?>'>
            <input type="hidden" name="act" value="editAbstractFileFront" />
            <input type="hidden" name="delegateId" id="delegateId" value="<?= $rowAbstractDetails['applicant_id']?>" />
            <input type="hidden" name="abstract_id" value="<?= $rowAbstractDetails['id'] ?>">
    <div class="registration_wrap">
        <div class="registartion_head">
            <a href="index.php"><i class="fal fa-arrow-left"></i>Back</a>
            <p><span>Registered Email Id</span><?= $rowUserDetails['user_email_id'] ?></p>
        </div>
        <div class="registration_inner">
            <div class="registration_left">
                <div class="registration_left_head">
                    <h5><?=$rowInfo['company_conf_name']?></span></h5>
                    <h6>Abstract Submission Portal</h6>
                </div>
                <ul id="progressbar">
                    <!-- <li id="primaryauthor"><?php user(); ?><span>Presenter Details</span></li> -->
                    <li  class="active" id="coauthor"><?php duser(); ?><span>Author & Co-Author Details</span></li>
                    <!-- <li id="abstractcategory"><i class="fal fa-tag"></i><span>Submission Category</span></li> -->
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
                            <!-- <div class="registration_right_body_head_right">
                                <label class="toggleswitch">
                                    <input class="toggleswitch-checkbox" type="checkbox" name="willBePresenter" use="willBePresenter" id="willBePresenter" onclick="setAsPresenter(this)">
                                    Same as Presentar Details?<div class="toggleswitch-switch"></div>
                                </label>
                            </div> -->
                        </div>
                        <div class="registration_right_body_content mb-3 registration_right_body_content_author">
                            <div class="form_grid">
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                    <input type="email"  name="abstract_author_edit_email"  id="abstract_author_edit_email"    validate="Please Enter Email Address" value="<?= $rowAbstractDetails['abstract_author_email_id'] ?>" required>
                                </div>
                                <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                     <div class="sub_frm_grp form_grid">
                                        <input type="text" value="+91" name="abstract_author_edit_phone_code" value="<?= $rowAbstractDetails['abstract_author_phone_code'] ?>" required validate="Please Enter Mobile Prefix">
                                        <input class="span_3" validate="Please Enter Mobile Number"  required  name="abstract_author_edit_phone_no" id="abstract_author_edit_phone_no<?= $rowAbstractDetails['abstract_submition_code'] ?>"  onkeypress="return isNumber(event)"  maxlength="10"  value="<?= $rowAbstractDetails['abstract_author_phone_no'] ?>">
                                    </div>
                                </div>
                                 <div class="frm_grp span_1">
                                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                                    <select name="abstract_author_edit_title" <?= $readonly ?>  id="user_initial_title"
                                        class="<?= $disabledclass ?>"
                                        <?= $disabled ?>
                                        validate="Please select your title"
                                        style="width:100%; padding:5px;" required>
                                        <option value="">Select Title</option>
                                        <option value="Dr" <?= strtoupper($rowAbstractDetails['abstract_author_title']) == 'DR' ? 'selected' : '' ?>>Dr.</option>
                                        <option value="Prof" <?= strtoupper($rowAbstractDetails['abstract_author_title']) == 'PROF' ? 'selected' : '' ?>>Prof.</option>
                                        <option value="Mr" <?= strtoupper($rowAbstractDetails['abstract_author_title']) == 'MR' ? 'selected' : '' ?>>Mr.</option>
                                        <option value="Ms" <?= strtoupper($rowAbstractDetails['abstract_author_title']) == 'MS' ? 'selected' : '' ?>>Ms.</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                    <input placeholder="Enter First Name" value="<?= ($rowAbstractDetails['abstract_author_first_name'] != '') ? ($rowAbstractDetails['abstract_author_first_name']) : '' ?>" required name="abstract_author_edit_first_name" id="abstract_author_edit_first_name" validate="Please Enter First Name" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Middle Name</p>
                                    <input placeholder="Middle Name"   name="abstract_author_edit_middle_name" id="abstract_author_middle_name" value="<?= ($rowAbstractDetails['abstract_author_middle_name'] != '') ? ($rowAbstractDetails['abstract_author_middle_name']) : '' ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                    <input required placeholder="Last Name" name="abstract_author_edit_last_name" id="abstract_author_last_name"  validate="Please Enter Last Name" value="<?= ($rowAbstractDetails['abstract_author_last_name'] != '') ? ($rowAbstractDetails['abstract_author_last_name']) : '' ?>">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address <i class="mandatory">*</i></p>
                                    <input name="abstract_author_edit_address" id="abstract_author_edit_address"  validate="Please Enter Your Address" type="text" required  value="<?= $rowAbstractDetails['abstract_author_address'] ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country <i class="mandatory">*</i></p>
                                      <select class=" <?= $disabledclass ?>"
                                        name="abstract_author_edit_country"
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
                                            <option   value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $rowAbstractDetails['abstract_author_country_id']) ? 'selected="selected"' : '' ?>>
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
                                        name="abstract_author_edit_state_id"
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
                                            <option   value="<?= $rowFetchState['st_id'] ?>" <?= ($rowFetchState['st_id'] == $rowAbstractDetails['abstract_author_state_id']) ? 'selected="selected"' : '' ?>>
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
                                    <input  type="text" name="abstract_author_edit_city" id="abstract_author_edit_city<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_city'] ?>">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Pin <i class="mandatory">*</i></p>
                                    <input  type="text" name="abstract_author_edit_pincode" id="abstract_author_pincode<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_pin'] ?>">
                                </div>
                                
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Institute <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                        <input type="text" name="abstract_author_edit_institute_name" id="abstract_author_edit_institute_name<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_institute_name'] ?>" >

                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Department <i class="mandatory">*</i></p>
                                    <div class="d-flex align-items-center">

                                    <input type="text" name="abstract_author_edit_department" id="abstract_author_edit_department<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_department'] ?>" >

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
                         <?php
                            $sqlCoAuthorDtls  = array();
                            $sqlCoAuthorDtls['QUERY']  =  "SELECT * 
                                                            FROM  " . _DB_ABSTRACT_COAUTHOR_ . "
                                                            WHERE `abstract_id` = '" . $rowAbstractDetails['id'] . "' 
                                                                AND `status` = 'A'";
                            $coAuthorDetails  =	$mycms->sql_select($sqlCoAuthorDtls);
                            $coAuthor_counter =	$mycms->sql_numrows($coAuthorDetails);
                            if ($coAuthor_counter > 0) {
                                $ii = 0;
                                foreach ($coAuthorDetails as $keyRow => $rowFetchcoAuthorDetails) {
                                    $coAuthor_counter--;
                                    $ii++;
                            ?>
                             <div class="registration_right_body_content mb-3 registration_right_body_content_author">
                            <div class="frm_grp span_4 coauthor_head">
                                <span class="coauthor_number"><?=$ii?></span>
                                <n>Co-Author</n>
                                <!-- <a href="#" class="guest_action delete_author"><?php delete(); ?></a> -->
                            </div>
                            <div class="form_grid">
                                 <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Email Address </p>
                                    <input type="hidden" name="abstract_coauthor_edit_id[]" id="abstract_coauthor_edit_id<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['id'] ?>" />
                                    <input type="email"  name="abstract_coauthor_edit_email[]"  id="abstract_coauthor_edit_email"  value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_email'] ?>"   validate="Please Enter Email Address" >
                                </div>
                                 <div class="frm_grp span_0 span_2">
                                    <p class="frm-head">Mobile Number</p>
                                    <div class="sub_frm_grp form_grid">
                                        <input type="text" value="+91" name="abstract_coauthor_edit_phone_code[]" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_phone_code'] ?>"  validate="Please Enter Mobile Prefix">
                                        <input class="span_3" validate="Please Enter Mobile Number"    name="abstract_coauthor_edit_phone_no[]" id="abstract_coauthor_edit_phone_no<?= $rowFetchcoAuthorDetails['id'] ?>"  onkeypress="return isNumber(event)"  maxlength="10"  value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_phone_no'] ?>">
                                    </div>
                                </div>
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                                    <select name="abstract_coauthor_edit_title[]"
                                        class="<?= $disabledclass ?>"
                                        <?= $disabled ?>
                                        validate="Please select your title"
                                        style="width:100%; padding:5px;" required>
                                        <option value="">Select Title</option>
                                        <option value="Dr" <?= strtoupper($rowFetchcoAuthorDetails['abstract_coauthor_title']) == 'DR' ? 'selected' : '' ?>>Dr.</option>
                                        <option value="Prof" <?= strtoupper($rowFetchcoAuthorDetails['abstract_coauthor_title']) == 'PROF' ? 'selected' : '' ?>>Prof.</option>
                                        <option value="Mr" <?= strtoupper($rowFetchcoAuthorDetails['abstract_coauthor_title']) == 'MR' ? 'selected' : '' ?>>Mr.</option>
                                        <option value="Ms" <?= strtoupper($rowFetchcoAuthorDetails['abstract_coauthor_title']) == 'MS' ? 'selected' : '' ?>>Ms.</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                    <input placeholder="Enter First Name"  required value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_first_name'] ?>"    name="abstract_coauthor_edit_first_name[]" id="abstract_coauthor_first_name" validate="Please Enter First Name" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Middle Name</p>
                                    <input placeholder="Middle Name"  value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_middle_name'] ?>"  name="abstract_coauthor_edit_middle_name[]" id="abstract_coauthor_middle_name">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                    <input  placeholder="Last Name" required  value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_last_name'] ?>"  name="abstract_coauthor_edit_last_name[]" id="abstract_coauthor_last_name"  validate="Please Enter Last Name">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address</p>
                                    <input name="abstract_coauthor_edit_address[]" id="abstract_coauthor_address"   value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_address'] ?>" validate="Please Enter Your Address" type="text" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country</p>
                                      <select class=" <?= $disabledclass ?>"
                                        name="abstract_coauthor_edit_country[]"
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
                                            <option   value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $rowAbstractDetails['abstract_author_country_id']) ? 'selected="selected"' : '' ?>>
                                                <?= $rowFetchCountry['country_name'] ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">State </p>
                                    <div class="d-flex align-items-center">
                                        <!-- Country icon -->
                                        <!-- <span style="margin-right:5px;">
                                                                <img src="images/country-R.png" alt="" style="width:24px;height:24px;">
                                                            </span> -->

                                        <!-- Country select -->
                                        <select class=" <?= $disabledclass ?>"
                                        name="abstract_coauthor_edit_state[]"
                                        forType="state"
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
                                            <option   value="<?= $rowFetchState['st_id'] ?>" <?= ($rowFetchState['st_id'] == $rowAbstractDetails['abstract_author_state_id']) ? 'selected="selected"' : '' ?>>
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
                                    <p class="frm-head">City </p>
                                     <input type="text" name="abstract_coauthor_edit_city[]" id="abstract_coauthor_edit_city<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_city_name'] ?>" class="form-control frmdec" style="text-transform:uppercase;" />
                                </div>
                                 <div class="frm_grp span_2">
                                    <p class="frm-head">Pin </p>
                                     <input type="text" name="abstract_coauthor_pincode[]" id="abstract_coauthor_pincode<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_pincode'] ?>" class="form-control frmdec" style="text-transform:uppercase;" />
                                </div>
                                
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Institute </p>
                                    <div class="d-flex align-items-center">

                                           	<input type="text" name="abstract_coauthor_institute_edit_name[]" id="abstract_coauthor_institute_edit_name<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_institute_name'] ?>" class="form-control frmdec" style="text-transform:uppercase;" autocomplete="off" />

                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Department </p>
                                    <div class="d-flex align-items-center">

                                           	<input type="text" name="abstract_coauthor_edit_department[]" id="abstract_coauthor_edit_department<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_department'] ?>" class="form-control frmdec" style="text-transform:uppercase;" autocomplete="off" />

                                    </div>
                                </div>
                            </div>
                            
                        </div>
                            <? } } ?>
                           
                            <button  id="coauthor_blank_section" class="add_guest coauthor_blank text-center"><?php add(); ?> Add Co-Author</button>

                        <!-- <div class="coauthor_blank text-center" > -->
                            <!-- <div class="coauthor_blank text-center">
                                <span><i class="fal fa-tv-alt"></i></span>
                                <h6>No Co-Authors Added</h6>
                                <p>If this is a solo submission, you can skip this step.</p> -->
                                <!-- <button  id="coauthor_blank_section" class="add_guest coauthor_blank text-center"><?php add(); ?> Add Co-Author</button> -->
                            <!-- </div> -->
                        <!-- </div> -->
                        <div class="registration_right_body_content" id="coauthor_form_section" style="display:none;">
                            <div class="coauthor_wrap">
                               
                            </div>
                            <button class="add_guest"><?php add(); ?> Add Co-Author</button>
                        </div>
                    </div>
                    <div class="registration_right_bottom justify-content-end">
                        <!-- <button type="button" name="previous" class="previous action-button-previous"><i class="fal fa-angle-left"></i>Previous</button> -->
                        <button type="button" name="next" class="next action-button">Continue<i class="fal fa-angle-right"></i></button>
                    </div>
                </fieldset>
                <!-- path -->               
                <!-- accommodation -->
                <fieldset class="registration_right_wrap"  id="fs_abstract">
                    <div class="registration_right_head">
                        Abstract Content
                    </div>
                    <div class="registration_right_body">
                        <!-- <div class="registration_right_body_head">
                            <div class="registration_right_body_head_left">
                                <h4>Abstract Content</h4>
                                <h5>Enter the scientific details of your abstract.</h5>
                            </div>
                        </div> -->
                        <div class="registration_right_body_content">
                                  
                            <div class="form_grid" id="fields_area">
                                   <?php
                                $sqlAbstractTopic    = array();
                                $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                            WHERE `status` = 'A' 
                                                            AND `category` = '".$rowAbstractDetails['abstract_cat']."'
                                                            ORDER BY `abstract_topic` ASC";

                                $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
                                if ($resultAbstractTopic) {
                                    ?>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Topic</p>
                                    <select  name="abstract_edit_topic_id" id="abstract_edit_topic_id<?= $rowAbstractDetails['abstract_submition_code'] ?>">
                                       <option value="">-- Select Topic --</option>
                                        <?php
                                        
                                            foreach ($resultAbstractTopic as $keyAbstractTopic => $rowAbstractTopic) {
                                        ?>
                                                <option value="<?= $rowAbstractTopic['id'] ?>" <?= ($rowAbstractTopic['id'] == $rowAbstractDetails['abstract_topic_id']) ? 'selected="selected"' : '' ?>><?= $rowAbstractTopic['abstract_topic'] ?></option>
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
                                    <input  name="abstract_edit_title" value="<?= $rowAbstractDetails['abstract_title'] ?>" id="abstract_title" spreadInGroup="abstractTitle" displayText="abstract_title_word_count" style="text-transform:uppercase;" required validate="Please enter the abstract title">
                                </div>
                                <?php if ($rowAbstractDetails['abstract_cat'] != '4') { ?>
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
                                    $category_id = $rowAbstractDetails['abstract_cat']?? null;
                                    $sub_category_id =$rowAbstractDetails['abstract_parent_type'] ?? null;
                                    $sub_subcategory_Id = $rowAbstractDetails['abstract_child_type'] ?? null;

                                    $category_fields = [];

                                    /* ================= CATEGORY ================= */
                                    if($category_id){

                                        $sqlCategory1 = [
                                            'QUERY' => "SELECT category_fields 
                                                        FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
                                                        WHERE id = ?",
                                            'PARAM' => [
                                                ['FILD'=>'id','DATA'=>$category_id,'TYP'=>'i']
                                            ]
                                        ];

                                        $resultCategory1 = $mycms->sql_select($sqlCategory1);
                                      
                                        if($resultCategory1){
                                            $category_fields = cleanFields($resultCategory1[0]['category_fields']);
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
                                    $field_ids = implode(",", $category_fields);

                                    // ABSTRACT FIELDS
                                    $sqlAbstractFields = [
                                        'QUERY' => "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " WHERE `status` = ?   ORDER BY FIELD(id, $field_ids)",
                                        'PARAM' => [
                                            ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's']
                                        ]
                                    ];
                                    $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                    $totalWordCount = 0;

                                    foreach ($resultAbstractFields as $value) {
                                        $fieldValue = $rowAbstractDetails[$value['field_key']] ?? '';
                                        if ($fieldValue != '' && $fieldValue != NULL && $fieldValue != 'NULL') {
                                    ?>
                                    <div class="frm_grp span_0 span_4">
                                        <p class="frm-head"><?= $value['display_name'] ?></p>
                                        <textarea required
                                                name="abstract_edit_<?= $value['field_key'] ?>"
                                                id="fieldVal_<?= $value['id'] ?>"
                                                checkFor="wordCount"
                                                spreadInGroup="abstractContent"
                                                displayText="abstract_body_count"
                                                word_type="<?= htmlspecialchars($cfg['ABSTRACT.TOTAL.WORD.TYPE'], ENT_QUOTES) ?>"
                                                validate="<?= $msg ?>"
                                                title="<?= $value['display_name'] ?>"><?= $fieldValue ?></textarea>
                                    </div>
                                    <?php } } ?>
                                    <? if($fieldValue != '' && $fieldValue != NULL && $fieldValue != 'NULL'  && ($value['display_name']=='Link Of The Video' || $value['display_name']=='Link of the video')){
                                            ?>
                                            <div class="frm_grp span_4" >
                                              <span style="" class="frm-head text-left">*Keep the given link accessible till you get a notification from the Secretariat.</span><br>
                                              <span style="color: #ff402a;" class="frm-head text-left">*Note: Video length must be of max 8 mins.</span>
                                            </div>
                                            <?
                                        }  
                                        ?>
                                    <?php if ($rowAbstractDetails['abstract_cat'] != '4') { ?>

                                    <div class="frm_grp span_4">
                                        <p style="color: #f3d178;" class="frm-head text-right">
                                            Total: <n id="abstract_body_count">0</n> / <?= $cfg['ABSTRACT.FREE_PAPER_SESSION_WORD_LIMIT'] ?> <?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>
                                        </p>
                                    </div>
                                    <?php } ?>
                                    <?php
                                    // CATEGORY FILE
                                    $sqlCategory = [
                                        'QUERY' => "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " WHERE id = ?",
                                        'PARAM' => [
                                            ['FILD' => 'id', 'DATA' => $rowAbstractDetails['abstract_cat'], 'TYP' => 'i']
                                        ]
                                    ];
                                    $resultCategory = $mycms->sql_select($sqlCategory);
                                    $category = $resultCategory[0] ?? null;

                                    if ($category && $category['doc_upload'] == 'yes' && $category['suporting_document_type'] !== 'null'):
                                        $existingCategoryFile = $rowAbstractDetails['category_required_file'] ?? '';
                                        $categoryTypes = json_decode($category['suporting_document_type']);
                                    ?>
                                    <div class="frm_grp span_4 upload-section-cat" id="upload_section-cat_<?= $category['id'] ?>">
                                        <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                        <div class="file-up-dtls">
                                            <h8><?= $category['suporting_document_name'] ?></h8>

                                            <div class="existing-file-wrapper" <?= $existingCategoryFile == '' ? 'style="display:none;"' : '' ?>>
                                                <p>
                                                    View File: <a href="<?= _BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].$existingCategoryFile ?>" target="_blank"><?= $existingCategoryFile ?></a>
                                                    <button type="button" class="upload_delet delete-file-btn" data-target="category"><i class="fal fa-trash-alt"></i></button>
                                                </p>
                                                <input type="hidden" name="original_category_file_name" value="<?= $existingCategoryFile ?>" />
                                                <input type="hidden" name="delete_category_file" value="0" />
                                            </div>

                                            <input class="form-control category-file" 
                                                type="file"  
                                                id="formFileAbstractCat"  
                                                name="category_required_file" 
                                                data-types="<?= implode(',', array_filter($categoryTypes)) ?>"  
                                               
                                                <?= $existingCategoryFile != '' ? 'style="display:none;"' : 'required' ?> />
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php
                                    // HOD CONSENT FILE
                                    if ($hod_consent_file_types !== 'null'):
                                        $existingHodFile = $rowAbstractDetails['abstract_consent_file'] ?? '';
                                        $hodTypes = json_decode($hod_consent_file_types);
                                    ?>
                                    <div class="frm_grp span_4">
                                        <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                        <input type="hidden" name="sessionId" id="sessionId" value="<?= session_id() ?>" />
                                        <div class="file-up-dtls">
                                            <h8>HOD Consent</h8>

                                            <div class="existing-file-wrapper" <?= $existingHodFile == '' ? 'style="display:none;"' : '' ?>>
                                                <p>
                                                    View File: <a href="<?= _BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].$existingHodFile ?>" target="_blank"><?= $existingHodFile ?></a>
                                                    <button type="button" class="upload_delet delete-file-btn" data-target="hod"><i class="fal fa-trash-alt"></i></button>
                                                </p>
                                                <input type="hidden" name="original_consent_file_name" value="<?= $existingHodFile ?>" />
                                                <input type="hidden" name="delete_hod_file" value="0" />
                                            </div>

                                            <input class="form-control consent-file" 
                                                type="file" 
                                                id="formFileHod"  
                                                name="upload_consent_abstract_file"
                                                data-types="<?= implode(',', array_filter($hodTypes)) ?>"  
                                               
                                                <?= $existingHodFile != '' ? 'style="display:none;"' : 'required' ?> />
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php
                                    // ABSTRACT FILE
                                    if ($abstract_file_types !== 'null'  && $category_id !='3'  && $category_id !='4'):
                                        $existingAbstractFile = $rowAbstractDetails['abstract_file'] ?? '';
                                        $abstractTypes = json_decode($abstract_file_types);
                                    ?>
                                    <div class="frm_grp span_4">
                                        <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                        <div class="file-up-dtls">
                                            <h8>Abstract File</h8>

                                            <div class="existing-file-wrapper" <?= $existingAbstractFile == '' ? 'style="display:none;"' : '' ?>>
                                                <p>
                                                    View File: <a href="<?= _BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].$existingAbstractFile ?>" target="_blank"><?= $existingAbstractFile ?></a>
                                                    <button type="button" class="upload_delet delete-file-btn" data-target="abstract"><i class="fal fa-trash-alt"></i></button>
                                                </p>
                                                <input type="hidden" name="original_abstract_file_name" value="<?= $existingAbstractFile ?>" />
                                                <input type="hidden" name="delete_abstract_file" value="0" />
                                            </div>

                                            <input class="form-control abstract-file" 
                                                type="file" 
                                                id="formFileAbstract"  
                                                name="upload_abstract_file" 
                                                data-types="<?= implode(',', array_filter($abstractTypes)) ?>"  
                                                
                                                <?= $existingAbstractFile != '' ? 'style="display:none;"' : 'required' ?> />
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php
                                    // NOMINATION FILES
                                    $sqlAbstractSubcat = [
                                        'QUERY' => "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
                                                    WHERE `status` = ? AND `related_category_id` = ? 
                                                    ORDER BY `id` ASC",
                                        'PARAM' => [
                                            ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'],
                                            ['FILD' => 'related_category_id', 'DATA' => $rowAbstractDetails['abstract_cat'], 'TYP' => 's']
                                        ]
                                    ];
                                    $resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);

                                    $sqlAbstractFile = [
                                        'QUERY' => "SELECT * FROM " . _DB_AWARD_REQUEST_ . " 
                                                    WHERE `status` = ? AND `submission_id` = ? 
                                                    ORDER BY `id` ASC",
                                        'PARAM' => [
                                            ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'],
                                            ['FILD' => 'submission_id', 'DATA' => $rowAbstractDetails['id'], 'TYP' => 's']
                                        ]
                                    ];
                                    $resultAbstractFile = $mycms->sql_select($sqlAbstractFile);                                 
                                    if ($rowAbstractDetails['abstract_cat'] == 1 && $researchCatCount >= 2) {
                                                       
                                        // Check if there is already a file for this category
                                        if (empty($resultAbstractFile)) {
                                            $blockCategory = false;
                                        }
                                    }
                                    if ($resultAbstractSubcat):
                                        foreach ($resultAbstractSubcat as $rowAbstractTopic):
                                            $existingNominationFile = $resultAbstractFile[0]['upload_nomination_file'] ?? '';
                                            $nominationTypes = json_decode($rowAbstractTopic['suporting_document_type']);
                                    ?>
                                    <div class="frm_grp span_4">
                                        <label class="custom-radio" for="nomination_name_<?= $rowAbstractTopic['id'] ?>">
                                            <input class="toggleswitch-checkbox award-toggle"  
                                                data-id="<?= $rowAbstractTopic['id'] ?>" 
                                                type="checkbox" 
                                                <?php if ($resultAbstractFile != '') echo 'checked'; ?>
                                                value="<?= $rowAbstractTopic['id'] ?>" 
                                                id="nomination_name_<?= $rowAbstractTopic['id'] ?>" 
                                                name="award_request"  data-block="<?= $blockCategory ? '1' : '0' ?>">
                                            <div class="toggleswitch-switch"></div> <?= "I want to opt for " . $rowAbstractTopic['award_name'] ?><span class="checkmark"></span>
                                            <br><br><span><i><?= $rowAbstractTopic['award_description'] ?></i></span>
                                        </label>
                                    </div>

                                    <?php if ($rowAbstractTopic['doc_upload'] == 'yes' && $rowAbstractTopic['suporting_document_type'] !== 'null'): ?>
                                    <div class="frm_grp span_4 upload-section" id="upload_section_<?= $rowAbstractTopic['id'] ?>" >
                                        <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                                        <div class="file-up-dtls">
                                            <h8><?= $rowAbstractTopic['suporting_document_name'] ?></h8>

                                            <div class="existing-file-wrapper" <?= $existingNominationFile == '' ? 'style="display:none;"' : '' ?>>
                                                <p>
                                                    View File: <a href="<?= _BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].$existingNominationFile ?>" target="_blank"><?= $existingNominationFile ?></a>
                                                    <button type="button" class="upload_delet delete-file-btn" data-target="nomination_<?= $rowAbstractTopic['id'] ?>"><i class="fal fa-trash-alt"></i></button>
                                                </p>
                                                <input type="hidden" name="original_nomination_file_name" value="<?= $existingNominationFile ?>" />
                                                <input type="hidden" name="delete_nomination_file" value="0" />
                                            </div>

                                            <input class="form-control nomination-file"
                                                type="file"
                                                id="formFileAbstract_<?= $rowAbstractTopic['id'] ?>"
                                                name="upload_nomination_file"
                                                data-types="<?= implode(',', array_filter($nominationTypes)) ?>"
                                                
                                                <?= $existingNominationFile != '' ? 'style="display:none;"' : '' ?> />
                                        </div>
                                    </div>
                                    <?php endif; endforeach; endif; ?>
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
                                <!-- <h4>Review Submission</h4> -->
                                <h5>Please review all details before final submission.</h5>
                            </div>
                        </div>

                        <div class="registration_right_body_content">
                            <div class="abstract_review_grid">
                        
                                <li>
                                    <h4>
                                        <n><?php duser(); ?>Auhtor Details</n><a href="#" class="editStep" data-target="fs_author">Edit</a>
                                    </h4>
                                    <div class="form_grid">
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Email Address</p>
                                            <h6 data-review="abstract_author_edit_email"></h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Mobile Number</p>
                                            <h6>
                                                 <span data-review="abstract_author_edit_phone_code"></span>
                                                <span data-review="abstract_author_edit_phone_no"></span>
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Name</p>
                                            <h6>
                                                <span data-review="abstract_author_edit_title"></span>
                                                <span data-review="abstract_author_edit_first_name"></span>
                                                <span data-review="abstract_author_edit_middle_name"></span>
                                                <span data-review="abstract_author_edit_last_name"></span>
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Address</p>
                                            <h6>
                                                 <span data-review="abstract_author_edit_address"></span>
                                        
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Country</p>
                                            <h6>
                                                 <span data-review="abstract_author_edit_country"></span>
                                             
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">State</p>
                                            <h6>
                                                <span data-review="abstract_author_edit_state_id"></span>
                                               
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">City</p>
                                            <h6>
                                              
                                                <span data-review="abstract_author_edit_city"></span>
                                            </h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Pin</p>
                                            <h6>
                                            
                                                <span data-review="abstract_author_edit_pincode"></span>
                                            </h6>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Institute</p>
                                            <h6 data-review="abstract_author_edit_institute_name"></h6>
                                        </div>
                                         <div class="frm_grp span_2">
                                            <p class="frm-head">Department</p>
                                            <h6 data-review="abstract_author_edit_department"></h6>
                                        </div>
                                    </div>
                                </li>
                                    <div class="coauthor_review_wrap">
                                        <!-- Co-author <li> items will be appended here -->
                                    </div>
                               <li>
                                    <h4>
                                        <n><i class="fal fa-tag"></i>Submission Category</n>
                                    </h4>
                                    <div class="form_grid">
                                        <? if($rowAbstractDetails['abstract_cat']!=""){ ?>
                                        <div class="frm_grp span_0 span_2">
                                            <p class="frm-head">Category</p>
                                            <h6><?=getCategoryName($rowAbstractDetails['abstract_cat'])?></h6>
                                        </div>
                                        <? } ?>
                                        <? if($rowAbstractDetails['abstract_parent_type']!=""){
                                            $sqlAbstractTopicSub			  =	array();
                                            $sqlAbstractTopicSub['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                                                                                WHERE `status` ='A'
                                                                            AND id='" . $rowAbstractDetails['abstract_parent_type'] . "'";


                                            $resultAbstractTopicSub = $mycms->sql_select($sqlAbstractTopicSub);
                                        ?>
                                        
                                        <div class="frm_grp span_0 span_2">
                                            <p class="frm-head">Sub Category 1</p>
                                            <h6><?=$resultAbstractTopicSub[0]['abstract_submission']?></h6>
                                        </div>
                                        <? } ?>
                                        <? if($rowAbstractDetails['abstract_child_type']!=""){
                                            $sqlAbstractPresentation             =    array();
                                            $sqlAbstractPresentation['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_PRESENTATION_ . " 
                                                                        WHERE `status` ='A'
                                                                    AND id='" . $rowAbstractDetails['abstract_child_type'] . "'";


                                            $resultAbstractPresentation = $mycms->sql_select($sqlAbstractPresentation);
                                            ?>
                                        <div class="frm_grp span_0 span_2">
                                            <p class="frm-head">Sub Category 2</p>
                                            <h6><?=$resultAbstractPresentation[0]['abstract_presentation']?></h6>
                                        </div>
                                        <? } ?>
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
                                            <h6 data-review="abstract_edit_topic_id"></h6>
                                        </div>
                                          <? } ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Title</p>
                                            <h6 data-review="abstract_edit_title"></h6>
                                        </div>
                                        <?php

                                          
                                        // echo '<pre>'; print_r($resultAbstractFields);
                                      foreach ($resultAbstractFields as $key => $value) {
									if ($rowAbstractDetails[$value['field_key']] != '' && $rowAbstractDetails[$value['field_key']] != NULL && $rowAbstractDetails[$value['field_key']] != 'NULL') {

                                        ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head"><?=$value['display_name']?></p>
                                            <h6 data-review="abstract_edit_<?= $value['field_key'] ?>"></h6>
                                        </div>
                                        <? } } ?>
                                         <!-- CATEGORY File -->
                                        <div class="frm_grp span_4" id="category_abstract_wrapper" style="display:none;">
                                            <p class="frm-head">Category File</p>
                                            <h6 id="review_category_file"><a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$rowAbstractDetails['category_required_file']?>" target="_blank">
                                                    View File (<?=$rowAbstractDetails['category_required_file']?>)
                                                </a></h6>
                                        </div>
                                        <!-- Abstract File -->
                                        <div class="frm_grp span_4" id="review_abstract_wrapper" style="display:none;">
                                            <p class="frm-head">Abstract File</p>
                                            <h6 id="review_abstract_file"><a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$rowAbstractDetails['abstract_file']?>" target="_blank">
                                                    View File (<?=$rowAbstractDetails['abstract_file']?>)
                                                </a>
                                            </h6>
                                        </div>

                                        <!-- HOD Consent -->
                                        <div class="frm_grp span_4" id="review_hod_wrapper" style="display:none;">
                                            <p class="frm-head">HOD Consent File</p>
                                            <h6 id="review_hod_file"><a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$rowAbstractDetails['abstract_consent_file']?>" target="_blank">
                                                    View File (<?=$rowAbstractDetails['abstract_consent_file']?>)
                                                </a>
                                        </h6>
                                        </div>
                                  
                                         <?php
									$sqlAbstractSubcat    = array();
									$sqlAbstractSubcat['QUERY']    = "SELECT * 
																		  FROM " . _DB_AWARD_MASTER_ . " 
																		 WHERE `status` = ? 
                                                                         AND `related_category_id` = ?
																	  ORDER BY `id` ASC";

									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'related_category_id',  'DATA' =>$rowAbstractDetails['abstract_cat'],  'TYP' => 's');

									$resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);

                                    $sqlAbstractFile    = array();
									$sqlAbstractFile['QUERY']    = "SELECT * 
																		  FROM " . _DB_AWARD_REQUEST_ . " 
																		 WHERE `status` = ? 
                                                                         AND `submission_id` = ?
																	  ORDER BY `id` ASC";

									$sqlAbstractFile['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
									$sqlAbstractFile['PARAM'][]   = array('FILD' => 'submission_id',  'DATA' =>$rowAbstractDetails['id'],  'TYP' => 's');

									$resultAbstractFile = $mycms->sql_select($sqlAbstractFile);

                                    ?>
                                    <div class="frm_grp span_4">
                                        <div class="frm_grp span_4">
                                            <!-- Nomination Files -->
                                            <div id="review_nomination_files">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Award Description</p>
                                                <h6><?=$resultAbstractSubcat[0]['award_description']?></h6>
                                            </div>

                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Nomination File</p>
                                                <h6>
                                                    <a href="<?=$cfg['FILES.ABSTRACT.REQUEST']?><?=$resultAbstractFile[0]['upload_nomination_file']?>" target="_blank">
                                                        View File (<?=$resultAbstractFile[0]['upload_nomination_file']?>)
                                                    </a>
                                                </h6>
                                            </div>
                                            </div>
                                        </div>
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
<? } } ?>
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
       let reviewCounter = 1; // counter for only valid co-authors

        // Include both old PHP-rendered co-authors and new dynamically added ones
        $(".registration_right_body_content_author, .coauthor_wrap .coauthor_item").each(function(index) {
            var coauthor = $(this);
            var nameVal = coauthor.find("[name='abstract_coauthor_edit_first_name[]']").val();

            if (!nameVal || nameVal.trim() === "") return;

            var reviewItem = `<li class="coauthor_review_item">
                <h4>
                    <n><?php duser(); ?> Co-Author ${reviewCounter} Details</n>
                    <a href="#" class="editStep" data-target="fs_author">Edit</a>
                </h4>
                <div class="form_grid">
                    <div class="frm_grp span_2"><p class="frm-head">Email Address</p><h6>${coauthor.find("[name='abstract_coauthor_edit_email[]']").val() || '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Mobile Number</p><h6>${coauthor.find("[name='abstract_coauthor_edit_phone_code[]']").val() || '-'}${coauthor.find("[name='abstract_coauthor_edit_phone_no[]']").val() || '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Name</p><h6>${coauthor.find("[name='abstract_coauthor_edit_title[]']").val()} ${coauthor.find("[name='abstract_coauthor_edit_first_name[]']").val()} ${coauthor.find("[name='abstract_coauthor_edit_middle_name[]']").val()} ${coauthor.find("[name='abstract_coauthor_edit_last_name[]']").val()}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Address</p><h6>
                         ${coauthor.find("[name='abstract_coauthor_edit_address[]']").val()|| '-'}
                      
                    </h6></div>

                     <div class="frm_grp span_2">
                        <p class="frm-head">Country</p>
                        <h6>${coauthor.find("[name='abstract_coauthor_edit_country[]']").val() ? coauthor.find("[name='abstract_coauthor_edit_country[]'] option:selected").text() : '-'}</h6>
                    </div>

                    <div class="frm_grp span_2">
                        <p class="frm-head">State</p>
                        <h6>${coauthor.find("[name='abstract_coauthor_edit_state[]']").val() ? coauthor.find("[name='abstract_coauthor_edit_state[]'] option:selected").text() : '-'}</h6>
                    </div>

                     <div class="frm_grp span_2"><p class="frm-head">City</p><h6>
                        
                        ${coauthor.find("[name='abstract_coauthor_edit_city[]']").val() || '-'}
                    </h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Pin</p><h6>
                
                        ${coauthor.find("[name='abstract_coauthor_pincode[]']").val() || '-'}
                    </h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Institute</p><h6>${coauthor.find("[name='abstract_coauthor_institute_edit_name[]']").val() || '-'}</h6></div>
                    <div class="frm_grp span_2"><p class="frm-head">Department</p><h6>${coauthor.find("[name='abstract_coauthor_edit_department[]']").val() || '-'}</h6></div>
                </div>
            </li>`;

            $(".coauthor_review_wrap").append(reviewItem);
            reviewCounter++; // increment only for valid co-authors
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
                    value = $("[name='"+field+"']:checked").next().text() || "-";
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
    function updateWordCount(group, type, limit, displayId) {
        var total = 0;
        var textareas = $("textarea[spreadInGroup='" + group + "']");

        textareas.each(function() {
            var val = $(this).val().trim();
            if (type === "character") {
                total += val.length;
            } else if (type === "word") {
                if (val !== "") {
                    total += val.split(/\s+/).length;
                }
            }
        });

        // Trim last textarea only if over limit
        if (total > limit) {
            var excess = total - limit;
            var lastTextarea = textareas.last();
            if (type === "character") {
                lastTextarea.val(lastTextarea.val().substring(0, lastTextarea.val().length - excess));
            } else if (type === "word") {
                var words = lastTextarea.val().trim().split(/\s+/);
              
                // Remove only the excess words
                words = words.slice(0, words.length - excess);
                lastTextarea.val(words.join(" "));
            }
            total = limit;
            alert("Total limit of " + limit + " " + type + "s exceeded!");
        }

        // Update display
        $("#" + displayId).text(total);
    }

    function updateTitleCount(textareaId, displayId, type, limit) {
        var val = $(textareaId).val().trim();
        var count = 0;

        if(type === 'character') {
            count = val.length;
            if(count > limit){
                $(textareaId).val(val.substring(0, limit));
                count = limit;
                alert("Maximum " + limit + " characters allowed!");
            }
        } else if(type === 'word') {
            var words = val.split(/\s+/).filter(Boolean);
            count = words.length;
            if(count > limit){
                $(textareaId).val(words.slice(0, limit).join(" "));
                count = limit;
                alert("Maximum " + limit + " words allowed!");
            }
        }

        $(displayId).text(count);
    }

    function initializeabstract() {
           // Word / character limit for Abstract Title
        $(document).ready(function(){
            var type = '<?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>'; // character / word
            var limit = <?= intval($cfg['ABSTRACT.TITLE.WORD.LIMIT']) ?>;
            if (currentAbstractCategoryId === '4') {
                // No limit for category 4 — skip enforcement entirely
                return;
            }

            // Prefill count on page load
            updateTitleCount("#abstract_title", "#abstract_title_count", type, limit);

            // Update count on input
            $("#abstract_title").off("input").on("input", function(){
                updateTitleCount("#abstract_title", "#abstract_title_count", type, limit);
            });
        });
        $(document).ready(function() {
            var overLimitAlerted = {}; // track alert per group
            if (currentAbstractCategoryId === '4') {
                // No limit for category 4 — skip enforcement entirely
                return;
            }

            function updateBodyCount() {
                $("textarea[checkFor='wordCount']").each(function() {
                    var type = ($(this).attr("word_type") || "word").trim().toLowerCase();
                    var limit = <?= intval($cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']) ?>;
                    var group = $(this).attr("spreadInGroup");

                    var total = 0;

                    $("textarea[spreadInGroup='" + group + "']").each(function() {
                        var val = $(this).val().trim();
                        if (type === "character") {
                            total += val.length;
                        } else if (type === "word") {
                            if (val !== "") total += val.split(/\s+/).length;
                        }
                    });

                    // Update display
                    $("#abstract_body_count").text(total);

                    // Initialize alert flag for this group if undefined
                    if (overLimitAlerted[group] === undefined) overLimitAlerted[group] = false;

                    // Show alert only once per group when limit is first crossed
                    if (total > limit && !overLimitAlerted[group]) {
                        alert("Total limit of " + limit + " " + type + "s exceeded!");
                        overLimitAlerted[group] = true;
                    }

                    // Reset alert flag when user goes back under the limit
                    if (total <= limit && overLimitAlerted[group]) {
                        overLimitAlerted[group] = false;
                    }
                });
            }

            // Show count on load
            updateBodyCount();

            // Update count on input
            $("textarea[checkFor='wordCount']").off("input").on("input", function() {
                updateBodyCount();
            });
        });

        // Re-run populateReview if using review section
        populateReview();
        
    }
   
    document.addEventListener("DOMContentLoaded", initializeabstract);
    function checkAbstractLimits() {

        // No limit for category 4 — always allow continuing
        if (currentAbstractCategoryId === '4') {
            return true;
        }

        var $bodyTextareas = $("textarea[checkFor='wordCount']");

        if ($bodyTextareas.length === 0) {
            return true;
        }

        var bodyLimit = <?= intval($cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']) ?>;
        var bodyType = "<?= addslashes($cfg['ABSTRACT.TOTAL.WORD.TYPE']) ?>";
        var total = 0;

        $("textarea[spreadInGroup='abstractContent']").each(function() {
            var type = ($(this).attr("word_type") || bodyType).trim().toLowerCase();
            var val = $(this).val().trim();

            if (type === "character") {
                total += val.length;
            } else if (type === "word") {
                if (val !== "") {
                    total += val.split(/\s+/).length;
                }
            }
        });

        if ($("#abstract_body_count").length) {
            $("#abstract_body_count").text(total);
            $("#abstract_body_count").css("color", total > bodyLimit ? "red" : "#f3d178");
        }

        if (total > bodyLimit) {
            toastr.error("Abstract Body exceeds the total limit of " + bodyLimit + " " + bodyType + "(s). You cannot continue.", "Error", {
                progressBar: true,
                timeOut: 5000,
                showMethod: "slideDown",
                hideMethod: "slideUp",
                direction: "ltr"
            });

            if ($("#abstract_body_count").length) {
                $('html, body').animate({
                    scrollTop: $("#abstract_body_count").offset().top - 100
                }, 500);
            }

            return false;
        }

        return true;
    }
function updateFileReview() {
    // CATEGORY FILE
    let categoryInput = document.getElementById('formFileAbstractCat');
    let categoryWrapper = $('#category_abstract_wrapper');
    let categoryLink = $('#review_category_file');

    if (categoryInput && categoryInput.files.length > 0) {
        let file = categoryInput.files[0];
        let url = URL.createObjectURL(file);

        categoryWrapper.show();
        categoryLink.html(`<a href="${url}" target="_blank">View File (${file.name})</a>`);
    } else {
        // Show existing file if available
        let existingCategoryFile = "<?= $rowAbstractDetails['category_required_file'] ?? '' ?>";
        if (existingCategoryFile !== '') {
            categoryWrapper.show();
            categoryLink.html(`<a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] ?>${existingCategoryFile}" target="_blank">View File (${existingCategoryFile})</a>`);
        } else {
            categoryWrapper.hide();
        }
    }

    // ABSTRACT FILE
    let abstractInput = document.getElementById('formFileAbstract');
    let abstractWrapper = $('#review_abstract_wrapper');
    let abstractLink = $('#review_abstract_file');

    if (abstractInput && abstractInput.files.length > 0) {
        let file = abstractInput.files[0];
        let url = URL.createObjectURL(file);

        abstractWrapper.show();
        abstractLink.html(`<a href="${url}" target="_blank">View File (${file.name})</a>`);
    } else {
        let existingAbstractFile = "<?= $rowAbstractDetails['abstract_file'] ?? '' ?>";
        if (existingAbstractFile !== '') {
            abstractWrapper.show();
            abstractLink.html(`<a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] ?>${existingAbstractFile}" target="_blank">View File (${existingAbstractFile})</a>`);
        } else {
            abstractWrapper.hide();
        }
    }

    // HOD CONSENT FILE
    let hodInput = document.getElementById('formFileHod');
    let hodWrapper = $('#review_hod_wrapper');
    let hodLink = $('#review_hod_file');

    if (hodInput && hodInput.files.length > 0) {
        let file = hodInput.files[0];
        let url = URL.createObjectURL(file);

        hodWrapper.show();
        hodLink.html(`<a href="${url}" target="_blank">View File (${file.name})</a>`);
    } else {
        let existingHodFile = "<?= $rowAbstractDetails['abstract_consent_file'] ?? '' ?>";
        if (existingHodFile !== '') {
            hodWrapper.show();
            hodLink.html(`<a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] ?>${existingHodFile}" target="_blank">View File (${existingHodFile})</a>`);
        } else {
            hodWrapper.hide();
        }
    }

    // NOMINATION FILES
    let html = '';
    $('.award-toggle:checked').each(function () {
        let id = $(this).data('id');
        let fileInput = document.getElementById('formFileAbstract_' + id);

        let awardText = $(this).closest('label').text().trim();
        let fileHtml = '';

        if (fileInput && fileInput.files.length > 0) {
            let file = fileInput.files[0];
            let url = URL.createObjectURL(file);
            fileHtml = `<a href="${url}" target="_blank">View File (${file.name})</a>`;
        } else {
            // Show existing file if available
            let existingFile = "<?= $resultAbstractFile[0]['upload_nomination_file'] ?? '' ?>";
            if (existingFile !== '') {
                fileHtml = `<a href="<?= $cfg['FILES.ABSTRACT.REQUEST'] ?>${existingFile}" target="_blank">View File (${existingFile})</a>`;
            }
        }

        if (fileHtml !== '') {
            html += `
                <div class="frm_grp span_4">
                    <p class="frm-head">Award Description</p>
                    <h6>${awardText}</h6>
                </div>
                <div class="frm_grp span_4">
                    <p class="frm-head">Nomination File</p>
                    <h6>${fileHtml}</h6>
                </div>
            `;
        }
    });

    $('#review_nomination_files').html(html);
}
    $(document).ready(function() {

        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        setProgressBar(current);

     $(".next").click(function() {
    current_fs = $(this).parent().parent();
    next_fs = $(this).parent().parent().next();
    var isValid = true;
    
    // ✅ Check abstract body limits first
    if (!checkAbstractLimits()) {
        return false;
    }
    
    populateReview();
    populateCoAuthorsReview();
    updateFileReview();
    current_fs.find("input, select, textarea").each(function() {
        // Skip body textareas from required validation
        if ($(this).attr("checkFor") === "wordCount") {
            return true;
        }
        
        if ($(this).prop("required") && $(this).val().trim() === "") {
            var msg = $(this).attr("validate") || "This field is required";
            
            toastr.error(msg, "Error", {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp",
                direction: "ltr"
            });
            
            $(this).focus();
            isValid = false;
            return false; // ⛔ stop `.each()` loop
        }
    });
    
    if (current_fs.hasClass("categoryfieldset")) {
        // check if any category is selected
        if (!current_fs.find("input[name='abstract_category']:checked").length) {
            toastr.error('Please select a category', 'Error', {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp"
            });
            isValid = false;
        }
        
        // SUB CATEGORY validation
        var subCategoryBox = current_fs.find(".subcategory_box:visible");
        if (subCategoryBox.length > 0 && !subCategoryBox.find("input[name='abstract_parent_type']:checked").length) {
            toastr.error('Please select a Sub Category', 'Error', {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp"
            });
            isValid = false;
        }
        
        var subSubCategoryBox = current_fs.find(".subsubcategory_box:visible");
        if (subSubCategoryBox.length > 0 && !subSubCategoryBox.find("input[name='abstract_child_type']:checked").length) {
            toastr.error('Please select a Sub Subcategory', 'Error', {
                progressBar: true,
                timeOut: 3000,
                showMethod: "slideDown",
                hideMethod: "slideUp"
            });
            isValid = false;
        }
    }
    
    if (!isValid) {
        return false; // ⛔ stop going to next step
    }
    
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

        // $(".submit").click(function() {
        //     return false;
        // })

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
            $('#abstract_author_state').val(presenter_state);
        },300);

        $('#isPresenter').val('Y');

    } else {

        $('#abstract_author_email').val('');
        $('#abstract_author_mobile').val('');
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
                    <p class="frm-head">Email Address </p>
                    <input type="hidden" name="abstract_coauthor_edit_id[]"  value="" />
                    <input type="email"  name="abstract_coauthor_edit_email[]"    validate="Please Enter Email Address" >
                </div>
                 <div class="frm_grp span_0 span_2">
                    <p class="frm-head">Mobile Number </p>
                    <div class="sub_frm_grp form_grid">
                        <input type="text" value="+91" name="abstract_coauthor_edit_phone_code[]" value=""  validate="Please Enter Mobile Prefix">
                        <input class="span_3" validate="Please Enter Mobile Number"    name="abstract_coauthor_edit_phone_no[]" onkeypress="return isNumber(event)"  maxlength="10"  value="">
                    </div>
                </div>
                <div class="frm_grp span_1">
                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                    <select name="abstract_coauthor_edit_title[]"
                        class=""
                        validate="Please select your title"
                        style="width:100%; padding:5px;" required>
                        <option value="">Select Title</option>
                        <option value="Dr" selected>Dr.</option>
                        <option value="Prof" >Prof.</option>
                        <option value="Mr" >Mr.</option>
                        <option value="Ms" >Ms.</option>
                    </select>
                </div>
                <div class="frm_grp span_3">
                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                    <input placeholder="Enter First Name"    name="abstract_coauthor_edit_first_name[]" required id="abstract_coauthor_first_name" validate="Please Enter First Name" >
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Middle Name</p>
                    <input placeholder="Middle Name"  name="abstract_coauthor_edit_middle_name[]" id="abstract_coauthor_middle_name">
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                    <input  placeholder="Last Name"  name="abstract_coauthor_edit_last_name[]" required id="abstract_coauthor_last_name"  validate="Please Enter Last Name">
                </div>
                <div class="frm_grp span_4">
                    <p class="frm-head">Address </p>
                    <input name="abstract_coauthor_edit_address[]" id="abstract_coauthor_address"  validate="Please Enter Your Address" type="text" >
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Country </p>
                        <select class="coauthor_country  <?= $disabledclass ?>" 
                        name="abstract_coauthor_edit_country[]"
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
                    <p class="frm-head">State </p>
                    <div class="d-flex align-items-center">
                        <select class="coauthor_state  <?= $disabledclass ?>"
                        name="abstract_coauthor_edit_state[]"
                        forType="stateCo"
                        
                        <?= $disabled ?>
                        validate="Please Select State"
                        style="flex:1;" >
                        <option value="">-- Select Country First --</option>
                       
                        </select>
                    </div>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">City</p>
                    <input type="text" name="abstract_coauthor_edit_city[]"  class="form-control" style="text-transform:uppercase;" />
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Pin</p>
                        <input type="text" name="abstract_coauthor_pincode[]"  class="form-control frmdec" style="text-transform:uppercase;" />
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Institute</p>
                    <div class="d-flex align-items-center">

                        <input type="text" name="abstract_coauthor_institute_edit_name[]" class="form-control"  />

                    </div>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Department </p>
                    <div class="d-flex align-items-center">

                    <input type="text" name="abstract_coauthor_edit_department[]" class="form-control"  />

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
    $(".category_radio").click(function(){

    var cat_id = $(this).val();

    $(".subcategory_box").hide();
    $("#subcat_"+cat_id).show();

    $(".subsubcategory_box").hide();

    });


    $(".subcategory_radio").click(function(){

    var sub_id = $(this).val();

    $(".subsubcategory_box").hide();
    $("#presentation_"+sub_id).show();

    });
    $(".category_radio").change(function(){

        var category_id = $(this).val();

        $.post("abstract_user.php",{category_id:category_id},function(data){

         $('#fields_area').html($(data).find('#fields_area').html());
         $('#fields_area1').html($(data).find('#fields_area1').html());
         initializeabstract(); // re-run your JS for the new content

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
    $(document).on("change",".coauthor_country",function(){

    var countryId = $(this).val();

    // var stateDropdown = $(this)
    //     .closest(".form_grid")
    //     .find(".coauthor_state");

    // stateDropdown.html('<option>Loading...</option>');

     if(countryId!=""){
		$.ajax({
					type: "POST",
					url: "returnData.process.php",
					data: "act=generateStateList&countryId="+countryId,
					dataType: "html",
					async: false,
					success: function(JSONObject){
						$("select[forType=stateCo]").html(JSONObject);
						$("select[forType=stateCo]").removeAttr("disabled");
					}
		});
	}else{
		$("select[forType=stateCo]").html('<option value="">-- Select Country First --</option>');
		$("select[forType=stateCo]").attr("disabled","disabled");
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
        $(document).ready(function () {

            $('.award-toggle').each(function () {

                var id = $(this).data('id');
                var fileInput = $('#formFileAbstract_' + id);
                     
                if ($(this).is(':checked')) {
                    // fileInput.attr('required', true);
                    $('#upload_section_' + id).show();
                } else {
                    fileInput.removeAttr('required');
                    $('#upload_section_' + id).hide();
                }

            });

        });
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
        document.querySelectorAll('.delete-file-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = btn.dataset.target; // e.g., "category", "hod", "abstract", "nomination_1"
                const wrapper = btn.closest('.existing-file-wrapper');
                const fileInput = wrapper.parentElement.querySelector('input[type="file"]');
                const deleteInput = wrapper.querySelector('input[name^="delete_"]');

                // Mark file for deletion
                if(deleteInput) deleteInput.value = '1';

                // Hide existing file & delete button
                wrapper.style.display = 'none';

                // Show file input
                if(fileInput) {
                    fileInput.style.display = 'block';
                    fileInput.required = true;
                }
            });
        });
    $(document).on("click", "button[type='submit'][name='submit']", function (e) {
        e.preventDefault();

        var $form = $("#caseRequestEditForm");

        // don't let hidden fields block submission
        $form.find(":input:hidden").removeAttr("required");

        if (!checkAbstractLimits()) return;

        var formData = new FormData($form[0]);
        formData.append("is_ajax", "1");

        // show loader + disable the button so it can't be double-clicked
        var $submitBtn = $(this);
        $submitBtn.prop("disabled", true);
        $("#formLoader").show();

        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (resp) {
                if (resp.status === "success") {
                    // keep loader up through the redirect — page is navigating away anyway
                    window.location.href = resp.redirect;
                } else {
                    $("#formLoader").hide();
                    $submitBtn.prop("disabled", false);
                    toastr.error(resp.message || "Something went wrong. Please try again.", "Error", {
                        progressBar: true,
                        timeOut: 4000,
                        showMethod: "slideDown",
                        hideMethod: "slideUp"
                    });
                }
            },
            error: function () {
                $("#formLoader").hide();
                $submitBtn.prop("disabled", false);
                toastr.error("Something went wrong. Please try again.", "Error", {
                    progressBar: true,
                    timeOut: 4000
                });
            }
        });
    });
 </script>
</html>
<? 
function getCategoryName($id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `category` 
												   FROM "._DB_ABSTRACT_TOPIC_CATEGORY_."
												 WHERE `id` = '".$id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['category'];
}
?>