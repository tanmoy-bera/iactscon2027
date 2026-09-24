<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Id Card</h2>
       <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Spot Registration</li>
                    </ol>
                </nav>
                <h2>Spot Registration</h2>
                <h6>Issue ID cards, kits, and manage on-spot requests.</h6>
            </div>
            <div class="page_top_wrap_right">
                <p><?php printi(); ?>Card Printed: <b>0</b></p>
                <p><?php check(); ?>Delevered: <b>0</b></p>
                <p><?php duser(); ?>Accoumpany: <b>0</b></p>
                <p><?php user(); ?>Total: <b>2</b></p>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newregistartion"><?php add(); ?>New Reg</a>
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
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                            <div class="regi_type">
                                <span class="badge_padding badge_primary">Delegate</span>
                                <span class="badge_padding badge_secondary">Early Bird</span>
                            </div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?>9674833617
                                </span>
                                <span>
                                    <?php email(); ?>drasim53@gmail.com
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Reg Number</h5>
                            <p>NATCON 2025-1176-0638</p>
                            <small>NATCON 2025-000334</small>
                        </div>
                        <div class="spot_details_box">
                            <h5>Sequence ID</h5>
                            <h6><?php qr(); ?><b>#82680594</b></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Date</h5>
                            <h6><?php calendar(); ?>19/11/2025</h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Payment Status</h5>
                            <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                            <h6>INR 14,000</h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom">
                    <div class="spot_box_bottom_left">
                        <h6 class="active">Participation Cert<?php circle(); ?><?php check(); ?></h6>
                        <h6>ID Card Delivered<?php circle(); ?><?php check(); ?></h6>
                    </div>
                    <div class="spot_box_bottom_right">
                        <a href="#" class="print"><?php printi(); ?>Print ID</a>
                        <a href="#"><?php bag(); ?>Kit</a>
                        <a href="#"><?php add(); ?>Services</a>
                        <a href="#" class="drp icon_hover badge_dark action-transparent"><?php down(); ?></a>
                    </div>
                </div>
                <div class="spot_service_break">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><?php windowi(); ?>Service Breakdown</h4>
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
                                            <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
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
                                            <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="spot_service_action">
                        <a href="#" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                        <a href="#" class="icon_hover badge_info action-transparent"><?php invoive(); ?>View Invoice</a>
                        <a href="#" class="icon_hover badge_danger action-transparent delet"><?php delete(); ?>Delete Record</a>
                    </div>
                </div>
            </div>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                            <div class="regi_type">
                                <span class="badge_padding badge_primary">Delegate</span>
                                <span class="badge_padding badge_secondary">Early Bird</span>
                            </div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?>9674833617
                                </span>
                                <span>
                                    <?php email(); ?>drasim53@gmail.com
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Reg Number</h5>
                            <p>NATCON 2025-1176-0638</p>
                            <small>NATCON 2025-000334</small>
                        </div>
                        <div class="spot_details_box">
                            <h5>Sequence ID</h5>
                            <h6><?php qr(); ?><b>#82680594</b></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Date</h5>
                            <h6><?php calendar(); ?>19/11/2025</h6>
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
                        <h6 class="active">Participation Cert<?php circle(); ?><?php check(); ?></h6>
                        <h6 class="active">ID Card Delivered<?php circle(); ?><?php check(); ?></h6>
                    </div>
                    <div class="spot_box_bottom_right">
                        <a href="#" class="print"><?php printi(); ?>Print ID</a>
                        <a href="#"><?php bag(); ?>Kit</a>
                        <a href="#"><?php add(); ?>Services</a>
                        <a href="#" class="drp icon_hover badge_dark action-transparent"><?php down(); ?></a>
                    </div>
                </div>
                <div class="spot_service_break">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><?php windowi(); ?>Service Breakdown</h4>
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
                                            <span class="mi-1 badge_padding badge_danger w-max-con text-uppercase"><?php unpaid(); ?>Unpaid</span>
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
                                            <span class="mi-1 badge_padding badge_danger w-max-con text-uppercase"><?php unpaid(); ?>Unpaid</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="spot_service_action">
                        <a href="#" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                        <a href="#" class="icon_hover badge_info action-transparent"><?php invoive(); ?>View Invoice</a>
                        <a href="#" class="icon_hover badge_danger action-transparent delet"><?php delete(); ?>Delete Record</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>