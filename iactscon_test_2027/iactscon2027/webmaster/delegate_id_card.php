<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Print/Allocation</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Print/Allocation</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Delegate ID Card</li>
                    </ol>
                </nav>
                <h2>Delegate ID Card</h2>
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
                <a href="javascript:void(null)"><?php export(); ?>Export</a>
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
                                    <div class="regi_contact">
                                        <span>
                                            <?php call(); ?>9674833617
                                        </span>
                                        <span>
                                            <?php email(); ?>drasim53@gmail.com
                                        </span>
                                        <span>
                                            <?php conregi() ?>##SLIP210126-000008
                                        </span>
                                        <span>
                                            <?php qr() ?>#82680594
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <ul class="service_ul">
                                <li class="badge_padding badge_primary">
                                    <?php conregi(); ?><span>Conference Registration</span><n class="mi-1 text_success w-max-con text-uppercase">Paid</n>
                                </li>
                                <li class="badge_padding badge_danger">
                                    <?php dinner(); ?><span>Gala Dinner</span><n class="mi-1 text_danger w-max-con text-uppercase">Unaid</n>
                                </li>
                                <li class="badge_padding badge_info">
                                    <?php workshop(); ?><span>Imaging Workshop</span><n class="mi-1 text_danger w-max-con text-uppercase">Unaid</n>
                                </li>
                            </ul>
                        </td>
                        
                        <td class="action">
                            <div class="action_div">
                                <a href="javascript:void(null)" class=" icon_hover badge_primary action-transparent w-auto br-5"><?php printi(); ?>Print</a>
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