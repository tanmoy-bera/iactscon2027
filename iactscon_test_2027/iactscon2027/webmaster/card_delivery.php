<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Card Delivery</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Card Delivery</li>
                    </ol>
                </nav>
                <h2>Guest Card Delivery Tracking</h2>
                <h6>Track the delivery status of guest ID cards and welcome kits.</h6>
            </div>
        </div>
        <ul class="regi_data_grid_ul mb-3">
            <li>
                <div>
                    <h5>Total Guests</h5>
                    <h4 class="mb-0">128</h4>
                </div>
                <span class="badge_primary"><?php duser(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Total Delivered</h5>
                    <h4 class="mb-0">28</h4>
                </div>
                <span class="badge_success"><?php check() ?></span>
            </li>
            <li>
                <div>
                    <h5>Total Pending</h5>
                    <h4 class="mb-0">100</h4>
                </div>
                <span class="badge_secondary"><?php pending(); ?></span>
            </li>
        </ul>
        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search by Name, or Guest ID...">
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

        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl">#</th>
                        <th>Guest ID</th>
                        <th>Guest Details</th>
                        <th>Card Status</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="sl">1</td>
                        <td>G-101</td>
                        <td>
                             <div class="d-flex align-items-start">
                                <div>
                                    <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                                    <div class="regi_type">
                                        <span class="badge_padding badge_dark">VIP GUEST</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="mi-1 badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?>Delivered</span>
                        </td>
                        <td class="action">
                            <div class="action_div">
                               <a href="#" class="icon_hover badge_secondary action-transparent w-auto br-5" title="Mark Delivery"><i class="fal fa-paper-plane"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="sl">2</td>
                        <td>G-102</td>
                        <td>
                             <div class="d-flex align-items-start">
                                <div>
                                    <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                                    <div class="regi_type">
                                        <span class="badge_padding badge_dark">SPEAKER GUEST</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="mi-1 badge_padding badge_secondary w-max-con text-uppercase"><?php Pending(); ?>Pending</span>
                        </td>
                        <td class="action">
                            <div class="action_div">
                               <a href="#" class="icon_hover badge_secondary action-transparent w-auto br-5" title="Mark Delivery"><i class="fal fa-paper-plane"></i></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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