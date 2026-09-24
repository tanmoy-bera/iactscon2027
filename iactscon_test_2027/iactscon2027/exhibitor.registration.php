<?php
include_once("includes/frontend.init.php");

include_once("includes/function.invoice.php");
include_once('includes/function.delegate.php');
include_once('includes/function.workshop.php');
include_once('includes/function.dinner.php');
include_once('includes/function.exhibitor.php');
include_once('includes/function.accommodation.php');
include_once('includes/function.registration.php');

$show = $_REQUEST['show'];



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="image/favicon.png" type="favicon">
    <title>:: Exhibitor Registration | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>

    <?php
    setTemplateStyleSheet();
    setTemplateBasicJS();
    //backButtonOffJS();





    ?>
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>roboto_css.css" />
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>terms_cond_refund_privacy.css" />
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>all.css" />
    <link rel="stylesheet" type="text/css" href="<?= _DIR_CM_CSS_ . "website/" ?>login_css.php?link_color=<?= $cfg['link_color'] ?>" />

    <link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>util/fontawesome.v5.7.2/css/all.css" />
    <link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>css/website/input-material_css.php?link_color=" />
    <link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>util/bootstrap.3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.tariff.js"></script>

    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/dinner_registration.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/accompany_registration.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/registration.paymentArea.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_registration/scripts/workshop_registration.js"></script>
    <!-- <script type="text/javascript" language="javascript" src="<?= _BASE_URL_ ?>webmaster/section_login/scripts/CountryStateRetriver.js"></script> -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .formbold-mb-3 {
            margin-bottom: 15px;
        }

        .formbold-relative {
            position: relative;
        }

        .formbold-opacity-0 {
            opacity: 0;
        }

        .formbold-stroke-current {
            stroke: #ffffff;
            z-index: 999;
        }

        #supportCheckbox:checked~div span {
            opacity: 1;
        }

        #supportCheckbox:checked~div {
            background: #6a64f1;
            border-color: #6a64f1;
        }

        .formbold-main-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .formbold-form-wrapper {
            /* margin: 0 auto; */
            max-width: 1200px;
            width: 100%;
            background: white;
            padding: 25px;
        }

        .formbold-img {
            display: block;
            margin: 0 auto 45px;
        }

        .formbold-input-wrapp>div {
            display: flex;
            gap: 20px;
        }

        .formbold-input-flex {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .formbold-input-flex>div {
            width: 50%;
        }

        .formbold-form-input {
            width: 100%;
            padding: 4px 10px;
            border-radius: 5px;
            border: 1px solid #dde3ec;
            background: #ffffff;
            font-weight: 500;
            font-size: 16px;
            color: #536387;
            outline: none;
            resize: none;
        }

        .formbold-form-input::placeholder,
        select.formbold-form-input,
        .formbold-form-input[type='date']::-webkit-datetime-edit-text,
        .formbold-form-input[type='date']::-webkit-datetime-edit-month-field,
        .formbold-form-input[type='date']::-webkit-datetime-edit-day-field,
        .formbold-form-input[type='date']::-webkit-datetime-edit-year-field {
            color: rgb(72 95 146 / 67%);
        }

        .formbold-form-input:focus {
            border-color: #6a64f1;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
        }

        .formbold-form-label {
            color: #536387;
            font-size: 14px;
            line-height: 20px;
            display: block;
            margin-bottom: 10px;
            padding-top: 8px;
            width: 100% !important
        }

        .formbold-checkbox-label {
            display: flex;
            cursor: pointer;
            user-select: none;
            font-size: 16px;
            line-height: 24px;
            color: #536387;
        }

        .formbold-checkbox-label a {
            margin-left: 5px;
            color: #6a64f1;
        }

        .formbold-input-checkbox {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .formbold-checkbox-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            margin-right: 16px;
            margin-top: 2px;
            border: 0.7px solid #dde3ec;
            border-radius: 3px;
        }

        .formbold-form-file {
            padding: 12px;
            font-size: 14px;
            line-height: 24px;
            color: rgba(83, 99, 135, 0.5);
        }

        .formbold-form-file::-webkit-file-upload-button {
            display: none;
        }

        .formbold-form-file:before {
            content: 'Upload';
            display: inline-block;
            background: #EEEEEE;
            border: 0.5px solid #E7E7E7;
            border-radius: 3px;
            padding: 3px 12px;
            outline: none;
            white-space: nowrap;
            -webkit-user-select: none;
            cursor: pointer;
            color: #637381;
            font-weight: 500;
            font-size: 12px;
            line-height: 16px;
            margin-right: 10px;
        }

        .formbold-btn {
            font-size: 16px;
            border-radius: 5px;
            padding: 14px 25px;
            border: none;
            font-weight: 500;
            background-color: #6a64f1;
            color: white;
            cursor: pointer;
            margin-top: 25px;
        }

        .formbold-btn:hover {
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
        }

        .formbold-w-45 {
            width: 45%;
        }

        .tr {
            font-size: 14px;
            color: #556087;
            font-weight: bolder;
            padding: 5px 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" language="javascript" src="<?= _DIR_CM_JSCRIPT_ . "website/" ?>login.js?x=<?php echo rand(0, 100) ?>"></script>
    <script src="<?= _BASE_URL_ ?>js/website/returnData.process.js"></script>





</head>

<body>
    <div class="container-fluied">
        <div class="container" style="font-size: 16px;">

            <?php
            switch ($show) {
                case 'list':
                    global $mycms, $cfg;
                    viewList($mycms, $cfg);
                    exit();
                    break;

                default:
                    global $mycms, $cfg;
                    reg_form($mycms, $cfg);

                    break;
            }
            ?>
        </div>
    </div>
    <?php
    function reg_form($mycms, $cfg)
    {

        $sql = array();
        $sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                                        WHERE `status`='A' order by id desc limit 1";

        $result = $mycms->sql_select($sql);
        $row    = $result[0];

        $header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
        if ($row['header_image'] != '') {
            $emailHeader  = $header_image;
        }

        $sqlConfDate = array();
        $sqlConfDate['QUERY']    = " SELECT MIN(conf_date) AS startDate, MAX(conf_date) AS endDate
									   FROM " . _DB_CONFERENCE_DATE_ . " 
									  WHERE `status` = ?";
        $sqlConfDate['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
        $resConfDate = $mycms->sql_select($sqlConfDate);
        $rowConfDate = $resConfDate[0];

        $cutoffArray  = array();
        $sqlCutoff['QUERY']    = " SELECT * 
									 FROM " . _DB_TARIFF_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
        $sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
        $resCutoff = $mycms->sql_select($sqlCutoff);
        if ($resCutoff) {
            foreach ($resCutoff as $i => $rowCutoff) {
                $cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
            }
        }


        $currentCutoffId = getTariffCutoffId();

        $conferenceTariffArray   = getAllRegistrationTariffs("", false);

        $workshopDetailsArray      = getAllWorkshopTariffs();
        $workshopCountArr          = totalWorkshopCountReport();

        $userREGtype                = $_REQUEST['userREGtype'];
        $abstractDelegateId      = $_REQUEST['delegateId'];
        $userRec                  = getUserDetails($abstractDelegateId);

        $exhibitor_company = getExhibitorCompanyDetails();

    ?>
        <div class="row">
            <?
            // leftCommonMenu();
            ?>
            <!-- <div class="col-xs-11 profileright-section"> -->
            <div class="banner-wrap" style="padding: 10px 10px; display: flex;justify-content: space-between;margin-bottom:0px">
                <h1>Exhibitor Registration</h1>
                <img src="<?php echo $emailHeader; ?>" style='width:250px;' alt="">
            </div>

            <a href="<?= _BASE_URL_ ?>exhibitor.registration.php?show=list"><button class="btn btn-info">View Registration List</button></a>

            <div class="formbold-form-wrapper">
                <form action="exhibitor.registration.process.php" method="POST">
                    <input type="hidden" name='act' value='insert'>
                    <input type="hidden" name='registration_tariff_cutoff_id' value='<?= $currentCutoffId ?>'>

                    <li use="registrationUserDetails" class="rightPanel_userDetails">
                        <div class="col-sm-6">
                            <label for="" class="formbold-form-label">Select Your Company</label>
                            <select class="formbold-form-input" id="exhibitor_company_code">
                                <option value="0">Select Company</option>
                                <?php foreach ($exhibitor_company as $key => $rowCompany) {
                                ?>

                                    <option value="<?= $rowCompany['exhibitor_company_code'] ?>"><?= $rowCompany['exhibitor_company_name'] ?></option>

                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-6">

                            <label for="registration_classification_id" class="formbold-form-label">Registration Classification</label>
                            <?php


                            if ($conferenceTariffArray) {
                                foreach ($conferenceTariffArray as $key => $registrationDetailsVal) {
                                    $styleCss = 'style=""';
                                    $classificationType = getRegClsfType($key);
                                    // echo '<pre>'; print_r(getRegClsfName($key));


                                    if ($classificationType != 'ACCOMPANY' && ($classificationType != 'COMBO' || $key == 3)) {
                            ?>
                                        <tr class="tlisting" <?= $styleCss ?>>
                                            <td align="left">

                                                <input type="checkbox" name="registration_classification_id" operationMode="registration_tariff" value="<?= $key ?>" currency="<?= $registrationDetailsVal[1]['CURRENCY'] ?>" registrationType="<?= $classificationType ?>" accommodationPackageId="<?= $residentialAccommodationPackageId[$key] ?>" />


                                                <?= getRegClsfName($key) ?>&nbsp;&nbsp; &nbsp;
                                            </td>
                                            <?

                                            foreach ($registrationDetailsVal as $keyCutoff => $rowCutoff) {
                                                //if($keyCutoff !='4') 
                                                //{

                                                $RegistrationTariffDisplay = $rowCutoff['CURRENCY'] . "&nbsp;" . $rowCutoff['AMOUNT'];
                                                if ($rowCutoff['AMOUNT'] <= 0) {
                                                    if ($classificationType == 'FULL_ACCESS') {
                                                        $RegistrationTariffDisplay = "Complimentary";
                                                    } else {
                                                        $RegistrationTariffDisplay = "Zero Value";
                                                    }
                                                }
                                            ?>
                                                <!-- <td align="right" use="registrationTariff" cutoff="<?= $keyCutoff ?>" tariffAmount="<?= $rowCutoff['AMOUNT'] ?>" tariffCurrency="<?= $rowCutoff['CURRENCY'] ?>"><?= $RegistrationTariffDisplay ?></td> -->
                                            <?php
                                                //}
                                            }
                                            ?>
                                        </tr>
                                <?
                                    }
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="<?= sizeof($cutoffArray) + 1 ?>" align="center">
                                        <strong style="color:#FF0000;">Classification not set</strong>
                                    </td>
                                </tr>
                            <?
                            }
                            ?>
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6">
                            <label for="user_email_id" class="formbold-form-label">Email Id<span class="mandatory">*</span></label>
                            <input type="email" class="formbold-form-input" name="email_id" id="email_id" required />
                            <!-- <input type="hidden" name="email_id_validation" id="email_id_validation" /> -->
                            <div id="email_div" style="padding: 7px 5px;"></div>
                        </div>


                        <div class="col-sm-6">
                            <label for="age" class="formbold-form-label"> Mobile </label>
                            <div style="display:flex">
                                <input type="tel" class="formbold-form-input" style="width: 25%;" name="usd_code" id="mobile_isd_code" value="<?= ($userRec['mobile_isd_code'] != '') ? ($userRec['mobile_isd_code']) : '+91' ?>" required />&nbsp;
                                <input type="tel" class="formbold-form-input" name="mobile_no" id="mobile_no" forType="mobileValidate" pattern="^\d{10}$" value="<?= ($userRec['user_mobile_no'] != '') ? ($userRec['user_mobile_no']) : '' ?>" required />
                            </div>
                            <!-- <input type="hidden" name="mobile_validation" id="mobile_validation" /> -->
                            <div id="mobile_div" style="padding: 7px 5px;"></div>


                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-3">
                            <label for="" class="formbold-form-label">Title</label>
                            <select name="title" id="title" style="width:90%;" class="formbold-form-input" tabindex="4" required>
                                <option value="Dr" selected="selected">Dr</option>
                                <option value="Prof">Prof</option>
                                <option value="Mr">Mr</option>
                                <option value="Ms">Ms</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label for="first_name" class="formbold-form-label">First Name<span class="mandatory">*</span></label>
                            <input type="text" class="formbold-form-input" name="first_name" id="first_name" style="width:90%; text-transform:uppercase;" tabindex="5" value="<?= ($userRec['user_first_name'] != '') ? ($userRec['user_first_name']) : '' ?>" required />
                        </div>
                        <div class="col-sm-3">
                            <label for="user_first_name" class="formbold-form-label">Last Name<span class="mandatory">*</span></label>
                            <input type="text" class="formbold-form-input" name="last_name" id="last_name" tabindex="7" style="width:90%; text-transform:uppercase;" value="<?= ($userRec['user_last_name'] != '') ? ($userRec['user_last_name']) : '' ?>" required implementvalidate="y" />
                        </div>

                        <div class="col-sm-3">

                            <label class="formbold-form-label">Gender <span class="mandatory">*</span></label>

                            <!-- <select class="formbold-form-input" name="occupation" id="occupation"> -->
                            <input type="radio" name="user_gender" id="user_gender_male" checked="checked" value="MALE" tabindex="8" required /> Male
                            <input type="radio" name="user_gender" id="user_gender_female" value="FEMALE" tabindex="9" required /> Female
                            <!-- </select> -->
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-9">
                            <label for="first_name" class="formbold-form-label">Address<span class="mandatory">*</span></label>
                            <textarea name="address" class="formbold-form-input" id="address" tabindex="10" style="height:50px; width:95%; text-transform:uppercase;"><?= ($userRec['user_address'] != '') ? ($userRec['user_address']) : '' ?></textarea>
                        </div>
                        <div class="col-sm-3">
                            <label for="user_first_name" class="formbold-form-label">Food Preference<span class="mandatory">*</span></label>
                            <input type="radio" name="food_preference" id="food_preference_veg" checked="checked" value="VEG" tabindex="15" /> Veg
                            <input type="radio" name="food_preference" id="food_preference_non_veg" value="NON VEG" tabindex="16" /> Non Veg
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-3">
                            <label for="user_country" class="formbold-form-label"> Country </label>
                            <select required implementvalidate="y" name="country" id="country" class="formbold-form-input" style="width:90%;" forType="countryState" stateId="user_state" onchange="stateRetriver(this);" tabindex="11" sequence="1">
                                <option value="0">-- Select Country --</option>
                                <?php
                                $sqlCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
																	           WHERE `status` = 'A' 
											                                ORDER BY `country_name`";
                                $resultCountry = $mycms->sql_select($sqlCountry);
                                if ($resultCountry) {
                                    foreach ($resultCountry as $i => $rowFetchUserCountry) {
                                ?>
                                        <option value="<?= $rowFetchUserCountry['country_id'] ?>"><?= $rowFetchUserCountry['country_name'] ?></option>
                                <?php
                                    }
                                }
                                ?>
                                <?
                                ?>
                            </select>
                        </div>


                        <div class="col-sm-3">

                            <label for="state" class="formbold-form-label"> State </label>
                            <div use='stateContainer'>
                                <select name="state" class="formbold-form-input" id="user_state" style="width:90%;" style="width:90%;" sequence="1" disabled="disabled" required implementvalidate="y">
                                    <option value="0">-- Select Country First --</option>
                                    <!-- <option value="730">WEST BENGAL</option> -->
                                </select>
                            </div>

                        </div>
                        <div class="col-sm-3">
                            <label for="first_name" class="formbold-form-label">City<span class="mandatory">*</span></label>
                            <input type="text" class="formbold-form-input" name="city" id="city" tabindex="13" style="width:90%; text-transform:uppercase;" value="<?= ($userRec['user_city'] != '') ? ($userRec['user_city']) : '' ?>" required implementvalidate="y" />
                        </div>
                        <div class="col-sm-3">
                            <label for="user_first_name" class="formbold-form-label">Postal Code<span class="mandatory">*</span></label>
                            <input type="text" class="formbold-form-input" name="postal_code" id="postal_code" tabindex="14" value="<?= ($userRec['user_pincode'] != '') ? ($userRec['user_pincode']) : '' ?>" style="width:90%; text-transform:uppercase;" required implementvalidate="y" />
                        </div>
                        <div class="col-sm-12">
                            <hr>
                        </div>

                        <!-- <label for="user_state" class="formbold-form-label"> Workshops </label> -->

                        <table width="90%" class="table table-striped">
                            <tr class="theader" style="background:#6c8cbb6b">
                                <td align="left"><b>Workshops</b></td>
                                <?
                                // foreach ($cutoffArray as $cutoffId => $cutoffName) {

                                //if($cutoffId !='4') 
                                //{
                                ?>
                                <td align="center" style="width: 180px;">Price</td>
                                <!-- <td align="right" style="width: 180px;"><?= strip_tags($cutoffName) ?></td> -->
                                <?
                                //}
                                // }
                                ?>
                            </tr>
                            <?php
                            // echo '<pre>'; print_r($workshopDetailsArray);	
                            if (sizeof($workshopDetailsArray) > 0) {

                                foreach ($workshopDetailsArray as $keyWorkshopclsf => $rowWorkshopclsf) {
                                    foreach ($rowWorkshopclsf as $keyRegClasf => $rowRegClasf) {

                                        $workshopTariff = getExhibitortariff('EXB000001', $rowRegClasf[4]['REG_ID']);
                                        $workshop_tariff_inr = json_decode($workshopTariff[0]['workshop_tariff_inr']);
                                        $workshop_id = json_decode($workshopTariff[0]['workshop_id']);
                                        $workshop_tariff_arr = array();
                                        foreach ($workshop_id as $key => $id) {
                                            $workshop_tariff_arr[$id] = $workshop_tariff_inr[$key];
                                        }

                                        // echo '<pre>';
                                        // print_r($workshopTariff);
                                        // die;
                                        // echo '<pre>'; print_r($rowRegClasf);
                                        // December workshop
                                        if ($rowRegClasf[4]['WORKSHOP_TYPE'] == 'MASTER CLASS') {

                                            $workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];

                                            if ($workshopCount < 1) {
                                                $style = 'disabled="disabled"';
                                                $span    = '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
                                            } else {
                                                $style = '';
                                                $span    = '';
                                            }
                            ?>
                                            <tr use="<?= $keyRegClasf ?>" operetionMode="workshopTariffTr" style="display:none;">
                                                <td align="left">
                                                    <div class="tooltip" style="opacity: 1;">
                                                        <?= $span ?>
                                                        <input type="checkbox" class="formbold-form-input" operationMode='workshopId' <?= $style ?> name="workshop_id[]" value="<?= $keyWorkshopclsf ?>" />
                                                    </div>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= getWorkshopName($keyWorkshopclsf) . ' (' . $mycms->cDate('m-d-Y', getWorkshopDate($keyWorkshopclsf)) . ')' ?>
                                                </td>
                                                <?

                                                // foreach ($rowRegClasf as $keyCutoff => $cutoffvalue) {
                                                if (in_array($rowRegClasf[4]['WORKSHOP_ID'], $workshop_id)) {
                                                    $WorkshopTariffDisplay = "INR &nbsp;" . $workshop_tariff_arr[$rowRegClasf[4]['WORKSHOP_ID']];
                                                    if ($workshop_tariff_arr[$rowRegClasf[4]['WORKSHOP_ID']] <= 0 || $workshop_tariff_arr[$rowRegClasf[4]['WORKSHOP_ID']] = "") {
                                                        $WorkshopTariffDisplay = "Included in Registration";
                                                    }
                                                }
                                                else{
                                                    $WorkshopTariffDisplay ="Not Set";
                                                }
                                                // $WorkshopTariffDisplay = $cutoffvalue['CURRENCY'] . "&nbsp;" . $cutoffvalue[$cutoffvalue['CURRENCY']];
                                                // if ($cutoffvalue[$cutoffvalue['CURRENCY']] <= 0) {
                                                //     $WorkshopTariffDisplay = "Included in Registration";
                                                // }
                                                ?>
                                                <td align="center" use="workshopTariff" cutoff="<?= $keyCutoff ?>" tariffAmount="<?= $cutoffvalue[$cutoffvalue['CURRENCY']] ?>" tariffCurrency="<?= $cutoffvalue['CURRENCY'] ?>"><?= $WorkshopTariffDisplay ?></td>
                                                <?

                                                // }
                                                ?>
                                            </tr>
                                        <?
                                        }

                                        // November workshop
                                        if ($rowRegClasf[4]['WORKSHOP_TYPE'] == 'WORKSHOP') {


                                            $workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];

                                            if ($workshopCount < 1) {
                                                $style = 'disabled="disabled"';
                                                $span    = '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
                                            } else {
                                                $style = '';
                                                $span    = '';
                                            }
                                        ?>
                                            <tr use="<?= $keyRegClasf ?>" operetionMode="workshopTariffTr" style="display:none;">
                                                <td align="left">
                                                    <div class="tooltip" style="opacity: 1;">
                                                        <?= $span ?>
                                                        <input type="checkbox" operationMode='workshopId_nov' <?= $style ?> name="workshop_id[]" value="<?= $keyWorkshopclsf ?>" />
                                                    </div>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ucwords(getWorkshopName($keyWorkshopclsf)) . ' (' . $mycms->cDate('m-d-Y', getWorkshopDate($keyWorkshopclsf)) . ')' ?>
                                                </td>
                                                <?
                                                // foreach ($rowRegClasf as $keyCutoff => $cutoffvalue) {
                                                //     //if($keyCutoff !='4') 
                                                //     //{
                                                //     $WorkshopTariffDisplay = $cutoffvalue['CURRENCY'] . "&nbsp;" . $cutoffvalue[$cutoffvalue['CURRENCY']];
                                                //     if ($cutoffvalue[$cutoffvalue['CURRENCY']] <= 0) {
                                                //         $WorkshopTariffDisplay = "Included in Registration";
                                                //     }

                                                if (in_array($rowRegClasf[4]['WORKSHOP_ID'], $workshop_id)) {
                                                    $WorkshopTariffDisplay = "INR &nbsp;" . $workshop_tariff_arr[$rowRegClasf[4]['WORKSHOP_ID']];
                                                    if ($workshop_tariff_arr[$rowRegClasf[4]['WORKSHOP_ID']] <= 0 || $workshop_tariff_arr[$rowRegClasf[4]['WORKSHOP_ID']] = "") {
                                                        $WorkshopTariffDisplay = "Included in Registration";
                                                    }
                                                }
                                                else{
                                                    $WorkshopTariffDisplay ="Not Set";
                                                }

                                                ?>
                                                <td align="center" use="workshopTariff" cutoff="<?= $keyCutoff ?>" tariffAmount="<?= $cutoffvalue[$cutoffvalue['CURRENCY']] ?>" tariffCurrency="<?= $cutoffvalue['CURRENCY'] ?>"><?= $WorkshopTariffDisplay ?></td>
                                                <?
                                                //}
                                                // }
                                                ?>
                                            </tr>
                            <?
                                        }
                                    }
                                }
                            }
                            ?>
                            <tr use="na" operetionMode="workshopTariffTr">
                                <td align="center" colspan="<?= sizeof($cutoffArray) + 1 ?>"><strong style="color:#FF0000;">Please Select Registration Classification First</strong></td>
                            </tr>
                        </table>
                        <div class=" col-lg-2 text-center pull-right">
                            <!-- <input type="submit" name="bttnSubmitStep1" operationmode="bttnSubmitStep1" id="bttnSubmitStep1" value="<?= ($isComplementary != 'Y') ? "Submit" : "Proceed" ?>" class="btn btn-blue" /> -->

                        </div>
                        <!-- <td align="right">
									<input type="submit" name="bttnSubmitStep1" operationmode="bttnSubmitStep1" id="bttnSubmitStep1" value="<?= ($isComplementary != 'Y') ? "Submit" : "Proceed" ?>" class="btn btn-blue" />
								</td> -->
                    </li>






                    <!-- <div class="formbold-mb-3">
                                <label for="upload" class="formbold-form-label">
                                    Upload Signature
                                </label>
                                <input type="file" name="upload" id="upload" class="formbold-form-input formbold-form-file" />
                            </div> -->


                    <li use="registrationAccompanyDetails" style="display:block;" class="rightPanel_accompany">
                        <?
                        //echo 'currentId='.$currentCutoffId;
                        //$accompanyCatagory      = 2;

                        $accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 

                        //$registrationAmount 	= $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
                        $registrationAmount     = getCutoffTariffAmnt($currentCutoffId);
                        $registrationCurrency     = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
                        //$conferenceTariffArray
                        $accompanyTariffAmnt     = getAllAccompanyTariffs($currentCutoffId);


                        // echo '<pre>'; print_r($accompanyTariffAmnt);
                        ?>
                        <input type="hidden" name="accompanyClasfId" value="<?= $accompanyCatagory ?>" />
                        <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?= $registrationAmount ?>" />
                        <div class="link" use="" style="padding: 8px;background: #c3cde3;"><b>Accompany</b></div>
                        <!-- <ul class="submenu" style="display: none"> -->
                        <!-- <li> -->
                        <div class="col-xs-12 form-group " style="border: 1px solid #dddddd;">
                            <div class="checkbox">
                                <label class="select-lable" style="padding:10px">Number of Accompanying Person(s)</label>
                                <div style="padding-top:10px">
                                    <label class="container-box" style="float:left; margin-right:20px;">None
                                        <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="0" amount="<?= 0 ?>" invoiceTitle="Accompanying Person" checked="checked" required>
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="container-box" style="float:left; margin-right:20px;">One
                                        <input type="radio" name="accompanyCount" id="accompanyCount1" use="accompanyCountSelect" value="1" amount="<?= floatval($registrationAmount) * 1 ?>" invoiceTitle="Accompanying - 1 Person">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="container-box" style="float:left; margin-right:20px;">Two
                                        <input type="radio" name="accompanyCount" id="accompanyCount2" use="accompanyCountSelect" value="2" amount="<?= floatval($registrationAmount) * 2 ?>" invoiceTitle="Accompanying - 2 Person">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="container-box" style="float:left; margin-right:20px;">Three
                                        <input type="radio" name="accompanyCount" id="accompanyCount3" use="accompanyCountSelect" value="3" amount="<?= floatval($registrationAmount) * 3 ?>" invoiceTitle="Accompanying - 3 Person">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="container-box" style="float:left; margin-right:20px;">Four
                                        <input type="radio" name="accompanyCount" id="accompanyCount4" use="accompanyCountSelect" value="4" amount="<?= floatval($registrationAmount) * 4 ?>" invoiceTitle="Accompanying - 4 Person">
                                        <span class="checkmark"></span>
                                    </label>
                                    <i class="itemPrice pull-right" id="accompanyAmntDisplay" operetionMode="workshopTariffTr" style="display:none;">
                                        <?
                                        // if (floatval($registrationAmount) > 0) {
                                        //     echo '@ ' . $registrationCurrency . ' ' . number_format($registrationAmount, 2);
                                        // }
                                        ?>
                                    </i>
                                    &nbsp;
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12" use="accompanyDetails" index="1" style="display:none;box-shadow: 1px 1px 4px rgb(0 0 0 / 25%);padding-bottom:38px;margin-bottom: 15px;">
                            <h4 class="formbold-form-label" style="font-weight: 700;">ACCOMPANY 1</h4>
                            <hr>
                            <div class="col-xs-6" actAs='fieldContainer'>
                                <label for="accompany_name_add_1" class="formbold-form-label">Name</label>
                                <input type="text" class="form-control formbold-form-input" name="accompany_name_add[0]" id="accompany_name_add_1" style="text-transform:uppercase;">
                                <input type="hidden" name="accompany_selected_add[0]" value="0" />
                                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                </div>
                            </div>

                            <!-- <div class="col-xs-8 form-group" actAs='fieldContainer'>
                                    <div class="checkbox">
                                        <label class="select-lable">DINNER</label>
                                        <?
                                        // $dinnerTariffArray   = getAllAccompanyDinnerTarrifDetails($currentCutoffId);

                                        //echo '<pre>'; print_r($dinnerTariffArray);
                                        foreach ($dinnerTariffArray as $keyDinner => $dinnerValue) {
                                            // echo '<pre>'; print_r($dinnerValue[$currentCutoffId]);

                                            //echo 'keydinner='.$keyDinner;
                                            foreach ($dinnerValue[$currentCutoffId] as $registration_classification_id => $row) {

                                                if ($cfg['currency_flag'] == 'USD' && $row['USD_AMOUNT'] > 0) {

                                                    $accompanyDinnerAmnt = $row['USD_AMOUNT'];
                                                    $accompanyDinnerCurrency = 'USD';
                                                } else {

                                                    $accompanyDinnerAmnt = $row['AMOUNT'];
                                                    $accompanyDinnerCurrency = $registrationDetailsVal['CURRENCY'];
                                                }



                                                // echo 'classificationID='. $registration_classification_id;
                                        ?>
                                                <label class="container-box accompany_dinner registration_accompany_dinner_classification_id<?= $registration_classification_id ?>" id="registration_accompany_dinner_classification_id<?= $registration_classification_id ?>" style="display: none;">
                                                    <i class="itemTitle left-i"><?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?></i>
                                                    <i class="itemPrice right-i pull-right">
                                                        <?
                                                        if (floatval($accompanyDinnerAmnt) > 0) {
                                                            echo $accompanyDinnerCurrency . ' ' . number_format($accompanyDinnerAmnt, 2);
                                                        }

                                                        ?>
                                                    </i>
                                                    <input type="checkbox" name="accompany_dinner_value[0]" id="dinner_value" value="<?= $dinnerValue[$currentCutoffId]['ID'] ?>" operationMode="dinner" amount="<?= $accompanyDinnerAmnt ?>" invoiceTitle="<?= $dinnerValue[$currentCutoffId]['DINNER_TITTLE'] ?> - Accompany 1" />
                                                    <span class="checkmark"></span>
                                                </label>
                                        <?

                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                                </div> -->

                            <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                <label class="formbold-form-label">Food Preference</label>
                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]" id="accompany_food_1_veg" value="VEG" tabindex="15" /> Veg
                                <input type="radio" name="accompany_food_choice[0]" groupName="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16" /> Non Veg
                                <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                            </div>
                        </div>

                        <div class="col-xs-12" use="accompanyDetails" index="2" style="display:none;box-shadow: 1px 1px 4px rgb(0 0 0 / 25%);padding-bottom:38px;margin-bottom: 15px;">
                            <h4 class="formbold-form-label" style="font-weight: 700;">ACCOMPANY 2</h4>
                            <hr>
                            <div class="col-xs-6 " actAs='fieldContainer'>
                                <label for="accompany_name_add_2" class="formbold-form-label">Name</label>
                                <input type="text" class="form-control" name="accompany_name_add[1]" id="accompany_name_add_2" style="text-transform:uppercase;">
                                <input type="hidden" name="accompany_selected_add[1]" value="1" />
                                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                            </div>


                            <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                <label class="formbold-form-label">Food Preference</label>
                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]" id="accompany_food_1_veg" value="VEG" tabindex="15" /> Veg
                                <input type="radio" name="accompany_food_choice[1]" groupName="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16" /> Non Veg
                                <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                            </div>
                        </div>

                        <div class="col-xs-12" use="accompanyDetails" index="3" style="display:none;box-shadow: 1px 1px 4px rgb(0 0 0 / 25%);padding-bottom:38px;margin-bottom: 15px;">
                            <h4 class="formbold-form-label" style="font-weight: 700;">ACCOMPANY 3</h4>
                            <hr>
                            <div class="col-xs-6" actAs='fieldContainer'>
                                <label for="accompany_name_add_3" class="formbold-form-label">Name</label>
                                <input type="text" class="form-control" name="accompany_name_add[2]" id="accompany_name_add_3" style="text-transform:uppercase;">
                                <input type="hidden" name="accompany_selected_add[2]" value="2" />
                                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                            </div>



                            <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                <label class="formbold-form-label">Food Preference</label>
                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]" id="accompany_food_1_veg" value="VEG" tabindex="15" /> Veg
                                <input type="radio" name="accompany_food_choice[2]" groupName="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16" /> Non Veg
                                <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                            </div>
                        </div>

                        <div class="col-xs-12" use="accompanyDetails" index="4" style="display:none;box-shadow: 1px 1px 4px rgb(0 0 0 / 25%);padding-bottom:38px;margin-bottom: 15px;">
                            <h4 class="formbold-form-label" style="font-weight: 700;">ACCOMPANY 4</h4>
                            <hr>
                            <div class="col-xs-6 ">
                                <label for="accompany_name_add_4" class="formbold-form-label">Name</label>
                                <input type="text" class="form-control" name="accompany_name_add[3]" id="accompany_name_add_4" style="text-transform:uppercase;">
                                <input type="hidden" name="accompany_selected_add[3]" value="3" />
                                <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
                            </div>


                            <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                <label class="formbold-form-label">Food Preference</label>
                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]" id="accompany_food_1_veg" value="VEG" tabindex="15" /> Veg
                                <input type="radio" name="accompany_food_choice[3]" groupName="accompany_food_choice" id="accompany_food_1_nonveg" value="NON VEG" tabindex="16" /> Non Veg
                                <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
                            </div>
                        </div>


                        <div class="clearfix"></div>
                        <!-- </li> -->
                        <!-- </ul> -->
                    </li>
                    <?php
                    $hotel_array = array();
                    $sqlHotel                = array();
                    $sqlHotel['QUERY']         = "SELECT tracm.id as ACCOMMODATION_TARIFF_ID,
										tracm.hotel_id as HOTEL_ID,
										tracm.package_id as ACCOMMODATION_PACKAGE_ID,
										tracm.tariff_cutoff_id as CUTOFF_ID,
										tracm.checkin_date_id as CHECKIN_DATE_ID,
										tracm.checkout_date_id as CHECKOUT_DATE_ID,
										tracm.currency as CURRENCY,
										tracm.inr_amount as AMOUNT,
										tracm.usd_amount as USD_AMOUNT,
										tracm.status as STATUS,
										hotel_master.hotel_name as HOTEL_NAME,
										chkindate.check_in_date as CHECKIN_DATE,
										chkoutdate.check_out_date as CHECKOUT_DATE,
										DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS
										FROM " . _DB_MASTER_HOTEL_ . " as hotel_master
										INNER JOIN " . _DB_TARIFF_ACCOMMODATION_ . " as tracm 
										on tracm.hotel_id = hotel_master.id AND tracm.status = 'A'
										LEFT JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " as chkindate
										on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
										LEFT JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " as chkoutdate
										on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
										WHERE hotel_master.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ?
										GROUP BY DAYS,tracm.hotel_id
										HAVING (DAYS) < 5
										 ORDER BY tracm.hotel_id ASC, DAYS ASC";  // HAVING (DAYS) < 4  // remove on 21.09.2022 (user can select hotels more then 3 days)

                    $sqlHotel['PARAM'][]    = array('FILD' => 'hotel_master.status', 'DATA' => 'A',  'TYP' => 's');
                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
                    $resHotel                = $mycms->sql_select($sqlHotel);


                    foreach ($resHotel as $key => $value) {
                        $nights            = '';
                        $temp_array     = array();
                        $temp_array['HOTEL_ID'] = $value['HOTEL_ID'];
                        $temp_array['ACCOMMODATION_PACKAGE_ID'] = $value['ACCOMMODATION_PACKAGE_ID'];

                        /*  commented on 21.09.2022 (user can select hotels more then 3 days)
				switch($value['DAYS'])
				{
					case '1':
						$nights = '1 Night Stay';
						break;
					case '2':
						$nights = '2 Nights Stay';
						break;
					case '3':
						$nights = '3 Nights Stay';
						break;
				}
				*/

                        $nights = $value['DAYS'] . ' Night Stay';

                        $sharing                = 'Individual';
                        $residentialPackDataOrganizer[$nights][$sharing][] = $value;
                        $hotel_array[$value['HOTEL_ID']] = $temp_array;
                    }

                    // accommodation related work by weavers end

                    // accommodation part is hide for now 22.07.2022 by weavers
                    if (sizeof($residentialPackDataOrganizer) > 0) {


                        //echo  '<pre>'; print_r($row_package);


                        // if(sizeof($hotel_array) > 0){

                    ?>

                        <li use="registrationOptions" style="display:block;" class="rightPanel_chooseOption">
                            <div class="link" use="rightAccordianL1TriggerDiv">ACCOMMODATION DETAILS</div>
                            <!-- <ul class="submenu" style="display:block;"> -->
                            <!-- <li> -->
                            <div class="col-xs-12 form-group" style="display:block;" use="residentialOperations" operationMode="chhoseServiceOptions">
                                <?php
                                //$accommodationDetails = $cfg['ACCOMMODATION_PACKAGE_ARRAY'];

                                // accommodation related work by weavers start
                                $accommodationDetails = array();
                                if (sizeof($hotel_array) > 0) {
                                    $hotel_ids = array_column($hotel_array, 'HOTEL_ID');
                                    $package_ids = array_column($hotel_array, 'ACCOMMODATION_PACKAGE_ID');
                                    $sqlHotel                = array();
                                    $sqlHotel['QUERY']         = "SELECT
											tracm.id as ACCOMMODATION_TARIFF_ID,
											tracm.hotel_id as HOTEL_ID,
											tracm.package_id as ACCOMMODATION_PACKAGE_ID, 
											chkindate.check_in_date as CHECKIN_DATE,
											tracm.checkin_date_id as CHECKIN_DATE_ID,
											tracm.checkout_date_id as CHECKOUT_DATE_ID,
											chkoutdate.check_out_date as CHECKOUT_DATE,
											DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
											FROM " . _DB_TARIFF_ACCOMMODATION_ . " as tracm
											INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " as chkindate
											on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
											INNER JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " as chkoutdate
											on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
											WHERE tracm.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ? 
											AND tracm.hotel_id IN(" . implode(',', $hotel_ids) . ") AND tracm.package_id IN(" . implode(',', $package_ids) . ")
											HAVING (DAYS) < 5
											ORDER BY tracm.hotel_id ASC, DAYS ASC,CHECKIN_DATE ASC, CHECKOUT_DATE ASC"; // HAVING (DAYS) < 4 // remove on 21.09.2022 (user can select hotels more then 3 days)
                                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.status', 'DATA' => 'A',  'TYP' => 's');
                                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
                                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
                                    $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
                                    $resultHotel                = $mycms->sql_select($sqlHotel);
                                    //echo '<pre>';print_r($resultHotel);
                                    foreach ($resultHotel as $key => $value) {
                                        // code...
                                        $accommodationDetails[$value['HOTEL_ID']][] = $value;
                                    }
                                }

                                // accommodation related work by weavers end $packageId
                                foreach ($accommodationDetails as $hotelId => $rowAccommodation) {
                                ?>
                                    <div class="col-xs-12 " style="padding: 0; display:block;" use="<?= $hotelId ?>" operetionMode="checkInCheckOutTr">
                                        <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                            <div class="radio">
                                                <label class="select-lable">CHECK-IN DATE</label>
                                                <?
                                                foreach ($rowAccommodation as $seq => $accPackDet) {

                                                ?>
                                                    <label class="container-box" style="display:block"><?= $accPackDet['CHECKIN_DATE'] ?>
                                                        <input type="radio" operetionMode="checkInCheckOut_<?= $accPackDet['HOTEL_ID'] . '_' . $accPackDet['DAYS'] ?>" use="accoStartDate" id="accDate_<?= $accPackDet['ACCOMMODATION_PACKAGE_ID'] . '_' . $accPackDet['HOTEL_ID'] . '_' . $accPackDet['DAYS'] . '_' . $currentCutoffId ?>" name="accDate[]" value="<?= $accPackDet['CHECKIN_DATE_ID'] . '-' . $accPackDet['CHECKOUT_DATE_ID'] . '-' . $accPackDet['HOTEL_ID'] ?>" checkoutDate="<?= $accPackDet['CHECKOUT_DATE_ID'] . '_' . $accPackDet['ACCOMMODATION_TARIFF_ID'] ?>" onClick="showChekinChekoutDate(this);">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                <?
                                                }
                                                ?>
                                            </div>
                                            <div class="alert alert-danger" callFor='alert'>Please select a proper
                                                option.</div>
                                        </div>
                                        <div class="col-xs-6 form-group" actAs='fieldContainer'>
                                            <div class="radio">
                                                <label class="select-lable">CHECK-OUT DATE</label>
                                                <?
                                                foreach ($rowAccommodation as $seq => $accPackDet) {
                                                ?>
                                                    <label class="container-box" style="display:none;"><?= $accPackDet['CHECKOUT_DATE'] ?>
                                                        <input type="radio" operetionMode="checkInCheckOut_<?= $accPackDet['HOTEL_ID'] . '_' . $accPackDet['DAYS'] ?>" value="<?= $accPackDet['CHECKOUT_DATE_ID'] . '_' . $accPackDet['ACCOMMODATION_TARIFF_ID'] ?>" use="accoEndDate" disabled="disabled">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                <?
                                                }
                                                ?>
                                            </div>
                                            <div class="alert alert-danger" callFor='alert'>Please select a proper
                                                option.</div>
                                        </div>
                                    </div>
                                <?
                                }
                                ?>
                                <div class="col-xs-12 " style="padding: 0; display:none;" use="ResidentialAccommodationAccompanyOption">
                                    <h4>ROOM SHARING PREFERENCE, IF ANY</h4>
                                    <div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
                                        <label for="preffered_accommpany_name">NAME</label>
                                        <input type="text" class="form-control" name="preffered_accommpany_name" id="preffered_accommpany_name">
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                    <div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
                                        <label for="preffered_accommpany_mobile">MOBILE</label>
                                        <input type="tel" class="form-control" name="preffered_accommpany_mobile" id="preffered_accommpany_mobile">
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                    <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
                                        <label for="preffered_accommpany_email">EMAIL</label>
                                        <input type="email" class="form-control" name="preffered_accommpany_email" id="preffered_accommpany_email">
                                        <div class="alert alert-danger" callFor='alert'>Please enter a proper value.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class=" col-xs-2 text-center pull-right">
                                <button type="button" class="submit" use='nextButton'>Next</button>
                            </div>
                            <div class="clearfix"></div>
                            <!-- </li> -->
                            <!-- </ul> -->
                        </li>
                    <?php } ?>
                    <div class=" col-xs-12 text-center pull-right">
                        <!-- <button type="submit" class="submit" use='nextButton'>Proceed to Payment</button> -->
                    </div>

                    <div class=" col-xs-12 text-center pull-right" style="display:flex">
                        <input type="submit" name="submit" id="bttnSubmitStep1" class="btn btn-success" value="Save & Next" />&nbsp;&nbsp;
                        <!-- <input type="submit" name="submit" id="bttnSubmitStep1" class="btn btn-info" value="Finish" /> -->
                    </div>



                </form>

            </div>
            <!-- </div> -->

        </div>
    <?php
    }

    function viewList($mycms, $cfg)
    {
        $sqlFetchUser  = array();
        $sqlFetchUser['QUERY']        = "SELECT * FROM " . _DB_EXHIBITOR_REGISTRATION_ . "
													WHERE  `status` = ? ORDER BY id";

        // $sqlFetchUser['PARAM'][]   = array('FILD' => 'mobile_no', 'DATA' => $mobile, 'TYP' => 's');
        $sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

        $resultFetchUser            = $mycms->sql_select($sqlFetchUser);

        $sql = array();
        $sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                                        WHERE `status`='A' order by id desc limit 1";

        $result = $mycms->sql_select($sql);
        $row    = $result[0];

        $header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
        if ($row['header_image'] != '') {
            $emailHeader  = $header_image;
        }
    ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <div class="row">
            <div class="banner-wrap" style="padding: 10px 10px; display: flex;justify-content: space-between;margin-bottom:0px">
                <h1>Registration List</h1>

                <img src="<?php echo $emailHeader; ?>" style='width:250px;' alt="">
            </div>
            <a href="<?= _BASE_URL_ ?>exhibitor.registration.php"><button class="btn btn-warning">Add more delegate</button></a>
            <hr>
            <table class="table table-striped" id="regList">
                <thead>
                    <tr>
                        <th scope="col">Sl no.</th>
                        <th scope="col">Email</th>
                        <th scope="col">Registration Details</th>
                        <th scope="col">Workshop</th>
                        <th scope="col">Accompany Persons</th>
                        <!-- <th scope="col">Last</th> -->
                        <!-- <th scope="col">Handle</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($resultFetchUser as $key => $rowUser) {
                    ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $rowUser['email_id'] ?></td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>Name: <?= $rowUser['full_name'] ?><br></tr>
                                        <tr>Mobile: <?= $rowUser['mobile_no'] ?></tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <?php
                                $ids = json_decode($rowUser['workshop_id']);
                                foreach ($ids as $key => $id) {
                                    echo ($key + 1) . ". " . getWorkshopName($id) . "<br>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($rowUser['accompanyCount'] > 0) {
                                    $accompany_names = json_decode($rowUser['accompany_name_add']);
                                    for ($i = 0; $i < $rowUser['accompanyCount']; $i++) {
                                        echo ($i + 1) . ". " . $accompany_names[$i] . "<br>";
                                    }
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                            <!-- <td>Otto</td>
                        <td>@mdo</td> -->
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
            <script>
                $(document).ready(function() {
                    // $('#myTable').DataTable();
                    let table = new DataTable('#regList');
                });
            </script>
        </div>
    <?php
    }
    ?>



    <script type="text/javascript">
        $(document).ready(function() {
            popDownAlert();
            $('#email_id').on('change', function() {
                var user_email_id = $.trim($("#email_id").val());
                var status = false;

                if (user_email_id != "") {

                    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (regex.test(user_email_id) == false) {
                        $('#email_div').html('<span style="color:#D41000;">Please enter a valid e-mail id!</span>');
                        // $("#email_id").val('');
                        $("#loaderImg").hide();
                    } else {
                        $.ajax({
                            type: "POST",
                            url: jsBASE_URL + 'exhibitor.registration.process.php',
                            data: 'act=getEmailValidationStatus&email=' + user_email_id,
                            dataType: 'json',
                            async: false,
                            success: function(JSONObject) {
                                if (JSONObject.STATUS == 'IN_USE' || JSONObject.STATUS == 'IN_USE_EXHIBITOR') {
                                    $("#email_id").val('');
                                    $('#email_div').html('<span style="color:#D41000; ">Email already registered with us.</span>');
                                } else if (JSONObject.STATUS == 'AVAILABLE') {
                                    // userdetails();
                                    $('#email_div').html('<span style="color:#009933;">Available</span>');
                                }
                            }
                        });
                    }
                }

            });

            $('#mobile_no').on('change', function() {
                var mobile = $.trim($("#mobile_no").val());
                var status = false;

                if (mobile != "") {

                    var regex = /^[0-9]{10}$/;
                    if (regex.test(mobile) == false) {
                        $('#mobile_div').html('<span style="color:#D41000;">Please enter a valid mobile number!</span>');
                        $("#mobile_no").val('');
                        $("#loaderImg").hide();
                    } else {
                        $.ajax({
                            type: "POST",
                            url: jsBASE_URL + 'exhibitor.registration.process.php',
                            data: 'act=getMobileValidationStatus&mobile=' + mobile,
                            dataType: 'json',
                            async: false,
                            success: function(JSONObject) {
                                if (JSONObject.STATUS == 'IN_USE' || JSONObject.STATUS == 'IN_USE_EXHIBITOR') {
                                    $("#mobile_no").val('');
                                    $('#mobile_div').html('<span style="color:#D41000; ">Mobile number is already registered with us.</span>');
                                } else if (JSONObject.STATUS == 'AVAILABLE') {
                                    // userdetails();
                                    $('#mobile_div').html('<span style="color:#009933;">Available</span>');
                                }
                            }
                        });
                    }
                }

            });

            $("input[type=radio][use=accompanyCountSelect]").click(function() {
                var count = parseInt($(this).val());
                var haveCount = $("div[use=accompanyDetails]").length;
                for (var i = 1; i <= count; i++) {
                    $("div[use=accompanyDetails][index='" + i + "']").slideDown();
                }
                for (var j = (count + 1); j <= haveCount; j++) {
                    var accomDiv = $("div[use=accompanyDetails][index='" + j + "']");
                    $(accomDiv).slideUp();
                    $(accomDiv).find("input[type=text]").val('');
                    $(accomDiv).find("input[type=radio]").prop('checked', false);
                    $(accomDiv).find("input[type=checkbox]").prop('checked', false);
                }
                // calculateTotalAmount();
            });

            function popDownAlert(obj) {
                if (typeof obj === typeof undefined) {
                    $("div[callFor=alert]").hide();
                } else {
                    $(obj).hide();
                }
            }

            $('#bttnSubmitStep1').click(function() {
                var validaCheck = $("input[type=checkbox][operationmode=validateCheck]:checked").length;
                if (validaCheck == 0) {
                    $('#frmRegistrationStep1').find('input[implementvalidate="y"], select[implementvalidate="y"]').prop('required', false);
                }
            });


        });

        function stateRetriver(obj) {
            var sequenceVal = $(obj).attr("sequence");
            var countryValue = $(obj).val();
            var stateControl = $(obj).attr("stateId");
            var stateControlDiv = $('#' + stateControl).parent().closest("div[use=stateContainer]");

            if (countryValue != "") {
                // console.log('<?= _BASE_URL_ ?> section_login/countryState.process.php?act=getStateControl&countryValue=' + countryValue + '&stateControl=' + stateControl + '&sequenceVal=' + sequenceVal);
                var act = 'getStateControl';
                $.ajax({
                    type: "POST",
                    url: "<?= _BASE_URL_ ?>webmaster/section_login/countryState.process.php",
                    data: 'act=getStateControl&countryValue=' + countryValue + '&stateControl=' + stateControl + '&sequenceVal=' + sequenceVal,
                    dataType: "html",
                    async: false,
                    success: function(message) {
                        if (message != "") {

                            $(stateControlDiv).html('');
                            $(stateControlDiv).html(message);
                        }
                        $('#user_state').addClass('formbold-form-input');
                    }
                });
            } else {
                // console.log(<?= _BASE_URL_ ?> + 'section_login/countryState.process.php?act=getBlankStateControl&countryValue=' + countryValue + '&stateControl=' + stateControl + '&sequenceVal=' + sequenceVal);
                $.ajax({
                    type: "POST",
                    url: "<?= _BASE_URL_ ?>webmaster/section_login/countryState.process.php",
                    data: 'act=getBlankStateControl&countryValue=' + countryValue + '&stateControl=' + stateControl + '&sequenceVal=' + sequenceVal,
                    dataType: "html",
                    async: false,
                    success: function(message) {
                        if (message != "") {
                            $(stateControlDiv).html('');
                            $(stateControlDiv).html(message);
                        }
                    }
                });
            }
        }


        function generateSateList(countryId, jBaseUrl) {
            console.log(jBaseUrl + "returnData.process.php?act=generateStateList&countryId=" + countryId);

            if (countryId != "") {
                $.ajax({
                    type: "POST",
                    url: jBaseUrl + "returnData.process.php",
                    data: "act=generateStateList&countryId=" + countryId,
                    dataType: "html",
                    async: false,
                    success: function(JSONObject) {
                        $("select[forType=state]").html(JSONObject);
                        $("select[forType=state]").removeAttr("disabled");
                    }
                });
            } else {
                $("select[forType=state]").html('<option value="">-- Select Country First --</option>');
                $("select[forType=state]").attr("disabled", "disabled");
            }
        }

        $("#hotel_select_id").on('change', function() {
            var hotel_id = $(this).val();
            $('.holel_list_combo').hide();
            if (hotel_id != '') {

                $('#hotelErr').hide();
                $('.combo_items' + hotel_id).show();
            } else {
                // alert(12);
                $('#hotelErr').show();
            }
        });

        $('#hotel_select_acco_id').on('change', function() {
            var hotel_id = $(this).val();

            if (hotel_id != '') {

                $.ajax({
                    type: "POST",
                    url: jBaseUrl + "returnData.process.php",
                    data: "act=getAllPackage&hotel_id=" + hotel_id,
                    dataType: "html",
                    async: false,
                    success: function(JSONObject) {
                        console.log(JSONObject);
                        if (JSONObject) {
                            if (JSONObject.trim() == 'error') {

                                alert("Seat is not available for this hotel.");
                            } else {

                                $('#packageDiv').html(JSONObject);
                                $('#packageDiv').show();
                            }

                        } else {
                            $('#packageDiv').html('');
                            $('#packageDiv').hide();
                        }

                    }
                });
            } else {

                $('#packageDiv').html('');
                $('#packageDiv').hide();
            }


        });

        function datediff(first, second) {
            return Math.round((second - first) / (1000 * 60 * 60 * 24));
        }

        /**
         * new Date("dateString") is browser-dependent and discouraged, so we'll write
         * a simple parse function for U.S. date format (which does no error checking)
         */
        function parseDate(str) {
            var mdy = str.split('-');
            return new Date(mdy[2], mdy[0] - 1, mdy[1]);
        }

        function get_checkout_val(val) {
            // alert(val);
            var checkInVal = $('#accomodation_package_checkin_id').val();
            var checkOutVal = val;
            const checkInArray = checkInVal.split("/");
            var checkInID = checkInArray[0];
            var checkInDate = checkInArray[1];

            const checkOutArray = checkOutVal.split("/");
            var checkOutID = checkOutArray[0];
            var checkOutDate = checkOutArray[1];

            var date1 = new Date(checkInDate);
            var date2 = new Date(checkOutDate);

            // Calculate the difference in milliseconds
            var differenceMs = Math.abs(date2 - date1);

            // Convert the difference to days
            var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));

            var totalAmount = 0;
            var totTable = $("table[use=totalAmountTable]");
            $(totTable).children("tbody").find("tr").remove();
            var gst_flag = $('#gst_flag').val();
            var cloneIt = false;
            $("input[name=package_id]").each(function() {
                if ($(this).prop('checked') == true) {
                    var packageID = $(this).val();
                    var amount = ($(this).attr('amount'));
                    var amountIncludedDay = parseFloat(amount) * parseInt(differenceDays);
                    calculateTotalAmount();

                }
            });


        }

        function get_checkin_val(val) {
            if (typeof val !== 'undefined' && val != '') {
                var checkOutVal = $('#accomodation_package_checkout_id').val("");
            }
        }

        function getPackageVal(val) {
            if (typeof val !== 'undefined' && val != '') {

                var checkInVal = $('#accomodation_package_checkin_id').val("");
                var checkOutVal = $('#accomodation_package_checkout_id').val("");
                calculateTotalAmount();
            }
        }


        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
        $('#user_first_name').on('keyup', function() {
            // Get textbox value and remove non-alphabet characters
            var val = $(this).val().replace(/[^a-zA-Z]/g, '');
            // Set textbox value to cleaned-up value
            $(this).val(val);
        });

        $('#user_last_name').on('keyup', function() {
            // Get textbox value and remove non-alphabet characters
            var val = $(this).val().replace(/[^a-zA-Z]/g, '');
            // Set textbox value to cleaned-up value
            $(this).val(val);
        });

        $('#user_city').on('keyup', function() {
            // Get textbox value and remove non-alphabet characters
            var val = $(this).val().replace(/[^a-zA-Z]/g, '');
            // Set textbox value to cleaned-up value
            $(this).val(val);
        });
    </script>


</body>


</html>