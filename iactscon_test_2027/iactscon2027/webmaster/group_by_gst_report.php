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
                        <li class="breadcrumb-item"><a href="#">GST</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Group By GST Report</li>
                    </ol>
                </nav>
                <h2>Group By GST Report</h2>
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
                <div class="spot_box_top align-items-center">
                    <div class="spot_name">
                        <div>
                            <div class="regi_name">Delegate - DELEGATE</div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Service Amount</h5>
                            <p><?php rupee() ?> 5000</p>
                        </div>
                        <div class="spot_details_box">
                            <h5>CGST Amount</h5>
                            <p><?php rupee() ?> 5000</p>
                        </div>
                        <div class="spot_details_box">
                            <h5>SGST Amount</h5>
                            <p><?php rupee() ?> 5000</p>
                        </div>
                        <div class="spot_details_box">
                            <h5>Invoice Amount</h5>
                            <p><?php rupee() ?> 5000</p>
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