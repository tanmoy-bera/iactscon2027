<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Card Identifier</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Spot</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Card Identifier</li>
                    </ol>
                </nav>
                <h2>Guest Card Identifier</h2>
                <h6>Scan QR code or Barcode to identify participant details instantly.</h6>
            </div>
           
        </div>

        <div class="indentify_wrap">
            <div class="indentify_left">
                <div class="card_indetify">
                    <span><i class="fal fa-scanner"></i></span>
                    <h5>Ready to Scan</h5>
                    <p>Place cursor in the box below and scan card</p>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <input placeholder="Scan Here" class="text-center">
                        </div>
                    </div>
                    <p class="track_frm_btm justify-content-center">
                        <a href="#" class="badge_success"><i class="fal fa-print"></i>Identify Card</a>
                    </p>
                </div>
            </div>
            <div class="indentify_right">
                <h6 class="accm_add_empty">No card scanned yet.</h6>
                <div class="identify_details d-none">
                    <div class="d-flex align-items-center">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                            <div class="regi_type mb-0">
                                <span class="badge_padding badge_default">NATCON-9988</span>
                            </div>
                        </div>
                    </div>

                    <ul>
                        <li>
                            <span>Category</span>
                            <n>Delegate</n>
                        </li>
                        <li>
                            <span>Status</span>
                            <n class="text_success">Paid</n>
                        </li>
                        <li>
                            <span>Access Level</span>
                            <n>Full</n>
                        </li>
                        <li>
                            <span>Last Activity</span>
                            <n>10:45 AM - Main Gate</n>
                        </li>
                    </ul>
                    <p class="track_frm_btm justify-content-start">
                         <a href="#" class="badge_dark">Clear</a>
                        <a href="#" class="badge_success">Allow Entry</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>