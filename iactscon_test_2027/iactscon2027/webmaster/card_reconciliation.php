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
                        <li class="breadcrumb-item active" aria-current="page">Card Reconciliation</li>
                    </ol>
                </nav>
                <h2>Card Reconciliation</h2>
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
                            <h5>Transaction Details</h5>
                            <p>NATCON 2025-000011 </p>
                            <small><?php calendar() ?> 21-01-2026</small>
                        </div>
                        <div class="spot_details_box">
                            <h5>RPR No.</h5>
                            <p>0000</p>
                        </div>
                        <div class="spot_details_box">
                            <h5>RRN No.</h5>
                            <p>0000</p>
                        </div>
                        <div class="spot_details_box">
                            <h5>Remarks</h5>
                            <p>Lorem Ipsum</p>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom">
                   <div class="spot_box_bottom_left">
                        <h6 class="badge_info">Transaction Amount: <n><?php rupee() ?> 5000</n>
                        </h6>
                    </div>
                    <div class="spot_box_bottom_right">
                        <a href="#" class="icon_hover badge_secondary action-transparent"><?php edit() ?>Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>