<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Tracking Report</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Spot</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tracking Analytics</li>
                    </ol>
                </nav>
                <h2>Tracking Analytics</h2>
                <h6>Comprehensive reports for kits, meals, and attendance tracking.</h6>
            </div>
        </div>
        <div class="regi_search_wrap mb-3">
            <div class="tracking_analytic_tab">
                <button data-tab="kit" class="active">kit</button>
                <button data-tab="breakfast">Breakfast</button>
                <button data-tab="lunch">Lunch</button>
                <button data-tab="dinner">Dinner</button>
                <button data-tab="workshop">Workshop</button>
                <button data-tab="exhibitor">Exhibitor</button>
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="javascript:void(null)"><?php export(); ?>Export</a>
                <a href="javascript:void(null)"><?php printi(); ?>Print</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <div class="filter_body">
                <div>
                    <label>From Date</label>
                    <input type="date">
                </div>
                <div>
                    <label>To Date</label>
                    <input type="date">
                </div>
            </div>
            <div class="filter_bottom">
                <button><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="tracking_analytic_box active" id="kit">
            <ul class="regi_data_grid_ul mb-3">
                <li>
                    <div>
                        <h5>Total Kit</h5>
                        <h4>2527</h4>
                        <h6 class="text_secondary">Across 20 record sets</h6>
                    </div>
                    <span class="badge_primary"><?php bag(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Scan Efficiency</h5>
                        <h4>92%</h4>
                        <h6 class="text_success">190 manual entries</h6>
                    </div>
                    <span class="badge_success"><?php qr(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Peak Time</h5>
                        <h4>1:00 PM</h4>
                        <h6 class="text_dark">Est. 245 scans/hr</h6>
                    </div>
                    <span class="badge_secondary"><?php clock(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Projected</h5>
                        <h4>2780</h4>
                        <h6 class="text_dark">+10% buffer est.</h6>
                    </div>
                    <span class="badge_info"><?php chartline() ?></span>
                </li>
            </ul>
            <div class="form_grid g_2">
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Delegate Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Faculty Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Student Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Accompanying Person Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tracking_analytic_box" id="breakfast">
            <ul class="regi_data_grid_ul mb-3">
                <li>
                    <div>
                        <h5>Total Breakfast</h5>
                        <h4>2527</h4>
                        <h6 class="text_secondary">Across 20 record sets</h6>
                    </div>
                    <span class="badge_primary"><?php bag(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Scan Efficiency</h5>
                        <h4>92%</h4>
                        <h6 class="text_success">190 manual entries</h6>
                    </div>
                    <span class="badge_success"><?php qr(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Peak Time</h5>
                        <h4>1:00 PM</h4>
                        <h6 class="text_dark">Est. 245 scans/hr</h6>
                    </div>
                    <span class="badge_secondary"><?php clock(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Projected</h5>
                        <h4>2780</h4>
                        <h6 class="text_dark">+10% buffer est.</h6>
                    </div>
                    <span class="badge_info"><?php chartline() ?></span>
                </li>
            </ul>
            <div class="form_grid g_2">
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Delegate Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Faculty Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Student Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Accompanying Person Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tracking_analytic_box" id="lunch">
            <ul class="regi_data_grid_ul mb-3">
                <li>
                    <div>
                        <h5>Total Lunch</h5>
                        <h4>2527</h4>
                        <h6 class="text_secondary">Across 20 record sets</h6>
                    </div>
                    <span class="badge_primary"><?php bag(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Scan Efficiency</h5>
                        <h4>92%</h4>
                        <h6 class="text_success">190 manual entries</h6>
                    </div>
                    <span class="badge_success"><?php qr(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Peak Time</h5>
                        <h4>1:00 PM</h4>
                        <h6 class="text_dark">Est. 245 scans/hr</h6>
                    </div>
                    <span class="badge_secondary"><?php clock(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Projected</h5>
                        <h4>2780</h4>
                        <h6 class="text_dark">+10% buffer est.</h6>
                    </div>
                    <span class="badge_info"><?php chartline() ?></span>
                </li>
            </ul>
            <div class="form_grid g_2">
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Delegate Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Faculty Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Student Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Accompanying Person Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tracking_analytic_box" id="dinner">
            <ul class="regi_data_grid_ul mb-3">
                <li>
                    <div>
                        <h5>Total Dinner</h5>
                        <h4>2527</h4>
                        <h6 class="text_secondary">Across 20 record sets</h6>
                    </div>
                    <span class="badge_primary"><?php bag(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Scan Efficiency</h5>
                        <h4>92%</h4>
                        <h6 class="text_success">190 manual entries</h6>
                    </div>
                    <span class="badge_success"><?php qr(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Peak Time</h5>
                        <h4>1:00 PM</h4>
                        <h6 class="text_dark">Est. 245 scans/hr</h6>
                    </div>
                    <span class="badge_secondary"><?php clock(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Projected</h5>
                        <h4>2780</h4>
                        <h6 class="text_dark">+10% buffer est.</h6>
                    </div>
                    <span class="badge_info"><?php chartline() ?></span>
                </li>
            </ul>
            <div class="form_grid g_2">
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Delegate Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Faculty Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Student Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Accompanying Person Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tracking_analytic_box" id="workshop">
            <ul class="regi_data_grid_ul mb-3">
                <li>
                    <div>
                        <h5>Total Workshop</h5>
                        <h4>2527</h4>
                        <h6 class="text_secondary">Across 20 record sets</h6>
                    </div>
                    <span class="badge_primary"><?php bag(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Scan Efficiency</h5>
                        <h4>92%</h4>
                        <h6 class="text_success">190 manual entries</h6>
                    </div>
                    <span class="badge_success"><?php qr(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Peak Time</h5>
                        <h4>1:00 PM</h4>
                        <h6 class="text_dark">Est. 245 scans/hr</h6>
                    </div>
                    <span class="badge_secondary"><?php clock(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Projected</h5>
                        <h4>2780</h4>
                        <h6 class="text_dark">+10% buffer est.</h6>
                    </div>
                    <span class="badge_info"><?php chartline() ?></span>
                </li>
            </ul>
            <div class="form_grid g_2">
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Delegate Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Faculty Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Student Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Accompanying Person Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tracking_analytic_box" id="exhibitor">
            <ul class="regi_data_grid_ul mb-3">
                <li>
                    <div>
                        <h5>Total Exhibitor</h5>
                        <h4>2527</h4>
                        <h6 class="text_secondary">Across 20 record sets</h6>
                    </div>
                    <span class="badge_primary"><?php bag(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Scan Efficiency</h5>
                        <h4>92%</h4>
                        <h6 class="text_success">190 manual entries</h6>
                    </div>
                    <span class="badge_success"><?php qr(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Peak Time</h5>
                        <h4>1:00 PM</h4>
                        <h6 class="text_dark">Est. 245 scans/hr</h6>
                    </div>
                    <span class="badge_secondary"><?php clock(); ?></span>
                </li>
                <li>
                    <div>
                        <h5>Projected</h5>
                        <h4>2780</h4>
                        <h6 class="text_dark">+10% buffer est.</h6>
                    </div>
                    <span class="badge_info"><?php chartline() ?></span>
                </li>
            </ul>
            <div class="form_grid g_2">
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Delegate Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Faculty Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Student Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tracking_report_box">
                    <div class="tracking_report_box_head">
                        <div class="tracking_report_box_head_left">
                            <span><?php chartline() ?></span>
                            <p>Accompanying Person Report<n>Records Found: 5</n>
                            </p>
                        </div>
                        <div class="tracking_report_box_head_right">
                            Category Total<n>535</n>
                        </div>
                    </div>
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Scanned</th>
                                    <th class="text-center">Manual</th>
                                    <th class="text-center">Total</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>5 December 2025</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">
                                        <div class="action_div">
                                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent br-5 w-auto"><?php view(); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUMMARY</td>
                                    <td class="text-center"><span class="text_success">104</span></td>
                                    <td class="text-center"><span class="text_default">16</span></td>
                                    <td class="text-center">120</td>
                                    <td class="action">

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
</html>