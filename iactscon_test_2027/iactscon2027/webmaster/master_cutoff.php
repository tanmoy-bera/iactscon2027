<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Cutoffs</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">System Master</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cuttoffs</li>
                    </ol>
                </nav>
                <h2>Master Cutoff</h2>
                <h6>Manage master cutoffs, and dates.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Cutoffs Menu</h6>
                <button data-tab="ef" class="com_info_left_click icon_hover badge_primary active"><?php conregi() ?>Registration Cutoff</button>
                <button data-tab="fi" class="com_info_left_click icon_hover badge_secondary action-transparent"><?php workshop() ?>Workshop Cutoff</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="ef">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php conregi() ?></span> Registration Cutoff</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1"><?php add() ?>Add Cutoff</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Cutoff Title</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sl">1</td>
                                                <td>Early Bird</td>
                                                <td>19/02/2025</td>
                                                <td>30/05/2025</td>
                                                <td>
                                                    <div class="action_div">
                                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Conference Date</span><a class="add mi-1"><?php add() ?>Add Date</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Conference Dates</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sl">1</td>
                                                <td>2025-03-04</td>
                                                <td>
                                                    <div class="action_div">
                                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><i class="fal fa-trash-alt"></i></a>
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
                <div class="com_info_box" id="fi">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php workshop() ?></span> Workshop Cutoff</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Cutoff</span><a class="add mi-1"><?php add() ?>Add Cutoff</a></h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Cutoff Title</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sl">1</td>
                                                <td>Early Bird</td>
                                                <td>19/02/2025</td>
                                                <td>30/05/2025</td>
                                                <td>
                                                    <div class="action_div">
                                                        <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
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