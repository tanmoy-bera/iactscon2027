<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Report</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Finance</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Slip Wise Report</li>
                    </ol>
                </nav>
                <h2>Slip Wise Report</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="#" class="badge_dark"><?php reseti() ?></a>
                <a href="slip_detail.php" class="badge_success"><i class="fal fa-arrow-right"></i>Proccess</a>
            </div>
        </div>
        <div class="registration-pop_body p-0">
            <div class="registration-pop_body_box">
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>General</span>
                    </h4>
                    <div class="form_grid">
                        <div class="frm_grp span_2">
                            <p class="frm-head">Unique Sequence</p>
                            <input>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Registration Id</p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">User Name</p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Registered</p>
                            <input type="date">
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Country</p>
                            <select>
                                <option>Select</option>
                                <option>Select</option>
                                <option>Select</option>
                                <option>Select</option>
                            </select>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">State</p>
                            <select>
                                <option>Select</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Services</span>
                        <a class="add mi-1"><i class="fal fa-check"></i>Select All</a>
                    </h4>
                    <div class="cus_check_wrap flex-column">
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop1">
                            <span class="checkmark">Only Conference</span>
                        </label>
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop2">
                            <span class="checkmark">Has Workshop</span>
                        </label>
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop1">
                            <span class="checkmark">Has Accompany</span>
                        </label>
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop2">
                            <span class="checkmark">Has Workshop</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="registration-pop_body_box">
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Conference Reg. Category</span>
                    </h4>
                    <div class="regi_category" style="display: block;">
                        <div class="cus_check_wrap flex-column">
                            <label class="cus_check category_check">
                                <input type="radio" name="regicategory" checked="">
                                <span class="checkmark">Early Bird</span>
                            </label>
                            <label class="cus_check category_check">
                                <input type="radio" name="regicategory">
                                <span class="checkmark">PGT</span>
                            </label>
                            <label class="cus_check category_check">
                                <input type="radio" name="regicategory">
                                <span class="checkmark">TB Worker</span>
                            </label>
                            <label class="cus_check category_check">
                                <input type="radio" name="regicategory">
                                <span class="checkmark">Faculty</span>
                            </label>
                            <label class="cus_check category_check">
                                <input type="radio" name="regicategory">
                                <span class="checkmark">Student</span>
                            </label>
                            <label class="cus_check category_check">
                                <input type="radio" name="regicategory">
                                <span class="checkmark">Exhibitor</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Workshop Category</span>
                        <a class="add mi-1"><i class="fal fa-check"></i>Select All</a>
                    </h4>
                    <div class="cus_check_wrap flex-column">
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop1">
                            <span class="checkmark">TB &amp; Critical Care Workshop</span>
                        </label>
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop2">
                            <span class="checkmark">TB &amp; Critical Care Workshop</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="registration-pop_body_box">
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Invoice Details</span>
                    </h4>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <p class="frm-head">Invoice No</p>
                            <input>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Slip No</p>
                            <input>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Invoice Date</p>
                            <input type="date">
                        </div>
                    </div>
                </div>
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Exclude</span>
                        <a class="add mi-1"><i class="fal fa-check"></i>Select All</a>
                    </h4>
                    <div class="cus_check_wrap flex-column">
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop1">
                            <span class="checkmark">Only Conference</span>
                        </label>
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop2">
                            <span class="checkmark">Has Workshop</span>
                        </label>
                        <label class="cus_check category_check workshop_check">
                            <input type="radio" name="workshop1">
                            <span class="checkmark">Has Accompany</span>
                        </label>
                    </div>
                </div>
                <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Payment</span>
                    </h4>
                    <div class="form_grid">
                        <div class="frm_grp span_2">
                            <p class="frm-head">Mode</p>
                            <div class="cus_check_wrap">
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Online</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Offline</span>
                                </label>
                            </div>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Status</p>
                            <div class="cus_check_wrap">
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Paid</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Unpaid</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="registration-pop_body_box_inner">
                    <h4 class="registration-pop_body_box_heading">
                        <span>Invoice</span>
                    </h4>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <p class="frm-head">Mode</p>
                            <div class="cus_check_wrap">
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Online</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Offline</span>
                                </label>
                            </div>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Status</p>
                            <div class="cus_check_wrap">
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Active</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Cancelled</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="gender">
                                    <span class="checkmark">Refunded</span>
                                </label>
                            </div>
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