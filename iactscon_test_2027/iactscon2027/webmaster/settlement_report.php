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
                        <li class="breadcrumb-item active" aria-current="page">Settlement Report</li>
                    </ol>
                </nav>
                <h2>Settlement Report</h2>
                <h6>View attendance and payment details for all workshop sessions.</h6>
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
                                    <?php qr(); ?>#82680594
                                </span>
                                <span>
                                    <?php bill(); ?>##SLIP210126-000008
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
                        <div class="spot_details_box">
                            <h5>Settled Amount</h5>
                            <p><?php rupee() ?> 5000 </p>
                        </div>
                         <div class="spot_details_box">
                            <h5>Extra</h5>
                            <p><?php rupee() ?> 5000 </p>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom">
                    <div class="spot_box_bottom_left">
                        <h6 class="badge_secondary">Service Amount: <n><?php rupee() ?> 5000</n>
                        </h6>
                        <h6 class="badge_success">Int Hnd Chrg: <n><?php rupee() ?> 5000</n>
                        </h6>
                    </div>
                    <div class="spot_box_bottom_right">
                        <!-- <a href="#" class="icon_hover badge_danger action-transparent">Cancel Invoice</a> -->
                        <!-- <a href="#" class="drp icon_hover badge_dark action-transparent"><i class="fal fa-angle-down"></i></a> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="bbp-pagination">
            <div class="bbp-pagination-count">Showing 1 to 10 of 150 entries</div>
            <span class="paginationDisplay">
                <div class="pagination"><a>1 of 15 Pages</a><a class="selected">1</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=1">2</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=2">3</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=1">&gt;&gt;</a> <a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=14">Last</a></div>
            </span>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>