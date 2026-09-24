<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Mail Content</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Mail Content</li>
                    </ol>
                </nav>
                <h2>Manage Mail Content</h2>
                <h6>Manage mail icons, images, and status.</h6>
            </div>
        </div>
        <div class="com_info_box_grid">
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Entitlements Setting</span>
                </h5>
                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="sl">#</th>
                                <th>Text</th>
                                <th>Icon</th>
                                <th class="action">Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="sl">1</td>
                                <td>Entry to Scientific Halls</td>
                                <td>
                                    <img class="header_img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_ICON_0028_240523181516.png">
                                </td>
                                <td>
                                    <div class="action_div">
                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                    </div>
                                </td>
                                <td class="action">
                                    <div class="action_div">
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Invoice Icon Setting</span>
                </h5>
                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="sl">#</th>
                                <th>Invoice Service Type</th>
                                <th>Icon</th>
                                <th class="action">Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="sl">1</td>
                                <td>Entry to Scientific Halls</td>
                                <td>
                                    <img class="header_img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_ICON_0028_240523181516.png">
                                </td>
                                <td>
                                    <div class="action_div">
                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                    </div>
                                </td>
                                <td class="action">
                                    <div class="action_div">
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Mailer Image Setting</span>
                </h5>
                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="sl">#</th>
                                <th>Image</th>
                                <th>Purpose</th>
                                <th class="action">Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="sl">1</td>
                                <td>
                                    <img class="header_img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_ICON_0028_240523181516.png">
                                </td>
                                <td>Entry to Scientific Halls</td>
                                <td>
                                    <div class="action_div">
                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                    </div>
                                </td>
                                <td class="action">
                                    <div class="action_div">
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>


</html>