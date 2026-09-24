<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Tracking</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Spot</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tracking</li>
                    </ol>
                </nav>
                <h2>Tracking</h2>
                <h6>Manage master data, invoice settings, and system content.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Tracking Menu</h6>
                <button data-tab="ef" class="com_info_left_click icon_hover badge_default active">Breakfast Tracking</button>
                <button data-tab="fi" class="com_info_left_click icon_hover badge_default action-transparent">Kit Tracking</button>
                <button data-tab="md" class="com_info_left_click icon_hover badge_default action-transparent">Lunch Tracking</button>
                <button data-tab="cc" class="com_info_left_click icon_hover badge_default action-transparent">Dinner Tracking</button>
                <button data-tab="wr" class="com_info_left_click icon_hover badge_default action-transparent">Workshop Tracking</button>
                <button data-tab="br" class="com_info_left_click icon_hover badge_default action-transparent">Exhibitor Tracking</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="ef">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">Track Breakfast Entry - 8 December 2025</h5>
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Track Breakfast</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Additional Track</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">No. Of People</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Remarks</p>
                                        <input>
                                    </div>
                                </div>
                                <p class="track_frm_btm">
                                    <a href="#" class="badge_success"><?php check(); ?>Submit</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="fi">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">Track Kit Entry For Delegate</h5>

                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Track Kit</span><a class="add">Total Track 0</a></h5>
                                <h5 class="tracking_sub_head">Kit Date: 20-November-2025</h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Additional Track (DELEGATE)</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">No. Of People</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Remarks</p>
                                        <input>
                                    </div>
                                </div>
                                <p class="track_frm_btm">
                                    <a href="#" class="badge_success"><?php check(); ?>Submit</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="md">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">Track Lunch Entry - 8-December-2025</h5>
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Track Lunch</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Additional Track</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">No. Of People</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Remarks</p>
                                        <input>
                                    </div>
                                </div>
                                <p class="track_frm_btm">
                                    <a href="#" class="badge_success"><?php check(); ?>Submit</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="cc">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">Track Dinner Entry - 8-December-2025</h5>
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Track Dinner</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Additional Track</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">No. Of People</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Remarks</p>
                                        <input>
                                    </div>
                                </div>
                                <p class="track_frm_btm">
                                    <a href="#" class="badge_success"><?php check(); ?>Submit</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="wr">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">Workshop Tracking</h5>
                            <div class="com_info_box_inner">
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Workshop Name</th>
                                                <th class="text-right">Track Workshop</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>TB & Critical Care Workshop</td>
                                                <td>
                                                    <div class="action_div"><a class="badge_secondary br-5 wrkshp_trak w-auto">Track Workshop <g><?php down() ?></g></a></div>
                                                </td>
                                            </tr>
                                            <tr class="sub_table_tr">
                                                <td colspan="2">
                                                    <div class="form_grid">
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                                            <input>
                                                        </div>
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">ID <i class="mandatory">*</i></p>
                                                            <input>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>TB & Critical Care Workshop</td>
                                                <td>
                                                    <div class="action_div"><a class="badge_secondary br-5 wrkshp_trak w-auto">Track Workshop <g><?php down() ?></g></a></div>
                                                </td>
                                            </tr>
                                            <tr class="sub_table_tr">
                                                <td colspan="2">
                                                    <div class="form_grid">
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                                            <input>
                                                        </div>
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">ID <i class="mandatory">*</i></p>
                                                            <input>
                                                        </div>
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
                <div class="com_info_box" id="br">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">Exhibitor Entry- 8-December-2025</h5>
                            <div class="com_info_box_inner">
                                <h5 class="tracking_sub_head"><span>Track Exhibitor Entry</span><a class="add">Total Track 0</a></h5>
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Registration ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">ID <i class="mandatory">*</i></p>
                                        <input>
                                    </div>

                                </div>
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
<script>
    $('.com_info_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box").removeClass("active");
        $(".com_info_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
    $('.com_info_box_content_sec_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box_content_sec_right_box").removeClass("active");
        $(".com_info_box_content_sec_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
</script>

</html>