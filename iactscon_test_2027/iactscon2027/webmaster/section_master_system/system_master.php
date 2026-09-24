<?php
include_once('includes/init.php');
include_once("../../includes/function.invoice.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.registration.php');
page_header('System Master');
include_once("includes/popup_system_master.php");
?>

<div class="body_wrap">
    <div class="page_top_wrap mb-3">
        <div class="page_top_wrap_left">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">

                    <li class="breadcrumb-item active" aria-current="page"><a href="#">System Master</a></li>
                </ol>
            </nav>
            <h2>Manage System Master</h2>
            <h6>Manage tariff, dates, packages, and classifications.</h6>
        </div>
    </div>

    <div class="com_info_wrap">
        <div class="com_info_left">
            <h6>System Menu</h6>
            <button data-tab="combo" class="com_info_left_click icon_hover badge_primary action-transparent"><i class="fal fa-school"></i>Combo</button>
            <button data-tab="cutoff" class="com_info_left_click icon_hover badge_secondary active"><?php rupee() ?>Cutoff</button>
            <button data-tab="registration" class="com_info_left_click icon_hover badge_success action-transparent"><?php user() ?>Registration</button>
            <button data-tab="workshop" class="com_info_left_click icon_hover badge_info action-transparent"><?php workshop() ?>Workshop</button>
            <button data-tab="accommodation" class="com_info_left_click icon_hover badge_danger action-transparent"><?php hotel() ?>Accommodation</button>
            <button data-tab="dinner" class="com_info_left_click icon_hover badge_dark action-transparent"><?php dinner() ?>Dinner</button>
            <button data-tab="accompany" class="com_info_left_click icon_hover badge_default action-transparent"><?php duser() ?>Accompany</button>
        </div>
        <div class="com_info_right">

            <div class="com_info_box" id="combo">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_primary"><?php credit() ?></span> Registration Tariff</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Registration Classification</th>
                                            <th class="text-right">Early Bird</th>
                                            <th class="text-right">Regular</th>
                                            <th class="text-right">Advance</th>
                                            <th class="text-right">Spot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_primary"><?php conregi() ?></span>Registration Classification</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Combo</span><a class="add mi-1 popup-btn" data-tab="newcombo"><?php add() ?>Add Classification</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Registration Classification</th>
                                            <th class="text-right">Early Bird</th>
                                            <th class="text-right">Regular</th>
                                            <th class="text-right">Advance</th>
                                            <th class="text-right">Spot</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Delegate</td>
                                            <td class="text-right">INR 4000.00</td>
                                            <td class="text-right">INR 6000.00</td>
                                            <td class="text-right">INR 8000.00</td>
                                            <td class="text-right">INR 12000.00</td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="com_info_box active" id="cutoff">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_secondary"><?php conregi() ?></span> Registration Cutoff</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1 popup-btn" data-tab="addregistrationcutoff"><?php add() ?>Add Cutoff</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Cutoff Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql_cal['QUERY']    =    "SELECT *  
											    FROM " . _DB_TARIFF_CUTOFF_ . " 
										       WHERE status != 'D'";
                                        $res_cal            =    $mycms->sql_select($sql_cal);
                                        $i = 1;

                                        foreach ($res_cal as $key => $rowsl) {
                                        ?>
                                            <tr>
                                                <td class="sl"><?= $i ?></td>
                                                <td><?= $rowsl['cutoff_title'] ?></td>
                                                <td><?= displayDateFormat($rowsl['start_date']) ?></td>
                                                <td><?= displayDateFormat($rowsl['end_date']) ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a
                                                            href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?>"
                                                            class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>">
                                                            <?= ($rowsl['status'] == 'A') ?
                                                                '<span class="badge_padding badge_success w-max-con text-uppercase">Active</span>'
                                                                : '<span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>' ?>
                                                        </a>


                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editregistrationcutoff" onclick="loadDataOnEditCutoff(<?= $rowsl['id']; ?>)" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteCutoff&id=<?= $rowsl['id']; ?>"
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto"
                                                            onclick="return confirm('Do you really want to remove this record');">
                                                            <?php delete(); ?>
                                                        </a>
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
                    <div class="com_info_box_grid_box">
                        <div class="com_info_box_inner">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Conference Date</span><a class="add mi-1 popup-btn" data-tab="addconferencedate"><?php add() ?>Add Date</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Conference Dates</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql_cd['QUERY'] = "SELECT * FROM " . _DB_CONFERENCE_DATE_ . " WHERE status != 'D' ORDER BY id";
                                        $res_cd = $mycms->sql_select($sql_cd);
                                        $k = 1;
                                        if (!empty($res_cd)) {
                                            foreach ($res_cd as $r) {
                                        ?>
                                                <tr>
                                                    <td class="sl"><?= $k ?></td>
                                                    <td><?= displayDateFormat($r['conf_date']) ?></td>
                                                    <td>
                                                        <div class="action_div">
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?= ($r['status'] == 'A') ? 'InactiveDate' : 'ActiveDate' ?>&id=<?= $r['id']; ?>"
                                                                class="<?= ($r['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>">
                                                                <?= ($r['status'] == 'A') ? '<span class="badge_padding badge_success w-max-con text-uppercase">Active</span>' : '<span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>' ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="action">
                                                        <div class="action_div">
                                                            <a href="javascript:void(0)" onclick="loadDataOnEditConference(<?= $r['id']; ?>)" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" data-tab="editconferencedate"><?php edit(); ?></a>
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteDate&id=<?= $r['id']; ?>"
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto"
                                                                onclick="return confirm('Do you really want to remove this record');">
                                                                <?php delete(); ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                                $k++;
                                            }
                                        } else {
                                            echo '<tr><td colspan="4" class="text-center">No conference dates found</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_secondary"><?php workshop() ?></span> Workshop Cutoff</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1 popup-btn" data-tab="addworkshopcutoff"><?php add() ?>Add Cutoff</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Cutoff Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // fetch workshop cutoffs from DB
                                        $sql_wk['QUERY'] = "SELECT * FROM " . _DB_WORKSHOP_CUTOFF_ . " WHERE status != 'D' ORDER BY id";
                                        $res_wk = $mycms->sql_select($sql_wk);
                                        $j = 1;
                                        if (!empty($res_wk)) {
                                            foreach ($res_wk as $rowsl) {
                                        ?>
                                                <tr>
                                                    <td class="sl"><?= $j ?></td>
                                                    <td><?= htmlspecialchars($rowsl['cutoff_title']) ?></td>
                                                    <td><?= displayDateFormat($rowsl['start_date']) ?></td>
                                                    <td><?= displayDateFormat($rowsl['end_date']) ?></td>
                                                    <td>
                                                        <div class="action_div">
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?= ($rowsl['status'] == 'A') ? 'InactiveWorkshop' : 'ActiveWorkshop' ?>&id=<?= $rowsl['id']; ?>"
                                                                class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>">
                                                                <?= ($rowsl['status'] == 'A') ?
                                                                    '<span class="badge_padding badge_success w-max-con text-uppercase">Active</span>'
                                                                    : '<span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>' ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="action">
                                                        <div class="action_div">
                                                            <a data-tab="editworkshopcutoff" onclick="loadDataOnEditWorkshopCutoff(<?= $rowsl['id']; ?>)" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteWorkshopCutoff&id=<?= $rowsl['id']; ?>"
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto"
                                                                onclick="return confirm('Do you really want to remove this record');">
                                                                <?php delete(); ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                                $j++;
                                            }
                                        } else {
                                            echo '<tr><td colspan="6" class="text-center">No workshop cutoffs found</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====================================== 3. REGISTRATION ====================================== -->
            <div class="com_info_box" id="registration">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_success"><?php conregi() ?></span> Registration Classifications</n>
                            <a class="add mi-1 popup-btn" data-tab="newregistrationclassification"><?php add() ?>Add Classification</a>
                        </h5>
                        <!-- <div class="accm_listing">
                            <div class="accm_box">
                                <?php
                                $sql_cls = ['QUERY' => "SELECT * FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " WHERE status != 'D' ORDER BY sequence_by ASC"];
                                $res_cls = $mycms->sql_select($sql_cls);
                                $n = 1;
                                if (!empty($res_cls)) {
                                    foreach ($res_cls as $c) {
                                        $icon_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $c['icon'];

                                ?>
                                        <div class="accm_top">
                                            <div class="spot_name d-flex align-items-start">
                                                <div class="regi_img_circle">
                                                    <img src="<?= $icon_image ?>" alt="" class="w-100 h-100">
                                                </div>
                                                <div>
                                                    <div class="regi_name"><?= htmlspecialchars($c['classification_title']) ?> - <?= htmlspecialchars($c['type']) ?></div>
                                                </div>
                                            </div>
                                            <div class="accm_details">
                                                <div class="accm_details_box">
                                                    <h5>Seat Limits</h5>
                                                    <h6><?= htmlspecialchars($c['seat_limit']) ?></h6>

                                                </div>
                                                <div class="accm_details_box">
                                                    <h5>Created Date</h5>
                                                    <h6><?php calendar() ?><?= displayDateFormat($c['created_dateTime'] ?? $c['created_datetime'] ?? '') ?></h6>
                                                </div>
                                                <div class="accm_details_box action" style="flex:unset;">
                                                    <h5 class="text-right">Status</h5>
                                                    <div class="action_div">
                                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_classification.process.php?act=<?= ($c['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $c['id']; ?>" class="<?= ($c['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>">
                                                            <?= ($c['status'] == 'A') ? '<span class="badge_padding badge_success w-max-con text-uppercase">Active</span>' : '<span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>' ?>
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                <?php
                                        $n++;
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">No classifications found</td></tr>';
                                }
                                ?>
                                <div class="accm_bottom justify-content-end">
                                    <div class="spot_box_bottom_right">
                                        <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent" data-tab="editregistrationclassification"><?php edit(); ?>Edit</a>
                                        <a href="javascript:void(null)" class="icon_hover badge_danger action-transparent"><?php delete(); ?>Delete</a>
                                        <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Registration Tariff<i class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="accm_tariff spot_service_break">
                                    <div class="service_breakdown_wrap mt-0">
                                        <h4><?php rupee() ?>Tariff Breakdown</h4>
                                        <div class="table_wrap">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Registration Classification</th>
                                                        <th class="text-right">Early Bird</th>
                                                        <th class="text-right">Regular</th>
                                                        <th class="text-right">Advance</th>
                                                        <th class="text-right">Spot</th>
                                                        <th class="action">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Delegate</td>
                                                        <td class="text-right">INR 4000.00</td>
                                                        <td class="text-right">INR 6000.00</td>
                                                        <td class="text-right">INR 8000.00</td>
                                                        <td class="text-right">INR 12000.00</td>
                                                        <td class="action">
                                                            <div class="action_div">
                                                                <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editregistrationtariff"><?php edit(); ?></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="com_info_box_inner">
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Classification Title</th>
                                            <th>Seat Limit</th>
                                            <th>Created Date</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql_cls = ['QUERY' => "SELECT * FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " WHERE status != 'D' ORDER BY sequence_by ASC"];
                                        $res_cls = $mycms->sql_select($sql_cls);
                                        $n = 1;
                                        if (!empty($res_cls)) {
                                            foreach ($res_cls as $c) {
                                        ?>
                                                <tr>
                                                    <td class="sl"><?= $n ?></td>
                                                    <td><?= htmlspecialchars($c['classification_title']) ?> - <?= htmlspecialchars($c['type']) ?></td>
                                                    <td><?= htmlspecialchars($c['seat_limit']) ?></td>
                                                    <td><?= displayDateFormat($c['created_dateTime'] ?? $c['created_datetime'] ?? '') ?></td>
                                                    <td>
                                                        <div class="action_div">
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_classification.process.php?act=<?= ($c['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $c['id']; ?>" class="<?= ($c['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>">
                                                                <?= ($c['status'] == 'A') ? '<span class="badge_padding badge_success w-max-con text-uppercase">Active</span>' : '<span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>' ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="action">
                                                        <div class="action_div">
                                                            <a href="javascript:void(0)" onclick="loadDataOnEditClassification(<?= $c['id']; ?>)" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" data-tab="editregistrationclassification"><?php edit(); ?></a>
                                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_classification.process.php?act=Remove&id=<?= $c['id']; ?>" class="icon_hover badge_danger action-transparent br-5 w-auto" onclick="return confirm('Do you really want to remove this classification?');"><?php delete(); ?></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                                $n++;
                                            }
                                        } else {
                                            echo '<tr><td colspan="6" class="text-center">No classifications found</td></tr>';
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_success"><?php credit() ?></span> Registration Tariff</n>
                        </h5>

                        <div class="com_info_box_inner">
                            <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1"><?php add() ?>Add Cutoff</a></h4> -->
                            <div class="table_wrap">
                                <table>
                                    <?

                                    $sql_cutoff['QUERY']    =    "SELECT cutoff.cutoff_title  
											   FROM " . _DB_TARIFF_CUTOFF_ . " cutoff
										      WHERE status = 'A'";
                                    $res = $mycms->sql_select($sql_cutoff);

                                    $registrationDetails = getAllRegistrationTariffs();
                                    //echo'<pre>';print_r($registrationDetails);echo'</pre>';						
                                    ?>
                                    <thead>
                                        <tr>
                                            <th>Registration Classification</th>
                                            <?
                                            foreach ($res as $k => $title) {
                                            ?>
                                                <th class="text-right"><?= strip_tags($title['cutoff_title']) ?></th>
                                            <?
                                            }
                                            ?>

                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($registrationDetails) {
                                            foreach ($registrationDetails as $key => $registrationDetailsVal) {

                                        ?>
                                                <tr>
                                                    <td><?= getRegClsfName($key) ?></td>
                                                    <?
                                                    foreach ($registrationDetailsVal as $keyCutoff => $rowCutoff) {
                                                    ?>
                                                        <td class="text-right"><?= $rowCutoff['CURRENCY'] ?> <?= $rowCutoff['AMOUNT'] ?></td>
                                                    <?php
                                                    }
                                                    ?>

                                                    <td class="action">
                                                        <div class="action_div">
                                                            <a href="javascript:void(0)" onclick="loadDataOnEditRegTariff(<?= $key ?>)" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" data-tab="editRegistrationTariff"><?php edit(); ?></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="7" align="center">
                                                    <span class="mandatory">No Record Present.</span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================= 4.  WORKSHOP =============================================== -->
            <div class="com_info_box" id="workshop">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_info"><?php workshop() ?></span> Workshop Type</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Workshop Type</span><a class="add mi-1 popup-btn" data-tab="addworkshoptype"><?php add() ?>Add Type</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Workshop Type</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td>Normal</td>
                                            <td>
                                                <div class="action_div">
                                                    <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                    <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                </div>
                                            </td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editworkshop"><?php edit(); ?></a>
                                                    <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_info"><?php workshop() ?></span> Workshops</n>
                            <a class="add mi-1 popup-btn" data-tab="addworkshop"><?php add() ?>Add Workshop</a>
                        </h5>
                        <div class="com_info_box_inner d-none">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Workshop</span><a class="add mi-1 popup-btn" data-tab="addworkshop"><?php add() ?>Add Workshop</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Workshop</th>
                                            <th>Seat</th>
                                            <th>Venue</th>
                                            <th>Date</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td>TB & Critical Care Workshop - WORKSHOP</td>
                                            <td>100</td>
                                            <td>Calcutta Medical Research Institute (CMRI)</td>
                                            <td>18/12/2025</td>
                                            <td>
                                                <div class="action_div">
                                                    <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                    <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                </div>
                                            </td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editworkshop"><?php edit(); ?></a>
                                                    <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="accm_listing">
                            <div class="accm_box">
                                <div class="accm_top">
                                    <div class="spot_name">

                                        <div>
                                            <div class="regi_name">Delegate - DELEGATE</div>
                                            <div class="regi_contact">
                                                <span>
                                                    <?php address() ?>Calcutta Medical Research Institute (CMRI)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accm_details">
                                        <div class="accm_details_box">
                                            <h5>Seat</h5>
                                            <h6>500</h6>

                                        </div>
                                        <div class="accm_details_box">
                                            <h5>Date</h5>
                                            <h6><?php calendar() ?>19/11/2025</h6>
                                        </div>
                                        <div class="accm_details_box action" style="flex:unset;">
                                            <h5 class="text-right">Status</h5>
                                            <div class="action_div">
                                                <span class="pay_status badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                <span class="pay_status badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="accm_bottom justify-content-end">
                                    <div class="spot_box_bottom_right">
                                        <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent" data-tab="editworkshop"><?php edit(); ?>Edit</a>
                                        <a href="javascript:void(null)" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Workshop Tariff<i class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="accm_tariff spot_service_break">
                                    <div class="service_breakdown_wrap mt-0">
                                        <h4><?php rupee() ?>Tariff Breakdown</h4>
                                        <div class="table_wrap">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">Classification</th>
                                                        <th class="text-right" colspan="2">Early Bird</th>
                                                        <th class="text-right" colspan="2">Regular</th>
                                                        <th class="text-right" colspan="2">Advance</th>
                                                        <th class="text-right" colspan="2">Spot</th>
                                                        <th class="action" rowspan="2">Action</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Delegate</td>
                                                        <td class="text-right">4000.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">8000.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">4000.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">8000.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="action">
                                                            <div class="action_div">
                                                                <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editworkshoptariff"><?php edit(); ?></a>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="com_info_box_grid_box d-none">
                        <h5 class="com_info_box_head">
                            <n><span class="text_info"><?php credit() ?></span> Workshop Tariff</n>
                        </h5>
                        <div class="com_info_box_inner">
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Workshop Name</th>
                                            <th class="text-right">Workshop Tariff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>TB &amp; Critical Care Workshop</td>
                                            <td>
                                                <div class="action_div"><a class="badge_secondary br-5 wrkshp_trak w-auto">Workshop Tariff <g><?php down() ?></g></a></div>
                                            </td>
                                        </tr>
                                        <tr class="sub_table_tr">
                                            <td colspan="2">
                                                <div class="table_wrap">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2">Classification</th>
                                                                <th class="text-right" colspan="2">Early Bird</th>
                                                                <th class="text-right" colspan="2">Regular</th>
                                                                <th class="text-right" colspan="2">Advance</th>
                                                                <th class="text-right" colspan="2">Spot</th>
                                                                <th class="action" rowspan="2">Action</th>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-right">INR</th>
                                                                <th class="text-right">USD</th>
                                                                <th class="text-right">INR</th>
                                                                <th class="text-right">USD</th>
                                                                <th class="text-right">INR</th>
                                                                <th class="text-right">USD</th>
                                                                <th class="text-right">INR</th>
                                                                <th class="text-right">USD</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Delegate</td>
                                                                <td class="text-right">4000.00</td>
                                                                <td class="text-right">0.00</td>
                                                                <td class="text-right">8000.00</td>
                                                                <td class="text-right">0.00</td>
                                                                <td class="text-right">4000.00</td>
                                                                <td class="text-right">0.00</td>
                                                                <td class="text-right">8000.00</td>
                                                                <td class="text-right">0.00</td>
                                                                <td class="action">
                                                                    <div class="action_div">
                                                                        <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editworkshoptariff"><?php edit(); ?></a>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>TB &amp; Critical Care Workshop</td>
                                            <td>
                                                <div class="action_div"><a class="badge_secondary br-5 wrkshp_trak w-auto">Track Workshop <g><?php down() ?></g></a></div>
                                            </td>
                                        </tr>
                                        <tr class="sub_table_tr">
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="com_info_box" id="accommodation">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_danger"><?php hotel() ?></span> Accommodation</n>
                            <a class="add mi-1 popup-btn" data-tab="newhotel"><?php add(); ?>Add Hotel</a>
                        </h5>
                        <div class="accm_listing">
                            <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Accommodation</span><a class="add mi-1 popup-btn" data-tab="newhotel"><?php add(); ?>Add Hotel</a></h4> -->
                            <div class="accm_box">
                                <div class="accm_top">
                                    <div class="accm_name">
                                        <div class="regi_name">Hotel Sonar Bangla, Mandarmani</div>
                                        <!-- <div class="regi_type">
                                                <span class="badge_padding badge_default">Package 1</span>
                                                <span class="badge_padding badge_default">Package 2</span>
                                            </div> -->
                                        <div class="regi_contact">
                                            <span>
                                                <?php calendar() ?>17/01/2025<i class="fal fa-long-arrow-right ml-1 mr-1"></i>17/01/2025
                                            </span>
                                            <span>
                                                <?php call() ?>9674833617
                                            </span>
                                            <span>
                                                <?php address() ?>Mandarmoni Beach Road, West Bengal
                                            </span>
                                            <span>
                                                <?php address() ?>2km from Venue
                                            </span>
                                        </div>
                                    </div>
                                    <div class="accm_details">
                                        <div class="accm_details_box">
                                            <h5>Seat Limits</h5>
                                            <ul class="accm_ul aminity_ul">
                                                <li><?php calendar() ?>
                                                    <n class="d-inline-block">17/01/2025</n>
                                                    <i class="fal fa-long-arrow-right"></i>
                                                    <n class="d-inline-block">50</n>
                                                </li>
                                                <li><?php calendar() ?>
                                                    <n class="d-inline-block">17/01/2025</n>
                                                    <i class="fal fa-long-arrow-right"></i>
                                                    <n class="d-inline-block">50</n>
                                                </li>
                                                <li><?php calendar() ?>
                                                    <n class="d-inline-block">17/01/2025</n>
                                                    <i class="fal fa-long-arrow-right"></i>
                                                    <n class="d-inline-block">50</n>
                                                </li>
                                                <li><?php calendar() ?>
                                                    <n class="d-inline-block">17/01/2025</n>
                                                    <i class="fal fa-long-arrow-right"></i>
                                                    <n class="d-inline-block">50</n>
                                                </li>
                                            </ul>

                                        </div>
                                        <div class="accm_details_box action" style="flex:unset;">
                                            <h5 class="text-right">Status</h5>
                                            <div class="action_div">
                                                <span class="pay_status badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                <span class="pay_status badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="accm_bottom justify-content-end">
                                    <div class="spot_box_bottom_right">
                                        <a href="javascript:void(null)" class="popup-btn icon_hover badge_primary action-transparent" data-tab="viewhotel"><?php view(); ?>View</a>
                                        <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent" data-tab="edithotel"><?php edit(); ?>Edit</a>
                                        <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        <a href="#" class="drp icon_hover badge_dark action-transparent">Accommodation Tariff<i class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="accm_tariff spot_service_break">
                                    <div class="service_breakdown_wrap mt-0">
                                        <h4><?php rupee() ?>Tariff Breakdown</h4>
                                        <div class="table_wrap">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th class="sl" rowspan="2">#</th>
                                                        <th rowspan="2">Room Type</th>
                                                        <th rowspan="2">Packages</th>
                                                        <th class="text-right" colspan="2">Early Bird</th>
                                                        <th class="text-right" colspan="2">Regular</th>
                                                        <th class="action" rowspan="2">Action</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td rowspan="2" class="sl">1</td>
                                                        <td rowspan="2">Premium</td>
                                                        <td>Single</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td rowspan="2" class="action">
                                                            <div class="action_div">
                                                                <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="hoteltariff"><i class="fal fa-pencil"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Twin</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="com_info_box" id="dinner">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_info"><?php dinner() ?></span> Dinner</n>
                            <a class="add mi-1 popup-btn" data-tab="adddinner"><?php add() ?>Add Dinner</a>
                        </h5>
                        <div class="com_info_box_inner d-none">
                            <h4 class="com_info_box_inner_sub_head"><span>Manage Dinner</span><a class="add mi-1 popup-btn" data-tab="adddinner"><?php add() ?>Add Dinner</a></h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Dinner Classification</th>
                                            <th>Date</th>
                                            <th>Hotel</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td><span>Gala dinner</span>
                                                <div class="regi_contact">
                                                    <span>
                                                        <i class="fal fa-globe"></i>Link
                                                    </span>
                                                </div>
                                            </td>
                                            <td>17/01/2025</td>
                                            <td>Hotel Sonar Bangla, Mandarmani</td>
                                            <td>
                                                <div class="action_div">
                                                    <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                    <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                </div>
                                            </td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editdinner"><?php edit(); ?></a>
                                                    <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="accm_listing">
                            <div class="accm_box">
                                <div class="accm_top">
                                    <div class="spot_name">
                                        <div>
                                            <div class="regi_name">Gala dinner</div>
                                            <div class="regi_contact">
                                                <span>
                                                    <?php calendar() ?>Hotel Sonar Bangla, Mandarmani
                                                </span>
                                                <span>
                                                    <?php address() ?>Hotel Sonar Bangla, Mandarmani
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accm_details">
                                        <div class="accm_details_box">
                                            <h5>Link</h5>
                                            <h6><a href="#" target="_blank">link</a></h6>
                                        </div>
                                        <div class="accm_details_box action" style="flex:unset;">
                                            <h5 class="text-right">Status</h5>
                                            <div class="action_div">
                                                <span class="pay_status badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                <span class="pay_status badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="accm_bottom justify-content-end">
                                    <div class="spot_box_bottom_right">
                                        <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent" data-tab="editworkshop"><?php edit(); ?>Edit</a>
                                        <a href="javascript:void(null)" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Dinner Tariff<i class="fal fa-angle-down"></i></a>
                                    </div>
                                </div>
                                <div class="accm_tariff spot_service_break">
                                    <div class="service_breakdown_wrap mt-0">
                                        <h4><?php rupee() ?>Tariff Breakdown</h4>
                                        <div class="table_wrap">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th class="sl" rowspan="2">#</th>
                                                        <th rowspan="2">Dinner</th>
                                                        <th class="text-right" colspan="2">Early Bird</th>
                                                        <th class="text-right" colspan="2">Regular</th>
                                                        <th class="action" rowspan="2">Action</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                        <th class="text-right">INR</th>
                                                        <th class="text-right">USD</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="sl">1</td>
                                                        <td>Accompanying Person</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="text-right">0.00</td>
                                                        <td class="action">
                                                            <div class="action_div">
                                                                <a class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="accompanytariff"><i class="fal fa-pencil"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ================================================ 7. ACCOMPANY ===================================================================== -->
            <div class="com_info_box" id="accompany">
                <div class="com_info_box_grid">
                    <div class="com_info_box_grid_box">
                        <h5 class="com_info_box_head">
                            <n><span class="text_info"><?php duser() ?></span> Accompany</n>
                            <!-- <a class="add mi-1 popup-btn" data-tab="addaccompany"><?php add() ?>Add Classification</a> -->
                        </h5>


                        <div class="accm_listing">
                            <?php
                            $sql_cal            =    array();
                            $sql_cal['QUERY']    =    "SELECT * 
													FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . "
													WHERE `status` 	!= 		?
													ORDER BY `id` ASC";

                            $sql_cal['PARAM'][]    =    array('FILD' => 'status',         'DATA' => 'D',         'TYP' => 's');

                            $res_cal = $mycms->sql_select($sql_cal);

                            $i = 1;

                            foreach ($res_cal as $key => $rowsl) {
                            ?>
                                <div class="accm_box">
                                    <div class="accm_top">

                                        <div class="spot_name">

                                            <div>
                                                <div class="regi_name"><?= $rowsl['classification_title'] ?> </div>

                                            </div>
                                        </div>

                                        <div class="accm_details">
                                            <div class="accm_details_box">
                                                <h5>Created Date</h5>
                                                <h6><?php calendar() ?><?= displayDateFormat($rowsl['created_dateTime']) ?></h6>
                                            </div>
                                            <div class="accm_details_box action" style="flex:unset;">
                                                <h5 class="text-right">Status</h5>
                                                <div class="action_div">
                                                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>">
                                                        <?= ($rowsl['status'] == 'A') ? '<span class="pay_status badge_padding badge_success w-max-con text-uppercase">Active</span>' :
                                                            '<span class="pay_status badge_padding badge_danger w-max-con text-uppercase">Inactive</span>' ?></a>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="accm_bottom justify-content-end">
                                        <div class="spot_box_bottom_right">
                                            <a href="javascript:void(null)" class="popup-btn icon_hover badge_secondary action-transparent" data-tab="editworkshop"><?php edit(); ?>Edit</a>
                                            <a href="javascript:void(null)" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                            <a href="javascript:void(null)" class="drp icon_hover badge_dark action-transparent">Accompany Tariff<i class="fal fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="accm_tariff spot_service_break" >
                                        <div class="service_breakdown_wrap mt-0">
                                            <h4><?php rupee() ?>Tariff Breakdown</h4>
                                            <div class="table_wrap">
                                                <table>
                                                    <?

                                                    $sqlTariffCutoff['QUERY']    =    "SELECT cutoff.cutoff_title  
                                                                            FROM " . _DB_TARIFF_CUTOFF_ . " cutoff
                                                                            WHERE status = 'A'";
                                                    $resCutoff = $mycms->sql_select($sqlTariffCutoff);

                                                    $registrationDetails = getAllAccompanyTariffs();
                                                    //echo'<pre>';print_r($registrationDetails);echo'</pre>';						
                                                    ?>
                                                    <thead>
                                                        <tr>
                                                            <th class="sl" rowspan="2">#</th>
                                                            <th rowspan="2"><?= $rowsl['classification_title'] ?></th>
                                                            <?
                                                            foreach ($resCutoff as $k => $title) {
                                                            ?>
                                                                <th class="text-right" colspan="2"><?= strip_tags($title['cutoff_title']) ?></th>
                                                            <?
                                                            }
                                                            ?>

                                                            <th class="action" rowspan="2">Action</th>
                                                        </tr>
                                                        <tr>
                                                            <?
                                                            foreach ($resCutoff as $k => $title) {
                                                            ?>
                                                                <th class="text-right">INR</th>
                                                                <th class="text-right">USD</th>
                                                            <?
                                                            }
                                                            ?>

                                                        </tr>
                                                    </thead>
                                                    <tbody> <?php
                                                            if ($registrationDetails) {
                                                                foreach ($registrationDetails as $key => $registrationDetailsVal) {
                                                            ?>
                                                                <tr>
                                                                    <td class="sl">1</td>
                                                                    <td><?= $rowsl['classification_title'] ?></td>
                                                                    <?
                                                                    foreach ($registrationDetailsVal as $keyCutoff => $rowCutoff) {
                                                                    ?>
                                                                        <td class="text-right"> <?= $rowCutoff['AMOUNT'] ?></td>
                                                                        <td class="text-right">-</td>
                                                                    <?php
                                                                    }
                                                                    ?>

                                                                    <td class="action">
                                                                        <div class="action_div">
                                                                            <a onclick="loadDataOnEditAccompTariff(<?= $key ?>)" class="icon_hover badge_secondary action-transparent br-5 w-auto popup-btn" data-tab="editAccompanyTariff"><i class="fal fa-pencil"></i></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?
                                                                }
                                                            } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="7" align="center">
                                                                    <span class="mandatory">No Record Present.</span>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?
                                $i++;
                            }
                            ?>
                        </div>
                    </div>
                 
                </div>
            </div>
        </div>
    </div>
</div>

<?php
page_footer();