<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Operations & Settings</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Exibitor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Company Information</li>
                    </ol>
                </nav>
                <h2>Operations & Settings</h2>
                <h6>Configure exhibitor settings, manage finances and print badges.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Tools Menu</h6>
                <button data-tab="ef" class="com_info_left_click icon_hover badge_primary active"><?php suitcase(); ?>Partnership Types</button>
                <button data-tab="fi" class="com_info_left_click icon_hover badge_secondary action-transparent"><?php settings(); ?>Tariff Config</button>
                <button data-tab="md" class="com_info_left_click icon_hover badge_success action-transparent"><?php credit() ?>Payment History</button>
                <button data-tab="cc" class="com_info_left_click icon_hover badge_info action-transparent"><?php printi() ?>Card Printing</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="ef">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php suitcase(); ?></span> Partnership Types</n><a class="add mi-1"><?php add(); ?>Add Date</a>
                            </h5>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Partnership Type</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td>ACADEMIC PARTNER</td>
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
                <div class="com_info_box" id="fi">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php settings() ?></span> Tariff Config</n>
                            </h5>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Company</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td>BIOMERIEUX LAB <span class="text-muted">[EXB09]</span></td>
                                            <td class="action">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="md">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php credit() ?></span> Payment History</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <h6 class="accm_add_empty">No Payment record</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="cc">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php printi() ?></span> Printing</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <form>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Select Company</p>
                                            <select>
                                                <option>1</option>
                                            </select>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Representative Name</p>
                                            <input>
                                        </div>
                                    </div>
                                    <p class="track_frm_btm">
                                        <a href="#" class="badge_success"><?php printi(); ?>Search & Print</a>
                                    </p>
                                </form>
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