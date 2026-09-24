<?php
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
// include_once("includes/header.php"); 
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css'>
<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />


<!-- <link rel="stylesheet" type="text/css" href="bootstrap-5/css/bootstrap.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="css/settings.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="css/navigation.css"> -->
<!-- <link rel='stylesheet' href='css/revolution.addon.beforeafter.css' type='text/css' media='all' /> -->
<!-- <link rel="stylesheet" type="text/css" href="css/style.css?v=4"> -->
<!-- <link rel="stylesheet" type="text/css" href="css/kolkata.css?v=4"> -->
<!-- <link rel="stylesheet" type="text/css" href="css/responcive.css?v=5"> -->
<!-- <link href="css/owl.carousel.min.css?v=2" rel="stylesheet" type="text/css"> -->
<!-- <link href="css/owl.theme.default.min.css?v=2" rel="stylesheet" type="text/css"> -->
<!-- <link href="css/odometer.css?v=2" rel="stylesheet" type="text/css"> -->


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rosarivo:ital@0;1&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= _BASE_URL_ ?>css/website/animate.min.css">
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/parvus@2.3.3/dist/css/parvus.min.css'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<link rel='stylesheet' href="<?= _BASE_URL_ ?>css/website/program-fonts/stylesheet.css">
<script src="<?= _BASE_URL_ ?>js/website/jquery.min.js"></script>
<script src="<?= _BASE_URL_ ?>js/website/owl.carousel.js"></script>


<link rel='stylesheet' href='<?= _BASE_URL_ ?>css/website/program-style.css' type='text/css' media='all' />

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-C5YRPYHPQ0"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-C5YRPYHPQ0');
</script>




<header>
    <div class="container">
        <div class="row">
            <div class="header_wrap" style="padding:15px;">
                <div class="header_inner">
                    <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . "/" . $cfg['MAILER.LOGO'] ?>" alt="" class="logo">
                    <div class="or_wrap">
                        <p>Organized by</p>
                        <img src="<?= _BASE_URL_ ?>images/rueda.png" alt="">
                    </div>
                </div>
                <?php
                $startDate = new DateTime($cfg['CONF_START_DATE']);
                $endDate = new DateTime($cfg['CONF_END_DATE']);


                // Same month
                $date = $startDate->format('F j') . '-' . $endDate->format('jS') . ', ' . $endDate->format('Y');

                ?>
                <div class="con_inner">
                    <h2 style="margin-top: 0px;"><?= $cfg['EMAIL_CONF_NAME']     ?></h2>
                    <h4 style="margin-top: 0px;"><?= $cfg['FULL_CONF_NAME'] ?></h4>
                    <h5 style="margin-top: 0px;"><?= $date ?> at <?= $cfg['EMAIL_CONF_VENUE'] ?>, Kolkata, India.</h5>
                </div>
            </div>
        </div>
    </div>
</header>

<body>
    <?php
    $show = $_REQUEST['show'];
    switch ($show) {

        // ADD SESSION FORM WINDOW
        case 'participants':
            participantListingDisplayWindow($cfg, $mycms);
            break;
        default:
            showSession($cfg, $mycms);
            break;
    }
    function showSession($cfg, $mycms)
    {
    ?>

        <section class="container-fluid py-6" style="padding-top: 30px;">
            <div class="container">
                <div class="row">
                    <div class="tuto_schedule_box">
                        <?php
                        $sqlDateisting = array();
                        $sqlDateisting['QUERY']        = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " WHERE `status` = ?";
                        $sqlDateisting['PARAM'][]      = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                        $resultDateListing           = $mycms->sql_select($sqlDateisting);
                        $Counter                     = 0;
                        if ($resultDateListing) {
                        ?>
                            <div class="schedule-tab-btn">
                                <a class="fac_btn" href="<?= _BASE_URL_ ?>program.php?show=participants">Faculty List</a>
                                <?php
                                foreach ($resultDateListing as $keyDateListing => $value) {
                                    $Counter++;
                                ?>
                                    <a href="javascript:void(0);" data-tab="day-<?= $value['id'] ?>" class="<?= $keyDateListing == 0 ? 'active' : '' ?>">
                                        <label for="date-check-<?= $value['id'] ?>">
                                            <span class="day">Day <?= $Counter ?></span>
                                            <span class="date_con">
                                                <span class="date">
                                                    <?= date('d', strtotime($value['conf_date'])) ?>
                                                </span>
                                                <span class="year">
                                                    <?= strtoupper(date('M', strtotime($value['conf_date']))) ?><br><?= date('Y', strtotime($value['conf_date'])) ?>
                                                </span>
                                            </span>
                                            <input type="radio" name="date-check" id="date-check-<?= $value['id'] ?>" value="<?= $value['id'] ?>" <?= $Counter == 1 ? 'checked' : '' ?> style="visibility:hidden">
                                        </label>
                                    </a>
                                <?
                                }
                                ?>

                            </div>
                        <?php
                        }
                        ?>

                        <div class="schedule-tabContainer">
                            <div style="display:flex;">
                                <div class="search-box">
                                    <input type="text" id="searchInput" style="padding: 25px;color: #ffffff;" placeholder="Search by Session Name">
                                    <a><i class="fas fa-search"></i></a>
                                </div>&nbsp;
                                <div class="search-box">
                                    <input type="text" id="searchSessionByFacultyName" style="padding: 25px;color: #ffffff;" placeholder="Search by Faculty Name">
                                    <a><i class="fas fa-search"></i></a>
                                </div>
                            </div>


                            <div id="data-body">

                                <!-- ajax -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            $(document).ready(function() {

                var dateId = $('input[name="date-check"]:checked').val();

                $.ajax({
                    type: "POST",
                    url: "program.process.php",
                    data: 'act=show_data&dateId=' + dateId,
                    dataType: "html",
                    async: true,
                    success: function(HTMLObject) {

                        $("#data-body").html(HTMLObject);
                    }
                });

                $('input[name="date-check"]').on('change', function() {
                    var dateId = $(this).val();
                    var hallId = $('select[name="hall-check"]').val();
                    // alert(hallId)

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_data&dateId=' + dateId + '&hallId=0',
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            // $("#data-body").html(HTMLObject);
                            $("#searchInput").val('');
                            $("#searchSessionByFacultyName").val('');

                            $("#data-body").fadeOut(100, function() { // First fade out any existing content
                                $("#data-body").html(HTMLObject); // Update with new content
                                $("#data-body").fadeIn(400);
                            });
                        }
                    });
                });

                $(document).on('change', 'select[name="hall-check"]', function() {
                    var dateId = $('input[name="date-check"]:checked').val();
                    // alert(dateId)
                    var hallId = $(this).val();

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_data&dateId=' + dateId + '&hallId=' + hallId,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            $("#data-body").html(HTMLObject);
                        }
                    });
                });

                $(document).on('keyup', '#searchInput', function() {
                    var searchInput = $(this).val().toLowerCase();
                    var dateId = $('input[name="date-check"]:checked').val();

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_data&dateId=' + dateId + '&hallId=0&searchInput=' + searchInput,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            // $("#data-body").html(HTMLObject);
                            $("#searchSessionByFacultyName").val('');
                            $("#data-body").fadeOut(100, function() { // First fade out any existing content
                                $("#data-body").html(HTMLObject); // Update with new content
                                $("#data-body").fadeIn(500);
                            });
                        }
                    });
                });
                $(document).on('keyup', '#searchSessionByFacultyName', function() {
                    var searchSessionByFacultyName = $(this).val().toLowerCase();
                    var dateId = $('input[name="date-check"]:checked').val();

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_data&dateId=' + dateId + '&hallId=0&searchSessionByFacultyName=' + searchSessionByFacultyName,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            // $("#data-body").html(HTMLObject);
                            $("#searchInput").val('');
                            $("#data-body").fadeOut(100, function() { // First fade out any existing content
                                $("#data-body").html(HTMLObject); // Update with new content
                                $("#data-body").fadeIn(500);
                            });
                        }
                    });
                });

                // ============ MODAL JS =================================
                $(document).on('click', '.spe-name', function() {
                    var id = $(this).attr('participant-id');
                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_participant_data&id=' + id,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            $("#participant_schedule_modal").html(HTMLObject);
                            $('.pop_up_wrap').show();
                        }
                    });
                    // alert(id)

                });

                $(document).on('click', '.date_tab a', function() {
                    var tabId = $(this).attr('data-tab');

                    $('.date_tab a').removeClass('active');
                    $('.pop_body ul').removeClass('active').hide();

                    $(this).addClass('active');
                    $('.' + tabId).addClass('active').show();
                });
                $(document).on('click', '.pop_cls', function() {
                    $('.pop_up_wrap').hide();
                });


            });

            $(function() {
                $('.schedule-tab-btn a').click(function() {
                    var tabId = $(this).attr('data-tab');

                    $('.schedule-tab-btn a').removeClass('active');
                    $('.schedule-tabcondent').removeClass('active').hide();

                    $(this).addClass('active');
                    $('#' + tabId).addClass('active').show();
                });
            });
            $(function() {
                $('.hall-btn a').click(function() {
                    var tabId = $(this).attr('data-tab');
                    $(this).parent().parent().find('.hall-btn a').removeClass('active');
                    $(this).parent().parent().find('.schedule_card_wrap').removeClass('active');
                    $(this).addClass('active');
                    $(this).parent().parent().find('.' + tabId).addClass('active');
                });
            });
            $(function() {
                $('.date_tab a').click(function() {
                    var tabId = $(this).attr('data-tab');

                    $('.date_tab a').removeClass('active');
                    $('.pop_body ul').removeClass('active').hide();

                    $(this).addClass('active');
                    $('.' + tabId).addClass('active').show();
                });
            });
            $(function() {
                $('.spe-name').click(function() {
                    $('.pop_up_wrap').show();
                });
            });
            $(function() {
                $('.pop_cls').click(function() {
                    $('.pop_up_wrap').hide();
                });
            });
        </script>
    <?php }
    function participantListingDisplayWindow($cfg, $mycms)
    {
    ?>
        <section class="container-fluid py-6" style="padding-top: 30px;">
            <div class="container">
                <div class="row">
                    <div class="tuto_schedule_box">
                        <?php
                        $sqlDateisting = array();
                        $sqlDateisting['QUERY']        = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " WHERE `status` = ?";
                        $sqlDateisting['PARAM'][]      = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                        $resultDateListing           = $mycms->sql_select($sqlDateisting);
                        $Counter                     = 0;
                        if (!$resultDateListing) {
                        ?>
                            <div class="schedule-tab-btn">
                                <?php
                                foreach ($resultDateListing as $keyDateListing => $value) {
                                    $Counter++;
                                ?>
                                    <a href="javascript:void(0);" data-tab="day-<?= $value['id'] ?>" class="<?= $keyDateListing == 0 ? 'active' : '' ?>">
                                        <label for="date-check-<?= $value['id'] ?>">
                                            <span class="day">Day <?= $Counter ?></span>
                                            <span class="date_con">
                                                <span class="date">
                                                    <?= date('d', strtotime($value['conf_date'])) ?>
                                                </span>
                                                <span class="year">
                                                    <?= strtoupper(date('M', strtotime($value['conf_date']))) ?><br><?= date('Y', strtotime($value['conf_date'])) ?>
                                                </span>
                                            </span>
                                            <input type="radio" name="date-check" id="date-check-<?= $value['id'] ?>" value="<?= $value['id'] ?>" <?= $Counter == 1 ? 'checked' : '' ?> style="visibility:hidden">
                                        </label>
                                    </a>
                                <?
                                }
                                ?>

                            </div>
                        <?php
                        }
                        ?>
                        <div class="schedule-tab-btn">
                            <a href="<?= _BASE_URL_ ?>program.php" style="padding: 18px;color: white;font-size: larger;width: 90%;box-shadow: 5px 3px 13px #0a0808a1;">Session List</a>
                        </div>
                        <div class="schedule-tabContainer">
                            <div class="search-box" style="margin-top: unset;margin-bottom: unset;">
                                <input type="text" id="searchParticipant" style="padding: 25px;color: #ffffff;" placeholder="Search by Participant Name or Email">
                                <a><i class="fas fa-search"></i></a>

                            </div>
                            <div id="participant-body">

                                <!-- ajax -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            $(document).ready(function() {

                var dateId = $('input[name="date-check"]:checked').val();

                $.ajax({
                    type: "POST",
                    url: "program.process.php",
                    data: 'act=show_participant_list',
                    dataType: "html",
                    async: true,
                    success: function(HTMLObject) {

                        $("#participant-body").html(HTMLObject);
                    }
                });

                $('input[name="date-check"]').on('change', function() {
                    var dateId = $(this).val();
                    var hallId = $('select[name="hall-check"]').val();
                    // alert(hallId)

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_data&dateId=' + dateId + '&hallId=0',
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            // $("#data-body").html(HTMLObject);

                            $("#data-body").fadeOut(100, function() { // First fade out any existing content
                                $("#data-body").html(HTMLObject); // Update with new content
                                $("#data-body").fadeIn(500);
                            });
                        }
                    });
                });

                $(document).on('change', 'select[name="hall-check"]', function() {
                    var dateId = $('input[name="date-check"]:checked').val();
                    // alert(dateId)
                    var hallId = $(this).val();

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_data&dateId=' + dateId + '&hallId=' + hallId,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            $("#data-body").html(HTMLObject);
                        }
                    });
                });

                $(document).on('keyup', '#searchParticipant', function() {
                    var searchInput = $(this).val().toLowerCase();
                    //var dateId = $('input[name="date-check"]:checked').val();

                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_participant_list&searchInput=' + searchInput,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            // $("#data-body").html(HTMLObject);
                            $("#participant-body").html(HTMLObject);

                        }
                    });
                });

                // ============ MODAL JS =================================
                $(document).on('click', '.spe-name', function() {
                    var id = $(this).attr('participant-id');
                    $.ajax({
                        type: "POST",
                        url: "program.process.php",
                        data: 'act=show_participant_data&id=' + id,
                        dataType: "html",
                        async: true,
                        success: function(HTMLObject) {
                            $("#participant_schedule_modal").html(HTMLObject);
                            $('.pop_up_wrap').show();
                        }
                    });
                    // alert(id)

                });

                $(document).on('click', '.date_tab a', function() {
                    var tabId = $(this).attr('data-tab');

                    $('.date_tab a').removeClass('active');
                    $('.pop_body ul').removeClass('active').hide();

                    $(this).addClass('active');
                    $('.' + tabId).addClass('active').show();
                });
                $(document).on('click', '.pop_cls', function() {
                    $('.pop_up_wrap').hide();
                });


            });

            $(function() {
                $('.schedule-tab-btn a').click(function() {
                    var tabId = $(this).attr('data-tab');

                    $('.schedule-tab-btn a').removeClass('active');
                    $('.schedule-tabcondent').removeClass('active').hide();

                    $(this).addClass('active');
                    $('#' + tabId).addClass('active').show();
                });
            });
            $(function() {
                $('.hall-btn a').click(function() {
                    var tabId = $(this).attr('data-tab');
                    $(this).parent().parent().find('.hall-btn a').removeClass('active');
                    $(this).parent().parent().find('.schedule_card_wrap').removeClass('active');
                    $(this).addClass('active');
                    $(this).parent().parent().find('.' + tabId).addClass('active');
                });
            });
            $(function() {
                $('.date_tab a').click(function() {
                    var tabId = $(this).attr('data-tab');

                    $('.date_tab a').removeClass('active');
                    $('.pop_body ul').removeClass('active').hide();

                    $(this).addClass('active');
                    $('.' + tabId).addClass('active').show();
                });
            });
            $(function() {
                $('.spe-name').click(function() {
                    $('.pop_up_wrap').show();
                });
            });
            $(function() {
                $('.pop_cls').click(function() {
                    $('.pop_up_wrap').hide();
                });
            });
        </script>

    <?php }
    include_once("includes/footer.php"); ?>
</body>
<div class="pop_up_wrap" id="participant_schedule_modal">

</div>


<script src='https://unpkg.com/gsap@3/dist/gsap.min.js'></script>
<script src='https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js'></script>
<script src='https://unpkg.com/@studio-freight/lenis@1.0.34/dist/lenis.min.js'></script>
<script src='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/three.js/r83/three.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.4/TweenMax.min.js'></script>
<script src='https://assets.codepen.io/16327/Flip.min.js'></script>
<script src='https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/parvus@2.3.3/dist/js/parvus.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

    });
</script>


</html>