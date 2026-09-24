<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Directory & Stalls</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Exhibitor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Exhibitor Directory</li>
                    </ol>
                </nav>
                <h2>Exhibitor Directory</h2>
                <h6>Manage sponsors, assign stalls, and view company details.</h6>
            </div>
            <div class="page_top_wrap_right">
                <div class="tracking_analytic_tab">
                    <button data-tab="gridview" class="active"><i class="fal fa-th"></i></button>
                    <button data-tab="listview"><i class="fal fa-list"></i></button>

                </div>
            </div>
        </div>
        <ul class="regi_data_grid_ul mb-3">
            <li>
                <div>
                    <h5>Total Revenue</h5>
                    <h4 class="mb-0">₹ 45.2L</h4>
                </div>
                <span class="badge_primary"><?php exibitor(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Exhibitors</h5>
                    <h4 class="mb-0">24</h4>
                </div>
                <span class="badge_info"><?php rupee(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Stalls Booked</h5>
                    <h4 class="mb-0">18<n class="text-muted">/40</n>
                    </h4>
                </div>
                <span class="badge_success"><?php check(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Pending Payment</h5>
                    <h4 class="mb-0">₹ 12.5L</h4>
                </div>
                <span class="badge_secondary"><?php pending(); ?></span>
            </li>
        </ul>
        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="javascript:void(null)"><?php export(); ?>Export</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newexibitor"><?php add(); ?>Add Exhibitor</a>
            </div>
        </div>
        <div class="tracking_analytic_box active" id="gridview">
            <div class="stall_grid_wrap">
                <div class="stall_box">
                    <div class="d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">BIOMERIEUX LAB</div>
                            <div class="regi_contact mt-0">
                                <span>Debkishore Gupta</span>
                            </div>
                        </div>
                        <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php view(); ?></a>
                                <a href="javascript:void(null)" class="icon_hover badge_secondary action-transparent"><?php edit(); ?></a>
                                <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                            </ul>
                        </div>
                    </div>
                    <div class="regi_type mt-2">
                        <span class="badge_padding badge_primary">Platinum Sponsor</span>
                        <span class="badge_padding badge_dark">Stall: A1</span>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Total Amount</h5>
                            <h6>₹ 15.00L</h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Status</h5>
                            <span class="mi-1 pay_status badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                        </div>
                    </div>
                </div>
                <div class="stall_box">
                    <div class="d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">BIOMERIEUX LAB</div>
                            <div class="regi_contact mt-0">
                                <span>Debkishore Gupta</span>
                            </div>
                        </div>
                        <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php view(); ?></a>
                                <a href="javascript:void(null)" class="icon_hover badge_secondary action-transparent"><?php edit(); ?></a>
                                <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                            </ul>
                        </div>
                    </div>
                    <div class="regi_type mt-2">
                        <span class="badge_padding badge_secondary">Gold Sponsor</span>
                        <span class="badge_padding badge_dark">Stall: A1</span>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Total Amount</h5>
                            <h6>₹ 15.00L</h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Status</h5>
                            <span class="mi-1 pay_status badge_padding badge_secondary w-max-con text-uppercase"><?php pending(); ?>Partial</span>
                        </div>
                    </div>
                </div>
                <div class="stall_box">
                    <div class="d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name">BIOMERIEUX LAB</div>
                            <div class="regi_contact mt-0">
                                <span>Debkishore Gupta</span>
                            </div>
                        </div>
                        <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php view(); ?></a>
                                <a href="javascript:void(null)" class="icon_hover badge_secondary action-transparent"><?php edit(); ?></a>
                                <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                            </ul>
                        </div>
                    </div>
                    <div class="regi_type mt-2">
                        <span class="badge_padding badge_info">Silver Sponsor</span>
                        <span class="badge_padding badge_dark">Stall: A1</span>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Total Amount</h5>
                            <h6>₹ 15.00L</h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Status</h5>
                            <span class="mi-1 pay_status badge_padding badge_danger w-max-con text-uppercase"><?php Unpaid(); ?>Unpaid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tracking_analytic_box" id="listview">
            <div class="stall_list_wrap table_wrap">
                <table>
                    <thead>
                        <tr>
                            <th class="sl">#</th>
                            <th>Exibitor</th>
                            <th>Payment</th>
                            <th class="action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="sl">1</td>
                            <td>
                                <div class="stall_box bg-transparent p-0 border-0" style="border-radius: 0;">
                                    <div class="d-flex align-items-start">
                                        <div class="regi_img_circle">
                                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                                            <span>AM</span>
                                        </div>
                                        <div>
                                            <div class="regi_name">BIOMERIEUX LAB</div>
                                            <div class="regi_contact mt-0">
                                                <span>Debkishore Gupta</span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="regi_type mt-2">
                                        <span class="badge_padding badge_primary">Platinum Sponsor</span>
                                        <span class="badge_padding badge_dark">Stall: A1</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <h6 class="mb-1">₹ 4,000.00</h6>
                                </div>
                                <div class="mb-1"><span class="mi-1 badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?>Paid</span></div>
                            </td>
                            <td class="action">
                                <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                    <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php view(); ?></a>
                                        <a href="javascript:void(null)" class="icon_hover badge_secondary action-transparent"><?php edit(); ?></a>
                                        <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>