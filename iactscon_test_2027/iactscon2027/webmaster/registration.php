<?php 
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php');

// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
?>

<?php
$indexVal          = 1;
$pageKey           = "_pgn" . $indexVal . "_";

$pageKeyVal        = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString     = "";
$searchArray       = array();

$searchArray[$pageKey]                     = $pageKeyVal;



if (isset($_REQUEST['src_registration']) && trim($_REQUEST['src_registration']) != '') {
	$searchArray['src_registration']                   = addslashes(trim($_REQUEST['src_registration']));
}
if (isset($_REQUEST['src_email_id']) && trim($_REQUEST['src_email_id']) != '') {
	$searchArray['src_email_id']                       = addslashes(trim($_REQUEST['src_email_id']));
}
if (isset($_REQUEST['src_access_key']) && trim($_REQUEST['src_access_key']) != '') {
	$searchArray['src_access_key']                     = addslashes(trim($_REQUEST['src_access_key'], '#'));
}
if (isset($_REQUEST['src_mobile_no']) && trim($_REQUEST['src_mobile_no']) != '') {
	$searchArray['src_mobile_no']                      = addslashes(trim($_REQUEST['src_mobile_no']));
}
if (isset($_REQUEST['src_user_first_name']) && trim($_REQUEST['src_user_first_name']) != '') {
	$searchArray['src_user_first_name']                = addslashes(trim($_REQUEST['src_user_first_name']));
}
if (isset($_REQUEST['src_user_middle_name']) && trim($_REQUEST['src_user_middle_name']) != '') {
	$searchArray['src_user_middle_name']               = addslashes(trim($_REQUEST['src_user_middle_name']));
}
if (isset($_REQUEST['src_user_full_name']) && trim($_REQUEST['src_user_full_name']) != '') {
	$searchArray['src_user_full_name']                = addslashes(trim($_REQUEST['src_user_full_name']));
}
if (isset($_REQUEST['src_invoice_no']) && trim($_REQUEST['src_invoice_no']) != '') {
	$searchArray['src_invoice_no']                     = addslashes(trim($_REQUEST['src_invoice_no']));
}
if (isset($_REQUEST['src_slip_no']) && trim($_REQUEST['src_slip_no']) != '') {
	$searchArray['src_slip_no']                        = addslashes(trim($_REQUEST['src_slip_no'], '##'));
}
if (isset($_REQUEST['src_registration_mode']) && trim($_REQUEST['src_registration_mode']) != '') {
	$searchArray['src_registration_mode']              = addslashes(trim($_REQUEST['src_registration_mode']));
}
if (isset($_REQUEST['src_user_last_name']) && trim($_REQUEST['src_user_last_name']) != '') {
	$searchArray['src_user_last_name']                 = addslashes(trim($_REQUEST['src_user_last_name']));
}
if (isset($_REQUEST['src_atom_transaction_ids']) && trim($_REQUEST['src_atom_transaction_ids']) != '') {
	$searchArray['src_atom_transaction_ids']           = addslashes(trim($_REQUEST['src_atom_transaction_ids']));
}
if (isset($_REQUEST['src_transaction_ids']) && trim($_REQUEST['src_transaction_ids']) != '') {
	$searchArray['src_transaction_ids']                = addslashes(trim($_REQUEST['src_transaction_ids']));
}
if (isset($_REQUEST['src_conf_reg_category']) && trim($_REQUEST['src_conf_reg_category']) != '') {
	$searchArray['src_conf_reg_category']              = addslashes(trim($_REQUEST['src_conf_reg_category']));
}
if (isset($_REQUEST['src_reg_category']) && trim($_REQUEST['src_reg_category']) != '') {
	$searchArray['src_reg_category']        		   = addslashes(trim($_REQUEST['src_reg_category']));
}
if (isset($_REQUEST['src_registration_id']) && trim($_REQUEST['src_registration_id']) != '') {
	$searchArray['src_registration_id']                = addslashes(trim($_REQUEST['src_registration_id']));
}
if (isset($_REQUEST['src_workshop_classf']) && trim($_REQUEST['src_workshop_classf']) != '') {
	$searchArray['src_workshop_classf']                = addslashes(trim($_REQUEST['src_workshop_classf']));
}
if (isset($_REQUEST['src_transaction_id']) && trim($_REQUEST['src_transaction_id']) != '') {
	$searchArray['src_transaction_id']                 = addslashes(trim($_REQUEST['src_transaction_id']));
}
if (isset($_REQUEST['src_payment_mode']) && trim($_REQUEST['src_payment_mode']) != '') {
	$searchArray['src_payment_mode']                   = addslashes(trim($_REQUEST['src_payment_mode']));
}
if (isset($_REQUEST['src_payment_status']) && trim($_REQUEST['src_payment_status']) != '') {
	$searchArray['src_payment_status']                 = addslashes(trim($_REQUEST['src_payment_status']));
}
if (isset($_REQUEST['src_accommodation']) && trim($_REQUEST['src_accommodation']) != '') {
	$searchArray['src_accommodation']                  = addslashes(trim($_REQUEST['src_accommodation']));
}
if (isset($_REQUEST['src_mobile_isd_code']) && trim($_REQUEST['src_mobile_isd_code']) != '') {
	$searchArray['src_mobile_isd_code']                = addslashes(trim($_REQUEST['src_mobile_isd_code']));
}
if (isset($_REQUEST['src_registration_type']) && trim($_REQUEST['src_registration_type']) != '') {
	$searchArray['src_registration_type']              = addslashes(trim($_REQUEST['src_registration_type']));
}
if (isset($_REQUEST['src_payment_date']) && trim($_REQUEST['src_payment_date']) != '') {
	$searchArray['src_payment_date']                   = addslashes(trim($_REQUEST['src_payment_date']));
}
if (isset($_REQUEST['src_cancel_invoice_id']) && trim($_REQUEST['src_cancel_invoice_id']) != '') {
	$searchArray['src_cancel_invoice_id']              = addslashes(trim($_REQUEST['src_cancel_invoice_id']));
}
if (isset($_REQUEST['src_transaction_slip_no']) && trim($_REQUEST['src_transaction_slip_no']) != '') {
	$searchArray['src_transaction_slip_no']            = addslashes(trim($_REQUEST['src_transaction_slip_no']));
}
if (isset($_REQUEST['src_registration_from_date']) && trim($_REQUEST['src_registration_from_date']) != '') {
	$searchArray['src_registration_from_date']         = addslashes(trim($_REQUEST['src_registration_from_date']));
}
if (isset($_REQUEST['src_registration_to_date']) && trim($_REQUEST['src_registration_to_date']) != '') {
	$searchArray['src_registration_to_date']           = addslashes(trim($_REQUEST['src_registration_to_date']));
}

if (isset($_REQUEST['src_hasPickup']) && trim($_REQUEST['src_hasPickup']) != '') {
	$searchArray['src_hasPickup']           		   = addslashes(trim($_REQUEST['src_hasPickup']));
}
if (isset($_REQUEST['src_hasDropoff']) && trim($_REQUEST['src_hasDropoff']) != '') {
	$searchArray['src_hasDropoff']           		   = addslashes(trim($_REQUEST['src_hasDropoff']));
}
if (isset($_REQUEST['src_hasTotPlus']) && trim($_REQUEST['src_hasTotPlus']) != '') {
	$searchArray['src_hasTotPlus']           		   = addslashes(trim($_REQUEST['src_hasTotPlus']));
}
if (isset($_REQUEST['src_hasLapSutur']) && trim($_REQUEST['src_hasLapSutur']) != '') {
	$searchArray['src_hasLapSutur']           		   = addslashes(trim($_REQUEST['src_hasLapSutur']));
}
if (isset($_REQUEST['src_has3rd4th']) && trim($_REQUEST['src_has3rd4th']) != '') {
	$searchArray['src_has3rd4th']           		   = addslashes(trim($_REQUEST['src_has3rd4th']));
}
if (isset($_REQUEST['src_hasCerviCancer']) && trim($_REQUEST['src_hasCerviCancer']) != '') {
	$searchArray['src_hasCerviCancer']           	   = addslashes(trim($_REQUEST['src_hasCerviCancer']));
}
if (isset($_REQUEST['src_hasGalaDinner']) && trim($_REQUEST['src_hasGalaDinner']) != '') {
	$searchArray['src_hasGalaDinner']           	   = addslashes(trim($_REQUEST['src_hasGalaDinner']));
}
if (isset($_REQUEST['src_hasAccompany']) && trim($_REQUEST['src_hasAccompany']) != '') {
	$searchArray['src_hasAccompany']           		   = addslashes(trim($_REQUEST['src_hasAccompany']));
}
if (isset($_REQUEST['src_hasAbstract']) && trim($_REQUEST['src_hasAbstract']) != '') {
	$searchArray['src_hasAbstract']           		   = addslashes(trim($_REQUEST['src_hasAbstract']));
}
if (isset($_REQUEST['src_hasAccommodation']) && trim($_REQUEST['src_hasAccommodation']) != '') {
	$searchArray['src_hasAccommodation']           	   = addslashes(trim($_REQUEST['src_hasAccommodation']));
}
if (isset($_REQUEST['src_hasUnpaidInvoice']) && trim($_REQUEST['src_hasUnpaidInvoice']) != '') {
	$searchArray['src_hasUnpaidInvoice']           	   = addslashes(trim($_REQUEST['src_hasUnpaidInvoice']));
}

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}

?>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>General Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registration List</li>
                    </ol>
                </nav>
                <h2>Participant Overview</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>
        <ul class="regi_data_grid_ul mb-3">
            <li>
                <div>
                    <?php
                    function getTariffCurrentCutoffId($cutoffId = "", $currentDate = "")
                    {		
                        global $cfg, $mycms;		
                        
                        $cutoffValue = ['data' => null]; // default
                        
                        if ($cutoffId == "") {
                            $sqlcutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " WHERE status = ?";
                            $sqlcutoff['PARAM'][] = [
                                'FILD' => 'status',
                                'DATA' => 'A',
                                'TYP'  => 's'
                            ];
                            $rescutoff = $mycms->sql_select($sqlcutoff);

                            if ($currentDate == '') {
                                $currentDate = date('Y-m-d');
                            }

                            foreach ($rescutoff as $valcutoff) {
                                $startDate = strtotime($valcutoff['start_date']);
                                $endDate   = strtotime($valcutoff['end_date']);
                                $current   = strtotime($currentDate);

                                if ($current >= $startDate && $current <= $endDate) {
                                    $cutoffValue['data'] = [
                                        'id'   => $valcutoff['id'],
                                        'name' => $valcutoff['cutoff_title']
                                    ];
                                    break; // stop at first match
                                }
                            }

                        } else {
                            $cutoffValue['data'] = ['id' => $cutoffId];
                        }

                        return $cutoffValue['data'];
                    }
                      $currentCutoff  = getTariffCurrentCutoffId();
                      $cutOff_cutoffId =$currentCutoff['id'];
                      $registrantIdsResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT id
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status = 'A'
                               AND (
                                    isRegistration = 'Y'
                                )
                                AND user_type = 'DELEGATE'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                            "
                        ]);

                        $registrantIds = array_column($registrantIdsResult, 'id'); // extract IDs into array
                         $idsString = implode(',', $registrantIds); // convert to comma-separated list
                        //  echo "<pre>";
                        //  print_r($idsString);
                        $totalResult = $mycms->sql_select([
                                        'QUERY' => "
                                            SELECT COUNT(*) AS total_registrants
                                            FROM " . _DB_USER_REGISTRATION_ . "
                                            WHERE status = 'A'
                                            AND (
                                                isRegistration = 'Y'
                                                )
                                            AND registration_payment_status != 'UNPAID'
                                            AND user_type = 'DELEGATE'
                                            AND operational_area NOT IN ('EXHIBITOR','GUEST')

                                        "
                                    ]);

                        $totalRegistrants = !empty($totalResult)
                            ? (int)$totalResult[0]['total_registrants']
                            : 0;
                        $thisWeekResult = $mycms->sql_select([
                                'QUERY' => "
                                    SELECT COUNT(*) AS this_week_total
                                    FROM " . _DB_USER_REGISTRATION_ . "
                                    WHERE status = 'A'
                                    AND (
                                                isRegistration = 'Y'
                                                )
                                    AND user_type = 'DELEGATE'
                                    AND registration_payment_status != 'UNPAID'
                                    AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                    AND created_dateTime >= NOW() - INTERVAL 7 DAY
                                "
                            ]);

                        $thisWeekTotal = !empty($thisWeekResult)
                            ? (int)$thisWeekResult[0]['this_week_total']
                            : 0;
                        $lastWeekResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT COUNT(*) AS last_week_total
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status = 'A'
                                 AND (
                                    isRegistration = 'Y'
                                    )
                                AND user_type = 'DELEGATE'
                                AND registration_payment_status != 'UNPAID'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                AND created_dateTime >= NOW() - INTERVAL 14 DAY
                                AND created_dateTime < NOW() - INTERVAL 7 DAY
                            "
                        ]);

                        $lastWeekTotal = !empty($lastWeekResult)
                        ? (int)$lastWeekResult[0]['last_week_total']
                        : 0;
                        $percentageChange = 0;

                        if ($lastWeekTotal > 0) {
                            $percentageChange = (($thisWeekTotal - $lastWeekTotal) / $lastWeekTotal) * 100;
                        }

                        $percentageChange = round($percentageChange);
                        $trendClass = $percentageChange >= 0 ? 'text_success' : 'text_danger';
                        $trendSign  = $percentageChange >= 0 ? '+' : '';
                        //pending registrants////
                          $pendingResult = $mycms->sql_select([
                                        'QUERY' => "
                                            SELECT COUNT(*) AS total_registrants
                                            FROM " . _DB_USER_REGISTRATION_ . "
                                            WHERE status = 'A'
                                            AND (
                                                isRegistration = 'Y'
                                                )
                                            AND registration_payment_status = 'UNPAID'
                                            AND user_type = 'DELEGATE'
                                            AND operational_area NOT IN ('EXHIBITOR','GUEST')

                                        "
                                    ]);

                        $pendingRegistrants = !empty($pendingResult)
                            ? (int)$pendingResult[0]['total_registrants']
                            : 0;
                        $thisWeekpendingResult = $mycms->sql_select([
                                'QUERY' => "
                                    SELECT COUNT(*) AS this_week_total
                                    FROM " . _DB_USER_REGISTRATION_ . "
                                    WHERE status = 'A'
                                    AND (
                                                isRegistration = 'Y'
                                                )
                                    AND user_type = 'DELEGATE'
                                    AND registration_payment_status = 'UNPAID'
                                    AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                    AND created_dateTime >= NOW() - INTERVAL 7 DAY
                                "
                            ]);

                        $thisWeekTotalpending = !empty($thisWeekpendingResult)
                            ? (int)$thisWeekpendingResult[0]['this_week_total']
                            : 0;
                        $lastWeekResultpending = $mycms->sql_select([
                            'QUERY' => "
                                SELECT COUNT(*) AS last_week_total
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status = 'A'
                                 AND (
                                    isRegistration = 'Y'
                                    )
                                AND user_type = 'DELEGATE'
                                 AND registration_payment_status = 'UNPAID'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                AND created_dateTime >= NOW() - INTERVAL 14 DAY
                                AND created_dateTime < NOW() - INTERVAL 7 DAY
                            "
                        ]);

                        $lastWeekTotalpending = !empty($lastWeekResultpending)
                        ? (int)$lastWeekResultpending[0]['last_week_total']
                        : 0;
                        $percentageChangepending = 0;

                        if ($lastWeekTotalpending > 0) {
                            $percentageChangepending = (($thisWeekTotalpending - $lastWeekTotalpending) / $lastWeekTotalpending) * 100;
                        }

                        $percentageChangepending = round($percentageChangepending);
                        $trendClasspending = $percentageChangepending >= 0 ? 'text_success' : 'text_danger';
                        $trendSignpending  = $percentageChangepending >= 0 ? '+' : '';
                        //////////////////////////////
                        // 2️⃣ REVENUE COLLECTED
                        // --------------------
                        function formatIndianCurrency($amount) {
                            if ($amount >= 10000000) return round($amount/10000000,2).' Cr';
                            if ($amount >= 100000) return round($amount/100000,2).' L';
                            return number_format($amount);
                        }
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
                        $totalRevenue = (float)($totalRevenueResult[0]['total_revenue'] ?? 0);

                        // This week / last week revenue
                         if (!empty($idsString)) {
                        $thisWeekRevenueResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT SUM(amount) AS this_week_revenue
                                FROM " . _DB_PAYMENT_ . "
                                WHERE payment_status = 'PAID'
                                 AND status = 'A'
                                 AND  delegate_id IN ($idsString)
                                AND created_dateTime >= NOW() - INTERVAL 7 DAY
                            "
                        ]);
                         }
                        $thisWeekRevenue = (float)($thisWeekRevenueResult[0]['this_week_revenue'] ?? 0);
                          if (!empty($idsString)) {
                        $lastWeekRevenueResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT SUM(amount) AS last_week_revenue
                                FROM " . _DB_PAYMENT_ . "
                                WHERE payment_status = 'PAID'
                                 AND status = 'A'
                                 AND  delegate_id IN ($idsString)
                                AND created_dateTime >= NOW() - INTERVAL 14 DAY
                                AND created_dateTime < NOW() - INTERVAL 7 DAY
                            "
                        ]);
                          }
                        $lastWeekRevenue = (float)($lastWeekRevenueResult[0]['last_week_revenue'] ?? 0);
 
                        // Safe percentage calculation
                        $revenuePercentageChange = 0;
                        if ($lastWeekRevenue > 0) {
                            $revenuePercentageChange = (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100;
                        }
                        $revenuePercentageChange = round($revenuePercentageChange);
                        $revenueTrendClass = $revenuePercentageChange >= 0 ? 'text_success' : 'text_danger';
                        $revenueTrendSign  = $revenuePercentageChange >= 0 ? '+' : '';

                    // --------------------
                    // 3️⃣ PENDING INVOICE AMOUNT
                    // --------------------
                    if (!empty($idsString)) {

                    $totalPendingResult = $mycms->sql_select([
                        'QUERY' => "
                            SELECT SUM(service_roundoff_price) AS total_pending
                            FROM " . _DB_INVOICE_ . "
                            WHERE (payment_status = 'UNPAID' OR payment_status = '' OR payment_status IS NULL)
                            AND status = 'A'
                            AND delegate_id IN ($idsString)
                        "
                    ]);
                       }
                    $totalPending = (int)($totalPendingResult[0]['total_pending'] ?? 0);
                    if (!empty($idsString)) {

                    $thisWeekPendingResult = $mycms->sql_select([
                        'QUERY' => "
                            SELECT SUM(service_roundoff_price) AS this_week_pending
                            FROM " . _DB_INVOICE_ . "
                            WHERE (payment_status = 'UNPAID' OR payment_status = '' OR payment_status IS NULL)
                            AND status = 'A'
                            AND delegate_id IN ($idsString)
                            AND created_dateTime >= NOW() - INTERVAL 7 DAY
                        "
                    ]);
                    }
                    $thisWeekPending = (int)($thisWeekPendingResult[0]['this_week_pending'] ?? 0);
                    if (!empty($idsString)) {

                    $lastWeekPendingResult = $mycms->sql_select([
                        'QUERY' => "
                            SELECT SUM(service_roundoff_price) AS last_week_pending
                            FROM " . _DB_INVOICE_ . "
                            WHERE (payment_status = 'UNPAID' OR payment_status = '' OR payment_status IS NULL)
                            AND status = 'A'
                            AND delegate_id IN ($idsString)
                            AND created_dateTime >= NOW() - INTERVAL 14 DAY
                            AND created_dateTime < NOW() - INTERVAL 7 DAY
                        "
                    ]);
                    }
                    $lastWeekPending = (int)($lastWeekPendingResult[0]['last_week_pending'] ?? 0);

                        // Safe percentage calculation (fewer pending = good)
                        $pendingPercentageChange = 0;
                        if ($lastWeekPending > 0) {
                            // Reverse sign: fewer pending = negative percentage
                            $pendingPercentageChange = -1 * (($thisWeekPending - $lastWeekPending) / $lastWeekPending) * 100;
                        }
                        $pendingPercentageChange = round($pendingPercentageChange);

                        // Trend class: negative (fewer pending) = text_success, positive (more pending) = text_danger
                        $pendingTrendClass = $pendingPercentageChange < 0 ? 'text_success' : 'text_danger';
                        $pendingTrendSign  = $pendingPercentageChange < 0 ? '' : '+'; // negative will have - automatically

                        if($cutOff_cutoffId!=''){
                        // 2️⃣ Total registrants for this cutoff
                        $cutOff_totalRegistrantsResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT COUNT(*) AS total_registrants
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status='A'
                                AND isRegistration='Y'
                                AND user_type = 'DELEGATE'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                AND registration_tariff_cutoff_id = $cutOff_cutoffId
                            "
                        ]);
                        }
                        $cutOff_totalRegistrants = (int)($cutOff_totalRegistrantsResult[0]['total_registrants'] ?? 0);
                       if($cutOff_cutoffId!=''){
                        // 3️⃣ This week / last week registrants
                        $cutOff_thisWeekResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT COUNT(*) AS this_week_total
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status='A'
                                AND isRegistration='Y'
                                AND user_type = 'DELEGATE'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                AND registration_tariff_cutoff_id = $cutOff_cutoffId
                                AND created_dateTime >= NOW() - INTERVAL 7 DAY
                            "
                        ]);
                       }
                        $cutOff_thisWeekTotal = (int)($cutOff_thisWeekResult[0]['this_week_total'] ?? 0);
                       if($cutOff_cutoffId!=''){
                        $cutOff_lastWeekResult = $mycms->sql_select([
                            'QUERY' => "
                                SELECT COUNT(*) AS last_week_total
                                FROM " . _DB_USER_REGISTRATION_ . "
                                WHERE status='A'
                                AND isRegistration='Y'
                                AND user_type = 'DELEGATE'
                                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                AND registration_tariff_cutoff_id = $cutOff_cutoffId
                                AND created_dateTime >= NOW() - INTERVAL 14 DAY
                                AND created_dateTime < NOW() - INTERVAL 7 DAY
                            "
                        ]);
                       }
                        $cutOff_lastWeekTotal = (int)($cutOff_lastWeekResult[0]['last_week_total'] ?? 0);

                        // 4️⃣ Week-over-week percentage change
                        $cutOff_percentageChange = ($cutOff_lastWeekTotal > 0)
                            ? round((($cutOff_thisWeekTotal - $cutOff_lastWeekTotal)/$cutOff_lastWeekTotal)*100)
                            : 0;

                        $cutOff_trendClass = $cutOff_percentageChange >= 0 ? 'text_success' : 'text_danger';
                        $cutOff_trendSign  = $cutOff_percentageChange >= 0 ? '+' : '';
                    ?>
                    <h5>Total Registrants</h5>
                    <h4><?=$totalRegistrants?></h4>

                    <h6 class="<?php echo $trendClass; ?>">
                         <?php 
                            if ($lastWeekTotal > 0) {
                                echo $trendSign . abs($percentageChange) . '% from last week';
                            } else {
                                echo 'New this week';
                            }
                        ?>
                    </h6>                
                </div>
                <span class="badge_primary"><?php duser(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Revenue Collected</h5>
                    <h4>₹ <?php echo formatIndianCurrency($totalRevenue); ?></h4>
                    <h6 class="<?php echo $revenueTrendClass; ?>">
                        <?php echo $revenueTrendSign . abs($revenuePercentageChange); ?>% from last week
                    </h6>
                </div>
                <span class="badge_success"><?php rupee() ?></span>
            </li>
            <li>
               <div>
                    <h5>Pending Payments</h5>
                    <h4>₹ <?php echo number_format($totalPending); ?></h4>
                     <h6 class="<?php echo $pendingTrendClass; ?>">
                        <?php echo $pendingTrendSign . abs($pendingPercentageChange); ?>% from last week
                    </h6>
                </div>
                <span class="badge_secondary"><?php pending(); ?></span>
            </li>
            <li class="d-none">
                <div>
                    <h5><?=$currentCutoff['name']?></h5>
                    <h4><?= $cutOff_thisWeekTotal ?></h4>
                    <h6 class="<?= $cutOff_trendClass ?>">
                        <?= $cutOff_lastWeekTotal > 0 
                            ? $cutOff_trendSign . $cutOff_percentageChange . '% from last week' 
                            : 'New this week' 
                        ?>
                    </h6>
                </div>
                <span class="badge_info"><?php windowi(); ?></span>
            </li> 
             <li>
                <div>
                    <h5>Pending Registrants</h5>
                     <h4><?=$pendingRegistrants?></h4>
                   <h6 class="<?php echo $trendClasspending; ?>">
                         <?php 
                            if ($lastWeekTotalpending > 0) {
                                echo $trendSignpending . abs($percentageChangepending) . '% from last week';
                            } else {
                                echo 'New this week';
                            }
                        ?>
                    </h6>    
                </div>
                <span class="badge_primary"><?php duser() ?></span>
            </li>
        </ul>

        <div class="regi_search_wrap mb-3">
            <!-- <div class="regi_search">
                <?php search(); ?>
                <input  id="searchInput"  name="src_global_search"  placeholder="Search by Name, Email, Mobile, or Reg ID...">
                <ul id="searchResults"></ul>
            </div> -->
            <div class="regi_search">
            <?php search(); ?>
                <input id="searchInput" name="q" placeholder="Search by Name, Email, Mobile, Reg ID, Unique Sequence ID, Slip No. or Transaction ID..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="registration.process.php?act=downloadUserListExcel"><?php export(); ?>Export</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newregistartion"><?php add(); ?>New Reg</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <form name="frmSearch" method="post" action="registration.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
            <div class="filter_body">
                <div class="filter_div">
                    <label>Registration Type</label>
                    <select  name="src_registration_type" id="src_registration_type">
                        <option value="">-- Select Registration type --</option>
                        <option value="GENERAL" <?= (trim($_REQUEST['src_registration_type'] == "GENERAL")) ? 'selected="selected"' : '' ?>>GENERAL</option>
                        <option value="SPOT" <?= (trim($_REQUEST['src_registration_type'] == "SPOT")) ? 'selected="selected"' : '' ?>>SPOT</option>
                    </select>
                </div>
                  <div class="filter_div">
                    <label>Conf. Reg. Category</label>
                	<select name="src_conf_reg_category" id="src_conf_reg_category" >
                        <option value="">-- Select Category --</option>
                        <?php
                        $sqlFetchClassification['QUERY']	 = "SELECT classf.`classification_title`, classf.`id`, classf.`currency`, classf.`type`,  masterHotel.hotel_name
                                                                        FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " classf
                                                            LEFT OUTER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
                                                                        ON classf.residential_hotel_id = masterHotel.id
                                                                        WHERE classf.status = 'A'
                                                                    ORDER BY classf.type DESC, IFNULL(classf.residential_hotel_id,0) ASC, classf.sequence_by ASC";
                        $resultClassification	 = $mycms->sql_select($sqlFetchClassification);

                        $optgroupName = "";

                        if ($resultClassification) {
                        ?>
                            <optgroup label="Conference Registration">
                                <?
                                foreach ($resultClassification as $key => $rowClassification) {
                                    if ($optgroupName != $rowClassification['hotel_name']) {
                                        $optgroupName = $rowClassification['hotel_name'];
                                        echo "</optgroup><optgroup label='" . "Residential Package - " . $optgroupName . "'>";
                                    }

                                    if ($rowClassification['type'] == "DELEGATE") {
                                        $clasNm = $rowClassification['classification_title'];
                                    } elseif ($rowClassification['type'] == "COMBO") {
                                        $clasNm = str_replace("Residential Package - ", "", $rowClassification['classification_title']);
                                    } else {
                                        $clasNm = $rowClassification['classification_title'];
                                    }

                                    if ($rowClassification['type'] == "COMBO" && $rowClassification['hotel_name'] != '') {
                                        $classfId = $rowClassification['id'] . '-G';
                                    } else {
                                        $classfId = $rowClassification['id'];
                                    }
                                ?>
                                    <option value="<?= $classfId ?>" <?= ($classfId == trim($_REQUEST['src_conf_reg_category'])) ? 'selected="selected"' : '' ?>>
                                        <?= $clasNm ?>
                                    </option>
                                    <?php
                                    if ($rowClassification['type'] == "COMBO" && $rowClassification['hotel_name'] != '') {
                                        $classfId = $rowClassification['id'] . '-I';
                                    ?>
                                        <option value="<?= $classfId ?>" <?= ($classfId == trim($_REQUEST['src_conf_reg_category'])) ? 'selected="selected"' : '' ?>>
                                            <?= $clasNm . ' - Inaugural Offer' ?>
                                        </option>
                                <?php
                                    }
                                }
                                ?>
                            </optgroup>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="filter_div">
                    <label>Payment Status</label>
                    <select name="src_payment_status" id="src_payment_status">
                        <option value="">-- Select Payment Status --</option>
                        <option value="PAID" <?= (trim($_REQUEST['src_payment_status'] == "PAID")) ? 'selected="selected"' : '' ?>>PAID</option>
                        <option value="UNPAID" <?= (trim($_REQUEST['src_payment_status'] == "UNPAID")) ? 'selected="selected"' : '' ?>>UNPAID</option>
                        <option value="COMPLIMENTARY" <?= (trim($_REQUEST['src_payment_status'] == "COMPLEMENTARY")) ? 'selected="selected"' : '' ?>>COMPLIMENTARY</option>
                        <option value="ZERO_VALUE" <?= (trim($_REQUEST['src_payment_status'] == "ZERO_VALUE")) ? 'selected="selected"' : '' ?>>ZERO VALUE</option>
                        <option value="CREDIT" <?= (trim($_REQUEST['src_payment_status'] == "CREDIT")) ? 'selected="selected"' : '' ?>>CREDIT</option>
                    </select>
                </div>
                 <div class="filter_div">
                    <label>Registration Mode</label>
                        <select name="src_registration_mode" id="src_registration_mode">
                            <option value="">-- Select Mode --</option>
                            <option value="ONLINE" <?= (trim($_REQUEST['src_registration_mode'] == "ONLINE")) ? 'selected="selected"' : '' ?>>ONLINE</option>
                            <option value="OFFLINE" <?= (trim($_REQUEST['src_registration_mode'] == "OFFLINE")) ? 'selected="selected"' : '' ?>>OFFLINE</option>
                        </select>
                </div>
                <div class="filter_div">
                    <label>Tags</label>
                        <?
                        $tagInKeys = array();
                        $sqlAllGeneralUser['QUERY']   = "   SELECT DISTINCT tags 
                                                                    FROM " . _DB_USER_REGISTRATION_ . " 
                                                                    WHERE `status` = 'A'";
                        $resAllGeneralUser   		  = $mycms->sql_select($sqlAllGeneralUser);
                        if ($resAllGeneralUser) {
                            foreach ($resAllGeneralUser as $k => $val) {
                                if (trim($val['tags'] != '')) {
                                    $tagInKeys[$val['tags']] = $val['tags'];
                                }
                            }
                        }
                        ?>
                        <select name="src_user_tags" >
                            <option value=""> -- Select Tags -- </option>
                            <?
                            foreach ($tagInKeys as $k => $tag) {
                            ?>
                                <option value="<?= $tag ?>" <?= $tag == $_REQUEST['src_user_tags'] ? 'selected' : '' ?>><?= $tag ?></option>
                            <?
                            }
                            ?>
                        </select>
                </div>
                <!-- <div>
                    <label>Registration Date</label>
                    <input type="date">
                </div> -->
              
            </div>
            <div class="filter_bottom">
                <button class="filter_reset" onclick="clearFilters();" ><?php reseti(); ?></button>
                <button type="submit"  class="badge_dark">Apply</button>
            </div>
            </form>
        </div>
        <div class="spot_listing ">
        <?php
            $searchCondition 		= "";
            if (isset($_REQUEST['src_conf_reg_category']) && $_REQUEST['src_conf_reg_category'] != '') {
                $exploded = explode("-", $_REQUEST['src_conf_reg_category']);
                if (sizeof($exploded) == 2) {
                    $_REQUEST['src_conf_reg_category'] = $exploded[0];

                    if ($exploded[1] == 'I') {
                        $searchCondition  .= " AND delegate_id IN ( SELECT id 
                                                                                FROM " . _DB_USER_REGISTRATION_ . " 
                                                                                WHERE status IN ('A','C')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                                AND conf_reg_date <=  '2019-01-15 23:59:59' )";
                    } else {
                        $searchCondition  .= " AND delegate_id IN ( SELECT id 
                                                                                FROM " . _DB_USER_REGISTRATION_ . " 
                                                                                WHERE status IN ('A','C')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                                AND conf_reg_date >  '2019-01-15 23:59:59' )";
                    }
                }
            }
            if ($_REQUEST['src_user_tags'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND LOCATE('," . $_REQUEST['src_user_tags'] . ",', CONCAT(',',tags,',') ) > 0 )";
            }
            if ($_REQUEST['src_roles'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND roles LIKE '%" . $_REQUEST['src_roles'] . "%')";
            }
            if ($_REQUEST['src_email_id'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%')";
            }
            if ($_REQUEST['src_access_key'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%')";
            }
            if ($_REQUEST['src_mobile_no'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%')";
            }
            if ($_REQUEST['src_user_first_name'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND (user_first_name  LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
                                                                                OR user_middle_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
                                                                                OR user_last_name   LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
                                                                                OR user_full_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'))";
            }
            if ($_REQUEST['src_payment_mode'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                        AND payment_mode = '" . $_REQUEST['src_payment_mode'] . "')";
            }
            if ($_REQUEST['src_transaction_id'] != '') {
                $searchCondition   .= " AND ( slip_id IN ( SELECT DISTINCT slip_id 
                                                                        FROM " . _DB_PAYMENT_ . " 
                                                                        WHERE status IN ('A')
                                                                        AND ( atom_atom_transaction_id = '" . $_REQUEST['src_transaction_id'] . "'
                                                                                OR atom_merchant_transaction_id = '" . $_REQUEST['src_transaction_id'] . "'
                                                                                OR atom_bank_transaction_id = '" . $_REQUEST['src_transaction_id'] . "'))
                                                        OR 
                                                        slip_id IN ( SELECT DISTINCT slip_id 
                                                                        FROM " . _DB_PAYMENT_REQUEST_ . " 
                                                                        WHERE status IN ('A')
                                                                        AND transaction_id = '" . $_REQUEST['src_transaction_id'] . "'))";
            }
            if ($_REQUEST['src_registration_mode'] != '') {
                $searchCondition   .= " AND invoice_mode LIKE '%" . $_REQUEST['src_registration_mode'] . "%'";
            }
            if ($_REQUEST['src_user_last_name'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND delegate.user_last_name LIKE '%" . $_REQUEST['src_user_last_name'] . "%')";
            }
            if ($_REQUEST['src_invoice_no'] != '') {
                $searchCondition   .= " AND invoice_number LIKE '%" . $_REQUEST['src_invoice_no'] . "%'";
            }
            if ($_REQUEST['src_slip_no'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT id
                                                                    FROM " . _DB_SLIP_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND slip_number LIKE '%" . $_REQUEST['src_slip_no'] . "%' )";
            }
            if ($_REQUEST['src_transaction_ids'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_ids'] . "%')";
            }
            if ($_REQUEST['src_transaction_slip_no'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND (card_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR rrn_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR cheque_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR draft_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR neft_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR rtgs_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR atom_transaction_card_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR atom_bank_transaction_id LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR remarks LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%' ))";
            }
            if ($_REQUEST['src_conf_reg_category'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND registration_classification_id = '" . $_REQUEST['src_conf_reg_category'] . "')";
            }
            if ($_REQUEST['src_reg_category'] != '') {
                if ($_REQUEST['src_reg_category'] == 'Conference') {
                    $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                            FROM " . _DB_USER_REGISTRATION_ . " 
                                                                            WHERE status IN ('A','C')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                                AND registration_classification_id IN (1,3,4,5,6))";
                } elseif ($_REQUEST['src_reg_category'] == 'Residential') {
                    $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                            FROM " . _DB_USER_REGISTRATION_ . " 
                                                                            WHERE status IN ('A','C')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                                AND registration_classification_id IN (7,8,9,10,11,12,13,14,15,16,17,18))";
                }
            }
            if ($_REQUEST['src_registration_type'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND registration_request LIKE '%" . $_REQUEST['src_registration_type'] . "%')";
            }
            if ($_REQUEST['src_registration_id'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "' 
                                                                        AND registration_payment_status IN ('ZERO_VALUE', 'COMPLEMENTARY', 'PAID'))";
            }
            if ($_REQUEST['src_payment_status_old'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "' 
                                                                        AND registration_payment_status = '" . $_REQUEST['src_payment_status'] . "')";
            }
            if ($_REQUEST['src_workshop_classf'] != '') {
                $id =  trim($_REQUEST['src_workshop_classf']);
                $workshop_id = substr($id, 0, 1);
                $payment_status = substr($id, 1);
                if ($payment_status == "P") {
                    $status = "PAID";
                } else if ($payment_status == "U") {
                    $status = "UNPAID";
                } else if ($payment_status == "C") {
                    $status = "COMPLEMENTARY";
                } else {
                    $status = "ALL";
                }

                if ($status != "ALL") {
                    $searchCondition   .= " AND id IN (   SELECT refference_invoice_id 
                                                                        FROM " . _DB_REQUEST_WORKSHOP_ . " 
                                                                        WHERE status IN ('A')
                                                                        AND workshop_id = '" . $workshop_id . "' 
                                                                        AND status = 'A' 
                                                                        AND payment_status = '" . $status . "')";
                } else {
                    $searchCondition   .= " AND id IN (SELECT refference_invoice_id 
                                                                    FROM " . _DB_REQUEST_WORKSHOP_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND workshop_id = '" . $workshop_id . "' 
                                                                    AND status = 'A' )";
                }
            }
            if ($_REQUEST['src_accommodation'] != '') {
                $searchCondition   .= " AND service_type = 'DELEGATE_ACCOMMODATION_REQUEST' 
                                                    AND payment_status = '" . $_REQUEST['src_accommodation'] . "' 
                                                    AND status = 'A' ";
            }
            if ($_REQUEST['src_country_name'] != "") {
                $searchCondition .= " AND delegate_id IN (  SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_country_id = '" . $_REQUEST['src_country_name'] . "%')";
            }
            if ($_REQUEST['src_state_name'] != "") {
                $searchCondition .= " AND delegate_id IN (  SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND user_state_id = '" . $_REQUEST['src_state_name'] . "')";
            }
            if ($_REQUEST['src_payment_date'] != '') {
                $searchCondition   .= "  AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                        AND payment_date = '" . $_REQUEST['src_payment_date'] . "')";
            }
            if ($_REQUEST['src_registration_from_date'] != '') {
                $searchCondition   .= " AND delegate_id IN (  SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('A','C')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                        AND conf_reg_date BETWEEN  '" . $_REQUEST['src_registration_from_date'] . " 00:00:00'
                                                                        AND '" . $_REQUEST['src_registration_to_date'] . " 23:59:59')";
            }
            if ($_REQUEST['src_cancel_invoice_id'] != '') {
                $searchCondition   .= " AND invoice_number LIKE '%" . $_REQUEST['src_cancel_invoice_id'] . "%' 
                                                    AND status = 'C'";
            }
            if ($_REQUEST['src_hasPickup'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT user_id FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " WHERE pikup_time IS NOT NULL)";
            }
            if ($_REQUEST['src_hasDropoff'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT user_id FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " WHERE dropoff_time IS NOT NULL)";
            }
            if ($_REQUEST['src_hasNotes'] != '') {
                $searchCondition   .= " AND delegate_id IN (   SELECT id 
                                                                            FROM " . _DB_USER_REGISTRATION_ . " 
                                                                            WHERE status IN ('A','C')
                                                                            AND user_type = 'DELEGATE'
                                                                            AND operational_area NOT IN ('EXHIBITOR')
                                                                            AND (
                                                                                        isRegistration = 'Y'
                                                                                        )
                                                                            AND TRIM(user_food_preference_in_details) != '')";
            }
            if ($_REQUEST['src_hasPayentTerSetButNotPaid'] != '') {
                $searchCondition   .= " AND slip_id IN (SELECT slip_id	 FROM " . _DB_PAYMENT_ . " payment	WHERE status = 'A' AND payment_status = 'UNPAID')";
            }
            if ($_REQUEST['src_hasUnpaidInvoice'] != '') {
                $searchCondition   .= " AND id IN (SELECT id FROM " . _DB_INVOICE_ . " invoice	WHERE status = 'A' AND payment_status = 'UNPAID')";
            }
            if ($_REQUEST['src_payment_status'] == 'UNPAID') {
                $searchCondition   .= " AND id IN (SELECT id FROM " . _DB_INVOICE_ . " invoice	WHERE status = 'A' AND payment_status = '" . $_REQUEST['src_payment_status'] . "')";

                
            }else if($_REQUEST['src_payment_status'] != ''){
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                        AND payment_status = '" . $_REQUEST['src_payment_status'] . "')";		
            }
        
                // // Bind :search parameter
                // $searchCondition = ""; // default empty


        if (!empty($_GET['q'])) {
            $q = urldecode(trim($_GET['q']));
            $words = explode(' ', $q);

            $subConditions = [];
            foreach ($words as $word) {
                $word = addslashes($word);

                // Require the word to appear in any of these columns
                $subConditions[] = "(user_full_name LIKE '%$word%' 
                                    OR user_email_id LIKE '%$word%' 
                                    OR user_mobile_no LIKE '%$word%' 
                                    OR user_registration_id LIKE '%$word%'
                                    OR user_unique_sequence LIKE '%$word%'
                                    OR slip_id IN ( SELECT id
                                                    FROM " . _DB_SLIP_ . " 
                                                    WHERE status IN ('A')
                                                    AND slip_number LIKE '%" .$word. "%' )
                                    OR delegate_id IN ( SELECT delegate_id
                                    FROM " . _DB_PAYMENT_ . " 
                                    WHERE status IN ('A')
                                    AND razorpay_transaction_id LIKE '%" .$word. "%' ))";

                
            }

            // Combine with AND between words — each word must appear somewhere
            $searchCondition .= " AND delegate_id IN (
                SELECT id
                FROM " . _DB_USER_REGISTRATION_ . "
                WHERE status IN ('A','C')
                AND user_type = 'DELEGATE'
                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                AND (isRegistration='Y' OR registration_request='ONLYWORKSHOP')
                AND " . implode(' AND ', $subConditions) . "
            )";
        }
                //echo $searchCondition;

                $sqlDelegateQueryset 			   = array();
                $sqlDelegateQueryset['QUERY']      = "SELECT DISTINCT delegate_id AS delegate_id									 
                                                                FROM " . _DB_INVOICE_ . "
                                                            WHERE status IN ('A') 
                                                                AND delegate_id IN ( SELECT id 
                                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                                    WHERE status IN ('A')
                                                                                        AND user_type = 'DELEGATE'
                                                                                        AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                                                                        AND (
                                                                                            isRegistration = 'Y'
                                                                                            
                                                                                            )
                                                                                            
                                                                                        )
                                                            " . $searchCondition . "  ORDER BY delegate_id DESC";

                //$sqlFetchUser         	= "";								
                //$idArr 					= getAllDelegates("","",$alterCondition,'','R001',true);

                $resultFetchUser     	  = $mycms->sql_select_paginated('R001', $sqlDelegateQueryset, 10);

            // echo '<pre>'; print_r($sqlDelegateQueryset);
            $perPage = 10; // IMPORTANT: must match SQL LIMIT

            $pageIndex = isset($_GET['_pgnR001_'])
                ? (int)$_GET['_pgnR001_']
                : 0;

            $offset = $pageIndex * $perPage;
            $counter =0;
            if ($resultFetchUser) //$idArr['IDS']
            {
                
                foreach ($resultFetchUser as $kkl => $idRow) //$idArr['IDS'] as $i=>$id
                {
                    $countertest = $offset + $kkl + 1;
                    $id = $idRow['delegate_id'];

                    $status = true;
                    $rowFetchUser = getUserDetails($id);
                    $counter      = $countertest;
                    $color = "#FFFFFF";
                    if ($rowFetchUser['account_status'] == "UNREGISTERED") {
                        $color = "#FFCCCC";
                        $status = false;
                    }
                    $totalAccompanyCount = 0;

                    if ($rowFetchUser['user_food_preference'] == 'VEG') {
                        $foodcolor = "#00CC00";
                    } else {
                        $foodcolor = "#FF0000";
                    }

                    $sqlListing	 = array();
                    $sqlListing['QUERY']		 = "SELECT COUNT(*) AS COUNTDATA
                                                    FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                                    WHERE `participant_delegate_id` = '" . $id . "' AND status='A'";
                    $resultsListing	 = $mycms->sql_select($sqlListing);
                    $existingDetail	 = $resultsListing[0];

                    //print_r($existingDetail['COUNTDATA']);
            ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span><?= $counter?></span>
                        </div>
                        <div>
                            <div class="regi_name"><?= strtoupper($rowFetchUser['user_full_name']) ?></div>
                            <div class="regi_type">
                               <?php
                                    if ($rowFetchUser['isRegistration'] == "Y") {
                                        if (empty(getRegClsfName($rowFetchUser['registration_classification_id']))) {
                                            $regClsName = getRegClsfComboName($rowFetchUser['registration_classification_id']);
                                        } else {
                                            $regClsName = getRegClsfName($rowFetchUser['registration_classification_id']);
                                        }
                                    }else if($rowFetchUser['isRegistration'] == "N" && $rowFetchUser['registration_request']=='ONLYWORKSHOP')
                                    $regClsName = 'ONLYWORKSHOP';
                                    ?>
                                <?php if ($rowFetchUser['membership_number'] != '' || $rowFetchUser['membership_number'] !=0) {
                                    $membership_number = "-" . $rowFetchUser['membership_number'];
                                }else{
                                    $membership_number = '';
                                }
                                ?>
                                <span class="badge_padding badge_primary"><?=$regClsName?><?= $membership_number ?></span>
                                    <?php if (getCutoffName($rowFetchUser['registration_tariff_cutoff_id'])) { ?>
                                    <span class="badge_padding badge_secondary">
                                        <?php
                                        echo '<span title="Cutoff">' . getCutoffName($rowFetchUser['registration_tariff_cutoff_id']) . '</span>';

                                        ?></span>
                                <?php } ?>
                            </div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?><?= $rowFetchUser['user_mobile_no'] ?>
                                </span>
                                <span>
                                    <?php email(); ?><?= $rowFetchUser['user_email_id'] ?>
                                </span>
                                 <span><?php qr() ?><?=strtoupper($rowFetchUser['user_unique_sequence'])?></span>
                                <span><?php calendar() ?><?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?></span>
                                <?if($rowFetchUser['user_food_preference_in_details']!='')
												{
												?>
                                <span><i class="fa fa-sticky-note" aria-hidden="true" style="color:#FF0000; cursor:pointer;" 
												  
												></i><?=$rowFetchUser['user_food_preference_in_details']?></span>
                              <? } ?>
                              
                             <? if (!empty($rowFetchUser['roles']) ) {
                                    $sqlListing = array();
                                    $sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A' AND `participant_email_id` = '" . $rowFetchUser['user_email_id'] . "'";
                                    $resultsListing	 = $mycms->sql_select($sqlListing);

                                    
                                    $sqlParticipantSchdule = array();
                                    $sqlParticipantSchdule['QUERY']		 = "SELECT DISTINCT participant_type 
                                                                                FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
                                                                                WHERE `participant_id` = '" . $resultsListing[0]['id'] . "'
                                                                            ORDER BY (CASE WHEN LOWER(participant_type)='chairperson' THEN 1
                                                                                            WHEN LOWER(participant_type)='moderator' THEN 2
                                                                                            WHEN LOWER(participant_type)='panellist' THEN 3
                                                                                            WHEN LOWER(participant_type)='speaker' THEN 4
                                                                                            ELSE 999999
                                                                                    END) ASC, participant_type ASC";
                                    $resultsParticipantSchdule	 = $mycms->sql_select($sqlParticipantSchdule);

                                    $participations = array();

                                    foreach ($resultsParticipantSchdule as $key => $rowDetail) {
                                        $participations[] = $rowDetail['participant_type'];
                                    }
                                
                                    if ($resultsListing && (!empty($participations))) {
                                        echo '<span style="color:#CC3333;"><i class="fa fa-sticky-note"></i><b> ' . $resultsListing[0]['participation_type'] . '</b>&nbsp;</span>';
                                    }else if($resultsListing && (empty($participations))){
                                        echo '<span style="color:#CC3333;"><i class="fa fa-sticky-note"></i><b>NONE</b>&nbsp;</span>';

                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box registration_details_box">
                           <ul class="service_ul">
                                <?php
                                $sqlFetchInvoice                = getRegistrationInvoiceCancelInvoiceDetails("", $id, "");

                                //echo '<pre>'; print_r($sqlFetchInvoice);																
                                $resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
                                $totalAmountAll = 0;
                                $accompanyCount = 0;
                                $workShopCount = 0;
                                 $dinnerCount = 0;
                                 $accoCount = 0;
                                //   echo '<pre>'; print_r($resultFetchInvoice ); echo '</pre>'; 
                                 if ($resultFetchInvoice) {
                                    	foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
                                            $showTheRecord = true;

                                            $invoiceCounter++;
                                            $slip = getInvoice($rowFetchInvoice['slip_id']);
                                            $returnArray    = discountAmount($rowFetchInvoice['id']);
                                            $percentage     = $returnArray['PERCENTAGE'];
                                            $totalAmount    = $returnArray['TOTAL_AMOUNT'];
                                            $discountAmount = $returnArray['DISCOUNT'];

                                            //   echo '<pre>'; print_r($rowFetchInvoice ); echo '</pre>'; 

                                            $thisUserDetails 	= getUserDetails($rowFetchInvoice[$id]);
                                            $thisUserClasfId 	= getUserClassificationId($rowFetchInvoice[$id]);
                                            $thisUserClasfName 	= getRegClsfName(getUserClassificationId($rowFetchInvoice[$id]));
		                               
											$totalAmountAll += $totalAmount;
											
                                           

                                            if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
                                                
                                ?>
                                <li class="badge_padding badge_primary">
                                    <?php conregi(); ?><span>Conference Registration</span>
                                    <? if($rowFetchInvoice['payment_status']=='PAID')
                                    {
                                    ?>
                                        <n class="mi-1 text_success w-max-con text-uppercase">Paid</n>
                                    <?
                                    }else if($rowFetchInvoice['payment_status']=='UNPAID'){
                                        ?>
                                         <n class="mi-1 text_danger w-max-con text-uppercase">Unpaid</n>
                                        <?
                                    }else if($rowFetchInvoice['payment_status']=='COMPLIMENTARY'){
                                        ?>
                                         <n class="mi-1 text_success w-max-con text-uppercase">COMPLIMENTARY</n>
                                        <?
                                    }
                                   ?>
                                </li>
                                <?php
                                    }
                                    if ($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
                                        $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "CONFERENCE");
                                        ?>
                                        <li class="badge_padding badge_primary">
                                            <?php conregi(); ?><span><?=$type?></span>
                                        </li>
                                        <?php
                                    }
                                   
                                    if ($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
                                        $workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'], true);
                                        $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "WORKSHOP");
                                        if ($workShopDetails['showInInvoices'] != 'Y') {
                                            $showTheRecord 		= false;
                                        }
                                         $workShopCount ++;
                                        if($rowFetchInvoice['payment_status']=='PAID')
                                            {
                                             $workshopPayment = 'PAID';
                                            }else if($rowFetchInvoice['payment_status']=='UNPAID'){
                                             $workshopPayment = 'UNPAID';

                                            }else if($rowFetchInvoice['payment_status']=='COMPLIMENTARY'){
                                            $workshopPayment = 'COMPLIMENTARY';

                                            }
                                        ?>
                                        
                                                           
                                      <?php
                                       }
                                        if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
                                        //    echo '<pre>'; print_r($rowFetchInvoice);		
                                            $type = getInvoiceTypeString($id, $rowFetchInvoice['refference_id'], "ACCOMPANY");
                                            $accompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
                                          
                                            if ($accompanyDetails['registration_request'] == 'GUEST') {
                                                ?>
                                                <li class="badge_padding badge_secondary">
                                                    <?php duser(); ?><span>Accompaning Guest - <?=$accompanyDetails['user_full_name']?></span>
                                                </li>
                                            <?                                          
                                            } else {
                                                $accompanyCount++;
                                                if($rowFetchInvoice['payment_status']=='PAID')
                                                        {
                                                        $accompanyPayment = 'PAID';
                                                        }else if($rowFetchInvoice['payment_status']=='UNPAID'){
                                                        $accompanyPayment = 'UNPAID';

                                                        }else if($rowFetchInvoice['payment_status']=='COMPLIMENTARY'){
                                                        $accompanyPayment = 'COMPLIMENTARY';

                                                        }
                                                    ?>
                                                
                                                   
                                             <?
                                            }
                                        }
                                       
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
								            $type = getInvoiceTypeString($id, $rowFetchInvoice['refference_id'], "ACCOMMODATION");
                                            $sqlAccomm = array();
                                            $sqlAccomm['QUERY']    = " SELECT accomm.*, hotel.hotel_name
                                                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
                                                                    INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
                                                                            ON accomm.hotel_id = hotel.id
                                                                            WHERE accomm.`user_id` = ?
                                                                            AND accomm.`refference_invoice_id` = ?";
                                            $sqlAccomm['PARAM'][]  = array('FILD' => 'user_id',  				'DATA' => $id,  			'TYP' => 's');
                                            $sqlAccomm['PARAM'][]  = array('FILD' => 'refference_invoice_id',  	'DATA' => $rowFetchInvoice['id'],  	'TYP' => 's');
                                            $resAccomm = $mycms->sql_select($sqlAccomm);
                                            //echo "<pre>";print_r($resAccomm);echo "</pre>";
                                            foreach ($resAccomm as $kk => $row) {
                                                $accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] = 'ACCOMMODATION';
                                                $accommodationRecords[$rowFetchInvoice['id']]['accomId'] = $row['id'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['packageId'] = $row['package_id'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] = $row['hotel_name'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] = $row['checkin_date'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] = $row['checkout_date'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['SHARE'] = array();
                                                //$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';
                                            }

                                          $type = getInvoiceTypeString($id, $rowFetchInvoice['refference_id'], "ACCOMMODATION");
                                            $accoDetails = getUserDetails($rowFetchInvoice['refference_id']);
                                          
                                          
                                                $accoCount++;
                                                if($rowFetchInvoice['payment_status']=='PAID')
                                                {
                                                $accoPayment = 'PAID';
                                                }else if($rowFetchInvoice['payment_status']=='UNPAID'){
                                                $accoPayment = 'UNPAID';

                                                }else if($rowFetchInvoice['payment_status']=='COMPLIMENTARY'){
                                                 $accoPayment = 'COMPLIMENTARY';

                                            }
                                                    
                                        
                                    
                                        }
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
                                           
                                            $sqlDetails  = array();
                                            $sqlDetails['QUERY'] = "  SELECT dinnerReq.*,  
                                                                            dinner.dinner_classification_name, dinner.date AS dinnerDate,
                                                                            user.user_full_name, dinner.dinner_hotel_name,
                                                                            inv.id AS invoiceId
                                                                        FROM "._DB_REQUEST_DINNER_." dinnerReq
                                                                INNER JOIN "._DB_DINNER_CLASSIFICATION_." dinner
                                                                        ON dinner.id = dinnerReq.package_id
                                                                INNER JOIN "._DB_USER_REGISTRATION_." user
                                                                        ON user.id = dinnerReq.refference_id
                                                            LEFT OUTER JOIN "._DB_INVOICE_." inv
                                                                        ON inv.id = dinnerReq.refference_invoice_id
                                                                        AND inv.status = 'A'
                                                                        AND inv.service_type IN('DELEGATE_DINNER_REQUEST','DELEGATE_CONFERENCE_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION','ACCOMPANY_CONFERENCE_REGISTRATION')
                                                                    WHERE dinnerReq.status = ?
                                                                        AND dinnerReq.`refference_invoice_id` = ?";
                                                                        
                                            $sqlDetails['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',            	'TYP' => 's');
                                            $sqlDetails['PARAM'][]  = array('FILD' => 'refference_invoice_id', 'DATA' =>$rowFetchInvoice['id'],  		'TYP' => 's');
                                            
                                            $resDetails = $mycms->sql_select($sqlDetails);
                                         
                                            $type =$resDetails[0]['dinner_classification_name']."-".$resDetails[0]['dinner_hotel_name']."<br>".$resDetails[0]['dinnerDate'];
                                           
                                            $type = getInvoiceTypeString($id, $rowFetchInvoice['refference_id'], "DINNER");
                                            $dinnerDetails = getUserDetails($rowFetchInvoice['refference_id']);
                                          
                                            $dinnerCount++;
                                            if($rowFetchInvoice['payment_status']=='PAID')
                                            {
                                            $dinnerPayment = 'PAID';
                                            }else if($rowFetchInvoice['payment_status']=='UNPAID'){
                                            $dinnerPayment = 'UNPAID';

                                            }else if($rowFetchInvoice['payment_status']=='COMPLIMENTARY'){
                                            $dinnerPayment = 'COMPLIMENTARY';

                                            }
                                        
                                        }
                                            
                                            
                                        }
                                    }
                                 ?>
                                 <?
                                 if ($workShopCount > 0) {
                                  ?>
                                  <li class="badge_padding badge_info">
                                         <?php workshop(); ?><j><?=$workShopCount?></j><span>Workshop</span>
                                          <? if($workshopPayment=='PAID')
                                            {
                                            ?>
                                                <n class="mi-1 text_success w-max-con text-uppercase">Paid</n>
                                            <?
                                            }else if($workshopPayment=='UNPAID'){
                                                ?>
                                                <n class="mi-1 text_danger w-max-con text-uppercase">Unpaid</n>
                                                <?
                                            }else if($workshopPayment=='COMPLIMENTARY'){
                                                ?>
                                              <n class="mi-1 text_success w-max-con text-uppercase">COMPLIMENTARY</n>      
                                         <?
                                            }
                                        ?>
                                    </li> 
                                <? } ?>       
                                <?
                                 if ($dinnerCount > 0) {
                                  ?>
                                  <li class="badge_padding badge_info">
                                         <?php dinner(); ?><j><?=$dinnerCount?></j><span>Dinner</span>
                                          <? if($dinnerPayment=='PAID')
                                            {
                                            ?>
                                                <n class="mi-1 text_success w-max-con text-uppercase">Paid</n>
                                            <?
                                            }else if($dinnerPayment=='UNPAID'){
                                                ?>
                                                <n class="mi-1 text_danger w-max-con text-uppercase">Unpaid</n>
                                                <?
                                            }else if($dinnerPayment=='COMPLIMENTARY'){
                                                ?>
                                              <n class="mi-1 text_success w-max-con text-uppercase">COMPLIMENTARY</n>      
                                         <?
                                            }
                                        ?>
                                    </li> 
                                <? } ?>      
                                 <?
                                 if ($accoCount > 0) {
                                  ?>
                                  <li class="badge_padding badge_info">
                                         <?php hotel(); ?><j><?=$accoCount?></j><span>Accomodation</span>
                                          <? if($accoPayment=='PAID')
                                            {
                                            ?>
                                                <n class="mi-1 text_success w-max-con text-uppercase">Paid</n>
                                            <?
                                            }else if($accoPayment=='UNPAID'){
                                                ?>
                                                <n class="mi-1 text_danger w-max-con text-uppercase">Unpaid</n>
                                                <?
                                            }else if($accoPayment=='COMPLIMENTARY'){
                                                ?>
                                              <n class="mi-1 text_success w-max-con text-uppercase">COMPLIMENTARY</n>      
                                         <?
                                            }
                                        ?>
                                    </li> 
                                <? } ?>      
                                 <?php
                                 if ($accompanyCount > 0) {
                                  ?>
                                 <li class="badge_padding badge_secondary">
                                    <?php duser(); ?><j><?=$accompanyCount?></j><span>Accompanying Person</span>
                                     <? if($accompanyPayment=='PAID')
                                            {
                                            ?>
                                                <n class="mi-1 text_success w-max-con text-uppercase">Paid</n>
                                            <?
                                            }else if($accompanyPayment=='UNPAID'){
                                                ?>
                                                <n class="mi-1 text_danger w-max-con text-uppercase">Unpaid</n>
                                                <?
                                            }else if($accompanyPayment=='COMPLIMENTARY'){
                                                ?>
                                              <n class="mi-1 text_success w-max-con text-uppercase">COMPLIMENTARY</n>      
                                         <?
                                            }
                                        ?>
                                        
                                </li>
                               <? } ?>
                               <!-- <li class="badge_padding badge_secondary" style="width: 50%;background-color: rgb(108 255 140 / 15%) !important;">
                                    <span>Mode Of payment</span>
                                 <? if($rowFetchInvoice['invoice_mode']=='OFFLINE'){ ?>
                                    <n class="mi-1 text_danger w-max-con text-uppercase" style="color: #9ec0ff !important;"><?=$rowFetchInvoice['invoice_mode']?></n>

                                 <? }else{ ?>
                                    <n class="mi-1 text_danger w-max-con text-uppercase" style="color: #7cff6a !important;"><?=$rowFetchInvoice['invoice_mode']?></n>

                                <?  } ?> -->
                                               
                                </li>
                            </ul>
                              <? if($rowFetchInvoice['invoice_mode']=='OFFLINE'  && $rowFetchInvoice['payment_status']!='COMPLIMENTARY'  && $rowFetchInvoice['payment_status']!='ZERO_VALUE'){ ?>
                                    <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase" style="color: #9ec0ff !important;"><?=$rowFetchInvoice['invoice_mode']?></span>

                                 <? }else if($rowFetchInvoice['invoice_mode']=='ONLINE'  && $rowFetchInvoice['payment_status']!='COMPLIMENTARY'  && $rowFetchInvoice['payment_status']!='ZERO_VALUE'){ ?>
                                    <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase" style="color: #76ffef !important;"><?=$rowFetchInvoice['invoice_mode']?></span>

                                <?  }else if($rowFetchInvoice['payment_status']=='COMPLIMENTARY' || $rowFetchInvoice['payment_status']=='ZERO_VALUE'){
                                                                        ?><span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase" style="color: #76ffef !important;"><?=$rowFetchInvoice['payment_status']?></span>

                              <?  }
                                
                                ?> 
                        </div>
                         <?php
                            $paymentCounter = 0;
                            $delegateId = $rowFetchUser['id'];
                            $resFetchSlip = slipDetailsOfUser($delegateId, false);
                        
                            $styleCss = 'style="display:none;"';

                            foreach ($resFetchSlip as $key => $rowFetchSlip) {

                                $counter++;
                                $resPaymentDetails = paymentDetails($rowFetchSlip['id']);

                                $paymentDescription = "-";
                                if ($key == 0) {
                                    $paymentId = $resPaymentDetails['id'];
                                    $slipId = $rowFetchSlip['id'];
                                }
                                $isChange = "YES";
                                $excludedAmount = invoiceAmountOfSlip($rowFetchSlip['id'], false, false);

                                $amount = invoiceAmountOfSlip($rowFetchSlip['id']);
                                $discountDeductionAmount = ($excludedAmount - $amount);

                                foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
                                    if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
                                        $discountAmountofSlip = $discountDeductionAmount;
                                    } else {
                                        $discountAmountofSlip = ($discountDeductionAmount / 1.18);
                                    }
                                }

                                $DiscountAmount = invoiceDiscountAmountOfSlip($rowFetchSlip['id'], true);

                            }
                            $sql = [];
                            $sql['QUERY'] = "
                            SELECT currency,
                                    SUM(service_roundoff_price) AS totalAmount
                            FROM " . _DB_INVOICE_ . "
                            WHERE status = ?
                                AND delegate_id = ?
                            ";

                            $sql['PARAM'][] = ['DATA' => 'A', 'TYP' => 's'];
                            $sql['PARAM'][] = ['DATA' => $delegateId, 'TYP' => 'i'];

                            $result = $mycms->sql_select($sql);

                            $row = $result[0] ?? [];
                            $currency = $row['currency'] ?? '';
                            $totalAmount = $row['totalAmount'] ?? 0;

                            ?>
                        <div class="spot_details_box align-items-end">
                            <h5>Payment Status</h5>
                           <?php if ($rowFetchSlip['payment_status'] === 'PAID') { ?>
                                <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase">
                                    <?php paid() ?>
                                    <?= $rowFetchSlip['payment_status']; ?>
                                </span>

                            <?php } ?>
                            <?php if ($rowFetchSlip['payment_status'] === 'UNPAID') { ?>
                                <span class="mi-1 pay_status badge_padding badge_danger w-max-con text-uppercase">
                                    <?php unpaid(); ?>
                                    <?= $rowFetchSlip['payment_status']; ?>
                                </span>

                            <?php } ?>
                            <h6> <?php

                            if ( $totalAmount  > 0) {
                                echo   $currency  . '&nbsp;' . $totalAmount;
                            
                            } else {
                                echo   $currency  . '&nbsp;' . "0.00";
                            }
                            ?></h6>
                             <!-- <? if($rowFetchInvoice['invoice_mode']=='OFFLINE'){ ?>
                                    <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase" style="color: #9ec0ff !important;"><?=$rowFetchInvoice['invoice_mode']?></span>

                                 <? }else{ ?>
                                    <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase" style="color: #76ffef !important;"><?=$rowFetchInvoice['invoice_mode']?></span>

                                <?  } ?>  -->
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom accm_bottom  justify-content-end">

                    <div class="spot_box_bottom_right">
                        <a href="javascript:void(null)" data-tab="addNotes"  data-user-id="<?=$idRow['delegate_id']?>" class="popup-btn icon_hover badge_info action-transparent addNotesbtn"><?php edit(); ?>Notes</a>
                        <!-- <a href="javascript:void(null)" data-tab="profile"  data-user-id="<?=$idRow['delegate_id']?>" class="popup-btn icon_hover badge_info action-transparent viewProfilebtn"><?php view(); ?>View</a> -->
                        <a href="javascript:void(null)" data-tab="editregistartion"  data-user-id="<?=$idRow['delegate_id']?>" class="popup-btn icon_hover badge_secondary action-transparent editbtn"><?php edit(); ?>Edit Details</a>
                        <a href="javascript:void(0);"  class="icon_hover badge_danger action-transparent delet" onclick="if (confirm('Do you really want to remove this user?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>registration.process.php?act=Trash&id=<?= $rowFetchUser['id']; ?>&userType=<?=$rowFetchUser['userType'] ?>'; 
                                                                        }"><?php delete(); ?>Delete</a>
                        <a href="#" class="drp icon_hover badge_dark action-transparent">Service Breakdown<?php down(); ?></a>
                    </div>
                </div>
                <div class=" accm_tariff   spot_service_break">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><?php windowi(); ?>Service Breakdown</h4>
                        
                        <ul class="service_breakdown_wrap_ul">
                            <?php
                            $sqlFetchInvoice                = getRegistrationInvoiceCancelInvoiceDetails("", $id, "");

                            //echo '<pre>'; print_r($sqlFetchInvoice);																
                            $resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
                            $totalAmountAll = 0;
                          
                            if ($resultFetchInvoice) {
                                    foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
                                        $showTheRecord = true;

                                        $invoiceCounter++;
                                        $slip = getInvoice($rowFetchInvoice['slip_id']);
                                        $returnArray    = discountAmount($rowFetchInvoice['id']);
                                        $percentage     = $returnArray['PERCENTAGE'];
                                        $totalAmount    = $returnArray['TOTAL_AMOUNT'];
                                        $discountAmount = $returnArray['DISCOUNT'];

                                        // echo '<pre>'; print_r($totalAmount ); echo '</pre>'; 

                                        $thisUserDetails 	= getUserDetails($rowFetchInvoice[$id]);
                                        $thisUserClasfId 	= getUserClassificationId($rowFetchInvoice[$id]);
                                        $thisUserClasfName 	= getRegClsfName(getUserClassificationId($rowFetchInvoice[$id]));
                                
                                        $totalAmountAll += $totalAmount;
                                        
                                        $type			 	= "";
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
                                            $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "CONFERENCE");
                                            $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-gift" aria-hidden="true"></i>&nbsp;Conference Registration';
                                        }
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
                                            $isResReg = true;
                                            $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "RESIDENTIAL");
                                            $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-gift" aria-hidden="true"></i>&nbsp;' . $type;

                                            $sqlAccomm = array();
                                            $sqlAccomm['QUERY']    = " SELECT accomm.*, hotel.hotel_name
                                                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
                                                                    INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
                                                                            ON accomm.hotel_id = hotel.id
                                                                            WHERE accomm.`user_id` = ?
                                                                            AND accomm.`refference_invoice_id` = ?";
                                            $sqlAccomm['PARAM'][]  = array('FILD' => 'user_id',  				'DATA' => $delegate_id,  			'TYP' => 's');
                                            $sqlAccomm['PARAM'][]  = array('FILD' => 'refference_invoice_id',  	'DATA' => $rowFetchInvoice['id'],  	'TYP' => 's');
                                            $resAccomm = $mycms->sql_select($sqlAccomm);

                                            foreach ($resAccomm as $kk => $row) {
                                                $serviceSummary[$rowFetchInvoice['id'] . 'accm'] = '';

                                                $accommodationRecords[$rowFetchInvoice['id']]['KEY'] 			= $rowFetchInvoice['id'] . 'accm';
                                                $accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] 		= 'RES-PACK';
                                                $accommodationRecords[$rowFetchInvoice['id']]['accomId'] 		= $row['id'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['packageId']	 	= $cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId];
                                                $accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] 	= $row['hotel_name'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] 	= $row['checkin_date'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] 	= $row['checkout_date'];

                                                $accommodationRecords[$rowFetchInvoice['id']]['WILL-SHARE'] 	= false;
                                                $accommodationRecords[$rowFetchInvoice['id']]['SHARE'] 			= array();

                                                //$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';

                                                if (in_array($thisUserClasfId, $cfg['RESIDENTIAL_SHARING_CLASF_ID'])) {
                                                    $accommodationRecords[$rowFetchInvoice['id']]['WILL-SHARE'] 			= true;
                                                    $accommodationRecords[$rowFetchInvoice['id']]['SHARE']['KEY'] 			= $rowFetchInvoice['id'] . 'accmshr';
                                                    $serviceSummary[$rowFetchInvoice['id'] . 'accmshr']						= '';

                                                    if (trim($row['preffered_accommpany_name']) != '') {
                                                        $accommodationRecords[$rowFetchInvoice['id']]['SHARE']['accomId'] 		= $row['id'];
                                                        $accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefName'] 		= $row['preffered_accommpany_name'];
                                                        $accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefMobile'] 	= $row['preffered_accommpany_mobile'];
                                                        $accommodationRecords[$rowFetchInvoice['id']]['SHARE']['prefEmail'] 	= $row['preffered_accommpany_email'];
                                                        /*$serviceSummary[$rowFetchInvoice['id'].'accmshr'] = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" onClick="openSharePrefEditPopup(this)" accomId="'.$row['id'].'" prefName="'.$row['preffered_accommpany_name'].'"  prefMobile="'.$row['preffered_accommpany_mobile'].'"  prefEmail="'.$row['preffered_accommpany_email'].'"></i>&nbsp;'.$row['preffered_accommpany_name'].'<br>
                                                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-phone" aria-hidden="true"></i>&nbsp;'.$row['preffered_accommpany_mobile'].'<br>
                                                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;'.$row['preffered_accommpany_email'].'';*/
                                                    } else {
                                                        $accommodationRecords[$rowFetchInvoice['id']]['SHARE']['accomId'] = $row['id'];
                                                        //$serviceSummary[$rowFetchInvoice['id'].'accmshr'] = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-smile-o" aria-hidden="true" style="cursor:pointer;" onClick="openSharePrefEditPopup(this)" accomId="'.$row['id'].'"></i>&nbsp;-';
                                                    }
                                                }
                                            }
                                        }
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
                                            $workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id'], true);
                                            $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "WORKSHOP");
                                            if ($workShopDetails['showInInvoices'] != 'Y') {
                                                $showTheRecord 		= false;
                                            }
                                            $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-stethoscope" aria-hidden="true"></i>&nbsp;' . $workShopDetails['classification_title'];
                                        }
                                        if ($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
                                            $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "ACCOMPANY");
                                            $accompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
                                            if ($accompanyDetails['registration_request'] == 'GUEST') {
                                                $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-smile-o" aria-hidden="true"></i>&nbsp;Accompaning Guest - ' . $accompanyDetails['user_full_name'];
                                            } else {
                                                $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-users" aria-hidden="true"></i>&nbsp;Accompany - ' . $accompanyDetails['user_full_name'];
                                            }
                                        }
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
                                            $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "ACCOMMODATION");
                                            //$serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-building" aria-hidden="true"></i>&nbsp;Accomodation';

                                            //$showTheRecord = false;
                                            $sqlAccomm = array();
                                            $sqlAccomm['QUERY']    = " SELECT accomm.*, hotel.hotel_name
                                                                            FROM " . _DB_REQUEST_ACCOMMODATION_ . " accomm
                                                                    INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
                                                                            ON accomm.hotel_id = hotel.id
                                                                            WHERE accomm.`user_id` = ?
                                                                            AND accomm.`refference_invoice_id` = ?";
                                            $sqlAccomm['PARAM'][]  = array('FILD' => 'user_id',  				'DATA' => $delegate_id,  			'TYP' => 's');
                                            $sqlAccomm['PARAM'][]  = array('FILD' => 'refference_invoice_id',  	'DATA' => $rowFetchInvoice['id'],  	'TYP' => 's');
                                            $resAccomm = $mycms->sql_select($sqlAccomm);
                                            //echo "<pre>";print_r($resAccomm);echo "</pre>";
                                            foreach ($resAccomm as $kk => $row) {
                                                $accommodationRecords[$rowFetchInvoice['id']]['BOOK-TYP'] = 'ACCOMMODATION';
                                                $accommodationRecords[$rowFetchInvoice['id']]['accomId'] = $row['id'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['packageId'] = $row['package_id'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['hotel_name'] = $row['hotel_name'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['checkin_date'] = $row['checkin_date'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['checkout_date'] = $row['checkout_date'];
                                                $accommodationRecords[$rowFetchInvoice['id']]['SHARE'] = array();
                                                //$serviceSummary[$rowFetchInvoice['id'].'accm'] = '<i class="fa fa-building" aria-hidden="true" style="cursor:pointer;" onClick="openAccmDateEditPopup(this)" accomId="'.$row['id'].'" packageId="'.$cfg['RESIDENTIAL_PACKAGE_ARRAY'][$thisUserClasfId].'"></i>&nbsp;Accommodation @ '.$row['hotel_name'].' <span style="font-size:12px;">['.$row['checkin_date'].' to '.$row['checkout_date'].']</span>';
                                            }

                                            $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-building" aria-hidden="true"></i>&nbsp;Accommodation @ ' . $accommodationRecords[$rowFetchInvoice['id']]['hotel_name'];
                                        }
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST") {
                                            $tourDetails = getTourDetails($invoiceDetails['refference_id']);
                                            $type = getInvoiceTypeString($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "TOUR");
                                            $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-bus" aria-hidden="true"></i>&nbsp;Tour';
                                        }
                                        if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
    
                                            $sqlDetails  = array();
                                            $sqlDetails['QUERY'] = "  SELECT dinnerReq.*,  
                                                                            dinner.dinner_classification_name, dinner.date AS dinnerDate,
                                                                            user.user_full_name, dinner.dinner_hotel_name,
                                                                            inv.id AS invoiceId
                                                                        FROM "._DB_REQUEST_DINNER_." dinnerReq
                                                                INNER JOIN "._DB_DINNER_CLASSIFICATION_." dinner
                                                                        ON dinner.id = dinnerReq.package_id
                                                                INNER JOIN "._DB_USER_REGISTRATION_." user
                                                                        ON user.id = dinnerReq.refference_id
                                                            LEFT OUTER JOIN "._DB_INVOICE_." inv
                                                                        ON inv.id = dinnerReq.refference_invoice_id
                                                                        AND inv.status = 'A'
                                                                        AND inv.service_type IN('DELEGATE_DINNER_REQUEST','DELEGATE_CONFERENCE_REGISTRATION','DELEGATE_RESIDENTIAL_REGISTRATION','ACCOMPANY_CONFERENCE_REGISTRATION')
                                                                    WHERE dinnerReq.status = ?
                                                                        AND dinnerReq.`refference_invoice_id` = ?";
                                                                        
                                            $sqlDetails['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',            	'TYP' => 's');
                                            $sqlDetails['PARAM'][]  = array('FILD' => 'refference_invoice_id', 'DATA' =>$rowFetchInvoice['id'],  		'TYP' => 's');
                                            
                                            $resDetails = $mycms->sql_select($sqlDetails);
                                        
                                            $type =$resDetails[0]['dinner_classification_name']."-".$resDetails[0]['dinner_hotel_name']."<br>".$resDetails[0]['dinnerDate'];
                                                                                                        
                                            $serviceSummary[$rowFetchInvoice['id']] = '<i class="fa fa-cutlery" aria-hidden="true"></i>&nbsp;Dinner';
                                            
                                        }
                                        if (isset($rowFetchInvoice['Refund_status']) && $rowFetchInvoice['Refund_status'] == 'Not_refunded') {
                                            $rowBackGround = "#FFFFCA";
                                        } elseif (isset($rowFetchInvoice['Refund_status']) && $rowFetchInvoice['Refund_status'] == 'Refunded') {
                                            $rowBackGround = "#FFCCCC";
                                        } else {
                                            $rowBackGround = "#FFFFFF";
                                        }

                                        if ($rowFetchInvoice['payment_status'] == "COMPLIMENTARY") {
                                            $payment_status = ' <span class="mi-1 badge_padding badge_default w-max-con text-uppercase"><i class="fal fa-gift"></i>Complimentary</span>';
                                        ?>
                                        <?php
                                        } elseif ($rowFetchInvoice['payment_status'] == "ZERO_VALUE") {
                                            $payment_status =' <span class="mi-1 badge_padding badge_partial w-max-con text-uppercase"><i class="fab fa-creative-commons-zero"></i>Zero Value</span>';
                                        ?>
                                        <?php
                                        } else if ($rowFetchInvoice['payment_status'] == "PAID") {
                                            if (!($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION" && $workShopDetails['display'] == 'N')) {
                                                $totalPaid += $totalAmount;
                                            }
                                            $paymentModeDisplay = $resPaymentDetails['payment_mode'] == 'NEFT' ? 'NEFT/UPI' : ($resPaymentDetails['payment_mode'] == 'Cheque' ? 'Cheque/DD' : $resPaymentDetails['payment_mode']);
                                            $payment_status = ' <span class="mi-1 badge_padding badge_success w-max-con text-uppercase"><i class="fal fa-check"></i>Paid</span>';
                                        } else if ($rowFetchInvoice['payment_status'] == "UNPAID") {
                                            $hasUnpaidBill	 = true;
                                            $totalUnpaid 	+= $totalAmount;
                                            $payment_status= '<span class="mi-1 badge_padding badge_danger w-max-con text-uppercase"><i class="fal fa-times"></i>Unpaid</span>';
                                        ?>
                                        <?php
                                        }

                                            
                            ?>
                            <li>
                                <n>
                                    <j><?=$type?></j>
                                    <j><small class="text-muted">Reference: </small><?= $rowFetchInvoice['invoice_number'] ?></j>
                                </n>
                                <g>
                                    <j><?= $rowFetchInvoice['currency'] ?> <?= number_format($totalAmount, 2) ?></j>
                                    <?=$payment_status?>
                                </g>
                            </li>
                            <? }  }  ?>
                        </ul>
                       
                    </div>
                </div>
            </div>
              <?php
                    }
                } else {
                    ?>
                  
                      <span class="mandatory">No Record Present.</span>
                       
                    <?php
                }
                ?>
            <div class="bbp-pagination">
              <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo('R001') ?></div>
                <span class="paginationDisplay">
                    <div class="pagination"><a><?= $mycms->paginate('R001', 'pagination') ?></a></div>
                </span>
            </div>
        </div>

     
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
function clearFilters() {
    // Get the form
    var form = document.forms['frmSearch'];

    // Clear selects
    form.src_registration_type.value = "";
    form.src_payment_status.value = "";
    form.src_conf_reg_category.value = "";
    form.src_user_tags.value = "";
    form.src_registration_mode.value = "";

    // Clear date input
    form.querySelector('input[type="date"]').value = "";

    // Optional: clear hidden act value (if you want a clean GET)
    // form.act.value = "";

    // Submit the form to PHP with empty values
    form.submit();
}
      /////////////////////view profile start/////////////////////////
        $(document).on('click', '.viewProfilebtn', function() {
        let userId   = $(this).data('user-id');
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                userId: userId,               
            },
        success: function(response) {
        $('#profile').html($(response).find('#profile').html());
        // Then show the popup
        $('#profile').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
    });
    $(document).on('click', '.editbtn', function() {
        let userId   = $(this).data('user-id');
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                edituserId: userId,               
            },
             beforeSend: showEditRegLoader,
        success: function(response) {
        $('#editregistartion').html($(response).find('#editregistartion').html());
        // Then show the popup
        initEditregistration();

        $('#editregistartion').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            },
             complete: hideEditRegLoader
        });
        // Trigger the popup-btn functionality
    });
    ///////////////////view profile edit///////////////////////////
    $(document).on('click', '.addNotesbtn', function() {
        let userId   = $(this).data('user-id');
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                userIdNotes: userId,               
            },
        success: function(response) {
        $('#addNotes').html($(response).find('#addNotes').html());
        // Then show the popup
        $('#addNotes').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
    });
    
</script>
<script>
// const searchInput = document.getElementById("searchInput");
// let typingTimer;
// const typingDelay = 300;

// searchInput.addEventListener("keypress", function(e) {
//     if (e.key === "Enter") {
//         const query = this.value.trim();
//         const url = new URL(window.location.href);

//         if(query.length > 0){
//             url.searchParams.set('q', query);
//         } else {
//             url.searchParams.delete('q');
//         }
//         url.searchParams.delete('_pgnR001_'); // reset pagination
//         window.location.href = url.toString();
//     }
// });

// searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));

const searchInput = document.getElementById("searchInput");
let typingTimer;
const typingDelay = 2000; // wait 0.8s after last keystroke

searchInput.addEventListener("keyup", function() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        const query = this.value.trim();

        const url = new URL(window.location.href);

        if (query.length > 0) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }

        url.searchParams.delete('_pgnR001_'); // reset pagination
        window.location.href = url.toString(); // reload page with new query
    }, typingDelay);
});

searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));
</script>
</html>