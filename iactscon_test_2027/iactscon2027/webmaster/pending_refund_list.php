<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Invoice Cancelation</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Invoice Cancelation</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pending Refund List</li>
                    </ol>
                </nav>
                <h2>Pending Refund List</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>
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
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">Dr. Asim Kumar Majumdar</div>
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
                            <h5>Inv Details</h5>
                            <p>NATCON 2025-000011 </p>
                            <small><?php calendar()?> 21-01-2026</small>
                        </div>
                        <div class="spot_details_box">
                            <h5>Slip Details</h5>
                            <h6>##SLIP210126-000008</h6>
                        </div>
                        <!-- <div class="spot_details_box">
                            <h5>Inv Amount</h5>
                            <h6><i class="fal fa-calendar"></i>19/11/2025</h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Action</h5>
                            <span class="mi-1 pay_status badge_padding badge_danger w-max-con text-uppercase"><?php close() ?>Close</span>
                        </div> -->
                    </div>
                </div>
                <div class="spot_box_bottom">
                    <div class="spot_box_bottom_left">
                        <h6 class="active">Invoice Amount: <n><?php rupee() ?> 5000</n></h6>
                    </div>
                    <div class="spot_box_bottom_right">
                        <a href="#" class="icon_hover badge_danger action-transparent">Cancel Invoice</a>
                        <a href="#" class="drp icon_hover badge_dark action-transparent"><i class="fal fa-angle-down"></i></a>
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