<?php 
include_once("includes/source.php");
include_once('includes/init.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
$loggedUserId		= $mycms->getLoggedUserId();
$buttonAccessArray = array();
$buttonAccessArray['endeveloper'] = 1;
$buttonAccessArray['sourav'] = 15;
$buttonAccessArray['zinia'] = 16;
$buttonAccessArray['tamanna'] = 123;
$buttonAccessArray['cilledbr'] = 6;
$buttonAccessArray['ipsita'] = 119;

$buttonSpPrgAccessArray = array();
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Manage Participant Comments</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <?php
    $participantId	 = $_REQUEST['id'];
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " WHERE `status` = 'A' AND `id` = '" . $participantId . "'";
	$resultsListing	 = $mycms->sql_select($sqlListing);
	$rowDetails		 = $resultsListing[0];
    ?>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Scientific Program</a></li>
                        <li class="breadcrumb-item">Manage Participant</li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Participant Comments</li>
                    </ol>
                </nav>
                <h2>Participant Comments</h2>
            </div>

        </div>
        <div class="com_info_wrap">
            <div class="com_info_left p-3" style="gap: var(--gap);">
                <div class="regi_img_circle">
                    <!-- <?
                    if ($rowDetails['participant_image'] != "" && file_exists('../../' . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $rowDetails['participant_image'])) {
                        $setUserProfileImage    = _BASE_URL_ . $cfg['SP.PARTICIPANT.PROFILE.IMAGE'] . $rowDetails['participant_image'];
                                     ?> <span><img alt="Participant Image" id="userProfileImagePreview" src="<?= $setUserProfileImage ?>"></span>

                       <? } else {
                        $setUserProfileImage    = "uploads/nouserimage.png";
                    }
                    ?> -->
                    <!-- <img src="" alt="" class="w-100 h-100"> -->
                </div>
                <div>
                    <div class="regi_name"><?=  $rowDetails['participant_full_name'] ?></div>
                    <div class="regi_contact">
                        
                        <span>
                            <i class="fal fa-envelope"></i><?= nl2br($rowDetails['participant_email_id']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="com_info_right">
                <div class="comment_info_box">
                    <div class="com_info_box_grid">
                        <?
                        $sqlParti = array();
                        $sqlParti['QUERY'] 	   = "SELECT participant_schedule.*, 
                                                    program_date.conf_date, 
                                                    program_topic.topic_title, program_topic.reference_tag, program_topic.topic_time_start, program_topic.topic_time_end, 
                                                    program_theme.theme_title, program_theme.theme_time_start, program_theme.theme_time_end, 
                                                    program_session.session_title, program_session.session_start_time, program_session.session_end_time, 
                                                    IFNULL( IFNULL(program_hallTempname.hall_name, program_hall.hall_title) , IFNULL(session_hallTempname.hall_name, session_hall.hall_title) ) AS hall_title,		
                                                    participant_details.participant_full_name 
                                                FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
                                        INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
                                                ON participant_schedule.date_id = program_date.id
                                        INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
                                                ON participant_schedule.participant_id = participant_details.id
                                    LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
                                                ON participant_schedule.topic_id = program_topic.id
                                    LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
                                                ON participant_schedule.session_id = program_session.id
                                    LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " session_hall
                                                ON program_session.session_hall_id = session_hall.id	
                                    LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " session_hallTempname
                                                ON session_hall.id = session_hallTempname.hall_id  
                                                AND session_hallTempname.date_id = program_date.id
                                    LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
                                                ON participant_schedule.theme_id = program_theme.id
                                    LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
                                                ON participant_schedule.hall_id = program_hall.id
                                    LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " program_hallTempname
                                                ON session_hall.id = program_hallTempname.hall_id  
                                                AND program_hallTempname.date_id = program_date.id
                                            WHERE program_date.status = 'A' 
                                                AND participant_schedule.participant_id = '" . $participantId . "' ";

                        $resParti		= $mycms->sql_select($sqlParti);

                        //echo '<pre>'; print_r($resParti);
                        $rowPartiDetail = array();
                        foreach ($resParti as $key => $rowaccomm) {
                            if ($rowaccomm['hall_id'] != '') {
                                $rowaccomm['display_topic_title'] = $rowaccomm['hall_title'];
                            }
                            if ($rowaccomm['session_id'] != '') {
                                $rowaccomm['display_topic_title'] = $rowaccomm['session_title'];
                            }
                            if ($rowaccomm['theme_id'] != '') {
                                $rowaccomm['display_topic_title'] = $rowaccomm['theme_title'];
                            }
                            if ($rowaccomm['topic_id'] != '') {
                                $rowaccomm['display_topic_title'] = $rowaccomm['reference_tag'] . '. ' . $rowaccomm['topic_title'];
                            }

                            $startTmExpld				= explode(":", $rowaccomm['start_time']);
                            $endTmExpld					= explode(":", $rowaccomm['end_time']);

                            $start_time					= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
                                . ":"
                                . (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

                            $end_time					= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
                                . ":"
                                . (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

                            $start_time_mins			= ($startTmExpld[0] * 60) + $startTmExpld[1];
                            $end_time_mins				= ($endTmExpld[0] * 60) + $endTmExpld[1];
                            $duration_mins		  		= $end_time_mins - $start_time_mins;

                            $rowaccomm['start_time']	= $start_time;
                            $rowaccomm['end_time']		= $end_time;
                            $rowaccomm['duration']		= $duration_mins;

                            $rowPartiDetail[$rowaccomm['conf_date']][$rowaccomm['session_id']][] = $rowaccomm;
                        }

                        foreach ($rowPartiDetail as $congDate => $dateWiseDetail) {
                        ?>
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <span class="text_default"><?= $congDate ?></span>
                            </h5>
                            	<?
							$ssId = '';
							foreach ($dateWiseDetail as $sessId => $theSession) {
								foreach ($theSession as $key => $schedule) {
							?>
                            <div class="com_info_box_inner" use="scheduleContainer" data_participant_id="<?= $participantId ?>" data_date_id="<?= $schedule['date_id'] ?>" data_hall_id="<?= $schedule['hall_id'] ?>" data_session_id="<?= $schedule['session_id'] ?>" data_theme_id="<?= $schedule['theme_id'] ?>" data_topic_id="<?= $schedule['topic_id'] ?>">
                                <h4 class="com_info_box_inner_sub_head"><span><?=strtolower(trim($schedule['participant_type']))?></span><a href="javascript:void(null)" class="popup-btn add mi-1" data-tab="participentcomment" onclick="commentAdd(this);"><?php add(); ?>Add Comment</a></h4>
                                <div class="spot_listing">
                                    <div class="spot_box">
                                        <div class="spot_box_top">
                                            <div class="spot_details pl-0 w-100">
                                                <div class="spot_details_box">
                                                    <h5>Auditorium / Hall</h5>
                                                    <p><?=$schedule['hall_title']?></p>
                                                    <p><?php clock() ?><?=$schedule['start_time'] . ' - ' . $schedule['end_time'] ?></p>
                                                </div>
                                                <div class="spot_details_box">
                                                    <h5>Session</h5>
                                                    <p><?=$schedule['session_title']?></p>
                                                </div>
                                                <div class="spot_details_box">
                                                    <h5>Topic of Presentation</h5>
                                                    <p><?=(trim($schedule['reference_tag']) != '' ? ($schedule['reference_tag'] . '.') : '') . ' ' . $schedule['display_topic_title']?></p>
                                                    <p>Duration: <?=$schedule['duration']?> minutes</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                    $sqlComent = array();
                                    $sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
                                                                recordingUser.name AS recoderName, completingUser.name As completerName
                                                        FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
                                                LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
                                                            ON participant_comment.recordedBy = recordingUser.a_id
                                                LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
                                                            ON participant_comment.completedBy = completingUser.a_id
                                                        WHERE participant_comment.participant_id = '" . $participantId . "'
                                                            AND participant_comment.hall_id 		= '" . $schedule['hall_id'] . "'
                                                            AND participant_comment.session_id 	= '" . $schedule['session_id'] . "'
                                                            AND participant_comment.theme_id 		= '" . $schedule['theme_id'] . "'
                                                            AND participant_comment.topic_id 		= '" . $schedule['topic_id'] . "'
                                                    ORDER BY participant_comment.id DESC";
                                    $resComent		= $mycms->sql_select($sqlComent);
                                    if ($resComent) {
                                        foreach ($resComent as $k => $rowComments) {
                                    ?>
                                    <div class="spot_box">
                                        <div class="spot_box_top">
                                            <div class="spot_details w-100 pl-0">
                                                <div class="spot_details_box" style="    flex: 5;">
                                                    <h5>Participant Comment</h5>
                                                    <h6><?= nl2br($rowComments['comment']) ?></h6>
                                                    <small>Recorded By <?= $rowComments['recoderName'] ?> on <?= $rowComments['created_dateTime'] ?></small>
                                                </div>
                                                <div class="spot_details_box align-items-end">
                                                    <div class="action_div">
                                                        <?
                                                        if (in_array($loggedUserId, $buttonAccessArray)) {
                                                            if ($rowComments['completionStatus'] == 'INCOMPLETE') {
                                                        ?>
                                                        <a data-tab="editparticipentcomment"  data_comment_id="<?= $rowComments['id'] ?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" onclick="commentEdit(this);"><?php edit(); ?></a>
                                                        <a href="#" data_comment_id="<?= $rowComments['id'] ?>"  class="icon_hover badge_danger action-transparent br-5 w-auto" onclick="commentDelete(this);"><?php delete(); ?></a>
                                                        <a href="javascript:void(null)"  data_comment_id="<?= $rowComments['id'] ?>"  data-tab="participentcommentcompletion" class="popup-btn badge_info icon_hover action-transparent br-5" onclick="commentComplete(this);"><i class="fal fa-play"></i></a>
                                                        <? } else{?>
                                                              <a data-tab="editparticipentcomment"  data_comment_id="<?= $rowComments['id'] ?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" onclick="commentEdit(this);"><?php edit(); ?></a>
                                                               <a href="#" data_comment_id="<?= $rowComments['id'] ?>"  class="icon_hover badge_danger action-transparent br-5 w-auto" onclick="commentDelete(this);"><?php delete(); ?></a>
                                                          <?} }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <? if ($rowComments['completionStatus'] == 'DONE') {
																		?>
                                        <div class="spot_box_bottom ">
                                            <div class="spot_box_bottom_left w-100">
                                                <ul class="service_breakdown_wrap_ul w-100">
                                                    <li class="p-0 border-0 w-100" style="padding: 0 !important;">
                                                        <n><small>Completed By<?= $rowComments['completerName'] ?> on <?= $rowComments['completionDate'] ?></small>
                                                            <j><?= nl2br($rowComments['completionRemarks']) ?></j>
                                                        </n>

                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <? } ?>
                                    </div>
                                     <? } } ?>
                                </div>
                            </div>
                            <? } } ?>
                           
                        </div>
                        <? } ?>
                      
                    </div>
                </div>
            </div>
        </div>


    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>
<script>
function commentAdd(obj) {

    setTimeout(function () {

        // Get current schedule container only
        let parent = $(obj).closest('div[use="scheduleContainer"]');

        var val_participant_id = parent.attr('data_participant_id');
        var val_date_id        = parent.attr('data_date_id');
        var val_hall_id        = parent.attr('data_hall_id');
        var val_session_id     = parent.attr('data_session_id');
        var val_theme_id       = parent.attr('data_theme_id');
        var val_topic_id       = parent.attr('data_topic_id');

        let param = "";
        param += "participant_id=" + val_participant_id;
        param += "&date_id=" + val_date_id;
        param += "&hall_id=" + val_hall_id;
        param += "&session_id=" + val_session_id;
        param += "&theme_id=" + val_theme_id;
        param += "&topic_id=" + val_topic_id;

        // Debug
        // console.log(param);

        $.ajax({
           url: 'includes/popup.php',
            type: 'POST',
            data: param,
            success: function (response) {
               $('#participentcomment').html($(response).find('#participentcomment').html());
            // Re-initialize after DOM replacement
            initparticipentcomment();
               $('#participentcomment').fadeIn();
            }
        });

    }, 200);
}
function commentEdit(obj) {

    setTimeout(function () {

        // Get current schedule container only
        let parent = $(obj).closest('div[use="scheduleContainer"]');

        var val_participant_id = parent.attr('data_participant_id');
        var val_date_id        = parent.attr('data_date_id');
        var val_hall_id        = parent.attr('data_hall_id');
        var val_session_id     = parent.attr('data_session_id');
        var val_theme_id       = parent.attr('data_theme_id');
        var val_topic_id       = parent.attr('data_topic_id');
				var val_comment_id = $(obj).attr('data_comment_id');

        let param = "";
        param += "participant_id=" + val_participant_id;
        param += "&date_id=" + val_date_id;
        param += "&hall_id=" + val_hall_id;
        param += "&session_id=" + val_session_id;
        param += "&theme_id=" + val_theme_id;
        param += "&topic_id=" + val_topic_id;
				param += "&comment_id=" + val_comment_id;

        // Debug

        $.ajax({
           url: 'includes/popup.php',
            type: 'POST',
            data: param,
            success: function (response) {
               $('#editparticipentcomment').html($(response).find('#editparticipentcomment').html());
            // Re-initialize after DOM replacement
            initeditparticipentcomment();
               $('#editparticipentcomment').fadeIn();
            }
        });

    }, 200);
}
function commentComplete(obj) {

    setTimeout(function () {

        // Get current schedule container only
        let parent = $(obj).closest('div[use="scheduleContainer"]');

        var val_participant_id = parent.attr('data_participant_id');
        var val_date_id        = parent.attr('data_date_id');
        var val_hall_id        = parent.attr('data_hall_id');
        var val_session_id     = parent.attr('data_session_id');
        var val_theme_id       = parent.attr('data_theme_id');
        var val_topic_id       = parent.attr('data_topic_id');
				var val_comment_id = $(obj).attr('data_comment_id');

        let param = "";
        param += "participant_id=" + val_participant_id;
        param += "&date_id=" + val_date_id;
        param += "&hall_id=" + val_hall_id;
        param += "&session_id=" + val_session_id;
        param += "&theme_id=" + val_theme_id;
        param += "&topic_id=" + val_topic_id;
				param += "&comment_id=" + val_comment_id;

        // Debug

        $.ajax({
           url: 'includes/popup.php',
            type: 'POST',
            data: param,
            success: function (response) {
               $('#participentcommentcompletion').html($(response).find('#participentcommentcompletion').html());
            // Re-initialize after DOM replacement
            initparticipentcommentcompletion();
               $('#participentcommentcompletion').fadeIn();
            }
        });

    }, 200);
}
function commentDelete(obj) {
     if (confirm('Are you sure you want to remove this comment?')) {

			setTimeout(function() {
			  let parent = $(obj).closest('div[use="scheduleContainer"]');

                var val_participant_id = parent.attr('data_participant_id');
                var val_date_id        = parent.attr('data_date_id');
                var val_hall_id        = parent.attr('data_hall_id');
                var val_session_id     = parent.attr('data_session_id');
                var val_theme_id       = parent.attr('data_theme_id');
                var val_topic_id       = parent.attr('data_topic_id');
				var val_comment_id = $(obj).attr('data_comment_id');


				param = "&participant_id=" + val_participant_id;
				param += "&date_id=" + val_date_id;
				param += "&hall_id=" + val_hall_id;
				param += "&session_id=" + val_session_id;
				param += "&theme_id=" + val_theme_id;
				param += "&topic_id=" + val_topic_id;
				param += "&comment_id=" + val_comment_id;

				$.ajax({
                    type: "POST",
                    url: "manage_participant.process.php",
                    data: 'act=deleteParticipantComment' + param,

                    success: function(response) {
             
                             location.reload();


                    }
                });
			}, 500);
		}
}
        </script>