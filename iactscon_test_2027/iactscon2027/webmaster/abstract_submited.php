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
                        <li class="breadcrumb-item"><a href="#">Abstract List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Submission Listing</li>
                    </ol>
                </nav>
                <h2>Submission Listing</h2>
                <h6>Issue ID cards, kits, and manage on-spot requests.</h6>
            </div>
            <div class="page_top_wrap_right">
                <?php
                $sqlFetch = array();
                $sqlFetch['QUERY'] = "SELECT count(abstractRequest.id) AS totalAbstractCount 
                                            FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest
                                             LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
															ON abstractRequest.applicant_id = registeredDelegates.id 
                                            WHERE abstractRequest.status = ?
                                                AND abstractRequest.tags = ?
                                                AND registeredDelegates.status = ?
                                               ";

                $sqlFetch['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                $sqlFetch['PARAM'][] = array('FILD' => 'tags', 'DATA' => 'Abstract', 'TYP' => 's');
                $sqlFetch['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                $result = $mycms->sql_select($sqlFetch);

                $sqlFetchAcc = array();
                $sqlFetchAcc['QUERY'] = "SELECT count(abstractRequest.id) AS totalAbstractCountAcc 
                                            FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest
                                             LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
											ON abstractRequest.applicant_id = registeredDelegates.id 
                                            WHERE abstractRequest.status = ?
                                             AND abstractRequest.abstract_result = ?
                                                AND abstractRequest.tags = ?
                                                AND registeredDelegates.status = ?
                                               ";

                $sqlFetchAcc['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                $sqlFetchAcc['PARAM'][] = array('FILD' => 'abstract_result', 'DATA' => 'ACCEPTED', 'TYP' => 's');
                $sqlFetchAcc['PARAM'][] = array('FILD' => 'tags', 'DATA' => 'Abstract', 'TYP' => 's');
                $sqlFetchAcc['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                $resultAcc = $mycms->sql_select($sqlFetchAcc);

                $sqlFetchRej = array();
                $sqlFetchRej['QUERY'] = "SELECT count(abstractRequest.id) AS totalAbstractCountRej 
                                             FROM " . _DB_ABSTRACT_REQUEST_ . " abstractRequest
                                             LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " registeredDelegates 
															ON abstractRequest.applicant_id = registeredDelegates.id 
                                            WHERE abstractRequest.status = ?
                                             AND abstractRequest.abstract_result = ?
                                                AND abstractRequest.tags = ?
                                                AND registeredDelegates.status = ?
                                               ";


                $sqlFetchRej['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                $sqlFetchRej['PARAM'][] = array('FILD' => 'abstract_result', 'DATA' => 'REJECTED', 'TYP' => 's');
                $sqlFetchRej['PARAM'][] = array('FILD' => 'tags', 'DATA' => 'Abstract', 'TYP' => 's');
                $sqlFetchRej['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                $resultRej = $mycms->sql_select($sqlFetchRej);
                ?>
                <p class="badge_primary"><?php user(); ?>Submitted: <b><?= $result[0]['totalAbstractCount'] ?? 0 ?></b>
                </p>
                <p class="badge_success"><?php paid(); ?>Accepted:
                    <b><?= $resultAcc[0]['totalAbstractCountAcc'] ?? 0 ?></b>
                </p>
                <p class="badge_danger"><?php unpaid(); ?>Rejected:
                    <b><?= $resultRej[0]['totalAbstractCountRej'] ?? 0 ?></b>
                </p>
            </div>
        </div>
        <?
        $searchString = "";
        $searchArray = array();

        // Collect all search parameters
        $searchParams = array(
            'src_registration_id',
            'src_full_name',
            'src_applicant_unique_sequence',
            'src_abstract_submission_code',
            'src_applicant_email_id',
            'src_classification_type',
            'src_abstract_category_id',
            'src_abstract_title',
            'src_abstract_award_id',
            'src_apply_from_date',
            'src_apply_to_date',
            'src_paper_presentation_category',
            'src_abstract_topic_id',
            'src_presentation_type',
            'src_type',
            'src_allocated'
        );

        foreach ($searchParams as $param) {
            if (isset($_REQUEST[$param]) && $_REQUEST[$param] != '') {
                $searchArray[$param] = $_REQUEST[$param];
                $searchString .= "&" . $param . "=" . urlencode($_REQUEST[$param]);
            }
        }

        // Also handle the search query from the search box
        if (!empty($_GET['q'])) {
            $searchString .= "&q=" . urlencode($_GET['q']);
        }
        ?>
        <div class="regi_search_wrap mb-3">
            <!-- <div class="regi_search">
                <?php search(); ?>
                <input id="abstractSearchInput" placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div> -->
            <div class="regi_search">
                <input id="searchInput" name="q" placeholder="Search by Name, Email, Mobile, or Reg ID..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)"
                    onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="download_abstract_excel.php?show=excel<?= $searchString ?>"><?php export(); ?>Export</a>
                <a href="download_abstract_excel.php?show=meritList<?= $searchString ?>"><?php export(); ?>Export Merit
                    List</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter"
                    onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <form name="frmSearch" method="post" action="abstract_submited.php"
                onSubmit="return FormValidator.validate(this);">
                <input type="hidden" name="act" value="search_registration" />
                <div class="filter_body">
                    <div class="filter_div">
                        <label>Applicant Name:</label>
                        <input type="text" name="src_full_name" id="src_full_name"
                            value="<?= $_REQUEST['src_full_name'] ?>" />
                    </div>
                    <div class="filter_div">
                        <label>Email Id:</label>
                        <input type="text" name="src_applicant_email_id" id="src_applicant_email_id"
                            value="<?= $_REQUEST['src_applicant_email_id'] ?>" />
                    </div>
                    <div class="filter_div">
                        <label>Unique Sequence:</label>
                        <input type="text" name="src_applicant_unique_sequence" id="src_applicant_unique_sequence"
                            value="<?= $_REQUEST['src_applicant_unique_sequence'] ?>" />
                    </div>
                    <div class="filter_div">
                        <label>Applicant Registration Id:</label>
                        <input type="text" name="src_registration_id" id="src_registration_id"
                            value="<?= $_REQUEST['src_registration_id'] ?>" />
                    </div>
                    <div class="filter_div">
                        <label>Submission Code:</label>
                        <input type="text" name="src_abstract_submission_code" id="src_abstract_submission_code"
                            value="<?= $_REQUEST['src_abstract_submission_code'] ?>" />
                    </div>
                    <div class="filter_div">
                        <label>User Classification Type:</label>
                        <select name="src_classification_type" id="src_classification_type">
                            <option value="">-- Select Registration Classificaiton Type --</option>
                            <?php
                            $sqlClassification = array();
                            $sqlClassification['QUERY'] = "SELECT * 
                                                                    FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
                                                                    WHERE `status` = ? AND `type` = ?
                                                                ORDER BY `classification_title` ASC";

                            $sqlClassification['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                            $sqlClassification['PARAM'][] = array('FILD' => 'type', 'DATA' => 'DELEGATE', 'TYP' => 's');

                            $resultClassificationTitle = $mycms->sql_select($sqlClassification);

                            if ($resultClassificationTitle) {
                                foreach ($resultClassificationTitle as $keyClassificationTitle => $rowClassificationTitle) {
                                    ?>
                                    <option value="<?= $rowClassificationTitle['id'] ?>"
                                        <?= ($rowClassificationTitle['id'] == $_REQUEST['src_classification_type']) ? 'selected="selected"' : '' ?>><?= $rowClassificationTitle['classification_title'] ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter_div">
                        <label>Abstract Submission Category:</label>
                        <?php
                        $sqlAbstractCat = array();
                        $sqlAbstractCat['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                                    WHERE `status` = 'A'
                                                                ORDER BY `id` ASC";

                        $resultAbstractCat = $mycms->sql_select($sqlAbstractCat);
                        ?>
                        <select name="src_abstract_category_id" id="src_abstract_category_id">
                            <option value="">-- Select Category --</option>
                            <?php
                            foreach ($resultAbstractCat as $key => $value) {
                                ?>
                                <option value="<?= $value['id'] ?>" <?php if ($value['id'] == $_REQUEST['src_abstract_category_id']) {
                                      echo 'selected';
                                  } ?>><?= $value['category'] ?></option>
                                <?php
                            }
                            ?>

                        </select>
                    </div>
                    <div class="filter_div">
                        <label>Abstract Title:</label>
                        <input type="text" name="src_abstract_title" id="src_abstract_title"
                            value="<?= $_REQUEST['src_abstract_title'] ?>" />
                    </div>
                    <div class="filter_div">
                        <label>Nomination:</label>
                        <select name="src_abstract_award_id" id="src_abstract_award_id">
                            <option value="">-- Select Nomination --</option>
                            <?php
                            $sqlAbstractSubcat = array();
                            $sqlAbstractSubcat['QUERY'] = "SELECT * 
                                                            FROM " . _DB_AWARD_MASTER_ . " 
                                                            WHERE `status` = ? 
                                                        ORDER BY `id` ASC";

                            $sqlAbstractSubcat['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                            $resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);

                            if ($resultAbstractSubcat) {
                                foreach ($resultAbstractSubcat as $keyAbstractTopic => $rowAbstractTopic) {
                                    ?>
                                    <option value="<?= $rowAbstractTopic['id'] ?>"
                                        <?= ($rowAbstractTopic['id'] == $_REQUEST['src_abstract_award_id']) ? 'selected="selected"' : '' ?>><?= $rowAbstractTopic['award_name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                </div>
                <div class="filter_bottom span_4">
                    <button type="button" name="" value="Clear"
                        onClick="window.location.href='abstract_submited.php'"><?php reseti(); ?></button>
                    <button type="submit" name="goSearch" value="Search">Apply</button>
                </div>
            </form>
        </div>

        <div class="spot_listing" id="spotListing">
            <?
            $searchCondition = "";
            if ($_REQUEST['src_registration_id'] != "") {
                $searchCondition .= " AND registeredDelegates.user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "%'";
            }
            if ($_REQUEST['src_full_name'] != "") {
                $searchCondition .= " AND registeredDelegates.user_full_name LIKE '%" . $_REQUEST['src_full_name'] . "%'";
            }
            if ($_REQUEST['src_applicant_unique_sequence'] != "") {
                $searchCondition .= " AND registeredDelegates.user_unique_sequence LIKE '%" . $_REQUEST['src_applicant_unique_sequence'] . "%'";
            }
            if ($_REQUEST['src_abstract_submission_code'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_submition_code LIKE '%" . $_REQUEST['src_abstract_submission_code'] . "%'";
            }
            if ($_REQUEST['src_applicant_email_id'] != "") {
                $searchCondition .= " AND registeredDelegates.user_email_id LIKE '%" . $_REQUEST['src_applicant_email_id'] . "%'";
            }
            // user registration classification type filter by weavers start
            if ($_REQUEST['src_classification_type'] != "") {
                $searchCondition .= " AND registeredDelegates.registration_classification_id = '" . $_REQUEST['src_classification_type'] . "'";
            }
            // user registration classification type filter by weavers end
            if ($_REQUEST['src_apply_from_date'] != "" && $_REQUEST['src_apply_to_date'] == "") {
                $searchCondition .= " AND abstractRequest.created_dateTime BETWEEN '" . $_REQUEST['src_apply_from_date'] . "'  AND '3000-01-01'";
            }
            if ($_REQUEST['src_apply_from_date'] == "" && $_REQUEST['src_apply_to_date'] != "") {
                $searchCondition .= " AND abstractRequest.created_dateTime BETWEEN '2000-01-01'  AND '" . $_REQUEST['src_apply_to_date'] . "'";
            }
            if ($_REQUEST['src_apply_from_date'] != "" && $_REQUEST['src_apply_to_date'] != "") {
                $searchCondition .= " AND abstractRequest.created_dateTime BETWEEN '" . $_REQUEST['src_apply_from_date'] . "'  AND '" . $_REQUEST['src_apply_to_date'] . "'";
            }
            if ($_REQUEST['src_paper_presentation_category'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_child_type = '" . $_REQUEST['src_paper_presentation_category'] . "'";
            }
            if ($_REQUEST['src_abstract_category'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_category = '" . $_REQUEST['src_abstract_category'] . "'";
            }
            if ($_REQUEST['src_abstract_topic_id'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_topic_id = '" . $_REQUEST['src_abstract_topic_id'] . "'";
            }
            if ($_REQUEST['src_presentation_type'] != "") {
                //$searchCondition          .= " AND ( (abstractRequest.abstract_child_type = '".$_REQUEST['src_presentation_type']."' AND abstractRequest.abstract_presentation_decision = 'DEFAULT')
                $searchCondition .= " AND ( (abstractRequest.abstract_cat = '" . $_REQUEST['src_presentation_type'] . "' AND abstractRequest.abstract_presentation_decision = 'DEFAULT')
                                                                    OR abstractRequest.abstract_presentation_decision = '" . $_REQUEST['src_presentation_type'] . "')";
            }
            if ($_REQUEST['src_abstract_award_id'] != "") {
                if ($_REQUEST['src_abstract_award_id'] == 'NULL') {
                    $searchCondition .= " AND abstractRequest.id NOT IN ( SELECT submission_id FROM " . _DB_AWARD_REQUEST_ . " WHERE status = 'A' )";
                } else {
                    $searchCondition .= " AND abstractRequest.id IN ( SELECT submission_id FROM " . _DB_AWARD_REQUEST_ . " WHERE award_id = '" . $_REQUEST['src_abstract_award_id'] . "' AND status = 'A' )";
                }
            }
            if ($_REQUEST['src_type'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_parent_type = '" . $_REQUEST['src_type'] . "'";
            }
            if ($_REQUEST['src_allocated'] != "" && $_REQUEST['src_allocated'] == 'Y') {
                $searchCondition .= " AND abstractRequest.id IN ( SELECT abstract_id FROM " . _DB_ABSTRACT_ALLOTMENT_ . " )";
            }
            if ($_REQUEST['src_allocated'] != "" && $_REQUEST['src_allocated'] == 'N') {
                $searchCondition .= " AND abstractRequest.id NOT IN ( SELECT abstract_id FROM " . _DB_ABSTRACT_ALLOTMENT_ . " )";
            }
            if ($_REQUEST['src_abstract_title'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_title LIKE '%" . $_REQUEST['src_abstract_title'] . "%'";
            }

            if ($_REQUEST['src_abstract_category_id'] != "") {
                $searchCondition .= " AND abstractRequest.abstract_cat ='" . $_REQUEST['src_abstract_category_id'] . "'";
            }
            if ($_REQUEST['src_abstract_subcategory_id'] != "") {
                $searchCondition .= " AND abstractTopic.sub_category = '" . $_REQUEST['src_abstract_subcategory_id'] . "'";
            }
            if (!empty($_GET['q'])) {

                $search = trim($_GET['q']);
                $search = addslashes($search); // (works, but see note below)
            
                $searchCondition = " AND (
                        registeredDelegates.user_first_name LIKE '%$search%' OR
                         registeredDelegates.user_full_name LIKE '%$search%' OR
                        registeredDelegates.user_middle_name LIKE '%$search%' OR
                        registeredDelegates.user_last_name LIKE '%$search%' OR
                        registeredDelegates.user_email_id LIKE '%$search%' OR
                        registeredDelegates.user_mobile_no LIKE '%$search%' OR
                        registeredDelegates.user_unique_sequence LIKE '%$search%' OR
                        abstractRequest.abstract_submition_code LIKE '%$search%'
                    )";
            }

            $abstractCounter = 0;

            $sqlAbstractDetails = abstractDetailsQuerySet("", $searchCondition);




            $resultAbstractDetails = $mycms->sql_select_paginated(1, $sqlAbstractDetails, 50, $restrt);
            $perPage = 50; // IMPORTANT: must match SQL LIMIT
            
            $pageIndex = isset($_GET['_pgn1_'])
                ? (int) $_GET['_pgn1_']
                : 0;

            $offset = $pageIndex * $perPage;
            //   echo "<pre>"; print_r($resultAbstractDetails	);die;
            if ($resultAbstractDetails) {
                $totalAvg = '';
                $totalMarks = 0;
                $totalReviewer = 0;
                foreach ($resultAbstractDetails as $i => $rowAbstractDetails) {
                    $abstractCounter++;

                    $abstractDetailsArray = getAbstractDetailsArray($rowAbstractDetails['id']);
                    $abstractId = $rowAbstractDetails['id'];
                    $abstractCurrentAllotedUsers = getAbstractAllotedUserIds($abstractId);

                    // echo '<pre>'; print_r($abstractDetailsArray); echo '<pre/>'; 
            
                    $rowStyleDecission = "";

                    if ($abstractDetailsArray['MARKS']['HAS_A_UNABLE'] == 'YES') {
                        $rowStyleDecission = " style='background-color: #FF28FF;'";
                    } elseif ($abstractDetailsArray['MARKS']['REVIEW_COUNT'] > 0) {
                        $rowStyleDecission = " style='background-color: #DDF1D6;'";
                    } else {
                        $rowStyleDecission = " style='background-color: #FFFFFF;'";
                    }

                    $abstractColor['ABSTRACT'] = "red";
                    $abstractColor['CASEREPORT'] = "blue";

                    $presentationColor['ORAL'] = "red";
                    $presentationColor['POSTER'] = "blue";
                    $presentationColor['VIDEO'] = "orange";

                    $isRegistered = true;

                    $hasEmail = false;
                    $sqlListing['QUERY'] = "SELECT id
                                                                    FROM " . _DB_ABSTRACT_COMMUNICATION_MAIL_ . " 
                                                                WHERE `abstractId` = '" . $rowAbstractDetails['id'] . "'";
                    $resultsListing = $mycms->sql_select($sqlListing);
                    if ($resultsListing) {
                        $hasEmail = true;
                    }
                    $sqlClassification = array();
                    $sqlClassification['QUERY'] = "SELECT * 
                                                                FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
                                                                WHERE `status` = ? AND `type` = ? AND `id` = ?";

                    $sqlClassification['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                    $sqlClassification['PARAM'][] = array('FILD' => 'type', 'DATA' => 'DELEGATE', 'TYP' => 's');
                    $sqlClassification['PARAM'][] = array('FILD' => 'id', 'DATA' => $rowAbstractDetails['registration_classification_id'], 'TYP' => 's');

                    $resultClassificationTitle = $mycms->sql_select($sqlClassification);


                    $sqlReview = array();
                    $sqlReview['QUERY'] = " SELECT SUM(R.`marks_obtained`) MARKS, COUNT(R.`faculty_id`) COUNTDATA
                                                                        FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " R INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " F ON R.faculty_id = F.id
                                                                        WHERE R.abstract_id = '" . $rowAbstractDetails['id'] . "' AND R.status='A' AND F.status='A'";

                    $resultReview = $mycms->sql_select($sqlReview);

                    // echo '<pre>'; print_r($resultClassificationTitle[0]);
            
                    $totalMarks = $resultReview[0]['MARKS'];
                    $totalReviewer = $resultReview[0]['COUNTDATA'];


                    if (!empty($totalMarks) && $totalReviewer > 0) {
                        $totalAvg = floatval($totalMarks / $totalReviewer);
                    }
                    ?>
                    <div class="spot_box">
                        <div class="spot_box_top">
                            <div class="spot_name d-flex align-items-start">
                                <div class="regi_img_circle">
                                    <!-- <img src="" alt="" class="w-100 h-100"> -->
                                    <span><?= $abstractCounter + ($_REQUEST['_pgn1_'] * 50) ?></span>
                                </div>
                                <div>
                                    <div class="regi_name"><?= $rowAbstractDetails['user_full_name'] ?></div>
                                    <?php
                                    if (
                                        $rowAbstractDetails['registration_payment_status'] == "PAID"
                                        || $rowAbstractDetails['registration_payment_status'] == "COMPLIMENTARY"
                                        || $rowAbstractDetails['registration_payment_status'] == "ZERO_VALUE"
                                    ) {
                                        ?>
                                        <div class="regi_type">

                                            <span
                                                class="badge_padding badge_primary"><?= $resultClassificationTitle[0]['classification_title'] ?></span>
                                            <?php if (getCutoffName($rowAbstractDetails['registration_tariff_cutoff_id'])) { ?>
                                                <span class="badge_padding badge_secondary">
                                                    <?php
                                                    echo '<span style="color: #8ee0f5; text-transform: uppercase;" title="Cutoff">' . getCutoffName($rowAbstractDetails['registration_tariff_cutoff_id']) . '</span>';

                                                    ?></span>
                                            <?php } ?>
                                        </div>
                                    <? } else {
                                        ?>
                                        <div class="regi_type">
                                            <span class="badge_padding badge_primary" style="color: #FF0000;!important">Not
                                                Registered Yet</span>
                                        </div>
                                        <?
                                    }
                                    ?>
                                    <div class="regi_contact">
                                        <span>
                                            <?php call(); ?>         <?= $rowAbstractDetails['user_mobile_no'] ?>
                                        </span>
                                        <span>
                                            <?php email(); ?>         <?= $rowAbstractDetails['user_email_id'] ?>
                                        </span>
                                        <?php

                                        ?>
                                        <span><?php qr() ?><?= strtoupper($rowAbstractDetails['user_unique_sequence']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="spot_details">
                                <div class="spot_details_box abstract_details_box flex3">
                                    <ol>
                                        <li class="badge_info">
                                            <span>Submision Code</span>
                                            <p><?= $rowAbstractDetails['abstract_submition_code'] ?></p>
                                        </li>
                                        <li class="badge_partial">
                                            <span>Submission Date</span>
                                            <p><?= date('d/m/Y h:i A', strtotime($rowAbstractDetails['created_dateTime'])) ?>
                                            </p>
                                        </li>
                                    </ol>
                                    <ul>
                                        <li class="span_2">
                                            <span>Category</span>
                                            <p><?= getCategoryName($rowAbstractDetails['abstract_cat']) ?></p>
                                        </li>
                                        <li class="span_2">
                                            <span>Sub Category 1</span>
                                            <?php
                                            $sqlAbstractTopicSub = array();
                                            $sqlAbstractTopicSub['QUERY'] = "SELECT `abstract_submission` FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                                                                    WHERE `status` ='A'
                                                                AND id='" . $rowAbstractDetails['abstract_parent_type'] . "'";


                                            $resultAbstractTopicSub = $mycms->sql_select($sqlAbstractTopicSub);
                                            ?>
                                            <p><?= $resultAbstractTopicSub[0]['abstract_submission'] ?></p>
                                        </li>
                                        <li class="span_2">
                                            <span>Sub Category 2</span>
                                            <?php
                                            $sqlAbstractTopicSub1 = array();
                                            $sqlAbstractTopicSub1['QUERY'] = "SELECT `abstract_presentation` FROM " . _DB_ABSTRACT_PRESENTATION_ . " 
                                                                WHERE `status` ='A'
                                                            AND id='" . $rowAbstractDetails['abstract_child_type'] . "'";


                                            $resultAbstractTopicSub1 = $mycms->sql_select($sqlAbstractTopicSub1);
                                            ?>
                                            <p><?= $resultAbstractTopicSub1[0]['abstract_presentation'] ?></p>
                                        </li>
                                        <li class="span_6">
                                            <span>Topic</span>
                                            <p><?= $rowAbstractDetails['abstract_topic'] ?></p>
                                        </li>
                                        <li class="span_6">
                                            <span>Title</span>
                                            <p><?= $rowAbstractDetails['abstract_title'] ?></p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="spot_details_box abstract_details_box">
                                     <?php
                                if (true || $abstractDetailsArray['MARKS']['REVIEW_COUNT'] > 0) {
                                  
                                    ?>
                                    <form class="abstrat_review" use='settingAbstractResult' action="abstract.free_papers.process.php" method="post">
                                        <input type="hidden" name="act" value="updateResult" />
                                        <input type="hidden" name="abstractId" value="<?= $rowAbstractDetails['id'] ?>" />
                                        <?php
                                        foreach ($searchArray as $key => $val) {
                                            ?>
                                            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
                                            <?php
                                        }
                                        ?>
                                        <?    if (!empty($totalMarks) && $totalReviewer > 0) { ?>
                                          <h5>Result :</h5> <?
                                            echo "<span style='font-size:10px;'>" . $totalMarks . "/" . $totalReviewer . "= " . $totalAvg ."</span>" ;
                                        } ?>
                                          <h5>Review Result</h5>
                                       
                                            <select name="abstract_result" onchange="updateAbstractResult(this);"
                                                abstractId="<?= $rowAbstractDetails['id'] ?>">
                                                <option value="NOT-DECIDED"
                                                    <?= ($rowAbstractDetails['abstract_result'] == 'NOT-DECIDED') ? "selected" : "" ?>>
                                                    NOT-DECIDED</option>
                                                <option value="ACCEPTED" <?= ($rowAbstractDetails['abstract_result'] == 'ACCEPTED') ? "selected" : "" ?>>ACCEPTED</option>
                                                <option value="REJECTED" <?= ($rowAbstractDetails['abstract_result'] == 'REJECTED') ? "selected" : "" ?>>REJECTED</option>
                                            </select>
                                       
                                    </form>
                                    <form class="abstrat_review" use='settingAbstractPresentationDecision' action="abstract.free_papers.process.php"
                                        method="post">
                                        <input type="hidden" name="act" value="updatePresentationDecision" />
                                        <input type="hidden" name="abstractId" value="<?= $rowAbstractDetails['id'] ?>" />
                                        <?php
                                        foreach ($searchArray as $key => $val) {
                                            ?>
                                            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
                                            <?php
                                        }
                                        ?>
                                         <h5>Presentation Mode</h5>
                                        
                                            <select name="abstract_presentation_decision"
                                                onchange="updatePresentationDecision(this);"
                                                abstractId="<?= $rowAbstractDetails['id'] ?>">
                                                <option value="DEFAULT"
                                                    <?= ($rowAbstractDetails['abstract_presentation_decision'] == 'DEFAULT') ? "selected" : "" ?>>DEFAULT</option>
                                                <option value="ORAL"
                                                    <?= ($rowAbstractDetails['abstract_presentation_decision'] == 'ORAL') ? "selected" : "" ?>>ORAL</option>
                                                <option value="POSTER"
                                                    <?= ($rowAbstractDetails['abstract_presentation_decision'] == 'POSTER') ? "selected" : "" ?>>POSTER</option>
                                                <option value="PAPER"
                                                    <?= ($rowAbstractDetails['abstract_presentation_decision'] == 'PAPER') ? "selected" : "" ?>>PAPER</option>
                                                <?/*<option value="VIDEO" <?=($rowAbstractDetails['abstract_presentation_decision']=='VIDEO')?"selected":""?>>VIDEO</option>*/ ?>
                                            </select>
                                      
                                    </form>
                                    <?
                                } else {
                                    ?>
                                    <h6><span>Not Reviewed</span>
                                        <!-- <i class="fa fa-eye-slash" aria-hidden="true" style="color:#f53f3f;" title="Not Reviewed"></i> -->
                                        <?php
                                }
                                ?>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="spot_box_bottom accm_bottom justify-content-end">

                            <div class="spot_box_bottom_right">
                                <a href="#" data-tab="abstract_edit" data-user-id="<?= $rowAbstractDetails['id'] ?>"
                                    class="popup-btn badge_secondary icon_hover action-transparent editbtn"><?php edit(); ?>Edit</a>
                                <a href="invoice_send_mail_abstract.php?show=sendMail&id=<?= $rowAbstractDetails['id'] ?>&applicantId=<?= $rowAbstractDetails['applicant_id'] ?>"
                                    target="_blank" class="badge_info icon_hover action-transparent"><?php pplane(); ?>Send
                                    Mail</a>
                                <a href="abstract.free_papers.process.php?act=RemoveAbstract&id=<?= $rowAbstractDetails['id'] ?>"
                                    onclick="return confirm('Do you really want to remove this record?')"
                                    class="badge_danger icon_hover action-transparent"><?php delete(); ?>Delete</a>
                                <a href="msdoc.abstract.download.php?id=<?= $rowAbstractDetails['id'] ?>&operation=print"
                                    target="_blank" style="float:right;">
                                    <i class="fa fa-print" aria-hidden="true"></i>Print</a>
                                <a href="msdoc.abstract.download.php?id=<?= $rowAbstractDetails['id'] ?>&operation=docdownload"
                                    style="float:right;">
                                    <i class="fa fa-download" aria-hidden="true"></i> Download</a><? ?>
                                <a href="#" class="drp icon_hover badge_dark action-transparent">Reviewer<?php down() ?></a>
                            </div>
                        </div>
                        <div class="accm_tariff spot_service_break">
                            <div class="service_breakdown_wrap mt-0">
                                <h4><?php duser(); ?>Allocate Reviewer</h4>
                                <ul class="service_breakdown_wrap_ul">
                                    <?php
                                    $counter = 0;
                                    $searchCondition = " AND grandTAB.id != '1'";

                                    $sqlFacultyDetails = array();
                                    $sqlFacultyDetails['QUERY'] = "SELECT faculty.*,
																   
																   IFNULL(faculty.faculty_title, '') AS facultyTitle,
																   IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
																   IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
																   IFNULL(faculty.faculty_last_name, '') AS facultyLastName,
																   faculty.id
															  
															  FROM " . _DB_FACULTY_ACCOUNT_ . " faculty 
															 
															 WHERE faculty.status = 'A'";

                                    $resultFacultyDetails = $mycms->sql_select($sqlFacultyDetails);
                                    if ($resultFacultyDetails) {
                                        foreach ($resultFacultyDetails as $i => $rowFacultyDetails) {
                                            $counter++;

                                            $userCurrAllotmentsIds = getReviewUserAllotedAbstractIds($rowFacultyDetails['id']);

                                            $currentAllotments = array();

                                            foreach ($userCurrAllotmentsIds as $ii => $abstractIdr) {
                                                $sqlAbstractDetails = abstractDetailsQuerySet($abstractIdr, '');
                                                $resultAbstractDetails = $mycms->sql_select($sqlAbstractDetails);
                                                $rowAbstractDetails = $resultAbstractDetails[0];

                                                $abstractContent = getAbstractProofContent($abstractIdr, '../../');

                                                $currentAllotments[] = "Sub. Code : " . $rowAbstractDetails['abstract_submition_code'] . ";"; //<br> Topic : ".$rowAbstractDetails['abstract_topic']."<br> Title : ".$abstractContent['TITLE'];
                                            }

                                            ?>
                                            <li>
                                                <n>
                                                    <j><?php
                                                    echo $rowFacultyDetails['faculty_title'] . " " . $rowFacultyDetails['faculty_first_name'] . " " . $rowFacultyDetails['faculty_middle_name'] . " " . $rowFacultyDetails['faculty_last_name'] . " ";
                                                    ?></j>
                                                </n>
                                                <g>
                                                    <label class="toggleswitch ">
                                                        <input class="toggleswitch-checkbox reviewer-toggle" type="checkbox"
                                                            data-abstract-id="<?= $abstractId ?>" <?= in_array($rowFacultyDetails['id'], $abstractCurrentAllotedUsers) ? "checked" : "" ?>
                                                            value="<?= $rowFacultyDetails['id'] ?>">
                                                        <div class="toggleswitch-switch"></div>
                                                    </label>
                                                </g>
                                            </li>
                                        <? }
                                    } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <? }
            } else {
                ?>
                <span class="mandatory" align="center">No Record Present.</span>
                <?php
            }
            ?>
        </div>
        <div class="bbp-pagination">
            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
            <span class="paginationDisplay">
                <div class="pagination"><a><?= $mycms->paginate(1, 'pagination') ?></a></div>
            </span>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    function updateAbstractResult(obj) {
        console.log("trigger updateAbstractResult");
        var parent = $(obj).parent().closest("form");
        $(parent).submit();
    }

    function updatePresentationDecision(obj) {
        //alert(12);
        console.log(obj);
        console.log("trigger updatePresentationDecision");

        var parent = $(obj).parent().closest("form");
        $(parent).submit();
    }

    $(document).ready(function () {

        $('form[use=settingAbstractResult]').submit(function (e) {
            e.preventDefault();

            var parent = $(this);
            var path = $(parent).attr("action");

            console.log("hitting >> " + path);

            $.ajax({
                type: "POST",
                url: path,
                data: $(parent).serialize(),
                dataType: "text",
                async: false,
                success: function (JSONObject) {
                    console.log("ran updateAbstractResult");
                    alert('Data updated successfully');
                    window.location.reload();
                }
            });
        });

        $('form[use=settingAbstractPresentationDecision]').submit(function (e) {
            e.preventDefault();

            var parent = $(this);
            var path = $(parent).attr("action");

            console.log("hitting >> " + path);

            $.ajax({
                type: "POST",
                url: path,
                data: $(parent).serialize(),
                dataType: "text",
                async: false,
                success: function (JSONObject) {
                    console.log("ran updatePresentationDecision");
                    alert('Data updated successfully');
                    window.location.reload();
                }
            });
        });
        $('form[use=settingInspectionTime]').submit(function (e) {
            e.preventDefault();

            var parent = $(this);
            var path = $(parent).attr("action");

            console.log("hitting >> " + path);

            $.ajax({
                type: "POST",
                url: path,
                data: $(parent).serialize(),
                dataType: "text",
                async: false,
                success: function (JSONObject) {
                    console.log("ran settingInspectionTime");
                    alert('Data updated successfully');
                    window.location.reload();
                }
            });
        });
    });
    $(document).on('click', '.editbtn', function () {
        let userId = $(this).data('user-id');
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                editabstractId: userId,
            },
            success: function (response) {
                $('#abstract_edit').html($(response).find('#abstract_edit').html());
                // Then show the popup
                initabstractedit();

                $('#abstract_edit').fadeIn();
            },
            error: function (xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
    });
    $(document).on('change', '.reviewer-toggle', function () {

        let $this = $(this);
        let reviewerId = $this.val();
        let abstractId = $this.data('abstract-id');
        let status = $this.is(':checked') ? 1 : 0;

        // Ask user before submitting
        let confirmMsg = status
            ? "Are you sure you want to assign this reviewer?"
            : "Are you sure you want to remove this reviewer?";

        if (!confirm(confirmMsg)) {
            // User clicked Cancel → revert toggle
            $this.prop('checked', !status);
            return; // stop AJAX
        }

        // 🔒 prevent spam clicking
        $this.prop('disabled', true);

        // Proceed with AJAX only if user confirms
        $.ajax({
            type: "POST",
            url: "abstract.free_papers.process.php",
            data: {
                act: 'allocateAbstract',
                reviewerId: reviewerId,
                abstractId: abstractId,
                status: status
            },
            success: function (res) {
                alert("Reviewer updated successfully!");
            },
            error: function () {
                alert("Error updating reviewer");
                // revert toggle on error
                $this.prop('checked', !status);
            },
            complete: function () {
                $this.prop('disabled', false);
            }
        });


    });

</script>
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const searchInput = document.getElementById('abstractSearchInput');
    //     const abstracts = document.querySelectorAll('.spot_box');

    //     searchInput.addEventListener('input', function() {
    //         const query = this.value.toLowerCase();

    //         abstracts.forEach(box => {
    //             const text = box.innerText.toLowerCase();
    //             if (text.includes(query)) {
    //                 box.style.display = ''; // show
    //             } else {
    //                 box.style.display = 'none'; // hide
    //             }
    //         });
    //     });
    // });

    const searchInput = document.getElementById("searchInput");
    let typingTimer;
    const typingDelay = 800; // wait 0.8s after last keystroke

    searchInput.addEventListener("keyup", function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(() => {
            const query = this.value.trim();

            const url = new URL(window.location.href);

            if (query.length > 0) {
                url.searchParams.set('q', query);
            } else {
                url.searchParams.delete('q');
            }

            url.searchParams.delete('_pgn1_'); // reset pagination
            window.location.href = url.toString(); // reload page with new query
        }, typingDelay);
    });

    searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));
</script>

</html>
<?
function getCategoryName($id)
{
    global $cfg, $mycms;

    $sqlSelectUser = array();
    $sqlSelectUser['QUERY'] = "SELECT `category` 
												   FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . "
												 WHERE `id` = '" . $id . "' AND status='A'";



    $resultSelectUser = $mycms->sql_select($sqlSelectUser);

    //echo '<pre>'; print_r($resultSelectUser);
    return $resultSelectUser[0]['category'];
}
?>