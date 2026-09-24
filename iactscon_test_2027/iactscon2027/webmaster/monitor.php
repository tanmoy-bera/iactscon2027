<?php include_once("includes/source.php"); ?>

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
                        <li class="breadcrumb-item"><a href="#">Spot</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Monitor</li>
                    </ol>
                </nav>
                <h2>Registration Live Monitor</h2>
                <h6>Real-time operational metrics for the registration desk.</h6>
            </div>
            <div class="page_top_wrap_right">
                <p class="badge_success live_sys"><?php circle(); ?>Live System</p>
            </div>
        </div>
        <div class="monitor_wrap">
            <div class="monitor_left">
                <ul class="regi_data_grid_ul accm_data_grid_ul mb-3">
                    <li>
                        <g class="bg-transparent p-0" style="border-radius: 0;"><span class="badge_success"><i class="fal fa-user-check"></i></span></g>
                        <div>
                            <h6 class="text-muted">Total Check-in</h6>
                            <h4>3 <n class="text-muted">/ 5</n>
                            </h4>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_success" data-done="70"></div>
                            </div>
                        </div>

                    </li>
                    <li>
                        <g class="bg-transparent p-0" style="border-radius: 0;"><span class="badge_info"><?php printi(); ?></span></g>
                        <div>
                            <h6 class="text-muted">Badges Printed</h6>
                            <h4>3 <n class="text-muted">/ 5</n>
                            </h4>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_info" data-done="70"></div>
                            </div>
                        </div>
                    </li>
                    <li>

                        <g class="bg-transparent p-0" style="border-radius: 0;"><span class="badge_secondary"><?php bag(); ?></span></g>
                        <div>
                            <h6 class="text-muted">Kits Handed Over</h6>
                            <h4>3 <n class="text-muted">/ 5</n>
                            </h4>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_secondary" data-done="70"></div>
                            </div>
                        </div>
                    </li>
                    <li>

                        <g class="bg-transparent p-0" style="border-radius: 0;"><span class="badge_primary"><?php rupee() ?></span></g>
                        <div>
                            <h6 class="text-muted">Revenue Collected</h6>
                            <h4>₹ 0.33L</h4>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress">
                                <div class="progress-done bg_primary" data-done="70"></div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="print_queue mb-3">
                    <div>
                        <span class="badge_danger"><?php pending() ?></span>
                        <p>Pending Print Queue<i>3 badges are stuck in queue at Desk 2.</i></p>
                    </div>
                    <a href="#">Resolve</a>
                </div>
                <!-- <div class="traffic_check">
                    <h5 class="monitor_right_head pl-0 pr-0 pt-0">Hourly Check-in Traffic</h5>
                </div> -->
            </div>
            <div class="monitor_right">
                <h5 class="monitor_right_head">Live Activity Feed<span>Real-time</span></h5>
                <ul>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_info"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                    <li>
                        <span class="badge_success"><?php printi() ?></span>
                        <p>Badge Printed for Dr. Asim Majumdar<b><?php clock() ?>10:42 AM • System</b></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>