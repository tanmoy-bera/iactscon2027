<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Finance</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Finance</a></li>
                        <li class="breadcrumb-item"><a href="#">Card Settlement</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Card Settlement Report</li>
                    </ol>
                </nav>
                <h2>Card Settlement Report</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>
         <ul class="regi_data_grid_ul mb-3">
            <li>
                <div>
                    <h5>Total Invoice Amount</h5>
                    <h4>₹ 45</h4>
                </div>
                <span class="badge_info"><?php rupee() ?></span>
            </li>
            <li>
                <div>
                    <h5>Total Settlement Amount</h5>
                    <h4>₹ 45</h4>
                </div>
                <span class="badge_info"><?php rupee() ?></span>
            </li>
        </ul>
          <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <div class="filter_body">
                <div>
                    <label>Registration Type</label>
                    <select>
                        <option>All Types</option>
                    </select>
                </div>
                <div>
                    <label>Payment Status</label>
                    <select>
                        <option>All Status</option>
                    </select>
                </div>
                <div>
                    <label>Registration Date</label>
                    <input type="date">
                </div>
            </div>
            <div class="filter_bottom">
                <button><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="spot_listing">
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name ">
                        <div>
                            <div class="regi_name">##SLIP210126-000008</div>
                            <div class="regi_contact">
                                <span>
                                    <?php rupee(); ?>680594
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Transaction Details</h5>
                            <p>NATCON 2025-000011 </p>
                            <small><?php calendar() ?> 21-01-2026</small>
                        </div>
                        <div class="spot_details_box">
                            <h5>Settlement Details</h5>
                            <p><?php rupee() ?> 5000 </p>
                            <small><?php calendar() ?> 21-01-2026</small>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Payment Status</h5>
                            <span class="mi-1 pay_status badge_padding badge_danger w-max-con text-uppercase"><?php unpaid(); ?>Unpaid</span>
                            <h6>INR 14,000</h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom">
                    <div class="spot_box_bottom_left">
                        <h6 class="badge_secondary">Total Amount: <n><?php rupee() ?> 5000</n>
                        </h6>
                    </div>
                    <div class="spot_box_bottom_right">
                        <!-- <a href="#" class="icon_hover badge_danger action-transparent">Cancel Invoice</a> -->
                        <!-- <a href="#" class="drp icon_hover badge_dark action-transparent"><i class="fal fa-angle-down"></i></a> -->
                    </div>
                </div>
                <div class="spot_service_break">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><i class="fal fa-window-maximize"></i>Service Breakdown</h4>
                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Reference</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            Conference Registration
                                        </td>
                                        <td>
                                            NATCON 2025-00033
                                        </td>
                                        <td class="text-right">
                                            ₹ 6,000
                                        </td>
                                        <td class="text-right">
                                            <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><i class="far fa-check-circle"></i>Paid</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Conference Registration
                                        </td>
                                        <td>
                                            NATCON 2025-00033
                                        </td>
                                        <td class="text-right">
                                            ₹ 6,000
                                        </td>
                                        <td class="text-right">
                                            <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><i class="far fa-check-circle"></i>Paid</span>
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
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>