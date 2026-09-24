<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include_once("includes/source.php"); 
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php');
$cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/section_registration/webmaster/";
?>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>General Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registration List</li>
                    </ol>
                </nav>
                <h2>Participant Overview</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>
        <ul class="regi_data_grid_ul mb-3">
            <li>
                <div>
                    <h5>Total Registrants</h5>
                    <h4>1,248</h4>

                    <h6 class="text_success">+12% from last week</h6>
                </div>
                <span class="badge_primary"><?php duser(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Revenue Collected</h5>
                    <h4>₹ 42.5L</h4>
                    <h6 class="text_success">+8% from last week</h6>
                </div>
                <span class="badge_success"><?php rupee() ?></span>
            </li>
            <li>
                <div>
                    <h5>Pending Payments</h5>
                    <h4>45</h4>
                    <h6 class="text_danger">-2% from last week</h6>
                </div>
                <span class="badge_secondary"><?php pending(); ?></span>
            </li>
            <li>
                <div>
                    <h5>Early Birds</h5>
                    <h4>850</h4>
                    <h6 class="text_success">+2% from last week</h6>
                </div>
                <span class="badge_info"><?php windowi(); ?></span>
            </li>
        </ul>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="javascript:void(null)"><?php export(); ?>Export</a>
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

        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl">#</th>
                        <th>Participant</th>
                        <th>Services</th>
                        <th>Payment</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="sl">
                            <button class="service_detail_btn"><?php down(); ?></button>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
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
                        </td>
                        <td>
                            <ul class="service_ul">
                                <li class="badge_padding badge_primary">
                                    <?php conregi(); ?><span>Conference Registration</span>
                                </li>
                                <li class="badge_padding badge_danger">
                                    <?php dinner(); ?><span>Gala Dinner</span></span>
                                </li>
                                <li class="badge_padding badge_info">
                                    <?php workshop(); ?><span>Imaging Workshop</span>
                                </li>
                                <li class="badge_padding badge_secondary">
                                    <?php duser(); ?><span>Accompanying Person</span>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-1">₹ 4,000.00</h6>
                            </div>
                            <div class="mb-1"><span class="mi-1 badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?>Paid</span></div>
                            <div><small class="text-muted">UPI TXN ID:00000001</small></div>
                        </td>
                        <td class="action">
                            <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="javascript:void(null)" data-tab="profile" class="popup-btn icon_hover badge_primary action-transparent"><?php view(); ?>View Profile</a>
                                    <a href="javascript:void(null)" data-tab="editregistartion" class="popup-btn icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                    <a href="#" class="icon_hover badge_info action-transparent"><?php invoive(); ?>Invoice & Mail</a>
                                    <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?>Delete</a>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr class="sub_table_tr">
                        <td colspan="5">
                            <div class="sub_table_div">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Ref ID</th>
                                            <th>Mode</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Conference Registration</td>
                                            <td>NATCON 2025-000325</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td class="text-right">₹ 6,000</td>
                                        </tr>
                                        <tr>
                                            <td>Workshop Registration</td>
                                            <td>NATCON 2025-000326</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td class="text-right">₹ 4,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gala Dinner</td>
                                            <td>NATCON 2025-000327</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td class="text-right">₹ 4,000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="sl">
                            <button class="service_detail_btn"><?php down(); ?></button>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
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
                        </td>
                        <td>
                            <ul class="service_ul">
                                <li class="badge_padding badge_primary">
                                    <?php conregi(); ?><span>Conference Registration</span>
                                </li>
                                <li class="badge_padding badge_danger">
                                    <?php dinner(); ?><span>Gala Dinner</span></span>
                                </li>
                                <li class="badge_padding badge_info">
                                    <?php workshop(); ?><span>Imaging Workshop</span>
                                </li>
                                <li class="badge_padding badge_secondary">
                                    <?php duser(); ?><span>Accompanying Person</span>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-1">₹ 4,000.00</h6>
                            </div>
                            <div class="mb-1"><span class="mi-1 badge_padding badge_danger w-max-con text-uppercase"><?php unpaid(); ?>Unpaid</span></div>
                            <div><small class="text-muted">UPI TXN ID:00000001</small></div>
                        </td>
                        <td class="action text-right">
                            <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="javascript:void(null)" data-tab="profile" class="popup-btn icon_hover badge_primary action-transparent"><?php view(); ?>View Profile</a>
                                    <a href="javascript:void(null)" data-tab="editregistartion" class="popup-btn icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                    <a href="#" class="icon_hover badge_info action-transparent"><?php invoive(); ?>Invoice & Mail</a>
                                    <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?>Delete</a>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr class="sub_table_tr">
                        <td colspan="5">
                            <div class="sub_table_div">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Ref ID</th>
                                            <th>Mode</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Conference Registration</td>
                                            <td>NATCON 2025-000325</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td>₹ 6,000</td>
                                        </tr>
                                        <tr>
                                            <td>Workshop Registration</td>
                                            <td>NATCON 2025-000326</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td>₹ 4,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gala Dinner</td>
                                            <td>NATCON 2025-000327</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td>₹ 4,000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="sl">
                            <button class="service_detail_btn"><?php down(); ?></button>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
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
                        </td>
                        <td>
                            <ul class="service_ul">
                                <li class="badge_padding badge_primary">
                                    <?php conregi(); ?><span>Conference Registration</span>
                                </li>
                                <li class="badge_padding badge_danger">
                                    <?php dinner(); ?><span>Gala Dinner</span></span>
                                </li>
                                <li class="badge_padding badge_info">
                                    <?php workshop(); ?><span>Imaging Workshop</span>
                                </li>
                                <li class="badge_padding badge_secondary">
                                    <?php duser(); ?><span>Accompanying Person</span>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-1">₹ 4,000.00</h6>
                            </div>
                            <div class="mb-1"><span class="mi-1 badge_padding badge_secondary w-max-con text-uppercase"><?php pending(); ?>Partial</span></div>
                            <div><small class="text-muted">UPI TXN ID:00000001</small></div>
                        </td>
                        <td class="action text-right">
                            <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="javascript:void(null)" data-tab="profile" class="popup-btn icon_hover badge_primary action-transparent"><?php view(); ?>View Profile</a>
                                    <a href="javascript:void(null)" data-tab="editregistartion" class="popup-btn icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                    <a href="#" class="icon_hover badge_info action-transparent"><?php invoive(); ?>Invoice & Mail</a>
                                    <a href="#" class="icon_hover badge_danger action-transparent"><?php delete(); ?>Delete</a>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr class="sub_table_tr">
                        <td colspan="5">
                            <div class="sub_table_div">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Ref ID</th>
                                            <th>Mode</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Conference Registration</td>
                                            <td>NATCON 2025-000325</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td>₹ 6,000</td>
                                        </tr>
                                        <tr>
                                            <td>Workshop Registration</td>
                                            <td>NATCON 2025-000326</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td>₹ 4,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gala Dinner</td>
                                            <td>NATCON 2025-000327</td>
                                            <td>OFFLINE (FRONT)</td>
                                            <td>₹ 4,000</td>
                                        </tr>
                                    </tbody>
                                </table>
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