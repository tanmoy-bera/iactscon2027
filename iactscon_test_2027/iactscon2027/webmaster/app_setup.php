<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Abstract Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="app_setup_top">
            <div class="app_setup_top_left">
                <h5><span class="badge_default"><i class="fal fa-cog"></i></span>Conference App Setup Portal</h5>
                <h3>IACTSCON 2026 Setup</h3>
                <p>80th National Conference of Pulmonary Diseases & Respiratory Medicine • Dedicated configuration
                    dashboard for dates, venues, committee, faculty, badge templates & features.</p>
            </div>
            <div class="app_setup_top_right">
                <h5>
                    <span></span>
                    <n>
                        <l>IACTSCON 2026<k class="badge_success">Live</k>
                        </l>
                        <g>Biswa Bangla Convention Centre, Kolkata</g>
                    </n>
                </h5>
                <a class="badge_success"><?php save() ?>Save All Setup</a>
            </div>
        </div>

        <div class="app_tab_btn owl-carousel owl-theme">
            <li class="active" data-tab="overviewstatus"><i class="fal fa-layer-group"></i>Overview &
                Status</li>
            <li data-tab="basic_details"><i class="fal fa-building"></i>Basic Details</li>
            <li data-tab="venue_setup"><i class="fal fa-map-marker-alt"></i>Venue Setup</li>
            <li data-tab="committee"><i class="fal fa-user-friends"></i>Committee</li>
            <li data-tab="faculty_master"><i class="fal fa-award"></i>Faculty Master</li>
            <li data-tab="quizpolls"><i class="fal fa-question-circle"></i>Quiz & Polls</li>
            <li data-tab="push_notification"><i class="fal fa-bell"></i>Push Notifications</li>
            <li data-tab="idcardstudio"><i class="fal fa-portrait"></i>ID Card Studio</li>
            <li data-tab="venueguide"><i class="fal fa-compass"></i>Venue Guide</li>
            <li data-tab="imagelibrary"><i class="fal fa-image"></i>Image Library</li>
            <li data-tab="brandtheme"><i class="fal fa-palette"></i>Brand Theme</li>
            <li data-tab="featuremodule"><i class="fal fa-sliders-v"></i>Feature Modules</li>
            <li data-tab="exexcutivestats"><i class="fal fa-signal-4"></i>Executive Stats</li>
        </div>

        <div class="app_tab_content" id="overviewstatus" style="display: block;">
            <div class="overvire_top">
                <div class="overvire_top_img"></div>
                <div class="overvire_top_mid">
                    <h6>
                        <l class="badge_success">Active Live Setup</l>
                        <n class="badge_secondary">Edition 2026</n>
                    </h6>
                    <h2>IACTSCON 2026</h2>
                    <p>80th National Conference of Pulmonary Diseases & Respiratory Medicine</p>
                    <f>"Advancing Precision Pulmonology & Global Thoracic Care"</f>
                </div>
                <a class="badge_secondary"><?php edit() ?>Edit Basic Setup</a>
            </div>

            <div class="overview_grid">
                <div class="overview_grid_box">
                    <n>Dates & Schedule<?php calendar() ?></n>
                    <h2>2026-12-17</h2>
                    <l>End: 2026-12-20 (Asia/Kolkata (GMT+05:30))</l>
                </div>
                <div class="overview_grid_box">
                    <n>Primary Venue<?php address() ?></n>
                    <h3>Biswa Bangla Convention Centre, Kolkata</h3>
                    <l class="text_success">Halls & Map Configured</l>
                </div>
                <div class="overview_grid_box">
                    <n>Secretariat Desk<?php email() ?></n>
                    <h3>secretariat@IACTSCON2026.org</h3>
                    <l>+91 98300 12345</l>
                </div>
                <div class="overview_grid_box">
                    <n>Expected Delegates<?php duser() ?></n>
                    <h2>3,450</h2>
                    <l class="text_success">Live Gateway Active</l>
                </div>
            </div>

            <div class="app_stup_index">
                <h5 class="app_heading"><i class="fal fa-shield-check"></i>IACTSCON 2026 Setup Readiness Index</h5>
                <ul>
                    <li>
                        <span><i class="fal fa-check-circle"></i></span>
                        <p>
                            <n>Conference Basic Info</n>
                            <g>Title, dates, currency & GST configured</g>
                        </p>
                    </li>
                    <li>
                        <span><i class="fal fa-check-circle"></i></span>
                        <p>
                            <n>Conference Basic Info</n>
                            <g>Title, dates, currency & GST configured</g>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="app_tab_content" id="basic_details">
            <div class="basic_grid">
                <div class="basic_grid_left">
                    <div class="basic_grid_box">
                        <h5 class="app_heading"><i class="fal fa-building"></i>Conference Identity & Info
                        </h5>
                        <div class="form_grid">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Conference Short Name</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Edition Year</p>
                                <input>
                            </div>
                            <div class="frm_grp span_4">
                                <p class="frm-head">Full Official Name</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Tagline</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Academic Theme</p>
                                <input>
                            </div>
                        </div>
                    </div>
                    <div class="basic_grid_box">
                        <h5 class="app_heading"><i class="fal fa-globe"></i>Web & Communication Desks
                        </h5>
                        <div class="form_grid">
                            <div class="frm_grp span_4">
                                <p class="frm-head">Website URL</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Secretariat Email</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Helpline Number</p>
                                <input>
                            </div>
                        </div>
                    </div>
                    <div class="basic_grid_box">
                        <h5 class="app_heading"><i class="fal fa-calendar"></i>Dates, Timezone & Taxes
                        </h5>
                        <div class="form_grid">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Registration Open Date</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Registration Closing Date</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Conference Start Date</p>
                                <input>
                            </div>

                            <div class="frm_grp span_2">
                                <p class="frm-head">Conference End Date</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Time Zone</p>
                                <input>
                            </div>
                            <div class="frm_grp span_1">
                                <p class="frm-head">Currency</p>
                                <input>
                            </div>
                            <div class="frm_grp span_1">
                                <p class="frm-head">GST Tax Rate</p>
                                <input>
                            </div>
                        </div>
                    </div>
                    <div class="basic_grid_box">
                        <h5 class="app_heading"><i class="fal fa-upload"></i>Brand Assets & Image URLs
                        </h5>
                        <div class="form_grid">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Primary Logo URL</p>
                                <input>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Favicon Image URL</p>
                                <input>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="basic_grid_right">
                    <div class="real_time_portal">
                        <h5 class="real_time_portal_heading">
                            <n><i class="fal fa-eye"></i>Real-Time Portal Preview</n>
                            <l class="badge_success">Live Gateway Sync</l>
                        </h5>

                        <div class="real_time_portal_body">
                            <div class="real_time_portal_body_head">
                                <span></span>
                                <h5>
                                    <n>2026 OFFICIAL DELEGATE PORTAL</n>
                                    <l>IACTSCON 2026</l>
                                    <g>80th National Conference of</g>
                                </h5>
                            </div>
                            <div class="real_time_portal_body_mid">"Advancing Precision Pulmonology & Global Thoracic
                                Care"
                            </div>
                            <ul class="real_time_portal_body_bottom">
                                <li>
                                    <n>Conference Dates:</n>
                                    <l>2026-12-17 - 2026-12-20</l>
                                </li>
                                <li>
                                    <n>Registration Closes:</n>
                                    <l>2026-11-30</l>
                                </li>
                                <li>
                                    <n>Tax Structure:</n>
                                    <l>18% GST Included</l>
                                </li>
                                <li>
                                    <n>Base Currency:</n>
                                    <l>INR (₹)</l>
                                </li>
                            </ul>
                            <a class="badge_default">Register for IACTSCON 2026<i class="fal fa-sign-in"></i></a>
                        </div>
                        <h6>Changes made in this form update the public landing page & invoices in real time.</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="venue_setup">

            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-map-marker-alt"></i>Conference Venues & Hall Locations</n>
                    <l>Configure primary convention grounds, banquet halls, floor counts & nearby transport links.</l>
                </h5>
                <a class="badge_default popup-btn" data-tab="appaddvenue" ><?php add() ?>Add Venue</a>
            </div>
            <div class="venue_grid">
                <div class="venue_box">
                    <div class="venue_box_top">
                        <h5>
                            <n>Biswa Bangla Convention Centre</n>
                            <l>Action Area I, New Town, Rajarhat, Kolkata, West Bengal</l>
                        </h5>
                        <div class="venue_box_top_right">
                            <button class="badge_dark">Set Primary</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="venue_box_mid">

                    </div>
                    <div class="venue_box_bottom">
                        <p>Premier world-class convention facility featuring a 3200-seater main auditorium, 4 mega
                            exhibition halls, and 12 breakout seminar suites.</p>
                        <ul>
                            <li><i class="fal fa-hotel"></i>8 Halls / Suites</li>
                            <li><i class="fal fa-wifi"></i>High-density Wi-Fi 6 network</li>
                            <li><i class="fal fa-parking"></i>Underground parking capacity</li>
                            <li><i class="fal fa-phone-alt"></i>Medical Desk Control</li>
                        </ul>
                        <a class="badge_dark"><i class="fal fa-map-marker-alt"></i>Open in Google Maps</a>
                    </div>
                </div>
                <div class="venue_box active">
                    <div class="venue_box_top">
                        <h5>
                            <n>Biswa Bangla Convention Centre</n>
                            <l>Action Area I, New Town, Rajarhat, Kolkata, West Bengal</l>
                        </h5>
                        <div class="venue_box_top_right">
                            <button class="badge_success">Primary Venue</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="venue_box_mid">

                    </div>
                    <div class="venue_box_bottom">
                        <p>Premier world-class convention facility featuring a 3200-seater main auditorium, 4 mega
                            exhibition halls, and 12 breakout seminar suites.</p>
                        <ul>
                            <li><i class="fal fa-hotel"></i>8 Halls / Suites</li>
                            <li><i class="fal fa-wifi"></i>High-density Wi-Fi 6 network</li>
                            <li><i class="fal fa-parking"></i>Underground parking capacity</li>
                            <li><i class="fal fa-phone-alt"></i>Medical Desk Control</li>
                        </ul>
                        <a class="badge_dark"><i class="fal fa-map-marker-alt"></i>Open in Google Maps</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="committee">
            <div class="venue_hall_top">
                <h5>
                    <n><?php duser() ?>Organizing Committee Directory</n>
                    <l>Manage committee leadership roles, designations, contact numbers & display order.</l>
                </h5>
                <a class="badge_default popup-btn" data-tab="appaddcommitteemember"><?php add() ?>Add Committee Member</a>
            </div>

            <div class="committee_filter">
                <ul class="appcom_tab">
                    <button class="active" id="appcomall">All</button>
                    <button id="appcompatron">Patron</button>
                    <button id="appcomchairperson">Chairperson</button>
                    <button id="appcomorgsec">Organizing Secretary</button>
                    <button id="appcomtreasurer">Treasurer</button>
                    <button id="appcommember">Committee Member</button>
                </ul>
                <div class="search_div">
                    <i class="fal fa-search"></i>
                    <input>
                </div>
            </div>
            <div class="committee_wrap">
                <div class="committee_box appcombox appcompatron">
                    <div class="committee_box_top">
                        <span></span>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </div>
                    <div class="committee_box_mid">
                        <span class="badge_padding badge_secondary">Patron</span>
                        <h5>Dr. Subhashis Mukherjee</h5>
                        <h6>Chief Patron</h6>
                        <p>Chief Patron • National Chest Council of India</p>
                    </div>
                    <ul>
                        <li><?php call() ?>+91 98301 00000</li>
                        <li><?php email() ?>patron@ncci.org</li>
                    </ul>
                </div>
                <div class="committee_box appcombox appcomchairperson">
                    <div class="committee_box_top">
                        <span></span>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </div>
                    <div class="committee_box_mid">
                        <span class="badge_padding badge_secondary">Patron</span>
                        <h5>Dr. Subhashis Mukherjee</h5>
                        <h6>Chief Patron</h6>
                        <p>Chief Patron • National Chest Council of India</p>
                    </div>
                    <ul>
                        <li><?php call() ?>+91 98301 00000</li>
                        <li><?php email() ?>patron@ncci.org</li>
                    </ul>
                </div>
                <div class="committee_box appcombox appcomorgsec">
                    <div class="committee_box_top">
                        <span></span>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </div>
                    <div class="committee_box_mid">
                        <span class="badge_padding badge_secondary">Patron</span>
                        <h5>Dr. Subhashis Mukherjee</h5>
                        <h6>Chief Patron</h6>
                        <p>Chief Patron • National Chest Council of India</p>
                    </div>
                    <ul>
                        <li><?php call() ?>+91 98301 00000</li>
                        <li><?php email() ?>patron@ncci.org</li>
                    </ul>
                </div>
                <div class="committee_box appcombox appcomtreasurer">
                    <div class="committee_box_top">
                        <span></span>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </div>
                    <div class="committee_box_mid">
                        <span class="badge_padding badge_secondary">Patron</span>
                        <h5>Dr. Subhashis Mukherjee</h5>
                        <h6>Chief Patron</h6>
                        <p>Chief Patron • National Chest Council of India</p>
                    </div>
                    <ul>
                        <li><?php call() ?>+91 98301 00000</li>
                        <li><?php email() ?>patron@ncci.org</li>
                    </ul>
                </div>
                <div class="committee_box appcombox appcommember">
                    <div class="committee_box_top">
                        <span></span>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </div>
                    <div class="committee_box_mid">
                        <span class="badge_padding badge_secondary">Patron</span>
                        <h5>Dr. Subhashis Mukherjee</h5>
                        <h6>Chief Patron</h6>
                        <p>Chief Patron • National Chest Council of India</p>
                    </div>
                    <ul>
                        <li><?php call() ?>+91 98301 00000</li>
                        <li><?php email() ?>patron@ncci.org</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="faculty_master">
            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-award"></i>Faculty & Keynote Speakers Master</n>
                    <l>Manage international & national faculty, session allocations, speaker badge colors & bulk CSV
                        import.</l>
                </h5>
                <div class="venue_hall_top_right">
                    <a class="badge_info"><i class="fal fa-file-import"></i>Bulk Import</a>
                    <a class="badge_default popup-btn" data-tab="appaddfaculty"><?php add() ?>Add Faculty</a>
                </div>
            </div>
            <div class="table_wrap app_faculty_table">
                <table>
                    <thead>
                        <tr>
                            <th>Faculty</th>
                            <th>Specialization & Hospital</th>
                            <th>Session Allocations</th>
                            <th>Badge Theme</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="regi_img_circle">
                                        <!-- <img src="" alt="" class="w-100 h-100"> -->
                                        <span>AM</span>
                                    </div>
                                    <div>
                                        <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                                        <div class="regi_contact mt-0">
                                            <span>MD, FRCP (London), FCCP</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="regi_contact mt-0">
                                    <span>
                                        Interventional Pulmonology & Cryobiopsy
                                    </span>
                                    <span>
                                        Royal Brompton Hospital, London (United Kingdom)
                                    </span>
                                </div>
                            </td>
                            <td>
                                <ul class="service_ul">
                                    <li class="badge_padding badge_dark">Keynote 1: Future of Airway Interventions</li>
                                    <li class="badge_padding badge_dark">Workshop A: Cryobiopsy Masterclass</li>
                                </ul>
                            </td>

                            <td>
                                <small></small>
                                #3b82f6
                            </td>
                            <td>
                                <button><i class="fal fa-star"></i></button>
                            </td>
                            <td>
                                <div class="action_div">
                                    <a href="javascript:void(null)"
                                        class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                            class="fal fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="app_tab_content" id="quizpolls">
            <div class="committee_filter">
                <ul class="quiz_tab">
                    <button data-tab="quizmaster" class="active">Gamified Quiz Master</button>
                    <button data-tab="quizlivepoll">Live Audience Poll</button>
                </ul>
            </div>
            <div id="quizmaster" class="quizpollcontent" style="display:block;">
                <div class="basic_grid">
                    <div class="basic_grid_left">
                        <div class="basic_grid_box">
                            <div class="venue_hall_top push_noti_top">
                                <h5>
                                    <n><i class="fal fa-palette"></i>Grand Conference Quiz Configuration</n>
                                    <l>Configure quiz duration, passing marks, negative marking & leaderboard.</l>
                                </h5>
                                <div class="toggleswitch-switch"></div>
                            </div>
                            <div class="form_grid g_6">
                                <div class="frm_grp span_6">
                                    <p class="frm-head">Quiz Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_6">
                                    <p class="frm-head">Instructions for Delegates</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Passing %</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Duration (Mins)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Max Attempts</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_6">
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="checkbox" name="food">
                                            <span class="checkmark">Randomize Questions</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="checkbox" name="food">
                                            <span class="checkmark">Show Public</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="basic_grid_right">
                        <div class="push_noti_right_botttom">
                            <h5 class="real_time_portal_heading">
                                <n>Question Bank (2)</n>
                                <l class="badge_success">Active Pool</l>
                            </h5>
                            <div class="push_noti_right_botttom_body quizques_wrap">
                                <div class="push_noti_right_botttom_body_box">
                                    <p>
                                        <n>Q1. Which HRCT chest pattern is most characteristic of Usual Interstitial
                                            Pneumonia (UIP)?</n>
                                    </p>
                                    <ul class="quizques">
                                        <li class="ansred">Subpleural honeycombing with basal predominance</li>
                                        <li>Centrilobular nodules with tree-in-bud appearance</li>
                                        <li>Mosaic attenuation with air trapping</li>
                                        <li>Crazy-paving pattern</li>
                                    </ul>
                                </div>
                                <div class="push_noti_right_botttom_body_box">
                                    <p>
                                        <n>Q1. Which HRCT chest pattern is most characteristic of Usual Interstitial
                                            Pneumonia (UIP)?</n>
                                    </p>
                                    <ul class="quizques">
                                        <li class="ansred">Subpleural honeycombing with basal predominance</li>
                                        <li>Centrilobular nodules with tree-in-bud appearance</li>
                                        <li>Mosaic attenuation with air trapping</li>
                                        <li>Crazy-paving pattern</li>
                                    </ul>
                                </div>
                                <div class="push_noti_right_botttom_body_box">
                                    <p>
                                        <n>Q1. Which HRCT chest pattern is most characteristic of Usual Interstitial
                                            Pneumonia (UIP)?</n>
                                    </p>
                                    <ul class="quizques">
                                        <li class="ansred">Subpleural honeycombing with basal predominance</li>
                                        <li>Centrilobular nodules with tree-in-bud appearance</li>
                                        <li>Mosaic attenuation with air trapping</li>
                                        <li>Crazy-paving pattern</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="quizlivepoll" class="quizpollcontent">
                <div class="venue_grid">
                    <div class="venue_guide_box">
                        <div class="venue_hall_top push_noti_top quiz_live_top">
                            <h5>
                                <n><i class="fal fa-palette"></i>Live Audience Poll Configuration</n>
                                <l>Launch real-time polls during keynotes or panel discussions.</l>
                            </h5>
                            <l class="badge_success">Live</l>
                        </div>
                        <div class="form_grid">
                            <div class="frm_grp span_4">
                                <p class="frm-head">Poll Question</p>
                                <input>
                            </div>
                            <div class="frm_grp span_4">
                                <p class="frm-head">Options</p>
                                <ul class="quizliveques">
                                    <li class="p-0 border-0 bg-transparent">
                                        <span>01.</span>
                                        <div class="frm_grp">
                                            <input>
                                        </div>
                                    </li>
                                    <li class="p-0 border-0 bg-transparent">
                                        <span>01.</span>
                                        <div class="frm_grp">
                                            <input>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="push_noti_right_botttom">
                        <div class="venue_guid_bottom_head">
                            <h5 class="app_heading">Live Theme Component Preview</h5>
                            <a>Dark Mood</a>
                        </div>
                        <div class="push_noti_right_botttom_body_box">
                            <p>
                                <n>"In your clinical practice, what is your primary diagnostic choice for suspected
                                    Peripheral Lung Lesions?"</n>
                            </p>
                            <ul class="quizques quizliveprew">
                                <li class="p-0 border-0 bg-transparent">
                                    <div class="progress-bar-wrap">
                                        <div class="progress-value">Radial EBUS with Fluoroscopy<l>
                                                <n>40%</n> (144)
                                            </l>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-done bg_secondary" data-progress="40"
                                                style="width: 40%; opacity: 1;"></div>
                                        </div>
                                    </div>
                                    <button class="badge_secondary"><?php add() ?> Vote</button>
                                </li>
                                <li class="p-0 border-0 bg-transparent">
                                    <div class="progress-bar-wrap">
                                        <div class="progress-value">Radial EBUS with Fluoroscopy<l>
                                                <n>40%</n> (144)
                                            </l>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-done bg_secondary" data-progress="40"
                                                style="width: 40%; opacity: 1;"></div>
                                        </div>
                                    </div>
                                    <button class="badge_secondary"><?php add() ?> Vote</button>
                                </li>
                                <li class="p-0 border-0 bg-transparent">
                                    <div class="progress-bar-wrap">
                                        <div class="progress-value">Radial EBUS with Fluoroscopy<l>
                                                <n>40%</n> (144)
                                            </l>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-done bg_secondary" data-progress="40"
                                                style="width: 40%; opacity: 1;"></div>
                                        </div>
                                    </div>
                                    <button class="badge_secondary"><?php add() ?> Vote</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="push_notification">
            <div class="venue_grid">
                <div class="venue_box push_noti_box">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-bell"></i>Push Notification Broadcast Studio</n>
                            <l>Dispatch instant mobile alerts or schedule push banners for delegates.</l>
                        </h5>
                    </div>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <p class="frm-head">Target Audience</p>
                            <select></select>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Notification Title</p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Message Description</p>
                            <textarea></textarea>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Action Button Label</p>
                            <input>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Action URL Path</p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <a class="badge_default"><i class="fal fa-paper-plane"></i>Broadcast Notification Now</a>
                        </div>
                    </div>
                </div>
                <div class="push_noti_right">
                    <div class="push_noti_right_grid">
                        <div class="push_noti_right_grid_box">
                            <span>Broadcasts Sent</span>
                            <p>2</p>
                        </div>
                        <div class="push_noti_right_grid_box">
                            <span>Broadcasts Sent</span>
                            <p class="text_success">87.5%</p>
                        </div>
                        <div class="push_noti_right_grid_box">
                            <span>Broadcasts Sent</span>
                            <p class="text_info">3,450</p>
                        </div>
                    </div>
                    <div class="push_noti_right_botttom">
                        <h5 class="push_noti_right_botttom_head">Dispatch History & Performance</h5>
                        <div class="push_noti_right_botttom_body">
                            <div class="push_noti_right_botttom_body_box">
                                <p>
                                    <n>Welcome to IACTSCON 2026!</n>
                                    <l class="badge_success badge_padding">All Delegates</l>
                                </p>
                                <span>Please pick up your smart badge and delegate kit from Hall A Registration Desk
                                    1-4.</span>
                                <h6>
                                    <l>Delivered: 3240</l>
                                    <l>Opened: 2890</l>
                                    <n class="text_secondary">CTR: 89.2%</n>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="idcardstudio">
            <div class="venue_grid">
                <div class="push_noti_right_botttom">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-portrait"></i>Live Badge Canvas Preview (Delegate)</n>
                        </h5>
                        <span class="badge_secondary">Standard 85x54mm CR80 Badge</span>
                    </div>

                    <div class="id_card_box">
                        <div class="id_card_content">
                            <div class="id_card_content_top">
                                <n>80th IACTSCON 2026 • KOLKATA</n>
                                <l class="badge_default">Delegate PASS</l>
                                <p></p>
                                <h5>Dr. Sarah Williams</h5>
                                <h6>Royal Brompton Hospital, London</h6>
                            </div>
                            <div class="id_card_content_bottom">
                                <h5></h5>
                                <p>Scan for Hall Access & Meal Counter</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="id_box_right">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-sliders-v"></i>Badge Template Configurator</n>
                            <l>Customize header text, category badge color, background gradients & QR positions.</l>
                        </h5>
                    </div>
                    <div class="form_grid">
                        <div class="frm_grp span_3">
                            <p class="frm-head">Select Badge Category</p>
                            <div class="cus_check_wrap">
                                <label class="cus_check gender_check">
                                    <input type="radio" name="food">
                                    <span class="checkmark">Delegate</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="food">
                                    <span class="checkmark">Faculty</span>
                                </label>
                            </div>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Header Text</p>
                            <input>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Footer Text</p>
                            <input>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Badge Pill Color</p>
                            <input type="color">
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Gradient CSS Background</p>
                            <input>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="venueguide">
            <div class="venue_guide_top">
                <div class="venue_hall_top push_noti_top">
                    <h5>
                        <n><i class="fal fa-compass"></i>Digital Venue & Travel Guide Setup</n>
                        <l>Configure floor plan PDF links, nearby emergency hotlines, shuttle bus timings & venue
                            guidelines for the delegate web app.</l>
                    </h5>
                </div>
                <div class="form_grid">
                    <div class="frm_grp span_2">
                        <p class="frm-head">Hero Cover Image URL</p>
                        <input>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Floor Plan PDF Document URL</p>
                        <input>
                    </div>
                </div>
            </div>

            <div class="venue_grid">
                <div class="venue_guide_box">
                    <h5 class="app_heading"><i class="fal fa-phone-volume"></i>On-Site Emergency Hotlines</h5>
                    <ul>
                        <li>
                            <p>
                                <n>Conference Secretariat Desk</n>
                                <g class="text_info">+91 98300 12345</g>
                            </p>
                        </li>
                        <li>
                            <p>
                                <n>On-site Ambulance Control</n>
                                <g class="text_info">+91 98300 99911</g>
                            </p>
                        </li>
                        <li>
                            <p>
                                <n>Venue Security Office</n>
                                <g class="text_info">+91 98300 99911</g>
                            </p>
                        </li>
                    </ul>
                </div>
                <div class="venue_guide_box">
                    <h5 class="app_heading"><i class="fal fa-bus-school"></i>Transport & Shuttle Logistics</h5>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <p class="frm-head">Shuttle Service Announcement</p>
                            <textarea></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="venue_guid_bottom">
                <div class="venue_guid_bottom_head">
                    <h5 class="app_heading"><i class="fal fa-shield-check"></i>Venue Guidelines & Security Rules</h5>
                    <a class="badge_default"><i class="fal fa-plus"></i>Add Rule</a>
                </div>
                <ul>
                    <li>
                        <span>01.</span>
                        <div class="frm_grp">
                            <input>
                        </div>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </li>
                    <li>
                        <span>01.</span>
                        <div class="frm_grp">
                            <input>
                        </div>
                        <div class="action_div action">
                            <a href="javascript:void(null)"
                                class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                    class="fal fa-trash-alt"></i></a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="app_tab_content" id="imagelibrary">
            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-image"></i>Organize Image Library</n>
                    <l>Manage logos, Venue images, Member Images, banners etc.</l>
                </h5>
                <a class="badge_default popup-btn" data-tab="appaddimage"><?php add() ?>Add Image</a>
            </div>

            <div class="committee_filter">
                <ul class="appimage_tab">
                    <button class="active" id="appimageall">All</button>
                    <button id="appimagelogo">Logos</button>
                    <button id="appimagevenue">Venue</button>
                    <button id="appimagefaculty">Faculty</button>
                    <button id="appimagecommittee">Committee</button>
                    <button id="appimagesponcers">Sponsors</button>
                    <button id="appimagebanner">Banners</button>
                    <button id="appimagegallery">Gallery</button>
                </ul>
                <div class="search_div">
                    <i class="fal fa-search"></i>
                    <input>
                </div>
            </div>
            <div class="committee_wrap">
                <div class="committee_box appimagebox appimagelogo">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="committee_box appimagebox appimagevenue">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="committee_box appimagebox appimagefaculty">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="committee_box appimagebox appimagecommittee">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="committee_box appimagebox appimagesponcers">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="committee_box appimagebox appimagebanner">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="committee_box appimagebox appimagegallery">
                    <div class="image_box">
                        <div class="image_box_top">
                            <span>Logo</span>
                            <img src="images/photo-1576091160399-112ba8d25d1d.avif">
                        </div>
                        <p>IACTSCON_2026_MainLogo_Dark.png</p>
                        <h6>1.2 MB • 2026-01-10</h6>
                        <div class="image_box_bottom">
                            <button class="badge_primary"><i class="fal fa-copy"></i>Copy Url</button>
                            <div class="action_div action">
                                <a href="javascript:void(null)"
                                    class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                        class="fal fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="app_tab_content" id="brandtheme">
            <div class="venue_grid">
                <div class="theme_box">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-palette"></i>Conference Brand Palette & Theme</n>
                            <l>Configure primary highlights, dark backgrounds & UI accent gradients.</l>
                        </h5>
                    </div>
                    <div class="form_grid">
                        <div class="frm_grp span_4">
                            <p class="frm-head">Preset Color Schemes</p>
                            <div class="cus_check_wrap form_grid">
                                <label class="cus_check gender_check span_2">
                                    <input type="radio" name="food">
                                    <span class="checkmark">Orange Accent (Default)</span>
                                </label>
                                <label class="cus_check gender_check span_2">
                                    <input type="radio" name="food">
                                    <span class="checkmark">Royal Emerald</span>
                                </label>
                            </div>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Primary Color (Buttons & Highlights)</p>
                            <div class="form_grid">
                                <input type="color" class="span_1">
                                <input class="span_3">
                            </div>
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Accent Color (Badges & Rings)</p>
                            <div class="form_grid">
                                <input type="color" class="span_1">
                                <input class="span_3">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="push_noti_right_botttom">
                    <div class="venue_guid_bottom_head">
                        <h5 class="app_heading">Live Theme Component Preview</h5>
                        <a class="badge_dark">Dark Mood</a>
                    </div>

                    <div class="theme_con_box">
                        <h5>
                            <span>NA</span>
                            <n>
                                <l>IACTSCON 2026</l>
                                <g>Theme System Active</g>
                            </n>
                        </h5>
                        <p>Primary Action Button</p>
                        <h6 class="badge_dark">
                            <n>Status Badge Accent</n>
                            <l>Active</l>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="featuremodule">
            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-sliders-v"></i>Feature Module Matrix & Toggles</n>
                    <l>Enable or disable specialized conference features (e.g. Gamified Quiz, E-Certificates, RFID
                        Access Control, Abstract Portal).</l>
                </h5>
                <a class="badge_success iconanimation"><i class="fas fa-circle"></i>24 / 24 Active Modules</a>
            </div>

            <div class="committee_filter">
                <ul class="appfeature_tab">
                    <button class="active" id="appfeatureall">All</button>
                    <button id="appfeaturecore">Core</button>
                    <button id="appfeatureregistration">Registration</button>
                    <button id="appfeaturescientific">Scientific</button>
                    <button id="appfeatureoperations">Operations</button>
                    <button id="appfeatureengagement">Engagement</button>
                    <button id="appfeaturesecurity">Security</button>
                </ul>
            </div>

            <div class="committee_wrap">
                <div class="feature_box appfeaturebox appfeaturecore">
                    <div class="feature_box_top">
                        <span class="badge_secondary"><?php duser() ?></span>
                        <h5>
                            <n>Registration Engine</n>
                            <l>Core</l>
                        </h5>
                        <label class="toggleswitch">
                            <input class="toggleswitch-checkbox" type="checkbox">
                            <div class="toggleswitch-switch"></div>
                        </label>
                    </div>
                    <p>Online registration, ticket tiers, discount codes & spot check-in desk</p>
                    <div class="feature_box_bottom">
                        <n class="text_success">Module Active</n>
                        <a class="badge_dark"><i class="fal fa-cog"></i>Config</a>
                    </div>
                </div>
                <div class="feature_box appfeaturebox appfeatureregistration">
                    <div class="feature_box_top">
                        <span class="badge_secondary"><?php duser() ?></span>
                        <h5>
                            <n>Registration Engine</n>
                            <l>Core</l>
                        </h5>
                        <label class="toggleswitch">
                            <input class="toggleswitch-checkbox" type="checkbox">
                            <div class="toggleswitch-switch"></div>
                        </label>
                    </div>
                    <p>Online registration, ticket tiers, discount codes & spot check-in desk</p>
                    <div class="feature_box_bottom">
                        <n class="text_success">Module Active</n>
                        <a class="badge_dark"><i class="fal fa-cog"></i>Config</a>
                    </div>
                </div>
                <div class="feature_box appfeaturebox appfeaturescientific">
                    <div class="feature_box_top">
                        <span class="badge_secondary"><?php duser() ?></span>
                        <h5>
                            <n>Registration Engine</n>
                            <l>Core</l>
                        </h5>
                        <label class="toggleswitch">
                            <input class="toggleswitch-checkbox" type="checkbox">
                            <div class="toggleswitch-switch"></div>
                        </label>
                    </div>
                    <p>Online registration, ticket tiers, discount codes & spot check-in desk</p>
                    <div class="feature_box_bottom">
                        <n class="text_success">Module Active</n>
                        <a class="badge_dark"><i class="fal fa-cog"></i>Config</a>
                    </div>
                </div>
                <div class="feature_box appfeaturebox appfeatureoperations">
                    <div class="feature_box_top">
                        <span class="badge_secondary"><?php duser() ?></span>
                        <h5>
                            <n>Registration Engine</n>
                            <l>Core</l>
                        </h5>
                        <label class="toggleswitch">
                            <input class="toggleswitch-checkbox" type="checkbox">
                            <div class="toggleswitch-switch"></div>
                        </label>
                    </div>
                    <p>Online registration, ticket tiers, discount codes & spot check-in desk</p>
                    <div class="feature_box_bottom">
                        <n class="text_success">Module Active</n>
                        <a class="badge_dark"><i class="fal fa-cog"></i>Config</a>
                    </div>
                </div>
                <div class="feature_box appfeaturebox appfeatureengagement">
                    <div class="feature_box_top">
                        <span class="badge_secondary"><?php duser() ?></span>
                        <h5>
                            <n>Registration Engine</n>
                            <l>Core</l>
                        </h5>
                        <label class="toggleswitch">
                            <input class="toggleswitch-checkbox" type="checkbox">
                            <div class="toggleswitch-switch"></div>
                        </label>
                    </div>
                    <p>Online registration, ticket tiers, discount codes & spot check-in desk</p>
                    <div class="feature_box_bottom">
                        <n class="text_success">Module Active</n>
                        <a class="badge_dark"><i class="fal fa-cog"></i>Config</a>
                    </div>
                </div>
                <div class="feature_box appfeaturebox appfeaturesecurity">
                    <div class="feature_box_top">
                        <span class="badge_secondary"><?php duser() ?></span>
                        <h5>
                            <n>Registration Engine</n>
                            <l>Core</l>
                        </h5>
                        <label class="toggleswitch">
                            <input class="toggleswitch-checkbox" type="checkbox">
                            <div class="toggleswitch-switch"></div>
                        </label>
                    </div>
                    <p>Online registration, ticket tiers, discount codes & spot check-in desk</p>
                    <div class="feature_box_bottom">
                        <n class="text_success">Module Active</n>
                        <a class="badge_dark"><i class="fal fa-cog"></i>Config</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="app_tab_content" id="exexcutivestats">
            <div class="push_noti_right_grid">
                <div class="executiver_grid_box">

                    <div class="executiver_grid_box_top">
                        <p>
                            <l>Total Conferences</l>
                            <n>8,600</n>
                        </p>
                        <span class="badge_primary"><?php duser() ?></span>
                    </div>
                    <div class="executiver_grid_box_bottom">
                        1 Live • 2 Upcoming
                    </div>
                </div>
            </div>

            <div class="exexcutivestats_bottom">
                <h5 class="app_heading"><i class="fal fa-microchip"></i>Cloud Infrastructure & Gateway Status</h5>
                <ul>
                    <li>
                        <p>Database Latency</p>
                        <span class="text_success">12 ms</span>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    </div>
    <?php include_once("includes/app_popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    
    $('.app_tab_btn').owlCarousel({
        loop: false,
        margin: 5,
        nav: true,
        dots: false,
        autoWidth: true,
        dots: false,
        dotsData: false,
        nav: true,
        navText: [
            '<i class="fal fa-angle-left"></i>',
            '<i class="fal fa-angle-right"></i>'
        ],
        mouseDrag: true,
    })
    $('.appfeature_tab button').click(function () {
        if (this.id == 'appfeatureall') {
            $('.appfeaturebox').fadeIn(450);
        } else {
            var $el = $('.' + this.id).fadeIn(450);
            $('.appfeaturebox').not($el).hide();
        }
        $('.appfeature_tab button').removeClass('active');
        $(this).addClass('active');
    })
    $('.appimage_tab button').click(function () {
        if (this.id == 'appimageall') {
            $('.appimagebox').fadeIn(450);
        } else {
            var $el = $('.' + this.id).fadeIn(450);
            $('.appimagebox').not($el).hide();
        }
        $('.appimage_tab button').removeClass('active');
        $(this).addClass('active');
    })
    $('.appcom_tab button').click(function () {
        if (this.id == 'appcomall') {
            $('.appcombox').fadeIn(450);
        } else {
            var $el = $('.' + this.id).fadeIn(450);
            $('.appcombox').not($el).hide();
        }
        $('.appcom_tab button').removeClass('active');
        $(this).addClass('active');
    })
    $('.app_tab_btn li').click(function () {
        var tabId = $(this).attr('data-tab');
        $(".app_tab_content").hide();
        $(".app_tab_btn li").removeClass('active');
        $('#' + tabId).show();
        $(this).addClass('active');
    });

    $('.quiz_tab button').click(function () {
        var tabId = $(this).attr('data-tab');
        $(".quizpollcontent").hide();
        $(".quiz_tab button").removeClass('active');
        $('#' + tabId).show();
        $(this).addClass('active');
    });
</script>

</html>