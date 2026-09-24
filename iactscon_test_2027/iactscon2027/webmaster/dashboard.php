<?php
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php');
?>
<?
$sqlMSG = array();
$sqlMSG['QUERY'] = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
			WHERE `id` = 1";
$result = $mycms->sql_select($sqlMSG);
$companyInfo = $result[0];
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Dashboard</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav class="dashboard_nav"><?= $companyInfo['company_conf_name'] ?> • Command Center</nav>
                <h2 class="dashboard_head"><?= $companyInfo['company_conf_full_name'] ?></h2>
                <div class="dashboard_date">
                    <div class="display-date">
                        <span id="day">day</span>,
                        <span id="daynum">00</span>
                        <span id="month">month</span>
                        <span id="year">0000</span>
                    </div>
                    <?php
                    $sqluser = array();
                    $sqluser['QUERY'] = "SELECT COUNT(id) AS total FROM " . _login_records_ . date("Ym") . "_" . "
                                        WHERE userType = 'A' 
                                        AND logoutIp IS NULL";

                    $resultuser = $mycms->sql_select($sqluser);
                    $user = $resultuser[0]['total'];
                    ?>
                    • <span class="text_success"><?= $user ?> Desk Active</span>
                </div>
            </div>
            <div class="page_top_wrap_right">
                <div class="dashboard_time">
                    <?php clock() ?>
                    <div class="display-time"></div>
                </div>
                <!-- <a href="#" class="badge_default dash_new_entry"><?php add() ?>New Entry</a> -->
            </div>
        </div>

        <div class="form_grid dash_grid g_3">
            <div class="dash_top span_3">
                <div class="dash_top_left d-none">
                    <h5>Mission Revenue</h5>
                    <h2>₹ 0.33L</h2>
                    <h6><span class="badge_success">+12% Target</span>Real-time gateway sync</h6>
                </div>
                <div class="dash_top_right">
                    <div class="dash_top_right_box">
                        <h6>Registration</h6>
                        <?
                        // --------------------
                        function formatIndianCurrency($amount)
                        {
                            if ($amount >= 10000000)
                                return round($amount / 10000000, 2) . ' Cr';
                            if ($amount >= 100000)
                                return round($amount / 100000, 2) . ' L';
                            return number_format($amount);
                        }
                        $registrantIdsResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT id
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status = 'A'
                               AND (
                                    isRegistration = 'Y'
                                    OR registration_request = 'ONLYWORKSHOP'
                                )
                                AND user_type = 'DELEGATE'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                            "
                        ]);

                        $registrantIds = array_column($registrantIdsResult, 'id'); // extract IDs into array
                        $idsString = implode(',', $registrantIds); // convert to comma-separated list
                        //  echo "<pre>";
                        if (!empty($idsString)) {

                            // Total revenue
                            $totalRevenueResult = $mycms->sql_select([
                                'QUERY' => "
                                SELECT SUM(amount) AS total_revenue
                                FROM " . _DB_PAYMENT_ . "
                                WHERE payment_status = 'PAID'
                                AND status = 'A'
                                 AND  delegate_id IN ($idsString)

                            "
                            ]);
                        }
                        $totalRevenue = (float) ($totalRevenueResult[0]['total_revenue'] ?? 0);

                        ?>
                        <h4>₹ <?php echo formatIndianCurrency($totalRevenue); ?></h4>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_primary" data-progress="20"></div>
                            </div>
                        </div>
                    </div>
                    <div class="dash_top_right_box">
                        <?
                        $workshopSlipId = $mycms->sql_select([
                            'QUERY' => "
                                SELECT slip_id
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                               AND service_type ='DELEGATE_WORKSHOP_REGISTRATION'
                            "
                        ]);

                        $SlipId = array_column($workshopSlipId, 'slip_id'); // extract IDs into array
                        $SlipidsString = implode(',', $SlipId); // convert to comma-separated list
                        if (!empty($SlipidsString)) {

                            // Total revenue
                            $totalworkshopRevenueResult = $mycms->sql_select([
                                'QUERY' => "
                                SELECT SUM(amount) AS total_revenue_workshop
                                FROM " . _DB_PAYMENT_ . "
                                WHERE payment_status = 'PAID'
                                AND status = 'A'
                                 AND  slip_id IN ($SlipidsString)

                            "
                            ]);
                        }
                        $totalRevenueworkshop = (float) ($totalworkshopRevenueResult[0]['total_revenue_workshop'] ?? 0);

                        ?>
                        <h6>Workshop</h6>
                        <h4>₹ <?php echo formatIndianCurrency($totalRevenueworkshop); ?></h4>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_info" data-progress="20"></div>
                            </div>
                        </div>
                    </div>
                    <div class="dash_top_right_box">
                        <?
                        $registrantIdsAcc = $mycms->sql_select([
                            'QUERY' => "
                                SELECT refference_delegate_id
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status = 'A'
                               AND (
                                    isRegistration = 'Y'
                                    OR registration_request = 'ONLYWORKSHOP'
                                )
                                AND user_type = 'ACCOMPANY'
                            "
                        ]);

                        $AccompanyIds = array_column($registrantIdsAcc, 'refference_delegate_id'); // extract IDs into array
                        $accompanyidsString = implode(',', $AccompanyIds); // convert to comma-separated list
                        if (!empty($accompanyidsString)) {
                            $accSlipId = $mycms->sql_select([
                                'QUERY' => "
                                SELECT SUM(service_roundoff_price) AS total_revenue_acc
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='ACCOMPANY_CONFERENCE_REGISTRATION'
                                 AND  delegate_id IN ($accompanyidsString)
                                 AND  payment_status = 'PAID'

                            "
                            ]);
                            $totalRevenueAcc = (float) ($accSlipId[0]['total_revenue_acc'] ?? 0);

                        }
                        ?>
                        <h6>Accompany</h6>
                        <h4>₹ <?php echo formatIndianCurrency($totalRevenueAcc); ?></h4>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_secondary" data-progress="20"></div>
                            </div>
                        </div>
                    </div>
                    <div class="dash_top_right_box">
                        <?
                        if (!empty($SlipidsString)) {
                            $accoSlipId = $mycms->sql_select([
                                'QUERY' => "
                                SELECT SUM(service_roundoff_price) AS total_revenue_acco
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_ACCOMMODATION_REQUEST'
                                AND  payment_status = 'PAID'
                                AND  delegate_id IN ($idsString)
                            "
                            ]);


                            $totalRevenueAcco = (float) ($accoSlipId[0]['total_revenue_acco'] ?? 0);
                        }
                        ?>
                        <h6>Accomodation</h6>
                        <h4>₹ <?php echo formatIndianCurrency($totalRevenueAcco); ?></h4>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_success" data-progress="20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dash_mid span_3">
                <ul class="regi_data_grid_ul accm_data_grid_ul">
                    <a href="#">
                        <li class="overflow-hidden">
                            <?
                            if (!empty($idsString)) {
                                $totalRegist = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(DISTINCT delegate_id) AS total_paid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_CONFERENCE_REGISTRATION'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'PAID'

                            "
                                ]);
                                $totalPaidresult = !empty($totalRegist) ? (int) $totalRegist[0]['total_paid_registration'] : 0;

                                $totalRegistUnpaid = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(DISTINCT delegate_id) AS total_unpaid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_CONFERENCE_REGISTRATION'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'UNPAID'

                            "
                                ]);
                                $totalUnpaidresult = !empty($totalRegistUnpaid) ? (int) $totalRegistUnpaid[0]['total_unpaid_registration'] : 0;

                                $totalRegistcomp = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(DISTINCT delegate_id) AS total_comp_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_CONFERENCE_REGISTRATION'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'COMPLIMENTARY'

                            "
                                ]);
                                $totalcompresult = !empty($totalRegistcomp) ? (int) $totalRegistcomp[0]['total_comp_registration'] : 0;
                            }
                            ?>
                            <div class="dash_mid_top">
                                <span class="badge_primary"><?php user() ?></span>
                                <h4>Registration</h4>
                                <j><i class="fal fa-arrow-right"></i></j>
                            </div>
                            <ol>
                                <li class="text_primary"><i>Paid</i><?= $totalPaidresult ?></li>
                                <li><i>Unpaid</i><?= $totalUnpaidresult ?></li>
                                <li><i>Comp</i><?= $totalcompresult ?></li>
                            </ol>
                            <n class="dash_grid_component text_primary"><?php user() ?></n>
                        </li>
                    </a>
                    <a href="#">
                        <li class="overflow-hidden">
                            <?
                            if (!empty($idsString)) {
                                $totalRegistWorkshop = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(delegate_id) AS total_paid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_WORKSHOP_REGISTRATION'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'PAID'

                            "
                                ]);
                                $totalPaidresultWorkshop = !empty($totalRegistWorkshop) ? (int) $totalRegistWorkshop[0]['total_paid_registration'] : 0;


                                $totalRegistUnpaidWorkshop = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(delegate_id) AS total_unpaid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_WORKSHOP_REGISTRATION'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'UNPAID'

                            "
                                ]);
                                $totalUnpaidresultWorkshop = !empty($totalRegistUnpaidWorkshop) ? (int) $totalRegistUnpaidWorkshop[0]['total_unpaid_registration'] : 0;

                                $totalRegistcompWorkshop = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(delegate_id) AS total_comp_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_WORKSHOP_REGISTRATION'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'COMPLIMENTARY'

                            "
                                ]);
                                $totalcompresultWorkshop = !empty($totalRegistcompWorkshop) ? (int) $totalRegistcompWorkshop[0]['total_comp_registration'] : 0;
                            }
                            ?>
                            <div class="dash_mid_top">
                                <span class="badge_info"><?php workshop() ?></span>
                                <h4>Workshop Registration</h4>
                                <j><i class="fal fa-arrow-right"></i></j>
                            </div>
                            <ol>
                                <li class="text_info"><i>Paid</i><?= $totalPaidresultWorkshop ?></li>
                                <li><i>Unpaid</i><?= $totalUnpaidresultWorkshop ?></li>
                                <li><i>Comp</i><?= $totalcompresultWorkshop ?></li>
                            </ol>
                            <n class="dash_grid_component text_info"><?php workshop() ?></n>
                        </li>
                    </a>
                    <a href="#">
                        <li class="overflow-hidden">
                            <?
                            if (!empty($accompanyidsString)) {
                                $totalRegistAcc = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count( delegate_id) AS total_paid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='ACCOMPANY_CONFERENCE_REGISTRATION'
                                AND  delegate_id IN ($accompanyidsString)
                                AND  payment_status = 'PAID'

                            "
                                ]);
                                $totalPaidresultAcc = !empty($totalRegistAcc) ? (int) $totalRegistAcc[0]['total_paid_registration'] : 0;

                                $totalRegistUnpaidAcc = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count( delegate_id) AS total_unpaid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='ACCOMPANY_CONFERENCE_REGISTRATION'
                                AND  delegate_id IN ($accompanyidsString)
                                AND  payment_status = 'UNPAID'

                            "
                                ]);
                                $totalUnpaidresultAcc = !empty($totalRegistUnpaidAcc) ? (int) $totalRegistUnpaidAcc[0]['total_unpaid_registration'] : 0;

                                $totalRegistcompAcc = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count( delegate_id) AS total_comp_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='ACCOMPANY_CONFERENCE_REGISTRATION'
                                AND  delegate_id IN ($accompanyidsString)
                                AND  payment_status = 'COMPLIMENTARY'

                            "
                                ]);
                                $totalcompresultAcc = !empty($totalRegistcompAcc) ? (int) $totalRegistcompAcc[0]['total_comp_registration'] : 0;
                            }
                            ?>
                            <div class="dash_mid_top">
                                <span class="badge_secondary"><?php duser() ?></span>
                                <h4>Accompanying Person</h4>
                                <j><i class="fal fa-arrow-right"></i></j>
                            </div>
                            <ol>
                                <li class="text_secondary"><i>Total</i><?= $totalPaidresultAcc ?? 0 ?></li>
                                <li><i>Unpaid</i><?= $totalUnpaidresultAcc ?? 0 ?></li>
                                <li><i>Comp</i><?= $totalcompresultAcc ?? 0 ?></li>
                            </ol>
                            <n class="dash_grid_component text_secondary"><?php duser() ?></n>
                        </li>
                    </a>
                    <a href="#">
                        <li class="overflow-hidden">
                            <?
                            if (!empty($idsString)) {
                                $totalRegistAcco = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(delegate_id) AS total_paid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_ACCOMMODATION_REQUEST'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'PAID'

                            "
                                ]);
                                $totalPaidresultAcco = !empty($totalRegistAcco) ? (int) $totalRegistAcco[0]['total_paid_registration'] : 0;


                                $totalRegistUnpaidAcco = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(delegate_id) AS total_unpaid_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_ACCOMMODATION_REQUEST'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'UNPAID'

                            "
                                ]);
                                $totalUnpaidresultAcco = !empty($totalRegistUnpaidAcco) ? (int) $totalRegistUnpaidAcco[0]['total_unpaid_registration'] : 0;

                                $totalRegistcompAcco = $mycms->sql_select([
                                    'QUERY' => "
                                SELECT count(delegate_id) AS total_comp_registration
                                FROM " . _DB_INVOICE_ . "
                                WHERE status = 'A'
                                AND service_type ='DELEGATE_ACCOMMODATION_REQUEST'
                                AND  delegate_id IN ($idsString)
                                AND  payment_status = 'COMPLIMENTARY'

                            "
                                ]);
                                $totalcompresultAcco = !empty($totalRegistcompAcco) ? (int) $totalRegistcompAcco[0]['total_comp_registration'] : 0;
                            }
                            ?>
                            <div class="dash_mid_top">
                                <span class="badge_danger"><?php hotel() ?></span>
                                <h4>Accommodation</h4>
                                <j><i class="fal fa-arrow-right"></i></j>
                            </div>
                            <ol>
                                <li class="text_danger"><i>Total</i><?= $totalPaidresultAcco ?></li>
                                <li><i>Unpaid</i><?= $totalUnpaidresultAcco ?></li>
                                <li><i>Comp</i><?= $totalcompresultAcco ?></li>
                            </ol>
                            <n class="dash_grid_component text_danger"><?php hotel() ?></n>
                        </li>
                    </a>
                    <a href="#">
                        <li class="overflow-hidden">
                            <div class="dash_mid_top">
                                <span class="badge_default"><?php exibitor() ?></span>
                                <h4>Exhibitor Stalls</h4>
                                <j><i class="fal fa-arrow-right"></i></j>
                            </div>
                            <ol>
                                <li class="text_default"><i>Total</i>0</li>
                                <li><i>Unpaid</i>0</li>
                                <li><i>Comp</i>0</li>
                            </ol>
                            <n class="dash_grid_component text_default"><?php exibitor() ?></n>
                        </li>
                    </a>
                    <a href="#">
                        <li class="overflow-hidden">
                            <div class="dash_mid_top">
                                <span class="badge_partial"><i class="fal fa-clipboard-list"></i></span>
                                <h4>Abstract</h4>
                                <j><i class="fal fa-arrow-right"></i></j>
                            </div>
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
                            <ol>
                                <li class="text_partial"><i>Submitted</i><?= $result[0]['totalAbstractCount'] ?? 0 ?>
                                </li>
                                <li><i>Accepted</i><?= $resultAcc[0]['totalAbstractCountAcc'] ?? 0 ?></li>
                                <li><i>Rejected</i><?= $resultRej[0]['totalAbstractCountRej'] ?? 0 ?></li>
                            </ol>
                            <n class="dash_grid_component text_partial"><i class="fal fa-clipboard-list"></i></n>
                        </li>
                    </a>
                </ul>
            </div>
            <div class="dash_left span_2">
                <h4 class="dash_oper_head">Operational Quick Launch</h4>
                <div class="form_grid dash_grid g_2">
                    <a href="#" class="shrtcut badge_primary shrtcut_hover">
                        <span class="badge_primary"><?php printi() ?></span>
                        <p>ID Card Printing
                            <n class="text_dark">Bulk Badge Management</n>
                        </p>
                    </a>
                    <a href="#" class="shrtcut badge_secondary shrtcut_hover">
                        <span class="badge_secondary"><?php printi() ?></span>
                        <p>ID Card Printing
                            <n class="text_dark">Bulk Badge Management</n>
                        </p>
                    </a>


                    <div class="wrkshp_status span_2">
                        <h4 class="dash_head"><i class="fal fa-microphone-stand"></i>Current Scientific Session</h4>
                        <div class="form_grid dash_grid g_2 wrkshp_status_wrap">
                            <div class="wrkshp_status_box">
                                <h6>
                                    <g>Hall A • Main Auditorium</g><span><?php address() ?></span>
                                </h6>
                                <h4>TB Management in 2025</h4>
                                <h5>Speaker: Dr. Rajesh K<span class='timer'></span></h5>
                                <div class="timerbar">
                                    <div class="bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dash_right span_1">
                <div class="monitor_right w-100">
                    <h5 class="monitor_right_head">Live Activity Feed<span>Real-time</span></h5>
                    <ul>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_info"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                        <li>
                            <span class="badge_success"><i class="fal fa-print"></i></span>
                            <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • <g>System</g></b>
                            </p>
                        </li>
                    </ul>
                    <div class="monitor_bottom"><a href="#" class="badge_dark">OPEN FULL MONITOR</a></div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    function timer(timeleft, timetotal, $element) {
        var progressBarWidth = timeleft * $element.width() / timetotal;
        $element.find('div.bar').animate({
            width: progressBarWidth
        }, timeleft == timetotal ? 0 : 1000, "linear");
        if (timeleft > 0) {
            setTimeout(function () {
                timer(timeleft - 1, timetotal, $element);
            }, 1000);
        }
        var date = new Date(null);
        date.setSeconds(timeleft);
        var timeString = date.toISOString().substr(11, 8);
        var newtimeleft = timeString

        $('.timer').text(newtimeleft)
    };

    timer(3600, 3600, $('.timerbar'));
    const displayTime = document.querySelector(".display-time");
    // Time
    function showTime() {
        let time = new Date();
        displayTime.innerText = time.toLocaleTimeString("en-US", {
            hour12: false
        });
        setTimeout(showTime, 1000);
    }

    showTime();

    // Date
    function updateDate() {
        let today = new Date();

        // return number
        let dayName = today.getDay(),
            dayNum = today.getDate(),
            month = today.getMonth(),
            year = today.getFullYear();

        const months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];
        const dayWeek = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
        ];
        // value -> ID of the html element
        const IDCollection = ["day", "daynum", "month", "year"];
        // return value array with number as a index
        const val = [dayWeek[dayName], dayNum, months[month], year];
        for (let i = 0; i < IDCollection.length; i++) {
            document.getElementById(IDCollection[i]).firstChild.nodeValue = val[i];
        }
    }

    updateDate();
</script>

</html>