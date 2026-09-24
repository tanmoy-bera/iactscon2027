<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Certificates</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Certificate Placement</li>
                    </ol>
                </nav>
                <h2>Certificate Placement</h2>
                <h6>Manage certificate generation queues and layout configurations.</h6>
            </div>
        </div>
        <ul class="regi_data_grid_ul mb-3">
            <li>
                <div class="w-100">
                    <h5>Participation Certs</h5>
                    <h4>1,248</h4>
                    <div class="progress-bar-wrap">
                        <div class="progress">
                            <div class="progress-done bg_success" data-progress="70"></div>
                        </div>
                    </div>
                </div>

            </li>
            <li>
                <div class="w-100">
                    <h5>Speaker Certs</h5>
                    <h4>45</h4>
                    <div class="progress-bar-wrap">
                        <div class="progress">
                            <div class="progress-done bg_primary" data-progress="40"></div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="w-100">
                    <h5>Workshop Certs</h5>
                    <h4>150</h4>
                    <div class="progress-bar-wrap">
                        <div class="progress">
                            <div class="progress-done bg_info" data-progress="10"></div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="w-100">
                    <h5>Pending Generation</h5>
                    <h4>320</h4>
                    <div class="progress-bar-wrap">
                        <div class="progress">
                            <div class="progress-done bg_secondary" data-progress="90"></div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>



        <div class="table_wrap">
            <table>
                <thead>
                    <tr>

                        <th>Batch Name</th>
                        <th>Type</th>
                        <th>Count</th>
                        <th>Status</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Delegate Batch #1</td>
                        <td>Participation</td>
                        <td>500</td>
                        <td>
                            <span class="mi-1 badge_padding badge_success w-max-con text-uppercase">Completed</span>
                        </td>
                        <td class="action">
                            <div class="action_div">
                                <a href="#" class="icon_hover badge_primary action-transparent w-auto br-5"><?php printi(); ?></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Delegate Batch #2</td>
                        <td>Participation</td>
                        <td>500</td>
                        <td>
                            <span class="mi-1 badge_padding badge_secondary w-max-con text-uppercase live_sys">Processing</span>
                        </td>
                        <td class="action">
                            <div class="action_div">
                                <a href="#" class="icon_hover badge_primary action-transparent w-auto br-5"><?php printi(); ?></a>
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