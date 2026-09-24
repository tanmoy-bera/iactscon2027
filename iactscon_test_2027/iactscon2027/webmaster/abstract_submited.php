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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/manage_additional_data.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
	<script type="text/javascript" language="javascript" src="section_login/scripts/CountryStateRetriver.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/dinner_registration.js"></script>
    <script type="text/javascript" language="javascript" src="scripts/accompany_registration.js"></script>
     <script>
    var jsBASE_URL	= "<?=_BASE_URL_?>";
    var jsWemaster_BASE_URL	= "<?=$cfg['SECTION_BASE_URL']?>";
    var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
         var jsSectionBaseURL	= "<?=$cfg['SECTION_BASE_URL']?>";

        </script>
<body>
     <style>
        .form-control:disabled, .form-control[readonly] {
            background-color: #2c2c2c!important;
            opacity: 1;
        }
    </style>
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

                            // PERF: build a lookup map once here so the row loop below
                            // never has to re-query the classification table per row.
                            $classificationMap = array();
                            if ($resultClassificationTitle) {
                                foreach ($resultClassificationTitle as $rowClassMap) {
                                    $classificationMap[$rowClassMap['id']] = $rowClassMap['classification_title'];
                                }
                            }

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
                        <label>Abstract Topic:</label>
                        <?php
                        $sqlAbstractTopic = array();
                        $topicCategoryFilter = "";
                        if (!empty($_REQUEST['src_abstract_category_id'])) {
                            $topicCategoryFilter = " AND `category` = '" . intval($_REQUEST['src_abstract_category_id']) . "'";
                        }
                        $sqlAbstractTopic['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                                    WHERE `status` = 'A'" . $topicCategoryFilter . "
                                                                                ORDER BY `id` ASC";

                        $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
                        ?>
                        <select name="src_abstract_topic_id" id="src_abstract_topic_id">
                            <option value="">-- Select Topic --</option>
                            <?php
                            foreach ($resultAbstractTopic as $keyAbstractTopic => $rowAbstractTopic) {
                                ?>
                                <option value="<?= $rowAbstractTopic['id'] ?>" <?php if ($rowAbstractTopic['id'] == $_REQUEST['src_abstract_topic_id']) {
                                      echo 'selected';
                                  } ?>><?= $rowAbstractTopic['abstract_topic'] ?></option>
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
                    <div class="filter_div">
                        <label>Reviewer Allocated:</label>
                        <select name="src_allocated" id="src_allocated">
                            <option value="">-- Select Option --</option>
                            <option value="Y"  <?= ('Y' == $_REQUEST['src_allocated']) ? 'selected="selected"' : '' ?>>Yes</option>
                            <option value="N"  <?= ('N' == $_REQUEST['src_allocated']) ? 'selected="selected"' : '' ?>>No</option>
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

            /* =====================================================================
             * PERFORMANCE: pre-fetch data ONCE for the whole page instead of
             * re-querying it inside the per-row loop below (which was previously
             * running dozens of extra queries for every single row on the page).
             * ===================================================================== */
            $reviewMarksMap = array();
            $awardMap = array();
            $resultFacultyDetails = array();

            if ($resultAbstractDetails) {

                // Collect all abstract ids shown on this page
                $pageAbstractIds = array();
                foreach ($resultAbstractDetails as $rowIdCollect) {
                    $pageAbstractIds[] = (int) $rowIdCollect['id'];
                }

                if (!empty($pageAbstractIds)) {
                    $idsList = implode(",", $pageAbstractIds);

                    // Batched review marks (replaces one SUM/COUNT query per row)
                    $sqlReviewBatch = array();
                    $sqlReviewBatch['QUERY'] = "SELECT R.abstract_id AS abstract_id,
                                                        SUM(R.marks_obtained) AS MARKS,
                                                        COUNT(R.faculty_id) AS COUNTDATA
                                                   FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " R
                                                   INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " F ON R.faculty_id = F.id
                                                  WHERE R.abstract_id IN (" . $idsList . ")
                                                    AND R.status = 'A'
                                                    AND F.status = 'A'
                                               GROUP BY R.abstract_id";
                    $resultReviewBatch = $mycms->sql_select($sqlReviewBatch);
                    if ($resultReviewBatch) {
                        foreach ($resultReviewBatch as $rowReviewBatch) {
                            $reviewMarksMap[$rowReviewBatch['abstract_id']] = array(
                                'MARKS' => $rowReviewBatch['MARKS'],
                                'COUNTDATA' => $rowReviewBatch['COUNTDATA'],
                            );
                        }
                    }

                    // Batched nomination/award lookup (replaces one query per row)
                    $sqlAwardBatch = array();
                    $sqlAwardBatch['QUERY'] = "SELECT * 
                                                  FROM " . _DB_AWARD_REQUEST_ . " 
                                                 WHERE `status` = 'A' 
                                                   AND `submission_id` IN (" . $idsList . ")
                                              ORDER BY `id` ASC";
                    $resultAwardBatch = $mycms->sql_select($sqlAwardBatch);
                    if ($resultAwardBatch) {
                        foreach ($resultAwardBatch as $rowAwardBatch) {
                            $awardMap[$rowAwardBatch['submission_id']][] = $rowAwardBatch;
                        }
                    }
                }

                // Faculty list is identical for every row - fetch once, not per row.
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
            }

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

                    // PERF: classification title now comes from the map built once
                    // above (near the filter dropdown) instead of a per-row query.
                    $resultClassificationTitle = array(
                        0 => array(
                            'classification_title' => $classificationMap[$rowAbstractDetails['registration_classification_id']] ?? ''
                        )
                    );

                    // PERF: review marks now come from the batched map built once
                    // above instead of a per-row SUM/COUNT query.
                    $totalMarks = $reviewMarksMap[$rowAbstractDetails['id']]['MARKS'] ?? null;
                    $totalReviewer = $reviewMarksMap[$rowAbstractDetails['id']]['COUNTDATA'] ?? 0;

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
                                       if ($rowAbstractDetails['isRegistration'] == "Y") {?>
                                          <span> Reg. Id: <?= $rowAbstractDetails['user_registration_id'] ?></span>
                                       <? } ?>
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
                                         <?php
                                            $gradeVerdict = getAbstractGradeVerdict($rowAbstractDetails['id']);

                                            if ($gradeVerdict) {
                                                ?>
                                                <h5>Result :</h5>
                                                <span style="font-size:10px;">
                                                    <b><?= $gradeVerdict['label'] ?></b>
                                                    (<?= $gradeVerdict['accepted'] ?> Accepted / <?= $gradeVerdict['rejected'] ?> Rejected of <?= $gradeVerdict['total'] ?>)
                                                </span>
                                                <?php
                                            } elseif (!empty($totalMarks) && $totalReviewer > 0) {
                                                ?>
                                                <h5>Result :</h5>
                                                <span style="font-size:10px;"><?= $totalMarks ?>/<?= $totalReviewer ?> = <?= $totalAvg ?></span>
                                                <?php
                                            }
                                            ?>
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
                                <?php
                                    // PERF: nomination flag now comes from the batched
                                    // award map built once above, instead of a per-row query.
                                    $resultAbstractFile = $awardMap[$rowAbstractDetails['id']] ?? array();

                                    if($resultAbstractFile){
                                        ?>
                                    <h5 class="abstrat_review">Nominated- Yes</h5>
                                    <? }else{
                                        ?><h5 class="abstrat_review">Nominated- No</h5> <?
                                    }?>
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
                                    // PERF: faculty list is fetched ONCE above (outside this
                                    // row loop) instead of being re-queried for every row, and
                                    // the previous nested per-faculty / per-allotment lookups
                                    // (which built an unused $currentAllotments value) have been
                                    // removed since that output was never actually rendered.
                                    $counter = 0;
                                    if ($resultFacultyDetails) {
                                        foreach ($resultFacultyDetails as $i => $rowFacultyDetails) {
                                            $counter++;
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
   
</body>
<div class="pop_up_wrap">
    <? $editabstractId = isset($_POST['editabstractId']) ? $_POST['editabstractId'] : null; ?>
    <div class="pop_up_inner">
           <!-- abstract edit pop up -->
        <div class="pop_up_body"  id="abstract_edit">
            <?php
            header('Content-Type: text/html; charset=UTF-8');
            $abstractId = addslashes(trim($editabstractId));
            $abstractCounter = 0;

            $sqlAbstractDetails = array();
            $sqlAbstractDetails['QUERY'] = "    SELECT abstractRequest.*,
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

            $sqlAbstractDetails['PARAM'][] = array('FILD' => 'abstractRequest.status', 'DATA' => 'D', 'TYP' => 's');
            $sqlAbstractDetails['PARAM'][] = array('FILD' => 'abstractRequest.tags', 'DATA' => 'Abstract', 'TYP' => 's');
            $sqlAbstractDetails['PARAM'][] = array('FILD' => 'abstractRequest.id', 'DATA' => $abstractId, 'TYP' => 's');

            $resultAbstractDetails = $mycms->sql_select($sqlAbstractDetails);
            ?>
            
               <form name="caseRequestEditForm" id="caseRequestEditForm" action="<?= _BASE_URL_ ?>abstract_request.process.php" method="post" enctype="multipart/form-data" onSubmit="return abstractEditValidationform(this);" indx='<?= $rowAbstractDetails['abstract_submition_code'] ?>'>
                    
            <?
            foreach ($resultAbstractDetails as $i => $rowAbstractDetails) {
                $abstractCounter++;
                $rowUserDetails = getUserDetails($rowAbstractDetails['applicant_id']);


                ?>
                    <input type="hidden" name="act" value="editAbstractFileBack" />
                    <input type="hidden" name="delegateId" id="delegateId" value="<?= $rowAbstractDetails['applicant_id'] ?>" />
                    <input type="hidden" name="abstract_id" value="<?= $rowAbstractDetails['id'] ?>">
                    <input type="hidden" name="proofingEdit" id="proofingEdit" value="no">
                    <div class="profile_pop_up">
                        <div class="profile_pop_left">
                            <div class="profile_left_box text-center ">
                                <div class="regi_img_circle m-auto">
                                    <!-- <img src="" alt="" class="w-100 h-100"> -->
                                    <span>AM</span>
                                </div>
                                <h5><?= $rowUserDetails['user_full_name'] ?></h5>
                                <h6>Submission Code: <?= $rowAbstractDetails['abstract_submition_code'] ?></h6>
                            </div>
                            <div class="profile_left_box">
                                <ul>
                                    <li>
                                        <?php email(); ?>
                                        <p>
                                            <b>Email</b>
                                            <span><?= $rowUserDetails['user_email_id'] ?></span>
                                        </p>
                                    </li>
                                    <li>
                                        <?php call(); ?>
                                        <p>
                                            <b>Phone</b>
                                            <span><?= $rowUserDetails['user_mobile_isd_code'] ?>         <?= $rowUserDetails['user_mobile_no'] ?></span>
                                        </p>
                                    </li>
                                    <li>
                                        <?php calendar(); ?>
                                        <p>
                                            <b>Submitted On</b>
                                            <span><?= date('d/m/Y h:i A', strtotime($rowAbstractDetails['created_dateTime'])) ?></span>
                                        </p>
                                    </li>
                                    <li>
                                        <?php address(); ?>
                                        <p>
                                            <b>Location</b>
                                            <span><?= $rowUserDetails['user_address'] ?></span>
                                        </p>
                                    </li>
                                    <li>
                                        <?php address(); ?>
                                        <p>
                                            <b>Institute</b>
                                            <span><?= $rowUserDetails['user_institute_name'] ?></span>
                                        </p>
                                    </li>
                                    <li>
                                        <?php address(); ?>
                                        <p>
                                            <b>Department</b>
                                            <span><?= $rowUserDetails['user_department'] ?></span>
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="profile_pop_right">
                            <div class="profile_pop_right_heading">
                                <span>Edit Abstract Details<i class="ml-1 badge_padding badge_dark" style="margin-right: 10px;">ID: <?= $rowUserDetails['user_registration_id'] ?></i></span> <span style="margin-right: 560px;"><label class="toggleswitch toggleswitch-editAbstract">
                                                <input class="toggleswitch-checkbox toggleswitch-checkbox-editAbstract" type="checkbox">
                                                <div class="toggleswitch-switch toggleswitch-switch-editAbstract"></div>
                                            </label></span>
                                <p>
                                    <!-- <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php export(); ?></a> -->
                                    <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                                </p>
                            </div>
                            <div class="profile_pop_right_body abstract_pop_right_body">
                                <div class="registration-pop_body_box_inner">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Personal Details</span>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Full Name</p>
                                            <input type="text" name="abstract_edit_user_name"  value="<?= $rowUserDetails['user_full_name'] ?>">
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Email</p>
                                            <input  type="text" name="abstract_user_edit_email_id"  value="<?= $rowUserDetails['user_email_id'] ?>">
                                        </div>
                                         <div class="frm_grp span_1">
                                            <p class="frm-head">Mobile No</p>
                                            <input  type="text" name="abstract_user_edit_phone_no"  value="<?= $rowUserDetails['user_mobile_no'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Author Details</span>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Full Name</p>
                                            <input type="text" name="abstract_edit_author_name" id="abstract_edit_author_name<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_name'] ?>">
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Email</p>
                                            <input  type="text" name="abstract_author_edit_email_id" id="abstract_author_edit_email_id<?= $rowAbstractDetails['abstract_author_email_id'] ?>" value="<?= $rowAbstractDetails['abstract_author_email_id'] ?>">
                                        </div>
                                         <div class="frm_grp span_1">
                                            <p class="frm-head">Mobile No</p>
                                            <input  type="text" name="abstract_author_edit_phone_no" id="abstract_author_edit_phone_no<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_phone_no'] ?>">
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Country</p>
                                            <select name="abstract_author_edit_country" id="abstract_author_edit_country<?= $rowAbstractDetails['abstract_submition_code'] ?>" class="drpdwn" style="text-transform:uppercase;" operationmode="countryControl" forType="country"
                                                stateId="abstract_author_edit_state_id" onchange="stateRetriver(this);">
                                                <option value="">-- Select Country --</option>
                                                <?php
                                                $sqlFetchCountry = array();
                                                $sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                                        WHERE `status` = 'A' 
                                                                    ORDER BY `country_name` ASC";
                                                $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                if ($resultFetchCountry) {
                                                    foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                        ?>
                                                                        <option value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $rowAbstractDetails['abstract_author_country_id']) ? 'selected="selected"' : '' ?>><?= $rowFetchCountry['country_name'] ?></option>
                                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?
                                        if ($rowAbstractDetails['abstract_author_country_id'] != '') {

                                            ?>
                                                        <script>
                                                $(document).ready(function () {
                                                    if (typeof generateSateList === 'function') {
                                                        generateSateList(<?= $rowAbstractDetails['abstract_author_country_id'] ?>, jsBASE_URL);
                                                        $('#abstract_author_edit_state_id option[value="<?= $rowAbstractDetails['abstract_author_state_id'] ?>"]').prop('selected', true);
                                                    } else {
                                                        console.error('generateSateList not loaded — check CountryStateRetriver.js path');
                                                    }
                                                });
                                                </script>
                                                     <?php
                                        }
                                        ?>
                                        <div class="frm_grp span_1" use='stateContainer'>
                                            <p class="frm-head">State</p>
                                           <select name="abstract_author_edit_state_id" id="abstract_author_edit_state_id" fortype="state">
                                                <option value="">-- Select State--</option>
                                       
                                            </select>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">City</p>
                                            <input  type="text" name="abstract_author_edit_city" id="abstract_author_edit_city<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_city'] ?>">
                                        </div>
                               
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Institute Name</p>
                                            <input type="text" name="abstract_author_edit_institute_name" id="abstract_author_edit_institute_name<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_institute_name'] ?>" >
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Department</p>
                                            <input type="text" name="abstract_author_edit_department" id="abstract_author_edit_department<?= $rowAbstractDetails['abstract_submition_code'] ?>" value="<?= $rowAbstractDetails['abstract_author_department'] ?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Co-Author Details</span>
                                        <a class="add mi-1 addCoAuthor"><?php add(); ?>Add</a>
                                    </h4>
                                    <div class="accm_add_wrap">
                                        <div id="coauthor_template" style="display:none;">
                                            <div class="accm_add_box abstract_co_auth_form">
                                                <div class="form_grid ">
                                                    <input type="hidden" name="abstract_coauthor_edit_id[]" />

                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">Full Name</p>
                                                        <input type="text" name="abstract_coauthor_edit_name[]" class="form-control" style="text-transform:uppercase;" autocomplete="off" />
                                                    </div>
                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">Email</p>
                                                        <input type="text" name="abstract_coauthor_edit_email[]" class="form-control" style="text-transform:uppercase;" autocomplete="off" />
                                                    </div>
                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">Mobile No</p>
                                                        <input type="text" name="abstract_coauthor_edit_phone_no[]" class="form-control mobile_mini_fld" />
                                                    </div>

                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">Country</p>
                                                        <select name="abstract_coauthor_edit_country[]" class="coauthor_country" forType="country"
                                                        stateId="abstract_coauthor_edit_state" onchange="stateRetriver(this);" >
                                                            <option value="">-- Select Country --</option>
                                                            <?php
                                                            if ($resultFetchCountry) {
                                                                foreach ($resultFetchCountry as $rowFetchCountry) {
                                                                    ?>
                                                                                <option value="<?= $rowFetchCountry['country_id'] ?>">
                                                                                    <?= $rowFetchCountry['country_name'] ?>
                                                                                </option>
                                                                    <?php }
                                                            } ?>
                                                        </select>
                                                    </div>
             
                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">State</p>
                                                        <select name="abstract_coauthor_edit_state[]" class="coauthor_state" id="abstract_coauthor_edit_state" forType="state">
                                                            <option value="">-- Select Country First --</option>
                                                        </select>
                                                    </div>

                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">City</p>
                                                        <input type="text" name="abstract_coauthor_edit_city[]" class="form-control" style="text-transform:uppercase;" />
                                                    </div>

                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">Institute Name</p>
                                                        <input type="text" name="abstract_coauthor_institute_edit_name[]" class="form-control" />
                                                    </div>

                                                    <div class="frm_grp span_1">
                                                        <p class="frm-head">Department</p>
                                                        <input type="text" name="abstract_coauthor_edit_department[]" class="form-control" />
                                                    </div>

                                                    <a href="#" class="accm_delet accm_delet_abstract_co icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $sqlCoAuthorDtls = array();
                                        $sqlCoAuthorDtls['QUERY'] = "SELECT * 
																	FROM  " . _DB_ABSTRACT_COAUTHOR_ . "
																   WHERE `abstract_id` = '" . $rowAbstractDetails['id'] . "' 
																	 AND `status` = 'A'";
                                        $coAuthorDetails = $mycms->sql_select($sqlCoAuthorDtls);
                                        $coAuthor_counter = $mycms->sql_numrows($coAuthorDetails);
                                        if ($coAuthor_counter > 0) {
                                            $ii = 0;
                                            foreach ($coAuthorDetails as $keyRow => $rowFetchcoAuthorDetails) {
                                                $coAuthor_counter--;
                                                $ii++;
                                                ?>
                                                        <div class="accm_add_box">
                                                            <div class="form_grid">
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">Full Name</p>
                                                                    <input type="hidden" name="abstract_coauthor_edit_id[]" id="abstract_coauthor_edit_id<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['id'] ?>" />
                                                                    <input type="text" name="abstract_coauthor_edit_name[]" id="abstract_coauthor_edit_name<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_name'] ?>" style="text-transform:uppercase;" operationMode="abstract_coauthor_name" sequenceBy="#COUNTER" autocomplete="off" />
                                                                </div>
                                                                 <div class="frm_grp span_1">
                                                                        <p class="frm-head">Email</p>
                                                                        <input type="text" name="abstract_coauthor_edit_email[]" class="form-control" style="text-transform:uppercase;" autocomplete="off"  value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_email'] ?>"/>
                                                                    </div>
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">Mobile No</p>
                                                                       <input type="text" name="abstract_coauthor_edit_phone_no[]" id="abstract_coauthor_edit_phone_no<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_phone_no'] ?>" class="form-control frmdec mobile_mini_fld" style="text-transform:uppercase; width:51.5%;" />

                                                                </div>
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">Country</p>
                                                                     <select name="abstract_coauthor_edit_country[]" id="abstract_coauthor_edit_country<?= $rowFetchcoAuthorDetails['id'] ?>" forType="country"
                                                                      stateId="abstract_coauthor_edit_state<?= $rowFetchcoAuthorDetails['id'] ?>" onchange="stateRetriver(this);">
                    
                                                                        <option value="">-- Select Country --</option>
                                                                        <?php
                                                                        $sqlFetchCountry = array();
                                                                        $sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
                                                                                WHERE `status` = 'A' 
                                                                                ORDER BY `country_name` ASC";
                                                                        $resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
                                                                        if ($resultFetchCountry) {
                                                                            foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
                                                                                ?>
                                                                                                <option value="<?= $rowFetchCountry['country_id'] ?>" <?= ($rowFetchCountry['country_id'] == $rowFetchcoAuthorDetails['abstract_coauthor_country_id']) ? 'selected="selected"' : '' ?>><?= $rowFetchCountry['country_name'] ?></option>
                                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                   </div>
                                                                    <?
                                                                    if ($rowFetchcoAuthorDetails['abstract_coauthor_country_id'] != '') {

                                                                        ?>
                                                                               <script>
                                                                                    $(document).ready(function () {

                                                                                    generateSateListCo(
                                                                                    <?= $rowFetchcoAuthorDetails['abstract_coauthor_country_id'] ?>,
                                                                                    jsBASE_URL,
                                                                                    'abstract_coauthor_edit_state<?= $rowFetchcoAuthorDetails['id'] ?>',
                                                                                    '<?= $rowFetchcoAuthorDetails['abstract_coauthor_state_id'] ?>'
                                                                                    );

                                                                                    });
                                                                                </script>
                                                                                <?php
                                                                    }
                                                                    ?>
                                                                <div class="frm_grp span_1" use='stateContainer'>
                                                                    <p class="frm-head">State</p>
                                                                   <select name="abstract_coauthor_edit_state[]" id="abstract_coauthor_edit_state<?= $rowFetchcoAuthorDetails['id'] ?>" operationMode="stateControl" fortype="stateCo">
                                                                       <option value="">-- Select Country First --</option>
                                               
                                                                    </select>
                                                                </div>
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">City</p>
                                                                    <input type="text" name="abstract_coauthor_edit_city[]" id="abstract_coauthor_edit_city<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_city_name'] ?>" class="form-control frmdec" style="text-transform:uppercase;" />
                                                                </div>
                                                                <!-- <div class="frm_grp span_1">
                                            <p class="frm-head">Pincode</p>
                                            <input>
                                        </div> -->
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">Institute Name</p>
                                                                       <input type="text" name="abstract_coauthor_institute_edit_name[]" id="abstract_coauthor_institute_edit_name<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_institute_name'] ?>" class="form-control frmdec" style="text-transform:uppercase;" autocomplete="off" />
                                                                </div>
                                                                <div class="frm_grp span_1">
                                                                    <p class="frm-head">Department</p>
                                                                       <input type="text" name="abstract_coauthor_edit_department[]" id="abstract_coauthor_edit_department<?= $rowFetchcoAuthorDetails['id'] ?>" value="<?= $rowFetchcoAuthorDetails['abstract_coauthor_department'] ?>" class="form-control frmdec" style="text-transform:uppercase;" autocomplete="off" />
                                                                </div>
                                                                <!-- <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a> -->

                                                            </div>
                                                        </div>
                                                <? }
                                        } else {
                                            ?>
                                                    <h6 class="accm_add_empty">No Co-Author Added</h6>
                                                    <?
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Abstract Details with Proofing</span>
                                          <span><label class="toggleswitch toggleswitch-Proofing">
                                                <input class="toggleswitch-checkbox toggleswitch-checkbox-Proofing" type="checkbox">
                                                <div class="toggleswitch-switch toggleswitch-switch-edit_Proofing"></div>Edit Proofing
                                            </label></span>
                                    </h4>
                            
                                    <div class="form_grid">
                                        <?php
                                        $sqlAbstractTopic = array();
                                        $sqlAbstractTopic['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                            WHERE `status` = 'A' 
                                                            ORDER BY `abstract_topic` ASC";

                                        $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
                                        if ($resultAbstractTopic) {
                                            ?>
                                                <div class="frm_grp span_2">
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
                                                </div>
                                        <? } ?>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Title</p>
                                            <div class="form_grid">
                                                <div class="frm_grp span_2">
                                                  <textarea name="abstract_edit_title" id="abstract_edit_title<?= $rowAbstractDetails['abstract_submition_code'] ?>" fieldType="abstractTitle"><?= $rowAbstractDetails['abstract_title'] ?></textarea>

                                                </div>
                                                <div class="frm_grp span_2">
                                                    <textarea class="proofingEditText" name="proof_abstract_title"  required ><?= ($rowAbstractDetails['proof_abstract_title'] != '') ? $rowAbstractDetails['proof_abstract_title'] : $rowAbstractDetails['abstract_title'] ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        function cleanFields($fields)
                                        {
                                            $fields = json_decode($fields, true);

                                            if (!is_array($fields)) {
                                                return [];
                                            }

                                            // remove empty values like "", null
                                            $fields = array_filter($fields, function ($val) {
                                                return $val !== null && $val !== '';
                                            });

                                            // ensure integers only (security)
                                            return array_map('intval', $fields);
                                        }
                                        $category_id = $rowAbstractDetails['abstract_cat'] ?? null;
                                        $sub_category_id = $rowAbstractDetails['abstract_parent_type'] ?? null;
                                        $sub_subcategory_Id = $rowAbstractDetails['abstract_child_type'] ?? null;

                                        $category_fields = [];

                                        /* ================= CATEGORY ================= */
                                        if ($category_id) {

                                            $sqlCategory1 = [
                                                'QUERY' => "SELECT category_fields 
                                                        FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                        WHERE id = ?",
                                                'PARAM' => [
                                                    ['FILD' => 'id', 'DATA' => $category_id, 'TYP' => 'i']
                                                ]
                                            ];

                                            $resultCategory1 = $mycms->sql_select($sqlCategory1);

                                            if ($resultCategory1) {
                                                $category_fields = cleanFields($resultCategory1[0]['category_fields']);
                                            }

                                            /* ================= SUB CATEGORY ================= */
                                            if (empty($category_fields) && $sub_category_id) {

                                                $sqlSubmission = [
                                                    'QUERY' => "SELECT category_fields 
                                                            FROM " . _DB_ABSTRACT_SUBMISSION_ . "
                                                            WHERE category = ? AND id = ?
                                                            LIMIT 1",
                                                    'PARAM' => [
                                                        ['FILD' => 'category', 'DATA' => $category_id, 'TYP' => 'i'],
                                                        ['FILD' => 'id', 'DATA' => $sub_category_id, 'TYP' => 'i']
                                                    ]
                                                ];

                                                $resultSubmission = $mycms->sql_select($sqlSubmission);

                                                if ($resultSubmission) {
                                                    $category_fields = cleanFields($resultSubmission[0]['category_fields']);
                                                }
                                            }

                                            /* ================= SUB SUB CATEGORY ================= */
                                            if (empty($category_fields) && $sub_subcategory_Id) {

                                                $sqlPresentation = [
                                                    'QUERY' => "SELECT category_fields 
                                                            FROM " . _DB_ABSTRACT_PRESENTATION_ . "
                                                            WHERE category_id = ? 
                                                            AND submission_id = ? 
                                                            AND id = ?
                                                            LIMIT 1",
                                                    'PARAM' => [
                                                        ['FILD' => 'category_id', 'DATA' => $category_id, 'TYP' => 'i'],
                                                        ['FILD' => 'submission_id', 'DATA' => $sub_category_id, 'TYP' => 'i'],
                                                        ['FILD' => 'id', 'DATA' => $sub_subcategory_Id, 'TYP' => 'i']
                                                    ]
                                                ];

                                                $resultPresentation = $mycms->sql_select($sqlPresentation);

                                                if ($resultPresentation) {
                                                    $category_fields = cleanFields($resultPresentation[0]['category_fields']);
                                                }
                                            }
                                        }

                                        /* ================= FINAL FALLBACK ================= */
                                        if (empty($category_fields)) {
                                            $category_fields = [];
                                        }
                                        $field_ids = implode(",", $category_fields);
                                        if (!empty($field_ids)) {
                                            $order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
                                        } else {
                                            $order_by = "ORDER BY id ASC";
                                        }
                                        $sqlAbstractFields = array();
                                        $sqlAbstractFields['QUERY'] = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
									WHERE `status` = ? " . $order_by . "";

                                        $sqlAbstractFields['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

                                        $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                        $totalWordCount = 0;
                                        foreach ($resultAbstractFields as $key => $value) {
                                            if ($rowAbstractDetails[$value['field_key']] != '' && $rowAbstractDetails[$value['field_key']] != NULL && $rowAbstractDetails[$value['field_key']] != 'NULL') {
                                                ?>
                                                        <div class="frm_grp span_4">
                                                            <p class="frm-head"><?= $value['display_name'] ?></p>
                                                            <div class="form_grid">
                                                                <div class="frm_grp span_2">
                                                                       <textarea name="abstract_edit_<?= $value['field_key'] ?>"  value="<?= htmlspecialchars($rowAbstractDetails[$value['field_key']], ENT_QUOTES, 'UTF-8') ?>" operationMode="abstractWordCounter" ><?= $rowAbstractDetails[$value['field_key']] ?></textarea>

                                                                </div>
                                                                <div class="frm_grp span_2">
                                                                    <textarea  class="proofingEditText" name="proof_<?= $value['field_key'] ?>" required ><?= ($rowAbstractDetails['proof_' . $value['field_key']] != '') ? $rowAbstractDetails['proof_' . $value['field_key']] : $rowAbstractDetails[$value['field_key']] ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <? }
                                        } ?>
                           
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Nomination & Uploaded Documents</span>
                                    </h4>
                                    <ul class="service_breakdown_wrap_ul">
                                
                                                 <?php
                                                 $sqlAbstractSubcat = array();
                                                 $sqlAbstractSubcat['QUERY'] = "SELECT * 
																		  FROM " . _DB_AWARD_MASTER_ . " 
																		 WHERE `status` = ? 
                                                                         AND `related_category_id` = ?
																	  ORDER BY `id` ASC";

                                                 $sqlAbstractSubcat['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                 $sqlAbstractSubcat['PARAM'][] = array('FILD' => 'related_category_id', 'DATA' => $rowAbstractDetails['abstract_cat'], 'TYP' => 's');

                                                 $resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);

                                                 $sqlAbstractFile = array();
                                                 $sqlAbstractFile['QUERY'] = "SELECT * 
																		  FROM " . _DB_AWARD_REQUEST_ . " 
																		 WHERE `status` = ? 
                                                                         AND `submission_id` = ?
																	  ORDER BY `id` ASC";

                                                 $sqlAbstractFile['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                                                 $sqlAbstractFile['PARAM'][] = array('FILD' => 'submission_id', 'DATA' => $rowAbstractDetails['id'], 'TYP' => 's');

                                                 $resultAbstractFile = $mycms->sql_select($sqlAbstractFile);

                                                 ?>
                                             <? if ($rowAbstractDetails['category_required_file'] != '') { ?>
                                                 <li>
                                    
                                                    <n>
                                                        <j>Category File</j>
                                                    </n>
                                                    <g>
                                                        <a class="icon_hover badge_primary br-5 w-auto action-transparent" href="<?= _BASE_URL_ ?><?= $cfg['FILES.ABSTRACT.REQUEST'] ?><?= $rowAbstractDetails['category_required_file'] ?>" target="_blank">
                                                                    <?php view() ?></a>

                                                    </g>
                                                </li>
                                         <? } ?>
                                           <? if ($rowAbstractDetails['abstract_file'] != '') { ?>
                                                 <li>
                                    
                                                    <n>
                                                        <j>Abstract File</j>
                                                    </n>
                                                    <g>
                                                        <a class="icon_hover badge_primary br-5 w-auto action-transparent" href="<?= _BASE_URL_ ?><?= $cfg['FILES.ABSTRACT.REQUEST'] ?><?= $rowAbstractDetails['abstract_file'] ?>" target="_blank">
                                                                    <?php view() ?></a>

                                                    </g>
                                                </li>
                                         <? } ?>
                                           <? if ($rowAbstractDetails['abstract_consent_file'] != '') { ?>
                                                 <li>
                                    
                                                    <n>
                                                        <j>HOD Consent File</j>
                                                    </n>
                                                    <g>
                                                        <a class="icon_hover badge_primary br-5 w-auto action-transparent" href="<?= _BASE_URL_ ?><?= $cfg['FILES.ABSTRACT.REQUEST'] ?><?= $rowAbstractDetails['abstract_consent_file'] ?>" target="_blank">
                                                                    <?php view() ?></a>

                                                    </g>
                                                </li>
                                         <? } ?>
                                         <?php if ($resultAbstractSubcat) {

    // Build awardRequestedMap ONCE, outside the loop
    $awardRequestedMap = array();
    if (!empty($resultAbstractFile)) {
        foreach ($resultAbstractFile as $af) {
            $awardRequestedMap[$af['award_id']] = $af;
        }
    }

    foreach ($resultAbstractSubcat as $rowAbstractTopic) {

        $awardId      = $rowAbstractTopic['id'];
        $existingRow  = $awardRequestedMap[$awardId] ?? null;
        $existingFile = $existingRow['upload_nomination_file'] ?? '';
        $isChecked    = !empty($existingRow);

        $needsUpload     = ($rowAbstractTopic['doc_upload'] == 'yes'
                            && $rowAbstractTopic['suporting_document_type'] !== 'null');
        $rowAbstracttype = $needsUpload ? json_decode($rowAbstractTopic['suporting_document_type']) : array();
        ?>

        <!-- ============ CHECKBOX ROW ============ -->
        <li>
            <n>
                <j><?= $resultAbstractSubcat[0]['award_description'] ?></j>
            </n>
            
            <g>
                <label class="custom-radio" for="nomination_name_<?= $awardId ?>"> 
                    <input class="toggleswitch-checkbox award-toggle"
                           data-award-id="<?= $awardId ?>"
                           data-abstract-id="<?= $rowAbstractDetails['id'] ?>"
                           data-has-file="<?= $existingFile !== '' ? '1' : '0' ?>"
                           type="checkbox"
                           name="award_request[<?= $awardId ?>]"
                           value="<?= $awardId ?>"
                           id="nomination_name_<?= $awardId ?>"
                           <?= $isChecked ? 'checked' : '' ?>>
                    <div class="toggleswitch-switch"><span class="checkmark"></span></div>
                </label>
            </g>
        </li>

        <!-- ============ UPLOAD WRAPPER (only when this award needs a file) ============ -->
        <?php if ($needsUpload) { ?>
        <li class="nomination-upload-wrap"
            id="upload_section_<?= $awardId ?>"
            data-award-id="<?= $awardId ?>"
            style="<?= $isChecked ? '' : 'display:none;' ?>">

            <!-- (A) EXISTING FILE VIEW — shown only when a file exists -->
            <div class="existing-nomination-file"
                 id="existing_file_<?= $awardId ?>"
                 style="<?= $existingFile !== '' ? '' : 'display:none;' ?>">

                <h8><?= $rowAbstractTopic['suporting_document_name'] ?></h8>
                <p>
                    <a href="<?= _BASE_URL_ ?><?= $cfg['FILES.ABSTRACT.REQUEST'] ?><?= $existingFile ?>"
                       target="_blank"
                       class="icon_hover badge_primary br-5 w-auto action-transparent">
                        <?= view() ?> View existing file
                    </a>

                    <button type="button"
                            class="badge_danger delete-nomination-file"
                            data-award-id="<?= $awardId ?>">
                        <?= delete() ?> Delete
                    </button>
                </p>
            </div>

            <!-- (B) NEW UPLOAD INPUT — shown when no file, or after Delete -->
            <div class="new-nomination-file"
                 id="new_file_<?= $awardId ?>"
                 style="<?= $existingFile === '' ? '' : 'display:none;' ?>">

                <img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
                <div class="file-up-dtls">
                    <h8><?= $rowAbstractTopic['suporting_document_name'] ?></h8>

                    <input type="hidden"
                           name="original_nomination_file_name[<?= $awardId ?>]"
                           value="<?= $existingFile ?>">

                    <input type="hidden"
                           class="delete-nomination-flag"
                           id="delete_flag_<?= $awardId ?>"
                           name="delete_nomination_file[<?= $awardId ?>]"
                           value="0">

                    <input class="form-control nomination-file"
                           type="file"
                           data-award-id="<?= $awardId ?>"
                           data-types="<?= implode(',', array_filter($rowAbstracttype)) ?>"
                           name="upload_nomination_file[<?= $awardId ?>]"
                           id="formFileAbstract_<?= $awardId ?>">

                    <h6>
                        <?= ($rowAbstracttype[0] != '') ? strtoupper($rowAbstracttype[0]) . " | " : '' ?>
                        <?= ($rowAbstracttype[1] != '') ? ucfirst($rowAbstracttype[1]) : '' ?>
                        <?= ($rowAbstracttype[2] != '') ? " | " . ucfirst($rowAbstracttype[2]) : '' ?>
                    </h6>
                    <span></span>
                </div>
            </div>
        </li>
        <?php } ?>

    <?php } /* end foreach */ ?>
<?php } /* end if */ ?>
                                    </ul>
                                </div>
                                    <?php
                                    $searchData = '';
                                    if (!empty($rowAbstractDetails['abstract_cat'])) {
                                        $searchData .= " AND JSON_SEARCH(criteria.`category_id`, 'one', '" . $rowAbstractDetails['abstract_cat'] . "') IS NOT NULL";
                                    }

                                    if (!empty($abstractResultSet['abstract_parent_type'])) {
                                        //$searchData.=" AND JSON_SEARCH(criteria.`sub_category_id`, 'one', '".$abstractResultSet['abstract_parent_type']."') IS NOT NULL";
                                    }

                                    $sqlReviewCriteria['QUERY'] = "SELECT criteria.*,
                                                                    
                                                                            criteria.id AS criteriaId,
                                                                            criteria.	abstract_name AS review_criteria,
                                                                            
                                                                            
                                                                            reviewResultDetailsId,
                                                                            resultCriteriaId,
                                                                            resultCriteriaOptionId,
                                                                            resultMarksObtained,
                                                                            resultMarksObtainedInvalue,
                                                                            review_individual_assessment_marks,
                                                                            faculty_review,
                                                                            facultyName

                                
                                                                FROM " . _DB_ABSTRACT_REVIEW_LIST_ . " criteria 
                                                    
                                                                
                                                    LEFT OUTER JOIN (
                                                    
                                                                            SELECT reviewResult.id AS reviewResultId, 
                                                                                reviewResult.faculty_review AS faculty_review, 
                                                                                reviewResultDetails.id AS reviewResultDetailsId,
                                                                                reviewResultDetails.review_criteria_id AS resultCriteriaId,
                                                                                reviewResultDetails.review_criteria_option_id AS resultCriteriaOptionId,
                                                                                reviewResultDetails.review_obtained_marks AS resultMarksObtained,
                                                                                reviewResultDetails.review_obtained_marks_invalue AS resultMarksObtainedInvalue,
                                                                                reviewResultDetails.review_individual_assessment_marks AS review_individual_assessment_marks,
                                                                                F.faculty_login_username as facultyName 
                                                                            
                                                                            FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " reviewResult 
                                                                            
                                                                        INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " reviewResultDetails 
                                                                                ON reviewResult.id = reviewResultDetails.review_result_id 

                                                                            INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " F 
                                                                                ON F.id = reviewResult.faculty_id
                                                                                
                                                                            WHERE 
                                                                                reviewResult.abstract_id = '" . $rowAbstractDetails['id'] . "' 
                                                                            AND reviewResult.status = 'A' 
                                                                            AND F.status = 'A' 

                                                                            AND reviewResultDetails.status = 'A' 
                                                                            
                                                                    ) reviewResultDetails 
                                                                ON reviewResultDetails.resultCriteriaId = criteria.id 
                                                                    
                                                                WHERE criteria.status = 'A' " . $searchData . "
                                                                
                                                            
                                                            ORDER BY criteria.id ASC";



                                    $resultReviewCriteria = $mycms->sql_select($sqlReviewCriteria);
                                    //   echo '<pre>'; print_r($resultReviewCriteria);	
                                

                                    $sqlReviewCat = array();
                                    $sqlReviewCat['QUERY'] = "SELECT abstract_name

                                
                                                                FROM " . _DB_ABSTRACT_REVIEW_LIST_ . " WHERE status='A' AND JSON_SEARCH(`category_id`, 'one', '" . $rowAbstractDetails['abstract_cat'] . "') IS NOT NULL ";

                                    $resultReviewCat = $mycms->sql_select($sqlReviewCat);
                                    $resultArray = [];
                                    $sqlReview = array();
                                    $sqlReview['QUERY'] = " SELECT SUM(R.`marks_obtained`) MARKS, COUNT(R.`faculty_id`) COUNTDATA
                                                                                FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " R INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " F ON R.faculty_id = F.id
                                                                                WHERE R.abstract_id = '" . $rowAbstractDetails['id'] . "' AND R.status='A' AND F.status='A'";

                                    $resultReview = $mycms->sql_select($sqlReview);

                                    // echo '<pre>'; print_r($resultClassificationTitle[0]);
                                
                                    $totalMark = $resultReview[0]['MARKS'];
                                    $totalReviewer = $resultReview[0]['COUNTDATA'];

                                    $remarks = $resultReviewCriteria[0]['faculty_review'];

                                    foreach ($resultReviewCriteria as $keyReviewCriteria => $rowReviewCriteria) {
                                        if ($rowReviewCriteria['score_option'] == 'textbox') {
                                            $resultArray[$rowReviewCriteria['facultyName']][$keyReviewCriteria]['abstract_name'] = $rowReviewCriteria['abstract_name'];

                                            $resultArray[$rowReviewCriteria['facultyName']][$keyReviewCriteria]['marks'] = $rowReviewCriteria['resultMarksObtained'];
                                        } else {
                                            $resultArray[$rowReviewCriteria['facultyName']][$keyReviewCriteria]['abstract_name'] = $rowReviewCriteria['abstract_name'];

                                            $resultArray[$rowReviewCriteria['facultyName']][$keyReviewCriteria]['marks'] = $rowReviewCriteria['resultMarksObtained'];
                                        }

                                    }
                                    $reviewDetails = getAbstractRsultArray($rowAbstractDetails['id']);
                                    // echo '<pre>';print_r($resultArray);
                                    $reviwer_name_arr = array();
                                    $skipped_reviewer_arr = array();
                                    foreach ($reviewDetails['REVIEWER'] as $key => $value) {
                                        if ($value['REVIEW_STATE'] == 'UNABLE') {
                                            array_push($reviwer_name_arr, "<span style='color:red'>" . ucwords(strtolower($value['NAME'])) . " - Skipped</span>");
                                        } else {
                                            array_push($reviwer_name_arr, ucwords(strtolower($value['NAME'])));

                                        }

                                        // array_push($reviwer_name_arr, ucwords(strtolower( $value['NAME'])));
                                        // if ($value['REVIEW_STATE'] == 'UNABLE') {
                                        // 	array_push($skipped_reviewer_arr,  ucwords(strtolower($value['NAME'])));
                                        // }
                                    }
                                    // echo '<pre>'; print_r($resultReviewCriteria);	
                                     if (!empty($totalMark) && $totalReviewer > 0) { ?>
                                        <div class="registration-pop_body_box_inner">
                                            <h4 class="registration-pop_body_box_heading">
                                                <span>Allotted Reviewers & Scores</span>
                                            </h4>
                                            <div class="spot_listing">

                                                <?php
                                                $totalReviewer = count($resultArray);
                                                $grandTotal    = 0;
                                                $counter       = 1;

                                                // For grade-based (Accepted/Rejected) categories
                                                $gradeCounts  = array('Accepted' => 0, 'Rejected' => 0);
                                                $isGradeBased = false;

                                                foreach ($resultArray as $key => $dataValue) {
                                                    ?>
                                                    <div class="spot_box">
                                                        <div class="spot_box_top">
                                                            <div class="spot_name d-flex align-items-start">
                                                                <div class="regi_img_circle">
                                                                    <span><?= $counter ?></span>
                                                                </div>
                                                                <div>
                                                                    <div class="regi_name"><?= $key ?></div>
                                                                    <div class="regi_type">
                                                                        <span class="badge_padding badge_primary">Reviewer</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="spot_details">
                                                                <div class="spot_details_box registration_details_box">
                                                                    <small>Scores:</small>
                                                                    <h4>
                                                                        <?php
                                                                        $totalMarks       = 0;
                                                                        $reviewerDecision = null; // this reviewer's Accepted/Rejected, if any

                                                                        foreach ($dataValue as $index => $value) {
                                                                            if (!isset($arrtotal[$index])) {
                                                                                $arrtotal[$index] = 0;
                                                                            }

                                                                            if (in_array($value['marks'], array('Accepted', 'Rejected'), true)) {
                                                                                // Grade-based criterion: don't add to numeric total
                                                                                $isGradeBased     = true;
                                                                                $reviewerDecision = $value['marks'];
                                                                            } else {
                                                                                $totalMarks       += (float) $value['marks'];
                                                                                $arrtotal[$index] += (float) $value['marks'];
                                                                            }
                                                                            ?>
                                                                            <span>
                                                                                <n><?= $value['abstract_name'] ?>:</n>
                                                                                <n><?= $value['marks'] ?></n>
                                                                            </span>
                                                                            <?php
                                                                        }

                                                                        // Count this reviewer's decision once, toward the majority
                                                                        if ($reviewerDecision !== null) {
                                                                            $gradeCounts[$reviewerDecision]++;
                                                                        }
                                                                        ?>
                                                                    </h4>
                                                                </div>
                                                                <div class="spot_details_box abstract_details_box flex3">
                                                                    <h4>
                                                                        <span class="text_success">
                                                                            <n>Total:</n>
                                                                            <n><?= $isGradeBased ? ($reviewerDecision ?? '-') : $totalMarks ?></n>
                                                                        </span>
                                                                    </h4>
                                                                    <ul>
                                                                        <li class="span_6">
                                                                            <span>Remarks</span>
                                                                            <p><?= $remarks ?></p>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $grandTotal += $totalMarks; // now correctly inside the loop
                                                    $counter++;
                                                }

                                                if ($isGradeBased) {
                                                    $accepted = $gradeCounts['Accepted'];
                                                    $rejected = $gradeCounts['Rejected'];

                                                    if ($accepted > $rejected) {
                                                        $finalLabel = 'Accepted';
                                                    } elseif ($rejected > $accepted) {
                                                        $finalLabel = 'Rejected';
                                                    } else {
                                                        $finalLabel = 'Accepted/Rejected';
                                                    }
                                                    ?>
                                                    <h5 class="regi_total badge_success mt-0">
                                                        Result
                                                        <span><?= $finalLabel ?> (<?= $accepted ?> Accepted / <?= $rejected ?> Rejected)</span>
                                                    </h5>
                                                    <?php
                                                } else {
                                                    $totalAvg = ($totalReviewer > 0) ? round($grandTotal / $totalReviewer, 2) : 0;
                                                    ?>
                                                    <h5 class="regi_total badge_success mt-0">
                                                        Avarage<span><?= $totalAvg ?></span>
                                                    </h5>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success abstracteditSubmmit" value="Update" ><?php save(); ?>Update Registration</button>
                        </div>
                    </div>
             <? } ?>
          </form>
          
           <script>
            /* Show/hide the file upload for the corresponding award */
           /* =====================================================================
   Nomination toggle + file state (edit popup)
   ===================================================================== */
$(document).on('change', '#abstract_edit .award-toggle', function () {

    var $cb       = $(this);
    var awardId   = $cb.data('award-id');
    var $wrap     = $('#abstract_edit #upload_section_' + awardId);
    var $existing = $('#abstract_edit #existing_file_' + awardId);
    var $newFile  = $('#abstract_edit #new_file_' + awardId);
    var $input    = $('#abstract_edit #formFileAbstract_' + awardId);
    var $delFlag  = $('#abstract_edit #delete_flag_' + awardId);

    // No upload UI for this award → nothing to toggle
    if (!$wrap.length) {
        return;
    }

    if ($cb.is(':checked')) {

        $wrap.slideDown(150);

        var hasExisting   = $existing.is(':visible');
        var markedDeleted = $delFlag.val() === '1';

        if (hasExisting && !markedDeleted) {
            $existing.show();
            $newFile.hide();
            $input.removeAttr('required').val('');
        } else {
            $existing.hide();
            $newFile.show();
            $input.attr('required', true);
        }

    } else {
        // Unchecked → hide everything, mark for deletion
        $wrap.slideUp(150);
        $input.removeAttr('required').val('');
        $delFlag.val('1');
    }
});

/* =====================================================================
   Delete existing nomination file
   ===================================================================== */
$(document).on('click', '#abstract_edit .delete-nomination-file', function (e) {
    e.preventDefault();

    var awardId   = $(this).data('award-id');
    var $existing = $('#abstract_edit #existing_file_' + awardId);
    var $newFile  = $('#abstract_edit #new_file_' + awardId);
    var $input    = $('#abstract_edit #formFileAbstract_' + awardId);
    var $delFlag  = $('#abstract_edit #delete_flag_' + awardId);

    if (!confirm('Delete the existing nomination file? You can upload a new one below.')) {
        return;
    }

    $delFlag.val('1');
    $existing.hide();
    $newFile.show();
    $input.attr('required', true).val('').focus();
});

            $(document).ready(function() {
                $(document).on('click', '.accm_delet_abstract_co', function(e) {
                        e.preventDefault();

                        // ✅ remove only that specific row
                        $(this).closest('.abstract_co_auth_form').remove();
                    });
                // Function to set all form fields to readonly/disabled mode
                function setReadOnlyMode(enableReadOnly, formId) {
                    // If formId is provided, scope to that specific form, otherwise use default
                    var $form = formId ? $('#' + formId) : $('#abstract_edit');
                    
                    // Select all form elements within the specific form except the toggle switches
                    var $formElements = $form.find('input, textarea, select');
                    
                    $formElements.each(function() {
                        var $element = $(this);
                        var tagName = $element.prop('tagName').toLowerCase();
                        
                        // Skip the toggle switches
                        if ($element.hasClass('toggleswitch-checkbox-editAbstract') || 
                            $element.hasClass('toggleswitch-checkbox-Proofing')) {
                            return true;
                        }
                        
                        // Skip the proofing textareas - they will be handled separately
                        if ($element.hasClass('proofingEditText')) {
                            return true;
                        }
                        
                        if (enableReadOnly) {
                            if (tagName === 'input' || tagName === 'textarea') {
                                $element.prop('readonly', true);
                                $element.addClass('readonly-mode');
                                $element.css('opacity', '0.5');
                            } else if (tagName === 'select') {
                                $element.prop('disabled', true);
                                $element.addClass('disabled-mode');
                            }
                        } else {
                            if (tagName === 'input' || tagName === 'textarea') {
                                $element.prop('readonly', false);
                                $element.removeClass('readonly-mode');
                                 $element.css('opacity', '1');
                            } else if (tagName === 'select') {
                                $element.prop('disabled', false);
                                $element.removeClass('disabled-mode');
                            }
                        }
                    });
                    
                    // Handle Add Co-Author button within this specific form
                    if (enableReadOnly) {
                        $form.find('.addCoAuthor').css({
                            'pointer-events': 'none',
                            'opacity': '0.5',
                            'cursor': 'not-allowed'
                        });
                        $form.find('.accm_delet').css({
                            'pointer-events': 'none',
                            'opacity': '0.5',
                            'cursor': 'not-allowed'
                        });
                    } else {
                        $form.find('.addCoAuthor').css({
                            'pointer-events': 'auto',
                            'opacity': '1',
                            'cursor': 'pointer'
                        });
                        $form.find('.accm_delet').css({
                            'pointer-events': 'auto',
                            'opacity': '1',
                            'cursor': 'pointer'
                        });
                    }
                    
                    // Disable/Enable ONLY the submit button with class 'abstracteditSubmmit' within this specific form
                    if (enableReadOnly) {
                        $form.find('button.abstracteditSubmmit').prop('disabled', true).addClass('disabled-mode');
                    } else {
                        $form.find('button.abstracteditSubmmit').prop('disabled', false).removeClass('disabled-mode');
                    }
                }
                            
                // Function to set proofing fields mode
                function setProofingMode(enableProofing) {
                    if (enableProofing) {
                        $('.proofingEditText').prop('readonly', false).removeClass('readonly-mode');
                          $('.proofingEditText').css('opacity', '1');
                    } else {
                        $('.proofingEditText').prop('readonly', true).addClass('readonly-mode');
                          $('.proofingEditText').css('opacity', '0.5');
                    }
                }
                
                // Initially set all fields to readonly (mode OFF)
                setReadOnlyMode(true);
                setProofingMode(false);
                
                // Disable proofing toggle initially (since main edit is OFF)
                $('.toggleswitch-checkbox-Proofing').prop('disabled', true);
                $('.toggleswitch-switch-edit_Proofing').css('opacity', '0.5');
                
                // Toggle for Edit Abstract Details
                $('.toggleswitch-checkbox-editAbstract').on('change', function() {
                    if ($(this).is(':checked')) {
                        // Turn OFF readonly mode (enable editing)
                        setReadOnlyMode(false);
                        
                        // Enable proofing toggle
                        $('.toggleswitch-checkbox-Proofing').prop('disabled', false);
                        $('.toggleswitch-switch-edit_Proofing').css('opacity', '1');
                        
                        // If proofing toggle was checked before, apply it
                        if ($('.toggleswitch-checkbox-Proofing').is(':checked')) {
                            setProofingMode(true);
                        } else {
                            setProofingMode(false);
                        }
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Edit mode enabled. You can now edit the abstract details.', 'Edit Mode', {
                                progressBar: true,
                                timeOut: 3000
                            });
                        }
                    } else {
                        // Turn ON readonly mode (disable editing)
                        setReadOnlyMode(true);
                        
                        // Disable proofing toggle and reset proofing fields to readonly
                        $('.toggleswitch-checkbox-Proofing').prop('disabled', true);
                        $('.toggleswitch-switch-edit_Proofing').css('opacity', '0.5');
                        setProofingMode(false);
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.info('Edit mode disabled. Abstract details are now read-only.', 'Read-Only Mode', {
                                progressBar: true,
                                timeOut: 3000
                            });
                        }
                    }
                });
                
                // Toggle for Proofing fields (only works when main edit toggle is ON)
                $('.toggleswitch-checkbox-Proofing').on('change', function() {
                    // Check if main edit toggle is ON
                    if ($('.toggleswitch-checkbox-editAbstract').is(':checked')) {
                        if ($(this).is(':checked')) {
                            setProofingMode(true);
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Proofing edit mode enabled', 'Proofing Mode', {
                                    progressBar: true,
                                    timeOut: 3000
                                });
                            }
                        } else {
                            setProofingMode(false);
                            if (typeof toastr !== 'undefined') {
                                toastr.info('Proofing edit mode disabled', 'Read-Only Mode', {
                                    progressBar: true,
                                    timeOut: 3000
                                });
                            }
                        }
                    } else {
                        // If main edit toggle is OFF, revert the proofing toggle
                        $(this).prop('checked', false);
                        if (typeof toastr !== 'undefined') {
                            toastr.warning('Please enable "Edit Abstract Details" first to edit proofing fields.', 'Warning', {
                                progressBar: true,
                                timeOut: 3000
                            });
                        }
                    }
                });
                
                // Initially set proofing fields to readonly
                $('.proofingEditText').prop('readonly', true).addClass('readonly-mode');
            });
            </script>
        </div>
        <!-- abstract edit pop up -->
    </div>
</div>
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
            url: 'abstract_submited.php',
            type: 'POST',
            data: {
                editabstractId: userId,
            },
              beforeSend: showEditRegLoader,
            success: function (response) {
                $('#abstract_edit').html($(response).find('#abstract_edit').html());
                // Then show the popup
                initabstractedit();

                $('#abstract_edit').fadeIn();
            },
             complete: hideEditRegLoader,
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

        // if (!confirm(confirmMsg)) {
        //     // User clicked Cancel → revert toggle
        //     $this.prop('checked', !status);
        //     return; // stop AJAX
        // }

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
                // alert("Reviewer updated successfully!");
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

<script>
    function showEditRegLoader() {
        $('.editreg-loader').css('display', 'flex');
    }
    function hideEditRegLoader() {
        $('.editreg-loader').hide();
    }
    </script>
<script>
   document.addEventListener("click", function(e) {
        if (e.target.closest(".popup_close")) {
            e.preventDefault(); // stops form submit
            $(".pop_up_wrap").hide(); // blur appears
            $(".pop_up_body").hide();
        }


    });
    function initabstractedit () {
    $(".addCoAuthor").click(function(e){

    e.preventDefault();

    var html = $("#coauthor_template").html();

    $(".accm_add_wrap").append(html);

    $(".accm_add_empty").remove(); // remove "No Co-Author Added"
   
});
// $(document).on("click",".accm_delet",function(e){

//     e.preventDefault();

//     $(this).closest(".accm_add_box").remove();

// });
 $(document).ready(function() {
         $('.proofingEditText').prop('disabled', true);
        function toggleProofing() {
            if ($('.toggleswitch-checkbox-Proofing').is(':checked')) {
                $('.proofingEditText').prop('disabled', false);
                 $('#proofingEdit').val('yes')
            } else {
                $('.proofingEditText').prop('disabled', true);
                 $('#proofingEdit').val('no')
            }
        }

        // Run on page load
        toggleProofing();

        // Run on toggle change
        $(document).on('change', '.toggleswitch-checkbox-Proofing', function() {
            toggleProofing();
        });

    });
}
document.addEventListener("DOMContentLoaded", initabstractedit);
function generateSateListCo(countryId,jBaseUrl,stateId,selectedStateId)
{
    if(countryId!=""){
        $.ajax({
            type: "POST",
            url: jBaseUrl+"returnData.process.php",
            data: "act=generateStateList&countryId="+countryId,
            dataType: "html",
            async: false,
            success: function(JSONObject){
                
                $("#"+stateId).html(JSONObject);
                $("#"+stateId).removeAttr("disabled");

                if(selectedStateId!=''){
                    $("#"+stateId).val(selectedStateId);
                }

            }
        });
    }else{
        $("#"+stateId).html('<option value="">-- Select Country First --</option>');
        $("#"+stateId).attr("disabled","disabled");
    }
}
</script>
<script>
$(document).ready(function () {
    function loadTopicsByCategory(categoryId, selectedTopicId) {
        var $topicSelect = $('#src_abstract_topic_id');

        if (!categoryId) {
            $topicSelect.html('<option value="">-- Select Topic --</option>');
            return;
        }

        $.ajax({
            type: "POST",
            url: jsBASE_URL + "returnData.process.php",
            data: {
                act: "getAbstractTopicsByCategory",
                categoryId: categoryId
            },
            dataType: "html",
            success: function (response) {
                $topicSelect.html(response);
                if (selectedTopicId) {
                    $topicSelect.val(selectedTopicId);
                }
            }
        });
    }

    $('#src_abstract_category_id').on('change', function () {
        loadTopicsByCategory($(this).val(), '');
    });
});
</script>
<?php
function getAbstractDecisionCounts($abstractId)
{
    global $mycms;

    $sql = array();
    $sql['QUERY'] = "SELECT d.review_result_id, d.review_obtained_marks AS decision
                        FROM " . _DB_ABSTRACT_REVIEW_RESULT_DETAILS_ . " d
                  INNER JOIN " . _DB_ABSTRACT_REVIEW_RESULT_ . " r
                          ON r.id = d.review_result_id
                         AND r.status = 'A'
                  INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " f
                          ON f.id = r.faculty_id
                         AND f.status = 'A'
                       WHERE r.abstract_id = '" . (int) $abstractId . "'
                         AND d.status = 'A'
                         AND d.review_obtained_marks IN ('Accepted', 'Rejected')";

    $res = $mycms->sql_select($sql);

    $counts = array('Accepted' => 0, 'Rejected' => 0);
    $seen   = array();

    if ($res) {
        foreach ($res as $row) {
            // one decision per reviewer submission, in case a reviewer
            // somehow has more than one grade row saved
            if (isset($seen[$row['review_result_id']])) {
                continue;
            }
            $seen[$row['review_result_id']] = true;

            if ($row['decision'] === 'Accepted') {
                $counts['Accepted']++;
            } elseif ($row['decision'] === 'Rejected') {
                $counts['Rejected']++;
            }
        }
    }

    return $counts;
}

/**
 * Returns the majority verdict for a grade-based abstract, or null
 * if no Accepted/Rejected decisions exist (i.e. it's not grade-based
 * or hasn't been reviewed yet).
 */
function getAbstractGradeVerdict($abstractId)
{
    $counts   = getAbstractDecisionCounts($abstractId);
    $accepted = $counts['Accepted'];
    $rejected = $counts['Rejected'];
    $total    = $accepted + $rejected;

    if ($total === 0) {
        return null;
    }

    if ($accepted > $rejected) {
        $label = 'Accepted';
    } elseif ($rejected > $accepted) {
        $label = 'Rejected';
    } else {
        $label = 'Accepted/Rejected'; // tie
    }

    return array(
        'label'    => $label,
        'accepted' => $accepted,
        'rejected' => $rejected,
        'total'    => $total,
    );
}
?>