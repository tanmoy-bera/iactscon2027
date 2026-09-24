<?php
include_once('includes/frontend.init.php');

// if ($mycms->getSession('LOGGED.USER.ID') == "" || $mycms->isSession('LOGGED.USER.ID') == false) {
//     login_session_control();
// }

// $delegateId           = $mycms->getSession('LOGGED.USER.ID');
$action               = $_REQUEST['act'];



switch ($action) {
    case 'show_data':
        show_data($mycms, $cfg);
        exit();
        break;

    case 'show_participant_data':
        show_participant_data($mycms, $cfg);
        exit();
        break;

    // FOR PARTICIPANT VIEW
    case 'show_participant_list':
        show_participant_list($mycms, $cfg);
        exit();
        break;
}


function show_data($mycms, $cfg)
{
    $dateId = $_REQUEST['dateId'];
    $hallId = $_REQUEST['hallId'];
    $search_condition_hall = '';
    $searchCondition = "";
    $session_name_search = addslashes(trim($_REQUEST['searchInput']));
    if ($_REQUEST['searchInput']) {
        $search_condition_hall = " AND session_title LIKE '%" . $session_name_search . "%' ";

        $searchCondition = " AND session.session_title LIKE '%" . $session_name_search . "%' ";
    }
    if ($_REQUEST['searchSessionByFacultyName']) {
        $searchSessionByFacultyName = addslashes(trim($_REQUEST['searchSessionByFacultyName']));
        $search_condition_hall = " AND id IN ( 
                                                SELECT DISTINCT sch.session_id FROM 
                                                 " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch 
                                		        INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
                                				ON sch.participant_id = prt.id
                                                WHERE prt.participant_full_name LIKE '%" . $searchSessionByFacultyName . "%' 
                                                AND  prt.status = 'A' 
                                                
                                                ) ";

        $searchCondition = " AND session.id IN ( 
                                                SELECT DISTINCT sch.session_id FROM 
                                                 " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch 
                                		        INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
                                				ON sch.participant_id = prt.id
                                                WHERE prt.participant_full_name LIKE '%" . $searchSessionByFacultyName . "%' 
                                                AND  prt.status = 'A' 
                                                
                                                ) ";
    }

    // if ($hallId) {
    //     $hallId = $hallId;
    // } else {
    //     $hallId = 9;
    // }


    // ===================================================== HALL NAME ======================================================
    $sql = array();
    $sql['QUERY']         = "SELECT hallData.*, IFNULL(hallTempname.hall_name, hallData.hall_title) AS hall_title
                              FROM " . _DB_MASTER_HALL_ . " hallData
                   LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " hallTempname
                                   ON hallData.id = hallTempname.hall_id
                               AND hallTempname.date_id = '" . $dateId . "'
                             WHERE hallData.status = 'A'  
                               AND hallData.id IN (SELECT session_hall_id FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " WHERE session_date_id = '" . $dateId . "' " . $search_condition_hall . ")
                          ORDER BY hallData.id";
    $resultHall  = $mycms->sql_select($sql);

    // $occupancyWidth = $genericWidth - 5;
    // $greatGrandPasWidth = sizeof($resultHall) * $genericWidth + 10;

?>
    <div class="schedule-tabcondent" id="day-<?= $dateId ?>" style="display: block;">
        <?php
        if ($resultHall) {
        ?>
            <div class="hall-btn">
                <!-- <a href="javascript:void(0);" data-tab="bcrh" class="active">Bidhan Chandra Roy Hall</a>
                                                     <a href="javascript:void(0);" data-tab="srh">Satyajit Ray Hall</a> -->
                <span class="total_time" style="color: antiquewhite;font-weight: bolder;"><?= $dateId == 1 ? "Venue - " : "Hall - "  ?></span>
                <select class="hall-select" class="" name="hall-check">
                    <!-- <option>--Select Hall--</option> -->
                    <?php
                    foreach ($resultHall as $key => $rowHall) {    ?>
                        <option value="<?= $rowHall['id'] ?>" <?= $rowHall['id'] == $hallId ? 'selected' : ($hallId == 0 ? ($key == 0 ? 'selected' : '') : '') ?>><?= $rowHall['hall_title'] ?></option>
                    <? } ?>
                </select>

            </div>
        <? } ?>


        <!-- ===============================   MAIN DATA ============================================= -->

        <?php
        global $cfg, $mycms, $searchArray, $searchString, $buttonAccessArray, $buttonSpPrgAccessArray, $sessionTypeIcons, $loggedUserId;

        if ($hallId == 0) {
            $hallId = $resultHall[0]['id'];
        }
        if ($hallId == 0 || $hallId == "" || !isset($hallId)) {
            $hallFilter = "";
        } else {
            $hallFilter = " AND session.session_hall_id = '" . $hallId . "' ";
        }
        $participantTypes = array("chairperson", "moderator", "panellist", "speaker");
        $sqlSelectSession = array();
        // $sqlSelectSession['QUERY']        = "SELECT session.*,
        // 										   (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60) AS session_start_time_mins,
        // 										   (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)-1 AS session_end_time_mins,
        // 										   hall.hall_title,
        // 										   venue.id AS venue_id,
        // 										   venue.program_venue,
        // 										   scheduleDate.conf_date AS session_date  

        // 									  FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " session

        // 								INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " scheduleDate 
        // 										ON session.session_date_id = scheduleDate.id

        // 								INNER JOIN " . _DB_MASTER_HALL_ . " hall 
        // 										ON session.session_hall_id = hall.id

        // 								INNER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue 
        // 										ON hall.hall_venue = venue.id

        // 									 WHERE session.status = 'A' 
        // 									   AND session.session_date_id = '" . $dateId . "'
        // 									   AND session.session_hall_id = '" . $$hallFilter . "' 
        // 										   " . $searchCondition . "
        // 								  ORDER BY (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60), (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)";

        //print_r($sqlSelectSession);

        $sqlSelectSession['QUERY'] = "
                                SELECT 
                                    session.*,
                                    (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60) AS session_start_time_mins,
                                    (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)-1 AS session_end_time_mins,
                                    hall.hall_title,
                                    venue.id AS venue_id,
                                    venue.program_venue,
                                    scheduleDate.conf_date AS session_date
                                FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " session
                                INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " scheduleDate 
                                        ON session.session_date_id = scheduleDate.id
                                INNER JOIN " . _DB_MASTER_HALL_ . " hall 
                                        ON session.session_hall_id = hall.id
                                INNER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue 
                                        ON hall.hall_venue = venue.id
                                WHERE session.status = 'A'
                                AND session.session_date_id = '" . $dateId . "'
                                $hallFilter
                                $searchCondition
                                ORDER BY 
                                    (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60),
                                    (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)";


        $resultSession             = $mycms->sql_select($sqlSelectSession);
        if (!$resultSession) {
            echo '<div class="schedule_card" style="margin-top: 10%;font-size:24px;">
            <p>The requested content does not exist!</p>
            </div>';
            exit;
        }
        ?>
        <!-- NATCON2025 -->
        <?php if (true) { //$dateId != 1 ?>
            <!-- per hall  -->
            <div class="schedule_card_wrap active bcrh">
                <!-- per session  -->
                <?php
                foreach ($resultSession as $keySchedule => $rowSession) {
                ?>
                    <div class="schedule_card">
                        <p class="main_topic"><?= $rowSession['session_title'] ?></p>
                        <p class="total_time"><i class="fas fa-clock"></i><?= date("h:i A", strtotime($rowSession['session_start_time'])) ?> - <?= date("h:i A", strtotime($rowSession['session_end_time'])) ?></p>
                        <?
                        $sqlFetchTheme = array();
                        $sqlFetchTheme['QUERY']           = "   SELECT *, 
																	   (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
																	   (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
																  FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
																 WHERE schedule_id = '" . $rowSession['id'] . "'
																   AND status = 'A'  
															  ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";
                        $resultTheme   = $mycms->sql_select($sqlFetchTheme);

                        if ($resultTheme) {

                        ?>
                            <ul>
                                <?
                                foreach ($resultTheme as $keyTheme => $rowtheme) {
                                    // CHAIRPERSON NAMES  
                                    $sqlParticipantTheme = array();
                                    $sqlParticipantTheme['QUERY']         = "SELECT prt.*, sch.participant_type
                                																  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
                                														    INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
                                																	ON sch.participant_id = prt.id
                                																 WHERE sch.`session_id` = '" . $rowtheme['schedule_id'] . "'
                                																   AND sch.`theme_id` = '" . $rowtheme['id'] . "'
                                																   AND (sch.topic_id IS NULL OR sch.topic_id = '')";
                                    $resultsParticipantTheme     = $mycms->sql_select($sqlParticipantTheme);
                                    // echo '<pre>'; print_r($resultsParticipantTheme);

                                    // TOPIC 
                                    $sqlFetchTopic = array();
                                    $sqlFetchTopic['QUERY']           = "   SELECT *, 
																						   topic_time_duration AS topic_time_duration_mins,
																						   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
																						   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
																					  FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
																					 WHERE schedule_session_id = '" . $rowSession['id'] . "'
																					   AND schedule_theme_id = '" . $rowtheme['id'] . "'
																					   AND status = 'A'
																				  ORDER BY sequence, id";
                                    $resultTopic                = $mycms->sql_select($sqlFetchTopic);
                                    if ($resultsParticipantTheme && !$resultTopic) {
                                        $cnt = 0;
                                        echo '<p class="spe_box">';
                                        foreach ($resultsParticipantTheme as $kt => $rowParticipantTheme) {
                                            if (
                                                $rowParticipantTheme['participant_type'] == 'Mentor'
                                                || $rowParticipantTheme['participant_type'] == 'Mentor'
                                            ) {

                                                if ($cnt == 0) {
                                ?>
                                                    <span class="spe_head"><?= $rowParticipantTheme['participant_type'] ?></span><br>
                                                <?php } ?>

                                                <span class="spe-name" participant-id="<?= $rowParticipantTheme['id'] ?>">

                                                    <?= $rowParticipantTheme['participant_full_name'] ?>
                                                </span>
                                            <?php
                                                $cnt++;
                                            }
                                            ?>
                                        <?php

                                        }
                                        echo '</p>';
                                    }
                                    foreach ($resultTopic as $keyTopic => $rowTopic) {

                                        ?>

                                        <!-- per session theme TOPIC  -->
                                        <li>
                                            <p class="sub_topic"><?= $rowTopic['topic_title'] ?></p>
                                            <p class="sub_time"><i class="fas fa-clock"></i><?= date("h:i A", strtotime($rowTopic['topic_time_start'])) ?> - <?= date("h:i A", strtotime($rowTopic['topic_time_end'])) ?></p>

                                            <?php
                                            $sqlParticipantTopic = array();
                                            $sqlParticipantTopic['QUERY']         = "SELECT prt.*, sch.participant_type
                                                                                FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
                                                                          INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
                                                                                  ON sch.participant_id = prt.id
                                                                               WHERE sch.`session_id` = '" . $rowTopic['schedule_session_id'] . "'
                                                                                 AND sch.`theme_id` = '" . $rowTopic['schedule_theme_id'] . "'
                                                                                 AND sch.`topic_id` = '" . $rowTopic['id'] . "'
                                                                                 
                                                                            ORDER BY prt.participant_full_name ";
                                            $resultsParticipantTopic     = $mycms->sql_select($sqlParticipantTopic);
                                            // echo "<pre>";
                                            // print_r($resultsParticipantTopic);
                                            // echo "</pre>";

                                            foreach ($resultsParticipantTopic as $kPt => $rowParticipant) {
                                                if ($rowParticipant['participant_type'] != 'Panelist') {
                                            ?>
                                                    <!-- each participant -->
                                                    <p class="spe_box">
                                                        <span class="spe_head"><?= $rowParticipant['participant_type'] ?></span><br>
                                                        <span class="spe-name" participant-id="<?= $rowParticipant['id'] ?>">
                                                            <!-- <img src="" alt=""> -->
                                                            <?= $rowParticipant['participant_full_name'] ?>
                                                        </span>
                                                    </p>
                                                    <?php }
                                            }
                                            // For Panelists
                                            $count = 0;
                                            foreach ($resultsParticipantTopic as $kPt => $rowParticipant) {
                                                if (
                                                    $rowParticipant['participant_type'] == 'Panelist'
                                                    || $rowParticipant['participant_type'] == 'Panellist'
                                                ) {

                                                    if ($count == 0) {
                                                    ?>
                                                        <span class="spe_head"><?= $rowParticipant['participant_type'] ?>s</span><br>
                                                    <?php } ?>

                                                    <span class="spe-name" participant-id="<?= $rowParticipant['id'] ?>">
                                                        <!-- <img src="" alt=""> -->
                                                        <?= $rowParticipant['participant_full_name'] ?>
                                                    </span>
                                                    <?php
                                                    $count++;
                                                }
                                            } // participant-topic end
                                            if ($resultsParticipantTheme) {
                                                $cnt = 0;
                                                echo '<p class="spe_box">';
                                                foreach ($resultsParticipantTheme as $kt => $rowParticipantTheme) {
                                                    if (
                                                        $rowParticipantTheme['participant_type'] == 'Chairperson'
                                                        || $rowParticipantTheme['participant_type'] == 'Mentor'
                                                    ) {

                                                        if ($cnt == 0) {
                                                    ?>
                                                            <span class="spe_head"><?= $rowParticipantTheme['participant_type'] ?></span><br>
                                                        <?php } ?>

                                                        <span class="spe-name" participant-id="<?= $rowParticipantTheme['id'] ?>">

                                                            <?= $rowParticipantTheme['participant_full_name'] ?>
                                                        </span>
                                                    <?php
                                                        $cnt++;
                                                    }
                                                    ?>
                                            <?php

                                                }
                                                echo '</p>';
                                            }
                                            ?>
                                        </li>
                                        <!-- per session theme TOPIC END -->
                            <?php
                                    } // topic end
                                } // theme end
                            } // session end
                            ?>


                            </ul>
                    </div>
                <?php } ?>
                <!-- per session end -->



            </div>
        <?php } else {
        ?>
            <div class="schedule-tabcondent" id="day-<?= $dateId ?>" style="display: block;">
                <div class="schedule_card_wrap active bcrh">
                    <div class="schedule_card">
                        <ul id="workshop-list">
                            <?php
                            foreach ($resultSession as $keySchedule => $rowSession) {
                                // echo "<pre>"; print_r($rowSession); echo "</pre>";
                                if (($rowSession['session_classifications_id']) !== "Break") {

                            ?>
                                    <li style="text-align: center;" class="workshops" id="workshop-box-<?= $keySchedule ?>"><a onclick="ViewWorkshopDetails(event, <?= $keySchedule ?>)">
                                            <h3 style="color: #f7edc3;"><?= $rowSession['session_title'] ?></h3>
                                            <p>Venue: <?= $rowSession['program_venue'] ?></p>
                                        </a> </li>
                                    <!-- =========================== Details ============================ -->

                                    <div class="schedule_card" id="workshop-details-<?= $keySchedule ?>" style="display: none;">
                                        <a onclick="BackToWorkshopList(event, <?= $keySchedule ?>)" style="float: right;cursor: pointer;font-weight: bolder;color: #f7e7b6;font-size:18px;"><- Back to Workshop List</a>
                                                <p class="main_topic"><?= $rowSession['session_title'] ?></p>
                                                <p class="total_time"><i class="fas fa-clock"></i><?= date("h:i A", strtotime($rowSession['session_start_time'])) ?> - <?= date("h:i A", strtotime($rowSession['session_end_time'])) ?></p>
                                                <?
                                                $sqlFetchTheme = array();
                                                $sqlFetchTheme['QUERY']           = "   SELECT *, 
																	   (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
																	   (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
																  FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
																 WHERE schedule_id = '" . $rowSession['id'] . "'
																   AND status = 'A'  
															  ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";
                                                $resultTheme   = $mycms->sql_select($sqlFetchTheme);

                                                if ($resultTheme) {

                                                ?>
                                                    <ul>
                                                        <?
                                                        foreach ($resultTheme as $keyTheme => $rowtheme) {
                                                            // CHAIRPERSON NAMES  
                                                            $sqlParticipantTheme = array();
                                                            $sqlParticipantTheme['QUERY']         = "SELECT prt.*, sch.participant_type
                                																  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
                                														    INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
                                																	ON sch.participant_id = prt.id
                                																 WHERE sch.`session_id` = '" . $rowtheme['schedule_id'] . "'
                                																   AND sch.`theme_id` = '" . $rowtheme['id'] . "'
                                																   AND (sch.topic_id IS NULL OR sch.topic_id = '')";
                                                            $resultsParticipantTheme     = $mycms->sql_select($sqlParticipantTheme);
                                                            // echo '<pre>'; print_r($resultsParticipantTheme);

                                                            // TOPIC 
                                                            $sqlFetchTopic = array();
                                                            $sqlFetchTopic['QUERY']           = "   SELECT *, 
																						   topic_time_duration AS topic_time_duration_mins,
																						   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
																						   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
																					  FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
																					 WHERE schedule_session_id = '" . $rowSession['id'] . "'
																					   AND schedule_theme_id = '" . $rowtheme['id'] . "'
																					   AND status = 'A'
																				  ORDER BY sequence, id";
                                                            $resultTopic                = $mycms->sql_select($sqlFetchTopic);
                                                            if ($resultsParticipantTheme && !$resultTopic) {
                                                                $cnt = 0;
                                                                echo '<p class="spe_box">';
                                                                foreach ($resultsParticipantTheme as $kt => $rowParticipantTheme) {
                                                                    if (
                                                                        $rowParticipantTheme['participant_type'] == 'Mentor'
                                                                        || $rowParticipantTheme['participant_type'] == 'Mentor'
                                                                    ) {

                                                                        if ($cnt == 0) {
                                                        ?>
                                                                            <span class="spe_head"><?= $rowParticipantTheme['participant_type'] ?></span><br>
                                                                        <?php } ?>

                                                                        <span class="spe-name" participant-id="<?= $rowParticipantTheme['id'] ?>">

                                                                            <?= $rowParticipantTheme['participant_full_name'] ?>
                                                                        </span>
                                                                    <?php
                                                                        $cnt++;
                                                                    }
                                                                    ?>
                                                                <?php

                                                                }
                                                                echo '</p>';
                                                            }
                                                            foreach ($resultTopic as $keyTopic => $rowTopic) {

                                                                ?>

                                                                <!-- per session theme TOPIC  -->
                                                                <li>
                                                                    <p class="sub_topic"><?= $rowTopic['topic_title'] ?></p>
                                                                    <p class="sub_time"><i class="fas fa-clock"></i><?= date("h:i A", strtotime($rowTopic['topic_time_start'])) ?> - <?= date("h:i A", strtotime($rowTopic['topic_time_end'])) ?></p>

                                                                    <?php
                                                                    $sqlParticipantTopic = array();
                                                                    $sqlParticipantTopic['QUERY']         = "SELECT prt.*, sch.participant_type
                                                                                FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
                                                                          INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
                                                                                  ON sch.participant_id = prt.id
                                                                               WHERE sch.`session_id` = '" . $rowTopic['schedule_session_id'] . "'
                                                                                 AND sch.`theme_id` = '" . $rowTopic['schedule_theme_id'] . "'
                                                                                 AND sch.`topic_id` = '" . $rowTopic['id'] . "'
                                                                                 
                                                                            ORDER BY prt.participant_full_name ";
                                                                    $resultsParticipantTopic     = $mycms->sql_select($sqlParticipantTopic);
                                                                    // echo "<pre>";
                                                                    // print_r($resultsParticipantTopic);
                                                                    // echo "</pre>";

                                                                    foreach ($resultsParticipantTopic as $kPt => $rowParticipant) {
                                                                        if ($rowParticipant['participant_type'] != 'Panelist') {
                                                                    ?>
                                                                            <!-- each participant -->
                                                                            <p class="spe_box">
                                                                                <span class="spe_head"><?= $rowParticipant['participant_type'] ?></span><br>
                                                                                <span class="spe-name" participant-id="<?= $rowParticipant['id'] ?>">
                                                                                    <!-- <img src="" alt=""> -->
                                                                                    <?= $rowParticipant['participant_full_name'] ?>
                                                                                </span>
                                                                            </p>
                                                                            <?php }
                                                                    }
                                                                    // For Panelists
                                                                    $count = 0;
                                                                    foreach ($resultsParticipantTopic as $kPt => $rowParticipant) {
                                                                        if (
                                                                            $rowParticipant['participant_type'] == 'Panelist'
                                                                            || $rowParticipant['participant_type'] == 'Panellist'
                                                                        ) {

                                                                            if ($count == 0) {
                                                                            ?>
                                                                                <span class="spe_head"><?= $rowParticipant['participant_type'] ?>s</span><br>
                                                                            <?php } ?>

                                                                            <span class="spe-name" participant-id="<?= $rowParticipant['id'] ?>">
                                                                                <!-- <img src="" alt=""> -->
                                                                                <?= $rowParticipant['participant_full_name'] ?>
                                                                            </span>
                                                                            <?php
                                                                            $count++;
                                                                        }
                                                                    } // participant-topic end
                                                                    if ($resultsParticipantTheme) {
                                                                        $cnt = 0;
                                                                        echo '<p class="spe_box">';
                                                                        foreach ($resultsParticipantTheme as $kt => $rowParticipantTheme) {
                                                                            if (
                                                                                $rowParticipantTheme['participant_type'] == 'Chairperson'
                                                                                || $rowParticipantTheme['participant_type'] == 'Mentor'
                                                                            ) {

                                                                                if ($cnt == 0) {
                                                                            ?>
                                                                                    <span class="spe_head"><?= $rowParticipantTheme['participant_type'] ?></span><br>
                                                                                <?php } ?>

                                                                                <span class="spe-name" participant-id="<?= $rowParticipantTheme['id'] ?>">

                                                                                    <?= $rowParticipantTheme['participant_full_name'] ?>
                                                                                </span>
                                                                            <?php
                                                                                $cnt++;
                                                                            }
                                                                            ?>
                                                                    <?php

                                                                        }
                                                                        echo '</p>';
                                                                    }
                                                                    ?>
                                                                </li>
                                                                <!-- per session theme TOPIC END -->
                                                    <?php
                                                            } // topic end
                                                        } // theme end
                                                    } // session end
                                                    ?>


                                                    </ul>
                                    </div>
                                    <!-- ---------------------------- X ------------------------------------ -->


                            <?php }
                            } ?>
                        </ul>

                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <!-- per hall  end -->



    </div>
    <script>
        function ViewWorkshopDetails(event, scheduleId) {
            event.preventDefault();
            var workshopDetails = document.getElementById('workshop-details-' + scheduleId);

            workshopDetails.style.display = "block";
            $('.workshops').hide();
            // document.getElementsByClassName('workshops').style.display = "none";


        }

        function BackToWorkshopList(event, scheduleId) {
            var workshopDetails = document.getElementById('workshop-details-' + scheduleId);
            workshopDetails.style.display = "none";
            $('.workshops').show();

        }
    </script>

    <!-- <script>
        $('select[name="hall-check"]').on('change', function() {
            var dateId = $('input[name="date-check"]').val();
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
    </script> -->
<?
}

function show_participant_data($mycms, $cfg)
{
    $participantId = $_REQUEST['id'];

    $sqlPartiDate['QUERY']        = " SELECT DISTINCT participant_schedule.date_id,participant_details.*,program_date.conf_date
                                             FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule 
                                         INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
											  ON participant_schedule.participant_id = participant_details.id
                                                INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
											  ON participant_schedule.date_id = program_date.id
                                    WHERE participant_schedule.participant_id='" . $participantId . "'  ";
    $resPartiDate        = $mycms->sql_select($sqlPartiDate);
    // echo '<pre>'; print_r($resPartiDate);
    $searchCondition = '';
?>
    <div class="pop_up_inner">
        <div class="pop_up_box">
            <a href="javascript:void(0);" class="pop_cls"><i class="fas fa-times"></i></a>
            <div class="pop_head">
                <h4>
                    <!-- <img src="" alt=""> -->
                    <?= $resPartiDate[0]['participant_full_name'] ?>
                </h4>
                <!-- ============ DATE BUTTONS ================================= -->
                <div class="date_tab">
                    <?php

                    foreach ($resPartiDate as $key => $rowParti) {


                    ?>
                        <a href="javascript:void(0);" class="<?= $key == 0 ? 'active' : '' ?>" data-tab="date<?= $key + 1 ?>"><?= date('d/m/Y', strtotime($rowParti['conf_date'])) ?></a>
                    <?php

                    } ?>
                    <!-- <a href="javascript:void(0);" data-tab="date2">15/01/25</a> -->
                </div>
            </div>
            <!-- ==================== DATA ========================= -->
            <div class="pop_body">

                <?php
                foreach ($resPartiDate as $keydate => $rowDate) {



                    $sqlParti['QUERY']        = "        SELECT participant_schedule.*, 
												program_date.conf_date AS conf_date, 
												 program_session.session_start_time, program_session.session_end_time, 
												 (TIME_TO_SEC(CONCAT(program_session.session_start_time,':00'))/60) AS session_start_time_mins,
												 (TIME_TO_SEC(CONCAT(program_session.session_end_time,':00'))/60)-1 AS session_end_time_mins,
												 program_topic.topic_title, program_topic.reference_tag,  program_theme.theme_title, program_session.session_title, 												
												 IFNULL( IFNULL(program_hallTempname.hall_name, program_hall.hall_title) , IFNULL(session_hallTempname.hall_name, session_hall.hall_title) ) AS hall_title,												 
												 participant_details.participant_full_name, participant_details.participant_email_id
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
										   WHERE program_date.status = 'A' AND program_date.id = '" . $rowDate['date_id'] . "' 
											 AND participant_schedule.participant_id = '" . $participantId . "' 
											 " . $searchCondition . "
										ORDER BY participant_schedule.start_time, program_session.session_start_time, program_session.session_end_time";
                    $resParti        = $mycms->sql_select($sqlParti);
                    // echo '<pre>';
                    // print_r($resParti);
                    // echo '</pre>';
                ?>


                    <ul class="date<?= $keydate + 1 ?> <?= $keydate == 0 ? 'active' : '' ?>">
                        <?php

                        foreach ($resParti as $key => $rowDetails) {
                        ?>
                            <li>
                                <h3><?= $rowDetails['participant_type'] ?></h3>
                                <h4><?= $rowDetails['topic_title'] ?></h4>
                                <h5><i class="fas fa-map-marker-alt"></i><?= $rowDetails['hall_title'] ?> | <i class="fas fa-clock"></i><?= date("h:i A", strtotime($rowDetails['start_time'])) ?> - <?= date("h:i A", strtotime($rowDetails['end_time'])) ?></h5>
                            </li>
                        <?php } ?>
                        <!-- <li>
                        <h3>Chairperson</h3>
                        <h4>Topic</h4>
                        <h5><i class="fas fa-map-marker-alt"></i>Hall A | <i class="fas fa-clock"></i>01:00 PM - 01:15 PM</h5>
                    </li> -->
                    </ul>
                <?php
                }
                ?>

                <!-- <ul class="date2">
                    <li>
                        <h3>Faculty</h3>
                        <h4>Topic</h4>
                        <h5><i class="fas fa-map-marker-alt"></i>Hall A | <i class="fas fa-clock"></i>01:00 PM - 01:15 PM</h5>
                    </li>
                    <li>
                        <h3>Chairperson</h3>
                        <h4>Topic</h4>
                        <h5><i class="fas fa-map-marker-alt"></i>Hall A | <i class="fas fa-clock"></i>01:00 PM - 01:15 PM</h5>
                    </li>
                </ul> -->
                <? ?>
            </div>
        </div>
    </div>

<?php

}

function show_participant_list($mycms, $cfg)
{
?>
    <div class="schedule-tabcondent" id="day1" style="display: block;">
        <div class="hall-btn">
            <!-- <a href="javascript:void(0);" data-tab="bcrh" class="active">Bidhan Chandra Roy Hall</a>
        <a href="javascript:void(0);" data-tab="srh">Satyajit Ray Hall</a> -->
            <!-- <select class="hall-select">
                <option>--Select Hall--</option>
                <option>Bidhan Chandra Roy Hall</option>
                <option>Satyajit Ray Hall</option>
            </select> -->
        </div>

        <div class="schedule_card_wrap active bcrh">
            <?php
            if ($_REQUEST['searchInput']) {
                $search_input = addslashes(trim($_REQUEST['searchInput']));
                $searchCondition = ' AND (`participant_full_name` LIKE "%' . $search_input . '%"  OR 
                                            `participant_email_id` LIKE "%' . $search_input . '%" OR 
                                            `participant_title` LIKE "%' . $search_input . '%" ) ';
            }


            $sqlListing = array();
            $sqlListing['QUERY']         = "SELECT *
                                              FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                             WHERE `status` = 'A' " . $searchCondition . "
                                          ORDER BY TRIM(participant_full_name) LIMIT 10";

            //echo '<!--'; print_r($sqlListing); echo '-->';

            $resultsListing     = $mycms->sql_select($sqlListing);

            // echo '<pre>'; print_r($resultsListing);						


            $prtcpntsArr     = array();
            foreach ($resultsListing as $key => $rowDetails) {
                // $prtcpntsArr[] = $rowDetails['id'];

            ?>
                <div class="schedule_card">
                    <p class="main_topic"><?= $rowDetails['participant_title'] . " " . $rowDetails['participant_full_name'] ?></p>
                    <span style="font-size: 18px;">
                        <i class="fas fa-envelope"></i> &nbsp;<?= $rowDetails['participant_email_id']  ?>
                        <br>
                        <i class="fas fa-phone"></i> &nbsp;<?= $rowDetails['participant_mobile_no'] ?>
                        <?= $rowDetails['participant_alternative_mobile_no'] == '' ? '' : ' / ' . $rowDetails['participant_alternative_mobile_no'] ?>
                    </span>
                    <!-- <p class="total_time"><i class="fas fa-clock"></i><?= $rowDetails['participant_email_id']  ?></p> -->
                    <ul>
                        <?php
                        $sqlParti['QUERY']  = "SELECT participant_schedule.*, 
                                            DATE_FORMAT(program_date.conf_date,'%M %e | %W') AS conf_date, 
                                            program_session.session_start_time, program_session.session_end_time, 
                                            (TIME_TO_SEC(CONCAT(program_session.session_start_time,':00'))/60) AS session_start_time_mins,
                                            (TIME_TO_SEC(CONCAT(program_session.session_end_time,':00'))/60)-1 AS session_end_time_mins,
                                            program_topic.topic_title, program_topic.reference_tag,  program_theme.theme_title, program_session.session_title, 												
                                            IFNULL( IFNULL(program_hallTempname.hall_name, program_hall.hall_title) , IFNULL(session_hallTempname.hall_name, session_hall.hall_title) ) AS hall_title,												 
                                            participant_details.participant_full_name, participant_details.participant_email_id
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
                                                        AND participant_schedule.participant_id = '" . $rowDetails['id'] . "' 
                                                        " . $searchCondition . "
                                                ORDER BY program_date.conf_date,participant_schedule.start_time, program_session.session_start_time, program_session.session_end_time";
                        $resParti        = $mycms->sql_select($sqlParti);


                        $rowPartiDetail = array();
                        foreach ($resParti as $key => $rowaccomm) {
                        ?>

                            <li style="width: 43%;">
                                <p class="sub_topic"><?= $rowaccomm['conf_date'] ?></p>
                                <p class="sub_time"><i class="fas fa-user"></i><?= $rowaccomm['participant_type'] ?></p>

                                <!-- <p class="sub_time"><i class="fas fa-clock"></i><?= date("h:i A", strtotime($rowaccomm['start_time'])) ?> - <?= date("h:i A", strtotime($rowaccomm['end_time'])) ?></p> -->
                                <p class="spe_box">
                                    <span style="align-items: center;font-size: 19px;font-weight: 400;margin-top: 15px;color:#f7e9c3;">
                                        <? if ($rowaccomm['participant_type'] != 'chairperson') { ?>
                                            <?= $rowaccomm['topic_title'] == '' ? '' : '<b>Topic :  </b>' . $rowaccomm['topic_title'] . '<br>' ?>
                                        <?php } ?>
                                        <b>Session : </b> <?= $rowaccomm['session_title'] . ' (' . $rowaccomm['session_start_time'] . ' - ' . $rowaccomm['session_end_time'] . ')' ?>
                                        <br>
                                        <b>Hall : </b> <?= $rowaccomm['hall_title']  ?>
                                        <br>

                                        <!-- <?= $rowaccomm['topic_title'] == '' ? '' : 'Topic : ' . $rowaccomm['topic_title'] . '<br>' ?>
                                       <?= $rowaccomm['topic_title'] == '' ? '' : 'Topic : ' . $rowaccomm['topic_title'] . '<br>' ?>
                                        
                                        Topic : <?= $rowaccomm['topic_title'] ?>
                                        <br>
                                        Topic : <?= $rowaccomm['topic_title'] ?>
                                        <br>
                                        Topic : <?= $rowaccomm['topic_title'] ?>
                                        <br> -->
                                    </span>
                                </p>

                            </li>
                        <?php } ?>
                        <!-- <li>
                            <p class="sub_topic">PANEL DISCUSSION - CONCEPTS OF CKM S N Routray</p>
                            <p class="sub_time"><i class="fas fa-clock"></i>02:15 PM - 02:45 PM</p>
                            <p class="spe_box">
                                <span class="spe_head">Speaker</span><br>
                                <span class="spe-name"><img src="" alt="">Biswajit Majumder</span>
                                <span class="spe-name"><img src="" alt="">Biswajit Majumder</span>
                                <span class="spe-name"><img src="" alt="">Biswajit Majumder</span>
                                <span class="spe-name"><img src="" alt="">Biswajit Majumder</span>
                            </p>
                        </li> -->

                    </ul>
                </div>

            <?php } // all participant list foreach
            ?>

        </div>

    </div>
<?php
}
