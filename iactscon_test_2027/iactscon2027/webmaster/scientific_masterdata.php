<?php 
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once("../../includes/function.registration.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accompany.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.abstract.php');
include_once('includes/function.php');
// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
?>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Scientific Program</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Scientific Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Master Data</a></li>
                    </ol>
                </nav>
                <h2>Master Data</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Master Menu</h6>
                <button data-tab="date_master" class="com_info_left_click icon_hover badge_secondary active">Date Masters</button>
                <button data-tab="venue_master" class="com_info_left_click icon_hover badge_success action-transparent">Venue Masters</button>
                <button data-tab="hall_master" class="com_info_left_click icon_hover badge_danger action-transparent">Hall Master</button>
                <button data-tab="participant_master" class="com_info_left_click icon_hover badge_info action-transparent">Participant Types</button>
                <button data-tab="highlight_speakers" class="com_info_left_click icon_hover badge_primary action-transparent">Eminent Speaker</button>
                <button data-tab="session_master" class="com_info_left_click icon_hover badge_dark action-transparent">Session Master</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box " id="highlight_speakers">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head"> 
                                <n><span class="text_primary">Highlight Speakers</n>
                                <a class="add mi-1 popup-btn" data-tab="newspeaker"><?php add() ?>Add Speaker</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Speaker</th>
                                                <th>Date & Time</th>
                                                <th>Hall</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing					 = array();
                                            $sqlHallListing['QUERY']		 = "SELECT * FROM " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . " WHERE `status` != 'D'";
                                            $resultHallListing  			 = $mycms->sql_select($sqlHallListing);
                                            $Counter			 			 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <!-- <div class="regi_img_circle">
                                                            <span>AM</span>
                                                        </div> -->
                                                        <div>
                                                            <div class="regi_name"><?= $rowHallListing['speaker_name'] ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php calendar() ?> <?= $rowHallListing['conf_datetime'] ?>
                                                   
                                                </td>
                                                <td><?= $rowHallListing['hall_name'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'SpeakerInactive' : 'SpeakerActive') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                        <!-- <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span> -->
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editspeaker" data-id="<?=$rowHallListing['id']?>?" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editspeakerbtn"><?php edit(); ?></a>
                                                        <!-- <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box active" id="date_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary">Date Masters</n>
                                <a class="add mi-1 popup-btn" data-tab="newscientificdate"><?php add() ?>Add Date</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Program Details</th>
                                                <th>Description</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing					 = array();
                                            $sqlHallListing['QUERY']		 = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " WHERE `status` != 'D'";
                                            $resultHallListing  			 = $mycms->sql_select($sqlHallListing);
                                            $Counter			 			 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td>
                                                    <div class="regi_name"><?= $rowHallListing['conf_title'] ?></div>
                                                    <div class="regi_contact mt-0">
                                                        <span style="color: #ffffff;!important">
                                                            <?php calendar() ?><?= date('Y-m-d', strtotime($rowHallListing['conf_date'])) ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?= $rowHallListing['conf_desc'] ?>
                                                </td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'DateInactive' : 'DateActive') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                       
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificdate" data-id="<?=$rowHallListing['id']?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editscientificdatebtn"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeConferenceDate&id=<?= $rowHallListing['id'] ?>" class="icon_hover badge_danger action-transparent br-5 w-auto"  onclick="return confirm('Do you really want to remove this record?')"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="venue_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success">Venue Masters</n>
                                <a class="add mi-1 popup-btn" data-tab="newscientificvenue"><?php add() ?>Add Venue</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Venue</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing			 = array();
                                            $sqlHallListing = array();
                                            $sqlHallListing['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_VENUE_ . " WHERE `status` != ?";

                                            $sqlHallListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');

                                            $resultHallListing   	 = $mycms->sql_select($sqlHallListing);
                                            $Counter			 	 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowHallListing['program_venue'] ?>

                                                </td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'InactiveVenue' : 'ActiveVenue') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificvenue" data-id="<?=$rowHallListing['id']?>" data-title="<?=$rowHallListing['program_venue']?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editscientificvenuebtn"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeVenue&id=<?= $rowHallListing['id'] ?>" onclick="return confirm('Do you really want to remove this record?')" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="participant_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info">Participant Types</n>
                                <a class="add mi-1 popup-btn" data-tab="newscientificparticipanttype"><?php add() ?>Add Type</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Participant Types</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlParticipantTypeListing					= array();
                                            $sqlParticipantTypeListing['QUERY']		 	= "SELECT * FROM " . _DB_SP_PARTICIPANT_TYPE_ . " WHERE `status` = 'A' ORDER BY type_name";
                                            $resultParticipantTypeListing  			 	= $mycms->sql_select($sqlParticipantTypeListing);
                                            $Counter			 			 			= 0;
                                            if ($resultParticipantTypeListing) {
                                                foreach ($resultParticipantTypeListing as $kkkk => $rowParticipantTypeListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowParticipantTypeListing['type_name'] ?>

                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificparticipanttype" data-id="<?=$rowParticipantTypeListing['id']?>" data-type="<?=$rowParticipantTypeListing['type_name']?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editscientificparticipanttypebtn"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeParticipantType&id=<?= $rowParticipantTypeListing['id'] ?>" class="icon_hover badge_danger action-transparent br-5 w-auto"  onclick="return confirm('Do you really want to remove this record?')"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="hall_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box"> 
                            <h5 class="com_info_box_head">
                            <n><span class="text_info">Hall Master</n>
                            <a class="add mi-1 popup-btn" data-tab="newscientifichall"><?php add(); ?>Add Hall</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Hall Title</th>
                                                <th>Venue</th>
                                                <th>Hall Tag</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing	= array();
                                            $sqlHallListing['QUERY'] = "SELECT hall.*,
                                                                            venue.program_venue 
                                                                        FROM " . _DB_MASTER_HALL_ . " hall
                                                            LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue
                                                                            ON hall.hall_venue = venue.id 
                                                                        WHERE hall.status != ?";

                                            $sqlHallListing['PARAM'][]  = array('FILD' => 'hall.status',  'DATA' => 'D',  'TYP' => 's');

                                            $resultHallListing   = $mycms->sql_select($sqlHallListing);
                                            $Counter			 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowHallListing['hall_title'] ?></td>
                                                <td><?= $rowHallListing['program_venue'] ?></td>
                                                <td><?= $rowHallListing['tag_name'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'HallInactive' : 'HallActive') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientifichall" data-id="<?=$rowHallListing['id']?>"  class="popup-btn editscientifichallbtn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeHall&id=<?= $rowHallListing['id'] ?>" onclick="return confirm('Doyou really want to remove this record?');" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="session_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info">Session Master</n>
                                <a class="add mi-1 popup-btn" data-tab="addscientificSession"><?php add() ?>Add Session</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Session Type Name</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlSessionListing	= array();
                                            $sqlSessionListing['QUERY'] = "SELECT * FROM " . _DB_SESSION_CLASSIFICATION .
                                                " WHERE status != 'D'";
                                            $resultSessionListing   = $mycms->sql_select($sqlSessionListing);
                                            $Counter			 = 0;
                                            if ($resultSessionListing) {
                                                foreach ($resultSessionListing as $keySessionListing => $rowSessionListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowSessionListing['session_classifications'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowSessionListing['status'] == 'A' ? 'sessionInactive' : 'sessionActive') ?>&id=<?= $rowSessionListing['id'] ?>" class="<?= ($rowSessionListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowSessionListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificSession" data-id="<?=$rowSessionListing['id']?>" data-stype="<?=$rowSessionListing['session_classifications']?>" class="popup-btn editscientificSessionbtn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeSession&id=<?= $rowSessionListing['id'] ?>"  onclick="return confirm('Doyou really want to remove this record?');" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
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
    $(document).ready(function() {
        // Check if URL has hash
        var hash = window.location.hash.substring(1); // removes '#'
        if(hash) {
            // Trigger the tab click for that hash
            var btn = $('.com_info_left_click[data-tab="' + hash + '"]');
            if(btn.length) {
                btn.trigger('click');
            }
        }
    });
     ///////////////////speaker edit start///////////////////////////
       $(document).on('click', '.editspeakerbtn', function() {
            //    console.log($(this).data('package'));
        let speakerHId   = $(this).data('id');
       
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                speakerHId: speakerHId
               
            },
             success: function(response) {

        $('#editspeaker').html($(response).find('#editspeaker').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditspeaker();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////speaker edit end///////////////////////////
           ///////////////////date edit start///////////////////////////
       $(document).on('click', '.editscientificdatebtn', function() {
            //    console.log($(this).data('package'));
        let dateId   = $(this).data('id');
       
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                dateId: dateId
               
            },
             success: function(response) {

        $('#editscientificdate').html($(response).find('#editscientificdate').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditscientificdater();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////date edit end///////////////////////////
         ///////////////////vanue edit start///////////////////////////

     $(document).on('click', '.editscientificvenuebtn', function() {
        var venue_id     = $(this).data('id');
        var title  = $(this).data('title');
       
        $('#venue_id').val(venue_id);
        $('#session_venue_edit').val(title);
     

        // if(status === 'A') {
        //     $('#edit_status_active').prop('checked', true);
        // } else {
        //     $('#edit_status_inactive').prop('checked', true);
        // }

        // Trigger the popup-btn functionality
     $('#editscientificvenue').fadeIn(); // or your popup open function
    });
             ///////////////////vanue edit end///////////////////////////
               ///////////////////participant edit start///////////////////////////

     $(document).on('click', '.editscientificparticipanttypebtn', function() {
        var participant_type_id     = $(this).data('id');
        var type  = $(this).data('type');
       
        $('#participant_type_id').val(participant_type_id);
        $('#type_name_edit').val(type);
     

    
        // Trigger the popup-btn functionality
     $('#editscientificparticipanttype').fadeIn(); // or your popup open function
    });
             ///////////////////participant edit end///////////////////////////
           /////////////////// hall start///////////////////////////
       $(document).on('click', '.editscientifichallbtn', function() {
            //    console.log($(this).data('package'));
        let hallId   = $(this).data('id');
       
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                hallId: hallId
               
            },
             success: function(response) {

        $('#editscientifichall').html($(response).find('#editscientifichall').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditscientifichall();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////hall edit end///////////////////////////
           ///////////////////session edit start///////////////////////////

     $(document).on('click', '.editscientificSessionbtn', function() {
        var sessionId     = $(this).data('id');
        var stype  = $(this).data('stype');
       
        $('#sessionId').val(sessionId);
        $('#session_type_edit').val(stype);
     

    
        // Trigger the popup-btn functionality
     $('#editscientificSession').fadeIn(); // or your popup open function
    });
             ///////////////////session edit end///////////////////////////
</script>

</html>